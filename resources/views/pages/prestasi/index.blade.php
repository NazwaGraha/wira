@extends('layouts.app')

@section('title', 'Hall of Fame & Jejak Prestasi Relawan PMR Wira')

@section('content')
<!-- Hero Section Megah: Nuansa Gelap Mewah & Emas Trofi (Sesuai Mockup) -->
<section class="relative bg-gradient-to-b from-stone-950 via-stone-900 to-stone-950 text-white pt-24 pb-16 px-4 sm:px-6 lg:px-8 overflow-hidden border-b border-amber-500/20">
    <!-- Spotlight Glow Emas & Merah di Latar Belakang -->
    <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-gradient-to-b from-amber-500/15 via-red-600/10 to-transparent blur-3xl pointer-events-none rounded-full"></div>
    <div class="absolute top-1/2 left-10 w-72 h-72 bg-red-600/10 blur-3xl pointer-events-none rounded-full"></div>
    <div class="absolute top-1/2 right-10 w-72 h-72 bg-amber-500/10 blur-3xl pointer-events-none rounded-full"></div>

    <div class="relative max-w-5xl mx-auto text-center space-y-5">
        <!-- Badge Tagline Emas -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-400/30 text-amber-300 text-xs font-black uppercase tracking-widest shadow-inner backdrop-blur-md">
            <i class="fa-solid fa-trophy text-amber-400"></i>
            <span>Etalase Prestasi & Kehormatan Relawan</span>
        </div>

        <!-- Judul Utama Bercahaya -->
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-white leading-tight">
            Jejak Prestasi <span class="bg-gradient-to-r from-amber-300 via-amber-400 to-yellow-200 bg-clip-text text-transparent">Relawan Kemanusiaan</span>
        </h1>

        <!-- Deskripsi Inspiratif -->
        <p class="text-stone-300 text-sm sm:text-base max-w-3xl mx-auto leading-relaxed">
            Membina karakter tangguh, sigap, dan berintegritas tinggi. Bukti nyata dedikasi dan keunggulan relawan muda PMR Wira SMAN 1 Ciawi dalam berbagai kejuaraan kepalangmerahan tingkat daerah hingga nasional.
        </p>

        <!-- 4 Stat Counter Boxes (Sesuai Mockup) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 max-w-4xl mx-auto pt-4">
            <div class="bg-stone-800/80 backdrop-blur-md p-4 rounded-2xl border border-stone-700/70 shadow-lg hover:border-amber-400/40 transition">
                <div class="text-2xl sm:text-3xl font-black text-amber-300">{{ $totalPiala }}+</div>
                <div class="text-xs font-bold text-stone-400 uppercase tracking-wider mt-0.5">Piala & Trofi</div>
            </div>

            <div class="bg-stone-800/80 backdrop-blur-md p-4 rounded-2xl border border-stone-700/70 shadow-lg hover:border-amber-400/40 transition">
                <div class="text-2xl sm:text-3xl font-black text-amber-400">{{ $totalJuaraUmum }}</div>
                <div class="text-xs font-bold text-stone-400 uppercase tracking-wider mt-0.5">Juara Umum</div>
            </div>

            <div class="bg-stone-800/80 backdrop-blur-md p-4 rounded-2xl border border-stone-700/70 shadow-lg hover:border-amber-400/40 transition">
                <div class="text-2xl sm:text-3xl font-black text-blue-400">{{ $totalNasional }}</div>
                <div class="text-xs font-bold text-stone-400 uppercase tracking-wider mt-0.5">Prestasi Nasional</div>
            </div>

            <div class="bg-stone-800/80 backdrop-blur-md p-4 rounded-2xl border border-stone-700/70 shadow-lg hover:border-amber-400/40 transition">
                <div class="text-2xl sm:text-3xl font-black text-emerald-400">100%</div>
                <div class="text-xs font-bold text-stone-400 uppercase tracking-wider mt-0.5">Jiwa Relawan</div>
            </div>
        </div>

        <!-- Quick Filter Pills -->
        <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
            <a href="{{ route('prestasi.index') }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition border {{ empty($year) && empty($level) && empty($category) ? 'bg-amber-400 text-stone-950 border-amber-300 shadow-md font-black' : 'bg-stone-800/80 text-stone-300 border-stone-700 hover:border-amber-400/50 hover:text-white' }}">
                Semua Prestasi
            </a>

            @foreach($yearsList as $yr)
                <a href="{{ route('prestasi.index', array_merge(request()->query(), ['tahun' => $yr])) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold transition border {{ (string)$year === (string)$yr ? 'bg-amber-400 text-stone-950 border-amber-300 shadow-md font-black' : 'bg-stone-800/80 text-stone-300 border-stone-700 hover:border-amber-400/50 hover:text-white' }}">
                    {{ $yr }}
                </a>
            @endforeach

            <a href="{{ route('prestasi.index', array_merge(request()->query(), ['tingkat' => 'Nasional'])) }}" 
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition border {{ $level === 'Nasional' ? 'bg-blue-500 text-white border-blue-400 shadow-md font-black' : 'bg-stone-800/80 text-stone-300 border-stone-700 hover:border-blue-400/50 hover:text-white' }}">
                <i class="fa-solid fa-earth-asia text-[10px] mr-1"></i> Tingkat Nasional
            </a>

            <a href="{{ route('prestasi.index', array_merge(request()->query(), ['tingkat' => 'Provinsi'])) }}" 
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition border {{ $level === 'Provinsi' ? 'bg-amber-500 text-stone-950 border-amber-400 shadow-md font-black' : 'bg-stone-800/80 text-stone-300 border-stone-700 hover:border-amber-400/50 hover:text-white' }}">
                <i class="fa-solid fa-medal text-[10px] mr-1"></i> Tingkat Provinsi
            </a>
        </div>
    </div>
