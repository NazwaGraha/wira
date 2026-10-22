@extends('layouts.admin')

@section('title', 'Tambah Slide Hero Baru')
@section('page_title', 'Tambah Slide Gambar Hero Baru')

@section('top_actions')
    <a href="{{ route('admin.hero-slides.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Petunjuk Rekomendasi Format & Ukuran Banner -->
    <div class="bg-gradient-to-r from-red-50 via-rose-50 to-amber-50 border border-red-200/80 rounded-3xl p-6 sm:p-7 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-pmr-primary text-white flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <div class="space-y-3 text-xs flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">
                        Panduan Format & Ukuran Gambar Banner Hero
                    </h3>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-white text-pmr-primary border border-red-200 shadow-xs">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Rekomendasi Resmi
                    </span>
                </div>
                <p class="text-slate-600 leading-relaxed">
                    Banner hero ditampilkan pada bagian paling atas halaman Beranda dengan efek slider transisi dinamis dan gradien merah khas PMR. Agar gambar tampil tajam dan proporsional di seluruh perangkat (smartphone, laptop, PC layar lebar), ikuti panduan berikut:
                </p>

                <!-- Grid Spesifikasi Singkat -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                    <div class="bg-white/90 backdrop-blur-sm p-3.5 rounded-2xl border border-red-100 shadow-2xs">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-up-right-and-down-left-from-center text-pmr-primary"></i> Resolusi Ideal
                        </div>
                        <div class="text-sm font-extrabold text-slate-900">1920 × 800 px</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">atau 1920 × 1080 px (16:9 Lanskap)</div>
                    </div>

                    <div class="bg-white/90 backdrop-blur-sm p-3.5 rounded-2xl border border-red-100 shadow-2xs">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-file-image text-emerald-600"></i> Format File
                        </div>
                        <div class="text-sm font-extrabold text-slate-900">JPG, JPEG, PNG, WEBP</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Format web umum & tajam</div>
                    </div>

                    <div class="bg-white/90 backdrop-blur-sm p-3.5 rounded-2xl border border-red-100 shadow-2xs">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-hard-drive text-amber-600"></i> Kapasitas Maksimal
                        </div>
                        <div class="text-sm font-extrabold text-slate-900">Maks. 5 MB</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Disarankan &lt; 2 MB agar website cepat</div>
                    </div>
                </div>

                <div class="bg-white/70 p-3 rounded-xl border border-red-100/60 text-[11px] text-slate-600 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-500 flex-shrink-0"></i>
                    <span><strong>Tips Desain:</strong> Letakkan objek foto utama (siswa/kegiatan) di bagian tengah agak ke kanan, karena sisi kiri akan dilapisi teks judul sambutan dan tombol aksi beranda.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Tambah Slide Hero -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm">
        <form action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data" class="space-y-7">
            @csrf

            <!-- Judul Slide -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Judul / Label Slide <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" required value="{{ old('title') }}" 
                       placeholder="Contoh: Apel Siaga Kemanusiaan 2026" 
                       class="w-full text-base px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-bold">
                @error('title')
                    <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bagian Upload Gambar Banner (Utama & Mudah Dipahami) -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Upload Gambar Banner Slide <span class="text-red-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-500">Pilih berkas dari laptop / smartphone</span>
                </div>

                <!-- Dropzone Box Upload Gambar -->
                <div id="dropzone_container" 
                     onclick="document.getElementById('banner_image').click()" 
                     class="cursor-pointer border-2 border-dashed border-red-300 hover:border-pmr-primary bg-red-50/30 hover:bg-red-50/60 transition-all duration-200 rounded-2xl p-7 sm:p-9 text-center flex flex-col items-center justify-center gap-3 group">
                    
                    <div class="w-16 h-16 rounded-2xl bg-white text-pmr-primary group-hover:bg-pmr-primary group-hover:text-white transition shadow-sm border border-red-100 flex items-center justify-center text-3xl">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>

                    <div>
                        <p class="text-sm sm:text-base font-extrabold text-slate-800">
                            <span class="text-pmr-primary underline underline-offset-2">Klik untuk Memilih Gambar Banner</span> atau seret file ke sini
                        </p>
                        <p class="text-xs text-slate-500 mt-1">
                            Format: <strong>JPG, JPEG, PNG, WEBP</strong> (Maksimal 5 MB &bull; Disarankan 1920 × 800 px)
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs group-hover:border-pmr-primary">
                        <i class="fa-solid fa-folder-open text-amber-500"></i> Telusuri Berkas Gambar
                    </span>
                </div>

                <!-- Input File Tersembunyi -->
                <input type="file" 
                       name="image" 
                       id="banner_image" 
                       accept="image/jpeg,image/png,image/jpg,image/webp" 
                       class="hidden" 
                       onchange="previewUploadedImage(event)">

                @error('image')
                    <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                @enderror

                <!-- Box Live Preview File Terpilih -->
                <div id="new_preview_box" class="hidden bg-emerald-50 border border-emerald-200 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-bold text-emerald-800">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>Gambar Banner Terpilih: <strong id="new_file_name">-</strong> (<span id="new_file_size">-</span>)</span>
                        </div>
                        <button type="button" onclick="cancelImageSelection()" class="text-xs font-bold text-red-600 hover:text-red-800 underline flex items-center gap-1">
                            <i class="fa-solid fa-xmark"></i> Hapus / Ganti Gambar
                        </button>
                    </div>
                    <div class="relative h-44 sm:h-56 rounded-xl overflow-hidden bg-slate-900 border border-emerald-300 shadow-inner">
                        <img id="new_image_preview" src="" alt="Preview Banner" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-950/80 via-pmr-primary/70 to-stone-950/70 flex items-end p-4">
                            <span class="text-white text-xs font-bold bg-emerald-700/80 backdrop-blur-xs px-2.5 py-1 rounded-md">
                                Pratinjau Tampilan dengan Gradien Khas PMR
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Opsi Lanjutan: URL / Preset Gambar (Collapsible / Default Tertutup) -->
                <details class="text-xs text-slate-500 pt-2 group">
                    <summary class="cursor-pointer font-semibold hover:text-slate-700 list-none flex items-center gap-1.5 select-none text-[11px]">
                        <i class="fa-solid fa-gear text-slate-400 group-open:rotate-90 transition-transform"></i>
                        <span>Opsi Lanjutan: Gunakan URL atau Gambar Bawaan Sistem (Khusus Pengembang / Lanjutan)</span>
                    </summary>
                    <div class="mt-3 p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <p class="text-[11px] text-slate-600">Anda dapat memasukkan path gambar manual atau memilih gambar bawaan sistem di bawah ini jika tidak ingin mengunggah berkas baru:</p>
                        <input type="text" name="image_path" id="image_path" value="{{ old('image_path') }}" 
                               placeholder="Contoh: /mockups/01_home.jpg atau https://..." 
                               class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-mono bg-white">
                        
                        <div class="flex items-center gap-2 text-[11px] text-slate-600 flex-wrap pt-1">
                            <span class="font-bold text-slate-700">Pilihan Cepat Gambar Bawaan:</span>
                            <button type="button" onclick="choosePresetImage('/mockups/01_home.jpg')" class="text-pmr-primary hover:underline font-bold">Beranda 01</button>
                            &bull;
                            <button type="button" onclick="choosePresetImage('/mockups/02_tentang_kami.jpg')" class="text-pmr-primary hover:underline font-bold">Tentang Kami 02</button>
                            &bull;
                            <button type="button" onclick="choosePresetImage('/mockups/03_kegiatan.jpg')" class="text-pmr-primary hover:underline font-bold">Pelatihan PP 03</button>
                            &bull;
                            <button type="button" onclick="choosePresetImage('/mockups/04_galeri.jpg')" class="text-pmr-primary hover:underline font-bold">Dokumentasi 04</button>
                            &bull;
                            <button type="button" onclick="choosePresetImage('/mockups/05_donor_darah.jpg')" class="text-pmr-primary hover:underline font-bold">Donor Darah 05</button>
                        </div>
                    </div>
                </details>
            </div>

            <!-- Keterangan / Caption -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Keterangan Singkat / Subjudul Slide (Opsional)
                </label>
                <textarea name="caption" rows="2" placeholder="Catatan atau deskripsi foto..." 
                          class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">{{ old('caption') }}</textarea>
                @error('caption')
                    <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Urutan & Status Aktif -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Urutan Tampil
                    </label>
                    <input type="number" name="order_position" value="{{ old('order_position', 1) }}" min="1" 
                           class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                    <p class="text-[11px] text-slate-500 mt-1">Slide dengan nomor urut lebih kecil akan tampil lebih dulu.</p>
                </div>

                <div class="sm:pt-2">
                    <label class="flex items-center gap-3 cursor-pointer bg-white p-3.5 rounded-xl border border-slate-200 hover:border-pmr-primary transition">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 accent-pmr-primary rounded">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Aktifkan Slide di Beranda</span>
                            <span class="text-[11px] text-slate-500 block">Tampilkan gambar ini pada putaran slider banner</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.hero-slides.index') }}" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-8 py-3.5 rounded-xl shadow-lg transition active:scale-98 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Slide Gambar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewUploadedImage(event) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran berkas melebihi batas 5 MB! Silakan pilih foto dengan ukuran lebih kecil.');
            cancelImageSelection();
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('new_image_preview').src = e.target.result;
            document.getElementById('new_file_name').textContent = file.name;
            
            const sizeInKb = (file.size / 1024).toFixed(1);
            const sizeText = sizeInKb > 1000 ? (sizeInKb / 1024).toFixed(2) + ' MB' : sizeInKb + ' KB';
            document.getElementById('new_file_size').textContent = sizeText;

            document.getElementById('new_preview_box').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function cancelImageSelection() {
    const input = document.getElementById('banner_image');
    input.value = '';
    document.getElementById('new_preview_box').classList.add('hidden');
    document.getElementById('new_image_preview').src = '';
}

function choosePresetImage(path) {
    document.getElementById('image_path').value = path;
    document.getElementById('new_image_preview').src = path;
    document.getElementById('new_file_name').textContent = path;
    document.getElementById('new_file_size').textContent = 'Preset Sistem';
    document.getElementById('new_preview_box').classList.remove('hidden');
}
</script>
@endsection
