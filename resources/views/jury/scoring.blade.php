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
                <div>
                    <div class="text-[9.5px] font-bold {{ $subText }} flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full {{ $activeDot }} animate-ping"></span>
                        PORTAL JURI • LIVE
                    </div>
                    <h2 class="text-xs sm:text-sm font-black text-white leading-tight truncate max-w-[210px]">
                        {{ $category->name }}
                    </h2>
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

                <!-- Bagian I: Kekuatan Tandu (5 Item x 50 = 250) -->
                <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black text-slate-200 uppercase tracking-wide">BAGIAN I: KEKUATAN TANDU</span>
                        <span id="subtotal-kekuatan" class="text-[10px] font-mono font-bold text-sky-400 bg-slate-800 px-2 py-0.5 rounded">
                            0 / 250
                        </span>
                    </div>

                    @php
                        $kekuatanItems = [
                            'kekuatan_1' => '1. Rebahan Tandu (50 Poin)',
                            'kekuatan_2' => '2. Angkat Beban Korban (50 Poin)',
                            'kekuatan_3a' => '3. Guncangan Tandu (50 Poin)',
                            'kekuatan_3b' => '4. Tarikan Tali Jaring (50 Poin)',
                            'kekuatan_4' => '5. Ikatan Palang Kokoh (50 Poin)',
                        ];
                    @endphp

                    @foreach ($kekuatanItems as $key => $label)
                        @php
                            $currVal = intval($existingRubric[$key] ?? 50);
                        @endphp
                        <input type="hidden" name="rubric[{{ $key }}]" id="rubric_{{ $key }}" value="{{ $currVal }}" class="rubric-lktr-kekuatan">
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-950/60 border border-slate-800/80 text-[10px]">
                            <span class="text-slate-200 font-medium">{{ $label }}</span>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="setLktrKekuatan('{{ $key }}', 50, this)" class="btn-opt-{{ $key }} px-2.5 py-1 rounded-lg text-[9px] font-bold transition {{ $currVal == 50 ? 'bg-blue-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                    Kuat
                                </button>
                                <button type="button" onclick="setLktrKekuatan('{{ $key }}', 0, this)" class="btn-opt-{{ $key }} px-2.5 py-1 rounded-lg text-[9px] font-bold transition {{ $currVal == 0 ? 'bg-rose-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                    Gagal
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Bagian II: Kerapihan & Ketepatan (11 Item) -->
                <div class="bg-slate-900 rounded-2xl p-3 border border-slate-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black text-slate-200 uppercase tracking-wide">BAGIAN II: KERAPIHAN & IKATAN</span>
                        <span id="subtotal-kerapihan" class="text-[10px] font-mono font-bold text-sky-400 bg-slate-800 px-2 py-0.5 rounded">
                            0 / 500
                        </span>
                    </div>

                    @php
                        $kerapihanItems = [
                            'kerapihan_1' => ['Simpul Pangkal (Awal & Akhir)', [50, 40, 30, 0], 50],
                            'kerapihan_2' => ['Palang Tandu & Kuncian', [50, 40, 30, 0], 50],
                            'kerapihan_3' => ['Jarak Ruas Anyaman', [45, 35, 25, 0], 45],
                            'kerapihan_4' => ['Simpul Jangkar Tandu', [45, 35, 25, 0], 45],
                            'kerapihan_5' => ['Kerapihan Ujung Tali', [45, 35, 25, 0], 45],
                            'kerapihan_6' => ['Ketegangan Tali Anyaman', [45, 35, 25, 0], 45],
                            'kerapihan_7' => ['Simpul Mati Pengunci', [45, 35, 25, 0], 45],
                            'kerapihan_8' => ['Kesejajaran Pegangan Bambu', [45, 35, 25, 0], 45],
                            'kerapihan_9' => ['Kerapihan Lipatan Selimut', [45, 35, 25, 0], 45],
                            'kerapihan_10' => ['Kesesuaian Ukuran Standar', [45, 35, 25, 0], 45],
                            'kerapihan_11' => ['Kekompakan & Ketertiban', [40, 30, 20, 0], 40],
                        ];
                    @endphp

                    @foreach ($kerapihanItems as $key => [$label, $options, $def])
                        @php
                            $currVal = intval($existingRubric[$key] ?? $def);
                        @endphp
                        <input type="hidden" name="rubric[{{ $key }}]" id="rubric_{{ $key }}" value="{{ $currVal }}" class="rubric-lktr-kerapihan">
                        <div class="p-2 rounded-xl bg-slate-950/60 border border-slate-800/80">
                            <div class="flex items-center justify-between text-[10px] mb-1.5">
                                <span class="text-slate-200 font-medium">{{ $loop->iteration }}. {{ $label }}</span>
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

            @elseif ($isPP)
                <!-- ========================================================================= -->
                <!-- 4. RUBRIK PERTOLONGAN PERTAMA (PP) -->
                <!-- ========================================================================= -->
                
                <!-- Stopwatch Praktik PP -->
                <div class="bg-slate-900 rounded-2xl p-2.5 border border-slate-800">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-1 text-slate-300 text-[10px] font-black uppercase">
                            <i class="fa-solid fa-stopwatch text-sky-400"></i> TIMER TINDAKAN PRAKTIK
                        </div>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-400">
                            Target: {{ $catLevel === 'Mula' ? '8 Menit' : ($catLevel === 'Wira' ? '12 Menit' : '10 Menit') }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between bg-black/40 rounded-xl p-2 border border-slate-800">
                        <div>
                            <div id="stopwatch-display" class="text-2xl font-black font-mono text-white">
                                {{ sprintf('%02d:%02d', $recordedMinutes, $recordedSeconds) }}
                            </div>
                            <div class="text-[8px] text-slate-400 mt-0.5 font-bold">Durasi Pelaksanaan Tindakan</div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="toggleStopwatch()" class="p-2 rounded-lg bg-blue-600 text-white text-xs active:scale-95 transition">
                                <i id="icon-timer-btn" class="fa-solid fa-play"></i>
                            </button>
                            <button type="button" onclick="resetStopwatch()" class="p-2 rounded-lg bg-slate-800 text-slate-300 text-xs active:scale-95 transition">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                @php
                    if ($catLevel === 'Mula') {
                        $ppItems = [
                            'pp_1' => ['1. Keamanan Diri & APD Dasar', 'Sarung tangan lateks dan masker terpasang sebelum menangani korban.', [15, 8, 0], 15],
                            'pp_2' => ['2. Cek Respon & Kesadaran', 'Tepuk pundak korban, panggil dengan lantang dan periksa pernapasan.', [15, 8, 0], 15],
                            'pp_3' => ['3. Hubungi Bantuan Guru / UKS', 'Minta tolong teman atau orang dewasa untuk memanggil guru/pihak medis.', [10, 5, 0], 10],
                            'pp_4' => ['4. Penanganan Luka Ringan / Lecet', 'Bersihkan luka, beri antiseptik, tutup kassa steril & balut mitella.', [30, 20, 0], 30],
                            'pp_5' => ['5. Penenangan & Posisi Nyaman', 'Berikan rasa tenang pada korban dan posisikan tubuh senyaman mungkin.', [15, 8, 0], 15],
                            'pp_6' => ['6. Pelaporan Hasil Tindakan', 'Laporkan kondisi korban dan penanganan yang telah diberikan.', [15, 8, 0], 15],
                        ];
                    } else {
                        $ppItems = [
                            'pp_1' => ['1. Keamanan & APD Lengkap', 'Aman diri (sarung tangan lateks, masker), aman lokasi dan aman korban.', [100, 60, 0], 100],
                            'pp_2' => ['2. Penilaian Dini (ASNT, CAB)', 'Kesan umum trauma/medis, respon ASNT, sirkulasi, nafas & jalan nafas.', [120, 70, 0], 120],
                            'pp_3' => ['3. Pemeriksaan Fisik Terarah', 'Pemeriksaan kepala hingga kaki, mencari tanda PLNB secara sistematis.', [120, 70, 0], 120],
                            'pp_4' => ['4. Tanda Vital & Riwayat KOMPAK', 'Cek frekuensi nadi, pernapasan, suhu/kulit, dan riwayat KOMPAK detail.', [140, 80, 0], 140],
                            'pp_5' => ['5. Penanganan Fraktur / Balut Bidai', 'Fiksasi 2 sendi, cek GSS sebelum & sesudah pembidaian, balut rapi.', [250, 200, 0], 250],
                            'pp_6' => ['6. Pencegahan Syok & Evakuasi', 'Selimuti korban, posisi pemulihan/tandu darurat LSB secara aman.', [100, 60, 0], 100],
                            'pp_7' => ['7. Pelaporan Tindakan ke Posko', 'Serah terima laporan medis: kondisi awal, tindakan dan kondisi akhir.', [70, 40, 0], 70],
                        ];
                    }
                @endphp

                <div class="space-y-2">
                    @foreach ($ppItems as $key => [$label, $desc, $opts, $def])
                        @php
                            $currVal = intval($existingRubric[$key] ?? $def);
                        @endphp
                        <input type="hidden" name="rubric[{{ $key }}]" id="rubric_{{ $key }}" value="{{ $currVal }}" class="rubric-pp-item">
                        <div class="bg-slate-900 rounded-xl p-2.5 border border-slate-800">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10.5px] font-bold text-slate-200">{{ $label }}</span>
                                <span id="display_{{ $key }}" class="font-mono font-bold text-xs text-sky-400">{{ $currVal }}</span>
                            </div>
                            <p class="text-[8px] text-slate-400 mb-1.5 leading-relaxed">{{ $desc }}</p>
                            <div class="grid grid-cols-3 gap-1">
                                <button type="button" onclick="setPpScore('{{ $key }}', {{ $opts[0] }}, this)" class="btn-pp-{{ $key }} py-1 rounded text-[8.5px] font-bold transition {{ $currVal == $opts[0] ? 'bg-blue-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                    Sempurna ({{ $opts[0] }})
                                </button>
                                <button type="button" onclick="setPpScore('{{ $key }}', {{ $opts[1] }}, this)" class="btn-pp-{{ $key }} py-1 rounded text-[8.5px] font-bold transition {{ $currVal == $opts[1] ? 'bg-blue-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                    Cukup ({{ $opts[1] }})
                                </button>
                                <button type="button" onclick="setPpScore('{{ $key }}', {{ $opts[2] }}, this)" class="btn-pp-{{ $key }} py-1 rounded text-[8.5px] font-bold transition {{ $currVal == $opts[2] ? 'bg-blue-600 text-white shadow' : 'bg-slate-800 text-slate-400' }}">
                                    Kurang ({{ $opts[2] }})
                                </button>
                            </div>
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
    const IS_PP = {{ $isPP ? 'true' : 'false' }};

    // ================= KALKULASI & INTERAKSI SKOR LIVE =================

    // 1. LKTR
    function setLktrKekuatan(key, val, btn) {
        document.getElementById('rubric_' + key).value = val;
        document.querySelectorAll('.btn-opt-' + key).forEach(b => {
            b.className = 'btn-opt-' + key + ' px-2.5 py-1 rounded-lg text-[9px] font-bold transition bg-slate-800 text-slate-400';
        });
        btn.className = 'btn-opt-' + key + ' px-2.5 py-1 rounded-lg text-[9px] font-bold transition ' + (val > 0 ? 'bg-blue-600 text-white shadow' : 'bg-rose-600 text-white shadow');
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
        document.getElementById('subtotal-kekuatan').innerText = subKekuatan + ' / 250';

        let subKerapihan = 0;
        document.querySelectorAll('.rubric-lktr-kerapihan').forEach(el => {
            subKerapihan += parseFloat(el.value) || 0;
        });
        document.getElementById('subtotal-kerapihan').innerText = subKerapihan + ' / 500';

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
    function setPpScore(key, val, btn) {
        document.getElementById('rubric_' + key).value = val;
        document.getElementById('display_' + key).innerText = val;
        document.querySelectorAll('.btn-pp-' + key).forEach(b => {
            b.className = 'btn-pp-' + key + ' py-1 rounded text-[8.5px] font-bold transition bg-slate-800 text-slate-400';
        });
        btn.className = 'btn-pp-' + key + ' py-1 rounded text-[8.5px] font-bold transition bg-blue-600 text-white shadow';
        recalculatePp();
    }

    function recalculatePp() {
        let total = 0;
        document.querySelectorAll('.rubric-pp-item').forEach(el => {
            total += parseFloat(el.value) || 0;
        });
        const maxScore = (CAT_LEVEL === 'Mula') ? 100 : 900;
        document.getElementById('input-final-score').value = total;
        document.getElementById('bottom-bar-subtitle').innerText = 'TOTAL SKOR PP';
        document.getElementById('bottom-bar-score').innerText = total;
        document.getElementById('bottom-bar-max').innerText = '/ ' + maxScore;
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
        document.getElementById('input-minutes').value = Math.floor(stopwatchSeconds / 60);
        document.getElementById('input-seconds').value = stopwatchSeconds % 60;
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
        else if (IS_PP) recalculatePp();
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
</script>
@endpush
