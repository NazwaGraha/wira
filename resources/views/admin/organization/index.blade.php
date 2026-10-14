@extends('layouts.admin')

@section('title', 'Bagan Kepengurusan')
@section('page_title', 'Kelola Bagan Kepengurusan')

@section('top_actions')
<div class="flex flex-wrap items-center gap-2">
    <form action="{{ route('admin.organization.sync-from-members') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyinkronkan seluruh susunan bagan kepengurusan dan staf secara otomatis berdasarkan data anggota yang aktif dan memiliki jabatan? Data bagan yang ada akan diperbarui sesuai jabatan anggota.')">
        @csrf
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 shadow-sm transition">
            <i class="fa-solid fa-arrows-rotate"></i>
            <span>⚡ Sinkronkan dari Data Anggota</span>
        </button>
    </form>
    <a href="{{ route('admin.organization.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 shadow-md shadow-red-950/20 transition">
        <i class="fa-solid fa-user-plus"></i>
        <span>Tambah Pengurus / Bidang</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-8">

    <!-- 1. Form Pengaturan Judul & Periode Kepengurusan -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h2 class="font-extrabold text-slate-800 text-sm">Pengaturan Judul & Periode Kepengurusan</h2>
                    <p class="text-xs text-slate-500">Ubah judul struktur setiap pergantian kepengurusan (misal: Struktur Organisasi 2026/2027)</p>
                </div>
            </div>
            <a href="{{ route('tentang-kami') }}" target="_blank" class="text-xs font-semibold text-pmr-primary hover:underline flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat di Web Publik
            </a>
        </div>

        <form action="{{ route('admin.organization.setting.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Badge / Label Atas -->
                <div>
                    <label for="badge" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Badge / Label Atas
                    </label>
                    <input type="text" name="badge" id="badge" value="{{ old('badge', $setting->badge) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        placeholder="Bagan Kepengurusan">
                    <span class="text-[11px] text-slate-400 mt-1 block">Teks kecil di atas judul utama</span>
                </div>

                <!-- Judul Struktur -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Judul Struktur Organisasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $setting->title) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition text-slate-900"
                        placeholder="Struktur Organisasi 2026/2027">
                    <span class="text-[11px] text-slate-400 mt-1 block">Dapat diubah tiap tahun periode kepengurusan berganti</span>
                </div>
            </div>

            <!-- Subjudul / Masa Bakti -->
            <div>
                <label for="subtitle" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Subjudul / Keterangan Masa Bakti
                </label>
                <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $setting->subtitle) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                    placeholder="Masa Bakti Ragana Dwi Pantara — Sinergi kepemimpinan dan dedikasi relawan siswa.">
                <span class="text-[11px] text-slate-400 mt-1 block">Penjelasan periode atau nama angkatan kepengurusan</span>
            </div>

            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Pengaturan Judul</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 2. Manajemen Nama-nama Pengurus & Jabatan -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-extrabold text-slate-800 text-sm">Daftar Pejabat & 5 Bidang Bagan Kepengurusan</h2>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fa-solid fa-users-viewfinder text-[9px]"></i> Sesuai Anggota Aktif
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kelola ketua, staf pelaksana, dan program kerja masing-masing bidang berdasarkan status keanggotaan</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <!-- Filter Tabs -->
                <div class="inline-flex items-center bg-slate-200/70 p-1 rounded-xl text-xs font-bold text-slate-600">
                    <a href="{{ route('admin.organization.index', ['status_filter' => 'all']) }}" 
                       class="px-3 py-1.5 rounded-lg transition {{ ($statusFilter ?? 'all') === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.organization.index', ['status_filter' => 'active_only']) }}" 
                       class="px-3 py-1.5 rounded-lg transition {{ ($statusFilter ?? '') === 'active_only' ? 'bg-emerald-600 text-white shadow-xs' : 'hover:text-emerald-700' }}">
                        <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Anggota Aktif
                    </a>
                    <a href="{{ route('admin.organization.index', ['status_filter' => 'purna_only']) }}" 
                       class="px-3 py-1.5 rounded-lg transition {{ ($statusFilter ?? '') === 'purna_only' ? 'bg-amber-600 text-white shadow-xs' : 'hover:text-amber-700' }}">
                        <i class="fa-solid fa-user-clock text-[10px] mr-1"></i> Purna / Nonaktif
                    </a>
                </div>

                <a href="{{ route('admin.organization.create') }}" class="bg-slate-900 hover:bg-slate-800 text-white px-3.5 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition">
                    <i class="fa-solid fa-plus text-red-400"></i>
                    <span>Tambah Pengurus</span>
                </a>
            </div>
        </div>

        @if($allMembers->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
                <h3 class="font-bold text-slate-800 text-base mb-1">Tidak Ada Data Pengurus</h3>
                <p class="text-slate-500 text-xs mb-6">
                    @if(($statusFilter ?? 'all') === 'active_only')
                        Tidak ada pengurus dengan anggota berstatus Aktif saat ini.
                    @elseif(($statusFilter ?? 'all') === 'purna_only')
                        Tidak ada pengurus dengan anggota berstatus Purna/Nonaktif.
                    @else
                        Silakan tambahkan data nama pengurus pertama untuk bagan organisasi.
                    @endif
                </p>
                <a href="{{ route('admin.organization.create') }}" class="bg-pmr-primary text-white text-xs font-bold px-4 py-2.5 rounded-xl inline-flex items-center gap-2 shadow">
                    <i class="fa-solid fa-plus"></i> Tambah Pengurus Baru
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/75 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-600">
                            <th class="py-3 px-4">Tingkat</th>
                            <th class="py-3 px-4">Foto & Pejabat / Ketua</th>
                            <th class="py-3 px-4">Jabatan / Bidang</th>
                            <th class="py-3 px-4">Staf Anggota</th>
                            <th class="py-3 px-4">Program Kerja</th>
                            <th class="py-3 px-4 text-center">Urutan</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($allMembers as $m)
                        @php
                            $photoUrl = $m->photo_url;
                            $initials = strtoupper(substr($m->name, 0, 2));
                            $staffList = is_array($m->staff_members) ? $m->staff_members : [];
                            $staffCount = count($staffList);
                            $hasWorkProgram = !empty(trim($m->work_program ?? ''));
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Tingkat -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($m->level === 1)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-stone-900 text-red-300">
                                        <i class="fa-solid fa-shield-halved text-[10px]"></i> Tingkat 1: Pembina
                                    </span>
                                @elseif($m->level === 2)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-pmr-primary">
                                        <i class="fa-solid fa-crown text-[10px]"></i> Tingkat 2: Ketua
                                    </span>
                                @elseif($m->level === 3)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-user-shield text-[10px]"></i> Tingkat 3: Wakil Ketua
                                    </span>
                                @elseif($m->level === 4)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                        <i class="fa-solid fa-users text-[10px]"></i> Tingkat 4: BPH
                                    </span>
                                @elseif($m->level === 5)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                        <i class="fa-solid fa-shapes text-[10px]"></i> Tingkat 5: Bidang
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                        <i class="fa-solid fa-user text-[10px]"></i> Tingkat 6: Lainnya
                                    </span>
                                @endif
                            </td>

                            <!-- Foto & Nama Pejabat -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="{{ $m->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shadow-sm flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-red-100 text-pmr-primary font-bold text-xs flex items-center justify-center border border-slate-200 flex-shrink-0">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-extrabold text-slate-900 text-sm">{{ $m->name }}</span>
                                            @if($m->level === 1)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                                    Pembina
                                                </span>
                                            @elseif($m->isLeaderActive())
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Anggota Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <i class="fa-solid fa-clock-rotate-left text-[9px]"></i> Purna / Nonaktif
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $m->subtitle ?: '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Jabatan -->
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                <div class="flex items-center gap-2">
                                    @if($m->icon)
                                        <i class="{{ $m->icon }} text-pmr-primary text-xs w-4 text-center"></i>
                                    @endif
                                    <span>{{ $m->position }}</span>
                                </div>
                            </td>

                            <!-- Staf Anggota -->
                            <td class="py-3.5 px-4">
                                @php
                                    $activeStaffList = $m->active_staff_members;
                                    $activeStaffCount = count($activeStaffList);
                                @endphp
                                @if($staffCount > 0)
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $activeStaffCount === $staffCount ? 'bg-red-50 text-pmr-primary' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                            <i class="fa-solid fa-users text-[10px]"></i> {{ $activeStaffCount }} / {{ $staffCount }} Staf Aktif
                                        </span>
                                        @if($activeStaffCount < $staffCount)
                                            <div class="text-[10px] text-amber-600 font-semibold">
                                                {{ $staffCount - $activeStaffCount }} staf purna/nonaktif disembunyikan
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <!-- Program Kerja -->
                            <td class="py-3.5 px-4">
                                @if($hasWorkProgram)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Ada Program Kerja
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum diinput</span>
                                @endif
                            </td>

                            <!-- Urutan -->
                            <td class="py-3.5 px-4 text-center font-bold text-xs text-slate-600">
                                {{ $m->order_position }}
                            </td>

                            <!-- Status Jabatan di Bagan -->
                            <td class="py-3.5 px-4 text-center">
                                @if($m->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Tayang
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.organization.edit', $m) }}" title="Edit Data" class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 font-bold transition text-xs flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </a>

                                    <form action="{{ route('admin.organization.destroy', $m) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data {{ $m->position }} ({{ $m->name }}) dari bagan kepengurusan?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Data" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 font-bold transition text-xs flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span>Hapus</span>
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
@endsection
