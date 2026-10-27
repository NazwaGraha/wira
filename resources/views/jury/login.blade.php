@extends('jury.layout')

@section('title', 'Login Juri Lapangan')

@section('content')
<div class="flex-1 flex flex-col justify-between p-6 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-white min-h-screen">
    
    <!-- Top Brand Header -->
    <div class="pt-6 text-center space-y-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-bold uppercase tracking-wider">
            <i class="fa-solid fa-gavel"></i> Portal Khusus Juri
        </div>
        <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-red-600 to-rose-500 mx-auto flex items-center justify-center text-white text-2xl shadow-xl shadow-red-900/40 border border-white/20">
            <i class="fa-solid fa-scale-balanced"></i>
        </div>
        <h1 class="text-2xl font-black tracking-tight text-white">
            Penilaian Lomba PMR
        </h1>
        <p class="text-slate-400 text-xs max-w-xs mx-auto leading-relaxed">
            Sua Bhakti Berkarya &bull; Masukkan kredensial akun juri yang diberikan oleh panitia.
        </p>
    </div>

    <!-- Login Form Card -->
    <div class="my-auto py-6">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-md space-y-4">
            
            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-950/80 border border-rose-800 text-rose-200 text-xs flex items-start gap-2.5 shadow-md">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm mt-0.5 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-950/80 border border-emerald-800 text-emerald-200 text-xs flex items-start gap-2.5 shadow-md">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm mt-0.5 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('jury.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Username Input -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">
                        Username / Kode Juri
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                            placeholder="Contoh: juri_tandu1"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-red-500 focus:outline-none rounded-2xl pl-11 pr-4 py-3 text-sm text-white font-bold placeholder-slate-600 transition shadow-inner">
                    </div>
                </div>

                <!-- Password / PIN Input -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">
                        PIN / Kata Sandi
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                        <input type="password" name="password" required
                            placeholder="Masukkan PIN / Password"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-red-500 focus:outline-none rounded-2xl pl-11 pr-4 py-3 text-sm font-mono text-white placeholder-slate-600 transition shadow-inner">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded text-red-600 bg-slate-950 border-slate-700">
                        <span>Ingat di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:to-rose-800 text-white font-black text-sm rounded-2xl shadow-xl shadow-red-900/40 transition flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                    <i class="fa-solid fa-arrow-right-to-bracket text-base"></i> Masuk ke Dashboard Juri
                </button>
            </form>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center pb-4 text-[11px] text-slate-500 space-y-1">
        <div>PMR Wira SMAN 1 Ciawi Kabupaten Bogor</div>
        <div class="text-[10px] text-slate-600">Butuh bantuan akun? Hubungi meja panitia sekretariat lomba.</div>
    </div>

</div>
@endsection
