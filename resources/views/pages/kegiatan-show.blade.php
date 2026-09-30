@extends('layouts.app')

@section('title', $activity->title)

@section('content')
<!-- Hero Cover -->
<section class="relative bg-slate-900 pt-24 pb-12 lg:pt-32 lg:pb-24 overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="{{ $activity->image ?: '/mockups/03_kegiatan.jpg' }}" alt="{{ $activity->title }}" class="w-full h-full object-cover opacity-40">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
    </div>
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 bg-pmr-primary/90 backdrop-blur-sm px-4 py-1.5 rounded-full text-xs font-bold text-white uppercase tracking-widest mb-6 shadow-lg">
            {{ $activity->category }}
        </div>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-6">
            {{ $activity->title }}
        </h1>
        
        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-sm text-slate-300 font-medium">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-red-400">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <span>{{ $activity->event_date ? $activity->event_date->translatedFormat('d F Y') : 'Terjadwal' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-red-400">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <span>{{ $activity->location ?? 'SMAN 1 Ciawi' }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Content Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Main Content -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
                <div class="prose prose-slate max-w-none prose-img:rounded-2xl prose-img:shadow-md prose-headings:font-extrabold prose-a:text-pmr-primary hover:prose-a:text-pmr-dark">
                    {!! $activity->description !!}
                </div>
                
                <div class="mt-12 pt-8 border-t border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Bagikan Kegiatan Ini:</h3>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition shadow-sm">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($activity->title) }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 hover:bg-sky-500 hover:text-white flex items-center justify-center transition shadow-sm">
                            <i class="fa-brands fa-twitter"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($activity->title . ' - ' . request()->fullUrl()) }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 hover:bg-green-500 hover:text-white flex items-center justify-center transition shadow-sm">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Back Button -->
            <a href="{{ route('kegiatan') }}" class="flex items-center justify-center gap-2 w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Indeks Kegiatan
            </a>

            <!-- Related Activities Widget -->
            @if($relatedActivities->count() > 0)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-1 h-6 bg-pmr-primary rounded-full"></div>
                    <h3 class="font-extrabold text-slate-900 text-lg">Kegiatan Lainnya</h3>
                </div>
                
                <div class="space-y-6">
                    @foreach($relatedActivities as $related)
                    <a href="{{ route('kegiatan.show', $related->slug) }}" class="group flex gap-4 items-start">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                            <img src="{{ $related->image ?: '/mockups/03_kegiatan.jpg' }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm leading-snug group-hover:text-pmr-primary transition line-clamp-2 mb-1">
                                {{ $related->title }}
                            </h4>
                            <div class="text-[11px] text-slate-500 font-medium">
                                {{ $related->event_date ? $related->event_date->translatedFormat('d M Y') : 'Terjadwal' }}
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
