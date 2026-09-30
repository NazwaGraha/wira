@extends('layouts.app')

@section('title', 'Live Klasemen & Juara Umum - ' . ($event->title ?? ''))

@section('content')
<div class="bg-slate-900 text-white pt-32 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <div class="inline-flex items-center gap-2 bg-red-500/20 text-red-400 border border-red-500/30 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-red-400 animate-ping"></span> Live Real-time Leaderboard
                </div>
                <h1 class="text-3xl sm:text-4xl font-black">Papan Skor & Klasemen Juara</h1>
                <p class="text-slate-400 text-sm mt-1">Hasil penilaian resmi dewan juri {{ $event->title ?? 'SUA BHAKTI BERKARYA III' }}</p>
            </div>

            <!-- Level Selector Tabs -->
            <div class="bg-slate-800 p-1.5 rounded-2xl border border-slate-700 flex w-full sm:w-auto gap-1">
                <a href="{{ route('lomba.scoreboard', ['level' => 'Mula']) }}" class="flex-1 sm:flex-initial text-center px-3 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition {{ $level == 'Mula' ? 'bg-red-600 text-white shadow-lg shadow-red-900/40' : 'text-slate-400 hover:text-white' }}">
                    Mula (SD)
                </a>
                <a href="{{ route('lomba.scoreboard', ['level' => 'Madya']) }}" class="flex-1 sm:flex-initial text-center px-3 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition {{ $level == 'Madya' ? 'bg-red-600 text-white shadow-lg shadow-red-900/40' : 'text-slate-400 hover:text-white' }}">
                    Madya (SMP)
                </a>
                <a href="{{ route('lomba.scoreboard', ['level' => 'Wira']) }}" class="flex-1 sm:flex-initial text-center px-3 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition {{ $level == 'Wira' ? 'bg-red-600 text-white shadow-lg shadow-red-900/40' : 'text-slate-400 hover:text-white' }}">
                    Wira (SMA)
                </a>
            </div>
        </div>
    </div>
</div>

