@extends('layouts.admin')

@section('title', 'Tambah Pengurus / Jabatan')
@section('page_title', 'Tambah Pengurus / Bidang Bagan')

@section('top_actions')
<a href="{{ route('admin.organization.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Kembali ke Bagan</span>
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Header Card -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-slate-800 text-sm">Formulir Bagan Kepengurusan</h2>
                <p class="text-xs text-slate-500">Nama, foto, dan profil otomatis diambil langsung dari Data Anggota.</p>
            </div>
            <span class="text-xs text-slate-500 font-semibold">* Wajib diisi</span>
        </div>

        <form action="{{ route('admin.organization.store') }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf
            <input type="hidden" name="member_id" id="member_id" value="{{ old('member_id') }}">
            <input type="hidden" name="name" id="name" value="{{ old('name') }}">
            <input type="hidden" name="photo" id="photo" value="{{ old('photo') }}">

            <!-- 1. PILIH ANGGOTA SEBAGAI PEJABAT / KETUA -->
            <div class="space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <label class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2">
                        <i class="fa-solid fa-id-card"></i> 1. Pilih Pejabat / Ketua dari Data Anggota <span class="text-red-500">*</span>
                    </label>
                    <a href="{{ route('admin.members.create') }}" target="_blank" class="text-[11px] font-bold text-slate-500 hover:text-pmr-primary transition flex items-center gap-1">
                        <i class="fa-solid fa-user-plus"></i> + Tambah Anggota Baru
                    </a>
                </div>

                <!-- Live Selection Card -->
                <div id="member-selected-card" class="hidden bg-gradient-to-r from-red-50/80 via-white to-slate-50 p-4 rounded-2xl border-2 border-red-200 flex items-center justify-between gap-4 animate-in fade-in duration-200">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <img id="card-member-photo" src="" alt="" class="w-14 h-14 rounded-full object-cover border-2 border-pmr-primary shadow-sm flex-shrink-0">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 mb-1">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Terpilih dari Data Anggota
                            </div>
                            <div id="card-member-name" class="font-extrabold text-slate-900 text-sm sm:text-base truncate"></div>
                            <div id="card-member-meta" class="text-xs text-slate-500 truncate"></div>
                        </div>
                    </div>

                    <button type="button" onclick="openMemberModal('leader')" class="px-3.5 py-2 rounded-xl bg-white hover:bg-red-50 text-pmr-primary border border-red-200 font-bold text-xs flex items-center gap-1.5 shadow-sm transition flex-shrink-0">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Ganti</span>
                    </button>
                </div>

                <!-- Empty State: Button To Select -->
                <div id="member-empty-card" class="p-6 bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xl mx-auto shadow-inner">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 text-sm">Belum Ada Anggota Dipilih</div>
                        <p class="text-xs text-slate-500 mt-0.5">Pilih nama pejabat/ketua dari database anggota agar foto dan data profil otomatis terisi.</p>
                    </div>
                    <button type="button" onclick="openMemberModal('leader')" class="bg-pmr-primary hover:bg-pmr-dark text-white px-5 py-2.5 rounded-xl font-bold text-xs inline-flex items-center gap-2 shadow-md shadow-red-950/20 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Cari & Pilih Anggota PMR</span>
                    </button>
                </div>
            </div>

            <!-- 2. JABATAN & TINGKAT HIRARKI -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-sitemap"></i> 2. Jabatan & Tingkat Bagan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tingkat Hirarki -->
                    <div>
                        <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Tingkat Hirarki Bagan <span class="text-red-500">*</span>
                        </label>
                        <select name="level" id="level" required onchange="handleLevelChange(this.value)" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition bg-white">
                            <option value="1" {{ old('level') == 1 ? 'selected' : '' }}>Tingkat 1 &mdash; Pembina PMR</option>
                            <option value="2" {{ old('level') == 2 ? 'selected' : '' }}>Tingkat 2 &mdash; Ketua Umum / Pimpinan</option>
                            <option value="3" {{ old('level') == 3 ? 'selected' : '' }}>Tingkat 3 &mdash; Wakil Ketua</option>
                            <option value="4" {{ old('level') == 4 ? 'selected' : '' }}>Tingkat 4 &mdash; Pengurus Harian (BPH: Sekretaris / Bendahara)</option>
                            <option value="5" {{ old('level', 5) == 5 ? 'selected' : '' }}>Tingkat 5 &mdash; 5 Bidang Utama (Markas, Pelayanan, Diklat, Humas, Kreasi)</option>
                            <option value="6" {{ old('level') == 6 ? 'selected' : '' }}>Tingkat 6 &mdash; Anggota / Divisi Tambahan</option>
                        </select>
                    </div>

                    <!-- Nama Jabatan / Posisi -->
                    <div>
                        <label for="position" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nama Jabatan / Bidang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                            placeholder="Contoh: Bidang Markas">
                        
                        <!-- Quick Presets -->
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <button type="button" onclick="setPresetPosition('Ketua Umum 2026/2027', 2, 'fa-solid fa-crown')" class="text-[10px] bg-slate-100 hover:bg-red-50 hover:text-pmr-primary font-bold px-2 py-0.5 rounded-md text-slate-600 transition">Ketua Umum</button>
                            <button type="button" onclick="setPresetPosition('Wakil Ketua', 3, 'fa-solid fa-user-shield')" class="text-[10px] bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 font-bold px-2 py-0.5 rounded-md transition">Wakil Ketua</button>
                            <button type="button" onclick="setPresetPosition('Sekretaris', 4, 'fa-solid fa-file-signature')" class="text-[10px] bg-slate-100 hover:bg-red-50 hover:text-pmr-primary font-bold px-2 py-0.5 rounded-md text-slate-600 transition">Sekretaris</button>
                            <button type="button" onclick="setPresetPosition('Bendahara', 4, 'fa-solid fa-wallet')" class="text-[10px] bg-slate-100 hover:bg-red-50 hover:text-pmr-primary font-bold px-2 py-0.5 rounded-md text-slate-600 transition">Bendahara</button>
                            <button type="button" onclick="setPresetPosition('Bidang Markas', 5, 'fa-solid fa-boxes-stacked')" class="text-[10px] bg-slate-100 hover:bg-red-50 hover:text-pmr-primary font-bold px-2 py-0.5 rounded-md text-slate-600 transition">Bidang Markas</button>
                            <button type="button" onclick="setPresetPosition('Bidang Pelayanan', 5, 'fa-solid fa-hand-holding-heart')" class="text-[10px] bg-slate-100 hover:bg-red-50 hover:text-pmr-primary font-bold px-2 py-0.5 rounded-md text-slate-600 transition">Bidang Pelayanan</button>
                            <button type="button" onclick="setPresetPosition('Bidang Diklat', 5, 'fa-solid fa-graduation-cap')" class="text-[10px] bg-slate-100 hover:bg-red-50 hover:text-pmr-primary font-bold px-2 py-0.5 rounded-md text-slate-600 transition">Bidang Diklat</button>
                            <button type="button" onclick="setPresetPosition('Bidang Humas', 5, 'fa-solid fa-bullhorn')" class="text-[10px] bg-slate-100 hover:bg-red-50 hover:text-pmr-primary font-bold px-2 py-0.5 rounded-md text-slate-600 transition">Bidang Humas</button>
                            <button type="button" onclick="setPresetPosition('Bidang Kreasi', 5, 'fa-solid fa-wand-magic-sparkles')" class="text-[10px] bg-slate-100 hover:bg-red-50 hover:text-pmr-primary font-bold px-2 py-0.5 rounded-md text-slate-600 transition">Bidang Kreasi</button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-1">
                    <!-- Subtitle / Peran -->
                    <div>
                        <label for="subtitle" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Keterangan / Subtitle Peran
                        </label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                            placeholder="Contoh: Ketua Bidang Markas">
                    </div>

                    <!-- Icon FontAwesome -->
                    <div>
                        <label for="icon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Icon FontAwesome
                        </label>
                        <div class="relative">
                            <input type="text" name="icon" id="icon" value="{{ old('icon', 'fa-solid fa-shapes') }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-pmr-primary">
                                <i id="icon-preview" class="fa-solid fa-shapes text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. PILIH STAF BIDANG DARI DATA ANGGOTA -->
            <div id="section-staff" class="space-y-4 pt-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2">
                            <i class="fa-solid fa-users"></i> 3. Anggota Staf (Diambil dari Data Anggota)
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tambahkan staf operasional bidang langsung dari database anggota.</p>
                    </div>

                    <button type="button" onclick="openMemberModal('staff')" class="bg-red-50 hover:bg-pmr-primary hover:text-white text-pmr-primary px-3.5 py-1.5 rounded-xl font-bold text-xs flex items-center gap-1.5 transition self-start sm:self-auto border border-red-200">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>+ Tambah Staf dari Anggota</span>
                    </button>
                </div>

                <!-- Container Baris Staf -->
                <div id="staff-container" class="space-y-2.5 min-h-[50px] p-4 bg-slate-50/75 rounded-2xl border border-dashed border-slate-300">
                    <p id="staff-empty-state" class="text-center text-xs text-slate-400 py-3">
                        Belum ada staf ditambahkan. Klik tombol <strong>"+ Tambah Staf dari Anggota"</strong> di atas.
                    </p>
                </div>
            </div>

            <!-- 4. PROGRAM KERJA BIDANG (FORM SATU-SATUNYA YANG PERLU DIKETIK) -->
            <div id="section-work-program" class="space-y-3 pt-2">
                <div class="pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2">
                        <i class="fa-solid fa-list-check"></i> 4. Program Kerja Bidang (Form Input Program Kerja)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tuliskan butir-butir program kerja bidang ini (1 program kerja per baris). Program kerja akan muncul saat tombol <em>"Program Kerja"</em> diklik di halaman publik.</p>
                </div>

                <div>
                    <label for="work_program" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Daftar Program Kerja
                    </label>
                    <textarea name="work_program" id="work_program" rows="5"
                        class="w-full p-4 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition font-sans leading-relaxed"
                        placeholder="Contoh:&#10;1. Pengelolaan dan inventarisasi obat-obatan serta tandu darurat UKS&#10;2. Pemeliharaan kebersihan dan kesiapan ruang markas PMR&#10;3. Pengadaan logistik medis darurat&#10;4. Jadwal piket harian siaga upacara">{{ old('work_program') }}</textarea>
                </div>
            </div>

            <!-- 5. PENGATURAN TAMBAHAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center pt-4 border-t border-slate-100">
                <div>
                    <label for="order_position" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Urutan Tampil (Angka)
                    </label>
                    <input type="number" name="order_position" id="order_position" value="{{ old('order_position', 1) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        min="0">
                </div>

                <div class="pt-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-pmr-primary focus:ring-pmr-primary border-slate-300">
                        <div>
                            <span class="text-sm font-bold text-slate-800">Tampilkan di Bagan (Aktif)</span>
                            <span class="block text-xs text-slate-500">Centang agar langsung muncul di web</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.organization.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Pengurus / Bidang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pencarian Database Anggota -->
