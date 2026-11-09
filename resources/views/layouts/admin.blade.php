@php
    $currentActiveSection = null;
    if (request()->routeIs('admin.dashboard') || request()->routeIs('admin.articles.*') || request()->routeIs('admin.categories.*')) {
        $currentActiveSection = 'menu_utama';
    } elseif (request()->routeIs('admin.advisors.*')) {
        $currentActiveSection = 'data_pembina';
    } elseif (request()->routeIs('admin.member-registrations.*') || request()->routeIs('admin.members.*')) {
        $currentActiveSection = 'data_anggota';
    } elseif (request()->routeIs('admin.user-permissions.*')) {
        $currentActiveSection = 'user_permissions';
    } elseif (request()->routeIs('admin.content-setup.*')) {
        $currentActiveSection = 'setup_konten';
    } elseif (
        request()->routeIs('admin.media-manager.*') ||
        request()->routeIs('admin.hero-slides.*') ||
        request()->routeIs('admin.popup-banner.*') ||
        request()->routeIs('admin.background-music.*') ||
        request()->routeIs('admin.uks-hotline.*') ||
        request()->routeIs('admin.activities.*') ||
        request()->routeIs('admin.gallery.*') ||
        request()->routeIs('admin.gallery-categories.*') ||
        request()->routeIs('admin.organization.*') ||
        request()->routeIs('admin.prestasi.*') ||
        request()->routeIs('admin.organization-guidelines.*')
    ) {
        $currentActiveSection = 'media_konten';
    } elseif (
        request()->routeIs('admin.competition-*') ||
        request()->routeIs('admin.parking.*')
    ) {
        $currentActiveSection = 'manajemen_lomba';
    } elseif (request()->routeIs('admin.accounting.*')) {
        $currentActiveSection = 'akuntansi_kas';
    } elseif (request()->routeIs('admin.blood-*')) {
        $currentActiveSection = 'donor_darah';
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | Backoffice PMR Wira SMAN 1 Ciawi</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js & FontAwesome & Tailwind -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pmr: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            primary: '#980000',
                            dark: '#6e0000',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }

        function adminSidebar() {
            return {
                mobileSidebarOpen: false,
                sections: {
                    menu_utama: true,
                    data_pembina: true,
                    data_anggota: true,
                    setup_konten: false,
                    media_konten: false,
                    manajemen_lomba: false,
                    akuntansi_kas: false,
                    donor_darah: false,
                    layanan_kemanusiaan: false,
                    akses_cepat: false,
                },
                init() {
                    try {
                        const saved = localStorage.getItem('pmr_admin_sidebar_sections_v2');
                        if (saved) {
                            const parsed = JSON.parse(saved);
                            if (parsed && typeof parsed === 'object') {
                                this.sections = Object.assign({}, this.sections, parsed);
                            }
                        }
                    } catch (e) {
                        console.warn('Could not read sidebar state from localStorage', e);
                    }

                    // Auto-expand section containing current active page
                    const activeSection = '{{ $currentActiveSection ?? '' }}';
                    if (activeSection && this.sections.hasOwnProperty(activeSection)) {
                        this.sections[activeSection] = true;
                    }
                },
                isOpen(key) {
                    return !!this.sections[key];
                },
                toggleSection(key) {
                    this.sections[key] = !this.sections[key];
                    try {
                        localStorage.setItem('pmr_admin_sidebar_sections_v2', JSON.stringify(this.sections));
                    } catch (e) {
                        console.warn('Could not save sidebar state to localStorage', e);
                    }
                }
            };
        }
    </script>
    @stack('styles')
