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
                    <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-sm shrink-0">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-semibold">Biaya Registrasi</div>
                        <div class="text-xs font-bold text-white" id="preview-fee">
                            Rp {{ number_format($event->registration_fee ?: 150000, 0, ',', '.') }} / Tim
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.competition-event.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
</script>
@endpush
@endsection
