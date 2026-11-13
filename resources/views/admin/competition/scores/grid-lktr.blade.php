@extends('layouts.admin')

@section('title', 'Grid Penilaian: ' . $category->name . ' (' . $category->level . ')')
@section('page_title', 'Grid Penilaian Lomba Ketangkasan Tandu (LKTR)')

@section('top_actions')
    <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
        <i class="fa-solid fa-table-list text-amber-500"></i> Lembar Skor Utama
    </a>
    <a href="{{ route('admin.competition-scores.export-excel', $category->id) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
        <i class="fa-solid fa-file-excel"></i> Export Excel (.xlsx)
    </a>
    <a href="{{ route('admin.competition-scores.index', ['level' => $category->level]) }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-300 transition">
        &larr; Cabang Lomba
    </a>
@endsection

@section('content')
@php
    $level = trim($category->level ?? 'Madya');
    $isMula = strcasecmp($level, 'Mula') === 0;
    $isMadya = strcasecmp($level, 'Madya') === 0;
    $isWira = strcasecmp($level, 'Wira') === 0;

    $genderCat = trim($category->gender_category ?? '');
    $isPutra = strcasecmp($genderCat, 'Putra') === 0;
    $isPutri = strcasecmp($genderCat, 'Putri') === 0;

    if ($isMula) {
        $levelBadge = 'MULA';
        $levelFull = 'PMR Mula (SD / MI)';
        $themeName = 'Hijau';
        $themeBgLight = 'bg-emerald-50';
        $themeBorder = 'border-emerald-500';
        $themeText = 'text-emerald-700';
        $themeAccentBtn = 'bg-emerald-600 hover:bg-emerald-500 text-white';
        $themeHeaderGradient = 'from-slate-900 via-slate-800 to-emerald-950';
        $themeSubBadge = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
        $themeBadgePill = 'bg-emerald-600 text-white';
        $pillActiveStyle = 'background: linear-gradient(135deg, #059669 0%, #047857 100%) !important; color:#fff !important; box-shadow:0 3px 8px rgba(5,150,105,0.4); border-color:#10b981 !important;';
        $intervalCardActiveStyle = 'background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%) !important; border-color:#059669 !important; box-shadow:0 4px 14px rgba(5,150,105,0.3) !important;';
    } elseif ($isWira) {
        $levelBadge = 'WIRA';
        $levelFull = 'PMR Wira (SMA / SMK / MA)';
        $themeName = 'Kuning';
        $themeBgLight = 'bg-amber-50';
        $themeBorder = 'border-amber-500';
        $themeText = 'text-amber-800';
        $themeAccentBtn = 'bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-slate-950 font-black';
        $themeHeaderGradient = 'from-slate-900 via-slate-800 to-amber-950';
        $themeSubBadge = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
        $themeBadgePill = 'bg-amber-400 text-slate-950 font-black';
        $pillActiveStyle = 'background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important; color:#fff !important; box-shadow:0 3px 8px rgba(217,119,6,0.4); border-color:#f59e0b !important;';
        $intervalCardActiveStyle = 'background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important; border-color:#d97706 !important; box-shadow:0 4px 14px rgba(217,119,6,0.3) !important;';
    } else { // Madya (Default Biru)
        $levelBadge = 'MADYA';
        $levelFull = 'PMR Madya (SMP / MTs)';
        $themeName = 'Biru';
        $themeBgLight = 'bg-sky-50';
        $themeBorder = 'border-sky-500';
        $themeText = 'text-sky-800';
        $themeAccentBtn = 'bg-blue-600 hover:bg-blue-500 text-white';
        $themeHeaderGradient = 'from-slate-900 via-slate-800 to-blue-950';
        $themeSubBadge = 'bg-sky-500/20 text-sky-300 border-sky-500/30';
        $themeBadgePill = 'bg-sky-500 text-white';
        $pillActiveStyle = 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important; color:#fff !important; box-shadow:0 3px 8px rgba(37,99,235,0.4); border-color:#38bdf8 !important;';
        $intervalCardActiveStyle = 'background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%) !important; border-color:#0284c7 !important; box-shadow:0 4px 14px rgba(2,132,199,0.3) !important;';
    }

    $schoolName = $currentTeam?->registration?->school_name ?? ($currentTeam?->team_name ?? 'Belum Ada Regu Terdaftar');
    $teamCode = 'No. Urut ' . str_pad((string) ($currentTeam?->order_number ?? '1'), 2, '0', STR_PAD_LEFT);
    $rubricData = is_array($existingRubric) ? $existingRubric : [];

    $vKetepatan1 = $rubricData['ketepatan_1'] ?? null;
    $vKetepatan2 = $rubricData['ketepatan_2'] ?? null;
    $vKetepatan3 = $rubricData['ketepatan_3'] ?? null;
    $vKetepatan4 = $rubricData['ketepatan_4'] ?? null;
    $vKetepatan5 = $rubricData['ketepatan_5'] ?? null;
    $vKetepatan6 = $rubricData['ketepatan_6'] ?? null;
    $vKetepatan7 = $rubricData['ketepatan_7'] ?? null;

    $vKekuatan1 = $rubricData['kekuatan_1'] ?? null;
    $vKekuatan2 = $rubricData['kekuatan_2'] ?? null;
    $vKekuatan3 = $rubricData['kekuatan_3'] ?? null;

    $ketepatanItems = [
        ['num' => 1, 'key' => 'ketepatan_1', 'label' => 'Bentuk Simetris', 'desc' => 'Kerapihan bentuk tandu seimbang antara kiri dan kanan', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan1, 'max' => 43],
        ['num' => 2, 'key' => 'ketepatan_2', 'label' => 'Jarak ikatan jangkar sejajar dengan sebrangnya', 'desc' => 'Simetri posisi ikatan jangkar antara palang kiri dan kanan', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan2, 'max' => 43],
        ['num' => 3, 'key' => 'ketepatan_3', 'label' => 'Ikatan mati 4', 'desc' => 'Ketepatan simpul mati pada 4 sudut bambu', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan3, 'max' => 43],
        ['num' => 4, 'key' => 'ketepatan_4', 'label' => 'Ikatan jangkar 7', 'desc' => 'Ketepatan 7 ikatan jangkar sepanjang tandu', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan4, 'max' => 43],
        ['num' => 5, 'key' => 'ketepatan_5', 'label' => 'Tali tubuh', 'desc' => 'Pemasangan tali tubuh pengaman korban', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan5, 'max' => 43],
        ['num' => 6, 'key' => 'ketepatan_6', 'label' => 'Sisa bambu pendek dan panjang', 'desc' => 'Ketepatan ukuran sisa ujung bambu sesuai standar', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan6, 'max' => 43],
        ['num' => 7, 'key' => 'ketepatan_7', 'label' => 'Mitela Presisi rapih', 'desc' => 'Kerapihan lipatan dan kekencangan mitela pembalut', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan7, 'max' => 43],
    ];

    $kekuatanItems = [
        ['num' => 1, 'key' => 'kekuatan_1', 'label' => 'Kekuatan Jaring / tali jangkar', 'desc' => 'Kekokohan anyaman jaring saat diuji beban', 'opts' => [44, 88, 133], 'ranges' => ['0-44', '45-88', '89-133'], 'val' => $vKekuatan1, 'max' => 133],
        ['num' => 2, 'key' => 'kekuatan_2', 'label' => 'Kekuatan bambu pendek (diputar)', 'desc' => 'Uji puntir pada bambu palang pendek tidak mudah bergeser', 'opts' => [44, 88, 133], 'ranges' => ['0-44', '45-88', '89-133'], 'val' => $vKekuatan2, 'max' => 133],
        ['num' => 3, 'key' => 'kekuatan_3', 'label' => 'Kekuatan bambu panjang (diputar) digoyang', 'desc' => 'Uji goyang dan puntir pada bambu utama tetap kokoh', 'opts' => [44, 88, 133], 'ranges' => ['0-44', '45-88', '89-133'], 'val' => $vKekuatan3, 'max' => 133],
    ];

    $vWaktu = $existingTime ?: ($isMula ? '05:00' : '03:30');

    // Interval time definitions
    if ($isMula) {
        $intervals = [
            ['idx' => 1, 'min' => '0-1', 'range' => '271 - 300', 'desc' => '00:00 - 01:00', 'color' => 'emerald'],
            ['idx' => 2, 'min' => '1.1 - 2', 'range' => '241 - 270', 'desc' => '01:01 - 02:00', 'color' => 'emerald'],
            ['idx' => 3, 'min' => '2.1 - 3', 'range' => '211 - 240', 'desc' => '02:01 - 03:00', 'color' => 'slate'],
            ['idx' => 4, 'min' => '3.1 - 4', 'range' => '181 - 210', 'desc' => '03:01 - 04:00', 'color' => 'slate'],
            ['idx' => 5, 'min' => '4.1 - 5', 'range' => '151 - 180', 'desc' => '04:01 - 05:00', 'color' => 'slate'],
            ['idx' => 6, 'min' => '5.1 - 6', 'range' => '121 - 150', 'desc' => '05:01 - 06:00', 'color' => 'slate'],
            ['idx' => 7, 'min' => '6.1 - 7', 'range' => '91 - 120', 'desc' => '06:01 - 07:00', 'color' => 'slate'],
            ['idx' => 8, 'min' => '7.1 - 8', 'range' => '61 - 90', 'desc' => '07:01 - 08:00', 'color' => 'slate'],
            ['idx' => 9, 'min' => '8.1 - 9', 'range' => '31 - 60', 'desc' => '08:01 - 09:00', 'color' => 'slate'],
            ['idx' => 10, 'min' => '9.1 - 10', 'range' => '0 - 30', 'desc' => '09:01 - 10:00', 'color' => 'rose'],
        ];
    } else {
        $intervals = [
            ['idx' => 1, 'min' => '0 - 1', 'range' => '251 - 300', 'desc' => '00:00 - 01:00', 'color' => 'emerald'],
            ['idx' => 2, 'min' => '1.1 - 2', 'range' => '201 - 250', 'desc' => '01:01 - 02:00', 'color' => 'emerald'],
            ['idx' => 3, 'min' => '2.1 - 3', 'range' => '151 - 200', 'desc' => '02:01 - 03:00', 'color' => 'slate'],
            ['idx' => 4, 'min' => '3.1 - 4', 'range' => '101 - 150', 'desc' => '03:01 - 04:00', 'color' => 'slate'],
            ['idx' => 5, 'min' => '4.1 - 5', 'range' => '51 - 100', 'desc' => '04:01 - 05:00', 'color' => 'slate'],
            ['idx' => 6, 'min' => '5.1 - 5.6', 'range' => '0 - 51', 'desc' => '05:01 - 05:6', 'color' => 'rose'],
        ];
    }