</section>

<!-- Konten Utama: Filter Detail & Card Showcase Grid -->
<section class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

    <!-- Filter Bar Interaktif -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200 mb-8">
        <form method="GET" action="{{ route('prestasi.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari piala atau nama ajang..." 
                    class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary focus:bg-white transition">
            </div>

            <!-- Kategori -->
            <div>
                <select name="kategori" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary focus:bg-white transition text-slate-700">
                    <option value="all">Semua Kategori Cabang</option>
                    <option value="Pertolongan Pertama" {{ $category === 'Pertolongan Pertama' ? 'selected' : '' }}>Pertolongan Pertama</option>
                    <option value="Tandu Darurat" {{ $category === 'Tandu Darurat' ? 'selected' : '' }}>Tandu Darurat</option>
                    <option value="Cepat Tepat" {{ $category === 'Cepat Tepat' ? 'selected' : '' }}>Cepat Tepat</option>
                    <option value="Mading Kreasi" {{ $category === 'Mading Kreasi' ? 'selected' : '' }}>Mading Kreasi</option>
                    <option value="Umum" {{ $category === 'Umum' ? 'selected' : '' }}>Juara Umum</option>
                </select>
            </div>

            <!-- Tingkat -->
            <div>
                <select name="tingkat" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary focus:bg-white transition text-slate-700">
                    <option value="all">Semua Tingkatan</option>
                    <option value="Nasional" {{ $level === 'Nasional' ? 'selected' : '' }}>Tingkat Nasional</option>
                    <option value="Provinsi" {{ $level === 'Provinsi' ? 'selected' : '' }}>Tingkat Provinsi</option>
                    <option value="Kabupaten/Kota" {{ $level === 'Kabupaten/Kota' ? 'selected' : '' }}>Tingkat Kabupaten/Kota</option>
                </select>
            </div>

            <!-- Tahun & Reset -->
            <div class="flex items-center gap-2">
                <select name="tahun" onchange="this.form.submit()" class="flex-grow px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary focus:bg-white transition text-slate-700">
                    <option value="">Semua Tahun</option>
                    @foreach($yearsList as $yr)
                        <option value="{{ $yr }}" {{ (string)$year === (string)$yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>
                @if($search || $level || $category || $year)
                    <a href="{{ route('prestasi.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Grid Kartu Prestasi 3 Kolom (Sesuai Mockup) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
        @forelse($achievements as $item)
            @php
                $contingent = is_array($item->contingent_members) ? $item->contingent_members : [];
                $gallery = is_array($item->gallery_images) ? $item->gallery_images : [];
                $galleryUrls = $item->gallery_urls;
            @endphp
            <div class="bg-white rounded-3xl shadow-md border border-slate-200 overflow-hidden flex flex-col hover:shadow-2xl hover:border-amber-400/80 transition-all duration-300 group">
                
                <!-- Foto Utama Piala & Badge Juara -->
                <div class="relative h-56 sm:h-60 bg-stone-900 overflow-hidden">
                    <img src="{{ $item->trophy_image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/20 to-transparent"></div>

                    <!-- Badge Juara Ribbon Pojok Kiri Atas -->
                    <div class="absolute top-3.5 left-3.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $item->rank_badge_class }} backdrop-blur-md">
                            <i class="fa-solid fa-award"></i>
                            <span>{{ $item->rank }}</span>
                        </span>
                    </div>

                    <!-- Badge Tingkat Pojok Kanan Atas -->
                    <div class="absolute top-3.5 right-3.5">
                        <span class="bg-stone-900/80 backdrop-blur-md text-white text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border border-white/20">
                            {{ $item->level }}
                        </span>
                    </div>

                    <!-- Tanggal Pelaksanaan di Bawah Foto -->
                    <div class="absolute bottom-3 left-4 text-xs font-bold text-amber-300 flex items-center gap-1.5 drop-shadow-sm">
                        <i class="fa-regular fa-calendar-check"></i>
                        <span>{{ $item->event_date->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                <!-- Konten Kartu -->
                <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <h3 class="font-black text-slate-900 text-base leading-snug group-hover:text-pmr-primary transition line-clamp-2" title="{{ $item->title }}">
                            "{{ $item->title }}"
                        </h3>
                        <div class="text-xs text-slate-500 flex items-center gap-1.5 font-semibold">
                            <i class="fa-solid fa-flag-checkered text-amber-500 text-[11px]"></i>
                            <span class="line-clamp-1">{{ $item->event_name }}</span>
                        </div>
                        @if($item->organizer)
                            <div class="text-[11px] text-slate-400 line-clamp-1">
                                Penyelenggara: {{ $item->organizer }}
                            </div>
                        @endif
                    </div>

                    <!-- Anggota Kontingen / Tim Peraih -->
                    @if(!empty($contingent) && count($contingent) > 0)
                        <div class="pt-2 border-t border-slate-100">
                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                <i class="fa-solid fa-users text-pmr-primary text-[10px]"></i> Kontingen Peraih:
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($contingent, 0, 3) as $m)
                                    <span class="bg-red-50 text-pmr-primary border border-red-200 text-[10px] font-bold px-2 py-0.5 rounded-md">
                                        {{ $m['name'] ?? '' }}
                                    </span>
                                @endforeach
                                @if(count($contingent) > 3)
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-1.5 py-0.5 rounded-md">
                                        +{{ count($contingent) - 3 }} lainnya
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Thumbnail Galeri Serah Terima & Tombol Aksi -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                        <!-- Mini Thumbnails Galeri -->
                        <div class="flex items-center -space-x-2 overflow-hidden">
                            @if(!empty($galleryUrls) && count($galleryUrls) > 0)
                                @foreach(array_slice($galleryUrls, 0, 3) as $gUrl)
                                    <img src="{{ $gUrl }}" alt="Galeri Piala" class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover">
                                @endforeach
                            @else
                                <span class="text-[11px] text-slate-400 italic">Dokumentasi</span>
                            @endif
                        </div>

                        <!-- Tombol Buka Modal Detail Galeri & Sertifikat -->
                        <button type="button" onclick="openAchievementModal({{ json_encode([
                            'title' => $item->title,
                            'event_name' => $item->event_name,
                            'organizer' => $item->organizer,
                            'event_date' => $item->event_date->translatedFormat('d F Y'),
                            'level' => $item->level,
                            'rank' => $item->rank,
                            'category' => $item->category,
                            'description' => $item->description,
                            'trophy_image_url' => $item->trophy_image_url,
                            'gallery_urls' => $item->gallery_urls,
                            'certificate_url' => $item->certificate_url,
                            'contingent_members' => $contingent,
                        ]) }})" class="bg-amber-400 hover:bg-amber-500 text-stone-950 px-3.5 py-2 rounded-xl text-xs font-black transition flex items-center gap-1.5 shadow-xs">
                            <span>Lihat Galeri & Detail</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200">
                <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-3 text-2xl">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <h3 class="font-black text-slate-800 text-lg">Tidak ada data prestasi yang cocok</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">Silakan coba ganti kata kunci pencarian, filter tahun, atau filter tingkat kejuaraan.</p>
                <a href="{{ route('prestasi.index') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-pmr-primary text-white rounded-xl text-xs font-bold hover:bg-pmr-dark transition">
                    <i class="fa-solid fa-rotate-left"></i> Tampilkan Semua Prestasi
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($achievements->hasPages())
        <div class="mt-10 flex justify-center">
            {{ $achievements->links() }}
        </div>
    @endif
