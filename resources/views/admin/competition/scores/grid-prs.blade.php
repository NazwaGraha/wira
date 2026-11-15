@extends('layouts.admin')

@php
    $isWira = strcasecmp(trim($category->level ?? ''), 'Wira') === 0;
    $themeColor = $isWira ? 'kuning' : 'biru';
    $levelName = $isWira ? 'Wira' : 'Madya';

    // Opsi khusus penampilan atribut sesuai tingkatan: Wira (80), Madya (50 atau 80)
    $optsAtribut = $isWira ? [20, 30, 55, 80] : [20, 30, 40, 50];
    $maxAtribut = $isWira ? 80 : 50;

    $prsSections = [
        [
            'no' => '1',
            'title' => 'Materi',
            'max_badge' => 'Maks. 150 Poin',
            'items' => [
                [
                    'key' => 'materi_relevansi',
                    'item_no' => '',
                    'label' => 'Relevansi materi dan pembahasan dengan judul atau tema yang ditentukan',
                    'options' => [60, 90, 120, 150],
                    'max' => 150,
                    'has_empty_first' => false,
                ],
            ],
        ],
        [
            'no' => '2',
            'title' => 'Komunikasi',
            'max_badge' => 'Maks. 480 Poin',
            'items' => [
                [
                    'key' => 'komunikasi_komunikatif',
                    'item_no' => 'a.',
                    'label' => 'Komunikatif',
                    'options' => [60, 80, 100, 120],
                    'max' => 120,
                    'has_empty_first' => false,
                ],
                [
                    'key' => 'komunikasi_tanya_jawab',
                    'item_no' => 'b.',
                    'label' => 'Penguasaan Materi (Tanya Jawab)',
                    'options' => [40, 60, 90, 120],
                    'max' => 120,
                    'has_empty_first' => false,
                ],
                [
                    'key' => 'komunikasi_suasana',
                    'item_no' => 'c.',
                    'label' => 'Kemampuan menghidupkan suasana audience',
                    'options' => [50, 60, 70, 80],
                    'max' => 80,
                    'has_empty_first' => false,
                ],
                [
                    'key' => 'komunikasi_percaya_diri',
                    'item_no' => 'd.',
                    'label' => 'Kepercayaan diri (Penyajian Menarik)',
                    'options' => [40, 60, 80],
                    'max' => 80,
                    'has_empty_first' => true, // Kolom 1 strip (-)
                ],
                [
                    'key' => 'komunikasi_gestur',
                    'item_no' => 'e.',
                    'label' => 'Intonasi dan gestur tubuh',
                    'options' => [30, 45, 60, 80],
                    'max' => 80,
                    'has_empty_first' => false,
                ],
            ],
        ],
        [
            'no' => '3',
            'title' => 'Keterampilan',
            'max_badge' => 'Maks. 180 Poin',
            'items' => [
                [
                    'key' => 'keterampilan_alat_peraga',
                    'item_no' => '',
                    'label' => 'Kreativitas alat peraga',
                    'options' => [60, 80, 100, 120],
                    'max' => 120,
                    'has_empty_first' => false,
                ],
                [
                    'key' => 'keterampilan_logo',
                    'item_no' => '',
                    'label' => 'Penggunaan Logo PMI dan Sekolah Penyelenggara',
                    'options' => [10, 25, 45, 60],
                    'max' => 60,
                    'has_empty_first' => false,
                ],
            ],
        ],
        [
            'no' => '4',
            'title' => 'Penampilan',
            'max_badge' => $isWira ? 'Maks. 130 Poin' : 'Maks. 100 Poin',
            'items' => [
                [
                    'key' => 'penampilan_kerapihan',
                    'item_no' => '',
                    'label' => 'Kerapihan berpakaian',
                    'options' => [20, 30, 40, 50],
                    'max' => 50,
                    'has_empty_first' => false,
                ],
                [
                    'key' => 'penampilan_atribut',
                    'item_no' => '',
                    'label' => 'Atribut sesuai dengan CID PMI' . ($isWira ? ' (WIRA: 20-80)' : ' (MADYA: 20-50)'),
                    'options' => $optsAtribut,
                    'max' => $maxAtribut,
                    'has_empty_first' => false,
                ],
            ],
        ],
        [
            'no' => '5',
            'title' => 'Manajemen Waktu',
            'max_badge' => 'Maks. 60 Poin',
            'items' => [
                [
                    'key' => 'manajemen_waktu',
                    'item_no' => '',
                    'label' => 'Manajemen Waktu',
                    'options' => [15, 30, 45, 60],
                    'max' => 60,
                    'has_empty_first' => false,
                ],
            ],
        ],
    ];

    $allCriteriaKeys = [];
    foreach ($prsSections as $sec) {
        foreach ($sec['items'] as $it) {
            $allCriteriaKeys[] = $it['key'];
        }
    }
