<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonorRegistration;
use Illuminate\Http\Request;

class BloodDonorRegistrationController extends Controller
{
    public function index()
    {
        $registrations = BloodDonorRegistration::with('event')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.blood-donor-registrations.index', compact('registrations'));
    }

    public function show(BloodDonorRegistration $bloodDonorRegistration)
    {
        return view('admin.blood-donor-registrations.show', compact('bloodDonorRegistration'));
    }

    public function update(Request $request, BloodDonorRegistration $bloodDonorRegistration)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,attended,completed',
            'medical_notes' => 'nullable|string'
        ]);

        $bloodDonorRegistration->update($request->only('status', 'medical_notes'));

        return back()->with('success', 'Status pendaftaran donor berhasil diperbarui.');
    }

    public function destroy(BloodDonorRegistration $bloodDonorRegistration)
    {
        $bloodDonorRegistration->delete();
        return back()->with('success', 'Data pendaftar dihapus.');
    }
}
