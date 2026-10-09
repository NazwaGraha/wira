@extends('layouts.admin')

@section('title', 'Edit ' . $event->title)
@section('page_title', 'Edit Pengaturan: ' . $event->title)

@section('top_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.competition-event.index') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Riwayat</span>
        </a>
        <a href="{{ route('admin.competition-event.show', $event->id) }}" class="bg-sky-600 hover:bg-sky-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-folder-open"></i>
            <span>Detail & History Data</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl shadow-xs">
            <div class="font-bold text-sm mb-1 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Ada kesalahan input:
            </div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Live Preview Banner (Mirrors Landing Page) -->
    <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 rounded-2xl p-6 sm:p-8 text-white border border-slate-800 relative overflow-hidden shadow-md">
        <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#ef4444_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative z-10 max-w-3xl mx-auto text-center space-y-3">
            <div class="inline-flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-400 px-3.5 py-1 rounded-full text-xs font-bold tracking-wider uppercase" id="preview-theme">
                <i class="fa-solid fa-trophy"></i> <span>{{ $event->theme ?: 'AJANG PRESTASI RELAWAN MUDA PMR WIRA CIAWI' }}</span>
            </div>

            <h2 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-white uppercase" id="preview-title">
                {{ $event->title ?: 'SUA BHAKTI BERKARYA III TAHUN 2025' }}
            </h2>

            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto leading-relaxed" id="preview-description">
                {{ $event->description ?: 'Ajang kompetisi kepalangmerahan bergengsi tingkat Mula (SD), Madya (SMP), dan Wira (SMA/SMK/MA) se-Jabodetabek dan sekitarnya.' }}
            </p>

            <!-- Quick Cards Preview -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4 text-left">
                <div class="bg-slate-800/80 border border-slate-700/80 p-3 rounded-xl flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center text-sm shrink-0">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold">Pelaksanaan</div>
                        <div class="text-xs font-bold text-white" id="preview-date">
                            {{ $event->start_date ? $event->start_date->translatedFormat('d F Y') : '15 Oktober 2026' }}
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/80 p-3 rounded-xl flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm shrink-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold">Lokasi</div>
                        <div class="text-xs font-bold text-white truncate max-w-[160px]" id="preview-location">
                            {{ $event->location ?: 'Kampus SMAN 1 Ciawi Bogor' }}
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/80 p-3 rounded-xl flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center text-sm shrink-0">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <div>
                        <div class="text-[10px] text-red-400 font-semibold uppercase tracking-wider">Pusat Informasi</div>
                        <div class="text-xs font-bold text-white">
                            Menu Informasi Lomba <span class="text-[10px] text-slate-400 font-normal">(8 Sub Menu)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Navigation Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex flex-wrap items-center gap-2">
            <a href="#section-identity" class="px-4 py-2 rounded-xl text-xs font-black bg-slate-900 text-white hover:bg-slate-800 transition flex items-center gap-2">
                <i class="fa-solid fa-sliders text-rose-400"></i>
                <span>1. Identitas & Jadwal Event</span>
            </a>
            <a href="#section-categories" class="px-4 py-2 rounded-xl text-xs font-black bg-red-600 text-white hover:bg-red-700 transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-trophy text-amber-300"></i>
                <span>2. Cabang Lomba Dipertandingkan ({{ $event->categories->count() }})</span>
            </a>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddCategoryModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Cabang Lomba</span>
            </button>
            <button type="button" onclick="openPresetModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>Template Paket Standar</span>
            </button>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form id="section-identity" action="{{ route('admin.competition-event.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Main Info & Schedule -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. Informasi Utama Event -->
                <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Identitas & Judul Lomba</h3>
                            <p class="text-xs text-slate-500">Teks ini tampil sebagai judul utama di website publik /lomba</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Judul Event / Nama Lomba <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="input-title" value="{{ old('title', $event->title) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 text-sm font-black text-slate-900" placeholder="SUA BHAKTI BERKARYA III TAHUN 2025">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tema / Slogan / Badge Atas
                            </label>
                            <input type="text" name="theme" id="input-theme" value="{{ old('theme', $event->theme) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 text-sm font-semibold text-slate-800" placeholder="AJANG PRESTASI RELAWAN MUDA PMR WIRA CIAWI">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Deskripsi Singkat Lomba
                            </label>
                            <textarea name="description" id="input-description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 text-sm text-slate-700 leading-relaxed">{{ old('description', $event->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Waktu & Lokasi -->
                <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Jadwal Pelaksanaan & Lokasi</h3>
                            <p class="text-xs text-slate-500">Informasi waktu kegiatan dan tempat penyelenggaraan</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Mulai
                                </label>
                                <input type="date" name="start_date" id="input-start-date" value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-semibold text-slate-800">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Selesai
                                </label>
                                <input type="date" name="end_date" id="input-end-date" value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-semibold text-slate-800">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Lokasi Pelaksanaan
                            </label>
                            <input type="text" name="location" id="input-location" value="{{ old('location', $event->location) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-semibold text-slate-800" placeholder="Kampus SMAN 1 Ciawi Bogor">
                        </div>
                    </div>
                </div>

                <!-- 3. Rekening Pembayaran -->
                <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Rekening Pembayaran Registrasi</h3>
                            <p class="text-xs text-slate-500">Tampil di formulir pendaftaran sebagai instruksi transfer biaya lomba</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Bank
                            </label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $event->bank_name) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-bold text-slate-800" placeholder="Bank BCA">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nomor Rekening
                            </label>
                            <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $event->bank_account_number) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-mono font-bold text-slate-900" placeholder="1234567890">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Atas Nama
                            </label>
                            <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $event->bank_account_holder) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-bold text-slate-800" placeholder="PMR WIRA SMAN 1 CIAWI">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 1 Col: Status & Lampiran -->
            <div class="space-y-6">

                <!-- 4. Kontrol Status & Biaya Standar -->
                <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200 space-y-5">
                    <h3 class="font-extrabold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-toggle-on text-emerald-500"></i>
                        <span>Status & Biaya</span>
                    </h3>

                    <!-- Toggle Pendaftaran Buka/Tutup -->
                    <div class="p-4 rounded-xl border {{ $event->is_registration_open ? 'bg-emerald-50/70 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-slate-900">Pendaftaran Online</div>
                                <div class="text-[11px] text-slate-500">Izinkan pendaftaran via web</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_registration_open" value="1" class="sr-only peer" {{ old('is_registration_open', $event->is_registration_open) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Toggle Event Aktif -->
                    <div class="p-4 rounded-xl border {{ $event->is_active ? 'bg-blue-50/70 border-blue-200' : 'bg-slate-50 border-slate-200' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-slate-900">Status Event Aktif</div>
                                <div class="text-[11px] text-slate-500">Event utama yang tampil di web</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $event->is_active) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Biaya Default per Tim -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Biaya Registrasi Default (per Regu)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="registration_fee" id="input-fee" value="{{ old('registration_fee', intval($event->registration_fee ?: 150000)) }}" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-mono font-bold text-slate-900">
                        </div>
                    </div>
                </div>

                <!-- 5. Dokumen Juklak & Pamflet Banner -->
                <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-indigo-500"></i>
                        <span>Buku Panduan & Pamflet</span>
                    </h3>

                    <!-- Upload Juklak/Juknis PDF -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Buku Panduan / Juklak Juknis (PDF)
                        </label>
                        <input type="file" name="handbook_file" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        @if($event->handbook_file)
                            <div class="mt-2 flex items-center gap-2 text-xs text-indigo-600 font-bold">
                                <i class="fa-solid fa-file-pdf"></i>
                                <a href="{{ asset('storage/' . $event->handbook_file) }}" target="_blank" class="hover:underline">Unduh Juknis Terpasang</a>
                            </div>
                        @endif
                    </div>

                    <!-- Upload Banner -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Banner / Pamflet Lomba (JPG/PNG)
                        </label>
                        <input type="file" name="banner_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        @if($event->banner_image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $event->banner_image) }}" alt="Banner Lomba" class="h-20 rounded-lg object-cover border border-slate-200">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-3.5 px-6 rounded-2xl shadow-lg shadow-red-900/30 transition flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-floppy-disk text-base"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

            </div>

        </div>

    </form>

    <!-- SECTION 2: KOMPOSISI CABANG LOMBA DI INI -->
    <div id="section-categories" class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200 space-y-6">
        
        <!-- Header & Action Buttons -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-start gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-red-500/20 shrink-0">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-black text-slate-900 text-lg sm:text-xl">Cabang Lomba yang Dipertandingkan</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-red-100 text-red-800">
                            {{ $event->categories->count() }} Mata Lomba
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        Atur komposisi cabang mata lomba khusus untuk <strong>{{ $event->title }}</strong>. Setiap perubahan (tambah, edit, atau hapus) akan <strong>otomatis sinkron</strong> ke formulir pendaftaran, lembar presensi, dan kartu pada halaman <strong>Input Nilai Lomba</strong>.
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" onclick="openPresetModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold px-3.5 py-2.5 rounded-xl text-xs transition flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i>
                    <span>Template Standar</span>
                </button>
                <button type="button" onclick="openAddCategoryModal()" class="bg-red-600 hover:bg-red-700 text-white font-extrabold px-4 py-2.5 rounded-xl text-xs transition flex items-center gap-2 shadow-md shadow-red-900/20">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Cabang Lomba</span>
                </button>
            </div>
        </div>

        <!-- Metric Counter Pills by Level -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Lomba</div>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ $event->categories->count() }}</div>
                </div>
                <div class="w-8 h-8 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>

            <div class="p-3.5 bg-blue-50/60 rounded-2xl border border-blue-100 flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">PMR Mula (SD)</div>
                    <div class="text-xl font-black text-blue-900 mt-0.5">{{ $categoriesByLevel['Mula']->count() }} <span class="text-xs font-normal text-blue-600">Lomba</span></div>
                </div>
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-child-reaching"></i>
                </div>
            </div>

            <div class="p-3.5 bg-red-50/60 rounded-2xl border border-red-100 flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-red-600 uppercase tracking-wider">PMR Madya (SMP)</div>
                    <div class="text-xl font-black text-red-900 mt-0.5">{{ $categoriesByLevel['Madya']->count() }} <span class="text-xs font-normal text-red-600">Lomba</span></div>
                </div>
                <div class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-user-group"></i>
                </div>
            </div>

            <div class="p-3.5 bg-amber-50/60 rounded-2xl border border-amber-100 flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">PMR Wira (SMA)</div>
                    <div class="text-xl font-black text-amber-900 mt-0.5">{{ $categoriesByLevel['Wira']->count() }} <span class="text-xs font-normal text-amber-600">Lomba</span></div>
                </div>
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            </div>
        </div>

        <!-- Filter Pills & Search Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pt-2">
            <!-- Level Filter Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-2xl border border-slate-200">
                <button type="button" onclick="filterCategoryLevel('all')" id="tab-lvl-all" class="cat-level-tab px-3.5 py-1.5 rounded-xl text-xs font-black transition bg-white text-slate-900 shadow-2xs">
                    Semua Tingkat ({{ $event->categories->count() }})
                </button>
                <button type="button" onclick="filterCategoryLevel('Mula')" id="tab-lvl-mula" class="cat-level-tab px-3.5 py-1.5 rounded-xl text-xs font-black transition text-slate-600 hover:text-slate-900">
                    <i class="fa-solid fa-child-reaching mr-1 text-blue-500"></i> Mula ({{ $categoriesByLevel['Mula']->count() }})
                </button>
                <button type="button" onclick="filterCategoryLevel('Madya')" id="tab-lvl-madya" class="cat-level-tab px-3.5 py-1.5 rounded-xl text-xs font-black transition text-slate-600 hover:text-slate-900">
                    <i class="fa-solid fa-user-group mr-1 text-red-500"></i> Madya ({{ $categoriesByLevel['Madya']->count() }})
                </button>
                <button type="button" onclick="filterCategoryLevel('Wira')" id="tab-lvl-wira" class="cat-level-tab px-3.5 py-1.5 rounded-xl text-xs font-black transition text-slate-600 hover:text-slate-900">
                    <i class="fa-solid fa-graduation-cap mr-1 text-amber-500"></i> Wira ({{ $categoriesByLevel['Wira']->count() }})
                </button>
            </div>

            <!-- Instant Search Input -->
            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="cat-search-input" oninput="searchCategories(this.value)" placeholder="Cari cabang lomba / kode..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-red-500 focus:bg-white transition">
            </div>
        </div>

        <!-- Table of Categories -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-extrabold text-[11px]">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Cabang Mata Lomba</th>
                        <th class="py-3.5 px-4">Tingkat</th>
                        <th class="py-3.5 px-4">Kategori Gender</th>
                        <th class="py-3.5 px-4">Tipe Penilaian</th>
                        <th class="py-3.5 px-4">Tier Poin</th>
                        <th class="py-3.5 px-4">Biaya / Regu</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="category-table-body">
                    @forelse($event->categories as $index => $cat)
                        <tr class="cat-row hover:bg-slate-50/80 transition"
                            data-id="{{ $cat->id }}"
                            data-level="{{ $cat->level }}"
                            data-gender="{{ $cat->gender_category }}"
                            data-name="{{ $cat->name }}"
                            data-code="{{ $cat->code }}"
                            data-scoring="{{ $cat->scoring_type }}"
                            data-tier="{{ $cat->point_tier }}"
                            data-fee="{{ intval($cat->registration_fee) }}"
                            data-members="{{ $cat->max_team_members }}"
                            data-order="{{ $cat->order_position }}"
                            data-active="{{ $cat->is_active ? '1' : '0' }}">
                            
                            <td class="py-3 px-4 text-center font-bold text-slate-400">
                                {{ $cat->order_position ?: ($index + 1) }}
                            </td>
                            
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold text-[11px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200 whitespace-nowrap">
                                    {{ $cat->code ?: '-' }}
                                </span>
                            </td>

                            <td class="py-3 px-4">
                                <div class="font-black text-slate-900 text-sm">
                                    {{ $cat->name }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Maks. {{ $cat->max_team_members }} orang / regu
                                </div>
                            </td>

                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($cat->level === 'Mula')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800">
                                        <i class="fa-solid fa-child-reaching text-[9px]"></i> MULA (SD)
                                    </span>
                                @elseif($cat->level === 'Madya')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-800">
                                        <i class="fa-solid fa-user-group text-[9px]"></i> MADYA (SMP)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-graduation-cap text-[9px]"></i> WIRA (SMA)
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($cat->gender_category === 'Putra')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-mars text-[9px]"></i> Putra
                                    </span>
                                @elseif($cat->gender_category === 'Putri')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-venus text-[9px]"></i> Putri
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="fa-solid fa-users text-[9px]"></i> Umum
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-slate-700 font-medium">
                                @php
                                    $scoreLabel = match($cat->scoring_type) {
                                        'written_practical_time' => 'Tertulis (30%) + Praktik (70%) + Waktu',
                                        'standard_time' => 'Nilai Teknis + Waktu',
                                        'tandu_standard_time' => 'Ketangkasan Tandu Reguler (Waktu)',
                                        'multi_criteria' => 'Rubrik Kriteria Juri Jamak',
                                        'bracket_quiz' => 'Cepat Tepat / Cerdas Cermat',
                                        'social_engagement' => 'Engagement / Medsos',
                                        default => ucwords(str_replace('_', ' ', $cat->scoring_type))
                                    };
                                @endphp
                                <span class="truncate block max-w-[190px]" title="{{ $scoreLabel }}">{{ $scoreLabel }}</span>
                            </td>

                            <td class="py-3 px-4 whitespace-nowrap font-bold">
                                @if($cat->point_tier === 'tier_1')
                                    <span class="text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded text-[10px]">Tier 1 (10/8/6)</span>
                                @elseif($cat->point_tier === 'tier_2')
                                    <span class="text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded text-[10px]">Tier 2 (8/6/4)</span>
                                @else
                                    <span class="text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-[10px]">Tier 3 (3/2/1)</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 font-mono font-bold text-slate-800 whitespace-nowrap">
                                Rp {{ number_format($cat->registration_fee ?: 150000, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($cat->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" onclick="editCategory(this)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition" title="Edit Cabang Lomba">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    <form action="{{ route('admin.competition-event.categories.destroy', [$event->id, $cat->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang lomba \'{{ $cat->name }} ({{ $cat->level }} - {{ $cat->gender_category }})\' dari edisi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition" title="Hapus Cabang Lomba">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl mb-3">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <div class="font-bold text-slate-700 text-sm">Belum Ada Cabang Lomba Terdaftar</div>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Tambahkan cabang lomba secara manual atau gunakan paket standar untuk langsung mengisi lomba jenjang Mula, Madya, dan Wira.
                                </p>
                                <div class="mt-4 flex items-center justify-center gap-2">
                                    <button type="button" onclick="openPresetModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded-xl text-xs transition">
                                        Gunakan Template Standar
                                    </button>
                                    <button type="button" onclick="openAddCategoryModal()" class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-xl text-xs transition">
                                        + Tambah Cabang Manual
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- ================= MODALS ================= -->

<!-- 1. Modal Tambah Cabang Lomba -->
<div id="modal-add-category" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-6 sm:p-7 overflow-y-auto max-h-[90vh] border border-slate-200">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h4 class="font-black text-slate-900 text-base">Tambah Cabang Lomba Baru</h4>
                    <p class="text-xs text-slate-500">Mata lomba ini akan langsung muncul di Input Nilai & Pendaftaran</p>
                </div>
            </div>
            <button type="button" onclick="closeAddCategoryModal()" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.competition-event.categories.store', $event->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tingkat / Level -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tingkatan (Level) <span class="text-red-500">*</span>
                    </label>
                    <select name="level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-xs font-bold text-slate-800 bg-slate-50">
                        <option value="Mula">PMR MULA (SD/MI)</option>
                        <option value="Madya" selected>PMR MADYA (SMP/MTs)</option>
                        <option value="Wira">PMR WIRA (SMA/SMK/MA)</option>
                    </select>
                </div>

                <!-- Gender Category -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori Gender <span class="text-red-500">*</span>
                    </label>
                    <select name="gender_category" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-xs font-bold text-slate-800 bg-slate-50">
                        <option value="Putra">Putra (Regu Laki-laki)</option>
                        <option value="Putri">Putri (Regu Perempuan)</option>
                        <option value="Umum" selected>Umum / Bebas Gender</option>
                        <option value="Campuran">Campuran</option>
                    </select>
                </div>
            </div>

            <!-- Nama Lomba -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Cabang Lomba <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required placeholder="Contoh: Pertolongan Pertama / Ketangkasan Tandu Reguler" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-xs font-black text-slate-900">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kode Lomba (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kode Singkat (Opsional)
                    </label>
                    <input type="text" name="code" placeholder="Contoh: LPP-MADYA-PA" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-xs font-mono font-bold text-slate-800">
                    <span class="text-[10px] text-slate-400 mt-1 block">Biarkan kosong untuk generate otomatis</span>
                </div>

                <!-- Tipe Penilaian -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tipe Penilaian Juri <span class="text-red-500">*</span>
                    </label>
                    <select name="scoring_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-xs font-bold text-slate-800 bg-slate-50">
                        <option value="written_practical_time">Ujian Tertulis 30% + Praktik 70% + Waktu (Pertolongan Pertama)</option>
                        <option value="standard_time">Nilai Teknis + Waktu (Tandu Reguler, Cuci Tangan)</option>
                        <option value="multi_criteria">Rubrik Kriteria Juri Jamak (Mading, Mewarnai)</option>
                        <option value="bracket_quiz">Cepat Tepat / Cerdas Cermat</option>
                        <option value="social_engagement">Sosial Media & Engagement (PMR Favorite)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Tier Poin Juara Umum -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tier Poin Juara Umum
                    </label>
                    <select name="point_tier" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-xs font-bold text-slate-800 bg-slate-50">
                        <option value="tier_1" selected>Tier 1 (10 / 8 / 6 Poin)</option>
                        <option value="tier_2">Tier 2 (8 / 6 / 4 Poin)</option>
                        <option value="tier_3">Tier 3 (3 / 2 / 1 Poin)</option>
                    </select>
                </div>

                <!-- Biaya Pendaftaran Cabang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Biaya / Regu (Rp)
                    </label>
                    <input type="number" name="registration_fee" value="{{ intval($event->registration_fee ?: 150000) }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-xs font-mono font-bold text-slate-800">
                </div>

                <!-- Maks Anggota -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Maks Anggota
                    </label>
                    <input type="number" name="max_team_members" value="2" min="1" max="20" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-xs font-bold text-slate-800">
                </div>
            </div>

            <!-- Status Aktif Checkbox -->
            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300">
                    <span class="text-xs font-bold text-slate-700">Aktifkan Cabang Lomba ini untuk dipertandingkan</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddCategoryModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-black px-5 py-2.5 rounded-xl text-xs shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Cabang Lomba</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal Edit Cabang Lomba -->
<div id="modal-edit-category" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-6 sm:p-7 overflow-y-auto max-h-[90vh] border border-slate-200">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h4 class="font-black text-slate-900 text-base">Edit Cabang Lomba</h4>
                    <p class="text-xs text-slate-500">Perbarui informasi cabang mata lomba</p>
                </div>
            </div>
            <button type="button" onclick="closeEditCategoryModal()" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="form-edit-category" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tingkat / Level -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tingkatan (Level) <span class="text-red-500">*</span>
                    </label>
                    <select name="level" id="edit-level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs font-bold text-slate-800 bg-slate-50">
                        <option value="Mula">PMR MULA (SD/MI)</option>
                        <option value="Madya">PMR MADYA (SMP/MTs)</option>
                        <option value="Wira">PMR WIRA (SMA/SMK/MA)</option>
                    </select>
                </div>

                <!-- Gender Category -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori Gender <span class="text-red-500">*</span>
                    </label>
                    <select name="gender_category" id="edit-gender" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs font-bold text-slate-800 bg-slate-50">
                        <option value="Putra">Putra (Regu Laki-laki)</option>
                        <option value="Putri">Putri (Regu Perempuan)</option>
                        <option value="Umum">Umum / Bebas Gender</option>
                        <option value="Campuran">Campuran</option>
                    </select>
                </div>
            </div>

            <!-- Nama Lomba -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Cabang Lomba <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs font-black text-slate-900">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kode Lomba -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kode Singkat
                    </label>
                    <input type="text" name="code" id="edit-code" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs font-mono font-bold text-slate-800">
                </div>

                <!-- Tipe Penilaian -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tipe Penilaian Juri <span class="text-red-500">*</span>
                    </label>
                    <select name="scoring_type" id="edit-scoring" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs font-bold text-slate-800 bg-slate-50">
                        <option value="written_practical_time">Ujian Tertulis 30% + Praktik 70% + Waktu (Pertolongan Pertama)</option>
                        <option value="standard_time">Nilai Teknis + Waktu (Tandu Reguler, Cuci Tangan)</option>
                        <option value="multi_criteria">Rubrik Kriteria Juri Jamak (Mading, Mewarnai)</option>
                        <option value="bracket_quiz">Cepat Tepat / Cerdas Cermat</option>
                        <option value="social_engagement">Sosial Media & Engagement (PMR Favorite)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Tier Poin -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tier Poin
                    </label>
                    <select name="point_tier" id="edit-tier" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs font-bold text-slate-800 bg-slate-50">
                        <option value="tier_1">Tier 1 (10 / 8 / 6 Poin)</option>
                        <option value="tier_2">Tier 2 (8 / 6 / 4 Poin)</option>
                        <option value="tier_3">Tier 3 (3 / 2 / 1 Poin)</option>
                    </select>
                </div>

                <!-- Biaya Pendaftaran -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Biaya / Regu (Rp)
                    </label>
                    <input type="number" name="registration_fee" id="edit-fee" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs font-mono font-bold text-slate-800">
                </div>

                <!-- Urutan No -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Urutan Tampil
                    </label>
                    <input type="number" name="order_position" id="edit-order" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs font-bold text-slate-800">
                </div>
            </div>

            <!-- Status Aktif Checkbox -->
            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit-active" value="1" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                    <span class="text-xs font-bold text-slate-700">Aktifkan Cabang Lomba ini</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-5 py-2.5 rounded-xl text-xs shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal Template Paket Standar -->
<div id="modal-preset-categories" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 overflow-y-auto max-h-[90vh] border border-slate-200">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h4 class="font-black text-slate-900 text-base">Salin Template Cabang Standar</h4>
                    <p class="text-xs text-slate-500">Isi otomatis cabang lomba resmi PMR</p>
                </div>
            </div>
            <button type="button" onclick="closePresetModal()" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.competition-event.categories.preset', $event->id) }}" method="POST" class="space-y-4">
            @csrf

            <p class="text-xs text-slate-600 leading-relaxed">
                Pilih paket lomba standar PMR yang ingin otomatis ditambahkan ke edisi ini. Lomba yang sudah ada tidak akan diduplikasi secara ganda.
            </p>

            <div class="space-y-2.5">
                <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 hover:border-indigo-400 cursor-pointer bg-slate-50">
                    <input type="radio" name="preset_type" value="all_standard" checked class="text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <div class="text-xs font-black text-slate-900">Semua Tingkat Lengkap (Mula, Madya, Wira)</div>
                        <div class="text-[11px] text-slate-500">Menyalin seluruh mata lomba PP, Tandu, Cuci Tangan, Mewarnai, Cepat Tepat, Olimpiade, Mading, & Favorite.</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 hover:border-indigo-400 cursor-pointer bg-slate-50">
                    <input type="radio" name="preset_type" value="mula_standard" class="text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <div class="text-xs font-black text-slate-900">Khusus PMR Mula (SD/MI)</div>
                        <div class="text-[11px] text-slate-500">PP Pa/Pi, Tandu Pa/Pi, Cuci Tangan, Mewarnai, dan Mading Kreasi.</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 hover:border-indigo-400 cursor-pointer bg-slate-50">
                    <input type="radio" name="preset_type" value="madya_standard" class="text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <div class="text-xs font-black text-slate-900">Khusus PMR Madya (SMP/MTs)</div>
                        <div class="text-[11px] text-slate-500">PP Pa/Pi, Tandu Pa/Pi, Cepat Tepat, Olimpiade, Mading, dan PMR Favorite.</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 hover:border-indigo-400 cursor-pointer bg-slate-50">
                    <input type="radio" name="preset_type" value="wira_standard" class="text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <div class="text-xs font-black text-slate-900">Khusus PMR Wira (SMA/SMK/MA)</div>
                        <div class="text-[11px] text-slate-500">PP Pa/Pi, Tandu Pa/Pi, Cepat Tepat, Olimpiade, Mading, dan PMR Favorite.</div>
                    </div>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closePresetModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-5 py-2.5 rounded-xl text-xs shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Terapkan Paket Lomba</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const inputTitle = document.getElementById('input-title');
    const inputTheme = document.getElementById('input-theme');
    const inputDesc = document.getElementById('input-description');
    const inputLocation = document.getElementById('input-location');
    const inputFee = document.getElementById('input-fee');
    const inputStartDate = document.getElementById('input-start-date');

    const previewTitle = document.getElementById('preview-title');
    const previewTheme = document.getElementById('preview-theme')?.querySelector('span');
    const previewDesc = document.getElementById('preview-description');
    const previewLocation = document.getElementById('preview-location');
    const previewFee = document.getElementById('preview-fee');
    const previewDate = document.getElementById('preview-date');

    if (inputTitle && previewTitle) {
        inputTitle.addEventListener('input', (e) => {
            previewTitle.textContent = e.target.value || 'SUA BHAKTI BERKARYA III TAHUN 2025';
        });
    }

    if (inputTheme && previewTheme) {
        inputTheme.addEventListener('input', (e) => {
            previewTheme.textContent = e.target.value || 'AJANG PRESTASI RELAWAN MUDA PMR WIRA CIAWI';
        });
    }

    if (inputDesc && previewDesc) {
        inputDesc.addEventListener('input', (e) => {
            previewDesc.textContent = e.target.value || '';
        });
    }

    if (inputLocation && previewLocation) {
        inputLocation.addEventListener('input', (e) => {
            previewLocation.textContent = e.target.value || '-';
        });
    }

    if (inputFee && previewFee) {
        inputFee.addEventListener('input', (e) => {
            const val = parseInt(e.target.value) || 0;
            previewFee.textContent = 'Rp ' + val.toLocaleString('id-ID') + ' / Tim';
        });
    }

    if (inputStartDate && previewDate) {
        inputStartDate.addEventListener('change', (e) => {
            if (e.target.value) {
                const dateObj = new Date(e.target.value);
                previewDate.textContent = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            }
        });
    }

    // ================= MODAL & FILTER FUNCTIONS =================
    function openAddCategoryModal() {
        const modal = document.getElementById('modal-add-category');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeAddCategoryModal() {
        const modal = document.getElementById('modal-add-category');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function openEditCategoryModal() {
        const modal = document.getElementById('modal-edit-category');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeEditCategoryModal() {
        const modal = document.getElementById('modal-edit-category');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function openPresetModal() {
        const modal = document.getElementById('modal-preset-categories');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closePresetModal() {
        const modal = document.getElementById('modal-preset-categories');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function editCategory(btn) {
        const row = btn.closest('.cat-row');
        if (!row) return;

        const id = row.getAttribute('data-id');
        const level = row.getAttribute('data-level');
        const gender = row.getAttribute('data-gender');
        const name = row.getAttribute('data-name');
        const code = row.getAttribute('data-code');
        const scoring = row.getAttribute('data-scoring');
        const tier = row.getAttribute('data-tier');
        const fee = row.getAttribute('data-fee');
        const order = row.getAttribute('data-order');
        const active = row.getAttribute('data-active') === '1';

        const form = document.getElementById('form-edit-category');
        form.action = "{{ url('admin/competition-events/' . $event->id . '/categories') }}/" + id;

        document.getElementById('edit-level').value = level;
        document.getElementById('edit-gender').value = gender;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-code').value = code || '';
        document.getElementById('edit-scoring').value = scoring;
        document.getElementById('edit-tier').value = tier;
        document.getElementById('edit-fee').value = fee;
        document.getElementById('edit-order').value = order;
        document.getElementById('edit-active').checked = active;

        openEditCategoryModal();
    }

    // Filter categories by Level Tabs
    let currentLevelFilter = 'all';
    function filterCategoryLevel(lvl) {
        currentLevelFilter = lvl;

        document.querySelectorAll('.cat-level-tab').forEach(tab => {
            tab.classList.remove('bg-white', 'text-slate-900', 'shadow-2xs');
            tab.classList.add('text-slate-600');
        });

        const activeTab = document.getElementById('tab-lvl-' + lvl.toLowerCase());
        if (activeTab) {
            activeTab.classList.add('bg-white', 'text-slate-900', 'shadow-2xs');
            activeTab.classList.remove('text-slate-600');
        }

        applyCategoryFilters();
    }

    // Instant Search
    let currentSearchQuery = '';
    function searchCategories(q) {
        currentSearchQuery = q.toLowerCase().trim();
        applyCategoryFilters();
    }

    function applyCategoryFilters() {
        const rows = document.querySelectorAll('.cat-row');
        rows.forEach(row => {
            const rowLevel = row.getAttribute('data-level');
            const rowName = (row.getAttribute('data-name') || '').toLowerCase();
            const rowCode = (row.getAttribute('data-code') || '').toLowerCase();

            const matchLevel = (currentLevelFilter === 'all' || rowLevel === currentLevelFilter);
            const matchSearch = (!currentSearchQuery || rowName.includes(currentSearchQuery) || rowCode.includes(currentSearchQuery));

            if (matchLevel && matchSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Close modals on Escape key or backdrop click
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAddCategoryModal();
            closeEditCategoryModal();
            closePresetModal();
        }
    });

    ['modal-add-category', 'modal-edit-category', 'modal-preset-categories'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', (e) => {
                if (e.target === el) {
                    el.classList.remove('flex');
                    el.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush
@endsection
