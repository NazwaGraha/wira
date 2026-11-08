@extends('layouts.app')

@section('title', 'Cek Status Pendaftaran & Unduh Dokumen - ' . ($registration ? $registration->school_name : 'PMR SMAN 1 Ciawi'))

@section('content')
<!-- Header & Pencarian Kompak (Slim & Modern Header) -->
<div class="bg-gradient-to-b from-slate-950 via-slate-900 to-slate-900 text-white pt-28 pb-8 border-b border-slate-800">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left space-y-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600/30 text-rose-300 border border-rose-500/40">
                    <i class="fa-solid fa-id-card-clip"></i> PORTAL RESMI KONTINGEN SUA BHAKTI BERKARYA IV 2026
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Cek Status & Unduh Dokumen</h1>
                <p class="text-xs text-slate-400 max-w-xl">
                    Periksa status verifikasi pendaftaran, unduh e-Kwitansi resmi, cetak kartu peserta, dan kelola e-tiket parkir kontingen Anda.
                </p>
            </div>

            <!-- Compact Search Bar -->
            <form action="{{ route('lomba.status') }}" method="GET" class="w-full md:w-auto shrink-0 flex items-center bg-slate-800/90 border border-slate-700 focus-within:border-red-500 rounded-2xl p-1.5 shadow-lg">
                <div class="relative flex-1 md:w-56">
                    <input type="text" name="code" value="{{ $code }}" placeholder="KODE REGISTRASI..." required class="w-full bg-transparent text-white px-3 py-2 text-center font-mono font-black tracking-widest text-sm focus:outline-none uppercase placeholder:normal-case placeholder:font-sans placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-500">
                </div>
                <button type="submit" class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer shrink-0">
                    <i class="fa-solid fa-magnifying-glass"></i> Cek
                </button>
            </form>
        </div>
    </div>
</div>

