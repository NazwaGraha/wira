<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CompetitionRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $level = $request->query('level');
        $search = $request->query('q');

        $query = CompetitionRegistration::with(['event', 'teams.category'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($level) {
            $query->where('level', $level);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('school_name', 'like', "%{$search}%")
                  ->orWhere('registration_code', 'like', "%{$search}%")
                  ->orWhere('advisor_name', 'like', "%{$search}%")
                  ->orWhere('advisor_phone', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => CompetitionRegistration::count(),
            'pending' => CompetitionRegistration::where('status', 'pending')->count(),
            'verified' => CompetitionRegistration::where('status', 'verified')->count(),
            'rejected' => CompetitionRegistration::where('status', 'rejected')->count(),
        ];

        return view('admin.competition.registrations.index', compact('registrations', 'counts', 'status', 'level', 'search'));
    }

    public function show($id)
    {
        $registration = CompetitionRegistration::with(['event', 'teams.category'])->findOrFail($id);
        return view('admin.competition.registrations.show', compact('registration'));
    }

    public function verify($id)
    {
        $registration = CompetitionRegistration::findOrFail($id);
        $registration->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', "Pendaftaran {$registration->school_name} ({$registration->registration_code}) berhasil diverifikasi! Kwitansi & Kartu Peserta kini sudah aktif.");
    }

    public function reject(Request $request, $id)
    {
        $registration = CompetitionRegistration::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_at' => now(),
            'verified_by' => auth()->user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', "Pendaftaran {$registration->school_name} telah ditolak.");
    }

    public function destroy($id)
    {
        $registration = CompetitionRegistration::find($id);

        if (!$registration) {
            return redirect()->route('admin.competition-registrations.index')->with('success', "Data pendaftaran telah dihapus.");
        }

        $school = $registration->school_name;

        DB::transaction(function () use ($registration) {
            // Delete file proof
            if ($registration->payment_proof && Storage::disk('public')->exists($registration->payment_proof)) {
                Storage::disk('public')->delete($registration->payment_proof);
            }

            // Delete teams and related scores
            foreach ($registration->teams as $team) {
                $team->scores()->delete();
                $team->delete();
            }

            $registration->delete();
        });

        return redirect()->route('admin.competition-registrations.index')->with('success', "Data pendaftaran {$school} berhasil dihapus.");
    }
}
