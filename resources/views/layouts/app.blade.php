<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PMR WIRA SMAN 1 CIAWI') | Ragana Dwi Pantara 2026/2027</title>
    <meta name="description" content="Portal Resmi PMR Wira SMAN 1 Ciawi - Unit Palang Merah Remaja Tingkat Wira Masa Bakti Ragana Dwi Pantara 2026/2027. Kemanusiaan, Kesiapsiagaan, dan Donor Darah.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome & Tailwind CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pmr: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                            primary: '#980000',
                            dark: '#6e0000',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-pmr {
            background: linear-gradient(135deg, #7f0000 0%, #980000 50%, #5a0000 100%);
        }
        .text-shadow-sm {
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <!-- Top Emergency & Announcement Bar -->
    <div class="bg-stone-900 text-white text-xs py-2.5 px-4 sm:px-8 lg:px-12 border-b border-stone-800">
        <div class="w-full max-w-[1720px] mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-3">
                <span class="bg-pmr-primary text-white font-bold px-2.5 py-0.5 rounded text-[10px] tracking-wider uppercase">Hotline UKS</span>
                <span class="text-stone-300">Piket Medis Sekolah & Pertolongan Pertama: <strong class="text-white">+62 812-3456-7890</strong></span>
            </div>
            <div class="flex items-center gap-4 text-stone-400">
                <a href="{{ route('donor-darah') }}" class="hover:text-red-400 transition flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-droplet text-red-500"></i> Info Donor Darah
                </a>
                <span class="text-stone-600">|</span>
                <a href="{{ route('admin.login') }}" class="hover:text-white transition flex items-center gap-1.5 text-xs font-medium">
                    <i class="fa-solid fa-lock"></i> Backoffice CMS
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header (Lebar Penuh & Mewah) -->
    <header class="sticky top-0 z-50 bg-pmr-primary text-white shadow-xl border-b border-pmr-dark">
        <div class="w-full max-w-[1720px] mx-auto px-4 sm:px-8 lg:px-12">
            <div class="flex items-center justify-between h-24 sm:h-28">
                
                <!-- Logo & School Brand (Diperbesar & Sangat Jelas) -->
                <a href="{{ route('home') }}" class="flex items-center gap-4 group flex-shrink-0">
                    <div class="h-16 sm:h-20 bg-white rounded-2xl px-3.5 sm:px-4 py-1.5 flex items-center justify-center shadow-xl group-hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo PMR Wira SMAN 1 Ciawi & PMI" class="h-13 sm:h-16 w-auto object-contain">
                    </div>
                    <div>
                        <div class="font-extrabold text-xl sm:text-2xl tracking-tight leading-tight uppercase group-hover:text-red-100 transition">
                            PMR WIRA SMAN 1 CIAWI
                        </div>
                        <div class="text-xs sm:text-sm font-semibold text-red-200 tracking-wider">
                            RAGANA DWI PANTARA 2026/2027
                        </div>
                    </div>
                </a>

                <!-- Desktop Menu (Lebar & Terbuka Nyaman) -->
                <nav class="hidden xl:flex items-center gap-1.5 font-semibold text-[15px]">
                    <a href="{{ route('home') }}" class="px-4 py-2.5 rounded-xl transition {{ request()->routeIs('home') ? 'bg-black/30 text-white font-bold shadow-inner' : 'hover:bg-white/10 text-white/90' }}">
                        Beranda
                    </a>
                    <a href="{{ route('tentang-kami') }}" class="px-4 py-2.5 rounded-xl transition {{ request()->routeIs('tentang-kami') ? 'bg-black/30 text-white font-bold shadow-inner' : 'hover:bg-white/10 text-white/90' }}">
                        Tentang Kami
                    </a>

                    <!-- Dropdown Menu: Info (Kegiatan, Galeri, Donor Darah, Artikel) -->
                    @php
                        $isInfoActive = request()->routeIs('kegiatan') || request()->routeIs('galeri') || request()->routeIs('donor-darah') || request()->routeIs('artikel.*');
                    @endphp
                    <div class="relative group">
                        <button type="button" class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 {{ $isInfoActive ? 'bg-black/30 text-white font-bold shadow-inner' : 'hover:bg-white/10 text-white/90' }}">
                            <span>Info</span>
                            <i class="fa-solid fa-chevron-down text-xs text-red-200 group-hover:rotate-180 transition-transform duration-200"></i>
                        </button>
                        
                        <!-- Dropdown Popup -->
                        <div class="absolute left-0 top-full pt-2 w-64 hidden group-hover:block transition-all duration-200 z-50">
                            <div class="bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-100 p-2 space-y-1">
                                <a href="{{ route('kegiatan') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl hover:bg-red-50 text-slate-700 hover:text-pmr-primary transition {{ request()->routeIs('kegiatan') ? 'bg-red-50 text-pmr-primary font-bold' : '' }}">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-calendar-check text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold leading-tight">Kegiatan</div>
                                        <div class="text-[11px] text-slate-400 font-normal">Agenda & program kerja</div>
                                    </div>
                                </a>

                                <a href="{{ route('galeri') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl hover:bg-red-50 text-slate-700 hover:text-pmr-primary transition {{ request()->routeIs('galeri') ? 'bg-red-50 text-pmr-primary font-bold' : '' }}">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-images text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold leading-tight">Galeri</div>
                                        <div class="text-[11px] text-slate-400 font-normal">Dokumentasi foto & video</div>
                                    </div>
                                </a>

                                <a href="{{ route('donor-darah') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl hover:bg-red-50 text-slate-700 hover:text-pmr-primary transition {{ request()->routeIs('donor-darah') ? 'bg-red-50 text-pmr-primary font-bold' : '' }}">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-droplet text-sm text-red-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold leading-tight">Donor Darah</div>
                                        <div class="text-[11px] text-slate-400 font-normal">Jadwal & live stok darah</div>
                                    </div>
                                </a>

                                <a href="{{ route('artikel.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl hover:bg-red-50 text-slate-700 hover:text-pmr-primary transition {{ request()->routeIs('artikel.*') ? 'bg-red-50 text-pmr-primary font-bold' : '' }}">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-newspaper text-sm text-emerald-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold leading-tight">Artikel & Edukasi</div>
                                        <div class="text-[11px] text-slate-400 font-normal">Berita & wawasan P3K</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('lomba.index') }}" class="px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('lomba.*') ? 'bg-black/30 text-white font-bold shadow-inner' : 'hover:bg-white/10 text-amber-300 font-bold' }}">
                        <i class="fa-solid fa-trophy text-amber-400"></i>
                        <span>Lomba PMR</span>
                    </a>

                    <a href="{{ route('kontak') }}" class="px-4 py-2.5 rounded-xl transition {{ request()->routeIs('kontak') ? 'bg-black/30 text-white font-bold shadow-inner' : 'hover:bg-white/10 text-white/90' }}">
                        Kontak
                    </a>
                </nav>

                <!-- Action Button -->
                <div class="hidden sm:flex items-center gap-3 flex-shrink-0">
                    <a href="{{ route('kontak') }}#daftar" class="bg-white text-pmr-primary hover:bg-red-50 hover:shadow-xl font-extrabold text-xs uppercase tracking-wider px-6 py-3 rounded-full transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-hand-holding-heart text-red-600 text-sm"></i> Gabung Relawan
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex xl:hidden">
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="p-2.5 rounded-xl text-white hover:bg-white/10 focus:outline-none">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden xl:hidden bg-pmr-dark border-t border-red-900/60 px-4 pt-3 pb-6 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md font-semibold text-white hover:bg-white/10">Beranda</a>
            <a href="{{ route('tentang-kami') }}" class="block px-3 py-2 rounded-md font-semibold text-white hover:bg-white/10">Tentang Kami</a>
            
            <!-- Mobile Submenu Info -->
            <div class="bg-black/20 p-2.5 rounded-xl space-y-1">
                <div class="text-[11px] font-bold uppercase tracking-wider text-red-300 px-3 mb-1">Menu Info</div>
                <a href="{{ route('kegiatan') }}" class="block px-3 py-2 rounded-md font-semibold text-white hover:bg-white/10 flex items-center gap-2.5">
                    <i class="fa-solid fa-calendar-check text-xs text-red-300"></i> Kegiatan
                </a>
                <a href="{{ route('galeri') }}" class="block px-3 py-2 rounded-md font-semibold text-white hover:bg-white/10 flex items-center gap-2.5">
                    <i class="fa-solid fa-images text-xs text-blue-300"></i> Galeri
                </a>
                <a href="{{ route('donor-darah') }}" class="block px-3 py-2 rounded-md font-semibold text-white hover:bg-white/10 flex items-center gap-2.5">
                    <i class="fa-solid fa-droplet text-xs text-red-400"></i> Donor Darah
                </a>
                <a href="{{ route('artikel.index') }}" class="block px-3 py-2 rounded-md font-semibold text-white hover:bg-white/10 flex items-center gap-2.5">
                    <i class="fa-solid fa-newspaper text-xs text-emerald-300"></i> Artikel & Edukasi
                </a>
            </div>

            <a href="{{ route('lomba.index') }}" class="block px-3 py-2 rounded-md font-bold text-amber-300 hover:bg-white/10 flex items-center gap-2">
                <i class="fa-solid fa-trophy text-amber-400"></i> Lomba PMR (SBB III)
            </a>
            <a href="{{ route('kontak') }}" class="block px-3 py-2 rounded-md font-semibold text-white hover:bg-white/10">Kontak</a>
            <div class="pt-3">
                <a href="{{ route('kontak') }}#daftar" class="block text-center w-full bg-white text-pmr-primary font-bold py-2.5 rounded-full">
                    <i class="fa-solid fa-hand-holding-heart mr-1.5"></i> Gabung Relawan Sekarang
                </a>
            </div>
        </div>
    </header>

    <!-- Main Dynamic Content -->
    <main class="flex-grow">
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-6">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                    <div>
                        <div class="font-bold text-sm">Berhasil!</div>
                        <div class="text-xs">{{ session('success') }}</div>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-stone-900 text-stone-300 pt-16 pb-10 border-t-4 border-pmr-primary">
        <div class="w-full max-w-[1720px] mx-auto px-4 sm:px-8 lg:px-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                
                <!-- Col 1: Brand & Identity -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white rounded-2xl p-2.5 inline-block shadow-md">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo PMR Wira SMAN 1 Ciawi & PMI" class="h-14 w-auto object-contain">
                        </div>
                    </div>
                    <p class="text-xs text-stone-400 leading-relaxed">
                        Unit kegiatan ekstrakurikuler Palang Merah Remaja tingkat SMA yang bergerak dalam bidang pertolongan pertama, kesiapsiagaan bencana, donor darah, dan bakti sosial kemanusiaan.
                    </p>
                    <div class="pt-2 flex items-center gap-3">
                        <a href="https://instagram.com/pmrwirasman1c" target="_blank" class="w-8 h-8 rounded-full bg-stone-800 hover:bg-pmr-primary hover:text-white flex items-center justify-center text-xs transition">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-stone-800 hover:bg-pmr-primary hover:text-white flex items-center justify-center text-xs transition">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-stone-800 hover:bg-pmr-primary hover:text-white flex items-center justify-center text-xs transition">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4 border-l-2 border-pmr-primary pl-2.5">Navigasi Utama</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-angle-right text-[10px] text-pmr-primary"></i> Beranda</a></li>
                        <li><a href="{{ route('tentang-kami') }}" class="hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-angle-right text-[10px] text-pmr-primary"></i> Profil & Visi Misi</a></li>
                        <li><a href="{{ route('kegiatan') }}" class="hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-angle-right text-[10px] text-pmr-primary"></i> Agenda & Program Kerja</a></li>
                        <li><a href="{{ route('galeri') }}" class="hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-angle-right text-[10px] text-pmr-primary"></i> Galeri Dokumentasi</a></li>
                        <li><a href="{{ route('donor-darah') }}" class="hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-angle-right text-[10px] text-pmr-primary"></i> Donor Darah Sukarela</a></li>
                        <li><a href="{{ route('artikel.index') }}" class="hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-angle-right text-[10px] text-pmr-primary"></i> Artikel & Tips P3K</a></li>
                    </ul>
                </div>

                <!-- Col 3: Principles -->
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4 border-l-2 border-pmr-primary pl-2.5">7 Prinsip Dasar PMI</h4>
                    <div class="grid grid-cols-1 gap-1.5 text-xs text-stone-400">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-check text-red-500 text-[10px]"></i> Kemanusiaan (Humanity)</span>
                        <span class="flex items-center gap-2"><i class="fa-solid fa-check text-red-500 text-[10px]"></i> Kesamaan (Impartiality)</span>
                        <span class="flex items-center gap-2"><i class="fa-solid fa-check text-red-500 text-[10px]"></i> Kenetralan (Neutrality)</span>
                        <span class="flex items-center gap-2"><i class="fa-solid fa-check text-red-500 text-[10px]"></i> Kemandirian (Independence)</span>
                        <span class="flex items-center gap-2"><i class="fa-solid fa-check text-red-500 text-[10px]"></i> Kesukarelaan (Voluntary Service)</span>
                        <span class="flex items-center gap-2"><i class="fa-solid fa-check text-red-500 text-[10px]"></i> Kesatuan (Unity)</span>
                        <span class="flex items-center gap-2"><i class="fa-solid fa-check text-red-500 text-[10px]"></i> Kesemestaan (Universality)</span>
                    </div>
                </div>

                <!-- Col 4: Contact & Secretariat -->
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4 border-l-2 border-pmr-primary pl-2.5">Sekretariat</h4>
                    <ul class="space-y-3 text-xs text-stone-400">
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot text-red-500 mt-0.5"></i>
                            <span>Ruang UKS / PMR SMAN 1 Ciawi<br>Jl. Veteran No. 46, Pandansari, Ciawi, Bogor, Jawa Barat 16720</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-envelope text-red-500"></i>
                            <span>pmrwira@sman1ciawi.sch.id</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-phone text-red-500"></i>
                            <span>(0251) 824-0000 / +62 813-8388-5600</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 border-t border-stone-800 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-500 gap-3">
                <p>&copy; {{ date('Y') }} PMR WIRA SMAN 1 CIAWI. Menyatu untuk Kemanusiaan.</p>
                <div>
                    <a href="https://nazwagraha.com" target="_blank" class="hover:text-stone-300 font-medium transition flex items-center gap-1">
                        By. <span class="font-bold text-amber-400 hover:text-amber-300 hover:underline">Nazwagraha</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
