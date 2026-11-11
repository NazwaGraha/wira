<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#980000">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Pemberitahuan') | PMR Wira SMAN 1 Ciawi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
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
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        html, body {
            min-height: 100%;
            min-height: 100dvh;
            -webkit-text-size-adjust: 100%;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            padding-top: env(safe-area-inset-top, 0px);
            padding-bottom: env(safe-area-inset-bottom, 0px);
            padding-left: env(safe-area-inset-left, 0px);
            padding-right: env(safe-area-inset-right, 0px);
        }
        .gradient-pmr {
            background: linear-gradient(135deg, #7f0000 0%, #980000 50%, #5a0000 100%);
        }
        button, a, input, select, textarea {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="h-full bg-slate-900 text-slate-100 flex flex-col justify-between selection:bg-rose-500 selection:text-white relative overflow-x-hidden">
    <!-- Ambient Background Glow -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-red-600/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-600/15 rounded-full blur-3xl"></div>
    </div>

    <!-- Header / Branding -->
    <header class="relative z-10 w-full border-b border-slate-800/80 bg-slate-950/60 backdrop-blur-md px-4 sm:px-8 py-3.5 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group transition">
            <img src="{{ asset('images/logo_pmi_sman1ciawi_official.png') }}" 
                 alt="Logo PMR Wira SMAN 1 Ciawi" 
                 class="h-9 w-auto object-contain bg-white/95 px-2 py-0.5 rounded-lg border border-slate-700/60 shadow-sm transition group-hover:scale-105"
                 onerror="this.src='{{ asset('images/logo-pmr.png') }}'">
            <div class="leading-tight">
                <span class="block text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase group-hover:text-red-400 transition">PMR WIRA SMAN 1 CIAWI</span>
                <span class="block text-[10px] text-slate-400 font-medium">Ragana Dwi Pantara • Masa Bakti 2026/2027</span>
            </div>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ url('/') }}" class="text-xs font-bold text-slate-300 hover:text-white bg-slate-800/80 hover:bg-slate-700 px-3 py-1.5 rounded-xl border border-slate-700/60 transition flex items-center gap-1.5 active:scale-95">
                <i class="fa-solid fa-house text-red-400"></i>
                <span class="hidden sm:inline">Beranda</span>
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="relative z-10 flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="max-w-xl w-full">
            <div class="bg-slate-950/80 border border-slate-800/80 rounded-3xl p-6 sm:p-10 shadow-2xl backdrop-blur-xl text-center relative overflow-hidden">
                <!-- Status Code Floating Pill -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-mono font-bold @yield('badge_class', 'bg-red-500/10 text-red-400 border border-red-500/20') mb-5">
                    <span class="w-2 h-2 rounded-full @yield('dot_class', 'bg-red-500 animate-pulse')"></span>
                    <span>KODE STATUS: @yield('code', 'INFO')</span>
                </div>

                <!-- Icon / Mascot Illustration -->
                <div class="relative w-28 h-28 mx-auto mb-6 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full @yield('glow_class', 'bg-red-500/20') blur-xl"></div>
                    <div class="relative w-24 h-24 rounded-2xl @yield('icon_bg', 'bg-gradient-to-tr from-slate-900 to-slate-800 border border-slate-700') flex items-center justify-center shadow-lg">
                        @hasSection('custom_icon')
                            @yield('custom_icon')
                        @else
                            <i class="@yield('icon', 'fa-solid fa-triangle-exclamation') text-4xl @yield('icon_color', 'text-red-400')"></i>
                        @endif
                    </div>
                </div>

                <!-- Title & Message -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-3 tracking-tight">
                    @yield('title')
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-md mx-auto mb-8">
                    @yield('message')
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-gradient-to-r from-red-700 to-red-600 hover:from-red-600 hover:to-red-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-red-950/50 transition flex items-center justify-center gap-2 active:scale-95">
                        <i class="fa-solid fa-house"></i>
                        <span>Kembali ke Beranda</span>
                    </a>

                    <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs sm:text-sm border border-slate-700 transition flex items-center justify-center gap-2 active:scale-95">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Halaman Sebelumnya</span>
                    </button>

                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs sm:text-sm border border-amber-500/30 transition flex items-center justify-center gap-2 active:scale-95">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Dashboard</span>
                        </a>
                    @endauth
                </div>

                <!-- Additional Helper Box / Alternative Links -->
                @yield('helper_content')
            </div>

            <!-- Hotline & Support Prompt -->
            <div class="mt-4 text-center text-xs text-slate-400 flex flex-wrap items-center justify-center gap-3">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-question text-slate-400"></i> Butuh bantuan cepat?
                </span>
                <a href="https://wa.me/6281283921820?text=Halo%20Admin%20PMR%20Wira%20SMAN%201%20Ciawi,%20saya%20mengalami%20kendala%20di%20website" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:text-emerald-300 font-bold underline flex items-center gap-1">
                    <i class="fa-brands fa-whatsapp"></i> Hotline UKS/Admin
                </a>
                <span>•</span>
                <a href="{{ url('/tentang-kami') }}" class="hover:text-slate-200 transition">Tentang Kami</a>
                <span>•</span>
                <a href="{{ url('/lomba') }}" class="hover:text-slate-200 transition">Info Lomba</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full border-t border-slate-800/80 bg-slate-950/60 backdrop-blur-md px-4 py-3 text-center text-[11px] text-slate-400">
        &copy; {{ date('Y') }} PMR Wira SMAN 1 Ciawi • Dikelola oleh Sie Teknologi & Publikasi Ragana Dwi Pantara
    </footer>
</body>
</html>
