@extends('errors.layout')

@section('title', 'Sedang Dalam Pemeliharaan')
@section('code', '503 SERVICE UNAVAILABLE')

@section('badge_class', 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20')
@section('dot_class', 'bg-emerald-500 animate-pulse')
@section('glow_class', 'bg-emerald-500/20')
@section('icon', 'fa-solid fa-screwdriver-wrench')
@section('icon_color', 'text-emerald-400')

@section('message')
    Portal PMR Wira SMAN 1 Ciawi saat ini sedang menjalani peningkatan sistem terjadwal agar pelayanan lomba, pendaftaran, dan informasi dapat berjalan lebih cepat dan optimal. Kami akan segera kembali!
@endsection

@section('helper_content')
    <div class="mt-6 p-4 rounded-2xl bg-slate-900/80 border border-slate-800 text-center text-xs">
        <div class="text-slate-300 font-bold mb-1 flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-clock text-amber-400"></i> Estimasi Pemeliharaan Singkat
        </div>
        <p class="text-slate-400">
            Pembaruan rutin biasanya memerlukan waktu 5 hingga 15 menit. Silakan muat ulang halaman ini secara berkala.
        </p>
        <button type="button" onclick="window.location.reload()" class="mt-3 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition inline-flex items-center gap-1.5 active:scale-95">
            <i class="fa-solid fa-arrows-rotate text-emerald-400"></i> Periksa Status Sekarang
        </button>
    </div>
@endsection
