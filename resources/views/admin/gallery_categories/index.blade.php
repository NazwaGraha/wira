@extends('layouts.admin')

@section('title', 'Kelola Kategori Galeri')
@section('page_title', 'Manajemen Kategori Galeri Foto & Video')

@section('top_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.gallery.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Media Baru
        </a>
        <a href="{{ route('admin.gallery.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-images"></i> Kelola Galeri
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl shrink-0"></i>
            <div class="text-sm font-semibold">{!! session('success') !!}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl shrink-0"></i>
            <div class="text-sm font-semibold">{!! session('error') !!}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-start gap-3 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg mt-0.5 shrink-0"></i>
            <div class="text-sm">
                <span class="font-bold">Gagal menyimpan data:</span>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- KOLOM 1: FORM INPUT KATEGORI BARU -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-5">
            <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-red-50 text-pmr-primary flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-folder-plus"></i>
                </div>
                <div>
                    <h2 class="font-extrabold text-slate-800 text-base">Tambah Kategori Baru</h2>
                    <p class="text-[11px] text-slate-400">Input kategori untuk opsi dropdown galeri</p>
                </div>
            </div>

            <form action="{{ route('admin.gallery-categories.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Kategori -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                           placeholder="Contoh: Latihan Gabungan & Simulasi"
                           class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-bold text-slate-900">
                </div>

                <!-- Slug Kategori -->
                <div>
                    <label for="slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Slug URL (Opsional)
                    </label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                           placeholder="latihan-gabungan-simulasi"
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-mono text-slate-700">
                    <span class="text-[10px] text-slate-400 mt-1 block">Otomatis digenerate dari nama jika dikosongkan.</span>
                </div>

                <!-- Deskripsi Kategori -->
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <textarea name="description" id="description" rows="3"
                              placeholder="Dokumentasi seputar kegiatan latihan dan simulasi kebencanaan..."
                              class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary text-slate-700">{{ old('description') }}</textarea>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider py-3 rounded-xl shadow-md shadow-red-950/20 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>

        <!-- KOLOM 2 & 3: TABEL DAFTAR KATEGORI TERSIMPAN -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-folder-tree"></i>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-slate-800 text-base">Daftar Kategori Galeri</h2>
                        <p class="text-[11px] text-slate-400">Total {{ $categories->count() }} kategori terdaftar di database</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Kategori & Slug</th>
                            <th class="px-6 py-4">Deskripsi</th>
                            <th class="px-6 py-4 text-center">Jumlah Media</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-images"></i>
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-slate-800 text-sm">{{ $category->name }}</div>
                                            <div class="text-[11px] font-mono text-slate-400">/{{ $category->slug }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 max-w-xs">
                                    <div class="text-xs text-slate-600 line-clamp-2">
                                        {{ $category->description ?: '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                        <i class="fa-solid fa-photo-film text-[10px]"></i>
                                        <span>{{ $category->galleries_count }} Media</span>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Edit Modal -->
                                        <button type="button" 
                                                onclick="openEditCategoryModal({{ json_encode($category) }})"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition"
                                                title="Edit Kategori">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('admin.gallery-categories.destroy', $category->id) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $category->name }}\'? Media yang terkait tidak akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Hapus Kategori">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                    Belum ada kategori galeri yang dibuat. Gunakan form di sebelah kiri untuk menambah kategori baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- MODAL EDIT KATEGORI -->
<div id="editCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Edit Kategori Galeri</h3>
                    <p class="text-[11px] text-slate-500">Perbarui informasi nama dan deskripsi kategori</p>
                </div>
            </div>
            <button type="button" onclick="closeEditCategoryModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-200/50 flex items-center justify-center transition">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <form id="editCategoryForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Edit Nama Kategori -->
            <div>
                <label for="edit_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="edit_name" required
                       class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-bold text-slate-900">
            </div>

            <!-- Edit Slug Kategori -->
            <div>
                <label for="edit_slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Slug URL
                </label>
                <input type="text" name="slug" id="edit_slug"
                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-mono text-slate-700">
            </div>

            <!-- Edit Deskripsi -->
            <div>
                <label for="edit_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Singkat
                </label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary text-slate-700"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-pmr-primary text-white hover:bg-pmr-dark shadow-md shadow-red-950/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditCategoryModal(category) {
        const form = document.getElementById('editCategoryForm');
        form.action = `/admin/gallery/categories/${category.id}`;
        
        document.getElementById('edit_name').value = category.name || '';
        document.getElementById('edit_slug').value = category.slug || '';
        document.getElementById('edit_description').value = category.description || '';
        
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }

    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
    }
</script>
@endpush