<div class="bg-slate-50 min-h-screen py-8 sm:py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-500 text-xl shrink-0"></i>
                <div class="text-xs sm:text-sm font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-xl shrink-0"></i>
                <div class="text-xs sm:text-sm font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        @if($registration)
            @php
                $isVerified = ($registration->status === 'verified');
                $isPending = ($registration->status === 'pending');
                $isRejected = ($registration->status === 'rejected');
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
                $autoOpenModal = $isVerified && $parkingTickets->isEmpty() && !session()->has('hide_vehicle_modal');
            @endphp

            <div x-data="statusPortalApp({
                autoOpenModal: {{ $autoOpenModal ? 'true' : 'false' }},
                r4Remaining: {{ $r4Remaining }},
                r2Remaining: {{ $r2Remaining }}
            })" class="space-y-6">

                <!-- ================= 1. KARTU UTAMA RANGKUMAN KONTINGEN (HERO DASHBOARD CARD) ================= -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Top Bar: Identitas Sekolah & Lencana Status -->
                    <div class="p-6 sm:p-7 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-200 flex flex-col md:flex-row justify-between md:items-center gap-5">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <!-- Salin Kode Registrasi -->
                                <button type="button" @click="copyCode('{{ $registration->registration_code }}')" class="font-mono text-xs font-black text-slate-800 bg-white hover:bg-slate-100 border border-slate-300 px-2.5 py-1 rounded-lg inline-flex items-center gap-1.5 shadow-2xs transition cursor-pointer" title="Klik untuk menyalin kode registrasi">
                                    <i class="fa-regular fa-copy text-slate-400"></i>
                                    <span>{{ $registration->registration_code }}</span>
                                    <span class="text-[10px] text-emerald-600 font-bold" x-show="copied" x-cloak>(Tersalin!)</span>
                                </button>

                                @php
                                    $levelColorClass = match($registration->level) {
                                        'Mula' => 'text-emerald-800 bg-emerald-100 border-emerald-300',
                                        'Madya' => 'text-blue-800 bg-blue-100 border-blue-300',
                                        default => 'text-amber-900 bg-amber-100 border-amber-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-md font-black text-xs border {{ $levelColorClass }}">
                                    PMR {{ $registration->level }}
                                </span>

                                <span class="px-2.5 py-0.5 rounded-md font-bold text-xs {{ ($registration->registration_wave ?? 1) == 2 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                    {{ ($registration->registration_wave ?? 1) == 2 ? ($registration->event->wave2_name ?: 'Gelombang 2') : ($registration->event->wave1_name ?: 'Gelombang 1 - Early Bird') }}
                                </span>
                            </div>

                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight">
                                {{ $registration->school_name }}
                            </h2>

                            <p class="text-xs text-slate-500 flex items-center gap-3 flex-wrap">
                                <span><i class="fa-solid fa-user-tie text-slate-400"></i> Pembina: <strong class="text-slate-700">{{ $registration->advisor_name }}</strong></span>
                                <span>&bull;</span>
                                <span><i class="fa-brands fa-whatsapp text-emerald-600"></i> <strong class="text-slate-700 font-mono">{{ $registration->advisor_phone }}</strong></span>
                                @if($registration->advisor_email)
                                    <span>&bull;</span>
                                    <span><i class="fa-solid fa-envelope text-slate-400"></i> {{ $registration->advisor_email }}</span>
                                @endif
                            </p>
                        </div>

                        <!-- Status Badge Prestisius -->
                        <div class="shrink-0">
                            @if($isVerified)
                                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-400 text-emerald-800 px-5 py-3 rounded-2xl shadow-sm text-center">
                                    <div class="inline-flex items-center gap-2 font-black text-sm text-emerald-700">
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i> TERVERIFIKASI / LUNAS
                                    </div>
                                    <span class="block text-[10px] text-emerald-600 font-bold mt-0.5">Berkas & Pembayaran Resmi Diterima</span>
                                </div>
                            @elseif($isPending)
                                <div class="bg-gradient-to-br from-amber-50 to-yellow-50 border-2 border-amber-400 text-amber-800 px-5 py-3 rounded-2xl shadow-sm text-center">
                                    <div class="inline-flex items-center gap-2 font-black text-sm text-amber-700">
                                        <i class="fa-solid fa-clock-rotate-left text-amber-500 text-lg"></i> MENUNGGU VERIFIKASI
                                    </div>
                                    <span class="block text-[10px] text-amber-600 font-bold mt-0.5">Sedang Dalam Pemeriksaan Panitia</span>
                                </div>
                            @else
                                <div class="bg-gradient-to-br from-rose-50 to-red-50 border-2 border-rose-400 text-rose-800 px-5 py-3 rounded-2xl shadow-sm text-center">
                                    <div class="inline-flex items-center gap-2 font-black text-sm text-rose-700">
                                        <i class="fa-solid fa-circle-xmark text-rose-500 text-lg"></i> DITOLAK
                                    </div>
                                    <span class="block text-[10px] text-rose-600 font-bold mt-0.5">Perlu Konfirmasi Perbaikan Data</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Milestone Progress Tracker Bar (4 Langkah Menuju Hari-H) -->
                    <div class="px-6 py-4 bg-slate-900 text-white">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 mb-2">
                            <span class="text-rose-400 font-black uppercase tracking-wider text-[10px]">
                                <i class="fa-solid fa-route"></i> ALUR PROGRES KONTINGEN
                            </span>
                            <span>Total Terdaftar: <strong class="text-white">{{ $registration->teams->count() }} Regu Lomba</strong></span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                            <!-- Step 1 -->
                            <div class="p-2.5 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                <div>
                                    <span class="block font-black text-[11px]">1. Formulir Terkirim</span>
                                    <span class="text-[9px] text-emerald-200/80">Data tersimpan di sistem</span>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="p-2.5 rounded-xl {{ $isVerified ? 'bg-emerald-500/15 border border-emerald-500/40 text-emerald-300' : 'bg-amber-500/15 border border-amber-500/40 text-amber-300' }} flex items-center gap-2">
                                <i class="fa-solid {{ $isVerified ? 'fa-circle-check text-emerald-400' : 'fa-hourglass-half text-amber-400 animate-pulse' }}"></i>
                                <div>
                                    <span class="block font-black text-[11px]">2. Verifikasi & Kwitansi</span>
                                    <span class="text-[9px] {{ $isVerified ? 'text-emerald-200/80' : 'text-amber-200/80' }}">{{ $isVerified ? 'Lunas & Sah' : 'Sedang diverifikasi' }}</span>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="p-2.5 rounded-xl {{ $hasTickets ? 'bg-emerald-500/15 border border-emerald-500/40 text-emerald-300' : ($isVerified ? 'bg-blue-500/15 border border-blue-500/40 text-blue-300' : 'bg-slate-800 text-slate-500 border border-slate-700') }} flex items-center gap-2">
                                <i class="fa-solid {{ $hasTickets ? 'fa-circle-check text-emerald-400' : ($isVerified ? 'fa-car text-blue-400' : 'fa-lock') }}"></i>
                                <div>
                                    <span class="block font-black text-[11px]">3. E-Tiket Parkir</span>
                                    <span class="text-[9px] {{ $hasTickets ? 'text-emerald-200/80' : ($isVerified ? 'text-blue-200/80' : 'text-slate-500') }}">{{ $hasTickets ? 'Tiket Terbit' : ($isVerified ? 'Siap Didaftarkan' : 'Menunggu verifikasi') }}</span>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="p-2.5 rounded-xl bg-slate-800/80 text-slate-400 border border-slate-700/80 flex items-center gap-2">
                                <i class="fa-solid fa-flag-checkered text-slate-500"></i>
                                <div>
                                    <span class="block font-black text-[11px]">4. Technical Meeting</span>
                                    <span class="text-[9px] text-slate-400">Jadwal & Hari-H Lomba</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($isRejected)
                        <!-- Catatan Penolakan -->
                        <div class="p-6 bg-rose-50 border-t border-rose-200 text-rose-800">
                            <h3 class="font-bold text-sm flex items-center gap-2">
                                <i class="fa-solid fa-circle-exclamation text-rose-600"></i> Catatan Penolakan dari Panitia:
                            </h3>
                            <p class="text-xs mt-2 bg-white p-3 rounded-xl border border-rose-200 font-medium">
                                {{ $registration->rejection_reason ?? 'Bukti transfer tidak valid atau belum sesuai nominal pendaftaran lomba.' }}
                            </p>
                            <p class="text-[11px] text-slate-500 mt-2">Mohon segera hubungi panitia melalui kontak WhatsApp untuk konfirmasi dan perbaikan berkas.</p>
                        </div>
                    @elseif($isPending)
                        <!-- Catatan Sedang Ditinjau -->
                        <div class="p-6 bg-amber-50/60 border-t border-amber-200 text-amber-800 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-amber-600 text-xl mt-0.5 shrink-0"></i>
                            <div>
                                <h3 class="font-bold text-sm">Pembayaran Anda Sedang Diperiksa Panitia</h3>
                                <p class="text-xs text-amber-700 mt-0.5 leading-relaxed">
                                    Panitia sedang memeriksa mutasi bukti transfer dan kelengkapan regu Anda. Begitu dinyatakan lunas, e-Kwitansi resmi, Kartu Peserta, dan formulir pendaftaran parkir akan otomatis aktif di halaman ini.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ================= 2. PUSAT AKSI CEPAT 3 KOLOM (QUICK ACTION HUB) ================= -->
                @if($isVerified)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Action Card 1: Berkas Resmi Lomba (Official Downloads) -->
                        <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-50/60 to-white rounded-3xl p-5 border-2 border-emerald-400/80 shadow-xs flex flex-col justify-between space-y-4">
                            <div>
                                <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-lg shadow-sm mb-3">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <h3 class="font-black text-slate-900 text-base leading-tight">Berkas Resmi Lomba</h3>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Unduh dokumen wajib untuk dibawa saat registrasi ulang / Technical Meeting (TM).
                                </p>
                            </div>

                            <div class="space-y-2 pt-2 border-t border-emerald-200/60">
                                <a href="{{ url('/lomba/kwitansi/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs px-4 py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-receipt"></i> Cetak e-Kwitansi (PDF)
                                </a>
                                <a href="{{ url('/lomba/kartu-peserta/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs px-4 py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-id-card"></i> Cetak Kartu Peserta ({{ $registration->teams->count() }} Regu)
                                </a>
                                @if($registration->advisor_email)
                                    <span class="block text-[10px] text-emerald-800 font-semibold text-center truncate" title="{{ $registration->advisor_email }}">
                                        <i class="fa-solid fa-envelope-circle-check"></i> Email: {{ $registration->advisor_email }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Card 2: Wadah Resmi Komunikasi (WhatsApp Channel) -->
                        <div class="bg-gradient-to-br from-green-600 to-emerald-700 text-white rounded-3xl p-5 shadow-md flex flex-col justify-between space-y-4">
                            <div>
                                <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-xs text-white flex items-center justify-center text-xl shadow-sm mb-3">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </div>
                                <span class="bg-white/20 text-white text-[9px] font-black uppercase px-2 py-0.5 rounded tracking-wider inline-block">
                                    WADAH RESMI PMR {{ $registration->level }}
                                </span>
                                <h3 class="font-black text-white text-base mt-1.5 leading-tight">
                                    Grup WhatsApp Pembina
                                </h3>
                                <p class="text-xs text-emerald-100 mt-1 leading-relaxed">
                                    Wajib diikuti pembina/pendamping untuk update nomor undian, arahan juri, dan jadwal TM.
                                </p>
                            </div>

                            <div class="pt-2">
                                <a href="{{ $registration->whatsapp_group_url }}" target="_blank" class="w-full bg-white hover:bg-emerald-50 text-emerald-800 font-black text-xs px-4 py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i> Gabung Grup WhatsApp &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Action Card 3: Fasilitas Parkir Kontingen (Parking E-Tickets) -->
                        <div class="bg-gradient-to-br from-amber-500/10 via-yellow-50/50 to-white rounded-3xl p-5 border-2 border-amber-300 shadow-xs flex flex-col justify-between space-y-4">
                            <div>
                                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-lg shadow-sm mb-3">
                                    <i class="fa-solid fa-square-parking"></i>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-800 bg-amber-100 px-2 py-0.5 rounded">
                                        FASILITAS PARKIR KONTINGEN RESMI
                                    </span>
                                </div>
                                <h3 class="font-black text-slate-900 text-base mt-1.5 leading-tight">Fasilitas Parkir Kontingen</h3>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Kuota: <strong>{{ $r4Allocated }} Mobil</strong> & <strong>{{ $r2Allocated }} Motor</strong>
                                    @if($hasTickets)
                                        <br><span class="text-emerald-700 font-bold"><i class="fa-solid fa-check-circle"></i> {{ $parkingTickets->count() }} Kendaraan Terdaftar</span>
                                    @else
                                        <br><span class="text-amber-700 font-bold"><i class="fa-solid fa-clock"></i> Belum Didaftarkan</span>
                                    @endif
                                </p>
                            </div>

                            <div class="space-y-2 pt-2 border-t border-amber-200/60">
                                @if($hasTickets)
                                    <a href="{{ route('parking.school-tickets', $registration->registration_code) }}" target="_blank" class="w-full bg-slate-900 hover:bg-slate-800 text-yellow-300 font-extrabold text-xs px-4 py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-print"></i> Cetak Semua E-Tiket Sekaligus (PDF)
                                    </a>
                                    <button type="button" @click="activeTab = 'parking'" class="w-full bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs px-4 py-2 rounded-xl border border-slate-300 transition flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-qrcode text-rose-500"></i> Kelola / Lihat Tiket &rarr;
                                    </button>
                                @else
                                    <button type="button" @click="openModal()" class="w-full bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-black text-xs px-4 py-3 rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-ticket text-yellow-300"></i> Daftarkan Kendaraan Sekarang &rarr;
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ================= 3. TAB NAVIGASI KONTEN (TABS VIEW SWITCHER) ================= -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Tab Header Bar -->
                    <div class="px-6 pt-4 border-b border-slate-200 flex items-center gap-3 overflow-x-auto bg-slate-50/70">
                        <button type="button" @click="activeTab = 'teams'" :class="activeTab === 'teams' ? 'border-red-600 text-red-600 bg-white font-black shadow-xs' : 'border-transparent text-slate-500 hover:text-slate-800 font-bold'" class="px-4 py-3 border-b-2 rounded-t-xl text-xs sm:text-sm flex items-center gap-2 transition shrink-0">
                            <i class="fa-solid fa-people-group"></i>
                            <span>Cabang Lomba & Regu ({{ $registration->teams->count() }})</span>
                        </button>

                        @if($isVerified)
                            <button type="button" @click="activeTab = 'parking'" :class="activeTab === 'parking' ? 'border-red-600 text-red-600 bg-white font-black shadow-xs' : 'border-transparent text-slate-500 hover:text-slate-800 font-bold'" class="px-4 py-3 border-b-2 rounded-t-xl text-xs sm:text-sm flex items-center gap-2 transition shrink-0">
                                <i class="fa-solid fa-square-parking"></i>
                                <span>Tiket Parkir & Alokasi ({{ $parkingTickets->count() }})</span>
                            </button>
                        @endif

                        <button type="button" @click="activeTab = 'guide'" :class="activeTab === 'guide' ? 'border-red-600 text-red-600 bg-white font-black shadow-xs' : 'border-transparent text-slate-500 hover:text-slate-800 font-bold'" class="px-4 py-3 border-b-2 rounded-t-xl text-xs sm:text-sm flex items-center gap-2 transition shrink-0">
                            <i class="fa-solid fa-circle-question"></i>
                            <span>Panduan & Kontak Panitia</span>
                        </button>
                    </div>

                    <!-- TAB CONTENT BODY -->
                    <div class="p-6 sm:p-8">

                        <!-- TAB 1: CABANG LOMBA & REGU PESERTA -->
                        <div x-show="activeTab === 'teams'" class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                                <div>
                                    <h3 class="font-black text-slate-900 text-base">Rincian Cabang Lomba & Regu Kontingen</h3>
                                    <p class="text-xs text-slate-500">Daftar regu yang telah terdaftar resmi beserta nomor urut tampil.</p>
                                </div>
                                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-full self-start sm:self-auto">
                                    Total: {{ $registration->teams->count() }} Regu
                                </span>
                            </div>

                            <!-- Grid Kartu Regu (Modern 2-Column Responsive Cards) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                                @forelse($registration->teams as $tIdx => $team)
                                    <div class="bg-slate-50/70 hover:bg-white border border-slate-200 hover:border-slate-300 rounded-2xl p-4 shadow-2xs hover:shadow-xs transition space-y-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-black text-slate-900 text-sm">
                                                        {{ $team->category->name }}
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $team->category->gender_category === 'Putra' ? 'bg-blue-100 text-blue-800' : ($team->category->gender_category === 'Putri' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700') }}">
                                                        {{ $team->category->gender_category }}
                                                    </span>
                                                </div>
                                                <div class="text-xs font-bold text-slate-700 mt-1">
                                                    {{ $team->team_name }}
                                                    @if($team->full_order_code)
                                                        &bull; <span class="font-mono text-slate-500 text-[11px] font-semibold">{{ $team->full_order_code }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Nomor Urut Tampil Badge Emas -->
                                            @if($team->order_number)
                                                <div class="shrink-0 text-right">
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black font-mono bg-amber-100 text-amber-950 border border-amber-300 shadow-2xs">
                                                        <i class="fa-solid fa-flag-checkered text-amber-600"></i> No. Urut: {{ $team->order_number }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Members List / Roster -->
                                        @php
                                            $members = is_array($team->members_list) ? $team->members_list : (is_string($team->members_list) ? json_decode($team->members_list, true) : []);
                                        @endphp
                                        <div class="bg-white rounded-xl p-3 border border-slate-200/80 text-xs space-y-2">
                                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-700">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="fa-solid fa-users text-rose-600"></i>
                                                    <span>Anggota Regu ({{ count($members) }} Orang):</span>
                                                </span>
                                                @if($team->members_file_url)
                                                    <a href="{{ $team->members_file_url }}" target="_blank" class="text-emerald-700 hover:text-emerald-900 inline-flex items-center gap-1 font-semibold text-[10px]">
                                                        <i class="fa-solid fa-file-excel"></i> File Excel
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
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[11px] font-medium bg-slate-50 border border-slate-200 text-slate-800">
                                                            <span class="font-bold text-slate-400">{{ $mIdx + 1 }}.</span>
                                                            <span>{{ $mName }}{{ $mRole }}</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-[11px] text-slate-400 italic">Daftar nama anggota regu belum diunggah.</p>
                                            @endif
                                        </div>

                                        <!-- Footer card: Scoring Type -->
                                        <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-slate-100">
                                            <span>Format Uji:</span>
                                            <span class="font-bold text-slate-700">
                                                {{ $team->category->scoring_type == 'standard_time' ? 'Uji Ketangkasan / Kecepatan Waktu' : 'Uji Penilaian Teknis Rubrik' }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-2 text-center py-8 text-slate-400">
                                        <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300"></i>
                                        <p class="font-bold text-sm text-slate-600">Belum ada regu yang didaftarkan</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- TAB 2: TIKET PARKIR & ALOKASI KANTONG -->
                        @if($isVerified)
                            <div x-show="activeTab === 'parking'" x-cloak class="space-y-6">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                                    <div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded border border-rose-100">
                                            PENGELOLAAN PARKIR RESMI
                                        </span>
                                        <h3 class="font-black text-slate-900 text-base mt-1 flex items-center gap-2">
                                            <i class="fa-solid fa-square-parking text-rose-600"></i> Status Tiket Parkir & Alokasi Prioritas
                                        </h3>
                                        <p class="text-xs text-slate-500">
                                            Tunjukkan e-tiket kepada petugas di gerbang kantong parkir saat kedatangan hari-H lomba.
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($hasTickets)
                                            <a href="{{ route('parking.school-tickets', $registration->registration_code) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition flex items-center gap-1.5">
                                                <i class="fa-solid fa-print"></i> Cetak Semua E-Tiket Sekaligus (PDF)
                                            </a>
                                        @endif
                                        @if($hasRemainingQuota)
                                            <button type="button" @click="openModal()" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                                                <i class="fa-solid fa-plus text-rose-400"></i> Tambah Kendaraan
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- PRIORITY ALLOCATION HIGHLIGHT BANNER -->
                                @if($isTopPriority || (isset($criteria['is_top_priority']) && $criteria['is_top_priority']))
                                    <!-- Golden Award Priority Banner -->
                                    <div class="bg-gradient-to-r from-amber-500/20 via-yellow-500/15 to-amber-500/20 border-2 border-amber-400 rounded-3xl p-6 shadow-sm relative overflow-hidden">
                                        <div class="absolute -right-12 -top-12 w-40 h-40 bg-yellow-400/20 rounded-full blur-2xl pointer-events-none"></div>
                                        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                            <div class="space-y-2">
                                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500 text-slate-950 shadow-sm">
                                                    <i class="fa-solid fa-star"></i> PRIORITAS PARKIR UTAMA DISETUJUI
                                                </div>
                                                <h4 class="text-xl font-black text-slate-950 tracking-tight uppercase">
                                                    {{ $assignedLocName ?: 'KANTONG PARKIR A (AREA KAMPUS UTAMA SMAN 1 CIAWI)' }}
                                                </h4>
                                                <p class="text-xs text-amber-950 font-medium leading-relaxed">
                                                    Selamat! Berdasarkan evaluasi 3 kriteria prioritas, kontingen Anda berhasil mendapatkan fasilitas parkir prioritas utama di area kampus depan SMAN 1 Ciawi.
                                                </p>

                                                <!-- 3-Pill Breakdown -->
                                                <div class="pt-2 flex flex-wrap gap-2 text-[11px] font-bold">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-slate-900 border border-amber-300 shadow-2xs">
                                                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                                        Semua Cabang Diikuti ({{ $criteria['categories_count'] ?? $registration->teams->pluck('competition_category_id')->unique()->count() }}/{{ $criteria['categories_total'] ?? 5 }} Cabang)
                                                    </span>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-slate-900 border border-amber-300 shadow-2xs">
                                                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                                        Kuota Regu Maksimal ({{ $registration->total_teams_count }} Regu Lengkap)
                                                    </span>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-slate-900 border border-amber-300 shadow-2xs">
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
                                    <!-- Regular Location Banner -->
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-3xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-blue-700 bg-blue-100 px-2.5 py-0.5 rounded-full">
                                                LOKASI PARKIR RESMI DISETUJUI
                                            </span>
                                            <h4 class="text-base font-black text-slate-900 mt-1 uppercase">
                                                {{ $assignedLocation->name }}
                                            </h4>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $assignedLocation->address }}</p>
                                        </div>
                                        @if($assignedLocation->gmaps_url)
                                            <a href="{{ $assignedLocation->gmaps_url }}" target="_blank" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                                                <i class="fa-solid fa-map-location-dot"></i> Buka Petunjuk Rute &rarr;
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <!-- Pending Notice -->
                                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 flex items-start gap-3 text-slate-700">
                                        <i class="fa-solid fa-clock-rotate-left text-amber-500 text-xl mt-0.5"></i>
                                        <div>
                                            <h4 class="font-bold text-sm text-slate-900">Data Kendaraan Terdaftar — Menunggu Penetapan Lokasi</h4>
                                            <p class="text-xs text-slate-600 mt-0.5">
                                                Panitia sedang memproses penempatan kantong parkir berdasarkan skor prioritas kontingen. Peringkat skor prioritas sekolah Anda: <strong>#{{ $registration->parking_priority_rank ?? '-' }}</strong>.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Vehicle Cards Grid -->
                                @if($hasTickets)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($parkingTickets as $ticket)
                                            <div class="bg-white border border-slate-200 hover:border-slate-300 rounded-2xl p-4 shadow-2xs transition flex flex-col justify-between">
                                                <div>
                                                    <div class="flex items-center justify-between gap-2 mb-2">
                                                        @php
                                                            $vBadgeColor = match($ticket->vehicle_type) {
                                                                'r2_motor' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                                'r4_elf' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                                'r4_bus' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                                default => 'bg-blue-100 text-blue-800 border-blue-200',
                                                            };
                                                        @endphp
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $vBadgeColor }}">
                                                            {{ $ticket->vehicle_type_label }}
                                                        </span>
                                                        <span class="font-mono text-xs font-black text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                                            {{ $ticket->ticket_code }}
                                                        </span>
                                                    </div>

                                                    <div class="w-full h-4 barcode-strip rounded-xs mb-3 opacity-80"></div>

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
                                                        <div class="p-1 bg-white border border-slate-200 rounded-lg shrink-0">
                                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode(route('parking.public-ticket', $ticket->qr_code_token)) }}" alt="QR" class="w-12 h-12">
                                                        </div>
                                                    </div>

                                                    <div class="text-[11px] text-slate-600 space-y-0.5 mb-3 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                                        <div>Pengemudi: <strong class="text-slate-800">{{ $ticket->driver_name ?: '-' }}</strong></div>
                                                        @if($ticket->driver_phone)
                                                            <div>WhatsApp: <strong class="text-slate-800 font-mono">{{ $ticket->driver_phone }}</strong></div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <a href="{{ route('parking.public-ticket', $ticket->qr_code_token) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center gap-1.5">
                                                            <i class="fa-solid fa-qrcode text-[10px] text-yellow-400"></i> Buka E-Tiket
                                                        </a>
                                                        @if($ticket->location && $ticket->location->gmaps_url)
                                                            <a href="{{ $ticket->location->gmaps_url }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1">
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
                                @else
                                    <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                                        <i class="fa-solid fa-car-side text-4xl text-slate-300 mb-2"></i>
                                        <h4 class="font-bold text-sm text-slate-700">Belum Ada Kendaraan yang Didaftarkan</h4>
                                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                            Sekolah Anda memiliki kuota {{ $r4Allocated }} Mobil dan {{ $r2Allocated }} Motor. Daftarkan sekarang untuk mendapatkan tiket resmi.
                                        </p>
                                        <button type="button" @click="openModal()" class="mt-4 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow transition">
                                            Daftarkan Kendaraan Sekarang
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- TAB 3: PANDUAN & KONTAK PANITIA -->
                        <div x-show="activeTab === 'guide'" x-cloak class="space-y-5">
                            <div class="pb-3 border-b border-slate-100">
                                <h3 class="font-black text-slate-900 text-base">Panduan Teknis & Bantuan Panitia</h3>
                                <p class="text-xs text-slate-500">Informasi penting pelaksanaan lomba Sua Bhakti Berkarya IV 2026.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50 space-y-2">
                                    <div class="flex items-center gap-2 font-bold text-sm text-slate-900">
                                        <i class="fa-solid fa-book-bookmark text-rose-600"></i>
                                        <span>Buku Petunjuk Pelaksanaan (Juklak-Juknis)</span>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        Pelajari tata tertib lomba, kriteria penilaian juri, dan ketentuan teknis tiap cabang lomba.
                                    </p>
                                    <a href="{{ route('lomba.index') }}#juklak" class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-700 pt-1">
                                        Lihat Juklak Juknis &rarr;
                                    </a>
                                </div>

                                <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50 space-y-2">
                                    <div class="flex items-center gap-2 font-bold text-sm text-slate-900">
                                        <i class="fa-solid fa-chart-simple text-emerald-600"></i>
                                        <span>Live Klasemen & Papan Skor Realtime</span>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        Pantau perolehan nilai dan peringkat kontingen secara transparan saat hari-H lomba.
                                    </p>
                                    <a href="{{ route('lomba.scoreboard') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700 pt-1">
                                        Buka Live Scoreboard &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ================= 4. MODAL POPUP REGISTRASI KENDARAAN ================= -->
                @if($isVerified)
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

                                <!-- Quota Alert -->
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
                                                    <select :name="'vehicles[' + index + '][vehicle_type]'" x-model="veh.vehicle_type" required class="w-full text-xs font-bold bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                                        <option value="r4_car">Roda 4 Mobil Pribadi / MPV / SUV</option>
                                                        <option value="r4_elf">Roda 4+ Minibus (Elf / HiAce)</option>
                                                        <option value="r4_bus">Roda 6+ Bus Besar Pariwisata</option>
                                                        <option value="r2_motor">Roda 2 Sepeda Motor</option>
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
                                                    <input type="text" :name="'vehicles[' + index + '][vehicle_brand]'" x-model="veh.vehicle_brand" placeholder="Contoh: Toyota Avanza / Isuzu Elf / Vario" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:border-red-500">
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

                                <div class="flex items-center justify-between pt-2">
                                    <button type="button" @click="addVehicle()" :disabled="!canAddMore" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-50 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                                        <i class="fa-solid fa-plus text-rose-500"></i> Tambah Kendaraan Lainnya
                                    </button>
                                    <span class="text-[11px] text-slate-400 italic" x-show="!canAddMore">Maksimal sesuai kuota yang tersedia.</span>
                                </div>

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
                @endif

            </div>
        @elseif($code)
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm max-w-lg mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-3xl mx-auto mb-4 border border-rose-100">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Kode Pendaftaran Tidak Ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                    Kode registrasi <code class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">{{ $code }}</code> tidak terdaftar dalam sistem. Mohon periksa kembali kode Anda (contoh: <code>SBB-U76922B</code>).
                </p>
                <div class="mt-6">
                    <a href="{{ route('lomba.status') }}" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-arrow-rotate-left"></i> Coba Kode Lain
                    </a>
                </div>
            </div>
        @else
            <!-- State awal saat baru membuka /lomba/status tanpa parameter kode -->
            <div class="bg-white rounded-3xl p-10 sm:p-12 text-center border border-slate-200 shadow-sm max-w-xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-500/10 to-rose-500/20 text-rose-600 flex items-center justify-center text-3xl mx-auto border border-rose-200/60 shadow-inner">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Periksa Status Kontingen Anda</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                        Masukkan kode pendaftaran resmi yang Anda terima saat mengirimkan formulir lomba untuk memeriksa status verifikasi, mengunduh e-Kwitansi resmi, dan mencetak Kartu Peserta.
                    </p>
                </div>
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
    function statusPortalApp(config) {
        return {
            activeTab: 'teams',
            showModal: config.autoOpenModal || false,
            r4Remaining: config.r4Remaining || 0,
            r2Remaining: config.r2Remaining || 0,
            copied: false,
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
            copyCode(text) {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2500);
                    });
                }
            },
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
