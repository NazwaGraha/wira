<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login Petugas Parkir Lapangan - SBB IV 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-slate-100 min-h-screen flex flex-col font-sans select-none p-4 sm:p-6 justify-between">

    <!-- Top Brand Header -->
    <div class="pt-6 sm:pt-10 text-center space-y-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold uppercase tracking-wider">
            <i class="fa-solid fa-square-parking text-rose-400"></i> Portal Petugas Parkir
        </div>
        <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-rose-600 to-red-600 mx-auto flex items-center justify-center text-white text-3xl shadow-xl shadow-rose-950/60 border border-white/20">
            <i class="fa-solid fa-id-card-clip"></i>
        </div>
        <h1 class="text-2xl font-black tracking-tight text-white">
            Petugas Parkir Lapangan
        </h1>
        <p class="text-slate-400 text-xs max-w-xs mx-auto leading-relaxed">
            SBB IV PMR Wira &bull; Masukkan username dan password petugas yang diberikan oleh panitia.
        </p>
    </div>

    <!-- Login Form Card -->
    <div class="my-auto py-6 max-w-md w-full mx-auto">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md space-y-5">
            
            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-950/80 border border-rose-800 text-rose-200 text-xs flex items-start gap-2.5 shadow-md">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm mt-0.5 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-3.5 rounded-2xl bg-rose-950/80 border border-rose-800 text-rose-200 text-xs flex items-start gap-2.5 shadow-md">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm mt-0.5 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('warning'))
                <div class="p-3.5 rounded-2xl bg-amber-950/80 border border-amber-800 text-amber-200 text-xs flex items-start gap-2.5 shadow-md">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400 text-sm mt-0.5 shrink-0"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="p-3.5 rounded-2xl bg-blue-950/80 border border-blue-800 text-blue-200 text-xs flex items-start gap-2.5 shadow-md">
                    <i class="fa-solid fa-circle-info text-blue-400 text-sm mt-0.5 shrink-0"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-950/80 border border-emerald-800 text-emerald-200 text-xs flex items-start gap-2.5 shadow-md">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm mt-0.5 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('parking.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Username Input -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">
                        Username Petugas
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                            placeholder="Contoh: petugas_parkir1"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-rose-500 focus:outline-none rounded-2xl pl-11 pr-4 py-3 text-sm text-white font-bold placeholder-slate-600 transition shadow-inner">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">
                        Kata Sandi / Password
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                        <input type="password" id="inputPassword" name="password" required
                            placeholder="Masukkan kata sandi"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-rose-500 focus:outline-none rounded-2xl pl-11 pr-11 py-3 text-sm text-white placeholder-slate-600 transition shadow-inner">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-3.5 text-slate-500 hover:text-slate-300 transition text-sm">
                            <i id="eyeIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded text-rose-600 bg-slate-950 border-slate-700 focus:ring-0">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-rose-600 via-red-600 to-rose-700 hover:from-rose-700 hover:to-red-800 text-white font-black text-sm rounded-2xl shadow-xl shadow-rose-950/50 transition flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                    <i class="fa-solid fa-arrow-right-to-bracket text-base"></i> Masuk ke Scanner Parkir
                </button>
            </form>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center pb-4 text-[11px] text-slate-500 space-y-1">
        <div>PMR Wira SMAN 1 Ciawi Kabupaten Bogor</div>
        <div class="text-[10px] text-slate-600">Akun petugas & penugasan pos dikelola oleh panitia via panel admin parkir.</div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passInput = document.getElementById('inputPassword');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
