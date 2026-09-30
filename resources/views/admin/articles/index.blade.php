@extends('layouts.admin')

@section('title', 'Kelola Artikel')
@section('page_title', 'Manajemen Artikel & Konten Edukasi')

@section('top_actions')
    <a href="{{ route('admin.articles.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tulis Artikel Baru
    </a>
@endsection

@section('content')
<!-- Filter & Stats Bar (Matching Mockup 08) -->
<div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm mb-8">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 w-full sm:w-auto overflow-x-auto pb-1">
            <a href="{{ route('admin.articles.index') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua ({{ $totalCount }})
            </a>
            <a href="{{ route('admin.articles.index', ['status' => 'published']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'published' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Published ({{ $publishedCount }})
            </a>
            <a href="{{ route('admin.articles.index', ['status' => 'draft']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'draft' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Draft ({{ $draftCount }})
            </a>
        </div>

        <!-- Search Input -->
        <form action="{{ route('admin.articles.index') }}" method="GET" class="w-full sm:w-72">
            <div class="relative flex items-center">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul artikel..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-pmr-primary">
            </div>
        </form>

    </div>
</div>

<!-- Main Table Card (Matching Mockup 08) -->
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider border-b border-slate-200 font-bold">
                <tr>
                    <th class="py-4 px-6">Thumbnail</th>
                    <th class="py-4 px-6">Judul Artikel</th>
                    <th class="py-4 px-6">Kategori</th>
                    <th class="py-4 px-6">Penulis</th>
                    <th class="py-4 px-6">Status</th>
                    <th class="py-4 px-6">Tanggal</th>
                    <th class="py-4 px-6">Views</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($articles as $art)
                    <tr class="hover:bg-slate-50/70 transition">
                        <!-- Thumbnail -->
                        <td class="py-4 px-6">
                            <div class="w-14 h-10 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-200">
                                <img src="{{ $art->thumbnail ?: '/mockups/07_artikel.jpg' }}" alt="" class="w-full h-full object-cover">
                            </div>
                        </td>

                        <!-- Title -->
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-800 text-sm hover:text-pmr-primary transition">
                                <a href="{{ route('admin.articles.edit', $art->id) }}">
                                    {{ $art->title }}
                                </a>
                            </div>
                            @if ($art->is_featured)
                                <span class="inline-block bg-red-100 text-pmr-primary text-[10px] font-bold px-2 py-0.5 rounded-full mt-1">
                                    ★ Featured
                                </span>
                            @endif
                        </td>

                        <!-- Category -->
                        <td class="py-4 px-6">
                            <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg text-xs font-semibold">
                                {{ $art->category->name ?? 'Umum' }}
                            </span>
                        </td>

                        <!-- Author -->
                        <td class="py-4 px-6 text-slate-600 font-medium">
                            {{ $art->author_name }}
                        </td>

                        <!-- Status Badge (Matching Mockup 08) -->
                        <td class="py-4 px-6">
                            @if ($art->status === 'published')
                                <span class="bg-emerald-100 text-emerald-800 font-bold px-3 py-1 rounded-full text-[11px] inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                                </span>
                            @else
                                <span class="bg-amber-100 text-amber-800 font-bold px-3 py-1 rounded-full text-[11px] inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                                </span>
                            @endif
                        </td>

                        <!-- Date -->
                        <td class="py-4 px-6 text-slate-500">
                            {{ $art->created_at ? $art->created_at->format('d/m/Y') : '-' }}
                        </td>

                        <!-- Views -->
                        <td class="py-4 px-6 font-bold text-slate-700">
                            {{ number_format($art->views_count) }}
                        </td>

                        <!-- Actions (Edit, Delete, Preview) -->
                        <td class="py-4 px-6 text-right space-x-2">
                            <a href="{{ route('artikel.show', $art->slug) }}" target="_blank" 
                               class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition" title="Preview">
                                Preview
                            </a>
                            <a href="{{ route('admin.articles.edit', $art->id) }}" 
                               class="inline-block px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.articles.destroy', $art->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-400">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300"></i>
                            <p>Tidak ada artikel yang sesuai kriteria.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-6 border-t border-slate-100">
        {{ $articles->links() }}
    </div>
</div>
@endsection
