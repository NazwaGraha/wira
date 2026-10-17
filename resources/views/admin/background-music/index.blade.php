@extends('layouts.admin')

@section('title', 'Kelola Sound Mars PMI & Musik Latar')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Status Bar -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-pmr-primary">Backoffice</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-700">Sound Mars PMI</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-music text-rose-500"></i>
                <span>Sound Mars PMI & Musik Latar Website</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola pemutar lagu Mars Palang Merah Indonesia yang berputar otomatis di seluruh halaman publik.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($music && $music->is_active)
                <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 px-3.5 py-1.5 rounded-full text-xs font-extrabold shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Status: Musik Latar Aktif</span>
                </div>
            @else
                <div class="inline-flex items-center gap-2 bg-slate-100 text-slate-600 border border-slate-300 px-3.5 py-1.5 rounded-full text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>Status: Dinonaktifkan</span>
                </div>
            @endif

            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-stone-800 text-white text-xs font-bold transition shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                <span>Lihat Beranda Publik</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-xs animate-in fade-in">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg flex-shrink-0"></i>
            <div class="text-xs sm:text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl shadow-xs">
            <div class="font-bold text-xs uppercase tracking-wider mb-1 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-exclamation"></i> Terjadi Kesalahan Input:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content Grid: 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Form Pengaturan & Upload Audio (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-pmr-primary"></i>
                    <span>Pengaturan Audio & Pemutar Mars PMI</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Sesuaikan judul, volume awal, serta berkas suara yang akan diperdengarkan kepada pengunjung website.
                </p>
            </div>

            <form action="{{ route('admin.background-music.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Judul Lagu & Subjudul -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                            Judul Musik / Lagu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="input_title"
                            value="{{ old('title', $music->title) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-pmr-primary/20 text-xs font-bold transition"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                            Subjudul / Keterangan
                        </label>
                        <input type="text" name="subtitle" id="input_subtitle"
                            value="{{ old('subtitle', $music->subtitle) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-pmr-primary/20 text-xs font-medium transition">
                    </div>
                </div>

                <!-- Info Berkas Audio Saat Ini -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-file-audio text-rose-500"></i>
                            <span>Berkas Audio Saat Ini</span>
                        </div>
                        @if($music->audio_path)
                            <span class="text-[10px] bg-rose-50 text-rose-700 font-bold px-2 py-0.5 rounded-md border border-rose-200">
                                Berkas Unggahan Kustom
                            </span>
                        @else
                            <span class="text-[10px] bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-md border border-emerald-200">
                                Berkas Resmi Bawaan (Mars PMI)
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-volume-high"></i>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="text-xs font-bold text-slate-800 truncate">
                                {{ $music->audio_path ? basename($music->audio_path) : 'mars-pmi.m4a (Paduan Suara Resmi PMI)' }}
                            </div>
                            <div class="text-[11px] text-slate-500 truncate">
                                Path: {{ $music->audio_url }}
                            </div>
                        </div>
                    </div>

                    @if($music->audio_path)
                        <div class="pt-1 flex justify-end">
                            <button type="button" onclick="document.getElementById('form-reset-default').submit()" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1.5 transition">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span>Kembalikan ke Audio Mars PMI Bawaan</span>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Upload Audio Baru (Opsional) -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Ganti / Unggah Berkas Audio Baru <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="file" name="audio_file" accept=".mp3,.m4a,.wav,.ogg,.aac"
                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-rose-50 file:text-pmr-primary hover:file:bg-rose-100 transition border border-slate-200 rounded-xl bg-white">
                    <p class="text-[11px] text-slate-400 mt-1">
                        Format didukung: <strong>MP3, M4A, WAV, OGG, AAC</strong> (Maks. 25 MB). Jika tidak diunggah, sistem tetap menggunakan berkas Mars PMI yang ada.
                    </p>
                </div>

                <!-- Pengaturan Volume Awal (Slider) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700">
                            Volume Awal Suara: <span id="volume_display" class="text-pmr-primary font-black">{{ old('default_volume', $music->default_volume) }}%</span>
                        </label>
                        <span class="text-[10px] text-slate-400">Direkomendasikan: 25% – 35% agar lembut</span>
                    </div>
                    <input type="range" name="default_volume" id="input_volume" min="5" max="100" step="5"
                        value="{{ old('default_volume', $music->default_volume) }}"
                        class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-pmr-primary"
                        oninput="updateVolumePreview(this.value)">
                </div>

                <!-- 3 Toggle Pengaturan (Aktif, Autoplay, Looping) -->
                <div class="space-y-3 pt-2">
                    
                    <!-- Toggle 1: Aktifkan Musik Latar -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                Aktifkan Sound Mars PMI di Website
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Menampilkan floating widget pemutar Mars PMI di seluruh halaman publik website.
                            </div>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $music->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Toggle 2: Autoplay saat interaksi pertama -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                Putar Otomatis saat Sentuhan / Klik Pertama
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Memulai lagu segera setelah pengunjung menyentuh layar, menutup popup, atau mengklik menu (memenuhi aturan browser modern).
                            </div>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="autoplay_on_first_interaction" value="1" {{ old('autoplay_on_first_interaction', $music->autoplay_on_first_interaction) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Toggle 3: Putar Berulang (Looping) -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                Putar Berulang Kali (Looping Musik)
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Memutar ulang lagu secara mulus dari awal begitu lagu Mars PMI selesai.
                            </div>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="loop" value="1" {{ old('loop', $music->loop) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                </div>

                <!-- Textarea Lirik Mars PMI -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Teks Lirik Lagu (Akan tampil pada popover lirik di website)
                    </label>
                    <textarea name="lyrics" rows="6"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-pmr-primary/20 text-xs font-medium transition leading-relaxed">{{ old('lyrics', $music->lyrics) }}</textarea>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-pmr-primary hover:bg-pmr-dark text-white text-xs font-black uppercase tracking-wider transition shadow-lg shadow-red-950/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Pengaturan Sound Mars PMI</span>
                    </button>
                </div>
            </form>

            <!-- Form Hidden Reset Default -->
            <form id="form-reset-default" action="{{ route('admin.background-music.reset') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

        <!-- Right Column: Interactive Player Preview & Panduan Teknis (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card 1: Pratinjau Pemutar Musik Live (Interactive Player) -->
            <div class="bg-gradient-to-br from-stone-900 via-stone-950 to-stone-900 text-white rounded-3xl p-6 shadow-xl border border-white/10 space-y-5 relative overflow-hidden">
                <!-- Ambient Glow -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-rose-600/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <h3 class="text-xs font-black uppercase tracking-widest text-amber-400 flex items-center gap-2">
                        <i class="fa-solid fa-play"></i>
                        <span>Pratinjau Pemutar Mars PMI</span>
                    </h3>
                    <span class="text-[10px] bg-white/15 px-2.5 py-0.5 rounded-full font-bold">
                        Live Preview
                    </span>
                </div>

                <!-- Visualizer Equalizer Bar -->
                <div class="flex items-center justify-center gap-1.5 py-4">
                    <span class="w-1.5 h-6 bg-rose-500 rounded-full animate-bounce [animation-delay:0.1s]"></span>
                    <span class="w-1.5 h-10 bg-amber-400 rounded-full animate-bounce [animation-delay:0.3s]"></span>
                    <span class="w-1.5 h-14 bg-red-600 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-1.5 h-8 bg-amber-300 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                    <span class="w-1.5 h-12 bg-rose-400 rounded-full animate-bounce [animation-delay:0.15s]"></span>
                    <span class="w-1.5 h-5 bg-red-500 rounded-full animate-bounce [animation-delay:0.35s]"></span>
                </div>

                <!-- Info Lagu di Pemutar -->
                <div class="text-center space-y-1">
                    <div id="preview_title" class="font-black text-sm text-white tracking-tight">
                        {{ $music->title }}
                    </div>
                    <div id="preview_subtitle" class="text-xs text-stone-400">
                        {{ $music->subtitle }}
                    </div>
                </div>

                <!-- Audio Controller -->
                <div class="bg-white/10 rounded-2xl p-4 border border-white/10 space-y-3">
                    <audio id="admin-preview-audio" preload="metadata">
                        <source src="{{ $music->audio_url }}" type="audio/mp4">
                        <source src="{{ $music->audio_url }}" type="audio/mpeg">
                    </audio>

                    <div class="flex items-center justify-between text-[11px] font-mono text-stone-400">
                        <span id="preview-current-time">00:00</span>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="toggleAdminPlay()" id="admin-play-btn" class="w-10 h-10 rounded-full bg-pmr-primary hover:bg-rose-600 text-white flex items-center justify-center text-sm shadow-lg transition">
                                <i class="fa-solid fa-play" id="admin-play-icon"></i>
                            </button>
                        </div>
                        <span id="preview-duration">01:30</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-white/20 h-1.5 rounded-full overflow-hidden cursor-pointer" onclick="seekAdminAudio(event)">
                        <div id="admin-audio-progress" class="bg-gradient-to-r from-amber-400 to-rose-500 h-full w-0 transition-all duration-150"></div>
                    </div>
                </div>

                <div class="text-center text-[11px] text-stone-400 italic">
                    Klik tombol play di atas untuk menguji kualitas audio dan kejernihan suara Mars PMI.
                </div>
            </div>

            <!-- Card 2: Edukasi Teknis Autoplay Policy -->
            <div class="bg-blue-50/60 rounded-3xl p-6 border border-blue-200/80 space-y-3">
                <div class="flex items-center gap-2 text-blue-900 font-extrabold text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                    <span>Informasi Kebijakan Browser (Autoplay Policy)</span>
                </div>
                <p class="text-xs text-blue-800 leading-relaxed">
                    Browser modern (Chrome, Safari iOS/Mac, Firefox) secara otomatis membatasi suara sampai pengunjung melakukan <strong>1 kali sentuhan atau klik pertama</strong> pada halaman website.
                </p>
                <div class="bg-white/80 p-3 rounded-xl border border-blue-200 text-[11px] text-blue-900 space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-check text-emerald-600"></i> Cara Kerja di Website PMR:
                    </div>
                    <div>Saat beranda terbuka, begitu pengunjung mengklik tombol popup (misal <em>"Lewati & Tutup"</em> atau klik mana saja di layar), musik Mars PMI akan <strong>langsung berputar otomatis</strong> secara anggun dan elegan.</div>
                </div>
            </div>

            <!-- Card 3: Lirik Resmi Mars PMI -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3">
                <div class="flex items-center gap-2 text-slate-800 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-book-bookmark text-amber-500"></i>
                    <span>Lirik Resmi Mars Palang Merah Indonesia</span>
                </div>
                <div class="text-xs text-slate-600 leading-relaxed font-serif whitespace-pre-line bg-stone-50 p-4 rounded-2xl border border-slate-200/60 italic">
                    {{ $music->lyrics }}
                </div>
                <div class="text-[11px] text-slate-400 text-right">
                    Karya Resmi: <strong>Mochtar H. S.</strong>
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    const audio = document.getElementById('admin-preview-audio');
    const playBtn = document.getElementById('admin-play-btn');
    const playIcon = document.getElementById('admin-play-icon');
    const progressBar = document.getElementById('admin-audio-progress');
    const currentTimeEl = document.getElementById('preview-current-time');
    const durationEl = document.getElementById('preview-duration');

    function updateVolumePreview(val) {
        document.getElementById('volume_display').textContent = val + '%';
        if (audio) {
            audio.volume = val / 100;
        }
    }

    function toggleAdminPlay() {
        if (!audio) return;
        if (audio.paused) {
            const volInput = document.getElementById('input_volume');
            if (volInput) audio.volume = volInput.value / 100;
            audio.play().then(() => {
                playIcon.classList.remove('fa-play');
                playIcon.classList.add('fa-pause');
            }).catch(e => console.log(e));
        } else {
            audio.pause();
            playIcon.classList.remove('fa-pause');
            playIcon.classList.add('fa-play');
        }
    }

    if (audio) {
        audio.addEventListener('timeupdate', () => {
            if (audio.duration) {
                const pct = (audio.currentTime / audio.duration) * 100;
                progressBar.style.width = pct + '%';
                currentTimeEl.textContent = formatTime(audio.currentTime);
                durationEl.textContent = formatTime(audio.duration);
            }
        });

        audio.addEventListener('ended', () => {
            playIcon.classList.remove('fa-pause');
            playIcon.classList.add('fa-play');
            progressBar.style.width = '0%';
        });
    }

    function seekAdminAudio(e) {
        if (!audio || !audio.duration) return;
        const rect = e.currentTarget.getBoundingClientRect();
        const pos = (e.clientX - rect.left) / rect.width;
        audio.currentTime = pos * audio.duration;
    }

    function formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60);
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }
</script>
@endpush
@endsection