@endphp
<div class="space-y-6">

    <style>
        .selected-pill-theme {
            {{ $pillActiveStyle }}
            font-weight: 800 !important;
            transform: scale(1.04);
        }
        .active-interval-card-theme {
            {{ $intervalCardActiveStyle }}
            transform: translateY(-2px);
        }
    </style>

    {{-- Banner Notifikasi Berhasil Simpan --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="text-xs bg-emerald-600 text-white font-extrabold px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition">
                Lihat di Lembar Skor Utama &rarr;
            </a>
        </div>
    @endif

    {{-- Header Info Card dengan Identitas Warna --}}
    <div class="bg-gradient-to-r {{ $themeHeaderGradient }} p-6 rounded-3xl shadow-xl text-white flex flex-col lg:flex-row justify-between lg:items-center gap-4 border border-slate-700">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-slate-700 via-slate-600 to-slate-800 p-0.5 shadow-lg flex items-center justify-center shrink-0">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <i class="fa-solid fa-bed text-2xl text-white"></i>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $themeSubBadge }} border">
                        {{ $category->code ?? 'LKTR' }}
                    </span>
                    <span class="text-xs text-slate-300 font-bold">{{ $levelFull }} &bull; Kategori {{ $category->gender_category }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $themeBadgePill }} shadow-xs">
                        IDENTITAS: {{ $themeName }}
                    </span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    {{ $category->name }}
                </h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    Ketepatan (Maks 301) + Kekuatan (Maks 399) + Waktu Tempuh {{ count($intervals) }} Interval (Maks 300) = Total 1000 Poin.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 bg-slate-800/90 px-4 py-2 rounded-2xl border border-slate-700 text-xs">
                <span class="text-slate-400 font-bold uppercase text-[10px]">Formula:</span>
                <span class="text-emerald-400 font-black">Teknis (700) + Waktu (300) = Total 1000 Poin</span>
            </div>
            <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="px-4 py-2 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-md">
                <i class="fa-solid fa-table-list"></i> Buka Lembar Skor Utama
            </a>
        </div>
    </div>

    {{-- WORKSPACE UTAMA LEBAR PENUH --}}
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- 1. TOP TEAM NAVIGATION & VIEW SWITCHER CARD --}}
        <div class="bg-white rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
            
            {{-- Navigasi Antar Regu & Quick Dropdown --}}
            <div class="flex items-center gap-2 sm:gap-3 w-full md:w-auto justify-between md:justify-start">
                @if($prevTeam)
                    <a href="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $prevTeam->id, 'round' => $round]) }}"
                       class="px-3.5 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-chevron-left text-[11px]"></i>
                        <span class="hidden sm:inline">Regu Sebelumnya</span>
                    </a>
                @else
                    <button disabled class="px-3.5 py-2 rounded-2xl bg-slate-50 text-slate-300 font-bold text-xs cursor-not-allowed flex items-center gap-1.5">
                        <i class="fa-solid fa-chevron-left text-[11px]"></i>
                        <span class="hidden sm:inline">Regu Sebelumnya</span>
                    </button>
                @endif

                <div class="relative flex-1 md:w-64">
                    <select onchange="window.location.href=this.value" 
                            class="w-full text-xs font-bold bg-slate-50 border border-slate-300 rounded-2xl px-3 py-2 text-slate-800 focus:ring-2 focus:ring-rose-500 focus:outline-none transition cursor-pointer">
                        @forelse($teams as $idx => $t)
                            @php
                                $tScore = $scores->get($t->id);
                                $hasScore = !empty($tScore && $tScore->final_score > 0);
                            @endphp
                            <option value="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $t->id, 'round' => $round]) }}"
                                    {{ ($currentTeam && $currentTeam->id == $t->id) ? 'selected' : '' }}>
                                {{ $t->order_number ?? ($idx + 1) }}. {{ $t->registration->school_name ?? $t->team_name }} ({{ $hasScore ? '✓ ' . number_format($tScore->final_score, 0) . ' pt' : 'Belum Dinilai' }})
                            </option>
                        @empty
                            <option disabled selected>-- Belum ada regu peserta terdaftar --</option>
                        @endforelse
                    </select>
                </div>

                @if($nextTeam)
                    <a href="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $nextTeam->id, 'round' => $round]) }}"
                       class="px-3.5 py-2 rounded-2xl {{ $themeAccentBtn }} font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                        <span class="hidden sm:inline">Regu Berikutnya</span>
                        <i class="fa-solid fa-chevron-right text-[11px]"></i>
                    </a>
                @else
                    <button disabled class="px-3.5 py-2 rounded-2xl bg-slate-50 text-slate-300 font-bold text-xs cursor-not-allowed flex items-center gap-1.5">
                        <span class="hidden sm:inline">Regu Berikutnya</span>
                        <i class="fa-solid fa-chevron-right text-[11px]"></i>
                    </button>
                @endif
            </div>

            {{-- View Mode Switcher Toggle --}}
            <div class="flex items-center gap-1.5 bg-slate-100 p-1.5 rounded-2xl border border-slate-200 shrink-0">
                <span class="text-[11px] font-bold text-slate-500 px-2 hidden lg:inline">Tampilan:</span>
                <button type="button" id="btnViewModeA" onclick="switchViewMode('A')" 
                        class="px-3.5 py-1.5 rounded-xl font-extrabold text-xs transition flex items-center gap-1.5 {{ $themeBadgePill }} shadow-sm">
                    <i class="fa-solid fa-table-cells-large text-[11px]"></i> Mode Kartu Modern (A)
                </button>
                <button type="button" id="btnViewModeB" onclick="switchViewMode('B')" 
                        class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900">
                    <i class="fa-solid fa-file-lines text-[11px]"></i> Mode Tabel Kertas (B)
                </button>
            </div>

        </div>

        {{-- 2. ACTIVE TEAM HERO CARD --}}
        @if($currentTeam)
            <div class="bg-gradient-to-r {{ $themeHeaderGradient }} rounded-3xl p-5 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl border border-slate-700">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl {{ $themeBadgePill }} font-mono font-black text-lg flex items-center justify-center shadow-md shrink-0">
                        {{ $currentTeam->order_number ?? '1' }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-wider {{ $themeSubBadge }} px-2 py-0.5 rounded-md border">
                                {{ $teamCode }} &bull; {{ $currentTeam->team_label ?? $currentTeam->team_name }}
                            </span>
                            @if($score && $score->final_score > 0)
                                <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-md border border-emerald-500/30 flex items-center gap-1">
                                    <i class="fa-solid fa-check text-[9px]"></i> Sudah Dinilai ({{ number_format($score->final_score, 0) }} Pts)
                                </span>
                            @else
                                <span class="text-[10px] font-bold bg-amber-500/20 text-amber-400 px-2 py-0.5 rounded-md border border-amber-500/30">
                                    Sedang Dinilai
                                </span>
                            @endif
                        </div>
                        <h2 class="text-xl font-black text-white mt-1">{{ $schoolName }}</h2>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-slate-950/70 px-4 py-2.5 rounded-2xl border border-slate-700/80">
                    <div class="text-right">
                        <span class="text-[9px] text-slate-400 uppercase tracking-wider block font-bold">Total Nilai Akumulasi</span>
                        <span class="text-2xl font-black text-emerald-400 font-mono" id="header_total_score">0</span>
                    </div>
                </div>
            </div>

            {{-- FORM PENILAIAN UTAMA --}}
            <form action="{{ route('admin.competition-scores.grid-save', $category->id) }}" method="POST" id="formGridLKTR">
                @csrf
                <input type="hidden" name="competition_participant_team_id" value="{{ $currentTeam->id }}">
                <input type="hidden" name="round_name" value="{{ $round }}">
                <input type="hidden" name="action_after" id="actionAfterInput" value="stay">
                <input type="hidden" name="next_team_id" value="{{ $nextTeam?->id }}">

                {{-- Hidden Master Inputs --}}
                <input type="hidden" name="rubric[ketepatan_1]" id="input_ketepatan_1" value="{{ $vKetepatan1 }}">
                <input type="hidden" name="rubric[ketepatan_2]" id="input_ketepatan_2" value="{{ $vKetepatan2 }}">
                <input type="hidden" name="rubric[ketepatan_3]" id="input_ketepatan_3" value="{{ $vKetepatan3 }}">
                <input type="hidden" name="rubric[ketepatan_4]" id="input_ketepatan_4" value="{{ $vKetepatan4 }}">
                <input type="hidden" name="rubric[ketepatan_5]" id="input_ketepatan_5" value="{{ $vKetepatan5 }}">
                <input type="hidden" name="rubric[ketepatan_6]" id="input_ketepatan_6" value="{{ $vKetepatan6 }}">
                <input type="hidden" name="rubric[ketepatan_7]" id="input_ketepatan_7" value="{{ $vKetepatan7 }}">

                <input type="hidden" name="rubric[kekuatan_1]" id="input_kekuatan_1" value="{{ $vKekuatan1 }}">
                <input type="hidden" name="rubric[kekuatan_2]" id="input_kekuatan_2" value="{{ $vKekuatan2 }}">
                <input type="hidden" name="rubric[kekuatan_3]" id="input_kekuatan_3" value="{{ $vKekuatan3 }}">

                <input type="hidden" name="subtotals[ketepatan]" id="input_subtotal_ketepatan" value="0">
                <input type="hidden" name="subtotals[kekuatan]" id="input_subtotal_kekuatan" value="0">
                <input type="hidden" name="subtotals[teknis]" id="input_subtotal_teknis" value="0">
                <input type="hidden" name="subtotals[waktu]" id="input_subtotal_waktu" value="0">
                <input type="hidden" name="subtotals[total]" id="input_subtotal_total" value="0">

                <input type="hidden" name="time_recorded" id="input_hidden_time_recorded" value="{{ $vWaktu }}">
                <input type="hidden" name="notes" id="input_hidden_notes" value="{{ $existingNotes }}">

                {{-- ========================================================= --}}
                {{-- ================== VIEW MODE A (OPSI A) ================= --}}
                {{-- ========================================================= --}}
                <div id="viewModeA" class="space-y-6">

                    {{-- SEKSI I: KETEPATAN (Maks 301) --}}
                    <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-slate-200 gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $themeBgLight }} {{ $themeText }} flex items-center justify-center font-bold text-lg">
                                    <i class="fa-solid fa-compass-drafting"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-base text-slate-900 uppercase tracking-tight">I. Ketepatan</h3>
                                    <p class="text-xs text-slate-500">7 Butir Penilaian &bull; Masing-masing bernilai maksimal 43 poin</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 {{ $themeBgLight }} px-4 py-2 rounded-2xl border {{ $themeBorder }}/30 text-xs self-start sm:self-center">
                                <span class="text-slate-600 font-bold uppercase text-[10px]">Subtotal Ketepatan:</span>
                                <span class="font-mono font-black {{ $themeText }} text-sm">
                                    <span id="display_a_subtotal_ketepatan">0</span> / 301 Poin
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[700px]">
                                <thead>
                                    <tr class="text-[11px] font-black uppercase text-slate-400 tracking-wider border-b border-slate-200">
                                        <th class="py-3 px-3 w-12 text-center">No</th>
                                        <th class="py-3 px-3">Aspek Kriteria Penilaian</th>
                                        <th class="py-3 px-3 text-center">Pilihan Cepat / Rentang Rubrik</th>
                                        <th class="py-3 px-3 text-center w-36">Nilai Juri (0-43)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach($ketepatanItems as $it)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="py-3 px-3 text-center">
                                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-xs inline-flex items-center justify-center">
                                                    {{ $it['num'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="font-extrabold text-slate-900 text-sm">{{ $it['label'] }}</div>
                                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $it['desc'] }}</div>
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" 
                                                            id="pill_ketepatan_{{ $it['num'] }}_0" 
                                                            onclick="setRubricValue('ketepatan_{{ $it['num'] }}', 14, 43)" 
                                                            class="btn-pill-ketepatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                        0 - 14
                                                    </button>
                                                    <button type="button" 
                                                            id="pill_ketepatan_{{ $it['num'] }}_1" 
                                                            onclick="setRubricValue('ketepatan_{{ $it['num'] }}', 28, 43)" 
                                                            class="btn-pill-ketepatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                        15 - 28
                                                    </button>
                                                    <button type="button" 
                                                            id="pill_ketepatan_{{ $it['num'] }}_2" 
                                                            onclick="setRubricValue('ketepatan_{{ $it['num'] }}', 43, 43)" 
                                                            class="btn-pill-ketepatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                        29 - 43
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <input type="number" 
                                                           id="input_a_ketepatan_{{ $it['num'] }}" 
                                                           min="0" 
                                                           max="43" 
                                                           value="{{ $it['val'] }}" 
                                                           placeholder="0" 
                                                           oninput="handleInputA('ketepatan_{{ $it['num'] }}', 43, this.value)" 
                                                           class="w-20 text-center font-mono font-black text-sm bg-white text-slate-900 border-2 {{ $themeBorder }} rounded-xl py-2 px-2 focus:ring-2 focus:ring-rose-200 focus:outline-none transition shadow-2xs">
                                                    <span class="text-xs font-bold text-slate-400">/ 43</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- SEKSI II: KEKUATAN (Maks 399) --}}
                    <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-slate-200 gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $themeBgLight }} {{ $themeText }} flex items-center justify-center font-bold text-lg">
                                    <i class="fa-solid fa-dumbbell"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-base text-slate-900 uppercase tracking-tight">II. Kekuatan</h3>
                                    <p class="text-xs text-slate-500">3 Butir Penilaian &bull; Masing-masing bernilai maksimal 133 poin</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 {{ $themeBgLight }} px-4 py-2 rounded-2xl border {{ $themeBorder }}/30 text-xs self-start sm:self-center">
                                <span class="text-slate-600 font-bold uppercase text-[10px]">Subtotal Kekuatan:</span>
                                <span class="font-mono font-black {{ $themeText }} text-sm">
                                    <span id="display_a_subtotal_kekuatan">0</span> / 399 Poin
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[700px]">
                                <thead>
                                    <tr class="text-[11px] font-black uppercase text-slate-400 tracking-wider border-b border-slate-200">
                                        <th class="py-3 px-3 w-12 text-center">No</th>
                                        <th class="py-3 px-3">Aspek Kriteria Penilaian</th>
                                        <th class="py-3 px-3 text-center">Pilihan Cepat / Rentang Rubrik</th>
                                        <th class="py-3 px-3 text-center w-36">Nilai Juri (0-133)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach($kekuatanItems as $it)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="py-3 px-3 text-center">
                                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-xs inline-flex items-center justify-center">
                                                    {{ $it['num'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="font-extrabold text-slate-900 text-sm">{{ $it['label'] }}</div>
                                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $it['desc'] }}</div>
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" 
                                                            id="pill_kekuatan_{{ $it['num'] }}_0" 
                                                            onclick="setRubricValue('kekuatan_{{ $it['num'] }}', 44, 133)" 
                                                            class="btn-pill-kekuatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                        0 - 44
                                                    </button>
                                                    <button type="button" 
                                                            id="pill_kekuatan_{{ $it['num'] }}_1" 
                                                            onclick="setRubricValue('kekuatan_{{ $it['num'] }}', 88, 133)" 
                                                            class="btn-pill-kekuatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                        45 - 88
                                                    </button>
                                                    <button type="button" 
                                                            id="pill_kekuatan_{{ $it['num'] }}_2" 
                                                            onclick="setRubricValue('kekuatan_{{ $it['num'] }}', 133, 133)" 
                                                            class="btn-pill-kekuatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                        89 - 133
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <input type="number" 
                                                           id="input_a_kekuatan_{{ $it['num'] }}" 
                                                           min="0" 
                                                           max="133" 
                                                           value="{{ $it['val'] }}" 
                                                           placeholder="0" 
                                                           oninput="handleInputA('kekuatan_{{ $it['num'] }}', 133, this.value)" 
                                                           class="w-24 text-center font-mono font-black text-sm bg-white text-slate-900 border-2 {{ $themeBorder }} rounded-xl py-2 px-2 focus:ring-2 focus:ring-rose-200 focus:outline-none transition shadow-2xs">
                                                    <span class="text-xs font-bold text-slate-400">/ 133</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- SEKSI III: KECEPATAN (WAKTU TEMPUH) --}}
                    <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-slate-200 gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
                                    <i class="fa-solid fa-stopwatch"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-black text-base text-slate-900 uppercase tracking-tight">III. Kecepatan (Waktu Tempuh)</h3>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $themeBadgePill }}">
                                            Skala {{ count($intervals) }} Interval Resmi {{ $levelBadge }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        @if($isMula)
                                            Skala 10 Interval: 0-1m (271-300) s/d 9.1-10m (0-30) &bull; Toleransi Maks 10:00 Menit
                                        @else
                                            Skala 6 Interval: 0-1m (251-300) s/d 5.1-5.6m (0-51) &bull; Toleransi Maks 05:30 Menit
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 bg-emerald-50 px-4 py-2 rounded-2xl border border-emerald-200 text-xs self-start sm:self-center">
                                <span class="text-slate-600 font-bold uppercase text-[10px]">Poin Waktu:</span>
                                <span class="font-mono font-black text-emerald-700 text-sm">
                                    <span id="display_a_subtotal_waktu">0</span> / 300 Poin
                                </span>
                            </div>
                        </div>

                        {{-- Time Input Hero Card --}}
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 mb-5">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-extrabold uppercase text-slate-700">Waktu Tempuh:</span>
                                <input type="text" 
                                       id="time_recorded_a" 
                                       value="{{ $vWaktu }}" 
                                       placeholder="{{ $isMula ? '05:00' : '03:30' }}" 
                                       oninput="handleTimeSync(this.value)" 
                                       class="w-32 text-center font-mono font-black text-xl bg-white text-slate-900 border-2 {{ $themeBorder }} rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-rose-500 focus:outline-none shadow-sm">
                                <span class="text-[11px] text-slate-500 font-medium">Format: Menit:Detik (contoh: {{ $isMula ? '04:25' : '03:42' }})</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="px-4 py-2 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 font-mono text-center">
                                    <span class="text-[9px] text-emerald-700 font-bold block uppercase tracking-wider">Hasil Poin:</span>
                                    <span class="text-base font-black" id="a_time_pts">0 Poin</span>
                                </div>
                                <div class="px-4 py-2 rounded-xl bg-slate-200 border border-slate-300 text-slate-800 font-mono text-center">
                                    <span class="text-[9px] text-slate-500 font-bold block uppercase tracking-wider">Interval Aktif:</span>
                                    <span class="text-xs font-black" id="a_time_tier">-</span>
                                </div>
                            </div>
                        </div>

                        {{-- Visual Grid Cards --}}
                        <div>
                            <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider block mb-2">
                                Tabel {{ count($intervals) }} Interval Konversi Waktu Resmi Dewan Juri:
                            </span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 {{ $isMula ? 'lg:grid-cols-10' : 'lg:grid-cols-6' }} gap-2.5">
                                @foreach($intervals as $int)
                                    <div id="card_interval_{{ $int['idx'] }}" class="p-2.5 rounded-2xl border border-slate-200 bg-white text-center transition">
                                        <span class="text-[10px] font-bold text-slate-500 block">{{ $int['min'] }} Menit</span>
                                        <span class="text-xs font-mono font-black text-{{ $int['color'] }}-700 block mt-0.5">{{ $int['range'] }}</span>
                                        <span class="text-[9px] text-slate-400 block font-sans">{{ $int['desc'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2 text-[10px] text-rose-600 font-bold text-right">
                                * Catatan: Waktu > {{ $isMula ? '10:00' : '05:6' }} Menit = 0 Poin
                            </div>
                        </div>

                    </div>

                    {{-- SEKSI IV: CATATAN KHUSUS DEWAN JURI --}}
                    <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-comment-dots text-slate-400"></i> Catatan Khusus Dewan Juri:
                        </label>
                        <textarea id="notes_a" rows="2" placeholder="Tuliskan catatan teknis regu jika ada (opsional)..." oninput="handleNotesSync(this.value)" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500">{{ $existingNotes }}</textarea>
                    </div>

                </div>

                {{-- ========================================================= --}}
                {{-- ================== VIEW MODE B (OPSI B) ================= --}}
                {{-- ========================================================= --}}
                <div id="viewModeB" class="hidden space-y-6">

                    {{-- KERTAS LEMBAR PENILAIAN RESMI (TABULAR PENUH) --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border-2 border-slate-900">
                        
                        {{-- Header Lembar Fisik --}}
                        <div class="border-b-2 border-slate-900 pb-4 mb-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ asset('images/logo-pmi.png') }}" alt="PMI" class="h-10 w-auto object-contain" onerror="this.style.display='none'">
                                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto object-contain" onerror="this.style.display='none'">
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-black tracking-widest text-rose-600 uppercase block">FORMAT PENILAIAN LOMBA KETANGKASAN TANDU (LKTR)</span>
                                        <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">LEMBAR PENILAIAN RESMI DEWAN JURI</h2>
                                    </div>
                                </div>
                                <div class="self-end sm:self-center">
                                    @if($isMula)
                                        <div class="border-2 border-slate-900 bg-white font-black text-base px-6 py-1.5 tracking-widest text-slate-900 shadow-sm rounded-lg text-center uppercase">
                                            MULA
                                        </div>
                                    @else
                                        <div class="border-2 border-slate-900 rounded-lg overflow-hidden text-[11px] font-black text-center shadow-sm">
                                            <div class="grid grid-cols-2 divide-x-2 divide-slate-900 border-b-2 border-slate-900">
                                                <div class="py-1 px-3 {{ $isMadya ? 'bg-slate-900 text-white' : 'bg-white text-slate-700' }}">Madya</div>
                                                <div class="py-1 px-3 {{ $isWira ? 'bg-slate-900 text-white' : 'bg-white text-slate-700' }}">Wira</div>
                                            </div>
                                            <div class="grid grid-cols-2 divide-x-2 divide-slate-900">
                                                <div class="py-1 px-3 {{ $isPutra ? 'bg-slate-800 text-white' : 'bg-white text-slate-700' }}">Putra</div>
                                                <div class="py-1 px-3 {{ $isPutri ? 'bg-slate-800 text-white' : 'bg-white text-slate-700' }}">Putri</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Metadata Baris Sesuai Lembar Fisik --}}
                            <div class="mt-4 bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs font-bold text-slate-800 space-y-1.5">
                                <div class="flex items-center">
                                    <span class="w-44 text-slate-500 uppercase tracking-wider text-[11px]">No. Urut Pendaftaran</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-mono font-black text-slate-900 bg-white border border-slate-200 px-2.5 py-0.5 rounded text-sm text-rose-600">
                                        {{ $teamCode }} &bull; {{ $currentTeam->team_label ?? $currentTeam->team_name }}
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-44 text-slate-500 uppercase tracking-wider text-[11px]">Asal Sekolah</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-black text-slate-900 text-sm">
                                        {{ $schoolName }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Penilaian Format Fisik --}}
                        <div class="rounded-2xl border-2 border-slate-900 mb-4 shadow-sm overflow-hidden overflow-x-auto">
                            <table class="w-full text-center border-collapse min-w-[750px]">
                                <thead>
                                    <tr class="bg-slate-900 text-white text-[11px] uppercase font-black tracking-wider">
                                        <th rowspan="2" class="py-2.5 px-2 w-12 border-r border-b border-slate-700">No</th>
                                        <th rowspan="2" class="py-2.5 px-4 text-left border-r border-b border-slate-700">ASPEK PENILAIAN</th>
                                        <th colspan="3" class="py-2.5 px-2 bg-slate-800 border-b border-slate-700">Penilaian (Input Nilai)</th>
                                    </tr>
                                    <tr class="bg-slate-800 text-slate-300 text-[10px] font-black uppercase">
                                        <th class="py-1.5 px-2 border-r border-slate-700 w-32">0-14</th>
                                        <th class="py-1.5 px-2 border-r border-slate-700 w-32">15-28</th>
                                        <th class="py-1.5 px-2 w-32">29-43</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs divide-y divide-slate-300 font-semibold text-slate-800">
                                    
                                    {{-- 1. KETEPATAN --}}
                                    <tr class="bg-slate-200/90 text-slate-900 font-black text-xs text-left border-b border-slate-300">
                                        <td rowspan="{{ count($ketepatanItems) + 1 }}" class="py-2.5 px-2 text-center border-r-2 border-slate-900 font-bold align-middle bg-slate-100 text-sm">
                                            1
                                        </td>
                                        <td class="py-2.5 px-4 tracking-wider uppercase font-black bg-slate-200/90 border-r border-slate-300">
                                            KETEPATAN <span class="text-[10px] text-slate-600 font-bold ml-1">(Maksimal: 301 Poin)</span>
                                        </td>
                                        <td colspan="3" class="py-2 px-3 text-right bg-slate-300/80 font-mono font-black text-xs text-slate-900">
                                            Subtotal Ketepatan: <span id="display_b_subtotal_ketepatan" class="text-rose-600 text-sm">0</span> / 301 Poin
                                        </td>
                                    </tr>

                                    @foreach($ketepatanItems as $it)
                                        @php
                                            $v = $it['val'];
                                            $col0Val = ($v !== null && $v !== '' && $v <= 14) ? $v : '';
                                            $col1Val = ($v !== null && $v !== '' && $v >= 15 && $v <= 28) ? $v : '';
                                            $col2Val = ($v !== null && $v !== '' && $v >= 29) ? $v : '';
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-2.5 px-4 text-left border-r border-slate-300 font-medium text-slate-800">
                                                {{ $it['label'] }}
                                            </td>
                                            <td class="p-1 border-r border-slate-300 w-32">
                                                <input type="number" 
                                                    id="input_b_col_ketepatan_{{ $it['num'] }}_0" 
                                                    value="{{ $col0Val }}" 
                                                    placeholder="0-14" 
                                                    oninput="handleInputB('ketepatan_{{ $it['num'] }}', 0, 43, this.value)" 
                                                    class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                            </td>
                                            <td class="p-1 border-r border-slate-300 w-32">
                                                <input type="number" 
                                                    id="input_b_col_ketepatan_{{ $it['num'] }}_1" 
                                                    value="{{ $col1Val }}" 
                                                    placeholder="15-28" 
                                                    oninput="handleInputB('ketepatan_{{ $it['num'] }}', 1, 43, this.value)" 
                                                    class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                            </td>
                                            <td class="p-1 w-32">
                                                <input type="number" 
                                                    id="input_b_col_ketepatan_{{ $it['num'] }}_2" 
                                                    value="{{ $col2Val }}" 
                                                    placeholder="29-43" 
                                                    oninput="handleInputB('ketepatan_{{ $it['num'] }}', 2, 43, this.value)" 
                                                    class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- 2. KEKUATAN --}}
                                    <tr class="bg-slate-200/90 text-slate-900 font-black text-xs text-left border-t-2 border-slate-900">
                                        <td rowspan="{{ count($kekuatanItems) + 2 }}" class="py-2.5 px-2 text-center border-r-2 border-slate-900 font-bold align-middle bg-slate-100 text-sm">
                                            2
                                        </td>
                                        <td class="py-2.5 px-4 tracking-wider uppercase font-black bg-slate-200/90 border-r border-slate-300">
                                            KEKUATAN <span class="text-[10px] text-slate-600 font-bold ml-1">(Maksimal: 399 Poin)</span>
                                        </td>
                                        <td colspan="3" class="py-2 px-3 text-right bg-slate-300/80 font-mono font-black text-xs text-slate-900">
                                            Subtotal Kekuatan: <span id="display_b_subtotal_kekuatan" class="text-rose-600 text-sm">0</span> / 399 Poin
                                        </td>
                                    </tr>
                                    <tr class="bg-slate-700 text-slate-200 text-[10px] font-black uppercase text-center">
                                        <td class="py-1 px-4 text-left border-r border-slate-600 font-bold text-slate-300">RENTANG NILAI KEKUATAN :</td>
                                        <th class="py-1 px-2 border-r border-slate-600 w-32">0-44</th>
                                        <th class="py-1 px-2 border-r border-slate-600 w-32">45-88</th>
                                        <th class="py-1 px-2 w-32">89-133</th>
                                    </tr>

                                    @foreach($kekuatanItems as $it)
                                        @php
                                            $v = $it['val'];
                                            $col0Val = ($v !== null && $v !== '' && $v <= 44) ? $v : '';
                                            $col1Val = ($v !== null && $v !== '' && $v >= 45 && $v <= 88) ? $v : '';
                                            $col2Val = ($v !== null && $v !== '' && $v >= 89) ? $v : '';
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-2.5 px-4 text-left border-r border-slate-300 font-medium text-slate-800">
                                                {{ $it['label'] }}
                                            </td>
                                            <td class="p-1 border-r border-slate-300 w-32">
                                                <input type="number" 
                                                    id="input_b_col_kekuatan_{{ $it['num'] }}_0" 
                                                    value="{{ $col0Val }}" 
                                                    placeholder="0-44" 
                                                    oninput="handleInputB('kekuatan_{{ $it['num'] }}', 0, 133, this.value)" 
                                                    class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                            </td>
                                            <td class="p-1 border-r border-slate-300 w-32">
                                                <input type="number" 
                                                    id="input_b_col_kekuatan_{{ $it['num'] }}_1" 
                                                    value="{{ $col1Val }}" 
                                                    placeholder="45-88" 
                                                    oninput="handleInputB('kekuatan_{{ $it['num'] }}', 1, 133, this.value)" 
                                                    class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                            </td>
                                            <td class="p-1 w-32">
                                                <input type="number" 
                                                    id="input_b_col_kekuatan_{{ $it['num'] }}_2" 
                                                    value="{{ $col2Val }}" 
                                                    placeholder="89-133" 
                                                    oninput="handleInputB('kekuatan_{{ $it['num'] }}', 2, 133, this.value)" 
                                                    class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- 3. WAKTU (Menit) --}}
                                    <tr class="bg-slate-200/90 text-slate-900 font-black text-xs text-left border-t-2 border-slate-900">
                                        <td rowspan="2" class="py-2.5 px-2 text-center border-r-2 border-slate-900 font-bold align-middle bg-slate-100 text-sm">
                                            3
                                        </td>
                                        <td class="py-2.5 px-4 tracking-wider uppercase font-black bg-slate-200/90 border-r border-slate-300">
                                            WAKTU (Menit) <span class="text-[10px] text-slate-600 font-bold ml-1">(Skala {{ count($intervals) }} Interval Menit &bull; Maks 300 Poin)</span>
                                        </td>
                                        <td colspan="3" class="py-2 px-3 text-right bg-emerald-100/90 font-mono font-black text-xs text-emerald-900">
                                            Poin Waktu: <span id="display_b_subtotal_waktu" class="text-emerald-700 text-sm font-black">0</span> / 300 Poin
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="p-0 border-t border-slate-300">
                                            <div class="grid {{ $isMula ? 'grid-cols-10' : 'grid-cols-6' }} divide-x divide-slate-300 text-center font-mono text-[10px] bg-slate-50">
                                                @foreach($intervals as $int)
                                                    <div class="py-1.5 font-bold" id="b_interval_lbl_{{ $int['idx'] }}">{{ $int['min'] }}</div>
                                                @endforeach
                                            </div>
                                            <div class="grid {{ $isMula ? 'grid-cols-10' : 'grid-cols-6' }} divide-x divide-slate-300 text-center font-mono text-[10px] border-t border-slate-300 bg-white">
                                                @foreach($intervals as $int)
                                                    <div class="py-1.5 font-black text-{{ $int['color'] }}-700 transition" id="b_tier_cell_{{ $int['idx'] }}">{{ $int['range'] }}</div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-900 text-white font-black text-sm border-t-2 border-slate-900">
                                        <td colspan="4" class="py-3 px-4 text-right tracking-wider uppercase text-slate-300 text-xs">
                                            TOTAL NILAI AKUMULASI (TEKNIS: <span id="display_b_footer_teknis">0</span> + WAKTU: <span id="display_b_footer_waktu">0</span>) :
                                        </td>
                                        <td class="py-3 px-2 bg-gradient-to-r from-red-600 to-rose-600 text-white font-mono text-xl tracking-tight text-center" id="display_b_total_score">
                                            0
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Box Waktu Tengah Sesuai Kertas --}}
                        <div class="max-w-md mx-auto bg-white border-2 border-slate-900 rounded-2xl p-4 text-center shadow-sm mb-4">
                            <h4 class="text-xs font-black uppercase text-slate-900 tracking-wider mb-2">Waktu</h4>
                            <div class="flex items-center justify-center gap-3">
                                <input type="text" 
                                    id="time_recorded_b" 
                                    value="{{ $vWaktu }}" 
                                    placeholder="{{ $isMula ? '05:00' : '03:30' }}" 
                                    oninput="handleTimeSync(this.value)" 
                                    class="w-36 text-center font-mono font-black text-xl bg-amber-50 text-amber-950 border-2 border-amber-400 rounded-xl px-3 py-1.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-inner">
                                <div class="text-left font-mono">
                                    <div class="text-xs font-black text-emerald-600" id="b_waktu_badge_pts">0 Poin</div>
                                    <div class="text-[10px] text-slate-500 font-bold" id="b_waktu_badge_tier">Interval: -</div>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-500 mt-2 block">Format: Menit:Detik (contoh: {{ $isMula ? '04:25' : '03:42' }}) &bull; Konversi otomatis {{ count($intervals) }} Interval</span>
                        </div>

                        {{-- Catatan Dewan Juri --}}
                        <div class="pt-1 mb-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-comment-dots text-slate-400"></i> Catatan Khusus Dewan Juri:
                            </label>
                            <textarea id="notes_b" rows="2" placeholder="Tuliskan catatan teknis regu jika ada (opsional)..." oninput="handleNotesSync(this.value)" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500">{{ $existingNotes }}</textarea>
                        </div>

                        {{-- Tanda Tangan Juri & Peserta Sesuai Lembar Fisik --}}
                        <div class="grid grid-cols-2 gap-6 pt-4 pb-2 border-t border-slate-200">
                            <div class="text-center">
                                <div class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-14">Tanda Tangan Juri</div>
                                <div class="font-bold text-xs text-slate-700">(................................................)</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-14">Tanda Tangan Peserta</div>
                                <div class="font-bold text-xs text-slate-700">(................................................)</div>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- ========================================================= --}}
                {{-- ============ STICKY BOTTOM ACTION SUMMARY BAR =========== --}}
                {{-- ========================================================= --}}
                <div class="sticky bottom-4 z-40 bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 border border-slate-700 shadow-2xl text-white flex flex-col md:flex-row items-center justify-between gap-4">
                    
                    {{-- Ringkasan Poin Live --}}
                    <div class="flex items-center gap-3 sm:gap-4 flex-wrap justify-center md:justify-start text-xs font-mono">
                        <div class="bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700">
                            <span class="text-[9px] text-slate-400 block font-sans uppercase">I. Ketepatan</span>
                            <span class="font-black text-rose-400" id="sticky_ketepatan">0</span><span class="text-slate-500">/301</span>
                        </div>
                        <span class="text-slate-500 font-bold">+</span>
                        <div class="bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700">
                            <span class="text-[9px] text-slate-400 block font-sans uppercase">II. Kekuatan</span>
                            <span class="font-black text-amber-400" id="sticky_kekuatan">0</span><span class="text-slate-500">/399</span>
                        </div>
                        <span class="text-slate-500 font-bold">+</span>
                        <div class="bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700">
                            <span class="text-[9px] text-slate-400 block font-sans uppercase">III. Waktu</span>
                            <span class="font-black text-emerald-400" id="sticky_waktu">+0</span><span class="text-slate-500">/300</span>
                        </div>
                        <span class="text-slate-500 font-bold">=</span>
                        <div class="bg-gradient-to-r from-red-600/30 to-rose-600/30 px-3.5 py-1.5 rounded-xl border border-rose-500/50">
                            <span class="text-[9px] text-rose-300 block font-sans uppercase">TOTAL AKHIR ({{ $levelBadge }})</span>
                            <span class="text-xl font-black text-emerald-400" id="sticky_total">0</span> <span class="text-xs text-slate-400 font-sans">/ 1000 Poin</span>
                        </div>
                    </div>

                    {{-- Tombol Aksi Simpan --}}
                    <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                        <button type="submit" onclick="setActionAfter('stay')" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-extrabold text-xs shadow-lg transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Nilai
                        </button>

                        @if($nextTeam)
                            <button type="submit" onclick="setActionAfter('next')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition flex items-center gap-1.5">
                                <i class="fa-solid fa-forward-step text-sky-400"></i> Simpan & Lanjut
                            </button>
                        @endif

                        <button type="submit" onclick="setActionAfter('back')" class="px-3.5 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-slate-400 hover:text-white font-semibold text-xs border border-slate-800 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-left"></i> Lembar Utama
                        </button>
                    </div>

                </div>

            </form>
        @else
            <div class="bg-white rounded-3xl p-12 text-center text-slate-500 border border-slate-200 shadow-sm">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                    <i class="fa-solid fa-users-slash"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800">Tidak Ada Regu yang Dipilih</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    @if($teams->isEmpty())
                        Kategori ini belum memiliki regu peserta yang terdaftar atau terverifikasi.
                    @else
                        Silakan pilih salah satu regu peserta pada daftar dropdown di atas untuk mulai menginput nilai.
                    @endif
                </p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Lembar Nilai
                    </a>
                    <a href="{{ route('admin.competition-participants.index') }}" class="px-4 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-500 transition flex items-center gap-2">
                        <i class="fa-solid fa-users-viewfinder"></i> Kelola Peserta
                    </a>
                </div>
            </div>
        @endif

    </div>

