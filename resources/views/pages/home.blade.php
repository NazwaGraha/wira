@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section (Ragana Dwi Pantara 2026/2027) with Dynamic Image Slider -->
<section class="relative bg-stone-900 text-white overflow-hidden min-h-[660px] lg:min-h-[760px] flex items-center" id="hero-slider-section">
    <!-- Slider Background Images with Crossfade -->
    <div class="absolute inset-0 z-0 overflow-hidden">
        @if ($heroSlides->count() > 0)
            @foreach ($heroSlides as $index => $slide)
                <div class="hero-slide-item absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100 active' : 'opacity-0 pointer-events-none' }}" 
                     data-index="{{ $index }}"
                     data-caption="{{ $slide->caption ?? '' }}"
                     data-title="{{ $slide->title ?? 'RAGANA DWI PANTARA' }}">
                    <img src="{{ $slide->image_url }}" alt="{{ $slide->title ?? 'Hero PMR' }}" 
                         class="w-full h-full object-cover object-center transform scale-100 transition-transform duration-[8000ms] ease-out">
                </div>
            @endforeach
        @else
            <div class="hero-slide-item absolute inset-0 opacity-100">
                <img src="/mockups/01_home.jpg" alt="PMR SMAN 1 Ciawi Hero" class="w-full h-full object-cover object-center">
            </div>
        @endif

        <!-- Gradien Merah Khas PMR Yang Disesuaikan Lebih Terang & Transparan (~40%) -->
        <div class="absolute inset-0 bg-gradient-to-r from-red-950/50 via-pmr-primary/40 to-stone-950/50 z-10 pointer-events-none"></div>
    </div>

    <!-- Hero Content (Always Front & Legible) -->
    <div class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-16 lg:pt-20 pb-12 sm:pb-16 lg:pb-20 flex flex-col items-center text-center">
        <div class="inline-flex items-center gap-2 bg-black/40 backdrop-blur-md px-3.5 sm:px-4 py-1.5 rounded-full text-[10px] sm:text-xs font-bold tracking-widest text-red-200 uppercase mb-4 sm:mb-6 border border-white/20 shadow-lg">
            <span class="w-2 h-2 rounded-full bg-red-400 animate-ping"></span>
            Masa Bakti Resmi 2026/2027
        </div>
        
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight max-w-4xl mb-4 sm:mb-6 drop-shadow-[0_4px_16px_rgba(0,0,0,0.85)]">
            RAGANA DWI PANTARA
        </h1>
        
        <!-- Sub-headline Organisasi (Tepat di Atas Kontrol Navigasi Slider) -->
        <p class="text-sm sm:text-lg lg:text-xl text-stone-100 max-w-2xl mb-0 font-semibold leading-relaxed px-2 drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)]">
            PMR Wira SMAN 1 Ciawi &mdash; Menyatu untuk Kemanusiaan, Sigap Menolong, Mengabdi Sepenuh Hati bagi Sesama.
        </p>

        <!-- Slider Controls & Indicators (Diturunkan Signifikan 2 cm Tambahan / Total 160px di Bawah Sub-headline) -->
        @if ($heroSlides->count() > 1)
            <div style="margin-top: 160px;" class="flex items-center gap-3 z-30">
                <button type="button" onclick="prevHeroSlide()" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center border border-white/20 transition backdrop-blur-sm shadow-md" title="Slide Sebelumnya">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>

                <div class="flex items-center gap-1.5 sm:gap-2">
                    @foreach ($heroSlides as $i => $s)
                        <button type="button" onclick="goHeroSlide({{ $i }})" 
                                class="hero-dot h-2.5 sm:h-3 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-white w-6 sm:w-8' : 'bg-white/40 hover:bg-white/70 w-2.5 sm:w-3' }}" 
                                data-dot="{{ $i }}" 
                                title="Slide {{ $i + 1 }}"></button>
                    @endforeach
                </div>

                <button type="button" onclick="nextHeroSlide()" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center border border-white/20 transition backdrop-blur-sm shadow-md" title="Slide Selanjutnya">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        @endif
    </div>
