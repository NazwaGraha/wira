@extends('layouts.app')

@section('title', 'Cek Status Pendaftaran Lomba PMR')

@section('content')
<div class="bg-slate-900 text-white pt-32 pb-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl font-extrabold">Cek Status & Unduh Dokumen</h1>
        <p class="text-slate-400 text-sm mt-2">
            Masukkan Kode Pendaftaran Anda untuk memeriksa status verifikasi, mengunduh e-Kwitansi resmi, dan mencetak Kartu Peserta.
        </p>

        <!-- Search Form -->
        <form action="{{ route('lomba.status') }}" method="GET" class="mt-8 max-w-md mx-auto flex gap-2">
            <input type="text" name="code" value="{{ $code }}" placeholder="Contoh: SBB-A1B2C3" required class="w-full bg-slate-800/80 border border-slate-700 text-white px-4 py-3 rounded-xl text-center font-mono font-bold tracking-widest text-base focus:outline-none focus:border-red-500 uppercase placeholder:normal-case placeholder:font-sans placeholder:font-normal placeholder:tracking-normal">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i> Cek
            </button>
        </form>
    </div>
</div>

<div class="bg-slate-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                <div class="text-sm font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-xl"></i>
                <div class="text-sm font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        @if($registration)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Header -->
                <div class="p-6 sm:p-8 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-slate-500 bg-slate-200 px-2 py-0.5 rounded">{{ $registration->registration_code }}</span>
                            @if($registration->created_at)
                                <span class="text-xs text-slate-400">&bull; {{ $registration->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                            @endif
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 mt-1 uppercase">{{ $registration->school_name }}</h2>
                        @php
                            $levelColorClass = match($registration->level) {
                                'Mula' => 'text-emerald-700 bg-emerald-100 border border-emerald-300',
                                'Madya' => 'text-blue-700 bg-blue-100 border border-blue-300',
                                default => 'text-amber-900 bg-amber-100 border border-amber-300',
                            };
                        @endphp
                        <div class="text-xs text-slate-600 font-semibold mt-1 flex items-center gap-2 flex-wrap">
                            <span>Tingkat: <span class="px-2.5 py-0.5 rounded-md font-black text-xs {{ $levelColorClass }}">PMR {{ $registration->level }}</span></span>
                            <span>&bull;</span>
                            <span>Periode: <span class="px-2 py-0.5 rounded-md font-bold text-xs {{ ($registration->registration_wave ?? 1) == 2 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">{{ ($registration->registration_wave ?? 1) == 2 ? ($registration->event->wave2_name ?: 'Gelombang 2') : ($registration->event->wave1_name ?: 'Gelombang 1') }}</span></span>
                            <span>&bull;</span>
                            <span>Pembina: {{ $registration->advisor_name }} ({{ $registration->advisor_phone }})</span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        @if($registration->status == 'verified')
                            <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2 rounded-xl font-extrabold text-sm shadow-sm">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i> TERVERIFIKASI / LUNAS
                            </div>
                        @elseif($registration->status == 'pending')
                            <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2 rounded-xl font-extrabold text-sm shadow-sm">
                                <i class="fa-solid fa-clock text-amber-500 text-base"></i> MENUNGGU VERIFIKASI
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-2 rounded-xl font-extrabold text-sm shadow-sm">
                                <i class="fa-solid fa-circle-xmark text-rose-500 text-base"></i> DITOLAK
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Content & Action Buttons -->
                <div class="p-6 sm:p-8 space-y-6">
                    @if($registration->status == 'verified')
                        <!-- Download Buttons Box -->
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                                    <i class="fa-solid fa-award text-emerald-600"></i> Pendaftaran Anda Resmi Diterima!
                                </h3>
                                <p class="text-xs text-slate-600 mt-1">
                                    Silakan unduh atau cetak dokumen berikut untuk dibawa pada saat Technical Meeting / Daftar Ulang.
                                </p>
                                @if($registration->advisor_email)
                                    <p class="text-[11px] text-emerald-800 font-semibold mt-1.5 flex items-center gap-1.5">
                                        <i class="fa-solid fa-envelope-circle-check text-emerald-600"></i> Salinan e-Kwitansi & Kartu Peserta juga telah dikirimkan ke email: <strong>{{ $registration->advisor_email }}</strong>
                                    </p>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2.5 w-full sm:w-auto">
                                <a href="{{ url('/lomba/kwitansi/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="flex-grow sm:flex-grow-0 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-receipt"></i> Cetak e-Kwitansi (PDF)
                                </a>
                                <a href="{{ url('/lomba/kartu-peserta/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="flex-grow sm:flex-grow-0 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-id-card"></i> Cetak Kartu Peserta
                                </a>
                            </div>
                        </div>
                    @elseif($registration->status == 'rejected')
                        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 text-rose-800">
                            <h3 class="font-bold text-sm flex items-center gap-2">
                                <i class="fa-solid fa-circle-exclamation text-rose-600"></i> Catatan Penolakan dari Panitia:
                            </h3>
                            <p class="text-xs mt-2 bg-white/70 p-3 rounded-xl border border-rose-100">
                                {{ $registration->rejection_reason ?? 'Bukti transfer tidak valid atau belum sesuai nominal.' }}
                            </p>
                            <p class="text-[11px] text-slate-500 mt-3">Silakan hubungi kontak panitia untuk konfirmasi perbaikan data.</p>
                        </div>
                    @else
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-amber-800 flex items-start gap-3">
                            <i class="fa-solid fa-hourglass-half text-amber-600 text-xl mt-0.5"></i>
                            <div>
                                <h3 class="font-bold text-sm">Sedang Dalam Pemeriksaan Panitia</h3>
                                <p class="text-xs text-amber-700 mt-1">
                                    Panitia sedang memeriksa kelengkapan data dan keaslian bukti pembayaran Anda. e-Kwitansi dan Kartu Peserta akan otomatis dapat diunduh begitu status diverifikasi.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- WhatsApp Group Official Banner -->
                    <div class="bg-gradient-to-r from-emerald-600 to-green-700 text-white rounded-2xl p-5 shadow-md flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center text-white text-2xl shrink-0">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div>
                                <span class="bg-white/20 text-white text-[10px] font-black uppercase px-2 py-0.5 rounded tracking-wider">
                                    Wadah Resmi Peserta PMR {{ $registration->level }}
                                </span>
                                <h4 class="font-extrabold text-white text-sm sm:text-base mt-0.5">
                                    Gabung Grup WhatsApp Peserta Tingkat {{ $registration->level }}
                                </h4>
                                <p class="text-xs text-emerald-100 mt-0.5">
                                    Wajib diikuti oleh pembina / pendamping untuk update informasi Technical Meeting (TM) & jadwal tampil.
                                </p>
                            </div>
                        </div>
                        <a href="{{ $registration->whatsapp_group_url }}" target="_blank" class="w-full sm:w-auto shrink-0 bg-white hover:bg-emerald-50 text-emerald-800 font-extrabold text-xs px-5 py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i> Join Grup WhatsApp &rarr;
                        </a>
                    </div>

                    @if($registration->status == 'verified')
                        @php
                            $parkingTickets = $registration->parkingTickets;
                            $hasTickets = $parkingTickets->isNotEmpty();
                            $criteria = $registration->parking_priority_criteria ?? [];
                            $isTopPriority = $criteria['is_top_priority'] ?? ($parkingTickets->first()?->location?->is_priority ?? false);
                            $assignedLocation = $parkingTickets->first()?->location ?? null;
                            $assignedLocName = $assignedLocation?->name ?? ($criteria['assigned_location_name'] ?? null);
                            $r4Remaining = $registration->remaining_quota_r4;
                            $r2Remaining = $registration->remaining_quota_r2;
                            $r4Allocated = $registration->allocated_quota_r4;
                            $r2Allocated = $registration->allocated_quota_r2;
                            $hasRemainingQuota = ($r4Remaining + $r2Remaining) > 0;
                            // Otomatis buka modal popup jika belum pernah mendaftarkan tiket
                            $autoOpenModal = $parkingTickets->isEmpty() && !session()->has('hide_vehicle_modal');
                        @endphp

                        <!-- ================= SECTION: STATUS TIKET PARKIR & ALOKASI PRIORITAS ================= -->
                        <div x-data="parkingRegistrationApp({
                            autoOpen: {{ $autoOpenModal ? 'true' : 'false' }},
                            r4Remaining: {{ $r4Remaining }},
                            r2Remaining: {{ $r2Remaining }}
                        })" class="space-y-4">

                            @if($hasTickets)
                                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-6">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded border border-rose-100">
                                                FASILITAS PARKIR KONTINGEN RESMI
                                            </span>
                                            <h3 class="font-black text-lg text-slate-900 mt-1 flex items-center gap-2">
                                                <i class="fa-solid fa-square-parking text-rose-600"></i> Status Tiket Parkir & Alokasi Prioritas
                                            </h3>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                Tunjukkan e-tiket kepada petugas di gerbang kantong parkir saat kedatangan hari-H lomba.
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ route('parking.school-tickets', $registration->registration_code) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                                                <i class="fa-solid fa-print"></i> Cetak Semua E-Tiket Sekaligus (PDF)
                                            </a>
                                            @if($hasRemainingQuota)
                                                <button type="button" @click="openModal()" class="px-3.5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                                                    <i class="fa-solid fa-plus text-rose-400"></i> Tambah Kendaraan
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- PRIORITY ALLOCATION HIGHLIGHT BANNER -->
                                    @if($isTopPriority || (isset($criteria['is_top_priority']) && $criteria['is_top_priority']))
                                        <!-- Golden Award Priority Banner (Matches Approved Mockup) -->
                                        <div class="bg-gradient-to-r from-amber-500/20 via-yellow-500/15 to-amber-500/20 border-2 border-amber-400 rounded-2xl p-5 sm:p-6 shadow-sm relative overflow-hidden">
                                            <div class="absolute -right-12 -top-12 w-40 h-40 bg-yellow-400/20 rounded-full blur-2xl pointer-events-none"></div>
                                            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                                <div class="space-y-2">
                                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500 text-slate-950 shadow-sm">
                                                        <i class="fa-solid fa-star"></i> PRIORITAS PARKIR UTAMA DISETUJUI
                                                    </div>
                                                    <h4 class="text-lg sm:text-xl font-black text-slate-950 tracking-tight uppercase">
                                                        {{ $assignedLocName ?: 'KANTONG PARKIR A (AREA KAMPUS UTAMA SMAN 1 CIAWI)' }}
                                                    </h4>
                                                    <p class="text-xs text-amber-950 font-medium leading-relaxed">
                                                        Selamat! Berdasarkan evaluasi 3 kriteria prioritas, kontingen Anda berhasil mendapatkan fasilitas parkir prioritas utama di area kampus depan SMAN 1 Ciawi.
                                                    </p>

                                                    <!-- 3-Pill Evaluation Breakdown -->
                                                    <div class="pt-2 flex flex-wrap gap-2 text-[11px] font-bold">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/90 text-slate-900 border border-amber-300 shadow-2xs">
                                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                                            Semua Cabang Diikuti ({{ $criteria['categories_count'] ?? $registration->teams->pluck('competition_category_id')->unique()->count() }}/{{ $criteria['categories_total'] ?? 5 }} Cabang)
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/90 text-slate-900 border border-amber-300 shadow-2xs">
                                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                                            Kuota Regu Maksimal ({{ $registration->total_teams_count }} Regu Lengkap)
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/90 text-slate-900 border border-amber-300 shadow-2xs">
                                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                                            Pendaftaran Tercepat (Peringkat #{{ $registration->parking_priority_rank ?? 1 }} Early Bird)
                                                        </span>
                                                    </div>
                                                </div>

                                                @if($assignedLocation && $assignedLocation->gmaps_url)
                                                    <a href="{{ $assignedLocation->gmaps_url }}" target="_blank" class="shrink-0 px-4 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-yellow-300 font-extrabold text-xs shadow-md transition flex items-center gap-2">
                                                        <i class="fa-solid fa-location-dot text-rose-400"></i> Rute Maps SMANIC &rarr;
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($assignedLocation)
                                        <!-- Regular Location Banner (Kantong B / C) -->
                                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                            <div>
                                                <span class="text-[10px] font-black uppercase tracking-wider text-blue-700 bg-blue-100 px-2.5 py-0.5 rounded-full">
                                                    LOKASI PARKIR RESMI DISETUJUI
                                                </span>
                                                <h4 class="text-base font-black text-slate-900 mt-1 uppercase">
                                                    {{ $assignedLocation->name }}
                                                </h4>
                                                <p class="text-xs text-slate-600 mt-0.5">{{ $assignedLocation->address }}</p>
                                                @if($assignedLocation->officer_name)
                                                    <p class="text-[11px] text-slate-500 mt-1">Petugas Jaga: <strong>{{ $assignedLocation->officer_name }}</strong> ({{ $assignedLocation->officer_phone }})</p>
                                                @endif
                                            </div>
                                            @if($assignedLocation->gmaps_url)
                                                <a href="{{ $assignedLocation->gmaps_url }}" target="_blank" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                                                    <i class="fa-solid fa-map-location-dot"></i> Buka Petunjuk Rute &rarr;
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <!-- Pending Panitia Plotting Notice -->
                                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 flex items-start gap-3 text-slate-700">
                                            <i class="fa-solid fa-clock-rotate-left text-amber-500 text-xl mt-0.5"></i>
                                            <div>
                                                <h4 class="font-bold text-sm text-slate-900">Data Kendaraan Terdaftar — Menunggu Penetapan Lokasi</h4>
                                                <p class="text-xs text-slate-600 mt-0.5">
                                                    Panitia sedang memproses penempatan kantong parkir berdasarkan skor prioritas kontingen. Peringkat skor prioritas sekolah Anda: <strong>#{{ $registration->parking_priority_rank ?? '-' }}</strong>. Lokasi kantong parkir akan segera diperbarui secara otomatis.
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- VEHICLE TICKET CARDS GRID (Matches Mockup) -->
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700">
                                                Daftar Kendaraan & E-Tiket Kontingen ({{ $parkingTickets->count() }} Kendaraan):
                                            </h4>
                                            @if($hasRemainingQuota)
                                                <span class="text-[11px] text-slate-500 font-medium">
                                                    Sisa Kuota: <strong class="text-slate-800">{{ $r4Remaining }} Mobil</strong> | <strong class="text-slate-800">{{ $r2Remaining }} Motor</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($parkingTickets as $ticket)
                                                <div class="bg-white border border-slate-200 hover:border-slate-300 rounded-2xl p-4 shadow-2xs transition flex flex-col justify-between">
                                                    <div>
                                                        <!-- Top Row: Badge & Ticket Code -->
                                                        <div class="flex items-center justify-between gap-2 mb-2">
                                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ str_starts_with($ticket->vehicle_type, 'r2') ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                                {{ $ticket->vehicle_type_label }}
                                                            </span>
                                                            <span class="font-mono text-xs font-black text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                                                {{ $ticket->ticket_code }}
                                                            </span>
                                                        </div>

                                                        <!-- Barcode Graphic Strip Decorative -->
                                                        <div class="w-full h-4 barcode-strip rounded-xs mb-3 opacity-80"></div>

                                                        <!-- License Plate & Vehicle Specs -->
                                                        <div class="flex items-start justify-between gap-3 mb-2">
                                                            <div>
                                                                <span class="font-mono text-base font-black text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-300 inline-block shadow-2xs">
                                                                    {{ $ticket->license_plate }}
                                                                </span>
                                                                <div class="text-xs font-bold text-slate-800 mt-1.5">
                                                                    {{ $ticket->vehicle_brand ?: 'Kendaraan Kontingen' }}
                                                                    @if($ticket->vehicle_color)
                                                                        <span class="text-slate-500 font-normal">({{ $ticket->vehicle_color }})</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <!-- Mini QR Code -->
                                                            <div class="p-1 bg-white border border-slate-200 rounded-lg shrink-0">
                                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode(route('parking.public-ticket', $ticket->qr_code_token)) }}" alt="QR" class="w-12 h-12">
                                                            </div>
                                                        </div>

                                                        <!-- Driver Info -->
                                                        <div class="text-[11px] text-slate-600 space-y-0.5 mb-3 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                                            <div class="flex items-center gap-1.5">
                                                                <i class="fa-solid fa-user-tie text-slate-400 text-[10px]"></i>
                                                                <span>Pengemudi: <strong class="text-slate-800">{{ $ticket->driver_name ?: '-' }}</strong></span>
                                                            </div>
                                                            @if($ticket->driver_phone)
                                                                <div class="flex items-center gap-1.5">
                                                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-[10px]"></i>
                                                                    <span>WhatsApp: <strong class="text-slate-800 font-mono">{{ $ticket->driver_phone }}</strong></span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Actions per Ticket -->
                                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                                        <div class="flex items-center gap-1.5 flex-wrap">
                                                            <a href="{{ route('parking.public-ticket', $ticket->qr_code_token) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center gap-1.5">
                                                                <i class="fa-solid fa-qrcode text-[10px] text-yellow-400"></i> Buka E-Tiket
                                                            </a>
                                                            @if($ticket->location && $ticket->location->gmaps_url)
                                                                <a href="{{ $ticket->location->gmaps_url }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1" title="Rute Maps">
                                                                    <i class="fa-solid fa-location-arrow text-rose-500"></i> Maps
                                                                </a>
                                                            @endif
                                                        </div>

                                                        @if($ticket->status === 'booked')
                                                            <form action="{{ route('lomba.delete-vehicle-ticket') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pendaftaran kendaraan {{ $ticket->license_plate }}?')">
                                                                @csrf
                                                                <input type="hidden" name="registration_code" value="{{ $registration->registration_code }}">
                                                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                                                <button type="submit" class="text-slate-400 hover:text-rose-600 p-1.5 text-xs transition" title="Hapus Kendaraan Ini">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Notice Card: Belum Mendaftarkan Kendaraan -->
                                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white rounded-2xl p-6 sm:p-7 shadow-lg border border-slate-700 flex flex-col md:flex-row items-center justify-between gap-5">
                                    <div class="space-y-2 text-center md:text-left">
                                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs">
                                            <i class="fa-solid fa-car-side"></i> FASILITAS PARKIR KONTINGEN RESMI
                                        </div>
                                        <h3 class="text-xl font-black text-white tracking-tight">
                                            Daftarkan Kendaraan Kontingen Sekolah Anda Sekarang
                                        </h3>
                                        <p class="text-xs text-slate-300 leading-relaxed max-w-xl">
                                            Berdasarkan <strong>{{ $registration->total_teams_count }} regu lomba</strong> yang terdaftar, sekolah Anda mendapatkan fasilitas kuota parkir gratis: <strong class="text-yellow-400">{{ $r4Allocated }} Mobil (Roda 4)</strong> dan <strong class="text-yellow-400">{{ $r2Allocated }} Motor (Roda 2)</strong>. Daftarkan plat nomor kendaraan untuk mendapatkan alokasi kantong parkir terbaik & e-tiket resmi!
                                        </p>
                                    </div>
                                    <button type="button" @click="openModal()" class="shrink-0 px-6 py-3.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-black text-xs shadow-xl transition flex items-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-ticket text-yellow-300 text-base"></i> Daftarkan Kendaraan & Dapatkan Tiket &rarr;
                                    </button>
                                </div>
                            @endif

                            <!-- ================= MODAL POPUP: REGISTRASI KENDARAAN (Matches Approved Mockup) ================= -->
                            <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs transition-opacity" style="display: none;">
                                <div @click.away="closeModal()" class="bg-white rounded-3xl max-w-2xl w-full max-h-[92vh] overflow-y-auto shadow-2xl border border-slate-200">
                                    
                                    <!-- Modal Header -->
                                    <div class="bg-gradient-to-r from-red-600 to-rose-700 text-white p-5 rounded-t-3xl relative">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-white p-1 rounded-lg">
                                                    <img src="{{ asset('images/logo_pmi_sman1ciawi_official.png') }}" alt="Logo PMI" class="h-7 w-auto">
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-black uppercase tracking-widest text-rose-200 block">SUA BHAKTI BERKARYA IV 2026</span>
                                                    <h3 class="font-black text-base text-white leading-tight">Registrasi Kendaraan & E-Tiket Parkir</h3>
                                                </div>
                                            </div>
                                            <button type="button" @click="closeModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition">✕</button>
                                        </div>
                                        <div class="mt-3 flex items-center gap-2 text-xs text-rose-100">
                                            <span class="font-mono bg-white/20 px-2 py-0.5 rounded font-bold">{{ $registration->registration_code }}</span>
                                            <span>&bull;</span>
                                            <span class="font-bold text-white uppercase">{{ $registration->school_name }}</span>
                                        </div>
                                    </div>

                                    <form action="{{ route('lomba.register-vehicles') }}" method="POST" class="p-6 space-y-5">
                                        @csrf
                                        <input type="hidden" name="registration_code" value="{{ $registration->registration_code }}">

                                        <!-- Quota Notice Header -->
                                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                            <div>
                                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">HAK KUOTA PARKIR KONTINGEN:</span>
                                                <p class="text-xs text-slate-700 mt-0.5">
                                                    Berdasarkan <strong>{{ $registration->total_teams_count }} regu</strong> yang terdaftar, kuota Anda:
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="px-3 py-1.5 rounded-xl bg-blue-100 text-blue-900 border border-blue-200 font-bold text-xs flex items-center gap-1.5">
                                                    <i class="fa-solid fa-car"></i> Sisa Mobil: <span class="font-black text-sm" x-text="r4Remaining"></span>
                                                </span>
                                                <span class="px-3 py-1.5 rounded-xl bg-purple-100 text-purple-900 border border-purple-200 font-bold text-xs flex items-center gap-1.5">
                                                    <i class="fa-solid fa-motorcycle"></i> Sisa Motor: <span class="font-black text-sm" x-text="r2Remaining"></span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Dynamic Vehicle Rows -->
                                        <div class="space-y-4">
                                            <template x-for="(veh, index) in vehicles" :key="index">
                                                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 relative space-y-3">
                                                    <div class="flex items-center justify-between">
                                                        <span class="font-black text-xs text-slate-800 flex items-center gap-1.5">
                                                            <i class="fa-solid fa-car-side text-rose-600"></i>
                                                            <span x-text="'Kendaraan Kontingen #' + (index + 1)"></span>
                                                        </span>
                                                        <button type="button" x-show="vehicles.length > 1" @click="removeVehicle(index)" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1">
                                                            <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                                        </button>
                                                    </div>

                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                        <!-- Jenis Kendaraan -->
                                                        <div>
                                                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Jenis Kendaraan *</label>
                                                            <select :name="'vehicles[' + index + '][vehicle_type]'" x-model="veh.vehicle_type" required class="w-full text-xs font-semibold bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:border-red-500">
                                                                <option value="r4_car">Roda 4 - Mobil Pribadi / MPV (Avanza, Innova, dll)</option>
                                                                <option value="r4_elf">Roda 4 - Minibus Elf / Toyota HiAce</option>
                                                                <option value="r4_bus">Roda 4 - Bus Besar Pariwisata</option>
                                                                <option value="r2_motor">Roda 2 - Sepeda Motor Pembina/Pendamping</option>
                                                            </select>
                                                        </div>

                                                        <!-- Plat Nomor -->
                                                        <div>
                                                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Nomor Polisi (Plat) *</label>
                                                            <input type="text" :name="'vehicles[' + index + '][license_plate]'" x-model="veh.license_plate" required placeholder="Contoh: F 1845 EA" class="w-full text-xs font-mono font-black uppercase bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:border-red-500">
                                                        </div>

                                                        <!-- Merek & Tipe -->
                                                        <div>
                                                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Merek & Tipe Kendaraan</label>
                                                            <input type="text" :name="'vehicles[' + index + '][vehicle_brand]'" x-model="veh.vehicle_brand" placeholder="Contoh: Toyota HiAce Commuter" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:border-red-500">
                                                        </div>

                                                        <!-- Warna Kendaraan -->
                                                        <div>
                                                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Warna Kendaraan</label>
                                                            <input type="text" :name="'vehicles[' + index + '][vehicle_color]'" x-model="veh.vehicle_color" placeholder="Contoh: Putih Metalik" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:border-red-500">
                                                        </div>

                                                        <!-- Nama Sopir -->
                                                        <div>
                                                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Nama Sopir / Pembina PJ</label>
                                                            <input type="text" :name="'vehicles[' + index + '][driver_name]'" x-model="veh.driver_name" placeholder="Contoh: Pak Hendra / Kak Rian" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:border-red-500">
                                                        </div>

                                                        <!-- No WA Sopir -->
                                                        <div>
                                                            <label class="text-[11px] font-bold text-slate-700 block mb-1">No. WhatsApp Sopir (Koordinasi Hari-H)</label>
                                                            <input type="text" :name="'vehicles[' + index + '][driver_phone]'" x-model="veh.driver_phone" placeholder="Contoh: 08123456789" class="w-full text-xs font-mono bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:border-red-500">
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Button Add Row -->
                                        <div class="flex items-center justify-between pt-2">
                                            <button type="button" @click="addVehicle()" :disabled="!canAddMore" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-50 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                                                <i class="fa-solid fa-plus text-rose-500"></i> Tambah Kendaraan Lainnya
                                            </button>
                                            <span class="text-[11px] text-slate-400 italic" x-show="!canAddMore">Maksimal sesuai sisa kuota yang tersedia.</span>
                                        </div>

                                        <!-- Footer Actions -->
                                        <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                                            <button type="button" @click="closeModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                                Tutup
                                            </button>
                                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                                                <i class="fa-solid fa-paper-plane"></i> Kirim Pendaftaran Kendaraan & Ajukan Tiket
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            </div>

                        </div>
                    @endif

                    <!-- Daftar Tim & Cabang Lomba -->
                    <div>
                        <h4 class="font-bold text-sm text-slate-800 uppercase tracking-wider mb-3">Cabang Lomba yang Didaftarkan</h4>
                        <div class="border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100">
                            @foreach($registration->teams as $team)
                                <div class="p-4 hover:bg-slate-50 transition space-y-2.5">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <div class="flex items-center flex-wrap gap-2">
                                                <div class="font-bold text-slate-800 text-sm">{{ $team->category->name }} ({{ $team->category->gender_category }})</div>
                                                @if($team->order_number)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black font-mono bg-amber-100 text-amber-900 border border-amber-300">
                                                        <i class="fa-solid fa-flag-checkered text-[9px] text-amber-600"></i> No. Urut Peserta: {{ $team->order_number }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                Nama Regu: <span class="font-semibold text-slate-700">{{ $team->team_name }}</span>
                                                @if($team->full_order_code)
                                                    &bull; <span class="font-mono text-slate-500 font-semibold text-[11px]">Kode: {{ $team->full_order_code }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="bg-slate-100 text-slate-600 text-xs font-semibold px-3 py-1 rounded-lg shrink-0">
                                            {{ $team->category->scoring_type == 'standard_time' ? 'Uji Ketangkasan / Waktu' : 'Uji Penilaian Teknis' }}
                                        </span>
                                    </div>

                                    @php
                                        $members = is_array($team->members_list) ? $team->members_list : (is_string($team->members_list) ? json_decode($team->members_list, true) : []);
                                    @endphp
                                    @if(!empty($members) || $team->members_file_url)
                                        <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3 text-xs space-y-2">
                                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-700">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="fa-solid fa-users text-red-600"></i>
                                                    <span>Daftar Peserta Regu ({{ count($members) }} Orang):</span>
                                                </span>
                                                @if($team->members_file_url)
                                                    <a href="{{ $team->members_file_url }}" target="_blank" class="text-emerald-700 hover:text-emerald-900 inline-flex items-center gap-1 font-semibold">
                                                        <i class="fa-solid fa-file-excel"></i> Unduh File Excel
                                                    </a>
                                                @endif
                                            </div>
                                            @if(!empty($members))
                                                <div class="flex flex-wrap gap-1.5 pt-0.5">
                                                    @foreach($members as $mIdx => $m)
                                                        @php
                                                            $mName = is_array($m) ? ($m['name'] ?? ($m['nama'] ?? 'Peserta')) : $m;
                                                            $mRole = is_array($m) && !empty($m['role'] ?? ($m['peran'] ?? '')) ? ' (' . ($m['role'] ?? $m['peran']) . ')' : '';
                                                        @endphp
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[11px] font-medium bg-white border border-slate-200 text-slate-800 shadow-2xs">
                                                            <span class="font-bold text-slate-400">{{ $mIdx + 1 }}.</span>
                                                            <span>{{ $mName }}{{ $mRole }}</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @elseif($code)
            <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Kode Pendaftaran Tidak Ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Mohon pastikan kode pendaftaran yang Anda masukkan sudah benar (contoh: <code>SBB-A1B2C3</code>).
                </p>
            </div>
        @endif

    </div>
</div>

@push('styles')
<style>
    .barcode-strip {
        background: repeating-linear-gradient(
            90deg,
            #000,
            #000 2px,
            #fff 2px,
            #fff 4px,
            #000 4px,
            #000 7px,
            #fff 7px,
            #fff 9px
        );
    }
</style>
@endpush

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
<script>
    function parkingRegistrationApp(config) {
        return {
            showModal: config.autoOpen || false,
            r4Remaining: config.r4Remaining || 0,
            r2Remaining: config.r2Remaining || 0,
            vehicles: [
                {
                    vehicle_type: (config.r4Remaining > 0 ? 'r4_car' : 'r2_motor'),
                    license_plate: '',
                    vehicle_brand: '',
                    vehicle_color: '',
                    driver_name: '',
                    driver_phone: ''
                }
            ],
            get canAddMore() {
                return this.vehicles.length < (this.r4Remaining + this.r2Remaining);
            },
            openModal() {
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
            },
            addVehicle() {
                if (this.canAddMore) {
                    this.vehicles.push({
                        vehicle_type: (this.r4Remaining > 0 ? 'r4_car' : 'r2_motor'),
                        license_plate: '',
                        vehicle_brand: '',
                        vehicle_color: '',
                        driver_name: '',
                        driver_phone: ''
                    });
                }
            },
            removeVehicle(index) {
                if (this.vehicles.length > 1) {
                    this.vehicles.splice(index, 1);
                }
            }
        };
    }
</script>
@endpush
@endsection
