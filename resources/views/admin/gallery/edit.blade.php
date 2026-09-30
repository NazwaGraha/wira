@extends('layouts.admin')

@section('title', 'Edit Media Galeri')
@section('page_title', 'Edit Media Galeri')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <form action="{{ route('admin.gallery.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Media <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl cursor-pointer">
                        <input type="radio" name="type" value="photo" {{ old('type', $gallery->type) == 'photo' ? 'checked' : '' }} class="w-4 h-4 text-pmr-primary focus:ring-pmr-primary" onchange="toggleVideoFields()">
                        <span class="font-bold text-slate-700">Foto</span>
                    </label>
                    <label class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl cursor-pointer">
                        <input type="radio" name="type" value="video" {{ old('type', $gallery->type) == 'video' ? 'checked' : '' }} class="w-4 h-4 text-pmr-primary focus:ring-pmr-primary" onchange="toggleVideoFields()">
                        <span class="font-bold text-slate-700">Video</span>
                    </label>
                </div>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Judul / Caption <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $gallery->title) }}" required class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Kategori (Opsional)</label>
                <select name="category" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Pertolongan Pertama" {{ old('category', $gallery->category) == 'Pertolongan Pertama' ? 'selected' : '' }}>Pertolongan Pertama</option>
                    <option value="Organisasi" {{ old('category', $gallery->category) == 'Organisasi' ? 'selected' : '' }}>Organisasi</option>
                    <option value="Donor Darah" {{ old('category', $gallery->category) == 'Donor Darah' ? 'selected' : '' }}>Donor Darah</option>
                    <option value="Upacara & Apel" {{ old('category', $gallery->category) == 'Upacara & Apel' ? 'selected' : '' }}>Upacara & Apel</option>
                    <option value="Sosialisasi" {{ old('category', $gallery->category) == 'Sosialisasi' ? 'selected' : '' }}>Sosialisasi</option>
                    <option value="Bakti Sosial" {{ old('category', $gallery->category) == 'Bakti Sosial' ? 'selected' : '' }}>Bakti Sosial</option>
                    <option value="Lainnya" {{ old('category', $gallery->category) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Unggah Foto / Thumbnail Video</label>
                <div class="flex items-start gap-4">
                    <div class="w-32 h-20 bg-slate-100 rounded-xl overflow-hidden border border-slate-200 flex-shrink-0">
                        <img src="{{ Storage::url($gallery->image_path) }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-grow">
                        <input type="file" name="image_file" accept="image/*" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        <div class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengganti gambar.</div>
                    </div>
                </div>
            </div>

            <div id="video_fields" class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" style="display: {{ old('type', $gallery->type) == 'video' ? 'grid' : 'none' }};">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">URL Video (Youtube/URL lainnya)</label>
                    <input type="url" name="video_url" value="{{ old('video_url', $gallery->video_url) }}" placeholder="https://youtube.com/..." class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Durasi Video (Opsional)</label>
                    <input type="text" name="duration" value="{{ old('duration', $gallery->duration) }}" placeholder="Contoh: 4 Menit 15 Detik" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $gallery->is_active) ? 'checked' : '' }} class="w-5 h-5 text-red-500 rounded border-slate-300 focus:ring-red-500">
                    <div>
                        <div class="font-bold text-slate-800">Tampilkan di Galeri Publik</div>
                        <div class="text-xs text-slate-500">Hilangkan centang jika ingin menyembunyikan media ini dari publik.</div>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
            <a href="{{ route('admin.gallery.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">Batal</a>
            <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white px-5 py-2.5 rounded-xl font-bold shadow-md shadow-red-950/20 transition flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleVideoFields() {
    const isVideo = document.querySelector('input[name="type"]:checked').value === 'video';
    const videoFields = document.getElementById('video_fields');
    if(isVideo) {
        videoFields.style.display = 'grid';
    } else {
        videoFields.style.display = 'none';
    }
}
</script>
@endpush
