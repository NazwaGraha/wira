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
    $isLevelMula = strcasecmp(trim($category->level ?? ''), 'Mula') === 0;
@endphp
<div class="space-y-6">

    <style>
        .selected-pill {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%) !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2), 0 4px 10px rgba(220, 38, 38, 0.4);
            border-color: #f87171 !important;
            transform: scale(1.04);
        }
        .highlight-time-row {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.25) 0%, rgba(245, 158, 11, 0.1) 100%) !important;
            border-left: 4px solid #f59e0b !important;
            font-weight: 800 !important;
            color: #b45309 !important;
        }
        .active-interval-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
            border-color: #f59e0b !important;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35) !important;
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

    {{-- Header Info Card --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-rose-950 p-6 rounded-3xl shadow-xl text-white flex flex-col lg:flex-row justify-between lg:items-center gap-4 border border-slate-700">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 via-rose-500 to-red-600 p-0.5 shadow-lg flex items-center justify-center shrink-0">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <i class="fa-solid fa-bed text-amber-400 text-2xl"></i>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        {{ $category->code ?? 'LKTR' }}
                    </span>
                    <span class="text-xs text-slate-300 font-bold">PMR {{ $category->level }} &bull; Kategori {{ $category->gender_category }}</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    {{ $category->name }}
                </h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    @if($isLevelMula)
                        Format Khusus PMR Mula: Ketepatan (Maks 301) + Kekuatan (Maks 399) + Waktu Tempuh 10 Interval (Maks 300) = Total 1000 Poin.
                    @else
                        1-Click Checklist Nilai Teknis (Maks 680) + Auto Look-up Nilai Waktu (Maks 300) &bull; Sinkronisasi Otomatis ke Lembar Skor Utama.
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 bg-slate-800/90 px-4 py-2 rounded-2xl border border-slate-700 text-xs">
                <span class="text-slate-400 font-bold uppercase text-[10px]">Formula:</span>
                <span class="text-emerald-400 font-black">Total = Teknis + Waktu</span>
            </div>
            <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="px-4 py-2 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-md">
                <i class="fa-solid fa-table-list"></i> Buka Lembar Skor Utama
            </a>
        </div>
    </div>

    @if($isLevelMula)
        {{-- ========================================================================= --}}
        {{-- ==================== WORKSPACE KHUSUS TINGKAT MULA ====================== --}}
        {{-- ========================================================================= --}}
        @php
            $schoolName = $currentTeam->registration->school_name ?? $currentTeam->team_name;
            $teamCode = 'No. Urut ' . str_pad($currentTeam->order_number ?? '1', 2, '0', STR_PAD_LEFT);
            $rubricData = is_array($existingRubric) ? $existingRubric : [];

            $vKetepatan1 = $rubricData['ketepatan_1'] ?? null;
            $vKetepatan2 = $rubricData['ketepatan_2'] ?? null;
            $vKetepatan3 = $rubricData['ketepatan_3'] ?? null;
            $vKetepatan4 = $rubricData['ketepatan_4'] ?? null;
            $vKetepatan5 = $rubricData['ketepatan_5'] ?? null;
            $vKetepatan6 = $rubricData['ketepatan_6'] ?? null;
            $vKetepatan7 = $rubricData['ketepatan_7'] ?? null;

            $vKekuatanMula1 = $rubricData['kekuatan_1'] ?? null;
            $vKekuatanMula2 = $rubricData['kekuatan_2'] ?? null;
            $vKekuatanMula3 = $rubricData['kekuatan_3'] ?? null;

            $mulaKetepatanItems = [
                ['num' => 1, 'key' => 'ketepatan_1', 'label' => 'Bentuk Simetris', 'desc' => 'Kerapihan bentuk tandu seimbang antara kiri dan kanan', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan1, 'max' => 43],
                ['num' => 2, 'key' => 'ketepatan_2', 'label' => 'Jarak ikatan jangkar sejajar dengan sebrangnya', 'desc' => 'Simetri posisi ikatan jangkar antara palang kiri dan kanan', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan2, 'max' => 43],
                ['num' => 3, 'key' => 'ketepatan_3', 'label' => 'Ikatan mati 4', 'desc' => 'Ketepatan simpul mati pada 4 sudut bambu', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan3, 'max' => 43],
                ['num' => 4, 'key' => 'ketepatan_4', 'label' => 'Ikatan jangkar 7', 'desc' => 'Ketepatan 7 ikatan jangkar sepanjang tandu', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan4, 'max' => 43],
                ['num' => 5, 'key' => 'ketepatan_5', 'label' => 'Tali tubuh', 'desc' => 'Pemasangan tali tubuh pengaman korban', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan5, 'max' => 43],
                ['num' => 6, 'key' => 'ketepatan_6', 'label' => 'Sisa bambu pendek dan panjang', 'desc' => 'Ketepatan ukuran sisa ujung bambu sesuai standar', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan6, 'max' => 43],
                ['num' => 7, 'key' => 'ketepatan_7', 'label' => 'Mitela Presisi rapih', 'desc' => 'Kerapihan lipatan dan kekencangan mitela pembalut', 'opts' => [14, 28, 43], 'ranges' => ['0-14', '15-28', '29-43'], 'val' => $vKetepatan7, 'max' => 43],
            ];

            $mulaKekuatanItems = [
                ['num' => 1, 'key' => 'kekuatan_1', 'label' => 'Kekuatan Jaring / tali jangkar', 'desc' => 'Kekokohan anyaman jaring saat diuji beban', 'opts' => [44, 88, 133], 'ranges' => ['0-44', '45-88', '89-133'], 'val' => $vKekuatanMula1, 'max' => 133],
                ['num' => 2, 'key' => 'kekuatan_2', 'label' => 'Kekuatan bambu pendek (diputar)', 'desc' => 'Uji puntir pada bambu palang pendek tidak mudah bergeser', 'opts' => [44, 88, 133], 'ranges' => ['0-44', '45-88', '89-133'], 'val' => $vKekuatanMula2, 'max' => 133],
                ['num' => 3, 'key' => 'kekuatan_3', 'label' => 'Kekuatan bambu panjang (diputar) digoyang', 'desc' => 'Uji goyang dan puntir pada bambu utama tetap kokoh', 'opts' => [44, 88, 133], 'ranges' => ['0-44', '45-88', '89-133'], 'val' => $vKekuatanMula3, 'max' => 133],
            ];

            $vWaktu = $existingTime ?: '05:00';
        @endphp

        <div class="max-w-6xl mx-auto space-y-6">

            {{-- 1. TOP TEAM NAVIGATION & VIEW SWITCHER CARD --}}
            <div class="bg-white rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
                
                {{-- Team Prev / Select / Next --}}
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
                            @foreach($teams as $idx => $t)
                                @php
                                    $tScore = $scores->get($t->id);
                                    $hasScore = !empty($tScore && $tScore->final_score > 0);
                                @endphp
                                <option value="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $t->id, 'round' => $round]) }}"
                                        {{ ($currentTeam && $currentTeam->id == $t->id) ? 'selected' : '' }}>
                                    {{ $t->order_number ?? ($idx + 1) }}. {{ $t->registration->school_name ?? $t->team_name }} ({{ $hasScore ? '✓ ' . number_format($tScore->final_score, 0) . ' pt' : 'Belum Dinilai' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($nextTeam)
                        <a href="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $nextTeam->id, 'round' => $round]) }}"
                           class="px-3.5 py-2 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
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
                    <button type="button" id="btnViewModeA" onclick="switchMulaViewMode('A')" 
                            class="px-3.5 py-1.5 rounded-xl font-extrabold text-xs transition flex items-center gap-1.5 bg-rose-600 text-white shadow-sm">
                        <i class="fa-solid fa-table-cells-large text-[11px]"></i> Mode Kartu Modern (A)
                    </button>
                    <button type="button" id="btnViewModeB" onclick="switchMulaViewMode('B')" 
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900">
                        <i class="fa-solid fa-file-lines text-[11px]"></i> Mode Tabel Kertas (B)
                    </button>
                </div>

            </div>

            {{-- 2. ACTIVE TEAM HERO CARD --}}
            @if($currentTeam)
                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-rose-950 rounded-3xl p-5 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl border border-slate-700">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white font-mono font-black text-lg flex items-center justify-center shadow-md shrink-0">
                            {{ $currentTeam->order_number ?? '1' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 px-2 py-0.5 rounded-md border border-rose-500/30">
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
                            <span class="text-[9px] text-slate-400 uppercase tracking-wider block font-bold">Total Nilai Saat Ini</span>
                            <span class="text-2xl font-black text-emerald-400 font-mono" id="mula_header_total_score">0</span>
                        </div>
                    </div>
                </div>

                {{-- FORM PENILAIAN UTAMA MULA --}}
                <form action="{{ route('admin.competition-scores.grid-save', $category->id) }}" method="POST" id="formGridLKTRMula">
                    @csrf
                    <input type="hidden" name="competition_participant_team_id" value="{{ $currentTeam->id }}">
                    <input type="hidden" name="round_name" value="{{ $round }}">
                    <input type="hidden" name="action_after" id="actionAfterInput" value="stay">
                    <input type="hidden" name="next_team_id" value="{{ $nextTeam?->id }}">

                    {{-- Hidden Rubric Inputs (Single Source of Truth) --}}
                    <input type="hidden" name="rubric[ketepatan_1]" id="input_ketepatan_1" value="{{ $vKetepatan1 }}">
                    <input type="hidden" name="rubric[ketepatan_2]" id="input_ketepatan_2" value="{{ $vKetepatan2 }}">
                    <input type="hidden" name="rubric[ketepatan_3]" id="input_ketepatan_3" value="{{ $vKetepatan3 }}">
                    <input type="hidden" name="rubric[ketepatan_4]" id="input_ketepatan_4" value="{{ $vKetepatan4 }}">
                    <input type="hidden" name="rubric[ketepatan_5]" id="input_ketepatan_5" value="{{ $vKetepatan5 }}">
                    <input type="hidden" name="rubric[ketepatan_6]" id="input_ketepatan_6" value="{{ $vKetepatan6 }}">
                    <input type="hidden" name="rubric[ketepatan_7]" id="input_ketepatan_7" value="{{ $vKetepatan7 }}">

                    <input type="hidden" name="rubric[kekuatan_1]" id="input_kekuatan_1" value="{{ $vKekuatanMula1 }}">
                    <input type="hidden" name="rubric[kekuatan_2]" id="input_kekuatan_2" value="{{ $vKekuatanMula2 }}">
                    <input type="hidden" name="rubric[kekuatan_3]" id="input_kekuatan_3" value="{{ $vKekuatanMula3 }}">

                    <input type="hidden" name="subtotals[ketepatan]" id="input_subtotal_ketepatan" value="0">
                    <input type="hidden" name="subtotals[kekuatan]" id="input_subtotal_kekuatan" value="0">
                    <input type="hidden" name="subtotals[teknis]" id="input_subtotal_teknis" value="0">
                    <input type="hidden" name="subtotals[waktu]" id="input_subtotal_waktu" value="0">
                    <input type="hidden" name="subtotals[total]" id="input_subtotal_total" value="0">

                    {{-- Hidden Time & Notes --}}
                    <input type="hidden" name="time_recorded" id="input_hidden_time_recorded" value="{{ $vWaktu }}">
                    <input type="hidden" name="notes" id="input_hidden_notes" value="{{ $existingNotes }}">

                    {{-- ========================================================= --}}
                    {{-- ================== VIEW MODE A (OPSI A) ================= --}}
                    {{-- ========================================================= --}}
                    <div id="mulaViewModeA" class="space-y-6">

                        {{-- SEKSI I: KETEPATAN (Maks 301) --}}
                        <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-slate-200 gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-lg">
                                        <i class="fa-solid fa-compass-drafting"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-base text-slate-900 uppercase tracking-tight">I. Ketepatan</h3>
                                        <p class="text-xs text-slate-500">7 Butir Penilaian &bull; Masing-masing bernilai maksimal 43 poin</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 bg-rose-50 px-4 py-2 rounded-2xl border border-rose-200 text-xs self-start sm:self-center">
                                    <span class="text-slate-600 font-bold uppercase text-[10px]">Subtotal Ketepatan:</span>
                                    <span class="font-mono font-black text-rose-600 text-sm">
                                        <span id="display_mula_a_subtotal_ketepatan">0</span> / 301 Poin
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
                                        @foreach($mulaKetepatanItems as $it)
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
                                                                id="pill_mula_ketepatan_{{ $it['num'] }}_0" 
                                                                onclick="setMulaRubricValue('ketepatan_{{ $it['num'] }}', 14, 43)" 
                                                                class="btn-pill-ketepatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                            0 - 14
                                                        </button>
                                                        <button type="button" 
                                                                id="pill_mula_ketepatan_{{ $it['num'] }}_1" 
                                                                onclick="setMulaRubricValue('ketepatan_{{ $it['num'] }}', 28, 43)" 
                                                                class="btn-pill-ketepatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                            15 - 28
                                                        </button>
                                                        <button type="button" 
                                                                id="pill_mula_ketepatan_{{ $it['num'] }}_2" 
                                                                onclick="setMulaRubricValue('ketepatan_{{ $it['num'] }}', 43, 43)" 
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
                                                               oninput="handleMulaInputA('ketepatan_{{ $it['num'] }}', 43, this.value)" 
                                                               class="w-20 text-center font-mono font-black text-sm bg-white text-slate-900 border-2 border-slate-300 rounded-xl py-2 px-2 focus:border-rose-500 focus:ring-2 focus:ring-rose-200 focus:outline-none transition shadow-2xs">
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
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-lg">
                                        <i class="fa-solid fa-dumbbell"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-base text-slate-900 uppercase tracking-tight">II. Kekuatan</h3>
                                        <p class="text-xs text-slate-500">3 Butir Penilaian &bull; Masing-masing bernilai maksimal 133 poin</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 bg-amber-50 px-4 py-2 rounded-2xl border border-amber-200 text-xs self-start sm:self-center">
                                    <span class="text-slate-600 font-bold uppercase text-[10px]">Subtotal Kekuatan:</span>
                                    <span class="font-mono font-black text-amber-800 text-sm">
                                        <span id="display_mula_a_subtotal_kekuatan">0</span> / 399 Poin
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
                                        @foreach($mulaKekuatanItems as $it)
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
                                                                id="pill_mula_kekuatan_{{ $it['num'] }}_0" 
                                                                onclick="setMulaRubricValue('kekuatan_{{ $it['num'] }}', 44, 133)" 
                                                                class="btn-pill-kekuatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                            0 - 44
                                                        </button>
                                                        <button type="button" 
                                                                id="pill_mula_kekuatan_{{ $it['num'] }}_1" 
                                                                onclick="setMulaRubricValue('kekuatan_{{ $it['num'] }}', 88, 133)" 
                                                                class="btn-pill-kekuatan_{{ $it['num'] }} px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                            45 - 88
                                                        </button>
                                                        <button type="button" 
                                                                id="pill_mula_kekuatan_{{ $it['num'] }}_2" 
                                                                onclick="setMulaRubricValue('kekuatan_{{ $it['num'] }}', 133, 133)" 
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
                                                               oninput="handleMulaInputA('kekuatan_{{ $it['num'] }}', 133, this.value)" 
                                                               class="w-24 text-center font-mono font-black text-sm bg-white text-slate-900 border-2 border-slate-300 rounded-xl py-2 px-2 focus:border-rose-500 focus:ring-2 focus:ring-rose-200 focus:outline-none transition shadow-2xs">
                                                        <span class="text-xs font-bold text-slate-400">/ 133</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- SEKSI III: KECEPATAN / WAKTU TEMPUH (Maks 300) --}}
                        <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-slate-200 gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
                                        <i class="fa-solid fa-stopwatch"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-base text-slate-900 uppercase tracking-tight">III. Kecepatan (Waktu Tempuh)</h3>
                                        <p class="text-xs text-slate-500">Skala 10 Interval Menit Resmi Dewan Juri &bull; Maksimal 300 Poin</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 bg-emerald-50 px-4 py-2 rounded-2xl border border-emerald-200 text-xs self-start sm:self-center">
                                    <span class="text-slate-600 font-bold uppercase text-[10px]">Poin Waktu:</span>
                                    <span class="font-mono font-black text-emerald-700 text-sm">
                                        <span id="display_mula_a_subtotal_waktu">0</span> / 300 Poin
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
                                           placeholder="04:25" 
                                           oninput="handleMulaTimeSync(this.value)" 
                                           class="w-32 text-center font-mono font-black text-xl bg-amber-50 text-amber-950 border-2 border-amber-400 rounded-xl px-3 py-1.5 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none shadow-inner">
                                    <span class="text-[11px] text-slate-500 font-medium">Format: Menit:Detik (contoh: 04:25)</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="px-4 py-2 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 font-mono text-center">
                                        <span class="text-[9px] text-emerald-700 font-bold block uppercase tracking-wider">Hasil Poin:</span>
                                        <span class="text-base font-black" id="mula_a_time_pts">0 Poin</span>
                                    </div>
                                    <div class="px-4 py-2 rounded-xl bg-slate-200 border border-slate-300 text-slate-800 font-mono text-center">
                                        <span class="text-[9px] text-slate-500 font-bold block uppercase tracking-wider">Interval Aktif:</span>
                                        <span class="text-xs font-black" id="mula_a_time_tier">-</span>
                                    </div>
                                </div>
                            </div>

                            {{-- 10 Interval Visual Grid Cards --}}
                            <div>
                                <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider block mb-2">Tabel 10 Interval Konversi Waktu Dewan Juri:</span>
                                <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-2">
                                    @php
                                        $intervals = [
                                            ['idx' => 1, 'min' => '0-1', 'range' => '271 - 300', 'color' => 'emerald'],
                                            ['idx' => 2, 'min' => '1.1 - 2', 'range' => '241 - 270', 'color' => 'emerald'],
                                            ['idx' => 3, 'min' => '2.1 - 3', 'range' => '211 - 240', 'color' => 'slate'],
                                            ['idx' => 4, 'min' => '3.1 - 4', 'range' => '181 - 210', 'color' => 'slate'],
                                            ['idx' => 5, 'min' => '4.1 - 5', 'range' => '151 - 180', 'color' => 'slate'],
                                            ['idx' => 6, 'min' => '5.1 - 6', 'range' => '121 - 150', 'color' => 'slate'],
                                            ['idx' => 7, 'min' => '6.1 - 7', 'range' => '91 - 120', 'color' => 'slate'],
                                            ['idx' => 8, 'min' => '7.1 - 8', 'range' => '61 - 90', 'color' => 'slate'],
                                            ['idx' => 9, 'min' => '8.1 - 9', 'range' => '31 - 60', 'color' => 'slate'],
                                            ['idx' => 10, 'min' => '9.1 - 10', 'range' => '0 - 30', 'color' => 'rose'],
                                        ];
                                    @endphp
                                    @foreach($intervals as $int)
                                        <div id="mula_card_interval_{{ $int['idx'] }}" class="p-2 rounded-xl border border-slate-200 bg-white text-center transition">
                                            <span class="text-[10px] font-bold text-slate-500 block">{{ $int['min'] }} Menit</span>
                                            <span class="text-xs font-mono font-black text-{{ $int['color'] }}-700 block mt-0.5">{{ $int['range'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        {{-- SEKSI IV: CATATAN KHUSUS DEWAN JURI --}}
                        <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-comment-dots text-slate-400"></i> Catatan Khusus Dewan Juri:
                            </label>
                            <textarea id="notes_a" rows="2" placeholder="Tuliskan catatan teknis regu jika ada (opsional)..." oninput="handleMulaNotesSync(this.value)" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500">{{ $existingNotes }}</textarea>
                        </div>

                    </div>


                    {{-- ========================================================= --}}
                    {{-- ================== VIEW MODE B (OPSI B) ================= --}}
                    {{-- ========================================================= --}}
                    <div id="mulaViewModeB" class="hidden space-y-6">

                        {{-- KERTAS LEMBAR PENILAIAN RESMI (TABULAR PENUH 1000px) --}}
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
                                        <div class="border-2 border-slate-900 bg-white font-black text-base px-6 py-1.5 tracking-widest text-slate-900 shadow-sm rounded-lg text-center uppercase">
                                            MULA
                                        </div>
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

                            {{-- Tabel Penilaian 5 Kolom Sesuai Format Kertas --}}
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
                                            <td rowspan="{{ count($mulaKetepatanItems) + 1 }}" class="py-2.5 px-2 text-center border-r-2 border-slate-900 font-bold align-middle bg-slate-100 text-sm">
                                                1
                                            </td>
                                            <td class="py-2.5 px-4 tracking-wider uppercase font-black bg-slate-200/90 border-r border-slate-300">
                                                KETEPATAN <span class="text-[10px] text-slate-600 font-bold ml-1">(Maksimal: 301 Poin)</span>
                                            </td>
                                            <td colspan="3" class="py-2 px-3 text-right bg-slate-300/80 font-mono font-black text-xs text-slate-900">
                                                Subtotal Ketepatan: <span id="display_mula_b_subtotal_ketepatan" class="text-rose-600 text-sm">0</span> / 301 Poin
                                            </td>
                                        </tr>

                                        @foreach($mulaKetepatanItems as $it)
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
                                                        oninput="handleMulaInputB('ketepatan_{{ $it['num'] }}', 0, 43, this.value)" 
                                                        class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                                </td>
                                                <td class="p-1 border-r border-slate-300 w-32">
                                                    <input type="number" 
                                                        id="input_b_col_ketepatan_{{ $it['num'] }}_1" 
                                                        value="{{ $col1Val }}" 
                                                        placeholder="15-28" 
                                                        oninput="handleMulaInputB('ketepatan_{{ $it['num'] }}', 1, 43, this.value)" 
                                                        class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                                </td>
                                                <td class="p-1 w-32">
                                                    <input type="number" 
                                                        id="input_b_col_ketepatan_{{ $it['num'] }}_2" 
                                                        value="{{ $col2Val }}" 
                                                        placeholder="29-43" 
                                                        oninput="handleMulaInputB('ketepatan_{{ $it['num'] }}', 2, 43, this.value)" 
                                                        class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- 2. KEKUATAN --}}
                                        <tr class="bg-slate-200/90 text-slate-900 font-black text-xs text-left border-t-2 border-slate-900">
                                            <td rowspan="{{ count($mulaKekuatanItems) + 2 }}" class="py-2.5 px-2 text-center border-r-2 border-slate-900 font-bold align-middle bg-slate-100 text-sm">
                                                2
                                            </td>
                                            <td class="py-2.5 px-4 tracking-wider uppercase font-black bg-slate-200/90 border-r border-slate-300">
                                                KEKUATAN <span class="text-[10px] text-slate-600 font-bold ml-1">(Maksimal: 399 Poin)</span>
                                            </td>
                                            <td colspan="3" class="py-2 px-3 text-right bg-slate-300/80 font-mono font-black text-xs text-slate-900">
                                                Subtotal Kekuatan: <span id="display_mula_b_subtotal_kekuatan" class="text-rose-600 text-sm">0</span> / 399 Poin
                                            </td>
                                        </tr>
                                        <tr class="bg-slate-700 text-slate-200 text-[10px] font-black uppercase text-center">
                                            <td class="py-1 px-4 text-left border-r border-slate-600 font-bold text-slate-300">RENTANG NILAI KEKUATAN :</td>
                                            <th class="py-1 px-2 border-r border-slate-600 w-32">0-44</th>
                                            <th class="py-1 px-2 border-r border-slate-600 w-32">45-88</th>
                                            <th class="py-1 px-2 w-32">89-133</th>
                                        </tr>

                                        @foreach($mulaKekuatanItems as $it)
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
                                                        oninput="handleMulaInputB('kekuatan_{{ $it['num'] }}', 0, 133, this.value)" 
                                                        class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                                </td>
                                                <td class="p-1 border-r border-slate-300 w-32">
                                                    <input type="number" 
                                                        id="input_b_col_kekuatan_{{ $it['num'] }}_1" 
                                                        value="{{ $col1Val }}" 
                                                        placeholder="45-88" 
                                                        oninput="handleMulaInputB('kekuatan_{{ $it['num'] }}', 1, 133, this.value)" 
                                                        class="w-full text-center font-mono font-black text-xs sm:text-sm bg-white text-slate-900 border border-slate-300 rounded-lg py-1.5 px-1 focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                                </td>
                                                <td class="p-1 w-32">
                                                    <input type="number" 
                                                        id="input_b_col_kekuatan_{{ $it['num'] }}_2" 
                                                        value="{{ $col2Val }}" 
                                                        placeholder="89-133" 
                                                        oninput="handleMulaInputB('kekuatan_{{ $it['num'] }}', 2, 133, this.value)" 
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
                                                WAKTU (Menit) <span class="text-[10px] text-slate-600 font-bold ml-1">(Skala 10 Interval Menit &bull; Maks 300 Poin)</span>
                                            </td>
                                            <td colspan="3" class="py-2 px-3 text-right bg-emerald-100/90 font-mono font-black text-xs text-emerald-900">
                                                Poin Waktu: <span id="display_mula_b_subtotal_waktu" class="text-emerald-700 text-sm font-black">0</span> / 300 Poin
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="p-0 border-t border-slate-300">
                                                <div class="grid grid-cols-10 divide-x divide-slate-300 text-center font-mono text-[10px] bg-slate-50">
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_1">0-1</div>
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_2">1.1 - 2</div>
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_3">2.1 - 3</div>
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_4">3.1 - 4</div>
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_5">4.1 - 5</div>
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_6">5.1 - 6</div>
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_7">6.1 - 7</div>
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_8">7.1 - 8</div>
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_9">8.1 - 9</div>
                                                    <div class="py-1.5 font-bold" id="mula_b_interval_lbl_10">9.1-10</div>
                                                </div>
                                                <div class="grid grid-cols-10 divide-x divide-slate-300 text-center font-mono text-[10px] border-t border-slate-300 bg-white">
                                                    <div class="py-1.5 font-black text-emerald-700 transition" id="mula_b_tier_cell_1">271-300</div>
                                                    <div class="py-1.5 font-black text-emerald-700 transition" id="mula_b_tier_cell_2">241-270</div>
                                                    <div class="py-1.5 font-black text-slate-700 transition" id="mula_b_tier_cell_3">211-240</div>
                                                    <div class="py-1.5 font-black text-slate-700 transition" id="mula_b_tier_cell_4">181-210</div>
                                                    <div class="py-1.5 font-black text-slate-700 transition" id="mula_b_tier_cell_5">151-180</div>
                                                    <div class="py-1.5 font-black text-slate-700 transition" id="mula_b_tier_cell_6">121-150</div>
                                                    <div class="py-1.5 font-black text-slate-700 transition" id="mula_b_tier_cell_7">91-120</div>
                                                    <div class="py-1.5 font-black text-slate-700 transition" id="mula_b_tier_cell_8">61-90</div>
                                                    <div class="py-1.5 font-black text-slate-700 transition" id="mula_b_tier_cell_9">31-60</div>
                                                    <div class="py-1.5 font-black text-rose-600 transition" id="mula_b_tier_cell_10">0-30</div>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-slate-900 text-white font-black text-sm border-t-2 border-slate-900">
                                            <td colspan="4" class="py-3 px-4 text-right tracking-wider uppercase text-slate-300 text-xs">
                                                TOTAL NILAI AKUMULASI (TEKNIS: <span id="display_mula_b_footer_teknis">0</span> + WAKTU: <span id="display_mula_b_footer_waktu">0</span>) :
                                            </td>
                                            <td class="py-3 px-2 bg-gradient-to-r from-red-600 to-rose-600 text-white font-mono text-xl tracking-tight text-center" id="display_mula_b_total_score">
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
                                        placeholder="04:25" 
                                        oninput="handleMulaTimeSync(this.value)" 
                                        class="w-36 text-center font-mono font-black text-xl bg-amber-50 text-amber-950 border-2 border-amber-400 rounded-xl px-3 py-1.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-inner">
                                    <div class="text-left font-mono">
                                        <div class="text-xs font-black text-emerald-600" id="mula_b_waktu_badge_pts">0 Poin</div>
                                        <div class="text-[10px] text-slate-500 font-bold" id="mula_b_waktu_badge_tier">Interval: -</div>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-500 mt-2 block">Format: Menit:Detik (contoh: 04:25) &bull; Konversi otomatis 10 Interval</span>
                            </div>

                            {{-- Catatan Dewan Juri --}}
                            <div class="pt-1 mb-4">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-comment-dots text-slate-400"></i> Catatan Khusus Dewan Juri:
                                </label>
                                <textarea id="notes_b" rows="2" placeholder="Tuliskan catatan teknis regu jika ada (opsional)..." oninput="handleMulaNotesSync(this.value)" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500">{{ $existingNotes }}</textarea>
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
                    {{-- ========= STICKY BOTTOM ACTION SUMMARY BAR (MULA) ======= --}}
                    {{-- ========================================================= --}}
                    <div class="sticky bottom-4 z-40 bg-slate-900/95 backdrop-blur-md rounded-2xl p-4 border border-slate-700 shadow-2xl text-white flex flex-col md:flex-row items-center justify-between gap-4">
                        
                        {{-- Ringkasan Poin --}}
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
                                <span class="text-[9px] text-rose-300 block font-sans uppercase">TOTAL AKHIR</span>
                                <span class="text-xl font-black text-emerald-400" id="sticky_total">0</span>
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
            @endif

        </div>

    @else
        {{-- ========================================================================= --}}
        {{-- ================= WORKSPACE LKTR MADYA & WIRA (ORIGINAL) ================ --}}
        {{-- ========================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- SIDEBAR KIRI: DAFTAR REGU PESERTA --}}
            <div class="lg:col-span-4 xl:col-span-3 bg-white rounded-3xl p-4 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                    <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-users-line text-rose-600"></i>
                        Daftar Regu Peserta
                    </h3>
                    <span class="text-[11px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                        {{ $teams->count() }} Regu
                    </span>
                </div>

                <div class="space-y-2 max-h-[750px] overflow-y-auto pr-1">
                    @forelse($teams as $idx => $t)
                        @php
                            $isActive = ($currentTeam && $currentTeam->id == $t->id);
                            $tScore = $scores->get($t->id);
                            $isGraded = !empty($tScore && $tScore->final_score > 0);
                            $reg = $t->registration;
                        @endphp
                        <a href="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $t->id, 'round' => $round]) }}"
                           class="block p-3 rounded-2xl border transition relative {{ $isActive ? 'bg-rose-50/90 border-rose-400 shadow-sm ring-2 ring-rose-400/20' : 'bg-slate-50 hover:bg-white border-slate-200' }}">
                            
                            <div class="flex items-center justify-between gap-2">
                                <span class="w-6 h-6 rounded-full text-[11px] font-black flex items-center justify-center {{ $isActive ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $t->order_number ?? ($idx + 1) }}
                                </span>
                                
                                @if($isGraded)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                        <i class="fa-solid fa-check text-[9px]"></i> {{ number_format($tScore->final_score, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-200 text-slate-500">
                                        Belum Dinilai
                                    </span>
                                @endif
                            </div>

                            <div class="mt-2">
                                <div class="font-extrabold text-xs text-slate-900 leading-snug line-clamp-1">
                                    {{ $reg->school_name ?? $t->team_name }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-medium flex items-center gap-2 mt-0.5">
                                    <span>No: {{ $t->order_number ?? ($idx + 1) }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $t->team_label ?? $t->team_name }}</span>
                                </div>
                            </div>

                            @if($isActive)
                                <div class="absolute -right-1 top-1/2 -translate-y-1/2 w-2 h-6 bg-rose-600 rounded-l"></div>
                            @endif
                        </a>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-user-xmark text-2xl mb-2 text-slate-300"></i>
                            <p>Belum ada regu terdaftar pada kategori ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- AREA UTAMA: FORMULIR GRID PENILAIAN LKTR MADYA & WIRA --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-6">
                @if(!$currentTeam)
                    <div class="bg-white rounded-3xl p-10 text-center text-slate-500 border border-slate-200 shadow-sm">
                        <i class="fa-solid fa-bed text-4xl mb-3 text-slate-300"></i>
                        <h3 class="font-bold text-base text-slate-800">Tidak Ada Regu yang Dipilih</h3>
                        <p class="text-xs text-slate-400 mt-1">Silakan pilih salah satu regu peserta pada daftar di sebelah kiri untuk mulai menginput nilai.</p>
                    </div>
                @else
                    @php
                        $schoolName = $currentTeam->registration->school_name ?? $currentTeam->team_name;
                        $teamCode = 'No. Urut ' . str_pad($currentTeam->order_number ?? '1', 2, '0', STR_PAD_LEFT);
                        $rubricData = is_array($existingRubric) ? $existingRubric : [];

                        $formatTitle = 'FORMAT PENILAIAN LOMBA TANDU (MADYA & WIRA)';

                        // Initial values for MADYA & WIRA
                        $vKekuatan1 = $rubricData['kekuatan_1'] ?? null;
                        $vKekuatan2 = $rubricData['kekuatan_2'] ?? null;
                        $vKekuatan3a = $rubricData['kekuatan_3a'] ?? null;
                        $vKekuatan3b = $rubricData['kekuatan_3b'] ?? null;
                        $vKekuatan4 = $rubricData['kekuatan_4'] ?? null;

                        $vKerapihan1 = $rubricData['kerapihan_1'] ?? null;
                        $vKerapihan2 = $rubricData['kerapihan_2'] ?? null;
                        $vKerapihan3 = $rubricData['kerapihan_3'] ?? null;
                        $vKerapihan4 = $rubricData['kerapihan_4'] ?? null;
                        $vKerapihan5 = $rubricData['kerapihan_5'] ?? null;
                        $vKerapihan6 = $rubricData['kerapihan_6'] ?? null;
                        $vKerapihan7 = $rubricData['kerapihan_7'] ?? null;
                        $vKerapihan8 = $rubricData['kerapihan_8'] ?? null;
                        $vKerapihan9 = $rubricData['kerapihan_9'] ?? null;
                        $vKerapihan10 = $rubricData['kerapihan_10'] ?? null;
                        $vKerapihan11 = $rubricData['kerapihan_11'] ?? null;

                        $vWaktu = $existingTime ?: '04:25';
                    @endphp

                    <form action="{{ route('admin.competition-scores.grid-save', $category->id) }}" method="POST" id="formGridLKTR">
                        @csrf
                        <input type="hidden" name="competition_participant_team_id" value="{{ $currentTeam->id }}">
                        <input type="hidden" name="round_name" value="{{ $round }}">
                        <input type="hidden" name="action_after" id="actionAfterInput" value="stay">
                        <input type="hidden" name="next_team_id" value="{{ $nextTeam?->id }}">

                        {{-- Hidden Inputs Rubrik Kriteria Teknis Madya & Wira --}}
                        <input type="hidden" name="rubric[kekuatan_1]" id="input_kekuatan_1" value="{{ $vKekuatan1 }}">
                        <input type="hidden" name="rubric[kekuatan_2]" id="input_kekuatan_2" value="{{ $vKekuatan2 }}">
                        <input type="hidden" name="rubric[kekuatan_3a]" id="input_kekuatan_3a" value="{{ $vKekuatan3a }}">
                        <input type="hidden" name="rubric[kekuatan_3b]" id="input_kekuatan_3b" value="{{ $vKekuatan3b }}">
                        <input type="hidden" name="rubric[kekuatan_4]" id="input_kekuatan_4" value="{{ $vKekuatan4 }}">

                        <input type="hidden" name="rubric[kerapihan_1]" id="input_kerapihan_1" value="{{ $vKerapihan1 }}">
                        <input type="hidden" name="rubric[kerapihan_2]" id="input_kerapihan_2" value="{{ $vKerapihan2 }}">
                        <input type="hidden" name="rubric[kerapihan_3]" id="input_kerapihan_3" value="{{ $vKerapihan3 }}">
                        <input type="hidden" name="rubric[kerapihan_4]" id="input_kerapihan_4" value="{{ $vKerapihan4 }}">
                        <input type="hidden" name="rubric[kerapihan_5]" id="input_kerapihan_5" value="{{ $vKerapihan5 }}">
                        <input type="hidden" name="rubric[kerapihan_6]" id="input_kerapihan_6" value="{{ $vKerapihan6 }}">
                        <input type="hidden" name="rubric[kerapihan_7]" id="input_kerapihan_7" value="{{ $vKerapihan7 }}">
                        <input type="hidden" name="rubric[kerapihan_8]" id="input_kerapihan_8" value="{{ $vKerapihan8 }}">
                        <input type="hidden" name="rubric[kerapihan_9]" id="input_kerapihan_9" value="{{ $vKerapihan9 }}">
                        <input type="hidden" name="rubric[kerapihan_10]" id="input_kerapihan_10" value="{{ $vKerapihan10 }}">
                        <input type="hidden" name="rubric[kerapihan_11]" id="input_kerapihan_11" value="{{ $vKerapihan11 }}">

                        <input type="hidden" name="subtotals[kekuatan]" id="input_subtotal_kekuatan" value="0">
                        <input type="hidden" name="subtotals[kerapihan]" id="input_subtotal_kerapihan" value="0">

                        <input type="hidden" name="subtotals[teknis]" id="input_subtotal_teknis" value="0">
                        <input type="hidden" name="subtotals[waktu]" id="input_subtotal_waktu" value="0">
                        <input type="hidden" name="subtotals[total]" id="input_subtotal_total" value="0">

                        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                            
                            {{-- SISI KIRI: LEMBAR PENILAIAN LKTR --}}
                            <div class="xl:col-span-8 bg-white rounded-3xl p-6 shadow-xl border border-slate-200 relative">
                                
                                <div class="text-center border-b-2 border-slate-800 pb-3 mb-4 mt-2">
                                    <div class="flex items-center justify-center gap-1.5 text-rose-600 font-black text-xs uppercase tracking-widest mb-0.5">
                                        <i class="fa-solid fa-circle-check"></i> LEMBAR PENILAIAN RESMI DEWAN JURI
                                    </div>
                                    <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                                        {{ $formatTitle }}
                                    </h2>
                                </div>

                                {{-- Metadata Peserta --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-3.5 rounded-2xl border border-slate-200 mb-4">
                                    <div>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">NO. URUT PESERTA :</span>
                                        <span class="text-sm font-black text-slate-900 flex items-center gap-1.5 mt-0.5">
                                            <i class="fa-solid fa-hashtag text-rose-600"></i> {{ $teamCode }} &bull; {{ $currentTeam->team_label ?? $currentTeam->team_name }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">ASAL SEKOLAH :</span>
                                        <span class="text-sm font-black text-slate-900 flex items-center gap-1.5 mt-0.5 truncate">
                                            <i class="fa-solid fa-school text-sky-600"></i> {{ $schoolName }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Tabel Item Penilaian LKTR Madya & Wira --}}
                                <div class="rounded-2xl border-2 border-slate-800 mb-4 shadow-sm overflow-hidden overflow-x-auto">
                                    <table class="w-full text-center border-collapse min-w-[550px]">
                                        <thead>
                                            <tr class="bg-slate-900 text-white text-[11px] uppercase font-black tracking-wider">
                                                <th class="py-2.5 px-2 w-10 border-r border-slate-700">NO</th>
                                                <th class="py-2.5 px-3 text-left border-r border-slate-700">URAIAN KRITERIA</th>
                                                <th colspan="4" class="py-2.5 px-2 bg-slate-800 border-r border-slate-700">PILIHAN NILAI (KLIK UNTUK MEMILIH)</th>
                                                <th class="py-2.5 px-2 w-20 bg-slate-950 text-amber-300">SKOR</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-xs divide-y divide-slate-200 font-semibold text-slate-800">
                                            
                                            {{-- SECTION I: KEKUATAN --}}
                                            <tr class="bg-sky-100 text-sky-950 font-black text-xs text-left">
                                                <td class="py-2 px-2 text-center border-r border-sky-200 font-mono">I.</td>
                                                <td colspan="5" class="py-2 px-3 tracking-wider uppercase border-r border-sky-200">
                                                    KEKUATAN <span class="text-[10px] text-sky-700 font-bold ml-2">(Maksimal: 400 Poin)</span>
                                                </td>
                                                <td class="py-2 px-2 text-center font-mono text-sky-900 font-black bg-sky-200/80" id="display_subtotal_kekuatan">0</td>
                                            </tr>

                                            {{-- 1. Kekencangan mitela --}}
                                            <tr>
                                                <td class="py-1.5 px-2 text-center border-r border-slate-200 text-slate-400">1</td>
                                                <td class="py-1.5 px-3 text-left border-r border-slate-200">Kekencangan mitela</td>
                                                @foreach([20, 40, 60, 80] as $opt)
                                                    <td class="p-1 border-r border-slate-200">
                                                        <button type="button" onclick="selectScore('kekuatan_1', {{ $opt }}, this)" class="btn-rubric-kekuatan_1 w-full py-1.5 rounded-lg {{ $vKekuatan1 == $opt ? 'selected-pill' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} font-bold text-xs border border-slate-200 transition">
                                                            {{ $opt }}{{ $vKekuatan1 == $opt ? ' ✓' : '' }}
                                                        </button>
                                                    </td>
                                                @endforeach
                                                <td class="py-1.5 px-2 bg-slate-50 font-mono font-black text-xs text-slate-900" id="score_kekuatan_1">{{ $vKekuatan1 ?: '-' }}</td>
                                            </tr>

                                            {{-- 2. Kekuatan jaring --}}
                                            <tr>
                                                <td class="py-1.5 px-2 text-center border-r border-slate-200 text-slate-400">2</td>
                                                <td class="py-1.5 px-3 text-left border-r border-slate-200">Kekuatan jaring</td>
                                                @foreach([20, 40, 60, 80] as $opt)
                                                    <td class="p-1 border-r border-slate-200">
                                                        <button type="button" onclick="selectScore('kekuatan_2', {{ $opt }}, this)" class="btn-rubric-kekuatan_2 w-full py-1.5 rounded-lg {{ $vKekuatan2 == $opt ? 'selected-pill' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} font-bold text-xs border border-slate-200 transition">
                                                            {{ $opt }}{{ $vKekuatan2 == $opt ? ' ✓' : '' }}
                                                        </button>
                                                    </td>
                                                @endforeach
                                                <td class="py-1.5 px-2 bg-slate-50 font-mono font-black text-xs text-slate-900" id="score_kekuatan_2">{{ $vKekuatan2 ?: '-' }}</td>
                                            </tr>

                                            {{-- 3. Kekuatan bambu (diputar) --}}
                                            <tr>
                                                <td rowspan="2" class="py-1.5 px-2 text-center border-r border-slate-200 text-slate-400 align-middle">3</td>
                                                <td class="py-1.5 px-3 text-left border-r border-slate-200">a. Bambu pendek</td>
                                                @foreach([20, 40, 60, 80] as $opt)
                                                    <td class="p-1 border-r border-slate-200">
                                                        <button type="button" onclick="selectScore('kekuatan_3a', {{ $opt }}, this)" class="btn-rubric-kekuatan_3a w-full py-1.5 rounded-lg {{ $vKekuatan3a == $opt ? 'selected-pill' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} font-bold text-xs border border-slate-200 transition">
                                                            {{ $opt }}{{ $vKekuatan3a == $opt ? ' ✓' : '' }}
                                                        </button>
                                                    </td>
                                                @endforeach
                                                <td class="py-1.5 px-2 bg-slate-50 font-mono font-black text-xs text-slate-900" id="score_kekuatan_3a">{{ $vKekuatan3a ?: '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="py-1.5 px-3 text-left border-r border-slate-200">b. Bambu panjang</td>
                                                @foreach([20, 40, 60, 80] as $opt)
                                                    <td class="p-1 border-r border-slate-200">
                                                        <button type="button" onclick="selectScore('kekuatan_3b', {{ $opt }}, this)" class="btn-rubric-kekuatan_3b w-full py-1.5 rounded-lg {{ $vKekuatan3b == $opt ? 'selected-pill' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} font-bold text-xs border border-slate-200 transition">
                                                            {{ $opt }}{{ $vKekuatan3b == $opt ? ' ✓' : '' }}
                                                        </button>
                                                    </td>
                                                @endforeach
                                                <td class="py-1.5 px-2 bg-slate-50 font-mono font-black text-xs text-slate-900" id="score_kekuatan_3b">{{ $vKekuatan3b ?: '-' }}</td>
                                            </tr>

                                            {{-- 4. Kekuatan bambu (digoyang) --}}
                                            <tr>
                                                <td class="py-1.5 px-2 text-center border-r border-slate-200 text-slate-400">4</td>
                                                <td class="py-1.5 px-3 text-left border-r border-slate-200">Kekuatan bambu (digoyang)</td>
                                                @foreach([20, 40, 60, 80] as $opt)
                                                    <td class="p-1 border-r border-slate-200">
                                                        <button type="button" onclick="selectScore('kekuatan_4', {{ $opt }}, this)" class="btn-rubric-kekuatan_4 w-full py-1.5 rounded-lg {{ $vKekuatan4 == $opt ? 'selected-pill' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} font-bold text-xs border border-slate-200 transition">
                                                            {{ $opt }}{{ $vKekuatan4 == $opt ? ' ✓' : '' }}
                                                        </button>
                                                    </td>
                                                @endforeach
                                                <td class="py-1.5 px-2 bg-slate-50 font-mono font-black text-xs text-slate-900" id="score_kekuatan_4">{{ $vKekuatan4 ?: '-' }}</td>
                                            </tr>

                                            {{-- SECTION II: KERAPIHAN --}}
                                            <tr class="bg-rose-100 text-rose-950 font-black text-xs text-left">
                                                <td class="py-2 px-2 text-center border-r border-rose-200 font-mono">II.</td>
                                                <td colspan="5" class="py-2 px-3 tracking-wider uppercase border-r border-rose-200">
                                                    KERAPIHAN <span class="text-[10px] text-rose-700 font-bold ml-2">(Maksimal: 280 Poin)</span>
                                                </td>
                                                <td class="py-2 px-2 text-center font-mono text-rose-900 font-black bg-rose-200/80" id="display_subtotal_kerapihan">0</td>
                                            </tr>

                                            @php
                                                $kerapihanItems = [
                                                    ['num' => 1, 'key' => 'kerapihan_1', 'label' => 'Bentuk simetris', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan1],
                                                    ['num' => 2, 'key' => 'kerapihan_2', 'label' => 'Jarak ikatan jangkar sejajar', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan2],
                                                    ['num' => 3, 'key' => 'kerapihan_3', 'label' => 'Bentuk tandu tidak baling', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan3],
                                                    ['num' => 4, 'key' => 'kerapihan_4', 'label' => 'Ikatan mati 4 sudut', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan4],
                                                    ['num' => 5, 'key' => 'kerapihan_5', 'label' => 'Ikatan jangkar 7 bagian', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan5],
                                                    ['num' => 6, 'key' => 'kerapihan_6', 'label' => 'Tali tubuh rapih & presisi', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan6],
                                                    ['num' => 7, 'key' => 'kerapihan_7', 'label' => 'Sisa tali tidak terurai', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan7],
                                                    ['num' => 8, 'key' => 'kerapihan_8', 'label' => 'Sisa bambu pendek simetris', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan8],
                                                    ['num' => 9, 'key' => 'kerapihan_9', 'label' => 'Sisa bambu panjang simetris', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan9],
                                                    ['num' => 10, 'key' => 'kerapihan_10', 'label' => 'Kebersihan & kerapihan mitela', 'opts' => [10, 20, 30, 40], 'val' => $vKerapihan10],
                                                    ['num' => 11, 'key' => 'kerapihan_11', 'label' => 'Posisi lipatan mitela presisi', 'opts' => [15, 30, 45, 60], 'val' => $vKerapihan11],
                                                ];
                                            @endphp

                                            @foreach($kerapihanItems as $it)
                                                <tr>
                                                    <td class="py-1.5 px-2 text-center border-r border-slate-200 text-slate-400">{{ $it['num'] }}</td>
                                                    <td class="py-1.5 px-3 text-left border-r border-slate-200">{{ $it['label'] }}</td>
                                                    @foreach($it['opts'] as $opt)
                                                        <td class="p-1 border-r border-slate-200">
                                                            <button type="button" onclick="selectScore('{{ $it['key'] }}', {{ $opt }}, this)" class="btn-rubric-{{ $it['key'] }} w-full py-1.5 rounded-lg {{ $it['val'] == $opt ? 'selected-pill' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} font-bold text-xs border border-slate-200 transition">
                                                                {{ $opt }}{{ $it['val'] == $opt ? ' ✓' : '' }}
                                                            </button>
                                                        </td>
                                                    @endforeach
                                                    <td class="py-1.5 px-2 bg-slate-50 font-mono font-black text-xs text-slate-900" id="score_{{ $it['key'] }}">{{ $it['val'] ?: '-' }}</td>
                                                </tr>
                                            @endforeach

                                            {{-- SECTION III: KECEPATAN (WAKTU TEMPUH) --}}
                                            <tr class="bg-emerald-100 text-emerald-950 font-black text-xs text-left">
                                                <td class="py-2 px-2 text-center border-r border-emerald-200 font-mono">III.</td>
                                                <td colspan="4" class="py-2 px-3 tracking-wider uppercase border-r border-emerald-200">
                                                    KECEPATAN (WAKTU TEMPUH) <span class="text-[10px] text-emerald-700 font-bold ml-2">(Maksimal: 300 Poin)</span>
                                                </td>
                                                <td class="py-2 px-2 text-center bg-white border-r border-emerald-200">
                                                    <div class="flex items-center justify-center gap-1.5">
                                                        <span class="text-[10px] font-bold text-slate-500 uppercase">Waktu:</span>
                                                        <input type="text" name="time_recorded" id="time_recorded" value="{{ $vWaktu }}" placeholder="04:25" oninput="updateTimeScore()" class="w-20 text-center font-mono font-black text-xs bg-amber-50 text-amber-900 border border-amber-300 rounded-lg px-2 py-1 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-2 text-center font-mono text-emerald-900 font-black bg-emerald-200/80" id="display_subtotal_waktu">0</td>
                                            </tr>

                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-slate-900 text-white font-black text-sm border-t-2 border-slate-800">
                                                <td colspan="6" class="py-3 px-4 text-right tracking-wider uppercase text-slate-300 text-xs">
                                                    TOTAL NILAI AKUMULASI (TEKNIS: <span id="display_footer_teknis">0</span> + WAKTU: <span id="display_footer_waktu">0</span>) :
                                                </td>
                                                <td class="py-3 px-2 bg-gradient-to-r from-red-600 to-rose-600 text-white font-mono text-xl tracking-tight" id="display_total_score">
                                                    0
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                {{-- Catatan Dewan Juri --}}
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1 flex items-center gap-1.5">
                                        <i class="fa-solid fa-comment-dots text-slate-400"></i> Catatan Khusus Dewan Juri:
                                    </label>
                                    <textarea name="notes" rows="2" placeholder="Tuliskan catatan teknis regu jika ada (opsional)..." class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500">{{ $existingNotes }}</textarea>
                                </div>

                                {{-- Tanda Tangan & Verifikasi Digital --}}
                                <div class="grid grid-cols-2 gap-4 pt-1">
                                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-center">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dewan Juri LKTR</span>
                                        <div class="text-xs font-black text-slate-900 mt-0.5">Juri Penilai Lapangan</div>
                                        <span class="text-[9px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full inline-block mt-1">✓ Terverifikasi Digital</span>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-center">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Peserta Terdaftar</span>
                                        <div class="text-xs font-black text-slate-900 mt-0.5 truncate">{{ $schoolName }}</div>
                                        <span class="text-[9px] text-slate-500 font-medium inline-block mt-1">{{ $teamCode }} &bull; {{ $currentTeam->team_label ?? $currentTeam->team_name }}</span>
                                    </div>
                                </div>

                            </div>

                            {{-- SISI KANAN: TABEL KONVERSI WAKTU TEMPUH & MASTER SHEET INTEGRATION --}}
                            <div class="xl:col-span-4 space-y-6">

                                {{-- TABEL KONVERSI WAKTU RESMI PANITIA --}}
                                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xl flex flex-col">
                                    <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-stopwatch text-amber-500 text-base"></i>
                                            <div>
                                                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Tabel Konversi Waktu Tempuh</h3>
                                                <span class="text-[10px] text-slate-500 font-medium">Tingkat Madya & Wira (Interval 10 Detik)</span>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">
                                            Maks 300 Poin
                                        </span>
                                    </div>

                                    <div class="overflow-y-auto max-h-[380px] rounded-xl border border-slate-200 pr-1">
                                        <table class="w-full text-center text-xs">
                                            <thead class="bg-slate-900 text-white uppercase text-[10px] font-black sticky top-0">
                                                <tr>
                                                    <th class="py-2 px-3 text-left">Rentang Waktu</th>
                                                    <th class="py-2 px-3 text-right">Nilai Poin</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 font-mono text-[11px]" id="tableTimeBody">
                                                <tr id="time_row_300" data-min="0" data-max="160"><td class="py-1.5 px-3 text-left text-slate-600">02.30 - 02.40</td><td class="py-1.5 px-3 text-right font-black text-emerald-600">300</td></tr>
                                                <tr id="time_row_285" data-min="161" data-max="170"><td class="py-1.5 px-3 text-left text-slate-600">02.41 - 02.50</td><td class="py-1.5 px-3 text-right font-black text-emerald-600">285</td></tr>
                                                <tr id="time_row_270" data-min="171" data-max="180"><td class="py-1.5 px-3 text-left text-slate-600">02.51 - 03.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">270</td></tr>
                                                <tr id="time_row_255" data-min="181" data-max="190"><td class="py-1.5 px-3 text-left text-slate-600">03.01 - 03.10</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">255</td></tr>
                                                <tr id="time_row_240" data-min="191" data-max="200"><td class="py-1.5 px-3 text-left text-slate-600">03.11 - 03.20</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">240</td></tr>
                                                <tr id="time_row_225" data-min="201" data-max="210"><td class="py-1.5 px-3 text-left text-slate-600">03.21 - 03.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">225</td></tr>
                                                <tr id="time_row_210" data-min="211" data-max="220"><td class="py-1.5 px-3 text-left text-slate-600">03.31 - 03.40</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">210</td></tr>
                                                <tr id="time_row_195" data-min="221" data-max="230"><td class="py-1.5 px-3 text-left text-slate-600">03.41 - 03.50</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">195</td></tr>
                                                <tr id="time_row_180" data-min="231" data-max="240"><td class="py-1.5 px-3 text-left text-slate-600">03.51 - 04.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">180</td></tr>
                                                <tr id="time_row_165" data-min="241" data-max="250"><td class="py-1.5 px-3 text-left text-slate-600">04.01 - 04.10</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">165</td></tr>
                                                <tr id="time_row_150" data-min="251" data-max="260"><td class="py-1.5 px-3 text-left text-slate-600">04.11 - 04.20</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">150</td></tr>
                                                <tr id="time_row_135" data-min="261" data-max="270"><td class="py-1.5 px-3 text-left text-slate-600">04.21 - 04.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">135</td></tr>
                                                <tr id="time_row_120" data-min="271" data-max="280"><td class="py-1.5 px-3 text-left text-slate-600">04.31 - 04.40</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">120</td></tr>
                                                <tr id="time_row_105" data-min="281" data-max="290"><td class="py-1.5 px-3 text-left text-slate-600">04.41 - 04.50</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">105</td></tr>
                                                <tr id="time_row_90" data-min="291" data-max="300"><td class="py-1.5 px-3 text-left text-slate-600">04.51 - 05.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">90</td></tr>
                                                <tr id="time_row_75" data-min="301" data-max="310"><td class="py-1.5 px-3 text-left text-slate-600">05.01 - 05.10</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">75</td></tr>
                                                <tr id="time_row_60" data-min="311" data-max="320"><td class="py-1.5 px-3 text-left text-slate-600">05.11 - 05.20</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">60</td></tr>
                                                <tr id="time_row_45" data-min="321" data-max="330"><td class="py-1.5 px-3 text-left text-slate-600">05.21 - 05.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">45</td></tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3 bg-amber-50 rounded-xl p-2.5 border border-amber-200 text-[11px] text-amber-900 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-info text-amber-600 text-sm shrink-0"></i>
                                        <span id="time_lookup_info">Input waktu otomatis mencocokkan ke baris tabel dan menambahkan poin waktu.</span>
                                    </div>
                                </div>

                                {{-- INTEGRASI KE LEMBAR SKOR UTAMA (MASTER SHEET) --}}
                                <div class="bg-slate-950 rounded-3xl p-5 border border-slate-800 shadow-2xl flex flex-col justify-between text-white">
                                    <div>
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-3">
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-table-list text-amber-400 text-sm"></i>
                                                <div>
                                                    <h4 class="text-xs font-black text-white uppercase">LEMBAR SKOR UTAMA (MASTER SHEET)</h4>
                                                    <span class="text-[10px] text-slate-400 font-medium">LKTR &bull; Terupdate Otomatis</span>
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                Live Sync
                                            </span>
                                        </div>

                                        <div class="bg-slate-900 rounded-2xl p-3 border border-slate-700 mb-3 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-black text-white truncate max-w-[180px]">{{ $schoolName }}</span>
                                                <span class="px-2 py-0.5 rounded bg-amber-400 text-slate-950 font-black text-[10px]" id="live_rank_badge">
                                                    {{ $score && $score->rank ? 'Rank ' . $score->rank : 'Siap Nilai' }}
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2 text-center text-xs font-mono">
                                                <div class="bg-slate-950 p-2 rounded-xl border border-slate-800">
                                                    <span class="text-[9px] text-slate-400 block font-sans">Nilai Teknis</span>
                                                    <strong class="text-slate-200" id="live_card_teknis">0</strong>
                                                </div>
                                                <div class="bg-slate-950 p-2 rounded-xl border border-slate-800">
                                                    <span class="text-[9px] text-slate-400 block font-sans" id="live_card_time_label">Waktu</span>
                                                    <strong class="text-amber-400" id="live_card_waktu">+0</strong>
                                                </div>
                                                <div class="bg-slate-950 p-2 rounded-xl border border-emerald-500/40">
                                                    <span class="text-[9px] text-emerald-400 block font-sans">Total Akhir</span>
                                                    <strong class="text-emerald-400 text-sm" id="live_card_total">0</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-2 pt-1">
                                            <button type="submit" onclick="setActionAfter('stay')" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-extrabold text-xs shadow-lg transition flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-floppy-disk"></i> Simpan Nilai Grid (Tetap di Sini)
                                            </button>

                                            @if($nextTeam)
                                                <button type="submit" onclick="setActionAfter('next')" class="w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition flex items-center justify-center gap-1.5">
                                                    <i class="fa-solid fa-forward-step text-sky-400"></i> Simpan & Lanjut ke Regu Berikutnya
                                                </button>
                                            @endif

                                            <button type="submit" onclick="setActionAfter('back')" class="w-full py-2 rounded-xl bg-transparent hover:bg-slate-900 text-slate-400 hover:text-white font-semibold text-xs transition flex items-center justify-center gap-1.5">
                                                <i class="fa-solid fa-arrow-left"></i> Simpan & Kembali ke Lembar Utama
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </form>
                @endif
            </div>

        </div>
    @endif

</div>

<script>
    const isLevelMula = {{ $isLevelMula ? 'true' : 'false' }};

    function setActionAfter(action) {
        document.getElementById('actionAfterInput').value = action;
    }

    // Switch between Opsi A (Modern Card) and Opsi B (Clean Paper Tabular) for Mula
    function switchMulaViewMode(mode) {
        const viewA = document.getElementById('mulaViewModeA');
        const viewB = document.getElementById('mulaViewModeB');
        const btnA = document.getElementById('btnViewModeA');
        const btnB = document.getElementById('btnViewModeB');

        if (!viewA || !viewB) return;

        if (mode === 'A') {
            viewA.classList.remove('hidden');
            viewB.classList.add('hidden');
            if (btnA) {
                btnA.classList.add('bg-rose-600', 'text-white', 'shadow-sm', 'font-extrabold');
                btnA.classList.remove('text-slate-600', 'hover:text-slate-900');
            }
            if (btnB) {
                btnB.classList.remove('bg-rose-600', 'text-white', 'shadow-sm', 'font-extrabold');
                btnB.classList.add('text-slate-600', 'hover:text-slate-900');
            }
        } else {
            viewA.classList.add('hidden');
            viewB.classList.remove('hidden');
            if (btnB) {
                btnB.classList.add('bg-rose-600', 'text-white', 'shadow-sm', 'font-extrabold');
                btnB.classList.remove('text-slate-600', 'hover:text-slate-900');
            }
            if (btnA) {
                btnA.classList.remove('bg-rose-600', 'text-white', 'shadow-sm', 'font-extrabold');
                btnA.classList.add('text-slate-600', 'hover:text-slate-900');
            }
        }

        try {
            localStorage.setItem('lktr_mula_view_mode', mode);
        } catch (e) {}
    }

    // Function for Madya & Wira range button selection
    function selectScore(key, val, btnElement) {
        const hiddenInput = document.getElementById('input_' + key);
        if (hiddenInput) {
            hiddenInput.value = val;
        }

        document.querySelectorAll('.btn-rubric-' + key).forEach(btn => {
            btn.classList.remove('selected-pill');
            btn.classList.add('bg-slate-100', 'text-slate-700');
            btn.innerHTML = btn.innerHTML.replace(' ✓', '').trim();
        });

        btnElement.classList.add('selected-pill');
        btnElement.classList.remove('bg-slate-100', 'text-slate-700');
        btnElement.innerHTML = val + ' ✓';

        const scoreCell = document.getElementById('score_' + key);
        if (scoreCell) {
            scoreCell.innerText = val;
        }

        recalculateAll();
    }

    // ==================== MULA LOGIC & SYNCHRONIZATION ====================

    // Synchronize Time input between View A and View B
    function handleMulaTimeSync(val) {
        const hidden = document.getElementById('input_hidden_time_recorded');
        if (hidden) hidden.value = val;

        const timeA = document.getElementById('time_recorded_a');
        if (timeA && timeA.value !== val) timeA.value = val;

        const timeB = document.getElementById('time_recorded_b');
        if (timeB && timeB.value !== val) timeB.value = val;

        updateTimeScore();
    }

    // Synchronize Notes textarea between View A and View B
    function handleMulaNotesSync(val) {
        const hidden = document.getElementById('input_hidden_notes');
        if (hidden) hidden.value = val;

        const notesA = document.getElementById('notes_a');
        if (notesA && notesA.value !== val) notesA.value = val;

        const notesB = document.getElementById('notes_b');
        if (notesB && notesB.value !== val) notesB.value = val;
    }

    // Input handler for View Mode A (single number input 0..max)
    function handleMulaInputA(key, maxVal, rawVal) {
        const valStr = (rawVal || '').toString().trim();
        let val = valStr === '' ? '' : parseInt(valStr, 10);

        if (isNaN(val)) val = '';
        if (val !== '' && val > maxVal) val = maxVal;
        if (val !== '' && val < 0) val = 0;

        // Keep input field updated if clamped
        const inputA = document.getElementById('input_a_' + key);
        if (inputA && val !== '' && inputA.value != val) {
            inputA.value = val;
        }

        // Update hidden master input
        const hidden = document.getElementById('input_' + key);
        if (hidden) hidden.value = val;

        // Highlight matching quick-pick pill in View A
        highlightMulaPill(key, val, maxVal);

        // Sync to View Mode B (3 columns)
        syncMulaToViewB(key, val, maxVal);

        recalculateAll();
    }

    // Click quick-pick pill in View Mode A
    function setMulaRubricValue(key, targetVal, maxVal) {
        const inputA = document.getElementById('input_a_' + key);
        if (inputA) inputA.value = targetVal;
        handleMulaInputA(key, maxVal, targetVal.toString());
    }

    // Input handler for View Mode B (3 range columns)
    function handleMulaInputB(key, colIdx, maxVal, rawVal) {
        const valStr = (rawVal || '').toString().trim();
        let val = valStr === '' ? '' : parseInt(valStr, 10);
        if (isNaN(val)) val = '';

        if (val !== '') {
            // Clear the other two columns in View B for this row
            [0, 1, 2].forEach(i => {
                if (i !== colIdx) {
                    const other = document.getElementById(`input_b_col_${key}_${i}`);
                    if (other) other.value = '';
                }
            });
        }

        // Update hidden master input
        const hidden = document.getElementById('input_' + key);
        if (hidden) hidden.value = val;

        // Sync to View A single input
        const inputA = document.getElementById('input_a_' + key);
        if (inputA) inputA.value = val;

        // Highlight matching quick-pick pill in View A
        highlightMulaPill(key, val, maxVal);

        recalculateAll();
    }

    function highlightMulaPill(key, val, maxVal) {
        [0, 1, 2].forEach(i => {
            const pill = document.getElementById(`pill_mula_${key}_${i}`);
            if (pill) {
                pill.classList.remove('selected-pill');
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
            const activePill = document.getElementById(`pill_mula_${key}_${colIdx}`);
            if (activePill) {
                activePill.classList.add('selected-pill');
                activePill.classList.remove('bg-slate-100', 'text-slate-700');
            }
        }
    }

    function syncMulaToViewB(key, val, maxVal) {
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
            if (seconds > 600) return 0;
            const score = 300 - Math.round(seconds * 0.5);
            return Math.max(0, Math.min(300, score));
        } else {
            if (seconds <= 160) return 300;
            if (seconds > 330) return 0;
            const step = Math.ceil((seconds - 160) / 10);
            const score = 300 - (step * 15);
            return Math.max(score, 45);
        }
    }

    function getMulaTierInfo(seconds) {
        if (seconds <= 0) return { tier: 0, text: '-', label: 'Belum Diisi' };
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
    }

    function updateTimeScore() {
        let timeStr = '';
        if (isLevelMula) {
            const h = document.getElementById('input_hidden_time_recorded');
            timeStr = h ? h.value.trim() : '05:00';
        } else {
            const timeInput = document.getElementById('time_recorded');
            timeStr = timeInput ? timeInput.value.trim() : '';
        }

        const seconds = parseTimeToSeconds(timeStr);
        const timeScore = computeTimeScore(seconds);

        if (isLevelMula) {
            const tierInfo = getMulaTierInfo(seconds);

            // Update View A badges
            const ptsA = document.getElementById('mula_a_time_pts');
            if (ptsA) ptsA.innerText = `${timeScore} Poin`;
            const tierA = document.getElementById('mula_a_time_tier');
            if (tierA) tierA.innerText = seconds > 0 ? tierInfo.label : '-';

            // Highlight View A 10 cards
            for (let i = 1; i <= 10; i++) {
                const card = document.getElementById('mula_card_interval_' + i);
                if (card) {
                    if (tierInfo.tier === i) {
                        card.classList.add('active-interval-card');
                    } else {
                        card.classList.remove('active-interval-card');
                    }
                }
            }

            // Update View B badges & table cells
            const ptsB = document.getElementById('mula_b_waktu_badge_pts');
            if (ptsB) ptsB.innerText = `${timeScore} Poin`;
            const tierB = document.getElementById('mula_b_waktu_badge_tier');
            if (tierB) tierB.innerText = seconds > 0 ? `Interval: ${tierInfo.label}` : 'Interval: -';

            for (let i = 1; i <= 10; i++) {
                const cell = document.getElementById('mula_b_tier_cell_' + i);
                const lbl = document.getElementById('mula_b_interval_lbl_' + i);
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

        } else {
            // Madya & Wira Table highlight
            document.querySelectorAll('#tableTimeBody tr').forEach(row => {
                row.classList.remove('highlight-time-row');
                const min = parseInt(row.getAttribute('data-min'), 10) || 0;
                const max = parseInt(row.getAttribute('data-max'), 10) || 0;

                if (seconds >= min && seconds <= max && seconds > 0) {
                    row.classList.add('highlight-time-row');
                    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            const info = document.getElementById('time_lookup_info');
            if (info) {
                if (seconds > 0) {
                    info.innerHTML = `Input waktu <strong>${timeStr}</strong> (${seconds} detik) cocok dengan skor waktu: <strong>${timeScore} Poin</strong>.`;
                } else {
                    info.innerHTML = 'Input waktu otomatis mencocokkan ke baris tabel dan menambahkan poin waktu.';
                }
            }
        }

        recalculateAll();
    }

    function recalculateAll() {
        let timeStr = '';
        if (isLevelMula) {
            const h = document.getElementById('input_hidden_time_recorded');
            timeStr = h ? h.value.trim() : '05:00';
        } else {
            const timeInput = document.getElementById('time_recorded');
            timeStr = timeInput ? timeInput.value.trim() : '';
        }
        const seconds = parseTimeToSeconds(timeStr);
        const timeScore = computeTimeScore(seconds);

        if (isLevelMula) {
            // Recalculate MULA
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

            // Update View A Subtotals
            const subKetA = document.getElementById('display_mula_a_subtotal_ketepatan');
            if (subKetA) subKetA.innerText = subtotalKetepatan;
            const subKekA = document.getElementById('display_mula_a_subtotal_kekuatan');
            if (subKekA) subKekA.innerText = subtotalKekuatan;
            const subWakA = document.getElementById('display_mula_a_subtotal_waktu');
            if (subWakA) subWakA.innerText = timeScore;

            // Update View B Subtotals & Footer
            const subKetB = document.getElementById('display_mula_b_subtotal_ketepatan');
            if (subKetB) subKetB.innerText = subtotalKetepatan;
            const subKekB = document.getElementById('display_mula_b_subtotal_kekuatan');
            if (subKekB) subKekB.innerText = subtotalKekuatan;
            const subWakB = document.getElementById('display_mula_b_subtotal_waktu');
            if (subWakB) subWakB.innerText = timeScore;

            const footTekB = document.getElementById('display_mula_b_footer_teknis');
            if (footTekB) footTekB.innerText = technicalScore;
            const footWakB = document.getElementById('display_mula_b_footer_waktu');
            if (footWakB) footWakB.innerText = timeScore;
            const footTotB = document.getElementById('display_mula_b_total_score');
            if (footTotB) footTotB.innerText = totalScore;

            // Update Top Header Total Badge
            const headerTot = document.getElementById('mula_header_total_score');
            if (headerTot) headerTot.innerText = totalScore;

            // Update Sticky Summary Bar
            const stickyKet = document.getElementById('sticky_ketepatan');
            if (stickyKet) stickyKet.innerText = subtotalKetepatan;
            const stickyKek = document.getElementById('sticky_kekuatan');
            if (stickyKek) stickyKek.innerText = subtotalKekuatan;
            const stickyWak = document.getElementById('sticky_waktu');
            if (stickyWak) stickyWak.innerText = `+${timeScore}`;
            const stickyTot = document.getElementById('sticky_total');
            if (stickyTot) stickyTot.innerText = totalScore;

            // Update Hidden Inputs
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

        } else {
            // Recalculate MADYA & WIRA
            if (!document.getElementById('display_subtotal_kekuatan')) return;

            const kekuatanKeys = ['kekuatan_1', 'kekuatan_2', 'kekuatan_3a', 'kekuatan_3b', 'kekuatan_4'];
            let subtotalKekuatan = 0;
            kekuatanKeys.forEach(k => {
                const el = document.getElementById('input_' + k);
                if (el && el.value) {
                    subtotalKekuatan += parseInt(el.value, 10) || 0;
                }
            });

            const kerapihanKeys = [
                'kerapihan_1', 'kerapihan_2', 'kerapihan_3', 'kerapihan_4',
                'kerapihan_5', 'kerapihan_6', 'kerapihan_7', 'kerapihan_8',
                'kerapihan_9', 'kerapihan_10', 'kerapihan_11'
            ];
            let subtotalKerapihan = 0;
            kerapihanKeys.forEach(k => {
                const el = document.getElementById('input_' + k);
                if (el && el.value) {
                    subtotalKerapihan += parseInt(el.value, 10) || 0;
                }
            });

            const technicalScore = subtotalKekuatan + subtotalKerapihan;
            const totalScore = technicalScore + timeScore;

            document.getElementById('display_subtotal_kekuatan').innerText = subtotalKekuatan;
            document.getElementById('display_subtotal_kerapihan').innerText = subtotalKerapihan;
            document.getElementById('display_subtotal_waktu').innerText = timeScore;
            document.getElementById('display_footer_teknis').innerText = technicalScore;
            document.getElementById('display_footer_waktu').innerText = timeScore;
            document.getElementById('display_total_score').innerText = totalScore;

            const liveTek = document.getElementById('live_card_teknis');
            if (liveTek) liveTek.innerText = technicalScore;
            const liveTimeLbl = document.getElementById('live_card_time_label');
            if (liveTimeLbl) liveTimeLbl.innerText = `Waktu (${timeStr || '-'})`;
            const liveWak = document.getElementById('live_card_waktu');
            if (liveWak) liveWak.innerText = `+${timeScore}`;
            const liveTot = document.getElementById('live_card_total');
            if (liveTot) liveTot.innerText = totalScore;

            document.getElementById('input_subtotal_kekuatan').value = subtotalKekuatan;
            document.getElementById('input_subtotal_kerapihan').value = subtotalKerapihan;
            document.getElementById('input_subtotal_teknis').value = technicalScore;
            document.getElementById('input_subtotal_waktu').value = timeScore;
            document.getElementById('input_subtotal_total').value = totalScore;
        }
    }

    // Run on initial load
    document.addEventListener('DOMContentLoaded', function() {
        if (isLevelMula) {
            // Restore saved view mode preference (A or B)
            try {
                const savedMode = localStorage.getItem('lktr_mula_view_mode') || 'A';
                switchMulaViewMode(savedMode);
            } catch (e) {
                switchMulaViewMode('A');
            }

            // Highlight initial pills based on saved values
            for (let i = 1; i <= 7; i++) {
                const val = document.getElementById('input_ketepatan_' + i)?.value;
                if (val) highlightMulaPill('ketepatan_' + i, parseInt(val, 10), 43);
            }
            for (let i = 1; i <= 3; i++) {
                const val = document.getElementById('input_kekuatan_' + i)?.value;
                if (val) highlightMulaPill('kekuatan_' + i, parseInt(val, 10), 133);
            }
        }

        updateTimeScore();
        recalculateAll();
    });
</script>
@endsection
