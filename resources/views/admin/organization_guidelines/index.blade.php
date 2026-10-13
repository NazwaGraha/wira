@extends('layouts.admin')

@section('title', 'Manajemen Pedoman Organisasi (GBHO & PPUO)')
@section('page_title', 'Pedoman Organisasi (GBHO & PPUO)')

@section('top_actions')
    <a href="{{ route('pedoman-organisasi') }}" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl shadow transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
        <span>Lihat Halaman Publik</span>
    </a>
@endsection

@section('content')
<div x-data="{ 
    activeTab: '{{ request('tab', 'gbho') }}',
    strategyModal: false,
    strategyMode: 'add',
    strategyId: null,
    strategyTitle: '',
    strategyDescription: '',
    strategyIcon: 'fa-solid fa-check',
    strategyOrder: 1,
    chapterModal: false,
    chapterMode: 'add',
    chapterId: null,
    chapterNumber: '',
    chapterTitle: '',
    chapterSummary: '',
    chapterBadge: '',
    chapterContent: '',
    chapterOrder: 1
}" class="space-y-6">

    <!-- Header Description Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 bg-amber-50 text-amber-800 border border-amber-200/80 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider mb-2">
                <i class="fa-solid fa-book-bookmark text-amber-600"></i> Konstitusi & Tata Tertib Organisasi
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                Manajemen Pedoman Organisasi (GBHO & PPUO)
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Kelola arah kebijakan umum, butir strategi pokok GBHO, 11 bab regulasi PPUO, serta berkas unduhan PDF resmi secara fleksibel tanpa perlu mengubah kode sumber.
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Periode {{ $guideline->period }}
            </span>
        </div>
    </div>

    <!-- Tab Switcher Navigation -->
    <div class="flex items-center overflow-x-auto pb-1 gap-2 border-b border-slate-200">
        <button type="button" 
                @click="activeTab = 'gbho'"
                :class="activeTab === 'gbho' ? 'border-pmr-primary text-pmr-primary bg-red-50/60 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50 font-bold'"
                class="px-5 py-3 rounded-t-2xl border-b-2 text-xs sm:text-sm flex items-center gap-2.5 transition whitespace-nowrap">
            <i class="fa-solid fa-compass text-base text-red-600"></i>
            <span>[ Dokumen GBHO 2026 ]</span>
        </button>
        <button type="button" 
                @click="activeTab = 'ppuo'"
                :class="activeTab === 'ppuo' ? 'border-pmr-primary text-pmr-primary bg-red-50/60 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50 font-bold'"
                class="px-5 py-3 rounded-t-2xl border-b-2 text-xs sm:text-sm flex items-center gap-2.5 transition whitespace-nowrap">
            <i class="fa-solid fa-book-journal-whills text-base text-amber-600"></i>
            <span>[ Dokumen PPUO & 11 Bab Regulasi ]</span>
        </button>
        <button type="button" 
                @click="activeTab = 'files'"
                :class="activeTab === 'files' ? 'border-pmr-primary text-pmr-primary bg-red-50/60 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50 font-bold'"
                class="px-5 py-3 rounded-t-2xl border-b-2 text-xs sm:text-sm flex items-center gap-2.5 transition whitespace-nowrap">
            <i class="fa-solid fa-file-pdf text-base text-red-700"></i>
            <span>[ Berkas PDF & Lembar Pengesahan ]</span>
        </button>
    </div>

    <!-- ============================================== -->
    <!-- TAB 1: DOKUMEN GBHO 2026                      -->
    <!-- ============================================== -->
    <div x-show="activeTab === 'gbho'" class="space-y-8">
        
        <!-- Form Arah Kebijakan Umum -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Arah Kebijakan Umum & Periode Kepengurusan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Teks haluan pokok yang ditampilkan di halaman publik Tentang Kami & Pedoman Organisasi.</p>
                </div>
                <span class="text-xs font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-full border border-red-200/70">Wajib Diisi</span>
            </div>

            <form action="{{ route('admin.organization-guidelines.update-gbho') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Periode Kepengurusan</label>
                        <input type="text" name="period" value="{{ old('period', $guideline->period) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-pmr-primary focus:border-transparent text-sm font-bold text-slate-800"
                               placeholder="Contoh: 2026/2027">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Tanggal Pengesahan</label>
                        <input type="text" name="established_date" value="{{ old('established_date', $guideline->established_date) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-pmr-primary focus:border-transparent text-sm font-semibold text-slate-800"
                               placeholder="Contoh: 05 Januari 2026">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Tempat Pengesahan</label>
                        <input type="text" name="established_place" value="{{ old('established_place', $guideline->established_place) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-pmr-primary focus:border-transparent text-sm font-semibold text-slate-800"
                               placeholder="Contoh: Ciawi, Bogor">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Judul Blok Arah Kebijakan</label>
                    <input type="text" name="gbho_policy_heading" value="{{ old('gbho_policy_heading', $guideline->gbho_policy_heading) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-pmr-primary focus:border-transparent text-sm font-bold text-slate-800"
                           placeholder="Arah Kebijakan Utama">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Teks Narasi Arah Kebijakan (GBHO)</label>
                    <textarea name="gbho_policy_text" rows="4" required
                              class="w-full p-4 rounded-xl border border-slate-300 focus:ring-2 focus:ring-pmr-primary focus:border-transparent text-sm font-medium text-slate-800 leading-relaxed"
                              placeholder="Masukkan narasi arah kebijakan haluan organisasi...">{{ old('gbho_policy_text', $guideline->gbho_policy_text) }}</textarea>
                </div>

                <div class="pt-2 flex items-center justify-end">
                    <button type="submit" class="px-6 py-3 bg-pmr-primary hover:bg-pmr-dark text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan Kebijakan</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Manajemen Strategi Pokok GBHO -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Strategi Pokok Organisasi (GBHO)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar pilar strategi operasional yang dapat ditambah, disunting, atau diatur ulang urutannya.</p>
                </div>
                <button type="button" 
                        @click="strategyMode = 'add'; strategyTitle = ''; strategyDescription = ''; strategyIcon = 'fa-solid fa-check'; strategyOrder = {{ $strategies->count() + 1 }}; strategyModal = true"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Strategi Baru</span>
                </button>
            </div>

            <!-- List Strategi Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($strategies as $st)
                <div class="p-5 rounded-2xl bg-slate-50 hover:bg-red-50/40 border border-slate-200/80 hover:border-red-200 transition-all duration-200 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-2.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center font-black text-xs shrink-0">
                                    {{ $st->order_number }}
                                </span>
                                <span class="text-xs font-black text-slate-900 leading-snug">{{ $st->title }}</span>
                            </div>
                            <div class="w-8 h-8 rounded-lg bg-white text-slate-400 group-hover:text-red-600 flex items-center justify-center text-xs shadow-xs shrink-0">
                                <i class="{{ $st->icon ?: 'fa-solid fa-check' }}"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed pl-9">
                            {{ $st->description }}
                        </p>
                    </div>

                    <div class="pt-4 mt-3 border-t border-slate-200/60 flex items-center justify-end gap-2">
                        <button type="button" 
                                @click="strategyMode = 'edit'; strategyId = {{ $st->id }}; strategyTitle = '{{ addslashes($st->title) }}'; strategyDescription = '{{ addslashes($st->description) }}'; strategyIcon = '{{ $st->icon }}'; strategyOrder = {{ $st->order_number }}; strategyModal = true"
                                class="px-3 py-1.5 rounded-lg bg-white hover:bg-amber-50 text-slate-600 hover:text-amber-700 border border-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                            <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
                        </button>
                        <form action="{{ route('admin.organization-guidelines.destroy-strategy', $st) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus butir strategi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-white hover:bg-red-50 text-slate-600 hover:text-red-700 border border-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                                <i class="fa-solid fa-trash-can text-[11px]"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-2 text-center p-8 bg-slate-50 rounded-2xl border border-dashed border-slate-300 text-slate-500 text-xs">
                    Belum ada butir strategi yang tercatat. Silakan klik tombol "Tambah Strategi Baru".
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- ============================================== -->
    <!-- TAB 2: DOKUMEN PPUO & 11 BAB REGULASI           -->
    <!-- ============================================== -->
    <div x-show="activeTab === 'ppuo'" class="space-y-6">
        
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Daftar Bab Regulasi PPUO (Pedoman Pelaksanaan Umum)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola isi ketentuan baku tiap bab, nomor romawi, dan ringkasan isi aturan.</p>
                </div>
                <button type="button" 
                        @click="chapterMode = 'add'; chapterNumber = 'BAB {{ $chapters->count() + 1 }}'; chapterTitle = ''; chapterSummary = ''; chapterBadge = 'Regulasi Baku'; chapterContent = ''; chapterOrder = {{ $chapters->count() + 1 }}; chapterModal = true"
                        class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Bab Baru</span>
                </button>
            </div>

            <!-- Table of Chapters -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-700 font-extrabold uppercase tracking-wider border-b border-slate-200">
                            <th class="p-3.5 text-center w-16">Urut</th>
                            <th class="p-3.5 w-24">Nomor</th>
                            <th class="p-3.5">Judul & Ringkasan Bab</th>
                            <th class="p-3.5 w-36">Label Badge</th>
                            <th class="p-3.5 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($chapters as $ch)
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="p-3.5 text-center font-bold text-slate-400">
                                {{ $ch->order_number }}
                            </td>
                            <td class="p-3.5 font-black text-red-700">
                                {{ $ch->chapter_number }}
                            </td>
                            <td class="p-3.5">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $ch->title }}</div>
                                <div class="text-slate-500 text-xs mt-0.5 line-clamp-1">{{ $ch->summary ?: 'Tidak ada ringkasan' }}</div>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-bold text-[11px] border border-slate-200">
                                    {{ $ch->badge ?: 'Regulasi' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" 
                                            @click="chapterMode = 'edit'; chapterId = {{ $ch->id }}; chapterNumber = '{{ addslashes($ch->chapter_number) }}'; chapterTitle = '{{ addslashes($ch->title) }}'; chapterSummary = '{{ addslashes($ch->summary ?? '') }}'; chapterBadge = '{{ addslashes($ch->badge ?? '') }}'; chapterContent = '{{ addslashes($ch->content ?? '') }}'; chapterOrder = {{ $ch->order_number }}; chapterModal = true"
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 flex items-center justify-center transition" title="Edit Bab">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <form action="{{ route('admin.organization-guidelines.destroy-chapter', $ch) }}" method="POST" onsubmit="return confirm('Hapus bab {{ $ch->chapter_number }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-red-100 text-slate-600 hover:text-red-800 flex items-center justify-center transition" title="Hapus Bab">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">
                                Belum ada bab regulasi. Silakan tambahkan bab baru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ============================================== -->
    <!-- TAB 3: BERKAS PDF & LEMBAR PENGESAHAN          -->
    <!-- ============================================== -->
    <div x-show="activeTab === 'files'" class="space-y-6">
        
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-sm">
            <div class="mb-6">
                <h3 class="text-lg font-black text-slate-900">Upload Berkas PDF Resmi & Sinkronisasi Publik</h3>
                <p class="text-xs text-slate-500 mt-0.5">Unggah dokumen fisik PDF resmi hasil scan/ekspor stempel pengesahan asli.</p>
            </div>

            <form action="{{ route('admin.organization-guidelines.update-files') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 2 PDF Upload Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- File 1: GBHO PDF -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-pmr-primary font-bold text-xs uppercase tracking-wider">
                                <i class="fa-solid fa-file-pdf"></i> Dokumen GBHO
                            </span>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                Status Aktif
                            </span>
                        </div>
                        <div>
                            <div class="font-extrabold text-sm text-slate-900">Berkas Asli Garis Besar Haluan Organisasi</div>
                            <div class="text-xs text-slate-500 mt-0.5">Tautan saat ini: <a href="{{ $guideline->gbho_pdf_url }}" target="_blank" class="text-pmr-primary underline font-bold">Unduh & Periksa File</a></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Unggah File PDF Baru (Maks 20MB)</label>
                            <input type="file" name="gbho_pdf" accept="application/pdf"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-pmr-primary file:text-white hover:file:bg-pmr-dark file:cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengganti file PDF saat ini.</p>
                        </div>
                    </div>

                    <!-- File 2: PPUO PDF -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-900 font-bold text-xs uppercase tracking-wider">
                                <i class="fa-solid fa-book-bookmark text-amber-700"></i> Dokumen PPUO (27 Hal)
                            </span>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                Status Aktif
                            </span>
                        </div>
                        <div>
                            <div class="font-extrabold text-sm text-slate-900">Berkas Buku Pedoman Pelaksanaan Organisasi</div>
                            <div class="text-xs text-slate-500 mt-0.5">Tautan saat ini: <a href="{{ $guideline->ppuo_pdf_url }}" target="_blank" class="text-amber-700 underline font-bold">Unduh & Periksa File</a></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Unggah File PDF Baru (Maks 30MB)</label>
                            <input type="file" name="ppuo_pdf" accept="application/pdf"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-700 file:cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengganti file PDF saat ini.</p>
                        </div>
                    </div>

                </div>

                <!-- Signers Card -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200/90 space-y-4">
                    <h4 class="font-black text-sm text-slate-900">Pejabat & Legalitas Penandatangan Lembar Pengesahan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Presidium Sidang</label>
                            <input type="text" name="signer_presidium" value="{{ old('signer_presidium', $guideline->signer_presidium) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pembina PMR Wira</label>
                            <input type="text" name="signer_pembina" value="{{ old('signer_pembina', $guideline->signer_pembina) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pihak Sekolah / Kepala Sekolah</label>
                            <input type="text" name="signer_school" value="{{ old('signer_school', $guideline->signer_school) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="px-6 py-3 bg-pmr-primary hover:bg-pmr-dark text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Simpan Berkas & Legalitas</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- ============================================== -->
    <!-- MODAL 1: STRATEGI GBHO (ADD / EDIT)            -->
    <!-- ============================================== -->
    <div x-show="strategyModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-black text-slate-900" x-text="strategyMode === 'add' ? 'Tambah Strategi GBHO Baru' : 'Edit Strategi GBHO'"></h3>
                <button type="button" @click="strategyModal = false" class="text-slate-400 hover:text-slate-700 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="strategyMode === 'add' ? '{{ route('admin.organization-guidelines.store-strategy') }}' : '{{ url('admin/organization-guidelines/strategy') }}/' + strategyId" method="POST" class="space-y-4">
                @csrf
                <template x-if="strategyMode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-1">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Urut</label>
                        <input type="number" name="order_number" x-model="strategyOrder" min="1" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ikon FontAwesome</label>
                        <input type="text" name="icon" x-model="strategyIcon"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs"
                               placeholder="fa-solid fa-check">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Strategi</label>
                    <input type="text" name="title" x-model="strategyTitle" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800"
                           placeholder="Contoh: Kedisiplinan & Sikap Dasar Relawan">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Poin</label>
                    <textarea name="description" x-model="strategyDescription" rows="3" required
                              class="w-full p-3 rounded-xl border border-slate-300 text-xs text-slate-800 leading-relaxed"
                              placeholder="Deskripsi strategi operasional..."></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button" @click="strategyModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-pmr-primary hover:bg-pmr-dark text-white text-xs font-extrabold shadow">
                        Simpan Strategi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 2: BAB PPUO (ADD / EDIT)                 -->
    <!-- ============================================== -->
    <div x-show="chapterModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-black text-slate-900" x-text="chapterMode === 'add' ? 'Tambah Bab PPUO Baru' : 'Edit Bab PPUO'"></h3>
                <button type="button" @click="chapterModal = false" class="text-slate-400 hover:text-slate-700 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="chapterMode === 'add' ? '{{ route('admin.organization-guidelines.store-chapter') }}' : '{{ url('admin/organization-guidelines/chapter') }}/' + chapterId" method="POST" class="space-y-4">
                @csrf
                <template x-if="chapterMode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Urutan</label>
                        <input type="number" name="order_number" x-model="chapterOrder" min="1" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Bab</label>
                        <input type="text" name="chapter_number" x-model="chapterNumber" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-black text-red-700"
                               placeholder="BAB I">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Label Badge</label>
                        <input type="text" name="badge" x-model="chapterBadge"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs"
                               placeholder="Identitas Resmi">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Bab</label>
                    <input type="text" name="title" x-model="chapterTitle" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800"
                           placeholder="Contoh: Nama, Waktu, dan Kedudukan">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ringkasan Bab</label>
                    <input type="text" name="summary" x-model="chapterSummary"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800"
                           placeholder="Penjelasan singkat isi bab...">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pasal & Ketentuan Regulasi (Opsional / Lengkap)</label>
                    <textarea name="content" x-model="chapterContent" rows="5"
                              class="w-full p-3 rounded-xl border border-slate-300 text-xs font-mono text-slate-800 leading-relaxed"
                              placeholder="Masukkan teks pasal atau butir-butir aturan..."></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button" @click="chapterModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold shadow">
                        Simpan Bab
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
