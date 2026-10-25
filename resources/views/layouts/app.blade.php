<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', trim($__env->yieldContent('title', 'PMR WIRA SMAN 1 CIAWI') . ' | Ragana Dwi Pantara 2026/2027'))</title>
    <meta name="description" content="@yield('meta_description', 'Portal Resmi PMR Wira SMAN 1 Ciawi - Unit Palang Merah Remaja Tingkat Wira Masa Bakti Ragana Dwi Pantara 2026/2027. Kemanusiaan, Kesiapsiagaan, dan Donor Darah.')">
    @yield('meta_tags')
    
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

    @php
        $hotlineUks = \App\Models\UksHotline::current();
    @endphp

    <!-- Top Emergency & Announcement Bar -->
    <div class="bg-stone-900 text-white text-[11px] sm:text-xs py-2 px-3 sm:px-8 lg:px-12 border-b border-stone-800">
        <div class="w-full max-w-[1720px] mx-auto flex flex-wrap justify-between items-center gap-2">
            <!-- Left: Layanan Siaga Medis UKS (Interaktif & Jelas Fungsinya) -->
            <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 {{ $hotlineUks->is_active ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30' : 'bg-stone-800 text-stone-400 border-stone-700' }} border font-extrabold px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] tracking-wider uppercase shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full {{ $hotlineUks->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-stone-500' }}"></span>
                    {{ $hotlineUks->label }}
                </span>

                <div class="flex items-center gap-2 text-stone-300 text-[11px] sm:text-xs">
                    <span class="hidden md:inline text-stone-400 font-medium">{{ $hotlineUks->service_name }}:</span>
                    <a href="{{ $hotlineUks->whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="font-black text-white hover:text-emerald-300 transition inline-flex items-center gap-1.5 group/phone" title="Klik untuk hubungi WhatsApp Piket Medis UKS">
                        <i class="fa-brands fa-whatsapp text-emerald-400 text-sm group-hover/phone:scale-110 transition"></i>
                        <span class="tracking-wide">{{ $hotlineUks->formatted_phone }}</span>
                    </a>
                    <button type="button" onclick="openUksHotlineModal()" class="inline-flex items-center gap-1 text-[10px] text-amber-300 hover:text-white bg-white/10 hover:bg-white/20 px-2 py-0.5 rounded-md transition font-bold cursor-pointer" title="Detail Layanan P3K Darurat UKS">
                        <i class="fa-solid fa-circle-info text-[9px]"></i>
                        <span>Info Siaga</span>
                    </button>
                </div>
            </div>

            <!-- Right: Donor Darah & Area Login -->
            <div class="flex items-center gap-3 sm:gap-4 text-stone-400">
                <a href="{{ route('donor-darah') }}" class="hover:text-red-400 transition flex items-center gap-1 font-medium">
                    <i class="fa-solid fa-droplet text-red-500"></i> <span class="hidden xs:inline">Info </span>Donor Darah
                </a>
                <span class="text-stone-600">|</span>
                @auth
                    @if(auth()->user()->canAccessBackoffice())
                        <a href="{{ route('admin.dashboard') }}" class="text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1 text-[11px] sm:text-xs font-bold">
                            <i class="fa-solid fa-gauge-high"></i> Backoffice
                        </a>
                    @else
                        <a href="{{ route('member.profile') }}" class="text-amber-300 hover:text-white transition flex items-center gap-1 text-[11px] sm:text-xs font-bold">
                            <i class="fa-solid fa-user-circle"></i> Area Anggota
                        </a>
                    @endif
                @else
                    <a href="{{ route('admin.login') }}" class="hover:text-white transition flex items-center gap-1 text-[11px] sm:text-xs font-medium">
                        <i class="fa-solid fa-lock"></i> Backoffice
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Alert Akses Ditolak Jika Login Gagal -->
    @if(session('auth_error'))
        <div id="auth-error-banner" class="bg-red-700 text-white text-xs sm:text-sm py-3 px-4 shadow-lg border-b border-red-800 transition-all duration-300">
            <div class="max-w-[1720px] mx-auto flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 font-semibold">
                    <span class="w-7 h-7 rounded-full bg-white text-red-700 flex items-center justify-center font-black flex-shrink-0">
                        <i class="fa-solid fa-ban"></i>
                    </span>
                    <span><strong>Akses Ditolak:</strong> {{ session('auth_error') }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.login') }}" class="px-3 py-1 bg-white text-red-700 hover:bg-red-100 rounded-lg text-xs font-bold transition">
                        Coba Lagi
                    </a>
                    <button type="button" onclick="document.getElementById('auth-error-banner').remove()" class="text-red-200 hover:text-white p-1">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Navigation Header (Ramping, Mewah & Mobile-First) -->
    <header class="sticky top-0 z-50 bg-pmr-primary text-white shadow-xl border-b border-pmr-dark">
        <div class="w-full max-w-[1720px] mx-auto px-3 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-16 sm:h-20 lg:h-22 gap-2 sm:gap-4">
                
                <!-- Logo & Brand Identity (Proporsional & Bebas Truncate / Ellipsis) -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3.5 group min-w-0 flex-shrink-0">
                    <div class="h-10 sm:h-12 lg:h-14 bg-white rounded-xl sm:rounded-2xl px-2 sm:px-3 py-1 flex items-center justify-center shadow-md group-hover:scale-105 transition-transform duration-200 flex-shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo PMR Wira SMAN 1 Ciawi & PMI" class="h-7 sm:h-9 lg:h-11 w-auto object-contain">
                    </div>
                    <div class="flex flex-col justify-center min-w-0">
                        <!-- Tampilan di Mobile (< 640px): Tegas & Tanpa Truncate -->
                        <div class="block sm:hidden">
                            <span class="font-black text-sm text-white tracking-tight leading-tight uppercase block whitespace-nowrap">
                                PMR WIRA
                            </span>
                            <span class="text-[10px] text-red-200 font-bold tracking-normal leading-none block whitespace-nowrap">
                                SMAN 1 Ciawi
                            </span>
                        </div>

                        <!-- Tampilan di Tablet & Desktop (>= 640px) -->
                        <div class="hidden sm:block">
                            <div class="font-black text-base lg:text-xl text-white tracking-tight leading-tight uppercase group-hover:text-red-100 transition whitespace-nowrap">
                                PMR WIRA SMAN 1 CIAWI
                            </div>
                            <div class="text-[10px] lg:text-xs font-semibold text-red-200 tracking-wider whitespace-nowrap">
                                Unit Relawan Siswa &bull; Masa Bakti 2026/2027
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Desktop Menu (Satu Baris Sejajar, Rapi & Elegan - Tampil di Desktop xl:) -->
                <nav class="hidden xl:flex items-center gap-1.5 font-semibold text-[14px] 2xl:text-[15px]">
                    <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ request()->routeIs('home') ? 'bg-black/25 text-white font-bold shadow-inner' : 'hover:bg-white/10 text-white/90' }}">
                        Beranda
                    </a>

                    <!-- Dropdown Menu: Tentang Kami (Profil, Prestasi, Pedoman Organisasi) -->
                    @php
                        $isAboutActive = request()->routeIs('tentang-kami') || request()->routeIs('pedoman-organisasi') || request()->is('prestasi*');
                    @endphp
                    <div class="relative group">
                        <button type="button" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 whitespace-nowrap {{ $isAboutActive ? 'bg-black/25 text-white font-bold shadow-inner' : 'hover:bg-white/10 text-white/90' }}">
                            <span>Tentang Kami</span>
                            <i class="fa-solid fa-chevron-down text-[11px] text-red-200 group-hover:rotate-180 transition-transform duration-200"></i>
                        </button>
                        
                        <!-- Dropdown Popup -->
                        <div class="absolute left-0 top-full pt-2 w-80 hidden group-hover:block transition-all duration-200 z-50">
                            <div class="bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-100 p-2 space-y-1">
                                <a href="{{ route('tentang-kami') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl hover:bg-red-50 text-slate-700 hover:text-pmr-primary transition {{ request()->routeIs('tentang-kami') ? 'bg-red-50 text-pmr-primary font-bold' : '' }}">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-shield-heart text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold leading-tight">Profil & Kepengurusan</div>
                                        <div class="text-[11px] text-slate-400 font-normal">Sejarah, visi misi & bagan pengurus</div>
                                    </div>
                                </a>

                                <a href="{{ url('/prestasi') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-800 transition {{ request()->is('prestasi*') ? 'bg-amber-50 text-amber-800 font-bold' : '' }}">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-trophy text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold leading-tight flex items-center gap-1.5">
                                            <span>Etalase Prestasi</span>
                                            <span class="bg-amber-100 text-amber-700 text-[10px] px-1.5 py-0.5 rounded-full font-black uppercase">Juara</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-normal">Koleksi trofi & penghargaan ajang</div>
                                    </div>
                                </a>

                                <a href="{{ route('pedoman-organisasi') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl hover:bg-red-50 text-slate-700 hover:text-pmr-primary transition {{ request()->routeIs('pedoman-organisasi') ? 'bg-red-50 text-pmr-primary font-bold' : '' }}">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-book-bookmark text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold leading-tight">Pedoman Organisasi</div>
                                        <div class="text-[11px] text-slate-400 font-normal">GBHO 2026 & PPUO 2026 (27 Bab)</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown Menu: Info & Kegiatan (Kegiatan, Galeri, Donor Darah, Artikel) -->
                    @php
                        $isInfoActive = request()->routeIs('kegiatan') || request()->routeIs('galeri') || request()->routeIs('donor-darah') || request()->routeIs('artikel.*');
                    @endphp
                    <div class="relative group">
                        <button type="button" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 whitespace-nowrap {{ $isInfoActive ? 'bg-black/25 text-white font-bold shadow-inner' : 'hover:bg-white/10 text-white/90' }}">
                            <span>Info & Kegiatan</span>
                            <i class="fa-solid fa-chevron-down text-[11px] text-red-200 group-hover:rotate-180 transition-transform duration-200"></i>
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

                    <!-- Menu Lomba PMR -->
                    <a href="{{ route('lomba.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('lomba.*') ? 'bg-black/25 text-amber-300 font-bold shadow-inner' : 'hover:bg-white/10 text-amber-300 font-bold' }}">
                        <i class="fa-solid fa-medal text-amber-400"></i>
                        <span>Lomba PMR</span>
                    </a>

                    <!-- Kontak -->
                    <a href="{{ route('kontak') }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ request()->routeIs('kontak') ? 'bg-black/25 text-white font-bold shadow-inner' : 'hover:bg-white/10 text-white/90' }}">
                        Kontak
                    </a>
                </nav>

                <!-- Action Button Gabung Relawan (Desktop Only) -->
                <div class="hidden xl:flex items-center gap-3 flex-shrink-0">
                    <a href="{{ route('kontak') }}#daftar" class="bg-white text-pmr-primary hover:bg-red-50 hover:shadow-xl font-extrabold text-xs uppercase tracking-wider px-5 py-2.5 rounded-full transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 shadow-md flex items-center gap-2 whitespace-nowrap">
                        <i class="fa-solid fa-hand-holding-heart text-red-600"></i>
                        <span>Gabung Relawan</span>
                    </a>
                </div>

                <!-- Mobile Header Actions (Sleek & Thumb-Friendly) -->
                <div class="flex xl:hidden items-center gap-2 flex-shrink-0">
                    <!-- Quick Trophy Shortcut -->
                    <a href="{{ route('lomba.index') }}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-400 hover:bg-amber-300 text-stone-900 shadow-md flex items-center justify-center transition active:scale-95 flex-shrink-0" title="Ajang Lomba PMR">
                        <i class="fa-solid fa-trophy text-amber-950 text-xs sm:text-sm"></i>
                    </a>

                    <!-- Hamburger Button Modern -->
                    <button type="button" onclick="toggleMobileMenu()" class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white/15 hover:bg-white/25 active:scale-95 text-white border border-white/20 shadow-md flex items-center justify-center transition focus:outline-none flex-shrink-0" aria-label="Buka Menu Navigasi">
                        <i id="mobile-menu-icon" class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modern Mobile App-Style Menu Drawer (Exact Match to Mockup) -->
        <div id="mobile-menu" class="hidden xl:hidden bg-stone-950/98 backdrop-blur-2xl border-t border-red-900/40 px-4 py-5 sm:px-6 shadow-2xl transition-all duration-300 max-h-[85vh] overflow-y-auto">
            
            <!-- Quick Event Banner Emas (Mockup State 2) -->
            <a href="{{ route('lomba.index') }}" class="mb-4 block bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 text-stone-950 p-3.5 rounded-2xl shadow-lg border border-amber-300/40 hover:opacity-95 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-stone-950/15 flex items-center justify-center text-lg flex-shrink-0">
                            🏆
                        </div>
                        <div class="min-w-0">
                            <div class="text-[9px] font-black uppercase tracking-widest text-amber-950/80">Ajang Prestasi PMR Wira</div>
                            <div class="text-xs sm:text-sm font-black truncate">LOMBA TINGKAT JAWA BARAT</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs bg-stone-950/15 px-2 py-1.5 rounded-lg"></i>
                </div>
            </a>

            <!-- Primary Nav Grid (2 Columns App-Style Icons - Mockup Match) -->
            <div class="grid grid-cols-2 gap-2.5 mb-4">
                <a href="{{ route('home') }}" class="p-3 rounded-2xl flex flex-col items-center text-center gap-1.5 transition {{ request()->routeIs('home') ? 'bg-red-600 text-white shadow-lg font-bold' : 'bg-stone-900/90 border border-stone-800/80 text-stone-200 hover:bg-stone-800' }}">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-red-400 text-base">
                        <i class="fa-solid fa-house"></i>
                    </div>
                    <span class="text-xs font-bold">Beranda</span>
                </a>

                <a href="{{ route('tentang-kami') }}" class="p-3 rounded-2xl flex flex-col items-center text-center gap-1.5 transition {{ request()->routeIs('tentang-kami') ? 'bg-red-600 text-white shadow-lg font-bold' : 'bg-stone-900/90 border border-stone-800/80 text-stone-200 hover:bg-stone-800' }}">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-amber-400 text-base">
                        <i class="fa-solid fa-shield-heart"></i>
                    </div>
                    <span class="text-xs font-bold">Profil & Struktur</span>
                </a>

                <a href="{{ url('/prestasi') }}" class="p-3 rounded-2xl flex flex-col items-center text-center gap-1.5 transition {{ request()->is('prestasi*') ? 'bg-amber-500 text-stone-950 shadow-lg font-black' : 'bg-stone-900/90 border border-stone-800/80 text-stone-200 hover:bg-stone-800' }}">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-amber-400 text-base">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <span class="text-xs font-bold">Etalase Prestasi</span>
                </a>

                <a href="{{ route('donor-darah') }}" class="p-3 rounded-2xl flex flex-col items-center text-center gap-1.5 transition {{ request()->routeIs('donor-darah') ? 'bg-red-600 text-white shadow-lg font-bold' : 'bg-stone-900/90 border border-stone-800/80 text-stone-200 hover:bg-stone-800' }}">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-red-500 text-base">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <span class="text-xs font-bold">Donor Darah</span>
                </a>

                <a href="{{ route('kegiatan') }}" class="p-3 rounded-2xl flex flex-col items-center text-center gap-1.5 transition {{ request()->routeIs('kegiatan*') ? 'bg-red-600 text-white shadow-lg font-bold' : 'bg-stone-900/90 border border-stone-800/80 text-stone-200 hover:bg-stone-800' }}">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-emerald-400 text-base">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <span class="text-xs font-bold">Agenda Kegiatan</span>
                </a>

                <a href="{{ route('galeri') }}" class="p-3 rounded-2xl flex flex-col items-center text-center gap-1.5 transition {{ request()->routeIs('galeri') ? 'bg-red-600 text-white shadow-lg font-bold' : 'bg-stone-900/90 border border-stone-800/80 text-stone-200 hover:bg-stone-800' }}">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-blue-400 text-base">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <span class="text-xs font-bold">Galeri Dokumentasi</span>
                </a>
            </div>

            <!-- Submenu Regulasi & Publikasi (Sleek List - Mockup Match) -->
            <div class="bg-stone-900/80 rounded-2xl p-3 border border-stone-800/80 mb-4 space-y-1">
                <div class="text-[10px] font-black uppercase tracking-widest text-stone-400 px-2 pb-1.5 border-b border-stone-800/60 mb-1">
                    Regulasi & Informasi
                </div>

                <a href="{{ route('pedoman-organisasi') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-800 text-stone-300 transition text-xs font-semibold {{ request()->routeIs('pedoman-organisasi') ? 'bg-amber-500/20 text-amber-300 font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-book-bookmark text-amber-400 w-4"></i> Pedoman Organisasi (GBHO & PPUO)
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-stone-500"></i>
                </a>

                <a href="{{ route('artikel.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-800 text-stone-300 transition text-xs font-semibold {{ request()->routeIs('artikel.*') ? 'bg-emerald-500/20 text-emerald-300 font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-newspaper text-emerald-400 w-4"></i> Artikel & Wawasan P3K
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-stone-500"></i>
                </a>

                <!-- Emergency Hotline UKS Quick Box (Mobile) -->
                <div class="p-3 rounded-2xl bg-gradient-to-br from-red-950/90 to-stone-900 border border-red-800/40 text-white space-y-2 shadow-inner my-1">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase text-amber-300">
                            <span class="w-1.5 h-1.5 rounded-full {{ $hotlineUks->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-stone-500' }}"></span>
                            {{ $hotlineUks->label }}
                        </span>
                        <span class="text-[9px] text-emerald-300 font-bold bg-emerald-500/10 px-2 py-0.2 rounded-full border border-emerald-500/20">
                            {{ $hotlineUks->status_text }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-stone-300 text-[10px]">{{ $hotlineUks->service_name }}</span>
                        <span class="font-black text-white font-mono">{{ $hotlineUks->formatted_phone }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-1.5 pt-0.5">
                        <a href="{{ $hotlineUks->whatsapp_url }}" target="_blank" class="py-1.5 px-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold flex items-center justify-center gap-1.5 shadow-xs">
                            <i class="fa-brands fa-whatsapp"></i> Chat WA
                        </a>
                        <a href="{{ $hotlineUks->tel_url }}" class="py-1.5 px-2 rounded-lg bg-white/15 hover:bg-white/25 text-white text-[11px] font-bold flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-phone text-[10px]"></i> Panggil
                        </a>
                    </div>
                </div>

                <a href="{{ route('kontak') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-800 text-stone-300 transition text-xs font-semibold {{ request()->routeIs('kontak') ? 'bg-red-500/20 text-red-300 font-bold' : '' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-phone text-red-400 w-4"></i> Kontak Sekretariat & Hotline UKS
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-stone-500"></i>
                </a>
            </div>

            <!-- Call to Action Button in Mobile Menu (Full Width White Button - Mockup Match) -->
            <div class="space-y-2 pt-1">
                <a href="{{ route('kontak') }}#daftar" class="block text-center w-full bg-white hover:bg-red-50 text-pmr-primary font-black py-3.5 rounded-xl shadow-xl text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition active:scale-95">
                    <i class="fa-solid fa-hand-holding-heart text-red-600 text-sm"></i> Daftar Jadi Relawan PMR
                </a>

                @auth
                    @if(auth()->user()->canAccessBackoffice())
                        <a href="{{ route('admin.dashboard') }}" class="block text-center w-full bg-stone-900 border border-stone-800 text-emerald-400 hover:text-emerald-300 font-bold py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 transition">
                            <i class="fa-solid fa-gauge-high"></i> Masuk Panel Backoffice
                        </a>
                    @else
                        <a href="{{ route('member.profile') }}" class="block text-center w-full bg-stone-900 border border-stone-800 text-amber-300 hover:text-amber-200 font-bold py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 transition">
                            <i class="fa-solid fa-user-circle"></i> Buka Area Biodata Anggota
                        </a>
                    @endif
                @else
                    <a href="{{ route('admin.login') }}" class="block text-center w-full bg-stone-900/60 border border-stone-800/80 text-stone-400 hover:text-white font-medium py-2 rounded-xl text-[11px] flex items-center justify-center gap-2 transition">
                        <i class="fa-solid fa-lock text-xs"></i> Portal Login Admin & Pengurus
                    </a>
                @endauth
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
                        <li><a href="{{ url('/prestasi') }}" class="hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-angle-right text-[10px] text-amber-400"></i> Etalase Prestasi Juara</a></li>
                        <li><a href="{{ route('pedoman-organisasi') }}" class="hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-angle-right text-[10px] text-pmr-primary"></i> Pedoman Organisasi (GBHO & PPUO)</a></li>
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
                            <span>Jl. Veteran III Banjarwaru, Ciawi, Bogor, Jawa Barat 16720</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-envelope text-red-500"></i>
                            <a href="mailto:pmrwirasman1c@gmail.com" class="hover:text-white transition">pmrwirasman1c@gmail.com</a>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                            <a href="https://wa.me/6285770246918" target="_blank" class="hover:text-white transition">085770246918</a>
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

    {{-- ========================================================================= --}}
    {{-- FLOATING MARS PMI AUDIO PLAYER (Global Background Music)                  --}}
    {{-- ========================================================================= --}}
    @php
        $marsPmiMusic = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('background_musics')) {
                $marsPmiMusic = \App\Models\BackgroundMusic::first();
            }
        } catch (\Throwable $e) {}

        if (!$marsPmiMusic) {
            $marsPmiMusic = (object)[
                'title' => 'Mars Palang Merah Indonesia',
                'subtitle' => 'Ciptaan: Mochtar H. S. • PMR Wira SMAN 1 Ciawi',
                'audio_url' => asset('audio/mars-pmi.m4a'),
                'is_active' => true,
                'autoplay_on_first_interaction' => true,
                'default_volume' => 30,
                'loop' => true,
                'lyrics' => "Palang Merah Indonesia\nSumber kasih umat manusia\nWarisan luhur, nusa dan bangsa\nWujud nyata pengayom Pancasila\n\nGerak juangnya ke seluruh nusa\nMendarmabaktikan bagi ampera\nTunaikan tugas suci tujuan PMI\nDi persada bunda pertiwi\n\nUntuk umat manusia\nDi seluruh dunia\nPMI menghantarkan jasa",
            ];
        }
    @endphp

    @if($marsPmiMusic && $marsPmiMusic->is_active)
        <!-- Hidden Audio Element -->
        <audio id="mars-pmi-player-audio" preload="auto" {{ $marsPmiMusic->loop ? 'loop' : '' }}>
            <source src="{{ $marsPmiMusic->audio_url }}" type="audio/mpeg">
            <source src="{{ $marsPmiMusic->audio_url }}" type="audio/mp4">
            @if(file_exists(public_path('audio/mars-pmi.mp3')))
                <source src="{{ asset('audio/mars-pmi.mp3') }}" type="audio/mpeg">
            @endif
            @if(file_exists(public_path('audio/mars-pmi.m4a')))
                <source src="{{ asset('audio/mars-pmi.m4a') }}" type="audio/mp4">
            @endif
        </audio>

        <!-- Floating Player Container (Bottom Left) -->
        <div id="mars-pmi-floating-widget" class="fixed bottom-4 left-4 z-50 select-none font-sans">
            
            <!-- Compact Pill Bar (Default View) -->
            <div id="mars-pmi-pill" class="flex items-center gap-2.5 bg-stone-900/95 hover:bg-stone-900 text-white pl-2.5 pr-3 py-2 rounded-full shadow-2xl border border-white/20 backdrop-blur-md transition-all duration-300 transform hover:scale-102">
                
                <!-- Play/Pause Toggle Button with Animated Disc / Equalizer -->
                <button type="button" onclick="toggleMarsPmiPlay()" class="w-8 h-8 rounded-full bg-pmr-primary hover:bg-rose-600 text-white flex items-center justify-center text-xs shadow-md transition flex-shrink-0 relative group" title="Putar / Jeda Mars PMI">
                    <i id="mars-pmi-play-icon" class="fa-solid fa-play text-[11px] ml-0.5"></i>
                    
                    <!-- Pulse ping effect when playing -->
                    <span id="mars-pmi-ping" class="absolute inset-0 rounded-full bg-rose-500 animate-ping opacity-40 hidden"></span>
                </button>

                <!-- Equalizer Waves Animation (Active when playing) -->
                <div class="flex items-center gap-0.5 h-4 px-1" onclick="toggleMarsPmiPlay()" style="cursor: pointer;">
                    <span class="w-1 h-2 bg-rose-500 rounded-full mars-eq-bar [animation-delay:0.1s]"></span>
                    <span class="w-1 h-3.5 bg-amber-400 rounded-full mars-eq-bar [animation-delay:0.3s]"></span>
                    <span class="w-1 h-4 bg-red-500 rounded-full mars-eq-bar [animation-delay:0.2s]"></span>
                    <span class="w-1 h-2.5 bg-amber-300 rounded-full mars-eq-bar [animation-delay:0.4s]"></span>
                </div>

                <!-- Title & Status Info -->
                <div class="flex flex-col cursor-pointer pr-1" onclick="toggleMarsPmiPlay()">
                    <span class="text-[11px] font-black tracking-tight text-white leading-tight flex items-center gap-1.5">
                        <span>Mars PMI</span>
                        <span id="mars-pmi-status-badge" class="text-[9px] font-bold text-amber-300 uppercase px-1.5 py-0.2 rounded bg-white/10">Audio</span>
                    </span>
                    <span id="mars-pmi-subtext" class="text-[9px] text-stone-400 leading-none mt-0.5">Klik untuk memutar</span>
                </div>

                <!-- Button to Open Expanded Panel & Lyrics -->
                <button type="button" onclick="toggleMarsPmiPanel()" class="w-6 h-6 rounded-full bg-white/10 hover:bg-white/25 text-stone-300 hover:text-white flex items-center justify-center text-[10px] transition ml-1" title="Lihat kontrol & lirik lagu">
                    <i class="fa-solid fa-sliders"></i>
                </button>
            </div>

            <!-- Expanded Control & Lyrics Panel Card (Hidden by Default) -->
            <div id="mars-pmi-panel" class="hidden absolute bottom-12 left-0 w-80 sm:w-88 bg-stone-900/98 text-white rounded-3xl p-5 shadow-2xl border border-white/20 backdrop-blur-xl mb-2 animate-in fade-in slide-in-from-bottom-3 duration-200">
                
                <!-- Panel Header -->
                <div class="flex items-start justify-between border-b border-white/10 pb-3 mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-pmr-primary flex items-center justify-center text-white text-sm shadow-md">
                            <i class="fa-solid fa-music"></i>
                        </div>
                        <div>
                            <div class="text-xs font-black text-white leading-tight">Mars PMI</div>
                            <div class="text-[10px] text-stone-400">Karya: Mochtar H. S.</div>
                        </div>
                    </div>
                    <button type="button" onclick="toggleMarsPmiPanel()" class="w-6 h-6 rounded-full bg-white/10 hover:bg-white/20 text-stone-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Time Progress & Seekbar -->
                <div class="space-y-1.5 mb-3">
                    <div class="w-full bg-white/15 h-1.5 rounded-full overflow-hidden cursor-pointer" onclick="seekMarsAudio(event)">
                        <div id="mars-pmi-progress-bar" class="bg-gradient-to-r from-amber-400 to-rose-500 h-full w-0 transition-all duration-150"></div>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-mono text-stone-400">
                        <span id="mars-pmi-time-current">00:00</span>
                        <span id="mars-pmi-time-duration">01:30</span>
                    </div>
                </div>

                <!-- Volume & Control Bar -->
                <div class="flex items-center justify-between gap-3 bg-white/5 p-2.5 rounded-2xl border border-white/10 mb-3">
                    <button type="button" onclick="toggleMarsMute()" id="mars-pmi-mute-btn" class="w-7 h-7 rounded-lg bg-white/10 text-stone-300 hover:text-white flex items-center justify-center text-xs">
                        <i class="fa-solid fa-volume-high" id="mars-pmi-mute-icon"></i>
                    </button>
                    <input type="range" id="mars-pmi-vol-slider" min="0" max="100" step="5" value="{{ $marsPmiMusic->default_volume ?? 30 }}" oninput="setMarsVolume(this.value)" class="flex-grow h-1.5 bg-white/20 rounded-lg appearance-none cursor-pointer accent-pmr-primary">
                    <span id="mars-pmi-vol-text" class="text-[10px] font-bold text-stone-400 w-8 text-right">{{ $marsPmiMusic->default_volume ?? 30 }}%</span>
                </div>

                <!-- Lyrics Accordion / View -->
                <div class="space-y-2">
                    <button type="button" onclick="toggleMarsLyrics()" class="w-full py-2 px-3 rounded-xl bg-white/10 hover:bg-white/15 text-stone-200 text-xs font-bold flex items-center justify-between transition">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-book-open text-amber-400 text-[11px]"></i>
                            <span>Buka Lirik Lagu Mars PMI</span>
                        </span>
                        <i id="mars-pmi-lyrics-chevron" class="fa-solid fa-chevron-down text-[10px] transition-transform"></i>
                    </button>

                    <div id="mars-pmi-lyrics-box" class="hidden max-h-48 overflow-y-auto p-3.5 rounded-2xl bg-stone-950/80 border border-white/10 text-stone-300 text-[11px] leading-relaxed font-serif whitespace-pre-line custom-scrollbar italic text-center">
{{ $marsPmiMusic->lyrics }}
                    </div>
                </div>

            </div>

        </div>

        <style>
            .mars-eq-bar {
                animation: marsEqualizer 1s ease-in-out infinite alternate;
                animation-play-state: paused;
            }
            .mars-playing .mars-eq-bar {
                animation-play-state: running;
            }
            @keyframes marsEqualizer {
                0% { height: 4px; }
                50% { height: 14px; }
                100% { height: 6px; }
            }
        </style>

        <script>
            let marsAudio = null;
            let marsIsPlaying = false;
            let marsUserHasInteracted = false;
            const marsDefaultVolume = {{ ($marsPmiMusic->default_volume ?? 30) / 100 }};
            const marsAutoplayAllowed = {{ $marsPmiMusic->autoplay_on_first_interaction ? 'true' : 'false' }};

            document.addEventListener('DOMContentLoaded', () => {
                marsAudio = document.getElementById('mars-pmi-player-audio');
                if (!marsAudio) return;

                // Set initial volume
                const savedVol = localStorage.getItem('pmr_mars_volume');
                if (savedVol !== null) {
                    marsAudio.volume = parseFloat(savedVol);
                    const slider = document.getElementById('mars-pmi-vol-slider');
                    const text = document.getElementById('mars-pmi-vol-text');
                    if (slider) slider.value = Math.round(parseFloat(savedVol) * 100);
                    if (text) text.textContent = Math.round(parseFloat(savedVol) * 100) + '%';
                } else {
                    marsAudio.volume = marsDefaultVolume;
                }

                // Audio events
                marsAudio.addEventListener('timeupdate', updateMarsProgress);
                marsAudio.addEventListener('ended', onMarsEnded);
                marsAudio.addEventListener('play', () => setMarsPlayingState(true));
                marsAudio.addEventListener('pause', () => setMarsPlayingState(false));

                // Check user preference
                const userPausedManually = localStorage.getItem('pmr_mars_paused') === 'true';

                if (!userPausedManually && marsAutoplayAllowed) {
                    // Try direct autoplay (some browsers allow if user previously interacted with domain)
                    const playPromise = marsAudio.play();
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            setMarsPlayingState(true);
                        }).catch(() => {
                            // Browser Autoplay Policy blocked - register global gesture trigger
                            setupSmartGestureAutoplay();
                        });
                    }
                }
            });

            function setupSmartGestureAutoplay() {
                const triggerPlay = () => {
                    if (marsUserHasInteracted) return;
                    marsUserHasInteracted = true;

                    const userPausedManually = localStorage.getItem('pmr_mars_paused') === 'true';
                    if (!userPausedManually && marsAudio && marsAudio.paused) {
                        marsAudio.play().then(() => {
                            setMarsPlayingState(true);
                        }).catch(() => {});
                    }

                    // Remove listeners once interacted
                    ['click', 'touchstart', 'keydown'].forEach(evt => {
                        document.removeEventListener(evt, triggerPlay);
                    });
                };

                ['click', 'touchstart', 'keydown'].forEach(evt => {
                    document.addEventListener(evt, triggerPlay, { passive: true });
                });
            }

            function toggleMarsPmiPlay() {
                if (!marsAudio) return;
                marsUserHasInteracted = true;

                if (marsAudio.paused) {
                    localStorage.setItem('pmr_mars_paused', 'false');
                    marsAudio.play().then(() => {
                        setMarsPlayingState(true);
                    }).catch(err => {
                        console.log('Audio play error:', err);
                    });
                } else {
                    localStorage.setItem('pmr_mars_paused', 'true');
                    marsAudio.pause();
                    setMarsPlayingState(false);
                }
            }

            function setMarsPlayingState(playing) {
                marsIsPlaying = playing;
                const widget = document.getElementById('mars-pmi-floating-widget');
                const playIcon = document.getElementById('mars-pmi-play-icon');
                const subtext = document.getElementById('mars-pmi-subtext');
                const badge = document.getElementById('mars-pmi-status-badge');
                const ping = document.getElementById('mars-pmi-ping');

                if (playing) {
                    if (widget) widget.classList.add('mars-playing');
                    if (playIcon) {
                        playIcon.classList.remove('fa-play', 'ml-0.5');
                        playIcon.classList.add('fa-pause');
                    }
                    if (subtext) subtext.textContent = 'Sedang Berputar...';
                    if (badge) {
                        badge.textContent = 'Memutar';
                        badge.classList.remove('text-amber-300', 'bg-white/10');
                        badge.classList.add('text-emerald-300', 'bg-emerald-500/20');
                    }
                    if (ping) ping.classList.remove('hidden');
                } else {
                    if (widget) widget.classList.remove('mars-playing');
                    if (playIcon) {
                        playIcon.classList.remove('fa-pause');
                        playIcon.classList.add('fa-play', 'ml-0.5');
                    }
                    if (subtext) subtext.textContent = 'Klik untuk memutar';
                    if (badge) {
                        badge.textContent = 'Jeda';
                        badge.classList.remove('text-emerald-300', 'bg-emerald-500/20');
                        badge.classList.add('text-amber-300', 'bg-white/10');
                    }
                    if (ping) ping.classList.add('hidden');
                }
            }

            function updateMarsProgress() {
                if (!marsAudio || !marsAudio.duration) return;
                const pct = (marsAudio.currentTime / marsAudio.duration) * 100;
                const bar = document.getElementById('mars-pmi-progress-bar');
                const currentEl = document.getElementById('mars-pmi-time-current');
                const durationEl = document.getElementById('mars-pmi-time-duration');

                if (bar) bar.style.width = pct + '%';
                if (currentEl) currentEl.textContent = formatMarsTime(marsAudio.currentTime);
                if (durationEl) durationEl.textContent = formatMarsTime(marsAudio.duration);
            }

            function onMarsEnded() {
                setMarsPlayingState(false);
            }

            function seekMarsAudio(e) {
                if (!marsAudio || !marsAudio.duration) return;
                const rect = e.currentTarget.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                marsAudio.currentTime = pos * marsAudio.duration;
            }

            function setMarsVolume(val) {
                if (!marsAudio) return;
                const v = val / 100;
                marsAudio.volume = v;
                localStorage.setItem('pmr_mars_volume', v.toString());
                const text = document.getElementById('mars-pmi-vol-text');
                const icon = document.getElementById('mars-pmi-mute-icon');
                if (text) text.textContent = val + '%';
                if (icon) {
                    if (v === 0) {
                        icon.className = 'fa-solid fa-volume-xmark text-rose-400';
                    } else if (v < 0.5) {
                        icon.className = 'fa-solid fa-volume-low';
                    } else {
                        icon.className = 'fa-solid fa-volume-high';
                    }
                }
            }

            function toggleMarsMute() {
                if (!marsAudio) return;
                const slider = document.getElementById('mars-pmi-vol-slider');
                if (marsAudio.volume > 0) {
                    marsAudio.dataset.prevVol = marsAudio.volume;
                    setMarsVolume(0);
                    if (slider) slider.value = 0;
                } else {
                    const prev = marsAudio.dataset.prevVol ? parseFloat(marsAudio.dataset.prevVol) * 100 : 30;
                    setMarsVolume(prev);
                    if (slider) slider.value = prev;
                }
            }

            function toggleMarsPmiPanel() {
                const panel = document.getElementById('mars-pmi-panel');
                if (panel) {
                    panel.classList.toggle('hidden');
                }
            }

            function toggleMarsLyrics() {
                const box = document.getElementById('mars-pmi-lyrics-box');
                const chevron = document.getElementById('mars-pmi-lyrics-chevron');
                if (box) {
                    box.classList.toggle('hidden');
                    if (chevron) {
                        chevron.classList.toggle('rotate-180');
                    }
                }
            }

            function formatMarsTime(sec) {
                const m = Math.floor(sec / 60);
                const s = Math.floor(sec % 60);
                return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
            }
        </script>
    @endif

    <!-- Modal Dialog Informasi & Siaga Hotline UKS -->
    <div id="uks-hotline-modal" class="fixed inset-0 z-[120] hidden items-center justify-center p-4 bg-stone-950/80 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-stone-200 animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="relative bg-gradient-to-r from-stone-900 via-stone-850 to-red-950 text-white p-6 sm:p-7 border-b border-white/10">
                <button type="button" onclick="closeUksHotlineModal()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-stone-300 hover:text-white flex items-center justify-center transition cursor-pointer" aria-label="Tutup Dialog">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="inline-flex items-center gap-1.5 {{ $hotlineUks->is_active ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30' : 'bg-stone-800 text-stone-400 border-stone-700' }} border px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full {{ $hotlineUks->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-stone-500' }}"></span>
                        {{ $hotlineUks->label }}
                    </span>
                    <span class="text-xs text-stone-300 font-semibold">{{ $hotlineUks->status_text }}</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-black tracking-tight text-white flex items-center gap-2.5">
                    <i class="fa-solid fa-kit-medical text-red-500"></i>
                    <span>{{ $hotlineUks->service_name }}</span>
                </h3>
                <p class="text-xs text-stone-300 mt-1">Layanan respon cepat tanggap darurat medis & P3K siswa PMR Wira SMAN 1 Ciawi</p>
            </div>

            <!-- Modal Body -->
            <div class="p-6 sm:p-7 space-y-4">
                <!-- Contact Highlight Card -->
                <div class="bg-stone-50 border border-stone-200/90 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-stone-400">Nomor Telepon / WhatsApp Piket</div>
                        <div class="text-xl font-black text-stone-900 tracking-wide font-mono mt-0.5">{{ $hotlineUks->formatted_phone }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $hotlineUks->whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                            <i class="fa-brands fa-whatsapp text-sm"></i> Chat WA
                        </a>
                        <a href="{{ $hotlineUks->tel_url }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-stone-800 hover:bg-stone-900 text-white font-bold text-xs shadow-sm transition">
                            <i class="fa-solid fa-phone text-xs"></i> Panggil
                        </a>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/70">
                        <div class="text-stone-400 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-user-shield text-red-600"></i> Petugas Siaga
                        </div>
                        <div class="font-extrabold text-stone-800">{{ $hotlineUks->officer_name }}</div>
                    </div>
                    <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/70">
                        <div class="text-stone-400 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-clock text-amber-600"></i> Jam Operasional
                        </div>
                        <div class="font-extrabold text-stone-800">{{ $hotlineUks->operational_hours }}</div>
                    </div>
                    <div class="sm:col-span-2 p-3.5 rounded-xl bg-stone-50 border border-stone-200/70">
                        <div class="text-stone-400 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-location-dot text-emerald-600"></i> Lokasi Pos Medis
                        </div>
                        <div class="font-extrabold text-stone-800">{{ $hotlineUks->location }}</div>
                    </div>
                </div>

                <!-- Description / Purpose Info -->
                <div class="bg-red-50/70 border border-red-200/80 rounded-2xl p-4 text-xs text-red-950 space-y-1.5">
                    <div class="font-black flex items-center gap-1.5 text-red-900">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Kapan Harus Menghubungi Hotline UKS?</span>
                    </div>
                    <p class="text-stone-600 leading-relaxed">
                        {{ $hotlineUks->description ?: 'Hotline ini difungsikan untuk pelaporan cepat siswa/guru yang mendadak sakit di kelas, pingsan saat upacara bendera, cedera saat olahraga/praktik, atau membutuhkan evakuasi tandu darurat ke Ruang UKS SMAN 1 Ciawi.' }}
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-stone-50 px-6 py-4 border-t border-stone-200 flex items-center justify-between">
                <span class="text-[11px] text-stone-500 font-medium">PMR Wira SMAN 1 Ciawi &bull; Sigap, Terlatih, Kemanusiaan</span>
                <button type="button" onclick="closeUksHotlineModal()" class="px-4 py-2 rounded-xl bg-stone-200 hover:bg-stone-300 text-stone-700 font-bold text-xs transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openUksHotlineModal() {
            const modal = document.getElementById('uks-hotline-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeUksHotlineModal() {
            const modal = document.getElementById('uks-hotline-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        // Close on backdrop click & Escape key
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('uks-hotline-modal');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeUksHotlineModal();
                    }
                });
            }
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeUksHotlineModal();
                }
            });
        });
    </script>

    <!-- Floating Widgets: Wira Agent AI + WhatsApp Berkedip -->
    @include('partials.floating-widgets')

    @stack('scripts')
</body>
</html>
