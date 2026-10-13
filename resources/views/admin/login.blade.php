<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Akses Backoffice & Area Anggota | PMR Wira SMAN 1 Ciawi</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased min-h-screen flex flex-col items-center justify-center p-4 selection:bg-red-500 selection:text-white">

    <div class="max-w-lg w-full bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden relative">
        <!-- Top Top Bar Accent -->
        <div class="h-2 bg-gradient-to-r from-red-800 via-pmr-primary to-slate-900"></div>

        <div class="p-6 sm:p-10">
            <!-- Header Logo Resmi Gabungan (PMI | SMAN 1 Ciawi) -->
            <div class="flex justify-center mb-6">
                <a href="{{ route('home') }}" class="group block" title="Kembali ke Beranda">
                    <div class="bg-white border border-slate-200 rounded-2xl p-2.5 sm:p-3 shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300">
                        <img src="{{ asset('images/logo_login_official.png') }}" 
                             alt="Logo PMI & SMAN 1 Ciawi" 
                             class="h-12 sm:h-16 w-auto object-contain">
                    </div>
                </a>
            </div>

            <!-- Judul & Subtitle -->
            <div class="text-center mb-6">
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    Portal Akses Backoffice & Area Anggota
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                    Silakan masuk menggunakan NRI (Nomor Registrasi Induk) atau NIP Pembina
                </p>
            </div>

            <!-- Flash Alert Messages -->
            @if (session('auth_error'))
                <div class="mb-5 bg-red-50 border-l-4 border-red-600 text-red-800 px-4 py-3 rounded-xl text-xs sm:text-sm flex items-start gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-600 mt-0.5 text-base flex-shrink-0"></i>
                    <div>
                        <strong class="font-bold">Akses Ditolak:</strong>
                        <div>{{ session('auth_error') }}</div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm flex items-start gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-base flex-shrink-0"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-5 bg-sky-50 border-l-4 border-sky-600 text-sky-800 px-4 py-3 rounded-xl text-xs sm:text-sm flex items-start gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-info text-sky-600 mt-0.5 text-base flex-shrink-0"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border-l-4 border-red-600 text-red-800 px-4 py-3 rounded-xl text-xs sm:text-sm shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Input NRI / NIP -->
                <div>
                    <label for="login_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor Registrasi (NRI) / NIP Pembina
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-slate-400 text-sm">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <input type="text" 
                               id="login_id" 
                               name="login_id" 
                               value="{{ old('login_id') }}" 
                               required 
                               placeholder="Contoh: 2026.01.001 / NIP Pembina"
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition shadow-inner">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                        <a href="{{ route('admin.password.forgot') }}" class="text-xs font-semibold text-red-700 hover:text-red-800 hover:underline transition">
                            Lupa Kata Sandi?
                        </a>
                    </div>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-slate-400 text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="••••••••"
                               class="w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition shadow-inner">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute right-3.5 text-slate-400 hover:text-slate-600 p-1 focus:outline-none">
                            <i id="eye-icon" class="fa-solid fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-pmr-primary rounded border-slate-300 focus:ring-pmr-primary accent-pmr-primary">
                        <span>Ingat Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-red-700 via-pmr-primary to-red-800 hover:from-red-800 hover:to-pmr-dark text-white font-bold text-sm tracking-wide py-3.5 rounded-xl shadow-lg shadow-red-900/20 hover:shadow-red-900/30 transition-all duration-200 active:scale-[0.99] flex items-center justify-center gap-2.5">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>

            <!-- Box Pemberitahuan Aktivasi Pertama Kali -->
            <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-start gap-3 text-xs text-slate-600">
                <div class="w-6 h-6 rounded-full bg-red-100 text-pmr-primary flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-info text-xs"></i>
                </div>
                <div>
                    <span class="font-bold text-slate-800 block mb-0.5">Aktivasi Pertama Kali?</span>
                    <span>Masukkan nomor NRI/NIP Anda untuk verifikasi otomatis dan pembuatan kata sandi akun baru, atau <a href="{{ route('admin.activate') }}" class="font-bold text-pmr-primary hover:underline">klik di sini</a>.</span>
                </div>
            </div>

            <!-- Footer Kembali ke Beranda -->
            <div class="mt-6 pt-5 border-t border-slate-200 text-center">
                <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Website Utama PMR Wira
                </a>
            </div>
        </div>

        <!-- SSL Security Badge Footer -->
        <div class="bg-slate-50 border-t border-slate-200 py-2.5 px-4 text-center">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                <i class="fa-solid fa-shield-halved text-emerald-600"></i> Koneksi Terenkripsi SSL 256-Bit
            </span>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
