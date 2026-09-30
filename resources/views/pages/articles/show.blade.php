@extends('layouts.app')

@section('title', $article->title)

@section('content')
<!-- Breadcrumbs -->
<div class="bg-white border-b border-slate-200 py-3">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-xs text-slate-500 flex items-center gap-2 overflow-x-auto">
        <a href="{{ route('home') }}" class="hover:text-pmr-primary">Beranda</a>
        <i class="fa-solid fa-angle-right text-[10px]"></i>
        <a href="{{ route('artikel.index') }}" class="hover:text-pmr-primary">Artikel</a>
        <i class="fa-solid fa-angle-right text-[10px]"></i>
        <span class="text-slate-800 font-semibold truncate">{{ Str::limit($article->title, 40) }}</span>
    </div>
</div>

<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header info -->
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 bg-red-100 text-pmr-primary text-xs font-bold px-3.5 py-1.5 rounded-full uppercase tracking-wider mb-4">
            <i class="fa-solid fa-tag"></i> {{ $article->category->name ?? 'Edukasi Kemanusiaan' }}
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-6">
            {{ $article->title }}
        </h1>
        
        <div class="flex flex-wrap items-center justify-between py-4 border-y border-slate-200 text-xs text-slate-500 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-pmr-primary text-white flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-800 text-sm">{{ $article->author_name }}</div>
                    <div>Relawan & Tim Redaksi PMR SMAN 1 Ciawi</div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span><i class="fa-regular fa-calendar mr-1"></i> {{ $article->published_at ? $article->published_at->format('d F Y') : date('d F Y') }}</span>
                <span>&bull;</span>
                <span><i class="fa-regular fa-clock mr-1"></i> {{ $article->estimated_reading_time }}</span>
                <span>&bull;</span>
                <span><i class="fa-regular fa-eye mr-1"></i> {{ $article->views_count }} pembaca</span>
            </div>
        </div>
    </div>

    <!-- Featured Image -->
    @if ($article->thumbnail)
        <div class="rounded-3xl overflow-hidden shadow-xl mb-10 bg-slate-900">
            <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}" class="w-full h-auto object-cover max-h-[500px]">
        </div>
    @endif

    <!-- Body content with nice typography -->
    <div class="prose max-w-none text-slate-700 leading-relaxed text-base sm:text-lg mb-12 space-y-6">
        {!! nl2br(e($article->body)) !!}
    </div>

    <!-- Share & Socials -->
    <div class="bg-slate-100 p-6 rounded-2xl flex flex-wrap items-center justify-between gap-4 mb-16 border border-slate-200">
        <span class="font-bold text-slate-800 text-xs uppercase tracking-wider">Bagikan Artikel Ini:</span>
        <div class="flex items-center gap-2">
            <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . url()->current()) }}" target="_blank" class="bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-xl flex items-center gap-1.5 hover:bg-emerald-600 transition">
                <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
            </a>
            <button onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan artikel berhasil disalin!');" class="bg-slate-800 text-white text-xs font-bold px-4 py-2 rounded-xl flex items-center gap-1.5 hover:bg-slate-900 transition">
                <i class="fa-solid fa-link text-sm"></i> Salin Link
            </button>
        </div>
    </div>

    <!-- Related Articles -->
    @if ($relatedArticles->count() > 0)
        <div class="border-t border-slate-200 pt-12">
            <h3 class="text-2xl font-extrabold text-slate-900 mb-8">Artikel Terkait Lainnya</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($relatedArticles as $rel)
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-200 flex flex-col">
                        <div class="h-40 bg-slate-900 overflow-hidden">
                            <img src="{{ $rel->thumbnail ?: '/mockups/07_artikel.jpg' }}" alt="{{ $rel->title }}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <h4 class="font-bold text-slate-800 text-sm mb-2 leading-snug line-clamp-2">
                                <a href="{{ route('artikel.show', $rel->slug) }}" class="hover:text-pmr-primary">{{ $rel->title }}</a>
                            </h4>
                            <p class="text-xs text-slate-500 line-clamp-2 mb-4 flex-grow">{{ $rel->excerpt }}</p>
                            <a href="{{ route('artikel.show', $rel->slug) }}" class="text-xs text-pmr-primary font-bold hover:underline">Baca &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</article>
@endsection
