@extends('layouts.admin')

@section('title', 'Grid Nilai Pertolongan Pertama Mula - ' . $category->display_name)
@section('page_title', 'Grid Nilai: ' . $category->display_name)

@section('top_actions')
    <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-xl font-bold text-xs shadow-2xs transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Lembar Skor Utama
    </a>
    <a href="{{ route('admin.competition-scores.master-sheet', ['level' => 'Mula']) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow transition flex items-center gap-2">
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
        border-color: #10b981;
        background: #ecfdf5;
        color: #047857;
    }
    .score-btn.active {
        background: #059669 !important;
        border-color: #047857 !important;
        color: #ffffff !important;
        box-shadow: 0 3px 8px rgba(5, 150, 105, 0.4);
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
                        $genderIcon = $isPi ? 'fa-venus' : ($isPa ? 'fa-mars' : 'fa-children');
                        $genderBadgeClass = $isPi ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200';
                        $curBadgeColor = $isPi ? 'bg-rose-600' : 'bg-emerald-600';
                        $borderColor = $isPi ? 'border-rose-500' : 'border-emerald-500';
                    @endphp

                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-3.5 py-1.5 rounded-xl {{ $genderBadgeClass }} border font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid {{ $genderIcon }}"></i> {{ $category->code ?? ($isPi ? 'LPP-MULA-PI' : 'LPP-MULA-PA') }} ({{ $genderText }})
                            </span>
                            <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-900 border border-emerald-300 font-extrabold text-xs">
                                PMR Tingkat Mula (SD / MI)
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
                                <i class="fa-solid {{ $genderIcon }}"></i> Khusus [{{ $category->code }}] PMR Mula {{ $genderText }} &bull; Total: {{ $teams->count() }} Regu
                            </span>
                        </label>
                        <div class="relative">
                            <select id="select-team-dropdown" onchange="window.location.href = this.value" class="w-full bg-slate-50 hover:bg-slate-100 border-2 {{ $borderColor }} rounded-2xl py-3 px-4 text-sm font-black text-slate-900 focus:outline-none appearance-none cursor-pointer shadow-xs transition">
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
                                    <option value="" disabled selected>-- Belum ada regu peserta [{{ $category->code }}] PMR Mula {{ $genderText }} (SD/MI) yang terdaftar --</option>
                                @endforelse
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                        </div>

                        @if($teams->isEmpty())
                            <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 p-3 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs font-bold shadow-2xs mt-2">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation text-emerald-600 text-base"></i>
                                    <span>Belum ada regu peserta <strong>[{{ $category->code }}] PMR Tingkat Mula {{ $genderText }} (SD/MI)</strong> yang terdaftar di cabang ini.</span>
                                </div>
                                <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs transition shrink-0">
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

                    <!-- Box Kasus Resmi Sesuai Lembar Fisik Mula -->
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 p-4 rounded-2xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shrink-0 shadow-md shadow-emerald-500/20">
                                <i class="fa-solid fa-kit-medical"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-800 block">KORBAN 1 • SKENARIO KASUS LAPANGAN:</span>
                                <div class="text-sm font-black text-slate-900 mt-0.5 flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md bg-white border border-emerald-300 text-emerald-900 font-extrabold text-xs shadow-2xs">1. Sayat</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right hidden sm:block">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Kategori</span>
                            <span class="text-xs font-black text-slate-800">Pertolongan Pertama Mula</span>
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
                                    {{ $score?->time_recorded ?: '00:00' }}
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
                                <span class="bg-slate-800 text-emerald-300 px-2 py-0.5 rounded text-[10px] font-mono font-bold border border-slate-700">Max 07:00</span>
                                <span class="hidden sm:inline text-slate-600">•</span>
                                <span class="hidden sm:inline text-[11px] text-slate-400">1m Kasus + 6m Tindakan</span>
                            </div>
                        </div>

                        <!-- 2. Baris Utama: Kotak Input Koreksi Manual Besar & Dominan -->
                        <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border-2 border-emerald-400 rounded-2xl p-4 shadow-lg space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-md bg-emerald-600 text-white font-black text-[11px] uppercase tracking-wider flex items-center gap-1 shadow-xs">
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
                                    <input type="text" id="time-recorded-input" name="time_recorded" value="{{ $score?->time_recorded ?: '00:00' }}" onchange="syncTimerInput(this.value)" placeholder="00:00" class="w-full bg-slate-950 border-2 border-emerald-400 rounded-xl py-2.5 px-3 text-center font-mono-num font-black text-3xl sm:text-4xl tracking-widest text-emerald-300 shadow-inner focus:outline-none focus:ring-4 focus:ring-emerald-400/30">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] font-mono font-extrabold text-slate-500 uppercase">
                                        MM:SS
                                    </span>
                                </div>
                                <div class="sm:col-span-5 flex flex-col gap-1.5">
                                    <button type="button" onclick="copyTimerToInput()" class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition shadow-md shadow-emerald-500/20 flex items-center justify-center gap-1.5 active:scale-95">
                                        <i class="fa-solid fa-arrow-down-long text-xs"></i>
                                        <span>Salin dari Stopwatch</span>
                                    </button>
                                    <div class="flex items-center justify-between text-[10px] text-slate-400 px-1">
                                        <span>*Ketik manual bebas</span>
                                        <span id="timer-warning-text" class="text-rose-400 font-bold"><i class="fa-solid fa-triangle-exclamation"></i> Max 07:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Live Total Score Counter (Mula Max 465 Poin) -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-300 p-4.5 rounded-2xl flex items-center justify-between shadow-sm">
                        <div>
                            <div class="text-xs font-black text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-calculator text-emerald-600"></i> TOTAL NILAI PRAKTIK MULA
                            </div>
                            <div class="text-[11px] text-emerald-800 font-medium mt-0.5">Penjumlahan Otomatis Seluruh Nilai Checklist</div>
                        </div>
                        <div class="text-right">
                            <div class="text-4xl font-black text-emerald-700 font-mono-num tracking-tight">
                                <span id="grand-total-display">0</span> <span class="text-base font-bold text-emerald-600 font-sans">/ 465</span>
                            </div>
                            <span id="score-percentage-badge" class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">
                                0% • Mulai Penilaian
                            </span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- MAIN RUBRIC TABLE (GRID NILAI RESMI SUA BHAKTI BERKARYA MULA) -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            
            <!-- Table Action Header -->
            <div class="p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                    <span class="font-black text-sm text-slate-900">Petunjuk Juri: Klik salah satu pilihan angka nilai pada setiap uraian kegiatan. Total otomatis terakumulasi.</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setAllMaxValues()" class="text-xs font-bold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 px-3.5 py-1.5 rounded-xl border border-emerald-300 transition shadow-2xs flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> Pilih Nilai Maksimal
                    </button>
                    <button type="button" onclick="resetAllRubric()" class="text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 px-3.5 py-1.5 rounded-xl border border-slate-300 transition shadow-2xs flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left"></i> Reset Pilihan
                    </button>
                </div>
            </div>

            @php
                // DEFINISI RESMI 9 BAGIAN & 28 BUTIR PERTOLONGAN PERTAMA MULA (MAX: 465 POIN)
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
                        'max' => 160,
                        'items' => [
                            '4_a' => ['label' => 'Menyimpulkan kesan umum kasus medis', 'options' => [0, 30]],
                            '4_b' => ['label' => 'Cek respon suara dan nyeri', 'options' => [0, 10, 20]],
                            '4_c' => ['label' => 'Meminta bantuan Guru', 'options' => [0, 10]],
                            '4_d' => ['label' => 'Menstabilkan korban ke posisi terlentang', 'options' => [0, 5, 10]],
                            '4_e' => ['label' => 'Cek nafas (LDR 3-5 detik) dan menyimpulkan', 'options' => [0, 15, 30]],
                            '4_f' => ['label' => 'Cek nadi radialis (5-10 detik) dan menyimpulkan', 'options' => [0, 15, 30]],
                            '4_g' => ['label' => 'Kontrol perdarahan 5 bagian tubuh dan menyimpulkan', 'options' => [0, 15, 30]],
                        ]
                    ],
                    5 => [
                        'title' => 'PEMERIKSAAN TANDA VITAL',
                        'max' => 80,
                        'items' => [
                            '5_a' => ['label' => 'Nadi 1 menit (diukur dan diperiksa dengan tepat)', 'options' => [5, 10, 20]],
                            '5_b' => ['label' => 'Nafas 1 menit (diukur dan diperiksan dengan tepat)', 'options' => [5, 10, 20]],
                            '5_c' => ['label' => 'Suhu (menyimpulkan hasil yang didapat dari korban)', 'options' => [5, 10, 20]],
                            '5_d' => ['label' => 'Kondisi, keadaan, dan warna kulit', 'options' => [5, 10, 20]],
                        ]
                    ],
                    6 => [
                        'title' => 'PENANGANAN SAYAT',
                        'max' => 110,
                        'items' => [
                            '6_a' => ['label' => 'Tepat menentukan posisi luka (kanan, kiri, atas, bawah)', 'options' => [0, 10]],
                            '6_b' => ['label' => 'Bersihkan sekitar luka dengan air bersih & beri antiseptik', 'options' => [10, 25, 50]],
                            '6_c' => ['label' => 'Balut luka dengan tepat & istirahatkan', 'options' => [10, 25, 50]],
                        ]
                    ],
                    7 => [
                        'title' => 'CEK FISIK (PLNB)',
                        'max' => 25,
                        'items' => [
                            '7_a' => ['label' => 'Kepala', 'options' => [0, 3, 5]],
                            '7_b' => ['label' => 'Leher', 'options' => [0, 3, 5]],
                            '7_c' => ['label' => 'Badan termasuk perut dan tulang punggung', 'options' => [0, 3, 5]],
                            '7_d' => ['label' => 'Alat gerak atas & GSS', 'options' => [0, 3, 5]],
                            '7_e' => ['label' => 'Alat gerak bawah & GSS', 'options' => [0, 3, 5]],
                        ]
                    ],
                    8 => [
                        'title' => 'RIWAYAT PENDERITA',
                        'max' => 10,
                        'items' => [
                            '8_a' => ['label' => 'Wawancara KOMPAK', 'options' => [0, 5, 10]],
                        ]
                    ],
                    9 => [
                        'title' => 'PELAPORAN & KERJASAMA',
                        'max' => 20,
                        'items' => [
                            '9_a' => ['label' => 'Kartu pelaporan diisi dengan tepat dan lengkap', 'options' => [0, 5, 10]],
                            '9_b' => ['label' => 'Kerjasama tim', 'options' => [0, 5, 10]],
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
                            <tr class="bg-emerald-600 text-white font-black text-xs uppercase tracking-wider">
                                <td class="py-2.5 px-4 text-center font-mono">{{ $secNum }}</td>
                                <td colspan="2" class="py-2.5 px-6">{{ $sec['title'] }}</td>
                                <td class="py-2.5 px-4 text-center bg-emerald-700 font-mono-num font-black text-xs text-white">
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
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono">•</td>
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
                                        <span id="display_{{ $itemKey }}" class="row-score-display {{ $currVal === null || $currVal === 0 ? 'text-slate-400' : 'text-emerald-700' }}">
                                            {{ $currVal !== null ? $currVal : $item['options'][0] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-900 text-white font-black text-sm">
                            <td colspan="2" class="py-4 px-6 uppercase tracking-wider text-emerald-300">
                                TOTAL AKUMULASI NILAI PRAKTIK PERTOLONGAN PERTAMA MULA
                            </td>
                            <td class="py-4 px-6 text-center text-xs text-slate-400 font-bold">
                                Waktu Tempuh Tercatat: <span id="bottom-time-display" class="text-white font-mono font-bold text-sm">{{ $score?->time_recorded ?: '00:00' }}</span>
                            </td>
                            <td class="py-4 px-4 text-center bg-emerald-600 text-white font-mono-num font-black text-xl">
                                <span id="grand-total-row">0</span> <span class="text-xs font-sans font-bold text-emerald-200">/ 465</span>
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
                        <div class="text-xs font-black text-emerald-600 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> 28 / 28 Item Lengkap
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                        <span>Otomatis sinkron ke Lembar Skor Utama & Peringkat Klasemen</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="submitScoreForm('stay')" class="px-5 py-3 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 font-extrabold text-xs border border-slate-300 transition shadow-xs flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-slate-500"></i> Simpan Saja
                    </button>
                    @if($nextTeam)
                        <button type="button" onclick="submitScoreForm('next')" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs transition shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                            <span>SIMPAN & LANJUT REGU BERIKUTNYA</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    @else
                        <button type="button" onclick="submitScoreForm('stay')" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                            <span>SIMPAN & SELESAI SEMUA REGU</span>
                            <i class="fa-solid fa-check-double"></i>
                        </button>
                    @endif
                </div>
            </div>

        </div>

        <!-- Catatan / Keterangan Tambahan Juri -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Catatan & Evaluasi Juri Lapangan (Opsional)
            </h3>
            <textarea name="notes" rows="3" placeholder="Tuliskan catatan khusus terkait penanganan luka sayat, ketepatan balutan, ketenangan adik-adik Mula, atau hal penting lainnya..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">{{ $score?->notes }}</textarea>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        recalculateGrandTotal();
        initStopwatchFromInput();
    });

    // Handle Selection of Score Pill
    function selectScoreOption(itemKey, val) {
        const input = document.getElementById(`input_${itemKey}`);
        if (!input) return;

        input.value = val;

        const row = document.getElementById(`row_${itemKey}`);
        if (row) {
            // Remove active from all buttons in this row
            row.querySelectorAll('.score-btn').forEach(btn => {
                btn.classList.remove('active', 'active-zero');
                if (parseInt(btn.dataset.val) === val) {
                    btn.classList.add(val === 0 ? 'active-zero' : 'active');
                }
            });

            // Update row display
            const display = document.getElementById(`display_${itemKey}`);
            if (display) {
                display.textContent = val;
                if (val === 0) {
                    display.className = 'row-score-display text-slate-400';
                } else {
                    display.className = 'row-score-display text-emerald-700';
                }
            }
        }

        recalculateGrandTotal();
    }

    // Recalculate Subtotals and Grand Total
    function recalculateGrandTotal() {
        let grandTotal = 0;
        const sectionTotals = {};

        document.querySelectorAll('.rubric-input').forEach(input => {
            const sec = input.dataset.section;
            const val = parseInt(input.value) || 0;

            sectionTotals[sec] = (sectionTotals[sec] || 0) + val;
            grandTotal += val;
        });

        // Update Section Subtotal Displays & Hidden Inputs
        for (const [sec, total] of Object.entries(sectionTotals)) {
            const subDisplay = document.getElementById(`subtotal-display-${sec}`);
            if (subDisplay) subDisplay.textContent = total;

            const subInput = document.getElementById(`subtotal-input-${sec}`);
            if (subInput) subInput.value = total;
        }

        // Update Grand Total Displays
        document.getElementById('grand-total-display').textContent = grandTotal;
        document.getElementById('grand-total-row').textContent = grandTotal;

        // Update Percentage Badge (Max 465 Poin)
        const pct = ((grandTotal / 465) * 100).toFixed(1);
        const badge = document.getElementById('score-percentage-badge');
        if (badge) {
            badge.innerHTML = `${pct}% • ` + (pct >= 85 ? 'Sangat Baik' : (pct >= 70 ? 'Baik' : (pct >= 50 ? 'Cukup' : 'Perlu Evaluasi')));
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

    function submitScoreForm(action) {
        document.getElementById('action-after').value = action;
        document.getElementById('grid-score-form').submit();
    }

    // ================= STOPWATCH TIMER RESMI MULA (7 MENIT TOTAL) =================
    let timerInterval = null;
    let timerSeconds = 0;
    let timerRunning = false;
    const MAX_SECONDS_ALLOWED = 420; // 7 Menit (07:00)

    function initStopwatchFromInput() {
        const inputVal = document.getElementById('time-recorded-input').value.trim();
        if (inputVal.includes(':')) {
            const parts = inputVal.split(':');
            timerSeconds = (parseInt(parts[0], 10) || 0) * 60 + (parseInt(parts[1], 10) || 0);
        } else {
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

        // Peringatan Batas Waktu 07:00
        const warning = document.getElementById('timer-warning-text');
        if (timerSeconds >= MAX_SECONDS_ALLOWED) {
            pauseTimer();
            if (warning) {
                warning.className = 'text-rose-500 font-black animate-bounce text-xs';
                warning.innerHTML = '<i class="fa-solid fa-circle-exclamation text-base"></i> BATAS 07:00 TERCAPAI! PENANGANAN DIBERHENTIKAN OTOMATIS.';
            }
            alert('Waktu Pertolongan Pertama telah mencapai batas maksimal 7 menit (07:00)!\nPenanganan korban wajib diberhentikan.');
        } else {
            if (warning) {
                warning.className = 'text-rose-400 font-bold';
                warning.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Lewat 07:00 penanganan wajib diberhentikan';
            }
        }
    }

    function startTimer() {
        if (timerRunning) return;
        timerRunning = true;
        timerInterval = setInterval(() => {
            timerSeconds++;
            updateTimerDisplay();
        }, 1000);
    }

    function pauseTimer() {
        timerRunning = false;
        clearInterval(timerInterval);
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
