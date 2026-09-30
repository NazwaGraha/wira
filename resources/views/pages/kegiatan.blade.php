@extends('layouts.app')

@section('title', 'Agenda & Kegiatan')

@section('content')
<!-- Page Header -->
<section class="gradient-pmr text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-3">Agenda & Kegiatan Kami</h1>
        <p class="text-red-100 text-sm sm:text-base max-w-xl mx-auto">
            Dokumentasi dan jadwal kegiatan kepalangmerahan, pelatihan pertolongan pertama, serta bakti sosial PMR Wira SMAN 1 Ciawi.
        </p>
    </div>
</section>

<!-- Filter Tabs Bar (Matching Mockup 03) -->
<div class="bg-white border-b border-slate-200 sticky top-20 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            @php
                $currentCat = request('kategori', 'Semua');
                $categories = ['Semua', 'Pertolongan Pertama', 'Donor Darah', 'Kesiapsiagaan Bencana', 'Pendidikan', 'Bakti Sosial'];
            @endphp

            @foreach ($categories as $cat)
                <a href="{{ route('kegiatan', ['kategori' => $cat === 'Semua' ? null : $cat]) }}" 
                   class="px-5 py-2 rounded-full text-xs font-bold transition whitespace-nowrap {{ ($currentCat === $cat || ($cat === 'Semua' && !$category)) ? 'bg-pmr-primary text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Featured Large Event Card -->
    @if ($featuredActivity && !$category)
        <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-200/80 mb-16 grid grid-cols-1 lg:grid-cols-12 gap-0">
            <a href="{{ route('kegiatan.show', $featuredActivity->slug) }}" class="lg:col-span-7 relative h-72 lg:h-auto min-h-[320px] bg-slate-900 block group">
                <img src="{{ $featuredActivity->image ?: '/mockups/03_kegiatan.jpg' }}" alt="{{ $featuredActivity->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute top-4 left-4 bg-pmr-primary text-white font-extrabold text-xs px-4 py-1.5 rounded-full uppercase tracking-wider shadow">
                    Program Prioritas 2026
                </div>
            </a>
            <div class="lg:col-span-5 p-8 sm:p-10 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-red-600 text-xs font-bold mb-3 uppercase tracking-wider">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span>{{ $featuredActivity->event_date ? $featuredActivity->event_date->format('d F Y') : 'Terjadwal' }}</span>
                    <span>&bull;</span>
                    <span><i class="fa-solid fa-location-dot"></i> {{ $featuredActivity->location ?? 'SMAN 1 Ciawi' }}</span>
                </div>
                <a href="{{ route('kegiatan.show', $featuredActivity->slug) }}">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight mb-4 hover:text-pmr-primary transition">
                        {{ $featuredActivity->title }}
                    </h2>
                </a>
                <div class="prose prose-sm text-slate-600 leading-relaxed mb-6 max-w-none line-clamp-4">
                    {{ strip_tags($featuredActivity->description) }}
                </div>
                <div class="pt-2">
                    <a href="{{ route('kegiatan.show', $featuredActivity->slug) }}" class="inline-flex items-center gap-2 bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-6 py-3 rounded-xl shadow transition">
                        Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Activities Grid (3 Columns) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($activities as $act)
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-200/80 transition duration-300 flex flex-col">
                <a href="{{ route('kegiatan.show', $act->slug) }}" class="relative h-52 overflow-hidden bg-slate-900 block">
                    <img src="{{ $act->image ?: '/mockups/03_kegiatan.jpg' }}" alt="{{ $act->title }}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                    <div class="absolute top-4 left-4 bg-pmr-primary text-white text-[11px] font-bold px-3 py-1 rounded-full shadow">
                        {{ $act->category }}
                    </div>
                    @if ($act->event_date)
                        <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-[11px] font-bold px-3 py-1 rounded-lg">
                            <i class="fa-regular fa-calendar mr-1"></i> {{ $act->event_date->format('d.m.Y') }}
                        </div>
                    @endif
                </a>
                <div class="p-6 flex flex-col flex-grow">
                    <a href="{{ route('kegiatan.show', $act->slug) }}">
                        <h3 class="font-bold text-slate-900 text-lg mb-2 leading-snug hover:text-pmr-primary transition">
                            {{ $act->title }}
                        </h3>
                    </a>
                    <div class="text-xs text-slate-400 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-red-500"></i> {{ $act->location ?: 'Kampus SMAN 1 Ciawi' }}
                    </div>
                    <p class="text-slate-600 text-xs leading-relaxed mb-6 flex-grow line-clamp-3">
                        {{ strip_tags($act->description) }}
                    </p>
                    <div class="border-t border-slate-100 pt-4 flex items-center justify-between text-xs">
                        <span class="text-pmr-primary font-bold uppercase tracking-wider">Unit PMR Wira</span>
                        <a href="{{ route('kegiatan.show', $act->slug) }}" class="text-red-700 hover:text-pmr-primary font-bold flex items-center gap-1">
                            Baca <i class="fa-solid fa-angle-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-16 text-slate-400">
                <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
                <p>Belum ada kegiatan yang ditemukan untuk kategori ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12 flex justify-center">
        {{ $activities->links() }}
    </div>
</div>
@endsection
