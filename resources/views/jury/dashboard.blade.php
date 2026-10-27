@extends('jury.layout')

@section('title', 'Dashboard Penilaian Juri')

@section('content')
<div class="flex-1 flex flex-col bg-slate-100 min-h-screen pb-20">

    <!-- Top Status Bar & Profile Header (Matching Mockup Screen 1) -->
    <div class="bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white p-5 pb-7 shadow-lg">
        <div class="flex items-center justify-between text-xs text-red-100 mb-4 font-semibold">
            <span class="flex items-center gap-1.5 font-bold">
                <i class="fa-solid fa-flag text-amber-300"></i> Sua Bhakti Berkarya IV
            </span>
            <span class="flex items-center gap-1.5 bg-black/20 px-2.5 py-0.5 rounded-full text-[10px]">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Online
            </span>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white font-black text-xl border border-white/30 shadow-md">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-red-200 font-bold">Selamat Bertugas,</div>
                    <h2 class="font-black text-base text-white leading-tight">{{ $user->name ?? $user->full_name ?? 'Petugas Juri' }}</h2>
                    <div class="text-[11px] text-red-100 flex items-center gap-1.5 mt-0.5">
                        <i class="fa-solid fa-id-badge text-[10px] text-amber-300"></i>
                        <span>{{ $user->role_label ?? (($user->role ?? null) === 'jury' ? 'Juri Lapangan' : ($user->position ?? 'Petugas')) }} &bull; {{ $user->username ?? $user->nri ?? $user->email ?? 'Petugas' }}</span>
                    </div>
                </div>
            </div>

            <!-- Logout Button -->
            <form action="{{ route('jury.logout') }}" method="POST" onsubmit="return confirm('Apakah Anda ingin keluar dari akun juri?')">
                @csrf
                <button type="submit" class="w-9 h-9 rounded-2xl bg-white/10 hover:bg-white/25 flex items-center justify-center text-white text-sm transition shadow-xs border border-white/20" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Summary Card (Floating Over Header) -->
    <div class="px-4 -mt-4">
        <div class="bg-white rounded-3xl p-4 shadow-md border border-slate-200/80 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tugas Penilaian Anda</span>
                <div class="text-base font-black text-slate-900 mt-0.5">
                    {{ $categories->count() }} Mata Lomba Aktif
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 font-black text-xs border border-emerald-200">
                    <i class="fa-solid fa-check text-[10px]"></i> {{ $totalTeamsScored }} / {{ $totalTeamsAssigned }} Selesai
                </span>
            </div>
        </div>
    </div>

    <!-- Main List of Assigned Competition Branches -->
    <div class="p-4 space-y-3 flex-1">
        <div class="text-[11px] font-black uppercase tracking-wider text-slate-400 flex items-center justify-between px-1">
            <span>PILIH MATA LOMBA DIUJI:</span>
            <span class="text-[10px] font-mono text-slate-500 font-bold">{{ $categories->count() }} TUGAS</span>
        </div>

        @forelse ($categories as $cat)
            @php
                $isMula = $cat->level == 'Mula';
                $isMadya = $cat->level == 'Madya';
                $isWira = $cat->level == 'Wira';
                $levelColor = $isMula ? 'emerald' : ($isMadya ? 'sky' : 'amber');
            @endphp
            <a href="{{ route('jury.scoring', $cat->id) }}" class="block bg-white rounded-3xl p-4 border-2 
                {{ $isMula ? 'border-emerald-500/80 hover:border-emerald-600' : ($isMadya ? 'border-blue-500/80 hover:border-blue-600' : 'border-amber-400 hover:border-amber-500') }}
                shadow-sm hover:shadow-md relative overflow-hidden transition active:scale-98">
                
                <!-- Side Level Strip -->
                <div class="absolute top-0 right-0 w-2 h-full 
                    {{ $isMula ? 'bg-emerald-500' : ($isMadya ? 'bg-blue-600' : 'bg-amber-400') }}"></div>

                <!-- Top Category Meta -->
                <div class="flex items-center justify-between mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase
                        {{ $isMula ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($isMadya ? 'bg-sky-100 text-sky-800 border border-sky-300' : 'bg-amber-100 text-amber-900 border border-amber-300') }}">
                        <i class="fa-solid fa-circle text-[6px]"></i> PMR {{ $cat->level }}
                    </span>
                    
                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full
                        {{ $cat->scored_teams_count == $cat->teams_count && $cat->teams_count > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                        {{ $cat->scored_teams_count }} / {{ $cat->teams_count }} Selesai ({{ $cat->progress_percent }}%)
                    </span>
                </div>

                <!-- Category Name -->
                <h3 class="font-black text-sm text-slate-900">
                    {{ $cat->name }}
                </h3>

                <!-- Submeta & Call-to-Action -->
                <div class="text-xs text-slate-500 mt-1 flex items-center justify-between pt-2 border-t border-slate-100">
                    <span class="font-medium text-[11px]">
                        {{ $cat->code }} &bull; {{ $cat->gender_category ?? 'Umum' }}
                    </span>
                    <span class="font-black text-xs flex items-center gap-1.5
                        {{ $isMula ? 'text-emerald-700' : ($isMadya ? 'text-blue-700' : 'text-amber-800') }}">
                        <span>Input Nilai & Foto</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
            </a>
        @empty
            <div class="text-center py-12 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-clipboard-question"></i>
                </div>
                <h4 class="font-black text-sm text-slate-900">Belum Ada Penugasan Lomba</h4>
                <p class="text-xs text-slate-500 max-w-xs mx-auto">
                    Akun Anda belum dipetakan ke cabang lomba manapun. Silakan konfirmasi ke panitia bagian penilaian.
                </p>
            </div>
        @endforelse

        <!-- Widget Mini: Dokumentasi Foto Juri (Matching Mockup) -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 text-white rounded-3xl p-4 border border-slate-800 shadow-md space-y-2 mt-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <span class="text-xs font-black">Dokumentasi Foto Regu</span>
                </div>
                <span class="text-[10px] font-mono text-emerald-400 font-bold bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-500/30">
                    {{ $totalPhotosCount }} Foto Tersimpan
                </span>
            </div>
            <p class="text-[11px] text-slate-300 leading-relaxed">
                Foto hasil karya & regu terunggah otomatis sebagai bukti otentik penilaian lapangan yang dapat ditinjau ulang oleh koordinator juri.
            </p>
        </div>
    </div>

    <!-- Bottom Navigation Bar Fixed -->
    <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white border-t border-slate-200 p-3 px-6 flex items-center justify-around text-center text-xs shadow-2xl z-40">
        <a href="{{ route('jury.dashboard') }}" class="text-red-600 font-black flex flex-col items-center">
            <i class="fa-solid fa-list-check text-base"></i>
            <span class="text-[10px] mt-0.5">Tugas Juri</span>
        </a>
        <a href="{{ route('admin.competition-leaderboard.index') }}" target="_blank" class="text-slate-400 hover:text-slate-700 flex flex-col items-center transition">
            <i class="fa-solid fa-ranking-star text-base"></i>
            <span class="text-[10px] mt-0.5">Klasemen Skor</span>
        </a>
        <form action="{{ route('jury.logout') }}" method="POST" onsubmit="return confirm('Keluar dari portal juri?')">
            @csrf
            <button type="submit" class="text-slate-400 hover:text-rose-600 flex flex-col items-center transition">
                <i class="fa-solid fa-right-from-bracket text-base"></i>
                <span class="text-[10px] mt-0.5">Keluar</span>
            </button>
        </form>
    </div>

</div>
@endsection
