@extends('layouts.app')

@section('title', $event->title ?? 'Lomba PMR - Sua Bhakti Berkarya')

@section('content')
<!-- Hero Section -->
<section class="relative bg-slate-900 text-white pt-32 pb-20 overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#ef4444_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase mb-6">
                <i class="fa-solid fa-trophy"></i> {{ $event->theme ?: 'AJANG PRESTASI RELAWAN MUDA PMR WIRA CIAWI' }}
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                {{ $event->title ?? 'SUA BHAKTI BERKARYA III TAHUN 2025' }}
            </h1>
            <p class="text-slate-300 text-base md:text-lg mb-8 leading-relaxed">
                {{ $event->description ?? 'Ajang kompetisi kepalangmerahan bergengsi tingkat Mula (SD), Madya (SMP), dan Wira (SMA/SMK/MA) se-Jabodetabek dan sekitarnya.' }}
            </p>

            <div class="flex flex-wrap justify-center gap-4">
                @if($event && $event->is_registration_open)
                    <a href="{{ route('lomba.register') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3.5 rounded-xl font-bold shadow-lg shadow-red-900/40 transition flex items-center gap-2">
                        <i class="fa-solid fa-file-pen"></i> Daftar Sekarang
                    </a>
                @endif
                <a href="{{ route('lomba.status') }}" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white px-7 py-3.5 rounded-xl font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Cek Status Pendaftaran
                </a>
                <a href="{{ route('lomba.scoreboard') }}" class="bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 px-7 py-3.5 rounded-xl font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-square-poll-vertical"></i> Live Klasemen Juara
                </a>
                @if($event && $event->handbook_file)
                    <a href="{{ asset('storage/' . $event->handbook_file) }}" target="_blank" class="bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/40 text-indigo-300 px-7 py-3.5 rounded-xl font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf"></i> Unduh Juklak Juknis
                    </a>
                @endif
            </div>
        </div>

        <!-- Quick Info Banner -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-slate-800/80 border border-slate-700/80 backdrop-blur p-5 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-semibold">Pelaksanaan</div>
                    <div class="text-sm font-bold text-white">
                        @if($event && $event->start_date)
                            @if($event->end_date && $event->end_date->format('Y-m-d') !== $event->start_date->format('Y-m-d'))
                                {{ $event->start_date->translatedFormat('d') }} - {{ $event->end_date->translatedFormat('d F Y') }}
                            @else
                                {{ $event->start_date->translatedFormat('d F Y') }}
                            @endif
                        @else
                            15 - 16 Oktober 2026
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-slate-800/80 border border-slate-700/80 backdrop-blur p-5 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-semibold">Lokasi</div>
                    <div class="text-sm font-bold text-white">{{ $event->location ?? 'Kampus SMAN 1 Ciawi Bogor' }}</div>
                </div>
            </div>

            <div class="bg-slate-800/80 border border-slate-700/80 backdrop-blur p-5 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-semibold">Biaya Registrasi</div>
                    <div class="text-sm font-bold text-white">Rp {{ number_format($event->registration_fee ?? 150000, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">/ Tim</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mata Lomba Section -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl font-extrabold text-slate-900">Mata Lomba yang Dipertandingkan</h2>
            <p class="text-slate-600 text-sm mt-2">Pilih tingkatan sekolah Anda untuk melihat cabang lomba yang tersedia.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- MULA (SD) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-extrabold px-3 py-1 rounded-full uppercase">Tingkat SD</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-2">PMR Mula</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-child-reaching"></i>
                    </div>
                </div>
                <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                    @foreach($categoriesByLevel->get('Mula', []) as $cat)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i>
                            <span><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') ({{ $cat->gender_category }}) @endif</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('lomba.register') }}?level=Mula" class="w-full text-center py-2.5 rounded-xl font-bold bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 transition text-sm">
                    Daftar Tingkat Mula &rarr;
                </a>
            </div>

            <!-- MADYA (SMP) -->
            <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-red-500/30 relative flex flex-col">
                <div class="absolute -top-3 right-6 bg-red-600 text-white text-[10px] font-extrabold px-3 py-0.5 rounded-full uppercase tracking-wider">
                    Paling Favorit
                </div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <span class="bg-red-100 text-red-800 text-xs font-extrabold px-3 py-1 rounded-full uppercase">Tingkat SMP</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-2">PMR Madya</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                </div>
                <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                    @foreach($categoriesByLevel->get('Madya', []) as $cat)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-red-500 mt-1"></i>
                            <span><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') ({{ $cat->gender_category }}) @endif</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('lomba.register') }}?level=Madya" class="w-full text-center py-2.5 rounded-xl font-bold bg-red-600 hover:bg-red-700 text-white shadow-md shadow-red-900/20 transition text-sm">
                    Daftar Tingkat Madya &rarr;
                </a>
            </div>

            <!-- WIRA (SMA) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <span class="bg-amber-100 text-amber-800 text-xs font-extrabold px-3 py-1 rounded-full uppercase">Tingkat SMA / SMK / MA</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-2">PMR Wira</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                </div>
                <ul class="space-y-3 flex-grow text-sm text-slate-600 mb-6">
                    @foreach($categoriesByLevel->get('Wira', []) as $cat)
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-amber-500 mt-1"></i>
                            <span><strong>{{ $cat->name }}</strong> @if($cat->gender_category !== 'Umum') ({{ $cat->gender_category }}) @endif</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('lomba.register') }}?level=Wira" class="w-full text-center py-2.5 rounded-xl font-bold bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-700 transition text-sm">
                    Daftar Tingkat Wira &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
