@extends('layouts.admin')

@php
    $catLevel = ucfirst(strtolower($category->level ?? 'Madya'));
    $isMadya = ($catLevel === 'Madya');
    $levelTitle = $isMadya ? 'MADYA (SMP / MTs)' : 'WIRA (SMA / SMK / MA)';
    
    // Theme Variables
    $levelBadgeClass = $isMadya ? 'bg-blue-600 text-white border-blue-400' : 'bg-amber-500 text-slate-950 font-black border-amber-300';
    $headerGradient = $isMadya ? 'from-slate-900 via-blue-950 to-slate-900 border-blue-800/40' : 'from-stone-900 via-amber-950 to-stone-900 border-amber-700/40';
    $accentBar = $isMadya ? 'bg-blue-600' : 'bg-amber-500';
    $accentText = $isMadya ? 'text-blue-600' : 'text-amber-600';
    $accentBgLight = $isMadya ? 'bg-blue-50 border-blue-200 text-blue-950' : 'bg-amber-50 border-amber-200 text-amber-950';
    $saveBtnClass = $isMadya ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-500/20' : 'bg-amber-500 hover:bg-amber-600 text-slate-950 font-black shadow-amber-500/20';
    $scoreTextColor = $isMadya ? 'text-blue-700' : 'text-amber-800';
    $activeInputBorder = $isMadya ? 'focus:border-blue-500 focus:ring-2 focus:ring-blue-200' : 'focus:border-amber-500 focus:ring-2 focus:ring-amber-200';
@endphp

@section('title', 'Grid Nilai Olimpiade - ' . $category->name . ' (' . $category->level . ')')
@section('page_title', 'Grid Nilai Olimpiade Kepalangmerahan (' . $levelTitle . ')')

