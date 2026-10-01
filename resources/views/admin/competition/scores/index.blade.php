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

@push('styles')
<style>
    @keyframes badgeBlink {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.15));
        }
        50% {
            opacity: 0.55;
            transform: scale(1.03);
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.25));
        }
    }
    .animate-badge-blink {
        animation: badgeBlink 1.4s ease-in-out infinite;
    }
</style>
@endpush

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
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between hover:border-red-400 transition hover:shadow-md">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="font-mono text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-600 px-2.5 py-1 rounded-md border border-slate-200">
                            {{ $cat->code ?? 'LOMBA' }}
                        </span>
                        <span class="text-xs font-extrabold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">
                            PMR {{ $cat->level }}
                        </span>
                    </div>

                    <!-- Gender Category Badge & Mascot Side-by-Side -->
                    <div class="mb-4 flex items-center justify-between gap-3 min-h-[76px] bg-slate-50/60 p-2.5 rounded-2xl border border-slate-100">
                        <!-- Left: 3x Larger Blinking Gender Category Badge -->
                        <div class="flex-grow">
                            @if($cat->gender_category === 'Putra')
                                <div class="animate-badge-blink inline-flex items-center gap-2 px-3.5 py-2 rounded-xl font-black text-xs sm:text-sm bg-blue-600 text-white shadow-lg shadow-blue-600/30 border-2 border-blue-300 uppercase tracking-wider">
                                    <i class="fa-solid fa-mars text-base text-blue-200 animate-pulse"></i> KATEGORI PUTRA
                                </div>
                            @elseif($cat->gender_category === 'Putri')
                                <div class="animate-badge-blink inline-flex items-center gap-2 px-3.5 py-2 rounded-xl font-black text-xs sm:text-sm bg-rose-600 text-white shadow-lg shadow-rose-600/30 border-2 border-rose-300 uppercase tracking-wider">
                                    <i class="fa-solid fa-venus text-base text-rose-200 animate-pulse"></i> KATEGORI PUTRI
                                </div>
                            @else
                                <div class="animate-badge-blink inline-flex items-center gap-2 px-3.5 py-2 rounded-xl font-black text-xs sm:text-sm bg-slate-800 text-white shadow-lg shadow-slate-900/30 border-2 border-slate-600 uppercase tracking-wider">
                                    <i class="fa-solid fa-users text-base text-slate-300 animate-pulse"></i> KATEGORI UMUM
                                </div>
                            @endif
                        </div>

                        <!-- Right: Transparent Mascot Photo aligned with Badge -->
                        @php
                            $mascotFile = null;
                            if ($cat->level === 'Mula') {
                                if ($cat->gender_category === 'Putra') {
                                    $mascotFile = 'Kategori_Putra.png';
                                } elseif ($cat->gender_category === 'Putri') {
                                    $mascotFile = 'Kategori_Putri.png';
                                } else {
                                    $mascotFile = 'Kategori_Umum.png';
                                }
                            } else {
                                $lvlKey = strtolower($cat->level); // madya, wira
                                $genderKey = 'umum';
                                if ($cat->gender_category === 'Putra') {
                                    $genderKey = 'putra';
                                } elseif ($cat->gender_category === 'Putri') {
                                    $genderKey = 'putri';
                                }
                                $mascotFile = "pmr_{$lvlKey}_{$genderKey}.png";
                            }
                            $mascotPath = $mascotFile ? public_path("images/mascot/{$mascotFile}") : null;
                        @endphp
                        @if($mascotPath && file_exists($mascotPath))
                            <div class="shrink-0 w-24 h-28 sm:w-28 sm:h-32 -my-3 flex items-center justify-center">
                                <img src="{{ asset('images/mascot/' . $mascotFile) }}?v={{ filemtime($mascotPath) }}" alt="Maskot {{ $cat->gender_category }}" class="max-h-full max-w-full object-contain filter drop-shadow-md hover:scale-110 transition duration-300">
                            </div>
                        @endif
                    </div>

                    <h3 class="text-xl font-black text-slate-900 leading-snug">
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
