@extends('layouts.app')

@section('title', $event->title ?? 'Lomba PMR - Sua Bhakti Berkarya')

@section('content')
<!-- Hero Section -->
<section class="relative bg-slate-900 text-white pt-32 pb-20 overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#ef4444_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase mb-6">
                <i class="fa-solid fa-trophy"></i> {{ $event->theme ?: 'AJANG PRESTASI RELAWAN MUDA PMR WIRA CIAWI' }}
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                {{ $event->title ?? 'SUA BHAKTI BERKARYA III TAHUN 2025' }}
            </h1>
            <p class="text-slate-300 text-base md:text-lg mb-8 leading-relaxed">
                {{ $event->description ?? 'Ajang kompetisi kepalangmerahan bergengsi tingkat Mula (SD), Madya (SMP), dan Wira (SMA/SMK/MA) se-Jabodetabek dan sekitarnya.' }}
            </p>

            <div class="flex flex-wrap justify-center gap-4">
                @if($event && $event->is_registration_open)
                    <a href="{{ route('lomba.register') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3.5 rounded-xl font-bold shadow-lg shadow-red-900/40 transition flex items-center gap-2">
                        <i class="fa-solid fa-file-pen"></i> Daftar Sekarang
                    </a>
                @endif
                <a href="{{ route('lomba.status') }}" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white px-7 py-3.5 rounded-xl font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Cek Status Pendaftaran
                </a>
                <a href="{{ route('lomba.scoreboard') }}" class="bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 px-7 py-3.5 rounded-xl font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-square-poll-vertical"></i> Live Klasemen Juara
                </a>
                @if($event && $event->handbook_file)
                    <a href="{{ asset('storage/' . $event->handbook_file) }}" target="_blank" class="bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/40 text-indigo-300 px-7 py-3.5 rounded-xl font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf"></i> Unduh Juklak Juknis
                    </a>
                @endif
            </div>
        </div>

        <!-- Quick Info Banner -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-slate-800/80 border border-slate-700/80 backdrop-blur p-5 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-semibold">Pelaksanaan</div>
                    <div class="text-sm font-bold text-white">
                        @if($event && $event->start_date)
                            @if($event->end_date && $event->end_date->format('Y-m-d') !== $event->start_date->format('Y-m-d'))
                                {{ $event->start_date->translatedFormat('d') }} - {{ $event->end_date->translatedFormat('d F Y') }}
                            @else
                                {{ $event->start_date->translatedFormat('d F Y') }}
                            @endif
                        @else
                            15 - 16 Oktober 2026
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-slate-800/80 border border-slate-700/80 backdrop-blur p-5 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-semibold">Lokasi</div>
                    <div class="text-sm font-bold text-white">{{ $event->location ?? 'Kampus SMAN 1 Ciawi Bogor' }}</div>
                </div>
            </div>

            <!-- Replaced: Menu Informasi Lomba (Previously Biaya Registrasi) -->
            <button type="button" onclick="openInfoModal()" class="text-left bg-gradient-to-br from-slate-800/90 via-slate-800/70 to-slate-800/90 hover:from-slate-800 hover:to-red-950/40 border border-slate-700/80 hover:border-red-500/50 backdrop-blur p-5 rounded-2xl flex items-center gap-4 transition-all duration-200 hover:shadow-xl hover:shadow-red-950/30 cursor-pointer group w-full relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-red-500/5 rounded-full blur-xl pointer-events-none"></div>
                <div class="w-12 h-12 rounded-xl bg-red-500/10 group-hover:bg-red-500/20 text-red-400 group-hover:text-red-300 flex items-center justify-center text-xl shrink-0 transition-transform duration-200 group-hover:scale-110 shadow-inner">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div class="flex-grow min-w-0">
                    <div class="flex items-center justify-between gap-1 mb-0.5">
                        <span class="text-xs text-red-400 font-bold uppercase tracking-wider">Pusat Informasi</span>
                        <span class="text-[10px] bg-red-500/20 text-red-300 px-2 py-0.5 rounded-full font-extrabold border border-red-500/30">8 Menu</span>
                    </div>
                    <div class="text-sm font-black text-white group-hover:text-red-200 transition truncate">Menu Informasi Lomba</div>
                    <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5 font-medium">
                        <span>Buka Berkas & Panduan</span>
                        <i class="fa-solid fa-arrow-right text-[9px] text-red-400 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </button>
        </div>
    </div>
</section>

