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
            <!-- MULA (SD / MI) - Dominasi HIJAU -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm hover:shadow-xl border-2 border-emerald-500/40 hover:border-emerald-500 transition-all flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-green-600"></div>
                <div class="flex items-center justify-between pb-4 border-b border-emerald-100/70 mb-5">
                    <div>
                        <span class="bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Tingkat SD / MI</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">
                            PMR Mula
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center text-xl shadow-xs group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-child-reaching"></i>
                    </div>
                </div>

                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60 w-fit mb-4">
                    <i class="fa-solid fa-layer-group text-emerald-600"></i>
                    <span>{{ $categoriesByLevel->get('Mula', collect())->count() }} Cabang Lomba Tersedia</span>
                </div>

                <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                    @foreach($categoriesByLevel->get('Mula', []) as $cat)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                            <span class="text-slate-700"><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/50">({{ $cat->gender_category }})</span> @endif</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('lomba.register') }}?level=Mula" class="w-full text-center py-3 rounded-xl font-extrabold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-950/15 transition flex items-center justify-center gap-2 text-sm group-hover:shadow-lg">
                    <span>Daftar Tingkat Mula</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- MADYA (SMP / MTs) - Dominasi BIRU -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm hover:shadow-xl border-2 border-blue-500/40 hover:border-blue-600 transition-all flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
                <div class="flex items-center justify-between pb-4 border-b border-blue-100/70 mb-5">
                    <div>
                        <span class="bg-blue-100 text-blue-800 border border-blue-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Tingkat SMP / MTs</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">
                            PMR Madya
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block animate-pulse"></span>
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center text-xl shadow-xs group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                </div>

                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200/60 w-fit mb-4">
                    <i class="fa-solid fa-layer-group text-blue-600"></i>
                    <span>{{ $categoriesByLevel->get('Madya', collect())->count() }} Cabang Lomba Tersedia</span>
                </div>

                <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                    @foreach($categoriesByLevel->get('Madya', []) as $cat)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-blue-500 mt-1 shrink-0"></i>
                            <span class="text-slate-700"><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200/50">({{ $cat->gender_category }})</span> @endif</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('lomba.register') }}?level=Madya" class="w-full text-center py-3 rounded-xl font-extrabold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-950/15 transition flex items-center justify-center gap-2 text-sm group-hover:shadow-lg">
                    <span>Daftar Tingkat Madya</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <!-- WIRA (SMA / SMK / MA) - Dominasi KUNING -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm hover:shadow-xl border-2 border-amber-500/40 hover:border-amber-500 transition-all flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500"></div>
                <div class="flex items-center justify-between pb-4 border-b border-amber-100/70 mb-5">
                    <div>
                        <span class="bg-amber-100 text-amber-900 border border-amber-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Tingkat SMA / SMK / MA</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">
                            PMR Wira
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block animate-pulse"></span>
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center text-xl shadow-xs group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                </div>

                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-900 border border-amber-200/60 w-fit mb-4">
                    <i class="fa-solid fa-layer-group text-amber-600"></i>
                    <span>{{ $categoriesByLevel->get('Wira', collect())->count() }} Cabang Lomba Tersedia</span>
                </div>

                <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                    @foreach($categoriesByLevel->get('Wira', []) as $cat)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-amber-500 mt-1 shrink-0"></i>
                            <span class="text-slate-700"><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') <span class="text-xs font-semibold text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200/50">({{ $cat->gender_category }})</span> @endif</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('lomba.register') }}?level=Wira" class="w-full text-center py-3 rounded-xl font-extrabold bg-amber-400 hover:bg-amber-500 text-slate-950 shadow-md shadow-amber-950/15 transition flex items-center justify-center gap-2 text-sm group-hover:shadow-lg">
                    <span>Daftar Tingkat Wira</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- MODAL POPUP: MENU INFORMASI LOMBA (8 SUB MENU TERPADU)       -->