@section('top_actions')
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.competition-scores.index', ['level' => $category->level]) }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2 rounded-xl font-bold text-xs shadow-2xs transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('admin.competition-scores.olimpiade-live', ['category' => $category->id, 'stage' => $activeStage]) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-sm transition flex items-center gap-1.5">
            <i class="fa-solid fa-desktop"></i> Layar Panggung / Proyektor
        </a>
        <form action="{{ route('admin.competition-scores.olimpiade-seed-simulation', $category->id) }}" method="POST" onsubmit="return confirm('Muat data simulasi 20 regu Olimpiade sesuai sheet panitia?')">
            @csrf
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-3.5 py-2 rounded-xl font-black text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Muat Data Simulasi Sheet
            </button>
        </form>
        <a href="{{ route('admin.competition-scores.olimpiade-export-excel', $category->id) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-sm transition flex items-center gap-1.5">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6 pb-28">

    <!-- Flash message -->
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-400 text-emerald-900 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-2xl text-emerald-600"></i>
                <div class="font-bold text-xs sm:text-sm">{{ session('success') }}</div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 text-xs font-bold">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border-2 border-rose-400 text-rose-900 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-2xl text-rose-600"></i>
                <div class="font-bold text-xs sm:text-sm">{{ session('error') }}</div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 text-xs font-bold">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
    @endif

    <!-- ================= APP HEADER CARD & STAGE STEPPER ================= -->
    <header class="bg-gradient-to-r {{ $headerGradient }} text-white rounded-3xl p-6 sm:p-7 shadow-xl border relative overflow-hidden space-y-6">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full blur-3xl opacity-25 {{ $isMadya ? 'bg-blue-500' : 'bg-amber-500' }} pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10 pb-5 border-b border-white/10">
            <!-- Left: Logos & Title -->
            <div class="flex items-center gap-4">
                <div class="bg-white p-2.5 rounded-2xl shadow-lg border border-white/20 shrink-0">
                    <img src="{{ asset('images/logo_pmi_sman1ciawi_official.png') }}" alt="Logo PMI SMAN 1 Ciawi" class="h-12 w-auto object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap mb-1">
                        <span class="px-3 py-0.5 rounded-full {{ $levelBadgeClass }} text-[10px] tracking-wider uppercase border shadow-sm">
                            {{ $levelTitle }}
                        </span>
                        <span class="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $isMadya ? 'bg-sky-400' : 'bg-amber-400' }} animate-ping"></span>
                            SISTEM PENILAIAN &bull; OLIMPIADE KEPALANGMERAHAN (3 TAHAP)
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                        Lembar Grid Nilai &amp; Turnamen Olimpiade
                    </h1>
                    <p class="text-xs text-slate-300 mt-1 flex items-center gap-2 flex-wrap">
                        <span>Sua Bhakti Berkarya &bull; PMI SMAN 1 Ciawi</span>
                        <span>&bull;</span>
                        <span class="font-mono text-[11px] {{ $isMadya ? 'text-sky-300' : 'text-amber-300' }} font-bold">Alur Resmi: Penyisihan (Top 9) &rarr; Semifinal (3 Termin) &rarr; Grand Final</span>
                    </p>
                </div>
            </div>

            <!-- Right: Stage Switcher Pills -->
            <div class="flex items-center gap-2 bg-slate-900/80 p-1.5 rounded-2xl shrink-0 border border-white/10 flex-wrap">
                <a href="{{ route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 1]) }}"
                   class="px-4 py-2 rounded-xl font-black text-xs transition flex items-center gap-2 {{ $activeStage === 1 ? ($isMadya ? 'bg-blue-600 text-white shadow-sm' : 'bg-amber-500 text-slate-950 shadow-sm') : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Sesi 1: Penyisihan</span>
                </a>
                <a href="{{ route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 2]) }}"
                   class="px-4 py-2 rounded-xl font-black text-xs transition flex items-center gap-2 {{ $activeStage === 2 ? ($isMadya ? 'bg-blue-600 text-white shadow-sm' : 'bg-amber-500 text-slate-950 shadow-sm') : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                    <i class="fa-solid fa-diagram-project"></i>
                    <span>Sesi 2: Semi-Final</span>
                </a>
                <a href="{{ route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 3]) }}"
                   class="px-4 py-2 rounded-xl font-black text-xs transition flex items-center gap-2 {{ $activeStage === 3 ? ($isMadya ? 'bg-blue-600 text-white shadow-sm' : 'bg-amber-500 text-slate-950 shadow-sm') : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                    <i class="fa-solid fa-trophy"></i>
                    <span>Sesi 3: Grand Final</span>
                </a>
            </div>
        </div>

        <!-- 3-Stage Progress Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 relative z-10">
            <!-- Step 1 Card -->
            <a href="{{ route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 1]) }}"
               class="p-4 rounded-2xl border-2 transition text-left flex items-start gap-3.5 {{ $activeStage === 1 ? ($isMadya ? 'bg-blue-900/50 border-blue-400' : 'bg-amber-900/50 border-amber-400') : 'bg-white/5 border-white/10 hover:border-white/30' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $activeStage === 1 ? ($isMadya ? 'bg-blue-500 text-white' : 'bg-amber-400 text-slate-950') : 'bg-white/10 text-white' }}">
                    1
                </div>
                <div>
                    <div class="font-black text-xs uppercase tracking-wider {{ $activeStage === 1 ? ($isMadya ? 'text-sky-300' : 'text-amber-300') : 'text-slate-200' }}">Sesi 1: Penyisihan (50 Soal)</div>
                    <div class="text-[11px] text-slate-300 font-semibold mt-0.5">Saring 9 Regu Terbaik Lolos Semifinal</div>
                    <div class="text-[10px] mt-1">
                        @if($top9Teams->count() >= 9)
                            <span class="text-emerald-400 font-bold"><i class="fa-solid fa-circle-check"></i> 9 Regu Siap Lolos</span>
                        @else
                            <span class="text-amber-300 font-bold"><i class="fa-solid fa-clock"></i> {{ $top9Teams->count() }} Regu Terdata</span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- Step 2 Card -->
            <a href="{{ route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 2]) }}"
               class="p-4 rounded-2xl border-2 transition text-left flex items-start gap-3.5 {{ $activeStage === 2 ? ($isMadya ? 'bg-blue-900/50 border-blue-400' : 'bg-amber-900/50 border-amber-400') : 'bg-white/5 border-white/10 hover:border-white/30' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $activeStage === 2 ? ($isMadya ? 'bg-blue-500 text-white' : 'bg-amber-400 text-slate-950') : 'bg-white/10 text-white' }}">
                    2
                </div>
                <div>
                    <div class="font-black text-xs uppercase tracking-wider {{ $activeStage === 2 ? ($isMadya ? 'text-sky-300' : 'text-amber-300') : 'text-slate-200' }}">Sesi 2: Semi-Final (3 Termin)</div>
                    <div class="text-[11px] text-slate-300 font-semibold mt-0.5">3 Termin @ 3 Tim (Pemisahan Sekolah)</div>
                    <div class="text-[10px] mt-1">
                        @if($hasSemiResults)
                            <span class="text-emerald-400 font-bold"><i class="fa-solid fa-circle-check"></i> Juara Termin &amp; Harapan Siap</span>
                        @else
                            <span class="text-slate-400">Amplop &bull; Rebutan &bull; Balon</span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- Step 3 Card -->
            <a href="{{ route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 3]) }}"
               class="p-4 rounded-2xl border-2 transition text-left flex items-start gap-3.5 {{ $activeStage === 3 ? ($isMadya ? 'bg-blue-900/50 border-blue-400' : 'bg-amber-900/50 border-amber-400') : 'bg-white/5 border-white/10 hover:border-white/30' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $activeStage === 3 ? ($isMadya ? 'bg-blue-500 text-white' : 'bg-amber-400 text-slate-950') : 'bg-white/10 text-white' }}">
                    3
                </div>
                <div>
                    <div class="font-black text-xs uppercase tracking-wider {{ $activeStage === 3 ? ($isMadya ? 'text-sky-300' : 'text-amber-300') : 'text-slate-200' }}">Sesi 3: Grand Final</div>
                    <div class="text-[11px] text-slate-300 font-semibold mt-0.5">Penentuan Juara 1, 2, dan 3</div>
                    <div class="text-[10px] mt-1">
                        @if($hasFinalResults)
                            <span class="text-amber-300 font-bold"><i class="fa-solid fa-trophy"></i> Juara Resmi Ditetapkan</span>
                        @else
                            <span class="text-slate-400">Rebutan Bertingkat &amp; Taruh Nilai</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    </header>


    {{-- ========================================================================= --}}
    {{-- SESI 1: BABAK PENYISIHAN (50 SOAL OLIMPIADE)                                --}}
    {{-- ========================================================================= --}}
    @if($activeStage === 1)
        <div class="space-y-6">

            <!-- Stage 1 Top Instruction & Smart Bracket Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 shadow-md border border-indigo-900/50 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-slate-950">
                            Sesi 1 (Tahap Penyisihan)
                        </span>
                        <span class="text-xs font-bold text-indigo-200">
                            Penyaringan 9 Regu Terbaik Lolos Semi-Final
                        </span>
                    </div>
                    <h2 class="text-lg font-black text-white">
                        Klasemen Nilai &amp; Waktu Tempuh Babak Penyisihan Olimpiade
                    </h2>
                    <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">
                        Pada babak ini <strong>belum ditentukan juara</strong>. Sistem secara otomatis menyaring <strong>9 regu terbaik (Top 9)</strong> yang berhak masuk ke babak semi-final. Peringkat diurutkan dari <strong>Nilai Akhir tertinggi</strong>, dan jika nilai kembar, <strong>Waktu Tempuh tercepat ([MM]:SS)</strong> menjadi penentu peringkat secara otomatis.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0 flex-wrap">
                    <form action="{{ route('admin.competition-scores.olimpiade-generate-semifinal', $category->id) }}" method="POST" onsubmit="return confirm('Generate bagan semifinal sekarang? 9 tim teratas hasil penyisihan akan didistribusikan ke 3 Termin dengan algoritma anti-bentrok satu sekolah.')">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-diagram-project"></i>
                            <span>Generate Bagan Semifinal (Anti-Bentrokan)</span>
                        </button>
                    </form>

                    <form action="{{ route('admin.competition-scores.olimpiade-reset-stage', $category->id) }}" method="POST" onsubmit="return confirm('Reset seluruh nilai babak penyisihan olimpiade?')">
                        @csrf
                        <input type="hidden" name="stage" value="prelim">
                        <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition flex items-center gap-1.5 cursor-pointer" title="Reset Nilai Penyisihan">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                    </form>
                </div>
            </div>

            <!-- Smart Bracket Info Callout -->
            <div class="bg-amber-50 border-2 border-amber-300 text-amber-950 p-4.5 rounded-2xl flex items-start gap-3.5 shadow-2xs">
                <i class="fa-solid fa-shield-halved text-2xl text-amber-600 shrink-0 mt-0.5"></i>
                <div class="text-xs space-y-1">
                    <div class="font-black text-sm uppercase tracking-wide text-amber-950">
                        Aturan Semifinal: Pemisahan Regu dari Pangkalan Sekolah yang Sama
                    </div>
                    <p class="text-amber-800 leading-relaxed font-medium">
                        Jika ada sekolah yang meloloskan lebih dari 1 regu ke babak semi-final (contoh: <em>SMAN 5 Bandung A &amp; B</em> atau <em>SMAN 1 Cikarang Utara A &amp; B</em>), sistem otomatis mendistribusikan mereka ke <strong>termin yang berbeda</strong> agar tidak saling mengeliminasi di babak semi-final!
                    </p>
                </div>
            </div>

            <!-- ================= FORMULA INFO CALLOUT & STATS CARDS ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                <!-- Formula Banner (7 Cols) -->
                <div class="lg:col-span-7 bg-white rounded-3xl p-5 shadow-sm border border-slate-200/90 flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-2xl {{ $isMadya ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-800' }} flex items-center justify-center text-lg font-black shrink-0">
                                <i class="fa-solid fa-calculator"></i>
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">PANDUAN &amp; FORMULA RESMI PERHITUNGAN NILAI</div>
                                <h3 class="text-sm sm:text-base font-black text-slate-900 leading-tight">
                                    Rumus Nilai Akhir &amp; Regulasi Tie-Breaker (Ranking)
                                </h3>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-xl {{ $accentBgLight }} font-mono font-black text-xs border">
                                Skema 50 Soal
                            </span>
                        </div>
                    </div>

                    <!-- Formula Highlight Box -->
                    <div class="bg-slate-900 text-white rounded-2xl p-3.5 mb-3 border border-slate-800">
                        <div class="flex items-center justify-between flex-wrap gap-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-bold uppercase text-[10px]">FORMULA:</span>
                                <span id="formula-label-display" class="font-mono font-black text-sm {{ $isMadya ? 'text-sky-400' : 'text-amber-400' }}">
                                    Nilai Akhir = (Benar &minus; Salah) &times; 2
                                </span>
                            </div>
                            <div class="flex items-center gap-2 font-mono text-[11px] text-slate-300">
                                <span class="px-2 py-0.5 rounded bg-emerald-900/60 text-emerald-300 border border-emerald-700/60">Benar = +1</span>
                                <span class="px-2 py-0.5 rounded bg-rose-900/60 text-rose-300 border border-rose-700/60">Salah = &minus;1</span>
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">Kosong = 0</span>
                            </div>
                        </div>
                        <div class="mt-2 text-[11px] text-slate-300 flex items-center gap-2 border-t border-slate-800 pt-2">
                            <i class="fa-solid fa-scale-balanced text-amber-400"></i>
                            <span><strong>Aturan Ranking:</strong> Diurutkan dari <strong>Nilai Tertinggi</strong>. Jika nilai sama, peserta dengan <strong>Waktu Tempuh Tercepat</strong> ([MM]:SS) otomatis menempati ranking lebih atas. <strong>Top 9</strong> melaju ke Semifinal.</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium flex-wrap gap-2">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            Kolom "Tidak Dijawab" dihitung otomatis: <code>50 - (Benar + Salah)</code>
                        </span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-slate-400">Pilihan Formula:</span>
                            <select id="select-formula-mode" onchange="changeFormulaMode(this.value)" class="text-[11px] font-bold bg-slate-100 border border-slate-300 rounded-lg px-2 py-0.5 text-slate-800 outline-none">
                                <option value="standard" selected>Resmi: (Benar - Salah) x 2</option>
                                <option value="skema2">Skema 2: 50 + Benar - Salah</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stats Mini Cards (5 Cols) -->
                <div class="lg:col-span-5 grid grid-cols-2 gap-3.5">
                    <div class="bg-white rounded-3xl p-4.5 shadow-sm border border-slate-200/90 flex flex-col justify-between">
                        <div class="flex items-center justify-between text-slate-500 text-xs font-bold mb-1">
                            <span>TOTAL PESERTA</span>
                            <i class="fa-solid fa-users {{ $accentText }}"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-black font-mono text-slate-900">
                                <span id="stat-total-teams">{{ $totalTeamsCount }}</span> <span class="text-xs font-semibold text-slate-500">Regu</span>
                            </div>
                            <div class="text-[10.5px] text-emerald-600 font-bold mt-0.5">● <span id="stat-scored-teams">{{ $scoredCount }}</span> Sudah Dinilai</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-4.5 shadow-sm border border-slate-200/90 flex flex-col justify-between">
                        <div class="flex items-center justify-between text-slate-500 text-xs font-bold mb-1">
                            <span>NILAI TERTINGGI</span>
                            <i class="fa-solid fa-trophy text-amber-500"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-black font-mono {{ $isMadya ? 'text-blue-600' : 'text-amber-600' }}">
                                <span id="stat-highest-score">{{ $highestScore }}</span> <span class="text-xs font-semibold text-slate-500">Poin</span>
                            </div>
                            <div id="stat-highest-team" class="text-[10.5px] text-slate-500 truncate font-semibold mt-0.5">
                                {{ $highestTeam ? ($highestTeam->registration?->school_name ?? $highestTeam->team_name) : '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-4.5 shadow-sm border border-slate-200/90 flex flex-col justify-between">
                        <div class="flex items-center justify-between text-slate-500 text-xs font-bold mb-1">
                            <span>RATA-RATA NILAI</span>
                            <i class="fa-solid fa-chart-simple text-indigo-500"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-black font-mono text-slate-900">
                                <span id="stat-avg-score">{{ $avgScore }}</span> <span class="text-xs font-semibold text-slate-500">Poin</span>
                            </div>
                            <div class="text-[10.5px] text-slate-500 font-semibold mt-0.5">Skala Nilai 0 &minus; 100</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-4.5 shadow-sm border border-slate-200/90 flex flex-col justify-between">
                        <div class="flex items-center justify-between text-slate-500 text-xs font-bold mb-1">
                            <span>WAKTU TERCEPAT</span>
                            <i class="fa-solid fa-bolt text-rose-500"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-black font-mono text-rose-600">
                                <span id="stat-fastest-time">{{ $fastestTime }}</span> <span class="text-xs font-semibold text-slate-500">MM:SS</span>
                            </div>
                            <div id="stat-fastest-team" class="text-[10.5px] text-slate-500 truncate font-semibold mt-0.5">
                                {{ $fastestTeam ? ($fastestTeam->registration?->school_name ?? $fastestTeam->team_name) : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= FORM & MAIN TABLE GRID CONTAINER ================= -->
            <form id="olimpiade-form" action="{{ route('admin.competition-scores.olimpiade-save', $category->id) }}" method="POST">
                @csrf
                <input type="hidden" name="formula_type" id="input-formula-type" value="standard">

                <div class="bg-white rounded-3xl shadow-md border border-slate-200 overflow-hidden">
                    
                    <!-- Table Toolbar -->
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full {{ $accentBar }} inline-block"></span>
                            <div>
                                <h2 class="font-black text-sm sm:text-base text-slate-900">
                                    Lembar Kerja Penginputan &amp; Klasemen Olimpiade
                                </h2>
                                <p class="text-[11px] text-slate-500 font-medium">Input jumlah jawaban benar, salah, dan waktu tempuh. Sistem otomatis mengurutkan klasemen dan menyaring 9 besar semifinal.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" id="filter-search" oninput="filterTableRows(this.value)" placeholder="Cari nama sekolah / regu..." class="pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 text-xs bg-white text-slate-900 outline-none w-48 sm:w-60 focus:border-blue-500">
                            </div>
                            <button type="button" onclick="sortRowsByRank()" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-arrow-down-short-wide"></i> Urutkan Ranking
                            </button>
                        </div>
                    </div>

                    <!-- Table Itself: Strictly starts with NO URUT, and far right is RANKING -->
                    <div class="overflow-x-auto">
                        <table id="olimpiade-table" class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white uppercase text-[10.5px] tracking-wider font-black select-none">
                                    <th class="py-4 px-3.5 text-center w-20 border-r border-slate-800">NO URUT</th>
                                    <th class="py-4 px-4 border-r border-slate-800 min-w-[260px]">NAMA PANGKALAN SEKOLAH / REGU</th>
                                    <th class="py-4 px-3 text-center border-r border-slate-800 w-36 bg-emerald-950/60 text-emerald-300">
                                        <div class="flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-check"></i> BENAR (+1)
                                        </div>
                                    </th>
                                    <th class="py-4 px-3 text-center border-r border-slate-800 w-36 bg-rose-950/60 text-rose-300">
                                        <div class="flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-xmark"></i> SALAH (&minus;1)
                                        </div>
                                    </th>
                                    <th class="py-4 px-3 text-center border-r border-slate-800 w-28 bg-slate-800 text-slate-300">
                                        KOSONG (0)
                                    </th>
                                    <th class="py-4 px-3 text-center border-r border-slate-800 w-36 bg-slate-900 text-amber-300">
                                        <div class="flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-stopwatch"></i> WAKTU (MM:SS)
                                        </div>
                                    </th>
                                    <th class="py-4 px-3 text-center border-r border-slate-800 w-32 {{ $isMadya ? 'bg-blue-950 text-sky-300' : 'bg-amber-950 text-amber-300' }}">
                                        NILAI AKHIR
                                    </th>
                                    <th class="py-4 px-4 text-center w-56 bg-black text-slate-200">
                                        RANKING
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="olimpiade-tbody" class="divide-y divide-slate-100 font-medium bg-white">
                                @forelse($sortedTeams as $idx => $t)
                                    @php
                                        $rVal = $t->rank;
                                        $bVal = $t->benar ?? 0;
                                        $sVal = $t->salah ?? 0;
                                        $kVal = $t->kosong ?? 50;
                                        $timeVal = $t->waktu ?: '00:00';
                                        $scoreVal = $t->final_score !== null ? $t->final_score : 0;
                                        $scoreAltVal = $t->score_alt !== null ? $t->score_alt : 50;
                                        $isQualified = ($rVal && $rVal <= 9);

                                        $rowHighlight = 'hover:bg-slate-50/80';
                                        if ($rVal === 1) {
                                            $rowHighlight = 'bg-amber-500/5 hover:bg-amber-500/10 border-l-4 border-l-amber-500';
                                        } elseif ($rVal === 2) {
                                            $rowHighlight = 'bg-slate-500/5 hover:bg-slate-500/10 border-l-4 border-l-slate-400';
                                        } elseif ($rVal === 3) {
                                            $rowHighlight = 'bg-amber-700/5 hover:bg-amber-700/10 border-l-4 border-l-amber-700';
                                        } elseif ($isQualified) {
                                            $rowHighlight = 'bg-emerald-500/5 hover:bg-emerald-500/10 border-l-4 border-l-emerald-500';
                                        } else {
                                            $rowHighlight = 'border-l-4 border-l-transparent hover:bg-slate-50/80';
                                        }
                                    @endphp
                                    <tr id="row-team-{{ $t->id }}" 
                                        class="team-row transition-colors duration-150 border-b border-slate-100 {{ $rowHighlight }}"
                                        data-team-id="{{ $t->id }}"
                                        data-school="{{ strtolower($t->registration?->school_name ?? $t->team_name) }}"
                                        data-order="{{ $t->order_number ?? $idx + 1 }}"
                                        data-score="{{ $scoreVal }}"
                                        data-time-seconds="{{ $t->time_seconds }}">
                                        
                                        <!-- 1. NO URUT -->
                                        <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 font-mono font-bold text-xs text-slate-700">
                                                {{ $t->order_number ?: sprintf('%02d', $idx + 1) }}
                                            </span>
                                        </td>

                                        <!-- 2. NAMA SEKOLAH & REGU -->
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-slate-900 text-xs sm:text-sm leading-tight flex items-center gap-1.5">
                                                {{ $t->registration?->school_name ?? $t->team_name }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-2">
                                                <span class="text-slate-600 font-semibold">{{ $t->team_label ?: $t->team_name }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="font-mono text-[10px] text-slate-400">ID: OLIM-{{ $t->id }}</span>
                                            </div>
                                        </td>

                                        <!-- 3. BENAR (1) -->
                                        <td class="py-3 px-3 text-center">
                                            <div class="inline-flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 shadow-2xs">
                                                <button type="button" onclick="adjustScoreInput('benar_{{ $t->id }}', -1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center cursor-pointer">-</button>
                                                <input type="number" 
                                                       id="benar_{{ $t->id }}" 
                                                       name="benar[{{ $t->id }}]" 
                                                       value="{{ $t->has_score ? $bVal : '' }}" 
                                                       min="0" 
                                                       max="50" 
                                                       placeholder="0"
                                                       oninput="handleRowChange({{ $t->id }})" 
                                                       class="input-benar w-12 text-center font-mono font-black text-sm text-emerald-700 bg-emerald-50/50 rounded-lg py-1 border border-emerald-200 outline-none {{ $activeInputBorder }}">
                                                <button type="button" onclick="adjustScoreInput('benar_{{ $t->id }}', 1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center cursor-pointer">+</button>
                                            </div>
                                        </td>

                                        <!-- 4. SALAH (-1) -->
                                        <td class="py-3 px-3 text-center">
                                            <div class="inline-flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 shadow-2xs">
                                                <button type="button" onclick="adjustScoreInput('salah_{{ $t->id }}', -1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center cursor-pointer">-</button>
                                                <input type="number" 
                                                       id="salah_{{ $t->id }}" 
                                                       name="salah[{{ $t->id }}]" 
                                                       value="{{ $t->has_score ? $sVal : '' }}" 
                                                       min="0" 
                                                       max="50" 
                                                       placeholder="0"
                                                       oninput="handleRowChange({{ $t->id }})" 
                                                       class="input-salah w-12 text-center font-mono font-black text-sm text-rose-700 bg-rose-50/50 rounded-lg py-1 border border-rose-200 outline-none {{ $activeInputBorder }}">
                                                <button type="button" onclick="adjustScoreInput('salah_{{ $t->id }}', 1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center cursor-pointer">+</button>
                                            </div>
                                        </td>

                                        <!-- 5. TIDAK DIJAWAB (0) -->
                                        <td class="py-3 px-3 text-center">
                                            <span id="kosong_{{ $t->id }}" class="inline-flex items-center justify-center w-10 py-1 rounded-lg bg-slate-100 border border-slate-200 font-mono font-bold text-xs text-slate-600" title="Otomatis: 50 - (Benar + Salah)">
                                                {{ $t->has_score ? $kVal : 50 }}
                                            </span>
                                        </td>

                                        <!-- 6. WAKTU TEMPUH (MM:SS) -->
                                        <td class="py-3 px-3 text-center">
                                            <div class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200 px-2 py-1 rounded-xl">
                                                <i class="fa-solid fa-stopwatch text-slate-400 text-xs"></i>
                                                <input type="text" 
                                                       id="waktu_{{ $t->id }}" 
                                                       name="waktu[{{ $t->id }}]" 
                                                       value="{{ $t->waktu ?: '' }}" 
                                                       placeholder="00:00" 
                                                       oninput="handleTimeChange({{ $t->id }}, this.value)"
                                                       class="input-waktu w-16 text-center font-mono font-bold text-xs text-slate-900 bg-transparent outline-none">
                                            </div>
                                        </td>

                                        <!-- 7. NILAI AKHIR -->
                                        <td class="py-3 px-3 text-center">
                                            <div class="inline-flex flex-col items-center">
                                                <span id="score-display-{{ $t->id }}" class="font-mono font-black text-base {{ $scoreTextColor }} bg-white px-3 py-1 rounded-xl border border-slate-200 shadow-xs">
                                                    {{ $t->has_score ? $scoreVal : '-' }}
                                                </span>
                                                <span id="score-alt-{{ $t->id }}" class="text-[9px] font-mono text-slate-400 mt-0.5">
                                                    Skema 2: {{ $t->has_score ? $scoreAltVal : '-' }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- 8. RANKING (Menggantikan Predikat Juara & Menampilkan Status Kelolosan Top 9) -->
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <div id="badge-container-{{ $t->id }}">
                                                @if($t->has_score && $rVal)
                                                    @if($rVal === 1)
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 font-black text-xs shadow-2xs">
                                                            <i class="fa-solid fa-crown text-amber-600"></i> Peringkat 1 &bull; Lolos Semifinal
                                                        </span>
                                                    @elseif($rVal === 2)
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-200 text-slate-900 border border-slate-300 font-black text-xs shadow-2xs">
                                                            <i class="fa-solid fa-award text-slate-600"></i> Peringkat 2 &bull; Lolos Semifinal
                                                        </span>
                                                    @elseif($rVal === 3)
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-950 border border-amber-400 font-black text-xs shadow-2xs">
                                                            <i class="fa-solid fa-award text-amber-700"></i> Peringkat 3 &bull; Lolos Semifinal
                                                        </span>
                                                    @elseif($rVal <= 9)
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-900 border border-emerald-300 font-black text-xs shadow-2xs">
                                                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Peringkat {{ $rVal }} &bull; Lolos Semifinal
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 font-bold text-xs">
                                                            <i class="fa-solid fa-circle-xmark text-slate-400"></i> Peringkat {{ $rVal }} &bull; Belum Lolos
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-100 text-slate-400 font-semibold text-xs border border-slate-200">
                                                        Belum Dinilai
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-12 text-center text-slate-400">
                                            <i class="fa-solid fa-users-slash text-3xl mb-2 block"></i>
                                            Belum ada tim peserta yang terdaftar pada kategori Olimpiade ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer Summary -->
                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600 font-medium">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Menampilkan <strong>{{ $allTeams->count() }} Peserta</strong> Olimpiade {{ $levelTitle }}</span>
                        </div>
                        <div class="flex items-center gap-2 font-mono text-[11px] text-slate-400">
                            <i class="fa-solid fa-shield-halved text-slate-400"></i>
                            Sistem Perhitungan Otomatis: Nilai Tertinggi &rarr; Waktu Tercepat
                        </div>
                    </div>

                </div>

                <!-- ================= STICKY BOTTOM SAVE ACTION BAR ================= -->
                <div class="sticky bottom-6 z-40 bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 shadow-2xl border border-slate-800 text-white flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl {{ $isMadya ? 'bg-blue-600' : 'bg-amber-500' }} flex items-center justify-center text-white {{ ! $isMadya ? 'text-slate-950' : '' }} font-black text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <div class="text-xs font-black text-white">Status Penyimpanan: Siap Disimpan</div>
                            <div class="text-[11px] text-slate-400">Perubahan nilai &amp; waktu tempuh akan langsung memperbarui klasemen ranking penyisihan.</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" onclick="location.reload()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition cursor-pointer">
                            Batalkan Perubahan
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl {{ $saveBtnClass }} font-black text-xs transition shadow-lg flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan &amp; Perbarui Klasemen Peringkat
                        </button>
                    </div>
                </div>
            </form>

        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- SESI 2: BABAK SEMI-FINAL (3 TERMIN @ 3 TIM)                               --}}
    {{-- ========================================================================= --}}
    @if($activeStage === 2)
        <div class="space-y-6">

            <!-- Stage 2 Top Instruction Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 shadow-md border border-indigo-900/50 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-500 text-white">
                            Sesi 2 (Babak Semi-Final)
                        </span>
                        <span class="text-xs font-bold text-indigo-200">
                            3 Termin Pertandingan @ 3 Regu (Format Cepat Tepat)
                        </span>
                    </div>
                    <h2 class="text-lg font-black text-white">
                        Penilaian Meja Semifinal: Soal Amplop, Rebutan, dan Balon
                    </h2>
                    <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">
                        Masing-masing termin bertanding 3 regu. Pemenang (Juara 1) tiap termin otomatis memegang tiket ke <strong>Babak Grand Final</strong>. Sedangkan <strong>Juara Harapan 1 dan 2</strong> ditentukan dari skor tertinggi seluruh peserta semi-final non-finalis.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0 flex-wrap">
                    <a href="{{ route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 3]) }}" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2">
                        <span>Lanjut ke Grand Final</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                    <form action="{{ route('admin.competition-scores.olimpiade-reset-stage', $category->id) }}" method="POST" onsubmit="return confirm('Reset seluruh nilai babak semifinal?')">
                        @csrf
                        <input type="hidden" name="stage" value="semifinal">
                        <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition flex items-center gap-1.5 cursor-pointer" title="Reset Nilai Semifinal">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form Simpan Nilai Semifinal (3 Termin) -->
            <form action="{{ route('admin.competition-scores.olimpiade-save-semifinal', $category->id) }}" method="POST">
                @csrf
                <div class="space-y-6">

                    @php
                        $terminConfigs = [
                            1 => ['title' => 'Termin 1 (Sesi Pagi / Meja 1)', 'teams' => $termin1Teams, 'color' => 'border-amber-400 bg-amber-50/20', 'headerBg' => 'bg-amber-400 text-slate-950', 'tiket' => 'Tiket Final A'],
                            2 => ['title' => 'Termin 2 (Sesi Siang / Meja 2)', 'teams' => $termin2Teams, 'color' => 'border-rose-400 bg-rose-50/20', 'headerBg' => 'bg-rose-500 text-white', 'tiket' => 'Tiket Final B'],
                            3 => ['title' => 'Termin 3 (Sesi Sore / Meja 3)', 'teams' => $termin3Teams, 'color' => 'border-emerald-400 bg-emerald-50/20', 'headerBg' => 'bg-emerald-600 text-white', 'tiket' => 'Tiket Final C'],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 gap-6">
                        @foreach($terminConfigs as $tNum => $conf)
                            <div class="bg-white rounded-3xl shadow-sm border {{ $conf['color'] }} overflow-hidden">
                                <div class="p-4 {{ $conf['headerBg'] }} flex items-center justify-between gap-3 flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-black/20 flex items-center justify-center font-black text-xs">
                                            T{{ $tNum }}
                                        </span>
                                        <span class="font-black text-sm uppercase tracking-wider">
                                            {{ $conf['title'] }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-wider px-2.5 py-1 rounded-lg bg-white/20">
                                        Juara 1 Lolos &rarr; {{ $conf['tiket'] }}
                                    </span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                                                <th class="py-3 px-4 w-16 text-center border-r border-slate-800">NO</th>
                                                <th class="py-3 px-5 border-r border-slate-800">NAMA SEKOLAH / REGU</th>
                                                <th class="py-3 px-4 text-center border-r border-slate-800 w-44">TOTAL NILAI SOAL AMPLOP</th>
                                                <th class="py-3 px-4 text-center border-r border-slate-800 w-48">TOTAL NILAI SOAL REBUTAN</th>
                                                <th class="py-3 px-4 text-center border-r border-slate-800 w-40">TOTAL NILAI BALON</th>
                                                <th class="py-3 px-4 text-center border-r border-slate-800 w-36 bg-slate-950">POIN TOTAL</th>
                                                <th class="py-3 px-4 text-center w-44 bg-black">RANKING / STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 font-medium">
                                            @forelse($conf['teams'] as $tIdx => $t)
                                                @php
                                                    $isWinner = ($t->termin_rank == 1);
                                                    $rowBg = $isWinner ? 'bg-emerald-50/70 font-bold border-l-4 border-l-emerald-500' : 'hover:bg-slate-50 border-l-4 border-l-transparent';
                                                @endphp
                                                <tr class="{{ $rowBg }} transition" id="row-semi-{{ $t->id }}" data-termin="{{ $tNum }}">
                                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800">
                                                        <input type="hidden" name="semi_termin[{{ $t->id }}]" value="{{ $tNum }}">
                                                        {{ $tIdx + 1 }}
                                                    </td>
                                                    <td class="py-3.5 px-5">
                                                        <div class="font-black text-slate-900 text-sm">
                                                            {{ $t->team_name ?? ($t->registration?->school_name ?? 'Regu ' . ($tIdx + 1)) }}
                                                        </div>
                                                        <div class="text-[11px] text-slate-500 font-medium">
                                                            Pangkalan: {{ $t->registration?->school_name ?? '-' }}
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center">
                                                        <input type="number" step="any" name="semi_amplop[{{ $t->id }}]" id="inp-amplop-{{ $t->id }}" value="{{ $t->soal_amplop ?? 0 }}" oninput="calcSemiRow({{ $t->id }}, {{ $tNum }})" class="w-24 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-indigo-500" data-termin="{{ $tNum }}">
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <input type="number" step="any" name="semi_rebutan[{{ $t->id }}]" id="inp-rebutan-{{ $t->id }}" value="{{ $t->rebutan ?? 0 }}" oninput="calcSemiRow({{ $t->id }}, {{ $tNum }})" class="w-24 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-indigo-500" data-termin="{{ $tNum }}">
                                                            <div class="flex flex-col gap-0.5">
                                                                <button type="button" onclick="adjustInput('inp-rebutan-{{ $t->id }}', 100, {{ $t->id }}, {{ $tNum }})" class="px-1.5 py-0.5 text-[9px] font-black rounded bg-emerald-100 text-emerald-800 hover:bg-emerald-200 shadow-2xs cursor-pointer">+100</button>
                                                                <button type="button" onclick="adjustInput('inp-rebutan-{{ $t->id }}', -50, {{ $t->id }}, {{ $tNum }})" class="px-1.5 py-0.5 text-[9px] font-black rounded bg-rose-100 text-rose-800 hover:bg-rose-200 shadow-2xs cursor-pointer">-50</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center">
                                                        <input type="number" step="any" name="semi_balon[{{ $t->id }}]" id="inp-balon-{{ $t->id }}" value="{{ $t->balon ?? 0 }}" oninput="calcSemiRow({{ $t->id }}, {{ $tNum }})" class="w-24 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-indigo-500" data-termin="{{ $tNum }}">
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center font-mono font-black text-base text-indigo-900 bg-indigo-50/50">
                                                        <span id="display-total-{{ $t->id }}">{{ number_format($t->total_score ?? 0, 0, ',', '.') }}</span>
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center font-bold" id="display-status-{{ $t->id }}">
                                                        @if($isWinner)
                                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-black uppercase tracking-wider shadow-2xs">
                                                                <i class="fa-solid fa-ticket text-emerald-600"></i> Lolos Final
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                                                                Peringkat {{ $t->termin_rank ?? ($tIdx + 1) }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="py-6 text-center text-slate-400 font-bold">
                                                        Belum ada regu pada termin ini. Klik "Generate Bagan Semifinal" di Tahap 1.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-white rounded-2xl p-4 border border-slate-200 flex items-center justify-between gap-3 flex-wrap">
                        <div class="text-xs text-slate-600 font-medium">
                            * Nilai total otomatis terhitung. Tekan tombol simpan untuk memperbarui tiket Final dan Juara Harapan.
                        </div>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Nilai Semifinal (Semua Termin)</span>
                        </button>
                    </div>

                </div>
            </form>

            <!-- Table: Kompilasi 6 Tim Non-Finalis (Penentuan Juara Harapan 1 & 2) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mt-6">
                <div class="p-5 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <div class="text-xs font-bold text-amber-700 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-award text-amber-500"></i> Akumulasi Poin Semifinal Non-Finalis
                        </div>
                        <h3 class="text-sm font-black text-slate-900 mt-0.5">
                            Penetapan Juara Harapan 1 &amp; Juara Harapan 2 (Sesuai Aturan Resmi)
                        </h3>
                    </div>
                    <span class="text-xs font-medium text-slate-500">
                        Diambil dari skor tertinggi dari seluruh termin semi-final (di luar 3 finalis)
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                                <th class="py-3 px-4 w-16 text-center border-r border-slate-800">NO</th>
                                <th class="py-3 px-5 border-r border-slate-800">NAMA SEKOLAH / REGU</th>
                                <th class="py-3 px-4 text-center border-r border-slate-800 w-36">TERMIN ASAL</th>
                                <th class="py-3 px-4 text-center border-r border-slate-800 w-44">POIN TOTAL SEMIFINAL</th>
                                <th class="py-3 px-4 text-center w-48 bg-black">STATUS PREDIKAT JUARA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-medium">
                            @forelse($sortedNonFinalists as $nIdx => $nt)
                                @php
                                    $isH1 = ($nIdx === 0);
                                    $isH2 = ($nIdx === 1);
                                    $rowClass = $isH1 ? 'bg-amber-50/80 font-bold' : ($isH2 ? 'bg-amber-50/40 font-bold' : '');
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800">
                                        {{ $nIdx + 1 }}
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-black text-slate-900 text-sm">
                                            {{ $nt->team_name ?? ($nt->registration?->school_name ?? '-') }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                        Termin {{ $nt->termin ?? '-' }} (Peringkat {{ $nt->termin_rank }})
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-black text-sm text-slate-900">
                                        {{ number_format($nt->total_score, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($isH1)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-stone-950 font-black text-xs shadow-2xs border border-yellow-300">
                                                <i class="fa-solid fa-trophy"></i> Juara Harapan 1
                                            </span>
                                        @elseif($isH2)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-100 text-amber-900 font-black text-xs shadow-2xs border border-amber-300">
                                                <i class="fa-solid fa-medal"></i> Juara Harapan 2
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-500">
                                                Peringkat {{ $nt->overall_rank ?? ($nIdx + 4) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 font-bold">
                                        Data nilai semifinal belum diisi. Lengkapi nilai di ketiga termin di atas lalu simpan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- SESI 3: BABAK GRAND FINAL                                                 --}}
    {{-- ========================================================================= --}}
    @if($activeStage === 3)
        <div class="space-y-6">

            <!-- Stage 3 Top Instruction Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-amber-950 to-slate-900 text-white rounded-3xl p-6 shadow-md border border-amber-900/50 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950">
                            Sesi 3 (Grand Final)
                        </span>
                        <span class="text-xs font-bold text-amber-200">
                            Perebutan Juara 1, 2, dan 3 Olimpiade
                        </span>
                    </div>
                    <h2 class="text-lg font-black text-white">
                        Babak Puncak: Soal Amplop, Rebutan Bertingkat, dan Taruh Nilai
                    </h2>
                    <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">
                        Diikuti oleh <strong>3 regu pemenang masing-masing termin semi-final</strong>. Babak ini menentukan Juara 1 (Emas), Juara 2 (Perak), dan Juara 3 (Perunggu). Juara Harapan 1 dan 2 otomatis dirangkum dari hasil semifinal.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0 flex-wrap">
                    <form action="{{ route('admin.competition-scores.olimpiade-reset-stage', $category->id) }}" method="POST" onsubmit="return confirm('Reset nilai babak final?')">
                        @csrf
                        <input type="hidden" name="stage" value="final">
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-rotate-left"></i> Reset Final
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form Simpan Nilai Grand Final -->
            <form action="{{ route('admin.competition-scores.olimpiade-save-final', $category->id) }}" method="POST">
                @csrf
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-trophy text-amber-500 text-base"></i>
                            <span class="font-black text-xs sm:text-sm text-slate-900">
                                Lembar Nilai Grand Final (3 Regu Puncak)
                            </span>
                        </div>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Nilai Grand Final</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                                    <th class="py-3.5 px-4 w-16 text-center border-r border-slate-800">NO</th>
                                    <th class="py-3.5 px-5 border-r border-slate-800">NAMA SEKOLAH / REGU FINALIS</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-44">TOTAL NILAI SOAL AMPLOP</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-52">TOTAL NILAI REBUTAN BERTINGKAT</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-44">TOTAL NILAI TARUH NILAI</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-40 bg-slate-950">POIN TOTAL</th>
                                    <th class="py-3.5 px-4 text-center w-48 bg-black">PERINGKAT JUARA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-medium">
                                @forelse($finalGrandTeams as $fIdx => $ft)
                                    @php
                                        $fRank = $ft->grand_rank;
                                        $hasScore = ($ft->total_score > 0);
                                        $isJuara1 = ($fRank === 1 && $hasScore);
                                        $isJuara2 = ($fRank === 2 && $hasScore);
                                        $isJuara3 = ($fRank === 3 && $hasScore);
                                        $teamName = $ft->team_name ?? ($ft->registration?->school_name ?? 'Finalis ' . ($fIdx + 1));
                                    @endphp
                                    <tr class="{{ $isJuara1 ? 'bg-amber-50/70 font-bold border-l-4 border-l-amber-500' : ($isJuara2 ? 'bg-slate-100/60 font-bold border-l-4 border-l-slate-400' : ($isJuara3 ? 'bg-amber-50/40 font-bold border-l-4 border-l-amber-700' : 'hover:bg-slate-50 border-l-4 border-l-transparent')) }} transition" id="row-final-{{ $ft->id }}" data-team-id="{{ $ft->id }}" data-team-name="{{ $teamName }}">
                                        <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800" id="final-no-{{ $ft->id }}">
                                            {{ $fIdx + 1 }}
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <div class="font-black text-slate-900 text-sm">
                                                {{ $teamName }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-medium">
                                                Pangkalan: {{ $ft->registration?->school_name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <input type="number" step="any" name="final_amplop[{{ $ft->id }}]" id="inp-final-amplop-{{ $ft->id }}" value="{{ $ft->soal_amplop ?? 0 }}" oninput="calcFinalRow({{ $ft->id }})" class="w-28 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-amber-500 final-score-input" data-team-id="{{ $ft->id }}">
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <input type="number" step="any" name="final_rebutan[{{ $ft->id }}]" id="inp-final-rebutan-{{ $ft->id }}" value="{{ $ft->rebutan_bertingkat ?? 0 }}" oninput="calcFinalRow({{ $ft->id }})" class="w-28 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-amber-500 final-score-input" data-team-id="{{ $ft->id }}">
                                                <div class="flex flex-col gap-0.5">
                                                    <button type="button" onclick="adjustFinalInput('inp-final-rebutan-{{ $ft->id }}', 100, {{ $ft->id }})" class="px-1.5 py-0.5 text-[9px] font-black rounded bg-emerald-100 text-emerald-800 hover:bg-emerald-200 shadow-2xs cursor-pointer">+100</button>
                                                    <button type="button" onclick="adjustFinalInput('inp-final-rebutan-{{ $ft->id }}', -50, {{ $ft->id }})" class="px-1.5 py-0.5 text-[9px] font-black rounded bg-rose-100 text-rose-800 hover:bg-rose-200 shadow-2xs cursor-pointer">-50</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <input type="number" step="any" name="final_taruh[{{ $ft->id }}]" id="inp-final-taruh-{{ $ft->id }}" value="{{ $ft->taruh_nilai ?? 0 }}" oninput="calcFinalRow({{ $ft->id }})" class="w-28 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-amber-500 final-score-input" data-team-id="{{ $ft->id }}">
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-mono font-black text-lg text-amber-950 bg-amber-50/50">
                                            <span id="display-final-total-{{ $ft->id }}">{{ number_format($ft->total_score ?? 0, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold" id="display-final-status-{{ $ft->id }}">
                                            @if($isJuara1)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-stone-950 font-black text-xs shadow-2xs border border-yellow-300">
                                                    🥇 Juara 1
                                                </span>
                                            @elseif($isJuara2)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-slate-200 to-slate-300 text-slate-800 font-black text-xs shadow-2xs border border-slate-400">
                                                    🥈 Juara 2
                                                </span>
                                            @elseif($isJuara3)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-amber-700 to-yellow-800 text-amber-100 font-black text-xs shadow-2xs border border-amber-600">
                                                    🥉 Juara 3
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-500 text-xs font-semibold border border-slate-200">
                                                    Belum Dinilai
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400 font-bold">
                                            Belum ada finalis. Selesaikan dan simpan penilaian di Sesi 2 (Semi-Final) terlebih dahulu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3 flex-wrap">
                        <div class="text-xs text-slate-500 font-medium">
                            * Babak Taruh Nilai: Peserta mempertaruhkan poin strategis sesuai ketentuan dewan juri.
                        </div>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Nilai Grand Final</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Grand Podium & Final Summary Cards -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-6">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-500"></i>
                        <span>Podium Kejuaraan Resmi Olimpiade Kepalangmerahan</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar juara resmi berdasarkan akumulasi babak Grand Final dan babak Semifinal.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Juara 1 -->
                    <div class="p-5 rounded-3xl bg-gradient-to-b from-amber-50 to-amber-100/60 border-2 border-amber-300 relative overflow-hidden flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-xl bg-amber-400 text-slate-950 font-black text-xs shadow-2xs">JUARA 1 (EMAS)</span>
                            <span class="text-3xl">🥇</span>
                        </div>
                        <div class="my-4">
                            @php $j1 = $finalGrandTeams->firstWhere('grand_rank', 1); @endphp
                            <div class="font-black text-lg text-slate-900">
                                {{ $j1 ? ($j1->team_name ?? ($j1->registration?->school_name ?? '-')) : 'Menunggu Grand Final' }}
                            </div>
                            <div class="text-xs font-semibold text-slate-600 mt-1">
                                Pangkalan: {{ $j1 ? ($j1->registration?->school_name ?? '-') : '-' }}
                            </div>
                        </div>
                        <div class="pt-3 border-t border-amber-200/80 flex items-center justify-between">
                            <span class="text-xs text-amber-900 font-bold">Total Poin Final</span>
                            <span class="font-mono font-black text-lg text-amber-950">{{ $j1 ? number_format($j1->total_score, 0, ',', '.') : '-' }}</span>
                        </div>
                    </div>

                    <!-- Juara 2 -->
                    <div class="p-5 rounded-3xl bg-gradient-to-b from-slate-50 to-slate-100/60 border-2 border-slate-300 relative overflow-hidden flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-xl bg-slate-300 text-slate-900 font-black text-xs shadow-2xs">JUARA 2 (PERAK)</span>
                            <span class="text-3xl">🥈</span>
                        </div>
                        <div class="my-4">
                            @php $j2 = $finalGrandTeams->firstWhere('grand_rank', 2); @endphp
                            <div class="font-black text-lg text-slate-900">
                                {{ $j2 ? ($j2->team_name ?? ($j2->registration?->school_name ?? '-')) : 'Menunggu Grand Final' }}
                            </div>
                            <div class="text-xs font-semibold text-slate-600 mt-1">
                                Pangkalan: {{ $j2 ? ($j2->registration?->school_name ?? '-') : '-' }}
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                            <span class="text-xs text-slate-700 font-bold">Total Poin Final</span>
                            <span class="font-mono font-black text-lg text-slate-900">{{ $j2 ? number_format($j2->total_score, 0, ',', '.') : '-' }}</span>
                        </div>
                    </div>

                    <!-- Juara 3 -->
                    <div class="p-5 rounded-3xl bg-gradient-to-b from-amber-50/50 to-amber-100/40 border-2 border-amber-600/40 relative overflow-hidden flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-xl bg-amber-700 text-white font-black text-xs shadow-2xs">JUARA 3 (PERUNGGU)</span>
                            <span class="text-3xl">🥉</span>
                        </div>
                        <div class="my-4">
                            @php $j3 = $finalGrandTeams->firstWhere('grand_rank', 3); @endphp
                            <div class="font-black text-lg text-slate-900">
                                {{ $j3 ? ($j3->team_name ?? ($j3->registration?->school_name ?? '-')) : 'Menunggu Grand Final' }}
                            </div>
                            <div class="text-xs font-semibold text-slate-600 mt-1">
                                Pangkalan: {{ $j3 ? ($j3->registration?->school_name ?? '-') : '-' }}
                            </div>
                        </div>
                        <div class="pt-3 border-t border-amber-600/20 flex items-center justify-between">
                            <span class="text-xs text-amber-900 font-bold">Total Poin Final</span>
                            <span class="font-mono font-black text-lg text-amber-950">{{ $j3 ? number_format($j3->total_score, 0, ',', '.') : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Juara Harapan 1 & 2 Cards -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Juara Harapan (Dari Semifinal):</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-black text-[10px] uppercase">JUARA HARAPAN 1</span>
                                <div class="font-black text-sm text-slate-900 mt-1">
                                    {{ $harapan1Team ? ($harapan1Team->team_name ?? ($harapan1Team->registration?->school_name ?? '-')) : '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500">Pangkalan: {{ $harapan1Team ? ($harapan1Team->registration?->school_name ?? '-') : '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] text-slate-400 font-semibold">Poin Semifinal</div>
                                <div class="font-mono font-black text-base text-slate-900">{{ $harapan1Team ? number_format($harapan1Team->total_score, 0, ',', '.') : '-' }}</div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded-md bg-slate-200 text-slate-800 font-black text-[10px] uppercase">JUARA HARAPAN 2</span>
                                <div class="font-black text-sm text-slate-900 mt-1">
                                    {{ $harapan2Team ? ($harapan2Team->team_name ?? ($harapan2Team->registration?->school_name ?? '-')) : '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500">Pangkalan: {{ $harapan2Team ? ($harapan2Team->registration?->school_name ?? '-') : '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] text-slate-400 font-semibold">Poin Semifinal</div>
                                <div class="font-mono font-black text-base text-slate-900">{{ $harapan2Team ? number_format($harapan2Team->total_score, 0, ',', '.') : '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif

</div>

@push('scripts')
<script>
    let currentFormulaMode = 'standard'; // 'standard' = (B - S) * 2 | 'skema2' = 50 + B - S

    function changeFormulaMode(mode) {
        currentFormulaMode = mode;
        const inputFormula = document.getElementById('input-formula-type');
        if (inputFormula) inputFormula.value = mode;

        const label = document.getElementById('formula-label-display');
        if (label) {
            if (mode === 'skema2') {
                label.innerHTML = 'Nilai Akhir = 50 + Benar &minus; Salah';
            } else {
                label.innerHTML = 'Nilai Akhir = (Benar &minus; Salah) &times; 2';
            }
        }

        // Recalculate all rows
        document.querySelectorAll('.team-row').forEach(row => {
            const teamId = row.getAttribute('data-team-id');
            calculateRowScore(teamId);
        });

        sortRowsByRank();
    }

    function adjustScoreInput(inputId, delta) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let cur = parseInt(input.value) || 0;
        let next = Math.max(0, Math.min(50, cur + delta));
        input.value = next;
        
        const teamId = inputId.split('_')[1];
        handleRowChange(teamId);
    }

    function handleRowChange(teamId) {
        calculateRowScore(teamId);
        sortRowsByRank();
    }

    function calculateRowScore(teamId) {
        const bInput = document.getElementById('benar_' + teamId);
        const sInput = document.getElementById('salah_' + teamId);
        const kDisplay = document.getElementById('kosong_' + teamId);
        const scoreDisplay = document.getElementById('score-display-' + teamId);
        const scoreAltDisplay = document.getElementById('score-alt-' + teamId);
        const row = document.getElementById('row-team-' + teamId);

        if (!bInput || !sInput) return;

        const b = parseInt(bInput.value) || 0;
        const s = parseInt(sInput.value) || 0;
        const k = Math.max(0, 50 - (b + s));

        if (kDisplay) kDisplay.innerText = k;

        const standardScore = (b - s) * 2;
        const skema2Score = 50 + b - s;

        const activeScore = (currentFormulaMode === 'skema2') ? skema2Score : standardScore;

        if (scoreDisplay) scoreDisplay.innerText = activeScore;
        if (scoreAltDisplay) scoreAltDisplay.innerText = 'Skema 2: ' + skema2Score;

        if (row) {
            row.setAttribute('data-score', activeScore);
        }
    }

    function parseTimeToSeconds(timeStr) {
        if (!timeStr) return 999999;
        const parts = timeStr.trim().split(':');
        if (parts.length >= 2) {
            const m = parseInt(parts[0]) || 0;
            const s = parseInt(parts[1]) || 0;
            return (m * 60) + s;
        }
        return 999999;
    }

    function handleTimeChange(teamId, val) {
        const row = document.getElementById('row-team-' + teamId);
        if (row) {
            row.setAttribute('data-time-seconds', parseTimeToSeconds(val));
        }
        sortRowsByRank();
    }

    function sortRowsByRank() {
        const tbody = document.getElementById('olimpiade-tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('.team-row'));

        rows.sort((a, b) => {
            const scoreA = parseFloat(a.getAttribute('data-score')) || -999;
            const scoreB = parseFloat(b.getAttribute('data-score')) || -999;

            if (scoreA !== scoreB) {
                return scoreB - scoreA; // Score DESC
            }

            const timeA = parseInt(a.getAttribute('data-time-seconds')) || 999999;
            const timeB = parseInt(b.getAttribute('data-time-seconds')) || 999999;

            return timeA - timeB; // Time ASC
        });

        // Re-append rows in sorted order and update ranking column
        rows.forEach((row, idx) => {
            tbody.appendChild(row);
            const teamId = row.getAttribute('data-team-id');
            const rankVal = idx + 1;
            const container = document.getElementById('badge-container-' + teamId);

            // Row border highlight
            if (rankVal === 1) {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-amber-500/5 hover:bg-amber-500/10 border-l-4 border-l-amber-500';
            } else if (rankVal === 2) {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-slate-500/5 hover:bg-slate-500/10 border-l-4 border-l-slate-400';
            } else if (rankVal === 3) {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-amber-700/5 hover:bg-amber-700/10 border-l-4 border-l-amber-700';
            } else if (rankVal <= 9) {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-emerald-500/5 hover:bg-emerald-500/10 border-l-4 border-l-emerald-500';
            } else {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 hover:bg-slate-50/80 border-l-4 border-l-transparent';
            }

            if (container) {
                if (rankVal === 1) {
                    container.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 font-black text-xs shadow-2xs"><i class="fa-solid fa-crown text-amber-600"></i> Peringkat 1 &bull; Lolos Semifinal</span>';
                } else if (rankVal === 2) {
                    container.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-200 text-slate-900 border border-slate-300 font-black text-xs shadow-2xs"><i class="fa-solid fa-award text-slate-600"></i> Peringkat 2 &bull; Lolos Semifinal</span>';
                } else if (rankVal === 3) {
                    container.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-950 border border-amber-400 font-black text-xs shadow-2xs"><i class="fa-solid fa-award text-amber-700"></i> Peringkat 3 &bull; Lolos Semifinal</span>';
                } else if (rankVal <= 9) {
                    container.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-900 border border-emerald-300 font-black text-xs shadow-2xs"><i class="fa-solid fa-circle-check text-emerald-600"></i> Peringkat ' + rankVal + ' &bull; Lolos Semifinal</span>';
                } else {
                    container.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 font-bold text-xs"><i class="fa-solid fa-circle-xmark text-slate-400"></i> Peringkat ' + rankVal + ' &bull; Belum Lolos</span>';
                }
            }
        });

        updateSummaryStats();
    }

    function updateSummaryStats() {
        const rows = document.querySelectorAll('.team-row');
        let total = rows.length;
        let scored = 0;
        let highest = -999;
        let highestTeamName = '-';
        let sum = 0;
        let fastestSec = 999999;
        let fastestTimeStr = '00:00';
        let fastestTeamName = '-';

        rows.forEach(r => {
            const teamId = r.getAttribute('data-team-id');
            const b = document.getElementById('benar_' + teamId)?.value;

            if (b !== '' && b !== undefined) {
                scored++;
                const score = parseFloat(r.getAttribute('data-score')) || 0;
                sum += score;
                const school = r.querySelector('td:nth-child(2) .font-bold')?.innerText || '-';

                if (score > highest) {
                    highest = score;
                    highestTeamName = school;
                }

                const tSec = parseInt(r.getAttribute('data-time-seconds')) || 999999;
                const tInput = document.getElementById('waktu_' + teamId)?.value || '00:00';
                if (tSec < fastestSec && tSec > 0) {
                    fastestSec = tSec;
                    fastestTimeStr = tInput;
                    fastestTeamName = school;
                }
            }
        });

        const elTotal = document.getElementById('stat-total-teams');
        const elScored = document.getElementById('stat-scored-teams');
        const elHigh = document.getElementById('stat-highest-score');
        const elHighTeam = document.getElementById('stat-highest-team');
        const elAvg = document.getElementById('stat-avg-score');
        const elFast = document.getElementById('stat-fastest-time');
        const elFastTeam = document.getElementById('stat-fastest-team');

        if (elTotal) elTotal.innerText = total;
        if (elScored) elScored.innerText = scored;
        if (elHigh) elHigh.innerText = highest > -999 ? highest : 0;
        if (elHighTeam) elHighTeam.innerText = highestTeamName;
        if (elAvg) elAvg.innerText = scored > 0 ? (sum / scored).toFixed(1) : 0;
        if (elFast) elFast.innerText = fastestTimeStr;
        if (elFastTeam) elFastTeam.innerText = fastestTeamName;
    }

    function filterTableRows(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.team-row').forEach(row => {
            const text = (row.getAttribute('data-school') || '') + ' ' + (row.innerText || '');
            if (!q || text.toLowerCase().includes(q)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Semi-Final & Grand Final calculations
    function calcSemiRow(teamId, terminNum) {
        const a = parseFloat(document.getElementById('inp-amplop-' + teamId)?.value) || 0;
        const r = parseFloat(document.getElementById('inp-rebutan-' + teamId)?.value) || 0;
        const b = parseFloat(document.getElementById('inp-balon-' + teamId)?.value) || 0;
        const total = a + r + b;

        const disp = document.getElementById('display-total-' + teamId);
        if (disp) {
            disp.innerText = new Intl.NumberFormat('id-ID').format(total);
        }
    }

    function adjustInput(inputId, delta, teamId, terminNum) {
        const inp = document.getElementById(inputId);
        if (!inp) return;
        const cur = parseFloat(inp.value) || 0;
        inp.value = cur + delta;
        calcSemiRow(teamId, terminNum);
    }

    function calcFinalRow(teamId) {
        const a = parseFloat(document.getElementById('inp-final-amplop-' + teamId)?.value) || 0;
        const r = parseFloat(document.getElementById('inp-final-rebutan-' + teamId)?.value) || 0;
        const t = parseFloat(document.getElementById('inp-final-taruh-' + teamId)?.value) || 0;
        const total = a + r + t;

        const disp = document.getElementById('display-final-total-' + teamId);
        if (disp) {
            disp.innerText = new Intl.NumberFormat('id-ID').format(total);
        }
    }

    function adjustFinalInput(inputId, delta, teamId) {
        const inp = document.getElementById(inputId);
        if (!inp) return;
        const cur = parseFloat(inp.value) || 0;
        inp.value = cur + delta;
        calcFinalRow(teamId);
    }

    // Auto sort on page load for Stage 1
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('olimpiade-tbody')) {
            sortRowsByRank();
        }
    });
</script>
@endpush
@endsection
