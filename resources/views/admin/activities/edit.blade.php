@extends('layouts.admin')

@section('title', 'Edit Kegiatan')
@section('page_title', 'Edit Kegiatan: ' . $activity->title)

@push('styles')
<!-- Quill 2.0 Rich Text Editor Styles -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    /* Custom Styling untuk Toolbar Quill agar serasi dengan UI PMR Backoffice */
    .ql-toolbar.ql-snow {
        border: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        background-color: #f8fafc;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        padding: 12px 14px !important;
    }
    .ql-container.ql-snow {
        border: none !important;
        font-family: inherit !important;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
        background-color: #ffffff;
    }
    .ql-editor {
        min-height: 320px;
        font-size: 0.95rem;
        line-height: 1.75;
        color: #1e293b;
        padding: 16px 20px !important;
    }
    .ql-editor.ql-blank::before {
        color: #94a3b8;
        font-style: normal;
        left: 20px;
    }
    /* Toolbar button hover & active styles */
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
    .ql-snow .ql-toolbar button:focus, 
    .ql-snow.ql-toolbar button.ql-active, 
    .ql-snow .ql-toolbar button.ql-active, 
    .ql-snow.ql-toolbar .ql-picker-label:hover, 
    .ql-snow .ql-toolbar .ql-picker-label:hover, 
    .ql-snow.ql-toolbar .ql-picker-label.ql-active, 
    .ql-snow .ql-toolbar .ql-picker-label.ql-active, 
    .ql-snow.ql-toolbar .ql-picker-item:hover, 
    .ql-snow .ql-toolbar .ql-picker-item:hover, 
    .ql-snow.ql-toolbar .ql-picker-item.ql-selected, 
    .ql-snow .ql-toolbar .ql-picker-item.ql-selected {
        color: #980000 !important;
    }
    .ql-snow.ql-toolbar button:hover .ql-stroke, 
    .ql-snow .ql-toolbar button:hover .ql-stroke, 
    .ql-snow.ql-toolbar button:focus .ql-stroke, 
    .ql-snow .ql-toolbar button:focus .ql-stroke, 
    .ql-snow.ql-toolbar button.ql-active .ql-stroke, 
    .ql-snow .ql-toolbar button.ql-active .ql-stroke {
        stroke: #980000 !important;
    }
    .ql-snow.ql-toolbar button:hover .ql-fill, 
    .ql-snow .ql-toolbar button:hover .ql-fill, 
    .ql-snow.ql-toolbar button:focus .ql-fill, 
    .ql-snow .ql-toolbar button:focus .ql-fill, 
    .ql-snow.ql-toolbar button.ql-active .ql-fill, 
    .ql-snow .ql-toolbar button.ql-active .ql-fill {
        fill: #980000 !important;
    }
</style>
@endpush

