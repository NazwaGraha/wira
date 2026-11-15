@php
    $teamId = $team?->id ?? null;
    $teamName = $team?->team_name ?? 'Regu Belum Dipilih';
    $teamLabel = $team?->team_label ?? '';
    $photosList = $photos ?? ($team ? $team->photos()->latest()->get() : collect());
    
    // Resolve theme styling based on level or passed theme
    $resolvedTheme = $theme ?? strtolower($category->level ?? 'madya');
    if ($resolvedTheme === 'wira') {
        $themeBorder = 'border-amber-400';
        $themeFocus = 'focus:ring-amber-400';
        $themeBadge = 'bg-amber-100 text-amber-950 border-amber-300';
        $themeBtnPrimary = 'bg-amber-500 hover:bg-amber-600 text-slate-950 shadow-amber-500/20';
        $themeIcon = 'text-amber-500';
    } elseif ($resolvedTheme === 'mula') {
        $themeBorder = 'border-emerald-400';
        $themeFocus = 'focus:ring-emerald-400';
        $themeBadge = 'bg-emerald-100 text-emerald-950 border-emerald-300';
        $themeBtnPrimary = 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/20';
        $themeIcon = 'text-emerald-600';
    } else { // madya (default: biru)
        $themeBorder = 'border-blue-400';
        $themeFocus = 'focus:ring-blue-400';
        $themeBadge = 'bg-blue-100 text-blue-950 border-blue-300';
        $themeBtnPrimary = 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-600/20';
        $themeIcon = 'text-blue-600';
    }
@endphp

