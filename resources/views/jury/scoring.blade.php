@extends('jury.layout')

@section('title', 'Form Penilaian: ' . $category->name . ' (' . $category->level . ')')

@section('content')
@php
    $catName = strtolower($category->name);
    $catCode = strtoupper($category->code ?? '');
    $catLevel = ucfirst(strtolower($category->level ?? 'Madya'));

    $isLKTR = str_contains($catName, 'tandu') || str_contains($catName, 'lktr') || str_starts_with($catCode, 'LKTR');
    $isPRS = str_contains($catName, 'pendidikan remaja sebaya') || str_contains($catName, 'prs') || str_starts_with($catCode, 'PRS');
    $isMewarnai = str_contains($catName, 'mewarnai') || str_starts_with($catCode, 'MEWARNAI');
    $isOlim = str_contains($catName, 'olimpiade') || str_contains($catName, 'olympiade') || str_starts_with($catCode, 'OLIM');
    $isPP = str_contains($catName, 'pertolongan pertama') || str_starts_with($catCode, 'LPP') || str_starts_with($catCode, 'PP');

    // WARNA HANYA UNTUK HEADER CARD & BADGE NOMOR URUT (SESUAI PERMINTAAN USER):
    if ($catLevel === 'Mula') {
        $levelBadge = 'bg-emerald-600 text-white shadow-md shadow-emerald-950/50';
        $headerGradient = 'from-emerald-950 via-teal-950 to-slate-900 border-b border-emerald-600/40';
        $orderBadge = 'bg-emerald-600 text-white shadow';
        $activeDot = 'bg-emerald-400';
        $activeBorder = 'border-emerald-600/40';
        $subText = 'text-emerald-400';
    } elseif ($catLevel === 'Wira') {
        $levelBadge = 'bg-amber-500 text-slate-950 shadow-md shadow-amber-950/50 font-black';
        $headerGradient = 'from-amber-950 via-yellow-950 to-slate-900 border-b border-amber-600/40';
        $orderBadge = 'bg-amber-500 text-slate-950 shadow font-black';
        $activeDot = 'bg-amber-400';
        $activeBorder = 'border-amber-600/40';
        $subText = 'text-amber-400';
    } else { // Madya (Default Biru, Persis Screenshot User)
        $levelBadge = 'bg-blue-600 text-white shadow-md shadow-blue-950/50';
        $headerGradient = 'from-sky-950 via-blue-950 to-slate-900 border-b border-blue-600/40';
        $orderBadge = 'bg-blue-600 text-white shadow';
        $activeDot = 'bg-sky-400';
        $activeBorder = 'border-blue-600/40';
        $subText = 'text-sky-400';
    }

    $existingRubric = $existingRubric ?? [];
    $existingSubtotals = $existingSubtotals ?? [];
@endphp

