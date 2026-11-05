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

    <!-- ================= APP HEADER CARD & PODIUM JUARA ================= -->
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
                            SISTEM PENILAIAN 1 SESI &bull; LANGSUNG MENENTUKAN JUARA
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                        Lembar Grid Nilai &amp; Penentuan Juara Olimpiade
                    </h1>
                    <p class="text-xs text-slate-300 mt-1 flex items-center gap-2 flex-wrap">
                        <span>Sua Bhakti Berkarya &bull; PMI SMAN 1 Ciawi</span>
                        <span>&bull;</span>
                        <span class="font-mono text-[11px] {{ $isMadya ? 'text-sky-300' : 'text-amber-300' }} font-bold">Nilai Tertinggi &amp; Waktu Tercepat Langsung Menentukan Juara 1 s.d. Harapan 3</span>
                    </p>
                </div>
            </div>

            <!-- Right: Action Reset -->
            <div class="flex items-center gap-2 shrink-0">
                <form action="{{ route('admin.competition-scores.olimpiade-reset-stage', $category->id) }}" method="POST" onsubmit="return confirm('Reset seluruh nilai dan hasil juara Olimpiade kategori ini?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900/80 hover:bg-rose-950 text-slate-300 hover:text-rose-200 border border-white/10 hover:border-rose-500/50 font-bold text-xs transition flex items-center gap-2 cursor-pointer shadow-sm">
                        <i class="fa-solid fa-rotate-left text-rose-400"></i> Reset Nilai Olimpiade
                    </button>
                </form>
            </div>
        </div>

        <!-- PODIUM PEMENANG 6 BESAR (Juara 1 s/d Juara Harapan 3) -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3.5 relative z-10">
            <!-- Juara 1 (Emas) -->
            <div class="bg-gradient-to-b from-amber-500/20 to-amber-950/40 rounded-2xl p-3.5 border-2 border-amber-400/80 shadow-lg flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-lg bg-amber-400 text-slate-950 font-black text-[10.5px] uppercase flex items-center gap-1 shadow-xs">
                        <i class="fa-solid fa-crown text-amber-900"></i> JUARA 1
                    </span>
                    <span class="text-[10px] font-mono text-amber-200 font-bold">EMAS</span>
                </div>
                <div>
                    <div id="podium-school-1" class="font-black text-white text-xs sm:text-sm leading-tight truncate">
                        {{ $juara1Team ? ($juara1Team->registration?->school_name ?? $juara1Team->team_name) : 'Belum Ada' }}
                    </div>
                    <div id="podium-team-1" class="text-[10px] text-amber-200/80 font-medium truncate mt-0.5">
                        {{ $juara1Team ? ($juara1Team->team_label ?: $juara1Team->team_name) : '-' }}
                    </div>
                </div>
                <div class="mt-2.5 pt-2 border-t border-amber-400/20 flex items-center justify-between font-mono">
                    <span class="text-[10px] text-amber-200">Skor: <strong id="podium-score-1" class="text-white text-xs">{{ $juara1Team?->final_score ?? '-' }}</strong></span>
                    <span id="podium-time-1" class="text-[10px] text-amber-200">{{ $juara1Team?->waktu ?? '-' }}</span>
                </div>
            </div>

            <!-- Juara 2 (Perak) -->
            <div class="bg-slate-800/60 rounded-2xl p-3.5 border border-slate-400/60 shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-200 text-slate-900 font-black text-[10.5px] uppercase flex items-center gap-1 shadow-xs">
                        <i class="fa-solid fa-medal text-slate-600"></i> JUARA 2
                    </span>
                    <span class="text-[10px] font-mono text-slate-300 font-bold">PERAK</span>
                </div>
                <div>
                    <div id="podium-school-2" class="font-black text-white text-xs sm:text-sm leading-tight truncate">
                        {{ $juara2Team ? ($juara2Team->registration?->school_name ?? $juara2Team->team_name) : 'Belum Ada' }}
                    </div>
                    <div id="podium-team-2" class="text-[10px] text-slate-300 font-medium truncate mt-0.5">
                        {{ $juara2Team ? ($juara2Team->team_label ?: $juara2Team->team_name) : '-' }}
                    </div>
                </div>
                <div class="mt-2.5 pt-2 border-t border-slate-700 flex items-center justify-between font-mono">
                    <span class="text-[10px] text-slate-300">Skor: <strong id="podium-score-2" class="text-white text-xs">{{ $juara2Team?->final_score ?? '-' }}</strong></span>
                    <span id="podium-time-2" class="text-[10px] text-slate-300">{{ $juara2Team?->waktu ?? '-' }}</span>
                </div>
            </div>

            <!-- Juara 3 (Perunggu) -->
            <div class="bg-amber-950/30 rounded-2xl p-3.5 border border-amber-600/40 shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-lg bg-amber-700/80 text-amber-100 font-black text-[10.5px] uppercase flex items-center gap-1 shadow-xs">
                        <i class="fa-solid fa-medal text-amber-300"></i> JUARA 3
                    </span>
                    <span class="text-[10px] font-mono text-amber-300 font-bold">PERUNGGU</span>
                </div>
                <div>
                    <div id="podium-school-3" class="font-black text-white text-xs sm:text-sm leading-tight truncate">
                        {{ $juara3Team ? ($juara3Team->registration?->school_name ?? $juara3Team->team_name) : 'Belum Ada' }}
                    </div>
                    <div id="podium-team-3" class="text-[10px] text-amber-200/70 font-medium truncate mt-0.5">
                        {{ $juara3Team ? ($juara3Team->team_label ?: $juara3Team->team_name) : '-' }}
                    </div>
                </div>
                <div class="mt-2.5 pt-2 border-t border-amber-600/20 flex items-center justify-between font-mono">
                    <span class="text-[10px] text-amber-300">Skor: <strong id="podium-score-3" class="text-white text-xs">{{ $juara3Team?->final_score ?? '-' }}</strong></span>
                    <span id="podium-time-3" class="text-[10px] text-amber-300">{{ $juara3Team?->waktu ?? '-' }}</span>
                </div>
            </div>

            <!-- Harapan 1 -->
            <div class="bg-sky-950/30 rounded-2xl p-3.5 border border-sky-600/30 shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="px-2 py-0.5 rounded-lg bg-sky-950 text-sky-300 border border-sky-700 font-bold text-[10px] uppercase flex items-center gap-1">
                        <i class="fa-solid fa-award text-sky-400"></i> HARAPAN 1
                    </span>
                    <span class="text-[10px] font-mono text-sky-300">#4</span>
                </div>
                <div>
                    <div id="podium-school-4" class="font-black text-white text-xs sm:text-sm leading-tight truncate">
                        {{ $harapan1Team ? ($harapan1Team->registration?->school_name ?? $harapan1Team->team_name) : 'Belum Ada' }}
                    </div>
                    <div id="podium-team-4" class="text-[10px] text-sky-200/70 font-medium truncate mt-0.5">
                        {{ $harapan1Team ? ($harapan1Team->team_label ?: $harapan1Team->team_name) : '-' }}
                    </div>
                </div>
                <div class="mt-2.5 pt-2 border-t border-sky-800/40 flex items-center justify-between font-mono">
                    <span class="text-[10px] text-sky-300">Skor: <strong id="podium-score-4" class="text-white text-xs">{{ $harapan1Team?->final_score ?? '-' }}</strong></span>
                    <span id="podium-time-4" class="text-[10px] text-sky-300">{{ $harapan1Team?->waktu ?? '-' }}</span>
                </div>
            </div>

            <!-- Harapan 2 -->
            <div class="bg-sky-950/20 rounded-2xl p-3.5 border border-sky-600/20 shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="px-2 py-0.5 rounded-lg bg-sky-950/80 text-sky-300 border border-sky-800 font-bold text-[10px] uppercase flex items-center gap-1">
                        <i class="fa-solid fa-award text-sky-400"></i> HARAPAN 2
                    </span>
                    <span class="text-[10px] font-mono text-sky-300">#5</span>
                </div>
                <div>
                    <div id="podium-school-5" class="font-black text-white text-xs sm:text-sm leading-tight truncate">
                        {{ $harapan2Team ? ($harapan2Team->registration?->school_name ?? $harapan2Team->team_name) : 'Belum Ada' }}
                    </div>
                    <div id="podium-team-5" class="text-[10px] text-sky-200/70 font-medium truncate mt-0.5">
                        {{ $harapan2Team ? ($harapan2Team->team_label ?: $harapan2Team->team_name) : '-' }}
                    </div>
                </div>
                <div class="mt-2.5 pt-2 border-t border-sky-800/30 flex items-center justify-between font-mono">
                    <span class="text-[10px] text-sky-300">Skor: <strong id="podium-score-5" class="text-white text-xs">{{ $harapan2Team?->final_score ?? '-' }}</strong></span>
                    <span id="podium-time-5" class="text-[10px] text-sky-300">{{ $harapan2Team?->waktu ?? '-' }}</span>
                </div>
            </div>

            <!-- Harapan 3 -->
            <div class="bg-sky-950/20 rounded-2xl p-3.5 border border-sky-600/20 shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="px-2 py-0.5 rounded-lg bg-sky-950/60 text-sky-300 border border-sky-800 font-bold text-[10px] uppercase flex items-center gap-1">
                        <i class="fa-solid fa-award text-sky-400"></i> HARAPAN 3
                    </span>
                    <span class="text-[10px] font-mono text-sky-300">#6</span>
                </div>
                <div>
                    <div id="podium-school-6" class="font-black text-white text-xs sm:text-sm leading-tight truncate">
                        {{ $harapan3Team ? ($harapan3Team->registration?->school_name ?? $harapan3Team->team_name) : 'Belum Ada' }}
                    </div>
                    <div id="podium-team-6" class="text-[10px] text-sky-200/70 font-medium truncate mt-0.5">
                        {{ $harapan3Team ? ($harapan3Team->team_label ?: $harapan3Team->team_name) : '-' }}
                    </div>
                </div>
                <div class="mt-2.5 pt-2 border-t border-sky-800/30 flex items-center justify-between font-mono">
                    <span class="text-[10px] text-sky-300">Skor: <strong id="podium-score-6" class="text-white text-xs">{{ $harapan3Team?->final_score ?? '-' }}</strong></span>
                    <span id="podium-time-6" class="text-[10px] text-sky-300">{{ $harapan3Team?->waktu ?? '-' }}</span>
                </div>
            </div>
        </div>
    </header>

    <div class="space-y-6">

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
                                    @if(($activeFormulaType ?? 'skema2') === 'skema2')
                                        Nilai Akhir = 50 + Benar &minus; Salah (Skema 2)
                                    @else
                                        Nilai Akhir = (Benar &minus; Salah) &times; 2 (Standar)
                                    @endif
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
                            <span><strong>Aturan Ranking:</strong> Diurutkan dari <strong>Nilai Tertinggi</strong>. Jika nilai sama, peserta dengan <strong>Waktu Tempuh Tercepat</strong> ([MM]:SS) otomatis menempati ranking lebih atas untuk menentukan <strong>Juara 1 s.d. Harapan 3</strong> secara langsung.</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium flex-wrap gap-2">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            Input Benar, Salah, Kosong mendukung 3 digit (contoh: 200) &bull; Waktu otomatis ([MM]:SS)
                        </span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-slate-400 font-semibold">Pilihan Formula:</span>
                            <select id="select-formula-mode" onchange="changeFormulaMode(this.value)" class="text-[11px] font-bold bg-slate-100 border border-slate-300 rounded-lg px-2 py-0.5 text-slate-800 outline-none">
                                <option value="skema2" {{ ($activeFormulaType ?? 'skema2') === 'skema2' ? 'selected' : '' }}>Skema 2: 50 + Benar - Salah (Draft Sheet)</option>
                                <option value="standard" {{ ($activeFormulaType ?? 'skema2') === 'standard' ? 'selected' : '' }}>Standar: (Benar - Salah) x 2</option>
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
                <input type="hidden" name="formula_type" id="input-formula-type" value="{{ $activeFormulaType ?? 'skema2' }}">

                <div class="bg-white rounded-3xl shadow-md border border-slate-200 overflow-hidden">
                    
                    <!-- Table Toolbar -->
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full {{ $accentBar }} inline-block"></span>
                            <div>
                                <h2 class="font-black text-sm sm:text-base text-slate-900">
                                    Lembar Kerja Penginputan &amp; Klasemen Olimpiade
                                </h2>
                                <p class="text-[11px] text-slate-500 font-medium">Input jumlah jawaban benar, salah, dan waktu tempuh. Sistem otomatis menentukan Juara 1 s.d. Harapan 3 secara presisi.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" id="filter-search" oninput="filterTableRows(this.value)" placeholder="Cari nama sekolah / regu..." class="pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 text-xs bg-white text-slate-900 outline-none w-44 sm:w-56 focus:border-blue-500">
                            </div>
                            <button type="button" onclick="sortRowsByRank()" id="btn-sort-rank" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-arrow-down-short-wide"></i> Urutkan Ranking
                            </button>
                            <button type="button" onclick="sortRowsByOrder()" id="btn-sort-order" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-1.5 border border-slate-300 shadow-2xs transition active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-arrow-down-1-9"></i> Urutkan No Urut
                            </button>
                        </div>
                    </div>

                    <!-- Table Itself: Strictly starts with NO URUT, and far right is PREDIKAT JUARA & RANKING -->
                    <div class="overflow-x-auto">
                        <table id="olimpiade-table" class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white uppercase text-[10px] sm:text-[10.5px] tracking-wider font-black select-none">
                                    <th class="py-3.5 px-2 text-center w-14 sm:w-16 border-r border-slate-800">NO URUT</th>
                                    <th class="py-3.5 px-3.5 border-r border-slate-800 min-w-[190px]">NAMA PANGKALAN SEKOLAH / REGU</th>
                                    <th class="py-3.5 px-2 text-center border-r border-slate-800 w-20 sm:w-24 bg-emerald-950/60 text-emerald-300">
                                        <div class="flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-check text-[10px]"></i> BENAR (+1)
                                        </div>
                                    </th>
                                    <th class="py-3.5 px-2 text-center border-r border-slate-800 w-20 sm:w-24 bg-rose-950/60 text-rose-300">
                                        <div class="flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-xmark text-[10px]"></i> SALAH (&minus;1)
                                        </div>
                                    </th>
                                    <th class="py-3.5 px-2 text-center border-r border-slate-800 w-16 sm:w-20 bg-slate-800 text-slate-300">
                                        KOSONG (0)
                                    </th>
                                    <th class="py-3.5 px-2 text-center border-r border-slate-800 w-24 sm:w-28 bg-slate-900 text-amber-300">
                                        <div class="flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-stopwatch text-[10px]"></i> WAKTU
                                        </div>
                                    </th>
                                    <th class="py-3.5 px-2 text-center border-r border-slate-800 w-20 sm:w-24 {{ $isMadya ? 'bg-blue-950 text-sky-300' : 'bg-amber-950 text-amber-300' }}">
                                        NILAI AKHIR
                                    </th>
                                    <th class="py-3.5 px-2.5 text-center w-44 sm:w-48 bg-black text-slate-200">
                                        PREDIKAT JUARA &amp; RANKING
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
                                        $isWinner = ($rVal && $rVal <= 6);

                                        $rowHighlight = 'border-l-4 border-l-transparent hover:bg-slate-50/80';
                                        if ($rVal === 1) {
                                            $rowHighlight = 'bg-amber-500/10 hover:bg-amber-500/15 border-l-4 border-l-amber-500';
                                        } elseif ($rVal === 2) {
                                            $rowHighlight = 'bg-slate-400/10 hover:bg-slate-400/15 border-l-4 border-l-slate-400';
                                        } elseif ($rVal === 3) {
                                            $rowHighlight = 'bg-amber-700/10 hover:bg-amber-700/15 border-l-4 border-l-amber-700';
                                        } elseif ($rVal >= 4 && $rVal <= 6) {
                                            $rowHighlight = 'bg-sky-500/5 hover:bg-sky-500/10 border-l-4 border-l-sky-500';
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
                                        <td class="py-3 px-2 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-mono font-bold text-xs text-slate-700">
                                                {{ $t->order_number ?: sprintf('%02d', $idx + 1) }}
                                            </span>
                                        </td>

                                        <!-- 2. NAMA SEKOLAH & REGU -->
                                        <td class="py-3 px-3.5">
                                            <div class="font-bold text-slate-900 text-xs sm:text-[13px] leading-tight">
                                                {{ $t->registration?->school_name ?? $t->team_name }}
                                            </div>
                                            <div class="text-[10.5px] text-slate-500 font-medium mt-0.5 flex items-center gap-1.5">
                                                <span class="text-slate-600 font-semibold">{{ $t->team_label ?: $t->team_name }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="font-mono text-[10px] text-slate-400">ID: OLIM-{{ $t->id }}</span>
                                            </div>
                                        </td>

                                        <!-- 3. BENAR (1) -->
                                        <td class="py-3 px-2 text-center">
                                            <input type="number" 
                                                   id="benar_{{ $t->id }}" 
                                                   name="benar[{{ $t->id }}]" 
                                                   value="{{ $t->has_score ? $bVal : '' }}" 
                                                   min="0" 
                                                   max="999" 
                                                   placeholder="0"
                                                   oninput="handleRowChange({{ $t->id }}, false)" 
                                                   onblur="handleRowChange({{ $t->id }}, true)"
                                                   class="input-benar w-14 sm:w-16 text-center font-mono font-black text-xs sm:text-sm text-emerald-700 bg-emerald-50/70 focus:bg-white rounded-lg py-1 border border-emerald-300 outline-none {{ $activeInputBorder }} shadow-2xs transition">
                                        </td>

                                        <!-- 4. SALAH (-1) -->
                                        <td class="py-3 px-2 text-center">
                                            <input type="number" 
                                                   id="salah_{{ $t->id }}" 
                                                   name="salah[{{ $t->id }}]" 
                                                   value="{{ $t->has_score ? $sVal : '' }}" 
                                                   min="0" 
                                                   max="999" 
                                                   placeholder="0"
                                                   oninput="handleRowChange({{ $t->id }}, false)" 
                                                   onblur="handleRowChange({{ $t->id }}, true)"
                                                   class="input-salah w-14 sm:w-16 text-center font-mono font-black text-xs sm:text-sm text-rose-700 bg-rose-50/70 focus:bg-white rounded-lg py-1 border border-rose-300 outline-none {{ $activeInputBorder }} shadow-2xs transition">
                                        </td>

                                        <!-- 5. TIDAK DIJAWAB / KOSONG (0) -->
                                        <td class="py-3 px-2 text-center">
                                            <input type="number" 
                                                   id="kosong_{{ $t->id }}" 
                                                   name="kosong[{{ $t->id }}]" 
                                                   value="{{ $t->has_score ? ($t->kosong !== null ? $t->kosong : $kVal) : '' }}" 
                                                   min="0" 
                                                   max="999" 
                                                   placeholder="0"
                                                   oninput="handleRowChange({{ $t->id }}, false, true)" 
                                                   onblur="handleRowChange({{ $t->id }}, true, true)"
                                                   class="input-kosong w-14 sm:w-16 text-center font-mono font-bold text-xs sm:text-sm text-slate-700 bg-slate-50/70 focus:bg-white rounded-lg py-1 border border-slate-300 outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200 shadow-2xs transition">
                                        </td>

                                        <!-- 6. WAKTU TEMPUH (MM:SS) -->
                                        <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1 bg-white border border-slate-200 hover:border-slate-300 focus-within:border-amber-500 focus-within:ring-1 focus-within:ring-amber-200 px-1.5 py-0.5 rounded-lg shadow-2xs transition">
                                                <i class="fa-solid fa-stopwatch text-amber-500 text-[10px]"></i>
                                                <input type="text" 
                                                       id="waktu_{{ $t->id }}" 
                                                       name="waktu[{{ $t->id }}]" 
                                                       value="{{ $t->waktu ?: '' }}" 
                                                       placeholder="00:00" 
                                                       maxlength="5"
                                                       oninput="onTimeInput(this, {{ $t->id }})"
                                                       onblur="onTimeBlur(this, {{ $t->id }})"
                                                       title="Format Waktu: Menit:Detik (contoh: 07:45)"
                                                       class="input-waktu w-14 text-center font-mono font-black text-xs text-slate-900 bg-transparent outline-none tracking-wider placeholder:text-slate-300">
                                            </div>
                                        </td>

                                        <!-- 7. NILAI AKHIR -->
                                        <td class="py-3 px-1.5 text-center">
                                            <div class="inline-flex flex-col items-center">
                                                <span id="score-display-{{ $t->id }}" class="font-mono font-black text-sm {{ $scoreTextColor }} bg-white px-2.5 py-0.5 rounded-lg border border-slate-200 shadow-2xs">
                                                    {{ $t->has_score ? $scoreVal : '-' }}
                                                </span>
                                                <span id="score-formula-tag-{{ $t->id }}" class="text-[8.5px] font-mono text-slate-400 mt-0.5">
                                                    {{ ($activeFormulaType ?? 'skema2') === 'skema2' ? '50+B-S' : '(B-S)x2' }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- 8. PREDIKAT JUARA & RANKING -->
                                        <td class="py-3 px-2 text-center whitespace-nowrap">
                                            <div id="badge-container-{{ $t->id }}" class="flex justify-center">
                                                @if($t->has_score && $rVal)
                                                    @if($rVal === 1)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-400 text-slate-950 font-black text-[11px] shadow-2xs">
                                                            <i class="fa-solid fa-crown text-amber-900 text-[10px]"></i> JUARA 1 (EMAS)
                                                        </span>
                                                    @elseif($rVal === 2)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-200 text-slate-900 font-black text-[11px] shadow-2xs">
                                                            <i class="fa-solid fa-medal text-slate-600 text-[10px]"></i> JUARA 2 (PERAK)
                                                        </span>
                                                    @elseif($rVal === 3)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-700 text-amber-100 font-black text-[11px] shadow-2xs">
                                                            <i class="fa-solid fa-medal text-amber-300 text-[10px]"></i> JUARA 3 (PERUNGGU)
                                                        </span>
                                                    @elseif($rVal === 4)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-100 text-sky-900 border border-sky-300 font-bold text-[11px]">
                                                            <i class="fa-solid fa-award text-sky-600 text-[10px]"></i> JUARA HARAPAN 1
                                                        </span>
                                                    @elseif($rVal === 5)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200 font-semibold text-[11px]">
                                                            <i class="fa-solid fa-award text-sky-500 text-[10px]"></i> JUARA HARAPAN 2
                                                        </span>
                                                    @elseif($rVal === 6)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50/70 text-sky-700 border border-sky-200 font-semibold text-[11px]">
                                                            <i class="fa-solid fa-award text-sky-400 text-[10px]"></i> JUARA HARAPAN 3
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 font-medium text-[11px]">
                                                            Peringkat {{ $rVal }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-400 font-medium text-[11px] border border-slate-200">
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
                            <div class="text-[11px] text-slate-400">Perubahan nilai &amp; waktu tempuh akan langsung memperbarui klasemen juara dan sertifikat lomba.</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" onclick="location.reload()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition cursor-pointer">
                            Batalkan Perubahan
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl {{ $saveBtnClass }} font-black text-xs transition shadow-lg flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan &amp; Perbarui Klasemen Juara
                        </button>
                    </div>
                </div>
            </form>

        </div>

</div>

@push('scripts')
<script>
    let currentFormulaMode = '{{ $activeFormulaType ?? 'skema2' }}';

    function changeFormulaMode(mode) {
        currentFormulaMode = mode;
        const hiddenInput = document.getElementById('input-formula-type');
        if (hiddenInput) hiddenInput.value = mode;

        const label = document.getElementById('formula-label-display');
        if (label) {
            if (mode === 'skema2') {
                label.innerHTML = 'Nilai Akhir = 50 + Benar &minus; Salah (Skema 2)';
            } else {
                label.innerHTML = 'Nilai Akhir = (Benar &minus; Salah) &times; 2 (Standar)';
            }
        }

        const rows = document.querySelectorAll('.team-row');
        rows.forEach(r => {
            const teamId = r.getAttribute('data-team-id');
            calculateRowScore(teamId);
        });

        recalculateAllRanks();
        updateSummaryStats();
    }

    function adjustScoreInput(inputId, delta) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let cur = parseInt(input.value) || 0;
        let next = Math.max(0, Math.min(999, cur + delta));
        input.value = next;
        
        const teamId = inputId.split('_')[1];
        handleRowChange(teamId, false);
    }

    function handleRowChange(teamId, shouldSort = false, isManualKosong = false) {
        calculateRowScore(teamId);

        const bInput = document.getElementById('benar_' + teamId);
        const sInput = document.getElementById('salah_' + teamId);
        const kInput = document.getElementById('kosong_' + teamId);

        if (isManualKosong && kInput) {
            kInput.dataset.manual = 'true';
        }

        // Auto kalkulasi kolom kosong hanya jika user belum mengisi kolom kosong secara manual
        if (bInput && sInput && kInput && !isManualKosong && kInput.dataset.manual !== 'true') {
            const b = parseInt(bInput.value) || 0;
            const s = parseInt(sInput.value) || 0;
            if (bInput.value !== '' || sInput.value !== '') {
                kInput.value = Math.max(0, 50 - (b + s));
            }
        }

        recalculateAllRanks();
        updateSummaryStats();

        if (shouldSort) {
            sortRowsByRank();
        }
    }

    function calculateRowScore(teamId) {
        const bInput = document.getElementById('benar_' + teamId);
        const sInput = document.getElementById('salah_' + teamId);
        const scoreDisplay = document.getElementById('score-display-' + teamId);
        const scoreFormulaTag = document.getElementById('score-formula-tag-' + teamId);
        const row = document.getElementById('row-team-' + teamId);

        if (!bInput || !sInput) return;

        const b = parseInt(bInput.value) || 0;
        const s = parseInt(sInput.value) || 0;

        // Hitung nilai akhir sesuai formula mode aktif
        const standardScore = (b - s) * 2;
        const skema2Score = 50 + b - s;
        const finalScore = (currentFormulaMode === 'standard') ? standardScore : skema2Score;

        if (scoreDisplay) scoreDisplay.innerText = finalScore;
        if (scoreFormulaTag) {
            scoreFormulaTag.innerText = (currentFormulaMode === 'standard') ? '(B - S) x 2' : '50 + B - S';
        }

        if (row) {
            row.setAttribute('data-score', finalScore);
        }
    }

    function getRankBadgeHtml(rankVal) {
        if (rankVal === 1) {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-400 text-slate-950 font-black text-[11px] shadow-2xs"><i class="fa-solid fa-crown text-amber-900 text-[10px]"></i> JUARA 1 (EMAS)</span>';
        } else if (rankVal === 2) {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-200 text-slate-900 font-black text-[11px] shadow-2xs"><i class="fa-solid fa-medal text-slate-600 text-[10px]"></i> JUARA 2 (PERAK)</span>';
        } else if (rankVal === 3) {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-700 text-amber-100 font-black text-[11px] shadow-2xs"><i class="fa-solid fa-medal text-amber-300 text-[10px]"></i> JUARA 3 (PERUNGGU)</span>';
        } else if (rankVal === 4) {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-100 text-sky-900 border border-sky-300 font-bold text-[11px]"><i class="fa-solid fa-award text-sky-600 text-[10px]"></i> JUARA HARAPAN 1</span>';
        } else if (rankVal === 5) {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200 font-semibold text-[11px]"><i class="fa-solid fa-award text-sky-500 text-[10px]"></i> JUARA HARAPAN 2</span>';
        } else if (rankVal === 6) {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50/70 text-sky-700 border border-sky-200 font-semibold text-[11px]"><i class="fa-solid fa-award text-sky-400 text-[10px]"></i> JUARA HARAPAN 3</span>';
        } else {
            return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 font-medium text-[11px]">Peringkat ' + rankVal + '</span>';
        }
    }

    function recalculateAllRanks() {
        const tbody = document.getElementById('olimpiade-tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('.team-row'));

        const scoredRows = [];
        const unscoredRows = [];

        rows.forEach(r => {
            const teamId = r.getAttribute('data-team-id');
            const bVal = document.getElementById('benar_' + teamId)?.value;
            const sVal = document.getElementById('salah_' + teamId)?.value;
            const hasInput = (bVal !== '' && bVal !== undefined) || (sVal !== '' && sVal !== undefined);

            if (hasInput) {
                scoredRows.push(r);
            } else {
                unscoredRows.push(r);
            }
        });

        // Urutkan scored rows berdasarkan: Score DESC -> Time ASC
        scoredRows.sort((a, b) => {
            const scoreA = parseFloat(a.getAttribute('data-score')) || -999;
            const scoreB = parseFloat(b.getAttribute('data-score')) || -999;
            if (scoreA !== scoreB) {
                return scoreB - scoreA;
            }
            const timeA = parseInt(a.getAttribute('data-time-seconds')) || 999999;
            const timeB = parseInt(b.getAttribute('data-time-seconds')) || 999999;
            return timeA - timeB;
        });

        // Terapkan ranking dan badge pada scored rows
        scoredRows.forEach((row, idx) => {
            const teamId = row.getAttribute('data-team-id');
            const rankVal = idx + 1;
            row.setAttribute('data-rank', rankVal);

            // Row styling
            if (rankVal === 1) {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-amber-500/10 hover:bg-amber-500/15 border-l-4 border-l-amber-500';
            } else if (rankVal === 2) {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-slate-400/10 hover:bg-slate-400/15 border-l-4 border-l-slate-400';
            } else if (rankVal === 3) {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-amber-700/10 hover:bg-amber-700/15 border-l-4 border-l-amber-700';
            } else if (rankVal >= 4 && rankVal <= 6) {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 bg-sky-500/5 hover:bg-sky-500/10 border-l-4 border-l-sky-500';
            } else {
                row.className = 'team-row transition-colors duration-150 border-b border-slate-100 hover:bg-slate-50/80 border-l-4 border-l-transparent';
            }

            const container = document.getElementById('badge-container-' + teamId);
            if (container) {
                container.innerHTML = getRankBadgeHtml(rankVal);
            }
        });

        // Terapkan pada unscored rows
        unscoredRows.forEach(row => {
            const teamId = row.getAttribute('data-team-id');
            row.setAttribute('data-rank', 9999);
            row.className = 'team-row transition-colors duration-150 border-b border-slate-100 hover:bg-slate-50/80 border-l-4 border-l-transparent';
            const container = document.getElementById('badge-container-' + teamId);
            if (container) {
                container.innerHTML = '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-400 font-medium text-[11px] border border-slate-200">Belum Dinilai</span>';
            }
        });

        // Update 6 Podium Cards di bagian atas secara real-time
        for (let i = 1; i <= 6; i++) {
            const r = scoredRows[i - 1];
            const schoolEl = document.getElementById('podium-school-' + i);
            const teamEl = document.getElementById('podium-team-' + i);
            const scoreEl = document.getElementById('podium-score-' + i);
            const timeEl = document.getElementById('podium-time-' + i);

            if (r) {
                const teamId = r.getAttribute('data-team-id');
                const school = r.querySelector('td:nth-child(2) .font-bold')?.innerText || '-';
                const teamLabel = r.querySelector('td:nth-child(2) .text-[11px] span:first-child')?.innerText || '-';
                const score = r.getAttribute('data-score') || '0';
                const time = document.getElementById('waktu_' + teamId)?.value || '00:00';

                if (schoolEl) schoolEl.innerText = school;
                if (teamEl) teamEl.innerText = teamLabel;
                if (scoreEl) scoreEl.innerText = score;
                if (timeEl) timeEl.innerText = time;
            } else {
                if (schoolEl) schoolEl.innerText = 'Belum Ada';
                if (teamEl) teamEl.innerText = '-';
                if (scoreEl) scoreEl.innerText = '-';
                if (timeEl) timeEl.innerText = '-';
            }
        }
    }

    function sortRowsByRank() {
        const tbody = document.getElementById('olimpiade-tbody');
        if (!tbody) return;
        recalculateAllRanks();

        const rows = Array.from(tbody.querySelectorAll('.team-row'));
        rows.sort((a, b) => {
            const rankA = parseInt(a.getAttribute('data-rank')) || 9999;
            const rankB = parseInt(b.getAttribute('data-rank')) || 9999;
            if (rankA !== rankB) {
                return rankA - rankB;
            }
            const orderA = parseInt(a.getAttribute('data-order')) || 9999;
            const orderB = parseInt(b.getAttribute('data-order')) || 9999;
            return orderA - orderB;
        });

        rows.forEach(r => tbody.appendChild(r));

        const btnRank = document.getElementById('btn-sort-rank');
        const btnOrder = document.getElementById('btn-sort-order');
        if (btnRank) {
            btnRank.className = 'px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition active:scale-95 cursor-pointer';
        }
        if (btnOrder) {
            btnOrder.className = 'px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-1.5 border border-slate-300 shadow-2xs transition active:scale-95 cursor-pointer';
        }
    }

    function sortRowsByOrder() {
        const tbody = document.getElementById('olimpiade-tbody');
        if (!tbody) return;
        recalculateAllRanks();

        const rows = Array.from(tbody.querySelectorAll('.team-row'));
        rows.sort((a, b) => {
            const orderA = parseInt(a.getAttribute('data-order')) || 9999;
            const orderB = parseInt(b.getAttribute('data-order')) || 9999;
            return orderA - orderB;
        });

        rows.forEach(r => tbody.appendChild(r));

        const btnRank = document.getElementById('btn-sort-rank');
        const btnOrder = document.getElementById('btn-sort-order');
        if (btnOrder) {
            btnOrder.className = 'px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition active:scale-95 cursor-pointer';
        }
        if (btnRank) {
            btnRank.className = 'px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-1.5 border border-slate-300 shadow-2xs transition active:scale-95 cursor-pointer';
        }
    }

    function parseTimeToSeconds(timeStr) {
        if (!timeStr) return 999999;
        timeStr = String(timeStr).trim();
        if (!timeStr || timeStr === '00:00') return 999999;

        timeStr = timeStr.replace('.', ':').replace(',', ':');

        if (timeStr.includes(':')) {
            const parts = timeStr.split(':');
            if (parts.length >= 2) {
                const m = parseInt(parts[0], 10) || 0;
                const s = parseInt(parts[1], 10) || 0;
                const total = (m * 60) + s;
                return total > 0 ? total : 999999;
            }
        }

        const digits = timeStr.replace(/\D/g, '');
        if (digits.length === 3 || digits.length === 4) {
            const padded = digits.padStart(4, '0');
            const m = parseInt(padded.slice(0, 2), 10) || 0;
            const s = parseInt(padded.slice(2), 10) || 0;
            const total = (m * 60) + s;
            return total > 0 ? total : 999999;
        }

        return 999999;
    }

    function onTimeInput(input, teamId) {
        let val = input.value;
        val = val.replace(/[^0-9:]/g, '');

        const parts = val.split(':');
        if (parts.length > 2) {
            val = parts[0] + ':' + parts.slice(1).join('');
        }

        if (!val.includes(':') && val.length >= 3) {
            val = val.slice(0, 2) + ':' + val.slice(2, 4);
        }

        if (val.length > 5) {
            val = val.slice(0, 5);
        }

        input.value = val;

        const row = document.getElementById('row-team-' + teamId);
        if (row) {
            row.setAttribute('data-time-seconds', parseTimeToSeconds(val));
        }

        recalculateAllRanks();
        updateSummaryStats();
    }

    function onTimeBlur(input, teamId) {
        let val = input.value.trim();
        if (!val) {
            const row = document.getElementById('row-team-' + teamId);
            if (row) row.setAttribute('data-time-seconds', 999999);
            recalculateAllRanks();
            updateSummaryStats();
            return;
        }

        if (/^\d{1}:[0-5]\d$/.test(val)) {
            val = '0' + val;
        } else if (/^\d{1,2}:\d{1}$/.test(val)) {
            const parts = val.split(':');
            val = parts[0].padStart(2, '0') + ':' + parts[1].padStart(2, '0');
        } else if (/^\d{3,4}$/.test(val)) {
            const padded = val.padStart(4, '0');
            val = padded.slice(0, 2) + ':' + padded.slice(2);
        } else if (/^\d{1,2}$/.test(val)) {
            val = val.padStart(2, '0') + ':00';
        }

        input.value = val;

        const row = document.getElementById('row-team-' + teamId);
        if (row) {
            row.setAttribute('data-time-seconds', parseTimeToSeconds(val));
        }

        recalculateAllRanks();
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

            if ((b !== '' && b !== undefined) || (s !== '' && s !== undefined)) {
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

    // Auto calculate ranks and summary on page load
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('olimpiade-tbody')) {
            recalculateAllRanks();
            updateSummaryStats();
        }
    });
</script>
@endpush
@endsection
