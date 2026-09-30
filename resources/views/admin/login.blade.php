<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Backoffice | PMR WIRA SMAN 1 CIAWI</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
<body class="bg-slate-900 text-slate-100 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-slate-950 border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-16 -right-16 w-40 h-40 bg-red-600/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-16 -left-16 w-40 h-40 bg-pmr-primary/20 rounded-full blur-3xl"></div>

        <div class="relative z-10 text-center mb-8">
            <div class="bg-white rounded-2xl p-3 inline-block shadow-xl shadow-red-950/50 mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PMR Wira SMAN 1 Ciawi & PMI" class="h-16 w-auto object-contain mx-auto">
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Backoffice CMS</h1>
            <p class="text-xs text-slate-400 mt-1">Portal Pengurus PMR WIRA SMAN 1 CIAWI</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-950/60 border border-red-800/80 text-red-200 px-4 py-3 rounded-xl text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Email Administrator</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-envelope absolute left-4 text-slate-500 text-xs"></i>
                    <input type="email" name="email" required value="{{ old('email', 'admin@pmrwirasman1c.sch.id') }}" 
                           class="w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Kata Sandi</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-lock absolute left-4 text-slate-500 text-xs"></i>
                    <input type="password" name="password" required value="admin123" 
                           class="w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition">
                </div>
                <div class="mt-2 text-[11px] text-slate-500">
                    *Akun default: <code>admin@pmrwirasman1c.sch.id</code> / <code>admin123</code>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="accent-red-600 rounded">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider py-3.5 rounded-xl shadow-lg transition active:scale-98 flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk ke Backoffice
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-900 text-center text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-slate-300 transition flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Website Publik
            </a>
        </div>
    </div>

</body>
</html>
