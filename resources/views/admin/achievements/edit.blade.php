@extends('layouts.admin')

@section('title', 'Edit Prestasi: ' . $achievement->title)
@section('page_title', 'Edit Prestasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
                <span>Edit Data Prestasi & Penghargaan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Perbarui rincian kejuaraan, nama ajang, kontingen peraih, atau tambah dokumentasi foto serah terima piala.
            </p>
        </div>
        <a href="{{ route('admin.prestasi.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Terdapat kendala pengisian:</div>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.prestasi.update', $achievement->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Informasi Utama Kejuaraan -->
        <div class="space-y-4">
            <h2 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2 pb-2 border-b border-slate-100">
                <i class="fa-solid fa-award"></i> 1. Rincian Prestasi & Kejuaraan
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Judul Prestasi / Penghargaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $achievement->title) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
                </div>

                <div>
                    <label for="event_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Ajang / Event Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="event_name" id="event_name" value="{{ old('event_name', $achievement->event_name) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
                </div>

                <div>
                    <label for="organizer" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Instansi / Penyelenggara
                    </label>
                    <input type="text" name="organizer" id="organizer" value="{{ old('organizer', $achievement->organizer) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
                </div>

                <div>
                    <label for="event_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Kegiatan / Penganugerahan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="event_date" id="event_date" value="{{ old('event_date', $achievement->event_date->format('Y-m-d')) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
                </div>

                <div>
                    <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tingkat Kejuaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="level" id="level" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
                        <option value="Nasional" {{ old('level', $achievement->level) === 'Nasional' ? 'selected' : '' }}>Tingkat Nasional</option>
                        <option value="Provinsi" {{ old('level', $achievement->level) === 'Provinsi' ? 'selected' : '' }}>Tingkat Provinsi</option>
                        <option value="Kabupaten/Kota" {{ old('level', $achievement->level) === 'Kabupaten/Kota' ? 'selected' : '' }}>Tingkat Kabupaten/Kota</option>
                        <option value="Kecamatan" {{ old('level', $achievement->level) === 'Kecamatan' ? 'selected' : '' }}>Tingkat Kecamatan</option>
                        <option value="Sekolah" {{ old('level', $achievement->level) === 'Sekolah' ? 'selected' : '' }}>Tingkat Sekolah / Internal</option>
                    </select>
                </div>

                <div>
                    <label for="rank" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Peringkat / Predikat Juara <span class="text-red-500">*</span>
                    </label>
                    <input list="rank-presets" type="text" name="rank" id="rank" value="{{ old('rank', $achievement->rank) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-amber-700 focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
                    <datalist id="rank-presets">
                        <option value="Juara Umum I">
                        <option value="Juara Umum">
                        <option value="Juara 1">
                        <option value="Juara 2">
                        <option value="Juara 3">
                        <option value="Harapan 1">
                        <option value="Best Team">
                        <option value="Juara Favorit">
                    </datalist>
                </div>

                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Cabang / Kategori Lomba
                    </label>
                    <input list="category-presets" type="text" name="category" id="category" value="{{ old('category', $achievement->category) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
                    <datalist id="category-presets">
                        <option value="Umum">
                        <option value="Pertolongan Pertama">
                        <option value="Pasang Tandu Darurat">
                        <option value="Cepat Tepat">
                        <option value="Mading Kreasi">
                        <option value="Kemanusiaan & Bakti Sosial">
                    </datalist>
                </div>
            </div>
        </div>

        <!-- 2. Anggota Kontingen / Tim Peraih -->
        <div class="space-y-4 pt-2">
            <h2 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2 pb-2 border-b border-slate-100">
                <i class="fa-solid fa-users"></i> 2. Nama Anggota Kontingen / Tim Peraih Juara
            </h2>

            @php
                $contingentRaw = '';
                if (is_array($achievement->contingent_members)) {
                    $lines = [];
                    foreach ($achievement->contingent_members as $m) {
                        $line = $m['name'] ?? '';
                        if (!empty($m['class_grade'])) {
                            $line .= ' (' . $m['class_grade'] . ')';
                        }
                        if ($line) $lines[] = $line;
                    }
                    $contingentRaw = implode("\n", $lines);
                }
            @endphp

            <div>
                <label for="contingent_raw" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Daftar Nama Anggota (Tulis 1 nama per baris)
                </label>
                <textarea name="contingent_raw" id="contingent_raw" rows="4"
                    class="w-full p-3.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition font-sans">{{ old('contingent_raw', $contingentRaw) }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Nama-nama ini akan tampil pada daftar kontingen peraih prestasi di kartu piala web publik.</p>
            </div>
        </div>

        <!-- 3. Foto Utama Piala & Galeri Serah Terima -->
        <div class="space-y-6 pt-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 flex-wrap gap-2">
                <h2 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2">
                    <i class="fa-solid fa-images text-sm"></i> 3. Dokumentasi Foto Piala & Galeri Penyerahan
                </h2>
                <span class="text-[11px] text-slate-500 font-medium">Foto kini ditampilkan dalam resolusi besar dengan fasilitas seleksi hapus foto</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- ================= FOTO UTAMA PIALA ================= -->
                <div class="lg:col-span-5 bg-slate-50/80 rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
                                <i class="fa-solid fa-trophy text-amber-500 mr-1"></i> Foto Utama Piala / Trofi
                            </label>
                            @if($achievement->trophy_image)
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black border border-emerald-200">
                                    <i class="fa-solid fa-check-circle mr-0.5"></i> Tersimpan
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold">
                                    Belum Ada
                                </span>
                            @endif
                        </div>

                        <!-- Large Trophy Image Preview Container -->
                        <div id="trophy-preview-wrapper" class="relative group rounded-2xl overflow-hidden border-2 border-slate-200 bg-white shadow-xs aspect-[4/3] flex items-center justify-center transition">
                            <img id="trophy-preview-img" src="{{ $achievement->trophy_image_url }}" alt="Foto Utama Piala" class="w-full h-full object-cover rounded-2xl transition duration-300">
                            
                            <!-- Overlay deletion mark -->
                            <div id="trophy-delete-overlay" class="absolute inset-0 bg-rose-900/85 backdrop-blur-xs hidden flex-col items-center justify-center text-white text-center p-4 transition z-10">
                                <i class="fa-solid fa-trash-can text-3xl mb-2 text-rose-300 animate-bounce"></i>
                                <span class="font-black text-sm uppercase tracking-wider">Foto Ditandai Dihapus</span>
                                <span class="text-[11px] text-rose-200 mt-1 max-w-xs">Foto ini akan dihapus permanen saat tombol "Simpan Perubahan" diklik.</span>
                                <button type="button" onclick="cancelDeleteTrophy()" class="mt-3 px-3.5 py-1.5 rounded-xl bg-white text-rose-800 font-bold text-xs hover:bg-rose-50 transition shadow-sm">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Batalkan Hapus
                                </button>
                            </div>

                            <!-- Floating Action Buttons -->
                            <div class="absolute bottom-3 right-3 flex items-center gap-2 opacity-90 group-hover:opacity-100 transition z-20">
                                <a id="trophy-zoom-link" href="{{ $achievement->trophy_image_url }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-black/70 hover:bg-black text-white text-[11px] font-bold backdrop-blur-xs transition flex items-center gap-1.5 shadow" title="Buka foto ukuran penuh di tab baru">
                                    <i class="fa-solid fa-up-right-from-square text-[10px]"></i> Zoom
                                </a>
                            </div>
                        </div>

                        <!-- Fasilitas Hapus Foto Utama -->
                        @if($achievement->trophy_image)
                            <div id="trophy-actions-box" class="mt-3 pt-3 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-300 px-3 py-2 rounded-xl transition text-xs font-bold text-slate-700 hover:text-rose-700 shadow-2xs select-none">
                                    <input type="checkbox" name="delete_trophy_image" id="delete_trophy_image" value="1" onchange="toggleTrophyDelete(this.checked)" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300 cursor-pointer">
                                    <span>Tandai Hapus Foto Utama</span>
                                </label>

                                <button type="button" onclick="instantDeleteTrophy({{ $achievement->id }})" class="text-xs font-bold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 px-3 py-2 rounded-xl transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-xs"></i> Hapus Sekarang
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- File input to upload/replace trophy image -->
                    <div class="pt-3 border-t border-slate-200 space-y-1.5">
                        <label for="trophy_image" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                            {{ $achievement->trophy_image ? 'Ganti Foto Utama Piala:' : 'Unggah Foto Utama Piala:' }}
                        </label>
                        <input type="file" name="trophy_image" id="trophy_image" accept="image/*" onchange="previewNewTrophyFile(this)"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-pmr-primary hover:file:bg-red-100 transition cursor-pointer border border-slate-200 rounded-xl bg-white p-1">
                        <p class="text-[11px] text-slate-400">Format JPG, PNG, WEBP (Maksimal 5MB). Pilih foto untuk melihat pratinjau langsung.</p>
                    </div>
                </div>

                <!-- ================= GALERI FOTO PENYERAHAN PIALA ================= -->
                <div class="lg:col-span-7 bg-slate-50/80 rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200">
                        <div>
                            <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
                                <i class="fa-solid fa-camera-retro text-pmr-primary mr-1"></i> Galeri Foto Momen Penyerahan
                            </label>
                            <span class="text-[11px] text-slate-500 font-medium">
                                Tersimpan <strong id="gallery-count-display" class="text-slate-800">{{ count($achievement->gallery_images ?? []) }}</strong> foto dokumentasi
                            </span>
                        </div>

                        <!-- Multi-select Toolbar for Deleting Gallery Photos -->
                        @if(!empty($achievement->gallery_images))
                            <div class="flex items-center gap-2 flex-wrap">
                                <label class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl cursor-pointer transition shadow-2xs select-none">
                                    <input type="checkbox" id="check-all-gallery" onchange="toggleSelectAllGallery(this.checked)" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300">
                                    <span>Pilih Semua Foto</span>
                                </label>

                                <button type="button" id="btn-delete-selected-gallery" onclick="deleteSelectedGalleryPhotos({{ $achievement->id }})" class="hidden text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 px-3.5 py-1.5 rounded-xl shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-trash-can"></i> Hapus Terpilih (<span id="selected-gallery-count">0</span>)
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Notice bar if items selected -->
                    <div id="selection-alert-bar" class="hidden bg-rose-50 border border-rose-300 text-rose-900 rounded-xl p-3 text-xs flex items-center justify-between gap-2 shadow-2xs">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm shrink-0"></i>
                            <span><strong id="selection-notice-text">0 foto</strong> ditandai untuk dihapus. Foto akan dihapus saat tombol <strong>"Simpan Perubahan"</strong> di bawah diklik.</span>
                        </div>
                        <button type="button" onclick="cancelAllGalleryDeletions()" class="text-[11px] font-bold text-rose-800 underline hover:text-rose-950 shrink-0 cursor-pointer">
                            Batal Pilih
                        </button>
                    </div>

                    <!-- Big Gallery Grid (Foto Diperbesar) -->
                    @if(!empty($achievement->gallery_images))
                        <div id="gallery-cards-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[520px] overflow-y-auto pr-1">
                            @foreach(($achievement->gallery_images ?? []) as $idx => $gImgPath)
                                @php
                                    $gUrl = (str_starts_with($gImgPath, 'http://') || str_starts_with($gImgPath, 'https://')) ? $gImgPath : asset('storage/' . $gImgPath);
                                @endphp
                                <div id="gallery-card-{{ $idx }}" class="gallery-photo-card relative group bg-white rounded-2xl border-2 border-slate-200 overflow-hidden shadow-2xs hover:shadow-md transition">
                                    
                                    <!-- Image Display (Large Viewport) -->
                                    <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100 flex items-center justify-center">
                                        <img src="{{ $gUrl }}" alt="Galeri Prestasi {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    </div>

                                    <!-- Overlay when marked for deletion -->
                                    <div id="gallery-overlay-{{ $idx }}" class="absolute inset-0 bg-rose-900/85 backdrop-blur-xs hidden flex-col items-center justify-center text-white text-center p-3 transition z-10">
                                        <i class="fa-solid fa-trash-can text-2xl mb-1 text-rose-300 animate-bounce"></i>
                                        <span class="font-black text-xs uppercase tracking-wider">Akan Dihapus</span>
                                        <span class="text-[10px] text-rose-200 mt-0.5">Dihapus saat Anda menyimpan form</span>
                                    </div>

                                    <!-- Top Left Photo Counter Badge -->
                                    <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-lg bg-black/60 text-white text-[10px] font-mono font-bold backdrop-blur-xs z-20">
                                        Foto #{{ $idx + 1 }}
                                    </span>

                                    <!-- Top Right Checkbox Selector for Deletion -->
                                    <label class="absolute top-2.5 right-2.5 bg-white/95 hover:bg-white text-slate-800 px-2.5 py-1 rounded-xl shadow-md border border-slate-200 cursor-pointer flex items-center gap-1.5 text-[11px] font-bold z-20 transition select-none hover:border-rose-400">
                                        <input type="checkbox" name="delete_gallery[]" value="{{ $idx }}" data-index="{{ $idx }}" onchange="toggleGalleryCard(this, {{ $idx }})" class="gallery-del-cb w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300 cursor-pointer">
                                        <span class="text-rose-600 font-extrabold"><i class="fa-solid fa-trash-can text-[10px]"></i> Hapus</span>
                                    </label>

                                    <!-- Bottom Action Bar -->
                                    <div class="p-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-1 text-[11px] relative z-20">
                                        <a href="{{ $gUrl }}" target="_blank" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-200 transition font-bold flex items-center gap-1" title="Lihat ukuran penuh di tab baru">
                                            <i class="fa-solid fa-magnifying-glass-plus text-[10px]"></i> Lihat Penuh
                                        </a>

                                        <button type="button" onclick="instantDeleteSingleGallery({{ $achievement->id }}, {{ $idx }})" class="px-2.5 py-1 rounded-lg text-rose-600 hover:bg-rose-100 hover:text-rose-800 transition font-bold flex items-center gap-1 cursor-pointer" title="Hapus foto ini secara instan langsung dari server">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus Sekarang
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center bg-white rounded-2xl border-2 border-dashed border-slate-200 text-slate-400">
                            <i class="fa-solid fa-images text-3xl mb-2 text-slate-300"></i>
                            <div class="text-xs font-bold text-slate-600">Belum Ada Foto Galeri</div>
                            <div class="text-[11px] text-slate-400 mt-1">Unggah foto dokumentasi penyerahan piala melalui input di bawah.</div>
                        </div>
                    @endif

                    <!-- Upload Input for Adding More Gallery Photos -->
                    <div class="pt-3 border-t border-slate-200 space-y-2">
                        <label for="gallery_images" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                            <span><i class="fa-solid fa-cloud-arrow-up mr-1 text-pmr-primary"></i> Tambah Foto Galeri Baru:</span>
                            <span class="text-[10px] text-slate-400 normal-case font-medium">Bisa memilih beberapa foto sekaligus</span>
                        </label>
                        <input type="file" name="gallery_images[]" id="gallery_images" multiple accept="image/*" onchange="previewNewGalleryFiles(this)"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-pmr-primary hover:file:bg-red-100 transition cursor-pointer border border-slate-200 rounded-xl bg-white p-1">
                        
                        <!-- New Gallery Photos Instant Local Preview Container -->
                        <div id="new-gallery-preview-container" class="hidden pt-2 space-y-2">
                            <div class="text-[11px] font-bold text-emerald-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check"></i> Pratinjau foto baru yang akan diunggah:
                            </div>
                            <div id="new-gallery-preview-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 p-3 bg-emerald-50/60 rounded-2xl border border-emerald-200"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ulasan / Narasi Singkat -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi / Cerita Singkat Pencapaian (Opsional)
                </label>
                <textarea name="description" id="description" rows="3"
                    class="w-full p-3.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition font-sans">{{ old('description', $achievement->description) }}</textarea>
            </div>
        </div>

        <!-- 4. Pengaturan Tampilan & Urutan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center pt-4 border-t border-slate-100">
            <div>
                <label for="order_position" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Prioritas Urutan
                </label>
                <input type="number" name="order_position" id="order_position" value="{{ old('order_position', $achievement->order_position) }}" min="1"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
            </div>

            <div class="pt-2 md:pt-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $achievement->is_featured) ? 'checked' : '' }}
                        class="w-5 h-5 rounded text-amber-500 focus:ring-amber-400 border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Tampilkan di Highlight Beranda</span>
                        <span class="text-[10px] text-slate-400">Muncul di etalase juara halaman utama</span>
                    </div>
                </label>
            </div>

            <div class="pt-2 md:pt-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $achievement->is_active) ? 'checked' : '' }}
                        class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Status Publik (Aktif)</span>
                        <span class="text-[10px] text-slate-400">Dapat dilihat publik di menu Prestasi</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('admin.prestasi.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // 1. Toggle Hapus Foto Utama Piala
    function toggleTrophyDelete(isDeleted) {
        const overlay = document.getElementById('trophy-delete-overlay');
        const checkbox = document.getElementById('delete_trophy_image');
        if (checkbox) checkbox.checked = isDeleted;

        if (overlay) {
            if (isDeleted) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            } else {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }
        }
    }

    function cancelDeleteTrophy() {
        toggleTrophyDelete(false);
    }

    // 2. Pratinjau Foto Utama Baru
    function previewNewTrophyFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('trophy-preview-img');
                const link = document.getElementById('trophy-zoom-link');
                if (img) img.src = e.target.result;
                if (link) link.href = e.target.result;
                toggleTrophyDelete(false);
            };
            reader.readAsDataURL(file);
        }
    }

    // 3. Instant Delete Foto Utama (AJAX)
    function instantDeleteTrophy(achievementId) {
        if (!confirm('Apakah Anda yakin ingin menghapus foto utama piala ini secara permanen dari server?')) {
            return;
        }

        fetch(`{{ url('admin/prestasi') }}/${achievementId}/trophy-image`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const img = document.getElementById('trophy-preview-img');
                if (img) img.src = 'https://images.unsplash.com/photo-1578269174936-2709b6aeb913?auto=format&fit=crop&w=600&q=80';
                const actionsBox = document.getElementById('trophy-actions-box');
                if (actionsBox) actionsBox.remove();
                toggleTrophyDelete(false);
                alert(data.message || 'Foto utama piala berhasil dihapus.');
            } else {
                alert(data.message || 'Gagal menghapus foto utama piala.');
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan jaringan saat menghapus foto: ' + err.message);
        });
    }

    // 4. Toggle Seleksi Kartu Galeri
    function toggleGalleryCard(checkbox, idx) {
        const overlay = document.getElementById(`gallery-overlay-${idx}`);
        const card = document.getElementById(`gallery-card-${idx}`);
        
        if (checkbox.checked) {
            if (overlay) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }
            if (card) {
                card.classList.add('border-rose-400', 'ring-2', 'ring-rose-200');
            }
        } else {
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }
            if (card) {
                card.classList.remove('border-rose-400', 'ring-2', 'ring-rose-200');
            }
        }
        updateGallerySelectionCount();
    }

    function updateGallerySelectionCount() {
        const cbs = document.querySelectorAll('.gallery-del-cb:checked');
        const count = cbs.length;
        const total = document.querySelectorAll('.gallery-del-cb').length;
        
        const countDisplay = document.getElementById('selected-gallery-count');
        const alertBar = document.getElementById('selection-alert-bar');
        const noticeText = document.getElementById('selection-notice-text');
        const btnDeleteSelected = document.getElementById('btn-delete-selected-gallery');
        const checkAll = document.getElementById('check-all-gallery');

        if (countDisplay) countDisplay.textContent = count;
        if (noticeText) noticeText.textContent = `${count} foto`;

        if (count > 0) {
            if (alertBar) alertBar.classList.remove('hidden');
            if (btnDeleteSelected) btnDeleteSelected.classList.remove('hidden');
        } else {
            if (alertBar) alertBar.classList.add('hidden');
            if (btnDeleteSelected) btnDeleteSelected.classList.add('hidden');
        }

        if (checkAll && total > 0) {
            checkAll.checked = (count === total);
        }
    }

    // 5. Pilih Semua / Batal Semua Galeri
    function toggleSelectAllGallery(isChecked) {
        const cbs = document.querySelectorAll('.gallery-del-cb');
        cbs.forEach(cb => {
            cb.checked = isChecked;
            const idx = cb.dataset.index;
            toggleGalleryCard(cb, idx);
        });
    }

    function cancelAllGalleryDeletions() {
        toggleSelectAllGallery(false);
        const checkAll = document.getElementById('check-all-gallery');
        if (checkAll) checkAll.checked = false;
    }

    // 6. Instant Delete Single Galeri (AJAX)
    function instantDeleteSingleGallery(achievementId, index) {
        if (!confirm('Apakah Anda yakin ingin menghapus foto galeri ini secara permanen dari server?')) {
            return;
        }

        const card = document.getElementById(`gallery-card-${index}`);
        if (card) card.style.opacity = '0.5';

        fetch(`{{ url('admin/prestasi') }}/${achievementId}/gallery/${index}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (card) {
                    card.classList.add('scale-90', 'opacity-0');
                    setTimeout(() => card.remove(), 250);
                }
                const countEl = document.getElementById('gallery-count-display');
                if (countEl && data.remaining_count !== undefined) {
                    countEl.textContent = data.remaining_count;
                }
                setTimeout(updateGallerySelectionCount, 300);
            } else {
                if (card) card.style.opacity = '1';
                alert(data.message || 'Gagal menghapus foto galeri.');
            }
        })
        .catch(err => {
            if (card) card.style.opacity = '1';
            alert('Terjadi kesalahan jaringan saat menghapus foto: ' + err.message);
        });
    }

    // 7. Instant Delete Selected Photos (AJAX)
    async function deleteSelectedGalleryPhotos(achievementId) {
        const cbs = Array.from(document.querySelectorAll('.gallery-del-cb:checked'));
        if (cbs.length === 0) return;

        if (!confirm(`Hapus permanen ${cbs.length} foto terpilih dari server sekarang?`)) {
            return;
        }

        const indexes = cbs.map(cb => parseInt(cb.dataset.index)).sort((a, b) => b - a);
        let successCount = 0;

        for (const idx of indexes) {
            try {
                const res = await fetch(`{{ url('admin/prestasi') }}/${achievementId}/gallery/${idx}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    successCount++;
                    const card = document.getElementById(`gallery-card-${idx}`);
                    if (card) card.remove();
                }
            } catch (e) {
                console.error(e);
            }
        }

        alert(`${successCount} foto berhasil dihapus permanen.`);
        location.reload();
    }

    // 8. Pratinjau Foto Galeri Baru Yang Dipilih
    function previewNewGalleryFiles(input) {
        const container = document.getElementById('new-gallery-preview-container');
        const grid = document.getElementById('new-gallery-preview-grid');
        if (!container || !grid) return;

        grid.innerHTML = '';
        if (input.files && input.files.length > 0) {
            container.classList.remove('hidden');
            Array.from(input.files).forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative rounded-xl overflow-hidden border border-emerald-300 aspect-[4/3] bg-white shadow-2xs group';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded bg-black/70 text-[9px] font-bold text-white truncate max-w-[90%]">
                            ${file.name}
                        </span>
                    `;
                    grid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        } else {
            container.classList.add('hidden');
        }
    }
</script>
@endpush
