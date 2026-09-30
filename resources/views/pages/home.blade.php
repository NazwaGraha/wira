@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section (Ragana Dwi Pantara 2026/2027) with Dynamic Image Slider -->
<section class="relative bg-stone-900 text-white overflow-hidden min-h-[580px] lg:min-h-[660px] flex items-center" id="hero-slider-section">
    <!-- Slider Background Images with Crossfade -->
    <div class="absolute inset-0 z-0 overflow-hidden">
        @if ($heroSlides->count() > 0)
            @foreach ($heroSlides as $index => $slide)
                <div class="hero-slide-item absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100 active' : 'opacity-0 pointer-events-none' }}" 
                     data-index="{{ $index }}"
                     data-caption="{{ $slide->caption ?? '' }}"
                     data-title="{{ $slide->title ?? 'RAGANA DWI PANTARA' }}">
                    <img src="{{ $slide->image_path }}" alt="{{ $slide->title ?? 'Hero PMR' }}" 
                         class="w-full h-full object-cover object-center transform scale-100 transition-transform duration-[8000ms] ease-out">
                </div>
            @endforeach
        @else
            <div class="hero-slide-item absolute inset-0 opacity-100">
                <img src="/mockups/01_home.jpg" alt="PMR SMAN 1 Ciawi Hero" class="w-full h-full object-cover object-center">
            </div>
        @endif

        <!-- Tetap Menjaga Gradien Merah Khas PMR Yang Aktif Sesuai Desain -->
        <div class="absolute inset-0 bg-gradient-to-r from-red-950/90 via-pmr-primary/80 to-stone-950/85 z-10 pointer-events-none"></div>
    </div>

    <!-- Hero Content (Always Front & Legible) -->
    <div class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 flex flex-col items-center text-center">
        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-bold tracking-widest text-red-200 uppercase mb-6 border border-white/20 shadow-inner">
            <span class="w-2 h-2 rounded-full bg-red-400 animate-ping"></span>
            Masa Bakti Resmi 2026/2027
        </div>
        
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight max-w-4xl mb-6 text-shadow-sm">
            RAGANA DWI PANTARA
        </h1>
        
        <p class="text-lg sm:text-xl text-stone-200 max-w-2xl mb-10 font-medium leading-relaxed">
            PMR Wira SMAN 1 Ciawi &mdash; Menyatu untuk Kemanusiaan, Sigap Menolong, Mengabdi Sepenuh Hati bagi Sesama.
        </p>

        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('kontak') }}#daftar" class="bg-white text-pmr-primary hover:bg-red-50 font-bold px-8 py-3.5 rounded-full shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-red-600"></i> Bergabung Sekarang
            </a>
            <a href="{{ route('tentang-kami') }}" class="bg-black/30 backdrop-blur-sm border-2 border-white/60 text-white hover:bg-white/20 font-bold px-8 py-3.5 rounded-full shadow-lg transition-all duration-200 text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-circle-info"></i> Pelajari Lebih Lanjut
            </a>
        </div>

        <!-- Slider Controls & Indicators (Hanya Tampil Jika Gambar > 1) -->
        @if ($heroSlides->count() > 1)
            <div class="mt-12 flex items-center gap-3 z-30">
                <button type="button" onclick="prevHeroSlide()" class="w-9 h-9 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center border border-white/20 transition backdrop-blur-sm" title="Slide Sebelumnya">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>

                <div class="flex items-center gap-2">
                    @foreach ($heroSlides as $i => $s)
                        <button type="button" onclick="goHeroSlide({{ $i }})" 
                                class="hero-dot w-3 h-3 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-white w-8' : 'bg-white/40 hover:bg-white/70' }}" 
                                data-dot="{{ $i }}" 
                                title="Slide {{ $i + 1 }}"></button>
                    @endforeach
                </div>

                <button type="button" onclick="nextHeroSlide()" class="w-9 h-9 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center border border-white/20 transition backdrop-blur-sm" title="Slide Selanjutnya">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        @endif
    </div>
</section>

<!-- KPI / Statistics Banner (4 Cards) -->
<section class="relative z-20 -mt-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-100 flex flex-col items-center text-center transform hover:-translate-y-1 transition duration-200">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-2xl mb-3 shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 tracking-tight">150+</div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Anggota Aktif</div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-100 flex flex-col items-center text-center transform hover:-translate-y-1 transition duration-200">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-2xl mb-3 shadow-inner">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 tracking-tight">25+</div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Kegiatan / Tahun</div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-100 flex flex-col items-center text-center transform hover:-translate-y-1 transition duration-200">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-2xl mb-3 shadow-inner">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 tracking-tight">10+</div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Penghargaan</div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-100 flex flex-col items-center text-center transform hover:-translate-y-1 transition duration-200">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-2xl mb-3 shadow-inner">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 tracking-tight">5000+</div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Jiwa Terbantu</div>
        </div>
    </div>
</section>

