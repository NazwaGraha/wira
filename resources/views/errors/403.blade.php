@extends('errors.layout')

@section('title', 'Akses Terbatas / Ditolak')
@section('code', '403 FORBIDDEN')

@section('badge_class', 'bg-amber-500/10 text-amber-400 border border-amber-500/20')
@section('dot_class', 'bg-amber-500 animate-pulse')
@section('glow_class', 'bg-amber-500/20')
@section('icon', 'fa-solid fa-lock')
@section('icon_color', 'text-amber-400')

@section('message')
    {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki hak akses atau izin untuk membuka halaman ini. Silakan hubungi Pembina atau Administrator jika Anda memerlukan akses khusus.' }}
@endsection

@section('helper_content')
    <div class="mt-6 p-4 rounded-2xl bg-slate-900/80 border border-slate-800 text-left text-xs text-slate-300">
        <div class="font-bold text-slate-200 mb-1 flex items-center gap-1.5">
            <i class="fa-solid fa-shield-halved text-amber-400"></i> Informasi Hak Akses:
        </div>
        <p class="text-slate-400 leading-relaxed">
            Halaman ini dilindungi sistem keamanan berbasis peran (Role-Based Access Control). Jika Anda adalah panitia, juri, atau petugas parkir, pastikan Anda telah masuk dengan akun yang sesuai.
        </p>
        <div class="mt-3 flex flex-wrap gap-2">
            <a href="{{ route('jury.login') }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-sky-400 text-[11px] font-bold border border-slate-700 transition">
                <i class="fa-solid fa-gavel mr-1"></i> Portal Juri
            </a>
            <a href="{{ route('parking.login') }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-emerald-400 text-[11px] font-bold border border-slate-700 transition">
                <i class="fa-solid fa-square-parking mr-1"></i> Petugas Parkir
            </a>
            <a href="{{ route('admin.login') }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 text-[11px] font-bold border border-slate-700 transition">
                <i class="fa-solid fa-user-shield mr-1"></i> Admin Backoffice
            </a>
        </div>
    </div>
@endsection