</section>

<!-- KPI / Statistics Banner (4 Cards) -->
<section class="relative z-20 -mt-6 sm:-mt-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-6">
        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-xl border border-slate-100 flex flex-col items-center text-center transform hover:-translate-y-1 transition duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg sm:text-2xl mb-2 sm:mb-3 shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">150+</div>
            <div class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mt-0.5 sm:mt-1">Anggota Aktif</div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-xl border border-slate-100 flex flex-col items-center text-center transform hover:-translate-y-1 transition duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg sm:text-2xl mb-2 sm:mb-3 shadow-inner">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">25+</div>
            <div class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mt-0.5 sm:mt-1">Kegiatan / Tahun</div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-xl border border-slate-100 flex flex-col items-center text-center transform hover:-translate-y-1 transition duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg sm:text-2xl mb-2 sm:mb-3 shadow-inner">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">10+</div>
            <div class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mt-0.5 sm:mt-1">Penghargaan</div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-xl border border-slate-100 flex flex-col items-center text-center transform hover:-translate-y-1 transition duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg sm:text-2xl mb-2 sm:mb-3 shadow-inner">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">5000+</div>
            <div class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider mt-0.5 sm:mt-1">Jiwa Terbantu</div>
        </div>
    </div>
</section>

<!-- About PMR Wira Intro Section -->
<section class="py-12 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12 items-center">
        <div>
            <div class="inline-flex items-center gap-2 text-pmr-primary font-bold text-xs uppercase tracking-widest mb-3">
                <i class="fa-solid fa-shield-heart"></i> Profil Ekstrakurikuler
            </div>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4 sm:mb-6">
                Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi
            </h2>
            <p class="text-slate-600 leading-relaxed text-sm sm:text-base mb-6">
                Palang Merah Remaja (PMR) Wira SMAN 1 Ciawi adalah wadah pembinaan dan pengembangan anggota remaja PMI tingkat SMA. Kami berkomitmen menanamkan nilai-nilai Tri Bakti PMR serta 7 Prinsip Dasar Palang Merah dan Bulan Sabit Merah Internasional.
            </p>
            <div class="space-y-3 mb-8">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs mt-0.5 flex-shrink-0">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-xs sm:text-sm text-slate-700 font-medium">Meningkatkan keterampilan pertolongan pertama (P3K) dan tanggap darurat bencana.</span>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs mt-0.5 flex-shrink-0">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-xs sm:text-sm text-slate-700 font-medium">Menyelenggarakan aksi donor darah sukarela secara berkala bersama UDD PMI.</span>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs mt-0.5 flex-shrink-0">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-xs sm:text-sm text-slate-700 font-medium">Membentuk jiwa kepemimpinan, kerelawanan, dan persahabatan antar siswa.</span>
                </div>
            </div>
            <a href="{{ route('tentang-kami') }}" class="inline-flex items-center gap-2 bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-6 py-3.5 rounded-xl shadow-md transition">
                Baca Profil Selengkapnya <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="relative">
            <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-slate-200">
                <img src="{{ $homeProfileImage?->image_url ?? asset('mockups/02_tentang_kami.jpg') }}" alt="{{ $homeProfileImage?->title ?? 'PMR SMAN 1 Ciawi Team' }}" class="w-full h-auto object-cover">
            </div>
            <div class="absolute -bottom-4 sm:-bottom-6 -left-4 sm:-left-6 bg-white p-4 sm:p-6 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-pmr-primary text-white flex items-center justify-center text-lg sm:text-xl">
                    <i class="fa-solid fa-certificate"></i>
                </div>
                <div>
                    <div class="font-extrabold text-slate-800 text-xs sm:text-sm">{{ $homeProfileImage?->badge_text ?? 'Terakreditasi A' }}</div>
                    <div class="text-[10px] sm:text-xs text-slate-500">{{ $homeProfileImage?->badge_subtext ?? 'Unit PMR Wira Teladan Kab. Bogor' }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3 Program Unggulan / Pilar Layanan -->
<section class="py-12 sm:py-20 bg-slate-100/70 border-y border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-16">
            <div class="text-pmr-primary font-bold text-xs uppercase tracking-widest mb-2">Pilar Aksi Kemanusiaan</div>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Program Unggulan PMR Wira</h2>
            <p class="text-slate-600 text-xs sm:text-sm mt-2 sm:mt-3">Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami di lingkungan SMAN 1 Ciawi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            <!-- Program 1 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm hover:shadow-xl border border-slate-200/60 transition duration-300 flex flex-col">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-2xl sm:text-3xl mb-4 sm:mb-6 shadow-inner">
                    <i class="fa-solid fa-droplet"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-2 sm:mb-3">Donor Darah Sukarela</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6 flex-grow">
                    Edukasi pentingnya donor darah bagi remaja usia 17 tahun ke atas serta aksi donor darah berkala bekerja sama dengan PMI Kabupaten Bogor.
                </p>
                <a href="{{ route('donor-darah') }}" class="text-pmr-primary font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 hover:gap-2.5 transition-all">
                    Cek Stok & Jadwal <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Program 2 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm hover:shadow-xl border border-slate-200/60 transition duration-300 flex flex-col">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-2xl sm:text-3xl mb-4 sm:mb-6 shadow-inner">
                    <i class="fa-solid fa-kit-medical"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-2 sm:mb-3">Pertolongan Pertama (PP)</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6 flex-grow">
                    Pelatihan intensif pembalutan, pembidaian, RJP/CPR, dan evakuasi korban untuk memastikan keselamatan di setiap kegiatan sekolah.
                </p>
                <a href="{{ route('kegiatan') }}" class="text-pmr-primary font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 hover:gap-2.5 transition-all">
                    Lihat Modul & Kegiatan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Program 3 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm hover:shadow-xl border border-slate-200/60 transition duration-300 flex flex-col">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-2xl sm:text-3xl mb-4 sm:mb-6 shadow-inner">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-2 sm:mb-3">Siaga Bencana & Sosial</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6 flex-grow">
                    Simulasi evakuasi gempa, sekolah aman bencana, serta penggalangan donasi dan bakti sosial untuk warga terdampak musibah.
                </p>
                <a href="{{ route('artikel.index') }}?kategori=kesiapsiagaan-bencana" class="text-pmr-primary font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 hover:gap-2.5 transition-all">
                    Panduan Mitigasi <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Showcase Prestasi & Rekam Jejak Juara Section -->
@if(isset($featuredAchievements) && $featuredAchievements->count() > 0)
<section class="py-16 sm:py-24 bg-gradient-to-b from-stone-900 via-stone-950 to-stone-900 text-white relative overflow-hidden">
    <!-- Glow ambient effects -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 sm:mb-14 gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-amber-500/15 border border-amber-500/30 px-3.5 py-1.5 rounded-full text-[11px] font-bold text-amber-300 uppercase tracking-widest mb-3 shadow-inner">
                    <i class="fa-solid fa-trophy text-amber-400"></i> Etalase Prestasi & Kejuaraan
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    Rekam Jejak Prestasi & Kebanggaan Relawan
                </h2>
                <p class="text-stone-300 text-xs sm:text-sm mt-2 max-w-2xl leading-relaxed">
                    Dedikasi, ketangkasan, dan jiwa kemanusiaan anggota PMR Wira SMAN 1 Ciawi yang terbukti dalam berbagai ajang kejuaraan bergengsi.
                </p>
            </div>
            <a href="{{ url('/prestasi') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-stone-950 font-extrabold text-xs sm:text-sm px-6 py-3.5 rounded-xl shadow-lg shadow-amber-500/20 transition-all transform hover:-translate-y-0.5 whitespace-nowrap">
                <span>Lihat Seluruh Prestasi</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            @foreach($featuredAchievements as $ach)
                <div class="group bg-stone-900/80 rounded-3xl overflow-hidden border border-amber-500/20 hover:border-amber-400/50 shadow-xl hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-300 flex flex-col">
                    <!-- Image Wrapper -->
                    <div class="relative h-56 sm:h-64 bg-stone-950 overflow-hidden">
                        <img src="{{ $ach->trophy_image_url }}" alt="{{ $ach->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/40 to-transparent"></div>
                        
                        <!-- Badges -->
                        <div class="absolute top-3.5 left-3.5 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $ach->rank_badge_class }} shadow-lg">
                                {{ $ach->rank }}
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/60 text-stone-200 backdrop-blur-md border border-white/10">
                                {{ $ach->level }}
                            </span>
                        </div>

                        <!-- Date Badge -->
                        <div class="absolute bottom-3 left-4 text-xs text-stone-300 flex items-center gap-1.5 font-medium">
                            <i class="fa-regular fa-calendar text-amber-400"></i>
                            <span>{{ $ach->event_date->translatedFormat('d F Y') }}</span>
                        </div>

                        @if($ach->gallery_images && count($ach->gallery_images) > 0)
                            <div class="absolute bottom-3 right-4 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-[10px] font-bold text-amber-300 border border-amber-500/30 flex items-center gap-1">
                                <i class="fa-solid fa-camera"></i>
                                <span>{{ count($ach->gallery_images) }} Foto</span>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="text-[11px] font-bold text-amber-400 uppercase tracking-widest mb-1.5">
                            {{ $ach->event_name }}
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-white mb-2 leading-snug group-hover:text-amber-300 transition-colors">
                            <a href="{{ url('/prestasi') }}">{{ $ach->title }}</a>
                        </h3>
                        <p class="text-stone-400 text-xs leading-relaxed mb-4 line-clamp-2">
                            {{ $ach->description }}
                        </p>

                        <!-- Organizer & Action -->
                        <div class="pt-4 border-t border-stone-800/80 mt-auto flex items-center justify-between text-xs text-stone-400">
                            <span class="truncate max-w-[180px]">
                                <i class="fa-solid fa-building-columns text-stone-500 mr-1"></i> {{ $ach->organizer ?? 'PMR Wira SMAN 1 Ciawi' }}
                            </span>
                            <a href="{{ url('/prestasi') }}" class="text-amber-400 group-hover:text-amber-300 font-bold flex items-center gap-1 hover:underline">
                                Detail <i class="fa-solid fa-angle-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Latest Articles Section (Matching Mockup 07) -->
<section class="py-12 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 sm:mb-12 gap-3 sm:gap-4">
        <div>
            <div class="text-pmr-primary font-bold text-xs uppercase tracking-widest mb-1.5">Pusat Edukasi & Publikasi</div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Artikel & Tips P3K Terkini</h2>
        </div>
        <a href="{{ route('artikel.index') }}" class="text-pmr-primary hover:text-pmr-dark font-bold text-xs sm:text-sm flex items-center gap-2">
            Lihat Semua Artikel <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
        @forelse ($latestArticles as $art)
            <article class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-100 transition duration-300 flex flex-col">
                <div class="relative h-44 sm:h-48 overflow-hidden bg-slate-100">
                    <img src="{{ $art->thumbnail ?: '/mockups/07_artikel.jpg' }}" alt="{{ $art->title }}" class="w-full h-full object-cover transform hover:scale-105 transition duration-300">
                    <div class="absolute top-3.5 left-3.5 bg-pmr-primary text-white text-[10px] sm:text-[11px] font-bold px-3 py-1 rounded-full shadow">
                        {{ $art->category->name ?? 'Edukasi' }}
                    </div>
                </div>
                <div class="p-5 sm:p-6 flex flex-col flex-grow">
                    <div class="flex items-center gap-3 text-xs text-slate-400 mb-2.5 sm:mb-3">
                        <span><i class="fa-regular fa-calendar mr-1"></i> {{ $art->published_at ? $art->published_at->format('d M Y') : date('d M Y') }}</span>
                        <span>&bull;</span>
                        <span><i class="fa-regular fa-clock mr-1"></i> {{ $art->estimated_reading_time }}</span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base sm:text-lg mb-2.5 leading-snug hover:text-pmr-primary transition">
                        <a href="{{ route('artikel.show', $art->slug) }}">{{ $art->title }}</a>
                    </h3>
                    <p class="text-slate-600 text-xs leading-relaxed mb-4 sm:mb-6 flex-grow line-clamp-3">
                        {{ $art->excerpt ?: Str::limit(strip_tags($art->body), 110) }}
                    </p>
                    <a href="{{ route('artikel.show', $art->slug) }}" class="text-pmr-primary font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 hover:gap-2.5 transition-all">
                        Baca Selengkapnya <i class="fa-solid fa-angle-right text-xs"></i>
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-3 text-center py-12 text-slate-400">
                Belum ada artikel yang dipublikasikan.
            </div>
        @endforelse
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-12 sm:py-16 gradient-pmr text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-4xl font-extrabold mb-3 sm:mb-4 tracking-tight">Siap Menjadi Bagian dari Relawan Kemanusiaan?</h2>
        <p class="text-red-100 max-w-2xl mx-auto text-xs sm:text-base mb-6 sm:mb-8 leading-relaxed">
            Pendaftaran calon relawan PMR Wira SMAN 1 Ciawi periode Ragana Dwi Pantara 2026/2027 telah dibuka secara daring. Daftarkan diri Anda sekarang tanpa dipungut biaya!
        </p>
        <a href="{{ route('kontak') }}#daftar" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-pmr-primary hover:bg-red-50 font-bold px-6 sm:px-8 py-3.5 sm:py-4 rounded-full shadow-2xl text-xs sm:text-sm uppercase tracking-wider transition transform active:scale-95">
            <i class="fa-solid fa-clipboard-check text-red-600"></i> Isi Formulir Pendaftaran Online
        </a>
    </div>
</section>

@if(isset($activePopupBanner) && $activePopupBanner)
    <!-- Modal Popup Banner Iklan / Pengumuman (Sesuai Mockup) -->
    <div id="home-popup-banner-modal" class="fixed inset-0 z-[60] bg-stone-950/85 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-6 md:p-8 select-none animate-in fade-in duration-300">
        
        <!-- Modal Card Container (Enlarged for High Impact & Readability) -->
        <div class="relative bg-stone-900 border border-amber-500/40 rounded-3xl max-w-xl sm:max-w-2xl md:max-w-3xl lg:max-w-4xl w-full max-h-[94vh] shadow-2xl overflow-y-auto flex flex-col p-4 sm:p-6 md:p-8 text-center animate-in zoom-in-95 duration-300">
            
            <!-- Tombol Close (X) di Pojok Kanan Atas -->
            <button type="button" onclick="closeHomePopupBanner()" class="absolute top-3.5 right-3.5 sm:top-5 sm:right-5 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-stone-950/80 hover:bg-white text-white hover:text-stone-950 flex items-center justify-center transition text-base sm:text-lg z-30 shadow-xl border border-white/20" title="Tutup Pengumuman">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Badge Pengumuman Resmi -->
            <div class="mb-3 sm:mb-4">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-pmr-primary text-white text-[11px] sm:text-xs font-black uppercase tracking-wider shadow-md border border-red-400/40">
                    <i class="fa-solid fa-bullhorn text-amber-300 text-xs sm:text-sm"></i> Pengumuman Resmi
                </span>
            </div>

            <!-- Konten Gambar Banner (Dapat Diklik jika ada target_url) -->
            <div class="rounded-2xl overflow-hidden bg-stone-950 border border-white/10 shadow-2xl mb-4 sm:mb-6 relative group {{ !empty($activePopupBanner->target_url) ? 'cursor-pointer' : '' }}">
                @if(!empty($activePopupBanner->target_url))
                    <a href="{{ route('popup-banner.click', $activePopupBanner->id) }}" {{ $activePopupBanner->open_in_new_tab ? 'target=_blank' : '' }} onclick="recordPopupClick()" class="block">
                        <img src="{{ $activePopupBanner->image_url }}" alt="{{ $activePopupBanner->title }}" class="w-full h-auto max-h-[68vh] sm:max-h-[72vh] md:max-h-[76vh] object-contain mx-auto group-hover:scale-[1.01] transition-transform duration-300">
                    </a>
                @else
                    <img src="{{ $activePopupBanner->image_url }}" alt="{{ $activePopupBanner->title }}" class="w-full h-auto max-h-[68vh] sm:max-h-[72vh] md:max-h-[76vh] object-contain mx-auto">
                @endif
            </div>

            <!-- Judul Pengumuman -->
            @if($activePopupBanner->title)
                <h3 class="text-base sm:text-lg md:text-xl font-black text-white leading-snug mb-4 px-2">
                    {{ $activePopupBanner->title }}
                </h3>
            @endif

            <!-- Tombol Aksi: Sesuai Permintaan User (Hanya Tombol Info & Tombol Lewati/Tutup) -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
                @if(!empty($activePopupBanner->target_url))
                    <!-- Tombol Info Lebih Lanjut -->
                    <a href="{{ route('popup-banner.click', $activePopupBanner->id) }}" {{ $activePopupBanner->open_in_new_tab ? 'target=_blank' : '' }} onclick="recordPopupClick()" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-pmr-primary hover:bg-pmr-dark text-white text-xs sm:text-sm font-black uppercase tracking-wider transition shadow-xl shadow-red-950/40 flex items-center justify-center gap-2.5 transform hover:-translate-y-0.5">
                        <span>{{ $activePopupBanner->button_text ?: 'Info Lebih Lanjut' }}</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                @endif

                <!-- Tombol Lewati & Tutup (Dengan Countdown Otomatis 30 Detik) -->
                <button type="button" onclick="closeHomePopupBanner()" class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-white/15 hover:bg-white/25 text-stone-200 hover:text-white text-xs sm:text-sm font-bold transition border border-white/20 inline-flex items-center justify-center gap-2.5">
                    <span>Lewati & Tutup</span>
                    <span id="popup-auto-close-timer" class="text-[11px] px-2 py-0.5 rounded-full bg-white/20 text-white font-mono font-bold" title="Otomatis tertutup dalam 30 detik">30s</span>
                </button>
            </div>

            <!-- Progress Bar Indikator 30 Detik -->
            <div class="w-full bg-white/10 h-1 rounded-full overflow-hidden mt-3" title="Otomatis tertutup dalam 30 detik">
                <div id="popup-progress-bar" class="h-full bg-gradient-to-r from-amber-400 to-amber-500 transition-all duration-1000 ease-linear" style="width: 100%;"></div>
            </div>

            <!-- Checkbox Opsi Cerdas: Jangan Tampilkan Lagi Hari Ini -->
            <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-center">
                <label class="inline-flex items-center gap-2 text-xs sm:text-sm text-stone-400 cursor-pointer hover:text-stone-300">
                    <input type="checkbox" id="dont-show-again-checkbox" class="w-4 h-4 rounded text-pmr-primary bg-stone-800 border-stone-700 focus:ring-pmr-primary">
                    <span>Jangan tampilkan lagi hari ini</span>
                </label>
            </div>

        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide-item');
    const dots = document.querySelectorAll('.hero-dot');
    const totalSlides = slides.length;
    let slideInterval = null;

    function showSlide(index) {
        if (totalSlides <= 1) return;

        if (index >= totalSlides) currentSlide = 0;
        else if (index < 0) currentSlide = totalSlides - 1;
        else currentSlide = index;

        slides.forEach((slide, i) => {
            if (i === currentSlide) {
                slide.classList.remove('opacity-0', 'pointer-events-none');
                slide.classList.add('opacity-100', 'active');
            } else {
                slide.classList.remove('opacity-100', 'active');
                slide.classList.add('opacity-0', 'pointer-events-none');
            }
        });

        dots.forEach((dot, i) => {
            if (i === currentSlide) {
                dot.classList.add('bg-white', 'w-8');
                dot.classList.remove('bg-white/40');
            } else {
                dot.classList.remove('bg-white', 'w-8');
                dot.classList.add('bg-white/40');
            }
        });
    }

    function nextHeroSlide() {
        showSlide(currentSlide + 1);
        resetAutoSlide();
    }

    function prevHeroSlide() {
        showSlide(currentSlide - 1);
        resetAutoSlide();
    }

    function goHeroSlide(index) {
        showSlide(index);
        resetAutoSlide();
    }

    function startAutoSlide() {
        if (totalSlides > 1) {
            slideInterval = setInterval(() => {
                showSlide(currentSlide + 1);
            }, 6000);
        }
    }

    function resetAutoSlide() {
        if (slideInterval) clearInterval(slideInterval);
        startAutoSlide();
    }

    document.addEventListener('DOMContentLoaded', () => {
        startAutoSlide();

        // Popup Banner Logic (Auto Delay, 30s Auto Dismiss & Tracking)
        @if(isset($activePopupBanner) && $activePopupBanner)
            const popupBannerId = {{ $activePopupBanner->id }};
            const storageKey = 'pmr_popup_closed_' + popupBannerId;
            const lastClosed = localStorage.getItem(storageKey);
            const now = new Date().getTime();
            const oneDay = 24 * 60 * 60 * 1000;

            const urlParams = new URLSearchParams(window.location.search);
            const forceShow = urlParams.has('show_popup') || urlParams.has('popup') || urlParams.has('preview');

            @auth
            const shouldShow = true;
            @else
            const shouldShow = forceShow || (!lastClosed || (now - parseInt(lastClosed)) > oneDay);
            @endauth

            if (shouldShow) {
                setTimeout(() => {
                    const modal = document.getElementById('home-popup-banner-modal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        startPopupAutoCloseTimer();
                        
                        // Kirim impression tracking secara silent via background fetch
                        fetch("{{ route('popup-banner.impression', $activePopupBanner->id) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        }).catch(() => {});
                    }
                }, 800);
            }
        @endif
    });

    let popupAutoCloseTimer = null;
    let popupSecondsRemaining = 30;

    function startPopupAutoCloseTimer() {
        clearPopupAutoCloseTimer();
        popupSecondsRemaining = 30;
        updatePopupTimerDisplay();

        popupAutoCloseTimer = setInterval(() => {
            popupSecondsRemaining--;
            updatePopupTimerDisplay();

            if (popupSecondsRemaining <= 0) {
                clearPopupAutoCloseTimer();
                closeHomePopupBanner();
            }
        }, 1000);
    }

    function updatePopupTimerDisplay() {
        const timerBadge = document.getElementById('popup-auto-close-timer');
        const progressBar = document.getElementById('popup-progress-bar');
        if (timerBadge) {
            timerBadge.textContent = Math.max(0, popupSecondsRemaining) + 's';
        }
        if (progressBar) {
            const pct = Math.max(0, (popupSecondsRemaining / 30) * 100);
            progressBar.style.width = pct + '%';
        }
    }

    function clearPopupAutoCloseTimer() {
        if (popupAutoCloseTimer) {
            clearInterval(popupAutoCloseTimer);
            popupAutoCloseTimer = null;
        }
    }

    function closeHomePopupBanner() {
        clearPopupAutoCloseTimer();
        const modal = document.getElementById('home-popup-banner-modal');
        if (modal) {
            modal.classList.add('hidden');
        }

        @if(isset($activePopupBanner) && $activePopupBanner)
            const checkbox = document.getElementById('dont-show-again-checkbox');
            if (checkbox && checkbox.checked) {
                localStorage.setItem('pmr_popup_closed_{{ $activePopupBanner->id }}', new Date().getTime().toString());
            }
        @endif
    }

    function recordPopupClick() {
        clearPopupAutoCloseTimer();
        closeHomePopupBanner();
    }
</script>
@endpush
