@extends('layouts.admin')

@section('title', 'Kelola Kegiatan')
@section('page_title', 'Kelola Agenda & Kegiatan PMR')

@section('top_actions')
<a href="{{ route('admin.activities.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 shadow-md shadow-red-950/20 transition">
    <i class="fa-solid fa-plus"></i>
    <span>Tambah Kegiatan Baru</span>
</a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI / Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kegiatan</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalActivities }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Tersimpan di database</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-xl">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Agenda Mendatang</div>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $upcomingCount }}</div>
                <div class="text-[11px] text-emerald-600 mt-1">Kegiatan aktif ke depan</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Program Prioritas</div>
                <div class="text-3xl font-extrabold text-amber-600 mt-1">{{ $featuredCount }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Banner utama kegiatan</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Search & Filter Bar -->
        <div class="p-6 bg-slate-50/70 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form action="{{ route('admin.activities.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-grow max-w-2xl">
                <div class="relative flex-grow">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari judul, lokasi, atau deskripsi kegiatan..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition">
                </div>

                <select name="kategori" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-pmr-primary transition bg-white">
                    <option value="Semua">Semua Kategori</option>
                    @foreach(['Pertolongan Pertama', 'Donor Darah', 'Kesiapsiagaan Bencana', 'Pendidikan', 'Bakti Sosial', 'Latihan Rutin'] as $c)
                        <option value="{{ $c }}" {{ $category === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold transition">
                    Filter
                </button>

                @if($search || ($category && $category !== 'Semua'))
                    <a href="{{ route('admin.activities.index') }}" class="text-xs text-red-600 hover:underline font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </form>

            <a href="{{ route('kegiatan') }}" target="_blank" class="text-xs font-semibold text-pmr-primary hover:underline flex items-center gap-1.5 self-start md:self-auto">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat di Web Publik
            </a>
        </div>

        @if($activities->isEmpty())
            <div class="p-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
                <h3 class="font-bold text-slate-800 text-base mb-1">Tidak Ada Kegiatan Ditemukan</h3>
                <p class="text-slate-500 text-xs mb-6">Belum ada kegiatan yang cocok dengan kriteria pencarian atau belum ada data yang dimasukkan.</p>
                <a href="{{ route('admin.activities.create') }}" class="bg-pmr-primary text-white text-xs font-bold px-4 py-2.5 rounded-xl inline-flex items-center gap-2 shadow">
                    <i class="fa-solid fa-plus"></i> Tambah Kegiatan Baru
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/75 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-600">
                            <th class="py-3 px-4 w-20">Foto</th>
                            <th class="py-3 px-4">Judul & Lokasi</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Tanggal Pelaksanaan</th>
                            <th class="py-3 px-4 text-center">Prioritas</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($activities as $act)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Thumbnail -->
                            <td class="py-3 px-4">
                                <div class="w-16 h-12 rounded-xl overflow-hidden bg-slate-900 shadow-sm border border-slate-200 flex-shrink-0">
                                    <img src="{{ $act->image ?: '/mockups/03_kegiatan.jpg' }}" alt="{{ $act->title }}" class="w-full h-full object-cover">
                                </div>
                            </td>

                            <!-- Title & Location -->
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 hover:text-pmr-primary transition">
                                    {{ $act->title }}
                                </div>
                                <div class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-location-dot text-red-500 text-[10px]"></i>
                                    <span>{{ $act->location ?: 'Kampus SMAN 1 Ciawi' }}</span>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-pmr-primary border border-red-100">
                                    {{ $act->category }}
                                </span>
                            </td>

                            <!-- Event Date -->
                            <td class="py-3 px-4 whitespace-nowrap text-xs">
                                @if($act->event_date)
                                    <div class="font-bold text-slate-800">
                                        {{ $act->event_date->format('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $act->event_date->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-slate-400">Belum diatur</span>
                                @endif
                            </td>

                            <!-- Featured Status -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($act->is_featured)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-star text-[10px] text-amber-500"></i> Prioritas
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Reguler</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.activities.edit', $act) }}" title="Edit Kegiatan" class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold transition text-xs flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </a>

                                    <form action="{{ route('admin.activities.destroy', $act) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan \'{{ $act->title }}\' dari database?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Kegiatan" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 font-bold transition text-xs flex items-center gap-1">
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

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
