<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $registration ? 'Portal Nilai - ' . $registration->school_name : 'Portal Resmi Peserta Lomba - Sua Bhakti Berkarya IV' }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: { 950: '#070b14' }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col font-sans select-none overflow-x-hidden">

    @if(!$registration)
        <!-- ================= HALAMAN LOGIN PORTAL PESERTA ================= -->
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 relative">
            
            <!-- Background Glow Effects -->
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-10 right-1/4 w-80 h-80 bg-rose-700/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="w-full max-w-md relative z-10 space-y-6">
                
                <!-- Logo & Brand Header -->
                <div class="text-center space-y-2">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-600 to-rose-700 text-white flex items-center justify-center text-3xl mx-auto shadow-xl shadow-red-950/60 border border-white/20">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div>
                        <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30 text-[10px] font-black uppercase tracking-wider inline-block">
                            SUA BHAKTI BERKARYA IV 2026
                        </span>
                        <h1 class="text-xl sm:text-2xl font-black text-white mt-1">PORTAL PESERTA LOMBA</h1>
                        <p class="text-xs text-slate-400">PMR Wira SMAN 1 Ciawi Bogor</p>
                    </div>
                </div>

                <!-- Login Box -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-5">
                    
                    <div class="border-b border-slate-800 pb-3">
                        <h2 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-lock text-rose-500"></i>
                            <span>Masuk ke Dashboard Nilai</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Gunakan email pendaftaran dan nomor registrasi kwitansi resmi.
                        </p>
                    </div>

                    <!-- Alert Box -->
                    <div id="loginAlert" class="hidden p-3 rounded-2xl text-xs flex items-start gap-2.5">
                        <i id="loginAlertIcon" class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-sm"></i>
                        <span id="loginAlertMsg" class="min-w-0 flex-1 leading-snug"></span>
                    </div>

                    <form id="formLogin" onsubmit="handlePortalLogin(event)" class="space-y-4">
                        <!-- Input Email -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">
                                <i class="fa-solid fa-envelope mr-1 text-slate-500"></i> Alamat Email Pembina / Pendaftar:
                            </label>
                            <input type="email" id="inputEmail" required placeholder="pembina@sekolah.sch.id" class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-xs text-white placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-rose-500 shadow-inner">
                        </div>

                        <!-- Input No. Registrasi / Bukti Pembayaran -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">
                                <i class="fa-solid fa-receipt mr-1 text-slate-500"></i> No. Registrasi / Kwitansi:
                            </label>
                            <input type="text" id="inputCode" required placeholder="Contoh: SBB-W54342H atau SBB-M64389O" class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-xs text-white uppercase font-mono tracking-wider placeholder:text-slate-600 placeholder:normal-case placeholder:font-sans focus:outline-none focus:ring-2 focus:ring-rose-500 shadow-inner">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btnLoginSubmit" class="w-full py-3.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-black text-xs sm:text-sm rounded-2xl shadow-lg shadow-rose-950/40 transition active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>Buka Portal Peserta</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </form>

                    <div class="pt-2 text-center text-[10.5px] text-slate-500 border-t border-slate-800/80">
                        Hanya pendaftaran yang berstatus <strong class="text-emerald-400 font-semibold">Lunas Terverifikasi</strong> yang dapat mengakses portal ini.
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="text-center">
                    <a href="{{ url('/') }}" class="text-xs text-slate-400 hover:text-white transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Kembali ke Beranda SBB IV</span>
                    </a>
                </div>

            </div>

            <!-- ================= MODAL SELAMAT DATANG (WELCOME MODAL) ================= -->
            <div id="modalWelcome" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center p-4 hidden">
                <div class="bg-slate-900 border border-emerald-500/40 rounded-3xl w-full max-w-sm p-6 shadow-2xl text-center space-y-4 animate-in zoom-in-95 duration-200">
                    <div class="w-16 h-16 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-3xl mx-auto shadow-lg shadow-emerald-950/60">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 block">KONTINGEN TERVERIFIKASI</span>
                        <h3 class="text-lg font-black text-white mt-1 uppercase" id="welcomeSchool">SMAN 1 CIAWI BOGOR</h3>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <span id="welcomeLevel" class="px-2 py-0.5 rounded text-[9.5px] font-black uppercase bg-rose-500/20 text-rose-400 border border-rose-500/30">WIRA</span>
                            <span id="welcomeCode" class="font-mono text-xs text-slate-300 font-bold">SBB-W54342H</span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        Data pendaftaran valid. Anda dapat memantau perolehan nilai seluruh cabang lomba yang diikuti secara real-time.
                    </p>

                    <button type="button" onclick="continueToDashboard()" class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-xs sm:text-sm rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2">
                        <span>Lanjutkan ke Dashboard</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

        </div>

    @else
        <!-- ================= DASHBOARD PORTAL PESERTA LOMBA ================= -->
        
        <!-- Top App Bar / Desktop Header -->
        <header class="bg-slate-900 border-b border-slate-800 px-4 sm:px-6 py-3 sticky top-0 z-30 shadow-md">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
                
                <!-- Left: Identity -->
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-600 to-rose-700 flex items-center justify-center text-white text-base shadow-md shrink-0">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[9.5px] font-black text-rose-400 uppercase tracking-wider block truncate">
                                PORTAL RESMI PESERTA LOMBA
                            </span>
                            <span class="hidden sm:inline-block px-1.5 py-0.2 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[8px] font-bold">
                                SBB IV 2026
                            </span>
                        </div>
                        <h1 class="text-xs sm:text-sm font-black text-white truncate max-w-[200px] sm:max-w-md">
                            {{ $registration->school_name }}
                        </h1>
                    </div>
                </div>

                <!-- Right: Live Sync Indicator & Actions -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <!-- Live Auto-update Pulse Pill -->
                    <div class="bg-slate-950/80 border border-slate-800 rounded-xl px-2.5 py-1.5 flex items-center gap-1.5 text-[10px] font-mono text-slate-300 shadow-inner" title="Pembaruan otomatis tiap 20 detik">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="hidden md:inline text-slate-400 font-sans">Live:</span>
                        <span id="countdownText" class="font-bold text-emerald-400">20s</span>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('participant.logout') }}" method="POST" class="inline" onsubmit="return confirm('Keluar dari portal nilai peserta?')">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 flex items-center justify-center text-xs transition border border-slate-700 active:scale-95" title="Keluar dari Portal">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>

            </div>
        </header>

        <!-- Main Container (Responsive Grid: Mobile Native & Desktop Widescreen) -->
        <main class="flex-1 max-w-7xl mx-auto w-full p-3 sm:p-6 space-y-4 sm:space-y-6">

            <!-- Hero Kontingen Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-800 border border-slate-800 rounded-3xl p-4 sm:p-6 shadow-xl relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-rose-600/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                    <div class="space-y-1.5 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase font-mono {{ strtolower($registration->level) === 'wira' ? 'bg-red-500/20 text-rose-400 border border-rose-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30' }}">
                                TINGKAT {{ strtoupper($registration->level) }}
                            </span>
                            <span class="font-mono text-xs font-bold text-slate-300 bg-slate-950 px-2.5 py-0.5 rounded-lg border border-slate-800">
                                {{ $registration->registration_code }}
                            </span>
                            <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-lg">
                                <i class="fa-solid fa-circle-check text-[9px] mr-1"></i> Terverifikasi Lunas
                            </span>
                        </div>

                        <h2 class="text-base sm:text-2xl font-black text-white uppercase tracking-tight">
                            {{ $registration->school_name }}
                        </h2>

                        <div class="text-xs text-slate-400 flex items-center gap-2 sm:gap-4 flex-wrap">
                            <span><i class="fa-solid fa-user-tie text-slate-500 mr-1.5"></i>Pembina: <strong class="text-slate-200">{{ $registration->advisor_name ?? '-' }}</strong></span>
                            <span>•</span>
                            <span><i class="fa-solid fa-flag text-rose-500 mr-1.5"></i>Total Mengikuti: <strong class="text-white">{{ $teamsData->count() }} Mata Lomba</strong></span>
                        </div>
                    </div>

                    <!-- Export Report Action Buttons -->
                    <div class="flex items-center gap-2 sm:gap-2.5 shrink-0 flex-wrap">
                        <!-- PDF Download -->
                        <a href="{{ route('participant.export-pdf') }}" target="_blank" class="px-3.5 sm:px-4 py-2 sm:py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-black text-xs rounded-xl shadow-md transition active:scale-95 flex items-center gap-2">
                            <i class="fa-solid fa-file-pdf text-sm"></i>
                            <span>Rekap PDF</span>
                        </a>

                        <!-- Excel Export -->
                        <a href="{{ route('participant.export-excel') }}" class="px-3.5 sm:px-4 py-2 sm:py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs rounded-xl shadow-md transition active:scale-95 flex items-center gap-2">
                            <i class="fa-solid fa-file-excel text-sm"></i>
                            <span>Export Excel</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Section Title & Score Notice Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-2">
                <div>
                    <h3 class="text-sm sm:text-base font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-simple text-rose-500"></i>
                        <span>Perolehan Nilai Cabang Lomba ({{ $teamsData->count() }} Lomba)</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">
                        Nilai diperbarui secara real-time langsung saat juri menyimpan penilaian.
                    </p>
                </div>
                <div class="text-[10px] text-slate-400 font-mono">
                    Sinkronisasi Terakhir: <span id="lastSyncedTime" class="text-slate-200 font-bold">{{ now()->format('H:i:s') }} WIB</span>
                </div>
            </div>

            <!-- ================= CONTEST CARDS (GRID 5 ATAU 7 MATA LOMBA) ================= -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4" id="contestCardsContainer">
                @forelse($teamsData as $t)
                    <div class="contest-card bg-slate-900 border border-slate-800 rounded-3xl p-4 sm:p-5 shadow-lg flex flex-col justify-between transition hover:border-slate-700 relative overflow-hidden" data-team-id="{{ $t['team_id'] }}">
                        
                        <!-- Top Card Header -->
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="px-2 py-0.5 rounded-lg text-[9.5px] font-black uppercase bg-slate-950 border border-slate-800 text-slate-300 font-mono">
                                        No. Undian: <strong class="text-rose-400">{{ $t['order_number'] }}</strong>
                                    </span>
                                    <h4 class="text-sm sm:text-base font-black text-white mt-1.5 leading-snug">
                                        {{ $t['category_name'] }}
                                    </h4>
                                    <div class="text-xs text-slate-400 font-medium">
                                        {{ $t['team_name'] }}
                                    </div>
                                </div>

                                <div class="shrink-0 text-right">
                                    <span class="status-badge px-2 py-0.5 rounded-full text-[9px] font-bold uppercase inline-block {{ $t['has_score'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                                        {{ $t['status_label'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Big Score Hero Display -->
                            <div class="bg-slate-950/90 border border-slate-800 rounded-2xl p-3 text-center shadow-inner relative">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">
                                    NILAI AKHIR LOMBA
                                </span>
                                
                                <div class="py-1">
                                    @if($t['is_disqualified'])
                                        <span class="score-value font-mono font-black text-xl text-rose-500">
                                            DISKUALIFIKASI
                                        </span>
                                    @elseif($t['has_score'])
                                        <span class="score-value font-mono font-black text-2xl sm:text-3xl text-amber-400 tracking-tight">
                                            {{ $t['formatted_score'] }}
                                        </span>
                                    @else
                                        <span class="score-value text-xs font-bold text-slate-500 italic block py-1.5">
                                            <i class="fa-solid fa-hourglass-half mr-1 text-amber-400/80"></i> Menunggu Penilaian Juri
                                        </span>
                                    @endif
                                </div>

                                @if($t['time_recorded'])
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        Waktu Tampil: <strong class="text-slate-200">{{ $t['time_recorded'] }}</strong>
                                    </div>
                                @endif
                            </div>

                            <!-- Team Members Roster -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase">
                                    <span>Anggota Regu ({{ $t['members_count'] }} Peserta):</span>
                                </div>
                                <div class="bg-slate-950/60 rounded-xl p-2 border border-slate-800/80 max-h-24 overflow-y-auto space-y-0.5 text-[11px] text-slate-300">
                                    @forelse($t['member_names'] as $mIdx => $mName)
                                        <div class="truncate flex items-center gap-1.5">
                                            <span class="text-[9px] font-mono text-slate-500">{{ $mIdx + 1 }}.</span>
                                            <span>{{ $mName }}</span>
                                        </div>
                                    @empty
                                        <div class="text-slate-500 italic text-[10px]">(Daftar anggota belum diisi)</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Card Bottom Action: Export Per Mata Lomba -->
                        <div class="pt-3 mt-3 border-t border-slate-800/80">
                            <a href="{{ route('participant.export-pdf', ['category_id' => $t['category_id']]) }}" target="_blank" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-[11px] rounded-xl border border-slate-700 transition flex items-center justify-center gap-1.5 active:scale-95">
                                <i class="fa-solid fa-file-pdf text-[10px] text-rose-400"></i>
                                <span>Cetak Nilai Lomba Ini (PDF)</span>
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full p-12 text-center bg-slate-900 rounded-3xl border border-slate-800 text-slate-400 text-xs">
                        Belum ada regu mata lomba yang terdaftar pada kontingen ini.
                    </div>
                @endforelse
            </div>

            <!-- Kebijakan Privasi & Ketentuan Transparansi -->
            <div class="p-4 bg-slate-900/60 border border-slate-800 rounded-2xl text-xs space-y-1 text-slate-400">
                <div class="font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-rose-500"></i>
                    <span>Ketentuan & Batasan Informasi Portal:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-slate-400 pl-1 leading-relaxed">
                    <li>Portal ini hanya menampilkan rekapitulasi data dan nilai resmi kontingen <strong>{{ $registration->school_name }}</strong>. Data sekolah lain terisolasi dan tidak dapat diakses.</li>
                    <li>Untuk menjaga objektivitas dan kerahasiaan hasil perlombaan, portal ini <strong>tidak memuat peringkat (ranking) ataupun penentuan juara</strong>.</li>
                    <li>Pengumuman Juara Umum, Juara Kategori, dan Juara Bergilir akan disampaikan secara resmi oleh Dewan Juri pada Upacara Penutupan.</li>
                </ul>
            </div>

        </main>
    @endif

    <!-- JavaScript Portal Logic -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ================= LOGIN PORTAL VIA AJAX =================
        async function handlePortalLogin(e) {
            e.preventDefault();
            const email = document.getElementById('inputEmail').value.trim();
            const code = document.getElementById('inputCode').value.trim();
            const btn = document.getElementById('btnLoginSubmit');
            const alertBox = document.getElementById('loginAlert');
            const alertMsg = document.getElementById('loginAlertMsg');
            const alertIcon = document.getElementById('loginAlertIcon');

            if (!email || !code) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memeriksa Data...';
            alertBox.classList.add('hidden');

            try {
                const res = await fetch("{{ route('participant.login') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        registration_code: code
                    })
                });

                const data = await res.json();

                if (data.success) {
                    // Munculkan Modal Selamat Datang
                    document.getElementById('welcomeSchool').innerText = data.school_name;
                    document.getElementById('welcomeCode').innerText = data.registration_code;
                    document.getElementById('welcomeLevel').innerText = data.level;
                    document.getElementById('modalWelcome').classList.remove('hidden');
                } else {
                    alertBox.className = 'p-3 rounded-2xl text-xs flex items-start gap-2.5 bg-rose-500/10 border border-rose-500/30 text-rose-300';
                    alertIcon.className = 'fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-sm text-rose-400';
                    alertMsg.innerText = data.message || 'Pendaftaran tidak ditemukan atau belum diverifikasi lunas.';
                    alertBox.classList.remove('hidden');
                }
            } catch (err) {
                alertBox.className = 'p-3 rounded-2xl text-xs flex items-start gap-2.5 bg-rose-500/10 border border-rose-500/30 text-rose-300';
                alertIcon.className = 'fa-solid fa-triangle-exclamation mt-0.5 shrink-0 text-sm text-rose-400';
                alertMsg.innerText = 'Terjadi gangguan jaringan saat menghubungi server: ' + err.message;
                alertBox.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Buka Portal Peserta</span> <i class="fa-solid fa-arrow-right"></i>';
            }
        }

        function continueToDashboard() {
            window.location.reload();
        }

        // ================= AUTO-UPDATE POLLING TIAP 20 DETIK =================
        @if($registration)
            let countdown = 20;
            const countdownEl = document.getElementById('countdownText');
            const lastSyncedEl = document.getElementById('lastSyncedTime');

            setInterval(() => {
                countdown--;
                if (countdownEl) {
                    countdownEl.innerText = `${countdown}s`;
                }

                if (countdown <= 0) {
                    countdown = 20;
                    pollLiveData();
                }
            }, 1000);

            async function pollLiveData() {
                try {
                    const res = await fetch("{{ route('participant.live-data') }}", {
                        headers: { 'Accept': 'application/json' }
                    });
                    const result = await res.json();

                    if (result.success && result.teams) {
                        if (lastSyncedEl && result.server_time) {
                            lastSyncedEl.innerText = `${result.server_time} WIB`;
                        }

                        // Update masing-masing kartu nilai tanpa reload layar
                        result.teams.forEach(team => {
                            const card = document.querySelector(`.contest-card[data-team-id="${team.team_id}"]`);
                            if (card) {
                                // Status badge
                                const badge = card.querySelector('.status-badge');
                                if (badge) {
                                    badge.innerText = team.status_label;
                                    badge.className = `status-badge px-2 py-0.5 rounded-full text-[9px] font-bold uppercase inline-block ${
                                        team.has_score ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
                                    }`;
                                }

                                // Score display
                                const scoreVal = card.querySelector('.score-value');
                                if (scoreVal) {
                                    if (team.is_disqualified) {
                                        scoreVal.className = 'score-value font-mono font-black text-xl text-rose-500';
                                        scoreVal.innerText = 'DISKUALIFIKASI';
                                    } else if (team.has_score) {
                                        scoreVal.className = 'score-value font-mono font-black text-2xl sm:text-3xl text-amber-400 tracking-tight';
                                        scoreVal.innerText = team.formatted_score;
                                    } else {
                                        scoreVal.className = 'score-value text-xs font-bold text-slate-500 italic block py-1.5';
                                        scoreVal.innerHTML = '<i class="fa-solid fa-hourglass-half mr-1 text-amber-400/80"></i> Menunggu Penilaian Juri';
                                    }
                                }
                            }
                        });
                    } else if (result.message && result.message.includes('Sesi')) {
                        // Sesi habis
                        window.location.reload();
                    }
                } catch (e) {
                    console.warn('Gagal melakukan polling live data:', e);
                }
            }
        @endif
    </script>
</body>
</html>
