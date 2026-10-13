@extends('layouts.admin')

@section('title', 'Kelola Kategori Artikel')
@section('page_title', 'Manajemen Kategori Artikel & Tips P3K')

@section('top_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.articles.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tulis Artikel Baru
        </a>
        <a href="{{ route('admin.articles.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-newspaper"></i> Kelola Artikel
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
                    <i class="fa-solid fa-tag"></i>
                </div>
                <div>
                    <h2 class="font-extrabold text-slate-800 text-base">Tambah Kategori Baru</h2>
                    <p class="text-[11px] text-slate-400">Input kategori untuk opsi dropdown artikel</p>
                </div>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Kategori -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                           placeholder="Contoh: Tips Pertolongan Pertama"
                           class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-bold text-slate-900">
                </div>

                <!-- Slug Kategori -->
                <div>
                    <label for="slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Slug URL (Opsional)
                    </label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                           placeholder="tips-pertolongan-pertama"
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-mono text-slate-700">
                    <span class="text-[10px] text-slate-400 mt-1 block">Otomatis diisi dari nama jika dikosongkan.</span>
                </div>

                <!-- Warna Badge -->
                <div>
                    <label for="color" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Warna Label / Badge
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" id="color" value="{{ old('color', '#980000') }}"
                               class="w-10 h-10 p-0.5 rounded-xl border border-slate-300 cursor-pointer">
                        <input type="text" id="color_hex" value="{{ old('color', '#980000') }}"
                               class="w-28 text-xs font-mono uppercase px-3 py-2 rounded-xl border border-slate-300 text-slate-700"
                               oninput="document.getElementById('color').value = this.value">
                    </div>
                    
                    <!-- Preset Palet Warna -->
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[10px] text-slate-400">Pilihan Cepat:</span>
                        <button type="button" onclick="setColor('#980000')" class="w-5 h-5 rounded-full bg-[#980000] border border-white shadow-xs" title="Merah PMR"></button>
                        <button type="button" onclick="setColor('#dc2626')" class="w-5 h-5 rounded-full bg-red-600 border border-white shadow-xs" title="Merah Terang"></button>
                        <button type="button" onclick="setColor('#059669')" class="w-5 h-5 rounded-full bg-emerald-600 border border-white shadow-xs" title="Hijau Medis"></button>
                        <button type="button" onclick="setColor('#0284c7')" class="w-5 h-5 rounded-full bg-sky-600 border border-white shadow-xs" title="Biru"></button>
                        <button type="button" onclick="setColor('#ea580c')" class="w-5 h-5 rounded-full bg-orange-600 border border-white shadow-xs" title="Oranye Siaga"></button>
                        <button type="button" onclick="setColor('#7c3aed')" class="w-5 h-5 rounded-full bg-purple-600 border border-white shadow-xs" title="Ungu"></button>
                    </div>
                </div>

                <!-- Deskripsi Kategori -->
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <textarea name="description" id="description" rows="2"
                              placeholder="Deskripsi materi atau topik yang dicakup dalam kategori ini..."
                              class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary leading-relaxed">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider py-3 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Kategori Baru
                </button>
            </form>
        </div>

        <!-- KOLOM 2: DAFTAR KATEGORI DI DATABASE -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-slate-800 text-base">Daftar Kategori Tersimpan</h2>
                        <p class="text-[11px] text-slate-400">Total: {{ $categories->count() }} Kategori aktif</p>
                    </div>
                </div>
            </div>

            @if($categories->isEmpty())
                <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                    <i class="fa-solid fa-tags text-slate-300 text-4xl mb-2"></i>
                    <p class="text-sm font-semibold text-slate-600">Belum ada kategori yang dibuat.</p>
                    <p class="text-xs text-slate-400 mt-0.5">Gunakan formulir di sebelah kiri untuk menambahkan kategori artikel pertama Anda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider border-b border-slate-200 font-bold">
                            <tr>
                                <th class="py-3 px-4">Kategori & Badge</th>
                                <th class="py-3 px-4">Slug</th>
                                <th class="py-3 px-4">Deskripsi</th>
                                <th class="py-3 px-4 text-center">Jumlah Artikel</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($categories as $cat)
                                <tr class="hover:bg-slate-50/60 transition group">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-2xs border border-white" style="background-color: {{ $cat->color ?? '#980000' }};"></span>
                                            <div>
                                                <span class="font-bold text-slate-900 block text-sm">{{ $cat->name }}</span>
                                                <span class="inline-block text-[10px] font-semibold px-2 py-0.2 rounded-md text-white mt-0.5" style="background-color: {{ $cat->color ?? '#980000' }};">
                                                    Badge Tag
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]">
                                        {{ $cat->slug }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate text-[11px]">
                                        {{ $cat->description ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $cat->articles_count > 0 ? 'bg-red-50 text-pmr-primary border border-red-200' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $cat->articles_count }} artikel
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->slug) }}', '{{ $cat->color ?? '#980000' }}', '{{ addslashes($cat->description ?? '') }}')"
                                                    class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit Kategori">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $cat->name }}\'?');" 
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Kategori">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

</div>

<!-- MODAL EDIT KATEGORI -->
<div id="editCategoryModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-pmr-primary"></i> Edit Kategori Artikel
            </h3>
            <button type="button" onclick="closeEditCategoryModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editCategoryForm" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="edit_name" required
                       class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-bold text-slate-900">
            </div>

            <div>
                <label for="edit_slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Slug URL
                </label>
                <input type="text" name="slug" id="edit_slug"
                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary font-mono text-slate-700">
            </div>

            <div>
                <label for="edit_color" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Warna Badge
                </label>
                <div class="flex items-center gap-3">
                    <input type="color" name="color" id="edit_color"
                           class="w-10 h-10 p-0.5 rounded-xl border border-slate-300 cursor-pointer">
                    <input type="text" id="edit_color_hex"
                           class="w-28 text-xs font-mono uppercase px-3 py-2 rounded-xl border border-slate-300 text-slate-700"
                           oninput="document.getElementById('edit_color').value = this.value">
                </div>
            </div>

            <div>
                <label for="edit_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Singkat
                </label>
                <textarea name="description" id="edit_description" rows="2"
                          class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary leading-relaxed"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setColor(hex) {
        document.getElementById('color').value = hex;
        document.getElementById('color_hex').value = hex;
    }

    document.getElementById('color').addEventListener('input', function() {
        document.getElementById('color_hex').value = this.value;
    });

    document.getElementById('edit_color').addEventListener('input', function() {
        document.getElementById('edit_color_hex').value = this.value;
    });

    // Auto-generate slug from name in create form
    document.getElementById('name').addEventListener('input', function() {
        const slugInput = document.getElementById('slug');
        if (!slugInput.dataset.manual) {
            slugInput.value = this.value.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)+/g, '');
        }
    });

    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    // Modal Edit Functions
    function openEditCategoryModal(id, name, slug, color, description) {
        const modal = document.getElementById('editCategoryModal');
        const form = document.getElementById('editCategoryForm');
        
        form.action = "{{ url('admin/kategori-artikel') }}/" + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_slug').value = slug;
        document.getElementById('edit_color').value = color || '#980000';
        document.getElementById('edit_color_hex').value = color || '#980000';
        document.getElementById('edit_description').value = description || '';

        modal.classList.remove('hidden');
    }

    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
    }
</script>
@endpush