<div class="bg-slate-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- SECTION 1: JUARA UMUM PODIUM & STANDINGS -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                <div>
                    <span class="bg-amber-100 text-amber-900 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Peringkat Tertinggi</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-2">Klasemen Juara Umum — PMR {{ $level }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Akumulasi seluruh perolehan poin dari setiap cabang mata lomba yang dimenangkan.</p>
                </div>
            </div>

            <!-- Top 3 Podium (If available) -->
            @if($overallStandings->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    @foreach($overallStandings->take(3) as $st)
                        <div class="rounded-2xl p-6 border-2 relative overflow-hidden flex flex-col justify-between {{ $st['overall_rank'] == 1 ? 'bg-amber-500/10 border-amber-400 text-amber-950 shadow-md' : ($st['overall_rank'] == 2 ? 'bg-slate-100 border-slate-300 text-slate-900' : 'bg-orange-50 border-orange-200 text-orange-950') }}">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-xs uppercase tracking-wider px-3 py-1 rounded-full {{ $st['overall_rank'] == 1 ? 'bg-amber-500 text-white' : ($st['overall_rank'] == 2 ? 'bg-slate-400 text-white' : 'bg-orange-400 text-white') }}">
                                    Juara {{ $st['overall_rank'] }}
                                </span>
                                <div class="text-3xl">
                                    {{ $st['overall_rank'] == 1 ? '🥇' : ($st['overall_rank'] == 2 ? '🥈' : '🥉') }}
                                </div>
                            </div>
                            
                            <div class="my-4">
                                <h3 class="text-lg font-black uppercase leading-tight">{{ $st['school_name'] }}</h3>
                                <div class="text-xs text-slate-500 mt-1">Total Kemenangan: {{ count($st['details']) }} Cabang</div>
                            </div>

                            <div class="pt-4 border-t border-slate-200/60 flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-500 uppercase">Total Poin:</span>
                                <span class="text-2xl font-black text-red-600 font-mono">{{ $st['total_points'] }} <span class="text-xs font-normal">pts</span></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Full Standings Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-700 text-xs font-bold uppercase border-y border-slate-200">
                        <tr>
                            <th class="px-6 py-4 w-20 text-center">Peringkat</th>
                            <th class="px-6 py-4">Nama Sekolah / Kontingen</th>
                            <th class="px-6 py-4">Rincian Perolehan Poin Cabang</th>
                            <th class="px-6 py-4 text-right w-36">Total Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($overallStandings as $st)
                            <tr class="hover:bg-slate-50/80 transition {{ $st['overall_rank'] <= 3 ? 'font-bold bg-amber-50/30' : '' }}">
                                <td class="px-6 py-4 text-center">
                                    @if($st['overall_rank'] == 1)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-400 text-amber-950 font-black shadow-sm">1</span>
                                    @elseif($st['overall_rank'] == 2)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-300 text-slate-800 font-black shadow-sm">2</span>
                                    @elseif($st['overall_rank'] == 3)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-300 text-orange-950 font-black shadow-sm">3</span>
                                    @else
                                        <span class="text-slate-400 font-bold">{{ $st['overall_rank'] }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-900 text-base uppercase">{{ $st['school_name'] }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($st['details'] as $catName => $pts)
                                            <span class="bg-slate-100 border border-slate-200 text-slate-700 text-[11px] font-semibold px-2 py-0.5 rounded-lg">
                                                {{ $catName }}: <strong class="text-red-600">+{{ $pts }}</strong>
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">Belum ada poin</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-lg font-black text-red-600 font-mono">{{ $st['total_points'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-10 text-slate-400 text-sm">
                                    Belum ada hasil pertandingan yang selesai dinilai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        <!-- SECTION 2: PER-CATEGORY LEADERBOARD -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                <div>
                    <span class="bg-red-100 text-red-900 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">Penilaian Per Cabang</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-2">Papan Nilai Cabang Lomba</h2>
                </div>

                <!-- Select Category Dropdown / Tabs -->
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('lomba.scoreboard', ['level' => $level, 'category_id' => $cat->id]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $selectedCategory && $selectedCategory->id == $cat->id ? 'bg-red-600 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $cat->name }} ({{ $cat->gender_category }})
                        </a>
                    @endforeach
                </div>
            </div>

            @if($selectedCategory)
                <div class="bg-slate-50 p-4 rounded-2xl mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-slate-800 text-lg">{{ $selectedCategory->display_name }}</h3>
                        <p class="text-xs text-slate-500">
                            Tipe Penilaian: <strong>{{ ucwords(str_replace('_', ' ', $selectedCategory->scoring_type)) }}</strong> &bull; Aturan Poin Juara: <strong>{{ $selectedCategory->point_tier == 'tier_1' ? '10 / 8 / 6' : ($selectedCategory->point_tier == 'tier_2' ? '8 / 6 / 4' : '3 / 2 / 1') }} Poin</strong>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1 rounded-lg">
                            {{ $scores->count() }} Peserta Dinilai
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100 text-slate-700 text-xs font-bold uppercase border-y border-slate-200">
                            <tr>
                                <th class="px-6 py-3.5 w-20 text-center">Peringkat</th>
                                <th class="px-6 py-3.5">No. Urut & Nama Regu</th>
                                <th class="px-6 py-3.5">Rincian Komponen Nilai</th>
                                <th class="px-6 py-3.5 text-center">Waktu Tempuh</th>
                                <th class="px-6 py-3.5 text-right w-36">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($scores as $sc)
                                <tr class="hover:bg-slate-50 transition {{ $sc->is_disqualified ? 'bg-slate-900 text-slate-400' : ($sc->rank == 1 ? 'bg-purple-50 text-purple-950 font-bold' : ($sc->rank == 2 ? 'bg-amber-50/50' : '')) }}">
                                    <td class="px-6 py-4 text-center">
                                        @if($sc->is_disqualified)
                                            <span class="bg-red-900 text-red-200 text-[10px] font-black px-2 py-0.5 rounded">DISKUALIFIKASI</span>
                                        @elseif($sc->rank == 1)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-purple-600 text-white font-black text-xs">1</span>
                                        @elseif($sc->rank == 2)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-500 text-white font-black text-xs">2</span>
                                        @elseif($sc->rank == 3)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-400 text-white font-black text-xs">3</span>
                                        @else
                                            <span class="text-slate-500 font-bold text-xs">{{ $sc->rank ?: '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-base">{{ $sc->team?->team_name ?? '-' }}</div>
                                        <div class="text-xs text-slate-400">No. Urut: {{ $sc->team?->order_number ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        @if(!empty($sc->score_details))
                                            @if(isset($sc->score_details['written_score']))
                                                <span>Tertulis: <strong>{{ $sc->score_details['written_score'] }}</strong> ({{ $sc->score_details['written_time'] ?? '-' }}) &bull; Praktik: <strong>{{ $sc->score_details['practical_score'] }}</strong> ({{ $sc->score_details['practical_time'] ?? '-' }})</span>
                                            @elseif(isset($sc->score_details['criteria_1']))
                                                <span>Kriteria 1: <strong>{{ $sc->score_details['criteria_1'] }}</strong> &bull; Kriteria 2: <strong>{{ $sc->score_details['criteria_2'] }}</strong> @if(isset($sc->score_details['criteria_3'])) &bull; Kriteria 3: <strong>{{ $sc->score_details['criteria_3'] }}</strong> @endif</span>
                                            @elseif(isset($sc->score_details['likes']))
                                                <span>Likes: <strong>{{ $sc->score_details['likes'] }}</strong> &bull; Stickers: <strong>{{ $sc->score_details['stickers'] }}</strong></span>
                                            @else
                                                <span class="text-slate-400">Nilai Murni</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono text-xs">
                                        {{ $sc->time_recorded ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-lg font-black font-mono {{ $sc->is_disqualified ? 'text-red-400' : 'text-slate-900' }}">{{ $sc->final_score }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-slate-400 text-xs">
                                        Belum ada nilai yang diinputkan untuk mata lomba ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
