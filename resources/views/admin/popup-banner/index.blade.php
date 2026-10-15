@extends('layouts.admin')

@section('title', 'Kelola Popup Banner Iklan & Pengumuman')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Status Bar (Sesuai Mockup) -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pmr-primary">Backoffice</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-700">Kelola Popup Banner</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-bullhorn text-amber-500"></i>
                <span>Popup Banner Iklan & Pengumuman</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola banner pop-up beranda dengan penjadwalan otomatis dan tombol tautan informasi.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($currentBanner && $currentBanner->status_label['text'] === 'Aktif Tayang')
                <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 px-3.5 py-1.5 rounded-full text-xs font-extrabold shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Status: 1 Banner Sedang Aktif Tayang</span>
                </div>
            @else
                <div class="inline-flex items-center gap-2 bg-slate-100 text-slate-600 border border-slate-300 px-3.5 py-1.5 rounded-full text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>Status: Tidak Ada Banner Aktif</span>
                </div>
            @endif

            <a href="{{ route('home') }}?show_popup=1" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-stone-800 text-white text-xs font-bold transition shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                <span>Lihat Beranda Publik</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-xs animate-in fade-in">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg flex-shrink-0"></i>
            <div class="text-xs sm:text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl shadow-xs">
            <div class="font-bold text-xs uppercase tracking-wider mb-1 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-exclamation"></i> Terjadi Kesalahan Input:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main 2-Columns Grid Layout (Sesuai Mockup) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Form Pengaturan Popup Banner (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-pmr-primary"></i>
                    <span>Form Pengaturan Popup Banner</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Silakan unggah gambar banner dan atur masa berlaku tayang.
                </p>
            </div>

            <form action="{{ route('admin.popup-banner.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5" id="popup-form">
                @csrf
                <input type="hidden" name="id" id="banner_id" value="{{ $currentBanner->id ?? '' }}">

                <!-- Judul Pengumuman / Iklan -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Judul Pengumuman / Iklan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="input_title" required
                        value="{{ old('title', $currentBanner->title ?? '') }}"
                        placeholder="Contoh: Ajang Lomba Ketangkasan PMR Wira Jabar 2026"
                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-pmr-primary/20 text-sm font-semibold transition"
                        oninput="updateLivePreview()">
                </div>

                <!-- Upload Gambar Banner Drag & Drop -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Gambar Banner Promosi <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="relative border-2 border-dashed border-slate-300 hover:border-pmr-primary rounded-3xl p-6 text-center bg-slate-50/60 hover:bg-red-50/20 transition cursor-pointer group" onclick="document.getElementById('banner_file').click()">
                        <input type="file" name="image" id="banner_file" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="previewSelectedImage(this)">
                        
                        <div class="flex flex-col items-center justify-center space-y-2" id="upload_placeholder">
                            <div class="w-14 h-14 rounded-2xl bg-white text-pmr-primary flex items-center justify-center text-2xl shadow-sm border border-slate-200 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs font-bold text-slate-700">
                                Klik untuk memilih berkas atau seret gambar banner ke sini
                            </div>
                            <div class="text-[11px] text-slate-400">
                                Format: JPG, PNG, WebP (Maksimal 5 MB). Rekomendasi rasio: Persegi (1:1) atau Portrait (4:5).
                            </div>
                        </div>

                        <!-- Mini Thumbnail Preview jika sudah ada file -->
                        <div id="image_preview_box" class="{{ !empty($currentBanner?->image_path) ? '' : 'hidden' }} mt-2">
                            <div class="relative inline-block rounded-2xl overflow-hidden border border-slate-200 bg-stone-900 shadow-sm max-h-48">
                                <img id="preview_thumb" src="{{ $currentBanner?->image_url ?? '' }}" alt="Preview" class="max-h-48 w-auto object-contain mx-auto">
                                <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold">
                                    <i class="fa-solid fa-arrow-rotate-right mr-1.5"></i> Ganti Gambar
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Target URL Link -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Target Tautan / URL Link <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-link text-xs"></i>
                        </div>
                        <input type="url" name="target_url" id="input_target_url"
                            value="{{ old('target_url', $currentBanner->target_url ?? '') }}"
                            placeholder="https://pmrwirasmanic.or.id/lomba/sua-bhakti-berkarya"
                            class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-pmr-primary/20 text-sm font-medium transition"
                            oninput="updateLivePreview()">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Bila diisi, tombol aksi Info dan klik banner akan membuka tautan ini.
                    </p>
                </div>

                <!-- Teks Tombol Aksi & Opsi Tab Baru -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                            Teks Tombol Aksi
                        </label>
                        <input type="text" name="button_text" id="input_button_text"
                            value="{{ old('button_text', $currentBanner->button_text ?? 'Info Lebih Lanjut') }}"
                            placeholder="Contoh: Info Lebih Lanjut / Daftar Sekarang"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-pmr-primary/20 text-xs font-semibold transition"
                            oninput="updateLivePreview()">
                    </div>

                    <div class="flex items-center pt-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="open_in_new_tab" value="1" {{ old('open_in_new_tab', $currentBanner->open_in_new_tab ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded text-pmr-primary focus:ring-pmr-primary border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Buka link di tab baru (_blank)</span>
                        </label>
                    </div>
                </div>

                <!-- Pengaturan Jadwal Tayang (Start & End Date) -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Jadwal Masa Berlaku Iklan <span class="text-slate-400 font-normal">(Auto-Scheduler)</span>
                    </label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-[11px] font-bold text-slate-500 mb-1">Mulai Tayang:</span>
                            <div class="relative">
                                <input type="datetime-local" name="start_date" id="input_start_date"
                                    value="{{ old('start_date', $currentBanner?->start_date ? $currentBanner->start_date->format('Y-m-d\TH:i') : '') }}"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-pmr-primary/20 text-xs font-medium transition"
                                    onchange="updateLivePreview()">
                            </div>
                            <span class="text-[10px] text-slate-400">Kosongkan jika ingin langsung tayang.</span>
                        </div>

                        <div>
                            <span class="block text-[11px] font-bold text-slate-500 mb-1">Selesai Tayang:</span>
                            <div class="relative">
                                <input type="datetime-local" name="end_date" id="input_end_date"
                                    value="{{ old('end_date', $currentBanner?->end_date ? $currentBanner->end_date->format('Y-m-d\TH:i') : '') }}"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-pmr-primary/20 text-xs font-medium transition"
                                    onchange="updateLivePreview()">
                            </div>
                            <span class="text-[10px] text-slate-400">Otomatis nonaktif setelah tanggal ini.</span>
                        </div>
                    </div>
                </div>

                <!-- Toggle Switch: Aktifkan Popup di Beranda -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                            Aktifkan Popup di Halaman Beranda
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            Jika diaktifkan, banner akan tampil di beranda sesuai jadwal masa berlaku.
                        </div>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" id="input_is_active" value="1" {{ old('is_active', $currentBanner->is_active ?? true) ? 'checked' : '' }} class="sr-only peer" onchange="updateLivePreview()">
                        <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <button type="submit" class="px-6 py-3 rounded-2xl bg-pmr-primary hover:bg-pmr-dark text-white text-xs font-black uppercase tracking-wider transition shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan & Publikasikan Popup</span>
                    </button>

                    <button type="button" onclick="resetFormToNew()" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Reset / Tambah Banner Baru
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column: Pratinjau Langsung (Live Screen Preview) & Statistik Banner (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Live Screen Preview Box (Sesuai Mockup) -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-laptop text-blue-500"></i>
                        <span>Pratinjau Langsung (Live Preview)</span>
                    </h3>
                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold">
                        Skala Simulasi
                    </span>
                </div>

                <!-- Laptop / Desktop Mockup Frame -->
                <div class="rounded-2xl overflow-hidden bg-stone-900 border border-slate-800 shadow-xl p-3 sm:p-4 text-center relative select-none">
                    <!-- Fake Top Browser Bar -->
                    <div class="flex items-center gap-1.5 mb-3 px-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500/80"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/80"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500/80"></span>
                        <span class="text-[9px] text-stone-400 font-mono ml-2 truncate">pmrwirasmanic.or.id</span>
                    </div>

                    <!-- Inner Preview Content: Simulated Popup on top of dark backdrop -->
                    <div class="relative bg-stone-950/90 rounded-xl p-3 border border-white/10 overflow-hidden shadow-2xl">
                        
                        <!-- Mini Close Button -->
                        <div class="absolute top-2 right-2 w-6 h-6 rounded-full bg-white/20 text-white flex items-center justify-center text-[10px]">
                            <i class="fa-solid fa-xmark"></i>
                        </div>

                        <!-- Mini Badge -->
                        <div class="inline-block bg-pmr-primary text-white text-[8px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full mb-2 border border-white/20">
                            Pengumuman Resmi
                        </div>

                        <!-- Banner Image Preview -->
                        <div class="rounded-lg overflow-hidden bg-stone-800 border border-white/10 mb-2.5 max-h-40 flex items-center justify-center">
                            <img id="live_preview_img" src="{{ $currentBanner?->image_url ?? 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80' }}" alt="Banner Preview" class="w-full h-auto object-cover max-h-40">
                        </div>

                        <!-- Judul Preview -->
                        <div id="live_preview_title" class="text-xs font-black text-white leading-tight mb-2 truncate px-1">
                            {{ $currentBanner->title ?? 'Ajang Lomba Ketangkasan PMR Wira Jabar 2026' }}
                        </div>

                        <!-- Action Buttons in Preview: Sesuai Permintaan User (Info Lebih Lanjut & Lewati/Tutup) -->
                        <div class="flex items-center justify-center gap-2 pt-1">
                            <div id="live_preview_btn_info" class="px-3 py-1.5 rounded-lg bg-pmr-primary text-white text-[10px] font-bold shadow-xs">
                                <span id="live_btn_text">{{ $currentBanner->button_text ?? 'Info Lebih Lanjut' }}</span> &rarr;
                            </div>
                            <div class="px-3 py-1.5 rounded-lg bg-white/15 text-stone-300 text-[10px] font-medium border border-white/20">
                                Lewati & Tutup
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-[11px] text-slate-500 italic text-center">
                    Tampilan ini mensimulasikan popup saat pengunjung membuka beranda.
                </div>
            </div>

            <!-- 3 KPI Statistik Metrik (Sesuai Mockup) -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">
                    Statistik Banner & Efektivitas
                </h3>

                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="text-xs font-bold text-slate-500 uppercase">Total Tayang</div>
                        <div class="text-2xl font-black text-slate-900 mt-1">
                            {{ number_format($totalTayang, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80">
                        <div class="text-xs font-bold text-blue-600 uppercase">Total Klik</div>
                        <div class="text-2xl font-black text-blue-700 mt-1">
                            {{ number_format($totalKlik, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80">
                        <div class="text-xs font-bold text-amber-600 uppercase">CTR (Rasio)</div>
                        <div class="text-2xl font-black text-amber-700 mt-1">
                            {{ $avgCtr }}%
                        </div>
                    </div>
                </div>

                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 justify-center bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                    <i class="fa-solid fa-clock-rotate-left text-pmr-primary"></i>
                    <span>Otomatis Nonaktif Setelah Tanggal Selesai Berlalu.</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Riwayat Seluruh Banner yang Pernah Dibuat -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-slate-600"></i>
                    <span>Riwayat & Daftar Popup Banner</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Daftar seluruh banner promosi yang pernah dibuat di sistem.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Banner</th>
                        <th class="py-3 px-4">Judul Pengumuman</th>
                        <th class="py-3 px-4">Jadwal Tayang</th>
                        <th class="py-3 px-4">Target Tautan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Tayang / Klik</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($banners as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4">
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-stone-900 border border-slate-200 flex-shrink-0">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                <div class="line-clamp-2 max-w-xs">{{ $item->title }}</div>
                                <div class="text-[10px] text-slate-400 font-normal">Teks Tombol: "{{ $item->button_text }}"</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                <div><strong class="text-slate-700">Mulai:</strong> {{ $item->start_date ? $item->start_date->translatedFormat('d M Y, H:i') : 'Langsung' }}</div>
                                <div><strong class="text-slate-700">Selesai:</strong> {{ $item->end_date ? $item->end_date->translatedFormat('d M Y, H:i') : 'Tanpa batas' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->target_url)
                                    <a href="{{ $item->target_url }}" target="_blank" class="text-pmr-primary hover:underline line-clamp-1 max-w-[180px] flex items-center gap-1 font-semibold">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        <span>{{ $item->target_url }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Tanpa Link (Hanya Info)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php $label = $item->status_label; @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $label['class'] }}">
                                    <i class="{{ $label['icon'] }} text-[9px]"></i>
                                    <span>{{ $label['text'] }}</span>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold">
                                <div>{{ number_format($item->impressions_count, 0, ',', '.') }} tayang</div>
                                <div class="text-[10px] text-blue-600">{{ number_format($item->clicks_count, 0, ',', '.') }} klik ({{ $item->ctr }}%)</div>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Toggle Status -->
                                    <form action="{{ route('admin.popup-banner.toggle', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-xl {{ $item->is_active ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }} transition" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fa-solid {{ $item->is_active ? 'fa-pause' : 'fa-play' }} text-xs"></i>
                                        </button>
                                    </form>

                                    <!-- Edit / Load into Form -->
                                    <button type="button" onclick="loadBannerToEdit({{ json_encode([
                                        'id' => $item->id,
                                        'title' => $item->title,
                                        'image_url' => $item->image_url,
                                        'target_url' => $item->target_url,
                                        'button_text' => $item->button_text,
                                        'open_in_new_tab' => $item->open_in_new_tab,
                                        'start_date' => $item->start_date ? $item->start_date->format('Y-m-d\TH:i') : '',
                                        'end_date' => $item->end_date ? $item->end_date->format('Y-m-d\TH:i') : '',
                                        'is_active' => $item->is_active,
                                    ]) }})" class="p-2 rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200 transition" title="Edit Data Ini">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    <!-- Hapus -->
                                    <form action="{{ route('admin.popup-banner.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus popup banner ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition" title="Hapus Banner">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada riwayat banner popup. Silakan gunakan form di atas untuk membuat banner pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
            <div class="pt-4 border-t border-slate-100">
                {{ $banners->links() }}
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    function previewSelectedImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview_thumb').src = e.target.result;
                document.getElementById('image_preview_box').classList.remove('hidden');
                document.getElementById('live_preview_img').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updateLivePreview() {
        const title = document.getElementById('input_title').value;
        const buttonText = document.getElementById('input_button_text').value || 'Info Lebih Lanjut';
        const targetUrl = document.getElementById('input_target_url').value;

        document.getElementById('live_preview_title').textContent = title || 'Judul Pengumuman Banner';
        document.getElementById('live_btn_text').textContent = buttonText;

        const infoBtn = document.getElementById('live_preview_btn_info');
        if (!targetUrl || targetUrl.trim() === '') {
            infoBtn.style.display = 'none';
        } else {
            infoBtn.style.display = 'inline-block';
        }
    }

    function loadBannerToEdit(data) {
        document.getElementById('banner_id').value = data.id;
        document.getElementById('input_title').value = data.title;
        document.getElementById('input_target_url').value = data.target_url || '';
        document.getElementById('input_button_text').value = data.button_text || 'Info Lebih Lanjut';
        document.getElementById('input_start_date').value = data.start_date || '';
        document.getElementById('input_end_date').value = data.end_date || '';
        document.getElementById('input_is_active').checked = Boolean(data.is_active);

        if (data.image_url) {
            document.getElementById('preview_thumb').src = data.image_url;
            document.getElementById('image_preview_box').classList.remove('hidden');
            document.getElementById('live_preview_img').src = data.image_url;
        }

        updateLivePreview();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetFormToNew() {
        document.getElementById('banner_id').value = '';
        document.getElementById('popup-form').reset();
        document.getElementById('image_preview_box').classList.add('hidden');
        document.getElementById('banner_file').value = '';
        updateLivePreview();
    }

    // Run on load
    document.addEventListener('DOMContentLoaded', function() {
        updateLivePreview();
    });
</script>
@endpush
@endsection
