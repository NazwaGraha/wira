@extends('layouts.admin')

@php
    $isWira = strcasecmp(trim($category->level ?? ''), 'Wira') === 0;
@endphp

@section('title', 'Grid Penilaian: ' . $category->name . ' (' . $category->level . ')')
@section('page_title', 'Grid Penilaian Lomba Pendidikan Remaja Sebaya (PRS) Tingkat ' . ($category->level ?? 'Madya'))

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
        .selected-cell {
            @if($isWira)
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2), 0 4px 10px rgba(225, 29, 72, 0.4);
            border-color: #fda4af !important;
            @else
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2), 0 4px 10px rgba(2, 132, 199, 0.4);
            border-color: #38bdf8 !important;
            @endif
            color: #ffffff !important;
            font-weight: 800 !important;
            transform: scale(1.02);
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
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 {{ $isWira ? 'to-rose-950' : 'to-sky-950' }} p-6 rounded-3xl shadow-xl text-white flex flex-col md:flex-row justify-between md:items-center gap-4 border border-slate-700">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-500 {{ $isWira ? 'to-pink-500' : 'to-amber-400' }} p-0.5 shadow-lg flex items-center justify-center">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <i class="fa-solid fa-heart-pulse text-rose-500 text-2xl"></i>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        {{ $category->code ?? ($isWira ? 'PRS-WIRA' : 'PRS-MADYA') }}
                    </span>
                    <span class="text-xs text-slate-300 font-bold">PMR {{ $category->level }} &bull; Kategori {{ $category->gender_category }}</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    {{ $category->name }}
                </h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    1-Click Touch Grid Penilaian resmi sesuai format juri &bull; Total nilai terakumulasi otomatis ke Lembar Skor Utama.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="px-4 py-2.5 rounded-2xl {{ $isWira ? 'bg-rose-600 hover:bg-rose-500' : 'bg-sky-600 hover:bg-sky-500' }} text-white font-bold text-xs transition flex items-center gap-2 shadow-md">
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
                    <i class="fa-solid fa-users-line {{ $isWira ? 'text-rose-600' : 'text-sky-600' }}"></i>
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
                       class="block p-3 rounded-2xl border transition relative {{ $isActive ? ($isWira ? 'bg-rose-50/90 border-rose-400 shadow-sm ring-2 ring-rose-400/20' : 'bg-sky-50/90 border-sky-400 shadow-sm ring-2 ring-sky-400/20') : 'bg-slate-50 hover:bg-white border-slate-200' }}">
                        
                        <div class="flex items-center justify-between gap-2">
                            <span class="w-6 h-6 rounded-full text-[11px] font-black flex items-center justify-center {{ $isActive ? ($isWira ? 'bg-rose-600 text-white' : 'bg-sky-600 text-white') : 'bg-slate-200 text-slate-700' }}">
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
                    
                    // Ambil nilai per kriteria yang sudah tersimpan
                    $vKreativitas = $rubricData['kreativitas'] ?? null;
                    $vTema = $rubricData['kesesuaian_tema'] ?? null;
                    $vBahasa = $rubricData['kejelasan_bahasa'] ?? null;
                    $vPresentasi = $rubricData['presentasi'] ?? null;
                    $vKecepatan = $rubricData['kecepatan_menjawab'] ?? null;
                @endphp

                <form action="{{ route('admin.competition-scores.grid-save', $category->id) }}" method="POST" id="formGridPRS">
                    @csrf
                    <input type="hidden" name="competition_participant_team_id" value="{{ $currentTeam->id }}">
                    <input type="hidden" name="round_name" value="{{ $round }}">
                    <input type="hidden" name="action_after" id="actionAfterInput" value="stay">
                    <input type="hidden" name="next_team_id" value="{{ $nextTeam?->id }}">

                    {{-- Header Kertas Penilaian --}}
                    <div class="text-center border-b-2 border-slate-800 pb-4 mb-5">
                        <div class="inline-flex items-center gap-1.5 text-rose-600 font-black text-xs uppercase tracking-widest mb-1">
                            <i class="fa-solid fa-circle-check"></i> Form Penilaian Juri Resmi
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            GRID PENILAIAN LOMBA PENDIDIKAN REMAJA SEBAYA
                        </h2>
                        <div class="text-xs sm:text-sm font-bold text-slate-700 uppercase tracking-widest mt-0.5">
                            TINGKAT {{ strtoupper($category->level ?? 'MADYA') }}
                        </div>
                    </div>

                    {{-- Metadata Peserta --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-5">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">NAMA SEKOLAH :</span>
                            <span class="text-sm sm:text-base font-black text-slate-900 flex items-center gap-2 mt-0.5">
                                <i class="fa-solid fa-school text-sky-600"></i> {{ $schoolName }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">NO. URUT PESERTA :</span>
                            <span class="text-sm sm:text-base font-black text-slate-900 flex items-center gap-2 mt-0.5">
                                <i class="fa-solid fa-id-card-clip text-rose-600"></i> {{ $teamCode }} &bull; {{ $currentTeam->team_name }}
                            </span>
                        </div>
                    </div>

                    {{-- Petunjuk Pengisian --}}
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-600 bg-amber-50 border border-amber-200 px-4 py-2 rounded-xl mb-4">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-hand-pointer text-amber-600"></i>
                            <strong>Petunjuk:</strong> Klik salah satu angka nilai pada setiap baris kriteria untuk mengakumulasikannya ke total skor.
                        </span>
                        <span class="text-[10px] font-bold text-amber-900 bg-amber-200/70 px-2 py-0.5 rounded">
                            Skor Maks: 1.000 Poin
                        </span>
                    </div>

                    {{-- Hidden Inputs Rubric --}}
                    <input type="hidden" name="rubric[kreativitas]" id="input_kreativitas" value="{{ $vKreativitas }}">
                    <input type="hidden" name="rubric[kesesuaian_tema]" id="input_kesesuaian_tema" value="{{ $vTema }}">
                    <input type="hidden" name="rubric[kejelasan_bahasa]" id="input_kejelasan_bahasa" value="{{ $vBahasa }}">
                    <input type="hidden" name="rubric[presentasi]" id="input_presentasi" value="{{ $vPresentasi }}">
                    <input type="hidden" name="rubric[kecepatan_menjawab]" id="input_kecepatan_menjawab" value="{{ $vKecepatan }}">

                    {{-- Tabel 5 Kriteria Grid PRS (Persis Lembar Asli User) --}}
                    <div class="rounded-2xl border-2 border-slate-800 mb-5 overflow-hidden shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-center border-collapse min-w-[700px]">
                                <thead>
                                    <tr class="bg-slate-900 text-white text-xs uppercase font-extrabold tracking-wider">
                                        <th class="py-3 px-3 text-left w-52 border-r border-slate-700">KRITERIA PENILAIAN</th>
                                        <th colspan="3" class="py-2.5 px-1 bg-amber-600 border-r border-amber-500/50">CUKUP</th>
                                        <th colspan="3" class="py-2.5 px-1 bg-sky-600 border-r border-sky-500/50">BAIK</th>
                                        <th colspan="3" class="py-2.5 px-1 bg-emerald-600 border-r border-emerald-500/50">SANGAT BAIK</th>
                                        <th class="py-3 px-3 w-28 bg-slate-950">SKOR</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs divide-y divide-slate-200 font-semibold text-slate-800">
                                    
                                    {{-- Row 1: KREATIVITAS --}}
                                    @php
                                        $optsKreativitas = [
                                            'cukup' => [120, 130, 140],
                                            'baik' => [150, 160, 170],
                                            'sangat_baik' => [180, 190, 200]
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition" id="row_kreativitas">
                                        <td class="py-2.5 px-3 text-left font-black text-slate-900 border-r border-slate-300 bg-slate-50">
                                            1. KREATIVITAS
                                        </td>
                                        @foreach($optsKreativitas['cukup'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kreativitas', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-slate-700 font-bold border border-amber-200 transition {{ $vKreativitas == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vKreativitas == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsKreativitas['baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kreativitas', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-slate-700 font-bold border border-sky-200 transition {{ $vKreativitas == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vKreativitas == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsKreativitas['sangat_baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kreativitas', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-slate-700 font-bold border border-emerald-200 transition {{ $vKreativitas == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vKreativitas == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        <td class="py-2 px-2 bg-sky-50/80 font-mono font-black text-sm text-sky-700" id="badge_kreativitas">
                                            {{ $vKreativitas ?? '-' }}
                                        </td>
                                    </tr>

                                    {{-- Row 2: KESESUAIAN TEMA --}}
                                    @php
                                        $optsTema = [
                                            'cukup' => [120, 130, 140],
                                            'baik' => [150, 160, 170],
                                            'sangat_baik' => [180, 190, 200]
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition" id="row_kesesuaian_tema">
                                        <td class="py-2.5 px-3 text-left font-black text-slate-900 border-r border-slate-300 bg-slate-50">
                                            2. KESESUAIAN TEMA
                                        </td>
                                        @foreach($optsTema['cukup'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kesesuaian_tema', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-slate-700 font-bold border border-amber-200 transition {{ $vTema == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vTema == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsTema['baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kesesuaian_tema', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-slate-700 font-bold border border-sky-200 transition {{ $vTema == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vTema == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsTema['sangat_baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kesesuaian_tema', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-slate-700 font-bold border border-emerald-200 transition {{ $vTema == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vTema == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        <td class="py-2 px-2 bg-sky-50/80 font-mono font-black text-sm text-sky-700" id="badge_kesesuaian_tema">
                                            {{ $vTema ?? '-' }}
                                        </td>
                                    </tr>

                                    {{-- Row 3: KEJELASAN BAHASA --}}
                                    @php
                                        $optsBahasa = [
                                            'cukup' => [60, 65, 70],
                                            'baik' => [75, 80, 85],
                                            'sangat_baik' => [90, 95, 100]
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition" id="row_kejelasan_bahasa">
                                        <td class="py-2.5 px-3 text-left font-black text-slate-900 border-r border-slate-300 bg-slate-50">
                                            3. KEJELASAN BAHASA
                                        </td>
                                        @foreach($optsBahasa['cukup'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kejelasan_bahasa', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-slate-700 font-bold border border-amber-200 transition {{ $vBahasa == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vBahasa == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsBahasa['baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kejelasan_bahasa', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-slate-700 font-bold border border-sky-200 transition {{ $vBahasa == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vBahasa == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsBahasa['sangat_baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kejelasan_bahasa', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-slate-700 font-bold border border-emerald-200 transition {{ $vBahasa == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vBahasa == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        <td class="py-2 px-2 bg-sky-50/80 font-mono font-black text-sm text-sky-700" id="badge_kejelasan_bahasa">
                                            {{ $vBahasa ?? '-' }}
                                        </td>
                                    </tr>

                                    {{-- Row 4: PRESENTASI --}}
                                    @php
                                        $optsPresentasi = [
                                            'cukup' => [240, 260, 280],
                                            'baik' => [300, 320, 340],
                                            'sangat_baik' => [360, 380, 400]
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition" id="row_presentasi">
                                        <td class="py-2.5 px-3 text-left font-black text-slate-900 border-r border-slate-300 bg-slate-50">
                                            4. PRESENTASI
                                        </td>
                                        @foreach($optsPresentasi['cukup'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('presentasi', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-slate-700 font-bold border border-amber-200 transition {{ $vPresentasi == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vPresentasi == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsPresentasi['baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('presentasi', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-slate-700 font-bold border border-sky-200 transition {{ $vPresentasi == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vPresentasi == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsPresentasi['sangat_baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('presentasi', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-slate-700 font-bold border border-emerald-200 transition {{ $vPresentasi == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vPresentasi == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        <td class="py-2 px-2 bg-sky-50/80 font-mono font-black text-sm text-sky-700" id="badge_presentasi">
                                            {{ $vPresentasi ?? '-' }}
                                        </td>
                                    </tr>

                                    {{-- Row 5: KECEPATAN MENJAWAB --}}
                                    @php
                                        $optsKecepatan = [
                                            'cukup' => [60, 65, 70],
                                            'baik' => [75, 80, 85],
                                            'sangat_baik' => [90, 95, 100]
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition" id="row_kecepatan_menjawab">
                                        <td class="py-2.5 px-3 text-left font-black text-slate-900 border-r border-slate-300 bg-slate-50">
                                            5. KECEPATAN MENJAWAB
                                        </td>
                                        @foreach($optsKecepatan['cukup'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kecepatan_menjawab', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-slate-700 font-bold border border-amber-200 transition {{ $vKecepatan == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vKecepatan == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsKecepatan['baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kecepatan_menjawab', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-sky-50 hover:bg-sky-100 text-slate-700 font-bold border border-sky-200 transition {{ $vKecepatan == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vKecepatan == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        @foreach($optsKecepatan['sangat_baik'] as $val)
                                            <td class="p-1 border-r border-slate-200">
                                                <button type="button" onclick="selectGridScore('kecepatan_menjawab', {{ $val }}, this)" class="grid-btn w-full py-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-slate-700 font-bold border border-emerald-200 transition {{ $vKecepatan == $val ? 'selected-cell' : '' }}">
                                                    {{ $val }}{{ $vKecepatan == $val ? ' ✓' : '' }}
                                                </button>
                                            </td>
                                        @endforeach
                                        <td class="py-2 px-2 bg-sky-50/80 font-mono font-black text-sm text-sky-700" id="badge_kecepatan_menjawab">
                                            {{ $vKecepatan ?? '-' }}
                                        </td>
                                    </tr>

                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-900 text-white font-black text-sm border-t-2 border-slate-800">
                                        <td colspan="10" class="py-3 px-4 text-right tracking-wider uppercase text-slate-300 text-xs">
                                            TOTAL NILAI AKUMULASI GRID:
                                        </td>
                                        <td class="py-3 px-2 bg-gradient-to-r {{ $isWira ? 'from-rose-600 to-red-600' : 'from-emerald-600 to-teal-600' }} text-white font-mono text-xl tracking-tight" id="badge_total_score">
                                            {{ number_format((float)($score->final_score ?? 0), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Pesan dari Dewan Juri & Tanda Tangan Verifikasi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="border border-slate-300 rounded-2xl p-4 bg-slate-50">
                            <label class="text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-comment-dots text-slate-500"></i> PESAN DARI DEWAN JURI :
                            </label>
                            <textarea name="notes" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 leading-relaxed focus:outline-none focus:ring-2 {{ $isWira ? 'focus:ring-rose-500' : 'focus:ring-sky-500' }}" rows="3" placeholder="Tuliskan catatan, evaluasi materi, atau masukan untuk regu peserta ini...">{{ $existingNotes }}</textarea>
                        </div>

                        <div class="border border-slate-300 rounded-2xl p-4 bg-slate-50 flex flex-col justify-between">
                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 text-center block mb-2">
                                Tanda Tangan / Verifikasi Digital
                            </span>
                            <div class="grid grid-cols-2 gap-3 text-center text-xs">
                                <div class="bg-white p-2.5 rounded-xl border border-dashed border-slate-300">
                                    <i class="fa-solid fa-signature text-slate-400 text-xl"></i>
                                    <div class="font-bold text-slate-800 mt-1">Dewan Juri</div>
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
                            <button type="submit" onclick="setActionAfter('stay')" class="px-5 py-2.5 rounded-xl bg-gradient-to-r {{ $isWira ? 'from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 shadow-rose-600/30' : 'from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-emerald-600/30' }} text-white font-black text-xs uppercase tracking-wider shadow-md flex items-center gap-2 transition">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Nilai Grid
                            </button>

                            @if($nextTeam)
                                <button type="submit" onclick="setActionAfter('next')" class="px-5 py-2.5 rounded-xl {{ $isWira ? 'bg-slate-800 hover:bg-slate-700' : 'bg-sky-600 hover:bg-sky-500 shadow-sky-600/30' }} text-white font-black text-xs uppercase tracking-wider shadow-md flex items-center gap-2 transition">
                                    <span>Simpan & Regu Berikutnya</span> <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            @endif

                            <button type="submit" onclick="setActionAfter('back')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition flex items-center gap-1.5">
                                <i class="fa-solid fa-table-list"></i> Simpan & Ke Skor Utama
                            </button>
                        </div>
                    </div>

                </form>

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
    function selectGridScore(criteria, value, buttonElement) {
        // 1. Set input hidden
        const inputEl = document.getElementById('input_' + criteria);
        if (inputEl) {
            inputEl.value = value;
        }

        // 2. Update visual buttons on this row
        const row = document.getElementById('row_' + criteria);
        if (row) {
            const buttons = row.querySelectorAll('.grid-btn');
            buttons.forEach(btn => {
                btn.classList.remove('selected-cell');
                // Hapus tanda ceklis jika ada
                btn.innerText = btn.innerText.replace(' ✓', '').trim();
            });

            // Tambahkan class aktif ke tombol yang diklik
            buttonElement.classList.add('selected-cell');
            buttonElement.innerText = value + ' ✓';
        }

        // 3. Update kolom skor baris
        const badgeEl = document.getElementById('badge_' + criteria);
        if (badgeEl) {
            badgeEl.innerText = value;
        }

        // 4. Hitung ulang total
        updateTotalScore();
    }

    function updateTotalScore() {
        const criteriaList = ['kreativitas', 'kesesuaian_tema', 'kejelasan_bahasa', 'presentasi', 'kecepatan_menjawab'];
        let total = 0;
        let values = [];
        let filledCount = 0;

        criteriaList.forEach(c => {
            const el = document.getElementById('input_' + c);
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

        // Update formula text
        const formulaEl = document.getElementById('calculationFormula');
        if (formulaEl) {
            if (filledCount === 5) {
                formulaEl.innerHTML = `<span class="text-emerald-700 font-black">Lengkap: ${values.join(' + ')} = ${total} Poin</span>`;
            } else if (filledCount > 0) {
                formulaEl.innerHTML = `<span class="text-amber-700 font-bold">${filledCount}/5 Kriteria Dipilih (${values.join(' + ')} = ${total})</span>`;
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
    }

    // Inisialisasi saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', () => {
        updateTotalScore();
    });
</script>
@endsection
