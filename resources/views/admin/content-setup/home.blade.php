@extends('layouts.admin')

@section('title', 'Setup Konten Beranda - ' . ($activeTab === 'pillars' ? 'Pilar Aksi Kemanusiaan' : 'Profil Ekstrakurikuler'))
@section('page_title', 'Setup Konten Halaman Beranda')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pmr-primary transition">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span>Setup Konten</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <a href="{{ route('admin.content-setup.home') }}" class="hover:text-pmr-primary transition">Beranda</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-700 font-semibold">{{ $activeTab === 'pillars' ? 'Pilar Aksi Kemanusiaan' : 'Profil Ekstrakurikuler' }}</span>
            </nav>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-house-laptop text-pmr-primary"></i>
                Setup Konten Beranda
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                {{ $activeTab === 'pillars' ? 'Kelola teks heading, deskripsi, ikon, judul, dan tautan 3 kartu Program Unggulan.' : 'Kelola teks heading (H1 - H5), narasi paragraf, poin keunggulan, tombol aksi, dan unggah foto profil ekstrakurikuler.' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square text-slate-500"></i>
                <span>Lihat di Beranda</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-sm shadow-sm animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold flex-shrink-0">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="flex-grow font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-sm shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="font-bold">Mohon perbaiki kesalahan berikut:</p>
                <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Tab Navigasi Konten Beranda -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200">
        <a href="{{ route('admin.content-setup.home', ['tab' => 'profile']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ $activeTab === 'profile' ? 'bg-pmr-primary text-white shadow-sm shadow-red-950/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <i class="fa-solid fa-shield-heart"></i>
            <span>Profil Ekstrakurikuler</span>
        </a>
        <a href="{{ route('admin.content-setup.home', ['tab' => 'pillars']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ $activeTab === 'pillars' ? 'bg-pmr-primary text-white shadow-sm shadow-red-950/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <i class="fa-solid fa-hand-holding-heart"></i>
            <span>Pilar Aksi Kemanusiaan</span>
        </a>
    </div>

    @if($activeTab === 'profile')
    <!-- ==================== TAB 1: PROFIL EKSTRAKURIKULER ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Kolom Kiri: Formulir Pengaturan (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('admin.content-setup.home.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="formProfile">
                @csrf

                <!-- Card 1: Heading & Teks Narasi -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-lg">
                            <i class="fa-solid fa-heading"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Judul & Narasi Profil Ekstrakurikuler</h3>
                            <p class="text-xs text-slate-500">Sesuaikan teks sub-judul (H5), judul utama (H1/H2), dan teks paragraf.</p>
                        </div>
                    </div>

                    <!-- Sub-Judul / Badge Tagline (H5) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Sub-Judul / Tagline Badge (H5) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-tag"></i>
                                </span>
                                <input type="text" name="tagline" id="input_tagline" 
                                    value="{{ old('tagline', $homeProfile->display_tagline) }}" 
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                    placeholder="Contoh: Profil Ekstrakurikuler" required>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Ikon Badge
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-icons"></i>
                                </span>
                                <input type="text" name="tagline_icon" id="input_tagline_icon" 
                                    value="{{ old('tagline_icon', $homeProfile->display_tagline_icon) }}" 
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                    placeholder="fa-solid fa-shield-heart">
                            </div>
                        </div>
                    </div>

                    <!-- Judul Utama (H1 / H2) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Judul Utama Section (H1 / H2) <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="headline" id="input_headline" rows="2"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-extrabold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition leading-snug" 
                            placeholder="Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi" required>{{ old('headline', $homeProfile->display_headline) }}</textarea>
                    </div>

                    <!-- Deskripsi Narasi Profil (Text Area) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Deskripsi Narasi Profil (Text Area Paragraf) <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="body_text" id="input_body_text" rows="4"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm leading-relaxed text-slate-700 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                            placeholder="Tuliskan profil singkat dan komitmen PMR Wira SMAN 1 Ciawi..." required>{{ old('body_text', $homeProfile->display_body_text) }}</textarea>
                    </div>
                </div>

                <!-- Card 2: Poin Checklist Keunggulan -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Poin Checklist Keunggulan</h3>
                            <p class="text-xs text-slate-500">Tiga poin utama komitmen aksi kemanusiaan yang berikon centang.</p>
                        </div>
                    </div>

                    @php
                        $checklistItems = $homeProfile->display_checklist_items;
                    @endphp

                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-[10px] font-black">1</span>
                                <span>Poin Keunggulan 1</span>
                            </label>
                            <input type="text" name="checklist_items[0]" id="input_checklist_0" 
                                value="{{ old('checklist_items.0', $checklistItems[0] ?? '') }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                placeholder="Poin 1...">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-[10px] font-black">2</span>
                                <span>Poin Keunggulan 2</span>
                            </label>
                            <input type="text" name="checklist_items[1]" id="input_checklist_1" 
                                value="{{ old('checklist_items.1', $checklistItems[1] ?? '') }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                placeholder="Poin 2...">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-[10px] font-black">3</span>
                                <span>Poin Keunggulan 3</span>
                            </label>
                            <input type="text" name="checklist_items[2]" id="input_checklist_2" 
                                value="{{ old('checklist_items.2', $checklistItems[2] ?? '') }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                placeholder="Poin 3...">
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tombol Aksi (CTA Button) -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-arrow-pointer"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Tombol Aksi (Call To Action)</h3>
                            <p class="text-xs text-slate-500">Tautan navigasi tombol di bawah poin checklist.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Teks Label Tombol
                            </label>
                            <input type="text" name="button_text" id="input_button_text" 
                                value="{{ old('button_text', $homeProfile->display_button_text) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                placeholder="Baca Profil Selengkapnya">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Tautan / URL Tujuan
                            </label>
                            <input type="text" name="button_url" id="input_button_url" 
                                value="{{ old('button_url', $homeProfile->display_button_url) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition font-mono text-xs" 
                                placeholder="/tentang-kami">
                        </div>
                    </div>
                </div>

                <!-- Card 4: Upload Foto Profil & Floating Badge -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Foto Profil & Floating Badge Card</h3>
                            <p class="text-xs text-slate-500">Unggah foto booklet profil dan sesuaikan kartu badge melayang.</p>
                        </div>
                    </div>

                    <!-- Upload Gambar -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Unggah Foto / Booklet Profil (Maks. 5 MB)
                        </label>

                        <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100/60 transition cursor-pointer" onclick="document.getElementById('input_image').click();">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-200 border-2 border-white shadow-md flex-shrink-0">
                                <img id="thumb_image" src="{{ $homeProfile->image_url }}" alt="Thumbnail Foto Profil" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-grow text-center sm:text-left space-y-1">
                                <div class="text-xs font-bold text-slate-800 flex items-center justify-center sm:justify-start gap-1.5">
                                    <i class="fa-solid fa-cloud-arrow-up text-pmr-primary"></i>
                                    <span>Klik untuk memilih berkas foto baru</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Mendukung format JPG, PNG, WEBP. Disarankan rasio landscape / 4:3 resolusi tajam.</p>
                                <div class="text-[10px] font-mono text-slate-400 truncate max-w-sm" id="filename_label">
                                    Berkas saat ini: {{ basename($homeProfile->image_path) }}
                                </div>
                            </div>
                        </div>

                        <input type="file" name="image" id="input_image" accept="image/*" class="hidden">
                    </div>

                    <!-- Floating Badge Card Settings -->
                    <div class="pt-3 border-t border-slate-100">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-1.5">
                            <i class="fa-solid fa-certificate text-amber-500"></i>
                            <span>Pengaturan Floating Badge Card (Kartu Melayang)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    Judul Badge
                                </label>
                                <input type="text" name="badge_text" id="input_badge_text" 
                                    value="{{ old('badge_text', $homeProfile->display_badge_text) }}" 
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                    placeholder="Terakreditasi A">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    Subteks / Keterangan Badge
                                </label>
                                <input type="text" name="badge_subtext" id="input_badge_subtext" 
                                    value="{{ old('badge_subtext', $homeProfile->display_badge_subtext) }}" 
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                    placeholder="Unit PMR Wira Teladan Kab. Bogor">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Simpan & Reset -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-sm text-white bg-pmr-primary hover:bg-pmr-dark transition shadow-lg shadow-red-950/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>

                    <button type="button" onclick="confirmResetProfile()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold text-xs text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-rose-600 transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Kembalikan ke Nilai Bawaan</span>
                    </button>
                </div>
            </form>

            <!-- Hidden Form Reset Profile -->
            <form id="formResetProfile" action="{{ route('admin.content-setup.home.profile.reset') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

        <!-- Kolom Kanan: Pratinjau Langsung (Live Preview) (5 Cols) -->
        <div class="lg:col-span-5 sticky top-20 space-y-4">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Pratinjau Langsung (Live Preview)</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                        Halaman Beranda
                    </span>
                </div>

                <!-- Simulation Section Card -->
                <div class="p-4 sm:p-6 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-5 text-left overflow-hidden">
                    <div>
                        <!-- Tagline Badge -->
                        <div class="inline-flex items-center gap-2 text-pmr-primary font-bold text-[11px] uppercase tracking-widest mb-2" id="preview_tagline_wrapper">
                            <i id="preview_tagline_icon" class="{{ $homeProfile->display_tagline_icon }}"></i>
                            <span id="preview_tagline">{{ $homeProfile->display_tagline }}</span>
                        </div>

                        <!-- Judul Utama (H2) -->
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight leading-snug mb-3" id="preview_headline">
                            {{ $homeProfile->display_headline }}
                        </h2>

                        <!-- Paragraf Deskripsi -->
                        <p class="text-slate-600 leading-relaxed text-xs mb-4" id="preview_body_text">
                            {{ $homeProfile->display_body_text }}
                        </p>

                        <!-- Poin Checklist -->
                        <div class="space-y-2 mb-5">
                            <div class="flex items-start gap-2.5" id="preview_chk_wrap_0">
                                <div class="w-5 h-5 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-[10px] mt-0.5 flex-shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <span class="text-xs text-slate-700 font-medium" id="preview_chk_0">{{ $checklistItems[0] ?? '' }}</span>
                            </div>
                            <div class="flex items-start gap-2.5" id="preview_chk_wrap_1">
                                <div class="w-5 h-5 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-[10px] mt-0.5 flex-shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <span class="text-xs text-slate-700 font-medium" id="preview_chk_1">{{ $checklistItems[1] ?? '' }}</span>
                            </div>
                            <div class="flex items-start gap-2.5" id="preview_chk_wrap_2">
                                <div class="w-5 h-5 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-[10px] mt-0.5 flex-shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <span class="text-xs text-slate-700 font-medium" id="preview_chk_2">{{ $checklistItems[2] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- CTA Button -->
                        <div class="mb-6">
                            <span class="inline-flex items-center gap-2 bg-pmr-primary text-white font-bold text-[11px] uppercase tracking-wider px-4 py-2.5 rounded-xl shadow-sm cursor-pointer">
                                <span id="preview_button_text">{{ $homeProfile->display_button_text }}</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Gambar & Floating Badge Card -->
                    <div class="relative pt-2">
                        <div class="rounded-2xl overflow-hidden shadow-lg border-2 border-white bg-slate-200">
                            <img id="preview_img" src="{{ $homeProfile->image_url }}" alt="Preview Foto Profil" class="w-full h-44 object-cover">
                        </div>

                        <div class="absolute -bottom-3 -left-2 bg-white p-3 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-2.5 max-w-[85%]">
                            <div class="w-8 h-8 rounded-full bg-pmr-primary text-white flex items-center justify-center text-sm flex-shrink-0">
                                <i class="fa-solid fa-certificate"></i>
                            </div>
                            <div class="truncate">
                                <div class="font-extrabold text-slate-800 text-[11px] truncate" id="preview_badge_text">{{ $homeProfile->display_badge_text }}</div>
                                <div class="text-[9px] text-slate-500 truncate" id="preview_badge_subtext">{{ $homeProfile->display_badge_subtext }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-amber-800 text-[11px] flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-amber-500"></i>
                    <span>Tampilan website publik akan diperbarui secara otomatis setelah menekan tombol <strong>Simpan Perubahan</strong>.</span>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- ==================== TAB 2: PILAR AKSI KEMANUSIAAN ==================== -->
    @php
        $programs = $homePillars->display_programs;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Kolom Kiri: Formulir Pengaturan (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('admin.content-setup.home.pillars.update') }}" method="POST" class="space-y-6" id="formPillars">
                @csrf

                <!-- Header Section Pengaturan -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-lg">
                            <i class="fa-solid fa-heading"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Judul & Narasi Pilar Aksi Kemanusiaan</h3>
                            <p class="text-xs text-slate-500">Pengaturan teks tagline, judul utama section, dan deskripsi ringkas.</p>
                        </div>
                    </div>

                    <!-- Tagline (H5) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Tagline / Sub-Judul Atas (H5) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="tagline" id="input_pillar_tagline" 
                            value="{{ old('tagline', $homePillars->display_tagline) }}" 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                            placeholder="Pilar Aksi Kemanusiaan" required>
                    </div>

                    <!-- Judul Utama Section (H2) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Judul Utama Section (H1 / H2) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="headline" id="input_pillar_headline" 
                            value="{{ old('headline', $homePillars->display_headline) }}" 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-extrabold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                            placeholder="Program Unggulan PMR Wira" required>
                    </div>

                    <!-- Deskripsi Ringkas Subtitle -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Deskripsi Ringkas Layanan (Text Area) <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="body_text" id="input_pillar_body_text" rows="2"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm leading-relaxed text-slate-700 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                            placeholder="Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami..." required>{{ old('body_text', $homePillars->display_body_text) }}</textarea>
                    </div>
                </div>

                <!-- 3 Kartu Program Unggulan -->
                <div class="space-y-5">
                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-pmr-primary"></span>
                            <h3 class="text-sm font-black uppercase tracking-wider text-slate-800">3 Kartu Bidang Layanan / Program</h3>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400">Total 3 Kartu</span>
                    </div>

                    @foreach([0, 1, 2] as $idx)
                    @php
                        $prog = $programs[$idx] ?? [
                            'icon' => 'fa-solid fa-star',
                            'title' => 'Program ' . ($idx + 1),
                            'description' => '',
                            'button_text' => 'Lihat Selengkapnya',
                            'button_url' => '#',
                        ];
                    @endphp
                    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg font-black border border-red-100">
                                    <i class="{{ $prog['icon'] ?? 'fa-solid fa-star' }}" id="preview_card_icon_indicator_{{ $idx }}"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-800 text-sm">Kartu Program {{ $idx + 1 }}</h4>
                                    <span class="text-[11px] text-slate-500" id="card_title_label_{{ $idx }}">{{ $prog['title'] }}</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-mono bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-bold">Kolom {{ $idx + 1 }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Ikon Program -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    Ikon FontAwesome
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-icons text-xs"></i>
                                    </span>
                                    <input type="text" name="cards[{{ $idx }}][icon]" id="input_card_icon_{{ $idx }}" 
                                        value="{{ old("cards.{$idx}.icon", $prog['icon']) }}" 
                                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                        placeholder="fa-solid fa-droplet">
                                </div>
                            </div>

                            <!-- Judul Program -->
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    Nama / Judul Program <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="cards[{{ $idx }}][title]" id="input_card_title_{{ $idx }}" 
                                    value="{{ old("cards.{$idx}.title", $prog['title']) }}" 
                                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                    placeholder="Contoh: Donor Darah Sukarela" required>
                            </div>
                        </div>

                        <!-- Deskripsi Program -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Deskripsi Program <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="cards[{{ $idx }}][description]" id="input_card_desc_{{ $idx }}" rows="3"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 leading-relaxed focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                placeholder="Uraian ringkas kegiatan program..." required>{{ old("cards.{$idx}.description", $prog['description']) }}</textarea>
                        </div>

                        <!-- Tombol CTA Kartu -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    Teks Tautan Tombol
                                </label>
                                <input type="text" name="cards[{{ $idx }}][button_text]" id="input_card_btn_text_{{ $idx }}" 
                                    value="{{ old("cards.{$idx}.button_text", $prog['button_text']) }}" 
                                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                    placeholder="Cek Stok & Jadwal">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    URL / Tautan Tujuan
                                </label>
                                <input type="text" name="cards[{{ $idx }}][button_url]" id="input_card_btn_url_{{ $idx }}" 
                                    value="{{ old("cards.{$idx}.button_url", $prog['button_url']) }}" 
                                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" 
                                    placeholder="/donor-darah">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Tombol Aksi Simpan & Reset -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-sm text-white bg-pmr-primary hover:bg-pmr-dark transition shadow-lg shadow-red-950/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>

                    <button type="button" onclick="confirmResetPillars()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold text-xs text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-rose-600 transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Kembalikan ke Nilai Bawaan</span>
                    </button>
                </div>
            </form>

            <!-- Hidden Form Reset Pillars -->
            <form id="formResetPillars" action="{{ route('admin.content-setup.home.pillars.reset') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

        <!-- Kolom Kanan: Pratinjau Langsung (Live Preview) (5 Cols) -->
        <div class="lg:col-span-5 sticky top-20 space-y-4">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Pratinjau Langsung (Live Preview)</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                        Halaman Beranda
                    </span>
                </div>

                <!-- Simulation Section Card Container (Mencerminkan Screenshot Pengguna) -->
                <div class="p-4 sm:p-6 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-5 text-center overflow-hidden">
                    <!-- Header Preview -->
                    <div>
                        <div class="text-pmr-primary font-bold text-[11px] uppercase tracking-widest mb-1.5" id="preview_pillar_tagline">
                            {{ $homePillars->display_tagline }}
                        </div>
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight leading-snug" id="preview_pillar_headline">
                            {{ $homePillars->display_headline }}
                        </h2>
                        <p class="text-slate-600 text-xs mt-1.5 leading-relaxed" id="preview_pillar_body_text">
                            {{ $homePillars->display_body_text }}
                        </p>
                    </div>

                    <!-- 3 Kartu Simulation Preview -->
                    <div class="space-y-3.5 text-left pt-2">
                        @foreach([0, 1, 2] as $idx)
                        @php
                            $prog = $programs[$idx] ?? [];
                        @endphp
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex flex-col transition">
                            <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg mb-3 shadow-inner">
                                <i class="{{ $prog['icon'] ?? 'fa-solid fa-star' }}" id="sim_icon_{{ $idx }}"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 mb-1.5" id="sim_title_{{ $idx }}">{{ $prog['title'] ?? '' }}</h3>
                            <p class="text-slate-600 text-[11px] leading-relaxed mb-3 flex-grow" id="sim_desc_{{ $idx }}">
                                {{ $prog['description'] ?? '' }}
                            </p>
                            <span class="text-pmr-primary font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 cursor-pointer">
                                <span id="sim_btn_{{ $idx }}">{{ $prog['button_text'] ?? 'Lihat Selengkapnya' }}</span>
                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-amber-800 text-[11px] flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-amber-500"></i>
                    <span>Tampilan website publik akan diperbarui secara otomatis setelah menekan tombol <strong>Simpan Perubahan</strong>.</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    // Tab 1: Realtime Live Preview Profil Ekstrakurikuler
    const inputTagline = document.getElementById('input_tagline');
    const inputTaglineIcon = document.getElementById('input_tagline_icon');
    const inputHeadline = document.getElementById('input_headline');
    const inputBodyText = document.getElementById('input_body_text');
    const inputChk0 = document.getElementById('input_checklist_0');
    const inputChk1 = document.getElementById('input_checklist_1');
    const inputChk2 = document.getElementById('input_checklist_2');
    const inputBtnText = document.getElementById('input_button_text');
    const inputBadgeText = document.getElementById('input_badge_text');
    const inputBadgeSubtext = document.getElementById('input_badge_subtext');
    const inputImage = document.getElementById('input_image');

    const previewTagline = document.getElementById('preview_tagline');
    const previewTaglineIcon = document.getElementById('preview_tagline_icon');
    const previewHeadline = document.getElementById('preview_headline');
    const previewBodyText = document.getElementById('preview_body_text');
    const previewChk0 = document.getElementById('preview_chk_0');
    const previewChk1 = document.getElementById('preview_chk_1');
    const previewChk2 = document.getElementById('preview_chk_2');
    const previewBtnText = document.getElementById('preview_button_text');
    const previewBadgeText = document.getElementById('preview_badge_text');
    const previewBadgeSubtext = document.getElementById('preview_badge_subtext');
    const previewImg = document.getElementById('preview_img');
    const thumbImg = document.getElementById('thumb_image');
    const filenameLabel = document.getElementById('filename_label');

    if (inputTagline) {
        inputTagline.addEventListener('input', (e) => {
            previewTagline.textContent = e.target.value.trim() || 'Profil Ekstrakurikuler';
        });
    }

    if (inputTaglineIcon) {
        inputTaglineIcon.addEventListener('input', (e) => {
            previewTaglineIcon.className = e.target.value.trim() || 'fa-solid fa-shield-heart';
        });
    }

    if (inputHeadline) {
        inputHeadline.addEventListener('input', (e) => {
            previewHeadline.textContent = e.target.value.trim() || 'Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi';
        });
    }

    if (inputBodyText) {
        inputBodyText.addEventListener('input', (e) => {
            previewBodyText.textContent = e.target.value.trim() || 'Palang Merah Remaja (PMR) Wira SMAN 1 Ciawi adalah wadah pembinaan dan pengembangan anggota remaja PMI tingkat SMA...';
        });
    }

    if (inputChk0) {
        inputChk0.addEventListener('input', (e) => {
            previewChk0.textContent = e.target.value.trim() || 'Poin 1...';
        });
    }

    if (inputChk1) {
        inputChk1.addEventListener('input', (e) => {
            previewChk1.textContent = e.target.value.trim() || 'Poin 2...';
        });
    }

    if (inputChk2) {
        inputChk2.addEventListener('input', (e) => {
            previewChk2.textContent = e.target.value.trim() || 'Poin 3...';
        });
    }

    if (inputBtnText) {
        inputBtnText.addEventListener('input', (e) => {
            previewBtnText.textContent = e.target.value.trim() || 'Baca Profil Selengkapnya';
        });
    }

    if (inputBadgeText) {
        inputBadgeText.addEventListener('input', (e) => {
            previewBadgeText.textContent = e.target.value.trim() || 'Terakreditasi A';
        });
    }

    if (inputBadgeSubtext) {
        inputBadgeSubtext.addEventListener('input', (e) => {
            previewBadgeSubtext.textContent = e.target.value.trim() || 'Unit PMR Wira Teladan Kab. Bogor';
        });
    }

    if (inputImage) {
        inputImage.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                filenameLabel.textContent = 'Berkas dipilih: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    thumbImg.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function confirmResetProfile() {
        if (confirm('Apakah Anda yakin ingin mengembalikan seluruh konten Profil Ekstrakurikuler ke setelan bawaan sistem?')) {
            document.getElementById('formResetProfile').submit();
        }
    }

    // Tab 2: Realtime Live Preview Pilar Aksi Kemanusiaan
    const inputPillarTagline = document.getElementById('input_pillar_tagline');
    const inputPillarHeadline = document.getElementById('input_pillar_headline');
    const inputPillarBodyText = document.getElementById('input_pillar_body_text');

    const previewPillarTagline = document.getElementById('preview_pillar_tagline');
    const previewPillarHeadline = document.getElementById('preview_pillar_headline');
    const previewPillarBodyText = document.getElementById('preview_pillar_body_text');

    if (inputPillarTagline) {
        inputPillarTagline.addEventListener('input', (e) => {
            previewPillarTagline.textContent = e.target.value.trim() || 'Pilar Aksi Kemanusiaan';
        });
    }

    if (inputPillarHeadline) {
        inputPillarHeadline.addEventListener('input', (e) => {
            previewPillarHeadline.textContent = e.target.value.trim() || 'Program Unggulan PMR Wira';
        });
    }

    if (inputPillarBodyText) {
        inputPillarBodyText.addEventListener('input', (e) => {
            previewPillarBodyText.textContent = e.target.value.trim() || 'Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami di lingkungan SMAN 1 Ciawi.';
        });
    }

    // Synchronization for 3 cards
    [0, 1, 2].forEach(idx => {
        const inputIcon = document.getElementById(`input_card_icon_${idx}`);
        const inputTitle = document.getElementById(`input_card_title_${idx}`);
        const inputDesc = document.getElementById(`input_card_desc_${idx}`);
        const inputBtn = document.getElementById(`input_card_btn_text_${idx}`);

        const simIcon = document.getElementById(`sim_icon_${idx}`);
        const simTitle = document.getElementById(`sim_title_${idx}`);
        const simDesc = document.getElementById(`sim_desc_${idx}`);
        const simBtn = document.getElementById(`sim_btn_${idx}`);
        const iconIndicator = document.getElementById(`preview_card_icon_indicator_${idx}`);
        const titleLabel = document.getElementById(`card_title_label_${idx}`);

        if (inputIcon) {
            inputIcon.addEventListener('input', (e) => {
                const iconClass = e.target.value.trim() || 'fa-solid fa-star';
                if (simIcon) simIcon.className = iconClass;
                if (iconIndicator) iconIndicator.className = iconClass;
            });
        }

        if (inputTitle) {
            inputTitle.addEventListener('input', (e) => {
                const val = e.target.value.trim() || `Program ${idx + 1}`;
                if (simTitle) simTitle.textContent = val;
                if (titleLabel) titleLabel.textContent = val;
            });
        }

        if (inputDesc) {
            inputDesc.addEventListener('input', (e) => {
                if (simDesc) simDesc.textContent = e.target.value.trim() || '';
            });
        }

        if (inputBtn) {
            inputBtn.addEventListener('input', (e) => {
                if (simBtn) simBtn.textContent = e.target.value.trim() || 'Lihat Selengkapnya';
            });
        }
    });

    function confirmResetPillars() {
        if (confirm('Apakah Anda yakin ingin mengembalikan seluruh konten Pilar Aksi Kemanusiaan ke setelan bawaan sistem?')) {
            document.getElementById('formResetPillars').submit();
        }
    }
</script>
@endsection
