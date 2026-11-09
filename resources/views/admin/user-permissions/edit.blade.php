@extends('layouts.admin')

@section('title', 'Atur Hak Akses: ' . $user->name)
@section('page_title', 'Pengaturan Hak Akses Pengguna')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="permissionsEditor()">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.user-permissions.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Hak Akses
        </a>
        <div class="text-xs text-slate-400">
            ID Anggota: <span class="font-mono font-bold text-slate-600">#{{ $user->id }}</span>
        </div>
    </div>

    <!-- Header Profil Pengguna & Mode Switch -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-5 sm:p-7 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <!-- User Info -->
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 border-2 border-slate-200 flex items-center justify-center overflow-hidden shrink-0 shadow-xs">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="font-extrabold text-slate-600 text-xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h2>
                        @if($user->isSuperUser())
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">SUPER ADMIN</span>
                        @endif
                    </div>
                    <div class="text-xs sm:text-sm text-slate-500 mt-0.5 flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-slate-700">NRI:</span> {{ $user->nri ?: '-' }}
                        <span>&bull;</span>
                        <span class="font-bold text-slate-700">Jabatan Pokok:</span> {{ $user->position ?: 'Anggota' }}
                        <span>&bull;</span>
                        <span class="px-2 py-0.2 rounded-md bg-slate-100 text-slate-700 font-bold text-xs">{{ $user->role_label }}</span>
                    </div>
                </div>
            </div>

            <!-- Mode Selector Switch -->
            <div class="bg-slate-100 p-1.5 rounded-2xl flex items-center gap-1 shrink-0 border border-slate-200/80">
                <button type="button" 
                        @click="setMode('default')" 
                        :class="mode === 'default' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-id-badge text-xs"></i>
                    <span>Ikuti Bawaan Jabatan</span>
                </button>
                <button type="button" 
                        @click="setMode('custom')" 
                        :class="mode === 'custom' ? 'bg-indigo-600 text-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                        class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-xs"></i>
                    <span>Kustomisasi Spesifik</span>
                </button>
            </div>
        </div>

        <!-- Mode Explanation Notice -->
        <div class="mt-5 pt-4 border-t border-slate-100 text-xs">
            <template x-if="mode === 'default'">
                <div class="flex items-center gap-2.5 text-slate-600 bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-3">
                    <i class="fa-solid fa-circle-info text-blue-500 text-base shrink-0"></i>
                    <div>
                        <strong>Mode Otomatis Aktif:</strong> Hak akses pengguna mengikuti standar bawaan jabatannya (<span class="font-bold text-slate-800">{{ $user->role_label }}</span>). Jika jabatan diubah, wewenang akses akan otomatis menyesuaikan.
                    </div>
                </div>
            </template>
            <template x-if="mode === 'custom'">
                <div class="flex items-center gap-2.5 text-indigo-900 bg-indigo-50 border border-indigo-200/60 rounded-xl px-4 py-3">
                    <i class="fa-solid fa-sliders text-indigo-600 text-base shrink-0"></i>
                    <div>
                        <strong>Mode Kustomisasi Spesifik Aktif:</strong> Anda dapat mencentang atau menghapus centang menu-menu di bawah ini secara mandiri tanpa terikat batasan jabatan struktural.
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Form Pengaturan Hak Akses -->
    <form method="POST" action="{{ route('admin.user-permissions.update', $user->id) }}" id="permissions-form">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="mode" :value="mode">

        <!-- Checklist Matriks Hak Akses Granular per Menu (Selalu Tampil) -->
        <div class="space-y-6">

            <!-- Quick Template Presets Bar -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                        <span>Template Preset Cepat (1-Klik)</span>
                    </div>
                    <div class="text-xs text-slate-500">
                        <span class="font-black text-indigo-600" x-text="selectedCount">0</span> Wewenang Menu Aktif
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    @foreach($presets as $key => $preset)
                        <button type="button" 
                                @click="applyPreset('{{ $key }}')" 
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 border bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 active:scale-95 shadow-2xs">
                            <i class="{{ $preset['icon'] }} text-xs"></i>
                            <span>{{ $preset['name'] }}</span>
                        </button>
                    @endforeach
                    <button type="button" 
                            @click="selectAll()" 
                            class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200 active:scale-95">
                        <i class="fa-solid fa-check-double text-xs"></i>
                        <span>Pilih Semua</span>
                    </button>
                    <button type="button" 
                            @click="unselectAll()" 
                            class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200 active:scale-95">
                        <i class="fa-solid fa-xmark text-xs"></i>
                        <span>Kosongkan Semua</span>
                    </button>
                </div>
            </div>

            <!-- Sensitive Action Toggles (Hapus & Export) -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-950 text-white rounded-3xl p-5 sm:p-6 shadow-lg border border-slate-800">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-rose-400"></i>
                    <span>Wewenang Aksi Sensitif & Proteksi Data</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Toggle Export / Cetak -->
                    <label class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 flex items-start justify-between gap-3 cursor-pointer hover:bg-slate-800 transition">
                        <div>
                            <div class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                                <span>Izin Export Excel & Cetak Laporan</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Mengizinkan pengguna mengunduh rekap spreadsheet anggota, peserta lomba, atau log parkir.</p>
                        </div>
                        <input type="checkbox" 
                               name="can_export" 
                               value="1" 
                               x-model="canExport"
                               class="w-5 h-5 rounded-md text-emerald-600 border-slate-600 focus:ring-emerald-500 accent-emerald-500 mt-1 shrink-0">
                    </label>

                    <!-- Toggle Hapus Data (DELETE) -->
                    <label class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 flex items-start justify-between gap-3 cursor-pointer hover:bg-slate-800 transition"
                           :class="canDelete ? 'border-rose-500/50 bg-rose-950/20' : ''">
                        <div>
                            <div class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-trash-can text-rose-400"></i>
                                <span>Izin Hapus Data Permanen (DELETE)</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">
                                <span class="text-rose-400 font-semibold">Perhatian:</span> Izin ini memungkinkan penghapusan artikel, data anggota, juri, atau jadwal.
                            </p>
                        </div>
                        <input type="checkbox" 
                               name="can_delete" 
                               value="1" 
                               x-model="canDelete"
                               class="w-5 h-5 rounded-md text-rose-600 border-slate-600 focus:ring-rose-500 accent-rose-500 mt-1 shrink-0">
                    </label>
                </div>
            </div>

            <!-- Modules Checklist Matrix (2 Columns Grid) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach($modules as $moduleKey => $module)
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden flex flex-col">
                        <!-- Module Header -->
                        <div class="px-4 py-3 bg-slate-50/90 border-b border-slate-200/80 flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg {{ $module['color'] }} flex items-center justify-center text-xs shrink-0">
                                    <i class="{{ $module['icon'] }}"></i>
                                </div>
                                <span class="text-xs font-black uppercase tracking-wider text-slate-800 truncate">{{ $module['name'] }}</span>
                            </div>

                            <!-- Select All within this module -->
                            <button type="button" 
                                    @click="toggleModuleGroup('{{ $moduleKey }}')"
                                    class="text-[11px] font-bold text-pmr-primary hover:text-pmr-dark hover:underline transition shrink-0 ml-2">
                                Toggle Grup
                            </button>
                        </div>

                        <!-- Module Items Checklist -->
                        <div class="p-3.5 space-y-2 flex-grow">
                            @foreach($module['items'] as $code => $item)
                                @php
                                    $badgeClass = match($item['type']) {
                                        'view' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'create' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'edit', 'manage' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'export' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'delete' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'root' => 'bg-red-50 text-red-700 border-red-200 font-black',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <label class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200/60 transition cursor-pointer group">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <input type="checkbox" 
                                               name="allowed_menus[]" 
                                               value="{{ $code }}" 
                                               data-module="{{ $moduleKey }}"
                                               x-model="checkedPermissions"
                                               class="w-4 h-4 rounded text-pmr-primary border-slate-300 focus:ring-pmr-primary accent-pmr-primary shrink-0">
                                        <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900 truncate">
                                            {{ $item['label'] }}
                                        </span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border uppercase tracking-wider shrink-0 ml-2 {{ $badgeClass }}">
                                        {{ $item['badge'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        <!-- Sticky Action Footer Bar -->
        <div class="sticky bottom-4 z-20 mt-8 bg-white/95 backdrop-blur-md border border-slate-200 rounded-2xl p-4 shadow-xl flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.user-permissions.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition">
                    Batal
                </a>
                <template x-if="mode === 'custom'">
                    <span class="text-xs font-semibold text-slate-500 hidden sm:inline">
                        Total <span class="font-bold text-pmr-primary" x-text="selectedCount"></span> wewenang dipilih
                    </span>
                </template>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-pmr-primary hover:bg-pmr-dark text-white font-black text-xs transition shadow-md shadow-red-950/20 active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Perubahan Hak Akses</span>
                </button>
            </div>
        </div>

    </form>

</div>

<script>
    const PRESETS = @json($presets);
    const INITIAL_PERMISSIONS = @json($activePermissions);
    const INITIAL_MODE = '{{ $isCustom ? "custom" : "default" }}';
    const INITIAL_CAN_DELETE = {{ $canDelete ? 'true' : 'false' }};
    const INITIAL_CAN_EXPORT = {{ $canExport ? 'true' : 'false' }};

    function permissionsEditor() {
        return {
            mode: INITIAL_MODE,
            checkedPermissions: [...INITIAL_PERMISSIONS],
            canDelete: INITIAL_CAN_DELETE,
            canExport: INITIAL_CAN_EXPORT,

            get selectedCount() {
                return this.checkedPermissions.length;
            },

            setMode(newMode) {
                this.mode = newMode;
            },

            applyPreset(presetKey) {
                this.mode = 'custom';
                if (PRESETS[presetKey]) {
                    const preset = PRESETS[presetKey];
                    this.checkedPermissions = [...preset.permissions];
                    this.canDelete = !!preset.can_delete;
                    this.canExport = !!preset.can_export;
                }
            },

            selectAll() {
                this.mode = 'custom';
                const checkboxes = document.querySelectorAll('input[name="allowed_menus[]"]');
                const allCodes = [];
                checkboxes.forEach(cb => allCodes.push(cb.value));
                this.checkedPermissions = allCodes;
            },

            unselectAll() {
                this.mode = 'custom';
                this.checkedPermissions = [];
            },

            toggleModuleGroup(moduleKey) {
                this.mode = 'custom';
                const checkboxes = document.querySelectorAll(`input[data-module="${moduleKey}"]`);
                const groupCodes = Array.from(checkboxes).map(cb => cb.value);
                const allSelected = groupCodes.every(code => this.checkedPermissions.includes(code));

                if (allSelected) {
                    // Uncheck all in group
                    this.checkedPermissions = this.checkedPermissions.filter(code => !groupCodes.includes(code));
                } else {
                    // Check all in group
                    const merged = new Set([...this.checkedPermissions, ...groupCodes]);
                    this.checkedPermissions = Array.from(merged);
                }
            }
        };
    }
</script>
@endsection
