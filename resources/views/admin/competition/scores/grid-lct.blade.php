@extends('layouts.admin')

@section('title', 'Grid Nilai LCT - ' . $category->name . ' (' . $category->level . ')')
@section('page_title', 'Grid Nilai Lomba Cepat Tepat (LCT)')

@section('top_actions')
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.competition-scores.index', ['level' => $category->level]) }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-3.5 py-2 rounded-xl font-bold text-xs shadow-2xs transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('admin.competition-scores.lct-live', ['category' => $category->id, 'stage' => $activeStage]) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-sm transition flex items-center gap-1.5">
            <i class="fa-solid fa-desktop"></i> Layar Panggung / Proyektor
        </a>
        <form action="{{ route('admin.competition-scores.lct-seed-simulation', $category->id) }}" method="POST" onsubmit="return confirm('Muat data simulasi 20 regu LCT sesuai contoh Excel panitia?')">
            @csrf
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-3.5 py-2 rounded-xl font-black text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Muat Data Simulasi Sheet
            </button>
        </form>
        <a href="{{ route('admin.competition-scores.export-excel', $category->id) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-sm transition flex items-center gap-1.5">
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

    <!-- Header Card & Stage Stepper Navigation -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1.5">
                    <span class="px-3 py-1 rounded-xl bg-red-100 text-red-800 border border-red-300 font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-bolt text-red-600"></i> {{ $category->code ?? 'LCT' }}
                    </span>
                    <span class="px-3 py-1 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 font-extrabold text-xs">
                        PMR Tingkat {{ $category->level }}
                    </span>
                    <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs">
                        {{ $allTeams->count() }} Regu Terdaftar
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Sistem Penilaian Lomba Cepat Tepat (3 Tahap Resmi)
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Alur penilaian otomatis: Penyisihan (Top 9) &rarr; Semi-Final (3 Termin Anti-Bentrokan Sekolah) &rarr; Grand Final & Juara Harapan.
                </p>
            </div>

            <!-- Quick Stage Switcher Pills -->
            <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl shrink-0">
                <a href="{{ route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 1]) }}"
                   class="px-4 py-2 rounded-xl font-black text-xs transition flex items-center gap-2 {{ $activeStage === 1 ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Sesi 1: Penyisihan</span>
                </a>
                <a href="{{ route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 2]) }}"
                   class="px-4 py-2 rounded-xl font-black text-xs transition flex items-center gap-2 {{ $activeStage === 2 ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-diagram-project"></i>
                    <span>Sesi 2: Semi-Final</span>
                </a>
                <a href="{{ route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 3]) }}"
                   class="px-4 py-2 rounded-xl font-black text-xs transition flex items-center gap-2 {{ $activeStage === 3 ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-trophy"></i>
                    <span>Sesi 3: Grand Final</span>
                </a>
            </div>
        </div>

        <!-- 3-Stage Progress Stepper Bar -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Step 1 Card -->
            <a href="{{ route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 1]) }}"
               class="p-4 rounded-2xl border-2 transition text-left flex items-start gap-3.5 {{ $activeStage === 1 ? 'bg-red-50/60 border-red-500 shadow-2xs' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $activeStage === 1 ? 'bg-red-600 text-white shadow-xs' : 'bg-slate-200 text-slate-700' }}">
                    1
                </div>
                <div>
                    <div class="font-black text-xs text-slate-900 uppercase tracking-wider">Tahap 1: Penyisihan</div>
                    <div class="text-[11px] text-slate-600 font-semibold mt-0.5">Saring 9 Regu Terbaik (Nilai & Waktu)</div>
                    <div class="text-[10px] text-slate-400 mt-1">
                        @if($top9Teams->count() >= 9)
                            <span class="text-emerald-700 font-bold"><i class="fa-solid fa-circle-check"></i> 9 Regu Siap Lolos</span>
                        @else
                            <span class="text-amber-700 font-bold"><i class="fa-solid fa-clock"></i> {{ $top9Teams->count() }} Regu Terdata</span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- Step 2 Card -->
            <a href="{{ route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 2]) }}"
               class="p-4 rounded-2xl border-2 transition text-left flex items-start gap-3.5 {{ $activeStage === 2 ? 'bg-indigo-50/60 border-indigo-500 shadow-2xs' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $activeStage === 2 ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-200 text-slate-700' }}">
                    2
                </div>
                <div>
                    <div class="font-black text-xs text-slate-900 uppercase tracking-wider">Tahap 2: Semi-Final</div>
                    <div class="text-[11px] text-slate-600 font-semibold mt-0.5">3 Termin @ 3 Tim (Anti-Satu Sekolah)</div>
                    <div class="text-[10px] text-slate-400 mt-1">
                        @if($hasSemiResults)
                            <span class="text-emerald-700 font-bold"><i class="fa-solid fa-circle-check"></i> Juara Termin & Harapan Terpilih</span>
                        @else
                            <span class="text-slate-500">Amplop &bull; Rebutan &bull; Balon</span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- Step 3 Card -->
            <a href="{{ route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 3]) }}"
               class="p-4 rounded-2xl border-2 transition text-left flex items-start gap-3.5 {{ $activeStage === 3 ? 'bg-amber-50/60 border-amber-500 shadow-2xs' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 {{ $activeStage === 3 ? 'bg-amber-500 text-slate-950 shadow-xs' : 'bg-slate-200 text-slate-700' }}">
                    3
                </div>
                <div>
                    <div class="font-black text-xs text-slate-900 uppercase tracking-wider">Tahap 3: Grand Final</div>
                    <div class="text-[11px] text-slate-600 font-semibold mt-0.5">Perebutan Juara 1, 2, dan 3</div>
                    <div class="text-[10px] text-slate-400 mt-1">
                        @if($hasFinalResults)
                            <span class="text-amber-800 font-bold"><i class="fa-solid fa-trophy"></i> Juara LCT Resmi Ditetapkan</span>
                        @else
                            <span class="text-slate-500">Rebutan Bertingkat &amp; Taruh Nilai</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    </div>


    {{-- ========================================================================= --}}
    {{-- STAGE 1: TAHAP PENYISIHAN                                                 --}}
    {{-- ========================================================================= --}}
    @if($activeStage === 1)
        <div class="space-y-6">
            <!-- Stage 1 Top Instruction & Smart Bracket Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 shadow-md border border-indigo-900/50 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-slate-950">
                            Sesi 1 (Tahap Penyisihan)
                        </span>
                        <span class="text-xs font-bold text-indigo-200">
                            Penyaringan 9 Regu Terbaik Lolos Semi-Final
                        </span>
                    </div>
                    <h2 class="text-lg font-black text-white">
                        Klasemen Nilai & Waktu Tempuh Babak Penyisihan
                    </h2>
                    <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">
                        Pada babak ini <strong>belum ditentukan juara</strong>. Sistem secara otomatis menyaring <strong>hingga 9 regu terbaik</strong> (atau seluruh peserta jika &le; 9 regu). Jika terdapat nilai yang sama, <strong>Waktu Tempuh ([MM]:SS)</strong> menjadi penentu peringkat secara instan.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0 flex-wrap">
                    <form action="{{ route('admin.competition-scores.lct-generate-semifinal', $category->id) }}" method="POST" onsubmit="return confirm('Generate bagan semifinal sekarang? 9 tim teratas akan didistribusikan ke 3 Termin dengan algoritma anti-bentrok satu sekolah.')">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-diagram-project"></i>
                            <span>Generate Bagan Semifinal (Anti-Bentrokan)</span>
                        </button>
                    </form>

                    <form action="{{ route('admin.competition-scores.lct-reset-stage', $category->id) }}" method="POST" onsubmit="return confirm('Reset seluruh nilai babak penyisihan?')">
                        @csrf
                        <input type="hidden" name="stage" value="prelim">
                        <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition flex items-center gap-1.5 cursor-pointer" title="Reset Nilai Penyisihan">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                    </form>
                </div>
            </div>

            <!-- Smart Bracket Info Callout -->
            <div class="bg-amber-50 border-2 border-amber-300 text-amber-950 p-4.5 rounded-2xl flex items-start gap-3.5 shadow-2xs">
                <i class="fa-solid fa-shield-halved text-2xl text-amber-600 shrink-0 mt-0.5"></i>
                <div class="text-xs space-y-1">
                    <div class="font-black text-sm uppercase tracking-wide text-amber-950">
                        Mekanisme Cerdas: Pemisahan Regu dari Pangkalan Sekolah yang Sama
                    </div>
                    <p class="text-amber-800 leading-relaxed font-medium">
                        Sistem mendeteksi dan mencegah protes peserta! Jika ada sekolah yang meloloskan lebih dari 1 regu ke babak semi-final (contoh: <em>SMAN 5 Bandung A &amp; B</em>, atau <em>SMAN 1 Cikarang Utara A &amp; B</em>), sistem otomatis mendistribusikan mereka ke <strong>termin yang berbeda</strong> agar tidak saling mengeliminasi di babak semi-final.
                    </p>
                </div>
            </div>

            <!-- Form Tabel Nilai Penyisihan -->
            <form action="{{ route('admin.competition-scores.lct-save-prelim', $category->id) }}" method="POST">
                @csrf
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-600 inline-block animate-ping"></span>
                            <span class="font-black text-xs sm:text-sm text-slate-900">
                                Lembar Nilai Penyisihan: Masukkan Nilai/Poin &amp; Waktu Tempuh
                            </span>
                        </div>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Nilai Penyisihan</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                                    <th class="py-3.5 px-4 w-16 text-center border-r border-slate-800">NO URUT</th>
                                    <th class="py-3.5 px-5 border-r border-slate-800">NAMA SEKOLAH / REGU</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-40">NILAI / POINT</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-44">WAKTU / TEMPUH ([MM]:SS)</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-36">NILAI AKHIR</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-28">RANKING</th>
                                    <th class="py-3.5 px-4 text-center w-36 bg-black">STATUS KELOLOSAN</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-medium">
                                @forelse($sortedPrelim as $idx => $t)
                                    @php
                                        $isQualified = $t->is_top_9 ?? false;
                                        $rank = $t->prelim_rank;
                                        if ($rank === 1) {
                                            $rowBg = 'border-l-4 border-l-amber-500 bg-amber-50/50 hover:bg-amber-50';
                                            $badgeClass = 'bg-gradient-to-tr from-amber-500 via-amber-400 to-yellow-300 text-slate-950 ring-2 ring-amber-300 shadow-sm font-black';
                                        } elseif ($rank === 2) {
                                            $rowBg = 'border-l-4 border-l-slate-400 bg-slate-100/70 hover:bg-slate-100';
                                            $badgeClass = 'bg-gradient-to-tr from-slate-400 via-slate-300 to-slate-200 text-slate-900 ring-2 ring-slate-300 shadow-sm font-black';
                                        } elseif ($rank === 3) {
                                            $rowBg = 'border-l-4 border-l-amber-700 bg-amber-50/40 hover:bg-amber-50';
                                            $badgeClass = 'bg-gradient-to-tr from-amber-700 via-amber-600 to-amber-500 text-white ring-2 ring-amber-600 shadow-sm font-black';
                                        } elseif ($isQualified) {
                                            $rowBg = 'border-l-4 border-l-emerald-500 bg-emerald-50/40 hover:bg-emerald-50';
                                            $badgeClass = 'bg-gradient-to-tr from-emerald-600 to-teal-500 text-white ring-1 ring-emerald-400 shadow-2xs font-bold';
                                        } else {
                                            $rowBg = 'border-l-4 border-l-transparent hover:bg-slate-50';
                                            $badgeClass = 'bg-slate-200 text-slate-700 font-bold';
                                        }
                                    @endphp
                                    <tr class="{{ $rowBg }} transition" id="row-team-{{ $t->id }}">
                                        <td class="py-3 px-4 text-center font-mono font-black text-slate-800">
                                            {{ $t->order_number ?? ($idx + 1) }}
                                        </td>
                                        <td class="py-3 px-5">
                                            <div class="font-extrabold text-slate-900 text-sm">
                                                {{ $t->team_name ?? ($t->registration?->school_name ?? 'Regu ' . ($idx + 1)) }}
                                            </div>
                                            @if($t->team_label)
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 mt-0.5">
                                                    {{ $t->team_label }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="number" step="any" name="prelim_scores[{{ $t->id }}]" value="{{ $t->prelim_score }}" placeholder="0" class="prelim-score-input w-28 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-red-500 focus:outline-none" data-team-id="{{ $t->id }}">
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="text" name="prelim_times[{{ $t->id }}]" value="{{ $t->prelim_time }}" placeholder="07:54" class="prelim-time-input w-28 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-red-500 focus:outline-none" title="Format [MM]:SS atau MM:SS" data-team-id="{{ $t->id }}">
                                        </td>
                                        <td class="py-3 px-4 text-center font-mono font-black text-sm text-slate-900" id="display-prelim-score-{{ $t->id }}">
                                            {{ $t->prelim_score !== null ? number_format($t->prelim_score, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-center font-mono text-sm" id="display-prelim-rank-{{ $t->id }}">
                                            @if($t->prelim_score !== null)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full leading-8 text-center {{ $badgeClass }}">
                                                    @if($rank === 1)
                                                        <i class="fa-solid fa-crown text-[9px] mr-0.5"></i>
                                                    @endif
                                                    {{ $t->prelim_rank }}
                                                </span>
                                            @else
                                                <span class="text-slate-300 font-mono font-bold">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center font-bold" id="display-prelim-status-{{ $t->id }}">
                                            @if($t->prelim_score === null)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-400 text-[10px] font-semibold">
                                                    <i class="fa-regular fa-clock"></i> Belum Dinilai
                                                </span>
                                            @elseif($isQualified)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-black uppercase tracking-wider shadow-2xs">
                                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Lolos Semi-Final
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 text-[10px] font-bold">
                                                    <i class="fa-solid fa-circle-xmark text-rose-500"></i> Gugur
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400 font-bold">
                                            Belum ada peserta terdaftar pada kategori LCT ini. Klik tombol "Muat Data Simulasi Sheet" di atas untuk mencoba.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2.5 flex-wrap text-xs">
                            <span class="text-slate-500 font-extrabold uppercase text-[10px] tracking-wider">Keterangan Warna Ranking:</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-100 text-amber-950 border border-amber-300 font-extrabold text-[11px] shadow-2xs">
                                <span class="w-3.5 h-3.5 rounded-full bg-gradient-to-tr from-amber-500 to-yellow-300 ring-1 ring-amber-400 inline-flex items-center justify-center text-[8px]">👑</span>
                                Peringkat 1 (Emas)
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-200 text-slate-800 border border-slate-300 font-extrabold text-[11px] shadow-2xs">
                                <span class="w-3.5 h-3.5 rounded-full bg-gradient-to-tr from-slate-400 to-slate-200 ring-1 ring-slate-300 inline-block"></span>
                                Peringkat 2 (Perak)
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-900/10 text-amber-800 border border-amber-800/30 font-extrabold text-[11px] shadow-2xs">
                                <span class="w-3.5 h-3.5 rounded-full bg-gradient-to-tr from-amber-700 to-amber-600 ring-1 ring-amber-500 inline-block"></span>
                                Peringkat 3 (Perunggu)
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 font-extrabold text-[11px] shadow-2xs">
                                <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 ring-1 ring-emerald-400 inline-block"></span>
                                Peringkat 4 - 9 (Lolos Semi-Final)
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 border border-slate-300 font-semibold text-[11px]">
                                <span class="w-3.5 h-3.5 rounded-full bg-slate-400 inline-block"></span>
                                Peringkat 10+ (Gugur)
                            </span>
                        </div>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer shrink-0">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Nilai Penyisihan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- STAGE 2: TAHAP SEMI-FINAL (3 TERMIN)                                      --}}
    {{-- ========================================================================= --}}
    @if($activeStage === 2)
        <div class="space-y-6">
            <!-- Stage 2 Top Instruction Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 shadow-md border border-indigo-900/50 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-500 text-white">
                            Sesi 2 (Semi-Final)
                        </span>
                        <span class="text-xs font-bold text-indigo-200">
                            3 Termin Pertandingan &bull; Juara 1 Masing-Masing Termin Lolos ke Final
                        </span>
                    </div>
                    <h2 class="text-lg font-black text-white">
                        Penilaian Meja Semifinal: Soal Amplop, Rebutan, dan Balon
                    </h2>
                    <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">
                        Masing-masing termin bertanding 3 regu. Pemenang (Juara 1) tiap termin otomatis memegang tiket ke <strong>Babak Final</strong>. Sedangkan <strong>Juara Harapan 1 dan 2</strong> ditentukan dari nilai tertinggi seluruh peserta semi-final non-finalis.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0 flex-wrap">
                    <a href="{{ route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 3]) }}" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2">
                        <span>Lanjut ke Grand Final</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                    <form action="{{ route('admin.competition-scores.lct-reset-stage', $category->id) }}" method="POST" onsubmit="return confirm('Reset seluruh nilai babak semifinal?')">
                        @csrf
                        <input type="hidden" name="stage" value="semifinal">
                        <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition flex items-center gap-1.5 cursor-pointer" title="Reset Nilai Semifinal">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form Simpan Nilai Semifinal (3 Termin) -->
            <form action="{{ route('admin.competition-scores.lct-save-semifinal', $category->id) }}" method="POST">
                @csrf
                <div class="space-y-6">

                    <!-- Termin 1, 2, 3 Grid -->
                    @php
                        $terminConfigs = [
                            1 => ['title' => 'Termin 1 (Sesi Pagi / Meja 1)', 'teams' => $termin1Teams, 'color' => 'border-amber-400 bg-amber-50/20', 'headerBg' => 'bg-amber-400 text-slate-950', 'tiket' => 'Tiket Final A'],
                            2 => ['title' => 'Termin 2 (Sesi Siang / Meja 2)', 'teams' => $termin2Teams, 'color' => 'border-rose-400 bg-rose-50/20', 'headerBg' => 'bg-rose-500 text-white', 'tiket' => 'Tiket Final B'],
                            3 => ['title' => 'Termin 3 (Sesi Sore / Meja 3)', 'teams' => $termin3Teams, 'color' => 'border-emerald-400 bg-emerald-50/20', 'headerBg' => 'bg-emerald-600 text-white', 'tiket' => 'Tiket Final C'],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 gap-6">
                        @foreach($terminConfigs as $tNum => $conf)
                            <div class="bg-white rounded-3xl shadow-sm border {{ $conf['color'] }} overflow-hidden">
                                <!-- Termin Header -->
                                <div class="p-4 {{ $conf['headerBg'] }} flex items-center justify-between gap-3 flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-black/20 flex items-center justify-center font-black text-xs">
                                            T{{ $tNum }}
                                        </span>
                                        <span class="font-black text-sm uppercase tracking-wider">
                                            {{ $conf['title'] }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-wider px-2.5 py-1 rounded-lg bg-white/20">
                                        Juara 1 Lolos &rarr; {{ $conf['tiket'] }}
                                    </span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                                                <th class="py-3 px-4 w-16 text-center border-r border-slate-800">NO</th>
                                                <th class="py-3 px-5 border-r border-slate-800">NAMA SEKOLAH / REGU</th>
                                                <th class="py-3 px-4 text-center border-r border-slate-800 w-44">TOTAL NILAI SOAL AMPLOP</th>
                                                <th class="py-3 px-4 text-center border-r border-slate-800 w-48">TOTAL NILAI SOAL REBUTAN</th>
                                                <th class="py-3 px-4 text-center border-r border-slate-800 w-40">TOTAL NILAI BALON</th>
                                                <th class="py-3 px-4 text-center border-r border-slate-800 w-36 bg-slate-950">POIN TOTAL</th>
                                                <th class="py-3 px-4 text-center w-44 bg-black">RANKING / STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 font-medium">
                                            @forelse($conf['teams'] as $tIdx => $t)
                                                @php
                                                    $isWinner = ($t->termin_rank == 1);
                                                    $rowBg = $isWinner ? 'bg-emerald-50/70 font-bold border-l-4 border-l-emerald-500' : 'hover:bg-slate-50 border-l-4 border-l-transparent';
                                                @endphp
                                                <tr class="{{ $rowBg }} transition" id="row-semi-{{ $t->id }}" data-termin="{{ $tNum }}">
                                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800">
                                                        <input type="hidden" name="semi_termin[{{ $t->id }}]" value="{{ $tNum }}">
                                                        {{ $tIdx + 1 }}
                                                    </td>
                                                    <td class="py-3.5 px-5">
                                                        <div class="font-black text-slate-900 text-sm">
                                                            {{ $t->team_name ?? ($t->registration?->school_name ?? 'Regu ' . ($tIdx + 1)) }}
                                                        </div>
                                                        <div class="text-[11px] text-slate-500 font-medium">
                                                            Pangkalan: {{ $t->registration?->school_name ?? '-' }}
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center">
                                                        <input type="number" step="any" name="semi_amplop[{{ $t->id }}]" id="inp-amplop-{{ $t->id }}" value="{{ $t->soal_amplop ?? 0 }}" oninput="calcSemiRow({{ $t->id }}, {{ $tNum }})" class="w-24 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-indigo-500" data-termin="{{ $tNum }}">
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <input type="number" step="any" name="semi_rebutan[{{ $t->id }}]" id="inp-rebutan-{{ $t->id }}" value="{{ $t->rebutan ?? 0 }}" oninput="calcSemiRow({{ $t->id }}, {{ $tNum }})" class="w-24 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-indigo-500" data-termin="{{ $tNum }}">
                                                            <!-- Quick Add Buttons -->
                                                            <div class="flex flex-col gap-0.5">
                                                                <button type="button" onclick="adjustInput('inp-rebutan-{{ $t->id }}', 100, {{ $t->id }}, {{ $tNum }})" class="px-1.5 py-0.5 text-[9px] font-black rounded bg-emerald-100 text-emerald-800 hover:bg-emerald-200 shadow-2xs cursor-pointer">+100</button>
                                                                <button type="button" onclick="adjustInput('inp-rebutan-{{ $t->id }}', -50, {{ $t->id }}, {{ $tNum }})" class="px-1.5 py-0.5 text-[9px] font-black rounded bg-rose-100 text-rose-800 hover:bg-rose-200 shadow-2xs cursor-pointer">-50</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center">
                                                        <input type="number" step="any" name="semi_balon[{{ $t->id }}]" id="inp-balon-{{ $t->id }}" value="{{ $t->balon ?? 0 }}" oninput="calcSemiRow({{ $t->id }}, {{ $tNum }})" class="w-24 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-indigo-500" data-termin="{{ $tNum }}">
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center font-mono font-black text-base text-indigo-900 bg-indigo-50/50">
                                                        <span id="display-total-{{ $t->id }}">{{ number_format($t->total_score ?? 0, 0, ',', '.') }}</span>
                                                    </td>
                                                    <td class="py-3.5 px-4 text-center font-bold" id="display-status-{{ $t->id }}">
                                                        @if($isWinner)
                                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-black uppercase tracking-wider shadow-2xs">
                                                                <i class="fa-solid fa-ticket text-emerald-600"></i> Lolos Final
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                                                                Peringkat {{ $t->termin_rank ?? ($tIdx + 1) }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="py-6 text-center text-slate-400 font-bold">
                                                        Belum ada regu pada termin ini. Klik "Generate Bagan Semifinal" di Tahap 1 atau tombol "Muat Data Simulasi Sheet".
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Bottom Bar with Save Button -->
                    <div class="bg-white rounded-2xl p-4 border border-slate-200 flex items-center justify-between gap-3 flex-wrap">
                        <div class="text-xs text-slate-600 font-medium">
                            * Nilai total otomatis terhitung. Tekan tombol simpan untuk memperbarui tiket Final dan Juara Harapan.
                        </div>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Nilai Semifinal (Semua Termin)</span>
                        </button>
                    </div>

                </div>
            </form>

            <!-- Table: Kompilasi 6 Tim Non-Finalis (Penentuan Juara Harapan 1 & 2) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mt-6">
                <div class="p-5 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <div class="text-xs font-bold text-amber-700 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-award text-amber-500"></i> Akumulasi Poin Semifinal Non-Finalis
                        </div>
                        <h3 class="text-sm font-black text-slate-900 mt-0.5">
                            Penetapan Juara Harapan 1 &amp; Juara Harapan 2 (Sesuai Aturan Resmi)
                        </h3>
                    </div>
                    <span class="text-xs font-medium text-slate-500">
                        Diambil dari skor tertinggi dari seluruh termin semi-final (di luar 3 finalis)
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                                <th class="py-3 px-4 w-16 text-center border-r border-slate-800">NO</th>
                                <th class="py-3 px-5 border-r border-slate-800">NAMA SEKOLAH / REGU</th>
                                <th class="py-3 px-4 text-center border-r border-slate-800 w-36">TERMIN ASAL</th>
                                <th class="py-3 px-4 text-center border-r border-slate-800 w-44">POIN TOTAL SEMIFINAL</th>
                                <th class="py-3 px-4 text-center w-48 bg-black">STATUS PREDIKAT JUARA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-medium">
                            @forelse($sortedNonFinalists as $nIdx => $nt)
                                @php
                                    $isH1 = ($nIdx === 0);
                                    $isH2 = ($nIdx === 1);
                                    $rowClass = $isH1 ? 'bg-amber-50/80 font-bold' : ($isH2 ? 'bg-amber-50/40 font-bold' : '');
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800">
                                        {{ $nIdx + 1 }}
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-black text-slate-900 text-sm">
                                            {{ $nt->team_name ?? ($nt->registration?->school_name ?? '-') }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                        Termin {{ $nt->termin ?? '-' }} (Peringkat {{ $nt->termin_rank }})
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-black text-sm text-slate-900">
                                        {{ number_format($nt->total_score, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($isH1)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-stone-950 font-black text-xs shadow-2xs border border-yellow-300">
                                                <i class="fa-solid fa-trophy"></i> Juara Harapan 1
                                            </span>
                                        @elseif($isH2)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-100 text-amber-900 font-black text-xs shadow-2xs border border-amber-300">
                                                <i class="fa-solid fa-medal"></i> Juara Harapan 2
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-500">
                                                Peringkat {{ $nt->overall_rank ?? ($nIdx + 4) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 font-bold">
                                        Data nilai semifinal belum diisi. Lengkapi nilai di ketiga termin di atas lalu simpan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- STAGE 3: TAHAP GRAND FINAL                                                --}}
    {{-- ========================================================================= --}}
    @if($activeStage === 3)
        <div class="space-y-6">
            <!-- Stage 3 Top Instruction Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-amber-950 to-slate-900 text-white rounded-3xl p-6 shadow-md border border-amber-900/50 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950">
                            Sesi 3 (Grand Final)
                        </span>
                        <span class="text-xs font-bold text-amber-200">
                            Perebutan Juara 1, 2, dan 3 LCT
                        </span>
                    </div>
                    <h2 class="text-lg font-black text-white">
                        Babak Puncak: Soal Amplop, Rebutan Bertingkat, dan Taruh Nilai
                    </h2>
                    <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">
                        Diikuti oleh <strong>3 regu pemenang masing-masing termin semi-final</strong>. Babak ini menentukan Juara 1 (Emas), Juara 2 (Perak), dan Juara 3 (Perunggu). Juara Harapan 1 dan 2 otomatis dirangkum dari hasil semifinal.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0 flex-wrap">
                    <form action="{{ route('admin.competition-scores.lct-reset-stage', $category->id) }}" method="POST" onsubmit="return confirm('Reset nilai babak final?')">
                        @csrf
                        <input type="hidden" name="stage" value="final">
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-rotate-left"></i> Reset Final
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form Simpan Nilai Grand Final -->
            <form action="{{ route('admin.competition-scores.lct-save-final', $category->id) }}" method="POST">
                @csrf
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-trophy text-amber-500 text-base"></i>
                            <span class="font-black text-xs sm:text-sm text-slate-900">
                                Lembar Nilai Grand Final (3 Regu Puncak)
                            </span>
                        </div>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Nilai Grand Final</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                                    <th class="py-3.5 px-4 w-16 text-center border-r border-slate-800">NO</th>
                                    <th class="py-3.5 px-5 border-r border-slate-800">NAMA SEKOLAH / REGU FINALIS</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-40">SOAL AMPLOP</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-52">REBUTAN BERTINGKAT</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-48">TARUH NILAI</th>
                                    <th class="py-3.5 px-4 text-center border-r border-slate-800 w-40 bg-slate-950">POIN TOTAL</th>
                                    <th class="py-3.5 px-4 text-center w-44 bg-black">PERINGKAT JUARA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-medium">
                                @forelse($finalGrandTeams as $fIdx => $ft)
                                    @php
                                        $fRank = $ft->grand_rank ?? ($fIdx + 1);
                                        $isJuara1 = ($fRank == 1 && $ft->total_score > 0);
                                        $isJuara2 = ($fRank == 2 && $ft->total_score > 0);
                                        $isJuara3 = ($fRank == 3 && $ft->total_score > 0);
                                    @endphp
                                    <tr class="{{ $isJuara1 ? 'bg-amber-50/70 font-bold' : '' }}">
                                        <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800">
                                            {{ $fIdx + 1 }}
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <div class="font-black text-slate-900 text-sm">
                                                {{ $ft->team_name ?? ($ft->registration?->school_name ?? 'Finalis ' . ($fIdx + 1)) }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-medium">
                                                Pangkalan: {{ $ft->registration?->school_name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <input type="number" step="any" name="final_amplop[{{ $ft->id }}]" id="inp-final-amplop-{{ $ft->id }}" value="{{ $ft->soal_amplop ?? 0 }}" oninput="calcFinalRow({{ $ft->id }})" class="w-28 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-amber-500">
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <input type="number" step="any" name="final_rebutan[{{ $ft->id }}]" id="inp-final-rebutan-{{ $ft->id }}" value="{{ $ft->rebutan_bertingkat ?? 0 }}" oninput="calcFinalRow({{ $ft->id }})" class="w-28 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-amber-500">
                                                <div class="flex flex-col gap-0.5">
                                                    <button type="button" onclick="adjustFinalInput('inp-final-rebutan-{{ $ft->id }}', 100, {{ $ft->id }})" class="px-1.5 py-0.5 text-[9px] font-black rounded bg-emerald-100 text-emerald-800 hover:bg-emerald-200 shadow-2xs cursor-pointer">+100</button>
                                                    <button type="button" onclick="adjustFinalInput('inp-final-rebutan-{{ $ft->id }}', -50, {{ $ft->id }})" class="px-1.5 py-0.5 text-[9px] font-black rounded bg-rose-100 text-rose-800 hover:bg-rose-200 shadow-2xs cursor-pointer">-50</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <input type="number" step="any" name="final_taruh[{{ $ft->id }}]" id="inp-final-taruh-{{ $ft->id }}" value="{{ $ft->taruh_nilai ?? 0 }}" oninput="calcFinalRow({{ $ft->id }})" class="w-28 text-center font-mono font-bold text-sm bg-white border border-slate-300 rounded-xl py-1.5 px-2 focus:ring-2 focus:ring-amber-500">
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-mono font-black text-lg text-amber-950 bg-amber-50/50">
                                            <span id="display-final-total-{{ $ft->id }}">{{ number_format($ft->total_score ?? 0, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($isJuara1)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-stone-950 font-black text-xs shadow-2xs border border-yellow-300">
                                                    🥇 Juara 1
                                                </span>
                                            @elseif($isJuara2)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-slate-200 to-slate-300 text-slate-800 font-black text-xs shadow-2xs border border-slate-400">
                                                    🥈 Juara 2
                                                </span>
                                            @elseif($isJuara3)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-amber-700 to-yellow-800 text-amber-100 font-black text-xs shadow-2xs border border-amber-600">
                                                    🥉 Juara 3
                                                </span>
                                            @else
                                                <span class="text-xs font-bold text-slate-600">
                                                    Peringkat {{ $fRank }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400 font-bold">
                                            Belum ada finalis. Selesaikan dan simpan penilaian di Sesi 2 (Semi-Final) terlebih dahulu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3 flex-wrap">
                        <div class="text-xs text-slate-500 font-medium">
                            * Babak Taruh Nilai: Peserta mempertaruhkan poin strategis sesuai ketentuan dewan juri.
                        </div>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Nilai Grand Final</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Grand Podium & Final Summary Cards -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-6">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-500"></i>
                        <span>Podium Kejuaraan Resmi Lomba Cepat Tepat (LCT)</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi lengkap seluruh predikat juara dari babak penyisihan hingga grand final.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <!-- Juara 1 -->
                    @php $j1 = $finalGrandTeams->firstWhere('grand_rank', 1); @endphp
                    <div class="bg-gradient-to-b from-amber-100 via-amber-50 to-white border-2 border-amber-400 rounded-2xl p-4 text-center space-y-2 shadow-xs">
                        <div class="text-3xl">🥇</div>
                        <div class="text-xs font-black uppercase tracking-wider text-amber-900">JUARA 1 (EMAS)</div>
                        <div class="font-black text-slate-900 text-sm truncate" title="{{ $j1?->team_name ?? '-' }}">
                            {{ $j1?->team_name ?? '-' }}
                        </div>
                        <div class="text-xs font-mono font-black text-amber-800">
                            {{ $j1 ? number_format($j1->total_score, 0, ',', '.') . ' Poin' : '-' }}
                        </div>
                    </div>

                    <!-- Juara 2 -->
                    @php $j2 = $finalGrandTeams->firstWhere('grand_rank', 2); @endphp
                    <div class="bg-gradient-to-b from-slate-100 via-slate-50 to-white border-2 border-slate-300 rounded-2xl p-4 text-center space-y-2 shadow-xs">
                        <div class="text-3xl">🥈</div>
                        <div class="text-xs font-black uppercase tracking-wider text-slate-800">JUARA 2 (PERAK)</div>
                        <div class="font-black text-slate-900 text-sm truncate" title="{{ $j2?->team_name ?? '-' }}">
                            {{ $j2?->team_name ?? '-' }}
                        </div>
                        <div class="text-xs font-mono font-black text-slate-700">
                            {{ $j2 ? number_format($j2->total_score, 0, ',', '.') . ' Poin' : '-' }}
                        </div>
                    </div>

                    <!-- Juara 3 -->
                    @php $j3 = $finalGrandTeams->firstWhere('grand_rank', 3); @endphp
                    <div class="bg-gradient-to-b from-amber-900/10 via-amber-50 to-white border-2 border-amber-600 rounded-2xl p-4 text-center space-y-2 shadow-xs">
                        <div class="text-3xl">🥉</div>
                        <div class="text-xs font-black uppercase tracking-wider text-amber-950">JUARA 3 (PERUNGGU)</div>
                        <div class="font-black text-slate-900 text-sm truncate" title="{{ $j3?->team_name ?? '-' }}">
                            {{ $j3?->team_name ?? '-' }}
                        </div>
                        <div class="text-xs font-mono font-black text-amber-900">
                            {{ $j3 ? number_format($j3->total_score, 0, ',', '.') . ' Poin' : '-' }}
                        </div>
                    </div>

                    <!-- Juara Harapan 1 -->
                    <div class="bg-gradient-to-b from-yellow-50 via-white to-white border-2 border-yellow-300 rounded-2xl p-4 text-center space-y-2 shadow-xs">
                        <div class="text-3xl">🏆</div>
                        <div class="text-xs font-black uppercase tracking-wider text-amber-900">JUARA HARAPAN 1</div>
                        <div class="font-black text-slate-900 text-sm truncate" title="{{ $harapan1Team?->team_name ?? '-' }}">
                            {{ $harapan1Team?->team_name ?? '-' }}
                        </div>
                        <div class="text-xs font-mono font-black text-amber-800">
                            {{ $harapan1Team ? number_format($harapan1Team->total_score, 0, ',', '.') . ' Poin' : '-' }}
                        </div>
                    </div>

                    <!-- Juara Harapan 2 -->
                    <div class="bg-gradient-to-b from-amber-50 via-white to-white border-2 border-amber-200 rounded-2xl p-4 text-center space-y-2 shadow-xs">
                        <div class="text-3xl">🎖️</div>
                        <div class="text-xs font-black uppercase tracking-wider text-amber-900">JUARA HARAPAN 2</div>
                        <div class="font-black text-slate-900 text-sm truncate" title="{{ $harapan2Team?->team_name ?? '-' }}">
                            {{ $harapan2Team?->team_name ?? '-' }}
                        </div>
                        <div class="text-xs font-mono font-black text-amber-800">
                            {{ $harapan2Team ? number_format($harapan2Team->total_score, 0, ',', '.') . ' Poin' : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // Live helper: konversi waktu string ke detik untuk client-side tie-breaker
    function parseTimeToSecondsJs(timeStr) {
        if (!timeStr) return 999999;
        timeStr = timeStr.trim();
        if (!timeStr) return 999999;

        const cleanStr = timeStr.replace(/[^\d:.]/g, '');
        if (cleanStr.length >= 3 && cleanStr.length <= 4 && /^\d{1,2}[0-5]\d$/.test(cleanStr) && !cleanStr.includes(':') && !cleanStr.includes('.')) {
            const min = parseInt(cleanStr.slice(0, cleanStr.length - 2), 10);
            const sec = parseInt(cleanStr.slice(-2), 10);
            return (min * 60) + sec;
        }

        if (cleanStr.includes(':')) {
            const parts = cleanStr.split(':');
            if (parts.length === 2) {
                return (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
            } else if (parts.length === 3) {
                return (parseInt(parts[0], 10) * 3600) + (parseInt(parts[1], 10) * 60) + parseInt(parts[2], 10);
            }
        } else if (cleanStr.includes('.')) {
            const parts = cleanStr.split('.');
            if (parts.length === 2) {
                return (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
            }
        }

        const num = parseFloat(cleanStr);
        return isNaN(num) ? 999999 : num;
    }

    // Live update ranking & kelolosan Babak Penyisihan (Skor Tertinggi -> Rank 1)
    function recalculateLivePrelimRanks() {
        const rows = [];
        document.querySelectorAll('.prelim-score-input').forEach(inp => {
            const teamId = inp.dataset.teamId;
            const valStr = inp.value.trim();
            const score = valStr === '' ? null : parseFloat(valStr);

            const timeInp = document.querySelector(`.prelim-time-input[data-team-id="${teamId}"]`);
            const timeStr = timeInp ? timeInp.value.trim() : '';
            const timeSeconds = parseTimeToSecondsJs(timeStr);

            rows.push({
                teamId: teamId,
                score: (score !== null && !isNaN(score)) ? score : null,
                timeSeconds: timeSeconds
            });

            // Update display Nilai Akhir
            const displayScore = document.getElementById('display-prelim-score-' + teamId);
            if (displayScore) {
                displayScore.textContent = (score !== null && !isNaN(score)) ? score.toLocaleString('id-ID') : '-';
            }
        });

        // Filter tim yang sudah diberi nilai
        const scoredRows = rows.filter(r => r.score !== null);

        // Sorting ketat: Nilai Tertinggi di atas (descending). Jika nilai sama, waktu tercepat di atas (ascending)
        scoredRows.sort((a, b) => {
            if (b.score !== a.score) {
                return b.score - a.score;
            }
            return a.timeSeconds - b.timeSeconds;
        });

        // Beri ranking 1 s/d N untuk yang sudah dinilai
        scoredRows.forEach((r, idx) => {
            r.rank = idx + 1;
            r.isTop9 = (idx < 9);
        });

        // Update tampilan DOM
        rows.forEach(r => {
            const rankEl = document.getElementById('display-prelim-rank-' + r.teamId);
            const statusEl = document.getElementById('display-prelim-status-' + r.teamId);
            const rowEl = document.getElementById('row-team-' + r.teamId);

            if (r.score === null) {
                if (rankEl) rankEl.innerHTML = '<span class="text-slate-300 font-mono font-bold">-</span>';
                if (statusEl) statusEl.innerHTML = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-400 text-[10px] font-semibold"><i class="fa-regular fa-clock"></i> Belum Dinilai</span>';
                if (rowEl) {
                    rowEl.className = 'border-l-4 border-l-transparent hover:bg-slate-50 transition';
                }
            } else {
                const isQual = r.isTop9;
                let badgeClass = 'bg-slate-200 text-slate-700 font-bold';
                let rowBgClass = 'border-l-4 border-l-transparent hover:bg-slate-50';
                let iconHtml = '';

                if (r.rank === 1) {
                    badgeClass = 'bg-gradient-to-tr from-amber-500 via-amber-400 to-yellow-300 text-slate-950 ring-2 ring-amber-300 shadow-sm font-black';
                    rowBgClass = 'border-l-4 border-l-amber-500 bg-amber-50/50 hover:bg-amber-50';
                    iconHtml = '<i class="fa-solid fa-crown text-[9px] mr-0.5"></i>';
                } else if (r.rank === 2) {
                    badgeClass = 'bg-gradient-to-tr from-slate-400 via-slate-300 to-slate-200 text-slate-900 ring-2 ring-slate-300 shadow-sm font-black';
                    rowBgClass = 'border-l-4 border-l-slate-400 bg-slate-100/70 hover:bg-slate-100';
                } else if (r.rank === 3) {
                    badgeClass = 'bg-gradient-to-tr from-amber-700 via-amber-600 to-amber-500 text-white ring-2 ring-amber-600 shadow-sm font-black';
                    rowBgClass = 'border-l-4 border-l-amber-700 bg-amber-50/40 hover:bg-amber-50';
                } else if (isQual) {
                    badgeClass = 'bg-gradient-to-tr from-emerald-600 to-teal-500 text-white ring-1 ring-emerald-400 shadow-2xs font-bold';
                    rowBgClass = 'border-l-4 border-l-emerald-500 bg-emerald-50/40 hover:bg-emerald-50';
                }

                if (rankEl) {
                    rankEl.innerHTML = `<span class="inline-flex items-center justify-center w-8 h-8 rounded-full leading-8 text-center text-xs ${badgeClass}">${iconHtml}${r.rank}</span>`;
                }
                if (statusEl) {
                    if (isQual) {
                        statusEl.innerHTML = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-black uppercase tracking-wider shadow-2xs"><i class="fa-solid fa-circle-check text-emerald-600"></i> Lolos Semi-Final</span>';
                    } else {
                        statusEl.innerHTML = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 text-[10px] font-bold"><i class="fa-solid fa-circle-xmark text-rose-500"></i> Gugur</span>';
                    }
                }
                if (rowEl) {
                    rowEl.className = `${rowBgClass} transition`;
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.prelim-score-input, .prelim-time-input').forEach(inp => {
            inp.addEventListener('input', recalculateLivePrelimRanks);
            inp.addEventListener('change', recalculateLivePrelimRanks);
        });
    });

    // Quick Add Score Adjuster
    function adjustInput(inputId, delta, teamId, terminNum) {
        const inp = document.getElementById(inputId);
        if (!inp) return;
        let current = parseFloat(inp.value) || 0;
        inp.value = current + delta;
        calcSemiRow(teamId, terminNum);
    }

    // Hitung Live Total Skor Semifinal & Evaluasi Ranking / Kelolosan Otomatis
    function calcSemiRow(teamId, terminNum) {
        if (!terminNum) {
            const input = document.getElementById('inp-amplop-' + teamId);
            terminNum = input?.dataset?.termin || 1;
        }

        const amplop = parseFloat(document.getElementById('inp-amplop-' + teamId)?.value) || 0;
        const rebutan = parseFloat(document.getElementById('inp-rebutan-' + teamId)?.value) || 0;
        const balon = parseFloat(document.getElementById('inp-balon-' + teamId)?.value) || 0;
        const total = amplop + rebutan + balon;

        const display = document.getElementById('display-total-' + teamId);
        if (display) {
            display.textContent = total.toLocaleString('id-ID');
        }

        recalculateTerminRanks(terminNum);
    }

    // Evaluasi Ranking per Termin: Nilai Tertinggi -> Ranking 1 (Lolos Final)
    function recalculateTerminRanks(terminNum) {
        const rows = [];
        document.querySelectorAll(`tr[data-termin="${terminNum}"]`).forEach(tr => {
            const teamIdMatch = tr.id.match(/row-semi-(\d+)/);
            if (!teamIdMatch) return;
            const teamId = teamIdMatch[1];
            const amplop = parseFloat(document.getElementById('inp-amplop-' + teamId)?.value) || 0;
            const rebutan = parseFloat(document.getElementById('inp-rebutan-' + teamId)?.value) || 0;
            const balon = parseFloat(document.getElementById('inp-balon-' + teamId)?.value) || 0;
            const total = amplop + rebutan + balon;

            rows.push({
                teamId: teamId,
                total: total,
                trElement: tr
            });
        });

        // Urutkan nilai tertinggi ke terendah (descending)
        rows.sort((a, b) => b.total - a.total);

        // Update Ranking dan Status Lolos Final secara LIVE!
        rows.forEach((r, idx) => {
            const rank = idx + 1;
            const statusEl = document.getElementById('display-status-' + r.teamId);

            if (rank === 1) {
                // Rank 1: Lolos Final!
                if (statusEl) {
                    statusEl.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-black uppercase tracking-wider shadow-2xs">
                        <i class="fa-solid fa-ticket text-emerald-600"></i> Lolos Final
                    </span>`;
                }
                r.trElement.className = 'bg-emerald-50/70 font-bold border-l-4 border-l-emerald-500 transition';
            } else {
                // Rank 2, 3: Peringkat 2, Peringkat 3
                if (statusEl) {
                    statusEl.innerHTML = `<span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                        Peringkat ${rank}
                    </span>`;
                }
                r.trElement.className = 'hover:bg-slate-50 border-l-4 border-l-transparent transition';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        [1, 2, 3].forEach(tNum => {
            if (document.querySelector(`tr[data-termin="${tNum}"]`)) {
                recalculateTerminRanks(tNum);
            }
        });
    });

    // Quick Add Final Score Adjuster
    function adjustFinalInput(inputId, delta, teamId) {
        const inp = document.getElementById(inputId);
        if (!inp) return;
        let current = parseFloat(inp.value) || 0;
        inp.value = current + delta;
        calcFinalRow(teamId);
    }

    // Hitung Live Total Skor Final
    function calcFinalRow(teamId) {
        const amplop = parseFloat(document.getElementById('inp-final-amplop-' + teamId)?.value) || 0;
        const rebutan = parseFloat(document.getElementById('inp-final-rebutan-' + teamId)?.value) || 0;
        const taruh = parseFloat(document.getElementById('inp-final-taruh-' + teamId)?.value) || 0;
        const total = amplop + rebutan + taruh;

        const display = document.getElementById('display-final-total-' + teamId);
        if (display) {
            display.textContent = total.toLocaleString('id-ID');
        }
    }
</script>
@endpush