</section>

<!-- Call to Action Banner Bawah: Nuansa Merah Palang Merah & Cahaya Emas (Sesuai Mockup) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
    <div class="relative bg-gradient-to-r from-red-800 via-pmr-primary to-red-900 rounded-3xl p-8 sm:p-12 text-white shadow-2xl overflow-hidden border border-red-700/50 text-center space-y-4">
        <!-- Efek Glow -->
        <div class="absolute -top-24 -left-24 w-64 h-64 bg-amber-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-yellow-400/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl mx-auto space-y-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-[11px] font-black uppercase tracking-wider border border-white/20">
                <i class="fa-solid fa-crown"></i> Generasi Relawan Juara
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                Bergabung & Cetak Prestasi Bersama PMR Wira SMAN 1 Ciawi
            </h2>
            <p class="text-xs sm:text-sm text-red-100 leading-relaxed">
                Asah keterampilan medis darurat, kepemimpinan, dan mental ketangguhan relawan bersama pembina dan pelatih berlisensi PMI.
            </p>
            <div class="pt-2">
                <a href="{{ route('kontak') }}#daftar" class="inline-flex items-center gap-2 bg-white text-pmr-primary hover:bg-red-50 hover:shadow-xl font-extrabold text-xs uppercase tracking-wider px-7 py-3.5 rounded-full transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg">
                    <i class="fa-solid fa-hand-holding-heart text-red-600"></i>
                    <span>Daftar Calon Anggota PMR Sekarang</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Modal Interaktif: Detail Prestasi, Galeri Foto Serah Terima & Piagam -->
