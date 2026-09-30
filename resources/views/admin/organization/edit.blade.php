@extends('layouts.admin')

@section('title', 'Edit Pengurus / Jabatan')
@section('page_title', 'Edit Data Pengurus / Bidang')

@section('top_actions')
<a href="{{ route('admin.organization.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Kembali ke Bagan</span>
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-slate-800 text-sm">Edit Data: {{ $member->position }} &mdash; {{ $member->name }}</h2>
                <p class="text-xs text-slate-500">Kelola ketua bidang, staf anggota, dan program kerja.</p>
            </div>
            <span class="text-xs text-slate-500 font-semibold">* Wajib diisi</span>
        </div>

        <form action="{{ route('admin.organization.update', $member) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
            @csrf
            @method('PUT')
            <input type="hidden" name="member_id" id="member_id" value="{{ old('member_id', $member->member_id) }}">

            <!-- 1. TINGKAT HIRARKI & JABATAN -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-sitemap"></i> 1. Tingkat Hirarki & Nama Jabatan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tingkat Hirarki Level -->
                    <div>
                        <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Tingkat Hirarki Bagan <span class="text-red-500">*</span>
                        </label>
                        <select name="level" id="level" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition bg-white">
                            <option value="1" {{ old('level', $member->level) == 1 ? 'selected' : '' }}>Tingkat 1 &mdash; Pembina PMR</option>
                            <option value="2" {{ old('level', $member->level) == 2 ? 'selected' : '' }}>Tingkat 2 &mdash; Ketua Umum / Pimpinan</option>
                            <option value="3" {{ old('level', $member->level) == 3 ? 'selected' : '' }}>Tingkat 3 &mdash; Pengurus Harian (BPH: Sekretaris / Bendahara)</option>
                            <option value="4" {{ old('level', $member->level) == 4 ? 'selected' : '' }}>Tingkat 4 &mdash; 5 Bidang Utama (Markas, Pelayanan, Diklat, Humas, Kreasi)</option>
                            <option value="5" {{ old('level', $member->level) == 5 ? 'selected' : '' }}>Tingkat 5 &mdash; Anggota / Divisi Tambahan</option>
                        </select>
                    </div>

                    <!-- Nama Jabatan / Posisi -->
                    <div>
                        <label for="position" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Nama Jabatan / Bidang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="position" id="position" value="{{ old('position', $member->position) }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                            placeholder="Contoh: Bidang Markas, Ketua Umum, Sekretaris">
                    </div>
                </div>
            </div>

            <!-- 2. DATA KETUA / PEJABAT -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-user-tie"></i> 2. Data Pejabat / Ketua
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Pejabat / Siswa (Bisa Input Manual atau Cari Database) -->
                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nama Pejabat / Siswa <span class="text-red-500">*</span>
                            </label>
                            <button type="button" onclick="openMemberModal('leader')" class="text-[11px] font-bold text-pmr-primary hover:text-pmr-dark hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-magnifying-glass"></i> Cari Data Anggota
                            </button>
                        </div>

                        <div class="relative">
                            <input type="text" name="name" id="name" value="{{ old('name', $member->name) }}" required autocomplete="off"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-pmr-primary focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition pr-10"
                                placeholder="Ketik nama langsung atau cari..."
                                oninput="handleNameInput(this.value)">
                            
                            <button type="button" onclick="openMemberModal('leader')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-pmr-primary transition" title="Pilih dari Data Anggota">
                                <i class="fa-solid fa-address-book text-base"></i>
                            </button>
                        </div>

                        <!-- Live Autocomplete Dropdown List -->
                        <div id="autocomplete-list" class="absolute z-50 left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-2xl border border-slate-200 divide-y divide-slate-100 max-h-60 overflow-y-auto hidden">
                            <!-- Populated by JS -->
                        </div>

                        @php
                            $linkedMember = $member->member;
                            $hasLinked = !empty($member->member_id) && $linkedMember;
                        @endphp
                        <div id="selected-member-badge" class="{{ $hasLinked ? '' : 'hidden' }} mt-2 p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800 font-semibold">
                            <div class="flex items-center gap-2">
                                <div id="leader-preview-avatar" class="w-7 h-7 rounded-full bg-emerald-200 text-emerald-900 font-bold flex items-center justify-center overflow-hidden flex-shrink-0 text-xs">
                                    @if($hasLinked && $linkedMember->photo)
                                        <img src="{{ $member->photo_url }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>
                                <span id="selected-member-text">
                                    Terhubung ke: <strong id="badge-name">{{ $hasLinked ? $linkedMember->name : '' }}</strong> (<span id="badge-class">{{ $hasLinked ? ($linkedMember->class_grade ?? 'Anggota PMR') : '' }}</span>)
                                </span>
                            </div>
                            <button type="button" onclick="clearSelectedMember()" class="text-slate-400 hover:text-rose-600 text-xs font-bold px-1.5 py-0.5 rounded hover:bg-white transition" title="Hapus tautan">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <p class="text-[11px] text-slate-400 mt-1">Bisa diketik bebas atau dipilih langsung dari database anggota.</p>
                    </div>

                    <!-- Subtitle / Peran / Keterangan -->
                    <div>
                        <label for="subtitle" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Keterangan / Subtitle Peran
                        </label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $member->subtitle) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                            placeholder="Contoh: Ketua Bidang Markas / Kelas XI-MIPA 1">
                        <p class="text-[11px] text-slate-400 mt-1">Muncul tepat di bawah nama pada kartu bagan.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Foto Kustom Pejabat (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Foto Pejabat / Ketua (Opsional)
                        </label>
                        <div class="flex items-center gap-4">
                            <div id="photo-preview-box" class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-xl overflow-hidden flex-shrink-0">
                                @if($member->photo_url)
                                    <img src="{{ $member->photo_url }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-image"></i>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <input type="file" name="photo" id="photo" accept="image/*" onchange="previewUploadedPhoto(event)"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maks: 3MB). Otomatis mengambil dari profil anggota jika dikosongkan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Icon FontAwesome -->
                    <div>
                        <label for="icon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Icon FontAwesome (Opsional)
                        </label>
                        <div class="relative">
                            <input type="text" name="icon" id="icon" value="{{ old('icon', $member->icon ?? 'fa-solid fa-boxes-stacked') }}"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                                placeholder="fa-solid fa-boxes-stacked">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i id="icon-preview" class="{{ old('icon', $member->icon ?? 'fa-solid fa-boxes-stacked') }} text-base"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. DAFTAR STAF BIDANG (Bisa Lebih Dari 2 Orang) -->
            <div id="section-staff" class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2">
                            <i class="fa-solid fa-users-gear"></i> 3. Anggota Staf (Bisa Lebih Dari 2 Orang)
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tambahkan staf pelaksana bidang dengan foto dan nama dari database anggota.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openMemberModal('staff')" class="bg-red-50 hover:bg-red-100 text-pmr-primary px-3 py-1.5 rounded-xl font-bold text-xs flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Pilih dari Anggota</span>
                        </button>
                        <button type="button" onclick="addManualStaffRow()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl font-bold text-xs flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-plus"></i>
                            <span>Input Manual</span>
                        </button>
                    </div>
                </div>

                @php
                    $existingStaff = old('staff_members', $member->staff_members ?? []);
                @endphp

                <!-- Container Baris Staf -->
                <div id="staff-container" class="space-y-3 min-h-[50px] p-4 bg-slate-50/75 rounded-2xl border border-dashed border-slate-300">
                    <p id="staff-empty-state" class="text-center text-xs text-slate-400 py-4 {{ !empty($existingStaff) && count($existingStaff) > 0 ? 'hidden' : '' }}">
                        Belum ada staf ditambahkan. Klik tombol <strong>"Pilih dari Anggota"</strong> atau <strong>"Input Manual"</strong> di atas.
                    </p>

                    @if(!empty($existingStaff) && is_array($existingStaff))
                        @foreach($existingStaff as $idx => $st)
                            @php
                                $stName = $st['name'] ?? '';
                                $stClass = $st['class_grade'] ?? '';
                                $stPhoto = $st['photo'] ?? '';
                                $stMemberId = $st['member_id'] ?? '';
                                $initials = strtoupper(substr($stName ?: 'ST', 0, 2));
                            @endphp
                            <div class="staff-row bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 animate-in fade-in duration-150" id="staff-row-{{ $idx }}">
                                <input type="hidden" name="staff_members[{{ $idx }}][member_id]" value="{{ $stMemberId }}">
                                <input type="hidden" name="staff_members[{{ $idx }}][photo]" value="{{ $stPhoto }}">

                                <div class="flex items-center gap-3 flex-grow w-full sm:w-auto">
                                    @if($stPhoto)
                                        <img src="{{ $stPhoto }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center font-bold text-xs flex-shrink-0">{{ $initials }}</div>
                                    @endif
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 flex-grow">
                                        <div>
                                            <input type="text" name="staff_members[{{ $idx }}][name]" value="{{ $stName }}" required
                                                placeholder="Nama Lengkap Staf"
                                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-800 focus:ring-1 focus:ring-pmr-primary">
                                        </div>
                                        <div>
                                            <input type="text" name="staff_members[{{ $idx }}][class_grade]" value="{{ $stClass }}"
                                                placeholder="Kelas / Jabatan Staf (cth: X-1)"
                                                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-600 focus:ring-1 focus:ring-pmr-primary">
                                        </div>
                                    </div>
                                </div>

                                <button type="button" onclick="removeStaffRow('staff-row-{{ $idx }}')" class="text-slate-400 hover:text-rose-600 p-2 rounded-lg hover:bg-red-50 transition self-end sm:self-center" title="Hapus Staf">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- 4. PROGRAM KERJA BIDANG -->
            <div id="section-work-program" class="space-y-4">
                <div class="pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black uppercase tracking-wider text-pmr-primary flex items-center gap-2">
                        <i class="fa-solid fa-list-check"></i> 4. Program Kerja Bidang (Popup Modal)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Program kerja yang akan tampil di popup modal saat pengunjung mengklik <em>"Klik disini untuk melihat program kerja"</em>.</p>
                </div>

                <div>
                    <label for="work_program" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Daftar Butir Program Kerja (1 Program per Baris atau Deskripsi)
                    </label>
                    <textarea name="work_program" id="work_program" rows="6"
                        class="w-full p-4 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition font-sans"
                        placeholder="Contoh:&#10;1. Pengelolaan dan inventarisasi obat-obatan dan tandu UKS&#10;2. Pemeliharaan kebersihan dan kesiapan ruang markas PMR&#10;3. Piket harian siaga upacara dan operasional markas&#10;4. Pengadaan logistik medis darurat">{{ old('work_program', $member->work_program) }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Gunakan enter untuk baris baru. Setiap baris akan diformat rapi dalam popup modal program kerja.</p>
                </div>
            </div>

            <!-- 5. PENGATURAN TAMBAHAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center pt-4 border-t border-slate-100">
                <!-- Urutan Tampil -->
                <div>
                    <label for="order_position" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Urutan Tampil Kartu (Angka)
                    </label>
                    <input type="number" name="order_position" id="order_position" value="{{ old('order_position', $member->order_position) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        min="0">
                </div>

                <!-- Status Aktif -->
                <div class="pt-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $member->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-pmr-primary focus:ring-pmr-primary border-slate-300">
                        <div>
                            <span class="text-sm font-bold text-slate-800">Tampilkan di Bagan (Aktif)</span>
                            <span class="block text-xs text-slate-500">Centang agar langsung muncul di web publik</span>
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
                    <span>Simpan Perubahan</span>
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
                    <p class="text-xs text-slate-500">Cari berdasarkan Nama, NIS, atau Kelas (Total: {{ $members->count() }} Anggota)</p>
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
            @forelse($members as $m)
                @php
                    $mPhoto = null;
                    if (!empty($m->photo)) {
                        $mPhoto = str_starts_with($m->photo, 'http') || str_starts_with($m->photo, '/') ? $m->photo : asset('storage/' . $m->photo);
                    }
                @endphp
                <div class="member-item p-3.5 rounded-xl border border-slate-200 hover:border-pmr-primary hover:bg-red-50/50 flex items-center justify-between cursor-pointer transition group"
                     onclick="selectMemberFromEl(this)"
                     data-id="{{ $m->id }}"
                     data-name="{{ $m->name }}"
                     data-class="{{ $m->class_grade ?? '' }}"
                     data-nis="{{ $m->nis ?? '' }}"
                     data-photo="{{ $mPhoto ?? '' }}"
                     data-search="{{ strtolower($m->name . ' ' . ($m->class_grade ?? '') . ' ' . ($m->nis ?? '')) }}">
                    <div class="flex items-center gap-3">
                        @if($mPhoto)
                            <img src="{{ $mPhoto }}" alt="{{ $m->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 text-pmr-primary flex items-center justify-center font-black text-sm flex-shrink-0 group-hover:bg-pmr-primary group-hover:text-white transition">
                                {{ strtoupper(substr($m->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <div class="font-bold text-slate-900 text-sm group-hover:text-pmr-primary transition">{{ $m->name }}</div>
                            <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                @if($m->class_grade)
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-semibold">Kelas: {{ $m->class_grade }}</span>
                                @endif
                                @if($m->nis)
                                    <span>NIS: {{ $m->nis }}</span>
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
            <span>Klik pada anggota untuk memilih</span>
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
    let currentModalTarget = 'leader'; // 'leader' or 'staff'
    let staffCount = {{ !empty($existingStaff) && is_array($existingStaff) ? count($existingStaff) : 0 }};

    function handleNameInput(query) {
        const list = document.getElementById('autocomplete-list');
        if (!query || query.trim().length < 1) {
            list.classList.add('hidden');
            list.innerHTML = '';
            return;
        }

        const q = query.toLowerCase().trim();
        const matches = membersData.filter(m => 
            (m.name && m.name.toLowerCase().includes(q)) || 
            (m.class_grade && m.class_grade.toLowerCase().includes(q)) ||
            (m.nis && m.nis.toLowerCase().includes(q))
        ).slice(0, 6);

        if (matches.length === 0) {
            list.classList.add('hidden');
            list.innerHTML = '';
            return;
        }

        let html = '';
        matches.forEach((m) => {
            const shortName = m.name ? m.name.substring(0, 2).toUpperCase() : 'PM';
            const avatarHtml = m.photo_url 
                ? `<img src="${m.photo_url}" class="w-8 h-8 rounded-full object-cover border border-slate-200">`
                : `<div class="w-8 h-8 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center font-bold text-xs">${shortName}</div>`;

            html += `
                <div class="p-3 hover:bg-red-50 cursor-pointer flex items-center justify-between transition"
                     onclick="selectMemberById(${m.id})">
                    <div class="flex items-center gap-2.5">
                        ${avatarHtml}
                        <div>
                            <div class="font-bold text-xs text-slate-800">${m.name}</div>
                            <div class="text-[11px] text-slate-400">${m.class_grade ? 'Kelas: ' + m.class_grade : ''} ${m.nis ? '&bull; NIS: ' + m.nis : ''}</div>
                        </div>
                    </div>
                    <span class="text-[10px] bg-red-100 text-pmr-primary font-bold px-2 py-0.5 rounded">Pilih</span>
                </div>
            `;
        });

        list.innerHTML = html;
        list.classList.remove('hidden');
    }

    function selectMemberFromEl(el) {
        const member = {
            id: el.getAttribute('data-id'),
            name: el.getAttribute('data-name'),
            class_grade: el.getAttribute('data-class'),
            nis: el.getAttribute('data-nis'),
            photo_url: el.getAttribute('data-photo')
        };

        if (currentModalTarget === 'leader') {
            selectLeader(member);
        } else if (currentModalTarget === 'staff') {
            addStaffRow(member);
        }
    }

    function selectMemberById(id) {
        const m = membersData.find(item => item.id == id);
        if (m) {
            if (currentModalTarget === 'leader') {
                selectLeader(m);
            } else if (currentModalTarget === 'staff') {
                addStaffRow(m);
            }
        }
    }

    function selectLeader(m) {
        const nameInput = document.getElementById('name');
        const memberIdInput = document.getElementById('member_id');
        const subtitleInput = document.getElementById('subtitle');
        const badge = document.getElementById('selected-member-badge');
        const badgeName = document.getElementById('badge-name');
        const badgeClass = document.getElementById('badge-class');
        const list = document.getElementById('autocomplete-list');
        const avatarPreview = document.getElementById('leader-preview-avatar');

        if (nameInput && m.name) nameInput.value = m.name;
        if (memberIdInput && m.id) memberIdInput.value = m.id;

        const posInput = document.getElementById('position');
        const currentPos = posInput ? posInput.value.trim() : '';

        if (subtitleInput && (!subtitleInput.value || subtitleInput.value.trim() === '')) {
            if (currentPos) {
                subtitleInput.value = 'Ketua ' + currentPos;
            } else if (m.class_grade) {
                subtitleInput.value = 'Kelas ' + m.class_grade;
            }
        }

        if (badge && badgeName && badgeClass) {
            badgeName.textContent = m.name || '';
            badgeClass.textContent = m.class_grade ? 'Kelas ' + m.class_grade : 'Anggota PMR';
            badge.classList.remove('hidden');

            if (avatarPreview) {
                if (m.photo_url) {
                    avatarPreview.innerHTML = `<img src="${m.photo_url}" class="w-full h-full object-cover">`;
                } else {
                    avatarPreview.innerHTML = `<span class="font-black">${(m.name || 'PM').substring(0,2).toUpperCase()}</span>`;
                }
            }
        }

        if (list) {
            list.classList.add('hidden');
            list.innerHTML = '';
        }

        closeMemberModal();
    }

    function clearSelectedMember() {
        const badge = document.getElementById('selected-member-badge');
        const memberIdInput = document.getElementById('member_id');
        if (badge) badge.classList.add('hidden');
        if (memberIdInput) memberIdInput.value = '';
    }

    function openMemberModal(target = 'leader') {
        currentModalTarget = target;
        const modal = document.getElementById('member-modal');
        const modalTitle = document.getElementById('modal-title');
        const searchInput = document.getElementById('modal-search-input');

        if (modalTitle) {
            modalTitle.textContent = target === 'leader' ? 'Pilih Pejabat / Ketua dari Anggota' : 'Pilih Staf Bidang dari Anggota';
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

    function filterModalMembers(query) {
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('.member-item');
        items.forEach(item => {
            const search = item.getAttribute('data-search') || '';
            if (search.includes(q)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    // --- STAFF REPEATER FUNCTIONS ---
    function addStaffRow(memberData = null) {
        const container = document.getElementById('staff-container');
        const emptyState = document.getElementById('staff-empty-state');
        if (emptyState) emptyState.classList.add('hidden');

        const index = staffCount++;
        const id = memberData ? (memberData.id || '') : '';
        const name = memberData ? (memberData.name || '') : '';
        const classGrade = memberData ? (memberData.class_grade || '') : '';
        const photoUrl = memberData ? (memberData.photo_url || memberData.photo || '') : '';

        const initials = name ? name.substring(0, 2).toUpperCase() : 'ST';
        const avatarHtml = photoUrl 
            ? `<img src="${photoUrl}" class="w-10 h-10 rounded-full object-cover border border-slate-200">`
            : `<div class="w-10 h-10 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center font-bold text-xs flex-shrink-0">${initials}</div>`;

        const row = document.createElement('div');
        row.className = 'staff-row bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 animate-in fade-in duration-150';
        row.id = `staff-row-${index}`;

        row.innerHTML = `
            <input type="hidden" name="staff_members[${index}][member_id]" value="${id}">
            <input type="hidden" name="staff_members[${index}][photo]" value="${photoUrl}">

            <div class="flex items-center gap-3 flex-grow w-full sm:w-auto">
                ${avatarHtml}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 flex-grow">
                    <div>
                        <input type="text" name="staff_members[${index}][name]" value="${name}" required
                            placeholder="Nama Lengkap Staf"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-800 focus:ring-1 focus:ring-pmr-primary">
                    </div>
                    <div>
                        <input type="text" name="staff_members[${index}][class_grade]" value="${classGrade}"
                            placeholder="Kelas / Jabatan Staf (cth: X-1)"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-600 focus:ring-1 focus:ring-pmr-primary">
                    </div>
                </div>
            </div>

            <button type="button" onclick="removeStaffRow('${row.id}')" class="text-slate-400 hover:text-rose-600 p-2 rounded-lg hover:bg-red-50 transition self-end sm:self-center" title="Hapus Staf">
                <i class="fa-solid fa-trash-can text-sm"></i>
            </button>
        `;

        container.appendChild(row);
        closeMemberModal();
    }

    function addManualStaffRow() {
        addStaffRow(null);
    }

    function removeStaffRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
        }
        const container = document.getElementById('staff-container');
        const rows = container.querySelectorAll('.staff-row');
        if (rows.length === 0) {
            const emptyState = document.getElementById('staff-empty-state');
            if (emptyState) emptyState.classList.remove('hidden');
        }
    }

    function previewUploadedPhoto(event) {
        const file = event.target.files[0];
        const previewBox = document.getElementById('photo-preview-box');
        if (file && previewBox) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewBox.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(file);
        }
    }

    const iconInput = document.getElementById('icon');
    if (iconInput) {
        iconInput.addEventListener('input', function(e) {
            const iconPreview = document.getElementById('icon-preview');
            if (iconPreview) {
                iconPreview.className = e.target.value.trim() || 'fa-solid fa-shapes';
            }
        });
    }

    document.addEventListener('click', function(e) {
        const autocomplete = document.getElementById('autocomplete-list');
        const nameInput = document.getElementById('name');
        if (autocomplete && !autocomplete.contains(e.target) && e.target !== nameInput) {
            autocomplete.classList.add('hidden');
        }
    });
</script>
@endpush
