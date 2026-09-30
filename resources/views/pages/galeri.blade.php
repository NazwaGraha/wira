@extends('layouts.app')

@section('title', 'Galeri Foto & Video')

@section('content')
<!-- Page Header -->
<section class="gradient-pmr text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-3">Galeri Foto & Video</h1>
        <p class="text-red-100 text-sm sm:text-base max-w-xl mx-auto">
            Dokumentasi visual momen pengabdian, latihan pertolongan pertama, upacara, dan persaudaraan relawan PMR Wira SMAN 1 Ciawi.
        </p>
    </div>
</section>

<!-- Filter Tabs -->
<div class="bg-white border-b border-slate-200 py-4 sticky top-20 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-center">
        <div class="flex items-center gap-2 overflow-x-auto">
            <button onclick="filterGallery('all')" class="gallery-filter-btn active px-5 py-2 rounded-full text-xs font-bold bg-pmr-primary text-white shadow">Semua Foto</button>
            <button onclick="filterGallery('latihan')" class="gallery-filter-btn px-5 py-2 rounded-full text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200">Latihan & PP</button>
            <button onclick="filterGallery('donor')" class="gallery-filter-btn px-5 py-2 rounded-full text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200">Donor Darah</button>
            <button onclick="filterGallery('sosial')" class="gallery-filter-btn px-5 py-2 rounded-full text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200">Bakti Sosial</button>
            <button onclick="filterGallery('video')" class="gallery-filter-btn px-5 py-2 rounded-full text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200">Video Dokumentasi</button>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Gallery Grid (Masonry Style matching Mockup 04) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5" id="gallery-container">
        @forelse($galleries->where('type', 'photo') as $item)
            @php
                $filterClass = strtolower(str_replace([' ', '&'], ['-', 'and'], $item->category));
                // simplified filter mapping for the mockup buttons
                if(str_contains(strtolower($item->category), 'latihan') || str_contains(strtolower($item->category), 'pertolongan')) $filterClass = 'latihan';
                elseif(str_contains(strtolower($item->category), 'donor')) $filterClass = 'donor';
                elseif(str_contains(strtolower($item->category), 'sosial')) $filterClass = 'sosial';
            @endphp
            <div class="gallery-item filter-{{ $filterClass }} filter-photo group relative rounded-2xl overflow-hidden shadow-md bg-slate-900 aspect-video sm:aspect-square cursor-pointer" onclick="openLightbox('{{ Storage::url($item->image_path) }}', '{{ $item->title }}')">
                <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4 text-white">
                    <span class="text-[10px] uppercase font-bold text-red-400">{{ $item->category }}</span>
                    <h4 class="text-xs font-bold leading-tight">{{ $item->title }}</h4>
                </div>
                <div class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/60 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-slate-500">
                Belum ada foto galeri.
            </div>
        @endforelse
    </div>

    <!-- Video Dokumentasi Section -->
    <div class="mt-20 gallery-item filter-video">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-xl">
                <i class="fa-brands fa-youtube"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Video Dokumentasi Kegiatan</h2>
                <p class="text-xs text-slate-500">Tayangan dokumenter aktivitas dan aksi relawan di lapangan.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($galleries->where('type', 'video') as $video)
                <a href="{{ $video->video_url ?? '#' }}" target="_blank" class="block bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-200 group">
                    <div class="relative h-44 bg-slate-900">
                        <img src="{{ Storage::url($video->image_path) }}" alt="{{ $video->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300 opacity-90">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-red-600 text-white flex items-center justify-center text-lg shadow-lg group-hover:scale-110 transition">
                                <i class="fa-solid fa-play ml-1"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-slate-800 text-sm mb-1 group-hover:text-pmr-primary transition">{{ $video->title }}</h4>
                        <span class="text-[11px] text-slate-400">Durasi: {{ $video->duration ?? '-' }}</span>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12 text-slate-500">
                    Belum ada video dokumentasi.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white text-3xl hover:text-red-400">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="max-w-4xl w-full text-center">
        <img id="lightbox-img" src="" alt="Enlarged" class="max-h-[80vh] mx-auto rounded-2xl shadow-2xl object-contain mb-4">
        <div id="lightbox-caption" class="text-white text-sm font-semibold tracking-wide"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openLightbox(src, caption) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox-caption').innerText = caption;
        document.getElementById('lightbox').classList.remove('hidden');
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
    }

    function filterGallery(type) {
        document.querySelectorAll('.gallery-filter-btn').forEach(btn => {
            btn.classList.remove('bg-pmr-primary', 'text-white', 'shadow');
            btn.classList.add('bg-slate-100', 'text-slate-600');
        });
        event.target.classList.remove('bg-slate-100', 'text-slate-600');
        event.target.classList.add('bg-pmr-primary', 'text-white', 'shadow');

        const items = document.querySelectorAll('.gallery-item');
        items.forEach(item => {
            if (type === 'all') {
                if (item.classList.contains('filter-photo')) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'block'; // Or handle video section separately
                }
            } else if (type === 'video') {
                if (item.classList.contains('filter-video')) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            } else {
                if (item.classList.contains('filter-' + type)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            }
        });
    }
</script>
@endpush