<div id="achievement-modal" class="fixed inset-0 z-50 bg-stone-950/85 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-100 flex items-start justify-between bg-gradient-to-r from-slate-50 via-red-50/30 to-amber-50/30">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span id="modal-rank-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-400 text-stone-950 border border-amber-300">
                        Juara Umum
                    </span>
                    <span id="modal-level-badge" class="text-xs font-bold text-slate-500">
                        Tingkat Provinsi
                    </span>
                </div>
                <h3 id="modal-title" class="font-black text-slate-900 text-base sm:text-lg leading-snug">
                    Judul Prestasi
                </h3>
                <div id="modal-event" class="text-xs text-slate-500 font-semibold">
                    Nama Event Kegiatan
                </div>
            </div>
            <button type="button" onclick="closeAchievementModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 overflow-y-auto space-y-6">
            
            <!-- Foto Utama Piala / Preview Slider -->
            <div>
                <div class="text-xs font-black uppercase tracking-wider text-slate-700 mb-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-trophy text-amber-500"></i> Dokumentasi Piala & Galeri Foto
                    </span>
                    <span id="modal-photo-counter" class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">
                        1 / 1 Foto
                    </span>
                </div>
                <div class="relative group/mainphoto rounded-2xl overflow-hidden bg-stone-950 border border-slate-200 aspect-video sm:h-80 flex items-center justify-center">
                    <img id="modal-trophy-img" src="" alt="Dokumentasi Prestasi" class="w-full h-full object-contain mx-auto cursor-pointer transition-all duration-300" onclick="openLightboxFromModal()">
                    
                    <!-- Overlay Tombol Panah Kiri di Modal -->
                    <button type="button" id="modal-prev-btn" onclick="prevModalPhoto(event)" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition shadow-lg opacity-80 hover:opacity-100 z-10 hidden" title="Foto Sebelumnya">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <!-- Overlay Tombol Panah Kanan di Modal -->
                    <button type="button" id="modal-next-btn" onclick="nextModalPhoto(event)" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition shadow-lg opacity-80 hover:opacity-100 z-10 hidden" title="Foto Selanjutnya">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>

                    <!-- Tombol Overlay Buka Layar Penuh -->
                    <div onclick="openLightboxFromModal()" class="absolute bottom-2.5 right-2.5 bg-black/70 hover:bg-black/90 text-white text-[11px] font-bold px-3 py-1.5 rounded-xl flex items-center gap-1.5 cursor-pointer backdrop-blur-xs transition z-10 shadow-md">
                        <i class="fa-solid fa-expand text-xs text-amber-300"></i>
                        <span class="hidden sm:inline">Mode Slider Layar Penuh</span>
                    </div>
                </div>
            </div>

            <!-- Galeri Foto Momen Penyerahan Piala -->
            <div id="modal-gallery-section" class="hidden">
                <div class="text-xs font-black uppercase tracking-wider text-slate-700 mb-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-images text-emerald-500"></i> Galeri Serah Terima Piala di Panggung
                    </span>
                    <span class="text-[10px] text-slate-400 font-semibold">Klik foto untuk membuka mode slider</span>
                </div>
                <div id="modal-gallery-container" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <!-- Dinamis via JS -->
                </div>
            </div>

            <!-- Deskripsi / Cerita Pencapaian -->
            <div id="modal-desc-section" class="hidden">
                <div class="text-xs font-black uppercase tracking-wider text-slate-700 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-quote-left text-pmr-primary"></i> Cerita & Perjuangan Kontingen
                </div>
                <p id="modal-description" class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200">
                </p>
            </div>

            <!-- Kontingen Lengkap -->
            <div id="modal-contingent-section" class="hidden">
                <div class="text-xs font-black uppercase tracking-wider text-slate-700 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-users text-pmr-primary"></i> Anggota Kontingen / Tim Peraih Juara
                </div>
                <div id="modal-contingent-list" class="flex flex-wrap gap-2">
                    <!-- Dinamis via JS -->
                </div>
            </div>

            <!-- Tombol Unduh Sertifikat jika ada -->
            <div id="modal-cert-section" class="hidden pt-2">
                <a id="modal-cert-link" href="#" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-stone-800 transition shadow-sm">
                    <i class="fa-solid fa-file-pdf text-red-400"></i>
                    <span>Buka / Unduh Piagam Penghargaan Resmi</span>
                </a>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeAchievementModal()" class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Fullscreen Lightbox Image Slider Modal (Z-Index 70) -->
