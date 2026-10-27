@extends('jury.layout')

@section('title', 'Wajib Ganti Password - Login Pertama Juri')

@section('content')
<div class="flex-1 flex flex-col justify-between p-6 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-white min-h-screen">
    
    <!-- Top Brand Header -->
    <div class="pt-6 text-center space-y-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
            <i class="fa-solid fa-shield-halved"></i> Aktivasi Akun &bull; Login Pertama
        </div>
        <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-amber-500 to-orange-600 mx-auto flex items-center justify-center text-white text-2xl shadow-xl shadow-amber-900/40 border border-white/20">
            <i class="fa-solid fa-key"></i>
        </div>
        <h1 class="text-2xl font-black tracking-tight text-white">
            Buat Kata Sandi / PIN Baru
        </h1>
        <p class="text-slate-300 text-xs max-w-xs mx-auto leading-relaxed">
            Halo <strong class="text-amber-300">{{ $user->name }}</strong> (<code>{{ $user->username }}</code>), demi keamanan data penilaian lomba, silakan buat kata sandi atau PIN baru akun juri Anda.
        </p>
    </div>

    <!-- Change Password Card -->
    <div class="my-auto py-6">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-md space-y-4">
            
            <div class="p-3.5 rounded-2xl bg-amber-950/60 border border-amber-800/80 text-amber-200 text-xs flex items-start gap-2.5 shadow-md">
                <i class="fa-solid fa-circle-info text-amber-400 text-sm mt-0.5 shrink-0"></i>
                <div class="leading-relaxed">
                    Setelah kata sandi baru disimpan, Anda akan diarahkan kembali ke halaman login untuk masuk menggunakan kata sandi baru Anda.
                </div>
            </div>

            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-950/80 border border-rose-800 text-rose-200 text-xs flex items-start gap-2.5 shadow-md">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm mt-0.5 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('jury.first-password-change.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Kata Sandi / PIN Baru -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">
                        Password / PIN Baru <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                        <input type="password" name="password" required autofocus
                            placeholder="Minimal 4 karakter (misal: 6 digit PIN)"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 focus:outline-none rounded-2xl pl-11 pr-4 py-3 text-sm font-mono text-white placeholder-slate-600 transition shadow-inner">
                    </div>
                    <span class="text-[10px] text-slate-500 mt-1 block">Bisa berupa 6 digit angka PIN agar mudah diketik di smartphone.</span>
                </div>

                <!-- Konfirmasi Kata Sandi Baru -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">
                        Konfirmasi Password / PIN Baru <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-check-double absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                        <input type="password" name="password_confirmation" required
                            placeholder="Ulangi password / PIN baru Anda"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 focus:outline-none rounded-2xl pl-11 pr-4 py-3 text-sm font-mono text-white placeholder-slate-600 transition shadow-inner">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 hover:from-amber-600 hover:to-orange-700 text-slate-950 font-black text-sm rounded-2xl shadow-xl shadow-amber-900/40 transition flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                    <i class="fa-solid fa-check-circle text-base"></i> Simpan Password Baru & Selesai
                </button>
            </form>

            <div class="text-center pt-2">
                <a href="{{ route('jury.login') }}" class="text-xs text-slate-400 hover:text-white transition">
                    &larr; Batalkan & Kembali ke Halaman Login
                </a>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center pb-4 text-[11px] text-slate-500 space-y-1">
        <div>PMR Wira SMAN 1 Ciawi Kabupaten Bogor</div>
        <div class="text-[10px] text-slate-600">Sistem Penjurian Lapangan &bull; Sua Bhakti Berkarya</div>
    </div>

</div>
@endsection
