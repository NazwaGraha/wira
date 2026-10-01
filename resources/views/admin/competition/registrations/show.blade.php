@extends('layouts.admin')

@section('title', 'Detail Pendaftaran - ' . $registration->school_name)
@section('page_title', 'Detail Pendaftaran Kontingen')

@section('top_actions')
    <a href="{{ route('admin.competition-registrations.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
        &larr; Kembali
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left Details -->
    <div class="lg:col-span-8 space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-start gap-4 pb-6 border-b border-slate-100">
                <div>
                    <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded">{{ $registration->registration_code }}</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-2 uppercase">{{ $registration->school_name }}</h2>
                    <div class="text-xs text-slate-500 font-semibold mt-1">
                        Tingkat: <span class="text-red-600 font-bold">PMR {{ $registration->level }}</span> &bull; Terdaftar: {{ $registration->created_at ? $registration->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                    </div>
                </div>

                <div>
                    @if($registration->status == 'verified')
                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-extrabold px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i> LUNAS / TERVERIFIKASI
                        </span>
                    @elseif($registration->status == 'pending')
                        <span class="bg-amber-50 text-amber-700 border border-amber-200 text-xs font-extrabold px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-clock text-amber-500"></i> MENUNGGU VERIFIKASI
                        </span>
                    @else
                        <span class="bg-rose-50 text-rose-700 border border-rose-200 text-xs font-extrabold px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-circle-xmark text-rose-500"></i> DITOLAK
                        </span>
                    @endif
                </div>
            </div>

            <!-- Identitas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-6 border-b border-slate-100 text-xs">
                <div>
                    <div class="font-bold text-slate-400 uppercase tracking-wider">Nama Pembina</div>
                    <div class="font-extrabold text-slate-800 text-sm mt-0.5">{{ $registration->advisor_name }}</div>
                </div>
                <div>
                    <div class="font-bold text-slate-400 uppercase tracking-wider">WhatsApp Pembina</div>
                    <div class="font-bold text-slate-800 text-sm mt-0.5"><i class="fa-brands fa-whatsapp text-emerald-500"></i> {{ $registration->advisor_phone }}</div>
                </div>
                <div>
                    <div class="font-bold text-slate-400 uppercase tracking-wider">Email</div>
                    <div class="font-medium text-slate-700 mt-0.5">{{ $registration->advisor_email ?: '-' }}</div>
                </div>
                <div>
                    <div class="font-bold text-slate-400 uppercase tracking-wider">Alamat Sekolah</div>
                    <div class="font-medium text-slate-700 mt-0.5">{{ $registration->school_address ?: '-' }}</div>
                </div>
            </div>

            <!-- Teams & Categories -->
            <div class="pt-6">
                <h3 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider mb-4">Cabang Mata Lomba Terdaftar ({{ $registration->teams->count() }})</h3>
                <div class="space-y-3">
                    @foreach($registration->teams as $team)
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                            <div>
                                <div class="font-black text-slate-900 text-sm">{{ $team->category->name }} ({{ $team->category->gender_category }})</div>
                                <div class="text-xs text-slate-500 mt-0.5">Nama Regu: <strong>{{ $team->team_name }}</strong></div>
                            </div>
                            <span class="bg-white border border-slate-200 text-slate-700 font-mono text-xs font-bold px-3 py-1 rounded-lg">
                                Rp {{ number_format($team->category->registration_fee ?: ($registration->event->registration_fee ?: 150000), 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Right Actions & Proof -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Verification Box -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider mb-4">Aksi Verifikasi</h3>
            
            <div class="bg-slate-50 p-4 rounded-xl mb-4 text-xs">
                <div class="text-slate-500">Total Nominal Tagihan:</div>
                <div class="text-xl font-black text-red-600 font-mono mt-0.5">Rp {{ number_format($registration->total_payment, 0, ',', '.') }}</div>
            </div>

            @if($registration->status == 'pending')
                <div class="space-y-3">
                    <form action="{{ route('admin.competition-registrations.verify', $registration->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-md transition flex items-center justify-center gap-2 text-xs">
                            <i class="fa-solid fa-check"></i> SETUJUI / LUNAS
                        </button>
                    </form>

                    <form action="{{ route('admin.competition-registrations.reject', $registration->id) }}" method="POST">
                        @csrf
                        <input type="text" name="rejection_reason" placeholder="Alasan penolakan..." required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl mb-2 focus:outline-none focus:border-red-500">
                        <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold py-2.5 rounded-xl transition text-xs flex items-center justify-center gap-2 border border-rose-200">
                            <i class="fa-solid fa-xmark"></i> Tolak Pendaftaran
                        </button>
                    </form>
                </div>
            @else
                <div class="space-y-2">
                    <a href="{{ url('/lomba/kwitansi/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl shadow transition flex items-center justify-center gap-2 text-xs">
                        <i class="fa-solid fa-receipt"></i> Cetak Kwitansi (PDF)
                    </a>
                    <a href="{{ url('/lomba/kartu-peserta/' . ($registration->registration_code ?: $registration->id)) }}" target="_blank" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-xl shadow transition flex items-center justify-center gap-2 text-xs">
                        <i class="fa-solid fa-id-card"></i> Cetak Kartu Peserta
                    </a>
                </div>
            @endif
        </div>

        <!-- Payment Proof Image Box -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider mb-4">Bukti Pembayaran</h3>
            @if($registration->payment_proof)
                <div class="rounded-xl overflow-hidden border border-slate-200">
                    <a href="{{ Storage::url($registration->payment_proof) }}" target="_blank">
                        <img src="{{ Storage::url($registration->payment_proof) }}" class="w-full h-auto object-cover hover:scale-105 transition duration-300" alt="Bukti Transfer">
                    </a>
                </div>
                <div class="text-[11px] text-slate-400 mt-2 text-center">Klik gambar untuk melihat resolusi penuh</div>
            @else
                <div class="text-xs text-slate-400 italic text-center py-6">Tidak ada lampiran bukti transfer</div>
            @endif
        </div>
    </div>

</div>
@endsection