<!-- About PMR Wira Intro Section -->
<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <div class="inline-flex items-center gap-2 text-pmr-primary font-bold text-xs uppercase tracking-widest mb-3">
                <i class="fa-solid fa-shield-heart"></i> Profil Ekstrakurikuler
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
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
                    <span class="text-sm text-slate-700 font-medium">Meningkatkan keterampilan pertolongan pertama (P3K) dan tanggap darurat bencana.</span>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs mt-0.5 flex-shrink-0">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-sm text-slate-700 font-medium">Menyelenggarakan aksi donor darah sukarela secara berkala bersama UDD PMI.</span>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center text-xs mt-0.5 flex-shrink-0">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-sm text-slate-700 font-medium">Membentuk jiwa kepemimpinan, kerelawanan, dan persahabatan antar siswa.</span>
                </div>
            </div>
            <a href="{{ route('tentang-kami') }}" class="inline-flex items-center gap-2 bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider px-6 py-3 rounded-xl shadow-md transition">
                Baca Profil Selengkapnya <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="relative">
            <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-slate-200">
                <img src="/mockups/02_tentang_kami.jpg" alt="PMR SMAN 1 Ciawi Team" class="w-full h-auto object-cover">
            </div>
            <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl border border-slate-100 hidden sm:flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-pmr-primary text-white flex items-center justify-center text-xl">
                    <i class="fa-solid fa-certificate"></i>
                </div>
                <div>
                    <div class="font-extrabold text-slate-800 text-sm">Terakreditasi A</div>
                    <div class="text-xs text-slate-500">Unit PMR Wira Teladan Kab. Bogor</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3 Program Unggulan / Pilar Layanan -->
<section class="py-20 bg-slate-100/70 border-y border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <div class="text-pmr-primary font-bold text-xs uppercase tracking-widest mb-2">Pilar Aksi Kemanusiaan</div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Program Unggulan PMR Wira</h2>
            <p class="text-slate-600 text-sm mt-3">Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami di lingkungan SMAN 1 Ciawi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Program 1 -->
            <div class="bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl border border-slate-200/60 transition duration-300 flex flex-col">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-3xl mb-6 shadow-inner">
                    <i class="fa-solid fa-droplet"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">Donor Darah Sukarela</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-6 flex-grow">
                    Edukasi pentingnya donor darah bagi remaja usia 17 tahun ke atas serta aksi donor darah berkala bekerja sama dengan PMI Kabupaten Bogor.
                </p>
                <a href="{{ route('donor-darah') }}" class="text-pmr-primary font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 hover:gap-2.5 transition-all">
                    Cek Stok & Jadwal <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Program 2 -->
            <div class="bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl border border-slate-200/60 transition duration-300 flex flex-col">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-3xl mb-6 shadow-inner">
                    <i class="fa-solid fa-kit-medical"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">Pertolongan Pertama (PP)</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-6 flex-grow">
                    Pelatihan intensif pembalutan, pembidaian, RJP/CPR, dan evakuasi korban untuk memastikan keselamatan di setiap kegiatan sekolah.
                </p>
                <a href="{{ route('kegiatan') }}" class="text-pmr-primary font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 hover:gap-2.5 transition-all">
                    Lihat Modul & Kegiatan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Program 3 -->
            <div class="bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl border border-slate-200/60 transition duration-300 flex flex-col">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-3xl mb-6 shadow-inner">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">Siaga Bencana & Sosial</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-6 flex-grow">
                    Simulasi evakuasi gempa, sekolah aman bencana, serta penggalangan donasi dan bakti sosial untuk warga terdampak musibah.
                </p>
                <a href="{{ route('artikel.index') }}?kategori=kesiapsiagaan-bencana" class="text-pmr-primary font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 hover:gap-2.5 transition-all">
                    Panduan Mitigasi <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Latest Articles Section (Matching Mockup 07) -->
<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
        <div>
            <div class="text-pmr-primary font-bold text-xs uppercase tracking-widest mb-1.5">Pusat Edukasi & Publikasi</div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Artikel & Tips P3K Terkini</h2>
        </div>
        <a href="{{ route('artikel.index') }}" class="text-pmr-primary hover:text-pmr-dark font-bold text-sm flex items-center gap-2">
            Lihat Semua Artikel <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse ($latestArticles as $art)
            <article class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-100 transition duration-300 flex flex-col">
                <div class="relative h-48 overflow-hidden bg-slate-100">
                    <img src="{{ $art->thumbnail ?: '/mockups/07_artikel.jpg' }}" alt="{{ $art->title }}" class="w-full h-full object-cover transform hover:scale-105 transition duration-300">
                    <div class="absolute top-4 left-4 bg-pmr-primary text-white text-[11px] font-bold px-3 py-1 rounded-full shadow">
                        {{ $art->category->name ?? 'Edukasi' }}
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center gap-3 text-xs text-slate-400 mb-3">
                        <span><i class="fa-regular fa-calendar mr-1"></i> {{ $art->published_at ? $art->published_at->format('d M Y') : date('d M Y') }}</span>
                        <span>&bull;</span>
                        <span><i class="fa-regular fa-clock mr-1"></i> {{ $art->estimated_reading_time }}</span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg mb-3 leading-snug hover:text-pmr-primary transition">
                        <a href="{{ route('artikel.show', $art->slug) }}">{{ $art->title }}</a>
                    </h3>
                    <p class="text-slate-600 text-xs leading-relaxed mb-6 flex-grow line-clamp-3">
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
<section class="py-16 gradient-pmr text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 tracking-tight">Siap Menjadi Bagian dari Relawan Kemanusiaan?</h2>
        <p class="text-red-100 max-w-2xl mx-auto text-sm sm:text-base mb-8">
            Pendaftaran calon relawan PMR Wira SMAN 1 Ciawi periode Ragana Dwi Pantara 2026/2027 telah dibuka secara daring. Daftarkan diri Anda sekarang tanpa dipungut biaya!
        </p>
        <a href="{{ route('kontak') }}#daftar" class="inline-flex items-center gap-2 bg-white text-pmr-primary hover:bg-red-50 font-bold px-8 py-4 rounded-full shadow-2xl text-sm uppercase tracking-wider transition transform active:scale-95">
            <i class="fa-solid fa-clipboard-check text-red-600"></i> Isi Formulir Pendaftaran Online
        </a>
    </div>
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
    });
</script>
@endpush