@section('top_actions')
<a href="{{ route('admin.activities.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Kembali ke Daftar Kegiatan</span>
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-extrabold text-slate-800 text-sm">Edit Data Kegiatan (ID: #{{ $activity->id }})</h2>
            <span class="text-xs text-slate-500">* Wajib diisi</span>
        </div>

        <form id="activity-form" action="{{ route('admin.activities.update', $activity) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Judul Kegiatan -->
            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Judul Kegiatan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $activity->title) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                    placeholder="Contoh: Pelatihan Pertolongan Pertama (PP) Tingkat Wira 2026">
                <span class="text-[11px] text-slate-400 mt-1 block">Slug saat ini: <code>{{ $activity->slug }}</code></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kategori -->
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kategori Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition bg-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $activity->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Pelaksanaan -->
                <div>
                    <label for="event_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Pelaksanaan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="event_date" id="event_date" value="{{ old('event_date', $activity->event_date ? $activity->event_date->format('Y-m-d') : date('Y-m-d')) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition bg-white">
                </div>
            </div>

            <!-- Lokasi Kegiatan -->
            <div>
                <label for="location" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Lokasi Kegiatan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="location" id="location" value="{{ old('location', $activity->location) }}" required
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        placeholder="Contoh: Aula Serbaguna SMAN 1 Ciawi / Lapangan Utama">
                </div>
            </div>

            <!-- Deskripsi & Rincian Kegiatan (Rich Text Editor Lengkap) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Deskripsi & Rincian Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-pmr-primary bg-red-50 px-2.5 py-0.5 rounded-full border border-red-100">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Rich Text WYSIWYG Editor
                    </span>
                </div>

                <!-- Hidden Input untuk menampung isi HTML editor ke database -->
                <textarea name="description" id="description" class="hidden" required>{{ old('description', $activity->description) }}</textarea>

                <!-- Container Editor Quill -->
                <div class="bg-white rounded-2xl border border-slate-300 shadow-sm focus-within:ring-2 focus-within:ring-pmr-primary focus-within:border-pmr-primary transition overflow-hidden">
                    <div id="quill-editor">
                        {!! old('description', $activity->description) !!}
                    </div>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1.5 px-1">
                    <span>Mendukung: Heading, Font & Ukuran, Bold/Italic/Underline, Warna Teks & Highlight, List Angka/Titik/Checkbox, Alignment, Link, Foto, Video, serta Undo & Redo.</span>
                    <span id="editor-counter" class="font-semibold text-slate-500">0 karakter</span>
                </div>
            </div>

            <!-- Upload / Ganti Gambar -->
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Foto / Banner Kegiatan Saat Ini
                </div>

                @if($activity->image)
                    <div class="flex items-center gap-4 p-3 bg-white rounded-xl border border-slate-200">
                        <div class="w-24 h-16 rounded-lg overflow-hidden bg-slate-900 flex-shrink-0">
                            <img src="{{ $activity->image }}" alt="{{ $activity->title }}" class="w-full h-full object-cover">
                        </div>
                        <div class="text-xs text-slate-600 truncate">
                            <span class="font-bold text-slate-800 block">Path file:</span>
                            <span class="text-slate-500 break-all">{{ $activity->image }}</span>
                        </div>
                    </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- File Upload Baru -->
                    <div>
                        <label class="block text-xs text-slate-600 font-semibold mb-1.5">
                            Ganti dengan Upload File Baru (Opsional)
                        </label>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-pmr-primary file:text-white hover:file:bg-pmr-dark file:cursor-pointer transition">
                        <span class="text-[11px] text-slate-400 mt-1 block">Biarkan kosong jika tidak ingin mengganti gambar.</span>
                    </div>

                    <!-- URL Gambar -->
                    <div>
                        <label for="image_url" class="block text-xs text-slate-600 font-semibold mb-1.5">
                            Atau Ganti dengan URL / Path Gambar
                        </label>
                        <input type="text" name="image_url" id="image_url" value="{{ old('image_url') }}"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                            placeholder="Contoh: /mockups/03_kegiatan.jpg atau https://...">
                    </div>
                </div>
            </div>

            <!-- Checkbox Program Prioritas (Featured) -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer p-4 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $activity->is_featured) ? 'checked' : '' }}
                        class="w-5 h-5 rounded text-pmr-primary focus:ring-pmr-primary border-slate-300">
                    <div>
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-star text-amber-500 text-xs"></i> Jadikan Sebagai Program Prioritas
                        </span>
                        <span class="block text-xs text-slate-500">Akan ditampilkan sebagai kartu besar utama di bagian atas halaman Agenda & Kegiatan.</span>
                    </div>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.activities.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
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
        // Daftarkan Custom Icons untuk Undo & Redo pada Quill
        const icons = Quill.import('ui/icons');
        icons['undo'] = '<svg viewBox="0 0 18 18"><polygon class="ql-fill ql-stroke" points="6 10 4 12 2 10 6 10"></polygon><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
        icons['redo'] = '<svg viewBox="0 0 18 18"><polygon class="ql-fill ql-stroke" points="12 10 14 12 16 10 12 10"></polygon><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

        // Konfigurasi Toolbar Sangat Lengkap
        const toolbarOptions = [
            // 1. History (Undo & Redo)
            ['undo', 'redo'],

            // 2. Font Family & Font Size
            [{ 'font': [] }, { 'size': ['small', false, 'large', 'huge'] }],

            // 3. Headings (H1, H2, H3, H4, H5, H6)
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

            // 4. Basic Text Styling
            ['bold', 'italic', 'underline', 'strike'],

            // 5. Text Color & Highlight Background
            [{ 'color': [] }, { 'background': [] }],

            // 6. Subscript / Superscript
            [{ 'script': 'sub'}, { 'script': 'super' }],

            // 7. Quotes & Code Blocks
            ['blockquote', 'code-block'],

            // 8. Lists (Numbered, Bulleted, Checkboxes)
            [{ 'list': 'ordered'}, { 'list': 'bullet'}, { 'list': 'check' }],

            // 9. Indent & Outdent, Text Direction
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],

            // 10. Alignments (Left, Center, Right, Justify)
            [{ 'align': [] }],

            // 11. Media Insertion (Links, Images, Embedded Videos)
            ['link', 'image', 'video'],

            // 12. Remove Formatting Clean Button
            ['clean']
        ];

        // Inisialisasi Editor Quill
        const quill = new Quill('#quill-editor', {
            modules: {
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        undo: function() {
                            this.quill.history.undo();
                        },
                        redo: function() {
                            this.quill.history.redo();
                        }
                    }
                },
                history: {
                    delay: 500,
                    maxStack: 200,
                    userOnly: true
                }
            },
            theme: 'snow',
            placeholder: 'Tuliskan deskripsi lengkap, rincian susunan acara, pemateri, tujuan kegiatan, dan informasi penting lainnya...'
        });

        const form = document.getElementById('activity-form');
        const descriptionInput = document.getElementById('description');
        const counterEl = document.getElementById('editor-counter');

        // Fungsi Sinkronisasi Data Editor ke Input Form
        function syncQuillToInput() {
            const html = quill.root.innerHTML;
            const text = quill.getText().trim();
            const hasMedia = html.includes('<img') || html.includes('<iframe') || html.includes('<video');
            
            // Jika kosong dan tidak ada gambar, set string kosong agar validasi required berjalan
            const isBlank = text.length === 0 && !hasMedia;
            descriptionInput.value = isBlank ? '' : html;

            // Update Counter
            if (counterEl) {
                const charCount = quill.getLength() - 1;
                counterEl.textContent = charCount + ' karakter';
            }
        }

        // Sinkronisasi otomatis saat ada pengetikan di editor
        quill.on('text-change', function () {
            syncQuillToInput();
        });

        // Hitung karakter awal
        syncQuillToInput();

        // Pastikan input form terupdate sebelum submit form
        form.addEventListener('submit', function (e) {
            syncQuillToInput();
            if (!descriptionInput.value.trim()) {
                e.preventDefault();
                alert('Silakan isi Deskripsi & Rincian Kegiatan terlebih dahulu.');
                quill.focus();
            }
        });
    });
</script>
@endpush
