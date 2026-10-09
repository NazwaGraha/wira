<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    </script>
    @stack('styles')
</head>
<body x-data="{ mobileSidebarOpen: false }" class="bg-slate-100 text-slate-800 font-sans antialiased flex h-screen overflow-hidden relative">

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
        <div class="flex-grow overflow-y-auto px-4 py-6 space-y-1">
            <div class="text-[11px] font-bold uppercase text-slate-500 tracking-wider px-3 mb-2">Menu Utama</div>
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.articles.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-newspaper w-5 text-center"></i>
                <span>Kelola Artikel</span>
                <span class="ml-auto bg-slate-800 text-slate-300 text-[10px] font-bold px-2 py-0.5 rounded-full">Active</span>
            </a>

            <a href="{{ route('admin.hero-slides.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.hero-slides.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-sliders w-5 text-center"></i>
                <span>Slider Hero Banner</span>
            </a>

            <a href="{{ route('admin.activities.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.activities.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-calendar-check w-5 text-center"></i>
                <span>Kelola Kegiatan</span>
            </a>

            <a href="{{ route('admin.gallery.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.gallery.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-images w-5 text-center"></i>
                <span>Kelola Galeri</span>
            </a>

            <a href="{{ route('admin.organization.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.organization.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-sitemap w-5 text-center"></i>
                <span>Bagan Kepengurusan</span>
            </a>

            <!-- Submenu Data Anggota -->
            <div class="pt-4 pb-1 text-[11px] font-bold uppercase text-slate-500 tracking-wider px-3">Data Anggota</div>
            <a href="{{ route('admin.members.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.members.create') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-user-plus w-5 text-center text-emerald-400"></i>
                <span>Input Data Anggota</span>
            </a>
            <a href="{{ route('admin.members.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.members.index') || request()->routeIs('admin.members.edit') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-users-rectangle w-5 text-center text-blue-400"></i>
                <span>Daftar Anggota</span>
            </a>

            <!-- Submenu Manajemen Lomba -->
            <div class="pt-4 pb-1 text-[11px] font-bold uppercase text-slate-500 tracking-wider px-3">SUA BHAKTI BERKARYA</div>
            <a href="{{ route('admin.competition-event.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.competition-event.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-sliders w-5 text-center text-rose-400"></i>
                <span>Pengaturan Event Lomba</span>
            </a>
            <a href="{{ route('admin.competition-registrations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.competition-registrations.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-clipboard-check w-5 text-center text-amber-400"></i>
                <span>Verifikasi Pendaftar</span>
            </a>
            <a href="{{ route('admin.competition-checkin.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.competition-checkin.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-qrcode w-5 text-center text-emerald-400"></i>
                <span>Daftar Ulang Peserta</span>
            </a>
            <a href="{{ route('admin.competition-participants.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.competition-participants.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-users-viewfinder w-5 text-center text-sky-400"></i>
                <span>Peserta Terverifikasi</span>
            </a>
            <a href="{{ route('admin.competition-scores.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.competition-scores.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-calculator w-5 text-center text-teal-400"></i>
                <span>Input Nilai Lomba</span>
            </a>
            <a href="{{ route('admin.competition-leaderboard.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.competition-leaderboard.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-trophy w-5 text-center text-yellow-400"></i>
                <span>Rekap Juara Umum</span>
            </a>
            <a href="{{ route('admin.competition-fees.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.competition-fees.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-coins w-5 text-center text-emerald-400"></i>
                <span>Setup Biaya Lomba</span>
            </a>
            <a href="{{ route('admin.competition-broadcast.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.competition-broadcast.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-paper-plane w-5 text-center text-indigo-400"></i>
                <span>Siaran Email / Informasi</span>
            </a>
            <a href="{{ route('admin.competition-info-menus.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.competition-info-menus.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}" title="Kelola Menu Informasi Lomba (Surat, Juklak, Grid, Denah, Kontak)">
                <i class="fa-solid fa-folder-open w-5 text-center text-pink-400"></i>
                <span class="truncate">Informasi Lomba</span>
            </a>

            <!-- Submenu Donor Darah -->
            <div class="pt-4 pb-1 text-[11px] font-bold uppercase text-slate-500 tracking-wider px-3">Kelola Donor Darah</div>
            <a href="{{ route('admin.blood-stocks.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.blood-stocks.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-droplet w-5 text-center text-red-400"></i>
                <span>Stok Darah</span>
            </a>
            <a href="{{ route('admin.blood-donation-events.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.blood-donation-events.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-calendar-plus w-5 text-center text-rose-400"></i>
                <span>Jadwal & Event</span>
            </a>
            <a href="{{ route('admin.blood-donor-registrations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.blood-donor-registrations.*') ? 'bg-pmr-primary text-white shadow-md shadow-red-950/40' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-users w-5 text-center text-orange-400"></i>
                <span>Pendaftar Donor</span>
            </a>

            <div class="pt-6 text-[11px] font-bold uppercase text-slate-500 tracking-wider px-3 mb-2">Layanan Kemanusiaan</div>

            <a href="{{ route('donor-darah') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition hover:bg-slate-800 text-slate-300">
                <i class="fa-solid fa-droplet w-5 text-center text-red-400"></i>
                <span>Donor Darah (Live)</span>
            </a>

            <a href="{{ route('kegiatan') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition hover:bg-slate-800 text-slate-300">
                <i class="fa-solid fa-calendar-check w-5 text-center text-amber-400"></i>
                <span>Agenda Kegiatan</span>
            </a>

            <a href="{{ route('galeri') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition hover:bg-slate-800 text-slate-300">
                <i class="fa-solid fa-images w-5 text-center text-blue-400"></i>
                <span>Galeri Foto</span>
            </a>

            <div class="pt-6 text-[11px] font-bold uppercase text-slate-500 tracking-wider px-3 mb-2">Akses Cepat</div>

            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition hover:bg-slate-800 text-emerald-400">
                <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i>
                <span>Lihat Web Publik</span>
            </a>

            <a href="/mockups/preview.html" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition hover:bg-slate-800 text-amber-300">
                <i class="fa-solid fa-palette w-5 text-center"></i>
                <span>Preview Mockup</span>
            </a>
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
                        <div class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@pmr.sch.id' }}</div>
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
                    <h1 class="text-sm sm:text-xl font-extrabold text-slate-900 truncate">@yield('page_title', 'Manajemen Artikel')</h1>
                    <p class="text-[10px] sm:text-xs text-slate-500 truncate hidden sm:block">PMR Wira SMAN 1 Ciawi &bull; Periode Ragana Dwi Pantara 2026/2027</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                <div class="hidden lg:flex items-center text-xs bg-slate-100 px-3.5 py-2 rounded-xl text-slate-600 gap-2 border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Database Status: <strong>Terkoneksi</strong></span>
                </div>

                @yield('top_actions')
            </div>
        </header>

        <!-- Body Scrollable Content -->
        <main class="flex-grow overflow-y-auto p-3.5 sm:p-6 md:p-8">
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
