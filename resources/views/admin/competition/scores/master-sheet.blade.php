@extends('layouts.admin')

@section('title', 'Lembar Skor Utama - Rekap Penilaian Lomba PMR')
@section('page_title', 'Lembar Skor Utama (Master Score Sheet)')

@section('top_actions')
    <a href="{{ route('admin.competition-scores.index', ['level' => $level !== 'all' ? $level : 'Madya']) }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2.5 rounded-xl font-bold text-xs shadow-2xs transition flex items-center gap-2">
        <i class="fa-solid fa-list-check text-teal-600"></i> Input Nilai per Cabang
    </a>
    <a href="{{ route('admin.competition-scores.master-sheet.export-excel', request()->query()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow transition flex items-center gap-2">
        <i class="fa-solid fa-file-excel"></i> Export Excel (.xlsx)
    </a>
    <a href="{{ route('admin.competition-leaderboard.index') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow transition flex items-center gap-2">
        <i class="fa-solid fa-trophy"></i> Rekap Juara Umum
    </a>
    <a href="{{ route('lomba.scoreboard') }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2">
        <i class="fa-solid fa-tv"></i> Papan Skor Publik
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Event Selector -->
    @include('admin.competition.partials.event-selector')

    <!-- Filter Bar Card -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 space-y-5">
        
        <!-- Tingkatan Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.competition-scores.master-sheet', array_merge(request()->except(['level', 'page']), ['level' => 'all'])) }}" class="px-5 py-2.5 rounded-xl font-black text-xs transition {{ $level === 'all' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-border-all mr-1.5"></i> SEMUA TINGKAT
                </a>
                <a href="{{ route('admin.competition-scores.master-sheet', array_merge(request()->except(['level', 'page']), ['level' => 'Mula'])) }}" class="px-5 py-2.5 rounded-xl font-black text-xs transition {{ $level === 'Mula' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                    <i class="fa-solid fa-child-reaching mr-1.5"></i> PMR MULA (SD)
                </a>
                <a href="{{ route('admin.competition-scores.master-sheet', array_merge(request()->except(['level', 'page']), ['level' => 'Madya'])) }}" class="px-5 py-2.5 rounded-xl font-black text-xs transition {{ $level === 'Madya' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-blue-50 text-blue-800 hover:bg-blue-100' }}">
                    <i class="fa-solid fa-user-group mr-1.5"></i> PMR MADYA (SMP)
                </a>
                <a href="{{ route('admin.competition-scores.master-sheet', array_merge(request()->except(['level', 'page']), ['level' => 'Wira'])) }}" class="px-5 py-2.5 rounded-xl font-black text-xs transition {{ $level === 'Wira' ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/30 font-black' : 'bg-amber-50 text-amber-900 hover:bg-amber-100' }}">
                    <i class="fa-solid fa-graduation-cap mr-1.5"></i> PMR WIRA (SMA)
                </a>
            </div>

            <div class="text-xs font-bold text-slate-500">
                Menampilkan data resmi skor dewan juri
            </div>
        </div>

        <!-- Filter Form: Sekolah, Cabang Lomba, Pencarian -->
        <form method="GET" action="{{ route('admin.competition-scores.master-sheet') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <input type="hidden" name="level" value="{{ $level }}">
            @if(request('event_id'))
                <input type="hidden" name="event_id" value="{{ request('event_id') }}">
            @endif

            <!-- Pilih Sekolah / Pangkalan -->
            <div class="md:col-span-4">
                <label for="school-filter" class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-school text-blue-600"></i> Pilih Asal Sekolah / Pangkalan:
                </label>
                <div class="relative">
                    <select id="school-filter" name="school_name" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 appearance-none cursor-pointer">
                        <option value="all">-- Semua Sekolah / Pangkalan ({{ $schools->count() }} Sekolah) --</option>
                        @foreach($schools as $sch)
                            <option value="{{ $sch }}" {{ $schoolName === $sch ? 'selected' : '' }}>
                                {{ $sch }}
                            </option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            <!-- Pilih Cabang Mata Lomba -->
            <div class="md:col-span-3">
                <label for="category-filter" class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-medal text-amber-500"></i> Cabang Mata Lomba:
                </label>
                <div class="relative">
                    <select id="category-filter" name="category_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 appearance-none cursor-pointer">
                        <option value="all">-- Semua Cabang Lomba ({{ $categories->count() }}) --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->gender_category }}) [{{ $cat->level }}]
                            </option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            <!-- Input Pencarian Bebas -->
            <div class="md:col-span-3">
                <label for="search-input" class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass text-slate-500"></i> Cari Sekolah / No Urut:
                </label>
                <input type="text" id="search-input" name="search" value="{{ $search }}" placeholder="Ketik kata kunci..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-black text-xs py-2.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.competition-scores.master-sheet', ['level' => $level]) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs py-2.5 px-3 rounded-xl transition flex items-center justify-center" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-wider">TOTAL REGU PESERTA</div>
                <div class="text-2xl font-black text-slate-900 mt-0.5 font-mono">{{ $totalTeams }} <span class="text-xs font-bold text-slate-400 font-sans">Regu</span></div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black text-emerald-600 uppercase tracking-wider">SUDAH DINILAI JURI</div>
                <div class="text-2xl font-black text-emerald-700 mt-0.5 font-mono">{{ $scoredTeams }} <span class="text-xs font-bold text-emerald-600 font-sans">Regu</span></div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black text-amber-600 uppercase tracking-wider">BELUM DINILAI</div>
                <div class="text-2xl font-black text-amber-700 mt-0.5 font-mono">{{ $unscoredTeams }} <span class="text-xs font-bold text-amber-600 font-sans">Regu</span></div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black text-purple-600 uppercase tracking-wider">TOTAL SEKOLAH</div>
                <div class="text-2xl font-black text-purple-700 mt-0.5 font-mono">{{ $schools->count() }} <span class="text-xs font-bold text-purple-600 font-sans">Sekolah</span></div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-school-flag"></i>
            </div>
        </div>
    </div>

    <!-- Master Table Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Table Header Banner -->
        <div class="p-5 border-b border-slate-200 bg-slate-50/80 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-blue-600"></i>
                    Lembar Skor Utama: Rekap Data Seluruh Regu & Sekolah
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if($schoolName !== 'all')
                        Filter Asal Sekolah: <strong class="text-slate-800">{{ $schoolName }}</strong> &bull;
                    @endif
                    Tingkat: <strong class="text-slate-800">{{ $level === 'all' ? 'Semua Tingkatan' : 'PMR ' . $level }}</strong>
                    ({{ $teams->count() }} baris data ditemukan)
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.competition-scores.master-sheet.export-excel', request()->query()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-file-excel"></i> Unduh Excel
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-black">
                        <th class="py-3.5 px-4 w-14 text-center border-r border-slate-800">NO. URUT</th>
                        <th class="py-3.5 px-3 w-20 text-center border-r border-slate-800">TINGKAT</th>
                        <th class="py-3.5 px-4 border-r border-slate-800">ASAL SEKOLAH / PANGKALAN</th>
                        <th class="py-3.5 px-4 border-r border-slate-800">CABANG MATA LOMBA</th>
                        <th class="py-3.5 px-4 border-r border-slate-800">NAMA REGU</th>
                        <th class="py-3.5 px-4 border-r border-slate-800 text-center">JURI PENILAI</th>
                        <th class="py-3.5 px-3 text-center border-r border-slate-800 w-28">NILAI PRAKTIK</th>
                        <th class="py-3.5 px-3 text-center border-r border-slate-800 w-24">WAKTU</th>
                        <th class="py-3.5 px-4 text-center border-r border-slate-800 w-28 bg-slate-950">NILAI AKHIR</th>
                        <th class="py-3.5 px-3 text-center border-r border-slate-800 w-24">PERINGKAT</th>
                        <th class="py-3.5 px-3 text-center w-28">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($teams as $index => $team)
                        @php
                            $cat = $team->category;
                            $reg = $team->registration;
                            $score = $team->scores->first();

                            $details = is_array($score?->score_details) ? $score->score_details : (json_decode($score?->score_details ?? '', true) ?? []);
                            $practicalScore = $details['practical_score'] ?? ($details['technical_score'] ?? ($details['score'] ?? ($score?->final_score !== null && $score?->final_score > 0 ? $score->final_score : null)));
                            $timeRecorded = $score?->time_recorded ?: null;
                            $finalScore = $score?->final_score;

                            $isPPmula = ($cat?->level === 'Mula') && (str_contains(strtolower($cat?->name ?? ''), 'pertolongan pertama') || str_starts_with($cat?->code ?? '', 'LPP'));

                            // Badge color by level
                            $levelBadgeClass = match($cat?->level) {
                                'Mula' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                'Madya' => 'bg-blue-100 text-blue-800 border-blue-300',
                                'Wira' => 'bg-amber-100 text-amber-900 border-amber-300',
                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/90 transition {{ $score?->rank == 1 ? 'bg-amber-50/50 font-bold' : '' }}">
                            <!-- No. Urut -->
                            <td class="py-3.5 px-4 font-mono text-center font-black text-slate-800 border-r border-slate-100">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-slate-100 border border-slate-200 text-xs">
                                    {{ $team->order_number ?: ($index + 1) }}
                                </span>
                            </td>

                            <!-- Tingkat -->
                            <td class="py-3.5 px-3 text-center border-r border-slate-100">
                                <span class="px-2 py-0.5 rounded-md border font-black text-[10px] uppercase {{ $levelBadgeClass }}">
                                    {{ $cat?->level ?: ($reg?->level ?: '-') }}
                                </span>
                            </td>

                            <!-- Asal Sekolah -->
                            <td class="py-3.5 px-4 border-r border-slate-100">
                                <div class="font-extrabold text-slate-900 text-xs">
                                    {{ $reg?->school_name ?: $team->team_name }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                    {{ $reg?->registration_code ?: 'OTS / Penambahan' }}
                                </div>
                            </td>

                            <!-- Cabang Mata Lomba -->
                            <td class="py-3.5 px-4 border-r border-slate-100">
                                <div class="font-bold text-slate-800 flex items-center gap-1.5 flex-wrap">
                                    <span>{{ $cat?->name ?: '-' }}</span>
                                    @if($cat?->gender_category === 'Putra')
                                        <span class="text-[10px] font-black text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.2 rounded">
                                            PA
                                        </span>
                                    @elseif($cat?->gender_category === 'Putri')
                                        <span class="text-[10px] font-black text-rose-700 bg-rose-50 border border-rose-200 px-1.5 py-0.2 rounded">
                                            PI
                                        </span>
                                    @endif
                                </div>
                                <span class="font-mono text-[10px] text-slate-400">
                                    {{ $cat?->code }}
                                </span>
                            </td>

                            <!-- Nama Regu -->
                            <td class="py-3.5 px-4 font-bold text-slate-700 border-r border-slate-100">
                                {{ $team->team_label ?: $team->team_name }}
                            </td>

                            <!-- Juri Penilai -->
                            <td class="py-3.5 px-4 text-center border-r border-slate-100">
                                @if($score?->jury)
                                    <span class="text-xs font-bold text-slate-800">
                                        {{ $score->jury->name }}
                                    </span>
                                @elseif($cat?->assignedJuries->isNotEmpty())
                                    <span class="text-[11px] text-slate-500 font-medium">
                                        {{ $cat->assignedJuries->first()->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Nilai Praktik -->
                            <td class="py-3.5 px-3 text-center font-mono font-bold border-r border-slate-100">
                                @if($practicalScore !== null)
                                    <span class="text-slate-900 font-black text-sm">{{ $practicalScore }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>

                            <!-- Waktu Praktik -->
                            <td class="py-3.5 px-3 text-center font-mono border-r border-slate-100">
                                @if($timeRecorded)
                                    <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-xs">{{ $timeRecorded }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>

                            <!-- Nilai Akhir (Total) -->
                            <td class="py-3.5 px-4 text-center font-mono border-r border-slate-100 bg-slate-50/50">
                                @if($finalScore !== null && $finalScore > 0)
                                    <span class="text-base font-black text-red-600">{{ number_format($finalScore, 2, ',', '.') }}</span>
                                @elseif($finalScore !== null && $finalScore == 0)
                                    <span class="text-xs font-bold text-slate-400">0,00</span>
                                @else
                                    <span class="text-slate-300 font-bold">-</span>
                                @endif
                            </td>

                            <!-- Peringkat -->
                            <td class="py-3.5 px-3 text-center border-r border-slate-100">
                                @if($score?->is_disqualified)
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-black text-[10px] border border-rose-300">
                                        DISKUALIFIKASI
                                    </span>
                                @elseif($score?->rank)
                                    @if($score->rank == 1)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-950 font-black text-xs shadow-sm" title="Juara 1">1</span>
                                    @elseif($score->rank == 2)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-300 text-slate-950 font-black text-xs shadow-sm" title="Juara 2">2</span>
                                    @elseif($score->rank == 3)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-400 text-white font-black text-xs shadow-sm" title="Juara 3">3</span>
                                    @elseif($score->rank == 4)
                                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-black text-[10px] border border-blue-200">Harapan 1</span>
                                    @elseif($score->rank == 5)
                                        <span class="px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-800 font-black text-[10px] border border-cyan-200">Harapan 2</span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                                            {{ $score->rank }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-300 font-mono">-</span>
                                @endif
                            </td>

                            <!-- Aksi Pintas -->
                            <td class="py-3.5 px-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($isPPmula)
                                        <a href="{{ route('admin.competition-scores.grid', ['category' => $cat->id, 'team_id' => $team->id]) }}" class="px-2.5 py-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1" title="Buka Formulir Grid Nilai Mula">
                                            <i class="fa-solid fa-table-cells text-[10px]"></i> Grid
                                        </a>
                                    @endif
                                    @if($cat)
                                        <a href="{{ route('admin.competition-scores.input', $cat->id) }}" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition" title="Buka Lembar Skor Cabang Lomba">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-12 text-slate-400">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="text-slate-300 text-4xl">
                                        <i class="fa-solid fa-folder-open"></i>
                                    </div>
                                    <div class="font-bold text-sm text-slate-700">Tidak ada data regu yang sesuai filter</div>
                                    <p class="text-xs text-slate-500">
                                        Coba ubah filter tingkatan, asal sekolah, atau reset filter untuk menampilkan seluruh data regu peserta.
                                    </p>
                                    <div class="pt-2">
                                        <a href="{{ route('admin.competition-scores.master-sheet') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs transition inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-rotate-left"></i> Tampilkan Semua Data
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