@endphp

@section('title', 'Grid Penilaian: ' . $category->name . ' (' . $category->level . ')')
@section('page_title', 'Grid Penilaian Lomba Pendidikan Remaja Sebaya (PRS) Tingkat ' . ($category->level ?? 'Madya'))

@section('top_actions')
    <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
        <i class="fa-solid fa-table-list {{ $isWira ? 'text-amber-500' : 'text-blue-500' }}"></i> Lembar Skor Utama
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

    {{-- Styling Khusus: Madya (Biru) & Wira (Kuning) --}}
    <style>
        .selected-cell {
            @if($isWira)
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.15), 0 4px 10px rgba(245, 158, 11, 0.45);
            border-color: #fde047 !important;
            color: #0f172a !important;
            font-weight: 900 !important;
            transform: scale(1.02);
            @else
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2), 0 4px 10px rgba(2, 132, 199, 0.45);
            border-color: #38bdf8 !important;
            color: #ffffff !important;
            font-weight: 900 !important;
            transform: scale(1.02);
            @endif
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

    {{-- Header Info Card: Madya (Biru) & Wira (Kuning) --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 {{ $isWira ? 'to-amber-950 border-amber-500/30' : 'to-sky-950 border-sky-500/30' }} p-6 rounded-3xl shadow-xl text-white flex flex-col md:flex-row justify-between md:items-center gap-4 border">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr {{ $isWira ? 'from-amber-500 to-yellow-400' : 'from-blue-600 to-sky-400' }} p-0.5 shadow-lg flex items-center justify-center">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <i class="fa-solid fa-comments {{ $isWira ? 'text-amber-400' : 'text-sky-400' }} text-2xl"></i>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $isWira ? 'bg-amber-500 text-slate-950 shadow-sm' : 'bg-blue-600 text-white shadow-sm' }}">
                        {{ $category->code ?? ($isWira ? 'PRS-WIRA' : 'PRS-MADYA') }}
                    </span>
                    <span class="text-xs {{ $isWira ? 'text-amber-200' : 'text-sky-200' }} font-bold">
                        PMR {{ $category->level }} &bull; Kategori {{ $category->gender_category }} &bull; Tema Warna: <strong class="uppercase">{{ $themeColor }}</strong>
                    </span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    {{ $category->name }} ({{ strtoupper($category->level ?? 'MADYA') }})
                </h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    1-Click Touch Grid Penilaian 11 item resmi sesuai format juri &bull; Total akumulasi nilai otomatis tersinkron ke Lembar Skor Utama.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="px-4 py-2.5 rounded-2xl {{ $isWira ? 'bg-amber-500 hover:bg-amber-400 text-slate-950 font-black' : 'bg-blue-600 hover:bg-blue-500 text-white font-bold' }} text-xs transition flex items-center gap-2 shadow-md">
                <i class="fa-solid fa-table-list"></i> Buka Lembar Skor Utama
            </a>
        </div>
    </div>

    {{-- Main Grid Area: Sidebar Navigasi Tim + Form Grid Penilaian --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- ==================== SIDEBAR KIRI: DAFTAR REGU PESERTA ==================== --}}
        <div class="lg:col-span-4 xl:col-span-3 bg-white rounded-3xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-users-line {{ $isWira ? 'text-amber-500' : 'text-blue-600' }}"></i>
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
                       class="block p-3 rounded-2xl border transition relative {{ $isActive ? ($isWira ? 'bg-amber-50/90 border-amber-400 shadow-sm ring-2 ring-amber-400/30' : 'bg-sky-50/90 border-sky-400 shadow-sm ring-2 ring-sky-400/30') : 'bg-slate-50 hover:bg-white border-slate-200' }}">
                        
                        <div class="flex items-center justify-between gap-2">
                            <span class="w-6 h-6 rounded-full text-[11px] font-black flex items-center justify-center {{ $isActive ? ($isWira ? 'bg-amber-500 text-slate-950 font-black' : 'bg-blue-600 text-white font-bold') : 'bg-slate-200 text-slate-700' }}">
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
                                {{ $t->team_name ?? 'Regu Tanpa Nama' }}
                            </div>
                            <div class="text-[11px] text-slate-500 font-medium truncate mt-0.5">
                                {{ $reg->school_name ?? ($t->team_label ?? 'Sekolah Tidak Diketahui') }}
                            </div>
                        </div>

                    </a>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        Belum ada regu terdaftar pada kategori ini.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ==================== PANEL KANAN: FORM GRID PENILAIAN PRS ==================== --}}
        <div class="lg:col-span-8 xl:col-span-9 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 relative">
            
            @if($currentTeam)
                @php
                    $reg = $currentTeam->registration;
                    $schoolName = $reg->school_name ?? ($currentTeam->team_label ?? ($isWira ? 'SMAN 1 Ciawi (Bogor)' : 'SMPN 1 Ciawi (Bogor)'));
                    $teamCode = $currentTeam->team_code ?? (($isWira ? 'PRS-WIR-0' : 'PRS-MDY-0').($currentTeam->order_number ?? $currentTeam->id));
                    $rubricData = $existingRubric ?? [];

                    // Parse waktu menit dan detik
                    $timeParts = explode(':', $existingTime ?? '00:00');
                    $recordedMinutes = intval($timeParts[0] ?? 0);
                    $recordedSeconds = intval($timeParts[1] ?? 0);
                @endphp

                <form action="{{ route('admin.competition-scores.grid-save', $category->id) }}" method="POST" id="formGridPRS">
                    @csrf
                    <input type="hidden" name="competition_participant_team_id" value="{{ $currentTeam->id }}">
                    <input type="hidden" name="round_name" value="{{ $round }}">
                    <input type="hidden" name="action_after" id="actionAfterInput" value="stay">
                    <input type="hidden" name="next_team_id" value="{{ $nextTeam?->id }}">
                    <input type="hidden" name="time_recorded" id="input_time_recorded" value="{{ sprintf('%02d:%02d', $recordedMinutes, $recordedSeconds) }}">

                    {{-- Header Kertas Penilaian Resmi Sesuai Gambar --}}
                    <div class="border-b-2 {{ $isWira ? 'border-amber-400' : 'border-blue-500' }} pb-4 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="inline-flex items-center gap-1.5 {{ $isWira ? 'text-amber-600' : 'text-blue-600' }} font-black text-xs uppercase tracking-widest mb-1">
                                <i class="fa-solid fa-file-circle-check"></i> Form Penilaian Juri Resmi
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                FORMAT PENILAIAN PENDIDIKAN REMAJA SEBAYA
                            </h2>
                            <p class="text-xs text-slate-500">
                                Sesuai standarisasi lembar rekap fisik juri Sua Bhakti Berkarya (SBB) IV 2026.
                            </p>
                        </div>

                        {{-- Kotak Tingkatan (Sesuai Kotak Kanan Atas di Lembar Fisik) --}}
                        <div class="shrink-0 text-center sm:text-right">
                            <span class="inline-block px-5 py-2 rounded-2xl text-sm font-black uppercase tracking-wider {{ $isWira ? 'bg-amber-400 text-slate-950 border-2 border-amber-500 shadow-md shadow-amber-400/30' : 'bg-blue-600 text-white border-2 border-blue-700 shadow-md shadow-blue-500/30' }}">
                                {{ $levelName }}
                            </span>
                        </div>
                    </div>

                    {{-- Metadata Peserta & Input Waktu (Sesuai Baris Header Lembar Juri) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-5">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">NO PESERTA :</span>
                            <span class="text-sm font-black text-slate-900 flex items-center gap-1.5 mt-0.5">
                                <i class="fa-solid fa-id-card-clip {{ $isWira ? 'text-amber-500' : 'text-blue-600' }}"></i>
                                {{ $teamCode }} &bull; Regu {{ $currentTeam->order_number ?? '1' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">NAMA SEKOLAH :</span>
                            <span class="text-sm font-black text-slate-900 flex items-center gap-1.5 mt-0.5 truncate" title="{{ $schoolName }}">
                                <i class="fa-solid fa-school {{ $isWira ? 'text-amber-500' : 'text-blue-600' }}"></i>
                                {{ $schoolName }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">WAKTU TAMPIL (JURI) :</span>
                            <div class="flex items-center gap-1.5 mt-1 font-mono">
                                <input type="number" id="input_minutes" name="time_minutes" min="0" max="99" value="{{ $recordedMinutes }}" oninput="updateTimeRecordedString()" class="w-14 h-8 text-center font-bold text-xs bg-white text-slate-900 border border-slate-300 rounded-lg focus:outline-none {{ $isWira ? 'focus:border-amber-400 focus:ring-1 focus:ring-amber-400' : 'focus:border-blue-500 focus:ring-1 focus:ring-blue-500' }}" placeholder="00">
                                <span class="text-xs font-bold text-slate-600">menit</span>
                                <input type="number" id="input_seconds" name="time_seconds" min="0" max="59" value="{{ $recordedSeconds }}" oninput="updateTimeRecordedString()" class="w-14 h-8 text-center font-bold text-xs bg-white text-slate-900 border border-slate-300 rounded-lg focus:outline-none {{ $isWira ? 'focus:border-amber-400 focus:ring-1 focus:ring-amber-400' : 'focus:border-blue-500 focus:ring-1 focus:ring-blue-500' }}" placeholder="00">
                                <span class="text-xs font-bold text-slate-600">detik</span>
                            </div>
                        </div>
                    </div>

                    {{-- Petunjuk Pengisian --}}
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-600 {{ $isWira ? 'bg-amber-50 border border-amber-200' : 'bg-blue-50 border border-blue-200' }} px-4 py-2.5 rounded-xl mb-4">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-hand-pointer {{ $isWira ? 'text-amber-600' : 'text-blue-600' }}"></i>
                            <span><strong>Petunjuk:</strong> Klik salah satu nilai bobot pada baris kriteria untuk mengakumulasikan total skor.</span>
                        </span>
                        <span class="text-[10px] font-black {{ $isWira ? 'text-amber-950 bg-amber-200/80' : 'text-blue-950 bg-blue-200/80' }} px-2.5 py-0.5 rounded-md">
                            Maks. 1.000 Poin
                        </span>
                    </div>

                    {{-- Hidden Inputs Rubric --}}
                    @foreach($allCriteriaKeys as $critKey)
                        <input type="hidden" name="rubric[{{ $critKey }}]" id="input_{{ $critKey }}" value="{{ $rubricData[$critKey] ?? '' }}">
                    @endforeach

                    {{-- ==================== TABEL RESMI FORMAT PENILAIAN PRS ==================== --}}
                    <div class="rounded-2xl border-2 {{ $isWira ? 'border-amber-400' : 'border-blue-500' }} mb-5 overflow-hidden shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-center border-collapse min-w-[760px]">
                                <thead>
                                    <tr class="bg-slate-900 text-white text-xs uppercase font-extrabold tracking-wider">
                                        <th class="py-3 px-2 w-12 border-r border-slate-700">No</th>
                                        <th class="py-3 px-4 text-left border-r border-slate-700">Uraian Penilaian</th>
                                        <th colspan="4" class="py-2.5 px-2 {{ $isWira ? 'bg-amber-600 border-amber-500' : 'bg-blue-600 border-blue-500' }} border-r text-center tracking-widest">
                                            Bobot Nilai
                                        </th>
                                        <th class="py-3 px-3 w-28 bg-slate-950 text-center">Skor</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs divide-y divide-slate-200 font-semibold text-slate-800">

                                    @foreach($prsSections as $sIndex => $sec)
                                        {{-- Header Kategori Utama (Materi, Komunikasi, Keterampilan, dsb) --}}
                                        <tr class="{{ $isWira ? 'bg-amber-100/70 border-amber-200' : 'bg-blue-50 border-blue-200' }} border-t-2">
                                            <td class="py-2 px-2 text-center font-black {{ $isWira ? 'text-amber-900' : 'text-blue-900' }} border-r border-slate-300">
                                                {{ $sec['no'] }}
                                            </td>
                                            <td colspan="5" class="py-2 px-3 text-left font-black {{ $isWira ? 'text-amber-950' : 'text-blue-950' }} uppercase tracking-wider text-xs">
                                                {{ $sec['title'] }}
                                            </td>
                                            <td class="py-2 px-2 text-center font-mono font-black text-xs {{ $isWira ? 'text-amber-800 bg-amber-50/50' : 'text-blue-800 bg-blue-50/50' }}" id="subtotal_sec_{{ $sec['no'] }}">
                                                -
                                            </td>
                                        </tr>

                                        {{-- Item-item pada kategori ini --}}
                                        @foreach($sec['items'] as $item)
                                            @php
                                                $currVal = $rubricData[$item['key']] ?? null;
                                                $opts = $item['options'];
                                            @endphp
                                            <tr class="hover:bg-slate-50 transition" id="row_{{ $item['key'] }}">
                                                {{-- Kolom No Sub-item --}}
                                                <td class="py-2.5 px-2 text-center text-slate-400 font-bold border-r border-slate-200">
                                                    {{ $item['item_no'] }}
                                                </td>

                                                {{-- Kolom Uraian --}}
                                                <td class="py-2.5 px-3 text-left font-bold text-slate-800 border-r border-slate-200 leading-snug">
                                                    {{ $item['label'] }}
                                                </td>

                                                {{-- 4 Kolom Bobot Nilai --}}
                                                @if($item['has_empty_first'])
                                                    {{-- Kolom 1 Kosong / Strip untuk 2.d (Kepercayaan diri) --}}
                                                    <td class="p-1 border-r border-slate-200 bg-slate-100/60 text-slate-300 text-center font-mono font-black">
                                                        -
                                                    </td>
                                                    @foreach($opts as $val)
                                                        <td class="p-1 border-r border-slate-200 w-20">
                                                            <button type="button" onclick="selectGridScore('{{ $item['key'] }}', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg {{ $isWira ? 'bg-amber-50 hover:bg-amber-100 text-slate-800 border border-amber-200' : 'bg-sky-50 hover:bg-sky-100 text-slate-800 border border-sky-200' }} font-bold transition {{ $currVal == $val ? 'selected-cell' : '' }}">
                                                                {{ $val }}{{ $currVal == $val ? ' ✓' : '' }}
                                                            </button>
                                                        </td>
                                                    @endforeach
                                                @else
                                                    @foreach($opts as $val)
                                                        <td class="p-1 border-r border-slate-200 w-20">
                                                            <button type="button" onclick="selectGridScore('{{ $item['key'] }}', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg {{ $isWira ? 'bg-amber-50 hover:bg-amber-100 text-slate-800 border border-amber-200' : 'bg-sky-50 hover:bg-sky-100 text-slate-800 border border-sky-200' }} font-bold transition {{ $currVal == $val ? 'selected-cell' : '' }}">
                                                                {{ $val }}{{ $currVal == $val ? ' ✓' : '' }}
                                                            </button>
                                                        </td>
                                                    @endforeach
                                                @endif

                                                {{-- Kolom Skor Baris --}}
                                                <td class="py-2 px-2 {{ $isWira ? 'bg-amber-50/60 text-amber-700' : 'bg-sky-50/60 text-sky-700' }} font-mono font-black text-sm" id="badge_{{ $item['key'] }}">
                                                    {{ $currVal ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    {{-- Baris Jumlah Nilai Maksimal --}}
                                    <tr class="bg-slate-900 text-white border-t-2 border-slate-700">
                                        <td colspan="2" class="py-3 px-4 text-right font-black uppercase tracking-wider text-xs border-r border-slate-700">
                                            JUMLAH NILAI (Maks. 1.000)
                                        </td>
                                        <td colspan="4" class="py-3 px-2 text-center text-xs font-bold text-slate-300 border-r border-slate-700">
                                            Akumulasi Skor 11 Item Penilaian
                                        </td>
                                        <td class="py-3 px-2 {{ $isWira ? 'bg-amber-500 text-slate-950' : 'bg-blue-600 text-white' }} font-mono font-black text-base" id="badge_total_score">
                                            0
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Catatan Penilaian & Tanda Tangan Digital --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-5">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase mb-1">
                                Catatan / Evaluasi Juri (Opsional):
                            </label>
                            <textarea name="notes" rows="3" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none {{ $isWira ? 'focus:border-amber-400 focus:ring-1 focus:ring-amber-400' : 'focus:border-blue-500 focus:ring-1 focus:ring-blue-500' }}" placeholder="Tuliskan catatan khusus terkait penyampaian materi, alat peraga, atau penguasaan audiens...">{{ $existingNotes }}</textarea>
                        </div>
                        <div>
                            <span class="block text-xs font-black text-slate-700 uppercase mb-1">
                                Verifikasi / Tanda Tangan Digital (Sesuai Kertas Nilai):
                            </span>
                            <div class="grid grid-cols-2 gap-3 text-center text-xs">
                                <div class="bg-white p-2.5 rounded-xl border border-dashed border-slate-300">
                                    <i class="fa-solid fa-signature text-slate-400 text-xl"></i>
                                    <div class="font-bold text-slate-800 mt-1">Juri Penilai</div>
                                    <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full inline-block mt-0.5">
                                        ✓ {{ auth()->user()->name ?? 'Dewan Juri' }}
                                    </span>
                                </div>
                                <div class="bg-white p-2.5 rounded-xl border border-dashed border-slate-300">
                                    <i class="fa-solid fa-user-check text-slate-400 text-xl"></i>
                                    <div class="font-bold text-slate-800 mt-1 truncate">{{ $schoolName }}</div>
                                    <span class="text-[10px] text-slate-500 font-medium inline-block mt-0.5">
                                        {{ $currentTeam->team_name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi Simpan & Sinkronisasi --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-200">
                        <div class="text-xs text-slate-500">
                            Status: <strong class="text-slate-800" id="calculationFormula">Pilih nilai pada tabel untuk memulai</strong>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button type="submit" onclick="setActionAfter('stay')" class="px-5 py-2.5 rounded-xl bg-gradient-to-r {{ $isWira ? 'from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-slate-950 shadow-amber-500/30' : 'from-blue-600 to-sky-600 hover:from-blue-500 hover:to-sky-500 text-white shadow-blue-600/30' }} font-black text-xs uppercase tracking-wider shadow-md flex items-center gap-2 transition">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Nilai Grid
                            </button>

                            @if($nextTeam)
                                <button type="submit" onclick="setActionAfter('next')" class="px-5 py-2.5 rounded-xl {{ $isWira ? 'bg-slate-900 hover:bg-slate-800 text-amber-300 border border-amber-500/30' : 'bg-sky-600 hover:bg-sky-500 text-white shadow-sky-600/30' }} font-black text-xs uppercase tracking-wider shadow-md flex items-center gap-2 transition">
                                    <span>Simpan & Regu Berikutnya</span> <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            @endif

                            <button type="submit" onclick="setActionAfter('back')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition flex items-center gap-1.5">
                                <i class="fa-solid fa-table-list"></i> Simpan & Ke Skor Utama
                            </button>
                        </div>
                    </div>

                </form>

                {{-- Fasilitas Foto Dokumen Grid, Upload Dokumen, dan View Dokumen Grid --}}
                @include('admin.competition.scores.partials.document-photos', [
                    'team' => $currentTeam,
                    'photos' => $existingPhotos,
                    'theme' => $isWira ? 'wira' : 'madya'
                ])

            @else
                <div class="text-center py-20 text-slate-400">
                    <i class="fa-solid fa-users text-5xl mb-3 text-slate-300"></i>
                    <p class="font-bold text-base text-slate-700">Pilih salah satu regu pada daftar di sebelah kiri untuk mulai menilai.</p>
                </div>
            @endif

        </div>

    </div>

</div>

{{-- Script Interaktif Grid PRS --}}
<script>
    const criteriaKeys = @json($allCriteriaKeys);

    function selectGridScore(criteria, value, buttonElement) {
        // 1. Set input hidden
        const inputEl = document.getElementById('input_' + criteria);
        if (inputEl) {
            inputEl.value = value;
        }

        // 2. Update visual button styles on this row
        const row = document.getElementById('row_' + criteria);
        if (row) {
            const buttons = row.querySelectorAll('.grid-btn');
            buttons.forEach(btn => {
                btn.classList.remove('selected-cell');
                btn.innerText = btn.innerText.replace(' ✓', '').trim();
            });

            buttonElement.classList.add('selected-cell');
            buttonElement.innerText = value + ' ✓';
        }

        // 3. Update kolom skor baris
        const badgeEl = document.getElementById('badge_' + criteria);
        if (badgeEl) {
            badgeEl.innerText = value;
        }

        // 4. Hitung ulang subtotal dan total
        updateTotalScore();
    }

    function updateTimeRecordedString() {
        const min = parseInt(document.getElementById('input_minutes')?.value) || 0;
        const sec = parseInt(document.getElementById('input_seconds')?.value) || 0;
        const formatted = String(min).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
        const hiddenEl = document.getElementById('input_time_recorded');
        if (hiddenEl) {
            hiddenEl.value = formatted;
        }
    }

    function updateTotalScore() {
        let total = 0;
        let filledCount = 0;
        let values = [];

        criteriaKeys.forEach(k => {
            const el = document.getElementById('input_' + k);
            const val = el ? parseInt(el.value) || 0 : 0;
            if (val > 0) {
                total += val;
                values.push(val);
                filledCount++;
            }
        });

        // Update badge total
        const totalBadge = document.getElementById('badge_total_score');
        if (totalBadge) {
            totalBadge.innerText = total.toLocaleString('id-ID');
        }

        // Hitung subtotal per kategori
        @foreach($prsSections as $sec)
            let subtotal_{{ $sec['no'] }} = 0;
            @foreach($sec['items'] as $item)
                const inp_{{ $item['key'] }} = document.getElementById('input_{{ $item['key'] }}');
                subtotal_{{ $sec['no'] }} += inp_{{ $item['key'] }} ? (parseInt(inp_{{ $item['key'] }}.value) || 0) : 0;
            @endforeach
            const subBadge_{{ $sec['no'] }} = document.getElementById('subtotal_sec_{{ $sec['no'] }}');
            if (subBadge_{{ $sec['no'] }}) {
                subBadge_{{ $sec['no'] }}.innerText = subtotal_{{ $sec['no'] }} > 0 ? subtotal_{{ $sec['no'] }} : '-';
            }
        @endforeach

        // Update formula text
        const formulaEl = document.getElementById('calculationFormula');
        if (formulaEl) {
            if (filledCount === criteriaKeys.length) {
                formulaEl.innerHTML = `<span class="text-emerald-700 font-black">Lengkap 11 Item: Total = ${total} Poin</span>`;
            } else if (filledCount > 0) {
                formulaEl.innerHTML = `<span class="{{ $isWira ? 'text-amber-800' : 'text-blue-800' }} font-bold">${filledCount}/11 Item Terpilih (Total Sementara: ${total} Poin)</span>`;
            } else {
                formulaEl.innerText = 'Pilih nilai pada tabel untuk memulai';
            }
        }
    }

    function setActionAfter(action) {
        const actionInput = document.getElementById('actionAfterInput');
        if (actionInput) {
            actionInput.value = action;
        }
        updateTimeRecordedString();
    }

    // Inisialisasi saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', () => {
        updateTimeRecordedString();
        updateTotalScore();
    });
</script>
@endsection
