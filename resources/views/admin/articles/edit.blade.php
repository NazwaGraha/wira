@extends('layouts.admin')

@section('title', 'Edit Artikel')
@section('page_title', 'Perbarui Artikel')

@section('top_actions')
    <a href="{{ route('admin.articles.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm">
        <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Judul -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Judul Artikel <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" required value="{{ old('title', $article->title) }}" 
                       class="w-full text-base px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-bold">
            </div>

            <!-- Kategori & Penulis Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kategori Artikel <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $article->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Penulis / Tim
                    </label>
                    <input type="text" name="author_name" value="{{ old('author_name', $article->author_name) }}" 
                           class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                </div>
            </div>

            <!-- Ringkasan Excerpt -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Ringkasan Singkat (Excerpt)
                </label>
                <textarea name="excerpt" rows="2" 
                          class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">{{ old('excerpt', $article->excerpt) }}</textarea>
            </div>

            <!-- Isi Konten Artikel -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Isi Lengkap Artikel <span class="text-red-500">*</span>
                </label>
                <textarea name="body" required rows="10" 
                          class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-sans leading-relaxed">{{ old('body', $article->body) }}</textarea>
            </div>

            <!-- Thumbnail URL -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    URL Gambar Thumbnail
                </label>
                <input type="text" name="thumbnail" value="{{ old('thumbnail', $article->thumbnail) }}" 
                       class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
            </div>

            <!-- Status Publikasi & Featured Checkbox -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Publikasi
                    </label>
                    <select name="status" class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                        <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>Published (Langsung Terbit)</option>
                        <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>Draft (Simpan Sementara)</option>
                    </select>
                </div>

                <div class="sm:pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }} class="w-5 h-5 accent-pmr-primary rounded">
                        <span class="text-xs font-bold text-slate-800">Jadikan Artikel Utama (Headline)</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.articles.index') }}" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-8 py-3.5 rounded-xl shadow-lg transition active:scale-98 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
