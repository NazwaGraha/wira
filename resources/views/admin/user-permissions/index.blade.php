@extends('layouts.admin')

@section('title', 'Manajemen Hak Akses Pengguna')
@section('page_title', 'Manajemen Hak Akses Pengguna')

@section('content')
<div class="space-y-6">

    <!-- Top Header & Ringkasan -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-lg shadow-xs border border-indigo-100">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Hak Akses Pengguna (Granular Permissions)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Konfigurasikan wewenang akses per menu dan proteksi aksi sensitif untuk setiap akun secara spesifik</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                RBAC System Aktif
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalUsers) }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengguna Terdaftar</div>
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-indigo-700">{{ number_format($totalCustom) }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hak Akses Kustom Spesifik</div>
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-id-badge"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-700">{{ number_format($totalDefault) }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Standar Bawaan Jabatan</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.user-permissions.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            <div class="sm:col-span-5 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama anggota, NRI, atau jabatan..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs md:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
            </div>

            <div class="sm:col-span-3">
                <select name="mode" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs md:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                    <option value="">-- Semua Tipe Hak Akses --</option>
                    <option value="custom" {{ $filterMode === 'custom' ? 'selected' : '' }}>Kustomisasi Spesifik (Granular)</option>
                    <option value="default" {{ $filterMode === 'default' ? 'selected' : '' }}>Standar Bawaan Jabatan</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs md:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                    <option value="">-- Semua Status --</option>
                    <option value="Aktif" {{ $filterStatus === 'Aktif' ? 'selected' : '' }}>Status Aktif</option>
                    <option value="Purna" {{ $filterStatus === 'Purna' ? 'selected' : '' }}>Status Purna</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter text-[11px]"></i> Filter
                </button>
                @if(!empty($search) || !empty($filterMode) || !empty($filterStatus))
                    <a href="{{ route('admin.user-permissions.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition text-xs font-bold" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Table Users & Permissions -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs md:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase text-slate-500 tracking-wider">
                        <th class="py-3.5 px-4">Pengguna / Anggota</th>
                        <th class="py-3.5 px-4">NRI & Status</th>
                        <th class="py-3.5 px-4">Jabatan Struktural</th>
                        <th class="py-3.5 px-4">Tipe Hak Akses</th>
                        <th class="py-3.5 px-4">Wewenang Khusus</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($users as $user)
                        @php
                            $isCustom = $user->hasCustomPermissions();
                            $customCount = $isCustom && is_array($user->custom_permissions) ? count($user->custom_permissions['allowed_menus'] ?? []) : 0;
                            $canDelete = $user->canDeleteData();
                            $canExport = $user->canExportData();
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Col 1: Nama & Foto -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="font-bold text-slate-600 text-xs">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-slate-900 truncate flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->isSuperUser())
                                                <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">ROOT</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400 truncate">{{ $user->email ?? 'Belum ada email' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Col 2: NRI & Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-700">{{ $user->nri ?: '-' }}</div>
                                <div>
                                    @if($user->isAktif())
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Purna
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Col 3: Jabatan -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200/80">
                                    {{ $user->position ?: 'Anggota' }}
                                </span>
                            </td>

                            <!-- Col 4: Tipe Hak Akses -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($user->isSuperUser())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-red-50 text-red-700 border border-red-200">
                                        <i class="fa-solid fa-crown text-[10px]"></i> Super Admin
                                    </span>
                                @elseif($isCustom)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="fa-solid fa-sliders text-[10px]"></i> Kustom ({{ $customCount }} Izin Aktif)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                                        <i class="fa-solid fa-id-badge text-[10px]"></i> Bawaan Jabatan
                                    </span>
                                @endif
                            </td>

                            <!-- Col 5: Wewenang Khusus (Hapus & Export) -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($canDelete)
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200" title="Diizinkan Menghapus Data">
                                            <i class="fa-solid fa-check text-[9px]"></i> Delete
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-600 text-[10px] font-bold border border-rose-200" title="Dilarang Menghapus Data">
                                            <i class="fa-solid fa-ban text-[9px]"></i> No Delete
                                        </span>
                                    @endif

                                    @if($canExport)
                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200" title="Diizinkan Export / Cetak">
                                            <i class="fa-solid fa-file-excel text-[9px]"></i> Export
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Col 6: Aksi -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.user-permissions.edit', $user->id) }}" 
                                       class="px-3 py-1.5 bg-pmr-primary hover:bg-pmr-dark text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                                        <i class="fa-solid fa-sliders text-[11px]"></i>
                                        <span>Atur Hak Akses</span>
                                    </a>

                                    @if($isCustom)
                                        <form method="POST" action="{{ route('admin.user-permissions.reset', $user->id) }}" onsubmit="return confirm('Kembalikan hak akses {{ $user->name }} ke bawaan jabatannya?');" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 rounded-xl text-xs transition" title="Reset ke Bawaan Jabatan">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-user-xmark text-4xl mb-3 opacity-40"></i>
                                <div class="font-bold text-sm text-slate-600">Tidak ada data pengguna yang sesuai</div>
                                <div class="text-xs">Coba sesuaikan kata kunci pencarian atau filter yang dipilih</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
