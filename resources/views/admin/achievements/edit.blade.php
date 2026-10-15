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
        <div class="space-y-4 pt-2">
            <h2 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2 pb-2 border-b border-slate-100">
                <i class="fa-solid fa-images"></i> 3. Dokumentasi Foto Piala & Galeri Penyerahan
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Foto Utama Piala -->
                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Foto Utama Piala / Trofi / Medali
                    </label>
                    @if($achievement->trophy_image)
                        <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-slate-200">
                            <img src="{{ $achievement->trophy_image_url }}" alt="Foto Piala" class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                            <div class="text-[11px] text-slate-500">
                                <span class="font-bold text-slate-800 block">Foto Saat Ini</span>
                                Unggah file di bawah jika ingin mengganti.
                            </div>
                        </div>
                    @endif
                    <input type="file" name="trophy_image" id="trophy_image" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-pmr-primary hover:file:bg-red-100 transition cursor-pointer">
                    <p class="text-[11px] text-slate-400">Kosongkan jika tidak ingin mengganti foto piala.</p>
                </div>

                <!-- Multi-upload Galeri Foto Penyerahan Piala -->
                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Galeri Foto Momen Penyerahan Piala
                    </label>
                    @if(!empty($achievement->gallery_urls))
                        <div class="flex flex-wrap gap-2 p-2 bg-white rounded-xl border border-slate-200 max-h-28 overflow-y-auto">
                            @foreach($achievement->gallery_urls as $gUrl)
                                <img src="{{ $gUrl }}" alt="Galeri" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                            @endforeach
                        </div>
                    @endif
                    <input type="file" name="gallery_images[]" id="gallery_images" multiple accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-pmr-primary hover:file:bg-red-100 transition cursor-pointer">
                    <p class="text-[11px] text-slate-400">Pilih file tambahan untuk menambah dokumentasi foto piala & momen penyerahan.</p>
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
