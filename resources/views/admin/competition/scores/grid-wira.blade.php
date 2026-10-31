@extends('layouts.admin')

@section('title', 'Grid Nilai Pertolongan Pertama Tingkat Wira - ' . $category->display_name)
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
    .btn-score.active-k {
        background-color: #f1f5f9 !important;
        color: #334155 !important;
        border-color: #94a3b8 !important;
        font-weight: 900 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    }
    .btn-score.active-c {
        background-color: #fef3c7 !important;
        color: #92400e !important;
        border-color: #f59e0b !important;
        font-weight: 900 !important;
        box-shadow: 0 2px 6px rgba(245, 158, 11, 0.25);
    }
    .btn-score.active-b {
        background-color: #dcfce7 !important;
        color: #166534 !important;
        border-color: #22c55e !important;
        font-weight: 900 !important;
        box-shadow: 0 2px 6px rgba(34, 197, 94, 0.25);
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

        <!-- Top Header & Metadata Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                
                <!-- Left: Team Selector & Navigation -->
                <div class="lg:col-span-7 space-y-4 border-b lg:border-b-0 lg:border-r border-slate-200 lg:pr-6 pb-6 lg:pb-0">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-3.5 py-1.5 rounded-xl {{ $category->gender_category === 'Putri' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }} font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                                @if($category->gender_category === 'Putri')
                                    <i class="fa-solid fa-venus"></i> {{ $category->code ?? 'LPP-WIRA-PI' }} (Putri)
                                @else
                                    <i class="fa-solid fa-mars"></i> {{ $category->code ?? 'LPP-WIRA-PA' }} (Putra)
                                @endif
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
                    @php
                        $isPi = ($category->gender_category === 'Putri') || str_contains(strtoupper($category->code ?? ''), '-PI');
                        $genderText = $isPi ? 'Putri' : 'Putra';
                        $genderIcon = $isPi ? 'fa-venus' : 'fa-mars';
                        $genderBadgeClass = $isPi ? 'text-rose-700 bg-rose-50 border-rose-200' : 'text-blue-700 bg-blue-50 border-blue-200';
                        $borderColor = $isPi ? 'border-rose-500' : 'border-amber-500';
                    @endphp
                    <div>
                        <label for="select-team-dropdown" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Pilih Regu Peserta yang Sedang Dinilai:</span>
                            <span class="{{ $genderBadgeClass }} font-extrabold text-[11px] px-2.5 py-0.5 rounded-md border flex items-center gap-1">
                                <i class="fa-solid {{ $genderIcon }}"></i> Khusus PMR Wira {{ $genderText }} &bull; Total: {{ $teams->count() }} Regu
                            </span>
                        </label>
                        <div class="relative">
                            <select id="select-team-dropdown" onchange="window.location.href=this.value" class="w-full bg-slate-50 hover:bg-slate-100 border-2 {{ $borderColor }} rounded-2xl py-3 px-4 text-sm font-black text-slate-900 focus:outline-none appearance-none cursor-pointer shadow-xs transition">
                                @forelse($teams as $idx => $t)
                                    @php
                                        $tScore = $scores->get($t->id);
                                        $isScored = $tScore && ($tScore->final_score !== null && $tScore->final_score > 0);
                                        $statusLabel = $isScored ? "✓ Nilai: {$tScore->final_score} (" . ($tScore->time_recorded ?: '-') . ")" : "Belum dinilai";
                                        $schoolName = $t->registration?->school_name ?: $t->team_name;
                                        $regCode = $t->registration?->registration_code ?: 'REG';
                                        $teamNo = $t->order_number ?: ($idx + 1);
                                    @endphp
                                    <option value="{{ route('admin.competition-scores.grid', ['category' => $category->id, 'team_id' => $t->id, 'round' => $round]) }}" {{ $currentTeam?->id == $t->id ? 'selected' : '' }}>
                                        [No. {{ $teamNo }}] {{ $schoolName }} • {{ $t->team_name }} [Wira {{ $genderText }}] ({{ $regCode }}) — {{ $statusLabel }}
                                    </option>
                                @empty
                                    <option value="" disabled selected>-- Belum ada regu PMR Wira {{ $genderText }} (SMA/SMK/MA) yang terdaftar di cabang ini --</option>
                                @endforelse
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                        </div>

                        @if($teams->isEmpty())
                            <div class="bg-amber-50 border border-amber-300 text-amber-900 p-3 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs font-bold shadow-2xs mt-2">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation text-amber-600 text-base"></i>
                                    <span>Belum ada regu peserta <strong>PMR Tingkat Wira {{ $genderText }} (SMA/SMK/MA)</strong> yang terdaftar di cabang ini.</span>
                                </div>
                                <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-black text-xs transition shrink-0">
                                    <i class="fa-solid fa-plus"></i> Tambah Regu {{ $genderText }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Official Technical Guidelines Info Box -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Mekanisme kejadian:</span>
                            <strong class="text-slate-800 font-extrabold">Di sekolah & sekitar sekolah</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Kasus:</span>
                            <strong class="text-amber-800 font-black"><i class="fa-solid fa-notes-medical mr-1 text-red-600"></i> Cedera jaringan lunak & pingsan</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Panduan:</span>
                            <strong class="text-slate-800 font-extrabold">Buku pedoman PP 2008 & 2009</strong>
                        </div>
                    </div>

                </div>

                <!-- Right: Stopwatch & Live Score Preview -->
                <div class="lg:col-span-5 space-y-4">
                    
                    <!-- Stopwatch Box -->
                    <div class="bg-slate-900 text-white p-5 rounded-2xl shadow-md border border-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-xs font-black tracking-wider uppercase text-slate-300">STOPWATCH WAKTU PRAKTIK</span>
                            </div>
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded font-mono font-bold">Maks: 06:30</span>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <!-- Digital Time Display -->
                            <div id="stopwatch-display" class="font-mono font-black text-4xl tracking-tight text-white select-none">
                                {{ $existingTime ?: '05:00' }}
                            </div>

                            <!-- Stopwatch Controls -->
                            <div class="flex items-center gap-1.5">
                                <button type="button" id="btn-timer-start" onclick="startTimer()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs px-3.5 py-2 rounded-xl transition flex items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-play text-[10px]"></i> Mulai
                                </button>
                                <button type="button" id="btn-timer-pause" onclick="pauseTimer()" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs px-3.5 py-2 rounded-xl transition flex items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-pause text-[10px]"></i> Jeda
                                </button>
                                <button type="button" id="btn-timer-reset" onclick="resetTimer()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs p-2 rounded-xl transition" title="Reset Waktu">
                                    <i class="fa-solid fa-rotate-right text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Manual Time Input Fallback -->
                        <div class="mt-2.5 pt-2 border-t border-slate-700/80 text-[11px] text-slate-400 flex items-center justify-between gap-2">
                            <span>Input/Koreksi Waktu:</span>
                            <input type="text" id="time-recorded-input" name="time_recorded" value="{{ $existingTime ?: '05:00' }}" onchange="syncTimerInput(this.value)" placeholder="MM:SS" class="w-20 bg-slate-950 text-center font-mono font-black text-sm border border-slate-600 rounded-lg px-2 py-0.5 text-amber-300 focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <!-- Live Total Score Counter -->
                    <div class="bg-amber-50 border-2 border-amber-300 p-4.5 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div>
                            <div class="text-xs font-black text-amber-950 uppercase tracking-wider">TOTAL NILAI PRAKTIK (GRID)</div>
                            <div class="text-[11px] text-amber-800 font-medium">Akumulasi Rubrik Lapangan Wira Sesuai Kriteria</div>
                        </div>
                        <div class="text-right">
                            <div class="text-4xl font-black text-amber-700 font-mono tracking-tight">
                                <span id="grand-total-display">0</span> <span class="text-sm font-bold text-amber-600 font-sans">/ 700</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- MAIN RUBRIC TABLE (GRID NILAI PERTOLONGAN PERTAMA TINGKAT WIRA) -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mt-6">
            
            <!-- Table Action Header -->
            <div class="p-5 border-b border-slate-200 bg-slate-50/80 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block animate-ping"></span>
                    <span class="font-black text-sm text-slate-900">Lembar Penilaian Lapangan Resmi: Klik Nilai (K / C / B) Pada Setiap Uraian</span>
                </div>
                <!-- Quick Fill Helper Buttons -->
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setAllValues('B')" class="text-xs font-bold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 px-3.5 py-1.5 rounded-xl border border-emerald-300 transition shadow-2xs">
                        <i class="fa-solid fa-circle-check mr-1"></i> Set Semua "Baik (B)"
                    </button>
                    <button type="button" onclick="resetAllRubric()" class="text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 px-3.5 py-1.5 rounded-xl border border-slate-300 transition shadow-2xs">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Kosongkan Rubrik
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                            <th class="py-3.5 px-4 w-12 text-center border-r border-slate-800">NO</th>
                            <th class="py-3.5 px-5 border-r border-slate-800">URAIAN KEGIATAN PERTOLONGAN PERTAMA</th>
                            <th class="py-3.5 px-4 text-center border-r border-slate-800 w-52">
                                PILIHAN NILAI JURI<br>
                                <span class="text-[9px] font-normal text-slate-300 tracking-normal">(K = Kurang, C = Cukup, B = Baik)</span>
                            </th>
                            <th class="py-3.5 px-4 text-center w-28 bg-black">SKOR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium">

                        <!-- ================= SECTION 1: PENILAIAN KEADAAN ================= -->
                        <tr class="bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">1</td>
                            <td colspan="2" class="py-2.5 px-5">PENILAIAN KEADAAN</td>
                            <td class="py-2.5 px-4 text-center bg-amber-500 font-mono font-black text-xs text-slate-950">
                                <span id="subtotal-display-1">0</span> / 50
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[1]" id="subtotal-input-1" value="0">

                        <!-- Sub 1.1 Aman Penolong -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Aman Penolong</td>
                        </tr>
                        @php
                            $itemsSec1A = [
                                '1_a' => ['label' => 'Sudah menggunakan Apd (masker & sarung tangan)', 'options' => [0, 5, 10]],
                            ];
                        @endphp
                        @foreach($itemsSec1A as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => 'a', 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 1])
                        @endforeach

                        <!-- Sub 1.2 Aman Lingkungan -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Aman Lingkungan</td>
                        </tr>
                        @php
                            $itemsSec1B = [
                                '1_b' => ['label' => 'Memperkenalkan diri dan tim', 'options' => [0, 5, 10], 'letter' => 'b'],
                                '1_c' => ['label' => 'Memastikan mekanisme kejadian', 'options' => [0, 5, 10], 'letter' => 'c'],
                                '1_d' => ['label' => 'Meminta izin dan saksi', 'options' => [0, 5, 10], 'letter' => 'd'],
                            ];
                        @endphp
                        @foreach($itemsSec1B as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 1])
                        @endforeach

                        <!-- Sub 1.3 Aman Pasien -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Aman Pasien</td>
                        </tr>
                        @php
                            $itemsSec1C = [
                                '1_e' => ['label' => 'Memastikan lokasi kejadian (aman & tidak aman)', 'options' => [0, 5, 10]],
                            ];
                        @endphp
                        @foreach($itemsSec1C as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => 'e', 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 1])
                        @endforeach


                        <!-- ================= SECTION 2: PENILAIAN DINI ================= -->
                        <tr class="bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">2</td>
                            <td colspan="2" class="py-2.5 px-5">PENILAIAN DINI</td>
                            <td class="py-2.5 px-4 text-center bg-amber-500 font-mono font-black text-xs text-slate-950">
                                <span id="subtotal-display-2">0</span> / 140
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[2]" id="subtotal-input-2" value="0">

                        @php
                            $itemsSec2 = [
                                '2_a' => ['label' => 'Menyebutkan kesan umum (trauma/medis)', 'options' => [0, 10, 20], 'letter' => 'a'],
                                '2_b' => ['label' => 'Cek respon (ASNT)', 'options' => [0, 10, 20], 'letter' => 'b'],
                                '2_c' => ['label' => 'Buka jalan nafas (ADTD)', 'options' => [0, 10, 20], 'letter' => 'c'],
                                '2_d' => ['label' => 'Cek nafas (LDR 3-5 detik)', 'options' => [0, 10, 20], 'letter' => 'd'],
                                '2_e' => ['label' => 'Cek nadi (Radialis 5-10 detik)', 'options' => [0, 10, 20], 'letter' => 'e'],
                                '2_f' => ['label' => 'Meminta bantuan Guru', 'options' => [0, 10, 20], 'letter' => 'f'],
                                '2_g' => ['label' => 'Kontrol pendarahan besar (ada/tidak)', 'options' => [0, 10, 20], 'letter' => 'g'],
                            ];
                        @endphp
                        @foreach($itemsSec2 as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 2])
                        @endforeach


                        <!-- ================= SECTION 3: PENANGANAN KASUS ================= -->
                        <tr class="bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">3</td>
                            <td colspan="2" class="py-2.5 px-5">PENANGANAN KASUS</td>
                            <td class="py-2.5 px-4 text-center bg-amber-500 font-mono font-black text-xs text-slate-950">
                                <span id="subtotal-display-3">0</span> / 330
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[3]" id="subtotal-input-3" value="0">

                        <!-- Sub 3.1 Kasus Trauma -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Kasus Trauma (Maks: 160)</td>
                        </tr>
                        @php
                            $itemsSec3Trauma = [
                                '3_a' => ['label' => 'Tepat menentukan posisi luka (kanan, kiri, atas, bawah)', 'options' => [0, 20, 40], 'letter' => 'a'],
                                '3_b' => ['label' => 'Tepat menggunakan peralatan (penutup luka & pembalut luka)', 'options' => [0, 20, 40], 'letter' => 'b'],
                                '3_c' => ['label' => 'Menyebutkan ciri-ciri luka & menentukan jenis luka', 'options' => [0, 20, 40], 'letter' => 'c'],
                                '3_d' => ['label' => 'Penatalaksanaan kasus trauma', 'options' => [0, 20, 40], 'letter' => 'd'],
                            ];
                        @endphp
                        @foreach($itemsSec3Trauma as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 3])
                        @endforeach

                        <!-- Sub 3.2 Penanganan Kasus Medis -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Penanganan Kasus Medis (Maks: 120)</td>
                        </tr>
                        @php
                            $itemsSec3Medis = [
                                '3_e' => ['label' => 'Menyebutkan tanda & gejala kasus medis', 'options' => [0, 20, 40], 'letter' => 'e'],
                                '3_f' => ['label' => 'Tepat menentukan jenis kasus medis', 'options' => [0, 20, 40], 'letter' => 'f'],
                                '3_g' => ['label' => 'Penatalaksanaan kasus medis', 'options' => [0, 20, 40], 'letter' => 'g'],
                            ];
                        @endphp
                        @foreach($itemsSec3Medis as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 3])
                        @endforeach

                        <!-- Sub 3.3 Pemeriksaan Fisik Keseluruhan -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Pemeriksaan Fisik Keseluruhan (Maks: 50)</td>
                        </tr>
                        @php
                            $itemsSec3Fisik = [
                                '3_h' => ['label' => 'Kepala (tengkorak, telinga, mata, hidung, mulut)', 'options' => [0, 5, 10], 'letter' => 'h'],
                                '3_i' => ['label' => 'Leher (tulang leher/cervical)', 'options' => [0, 5, 10], 'letter' => 'i'],
                                '3_j' => ['label' => 'Batang Tubuh (bahu, dada, perut, panggul, tulang belakang)', 'options' => [0, 5, 10], 'letter' => 'j'],
                                '3_k' => ['label' => 'Alat gerak atas (lengan atas, sikut, lengan bawah, pergelangan tangan)', 'options' => [0, 5, 10], 'letter' => 'k'],
                                '3_l' => ['label' => 'Alat gerak bawah (tungkai atas, lutut, tungkai bawah, pergelangan kaki)', 'options' => [0, 5, 10], 'letter' => 'l'],
                            ];
                        @endphp
                        @foreach($itemsSec3Fisik as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 3])
                        @endforeach


                        <!-- ================= SECTION 4: RIWAYAT PENDERITA ================= -->
                        <tr class="bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">4</td>
                            <td colspan="2" class="py-2.5 px-5">RIWAYAT PENDERITA</td>
                            <td class="py-2.5 px-4 text-center bg-amber-500 font-mono font-black text-xs text-slate-950">
                                <span id="subtotal-display-4">0</span> / 140
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[4]" id="subtotal-input-4" value="0">

                        <!-- Sub 4.1 Pemeriksaan Tanda Vital -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Pemeriksaan Tanda Vital</td>
                        </tr>
                        @php
                            $itemsSec4 = [
                                '4_a' => ['label' => 'Nadi 1 menit (diukur dan diperiksa dengan tepat)', 'options' => [0, 10, 20], 'letter' => 'a'],
                                '4_b' => ['label' => 'Nafas 1 menit (diukur dan diperiksa dengan tepat)', 'options' => [0, 10, 20], 'letter' => 'b'],
                                '4_c' => ['label' => 'Suhu (menggunakan alat atau tidak menggunakan alat)', 'options' => [0, 10, 20], 'letter' => 'c'],
                                '4_d' => ['label' => 'Menganalisa kondisi dan warna kulit', 'options' => [0, 10, 20], 'letter' => 'd'],
                                '4_e' => ['label' => 'Wawancara KOMPAK (pasien sadar)', 'options' => [0, 10, 20], 'letter' => 'e'],
                                '4_f' => ['label' => 'Dukungan emosional', 'options' => [0, 10, 20], 'letter' => 'f'],
                                '4_g' => ['label' => 'Komunikasi dengan pasien', 'options' => [0, 10, 20], 'letter' => 'g'],
                            ];
                        @endphp
                        @foreach($itemsSec4 as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 4])
                        @endforeach


                        <!-- ================= SECTION 5: PELAPORAN ================= -->
                        <tr class="bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">5</td>
                            <td colspan="2" class="py-2.5 px-5">PELAPORAN</td>
                            <td class="py-2.5 px-4 text-center bg-amber-500 font-mono font-black text-xs text-slate-950">
                                <span id="subtotal-display-5">0</span> / 20
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[5]" id="subtotal-input-5" value="0">
                        @php
                            $itemsSec5 = [
                                '5_a' => ['label' => 'Pengisian kartu luka (terisi lengkap)', 'options' => [0, 10, 20]],
                            ];
                        @endphp
                        @foreach($itemsSec5 as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => 'a', 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 5])
                        @endforeach


                        <!-- ================= SECTION 6: KEKOMPAKAN TIM ================= -->
                        <tr class="bg-slate-800 text-white font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">6</td>
                            <td colspan="2" class="py-2.5 px-5">KEKOMPAKAN TIM</td>
                            <td class="py-2.5 px-4 text-center bg-slate-900 font-mono font-black text-xs text-white">
                                <span id="subtotal-display-6">0</span> / 20
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[6]" id="subtotal-input-6" value="0">
                        @php
                            $itemsSec6 = [
                                '6_a' => ['label' => 'Kekompakan tim', 'options' => [0, 10, 20]],
                            ];
                        @endphp
                        @foreach($itemsSec6 as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => 'a', 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 6])
                        @endforeach


                        <!-- ================= GRAND TOTAL ROW ================= -->
                        <tr class="bg-slate-900 text-white font-black text-base border-t-2 border-slate-800">
                            <td colspan="3" class="py-4 px-6 text-center uppercase tracking-wider font-extrabold text-sm sm:text-base">
                                JUMLAH TOTAL NILAI PRAKTIK LAPANGAN
                            </td>
                            <td class="py-4 px-4 text-center font-mono text-2xl text-amber-400 bg-black">
                                <span id="grand-total-row">0</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Bottom Sheet Metadata & Signatures (Matching Official Physical Paper) -->
            <div class="p-6 bg-slate-50 border-t border-slate-200 space-y-4 text-xs font-bold text-slate-800">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 border-b border-slate-200 pb-4">
                    <div class="md:col-span-8 space-y-2">
                        <div>
                            <span class="text-slate-500 uppercase text-[10px]">Mekanisme kejadian :</span>
                            <span class="text-slate-900 font-extrabold">Di sekolah dan sekitar sekolah</span>
                        </div>
                        <div>
                            <span class="text-slate-500 uppercase text-[10px]">Kasus :</span>
                            <span class="text-slate-900 font-extrabold">Cedera jaringan lunak (luka) dan kasus pingsan</span>
                        </div>
                        <div>
                            <span class="text-slate-500 uppercase text-[10px]">Panduan :</span>
                            <span class="text-slate-900 font-extrabold">Buku pedoman PP tahun 2008 dan 2009</span>
                        </div>
                        <div class="pt-1">
                            <span class="text-slate-500 uppercase text-[10px]">No Urut :</span>
                            <span class="font-mono text-slate-900 font-extrabold">{{ $currentTeam?->order_number ?: '-' }} / {{ $currentTeam?->team_name ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 uppercase text-[10px]">Nama Sekolah :</span>
                            <span class="text-slate-900 font-extrabold">{{ $currentTeam?->registration?->school_name ?: $currentTeam?->team_name ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 uppercase text-[10px]">Tingkat :</span>
                            <span class="text-slate-900 font-extrabold">Wira</span>
                        </div>
                    </div>

                    <div class="md:col-span-4 flex flex-col justify-between items-center sm:items-end gap-4">
                        <div class="bg-white p-3 rounded-xl border border-slate-200 w-full text-center">
                            <span class="text-[10px] text-slate-500 uppercase font-black block">WAKTU PRAKTIK :</span>
                            <span id="bottom-time-display" class="font-mono text-xl font-black text-amber-700">{{ $existingTime ?: '05:00' }}</span>
                        </div>

                        <!-- Signatures Box -->
                        <div class="grid grid-cols-2 gap-3 w-full">
                            <div class="border border-slate-300 bg-white rounded-xl p-2.5 text-center">
                                <div class="text-[10px] uppercase text-slate-400 font-bold mb-6">Juri</div>
                                <div class="text-[11px] font-black text-slate-800 border-t border-slate-200 pt-1">
                                    ( {{ auth()->user()->name ?? 'Dewan Juri' }} )
                                </div>
                            </div>
                            <div class="border border-slate-300 bg-white rounded-xl p-2.5 text-center">
                                <div class="text-[10px] uppercase text-slate-400 font-bold mb-6">Peserta</div>
                                <div class="text-[11px] font-black text-slate-800 border-t border-slate-200 pt-1">
                                    ( {{ $currentTeam?->team_name ?? 'Danru Peserta' }} )
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes for Judges -->
                <div>
                    <label for="notes" class="block text-[11px] font-bold text-slate-600 mb-1">Catatan Khusus Juri Lapangan (Opsional):</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:border-amber-500 placeholder-slate-400 font-normal" placeholder="Tuliskan catatan observasi atau penanganan regu jika ada...">{{ $existingNotes }}</textarea>
                </div>
            </div>

        </div>

    </form>

</div>

<!-- STICKY BOTTOM ACTION BAR -->
<div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t-2 border-slate-200 px-4 sm:px-6 py-3.5 shadow-2xl z-50">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-start">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Nilai Wira</div>
                <div class="text-2xl font-black font-mono text-slate-900">
                    <span id="bottom-score-display">0</span> <span class="text-xs text-slate-400 font-sans">/ 700</span>
                </div>
            </div>
            <div class="border-l border-slate-200 pl-4 hidden sm:block">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Waktu Lapangan</div>
                <div id="bottom-time-display-sticky" class="text-xl font-black font-mono text-amber-700">
                    {{ $existingTime ?: '05:00' }}
                </div>
            </div>
            @if($currentTeam)
                <div class="border-l border-slate-200 pl-4 hidden md:block">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Regu Sedang Dinilai</div>
                    <div class="text-xs font-black text-slate-800 truncate max-w-xs">
                        {{ $currentTeam->registration?->school_name ?: $currentTeam->team_name }}
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end flex-wrap">
            <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                Batal
            </a>

            <!-- Save and Stay on this team -->
            <button type="button" onclick="submitScoreForm('stay')" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black transition flex items-center gap-1.5 shadow-md shadow-amber-950/10">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Nilai
            </button>

            <!-- Save and Go to next team -->
            @if($nextTeam)
                <button type="button" onclick="submitScoreForm('next')" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-black transition flex items-center gap-1.5 shadow-md">
                    <span>Simpan & Regu Berikutnya</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </button>
            @else
                <button type="button" onclick="submitScoreForm('back')" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-black transition flex items-center gap-1.5 shadow-md">
                    <i class="fa-solid fa-check-double"></i>
                    <span>Simpan & Selesai</span>
                </button>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initial calculation on load
    document.addEventListener('DOMContentLoaded', () => {
        recalcAll();
    });

    // Select option for a rubric row
    function selectOption(key, val, triggerRecalc = true) {
        const input = document.getElementById('input_' + key);
        if (!input) return;
        input.value = val;

        const row = document.getElementById('row_' + key);
        if (!row) return;

        // Reset all active classes on this row's buttons
        row.querySelectorAll('.btn-score').forEach(btn => {
            btn.classList.remove('active-k', 'active-c', 'active-b');
            btn.classList.add('bg-white');
            const type = btn.dataset.type;
            if (type === 'k') btn.classList.add('text-slate-600');
            if (type === 'c') btn.classList.add('text-amber-700');
            if (type === 'b') btn.classList.add('text-emerald-700');
        });

        // Activate matching button
        const clickedBtn = row.querySelector(`.btn-score[data-value="${val}"]`);
        if (clickedBtn) {
            clickedBtn.classList.remove('bg-white');
            const type = clickedBtn.dataset.type;
            if (type === 'k') clickedBtn.classList.add('active-k');
            if (type === 'c') clickedBtn.classList.add('active-c');
            if (type === 'b') clickedBtn.classList.add('active-b');
        }

        // Update row score display
        const display = row.querySelector('.row-score-display');
        const maxVal = parseInt(row.dataset.max || 0);
        if (display) {
            display.textContent = val;
            display.classList.remove('text-slate-400', 'text-amber-700', 'text-emerald-700');
            if (val === 0) {
                display.classList.add('text-slate-400');
            } else if (val === maxVal) {
                display.classList.add('text-emerald-700');
            } else {
                display.classList.add('text-amber-700');
            }
        }

        if (triggerRecalc) {
            recalcAll();
        }
    }

    // Recalculate section subtotals and grand total
    function recalcAll() {
        let grandTotal = 0;

        for (let sec = 1; sec <= 6; sec++) {
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
        document.getElementById('bottom-score-display').textContent = grandTotal;
    }

    // Set all rubric items to maximum score "Baik (B)"
    function setAllValues(type) {
        document.querySelectorAll('tr[id^="row_"]').forEach(row => {
            const btnB = row.querySelector('.btn-score[data-type="' + type.toLowerCase() + '"]');
            if (btnB) {
                btnB.click();
            }
        });
    }

    // Reset all rubric options to 0
    function resetAllRubric() {
        if (!confirm('Apakah Anda yakin ingin mengosongkan pilihan rubrik nilai ini?')) return;
        document.querySelectorAll('tr[id^="row_"]').forEach(row => {
            const btnK = row.querySelector('.btn-score[data-type="k"]');
            if (btnK) {
                btnK.click();
            }
        });
    }

    // Submit scoring form
    function submitScoreForm(action) {
        document.getElementById('action-after').value = action;
        document.getElementById('grid-score-form').submit();
    }

    // Stopwatch Logic
    let timerInterval = null;
    let timerSeconds = 0;
    let timerRunning = false;

    // Parse MM:SS to seconds on start
    function initStopwatchFromInput() {
        const inputVal = document.getElementById('time-recorded-input').value.trim();
        if (inputVal.includes(':')) {
            const parts = inputVal.split(':');
            timerSeconds = (parseInt(parts[0]) || 0) * 60 + (parseInt(parts[1]) || 0);
        } else if (!isNaN(parseInt(inputVal))) {
            timerSeconds = parseInt(inputVal) || 0;
        }
    }
    initStopwatchFromInput();

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
        const stickyDisplay = document.getElementById('bottom-time-display-sticky');
        if (stickyDisplay) stickyDisplay.textContent = str;
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
        const stickyDisplay = document.getElementById('bottom-time-display-sticky');
        if (stickyDisplay) stickyDisplay.textContent = val || '00:00';
        initStopwatchFromInput();
    }
</script>
@endpush