<div id="prestasi-lightbox" class="fixed inset-0 z-[70] bg-stone-950/95 backdrop-blur-lg hidden flex flex-col justify-between p-3 sm:p-6 select-none animate-in fade-in duration-200">
    
    <!-- Lightbox Top Bar -->
    <div class="flex items-center justify-between text-white py-2 px-2 sm:px-4 z-20">
        <div class="flex items-center gap-3">
            <span id="lb-counter" class="bg-white/15 px-3 py-1 rounded-full text-xs font-black tracking-wider text-amber-300 border border-white/10">
                1 / 1
            </span>
            <div class="min-w-0">
                <div id="lb-title" class="text-xs sm:text-sm font-bold text-white truncate max-w-[200px] sm:max-w-md"></div>
                <div id="lb-subtitle" class="text-[10px] sm:text-xs text-stone-400 truncate"></div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="closePrestasiLightbox()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition text-lg active:scale-95" title="Tutup Slider (Esc)">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Lightbox Main Stage (With Left and Right navigation buttons) -->
    <div class="relative flex-grow flex items-center justify-center px-2 sm:px-16 my-auto overflow-hidden" onclick="handleLightboxBackdropClick(event)">
        
        <!-- Tombol Panah Kiri (Prev) -->
        <button type="button" id="lb-prev-btn" onclick="lightboxPrev(event)" class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/15 hover:bg-white text-white hover:text-stone-950 backdrop-blur-md flex items-center justify-center transition-all duration-200 text-lg sm:text-xl shadow-2xl z-30 active:scale-95 border border-white/20" title="Foto Sebelumnya (Panah Kiri)">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <!-- Container Gambar Utama dengan Efek Transisi Halus -->
        <div class="max-w-5xl max-h-[70vh] w-full flex items-center justify-center p-2">
            <img id="lb-image" src="" alt="Galeri Prestasi" class="max-h-[68vh] max-w-full w-auto h-auto rounded-2xl shadow-2xl object-contain transition-all duration-200">
        </div>

        <!-- Tombol Panah Kanan (Next) -->
        <button type="button" id="lb-next-btn" onclick="lightboxNext(event)" class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/15 hover:bg-white text-white hover:text-stone-950 backdrop-blur-md flex items-center justify-center transition-all duration-200 text-lg sm:text-xl shadow-2xl z-30 active:scale-95 border border-white/20" title="Foto Selanjutnya (Panah Kanan)">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

    <!-- Lightbox Bottom Thumbnail Bar -->
    <div class="py-2 px-2 z-20">
        <div id="lb-thumbnails" class="flex items-center justify-center gap-2 overflow-x-auto py-2 max-w-3xl mx-auto">
            <!-- Dinamis via JS -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentAchievementPhotos = [];
    let currentPhotoIndex = 0;
    let currentAchievementData = null;

    function openAchievementModal(data) {
        currentAchievementData = data;
        document.getElementById('modal-title').textContent = data.title;
        document.getElementById('modal-event').textContent = data.event_name + (data.organizer ? ' • ' + data.organizer : '') + ' • ' + data.event_date;
        document.getElementById('modal-rank-badge').textContent = data.rank;
        document.getElementById('modal-level-badge').textContent = 'Tingkat ' + data.level;

        // Kumpulkan semua foto ke dalam array slide
        currentAchievementPhotos = [];
        if (data.trophy_image_url) {
            currentAchievementPhotos.push({
                url: data.trophy_image_url,
                caption: 'Trofi & Piala Utama',
                type: 'trophy'
            });
        }
        if (data.gallery_urls && Array.isArray(data.gallery_urls)) {
            data.gallery_urls.forEach((url, i) => {
                currentAchievementPhotos.push({
                    url: url,
                    caption: `Dokumentasi Penyerahan Piala #${i + 1}`,
                    type: 'gallery'
                });
            });
        }

        currentPhotoIndex = 0;
        renderModalPhoto(0);

        // Render Thumbnail Galeri di Modal
        const gallerySection = document.getElementById('modal-gallery-section');
        const galleryContainer = document.getElementById('modal-gallery-container');
        galleryContainer.innerHTML = '';

        if (data.gallery_urls && data.gallery_urls.length > 0) {
            data.gallery_urls.forEach((url, index) => {
                const slideIndex = data.trophy_image_url ? (index + 1) : index;
                
                const card = document.createElement('div');
                card.className = 'group relative rounded-xl overflow-hidden border-2 border-slate-200 h-28 bg-stone-900 cursor-pointer hover:border-amber-400 hover:shadow-lg transition-all duration-200';
                card.onclick = function() {
                    openPrestasiLightbox(slideIndex);
                };

                card.innerHTML = `
                    <img src="${url}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                        <div class="w-8 h-8 rounded-full bg-white/90 group-hover:bg-white text-stone-900 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition shadow-md">
                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-1 right-1 bg-black/70 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                        Foto ${index + 1}
                    </div>
                `;
                galleryContainer.appendChild(card);
            });
            gallerySection.classList.remove('hidden');
        } else {
            gallerySection.classList.add('hidden');
        }

        // Deskripsi
        const descSection = document.getElementById('modal-desc-section');
        const descText = document.getElementById('modal-description');
        if (data.description && data.description.trim() !== '') {
            descText.textContent = data.description;
            descSection.classList.remove('hidden');
        } else {
            descSection.classList.add('hidden');
        }

        // Kontingen
        const contSection = document.getElementById('modal-contingent-section');
        const contList = document.getElementById('modal-contingent-list');
        contList.innerHTML = '';
        if (data.contingent_members && data.contingent_members.length > 0) {
            data.contingent_members.forEach(m => {
                const badge = document.createElement('span');
                badge.className = 'bg-red-50 text-pmr-primary border border-red-200 text-xs font-bold px-3 py-1 rounded-lg';
                badge.textContent = m.name + (m.class_grade ? ' (' + m.class_grade + ')' : '');
                contList.appendChild(badge);
            });
            contSection.classList.remove('hidden');
        } else {
            contSection.classList.add('hidden');
        }

        // Sertifikat
        const certSection = document.getElementById('modal-cert-section');
        const certLink = document.getElementById('modal-cert-link');
        if (data.certificate_url) {
            certLink.href = data.certificate_url;
            certSection.classList.remove('hidden');
        } else {
            certSection.classList.add('hidden');
        }

        document.getElementById('achievement-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function renderModalPhoto(index) {
        if (!currentAchievementPhotos || currentAchievementPhotos.length === 0) return;
        if (index < 0) index = currentAchievementPhotos.length - 1;
        if (index >= currentAchievementPhotos.length) index = 0;
        currentPhotoIndex = index;

        const photo = currentAchievementPhotos[currentPhotoIndex];
        const mainImg = document.getElementById('modal-trophy-img');
        if (photo) {
            mainImg.src = photo.url;
        }

        // Update Counter
        const counterEl = document.getElementById('modal-photo-counter');
        if (counterEl) {
            counterEl.textContent = `${currentPhotoIndex + 1} / ${currentAchievementPhotos.length} Foto`;
        }

        // Tampilkan tombol navigasi modal jika foto > 1
        const prevBtn = document.getElementById('modal-prev-btn');
        const nextBtn = document.getElementById('modal-next-btn');
        if (currentAchievementPhotos.length > 1) {
            prevBtn?.classList.remove('hidden');
            nextBtn?.classList.remove('hidden');
        } else {
            prevBtn?.classList.add('hidden');
            nextBtn?.classList.add('hidden');
        }
    }

    function prevModalPhoto(e) {
        if (e) e.stopPropagation();
        renderModalPhoto(currentPhotoIndex - 1);
    }

    function nextModalPhoto(e) {
        if (e) e.stopPropagation();
        renderModalPhoto(currentPhotoIndex + 1);
    }

    function openLightboxFromModal() {
        openPrestasiLightbox(currentPhotoIndex);
    }

    // Lightbox Slider Functions
    function openPrestasiLightbox(startIndex = 0) {
        if (!currentAchievementPhotos || currentAchievementPhotos.length === 0) return;
        currentPhotoIndex = startIndex;
        if (currentPhotoIndex < 0) currentPhotoIndex = 0;
        if (currentPhotoIndex >= currentAchievementPhotos.length) currentPhotoIndex = currentAchievementPhotos.length - 1;

        renderLightboxSlide();

        const lb = document.getElementById('prestasi-lightbox');
        lb.classList.remove('hidden');
    }

    function renderLightboxSlide() {
        const photo = currentAchievementPhotos[currentPhotoIndex];
        if (!photo) return;

        const imgEl = document.getElementById('lb-image');
        imgEl.src = photo.url;
        document.getElementById('lb-counter').textContent = `${currentPhotoIndex + 1} / ${currentAchievementPhotos.length} Foto`;
        
        if (currentAchievementData) {
            document.getElementById('lb-title').textContent = currentAchievementData.title;
            document.getElementById('lb-subtitle').textContent = photo.caption + ' • ' + currentAchievementData.event_name;
        }

        // Tampilkan tombol prev/next jika foto > 1
        const prevBtn = document.getElementById('lb-prev-btn');
        const nextBtn = document.getElementById('lb-next-btn');
        if (currentAchievementPhotos.length > 1) {
            prevBtn?.classList.remove('hidden');
            nextBtn?.classList.remove('hidden');
        } else {
            prevBtn?.classList.add('hidden');
            nextBtn?.classList.add('hidden');
        }

        // Render Thumbnails bar di bawah
        const thumbContainer = document.getElementById('lb-thumbnails');
        thumbContainer.innerHTML = '';
        currentAchievementPhotos.forEach((item, idx) => {
            const thumb = document.createElement('div');
            const isActive = idx === currentPhotoIndex;
            thumb.className = `w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden cursor-pointer transition-all duration-200 border-2 flex-shrink-0 ${
                isActive ? 'border-amber-400 scale-105 ring-2 ring-amber-400/50' : 'border-white/20 opacity-60 hover:opacity-100 hover:border-white/60'
            }`;
            thumb.onclick = function() {
                currentPhotoIndex = idx;
                renderLightboxSlide();
            };
            thumb.innerHTML = `<img src="${item.url}" class="w-full h-full object-cover">`;
            thumbContainer.appendChild(thumb);
        });

        // Sinkronkan foto modal di belakangnya
        renderModalPhoto(currentPhotoIndex);
    }

    function lightboxPrev(e) {
        if (e) e.stopPropagation();
        if (currentAchievementPhotos.length <= 1) return;
        currentPhotoIndex = (currentPhotoIndex - 1 + currentAchievementPhotos.length) % currentAchievementPhotos.length;
        renderLightboxSlide();
    }

    function lightboxNext(e) {
        if (e) e.stopPropagation();
        if (currentAchievementPhotos.length <= 1) return;
        currentPhotoIndex = (currentPhotoIndex + 1) % currentAchievementPhotos.length;
        renderLightboxSlide();
    }

    function closePrestasiLightbox() {
        document.getElementById('prestasi-lightbox').classList.add('hidden');
    }

    function handleLightboxBackdropClick(e) {
        if (e.target.id === 'lb-image') return;
        if (e.target.closest('#lb-prev-btn') || e.target.closest('#lb-next-btn')) return;
        closePrestasiLightbox();
    }

    function closeAchievementModal() {
        closePrestasiLightbox();
        document.getElementById('achievement-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Keyboard & Gesture Navigation
    document.addEventListener('keydown', function(e) {
        const lb = document.getElementById('prestasi-lightbox');
        const modal = document.getElementById('achievement-modal');

        if (lb && !lb.classList.contains('hidden')) {
            if (e.key === 'ArrowLeft') {
                lightboxPrev();
            } else if (e.key === 'ArrowRight') {
                lightboxNext();
            } else if (e.key === 'Escape') {
                closePrestasiLightbox();
            }
        } else if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'ArrowLeft') {
                prevModalPhoto();
            } else if (e.key === 'ArrowRight') {
                nextModalPhoto();
            } else if (e.key === 'Escape') {
                closeAchievementModal();
            }
        }
    });

    // Touch Swipe Support for Mobile
    let touchStartX = 0;
    let touchEndX = 0;
    const lbStage = document.getElementById('prestasi-lightbox');
    if (lbStage) {
        lbStage.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        lbStage.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleTouchGesture();
        }, { passive: true });
    }

    function handleTouchGesture() {
        const deltaX = touchEndX - touchStartX;
        if (Math.abs(deltaX) > 40) {
            if (deltaX < 0) {
                lightboxNext();
            } else {
                lightboxPrev();
            }
        }
    }
</script>
@endpush
@endsection
