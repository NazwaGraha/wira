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
<div class="space-y-6">

    <style>
        .selected-pill {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%) !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2), 0 4px 10px rgba(220, 38, 38, 0.4);
            border-color: #f87171 !important;
            transform: scale(1.05);
        }
        .highlight-time-row {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.25) 0%, rgba(245, 158, 11, 0.1) 100%) !important;
            border-left: 4px solid #f59e0b !important;
            font-weight: 800 !important;
            color: #b45309 !important;
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
                    1-Click Checklist Nilai Teknis (Maks 680) + Auto Look-up Nilai Waktu (Maks 300) &bull; Sinkronisasi Otomatis ke Lembar Skor Utama.
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

    {{-- Main Grid Area: Sidebar Navigasi Tim + Form Grid LKTR + Panel Konversi Waktu --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- ==================== SIDEBAR KIRI: DAFTAR REGU PESERTA ==================== --}}
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

            {{-- List Regu --}}
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

        {{-- ==================== AREA UTAMA: FORMULIR GRID PENILAIAN LKTR ==================== --}}
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

                    $isLevelMula = strcasecmp(trim($category->level), 'Mula') === 0;
                    $formatTitle = $isLevelMula ? 'FORMAT PENILAIAN LOMBA TANDU (MULA)' : 'FORMAT PENILAIAN LOMBA TANDU (MADYA & WIRA)';
                    
                    // Initial values from DB or defaults
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

                    $vWaktu = $existingTime ?: ($isLevelMula ? '05:00' : '04:25');
                @endphp

                <form action="{{ route('admin.competition-scores.grid-save', $category->id) }}" method="POST" id="formGridLKTR">
                    @csrf
                    <input type="hidden" name="competition_participant_team_id" value="{{ $currentTeam->id }}">
                    <input type="hidden" name="round_name" value="{{ $round }}">
                    <input type="hidden" name="action_after" id="actionAfterInput" value="stay">
                    <input type="hidden" name="next_team_id" value="{{ $nextTeam?->id }}">

                    {{-- Hidden Inputs Rubrik Kriteria Teknis --}}
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

                    {{-- Hidden Subtotals --}}
                    <input type="hidden" name="subtotals[kekuatan]" id="input_subtotal_kekuatan" value="0">
                    <input type="hidden" name="subtotals[kerapihan]" id="input_subtotal_kerapihan" value="0">
                    <input type="hidden" name="subtotals[teknis]" id="input_subtotal_teknis" value="0">
                    <input type="hidden" name="subtotals[waktu]" id="input_subtotal_waktu" value="0">
                    <input type="hidden" name="subtotals[total]" id="input_subtotal_total" value="0">

                    {{-- Layout Split: Kiri (Kertas Lembar Juri) & Kanan (Tabel Waktu & Live Master Sheet) --}}
                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                        
                        {{-- SISI KIRI: LEMBAR PENILAIAN LKTR --}}
                        <div class="xl:col-span-8 bg-white rounded-3xl p-6 shadow-xl border border-slate-200 relative">
                            
                            {{-- Decorative Badge --}}
                            <div class="absolute top-0 right-0 bg-gradient-to-l from-red-600 to-rose-500 text-white text-[10px] sm:text-[11px] font-black px-4 py-1.5 rounded-bl-2xl shadow-md uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-hand-pointer"></i> 1-Click Checklist / Lingkari Nilai
                            </div>

                            {{-- Header Kertas Lembar Juri --}}
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
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">NOMOR URUT :</span>
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

                            {{-- Tabel Item Penilaian LKTR --}}
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

                                        {{-- 3. Kekuatan bambu --}}
                                        <tr class="bg-slate-50/80">
                                            <td class="py-1.5 px-2 text-center border-r border-slate-200 text-slate-400">3</td>
                                            <td colspan="6" class="py-1.5 px-3 text-left font-bold text-slate-700">Kekuatan bambu :</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-center border-r border-slate-200 text-slate-400"></td>
                                            <td class="py-1.5 px-3 text-left border-r border-slate-200 pl-6 text-slate-600">a. Diputar bambu kecil</td>
                                            @foreach([15, 30, 45, 60] as $opt)
                                                <td class="p-1 border-r border-slate-200">
                                                    <button type="button" onclick="selectScore('kekuatan_3a', {{ $opt }}, this)" class="btn-rubric-kekuatan_3a w-full py-1.5 rounded-lg {{ $vKekuatan3a == $opt ? 'selected-pill' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} font-bold text-xs border border-slate-200 transition">
                                                        {{ $opt }}{{ $vKekuatan3a == $opt ? ' ✓' : '' }}
                                                    </button>
                                                </td>
                                            @endforeach
                                            <td class="py-1.5 px-2 bg-slate-50 font-mono font-black text-xs text-slate-900" id="score_kekuatan_3a">{{ $vKekuatan3a ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2 text-center border-r border-slate-200 text-slate-400"></td>
                                            <td class="py-1.5 px-3 text-left border-r border-slate-200 pl-6 text-slate-600">b. Diinjak 4 sisi</td>
                                            @foreach([25, 50, 75, 100] as $opt)
                                                <td class="p-1 border-r border-slate-200">
                                                    <button type="button" onclick="selectScore('kekuatan_3b', {{ $opt }}, this)" class="btn-rubric-kekuatan_3b w-full py-1.5 rounded-lg {{ $vKekuatan3b == $opt ? 'selected-pill' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} font-bold text-xs border border-slate-200 transition">
                                                        {{ $opt }}{{ $vKekuatan3b == $opt ? ' ✓' : '' }}
                                                    </button>
                                                </td>
                                            @endforeach
                                            <td class="py-1.5 px-2 bg-slate-50 font-mono font-black text-xs text-slate-900" id="score_kekuatan_3b">{{ $vKekuatan3b ?: '-' }}</td>
                                        </tr>

                                        {{-- 4. Kekuatan tali pengaman --}}
                                        <tr>
                                            <td class="py-1.5 px-2 text-center border-r border-slate-200 text-slate-400">4</td>
                                            <td class="py-1.5 px-3 text-left border-r border-slate-200">Kekuatan tali pengaman</td>
                                            @foreach([20, 40, 60, 80] as $opt)
                                                <td class="p-1 border-r border-slate-200">
                                                    <button type="button" onclick="selectScore('kekuatan_4', {{ $opt }}, this)" class="btn-rubric-kekuatan_4 w-full py-1.5 rounded-lg {{ $vKekuatan4 == $opt ? 'selected-pill' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} font-bold text-xs border border-slate-200 transition">
                                                        {{ $opt }}{{ $vKekuatan4 == $opt ? ' ✓' : '' }}
                                                    </button>
                                                </td>
                                            @endforeach
                                            <td class="py-1.5 px-2 bg-slate-50 font-mono font-black text-xs text-slate-900" id="score_kekuatan_4">{{ $vKekuatan4 ?: '-' }}</td>
                                        </tr>

                                        {{-- SECTION II: KETEPATAN & KERAPIHAN --}}
                                        <tr class="bg-amber-100 text-amber-950 font-black text-xs text-left">
                                            <td class="py-2 px-2 text-center border-r border-amber-200 font-mono">II.</td>
                                            <td colspan="5" class="py-2 px-3 tracking-wider uppercase border-r border-amber-200">
                                                KETEPATAN & KERAPIHAN <span class="text-[10px] text-amber-700 font-bold ml-2">(Maksimal: 280 Poin)</span>
                                            </td>
                                            <td class="py-2 px-2 text-center font-mono text-amber-900 font-black bg-amber-200/80" id="display_subtotal_kerapihan">0</td>
                                        </tr>

                                        @php
                                            $kerapihanItems = [
                                                ['num' => 1, 'key' => 'kerapihan_1', 'label' => 'Garis tengah jaring', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan1],
                                                ['num' => 2, 'key' => 'kerapihan_2', 'label' => 'Sisa bambu panjang', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan2],
                                                ['num' => 3, 'key' => 'kerapihan_3', 'label' => 'Sisa bambu kecil', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan3],
                                                ['num' => 4, 'key' => 'kerapihan_4', 'label' => 'Jumlah lilitan pangkal dan cincin', 'opts' => [20, 25, 30, 45], 'val' => $vKerapihan4],
                                                ['num' => 5, 'key' => 'kerapihan_5', 'label' => 'Jumlah silangan', 'opts' => [10, 15, 20, 25], 'val' => $vKerapihan5],
                                                ['num' => 6, 'key' => 'kerapihan_6', 'label' => 'Diameter tali 5mm', 'opts' => [10, 15, 20, 25], 'val' => $vKerapihan6],
                                                ['num' => 7, 'key' => 'kerapihan_7', 'label' => 'Jarak antar jangkar', 'opts' => [10, 15, 20, 25], 'val' => $vKerapihan7],
                                                ['num' => 8, 'key' => 'kerapihan_8', 'label' => 'Bentuk tandu', 'opts' => [10, 15, 20, 40], 'val' => $vKerapihan8],
                                                ['num' => 9, 'key' => 'kerapihan_9', 'label' => 'Sisa tali', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan9],
                                                ['num' => 10, 'key' => 'kerapihan_10', 'label' => 'Sunan tali', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan10],
                                                ['num' => 11, 'key' => 'kerapihan_11', 'label' => 'Pemasangan mitela', 'opts' => [5, 10, 15, 20], 'val' => $vKerapihan11],
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
                                            <span class="text-[10px] text-slate-500 font-medium">
                                                {{ $isLevelMula ? 'Tingkat Mula (Interval 15 Detik)' : 'Tingkat Madya & Wira (Interval 10 Detik)' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">
                                        Maks 300 Poin
                                    </span>
                                </div>

                                {{-- Scrollable Table Waktu Sesuai Tingkatan --}}
                                <div class="overflow-y-auto max-h-[380px] rounded-xl border border-slate-200 pr-1">
                                    <table class="w-full text-center text-xs">
                                        <thead class="bg-slate-900 text-white uppercase text-[10px] font-black sticky top-0">
                                            <tr>
                                                <th class="py-2 px-3 text-left">Rentang Waktu</th>
                                                <th class="py-2 px-3 text-right">Nilai Poin</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 font-mono text-[11px]" id="tableTimeBody">
                                            @if(!$isLevelMula)
                                                {{-- MADYA & WIRA: Rentang Waktu --}}
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
                                            @else
                                                {{-- MULA: Rentang Waktu (Interval 15s) --}}
                                                <tr id="time_row_300" data-min="0" data-max="180"><td class="py-1.5 px-3 text-left text-slate-600">&lt; 03.00</td><td class="py-1.5 px-3 text-right font-black text-emerald-600">300</td></tr>
                                                <tr id="time_row_290" data-min="181" data-max="195"><td class="py-1.5 px-3 text-left text-slate-600">03.01 - 03.15</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">290</td></tr>
                                                <tr id="time_row_280" data-min="196" data-max="210"><td class="py-1.5 px-3 text-left text-slate-600">03.16 - 03.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">280</td></tr>
                                                <tr id="time_row_270" data-min="211" data-max="225"><td class="py-1.5 px-3 text-left text-slate-600">03.31 - 03.45</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">270</td></tr>
                                                <tr id="time_row_260" data-min="226" data-max="240"><td class="py-1.5 px-3 text-left text-slate-600">03.46 - 04.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">260</td></tr>
                                                <tr id="time_row_250" data-min="241" data-max="255"><td class="py-1.5 px-3 text-left text-slate-600">04.01 - 04.15</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">250</td></tr>
                                                <tr id="time_row_240" data-min="256" data-max="270"><td class="py-1.5 px-3 text-left text-slate-600">04.16 - 04.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">240</td></tr>
                                                <tr id="time_row_230" data-min="271" data-max="285"><td class="py-1.5 px-3 text-left text-slate-600">04.31 - 04.45</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">230</td></tr>
                                                <tr id="time_row_220" data-min="286" data-max="300"><td class="py-1.5 px-3 text-left text-slate-600">04.46 - 05.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">220</td></tr>
                                                <tr id="time_row_210" data-min="301" data-max="315"><td class="py-1.5 px-3 text-left text-slate-600">05.01 - 05.15</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">210</td></tr>
                                                <tr id="time_row_200" data-min="316" data-max="330"><td class="py-1.5 px-3 text-left text-slate-600">05.16 - 05.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">200</td></tr>
                                                <tr id="time_row_190" data-min="331" data-max="345"><td class="py-1.5 px-3 text-left text-slate-600">05.31 - 05.45</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">190</td></tr>
                                                <tr id="time_row_180" data-min="346" data-max="360"><td class="py-1.5 px-3 text-left text-slate-600">05.46 - 06.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">180</td></tr>
                                                <tr id="time_row_170" data-min="361" data-max="375"><td class="py-1.5 px-3 text-left text-slate-600">06.01 - 06.15</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">170</td></tr>
                                                <tr id="time_row_160" data-min="376" data-max="390"><td class="py-1.5 px-3 text-left text-slate-600">06.16 - 06.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">160</td></tr>
                                                <tr id="time_row_150" data-min="391" data-max="405"><td class="py-1.5 px-3 text-left text-slate-600">06.31 - 06.45</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">150</td></tr>
                                                <tr id="time_row_140" data-min="406" data-max="420"><td class="py-1.5 px-3 text-left text-slate-600">06.46 - 07.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">140</td></tr>
                                                <tr id="time_row_130" data-min="421" data-max="435"><td class="py-1.5 px-3 text-left text-slate-600">07.01 - 07.15</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">130</td></tr>
                                                <tr id="time_row_120" data-min="436" data-max="450"><td class="py-1.5 px-3 text-left text-slate-600">07.16 - 07.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">120</td></tr>
                                                <tr id="time_row_110" data-min="451" data-max="465"><td class="py-1.5 px-3 text-left text-slate-600">07.31 - 07.45</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">110</td></tr>
                                                <tr id="time_row_100" data-min="466" data-max="480"><td class="py-1.5 px-3 text-left text-slate-600">07.46 - 08.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">100</td></tr>
                                                <tr id="time_row_90" data-min="481" data-max="495"><td class="py-1.5 px-3 text-left text-slate-600">08.01 - 08.15</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">90</td></tr>
                                                <tr id="time_row_80" data-min="496" data-max="510"><td class="py-1.5 px-3 text-left text-slate-600">08.16 - 08.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">80</td></tr>
                                                <tr id="time_row_70" data-min="511" data-max="525"><td class="py-1.5 px-3 text-left text-slate-600">08.31 - 08.45</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">70</td></tr>
                                                <tr id="time_row_60" data-min="526" data-max="540"><td class="py-1.5 px-3 text-left text-slate-600">08.46 - 09.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">60</td></tr>
                                                <tr id="time_row_50" data-min="541" data-max="555"><td class="py-1.5 px-3 text-left text-slate-600">09.01 - 09.15</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">50</td></tr>
                                                <tr id="time_row_40" data-min="556" data-max="570"><td class="py-1.5 px-3 text-left text-slate-600">09.16 - 09.30</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">40</td></tr>
                                                <tr id="time_row_30" data-min="571" data-max="585"><td class="py-1.5 px-3 text-left text-slate-600">09.31 - 09.45</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">30</td></tr>
                                                <tr id="time_row_20" data-min="586" data-max="600"><td class="py-1.5 px-3 text-left text-slate-600">09.46 - 10.00</td><td class="py-1.5 px-3 text-right font-bold text-slate-700">20</td></tr>
                                            @endif
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

                                    {{-- Baris Peserta Terupdate --}}
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

</div>

<script>
    const isLevelMula = {{ $isLevelMula ? 'true' : 'false' }};

    function setActionAfter(action) {
        document.getElementById('actionAfterInput').value = action;
    }

    function selectScore(key, val, btnElement) {
        // Update hidden input
        const hiddenInput = document.getElementById('input_' + key);
        if (hiddenInput) {
            hiddenInput.value = val;
        }

        // Toggle button styling for this key
        document.querySelectorAll('.btn-rubric-' + key).forEach(btn => {
            btn.classList.remove('selected-pill');
            btn.classList.add('bg-slate-100', 'text-slate-700');
            // Remove checkmark
            btn.innerHTML = btn.innerHTML.replace(' ✓', '').trim();
        });

        btnElement.classList.add('selected-pill');
        btnElement.classList.remove('bg-slate-100', 'text-slate-700');
        btnElement.innerHTML = val + ' ✓';

        // Update row score cell
        const scoreCell = document.getElementById('score_' + key);
        if (scoreCell) {
            scoreCell.innerText = val;
        }

        // Recalculate
        recalculateAll();
    }

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
            // Mula:
            // <= 03:00 (180s) => 300
            // 03:01 - 03:15 (181 - 195s) => 290
            // ...
            // 09:46 - 10:00 (586 - 600s) => 20
            // > 10:00 => 0
            if (seconds <= 180) return 300;
            if (seconds > 600) return 0;
            const step = Math.ceil((seconds - 180) / 15);
            const score = 300 - (step * 10);
            return Math.max(score, 20);
        } else {
            // Madya & Wira:
            // <= 02:40 (160s) => 300
            // 02:41 - 02:50 (161 - 170s) => 285
            // ...
            // 05:21 - 05:30 (321 - 330s) => 45
            // > 05:30 => 0
            if (seconds <= 160) return 300;
            if (seconds > 330) return 0;
            const step = Math.ceil((seconds - 160) / 10);
            const score = 300 - (step * 15);
            return Math.max(score, 45);
        }
    }

    function updateTimeScore() {
        const timeInput = document.getElementById('time_recorded');
        const timeStr = timeInput ? timeInput.value.trim() : '';
        const seconds = parseTimeToSeconds(timeStr);
        const timeScore = computeTimeScore(seconds);

        // Highlight matching table row
        document.querySelectorAll('#tableTimeBody tr').forEach(row => {
            row.classList.remove('highlight-time-row');
            const min = parseInt(row.getAttribute('data-min'), 10) || 0;
            const max = parseInt(row.getAttribute('data-max'), 10) || 0;

            if (seconds >= min && seconds <= max && seconds > 0) {
                row.classList.add('highlight-time-row');
                // Scroll row into view if needed
                row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });

        // Update info text
        const info = document.getElementById('time_lookup_info');
        if (info) {
            if (seconds > 0) {
                info.innerHTML = `Input waktu <strong>${timeStr}</strong> (${seconds} detik) cocok dengan skor waktu: <strong>${timeScore} Poin</strong>.`;
            } else {
                info.innerHTML = 'Input waktu otomatis mencocokkan ke baris tabel dan menambahkan poin waktu.';
            }
        }

        recalculateAll();
    }

    function recalculateAll() {
        // 1. Kekuatan
        const kekuatanKeys = ['kekuatan_1', 'kekuatan_2', 'kekuatan_3a', 'kekuatan_3b', 'kekuatan_4'];
        let subtotalKekuatan = 0;
        kekuatanKeys.forEach(k => {
            const el = document.getElementById('input_' + k);
            if (el && el.value) {
                subtotalKekuatan += parseInt(el.value, 10) || 0;
            }
        });

        // 2. Ketepatan & Kerapihan
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

        // 3. Waktu
        const timeInput = document.getElementById('time_recorded');
        const timeStr = timeInput ? timeInput.value.trim() : '';
        const seconds = parseTimeToSeconds(timeStr);
        const timeScore = computeTimeScore(seconds);

        // 4. Total
        const totalScore = technicalScore + timeScore;

        // Update Displays
        document.getElementById('display_subtotal_kekuatan').innerText = subtotalKekuatan;
        document.getElementById('display_subtotal_kerapihan').innerText = subtotalKerapihan;
        document.getElementById('display_subtotal_waktu').innerText = timeScore;
        document.getElementById('display_footer_teknis').innerText = technicalScore;
        document.getElementById('display_footer_waktu').innerText = timeScore;
        document.getElementById('display_total_score').innerText = totalScore;

        // Update Live Sync Card
        document.getElementById('live_card_teknis').innerText = technicalScore;
        document.getElementById('live_card_time_label').innerText = `Waktu (${timeStr || '-'})`;
        document.getElementById('live_card_waktu').innerText = `+${timeScore}`;
        document.getElementById('live_card_total').innerText = totalScore;

        // Update Hidden Inputs
        document.getElementById('input_subtotal_kekuatan').value = subtotalKekuatan;
        document.getElementById('input_subtotal_kerapihan').value = subtotalKerapihan;
        document.getElementById('input_subtotal_teknis').value = technicalScore;
        document.getElementById('input_subtotal_waktu').value = timeScore;
        document.getElementById('input_subtotal_total').value = totalScore;
    }

    // Run on initial load
    document.addEventListener('DOMContentLoaded', function() {
        updateTimeScore();
        recalculateAll();
    });
</script>
@endsection
