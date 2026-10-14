<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            /** @var Member $user */
            $user = Auth::user();
            if (method_exists($user, 'canAccessBackoffice') && $user->canAccessBackoffice()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('member.profile');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $loginInput = $request->input('login_id') ?? $request->input('email');
        if ($loginInput !== null) {
            $request->merge(['login_id' => $loginInput]);
        }

        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'login_id.required' => 'Silakan masukkan Nomor Registrasi (NRI), NIP Pembina, atau Email Super User.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginId = trim((string) $request->input('login_id'));
        $password = (string) $request->input('password');
        $remember = $request->boolean('remember');

        // 1. Pengecekan Khusus Super User
        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            if (strtolower($loginId) !== 'nazwagraha@gmail.com') {
                return redirect()->route('home')->with('auth_error', 'Akses Ditolak: Selain Super User, login hanya diizinkan menggunakan NRI (Anggota) atau NIP (Pembina).');
            }

            // Validasi kredensial Super User
            if (Auth::attempt(['login_id' => $loginId, 'password' => $password], $remember)) {
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->route('home')->with('auth_error', 'Akses Ditolak: Kredensial Super User tidak sesuai.');
        }

        // 2. Pengecekan Khusus Akun Admin Sementara (NRI: admin, Password: pmrwirasmanic)
        if (strtolower($loginId) === 'admin') {
            if (Auth::attempt(['login_id' => 'admin', 'password' => $password], $remember)) {
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->route('home')->with('auth_error', 'Akses Ditolak: Kata sandi Admin darurat tidak sesuai.');
        }

        // 3. Pencarian Data Anggota berdasarkan NRI / NIP, atau Pembina berdasarkan NIP
        $user = Member::where(function ($query) use ($loginId) {
            $query->where('nri', $loginId)
                ->orWhere('nip', $loginId);
        })->first();

        if (! $user) {
            $user = Advisor::where('nip', $loginId)->first();
        }

        if (! $user) {
            return redirect()->route('home')->with('auth_error', 'Akses Ditolak: Nomor NRI Anggota atau NIP Pembina tidak ditemukan dalam basis data.');
        }

        // 4. Login Pertama Kali (Aktivasi Akun & Pembuatan Password)
        if (empty($user->password)) {
            $identKey = $user instanceof Advisor ? $user->nip : ($user->nri ?? $user->nip);

            return redirect()->route('admin.activate', ['nri' => $identKey])
                ->with('info', 'Nomor identitas terverifikasi! Karena ini login pertama kali, silakan buat kata sandi untuk akun Anda.');
        }

        // 5. Verifikasi Password Pengguna (Anggota atau Pembina)
        if (Auth::attempt(['login_id' => $loginId, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            /** @var Member|Advisor $loggedInUser */
            $loggedInUser = Auth::user();

            if (method_exists($loggedInUser, 'canAccessBackoffice') && $loggedInUser->canAccessBackoffice()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->route('member.profile')->with('info', 'Selamat datang di Area Anggota PMR Wira SMAN 1 Ciawi! Akun Anda berstatus Purna sehingga akses dibatasi pada biodata pribadi.');
        }

        // Jika password salah
        return redirect()->route('home')->with('auth_error', 'Akses Ditolak: Kata sandi yang Anda masukkan salah.');
    }

    /**
     * Form Aktivasi Akun (Login Pertama Kali)
     */
    public function showActivationForm(Request $request)
    {
        $nri = $request->query('nri');
        $member = null;

        if ($nri) {
            $member = Member::where('nri', $nri)->orWhere('nip', $nri)->first();
            if (! $member) {
                $member = Advisor::where('nip', $nri)->first();
            }
        }

        return view('admin.activate', compact('member', 'nri'));
    }

    /**
     * Proses Simpan Password Baru saat Aktivasi Pertama Kali
     */
    public function activateAccount(Request $request)
    {
        $request->validate([
            'nri' => 'required|string',
            'email' => 'nullable|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nri.required' => 'NRI atau NIP Pembina wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $loginId = trim($request->input('nri'));

        $user = Member::where(function ($query) use ($loginId) {
            $query->where('nri', $loginId)
                ->orWhere('nip', $loginId);
        })->first();

        if (! $user) {
            $user = Advisor::where('nip', $loginId)->first();
        }

        if (! $user) {
            return back()->withErrors(['nri' => 'Data anggota atau pembina dengan nomor tersebut tidak ditemukan.'])->withInput();
        }

        if (! empty($user->password)) {
            return redirect()->route('admin.login')->with('info', 'Akun Anda sudah aktif sebelumnya. Silakan langsung login.');
        }

        $user->password = Hash::make($request->input('password'));

        if ($request->filled('email') && empty($user->email)) {
            $user->email = trim($request->input('email'));
        }

        $user->save();

        return redirect()->route('admin.login')->with('success', 'Aktivasi akun berhasil! Kata sandi baru Anda telah aktif. Silakan masuk sekarang.');
    }

    /**
     * Form Lupa Kata Sandi
     */
    public function showForgotPasswordForm()
    {
        return view('admin.forgot-password');
    }

    /**
     * Pengiriman Tautan Reset Password ke Email Anggota
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
        ], [
            'login_id.required' => 'Silakan masukkan NRI atau NIP Anda.',
        ]);

        $loginId = trim($request->input('login_id'));

        $member = Member::where(function ($query) use ($loginId) {
            $query->where('nri', $loginId)
                ->orWhere('nip', $loginId);
        })->first();

        if (! $member) {
            $member = Advisor::where('nip', $loginId)->first();
        }

        if (! $member) {
            return back()->withErrors(['login_id' => 'Nomor NRI atau NIP tidak ditemukan dalam sistem.'])->withInput();
        }

        if (empty($member->email)) {
            return back()->withErrors(['login_id' => 'Akun ini belum memiliki alamat email terdaftar. Silakan hubungi admin/pembina untuk mendaftarkan email Anda.'])->withInput();
        }

        // Buat token reset
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $member->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('admin.password.reset', [
            'token' => $token,
            'email' => $member->email,
        ]);

        $idLabel = $member instanceof Advisor ? ('NIP: '.$member->nip) : ('NRI: '.($member->nri ?? $member->nip));

        // Coba kirimkan email jika mailer siap, atau catat ke log
        try {
            Mail::raw("Halo {$member->name},\n\nAnda menerima email ini karena ada permintaan pengaturan ulang kata sandi untuk akun PMR Wira SMAN 1 Ciawi Anda ({$idLabel}).\n\nSilakan klik tautan berikut untuk membuat kata sandi baru:\n{$resetUrl}\n\nTautan ini berlaku selama 60 menit.\n\nJika Anda tidak melakukan permintaan ini, abaikan email ini.\n\nSalam,\nPMR WIRA SMAN 1 CIAWI", function ($message) use ($member) {
                $message->to($member->email)
                    ->subject('Tautan Reset Kata Sandi Akun PMR Wira SMAN 1 Ciawi');
            });
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email reset password: '.$e->getMessage(), [
                'reset_url' => $resetUrl,
            ]);
        }

        // Sensor email untuk keamanan (contoh: jo***@domain.com)
        $parts = explode('@', $member->email);
        $namePart = $parts[0];
        $domainPart = $parts[1] ?? '';
        $maskedEmail = (strlen($namePart) > 2 ? substr($namePart, 0, 2).str_repeat('*', strlen($namePart) - 2) : $namePart).'@'.$domainPart;

        return back()->with('success', "Tautan reset kata sandi telah dikirimkan ke email terdaftar Anda ({$maskedEmail}). Silakan periksa kotak masuk atau spam.");
    }

    /**
     * Form Input Kata Sandi Baru dari Tautan Reset
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        $email = $request->query('email');

        return view('admin.reset-password', compact('token', 'email'));
    }

    /**
     * Simpan Kata Sandi Baru dari Reset Link
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token reset kata sandi tidak valid atau telah kedaluwarsa.']);
        }

        // Cek kedaluwarsa (60 menit)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return back()->withErrors(['email' => 'Token reset kata sandi telah kedaluwarsa. Silakan ajukan permohonan baru.']);
        }

        $user = Member::where('email', $request->email)->first();
        if (! $user) {
            $user = Advisor::where('email', $request->email)->first();
        }

        if (! $user) {
            return back()->withErrors(['email' => 'Data pengguna dengan email tersebut tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('admin.login')->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