<!-- Mata Lomba Section -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl font-extrabold text-slate-900">Mata Lomba yang Dipertandingkan</h2>
            <p class="text-slate-600 text-sm mt-2">Pilih tingkatan sekolah Anda untuk melihat cabang lomba yang tersedia.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- MULA (SD) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-extrabold px-3 py-1 rounded-full uppercase">Tingkat SD</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-2">PMR Mula</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-child-reaching"></i>
                    </div>
                </div>
                <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                    @foreach($categoriesByLevel->get('Mula', []) as $cat)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i>
                            <span><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') ({{ $cat->gender_category }}) @endif</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('lomba.register') }}?level=Mula" class="w-full text-center py-2.5 rounded-xl font-bold bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 transition text-sm">
                    Daftar Tingkat Mula &rarr;
                </a>
            </div>

            <!-- MADYA (SMP) -->
            <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-red-500/30 relative flex flex-col">
                <div class="absolute -top-3 right-6 bg-red-600 text-white text-[10px] font-extrabold px-3 py-0.5 rounded-full uppercase tracking-wider">
                    Paling Favorit
                </div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <span class="bg-red-100 text-red-800 text-xs font-extrabold px-3 py-1 rounded-full uppercase">Tingkat SMP</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-2">PMR Madya</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                </div>
                <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                    @foreach($categoriesByLevel->get('Madya', []) as $cat)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-red-500 mt-1"></i>
                            <span><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') ({{ $cat->gender_category }}) @endif</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('lomba.register') }}?level=Madya" class="w-full text-center py-2.5 rounded-xl font-bold bg-red-600 hover:bg-red-700 text-white shadow-md shadow-red-900/20 transition text-sm">
                    Daftar Tingkat Madya &rarr;
                </a>
            </div>

            <!-- WIRA (SMA) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <span class="bg-amber-100 text-amber-800 text-xs font-extrabold px-3 py-1 rounded-full uppercase">Tingkat SMA / SMK / MA</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-2">PMR Wira</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                </div>
                <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                    @foreach($categoriesByLevel->get('Wira', []) as $cat)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-amber-500 mt-1"></i>
                            <span><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') ({{ $cat->gender_category }}) @endif</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('lomba.register') }}?level=Wira" class="w-full text-center py-2.5 rounded-xl font-bold bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-700 transition text-sm">
                    Daftar Tingkat Wira &rarr;
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- MODAL POPUP: MENU INFORMASI LOMBA (8 SUB MENU TERPADU)       -->
<!-- ============================================================ -->
<div id="modal-informasi-lomba" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-md transition-all duration-300 flex items-center justify-center p-3 sm:p-5" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-5xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border border-slate-700/80 rounded-3xl shadow-2xl shadow-red-950/40 overflow-hidden transform transition-all text-white my-6">
        
        <!-- Modal Header -->
        <div class="p-5 sm:p-7 border-b border-slate-800 flex items-center justify-between gap-4 bg-slate-900/80 backdrop-blur sticky top-0 z-10">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-600 to-rose-700 text-white flex items-center justify-center text-xl shadow-lg shadow-red-900/50 shrink-0">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-300 border border-red-500/30">
                            <i class="fa-solid fa-circle-info mr-1"></i> Pusat Unduhan & Berkas
                        </span>
                        <span class="text-xs text-slate-400 font-semibold hidden sm:inline">{{ $event->title ?? 'Sua Bhakti Berkarya' }}</span>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-black text-white mt-1">Menu Informasi Lomba</h3>
                </div>
            </div>
            <button type="button" onclick="closeInfoModal()" class="w-10 h-10 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition border border-slate-700 cursor-pointer shrink-0" title="Tutup Modal (ESC)">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Body: 8 Sub Menu Cards Grid -->
        <div class="p-5 sm:p-8 space-y-6 max-h-[75vh] overflow-y-auto">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-800/40 p-3.5 rounded-2xl border border-slate-700/50 text-xs text-slate-300">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bullhorn text-amber-400 text-sm"></i>
                    <span>Pilih sub menu di bawah untuk mengunduh berkas resmi, melihat panduan, denah lokasi, atau menghubungi panitia.</span>
                </div>
                <span class="text-[11px] font-bold text-slate-400 shrink-0">Total: 8 Sub Menu</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- 1. Surat Rekomendasi -->
                <div class="bg-slate-800/60 hover:bg-slate-800/90 border border-slate-700/70 hover:border-red-500/50 rounded-2xl p-4.5 transition-all duration-200 flex flex-col justify-between group hover:shadow-lg hover:shadow-red-950/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-red-500/10 text-red-400 group-hover:bg-red-500/20 flex items-center justify-center text-lg transition">
                                <i class="fa-solid fa-file-shield"></i>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-red-950/80 text-red-300 border border-red-500/30">
                                Dokumen Resmi
                            </span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-sm group-hover:text-red-300 transition">Surat Rekomendasi</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-snug">Dokumen rekomendasi izin kegiatan dari PMI dan instansi kedinasan terkait.</p>
                        </div>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-700/60">
                        <button type="button" onclick="handleDocumentAction('Surat Rekomendasi', 'rekomendasi')" class="w-full py-2 px-3 rounded-xl bg-red-600/20 hover:bg-red-600 text-red-300 hover:text-white font-bold text-xs transition border border-red-500/30 flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-file-arrow-down text-xs"></i>
                            <span>Unduh / Lihat Dokumen</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Surat Undangan Lomba -->
                <div class="bg-slate-800/60 hover:bg-slate-800/90 border border-slate-700/70 hover:border-sky-500/50 rounded-2xl p-4.5 transition-all duration-200 flex flex-col justify-between group hover:shadow-lg hover:shadow-sky-950/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 group-hover:bg-sky-500/20 flex items-center justify-center text-lg transition">
                                <i class="fa-solid fa-envelope-open-text"></i>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-sky-950/80 text-sky-300 border border-sky-500/30">
                                Undangan
                            </span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-sm group-hover:text-sky-300 transition">Surat Undangan Lomba</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-snug">Surat edaran undangan resmi partisipasi lomba untuk kepala sekolah & pembina PMR.</p>
                        </div>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-700/60">
                        <button type="button" onclick="handleDocumentAction('Surat Undangan Lomba', 'undangan')" class="w-full py-2 px-3 rounded-xl bg-sky-600/20 hover:bg-sky-600 text-sky-300 hover:text-white font-bold text-xs transition border border-sky-500/30 flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-file-arrow-down text-xs"></i>
                            <span>Unduh Undangan</span>
                        </button>
                    </div>
                </div>

                <!-- 3. Juklak Juknis -->
                <div class="bg-slate-800/60 hover:bg-slate-800/90 border border-slate-700/70 hover:border-rose-500/50 rounded-2xl p-4.5 transition-all duration-200 flex flex-col justify-between group hover:shadow-lg hover:shadow-rose-950/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 group-hover:bg-rose-500/20 flex items-center justify-center text-lg transition">
                                <i class="fa-solid fa-book-bookmark"></i>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-rose-950/80 text-rose-300 border border-rose-500/30">
                                Wajib Dibaca
                            </span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-sm group-hover:text-rose-300 transition">Juklak Juknis</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-snug">Petunjuk Pelaksanaan & Petunjuk Teknis aturan resmi perlombaan dan tata tertib.</p>
                        </div>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-700/60">
                        @if($event && $event->handbook_file)
                            <a href="{{ asset('storage/' . $event->handbook_file) }}" target="_blank" class="w-full py-2 px-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-md shadow-rose-900/30">
                                <i class="fa-solid fa-file-pdf text-xs"></i>
                                <span>Unduh Juklak Juknis</span>
                            </a>
                        @else
                            <button type="button" onclick="handleDocumentAction('Juklak Juknis Lomba', 'juklak')" class="w-full py-2 px-3 rounded-xl bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white font-bold text-xs transition border border-rose-500/30 flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-file-pdf text-xs"></i>
                                <span>Unduh Juklak Juknis</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- 4. Grid Nilai -->
                <div class="bg-slate-800/60 hover:bg-slate-800/90 border border-slate-700/70 hover:border-amber-500/50 rounded-2xl p-4.5 transition-all duration-200 flex flex-col justify-between group hover:shadow-lg hover:shadow-amber-950/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 group-hover:bg-amber-500/20 flex items-center justify-center text-lg transition">
                                <i class="fa-solid fa-table-list"></i>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-950/80 text-amber-300 border border-amber-500/30">
                                Transparansi
                            </span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-sm group-hover:text-amber-300 transition">Grid Nilai</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-snug">Matriks rubrik penilaian juri, bobot kriteria teknis, dan rumus perhitungan skor.</p>
                        </div>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-700/60">
                        <button type="button" onclick="handleDocumentAction('Grid & Rubrik Nilai Juri', 'grid_nilai')" class="w-full py-2 px-3 rounded-xl bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-white font-bold text-xs transition border border-amber-500/30 flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-chart-column text-xs"></i>
                            <span>Lihat Rubrik Nilai</span>
                        </button>
                    </div>
                </div>

                <!-- 5. Peta Lokasi Lomba -->
                <div class="bg-slate-800/60 hover:bg-slate-800/90 border border-slate-700/70 hover:border-emerald-500/50 rounded-2xl p-4.5 transition-all duration-200 flex flex-col justify-between group hover:shadow-lg hover:shadow-emerald-950/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20 flex items-center justify-center text-lg transition">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-950/80 text-emerald-300 border border-emerald-500/30">
                                Venue & Denah
                            </span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-sm group-hover:text-emerald-300 transition">Peta Lokasi Lomba</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-snug">Denah kampus SMAN 1 Ciawi, posisi pos mata lomba, area transit, dan rute navigasi.</p>
                        </div>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-700/60">
                        <a href="https://maps.google.com/?q=SMAN+1+Ciawi+Bogor" target="_blank" class="w-full py-2 px-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white font-bold text-xs transition border border-emerald-500/30 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-diamond-turn-right text-xs"></i>
                            <span>Buka Google Maps</span>
                        </a>
                    </div>
                </div>

                <!-- 6. Buku Panduan Lomba -->
                <div class="bg-slate-800/60 hover:bg-slate-800/90 border border-slate-700/70 hover:border-purple-500/50 rounded-2xl p-4.5 transition-all duration-200 flex flex-col justify-between group hover:shadow-lg hover:shadow-purple-950/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 group-hover:bg-purple-500/20 flex items-center justify-center text-lg transition">
                                <i class="fa-solid fa-book-open-reader"></i>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-purple-950/80 text-purple-300 border border-purple-500/30">
                                Handbook
                            </span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-sm group-hover:text-purple-300 transition">Buku Panduan Lomba</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-snug">Panduan teknis operasional untuk kontingen, pembina pendamping, dan peserta lomba.</p>
                        </div>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-700/60">
                        @if($event && $event->handbook_file)
                            <a href="{{ asset('storage/' . $event->handbook_file) }}" target="_blank" class="w-full py-2 px-3 rounded-xl bg-purple-600/20 hover:bg-purple-600 text-purple-300 hover:text-white font-bold text-xs transition border border-purple-500/30 flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-file-arrow-down text-xs"></i>
                                <span>Unduh Handbook</span>
                            </a>
                        @else
                            <button type="button" onclick="handleDocumentAction('Buku Panduan Lomba (Handbook)', 'panduan')" class="w-full py-2 px-3 rounded-xl bg-purple-600/20 hover:bg-purple-600 text-purple-300 hover:text-white font-bold text-xs transition border border-purple-500/30 flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-file-arrow-down text-xs"></i>
                                <span>Unduh Handbook</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- 7. Dokumentasi Kegiatan -->
                <div class="bg-slate-800/60 hover:bg-slate-800/90 border border-slate-700/70 hover:border-cyan-500/50 rounded-2xl p-4.5 transition-all duration-200 flex flex-col justify-between group hover:shadow-lg hover:shadow-cyan-950/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 group-hover:bg-cyan-500/20 flex items-center justify-center text-lg transition">
                                <i class="fa-solid fa-photo-film"></i>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-cyan-950/80 text-cyan-300 border border-cyan-500/30">
                                Galeri Media
                            </span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-sm group-hover:text-cyan-300 transition">Dokumentasi Kegiatan</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-snug">Koleksi foto, video rekaman lomba, dan kilas balik gelaran Sua Bhakti Berkarya.</p>
                        </div>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-700/60">
                        <a href="https://instagram.com/pmrwirasman1c" target="_blank" class="w-full py-2 px-3 rounded-xl bg-cyan-600/20 hover:bg-cyan-600 text-cyan-300 hover:text-white font-bold text-xs transition border border-cyan-500/30 flex items-center justify-center gap-1.5">
                            <i class="fa-brands fa-instagram text-xs"></i>
                            <span>Lihat Galeri Foto & Video</span>
                        </a>
                    </div>
                </div>

                <!-- 8. Contact Person -->
                <div class="bg-slate-800/60 hover:bg-slate-800/90 border border-slate-700/70 hover:border-emerald-500/50 rounded-2xl p-4.5 transition-all duration-200 flex flex-col justify-between group hover:shadow-lg hover:shadow-emerald-950/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20 flex items-center justify-center text-lg transition">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-950/80 text-emerald-300 border border-emerald-500/30">
                                Hotline 24/7
                            </span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-sm group-hover:text-emerald-300 transition">Contact Person</h4>
                            <p class="text-xs text-slate-400 mt-1 leading-snug">Layanan konsultasi resmi narahubung panitia lomba untuk pertanyaan dan konfirmasi.</p>
                        </div>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-700/60">
                        <a href="https://wa.me/6281383885600?text=Halo%20Panitia%20Sua%20Bhakti%20Berkarya%2C%20saya%20ingin%20bertanya%20seputar%20informasi%20lomba" target="_blank" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-md shadow-emerald-950/40">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Chat WhatsApp Panitia</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 sm:p-6 bg-slate-950/90 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
            <div class="flex items-center gap-2 text-center sm:text-left">
                <i class="fa-solid fa-circle-question text-amber-400 text-sm shrink-0"></i>
                <span>Membutuhkan surat resmi khusus atau konfirmasi berkas? Hubungi sekretariat panitia lomba.</span>
            </div>
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <a href="https://wa.me/6281383885600?text=Halo%20Panitia%20Sua%20Bhakti%20Berkarya%2C%20saya%20ingin%20bertanya%20seputar%20informasi%20lomba" target="_blank" class="flex-1 sm:flex-initial bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-brands fa-whatsapp text-sm"></i> Hubungi Panitia
                </a>
                <button type="button" onclick="closeInfoModal()" class="flex-1 sm:flex-initial bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold px-4 py-2 rounded-xl transition border border-slate-700 cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Mini Notice Dialog for Pending Upload Documents -->
