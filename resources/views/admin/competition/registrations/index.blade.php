@extends('layouts.admin')

@section('title', 'Verifikasi Pendaftar Lomba')
@section('page_title', 'Verifikasi & Administrasi Kontingen Lomba')

@section('top_actions')
    <a href="{{ route('lomba.register') }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Form Pendaftaran
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Metric Counts Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.competition-registrations.index') }}" class="p-5 rounded-2xl border transition {{ !$status ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold {{ !$status ? 'text-slate-400' : 'text-slate-500' }}">Total Pendaftar</div>
            <div class="text-2xl font-black mt-1">{{ $counts['all'] }}</div>
        </a>

        <a href="{{ route('admin.competition-registrations.index', ['status' => 'pending']) }}" class="p-5 rounded-2xl border transition {{ $status == 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold {{ $status == 'pending' ? 'text-amber-100' : 'text-slate-500' }}">Perlu Verifikasi</div>
            <div class="text-2xl font-black mt-1">{{ $counts['pending'] }}</div>
        </a>

        <a href="{{ route('admin.competition-registrations.index', ['status' => 'verified']) }}" class="p-5 rounded-2xl border transition {{ $status == 'verified' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold {{ $status == 'verified' ? 'text-emerald-100' : 'text-slate-500' }}">Terverifikasi (Lunas)</div>
            <div class="text-2xl font-black mt-1">{{ $counts['verified'] }}</div>
        </a>

        <a href="{{ route('admin.competition-registrations.index', ['status' => 'rejected']) }}" class="p-5 rounded-2xl border transition {{ $status == 'rejected' ? 'bg-rose-600 text-white border-rose-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold {{ $status == 'rejected' ? 'text-rose-100' : 'text-slate-500' }}">Ditolak</div>
            <div class="text-2xl font-black mt-1">{{ $counts['rejected'] }}</div>
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <!-- Filter and Search -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Filter Tingkat:</span>
                <a href="{{ route('admin.competition-registrations.index', array_merge(request()->query(), ['level' => ''])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ !$level ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
                <a href="{{ route('admin.competition-registrations.index', array_merge(request()->query(), ['level' => 'Mula'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Mula' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Mula (SD)</a>
                <a href="{{ route('admin.competition-registrations.index', array_merge(request()->query(), ['level' => 'Madya'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Madya' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Madya (SMP)</a>
                <a href="{{ route('admin.competition-registrations.index', array_merge(request()->query(), ['level' => 'Wira'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $level == 'Wira' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Wira (SMA)</a>
            </div>

            <form action="{{ route('admin.competition-registrations.index') }}" method="GET" class="w-full md:w-auto flex gap-2">
                @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
                @if($level) <input type="hidden" name="level" value="{{ $level }}"> @endif
                <div class="relative w-full md:w-64">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari Sekolah / Kode..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-red-500">
                </div>
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-bold">Cari</button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-bold border-y border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Kode & Sekolah</th>
                        <th class="px-6 py-4">Tingkat & Cabang</th>
                        <th class="px-6 py-4">Pembina / Kontak</th>
                        <th class="px-6 py-4">Pembayaran</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($registrations as $reg)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <span class="font-mono text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">{{ $reg->registration_code }}</span>
                                <div class="font-extrabold text-slate-900 text-sm mt-1 uppercase">{{ $reg->school_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $reg->created_at ? $reg->created_at->translatedFormat('d M Y, H:i') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-slate-100 text-slate-700 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border">
                                    PMR {{ $reg->level }}
                                </span>
                                <div class="text-xs text-slate-600 font-semibold mt-1">
                                    {{ $reg->teams->count() }} Cabang Lomba
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-xs">{{ $reg->advisor_name }}</div>
                                <div class="text-xs text-slate-500"><i class="fa-brands fa-whatsapp text-emerald-500"></i> {{ $reg->advisor_phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-mono font-bold text-slate-900 text-xs">Rp {{ number_format($reg->total_payment, 0, ',', '.') }}</div>
                                @if($reg->payment_proof)
                                    <a href="{{ Storage::url($reg->payment_proof) }}" target="_blank" class="text-[11px] text-blue-600 hover:underline inline-flex items-center gap-1 mt-0.5 font-semibold">
                                        <i class="fa-solid fa-receipt"></i> Bukti Bayar
                                    </a>
                                @else
                                    <span class="text-[10px] text-slate-400">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($reg->status == 'verified')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-extrabold px-3 py-1 rounded-full flex items-center gap-1 w-max">
                                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Lunas
                                    </span>
                                @elseif($reg->status == 'pending')
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-extrabold px-3 py-1 rounded-full flex items-center gap-1 w-max">
                                        <i class="fa-solid fa-clock text-amber-500"></i> Pending
                                    </span>
                                @else
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-extrabold px-3 py-1 rounded-full flex items-center gap-1 w-max">
                                        <i class="fa-solid fa-circle-xmark text-rose-500"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.competition-registrations.show', $reg->id) }}" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-800 hover:text-white transition" title="Lihat Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    @if($reg->status == 'pending')
                                        <form action="{{ route('admin.competition-registrations.verify', $reg->id) }}" method="POST" onsubmit="return confirm('Verifikasi dan setujui pendaftaran ini?');">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition" title="Setujui / Verifikasi">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($reg->status == 'verified')
                                        <a href="{{ url('/lomba/kwitansi/' . ($reg->registration_code ?: $reg->id)) }}" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition" title="Cetak Kwitansi">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                        <a href="{{ url('/lomba/kartu-peserta/' . ($reg->registration_code ?: $reg->id)) }}" target="_blank" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition" title="Cetak Kartu Peserta">
                                            <i class="fa-solid fa-id-card"></i>
                                        </a>
                                    @endif

                                    <form action="{{ route('admin.competition-registrations.destroy', $reg->id) }}" method="POST" onsubmit="return confirm('Hapus data pendaftaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-2"></i>
                                <div class="font-bold text-slate-600">Belum ada data pendaftar</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $registrations->links() }}
        </div>
    </div>

</div>
@endsection