<!-- ================= BAGIAN DOKUMENTASI FOTO & BERKAS GRID NILAI ================= -->
<div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 mt-6 transition-all" id="admin-document-photos-container">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-2xl bg-slate-100 flex items-center justify-center {{ $themeIcon }} text-base shadow-xs">
                    <i class="fa-solid fa-camera-retro"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        Dokumentasi Foto &amp; Berkas Grid Nilai
                        <span id="admin-photo-count-badge" class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border {{ $themeBadge }}">
                            <span id="admin-photo-count-text">{{ $photosList->count() }} Berkas Terlampir</span>
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Ambil foto langsung melalui kamera atau upload berkas scan lembar nilai fisik / karya untuk <strong class="text-slate-800" id="admin-current-team-display">{{ $teamName }}</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Kamera & Upload -->
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            <button type="button" id="admin-btn-open-camera" onclick="openAdminCameraModal()" class="px-4 py-2.5 rounded-xl {{ $themeBtnPrimary }} font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95 {{ !$teamId ? 'opacity-50 pointer-events-none' : '' }}">
                <i class="fa-solid fa-camera"></i>
                <span>Foto Dokumen Grid</span>
            </button>
            <button type="button" id="admin-btn-upload-file" onclick="document.getElementById('admin-doc-file-input').click()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95 {{ !$teamId ? 'opacity-50 pointer-events-none' : '' }}">
                <i class="fa-solid fa-cloud-arrow-up text-slate-300"></i>
                <span>Upload Dokumen Grid</span>
            </button>
        </div>
    </div>

    @if(isset($teams) && $teams->count() > 1)
        <!-- Pilihan Regu Cepat jika dalam mode tabel batch (Mewarnai, Olimpiade, LCT) -->
        <div class="mt-4 p-3 rounded-2xl bg-slate-50 border border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <label for="admin-doc-team-selector" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                <i class="fa-solid fa-users text-blue-600"></i> Pilih Regu Peserta:
            </label>
            <select id="admin-doc-team-selector" onchange="switchAdminPhotoTeam(this.value, this.options[this.selectedIndex].text)" class="bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 max-w-md">
                @foreach($teams as $t)
                    @php
                        $tLabel = $t->registration->school_name ?? $t->team_name;
                        $tOrder = $t->order_number ? sprintf('%02d. ', $t->order_number) : '';
                    @endphp
                    <option value="{{ $t->id }}" {{ ($teamId == $t->id) ? 'selected' : '' }}>
                        {{ $tOrder }}{{ $tLabel }} ({{ $t->team_name }})
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Hidden Inputs File Uploader -->
    @if($teamId)
        <input type="file" id="admin-doc-file-input" accept="image/jpeg,image/png,image/webp,application/pdf" multiple class="hidden" onchange="handleAdminFileUpload(this, '{{ $teamId }}')">
        <input type="file" id="admin-doc-fallback-camera" accept="image/*" capture="environment" class="hidden" onchange="handleAdminFileUpload(this, '{{ $teamId }}')">
    @endif

    <!-- Upload Progress Indicator Bar (Disembunyikan secara default) -->
    <div id="admin-upload-progress" class="hidden mt-4 p-3 rounded-2xl bg-blue-50 border border-blue-200">
        <div class="flex items-center justify-between text-xs font-bold text-blue-900 mb-1.5">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-circle-notch fa-spin text-blue-600"></i>
                <span id="admin-upload-status-text">Mengunggah dokumen...</span>
            </span>
            <span id="admin-upload-percent-text">0%</span>
        </div>
        <div class="w-full bg-blue-200 rounded-full h-2 overflow-hidden">
            <div id="admin-upload-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    <!-- Gallery / Grid Dokumen yang Telah Di-upload (View Dokumen Grid) -->
    <div class="mt-4">
        <div id="admin-photos-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @forelse ($photosList as $idx => $photo)
                @php
                    $isPdf = str_ends_with(strtolower($photo->photo_path), '.pdf');
                @endphp
                <div id="admin-photo-card-{{ $photo->id }}" class="admin-photo-item relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 aspect-[4/3] group shadow-2xs hover:shadow-md transition">
                    @if($isPdf)
                        <div class="w-full h-full flex flex-col items-center justify-center bg-slate-900 p-2 text-center">
                            <i class="fa-solid fa-file-pdf text-red-400 text-3xl mb-1"></i>
                            <span class="text-[9px] font-bold text-slate-300 line-clamp-1">{{ $photo->caption ?: 'Dokumen PDF' }}</span>
                        </div>
                    @else
                        <img src="{{ $photo->photo_url }}" alt="{{ $photo->caption }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                    @endif

                    <!-- Overlay Hover Aksi & Detail -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/70 opacity-90 sm:opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-mono font-bold bg-black/60 text-slate-200 px-1.5 py-0.5 rounded border border-white/10">
                                #{{ $idx + 1 }}
                            </span>
                            <div class="flex items-center gap-1">
                                <a href="{{ $photo->photo_url }}" download class="w-6 h-6 rounded-lg bg-black/60 hover:bg-black text-slate-200 hover:text-white flex items-center justify-center text-[10px] transition" title="Unduh File">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <button type="button" onclick="deleteAdminPhoto({{ $photo->id }})" class="w-6 h-6 rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white flex items-center justify-center text-[10px] transition" title="Hapus Dokumen">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Klik untuk View Dokumen Lightbox -->
                        <div class="cursor-pointer" onclick="openAdminLightbox('{{ $photo->photo_url }}', '{{ addslashes($photo->caption ?: 'Dokumentasi Grid Nilai') }}', '{{ $photo->file_size ?: '-' }}', {{ $idx }}, {{ $isPdf ? 'true' : 'false' }})">
                            <div class="text-[10px] font-black text-white truncate drop-shadow">
                                {{ $photo->caption ?: 'Dokumentasi Nilai' }}
                            </div>
                            <div class="flex items-center justify-between text-[8.5px] text-slate-300 mt-0.5">
                                <span>{{ $photo->created_at?->format('H:i') ?? '-' }}</span>
                                <span class="font-mono bg-white/20 px-1 rounded">{{ $photo->file_size ?: 'Foto' }}</span>
                            </div>
                            <div class="mt-1 flex items-center gap-1 text-[8.5px] font-bold text-sky-300">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                                <span>Lihat Penuh</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div id="admin-photos-empty-state" class="col-span-full py-8 px-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/70 text-center">
                    <div class="w-12 h-12 mx-auto mb-2 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 text-xl shadow-2xs">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div class="text-xs font-bold text-slate-700">Belum ada foto / dokumen grid yang terlampir</div>
                    <p class="text-[11px] text-slate-400 mt-0.5 max-w-md mx-auto">
                        Gunakan tombol <strong>Foto Dokumen Grid</strong> untuk memotret fisik lembar nilai via webcam/kamera HP atau <strong>Upload Dokumen Grid</strong> untuk memilih berkas dari komputer/galeri.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ================= MODAL AMBIL FOTO KAMERA (DESKTOP & MOBILE WEBRTC) ================= -->
