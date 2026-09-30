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
    <div class="bg-stone-900 text-white text-[11px] sm:text-xs py-2 px-3 sm:px-8 lg:px-12 border-b border-stone-800">
        <div class="w-full max-w-[1720px] mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-2 sm:gap-3">
                <span class="bg-pmr-primary text-white font-bold px-2 py-0.5 rounded text-[9px] sm:text-[10px] tracking-wider uppercase">Hotline UKS</span>
                <span class="text-stone-300 truncate max-w-[240px] sm:max-w-none">Piket Medis: <strong class="text-white">+62 812-3456-7890</strong></span>
            </div>
            <div class="flex items-center gap-3 sm:gap-4 text-stone-400">
                <a href="{{ route('donor-darah') }}" class="hover:text-red-400 transition flex items-center gap-1 font-medium">
                    <i class="fa-solid fa-droplet text-red-500"></i> <span class="hidden xs:inline">Info </span>Donor Darah
                </a>
                <span class="text-stone-600">|</span>
                <a href="{{ route('admin.login') }}" class="hover:text-white transition flex items-center gap-1 text-[11px] sm:text-xs font-medium">
                    <i class="fa-solid fa-lock"></i> Backoffice
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header (Lebar Penuh & Mewah) -->
    <header class="sticky top-0 z-50 bg-pmr-primary text-white shadow-xl border-b border-pmr-dark">
        <div class="w-full max-w-[1720px] mx-auto px-3 sm:px-8 lg:px-12">
            <div class="flex items-center justify-between h-20 sm:h-28">
                
                <!-- Logo & School Brand (Diperbesar & Sangat Jelas) -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-4 group flex-shrink-0">
                    <div class="h-14 sm:h-20 bg-white rounded-xl sm:rounded-2xl px-2.5 sm:px-4 py-1 sm:py-1.5 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo PMR Wira SMAN 1 Ciawi & PMI" class="h-11 sm:h-16 w-auto object-contain">
                    </div>
                    <div>
                        <div class="font-black text-sm sm:text-2xl tracking-tight leading-tight uppercase group-hover:text-red-100 transition">
                            PMR WIRA SMAN 1 CIAWI
                        </div>
                        <div class="text-[10px] sm:text-sm font-semibold text-red-200 tracking-wider">
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

                <!-- Mobile Menu Button (Modern & Responsive) -->
                <div class="flex xl:hidden items-center gap-2">
                    <a href="{{ route('lomba.index') }}" class="bg-amber-400 hover:bg-amber-300 text-stone-900 px-3 py-1.5 rounded-xl font-black text-xs shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-trophy text-amber-800 text-xs"></i> Lomba
                    </a>
                    <button type="button" onclick="toggleMobileMenu()" class="w-10 h-10 rounded-xl bg-white/15 hover:bg-white/25 active:scale-95 text-white border border-white/20 flex items-center justify-center transition focus:outline-none" aria-label="Menu Navigasi">
                        <i id="mobile-menu-icon" class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modern Mobile App-Style Menu Drawer -->
        <div id="mobile-menu" class="hidden xl:hidden bg-stone-900/98 backdrop-blur-xl border-t border-red-900/60 px-4 py-6 shadow-2xl transition-all duration-300 max-h-[85vh] overflow-y-auto">
            
            <!-- Quick Event Banner in Mobile Menu -->
            <a href="{{ route('lomba.index') }}" class="mb-4 block bg-gradient-to-r from-red-600 to-amber-600 p-4 rounded-2xl text-white shadow-lg border border-red-400/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                            🏆
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-amber-200 uppercase tracking-widest">Ajang Prestasi PMR</div>
                            <div class="text-sm font-black">SUA BHAKTI BERKARYA III</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-arrow-right text-xs bg-white/20 p-2 rounded-lg"></i>
                </div>
            </a>

            <!-- Primary Nav Grid (2 Columns) -->
            <div class="grid grid-cols-2 gap-2.5 mb-4">
                <a href="{{ route('home') }}" class="p-3.5 rounded-xl flex items-center gap-3 transition {{ request()->routeIs('home') ? 'bg-red-600 text-white font-bold shadow-md' : 'bg-stone-800/80 hover:bg-stone-700 text-stone-200' }}">
                    <i class="fa-solid fa-house text-red-400 text-sm"></i>
                    <span class="text-xs font-bold">Beranda</span>
                </a>
                <a href="{{ route('tentang-kami') }}" class="p-3.5 rounded-xl flex items-center gap-3 transition {{ request()->routeIs('tentang-kami') ? 'bg-red-600 text-white font-bold shadow-md' : 'bg-stone-800/80 hover:bg-stone-700 text-stone-200' }}">
                    <i class="fa-solid fa-shield-heart text-amber-400 text-sm"></i>
                    <span class="text-xs font-bold">Tentang Kami</span>
                </a>
                <a href="{{ route('kontak') }}" class="p-3.5 rounded-xl flex items-center gap-3 transition {{ request()->routeIs('kontak') ? 'bg-red-600 text-white font-bold shadow-md' : 'bg-stone-800/80 hover:bg-stone-700 text-stone-200' }}">
                    <i class="fa-solid fa-envelope text-blue-400 text-sm"></i>
                    <span class="text-xs font-bold">Kontak Kami</span>
                </a>
                <a href="{{ route('donor-darah') }}" class="p-3.5 rounded-xl flex items-center gap-3 transition {{ request()->routeIs('donor-darah') ? 'bg-red-600 text-white font-bold shadow-md' : 'bg-stone-800/80 hover:bg-stone-700 text-stone-200' }}">
                    <i class="fa-solid fa-droplet text-red-400 text-sm"></i>
                    <span class="text-xs font-bold">Donor Darah</span>
                </a>
            </div>

            <!-- Submenu Info Section -->
            <div class="bg-stone-800/60 rounded-2xl p-3.5 border border-stone-700/60 mb-4 space-y-1">
                <div class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 px-2 pb-1.5">Informasi & Publikasi</div>
                
                <a href="{{ route('kegiatan') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-700/80 text-stone-200 transition text-xs font-semibold">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-calendar-check text-red-400 w-4"></i> Agenda Kegiatan
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-stone-500"></i>
                </a>

                <a href="{{ route('galeri') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-700/80 text-stone-200 transition text-xs font-semibold">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-images text-blue-400 w-4"></i> Galeri Dokumentasi
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-stone-500"></i>
                </a>

                <a href="{{ route('artikel.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-700/80 text-stone-200 transition text-xs font-semibold">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-newspaper text-emerald-400 w-4"></i> Artikel & Edukasi P3K
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-stone-500"></i>
                </a>
            </div>

            <!-- Call to Action Button in Mobile Menu -->
            <div class="pt-2">
                <a href="{{ route('kontak') }}#daftar" class="block text-center w-full bg-red-600 hover:bg-red-700 text-white font-extrabold py-3.5 rounded-xl shadow-lg shadow-red-950/50 text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                    <i class="fa-solid fa-hand-holding-heart text-sm"></i> Formulir Calon Anggota PMR
                </a>
            </div>
        </div>
    </header>

    <!-- Main Dynamic Content (Mobile Bottom Padding so floating bar doesn't overlap) -->
    <main class="flex-grow pb-16 xl:pb-0">
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

    <!-- Floating Mobile Bottom Navigation Bar (App Experience, Hidden on Desktop xl:hidden) -->
    <nav class="xl:hidden fixed bottom-0 left-0 right-0 z-40 bg-stone-900/95 backdrop-blur-lg border-t border-stone-800/90 shadow-2xl px-2 py-1.5 flex items-center justify-around text-[10px] font-semibold text-stone-400">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('home') ? 'text-red-500 font-bold' : 'hover:text-stone-200' }}">
            <i class="fa-solid fa-house text-base"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('lomba.index') }}" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition relative {{ request()->routeIs('lomba.*') ? 'text-amber-400 font-bold' : 'text-amber-400/90 hover:text-amber-300' }}">
            <i class="fa-solid fa-trophy text-base text-amber-400 animate-pulse"></i>
            <span class="text-amber-400">Lomba</span>
            <span class="absolute -top-1 right-1 w-2 h-2 rounded-full bg-amber-400"></span>
        </a>
        <a href="{{ route('donor-darah') }}" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('donor-darah') ? 'text-red-500 font-bold' : 'hover:text-stone-200' }}">
            <i class="fa-solid fa-droplet text-base text-red-500"></i>
            <span>Donor</span>
        </a>
        <a href="{{ route('kontak') }}" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('kontak') ? 'text-red-500 font-bold' : 'hover:text-stone-200' }}">
            <i class="fa-solid fa-envelope text-base"></i>
            <span>Kontak</span>
        </a>
        <button type="button" onclick="toggleMobileMenu()" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl text-stone-400 hover:text-white active:scale-95 transition">
            <i class="fa-solid fa-bars text-base"></i>
            <span>Menu</span>
        </button>
    </nav>

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

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('mobile-menu-icon');
            if (menu) {
                const isHidden = menu.classList.contains('hidden');
                if (isHidden) {
                    menu.classList.remove('hidden');
                    if (icon) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-xmark');
                    }
                } else {
                    menu.classList.add('hidden');
                    if (icon) {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                }
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
