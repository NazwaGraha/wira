@extends('layouts.admin')

@section('title', 'Penilaian Lomba PMR')
@section('page_title', 'Input Nilai Dewan Juri')

@section('top_actions')
    <a href="{{ route('admin.competition-leaderboard.index') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow transition flex items-center gap-2">
        <i class="fa-solid fa-trophy"></i> Lihat Rekap Juara Umum
    </a>
    <a href="{{ route('lomba.scoreboard') }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2">
        <i class="fa-solid fa-tv"></i> Buka Layar Proyektor
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Level Tabs -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.competition-scores.index', ['level' => 'Mula']) }}" class="px-6 py-3 rounded-xl font-extrabold text-sm transition {{ $level == 'Mula' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fa-solid fa-child-reaching mr-1.5"></i> PMR MULA (SD)
        </a>
        <a href="{{ route('admin.competition-scores.index', ['level' => 'Madya']) }}" class="px-6 py-3 rounded-xl font-extrabold text-sm transition {{ $level == 'Madya' ? 'bg-red-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fa-solid fa-user-group mr-1.5"></i> PMR MADYA (SMP)
        </a>
        <a href="{{ route('admin.competition-scores.index', ['level' => 'Wira']) }}" class="px-6 py-3 rounded-xl font-extrabold text-sm transition {{ $level == 'Wira' ? 'bg-amber-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fa-solid fa-graduation-cap mr-1.5"></i> PMR WIRA (SMA)
        </a>
    </div>

    <!-- Category Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $cat)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between hover:border-red-400 transition">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                            {{ $cat->code ?? 'LOMBA' }}
                        </span>
                        @if($cat->gender_category === 'Putra')
                            <span class="text-xs font-extrabold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-md flex items-center gap-1">
                                <i class="fa-solid fa-mars text-blue-600"></i> Putra
                            </span>
                        @elseif($cat->gender_category === 'Putri')
                            <span class="text-xs font-extrabold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-md flex items-center gap-1">
                                <i class="fa-solid fa-venus text-rose-600"></i> Putri
                            </span>
                        @else
                            <span class="text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md flex items-center gap-1">
                                <i class="fa-solid fa-users text-slate-500"></i> Umum
                            </span>
                        @endif
                    </div>

                    <h3 class="text-lg font-black text-slate-900 leading-snug">
                        {{ $cat->name }}
                    </h3>

                    <div class="text-xs text-slate-500 mt-2 space-y-1">
                        <div>Tipe Penilaian: <strong class="text-slate-700">{{ ucwords(str_replace('_', ' ', $cat->scoring_type)) }}</strong></div>
                        <div>Poin Juara Umum: <strong class="text-red-600">{{ $cat->point_tier == 'tier_1' ? '10 / 8 / 6' : ($cat->point_tier == 'tier_2' ? '8 / 6 / 4' : '3 / 2 / 1') }} Poin</strong></div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-lg">
                        {{ $cat->teams_count }} Tim Terdaftar
                    </span>

                    <a href="{{ route('admin.competition-scores.input', $cat->id) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square"></i> Input Nilai Juri
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
