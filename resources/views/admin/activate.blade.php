<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun & Buat Kata Sandi | PMR Wira SMAN 1 Ciawi</title>
    
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
            <!-- Header Logo -->
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
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold mb-2">
                    <i class="fa-solid fa-sparkles text-amber-500"></i> Aktivasi Akun Pertama Kali
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Buat Kata Sandi Akun Baru
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Atur kata sandi akun Anda untuk mengakses Portal Backoffice & Client Area
                </p>
            </div>

            <!-- Member Info Card (Jika ditemukan) -->
            @if ($member)
                <div class="mb-5 p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-3.5">
                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                    <div class="min-w-0">
                        <div class="font-extrabold text-sm text-slate-900 truncate">{{ $member->name }}</div>
                        <div class="text-xs text-slate-500">
                            NRI: <span class="font-bold text-slate-800">{{ $member->nri }}</span> 
                            @if($member->nip) | NIP: <span class="font-bold text-slate-800">{{ $member->nip }}</span>@endif
                        </div>
                        <div class="text-xs text-pmr-primary font-semibold mt-0.5">{{ $member->position ?: 'Anggota PMR Wira' }}</div>
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-5 bg-sky-50 border-l-4 border-sky-600 text-sky-800 px-4 py-3 rounded-xl text-xs sm:text-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-sky-600 mt-0.5 text-base flex-shrink-0"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border-l-4 border-red-600 text-red-800 px-4 py-3 rounded-xl text-xs sm:text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form Aktivasi -->
            <form action="{{ route('admin.activate.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="nri" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor Registrasi (NRI) / NIP Pembina
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-slate-400 text-sm">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <input type="text" 
                               id="nri" 
                               name="nri" 
                               value="{{ old('nri', $nri) }}" 
                               required 
                               placeholder="Contoh: 2026.01.001"
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                    </div>
                </div>

                @if (!$member || empty($member->email))
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Email (Untuk Notifikasi & Reset Kata Sandi)
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400 text-sm">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="email.aktif@anda.com"
                                   class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                        </div>
                    </div>
                @endif

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Buat Kata Sandi Baru
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-slate-400 text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="Minimal 6 karakter"
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Ulangi Kata Sandi Baru
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-slate-400 text-sm">
                            <i class="fa-solid fa-lock-open"></i>
                        </span>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required 
                               placeholder="Ketik ulang kata sandi baru"
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-red-700 via-pmr-primary to-red-800 hover:from-red-800 hover:to-pmr-dark text-white font-bold text-sm tracking-wide py-3.5 rounded-xl shadow-lg transition active:scale-[0.99] flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Simpan Kata Sandi & Aktifkan Akun</span>
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
