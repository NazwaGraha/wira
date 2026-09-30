@extends('layouts.app')

@section('title', 'Kontak & Pendaftaran Relawan')

@section('content')
<!-- Page Header -->
<section class="gradient-pmr text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-3">Hubungi Kami & Pendaftaran Relawan</h1>
        <p class="text-red-100 text-sm sm:text-base max-w-2xl mx-auto">
            Saluran informasi resmi PMR Wira SMAN 1 Ciawi dan formulir online pendaftaran anggota baru masa bakti Ragana Dwi Pantara 2026/2027.
        </p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Left Column: Contact Cards + Location (6 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Informasi Kontak Sekretariat</h2>
            <p class="text-xs text-slate-500 mb-6">Silakan hubungi kami untuk koordinasi kegiatan, peliputan, atau informasi pendaftaran.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Card 1 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-800">Sekretariat PMR</div>
                        <div class="text-[11px] text-slate-500 mt-1 leading-snug">
                            Ruang UKS SMAN 1 Ciawi, Jl. Veteran No. 46, Pandansari, Bogor.
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-800">WhatsApp Hotline</div>
                        <div class="text-[11px] text-slate-500 mt-1">
                            +62 813-8388-5600
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-brands fa-instagram"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-800">Instagram Resmi</div>
                        <a href="https://instagram.com/pmrwirasman1c" target="_blank" class="text-[11px] text-pmr-primary font-semibold hover:underline mt-1 block">
                            @pmrwirasman1c
                        </a>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-800">Email Resmi</div>
                        <div class="text-[11px] text-slate-500 mt-1">
                            pmrwira@sman1ciawi.sch.id
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Mockup Card -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-pmr-primary"></i> Peta Lokasi Kampus
                </div>
                <div class="rounded-2xl overflow-hidden h-48 bg-slate-200 relative">
                    <iframe class="w-full h-full border-0" 
                            src="https://maps.google.com/maps?q=SMAN%201%20Ciawi%20Bogor&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                            loading="lazy"></iframe>
                </div>
                <div class="mt-3 flex justify-between items-center text-[11px] text-slate-500">
                    <span><strong>Jam Piket Medis UKS:</strong> Senin &mdash; Jumat (07.00 - 15.30 WIB)</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Registration Form (7 Cols - Matching Mockup 06) -->
        <div id="daftar" class="lg:col-span-7">
            <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl border border-slate-200/90">
                <div class="inline-flex items-center gap-2 bg-red-100 text-pmr-primary font-bold text-xs px-3.5 py-1.5 rounded-full uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-user-plus"></i> Formulir Rekrutmen
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight mb-2">
                    Pendaftaran Anggota Baru 2026/2027
                </h2>
                <p class="text-xs text-slate-500 mb-8 leading-relaxed">
                    Khusus bagi siswa/siswi kelas X & XI SMAN 1 Ciawi yang berjiwa sosial dan ingin mengembangkan keahlian pertolongan pertama serta kepemimpinan.
                </p>

                <form action="{{ route('daftar-anggota.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap Siswa</label>
                        <input type="text" name="full_name" required placeholder="Contoh: Muhammad Fauzan" 
                               value="{{ old('full_name') }}"
                               class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kelas / Jurusan</label>
                            <input type="text" name="class_grade" required placeholder="Contoh: X-MIPA 3" 
                                   value="{{ old('class_grade') }}"
                                   class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">NISN (Nomor Induk)</label>
                            <input type="text" name="nisn" placeholder="008xxxxxxxx" 
                                   value="{{ old('nisn') }}"
                                   class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp Aktif</label>
                            <input type="tel" name="phone" required placeholder="08xxxxxxxxxx" 
                                   value="{{ old('phone') }}"
                                   class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email (Opsional)</label>
                            <input type="email" name="email" placeholder="nama@gmail.com" 
                                   value="{{ old('email') }}"
                                   class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilihan Peminatan Bidang</label>
                        <select name="interest_field" required class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                            <option value="Pertolongan Pertama (PP)">Pertolongan Pertama (PP) & Medis Lapangan</option>
                            <option value="Kesiapsiagaan Bencana">Kesiapsiagaan Bencana & Evakuasi (PRS)</option>
                            <option value="Donor Darah & Kesehatan">Donor Darah Sukarela & Promosi Kesehatan</option>
                            <option value="Humas & Media Publikasi">Humas, Jurnalistik Kemanusiaan & Dokumentasi</option>
                            <option value="Kepemimpinan & Logistik">Kepemimpinan, Dapur Umum & Logistik Darurat</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alasan / Motivasi Bergabung</label>
                        <textarea name="motivation" required rows="3" placeholder="Ceritakan motivasi Anda ingin menjadi relawan PMR Wira..." 
                                  class="w-full text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">{{ old('motivation') }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-sm uppercase tracking-wider py-4 rounded-xl shadow-xl transition active:scale-98 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Formulir Pendaftaran
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- FAQ Section -->
    <div class="mt-20">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h3 class="text-2xl font-extrabold text-slate-900">Pertanyaan Seputar Rekrutmen Anggota</h3>
            <p class="text-xs text-slate-500 mt-1">Semua hal yang perlu Anda ketahui sebelum menjadi bagian dari keluarga besar PMR Wira.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-pmr-primary font-bold text-sm mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question"></i> Apakah ada biaya pendaftaran?
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Tidak ada biaya sama sekali. Seluruh proses pendaftaran dan pembinaan dasar PMR Wira SMAN 1 Ciawi bersifat gratis (bebas biaya).
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-pmr-primary font-bold text-sm mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question"></i> Kapan jadwal latihan rutin diadakan?
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Latihan rutin ekstrakurikuler diadakan setiap hari Jumat pukul 14.30 &mdash; 16.30 WIB di lingkungan kampus SMAN 1 Ciawi.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-pmr-primary font-bold text-sm mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question"></i> Apakah pemula tanpa dasar medis boleh ikut?
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Tentu saja! Semua anggota baru akan dibimbing dari dasar, mulai dari pengenalan anatomi dasar, teknik pembalutan mitela, hingga evakuasi tandu.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
