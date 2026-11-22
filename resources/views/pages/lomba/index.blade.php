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
    <!-- KONDISI: BELUM ADA JADWAL LOMBA / OFF-SEASON / KILAS BALIK JUARA    -->
    <!-- =================================================================== -->
    @php
        $targetEv = $event ?: $pastEvent;
        $mascotLeft = $targetEv ? $targetEv->getCountdownMascotLeftUrl() : asset('images/mascot/countdown_mascot_left.png');
        $mascotRight = $targetEv ? $targetEv->getCountdownMascotRightUrl() : asset('images/mascot/countdown_mascot_right.png');
    @endphp

    <section class="relative bg-slate-900 text-white pt-32 sm:pt-36 pb-16 sm:pb-24 overflow-hidden">
        <!-- Ambient Grid & Glow Background -->
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#ef4444_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-red-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <!-- Hero Card diapit oleh Maskot Harimau Putih PMR Wira SMANIC -->
            <div class="max-w-5xl mx-auto relative px-2 sm:px-4">
                <!-- Mascot Kiri (Putri / Siswi PMR) - Desktop -->
                <div class="hidden lg:block absolute -left-12 xl:-left-16 bottom-0 z-20 pointer-events-none select-none transition-transform hover:scale-105 duration-300">
                    <img src="{{ $mascotLeft }}" alt="Maskot PMR Putri" class="h-64 xl:h-76 w-auto drop-shadow-[0_15px_30px_rgba(0,0,0,0.7)] object-contain">
                </div>

                <!-- Mascot Kanan (Putra / Siswa PMR) - Desktop -->
                <div class="hidden lg:block absolute -right-12 xl:-right-16 bottom-0 z-20 pointer-events-none select-none transition-transform hover:scale-105 duration-300">
                    <img src="{{ $mascotRight }}" alt="Maskot PMR Putra" class="h-64 xl:h-76 w-auto drop-shadow-[0_15px_30px_rgba(0,0,0,0.7)] object-contain">
                </div>

                <!-- Mascots Mobile & Tablet (Tampil di Kiri & Kanan Atas Kartu) -->
                <div class="flex lg:hidden items-end justify-between px-4 sm:px-6 -mb-4 relative z-20 pointer-events-none">
                    <img src="{{ $mascotLeft }}" alt="Maskot PMR Putri" class="w-16 sm:w-20 h-auto drop-shadow-[0_10px_20px_rgba(0,0,0,0.6)] object-contain -rotate-3">
                    <img src="{{ $mascotRight }}" alt="Maskot PMR Putra" class="w-16 sm:w-20 h-auto drop-shadow-[0_10px_20px_rgba(0,0,0,0.6)] object-contain rotate-3">
                </div>

                <!-- Central Off-Season Status Card -->
                <div class="max-w-4xl mx-auto bg-gradient-to-br from-slate-800/95 via-slate-800/85 to-slate-900/95 border-2 border-red-500/40 rounded-3xl p-6 sm:p-10 shadow-2xl backdrop-blur relative overflow-hidden text-center">
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 space-y-4">
                        <!-- Trophy Icon Badge -->
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-amber-500/20 to-red-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-3xl shadow-lg shadow-amber-950/40">
                            <i class="fa-solid fa-trophy"></i>
                        </div>

                        <!-- Status Pill Badge -->
                        <div class="inline-flex items-center gap-2 bg-red-500/15 border border-red-500/40 text-red-300 px-4 py-1.5 rounded-full text-xs font-black tracking-wider uppercase">
                            <i class="fa-solid fa-calendar-check text-amber-400"></i>
                            <span>AGENDA LOMBA PMR WIRA CIAWI</span>
                        </div>

                        <!-- Main Heading -->
                        <h1 class="text-2xl sm:text-4xl md:text-5xl font-black tracking-tight text-white leading-tight">
                            Belum Ada Jadwal Lomba Saat Ini
                        </h1>

                        <!-- Subtitle / Explanation (Sesuai Arahan Pengguna) -->
                        <p class="text-slate-300 text-xs sm:text-sm md:text-base leading-relaxed max-w-2xl mx-auto">
                            Saat ini panitia belum membuka agenda kompetisi kepalangmerahan baru. Persiapan dan penyusunan ajang perlombaan berikutnya sedang dipersiapkan oleh pengurus PMR Wira SMAN 1 Ciawi.
                        </p>

                        <!-- Action Buttons -->
                        <div class="pt-3 flex flex-wrap items-center justify-center gap-3 sm:gap-4 max-w-2xl mx-auto">
                            <a href="{{ route('lomba.scoreboard') }}" class="w-full sm:w-auto bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-6 py-3 rounded-xl font-black shadow-lg shadow-emerald-950/40 transition flex items-center justify-center gap-2 text-xs sm:text-sm">
                                <i class="fa-solid fa-trophy text-amber-300"></i>
                                <span>Papan Skor & Klasemen Akhir</span>
                            </a>

                            <a href="{{ route('lomba.status') }}" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 hover:text-white px-6 py-3 rounded-xl font-bold transition flex items-center justify-center gap-2 text-xs sm:text-sm">
                                <i class="fa-solid fa-id-card text-rose-400"></i>
                                <span>Cek Status & Unduh Sertifikat</span>
                            </a>

                            <button type="button" onclick="openInfoModal()" class="w-full sm:w-auto bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white px-5 py-3 rounded-xl font-bold transition flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer">
                                <i class="fa-solid fa-folder-open text-blue-400"></i>
                                <span>Arsip Informasi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Stats Badges (Mobile: 2 cols, Desktop: 4 cols) -->
            <div class="mt-12 max-w-4xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-slate-800/70 border border-slate-700/70 p-4 rounded-2xl text-center backdrop-blur shadow-md">
                    <div class="text-amber-400 text-xl sm:text-2xl mb-1"><i class="fa-solid fa-trophy"></i></div>
                    <div class="text-xs sm:text-sm font-bold text-white">Juara Umum</div>
                    <div class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Piala Bergilir PMI</div>
                </div>

                <div class="bg-slate-800/70 border border-slate-700/70 p-4 rounded-2xl text-center backdrop-blur shadow-md">
                    <div class="text-emerald-400 text-xl sm:text-2xl mb-1"><i class="fa-solid fa-users"></i></div>
                    <div class="text-xs sm:text-sm font-bold text-white">450+ Relawan</div>
                    <div class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Partisipan Terdahulu</div>
                </div>

                <div class="bg-slate-800/70 border border-slate-700/70 p-4 rounded-2xl text-center backdrop-blur shadow-md">
                    <div class="text-blue-400 text-xl sm:text-2xl mb-1"><i class="fa-solid fa-graduation-cap"></i></div>
                    <div class="text-xs sm:text-sm font-bold text-white">3 Tingkatan</div>
                    <div class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Mula, Madya, Wira</div>
                </div>

                <div class="bg-slate-800/70 border border-slate-700/70 p-4 rounded-2xl text-center backdrop-blur shadow-md">
                    <div class="text-rose-400 text-xl sm:text-2xl mb-1"><i class="fa-solid fa-heart-pulse"></i></div>
                    <div class="text-xs sm:text-sm font-bold text-white">7 Prinsip Dasar</div>
                    <div class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Gerakan Palang Merah</div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================== -->
    <!-- KILAS BALIK LOMBA SEBELUMNYA & HALL OF FAME JUARA LENGKAP            -->
    <!-- =================================================================== -->
    <section class="py-16 sm:py-24 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
                <span class="text-xs font-black uppercase tracking-wider text-red-600 bg-red-100 border border-red-200 px-3.5 py-1.5 rounded-full inline-flex items-center gap-1.5 mb-3 shadow-xs">
                    <i class="fa-solid fa-crown text-amber-500"></i> Dokumentasi & Hall of Fame Kejuaraan
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Kilas Balik Lomba & Panggung Juara</h2>
                <p class="text-slate-600 text-xs sm:text-sm md:text-base mt-2">
                    Apresiasi sportivitas, dedikasi kemanusiaan, dan panggung kehormatan para juara ajang Sua Bhakti Berkarya PMR Wira SMAN 1 Ciawi Bogor.
                </p>
            </div>

            <!-- =================================================================== -->
            <!-- TAB BAR FILTER TINGKATAN (WIRA, MADYA, MULA, SEMUA)               -->
            <!-- =================================================================== -->
            <div class="mb-10">
                <div class="flex flex-wrap items-center justify-center gap-2 p-1.5 bg-slate-200/90 rounded-2xl max-w-2xl mx-auto shadow-inner border border-slate-300">
                    <button type="button" onclick="setHallOfFameTab('wira')" id="tab-btn-wira" class="hall-of-fame-tab-btn flex-1 py-2.5 px-4 rounded-xl font-black text-xs sm:text-sm transition flex items-center justify-center gap-2 bg-red-600 text-white shadow-md cursor-pointer" data-level="wira">
                        <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
                        <span>Tingkat Wira</span>
                        <span class="text-[10px] uppercase font-bold opacity-80 hidden md:inline">(SMA/SMK)</span>
                    </button>
                    <button type="button" onclick="setHallOfFameTab('madya')" id="tab-btn-madya" class="hall-of-fame-tab-btn flex-1 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2 text-slate-700 hover:text-slate-900 cursor-pointer" data-level="madya">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <span>Tingkat Madya</span>
                        <span class="text-[10px] uppercase font-bold opacity-80 hidden md:inline">(SMP/MTs)</span>
                    </button>
                    <button type="button" onclick="setHallOfFameTab('mula')" id="tab-btn-mula" class="hall-of-fame-tab-btn flex-1 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2 text-slate-700 hover:text-slate-900 cursor-pointer" data-level="mula">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>Tingkat Mula</span>
                        <span class="text-[10px] uppercase font-bold opacity-80 hidden md:inline">(SD/MI)</span>
                    </button>
                    <button type="button" onclick="setHallOfFameTab('all')" id="tab-btn-all" class="hall-of-fame-tab-btn py-2.5 px-3 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-1.5 text-slate-700 hover:text-slate-900 cursor-pointer" data-level="all" title="Tampilkan Semua Tingkatan">
                        <i class="fa-solid fa-layer-group text-xs"></i>
                        <span class="hidden sm:inline">Semua</span>
                    </button>
                </div>
            </div>

            <!-- =================================================================== -->
            <!-- 1. KONTEN TINGKAT WIRA (SMA / SMK / MA SEDERAJAT)                   -->
            <!-- =================================================================== -->
            <div class="hall-of-fame-level-content space-y-12 mb-16" data-level="wira" id="level-section-wira">
                
                <!-- PODIUM JUARA UMUM WIRA -->
                <div class="bg-gradient-to-br from-slate-900 via-red-950/40 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl border border-red-500/30 relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-700/80 mb-8">
                            <div>
                                <span class="text-red-400 font-black text-xs uppercase tracking-widest flex items-center gap-1.5 mb-1">
                                    <i class="fa-solid fa-award"></i> TINGKAT WIRA (SMA / SMK / MA)
                                </span>
                                <h3 class="text-xl sm:text-3xl font-black text-white">Podium Juara Umum Tingkat Wira</h3>
                            </div>
                            <span class="bg-red-500/20 text-red-300 border border-red-500/40 px-3 py-1.5 rounded-xl text-xs font-bold self-start md:self-auto flex items-center gap-1.5">
                                <i class="fa-solid fa-trophy text-amber-400"></i> Memperebutkan Piala Bergilir PMI Wira
                            </span>
                        </div>

                        <!-- 3 Posisi Podium (Juara 2 - Juara 1 - Juara 3) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                            <!-- JUARA UMUM 2 WIRA -->
                            <div class="bg-slate-800/85 rounded-2xl p-6 border border-slate-700 flex flex-col justify-between hover:border-slate-500 transition shadow-lg">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-full bg-slate-700 text-slate-200 text-xs font-black uppercase tracking-wider">
                                            🥈 Juara Umum 2
                                        </span>
                                        <span class="text-xs font-mono text-slate-400 font-bold">WIRA</span>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-slate-700/50 flex items-center justify-center text-3xl mx-auto mb-3 text-slate-300">
                                        🥈
                                    </div>
                                    <h4 class="text-lg font-black text-white text-center">SMAN 2 CIBINONG</h4>
                                    <p class="text-xs text-slate-400 text-center mt-1">Piala Tetap & Medali Perak Kejuaraan</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-slate-700/80 flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Akumulasi Skor:</span>
                                    <span class="font-mono font-black text-slate-200 text-base">1.320 pts</span>
                                </div>
                            </div>

                            <!-- JUARA UMUM 1 WIRA (GOLD HIGHLIGHT) -->
                            <div class="bg-gradient-to-b from-amber-500/20 via-slate-800 to-slate-800 rounded-3xl p-6 sm:p-7 border-2 border-amber-400 text-white flex flex-col justify-between shadow-2xl relative order-first md:order-none">
                                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-950 px-4 py-1 rounded-full text-xs font-black uppercase tracking-widest shadow-md flex items-center gap-1.5 whitespace-nowrap">
                                    <i class="fa-solid fa-crown text-amber-900"></i> JUARA UMUM BERGILIR WIRA
                                </div>
                                <div class="pt-2">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-full bg-amber-400 text-slate-950 text-xs font-black uppercase tracking-wider">
                                            🥇 Juara Umum 1
                                        </span>
                                        <span class="text-xs font-mono text-amber-300 font-bold">Piala Bergilir</span>
                                    </div>
                                    <div class="w-20 h-20 rounded-2xl bg-amber-400/20 border border-amber-400/40 flex items-center justify-center text-4xl mx-auto mb-3 text-amber-300 shadow-inner">
                                        🏆
                                    </div>
                                    <h4 class="text-xl font-black text-white text-center tracking-tight">SMAN 1 KOTA BOGOR</h4>
                                    <p class="text-xs text-amber-200 text-center mt-1">Peraih Piala Bergilir PMI Cabang Kabupaten Bogor (Wira)</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-amber-400/40 flex items-center justify-between text-xs">
                                    <span class="text-amber-200 font-bold uppercase tracking-wider">Total Skor Juara:</span>
                                    <span class="font-mono font-black text-amber-300 text-xl">1.450 pts</span>
                                </div>
                            </div>

                            <!-- JUARA UMUM 3 WIRA -->
                            <div class="bg-slate-800/85 rounded-2xl p-6 border border-slate-700 flex flex-col justify-between hover:border-slate-500 transition shadow-lg">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-full bg-amber-900/40 text-amber-300 text-xs font-black uppercase tracking-wider border border-amber-800">
                                            🥉 Juara Umum 3
                                        </span>
                                        <span class="text-xs font-mono text-slate-400 font-bold">WIRA</span>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-amber-900/30 flex items-center justify-center text-3xl mx-auto mb-3 text-amber-400">
                                        🥉
                                    </div>
                                    <h4 class="text-lg font-black text-white text-center">SMA PLUS PGRI CIBINONG</h4>
                                    <p class="text-xs text-slate-400 text-center mt-1">Piala Tetap & Medali Perunggu Kejuaraan</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-slate-700/80 flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Akumulasi Skor:</span>
                                    <span class="font-mono font-black text-amber-400 text-base">1.210 pts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DAFTAR SELURUH JUARA MATA LOMBA WIRA -->
                @php
                    $wiraCats = $categoriesByLevel->get('Wira') ?? collect();
                    $sampleWiraJuara = [
                        ['SMAN 1 Kota Bogor', 'SMAN 2 Cibinong', 'SMKN 1 Ciawi', 'SMAN 1 Sukaraja', 'SMA Plus PGRI', 'MAN 2 Kota Bogor'],
                        ['SMAN 2 Cibinong', 'SMAN 1 Kota Bogor', 'SMA Kosgoro Bogor', 'SMKN 1 Ciomas', 'SMA Bina Bangsa Mandiri', 'SMAN 1 Cisarua'],
                        ['SMAN 1 Kota Bogor', 'SMA Plus PGRI Cibinong', 'SMAN 1 Megamendung', 'SMAN 1 Dramaga', 'SMAN 3 Kota Bogor', 'SMAIT Ummul Quro'],
                        ['SMA Plus PGRI Cibinong', 'SMAN 1 Kota Bogor', 'MAN 2 Kota Bogor', 'SMAN 2 Cibinong', 'SMA Dwiwarna', 'SMAN 1 Babakan Madang'],
                        ['SMAN 1 Megamendung', 'SMAN 1 Kota Bogor', 'SMAN 2 Cibinong', 'SMKN 1 Ciawi', 'SMA Kornita IPB', 'SMA Regina Pacis'],
                        ['SMKN 1 Ciawi', 'SMAN 1 Ciawi', 'SMA Bina Profesi', 'SMA Plus PGRI Cibinong', 'SMAN 1 Cigombong', 'SMK Wikrama Bogor'],
                        ['SMAN 1 Kota Bogor', 'SMAN 2 Cibinong', 'SMA Regina Pacis', 'SMAN 1 Caringin', 'SMA IT Al-Madinah', 'SMKN 1 Bogor'],
                        ['SMAN 2 Cibinong', 'SMAN 1 Megamendung', 'SMA Plus PGRI Cibinong', 'SMAN 1 Kota Bogor', 'SMKN 1 Ciawi', 'SMA Kosgoro Bogor'],
                    ];
                    $wiraList = $wiraCats->isNotEmpty() ? $wiraCats : collect([
                        (object)['name' => 'Pertolongan Pertama', 'gender_category' => 'Putra', 'level' => 'Wira', 'code' => 'LPP-WIRA-PA', 'image_url' => asset('mockups/03_kegiatan.jpg')],
                        (object)['name' => 'Pertolongan Pertama', 'gender_category' => 'Putri', 'level' => 'Wira', 'code' => 'LPP-WIRA-PI', 'image_url' => asset('mockups/03_kegiatan.jpg')],
                        (object)['name' => 'Ketangkasan Tandu Reguler', 'gender_category' => 'Putra', 'level' => 'Wira', 'code' => 'LKTR-WIRA-PA', 'image_url' => asset('mockups/04_galeri.jpg')],
                        (object)['name' => 'Ketangkasan Tandu Reguler', 'gender_category' => 'Putri', 'level' => 'Wira', 'code' => 'LKTR-WIRA-PI', 'image_url' => asset('mockups/04_galeri.jpg')],
                        (object)['name' => 'Lomba Cepat Tepat (LCT)', 'gender_category' => 'Umum', 'level' => 'Wira', 'code' => 'LCT-WIRA', 'image_url' => asset('mockups/07_artikel.jpg')],
                        (object)['name' => 'Olimpiade Kepalangmerahan', 'gender_category' => 'Umum', 'level' => 'Wira', 'code' => 'OLIM-WIRA', 'image_url' => asset('mockups/07_artikel.jpg')],
                        (object)['name' => 'Mading Kreasi 3D', 'gender_category' => 'Umum', 'level' => 'Wira', 'code' => 'MADING-WIRA', 'image_url' => asset('mockups/01_home.jpg')],
                        (object)['name' => 'PMR Favorite (Sosial Media)', 'gender_category' => 'Umum', 'level' => 'Wira', 'code' => 'FAV-WIRA', 'image_url' => asset('mockups/01_home.jpg')],
                    ]);
                @endphp

                <div class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900">Rekapitulasi Seluruh Cabang Lomba Tingkat Wira</h3>
                                <span class="bg-red-100 text-red-800 text-[11px] font-black px-2.5 py-0.5 rounded-full border border-red-200">
                                    {{ $wiraList->count() }} Mata Lomba
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500">Daftar pemenang Juara Utama 1, 2, 3 hingga Juara Harapan 1, 2, 3 untuk seluruh mata lomba unit SMA/SMK/MA sederajat.</p>
                        </div>
                        <span class="text-xs font-bold text-red-700 bg-red-50 border border-red-200 px-3 py-1.5 rounded-xl self-start sm:self-auto shadow-2xs">
                            <i class="fa-solid fa-list-check text-red-500 mr-1"></i> Penjurian Resmi Wira
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($wiraList as $index => $cat)
                            @php
                                $catWins = method_exists($cat, 'getWinners') ? $cat->getWinners() : collect();
                                $w1 = $catWins->firstWhere('official_rank', 1) ?? $catWins->firstWhere('rank', 1);
                                $w2 = $catWins->firstWhere('official_rank', 2) ?? $catWins->firstWhere('rank', 2);
                                $w3 = $catWins->firstWhere('official_rank', 3) ?? $catWins->firstWhere('rank', 3);
                                $h1 = $catWins->firstWhere('official_rank', 4) ?? $catWins->firstWhere('rank', 4);
                                $h2 = $catWins->firstWhere('official_rank', 5) ?? $catWins->firstWhere('rank', 5);
                                $h3 = $catWins->firstWhere('official_rank', 6) ?? $catWins->firstWhere('rank', 6);
                                $sample = $sampleWiraJuara[$index % count($sampleWiraJuara)];
                                $imgUrl = $cat->image_url ?? asset('mockups/03_kegiatan.jpg');
                            @endphp

                            <div class="bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden flex flex-col justify-between group hover:border-red-400 transition">
                                <div>
                                    <div class="relative h-44 sm:h-48 bg-slate-900 overflow-hidden">
                                        <img src="{{ $imgUrl }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/30 to-transparent"></div>
                                        <div class="absolute top-3 left-3 flex items-center gap-1.5 flex-wrap">
                                            <span class="bg-red-600 text-white text-[10px] font-black uppercase px-2.5 py-1 rounded-lg shadow">
                                                Wira • {{ $cat->gender_category }}
                                            </span>
                                            @if(!empty($cat->code))
                                                <span class="bg-slate-900/80 backdrop-blur-xs text-slate-300 text-[10px] font-mono font-bold px-2 py-0.5 rounded-lg border border-slate-700">
                                                    {{ $cat->code }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="absolute bottom-3 left-3 right-3 text-white">
                                            <h4 class="font-black text-base sm:text-lg">
                                                {{ $cat->name }} {{ ($cat->gender_category && $cat->gender_category !== 'Umum') ? '('.$cat->gender_category.')' : '' }}
                                            </h4>
                                            <p class="text-xs text-slate-200">Ajang Kejuaraan PMR Wira SMAN 1 Ciawi Bogor</p>
                                        </div>
                                    </div>

                                    <div class="p-5 sm:p-6 space-y-2.5">
                                        <!-- JUARA 1 -->
                                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-amber-50/80 border border-amber-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-amber-500 text-white font-black text-xs flex items-center justify-center shrink-0">1</span>
                                                <span class="font-extrabold text-slate-900 truncate">{{ $w1 ? ($w1->team?->registration?->school_name ?? $w1->team?->team_name) : $sample[0] }}</span>
                                            </div>
                                            <span class="font-bold text-amber-800 text-[11px] shrink-0 ml-2">{{ $w1 ? ($w1->award_title ?: 'Juara 1 Utama') : 'Juara 1 Utama' }}</span>
                                        </div>

                                        <!-- JUARA 2 -->
                                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-50 border border-slate-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-slate-400 text-white font-black text-xs flex items-center justify-center shrink-0">2</span>
                                                <span class="font-bold text-slate-800 truncate">{{ $w2 ? ($w2->team?->registration?->school_name ?? $w2->team?->team_name) : $sample[1] }}</span>
                                            </div>
                                            <span class="font-semibold text-slate-600 text-[11px] shrink-0 ml-2">{{ $w2 ? ($w2->award_title ?: 'Juara 2 Utama') : 'Juara 2 Utama' }}</span>
                                        </div>

                                        <!-- JUARA 3 -->
                                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-orange-50/60 border border-orange-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-orange-400 text-white font-black text-xs flex items-center justify-center shrink-0">3</span>
                                                <span class="font-bold text-slate-800 truncate">{{ $w3 ? ($w3->team?->registration?->school_name ?? $w3->team?->team_name) : $sample[2] }}</span>
                                            </div>
                                            <span class="font-semibold text-orange-800 text-[11px] shrink-0 ml-2">{{ $w3 ? ($w3->award_title ?: 'Juara 3 Utama') : 'Juara 3 Utama' }}</span>
                                        </div>

                                        <!-- HARAPAN 1, 2, 3 -->
                                        <div class="pt-2 grid grid-cols-3 gap-2 text-[11px] text-slate-600">
                                            <div class="p-2 rounded-lg bg-slate-100/80 text-center">
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Harapan 1</span>
                                                <span class="font-semibold text-slate-800 truncate block">{{ $h1 ? ($h1->team?->registration?->school_name ?? $h1->team?->team_name) : $sample[3] }}</span>
                                            </div>
                                            <div class="p-2 rounded-lg bg-slate-100/80 text-center">
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Harapan 2</span>
                                                <span class="font-semibold text-slate-800 truncate block">{{ $h2 ? ($h2->team?->registration?->school_name ?? $h2->team?->team_name) : $sample[4] }}</span>
                                            </div>
                                            <div class="p-2 rounded-lg bg-slate-100/80 text-center">
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Harapan 3</span>
                                                <span class="font-semibold text-slate-800 truncate block">{{ $h3 ? ($h3->team?->registration?->school_name ?? $h3->team?->team_name) : $sample[5] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- =================================================================== -->
            <!-- 2. KONTEN TINGKAT MADYA (SMP / MTS SEDERAJAT)                       -->
            <!-- =================================================================== -->
            <div class="hall-of-fame-level-content space-y-12 mb-16 hidden" data-level="madya" id="level-section-madya">
                
                <!-- PODIUM JUARA UMUM MADYA -->
                <div class="bg-gradient-to-br from-slate-900 via-blue-950/40 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl border border-blue-500/30 relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-700/80 mb-8">
                            <div>
                                <span class="text-blue-400 font-black text-xs uppercase tracking-widest flex items-center gap-1.5 mb-1">
                                    <i class="fa-solid fa-award"></i> TINGKAT MADYA (SMP / MTS)
                                </span>
                                <h3 class="text-xl sm:text-3xl font-black text-white">Podium Juara Umum Tingkat Madya</h3>
                            </div>
                            <span class="bg-blue-500/20 text-blue-300 border border-blue-500/40 px-3 py-1.5 rounded-xl text-xs font-bold self-start md:self-auto flex items-center gap-1.5">
                                <i class="fa-solid fa-trophy text-amber-400"></i> Memperebutkan Piala Bergilir PMI Madya
                            </span>
                        </div>

                        <!-- 3 Posisi Podium (Juara 2 - Juara 1 - Juara 3) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                            <!-- JUARA UMUM 2 MADYA -->
                            <div class="bg-slate-800/85 rounded-2xl p-6 border border-slate-700 flex flex-col justify-between hover:border-slate-500 transition shadow-lg">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-full bg-slate-700 text-slate-200 text-xs font-black uppercase tracking-wider">
                                            🥈 Juara Umum 2
                                        </span>
                                        <span class="text-xs font-mono text-slate-400 font-bold">MADYA</span>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-slate-700/50 flex items-center justify-center text-3xl mx-auto mb-3 text-slate-300">
                                        🥈
                                    </div>
                                    <h4 class="text-lg font-black text-white text-center">SMPN 1 CIAWI</h4>
                                    <p class="text-xs text-slate-400 text-center mt-1">Piala Tetap Kepala Sekolah & Piagam</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-slate-700/80 flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Akumulasi Skor:</span>
                                    <span class="font-mono font-black text-slate-200 text-base">1.290 pts</span>
                                </div>
                            </div>

                            <!-- JUARA UMUM 1 MADYA (GOLD HIGHLIGHT) -->
                            <div class="bg-gradient-to-b from-blue-500/20 via-slate-800 to-slate-800 rounded-3xl p-6 sm:p-7 border-2 border-amber-400 text-white flex flex-col justify-between shadow-2xl relative order-first md:order-none">
                                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-950 px-4 py-1 rounded-full text-xs font-black uppercase tracking-widest shadow-md flex items-center gap-1.5 whitespace-nowrap">
                                    <i class="fa-solid fa-crown text-amber-900"></i> JUARA UMUM BERGILIR MADYA
                                </div>
                                <div class="pt-2">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-full bg-amber-400 text-slate-950 text-xs font-black uppercase tracking-wider">
                                            🥇 Juara Umum 1
                                        </span>
                                        <span class="text-xs font-mono text-amber-300 font-bold">Piala Bergilir</span>
                                    </div>
                                    <div class="w-20 h-20 rounded-2xl bg-amber-400/20 border border-amber-400/40 flex items-center justify-center text-4xl mx-auto mb-3 text-amber-300 shadow-inner">
                                        🏆
                                    </div>
                                    <h4 class="text-xl font-black text-white text-center tracking-tight">SMPN 1 CARINGIN</h4>
                                    <p class="text-xs text-amber-200 text-center mt-1">Peraih Piala Bergilir PMI Cabang Kabupaten Bogor (Madya)</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-amber-400/40 flex items-center justify-between text-xs">
                                    <span class="text-amber-200 font-bold uppercase tracking-wider">Total Skor Juara:</span>
                                    <span class="font-mono font-black text-amber-300 text-xl">1.380 pts</span>
                                </div>
                            </div>

                            <!-- JUARA UMUM 3 MADYA -->
                            <div class="bg-slate-800/85 rounded-2xl p-6 border border-slate-700 flex flex-col justify-between hover:border-slate-500 transition shadow-lg">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-full bg-amber-900/40 text-amber-300 text-xs font-black uppercase tracking-wider border border-amber-800">
                                            🥉 Juara Umum 3
                                        </span>
                                        <span class="text-xs font-mono text-slate-400 font-bold">MADYA</span>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-amber-900/30 flex items-center justify-center text-3xl mx-auto mb-3 text-amber-400">
                                        🥉
                                    </div>
                                    <h4 class="text-lg font-black text-white text-center">SMPN 2 MEGAMENDUNG</h4>
                                    <p class="text-xs text-slate-400 text-center mt-1">Piala Tetap & Medali Perunggu</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-slate-700/80 flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Akumulasi Skor:</span>
                                    <span class="font-mono font-black text-amber-400 text-base">1.150 pts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DAFTAR SELURUH JUARA MATA LOMBA MADYA -->
                @php
                    $madyaCats = $categoriesByLevel->get('Madya') ?? collect();
                    $sampleMadyaJuara = [
                        ['SMP Bina Insan Mandiri', 'SMPN 1 Caringin', 'SMPN 4 Gunung Putri', 'SMP PGRI 9 Bogor', 'SMPN 2 Ciawi', 'MTs Al-Ikhlas'],
                        ['SMPN 1 Caringin', 'SMPN 1 Ciawi', 'SMPN 2 Megamendung', 'SMP Al-Azhar Plus', 'MTsN 1 Bogor', 'SMP Amaliah Ciawi'],
                        ['SMP PGRI 9 Kota Bogor', 'SMP Bina Insan Mandiri', 'SMPN 1 Caringin', 'SMPN 4 Gn. Putri', 'SMPN 1 Megamendung', 'SMP IT Nurul Fikri'],
                        ['SMPN 2 Megamendung', 'SMP PGRI 9 Kota Bogor', 'SMPN 1 Caringin', 'SMP IT Al-Fityan', 'SMPN 1 Ciawi', 'SMP PGRI Ciawi'],
                        ['SMPN 1 Caringin', 'SMP Bina Insan Mandiri', 'SMP PGRI 9 Kota Bogor', 'SMPN 2 Ciawi', 'MTsN 1 Bogor', 'SMP Citra Kasih'],
                        ['SMPN 1 Ciawi', 'SMPN 1 Bogor', 'MTsN 2 Bogor', 'SMP Islam Terpadu', 'SMPN 2 Ciawi', 'SMP IT BBS Bogor'],
                        ['SMPN 4 Gunung Putri', 'SMP PGRI 9 Kota Bogor', 'SMPN 1 Caringin', 'SMP Bina Insan', 'SMPN 3 Ciawi', 'SMP Al-Azhar Plus'],
                        ['SMPN 1 Ciawi', 'SMPN 1 Caringin', 'SMPN 2 Megamendung', 'SMP Al-Azhar Plus', 'SMPN 4 Bogor', 'SMP Amaliah Ciawi'],
                    ];
                    $madyaList = $madyaCats->isNotEmpty() ? $madyaCats : collect([
                        (object)['name' => 'Pertolongan Pertama', 'gender_category' => 'Putra', 'level' => 'Madya', 'code' => 'LPP-MADYA-PA', 'image_url' => asset('mockups/03_kegiatan.jpg')],
                        (object)['name' => 'Pertolongan Pertama', 'gender_category' => 'Putri', 'level' => 'Madya', 'code' => 'LPP-MADYA-PI', 'image_url' => asset('mockups/03_kegiatan.jpg')],
                        (object)['name' => 'Ketangkasan Tandu Reguler', 'gender_category' => 'Putra', 'level' => 'Madya', 'code' => 'LKTR-MADYA-PA', 'image_url' => asset('mockups/04_galeri.jpg')],
                        (object)['name' => 'Ketangkasan Tandu Reguler', 'gender_category' => 'Putri', 'level' => 'Madya', 'code' => 'LKTR-MADYA-PI', 'image_url' => asset('mockups/04_galeri.jpg')],
                        (object)['name' => 'Lomba Cepat Tepat (LCT)', 'gender_category' => 'Umum', 'level' => 'Madya', 'code' => 'LCT-MADYA', 'image_url' => asset('mockups/07_artikel.jpg')],
                        (object)['name' => 'Olimpiade Kepalangmerahan', 'gender_category' => 'Umum', 'level' => 'Madya', 'code' => 'OLIM-MADYA', 'image_url' => asset('mockups/07_artikel.jpg')],
                        (object)['name' => 'Pendidikan Remaja Sebaya (PRS)', 'gender_category' => 'Umum', 'level' => 'Madya', 'code' => 'PRS-MADYA', 'image_url' => asset('mockups/01_home.jpg')],
                        (object)['name' => 'Mading Kreasi', 'gender_category' => 'Umum', 'level' => 'Madya', 'code' => 'MADING-MADYA', 'image_url' => asset('mockups/01_home.jpg')],
                    ]);
                @endphp

                <div class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900">Rekapitulasi Seluruh Cabang Lomba Tingkat Madya</h3>
                                <span class="bg-blue-100 text-blue-800 text-[11px] font-black px-2.5 py-0.5 rounded-full border border-blue-200">
                                    {{ $madyaList->count() }} Mata Lomba
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500">Daftar pemenang Juara Utama 1, 2, 3 hingga Juara Harapan 1, 2, 3 untuk unit SMP/MTs sederajat.</p>
                        </div>
                        <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-xl self-start sm:self-auto shadow-2xs">
                            <i class="fa-solid fa-list-check text-blue-500 mr-1"></i> Penjurian Resmi Madya
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($madyaList as $index => $cat)
                            @php
                                $catWins = method_exists($cat, 'getWinners') ? $cat->getWinners() : collect();
                                $w1 = $catWins->firstWhere('official_rank', 1) ?? $catWins->firstWhere('rank', 1);
                                $w2 = $catWins->firstWhere('official_rank', 2) ?? $catWins->firstWhere('rank', 2);
                                $w3 = $catWins->firstWhere('official_rank', 3) ?? $catWins->firstWhere('rank', 3);
                                $h1 = $catWins->firstWhere('official_rank', 4) ?? $catWins->firstWhere('rank', 4);
                                $h2 = $catWins->firstWhere('official_rank', 5) ?? $catWins->firstWhere('rank', 5);
                                $h3 = $catWins->firstWhere('official_rank', 6) ?? $catWins->firstWhere('rank', 6);
                                $sample = $sampleMadyaJuara[$index % count($sampleMadyaJuara)];
                                $imgUrl = $cat->image_url ?? asset('mockups/03_kegiatan.jpg');
                            @endphp

                            <div class="bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden flex flex-col justify-between group hover:border-blue-400 transition">
                                <div>
                                    <div class="relative h-44 sm:h-48 bg-slate-900 overflow-hidden">
                                        <img src="{{ $imgUrl }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/30 to-transparent"></div>
                                        <div class="absolute top-3 left-3 flex items-center gap-1.5 flex-wrap">
                                            <span class="bg-blue-600 text-white text-[10px] font-black uppercase px-2.5 py-1 rounded-lg shadow">
                                                Madya • {{ $cat->gender_category }}
                                            </span>
                                            @if(!empty($cat->code))
                                                <span class="bg-slate-900/80 backdrop-blur-xs text-slate-300 text-[10px] font-mono font-bold px-2 py-0.5 rounded-lg border border-slate-700">
                                                    {{ $cat->code }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="absolute bottom-3 left-3 right-3 text-white">
                                            <h4 class="font-black text-base sm:text-lg">
                                                {{ $cat->name }} {{ ($cat->gender_category && $cat->gender_category !== 'Umum') ? '('.$cat->gender_category.')' : '' }}
                                            </h4>
                                            <p class="text-xs text-slate-200">Ajang Kejuaraan PMR Madya SMAN 1 Ciawi Bogor</p>
                                        </div>
                                    </div>

                                    <div class="p-5 sm:p-6 space-y-2.5">
                                        <!-- JUARA 1 -->
                                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-amber-50/80 border border-amber-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-amber-500 text-white font-black text-xs flex items-center justify-center shrink-0">1</span>
                                                <span class="font-extrabold text-slate-900 truncate">{{ $w1 ? ($w1->team?->registration?->school_name ?? $w1->team?->team_name) : $sample[0] }}</span>
                                            </div>
                                            <span class="font-bold text-amber-800 text-[11px] shrink-0 ml-2">{{ $w1 ? ($w1->award_title ?: 'Juara 1 Utama') : 'Juara 1 Utama' }}</span>
                                        </div>

                                        <!-- JUARA 2 -->
                                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-50 border border-slate-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-slate-400 text-white font-black text-xs flex items-center justify-center shrink-0">2</span>
                                                <span class="font-bold text-slate-800 truncate">{{ $w2 ? ($w2->team?->registration?->school_name ?? $w2->team?->team_name) : $sample[1] }}</span>
                                            </div>
                                            <span class="font-semibold text-slate-600 text-[11px] shrink-0 ml-2">{{ $w2 ? ($w2->award_title ?: 'Juara 2 Utama') : 'Juara 2 Utama' }}</span>
                                        </div>

                                        <!-- JUARA 3 -->
                                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-orange-50/60 border border-orange-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-orange-400 text-white font-black text-xs flex items-center justify-center shrink-0">3</span>
                                                <span class="font-bold text-slate-800 truncate">{{ $w3 ? ($w3->team?->registration?->school_name ?? $w3->team?->team_name) : $sample[2] }}</span>
                                            </div>
                                            <span class="font-semibold text-orange-800 text-[11px] shrink-0 ml-2">{{ $w3 ? ($w3->award_title ?: 'Juara 3 Utama') : 'Juara 3 Utama' }}</span>
                                        </div>

                                        <!-- HARAPAN 1, 2, 3 -->
                                        <div class="pt-2 grid grid-cols-3 gap-2 text-[11px] text-slate-600">
                                            <div class="p-2 rounded-lg bg-slate-100/80 text-center">
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Harapan 1</span>
                                                <span class="font-semibold text-slate-800 truncate block">{{ $h1 ? ($h1->team?->registration?->school_name ?? $h1->team?->team_name) : $sample[3] }}</span>
                                            </div>
                                            <div class="p-2 rounded-lg bg-slate-100/80 text-center">
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Harapan 2</span>
                                                <span class="font-semibold text-slate-800 truncate block">{{ $h2 ? ($h2->team?->registration?->school_name ?? $h2->team?->team_name) : $sample[4] }}</span>
                                            </div>
                                            <div class="p-2 rounded-lg bg-slate-100/80 text-center">
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Harapan 3</span>
                                                <span class="font-semibold text-slate-800 truncate block">{{ $h3 ? ($h3->team?->registration?->school_name ?? $h3->team?->team_name) : $sample[5] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- =================================================================== -->
            <!-- 3. KONTEN TINGKAT MULA (SD / MI SEDERAJAT)                           -->
            <!-- =================================================================== -->
            <div class="hall-of-fame-level-content space-y-12 mb-16 hidden" data-level="mula" id="level-section-mula">
                
                <!-- PODIUM JUARA UMUM MULA -->
                <div class="bg-gradient-to-br from-slate-900 via-emerald-950/40 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl border border-emerald-500/30 relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-600/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-700/80 mb-8">
                            <div>
                                <span class="text-emerald-400 font-black text-xs uppercase tracking-widest flex items-center gap-1.5 mb-1">
                                    <i class="fa-solid fa-award"></i> TINGKAT MULA (SD / MI)
                                </span>
                                <h3 class="text-xl sm:text-3xl font-black text-white">Podium Juara Umum Tingkat Mula</h3>
                            </div>
                            <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 px-3 py-1.5 rounded-xl text-xs font-bold self-start md:self-auto flex items-center gap-1.5">
                                <i class="fa-solid fa-trophy text-amber-400"></i> Memperebutkan Piala Bergilir PMI Mula
                            </span>
                        </div>

                        <!-- 3 Posisi Podium (Juara 2 - Juara 1 - Juara 3) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                            <!-- JUARA UMUM 2 MULA -->
                            <div class="bg-slate-800/85 rounded-2xl p-6 border border-slate-700 flex flex-col justify-between hover:border-slate-500 transition shadow-lg">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-full bg-slate-700 text-slate-200 text-xs font-black uppercase tracking-wider">
                                            🥈 Juara Umum 2
                                        </span>
                                        <span class="text-xs font-mono text-slate-400 font-bold">MULA</span>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-slate-700/50 flex items-center justify-center text-3xl mx-auto mb-3 text-slate-300">
                                        🥈
                                    </div>
                                    <h4 class="text-lg font-black text-white text-center">SDN CIAWI 01</h4>
                                    <p class="text-xs text-slate-400 text-center mt-1">Piala Tetap & Medali Perak</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-slate-700/80 flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Akumulasi Skor:</span>
                                    <span class="font-mono font-black text-slate-200 text-base">1.150 pts</span>
                                </div>
                            </div>

                            <!-- JUARA UMUM 1 MULA (GOLD HIGHLIGHT) -->
                            <div class="bg-gradient-to-b from-emerald-500/20 via-slate-800 to-slate-800 rounded-3xl p-6 sm:p-7 border-2 border-amber-400 text-white flex flex-col justify-between shadow-2xl relative order-first md:order-none">
                                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-950 px-4 py-1 rounded-full text-xs font-black uppercase tracking-widest shadow-md flex items-center gap-1.5 whitespace-nowrap">
                                    <i class="fa-solid fa-crown text-amber-900"></i> JUARA UMUM BERGILIR MULA
                                </div>
                                <div class="pt-2">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-full bg-amber-400 text-slate-950 text-xs font-black uppercase tracking-wider">
                                            🥇 Juara Umum 1
                                        </span>
                                        <span class="text-xs font-mono text-amber-300 font-bold">Piala Bergilir</span>
                                    </div>
                                    <div class="w-20 h-20 rounded-2xl bg-amber-400/20 border border-amber-400/40 flex items-center justify-center text-4xl mx-auto mb-3 text-amber-300 shadow-inner">
                                        🏆
                                    </div>
                                    <h4 class="text-xl font-black text-white text-center tracking-tight">SDN HARAPAN BANGSA</h4>
                                    <p class="text-xs text-amber-200 text-center mt-1">Peraih Piala Bergilir PMI Cabang Kabupaten Bogor (Mula)</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-amber-400/40 flex items-center justify-between text-xs">
                                    <span class="text-amber-200 font-bold uppercase tracking-wider">Total Skor Juara:</span>
                                    <span class="font-mono font-black text-amber-300 text-xl">1.240 pts</span>
                                </div>
                            </div>

                            <!-- JUARA UMUM 3 MULA -->
                            <div class="bg-slate-800/85 rounded-2xl p-6 border border-slate-700 flex flex-col justify-between hover:border-slate-500 transition shadow-lg">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 rounded-full bg-amber-900/40 text-amber-300 text-xs font-black uppercase tracking-wider border border-amber-800">
                                            🥉 Juara Umum 3
                                        </span>
                                        <span class="text-xs font-mono text-slate-400 font-bold">MULA</span>
                                    </div>
                                    <div class="w-16 h-16 rounded-2xl bg-amber-900/30 flex items-center justify-center text-3xl mx-auto mb-3 text-amber-400">
                                        🥉
                                    </div>
                                    <h4 class="text-lg font-black text-white text-center">SDIT INSAN KAMIL BOGOR</h4>
                                    <p class="text-xs text-slate-400 text-center mt-1">Piala Tetap & Medali Perunggu</p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-slate-700/80 flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Akumulasi Skor:</span>
                                    <span class="font-mono font-black text-amber-400 text-base">1.080 pts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DAFTAR SELURUH JUARA MATA LOMBA MULA -->
                @php
                    $mulaCats = $categoriesByLevel->get('Mula') ?? collect();
                    $sampleMulaJuara = [
                        ['SDN Harapan Bangsa', 'SDN Ciawi 01', 'SDIT Insan Kamil Bogor', 'SDN Pandansari 02', 'SDN Bendungan 01', 'MIS Ciawi'],
                        ['SDN Ciawi 01', 'SDN Harapan Bangsa', 'SDIT Insan Kamil Bogor', 'SDN Ciawi 02', 'SDN Pandansari 01', 'MIS Al-Hidayah'],
                        ['SDN Ciawi 01', 'SDN Harapan Bangsa', 'SDN Pandansari 01', 'SDIT Insan Kamil', 'SDN Teluk Pinang 02', 'SDN Bitungsari 01'],
                        ['SDIT Insan Kamil Bogor', 'SDN Ciawi 01', 'SDN Harapan Bangsa', 'SDN Banjarwaru 01', 'SDN Citapen 01', 'SDN Cipayung 02'],
                        ['SDIT Insan Kamil Bogor', 'SDN Harapan Bangsa', 'SDN Ciawi 01', 'SDN Banjarwaru 01', 'SDN Citapen 01', 'SDN Cipayung 02'],
                        ['SDN Harapan Bangsa', 'SDIT Insan Kamil Bogor', 'SDN Ciawi 01', 'SDN Ciawi 02', 'MIS Al-Hidayah', 'SDN Banjarsari 01'],
                    ];
                    $mulaList = $mulaCats->isNotEmpty() ? $mulaCats : collect([
                        (object)['name' => 'Pertolongan Pertama', 'gender_category' => 'Putra', 'level' => 'Mula', 'code' => 'LPP-MULA-PA', 'image_url' => asset('mockups/03_kegiatan.jpg')],
                        (object)['name' => 'Pertolongan Pertama', 'gender_category' => 'Putri', 'level' => 'Mula', 'code' => 'LPP-MULA-PI', 'image_url' => asset('mockups/03_kegiatan.jpg')],
                        (object)['name' => 'Ketangkasan Tandu Reguler', 'gender_category' => 'Umum', 'level' => 'Mula', 'code' => 'LKTR-MULA', 'image_url' => asset('mockups/04_galeri.jpg')],
                        (object)['name' => 'Ketangkasan Cuci Tangan (CTPS)', 'gender_category' => 'Umum', 'level' => 'Mula', 'code' => 'CTPS-MULA', 'image_url' => asset('mockups/03_kegiatan.jpg')],
                        (object)['name' => 'Lomba Mewarnai & Menggambar', 'gender_category' => 'Umum', 'level' => 'Mula', 'code' => 'WARNA-MULA', 'image_url' => asset('mockups/01_home.jpg')],
                        (object)['name' => 'Mading Kreasi Mula', 'gender_category' => 'Umum', 'level' => 'Mula', 'code' => 'MADING-MULA', 'image_url' => asset('mockups/01_home.jpg')],
                    ]);
                @endphp

                <div class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900">Rekapitulasi Seluruh Cabang Lomba Tingkat Mula</h3>
                                <span class="bg-emerald-100 text-emerald-800 text-[11px] font-black px-2.5 py-0.5 rounded-full border border-emerald-200">
                                    {{ $mulaList->count() }} Mata Lomba
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500">Daftar pemenang Juara Utama 1, 2, 3 hingga Juara Harapan 1, 2, 3 untuk unit SD/MI sederajat.</p>
                        </div>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl self-start sm:self-auto shadow-2xs">
                            <i class="fa-solid fa-list-check text-emerald-500 mr-1"></i> Penjurian Resmi Mula
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($mulaList as $index => $cat)
                            @php
                                $catWins = method_exists($cat, 'getWinners') ? $cat->getWinners() : collect();
                                $w1 = $catWins->firstWhere('official_rank', 1) ?? $catWins->firstWhere('rank', 1);
                                $w2 = $catWins->firstWhere('official_rank', 2) ?? $catWins->firstWhere('rank', 2);
                                $w3 = $catWins->firstWhere('official_rank', 3) ?? $catWins->firstWhere('rank', 3);
                                $h1 = $catWins->firstWhere('official_rank', 4) ?? $catWins->firstWhere('rank', 4);
                                $h2 = $catWins->firstWhere('official_rank', 5) ?? $catWins->firstWhere('rank', 5);
                                $h3 = $catWins->firstWhere('official_rank', 6) ?? $catWins->firstWhere('rank', 6);
                                $sample = $sampleMulaJuara[$index % count($sampleMulaJuara)];
                                $imgUrl = $cat->image_url ?? asset('mockups/03_kegiatan.jpg');
                            @endphp

                            <div class="bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden flex flex-col justify-between group hover:border-emerald-400 transition">
                                <div>
                                    <div class="relative h-44 sm:h-48 bg-slate-900 overflow-hidden">
                                        <img src="{{ $imgUrl }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/30 to-transparent"></div>
                                        <div class="absolute top-3 left-3 flex items-center gap-1.5 flex-wrap">
                                            <span class="bg-emerald-600 text-white text-[10px] font-black uppercase px-2.5 py-1 rounded-lg shadow">
                                                Mula • {{ $cat->gender_category }}
                                            </span>
                                            @if(!empty($cat->code))
                                                <span class="bg-slate-900/80 backdrop-blur-xs text-slate-300 text-[10px] font-mono font-bold px-2 py-0.5 rounded-lg border border-slate-700">
                                                    {{ $cat->code }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="absolute bottom-3 left-3 right-3 text-white">
                                            <h4 class="font-black text-base sm:text-lg">
                                                {{ $cat->name }} {{ ($cat->gender_category && $cat->gender_category !== 'Umum') ? '('.$cat->gender_category.')' : '' }}
                                            </h4>
                                            <p class="text-xs text-slate-200">Ajang Kejuaraan PMR Mula SMAN 1 Ciawi Bogor</p>
                                        </div>
                                    </div>

                                    <div class="p-5 sm:p-6 space-y-2.5">
                                        <!-- JUARA 1 -->
                                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-amber-50/80 border border-amber-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-amber-500 text-white font-black text-xs flex items-center justify-center shrink-0">1</span>
                                                <span class="font-extrabold text-slate-900 truncate">{{ $w1 ? ($w1->team?->registration?->school_name ?? $w1->team?->team_name) : $sample[0] }}</span>
                                            </div>
                                            <span class="font-bold text-amber-800 text-[11px] shrink-0 ml-2">{{ $w1 ? ($w1->award_title ?: 'Juara 1 Utama') : 'Juara 1 Utama' }}</span>
                                        </div>

                                        <!-- JUARA 2 -->
                                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-50 border border-slate-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-slate-400 text-white font-black text-xs flex items-center justify-center shrink-0">2</span>
                                                <span class="font-bold text-slate-800 truncate">{{ $w2 ? ($w2->team?->registration?->school_name ?? $w2->team?->team_name) : $sample[1] }}</span>
                                            </div>
                                            <span class="font-semibold text-slate-600 text-[11px] shrink-0 ml-2">{{ $w2 ? ($w2->award_title ?: 'Juara 2 Utama') : 'Juara 2 Utama' }}</span>
                                        </div>

                                        <!-- JUARA 3 -->
                                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-orange-50/60 border border-orange-200">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-orange-400 text-white font-black text-xs flex items-center justify-center shrink-0">3</span>
                                                <span class="font-bold text-slate-800 truncate">{{ $w3 ? ($w3->team?->registration?->school_name ?? $w3->team?->team_name) : $sample[2] }}</span>
                                            </div>
                                            <span class="font-semibold text-orange-800 text-[11px] shrink-0 ml-2">{{ $w3 ? ($w3->award_title ?: 'Juara 3 Utama') : 'Juara 3 Utama' }}</span>
                                        </div>

                                        <!-- HARAPAN 1, 2, 3 -->
                                        <div class="pt-2 grid grid-cols-3 gap-2 text-[11px] text-slate-600">
                                            <div class="p-2 rounded-lg bg-slate-100/80 text-center">
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Harapan 1</span>
                                                <span class="font-semibold text-slate-800 truncate block">{{ $h1 ? ($h1->team?->registration?->school_name ?? $h1->team?->team_name) : $sample[3] }}</span>
                                            </div>
                                            <div class="p-2 rounded-lg bg-slate-100/80 text-center">
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Harapan 2</span>
                                                <span class="font-semibold text-slate-800 truncate block">{{ $h2 ? ($h2->team?->registration?->school_name ?? $h2->team?->team_name) : $sample[4] }}</span>
                                            </div>
                                            <div class="p-2 rounded-lg bg-slate-100/80 text-center">
                                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Harapan 3</span>
                                                <span class="font-semibold text-slate-800 truncate block">{{ $h3 ? ($h3->team?->registration?->school_name ?? $h3->team?->team_name) : $sample[5] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Tab Switching JavaScript -->
            <script>
                function setHallOfFameTab(level) {
                    const tabs = document.querySelectorAll('.hall-of-fame-tab-btn');
                    const sections = document.querySelectorAll('.hall-of-fame-level-content');
                    
                    tabs.forEach(btn => {
                        const btnLevel = btn.getAttribute('data-level');
                        if (btnLevel === level) {
                            let activeClass = 'bg-slate-900 text-white';
                            if (level === 'wira') activeClass = 'bg-red-600 text-white';
                            else if (level === 'madya') activeClass = 'bg-blue-600 text-white';
                            else if (level === 'mula') activeClass = 'bg-emerald-600 text-white';

                            btn.className = 'hall-of-fame-tab-btn ' + (btnLevel === 'all' ? 'py-2.5 px-3' : 'flex-1 py-2.5 px-4') + ' rounded-xl font-black text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow-md cursor-pointer ' + activeClass;
                        } else {
                            btn.className = 'hall-of-fame-tab-btn ' + (btnLevel === 'all' ? 'py-2.5 px-3' : 'flex-1 py-2.5 px-4') + ' rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2 text-slate-700 hover:text-slate-900 hover:bg-slate-100/60 cursor-pointer';
                        }
                    });

                    sections.forEach(sec => {
                        const secLevel = sec.getAttribute('data-level');
                        if (level === 'all' || secLevel === level) {
                            sec.classList.remove('hidden');
                        } else {
                            sec.classList.add('hidden');
                        }
                    });
                }
            </script>

            <!-- 3. GALERI MOMEN & DOKUMENTASI AKSI PESERTA -->
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900">Potret Sportivitas & Aksi Relawan</h3>
                        <p class="text-xs sm:text-sm text-slate-500">Kumpulan momen kegigihan, persahabatan, dan semangat kemanusiaan di arena lomba.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition group">
                        <div class="relative h-48 bg-slate-900 overflow-hidden">
                            <img src="{{ asset('mockups/03_kegiatan.jpg') }}" alt="Simulasi Medis Darurat" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                            <span class="absolute bottom-3 left-3 text-white font-bold text-xs">Simulasi Penanganan Pasien Lapangan</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition group">
                        <div class="relative h-48 bg-slate-900 overflow-hidden">
                            <img src="{{ asset('mockups/04_galeri.jpg') }}" alt="Aksi Pasang Tandu Bambu" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                            <span class="absolute bottom-3 left-3 text-white font-bold text-xs">Ketangkasan & Kekompakan Pasang Tandu</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition group">
                        <div class="relative h-48 bg-slate-900 overflow-hidden">
                            <img src="{{ asset('mockups/07_artikel.jpg') }}" alt="Edukasi Kemanusiaan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                            <span class="absolute bottom-3 left-3 text-white font-bold text-xs">Edukasi & Uji Wawasan Remaja Sebaya</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. KERJASAMA & NARAHUBUNG PANITIA -->
            <div class="mt-14 bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-8 text-white border border-slate-700 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
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
                    @if($event ? $event->isRegisterButtonVisible() : false)
                        <a href="{{ route('lomba.register') }}" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3.5 rounded-xl font-bold shadow-lg shadow-red-900/40 transition flex items-center justify-center gap-2 text-sm sm:text-base">
                            <i class="fa-solid fa-file-pen"></i> <span>Daftar Sekarang</span>
                        </a>
                    @endif
                    @if($event ? $event->isStatusButtonVisible() : true)
                        <a href="{{ route('lomba.status') }}" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white px-7 py-3.5 rounded-xl font-bold transition flex items-center justify-center gap-2 text-sm sm:text-base">
                            <i class="fa-solid fa-magnifying-glass"></i> <span>Cek Status Pendaftaran</span>
                        </a>
                    @endif
                    @if($event ? $event->isScoreboardButtonVisible() : true)
                        <a href="{{ route('lomba.scoreboard') }}" class="w-full sm:w-auto bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 px-7 py-3.5 rounded-xl font-bold transition flex items-center justify-center gap-2 text-sm sm:text-base">
                            <i class="fa-solid fa-square-poll-vertical"></i> <span>Live Klasemen Juara</span>
                        </a>
                    @endif
                    <a href="#tiket-parkir-umum" class="w-full sm:w-auto bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 text-slate-950 font-black px-7 py-3.5 rounded-xl shadow-lg shadow-amber-950/30 transition flex items-center justify-center gap-2 text-sm sm:text-base">
                        <i class="fa-solid fa-coins text-slate-950"></i> <span>Beli Tiket Parkir Umum</span>
                    </a>
                    @if($event ? $event->isHandbookButtonVisible() : false)
                        @php
                            $handbookUrl = ($event && $event->handbook_file) ? asset('storage/' . $event->handbook_file) : null;
                            if (!$handbookUrl && isset($infoMenus)) {
                                $juklakItem = $infoMenus->first(fn($m) => in_array($m->title, ['Juklak Juknis', 'Buku Panduan Lomba']));
                                if ($juklakItem && $juklakItem->file_path) {
                                    $handbookUrl = asset('storage/' . $juklakItem->file_path);
                                }
                            }
                        @endphp
                        <a href="{{ $handbookUrl ?: '#menu-informasi' }}" {{ $handbookUrl ? 'target="_blank"' : 'onclick="openInfoMenuModal()"' }} class="w-full sm:w-auto bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/40 text-indigo-300 px-7 py-3.5 rounded-xl font-bold transition flex items-center justify-center gap-2 text-sm sm:text-base">
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
                <!-- Countdown Card Wrapper with Mascots -->
                <div class="mt-10 sm:mt-14 max-w-5xl mx-auto relative px-2 sm:px-4">

                    @if($event->isCountdownMascotVisible())
                        <!-- Mascot Kiri (Gambar 2: Putri / Siswi PMR) - Desktop -->
                        <div class="hidden lg:block absolute -left-12 xl:-left-20 bottom-0 z-20 pointer-events-none select-none transition-transform hover:scale-105 duration-300">
                            <img src="{{ $event->getCountdownMascotLeftUrl() }}" alt="Maskot PMR Putri" class="h-64 xl:h-72 w-auto drop-shadow-[0_15px_30px_rgba(0,0,0,0.7)] object-contain">
                        </div>

                        <!-- Mascot Kanan (Gambar 3: Putra / Siswa PMR) - Desktop -->
                        <div class="hidden lg:block absolute -right-12 xl:-right-20 bottom-0 z-20 pointer-events-none select-none transition-transform hover:scale-105 duration-300">
                            <img src="{{ $event->getCountdownMascotRightUrl() }}" alt="Maskot PMR Putra" class="h-64 xl:h-72 w-auto drop-shadow-[0_15px_30px_rgba(0,0,0,0.7)] object-contain">
                        </div>

                        <!-- Mascots Mobile & Tablet (Tampil di Kiri & Kanan Atas Kartu) -->
                        <div class="flex lg:hidden items-end justify-between px-4 sm:px-6 -mb-4 relative z-20 pointer-events-none">
                            <img src="{{ $event->getCountdownMascotLeftUrl() }}" alt="Maskot PMR Putri" class="w-16 sm:w-20 h-auto drop-shadow-[0_10px_20px_rgba(0,0,0,0.6)] object-contain -rotate-3">
                            <img src="{{ $event->getCountdownMascotRightUrl() }}" alt="Maskot PMR Putra" class="w-16 sm:w-20 h-auto drop-shadow-[0_10px_20px_rgba(0,0,0,0.6)] object-contain rotate-3">
                        </div>
                    @endif

                    <!-- Countdown & Wave Schedule Card -->
                    <div class="max-w-4xl mx-auto bg-gradient-to-br from-slate-800/95 via-slate-800/80 to-slate-900/95 border-2 border-red-500/40 rounded-3xl p-5 sm:p-7 shadow-2xl backdrop-blur relative overflow-hidden">
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
            </div>
            @endif

            <!-- Quick Info Banner -->
            <div class="mt-10 sm:mt-12 grid grid-cols-1 {{ ($isInfoMenuVisible ?? true) ? 'md:grid-cols-3 max-w-4xl' : 'md:grid-cols-2 max-w-2xl' }} gap-4 sm:gap-6 mx-auto">
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

                @if($isInfoMenuVisible ?? true)
                <!-- Pusat Informasi Button -->
                <button type="button" onclick="openInfoModal()" class="text-left bg-gradient-to-br from-slate-800/90 via-slate-800/70 to-slate-800/90 hover:from-slate-800 hover:to-red-950/40 border border-slate-700/80 hover:border-red-500/50 backdrop-blur p-4 sm:p-5 rounded-2xl flex items-center gap-4 transition-all duration-200 hover:shadow-xl hover:shadow-red-950/30 cursor-pointer group w-full relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-red-500/5 rounded-full blur-xl pointer-events-none"></div>
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-red-500/10 group-hover:bg-red-500/20 text-red-400 group-hover:text-red-300 flex items-center justify-center text-lg sm:text-xl shrink-0 transition-transform duration-200 group-hover:scale-110 shadow-inner">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span class="text-xs text-red-400 font-bold uppercase tracking-wider">Pusat Informasi</span>
                            <span class="text-[10px] bg-red-500/20 text-red-300 px-2 py-0.5 rounded-full font-extrabold border border-red-500/30">{{ $infoMenus->count() }} Menu</span>
                        </div>
                        <div class="text-xs sm:text-sm font-black text-white group-hover:text-red-200 transition truncate">Menu Informasi Lomba</div>
                        <div class="text-[10px] sm:text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5 font-medium">
                            <span>Buka Berkas & Panduan</span>
                            <i class="fa-solid fa-arrow-right text-[9px] text-red-400 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </button>
                @endif
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

    <!-- =================================================================== -->
    <!-- SECTION KHUSUS: PEMESANAN & PEMBELIAN E-TIKET PARKIR PENGUNJUNG UMUM  -->
    <!-- =================================================================== -->
    <section id="tiket-parkir-umum" class="py-14 sm:py-20 bg-slate-900 text-white relative overflow-hidden border-t border-slate-800"
        x-data="publicParkingBooking({
            rates: {
                r2_motor: {{ $parkingFareSetting->fare_r2_motor ?? 5000 }},
                r4_car: {{ $parkingFareSetting->fare_r4_car ?? 10000 }},
                r4_elf: {{ $parkingFareSetting->fare_r4_minibus ?? 25000 }},
                r4_bus: {{ $parkingFareSetting->fare_r6_bus ?? 50000 }}
            },
            isActive: {{ ($parkingFareSetting->is_public_parking_active ?? true) ? 'true' : 'false' }},
            defaultType: 'r4_car'
        })">
        
        <!-- Ambient Glow -->
        <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500/15 text-amber-300 border border-amber-500/30 mb-3 shadow-inner">
                    <i class="fa-solid fa-coins text-amber-400"></i>
                    <span>E-TIKET RESMI HARI-H • FASILITAS PENGUNJUNG UMUM</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                    Pemesanan & Pembelian E-Tiket Parkir Resmi Pengunjung Umum
                </h2>
                <p class="text-slate-400 text-xs sm:text-sm md:text-base mt-2.5 leading-relaxed">
                    Fasilitas kantong parkir resmi Sua Bhakti Berkarya IV 2026 untuk orang tua siswa, alumni, supporter, dan penonton umum di kampus SMAN 1 Ciawi Bogor.
                </p>
            </div>

            <!-- Flash Alert -->
            @if(session('success'))
                <div class="mb-8 max-w-4xl mx-auto bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 px-5 py-4 rounded-2xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-xl shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 max-w-4xl mx-auto bg-rose-500/15 border border-rose-500/40 text-rose-300 px-5 py-4 rounded-2xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-xl shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold">{{ session('error') }}</div>
                </div>
            @endif

            <!-- 4 Column Rates Showcase Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-10">
                <!-- Roda 2 -->
                <div @click="selectVehicleType('r2_motor')" :class="vehicleType === 'r2_motor' ? 'ring-2 ring-purple-400 bg-slate-800/90 shadow-lg shadow-purple-950/40 scale-[1.02]' : 'bg-slate-800/60 hover:bg-slate-800/80 border-slate-700/80'" class="p-5 rounded-3xl border transition cursor-pointer flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-11 h-11 rounded-2xl bg-purple-500/15 text-purple-400 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition">
                                <i class="fa-solid fa-motorcycle"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-purple-300 bg-purple-500/20 border border-purple-500/30 px-2.5 py-0.5 rounded-full">
                                RODA 2
                            </span>
                        </div>
                        <h4 class="font-black text-sm text-white">Sepeda Motor</h4>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Matic, Bebek, Sport</span>
                    </div>
                    <div class="pt-3 border-t border-slate-700/60 flex items-baseline justify-between mt-3">
                        <span class="text-[10px] text-slate-400">Tarif Flat:</span>
                        <span class="font-mono font-black text-base text-purple-300">Rp {{ number_format($parkingFareSetting->fare_r2_motor ?? 5000, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Roda 4 Mobil Pribadi -->
                <div @click="selectVehicleType('r4_car')" :class="vehicleType === 'r4_car' ? 'ring-2 ring-blue-400 bg-slate-800/90 shadow-lg shadow-blue-950/40 scale-[1.02]' : 'bg-slate-800/60 hover:bg-slate-800/80 border-slate-700/80'" class="p-5 rounded-3xl border transition cursor-pointer flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-11 h-11 rounded-2xl bg-blue-500/15 text-blue-400 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition">
                                <i class="fa-solid fa-car"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-blue-300 bg-blue-500/20 border border-blue-500/30 px-2.5 py-0.5 rounded-full">
                                RODA 4
                            </span>
                        </div>
                        <h4 class="font-black text-sm text-white">Mobil Pribadi / MPV / SUV</h4>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Avanza, Innova, Brio, dll</span>
                    </div>
                    <div class="pt-3 border-t border-slate-700/60 flex items-baseline justify-between mt-3">
                        <span class="text-[10px] text-slate-400">Tarif Flat:</span>
                        <span class="font-mono font-black text-base text-blue-300">Rp {{ number_format($parkingFareSetting->fare_r4_car ?? 10000, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Roda 4+ Minibus -->
                <div @click="selectVehicleType('r4_elf')" :class="vehicleType === 'r4_elf' ? 'ring-2 ring-amber-400 bg-slate-800/90 shadow-lg shadow-amber-950/40 scale-[1.02]' : 'bg-slate-800/60 hover:bg-slate-800/80 border-slate-700/80'" class="p-5 rounded-3xl border transition cursor-pointer flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-11 h-11 rounded-2xl bg-amber-500/15 text-amber-400 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition">
                                <i class="fa-solid fa-van-shuttle"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-300 bg-amber-500/20 border border-amber-500/30 px-2.5 py-0.5 rounded-full">
                                RODA 4+ MINIBUS
                            </span>
                        </div>
                        <h4 class="font-black text-sm text-white">Minibus (Elf / HiAce)</h4>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Elf Short/Long, Toyota HiAce</span>
                    </div>
                    <div class="pt-3 border-t border-slate-700/60 flex items-baseline justify-between mt-3">
                        <span class="text-[10px] text-slate-400">Tarif Flat:</span>
                        <span class="font-mono font-black text-base text-amber-300">Rp {{ number_format($parkingFareSetting->fare_r4_minibus ?? 25000, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Roda 6+ Bus Besar -->
                <div @click="selectVehicleType('r4_bus')" :class="vehicleType === 'r4_bus' ? 'ring-2 ring-rose-400 bg-slate-800/90 shadow-lg shadow-rose-950/40 scale-[1.02]' : 'bg-slate-800/60 hover:bg-slate-800/80 border-slate-700/80'" class="p-5 rounded-3xl border transition cursor-pointer flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-11 h-11 rounded-2xl bg-rose-500/15 text-rose-400 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition">
                                <i class="fa-solid fa-bus"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-300 bg-rose-500/20 border border-rose-500/30 px-2.5 py-0.5 rounded-full">
                                RODA 6+ BUS
                            </span>
                        </div>
                        <h4 class="font-black text-sm text-white">Bus Besar Pariwisata</h4>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Bus Besar 40–60 Kursi</span>
                    </div>
                    <div class="pt-3 border-t border-slate-700/60 flex items-baseline justify-between mt-3">
                        <span class="text-[10px] text-slate-400">Tarif Flat:</span>
                        <span class="font-mono font-black text-base text-rose-300">Rp {{ number_format($parkingFareSetting->fare_r6_bus ?? 50000, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Two-Column Interactive Grid: Form Booking & Live Preview -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: Booking Form (7 Cols) -->
                <div class="lg:col-span-7 bg-slate-800/80 border border-slate-700 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-700/80 mb-6">
                        <div>
                            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block">Langkah 1 &bull; Isi Data Kendaraan</span>
                            <h3 class="text-lg sm:text-xl font-black text-white mt-0.5">Formulir Pemesanan E-Tiket Parkir</h3>
                        </div>
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                    </div>

                    @if(!($parkingFareSetting->is_public_parking_active ?? true))
                        <div class="p-6 bg-slate-900/90 rounded-2xl border border-slate-700 text-center space-y-2">
                            <i class="fa-solid fa-lock text-3xl text-amber-400 mb-1"></i>
                            <h4 class="text-base font-bold text-white">Layanan Parkir Umum Sedang Ditutup</h4>
                            <p class="text-xs text-slate-400">Panitia sedang memprioritaskan kantong parkir khusus kontingen sekolah peserta lomba. Silakan cek kembali nanti atau hubungi panitia.</p>
                        </div>
                    @else
                        <form action="{{ route('lomba.buy-parking-ticket') }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Pilihan Kategori Kendaraan -->
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Jenis Kendaraan <span class="text-rose-400">*</span></label>
                                <select name="vehicle_type" x-model="vehicleType" required class="w-full text-xs sm:text-sm font-bold bg-slate-900 border border-slate-700 focus:border-amber-500 rounded-xl px-3.5 py-3 text-white focus:outline-none">
                                    <option value="r2_motor">Roda 2 Sepeda Motor (Rp {{ number_format($parkingFareSetting->fare_r2_motor ?? 5000, 0, ',', '.') }})</option>
                                    <option value="r4_car">Roda 4 Mobil Pribadi / MPV / SUV (Rp {{ number_format($parkingFareSetting->fare_r4_car ?? 10000, 0, ',', '.') }})</option>
                                    <option value="r4_elf">Roda 4+ Minibus (Elf / HiAce) (Rp {{ number_format($parkingFareSetting->fare_r4_minibus ?? 25000, 0, ',', '.') }})</option>
                                    <option value="r4_bus">Roda 6+ Bus Besar Pariwisata (Rp {{ number_format($parkingFareSetting->fare_r6_bus ?? 50000, 0, ',', '.') }})</option>
                                </select>
                            </div>

                            <!-- Plat Nomor & Merek -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="text-xs font-bold text-slate-300 block mb-1">Nomor Polisi (Plat Nomor) <span class="text-rose-400">*</span></label>
                                    <input type="text" name="license_plate" x-model="licensePlate" required placeholder="F 1845 EA" class="w-full text-xs sm:text-sm font-mono font-black uppercase bg-slate-900 border border-slate-700 focus:border-amber-500 rounded-xl px-3.5 py-3 text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-slate-300 block mb-1">Merek & Warna Kendaraan</label>
                                    <input type="text" name="vehicle_brand" x-model="vehicleBrand" placeholder="Toyota Avanza - Hitam" class="w-full text-xs sm:text-sm bg-slate-900 border border-slate-700 focus:border-amber-500 rounded-xl px-3.5 py-3 text-white focus:outline-none">
                                </div>
                            </div>

                            <!-- Nama & WhatsApp -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="text-xs font-bold text-slate-300 block mb-1">Nama Pemesan / Pengemudi <span class="text-rose-400">*</span></label>
                                    <input type="text" name="driver_name" x-model="driverName" required placeholder="Bpk. Hendra (Wali Murid)" class="w-full text-xs sm:text-sm bg-slate-900 border border-slate-700 focus:border-amber-500 rounded-xl px-3.5 py-3 text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-slate-300 block mb-1">No. WhatsApp Aktif <span class="text-rose-400">*</span></label>
                                    <input type="text" name="driver_phone" x-model="driverPhone" required placeholder="08123456789" class="w-full text-xs sm:text-sm font-mono bg-slate-900 border border-slate-700 focus:border-amber-500 rounded-xl px-3.5 py-3 text-white focus:outline-none">
                                </div>
                            </div>

                            <!-- Pilihan Metode Pembayaran -->
                            <div class="pt-2">
                                <label class="text-xs font-bold text-slate-300 block mb-2">Pilihan Metode Pembayaran <span class="text-rose-400">*</span></label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label :class="paymentMethod === 'qris' ? 'border-emerald-500 bg-emerald-500/10 text-white' : 'border-slate-700 bg-slate-900/60 text-slate-300'" class="p-3.5 rounded-2xl border-2 flex items-center gap-3 cursor-pointer transition">
                                        <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="text-emerald-500 focus:ring-emerald-500">
                                        <div>
                                            <span class="font-bold text-xs block">QRIS Digital (Instan)</span>
                                            <span class="text-[10px] text-slate-400">Scan bayar otomatis & tiket langsung lunas</span>
                                        </div>
                                    </label>
                                    <label :class="paymentMethod === 'cash' ? 'border-amber-500 bg-amber-500/10 text-white' : 'border-slate-700 bg-slate-900/60 text-slate-300'" class="p-3.5 rounded-2xl border-2 flex items-center gap-3 cursor-pointer transition">
                                        <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="text-amber-500 focus:ring-amber-500">
                                        <div>
                                            <span class="font-bold text-xs block">Bayar Tunai di Pintu Masuk</span>
                                            <span class="text-[10px] text-slate-400">Bayar ke petugas saat tiba di gerbang</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-3">
                                <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:to-rose-800 text-white font-black text-sm sm:text-base shadow-xl shadow-red-950/40 transition flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-lock"></i>
                                    <span>Bayar & Terbitkan E-Tiket Parkir (<span x-text="formattedFee"></span>)</span>
                                </button>
                                <span class="block text-[11px] text-slate-400 text-center mt-2">
                                    <i class="fa-solid fa-shield-halved text-emerald-400"></i> E-Tiket ber-QR Code resmi akan langsung tampil dan tautan tiket dikirim via WhatsApp.
                                </span>
                            </div>
                        </form>
                    @endif
                </div>

                <!-- Right: Live Preview & Search Ticket (5 Cols) -->
                <div class="lg:col-span-5 space-y-6">

                    <!-- Live Digital Ticket Preview Card -->
                    <div class="bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 border-2 border-dashed border-slate-700 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-400">
                                <i class="fa-solid fa-qrcode mr-1"></i> PRATINJAU E-TIKET DIGITAL
                            </span>
                            <span class="text-[9px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-400">SBB IV 2026</span>
                        </div>

                        <div class="text-center space-y-3">
                            <div class="inline-block p-3.5 bg-white rounded-2xl shadow-lg border border-slate-200">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=SBB4-PARKING-PREVIEW" alt="QR Code Preview" class="w-32 h-32 mx-auto">
                            </div>

                            <div>
                                <span class="font-mono text-base font-black px-3 py-1 bg-black rounded-lg border border-slate-700 text-white tracking-widest inline-block" x-text="licensePlate ? licensePlate.toUpperCase() : 'F 1845 EA'"></span>
                                <span class="block text-xs font-bold text-amber-300 mt-1" x-text="driverName ? driverName : 'Nama Pengemudi'"></span>
                            </div>

                            <div class="bg-slate-900/90 rounded-xl p-3 border border-slate-800 text-xs text-left space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Jenis:</span>
                                    <span class="font-bold text-white" x-text="vehicleLabel"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Tarif Flat:</span>
                                    <span class="font-bold text-amber-400 font-mono" x-text="formattedFee"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Status Pembayaran:</span>
                                    <span class="font-bold" :class="paymentMethod === 'qris' ? 'text-emerald-400' : 'text-amber-400'" x-text="paymentMethod === 'qris' ? 'Lunas (QRIS Digital)' : 'Bayar di Pintu Masuk'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lacak / Cek Tiket Saya Card -->
                    <div class="bg-slate-800/60 border border-slate-700/80 rounded-3xl p-5 shadow-lg">
                        <h4 class="font-black text-sm text-white flex items-center gap-2 mb-1">
                            <i class="fa-solid fa-magnifying-glass text-amber-400"></i> Sudah Pernah Memesan Tiket?
                        </h4>
                        <p class="text-xs text-slate-400 mb-3">Masukkan nomor polisi atau kode tiket Anda untuk membuka kembali karcis digital Anda:</p>

                        <form action="{{ route('lomba.check-parking-ticket') }}" method="GET" class="flex gap-2">
                            <input type="text" name="ticket_search" placeholder="No. Plat (Contoh: F 1845 EA)..." required class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono font-bold text-white uppercase focus:outline-none focus:border-amber-500">
                            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-4 py-2 rounded-xl font-black text-xs transition shrink-0 cursor-pointer">
                                Lacak
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </section>
@endif

@if($isInfoMenuVisible ?? true)
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
                        $isGalleryMedia = str_contains(strtolower($menu->category_badge ?? ''), 'galeri') || str_contains(strtolower($menu->button_text ?? ''), 'galeri') || str_contains(strtolower($menu->title ?? ''), 'dokumentasi');
                        $hasFile = ($menu->action_type === 'file' && $menu->file_path);
                        $isLink = (in_array($menu->action_type, ['link', 'whatsapp']) || $isGalleryMedia) && ($menu->url_link || $isGalleryMedia);
                        $menuLink = $isGalleryMedia ? ($menu->url_link ?: 'https://www.instagram.com/pmr_sman1c') : $menu->url_link;
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
                                <a href="{{ $menuLink }}" target="_blank" rel="noopener noreferrer" class="w-full py-2.5 sm:py-3 px-4 rounded-xl {{ $pal['btn_bg'] }} {{ $pal['btn_text'] }} font-black text-xs transition border {{ $pal['btn_border'] }} flex items-center justify-center gap-2 shadow-sm">
                                    @if($isGalleryMedia)
                                        <i class="fa-brands fa-instagram text-sm"></i>
                                    @else
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    @endif
                                    <span>{{ $menu->button_text ?: 'Buka Galeri Foto & Video' }}</span>
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
@endif

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

    function publicParkingBooking(config) {
        return {
            vehicleType: config.defaultType || 'r4_car',
            rates: config.rates || { r2_motor: 5000, r4_car: 10000, r4_elf: 25000, r4_bus: 50000 },
            licensePlate: '',
            vehicleBrand: '',
            driverName: '',
            driverPhone: '',
            paymentMethod: 'qris',
            selectVehicleType(type) {
                this.vehicleType = type;
            },
            get currentFee() {
                return this.rates[this.vehicleType] || 10000;
            },
            get formattedFee() {
                return 'Rp ' + Number(this.currentFee).toLocaleString('id-ID');
            },
            get vehicleLabel() {
                const labels = {
                    'r2_motor': 'Roda 2 Sepeda Motor',
                    'r4_car': 'Roda 4 Mobil Pribadi / MPV / SUV',
                    'r4_elf': 'Roda 4+ Minibus (Elf / HiAce)',
                    'r4_bus': 'Roda 6+ Bus Besar Pariwisata'
                };
                return labels[this.vehicleType] || 'Roda 4 Mobil Pribadi / MPV / SUV';
            }
        };
    }
</script>
@endpush
@endsection
