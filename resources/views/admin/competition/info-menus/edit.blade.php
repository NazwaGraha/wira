@extends('layouts.admin')

@section('title', 'Edit Sub Menu: ' . $infoMenu->title)
@section('page_title', 'Edit Sub Menu Informasi Lomba')

@section('top_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.competition-info-menus.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-2 border border-slate-200">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl">
            <div class="font-bold text-sm mb-1 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-500"></i> Mohon periksa kembali form isian:
            </div>
            <ul class="list-disc pl-5 text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.competition-info-menus.update', $infoMenu->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-7">
        @csrf
        @method('PUT')

        <!-- Section 1: Identitas Menu -->
        <div class="border-b border-slate-100 pb-6 space-y-5">
            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-heading text-pmr-primary"></i> 1. Identitas & Teks Sub Menu
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Title -->
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700">Nama Sub Menu <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $infoMenu->title) }}" required placeholder="Contoh: Surat Rekomendasi, Grid Nilai, Peta Lokasi" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-sm font-semibold transition">
                </div>

                <!-- Category Badge -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Label Badge Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="category_badge" id="category_badge" value="{{ old('category_badge', $infoMenu->category_badge) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-sm font-semibold transition">
                    <!-- Quick Pill Suggestions -->
                    <div class="flex items-center gap-1.5 flex-wrap pt-1 text-[11px]">
                        <span class="text-slate-400">Pilihan cepat:</span>
                        <button type="button" onclick="setBadge('Dokumen Resmi')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold cursor-pointer">Dokumen Resmi</button>
                        <button type="button" onclick="setBadge('Undangan')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold cursor-pointer">Undangan</button>
                        <button type="button" onclick="setBadge('Wajib Dibaca')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold cursor-pointer">Wajib Dibaca</button>
                        <button type="button" onclick="setBadge('Transparansi')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold cursor-pointer">Transparansi</button>
                        <button type="button" onclick="setBadge('Venue & Denah')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold cursor-pointer">Venue & Denah</button>
                        <button type="button" onclick="setBadge('Handbook')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold cursor-pointer">Handbook</button>
                        <button type="button" onclick="setBadge('Galeri Media')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold cursor-pointer">Galeri Media</button>
                        <button type="button" onclick="setBadge('Hotline 24/7')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold cursor-pointer">Hotline 24/7</button>
                    </div>
                </div>

                <!-- Event Association -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Tautkan ke Event</label>
                    <select name="competition_event_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-sm font-semibold transition bg-white">
                        <option value="">Semua Event (Global)</option>
                        @foreach($allEvents as $ev)
                            <option value="{{ $ev->id }}" {{ old('competition_event_id', $infoMenu->competition_event_id) == $ev->id ? 'selected' : '' }}>
                                {{ $ev->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700">Keterangan / Deskripsi Singkat</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-sm font-medium transition">{{ old('description', $infoMenu->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Section 2: Visual, Ikon & Warna -->
        <div class="border-b border-slate-100 pb-6 space-y-5">
            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-palette text-amber-500"></i> 2. Ikon & Warna Tema Visual
            </h3>

            <!-- Color Palette Selector -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Warna Aksen Tema <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $colors = [
                            'red' => ['label' => 'Merah (Red)', 'bg' => 'bg-red-500'],
                            'sky' => ['label' => 'Biru Langit (Sky)', 'bg' => 'bg-sky-500'],
                            'rose' => ['label' => 'Mawar (Rose)', 'bg' => 'bg-rose-500'],
                            'amber' => ['label' => 'Kuning Emas (Amber)', 'bg' => 'bg-amber-500'],
                            'emerald' => ['label' => 'Hijau Zamrud (Emerald)', 'bg' => 'bg-emerald-500'],
                            'purple' => ['label' => 'Ungu (Purple)', 'bg' => 'bg-purple-500'],
                            'cyan' => ['label' => 'Cyan / Toska', 'bg' => 'bg-cyan-500'],
                            'indigo' => ['label' => 'Indigo / Biru Tua', 'bg' => 'bg-indigo-500'],
                        ];
                    @endphp
                    @foreach($colors as $key => $col)
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-slate-300 cursor-pointer transition has-[:checked]:border-slate-800 has-[:checked]:bg-slate-50">
                            <input type="radio" name="color_theme" value="{{ $key }}" {{ old('color_theme', $infoMenu->color_theme) == $key ? 'checked' : '' }} class="text-slate-800 focus:ring-0">
                            <span class="w-3.5 h-3.5 rounded-full {{ $col['bg'] }} shrink-0"></span>
                            <span class="text-xs font-bold text-slate-700">{{ $col['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Icon Selector -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Ikon FontAwesome <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-xl text-slate-800 border border-slate-200 shrink-0">
                        <i id="preview_icon" class="{{ old('icon', $infoMenu->icon) }}"></i>
                    </div>
                    <input type="text" name="icon" id="icon_input" value="{{ old('icon', $infoMenu->icon) }}" required oninput="document.getElementById('preview_icon').className = this.value" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-sm font-semibold transition">
                </div>
                <!-- Quick Icon Buttons -->
                <div class="flex items-center gap-2 flex-wrap pt-1 text-xs">
                    <span class="text-slate-400 text-[11px]">Rekomendasi ikon:</span>
                    <button type="button" onclick="setIcon('fa-solid fa-file-shield')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-solid fa-file-shield mr-1"></i> Surat Izin</button>
                    <button type="button" onclick="setIcon('fa-solid fa-envelope-open-text')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-solid fa-envelope-open-text mr-1"></i> Undangan</button>
                    <button type="button" onclick="setIcon('fa-solid fa-book-bookmark')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-solid fa-book-bookmark mr-1"></i> Juklak</button>
                    <button type="button" onclick="setIcon('fa-solid fa-table-list')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-solid fa-table-list mr-1"></i> Grid Nilai</button>
                    <button type="button" onclick="setIcon('fa-solid fa-map-location-dot')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-solid fa-map-location-dot mr-1"></i> Lokasi</button>
                    <button type="button" onclick="setIcon('fa-solid fa-book-open-reader')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-solid fa-book-open-reader mr-1"></i> Buku Panduan</button>
                    <button type="button" onclick="setIcon('fa-solid fa-photo-film')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-solid fa-photo-film mr-1"></i> Dokumentasi</button>
                    <button type="button" onclick="setIcon('fa-brands fa-whatsapp')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-brands fa-whatsapp mr-1 text-emerald-600"></i> WhatsApp</button>
                    <button type="button" onclick="setIcon('fa-solid fa-award')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-solid fa-award mr-1"></i> Sertifikat</button>
                    <button type="button" onclick="setIcon('fa-solid fa-circle-info')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer"><i class="fa-solid fa-circle-info mr-1"></i> Info</button>
                </div>
            </div>
        </div>

        <!-- Section 3: Aksi Tombol & Berkas -->
        <div class="border-b border-slate-100 pb-6 space-y-5">
            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-arrow-pointer text-sky-500"></i> 3. Aksi Tombol & Berkas / Tautan
            </h3>

            <!-- Action Type Selection -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Tipe Aksi Tombol <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 cursor-pointer transition has-[:checked]:border-pmr-primary has-[:checked]:bg-red-50/50 flex flex-col justify-between">
                        <div class="flex items-center gap-2 font-bold text-xs text-slate-800">
                            <input type="radio" name="action_type" value="file" {{ old('action_type', $infoMenu->action_type) == 'file' ? 'checked' : '' }} onchange="toggleActionFields()" class="text-pmr-primary focus:ring-0">
                            <span>Unggah Berkas</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Upload file PDF/Word/Excel untuk diunduh langsung pengunjung.</p>
                    </label>

                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 cursor-pointer transition has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50/50 flex flex-col justify-between">
                        <div class="flex items-center gap-2 font-bold text-xs text-slate-800">
                            <input type="radio" name="action_type" value="link" {{ old('action_type', $infoMenu->action_type) == 'link' ? 'checked' : '' }} onchange="toggleActionFields()" class="text-sky-500 focus:ring-0">
                            <span>Tautan Website / URL</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Membuka tautan luar (Google Maps, Instagram, Google Drive, dll).</p>
                    </label>

                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 cursor-pointer transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 flex flex-col justify-between">
                        <div class="flex items-center gap-2 font-bold text-xs text-slate-800">
                            <input type="radio" name="action_type" value="whatsapp" {{ old('action_type', $infoMenu->action_type) == 'whatsapp' ? 'checked' : '' }} onchange="toggleActionFields()" class="text-emerald-500 focus:ring-0">
                            <span>Chat WhatsApp</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Membuka chat WhatsApp panitia dengan pesan pembuka otomatis.</p>
                    </label>

                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 cursor-pointer transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/50 flex flex-col justify-between">
                        <div class="flex items-center gap-2 font-bold text-xs text-slate-800">
                            <input type="radio" name="action_type" value="notice" {{ old('action_type', $infoMenu->action_type) == 'notice' ? 'checked' : '' }} onchange="toggleActionFields()" class="text-amber-500 focus:ring-0">
                            <span>Sedang Disiapkan</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Menampilkan modal notifikasi bahwa dokumen sedang difinalisasi.</p>
                    </label>
                </div>
            </div>

            <!-- Field for File Upload -->
            <div id="field_file_upload" class="space-y-3 p-4.5 rounded-2xl bg-slate-50 border border-slate-200">
                @if($infoMenu->file_path)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-base">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">{{ basename($infoMenu->file_path) }}</div>
                                <a href="{{ asset('storage/' . $infoMenu->file_path) }}" target="_blank" class="text-[11px] text-pmr-primary hover:underline font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka / Unduh Berkas Ini
                                </a>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 text-xs font-bold text-rose-600 cursor-pointer">
                            <input type="checkbox" name="remove_file" value="1" class="rounded text-rose-600 focus:ring-0">
                            <span>Hapus Berkas Ini</span>
                        </label>
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        {{ $infoMenu->file_path ? 'Ganti Berkas dengan yang Baru (Opsional)' : 'Unggah Berkas Dokumen (PDF, DOC, XLS, ZIP, Gambar)' }}
                    </label>
                    <input type="file" name="file_upload" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-pmr-primary file:text-white hover:file:bg-pmr-dark file:cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Maksimal 20 MB. Format PDF sangat disarankan.</p>
                </div>
            </div>

            <!-- Field for URL -->
            <div id="field_url_link" class="space-y-1.5 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <label class="block text-xs font-bold text-slate-700">Alamat Tautan (URL / Link)</label>
                <input type="text" name="url_link" value="{{ old('url_link', $infoMenu->url_link) }}" placeholder="https://maps.google.com/... atau https://wa.me/62813..." class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-sm font-medium transition">
                <p class="text-[11px] text-slate-400 mt-1">Pastikan diawali dengan <code>https://</code> atau <code>http://</code>.</p>
            </div>

            <!-- Field for 3 Contact Persons (PMR Mula, PMR Madya, PMR Wira) -->
            @php
                $contacts = $infoMenu->contacts_list;
                $mula = collect($contacts)->firstWhere('level', 'Mula') ?? ['name' => 'Kak Panitia Mula', 'phone' => '081383885600', 'role' => 'Koordinator PMR Mula (SD/MI)'];
                $madya = collect($contacts)->firstWhere('level', 'Madya') ?? ['name' => 'Kak Panitia Madya', 'phone' => '081383885600', 'role' => 'Koordinator PMR Madya (SMP/MTs)'];
                $wira = collect($contacts)->firstWhere('level', 'Wira') ?? ['name' => 'Kak Panitia Wira', 'phone' => '081383885600', 'role' => 'Koordinator PMR Wira (SMA/SMK/MA)'];
            @endphp
            <div id="field_contacts_data" class="space-y-4 p-5 rounded-3xl bg-emerald-50/70 border-2 border-emerald-300">
                <div class="flex items-center gap-2.5 text-emerald-950 font-black text-sm">
                    <i class="fa-brands fa-whatsapp text-emerald-600 text-xl"></i>
                    <span>Daftar 3 Narahubung Contact Person (PMR Mula, Madya, dan Wira)</span>
                </div>
                <p class="text-xs text-emerald-800 leading-relaxed">
                    Pengunjung lomba dapat memilih salah satu narahubung sesuai tingkatan PMR sekolah mereka untuk berkonsultasi langsung via WhatsApp.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                    <!-- 1. PMR MULA -->
                    <div class="p-4 rounded-2xl bg-white border-2 border-emerald-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                🟢 PMR Mula (SD/MI)
                            </span>
                            <input type="hidden" name="contacts_data[0][level]" value="Mula">
                            <input type="hidden" name="contacts_data[0][color]" value="emerald">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">Nama Narahubung</label>
                            <input type="text" name="contacts_data[0][name]" value="{{ old('contacts_data.0.name', $mula['name'] ?? '') }}" placeholder="Contoh: Kak Siti / Kak Ahmad" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">No. WhatsApp / HP</label>
                            <input type="text" name="contacts_data[0][phone]" value="{{ old('contacts_data.0.phone', $mula['phone'] ?? '') }}" placeholder="081383885600" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">Keterangan / Jabatan</label>
                            <input type="text" name="contacts_data[0][role]" value="{{ old('contacts_data.0.role', $mula['role'] ?? 'Koordinator PMR Mula (SD/MI)') }}" placeholder="Koordinator PMR Mula" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <!-- 2. PMR MADYA -->
                    <div class="p-4 rounded-2xl bg-white border-2 border-blue-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                🔵 PMR Madya (SMP/MTs)
                            </span>
                            <input type="hidden" name="contacts_data[1][level]" value="Madya">
                            <input type="hidden" name="contacts_data[1][color]" value="blue">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">Nama Narahubung</label>
                            <input type="text" name="contacts_data[1][name]" value="{{ old('contacts_data.1.name', $madya['name'] ?? '') }}" placeholder="Contoh: Kak Budi / Kak Rina" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">No. WhatsApp / HP</label>
                            <input type="text" name="contacts_data[1][phone]" value="{{ old('contacts_data.1.phone', $madya['phone'] ?? '') }}" placeholder="081383885600" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">Keterangan / Jabatan</label>
                            <input type="text" name="contacts_data[1][role]" value="{{ old('contacts_data.1.role', $madya['role'] ?? 'Koordinator PMR Madya (SMP/MTs)') }}" placeholder="Koordinator PMR Madya" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-blue-500">
                        </div>
                    </div>

                    <!-- 3. PMR WIRA -->
                    <div class="p-4 rounded-2xl bg-white border-2 border-amber-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                🟡 PMR Wira (SMA/SMK/MA)
                            </span>
                            <input type="hidden" name="contacts_data[2][level]" value="Wira">
                            <input type="hidden" name="contacts_data[2][color]" value="amber">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">Nama Narahubung</label>
                            <input type="text" name="contacts_data[2][name]" value="{{ old('contacts_data.2.name', $wira['name'] ?? '') }}" placeholder="Contoh: Kak Dika / Kak Maya" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:border-amber-500 focus:ring-1 focus:ring-amber-200">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">No. WhatsApp / HP</label>
                            <input type="text" name="contacts_data[2][phone]" value="{{ old('contacts_data.2.phone', $wira['phone'] ?? '') }}" placeholder="081383885600" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:border-amber-500 focus:ring-1 focus:ring-amber-200">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700">Keterangan / Jabatan</label>
                            <input type="text" name="contacts_data[2][role]" value="{{ old('contacts_data.2.role', $wira['role'] ?? 'Koordinator PMR Wira (SMA/SMK/MA)') }}" placeholder="Koordinator PMR Wira" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-amber-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Text -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Teks Label Pada Tombol <span class="text-rose-500">*</span></label>
                <input type="text" name="button_text" id="button_text" value="{{ old('button_text', $infoMenu->button_text) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-sm font-semibold transition">
            </div>
        </div>

        <!-- Section 4: Urutan & Status -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
            <div class="flex items-center gap-6">
                <!-- Order -->
                <div class="space-y-1 w-32">
                    <label class="block text-xs font-bold text-slate-700">Nomor Urutan</label>
                    <input type="number" name="order_position" value="{{ old('order_position', $infoMenu->order_position) }}" min="1" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary text-sm font-bold text-center">
                </div>

                <!-- Is Active -->
                <label class="flex items-center gap-3 cursor-pointer pt-4">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $infoMenu->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded text-pmr-primary focus:ring-0">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Tampilkan Menu Ini</div>
                        <div class="text-[11px] text-slate-400">Aktifkan agar muncul di halaman depan</div>
                    </div>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-3 pt-4 sm:pt-0">
                <a href="{{ route('admin.competition-info-menus.index') }}" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-pmr-primary hover:bg-pmr-dark text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </div>

    </form>

</div>

<script>
    function setBadge(val) {
        document.getElementById('category_badge').value = val;
    }

    function setIcon(val) {
        document.getElementById('icon_input').value = val;
        document.getElementById('preview_icon').className = val;
    }

    function toggleActionFields() {
        const actionType = document.querySelector('input[name="action_type"]:checked')?.value || 'file';
        const fileBox = document.getElementById('field_file_upload');
        const urlBox = document.getElementById('field_url_link');
        const contactsBox = document.getElementById('field_contacts_data');

        if (actionType === 'file') {
            if (fileBox) fileBox.style.display = 'block';
            if (urlBox) urlBox.style.display = 'none';
            if (contactsBox) contactsBox.style.display = 'none';
        } else if (actionType === 'link') {
            if (fileBox) fileBox.style.display = 'none';
            if (urlBox) urlBox.style.display = 'block';
            if (contactsBox) contactsBox.style.display = 'none';
        } else if (actionType === 'whatsapp') {
            if (fileBox) fileBox.style.display = 'none';
            if (urlBox) urlBox.style.display = 'none';
            if (contactsBox) contactsBox.style.display = 'block';
        } else {
            if (fileBox) fileBox.style.display = 'none';
            if (urlBox) urlBox.style.display = 'none';
            if (contactsBox) contactsBox.style.display = 'none';
        }
    }

    // Run on load
    document.addEventListener('DOMContentLoaded', toggleActionFields);
</script>
@endsection
