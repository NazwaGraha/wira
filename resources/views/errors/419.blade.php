@extends('errors.layout')

@section('title', 'Sesi Halaman Berakhir')
@section('code', '419 PAGE EXPIRED')

@section('badge_class', 'bg-blue-500/10 text-blue-400 border border-blue-500/20')
@section('dot_class', 'bg-blue-500 animate-pulse')
@section('glow_class', 'bg-blue-500/20')
@section('icon', 'fa-solid fa-clock-rotate-left')
@section('icon_color', 'text-blue-400')

@section('message')
    Sesi keamanan halaman Anda telah berakhir karena tidak ada aktivitas dalam waktu tertentu. Cukup muat ulang halaman ini untuk memperbarui token keamanan Anda.
@endsection

@section('helper_content')
    <div class="mt-6">
        <button type="button" onclick="window.location.reload()" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-sky-600 hover:from-blue-500 hover:to-sky-500 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-blue-950/50 transition flex items-center justify-center gap-2 mx-auto active:scale-95">
            <i class="fa-solid fa-arrows-rotate"></i>
            <span>Muat Ulang Halaman (Refresh)</span>
        </button>
        <p class="text-[11px] text-slate-400 mt-2">
            Tip: Jika Anda sedang mengisi formulir, pastikan data tersimpan sebelum berpindah aplikasi.
        </p>
    </div>
@endsection
