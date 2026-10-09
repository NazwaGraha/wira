@extends('layouts.admin')

@section('title', 'Kelola Menu Informasi Lomba')
@section('page_title', 'Pusat Pengelolaan Menu Informasi Lomba')

@section('top_actions')
    <div class="flex items-center gap-2.5 flex-wrap">
        <a href="{{ route('lomba.index') }}" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-2 border border-slate-200">
            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Lihat Tampilan Web
        </a>
        <form action="{{ route('admin.competition-info-menus.reset-defaults') }}" method="POST" onsubmit="return confirm('Kembalikan atau lengkapi 8 sub menu informasi lomba standar bawaan sistem?');" class="inline">
            @csrf
            <button type="submit" class="bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-2 border border-amber-200 cursor-pointer">
                <i class="fa-solid fa-rotate-left"></i> Pulihkan 8 Menu Default
            </button>
        </form>
        <a href="{{ route('admin.competition-info-menus.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Sub Menu Baru
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg shrink-0"></i>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg shrink-0"></i>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Event Selector Filter -->
    @if($allEvents->count() > 1)
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-medium">Pilih Event Perlombaan:</div>
                    <div class="font-extrabold text-sm text-slate-800">{{ $selectedEvent ? $selectedEvent->title : 'Semua Event' }}</div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @foreach($allEvents as $ev)
                    <a href="{{ route('admin.competition-info-menus.index', ['event_id' => $ev->id]) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $selectedEvent && $selectedEvent->id === $ev->id ? 'bg-pmr-primary text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                        {{ $ev->title }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Statistic Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-folder-tree"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $totalMenus }}</div>
                <div class="text-xs text-slate-500 font-medium">Total Sub Menu</div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $activeCount }}</div>
                <div class="text-xs text-slate-500 font-medium">Menu Aktif</div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $fileCount }}</div>
                <div class="text-xs text-slate-500 font-medium">Dokumen Berkas</div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-link"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $linkCount }}</div>
                <div class="text-xs text-slate-500 font-medium">Tautan / Kontak</div>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($infoMenus as $item)
            @php
                $themeClasses = $item->theme_classes;
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden group {{ !$item->is_active ? 'opacity-60 bg-slate-50/80' : '' }}">
                
                <!-- Card Header Info -->
                <div class="p-5.5 space-y-4">
                    <!-- Top Bar: Icon, Badge, Order -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-xl text-slate-700 shadow-inner group-hover:scale-105 transition">
                            <i class="{{ $item->icon ?: 'fa-solid fa-folder-open' }}"></i>
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap justify-end">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $item->category_badge }}
                            </span>
                            <span class="w-6 h-6 rounded-md bg-slate-800 text-white text-[11px] font-black flex items-center justify-center" title="Urutan Posisi">
                                #{{ $item->order_position }}
                            </span>
                        </div>
                    </div>

                    <!-- Title & Description -->
                    <div>
                        <h4 class="font-extrabold text-base text-slate-800 group-hover:text-pmr-primary transition line-clamp-1">
                            {{ $item->title }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1.5 line-clamp-3 leading-relaxed">
                            {{ $item->description ?: 'Tidak ada keterangan tambahan.' }}
                        </p>
                    </div>

                    <!-- Action Type Details -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Tipe Aksi:</span>
                            <span class="font-bold text-slate-700 capitalize">
                                @if($item->action_type === 'file')
                                    <i class="fa-solid fa-file-arrow-down text-rose-500 mr-1"></i> Berkas Upload
                                @elseif($item->action_type === 'link')
                                    <i class="fa-solid fa-arrow-up-right-from-square text-sky-500 mr-1"></i> Tautan URL
                                @elseif($item->action_type === 'whatsapp')
                                    <i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i> Chat WhatsApp
                                @else
                                    <i class="fa-solid fa-clock-rotate-left text-amber-500 mr-1"></i> Status Disiapkan
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Warna Tema:</span>
                            <span class="font-bold capitalize text-slate-700 flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-full inline-block bg-{{ $item->color_theme == 'red' ? 'red-600' : ($item->color_theme == 'sky' ? 'sky-500' : ($item->color_theme == 'rose' ? 'rose-600' : ($item->color_theme == 'amber' ? 'amber-500' : ($item->color_theme == 'emerald' ? 'emerald-500' : ($item->color_theme == 'purple' ? 'purple-600' : ($item->color_theme == 'cyan' ? 'cyan-500' : 'indigo-600')))))) }}"></span>
                                {{ $item->color_theme }}
                            </span>
                        </div>
                        @if($item->action_type === 'file' && $item->file_path)
                            <div class="pt-1 border-t border-slate-200/60 truncate">
                                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="text-pmr-primary hover:underline font-semibold flex items-center gap-1 truncate">
                                    <i class="fa-solid fa-paperclip shrink-0"></i>
                                    <span class="truncate">{{ basename($item->file_path) }}</span>
                                </a>
                            </div>
                        @elseif($item->url_link)
                            <div class="pt-1 border-t border-slate-200/60 truncate">
                                <a href="{{ $item->url_link }}" target="_blank" class="text-sky-600 hover:underline font-semibold flex items-center gap-1 truncate">
                                    <i class="fa-solid fa-link shrink-0"></i>
                                    <span class="truncate">{{ $item->url_link }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Footer & Action Buttons -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    <!-- Toggle Active Form -->
                    <form action="{{ route('admin.competition-info-menus.toggle-active', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer {{ $item->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}" title="Klik untuk {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="fa-solid fa-power-off text-[11px]"></i>
                            <span>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </button>
                    </form>

                    <!-- Edit & Delete -->
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.competition-info-menus.edit', $item->id) }}" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition text-xs font-bold" title="Edit Menu">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('admin.competition-info-menus.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu \'{{ $item->title }}\'?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition text-xs font-bold cursor-pointer" title="Hapus Menu">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h4 class="font-extrabold text-slate-700 text-base">Belum Ada Sub Menu Informasi Lomba</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                    Klik tombol pulihkan di atas untuk mengaktifkan 8 menu standar otomatis, atau klik tambah untuk membuat manual.
                </p>
                <div class="mt-5 flex items-center justify-center gap-3">
                    <form action="{{ route('admin.competition-info-menus.reset-defaults') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-rotate-left"></i> Pulihkan 8 Menu Default
                        </button>
                    </form>
                    <a href="{{ route('admin.competition-info-menus.create') }}" class="px-5 py-2.5 rounded-xl bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Tambah Menu Baru
                    </a>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
