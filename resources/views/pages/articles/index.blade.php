@extends('layouts.app')

@section('title', 'Artikel & Edukasi Kemanusiaan')

@section('content')
<!-- Hero Search Banner (Matching Mockup 07) -->
<section class="gradient-pmr text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-4">
            Artikel & Edukasi Kemanusiaan
        </h1>
        <p class="text-red-100 text-sm mb-8">
            Pusat literasi pertolongan pertama, kesiapsiagaan bencana, dan kabar inspiratif keluarga besar PMR Wira SMAN 1 Ciawi.
        </p>

        <!-- Search Bar -->
        <form action="{{ route('artikel.index') }}" method="GET" class="relative max-w-2xl mx-auto">
            <div class="relative flex items-center">
                <i class="fa-solid fa-magnifying-glass absolute left-5 text-slate-400 text-base"></i>
                <input type="text" name="q" value="{{ request('q') }}" 
                       placeholder="Cari artikel kepalangmerahan, tips P3K, info relawan..." 
                       class="w-full pl-12 pr-28 py-4 rounded-full text-slate-900 text-sm bg-white shadow-xl focus:outline-none focus:ring-4 focus:ring-red-300">
                <button type="submit" class="absolute right-2 bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-6 py-2.5 rounded-full transition">
                    Cari
                </button>
            </div>
        </form>

        <!-- Popular Tag Pills -->
        <div class="flex items-center justify-center gap-2 flex-wrap mt-6 text-xs">
            <span class="text-red-200 font-semibold">Tag Populer:</span>
            <a href="{{ route('artikel.index') }}?q=P3K" class="bg-white/15 hover:bg-white/25 px-3 py-1 rounded-full text-white transition">tips P3K</a>
            <a href="{{ route('artikel.index') }}?kategori=edukasi-kesehatan" class="bg-white/15 hover:bg-white/25 px-3 py-1 rounded-full text-white transition">edukasi kesehatan</a>
            <a href="{{ route('artikel.index') }}?kategori=kesiapsiagaan-bencana" class="bg-white/15 hover:bg-white/25 px-3 py-1 rounded-full text-white transition">mitigasi bencana</a>
            <a href="{{ route('artikel.index') }}?kategori=donor-darah" class="bg-white/15 hover:bg-white/25 px-3 py-1 rounded-full text-white transition">donor darah</a>
            <a href="{{ route('artikel.index') }}?kategori=kisah-relawan" class="bg-white/15 hover:bg-white/25 px-3 py-1 rounded-full text-white transition">kisah relawan</a>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Left Column: Articles Main Content (8 Cols - Matching Mockup 07) -->
        <div class="lg:col-span-8 space-y-10">
            
            <!-- Featured Hero Article Card -->
            @if ($featuredArticle && !request('q') && !request('kategori'))
                <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-slate-200/80 group">
                    <div class="relative h-72 sm:h-96 bg-slate-900 overflow-hidden">
                        <img src="{{ $featuredArticle->thumbnail ?: '/mockups/07_artikel.jpg' }}" alt="{{ $featuredArticle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
                        
                        <div class="absolute top-6 left-6">
                            <span class="bg-pmr-primary text-white text-xs font-extrabold px-4 py-1.5 rounded-full uppercase tracking-wider shadow">
                                {{ $featuredArticle->category->name ?? 'Tips Utama' }}
                            </span>
                        </div>

                        <div class="absolute bottom-6 left-6 right-6 text-white">
                            <div class="flex items-center gap-3 text-xs text-slate-300 mb-2">
                                <span><i class="fa-regular fa-user mr-1"></i> {{ $featuredArticle->author_name }}</span>
                                <span>&bull;</span>
                                <span><i class="fa-regular fa-calendar mr-1"></i> {{ $featuredArticle->published_at ? $featuredArticle->published_at->format('d M Y') : date('d M Y') }}</span>
                                <span>&bull;</span>
                                <span><i class="fa-regular fa-clock mr-1"></i> {{ $featuredArticle->estimated_reading_time }}</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold leading-tight mb-3">
                                <a href="{{ route('artikel.show', $featuredArticle->slug) }}" class="hover:text-red-300 transition">
                                    {{ $featuredArticle->title }}
                                </a>
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-200 line-clamp-2 leading-relaxed">
                                {{ $featuredArticle->excerpt }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Regular Articles Grid (2 Cols) -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-extrabold text-slate-900">
                        @if (request('q'))
                            Hasil Pencarian: "{{ request('q') }}"
                        @elseif (request('kategori'))
                            Kategori: {{ ucfirst(str_replace('-', ' ', request('kategori'))) }}
                        @else
                            Artikel Terbaru
                        @endif
                    </h3>
                    <span class="text-xs text-slate-500">{{ $articles->total() }} Artikel Ditemukan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @forelse ($articles as $art)
                        <article class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-200/80 transition duration-300 flex flex-col">
                            <div class="relative h-48 bg-slate-900 overflow-hidden">
                                <img src="{{ $art->thumbnail ?: '/mockups/07_artikel.jpg' }}" alt="{{ $art->title }}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                <div class="absolute top-3 left-3 bg-pmr-primary text-white text-[10px] font-bold px-3 py-1 rounded-full shadow">
                                    {{ $art->category->name ?? 'Edukasi' }}
                                </div>
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 mb-2">
                                    <span><i class="fa-regular fa-calendar"></i> {{ $art->published_at ? $art->published_at->format('d M Y') : date('d M Y') }}</span>
                                    <span>&bull;</span>
                                    <span><i class="fa-regular fa-eye"></i> {{ $art->views_count }} views</span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-base mb-2 leading-snug hover:text-pmr-primary transition">
                                    <a href="{{ route('artikel.show', $art->slug) }}">{{ $art->title }}</a>
                                </h4>
                                <p class="text-slate-600 text-xs leading-relaxed mb-6 flex-grow line-clamp-3">
                                    {{ $art->excerpt ?: Str::limit(strip_tags($art->body), 110) }}
                                </p>
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <span class="text-[11px] font-semibold text-slate-500">{{ $art->author_name }}</span>
                                    <a href="{{ route('artikel.show', $art->slug) }}" class="text-pmr-primary font-bold hover:underline">
                                        Baca <i class="fa-solid fa-angle-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-2 text-center py-16 bg-white rounded-3xl border border-slate-200 text-slate-400">
                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-300"></i>
                            <p class="text-sm font-semibold">Tidak ada artikel yang cocok dengan pencarian Anda.</p>
                            <a href="{{ route('artikel.index') }}" class="text-xs text-pmr-primary font-bold underline mt-2 inline-block">Reset Filter Pencarian</a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-10 flex justify-center">
                    {{ $articles->links() }}
                </div>
            </div>

        </div>

        <!-- Right Column: Sidebar (4 Cols - Matching Mockup 07) -->
        <div class="lg:col-span-4 space-y-8">
            
            <!-- Category List Widget -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200">
                <h4 class="text-base font-extrabold text-slate-900 mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-tags text-pmr-primary"></i> Kategori Artikel
                </h4>
                <ul class="space-y-2 text-xs">
                    <li>
                        <a href="{{ route('artikel.index') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 transition {{ !request('kategori') ? 'text-pmr-primary font-bold bg-red-50/60' : 'text-slate-600' }}">
                            <span>Semua Kategori</span>
                            <span class="bg-slate-100 px-2 py-0.5 rounded-full font-bold text-[10px]">{{ \App\Models\Article::where('status', 'published')->count() }}</span>
                        </a>
                    </li>
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('artikel.index', ['kategori' => $cat->slug]) }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 transition {{ request('kategori') === $cat->slug ? 'text-pmr-primary font-bold bg-red-50/60' : 'text-slate-600' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="bg-slate-100 px-2 py-0.5 rounded-full font-bold text-[10px]">{{ $cat->articles_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Popular & Trending Articles -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200">
                <h4 class="text-base font-extrabold text-slate-900 mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-fire text-amber-500"></i> Artikel Populer & Trending
                </h4>
                <div class="space-y-4">
                    @foreach ($popularArticles as $pop)
                        <div class="flex items-start gap-3 group">
                            <div class="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                                <img src="{{ $pop->thumbnail ?: '/mockups/07_artikel.jpg' }}" alt="{{ $pop->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                            </div>
                            <div class="overflow-hidden">
                                <h5 class="text-xs font-bold text-slate-800 leading-snug group-hover:text-pmr-primary transition line-clamp-2">
                                    <a href="{{ route('artikel.show', $pop->slug) }}">{{ $pop->title }}</a>
                                </h5>
                                <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-2">
                                    <span><i class="fa-regular fa-eye"></i> {{ $pop->views_count }} views</span>
                                    <span>&bull;</span>
                                    <span>{{ $pop->published_at ? $pop->published_at->format('d/m/Y') : '' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Call to Action Card: Kirim Tulisan -->
            <div class="rounded-3xl p-7 text-white gradient-pmr shadow-lg text-center relative overflow-hidden">
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-full bg-white/20 text-white flex items-center justify-center text-xl mx-auto mb-3 shadow-inner">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <h4 class="text-lg font-extrabold mb-2">Punya Kisah Relawan?</h4>
                    <p class="text-xs text-red-100 leading-relaxed mb-5">
                        Kirimkan tulisan, opini kesehatan, atau pengalaman kemanusiaan Anda untuk diterbitkan di website resmi PMR Wira SMAN 1 Ciawi.
                    </p>
                    <a href="{{ route('kontak') }}" class="inline-block w-full bg-white text-pmr-primary hover:bg-red-50 font-bold text-xs uppercase tracking-wider py-3 rounded-xl shadow transition">
                        Hubungi Redaksi PMR
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