</head>
<body x-data="adminSidebar()" class="bg-slate-100 text-slate-800 font-sans antialiased flex h-screen overflow-hidden relative">

    <!-- Mobile Backdrop Overlay -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false" 
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 md:hidden"
         style="display: none;"></div>

    <!-- Sidebar -->
    <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="fixed md:static inset-y-0 left-0 z-50 w-72 md:w-64 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 border-r border-slate-800 transition-transform duration-300 ease-in-out shadow-2xl md:shadow-none">
        <!-- Brand Header -->
        <div class="h-20 md:h-24 bg-slate-950 px-4 flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="bg-white rounded-xl p-2 flex items-center justify-center shadow-md shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo PMR Wira SMAN 1 Ciawi" class="h-9 md:h-11 w-auto object-contain">
                </div>
                <div class="overflow-hidden">
                    <div class="font-extrabold text-white text-xs tracking-wide truncate">PMR WIRA CIAWI</div>
                    <div class="text-[10px] text-red-400 font-semibold tracking-wider uppercase">Backoffice CMS</div>
                </div>
            </div>
            <!-- Tombol Tutup Sidebar Khusus Mobile -->
            <button type="button" @click="mobileSidebarOpen = false" class="md:hidden p-2 text-slate-400 hover:text-white rounded-xl hover:bg-slate-800 transition" title="Tutup Menu">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-grow overflow-y-auto px-3.5 py-4 space-y-1.5 text-xs">
            <!-- ========================================== -->
            <!-- 1. MENU UTAMA -->
            <!-- ========================================== -->
            @php $isMenuUtamaActive = ($currentActiveSection === 'menu_utama'); @endphp
            <div class="space-y-1">
                <button type="button" 
                        @click="toggleSection('menu_utama')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none {{ $isMenuUtamaActive ? 'text-white bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-gauge-high text-xs {{ $isMenuUtamaActive ? 'text-rose-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                        <span class="truncate">Menu Utama</span>
                        @if($isMenuUtamaActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                       :class="isOpen('menu_utama') ? 'rotate-180 text-white' : ''"></i>
                </button>

                <div x-show="isOpen('menu_utama')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-chart-pie w-4 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    @if(auth()->user()?->hasPermission('articles.view'))
                    <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.articles.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-newspaper w-4 text-center"></i>
                        <span class="truncate">Kelola Artikel</span>
                        <span class="ml-auto bg-slate-800 text-slate-300 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Active</span>
                    </a>
                    @endif

                    @if(auth()->user()?->hasPermission('categories.view'))
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.categories.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-tags w-4 text-center"></i>
                        <span class="truncate">Kategori Artikel</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 2. DATA PEMBINA -->
            <!-- ========================================== -->
            @if(auth()->user()?->hasPermission('advisors.view'))
            @php $isDataPembinaActive = ($currentActiveSection === 'data_pembina'); @endphp
            <div class="pt-1 space-y-1">
                <button type="button" 
                        @click="toggleSection('data_pembina')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none {{ $isDataPembinaActive ? 'text-white bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-user-shield text-xs {{ $isDataPembinaActive ? 'text-amber-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                        <span class="truncate">Data Pembina</span>
                        @if($isDataPembinaActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                       :class="isOpen('data_pembina') ? 'rotate-180 text-white' : ''"></i>
                </button>

                <div x-show="isOpen('data_pembina')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    @if(auth()->user()?->hasPermission('advisors.create'))
                    <a href="{{ route('admin.advisors.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.advisors.create') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-user-tie w-4 text-center text-amber-400"></i>
                        <span class="truncate">Input Data Pembina</span>
                    </a>
                    @endif
                    <a href="{{ route('admin.advisors.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.advisors.index') || request()->routeIs('admin.advisors.edit') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-shield-halved w-4 text-center text-rose-400"></i>
                        <span class="truncate">Daftar Pembina</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 3. DATA ANGGOTA -->
            <!-- ========================================== -->
            @if(auth()->user()?->hasPermission('members.view') || auth()->user()?->hasPermission('member_registrations.view'))
            @php 
                $isDataAnggotaActive = ($currentActiveSection === 'data_anggota');
                $pendingRegistrationsCount = \App\Models\MemberRegistration::where('status', 'pending')->count();
            @endphp
            <div class="pt-1 space-y-1">
                <button type="button" 
                        @click="toggleSection('data_anggota')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none {{ $isDataAnggotaActive ? 'text-white bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-users text-xs {{ $isDataAnggotaActive ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                        <span class="truncate">Data Anggota</span>
                        @if($isDataAnggotaActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($pendingRegistrationsCount > 0 && auth()->user()?->hasPermission('member_registrations.view'))
                            <span class="bg-amber-500/20 text-amber-300 text-[10px] font-black px-1.5 py-0.2 rounded-full border border-amber-500/30">
                                {{ $pendingRegistrationsCount }}
                            </span>
                        @endif
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                           :class="isOpen('data_anggota') ? 'rotate-180 text-white' : ''"></i>
                    </div>
                </button>

                <div x-show="isOpen('data_anggota')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    @if(auth()->user()?->hasPermission('member_registrations.view'))
                    <a href="{{ route('admin.member-registrations.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.member-registrations.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-id-card-clip w-4 text-center text-amber-400"></i>
                        <div class="flex items-center justify-between flex-1 min-w-0">
                            <span class="truncate">Calon Anggota Baru</span>
                            @if($pendingRegistrationsCount > 0)
                                <span class="bg-amber-500/20 text-amber-300 text-[10px] font-black px-2 py-0.5 rounded-full border border-amber-500/30 shrink-0 ml-1">{{ $pendingRegistrationsCount }}</span>
                            @endif
                        </div>
                    </a>
                    @endif

                    @if(auth()->user()?->hasPermission('members.create'))
                    <a href="{{ route('admin.members.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.members.create') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-user-plus w-4 text-center text-emerald-400"></i>
                        <span class="truncate">Input Data Anggota</span>
                    </a>
                    @endif

                    @if(auth()->user()?->hasPermission('members.view'))
                    <a href="{{ route('admin.members.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.members.index') || request()->routeIs('admin.members.edit') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-users-rectangle w-4 text-center text-blue-400"></i>
                        <span class="truncate">Daftar Anggota</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 4. SETUP KONTEN -->
            <!-- ========================================== -->
            @if(auth()->user()?->hasPermission('content_setup.home') || auth()->user()?->hasPermission('content_setup.about'))
            @php $isSetupKontenActive = ($currentActiveSection === 'setup_konten'); @endphp
            <div class="pt-1 space-y-1">
                <button type="button" 
                        @click="toggleSection('setup_konten')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none {{ $isSetupKontenActive ? 'text-white bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-desktop text-xs {{ $isSetupKontenActive ? 'text-rose-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                        <span class="truncate">Setup Konten</span>
                        @if($isSetupKontenActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                       :class="isOpen('setup_konten') ? 'rotate-180 text-white' : ''"></i>
                </button>

                <div x-show="isOpen('setup_konten')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    @if(auth()->user()?->hasPermission('content_setup.home'))
                    <a href="{{ route('admin.content-setup.home') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.content-setup.home*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-house-laptop w-4 text-center text-rose-400"></i>
                        <div class="flex items-center justify-between flex-grow min-w-0">
                            <span class="truncate">Beranda</span>
                            <span class="text-[9px] bg-rose-500/20 text-rose-300 border border-rose-500/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0 ml-1">Home</span>
                        </div>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('content_setup.about'))
                    <a href="{{ route('admin.content-setup.about') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.content-setup.about*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-circle-info w-4 text-center text-amber-400"></i>
                        <div class="flex items-center justify-between flex-grow min-w-0">
                            <span class="truncate">Tentang Kami</span>
                            <span class="text-[9px] bg-amber-500/20 text-amber-300 border border-amber-500/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0 ml-1">Profil</span>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 5. MEDIA & KONTEN WEB -->
            <!-- ========================================== -->
            @if(
                auth()->user()?->hasPermission('media_manager.view') ||
                auth()->user()?->hasPermission('popup_banner.manage') ||
                auth()->user()?->hasPermission('background_music.manage') ||
                auth()->user()?->hasPermission('uks_hotline.manage') ||
                auth()->user()?->hasPermission('activities.view') ||
                auth()->user()?->hasPermission('gallery.view') ||
                auth()->user()?->hasPermission('organization.view') ||
                auth()->user()?->hasPermission('prestasi.view') ||
                auth()->user()?->hasPermission('organization_guidelines.view')
            )
            @php $isMediaKontenActive = ($currentActiveSection === 'media_konten'); @endphp
            <div class="pt-1 space-y-1">
                <button type="button" 
                        @click="toggleSection('media_konten')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none {{ $isMediaKontenActive ? 'text-white bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-photo-film text-xs {{ $isMediaKontenActive ? 'text-rose-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                        <span class="truncate">Media & Konten Web</span>
                        @if($isMediaKontenActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                       :class="isOpen('media_konten') ? 'rotate-180 text-white' : ''"></i>
                </button>

                <div x-show="isOpen('media_konten')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    @if(auth()->user()?->hasPermission('media_manager.view'))
                    <a href="{{ route('admin.media-manager.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.media-manager.*') || request()->routeIs('admin.hero-slides.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-photo-film w-4 text-center text-rose-400"></i>
                        <span class="truncate">Kelola Gambar & Media</span>
                        <span class="ml-auto bg-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Web</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('popup_banner.manage'))
                    <a href="{{ route('admin.popup-banner.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.popup-banner.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-bullhorn w-4 text-center text-amber-400"></i>
                        <span class="truncate">Popup Banner Iklan</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('background_music.manage'))
                    <a href="{{ route('admin.background-music.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.background-music.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-music w-4 text-center text-rose-400"></i>
                        <span class="truncate">Sound Mars PMI</span>
                        <span class="ml-auto bg-rose-500/20 text-rose-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-rose-500/30 shrink-0">Audio</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('uks_hotline.manage'))
                    <a href="{{ route('admin.uks-hotline.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.uks-hotline.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-truck-medical w-4 text-center text-rose-400"></i>
                        <span class="truncate">Hotline & Siaga UKS</span>
                        <span class="ml-auto bg-emerald-500/20 text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-500/30 shrink-0">Siaga</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('activities.view'))
                    <a href="{{ route('admin.activities.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.activities.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-calendar-check w-4 text-center"></i>
                        <span class="truncate">Kelola Kegiatan</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('gallery.view'))
                    <a href="{{ route('admin.gallery.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.gallery.*') && !request()->routeIs('admin.gallery-categories.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-images w-4 text-center"></i>
                        <span class="truncate">Kelola Galeri</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('gallery.manage'))
                    <a href="{{ route('admin.gallery-categories.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.gallery-categories.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-folder-tree w-4 text-center"></i>
                        <span class="truncate">Kategori Galeri</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('organization.view'))
                    <a href="{{ route('admin.organization.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.organization.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-sitemap w-4 text-center"></i>
                        <span class="truncate">Bagan Kepengurusan</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('prestasi.view'))
                    <a href="{{ route('admin.prestasi.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.prestasi.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-trophy w-4 text-center text-amber-400"></i>
                        <span class="truncate">Prestasi & Penghargaan</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('organization_guidelines.view'))
                    <a href="{{ route('admin.organization-guidelines.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.organization-guidelines.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-book-bookmark w-4 text-center text-amber-400"></i>
                        <span class="truncate">Pedoman Organisasi</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 6. SUA BHAKTI BERKARYA (MANAJEMEN LOMBA) -->
            <!-- ========================================== -->
            @if(
                auth()->user()?->hasPermission('competition_event.view') ||
                auth()->user()?->hasPermission('competition_registrations.view') ||
                auth()->user()?->hasPermission('competition_checkin.view') ||
                auth()->user()?->hasPermission('competition_scores.view') ||
                auth()->user()?->hasPermission('competition_leaderboard.view') ||
                auth()->user()?->hasPermission('competition_participants.view') ||
                auth()->user()?->hasPermission('parking_locations.view') ||
                auth()->user()?->hasPermission('parking_tickets.view')
            )
            @php $isManajemenLombaActive = ($currentActiveSection === 'manajemen_lomba'); @endphp
            <div class="pt-1 space-y-1">
                <button type="button" 
                        @click="toggleSection('manajemen_lomba')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none {{ $isManajemenLombaActive ? 'text-white bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-trophy text-xs {{ $isManajemenLombaActive ? 'text-amber-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                        <span class="truncate">Sua Bhakti Berkarya</span>
                        @if($isManajemenLombaActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                       :class="isOpen('manajemen_lomba') ? 'rotate-180 text-white' : ''"></i>
                </button>

                <div x-show="isOpen('manajemen_lomba')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    @if(auth()->user()?->hasPermission('competition_event.view'))
                    <a href="{{ route('admin.competition-event.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-event.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-sliders w-4 text-center text-rose-400"></i>
                        <span class="truncate">Pengaturan Event Lomba</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_bank_accounts.manage'))
                    <a href="{{ route('admin.competition-bank-accounts.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-bank-accounts.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-credit-card w-4 text-center text-emerald-400"></i>
                        <div class="flex items-center justify-between flex-grow min-w-0">
                            <span class="truncate">Rekening Transfer Lomba</span>
                            <span class="text-[9px] bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0 ml-1">3 Tingkat</span>
                        </div>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_fees.manage'))
                    <a href="{{ route('admin.competition-fees.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-fees.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-calendar-check w-4 text-center text-amber-400"></i>
                        <div class="flex items-center justify-between flex-grow min-w-0">
                            <span class="truncate">Jadwal & Biaya Gelombang</span>
                            <span class="text-[9px] bg-amber-400/20 text-amber-300 border border-amber-400/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0 ml-1">2 Gel</span>
                        </div>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_quotas.manage'))
                    <a href="{{ route('admin.competition-quotas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-quotas.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-sliders w-4 text-center text-rose-400"></i>
                        <div class="flex items-center justify-between flex-grow min-w-0">
                            <span class="truncate">Pengaturan Kuota Regu</span>
                            <span class="text-[9px] bg-rose-400/20 text-rose-300 border border-rose-400/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0 ml-1">Kuota</span>
                        </div>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_registrations.view'))
                    <a href="{{ route('admin.competition-registrations.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-registrations.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-clipboard-check w-4 text-center text-amber-400"></i>
                        <span class="truncate">Verifikasi Pendaftar</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_checkin.view'))
                    <a href="{{ route('admin.competition-checkin.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-checkin.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-qrcode w-4 text-center text-emerald-400"></i>
                        <span class="truncate">Daftar Ulang Peserta</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_juries.view'))
                    <a href="{{ route('admin.competition-juries.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-juries.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-user-gear w-4 text-center text-amber-400"></i>
                        <div class="flex items-center justify-between flex-grow min-w-0">
                            <span class="truncate">Input & Registrasi Juri</span>
                            <span class="text-[9px] bg-amber-400/20 text-amber-300 border border-amber-400/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0 ml-1">Juri</span>
                        </div>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_scores.view'))
                    <a href="{{ route('admin.competition-scores.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ (request()->routeIs('admin.competition-scores.*') && !request()->routeIs('admin.competition-scores.master-sheet*')) ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-calculator w-4 text-center text-teal-400"></i>
                        <span class="truncate">Input Nilai Lomba</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_scores.master_sheet'))
                    <a href="{{ route('admin.competition-scores.master-sheet') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-scores.master-sheet*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-table-list w-4 text-center text-blue-400"></i>
                        <div class="flex items-center justify-between flex-grow min-w-0">
                            <span class="truncate">Lembar Skor Utama</span>
                            <span class="text-[9px] bg-blue-500/20 text-blue-300 border border-blue-400/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0 ml-1">Rekap</span>
                        </div>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_mascots.manage'))
                    <a href="{{ route('admin.competition-mascots.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-mascots.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-cat w-4 text-center text-amber-300"></i>
                        <div class="flex items-center justify-between flex-grow min-w-0">
                            <span class="truncate">Maskot Lomba</span>
                            <span class="text-[9px] bg-amber-400/20 text-amber-300 border border-amber-400/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0 ml-1">Foto</span>
                        </div>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_leaderboard.view'))
                    <a href="{{ route('admin.competition-leaderboard.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-leaderboard.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-trophy w-4 text-center text-yellow-400"></i>
                        <span class="truncate">Rekap Juara Umum</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_participants.view'))
                    <a href="{{ route('admin.competition-participants.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-participants.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-medal w-4 text-center text-amber-400"></i>
                        <div class="flex items-center justify-between flex-grow min-w-0">
                            <span class="truncate">Daftar Peserta & Juara</span>
                            <span class="text-[9px] bg-amber-400/20 text-amber-300 border border-amber-400/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0 ml-1">Event</span>
                        </div>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_broadcast.send'))
                    <a href="{{ route('admin.competition-broadcast.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-broadcast.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-paper-plane w-4 text-center text-indigo-400"></i>
                        <span class="truncate">Siaran Email / Informasi</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('competition_info_menus.manage'))
                    <a href="{{ route('admin.competition-info-menus.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.competition-info-menus.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}" title="Kelola Menu Informasi Lomba (Surat, Juklak, Grid, Denah, Kontak)">
                        <i class="fa-solid fa-folder-open w-4 text-center text-pink-400"></i>
                        <span class="truncate">Informasi Lomba</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('parking_locations.view') || auth()->user()?->hasPermission('parking_tickets.view'))
                    <a href="{{ route('admin.parking.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.parking.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}" title="Kelola Kantong Parkir & Tiket Kendaraan Kontingen">
                        <i class="fa-solid fa-square-parking w-4 text-center text-amber-400"></i>
                        <span class="truncate">Tiket & Kantong Parkir</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 6b. AKUNTANSI & KAS (ACCOUNTING MULTI-PROYEK) -->
            <!-- ========================================== -->
            @php $isAccountingActive = ($currentActiveSection === 'akuntansi_kas'); @endphp
            <div class="pt-1 space-y-1">
                <button type="button" 
                        @click="toggleSection('akuntansi_kas')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none {{ $isAccountingActive ? 'text-white bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-calculator text-xs {{ $isAccountingActive ? 'text-emerald-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                        <span class="truncate">Akuntansi & Kas</span>
                        @if($isAccountingActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-[9px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-1.5 py-0.2 rounded font-black uppercase">Accurate</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                           :class="isOpen('akuntansi_kas') ? 'rotate-180 text-white' : ''"></i>
                    </div>
                </button>

                <div x-show="isOpen('akuntansi_kas')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    
                    <a href="{{ route('admin.accounting.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.accounting.index') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-chart-pie w-4 text-center text-emerald-400"></i>
                        <span class="truncate">Dashboard & Proyek</span>
                    </a>

                    <a href="{{ route('admin.accounting.projects') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.accounting.projects*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-diagram-project w-4 text-center text-purple-400"></i>
                        <span class="truncate">Master Proyek Kegiatan</span>
                    </a>

                    <a href="{{ route('admin.accounting.accounts') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.accounting.accounts*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-list-ol w-4 text-center text-blue-400"></i>
                        <span class="truncate">Bagan Akun (CoA)</span>
                    </a>

                    <a href="{{ route('admin.accounting.transactions') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.accounting.transactions*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-receipt w-4 text-center text-amber-400"></i>
                        <span class="truncate">Kas Masuk & Keluar</span>
                    </a>

                    <a href="{{ route('admin.accounting.reports') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.accounting.reports*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-file-invoice-dollar w-4 text-center text-rose-400"></i>
                        <span class="truncate">Laporan Keuangan</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 7. KELOLA DONOR DARAH -->
            <!-- ========================================== -->
            @if(
                auth()->user()?->hasPermission('blood_stocks.view') ||
                auth()->user()?->hasPermission('blood_donation_events.view') ||
                auth()->user()?->hasPermission('blood_donor_registrations.view')
            )
            @php $isDonorDarahActive = ($currentActiveSection === 'donor_darah'); @endphp
            <div class="pt-1 space-y-1">
                <button type="button" 
                        @click="toggleSection('donor_darah')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none {{ $isDonorDarahActive ? 'text-white bg-slate-800/90' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-hand-holding-droplet text-xs {{ $isDonorDarahActive ? 'text-rose-400' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                        <span class="truncate">Kelola Donor Darah</span>
                        @if($isDonorDarahActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                       :class="isOpen('donor_darah') ? 'rotate-180 text-white' : ''"></i>
                </button>

                <div x-show="isOpen('donor_darah')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    @if(auth()->user()?->hasPermission('blood_stocks.view'))
                    <a href="{{ route('admin.blood-stocks.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.blood-stocks.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-droplet w-4 text-center text-red-400"></i>
                        <span class="truncate">Stok Darah</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('blood_donation_events.view'))
                    <a href="{{ route('admin.blood-donation-events.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.blood-donation-events.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-calendar-plus w-4 text-center text-rose-400"></i>
                        <span class="truncate">Jadwal & Event</span>
                    </a>
                    @endif
                    @if(auth()->user()?->hasPermission('blood_donor_registrations.view'))
                    <a href="{{ route('admin.blood-donor-registrations.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition {{ request()->routeIs('admin.blood-donor-registrations.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                        <i class="fa-solid fa-users w-4 text-center text-orange-400"></i>
                        <span class="truncate">Pendaftar Donor</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 8. LAYANAN KEMANUSIAAN -->
            <!-- ========================================== -->
            <div class="pt-2 space-y-1 border-t border-slate-800/80 mt-2">
                <button type="button" 
                        @click="toggleSection('layanan_kemanusiaan')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none text-slate-400 hover:text-slate-200 hover:bg-slate-800/50">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-heart-pulse text-xs text-rose-400 group-hover:text-rose-300"></i>
                        <span class="truncate">Layanan Kemanusiaan</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                       :class="isOpen('layanan_kemanusiaan') ? 'rotate-180 text-white' : ''"></i>
                </button>

                <div x-show="isOpen('layanan_kemanusiaan')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    <a href="{{ route('donor-darah') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition hover:bg-slate-800 text-slate-300">
                        <i class="fa-solid fa-droplet w-4 text-center text-red-400"></i>
                        <span class="truncate">Donor Darah (Live)</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500 ml-auto shrink-0"></i>
                    </a>
                    <a href="{{ route('kegiatan') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition hover:bg-slate-800 text-slate-300">
                        <i class="fa-solid fa-calendar-check w-4 text-center text-amber-400"></i>
                        <span class="truncate">Agenda Kegiatan</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500 ml-auto shrink-0"></i>
                    </a>
                    <a href="{{ route('galeri') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition hover:bg-slate-800 text-slate-300">
                        <i class="fa-solid fa-images w-4 text-center text-blue-400"></i>
                        <span class="truncate">Galeri Foto</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500 ml-auto shrink-0"></i>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 9. AKSES CEPAT -->
            <!-- ========================================== -->
            <div class="pt-2 space-y-1 border-t border-slate-800/80 mt-2">
                <button type="button" 
                        @click="toggleSection('akses_cepat')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-left text-[11px] font-bold uppercase tracking-wider rounded-xl transition group select-none text-slate-400 hover:text-slate-200 hover:bg-slate-800/50">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-rocket text-xs text-emerald-400 group-hover:text-emerald-300"></i>
                        <span class="truncate">Akses Cepat</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0"
                       :class="isOpen('akses_cepat') ? 'rotate-180 text-white' : ''"></i>
                </button>

                <div x-show="isOpen('akses_cepat')" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-2 space-y-1 border-l-2 border-slate-800 ml-3.5 my-1">
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium text-xs md:text-sm transition hover:bg-slate-800 text-emerald-400">
                        <i class="fa-solid fa-arrow-up-right-from-square w-4 text-center"></i>
                        <span class="truncate">Lihat Web Publik</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 10. HAK AKSES PENGGUNA (POSISI PALING BAWAH) -->
            <!-- ========================================== -->
            @if(auth()->user()?->hasPermission('user_permissions.view') || auth()->user()?->canManagePermissions())
            @php $isUserPermissionsActive = ($currentActiveSection === 'user_permissions'); @endphp
            <div class="pt-2 border-t border-slate-800/80 mt-2">
                <a href="{{ route('admin.user-permissions.index') }}" 
                   class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition group {{ $isUserPermissionsActive ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300 bg-slate-800/40 border border-indigo-500/20' }}"
                   title="Manajemen Hak Akses Pengguna (Granular Permissions)">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-shield-halved text-xs text-indigo-400"></i>
                        <span class="truncate">Hak Akses Pengguna</span>
                        @if($isUserPermissionsActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </div>
                    <span class="text-[9px] bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 px-1.5 py-0.2 rounded font-black uppercase shrink-0">RBAC</span>
                </a>
            </div>
            @endif
        </div>

        <!-- User Info & Logout -->
        <div class="p-4 border-t border-slate-800 bg-slate-950">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 font-bold text-xs flex-shrink-0">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Admin PMR' }}</div>
                        <div class="text-[10px] text-red-400 font-semibold truncate">{{ auth()->user()->role_label ?? 'Administrator' }}</div>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded-lg transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col h-full overflow-hidden w-full min-w-0">
        <!-- Top Navbar -->
        <header class="h-16 sm:h-20 bg-white border-b border-slate-200 px-3.5 sm:px-8 flex items-center justify-between flex-shrink-0 gap-2 sm:gap-4">
            <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
                <!-- Hamburger Button (Mobile Only) -->
                <button type="button" 
                        @click="mobileSidebarOpen = true" 
                        class="md:hidden p-2 rounded-xl text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition flex items-center justify-center shrink-0 border border-slate-200 shadow-xs"
                        aria-label="Buka Menu Sidebar">
                    <i class="fa-solid fa-bars text-base"></i>
                </button>

                <div class="min-w-0">
                    <h1 class="text-sm sm:text-xl font-extrabold text-slate-900 truncate">@yield('page_title', 'Admin Panel')</h1>
                    <p class="text-[10px] sm:text-xs text-slate-500 truncate hidden sm:block">PMR Wira SMAN 1 Ciawi &bull; Periode Ragana Dwi Pantara 2026/2027</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                @auth
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-xl border border-slate-200 text-xs">
                        <i class="fa-solid fa-user-shield text-pmr-primary"></i>
                        <span class="font-bold text-slate-800 hidden sm:inline">{{ auth()->user()->name }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-800">
                            {{ auth()->user()->role_label }}
                        </span>
                    </div>
                @endauth

                @yield('top_actions')
            </div>
        </header>

        <!-- Body Scrollable Content -->
        <main class="flex-grow overflow-y-auto p-3.5 sm:p-6 md:p-8">
            @if (session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-xl"></i>
                    <div class="text-sm font-semibold">{{ session('error') }}</div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                    <div class="text-sm font-semibold">{{ session('success') }}</div>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl shadow-sm">
                    <div class="font-bold text-sm mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500"></i> Perhatian:
                    </div>
                    <ul class="list-disc pl-5 text-xs space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
