<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Penilaian Juri Lapangan') - PMR Wira SMAN 1 Ciawi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700;800&family=Fredoka:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        html {
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #0f172a;
            min-height: 100vh;
            min-height: 100dvh;
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
        }
        button, a, input, select, textarea {
            touch-action: manipulation;
        }
        @media screen and (max-width: 768px) {
            input:not([type="checkbox"]):not([type="radio"]):not([type="range"]),
            select,
            textarea {
                font-size: 16px !important;
            }
        }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }
        .scroll-hidden::-webkit-scrollbar { display: none; }
        .scroll-hidden { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-900 flex justify-center min-h-screen">

    <!-- Container Terpusat Mobile-First (Maksimal Lebar Layar Smartphone 480px) -->
    <div class="w-full max-w-[480px] bg-slate-50 min-h-screen flex flex-col shadow-2xl relative overflow-x-hidden">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
