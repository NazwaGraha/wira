<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionRegistration;
use App\Models\ParkingLocation;
use App\Models\ParkingQuotaSetting;
use App\Models\ParkingTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParkingManagementController extends Controller
{
    public function index(Request $request): View
    {
        $locations = ParkingLocation::withCount([
            'tickets as booked_r4_count' => function ($q) {
                $q->whereIn('vehicle_type', ['r4_car', 'r4_elf', 'r4_bus'])
                    ->whereIn('status', ['booked', 'parked']);
            },
            'tickets as booked_r2_count' => function ($q) {
                $q->where('vehicle_type', 'r2_motor')
                    ->whereIn('status', ['booked', 'parked']);
            },
            'tickets as parked_count' => function ($q) {
                $q->where('status', 'parked');
            },
        ])->get();

        // Calculate overall stats
        $totalCapacityR4 = $locations->sum('capacity_r4');
        $totalCapacityR2 = $locations->sum('capacity_r2');
        $totalBookedR4 = $locations->sum('booked_r4_count');
        $totalBookedR2 = $locations->sum('booked_r2_count');
        $totalParked = $locations->sum('parked_count');
        $totalTickets = ParkingTicket::count();

        // Filter tickets
        $query = ParkingTicket::with(['location', 'registration', 'checker'])
            ->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                    ->orWhere('ticket_code', 'like', "%{$search}%")
                    ->orWhere('vehicle_brand', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%")
                    ->orWhereHas('registration', function ($rq) use ($search) {
                        $rq->where('school_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('location_id')) {
            $query->where('parking_location_id', $request->query('location_id'));
        }

        if ($request->filled('vehicle_type')) {
            $vType = $request->query('vehicle_type');
            if ($vType === 'r4') {
                $query->whereIn('vehicle_type', ['r4_car', 'r4_elf', 'r4_bus']);
            } elseif ($vType === 'r2') {
                $query->where('vehicle_type', 'r2_motor');
            } else {
                $query->where('vehicle_type', $vType);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $tickets = $query->paginate(20)->withQueryString();

        // Verified schools for allocation
        $schools = CompetitionRegistration::where('status', 'verified')
            ->orderBy('school_name')
            ->get(['id', 'school_name', 'registration_code', 'level']);

        return view('admin.parking.index', compact(
            'locations',
            'totalCapacityR4',
            'totalCapacityR2',
            'totalBookedR4',
            'totalBookedR2',
            'totalParked',
            'totalTickets',
            'tickets',
            'schools'
        ));
    }

    public function storeLocation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'gmaps_url' => 'nullable|url|max:500',
            'capacity_r4' => 'required|integer|min:0',
            'capacity_r2' => 'required|integer|min:0',
            'officer_name' => 'nullable|string|max:100',
            'officer_phone' => 'nullable|string|max:30',
            'status' => 'required|in:open,full,closed',
            'notes' => 'nullable|string',
        ]);

        ParkingLocation::create($validated);

        return back()->with('success', 'Kantong parkir berhasil ditambahkan!');
    }

    public function updateLocation(Request $request, ParkingLocation $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'gmaps_url' => 'nullable|url|max:500',
            'capacity_r4' => 'required|integer|min:0',
            'capacity_r2' => 'required|integer|min:0',
            'officer_name' => 'nullable|string|max:100',
            'officer_phone' => 'nullable|string|max:30',
            'status' => 'required|in:open,full,closed',
            'notes' => 'nullable|string',
        ]);

        $location->update($validated);

        return back()->with('success', 'Data kantong parkir berhasil diperbarui!');
    }

    public function destroyLocation(ParkingLocation $location): RedirectResponse
    {
        // Unlink any tickets assigned to this location
        $location->tickets()->update(['parking_location_id' => null]);
        $location->delete();

        return back()->with('success', 'Kantong parkir berhasil dihapus.');
    }

    public function storeTicket(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'competition_registration_id' => 'nullable|exists:competition_registrations,id',
            'parking_location_id' => 'nullable|exists:parking_locations,id',
            'vehicle_type' => 'required|in:r4_car,r4_elf,r4_bus,r2_motor',
            'license_plate' => 'required|string|max:30',
            'vehicle_brand' => 'nullable|string|max:100',
            'vehicle_color' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        // Enforcement Kuota Parkir Khusus Sekolah
        if (! empty($validated['competition_registration_id'])) {
            $registration = CompetitionRegistration::find($validated['competition_registration_id']);
            if ($registration) {
                $isR4 = in_array($validated['vehicle_type'], ['r4_car', 'r4_elf', 'r4_bus']);
                if ($isR4 && $registration->remaining_quota_r4 <= 0) {
                    return back()->withInput()->withErrors([
                        'license_plate' => "Kuota tiket Roda 4 untuk {$registration->school_name} sudah habis (Maksimal {$registration->allocated_quota_r4} slot). Silakan ubah kuota sekolah pada menu Pengaturan Kuota jika ingin menambah dispensasi tiket.",
                    ]);
                }
                if (! $isR4 && $registration->remaining_quota_r2 <= 0) {
                    return back()->withInput()->withErrors([
                        'license_plate' => "Kuota tiket Roda 2 (Motor) untuk {$registration->school_name} sudah habis (Maksimal {$registration->allocated_quota_r2} slot). Silakan ubah kuota sekolah jika ingin menambah dispensasi tiket.",
                    ]);
                }
            }
        }

        $validated['license_plate'] = strtoupper(trim($validated['license_plate']));

        ParkingTicket::create($validated);

        return back()->with('success', 'Tiket parkir kendaraan berhasil diterbitkan!');
    }

    /**
     * Halaman Pengaturan Kuota Parkir Sekolah & Matriks Proporsional Regu
     */
    public function quotas(Request $request): View
    {
        $setting = ParkingQuotaSetting::current();

        $query = CompetitionRegistration::where('status', 'verified')
            ->with(['teams', 'parkingTickets'])
            ->orderBy('school_name');

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('school_name', 'like', "%{$search}%")
                    ->orWhere('registration_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->query('level'));
        }

        $schools = $query->paginate(20)->withQueryString();

        // Calculate KPI summaries
        $allVerified = CompetitionRegistration::where('status', 'verified')
            ->with(['teams', 'parkingTickets'])
            ->get();

        $totalVerifiedSchools = $allVerified->count();
        $totalAllocatedR4 = $allVerified->sum('allocated_quota_r4');
        $totalAllocatedR2 = $allVerified->sum('allocated_quota_r2');
        $totalIssuedR4 = $allVerified->sum('issued_tickets_r4_count');
        $totalIssuedR2 = $allVerified->sum('issued_tickets_r2_count');
        $customQuotaCount = $allVerified->where('parking_quota_mode', 'custom')->count();

        // Total capacity of all parking locations for comparison
        $totalLocationCapacityR4 = ParkingLocation::sum('capacity_r4');
        $totalLocationCapacityR2 = ParkingLocation::sum('capacity_r2');

        return view('admin.parking.quotas', compact(
            'setting',
            'schools',
            'totalVerifiedSchools',
            'totalAllocatedR4',
            'totalAllocatedR2',
            'totalIssuedR4',
            'totalIssuedR2',
            'customQuotaCount',
            'totalLocationCapacityR4',
            'totalLocationCapacityR2'
        ));
    }

    public function updateQuotaSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => 'required|in:proportional_teams,flat',
            'flat_default_r4' => 'required|integer|min:0',
            'flat_default_r2' => 'required|integer|min:0',
            'matrix_rules' => 'nullable|array',
            'matrix_rules.*.min_teams' => 'required|integer|min:1',
            'matrix_rules.*.max_teams' => 'required|integer|min:1',
            'matrix_rules.*.quota_r4' => 'required|integer|min:0',
            'matrix_rules.*.quota_r2' => 'required|integer|min:0',
            'matrix_rules.*.label' => 'required|string|max:100',
        ]);

        $setting = ParkingQuotaSetting::current();
        $setting->update($validated);

        return back()->with('success', 'Konfigurasi aturan kuota parkir berhasil diperbarui!');
    }

    public function updateSchoolQuota(Request $request, CompetitionRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'parking_quota_mode' => 'required|in:auto,custom',
            'custom_quota_r4' => 'nullable|integer|min:0',
            'custom_quota_r2' => 'nullable|integer|min:0',
            'parking_notes' => 'nullable|string|max:500',
        ]);

        if ($validated['parking_quota_mode'] === 'auto') {
            $validated['custom_quota_r4'] = null;
            $validated['custom_quota_r2'] = null;
        }

        $registration->update($validated);

        return back()->with('success', "Kuota parkir untuk {$registration->school_name} berhasil diperbarui!");
    }

    public function resetAllSchoolQuotas(): RedirectResponse
    {
        CompetitionRegistration::where('status', 'verified')->update([
            'parking_quota_mode' => 'auto',
            'custom_quota_r4' => null,
            'custom_quota_r2' => null,
        ]);

        return back()->with('success', 'Seluruh kuota sekolah berhasil di-reset ke mode otomatis!');
    }

    public function updateTicket(Request $request, ParkingTicket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'competition_registration_id' => 'nullable|exists:competition_registrations,id',
            'parking_location_id' => 'nullable|exists:parking_locations,id',
            'vehicle_type' => 'required|in:r4_car,r4_elf,r4_bus,r2_motor',
            'license_plate' => 'required|string|max:30',
            'vehicle_brand' => 'nullable|string|max:100',
            'vehicle_color' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:30',
            'status' => 'required|in:booked,parked,checked_out,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['license_plate'] = strtoupper(trim($validated['license_plate']));

        $ticket->update($validated);

        return back()->with('success', 'Data tiket parkir berhasil diperbarui!');
    }

    public function assignLocation(Request $request): RedirectResponse
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:parking_tickets,id',
            'parking_location_id' => 'nullable|exists:parking_locations,id',
        ]);

        ParkingTicket::whereIn('id', $request->ticket_ids)
            ->update(['parking_location_id' => $request->parking_location_id]);

        return back()->with('success', 'Alokasi kantong parkir berhasil diperbarui untuk tiket terpilih!');
    }

    public function updateStatus(Request $request, ParkingTicket $ticket): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:booked,parked,checked_out,cancelled',
        ]);

        $status = $request->status;
        $updates = ['status' => $status];

        if ($status === 'parked' && empty($ticket->check_in_at)) {
            $updates['check_in_at'] = now();
            $updates['checked_by_user_id'] = auth()->id();
        } elseif ($status === 'checked_out') {
            $updates['check_out_at'] = now();
        }

        $ticket->update($updates);

        return back()->with('success', "Status tiket {$ticket->ticket_code} berhasil diubah menjadi {$ticket->fresh()->status_label}.");
    }

    public function destroyTicket(ParkingTicket $ticket): RedirectResponse
    {
        $ticket->delete();

        return back()->with('success', 'Tiket parkir berhasil dihapus.');
    }

    public function printTicket(ParkingTicket $ticket): View
    {
        $ticket->load(['location', 'registration', 'checker']);

        return view('admin.parking.ticket-print', compact('ticket'));
    }

    public function exportExcel(): StreamedResponse
    {
        $fileName = 'Laporan_Kantong_Parkir_SBB4_'.date('Ymd_His').'.csv';

        $tickets = ParkingTicket::with(['location', 'registration', 'checker'])
            ->orderBy('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($tickets) {
            $file = fopen('php://output', 'w');
            // Write BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'No',
                'No. Tiket',
                'Asal Sekolah / Kontingen',
                'No. Polisi (Plat)',
                'Jenis Kendaraan',
                'Merek / Tipe',
                'Warna',
                'Kantong Parkir',
                'Nama Sopir / PJ',
                'No. Kontak',
                'Status',
                'Waktu Check-In',
                'Waktu Check-Out',
                'Petugas Validasi',
                'Catatan',
            ]);

            foreach ($tickets as $idx => $t) {
                fputcsv($file, [
                    $idx + 1,
                    $t->ticket_code,
                    $t->school_name,
                    $t->license_plate,
                    $t->vehicle_type_label,
                    $t->vehicle_brand ?? '-',
                    $t->vehicle_color ?? '-',
                    $t->location?->name ?? 'Belum Di-plot',
                    $t->driver_name ?? '-',
                    $t->driver_phone ?? '-',
                    $t->status_label,
                    $t->check_in_at ? $t->check_in_at->format('d/m/Y H:i:s') : '-',
                    $t->check_out_at ? $t->check_out_at->format('d/m/Y H:i:s') : '-',
                    $t->checker?->name ?? '-',
                    $t->notes ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