<div id="modal-doc-notice" class="fixed inset-0 z-60 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-md transition-all duration-200 flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-md w-full p-6 text-white text-center space-y-4 shadow-2xl">
        <div class="w-14 h-14 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center justify-center text-2xl mx-auto">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div>
            <h4 class="text-base font-black text-white" id="notice-doc-title">Dokumen Dalam Proses</h4>
            <p class="text-xs text-slate-300 mt-2 leading-relaxed" id="notice-doc-message">
                Berkas ini sedang dalam tahap pengesahan akhir oleh panitia & instansi terkait. Anda dapat meminta salinan dokumen secara langsung kepada narahubung panitia.
            </p>
        </div>
        <div class="pt-2 flex flex-col sm:flex-row gap-2.5 justify-center">
            <a id="notice-wa-btn" href="https://wa.me/6281383885600?text=Halo%20Panitia%20Sua%20Bhakti%20Berkarya%2C%20saya%20ingin%20meminta%20salinan%20dokumen%20lomba" target="_blank" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 shadow-md">
                <i class="fa-brands fa-whatsapp text-sm"></i> Hubungi Narahubung via WA
            </a>
            <button type="button" onclick="closeDocNotice()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs px-4 py-2.5 rounded-xl transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openInfoModal() {
        const modal = document.getElementById('modal-informasi-lomba');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeInfoModal() {
        const modal = document.getElementById('modal-informasi-lomba');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function handleDocumentAction(docTitle, docType) {
        const noticeModal = document.getElementById('modal-doc-notice');
        const titleEl = document.getElementById('notice-doc-title');
        const msgEl = document.getElementById('notice-doc-message');
        const waBtn = document.getElementById('notice-wa-btn');

        if (titleEl) titleEl.innerText = docTitle;
        if (msgEl) {
            msgEl.innerText = `Dokumen ${docTitle} saat ini sedang dalam proses distribusi/pengesahan akhir oleh panitia pelaksana. Silakan hubungi narahubung panitia untuk mendapatkan berkas digital resmi secara langsung via WhatsApp.`;
        }
        if (waBtn) {
            waBtn.href = `https://wa.me/6281383885600?text=${encodeURIComponent('Halo Panitia Sua Bhakti Berkarya, saya ingin meminta berkas resmi: ' + docTitle)}`;
        }

        if (noticeModal) {
            noticeModal.classList.remove('hidden');
        }
    }

    function closeDocNotice() {
        const noticeModal = document.getElementById('modal-doc-notice');
        if (noticeModal) {
            noticeModal.classList.add('hidden');
        }
    }

    // Close on backdrop click
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modal-informasi-lomba');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeInfoModal();
                }
            });
        }

        const noticeModal = document.getElementById('modal-doc-notice');
        if (noticeModal) {
            noticeModal.addEventListener('click', function(e) {
                if (e.target === noticeModal) {
                    closeDocNotice();
                }
            });
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDocNotice();
                closeInfoModal();
            }
        });
    });
</script>
@endpush
@endsection
