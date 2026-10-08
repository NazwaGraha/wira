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

    <!-- Event Selector & History Bar -->
    @include('admin.competition.partials.event-selector')

    <!-- Metric Counts Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.competition-registrations.index', request()->except('status')) }}" class="p-5 rounded-2xl border transition {{ !$status ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold {{ !$status ? 'text-slate-400' : 'text-slate-500' }}">Total Pendaftar</div>
            <div class="text-2xl font-black mt-1">{{ $counts['all'] }}</div>
        </a>

        <a href="{{ route('admin.competition-registrations.index', array_merge(request()->query(), ['status' => 'pending'])) }}" class="p-5 rounded-2xl border transition {{ $status == 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold {{ $status == 'pending' ? 'text-amber-100' : 'text-slate-500' }}">Perlu Verifikasi</div>
            <div class="text-2xl font-black mt-1">{{ $counts['pending'] }}</div>
        </a>

        <a href="{{ route('admin.competition-registrations.index', array_merge(request()->query(), ['status' => 'verified'])) }}" class="p-5 rounded-2xl border transition {{ $status == 'verified' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold {{ $status == 'verified' ? 'text-emerald-100' : 'text-slate-500' }}">Terverifikasi (Lunas)</div>
            <div class="text-2xl font-black mt-1">{{ $counts['verified'] }}</div>
        </a>

        <a href="{{ route('admin.competition-registrations.index', array_merge(request()->query(), ['status' => 'rejected'])) }}" class="p-5 rounded-2xl border transition {{ $status == 'rejected' ? 'bg-rose-600 text-white border-rose-600 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}">
            <div class="text-xs font-semibold {{ $status == 'rejected' ? 'text-rose-100' : 'text-slate-500' }}">Ditolak</div>
            <div class="text-2xl font-black mt-1">{{ $counts['rejected'] }}</div>
        </a>
    </div>

    <!-- History & Statistics Summary per Edition / Year -->
    <div x-data="{ openStats: false, openSchools: false }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/70 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg shrink-0">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm">Rekapitulasi Data Peserta & Asal Sekolah</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Analisis jumlah pendaftar tahun ke tahun dan daftar sekolah yang berpartisipasi</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="openStats = !openStats" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border" :class="openStats ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Statistik Tiap Tahun ({{ count($eventStats) }} Edisi)</span>
                    <i class="fa-solid text-[10px]" :class="openStats ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <button type="button" @click="openSchools = !openSchools" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border" :class="openSchools ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                    <i class="fa-solid fa-school"></i>
                    <span>Daftar Sekolah ({{ $schoolsSummary->count() }})</span>
                    <i class="fa-solid text-[10px]" :class="openSchools ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
            </div>
        </div>

        <!-- Section 1: Comparison Across Years / Editions -->
        <div x-show="openStats" x-collapse class="p-5 border-b border-slate-100 bg-slate-50/30">
            <div class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-3 flex items-center gap-2">
                <i class="fa-solid fa-layer-group text-indigo-500"></i> Perbandingan Partisipasi Antar Edisi Lomba
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase">
                        <tr>
                            <th class="p-3 rounded-l-lg">Edisi / Tahun Lomba</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Jumlah Sekolah</th>
                            <th class="p-3 text-center">Total Regu</th>
                            <th class="p-3 text-center">Mula (SD)</th>
                            <th class="p-3 text-center">Madya (SMP)</th>
                            <th class="p-3 text-center">Wira (SMA)</th>
                            <th class="p-3 text-center rounded-r-lg">Terverifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($eventStats as $stat)
                            <tr class="hover:bg-indigo-50/40 transition {{ isset($event) && $event->id == $stat['event']->id ? 'bg-indigo-50/60 font-bold' : '' }}">
                                <td class="p-3">
                                    <div class="font-extrabold text-slate-900">{{ $stat['event']->title }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $stat['event']->start_date ? $stat['event']->start_date->translatedFormat('d M Y') : '-' }}</div>
                                </td>
                                <td class="p-3 text-center">
                                    @if($stat['event']->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">AKTIF</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">ARSIP</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center font-extrabold text-slate-800 text-sm">
                                    {{ $stat['total_schools'] }}
                                </td>
                                <td class="p-3 text-center font-black text-indigo-700 text-sm">
                                    {{ $stat['total_teams'] }}
                                </td>
                                <td class="p-3 text-center text-blue-700 font-bold">
                                    {{ $stat['mula'] }}
                                </td>
                                <td class="p-3 text-center text-red-700 font-bold">
                                    {{ $stat['madya'] }}
                                </td>
                                <td class="p-3 text-center text-amber-700 font-bold">
                                    {{ $stat['wira'] }}
                                </td>
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $stat['verified_registrations'] }} / {{ $stat['total_registrations'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-4 text-center text-slate-400">Belum ada data edisi lomba.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: List of Participating Schools for Current Selected Edition -->
        <div x-show="openSchools" x-collapse class="p-5 bg-white">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-bold uppercase text-slate-400 tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-red-500"></i> Asal Sekolah Terdaftar di Edisi: <span class="text-slate-800 font-black">{{ $event?->title ?? 'Semua Edisi' }}</span>
                </div>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto border border-slate-100 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="p-3">No</th>
                            <th class="p-3">Nama Sekolah</th>
                            <th class="p-3">Tingkat</th>
                            <th class="p-3">Regu</th>
                            <th class="p-3">Pembina</th>
                            <th class="p-3">Alamat Email</th>
                            <th class="p-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($schoolsSummary as $idx => $sch)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                <td class="p-3 font-extrabold text-slate-900 uppercase">{{ $sch->school_name }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $sch->level == 'Mula' ? 'bg-blue-50 text-blue-700 border border-blue-200' : ($sch->level == 'Madya' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                        {{ $sch->level }}
                                    </span>
                                </td>
                                <td class="p-3 font-bold text-slate-800">{{ $sch->teams_count }} Regu</td>
                                <td class="p-3 text-slate-700">{{ $sch->advisor_name }} ({{ $sch->advisor_phone }})</td>
                                <td class="p-3 font-medium text-slate-600">{{ $sch->advisor_email ?: '-' }}</td>
                                <td class="p-3 text-center">
                                    @if($sch->status == 'verified')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Lunas</span>
                                    @elseif($sch->status == 'pending')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800">Pending</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-800">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-slate-400">Belum ada sekolah yang terdaftar pada edisi ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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
                @if(request('event_id')) <input type="hidden" name="event_id" value="{{ request('event_id') }}"> @endif
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
                                    {{ $reg->teams->count() }} Regu Terdaftar
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-xs">{{ $reg->advisor_name }}</div>
                                <div class="text-xs text-slate-500"><i class="fa-brands fa-whatsapp text-emerald-500"></i> {{ $reg->advisor_phone }}</div>
                                @if($reg->advisor_email)
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1 font-normal">
                                        <i class="fa-regular fa-envelope text-slate-400 text-[10px]"></i> 
                                        <span class="truncate max-w-[170px]" title="{{ $reg->advisor_email }}">{{ $reg->advisor_email }}</span>
                                    </div>
                                @endif
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
