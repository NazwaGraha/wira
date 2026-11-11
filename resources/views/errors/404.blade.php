@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404 NOT FOUND')

@section('badge_class', 'bg-rose-500/10 text-rose-400 border border-rose-500/20')
@section('dot_class', 'bg-rose-500 animate-pulse')
@section('glow_class', 'bg-rose-500/20')
@section('icon', 'fa-solid fa-compass-drafting')
@section('icon_color', 'text-rose-400')

@section('message')
    Tautan atau alamat yang Anda tuju mungkin sudah dipindahkan, diubah namanya, atau saat ini tidak tersedia. Mari kami bantu temukan informasi yang Anda cari.
@endsection

@section('helper_content')
    <div class="mt-6 p-4 rounded-2xl bg-slate-900/80 border border-slate-800 text-left text-xs">
        <div class="font-bold text-slate-200 mb-2 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-map-location-dot text-rose-400"></i> Halaman Populer & Pintasan:
            </span>
            <span class="text-[10px] text-slate-400 font-mono">PMR SMANIC</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <a href="{{ url('/') }}" class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700/60 hover:border-slate-600 transition block">
                <div class="font-bold text-white flex items-center gap-1.5 text-[11px]">
                    <i class="fa-solid fa-house text-rose-400"></i> Beranda
                </div>
                <p class="text-[10px] text-slate-400 truncate mt-0.5">Portal Utama</p>
            </a>

            <a href="{{ url('/lomba') }}" class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700/60 hover:border-slate-600 transition block">
                <div class="font-bold text-white flex items-center gap-1.5 text-[11px]">
                    <i class="fa-solid fa-trophy text-amber-400"></i> Sua Bhakti Berkarya
                </div>
                <p class="text-[10px] text-slate-400 truncate mt-0.5">Info & Pendaftaran Lomba</p>
            </a>

            <a href="{{ url('/artikel') }}" class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700/60 hover:border-slate-600 transition block">
                <div class="font-bold text-white flex items-center gap-1.5 text-[11px]">
                    <i class="fa-solid fa-newspaper text-blue-400"></i> Berita & Artikel
                </div>
                <p class="text-[10px] text-slate-400 truncate mt-0.5">Kabar Kegiatan PMR</p>
            </a>

            <a href="{{ url('/donor-darah') }}" class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700/60 hover:border-slate-600 transition block">
                <div class="font-bold text-white flex items-center gap-1.5 text-[11px]">
                    <i class="fa-solid fa-heart-pulse text-red-400"></i> Donor Darah
                </div>
                <p class="text-[10px] text-slate-400 truncate mt-0.5">Jadwal & Permohonan</p>
            </a>

            <a href="{{ url('/galeri') }}" class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700/60 hover:border-slate-600 transition block">
                <div class="font-bold text-white flex items-center gap-1.5 text-[11px]">
                    <i class="fa-solid fa-images text-emerald-400"></i> Galeri Foto
                </div>
                <p class="text-[10px] text-slate-400 truncate mt-0.5">Dokumentasi Aksi</p>
            </a>

            <a href="{{ url('/kontak') }}" class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700/60 hover:border-slate-600 transition block">
                <div class="font-bold text-white flex items-center gap-1.5 text-[11px]">
                    <i class="fa-solid fa-phone text-purple-400"></i> Kontak & UKS
                </div>
                <p class="text-[10px] text-slate-400 truncate mt-0.5">Hubungi Pengurus</p>
            </a>
        </div>
    </div>
@endsection
