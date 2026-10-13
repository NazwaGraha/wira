@extends('layouts.app')

@section('title', 'Landasan & Pedoman Organisasi (GBHO & PPUO)')

@section('content')
<!-- Page Header Banner -->
<section class="gradient-pmr text-white py-16 sm:py-20 relative overflow-hidden">
    <!-- Glow Effects -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-red-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-black/30 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-bold text-red-200 uppercase tracking-widest mb-4 border border-white/20">
            <i class="fa-solid fa-scale-balanced text-red-300"></i> Dokumen & Konstitusi Resmi Unit
        </div>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight mb-4 text-shadow-sm">
            Landasan & Pedoman Organisasi
        </h1>
        <p class="text-red-100 text-sm sm:text-base max-w-3xl mx-auto leading-relaxed">
            Garis Besar Haluan Organisasi (GBHO) dan Pedoman Pelaksanaan Umum Organisasi (PPUO) Masa Bakti Ragana Dwi Pantara 2026 &mdash; SMAN 1 Ciawi Kabupaten Bogor.
        </p>

        <!-- Download Buttons in Header -->
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ asset('documents/GBHO_PMR_WIRA_SMAN_1_CIAWI_2026.pdf') }}" download 
               class="px-5 py-3 rounded-full bg-white text-pmr-primary hover:bg-red-50 font-bold text-xs uppercase tracking-wider shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf text-red-600"></i> Unduh GBHO 2026 (PDF)
            </a>
            <a href="{{ asset('documents/PPUO_PMR_WIRA_SMAN_1_CIAWI_2026.pdf') }}" download 
               class="px-5 py-3 rounded-full bg-red-950/60 hover:bg-red-950 text-white font-bold text-xs uppercase tracking-wider border border-white/30 backdrop-blur-sm transition flex items-center gap-2">
                <i class="fa-solid fa-book-bookmark text-amber-300"></i> Unduh Buku PPUO (27 Hal)
            </a>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<div class="bg-white border-b border-slate-200 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-xs text-slate-500 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-pmr-primary">Beranda</a>
        <i class="fa-solid fa-angle-right text-[10px]"></i>
        <a href="{{ route('tentang-kami') }}" class="hover:text-pmr-primary">Tentang Kami</a>
        <i class="fa-solid fa-angle-right text-[10px]"></i>
        <span class="text-slate-800 font-semibold">Pedoman Organisasi (GBHO & PPUO)</span>
    </div>
</div>

