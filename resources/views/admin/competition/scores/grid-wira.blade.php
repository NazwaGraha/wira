@extends('layouts.admin')

@section('title', 'Grid Nilai Pertolongan Pertama Wira - ' . $category->display_name)
@section('page_title', 'Grid Nilai: ' . $category->display_name)

@section('top_actions')
    <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-xl font-bold text-xs shadow-2xs transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Lembar Skor Utama
    </a>
    <a href="{{ route('admin.competition-scores.master-sheet', ['level' => 'Wira']) }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow transition flex items-center gap-2">
        <i class="fa-solid fa-table-list"></i> Rekap Semua Sekolah
    </a>
@endsection

@push('styles')
<style>
    .font-mono-num {
        font-family: 'JetBrains Mono', monospace;
    }
    .score-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 32px;
        padding: 0 10px;
        font-size: 12px;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }
    .score-btn:hover {
        border-color: #f59e0b;
        background: #fffbeb;
        color: #b45309;
    }
    .score-btn.active {
        background: #f59e0b !important;
        border-color: #d97706 !important;
        color: #ffffff !important;
        box-shadow: 0 3px 8px rgba(245, 158, 11, 0.4);
        transform: translateY(-1px);
    }
    .score-btn.active-zero {
        background: #64748b !important;
        border-color: #475569 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 pb-28">

    <!-- Flash message -->
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-400 text-emerald-900 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-2xl text-emerald-600"></i>
            <div class="font-bold text-sm">{{ session('success') }}</div>
        </div>
    @endif

    <!-- Main Score Form -->
    <form id="grid-score-form" action="{{ route('admin.competition-scores.grid-save', $category->id) }}" method="POST">
        @csrf
        <input type="hidden" name="round_name" value="{{ $round }}">
        <input type="hidden" name="competition_participant_team_id" value="{{ $currentTeam?->id }}">
        <input type="hidden" id="action-after" name="action_after" value="stay">
        <input type="hidden" name="next_team_id" value="{{ $nextTeam?->id }}">

        <!-- Top Header & Metadata Card (Nomor Urut, Nama Sekolah, Kasus Korban, Stopwatch, Total Nilai) -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                
                <!-- Left: Team Selector & Navigation -->
                <div class="lg:col-span-7 space-y-4 border-b lg:border-b-0 lg:border-r border-slate-200 lg:pr-6 pb-6 lg:pb-0">
                    @php
                        $categoryCode = strtoupper(trim($category->code ?? ''));
                        $catName = strtolower($category->name ?? '');
                        $genderCat = $category->gender_category ?? '';
                        $isPi = ($genderCat === 'Putri')
                            || str_ends_with($categoryCode, '-PI')
                            || str_contains($categoryCode, '-PI-')
                            || str_contains($catName, 'putri');
                        $isPa = ($genderCat === 'Putra')
                            || str_ends_with($categoryCode, '-PA')
                            || str_contains($categoryCode, '-PA-')
                            || str_contains($catName, 'putra');
                        $genderText = $isPi ? 'Putri' : ($isPa ? 'Putra' : ($category->gender_category ?: 'Umum'));
                        $genderIcon = $isPi ? 'fa-venus' : 'fa-mars';
                        $genderBadgeClass = $isPi ? 'text-rose-700 bg-rose-50 border-rose-200' : 'text-blue-700 bg-blue-50 border-blue-200';
                        $curBadgeColor = $isPi ? 'bg-rose-600' : 'bg-amber-600';
                        $borderColor = $isPi ? 'border-rose-500' : 'border-amber-500';
                    @endphp

                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-3.5 py-1.5 rounded-xl {{ $isPi ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }} font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid {{ $genderIcon }}"></i> {{ $category->code ?? ($isPi ? 'LPP-WIRA-PI' : 'LPP-WIRA-PA') }} ({{ $genderText }})
                            </span>
                            <span class="px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 font-extrabold text-xs">
                                PMR Wira (SMA / SMK / MA)
                            </span>
                        </div>

                        <!-- Prev / Next Team navigation -->
                        <div class="flex items-center gap-2 text-xs">
                            @if($prevTeam)
                                <a href="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $prevTeam->id, 'round' => $round]) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition flex items-center gap-1 border border-slate-200">
                                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Sebelumnya
                                </a>
                            @else
                                <span class="px-3 py-1.5 rounded-xl bg-slate-50 text-slate-400 font-bold border border-slate-100 cursor-not-allowed">
                                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Sebelumnya
                                </span>
                            @endif

                            @if($nextTeam)
                                <a href="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $nextTeam->id, 'round' => $round]) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold transition flex items-center gap-1 shadow-2xs">
                                    Berikutnya <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </a>
                            @else
                                <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-400 font-bold border border-slate-200 cursor-not-allowed">
                                    Berikutnya <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Dropdown Select Current Team -->
                    <div>
                        <label for="select-team-dropdown" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Pilih Regu Peserta yang Sedang Dinilai:</span>
                            <span class="{{ $genderBadgeClass }} font-extrabold text-[11px] px-2.5 py-0.5 rounded-md border flex items-center gap-1">
                                <i class="fa-solid {{ $genderIcon }}"></i> Khusus [{{ $category->code }}] PMR Wira {{ $genderText }} &bull; Total: {{ $teams->count() }} Regu
                            </span>
                        </label>
                        <div class="relative">
                            <select id="select-team-dropdown" onchange="window.location.href=this.value" class="w-full bg-slate-50 hover:bg-slate-100 border-2 {{ $borderColor }} rounded-2xl py-3 px-4 text-sm font-black text-slate-900 focus:outline-none appearance-none cursor-pointer shadow-xs transition">
                                @forelse($teams as $idx => $t)
                                    @php
                                        $tScore = $scores->get($t->id);
                                        $isScored = $tScore && ($tScore->final_score !== null && $tScore->final_score > 0);
                                        $statusLabel = $isScored ? "✓ Nilai: {$tScore->final_score} (" . ($tScore->time_recorded ?: '-') . ")" : "Belum dinilai";
                                        $optSchool = trim($t->registration?->school_name ?? '');
                                        $optRawName = trim($t->team_name ?? '');
                                        $optLabel = trim($t->team_label ?? '');

                                        $optRegu = $optLabel;
                                        if (empty($optRegu)) {
                                            if (!empty($optSchool) && str_starts_with($optRawName, $optSchool)) {
                                                $optRegu = trim(substr($optRawName, strlen($optSchool)));
                                            } else {
                                                $optRegu = $optRawName;
                                            }
                                        }
                                        if (empty($optRegu)) {
                                            $optRegu = 'Regu ' . ($idx + 1);
                                        }
                                        $displaySchool = !empty($optSchool) ? $optSchool : ($optRawName ?: 'Sekolah');
                                        $regCode = $t->registration?->registration_code ?: 'REG';
                                        $teamNo = $t->order_number ?: sprintf('A-%03d', $idx + 1);
                                    @endphp
                                    <option value="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $t->id, 'round' => $round]) }}" {{ $currentTeam?->id == $t->id ? 'selected' : '' }}>
                                        [No. {{ $teamNo }}] {{ $displaySchool }} • {{ $optRegu }} [{{ $category->code }}] ({{ $regCode }}) — {{ $statusLabel }}
                                    </option>
                                @empty
                                    <option value="" disabled selected>-- Belum ada regu peserta [{{ $category->code }}] PMR Wira {{ $genderText }} (SMA/SMK/MA) yang terdaftar --</option>
                                @endforelse
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                        </div>

                        @if($teams->isEmpty())
                            <div class="bg-amber-50 border border-amber-300 text-amber-900 p-3 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs font-bold shadow-2xs mt-2">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation text-amber-600 text-base"></i>
                                    <span>Belum ada regu peserta <strong>[{{ $category->code }}] PMR Tingkat Wira {{ $genderText }} (SMA/SMK/MA)</strong> yang terdaftar di cabang ini.</span>
                                </div>
                                <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-black text-xs transition shrink-0">
                                    <i class="fa-solid fa-plus"></i> Tambah Regu {{ $genderText }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Current Active Team Details Banner -->
                    @php
                        $curTeamIdx = $teams->search(fn($item) => $item->id === $currentTeam?->id);
                        $curOrderNo = $currentTeam?->order_number ?: ($curTeamIdx !== false ? sprintf('A-%03d', $curTeamIdx + 1) : 'A-001');
                        $curSchool = $currentTeam?->registration?->school_name ?: ($currentTeam?->team_name ?? 'Pilih Regu');
                        $curRegu = $currentTeam?->team_label;
                        if (empty($curRegu) && $currentTeam) {
                            if (!empty($currentTeam->registration?->school_name) && str_starts_with($currentTeam->team_name, $currentTeam->registration->school_name)) {
                                $curRegu = trim(substr($currentTeam->team_name, strlen($currentTeam->registration->school_name)));
                            } else {
                                $curRegu = $currentTeam->team_name;
                            }
                        }
                        if (empty($curRegu) && $currentTeam) {
                            $curRegu = 'Regu ' . ($curTeamIdx !== false ? $curTeamIdx + 1 : 1);
                        }
                    @endphp
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Regu Aktif Dinilai</span>
                            <div class="text-base font-black text-slate-900 mt-0.5 flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-lg {{ $curBadgeColor }} text-white font-mono font-black text-xs">
                                    {{ $curOrderNo }}
                                </span>
                                <span class="font-black">{{ $curSchool }}</span>
                                <span class="text-xs font-bold text-slate-600">• {{ $curRegu }}</span>
                                <span class="px-2 py-0.5 rounded-md {{ $genderBadgeClass }} text-[11px] font-black border">
                                    [{{ $category->code }}]
                                </span>
                            </div>
                        </div>
                        @if($currentTeam)
                            <div class="text-right shrink-0">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Kode Registrasi</span>
                                <span class="font-mono font-bold text-xs text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-200">
                                    {{ $currentTeam->registration?->registration_code ?: '-' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Box Kasus Resmi Sesuai Lembar Fisik Sua Bhakti Berkarya -->
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 p-4 rounded-2xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl shrink-0 shadow-md shadow-amber-500/20">
                                <i class="fa-solid fa-kit-medical"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-amber-800 block">KORBAN 1 &bull; SKENARIO KASUS LAPANGAN:</span>
                                <div class="text-sm font-black text-slate-900 mt-0.5 flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md bg-white border border-amber-300 text-amber-900 font-bold text-xs shadow-2xs">1. Syok</span>
                                    <span class="text-slate-400">&bull;</span>
                                    <span class="px-2 py-0.5 rounded-md bg-white border border-amber-300 text-amber-900 font-bold text-xs shadow-2xs">2. Patah Tulang</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right hidden sm:block">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Kategori</span>
                            <span class="text-xs font-black text-slate-800">Pertolongan Pertama Wira</span>
                        </div>
                    </div>

                </div>

                <!-- Right: Stopwatch & Live Score Preview -->
                <div class="lg:col-span-5 space-y-4">
                    
                    <!-- Stopwatch & Input Koreksi Waktu Box (Compact Stopwatch + Big Manual Time Input) -->
                    <div class="bg-slate-900 text-white p-5 rounded-3xl shadow-xl border-2 border-slate-700 space-y-4">
                        
                        <!-- 1. Baris Atas: Stopwatch Ringkas (Tidak Terlalu Besar) -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-3.5 border-b border-slate-800">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-xs font-bold text-slate-400">Stopwatch:</span>
                                <span id="stopwatch-display" class="font-mono-num font-black text-xl text-white tracking-wider bg-slate-950 px-3 py-1 rounded-lg border border-slate-800 select-none shadow-inner">
                                    {{ $existingTime ?: '00:00' }}
                                </span>
                            </div>

                            <!-- Tombol Kontrol Timer Ringkas -->
                            <div class="flex items-center gap-1.5">
                                <button type="button" id="btn-timer-start" onclick="startTimer()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-3 py-1.5 rounded-lg transition flex items-center gap-1 active:scale-95 shadow-sm">
                                    <i class="fa-solid fa-play text-[9px]"></i> Mulai
                                </button>
                                <button type="button" id="btn-timer-pause" onclick="pauseTimer()" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-3 py-1.5 rounded-lg transition flex items-center gap-1 active:scale-95 shadow-sm">
                                    <i class="fa-solid fa-pause text-[9px]"></i> Jeda
                                </button>
                                <button type="button" id="btn-timer-reset" onclick="resetTimer()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs p-1.5 rounded-lg border border-slate-700 transition active:scale-95" title="Reset Stopwatch">
                                    <i class="fa-solid fa-rotate-right text-xs"></i>
                                </button>
                            </div>

                            <!-- Info Batas & Fase Waktu Ringkas -->
                            <div class="flex items-center gap-2 text-[11px] font-medium text-slate-400">
                                <span class="bg-slate-800 text-amber-400 px-2 py-0.5 rounded text-[10px] font-mono font-bold border border-slate-700">Max 08:00</span>
                                <span class="hidden sm:inline text-slate-600">•</span>
                                <span class="hidden sm:inline text-[11px] text-slate-400">1m Kasus + 7m Tindakan</span>
                            </div>
                        </div>

                        <!-- 2. Baris Utama: Kotak Input Koreksi Manual Besar & Dominan -->
                        <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border-2 border-amber-400 rounded-2xl p-4 shadow-lg space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-md bg-amber-400 text-slate-950 font-black text-[11px] uppercase tracking-wider flex items-center gap-1 shadow-xs">
                                        <i class="fa-solid fa-pen-to-square"></i> KOREKSI WAKTU TEMPUH
                                    </span>
                                    <span class="text-xs font-bold text-slate-300 hidden sm:inline">(Waktu Resmi Regu)</span>
                                </div>
                                <span class="text-[11px] text-emerald-400 bg-emerald-950/90 border border-emerald-800 px-2.5 py-0.5 rounded-full font-bold flex items-center gap-1.5">
                                    <i class="fa-solid fa-database text-[10px]"></i> Tersimpan ke Database
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                                <div class="sm:col-span-7 relative">
                                    <input type="text" id="time-recorded-input" name="time_recorded" value="{{ $existingTime ?: '00:00' }}" onchange="syncTimerInput(this.value)" placeholder="00:00" class="w-full bg-slate-950 border-2 border-amber-400 rounded-xl py-2.5 px-3 text-center font-mono-num font-black text-3xl sm:text-4xl tracking-widest text-amber-300 shadow-inner focus:outline-none focus:ring-4 focus:ring-amber-400/30">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] font-mono font-extrabold text-slate-500 uppercase">
                                        MM:SS
                                    </span>
                                </div>
                                <div class="sm:col-span-5 flex flex-col gap-1.5">
                                    <button type="button" onclick="copyTimerToInput()" class="w-full py-2.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition shadow-md shadow-amber-500/20 flex items-center justify-center gap-1.5 active:scale-95">
                                        <i class="fa-solid fa-arrow-down-long text-xs"></i>
                                        <span>Salin dari Stopwatch</span>
                                    </button>
                                    <div class="flex items-center justify-between text-[10px] text-slate-400 px-1">
                                        <span>*Ketik manual bebas</span>
                                        <span id="timer-warning-text" class="text-rose-400 font-bold"><i class="fa-solid fa-triangle-exclamation"></i> Max 08:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Live Total Score Counter (Max: 680) -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-300 p-4.5 rounded-2xl flex items-center justify-between shadow-sm">
                        <div>
                            <div class="text-xs font-black text-amber-950 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-calculator text-amber-600"></i> TOTAL NILAI PRAKTIK WIRA
                            </div>
                            <div class="text-[11px] text-amber-800 font-medium mt-0.5">Penjumlahan Otomatis Seluruh Nilai Checklist</div>
                        </div>
                        <div class="text-right">
                            <div class="text-4xl font-black text-amber-700 font-mono-num tracking-tight">
                                <span id="grand-total-display">0</span> <span class="text-base font-bold text-amber-600 font-sans">/ 680</span>
                            </div>
                            <span id="score-percentage-badge" class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">
                                0%
                            </span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- MAIN RUBRIC TABLE (GRID NILAI RESMI SUA BHAKTI BERKARYA) -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mt-6">
            
            <!-- Table Action Header -->
            <div class="p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block animate-ping"></span>
                    <span class="font-black text-sm text-slate-900">Petunjuk Juri: Klik salah satu pilihan angka nilai pada setiap uraian kegiatan. Total otomatis terakumulasi.</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setAllMaxValues()" class="text-xs font-bold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 px-3.5 py-1.5 rounded-xl border border-emerald-300 transition shadow-2xs flex items-center gap-1 active:scale-95">
                        <i class="fa-solid fa-circle-check"></i> Pilih Nilai Maksimal
                    </button>
                    <button type="button" onclick="resetAllRubric()" class="text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 px-3.5 py-1.5 rounded-xl border border-slate-300 transition shadow-2xs flex items-center gap-1 active:scale-95">
                        <i class="fa-solid fa-rotate-left"></i> Reset Pilihan
                    </button>
                </div>
            </div>

            @php
                $sections = [
                    1 => [
                        'title' => 'APD',
                        'max' => 20,
                        'items' => [
                            '1_a' => ['label' => 'Terpasang sarung tangan lateks', 'options' => [0, 5, 10]],
                            '1_b' => ['label' => 'Terpasang masker', 'options' => [0, 5, 10]],
                        ]
                    ],
                    2 => [
                        'title' => 'PENILAIAN KEADAAN',
                        'max' => 30,
                        'items' => [
                            '2_a' => ['label' => 'Menjelaskan mekanisme kejadian', 'options' => [0, 5, 10]],
                            '2_b' => ['label' => 'Memperkenalkan diri dan tim', 'options' => [0, 5, 10]],
                            '2_c' => ['label' => 'Meminta izin dan saksi', 'options' => [0, 5, 10]],
                        ]
                    ],
                    3 => [
                        'title' => 'PENILAIAN & PENATALAKSANAAN KEAMANAN',
                        'max' => 10,
                        'items' => [
                            '3_a' => ['label' => 'Menyimpulkan keadaan lokasi kejadian', 'options' => [0, 5, 10]],
                        ]
                    ],
                    4 => [
                        'title' => 'PENILAIAN DINI',
                        'max' => 175,
                        'items' => [
                            '4_a' => ['label' => 'Menyimpulkan kesan umum kasus medis', 'options' => [0, 30]],
                            '4_b' => ['label' => 'Cek respon suara dan nyeri', 'options' => [0, 10, 20]],
                            '4_c' => ['label' => 'Meminta bantuan Guru', 'options' => [0, 10]],
                            '4_d' => ['label' => 'Menstabilkan korban ke posisi terlentang', 'options' => [0, 5, 10]],
                            '4_e' => ['label' => 'Buka jalan nafas (ADTD)', 'options' => [0, 10, 25]],
                            '4_f' => ['label' => 'Cek nafas (LDR 3-5 detik) dan menyimpulkan', 'options' => [0, 15, 25]],
                            '4_g' => ['label' => 'Cek nadi radialis (5-10 detik) dan menyimpulkan', 'options' => [0, 15, 30]],
                            '4_h' => ['label' => 'Kontrol perdarahan 5 bagian tubuh dan menyimpulkan', 'options' => [5, 15, 25]],
                        ]
                    ],
                    5 => [
                        'title' => 'PEMERIKSAAN TANDA VITAL',
                        'max' => 100,
                        'items' => [
                            '5_a' => ['label' => 'Nadi 1 menit (diukur dan diperiksa dengan tepat)', 'options' => [5, 10, 20]],
                            '5_b' => ['label' => 'Nafas 1 menit (diukur dan diperiksa dengan tepat)', 'options' => [5, 10, 20]],
                            '5_c' => ['label' => 'Suhu (menyimpulkan hasil yang didapat dari korban)', 'options' => [5, 10, 20]],
                            '5_d' => ['label' => 'Kondisi, keadaan, dan warna kulit', 'options' => [5, 10, 20]],
                            '5_e' => ['label' => 'Menyimpulkan adanya gangguan medis (syok)', 'options' => [5, 10, 20]],
                        ]
                    ],
                    6 => [
                        'title' => 'PENANGANAN SYOK',
                        'max' => 160,
                        'items' => [
                            '6_a' => ['label' => 'Cek respon dan membuka jalan nafas', 'options' => [10, 25, 40]],
                            '6_b' => ['label' => 'Tinggikan tungkai dan melonggarkan bagian yang mengikat', 'options' => [10, 25, 40]],
                            '6_c' => ['label' => 'Selimuti korban seluruh bagian tubuh', 'options' => [10, 25, 40]],
                            '6_d' => ['label' => 'Memberi oksigen', 'options' => [10, 25, 40]],
                        ]
                    ],
                    7 => [
                        'title' => 'PENANGANAN PATAH TULANG',
                        'max' => 130,
                        'items' => [
                            '7_a' => ['label' => 'Tepat menentukan posisi luka (kanan, kiri, atas, bawah)', 'options' => [0, 10]],
                            '7_b' => ['label' => 'Fiksasi manual (pertahankan posisi)', 'options' => [10, 25, 40]],
                            '7_c' => ['label' => 'Mengukur bidai & masukkan mitela sesuai ketentuan', 'options' => [10, 25, 40]],
                            '7_d' => ['label' => 'Pengikatan pada sendi yang banyak bergerak', 'options' => [10, 25, 40]],
                        ]
                    ],
                    8 => [
                        'title' => 'CEK FISIK (PLNB)',
                        'max' => 25,
                        'items' => [
                            '8_a' => ['label' => 'Kepala', 'options' => [0, 3, 5]],
                            '8_b' => ['label' => 'Leher', 'options' => [0, 3, 5]],
                            '8_c' => ['label' => 'Badan termasuk perut dan tulang punggung', 'options' => [0, 3, 5]],
                            '8_d' => ['label' => 'Alat gerak atas & GSS', 'options' => [0, 3, 5]],
                            '8_e' => ['label' => 'Alat gerak bawah & GSS', 'options' => [0, 3, 5]],
                        ]
                    ],
                    9 => [
                        'title' => 'RIWAYAT PENDERITA',
                        'max' => 10,
                        'items' => [
                            '9_a' => ['label' => 'Wawancara KOMPAK', 'options' => [0, 5, 10]],
                        ]
                    ],
                    10 => [
                        'title' => 'PELAPORAN & KERJASAMA',
                        'max' => 20,
                        'items' => [
                            '10_a' => ['label' => 'Kartu pelaporan diisi dengan tepat dan lengkap', 'options' => [0, 5, 10]],
                            '10_b' => ['label' => 'Kerjasama tim', 'options' => [0, 5, 10]],
                        ]
                    ],
                ];
            @endphp

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                            <th class="py-3.5 px-4 w-14 text-center border-r border-slate-800">NO</th>
                            <th class="py-3.5 px-6 border-r border-slate-800">URAIAN KEGIATAN PERTOLONGAN PERTAMA</th>
                            <th class="py-3.5 px-6 text-center border-r border-slate-800 w-64">
                                PILIHAN NILAI CHECKLIST<br>
                                <span class="text-[9px] font-normal text-slate-300 tracking-normal">(Klik tombol angka yang sesuai)</span>
                            </th>
                            <th class="py-3.5 px-4 text-center w-28 bg-black">SKOR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium text-slate-800">
                        @foreach ($sections as $secNum => $sec)
                            <!-- Section Header Row -->
                            <tr class="bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider">
                                <td class="py-2.5 px-4 text-center font-mono">{{ $secNum }}</td>
                                <td colspan="2" class="py-2.5 px-6">{{ $sec['title'] }}</td>
                                <td class="py-2.5 px-4 text-center bg-amber-500 font-mono-num font-black text-xs text-slate-950">
                                    <span id="subtotal-display-{{ $secNum }}">0</span> / {{ $sec['max'] }}
                                </td>
                            </tr>
                            <input type="hidden" name="subtotals[{{ $secNum }}]" id="subtotal-input-{{ $secNum }}" value="0">

                            <!-- Item Rows -->
                            @foreach ($sec['items'] as $itemKey => $item)
                                @php
                                    $currVal = isset($existingRubric[$itemKey]) ? (int)$existingRubric[$itemKey] : null;
                                    $maxOption = end($item['options']);
                                @endphp
                                <tr id="row_{{ $itemKey }}" class="hover:bg-slate-50 transition" data-key="{{ $itemKey }}" data-section="{{ $secNum }}" data-max="{{ $maxOption }}">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono">&bull;</td>
                                    <td class="py-3 px-6 font-bold text-slate-800 leading-relaxed">{{ $item['label'] }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <input type="hidden" name="rubric[{{ $itemKey }}]" id="input_{{ $itemKey }}" class="rubric-input" data-section="{{ $secNum }}" value="{{ $currVal !== null ? $currVal : $item['options'][0] }}">

                                        <div class="inline-flex items-center justify-center gap-1.5 p-1 bg-slate-100/70 rounded-xl border border-slate-200">
                                            @foreach ($item['options'] as $opt)
                                                @php
                                                    $isActive = ($currVal !== null && $currVal === $opt);
                                                @endphp
                                                <button type="button" onclick="selectScoreOption('{{ $itemKey }}', {{ $opt }})" data-val="{{ $opt }}" class="score-btn {{ $isActive ? ($opt == 0 ? 'active-zero' : 'active') : '' }}">
                                                    {{ $opt }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono-num font-black text-sm text-slate-900 bg-slate-50/60 row-score-cell">
                                        <span id="display_{{ $itemKey }}" class="row-score-display {{ $currVal === null || $currVal === 0 ? 'text-slate-400' : 'text-amber-700' }}">
                                            {{ $currVal !== null ? $currVal : $item['options'][0] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-900 text-white font-black text-sm">
                            <td colspan="2" class="py-4 px-6 uppercase tracking-wider text-amber-300">
                                TOTAL AKUMULASI NILAI PRAKTIK PERTOLONGAN PERTAMA WIRA
                            </td>
                            <td class="py-4 px-6 text-center text-xs text-slate-400 font-bold">
                                Waktu Pelaksanaan: <span id="bottom-time-display" class="text-white font-mono font-bold text-sm">{{ $existingTime ?: '06:45' }}</span> (Max: 08:00)
                            </td>
                            <td class="py-4 px-4 text-center bg-amber-500 text-slate-950 font-mono-num font-black text-xl">
                                <span id="grand-total-row">0</span> <span class="text-xs font-sans font-bold">/ 680</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Sticky Bottom Action Footer -->
            <div class="p-6 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white rounded-2xl border border-slate-200 flex items-center gap-3">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Status Pengisian:</div>
                        <div id="items-completion-status" class="text-xs font-black text-emerald-600 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> 32 / 32 Item Lengkap
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                        <span>Otomatis sinkron ke Lembar Skor Utama & Peringkat Klasemen</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="submitScoreForm('stay')" class="px-5 py-3 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 font-extrabold text-xs border border-slate-300 transition shadow-xs flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-floppy-disk text-slate-500"></i> Simpan Saja
                    </button>
                    @if($nextTeam)
                        <button type="button" onclick="submitScoreForm('next')" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-slate-950 font-black text-xs transition shadow-lg shadow-amber-500/20 flex items-center gap-2 active:scale-95">
                            <span>SIMPAN & LANJUT REGU BERIKUTNYA</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    @else
                        <button type="button" onclick="submitScoreForm('back')" class="px-6 py-3 rounded-2xl bg-slate-900 hover:bg-black text-white font-black text-xs transition shadow-md flex items-center gap-2 active:scale-95">
                            <i class="fa-solid fa-check-double text-amber-400"></i>
                            <span>Simpan & Selesai</span>
                        </button>
                    @endif
                </div>
            </div>

        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        recalculateGrandTotal();
        initStopwatchFromInput();
    });

    // Pilihan Nilai Checklist (Pill Button)
    function selectScoreOption(key, val) {
        const input = document.getElementById('input_' + key);
        if (!input) return;
        input.value = val;

        const row = document.getElementById('row_' + key);
        if (!row) return;

        // Reset semua tombol pada baris ini
        row.querySelectorAll('.score-btn').forEach(btn => {
            btn.classList.remove('active', 'active-zero');
        });

        // Aktifkan tombol yang diklik
        const targetBtn = row.querySelector(`.score-btn[data-val="${val}"]`);
        if (targetBtn) {
            targetBtn.classList.add(val === 0 ? 'active-zero' : 'active');
        }

        // Tampilkan angka pada kolom skor
        const display = document.getElementById('display_' + key);
        if (display) {
            display.textContent = val;
            display.className = 'row-score-display ' + (val === 0 ? 'text-slate-400' : 'text-amber-700 font-black');
        }

        recalculateGrandTotal();
    }

    // Hitung Ulang Subtotal Bagian & Total Nilai Akhir
    function recalculateGrandTotal() {
        let grandTotal = 0;

        for (let sec = 1; sec <= 10; sec++) {
            let secTotal = 0;
            document.querySelectorAll(`input.rubric-input[data-section="${sec}"]`).forEach(inp => {
                secTotal += parseInt(inp.value || 0);
            });

            const secInput = document.getElementById(`subtotal-input-${sec}`);
            if (secInput) secInput.value = secTotal;

            const secDisplay = document.getElementById(`subtotal-display-${sec}`);
            if (secDisplay) secDisplay.textContent = secTotal;

            grandTotal += secTotal;
        }

        // Update Grand Total Displays
        document.getElementById('grand-total-display').textContent = grandTotal;
        document.getElementById('grand-total-row').textContent = grandTotal;

        // Update Percentage Badge
        const pct = ((grandTotal / 680) * 100).toFixed(1);
        const badge = document.getElementById('score-percentage-badge');
        if (badge) {
            badge.textContent = `${pct}% &bull; ` + (pct >= 85 ? 'Sangat Baik' : (pct >= 70 ? 'Baik' : 'Cukup'));
            badge.innerHTML = `${pct}% &bull; ` + (pct >= 85 ? 'Sangat Baik' : (pct >= 70 ? 'Baik' : 'Cukup'));
        }
    }

    // Set Semua Opsi Nilai Maksimal
    function setAllMaxValues() {
        document.querySelectorAll('tr[id^="row_"]').forEach(row => {
            const maxVal = row.dataset.max;
            const btnMax = row.querySelector(`.score-btn[data-val="${maxVal}"]`);
            if (btnMax) {
                btnMax.click();
            }
        });
    }

    // Reset Semua Opsi Nilai ke 0 (atau opsi pertama)
    function resetAllRubric() {
        if (!confirm('Apakah Anda yakin ingin mengosongkan/mereset seluruh pilihan rubrik nilai ini?')) return;
        document.querySelectorAll('tr[id^="row_"]').forEach(row => {
            const firstBtn = row.querySelector('.score-btn');
            if (firstBtn) {
                firstBtn.click();
            }
        });
    }

    // Submit scoring form
    function submitScoreForm(action) {
        document.getElementById('action-after').value = action;
        document.getElementById('grid-score-form').submit();
    }

    // ================= STOPWATCH TIMER RESMI (8 MENIT TOTAL) =================
    let timerInterval = null;
    let timerSeconds = 0;
    let timerRunning = false;
    const MAX_SECONDS_ALLOWED = 480; // 8 Menit (08:00)

    function initStopwatchFromInput() {
        const inputVal = document.getElementById('time-recorded-input').value.trim();
        if (inputVal.includes(':')) {
            const parts = inputVal.split(':');
            timerSeconds = (parseInt(parts[0]) || 0) * 60 + (parseInt(parts[1]) || 0);
        } else if (!isNaN(parseInt(inputVal))) {
            timerSeconds = parseInt(inputVal) || 0;
        }
    }

    function formatTime(totalSec) {
        const m = Math.floor(totalSec / 60).toString().padStart(2, '0');
        const s = (totalSec % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    function updateTimerDisplay() {
        const str = formatTime(timerSeconds);
        document.getElementById('stopwatch-display').textContent = str;
        document.getElementById('time-recorded-input').value = str;
        document.getElementById('bottom-time-display').textContent = str;

        // Peringatan Batas Waktu 08:00
        const warning = document.getElementById('timer-warning-text');
        if (timerSeconds >= MAX_SECONDS_ALLOWED) {
            pauseTimer();
            if (warning) {
                warning.className = 'text-rose-500 font-black animate-bounce text-xs';
                warning.innerHTML = '<i class="fa-solid fa-circle-exclamation text-base"></i> BATAS 08:00 TERCAPAI! PENANGANAN DIBERHENTIKAN OTOMATIS.';
            }
            alert('Waktu Pertolongan Pertama telah mencapai batas maksimal 8 menit (08:00)!\nPenanganan korban wajib diberhentikan.');
        } else {
            if (warning) {
                warning.className = 'text-rose-400 font-bold';
                warning.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Lewat 08:00 penanganan wajib diberhentikan';
            }
        }
    }

    function startTimer() {
        if (timerRunning) return;
        timerRunning = true;
        document.getElementById('btn-timer-start').classList.add('opacity-50');
        timerInterval = setInterval(() => {
            timerSeconds++;
            updateTimerDisplay();
        }, 1000);
    }

    function pauseTimer() {
        if (!timerRunning) return;
        timerRunning = false;
        clearInterval(timerInterval);
        document.getElementById('btn-timer-start').classList.remove('opacity-50');
    }

    function resetTimer() {
        pauseTimer();
        timerSeconds = 0;
        updateTimerDisplay();
    }

    function syncTimerInput(val) {
        document.getElementById('stopwatch-display').textContent = val || '00:00';
        document.getElementById('bottom-time-display').textContent = val || '00:00';
        initStopwatchFromInput();
    }

    function copyTimerToInput() {
        const timerVal = document.getElementById('stopwatch-display').textContent.trim();
        document.getElementById('time-recorded-input').value = timerVal;
        document.getElementById('bottom-time-display').textContent = timerVal;
        initStopwatchFromInput();
    }
</script>
@endpush
