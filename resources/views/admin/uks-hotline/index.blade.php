@extends('layouts.admin')

@section('title', 'Pengaturan Hotline & Siaga UKS')
@section('page_title', 'Hotline & Siaga Piket Medis UKS')

@section('content')
<div class="space-y-6">

    <!-- Header & Info Banner -->
    <div class="bg-gradient-to-r from-red-900 via-pmr-primary to-stone-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-red-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-3.5 py-1 rounded-full text-xs font-bold text-amber-300 uppercase tracking-widest border border-white/20">
                    <i class="fa-solid fa-truck-medical text-amber-300"></i> Layanan Tanggap Darurat & Pertolongan Pertama
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Pengaturan Hotline & Piket Medis UKS
                </h1>
                <p class="text-red-100 text-xs sm:text-sm leading-relaxed">
                    Kontak cepat darurat yang tampil di bar atas (*top header*) website untuk memudahkan siswa, guru, dan warga sekolah memanggil tim medis PMR saat terjadi kondisi darurat kesehatan, pingsan saat upacara, atau cedera olahraga di lingkungan SMAN 1 Ciawi.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <form action="{{ route('admin.uks-hotline.toggle') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-2xl {{ $hotline->is_active ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-stone-700 hover:bg-stone-600' }} text-white text-xs font-black uppercase tracking-wider transition shadow-lg flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $hotline->is_active ? 'bg-white animate-pulse' : 'bg-stone-400' }}"></span>
                        <span>{{ $hotline->is_active ? 'Status: Siaga Aktif' : 'Status: Non-Aktif / Tutup' }}</span>
                    </button>
                </form>

                <a href="{{ route('home') }}" target="_blank" class="px-4 py-2.5 rounded-2xl bg-white/15 hover:bg-white text-white hover:text-stone-900 text-xs font-black uppercase tracking-wider transition border border-white/30 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Pratinjau Live</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content: Form & Live Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Form Settings (8 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('admin.uks-hotline.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Card 1: Informasi Kontak Utama -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg flex-shrink-0 font-bold">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Kontak Cepat & Identitas Layanan</h2>
                            <p class="text-xs text-slate-500">Nomor telepon dan nama badge yang tampil di bar atas.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nomor HP / WhatsApp -->
                        <div class="sm:col-span-2">
                            <label for="phone_number" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nomor Telepon / WhatsApp Piket <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 font-bold text-xs">
                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                                </span>
                                <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $hotline->phone_number) }}" required
                                       placeholder="Contoh: 083872506817"
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm font-bold text-slate-800 transition">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Gunakan nomor aktif petugas piket UKS atau koordinator medis yang mudah dihubungi.</p>
                        </div>

                        <!-- Badge Label -->
                        <div>
                            <label for="label" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Label Badge <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="label" name="label" value="{{ old('label', $hotline->label) }}" required
                                   placeholder="Contoh: Hotline UKS"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm font-semibold text-slate-800 transition">
                        </div>

                        <!-- Nama Layanan -->
                        <div>
                            <label for="service_name" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Subjudul Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="service_name" name="service_name" value="{{ old('service_name', $hotline->service_name) }}" required
                                   placeholder="Contoh: Piket Medis & P3K Sekolah"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm font-semibold text-slate-800 transition">
                        </div>

                        <!-- Petugas Bertugas -->
                        <div class="sm:col-span-2">
                            <label for="officer_name" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Petugas / Penanggung Jawab Siaga
                            </label>
                            <input type="text" id="officer_name" name="officer_name" value="{{ old('officer_name', $hotline->officer_name) }}"
                                   placeholder="Contoh: Tim Relawan PMR Wira SMAN 1 Ciawi"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm font-semibold text-slate-800 transition">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Jam Operasional, Lokasi, dan Status -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0 font-bold">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Jadwal Piket & Pos Medis</h2>
                            <p class="text-xs text-slate-500">Waktu pelayanan dan lokasi penanganan pertama.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Jam Operasional -->
                        <div>
                            <label for="operational_hours" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jam Operasional <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="operational_hours" name="operational_hours" value="{{ old('operational_hours', $hotline->operational_hours) }}" required
                                   placeholder="Contoh: Senin - Jumat (07.00 - 16.00 WIB)"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm font-semibold text-slate-800 transition">
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="location" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Lokasi Pos UKS <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="location" name="location" value="{{ old('location', $hotline->location) }}" required
                                   placeholder="Contoh: Ruang UKS SMAN 1 Ciawi"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm font-semibold text-slate-800 transition">
                        </div>

                        <!-- Teks Status -->
                        <div>
                            <label for="status_text" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Teks Status Siaga <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="status_text" name="status_text" value="{{ old('status_text', $hotline->status_text) }}" required
                                   placeholder="Contoh: Siaga di Jam Sekolah"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm font-semibold text-slate-800 transition">
                        </div>

                        <!-- Toggle Status Aktif -->
                        <div class="flex items-center pt-6">
                            <label class="relative flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $hotline->is_active) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                <span class="text-xs font-bold text-slate-800">Aktifkan Status Siaga Medis</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pesan Otomatis WhatsApp & Penjelasan Layanan -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0 font-bold">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Format Pesan Otomatis & Deskripsi</h2>
                            <p class="text-xs text-slate-500">Teks pembuka saat tombol WhatsApp ditekan oleh pengunjung.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="whatsapp_message" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Format Pesan Cepat WhatsApp
                            </label>
                            <textarea id="whatsapp_message" name="whatsapp_message" rows="2"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm text-slate-800 transition">{{ old('whatsapp_message', $hotline->whatsapp_message) }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1">Pesan ini otomatis terisi di kolom chat WhatsApp ketika siswa/guru mengeklik nomor hotline.</p>
                        </div>

                        <div>
                            <label for="description" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                Penjelasan Layanan Darurat UKS
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm text-slate-800 transition">{{ old('description', $hotline->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" class="px-7 py-3 rounded-2xl bg-pmr-primary hover:bg-red-800 text-white font-extrabold text-xs sm:text-sm shadow-xl hover:shadow-red-950/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Pengaturan Hotline UKS</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Preview & FAQ (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card Preview Tampilan Live -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Pratinjau di Header Web</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400">Desktop & Mobile</span>
                </div>

                <!-- Preview Header Strip -->
                <div class="rounded-2xl overflow-hidden border border-stone-800 shadow-md">
                    <div class="bg-stone-900 text-white text-[11px] py-2.5 px-3 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="bg-pmr-primary text-white font-black px-2 py-0.5 rounded text-[9px] tracking-wider uppercase flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full {{ $hotline->is_active ? 'bg-emerald-300 animate-pulse' : 'bg-stone-400' }}"></span>
                                {{ $hotline->label }}
                            </span>
                            <span class="text-stone-300 text-xs truncate">
                                {{ $hotline->service_name }}: <strong class="text-white hover:text-red-300 transition">{{ $hotline->formatted_phone }}</strong>
                            </span>
                        </div>
                        <span class="text-[9px] text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20 hidden sm:inline">
                            {{ $hotline->status_text }}
                        </span>
                    </div>
                </div>

                <!-- Test Direct Action Buttons -->
                <div class="space-y-2 pt-2">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Uji Tautan Langsung:</div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ $hotline->whatsapp_url }}" target="_blank" class="p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-center gap-2 transition">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i>
                            <span>Tes WhatsApp</span>
                        </a>
                        <a href="{{ $hotline->tel_url }}" class="p-3 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-800 text-xs font-bold flex items-center justify-center gap-2 transition">
                            <i class="fa-solid fa-phone text-blue-600 text-xs"></i>
                            <span>Tes Telepon</span>
                        </a>
                    </div>
                </div>

                <!-- Detail Card Informasi Ruang UKS -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5 text-xs">
                    <div class="font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-pmr-primary"></i>
                        <span>Informasi Siaga UKS</span>
                    </div>
                    <div class="space-y-1.5 text-slate-600">
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-slate-400 w-3.5 mt-0.5"></i>
                            <span><strong>Lokasi:</strong> {{ $hotline->location }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-clock text-slate-400 w-3.5 mt-0.5"></i>
                            <span><strong>Jam Kerja:</strong> {{ $hotline->operational_hours }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-user-shield text-slate-400 w-3.5 mt-0.5"></i>
                            <span><strong>Penanggung Jawab:</strong> {{ $hotline->officer_name ?: 'PMR Wira SMAN 1 Ciawi' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Edukasi: Apa Maksud & Fungsinya? -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50/50 rounded-3xl p-6 border border-amber-200/80 shadow-2xs space-y-3">
                <div class="flex items-center gap-2.5 text-amber-900 font-extrabold text-sm">
                    <i class="fa-solid fa-circle-question text-amber-600 text-base"></i>
                    <span>Apa Maksud & Fungsi Hotline UKS?</span>
                </div>
                <div class="text-xs text-amber-950/80 leading-relaxed space-y-2 font-medium">
                    <p>
                        <strong>1. Pertolongan Pertama Cepat:</strong> Saat upacara bendera, jam olahraga, atau KBM, jika ada siswa atau guru yang pingsan/sakit/cedera, warga sekolah dapat langsung menghubungi nomor ini untuk pemanggilan tim medis & tandu.
                    </p>
                    <p>
                        <strong>2. Siaga Kegiatan Sekolah:</strong> Nomor hotline dapat dialihkan ke koordinator medis saat sekolah mengadakan kegiatan besar (seperti Lomba PMR, Classmeeting, Pentas Seni, atau Kamping Pramuka).
                    </p>
                    <p>
                        <strong>3. Akses Langsung Satu Sentuhan:</strong> Nomor ini kini dapat diklik langsung di smartphone untuk membuka panggilan telepon darurat atau obrolan WhatsApp siap kirim.
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