<div class="flex-1 flex flex-col bg-slate-950 text-slate-100 min-h-screen pb-28">

    <!-- ================= HEADER CARD (DENGAN TEMA WARNA SESUAI TINGKATAN) ================= -->
    <div class="bg-gradient-to-r {{ $headerGradient }} px-4 pt-3 pb-3.5 shrink-0">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('jury.dashboard') }}" class="w-8 h-8 rounded-xl bg-slate-900/80 hover:bg-slate-800 flex items-center justify-center text-slate-200 text-xs border border-slate-700/60 transition active:scale-95">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                <div class="flex items-center gap-2">
                    <div class="bg-white p-1 rounded-lg shrink-0 shadow-xs border border-slate-700/60 flex items-center justify-center">
                        <img src="{{ asset('images/logo_pmi_sman1ciawi_official.png') }}" alt="Logo PMI SMAN 1 Ciawi" class="h-6 w-auto object-contain">
                    </div>
                    <div>
                        <div class="text-[9px] font-bold {{ $subText }} flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $activeDot }} animate-ping"></span>
                            PORTAL JURI • LIVE
                        </div>
                        <h2 class="text-xs sm:text-sm font-black text-white leading-tight truncate max-w-[170px]">
                            {{ $category->name }}
                        </h2>
                    </div>
                </div>
            </div>
            <!-- Pill Badge Tingkatan (Mula=Hijau, Madya=Biru, Wira=Kuning) -->
            <span class="{{ $levelBadge }} text-[9.5px] font-black px-2.5 py-0.5 rounded-full tracking-wider uppercase">
                {{ strtoupper($category->level) }}
            </span>
        </div>

        @if ($activeTeam)
            <!-- Card Regu Aktif (Persis Seperti Cuplikan Gambar) -->
            <div class="mt-2.5 bg-slate-950/85 p-2.5 rounded-2xl border {{ $activeBorder }} flex items-center justify-between shadow-inner">
                <div class="flex items-center gap-2.5 min-w-0">
                    <!-- Pill Badge No. Urut Peserta (Mula=Hijau, Madya=Biru, Wira=Kuning) -->
                    <span class="px-2.5 py-1 rounded-xl {{ $orderBadge }} font-mono font-black text-xs shrink-0">
                        {{ $activeTeam->order_number ?: ($activeTeam->team_label ?: 'A-001') }}
                    </span>
                    <div class="min-w-0">
                        <div class="text-xs font-black text-white leading-tight truncate">
                            {{ $activeTeam->school_name ?? $activeTeam->registration?->school_name ?? $activeTeam->team_name }}
                        </div>
                        <div class="text-[9.5px] text-slate-400 mt-0.5 truncate">
                            {{ $activeTeam->team_name ?: ('Regu ' . $activeTeam->team_label) }} • Peserta {{ $teams->search(fn($item) => $item->id === $activeTeam->id) + 1 }} dari {{ $teams->count() }}
                        </div>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('carousel-teams-box').classList.toggle('hidden')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-[10px] font-bold text-slate-200 flex items-center gap-1 border border-slate-700 shrink-0 ml-2 active:scale-95 transition">
                    <span>Ganti</span> <i class="fa-solid fa-chevron-right text-[8px]"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Alert Notifikasi -->
    @if (session('success'))
        <div class="m-3 p-3 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400">✕</button>
        </div>
    @endif

    @if (session('error'))
        <div class="m-3 p-3 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-300 text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-400">✕</button>
        </div>
    @endif

    <!-- Carousel Horizontal No. Urut Peserta (Dapat Di-toggle) -->
    <div id="carousel-teams-box" class="bg-slate-900 border-b border-slate-800 px-3 py-2 shrink-0">
        <div class="flex items-center justify-between text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
            <span>PILIH NO. URUT PESERTA:</span>
            <span class="text-emerald-400 font-bold">
                {{ $teams->filter(fn($t) => $t->scores->isNotEmpty())->count() }} / {{ $teams->count() }} Dinilai
            </span>
        </div>
        
        <div class="flex items-center gap-1.5 overflow-x-auto scroll-hidden pb-1">
            @forelse ($teams as $t)
                @php
                    $isActive = $activeTeam && $activeTeam->id === $t->id;
                    $hasScore = $t->scores->isNotEmpty();
                    $scoreVal = $hasScore ? number_format($t->scores->first()->final_score, 0) : null;
                    $badgeNo = $t->order_number ?: ($t->team_label ?: $loop->iteration);
                @endphp
                <a href="{{ route('jury.scoring', ['category' => $category->id, 'team_id' => $t->id]) }}"
                    class="shrink-0 px-2.5 py-1.5 rounded-xl flex flex-col items-center transition active:scale-95
                    {{ $isActive ? $orderBadge . ' ring-2 ring-white/50 scale-105' : ($hasScore ? 'bg-slate-800 text-emerald-400 border border-emerald-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800') }}">
                    <span class="font-mono text-xs font-black">{{ $badgeNo }}</span>
                    <span class="text-[7.5px] leading-tight {{ $isActive ? 'text-white/90 font-bold' : ($hasScore ? 'text-emerald-400' : 'text-slate-500') }}">
                        @if ($isActive)
                            Menilai
                        @elseif ($hasScore)
                            ✓ {{ $scoreVal }}
                        @else
                            Belum
                        @endif
                    </span>
                </a>
            @empty
                <div class="text-xs text-slate-500 py-1 italic">Belum ada regu terdaftar di cabang lomba ini.</div>
            @endforelse
        </div>
    </div>

    @if ($activeTeam)
        <!-- ================= BAGIAN DOKUMENTASI FOTO (KOMPAK & CEPAT) ================= -->
        <div class="px-3 pt-3">
            <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-camera text-sky-400"></i> Dokumentasi Foto Regu:
                    </span>
                    <span id="photo-count-badge" class="text-[9px] font-bold text-emerald-400 bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-500/30">
                        <span id="photo-count-text">{{ $activePhotos->count() }} Foto Terlampir</span>
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-2">
                    <button type="button" onclick="openCameraModal()" class="py-2 px-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow flex items-center justify-center gap-1.5 active:scale-95 transition">
                        <i class="fa-solid fa-camera"></i> Buka Kamera
                    </button>
                    <button type="button" onclick="document.getElementById('gallery-file-input').click()" class="py-2 px-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 flex items-center justify-center gap-1.5 active:scale-95 transition">
                        <i class="fa-solid fa-images text-slate-400"></i> Pilih Galeri
                    </button>
                </div>

                <input type="file" id="gallery-file-input" accept="image/*" multiple class="hidden" onchange="handleGalleryUpload(this)">
                <input type="file" id="fallback-camera-input" accept="image/*" capture="environment" class="hidden" onchange="handleGalleryUpload(this)">

                <div id="photos-grid" class="grid grid-cols-2 gap-2">
                    @forelse ($activePhotos as $idx => $photo)
                        <div id="photo-card-{{ $photo->id }}" class="relative rounded-xl overflow-hidden border border-slate-700 bg-slate-800 aspect-[4/3] group">
                            <img src="{{ $photo->photo_url }}" alt="{{ $photo->caption }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-transparent flex flex-col justify-between p-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[8px] font-mono bg-black/70 text-emerald-400 px-1 py-0.5 rounded font-bold">
                                        Foto {{ $idx + 1 }}
                                    </span>
                                    <button type="button" onclick="deletePhoto({{ $photo->id }})" class="w-4 h-4 bg-red-600 text-white rounded-full flex items-center justify-center text-[8px] font-bold">✕</button>
                                </div>
                                <div class="text-[8px] font-bold text-white truncate">{{ $photo->caption ?: 'Dokumentasi' }}</div>
                            </div>
                        </div>
                    @empty
                        <div id="empty-photos-notice" class="col-span-2 text-center py-2 bg-slate-950/60 border border-dashed border-slate-800 rounded-xl text-slate-500 text-[10px]">
                            Belum ada foto. Tekan <strong>"Buka Kamera"</strong> untuk lampiran bukti fisik.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ================= FORM PENILAIAN GRID SESUAI KATEGORI (DARK SLATE NETRAL) ================= -->
        <form id="scoring-form" action="{{ route('jury.save-score', ['category' => $category->id, 'team' => $activeTeam->id]) }}" method="POST" class="p-3 space-y-3 flex-1">
            @csrf

            <!-- Hidden Inputs Penampung Skor & Waktu -->
            <input type="hidden" name="final_score" id="input-final-score" value="{{ old('final_score', $activeScore ? $activeScore->final_score : '0') }}">
            <input type="hidden" name="minutes" id="input-minutes" value="{{ old('minutes', $recordedMinutes) }}">
            <input type="hidden" name="seconds" id="input-seconds" value="{{ old('seconds', $recordedSeconds) }}">

            @if ($isLKTR)
                <!-- ========================================================================= -->
                <!-- 1. RUBRIK KETANGKASAN TANDU REGULER (LKTR) -->
                <!-- ========================================================================= -->

                <!-- Stopwatch & Konversi Waktu LKTR -->
                <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5 text-slate-300 text-[10px] font-black uppercase">
                            <i class="fa-solid fa-stopwatch text-sky-400"></i> STOPWATCH WAKTU TEMPUH
                        </div>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-400">
                            {{ $catLevel === 'Mula' ? 'Standar Mula: Max 10 Menit' : 'Madya/Wira: Max 5:30 Menit' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between bg-black/40 rounded-xl p-2.5 border border-slate-800">
                        <div>
                            <div id="stopwatch-display" class="text-2xl font-black font-mono text-white">
                                {{ sprintf('%02d:%02d', $recordedMinutes, $recordedSeconds) }}
                            </div>
                            <div id="time-status-label" class="text-[8px] text-slate-400 mt-0.5 font-bold">Waktu Tempuh Tandu</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[8px] text-slate-400 block font-semibold">Poin Waktu:</span>
                            <span id="display-time-points" class="text-base font-black font-mono text-sky-400 bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-700">
                                +0
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 mt-2">
                        <button type="button" id="btn-timer-start" onclick="toggleStopwatch()" class="flex-1 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-[10px] flex items-center justify-center gap-1 shadow active:scale-95 transition">
                            <i class="fa-solid fa-play text-[8px]"></i> <span id="label-timer-btn">Start</span>
                        </button>
                        <button type="button" onclick="resetStopwatch()" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[10px] flex items-center gap-1 active:scale-95 transition">
                            <i class="fa-solid fa-rotate-right text-[8px]"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- Bagian I: Kekuatan Tandu (Maksimal: 400 Poin) -->
                <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black text-slate-200 uppercase tracking-wide">BAGIAN I: KEKUATAN TANDU</span>
                        <span id="subtotal-kekuatan" class="text-[10px] font-mono font-bold text-sky-400 bg-slate-800 px-2 py-0.5 rounded">
                            0 / 400
                        </span>
                    </div>

                    @php
                        $kekuatanItems = [
                            'kekuatan_1' => ['1. Kekencangan mitela', [20, 40, 60, 80], 80],
                            'kekuatan_2' => ['2. Kekuatan jaring', [20, 40, 60, 80], 80],
                            'kekuatan_3a' => ['3a. Diputar bambu kecil', [15, 30, 45, 60], 60],
                            'kekuatan_3b' => ['3b. Diinjak 4 sisi', [25, 50, 75, 100], 100],
                            'kekuatan_4' => ['4. Kekuatan tali pengaman', [20, 40, 60, 80], 80],
                        ];
                    @endphp

                    @foreach ($kekuatanItems as $key => [$label, $options, $def])
                        @php
                            $currVal = intval($existingRubric[$key] ?? $def);
                        @endphp
                        <input type="hidden" name="rubric[{{ $key }}]" id="rubric_{{ $key }}" value="{{ $currVal }}" class="rubric-lktr-kekuatan">
                        <div class="p-2 rounded-xl bg-slate-950/60 border border-slate-800/80">
                            <div class="flex items-center justify-between text-[10px] mb-1.5">
                                <span class="text-slate-200 font-medium">{{ $label }}</span>
                                <span id="display_{{ $key }}" class="font-mono font-bold text-sky-400">{{ $currVal }}</span>
                            </div>
                            <div class="grid grid-cols-4 gap-1">
                                @foreach ($options as $opt)
                                    <button type="button" onclick="setLktrKekuatan('{{ $key }}', {{ $opt }}, this)" class="btn-opt-{{ $key }} py-1 rounded text-[8.5px] font-bold transition {{ $currVal == $opt ? 'bg-blue-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                        {{ $opt }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Bagian II: Kerapihan & Ketepatan (Maksimal: 300 Poin) -->
                <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black text-slate-200 uppercase tracking-wide">BAGIAN II: KERAPIHAN & KETEPATAN</span>
                        <span id="subtotal-kerapihan" class="text-[10px] font-mono font-bold text-sky-400 bg-slate-800 px-2 py-0.5 rounded">
                            0 / 300
                        </span>
                    </div>

                    @php
                        $kerapihanItems = [
                            'kerapihan_1' => ['1. Garis tengah jaring', [5, 10, 15, 20], 20],
                            'kerapihan_2' => ['2. Sisa bambu panjang', [5, 10, 15, 20], 20],
                            'kerapihan_3' => ['3. Sisa bambu kecil', [5, 10, 15, 20], 20],
                            'kerapihan_4' => ['4. Jumlah lilitan pangkal & cincin', [20, 25, 30, 45], 45],
                            'kerapihan_5' => ['5. Jumlah silangan', [10, 15, 20, 25], 25],
                            'kerapihan_6' => ['6. Diameter tali 5mm', [10, 15, 20, 25], 25],
                            'kerapihan_7' => ['7. Jarak antar jangkar', [15, 20, 25, 30], 30],
                            'kerapihan_8' => ['8. Bentuk tandu', [10, 15, 20, 40], 40],
                            'kerapihan_9' => ['9. Sisa tali', [10, 15, 20, 25], 25],
                            'kerapihan_10' => ['10. Sunan tali', [10, 15, 20, 25], 25],
                            'kerapihan_11' => ['11. Pemasangan mitela', [10, 15, 20, 25], 25],
                        ];
                    @endphp

                    @foreach ($kerapihanItems as $key => [$label, $options, $def])
                        @php
                            $currVal = intval($existingRubric[$key] ?? $def);
                        @endphp
                        <input type="hidden" name="rubric[{{ $key }}]" id="rubric_{{ $key }}" value="{{ $currVal }}" class="rubric-lktr-kerapihan">
                        <div class="p-2 rounded-xl bg-slate-950/60 border border-slate-800/80">
                            <div class="flex items-center justify-between text-[10px] mb-1.5">
                                <span class="text-slate-200 font-medium">{{ $label }}</span>
                                <span id="display_{{ $key }}" class="font-mono font-bold text-sky-400">{{ $currVal }}</span>
                            </div>
                            <div class="grid grid-cols-4 gap-1">
                                @foreach ($options as $opt)
                                    <button type="button" onclick="setLktrKerapihan('{{ $key }}', {{ $opt }}, this)" class="btn-opt-{{ $key }} py-1 rounded text-[8.5px] font-bold transition {{ $currVal == $opt ? 'bg-blue-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                        {{ $opt }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

            @elseif ($isPRS)
                <!-- ========================================================================= -->
                <!-- 2. RUBRIK PENDIDIKAN REMAJA SEBAYA (PRS) -->
                <!-- ========================================================================= -->

                @php
                    $isWira = ($catLevel === 'Wira');
                    $prsItems = [
                        'kreativitas' => [
                            '1. Penguasaan Materi PRS',
                            'Kedalaman materi tumbuh kembang remaja, NAPZA, kespro & norma.',
                            $isWira ? [120, 160, 200] : [100, 140, 180],
                            $isWira ? 200 : 180
                        ],
                        'kesesuaian_tema' => [
                            '2. Kreativitas & Media Presentasi',
                            'Inovasi alat peraga edukatif, simulasi peran & visual interaktif.',
                            $isWira ? [120, 160, 200] : [100, 140, 180],
                            $isWira ? 200 : 180
                        ],
                        'kejelasan_bahasa' => [
                            '3. Kemampuan Komunikasi & Bahasa',
                            'Artikulasi jelas, bahasa mudah dipahami sebaya & gaya persuasif.',
                            $isWira ? [60, 80, 100] : [50, 70, 90],
                            $isWira ? 100 : 90
                        ],
                        'presentasi' => [
                            '4. Dinamika Kelompok & Audiens',
                            'Interaksi dua arah, respon tanggap atas pertanyaan teman sebaya.',
                            $isWira ? [240, 320, 400] : [200, 280, 360],
                            $isWira ? 320 : 280
                        ],
                        'kecepatan_menjawab' => [
                            '5. Sikap, Etika & Kekompakan',
                            'Kesantunan etika, rasa percaya diri dan kerjasama tim.',
                            $isWira ? [60, 80, 100] : [50, 70, 90],
                            $isWira ? 100 : 90
                        ],
                    ];
                @endphp

                <div class="space-y-2.5">
                    @foreach ($prsItems as $key => [$label, $desc, $opts, $def])
                        @php
                            $currVal = intval($existingRubric[$key] ?? $def);
                        @endphp
                        <input type="hidden" name="rubric[{{ $key }}]" id="rubric_{{ $key }}" value="{{ $currVal }}" class="rubric-prs-item">
                        <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[11px] font-bold text-slate-200">{{ $label }}</span>
                                <span id="display_{{ $key }}" class="font-mono font-black text-xs text-sky-400">
                                    {{ $currVal }} / {{ $opts[2] }}
                                </span>
                            </div>
                            <p class="text-[8.5px] text-slate-400 mb-2 leading-relaxed">{{ $desc }}</p>
                            <div class="grid grid-cols-3 gap-1.5">
                                <button type="button" onclick="setPrsScore('{{ $key }}', {{ $opts[0] }}, {{ $opts[2] }}, this)" class="btn-prs-{{ $key }} py-1.5 rounded-lg text-[9px] font-bold transition {{ $currVal == $opts[0] ? 'bg-blue-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                    Cukup ({{ $opts[0] }})
                                </button>
                                <button type="button" onclick="setPrsScore('{{ $key }}', {{ $opts[1] }}, {{ $opts[2] }}, this)" class="btn-prs-{{ $key }} py-1.5 rounded-lg text-[9px] font-bold transition {{ $currVal == $opts[1] ? 'bg-blue-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                    Baik ({{ $opts[1] }})
                                </button>
                                <button type="button" onclick="setPrsScore('{{ $key }}', {{ $opts[2] }}, {{ $opts[2] }}, this)" class="btn-prs-{{ $key }} py-1.5 rounded-lg text-[9px] font-bold transition {{ $currVal == $opts[2] ? 'bg-blue-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                    Sgt Baik ({{ $opts[2] }})
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

            @elseif ($isMewarnai)
                <!-- ========================================================================= -->
                <!-- 3. RUBRIK LOMBA MEWARNAI (MULA) -->
                <!-- ========================================================================= -->
                @php
                    $v1 = floatval($existingRubric['criteria_1'] ?? 38);
                    $v2 = floatval($existingRubric['criteria_2'] ?? 36);
                    $v3 = floatval($existingRubric['criteria_3'] ?? 18);
                @endphp

                <input type="hidden" name="rubric[criteria_1]" id="rubric_c1" value="{{ $v1 }}" class="rubric-mewarnai-item">
                <input type="hidden" name="rubric[criteria_2]" id="rubric_c2" value="{{ $v2 }}" class="rubric-mewarnai-item">
                <input type="hidden" name="rubric[criteria_3]" id="rubric_c3" value="{{ $v3 }}" class="rubric-mewarnai-item">

                <div class="space-y-2.5">
                    <!-- Kriteria 1: Kerapihan & Kebersihan (Max 40) -->
                    <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-bold text-slate-200">1. Kerapihan & Kebersihan</span>
                            <span id="display_c1" class="font-mono font-black text-xs text-sky-400">{{ $v1 }} / 40</span>
                        </div>
                        <p class="text-[8.5px] text-slate-400 mb-2">Goresan tidak keluar batas pola dan kertas bersih tanpa noda.</p>
                        <input type="range" min="1" max="40" value="{{ $v1 }}" oninput="setMewarnaiScore('c1', this.value)" class="w-full h-1.5 bg-slate-800 rounded-lg appearance-none cursor-pointer mb-2 accent-sky-400">
                        <div class="grid grid-cols-4 gap-1">
                            <button type="button" onclick="setMewarnaiScore('c1', 20)" class="py-1 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold">20</button>
                            <button type="button" onclick="setMewarnaiScore('c1', 30)" class="py-1 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold">30</button>
                            <button type="button" onclick="setMewarnaiScore('c1', 38)" class="py-1 rounded bg-blue-600 text-white text-[9px] font-black shadow">38</button>
                            <button type="button" onclick="setMewarnaiScore('c1', 40)" class="py-1 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold">40</button>
                        </div>
                    </div>

                    <!-- Kriteria 2: Kombinasi Warna (Max 40) -->
                    <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-bold text-slate-200">2. Kombinasi Warna & Estetika</span>
                            <span id="display_c2" class="font-mono font-black text-xs text-sky-400">{{ $v2 }} / 40</span>
                        </div>
                        <p class="text-[8.5px] text-slate-400 mb-2">Harmonisasi pencampuran warna, gradasi halus & teknik.</p>
                        <input type="range" min="1" max="40" value="{{ $v2 }}" oninput="setMewarnaiScore('c2', this.value)" class="w-full h-1.5 bg-slate-800 rounded-lg appearance-none cursor-pointer mb-2 accent-sky-400">
                        <div class="grid grid-cols-4 gap-1">
                            <button type="button" onclick="setMewarnaiScore('c2', 20)" class="py-1 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold">20</button>
                            <button type="button" onclick="setMewarnaiScore('c2', 30)" class="py-1 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold">30</button>
                            <button type="button" onclick="setMewarnaiScore('c2', 36)" class="py-1 rounded bg-blue-600 text-white text-[9px] font-black shadow">36</button>
                            <button type="button" onclick="setMewarnaiScore('c2', 40)" class="py-1 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold">40</button>
                        </div>
                    </div>

                    <!-- Kriteria 3: Kreativitas (Max 20) -->
                    <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-bold text-slate-200">3. Kreativitas & Inovasi</span>
                            <span id="display_c3" class="font-mono font-black text-xs text-sky-400">{{ $v3 }} / 20</span>
                        </div>
                        <p class="text-[8.5px] text-slate-400 mb-2">Penambahan ornamen latar belakang kreatif dan keberanian inovasi.</p>
                        <input type="range" min="1" max="20" value="{{ $v3 }}" oninput="setMewarnaiScore('c3', this.value)" class="w-full h-1.5 bg-slate-800 rounded-lg appearance-none cursor-pointer mb-2 accent-sky-400">
                        <div class="grid grid-cols-4 gap-1">
                            <button type="button" onclick="setMewarnaiScore('c3', 10)" class="py-1 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold">10</button>
                            <button type="button" onclick="setMewarnaiScore('c3', 15)" class="py-1 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold">15</button>
                            <button type="button" onclick="setMewarnaiScore('c3', 18)" class="py-1 rounded bg-blue-600 text-white text-[9px] font-black shadow">18</button>
                            <button type="button" onclick="setMewarnaiScore('c3', 20)" class="py-1 rounded bg-slate-800 hover:bg-slate-700 text-[9px] text-slate-300 font-bold">20</button>
                        </div>
                    </div>
                </div>

            @elseif ($isOlim)
                <!-- ========================================================================= -->
                <!-- 4. RUBRIK OLIMPIADE KEPALANGMERAHAN (OLIM) 50 SOAL -->
                <!-- ========================================================================= -->
                @php
                    $olimBenar = intval($existingRubric['benar'] ?? 0);
                    $olimSalah = intval($existingRubric['salah'] ?? 0);
                    $olimKosong = max(0, 50 - ($olimBenar + $olimSalah));
                    $olimScore = ($olimBenar - $olimSalah) * 2;
                @endphp

                <!-- Stopwatch & Input Waktu Pengerjaan -->
                <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800 space-y-2.5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase text-slate-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-stopwatch {{ $catLevel === 'Madya' ? 'text-sky-400' : 'text-amber-400' }}"></i> STOPWATCH WAKTU PENGERJAAN
                        </span>
                        <span class="text-[8.5px] font-mono text-slate-400 bg-slate-950 px-2 py-0.5 rounded border border-slate-800">
                            Format [MM]:SS
                        </span>
                    </div>

                    <div class="flex items-center justify-between bg-black/60 rounded-xl p-2.5 border border-slate-800">
                        <div>
                            <div id="stopwatch-display" class="text-2xl font-black font-mono {{ $catLevel === 'Madya' ? 'text-sky-400' : 'text-amber-400' }} tracking-wider">
                                {{ sprintf('%02d:%02d', $recordedMinutes, $recordedSeconds) }}
                            </div>
                            <div class="text-[8px] text-slate-400 mt-0.5 font-bold">Waktu Tempuh Olimpiade</div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="toggleStopwatch()" class="px-3 py-1.5 rounded-lg {{ $catLevel === 'Madya' ? 'bg-blue-600 hover:bg-blue-500 text-white' : 'bg-amber-500 hover:bg-amber-600 text-slate-950 font-black' }} font-bold text-xs active:scale-95 transition flex items-center gap-1 shadow">
                                <i id="icon-timer-btn" class="fa-solid fa-play text-[9px]"></i>
                                <span id="label-timer-btn">Mulai</span>
                            </button>
                            <button type="button" onclick="resetStopwatch()" class="p-1.5 px-2.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs border border-slate-700 active:scale-95 transition" title="Reset">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Koreksi Waktu Manual Fleksibel -->
                    <div class="bg-slate-950/70 rounded-xl p-2 border border-slate-800/80 flex items-center justify-between gap-2">
                        <span class="text-[9.5px] font-bold text-slate-400 shrink-0">Koreksi Manual:</span>
                        <div class="flex items-center gap-1.5">
                            <div class="flex items-center gap-1">
                                <input type="number" id="input-minutes-box" value="{{ $recordedMinutes }}" min="0" max="180" oninput="syncManualTime()" class="w-12 bg-black/60 border border-slate-700 rounded-lg py-1 text-center font-mono font-bold text-xs text-white">
                                <span class="text-[9px] text-slate-500">m</span>
                            </div>
                            <span class="text-slate-600">:</span>
                            <div class="flex items-center gap-1">
                                <input type="number" id="input-seconds-box" value="{{ $recordedSeconds }}" min="0" max="59" oninput="syncManualTime()" class="w-12 bg-black/60 border border-slate-700 rounded-lg py-1 text-center font-mono font-bold text-xs text-white">
                                <span class="text-[9px] text-slate-500">s</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Benar, Salah, Kosong Olimpiade -->
                <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800 space-y-3 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-[11px] font-black text-slate-100 uppercase tracking-wide">
                            LEMBAR JAWABAN (50 SOAL)
                        </span>
                        <span class="text-[9.5px] font-mono font-bold text-slate-400 bg-slate-950 px-2 py-0.5 rounded-lg border border-slate-800">
                            Bobot: B (+1) • S (-1) • K (0)
                        </span>
                    </div>

                    <!-- Row 1: Jawaban Benar -->
                    <div class="bg-slate-950/60 p-2.5 rounded-xl border border-slate-800/80 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-emerald-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Jawaban Benar (+1)
                            </div>
                            <div class="text-[9px] text-slate-400 mt-0.5">Jumlah butir soal dijawab tepat</div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="adjustMobileOlim('benar', -1)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-sm flex items-center justify-center">-</button>
                            <input type="number" name="rubric[benar]" id="mobile-olim-benar" value="{{ $olimBenar }}" min="0" max="50" oninput="recalculateMobileOlim()" class="w-14 bg-black/70 border border-emerald-500/40 rounded-lg py-1.5 text-center font-mono font-black text-base text-emerald-300 outline-none">
                            <button type="button" onclick="adjustMobileOlim('benar', 1)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-sm flex items-center justify-center">+</button>
                        </div>
                    </div>

                    <!-- Row 2: Jawaban Salah -->
                    <div class="bg-slate-950/60 p-2.5 rounded-xl border border-slate-800/80 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-rose-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-xmark"></i> Jawaban Salah (&minus;1)
                            </div>
                            <div class="text-[9px] text-slate-400 mt-0.5">Jumlah butir soal dijawab keliru</div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="adjustMobileOlim('salah', -1)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-sm flex items-center justify-center">-</button>
                            <input type="number" name="rubric[salah]" id="mobile-olim-salah" value="{{ $olimSalah }}" min="0" max="50" oninput="recalculateMobileOlim()" class="w-14 bg-black/70 border border-rose-500/40 rounded-lg py-1.5 text-center font-mono font-black text-base text-rose-300 outline-none">
                            <button type="button" onclick="adjustMobileOlim('salah', 1)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-sm flex items-center justify-center">+</button>
                        </div>
                    </div>

                    <!-- Row 3: Tidak Dijawab / Kosong -->
                    <div class="bg-slate-950/40 p-2.5 rounded-xl border border-slate-800/60 flex items-center justify-between text-slate-400">
                        <div>
                            <div class="text-xs font-bold text-slate-300 flex items-center gap-1">
                                <i class="fa-solid fa-circle-minus"></i> Tidak Dijawab (0)
                            </div>
                            <div class="text-[9px] text-slate-500 mt-0.5">Otomatis: 50 &minus; (Benar + Salah)</div>
                        </div>
                        <span id="mobile-olim-kosong" class="font-mono font-bold text-sm text-slate-300 bg-slate-800 px-3 py-1 rounded-lg border border-slate-700">
                            {{ $olimKosong }}
                        </span>
                    </div>
                </div>

            @elseif ($isPP)
                <!-- ========================================================================= -->
                <!-- 4. RUBRIK PERTOLONGAN PERTAMA (PP) RESMI SUA BHAKTI BERKARYA (MOBILE-FIRST) -->
                <!-- ========================================================================= -->
                
                @php
                    $scenarioTitle = match ($catLevel) {
                        'Mula' => '1. Sayat • 2. Mimisan',
                        'Wira' => '1. Syok • 2. Patah Tulang',
                        default => '1. Pingsan • 2. Robek',
                    };

                    $targetTimeLabel = match ($catLevel) {
                        'Mula' => 'Target: 7 Menit (1m Kasus + 6m Tindakan)',
                        'Wira' => 'Target: 8 Menit (1m Kasus + 7m Tindakan)',
                        default => 'Target: 8 Menit (1m Kasus + 7m Tindakan)',
                    };

                    if ($catLevel === 'Mula') {
                        $ppSections = [
                            1 => [
                                'title' => '1. APD (ALAT PELINDUNG DIRI)',
                                'max' => 20,
                                'items' => [
                                    '1_a' => ['label' => 'Terpasang sarung tangan lateks', 'options' => [0, 5, 10]],
                                    '1_b' => ['label' => 'Terpasang masker', 'options' => [0, 5, 10]],
                                ]
                            ],
                            2 => [
                                'title' => '2. PENILAIAN KEADAAN',
                                'max' => 30,
                                'items' => [
                                    '2_a' => ['label' => 'Menjelaskan mekanisme kejadian', 'options' => [0, 5, 10]],
                                    '2_b' => ['label' => 'Memperkenalkan diri dan tim', 'options' => [0, 5, 10]],
                                    '2_c' => ['label' => 'Meminta izin dan saksi', 'options' => [0, 5, 10]],
                                ]
                            ],
                            3 => [
                                'title' => '3. PENILAIAN KEAMANAN',
                                'max' => 10,
                                'items' => [
                                    '3_a' => ['label' => 'Menyimpulkan keadaan lokasi kejadian', 'options' => [0, 5, 10]],
                                ]
                            ],
                            4 => [
                                'title' => '4. PENILAIAN DINI',
                                'max' => 160,
                                'items' => [
                                    '4_a' => ['label' => 'Menyimpulkan kesan umum kasus medis', 'options' => [0, 30]],
                                    '4_b' => ['label' => 'Cek respon suara dan nyeri', 'options' => [0, 10, 20]],
                                    '4_c' => ['label' => 'Meminta bantuan Guru / UKS', 'options' => [0, 10]],
                                    '4_d' => ['label' => 'Menstabilkan korban ke posisi terlentang', 'options' => [0, 5, 10]],
                                    '4_e' => ['label' => 'Cek nafas (LDR 3-5 detik) & menyimpulkan', 'options' => [0, 15, 30]],
                                    '4_f' => ['label' => 'Cek nadi radialis (5-10 detik) & menyimpulkan', 'options' => [0, 15, 30]],
                                    '4_g' => ['label' => 'Kontrol perdarahan 5 bagian & menyimpulkan', 'options' => [0, 15, 30]],
                                ]
                            ],
                            5 => [
                                'title' => '5. PEMERIKSAAN TANDA VITAL',
                                'max' => 80,
                                'items' => [
                                    '5_a' => ['label' => 'Nadi 1 menit (diukur & diperiksa tepat)', 'options' => [5, 10, 20]],
                                    '5_b' => ['label' => 'Nafas 1 menit (diukur & diperiksa tepat)', 'options' => [5, 10, 20]],
                                    '5_c' => ['label' => 'Suhu (menyimpulkan hasil dari korban)', 'options' => [5, 10, 20]],
                                    '5_d' => ['label' => 'Kondisi, keadaan, dan warna kulit', 'options' => [5, 10, 20]],
                                ]
                            ],
                            6 => [
                                'title' => '6. PENANGANAN SAYAT (KASUS 1)',
                                'max' => 110,
                                'items' => [
                                    '6_a' => ['label' => 'Tepat menentukan posisi luka (kanan/kiri/atas/bawah)', 'options' => [0, 10]],
                                    '6_b' => ['label' => 'Bersihkan sekitar luka & antiseptik', 'options' => [10, 25, 50]],
                                    '6_c' => ['label' => 'Balut luka dengan tepat & istirahatkan', 'options' => [10, 25, 50]],
                                ]
                            ],
                            7 => [
                                'title' => '7. CEK FISIK (PLNB)',
                                'max' => 25,
                                'items' => [
                                    '7_a' => ['label' => 'Kepala', 'options' => [0, 3, 5]],
                                    '7_b' => ['label' => 'Leher', 'options' => [0, 3, 5]],
                                    '7_c' => ['label' => 'Badan termasuk perut & punggung', 'options' => [0, 3, 5]],
                                    '7_d' => ['label' => 'Alat gerak atas & GSS', 'options' => [0, 3, 5]],
                                    '7_e' => ['label' => 'Alat gerak bawah & GSS', 'options' => [0, 3, 5]],
                                ]
                            ],
                            8 => [
                                'title' => '8. RIWAYAT PENDERITA',
                                'max' => 10,
                                'items' => [
                                    '8_a' => ['label' => 'Wawancara KOMPAK', 'options' => [0, 5, 10]],
                                ]
                            ],
                            9 => [
                                'title' => '9. PELAPORAN & KERJASAMA',
                                'max' => 20,
                                'items' => [
                                    '9_a' => ['label' => 'Kartu pelaporan diisi tepat & lengkap', 'options' => [0, 5, 10]],
                                    '9_b' => ['label' => 'Kerjasama tim kompak & dinamis', 'options' => [0, 5, 10]],
                                ]
                            ],
                        ];
                    } elseif ($catLevel === 'Wira') {
                        $ppSections = [
                            1 => [
                                'title' => '1. APD (ALAT PELINDUNG DIRI)',
                                'max' => 20,
                                'items' => [
                                    '1_a' => ['label' => 'Terpasang sarung tangan lateks', 'options' => [0, 5, 10]],
                                    '1_b' => ['label' => 'Terpasang masker', 'options' => [0, 5, 10]],
                                ]
                            ],
                            2 => [
                                'title' => '2. PENILAIAN KEADAAN',
                                'max' => 30,
                                'items' => [
                                    '2_a' => ['label' => 'Menjelaskan mekanisme kejadian', 'options' => [0, 5, 10]],
                                    '2_b' => ['label' => 'Memperkenalkan diri dan tim', 'options' => [0, 5, 10]],
                                    '2_c' => ['label' => 'Meminta izin dan saksi', 'options' => [0, 5, 10]],
                                ]
                            ],
                            3 => [
                                'title' => '3. PENILAIAN KEAMANAN',
                                'max' => 10,
                                'items' => [
                                    '3_a' => ['label' => 'Menyimpulkan keadaan lokasi kejadian', 'options' => [0, 5, 10]],
                                ]
                            ],
                            4 => [
                                'title' => '4. PENILAIAN DINI',
                                'max' => 175,
                                'items' => [
                                    '4_a' => ['label' => 'Menyimpulkan kesan umum kasus medis', 'options' => [0, 30]],
                                    '4_b' => ['label' => 'Cek respon suara dan nyeri', 'options' => [0, 10, 20]],
                                    '4_c' => ['label' => 'Meminta bantuan Guru / UKS', 'options' => [0, 10]],
                                    '4_d' => ['label' => 'Menstabilkan korban ke posisi terlentang', 'options' => [0, 5, 10]],
                                    '4_e' => ['label' => 'Buka jalan nafas (ADTD)', 'options' => [0, 10, 25]],
                                    '4_f' => ['label' => 'Cek nafas (LDR 3-5 detik) & menyimpulkan', 'options' => [0, 15, 25]],
                                    '4_g' => ['label' => 'Cek nadi radialis (5-10 detik) & menyimpulkan', 'options' => [0, 15, 30]],
                                    '4_h' => ['label' => 'Kontrol perdarahan 5 bagian & menyimpulkan', 'options' => [5, 15, 25]],
                                ]
                            ],
                            5 => [
                                'title' => '5. PEMERIKSAAN TANDA VITAL',
                                'max' => 100,
                                'items' => [
                                    '5_a' => ['label' => 'Nadi 1 menit (diukur & diperiksa tepat)', 'options' => [5, 10, 20]],
                                    '5_b' => ['label' => 'Nafas 1 menit (diukur & diperiksa tepat)', 'options' => [5, 10, 20]],
                                    '5_c' => ['label' => 'Suhu (menyimpulkan hasil dari korban)', 'options' => [5, 10, 20]],
                                    '5_d' => ['label' => 'Kondisi, keadaan, dan warna kulit', 'options' => [5, 10, 20]],
                                    '5_e' => ['label' => 'Menyimpulkan adanya gangguan medis (syok)', 'options' => [5, 10, 20]],
                                ]
                            ],
                            6 => [
                                'title' => '6. PENANGANAN SYOK (KASUS 1)',
                                'max' => 160,
                                'items' => [
                                    '6_a' => ['label' => 'Cek respon dan membuka jalan nafas', 'options' => [10, 25, 40]],
                                    '6_b' => ['label' => 'Tinggikan tungkai & longgarkan pakaian', 'options' => [10, 25, 40]],
                                    '6_c' => ['label' => 'Selimuti korban seluruh bagian tubuh', 'options' => [10, 25, 40]],
                                    '6_d' => ['label' => 'Memberi oksigen', 'options' => [10, 25, 40]],
                                ]
                            ],
                            7 => [
                                'title' => '7. PENANGANAN PATAH TULANG (KASUS 2)',
                                'max' => 130,
                                'items' => [
                                    '7_a' => ['label' => 'Tepat menentukan posisi luka (kanan, kiri, atas, bawah)', 'options' => [0, 10]],
                                    '7_b' => ['label' => 'Fiksasi manual (pertahankan posisi)', 'options' => [10, 25, 40]],
                                    '7_c' => ['label' => 'Mengukur bidai & masukkan mitela sesuai ketentuan', 'options' => [10, 25, 40]],
                                    '7_d' => ['label' => 'Pengikatan pada sendi yang banyak bergerak', 'options' => [10, 25, 40]],
                                ]
                            ],
                            8 => [
                                'title' => '8. CEK FISIK (PLNB)',
                                'max' => 25,
                                'items' => [
                                    '8_a' => ['label' => 'Kepala', 'options' => [0, 3, 5]],
                                    '8_b' => ['label' => 'Leher', 'options' => [0, 3, 5]],
                                    '8_c' => ['label' => 'Badan termasuk perut & punggung', 'options' => [0, 3, 5]],
                                    '8_d' => ['label' => 'Alat gerak atas & GSS', 'options' => [0, 3, 5]],
                                    '8_e' => ['label' => 'Alat gerak bawah & GSS', 'options' => [0, 3, 5]],
                                ]
                            ],
                            9 => [
                                'title' => '9. RIWAYAT PENDERITA',
                                'max' => 10,
                                'items' => [
                                    '9_a' => ['label' => 'Wawancara KOMPAK', 'options' => [0, 5, 10]],
                                ]
                            ],
                            10 => [
                                'title' => '10. PELAPORAN & KERJASAMA',
                                'max' => 20,
                                'items' => [
                                    '10_a' => ['label' => 'Kartu pelaporan diisi tepat & lengkap', 'options' => [0, 5, 10]],
                                    '10_b' => ['label' => 'Kerjasama tim kompak & dinamis', 'options' => [0, 5, 10]],
                                ]
                            ],
                        ];
                    } else { // Madya (Standar Kategori 164)
                        $ppSections = [
                            1 => [
                                'title' => '1. APD (ALAT PELINDUNG DIRI)',
                                'max' => 20,
                                'items' => [
                                    '1_a' => ['label' => 'Terpasang sarung tangan lateks', 'options' => [0, 5, 10]],
                                    '1_b' => ['label' => 'Terpasang masker', 'options' => [0, 5, 10]],
                                ]
                            ],
                            2 => [
                                'title' => '2. PENILAIAN KEADAAN',
                                'max' => 30,
                                'items' => [
                                    '2_a' => ['label' => 'Menjelaskan mekanisme kejadian', 'options' => [0, 5, 10]],
                                    '2_b' => ['label' => 'Memperkenalkan diri dan tim', 'options' => [0, 5, 10]],
                                    '2_c' => ['label' => 'Meminta izin dan saksi', 'options' => [0, 5, 10]],
                                ]
                            ],
                            3 => [
                                'title' => '3. PENILAIAN KEAMANAN',
                                'max' => 10,
                                'items' => [
                                    '3_a' => ['label' => 'Menyimpulkan keadaan lokasi kejadian', 'options' => [0, 5, 10]],
                                ]
                            ],
                            4 => [
                                'title' => '4. PENILAIAN DINI',
                                'max' => 185,
                                'items' => [
                                    '4_a' => ['label' => 'Menyimpulkan kesan umum kasus medis', 'options' => [0, 30]],
                                    '4_b' => ['label' => 'Cek respon suara dan nyeri', 'options' => [0, 10, 20]],
                                    '4_c' => ['label' => 'Meminta bantuan Guru / UKS', 'options' => [0, 10]],
                                    '4_d' => ['label' => 'Menstabilkan korban ke posisi terlentang', 'options' => [0, 5, 10]],
                                    '4_e' => ['label' => 'Buka jalan nafas (ADTD)', 'options' => [0, 10, 25]],
                                    '4_f' => ['label' => 'Cek nafas (LDR 3-5 detik) & menyimpulkan', 'options' => [0, 15, 30]],
                                    '4_g' => ['label' => 'Cek nadi karotis (5-10 detik) & menyimpulkan', 'options' => [0, 15, 30]],
                                    '4_h' => ['label' => 'Kontrol perdarahan 5 bagian & menyimpulkan', 'options' => [0, 15, 30]],
                                ]
                            ],
                            5 => [
                                'title' => '5. PEMERIKSAAN TANDA VITAL',
                                'max' => 100,
                                'items' => [
                                    '5_a' => ['label' => 'Nadi 1 menit (diukur & diperiksa tepat)', 'options' => [5, 10, 20]],
                                    '5_b' => ['label' => 'Nafas 1 menit (diukur & diperiksa tepat)', 'options' => [5, 10, 20]],
                                    '5_c' => ['label' => 'Suhu (menyimpulkan hasil dari korban)', 'options' => [5, 10, 20]],
                                    '5_d' => ['label' => 'Kondisi, keadaan, dan warna kulit', 'options' => [5, 10, 20]],
                                    '5_e' => ['label' => 'Menyimpulkan adanya gangguan medis (pingsan)', 'options' => [5, 10, 20]],
                                ]
                            ],
                            6 => [
                                'title' => '6. PENANGANAN PINGSAN (KASUS 1)',
                                'max' => 200,
                                'items' => [
                                    '6_a' => ['label' => 'Cek respon dan membuka jalan nafas', 'options' => [10, 25, 50]],
                                    '6_b' => ['label' => 'Tinggikan tungkai & longgarkan pakaian', 'options' => [10, 25, 50]],
                                    '6_c' => ['label' => 'Selimuti korban seluruh bagian tubuh', 'options' => [10, 25, 50]],
                                    '6_d' => ['label' => 'Memberi oksigen', 'options' => [10, 25, 50]],
                                ]
                            ],
                            7 => [
                                'title' => '7. PENANGANAN ROBEK (KASUS 2)',
                                'max' => 160,
                                'items' => [
                                    '7_a' => ['label' => 'Tepat menentukan posisi luka (kanan/kiri/atas/bawah)', 'options' => [0, 10]],
                                    '7_b' => ['label' => 'Kendalikan perdarahan (TET)', 'options' => [10, 25, 50]],
                                    '7_c' => ['label' => 'Bersihkan sekitar luka & antiseptik', 'options' => [10, 25, 50]],
                                    '7_d' => ['label' => 'Balut luka dengan tepat & istirahatkan', 'options' => [10, 25, 50]],
                                ]
                            ],
                            8 => [
                                'title' => '8. CEK FISIK (PLNB)',
                                'max' => 25,
                                'items' => [
                                    '8_a' => ['label' => 'Kepala', 'options' => [0, 3, 5]],
                                    '8_b' => ['label' => 'Leher', 'options' => [0, 3, 5]],
                                    '8_c' => ['label' => 'Badan termasuk perut & punggung', 'options' => [0, 3, 5]],
                                    '8_d' => ['label' => 'Alat gerak atas & GSS', 'options' => [0, 3, 5]],
                                    '8_e' => ['label' => 'Alat gerak bawah & GSS', 'options' => [0, 3, 5]],
                                ]
                            ],
                            9 => [
                                'title' => '9. RIWAYAT PENDERITA',
                                'max' => 10,
                                'items' => [
                                    '9_a' => ['label' => 'Wawancara KOMPAK', 'options' => [0, 5, 10]],
                                ]
                            ],
                            10 => [
                                'title' => '10. PELAPORAN & KERJASAMA',
                                'max' => 20,
                                'items' => [
                                    '10_a' => ['label' => 'Kartu pelaporan diisi tepat & lengkap', 'options' => [0, 5, 10]],
                                    '10_b' => ['label' => 'Kerjasama tim kompak & dinamis', 'options' => [0, 5, 10]],
                                ]
                            ],
                        ];
                    }

                    $scenarioBannerGrad = match ($catLevel) {
                        'Mula' => 'from-emerald-950/90 to-slate-900 border-emerald-600/30',
                        'Wira' => 'from-amber-950/90 to-slate-900 border-amber-600/30',
                        default => 'from-blue-950/90 to-slate-900 border-blue-600/30',
                    };
                    $scenarioIconBox = match ($catLevel) {
                        'Mula' => 'bg-emerald-600/30 border-emerald-500/40 text-emerald-400',
                        'Wira' => 'bg-amber-500/25 border-amber-400/40 text-amber-400',
                        default => 'bg-blue-600/30 border-blue-500/40 text-blue-400',
                    };
                    $scenarioTitleText = match ($catLevel) {
                        'Mula' => 'text-emerald-300',
                        'Wira' => 'text-amber-300',
                        default => 'text-blue-300',
                    };
                    $scenarioPill = match ($catLevel) {
                        'Mula' => 'bg-emerald-600/30 border-emerald-400/40 text-white',
                        'Wira' => 'bg-amber-500/30 border-amber-400/40 text-amber-200',
                        default => 'bg-blue-600/30 border-blue-400/40 text-white',
                    };
                @endphp

                <!-- Banner Skenario Kasus Korban Lapangan -->
                <div class="bg-gradient-to-r {{ $scenarioBannerGrad }} rounded-2xl p-2.5 border flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl {{ $scenarioIconBox }} border flex items-center justify-center text-sm shrink-0">
                            <i class="fa-solid fa-kit-medical"></i>
                        </div>
                        <div>
                            <span class="text-[8.5px] font-black uppercase {{ $scenarioTitleText }} block tracking-wider">KORBAN 1 • SKENARIO KASUS LAPANGAN:</span>
                            <div class="text-xs font-black text-white flex items-center gap-1.5 mt-0.5">
                                <span class="px-2 py-0.5 rounded {{ $scenarioPill }} border text-[10px]">{{ $scenarioTitle }}</span>
                            </div>
                        </div>
                    </div>
                    <span class="text-[8px] font-bold text-slate-400 bg-slate-900 px-2 py-1 rounded-lg border border-slate-800 shrink-0">
                        Kasus 1
                    </span>
                </div>

                <!-- Stopwatch & Input Koreksi Waktu (Mobile PP) -->
                <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800 space-y-2.5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase text-slate-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-stopwatch text-sky-400"></i> TIMER TINDAKAN PRAKTIK
                        </span>
                        <span class="text-[8.5px] font-mono text-slate-400 bg-slate-950 px-2 py-0.5 rounded border border-slate-800">
                            {{ $targetTimeLabel }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between bg-black/60 rounded-xl p-2.5 border border-slate-800">
                        <div>
                            <div id="stopwatch-display" class="text-2xl font-black font-mono-num text-emerald-400 tracking-wider">
                                {{ sprintf('%02d:%02d', $recordedMinutes, $recordedSeconds) }}
                            </div>
                            <div class="text-[8px] text-slate-400 mt-0.5 font-bold">Waktu Pelaksanaan Lapangan</div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="toggleStopwatch()" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs active:scale-95 transition flex items-center gap-1 shadow">
                                <i id="icon-timer-btn" class="fa-solid fa-play text-[9px]"></i>
                                <span id="label-timer-btn">Mulai</span>
                            </button>
                            <button type="button" onclick="resetStopwatch()" class="p-1.5 px-2.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs border border-slate-700 active:scale-95 transition" title="Reset">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Koreksi Waktu Manual Fleksibel -->
                    <div class="bg-slate-950/70 rounded-xl p-2 border border-slate-800/80 flex items-center justify-between gap-2">
                        <span class="text-[9.5px] font-bold text-slate-400 shrink-0">Koreksi Manual:</span>
                        <div class="flex items-center gap-1.5">
                            <div class="flex items-center gap-1">
                                <input type="number" id="input-minutes-box" value="{{ $recordedMinutes }}" min="0" max="180" oninput="syncManualTime()" class="w-12 bg-black/60 border border-slate-700 rounded-lg py-1 text-center font-mono font-bold text-xs text-white">
                                <span class="text-[9px] text-slate-500">m</span>
                            </div>
                            <span class="text-slate-600">:</span>
                            <div class="flex items-center gap-1">
                                <input type="number" id="input-seconds-box" value="{{ $recordedSeconds }}" min="0" max="59" oninput="syncManualTime()" class="w-12 bg-black/60 border border-slate-700 rounded-lg py-1 text-center font-mono font-bold text-xs text-white">
                                <span class="text-[9px] text-slate-500">s</span>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $activeBtnClass = match ($catLevel) {
                        'Mula' => 'bg-emerald-600 text-white shadow-md shadow-emerald-500/30 border border-emerald-400',
                        'Wira' => 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/30 border border-amber-400 font-black',
                        default => 'bg-blue-600 text-white shadow-md shadow-blue-500/30 border border-blue-400',
                    };
                    $subtotalPillColor = match ($catLevel) {
                        'Mula' => 'text-emerald-400 bg-emerald-950/70 border-emerald-800/40',
                        'Wira' => 'text-amber-400 bg-amber-950/70 border-amber-800/40',
                        default => 'text-sky-400 bg-blue-950/70 border-blue-800/40',
                    };
                    $subtotalTextColor = match ($catLevel) {
                        'Mula' => 'text-emerald-400',
                        'Wira' => 'text-amber-400',
                        default => 'text-sky-400',
                    };
                @endphp

                <!-- DAFTAR BAGIAN PENILAIAN RESMI PERTOLONGAN PERTAMA (TOUCH-FRIENDLY PILLS) -->
                <div class="space-y-3">
                    @foreach ($ppSections as $secNum => $sec)
                        @php
                            $secSubtotal = intval($existingSubtotals[$secNum] ?? 0);
                        @endphp
                        <input type="hidden" name="subtotals[{{ $secNum }}]" id="subtotal-input-{{ $secNum }}" value="{{ $secSubtotal }}">
                        
                        <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800 space-y-2.5 shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-[11px] font-black text-slate-100 uppercase tracking-wide">
                                    {{ $sec['title'] }}
                                </span>
                                <span class="text-[9.5px] font-mono-num font-bold {{ $subtotalPillColor }} px-2 py-0.5 rounded-lg border">
                                    Subtotal: <span id="subtotal-display-{{ $secNum }}">{{ $secSubtotal }}</span> / {{ $sec['max'] }}
                                </span>
                            </div>

                            @foreach ($sec['items'] as $itemKey => $item)
                                @php
                                    $currVal = isset($existingRubric[$itemKey]) ? intval($existingRubric[$itemKey]) : null;
                                @endphp
                                <input type="hidden" name="rubric[{{ $itemKey }}]" id="rubric_{{ $itemKey }}" value="{{ $currVal !== null ? $currVal : $item['options'][0] }}" class="rubric-pp-item" data-section="{{ $secNum }}">
                                
                                <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80">
                                    <div class="flex items-center justify-between text-[10.5px] mb-2">
                                        <span class="text-slate-200 font-semibold leading-snug">&bull; {{ $item['label'] }}</span>
                                        <span id="display_{{ $itemKey }}" class="font-mono-num font-black text-xs {{ $subtotalTextColor }} shrink-0 ml-2">
                                            {{ $currVal !== null ? $currVal : $item['options'][0] }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-{{ count($item['options']) }} gap-1.5">
                                        @foreach ($item['options'] as $opt)
                                            @php
                                                $isSelected = ($currVal !== null) ? ($currVal == $opt) : ($loop->first);
                                            @endphp
                                            <button type="button" onclick="setPpScore('{{ $itemKey }}', {{ $opt }}, {{ $secNum }}, this)" class="btn-pp-{{ $itemKey }} py-2.5 rounded-xl font-mono text-xs font-bold transition flex items-center justify-center gap-1 active:scale-95 {{ $isSelected ? $activeBtnClass : 'bg-slate-800 text-slate-400 border border-slate-700/80' }}">
                                                {{ $opt }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

            @else
                <!-- ========================================================================= -->
                <!-- 5. FALLBACK UMUM (TOUCH STEPPER & WAKTU) -->
                <!-- ========================================================================= -->
                <div class="bg-slate-900 rounded-2xl p-3.5 border border-slate-800">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-black text-slate-200 uppercase tracking-wide">
                            Nilai Praktik (0 - 100)
                        </label>
                        <span class="text-[10px] text-slate-400 font-bold">Maks. 100</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="adjustGeneralScore(-1)" class="w-12 h-12 rounded-xl bg-slate-800 hover:bg-slate-700 font-black text-slate-200 text-2xl flex items-center justify-center active:scale-95 transition">-</button>
                        <input type="number" step="0.1" name="fallback_score" id="input-general-score" 
                            value="{{ old('final_score', $activeScore ? $activeScore->final_score : '85.0') }}" 
                            oninput="updateGeneralScore(this.value)"
                            class="flex-1 bg-black/40 border border-slate-700 rounded-xl py-2.5 px-3 text-center font-mono font-black text-2xl text-white focus:outline-none focus:border-blue-500 shadow-inner">
                        <button type="button" onclick="adjustGeneralScore(1)" class="w-12 h-12 rounded-xl bg-slate-800 hover:bg-slate-700 font-black text-slate-200 text-2xl flex items-center justify-center active:scale-95 transition">+</button>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-2xl p-3.5 border border-slate-800">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-black text-slate-200 uppercase tracking-wide">
                            Waktu Pelaksanaan / Tempuh
                        </label>
                        <span class="text-[10px] text-slate-400 font-mono font-bold">mm:ss</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 font-bold">Menit:</span>
                            <input type="number" id="input-minutes-box" value="{{ $recordedMinutes }}" min="0" max="180" oninput="document.getElementById('input-minutes').value = this.value" class="w-full bg-black/40 border border-slate-700 rounded-xl py-2 text-center font-mono font-black text-lg text-white">
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 font-bold">Detik:</span>
                            <input type="number" id="input-seconds-box" value="{{ $recordedSeconds }}" min="0" max="59" oninput="document.getElementById('input-seconds').value = this.value" class="w-full bg-black/40 border border-slate-700 rounded-xl py-2 text-center font-mono font-black text-lg text-white">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Catatan Juri Singkat -->
            <div class="bg-slate-900 rounded-xl p-2.5 border border-slate-800">
                <label class="text-[10px] font-bold text-slate-400 block mb-1">Catatan / Evaluasi Dewan Juri (Opsional):</label>
                <input type="text" name="notes" value="{{ old('notes', $activeScore?->notes) }}" placeholder="Contoh: Sangat percaya diri, gerakan tanggap & kompak..." class="w-full text-xs p-2.5 bg-black/40 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-blue-500">
            </div>

            <!-- ================= FIXED BOTTOM ACTION BAR ================= -->
            <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] p-3 bg-slate-900/95 backdrop-blur-md border-t border-slate-800 z-40 shadow-2xl flex items-center justify-between">
                <div>
                    <div id="bottom-bar-subtitle" class="text-[8.5px] text-slate-400 font-bold uppercase tracking-wider">TOTAL SKOR AKHIR</div>
                    <div class="flex items-baseline gap-1">
                        <span id="bottom-bar-score" class="text-xl font-black font-mono text-white leading-tight">0</span>
                        <span id="bottom-bar-max" class="text-[10px] text-slate-400 font-normal">/ 100</span>
                    </div>
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-xs flex items-center gap-2 shadow-lg active:scale-95 transition">
                    <span>SIMPAN & LANJUT</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </button>
            </div>
        </form>
    @else
        <div class="p-8 text-center text-slate-500 text-sm">
            Tidak ada regu yang dapat dinilai pada cabang lomba ini.
        </div>
    @endif

</div>

<!-- ================= MODAL BIDIK KAMERA SMARTPHONE (DEPAN & BELAKANG) ================= -->
<div id="camera-modal" class="fixed inset-0 bg-slate-950 z-50 flex flex-col justify-between hidden">
    <!-- Top Bar Modal Kamera -->
    <div class="p-4 pt-3 flex items-center justify-between border-b border-slate-800 bg-black/60 backdrop-blur-md text-white z-10">
        <button type="button" onclick="closeCameraModal()" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-sm text-white">
            ✕
        </button>
        <div class="text-center">
            <span class="text-[10px] uppercase tracking-wider text-rose-400 font-bold block">Kamera HP Aktif</span>
            <span class="text-xs font-black text-white truncate max-w-[200px] block">
                {{ $activeTeam ? ($activeTeam->order_number ?: $activeTeam->team_label) . ' • ' . ($activeTeam->school_name ?? $activeTeam->team_name) : 'Regu' }}
            </span>
        </div>
        <div class="w-9 h-9 flex items-center justify-center text-amber-400 text-sm">
            <i class="fa-solid fa-bolt"></i>
        </div>
    </div>

    <!-- Live Viewfinder Video Stream -->
    <div class="relative flex-1 m-3 rounded-3xl overflow-hidden border-2 border-slate-700 bg-black flex items-center justify-center">
        <video id="camera-video" autoplay playsinline muted class="w-full h-full object-cover"></video>
        <canvas id="camera-canvas" class="hidden"></canvas>

        <div id="camera-flash" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-150"></div>

        <div class="absolute top-3 left-3 flex items-center gap-1.5">
            <span id="camera-facing-badge" class="text-[9px] font-mono bg-black/70 text-emerald-400 px-2.5 py-1 rounded-full border border-emerald-400/40 font-bold">
                ● KAMERA BELAKANG
            </span>
        </div>

        <div id="camera-uploading" class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center gap-2 text-white hidden z-20">
            <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-400"></i>
            <span class="text-xs font-bold">Mengompresi & Menyimpan Foto...</span>
        </div>
    </div>

    <!-- Bottom Controls Shutter -->
    <div class="p-6 pb-8 bg-black/80 backdrop-blur-md border-t border-slate-800 flex items-center justify-around z-10">
        <button type="button" onclick="document.getElementById('fallback-camera-input').click()" class="w-12 h-12 rounded-2xl bg-slate-800 border border-slate-700 flex flex-col items-center justify-center text-[9px] text-slate-300 active:scale-95 transition">
            <i class="fa-solid fa-folder-open text-base text-blue-400"></i>
            <span>File</span>
        </button>

        <button type="button" id="btn-shutter" onclick="capturePhoto()" class="w-20 h-20 rounded-full bg-white p-1.5 shadow-2xl flex items-center justify-center active:scale-90 transition ring-4 ring-rose-500/40 cursor-pointer">
            <div class="w-full h-full rounded-full border-2 border-black flex items-center justify-center bg-gradient-to-tr from-rose-600 to-red-500">
                <i class="fa-solid fa-camera text-white text-2xl"></i>
            </div>
        </button>

        <button type="button" id="btn-flip" onclick="flipCamera()" class="w-12 h-12 rounded-2xl bg-slate-800 border border-slate-700 flex flex-col items-center justify-center text-[9px] text-slate-300 active:scale-95 transition">
            <i class="fa-solid fa-camera-rotate text-base text-emerald-400"></i>
            <span id="flip-label">Balik</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const TEAM_ID = {{ $activeTeam ? $activeTeam->id : 'null' }};
    const UPLOAD_URL = '{{ route("jury.upload-photo") }}';
    const DELETE_URL_BASE = '{{ url("/juri/foto") }}';
    const CAT_LEVEL = '{{ $catLevel }}';
    const IS_LKTR = {{ $isLKTR ? 'true' : 'false' }};
    const IS_PRS = {{ $isPRS ? 'true' : 'false' }};
    const IS_MEWARNAI = {{ $isMewarnai ? 'true' : 'false' }};
    const IS_OLIM = {{ $isOlim ? 'true' : 'false' }};
    const IS_PP = {{ $isPP ? 'true' : 'false' }};

    // ================= KALKULASI & INTERAKSI SKOR LIVE =================

    // 1. LKTR
    function setLktrKekuatan(key, val, btn) {
        document.getElementById('rubric_' + key).value = val;
        document.getElementById('display_' + key).innerText = val;
        document.querySelectorAll('.btn-opt-' + key).forEach(b => {
            b.className = 'btn-opt-' + key + ' py-1 rounded text-[8.5px] font-bold transition bg-slate-800 text-slate-400';
        });
        btn.className = 'btn-opt-' + key + ' py-1 rounded text-[8.5px] font-bold transition bg-blue-600 text-white shadow';
        recalculateLktr();
    }

    function setLktrKerapihan(key, val, btn) {
        document.getElementById('rubric_' + key).value = val;
        document.getElementById('display_' + key).innerText = val;
        document.querySelectorAll('.btn-opt-' + key).forEach(b => {
            b.className = 'btn-opt-' + key + ' py-1 rounded text-[8.5px] font-bold transition bg-slate-800 text-slate-400';
        });
        btn.className = 'btn-opt-' + key + ' py-1 rounded text-[8.5px] font-bold transition bg-blue-600 text-white shadow';
        recalculateLktr();
    }

    function calculateLktrTimeScore(seconds) {
        if (seconds <= 0) return 0;
        if (CAT_LEVEL === 'Mula') {
            if (seconds <= 180) return 300;
            if (seconds > 600) return 0;
            const step = Math.ceil((seconds - 180) / 15);
            return Math.max(300 - (step * 10), 20);
        } else {
            if (seconds <= 160) return 300;
            if (seconds > 330) return 0;
            const step = Math.ceil((seconds - 160) / 10);
            return Math.max(300 - (step * 15), 45);
        }
    }

    function recalculateLktr() {
        let subKekuatan = 0;
        document.querySelectorAll('.rubric-lktr-kekuatan').forEach(el => {
            subKekuatan += parseFloat(el.value) || 0;
        });
        document.getElementById('subtotal-kekuatan').innerText = subKekuatan + ' / 400';

        let subKerapihan = 0;
        document.querySelectorAll('.rubric-lktr-kerapihan').forEach(el => {
            subKerapihan += parseFloat(el.value) || 0;
        });
        document.getElementById('subtotal-kerapihan').innerText = subKerapihan + ' / 300';

        const mins = parseInt(document.getElementById('input-minutes').value) || 0;
        const secs = parseInt(document.getElementById('input-seconds').value) || 0;
        const totalSeconds = (mins * 60) + secs;
        const timeScore = calculateLktrTimeScore(totalSeconds);

        document.getElementById('display-time-points').innerText = '+' + timeScore;

        const technicalScore = subKekuatan + subKerapihan;
        const grandTotal = technicalScore + timeScore;

        document.getElementById('input-final-score').value = grandTotal;
        document.getElementById('bottom-bar-subtitle').innerText = `TEKNIS: ${technicalScore} + WAKTU: ${timeScore}`;
        document.getElementById('bottom-bar-score').innerText = grandTotal;
        document.getElementById('bottom-bar-max').innerText = 'POIN';
    }

    // 2. PRS
    function setPrsScore(key, val, max, btn) {
        document.getElementById('rubric_' + key).value = val;
        document.getElementById('display_' + key).innerText = val + ' / ' + max;
        document.querySelectorAll('.btn-prs-' + key).forEach(b => {
            b.className = 'btn-prs-' + key + ' py-1.5 rounded-lg text-[9px] font-bold transition bg-slate-800 text-slate-400';
        });
        btn.className = 'btn-prs-' + key + ' py-1.5 rounded-lg text-[9px] font-bold transition bg-blue-600 text-white shadow';
        recalculatePrs();
    }

    function recalculatePrs() {
        let total = 0;
        document.querySelectorAll('.rubric-prs-item').forEach(el => {
            total += parseFloat(el.value) || 0;
        });
        const maxScore = (CAT_LEVEL === 'Wira') ? 1000 : 900;
        document.getElementById('input-final-score').value = total;
        document.getElementById('bottom-bar-subtitle').innerText = 'TOTAL SKOR PRS';
        document.getElementById('bottom-bar-score').innerText = total;
        document.getElementById('bottom-bar-max').innerText = '/ ' + maxScore;
    }

    // 3. MEWARNAI
    function setMewarnaiScore(type, val) {
        document.getElementById('rubric_' + type).value = val;
        const max = type === 'c3' ? 20 : 40;
        document.getElementById('display_' + type).innerText = val + ' / ' + max;
        recalculateMewarnai();
    }

    function recalculateMewarnai() {
        let total = 0;
        document.querySelectorAll('.rubric-mewarnai-item').forEach(el => {
            total += parseFloat(el.value) || 0;
        });
        document.getElementById('input-final-score').value = total.toFixed(1);
        document.getElementById('bottom-bar-subtitle').innerText = 'TOTAL SKOR MEWARNAI';
        document.getElementById('bottom-bar-score').innerText = total.toFixed(1);
        document.getElementById('bottom-bar-max').innerText = '/ 100';
    }

    // 4. PP
    function setPpScore(key, val, secNum, btn) {
        const inputEl = document.getElementById('rubric_' + key);
        if (inputEl) inputEl.value = val;
        const displayEl = document.getElementById('display_' + key);
        if (displayEl) displayEl.innerText = val;

        const parent = btn.parentElement;
        if (parent) {
            const activeClass = (CAT_LEVEL === 'Mula')
                ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/30 border border-emerald-400'
                : ((CAT_LEVEL === 'Wira')
                    ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/30 border border-amber-400 font-black'
                    : 'bg-blue-600 text-white shadow-md shadow-blue-500/30 border border-blue-400');

            parent.querySelectorAll('.btn-pp-' + key).forEach(b => {
                b.className = 'btn-pp-' + key + ' py-2.5 rounded-xl font-mono text-xs font-bold transition flex items-center justify-center gap-1 active:scale-95 bg-slate-800 text-slate-400 border border-slate-700/80';
            });
            btn.className = 'btn-pp-' + key + ' py-2.5 rounded-xl font-mono text-xs font-bold transition flex items-center justify-center gap-1 active:scale-95 ' + activeClass;
        }

        recalculateSectionPp(secNum);
        recalculatePp();
    }

    function recalculateSectionPp(secNum) {
        let secTotal = 0;
        document.querySelectorAll(`.rubric-pp-item[data-section="${secNum}"]`).forEach(el => {
            secTotal += parseFloat(el.value) || 0;
        });
        const subDisplay = document.getElementById(`subtotal-display-${secNum}`);
        if (subDisplay) subDisplay.innerText = secTotal;
        const subInput = document.getElementById(`subtotal-input-${secNum}`);
        if (subInput) subInput.value = secTotal;
    }

    function recalculatePp() {
        let total = 0;
        document.querySelectorAll('.rubric-pp-item').forEach(el => {
            total += parseFloat(el.value) || 0;
        });
        const maxScore = (CAT_LEVEL === 'Mula') ? 465 : ((CAT_LEVEL === 'Wira') ? 680 : 760);
        document.getElementById('input-final-score').value = total;
        document.getElementById('bottom-bar-subtitle').innerText = 'TOTAL SKOR PP (' + CAT_LEVEL.toUpperCase() + ')';
        document.getElementById('bottom-bar-score').innerText = total;
        document.getElementById('bottom-bar-max').innerText = '/ ' + maxScore;
    }

    // 5. OLIMPIADE
    function adjustMobileOlim(field, delta) {
        const input = document.getElementById('mobile-olim-' + field);
        if (!input) return;
        let cur = parseInt(input.value) || 0;
        input.value = Math.max(0, Math.min(50, cur + delta));
        recalculateMobileOlim();
    }

    function recalculateMobileOlim() {
        const bInput = document.getElementById('mobile-olim-benar');
        const sInput = document.getElementById('mobile-olim-salah');
        const kDisplay = document.getElementById('mobile-olim-kosong');
        const b = parseInt(bInput ? bInput.value : 0) || 0;
        const s = parseInt(sInput ? sInput.value : 0) || 0;
        const k = Math.max(0, 50 - (b + s));
        if (kDisplay) kDisplay.innerText = k;

        const totalScore = (b - s) * 2;
        document.getElementById('input-final-score').value = totalScore;
        const sub = document.getElementById('bottom-bar-subtitle');
        if (sub) sub.innerText = `BENAR: ${b} • SALAH: ${s} • KOSONG: ${k}`;
        const scEl = document.getElementById('bottom-bar-score');
        if (scEl) scEl.innerText = totalScore;
        const maxEl = document.getElementById('bottom-bar-max');
        if (maxEl) maxEl.innerText = 'POIN';
    }

    function syncManualTime() {
        const mBox = document.getElementById('input-minutes-box');
        const sBox = document.getElementById('input-seconds-box');
        const m = mBox ? parseInt(mBox.value) || 0 : 0;
        const s = sBox ? parseInt(sBox.value) || 0 : 0;
        stopwatchSeconds = (m * 60) + s;
        updateStopwatchUI();
    }

    // 5. FALLBACK
    function adjustGeneralScore(amount) {
        const input = document.getElementById('input-general-score');
        let val = parseFloat(input.value) || 0;
        val = Math.max(0, Math.min(100, val + amount));
        input.value = val.toFixed(1);
        updateGeneralScore(val);
    }

    function updateGeneralScore(val) {
        document.getElementById('input-final-score').value = val;
        document.getElementById('bottom-bar-score').innerText = parseFloat(val || 0).toFixed(1);
    }

    // ================= STOPWATCH TIMER =================
    let timerInterval = null;
    let stopwatchSeconds = (parseInt('{{ $recordedMinutes }}') * 60) + parseInt('{{ $recordedSeconds }}') || 0;

    function formatTime(s) {
        const m = Math.floor(s / 60);
        const remS = s % 60;
        return (m < 10 ? '0' : '') + m + ':' + (remS < 10 ? '0' : '') + remS;
    }

    function updateStopwatchUI() {
        const el = document.getElementById('stopwatch-display');
        if (el) el.innerText = formatTime(stopwatchSeconds);
        const m = Math.floor(stopwatchSeconds / 60);
        const s = stopwatchSeconds % 60;
        document.getElementById('input-minutes').value = m;
        document.getElementById('input-seconds').value = s;
        const mBox = document.getElementById('input-minutes-box');
        if (mBox && document.activeElement !== mBox) mBox.value = (m < 10 ? '0' : '') + m;
        const sBox = document.getElementById('input-seconds-box');
        if (sBox && document.activeElement !== sBox) sBox.value = (s < 10 ? '0' : '') + s;
        if (IS_LKTR) recalculateLktr();
    }

    function toggleStopwatch() {
        const btnLabel = document.getElementById('label-timer-btn');
        const icon = document.getElementById('icon-timer-btn');
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
            if (btnLabel) btnLabel.innerText = 'Resume';
            if (icon) icon.className = 'fa-solid fa-play';
        } else {
            timerInterval = setInterval(() => {
                stopwatchSeconds++;
                updateStopwatchUI();
            }, 1000);
            if (btnLabel) btnLabel.innerText = 'Pause';
            if (icon) icon.className = 'fa-solid fa-pause';
        }
    }

    function resetStopwatch() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
        stopwatchSeconds = 0;
        updateStopwatchUI();
        const btnLabel = document.getElementById('label-timer-btn');
        const icon = document.getElementById('icon-timer-btn');
        if (btnLabel) btnLabel.innerText = 'Start';
        if (icon) icon.className = 'fa-solid fa-play';
    }

    // Inisialisasi perhitungan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        if (IS_LKTR) recalculateLktr();
        else if (IS_PRS) recalculatePrs();
        else if (IS_MEWARNAI) recalculateMewarnai();
        else if (IS_PP) {
            document.querySelectorAll('[id^="subtotal-input-"]').forEach(subInp => {
                const secNum = subInp.id.replace('subtotal-input-', '');
                recalculateSectionPp(secNum);
            });
            recalculatePp();
        }
        else {
            const gen = document.getElementById('input-general-score');
            if (gen) updateGeneralScore(gen.value);
        }
    });

    // ================= KAMERA SMARTPHONE (DEPAN & BELAKANG) =================
    let currentStream = null;
    let currentFacingMode = 'environment';

    async function openCameraModal() {
        const modal = document.getElementById('camera-modal');
        modal.classList.remove('hidden');
        await startCameraStream();
    }

    function closeCameraModal() {
        stopCameraStream();
        const modal = document.getElementById('camera-modal');
        modal.classList.add('hidden');
    }

    async function startCameraStream() {
        stopCameraStream();
        const video = document.getElementById('camera-video');
        const badge = document.getElementById('camera-facing-badge');

        const constraints = {
            audio: false,
            video: {
                facingMode: currentFacingMode,
                width: { ideal: 1280 },
                height: { ideal: 960 }
            }
        };

        try {
            currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = currentStream;
            badge.innerText = currentFacingMode === 'environment' ? '● KAMERA BELAKANG' : '● KAMERA DEPAN';
            badge.className = 'text-[9px] font-mono bg-black/70 ' + (currentFacingMode === 'environment' ? 'text-emerald-400 border-emerald-400/40' : 'text-sky-400 border-sky-400/40') + ' px-2.5 py-1 rounded-full border font-bold';
        } catch (err) {
            console.warn('getUserMedia error with constraint, trying fallback:', err);
            try {
                currentStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = currentStream;
                badge.innerText = '● KAMERA AKTIF';
            } catch (fallbackErr) {
                alert('Tidak dapat mengakses kamera smartphone: ' + fallbackErr.message + '\nMengalihkan ke pembuka kamera bawaan HP...');
                closeCameraModal();
                document.getElementById('fallback-camera-input').click();
            }
        }
    }

    function stopCameraStream() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
        const video = document.getElementById('camera-video');
        if (video) video.srcObject = null;
    }

    function flipCamera() {
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        document.getElementById('flip-label').innerText = currentFacingMode === 'environment' ? 'Depan' : 'Belakang';
        startCameraStream();
    }

    function capturePhoto() {
        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('camera-canvas');
        const flash = document.getElementById('camera-flash');
        const loading = document.getElementById('camera-uploading');

        if (!video.videoWidth) {
            alert('Kamera belum siap, tunggu sebentar...');
            return;
        }

        flash.classList.remove('opacity-0');
        flash.classList.add('opacity-100');
        setTimeout(() => {
            flash.classList.remove('opacity-100');
            flash.classList.add('opacity-0');
        }, 120);

        let w = video.videoWidth;
        let h = video.videoHeight;
        const maxDim = 1280;
        if (w > maxDim || h > maxDim) {
            if (w > h) {
                h = Math.round((h * maxDim) / w);
                w = maxDim;
            } else {
                w = Math.round((w * maxDim) / h);
                h = maxDim;
            }
        }

        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext('2d');
        
        if (currentFacingMode === 'user') {
            ctx.translate(w, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, w, h);

        const base64Data = canvas.toDataURL('image/jpeg', 0.82);

        loading.classList.remove('hidden');

        fetch(UPLOAD_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                team_id: TEAM_ID,
                image_base64: base64Data,
                caption: currentFacingMode === 'user' ? 'Foto Regu (Kamera Depan)' : 'Foto Karya Regu (Kamera Belakang)'
            })
        })
        .then(res => res.json())
        .then(data => {
            loading.classList.add('hidden');
            if (data.success) {
                addPhotoCardToGrid(data.photo);
                closeCameraModal();
            } else {
                alert('Gagal menyimpan foto: ' + data.message);
            }
        })
        .catch(err => {
            loading.classList.add('hidden');
            alert('Terjadi kesalahan koneksi saat upload foto: ' + err.message);
        });
    }

    function handleGalleryUpload(input) {
        if (!input.files || input.files.length === 0) return;

        const files = Array.from(input.files);
        files.forEach(file => {
            const formData = new FormData();
            formData.append('team_id', TEAM_ID);
            formData.append('photo_file', file);
            formData.append('caption', 'Dokumentasi Galeri');

            fetch(UPLOAD_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    addPhotoCardToGrid(data.photo);
                } else {
                    alert('Gagal mengunggah foto: ' + data.message);
                }
            })
            .catch(err => {
                alert('Gagal mengunggah foto dari berkas: ' + err.message);
            });
        });

        input.value = '';
    }

    function addPhotoCardToGrid(photo) {
        const grid = document.getElementById('photos-grid');
        const emptyNotice = document.getElementById('empty-photos-notice');
        if (emptyNotice) emptyNotice.remove();

        const count = grid.querySelectorAll('.group').length + 1;
        const card = document.createElement('div');
        card.id = 'photo-card-' + photo.id;
        card.className = 'relative rounded-xl overflow-hidden border border-slate-700 bg-slate-800 aspect-[4/3] group';
        card.innerHTML = `
            <img src="${photo.photo_url}" alt="${photo.caption}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-transparent flex flex-col justify-between p-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-[8px] font-mono bg-black/70 text-emerald-400 px-1 py-0.5 rounded font-bold">
                        Foto ${count}
                    </span>
                    <button type="button" onclick="deletePhoto(${photo.id})" class="w-4 h-4 bg-red-600 text-white rounded-full flex items-center justify-center text-[8px] font-bold">✕</button>
                </div>
                <div class="text-[8px] font-bold text-white truncate">${photo.caption || 'Dokumentasi'}</div>
            </div>
        `;
        grid.appendChild(card);

        const countText = document.getElementById('photo-count-text');
        if (countText) countText.innerText = count + ' Foto Terlampir';
    }

    function deletePhoto(photoId) {
        if (!confirm('Hapus foto dokumentasi ini?')) return;

        fetch(DELETE_URL_BASE + '/' + photoId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const el = document.getElementById('photo-card-' + photoId);
                if (el) el.remove();

                const grid = document.getElementById('photos-grid');
                const remaining = grid.querySelectorAll('.group').length;
                const countText = document.getElementById('photo-count-text');
                if (countText) countText.innerText = remaining + ' Foto Terlampir';

                if (remaining === 0) {
                    grid.innerHTML = `
                        <div id="empty-photos-notice" class="col-span-2 text-center py-2 bg-slate-950/60 border border-dashed border-slate-800 rounded-xl text-slate-500 text-[10px]">
                            Belum ada foto. Tekan <strong>"Buka Kamera"</strong> untuk lampiran bukti fisik.
                        </div>
                    `;
                }
            } else {
                alert('Gagal menghapus foto: ' + data.message);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (IS_OLIM) {
            recalculateMobileOlim();
        }
    });
</script>
@endpush
