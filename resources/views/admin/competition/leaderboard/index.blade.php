@extends('layouts.admin')

@section('title', 'Rekap Juara Umum - PMR ' . $level)
@section('page_title', 'Rekapitulasi Poin & Klasemen Juara Umum')

@section('top_actions')
    <a href="{{ route('lomba.scoreboard', ['level' => $level]) }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2">
        <i class="fa-solid fa-tv"></i> Tampilkan di Proyektor
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Level Selector Tabs -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.competition-leaderboard.index', ['level' => 'Mula']) }}" class="px-6 py-3 rounded-xl font-extrabold text-sm transition {{ $level == 'Mula' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fa-solid fa-child-reaching mr-1.5"></i> PMR MULA (SD)
        </a>
        <a href="{{ route('admin.competition-leaderboard.index', ['level' => 'Madya']) }}" class="px-6 py-3 rounded-xl font-extrabold text-sm transition {{ $level == 'Madya' ? 'bg-red-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fa-solid fa-user-group mr-1.5"></i> PMR MADYA (SMP)
        </a>
        <a href="{{ route('admin.competition-leaderboard.index', ['level' => 'Wira']) }}" class="px-6 py-3 rounded-xl font-extrabold text-sm transition {{ $level == 'Wira' ? 'bg-amber-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fa-solid fa-graduation-cap mr-1.5"></i> PMR WIRA (SMA)
        </a>
    </div>

    <!-- Juara Umum Summary Podium Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($standings->take(3) as $st)
            <div class="bg-white rounded-2xl p-6 border-2 relative overflow-hidden flex flex-col justify-between shadow-sm {{ $st['rank'] == 1 ? 'border-amber-400 bg-amber-50/20' : ($st['rank'] == 2 ? 'border-slate-300' : 'border-orange-200') }}">
                <div class="flex items-center justify-between">
                    <span class="font-black text-xs uppercase tracking-wider px-3 py-1 rounded-full {{ $st['rank'] == 1 ? 'bg-amber-500 text-white' : ($st['rank'] == 2 ? 'bg-slate-400 text-white' : 'bg-orange-400 text-white') }}">
                        Juara Umum {{ $st['rank'] }}
                    </span>
                    <div class="text-3xl">
                        {{ $st['rank'] == 1 ? '🥇' : ($st['rank'] == 2 ? '🥈' : '🥉') }}
                    </div>
                </div>
                
                <div class="my-4">
                    <h3 class="text-lg font-black text-slate-900 uppercase leading-tight">{{ $st['school_name'] }}</h3>
                    <div class="text-xs text-slate-500 mt-1">Kontingen PMR {{ $level }}</div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500 uppercase">Perolehan Poin:</span>
                    <span class="text-2xl font-black text-red-600 font-mono">{{ $st['total_points'] }} <span class="text-xs font-normal">pts</span></span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Matrix Standings Table (Excel Style) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-black text-base text-slate-900">Tabel Rekapitulasi Poin Juara Umum</h3>
                <p class="text-xs text-slate-500">Matriks akumulasi perolehan poin dari semua cabang lomba tingkat PMR {{ $level }}.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-800 uppercase font-black border-y border-slate-200">
                    <tr>
                        <th class="p-3 w-12 text-center">Rank</th>
                        <th class="p-3 min-w-[180px]">Nama Sekolah</th>
                        @foreach($categories as $cat)
                            <th class="p-3 text-center border-l border-slate-200 font-bold" title="{{ $cat->display_name }}">
                                {{ $cat->code ?? $cat->name }}
                            </th>
                        @endforeach
                        <th class="p-3 text-right border-l-2 border-slate-800 w-28 bg-slate-200 font-black">Total Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($standings as $st)
                        <tr class="hover:bg-slate-50 transition {{ $st['rank'] <= 3 ? 'bg-amber-50/40 font-bold' : '' }}">
                            <td class="p-3 text-center">
                                @if($st['rank'] == 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-amber-950 font-black text-xs">1</span>
                                @elseif($st['rank'] == 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-300 text-slate-800 font-black text-xs">2</span>
                                @elseif($st['rank'] == 3)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-300 text-orange-950 font-black text-xs">3</span>
                                @else
                                    <span class="text-slate-400">{{ $st['rank'] }}</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <div class="font-extrabold text-slate-900 uppercase">{{ $st['school_name'] }}</div>
                            </td>
                            @foreach($categories as $cat)
                                <td class="p-3 text-center border-l border-slate-100 font-mono">
                                    @php $pts = $st['categories'][$cat->id] ?? 0; @endphp
                                    @if($pts > 0)
                                        <span class="bg-red-50 text-red-700 font-black px-2 py-0.5 rounded border border-red-200">{{ $pts }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="p-3 text-right border-l-2 border-slate-800 bg-slate-50 font-mono font-black text-sm text-red-600">
                                {{ $st['total_points'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + $categories->count() }}" class="text-center py-10 text-slate-400">
                                Belum ada data sekolah yang terdaftar atau dinilai pada tingkatan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Ketentuan Poin Box -->
        <div class="mt-6 p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 space-y-1">
            <div class="font-bold text-slate-800 mb-1">Ketentuan Poin Juara Umum (Sesuai Regulasi SBB III):</div>
            <div>&bull; <strong>LPP, LCT, Mading Kreasi, Olimpiade Kepalangmerahan:</strong> Juara 1 = 10 Poin, Juara 2 = 8 Poin, Juara 3 = 6 Poin</div>
            <div>&bull; <strong>LKTR (Tandu), LKCT (Cuci Tangan), Mewarnai:</strong> Juara 1 = 8 Poin, Juara 2 = 6 Poin, Juara 3 = 4 Poin</div>
            <div>&bull; <strong>PMR Favorite:</strong> Juara 1 = 3 Poin, Juara 2 = 2 Poin, Juara 3 = 1 Poin</div>
        </div>
    </div>

</div>
@endsection
