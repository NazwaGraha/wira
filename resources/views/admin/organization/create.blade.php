@extends('layouts.admin')

@section('title', 'Tambah Pengurus / Jabatan')
@section('page_title', 'Tambah Pengurus / Jabatan Baru')

@section('top_actions')
<a href="{{ route('admin.organization.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Kembali ke Bagan</span>
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-extrabold text-slate-800 text-sm">Formulir Pengurus / Jabatan Baru</h2>
            <span class="text-xs text-slate-500">* Wajib diisi</span>
        </div>

        <form action="{{ route('admin.organization.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Tingkat Hirarki Level -->
            <div>
                <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Tingkat Hirarki Bagan <span class="text-red-500">*</span>
                </label>
                <select name="level" id="level" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition bg-white">
                    <option value="1" {{ old('level') == 1 ? 'selected' : '' }}>Tingkat 1 &mdash; Pembina PMR</option>
                    <option value="2" {{ old('level') == 2 ? 'selected' : '' }}>Tingkat 2 &mdash; Ketua Umum / Pimpinan</option>
                    <option value="3" {{ old('level') == 3 ? 'selected' : '' }}>Tingkat 3 &mdash; Pengurus Harian (BPH: Sekretaris / Bendahara)</option>
                    <option value="4" {{ old('level', 4) == 4 ? 'selected' : '' }}>Tingkat 4 &mdash; Koordinator Seksi / Divisi (Sie Kesehatan, Diklat, Humas, dll)</option>
                    <option value="5" {{ old('level') == 5 ? 'selected' : '' }}>Tingkat 5 &mdash; Anggota / Seksi Tambahan</option>
                </select>
                <p class="text-[11px] text-slate-500 mt-1">Menentukan posisi kartu dalam visual bagan organisasi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jabatan -->
                <div>
                    <label for="position" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Jabatan / Posisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="position" id="position" value="{{ old('position') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        placeholder="Contoh: Ketua Umum 2026/2027">
                </div>

                <!-- Nama Lengkap Pejabat / Siswa (Bisa Input Manual atau Cari Database) -->
                <div class="relative">
                    <div class="flex items-center justify-between mb-2">
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Nama Pejabat / Siswa <span class="text-red-500">*</span>
                        </label>
                        <button type="button" onclick="openMemberModal()" class="text-[11px] font-bold text-pmr-primary hover:text-pmr-dark hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-magnifying-glass"></i> Cari Data Anggota
                        </button>
                    </div>

                    <div class="relative">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="off"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-pmr-primary focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition pr-10"
                            placeholder="Ketik nama langsung atau cari..."
                            oninput="handleNameInput(this.value)">
                        
                        <button type="button" onclick="openMemberModal()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-pmr-primary transition" title="Pilih dari Data Anggota">
                            <i class="fa-solid fa-address-book text-base"></i>
                        </button>
                    </div>

                    <!-- Live Autocomplete Dropdown List -->
                    <div id="autocomplete-list" class="absolute z-50 left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-2xl border border-slate-200 divide-y divide-slate-100 max-h-60 overflow-y-auto hidden">
                        <!-- Populated by JS -->
                    </div>

                    <div id="selected-member-badge" class="hidden mt-2 p-2 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-between text-xs text-emerald-800 font-semibold">
                        <span id="selected-member-text" class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            Terhubung ke Anggota: <strong id="badge-name"></strong> (<span id="badge-class"></span>)
                        </span>
                        <button type="button" onclick="clearSelectedMember()" class="text-slate-400 hover:text-rose-600 text-xs font-bold px-1.5 py-0.5 rounded hover:bg-white transition" title="Hapus tautan">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-400 mt-1">Bisa ketik manual (cth: Guru/Pembina) atau pilih dari database anggota PMR.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subtitle / Keterangan Kelas -->
                <div>
                    <label for="subtitle" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Keterangan / Kelas / Peran
                    </label>
                    <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        placeholder="Contoh: Kelas XI-MIPA 1 / Piket UKS">
                </div>

                <!-- Icon FontAwesome -->
                <div>
                    <label for="icon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Icon FontAwesome (Opsional)
                    </label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', 'fa-solid fa-notes-medical') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        placeholder="Contoh: fa-solid fa-notes-medical">
                    <p class="text-[11px] text-slate-400 mt-1">Bisa gunakan icon seperti fa-solid fa-notes-medical, fa-solid fa-bullhorn, dll.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <!-- Urutan Tampil -->
                <div>
                    <label for="order_position" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Urutan Tampil (Angka)
                    </label>
                    <input type="number" name="order_position" id="order_position" value="{{ old('order_position', 1) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        min="0">
                </div>

                <!-- Status Aktif -->
                <div class="pt-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
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
                    <span>Simpan Pengurus</span>
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
                    <h3 class="font-extrabold text-slate-900 text-base">Pilih Dari Database Anggota PMR</h3>
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
                <div class="member-item p-3.5 rounded-xl border border-slate-200 hover:border-pmr-primary hover:bg-red-50/50 flex items-center justify-between cursor-pointer transition group"
                     onclick="selectMember({ id: {{ $m->id }}, name: '{{ addslashes($m->name) }}', class_grade: '{{ addslashes($m->class_grade ?? '') }}', nis: '{{ addslashes($m->nis ?? '') }}' })"
                     data-name="{{ strtolower($m->name) }}"
                     data-class="{{ strtolower($m->class_grade ?? '') }}"
                     data-nis="{{ strtolower($m->nis ?? '') }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 text-pmr-primary flex items-center justify-center font-black text-sm flex-shrink-0 group-hover:bg-pmr-primary group-hover:text-white transition">
                            {{ strtoupper(substr($m->name, 0, 2)) }}
                        </div>
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
            <span>Klik pada anggota untuk otomatis mengisi nama & kelas</span>
            <button type="button" onclick="closeMemberModal()" class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold transition">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const membersData = @json($members->map(function($m) {
        return [
            'id' => $m->id,
            'name' => $m->name,
            'class_grade' => $m->class_grade ?? '',
            'nis' => $m->nis ?? '',
        ];
    }));

    function handleNameInput(query) {
        const list = document.getElementById('autocomplete-list');
        if (!query || query.trim().length < 1) {
            list.classList.add('hidden');
            list.innerHTML = '';
            return;
        }

        const q = query.toLowerCase().trim();
        const matches = membersData.filter(m => 
            m.name.toLowerCase().includes(q) || 
            m.class_grade.toLowerCase().includes(q) ||
            m.nis.toLowerCase().includes(q)
        ).slice(0, 6);

        if (matches.length === 0) {
            list.classList.add('hidden');
            list.innerHTML = '';
            return;
        }

        let html = '';
        matches.forEach(m => {
            html += `
                <div class="p-3 hover:bg-red-50 cursor-pointer flex items-center justify-between transition"
                     onclick="selectMember(${JSON.stringify(m).replace(/"/g, '&quot;')})">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center font-bold text-xs">
                            ${m.name.substring(0,2).toUpperCase()}
                        </div>
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

    function selectMember(m) {
        const nameInput = document.getElementById('name');
        const subtitleInput = document.getElementById('subtitle');
        const badge = document.getElementById('selected-member-badge');
        const badgeName = document.getElementById('badge-name');
        const badgeClass = document.getElementById('badge-class');
        const list = document.getElementById('autocomplete-list');

        if (nameInput) nameInput.value = m.name;
        if (subtitleInput && (!subtitleInput.value || subtitleInput.value.trim() === '')) {
            subtitleInput.value = m.class_grade ? 'Kelas ' + m.class_grade : '';
        }

        if (badge && badgeName && badgeClass) {
            badgeName.textContent = m.name;
            badgeClass.textContent = m.class_grade ? 'Kelas ' + m.class_grade : 'Anggota PMR';
            badge.classList.remove('hidden');
        }

        if (list) {
            list.classList.add('hidden');
            list.innerHTML = '';
        }

        closeMemberModal();
    }

    function clearSelectedMember() {
        const badge = document.getElementById('selected-member-badge');
        if (badge) badge.classList.add('hidden');
    }

    function openMemberModal() {
        const modal = document.getElementById('member-modal');
        const searchInput = document.getElementById('modal-search-input');
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
            const name = item.getAttribute('data-name') || '';
            const cls = item.getAttribute('data-class') || '';
            const nis = item.getAttribute('data-nis') || '';

            if (name.includes(q) || cls.includes(q) || nis.includes(q)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
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
