@extends('layouts.admin')

@section('title', 'Manajemen Prestasi & Rekam Jejak Juara')
@section('page_title', 'Prestasi & Penghargaan')

@section('top_actions')
<div class="flex items-center gap-2">
    <a href="{{ route('prestasi.index') }}" target="_blank" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center gap-2 transition shadow-xs">
        <i class="fa-solid fa-arrow-up-right-from-square text-amber-500"></i>
        <span>Lihat Web Publik</span>
    </a>
    <a href="{{ route('admin.prestasi.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 shadow-md shadow-red-950/20 transition">
        <i class="fa-solid fa-plus"></i>
        <span>Tambah Prestasi Baru</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-black text-slate-900 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-trophy"></i>
                    </span>
                    <span>Manajemen Prestasi & Rekam Jejak Juara</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Catat dan abadikan seluruh pencapaian kejuaraan, penghargaan kemanusiaan, serta galeri foto serah terima piala PMR Wira.
                </p>
            </div>
            
            <a href="{{ route('admin.prestasi.create') }}" class="inline-flex items-center gap-2 bg-pmr-primary hover:bg-pmr-dark text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm self-start md:self-auto">
                <i class="fa-solid fa-trophy"></i> Input Juara Baru
            </a>
        </div>

        <!-- 4 KPI Metrics Card (Sesuai Mockup) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                    <span>Total Prestasi</span>
                    <i class="fa-solid fa-award text-amber-500 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-slate-900 mt-1.5">{{ $totalPrestasi }} <span class="text-xs font-bold text-slate-400">Ajang</span></div>
            </div>

            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                    <span>Juara Umum</span>
                    <i class="fa-solid fa-crown text-amber-500 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-amber-600 mt-1.5">{{ $totalJuaraUmum }} <span class="text-xs font-bold text-slate-400">Kali</span></div>
            </div>

            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                    <span>Tingkat Nasional</span>
                    <i class="fa-solid fa-earth-asia text-blue-500 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-blue-600 mt-1.5">{{ $totalNasional }} <span class="text-xs font-bold text-slate-400">Ajang</span></div>
            </div>

            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                    <span>Dokumentasi Foto</span>
                    <i class="fa-solid fa-images text-emerald-500 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-emerald-600 mt-1.5">{{ $totalFoto }} <span class="text-xs font-bold text-slate-400">Foto</span></div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.prestasi.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama kejuaraan, ajang, atau kategori..." 
                    class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary focus:bg-white transition">
            </div>

            <div>
                <select name="level" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary focus:bg-white transition">
                    <option value="all">Semua Tingkatan</option>
                    <option value="Nasional" {{ $level === 'Nasional' ? 'selected' : '' }}>Tingkat Nasional</option>
                    <option value="Provinsi" {{ $level === 'Provinsi' ? 'selected' : '' }}>Tingkat Provinsi</option>
                    <option value="Kabupaten/Kota" {{ $level === 'Kabupaten/Kota' ? 'selected' : '' }}>Tingkat Kabupaten/Kota</option>
                    <option value="Kecamatan" {{ $level === 'Kecamatan' ? 'selected' : '' }}>Tingkat Kecamatan</option>
                    <option value="Sekolah" {{ $level === 'Sekolah' ? 'selected' : '' }}>Tingkat Sekolah</option>
                </select>
            </div>

            <div>
                <select name="rank" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary focus:bg-white transition">
                    <option value="all">Semua Predikat Juara</option>
                    <option value="juara_umum" {{ $rank === 'juara_umum' ? 'selected' : '' }}>Juara Umum</option>
                    <option value="juara_1" {{ $rank === 'juara_1' ? 'selected' : '' }}>Juara 1</option>
                    <option value="Juara 2" {{ $rank === 'Juara 2' ? 'selected' : '' }}>Juara 2</option>
                    <option value="Juara 3" {{ $rank === 'Juara 3' ? 'selected' : '' }}>Juara 3</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <select name="year" onchange="this.form.submit()" class="flex-grow px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary focus:bg-white transition">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ (string)$year === (string)$yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
                @if($search || $level || $rank || $year)
                    <a href="{{ route('admin.prestasi.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Card (Sesuai Mockup) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4 text-center w-12">#</th>
                        <th class="py-3.5 px-4 w-20">Foto</th>
                        <th class="py-3.5 px-4">Prestasi & Nama Ajang</th>
                        <th class="py-3.5 px-4">Tingkat & Juara</th>
                        <th class="py-3.5 px-4">Tanggal Ajang</th>
                        <th class="py-3.5 px-4">Kontingen</th>
                        <th class="py-3.5 px-4 text-center">Galeri</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($achievements as $index => $item)
                        @php
                            $memberCount = is_array($item->contingent_members) ? count($item->contingent_members) : 0;
                            $galleryCount = is_array($item->gallery_images) ? count($item->gallery_images) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition group">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                {{ $achievements->firstItem() + $index }}
                            </td>
                            
                            <!-- Foto Piala -->
                            <td class="py-3.5 px-4">
                                <img src="{{ $item->trophy_image_url }}" alt="{{ $item->title }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-2xs group-hover:scale-105 transition">
                            </td>

                            <!-- Judul & Event -->
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900 group-hover:text-pmr-primary transition line-clamp-1">
                                    {{ $item->title }}
                                </div>
                                <div class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                                    <i class="fa-solid fa-flag-checkered text-[10px] text-amber-500 mr-1"></i>{{ $item->event_name }}
                                </div>
                                @if($item->organizer)
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Penyelenggara: {{ $item->organizer }}
                                    </div>
                                @endif
                            </td>

                            <!-- Tingkat & Predikat -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $item->rank_badge_class }} self-start">
                                        <i class="fa-solid fa-crown text-[9px]"></i>
                                        {{ $item->rank }}
                                    </span>
                                    <span class="text-[11px] font-bold text-slate-600">
                                        Tingkat {{ $item->level }}
                                    </span>
                                </div>
                            </td>

                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 font-semibold">
                                {{ $item->event_date->translatedFormat('d M Y') }}
                            </td>

                            <!-- Kontingen -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded-lg font-bold text-[11px] flex items-center gap-1.5 w-max">
                                    <i class="fa-solid fa-users text-pmr-primary text-[10px]"></i>
                                    {{ $memberCount }} Anggota
                                </span>
                            </td>

                            <!-- Galeri Foto Count -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($galleryCount > 0)
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full text-[10px] font-black">
                                        {{ $galleryCount }} Foto
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">-</span>
                                @endif
                            </td>

                            <!-- Status & Highlight -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex flex-col items-center gap-1">
                                    @if($item->is_featured)
                                        <span class="bg-amber-100 text-amber-800 border border-amber-300 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase flex items-center gap-1">
                                            <i class="fa-solid fa-star text-[9px] text-amber-600"></i> Highlight
                                        </span>
                                    @elseif($item->is_active)
                                        <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full text-[10px] font-bold">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full text-[10px] font-bold">
                                            Arsip
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.prestasi.edit', $item->id) }}" class="p-1.5 rounded-lg text-slate-600 hover:text-pmr-primary hover:bg-red-50 border border-slate-200 transition" title="Edit Data">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.prestasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data prestasi \'{{ $item->title }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition" title="Hapus Prestasi">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-300 text-2xl">
                                    <i class="fa-solid fa-trophy"></i>
                                </div>
                                <div class="font-bold text-slate-700">Belum ada data prestasi</div>
                                <div class="text-xs text-slate-400 mt-1">Klik tombol "+ Tambah Prestasi Baru" untuk mencatat rekam jejak juara.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($achievements->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                {{ $achievements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
