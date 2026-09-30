@extends('layouts.admin')

@section('title', 'Edit Slide Hero')
@section('page_title', 'Perbarui Slide Gambar Hero')

@section('top_actions')
    <a href="{{ route('admin.hero-slides.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm">
        <form action="{{ route('admin.hero-slides.update', $slide->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Judul Slide -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Judul / Label Slide <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" required value="{{ old('title', $slide->title) }}" 
                       class="w-full text-base px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-bold">
            </div>

            <!-- Path / URL Gambar -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Path / URL Gambar Latar <span class="text-red-500">*</span>
                </label>
                <input type="text" name="image_path" id="image_path" required value="{{ old('image_path', $slide->image_path) }}" 
                       class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                
                <!-- Preview Gambar Saat Ini -->
                <div class="mt-3 relative h-36 rounded-2xl overflow-hidden bg-slate-900 border border-slate-200">
                    <img src="{{ $slide->image_path }}" alt="Preview" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-950/80 via-pmr-primary/70 to-stone-950/70 flex items-center px-4">
                        <span class="text-white text-xs font-semibold">Pratinjau dengan gradien merah PMR</span>
                    </div>
                </div>
            </div>

            <!-- Keterangan / Caption -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Keterangan Singkat / Catatan Slide (Opsional)
                </label>
                <textarea name="caption" rows="2" 
                          class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">{{ old('caption', $slide->caption) }}</textarea>
            </div>

            <!-- Urutan & Status Aktif -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Urutan Tampil
                    </label>
                    <input type="number" name="order_position" value="{{ old('order_position', $slide->order_position) }}" min="1" 
                           class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                </div>

                <div class="sm:pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $slide->is_active) ? 'checked' : '' }} class="w-5 h-5 accent-pmr-primary rounded">
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
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Slide
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
