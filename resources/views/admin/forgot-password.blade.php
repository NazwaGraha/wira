<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi | PMR Wira SMAN 1 Ciawi</title>
    
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
<body class="bg-slate-100 text-slate-800 font-sans antialiased min-h-screen flex flex-col items-center justify-center p-4">

    <div class="max-w-lg w-full bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden relative">
        <div class="h-2 bg-gradient-to-r from-red-800 via-pmr-primary to-slate-900"></div>

        <div class="p-6 sm:p-10">
            <!-- Header Logo Resmi -->
            <div class="flex justify-center mb-6">
                <a href="{{ route('home') }}" class="group block">
                    <div class="bg-white border border-slate-200 rounded-2xl p-2.5 sm:p-3 shadow-md group-hover:shadow-lg transition">
                        <img src="{{ asset('images/logo_login_official.png') }}" 
                             alt="Logo PMI & SMAN 1 Ciawi" 
                             class="h-12 sm:h-14 w-auto object-contain">
                    </div>
                </a>
            </div>

            <!-- Title -->
            <div class="text-center mb-6">
                <div class="w-12 h-12 bg-red-100 text-pmr-primary rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <i class="fa-solid fa-key text-xl"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Lupa Kata Sandi Akun
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Masukkan nomor NRI atau NIP Anda untuk menerima tautan atur ulang kata sandi ke email terdaftar.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm flex items-start gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-base flex-shrink-0"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border-l-4 border-red-600 text-red-800 px-4 py-3 rounded-xl text-xs sm:text-sm shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.password.email') }}" method="POST" class="space-y-4">
                @csrf

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
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-red-700 via-pmr-primary to-red-800 hover:from-red-800 hover:to-pmr-dark text-white font-bold text-sm tracking-wide py-3.5 rounded-xl shadow-lg transition active:scale-[0.99] flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Tautan Reset ke Email</span>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-200 text-center">
                <a href="{{ route('admin.login') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