<div id="admin-camera-modal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-4 transition-all">
    <div class="relative w-full max-w-xl bg-slate-950 rounded-3xl overflow-hidden border border-slate-800 shadow-2xl flex flex-col max-h-[95vh]">
        
        <!-- Header Modal Kamera -->
        <div class="p-3.5 sm:p-4 bg-slate-900 border-b border-slate-800 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center text-sm border border-blue-500/30">
                    <i class="fa-solid fa-camera"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <span>Ambil Foto Dokumen Grid</span>
                        <span id="admin-camera-facing-badge" class="text-[9px] font-mono bg-emerald-950 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full font-bold">
                            ● MEMUAT KAMERA...
                        </span>
                    </h4>
                    <p class="text-[10px] text-slate-400" id="admin-camera-team-name">{{ $teamName }}</p>
                </div>
            </div>
            <button type="button" onclick="closeAdminCameraModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Area Viewfinder Kamera Video -->
        <div class="relative bg-black flex items-center justify-center overflow-hidden aspect-[4/3] w-full shrink-0">
            <video id="admin-camera-video" autoplay playsinline webkit-playsinline muted class="w-full h-full object-cover"></video>
            
            <!-- Garis Bidik Viewfinder Dokumen -->
            <div class="absolute inset-4 sm:inset-6 border-2 border-dashed border-white/40 rounded-2xl pointer-events-none flex flex-col justify-between p-3">
                <div class="flex justify-between text-[9px] font-mono text-white/70">
                    <span>◤ GRID NILAI</span>
                    <span>DOKUMEN ◥</span>
                </div>
                <div class="flex justify-between text-[9px] font-mono text-white/70">
                    <span>◣ REGU PESERTA</span>
                    <span>VERIFIKASI ◢</span>
                </div>
            </div>

            <!-- Efek Flash Shutter -->
            <div id="admin-camera-flash" class="absolute inset-0 bg-white opacity-0 transition-opacity duration-150 pointer-events-none"></div>

            <!-- Loading Spinner saat Upload -->
            <div id="admin-camera-uploading" class="absolute inset-0 bg-black/80 backdrop-blur-xs flex flex-col items-center justify-center gap-2 text-white hidden">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-400"></i>
                <span class="text-xs font-bold">Menyimpan dan mengompres foto...</span>
            </div>
        </div>

        <!-- Canvas Tersembunyi untuk Snapshot Frame -->
        <canvas id="admin-camera-canvas" class="hidden"></canvas>

        <!-- Form Keterangan / Caption & Tombol Aksi Kontrol Kamera -->
        <div class="p-3.5 sm:p-4 bg-slate-900 border-t border-slate-800 space-y-3 shrink-0">
            <div>
                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">
                    Label / Keterangan Dokumen (Opsional):
                </label>
                <div class="flex items-center gap-2">
                    <input type="text" id="admin-camera-caption-input" placeholder="Contoh: Lembar Nilai Fisik, Foto Balutan, Karya..." value="Lembar Nilai Fisik" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    <button type="button" onclick="adminFlipCamera()" class="shrink-0 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 flex items-center gap-1.5 transition cursor-pointer active:scale-95" title="Putar Kamera (Depan / Belakang / Eksternal)">
                        <i class="fa-solid fa-camera-rotate text-blue-400"></i>
                        <span id="admin-flip-label" class="hidden sm:inline">Ganti Kamera</span>
                    </button>
                </div>
            </div>

            <!-- Tombol Shutter Jepret Foto -->
            <div class="flex items-center justify-between gap-3 pt-1">
                <button type="button" onclick="closeAdminCameraModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>

                <button type="button" onclick="captureAdminPhoto()" class="flex-1 py-3 px-4 rounded-2xl bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-500 hover:to-sky-400 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-circle-dot text-base text-rose-300 animate-pulse"></i>
                    <span>Jepret &amp; Simpan Foto</span>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- ================= MODAL LIGHTBOX VIEWER PENUH (DOKUMEN ZOOM & DOWNLOAD) ================= -->
<div id="admin-lightbox-modal" class="fixed inset-0 z-50 bg-black/95 backdrop-blur-md hidden flex flex-col justify-between p-3 sm:p-5 transition-all">
    
    <!-- Topbar Lightbox -->
    <div class="flex items-center justify-between text-white pb-3 border-b border-white/10 shrink-0">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-sm">
                <i class="fa-solid fa-image"></i>
            </span>
            <div>
                <h4 id="admin-lightbox-title" class="text-xs sm:text-sm font-black uppercase tracking-wider text-white">Pratinjau Dokumen Grid</h4>
                <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                    <span id="admin-lightbox-meta">-</span>
                    <span>&bull;</span>
                    <span id="admin-lightbox-counter">Foto 1 dari 1</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="adminLightboxZoom(0.2)" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs transition" title="Perbesar">
                <i class="fa-solid fa-magnifying-glass-plus"></i>
            </button>
            <button type="button" onclick="adminLightboxZoom(-0.2)" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs transition" title="Perkecil">
                <i class="fa-solid fa-magnifying-glass-minus"></i>
            </button>
            <button type="button" onclick="adminLightboxRotate()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs transition" title="Putar 90 Derajat">
                <i class="fa-solid fa-rotate-right"></i>
            </button>
            <a id="admin-lightbox-download" href="#" download class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 transition shadow-sm" title="Unduh File">
                <i class="fa-solid fa-download"></i>
                <span class="hidden sm:inline">Unduh</span>
            </a>
            <button type="button" onclick="closeAdminLightbox()" class="w-8 h-8 rounded-xl bg-rose-600 hover:bg-rose-500 text-white flex items-center justify-center text-xs transition cursor-pointer" title="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Area Gambar Penuh / PDF Viewer -->
    <div class="relative flex-1 flex items-center justify-center overflow-hidden my-3">
        <!-- Tombol Prev -->
        <button type="button" onclick="adminLightboxPrev()" id="admin-lightbox-btn-prev" class="absolute left-2 sm:left-4 z-10 w-10 h-10 rounded-2xl bg-black/60 hover:bg-black text-white flex items-center justify-center text-sm border border-white/20 transition cursor-pointer">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <!-- Media Gambar Utama -->
        <div id="admin-lightbox-media-container" class="max-w-full max-h-full flex items-center justify-center overflow-auto transition-transform duration-200">
            <img id="admin-lightbox-img" src="" alt="Pratinjau Dokumen" class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-2xl transition-transform duration-200">
            <iframe id="admin-lightbox-iframe" src="" class="w-full h-[75vh] rounded-xl bg-white hidden border-0"></iframe>
        </div>

        <!-- Tombol Next -->
        <button type="button" onclick="adminLightboxNext()" id="admin-lightbox-btn-next" class="absolute right-2 sm:right-4 z-10 w-10 h-10 rounded-2xl bg-black/60 hover:bg-black text-white flex items-center justify-center text-sm border border-white/20 transition cursor-pointer">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

    <!-- Footer Lightbox -->
    <div class="text-center text-[11px] text-slate-400 py-1 border-t border-white/10 shrink-0">
        Gunakan tombol panah keyboard (&larr; / &rarr;) untuk navigasi, dan tombol <kbd class="px-1.5 py-0.5 rounded bg-white/10 font-mono text-[9px] text-white">Esc</kbd> untuk menutup pratinjau.
    </div>

