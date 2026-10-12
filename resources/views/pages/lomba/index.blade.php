@extends('layouts.app')

@php
    $pageTitle = match($pageState ?? 'active') {
        'empty' => 'Agenda Lomba - PMR Wira SMAN 1 Ciawi',
        'teaser' => 'Save The Date: ' . ($event->title ?? 'Sua Bhakti Berkarya') . ' - PMR Wira SMAN 1 Ciawi',
        default => ($event->title ?? 'Lomba PMR - Sua Bhakti Berkarya') . ' - PMR Wira SMAN 1 Ciawi',
    };
@endphp

@section('title', $pageTitle)

@section('content')

@if(($pageState ?? 'active') === 'empty')
    <!-- =================================================================== -->
    <!-- KONDISI 3: BELUM ADA JADWAL LOMBA (OFF-SEASON / EMPTY STATE)         -->
    <!-- =================================================================== -->
    <section class="relative bg-slate-900 text-white pt-36 sm:pt-40 pb-16 sm:pb-24 overflow-hidden">
        <!-- Ambient Grid & Glow Background -->
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#ef4444_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-red-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <!-- Status Badge -->
                <div class="inline-flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase mb-5">
                    <i class="fa-solid fa-trophy"></i>
                    <span>AGENDA LOMBA PMR WIRA CIAWI</span>
                </div>

                <!-- Modern Vector Illustration: Gold Trophy & Red Calendar -->
                <div class="w-60 sm:w-72 mx-auto mb-6 relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/20 via-red-500/15 to-transparent rounded-full blur-2xl pointer-events-none"></div>
                    <svg class="w-full h-auto drop-shadow-2xl relative z-10" viewBox="0 0 320 185" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="trophyGold" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#FDE047" />
                                <stop offset="45%" stop-color="#F59E0B" />
                                <stop offset="100%" stop-color="#D97706" />
                            </linearGradient>
                            <linearGradient id="trophyBase" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#475569" />
                                <stop offset="100%" stop-color="#1E293B" />
                            </linearGradient>
                            <linearGradient id="calHeader" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#EF4444" />
                                <stop offset="100%" stop-color="#DC2626" />
                            </linearGradient>
                        </defs>

                        <!-- Soft Ground Shadow -->
                        <ellipse cx="160" cy="168" rx="100" ry="10" fill="#020617" opacity="0.6" />

                        <!-- CALENDAR (Right / Background) -->
                        <g transform="translate(142, 32)">
                            <!-- Calendar Shadow & Body -->
                            <rect x="0" y="0" width="116" height="126" rx="16" fill="#1E293B" stroke="#334155" stroke-width="2" />
                            <!-- Red PMR Header -->
                            <path d="M0 16C0 7.16344 7.16344 0 16 0H100C108.837 0 116 7.16344 116 16V36H0V16Z" fill="url(#calHeader)" />
                            
                            <!-- Spiral Rings -->
                            <rect x="20" y="-6" width="6" height="13" rx="3" fill="#F8FAFC" />
                            <rect x="55" y="-6" width="6" height="13" rx="3" fill="#F8FAFC" />
                            <rect x="90" y="-6" width="6" height="13" rx="3" fill="#F8FAFC" />

                            <!-- Calendar Date Dots -->
                            <!-- Header Dots -->
                            <circle cx="23" cy="50" r="3" fill="#64748B" />
                            <circle cx="46" cy="50" r="3" fill="#64748B" />
                            <circle cx="70" cy="50" r="3" fill="#64748B" />
                            <circle cx="93" cy="50" r="3" fill="#64748B" />

                            <!-- Row 1 -->
                            <circle cx="23" cy="68" r="4" fill="#475569" />
                            <circle cx="46" cy="68" r="4" fill="#475569" />
                            <circle cx="70" cy="68" r="4" fill="#475569" />
                            <circle cx="93" cy="68" r="4" fill="#475569" />

                            <!-- Row 2 (Highlighted Date) -->
                            <circle cx="23" cy="86" r="4" fill="#475569" />
                            <circle cx="46" cy="86" r="4" fill="#475569" />
                            <rect x="62" y="78" width="16" height="16" rx="4" fill="#EF4444" />
                            <circle cx="70" cy="86" r="3" fill="#FFFFFF" />
                            <circle cx="93" cy="86" r="4" fill="#475569" />

                            <!-- Row 3 -->
                            <circle cx="23" cy="104" r="4" fill="#334155" />
                            <circle cx="46" cy="104" r="4" fill="#334155" />
                            <circle cx="70" cy="104" r="4" fill="#334155" />
                            <circle cx="93" cy="104" r="4" fill="#334155" />
                        </g>

                        <!-- GOLDEN TROPHY (Left / Foreground) -->
                        <g transform="translate(58, 22)">
                            <!-- Trophy Left Handle -->
                            <path d="M22 45 C4 45, 4 82, 32 87" stroke="url(#trophyGold)" stroke-width="7" stroke-linecap="round" fill="none" />
                            <!-- Trophy Right Handle -->
                            <path d="M78 45 C96 45, 96 82, 68 87" stroke="url(#trophyGold)" stroke-width="7" stroke-linecap="round" fill="none" />

                            <!-- Trophy Main Cup -->
                            <path d="M22 30 H78 V62 C78 78, 64 93, 50 93 C36 93, 22 78, 22 62 Z" fill="url(#trophyGold)" />
                            <!-- Trophy Inner Rim -->
                            <ellipse cx="50" cy="30" rx="28" ry="7" fill="#FDE047" />

                            <!-- Star Embossed on Cup -->
                            <path d="M50 49 L52.5 55.5 L59 56 L54 60.5 L55.5 67 L50 63.5 L44.5 67 L46 60.5 L41 56 L47.5 55.5 Z" fill="#FFFFFF" opacity="0.9" />

                            <!-- Stem -->
                            <rect x="44" y="93" width="12" height="23" rx="2" fill="url(#trophyGold)" />
                            <ellipse cx="50" cy="106" rx="9" ry="3" fill="#FDE047" />

                            <!-- Base / Pedestal -->
                            <path d="M30 116 H70 L76 138 H24 Z" fill="url(#trophyBase)" stroke="#475569" stroke-width="1.5" />
                            <rect x="18" y="138" width="64" height="8" rx="3" fill="#0F172A" stroke="#334155" stroke-width="1" />
                            <rect x="34" y="122" width="32" height="10" rx="2" fill="url(#trophyGold)" />
                        </g>

                        <!-- Sparkles -->
                        <path d="M272 25 L274 31 L280 33 L274 35 L272 41 L270 35 L264 33 L270 31 Z" fill="#FDE047" />
                        <path d="M42 50 L43.5 54.5 L48 56 L43.5 57.5 L42 62 L40.5 57.5 L36 56 L40.5 54.5 Z" fill="#F87171" opacity="0.8" />
                        <path d="M148 16 L149.5 20 L154 21.5 L149.5 23 L148 27 L146.5 23 L142 21.5 L146.5 20 Z" fill="#38BDF8" opacity="0.8" />
                        <circle cx="288" cy="72" r="2.5" fill="#FDE047" opacity="0.6" />
                        <circle cx="34" cy="105" r="2" fill="#38BDF8" opacity="0.6" />
                    </svg>
                </div>

                <!-- Main Heading -->
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-black tracking-tight text-white mb-4 leading-tight">
                    Belum Ada Jadwal Lomba Saat Ini
                </h1>

                <!-- Subtitle / Explanation -->
                <p class="text-slate-300 text-sm sm:text-base md:text-lg mb-8 leading-relaxed max-w-2xl mx-auto">
                    Saat ini panitia belum membuka agenda kompetisi kepalangmerahan baru. Persiapan dan penyusunan ajang perlombaan berikutnya sedang dirancang oleh pengurus PMR Wira SMAN 1 Ciawi.
                </p>

                <!-- Action Buttons (Ultra Mobile Friendly) -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 max-w-md sm:max-w-none mx-auto">
                    <a href="{{ route('home') }}" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-7 py-3.5 rounded-xl font-bold shadow-lg shadow-red-900/40 transition flex items-center justify-center gap-2 text-sm sm:text-base">
                        <i class="fa-solid fa-house"></i>
                        <span>Kembali ke Beranda</span>
                    </a>

                    <button type="button" onclick="openInfoModal()" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 hover:text-white px-6 py-3.5 rounded-xl font-bold transition flex items-center justify-center gap-2 text-sm sm:text-base cursor-pointer">
                        <i class="fa-solid fa-folder-open"></i>
                        <span>Arsip Informasi</span>
                    </button>
                </div>
            </div>

            <!-- Summary Stats Badges (Mobile: 2 cols, Desktop: 4 cols) -->
            <div class="mt-12 max-w-4xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-slate-800/70 border border-slate-700/70 p-4 rounded-2xl text-center backdrop-blur">
                    <div class="text-amber-400 text-xl sm:text-2xl mb-1"><i class="fa-solid fa-trophy"></i></div>
                    <div class="text-xs sm:text-sm font-bold text-white">Juara Umum</div>
                    <div class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Piala Bergilir PMI</div>
                </div>

                <div class="bg-slate-800/70 border border-slate-700/70 p-4 rounded-2xl text-center backdrop-blur">
                    <div class="text-emerald-400 text-xl sm:text-2xl mb-1"><i class="fa-solid fa-users"></i></div>
                    <div class="text-xs sm:text-sm font-bold text-white">450+ Relawan</div>
                    <div class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Partisipan Terdahulu</div>
                </div>

                <div class="bg-slate-800/70 border border-slate-700/70 p-4 rounded-2xl text-center backdrop-blur">
                    <div class="text-blue-400 text-xl sm:text-2xl mb-1"><i class="fa-solid fa-graduation-cap"></i></div>
                    <div class="text-xs sm:text-sm font-bold text-white">3 Tingkatan</div>
                    <div class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Mula, Madya, Wira</div>
                </div>

                <div class="bg-slate-800/70 border border-slate-700/70 p-4 rounded-2xl text-center backdrop-blur">
                    <div class="text-rose-400 text-xl sm:text-2xl mb-1"><i class="fa-solid fa-heart-pulse"></i></div>
                    <div class="text-xs sm:text-sm font-bold text-white">7 Prinsip Dasar</div>
                    <div class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Gerakan Palang Merah</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Kilas Balik Lomba Sebelumnya (Hall of Fame & Dokumentasi) -->
    <section class="py-14 sm:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14">
                <span class="text-xs font-black uppercase tracking-wider text-red-600 bg-red-100 border border-red-200 px-3 py-1 rounded-full inline-block mb-3">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Dokumentasi & Hall of Fame
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kilas Balik Lomba Sebelumnya</h2>
                <p class="text-slate-600 text-xs sm:text-sm mt-2">Potret sportivitas dan semangat kemanusiaan relawan muda di ajang Sua Bhakti Berkarya PMR Wira SMAN 1 Ciawi.</p>
            </div>

            <!-- Gallery Grid (Mobile Friendly 1 col on mobile, 2 on tablet, 3 on desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @if(isset($pastGalleries) && $pastGalleries->count() > 0)
                    @foreach($pastGalleries as $gal)
                        <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col group">
                            <div class="relative h-48 sm:h-52 bg-slate-900 overflow-hidden">
                                <img src="{{ asset('storage/' . $gal->image_path) }}" alt="{{ $gal->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                                <span class="absolute top-3 left-3 bg-red-600/90 text-white text-[10px] font-black uppercase px-2.5 py-1 rounded-lg backdrop-blur">
                                    {{ $gal->category ?: 'Dokumentasi Lomba' }}
                                </span>
                            </div>
                            <div class="p-5 flex-grow flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-red-600 transition">{{ $gal->title }}</h4>
                                    <p class="text-xs text-slate-500 mt-1">Dokumentasi resmi ajang Sua Bhakti Berkarya PMR Wira SMAN 1 Ciawi.</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback Themed Highlights Cards -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between group hover:border-red-400 transition">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl font-bold">
                                <i class="fa-solid fa-kit-medical"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-red-600 bg-red-50 border border-red-200 px-2.5 py-0.5 rounded-full inline-block">Mata Lomba Utama</span>
                            <h4 class="text-base sm:text-lg font-black text-slate-900">Pertolongan Pertama (PP)</h4>
                            <p class="text-xs text-slate-600 leading-relaxed">Simulasi penanganan kegawatdaruratan medis, patah tulang, luka bakar, dan evakuasi korban secara cepat, tepat, dan manusiawi.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between group hover:border-blue-400 transition">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                                <i class="fa-solid fa-person-walking-dashed-line-arrow-right"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-full inline-block">Ketangkasan Fisik</span>
                            <h4 class="text-base sm:text-lg font-black text-slate-900">Pasang Bongkar Tandu Darurat</h4>
                            <p class="text-xs text-slate-600 leading-relaxed">Uji kecepatan, kerapihan simpul, dan kekokohan tandu darurat bambu dalam situasi bencana dan darurat lapangan.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between group hover:border-amber-400 transition">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                                <i class="fa-solid fa-trophy"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full inline-block">Prestasi Bergengsi</span>
                            <h4 class="text-base sm:text-lg font-black text-slate-900">Piala Bergilir & Medali Kejuaraan</h4>
                            <p class="text-xs text-slate-600 leading-relaxed">Memperebutkan Piala Bergilir Kepala Sekolah dan puluhan piala tetap untuk Juara Utama 1, 2, 3 di tiap mata lomba.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Collaboration / Contact Box -->
            <div class="mt-12 bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-8 text-white border border-slate-700 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center md:text-left max-w-xl">
                    <div class="inline-flex items-center gap-2 text-red-400 text-xs font-black uppercase tracking-wider">
                        <i class="fa-solid fa-handshake"></i> Kerjasama & Undangan Kegiatan
                    </div>
                    <h3 class="text-lg sm:text-2xl font-black text-white">Ingin Mengundang atau Mengadakan Latihan Bersama?</h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        PMR Wira SMAN 1 Ciawi terbuka untuk kerjasama antar unit PMR sekolah, latihan gabungan (LATGAB), maupun kegiatan kemanusiaan lainnya.
                    </p>
                </div>
                <div class="shrink-0 w-full md:w-auto flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="openContactPersonModal()" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm px-6 py-3.5 rounded-xl transition flex items-center justify-center gap-2 shadow-md cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                        <span>Hubungi Narahubung Panitia</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

@elseif(($pageState ?? 'active') === 'teaser')
    <!-- =================================================================== -->
    <!-- KONDISI 2: EVENT AKAN DATANG / TEASER (MASIH BEBERAPA BULAN KE DEPAN) -->
    <!-- =================================================================== -->
    <section class="relative bg-slate-900 text-white pt-28 sm:pt-32 pb-16 sm:pb-24 overflow-hidden">
        <!-- Ambient Background -->
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#ef4444_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-red-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl lg:max-w-5xl mx-auto">
                <!-- Save The Date Badge -->
                <div class="inline-flex items-center gap-2 bg-amber-500/15 border border-amber-500/40 text-amber-300 px-4 py-1.5 rounded-full text-xs font-extrabold tracking-wider uppercase mb-5 shadow-sm">
                    <i class="fa-solid fa-bell text-amber-400 animate-bounce"></i>
                    <span>SAVE THE DATE: AKAN SEGERA HADIR</span>
                </div>

                @php
                    $titleRaw = $event->title ?? 'SUA BHAKTI BERKARYA V TAHUN 2027';
                    if (preg_match('/^(.*?)\s+(TAHUN\s+\d+.*)$/i', $titleRaw, $m)) {
                        $eventMainTitle = trim($m[1]);
                        $eventYearTitle = trim($m[2]);
                    } else {
                        $eventMainTitle = $titleRaw;
                        $eventYearTitle = '';
                    }
                    $teaserTarget = $event ? $event->getCountdownTarget() : null;
                @endphp

                <!-- Title: 1 line on desktop, clean wrap on mobile -->
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-black tracking-tight mb-4 leading-tight">
                    @if($eventYearTitle)
                        <span class="inline-block">{{ $eventMainTitle }}</span>
                        <span class="inline-block whitespace-nowrap"> {{ $eventYearTitle }}</span>
                    @else
                        {{ $titleRaw }}
                    @endif
                </h1>

                <p class="text-slate-300 text-sm sm:text-base md:text-lg mb-8 leading-relaxed max-w-2xl mx-auto">
                    {{ $event->description ?? 'Ajang kompetisi kepalangmerahan bergengsi tingkat Mula (SD), Madya (SMP), dan Wira (SMA/SMK/MA) se-Jabodetabek dan sekitarnya akan segera dibuka. Persiapkan kontingen terbaik sekolah Anda!' }}
                </p>

                <!-- Teaser Long-Term Countdown Card (Mobile-Optimized Grid) -->
                <div class="max-w-3xl mx-auto bg-gradient-to-br from-slate-800/95 via-slate-800/85 to-slate-900/95 border-2 border-amber-500/40 rounded-3xl p-5 sm:p-7 shadow-2xl backdrop-blur relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-40 h-40 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="relative z-10 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>Estimasi Menuju Pembukaan Pendaftaran</span>
                        </div>

                        <h3 class="text-lg sm:text-2xl font-black text-white tracking-tight">
                            Pendaftaran Resmi Segera Dibuka
                        </h3>

                        <p class="text-xs sm:text-sm text-slate-300 max-w-lg mx-auto">
                            @if($event && $event->wave1_start_date)
                                Pendaftaran Gelombang 1 dijadwalkan buka pada: <strong class="text-amber-300">{{ $event->wave1_start_date->translatedFormat('d F Y, H:i') }} WIB</strong>.
                            @elseif($event && $event->start_date)
                                Pelaksanaan lomba dijadwalkan pada: <strong class="text-amber-300">{{ $event->start_date->translatedFormat('d F Y') }}</strong>.
                            @else
                                Pantau terus hitung mundur di bawah ini untuk menjadi kontingen pertama yang mendaftar.
                            @endif
                        </p>

                        <!-- Digital Clock 4 Grid (Never Overflows on Mobile 320px-390px) -->
                        @if($teaserTarget)
                            <div class="grid grid-cols-4 gap-2 sm:gap-3 max-w-sm sm:max-w-md mx-auto pt-2" id="teaser-countdown-clock" data-target="{{ $teaserTarget->toIso8601String() }}">
                                <div class="bg-slate-900/90 border border-slate-700/80 p-2.5 sm:p-3 rounded-2xl text-center shadow-inner">
                                    <span class="font-mono text-xl sm:text-3xl font-black text-amber-400 block" id="tsr-days">00</span>
                                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hari</span>
                                </div>
                                <div class="bg-slate-900/90 border border-slate-700/80 p-2.5 sm:p-3 rounded-2xl text-center shadow-inner">
                                    <span class="font-mono text-xl sm:text-3xl font-black text-amber-400 block" id="tsr-hours">00</span>
                                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jam</span>
                                </div>
                                <div class="bg-slate-900/90 border border-slate-700/80 p-2.5 sm:p-3 rounded-2xl text-center shadow-inner">
                                    <span class="font-mono text-xl sm:text-3xl font-black text-amber-400 block" id="tsr-minutes">00</span>
                                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menit</span>
                                </div>
                                <div class="bg-slate-900/90 border border-slate-700/80 p-2.5 sm:p-3 rounded-2xl text-center shadow-inner">
                                    <span class="font-mono text-xl sm:text-3xl font-black text-amber-400 block" id="tsr-seconds">00</span>
                                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Detik</span>
                                </div>
                            </div>
                        @endif

                        <!-- Teaser Action Buttons (Mobile: full width stack, Desktop: flex row) -->
                        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                            @if($event && $event->handbook_file)
                                <a href="{{ asset('storage/' . $event->handbook_file) }}" target="_blank" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-7 py-3.5 rounded-xl font-bold shadow-lg shadow-red-900/40 transition flex items-center justify-center gap-2 text-xs sm:text-sm">
                                    <i class="fa-solid fa-file-pdf"></i>
                                    <span>Unduh Surat Pemberitahuan</span>
                                </a>
                            @else
                                <button type="button" onclick="handleDocumentAction('Surat Pemberitahuan Lomba', 'notice')" class="w-full sm:w-auto bg-red-600/90 hover:bg-red-600 text-white px-7 py-3.5 rounded-xl font-bold shadow-lg shadow-red-900/30 transition flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer">
                                    <i class="fa-solid fa-file-lines"></i>
                                    <span>Unduh Surat Pemberitahuan</span>
                                </button>
                            @endif

                            <button type="button" onclick="openInfoModal()" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white px-6 py-3.5 rounded-xl font-bold transition flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer">
                                <i class="fa-solid fa-folder-open"></i>
                                <span>Pusat Informasi</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Metadata Cards (Pelaksanaan, Lokasi, Tingkatan) -->
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl mx-auto text-left">
                    <div class="bg-slate-800/80 border border-slate-700/80 p-4 rounded-2xl flex items-center gap-3 backdrop-blur">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Rencana Pelaksanaan</div>
                            <div class="text-xs sm:text-sm font-bold text-white">
                                @if($event && $event->start_date)
                                    {{ $event->start_date->translatedFormat('d F Y') }}
                                @else
                                    Sesuai Jadwal Resmi
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-800/80 border border-slate-700/80 p-4 rounded-2xl flex items-center gap-3 backdrop-blur">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Lokasi Kegiatan</div>
                            <div class="text-xs sm:text-sm font-bold text-white truncate max-w-[200px]">
                                {{ $event->location ?? 'Kampus SMAN 1 Ciawi Bogor' }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-800/80 border border-slate-700/80 p-4 rounded-2xl flex items-center gap-3 backdrop-blur">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Sasaran Peserta</div>
                            <div class="text-xs sm:text-sm font-bold text-white">Mula, Madya, Wira</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Bocoran Cabang Lomba yang Akan Dipertandingkan -->
    <section class="py-14 sm:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14">
                <span class="text-xs font-black uppercase tracking-wider text-amber-800 bg-amber-100 border border-amber-200 px-3 py-1 rounded-full inline-block mb-3">
                    <i class="fa-solid fa-layer-group mr-1"></i> Preview Kategori
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kategori yang Akan Dilombakan</h2>
                <p class="text-slate-600 text-xs sm:text-sm mt-2">Daftar cabang mata lomba yang dipersiapkan untuk tiap tingkatan kontingen sekolah.</p>
            </div>

            <!-- 3 Tingkatan Cards (Mula, Madya, Wira) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- MULA (SD / MI) -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border-2 border-emerald-500/40 flex flex-col relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-green-600"></div>
                    <div class="flex items-center justify-between pb-4 border-b border-emerald-100/70 mb-5">
                        <div>
                            <span class="bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Tingkat SD / MI</span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">PMR Mula</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-child-reaching"></i>
                        </div>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60 w-fit mb-4">
                        <i class="fa-solid fa-clock text-emerald-600"></i>
                        <span>{{ $categoriesByLevel->get('Mula', collect())->count() ?: '7' }} Cabang Lomba Direncanakan</span>
                    </div>

                    <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                        @forelse($categoriesByLevel->get('Mula', []) as $cat)
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                                <span class="text-slate-700"><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') <span class="text-xs text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/50">({{ $cat->gender_category }})</span> @endif</span>
                            </li>
                        @empty
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i><span>Pertolongan Pertama (Putra/Putri)</span></li>
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i><span>Ketangkasan Tandu Reguler</span></li>
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i><span>Ketangkasan Cuci Tangan</span></li>
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i><span>Lomba Mewarnai Kepalangmerahan</span></li>
                        @endforelse
                    </ul>

                    <button type="button" onclick="openContactPersonModal()" class="w-full text-center py-3 rounded-xl font-extrabold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                        <span>Konsultasi Materi PMR Mula</span>
                    </button>
                </div>

                <!-- MADYA (SMP / MTs) -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border-2 border-blue-500/40 flex flex-col relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
                    <div class="flex items-center justify-between pb-4 border-b border-blue-100/70 mb-5">
                        <div>
                            <span class="bg-blue-100 text-blue-800 border border-blue-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Tingkat SMP / MTs</span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">PMR Madya</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-user-group"></i>
                        </div>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200/60 w-fit mb-4">
                        <i class="fa-solid fa-clock text-blue-600"></i>
                        <span>{{ $categoriesByLevel->get('Madya', collect())->count() ?: '8' }} Cabang Lomba Direncanakan</span>
                    </div>

                    <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                        @forelse($categoriesByLevel->get('Madya', []) as $cat)
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-blue-500 mt-1 shrink-0"></i>
                                <span class="text-slate-700"><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') <span class="text-xs text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200/50">({{ $cat->gender_category }})</span> @endif</span>
                            </li>
                        @empty
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-blue-500 mt-1"></i><span>Pertolongan Pertama (Putra/Putri)</span></li>
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-blue-500 mt-1"></i><span>Ketangkasan Tandu Reguler (Putra/Putri)</span></li>
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-blue-500 mt-1"></i><span>Cepat Tepat Kepalangmerahan</span></li>
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-blue-500 mt-1"></i><span>Olimpiade & Mading Kreasi</span></li>
                        @endforelse
                    </ul>

                    <button type="button" onclick="openContactPersonModal()" class="w-full text-center py-3 rounded-xl font-extrabold bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 transition flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-blue-600"></i>
                        <span>Konsultasi Materi PMR Madya</span>
                    </button>
                </div>

                <!-- WIRA (SMA / SMK / MA) -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border-2 border-amber-500/40 flex flex-col relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500"></div>
                    <div class="flex items-center justify-between pb-4 border-b border-amber-100/70 mb-5">
                        <div>
                            <span class="bg-amber-100 text-amber-900 border border-amber-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Tingkat SMA / SMK / MA</span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">PMR Wira</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-900 border border-amber-200/60 w-fit mb-4">
                        <i class="fa-solid fa-clock text-amber-600"></i>
                        <span>{{ $categoriesByLevel->get('Wira', collect())->count() ?: '8' }} Cabang Lomba Direncanakan</span>
                    </div>

                    <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                        @forelse($categoriesByLevel->get('Wira', []) as $cat)
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-amber-500 mt-1 shrink-0"></i>
                                <span class="text-slate-700"><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') <span class="text-xs text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200/50">({{ $cat->gender_category }})</span> @endif</span>
                            </li>
                        @empty
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-amber-500 mt-1"></i><span>Pertolongan Pertama (Putra/Putri)</span></li>
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-amber-500 mt-1"></i><span>Ketangkasan Tandu Reguler (Putra/Putri)</span></li>
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-amber-500 mt-1"></i><span>Cepat Tepat & Olimpiade</span></li>
                            <li class="flex items-start gap-2.5"><i class="fa-solid fa-circle-check text-amber-500 mt-1"></i><span>Mading Kreasi & Donor Darah Siswa</span></li>
                        @endforelse
                    </ul>

                    <button type="button" onclick="openContactPersonModal()" class="w-full text-center py-3 rounded-xl font-extrabold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 transition flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-amber-600"></i>
                        <span>Konsultasi Materi PMR Wira</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

@else
    <!-- =================================================================== -->
    <!-- KONDISI 1: EVENT AKTIF (PENDAFTARAN SEDANG / SEGERA DIBUKA 1-2 GELOMBANG) -->
    <!-- =================================================================== -->
    <!-- Hero Section -->
    <section class="relative bg-slate-900 text-white pt-28 sm:pt-32 pb-16 sm:pb-20 overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#ef4444_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl lg:max-w-5xl mx-auto">
                <div class="inline-flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase mb-6">
                    <i class="fa-solid fa-trophy"></i> {{ $event->theme ?: 'AJANG PRESTASI RELAWAN MUDA PMR WIRA CIAWI' }}
                </div>
                @php
                    $titleRaw = $event->title ?? 'SUA BHAKTI BERKARYA IV TAHUN 2026';
                    if (preg_match('/^(.*?)\s+(TAHUN\s+\d+.*)$/i', $titleRaw, $m)) {
                        $eventMainTitle = trim($m[1]);
                        $eventYearTitle = trim($m[2]);
                    } else {
                        $eventMainTitle = $titleRaw;
                        $eventYearTitle = '';
                    }
                @endphp
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-black tracking-tight mb-4 leading-tight">
                    @if($eventYearTitle)
                        <span class="inline-block">{{ $eventMainTitle }}</span>
                        <span class="inline-block whitespace-nowrap"> {{ $eventYearTitle }}</span>
                    @else
                        {{ $titleRaw }}
                    @endif
                </h1>
                <p class="text-slate-300 text-sm sm:text-base md:text-lg mb-8 leading-relaxed max-w-2xl mx-auto">
                    {{ $event->description ?? 'Ajang kompetisi kepalangmerahan bergengsi tingkat Mula (SD), Madya (SMP), dan Wira (SMA/SMK/MA) se-Jabodetabek dan sekitarnya.' }}
                </p>

                <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-3 sm:gap-4 max-w-md sm:max-w-none mx-auto">
                    @if($event && $event->is_registration_open)
                        <a href="{{ route('lomba.register') }}" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3.5 rounded-xl font-bold shadow-lg shadow-red-900/40 transition flex items-center justify-center gap-2 text-sm sm:text-base">
                            <i class="fa-solid fa-file-pen"></i> <span>Daftar Sekarang</span>
                        </a>
                    @endif
                    <a href="{{ route('lomba.status') }}" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white px-7 py-3.5 rounded-xl font-bold transition flex items-center justify-center gap-2 text-sm sm:text-base">
                        <i class="fa-solid fa-magnifying-glass"></i> <span>Cek Status Pendaftaran</span>
                    </a>
                    <a href="{{ route('lomba.scoreboard') }}" class="w-full sm:w-auto bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 px-7 py-3.5 rounded-xl font-bold transition flex items-center justify-center gap-2 text-sm sm:text-base">
                        <i class="fa-solid fa-square-poll-vertical"></i> <span>Live Klasemen Juara</span>
                    </a>
                    @if($event && $event->handbook_file)
                        <a href="{{ asset('storage/' . $event->handbook_file) }}" target="_blank" class="w-full sm:w-auto bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/40 text-indigo-300 px-7 py-3.5 rounded-xl font-bold transition flex items-center justify-center gap-2 text-sm sm:text-base">
                            <i class="fa-solid fa-file-pdf"></i> <span>Unduh Juklak Juknis</span>
                        </a>
                    @endif
                </div>
            </div>

            @php
                $activeWave = $event ? $event->getActiveWave() : 1;
                $activeWaveLabel = $event ? $event->getActiveWaveLabel() : 'Gelombang 1';
                $countdownTarget = $event ? $event->getCountdownTarget() : null;
                $isWavesEnabled = $event && $event->is_waves_enabled;
            @endphp

            @if($event && ($isWavesEnabled || $event->wave1_end_date))
                <!-- Countdown & Wave Schedule Card -->
                <div class="mt-10 sm:mt-12 max-w-4xl mx-auto bg-gradient-to-br from-slate-800/95 via-slate-800/80 to-slate-900/95 border-2 border-red-500/40 rounded-3xl p-5 sm:p-7 shadow-2xl backdrop-blur relative overflow-hidden">
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                        <!-- Status & Information -->
                        <div class="text-center md:text-left space-y-2 max-w-md">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $activeWave == 1 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : ($activeWave == 2 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($activeWave === 'upcoming' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/40' : 'bg-slate-700 text-slate-300')) }}">
                                @if($activeWave == 1)
                                    <i class="fa-solid fa-fire text-amber-400 animate-pulse"></i>
                                    <span>Sedang Dibuka: {{ $event->wave1_name ?: 'Gelombang 1' }}</span>
                                @elseif($activeWave == 2)
                                    <i class="fa-solid fa-bolt-lightning text-emerald-400 animate-pulse"></i>
                                    <span>Sedang Dibuka: {{ $event->wave2_name ?: 'Gelombang 2' }}</span>
                                @elseif($activeWave === 'upcoming')
                                    <i class="fa-solid fa-clock text-blue-400"></i>
                                    <span>Segera Dibuka: {{ $event->wave1_name ?: 'Gelombang 1' }}</span>
                                @else
                                    <i class="fa-solid fa-lock text-slate-400"></i>
                                    <span>Pendaftaran Telah Ditutup</span>
                                @endif
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                @if($activeWave == 1)
                                    Hitung Mundur {{ $event->wave1_name ?: 'Gelombang 1' }}
                                @elseif($activeWave == 2)
                                    Hitung Mundur Penutupan Pendaftaran
                                @elseif($activeWave === 'upcoming')
                                    Menuju Pembukaan Pendaftaran
                                @else
                                    Pendaftaran Telah Selesai
                                @endif
                            </h3>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                @if($activeWave == 1)
                                    Daftar sekarang dengan tarif Early Bird! Begitu waktu habis, sistem otomatis beralih ke tarif {{ $event->wave2_name ?: 'Gelombang 2' }}.
                                @elseif($activeWave == 2)
                                    Periode pendaftaran {{ $event->wave2_name ?: 'Gelombang 2' }} sedang dibuka. Segera lengkapi formulir pendaftaran sebelum batas akhir.
                                @elseif($activeWave === 'upcoming')
                                    Pendaftaran dibuka resmi pada {{ $event->wave1_start_date ? $event->wave1_start_date->translatedFormat('d F Y, H:i') : '-' }} WIB.
                                @else
                                    Periode pendaftaran lomba telah berakhir. Terima kasih atas antusiasme seluruh kontingen.
                                @endif
                            </p>
                        </div>

                        <!-- Countdown Digital Clock (Mobile-Friendly 4 Cols) -->
                        @if($countdownTarget && in_array($activeWave, [1, 2, 'upcoming']))
                            <div class="grid grid-cols-4 gap-2 sm:gap-3 text-center max-w-xs sm:max-w-none" id="index-countdown-clock" data-target="{{ $countdownTarget->toIso8601String() }}">
                                <div class="bg-slate-900/90 border border-slate-700/80 px-2.5 sm:px-4 py-2 sm:py-3 rounded-2xl min-w-[56px] sm:min-w-[72px] shadow-inner">
                                    <span class="font-mono text-xl sm:text-3xl font-black text-yellow-400" id="idx-days">00</span>
                                    <span class="block text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Hari</span>
                                </div>
                                <div class="bg-slate-900/90 border border-slate-700/80 px-2.5 sm:px-4 py-2 sm:py-3 rounded-2xl min-w-[56px] sm:min-w-[72px] shadow-inner">
                                    <span class="font-mono text-xl sm:text-3xl font-black text-yellow-400" id="idx-hours">00</span>
                                    <span class="block text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Jam</span>
                                </div>
                                <div class="bg-slate-900/90 border border-slate-700/80 px-2.5 sm:px-4 py-2 sm:py-3 rounded-2xl min-w-[56px] sm:min-w-[72px] shadow-inner">
                                    <span class="font-mono text-xl sm:text-3xl font-black text-yellow-400" id="idx-minutes">00</span>
                                    <span class="block text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Menit</span>
                                </div>
                                <div class="bg-slate-900/90 border border-slate-700/80 px-2.5 sm:px-4 py-2 sm:py-3 rounded-2xl min-w-[56px] sm:min-w-[72px] shadow-inner">
                                    <span class="font-mono text-xl sm:text-3xl font-black text-yellow-400" id="idx-seconds">00</span>
                                    <span class="block text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Detik</span>
                                </div>
                            </div>
                        @else
                            <div class="bg-slate-900/70 border border-slate-700 px-5 py-3 rounded-2xl text-center">
                                <span class="text-sm font-bold text-slate-300">Periode Terjadwal</span>
                            </div>
                        @endif
                    </div>

                    <!-- Wave 1 & Wave 2 Timeline Badges -->
                    <div class="mt-5 pt-4 border-t border-slate-700/60 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="flex items-center justify-between p-2.5 rounded-xl {{ $activeWave == 1 ? 'bg-amber-500/15 border border-amber-500/30 text-amber-200' : 'bg-slate-900/40 border border-slate-800 text-slate-400' }}">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-flag-checkered {{ $activeWave == 1 ? 'text-amber-400' : 'text-slate-500' }}"></i>
                                <span class="font-bold">{{ $event->wave1_name ?: 'Gelombang 1' }}</span>
                                @if($activeWave == 1)
                                    <span class="text-[9px] bg-amber-500 text-slate-950 font-black px-1.5 py-0.2 rounded-full uppercase">Aktif</span>
                                @endif
                            </div>
                            <span class="font-mono text-[11px]">
                                @if($event->wave1_start_date && $event->wave1_end_date)
                                    {{ $event->wave1_start_date->translatedFormat('d M') }} - {{ $event->wave1_end_date->translatedFormat('d M Y') }}
                                @elseif($event->wave1_end_date)
                                    s/d {{ $event->wave1_end_date->translatedFormat('d M Y') }}
                                @else
                                    Sesuai Jadwal
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-2.5 rounded-xl {{ $activeWave == 2 ? 'bg-emerald-500/15 border border-emerald-500/30 text-emerald-200' : 'bg-slate-900/40 border border-slate-800 text-slate-400' }}">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-flag-checkered {{ $activeWave == 2 ? 'text-emerald-400' : 'text-slate-500' }}"></i>
                                <span class="font-bold">{{ $event->wave2_name ?: 'Gelombang 2' }}</span>
                                @if($activeWave == 2)
                                    <span class="text-[9px] bg-emerald-500 text-slate-950 font-black px-1.5 py-0.2 rounded-full uppercase">Aktif</span>
                                @endif
                            </div>
                            <span class="font-mono text-[11px]">
                                @if($event->wave2_start_date && $event->wave2_end_date)
                                    {{ $event->wave2_start_date->translatedFormat('d M') }} - {{ $event->wave2_end_date->translatedFormat('d M Y') }}
                                @elseif($event->wave2_end_date)
                                    s/d {{ $event->wave2_end_date->translatedFormat('d M Y') }}
                                @else
                                    Sesuai Jadwal
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Info Banner -->
            <div class="mt-10 sm:mt-12 grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 max-w-4xl mx-auto">
                <div class="bg-slate-800/80 border border-slate-700/80 backdrop-blur p-4 sm:p-5 rounded-2xl flex items-center gap-4">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center text-lg sm:text-xl shrink-0">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 font-semibold">Pelaksanaan</div>
                        <div class="text-xs sm:text-sm font-bold text-white">
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

                <div class="bg-slate-800/80 border border-slate-700/80 backdrop-blur p-4 sm:p-5 rounded-2xl flex items-center gap-4">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg sm:text-xl shrink-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 font-semibold">Lokasi</div>
                        <div class="text-xs sm:text-sm font-bold text-white">{{ $event->location ?? 'Kampus SMAN 1 Ciawi Bogor' }}</div>
                    </div>
                </div>

                <!-- Pusat Informasi Button -->
                <button type="button" onclick="openInfoModal()" class="text-left bg-gradient-to-br from-slate-800/90 via-slate-800/70 to-slate-800/90 hover:from-slate-800 hover:to-red-950/40 border border-slate-700/80 hover:border-red-500/50 backdrop-blur p-4 sm:p-5 rounded-2xl flex items-center gap-4 transition-all duration-200 hover:shadow-xl hover:shadow-red-950/30 cursor-pointer group w-full relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-red-500/5 rounded-full blur-xl pointer-events-none"></div>
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-red-500/10 group-hover:bg-red-500/20 text-red-400 group-hover:text-red-300 flex items-center justify-center text-lg sm:text-xl shrink-0 transition-transform duration-200 group-hover:scale-110 shadow-inner">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span class="text-xs text-red-400 font-bold uppercase tracking-wider">Pusat Informasi</span>
                            <span class="text-[10px] bg-red-500/20 text-red-300 px-2 py-0.5 rounded-full font-extrabold border border-red-500/30">8 Menu</span>
                        </div>
                        <div class="text-xs sm:text-sm font-black text-white group-hover:text-red-200 transition truncate">Menu Informasi Lomba</div>
                        <div class="text-[10px] sm:text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5 font-medium">
                            <span>Buka Berkas & Panduan</span>
                            <i class="fa-solid fa-arrow-right text-[9px] text-red-400 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </section>

    <!-- Mata Lomba Section -->
    <section class="py-14 sm:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Mata Lomba yang Dipertandingkan</h2>
                <p class="text-slate-600 text-xs sm:text-sm mt-2">Pilih tingkatan sekolah Anda untuk melihat cabang lomba yang tersedia.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- MULA (SD / MI) -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm hover:shadow-xl border-2 border-emerald-500/40 hover:border-emerald-500 transition-all flex flex-col relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-green-600"></div>
                    <div class="flex items-center justify-between pb-4 border-b border-emerald-100/70 mb-5">
                        <div>
                            <span class="bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Tingkat SD / MI</span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">
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

                    <a href="{{ route('lomba.register') }}?level=Mula" class="w-full text-center py-3.5 rounded-xl font-extrabold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-950/15 transition flex items-center justify-center gap-2 text-xs sm:text-sm group-hover:shadow-lg">
                        <span>Daftar Tingkat Mula</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <!-- MADYA (SMP / MTs) -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm hover:shadow-xl border-2 border-blue-500/40 hover:border-blue-600 transition-all flex flex-col relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
                    <div class="flex items-center justify-between pb-4 border-b border-blue-100/70 mb-5">
                        <div>
                            <span class="bg-blue-100 text-blue-800 border border-blue-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Tingkat SMP / MTs</span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">
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

                    <a href="{{ route('lomba.register') }}?level=Madya" class="w-full text-center py-3.5 rounded-xl font-extrabold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-950/15 transition flex items-center justify-center gap-2 text-xs sm:text-sm group-hover:shadow-lg">
                        <span>Daftar Tingkat Madya</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <!-- WIRA (SMA / SMK / MA) -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm hover:shadow-xl border-2 border-amber-500/40 hover:border-amber-500 transition-all flex flex-col relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500"></div>
                    <div class="flex items-center justify-between pb-4 border-b border-amber-100/70 mb-5">
                        <div>
                            <span class="bg-amber-100 text-amber-900 border border-amber-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Tingkat SMA / SMK / MA</span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">
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

                    <a href="{{ route('lomba.register') }}?level=Wira" class="w-full text-center py-3.5 rounded-xl font-extrabold bg-amber-400 hover:bg-amber-500 text-slate-950 shadow-md shadow-amber-950/15 transition flex items-center justify-center gap-2 text-xs sm:text-sm group-hover:shadow-lg">
                        <span>Daftar Tingkat Wira</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endif

<!-- ============================================================ -->
<!-- MODAL POPUP: MENU INFORMASI LOMBA (8 SUB MENU TERPADU)       -->
<!-- ============================================================ -->
<div id="modal-informasi-lomba" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/85 backdrop-blur-md transition-all duration-300 flex items-center justify-center p-3 sm:p-6" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-7xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border-2 border-slate-700/80 rounded-3xl shadow-2xl shadow-red-950/50 overflow-hidden transform transition-all text-white my-4 sm:my-8">
        
        <!-- Modal Header -->
        <div class="p-5 sm:p-7 border-b border-slate-800 flex items-center justify-between gap-4 bg-slate-900/90 backdrop-blur sticky top-0 z-10">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-red-600 to-rose-700 text-white flex items-center justify-center text-xl sm:text-3xl shadow-xl shadow-red-900/50 shrink-0">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider bg-red-500/20 text-red-300 border border-red-500/40">
                            <i class="fa-solid fa-circle-info mr-1"></i> Pusat Unduhan & Dokumen Resmi
                        </span>
                    </div>
                    <h3 class="text-xl sm:text-3xl font-black text-white mt-1 tracking-tight">Menu Informasi Lomba</h3>
                </div>
            </div>
            <button type="button" onclick="closeInfoModal()" class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition border border-slate-700 cursor-pointer shrink-0 shadow-md" title="Tutup Modal (ESC)">
                <i class="fa-solid fa-xmark text-lg sm:text-xl"></i>
            </button>
        </div>

        <!-- Modal Body: Sub Menu Cards Grid -->
        <div class="p-4 sm:p-8 md:p-10 space-y-5 sm:space-y-8 max-h-[75vh] overflow-y-auto">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-800/60 p-4 sm:p-5 rounded-2xl border border-slate-700/60 text-xs sm:text-sm text-slate-200">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-bullhorn text-amber-400 text-base sm:text-lg shrink-0"></i>
                    <span>Pilih sub menu di bawah untuk mengunduh dokumen resmi, panduan, atau menghubungi panitia.</span>
                </div>
                <span class="text-[11px] sm:text-xs font-black text-amber-300 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg shrink-0 w-fit">{{ $infoMenus->count() }} Pilihan Sub Menu</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @forelse($infoMenus as $menu)
                    @php
                        $pal = $menu->theme_classes;
                        $hasFile = ($menu->action_type === 'file' && $menu->file_path);
                        $isLink = in_array($menu->action_type, ['link', 'whatsapp']) && $menu->url_link;
                    @endphp
                    <div class="bg-slate-800/70 hover:bg-slate-800/95 border-2 border-slate-700/70 {{ $pal['border_hover'] }} rounded-3xl p-5 sm:p-6 transition-all duration-200 flex flex-col justify-between group hover:shadow-xl {{ $pal['shadow_hover'] }}">
                        <div class="space-y-3.5">
                            <div class="flex items-center justify-between">
                                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl {{ $pal['icon_bg'] }} {{ $pal['icon_text'] }} {{ $pal['icon_hover'] }} flex items-center justify-center text-lg sm:text-2xl transition">
                                    <i class="{{ $menu->icon ?: 'fa-solid fa-folder-open' }}"></i>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $pal['badge_bg'] }} {{ $pal['badge_text'] }} border {{ $pal['badge_border'] }}">
                                    {{ $menu->category_badge }}
                                </span>
                            </div>
                            <div>
                                <h4 class="font-black text-white text-base sm:text-lg {{ $pal['title_hover'] }} transition">
                                    {{ $menu->title }}
                                </h4>
                                <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed font-medium">
                                    {{ $menu->description }}
                                </p>
                            </div>
                        </div>
                        <div class="pt-4 mt-3 border-t border-slate-700/60">
                            @if($hasFile)
                                <a href="{{ asset('storage/' . $menu->file_path) }}" target="_blank" class="w-full py-2.5 sm:py-3 px-4 rounded-xl {{ $pal['btn_solid'] }} font-black text-xs transition flex items-center justify-center gap-2 shadow-md">
                                    <i class="fa-solid fa-file-arrow-down"></i>
                                    <span>{{ $menu->button_text ?: 'Unduh Berkas' }}</span>
                                </a>
                            @elseif($menu->action_type === 'whatsapp' || str_contains(strtolower($menu->title), 'contact'))
                                <button type="button" onclick="openContactPersonModal()" class="w-full py-2.5 sm:py-3 px-4 rounded-xl {{ $pal['btn_solid'] }} font-black text-xs transition flex items-center justify-center gap-2 shadow-md cursor-pointer">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span>{{ ($menu->button_text && !in_array($menu->button_text, ['Hubungi Contact Person', 'Pilih Contact Person', 'Chat WhatsApp Panitia'])) ? $menu->button_text : 'Hubungi Panitia' }}</span>
                                </button>
                            @elseif($isLink)
                                <a href="{{ $menu->url_link }}" target="_blank" class="w-full py-2.5 sm:py-3 px-4 rounded-xl {{ $pal['btn_bg'] }} {{ $pal['btn_text'] }} font-black text-xs transition border {{ $pal['btn_border'] }} flex items-center justify-center gap-2 shadow-sm">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    <span>{{ $menu->button_text ?: 'Buka Tautan' }}</span>
                                </a>
                            @else
                                <button type="button" onclick="handleDocumentAction('{{ addslashes($menu->title) }}', 'notice')" class="w-full py-2.5 sm:py-3 px-4 rounded-xl {{ $pal['btn_bg'] }} {{ $pal['btn_text'] }} font-black text-xs transition border {{ $pal['btn_border'] }} flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                                    <i class="fa-solid fa-file-arrow-down"></i>
                                    <span>{{ $menu->button_text ?: 'Unduh Dokumen' }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center text-slate-400">
                        <i class="fa-solid fa-folder-open text-3xl mb-2"></i>
                        <p class="text-sm">Belum ada informasi lomba yang ditambahkan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 sm:p-6 bg-slate-950/95 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm text-slate-300">
            <div class="flex items-center gap-2 text-center sm:text-left">
                <i class="fa-solid fa-circle-question text-amber-400 text-sm sm:text-base shrink-0"></i>
                <span>Butuh surat resmi khusus atau konfirmasi berkas? Hubungi sekretariat panitia lomba.</span>
            </div>
            <button type="button" onclick="closeInfoModal()" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold px-6 py-2.5 rounded-xl transition border border-slate-700 cursor-pointer text-xs sm:text-sm">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL POPUP: CONTACT PERSON RESMI (3 TINGKAT: MULA, MADYA, WIRA) -->
<!-- ============================================================ -->
<div id="modal-contact-person" class="fixed inset-0 hidden overflow-y-auto bg-slate-950/90 backdrop-blur-md transition-all duration-300 flex items-center justify-center p-3 sm:p-6" style="z-index: 99999;" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-4xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border-2 border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden transform transition-all text-white my-4 sm:my-8">
        
        <!-- Header -->
        <div class="p-5 sm:p-7 border-b border-slate-800 flex items-center justify-between gap-4 bg-slate-900/90 backdrop-blur sticky top-0 z-10">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-xl sm:text-3xl shadow-xl shadow-emerald-950/50 shrink-0">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1">
                        <i class="fa-solid fa-headset"></i> Hotline Resmi Panitia
                    </span>
                    <h3 class="text-xl sm:text-3xl font-black text-white mt-1 tracking-tight">Contact Person Lomba (3 Tingkat)</h3>
                </div>
            </div>
            <button type="button" onclick="closeContactPersonModal()" class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition border border-slate-700 cursor-pointer shrink-0 shadow-md" title="Tutup (ESC)">
                <i class="fa-solid fa-xmark text-lg sm:text-xl"></i>
            </button>
        </div>

        <!-- Body: 3 Contact Cards -->
        <div class="p-4 sm:p-8 space-y-4 sm:space-y-5 max-h-[78vh] overflow-y-auto">
            <p class="text-xs sm:text-sm text-slate-200 font-medium leading-relaxed">
                Silakan pilih narahubung panitia di bawah ini sesuai dengan tingkatan kontingen sekolah Anda untuk berkonsultasi langsung via WhatsApp:
            </p>

            <div class="space-y-3 sm:space-y-4">
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
                                'btn_bg' => 'bg-emerald-600 hover:bg-emerald-500 text-white',
                            ],
                            'Madya' => [
                                'badge_bg' => 'bg-blue-500/20 text-blue-300 border-blue-500/40',
                                'title' => 'PMR MADYA (Tingkat SMP / MTs)',
                                'border' => 'border-blue-500/50 hover:border-blue-400',
                                'dot' => 'bg-blue-400',
                                'btn_bg' => 'bg-blue-600 hover:bg-blue-500 text-white',
                            ],
                            default => [
                                'badge_bg' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
                                'title' => 'PMR WIRA (Tingkat SMA / SMK / MA)',
                                'border' => 'border-amber-500/50 hover:border-amber-400',
                                'dot' => 'bg-amber-400',
                                'btn_bg' => 'bg-amber-400 hover:bg-amber-300 text-slate-950 font-black',
                            ],
                        };
                    @endphp

                    <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-slate-800/85 border-2 {{ $cardTheme['border'] }} transition-all flex flex-col md:flex-row md:items-center justify-between gap-4 group hover:bg-slate-800 shadow-md">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $cardTheme['dot'] }} animate-pulse shrink-0"></span>
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider {{ $cardTheme['badge_bg'] }} border">
                                    {{ $cardTheme['title'] }}
                                </span>
                            </div>
                            <div class="text-lg sm:text-xl font-black text-white">
                                {{ $ct['name'] ?? 'Panitia ' . $lvl }}
                            </div>
                            <div class="text-xs sm:text-sm text-slate-300 font-medium flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-phone text-xs text-slate-400"></i><span class="font-bold text-white">{{ $ct['phone'] ?? '081383885600' }}</span></span>
                                @if(!empty($ct['role']))
                                    <span class="text-slate-500 hidden sm:inline">•</span>
                                    <span class="text-slate-300">{{ $ct['role'] }}</span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ $waUrl }}" target="_blank" class="w-full md:w-auto px-5 py-3 rounded-xl {{ $cardTheme['btn_bg'] }} font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2 shadow-lg shrink-0 cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>Chat WA PMR {{ $lvl }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 sm:p-5 bg-slate-950/95 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-300">
            <span>Panitia siap melayani pertanyaan teknis perlombaan dan pendaftaran.</span>
            <button type="button" onclick="closeContactPersonModal()" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold transition border border-slate-700 cursor-pointer shrink-0">
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

    // Generic Countdown Timer Helper
    function setupCountdown(clockId, dId, hId, mId, sId) {
        const clockEl = document.getElementById(clockId);
        if (!clockEl) return;
        const targetStr = clockEl.getAttribute('data-target');
        if (!targetStr) return;

        const targetTime = new Date(targetStr).getTime();
        const daysEl = document.getElementById(dId);
        const hoursEl = document.getElementById(hId);
        const minutesEl = document.getElementById(mId);
        const secondsEl = document.getElementById(sId);

        function update() {
            const now = new Date().getTime();
            const diff = targetTime - now;

            if (diff <= 0) {
                if (daysEl) daysEl.textContent = '00';
                if (hoursEl) hoursEl.textContent = '00';
                if (minutesEl) minutesEl.textContent = '00';
                if (secondsEl) secondsEl.textContent = '00';
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                return;
            }

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            if (daysEl) daysEl.textContent = String(d).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(h).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(m).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(s).padStart(2, '0');
        }

        update();
        setInterval(update, 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Init Active Wave Countdown (Condition 1)
        setupCountdown('index-countdown-clock', 'idx-days', 'idx-hours', 'idx-minutes', 'idx-seconds');

        // Init Teaser Countdown (Condition 2)
        setupCountdown('teaser-countdown-clock', 'tsr-days', 'tsr-hours', 'tsr-minutes', 'tsr-seconds');

        // Modal backdrop click handlers
        const modal = document.getElementById('modal-informasi-lomba');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeInfoModal();
            });
        }

        const noticeModal = document.getElementById('modal-doc-notice');
        if (noticeModal) {
            noticeModal.addEventListener('click', function(e) {
                if (e.target === noticeModal) closeDocNotice();
            });
        }

        const contactModal = document.getElementById('modal-contact-person');
        if (contactModal) {
            contactModal.addEventListener('click', function(e) {
                if (e.target === contactModal) closeContactPersonModal();
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
