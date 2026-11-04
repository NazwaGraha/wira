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
        <a href="{{ route('admin.competition-scores.olimpiade-live', $category->id) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-sm transition flex items-center gap-1.5">
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

    <!-- ================= APP HEADER CARD ================= -->
    <header class="bg-gradient-to-r {{ $headerGradient }} text-white rounded-3xl p-6 sm:p-7 shadow-xl border relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full blur-3xl opacity-25 {{ $isMadya ? 'bg-blue-500' : 'bg-amber-500' }} pointer-events-none"></div>

        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 relative z-10">
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
                            PORTAL PENILAIAN &bull; OLIMPIADE KEPALANGMERAHAN
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                        Lembar Grid Nilai &amp; Peringkat Olimpiade
                    </h1>
                    <p class="text-xs text-slate-300 mt-1 flex items-center gap-2">
                        <span>Sua Bhakti Berkarya &bull; PMI SMAN 1 Ciawi</span>
                        <span>&bull;</span>
                        <span class="font-mono text-[11px] {{ $isMadya ? 'text-sky-300' : 'text-amber-300' }} font-bold">Total 50 Soal Pilihan Ganda</span>
                    </p>
                </div>
            </div>

            <!-- Right: Action Buttons -->
            <div class="flex items-center gap-3 flex-wrap">
                <form action="{{ route('admin.competition-scores.olimpiade-reset', $category->id) }}" method="POST" onsubmit="return confirm('Kosongkan dan reset seluruh nilai olimpiade ini?')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-2xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center gap-2 shadow-sm cursor-pointer">
                        <i class="fa-solid fa-arrow-rotate-left"></i> Reset
                    </button>
                </form>
                <a href="{{ route('admin.competition-scores.olimpiade-export-excel', $category->id) }}" class="px-4 py-2.5 rounded-2xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-file-excel text-emerald-400"></i> Export Excel
                </a>
                <a href="{{ route('admin.competition-scores.olimpiade-live', $category->id) }}" target="_blank" class="px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-desktop"></i> Layar Panggung
                </a>
                <button type="button" onclick="document.getElementById('olimpiade-form').submit()" class="px-5 py-2.5 rounded-2xl {{ $saveBtnClass }} font-black text-xs transition flex items-center gap-2 shadow-lg cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Seluruh Nilai
                </button>
            </div>
        </div>
    </header>

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
                    <span><strong>Aturan Ranking:</strong> Diurutkan dari <strong>Nilai Tertinggi</strong>. Jika nilai sama/kembar, peserta dengan <strong>Waktu Tempuh Tercepat</strong> ([MM]:SS) otomatis menempati ranking lebih atas.</span>
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
            <!-- Stat 1: Total Peserta -->
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

            <!-- Stat 2: Nilai Tertinggi -->
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

            <!-- Stat 3: Rata-rata Nilai -->
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

            <!-- Stat 4: Waktu Tercepat -->
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
                        <p class="text-[11px] text-slate-500 font-medium">Input jumlah jawaban benar, salah, dan waktu tempuh. Sistem langsung menghitung skor dan ranking secara instan.</p>
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

            <!-- Table Itself -->
            <div class="overflow-x-auto">
                <table id="olimpiade-table" class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase text-[10.5px] tracking-wider font-black select-none">
                            <th class="py-4 px-3.5 text-center w-14 border-r border-slate-800">RANK</th>
                            <th class="py-4 px-3 text-center w-16 border-r border-slate-800">NO URUT</th>
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
                            <th class="py-4 px-3 text-center w-36 bg-black text-slate-300">
                                PREDIKAT JUARA
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

                                $rankBadgeClass = 'bg-slate-100 text-slate-700 font-bold border-slate-200';
                                $rowHighlight = 'hover:bg-slate-50/80';
                                if ($rVal === 1) {
                                    $rankBadgeClass = 'bg-amber-400 text-slate-950 font-black shadow-md border-amber-300';
                                    $rowHighlight = 'bg-amber-500/5 hover:bg-amber-500/10 border-l-4 border-l-amber-500';
                                } elseif ($rVal === 2) {
                                    $rankBadgeClass = 'bg-slate-300 text-slate-900 font-black shadow-md border-slate-200';
                                    $rowHighlight = 'bg-slate-500/5 hover:bg-slate-500/10 border-l-4 border-l-slate-400';
                                } elseif ($rVal === 3) {
                                    $rankBadgeClass = 'bg-amber-700 text-amber-100 font-black shadow-md border-amber-600';
                                    $rowHighlight = 'bg-amber-700/5 hover:bg-amber-700/10 border-l-4 border-l-amber-700';
                                } elseif ($rVal && $rVal <= 6) {
                                    $rankBadgeClass = 'bg-blue-100 text-blue-900 font-bold border-blue-200';
                                }
                            @endphp
                            <tr id="row-team-{{ $t->id }}" 
                                class="team-row transition-colors duration-150 border-b border-slate-100 {{ $rowHighlight }}"
                                data-team-id="{{ $t->id }}"
                                data-school="{{ strtolower($t->registration?->school_name ?? $t->team_name) }}"
                                data-order="{{ $t->order_number ?? $idx + 1 }}"
                                data-score="{{ $scoreVal }}"
                                data-time-seconds="{{ $t->time_seconds }}">
                                
                                <!-- 1. RANK -->
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <span id="rank-badge-{{ $t->id }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs {{ $rankBadgeClass }} border">
                                        {{ $rVal ?: '-' }}
                                    </span>
                                </td>

                                <!-- 2. NO URUT -->
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 font-mono font-bold text-xs text-slate-700">
                                        {{ $t->order_number ?: sprintf('%02d', $idx + 1) }}
                                    </span>
                                </td>

                                <!-- 3. NAMA SEKOLAH & REGU -->
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

                                <!-- 4. BENAR (1) -->
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

                                <!-- 5. SALAH (-1) -->
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

                                <!-- 6. TIDAK DIJAWAB (0) -->
                                <td class="py-3 px-3 text-center">
                                    <span id="kosong_{{ $t->id }}" class="inline-flex items-center justify-center w-10 py-1 rounded-lg bg-slate-100 border border-slate-200 font-mono font-bold text-xs text-slate-600" title="Otomatis: 50 - (Benar + Salah)">
                                        {{ $t->has_score ? $kVal : 50 }}
                                    </span>
                                </td>

                                <!-- 7. WAKTU TEMPUH (MM:SS) -->
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

                                <!-- 8. NILAI AKHIR -->
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

                                <!-- 9. PREDIKAT JUARA -->
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <div id="badge-container-{{ $t->id }}">
                                        @if($t->badge_type === 'gold')
                                            <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 border border-amber-300 font-black text-[10px] tracking-wider uppercase flex items-center gap-1 justify-center">
                                                <i class="fa-solid fa-crown text-amber-600"></i> JUARA 1
                                            </span>
                                        @elseif($t->badge_type === 'silver')
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-200 text-slate-800 border border-slate-300 font-black text-[10px] tracking-wider uppercase flex items-center gap-1 justify-center">
                                                <i class="fa-solid fa-award text-slate-600"></i> JUARA 2
                                            </span>
                                        @elseif($t->badge_type === 'bronze')
                                            <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-950 border border-amber-400 font-black text-[10px] tracking-wider uppercase flex items-center gap-1 justify-center">
                                                <i class="fa-solid fa-award text-amber-700"></i> JUARA 3
                                            </span>
                                        @elseif($t->badge_type === 'blue')
                                            <span class="px-2 py-0.5 rounded-lg bg-sky-100 text-sky-800 border border-sky-300 font-bold text-[9.5px] uppercase tracking-wider block text-center">
                                                {{ $t->badge }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-500 font-semibold text-[9.5px] uppercase tracking-wider block text-center">
                                                {{ $t->badge }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
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
                    Sistem Perhitungan Otomatis Sesuai Regulasi Resmi Juri
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
                    <div class="text-[11px] text-slate-400">Seluruh perubahan nilai &amp; waktu tempuh akan langsung memperbarui klasemen juara umum.</div>
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

@push('scripts')
<script>
    let currentFormulaMode = 'standard'; // 'standard' = (B - S) * 2 | 'skema2' = 50 + B - S

    function changeFormulaMode(mode) {
        currentFormulaMode = mode;
        document.getElementById('input-formula-type').value = mode;
        const label = document.getElementById('formula-label-display');
        if (mode === 'skema2') {
            label.innerHTML = 'Nilai Akhir = 50 + Benar &minus; Salah';
        } else {
            label.innerHTML = 'Nilai Akhir = (Benar &minus; Salah) &times; 2';
        }

        // Recalculate all rows
        document.querySelectorAll('.team-row').forEach(row => {
            const teamId = row.getAttribute('data-team-id');
            calculateRowScore(teamId);
        });

        updateSummaryStats();
    }

    function adjustScoreInput(inputId, delta) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let cur = parseInt(input.value) || 0;
        let next = Math.max(0, Math.min(50, cur + delta));
        input.value = next;
        
        // Trigger change
        const teamId = inputId.split('_')[1];
        handleRowChange(teamId);
    }

    function handleRowChange(teamId) {
        calculateRowScore(teamId);
        updateSummaryStats();
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
        updateSummaryStats();
    }

    function sortRowsByRank() {
        const tbody = document.getElementById('olimpiade-tbody');
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

        // Re-append rows in sorted order and update rank badges
        rows.forEach((row, idx) => {
            tbody.appendChild(row);
            const teamId = row.getAttribute('data-team-id');
            const rankVal = idx + 1;
            const badgeEl = document.getElementById('rank-badge-' + teamId);
            const container = document.getElementById('badge-container-' + teamId);

            if (badgeEl) {
                badgeEl.innerText = rankVal;
                badgeEl.className = 'inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs border ';
                if (rankVal === 1) {
                    badgeEl.className += 'bg-amber-400 text-slate-950 font-black shadow-md border-amber-300';
                    row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-amber-500/5 hover:bg-amber-500/10 border-l-4 border-l-amber-500';
                } else if (rankVal === 2) {
                    badgeEl.className += 'bg-slate-300 text-slate-900 font-black shadow-md border-slate-200';
                    row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-slate-500/5 hover:bg-slate-500/10 border-l-4 border-l-slate-400';
                } else if (rankVal === 3) {
                    badgeEl.className += 'bg-amber-700 text-amber-100 font-black shadow-md border-amber-600';
                    row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-amber-700/5 hover:bg-amber-700/10 border-l-4 border-l-amber-700';
                } else if (rankVal <= 6) {
                    badgeEl.className += 'bg-blue-100 text-blue-900 font-bold border-blue-200';
                    row.className = 'team-row transition-colors duration-150 border-b border-slate-100 hover:bg-slate-50/80';
                } else {
                    badgeEl.className += 'bg-slate-100 text-slate-700 font-bold border-slate-200';
                    row.className = 'team-row transition-colors duration-150 border-b border-slate-100 hover:bg-slate-50/80';
                }
            }

            if (container) {
                if (rankVal === 1) {
                    container.innerHTML = '<span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 border border-amber-300 font-black text-[10px] tracking-wider uppercase flex items-center gap-1 justify-center"><i class="fa-solid fa-crown text-amber-600"></i> JUARA 1</span>';
                } else if (rankVal === 2) {
                    container.innerHTML = '<span class="px-2.5 py-1 rounded-lg bg-slate-200 text-slate-800 border border-slate-300 font-black text-[10px] tracking-wider uppercase flex items-center gap-1 justify-center"><i class="fa-solid fa-award text-slate-600"></i> JUARA 2</span>';
                } else if (rankVal === 3) {
                    container.innerHTML = '<span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-950 border border-amber-400 font-black text-[10px] tracking-wider uppercase flex items-center gap-1 justify-center"><i class="fa-solid fa-award text-amber-700"></i> JUARA 3</span>';
                } else if (rankVal === 4) {
                    container.innerHTML = '<span class="px-2 py-0.5 rounded-lg bg-sky-100 text-sky-800 border border-sky-300 font-bold text-[9.5px] uppercase tracking-wider block text-center">HARAPAN 1</span>';
                } else if (rankVal === 5) {
                    container.innerHTML = '<span class="px-2 py-0.5 rounded-lg bg-sky-100 text-sky-800 border border-sky-300 font-bold text-[9.5px] uppercase tracking-wider block text-center">HARAPAN 2</span>';
                } else if (rankVal === 6) {
                    container.innerHTML = '<span class="px-2 py-0.5 rounded-lg bg-sky-100 text-sky-800 border border-sky-300 font-bold text-[9.5px] uppercase tracking-wider block text-center">HARAPAN 3</span>';
                } else {
                    container.innerHTML = '<span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-500 font-semibold text-[9.5px] uppercase tracking-wider block text-center">TERVERIFIKASI</span>';
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
            const s = document.getElementById('salah_' + teamId)?.value;

            if (b !== '' && b !== undefined) {
                scored++;
                const score = parseFloat(r.getAttribute('data-score')) || 0;
                sum += score;
                const school = r.querySelector('td:nth-child(3) .font-bold')?.innerText || '-';

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

        document.getElementById('stat-total-teams').innerText = total;
        document.getElementById('stat-scored-teams').innerText = scored;
        document.getElementById('stat-highest-score').innerText = highest > -999 ? highest : 0;
        document.getElementById('stat-highest-team').innerText = highestTeamName;
        document.getElementById('stat-avg-score').innerText = scored > 0 ? (sum / scored).toFixed(1) : 0;
        document.getElementById('stat-fastest-time').innerText = fastestTimeStr;
        document.getElementById('stat-fastest-team').innerText = fastestTeamName;
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
</script>
@endpush
@endsection
