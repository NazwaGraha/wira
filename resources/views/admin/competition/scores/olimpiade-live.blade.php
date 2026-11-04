<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIVE KLASEMEN OLIMPIADE - {{ $category->name }} ({{ strtoupper($category->level) }})</title>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=JetBrains+Mono:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 50% 10%, #1e1b18 0%, #0f172a 40%, #020617 100%);
            color: #f8fafc;
        }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-screen flex flex-col p-6">

    @php
        $isMadya = strcasecmp((string)$category->level, 'Madya') === 0;
        $levelBadge = $isMadya ? 'bg-blue-600 text-white' : 'bg-amber-500 text-slate-950 font-black';
        $accentText = $isMadya ? 'text-sky-400' : 'text-amber-400';
    @endphp

    <!-- Header Live -->
    <header class="max-w-7xl w-full mx-auto bg-slate-900/80 backdrop-blur-md rounded-3xl p-5 border border-slate-800 shadow-2xl flex items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="bg-white p-2 rounded-2xl shrink-0 shadow-sm">
                <img src="{{ asset('images/logo_pmi_sman1ciawi_official.png') }}" alt="Logo PMI SMAN 1 Ciawi" class="h-10 w-auto object-contain">
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full {{ $levelBadge }} text-[10px] tracking-wider uppercase font-black">
                        {{ strtoupper($category->level) }}
                    </span>
                    <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        LAYAR PANGGUNG &bull; KLASEMEN RESMI LIVE
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight mt-0.5">
                    {{ $category->name }}
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-xl bg-black/60 border border-slate-700 font-mono text-xs text-slate-300">
                Auto-Refresh: <strong id="refresh-timer" class="{{ $accentText }}">10s</strong>
            </span>
            <button type="button" onclick="location.reload()" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </header>

    <!-- Podium Top 3 (Jika ada minimal 3 tim dinilai) -->
    @if($teams->count() >= 3)
        <div class="max-w-7xl w-full mx-auto grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- JUARA 2 -->
            @php $t2 = $teams->get(1); @endphp
            <div class="bg-slate-900/90 rounded-3xl p-5 border border-slate-700/80 shadow-xl flex flex-col justify-between order-2 md:order-1">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-3 py-1 rounded-xl bg-slate-200 text-slate-900 font-black text-xs uppercase flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-medal text-slate-500"></i> JUARA 2
                    </span>
                    <span class="font-mono text-xs text-slate-400">Waktu: <strong class="text-white">{{ $t2->waktu }}</strong></span>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white leading-tight truncate">
                        {{ $t2->registration?->school_name ?? $t2->team_name }}
                    </h3>
                    <div class="text-xs text-slate-400 mt-0.5 font-medium">{{ $t2->team_label ?: $t2->team_name }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-baseline justify-between">
                    <span class="text-xs text-slate-400 font-bold uppercase">Nilai Akhir:</span>
                    <span class="text-3xl font-black font-mono text-slate-200">{{ $t2->final_score }}</span>
                </div>
            </div>

            <!-- JUARA 1 -->
            @php $t1 = $teams->get(0); @endphp
            <div class="bg-gradient-to-b from-amber-950/80 to-slate-900 rounded-3xl p-6 border-2 border-amber-500/80 shadow-2xl flex flex-col justify-between order-1 md:order-2 transform md:-translate-y-2">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-3.5 py-1 rounded-xl bg-amber-400 text-slate-950 font-black text-xs uppercase flex items-center gap-1.5 shadow-md">
                        <i class="fa-solid fa-crown text-amber-900"></i> JUARA 1
                    </span>
                    <span class="font-mono text-xs text-amber-200">Waktu: <strong class="text-white">{{ $t1->waktu }}</strong></span>
                </div>
                <div>
                    <h3 class="text-xl font-black text-white leading-tight truncate">
                        {{ $t1->registration?->school_name ?? $t1->team_name }}
                    </h3>
                    <div class="text-xs text-amber-300 mt-0.5 font-medium">{{ $t1->team_label ?: $t1->team_name }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-amber-500/30 flex items-baseline justify-between">
                    <span class="text-xs text-amber-300 font-bold uppercase">Nilai Sempurna:</span>
                    <span class="text-4xl font-black font-mono text-amber-400">{{ $t1->final_score }}</span>
                </div>
            </div>

            <!-- JUARA 3 -->
            @php $t3 = $teams->get(2); @endphp
            <div class="bg-slate-900/90 rounded-3xl p-5 border border-amber-800/60 shadow-xl flex flex-col justify-between order-3">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-3 py-1 rounded-xl bg-amber-700 text-amber-100 font-black text-xs uppercase flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-medal text-amber-300"></i> JUARA 3
                    </span>
                    <span class="font-mono text-xs text-slate-400">Waktu: <strong class="text-white">{{ $t3->waktu }}</strong></span>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white leading-tight truncate">
                        {{ $t3->registration?->school_name ?? $t3->team_name }}
                    </h3>
                    <div class="text-xs text-slate-400 mt-0.5 font-medium">{{ $t3->team_label ?: $t3->team_name }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-baseline justify-between">
                    <span class="text-xs text-slate-400 font-bold uppercase">Nilai Akhir:</span>
                    <span class="text-3xl font-black font-mono text-amber-500">{{ $t3->final_score }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Table -->
    <main class="max-w-7xl w-full mx-auto bg-slate-900/90 rounded-3xl border border-slate-800 overflow-hidden shadow-2xl flex-1">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-black/60 text-slate-400 uppercase text-xs font-black tracking-wider border-b border-slate-800">
                        <th class="py-4 px-4 text-center w-20">RANK</th>
                        <th class="py-4 px-4 text-center w-24">NO URUT</th>
                        <th class="py-4 px-5">PANGKALAN SEKOLAH / REGU</th>
                        <th class="py-4 px-4 text-center w-28 text-emerald-400">BENAR</th>
                        <th class="py-4 px-4 text-center w-28 text-rose-400">SALAH</th>
                        <th class="py-4 px-4 text-center w-28 text-slate-400">KOSONG</th>
                        <th class="py-4 px-4 text-center w-36 text-amber-300">WAKTU TEMPUH</th>
                        <th class="py-4 px-5 text-center w-36 text-white bg-slate-800/80">NILAI AKHIR</th>
                        <th class="py-4 px-4 text-center w-48 text-amber-400 bg-black">PREDIKAT JUARA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70 font-semibold">
                    @forelse($teams as $t)
                        @php
                            $r = $t->live_rank;
                            $rowBg = 'hover:bg-slate-800/40';
                            $rankClass = 'bg-slate-800 text-slate-300 border-slate-700';
                            if ($r === 1) {
                                $rowBg = 'bg-amber-500/10 hover:bg-amber-500/15';
                                $rankClass = 'bg-amber-400 text-slate-950 font-black border-amber-300';
                            } elseif ($r === 2) {
                                $rowBg = 'bg-slate-400/10 hover:bg-slate-400/15';
                                $rankClass = 'bg-slate-300 text-slate-900 font-black border-slate-200';
                            } elseif ($r === 3) {
                                $rowBg = 'bg-amber-700/10 hover:bg-amber-700/15';
                                $rankClass = 'bg-amber-700 text-amber-100 font-black border-amber-600';
                            } elseif ($r <= 6) {
                                $rowBg = 'bg-sky-950/20 hover:bg-sky-950/30';
                            }
                        @endphp
                        <tr class="{{ $rowBg }} transition">
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl font-mono font-black text-sm {{ $rankClass }} border">
                                    {{ $r }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg bg-black/40 border border-slate-800 font-mono text-xs text-slate-400">
                                    {{ $t->order_number ?: sprintf('%02d', $loop->iteration) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="font-black text-white text-base leading-tight">
                                    {{ $t->registration?->school_name ?? $t->team_name }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $t->team_label ?: $t->team_name }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-400">
                                {{ $t->benar !== null ? $t->benar : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-rose-400">
                                {{ $t->salah !== null ? $t->salah : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-400">
                                {{ $t->kosong !== null ? $t->kosong : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-amber-300">
                                {{ $t->waktu ?: '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-mono font-black text-xl text-white bg-slate-800/40">
                                {{ $t->final_score !== null ? $t->final_score : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($r === 1)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-400 text-slate-950 font-black text-xs shadow-sm">
                                        <i class="fa-solid fa-crown text-amber-900"></i> JUARA 1 (EMAS)
                                    </span>
                                @elseif($r === 2)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-200 text-slate-900 font-black text-xs shadow-sm">
                                        <i class="fa-solid fa-medal text-slate-600"></i> JUARA 2 (PERAK)
                                    </span>
                                @elseif($r === 3)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-700 text-amber-100 font-black text-xs shadow-sm">
                                        <i class="fa-solid fa-medal text-amber-300"></i> JUARA 3 (PERUNGGU)
                                    </span>
                                @elseif($r === 4)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-950 text-sky-300 border border-sky-600 font-bold text-xs">
                                        <i class="fa-solid fa-award text-sky-400"></i> JUARA HARAPAN 1
                                    </span>
                                @elseif($r === 5)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-950/80 text-sky-300 border border-sky-700 font-bold text-xs">
                                        <i class="fa-solid fa-award text-sky-400"></i> JUARA HARAPAN 2
                                    </span>
                                @elseif($r === 6)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-950/60 text-sky-300 border border-sky-800 font-bold text-xs">
                                        <i class="fa-solid fa-award text-sky-400"></i> JUARA HARAPAN 3
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-800/80 text-slate-400 border border-slate-700 text-xs font-semibold">
                                        Peringkat {{ $r }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500 font-normal">
                                Belum ada nilai yang diinput ke sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <script>
        let countdown = 10;
        setInterval(() => {
            countdown--;
            const timerEl = document.getElementById('refresh-timer');
            if (timerEl) timerEl.innerText = countdown + 's';
            if (countdown <= 0) {
                location.reload();
            }
        }, 1000);
    </script>
</body>
</html>
