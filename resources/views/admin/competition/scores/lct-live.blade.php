<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LIVE SCOREBOARD - {{ $category->name }} ({{ strtoupper($category->level) }})</title>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Chakra+Petch:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    {{-- Tailwind CSS CDN for Standalone Live Stage View --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['Chakra Petch', 'monospace'],
                    },
                    colors: {
                        brand: {
                            gold: '#F59E0B',
                            red: '#E11D48',
                            blue: '#0284C7',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(0.99); }
        }
        .animate-pulse-slow {
            animation: pulse-slow 3s infinite ease-in-out;
        }
        .glow-gold {
            box-shadow: 0 0 35px rgba(245, 158, 11, 0.35);
        }
        .glow-red {
            box-shadow: 0 0 35px rgba(225, 29, 72, 0.35);
        }
        .glow-cyan {
            box-shadow: 0 0 35px rgba(6, 182, 212, 0.35);
        }
        .bg-mesh {
            background-color: #030712;
            background-image: 
                radial-gradient(at 10% 15%, rgba(225, 29, 72, 0.18) 0px, transparent 50%),
                radial-gradient(at 90% 15%, rgba(14, 165, 233, 0.18) 0px, transparent 50%),
                radial-gradient(at 50% 85%, rgba(245, 158, 11, 0.15) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-mesh text-slate-100 min-h-screen flex flex-col font-sans select-none overflow-x-hidden">

    {{-- Top Bar / Live Control Header --}}
    <header class="border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-md px-6 py-4 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
            
            {{-- Brand & Event Info --}}
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 p-0.5 shadow-lg flex items-center justify-center">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                        <i class="fa-solid fa-bolt-lightning text-amber-400 text-xl"></i>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 uppercase tracking-widest animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                            LIVE PANGGUNG
                        </span>
                        <span class="text-xs text-slate-400 font-medium tracking-wide">
                            {{ $category->event->name ?? 'SUA BHAKTI BERKARYA' }}
                        </span>
                    </div>
                    <h1 class="text-xl md:text-2xl font-black tracking-tight text-white flex items-center gap-2">
                        <span>{{ $category->name }}</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-slate-800 text-amber-300 font-bold border border-slate-700 uppercase">
                            {{ $category->level }}
                        </span>
                    </h1>
                </div>
            </div>

            {{-- Stage Switcher --}}
            <div class="flex items-center gap-2 bg-slate-900/90 p-1.5 rounded-2xl border border-slate-800 shadow-inner">
                <a href="{{ route('admin.competition-scores.lct-live', ['category' => $category->id, 'stage' => 2, 'termin' => 1]) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $stage == 2 && $termin == 1 ? 'bg-gradient-to-r from-sky-600 to-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Semi Termin 1</span>
                </a>
                <a href="{{ route('admin.competition-scores.lct-live', ['category' => $category->id, 'stage' => 2, 'termin' => 2]) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $stage == 2 && $termin == 2 ? 'bg-gradient-to-r from-sky-600 to-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Semi Termin 2</span>
                </a>
                <a href="{{ route('admin.competition-scores.lct-live', ['category' => $category->id, 'stage' => 2, 'termin' => 3]) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $stage == 2 && $termin == 3 ? 'bg-gradient-to-r from-sky-600 to-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Semi Termin 3</span>
                </a>
                <a href="{{ route('admin.competition-scores.lct-live', ['category' => $category->id, 'stage' => 3]) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ $stage == 3 ? 'bg-gradient-to-r from-amber-500 to-rose-600 text-white shadow-md shadow-rose-900/30' : 'text-amber-400 hover:text-amber-200 hover:bg-amber-950/30' }}">
                    <i class="fa-solid fa-trophy"></i>
                    <span>GRAND FINAL</span>
                </a>
            </div>

            {{-- Actions: Clock, Refresh Toggle & Fullscreen --}}
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div id="liveClock" class="font-mono text-base font-bold text-slate-200">00:00:00</div>
                    <div class="text-[10px] text-emerald-400 flex items-center justify-end gap-1 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span id="refreshLabel">Auto 5s</span>
                    </div>
                </div>

                <button onclick="toggleAutoRefresh()" id="btnAutoRefresh" class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 transition" title="Jeda / Mulai Auto-Refresh">
                    <i class="fa-solid fa-sync fa-spin text-sm" id="iconSync"></i>
                </button>

                <button onclick="toggleFullscreen()" class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 transition" title="Layar Penuh (F11)">
                    <i class="fa-solid fa-expand text-sm"></i>
                </button>

                <a href="{{ route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => $stage]) }}" 
                   class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="hidden md:inline">Dashboard</span>
                </a>
            </div>

        </div>
    </header>

    {{-- Main Stage Arena --}}
    <main class="flex-1 max-w-7xl w-full mx-auto p-6 md:p-8 flex flex-col justify-center">

        @if($stage == 2)
            {{-- ==================== SEMI FINAL LIVE SCREEN ==================== --}}
            <div class="mb-8 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-sky-950/80 border border-sky-800 text-sky-300 text-xs font-black uppercase tracking-widest shadow-md">
                    <i class="fa-solid fa-layer-group text-sky-400"></i>
                    BABAK SEMI FINAL &bull; TERMIN {{ $termin }}
                </div>
                <h2 class="text-3xl md:text-5xl font-black text-white mt-3 tracking-tight">
                    PAPAN SKOR TERMIN {{ $termin }}
                </h2>
                <p class="text-slate-400 text-sm mt-1 max-w-xl mx-auto">
                    Juara 1 termin ini berhak mengunci tiket ke <strong class="text-amber-400">Grand Final</strong>, sedangkan tim lainnya bersaing memperebutkan <strong class="text-slate-300">Juara Harapan</strong>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-stretch">
                @php
                    $colors = [
                        ['border' => 'border-sky-500/40', 'glow' => 'glow-cyan', 'header' => 'from-sky-600/30 to-blue-600/10', 'badge' => 'bg-sky-500 text-white', 'btn' => 'text-sky-400'],
                        ['border' => 'border-indigo-500/40', 'glow' => 'glow-blue', 'header' => 'from-indigo-600/30 to-purple-600/10', 'badge' => 'bg-indigo-500 text-white', 'btn' => 'text-indigo-400'],
                        ['border' => 'border-purple-500/40', 'glow' => 'glow-purple', 'header' => 'from-purple-600/30 to-pink-600/10', 'badge' => 'bg-purple-500 text-white', 'btn' => 'text-purple-400'],
                    ];
                @endphp

                @forelse($terminTeams as $idx => $score)
                    @php
                        $cfg = $colors[$idx % count($colors)];
                        $team = $score->team;
                        $details = $score->score_details ?? [];
                        $isLeader = ($idx === 0 && (float)$score->final_score > 0);
                    @endphp
                    <div class="rounded-3xl bg-slate-900/80 border-2 {{ $isLeader ? 'border-amber-400/80 glow-gold scale-105' : $cfg['border'] }} p-6 flex flex-col justify-between backdrop-blur-xl relative transition-all duration-500 shadow-2xl">
                        
                        {{-- Top Badge: Posisi / Tiket Final --}}
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $isLeader ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-800 text-slate-300' }}">
                                @if($isLeader)
                                    <i class="fa-solid fa-crown mr-1"></i> PEMIMPIN TERMIN
                                @else
                                    REGU {{ chr(65 + $idx) }}
                                @endif
                            </span>

                            @if($isLeader)
                                <span class="px-3 py-1 rounded-full text-[11px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                                    <i class="fa-solid fa-ticket"></i> TIKET FINAL
                                </span>
                            @endif
                        </div>

                        {{-- Nama Sekolah & Regu --}}
                        <div class="text-center py-2 flex-1 flex flex-col justify-center">
                            <div class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-1">
                                {{ $team->registration->school_name ?? '-' }}
                            </div>
                            <h3 class="text-2xl lg:text-3xl font-black text-white leading-tight line-clamp-2">
                                {{ $team->name ?? 'Regu Tanpa Nama' }}
                            </h3>
                            <div class="text-xs text-slate-500 mt-1 font-mono">
                                No. Regu: {{ $team->team_code ?? ('#'.$team->id) }}
                            </div>
                        </div>

                        {{-- Perolehan Nilai Per Babak --}}
                        <div class="bg-slate-950/60 rounded-2xl p-4 border border-slate-800/80 my-4 space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-envelope text-amber-400"></i> Soal Amplop</span>
                                <span class="font-mono font-bold text-sm text-slate-100">{{ $details['amplop'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-hand-pointer text-sky-400"></i> Rebutan</span>
                                <span class="font-mono font-bold text-sm {{ ($details['rebutan'] ?? 0) < 0 ? 'text-rose-400' : 'text-slate-100' }}">
                                    {{ $details['rebutan'] ?? 0 }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-dot text-rose-400"></i> Balon</span>
                                <span class="font-mono font-bold text-sm text-slate-100">{{ $details['balon'] ?? 0 }}</span>
                            </div>
                        </div>

                        {{-- TOTAL SKOR BESAR (LED STYLE) --}}
                        <div class="text-center pt-2">
                            <div class="text-[11px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Poin Akumulasi</div>
                            <div class="font-mono text-5xl lg:text-6xl font-black tracking-tight {{ $isLeader ? 'text-amber-400' : 'text-white' }} drop-shadow-lg">
                                {{ number_format((float)$score->final_score, 0, ',', '.') }}
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 bg-slate-900/40 rounded-3xl border border-dashed border-slate-800">
                        <i class="fa-solid fa-users-slash text-4xl text-slate-600 mb-3"></i>
                        <h4 class="text-lg font-bold text-slate-300">Belum Ada Data Tim di Termin {{ $termin }}</h4>
                        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                            Silakan lakukan undian / generate semifinal terlebih dahulu pada Dashboard Penilaian LCT Tahap Penyisihan.
                        </p>
                    </div>
                @endforelse
            </div>

        @else
            {{-- ==================== GRAND FINAL LIVE SCREEN ==================== --}}
            <div class="mb-8 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-950/80 border border-amber-800 text-amber-300 text-xs font-black uppercase tracking-widest shadow-md">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                    BABAK PUNCAK &bull; GRAND FINAL
                </div>
                <h2 class="text-3xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-rose-300 to-amber-200 mt-3 tracking-tight">
                    PEREBUTAN JUARA 1, 2, DAN 3
                </h2>
                <p class="text-slate-400 text-sm mt-1 max-w-xl mx-auto">
                    Pertarungan 3 Juara Termin Semi-Final melalui babak <strong>Soal Amplop</strong>, <strong>Rebutan Bertingkat</strong>, dan <strong>Taruh Nilai</strong>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-stretch">
                @php
                    $podiumConfig = [
                        0 => ['title' => 'KANDIDAT JUARA 1', 'badge' => 'bg-amber-400 text-slate-950', 'border' => 'border-amber-400/90 glow-gold scale-105', 'color' => 'text-amber-400', 'icon' => 'fa-trophy'],
                        1 => ['title' => 'KANDIDAT JUARA 2', 'badge' => 'bg-slate-300 text-slate-950', 'border' => 'border-slate-400/60', 'color' => 'text-slate-200', 'icon' => 'fa-medal'],
                        2 => ['title' => 'KANDIDAT JUARA 3', 'badge' => 'bg-amber-700 text-white', 'border' => 'border-amber-700/60', 'color' => 'text-amber-600', 'icon' => 'fa-award'],
                    ];
                @endphp

                @forelse($finalScores as $idx => $score)
                    @php
                        $cfg = $podiumConfig[$idx] ?? $podiumConfig[1];
                        $team = $score->team;
                        $details = $score->score_details ?? [];
                    @endphp
                    <div class="rounded-3xl bg-slate-900/90 border-2 {{ $cfg['border'] }} p-6 md:p-8 flex flex-col justify-between backdrop-blur-xl relative transition-all duration-500 shadow-2xl">
                        
                        {{-- Top Badge: Ranking --}}
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $cfg['badge'] }} flex items-center gap-1.5 shadow-md">
                                <i class="fa-solid {{ $cfg['icon'] }}"></i>
                                {{ $cfg['title'] }}
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-400">
                                FINALIS {{ $idx + 1 }}
                            </span>
                        </div>

                        {{-- Nama Sekolah & Regu --}}
                        <div class="text-center py-4 flex-1 flex flex-col justify-center">
                            <div class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-1">
                                {{ $team->registration->school_name ?? '-' }}
                            </div>
                            <h3 class="text-2xl lg:text-4xl font-black text-white leading-tight">
                                {{ $team->name ?? 'Regu Finalis' }}
                            </h3>
                            <div class="text-xs text-slate-500 mt-2 font-mono">
                                No. Regu: {{ $team->team_code ?? ('#'.$team->id) }}
                            </div>
                        </div>

                        {{-- Perolehan Nilai Final --}}
                        <div class="bg-slate-950/70 rounded-2xl p-4 border border-slate-800 my-4 space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-envelope text-amber-400"></i> Soal Amplop</span>
                                <span class="font-mono font-bold text-sm text-slate-100">{{ $details['amplop'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-layer-group text-sky-400"></i> Rebutan Bertingkat</span>
                                <span class="font-mono font-bold text-sm {{ ($details['rebutan'] ?? 0) < 0 ? 'text-rose-400' : 'text-slate-100' }}">
                                    {{ $details['rebutan'] ?? 0 }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-coins text-emerald-400"></i> Taruh Nilai</span>
                                <span class="font-mono font-bold text-sm {{ ($details['taruh_nilai'] ?? 0) < 0 ? 'text-rose-400' : 'text-slate-100' }}">
                                    {{ $details['taruh_nilai'] ?? 0 }}
                                </span>
                            </div>
                        </div>

                        {{-- TOTAL SKOR BESAR FINAL --}}
                        <div class="text-center pt-2">
                            <div class="text-[11px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Poin Akhir</div>
                            <div class="font-mono text-5xl lg:text-7xl font-black tracking-tight {{ $cfg['color'] }} drop-shadow-lg">
                                {{ number_format((float)$score->final_score, 0, ',', '.') }}
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 bg-slate-900/40 rounded-3xl border border-dashed border-slate-800">
                        <i class="fa-solid fa-trophy text-4xl text-slate-600 mb-3"></i>
                        <h4 class="text-lg font-bold text-slate-300">Belum Ada Finalis yang Terkunci</h4>
                        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                            Selesaikan penilaian seluruh termin Semi-Final terlebih dahulu untuk memunculkan 3 Juara Termin ke Grand Final.
                        </p>
                    </div>
                @endforelse
            </div>
        @endif

    </main>

    {{-- Footer Info --}}
    <footer class="border-t border-slate-900 bg-slate-950/80 px-6 py-3 text-center text-xs text-slate-500 flex flex-wrap justify-between items-center max-w-7xl mx-auto w-full">
        <div>
            Sistem Skoring Digital LCT &bull; PMR Wira SMAN 1 Ciawi Bogor
        </div>
        <div>
            Tekan <kbd class="px-1.5 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-300 font-mono">F11</kbd> untuk Layar Penuh
        </div>
    </footer>

    {{-- Script Realtime Jam & Auto-Refresh --}}
    <script>
        // Realtime Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.innerText = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Auto Refresh
        let isAutoRefresh = true;
        let refreshInterval = null;

        function startAutoRefresh() {
            if (refreshInterval) clearInterval(refreshInterval);
            refreshInterval = setInterval(() => {
                if (isAutoRefresh) {
                    window.location.reload();
                }
            }, 5000); // 5 Detik
        }

        function toggleAutoRefresh() {
            isAutoRefresh = !isAutoRefresh;
            const label = document.getElementById('refreshLabel');
            const icon = document.getElementById('iconSync');
            if (isAutoRefresh) {
                if (label) label.innerText = 'Auto 5s';
                if (icon) icon.className = 'fa-solid fa-sync fa-spin text-sm';
            } else {
                if (label) label.innerText = 'Dijeda';
                if (icon) icon.className = 'fa-solid fa-pause text-sm text-amber-400';
            }
        }
        startAutoRefresh();

        // Fullscreen Toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error("Gagal fullscreen:", err);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
</body>
</html>
