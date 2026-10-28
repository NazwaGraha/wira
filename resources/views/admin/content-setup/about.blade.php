@extends('layouts.admin')

@section('title', 'Setup Konten Tentang Kami')
@section('page_title', 'Setup Konten Halaman Tentang Kami')

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
                <span class="text-slate-700 font-semibold">Tentang Kami</span>
            </nav>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-pmr-primary"></i>
                Setup Konten Tentang Kami
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola konten narasi Profil Mengenal PMR, Tri Bakti, 7 Dasar Prinsip Palang Merah, serta Visi & Misi Organisasi.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url('/tentang-kami') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square text-slate-500"></i>
                <span>Lihat di Halaman Publik</span>
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

    <!-- Tab Navigasi 4 Section -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200">
        <a href="{{ route('admin.content-setup.about', ['tab' => 'intro']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition whitespace-nowrap {{ $activeTab === 'intro' ? 'bg-pmr-primary text-white shadow-sm shadow-red-950/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <i class="fa-solid fa-book-open-reader"></i>
            <span>Mengenal PMR</span>
        </a>
        <a href="{{ route('admin.content-setup.about', ['tab' => 'tri_bakti']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition whitespace-nowrap {{ $activeTab === 'tri_bakti' ? 'bg-pmr-primary text-white shadow-sm shadow-red-950/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <i class="fa-solid fa-hands-holding-child"></i>
            <span>Tri Bakti PMR</span>
        </a>
        <a href="{{ route('admin.content-setup.about', ['tab' => 'principles']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition whitespace-nowrap {{ $activeTab === 'principles' ? 'bg-pmr-primary text-white shadow-sm shadow-red-950/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <i class="fa-solid fa-scale-balanced"></i>
            <span>7 Prinsip Dasar</span>
        </a>
        <a href="{{ route('admin.content-setup.about', ['tab' => 'vision_mission']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition whitespace-nowrap {{ $activeTab === 'vision_mission' ? 'bg-pmr-primary text-white shadow-sm shadow-red-950/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <i class="fa-solid fa-bullseye"></i>
            <span>Visi & Misi</span>
        </a>
    </div>

    @if($activeTab === 'intro')
    <!-- ==================== TAB 1: MENGENAL PMR ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('admin.content-setup.about.intro.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="formIntro">
                @csrf

                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-lg">
                            <i class="fa-solid fa-book-open-reader"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Judul & Definisi Resmi Mengenal PMR</h3>
                            <p class="text-xs text-slate-500">Pengaturan naskah definisi resmi dan cerita dedikasi unit PMR.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tagline / Sub-Judul (H5) <span class="text-rose-500">*</span></label>
                            <input type="text" name="tagline" id="input_intro_tagline" value="{{ old('tagline', $aboutIntro->display_tagline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="MENGENAL PMR" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Ikon Tagline</label>
                            <input type="text" name="tagline_icon" id="input_intro_icon" value="{{ old('tagline_icon', $aboutIntro->display_tagline_icon) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="fa-solid fa-book-open-reader">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Judul Utama Section (H1 / H2) <span class="text-rose-500">*</span></label>
                        <input type="text" name="headline" id="input_intro_headline" value="{{ old('headline', $aboutIntro->display_headline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-extrabold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Profil PMR Wira SMAN 1 Ciawi" required>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Kutipan Definisi Resmi PMR (Quote Box) <span class="text-rose-500">*</span></label>
                        <textarea name="quote_text" id="input_intro_quote" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm leading-relaxed text-slate-800 italic focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Palang Merah Remaja atau PMR adalah..." required>{{ old('quote_text', $aboutIntro->display_quote_text) }}</textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Paragraf Narasi 1 (Peran di Sekolah) <span class="text-rose-500">*</span></label>
                        <textarea name="body_text" id="input_intro_p1" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm leading-relaxed text-slate-700 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Di SMA Negeri 1 Ciawi, unit PMR Wira berperan..." required>{{ old('body_text', $aboutIntro->display_body_text) }}</textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Paragraf Narasi 2 (Komitmen Kepengurusan)</label>
                        <textarea name="body_text_secondary" id="input_intro_p2" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm leading-relaxed text-slate-700 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Masa bakti Ragana Dwi Pantara 2026/2027 berkomitmen...">{{ old('body_text_secondary', $aboutIntro->display_body_text_secondary) }}</textarea>
                    </div>
                </div>

                <!-- Foto & Floating Badge -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Foto Booklet & Floating Badge Card</h3>
                            <p class="text-xs text-slate-500">Unggah foto profil pengenalan dan atur badge melayang.</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Unggah Foto / Booklet Profil (Maks. 5 MB)</label>
                        <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100/60 transition cursor-pointer" onclick="document.getElementById('input_intro_img').click();">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-200 border-2 border-white shadow-md flex-shrink-0">
                                <img id="thumb_intro_img" src="{{ $aboutIntro->image_url }}" alt="Thumbnail Foto Profil" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-grow text-center sm:text-left space-y-1">
                                <div class="text-xs font-bold text-slate-800 flex items-center justify-center sm:justify-start gap-1.5">
                                    <i class="fa-solid fa-cloud-arrow-up text-pmr-primary"></i>
                                    <span>Klik untuk memilih berkas foto baru</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Mendukung format JPG, PNG, WEBP. Disarankan gambar tegak / booklet vertikal.</p>
                                <div class="text-[10px] font-mono text-slate-400 truncate max-w-sm" id="filename_intro_label">
                                    Berkas saat ini: {{ basename($aboutIntro->image_path) }}
                                </div>
                            </div>
                        </div>
                        <input type="file" name="image" id="input_intro_img" accept="image/*" class="hidden">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Judul Badge</label>
                            <input type="text" name="badge_text" id="input_intro_badge_text" value="{{ old('badge_text', $aboutIntro->display_badge_text) }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="PMR Tingkat Wira">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Subteks Badge</label>
                            <input type="text" name="badge_subtext" id="input_intro_badge_subtext" value="{{ old('badge_subtext', $aboutIntro->display_badge_subtext) }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Binaan PMI Kab. Bogor & SMAN 1 Ciawi">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-sm text-white bg-pmr-primary hover:bg-pmr-dark transition shadow-lg shadow-red-950/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                    <button type="button" onclick="confirmResetIntro()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold text-xs text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-rose-600 transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Kembalikan ke Nilai Bawaan</span>
                    </button>
                </div>
            </form>

            <form id="formResetIntro" action="{{ route('admin.content-setup.about.intro.reset') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

        <!-- Kolom Kanan: Pratinjau Langsung (Live Preview) Mengenal PMR -->
        <div class="lg:col-span-5 sticky top-20 space-y-4">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Live Preview: Mengenal PMR</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">Tentang Kami</span>
                </div>

                <div class="p-4 sm:p-6 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-4 text-left overflow-hidden">
                    <div class="inline-flex items-center gap-2 text-pmr-primary font-bold text-[11px] uppercase tracking-widest">
                        <i id="preview_intro_icon" class="{{ $aboutIntro->display_tagline_icon }}"></i>
                        <span id="preview_intro_tagline">{{ $aboutIntro->display_tagline }}</span>
                    </div>

                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight leading-snug" id="preview_intro_headline">
                        {{ $aboutIntro->display_headline }}
                    </h2>

                    <div class="p-3.5 bg-gradient-to-br from-red-50 to-white rounded-2xl border-l-4 border-pmr-primary shadow-xs border border-slate-200/80">
                        <blockquote class="text-xs font-bold text-slate-800 leading-relaxed italic" id="preview_intro_quote">
                            "{{ $aboutIntro->display_quote_text }}"
                        </blockquote>
                    </div>

                    <p class="text-slate-600 leading-relaxed text-xs" id="preview_intro_p1">
                        {{ $aboutIntro->display_body_text }}
                    </p>

                    <p class="text-slate-600 leading-relaxed text-xs" id="preview_intro_p2">
                        {{ $aboutIntro->display_body_text_secondary }}
                    </p>

                    <div class="relative pt-2">
                        <div class="rounded-2xl overflow-hidden shadow-lg border-2 border-white bg-slate-900">
                            <img id="preview_intro_img" src="{{ $aboutIntro->image_url }}" alt="Preview Foto Profil" class="w-full h-40 object-cover">
                        </div>
                        <div class="absolute -bottom-3 -left-2 bg-white p-2.5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-2 max-w-[85%]">
                            <div class="w-7 h-7 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div class="truncate">
                                <div class="font-extrabold text-slate-800 text-[10px] truncate" id="preview_intro_badge_text">{{ $aboutIntro->display_badge_text }}</div>
                                <div class="text-[8px] text-slate-500 truncate" id="preview_intro_badge_subtext">{{ $aboutIntro->display_badge_subtext }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif($activeTab === 'tri_bakti')
    <!-- ==================== TAB 2: TRI BAKTI PMR ==================== -->
    @php
        $triBaktiList = $aboutTriBakti->display_tri_bakti;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('admin.content-setup.about.tri-bakti.update') }}" method="POST" class="space-y-6" id="formTriBakti">
                @csrf

                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-lg">
                            <i class="fa-solid fa-hands-holding-child"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Judul & Narasi Tri Bakti PMR</h3>
                            <p class="text-xs text-slate-500">Pengaturan naskah pembuka tiga pilar kewajiban utama PMR.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tagline / Sub-Judul (H5) <span class="text-rose-500">*</span></label>
                            <input type="text" name="tagline" id="input_tb_tagline" value="{{ old('tagline', $aboutTriBakti->display_tagline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Tri Bakti PMR" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Ikon Tagline</label>
                            <input type="text" name="tagline_icon" id="input_tb_icon" value="{{ old('tagline_icon', $aboutTriBakti->display_tagline_icon) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="fa-solid fa-hands-holding-child">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Judul Utama Section (H1 / H2) <span class="text-rose-500">*</span></label>
                        <input type="text" name="headline" id="input_tb_headline" value="{{ old('headline', $aboutTriBakti->display_headline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-extrabold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Tri Bakti Palang Merah Remaja" required>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Deskripsi Ringkas Pengantar <span class="text-rose-500">*</span></label>
                        <textarea name="body_text" id="input_tb_body_text" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm leading-relaxed text-slate-700 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Tiga pilar kewajiban utama..." required>{{ old('body_text', $aboutTriBakti->display_body_text) }}</textarea>
                    </div>
                </div>

                <!-- 3 Kartu Tri Bakti -->
                <div class="space-y-5">
                    @foreach([0, 1, 2] as $idx)
                    @php
                        $tb = $triBaktiList[$idx] ?? [
                            'sub_badge' => 'Tri Bakti ' . ($idx + 1),
                            'icon' => 'fa-solid fa-hand-holding-heart',
                            'title' => '',
                            'description' => '',
                        ];
                    @endphp
                    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-red-600 text-white flex items-center justify-center text-lg shadow-md">
                                    <i class="{{ $tb['icon'] ?? 'fa-solid fa-hand-holding-heart' }}" id="preview_tb_icon_ind_{{ $idx }}"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-800 text-sm">Pilar {{ $idx + 1 }}</h4>
                                    <span class="text-[11px] text-slate-500" id="tb_title_label_{{ $idx }}">{{ $tb['title'] }}</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-mono bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-bold">Tri Bakti {{ $idx + 1 }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Sub-Badge</label>
                                <input type="text" name="cards[{{ $idx }}][sub_badge]" id="input_tb_subbadge_{{ $idx }}" value="{{ old("cards.{$idx}.sub_badge", $tb['sub_badge']) }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-rose-600 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Tri Bakti {{ $idx + 1 }}">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Ikon FontAwesome</label>
                                <input type="text" name="cards[{{ $idx }}][icon]" id="input_tb_icon_{{ $idx }}" value="{{ old("cards.{$idx}.icon", $tb['icon']) }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="fa-solid fa-hand-holding-heart">
                            </div>
                            <div class="space-y-1.5 sm:col-span-3">
                                <label class="block text-xs font-bold text-slate-700">Judul Pilar Tri Bakti <span class="text-rose-500">*</span></label>
                                <input type="text" name="cards[{{ $idx }}][title]" id="input_tb_title_{{ $idx }}" value="{{ old("cards.{$idx}.title", $tb['title']) }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Judul Pilar..." required>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Deskripsi Pilar <span class="text-rose-500">*</span></label>
                            <textarea name="cards[{{ $idx }}][description]" id="input_tb_desc_{{ $idx }}" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 leading-relaxed focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Uraian kegiatan..." required>{{ old("cards.{$idx}.description", $tb['description']) }}</textarea>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-sm text-white bg-pmr-primary hover:bg-pmr-dark transition shadow-lg shadow-red-950/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                    <button type="button" onclick="confirmResetTriBakti()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold text-xs text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-rose-600 transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Kembalikan ke Nilai Bawaan</span>
                    </button>
                </div>
            </form>

            <form id="formResetTriBakti" action="{{ route('admin.content-setup.about.tri-bakti.reset') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

        <!-- Kolom Kanan: Pratinjau Langsung Tri Bakti (Dark Theme) -->
        <div class="lg:col-span-5 sticky top-20 space-y-4">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Live Preview: Tri Bakti PMR</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-900 text-red-300">Tema Gelap</span>
                </div>

                <!-- Simulation Section Tri Bakti Card (Dark Theme Sesuai Gambar User) -->
                <div class="p-5 rounded-2xl bg-slate-900 text-white space-y-4 text-center overflow-hidden border border-slate-800 shadow-xl">
                    <div>
                        <div class="inline-flex items-center gap-1.5 bg-red-500/20 text-red-300 font-extrabold text-[10px] px-3 py-1 rounded-full uppercase tracking-widest mb-1.5 border border-red-500/30">
                            <i id="preview_sim_tb_icon" class="{{ $aboutTriBakti->display_tagline_icon }}"></i>
                            <span id="preview_sim_tb_tagline">{{ $aboutTriBakti->display_tagline }}</span>
                        </div>
                        <h2 class="text-base sm:text-lg font-extrabold text-white tracking-tight leading-snug" id="preview_sim_tb_headline">
                            {{ $aboutTriBakti->display_headline }}
                        </h2>
                        <p class="text-slate-400 text-[11px] mt-1 leading-relaxed" id="preview_sim_tb_body">
                            {{ $aboutTriBakti->display_body_text }}
                        </p>
                    </div>

                    <div class="space-y-3 text-left pt-1">
                        @foreach([0, 1, 2] as $idx)
                        @php
                            $tb = $triBaktiList[$idx] ?? [];
                        @endphp
                        <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 shadow-sm flex flex-col transition">
                            <div class="w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center text-lg mb-2 shadow-md">
                                <i class="{{ $tb['icon'] ?? 'fa-solid fa-hand-holding-heart' }}" id="sim_tb_icon_{{ $idx }}"></i>
                            </div>
                            <div class="text-[9px] font-bold text-red-400 uppercase tracking-widest mb-1" id="sim_tb_subbadge_{{ $idx }}">{{ $tb['sub_badge'] ?? ('Tri Bakti ' . ($idx + 1)) }}</div>
                            <h3 class="text-xs font-extrabold text-white mb-1.5" id="sim_tb_title_{{ $idx }}">{{ $tb['title'] ?? '' }}</h3>
                            <p class="text-slate-300 text-[10px] leading-relaxed" id="sim_tb_desc_{{ $idx }}">
                                {{ $tb['description'] ?? '' }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif($activeTab === 'principles')
    <!-- ==================== TAB 3: 7 DASAR PRINSIP PALANG MERAH ==================== -->
    @php
        $principlesList = $aboutPrinciples->display_principles;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('admin.content-setup.about.principles.update') }}" method="POST" class="space-y-6" id="formPrinciples">
                @csrf

                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-lg">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Judul & Narasi 7 Dasar Prinsip</h3>
                            <p class="text-xs text-slate-500">Landasan moral fundamental gerakan palang merah internasional.</p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tagline / Sub-Judul (H5) <span class="text-rose-500">*</span></label>
                        <input type="text" name="tagline" id="input_pr_tagline" value="{{ old('tagline', $aboutPrinciples->display_tagline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Landasan Moral Universal" required>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Judul Utama Section (H1 / H2) <span class="text-rose-500">*</span></label>
                        <input type="text" name="headline" id="input_pr_headline" value="{{ old('headline', $aboutPrinciples->display_headline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-extrabold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="7 Dasar Prinsip Palang Merah" required>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Deskripsi Ringkas Pengantar <span class="text-rose-500">*</span></label>
                        <textarea name="body_text" id="input_pr_body_text" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm leading-relaxed text-slate-700 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Tujuh prinsip fundamental..." required>{{ old('body_text', $aboutPrinciples->display_body_text) }}</textarea>
                    </div>
                </div>

                <!-- 7 Kartu Prinsip -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h4 class="font-extrabold text-slate-800 text-sm">7 Kartu Prinsip Palang Merah</h4>
                        <span class="text-[10px] font-mono bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-bold">7 Prinsip</span>
                    </div>

                    <div class="space-y-4">
                        @foreach([0, 1, 2, 3, 4, 5, 6] as $idx)
                        @php
                            $p = $principlesList[$idx] ?? ['icon' => 'fa-solid fa-heart', 'title' => '', 'subtitle' => ''];
                        @endphp
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center text-xs">
                                        <i class="{{ $p['icon'] ?? 'fa-solid fa-heart' }}" id="pr_icon_ind_{{ $idx }}"></i>
                                    </div>
                                    <span class="text-xs font-black text-slate-800">Prinsip {{ $idx + 1 }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-bold text-slate-600">Ikon FontAwesome</label>
                                    <input type="text" name="cards[{{ $idx }}][icon]" id="input_pr_icon_{{ $idx }}" value="{{ old("cards.{$idx}.icon", $p['icon']) }}" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:ring-2 focus:ring-pmr-primary/20 transition">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-bold text-slate-600">Nama (Indonesia)</label>
                                    <input type="text" name="cards[{{ $idx }}][title]" id="input_pr_title_{{ $idx }}" value="{{ old("cards.{$idx}.title", $p['title']) }}" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-pmr-primary/20 transition" required>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-bold text-slate-600">Istilah (Inggris)</label>
                                    <input type="text" name="cards[{{ $idx }}][subtitle]" id="input_pr_sub_{{ $idx }}" value="{{ old("cards.{$idx}.subtitle", $p['subtitle']) }}" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-2 focus:ring-pmr-primary/20 transition">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-sm text-white bg-pmr-primary hover:bg-pmr-dark transition shadow-lg shadow-red-950/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                    <button type="button" onclick="confirmResetPrinciples()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold text-xs text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-rose-600 transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Kembalikan ke Nilai Bawaan</span>
                    </button>
                </div>
            </form>

            <form id="formResetPrinciples" action="{{ route('admin.content-setup.about.principles.reset') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

        <!-- Kolom Kanan: Pratinjau Langsung 7 Prinsip -->
        <div class="lg:col-span-5 sticky top-20 space-y-4">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Live Preview: 7 Dasar Prinsip</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">Tentang Kami</span>
                </div>

                <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-4 text-center overflow-hidden">
                    <div>
                        <div class="text-pmr-primary font-bold text-[11px] uppercase tracking-widest mb-1" id="preview_sim_pr_tagline">
                            {{ $aboutPrinciples->display_tagline }}
                        </div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight leading-snug" id="preview_sim_pr_headline">
                            {{ $aboutPrinciples->display_headline }}
                        </h2>
                        <p class="text-slate-600 text-[11px] mt-1 leading-relaxed" id="preview_sim_pr_body">
                            {{ $aboutPrinciples->display_body_text }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-1">
                        @foreach([0, 1, 2, 3, 4, 5, 6] as $idx)
                        @php
                            $p = $principlesList[$idx] ?? [];
                        @endphp
                        <div class="bg-white border border-slate-200 p-2.5 rounded-xl text-center flex flex-col items-center shadow-2xs">
                            <div class="w-8 h-8 rounded-full bg-red-50 text-pmr-primary flex items-center justify-center text-sm mb-1.5 shadow-inner">
                                <i class="{{ $p['icon'] ?? 'fa-solid fa-heart' }}" id="sim_pr_icon_{{ $idx }}"></i>
                            </div>
                            <div class="font-extrabold text-[10px] text-slate-800" id="sim_pr_title_{{ $idx }}">{{ $p['title'] ?? '' }}</div>
                            <div class="text-[8px] font-semibold text-slate-500" id="sim_pr_sub_{{ $idx }}">{{ $p['subtitle'] ?? '' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif($activeTab === 'vision_mission')
    <!-- ==================== TAB 4: VISI & MISI KAMI ==================== -->
    @php
        $missionsList = $aboutVisionMission->display_mission_items;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('admin.content-setup.about.vision-mission.update') }}" method="POST" class="space-y-6" id="formVisionMission">
                @csrf

                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-lg">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Visi & Misi Arah Gerak Organisasi</h3>
                            <p class="text-xs text-slate-500">Kelola naskah visi dan 4 pilar misi utama kepengurusan PMR.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tagline / Sub-Judul (H5) <span class="text-rose-500">*</span></label>
                            <input type="text" name="tagline" id="input_vm_tagline" value="{{ old('tagline', $aboutVisionMission->display_tagline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Arah Gerak Organisasi" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Judul Utama Section (H1 / H2) <span class="text-rose-500">*</span></label>
                            <input type="text" name="headline" id="input_vm_headline" value="{{ old('headline', $aboutVisionMission->display_headline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-extrabold text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Visi & Misi Kami" required>
                        </div>
                    </div>

                    <!-- Visi Organisasi -->
                    <div class="space-y-1.5 pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-eye text-pmr-primary"></i>
                            <span>Naskah Pernyataan Visi Organisasi <span class="text-rose-500">*</span></span>
                        </label>
                        <textarea name="vision_text" id="input_vm_vision" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm leading-relaxed text-slate-800 italic font-bold focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Terwujudnya PMR Wira SMAN 1 Ciawi..." required>{{ old('vision_text', $aboutVisionMission->display_vision_text) }}</textarea>
                    </div>

                    <!-- Misi Organisasi (4 Poin) -->
                    <div class="space-y-3 pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-bullseye text-pmr-primary"></i>
                            <span>4 Poin Misi Organisasi</span>
                        </label>

                        @foreach([0, 1, 2, 3] as $mIdx)
                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-[10px] font-bold">{{ $mIdx + 1 }}</span>
                                <span>Misi {{ $mIdx + 1 }}</span>
                            </label>
                            <input type="text" name="missions[{{ $mIdx }}]" id="input_vm_mission_{{ $mIdx }}" value="{{ old("missions.{$mIdx}", $missionsList[$mIdx] ?? '') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-pmr-primary/20 focus:border-pmr-primary transition" placeholder="Poin misi {{ $mIdx + 1 }}...">
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-sm text-white bg-pmr-primary hover:bg-pmr-dark transition shadow-lg shadow-red-950/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                    <button type="button" onclick="confirmResetVisionMission()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold text-xs text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-rose-600 transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Kembalikan ke Nilai Bawaan</span>
                    </button>
                </div>
            </form>

            <form id="formResetVisionMission" action="{{ route('admin.content-setup.about.vision-mission.reset') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

        <!-- Kolom Kanan: Pratinjau Langsung Visi & Misi -->
        <div class="lg:col-span-5 sticky top-20 space-y-4">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Live Preview: Visi & Misi</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">Tentang Kami</span>
                </div>

                <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-4 text-center overflow-hidden">
                    <div>
                        <div class="text-pmr-primary font-bold text-[11px] uppercase tracking-widest mb-1" id="preview_sim_vm_tagline">
                            {{ $aboutVisionMission->display_tagline }}
                        </div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight leading-snug" id="preview_sim_vm_headline">
                            {{ $aboutVisionMission->display_headline }}
                        </h2>
                    </div>

                    <!-- Visi Preview Box -->
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 text-left relative overflow-hidden">
                        <div class="inline-flex items-center gap-1.5 bg-red-100 text-pmr-primary font-extrabold text-[9px] px-2.5 py-1 rounded-full uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-eye text-[9px]"></i> Visi Organisasi
                        </div>
                        <blockquote class="text-[11px] font-bold text-slate-800 leading-relaxed italic" id="preview_sim_vm_vision">
                            "{{ $aboutVisionMission->display_vision_text }}"
                        </blockquote>
                    </div>

                    <!-- Misi Preview Box -->
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 text-left relative overflow-hidden">
                        <div class="inline-flex items-center gap-1.5 bg-red-100 text-pmr-primary font-extrabold text-[9px] px-2.5 py-1 rounded-full uppercase tracking-wider mb-2.5">
                            <i class="fa-solid fa-bullseye text-[9px]"></i> Misi Organisasi
                        </div>
                        <ul class="space-y-2 text-[11px] text-slate-700 font-medium">
                            @foreach([0, 1, 2, 3] as $mIdx)
                            <li class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-[9px] mt-0.5 font-bold flex-shrink-0">{{ $mIdx + 1 }}</span>
                                <span id="preview_sim_vm_mission_{{ $mIdx }}">{{ $missionsList[$mIdx] ?? '' }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    // 1. Script Live Preview Mengenal PMR
    const inputIntroTagline = document.getElementById('input_intro_tagline');
    const inputIntroIcon = document.getElementById('input_intro_icon');
    const inputIntroHeadline = document.getElementById('input_intro_headline');
    const inputIntroQuote = document.getElementById('input_intro_quote');
    const inputIntroP1 = document.getElementById('input_intro_p1');
    const inputIntroP2 = document.getElementById('input_intro_p2');
    const inputIntroBadgeText = document.getElementById('input_intro_badge_text');
    const inputIntroBadgeSubtext = document.getElementById('input_intro_badge_subtext');
    const inputIntroImg = document.getElementById('input_intro_img');

    if (inputIntroTagline) {
        inputIntroTagline.addEventListener('input', (e) => {
            document.getElementById('preview_intro_tagline').textContent = e.target.value.trim() || 'MENGENAL PMR';
        });
    }
    if (inputIntroIcon) {
        inputIntroIcon.addEventListener('input', (e) => {
            document.getElementById('preview_intro_icon').className = e.target.value.trim() || 'fa-solid fa-book-open-reader';
        });
    }
    if (inputIntroHeadline) {
        inputIntroHeadline.addEventListener('input', (e) => {
            document.getElementById('preview_intro_headline').textContent = e.target.value.trim() || 'Profil PMR Wira SMAN 1 Ciawi';
        });
    }
    if (inputIntroQuote) {
        inputIntroQuote.addEventListener('input', (e) => {
            document.getElementById('preview_intro_quote').textContent = '"' + (e.target.value.trim() || '') + '"';
        });
    }
    if (inputIntroP1) {
        inputIntroP1.addEventListener('input', (e) => {
            document.getElementById('preview_intro_p1').textContent = e.target.value.trim() || '';
        });
    }
    if (inputIntroP2) {
        inputIntroP2.addEventListener('input', (e) => {
            document.getElementById('preview_intro_p2').textContent = e.target.value.trim() || '';
        });
    }
    if (inputIntroBadgeText) {
        inputIntroBadgeText.addEventListener('input', (e) => {
            document.getElementById('preview_intro_badge_text').textContent = e.target.value.trim() || 'PMR Tingkat Wira';
        });
    }
    if (inputIntroBadgeSubtext) {
        inputIntroBadgeSubtext.addEventListener('input', (e) => {
            document.getElementById('preview_intro_badge_subtext').textContent = e.target.value.trim() || 'Binaan PMI Kab. Bogor & SMAN 1 Ciawi';
        });
    }
    if (inputIntroImg) {
        inputIntroImg.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('filename_intro_label').textContent = 'Berkas dipilih: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('preview_intro_img').src = event.target.result;
                    document.getElementById('thumb_intro_img').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    function confirmResetIntro() {
        if (confirm('Apakah Anda yakin ingin mengembalikan seluruh konten Mengenal PMR ke setelan bawaan sistem?')) {
            document.getElementById('formResetIntro').submit();
        }
    }

    // 2. Script Live Preview Tri Bakti PMR
    const inputTbTagline = document.getElementById('input_tb_tagline');
    const inputTbIcon = document.getElementById('input_tb_icon');
    const inputTbHeadline = document.getElementById('input_tb_headline');
    const inputTbBody = document.getElementById('input_tb_body_text');

    if (inputTbTagline) {
        inputTbTagline.addEventListener('input', (e) => {
            document.getElementById('preview_sim_tb_tagline').textContent = e.target.value.trim() || 'Tri Bakti PMR';
        });
    }
    if (inputTbIcon) {
        inputTbIcon.addEventListener('input', (e) => {
            document.getElementById('preview_sim_tb_icon').className = e.target.value.trim() || 'fa-solid fa-hands-holding-child';
        });
    }
    if (inputTbHeadline) {
        inputTbHeadline.addEventListener('input', (e) => {
            document.getElementById('preview_sim_tb_headline').textContent = e.target.value.trim() || 'Tri Bakti Palang Merah Remaja';
        });
    }
    if (inputTbBody) {
        inputTbBody.addEventListener('input', (e) => {
            document.getElementById('preview_sim_tb_body').textContent = e.target.value.trim() || '';
        });
    }

    [0, 1, 2].forEach(idx => {
        const inSub = document.getElementById(`input_tb_subbadge_${idx}`);
        const inIco = document.getElementById(`input_tb_icon_${idx}`);
        const inTit = document.getElementById(`input_tb_title_${idx}`);
        const inDes = document.getElementById(`input_tb_desc_${idx}`);

        const simSub = document.getElementById(`sim_tb_subbadge_${idx}`);
        const simIco = document.getElementById(`sim_tb_icon_${idx}`);
        const simTit = document.getElementById(`sim_tb_title_${idx}`);
        const simDes = document.getElementById(`sim_tb_desc_${idx}`);
        const indIco = document.getElementById(`preview_tb_icon_ind_${idx}`);
        const indTit = document.getElementById(`tb_title_label_${idx}`);

        if (inSub && simSub) {
            inSub.addEventListener('input', (e) => { simSub.textContent = e.target.value.trim() || `Tri Bakti ${idx + 1}`; });
        }
        if (inIco) {
            inIco.addEventListener('input', (e) => {
                const cls = e.target.value.trim() || 'fa-solid fa-hand-holding-heart';
                if (simIco) simIco.className = cls;
                if (indIco) indIco.className = cls;
            });
        }
        if (inTit) {
            inTit.addEventListener('input', (e) => {
                const val = e.target.value.trim() || '';
                if (simTit) simTit.textContent = val;
                if (indTit) indTit.textContent = val;
            });
        }
        if (inDes && simDes) {
            inDes.addEventListener('input', (e) => { simDes.textContent = e.target.value.trim() || ''; });
        }
    });

    function confirmResetTriBakti() {
        if (confirm('Apakah Anda yakin ingin mengembalikan seluruh konten Tri Bakti PMR ke setelan bawaan sistem?')) {
            document.getElementById('formResetTriBakti').submit();
        }
    }

    // 3. Script Live Preview 7 Dasar Prinsip
    const inputPrTagline = document.getElementById('input_pr_tagline');
    const inputPrHeadline = document.getElementById('input_pr_headline');
    const inputPrBody = document.getElementById('input_pr_body_text');

    if (inputPrTagline) {
        inputPrTagline.addEventListener('input', (e) => {
            document.getElementById('preview_sim_pr_tagline').textContent = e.target.value.trim() || 'Landasan Moral Universal';
        });
    }
    if (inputPrHeadline) {
        inputPrHeadline.addEventListener('input', (e) => {
            document.getElementById('preview_sim_pr_headline').textContent = e.target.value.trim() || '7 Dasar Prinsip Palang Merah';
        });
    }
    if (inputPrBody) {
        inputPrBody.addEventListener('input', (e) => {
            document.getElementById('preview_sim_pr_body').textContent = e.target.value.trim() || '';
        });
    }

    [0, 1, 2, 3, 4, 5, 6].forEach(idx => {
        const inIco = document.getElementById(`input_pr_icon_${idx}`);
        const inTit = document.getElementById(`input_pr_title_${idx}`);
        const inSub = document.getElementById(`input_pr_sub_${idx}`);

        const simIco = document.getElementById(`sim_pr_icon_${idx}`);
        const simTit = document.getElementById(`sim_pr_title_${idx}`);
        const simSub = document.getElementById(`sim_pr_sub_${idx}`);
        const indIco = document.getElementById(`pr_icon_ind_${idx}`);

        if (inIco) {
            inIco.addEventListener('input', (e) => {
                const cls = e.target.value.trim() || 'fa-solid fa-heart';
                if (simIco) simIco.className = cls;
                if (indIco) indIco.className = cls;
            });
        }
        if (inTit && simTit) {
            inTit.addEventListener('input', (e) => { simTit.textContent = e.target.value.trim().toUpperCase() || ''; });
        }
        if (inSub && simSub) {
            inSub.addEventListener('input', (e) => { simSub.textContent = e.target.value.trim() || ''; });
        }
    });

    function confirmResetPrinciples() {
        if (confirm('Apakah Anda yakin ingin mengembalikan seluruh konten 7 Dasar Prinsip ke setelan bawaan sistem?')) {
            document.getElementById('formResetPrinciples').submit();
        }
    }

    // 4. Script Live Preview Visi & Misi
    const inputVmTagline = document.getElementById('input_vm_tagline');
    const inputVmHeadline = document.getElementById('input_vm_headline');
    const inputVmVision = document.getElementById('input_vm_vision');

    if (inputVmTagline) {
        inputVmTagline.addEventListener('input', (e) => {
            document.getElementById('preview_sim_vm_tagline').textContent = e.target.value.trim() || 'Arah Gerak Organisasi';
        });
    }
    if (inputVmHeadline) {
        inputVmHeadline.addEventListener('input', (e) => {
            document.getElementById('preview_sim_vm_headline').textContent = e.target.value.trim() || 'Visi & Misi Kami';
        });
    }
    if (inputVmVision) {
        inputVmVision.addEventListener('input', (e) => {
            document.getElementById('preview_sim_vm_vision').textContent = '"' + (e.target.value.trim() || '') + '"';
        });
    }

    [0, 1, 2, 3].forEach(mIdx => {
        const inM = document.getElementById(`input_vm_mission_${mIdx}`);
        const simM = document.getElementById(`preview_sim_vm_mission_${mIdx}`);
        if (inM && simM) {
            inM.addEventListener('input', (e) => { simM.textContent = e.target.value.trim() || ''; });
        }
    });

    function confirmResetVisionMission() {
        if (confirm('Apakah Anda yakin ingin mengembalikan seluruh konten Visi & Misi Organisasi ke setelan bawaan sistem?')) {
            document.getElementById('formResetVisionMission').submit();
        }
    }
</script>
@endsection
