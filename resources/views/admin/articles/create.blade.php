@extends('layouts.admin')

@section('title', 'Tulis Artikel & Tips P3K Baru')
@section('page_title', 'Tulis Artikel & Tips P3K Baru')

@push('styles')
<!-- Quill 2.0 Rich Text Editor Styles -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    /* Custom Styling Toolbar Quill agar serasi dengan UI PMR Wira Backoffice */
    .ql-toolbar.ql-snow {
        border: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        background-color: #f8fafc;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        padding: 12px 14px !important;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .ql-container.ql-snow {
        border: none !important;
        font-family: inherit !important;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
        background-color: #ffffff;
    }
    .ql-editor {
        min-height: 380px;
        font-size: 1rem;
        line-height: 1.8;
        color: #1e293b;
        padding: 20px 24px !important;
    }
    .ql-editor.ql-blank::before {
        color: #94a3b8;
        font-style: normal;
        left: 24px;
    }
    .ql-snow .ql-stroke {
        stroke: #475569;
    }
    .ql-snow .ql-fill {
        fill: #475569;
    }
    .ql-snow .ql-picker {
        color: #475569;
        font-weight: 600;
    }
    .ql-snow.ql-toolbar button:hover, 
    .ql-snow .ql-toolbar button:hover, 
    .ql-snow.ql-toolbar button:focus, 
    .ql-snow.ql-toolbar button.ql-active, 
    .ql-snow .ql-toolbar button.ql-active, 
    .ql-snow.ql-toolbar .ql-picker-label:hover, 
    .ql-snow.ql-toolbar .ql-picker-label.ql-active, 
    .ql-snow.ql-toolbar .ql-picker-item:hover, 
    .ql-snow.ql-toolbar .ql-picker-item.ql-selected {
        color: #980000 !important;
    }
    .ql-snow.ql-toolbar button:hover .ql-stroke, 
    .ql-snow .ql-toolbar button:focus .ql-stroke, 
    .ql-snow.ql-toolbar button.ql-active .ql-stroke {
        stroke: #980000 !important;
    }
    .ql-snow.ql-toolbar button:hover .ql-fill, 
    .ql-snow.ql-toolbar button:focus .ql-fill, 
    .ql-snow.ql-toolbar button.ql-active .ql-fill {
        fill: #980000 !important;
    }
</style>
@endpush