<!-- Main Interactive Container with Alpine.js -->
<div x-data="{ activeTab: (window.location.hash === '#gbho' ? 'gbho' : 'ppuo'), activeBab: 1, searchQuery: '' }" 
     x-init="window.addEventListener('hashchange', () => { if(window.location.hash === '#gbho') activeTab = 'gbho'; else if(window.location.hash === '#ppuo') activeTab = 'ppuo'; })"
     class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">

    <!-- Tab Switcher Navigation -->
    <div class="flex items-center justify-center mb-12">
        <div class="inline-flex p-1.5 bg-slate-200/80 rounded-2xl border border-slate-300 shadow-inner">
            <button type="button" 
                    @click="activeTab = 'gbho'" 
                    :class="activeTab === 'gbho' ? 'bg-white text-pmr-primary shadow-md font-extrabold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="px-6 sm:px-8 py-3 rounded-xl text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center gap-2.5">
                <i class="fa-solid fa-compass text-base text-red-600"></i>
                <span>Garis Besar Haluan (GBHO 2026)</span>
            </button>
            <button type="button" 
                    @click="activeTab = 'ppuo'" 
                    :class="activeTab === 'ppuo' ? 'bg-white text-pmr-primary shadow-md font-extrabold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="px-6 sm:px-8 py-3 rounded-xl text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center gap-2.5">
                <i class="fa-solid fa-book-journal-whills text-base text-amber-600"></i>
                <span>Pedoman Pelaksanaan (PPUO 2026)</span>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: GBHO (Garis Besar Haluan Organisasi) -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'gbho'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-10">
        
        <!-- Header Dokumen GBHO -->
        <div class="bg-gradient-to-br from-red-950 via-slate-900 to-black text-white p-8 sm:p-12 rounded-3xl shadow-xl relative overflow-hidden border border-red-900/40">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                <div class="space-y-3 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 bg-red-600/30 text-red-300 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-red-500/30">
                        <i class="fa-solid fa-check-double"></i> Dokumen Kebijakan Makro
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                        Garis Besar Haluan Organisasi (GBHO)
                    </h2>
                    <p class="text-sm text-slate-300 max-w-2xl leading-relaxed">
                        PMR Unit SMA Negeri 1 Ciawi Periode 2026 &mdash; Disusun dan disahkan sebagai acuan berjalannya roda kepengurusan organisasi agar tujuan tercapai secara optimal.
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ asset('documents/GBHO_PMR_WIRA_SMAN_1_CIAWI_2026.pdf') }}" download
                       class="px-6 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition flex items-center gap-2">
                        <i class="fa-solid fa-download"></i> Unduh File Asli PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Arah Kebijakan (Highlight Card) -->
        <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/90 shadow-sm relative overflow-hidden">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-pmr-primary flex items-center justify-center text-2xl flex-shrink-0 mt-1">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase text-red-600 tracking-widest mb-1.5">Visi & Arah Kebijakan 2026</h3>
                    <h4 class="text-xl sm:text-2xl font-black text-slate-900 mb-4">Arah Kebijakan Organisasi</h4>
                    <p class="text-sm sm:text-base text-slate-700 leading-relaxed font-medium bg-red-50/60 p-6 rounded-2xl border-l-4 border-pmr-primary">
                        "PMR SMAN 1 Ciawi diarahkan sebagai organisasi kepalangmerahan yang <strong>aktif, dan solid</strong> dengan penguatan kapasitas anggota dalam <strong>kecerdasan berorganisasi, ketangguhan fisik, serta keterampilan kepalangmerahan</strong>. Organisasi ini berfokus pada penumbuhan jiwa kemanusiaan, kepedulian sosial, dan kerja sama yang kuat, sehingga mampu memberikan manfaat nyata bagi warga sekolah melalui aksi kemanusiaan yang <strong>cepat, tepat, dan berdampak</strong>."
                    </p>
                </div>
            </div>
        </div>

        <!-- 6 Strategi Pencapaian -->
        <div>
            <div class="text-center max-w-2xl mx-auto mb-8">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">6 Pilar Strategi Pencapaian</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Langkah sistematis untuk mewujudkan arah kebijakan kepengurusan PMR Unit SMAN 1 Ciawi 2026:
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Strategi 1 -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-red-600 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                            <i class="fa-solid fa-stopwatch"></i>
                        </div>
                        <div class="text-xs font-black uppercase text-red-600 tracking-wider mb-1">Strategi 01</div>
                        <h4 class="font-extrabold text-slate-900 text-base mb-2.5">Kedisiplinan & Tanggung Jawab</h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Menanamkan kedisiplinan dan rasa tanggung jawab kepada seluruh anggota dalam setiap kegiatan, rapat, dan kepanitiaan sebagai dasar terbentuknya organisasi yang tertib dan solid.
                        </p>
                    </div>
                </div>

                <!-- Strategi 2 -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                            <i class="fa-solid fa-kit-medical"></i>
                        </div>
                        <div class="text-xs font-black uppercase text-emerald-600 tracking-wider mb-1">Strategi 02</div>
                        <h4 class="font-extrabold text-slate-900 text-base mb-2.5">Keahlian Medis & Pertolongan Pertama</h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Meningkatkan kemampuan kepalangmerahan dan medis dasar anggota melalui pelatihan pertolongan pertama yang dilaksanakan secara bertahap, rutin, dan sesuai standar PMR.
                        </p>
                    </div>
                </div>

                <!-- Strategi 3 -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                            <i class="fa-solid fa-person-hiking"></i>
                        </div>
                        <div class="text-xs font-black uppercase text-amber-600 tracking-wider mb-1">Strategi 03</div>
                        <h4 class="font-extrabold text-slate-900 text-base mb-2.5">Pembinaan Fisik & Mental (Long March)</h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Menyelenggarakan program pembinaan fisik dan mental anggota, seperti latihan fisik berkala dan kegiatan ketahanan mental, untuk membentuk anggota PMR yang kuat, tangguh, dan siap bertugas.
                        </p>
                    </div>
                </div>

                <!-- Strategi 4 -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                            <i class="fa-solid fa-people-roof"></i>
                        </div>
                        <div class="text-xs font-black uppercase text-blue-600 tracking-wider mb-1">Strategi 04</div>
                        <h4 class="font-extrabold text-slate-900 text-base mb-2.5">Kekompakan & Kekeluargaan</h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Membangun kekompakan dan rasa kekeluargaan antar anggota PMR, dengan menciptakan kegiatan kebersamaan yang positif, edukatif, dan menyenangkan sehingga PMR menjadi lingkungan yang nyaman bagi seluruh anggota.
                        </p>
                    </div>
                </div>

                <!-- Strategi 5 -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                        <div class="text-xs font-black uppercase text-purple-600 tracking-wider mb-1">Strategi 05</div>
                        <h4 class="font-extrabold text-slate-900 text-base mb-2.5">Kepemimpinan & Manajemen Kerja</h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Mengembangkan kemampuan organisasi dan kepemimpinan anggota, melalui pelatihan manajemen kegiatan, kerja tim, dan pembiasaan peran aktif dalam setiap program kerja.
                        </p>
                    </div>
                </div>

                <!-- Strategi 6 -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                            <i class="fa-solid fa-handshake-angle"></i>
                        </div>
                        <div class="text-xs font-black uppercase text-teal-600 tracking-wider mb-1">Strategi 06</div>
                        <h4 class="font-extrabold text-slate-900 text-base mb-2.5">Sinergi Pembina, Alumni & Mitra</h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Memperkuat hubungan dan kerja sama antara anggota, pembina, purna PMR, serta organisasi dan ekstrakurikuler lain di SMA Negeri 1 Ciawi untuk mendukung keberlanjutan dan kemajuan PMR.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penutup GBHO -->
        <div class="bg-slate-100 p-6 sm:p-8 rounded-3xl border border-slate-200 text-center text-xs text-slate-500 leading-relaxed">
            <p>Demikian Garis Besar Haluan Organisasi PMR Unit SMA Negeri 1 Ciawi yang disusun dan disahkan sebagai acuan berjalannya kepengurusan organisasi periode 2026 agar tujuan organisasi tercapai.</p>
        </div>
    </div>

    <!-- ==================================================== -->
    <!-- TAB 2: PPUO (Pedoman Pelaksanaan Umum Organisasi 2026) -->
    <!-- ==================================================== -->
    <div x-show="activeTab === 'ppuo'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-10">

        <!-- Banner Buku PPUO -->
        <div class="bg-gradient-to-r from-stone-900 via-slate-900 to-red-950 text-white rounded-3xl p-6 sm:p-10 shadow-xl border border-red-900/40 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5 text-center md:text-left flex-col md:flex-row">
                <div class="w-20 h-20 rounded-2xl bg-white p-2.5 shadow-lg flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo PMI" class="h-full w-auto object-contain">
                </div>
                <div>
                    <div class="text-xs font-bold text-red-400 uppercase tracking-widest mb-1">Buku Panduan & Anggaran Rumah Tangga</div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white">Pedoman Pelaksanaan Umum Organisasi (PPUO)</h2>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1">Palang Merah Remaja Unit SMAN 1 Ciawi &bull; Edisi Resmi Januari 2026 (27 Halaman)</p>
                </div>
            </div>

            <div class="flex-shrink-0">
                <a href="{{ asset('documents/PPUO_PMR_WIRA_SMAN_1_CIAWI_2026.pdf') }}" download 
                   class="px-6 py-3.5 bg-gradient-to-r from-red-600 to-pmr-primary hover:from-red-700 hover:to-red-900 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf"></i> Unduh Lengkap PDF
                </a>
            </div>
        </div>

        <!-- Layout 2 Kolom: Kiri Navigasi 11 Bab, Kanan Detail Konten Bab -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Kolom Kiri: Indeks 11 Bab (Pill Buttons) -->
            <div class="lg:col-span-4 bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm sticky top-24 space-y-2">
                <div class="px-3 py-2 text-xs font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 flex items-center justify-between">
                    <span>Daftar Isi (11 Bab)</span>
                    <i class="fa-solid fa-list-ol text-red-500"></i>
                </div>

                <!-- Bab 1 -->
                <button type="button" @click="activeBab = 1" 
                        :class="activeBab === 1 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB I</strong> &mdash; Ketentuan Umum</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 2 -->
                <button type="button" @click="activeBab = 2" 
                        :class="activeBab === 2 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB II</strong> &mdash; Nama & Kedudukan Markas</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 3 -->
                <button type="button" @click="activeBab = 3" 
                        :class="activeBab === 3 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB III</strong> &mdash; Keanggotaan & Lembaga</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 4 -->
                <button type="button" @click="activeBab = 4" 
                        :class="activeBab === 4 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB IV</strong> &mdash; Struktur & Program Kerja</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 5 -->
                <button type="button" @click="activeBab = 5" 
                        :class="activeBab === 5 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB V</strong> &mdash; Seragam, Syal & Lambang</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 6 -->
                <button type="button" @click="activeBab = 6" 
                        :class="activeBab === 6 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB VI</strong> &mdash; Tampilan Diri & Atribut</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 7 -->
                <button type="button" @click="activeBab = 7" 
                        :class="activeBab === 7 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB VII</strong> &mdash; Markas & Bedah UKS</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 8 -->
                <button type="button" @click="activeBab = 8" 
                        :class="activeBab === 8 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB VIII</strong> &mdash; Etika & Sopan Santun</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 9 -->
                <button type="button" @click="activeBab = 9" 
                        :class="activeBab === 9 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB IX</strong> &mdash; Prosedur Rapat Organisasi</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 10 -->
                <button type="button" @click="activeBab = 10" 
                        :class="activeBab === 10 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB X</strong> &mdash; Sanksi Disiplin & Tata Tertib</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>

                <!-- Bab 11 -->
                <button type="button" @click="activeBab = 11" 
                        :class="activeBab === 11 ? 'bg-pmr-primary text-white font-bold shadow-md' : 'hover:bg-slate-50 text-slate-700 font-medium'"
                        class="w-full text-left px-3.5 py-3 rounded-xl text-xs transition flex items-center justify-between">
                    <span><strong>BAB XI</strong> &mdash; Pengesahan & Penutup</span>
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-70"></i>
                </button>
            </div>

            <!-- Kolom Kanan: Isi Dokumen Tiap Bab -->
            <div class="lg:col-span-8 bg-white p-8 sm:p-12 rounded-3xl border border-slate-200/90 shadow-sm min-h-[500px]">
                
                <!-- BAB I: UMUM -->
                <div x-show="activeBab === 1" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB I</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">KETENTUAN UMUM</h3>
                    </div>

                    <div class="space-y-5 text-sm text-slate-700 leading-relaxed">
                        <div>
                            <h4 class="font-bold text-slate-900 mb-1">A. Pengertian</h4>
                            <p>Peraturan Urusan Dinas Dalam PMR Unit SMA Negeri 1 Ciawi adalah ketentuan yang mengatur cara-cara menanamkan sikap disiplin pada anggota dalam kegiatan sehari-hari sesuai tugas masing-masing, baik di dalam maupun di luar sekolah.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 mb-1">B. Maksud</h4>
                            <p>Peraturan Urusan Dinas Dalam PMR Unit SMA Negeri 1 Ciawi ini dimaksudkan untuk memberikan petunjuk dan pedoman kerja bagi seluruh anggota dalam menjalankan tugas pokok dan fungsinya.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 mb-1">C. Tujuan</h4>
                            <p>Tujuan Peraturan Urusan Dinas Dalam PMR Unit SMA Negeri 1 Ciawi yaitu agar tercapainya daya guna serta kesiapan anggota PMR Unit SMA Negeri 1 Ciawi yang maksimal dalam menjalankan tugas kemanusiaan.</p>
                        </div>
                    </div>
                </div>

                <!-- BAB II: NAMA & KEDUDUKAN MARKAS -->
                <div x-show="activeBab === 2" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB II</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">NAMA DAN KEDUDUKAN MARKAS</h3>
                    </div>

                    <div class="space-y-5 text-sm text-slate-700 leading-relaxed">
                        <div>
                            <h4 class="font-bold text-slate-900 mb-1">A. Nama Organisasi</h4>
                            <p>Organisasi ini bernama <strong>Palang Merah Remaja Unit Sekolah Menengah Atas Negeri 1 Ciawi</strong> yang selanjutnya disebut <strong>PMR Unit SMA Negeri 1 Ciawi</strong>.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 mb-1">B. Kedudukan Markas</h4>
                            <p>Kedudukan Markas PMR Unit SMA Negeri 1 Ciawi bertempat di <strong>Ruang UKS SMA Negeri 1 Ciawi</strong>, Jl. Veteran III Banjarsari Ciawi, Kab. Bogor. Telp. (0251) 8240444.</p>
                        </div>
                    </div>
                </div>

                <!-- BAB III: KEANGGOTAAN -->
                <div x-show="activeBab === 3" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB III</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">KEANGGOTAAN & KELEMBAGAAN</h3>
                    </div>

                    <div class="space-y-5 text-sm text-slate-700 leading-relaxed">
                        <div>
                            <h4 class="font-bold text-slate-900 mb-2">A. Syarat Anggota</h4>
                            <ul class="list-disc pl-5 space-y-1.5 text-xs sm:text-sm">
                                <li>Tercatat sebagai Siswa SMA Negeri 1 Ciawi.</li>
                                <li>Setiap calon anggota PMR yang ingin bergabung wajib mengisi formulir pendaftaran anggota.</li>
                                <li>Wajib Mengikuti rangkaian kegiatan Pendidikan dan Latihan Dasar (Diklatdas) yang telah dirancang oleh PMR Unit SMA Negeri 1 Ciawi.</li>
                                <li><strong>Kebijakan Anggota Baru di Tengah Kepengurusan:</strong> Diberikan kartu SKU dengan ketentuan menyelesaikan minimal 3 program kerja besar untuk mendapatkan Syal dan PDH, serta wajib mengikuti Diklatdas di tahun kepengurusan berikutnya.</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-bold text-slate-900 mb-2">B. Status Keanggotaan</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                                    <div class="font-bold text-slate-900 text-xs">1. Anggota Biasa</div>
                                    <div class="text-xs text-slate-500 mt-1">Anggota yang telah lulus Diklatdas PMR SMAN 1 Ciawi.</div>
                                </div>
                                <div class="p-3.5 bg-red-50 rounded-2xl border border-red-200">
                                    <div class="font-bold text-red-900 text-xs">2. Anggota Pengurus</div>
                                    <div class="text-xs text-red-700 mt-1">Anggota yang telah lulus Diklatdas dan menempati jabatan struktural organisasi.</div>
                                </div>
                                <div class="p-3.5 bg-blue-50 rounded-2xl border border-blue-200">
                                    <div class="font-bold text-blue-900 text-xs">3. Anggota Kehormatan</div>
                                    <div class="text-xs text-blue-700 mt-1">Orang yang berkontribusi nyata kepada PMR, ditetapkan saat RAT.</div>
                                </div>
                                <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-200">
                                    <div class="font-bold text-amber-900 text-xs">4. Anggota Purna Bakti</div>
                                    <div class="text-xs text-amber-700 mt-1">Anggota yang telah purna kepengurusan atau alumni organisasi.</div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-bold text-slate-900 mb-2">C. Kelembagaan</h4>
                            <p class="text-xs sm:text-sm">1. <strong>Rapat Anggota Tahunan (RAT)</strong> yang dilaksanakan pada akhir kepengurusan merupakan lembaga tertinggi dalam organisasi.</p>
                            <p class="text-xs sm:text-sm mt-1">2. <strong>Pengurus dan Dewan Perwakilan Anggota (DPA/Purna Bakti)</strong> merupakan lembaga tinggi dalam organisasi.</p>
                        </div>

                        <div>
                            <h4 class="font-bold text-slate-900 mb-2">D. Hak & Kewajiban</h4>
                            <p class="text-xs sm:text-sm">Setiap anggota wajib mengamalkan Tri Bakti PMR dan 7 Prinsip Palang Merah, menaati PPUO, memakai KTA, dan menjaga nama baik almamater.</p>
                        </div>
                    </div>
                </div>

                <!-- BAB IV: STRUKTUR & PROGRAM KERJA -->
                <div x-show="activeBab === 4" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB IV</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">STRUKTUR ORGANISASI & PROGRAM KERJA</h3>
                    </div>

                    <div class="space-y-5 text-sm text-slate-700 leading-relaxed">
                        <p>Struktur kepengurusan terdiri dari Pelindung (Kepala Sekolah & PMI), Pembina, Fasilitator/Pelatih, Ketua Umum, Wakil Ketua, Sekretaris, Bendahara, dan <strong>5 Bidang Inti</strong>:</p>

                        <div class="space-y-3">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <strong class="text-red-700 font-bold block mb-1">1. Bidang Pendidikan dan Latihan (Diklat)</strong>
                                <span class="text-xs text-slate-600">Latihan rutin, Diklatdas PMR, materi edukasi Instagram, persiapan kontingen lomba, demo ekskul, dan kegiatan fisik Long March setiap 3 bulan sekali.</span>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <strong class="text-red-700 font-bold block mb-1">2. Bidang Pelayanan</strong>
                                <span class="text-xs text-slate-600">Pelayanan medis upacara & event kesiswaan, donor darah sukarela (DDS), penyuluhan penjaringan bersama Puskesmas Ciawi, cek kesehatan anggota bulanan, dan bakti sosial panti asuhan.</span>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <strong class="text-red-700 font-bold block mb-1">3. Bidang Markas</strong>
                                <span class="text-xs text-slate-600">Manajemen Organisasi Dasar (MOD), penyusunan tata tertib, kolektif BET, Bedah UKS (2x setahun), pelaksanaan RAT, koordinasi KTA, peminjaman ruangan, dan piket harian UKS.</span>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <strong class="text-red-700 font-bold block mb-1">4. Bidang Humas</strong>
                                <span class="text-xs text-slate-600">Peringatan HUT PMI, bakti sosial, pengaktifan Forum Madya-Wira, pencarian informasi perlombaan, roadshow sosialisasi, dan seminar kepalangmerahan.</span>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <strong class="text-red-700 font-bold block mb-1">5. Bidang Kreasi</strong>
                                <span class="text-xs text-slate-600">Pengelolaan media sosial, pengelolaan website profil PMR, PMR In Action, video edukasi, podcast Wisnic Radio, mading Wall of Ideas & mading online, dan poster peringatan hari besar.</span>
                            </div>
                        </div>

                        <div class="p-3 bg-amber-50 text-amber-900 rounded-xl text-xs font-semibold">
                            <i class="fa-solid fa-clock-rotate-left mr-1"></i> <strong>Masa Jabatan:</strong> Masa jabatan dalam 1 periode kepengurusan adalah selama 1 (satu) tahun sejak ditetapkan.
                        </div>
                    </div>
                </div>

                <!-- BAB V: SERAGAM & LAMBANG -->
                <div x-show="activeBab === 5" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB V</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">SERAGAM, SYAL & LAMBANG</h3>
                    </div>

                    <div class="space-y-6 text-sm text-slate-700 leading-relaxed">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- PDH -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                                <div class="w-10 h-10 mx-auto rounded-full bg-red-100 text-red-700 flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm">Pakaian Dinas Harian (PDH)</h4>
                                <p class="text-xs text-slate-500 mt-1">Kemeja putih atribut lengkap, celana/rok abu-abu, syal kuning, dan tanda jabatan. Digunakan untuk apel, seminar, dan perlombaan.</p>
                            </div>

                            <!-- PSAS -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                                <div class="w-10 h-10 mx-auto rounded-full bg-blue-100 text-blue-700 flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-school"></i>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm">Pakaian PSAS</h4>
                                <p class="text-xs text-slate-500 mt-1">Seragam sekolah lengkap dengan syal kuning. Digunakan saat kumpul rutin dan menjadi petugas upacara hari Senin.</p>
                            </div>

                            <!-- PDL -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                                <div class="w-10 h-10 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-shirt"></i>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm">Pakaian Dinas Lapangan (PDL)</h4>
                                <p class="text-xs text-slate-500 mt-1">Kaos PDL khas PMR SMANIC bertuliskan "Siamo Tutti Fratelli", syal PMR. Dipakai pada kegiatan latihan lapangan, diklatsar, dan siaga bencana.</p>
                            </div>
                        </div>

                        <!-- Spesifikasi Syal Kuning -->
                        <div class="p-5 bg-amber-50 rounded-2xl border border-amber-200 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-400 text-stone-900 flex items-center justify-center text-xl font-bold flex-shrink-0">
                                🟡
                            </div>
                            <div>
                                <h4 class="font-extrabold text-stone-900 text-sm">Standar Spesifikasi Syal PMR Wira</h4>
                                <p class="text-xs text-stone-700 mt-1">
                                    Syal PMR Wira berbentuk <strong>segitiga sama kaki dengan ukuran 120 x 90 x 90 cm</strong>, warna dasar <strong>kuning</strong>, dan pada sepertiga ujung syal terdapat <strong>logo resmi PMI (Palang Merah Indonesia)</strong>. Syal hanya berhak dikenakan oleh anggota yang telah resmi menyelesaikan Diklatdas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAB VI: TAMPILAN DIRI -->
                <div x-show="activeBab === 6" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB VI</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">TAMPILAN DIRI ANGGOTA</h3>
                    </div>

                    <div class="space-y-4 text-xs sm:text-sm text-slate-700 leading-relaxed">
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Pakaian:</strong> Kemeja PDH tidak boleh ketat, bersih, dan rapi saat menjalankan tugas kedinasan.</li>
                            <li><strong>Jilbab:</strong> Khusus anggota putri, pemakaian jilbab wajib mengikuti warna dasar pakaian.</li>
                            <li><strong>Syal Kuning:</strong> Hanya boleh digunakan setelah anggota lulus kegiatan Diklatdas.</li>
                            <li><strong>Rambut Putra:</strong> Rambut rapi, tidak menutupi telinga, dan tidak menutupi pandangan mata. Wajah dijaga kerapihannya (janggut, jambang, kumis).</li>
                            <li><strong>Warna Rambut:</strong> Tidak diperbolehkan mewarnai rambut (rambut wajib warna alami).</li>
                        </ul>
                    </div>
                </div>

                <!-- BAB VII: MARKAS & BEDAH UKS -->
                <div x-show="activeBab === 7" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB VII</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">MARKAS & BEDAH UKS</h3>
                    </div>

                    <div class="space-y-4 text-xs sm:text-sm text-slate-700 leading-relaxed">
                        <p>Markas PMR bertempat di <strong>Ruang UKS SMAN 1 Ciawi</strong> sebagai pusat operasional dan layanan medis siswa.</p>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                            <strong class="text-slate-900 font-bold block">Piket Markas Harian:</strong>
                            <p>Wajib diikuti seluruh anggota aktif berdasarkan jadwal kerja sekolah: membersihkan ruangan, melayani pasien UKS, mengorganisir obat/peralatan, mencatat inventaris, dan mengunci markas setelah jam tugas berakhir.</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                            <strong class="text-slate-900 font-bold block">Bedah UKS:</strong>
                            <p>Dilaksanakan minimal <strong>2 kali</strong> dalam 1 periode kepengurusan oleh seluruh anggota aktif: pembersihan total, perombakan tata letak (layout), pencatatan ulang stok obat-obatan, serta senam dan makan bersama untuk mempererat kekeluargaan.</p>
                        </div>
                    </div>
                </div>

                <!-- BAB VIII: ETIKA ANGGOTA -->
                <div x-show="activeBab === 8" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB VIII</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">ETIKA ANGGOTA</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-700">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <strong class="text-slate-900 font-bold block mb-1">Masuk/Keluar Markas</strong>
                            Mengetuk pintu, mengucapkan salam, membuka alas kaki (kecuali saat tindakan medis darurat). Menjaga ketertiban di dalam markas.
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <strong class="text-slate-900 font-bold block mb-1">Makan & Minum</strong>
                            Berdoa, dalam posisi duduk (tidak boleh makan/minum berdiri). Tidak makan santai di saat rekan lain sedang sibuk bertugas.
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <strong class="text-slate-900 font-bold block mb-1">Komunikasi & Bertamu</strong>
                            Menggunakan bahasa sopan, dilarang merendahkan orang lain. Saat bertamu ke unit lain wajib membawa surat tugas dan berseragam rapi minimal 2 orang.
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <strong class="text-slate-900 font-bold block mb-1">Aturan Perizinan</strong>
                            Izin ketidakhadiran (karena sakit, dinas sekolah, atau les) wajib diajukan selambat-lambatnya H-1 kepada Ketua Umum atau Pembina.
                        </div>
                    </div>
                </div>

                <!-- BAB IX: RAPAT -->
                <div x-show="activeBab === 9" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB IX</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">PROSEDUR RAPAT ORGANISASI</h3>
                    </div>

                    <div class="space-y-3 text-xs sm:text-sm text-slate-700 leading-relaxed">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <strong class="text-slate-900 font-bold block mb-1">1. Rapat Anggota Tahunan (RAT)</strong>
                            Dilaksanakan di akhir masa jabatan, dihadiri seluruh anggota, DPA, pembina, dan pelatih. Agenda utama: Laporan Pertanggungjawaban (LPJ), penetapan PPUO & GBHO, serta serah terima jabatan (Sertijab).
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <strong class="text-slate-900 font-bold block mb-1">2. Rapat Kerja (Raker)</strong>
                            Dilaksanakan maksimal 7x24 jam setelah Ketua Umum terpilih, dihadiri seluruh anggota untuk menyusun rencana strategis dan detail program kerja 1 periode ke depan.
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <strong class="text-slate-900 font-bold block mb-1">3. Rapat Anggota Luar Biasa</strong>
                            Dilaksanakan guna menyelamatkan organisasi jika kepengurusan vakum, ketua berhalangan tetap, atau terjadi kendala darurat.
                        </div>
                    </div>
                </div>

                <!-- BAB X: TATA TERTIB & SANKSI -->
                <div x-show="activeBab === 10" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB X</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">SANKSI DISIPLIN & TATA TERTIB</h3>
                    </div>

                    <div class="space-y-5 text-xs sm:text-sm text-slate-700 leading-relaxed">
                        <div class="p-4 bg-red-50 text-red-950 rounded-2xl border border-red-200">
                            <strong class="font-bold block mb-1">Larangan Tegas:</strong>
                            Dilarang menggunakan sepatu/sandal di UKS (kecuali darurat), dilarang menghina lambang, dilarang berisik/membuang sampah, dan <strong>dilarang keras menjadikan UKS tempat berpacaran atau membolos</strong>.
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-black text-[10px] uppercase">Ringan</span>
                                <p class="mt-2 text-slate-600">Lupa salam, membuang sampah, tidak membawa atribut 1x. <br><strong>Sanksi:</strong> Teguran lisan, nasihat edukatif.</p>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded font-black text-[10px] uppercase">Sedang</span>
                                <p class="mt-2 text-slate-600">Terlambat, lupa atribut >2x, mengotori UKS. <br><strong>Sanksi:</strong> Piket tambahan, sanksi fisik ringan (lari lapangan).</p>
                            </div>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded font-black text-[10px] uppercase">Berat</span>
                                <p class="mt-2 text-slate-600">Berkelahi, mencemarkan nama baik, absen >4x tanpa izin. <br><strong>Sanksi:</strong> Surat Peringatan (SP), skorsing, pemecatan keanggotaan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAB XI: PENGESAHAN -->
                <div x-show="activeBab === 11" class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <span class="text-xs font-black uppercase text-red-600 tracking-wider">BAB XI</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">LEMBAR PENGESAHAN RESMI</h3>
                    </div>

                    <div class="p-6 sm:p-8 bg-slate-50 rounded-3xl border border-slate-200 space-y-6 text-center">
                        <div class="text-xs text-slate-500 uppercase tracking-widest font-bold">
                            Ditetapkan di Ciawi, Bogor &mdash; Januari 2026
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[11px] text-slate-400 font-semibold mb-6">Ketua Umum,</div>
                                <div class="font-bold text-slate-900 text-sm">Sulthan Arkana Putra R.</div>
                                <div class="text-xs text-slate-500">NIP. 242510286</div>
                            </div>
                            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[11px] text-slate-400 font-semibold mb-6">Sekretaris,</div>
                                <div class="font-bold text-slate-900 text-sm">Putri Raisya Anjani</div>
                                <div class="text-xs text-slate-500">NIP. 242510280</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[11px] text-slate-400 font-semibold mb-6">Pembina PMR,</div>
                                <div class="font-bold text-slate-900 text-sm">Abdul Hamid Saputra, S.Hum., M.Pd.</div>
                                <div class="text-xs text-slate-500">NIP. 199805102025211039</div>
                            </div>
                            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs">
                                <div class="text-[11px] text-slate-400 font-semibold mb-6">Mengetahui, Kepala Sekolah</div>
                                <div class="font-bold text-slate-900 text-sm">Luki Adam Bahtiar, S.Pd., M.Pd.</div>
                                <div class="text-xs text-slate-500">NIP. 197904282003121006</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection
