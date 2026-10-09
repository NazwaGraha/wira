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

                    <!-- Daftar Tim & Cabang Lomba -->
                    <div>
                        <h4 class="font-bold text-sm text-slate-800 uppercase tracking-wider mb-3">Cabang Lomba yang Didaftarkan</h4>
                        <div class="border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100">
                            @foreach($registration->teams as $team)
                                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $team->category->name }} ({{ $team->category->gender_category }})</div>
                                        <div class="text-xs text-slate-500">Nama Regu: <span class="font-semibold text-slate-700">{{ $team->team_name }}</span></div>
                                    </div>
                                    <span class="bg-slate-100 text-slate-600 text-xs font-semibold px-3 py-1 rounded-lg">
                                        {{ $team->category->scoring_type == 'standard_time' ? 'Uji Ketangkasan / Waktu' : 'Uji Penilaian Teknis' }}
                                    </span>
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
@endsection