@section('top_actions')
    <a href="{{ route('admin.articles.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-start gap-3 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg mt-0.5 shrink-0"></i>
            <div class="text-sm">
                <span class="font-bold">Mohon periksa kembali formulir:</span>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form id="article-form" action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- KOLOM KIRI (KONTEN UTAMA & EDITOR & SEO) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- KARTU 1: KONTEN ARTIKEL -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-red-50 text-pmr-primary flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-pen-nib"></i>
                            </div>
                            <h2 class="font-extrabold text-slate-800 text-base">Informasi & Konten Artikel</h2>
                        </div>
                        <span class="text-xs text-slate-400"><span class="text-red-500">*</span> Wajib diisi</span>
                    </div>

                    <!-- Judul Artikel -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Judul Artikel / Tips P3K <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}" 
                               placeholder="Contoh: Protokol Pembalutan Fraktur Tulang Tertutup di Lapangan" 
                               class="w-full text-lg px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-extrabold text-slate-900 placeholder:text-slate-400 placeholder:font-normal transition">
                    </div>

                    <!-- Kategori & Penulis Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <button type="button" onclick="openQuickCategoryModal()" class="text-xs font-bold text-pmr-primary hover:text-red-700 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-plus-circle"></i> Tambah Kategori
                                </button>
                            </div>
                            <select name="category_id" id="category_id" required class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white font-semibold">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="author_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Nama Penulis / Tim
                            </label>
                            <input type="text" name="author_name" id="author_name" value="{{ old('author_name', 'Tim Medis PMR SMAN 1 Ciawi') }}" 
                                   class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-medium">
                        </div>
                    </div>

                    <!-- Ringkasan Excerpt -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="excerpt" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Ringkasan Singkat (Excerpt)
                            </label>
                            <span class="text-[11px] text-slate-400" id="excerpt-counter">0/500 karakter</span>
                        </div>
                        <textarea name="excerpt" id="excerpt" rows="2" maxlength="500" placeholder="Tuliskan 1-2 kalimat ringkasan pengantar yang memikat untuk cuplikan kartu artikel di beranda..." 
                                  class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary transition leading-relaxed">{{ old('excerpt') }}</textarea>
                    </div>

                    <!-- Rich Text Editor Lengkap (Quill 2.0) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Isi Lengkap Artikel <span class="text-red-500">*</span>
                            </label>
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-pmr-primary bg-red-50 px-2.5 py-0.5 rounded-full border border-red-100">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Full WYSIWYG Editor
                            </span>
                        </div>

                        <!-- Hidden Textarea untuk menampung HTML data ke server -->
                        <textarea name="body" id="body" class="hidden" required>{{ old('body') }}</textarea>

                        <!-- Container Editor Quill -->
                        <div class="bg-white rounded-2xl border border-slate-300 shadow-sm focus-within:ring-2 focus-within:ring-pmr-primary focus-within:border-pmr-primary transition overflow-hidden">
                            <div id="quill-editor">
                                {!! old('body') !!}
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between text-[11px] text-slate-400 mt-2 px-1 gap-2">
                            <span>Mendukung: Heading, Font Size, Bold/Italic/Underline, Warna & Highlight, List Angka/Titik/Checklist, Alignment, Link, Foto, Video, serta Undo/Redo.</span>
                            <span id="editor-counter" class="font-bold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-md">0 kata | 0 karakter</span>
                        </div>
                    </div>
                </div>

                <!-- KARTU 2: PENGATURAN SEO LENGKAP (COMPLETE SEO SUITE) -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-magnifying-glass-chart"></i>
                            </div>
                            <div>
                                <h2 class="font-extrabold text-slate-800 text-base">Optimasi SEO Lengkap (Search Engine Optimization)</h2>
                                <p class="text-[11px] text-slate-400">Tingkatkan peringkat artikel di Google Search dan optimalkan tampilan share media sosial.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                            SEO Friendly
                        </span>
                    </div>

                    <!-- Focus Keyword & SEO Score Indicator -->
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-4">
                        <div>
                            <label for="focus_keyword" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-key text-amber-500"></i> Kata Kunci Utama (Focus Keyword)
                            </label>
                            <input type="text" name="focus_keyword" id="focus_keyword" value="{{ old('focus_keyword') }}"
                                   placeholder="Contoh: pembalutan fraktur, pertolongan pertama, donor darah"
                                   class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
                            <span class="text-[11px] text-slate-400 mt-1 block">Kata kunci yang ingin Anda targetkan agar muncul di halaman pertama pencarian Google.</span>
                        </div>

                        <!-- Live SEO Checklist -->
                        <div class="pt-2 border-t border-slate-200/60">
                            <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block mb-2">Checklist Analisis SEO Otomatis:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs" id="seo-checklist">
                                <div class="flex items-center gap-2 text-slate-400" id="check-title">
                                    <i class="fa-solid fa-circle-xmark text-slate-300"></i>
                                    <span>Kata kunci ada di Judul Artikel</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-400" id="check-desc">
                                    <i class="fa-solid fa-circle-xmark text-slate-300"></i>
                                    <span>Kata kunci ada di Meta Description</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-400" id="check-slug">
                                    <i class="fa-solid fa-circle-xmark text-slate-300"></i>
                                    <span>Kata kunci ada di Slug URL</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-400" id="check-body">
                                    <i class="fa-solid fa-circle-xmark text-slate-300"></i>
                                    <span>Kata kunci ada di Konten Artikel</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Title (SEO Title) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="meta_title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Meta Title (Judul di Google)
                            </label>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="copyTitleToMeta()" class="text-[11px] text-emerald-600 hover:underline font-semibold">
                                    Gunakan Judul Artikel
                                </button>
                                <span class="text-[11px] text-slate-400" id="meta-title-counter">0/60 karakter</span>
                            </div>
                        </div>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" maxlength="70"
                               placeholder="Judul yang menarik dan ramah mesin pencari (optimal 50-60 karakter)..."
                               class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                    </div>

                    <!-- Custom Slug (Permalink) -->
                    <div>
                        <label for="slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kustomisasi URL Slug (Permalink)
                        </label>
                        <div class="flex items-center rounded-xl border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-emerald-500">
                            <span class="bg-slate-100 text-slate-500 text-xs px-3 py-2.5 border-r border-slate-300 select-none">
                                https://pmrwirasmanic.or.id/artikel/
                            </span>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                   placeholder="protokol-pembalutan-fraktur"
                                   class="w-full text-xs px-3 py-2.5 focus:outline-none font-mono text-slate-800">
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Biarkan kosong jika ingin slug dibuat otomatis dari judul artikel.</span>
                    </div>

                    <!-- Meta Description -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="meta_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Meta Description (Deskripsi Cuplikan Google)
                            </label>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="copyExcerptToMeta()" class="text-[11px] text-emerald-600 hover:underline font-semibold">
                                    Ambil dari Excerpt
                                </button>
                                <span class="text-[11px] text-slate-400" id="meta-desc-counter">0/160 karakter</span>
                            </div>
                        </div>
                        <textarea name="meta_description" id="meta_description" rows="3" maxlength="200"
                                  placeholder="Deskripsi ringkas yang muncul tepat di bawah judul pada hasil pencarian Google (optimal 120-160 karakter)..."
                                  class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 leading-relaxed">{{ old('meta_description') }}</textarea>
                    </div>

                    <!-- Meta Keywords & Canonical URL Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="meta_keywords" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Meta Keywords (Kata Kunci Tag)
                            </label>
                            <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}"
                                   placeholder="p3k, patah tulang, pmr wira, sman 1 ciawi"
                                   class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-[10px] text-slate-400 mt-1 block">Pisahkan setiap kata kunci dengan tanda koma (,).</span>
                        </div>

                        <div>
                            <label for="canonical_url" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Canonical URL (Opsional)
                            </label>
                            <input type="url" name="canonical_url" id="canonical_url" value="{{ old('canonical_url') }}"
                                   placeholder="https://pmrwirasmanic.or.id/artikel/..."
                                   class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-[10px] text-slate-400 mt-1 block">Gunakan jika artikel ini pernah dimuat di sumber lain.</span>
                        </div>
                    </div>

                    <!-- LIVE GOOGLE SEARCH SNIPPET PREVIEW -->
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-brands fa-google text-blue-500"></i> Live Preview Hasil Pencarian Google
                            </span>
                            <span class="text-[10px] text-slate-400">Desktop / Mobile View</span>
                        </div>

                        <!-- Card Snippet Google -->
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-1">
                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                <span class="w-4 h-4 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-[10px] font-bold">P</span>
                                <span class="font-medium text-slate-700">pmrwirasmanic.or.id</span>
                                <span class="text-slate-400">&rsaquo;</span>
                                <span class="text-slate-500 font-mono text-[11px]" id="google-preview-slug">artikel &rsaquo; protokol-pembalutan</span>
                            </div>
                            <h3 class="text-blue-700 hover:underline font-semibold text-base leading-snug cursor-pointer line-clamp-1" id="google-preview-title">
                                Judul Artikel Anda Akan Tampil di Sini | PMR Wira SMAN 1 Ciawi
                            </h3>
                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-2" id="google-preview-desc">
                                Deskripsi artikel yang Anda masukkan pada Meta Description akan tampil rapi di sini untuk memikat pembaca di mesin pencari Google...
                            </p>
                        </div>
                    </div>

                    <!-- OPEN GRAPH / SOCIAL MEDIA SETTINGS -->
                    <div class="pt-2 border-t border-slate-200 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-share-nodes text-indigo-500"></i> Social Media & Open Graph (WhatsApp, FB, X)
                            </span>
                            <span class="text-[11px] text-slate-400">Pratinjau saat dibagikan ke medsos</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="og_title" class="block text-xs font-bold text-slate-600 mb-1">
                                    Judul Share Medsos (og:title)
                                </label>
                                <input type="text" name="og_title" id="og_title" value="{{ old('og_title') }}"
                                       placeholder="Default: Mengikuti Meta Title"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="og_description" class="block text-xs font-bold text-slate-600 mb-1">
                                    Deskripsi Share Medsos (og:description)
                                </label>
                                <input type="text" name="og_description" id="og_description" value="{{ old('og_description') }}"
                                       placeholder="Default: Mengikuti Meta Description"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Upload Khusus OG Image jika ingin gambar share berbeda dari gambar utama -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">
                                Gambar Khusus Share Medsos (Opsional)
                            </label>
                            <input type="file" name="og_image_file" id="og_image_file" accept="image/*"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer transition">
                            <span class="text-[10px] text-slate-400 mt-1 block">Rekomendasi rasio 1200x630 px. Jika kosong, sistem otomatis memakai gambar utama artikel.</span>
                        </div>
                    </div>

                </div>

            </div>

            <!-- KOLOM KANAN (UPLOAD GAMBAR UTAMA, STATUS & AKSI) -->
            <div class="space-y-6">

                <!-- KARTU 3: GAMBAR UTAMA (THUMBNAIL / HERO IMAGE) -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-800 text-sm">Gambar Utama Artikel</h2>
                            <p class="text-[10px] text-slate-400">Bisa upload dari mana saja & semua format gambar</p>
                        </div>
                    </div>

                    <!-- Area Dropzone & File Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Upload File Gambar Utama
                        </label>
                        
                        <div id="dropzone-area" class="border-2 border-dashed border-slate-300 hover:border-pmr-primary rounded-2xl p-5 text-center transition bg-slate-50 hover:bg-red-50/20 cursor-pointer relative group">
                            <input type="file" name="image" id="image-input" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif,image/svg+xml,image/bmp,image/avif,image/heic,image/heif,image/*" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="space-y-2 pointer-events-none" id="dropzone-placeholder">
                                <div class="w-12 h-12 mx-auto rounded-full bg-red-100/60 text-pmr-primary flex items-center justify-center text-xl group-hover:scale-110 transition">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Klik atau Seret Gambar ke Sini</span>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">Mendukung: JPG, JPEG, PNG, WEBP, GIF, SVG, BMP, AVIF, HEIC</span>
                                </div>
                                <span class="inline-block text-[10px] font-semibold text-slate-400 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                    Ukuran Maksimal: 15 MB
                                </span>
                            </div>

                            <!-- Preview Box -->
                            <div id="image-preview-container" class="hidden relative rounded-xl overflow-hidden shadow-md border border-slate-200 group-hover:shadow-lg transition">
                                <img id="image-preview" src="" alt="Pratinjau Gambar" class="w-full h-44 object-cover">
                                <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                    <span class="bg-white/90 text-slate-800 px-3 py-1 rounded-lg text-xs font-bold">Ganti Gambar</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reset preview button -->
                        <div id="remove-preview-btn-container" class="hidden mt-2 text-right">
                            <button type="button" onclick="resetImagePreview()" class="text-xs text-rose-600 hover:text-rose-800 font-semibold flex items-center gap-1 ml-auto">
                                <i class="fa-solid fa-trash-can"></i> Hapus Gambar Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Opsi URL / Path Gambar Alternatif -->
                    <div class="pt-3 border-t border-slate-100 space-y-2">
                        <label for="thumbnail" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Atau Masukkan URL / Path Gambar
                        </label>
                        <input type="text" name="thumbnail" id="thumbnail" value="{{ old('thumbnail', '/mockups/07_artikel.jpg') }}" 
                               placeholder="/mockups/07_artikel.jpg atau https://..." 
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                        
                        <div class="text-[11px] text-slate-500">
                            <span class="font-semibold block mb-1 text-[10px] uppercase tracking-wider text-slate-400">Pilihan Cepat Gambar Default:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="setPresetThumbnail('/mockups/07_artikel.jpg')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-semibold transition">
                                    Artikel Standar
                                </button>
                                <button type="button" onclick="setPresetThumbnail('/mockups/03_kegiatan.jpg')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-semibold transition">
                                    Latihan P3K
                                </button>
                                <button type="button" onclick="setPresetThumbnail('/mockups/05_donor_darah.jpg')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-semibold transition">
                                    Donor Darah
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KARTU 4: PENGATURAN PUBLIKASI & STATUS -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <h2 class="font-extrabold text-slate-800 text-sm">Status & Visibilitas</h2>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Status Publikasi
                        </label>
                        <select name="status" id="status" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white font-bold">
                            <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>
                                Published (Langsung Terbit)
                            </option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>
                                Draft (Simpan Sementara)
                            </option>
                        </select>
                    </div>

                    <!-- Featured Checkbox -->
                    <div class="p-4 bg-amber-50/60 rounded-2xl border border-amber-200/80">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} 
                                   class="w-4 h-4 mt-0.5 accent-pmr-primary rounded">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block flex items-center gap-1">
                                    <i class="fa-solid fa-star text-amber-500 text-xs"></i> Jadikan Artikel Utama
                                </span>
                                <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">
                                    Artikel akan diprioritaskan di baris paling atas halaman blog.
                                </span>
                            </div>
                        </label>
                    </div>

                    <!-- Tombol Aksi Simpan -->
                    <div class="pt-3 border-t border-slate-100 space-y-2">
                        <button type="submit" class="w-full bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider py-3.5 rounded-xl shadow-lg shadow-red-950/20 transition active:scale-98 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Simpan & Publikasikan</span>
                        </button>
                        
                        <a href="{{ route('admin.articles.index') }}" class="w-full block text-center py-2.5 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 transition">
                            Batal
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </form>
</div>

<!-- Modal Quick Tambah Kategori -->
<div id="quickCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-folder-plus"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Tambah Kategori Baru</h3>
                    <p class="text-[11px] text-slate-500">Kategori akan langsung tersimpan & terpilih di dropdown</p>
                </div>
            </div>
            <button type="button" onclick="closeQuickCategoryModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-200/50 flex items-center justify-center transition">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <form id="quickCategoryForm" onsubmit="submitQuickCategory(event)" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" id="quick_cat_name" required placeholder="Contoh: Tips P3K & Tanggap Darurat" 
                       class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-semibold text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Warna Badge
                    </label>
                    <select id="quick_cat_color" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-semibold bg-white">
                        <option value="red" selected>Merah (PMR)</option>
                        <option value="blue">Biru (Edukasi)</option>
                        <option value="green">Hijau (Kesehatan)</option>
                        <option value="yellow">Kuning (Peringatan)</option>
                        <option value="purple">Ungu (Pelatihan)</option>
                        <option value="slate">Abu-abu (Umum)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi Singkat
                    </label>
                    <input type="text" id="quick_cat_desc" placeholder="Opsional" 
                           class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary text-slate-800">
                </div>
            </div>

            <div id="quick_cat_error" class="text-xs text-red-600 bg-red-50 p-2.5 rounded-lg hidden"></div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeQuickCategoryModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" id="quick_cat_submit_btn" class="px-4 py-2 rounded-xl text-xs font-bold bg-pmr-primary text-white hover:bg-red-700 shadow-md shadow-red-500/20 flex items-center gap-2 transition">
                    <span>Simpan & Pilih</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Quill 2.0 Rich Text Editor Script -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Daftarkan Custom Icons untuk Undo & Redo pada Quill
        const icons = Quill.import('ui/icons');
        icons['undo'] = '<svg viewBox="0 0 18 18"><polygon class="ql-fill ql-stroke" points="6 10 4 12 2 10 6 10"></polygon><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
        icons['redo'] = '<svg viewBox="0 0 18 18"><polygon class="ql-fill ql-stroke" points="12 10 14 12 16 10 12 10"></polygon><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

        // 2. Konfigurasi Toolbar Lengkap
        const toolbarOptions = [
            ['undo', 'redo'],
            [{ 'font': [] }, { 'size': ['small', false, 'large', 'huge'] }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            ['blockquote', 'code-block'],
            [{ 'list': 'ordered'}, { 'list': 'bullet'}, { 'list': 'check' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],
            [{ 'align': [] }],
            ['link', 'image', 'video'],
            ['clean']
        ];

        // 3. Inisialisasi Editor Quill
        const quill = new Quill('#quill-editor', {
            modules: {
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        undo: function() { this.quill.history.undo(); },
                        redo: function() { this.quill.history.redo(); }
                    }
                },
                history: {
                    delay: 500,
                    maxStack: 200,
                    userOnly: true
                }
            },
            theme: 'snow',
            placeholder: 'Tuliskan materi edukasi, protokol pertolongan pertama, panduan kesiapsiagaan bencana, atau kisah relawan di sini secara lengkap...'
        });

        const form = document.getElementById('article-form');
        const bodyInput = document.getElementById('body');
        const editorCounterEl = document.getElementById('editor-counter');
        const titleInput = document.getElementById('title');
        const excerptInput = document.getElementById('excerpt');
        const excerptCounterEl = document.getElementById('excerpt-counter');
        const metaTitleInput = document.getElementById('meta_title');
        const metaTitleCounterEl = document.getElementById('meta-title-counter');
        const metaDescInput = document.getElementById('meta_description');
        const metaDescCounterEl = document.getElementById('meta-desc-counter');
        const slugInput = document.getElementById('slug');
        const focusKeywordInput = document.getElementById('focus_keyword');

        // Fungsi Sinkronisasi Data Editor ke Input Form
        function syncQuillToInput() {
            const html = quill.root.innerHTML;
            const text = quill.getText().trim();
            const hasMedia = html.includes('<img') || html.includes('<iframe') || html.includes('<video');
            
            bodyInput.value = (text.length === 0 && !hasMedia) ? '' : html;

            if (editorCounterEl) {
                const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
                const chars = quill.getLength() - 1;
                editorCounterEl.textContent = words + ' kata | ' + chars + ' karakter';
            }

            updateSeoAnalysis();
        }

        quill.on('text-change', syncQuillToInput);
        syncQuillToInput();

        // Excerpt Counter
        excerptInput.addEventListener('input', function() {
            excerptCounterEl.textContent = this.value.length + '/500 karakter';
            updateGooglePreview();
            updateSeoAnalysis();
        });

        // Meta Title Counter & Live Preview
        metaTitleInput.addEventListener('input', function() {
            metaTitleCounterEl.textContent = this.value.length + '/60 karakter';
            updateGooglePreview();
            updateSeoAnalysis();
        });

        // Meta Description Counter & Live Preview
        metaDescInput.addEventListener('input', function() {
            metaDescCounterEl.textContent = this.value.length + '/160 karakter';
            updateGooglePreview();
            updateSeoAnalysis();
        });

        // Title Input Live Updates
        titleInput.addEventListener('input', function() {
            if (!slugInput.value.trim()) {
                updateGooglePreview();
            }
            updateGooglePreview();
            updateSeoAnalysis();
        });

        // Slug Input Live Updates
        slugInput.addEventListener('input', function() {
            updateGooglePreview();
            updateSeoAnalysis();
        });

        // Focus Keyword Live Updates
        focusKeywordInput.addEventListener('input', function() {
            updateSeoAnalysis();
        });

        // Update Google Snippet Preview
        function updateGooglePreview() {
            const previewTitleEl = document.getElementById('google-preview-title');
            const previewDescEl = document.getElementById('google-preview-desc');
            const previewSlugEl = document.getElementById('google-preview-slug');

            const currentTitle = metaTitleInput.value.trim() || titleInput.value.trim() || 'Judul Artikel Anda Akan Tampil di Sini';
            previewTitleEl.textContent = currentTitle + (currentTitle.includes('PMR Wira') ? '' : ' | PMR Wira SMAN 1 Ciawi');

            const currentDesc = metaDescInput.value.trim() || excerptInput.value.trim() || 'Deskripsi artikel yang Anda masukkan pada Meta Description akan tampil rapi di sini untuk memikat pembaca di mesin pencari Google...';
            previewDescEl.textContent = currentDesc;

            const currentSlug = slugInput.value.trim() || (titleInput.value.trim() ? titleInput.value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '') : 'protokol-pembalutan');
            previewSlugEl.textContent = 'artikel \u203a ' + currentSlug;
        }

        // Live SEO Analysis Checklist
        function updateSeoAnalysis() {
            const kw = focusKeywordInput.value.trim().toLowerCase();
            const title = titleInput.value.trim().toLowerCase();
            const metaDesc = (metaDescInput.value.trim() || excerptInput.value.trim()).toLowerCase();
            const slug = (slugInput.value.trim() || title).toLowerCase();
            const body = quill.getText().toLowerCase();

            function setCheck(id, pass) {
                const el = document.getElementById(id);
                if (!el) return;
                const icon = el.querySelector('i');
                if (pass && kw.length > 0) {
                    el.className = 'flex items-center gap-2 text-emerald-600 font-semibold';
                    icon.className = 'fa-solid fa-circle-check text-emerald-500';
                } else {
                    el.className = 'flex items-center gap-2 text-slate-400';
                    icon.className = 'fa-solid fa-circle-xmark text-slate-300';
                }
            }

            setCheck('check-title', kw.length > 0 && title.includes(kw));
            setCheck('check-desc', kw.length > 0 && metaDesc.includes(kw));
            setCheck('check-slug', kw.length > 0 && slug.includes(kw.replace(/\s+/g, '-')));
            setCheck('check-body', kw.length > 0 && body.includes(kw));
        }

        // Form Submit Validation
        form.addEventListener('submit', function (e) {
            syncQuillToInput();
            if (!bodyInput.value.trim()) {
                e.preventDefault();
                alert('Silakan isi Konten Artikel terlebih dahulu.');
                quill.focus();
            }
        });

        // 4. Image Upload & Preview Logic
        const imageInput = document.getElementById('image-input');
        const previewContainer = document.getElementById('image-preview-container');
        const previewImg = document.getElementById('image-preview');
        const dropzonePlaceholder = document.getElementById('dropzone-placeholder');
        const removePreviewBtn = document.getElementById('remove-preview-btn-container');
        const thumbnailTextInput = document.getElementById('thumbnail');

        imageInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    dropzonePlaceholder.classList.add('hidden');
                    removePreviewBtn.classList.remove('hidden');
                    thumbnailTextInput.value = ''; // Kosongkan path text jika upload file lokal
                };

                reader.readAsDataURL(file);
            }
        });

        window.resetImagePreview = function () {
            imageInput.value = '';
            previewImg.src = '';
            previewContainer.classList.add('hidden');
            dropzonePlaceholder.classList.remove('hidden');
            removePreviewBtn.classList.add('hidden');
            thumbnailTextInput.value = '/mockups/07_artikel.jpg';
        };

        window.setPresetThumbnail = function (url) {
            resetImagePreview();
            thumbnailTextInput.value = url;
            previewImg.src = url;
            previewContainer.classList.remove('hidden');
            dropzonePlaceholder.classList.add('hidden');
            removePreviewBtn.classList.remove('hidden');
        };

        window.copyTitleToMeta = function () {
            if (titleInput.value.trim()) {
                metaTitleInput.value = titleInput.value.trim();
                metaTitleCounterEl.textContent = metaTitleInput.value.length + '/60 karakter';
                updateGooglePreview();
                updateSeoAnalysis();
            }
        };

        window.copyExcerptToMeta = function () {
            if (excerptInput.value.trim()) {
                metaDescInput.value = excerptInput.value.trim();
                metaDescCounterEl.textContent = metaDescInput.value.length + '/160 karakter';
                updateGooglePreview();
                updateSeoAnalysis();
            }
        };

        // Inisialisasi awal Google preview
        updateGooglePreview();

        // Quick Category Modal Functions
        window.openQuickCategoryModal = function () {
            const modal = document.getElementById('quickCategoryModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.getElementById('quick_cat_name').focus();
                const err = document.getElementById('quick_cat_error');
                if (err) {
                    err.classList.add('hidden');
                    err.textContent = '';
                }
            }
        };

        window.closeQuickCategoryModal = function () {
            const modal = document.getElementById('quickCategoryModal');
            if (modal) {
                modal.classList.add('hidden');
                document.getElementById('quickCategoryForm').reset();
            }
        };

        window.submitQuickCategory = async function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('quick_cat_submit_btn');
            const errorBox = document.getElementById('quick_cat_error');
            const name = document.getElementById('quick_cat_name').value.trim();
            const color = document.getElementById('quick_cat_color').value;
            const description = document.getElementById('quick_cat_desc').value.trim();

            if (!name) return;

            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Menyimpan...</span>';
            errorBox.classList.add('hidden');

            try {
                const response = await fetch("{{ route('admin.categories.quick-store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, color, description })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const selectEl = document.getElementById('category_id');
                    const newOption = document.createElement('option');
                    newOption.value = data.category.id;
                    newOption.textContent = data.category.name;
                    newOption.selected = true;
                    selectEl.appendChild(newOption);

                    closeQuickCategoryModal();
                } else {
                    errorBox.textContent = data.message || 'Gagal menyimpan kategori. Silakan periksa kembali.';
                    errorBox.classList.remove('hidden');
                }
            } catch (err) {
                errorBox.textContent = 'Terjadi kesalahan jaringan atau server saat menyimpan kategori.';
                errorBox.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        };
    });
</script>
@endpush
