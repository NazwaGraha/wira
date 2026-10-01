@extends('layouts.app')

@section('title', 'Layanan & Info Donor Darah')

@section('content')
<!-- Hero Section (Matching Mockup 05) -->
<section class="gradient-pmr text-white py-20 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
        <div class="lg:col-span-8">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-1 rounded-full text-xs font-bold text-red-100 uppercase tracking-widest mb-4">
                <i class="fa-solid fa-heart-pulse text-red-300"></i> Aksi Kemanusiaan Berkelanjutan
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight mb-4">
                Layanan & Info Donor Darah Sukarela
            </h1>
            <p class="text-lg text-red-100 max-w-2xl mb-8 leading-relaxed">
                Satu Kantong Darah Anda, Sejuta Harapan Bagi Sesama. Bersama PMR Wira SMAN 1 Ciawi dan UDD PMI Kabupaten Bogor, wujudkan kepedulian nyata.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#daftar-donor" class="bg-white text-pmr-primary hover:bg-red-50 font-bold px-8 py-3.5 rounded-full shadow-xl text-sm uppercase tracking-wider transition flex items-center gap-2">
                    <i class="fa-solid fa-droplet text-red-600"></i> Ayo Donor Sekarang!
                </a>
                <a href="#jadwal" class="bg-black/30 border border-white/40 text-white hover:bg-white/10 font-bold px-7 py-3.5 rounded-full text-sm uppercase tracking-wider transition">
                    Lihat Jadwal Terdekat
                </a>
            </div>
        </div>

        <div class="lg:col-span-4 hidden lg:block">
            <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white/20 bg-slate-900">
                <img src="/mockups/05_donor_darah.jpg" alt="Ayo Donor Darah" class="w-full h-auto object-cover">
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Left Side: Jadwal + Syarat + Stok Darah (7 Cols) -->
        <div class="lg:col-span-7 space-y-12">
            
            <!-- Jadwal Donor Darah Terdekat Card with Countdown -->
            <div id="jadwal" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200">
                @if($activeEvent)
                    <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
                        <div>
                            <div class="text-xs font-bold text-pmr-primary uppercase tracking-wider">Agenda Kegiatan</div>
                            <h3 class="text-2xl font-extrabold text-slate-900">{{ $activeEvent->title }}</h3>
                        </div>
                        <span class="bg-red-100 text-pmr-primary text-xs font-extrabold px-3.5 py-1.5 rounded-full uppercase">
                            Terbuka Umum
                        </span>
                    </div>

                    @if($activeEvent->banner_image)
                        <div class="mb-6 rounded-2xl overflow-hidden shadow-sm border border-slate-200">
                            <img src="{{ Storage::url($activeEvent->banner_image) }}" alt="Banner Event" class="w-full h-auto object-cover">
                        </div>
                    @endif

                    <!-- Countdown Timer Display -->
                    <div class="grid grid-cols-4 gap-3 bg-red-50/70 p-5 rounded-2xl border border-red-100 text-center mb-6" id="donor-countdown" data-target="{{ \Carbon\Carbon::parse($activeEvent->event_date->format('Y-m-d') . ' ' . $activeEvent->time_start)->format('Y-m-d\TH:i:s') }}">
                        <div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-pmr-primary" id="cd-days">00</div>
                            <div class="text-[11px] font-bold text-slate-600 uppercase mt-0.5">Hari</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-pmr-primary" id="cd-hours">00</div>
                            <div class="text-[11px] font-bold text-slate-600 uppercase mt-0.5">Jam</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-pmr-primary" id="cd-minutes">00</div>
                            <div class="text-[11px] font-bold text-slate-600 uppercase mt-0.5">Menit</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-pmr-primary" id="cd-seconds">00</div>
                            <div class="text-[11px] font-bold text-slate-600 uppercase mt-0.5">Detik</div>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm text-slate-600 border-b border-slate-100 pb-6 mb-6">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-location-dot text-red-500 w-5 text-center"></i>
                            <span><strong>Lokasi:</strong> {{ $activeEvent->location }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fa-regular fa-clock text-red-500 w-5 text-center"></i>
                            <span><strong>Waktu:</strong> Pukul {{ \Carbon\Carbon::parse($activeEvent->time_start)->format('H:i') }} &mdash; {{ $activeEvent->time_end ? \Carbon\Carbon::parse($activeEvent->time_end)->format('H:i') : 'Selesai' }} WIB</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-calendar-day text-red-500 w-5 text-center"></i>
                            <span><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($activeEvent->event_date)->translatedFormat('l, d F Y') }}</span>
                        </div>
                    </div>

                    @if($activeEvent->description)
                        <div class="prose prose-sm prose-slate max-w-none border-b border-slate-100 pb-6 mb-6">
                            {!! $activeEvent->description !!}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300 mb-4 block"></i>
                        <h3 class="text-xl font-bold text-slate-800">Belum Ada Jadwal Donor Darah Terdekat</h3>
                        <p class="text-sm text-slate-500 mt-2">Pantau terus halaman ini untuk informasi donor darah selanjutnya.</p>
                    </div>
                @endif

                <div class="flex items-center justify-between text-xs text-slate-500 mt-6">
                    <span>*Disediakan snack bergizi, vitamin, dan sertifikat partisipasi pendonor.</span>
                </div>
            </div>

            <!-- Syarat & Ketentuan Pendonor Darah (5 Infografis Cards) -->
            <div>
                <h3 class="text-2xl font-extrabold text-slate-900 mb-6">Syarat & Ketentuan Pendonor Darah</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary mx-auto flex items-center justify-center text-lg mb-2">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-800">1. Usia Minimal</div>
                        <div class="text-xs text-slate-500 mt-1">17 &mdash; 60 Tahun</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary mx-auto flex items-center justify-center text-lg mb-2">
                            <i class="fa-solid fa-weight-scale"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-800">2. Berat Badan</div>
                        <div class="text-xs text-slate-500 mt-1">Minimal 45 Kg</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary mx-auto flex items-center justify-center text-lg mb-2">
                            <i class="fa-solid fa-vial"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-800">3. Kadar Hb</div>
                        <div class="text-xs text-slate-500 mt-1">12,5 &mdash; 17,0 g/dL</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary mx-auto flex items-center justify-center text-lg mb-2">
                            <i class="fa-solid fa-bed"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-800">4. Istirahat Cukup</div>
                        <div class="text-xs text-slate-500 mt-1">Tidur Min. 5 Jam</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center col-span-2 sm:col-span-2">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-pmr-primary mx-auto flex items-center justify-center text-lg mb-2">
                            <i class="fa-solid fa-heart-pulse"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-800">5. Sehat Jasmani & Rohani</div>
                        <div class="text-xs text-slate-500 mt-1">Tidak sedang mengonsumsi obat keras/antibiotik dalam 3 hari terakhir</div>
                    </div>
                </div>
            </div>

            <!-- FAQ Accordion -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200">
                <h3 class="text-xl font-extrabold text-slate-900 mb-6">Pertanyaan Sering Diajukan (FAQ)</h3>
                <div class="space-y-4 text-sm">
                    <details class="group bg-slate-50 p-4 rounded-xl border border-slate-200" open>
                        <summary class="font-bold text-slate-800 cursor-pointer list-none flex justify-between items-center">
                            <span>Apa saja manfaat donor darah bagi tubuh?</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-slate-600 text-xs mt-2 leading-relaxed">
                            Donor darah menstimulasi produksi sel darah merah baru, membantu menjaga kesehatan organ jantung, serta mendeteksi kondisi kesehatan dini secara gratis.
                        </p>
                    </details>

                    <details class="group bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <summary class="font-bold text-slate-800 cursor-pointer list-none flex justify-between items-center">
                            <span>Apakah donor darah terasa sakit?</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-slate-600 text-xs mt-2 leading-relaxed">
                            Rasa sakit hanya terasa sesaat seperti gigitan semut ketika jarum steril dimasukkan ke pembuluh vena. Proses pengambilan darah berlangsung cepat sekitar 8-10 menit.
                        </p>
                    </details>

                    <details class="group bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <summary class="font-bold text-slate-800 cursor-pointer list-none flex justify-between items-center">
                            <span>Berapa interval waktu antar donor darah?</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-slate-600 text-xs mt-2 leading-relaxed">
                            Jarak minimal donor darah lengkap (whole blood) adalah 60 hari (2 bulan) sekali untuk memberi waktu tubuh membentuk cadangan zat besi yang cukup.
                        </p>
                    </details>
                </div>
            </div>

        </div>

        <!-- Right Side: Live Blood Stock Status + Form Pendaftaran (5 Cols) -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- Live Status Stok Kebutuhan Darah (Matching Mockup 05) -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-xs font-bold text-pmr-primary uppercase tracking-wider">Live Status</div>
                        <h3 class="text-xl font-extrabold text-slate-900">Ketersediaan & Stok Darah</h3>
                    </div>
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                </div>
                <p class="text-xs text-slate-500 mb-6">Status terkini ketersediaan darah PMI Kabupaten Bogor & Posko SMAN 1 Ciawi.</p>

                <div class="grid grid-cols-2 gap-4">
                    @forelse ($bloodStocks as $stock)
                        @php
                            $badgeClass = match($stock->status) {
                                'aman' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'menipis' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'kritis' => 'bg-rose-50 text-rose-700 border-rose-200',
                                default => 'bg-slate-50 text-slate-700 border-slate-200',
                            };
                            $badgeText = match($stock->status) {
                                'aman' => 'Stok Aman',
                                'menipis' => 'Menipis',
                                'kritis' => 'Sangat Butuh',
                                default => 'Normal',
                            };
                        @endphp
                        <div class="border rounded-2xl p-4 flex items-center justify-between {{ $badgeClass }}">
                            <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center font-extrabold text-xl text-pmr-primary">
                                {{ $stock->blood_type }}
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-extrabold uppercase">{{ $badgeText }}</div>
                                <div class="text-[11px] opacity-75">{{ $stock->bags_count }} Kantong</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center text-xs text-slate-400 py-4">Data stok sedang disinkronkan.</div>
                    @endforelse
                </div>
            </div>

            <!-- Form Pendaftaran Donor Darah Online -->
            <div id="daftar-donor" class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/90 relative overflow-hidden">
                <div class="inline-flex items-center gap-2 text-pmr-primary font-bold text-xs uppercase tracking-widest mb-2">
                    <i class="fa-solid fa-clipboard-user"></i> Registrasi Donor Darah
                </div>
                <h3 class="text-2xl font-extrabold text-slate-900 mb-2">Daftar Donor Online</h3>
                <p class="text-xs text-slate-500 mb-6">Pra-registrasi untuk mempercepat antrean pemeriksaan medis di lokasi acara.</p>

                @if($activeEvent && $activeEvent->is_registration_link_active)
                    @if($activeEvent->registration_link)
                        <div class="text-center py-8">
                            <i class="fa-solid fa-up-right-from-square text-4xl text-pmr-primary mb-4 block"></i>
                            <h4 class="font-bold text-slate-800 mb-2">Pendaftaran Melalui Link Eksternal</h4>
                            <p class="text-sm text-slate-500 mb-6">Silakan klik tombol di bawah ini untuk mengisi formulir pendaftaran.</p>
                            <a href="{{ $activeEvent->registration_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-sm uppercase tracking-wider px-8 py-3.5 rounded-xl shadow-lg transition active:scale-95">
                                Menuju Form Pendaftaran <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <form action="{{ route('donor-darah.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="blood_donation_event_id" value="{{ $activeEvent->id }}">

                            @if(session('success'))
                                <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl text-sm border border-emerald-200">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(isset($errors) && $errors->any())
                                <div class="bg-rose-50 text-rose-700 p-4 rounded-xl text-sm border border-rose-200">
                                    <ul class="list-disc pl-5">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap</label>
                                <input type="text" name="name" required placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp</label>
                                    <input type="tel" name="phone" required placeholder="0812xxxx" value="{{ old('phone') }}" class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Golongan Darah</label>
                                    <select name="blood_type" required class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                                        <option value="A" {{ old('blood_type') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('blood_type') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="AB" {{ old('blood_type') == 'AB' ? 'selected' : '' }}>AB</option>
                                        <option value="O" {{ old('blood_type') == 'O' ? 'selected' : '' }}>O</option>
                                        <option value="Belum Tahu" {{ old('blood_type') == 'Belum Tahu' ? 'selected' : '' }}>Belum Tahu</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Rhesus</label>
                                    <select name="rhesus" class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary bg-white">
                                        <option value="+" {{ old('rhesus') == '+' ? 'selected' : '' }}>Positif (+)</option>
                                        <option value="-" {{ old('rhesus') == '-' ? 'selected' : '' }}>Negatif (-)</option>
                                        <option value="" {{ old('rhesus') == '' ? 'selected' : '' }}>Tidak Tahu</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Terakhir Donor</label>
                                    <input type="date" name="last_donation_date" value="{{ old('last_donation_date') }}" class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 pt-2">
                                <input type="checkbox" required id="agree" class="mt-1 accent-pmr-primary rounded">
                                <label for="agree" class="text-xs text-slate-500 leading-snug">
                                    Saya menyatakan bahwa data yang saya masukkan adalah benar dan saya bersedia menjalani skrining kesehatan pra-donor.
                                </label>
                            </div>

                            <button type="submit" class="w-full bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider py-3.5 rounded-xl shadow-lg transition active:scale-98 flex items-center justify-center gap-2 mt-4">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Pendaftaran Donor
                            </button>
                        </form>
                    @endif
                @else
                    <div class="text-center py-10 bg-slate-50 rounded-2xl border border-slate-200">
                        <i class="fa-solid fa-lock text-3xl text-slate-400 mb-3 block"></i>
                        <h4 class="font-bold text-slate-700 mb-1">Pendaftaran Belum Dibuka</h4>
                        <p class="text-xs text-slate-500">Saat ini pendaftaran online untuk kegiatan donor darah sedang ditutup.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cdElement = document.getElementById('donor-countdown');
    if (!cdElement) return;

    const targetDateStr = cdElement.getAttribute('data-target');
    if (!targetDateStr) return;

    const targetDate = new Date(targetDateStr).getTime();

    const timer = setInterval(function() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance < 0) {
            clearInterval(timer);
            document.getElementById('cd-days').innerText = '00';
            document.getElementById('cd-hours').innerText = '00';
            document.getElementById('cd-minutes').innerText = '00';
            document.getElementById('cd-seconds').innerText = '00';
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('cd-days').innerText = days.toString().padStart(2, '0');
        document.getElementById('cd-hours').innerText = hours.toString().padStart(2, '0');
        document.getElementById('cd-minutes').innerText = minutes.toString().padStart(2, '0');
        document.getElementById('cd-seconds').innerText = seconds.toString().padStart(2, '0');
    }, 1000);
});
</script>
@endpush
@endsection
