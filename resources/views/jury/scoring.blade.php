@extends('jury.layout')

@section('title', 'Form Penilaian: ' . $category->name)

@section('content')
<div class="flex-1 flex flex-col bg-slate-50 min-h-screen pb-24">

    <!-- Top Navigation Bar -->
    <div class="bg-white border-b border-slate-200 p-3 pt-3 flex items-center justify-between sticky top-0 z-30 shadow-xs">
        <a href="{{ route('jury.dashboard') }}" class="text-slate-600 hover:text-slate-900 font-bold text-xs flex items-center gap-1.5 px-2 py-1 rounded-lg hover:bg-slate-100 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <div class="text-center">
            <span class="text-[9px] font-bold uppercase text-slate-400 block leading-tight">Mata Lomba</span>
            <span class="text-xs font-black text-slate-900 truncate max-w-[200px] block">
                {{ $category->name }} ({{ $category->level }})
            </span>
        </div>
        <a href="{{ route('jury.dashboard') }}" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
            <i class="fa-solid fa-house"></i>
        </a>
    </div>

    @if (session('success'))
        <div class="m-3 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600">✕</button>
        </div>
    @endif

    @if (session('error'))
        <div class="m-3 p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-800 text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600">✕</button>
        </div>
    @endif

    <!-- Carousel Horizontal: Daftar Nomor Urut Peserta (Matching Mockup Layar 2) -->
    <div class="bg-slate-100 p-3 border-b border-slate-200">
        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex justify-between">
            <span>PILIH REGU TAMPIL:</span>
            <span class="text-blue-600 font-bold">
                {{ $teams->count() }} Regu Terdaftar
            </span>
        </div>
        
        <div class="flex items-center gap-2 overflow-x-auto scroll-hidden pb-1">
            @forelse ($teams as $t)
                @php
                    $isActive = $activeTeam && $activeTeam->id === $t->id;
                    $hasScore = $t->scores->isNotEmpty();
                    $scoreVal = $hasScore ? number_format($t->scores->first()->final_score, 1) : null;
                    $badgeNo = $t->order_number ?: ($t->team_label ?: $loop->iteration);
                @endphp
                <a href="{{ route('jury.scoring', ['category' => $category->id, 'team_id' => $t->id]) }}"
                    class="shrink-0 px-3 py-1.5 rounded-2xl flex flex-col items-center transition active:scale-95
                    {{ $isActive ? 'bg-blue-600 text-white font-mono-num font-black shadow-md ring-2 ring-blue-400 scale-105' : ($hasScore ? 'bg-emerald-600 text-white font-mono-num font-bold opacity-80' : 'bg-white text-slate-700 border border-slate-300 font-mono-num font-bold') }}">
                    <span class="{{ $isActive ? 'text-xs' : 'text-[11px]' }}">{{ $badgeNo }}</span>
                    <span class="text-[8px] {{ $isActive ? 'font-bold text-blue-100' : ($hasScore ? 'text-emerald-100' : 'text-slate-400') }}">
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
                <div class="text-xs text-slate-500 py-1 italic">Belum ada regu terverifikasi di cabang lomba ini.</div>
            @endforelse
        </div>
    </div>

    @if ($activeTeam)
        <!-- Target Participant Info Box -->
        <div class="p-3.5 bg-white border-b border-slate-200 flex items-center justify-between shadow-2xs">
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono-num font-black text-sm px-2.5 py-0.5 rounded-lg bg-amber-100 text-amber-900 border border-amber-300">
                        {{ $activeTeam->order_number ?: ($activeTeam->team_label ?: 'Regu') }}
                    </span>
                    <span class="text-xs font-black text-slate-900 uppercase tracking-tight">
                        {{ $activeTeam->school_name ?? $activeTeam->registration?->school_name ?? $activeTeam->team_name }}
                    </span>
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Regu: <strong>{{ $activeTeam->team_name ?: ('Regu ' . $activeTeam->team_label) }}</strong>
                    @if (is_array($activeTeam->members_list) && count($activeTeam->members_list) > 0)
                        &bull; {{ implode(', ', array_slice(array_column($activeTeam->members_list, 'name'), 0, 2)) }}
                    @endif
                </div>
            </div>
            <span class="text-[9px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200 shrink-0">
                <i class="fa-solid fa-clock"></i> Di Lapangan
            </span>
        </div>

        <!-- ================= BAGIAN KHUSUS DOKUMENTASI FOTO (MENCOLOK DI ATAS INPUT NILAI) ================= -->
        <div class="p-4 bg-gradient-to-b from-rose-50/90 to-red-50/50 border-b-2 border-red-200">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-xs font-black text-red-950 uppercase tracking-wide flex items-center gap-1.5">
                    <i class="fa-solid fa-camera text-red-600 text-base"></i> Dokumentasi Foto Regu:
                </span>
                <span id="photo-count-badge" class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full border border-emerald-300 flex items-center gap-1">
                    <i class="fa-solid fa-check text-[9px]"></i> <span id="photo-count-text">{{ $activePhotos->count() }} Foto Terlampir</span>
                </span>
            </div>

            <!-- 2 TOMBOL BESAR: BUKA KAMERA HP & PILIH GALERI -->
            <div class="grid grid-cols-2 gap-2.5 mb-3">
                <!-- TOMBOL 1: BUKA KAMERA HANDPHONE (MENDUKUNG KAMERA DEPAN & BELAKANG) -->
                <button type="button" onclick="openCameraModal()" class="py-3.5 px-3 bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-black text-xs rounded-2xl shadow-lg shadow-red-900/30 flex items-center justify-center gap-2 border-2 border-red-500 active:scale-95 transition">
                    <i class="fa-solid fa-camera text-base"></i>
                    <span class="leading-tight">Buka Kamera HP</span>
                </button>

                <!-- TOMBOL 2: PILIH DARI GALERI HANDPHONE -->
                <button type="button" onclick="document.getElementById('gallery-file-input').click()" class="py-3.5 px-3 bg-white hover:bg-slate-50 text-slate-800 font-extrabold text-xs rounded-2xl border-2 border-slate-300 shadow-sm flex items-center justify-center gap-2 active:scale-95 transition">
                    <i class="fa-solid fa-images text-blue-600 text-base"></i>
                    <span class="leading-tight">Pilih Galeri</span>
                </button>
            </div>

            <!-- Input Berkas Tersembunyi untuk Galeri -->
            <input type="file" id="gallery-file-input" accept="image/*" multiple class="hidden" onchange="handleGalleryUpload(this)">

            <!-- Input Berkas Tersembunyi untuk Fallback Kamera Native OS -->
            <input type="file" id="fallback-camera-input" accept="image/*" capture="environment" class="hidden" onchange="handleGalleryUpload(this)">

            <!-- PRATINJAU GRID FOTO HASIL JEPRETAN / UNGGAHAN -->
            <div id="photos-grid" class="grid grid-cols-2 gap-2.5">
                @forelse ($activePhotos as $idx => $photo)
                    <div id="photo-card-{{ $photo->id }}" class="relative rounded-2xl overflow-hidden border-2 border-slate-300 bg-slate-800 aspect-[4/3] shadow-md group">
                        <img src="{{ $photo->photo_url }}" alt="{{ $photo->caption }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-transparent flex flex-col justify-between p-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-mono bg-black/70 text-emerald-400 px-1.5 py-0.5 rounded font-bold">
                                    Foto {{ $idx + 1 }}
                                </span>
                                <button type="button" onclick="deletePhoto({{ $photo->id }})" class="w-5 h-5 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center text-[10px] shadow font-bold cursor-pointer">
                                    ✕
                                </button>
                            </div>
                            <div class="text-[9px] font-bold text-white leading-tight">
                                <div class="truncate">{{ $photo->caption ?: 'Dokumentasi Lapangan' }}</div>
                                <span class="text-[8px] text-slate-300 font-normal">{{ $photo->created_at->format('H:i') }} WIB &bull; {{ $photo->file_size }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div id="empty-photos-notice" class="col-span-2 text-center py-4 bg-white/70 border border-dashed border-red-300 rounded-2xl p-3 text-slate-500 text-xs">
                        <i class="fa-solid fa-camera-rotate text-red-400 text-lg mb-1 block"></i>
                        <span>Belum ada foto dokumentasi. Tekan <strong>"Buka Kamera HP"</strong> untuk memotret karya regu ini.</span>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-2 text-[10px] text-slate-500 flex items-center gap-1.5">
                <i class="fa-solid fa-bolt text-amber-500"></i>
                <span>Otomatis kompresi foto (&plusmn;300 KB) & langsung tersimpan ke server.</span>
            </div>
        </div>

        <!-- ================= FORM INPUT NILAI & WAKTU TEMPUH ================= -->
        <form id="scoring-form" action="{{ route('jury.save-score', ['category' => $category->id, 'team' => $activeTeam->id]) }}" method="POST" class="p-4 space-y-4 flex-1 bg-white">
            @csrf

            <!-- Input 1: Nilai Praktik (Touch-Friendly Stepper) -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 shadow-2xs">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-black text-slate-800 uppercase tracking-wide">
                        1. Nilai Praktik (0 - 100) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[10px] text-slate-400 font-bold">Maks. 100</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="adjustScore(-1)" class="w-11 h-11 rounded-xl bg-slate-200 hover:bg-slate-300 font-black text-slate-700 text-2xl flex items-center justify-center active:scale-95 transition select-none">-</button>
                    <input type="number" step="0.1" name="final_score" id="input-final-score" 
                        value="{{ old('final_score', $activeScore ? $activeScore->final_score : '85.0') }}" 
                        required min="0" max="100"
                        class="flex-1 bg-white border border-slate-300 rounded-xl py-2 px-3 text-center font-mono-num font-black text-xl text-slate-900 focus:outline-none focus:border-red-500 shadow-inner">
                    <button type="button" onclick="adjustScore(1)" class="w-11 h-11 rounded-xl bg-slate-200 hover:bg-slate-300 font-black text-slate-700 text-2xl flex items-center justify-center active:scale-95 transition select-none">+</button>
                </div>
            </div>

            <!-- Input 2: Waktu Tempuh Tandu (Menit & Detik) -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 shadow-2xs">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-black text-slate-800 uppercase tracking-wide">
                        2. Waktu Tempuh / Durasi
                    </label>
                    <span class="text-[10px] text-slate-400 font-mono font-bold">mm:ss</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500 font-bold">Menit:</span>
                        <input type="number" name="minutes" id="input-minutes" 
                            value="{{ old('minutes', $recordedMinutes) }}" min="0" max="180" placeholder="00"
                            class="w-full bg-white border border-slate-300 rounded-xl py-2 text-center font-mono-num font-black text-lg text-slate-900 focus:outline-none focus:border-red-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500 font-bold">Detik:</span>
                        <input type="number" name="seconds" id="input-seconds" 
                            value="{{ old('seconds', $recordedSeconds) }}" min="0" max="59" placeholder="00"
                            class="w-full bg-white border border-slate-300 rounded-xl py-2 text-center font-mono-num font-black text-lg text-slate-900 focus:outline-none focus:border-red-500">
                    </div>
                </div>
            </div>

            <!-- Catatan Juri Singkat -->
            <div>
                <label class="text-xs font-bold text-slate-600 block mb-1">Catatan Juri (Opsional):</label>
                <input type="text" name="notes" value="{{ old('notes', $activeScore?->notes) }}" placeholder="Contoh: Ikatan jangkar rapi, tandu kokoh..." class="w-full text-xs p-3 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:border-blue-500">
            </div>

            <!-- Fixed Bottom Action Bar -->
            <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] p-4 bg-white/95 backdrop-blur-md border-t border-slate-200 z-30 shadow-2xl">
                <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-black text-sm py-3.5 rounded-2xl shadow-xl shadow-emerald-950/20 transition flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                    <i class="fa-solid fa-floppy-disk text-base"></i> Simpan Nilai & Lanjut Regu Berikutnya &rarr;
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
        <!-- Video Element -->
        <video id="camera-video" autoplay playsinline muted class="w-full h-full object-cover"></video>
        
        <!-- Canvas Hidden for Snapshotting -->
        <canvas id="camera-canvas" class="hidden"></canvas>

        <!-- Rule of Thirds Grid Overlay -->
        <div class="absolute inset-0 grid grid-cols-3 grid-rows-3 pointer-events-none opacity-25">
            <div class="border-r border-b border-white"></div>
            <div class="border-r border-b border-white"></div>
            <div class="border-b border-white"></div>
            <div class="border-r border-b border-white"></div>
            <div class="border-r border-b border-white"></div>
            <div class="border-b border-white"></div>
            <div class="border-r border-white"></div>
            <div class="border-r border-white"></div>
            <div></div>
        </div>

        <!-- Autofocus Brackets -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-24 border-2 border-amber-400/90 rounded-2xl pointer-events-none flex items-center justify-center">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
        </div>

        <!-- Top Status Badges inside Viewfinder -->
        <div class="absolute top-3 left-3 right-3 flex items-center justify-between pointer-events-none z-10">
            <span id="camera-facing-badge" class="text-[9px] font-mono bg-black/70 text-emerald-400 px-2.5 py-1 rounded-full border border-emerald-400/40 font-bold">
                ● KAMERA BELAKANG
            </span>
            <span class="text-[9px] font-mono bg-black/70 text-white px-2.5 py-1 rounded-full">
                1280px &bull; Auto HD
            </span>
        </div>

        <!-- Shutter Flash Effect Overlay -->
        <div id="camera-flash" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-150"></div>

        <!-- Loading Upload Overlay -->
        <div id="camera-uploading" class="absolute inset-0 bg-black/80 flex flex-col items-center justify-center text-white hidden z-20">
            <i class="fa-solid fa-spinner fa-spin text-3xl text-rose-500 mb-2"></i>
            <span class="text-xs font-bold">Menyimpan & Mengompresi Foto...</span>
        </div>
    </div>

    <!-- Camera Controls Bar -->
    <div class="p-4 pb-6 bg-black/90 border-t border-slate-900 flex items-center justify-around z-10">
        
        <!-- Tombol Buka Galeri -->
        <button type="button" onclick="document.getElementById('gallery-file-input').click(); closeCameraModal();" class="w-12 h-12 rounded-2xl bg-slate-800 border border-slate-700 flex flex-col items-center justify-center text-[9px] text-slate-300 active:scale-95 transition">
            <i class="fa-solid fa-images text-base text-sky-400"></i>
            <span>Galeri</span>
        </button>

        <!-- TOMBOL RANA BULAT BESAR (SHUTTER BUTTON UNTUK JEPRET FOTO) -->
        <button type="button" id="btn-shutter" onclick="capturePhoto()" class="w-20 h-20 rounded-full bg-white p-1.5 shadow-2xl flex items-center justify-center active:scale-90 transition ring-4 ring-rose-500/40 cursor-pointer">
            <div class="w-full h-full rounded-full border-2 border-black flex items-center justify-center bg-gradient-to-tr from-rose-600 to-red-500">
                <i class="fa-solid fa-camera text-white text-2xl"></i>
            </div>
        </button>

        <!-- TOMBOL GANTI KAMERA (FLIP DEPAN / BELAKANG) -->
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

    let currentStream = null;
    let currentFacingMode = 'environment'; // 'environment' = kamera belakang, 'user' = kamera depan

    function adjustScore(amount) {
        const input = document.getElementById('input-final-score');
        let val = parseFloat(input.value) || 0;
        val = Math.max(0, Math.min(100, val + amount));
        input.value = val.toFixed(1);
    }

    // ================= KAMERA SMARTPHONE (DEPAN & BELAKANG) =================
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
            // Fallback jika exact facingMode gagal di device tertentu
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

    // Jepret Foto dari Live Viewfinder Video
    function capturePhoto() {
        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('camera-canvas');
        const flash = document.getElementById('camera-flash');
        const loading = document.getElementById('camera-uploading');

        if (!video.videoWidth) {
            alert('Kamera belum siap, tunggu sebentar...');
            return;
        }

        // Efek Flash Shutter
        flash.classList.remove('opacity-0');
        flash.classList.add('opacity-100');
        setTimeout(() => {
            flash.classList.remove('opacity-100');
            flash.classList.add('opacity-0');
        }, 120);

        // Render video frame to canvas dengan resize maksimal 1280px
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
        
        // Jika kamera depan, cerminkan horizontal agar natural
        if (currentFacingMode === 'user') {
            ctx.translate(w, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, w, h);

        // Kompresi ke format JPEG kualitas 0.8
        const base64Data = canvas.toDataURL('image/jpeg', 0.82);

        loading.classList.remove('hidden');

        // Kirim via AJAX
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
                // Tutup kamera setelah jepret agar juri melihat hasilnya
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

    // Handle Upload Foto dari Galeri atau Fallback Kamera Native
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
        card.className = 'relative rounded-2xl overflow-hidden border-2 border-slate-300 bg-slate-800 aspect-[4/3] shadow-md group animate-fade-in';
        card.innerHTML = `
            <img src="${photo.photo_url}" alt="${photo.caption}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-transparent flex flex-col justify-between p-2">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-mono bg-black/70 text-emerald-400 px-1.5 py-0.5 rounded font-bold">
                        Foto ${count}
                    </span>
                    <button type="button" onclick="deletePhoto(${photo.id})" class="w-5 h-5 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center text-[10px] shadow font-bold cursor-pointer">
                        ✕
                    </button>
                </div>
                <div class="text-[9px] font-bold text-white leading-tight">
                    <div class="truncate">${photo.caption || 'Dokumentasi'}</div>
                    <span class="text-[8px] text-slate-300 font-normal">${photo.created_at} WIB &bull; ${photo.file_size}</span>
                </div>
            </div>
        `;
        grid.appendChild(card);

        // Update badge text
        document.getElementById('photo-count-text').innerText = count + ' Foto Terlampir';
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
                document.getElementById('photo-count-text').innerText = remaining + ' Foto Terlampir';

                if (remaining === 0) {
                    grid.innerHTML = `
                        <div id="empty-photos-notice" class="col-span-2 text-center py-4 bg-white/70 border border-dashed border-red-300 rounded-2xl p-3 text-slate-500 text-xs">
                            <i class="fa-solid fa-camera-rotate text-red-400 text-lg mb-1 block"></i>
                            <span>Belum ada foto dokumentasi. Tekan <strong>"Buka Kamera HP"</strong> untuk memotret karya regu ini.</span>
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