<div id="member-modal" class="fixed inset-0 z-50 bg-stone-900/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[85vh] shadow-2xl flex flex-col overflow-hidden animate-in fade-in duration-200">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-pmr-primary flex items-center justify-center text-lg">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base" id="modal-title">Pilih Dari Database Anggota PMR</h3>
                    <p class="text-xs text-slate-500">Cari berdasarkan Nama, NRI, atau Kelas <span id="modal-count-label" class="font-bold text-pmr-primary">(Tersedia: {{ $members->count() }} Anggota)</span></p>
                </div>
            </div>
            <button type="button" onclick="closeMemberModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Search Bar -->
        <div class="p-4 border-b border-slate-100 bg-white">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="modal-search-input" oninput="filterModalMembers(this.value)" placeholder="Ketik nama anggota atau kelas..." 
                    class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
            </div>
        </div>

        <!-- Modal Member List -->
        <div class="p-4 overflow-y-auto flex-grow space-y-2 max-h-[50vh]" id="modal-member-list">
            <!-- Empty state jika tidak ada hasil pencarian / semua sudah terpilih -->
            <div id="modal-no-results" class="text-center py-10 text-slate-400 hidden">
                <i class="fa-solid fa-user-check text-4xl mb-2 text-emerald-400 block"></i>
                <div class="font-bold text-slate-600">Tidak ada anggota yang dapat dipilih</div>
                <div class="text-xs text-slate-400 mt-1">Anggota lainnya sudah dipilih pada jabatan/staf ini atau tidak cocok dengan pencarian.</div>
            </div>

            @if(!empty($advisors) && $advisors->isNotEmpty())
                <!-- Grup Khusus Pembina PMR (Tingkat 1) -->
                <div id="modal-advisor-group" class="mb-4 bg-gradient-to-r from-red-50 to-rose-50/50 p-3.5 rounded-2xl border border-red-200">
                    <div class="flex items-center justify-between mb-2.5 px-1">
                        <span class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-1.5">
                            <i class="fa-solid fa-graduation-cap"></i> Data Pembina PMR (Guru / Pembina)
                        </span>
                        <a href="{{ route('admin.advisors.create') }}" target="_blank" class="text-[11px] font-bold text-red-600 hover:underline">
                            + Input Pembina Baru
                        </a>
                    </div>
                    <div class="space-y-2">
                        @foreach($advisors as $adv)
                            <div class="advisor-item p-3 rounded-xl border border-red-200 bg-white hover:border-pmr-primary hover:shadow-xs flex items-center justify-between cursor-pointer transition group"
                                 onclick="selectAdvisorFromEl(this)"
                                 data-name="{{ $adv->name }}"
                                 data-title="{{ $adv->title_degree }}"
                                 data-position="{{ $adv->position }}"
                                 data-nip="{{ $adv->nip }}"
                                 data-photo="{{ $adv->photo_url }}"
                                 data-search="{{ strtolower($adv->name . ' ' . $adv->position . ' ' . $adv->nip . ' ' . $adv->title_degree) }}">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $adv->photo_url }}" alt="{{ $adv->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-red-400 flex-shrink-0">
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm group-hover:text-pmr-primary transition">
                                            {{ $adv->name }}{{ $adv->title_degree ? ', ' . $adv->title_degree : '' }}
                                        </div>
                                        <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                            <span class="text-red-700 font-semibold">{{ $adv->position }}</span>
                                            @if($adv->nip)
                                                <span>• NIP. {{ $adv->nip }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-pmr-primary opacity-90 group-hover:opacity-100 transition flex items-center gap-1 bg-red-100/60 group-hover:bg-red-100 px-2.5 py-1 rounded-lg">
                                    Pilih Pembina <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 px-1 pt-1 pb-2 flex items-center gap-1.5" id="member-students-header">
                <i class="fa-solid fa-users"></i> Data Anggota Siswa PMR
            </div>

            @forelse($members as $m)
                @php
                    $mPhoto = $m->photo_url;
                    $mStatus = $m->effective_status;
                    $mNri = $m->nri ?: ($m->nis ?: '-');
                @endphp
                <div class="member-item p-3.5 rounded-xl border border-slate-200 hover:border-pmr-primary hover:bg-red-50/50 flex items-center justify-between cursor-pointer transition group"
                     onclick="selectMemberFromEl(this)"
                     data-id="{{ $m->id }}"
                     data-name="{{ $m->name }}"
                     data-class="{{ $m->class_grade ?? '' }}"
                     data-position="{{ $m->position ?? '' }}"
                     data-nri="{{ $mNri !== '-' ? $mNri : '' }}"
                     data-nis="{{ $mNri !== '-' ? $mNri : '' }}"
                     data-photo="{{ $mPhoto }}"
                     data-status="{{ $mStatus }}"
                     data-search="{{ strtolower($m->name . ' ' . ($m->position ?? '') . ' ' . ($m->class_grade ?? '') . ' ' . $mNri . ' ' . $mStatus) }}">
                    <div class="flex items-center gap-3">
                        <img src="{{ $mPhoto }}" alt="{{ $m->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 flex-shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($m->name) }}&background=dc2626&color=ffffff&bold=true&size=128';">
                        <div>
                            <div class="font-bold text-slate-900 text-sm group-hover:text-pmr-primary transition flex flex-wrap items-center gap-1.5">
                                <span>{{ $m->name }}</span>
                                @if(!empty($m->position))
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-red-100 text-pmr-primary border border-red-200 flex items-center gap-1 shadow-2xs">
                                        <i class="fa-solid fa-award text-[9px]"></i>
                                        <span>{{ $m->position }}</span>
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $mStatus === 'Purna' ? 'bg-slate-100 text-slate-600 border border-slate-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                    {{ $mStatus }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                @if($m->class_grade)
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-semibold">Kelas: {{ $m->class_grade }}</span>
                                @endif
                                @if($mNri !== '-')
                                    <span>NRI: {{ $mNri }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-pmr-primary opacity-0 group-hover:opacity-100 transition flex items-center gap-1">
                        Pilih <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
            @empty
                <div class="text-center py-10 text-slate-400">
                    <i class="fa-solid fa-folder-open text-4xl mb-2 text-slate-300 block"></i>
                    Belum ada data anggota yang terdaftar di database.
                </div>
            @endforelse
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500">
            <span>Klik pada anggota untuk memilih secara instan</span>
            <button type="button" onclick="closeMemberModal()" class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold transition">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const membersData = {!! json_encode($membersJson) !!};
    const globalUsedMemberIds = new Set({!! json_encode($usedMemberIds ?? []) !!}.map(String));
    const globalUsedMemberNames = new Set({!! json_encode($usedMemberNames ?? []) !!}.map(s => String(s).trim().toLowerCase()));
    let currentModalTarget = 'leader';
    let staffCount = 0;

    function openMemberModal(target = 'leader') {
        currentModalTarget = target;
        const modal = document.getElementById('member-modal');
        const modalTitle = document.getElementById('modal-title');
        const searchInput = document.getElementById('modal-search-input');
        const advisorGroup = document.getElementById('modal-advisor-group');
        const studentsHeader = document.getElementById('member-students-header');

        if (modalTitle) {
            modalTitle.textContent = target === 'leader' ? 'Pilih Pejabat / Pembina / Ketua' : 'Pilih Staf Bidang dari Anggota Siswa';
        }

        if (advisorGroup) {
            if (target === 'staff') {
                advisorGroup.classList.add('hidden');
                if (studentsHeader) studentsHeader.classList.add('hidden');
            } else {
                advisorGroup.classList.remove('hidden');
                if (studentsHeader) studentsHeader.classList.remove('hidden');
            }
        }

        if (modal) {
            modal.classList.remove('hidden');
            if (searchInput) {
                searchInput.value = '';
                filterModalMembers('');
                setTimeout(() => searchInput.focus(), 100);
            }
        }
    }

    function closeMemberModal() {
        const modal = document.getElementById('member-modal');
        if (modal) modal.classList.add('hidden');
    }

    function getSelectedMemberIds() {
        const selected = new Set(globalUsedMemberIds);
        
        // ID anggota yang saat ini terpilih sebagai Pejabat / Ketua di form ini
        const leaderId = document.getElementById('member_id')?.value;
        if (leaderId) {
            selected.add(String(leaderId));
        }

        // ID anggota yang saat ini terdaftar di daftar staf bidang di form ini
        document.querySelectorAll('input[name^="staff_members"][name$="[member_id]"]').forEach(input => {
            if (input.value) {
                selected.add(String(input.value));
            }
        });

        return selected;
    }

    function getSelectedMemberNames() {
        const selectedNames = new Set(globalUsedMemberNames);

        // Nama anggota yang saat ini terpilih sebagai Pejabat / Ketua di form ini
        const leaderName = document.getElementById('name')?.value;
        if (leaderName) {
            selectedNames.add(String(leaderName).trim().toLowerCase());
        }

        // Nama staf yang saat ini terdaftar di form ini
        document.querySelectorAll('input[name^="staff_members"][name$="[name]"]').forEach(input => {
            if (input.value) {
                selectedNames.add(String(input.value).trim().toLowerCase());
            }
        });

        return selectedNames;
    }

    function filterModalMembers(query) {
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('.member-item');
        const advisorItems = document.querySelectorAll('.advisor-item');
        const selectedIds = getSelectedMemberIds();
        const selectedNames = getSelectedMemberNames();
        let visibleCount = 0;

        // Filter daftar Pembina (hanya jika target == leader)
        if (currentModalTarget === 'leader') {
            advisorItems.forEach(item => {
                const search = item.getAttribute('data-search') || '';
                if (!q || search.includes(q)) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });
        }

        // Filter daftar Anggota Siswa
        items.forEach(item => {
            const itemId = String(item.getAttribute('data-id') || '');
            const itemName = String(item.getAttribute('data-name') || '').trim().toLowerCase();
            const search = item.getAttribute('data-search') || '';

            // Sembunyikan anggota yang sudah dipilih di struktur organisasi
            const isAlreadySelected = (itemId && selectedIds.has(itemId)) || (itemName && selectedNames.has(itemName));

            if (!isAlreadySelected && (!q || search.includes(q))) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        // Update counter dan empty state jika semua anggota sudah dipilih / tidak cocok pencarian
        const emptyState = document.getElementById('modal-no-results');
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        const countLabel = document.getElementById('modal-count-label');
        if (countLabel) {
            countLabel.textContent = `(Tersedia: ${visibleCount} Pilihan)`;
        }
    }

    function selectAdvisorFromEl(el) {
        const name = el.getAttribute('data-name') || '';
        const title = el.getAttribute('data-title') || '';
        const pos = el.getAttribute('data-position') || 'Pembina PMR';
        const photo = el.getAttribute('data-photo') || '';
        const nip = el.getAttribute('data-nip') || '';

        document.getElementById('member_id').value = '';
        document.getElementById('name').value = name;
        document.getElementById('photo').value = photo;

        const posInput = document.getElementById('position');
        if (posInput && (!posInput.value || posInput.value.trim() === '')) {
            posInput.value = pos;
        }

        const levelSelect = document.getElementById('level');
        if (levelSelect) {
            levelSelect.value = 1;
        }

        const subtitleInput = document.getElementById('subtitle');
        if (subtitleInput && (!subtitleInput.value || subtitleInput.value.trim() === '' || subtitleInput.value.includes('Ketua'))) {
            subtitleInput.value = title ? ('Pembina • ' + title) : pos;
        }

        const card = document.getElementById('member-selected-card');
        const emptyCard = document.getElementById('member-empty-card');
        const cardPhoto = document.getElementById('card-member-photo');
        const cardName = document.getElementById('card-member-name');
        const cardMeta = document.getElementById('card-member-meta');

        if (cardName) cardName.textContent = name + (title ? ', ' + title : '');
        if (cardPhoto) cardPhoto.src = photo || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=991b1b&color=ffffff&bold=true');
        if (cardMeta) cardMeta.textContent = pos + (nip ? ' • NIP. ' + nip : '');
        if (card) card.classList.remove('hidden');
        if (emptyCard) emptyCard.classList.add('hidden');

        closeMemberModal();
    }

    function selectMemberFromEl(el) {
        const member = {
            id: el.getAttribute('data-id'),
            name: el.getAttribute('data-name'),
            class_grade: el.getAttribute('data-class'),
            nis: el.getAttribute('data-nis'),
            photo_url: el.getAttribute('data-photo'),
            position: el.getAttribute('data-position') || ''
        };

        if (currentModalTarget === 'leader') {
            selectLeader(member);
        } else if (currentModalTarget === 'staff') {
            addStaffRow(member);
        }
    }

    function autoFillFromMemberPosition(rawPos, classGrade = '') {
        if (!rawPos || !rawPos.trim()) return;
        const posLower = rawPos.toLowerCase().trim();
        const posInput = document.getElementById('position');
        const levelSelect = document.getElementById('level');
        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('icon-preview');
        const subtitleInput = document.getElementById('subtitle');

        let targetLevel = null;
        let targetPos = rawPos;
        let targetIcon = 'fa-solid fa-shapes';
        let targetSubtitle = rawPos;

        if (posLower.includes('ketua umum') || (posLower.includes('ketua') && !posLower.includes('wakil') && !posLower.includes('bidang'))) {
            targetLevel = 2;
            targetPos = 'Ketua Umum';
            targetIcon = 'fa-solid fa-crown';
            targetSubtitle = 'Masa Bakti Ragana Dwi Pantara';
        } else if (posLower.includes('wakil')) {
            targetLevel = 3;
            targetPos = 'Wakil Ketua';
            targetIcon = 'fa-solid fa-user-shield';
            targetSubtitle = 'Wakil Ketua PMR Wira';
        } else if (posLower.includes('sekretaris')) {
            targetLevel = 4;
            targetPos = 'Sekretaris';
            targetIcon = 'fa-solid fa-file-signature';
            targetSubtitle = 'Sekretaris PMR Wira';
        } else if (posLower.includes('bendahara')) {
            targetLevel = 4;
            targetPos = 'Bendahara';
            targetIcon = 'fa-solid fa-wallet';
            targetSubtitle = 'Bendahara PMR Wira';
        } else if (posLower.includes('markas') || posLower.includes('logistik')) {
            targetLevel = 5;
            targetPos = 'Ketua Bidang Markas & Logistik';
            targetIcon = 'fa-solid fa-boxes-stacked';
            targetSubtitle = 'Bidang Markas PMR';
        } else if (posLower.includes('pelayanan') || posLower.includes('kesehatan') || posLower.includes('pp')) {
            targetLevel = 5;
            targetPos = 'Ketua Bidang Pelayanan Kesehatan & PP';
            targetIcon = 'fa-solid fa-heart-pulse';
            targetSubtitle = 'Bidang Pelayanan PMR';
        } else if (posLower.includes('diklat') || posLower.includes('pendidikan') || posLower.includes('pelatihan')) {
            targetLevel = 5;
            targetPos = 'Ketua Bidang Pendidikan & Pelatihan (Diklat)';
            targetIcon = 'fa-solid fa-graduation-cap';
            targetSubtitle = 'Bidang Diklat PMR';
        } else if (posLower.includes('humas') || posLower.includes('hubungan')) {
            targetLevel = 5;
            targetPos = 'Ketua Bidang Hubungan Masyarakat (Humas)';
            targetIcon = 'fa-solid fa-bullhorn';
            targetSubtitle = 'Bidang Humas PMR';
        } else if (posLower.includes('kreasi') || posLower.includes('bakat') || posLower.includes('minat')) {
            targetLevel = 5;
            targetPos = 'Ketua Bidang Kreasi, Bakat & Minat';
            targetIcon = 'fa-solid fa-palette';
            targetSubtitle = 'Bidang Kreasi & Minat PMR';
        }

        if (posInput) posInput.value = targetPos;
        if (targetLevel && levelSelect) {
            levelSelect.value = targetLevel;
            if (typeof toggleLevelSections === 'function') toggleLevelSections();
        }
        if (iconInput) {
            iconInput.value = targetIcon;
            if (iconPreview) iconPreview.className = targetIcon + ' text-sm';
        }
        if (subtitleInput) {
            subtitleInput.value = targetSubtitle;
        }
    }

    function selectLeader(m) {
        document.getElementById('member_id').value = m.id || '';
        document.getElementById('name').value = m.name || '';
        document.getElementById('photo').value = m.photo_url || '';

        const card = document.getElementById('member-selected-card');
        const emptyCard = document.getElementById('member-empty-card');
        const cardPhoto = document.getElementById('card-member-photo');
        const cardName = document.getElementById('card-member-name');
        const cardMeta = document.getElementById('card-member-meta');
        const subtitleInput = document.getElementById('subtitle');
        const posInput = document.getElementById('position');

        if (cardName) cardName.textContent = m.name;
        if (cardPhoto) cardPhoto.src = m.photo_url || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(m.name) + '&background=dc2626&color=ffffff&bold=true');
        
        const metaParts = [];
        if (m.position) metaParts.push(m.position);
        if (m.class_grade) metaParts.push('Kelas ' + m.class_grade);
        if (m.nri || m.nis) metaParts.push('NRI: ' + (m.nri || m.nis));
        if (cardMeta) cardMeta.textContent = metaParts.length > 0 ? metaParts.join(' • ') : 'Anggota PMR';

        if (m.position) {
            autoFillFromMemberPosition(m.position, m.class_grade);
        } else {
            if (subtitleInput && (!subtitleInput.value || subtitleInput.value.trim() === '')) {
                const pos = posInput ? posInput.value.trim() : '';
                if (pos) {
                    subtitleInput.value = 'Ketua ' + pos;
                } else if (m.class_grade) {
                    subtitleInput.value = 'Kelas ' + m.class_grade;
                }
            }
        }

        if (card) card.classList.remove('hidden');
        if (emptyCard) emptyCard.classList.add('hidden');

        closeMemberModal();
    }

    function setPresetPosition(pos, level, icon) {
        const posInput = document.getElementById('position');
        const levelSelect = document.getElementById('level');
        const iconInput = document.getElementById('icon');
        const subtitleInput = document.getElementById('subtitle');
        const iconPreview = document.getElementById('icon-preview');

        if (posInput) posInput.value = pos;
        if (levelSelect) levelSelect.value = level;
        if (iconInput) iconInput.value = icon;
        if (iconPreview) iconPreview.className = icon + ' text-sm';

        if (subtitleInput && (!subtitleInput.value || subtitleInput.value.trim() === '' || subtitleInput.value.startsWith('Ketua ') || subtitleInput.value.includes('PMR Wira'))) {
            if (pos === 'Wakil Ketua') {
                subtitleInput.value = 'Wakil Ketua PMR Wira';
            } else if (pos.startsWith('Ketua Umum')) {
                subtitleInput.value = 'Masa Bakti Ragana Dwi Pantara';
            } else if (pos === 'Sekretaris' || pos === 'Bendahara') {
                subtitleInput.value = pos + ' PMR Wira';
            } else {
                subtitleInput.value = 'Ketua ' + pos;
            }
        }
    }

    // --- STAFF REPEATER ---
    function addStaffRow(m) {
        const container = document.getElementById('staff-container');
        const emptyState = document.getElementById('staff-empty-state');
        if (emptyState) emptyState.classList.add('hidden');

        const index = staffCount++;
        const id = m ? (m.id || '') : '';
        const name = m ? (m.name || '') : '';
        const classGrade = m ? (m.class_grade || '') : '';
        const position = m ? (m.position || '') : '';
        const photoUrl = m ? (m.photo_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=dc2626&color=ffffff&bold=true') : '';

        const row = document.createElement('div');
        row.className = 'staff-row bg-white p-3 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between gap-3 animate-in fade-in duration-150';
        row.id = `staff-row-${index}`;

        row.innerHTML = `
            <input type="hidden" name="staff_members[${index}][member_id]" value="${id}">
            <input type="hidden" name="staff_members[${index}][name]" value="${name}">
            <input type="hidden" name="staff_members[${index}][class_grade]" value="${classGrade}">
            <input type="hidden" name="staff_members[${index}][photo]" value="${photoUrl}">
            <input type="hidden" name="staff_members[${index}][position]" value="${position}">

            <div class="flex items-center gap-3 min-w-0">
                <img src="${photoUrl}" class="w-10 h-10 rounded-full object-cover border border-slate-200 flex-shrink-0">
                <div class="min-w-0">
                    <div class="font-bold text-xs text-slate-800 truncate flex items-center gap-1.5">
                        <span>${name}</span>
                        ${position ? `<span class="bg-red-50 text-pmr-primary border border-red-200 text-[10px] font-bold px-1.5 py-0.2 rounded">${position}</span>` : ''}
                    </div>
                    <div class="text-[11px] text-slate-400 truncate">${classGrade ? 'Kelas: ' + classGrade : 'Anggota PMR'}</div>
                </div>
            </div>

            <button type="button" onclick="removeStaffRow('${row.id}')" class="text-slate-400 hover:text-rose-600 p-2 rounded-lg hover:bg-red-50 transition" title="Hapus Staf">
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
        `;

        container.appendChild(row);
        closeMemberModal();
    }

    function removeStaffRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) row.remove();
        const container = document.getElementById('staff-container');
        const rows = container.querySelectorAll('.staff-row');
        if (rows.length === 0) {
            const emptyState = document.getElementById('staff-empty-state');
            if (emptyState) emptyState.classList.remove('hidden');
        }
    }

    const iconInput = document.getElementById('icon');
    if (iconInput) {
        iconInput.addEventListener('input', function(e) {
            const iconPreview = document.getElementById('icon-preview');
            if (iconPreview) {
                iconPreview.className = (e.target.value.trim() || 'fa-solid fa-shapes') + ' text-sm';
            }
        });
    }
</script>
@endpush
