<?php

namespace App\Http\Controllers;

use App\Models\CompetitionRegistration;
use App\Models\ParkingFareSetting;
use App\Models\ParkingLocation;
use App\Models\ParkingTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParkingOfficerController extends Controller
{
    public function scanner(Request $request): View
    {
        $locations = ParkingLocation::where('status', '!=', 'closed')
            ->orderBy('name')
            ->get();

        $selectedLocationId = $request->query('location_id', $locations->first()?->id);
        $activeLocation = $locations->firstWhere('id', $selectedLocationId) ?: $locations->first();

        $fareSetting = ParkingFareSetting::current();

        // Recent check-ins for this location (Activity Log Tab)
        $recentTickets = collect([]);
        $todayCashTotal = 0;
        $todayTicketsCount = 0;

        if ($activeLocation) {
            $recentTickets = ParkingTicket::where('parking_location_id', $activeLocation->id)
                ->whereNotNull('check_in_at')
                ->latest('check_in_at')
                ->limit(15)
                ->get();

            $todayCashTotal = (int) ParkingTicket::where('parking_location_id', $activeLocation->id)
                ->where('is_public', true)
                ->where('payment_method', 'cash')
                ->where('payment_status', 'paid')
                ->whereDate('check_in_at', today())
                ->sum('fee_amount');

            $todayTicketsCount = ParkingTicket::where('parking_location_id', $activeLocation->id)
                ->where('is_public', true)
                ->whereDate('check_in_at', today())
                ->count();
        }

        return view('parking.scanner', compact('locations', 'activeLocation', 'recentTickets', 'fareSetting', 'todayCashTotal', 'todayTicketsCount'));
    }

    public function verifyTicket(Request $request): JsonResponse
    {
        $code = trim($request->input('code', ''));
        $currentLocationId = $request->input('location_id');

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tiket atau QR code tidak boleh kosong.',
            ], 422);
        }

        $ticket = ParkingTicket::with(['location', 'registration'])
            ->where(function ($q) use ($code) {
                $q->where('qr_code_token', $code)
                    ->orWhere('ticket_code', $code)
                    ->orWhere('license_plate', strtoupper($code));
            })
            ->first();

        if (! $ticket) {
            return response()->json([
                'success' => false,
                'found' => false,
                'message' => 'Tiket tidak ditemukan dalam sistem. Pastikan QR code atau kode tiket valid.',
            ], 404);
        }

        $isMatchingLocation = empty($currentLocationId) || ($ticket->parking_location_id == $currentLocationId);
        $assignedLocationName = $ticket->location?->name ?? 'Belum Di-plot (Bebas)';

        return response()->json([
            'success' => true,
            'found' => true,
            'is_matching_location' => $isMatchingLocation,
            'ticket' => [
                'id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'license_plate' => $ticket->license_plate,
                'vehicle_type' => $ticket->vehicle_type,
                'vehicle_type_label' => $ticket->vehicle_type_label,
                'vehicle_brand' => $ticket->vehicle_brand ?? '-',
                'vehicle_color' => $ticket->vehicle_color ?? '-',
                'school_name' => $ticket->school_name,
                'is_public' => (bool) $ticket->is_public,
                'fee_amount' => (int) $ticket->fee_amount,
                'formatted_fee' => $ticket->formatted_fee,
                'payment_method' => $ticket->payment_method,
                'payment_method_label' => $ticket->payment_method_label,
                'payment_status' => $ticket->payment_status,
                'payment_status_label' => $ticket->payment_status_label,
                'driver_name' => $ticket->driver_name ?? '-',
                'driver_phone' => $ticket->driver_phone ?? '-',
                'status' => $ticket->status,
                'status_label' => $ticket->status_label,
                'status_badge_class' => $ticket->status_badge_class,
                'parking_location_id' => $ticket->parking_location_id,
                'assigned_location_name' => $assignedLocationName,
                'check_in_at' => $ticket->check_in_at ? $ticket->check_in_at->format('H:i') : null,
            ],
        ]);
    }

    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_id' => 'required|exists:parking_tickets,id',
            'location_id' => 'required|exists:parking_locations,id',
        ]);

        $ticket = ParkingTicket::findOrFail($request->ticket_id);
        $location = ParkingLocation::findOrFail($request->location_id);

        $updateData = [
            'status' => 'parked',
            'parking_location_id' => $location->id,
            'check_in_at' => now(),
            'checked_by_user_id' => auth()->id(),
        ];

        // Jika tiket umum dan belum bayar (bayar tunai di pintu), tandai lunas
        if ($ticket->is_public && $ticket->payment_status === 'unpaid') {
            $updateData['payment_status'] = 'paid';
        }

        $ticket->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Kendaraan {$ticket->license_plate} ({$ticket->school_name}) berhasil Check-In di {$location->name}.",
            'ticket' => [
                'id' => $ticket->id,
                'license_plate' => $ticket->license_plate,
                'check_in_at' => $ticket->check_in_at->format('H:i:s'),
                'status' => $ticket->status,
                'status_label' => $ticket->status_label,
            ],
            'location' => [
                'id' => $location->id,
                'parked_r4' => $location->fresh()->parked_r4_count,
                'capacity_r4' => $location->capacity_r4,
                'parked_r2' => $location->fresh()->parked_r2_count,
                'capacity_r2' => $location->capacity_r2,
            ],
        ]);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_id' => 'required|exists:parking_tickets,id',
        ]);

        $ticket = ParkingTicket::findOrFail($request->ticket_id);

        $ticket->update([
            'status' => 'checked_out',
            'check_out_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Kendaraan {$ticket->license_plate} berhasil Check-Out (keluar).",
        ]);
    }

    /**
     * Penerbitan Karcis Dadakan (On-The-Spot POS) oleh Petugas Gerbang Lapangan
     */
    public function issueOnTheSpotTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'parking_location_id' => 'required|exists:parking_locations,id',
            'vehicle_type' => 'required|in:r2_motor,r4_car,r4_elf,r4_bus',
            'license_plate' => 'required|string|max:30',
            'vehicle_brand' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:30',
            'payment_method' => 'required|in:cash,qris',
        ]);

        $fareSetting = ParkingFareSetting::current();
        $location = ParkingLocation::findOrFail($validated['parking_location_id']);

        $validated['license_plate'] = strtoupper(trim($validated['license_plate']));
        $validated['is_public'] = true;
        $validated['fee_amount'] = $fareSetting->getFareForVehicleType($validated['vehicle_type']);
        $validated['payment_status'] = 'paid';
        $validated['status'] = 'parked'; // Langsung terparkir / check-in saat terbit di pintu
        $validated['check_in_at'] = now();
        $validated['driver_name'] = $validated['driver_name'] ?: 'Pengemudi Umum';
        $validated['checked_by_user_id'] = auth()->id();

        $ticket = ParkingTicket::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Karcis {$ticket->license_plate} berhasil diterbitkan & langsung Check-In!",
            'ticket' => [
                'id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'license_plate' => $ticket->license_plate,
                'vehicle_type_label' => $ticket->vehicle_type_label,
                'fee_amount' => $ticket->fee_amount,
                'formatted_fee' => $ticket->formatted_fee,
                'payment_method_label' => $ticket->payment_method_label,
                'status' => $ticket->status,
                'check_in_at' => $ticket->check_in_at->format('H:i'),
                'public_ticket_url' => route('parking.public-ticket', $ticket->qr_code_token),
                'print_url' => route('admin.parking.tickets.print', $ticket->id),
            ],
            'location' => [
                'id' => $location->id,
                'parked_r4' => $location->fresh()->parked_r4_count,
                'capacity_r4' => $location->capacity_r4,
                'remaining_r4' => $location->fresh()->remaining_r4,
                'parked_r2' => $location->fresh()->parked_r2_count,
                'capacity_r2' => $location->capacity_r2,
                'remaining_r2' => $location->fresh()->remaining_r2,
            ],
        ]);
    }

    public function publicTicket(string $token): View
    {
        $ticket = ParkingTicket::with(['location', 'registration'])
            ->where('qr_code_token', $token)
            ->orWhere('ticket_code', $token)
            ->firstOrFail();

        return view('parking.public-ticket', compact('ticket'));
    }

    public function schoolTicketsPrint(string $code): View
    {
        $registration = CompetitionRegistration::with(['parkingTickets.location', 'event'])
            ->where('registration_code', $code)
            ->orWhere('id', $code)
            ->firstOrFail();

        $tickets = $registration->parkingTickets;

        return view('parking.school-tickets-print', compact('registration', 'tickets'));
    }
}
