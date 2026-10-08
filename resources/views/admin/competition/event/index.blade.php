@extends('layouts.admin')

@section('title', 'Database & Riwayat SUA BHAKTI BERKARYA')
@section('page_title', 'Database & Riwayat Event (SUA BHAKTI BERKARYA)')

@section('top_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('lomba.index') }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-arrow-up-right-from-square text-amber-400"></i>
            <span>Web Publik /lomba</span>
        </a>
        <a href="{{ route('admin.competition-event.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Edisi Baru</span>
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

    <!-- 1. Highlight Edisi yang Sedang Aktif di Web Utama -->
    @if($activeEvent)
        <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 text-white rounded-2xl p-6 sm:p-7 border border-slate-700 shadow-md relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl text-white pointer-events-none">
                <i class="fa-solid fa-trophy"></i>
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-3 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500 text-slate-950 shadow-sm animate-pulse">
                            <i class="fa-solid fa-circle text-[8px]"></i> EDISI AKTIF DI WEBSITE
                        </span>
                        @if($activeEvent->is_registration_open)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                <i class="fa-solid fa-door-open"></i> Pendaftaran Buka
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <i class="fa-solid fa-door-closed"></i> Pendaftaran Tutup
                            </span>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight uppercase">
                            {{ $activeEvent->title }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                            {{ $activeEvent->description ?: 'Ajang kompetisi kepalangmerahan PMR Wira SMAN 1 Ciawi Bogor.' }}
                        </p>
                    </div>

                    <!-- Meta Tags -->
                    <div class="flex flex-wrap gap-4 pt-1 text-xs text-slate-300">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-days text-rose-400"></i>
                            <span>{{ $activeEvent->start_date ? $activeEvent->start_date->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-emerald-400"></i>
                            <span>{{ $activeEvent->location ?: '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-money-bill-wave text-amber-400"></i>
                            <span>Rp {{ number_format($activeEvent->registration_fee ?: 0, 0, ',', '.') }} / Tim</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-sitemap text-indigo-400"></i>
                            <span>{{ $activeEvent->categories_count }} Cabang Lomba</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-school text-sky-400"></i>
                            <span>{{ $activeEvent->registrations_count }} Sekolah Terdaftar</span>
                        </div>
                    </div>
                </div>

                <!-- Action Button in Card -->
                <div class="flex lg:flex-col gap-2 shrink-0">
                    <a href="{{ route('admin.competition-event.edit', $activeEvent->id) }}" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center justify-center gap-2 shadow-md shadow-red-950/40">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit Pengaturan Edisi Ini</span>
                    </a>
                    <a href="{{ route('admin.competition-event.show', $activeEvent->id) }}" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-folder-open text-sky-400"></i>
                        <span>Detail & History Data</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. Tabel Database & History Seluruh Edisi SUA BHAKTI BERKARYA -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-database text-red-600"></i>
                    <span>Daftar Riwayat Edisi SUA BHAKTI BERKARYA (History)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Seluruh arsip penyelenggaraan lomba dari tahun ke tahun tersimpan aman di database</p>
            </div>

            <div class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg w-fit">
                Total: {{ $events->count() }} Edisi Tercatat
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-black tracking-wider text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Edisi / Judul Lomba</th>
                        <th class="py-3.5 px-4">Tahun & Tanggal</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4 text-center">Cabang Lomba</th>
                        <th class="py-3.5 px-4 text-center">Sekolah Terdaftar</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($events as $event)
                        <tr class="hover:bg-slate-50/80 transition {{ $event->is_active ? 'bg-red-50/20' : '' }}">
                            <td class="py-4 px-5">
                                <div class="font-black text-slate-900 text-sm">
                                    {{ $event->title }}
                                </div>
                                @if($event->theme)
                                    <div class="text-[11px] text-slate-500 mt-0.5 italic">
                                        &ldquo;{{ $event->theme }}&rdquo;
                                    </div>
                                @endif
                                <div class="text-[10px] text-slate-400 font-mono mt-1">
                                    Slug: {{ $event->slug }}
                                </div>
                            </td>

                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">
                                    {{ $event->start_date ? $event->start_date->translatedFormat('d M Y') : '-' }}
                                </div>
                                @if($event->end_date && $event->end_date != $event->start_date)
                                    <div class="text-[10px] text-slate-400">
                                        s/d {{ $event->end_date->translatedFormat('d M Y') }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-4 px-4 max-w-[180px] truncate">
                                {{ $event->location ?: '-' }}
                            </td>

                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="bg-indigo-50 text-indigo-700 font-bold px-2.5 py-1 rounded-md text-[11px]">
                                    {{ $event->categories_count }} Cabang
                                </span>
                            </td>

                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-md text-[11px]">
                                    {{ $event->registrations_count }} Sekolah
                                </span>
                            </td>

                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @if($event->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> AKTIF
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                        <i class="fa-solid fa-box-archive text-[10px]"></i> ARSIP
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-right whitespace-nowrap space-x-1.5">
                                <!-- Lihat Detail / History -->
                                <a href="{{ route('admin.competition-event.show', $event->id) }}" class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-2.5 py-1.5 rounded-lg text-xs transition" title="Lihat History & Data">
                                    <i class="fa-solid fa-eye text-sky-600"></i>
                                    <span>History</span>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('admin.competition-event.edit', $event->id) }}" class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-2.5 py-1.5 rounded-lg text-xs transition" title="Edit Pengaturan">
                                    <i class="fa-solid fa-pen-to-square text-amber-600"></i>
                                    <span>Edit</span>
                                </a>

                                <!-- Set Sebagai Event Aktif -->
                                @if(!$event->is_active)
                                    <form action="{{ route('admin.competition-event.activate', $event->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Jadikan edisi ini sebagai EVENT AKTIF di website publik?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold px-2.5 py-1.5 rounded-lg text-xs transition" title="Aktifkan Edisi Ini">
                                            <i class="fa-solid fa-play text-emerald-600"></i>
                                            <span>Aktifkan</span>
                                        </button>
                                    </form>
                                @endif

                                <!-- Hapus (Hanya jika belum ada pendaftar & tidak aktif) -->
                                @if(!$event->is_active && $event->registrations_count === 0)
                                    <form action="{{ route('admin.competition-event.destroy', $event->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus edisi lomba ini dari database?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-7 h-7 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition" title="Hapus">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada edisi lomba yang tercatat di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
