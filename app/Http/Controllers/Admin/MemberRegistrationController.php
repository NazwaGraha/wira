<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberRegistration;
use App\Services\MemberSpreadsheetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MemberRegistrationController extends Controller
{
    /**
     * Tampilkan daftar pendaftar calon anggota baru.
     */
    public function index(Request $request): View
    {
        $query = MemberRegistration::query();

        // Filter status
        $status = $request->input('status', 'all');
        if ($status !== 'all' && in_array($status, ['pending', 'verified', 'accepted', 'rejected'])) {
            $query->where('status', $status);
        }

        // Pencarian nama, NISN, telepon, email, kelas
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('class_grade', 'like', "%{$search}%");
            });
        }

        $registrations = $query->latest()->paginate(15)->withQueryString();

        // Hitungan ringkasan statistik
        $counts = [
            'total' => MemberRegistration::count(),
            'pending' => MemberRegistration::where('status', 'pending')->count(),
            'verified' => MemberRegistration::where('status', 'verified')->count(),
            'accepted' => MemberRegistration::where('status', 'accepted')->count(),
            'rejected' => MemberRegistration::where('status', 'rejected')->count(),
        ];

        return view('admin.member-registrations.index', compact('registrations', 'counts', 'status'));
    }

    /**
     * Export daftar pendaftar calon anggota baru ke file MS Excel (.xlsx).
     */
    public function export(Request $request, MemberSpreadsheetService $spreadsheetService): BinaryFileResponse
    {
        $query = MemberRegistration::query();

        // Filter status
        $status = $request->input('status', 'all');
        if ($status !== 'all' && in_array($status, ['pending', 'verified', 'accepted', 'rejected'])) {
            $query->where('status', $status);
        }

        // Pencarian nama, NISN, telepon, email, kelas
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('class_grade', 'like', "%{$search}%");
            });
        }

        $registrations = $query->latest()->get();
        $filePath = $spreadsheetService->exportRegistrations($registrations);

        $statusSuffix = ($status !== 'all') ? '_'.ucfirst($status) : '';
        $filename = 'Pendaftar_Calon_Anggota_PMR'.$statusSuffix.'_'.date('Ymd_His').'.xlsx';

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Update status pendaftar (pending, verified, accepted, rejected).
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,verified,accepted,rejected',
        ]);

        $registration = MemberRegistration::findOrFail($id);
        $registration->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', "Status pendaftaran untuk {$registration->full_name} berhasil diubah menjadi: {$registration->status_badge['label']}.");
    }

    /**
     * Hapus berkas pendaftaran calon anggota.
     */
    public function destroy(int $id): RedirectResponse
    {
        $registration = MemberRegistration::findOrFail($id);
        $name = $registration->full_name;
        $registration->delete();

        return redirect()->back()->with('success', "Data pendaftaran calon anggota {$name} berhasil dihapus.");
    }

    /**
     * Arahkan pendaftar yang diterima menjadi data anggota resmi.
     */
    public function promoteToMember(int $id): RedirectResponse
    {
        $registration = MemberRegistration::findOrFail($id);
        $registration->update(['status' => 'accepted']);

        return redirect()->route('admin.members.create', [
            'name' => $registration->full_name,
            'class_grade' => $registration->class_grade,
            'phone' => $registration->phone,
            'email' => $registration->email,
        ])->with('info', "Memproses penerimaan calon anggota {$registration->full_name}. Silakan lengkapi nomor identitas & jabatan anggota.");
    }
}
