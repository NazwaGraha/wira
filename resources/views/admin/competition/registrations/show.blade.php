@extends('layouts.admin')

@section('title', 'Detail Pendaftaran - ' . $registration->school_name)
@section('page_title', 'Detail Pendaftaran Kontingen')

@section('top_actions')
    <a href="{{ route('admin.competition-registrations.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
        &larr; Kembali
    </a>
@endsection

@section('content')
@if($registration->hasPendingFollowup())
    <div class="mb-6 p-5 sm:p-6 bg-gradient-to-r from-amber-500 via-amber-600 to-orange-500 text-white rounded-2xl shadow-lg border border-amber-400 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl shrink-0 text-white">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-white text-amber-800 shadow-xs">Pemberitahuan Penting</span>
                    <span class="text-xs text-amber-100 font-bold">Pendaftaran Susulan Masuk</span>
                </div>
                <h3 class="text-lg font-black mt-1">Sekolah ini melakukan pendaftaran regu susulan!</h3>
                <p class="text-xs text-amber-50 mt-1 max-w-2xl leading-relaxed">
                    Terdapat penambahan regu baru dengan tambahan pembayaran sebesar <strong class="text-white underline">Rp {{ number_format($registration->getPendingFollowupAmount(), 0, ',', '.') }}</strong> yang memerlukan verifikasi bukti transfer. Bukti transfer susulan telah terlampir di kolom bukti pembayaran di bawah.
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <form action="{{ route('admin.competition-registrations.verify-followup', $registration->id) }}" method="POST" onsubmit="return confirm('Sahkan & verifikasi pembayaran susulan sebesar Rp {{ number_format($registration->getPendingFollowupAmount(), 0, ',', '.') }}? Kwitansi dan Kartu Peserta akan mencakup seluruh regu baru.');">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-white text-amber-800 hover:bg-amber-50 font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check-double text-amber-600"></i>
                    <span>Verifikasi Pembayaran Susulan (Lunas)</span>
                </button>
            </form>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left Details -->
    <div class="lg:col-span-8 space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-start gap-4 pb-6 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded">{{ $registration->registration_code }}</span>
                        <span class="px-2.5 py-0.5 rounded text-xs font-extrabold {{ $registration->registration_wave == 2 ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                            {{ $registration->registration_wave == 2 ? ($registration->event->wave2_name ?: 'Gelombang 2') : ($registration->event->wave1_name ?: 'Gelombang 1') }}
                        </span>
                    </div>
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
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-black text-slate-900 text-sm">{{ $team->category->name }} ({{ $team->category->gender_category }})</div>
                                    <div class="text-xs text-slate-500 mt-0.5">Nama Regu: <strong>{{ $team->team_name }}</strong></div>
                                </div>
                                <span class="bg-white border border-slate-200 text-slate-700 font-mono text-xs font-bold px-3 py-1 rounded-lg">
                                    Rp {{ number_format($team->category ? $team->category->getFeeForWave($registration->registration_wave ?: 1) : floatval($registration->event->registration_fee ?: 150000), 0, ',', '.') }}
                                </span>
                            </div>

                            @php
                                $members = is_array($team->members_list) ? $team->members_list : (is_string($team->members_list) ? json_decode($team->members_list, true) : []);
                            @endphp
                            @if(!empty($members) || $team->members_file_url)
                                <div class="bg-white border border-slate-200 rounded-xl p-3 text-xs space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-700 flex items-center gap-1.5 text-xs">
                                            <i class="fa-solid fa-users text-blue-600"></i>
                                            Daftar Anggota / Peserta ({{ count($members) }} Orang):
                                        </span>
                                        @if($team->members_file_url)
                                            <a href="{{ $team->members_file_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                                                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                                                <span>Download File Excel</span>
                                            </a>
                                        @endif
                                    </div>
                                    @if(!empty($members))
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-1.5 pt-1">
                                            @foreach($members as $mIdx => $m)
                                                @php
                                                    $mName = is_array($m) ? ($m['name'] ?? ($m['nama'] ?? 'Peserta')) : $m;
                                                    $mRole = is_array($m) && !empty($m['role'] ?? ($m['peran'] ?? '')) ? ' (' . ($m['role'] ?? $m['peran']) . ')' : '';
                                                @endphp
                                                <div class="bg-slate-50 border border-slate-200/80 px-2.5 py-1 rounded-md text-[11px] flex items-center justify-between">
                                                    <span class="truncate font-medium text-slate-800"><span class="font-bold text-slate-400 mr-1">{{ $mIdx + 1 }}.</span>{{ $mName }}</span>
                                                    @if($mRole)
                                                        <span class="text-[10px] text-slate-500 font-semibold">{{ $mRole }}</span>
                                                    @endif
                                                </div>
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

    <!-- Right Actions & Proof -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Verification Box -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider mb-4">Aksi Verifikasi</h3>
            
            <div class="bg-slate-50 p-4 rounded-xl mb-4 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Total Nominal Tagihan:</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $registration->registration_wave == 2 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        Gelombang {{ $registration->registration_wave ?? 1 }}
                    </span>
                </div>
                <div class="text-xl font-black text-red-600 font-mono mt-1">Rp {{ number_format($registration->total_payment, 0, ',', '.') }}</div>

                <button type="button" onclick="document.getElementById('modalEditWavePayment').classList.remove('hidden')" class="mt-3 w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-bold shadow-2xs transition">
                    <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                    <span>Ubah Gelombang / Nominal</span>
                </button>
            </div>

            @if($registration->hasPendingFollowup())
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl mb-4 text-xs space-y-2.5">
                    <div class="flex items-center gap-2 font-bold text-amber-800">
                        <i class="fa-solid fa-bell text-amber-600"></i>
                        <span>Pendaftaran Susulan Perlu Verifikasi!</span>
                    </div>
                    <div class="text-slate-600">
                        Nominal Susulan: <strong class="text-amber-800 font-mono text-sm">Rp {{ number_format($registration->getPendingFollowupAmount(), 0, ',', '.') }}</strong>
                    </div>
                    <form action="{{ route('admin.competition-registrations.verify-followup', $registration->id) }}" method="POST" onsubmit="return confirm('Verifikasi pembayaran susulan sebesar Rp {{ number_format($registration->getPendingFollowupAmount(), 0, ',', '.') }}?');">
                        @csrf
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 rounded-lg transition text-xs flex items-center justify-center gap-2 shadow-xs">
                            <i class="fa-solid fa-check-double"></i> Verifikasi Susulan (Lunas)
                        </button>
                    </form>
                </div>
            @endif

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

                    @if($registration->advisor_email)
                        <form action="{{ route('admin.competition-registrations.resend-email', $registration->id) }}" method="POST" onsubmit="return confirm('Kirimkan ulang e-Kwitansi & Kartu Peserta ke email {{ $registration->advisor_email }}?');">
                            @csrf
                            <button type="submit" class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2 text-xs">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Ulang Email ke Pembina
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- Payment Proof Image Box -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            @php $allProofs = $registration->getAllPaymentProofs(); @endphp
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider">Bukti Pembayaran</h3>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ count($allProofs) }} File</span>
            </div>

            @if(count($allProofs) > 0)
                <div class="space-y-5">
                    @foreach($allProofs as $pIdx => $proof)
                        <div class="p-3.5 rounded-xl border {{ ($proof['type'] ?? '') === 'followup' ? 'bg-amber-50/50 border-amber-200' : 'bg-slate-50 border-slate-200' }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-xs font-black {{ ($proof['type'] ?? '') === 'followup' ? 'text-amber-900' : 'text-slate-800' }}">{{ $proof['label'] }}</span>
                                    @if(($proof['type'] ?? '') === 'followup')
                                        @if(($proof['status'] ?? '') === 'pending')
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-amber-500 text-white animate-pulse">Menunggu Verif</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-600 text-white">Terverifikasi</span>
                                        @endif
                                    @endif
                                </div>
                                <span class="font-mono text-xs font-black text-slate-800">Rp {{ number_format($proof['amount'] ?? 0, 0, ',', '.') }}</span>
                            </div>

                            @if(!empty($proof['date']))
                                <div class="text-[11px] text-slate-400 mb-2 flex items-center gap-1">
                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                    <span>{{ \Carbon\Carbon::parse($proof['date'])->translatedFormat('d M Y, H:i') }} WIB</span>
                                </div>
                            @endif

                            @php
                                $isPdf = str_ends_with(strtolower($proof['path'] ?? ''), '.pdf');
                            @endphp

                            @if($isPdf)
                                <div class="rounded-xl p-6 bg-slate-100 border border-slate-200 text-center">
                                    <i class="fa-solid fa-file-pdf text-rose-500 text-4xl mb-2"></i>
                                    <div class="text-xs font-bold text-slate-700">Dokumen Bukti Transfer (PDF)</div>
                                    <div class="mt-3">
                                        <a href="{{ $proof['url'] }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-rose-600 text-white font-bold text-xs hover:bg-rose-700 transition shadow-xs">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Dokumen PDF
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <a href="{{ $proof['url'] }}" target="_blank">
                                        <img src="{{ $proof['url'] }}" class="w-full h-48 object-cover hover:scale-105 transition duration-300" alt="{{ $proof['label'] }}">
                                    </a>
                                </div>
                                <div class="mt-2 text-center">
                                    <a href="{{ $proof['url'] }}" target="_blank" class="text-[11px] text-blue-600 hover:underline font-bold inline-flex items-center gap-1">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka Gambar Resolusi Penuh
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-xs text-slate-400 italic text-center py-6">Tidak ada lampiran bukti transfer</div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Ubah Gelombang & Tagihan -->
@php
    $autoWave1Total = 0;
    $autoWave2Total = 0;
    foreach ($registration->teams as $t) {
        $c = $t->category;
        $autoWave1Total += $c ? $c->getFeeForWave(1) : floatval($registration->event->registration_fee ?: 150000);
        $autoWave2Total += $c ? $c->getFeeForWave(2) : floatval($registration->event->registration_fee ?: 150000);
    }