</div>

<script>
    const categoryLevel = '{{ $level }}'.toLowerCase();
    const isLevelMula = (categoryLevel === 'mula');
    const isLevelMadya = (categoryLevel === 'madya');
    const isLevelWira = (categoryLevel === 'wira');
    const intervalCount = {{ count($intervals) }};

    function setActionAfter(action) {
        document.getElementById('actionAfterInput').value = action;
    }

    // Switch between Opsi A (Modern Card) and Opsi B (Clean Paper Tabular)
    function switchViewMode(mode) {
        const viewA = document.getElementById('viewModeA');
        const viewB = document.getElementById('viewModeB');
        const btnA = document.getElementById('btnViewModeA');
        const btnB = document.getElementById('btnViewModeB');

        if (!viewA || !viewB) return;

        if (mode === 'A') {
            viewA.classList.remove('hidden');
            viewB.classList.add('hidden');
            if (btnA) {
                btnA.classList.add('shadow-sm', 'font-extrabold');
                btnA.classList.remove('text-slate-600', 'hover:text-slate-900');
            }
            if (btnB) {
                btnB.classList.remove('shadow-sm', 'font-extrabold');
                btnB.classList.add('text-slate-600', 'hover:text-slate-900');
            }
        } else {
            viewA.classList.add('hidden');
            viewB.classList.remove('hidden');
            if (btnB) {
                btnB.classList.add('shadow-sm', 'font-extrabold');
                btnB.classList.remove('text-slate-600', 'hover:text-slate-900');
            }
            if (btnA) {
                btnA.classList.remove('shadow-sm', 'font-extrabold');
                btnA.classList.add('text-slate-600', 'hover:text-slate-900');
            }
        }

        try {
            localStorage.setItem('lktr_view_mode_' + categoryLevel, mode);
        } catch (e) {}
    }

    // Synchronize Time input between View A and View B
    function handleTimeSync(val) {
        const hidden = document.getElementById('input_hidden_time_recorded');
        if (hidden) hidden.value = val;

        const timeA = document.getElementById('time_recorded_a');
        if (timeA && timeA.value !== val) timeA.value = val;

        const timeB = document.getElementById('time_recorded_b');
        if (timeB && timeB.value !== val) timeB.value = val;

        updateTimeScore();
    }

    // Synchronize Notes textarea between View A and View B
    function handleNotesSync(val) {
        const hidden = document.getElementById('input_hidden_notes');
        if (hidden) hidden.value = val;

        const notesA = document.getElementById('notes_a');
        if (notesA && notesA.value !== val) notesA.value = val;

        const notesB = document.getElementById('notes_b');
        if (notesB && notesB.value !== val) notesB.value = val;
    }

    // Input handler for View Mode A (single number input 0..max)
    function handleInputA(key, maxVal, rawVal) {
        const valStr = (rawVal || '').toString().trim();
        let val = valStr === '' ? '' : parseInt(valStr, 10);

        if (isNaN(val)) val = '';
        if (val !== '' && val > maxVal) val = maxVal;
        if (val !== '' && val < 0) val = 0;

        const inputA = document.getElementById('input_a_' + key);
        if (inputA && val !== '' && inputA.value != val) {
            inputA.value = val;
        }

        const hidden = document.getElementById('input_' + key);
        if (hidden) hidden.value = val;

        highlightPill(key, val, maxVal);
        syncToViewB(key, val, maxVal);

        recalculateAll();
    }

    // Click quick-pick pill in View Mode A
    function setRubricValue(key, targetVal, maxVal) {
        const inputA = document.getElementById('input_a_' + key);
        if (inputA) inputA.value = targetVal;
        handleInputA(key, maxVal, targetVal.toString());
    }

    // Input handler for View Mode B (3 range columns)
    function handleInputB(key, colIdx, maxVal, rawVal) {
        const valStr = (rawVal || '').toString().trim();
        let val = valStr === '' ? '' : parseInt(valStr, 10);
        if (isNaN(val)) val = '';

        if (val !== '') {
            [0, 1, 2].forEach(i => {
                if (i !== colIdx) {
                    const other = document.getElementById(`input_b_col_${key}_${i}`);
                    if (other) other.value = '';
                }
            });
        }

        const hidden = document.getElementById('input_' + key);
        if (hidden) hidden.value = val;

        const inputA = document.getElementById('input_a_' + key);
        if (inputA) inputA.value = val;

        highlightPill(key, val, maxVal);

        recalculateAll();
    }

    function highlightPill(key, val, maxVal) {
        [0, 1, 2].forEach(i => {
            const pill = document.getElementById(`pill_${key}_${i}`);
            if (pill) {
                pill.classList.remove('selected-pill-theme');
                pill.classList.add('bg-slate-100', 'text-slate-700');
            }
        });

        if (val === '' || val === null || isNaN(val)) return;

        let colIdx = -1;
        if (maxVal === 43) {
            if (val <= 14) colIdx = 0;
            else if (val <= 28) colIdx = 1;
            else colIdx = 2;
        } else if (maxVal === 133) {
            if (val <= 44) colIdx = 0;
            else if (val <= 88) colIdx = 1;
            else colIdx = 2;
        }

        if (colIdx >= 0) {
            const activePill = document.getElementById(`pill_${key}_${colIdx}`);
            if (activePill) {
                activePill.classList.add('selected-pill-theme');
                activePill.classList.remove('bg-slate-100', 'text-slate-700');
            }
        }
    }

    function syncToViewB(key, val, maxVal) {
        [0, 1, 2].forEach(i => {
            const col = document.getElementById(`input_b_col_${key}_${i}`);
            if (col) col.value = '';
        });

        if (val === '' || val === null || isNaN(val)) return;

        let colIdx = -1;
        if (maxVal === 43) {
            if (val <= 14) colIdx = 0;
            else if (val <= 28) colIdx = 1;
            else colIdx = 2;
        } else if (maxVal === 133) {
            if (val <= 44) colIdx = 0;
            else if (val <= 88) colIdx = 1;
            else colIdx = 2;
        }

        if (colIdx >= 0) {
            const activeCol = document.getElementById(`input_b_col_${key}_${colIdx}`);
            if (activeCol) activeCol.value = val;
        }
    }

    // ==================== TIME CALCULATIONS ====================

    function parseTimeToSeconds(timeStr) {
        if (!timeStr) return 0;
        timeStr = timeStr.trim().replace('.', ':');
        const parts = timeStr.split(':');
        if (parts.length === 2) {
            const m = parseInt(parts[0], 10) || 0;
            const s = parseInt(parts[1], 10) || 0;
            return (m * 60) + s;
        }
        return parseInt(timeStr, 10) || 0;
    }

    function computeTimeScore(seconds) {
        if (seconds <= 0) return 0;

        if (isLevelMula) {
            // Mula 10 Interval Scale (Maks 10:00)
            if (seconds > 600) return 0;
            const score = 300 - Math.round(seconds * 0.5);
            return Math.max(0, Math.min(300, score));
        } else {
            // Madya & Wira 6 Interval Scale (Maks 05:30)
            if (seconds > 330) return 0;
            if (seconds <= 300) {
                const score = 300 - Math.round(seconds * (250 / 300));
                return Math.max(51, Math.min(300, score));
            } else {
                const diff = seconds - 300;
                const score = 51 - Math.round(diff * (51 / 30));
                return Math.max(0, Math.min(51, score));
            }
        }
    }

    function getTierInfo(seconds) {
        if (seconds <= 0) return { tier: 0, text: '-', label: 'Belum Diisi' };

        if (isLevelMula) {
            if (seconds <= 60) return { tier: 1, text: '271 - 300 Poin', label: '0-1 Menit' };
            if (seconds <= 120) return { tier: 2, text: '241 - 270 Poin', label: '1.1 - 2 Menit' };
            if (seconds <= 180) return { tier: 3, text: '211 - 240 Poin', label: '2.1 - 3 Menit' };
            if (seconds <= 240) return { tier: 4, text: '181 - 210 Poin', label: '3.1 - 4 Menit' };
            if (seconds <= 300) return { tier: 5, text: '151 - 180 Poin', label: '4.1 - 5 Menit' };
            if (seconds <= 360) return { tier: 6, text: '121 - 150 Poin', label: '5.1 - 6 Menit' };
            if (seconds <= 420) return { tier: 7, text: '91 - 120 Poin', label: '6.1 - 7 Menit' };
            if (seconds <= 480) return { tier: 8, text: '61 - 90 Poin', label: '7.1 - 8 Menit' };
            if (seconds <= 540) return { tier: 9, text: '31 - 60 Poin', label: '8.1 - 9 Menit' };
            if (seconds <= 600) return { tier: 10, text: '0 - 30 Poin', label: '9.1 - 10 Menit' };
            return { tier: 11, text: '0 Poin', label: '> 10 Menit' };
        } else {
            // Madya & Wira 6 Interval
            if (seconds <= 60) return { tier: 1, text: '251 - 300 Poin', label: '0-1 Menit' };
            if (seconds <= 120) return { tier: 2, text: '201 - 250 Poin', label: '1.1 - 2 Menit' };
            if (seconds <= 180) return { tier: 3, text: '151 - 200 Poin', label: '2.1 - 3 Menit' };
            if (seconds <= 240) return { tier: 4, text: '101 - 150 Poin', label: '3.1 - 4 Menit' };
            if (seconds <= 300) return { tier: 5, text: '51 - 100 Poin', label: '4.1 - 5 Menit' };
            if (seconds <= 330) return { tier: 6, text: '0 - 51 Poin', label: '5.1 - 5.6 Menit' };
            return { tier: 7, text: '0 Poin', label: '> 05:6 Menit' };
        }
    }

    function updateTimeScore() {
        const h = document.getElementById('input_hidden_time_recorded');
        const timeStr = h ? h.value.trim() : '';

        const seconds = parseTimeToSeconds(timeStr);
        const timeScore = computeTimeScore(seconds);
        const tierInfo = getTierInfo(seconds);

        // Update View A badges
        const ptsA = document.getElementById('a_time_pts');
        if (ptsA) ptsA.innerText = `${timeScore} Poin`;
        const tierA = document.getElementById('a_time_tier');
        if (tierA) tierA.innerText = seconds > 0 ? tierInfo.label : '-';

        // Highlight View A visual cards
        for (let i = 1; i <= intervalCount; i++) {
            const card = document.getElementById('card_interval_' + i);
            if (card) {
                if (tierInfo.tier === i) {
                    card.classList.add('active-interval-card-theme');
                } else {
                    card.classList.remove('active-interval-card-theme');
                }
            }
        }

        // Update View B badges & table cells
        const ptsB = document.getElementById('b_waktu_badge_pts');
        if (ptsB) ptsB.innerText = `${timeScore} Poin`;
        const tierB = document.getElementById('b_waktu_badge_tier');
        if (tierB) tierB.innerText = seconds > 0 ? `Interval: ${tierInfo.label}` : 'Interval: -';

        for (let i = 1; i <= intervalCount; i++) {
            const cell = document.getElementById('b_tier_cell_' + i);
            const lbl = document.getElementById('b_interval_lbl_' + i);
            if (cell && lbl) {
                if (tierInfo.tier === i) {
                    cell.classList.add('bg-amber-300', 'text-slate-950', 'ring-2', 'ring-amber-500', 'ring-inset');
                    lbl.classList.add('bg-amber-200', 'text-slate-950');
                } else {
                    cell.classList.remove('bg-amber-300', 'text-slate-950', 'ring-2', 'ring-amber-500', 'ring-inset');
                    lbl.classList.remove('bg-amber-200', 'text-slate-950');
                }
            }
        }

        recalculateAll();
    }

    function recalculateAll() {
        const h = document.getElementById('input_hidden_time_recorded');
        const timeStr = h ? h.value.trim() : '';
        const seconds = parseTimeToSeconds(timeStr);
        const timeScore = computeTimeScore(seconds);

        const ketepatanKeys = ['ketepatan_1', 'ketepatan_2', 'ketepatan_3', 'ketepatan_4', 'ketepatan_5', 'ketepatan_6', 'ketepatan_7'];
        let subtotalKetepatan = 0;
        ketepatanKeys.forEach(k => {
            const el = document.getElementById('input_' + k);
            if (el && el.value) {
                subtotalKetepatan += parseInt(el.value, 10) || 0;
            }
        });

        const kekuatanKeys = ['kekuatan_1', 'kekuatan_2', 'kekuatan_3'];
        let subtotalKekuatan = 0;
        kekuatanKeys.forEach(k => {
            const el = document.getElementById('input_' + k);
            if (el && el.value) {
                subtotalKekuatan += parseInt(el.value, 10) || 0;
            }
        });

        const technicalScore = subtotalKetepatan + subtotalKekuatan;
        const totalScore = technicalScore + timeScore;

        // View A Subtotals
        const subKetA = document.getElementById('display_a_subtotal_ketepatan');
        if (subKetA) subKetA.innerText = subtotalKetepatan;
        const subKekA = document.getElementById('display_a_subtotal_kekuatan');
        if (subKekA) subKekA.innerText = subtotalKekuatan;
        const subWakA = document.getElementById('display_a_subtotal_waktu');
        if (subWakA) subWakA.innerText = timeScore;

        // View B Subtotals & Footer
        const subKetB = document.getElementById('display_b_subtotal_ketepatan');
        if (subKetB) subKetB.innerText = subtotalKetepatan;
        const subKekB = document.getElementById('display_b_subtotal_kekuatan');
        if (subKekB) subKekB.innerText = subtotalKekuatan;
        const subWakB = document.getElementById('display_b_subtotal_waktu');
        if (subWakB) subWakB.innerText = timeScore;

        const footTekB = document.getElementById('display_b_footer_teknis');
        if (footTekB) footTekB.innerText = technicalScore;
        const footWakB = document.getElementById('display_b_footer_waktu');
        if (footWakB) footWakB.innerText = timeScore;
        const footTotB = document.getElementById('display_b_total_score');
        if (footTotB) footTotB.innerText = totalScore;

        // Header Total Badge
        const headerTot = document.getElementById('header_total_score');
        if (headerTot) headerTot.innerText = totalScore;

        // Sticky Summary Bar
        const stickyKet = document.getElementById('sticky_ketepatan');
        if (stickyKet) stickyKet.innerText = subtotalKetepatan;
        const stickyKek = document.getElementById('sticky_kekuatan');
        if (stickyKek) stickyKek.innerText = subtotalKekuatan;
        const stickyWak = document.getElementById('sticky_waktu');
        if (stickyWak) stickyWak.innerText = `+${timeScore}`;
        const stickyTot = document.getElementById('sticky_total');
        if (stickyTot) stickyTot.innerText = totalScore;

        // Hidden Inputs
        const inSubKet = document.getElementById('input_subtotal_ketepatan');
        if (inSubKet) inSubKet.value = subtotalKetepatan;
        const inSubKek = document.getElementById('input_subtotal_kekuatan');
        if (inSubKek) inSubKek.value = subtotalKekuatan;
        const inSubTek = document.getElementById('input_subtotal_teknis');
        if (inSubTek) inSubTek.value = technicalScore;
        const inSubWak = document.getElementById('input_subtotal_waktu');
        if (inSubWak) inSubWak.value = timeScore;
        const inSubTot = document.getElementById('input_subtotal_total');
        if (inSubTot) inSubTot.value = totalScore;
    }

    // Run on initial load
    document.addEventListener('DOMContentLoaded', function() {
        try {
            const savedMode = localStorage.getItem('lktr_view_mode_' + categoryLevel) || 'A';
            switchViewMode(savedMode);
        } catch (e) {
            switchViewMode('A');
        }

        // Highlight initial pills based on saved values
        for (let i = 1; i <= 7; i++) {
            const val = document.getElementById('input_ketepatan_' + i)?.value;
            if (val) highlightPill('ketepatan_' + i, parseInt(val, 10), 43);
        }
        for (let i = 1; i <= 3; i++) {
            const val = document.getElementById('input_kekuatan_' + i)?.value;
            if (val) highlightPill('kekuatan_' + i, parseInt(val, 10), 133);
        }

        updateTimeScore();
        recalculateAll();
    });
</script>
@endsection
