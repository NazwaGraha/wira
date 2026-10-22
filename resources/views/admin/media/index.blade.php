@extends('layouts.admin')

@section('title', 'Pusat Kelola Gambar & Media Web')
@section('page_title', 'Pusat Kelola Gambar & Media Web')

@section('top_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('home') }}" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Beranda Publik
        </a>
        <a href="{{ route('tentang-kami') }}" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Tentang Kami
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6" x-data="{ currentTab: '{{ $activeTab }}' }">

    <!-- Header Banner Ringkasan -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 text-pmr-primary font-bold text-xs uppercase tracking-widest mb-1.5">
                <i class="fa-solid fa-photo-film"></i> Manajemen Media Terpadu
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Pusat Pengaturan Gambar & Banner Website
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Kelola seluruh foto dan banner konten utama website dalam satu menu terpadu. Perhatikan perbedaan mode antara <strong>Multi-Gambar (bisa lebih dari 1 foto berputar)</strong> pada Hero Banner dan <strong>Gambar Tunggal (1 foto utama)</strong> pada profil konten.
            </p>
        </div>

        <!-- Quick Summary Badge -->
        <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-2xl border border-slate-200 self-stretch sm:self-auto justify-center">
            <div class="text-center px-4 py-1">
                <div class="text-lg font-extrabold text-pmr-primary">{{ $heroSlides->count() }}</div>
                <div class="text-[10px] font-bold text-slate-500 uppercase">Slide Hero</div>
            </div>
            <div class="h-8 w-px bg-slate-200"></div>
            <div class="text-center px-4 py-1">
                <div class="text-lg font-extrabold text-slate-800">2</div>
                <div class="text-[10px] font-bold text-slate-500 uppercase">Foto Profil</div>
            </div>
        </div>
    </div>

    <!-- Tab Switcher Navigasi Modern -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <!-- Tab 1: Hero Slider -->
        <button type="button" 
                @click="currentTab = 'hero'"
                :class="currentTab === 'hero' ? 'bg-pmr-primary text-white shadow-md' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200'"
                class="px-5 py-3 rounded-2xl font-extrabold text-xs uppercase tracking-wider transition-all duration-200 flex items-center gap-2.5 shrink-0">
            <i class="fa-solid fa-sliders"></i>
            <span>1. Slider Hero Beranda</span>
            <span :class="currentTab === 'hero' ? 'bg-white/20 text-white' : 'bg-red-50 text-pmr-primary'" class="text-[10px] px-2 py-0.5 rounded-full font-bold">
                Multi-Gambar ({{ $heroSlides->count() }})
            </span>
        </button>

        <!-- Tab 2: Profil Ekstrakurikuler -->
        <button type="button" 
                @click="currentTab = 'home_profile'"
                :class="currentTab === 'home_profile' ? 'bg-pmr-primary text-white shadow-md' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200'"
                class="px-5 py-3 rounded-2xl font-extrabold text-xs uppercase tracking-wider transition-all duration-200 flex items-center gap-2.5 shrink-0">
            <i class="fa-solid fa-shield-heart"></i>
            <span>2. Profil Beranda</span>
            <span :class="currentTab === 'home_profile' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'" class="text-[10px] px-2 py-0.5 rounded-full font-bold">
                1 Gambar Utama
            </span>
        </button>

        <!-- Tab 3: Mengenal PMR -->
        <button type="button" 
                @click="currentTab = 'about_intro'"
                :class="currentTab === 'about_intro' ? 'bg-pmr-primary text-white shadow-md' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200'"
                class="px-5 py-3 rounded-2xl font-extrabold text-xs uppercase tracking-wider transition-all duration-200 flex items-center gap-2.5 shrink-0">
            <i class="fa-solid fa-book-open-reader"></i>
            <span>3. Mengenal PMR (Tentang Kami)</span>
            <span :class="currentTab === 'about_intro' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'" class="text-[10px] px-2 py-0.5 rounded-full font-bold">
                1 Gambar Utama
            </span>
        </button>

        <!-- Tab 4: Popup Banner -->
        <button type="button" 
                @click="currentTab = 'popup'"
                :class="currentTab === 'popup' ? 'bg-pmr-primary text-white shadow-md' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200'"
                class="px-5 py-3 rounded-2xl font-extrabold text-xs uppercase tracking-wider transition-all duration-200 flex items-center gap-2.5 shrink-0">
            <i class="fa-solid fa-bullhorn"></i>
            <span>4. Popup Banner Iklan</span>
            <span :class="currentTab === 'popup' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'" class="text-[10px] px-2 py-0.5 rounded-full font-bold">
                Pengumuman
            </span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: SLIDER HERO BANNER (MULTI-IMAGE)    -->
    <!-- ========================================== -->
    <div x-show="currentTab === 'hero'" class="space-y-6" x-cloak>
        <!-- Notice Perbedaan Mode Multi-Gambar -->
        <div class="bg-gradient-to-r from-red-50 via-rose-50 to-amber-50 border border-red-200/80 rounded-3xl p-6 sm:p-7 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-pmr-primary text-white flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-pmr-primary border border-red-200 shadow-2xs mb-1">
                            <i class="fa-solid fa-layer-group"></i> Mode Multi-Gambar (Bisa Lebih Dari 1 Gambar)
                        </div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">
                            Pengaturan Slider Carousel Hero Beranda
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed max-w-3xl">
                            Khusus Hero Banner, Anda dapat menambahkan <strong>lebih dari satu gambar</strong>. Sistem secara cerdas akan memutar seluruh gambar aktif sebagai *Slider Carousel Otomatis* bergantian setiap 6 detik dengan transisi halus, tombol navigasi, dan indikator titik.
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.hero-slides.create') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-5 py-3 rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-plus"></i> Tambah Slide Gambar Baru
                </a>
            </div>
        </div>

        <!-- Slides Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse ($heroSlides as $slide)
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm flex flex-col group">
                    <!-- Thumbnail with Gradient Preview -->
                    <div class="relative h-48 sm:h-56 bg-slate-900 overflow-hidden">
                        <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        
                        <!-- Overlay simulasi gradien -->
                        <div class="absolute inset-0 bg-gradient-to-r from-red-950/80 via-pmr-primary/70 to-stone-950/70"></div>
                        
                        <!-- Badge Urutan & Status -->
                        <div class="absolute top-4 left-4 flex items-center gap-2">
                            <span class="bg-black/60 text-white font-extrabold text-xs px-3 py-1 rounded-full backdrop-blur-sm">
                                Urutan #{{ $slide->order_position }}
                            </span>
                            @if ($slide->is_active)
                                <span class="bg-emerald-500 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow">
                                    Aktif di Beranda
                                </span>
                            @else
                                <span class="bg-slate-500 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow">
                                    Nonaktif
                                </span>
                            @endif
                        </div>

                        <!-- Slide text preview -->
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <h4 class="font-extrabold text-base leading-tight">{{ $slide->title }}</h4>
                            @if ($slide->caption)
                                <p class="text-xs text-red-100 line-clamp-1 mt-1 opacity-90">{{ $slide->caption }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Card Info & Actions -->
                    <div class="p-5 flex items-center justify-between bg-white text-xs border-t border-slate-100 mt-auto">
                        <div class="text-slate-400 font-mono text-[11px] truncate max-w-[200px]" title="{{ $slide->image_path }}">
                            {{ $slide->image_path }}
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.hero-slides.edit', $slide->id) }}" class="px-3.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold transition flex items-center gap-1.5">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <form action="{{ route('admin.hero-slides.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus slide ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-16 bg-white rounded-3xl border border-slate-200 text-slate-400">
                    <i class="fa-solid fa-images text-4xl mb-3 text-slate-300"></i>
                    <p class="text-sm font-semibold">Belum ada slide gambar yang ditambahkan.</p>
                    <a href="{{ route('admin.hero-slides.create') }}" class="text-xs text-pmr-primary font-bold underline mt-2 inline-block">Tambah Slide Pertama Sekarang</a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 2: PROFIL EKSTRAKURIKULER BERANDA (SINGLE-IMAGE: 1 GAMBAR) -->
    <!-- ============================================================== -->
    <div x-show="currentTab === 'home_profile'" class="space-y-6" x-cloak>
        <!-- Notice Mode Gambar Tunggal -->
        <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200/80 rounded-3xl p-6 sm:p-7 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                    <i class="fa-solid fa-shield-heart"></i>
                </div>
                <div class="space-y-2 text-xs flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-emerald-700 border border-emerald-200 shadow-2xs">
                            <i class="fa-solid fa-image"></i> Mode Gambar Tunggal (Hanya 1 Foto Utama)
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500">Tampil di: Halaman Beranda (Section Profil)</span>
                    </div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">
                        Gambar Profil Ekstrakurikuler: "Membangun Karakter Kemanusiaan"
                    </h3>
                    <p class="text-slate-600 leading-relaxed">
                        Berbeda dengan Hero Banner yang bisa memiliki banyak slide, section ini hanya memerlukan <strong>1 foto utama</strong> yang merepresentasikan kekompakan dan dedikasi relawan PMR Wira SMAN 1 Ciawi. Mengunggah gambar baru akan <strong>langsung menggantikan foto sebelumnya</strong>.
                    </p>

                    <!-- Info Ukuran & Format -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="bg-white/90 p-3 rounded-2xl border border-emerald-100">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Resolusi Disarankan</div>
                            <div class="text-sm font-extrabold text-slate-900 mt-0.5">1200 × 900 px</div>
                            <div class="text-[11px] text-slate-500">Rasio 4:3 Lanskap atau 1200 × 800 px</div>
                        </div>
                        <div class="bg-white/90 p-3 rounded-2xl border border-emerald-100">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Format Gambar</div>
                            <div class="text-sm font-extrabold text-slate-900 mt-0.5">JPG, JPEG, PNG, WEBP</div>
                            <div class="text-[11px] text-slate-500">Format gambar standar tajam</div>
                        </div>
                        <div class="bg-white/90 p-3 rounded-2xl border border-emerald-100">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Kapasitas Maksimal</div>
                            <div class="text-sm font-extrabold text-slate-900 mt-0.5">Maks. 5 MB</div>
                            <div class="text-[11px] text-slate-500">Disarankan &lt; 2 MB untuk performa web</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 Kolom: Kiri Pratinjau Tampilan Web, Kanan Form Upload Baru -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Kolom Kiri: Pratinjau Tampilan Saat Ini Sesuai Halaman Beranda -->
            <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-eye text-emerald-600"></i> Pratinjau Gambar Aktif Saat Ini
                    </span>
                    <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                        Aktif di Beranda
                    </span>
                </div>

                <!-- Frame Mockup Simulasi Tampilan Web Publik -->
                <div class="relative bg-slate-100 p-3 rounded-3xl border border-slate-200">
                    <div class="rounded-2xl overflow-hidden shadow-xl border-4 border-white bg-slate-200 relative aspect-4/3">
                        <img id="current_home_profile_img" src="{{ $homeProfileImage->image_url }}" alt="{{ $homeProfileImage->title }}" class="w-full h-full object-cover">
                    </div>

                    <!-- Floating Badge Simulasi -->
                    <div class="absolute -bottom-2 -left-2 bg-white p-3.5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-pmr-primary text-white flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-800 text-xs">{{ $homeProfileImage->badge_text ?? 'Terakreditasi A' }}</div>
                            <div class="text-[10px] text-slate-500">{{ $homeProfileImage->badge_subtext ?? 'Unit PMR Wira Teladan Kab. Bogor' }}</div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 text-slate-400 font-mono text-[11px] truncate" title="{{ $homeProfileImage->image_path }}">
                    Path Berkas: {{ $homeProfileImage->image_path }}
                </div>
            </div>

            <!-- Kolom Kanan: Formulir Upload Ganti Gambar Baru -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                <form action="{{ route('admin.media-manager.update-section') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="section_key" value="home_profile">

                    <div>
                        <h4 class="text-base font-extrabold text-slate-900 tracking-tight">
                            Ganti Foto Profil Ekstrakurikuler Beranda
                        </h4>
                        <p class="text-xs text-slate-500 mt-1">
                            Pilih berkas foto baru dari laptop atau HP Anda. Foto lama akan otomatis digantikan oleh berkas baru ini.
                        </p>
                    </div>

                    <!-- Dropzone Box Upload Gambar -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pilih Berkas Foto Baru:
                        </label>

                        <div id="dropzone_home_profile" 
                             onclick="document.getElementById('input_home_profile').click()" 
                             class="cursor-pointer border-2 border-dashed border-emerald-300 hover:border-emerald-600 bg-emerald-50/20 hover:bg-emerald-50/50 transition rounded-2xl p-6 sm:p-8 text-center flex flex-col items-center justify-center gap-2.5 group">
                            
                            <div class="w-14 h-14 rounded-2xl bg-white text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition shadow-sm border border-emerald-100 flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>

                            <div>
                                <p class="text-sm font-extrabold text-slate-800">
                                    <span class="text-emerald-700 underline underline-offset-2">Klik untuk Memilih Foto</span> atau seret file ke sini
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Format: <strong>JPG, JPEG, PNG, WEBP</strong> (Maksimal 5 MB &bull; Rasio 4:3)
                                </p>
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs group-hover:border-emerald-500">
                                <i class="fa-solid fa-folder-open text-amber-500"></i> Telusuri Galeri
                            </span>
                        </div>

                        <input type="file" 
                               name="image" 
                               id="input_home_profile" 
                               accept="image/jpeg,image/png,image/jpg,image/webp" 
                               class="hidden" 
                               onchange="previewSectionUpload(event, 'preview_box_home', 'img_preview_home', 'name_home', 'size_home')">

                        <!-- Live Preview Box -->
                        <div id="preview_box_home" class="hidden bg-emerald-50 border border-emerald-200 rounded-2xl p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold text-emerald-800">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    <span>Foto Baru Terpilih: <strong id="name_home">-</strong> (<span id="size_home">-</span>)</span>
                                </div>
                                <button type="button" onclick="cancelSectionUpload('input_home_profile', 'preview_box_home', 'img_preview_home')" class="text-xs font-bold text-red-600 hover:text-red-800 underline flex items-center gap-1">
                                    <i class="fa-solid fa-xmark"></i> Batal Ganti
                                </button>
                            </div>
                            <div class="relative h-44 rounded-xl overflow-hidden bg-slate-900 border border-emerald-300">
                                <img id="img_preview_home" src="" alt="Preview Baru" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    <!-- Pengaturan Teks Floating Badge (Opsional) -->
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-4">
                        <div class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-certificate text-pmr-primary"></i> Pengaturan Teks Badge Akreditasi (Floating Badge)
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Judul Badge</label>
                                <input type="text" name="badge_text" value="{{ old('badge_text', $homeProfileImage->badge_text) }}" 
                                       placeholder="Contoh: Terakreditasi A" 
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Subjudul Badge</label>
                                <input type="text" name="badge_subtext" value="{{ old('badge_subtext', $homeProfileImage->badge_subtext) }}" 
                                       placeholder="Contoh: Unit PMR Wira Teladan Kab. Bogor" 
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" 
                                onclick="if(confirm('Kembalikan foto profil beranda ke setelan awal sistem?')) { document.getElementById('reset_home_form').submit(); }" 
                                class="text-xs text-slate-500 hover:text-red-600 font-bold underline">
                            Kembalikan ke Default
                        </button>

                        <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-8 py-3.5 rounded-xl shadow-lg transition active:scale-98 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Gambar Beranda
                        </button>
                    </div>
                </form>

                <form id="reset_home_form" action="{{ route('admin.media-manager.reset-section') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="section_key" value="home_profile">
                </form>
            </div>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 3: MENGENAL PMR TENTANG KAMI (SINGLE-IMAGE: 1 GAMBAR)     -->
    <!-- ============================================================== -->
    <div x-show="currentTab === 'about_intro'" class="space-y-6" x-cloak>
        <!-- Notice Mode Gambar Tunggal -->
        <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-slate-50 border border-blue-200/80 rounded-3xl p-6 sm:p-7 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                    <i class="fa-solid fa-book-open-reader"></i>
                </div>
                <div class="space-y-2 text-xs flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-blue-700 border border-blue-200 shadow-2xs">
                            <i class="fa-solid fa-image"></i> Mode Gambar Tunggal (Hanya 1 Foto Utama)
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500">Tampil di: Halaman Tentang Kami (Section Mengenal PMR)</span>
                    </div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">
                        Gambar Profil Mengenal PMR: "Definisi Resmi & Tri Bakti"
                    </h3>
                    <p class="text-slate-600 leading-relaxed">
                        Section ini hanya memerlukan <strong>1 foto resmi</strong> yang tampil di samping naskah definisi kepalangmerahan pada halaman Tentang Kami. Mengunggah gambar baru akan <strong>langsung menggantikan foto lama</strong>.
                    </p>

                    <!-- Info Ukuran & Format -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="bg-white/90 p-3 rounded-2xl border border-blue-100">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Resolusi Disarankan</div>
                            <div class="text-sm font-extrabold text-slate-900 mt-0.5">1200 × 900 px</div>
                            <div class="text-[11px] text-slate-500">Rasio 4:3 Lanskap atau 1200 × 800 px</div>
                        </div>
                        <div class="bg-white/90 p-3 rounded-2xl border border-blue-100">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Format Gambar</div>
                            <div class="text-sm font-extrabold text-slate-900 mt-0.5">JPG, JPEG, PNG, WEBP</div>
                            <div class="text-[11px] text-slate-500">Format gambar standar tajam</div>
                        </div>
                        <div class="bg-white/90 p-3 rounded-2xl border border-blue-100">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Kapasitas Maksimal</div>
                            <div class="text-sm font-extrabold text-slate-900 mt-0.5">Maks. 5 MB</div>
                            <div class="text-[11px] text-slate-500">Disarankan &lt; 2 MB untuk performa web</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 Kolom: Kiri Pratinjau Tampilan Web, Kanan Form Upload Baru -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Kolom Kiri: Pratinjau Tampilan Saat Ini Sesuai Halaman Tentang Kami -->
            <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-eye text-blue-600"></i> Pratinjau Gambar Aktif Saat Ini
                    </span>
                    <span class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                        Aktif di Tentang Kami
                    </span>
                </div>

                <!-- Frame Mockup Simulasi Tampilan Web Publik -->
                <div class="relative bg-slate-100 p-3 rounded-3xl border border-slate-200">
                    <div class="rounded-2xl overflow-hidden shadow-xl border-4 border-white bg-slate-900 relative aspect-4/3">
                        <img id="current_about_intro_img" src="{{ $aboutIntroImage->image_url }}" alt="{{ $aboutIntroImage->title }}" class="w-full h-full object-cover">
                    </div>

                    <!-- Floating Badge Simulasi -->
                    <div class="absolute -bottom-2 -left-2 bg-white p-3.5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-800 text-xs">{{ $aboutIntroImage->badge_text ?? 'PMR Tingkat Wira' }}</div>
                            <div class="text-[10px] text-slate-500">{{ $aboutIntroImage->badge_subtext ?? 'Binaan PMI Kab. Bogor & SMAN 1 Ciawi' }}</div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 text-slate-400 font-mono text-[11px] truncate" title="{{ $aboutIntroImage->image_path }}">
                    Path Berkas: {{ $aboutIntroImage->image_path }}
                </div>
            </div>

            <!-- Kolom Kanan: Formulir Upload Ganti Gambar Baru -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                <form action="{{ route('admin.media-manager.update-section') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="section_key" value="about_intro">

                    <div>
                        <h4 class="text-base font-extrabold text-slate-900 tracking-tight">
                            Ganti Foto Profil Mengenal PMR
                        </h4>
                        <p class="text-xs text-slate-500 mt-1">
                            Pilih berkas foto baru dari laptop atau HP Anda. Foto lama akan otomatis digantikan oleh berkas baru ini.
                        </p>
                    </div>

                    <!-- Dropzone Box Upload Gambar -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pilih Berkas Foto Baru:
                        </label>

                        <div id="dropzone_about_intro" 
                             onclick="document.getElementById('input_about_intro').click()" 
                             class="cursor-pointer border-2 border-dashed border-blue-300 hover:border-blue-600 bg-blue-50/20 hover:bg-blue-50/50 transition rounded-2xl p-6 sm:p-8 text-center flex flex-col items-center justify-center gap-2.5 group">
                            
                            <div class="w-14 h-14 rounded-2xl bg-white text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition shadow-sm border border-blue-100 flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>

                            <div>
                                <p class="text-sm font-extrabold text-slate-800">
                                    <span class="text-blue-700 underline underline-offset-2">Klik untuk Memilih Foto</span> atau seret file ke sini
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Format: <strong>JPG, JPEG, PNG, WEBP</strong> (Maksimal 5 MB &bull; Rasio 4:3)
                                </p>
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs group-hover:border-blue-500">
                                <i class="fa-solid fa-folder-open text-amber-500"></i> Telusuri Galeri
                            </span>
                        </div>

                        <input type="file" 
                               name="image" 
                               id="input_about_intro" 
                               accept="image/jpeg,image/png,image/jpg,image/webp" 
                               class="hidden" 
                               onchange="previewSectionUpload(event, 'preview_box_about', 'img_preview_about', 'name_about', 'size_about')">

                        <!-- Live Preview Box -->
                        <div id="preview_box_about" class="hidden bg-blue-50 border border-blue-200 rounded-2xl p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold text-blue-800">
                                    <i class="fa-solid fa-circle-check text-blue-600"></i>
                                    <span>Foto Baru Terpilih: <strong id="name_about">-</strong> (<span id="size_about">-</span>)</span>
                                </div>
                                <button type="button" onclick="cancelSectionUpload('input_about_intro', 'preview_box_about', 'img_preview_about')" class="text-xs font-bold text-red-600 hover:text-red-800 underline flex items-center gap-1">
                                    <i class="fa-solid fa-xmark"></i> Batal Ganti
                                </button>
                            </div>
                            <div class="relative h-44 rounded-xl overflow-hidden bg-slate-900 border border-blue-300">
                                <img id="img_preview_about" src="" alt="Preview Baru" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    <!-- Pengaturan Teks Floating Badge (Opsional) -->
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-4">
                        <div class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-pmr-primary"></i> Pengaturan Teks Badge Identitas (Floating Badge)
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Judul Badge</label>
                                <input type="text" name="badge_text" value="{{ old('badge_text', $aboutIntroImage->badge_text) }}" 
                                       placeholder="Contoh: PMR Tingkat Wira" 
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Subjudul Badge</label>
                                <input type="text" name="badge_subtext" value="{{ old('badge_subtext', $aboutIntroImage->badge_subtext) }}" 
                                       placeholder="Contoh: Binaan PMI Kab. Bogor & SMAN 1 Ciawi" 
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" 
                                onclick="if(confirm('Kembalikan foto Mengenal PMR ke setelan awal sistem?')) { document.getElementById('reset_about_form').submit(); }" 
                                class="text-xs text-slate-500 hover:text-red-600 font-bold underline">
                            Kembalikan ke Default
                        </button>

                        <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-8 py-3.5 rounded-xl shadow-lg transition active:scale-98 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Gambar Tentang Kami
                        </button>
                    </div>
                </form>

                <form id="reset_about_form" action="{{ route('admin.media-manager.reset-section') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="section_key" value="about_intro">
                </form>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 4: POPUP BANNER IKLAN / PENGUMUMAN     -->
    <!-- ========================================== -->
    <div x-show="currentTab === 'popup'" class="space-y-6" x-cloak>
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl flex-shrink-0 border border-amber-200">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight">
                        Manajemen Popup Banner Iklan & Pengumuman
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-xl leading-relaxed">
                        Kelola banner pop-up modal yang muncul di layar saat pengunjung pertama kali membuka website. Cocok untuk publikasi ajang akbar lomba ketangkasan, rekrutmen relawan, atau agenda kemanusiaan.
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.popup-banner.index') }}" class="bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-6 py-3.5 rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Pengaturan Popup Banner
            </a>
        </div>
    </div>

</div>

<script>
function previewSectionUpload(event, boxId, imgId, nameId, sizeId) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran berkas melebihi batas 5 MB! Silakan pilih foto dengan ukuran lebih kecil.');
            event.target.value = '';
            document.getElementById(boxId).classList.add('hidden');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(imgId).src = e.target.result;
            document.getElementById(nameId).textContent = file.name;

            const sizeInKb = (file.size / 1024).toFixed(1);
            const sizeText = sizeInKb > 1000 ? (sizeInKb / 1024).toFixed(2) + ' MB' : sizeInKb + ' KB';
            document.getElementById(sizeId).textContent = sizeText;

            document.getElementById(boxId).classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function cancelSectionUpload(inputId, boxId, imgId) {
    document.getElementById(inputId).value = '';
    document.getElementById(boxId).classList.add('hidden');
    document.getElementById(imgId).src = '';
}
</script>
@endsection