@endphp
<div id="modalEditWavePayment" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 sm:p-7 border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-base text-slate-900">Ubah Gelombang & Tagihan</h4>
                    <p class="text-xs text-slate-500 font-medium">{{ $registration->school_name }} ({{ $registration->registration_code }})</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modalEditWavePayment').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.competition-registrations.update', $registration->id) }}" method="POST" class="mt-5 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Gelombang Pendaftaran</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex flex-col p-3.5 border-2 rounded-xl cursor-pointer transition hover:border-amber-400 has-checked:border-amber-500 has-checked:bg-amber-50/50 border-slate-200">
                        <input type="radio" name="registration_wave" value="1" {{ ($registration->registration_wave ?? 1) == 1 ? 'checked' : '' }} onchange="updateModalAmountPreview(1)" class="sr-only">
                        <span class="text-xs font-extrabold text-slate-900 flex items-center justify-between">
                            <span>Gelombang 1</span>
                            <span class="w-2.5 h-2.5 rounded-full border border-slate-300 peer-checked:bg-amber-500"></span>
                        </span>
                        <span class="text-[11px] text-slate-500 mt-1">Tarif Gel. 1 (Total: Rp {{ number_format($autoWave1Total, 0, ',', '.') }})</span>
                    </label>

                    <label class="relative flex flex-col p-3.5 border-2 rounded-xl cursor-pointer transition hover:border-emerald-400 has-checked:border-emerald-500 has-checked:bg-emerald-50/50 border-slate-200">
                        <input type="radio" name="registration_wave" value="2" {{ ($registration->registration_wave ?? 1) == 2 ? 'checked' : '' }} onchange="updateModalAmountPreview(2)" class="sr-only">
                        <span class="text-xs font-extrabold text-slate-900 flex items-center justify-between">
                            <span>Gelombang 2</span>
                            <span class="w-2.5 h-2.5 rounded-full border border-slate-300 peer-checked:bg-emerald-500"></span>
                        </span>
                        <span class="text-[11px] text-slate-500 mt-1">Tarif Gel. 2 (Total: Rp {{ number_format($autoWave2Total, 0, ',', '.') }})</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Total Nominal Tagihan (Rp)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" id="inputTotalPayment" name="total_payment" value="{{ intval($registration->total_payment) }}" min="0" step="1000" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-black font-mono text-slate-900 focus:bg-white focus:outline-none focus:border-blue-500 transition">
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5 leading-relaxed">
                    Nominal ini akan tertera pada kolom pembayaran admin serta e-Kwitansi resmi peserta.
                </p>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" id="checkRecalculate" name="recalculate_payment" value="1" checked onchange="toggleManualInput(this)" class="mt-0.5 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                    <span class="text-xs text-slate-600 font-medium">
                        <strong class="text-slate-800">Hitung Otomatis</strong>: Sinkronkan total nominal pembayaran secara otomatis sesuai akumulasi tarif cabang lomba pada gelombang yang dipilih.
                    </span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditWavePayment').classList.add('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-black bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const wave1AutoAmount = {{ $autoWave1Total }};
    const wave2AutoAmount = {{ $autoWave2Total }};

    function updateModalAmountPreview(wave) {
        const check = document.getElementById('checkRecalculate');
        const input = document.getElementById('inputTotalPayment');
        if (check && check.checked && input) {
            input.value = (wave === 1) ? wave1AutoAmount : wave2AutoAmount;
        }
    }

    function toggleManualInput(checkbox) {
        const input = document.getElementById('inputTotalPayment');
        if (!input) return;
        if (checkbox.checked) {
            const selectedWave = document.querySelector('input[name="registration_wave"]:checked');
            const waveVal = selectedWave ? parseInt(selectedWave.value) : 1;
            input.value = (waveVal === 1) ? wave1AutoAmount : wave2AutoAmount;
        }
    }
</script>
@endsection
