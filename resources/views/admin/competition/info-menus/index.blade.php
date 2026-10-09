@extends('layouts.admin')

@section('title', 'Kelola Menu Informasi Lomba')
@section('page_title', 'Pusat Pengelolaan Menu Informasi Lomba')

@section('top_actions')
    <div class="flex items-center gap-2.5 flex-wrap">
        <a href="{{ route('lomba.index') }}" target="_blank" class="bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-2 border border-slate-200 shadow-sm">
            <i class="fa-solid fa-arrow-up-right-from-square text-xs text-sky-500"></i> Lihat Tampilan Web
        </a>
        <form action="{{ route('admin.competition-info-menus.reset-defaults') }}" method="POST" onsubmit="return confirm('Kembalikan atau lengkapi 8 sub menu informasi lomba standar bawaan sistem?');" class="inline">
            @csrf
            <button type="submit" class="bg-amber-50 hover:bg-amber-100 text-amber-700 font-extrabold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-2 border border-amber-200 cursor-pointer shadow-sm">
                <i class="fa-solid fa-rotate-left text-amber-600"></i> Pulihkan 8 Menu Default
            </button>
        </form>
        <a href="{{ route('admin.competition-info-menus.create') }}" class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-extrabold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-md shadow-red-950/20 transition flex items-center gap-2">
            <i class="fa-solid fa-plus text-sm"></i> Tambah Sub Menu Baru
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="bg-emerald-500/10 border-2 border-emerald-500/30 text-emerald-900 px-5 py-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-base shrink-0 shadow-sm">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Berhasil</div>
                    <div class="text-sm font-extrabold text-emerald-950">{{ session('success') }}</div>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 text-sm font-bold p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-500/10 border-2 border-rose-500/30 text-rose-900 px-5 py-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center text-base shrink-0 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-rose-700 uppercase tracking-wider">Perhatian</div>
                    <div class="text-sm font-extrabold text-rose-950">{{ session('error') }}</div>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 text-sm font-bold p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Hero Banner with Visual Overview -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900 to-red-950 text-white border border-slate-800 shadow-xl p-6 sm:p-8">
        <!-- Background decorative ambient circles -->
        <div class="absolute -right-12 -bottom-12 w-72 h-72 rounded-full bg-red-600/15 blur-3xl pointer-events-none"></div>
        <div class="absolute left-1/3 -top-12 w-64 h-64 rounded-full bg-rose-600/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/20 border border-red-500/30 text-red-300 text-xs font-black uppercase tracking-wider">
                    <i class="fa-solid fa-folder-open text-xs"></i> Pusat Berkas & Layanan Kontingen Lomba
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Pengelolaan Menu Informasi Lomba
                </h2>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Kelola 8 sub menu informasi terpadu yang tampil pada pop-up modal di halaman utama lomba. Unggah dokumen PDF resmi (Surat Rekomendasi, Juklak Juknis, Grid Nilai), pasang tautan Google Maps denah venue, atau nomor hotline WhatsApp narahubung.
                </p>
            </div>

            <!-- Quick Action Box / Summary Indicator -->
            <div class="flex items-center gap-3 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('admin.competition-info-menus.create') }}" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-black text-xs uppercase tracking-wider transition shadow-lg shadow-red-950/50 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus text-sm"></i> Tambah Sub Menu Baru
                </a>
                <a href="{{ route('lomba.index') }}" target="_blank" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-eye text-sm text-sky-400"></i> Cek Tampilan Web
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Modern Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Menu -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition relative overflow-hidden group">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-rose-500"></div>
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition">
                    <i class="fa-solid fa-folder-tree"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md bg-red-50 text-red-700 border border-red-200/60">
                    Total
                </span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalMenus }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5">Sub Menu Terdaftar</div>
            </div>
        </div>

        <!-- 2. Menu Aktif -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition relative overflow-hidden group">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live
                </span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $activeCount }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5">Tampil di Halaman Publik</div>
            </div>
        </div>

        <!-- 3. Berkas Terlampir -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition relative overflow-hidden group">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-sky-500 to-blue-500"></div>
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md bg-sky-50 text-sky-700 border border-sky-200/60">
                    {{ $uploadedFileCount }} / {{ $fileCount }} Terpasang
                </span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $uploadedFileCount }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5">File Berkas Siap Unduh</div>
            </div>
        </div>

        <!-- 4. Tautan & Hotline -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition relative overflow-hidden group">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-indigo-500"></div>
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition">
                    <i class="fa-solid fa-link"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md bg-purple-50 text-purple-700 border border-purple-200/60">
                    URL & WA
                </span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $linkCount }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5">Tautan Luar & Hotline</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar, Real-time Search & View Switcher -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search Input -->
        <div class="relative w-full md:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="menuSearchInput" onkeyup="filterMenuCards()" placeholder="Cari nama menu, badge, atau tipe..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-red-500 focus:ring-2 focus:ring-red-100 text-xs font-semibold transition">
        </div>

        <!-- Filter Pills & View Mode Switcher -->
        <div class="flex items-center gap-2 flex-wrap w-full md:w-auto justify-between md:justify-end">
            <!-- Filter by Type -->
            <div class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 border border-slate-200/80 text-xs font-bold text-slate-600">
                <button type="button" onclick="setFilterType('all')" class="filter-btn active px-3 py-1.5 rounded-lg transition bg-white text-slate-900 shadow-xs cursor-pointer" data-filter="all">
                    Semua ({{ $totalMenus }})
                </button>
                <button type="button" onclick="setFilterType('file')" class="filter-btn px-3 py-1.5 rounded-lg transition hover:text-slate-900 cursor-pointer" data-filter="file">
                    <i class="fa-solid fa-file-pdf mr-1 text-rose-500"></i> Berkas
                </button>
                <button type="button" onclick="setFilterType('link')" class="filter-btn px-3 py-1.5 rounded-lg transition hover:text-slate-900 cursor-pointer" data-filter="link">
                    <i class="fa-solid fa-link mr-1 text-sky-500"></i> Tautan / WA
                </button>
            </div>

            <!-- View Mode Switcher -->
            <div class="flex items-center gap-1 p-1 rounded-xl bg-slate-100 border border-slate-200/80 text-xs font-bold text-slate-600">
                <button type="button" onclick="switchView('grid')" id="btnViewGrid" class="px-2.5 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition cursor-pointer" title="Tampilan Grid Kartu Visual">
                    <i class="fa-solid fa-table-cells-large"></i>
                </button>
                <button type="button" onclick="switchView('table')" id="btnViewTable" class="px-2.5 py-1.5 rounded-lg hover:text-slate-900 transition cursor-pointer" title="Tampilan Tabel Data">
                    <i class="fa-solid fa-table-list"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- VIEW 1: VIBRANT COLORFUL GRID CARDS (DEFAULT)                  -->
    <!-- ============================================================== -->
    <div id="gridContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($infoMenus as $item)
            @php
                $theme = strtolower($item->color_theme ?: 'red');
                $hasFile = ($item->action_type === 'file' && $item->file_path);
                
                // Color configuration presets for backoffice visual cards
                $accents = [
                    'red' => [
                        'bar' => 'from-red-600 to-rose-600',
                        'icon_bg' => 'bg-red-50 text-red-600 border-red-200/80',
                        'badge' => 'bg-red-50 text-red-700 border-red-200',
                        'highlight_box' => 'bg-red-50/70 border-red-200/80 text-red-900',
                        'border_hover' => 'hover:border-red-400',
                    ],
                    'sky' => [
                        'bar' => 'from-sky-500 to-blue-600',
                        'icon_bg' => 'bg-sky-50 text-sky-600 border-sky-200/80',
                        'badge' => 'bg-sky-50 text-sky-700 border-sky-200',
                        'highlight_box' => 'bg-sky-50/70 border-sky-200/80 text-sky-900',
                        'border_hover' => 'hover:border-sky-400',
                    ],
                    'rose' => [
                        'bar' => 'from-rose-500 to-pink-600',
                        'icon_bg' => 'bg-rose-50 text-rose-600 border-rose-200/80',
                        'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
                        'highlight_box' => 'bg-rose-50/70 border-rose-200/80 text-rose-900',
                        'border_hover' => 'hover:border-rose-400',
                    ],
                    'amber' => [
                        'bar' => 'from-amber-500 to-orange-500',
                        'icon_bg' => 'bg-amber-50 text-amber-600 border-amber-200/80',
                        'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'highlight_box' => 'bg-amber-50/70 border-amber-200/80 text-amber-900',
                        'border_hover' => 'hover:border-amber-400',
                    ],
                    'emerald' => [
                        'bar' => 'from-emerald-500 to-teal-600',
                        'icon_bg' => 'bg-emerald-50 text-emerald-600 border-emerald-200/80',
                        'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'highlight_box' => 'bg-emerald-50/70 border-emerald-200/80 text-emerald-900',
                        'border_hover' => 'hover:border-emerald-400',
                    ],
                    'purple' => [
                        'bar' => 'from-purple-500 to-indigo-600',
                        'icon_bg' => 'bg-purple-50 text-purple-600 border-purple-200/80',
                        'badge' => 'bg-purple-50 text-purple-700 border-purple-200',
                        'highlight_box' => 'bg-purple-50/70 border-purple-200/80 text-purple-900',
                        'border_hover' => 'hover:border-purple-400',
                    ],
                    'cyan' => [
                        'bar' => 'from-cyan-500 to-teal-600',
                        'icon_bg' => 'bg-cyan-50 text-cyan-600 border-cyan-200/80',
                        'badge' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                        'highlight_box' => 'bg-cyan-50/70 border-cyan-200/80 text-cyan-900',
                        'border_hover' => 'hover:border-cyan-400',
                    ],
                    'indigo' => [
                        'bar' => 'from-indigo-500 to-violet-600',
                        'icon_bg' => 'bg-indigo-50 text-indigo-600 border-indigo-200/80',
                        'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'highlight_box' => 'bg-indigo-50/70 border-indigo-200/80 text-indigo-900',
                        'border_hover' => 'hover:border-indigo-400',
                    ],
                ];

                $ui = $accents[$theme] ?? $accents['red'];
            @endphp

            <div class="menu-card bg-white rounded-3xl border-2 border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group {{ $ui['border_hover'] }} {{ !$item->is_active ? 'opacity-65 grayscale-20 bg-slate-50/80' : '' }}"
                 data-title="{{ strtolower($item->title) }}"
                 data-badge="{{ strtolower($item->category_badge) }}"
                 data-type="{{ $item->action_type }}"
                 data-active="{{ $item->is_active ? '1' : '0' }}">
                
                <!-- Top Color Accent Gradient Strip -->
                <div class="h-2 w-full bg-gradient-to-r {{ $ui['bar'] }}"></div>

                <div class="p-6 space-y-4">
                    <!-- Top Bar: Icon, Category Badge & Order Chip -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="w-13 h-13 rounded-2xl {{ $ui['icon_bg'] }} border flex items-center justify-center text-2xl shadow-xs group-hover:scale-108 transition duration-200">
                            <i class="{{ $item->icon ?: 'fa-solid fa-folder-open' }}"></i>
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap justify-end">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-black uppercase tracking-wider border shadow-2xs {{ $ui['badge'] }}">
                                {{ $item->category_badge }}
                            </span>
                            <span class="px-2 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-black shadow-xs" title="Urutan Posisi">
                                #{{ $item->order_position }}
                            </span>
                        </div>
                    </div>

                    <!-- Title & Description -->
                    <div>
                        <h4 class="font-black text-slate-900 text-base group-hover:text-red-600 transition tracking-tight line-clamp-1">
                            {{ $item->title }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed font-medium">
                            {{ $item->description ?: 'Tidak ada keterangan tambahan.' }}
                        </p>
                    </div>

                    <!-- Dedicated Action Status Box (Clear & Useful!) -->
                    <div class="rounded-2xl p-3.5 border text-xs space-y-2 {{ $ui['highlight_box'] }}">
                        <div class="flex items-center justify-between font-bold text-[11px]">
                            <span class="text-slate-500 uppercase tracking-wider">Aksi Tombol:</span>
                            <span class="font-black">
                                @if($item->action_type === 'file')
                                    <span class="text-rose-600"><i class="fa-solid fa-file-arrow-down mr-1"></i> Berkas Dokumen</span>
                                @elseif($item->action_type === 'link')
                                    <span class="text-sky-600"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Tautan Luar / URL</span>
                                @elseif($item->action_type === 'whatsapp')
                                    <span class="text-emerald-600"><i class="fa-brands fa-whatsapp mr-1"></i> Chat WhatsApp</span>
                                @else
                                    <span class="text-amber-600"><i class="fa-solid fa-clock-rotate-left mr-1"></i> Modal "Sedang Disiapkan"</span>
                                @endif
                            </span>
                        </div>

                        <!-- File or Link Direct Interaction -->
                        @if($item->action_type === 'file')
                            @if($hasFile)
                                <div class="p-2.5 rounded-xl bg-white/90 border border-emerald-200/80 flex items-center justify-between gap-2 shadow-2xs">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fa-solid fa-file-pdf text-rose-500 text-base shrink-0"></i>
                                        <span class="truncate font-bold text-slate-800 text-[11px]">{{ basename($item->file_path) }}</span>
                                    </div>
                                    <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10px] shrink-0 transition flex items-center gap-1">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i> Buka
                                    </a>
                                </div>
                            @else
                                <div class="p-2.5 rounded-xl bg-white/90 border border-amber-300/80 flex items-center justify-between gap-2 shadow-2xs">
                                    <div class="flex items-center gap-1.5 text-amber-700 font-bold text-[11px] truncate">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-500 shrink-0"></i>
                                        <span class="truncate">Belum ada file terunggah</span>
                                    </div>
                                    <button type="button" onclick="openQuickUpload('{{ $item->id }}', '{{ addslashes($item->title) }}')" class="px-2.5 py-1 rounded-lg bg-red-600 hover:bg-red-500 text-white font-black text-[10px] shrink-0 transition shadow-xs cursor-pointer flex items-center gap-1">
                                        <i class="fa-solid fa-upload text-[9px]"></i> + Upload
                                    </button>
                                </div>
                            @endif
                        @elseif($item->action_type === 'link')
                            <div class="p-2.5 rounded-xl bg-white/90 border border-sky-200/80 flex items-center justify-between gap-2 shadow-2xs">
                                <div class="flex items-center gap-1.5 text-sky-700 font-bold text-[11px] truncate">
                                    <i class="fa-solid fa-link shrink-0"></i>
                                    <span class="truncate">{{ $item->url_link ?: 'Belum ada tautan' }}</span>
                                </div>
                                @if($item->url_link)
                                    <a href="{{ $item->url_link }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-extrabold text-[10px] shrink-0 transition flex items-center gap-1">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i> Tes
                                    </a>
                                @endif
                            </div>
                        @elseif($item->action_type === 'whatsapp')
                            <div class="p-2.5 rounded-xl bg-white/90 border border-emerald-200/80 flex items-center justify-between gap-2 shadow-2xs">
                                <div class="flex items-center gap-1.5 text-emerald-700 font-bold text-[11px] truncate">
                                    <i class="fa-brands fa-whatsapp text-emerald-500 text-sm shrink-0"></i>
                                    <span class="truncate">Hotline WhatsApp</span>
                                </div>
                                @if($item->url_link)
                                    <a href="{{ $item->url_link }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10px] shrink-0 transition flex items-center gap-1">
                                        <i class="fa-brands fa-whatsapp text-[10px]"></i> Chat
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="p-2 rounded-xl bg-amber-100/60 text-amber-800 text-[11px] font-medium flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-info text-amber-600 shrink-0"></i>
                                <span>Menampilkan dialog notifikasi "Sedang Disiapkan Panitia".</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Footer & Action Buttons -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    <!-- Toggle Switch Form -->
                    <form action="{{ route('admin.competition-info-menus.toggle-active', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-2 cursor-pointer shadow-2xs {{ $item->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}" title="Klik untuk {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }} menu ini">
                            <span class="w-2 h-2 rounded-full {{ $item->is_active ? 'bg-emerald-600 animate-pulse' : 'bg-slate-400' }}"></span>
                            <span>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </button>
                    </form>

                    <!-- Edit, Quick Upload & Delete -->
                    <div class="flex items-center gap-1.5">
                        @if($item->action_type === 'file')
                            <button type="button" onclick="openQuickUpload('{{ $item->id }}', '{{ addslashes($item->title) }}')" class="p-2 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-700 hover:text-red-600 transition text-xs font-bold border border-slate-200 cursor-pointer" title="{{ $hasFile ? 'Ganti File Berkas' : 'Unggah File Berkas' }}">
                                <i class="fa-solid fa-upload"></i>
                            </button>
                        @endif
                        <a href="{{ route('admin.competition-info-menus.edit', $item->id) }}" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white transition text-xs font-extrabold flex items-center gap-1.5 border border-blue-200/80 shadow-2xs" title="Edit Detail Sub Menu">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit</span>
                        </a>
                        <form action="{{ route('admin.competition-info-menus.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu \'{{ $item->title }}\'? File yang terunggah juga akan dihapus.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white transition text-xs font-bold border border-rose-200/80 cursor-pointer shadow-2xs" title="Hapus Menu">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border-2 border-dashed border-slate-300 p-8">
                <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-500 mx-auto flex items-center justify-center text-3xl mb-4 shadow-inner">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h4 class="font-black text-slate-800 text-lg">Belum Ada Sub Menu Informasi Lomba</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                    Data menu masih kosong. Klik tombol pulihkan untuk otomatis memuat 8 sub menu standar perlombaan bawaan sistem.
                </p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <form action="{{ route('admin.competition-info-menus.reset-defaults') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-rotate-left"></i> Pulihkan 8 Menu Default
                        </button>
                    </form>
                    <a href="{{ route('admin.competition-info-menus.create') }}" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Tambah Menu Baru
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- ============================================================== -->
    <!-- VIEW 2: COMPACT STRUCTURED TABLE VIEW                          -->
    <!-- ============================================================== -->
    <div id="tableContainer" class="hidden bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white text-[11px] font-black uppercase tracking-wider border-b border-slate-800">
                        <th class="py-4 px-4 text-center w-14">#</th>
                        <th class="py-4 px-5">Nama Sub Menu & Ikon</th>
                        <th class="py-4 px-4">Kategori</th>
                        <th class="py-4 px-4">Warna</th>
                        <th class="py-4 px-4">Tipe Aksi & Berkas / Tautan</th>
                        <th class="py-4 px-4 text-center">Status</th>
                        <th class="py-4 px-5 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($infoMenus as $item)
                        @php
                            $hasFile = ($item->action_type === 'file' && $item->file_path);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ !$item->is_active ? 'bg-slate-50/60 opacity-60' : '' }}">
                            <td class="py-4 px-4 text-center font-black text-slate-800">
                                #{{ $item->order_position }}
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-lg text-slate-700 shrink-0 border border-slate-200">
                                        <i class="{{ $item->icon ?: 'fa-solid fa-folder-open' }}"></i>
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-sm text-slate-900">{{ $item->title }}</div>
                                        <div class="text-[11px] text-slate-400 line-clamp-1">{{ $item->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $item->category_badge }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold capitalize bg-slate-100 text-slate-700">
                                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $item->color_theme == 'red' ? 'red-600' : ($item->color_theme == 'sky' ? 'sky-500' : ($item->color_theme == 'rose' ? 'rose-600' : ($item->color_theme == 'amber' ? 'amber-500' : ($item->color_theme == 'emerald' ? 'emerald-500' : ($item->color_theme == 'purple' ? 'purple-600' : ($item->color_theme == 'cyan' ? 'cyan-500' : 'indigo-600')))))) }}"></span>
                                    {{ $item->color_theme }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                @if($item->action_type === 'file')
                                    @if($hasFile)
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 font-bold text-rose-600 hover:underline">
                                            <i class="fa-solid fa-file-pdf"></i>
                                            <span>{{ basename($item->file_path) }}</span>
                                        </a>
                                    @else
                                        <button type="button" onclick="openQuickUpload('{{ $item->id }}', '{{ addslashes($item->title) }}')" class="inline-flex items-center gap-1 font-bold text-amber-600 hover:underline cursor-pointer">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Belum ada file (+ Upload)
                                        </button>
                                    @endif
                                @elseif($item->action_type === 'link')
                                    <a href="{{ $item->url_link }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-sky-600 hover:underline max-w-[200px] truncate">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        <span class="truncate">{{ $item->url_link }}</span>
                                    </a>
                                @elseif($item->action_type === 'whatsapp')
                                    <a href="{{ $item->url_link }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-emerald-600 hover:underline">
                                        <i class="fa-brands fa-whatsapp text-sm"></i> Chat WhatsApp
                                    </a>
                                @else
                                    <span class="text-slate-400 font-semibold"><i class="fa-solid fa-clock-rotate-left mr-1"></i> Sedang Disiapkan</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <form action="{{ route('admin.competition-info-menus.toggle-active', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-black transition cursor-pointer {{ $item->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.competition-info-menus.edit', $item->id) }}" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.competition-info-menus.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus menu ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold transition cursor-pointer" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data sub menu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ============================================================== -->
<!-- MODAL POPUP: QUICK UPLOAD FILE DOKUMEN                         -->
<!-- ============================================================== -->
<div id="quickUploadModal" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden transform transition-all text-slate-800 animate-scale-up">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-red-600 to-rose-600 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-file-arrow-up"></i>
                </div>
                <div>
                    <h3 class="font-black text-base">Unggah Berkas Cepat</h3>
                    <div id="quickUploadMenuTitle" class="text-xs text-red-100 font-medium">Sub Menu</div>
                </div>
            </div>
            <button type="button" onclick="closeQuickUpload()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="quickUploadForm" method="POST" enctype="multipart/form-data" action="" class="p-6 space-y-5">
            @csrf
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Pilih File Berkas (PDF, DOC, XLS, ZIP) <span class="text-rose-500">*</span></label>
                <div class="border-2 border-dashed border-slate-300 hover:border-red-500 rounded-2xl p-6 text-center transition bg-slate-50/50">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-2"></i>
                    <div class="text-xs font-bold text-slate-700">Klik untuk memilih berkas dari komputer</div>
                    <div class="text-[11px] text-slate-400 mt-1">Format PDF, DOCX, XLSX, ZIP. Maksimal 25 MB.</div>
                    <input type="file" name="file_upload" id="quickFileInput" required accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.jpg,.jpeg,.png" class="mt-3 w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-600 file:text-white hover:file:bg-red-700 file:cursor-pointer">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeQuickUpload()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-md transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-check"></i> Simpan & Pasang Berkas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Quick search cards
    function filterMenuCards() {
        const query = document.getElementById('menuSearchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.menu-card');

        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const badge = card.getAttribute('data-badge') || '';
            const type = card.getAttribute('data-type') || '';

            if (title.includes(query) || badge.includes(query) || type.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Filter by type pill
    function setFilterType(type) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
            btn.classList.add('hover:text-slate-900');
        });

        const activeBtn = document.querySelector(`.filter-btn[data-filter="${type}"]`);
        if (activeBtn) {
            activeBtn.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
            activeBtn.classList.remove('hover:text-slate-900');
        }

        const cards = document.querySelectorAll('.menu-card');
        cards.forEach(card => {
            const cardType = card.getAttribute('data-type');
            if (type === 'all') {
                card.style.display = 'flex';
            } else if (type === 'file') {
                card.style.display = (cardType === 'file') ? 'flex' : 'none';
            } else if (type === 'link') {
                card.style.display = (cardType === 'link' || cardType === 'whatsapp') ? 'flex' : 'none';
            }
        });
    }

    // Switch between Grid and Table View
    function switchView(mode) {
        const grid = document.getElementById('gridContainer');
        const table = document.getElementById('tableContainer');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnTable = document.getElementById('btnViewTable');

        if (mode === 'grid') {
            grid.classList.remove('hidden');
            table.classList.add('hidden');
            btnGrid.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
            btnTable.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
        } else {
            grid.classList.add('hidden');
            table.classList.remove('hidden');
            btnTable.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
            btnGrid.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
        }
    }

    // Quick upload modal logic
    function openQuickUpload(menuId, menuTitle) {
        document.getElementById('quickUploadMenuTitle').textContent = menuTitle;
        document.getElementById('quickUploadForm').action = `/admin/competition-info-menus/${menuId}/quick-upload`;
        document.getElementById('quickFileInput').value = '';
        document.getElementById('quickUploadModal').classList.remove('hidden');
    }

    function closeQuickUpload() {
        document.getElementById('quickUploadModal').classList.add('hidden');
    }

    // Close on backdrop or ESC
    document.getElementById('quickUploadModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeQuickUpload();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeQuickUpload();
    });
</script>
@endsection
