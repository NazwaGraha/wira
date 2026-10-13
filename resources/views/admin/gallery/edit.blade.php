@extends('layouts.admin')

@section('title', 'Edit Media Galeri')
@section('page_title', 'Edit Media Galeri')

@section('content')
<div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
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
                <input type="text" name="title" value="{{ old('title', $gallery->title) }}" required class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 font-bold text-slate-800">
            </div>

            <!-- Kategori Galeri Dropdown dengan Modal Tambah Cepat -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label for="gallery_category_id" class="block text-sm font-bold text-slate-700">
                        Kategori Galeri (Opsional)
                    </label>
                    <button type="button" onclick="openQuickCategoryModal()" class="text-xs font-bold text-pmr-primary hover:text-red-700 hover:underline flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Kategori
                    </button>
                </div>
                <select name="gallery_category_id" id="gallery_category_id" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 font-semibold text-slate-800">
                    <option value="">-- Pilih Kategori Galeri --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('gallery_category_id', $gallery->gallery_category_id ?? ($gallery->category === $cat->name ? $cat->id : '')) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Kategori diambil langsung dari database. Anda dapat menambah kategori baru secara instan melalui tombol di atas.</p>
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

<!-- Modal Quick Tambah Kategori Galeri -->
<div id="quickCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-folder-plus"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Tambah Kategori Galeri Baru</h3>
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
                    Nama Kategori Galeri <span class="text-red-500">*</span>
                </label>
                <input type="text" id="quick_cat_name" required placeholder="Contoh: Latihan Gabungan & Simulasi" 
                       class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Singkat (Opsional)
                </label>
                <input type="text" id="quick_cat_desc" placeholder="Contoh: Foto-foto aktivitas simulasi pertolongan..." 
                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary text-slate-800">
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
    const description = document.getElementById('quick_cat_desc').value.trim();

    if (!name) return;

    submitBtn.disabled = true;
    const originalBtnHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Menyimpan...</span>';
    errorBox.classList.add('hidden');

    try {
        const response = await fetch("{{ route('admin.gallery-categories.quick-store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, description })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            const selectEl = document.getElementById('gallery_category_id');
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
</script>
@endpush
