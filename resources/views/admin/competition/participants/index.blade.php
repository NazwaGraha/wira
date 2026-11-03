@extends('layouts.admin')

@section('title', 'Daftar Peserta & Rekap Kejuaraan Lomba')
@section('page_title', 'Daftar Regu, Peserta & Rekap Kejuaraan Lomba')

@section('top_actions')
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.competition-participants.export-excel', request()->query()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs" title="Export data regu & peserta lomba terverifikasi ke file MS Excel (.xlsx)">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        <a href="{{ route('admin.competition-participants.print', request()->query()) }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-print"></i> Cetak Lembar Presensi / Roster
        </a>
        <a href="{{ route('admin.competition-leaderboard.index', ['event_id' => $event?->id]) }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-4 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-trophy"></i> Klasemen Juara Umum
        </a>
        <a href="{{ route('admin.competition-registrations.index', ['status' => 'pending']) }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-clipboard-check"></i> Verifikasi Pendaftar Baru
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Card dengan Logo Resmi PMI & SMAN 1 Ciawi dan Event Switcher -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
            <!-- Left: Logo Resmi & Context Title -->
            <div class="flex items-center gap-4">
                <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-xs shrink-0">
                    <img src="{{ asset('images/logo_pmi_sman1ciawi_official.png') }}" alt="Logo Resmi Palang Merah Indonesia & SMA Negeri 1 Ciawi Kabupaten Bogor" class="h-14 sm:h-16 w-auto object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                            Sua Bhakti Berkarya
                        </span>
                        @if($event && $event->is_active)
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[9px]"></i> Edisi Aktif
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200 flex items-center gap-1">
                                <i class="fa-solid fa-box-archive text-[9px]"></i> Arsip Riwayat Lomba
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                        {{ $event ? $event->title : 'Sua Bhakti Berkarya' }}
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5 flex items-center gap-3 flex-wrap">
                        <span><i class="fa-solid fa-calendar-days text-rose-500 mr-1"></i> {{ $event && $event->start_date ? $event->start_date->translatedFormat('d F Y') : '18 Mei 2025' }}</span>
                        <span><i class="fa-solid fa-location-dot text-emerald-500 mr-1"></i> {{ $event && $event->location ? $event->location : 'Kampus SMAN 1 Ciawi Kabupaten Bogor' }}</span>
                        <span><i class="fa-solid fa-users text-blue-500 mr-1"></i> {{ $stats['total_teams'] }} Regu Terdaftar ({{ $stats['total_schools'] }} Kontingen Sekolah)</span>
                    </p>
                </div>
            </div>

            <!-- Right: Event Switcher Selector Dropdown -->
            <div class="w-full lg:w-auto bg-slate-50 p-3.5 rounded-2xl border border-slate-200 shrink-0">
                <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider block mb-1 flex items-center gap-1">
                    <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Pilih Edisi Event Lomba:
                </span>
                <form method="GET" action="" class="flex items-center gap-2">
                    @foreach(request()->except(['event_id', 'page']) as $k => $v)
                        @if(is_string($v) || is_numeric($v))
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <select name="event_id" onchange="this.form.submit()" class="w-full lg:w-72 bg-white border-2 border-slate-300 hover:border-red-500 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-black text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-200 shadow-2xs cursor-pointer transition">
                        @foreach($allEvents as $ev)
                            <option value="{{ $ev->id }}" {{ ($event && $event->id == $ev->id) ? 'selected' : '' }}>
                                {{ $ev->title }} {{ $ev->start_date ? '(' . $ev->start_date->format('Y') . ')' : '' }} {{ $ev->is_active ? '⭐ [AKTIF]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    <!-- SECTION 1: PODIUM JUARA UMUM 3 TINGKATAN (MULA, MADYA, WIRA) -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                        <i class="fa-solid fa-trophy"></i>
                    </span>
                    <h3 class="font-black text-base sm:text-lg text-slate-900">
                        Rekapitulasi Juara Umum: {{ $event ? $event->title : 'Sua Bhakti Berkarya' }}
                    </h3>
                </div>
                <p class="text-xs text-slate-500 mt-1">Perolehan akumulasi poin tertinggi dari seluruh cabang lomba untuk masing-masing tingkatan PMR.</p>
            </div>
            <a href="{{ route('admin.competition-leaderboard.index', ['event_id' => $event?->id]) }}" class="inline-flex items-center gap-1.5 text-xs font-black text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3 py-1.5 rounded-xl transition">
                <span>Buka Matriks Skor Lengkap</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- 1. PMR WIRA -->
            <div class="bg-gradient-to-b from-amber-50/60 to-white rounded-2xl p-5 border-2 border-amber-300 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-400 text-slate-950 border border-amber-500 flex items-center gap-1.5">
                            <i class="fa-solid fa-graduation-cap"></i> PMR WIRA (SMA/SMK)
                        </span>
                        <span class="text-2xl">🏆</span>
                    </div>

                    @php $wiraStandings = $standingsByLevel['Wira'] ?? []; @endphp
                    @if(count($wiraStandings) > 0)
                        <div class="space-y-2.5">
                            @foreach(array_slice($wiraStandings, 0, 3) as $wIdx => $st)
                                <div class="p-2.5 rounded-xl border flex items-center justify-between gap-2 {{ $wIdx == 0 ? 'bg-amber-100/70 border-amber-300 shadow-2xs' : 'bg-white border-slate-200' }}">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs shrink-0 {{ $wIdx == 0 ? 'bg-amber-500 text-white' : ($wIdx == 1 ? 'bg-slate-400 text-white' : 'bg-amber-700 text-white') }}">
                                            {{ $wIdx + 1 }}
                                        </span>
                                        <div class="truncate">
                                            <div class="font-black text-xs text-slate-900 truncate">{{ $st['school_name'] }}</div>
                                            <div class="text-[10px] text-slate-500 font-medium">Juara Umum {{ $wIdx + 1 }}</div>
                                        </div>
                                    </div>
                                    <span class="font-mono font-black text-xs text-slate-900 shrink-0 bg-white px-2 py-0.5 rounded border border-slate-200">
                                        {{ $st['total_points'] }} pts
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-xs text-slate-400 font-medium italic">
                            Belum ada rekapitulasi nilai untuk tingkat Wira
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. PMR MADYA -->
            <div class="bg-gradient-to-b from-blue-50/60 to-white rounded-2xl p-5 border-2 border-blue-300 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-blue-600 text-white border border-blue-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-user-group"></i> PMR MADYA (SMP/MTs)
                        </span>
                        <span class="text-2xl">🏆</span>
                    </div>

                    @php $madyaStandings = $standingsByLevel['Madya'] ?? []; @endphp
                    @if(count($madyaStandings) > 0)
                        <div class="space-y-2.5">
                            @foreach(array_slice($madyaStandings, 0, 3) as $mIdx => $st)
                                <div class="p-2.5 rounded-xl border flex items-center justify-between gap-2 {{ $mIdx == 0 ? 'bg-blue-100/70 border-blue-300 shadow-2xs' : 'bg-white border-slate-200' }}">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs shrink-0 {{ $mIdx == 0 ? 'bg-blue-600 text-white' : ($mIdx == 1 ? 'bg-slate-400 text-white' : 'bg-amber-700 text-white') }}">
                                            {{ $mIdx + 1 }}
                                        </span>
                                        <div class="truncate">
                                            <div class="font-black text-xs text-slate-900 truncate">{{ $st['school_name'] }}</div>
                                            <div class="text-[10px] text-slate-500 font-medium">Juara Umum {{ $mIdx + 1 }}</div>
                                        </div>
                                    </div>
                                    <span class="font-mono font-black text-xs text-slate-900 shrink-0 bg-white px-2 py-0.5 rounded border border-slate-200">
                                        {{ $st['total_points'] }} pts
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-xs text-slate-400 font-medium italic">
                            Belum ada rekapitulasi nilai untuk tingkat Madya
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. PMR MULA -->
            <div class="bg-gradient-to-b from-emerald-50/60 to-white rounded-2xl p-5 border-2 border-emerald-300 shadow-xs relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-600 text-white border border-emerald-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-child-reaching"></i> PMR MULA (SD/MI)
                        </span>
                        <span class="text-2xl">🏆</span>
                    </div>

                    @php $mulaStandings = $standingsByLevel['Mula'] ?? []; @endphp
                    @if(count($mulaStandings) > 0)
                        <div class="space-y-2.5">
                            @foreach(array_slice($mulaStandings, 0, 3) as $muIdx => $st)
                                <div class="p-2.5 rounded-xl border flex items-center justify-between gap-2 {{ $muIdx == 0 ? 'bg-emerald-100/70 border-emerald-300 shadow-2xs' : 'bg-white border-slate-200' }}">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs shrink-0 {{ $muIdx == 0 ? 'bg-emerald-600 text-white' : ($muIdx == 1 ? 'bg-slate-400 text-white' : 'bg-amber-700 text-white') }}">
                                            {{ $muIdx + 1 }}
                                        </span>
                                        <div class="truncate">
                                            <div class="font-black text-xs text-slate-900 truncate">{{ $st['school_name'] }}</div>
                                            <div class="text-[10px] text-slate-500 font-medium">Juara Umum {{ $muIdx + 1 }}</div>
                                        </div>
                                    </div>
                                    <span class="font-mono font-black text-xs text-slate-900 shrink-0 bg-white px-2 py-0.5 rounded border border-slate-200">
                                        {{ $st['total_points'] }} pts
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-xs text-slate-400 font-medium italic">
                            Belum ada rekapitulasi nilai untuk tingkat Mula
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: REKAP JUARA 1 S/D HARAPAN 3 PER CABANG LOMBA (ACCORDION/CARDS) -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
            <div>
                <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-medal text-amber-500"></i>
                    <span>Rekapitulasi Pemenang Cabang Lomba (Juara 1 s/d Juara Harapan 3)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar pemenang resmi untuk setiap cabang lomba pada edisi {{ $event ? $event->title : 'ini' }}.</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-xl">
                Total: {{ count($allEventCategories) }} Cabang Lomba
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($allEventCategories as $catItem)
                @php
                    $wins = $categoryWinners[$catItem->id] ?? collect();
                    $badgeLvl = $catItem->level == 'Mula' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($catItem->level == 'Madya' ? 'bg-blue-50 text-blue-800 border-blue-200' : 'bg-amber-50 text-amber-800 border-amber-200');
                @endphp
                <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50 hover:bg-white transition shadow-2xs">
                    <div class="flex items-center justify-between gap-2 border-b border-slate-200 pb-2.5 mb-2.5">
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase border {{ $badgeLvl }}">
                                {{ $catItem->level }} &bull; {{ $catItem->code }}
                            </span>
                            <h4 class="font-black text-xs text-slate-900 mt-1">{{ $catItem->name }} ({{ $catItem->gender_category }})</h4>
                        </div>
                        <a href="{{ route('admin.competition-scores.input', $catItem->id) }}" class="p-1.5 text-slate-400 hover:text-slate-800 text-xs" title="Buka Lembar Skor">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>

                    @if($wins->isNotEmpty())
                        <div class="space-y-1.5">
                            @foreach($wins as $w)
                                @php
                                    $rank = $w->official_rank ?? $w->rank;
                                    $awardBadge = match($rank) {
                                        1 => 'bg-amber-100 text-amber-900 font-black border-amber-300',
                                        2 => 'bg-slate-200 text-slate-800 font-black border-slate-300',
                                        3 => 'bg-orange-100 text-orange-900 font-black border-orange-300',
                                        4 => 'bg-blue-50 text-blue-800 font-bold border-blue-200',
                                        5 => 'bg-indigo-50 text-indigo-800 font-bold border-indigo-200',
                                        6 => 'bg-purple-50 text-purple-800 font-bold border-purple-200',
                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                    $teamName = $w->team?->team_name ?? 'Regu Peserta';
                                    $schoolName = $w->team?->registration?->school_name ?: $teamName;
                                @endphp
                                <div class="flex items-center justify-between gap-2 text-[11px] p-1.5 rounded-lg bg-white border border-slate-100">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] uppercase border shrink-0 {{ $awardBadge }}">
                                            {{ $w->award_title ?? "Juara {$rank}" }}
                                        </span>
                                        <span class="font-bold text-slate-800 truncate" title="{{ $schoolName }}">{{ $schoolName }}</span>
                                    </div>
                                    <span class="font-mono font-bold text-slate-700 text-[10px] shrink-0">
                                        {{ $w->final_score > 0 ? $w->final_score : '-' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-[11px] text-slate-400 italic">
                            Belum ada pemenang yang diputuskan
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- SECTION 3: RINGKASAN PORTOFOLIO KONTINGEN SEKOLAH (MODAL TRIGGER BUTTONS) -->
    @if(count($schoolPortfolios) > 0)
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/60 pb-4">
                <div>
                    <h3 class="font-black text-base sm:text-lg flex items-center gap-2 text-white">
                        <i class="fa-solid fa-school text-amber-400"></i>
                        <span>Portofolio Kontingen Sekolah (Lomba, Peserta & Riwayat Nilai)</span>
                    </h3>
                    <p class="text-xs text-slate-300 mt-0.5">Pilih sekolah di bawah untuk melihat rincian seluruh cabang lomba yang diikuti, nama siswa peserta, dan riwayat nilai awal hingga akhir.</p>
                </div>
                <span class="text-xs font-bold bg-slate-700/60 text-amber-300 px-3 py-1 rounded-xl border border-slate-600">
                    {{ count($schoolPortfolios) }} Sekolah Kontingen Terverifikasi
                </span>
            </div>

            <!-- School Pills -->
            <div class="flex flex-wrap gap-2.5 mt-4 max-h-48 overflow-y-auto pr-2">
                @foreach($schoolPortfolios as $cleanSchool => $port)
                    <button type="button" onclick="openSchoolModal('{{ addslashes($cleanSchool) }}')" class="px-3 py-1.5 rounded-xl bg-slate-800/90 hover:bg-amber-500 hover:text-slate-950 border border-slate-700 text-xs font-bold transition flex items-center gap-2 group shadow-2xs">
                        <span class="text-slate-200 group-hover:text-slate-950">{{ $port['school_name'] }}</span>
                        <span class="px-1.5 py-0.2 rounded-md bg-slate-700 group-hover:bg-slate-900 group-hover:text-white text-[10px] font-mono">
                            {{ $port['teams']->count() }} Regu
                        </span>
                        @if($port['awards']->count() > 0)
                            <span class="text-amber-400 group-hover:text-slate-950 font-black text-[11px]">🏆 {{ $port['awards']->count() }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Statistic Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
        <!-- Total Regu -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Regu</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_teams'] }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">{{ $stats['total_schools'] }} Kontingen Sekolah</div>
        </div>

        <!-- Putra -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['gender' => 'Putra'])) }}" class="p-4 rounded-2xl border transition {{ $gender == 'Putra' ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-blue-300' }}">
            <div class="text-[11px] font-bold {{ $gender == 'Putra' ? 'text-blue-100' : 'text-blue-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-mars"></i> Regu Putra
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['putra_teams'] }}</div>
            <div class="text-[10px] {{ $gender == 'Putra' ? 'text-blue-100' : 'text-slate-500' }} mt-0.5">Kategori Laki-laki</div>
        </a>

        <!-- Putri -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['gender' => 'Putri'])) }}" class="p-4 rounded-2xl border transition {{ $gender == 'Putri' ? 'bg-rose-600 text-white border-rose-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-rose-300' }}">
            <div class="text-[11px] font-bold {{ $gender == 'Putri' ? 'text-rose-100' : 'text-rose-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-venus"></i> Regu Putri
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['putri_teams'] }}</div>
            <div class="text-[10px] {{ $gender == 'Putri' ? 'text-rose-100' : 'text-slate-500' }} mt-0.5">Kategori Perempuan</div>
        </a>

        <!-- Mula -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Mula'])) }}" class="p-4 rounded-2xl border transition {{ $level == 'Mula' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-emerald-300' }}">
            <div class="text-[11px] font-bold {{ $level == 'Mula' ? 'text-emerald-100' : 'text-emerald-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-child-reaching"></i> PMR Mula
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['mula_teams'] }}</div>
            <div class="text-[10px] {{ $level == 'Mula' ? 'text-emerald-100' : 'text-slate-500' }} mt-0.5">Jenjang SD/MI</div>
        </a>

        <!-- Madya -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Madya'])) }}" class="p-4 rounded-2xl border transition {{ $level == 'Madya' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-indigo-300' }}">
            <div class="text-[11px] font-bold {{ $level == 'Madya' ? 'text-indigo-100' : 'text-indigo-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-user-group"></i> PMR Madya
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['madya_teams'] }}</div>
            <div class="text-[10px] {{ $level == 'Madya' ? 'text-indigo-100' : 'text-slate-500' }} mt-0.5">Jenjang SMP/MTs</div>
        </a>

        <!-- Wira -->
        <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Wira'])) }}" class="p-4 rounded-2xl border transition {{ $level == 'Wira' ? 'bg-amber-600 text-white border-amber-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-amber-300' }}">
            <div class="text-[11px] font-bold {{ $level == 'Wira' ? 'text-amber-100' : 'text-amber-600' }} uppercase tracking-wider flex items-center gap-1">
                <i class="fa-solid fa-graduation-cap"></i> PMR Wira
            </div>
            <div class="text-2xl font-black mt-1">{{ $stats['wira_teams'] }}</div>
            <div class="text-[10px] {{ $level == 'Wira' ? 'text-amber-100' : 'text-slate-500' }} mt-0.5">Jenjang SMA/SMK</div>
        </a>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-4">
        <!-- Top Row Filters -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            
            <!-- Tingkat & Gender Filter Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tingkat -->
                <div class="inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->except('level'), [])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ !$level ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Semua Tingkat ({{ $stats['total_teams'] }})</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Mula'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Mula' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Mula ({{ $stats['mula_teams'] }})</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Madya'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Madya' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Madya ({{ $stats['madya_teams'] }})</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['level' => 'Wira'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Wira' ? 'bg-amber-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Wira ({{ $stats['wira_teams'] }})</a>
                </div>

                <!-- Gender -->
                <div class="inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->except('gender'), [])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ !$gender ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Semua Gender</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['gender' => 'Putra'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $gender == 'Putra' ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"><i class="fa-solid fa-mars mr-1"></i> Putra</a>
                    <a href="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['gender' => 'Putri'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $gender == 'Putri' ? 'bg-rose-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"><i class="fa-solid fa-venus mr-1"></i> Putri</a>
                </div>
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.competition-participants.index') }}" method="GET" class="w-full lg:w-auto flex items-center gap-2">
                @if(request('event_id')) <input type="hidden" name="event_id" value="{{ request('event_id') }}"> @endif
                @if($level) <input type="hidden" name="level" value="{{ $level }}"> @endif
                @if($gender) <input type="hidden" name="gender" value="{{ $gender }}"> @endif
                @if($categoryId) <input type="hidden" name="category_id" value="{{ $categoryId }}"> @endif
                
                <div class="relative w-full lg:w-72">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari Sekolah, Regu, No Urut..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-red-500">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold transition">Cari</button>
                @if($search || $categoryId || $gender || $level)
                    <a href="{{ route('admin.competition-participants.index', request('event_id') ? ['event_id' => request('event_id')] : []) }}" class="text-xs text-slate-500 hover:text-red-600 px-2 font-semibold">Reset</a>
                @endif
            </form>
        </div>

        <!-- Bottom Row: Specific Category Dropdown Filter & Batch Actions -->
        <div class="pt-3 border-t border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-xs font-bold text-slate-500 shrink-0">Cabang Lomba:</span>
                <select onchange="location = this.value;" class="bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 rounded-xl px-3 py-2 focus:outline-none focus:border-red-500 w-full md:w-auto">
                    <option value="{{ route('admin.competition-participants.index', array_merge(request()->except('category_id'), [])) }}">-- Seluruh Cabang Mata Lomba --</option>
                    @foreach($categories as $cat)
                        <option value="{{ route('admin.competition-participants.index', array_merge(request()->query(), ['category_id' => $cat->id])) }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            [{{ $cat->level }}] {{ $cat->name }} ({{ $cat->gender_category }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Auto-Number & Tools -->
            <div class="flex items-center gap-2 w-full md:w-auto justify-end flex-wrap">
                <form action="{{ route('admin.competition-participants.auto-assign-orders') }}" method="POST" onsubmit="return confirm('Generate No. Urut Peserta format A-001.. lalu B-001.. secara berurutan untuk regu yang terfilter saat ini?');">
                    @csrf
                    @if($categoryId) <input type="hidden" name="category_id" value="{{ $categoryId }}"> @endif
                    @if($level) <input type="hidden" name="level" value="{{ $level }}"> @endif
                    <input type="hidden" name="shuffle" value="0">
                    <button type="submit" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-list-ol text-indigo-600"></i> Auto No. Urut (A-001, B-001)
                    </button>
                </form>

                <form action="{{ route('admin.competition-participants.auto-assign-orders') }}" method="POST" onsubmit="return confirm('Acak / Undi No. Urut Peserta format A-001.. lalu B-001.. (Undian TM Digital) untuk regu yang terfilter saat ini?');">
                    @csrf
                    @if($categoryId) <input type="hidden" name="category_id" value="{{ $categoryId }}"> @endif
                    @if($level) <input type="hidden" name="level" value="{{ $level }}"> @endif
                    <input type="hidden" name="shuffle" value="1">
                    <button type="submit" class="bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-shuffle text-amber-600"></i> Acak Undian TM (A-001, B-001)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main List & Form for Bulk Updating Order Numbers -->
    <form action="{{ route('admin.competition-participants.bulk-update-orders') }}" method="POST" id="orders-form">
        @csrf

        <div class="space-y-6">
            @forelse($teamsByCategory as $catId => $categoryTeams)
                @php
                    $firstTeam = $categoryTeams->first();
                    $category = $firstTeam->category;
                    $isPa = $category?->gender_category === 'Putra';
                    $isPi = $category?->gender_category === 'Putri';
                @endphp

                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Category Card Header -->
                    <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 {{ $category?->level == 'Mula' ? 'bg-emerald-50/50' : ($category?->level == 'Madya' ? 'bg-blue-50/50' : 'bg-amber-50/50') }}">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-white text-base {{ $isPa ? 'bg-blue-600' : ($isPi ? 'bg-rose-600' : 'bg-slate-800') }} shadow-2xs">
                                @if($isPa) <i class="fa-solid fa-mars"></i>
                                @elseif($isPi) <i class="fa-solid fa-venus"></i>
                                @else <i class="fa-solid fa-users"></i> @endif
                            </span>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-black text-slate-900 text-base">
                                        {{ $category?->name ?? 'Cabang Lomba' }}
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $category?->level == 'Mula' ? 'bg-emerald-100 text-emerald-800' : ($category?->level == 'Madya' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        PMR {{ $category?->level }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-white border border-slate-200 text-slate-700">
                                        [{{ $category?->code ?? '-' }}]
                                    </span>
                                    @if($isPa)
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700">
                                            Putra (♂)
                                        </span>
                                    @elseif($isPi)
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700">
                                            Putri (♀)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600">
                                            Umum / Campuran
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5 font-medium">
                                    {{ $categoryTeams->count() }} Regu Terdaftar di Cabang ini
                                </div>
                            </div>
                        </div>

                        <!-- Action Links for this Category -->
                        <div class="flex items-center gap-2">
                            @if($category)
                                <a href="{{ route('admin.competition-scores.grid', $category->id) }}" class="bg-slate-900 hover:bg-black text-white px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                                    <i class="fa-solid fa-table-cells text-amber-400"></i> Buka Grid Penilaian
                                </a>
                                <a href="{{ route('admin.competition-scores.input', $category->id) }}" class="bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                                    <i class="fa-solid fa-calculator text-teal-600"></i> Lembar Nilai Juri
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Teams Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 text-slate-700 uppercase font-black border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3.5 w-28 text-center">No. Urut</th>
                                    <th class="px-4 py-3.5 min-w-[200px]">Asal Sekolah & Nama Regu</th>
                                    <th class="px-4 py-3.5 min-w-[240px]">Daftar Nama Anggota Regu</th>
                                    <th class="px-4 py-3.5 min-w-[170px]">Rincian Nilai & Waktu</th>
                                    <th class="px-4 py-3.5 min-w-[150px] text-center">Prestasi / Gelar</th>
                                    <th class="px-4 py-3.5 w-28 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($categoryTeams as $idx => $team)
                                    @php
                                        $reg = $team->registration;
                                        $members = is_array($team->members_list) ? $team->members_list : [];
                                        $award = $teamAwards[$team->id] ?? null;
                                        
                                        // Ambil skor tersimpan untuk tim ini
                                        $teamScore = $team->scores->where('competition_category_id', $category->id)->first();
                                        $finalScoreVal = $teamScore?->final_score;
                                        $timeRecordedVal = $teamScore?->time_recorded;
                                        $scoreDetails = is_array($teamScore?->score_details) ? $teamScore->score_details : (json_decode($teamScore?->score_details ?? '', true) ?: []);
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition {{ $award ? 'bg-amber-50/20' : '' }}">
                                        <!-- Order Number Input -->
                                        <td class="px-4 py-4 text-center">
                                            <input type="text" name="orders[{{ $team->id }}]" value="{{ $team->order_number ?? '' }}" placeholder="cth: A-001" class="w-24 px-2.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-center font-black font-mono text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-red-500 shadow-2xs">
                                        </td>

                                        <!-- Team & School Name -->
                                        <td class="px-4 py-4">
                                            <div class="font-extrabold text-slate-900 text-sm">
                                                {{ $reg?->school_name ?: $team->team_name }}
                                            </div>
                                            <div class="text-xs font-bold text-slate-700 mt-0.5">
                                                {{ $team->team_name }}
                                                @if($team->team_label)
                                                    <span class="text-slate-500 font-normal">({{ $team->team_label }})</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($reg)
                                                    <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                        {{ $reg->registration_code }}
                                                    </span>
                                                    <button type="button" onclick="openSchoolModal('{{ addslashes(preg_replace('/\s*\([A-Za-z0-9\s]+\)$/', '', $reg->school_name)) }}')" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 underline">
                                                        Lihat Semua Lomba Sekolah Ini
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Members List (Nama-nama Peserta) -->
                                        <td class="px-4 py-4">
                                            @if(count($members) > 0)
                                                <div class="space-y-1">
                                                    @foreach($members as $mIdx => $m)
                                                        @php
                                                            $mName = is_array($m) ? ($m['name'] ?? '-') : $m;
                                                            $mRole = is_array($m) ? ($m['role'] ?? ($mIdx == 0 ? 'Ketua' : 'Anggota')) : ($mIdx == 0 ? 'Ketua' : 'Anggota');
                                                        @endphp
                                                        <div class="text-[11px] text-slate-800 font-semibold flex items-center gap-1.5">
                                                            <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold {{ $mIdx == 0 ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-700' }} shrink-0">
                                                                {{ $mIdx + 1 }}
                                                            </span>
                                                            <span class="truncate">{{ $mName }}</span>
                                                            @if($mIdx == 0)
                                                                <span class="text-[9px] bg-amber-100 text-amber-900 px-1.5 py-0.2 rounded font-black uppercase">Ketua</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                    @if($team->members_file_url)
                                                        <div class="pt-1">
                                                            <a href="{{ $team->members_file_url }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded transition">
                                                                <i class="fa-solid fa-file-excel"></i> Unduh File Excel Anggota
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">Belum diisi nama anggota</span>
                                                @if($team->members_file_url)
                                                    <div class="pt-1">
                                                        <a href="{{ $team->members_file_url }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded transition">
                                                            <i class="fa-solid fa-file-excel"></i> File Excel
                                                        </a>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>

                                        <!-- Scores & Time Breakdown (Rincian Nilai dari Awal s/d Akhir) -->
                                        <td class="px-4 py-4">
                                            @if($finalScoreVal !== null && $finalScoreVal > 0)
                                                <div>
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-[10px] font-bold text-slate-500 uppercase">Nilai Akhir:</span>
                                                        <span class="font-mono font-black text-sm text-slate-900 bg-emerald-50 text-emerald-900 px-2 py-0.5 rounded border border-emerald-200">
                                                            {{ $finalScoreVal }}
                                                        </span>
                                                    </div>
                                                    @if($timeRecordedVal)
                                                        <div class="text-[10px] font-medium text-slate-500 mt-1 flex items-center gap-1">
                                                            <i class="fa-solid fa-stopwatch text-indigo-500"></i>
                                                            <span>Waktu Tempuh: <strong>{{ $timeRecordedVal }}</strong></span>
                                                        </div>
                                                    @endif
                                                    
                                                    {{-- Rincian Pos / Subtotals jika ada --}}
                                                    @if(!empty($scoreDetails['subtotals']))
                                                        <div class="mt-1 flex flex-wrap gap-1">
                                                            @foreach(array_slice($scoreDetails['subtotals'], 0, 3) as $kPos => $vPos)
                                                                <span class="text-[9px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-mono" title="Pos {{ $kPos }}: {{ $vPos }}">
                                                                    P{{ $kPos }}: {{ $vPos }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @elseif(!empty($scoreDetails['score']))
                                                        <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                                            Babak: {{ $scoreDetails['score'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">
                                                    <i class="fa-solid fa-clock text-[9px]"></i> Belum dinilai
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Award / Championship Status -->
                                        <td class="px-4 py-4 text-center">
                                            @if($award)
                                                @php
                                                    $rAward = $award['rank'] ?? 1;
                                                    $awardStyle = match($rAward) {
                                                        1 => 'bg-amber-100 text-amber-950 border-amber-400 font-black shadow-2xs',
                                                        2 => 'bg-slate-200 text-slate-900 border-slate-300 font-black',
                                                        3 => 'bg-orange-100 text-orange-950 border-orange-300 font-black',
                                                        4 => 'bg-blue-100 text-blue-900 border-blue-300 font-bold',
                                                        5 => 'bg-indigo-100 text-indigo-900 border-indigo-300 font-bold',
                                                        6 => 'bg-purple-100 text-purple-900 border-purple-300 font-bold',
                                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                                    };
                                                    $trophyIcon = match($rAward) {
                                                        1 => '🥇',
                                                        2 => '🥈',
                                                        3 => '🥉',
                                                        default => '🎖️',
                                                    };
                                                @endphp
                                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs border {{ $awardStyle }}">
                                                    <span>{{ $trophyIcon }}</span>
                                                    <span>{{ $award['award_title'] }}</span>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fa-solid fa-check text-[9px]"></i> Terverifikasi
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-4 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                @if($reg)
                                                    <button type="button" onclick="openSchoolModal('{{ addslashes(preg_replace('/\s*\([A-Za-z0-9\s]+\)$/', '', $reg->school_name)) }}')" class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition" title="Lihat Portofolio Lengkap Sekolah Ini">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>
                                                @endif
                                                <button type="button" onclick="openEditModal({{ $team->id }}, '{{ addslashes($team->team_name) }}', '{{ addslashes($team->team_label ?? '') }}', '{{ $team->order_number ?? '' }}', {{ json_encode($members) }})" class="p-2 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="Edit Detail Regu & Anggota">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-base">Belum Ada Peserta Terverifikasi yang Sesuai Filter</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Pastikan pendaftaran kontingen sekolah sudah diverifikasi di menu <a href="{{ route('admin.competition-registrations.index') }}" class="text-red-600 font-bold underline">Verifikasi Pendaftar</a>.
                    </p>
                </div>
            @endforelse
        </div>

        @if($teams->isNotEmpty())
            <!-- Sticky Bottom Submit Footer -->
            <div class="mt-8 sticky bottom-4 z-20 bg-white/95 backdrop-blur-md p-5 rounded-2xl shadow-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-500 text-sm"></i>
                    <span>Nomor urut tampil yang diisi di atas akan menjadi nomor undian resmi pada lembar penilaian juri.</span>
                </div>
                <button type="submit" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-extrabold text-xs shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan Nomor Urut
                </button>
            </div>
        @endif
    </form>

</div>

<!-- MODAL PORTOFOLIO LENGKAP KONTINGEN SEKOLAH -->
<div id="schoolModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-slate-200 space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-school"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-lg leading-tight" id="schoolModalTitle">Detail Partisipasi Kontingen</h3>
                    <div class="text-xs text-slate-500 font-medium" id="schoolModalSubtitle">Portofolio Lomba & Rincian Nilai</div>
                </div>
            </div>
            <button type="button" onclick="closeSchoolModal()" class="text-slate-400 hover:text-slate-600 p-2">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <div id="schoolModalContent" class="space-y-4 text-xs">
            <!-- Dynamic Content Injected by JS -->
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeSchoolModal()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold">
                Tutup Jendela
            </button>
        </div>
    </div>
</div>

<!-- Modal Edit Regu & Anggota -->
<div id="editTeamModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-900 text-base" id="modalTitle">Edit Detail Regu Peserta</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editTeamForm" method="POST" action="">
            @csrf
            <div class="space-y-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">No. Urut Peserta</label>
                    <input type="text" id="modalOrderNumber" name="order_number" placeholder="Contoh: A-001 / B-001" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-mono font-bold focus:bg-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Lengkap Regu</label>
                    <input type="text" id="modalTeamName" name="team_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-bold focus:bg-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Label Regu (cth: Regu A / Regu Putra 1)</label>
                    <input type="text" id="modalTeamLabel" name="team_label" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">
                        Daftar Nama Anggota Regu <span class="text-slate-400 font-normal">(1 nama per baris)</span>
                    </label>
                    <textarea id="modalMembersRaw" name="members_raw" rows="6" placeholder="1. Dimas Arya (Ketua)&#10;2. Fadli Zaki&#10;3. M. Rizky&#10;4. Farhan Hadi" class="w-full p-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:border-red-500 font-mono text-xs"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-pmr-primary hover:bg-pmr-dark text-white text-xs font-bold shadow-md shadow-red-950/20">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const schoolPortfoliosData = @json($schoolPortfolios);

    function openSchoolModal(cleanSchoolName) {
        const port = schoolPortfoliosData[cleanSchoolName];
        if (!port) return;

        document.getElementById('schoolModalTitle').innerText = port.school_name;
        document.getElementById('schoolModalSubtitle').innerText = `Jenjang PMR ${port.level} • No. Registrasi: ${port.registration_code || '-'} • Pembina: ${port.advisor_name || '-'} (${port.advisor_phone || '-'})`;

        let html = '';

        // Header Summary Box
        html += `
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Akumulasi Poin Juara Umum</span>
                    <span class="font-mono font-black text-xl text-slate-900">${port.total_points} <span class="text-xs font-normal">pts</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-amber-100 text-amber-900 font-black px-3 py-1 rounded-xl text-xs border border-amber-300">
                        🏆 ${port.awards.length} Gelar Juara Diraih
                    </span>
                    <span class="bg-blue-100 text-blue-900 font-bold px-3 py-1 rounded-xl text-xs border border-blue-200">
                        ${port.teams.length} Regu Bertanding
                    </span>
                </div>
            </div>
        `;

        // Teams and Categories Breakdown
        html += '<h4 class="font-black text-slate-900 text-sm mt-3">Cabang Lomba yang Diikuti & Rincian Peserta/Nilai:</h4>';
        html += '<div class="space-y-3">';

        port.teams.forEach((tm, idx) => {
            const cat = tm.category || {};
            const aw = port.awards.find(a => a.team_name === tm.team_name);
            const scoreObj = tm.scores && tm.scores.length > 0 ? tm.scores[0] : null;
            const finalScore = scoreObj ? scoreObj.final_score : '-';
            const timeRecorded = scoreObj ? scoreObj.time_recorded : null;
            
            let membersList = [];
            if (Array.isArray(tm.members_list)) {
                membersList = tm.members_list.map(m => typeof m === 'object' ? m.name : m).filter(Boolean);
            }

            html += `
                <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-2xs space-y-2">
                    <div class="flex items-center justify-between flex-wrap gap-2 border-b border-slate-100 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-slate-100 border border-slate-200 text-slate-700">
                                ${cat.code || 'LOMBA'}
                            </span>
                            <span class="font-black text-slate-900 text-sm">${cat.name || 'Cabang Lomba'} (${cat.gender_category || 'Umum'})</span>
                            <span class="text-xs text-slate-500">• ${tm.team_name}</span>
                        </div>
                        <div>
                            ${aw ? `<span class="bg-amber-100 text-amber-950 px-2.5 py-0.5 rounded-lg font-black text-xs border border-amber-300">🏆 ${aw.award_title}</span>` : '<span class="text-slate-400 font-medium text-[11px]">Terverifikasi</span>'}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs pt-1">
                        <div>
                            <span class="font-bold text-slate-400 uppercase text-[10px] block mb-1">Daftar Anggota Regu:</span>
                            ${membersList.length > 0 ? `
                                <ul class="space-y-0.5 text-slate-700">
                                    ${membersList.map((m, mI) => `<li><i class="fa-solid fa-user text-[9px] text-slate-400 mr-1"></i> ${mI+1}. ${m} ${mI === 0 ? '<span class="text-[9px] text-amber-600 font-black">(Ketua)</span>' : ''}</li>`).join('')}
                                </ul>
                            ` : '<span class="text-slate-400 italic">Nama anggota belum dicatat</span>'}
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="font-bold text-slate-400 uppercase text-[10px] block mb-1">Rincian Skor & Waktu Tempuh:</span>
                            <div class="flex items-center justify-between text-xs mt-1">
                                <span class="text-slate-600">Nilai Akhir Resmi:</span>
                                <span class="font-mono font-black text-slate-900 text-sm bg-white px-2 py-0.5 rounded border border-slate-200">${finalScore}</span>
                            </div>
                            ${timeRecorded ? `
                                <div class="flex items-center justify-between text-xs mt-1">
                                    <span class="text-slate-600">Waktu Tempuh:</span>
                                    <span class="font-mono font-bold text-indigo-700">${timeRecorded}</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';

        document.getElementById('schoolModalContent').innerHTML = html;
        document.getElementById('schoolModal').classList.remove('hidden');
    }

    function closeSchoolModal() {
        document.getElementById('schoolModal').classList.add('hidden');
    }

    function openEditModal(teamId, teamName, teamLabel, orderNumber, members) {
        document.getElementById('modalOrderNumber').value = orderNumber || '';
        document.getElementById('modalTeamName').value = teamName || '';
        document.getElementById('modalTeamLabel').value = teamLabel || '';
        
        let membersText = '';
        if (Array.isArray(members)) {
            membersText = members.map(m => typeof m === 'object' ? (m.name || '') : m).filter(Boolean).join('\n');
        }
        document.getElementById('modalMembersRaw').value = membersText;

        const form = document.getElementById('editTeamForm');
        form.action = `/admin/competition-participants/${teamId}`;

        document.getElementById('editTeamModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editTeamModal').classList.add('hidden');
    }
</script>
@endpush
