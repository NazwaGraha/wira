@extends('layouts.admin')

@section('title', 'Tambah Slide Hero Baru')
@section('page_title', 'Tambah Slide Gambar Hero Baru')

@section('top_actions')
    <a href="{{ route('admin.hero-slides.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm">
        <form action="{{ route('admin.hero-slides.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Judul Slide -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Judul / Label Slide <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" required value="{{ old('title') }}" 
                       placeholder="Contoh: Apel Siaga Kemanusiaan 2026" 
                       class="w-full text-base px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-bold">
            </div>

            <!-- Path / URL Gambar -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Path / URL Gambar Latar <span class="text-red-500">*</span>
                </label>
                <input type="text" name="image_path" id="image_path" required value="{{ old('image_path') }}" 
                       placeholder="/mockups/01_home.jpg atau https://..." 
                       class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                
                <div class="flex items-center gap-2 mt-2.5 text-xs text-slate-500 flex-wrap">
                    <span class="font-semibold text-slate-600">Pilihan Cepat Gambar Yang Tersedia:</span>
                    <button type="button" onclick="document.getElementById('image_path').value='/mockups/01_home.jpg'" class="text-pmr-primary underline font-bold">Beranda 01</button>
                    &bull;
                    <button type="button" onclick="document.getElementById('image_path').value='/mockups/02_tentang_kami.jpg'" class="text-pmr-primary underline font-bold">Tentang Kami 02</button>
                    &bull;
                    <button type="button" onclick="document.getElementById('image_path').value='/mockups/03_kegiatan.jpg'" class="text-pmr-primary underline font-bold">Pelatihan PP 03</button>
                    &bull;
                    <button type="button" onclick="document.getElementById('image_path').value='/mockups/04_galeri.jpg'" class="text-pmr-primary underline font-bold">Dokumentasi Galeri 04</button>
                    &bull;
                    <button type="button" onclick="document.getElementById('image_path').value='/mockups/05_donor_darah.jpg'" class="text-pmr-primary underline font-bold">Donor Darah 05</button>
                </div>
            </div>

            <!-- Keterangan / Caption -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Keterangan Singkat / Catatan Slide (Opsional)
                </label>
                <textarea name="caption" rows="2" placeholder="Catatan atau deskripsi foto..." 
                          class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">{{ old('caption') }}</textarea>
            </div>

            <!-- Urutan & Status Aktif -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Urutan Tampil
                    </label>
                    <input type="number" name="order_position" value="{{ old('order_position', 1) }}" min="1" 
                           class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                </div>

                <div class="sm:pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 accent-pmr-primary rounded">
                        <span class="text-xs font-bold text-slate-800">Aktifkan Slide di Beranda</span>
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
@endsection