</div>

<!-- ================= JAVASCRIPT LOGIC DOKUMENTASI GRID (WEBRTC & CROSS-BROWSER) ================= -->
<script>
    // Konfigurasi URL API & CSRF
    const ADMIN_PHOTO_UPLOAD_URL = "{{ route('admin.competition-scores.upload-photo') }}";
    const ADMIN_PHOTO_DELETE_BASE = "{{ url('admin/competition-scores/photo') }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";

    let activeAdminTeamId = "{{ $teamId }}";
    let activeAdminTeamName = "{{ addslashes($teamName) }}";
    
    // Status Kamera WebRTC
    let adminStream = null;
    let adminFacingMode = 'environment';
    let adminVideoDevices = [];
    let adminCurrentDeviceIndex = 0;

    // Status Lightbox
    let adminGalleryItems = [];
    let adminCurrentLightboxIndex = 0;
    let adminZoomScale = 1.0;
    let adminRotation = 0;

    // Inisialisasi daftar item galeri saat ini
    function refreshAdminGalleryData() {
        adminGalleryItems = [];
        const cards = document.querySelectorAll('.admin-photo-item');
        cards.forEach((c, idx) => {
            const img = c.querySelector('img');
            const iframe = c.querySelector('iframe');
            const isPdf = c.querySelector('.fa-file-pdf') !== null;
            const caption = c.querySelector('.drop-shadow')?.innerText?.trim() || 'Dokumentasi Grid Nilai';
            const meta = c.querySelector('.font-mono')?.innerText?.trim() || '';
            const downloadUrl = c.querySelector('a[download]')?.getAttribute('href') || (img ? img.src : '');
            
            adminGalleryItems.push({
                url: downloadUrl,
                caption: caption,
                meta: meta,
                isPdf: isPdf,
                index: idx
            });
        });
    }

    // ================= MODAL KAMERA INTERAKTIF (CROSS-BROWSER) =================
    async function openAdminCameraModal(teamId = null, teamName = null) {
        if (teamId) activeAdminTeamId = teamId;
        if (teamName) {
            activeAdminTeamName = teamName;
            const lbl = document.getElementById('admin-camera-team-name');
            if (lbl) lbl.innerText = teamName;
        }

        const modal = document.getElementById('admin-camera-modal');
        if (!modal) return;
        modal.classList.remove('hidden');

        // Validasi dukungan WebRTC pada browser (Chrome, Safari, Firefox, Edge, Opera, Samsung Internet)
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Browser atau protokol akses Anda (non-HTTPS) belum mengizinkan WebRTC kamera langsung.\nSilakan gunakan tombol "Upload Dokumen Grid" atau kamera bawaan.');
            closeAdminCameraModal();
            const fallbackInput = document.getElementById('admin-doc-fallback-camera');
            if (fallbackInput) fallbackInput.click();
            return;
        }

        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            adminVideoDevices = devices.filter(d => d.kind === 'videoinput');
        } catch (e) {
            console.log('Enumerate devices notice:', e);
        }

        await startAdminCameraStream();
    }

    function closeAdminCameraModal() {
        stopAdminCameraStream();
        const modal = document.getElementById('admin-camera-modal');
        if (modal) modal.classList.add('hidden');
    }

    async function startAdminCameraStream(preferredDeviceId = null) {
        stopAdminCameraStream();
        const video = document.getElementById('admin-camera-video');
        const badge = document.getElementById('admin-camera-facing-badge');
        if (!video) return;

        // Atribut ramah Safari iOS & WebKit
        video.muted = true;
        video.playsInline = true;
        video.setAttribute('playsinline', 'true');
        video.setAttribute('webkit-playsinline', 'true');
        video.setAttribute('muted', 'true');

        let constraints;
        if (preferredDeviceId) {
            constraints = {
                audio: false,
                video: {
                    deviceId: { exact: preferredDeviceId },
                    width: { ideal: 1920, max: 2560 },
                    height: { ideal: 1080, max: 1440 }
                }
            };
        } else {
            constraints = {
                audio: false,
                video: {
                    facingMode: { ideal: adminFacingMode },
                    width: { ideal: 1920, max: 2560 },
                    height: { ideal: 1080, max: 1440 }
                }
            };
        }

        try {
            adminStream = await navigator.mediaDevices.getUserMedia(constraints);
        } catch (err) {
            console.warn('getUserMedia ideal gagal, mencoba fallback facingMode:', err);
            // Fallback 1: Jika kamera belakang tidak ada pada laptop desktop (hanya webcam depan)
            try {
                const oppositeFacing = adminFacingMode === 'environment' ? 'user' : 'environment';
                adminStream = await navigator.mediaDevices.getUserMedia({
                    audio: false,
                    video: { facingMode: { ideal: oppositeFacing } }
                });
                adminFacingMode = oppositeFacing;
            } catch (fallback1) {
                console.warn('Fallback facingMode sebaliknya gagal, mencoba kamera default apapun:', fallback1);
                // Fallback 2: Minta kamera umum tanpa batasan facingMode
                try {
                    adminStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                } catch (fallback2) {
                    console.error('Seluruh upaya kamera gagal:', fallback2);
                    alert('Tidak dapat mengaktifkan kamera (' + (fallback2.name || fallback2.message || 'Izin ditolak') + ').\nPastikan izin akses kamera telah diaktifkan pada browser.');
                    closeAdminCameraModal();
                    const fallbackInput = document.getElementById('admin-doc-fallback-camera');
                    if (fallbackInput) fallbackInput.click();
                    return;
                }
            }
        }

        if (adminStream && video) {
            video.srcObject = adminStream;
            video.muted = true;
            try {
                await video.play();
            } catch (playErr) {
                console.log('Video play catch (Safari autoplay policy):', playErr);
            }

            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                adminVideoDevices = devices.filter(d => d.kind === 'videoinput');
            } catch (e) {}

            const activeTrack = adminStream.getVideoTracks()[0];
            const trackSettings = activeTrack ? (typeof activeTrack.getSettings === 'function' ? activeTrack.getSettings() : {}) : {};
            const trackLabel = (activeTrack && activeTrack.label) ? activeTrack.label.toLowerCase() : '';

            const isBack = adminFacingMode === 'environment' || trackLabel.includes('back') || trackLabel.includes('belakang') || trackSettings.facingMode === 'environment';

            if (badge) {
                badge.innerText = isBack ? '● KAMERA BELAKANG' : '● KAMERA DEPAN';
                badge.className = 'text-[9px] font-mono bg-black/70 ' + (isBack ? 'text-emerald-400 border-emerald-400/40' : 'text-sky-400 border-sky-400/40') + ' px-2.5 py-1 rounded-full border font-bold';
            }

            const flipLabel = document.getElementById('admin-flip-label');
            if (flipLabel) {
                flipLabel.innerText = isBack ? 'Kamera Depan' : 'Kamera Belakang';
            }
        }
    }

    function stopAdminCameraStream() {
        if (adminStream) {
            adminStream.getTracks().forEach(track => track.stop());
            adminStream = null;
        }
        const video = document.getElementById('admin-camera-video');
        if (video) video.srcObject = null;
    }

    function adminFlipCamera() {
        if (adminVideoDevices && adminVideoDevices.length > 1) {
            adminCurrentDeviceIndex = (adminCurrentDeviceIndex + 1) % adminVideoDevices.length;
            const nextDev = adminVideoDevices[adminCurrentDeviceIndex];
            if (nextDev && nextDev.deviceId) {
                const lbl = (nextDev.label || '').toLowerCase();
                if (lbl.includes('front') || lbl.includes('user') || lbl.includes('facetime') || lbl.includes('selfie') || lbl.includes('depan')) {
                    adminFacingMode = 'user';
                } else {
                    adminFacingMode = 'environment';
                }
                startAdminCameraStream(nextDev.deviceId);
                return;
            }
        }

        adminFacingMode = (adminFacingMode === 'environment') ? 'user' : 'environment';
        startAdminCameraStream();
    }

    function captureAdminPhoto() {
        const video = document.getElementById('admin-camera-video');
        const canvas = document.getElementById('admin-camera-canvas');
        const flash = document.getElementById('admin-camera-flash');
        const loading = document.getElementById('admin-camera-uploading');
        const captionInput = document.getElementById('admin-camera-caption-input');

        if (!video || !video.videoWidth) {
            alert('Kamera sedang memuat gambar, tunggu sesaat lalu tekan tombol jepret kembali.');
            return;
        }

        // Efek Flash
        flash.classList.remove('opacity-0');
        flash.classList.add('opacity-100');
        setTimeout(() => {
            flash.classList.remove('opacity-100');
            flash.classList.add('opacity-0');
        }, 120);

        let w = video.videoWidth;
        let h = video.videoHeight;
        const maxDim = 1920;
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

        if (adminFacingMode === 'user') {
            ctx.translate(w, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, w, h);

        const base64Data = canvas.toDataURL('image/jpeg', 0.88);
        const caption = captionInput?.value?.trim() || 'Dokumentasi Lembar Nilai';

        loading.classList.remove('hidden');

        fetch(ADMIN_PHOTO_UPLOAD_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                team_id: activeAdminTeamId,
                image_base64: base64Data,
                caption: caption
            })
        })
        .then(res => res.json())
        .then(data => {
            loading.classList.add('hidden');
            if (data.success) {
                appendAdminPhotoCard(data.photo);
                closeAdminCameraModal();
            } else {
                alert('Gagal menyimpan foto: ' + (data.message || 'Terjadi kesalahan sistem.'));
            }
        })
        .catch(err => {
            loading.classList.add('hidden');
            console.error('Upload camera error:', err);
            alert('Terjadi kesalahan saat mengunggah foto ke server.');
        });
    }

    // ================= UPLOAD BERKAS FILE (GALERI / DOKUMEN) =================
    function handleAdminFileUpload(input, teamId) {
        if (!input.files || input.files.length === 0) return;
        const targetTeamId = teamId || activeAdminTeamId;

        const progressContainer = document.getElementById('admin-upload-progress');
        const progressBar = document.getElementById('admin-upload-bar');
        const percentText = document.getElementById('admin-upload-percent-text');
        const statusText = document.getElementById('admin-upload-status-text');

        if (progressContainer) progressContainer.classList.remove('hidden');

        const files = Array.from(input.files);
        let uploadedCount = 0;

        function uploadNextFile(index) {
            if (index >= files.length) {
                if (progressContainer) {
                    statusText.innerText = 'Seluruh berkas berhasil diunggah!';
                    setTimeout(() => progressContainer.classList.add('hidden'), 1500);
                }
                input.value = '';
                return;
            }

            const file = files[index];
            const formData = new FormData();
            formData.append('team_id', targetTeamId);
            formData.append('photo_file', file);
            formData.append('caption', file.name.replace(/\.[^/.]+$/, ""));

            const percent = Math.round(((index) / files.length) * 100);
            if (progressBar) progressBar.style.width = percent + '%';
            if (percentText) percentText.innerText = percent + '%';
            if (statusText) statusText.innerText = `Mengunggah berkas (${index + 1}/${files.length}): ${file.name}...`;

            fetch(ADMIN_PHOTO_UPLOAD_URL, {
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
                    appendAdminPhotoCard(data.photo);
                } else {
                    console.error('File upload error:', data.message);
                }
                uploadNextFile(index + 1);
            })
            .catch(err => {
                console.error('Upload exception:', err);
                uploadNextFile(index + 1);
            });
        }

        uploadNextFile(0);
    }

    // ================= APPEND PHOTO CARD SECARA DINAMIS =================
    function appendAdminPhotoCard(photo) {
        const grid = document.getElementById('admin-photos-grid');
        const emptyState = document.getElementById('admin-photos-empty-state');
        if (emptyState) emptyState.remove();

        const countBadge = document.getElementById('admin-photo-count-text');
        const existingCards = document.querySelectorAll('.admin-photo-item');
        const newCount = existingCards.length + 1;
        if (countBadge) countBadge.innerText = `${newCount} Berkas Terlampir`;

        const card = document.createElement('div');
        card.id = `admin-photo-card-${photo.id}`;
        card.className = 'admin-photo-item relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 aspect-[4/3] group shadow-2xs hover:shadow-md transition';

        const isPdf = photo.is_pdf || photo.photo_url.toLowerCase().endsWith('.pdf');

        card.innerHTML = `
            ${isPdf ? `
                <div class="w-full h-full flex flex-col items-center justify-center bg-slate-900 p-2 text-center">
                    <i class="fa-solid fa-file-pdf text-red-400 text-3xl mb-1"></i>
                    <span class="text-[9px] font-bold text-slate-300 line-clamp-1">${photo.caption || 'Dokumen PDF'}</span>
                </div>
            ` : `
                <img src="${photo.photo_url}" alt="${photo.caption || 'Dokumentasi'}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
            `}
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/70 opacity-90 sm:opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-mono font-bold bg-black/60 text-slate-200 px-1.5 py-0.5 rounded border border-white/10">
                        #${newCount}
                    </span>
                    <div class="flex items-center gap-1">
                        <a href="${photo.photo_url}" download class="w-6 h-6 rounded-lg bg-black/60 hover:bg-black text-slate-200 hover:text-white flex items-center justify-center text-[10px] transition" title="Unduh File">
                            <i class="fa-solid fa-download"></i>
                        </a>
                        <button type="button" onclick="deleteAdminPhoto(${photo.id})" class="w-6 h-6 rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white flex items-center justify-center text-[10px] transition" title="Hapus Dokumen">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>

                <div class="cursor-pointer" onclick="openAdminLightbox('${photo.photo_url}', '${(photo.caption || 'Dokumentasi Grid Nilai').replace(/'/g, "\\'")}', '${photo.file_size || '-'}', ${newCount - 1}, ${isPdf ? 'true' : 'false'})">
                    <div class="text-[10px] font-black text-white truncate drop-shadow">
                        ${photo.caption || 'Dokumentasi Nilai'}
                    </div>
                    <div class="flex items-center justify-between text-[8.5px] text-slate-300 mt-0.5">
                        <span>${photo.created_at || 'Baru Saja'}</span>
                        <span class="font-mono bg-white/20 px-1 rounded">${photo.file_size || 'Foto'}</span>
                    </div>
                    <div class="mt-1 flex items-center gap-1 text-[8.5px] font-bold text-sky-300">
                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                        <span>Lihat Penuh</span>
                    </div>
                </div>
            </div>
        `;

        grid.prepend(card);
        refreshAdminGalleryData();
    }

    // ================= HAPUS FOTO DOKUMEN =================
    function deleteAdminPhoto(photoId) {
        if (!confirm('Apakah Anda yakin ingin menghapus dokumen foto grid ini?')) return;

        fetch(`${ADMIN_PHOTO_DELETE_BASE}/${photoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById(`admin-photo-card-${photoId}`);
                if (card) {
                    card.classList.add('scale-75', 'opacity-0');
                    setTimeout(() => {
                        card.remove();
                        const remaining = document.querySelectorAll('.admin-photo-item');
                        const countBadge = document.getElementById('admin-photo-count-text');
                        if (countBadge) countBadge.innerText = `${remaining.length} Berkas Terlampir`;

                        if (remaining.length === 0) {
                            const grid = document.getElementById('admin-photos-grid');
                            grid.innerHTML = `
                                <div id="admin-photos-empty-state" class="col-span-full py-8 px-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/70 text-center">
                                    <div class="w-12 h-12 mx-auto mb-2 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 text-xl shadow-2xs">
                                        <i class="fa-solid fa-camera"></i>
                                    </div>
                                    <div class="text-xs font-bold text-slate-700">Belum ada foto / dokumen grid yang terlampir</div>
                                    <p class="text-[11px] text-slate-400 mt-0.5 max-w-md mx-auto">
                                        Gunakan tombol <strong>Foto Dokumen Grid</strong> untuk memotret fisik lembar nilai via webcam/kamera HP atau <strong>Upload Dokumen Grid</strong> untuk memilih berkas dari komputer/galeri.
                                    </p>
                                </div>
                            `;
                        }
                        refreshAdminGalleryData();
                    }, 250);
                }
            } else {
                alert('Gagal menghapus: ' + (data.message || 'Terjadi kesalahan sistem.'));
            }
        })
        .catch(err => {
            console.error('Delete photo error:', err);
            alert('Terjadi kesalahan saat menghapus foto.');
        });
    }

    // ================= LIGHTBOX VIEWER PENUH =================
    function openAdminLightbox(url, caption, meta, index, isPdf = false) {
        refreshAdminGalleryData();
        adminCurrentLightboxIndex = index || 0;
        adminZoomScale = 1.0;
        adminRotation = 0;

        const modal = document.getElementById('admin-lightbox-modal');
        const img = document.getElementById('admin-lightbox-img');
        const iframe = document.getElementById('admin-lightbox-iframe');
        const title = document.getElementById('admin-lightbox-title');
        const metaEl = document.getElementById('admin-lightbox-meta');
        const counter = document.getElementById('admin-lightbox-counter');
        const downloadBtn = document.getElementById('admin-lightbox-download');

        if (!modal) return;
        modal.classList.remove('hidden');

        renderLightboxCurrentItem();
    }

    function renderLightboxCurrentItem() {
        if (!adminGalleryItems || adminGalleryItems.length === 0) return;
        if (adminCurrentLightboxIndex < 0) adminCurrentLightboxIndex = 0;
        if (adminCurrentLightboxIndex >= adminGalleryItems.length) adminCurrentLightboxIndex = adminGalleryItems.length - 1;

        const it = adminGalleryItems[adminCurrentLightboxIndex];
        const img = document.getElementById('admin-lightbox-img');
        const iframe = document.getElementById('admin-lightbox-iframe');
        const title = document.getElementById('admin-lightbox-title');
        const metaEl = document.getElementById('admin-lightbox-meta');
        const counter = document.getElementById('admin-lightbox-counter');
        const downloadBtn = document.getElementById('admin-lightbox-download');
        const mediaContainer = document.getElementById('admin-lightbox-media-container');

        if (title) title.innerText = it.caption || 'Dokumen Grid Nilai';
        if (metaEl) metaEl.innerText = it.meta || '';
        if (counter) counter.innerText = `Dokumen ${adminCurrentLightboxIndex + 1} dari ${adminGalleryItems.length}`;
        if (downloadBtn) {
            downloadBtn.href = it.url;
            downloadBtn.download = it.caption || 'dokumen_grid';
        }

        adminZoomScale = 1.0;
        adminRotation = 0;
        applyLightboxTransform();

        if (it.isPdf) {
            if (img) img.classList.add('hidden');
            if (iframe) {
                iframe.classList.remove('hidden');
                iframe.src = it.url;
            }
        } else {
            if (iframe) {
                iframe.classList.add('hidden');
                iframe.src = '';
            }
            if (img) {
                img.classList.remove('hidden');
                img.src = it.url;
            }
        }
    }

    function closeAdminLightbox() {
        const modal = document.getElementById('admin-lightbox-modal');
        if (modal) modal.classList.add('hidden');
        const iframe = document.getElementById('admin-lightbox-iframe');
        if (iframe) iframe.src = '';
    }

    function adminLightboxPrev() {
        if (adminCurrentLightboxIndex > 0) {
            adminCurrentLightboxIndex--;
            renderLightboxCurrentItem();
        }
    }

    function adminLightboxNext() {
        if (adminCurrentLightboxIndex < adminGalleryItems.length - 1) {
            adminCurrentLightboxIndex++;
            renderLightboxCurrentItem();
        }
    }

    function adminLightboxZoom(step) {
        adminZoomScale = Math.max(0.5, Math.min(3.0, adminZoomScale + step));
        applyLightboxTransform();
    }

    function adminLightboxRotate() {
        adminRotation = (adminRotation + 90) % 360;
        applyLightboxTransform();
    }

    function applyLightboxTransform() {
        const img = document.getElementById('admin-lightbox-img');
        if (img) {
            img.style.transform = `scale(${adminZoomScale}) rotate(${adminRotation}deg)`;
        }
    }

    // ================= DUKUNGAN BATCH / MULTI-REGU =================
    function switchAdminPhotoTeam(teamId, teamName) {
        if (!teamId) return;
        activeAdminTeamId = teamId;
        activeAdminTeamName = teamName || 'Regu ' + teamId;

        const teamDisplay = document.getElementById('admin-current-team-display');
        if (teamDisplay) teamDisplay.innerText = activeAdminTeamName;

        const cameraTeam = document.getElementById('admin-camera-team-name');
        if (cameraTeam) cameraTeam.innerText = activeAdminTeamName;

        const btnCamera = document.getElementById('admin-btn-open-camera');
        const btnUpload = document.getElementById('admin-btn-upload-file');
        if (btnCamera) btnCamera.classList.remove('opacity-50', 'pointer-events-none');
        if (btnUpload) btnUpload.classList.remove('opacity-50', 'pointer-events-none');

        const grid = document.getElementById('admin-photos-grid');
        if (grid) {
            grid.innerHTML = `
                <div class="col-span-full py-8 text-center text-slate-400">
                    <i class="fa-solid fa-circle-notch fa-spin text-2xl text-blue-500 mb-2"></i>
                    <div class="text-xs font-bold">Memuat dokumen regu...</div>
                </div>
            `;
        }

        fetch(`{{ url('admin/competition-scores/team-photos') }}/${teamId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderPhotosFromList(data.photos);
                }
            })
            .catch(err => {
                console.error('Error fetching team photos:', err);
            });
    }

    function focusAdminPhotoTeam(teamId, teamName) {
        const selector = document.getElementById('admin-doc-team-selector');
        if (selector) {
            selector.value = teamId;
        }
        switchAdminPhotoTeam(teamId, teamName);
        const container = document.getElementById('admin-document-photos-container');
        if (container) {
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function renderPhotosFromList(photos) {
        const grid = document.getElementById('admin-photos-grid');
        if (!grid) return;
        grid.innerHTML = '';

        const countBadge = document.getElementById('admin-photo-count-text');
        if (countBadge) countBadge.innerText = `${photos.length} Berkas Terlampir`;

        if (!photos || photos.length === 0) {
            grid.innerHTML = `
                <div id="admin-photos-empty-state" class="col-span-full py-8 px-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/70 text-center">
                    <div class="w-12 h-12 mx-auto mb-2 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 text-xl shadow-2xs">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div class="text-xs font-bold text-slate-700">Belum ada foto / dokumen grid yang terlampir</div>
                    <p class="text-[11px] text-slate-400 mt-0.5 max-w-md mx-auto">
                        Gunakan tombol <strong>Foto Dokumen Grid</strong> untuk memotret fisik lembar nilai via webcam/kamera HP atau <strong>Upload Dokumen Grid</strong> untuk memilih berkas dari komputer/galeri.
                    </p>
                </div>
            `;
            refreshAdminGalleryData();
            return;
        }

        photos.forEach(photo => {
            appendAdminPhotoCard(photo);
        });
        refreshAdminGalleryData();
    }

    // Keyboard navigation & listener
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('admin-lightbox-modal');
        if (lightbox && !lightbox.classList.contains('hidden')) {
            if (e.key === 'Escape') closeAdminLightbox();
            if (e.key === 'ArrowLeft') adminLightboxPrev();
            if (e.key === 'ArrowRight') adminLightboxNext();
        }

        const cameraModal = document.getElementById('admin-camera-modal');
        if (cameraModal && !cameraModal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeAdminCameraModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        refreshAdminGalleryData();
    });
</script>
