<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberAreaController extends Controller
{
    /**
     * Dashboard / Halaman Profil Pribadi Anggota (Client Area)
     */
    public function profile()
    {
        /** @var Member $member */
        $member = Auth::user();

        return view('pages.member.profile', compact('member'));
    }

    /**
     * Update Biodata Pribadi dari Client Area
     */
    public function updateProfile(Request $request)
    {
        /** @var Member $member */
        $member = Auth::user();

        if ($member->isSuperUser()) {
            return back()->with('info', 'Profil Super Administrator bersifat virtual.');
        }

        $request->validate([
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'motto' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $member->phone = $request->phone;
        $member->address = $request->address;
        $member->motto = $request->motto;

        if ($request->filled('email')) {
            $member->email = $request->email;
        }

        if ($request->hasFile('photo')) {
            if ($member->photo && Storage::disk('public')->exists($member->photo)) {
                Storage::disk('public')->delete($member->photo);
            }
            $path = $request->file('photo')->store('members', 'public');
            $member->photo = $path;
        }

        $member->save();

        return back()->with('success', 'Biodata profil Anda berhasil diperbarui.');
    }

    /**
     * Perbarui Kata Sandi Akun
     */
    public function updatePassword(Request $request)
    {
        /** @var Member $member */
        $member = Auth::user();

        if ($member->isSuperUser()) {
            return back()->with('error', 'Kata sandi Super Administrator dikonfigurasi melalui sistem keamanan utama.');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (! Hash::check($request->current_password, $member->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.']);
        }

        $member->password = Hash::make($request->password);
        $member->save();

        return back()->with('success', 'Kata sandi Anda berhasil diubah.');
    }
}