<!-- ============================================================ -->
<div id="modal-informasi-lomba" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/85 backdrop-blur-md transition-all duration-300 flex items-center justify-center p-3 sm:p-6" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-7xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border-2 border-slate-700/80 rounded-3xl shadow-2xl shadow-red-950/50 overflow-hidden transform transition-all text-white my-4 sm:my-8">
        
        <!-- Modal Header -->
        <div class="p-6 sm:p-8 border-b border-slate-800 flex items-center justify-between gap-4 bg-slate-900/90 backdrop-blur sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-red-600 to-rose-700 text-white flex items-center justify-center text-2xl sm:text-3xl shadow-xl shadow-red-900/50 shrink-0">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-red-500/20 text-red-300 border border-red-500/40">
                            <i class="fa-solid fa-circle-info mr-1.5"></i> Pusat Unduhan & Dokumen Resmi
                        </span>
                        <span class="text-sm text-slate-300 font-bold hidden sm:inline">{{ $event->title ?? 'Sua Bhakti Berkarya' }}</span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl md:text-4xl font-black text-white mt-1.5 tracking-tight">Menu Informasi Lomba</h3>
                </div>
            </div>
            <button type="button" onclick="closeInfoModal()" class="w-12 h-12 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition border border-slate-700 cursor-pointer shrink-0 shadow-md" title="Tutup Modal (ESC)">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Modal Body: Sub Menu Cards Grid (Managed via Backoffice) -->
        <div class="p-6 sm:p-8 md:p-10 space-y-6 sm:space-y-8 max-h-[75vh] overflow-y-auto">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-800/60 p-4 sm:p-5 rounded-2xl border border-slate-700/60 text-sm sm:text-base text-slate-200">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-bullhorn text-amber-400 text-lg sm:text-xl shrink-0"></i>
                    <span class="font-medium">Pilih sub menu di bawah untuk mengunduh dokumen resmi, membaca panduan, melihat denah lokasi, atau menghubungi panitia.</span>
                </div>
                <span class="text-xs sm:text-sm font-black text-amber-300 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-lg shrink-0 w-fit">{{ $infoMenus->count() }} Pilihan Sub Menu</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
                @forelse($infoMenus as $menu)
                    @php
                        $pal = $menu->theme_classes;
                        $hasFile = ($menu->action_type === 'file' && $menu->file_path);
                        $isLink = in_array($menu->action_type, ['link', 'whatsapp']) && $menu->url_link;
                    @endphp
                    <div class="bg-slate-800/70 hover:bg-slate-800/95 border-2 border-slate-700/70 {{ $pal['border_hover'] }} rounded-3xl p-5 sm:p-6 transition-all duration-200 flex flex-col justify-between group hover:shadow-xl {{ $pal['shadow_hover'] }}">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl {{ $pal['icon_bg'] }} {{ $pal['icon_text'] }} {{ $pal['icon_hover'] }} flex items-center justify-center text-xl sm:text-2xl transition">
                                    <i class="{{ $menu->icon ?: 'fa-solid fa-folder-open' }}"></i>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider {{ $pal['badge_bg'] }} {{ $pal['badge_text'] }} border {{ $pal['badge_border'] }}">
                                    {{ $menu->category_badge }}
                                </span>
                            </div>
                            <div>
                                <h4 class="font-black text-white text-base sm:text-lg {{ $pal['title_hover'] }} transition">
                                    {{ $menu->title }}
                                </h4>
                                <p class="text-sm text-slate-200 mt-2 leading-relaxed font-medium">
                                    {{ $menu->description }}
                                </p>
                                @if($menu->action_type === 'whatsapp' || str_contains(strtolower($menu->title), 'contact'))
                                    <div class="mt-3 flex items-center gap-1.5 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">🟢 PMR Mula</span>
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-blue-500/20 text-blue-300 border border-blue-500/30">🔵 PMR Madya</span>
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30">🟡 PMR Wira</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="pt-5 mt-4 border-t border-slate-700/60">
                            @if($hasFile)
                                <a href="{{ asset('storage/' . $menu->file_path) }}" target="_blank" class="w-full py-3 px-4 rounded-xl {{ $pal['btn_solid'] }} font-black text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow-md">
                                    <i class="fa-solid fa-file-arrow-down text-sm"></i>
                                    <span>{{ $menu->button_text ?: 'Unduh Berkas' }}</span>
                                </a>
                            @elseif($menu->action_type === 'whatsapp' || str_contains(strtolower($menu->title), 'contact'))
                                <button type="button" onclick="openContactPersonModal()" class="w-full py-3 px-4 rounded-xl {{ $pal['btn_solid'] }} font-black text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow-md cursor-pointer">
                                    <i class="fa-brands fa-whatsapp text-base"></i>
                                    <span>{{ ($menu->button_text && !in_array($menu->button_text, ['Hubungi Contact Person', 'Pilih Contact Person', 'Chat WhatsApp Panitia'])) ? $menu->button_text : 'Hubungi Panitia' }}</span>
                                </button>
                            @elseif($isLink)
                                <a href="{{ $menu->url_link }}" target="_blank" class="w-full py-3 px-4 rounded-xl {{ $pal['btn_bg'] }} {{ $pal['btn_text'] }} font-black text-xs sm:text-sm transition border {{ $pal['btn_border'] }} flex items-center justify-center gap-2 shadow-sm">
                                    @if(str_contains($menu->url_link, 'maps'))
                                        <i class="fa-solid fa-diamond-turn-right text-sm"></i>
                                    @elseif(str_contains($menu->url_link, 'instagram'))
                                        <i class="fa-brands fa-instagram text-sm"></i>
                                    @else
                                        <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                                    @endif
                                    <span>{{ $menu->button_text ?: 'Buka Tautan' }}</span>
                                </a>
                            @else
                                <button type="button" onclick="handleDocumentAction('{{ addslashes($menu->title) }}', 'notice')" class="w-full py-3 px-4 rounded-xl {{ $pal['btn_bg'] }} {{ $pal['btn_text'] }} font-black text-xs sm:text-sm transition border {{ $pal['btn_border'] }} flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                                    <i class="fa-solid fa-file-arrow-down text-sm"></i>
                                    <span>{{ $menu->button_text ?: 'Unduh Dokumen' }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400">
                        <i class="fa-solid fa-folder-open text-3xl mb-2"></i>
                        <p class="text-sm">Belum ada informasi lomba yang ditambahkan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-5 sm:p-6 md:p-7 bg-slate-950/95 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-300">
            <div class="flex items-center gap-2.5 text-center sm:text-left">
                <i class="fa-solid fa-circle-question text-amber-400 text-base sm:text-lg shrink-0"></i>
                <span class="font-medium">Membutuhkan surat resmi khusus atau konfirmasi berkas? Hubungi sekretariat panitia lomba.</span>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 justify-end">
                <button type="button" onclick="closeInfoModal()" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-slate-200 font-black px-7 py-3 rounded-xl transition border border-slate-700 cursor-pointer text-sm">
                    Tutup
                </button>
            </div>
        </div>

    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL POPUP: CONTACT PERSON RESMI (3 TINGKAT: MULA, MADYA, WIRA) -->
<!-- ============================================================ -->
<div id="modal-contact-person" class="fixed inset-0 hidden overflow-y-auto bg-slate-950/90 backdrop-blur-md transition-all duration-300 flex items-center justify-center p-3 sm:p-6" style="z-index: 99999;" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-4xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border-2 border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden transform transition-all text-white my-4 sm:my-8">
        
        <!-- Header -->
        <div class="p-6 sm:p-8 border-b border-slate-800 flex items-center justify-between gap-4 bg-slate-900/90 backdrop-blur sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-2xl sm:text-3xl shadow-xl shadow-emerald-950/50 shrink-0">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <span class="px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-headset"></i> Hotline Resmi Panitia
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black text-white mt-1.5 tracking-tight">Contact Person Lomba (3 Tingkat)</h3>
                </div>
            </div>
            <button type="button" onclick="closeContactPersonModal()" class="w-12 h-12 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition border border-slate-700 cursor-pointer shrink-0 shadow-md" title="Tutup (ESC)">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Body: 3 Contact Cards -->
        <div class="p-6 sm:p-8 md:p-10 space-y-6 max-h-[78vh] overflow-y-auto">
            <p class="text-sm sm:text-base text-slate-200 font-medium leading-relaxed">
                Silakan pilih narahubung panitia di bawah ini sesuai dengan tingkatan kontingen PMR sekolah Anda untuk berkonsultasi langsung via WhatsApp:
            </p>

            <div class="space-y-4 sm:space-y-5">
                @php
                    $contactItem = $infoMenus->first(function($m) {
                        return $m->action_type === 'whatsapp' || str_contains(strtolower($m->title), 'contact');
                    });
                    $contactsList = $contactItem ? $contactItem->contacts_list : \App\Models\CompetitionInfoMenu::defaultContacts();
                @endphp

                @foreach($contactsList as $ct)
                    @php
                        $lvl = $ct['level'] ?? 'Lomba';
                        $waUrl = \App\Models\CompetitionInfoMenu::formatWhatsAppUrl($ct['phone'] ?? '', $lvl, $ct['name'] ?? '');
                        
                        $cardTheme = match($lvl) {
                            'Mula' => [
                                'badge_bg' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
                                'title' => 'PMR MULA (Tingkat SD / MI)',
                                'border' => 'border-emerald-500/50 hover:border-emerald-400',
                                'dot' => 'bg-emerald-400',
                                'name_hover' => 'group-hover:text-emerald-300',
                                'btn_bg' => 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-950/60 border border-emerald-400/30',
                                'icon_color' => 'text-white',
                            ],
                            'Madya' => [
                                'badge_bg' => 'bg-blue-500/20 text-blue-300 border-blue-500/40',
                                'title' => 'PMR MADYA (Tingkat SMP / MTs)',
                                'border' => 'border-blue-500/50 hover:border-blue-400',
                                'dot' => 'bg-blue-400',
                                'name_hover' => 'group-hover:text-blue-300',
                                'btn_bg' => 'bg-blue-600 hover:bg-blue-500 text-white shadow-blue-950/60 border border-blue-400/30',
                                'icon_color' => 'text-white',
                            ],
                            default => [
                                'badge_bg' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
                                'title' => 'PMR WIRA (Tingkat SMA / SMK / MA)',
                                'border' => 'border-amber-500/50 hover:border-amber-400',
                                'dot' => 'bg-amber-400',
                                'name_hover' => 'group-hover:text-amber-300',
                                'btn_bg' => 'bg-amber-400 hover:bg-amber-300 text-slate-950 shadow-amber-950/60 border border-amber-300',
                                'icon_color' => 'text-slate-950',
                            ],
                        };
                    @endphp

                    <div class="p-6 sm:p-7 rounded-3xl bg-slate-800/85 border-2 {{ $cardTheme['border'] }} transition-all flex flex-col md:flex-row md:items-center justify-between gap-5 group hover:bg-slate-800 shadow-md">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5">
                                <span class="w-3 h-3 rounded-full {{ $cardTheme['dot'] }} animate-pulse shrink-0"></span>
                                <span class="px-3.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider {{ $cardTheme['badge_bg'] }} border">
                                    {{ $cardTheme['title'] }}
                                </span>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-white {{ $cardTheme['name_hover'] }} transition">
                                {{ $ct['name'] ?? 'Panitia ' . $lvl }}
                            </div>
                            <div class="text-sm sm:text-base text-slate-300 font-medium flex items-center gap-3 flex-wrap">
                                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-phone text-xs text-slate-400"></i><span class="font-bold text-white">{{ $ct['phone'] ?? '081383885600' }}</span></span>
                                @if(!empty($ct['role']))
                                    <span class="text-slate-500 hidden sm:inline">•</span>
                                    <span class="text-slate-300">{{ $ct['role'] }}</span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ $waUrl }}" target="_blank" class="px-6 py-4 rounded-2xl {{ $cardTheme['btn_bg'] }} font-black text-sm sm:text-base transition-all flex items-center justify-center gap-2.5 shadow-xl shrink-0 cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                            <i class="fa-brands fa-whatsapp text-xl sm:text-2xl {{ $cardTheme['icon_color'] }}"></i>
                            <span>Chat WA PMR {{ $lvl }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Footer -->
        <div class="p-5 sm:p-6 bg-slate-950/95 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs sm:text-sm text-slate-300">
            <span class="font-medium text-center sm:text-left">Panitia siap melayani pertanyaan seputar teknis perlombaan, verifikasi berkas, dan pendaftaran.</span>
            <button type="button" onclick="closeContactPersonModal()" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs sm:text-sm transition border border-slate-700 cursor-pointer shrink-0">
                Tutup
            </button>
        </div>

    </div>
</div>

<!-- Mini Notice Dialog for Pending Upload Documents -->
<div id="modal-doc-notice" class="fixed inset-0 hidden overflow-y-auto bg-slate-950/90 backdrop-blur-md transition-all duration-200 flex items-center justify-center p-4" style="z-index: 99999;">
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
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeInfoModal() {
        const modal = document.getElementById('modal-informasi-lomba');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function openContactPersonModal() {
        const modal = document.getElementById('modal-contact-person');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeContactPersonModal() {
        const modal = document.getElementById('modal-contact-person');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            const infoModal = document.getElementById('modal-informasi-lomba');
            if (!infoModal || infoModal.classList.contains('hidden')) {
                document.body.style.overflow = '';
            }
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
            noticeModal.style.display = 'flex';
        }
    }

    function closeDocNotice() {
        const noticeModal = document.getElementById('modal-doc-notice');
        if (noticeModal) {
            noticeModal.classList.add('hidden');
            noticeModal.style.display = 'none';
        }
    }

    // Close on backdrop click & ESC
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

        const contactModal = document.getElementById('modal-contact-person');
        if (contactModal) {
            contactModal.addEventListener('click', function(e) {
                if (e.target === contactModal) {
                    closeContactPersonModal();
                }
            });
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDocNotice();
                closeContactPersonModal();
                closeInfoModal();
            }
        });
    });
</script>
@endpush
@endsection
