@extends('layouts.admin')

@section('title', 'Grid Nilai Pertolongan Pertama Tingkat Mula - ' . $category->display_name)
@section('page_title', 'Grid Nilai: ' . $category->display_name)

@section('top_actions')
    <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-xl font-bold text-xs shadow-2xs transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Lembar Skor Utama
    </a>
    <a href="{{ route('admin.competition-scores.master-sheet', ['level' => 'Mula']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow transition flex items-center gap-2">
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
                                    <i class="fa-solid fa-venus"></i> {{ $category->code ?? 'LPP-MULA-PI' }} (Putri)
                                @else
                                    <i class="fa-solid fa-mars"></i> {{ $category->code ?? 'LPP-MULA-PA' }} (Putra)
                                @endif
                            </span>
                            <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 font-extrabold text-xs">
                                PMR Mula (SD/MI)
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

                    <!-- Dropdown Select Team -->
                    @php
                        $isPi = ($category->gender_category === 'Putri') || str_contains(strtoupper($category->code ?? ''), '-PI');
                        $genderText = $isPi ? 'Putri' : 'Putra';
                    @endphp
                    <div>
                        <label for="team-selector-dropdown" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>PILIH REGU PESERTA YANG SEDANG DINILAI:</span>
                            <span class="text-emerald-700 font-extrabold text-[11px] bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                Khusus PMR Mula {{ $genderText }} (SD/MI) &bull; Total: {{ $teams->count() }} Regu
                            </span>
                        </label>
                        <div class="relative">
                            <select id="team-selector-dropdown" onchange="window.location.href='{{ route('admin.competition-scores.grid', $category->id) }}?round={{ $round }}&team_id=' + this.value" class="w-full bg-slate-50 hover:bg-slate-100 border-2 border-red-500 rounded-2xl py-3 px-4 text-sm font-black text-slate-900 focus:outline-none focus:ring-4 focus:ring-red-100 transition cursor-pointer appearance-none shadow-xs">
                                @forelse($teams as $t)
                                    @php
                                        $tScore = $scores->get($t->id);
                                        $hasScore = $tScore && ($tScore->final_score > 0 || !empty($tScore->score_details['grid_rubric']));
                                    @endphp
                                    <option value="{{ $t->id }}" {{ $currentTeam?->id == $t->id ? 'selected' : '' }}>
                                        [No. {{ $t->order_number ?: '-' }}] {{ $t->registration?->school_name ?: $t->team_name }} &bull; {{ $t->team_label ?: $t->team_name }} [Mula {{ $genderText }}] ({{ $t->registration?->registration_code ?: 'OTS' }}) {{ $hasScore ? '✓ [Skor: ' . $tScore->final_score . ' | Wkt: ' . ($tScore->time_recorded ?: '-') . ']' : '(Belum dinilai)' }}
                                    </option>
                                @empty
                                    <option value="" disabled selected>-- Belum ada regu PMR Mula {{ $genderText }} (SD/MI) yang terdaftar di cabang ini --</option>
                                @endforelse
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Technical Guideline metadata -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-[11px] text-slate-600 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80">
                        <div>
                            <span class="text-slate-400 font-bold block text-[10px] uppercase">Mekanisme Kejadian:</span>
                            <strong class="text-slate-800 font-extrabold">Di sekolah & sekitar sekolah</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold block text-[10px] uppercase">Kasus Lomba:</span>
                            <strong class="text-emerald-700 font-black"><i class="fa-solid fa-bandage mr-0.5"></i> Luka Ringan</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold block text-[10px] uppercase">Buku Panduan:</span>
                            <strong class="text-slate-800 font-extrabold">Pedoman PP 2008 & 2009</strong>
                        </div>
                    </div>
                </div>

                <!-- Right: Live Stopwatch & Score Overview -->
                <div class="lg:col-span-5 flex flex-col justify-between h-full space-y-4">
                    
                    <!-- Stopwatch Card -->
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-5 rounded-2xl shadow-md border border-slate-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-extrabold text-slate-300 tracking-wider uppercase flex items-center gap-1.5">
                                <i class="fa-solid fa-stopwatch text-amber-400"></i> Stopwatch Waktu Praktik
                            </span>
                            <span class="text-[10px] bg-amber-400/20 text-amber-300 font-black px-2.5 py-0.5 rounded-full border border-amber-400/30">
                                Maks: 06:30
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <div id="stopwatch-display" class="text-4xl font-black font-mono tracking-widest text-emerald-400">
                                {{ $existingTime ?: '05:00' }}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" id="btn-timer-start" onclick="startTimer()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-1">
                                    <i class="fa-solid fa-play text-[10px]"></i> Mulai
                                </button>
                                <button type="button" id="btn-timer-pause" onclick="pauseTimer()" class="bg-amber-500 hover:bg-amber-600 text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-1">
                                    <i class="fa-solid fa-pause text-[10px]"></i> Jeda
                                </button>
                                <button type="button" onclick="resetTimer()" class="bg-slate-700 hover:bg-slate-600 text-slate-200 px-3 py-2 rounded-xl font-bold text-xs transition" title="Reset Waktu">
                                    <i class="fa-solid fa-rotate-right text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-slate-700/80 text-[11px] text-slate-400 flex items-center justify-between gap-2">
                            <span>Input Waktu Lapangan (MM:SS):</span>
                            <input type="text" id="time-recorded-input" name="time_recorded" value="{{ $existingTime ?: '05:00' }}" oninput="syncTimerInput(this.value)" class="w-20 bg-slate-950 text-center font-mono font-black text-sm border border-slate-600 rounded-lg px-2 py-1 text-emerald-300 focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <!-- Live Score Counter -->
                    <div class="bg-emerald-50 border-2 border-emerald-300 p-5 rounded-2xl flex items-center justify-between shadow-2xs">
                        <div>
                            <div class="text-xs font-black text-emerald-900 uppercase tracking-wider">TOTAL NILAI PRAKTIK (GRID)</div>
                            <div class="text-[11px] text-emerald-700 font-medium">Akumulasi 6 Bagian Rubrik Lapangan</div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl sm:text-4xl font-black text-emerald-700 tracking-tight font-mono">
                                <span id="grand-total-display">0</span> <span class="text-sm font-bold text-emerald-600">/ 540</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- MAIN RUBRIC TABLE (GRID NILAI PERTOLONGAN PERTAMA TINGKAT MULA) -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mt-6">
            
            <!-- Table Action Header -->
            <div class="p-5 border-b border-slate-200 bg-slate-50/80 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block animate-ping"></span>
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
                        <tr class="bg-emerald-600 text-white font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">1</td>
                            <td colspan="2" class="py-2.5 px-5">PENILAIAN KEADAAN</td>
                            <td class="py-2.5 px-4 text-center bg-emerald-700 font-mono font-black text-xs">
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
                            $itemsSec1 = [
                                '1_a' => ['label' => 'Sudah menggunakan APD (masker & sarung tangan)', 'options' => [0, 5, 10]],
                            ];
                        @endphp
                        @foreach($itemsSec1 as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => 'a', 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 1])
                        @endforeach

                        <!-- Sub 1.2 Aman Lingkungan -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Aman Lingkungan</td>
                        </tr>
                        @php
                            $itemsSec1b = [
                                '1_b' => ['label' => 'Memperkenalkan diri dan tim', 'options' => [0, 5, 10], 'letter' => 'b'],
                                '1_c' => ['label' => 'Memastikan sistematika kejadian', 'options' => [0, 5, 10], 'letter' => 'c'],
                                '1_d' => ['label' => 'Meminta izin dan saksi', 'options' => [0, 5, 10], 'letter' => 'd'],
                            ];
                        @endphp
                        @foreach($itemsSec1b as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 1])
                        @endforeach

                        <!-- Sub 1.3 Aman Pasien -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Aman Pasien</td>
                        </tr>
                        @include('admin.competition.scores.partials.grid-row', ['key' => '1_e', 'letter' => 'e', 'label' => 'Memastikan lokasi kejadian (aman & tidak aman)', 'options' => [0, 5, 10], 'section' => 1])


                        <!-- ================= SECTION 2: PENILAIAN DINI ================= -->
                        <tr class="bg-emerald-600 text-white font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">2</td>
                            <td colspan="2" class="py-2.5 px-5">PENILAIAN DINI</td>
                            <td class="py-2.5 px-4 text-center bg-emerald-700 font-mono font-black text-xs">
                                <span id="subtotal-display-2">0</span> / 100
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[2]" id="subtotal-input-2" value="0">
                        @php
                            $itemsSec2 = [
                                '2_a' => ['label' => 'Menyebutkan kesan umum (trauma/medis)', 'options' => [0, 10, 20], 'letter' => 'a'],
                                '2_b' => ['label' => 'Cek respon (awas)', 'options' => [0, 10, 20], 'letter' => 'b'],
                                '2_c' => ['label' => 'Cek nafas (Pasien sadar)', 'options' => [0, 10, 20], 'letter' => 'c'],
                                '2_d' => ['label' => 'Cek nadi (Radialis)', 'options' => [0, 10, 20], 'letter' => 'd'],
                                '2_e' => ['label' => 'Meminta bantuan Guru/Orang dewasa', 'options' => [0, 10, 20], 'letter' => 'e'],
                            ];
                        @endphp
                        @foreach($itemsSec2 as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 2])
                        @endforeach


                        <!-- ================= SECTION 3: PENANGANAN KASUS ================= -->
                        <tr class="bg-emerald-600 text-white font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">3</td>
                            <td colspan="2" class="py-2.5 px-5">PENANGANAN KASUS</td>
                            <td class="py-2.5 px-4 text-center bg-emerald-700 font-mono font-black text-xs">
                                <span id="subtotal-display-3">0</span> / 210
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[3]" id="subtotal-input-3" value="0">

                        <!-- Sub 3.1 Kasus Trauma -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Kasus Trauma (Bobot Skor Tinggi: 0 / 20 / 40)</td>
                        </tr>
                        @php
                            $itemsSec3Trauma = [
                                '3_a' => ['label' => 'Tepat menentukan posisi luka (kiri, kanan, atas, bawah)', 'options' => [0, 20, 40], 'letter' => 'a'],
                                '3_b' => ['label' => 'Tepat menggunakan peralatan (penutup luka & pembalut luka)', 'options' => [0, 20, 40], 'letter' => 'b'],
                                '3_c' => ['label' => 'Menyebutkan ciri-ciri & menentukan jenis luka', 'options' => [0, 20, 40], 'letter' => 'c'],
                                '3_d' => ['label' => 'Penatalaksanaan Kasus Trauma', 'options' => [0, 20, 40], 'letter' => 'd'],
                            ];
                        @endphp
                        @foreach($itemsSec3Trauma as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 3])
                        @endforeach

                        <!-- Sub 3.2 Pemeriksaan Fisik Keseluruhan -->
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Pemeriksaan Fisik Keseluruhan (0 / 5 / 10)</td>
                        </tr>
                        @php
                            $itemsSec3Fisik = [
                                '3_e' => ['label' => 'Kepala (tengkorak, telinga, mata, hidung, mulut)', 'options' => [0, 5, 10], 'letter' => 'e'],
                                '3_f' => ['label' => 'Leher (tulang leher / cervical)', 'options' => [0, 5, 10], 'letter' => 'f'],
                                '3_g' => ['label' => 'Batang Tubuh (bahu, dada, perut, panggul, tulang belakang)', 'options' => [0, 5, 10], 'letter' => 'g'],
                                '3_h' => ['label' => 'Alat gerak atas (lengan atas, sikut, lengan bawah, pergelangan)', 'options' => [0, 5, 10], 'letter' => 'h'],
                                '3_i' => ['label' => 'Alat gerak bawah (tungkai atas, lutut, tungkai bawah, pergelangan)', 'options' => [0, 5, 10], 'letter' => 'i'],
                            ];
                        @endphp
                        @foreach($itemsSec3Fisik as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 3])
                        @endforeach


                        <!-- ================= SECTION 4: RIWAYAT PENDERITA ================= -->
                        <tr class="bg-emerald-600 text-white font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">4</td>
                            <td colspan="2" class="py-2.5 px-5">RIWAYAT PENDERITA</td>
                            <td class="py-2.5 px-4 text-center bg-emerald-700 font-mono font-black text-xs">
                                <span id="subtotal-display-4">0</span> / 140
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[4]" id="subtotal-input-4" value="0">
                        <tr class="bg-slate-100/90 font-extrabold text-slate-700 text-[11px]">
                            <td class="py-1.5 px-4 text-center text-slate-400">&bull;</td>
                            <td colspan="3" class="py-1.5 px-5">Pemeriksaan Tanda Vital & Wawancara (0 / 10 / 20)</td>
                        </tr>
                        @php
                            $itemsSec4 = [
                                '4_a' => ['label' => 'Nadi 1 menit (diukur dan diperiksa dengan tepat)', 'options' => [0, 10, 20], 'letter' => 'a'],
                                '4_b' => ['label' => 'Nafas 1 menit (diukur dan diperiksa dengan tepat)', 'options' => [0, 10, 20], 'letter' => 'b'],
                                '4_c' => ['label' => 'Suhu (menggunakan alat atau tidak menggunakan alat)', 'options' => [0, 10, 20], 'letter' => 'c'],
                                '4_d' => ['label' => 'Menganalisa kondisi dan warna kulit', 'options' => [0, 10, 20], 'letter' => 'd'],
                                '4_e' => ['label' => 'Wawancara KOMPAK (pasien sadar)', 'options' => [0, 10, 20], 'letter' => 'e'],
                                '4_f' => ['label' => 'Dukungan emosional kepada korban', 'options' => [0, 10, 20], 'letter' => 'f'],
                                '4_g' => ['label' => 'Komunikasi dengan pasien selama tindakan', 'options' => [0, 10, 20], 'letter' => 'g'],
                            ];
                        @endphp
                        @foreach($itemsSec4 as $key => $meta)
                            @include('admin.competition.scores.partials.grid-row', ['key' => $key, 'letter' => $meta['letter'], 'label' => $meta['label'], 'options' => $meta['options'], 'section' => 4])
                        @endforeach


                        <!-- ================= SECTION 5: PELAPORAN ================= -->
                        <tr class="bg-emerald-600 text-white font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">5</td>
                            <td colspan="2" class="py-2.5 px-5">PELAPORAN</td>
                            <td class="py-2.5 px-4 text-center bg-emerald-700 font-mono font-black text-xs">
                                <span id="subtotal-display-5">0</span> / 20
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[5]" id="subtotal-input-5" value="0">
                        @include('admin.competition.scores.partials.grid-row', ['key' => '5_a', 'letter' => 'a', 'label' => 'Pengisian kartu luka (diisi lengkap & jelas)', 'options' => [0, 10, 20], 'section' => 5])


                        <!-- ================= SECTION 6: KEKOMPAKAN TIM ================= -->
                        <tr class="bg-emerald-600 text-white font-black text-xs uppercase tracking-wider">
                            <td class="py-2.5 px-4 text-center font-mono">6</td>
                            <td colspan="2" class="py-2.5 px-5">KEKOMPAKAN TIM</td>
                            <td class="py-2.5 px-4 text-center bg-emerald-700 font-mono font-black text-xs">
                                <span id="subtotal-display-6">0</span> / 20
                            </td>
                        </tr>
                        <input type="hidden" name="subtotals[6]" id="subtotal-input-6" value="0">
                        @include('admin.competition.scores.partials.grid-row', ['key' => '6_a', 'letter' => 'a', 'label' => 'Kekompakan tim, koordinasi & pembagian tugas anggota regu', 'options' => [0, 10, 20], 'section' => 6])


                        <!-- ================= GRAND TOTAL ROW ================= -->
                        <tr class="bg-slate-900 text-white font-black text-base border-t-2 border-slate-800">
                            <td colspan="3" class="py-4 px-6 text-right uppercase tracking-wider font-extrabold text-sm sm:text-base">
                                JUMLAH TOTAL NILAI PRAKTIK:
                            </td>
                            <td class="py-4 px-4 text-center font-mono text-2xl text-emerald-400 bg-black">
                                <span id="grand-total-row">0</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Footer Sign-off & Notes -->
            <div class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
                <div class="w-full md:w-auto space-y-1">
                    <label for="jury-notes-input" class="font-extrabold text-slate-700 block">Catatan Khusus Juri (Opsional):</label>
                    <input type="text" id="jury-notes-input" name="notes" value="{{ $existingNotes }}" placeholder="Tuliskan catatan lapangan jika ada..." class="w-full md:w-96 text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:border-red-500 shadow-2xs">
                </div>
                <div class="flex items-center gap-3 self-end md:self-auto">
                    <div class="text-right">
                        <div class="font-bold text-slate-800">Dewan Juri Bertugas:</div>
                        <div class="text-[11px] text-slate-500 font-medium">
                            @forelse($category->assignedJuries as $jury)
                                {{ $jury->name }}@if(!$loop->last), @endif
                            @empty
                                {{ auth()->user()?->name ?? 'Dewan Juri Resmi' }}
                            @endforelse
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-slate-300 flex items-center justify-center font-bold text-slate-600 shadow-2xs">
                        <i class="fa-solid fa-signature"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- STICKY BOTTOM ACTION BAR (FOR MOBILE, TABLET & DESKTOP JURY) -->
        <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t-2 border-slate-200 px-4 sm:px-6 py-3.5 shadow-2xl z-50">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-start">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Regu Sedang Dinilai:</span>
                        <strong class="text-slate-900 text-xs sm:text-sm font-black truncate max-w-[200px] sm:max-w-xs block">
                            [No. {{ $currentTeam?->order_number ?: '-' }}] {{ $currentTeam?->registration?->school_name ?: $currentTeam?->team_name }}
                        </strong>
                    </div>
                    <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>
                    <div class="flex items-center gap-3">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Waktu:</span>
                            <strong id="bottom-time-display" class="text-emerald-700 font-mono font-black text-sm">{{ $existingTime ?: '05:00' }}</strong>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Nilai:</span>
                            <strong class="text-emerald-700 font-mono font-black text-base"><span id="bottom-score-display">0</span> <span class="text-xs text-slate-400 font-normal">/ 540</span></strong>
                        </div>
                    </div>
                </div>

                <!-- Submit Action Buttons -->
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-3.5 py-2.5 rounded-xl transition">
                        Batal
                    </a>
                    <button type="button" onclick="submitScoreForm('stay')" class="bg-red-600 hover:bg-red-700 text-white font-black text-xs px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i> <span>Simpan Nilai</span>
                    </button>
                    @if($nextTeam)
                        <button type="button" onclick="submitScoreForm('next')" class="bg-slate-900 hover:bg-black text-white font-black text-xs px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <span>Simpan & Regu Berikutnya</span> <i class="fa-solid fa-forward-step text-[10px]"></i>
                        </button>
                    @else
                        <button type="button" onclick="submitScoreForm('back')" class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <span>Simpan & Selesai</span> <i class="fa-solid fa-circle-check text-[10px]"></i>
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
    // Existing stored values
    const initialRubric = @json($existingRubric);

    // Initialize values on DOM load
    document.addEventListener('DOMContentLoaded', function() {
        if (initialRubric && Object.keys(initialRubric).length > 0) {
            for (const [key, val] of Object.entries(initialRubric)) {
                selectOption(key, parseInt(val), false);
            }
        }
        recalcAll();
    });

    // Select rubric item option (0, C, B)
    function selectOption(key, val, triggerRecalc = true) {
        const input = document.getElementById('input_' + key);
        if (!input) return;
        input.value = val;

        const row = document.getElementById('row_' + key);
        if (row) {
            // Update active state on buttons
            row.querySelectorAll('.btn-score').forEach(btn => {
                btn.classList.remove('active-k', 'active-c', 'active-b');
                const optVal = parseInt(btn.getAttribute('data-value'));
                if (optVal === val) {
                    const optType = btn.getAttribute('data-type'); // 'k', 'c', 'b'
                    btn.classList.add('active-' + optType);
                }
            });

            // Update displayed row score
            const displayEl = row.querySelector('.row-score-display');
            if (displayEl) {
                displayEl.textContent = val;
                if (val === 0) {
                    displayEl.className = 'row-score-display font-mono font-bold text-slate-400 text-sm';
                } else if (val < parseInt(row.getAttribute('data-max') || '20')) {
                    displayEl.className = 'row-score-display font-mono font-black text-amber-700 text-sm';
                } else {
                    displayEl.className = 'row-score-display font-mono font-black text-emerald-700 text-sm';
                }
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
</script>
@endpush
