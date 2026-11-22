@extends('layouts.admin')

@section('title', 'Dokumentasi & Galeri Momen Lomba')
@section('page_title', 'Dokumentasi & Galeri Lomba (Potret Sportivitas)')

@section('top_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('lomba.index', ['edisi' => $selectedEvent?->id]) }}#kilas-balik" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-3 sm:px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-arrow-up-right-from-square text-amber-400"></i>
            <span>Lihat di Web Publik /lomba</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-xmark text-rose-500 text-xl"></i>
            <div class="text-sm font-semibold">{{ session('error') }}</div>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl shadow-xs">
            <div class="font-bold text-xs uppercase mb-1 flex items-center gap-1.5 text-rose-700">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Gagal Mengunggah Foto:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 1. Pilihan Edisi Lomba Yang Dikelola -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 text-white rounded-3xl p-6 sm:p-7 border border-slate-700 shadow-md">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center text-2xl font-black shrink-0 shadow-inner">
                    <i class="fa-solid fa-camera-retro"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-400">Pilih Edisi Penyelenggaraan:</span>
                        @if($selectedEvent && $selectedEvent->is_active)
                            <span class="text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-bold px-2 py-0.5 rounded-full uppercase">
                                Edisi Aktif
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white mt-0.5">
                        {{ $selectedEvent->title ?? 'Pilih Edisi Lomba' }}
                    </h2>
                    <p class="text-xs text-slate-300 mt-1">
                        Foto yang diunggah di bawah ini akan tampil di bagian <strong>Potret Sportivitas & Aksi Peserta Lomba</strong> pada edisi ini.
                    </p>
                </div>
            </div>

            <!-- Dropdown Switcher Edisi -->
            <div class="w-full md:w-auto">
                <label for="event-selector" class="sr-only">Pilih Edisi</label>
                <div class="relative">
                    <select id="event-selector" onchange="window.location.href = '{{ route('admin.competition-galleries.index') }}?event_id=' + this.value" class="w-full md:w-72 bg-slate-800 hover:bg-slate-700 border border-slate-600 text-white text-xs font-bold rounded-xl px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer shadow-xs transition appearance-none">
                        @foreach($events as $ev)
                            <option value="{{ $ev->id }}" {{ ($selectedEvent && $selectedEvent->id === $ev->id) ? 'selected' : '' }}>
                                {{ $ev->title }} ({{ $ev->start_date ? $ev->start_date->format('Y') : '-' }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Form Unggah Foto Dokumentasi Baru (Multiple Upload) -->
    @if($selectedEvent)
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-lg">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 text-base">Unggah Foto Momen Kegiatan</h3>
                        <p class="text-xs text-slate-500">Anda dapat memilih satu atau beberapa foto sekaligus (JPG, PNG, WEBP, Maksimal 10MB per foto).</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-lg hidden sm:inline-block">
                    Mendukung Multi-Upload
                </span>
            </div>

            <form action="{{ route('admin.competition-galleries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="competition_event_id" value="{{ $selectedEvent->id }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- File Input (Multiple) -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih File Foto <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="image_files[]" multiple required accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl bg-slate-50 focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                        <span class="text-[10px] text-slate-400 mt-1 block">Tahan tombol <strong>Ctrl / Cmd</strong> untuk memilih beberapa foto sekaligus.</span>
                    </div>

                    <!-- Keterangan / Caption -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Keterangan Momen / Caption (Opsional)
                        </label>
                        <input type="text" name="caption" placeholder="Contoh: Aksi Pasang Tandu Bambu Cepat" class="w-full px-4 py-2 text-xs border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:border-red-500 font-medium">
                        <span class="text-[10px] text-slate-400 mt-1 block">Jika dikosongkan, nama file akan dijadikan caption otomatis.</span>
                    </div>

                    <!-- Kategori / Tag -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kategori / Tag Momen
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="tag" placeholder="Pertolongan Pertama / Tandu / Suasana / Juara" list="tag-suggestions" class="w-full px-4 py-2 text-xs border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:border-red-500 font-medium">
                            <datalist id="tag-suggestions">
                                <option value="Pertolongan Pertama">
                                <option value="Ketangkasan Tandu">
                                <option value="Cepat Tepat LCT">
                                <option value="Pendidikan Remaja Sebaya">
                                <option value="Mading Kreasi">
                                <option value="Lomba Mewarnai">
                                <option value="Upacara Pembukaan">
                                <option value="Panggung Juara">
                                <option value="Aksi Peserta Lomba">
                            </datalist>

                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-black px-5 py-2 rounded-xl text-xs shadow-md transition flex items-center gap-1.5 shrink-0">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span>Unggah</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- 3. Daftar Foto Terunggah Pada Edisi Ini -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <h3 class="font-black text-slate-900 text-base">Koleksi Foto Terunggah</h3>
                    <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded-full border border-amber-200">
                        {{ $galleries->count() }} Foto
                    </span>
                </div>
                <div class="text-xs text-slate-400">
                    Edisi: <strong>{{ $selectedEvent->title }}</strong>
                </div>
            </div>

            @if($galleries->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($galleries as $gallery)
                        <div class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200 shadow-2xs group hover:border-red-300 transition flex flex-col justify-between">
                            <div>
                                <div class="relative h-44 bg-slate-900 overflow-hidden">
                                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->caption }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                                    @if($gallery->tag)
                                        <span class="absolute top-2.5 left-2.5 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-md border border-slate-700">
                                            {{ $gallery->tag }}
                                        </span>
                                    @endif
                                    <span class="absolute bottom-2.5 left-2.5 right-2.5 text-white font-bold text-xs truncate">
                                        {{ $gallery->caption ?: 'Dokumentasi Lomba' }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between gap-2">
                                <span class="text-[10px] text-slate-400 font-mono">
                                    {{ $gallery->created_at ? $gallery->created_at->format('d M Y') : '-' }}
                                </span>

                                <div class="flex items-center gap-1.5">
                                    <!-- Edit Modal Button -->
                                    <button type="button" onclick="openEditGalleryModal({{ $gallery->id }}, '{{ addslashes($gallery->caption) }}', '{{ addslashes($gallery->tag) }}')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition text-xs" title="Edit Caption">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.competition-galleries.destroy', $gallery->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus foto ini dari dokumentasi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition text-xs" title="Hapus Foto">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="py-12 text-center text-slate-400">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3 shadow-inner">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <div class="font-bold text-slate-800 text-sm">Belum Ada Foto Terunggah Untuk Edisi Ini</div>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                        Gunakan form di atas untuk mengunggah beberapa foto momen lomba. Foto-foto tersebut akan langsung tampil di halaman depan pada bagian <strong>Potret Sportivitas & Aksi Peserta Lomba</strong>.
                    </p>
                </div>
            @endif
        </div>
    @endif

</div>

<!-- Modal Edit Caption / Tag -->
<div id="modal-edit-gallery" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <h4 class="font-black text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-pen text-red-600"></i> Edit Keterangan Foto
            </h4>
            <button type="button" onclick="closeEditGalleryModal()" class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <form id="form-edit-gallery" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Caption / Keterangan</label>
                <input type="text" name="caption" id="modal-edit-caption" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-red-500 font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tag / Kategori Momen</label>
                <input type="text" name="tag" id="modal-edit-tag" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-red-500 font-medium">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditGalleryModal()" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-black px-4 py-2 rounded-xl text-xs shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditGalleryModal(id, caption, tag) {
        const form = document.getElementById('form-edit-gallery');
        form.action = '{{ url("admin/competition-galleries") }}/' + id;
        document.getElementById('modal-edit-caption').value = caption;
        document.getElementById('modal-edit-tag').value = tag;

        const modal = document.getElementById('modal-edit-gallery');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditGalleryModal() {
        const modal = document.getElementById('modal-edit-gallery');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
