@extends('layouts.admin')

@section('title', 'Pengelolaan Kantong Parkir & Tiket Kendaraan')
@section('page_title', 'Pengelolaan Kantong Parkir & Tiket')

@section('top_actions')
    <a href="{{ route('admin.parking.quotas') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fa-solid fa-sliders"></i> Pengaturan Kuota Sekolah
    </a>
    <button type="button" onclick="openModal('modalFareSettings')" class="bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 text-white px-4 py-2.5 rounded-xl font-extrabold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fa-solid fa-coins"></i> Setup Tarif Parkir Umum
    </button>
    <button type="button" onclick="openModal('modalCreateOfficer')" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fa-solid fa-user-plus text-rose-400"></i> + Petugas Parkir
    </button>
    <a href="{{ route('parking.index') }}" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fa-solid fa-camera-viewfinder text-rose-400"></i> Buka Web Petugas Parkir (/parkir)
    </a>
    <a href="{{ route('admin.parking.export-excel') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fa-solid fa-file-excel"></i> Ekspor Laporan (.CSV)
    </a>
    <button type="button" onclick="openModal('modalCreateTicket')" class="bg-pmr-primary hover:bg-rose-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2">
        <i class="fa-solid fa-ticket"></i> Terbitkan Tiket Parkir
    </button>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Terjadi Kesalahan Input:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 1. TOP METRICS SUMMARY -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Kantong Parkir -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-square-parking"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Kantong Parkir</span>
                <div class="text-xl font-black text-slate-800 mt-0.5">
                    {{ $locations->count() }} <span class="text-xs font-normal text-slate-500">Titik Lokasi</span>
                </div>
            </div>
        </div>

        <!-- Metric 2: Roda 4 -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-van-shuttle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Keterisian Roda 4</span>
                <div class="text-xl font-black text-slate-800 mt-0.5">
                    {{ $totalBookedR4 }} <span class="text-xs font-normal text-slate-500">/ {{ $totalCapacityR4 }} Slot</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                    @php $p4 = $totalCapacityR4 > 0 ? min(100, round(($totalBookedR4 / $totalCapacityR4) * 100)) : 0; @endphp
                    <div class="bg-amber-500 h-full rounded-full transition-all duration-300" style="width: {{ $p4 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Metric 3: Roda 2 -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Keterisian Roda 2</span>
                <div class="text-xl font-black text-slate-800 mt-0.5">
                    {{ $totalBookedR2 }} <span class="text-xs font-normal text-slate-500">/ {{ $totalCapacityR2 }} Slot</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                    @php $p2 = $totalCapacityR2 > 0 ? min(100, round(($totalBookedR2 / $totalCapacityR2) * 100)) : 0; @endphp
                    <div class="bg-purple-500 h-full rounded-full transition-all duration-300" style="width: {{ $p2 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Metric 4: Check-in Terparkir -->
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kendaraan Terparkir (Check-In)</span>
                <div class="text-xl font-black text-emerald-600 mt-0.5">
                    {{ $totalParked }} <span class="text-xs font-normal text-slate-500">/ {{ $totalTickets }} Tiket</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 1b. SECTION: SETUP & RINGKASAN TARIF PARKIR UMUM (NON-KONTINGEN) -->
    <div class="bg-gradient-to-r from-amber-500/10 via-yellow-50/50 to-white rounded-3xl p-6 border-2 border-amber-300 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 mb-4 border-b border-amber-200/60">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-900 bg-amber-100 border border-amber-300 px-2.5 py-0.5 rounded-full">
                        <i class="fa-solid fa-coins text-amber-600"></i> TARIF PARKIR UMUM
                    </span>
                    @if($fareSetting->is_public_parking_active)
                        <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Layanan Terbuka untuk Umum
                        </span>
                    @else
                        <span class="text-[10px] font-bold text-slate-600 bg-slate-200 px-2 py-0.5 rounded-full border border-slate-300">
                            Tutup (Khusus Kontingen)
                        </span>
                    @endif
                </div>
                <h3 class="font-black text-lg text-slate-900 mt-1 flex items-center gap-2">
                    Pengaturan Harga Tiket & Tarif Parkir Umum
                </h3>
                <p class="text-xs text-slate-600 mt-0.5">
                    Tarif resmi untuk kendaraan penonton, orang tua siswa, dan pengunjung umum di hari-H lomba.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openModal('modalFareSettings')" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-yellow-300 font-extrabold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-sliders text-rose-400"></i> Ubah Pengaturan Tarif
                </button>
            </div>
        </div>

        <!-- 4 Category Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Roda 2 -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs space-y-2">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-base">
                        <i class="fa-solid fa-motorcycle"></i>
                    </div>
                    <span class="text-[10px] font-black text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                        RODA 2
                    </span>
                </div>
                <div>
                    <h4 class="font-black text-xs text-slate-900">Sepeda Motor</h4>
                    <span class="text-[10px] text-slate-400 block">Matic, Bebek, Sport</span>
                </div>
                <div class="pt-1 border-t border-slate-100 flex items-baseline justify-between">
                    <span class="text-[10px] text-slate-400 font-medium">Tarif Resmi:</span>
                    <span class="text-base font-black text-slate-900 font-mono">Rp {{ number_format($fareSetting->fare_r2_motor, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Roda 4 -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs space-y-2">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-base">
                        <i class="fa-solid fa-car"></i>
                    </div>
                    <span class="text-[10px] font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                        RODA 4
                    </span>
                </div>
                <div>
                    <h4 class="font-black text-xs text-slate-900">Mobil Pribadi / MPV / SUV</h4>
                    <span class="text-[10px] text-slate-400 block">Avanza, Innova, Brio, dll</span>
                </div>
                <div class="pt-1 border-t border-slate-100 flex items-baseline justify-between">
                    <span class="text-[10px] text-slate-400 font-medium">Tarif Resmi:</span>
                    <span class="text-base font-black text-slate-900 font-mono">Rp {{ number_format($fareSetting->fare_r4_car, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Roda 4+ Minibus -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs space-y-2">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-base">
                        <i class="fa-solid fa-van-shuttle"></i>
                    </div>
                    <span class="text-[10px] font-black text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                        RODA 4+ MINIBUS
                    </span>
                </div>
                <div>
                    <h4 class="font-black text-xs text-slate-900">Minibus (Elf / HiAce)</h4>
                    <span class="text-[10px] text-slate-400 block">Elf Short/Long, Toyota HiAce</span>
                </div>
                <div class="pt-1 border-t border-slate-100 flex items-baseline justify-between">
                    <span class="text-[10px] text-slate-400 font-medium">Tarif Resmi:</span>
                    <span class="text-base font-black text-slate-900 font-mono">Rp {{ number_format($fareSetting->fare_r4_minibus, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Roda 6+ Bus Besar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs space-y-2">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
                        <i class="fa-solid fa-bus"></i>
                    </div>
                    <span class="text-[10px] font-black text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                        RODA 6+ BUS
                    </span>
                </div>
                <div>
                    <h4 class="font-black text-xs text-slate-900">Bus Besar Pariwisata</h4>
                    <span class="text-[10px] text-slate-400 block">Bus Besar 40-60 Kursi</span>
                </div>
                <div class="pt-1 border-t border-slate-100 flex items-baseline justify-between">
                    <span class="text-[10px] text-slate-400 font-medium">Tarif Resmi:</span>
                    <span class="text-base font-black text-slate-900 font-mono">Rp {{ number_format($fareSetting->fare_r6_bus, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Revenue Summary Strip -->
        <div class="mt-4 pt-3 border-t border-amber-200/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-slate-600 font-semibold">
                    <i class="fa-solid fa-cash-register text-amber-600"></i> Rekap Kas Parkir Umum:
                </span>
                <span class="font-mono font-black text-emerald-700 text-sm bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-200">
                    Total: Rp {{ number_format($publicRevenueTotal, 0, ',', '.') }}
                </span>
                <span class="text-slate-500 text-[11px]">
                    (Tunai: <strong class="text-slate-800">Rp {{ number_format($publicCashTotal, 0, ',', '.') }}</strong> &bull; QRIS: <strong class="text-slate-800">Rp {{ number_format($publicQrisTotal, 0, ',', '.') }}</strong> &bull; <strong class="text-slate-800">{{ $publicTicketsCount }}</strong> Karcis)
                </span>
            </div>
            <span class="text-[11px] text-slate-500 font-medium">
                Metode Diterima:
                @if($fareSetting->allow_cash) <span class="font-bold text-slate-700">Tunai</span> @endif
                @if($fareSetting->allow_cash && $fareSetting->allow_qris) & @endif
                @if($fareSetting->allow_qris) <span class="font-bold text-slate-700">QRIS</span> @endif
                &bull; Denda Karcis Hilang: <strong>Rp {{ number_format($fareSetting->lost_ticket_penalty, 0, ',', '.') }}</strong>
            </span>
        </div>
    </div>

    <!-- 2. SECTION: DAFTAR KANTONG PARKIR -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="font-black text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-rose-600"></i> Daftar Kantong Parkir & Penugasan Petugas
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Atur lokasi kantong parkir, kuota estimasi kendaraan roda 4 & 2, serta petugas penanggung jawab lapangan.</p>
            </div>
            <button type="button" onclick="openModal('modalCreateLocation')" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center gap-2 shadow-xs shrink-0">
                <i class="fa-solid fa-plus text-rose-400"></i> Tambah Kantong Parkir
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($locations as $loc)
                <div class="border border-slate-200 hover:border-slate-300 rounded-2xl p-5 bg-gradient-to-b from-white to-slate-50/50 flex flex-col justify-between transition group shadow-2xs">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="min-w-0">
                                <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">
                                    {{ $loc->code ?: 'K-PARKIR' }}
                                </span>
                                <h4 class="font-black text-base text-slate-900 mt-1 truncate">{{ $loc->name }}</h4>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $loc->status_badge_class }}">
                                {{ $loc->status_label }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 mb-4 line-clamp-2">
                            <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> {{ $loc->address ?: 'Lokasi kampus / sekitar SMAN 1 Ciawi' }}
                        </p>

                        <!-- Meter Keterisian R4 & R2 -->
                        <div class="space-y-3 bg-white p-3 rounded-xl border border-slate-100 mb-4">
                            <!-- Roda 4 -->
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-car-side text-blue-500"></i> Roda 4 (Mobil/Bus/Elf)
                                    </span>
                                    <span class="font-mono font-bold text-slate-800 text-[11px]">
                                        {{ $loc->booked_r4_count }} / {{ $loc->capacity_r4 }} <span class="text-slate-400 font-normal">({{ $loc->remaining_r4 }} sisa)</span>
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300 {{ $loc->percent_r4 >= 90 ? 'bg-rose-500' : ($loc->percent_r4 >= 75 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ $loc->percent_r4 }}%"></div>
                                </div>
                            </div>

                            <!-- Roda 2 -->
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-motorcycle text-purple-500"></i> Roda 2 (Motor)
                                    </span>
                                    <span class="font-mono font-bold text-slate-800 text-[11px]">
                                        {{ $loc->booked_r2_count }} / {{ $loc->capacity_r2 }} <span class="text-slate-400 font-normal">({{ $loc->remaining_r2 }} sisa)</span>
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300 {{ $loc->percent_r2 >= 90 ? 'bg-rose-500' : ($loc->percent_r2 >= 75 ? 'bg-amber-500' : 'bg-purple-500') }}" style="width: {{ $loc->percent_r2 }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Petugas Jaga -->
                        <div class="text-xs text-slate-600 bg-slate-100/60 p-2.5 rounded-xl border border-slate-200/60 flex items-center justify-between">
                            <div class="truncate mr-2">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Petugas Jaga:</span>
                                <span class="font-bold text-slate-800">{{ $loc->officer_name ?: 'Belum ditentukan' }}</span>
                            </div>
                            @if($loc->officer_phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $loc->officer_phone) }}" target="_blank" class="w-7 h-7 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs" title="Hubungi Petugas via WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 pt-3 border-t border-slate-200/80 flex items-center justify-between gap-2">
                        @if($loc->gmaps_url)
                            <a href="{{ $loc->gmaps_url }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Rute Maps
                            </a>
                        @else
                            <span class="text-[11px] text-slate-400 italic">Maps belum diisi</span>
                        @endif

                        <div class="flex items-center gap-1">
                            <button type="button" onclick="editLocation({{ json_encode($loc) }})" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition" title="Edit Kantong Parkir">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('admin.parking.locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kantong parkir ini? Tiket yang sudah dialokasikan akan di-unplot.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition" title="Hapus Kantong Parkir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400">
                    <i class="fa-solid fa-square-parking text-4xl mb-3 text-slate-300"></i>
                    <p class="font-bold text-slate-600">Belum Ada Kantong Parkir</p>
                    <p class="text-xs text-slate-400 mt-1">Silakan klik tombol <strong>"Tambah Kantong Parkir"</strong> di atas untuk menambahkan lokasi parkir peserta.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2b. SECTION: TIM PETUGAS PARKIR LAPANGAN & KREDENSIAL POS -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-5">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full">
                        <i class="fa-solid fa-id-badge text-rose-500"></i> MANAJEMEN PETUGAS
                    </span>
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                        {{ $activeOfficersCount }} Aktif / {{ $totalOfficersCount }} Terdaftar
                    </span>
                </div>
                <h3 class="font-black text-lg text-slate-900 flex items-center gap-2 mt-1">
                    <i class="fa-solid fa-users-gear text-rose-600"></i> Tim Petugas Parkir Lapangan & Penugasan Pos
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kelola akun login petugas lapangan (username & password) serta tentukan kantong parkir mana yang menjadi wilayah wewenangnya.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('parking.index') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 border border-slate-200">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px] text-slate-500"></i> Buka Portal Lapangan (/parkir)
                </a>
                <button type="button" onclick="openModal('modalCreateOfficer')" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs transition flex items-center gap-2 shadow-xs cursor-pointer">
                    <i class="fa-solid fa-user-plus"></i> Daftarkan Petugas Parkir
                </button>
            </div>
        </div>

        <!-- Tabel Petugas Parkir -->
        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Petugas Lapangan</th>
                        <th class="py-3 px-4">Kredensial Login</th>
                        <th class="py-3 px-4">Wilayah Pos Bertugas</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Login Terakhir</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($officers as $officer)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Petugas Lapangan -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 text-white flex items-center justify-center font-black text-xs shadow-xs shrink-0">
                                        {{ strtoupper(substr($officer->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 text-xs">{{ $officer->name }}</div>
                                        @if($officer->phone)
                                            <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                                <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $officer->phone) }}" target="_blank" class="hover:underline font-mono text-slate-600">
                                                    {{ $officer->phone }}
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">No. HP belum diisi</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Kredensial Login -->
                            <td class="py-3 px-4">
                                <div class="space-y-0.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-slate-100 border border-slate-200 font-mono font-bold text-slate-800 text-[11px]">
                                        <i class="fa-solid fa-user-lock text-slate-400 text-[10px]"></i> {{ $officer->username }}
                                    </span>
                                    <div class="text-[10px] text-slate-400">Password terenkripsi aman</div>
                                </div>
                            </td>

                            <!-- Wilayah Pos Bertugas -->
                            <td class="py-3 px-4">
                                @if($officer->locations->count() > 0)
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        @foreach($officer->locations as $loc)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold">
                                                <i class="fa-solid fa-location-dot text-rose-500 text-[9px]"></i>
                                                {{ $loc->code ?: $loc->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">
                                        <i class="fa-solid fa-globe text-emerald-500 text-[9px]"></i> Semua Pos (Bebas / Koordinator)
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4 text-center">
                                @if($officer->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Login Terakhir -->
                            <td class="py-3 px-4 text-center text-[11px] text-slate-500">
                                @if($officer->last_login_at)
                                    <span class="font-medium text-slate-700 block">{{ $officer->last_login_at->format('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $officer->last_login_at->format('H:i') }} WIB</span>
                                @else
                                    <span class="text-slate-400 italic">Belum pernah</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('admin.parking.officers.toggle-status', $officer->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg border {{ $officer->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }} text-xs transition cursor-pointer" title="{{ $officer->is_active ? 'Nonaktifkan Petugas' : 'Aktifkan Petugas' }}">
                                            <i class="fa-solid {{ $officer->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Edit Officer Button -->
                                    <button type="button" onclick="editOfficer({{ json_encode($officer->load('locations')) }})" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-xs transition cursor-pointer" title="Edit Data & Penugasan Pos">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Officer Button -->
                                    <form action="{{ route('admin.parking.officers.destroy', $officer->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus petugas parkir ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs transition cursor-pointer" title="Hapus Petugas">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-user-xmark text-3xl text-slate-300 mb-2"></i>
                                <p class="font-bold text-slate-600">Belum Ada Petugas Parkir Terdaftar</p>
                                <p class="text-xs text-slate-400 mt-0.5">Daftarkan petugas parkir agar mereka dapat login ke <code>/parkir</code> untuk scan karcis dan validasi lokasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. SECTION: TABEL DATA TIKET KENDARAAN & PLOTTING -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="font-black text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-ticket text-rose-600"></i> Data Tiket Kendaraan & Plotting Kontingen
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh kendaraan terdaftar (R4 & R2), status alokasi kantong parkir, serta histori check-in.</p>
            </div>

            <!-- Filter & Bulk Actions -->
            <form action="{{ route('admin.parking.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <!-- Search -->
                <div class="relative flex-1 sm:w-56">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Plat / Sekolah / Sopir..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <i class="fa-solid fa-search text-slate-400 absolute left-3 top-2.5 text-xs"></i>
                </div>

                <!-- Filter Kantong -->
                <select name="location_id" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none">
                    <option value="">Semua Kantong Parkir</option>
                    @foreach($locations as $l)
                        <option value="{{ $l->id }}" {{ request('location_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>

                <!-- Filter Jenis -->
                <select name="vehicle_type" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none">
                    <option value="">Semua Jenis</option>
                    <option value="r4" {{ request('vehicle_type') == 'r4' ? 'selected' : '' }}>Roda 4 (Semua)</option>
                    <option value="r2" {{ request('vehicle_type') == 'r2' ? 'selected' : '' }}>Roda 2 (Motor)</option>
                    <option value="r4_car" {{ request('vehicle_type') == 'r4_car' ? 'selected' : '' }}>Mobil Pribadi</option>
                    <option value="r4_elf" {{ request('vehicle_type') == 'r4_elf' ? 'selected' : '' }}>Minibus Elf / HiAce</option>
                    <option value="r4_bus" {{ request('vehicle_type') == 'r4_bus' ? 'selected' : '' }}>Bus Pariwisata</option>
                </select>

                <!-- Filter Status -->
                <select name="status" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="parked" {{ request('status') == 'parked' ? 'selected' : '' }}>Terparkir (Check-In)</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Selesai (Keluar)</option>
                </select>

                @if(request()->hasAny(['search', 'location_id', 'vehicle_type', 'status']))
                    <a href="{{ route('admin.parking.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Form Bulk Plotting Action -->
        <form id="formBulkAssign" action="{{ route('admin.parking.tickets.assign-location') }}" method="POST">
            @csrf
            <div class="mb-3 flex items-center justify-between gap-3 bg-slate-50 p-3 rounded-2xl border border-slate-200 text-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-700"><i class="fa-solid fa-list-check text-rose-600"></i> Aksi Plotting Masal:</span>
                    <span id="selectedCountBadge" class="bg-rose-100 text-rose-800 px-2 py-0.5 rounded-md font-bold">0 dipilih</span>
                </div>
                <div class="flex items-center gap-2">
                    <select name="parking_location_id" id="bulkLocationSelect" required class="text-xs bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-slate-800 font-bold focus:outline-none">
                        <option value="">-- Pilih Kantong Parkir Tujuan --</option>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}">{{ $l->name }} (Sisa R4: {{ $l->remaining_r4 }} | R2: {{ $l->remaining_r2 }})</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="submitBulkAssign()" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <i class="fa-solid fa-arrows-split-up-and-left"></i> Terapkan Plotting
                    </button>
                    <button type="button" onclick="submitBulkDelete()" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer" title="Hapus Tiket yang Dicentang">
                        <i class="fa-solid fa-trash-can"></i> Hapus Terpilih
                    </button>
                </div>
            </div>

            <!-- Tabel Tiket -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-black uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="p-3.5 text-center w-10">
                                <input type="checkbox" id="checkAll" onclick="toggleCheckAll(this)" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            </th>
                            <th class="p-3.5">No. Tiket</th>
                            <th class="p-3.5">Asal Sekolah / Kontingen</th>
                            <th class="p-3.5">No. Polisi (Plat)</th>
                            <th class="p-3.5">Jenis & Merek</th>
                            <th class="p-3.5">Kantong Parkir</th>
                            <th class="p-3.5">Sopir / Kontak</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($tickets as $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3.5 text-center">
                                    <input type="checkbox" name="ticket_ids[]" value="{{ $t->id }}" class="ticket-checkbox rounded border-slate-300 text-rose-600 focus:ring-rose-500" onchange="updateSelectedCount()">
                                </td>
                                <td class="p-3.5">
                                    <span class="font-mono font-black text-slate-900 block">{{ $t->ticket_code }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ substr($t->qr_code_token, 0, 8) }}...</span>
                                </td>
                                <td class="p-3.5">
                                    <span class="font-bold text-slate-900 block truncate max-w-[180px]">{{ $t->school_name }}</span>
                                    @if($t->registration)
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $t->registration->registration_code }}</span>
                                    @else
                                        <span class="text-[10px] text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200 font-bold inline-flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-coins text-amber-600"></i> Umum • {{ $t->formatted_fee }} ({{ $t->payment_method_label }})
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-mono font-black text-xs tracking-wider inline-block">
                                        {{ $t->license_plate }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <div class="flex items-center gap-1.5 font-bold text-slate-800">
                                        @if(str_starts_with($t->vehicle_type, 'r2'))
                                            <i class="fa-solid fa-motorcycle text-purple-600"></i>
                                        @elseif($t->vehicle_type === 'r4_bus')
                                            <i class="fa-solid fa-bus text-rose-600"></i>
                                        @elseif($t->vehicle_type === 'r4_elf')
                                            <i class="fa-solid fa-van-shuttle text-amber-600"></i>
                                        @else
                                            <i class="fa-solid fa-car-side text-blue-600"></i>
                                        @endif
                                        <span>{{ $t->vehicle_type_label }}</span>
                                    </div>
                                    <span class="text-[11px] text-slate-500 block mt-0.5 truncate max-w-[170px]">
                                        {{ $t->vehicle_brand ?: '-' }} ({{ $t->vehicle_color ?: '-' }})
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    @if($t->location)
                                        <span class="font-bold text-slate-900 block">{{ $t->location->name }}</span>
                                        <span class="text-[10px] text-slate-500 block truncate max-w-[160px]">{{ $t->location->officer_name ?: 'Petugas Parkir' }}</span>
                                    @else
                                        <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 inline-block">
                                            Belum Di-plot
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <span class="font-bold text-slate-800 block">{{ $t->driver_name ?: '-' }}</span>
                                    @if($t->driver_phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $t->driver_phone) }}?text=Halo%20{{ urlencode($t->driver_name) }},%20berikut%20tiket%20parkir%20resmi%20lomba%20PMR%20SMANIC%20anda:%20{{ urlencode(route('parking.public-ticket', $t->qr_code_token)) }}" target="_blank" class="text-[11px] text-emerald-600 hover:text-emerald-800 font-bold flex items-center gap-1 mt-0.5">
                                            <i class="fa-brands fa-whatsapp text-emerald-500"></i> {{ $t->driver_phone }}
                                        </a>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">No WA tidak ada</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $t->status_badge_class }} block mb-1">
                                        {{ $t->status_label }}
                                    </span>
                                    @if($t->check_in_at)
                                        <span class="text-[9px] text-slate-400 font-mono block">In: {{ $t->check_in_at->format('H:i') }}</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('parking.public-ticket', $t->qr_code_token) }}" target="_blank" class="p-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 text-xs transition" title="Lihat E-Tiket Digital">
                                            <i class="fa-solid fa-qrcode"></i>
                                        </a>
                                        <a href="{{ route('admin.parking.tickets.print', $t->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition" title="Cetak Tiket PDF/Kertas">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                        <button type="button" onclick="editTicket({{ json_encode($t) }})" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs transition" title="Edit Data Tiket">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button type="button" onclick="changeTicketStatus({{ $t->id }}, '{{ $t->status }}')" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs transition" title="Ubah Status Masuk/Keluar">
                                            <i class="fa-solid fa-arrows-rotate"></i>
                                        </button>
                                        <form action="{{ route('admin.parking.tickets.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket parkir {{ $t->ticket_code }} ({{ $t->license_plate }})? Data tiket dan riwayat kendaraan ini akan dihapus permanen.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition cursor-pointer" title="Hapus Tiket Kendaraan">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-slate-400">
                                    <i class="fa-solid fa-car-tunnel text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-bold text-slate-600">Tidak ada data tiket kendaraan ditemukan</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Silakan klik tombol <strong>"Terbitkan Tiket Parkir"</strong> untuk mendaftarkan kendaraan baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>

</div>

<!-- ================= MODALS ================= -->

<!-- 1. MODAL TAMBAH KANTONG PARKIR -->
<div id="modalCreateLocation" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-square-parking text-rose-600"></i> Tambah Kantong Parkir Baru
            </h4>
            <button type="button" onclick="closeModal('modalCreateLocation')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center">✕</button>
        </div>

        <form action="{{ route('admin.parking.locations.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Kantong Parkir <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Kantong Parkir A (Lapangan Depan)" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kode Singkat</label>
                    <input type="text" name="code" placeholder="KPA" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kapasitas Roda 4 (Mobil/Bus/Elf) <span class="text-rose-500">*</span></label>
                    <input type="number" name="capacity_r4" required min="0" value="30" class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kapasitas Roda 2 (Motor) <span class="text-rose-500">*</span></label>
                    <input type="number" name="capacity_r2" required min="0" value="100" class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Petugas Jaga Lapangan</label>
                    <input type="text" name="officer_name" placeholder="Kang Farhan & Teh Dinda" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. WhatsApp Petugas</label>
                    <input type="text" name="officer_phone" placeholder="08123456789" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Alamat / Patokan Lokasi</label>
                <textarea name="address" rows="2" placeholder="Jl. Veteran No. 1, Ciawi - Lapangan depan gerbang SMAN 1 Ciawi" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Link Google Maps (Navigasi GPS)</label>
                <input type="url" name="gmaps_url" placeholder="https://maps.app.goo.gl/..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <input type="hidden" name="status" value="open">
                <button type="button" onclick="closeModal('modalCreateLocation')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md">Simpan Kantong Parkir</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL EDIT KANTONG PARKIR -->
<div id="modalEditLocation" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-rose-600"></i> Edit Kantong Parkir
            </h4>
            <button type="button" onclick="closeModal('modalEditLocation')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center">✕</button>
        </div>

        <form id="formEditLocation" action="" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Kantong Parkir <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit_loc_name" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kode</label>
                    <input type="text" name="code" id="edit_loc_code" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kapasitas R4 <span class="text-rose-500">*</span></label>
                    <input type="number" name="capacity_r4" id="edit_loc_r4" required min="0" class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kapasitas R2 <span class="text-rose-500">*</span></label>
                    <input type="number" name="capacity_r2" id="edit_loc_r2" required min="0" class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Petugas Jaga</label>
                    <input type="text" name="officer_name" id="edit_loc_officer_name" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. WhatsApp</label>
                    <input type="text" name="officer_phone" id="edit_loc_officer_phone" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Status Kantong</label>
                    <select name="status" id="edit_loc_status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="open">Tersedia (Open)</option>
                        <option value="full">Penuh (Full)</option>
                        <option value="closed">Ditutup (Closed)</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Link Google Maps</label>
                    <input type="url" name="gmaps_url" id="edit_loc_gmaps" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Alamat / Patokan Lokasi</label>
                <textarea name="address" id="edit_loc_address" rows="2" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditLocation')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL TERBITKAN TIKET PARKIR -->
<div id="modalCreateTicket" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-ticket text-rose-600"></i> Terbitkan Tiket Parkir Kendaraan
            </h4>
            <button type="button" onclick="closeModal('modalCreateTicket')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center">✕</button>
        </div>

        <form action="{{ route('admin.parking.tickets.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Asal Sekolah / Kontingen</label>
                <select name="competition_registration_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                    <option value="">-- Umum / Bukan Kontingen Sekolah --</option>
                    @foreach($schools as $sch)
                        <option value="{{ $sch->id }}">
                            {{ $sch->school_name }} ({{ $sch->level }}) • Sisa R4: {{ $sch->remaining_quota_r4 }} | R2: {{ $sch->remaining_quota_r2 }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. Polisi (Plat Nomor) <span class="text-rose-500">*</span></label>
                    <input type="text" name="license_plate" required placeholder="F 1845 EA" class="w-full text-xs font-mono font-black uppercase bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Jenis Kendaraan <span class="text-rose-500">*</span></label>
                    <select name="vehicle_type" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="r2_motor">Roda 2 Sepeda Motor</option>
                        <option value="r4_car">Roda 4 Mobil Pribadi / MPV / SUV</option>
                        <option value="r4_elf">Roda 4+ Minibus (Elf / HiAce)</option>
                        <option value="r4_bus">Roda 6+ Bus Besar Pariwisata</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Merek & Tipe</label>
                    <input type="text" name="vehicle_brand" placeholder="Toyota HiAce Commuter" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Warna Kendaraan</label>
                    <input type="text" name="vehicle_color" placeholder="Putih Metalik" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Sopir / PJ</label>
                    <input type="text" name="driver_name" placeholder="Pak Hendra" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. WhatsApp Sopir</label>
                    <input type="text" name="driver_phone" placeholder="081388991234" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Alokasi Kantong Parkir Langsung</label>
                <select name="parking_location_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                    <option value="">-- Belum Dialokasikan (Bisa Di-plot Nanti) --</option>
                    @foreach($locations as $l)
                        <option value="{{ $l->id }}">{{ $l->name }} (Sisa R4: {{ $l->remaining_r4 }} | R2: {{ $l->remaining_r2 }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Khusus Parkir Umum: Metode Pembayaran & Catatan Tarif -->
            <div class="grid grid-cols-2 gap-3 p-3.5 bg-amber-50/70 rounded-2xl border border-amber-200">
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Metode Bayar (Bila Tiket Umum)</label>
                    <select name="payment_method" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none">
                        <option value="cash">Tunai (Cash Petugas)</option>
                        <option value="qris">QRIS (Digital Panitia)</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Status Pembayaran</label>
                    <select name="payment_status" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-800 font-bold focus:outline-none">
                        <option value="paid">Lunas (Karcis Terbit)</option>
                        <option value="unpaid">Belum Lunas (Bayar di Pintu)</option>
                    </select>
                </div>
                <div class="col-span-2 text-[10px] text-amber-900 leading-tight">
                    <i class="fa-solid fa-circle-info text-amber-600"></i> Jika asal sekolah dipilih (kontingen), tarif otomatis <strong>Gratis (Rp 0)</strong>. Jika untuk umum, sistem otomatis menghitung tarif resmi (Motor Rp {{ number_format($fareSetting->fare_r2_motor, 0, ',', '.') }}, Mobil Rp {{ number_format($fareSetting->fare_r4_car, 0, ',', '.') }}, Minibus Rp {{ number_format($fareSetting->fare_r4_minibus, 0, ',', '.') }}, Bus Rp {{ number_format($fareSetting->fare_r6_bus, 0, ',', '.') }}).
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalCreateTicket')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-pmr-primary hover:bg-rose-700 text-white font-bold text-xs shadow-md">Terbitkan Tiket</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. MODAL EDIT TIKET PARKIR -->
<div id="modalEditTicket" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-rose-600"></i> Edit Data Tiket Kendaraan
            </h4>
            <button type="button" onclick="closeModal('modalEditTicket')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center">✕</button>
        </div>

        <form id="formEditTicket" action="" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. Polisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="license_plate" id="edit_ticket_plate" required class="w-full text-xs font-mono font-black uppercase bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Jenis Kendaraan <span class="text-rose-500">*</span></label>
                    <select name="vehicle_type" id="edit_ticket_vtype" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="r2_motor">Roda 2 Sepeda Motor</option>
                        <option value="r4_car">Roda 4 Mobil Pribadi / MPV / SUV</option>
                        <option value="r4_elf">Roda 4+ Minibus (Elf / HiAce)</option>
                        <option value="r4_bus">Roda 6+ Bus Besar Pariwisata</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Merek & Tipe</label>
                    <input type="text" name="vehicle_brand" id="edit_ticket_brand" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Warna</label>
                    <input type="text" name="vehicle_color" id="edit_ticket_color" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Sopir</label>
                    <input type="text" name="driver_name" id="edit_ticket_driver_name" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">No. WhatsApp</label>
                    <input type="text" name="driver_phone" id="edit_ticket_driver_phone" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kantong Parkir</label>
                    <select name="parking_location_id" id="edit_ticket_location_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="">-- Belum Di-plot --</option>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Status Tiket</label>
                    <select name="status" id="edit_ticket_status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none">
                        <option value="booked">Terjadwal</option>
                        <option value="parked">Terparkir (Check-In)</option>
                        <option value="checked_out">Selesai (Keluar)</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="competition_registration_id" id="edit_ticket_registration_id">

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditTicket')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- 5. MODAL QUICK STATUS UPDATE -->
<div id="modalStatusUpdate" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center">
        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-3">
            <i class="fa-solid fa-arrows-rotate"></i>
        </div>
        <h4 class="font-black text-slate-900 text-base">Ubah Status Kendaraan</h4>
        <p class="text-xs text-slate-500 mt-1 mb-4">Pilih status operasional terkini untuk kendaraan ini:</p>

        <form id="formStatusUpdate" action="" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 gap-2 text-left">
                <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="status" value="parked" class="text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="font-bold text-xs text-slate-900 block">Terparkir (Check-In Masuk)</span>
                        <span class="text-[10px] text-slate-500">Kendaraan sudah tiba di kantong parkir</span>
                    </div>
                </label>
                <label class="p-3 rounded-xl border border-slate-200 hover:border-blue-500 flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="status" value="checked_out" class="text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="font-bold text-xs text-slate-900 block">Selesai (Check-Out Keluar)</span>
                        <span class="text-[10px] text-slate-500">Kendaraan sudah keluar dan membebaskan slot</span>
                    </div>
                </label>
                <label class="p-3 rounded-xl border border-slate-200 hover:border-amber-500 flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="status" value="booked" class="text-amber-600 focus:ring-amber-500">
                    <div>
                        <span class="font-bold text-xs text-slate-900 block">Terjadwal (Reset / Belum Tiba)</span>
                        <span class="text-[10px] text-slate-500">Kembalikan ke status belum check-in</span>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-between pt-3">
                <button type="button" onclick="closeModal('modalStatusUpdate')" class="px-4 py-2 rounded-xl text-slate-600 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs">Perbarui Status</button>
            </div>
        </form>
    </div>
</div>

<!-- 6. MODAL SETUP TARIF PARKIR UMUM -->
<div id="modalFareSettings" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-coins text-amber-500"></i> Setup Tarif & Harga Tiket Parkir Umum
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Atur skema biaya flat per hari untuk kendaraan pengunjung & non-kontingen.</p>
            </div>
            <button type="button" onclick="closeModal('modalFareSettings')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center">✕</button>
        </div>

        <form action="{{ route('admin.parking.fares.update') }}" method="POST" class="mt-4 space-y-4">
            @csrf

            <!-- Status Layanan Parkir Umum -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-4">
                <div>
                    <span class="font-bold text-xs text-slate-900 block">Status Layanan Parkir Umum</span>
                    <span class="text-[11px] text-slate-500 block">Buka akses penerbitan karcis dan pencatatan kas parkir umum.</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_public_parking_active" value="1" {{ $fareSetting->is_public_parking_active ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
            </div>

            <!-- 4 Kolom Tarif Kendaraan -->
            <div class="space-y-3">
                <span class="text-xs font-black text-slate-900 uppercase tracking-wider block">
                    <i class="fa-solid fa-tag text-rose-500"></i> Biaya Flat Parkir Per Hari (Rp)
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Roda 2 -->
                    <div class="p-3 bg-purple-50/60 rounded-2xl border border-purple-200 space-y-1.5">
                        <label class="text-xs font-bold text-purple-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-motorcycle text-purple-600"></i> Roda 2 (Sepeda Motor)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-xs text-slate-400">Rp</span>
                            <input type="number" min="0" step="1000" name="fare_r2_motor" value="{{ $fareSetting->fare_r2_motor }}" required class="w-full text-xs font-mono font-bold bg-white border border-purple-200 rounded-xl pl-9 pr-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <span class="text-[10px] text-purple-700 block">Semua tipe motor matic/bebek/sport</span>
                    </div>

                    <!-- Roda 4 Mobil Pribadi -->
                    <div class="p-3 bg-blue-50/60 rounded-2xl border border-blue-200 space-y-1.5">
                        <label class="text-xs font-bold text-blue-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-car text-blue-600"></i> Roda 4 (Mobil Pribadi / MPV / SUV)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-xs text-slate-400">Rp</span>
                            <input type="number" min="0" step="1000" name="fare_r4_car" value="{{ $fareSetting->fare_r4_car }}" required class="w-full text-xs font-mono font-bold bg-white border border-blue-200 rounded-xl pl-9 pr-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <span class="text-[10px] text-blue-700 block">Sedan, City Car, MPV, SUV, Minivan</span>
                    </div>

                    <!-- Roda 4+ Minibus -->
                    <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-200 space-y-1.5">
                        <label class="text-xs font-bold text-amber-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-van-shuttle text-amber-600"></i> Roda 4+ Minibus (Elf / HiAce)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-xs text-slate-400">Rp</span>
                            <input type="number" min="0" step="1000" name="fare_r4_minibus" value="{{ $fareSetting->fare_r4_minibus }}" required class="w-full text-xs font-mono font-bold bg-white border border-amber-200 rounded-xl pl-9 pr-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <span class="text-[10px] text-amber-700 block">Elf Short/Long, Toyota HiAce, Medium Bus</span>
                    </div>

                    <!-- Roda 6+ Bus Besar -->
                    <div class="p-3 bg-rose-50/60 rounded-2xl border border-rose-200 space-y-1.5">
                        <label class="text-xs font-bold text-rose-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-bus text-rose-600"></i> Roda 6+ Bus Besar Pariwisata
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-xs text-slate-400">Rp</span>
                            <input type="number" min="0" step="1000" name="fare_r6_bus" value="{{ $fareSetting->fare_r6_bus }}" required class="w-full text-xs font-mono font-bold bg-white border border-rose-200 rounded-xl pl-9 pr-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                        </div>
                        <span class="text-[10px] text-rose-700 block">Big Bus Pariwisata 40-60 seat</span>
                    </div>
                </div>
            </div>

            <!-- Metode Pembayaran Diterima & Denda Tiket Hilang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <label class="text-xs font-bold text-slate-900 block">Metode Pembayaran Kasir:</label>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                            <input type="checkbox" name="allow_cash" value="1" {{ $fareSetting->allow_cash ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold">Uang Tunai (Cash)</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                            <input type="checkbox" name="allow_qris" value="1" {{ $fareSetting->allow_qris ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold">QRIS Digital PMR SMANIC</span>
                        </label>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5">
                    <label class="text-xs font-bold text-slate-900 block">Denda Karcis Hilang (Rp):</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-xs text-slate-400">Rp</span>
                        <input type="number" min="0" step="5000" name="lost_ticket_penalty" value="{{ $fareSetting->lost_ticket_penalty }}" required class="w-full text-xs font-mono font-bold bg-white border border-slate-300 rounded-xl pl-9 pr-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                    <span class="text-[10px] text-slate-500 block">Wajib tunjukkan STNK asli</span>
                </div>
            </div>

            <!-- Catatan Karcis / Struk -->
            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Catatan Struk / Karcis Parkir</label>
                <textarea name="ticket_receipt_note" rows="2" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Simpan struk ini untuk ditunjukkan saat check-out kendaraan...">{{ $fareSetting->ticket_receipt_note }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalFareSettings')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Tarif
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 6. MODAL TAMBAH PETUGAS PARKIR -->
<div id="modalCreateOfficer" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-rose-600"></i> Daftarkan Petugas Parkir Baru
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Buat akun login & tentukan pos kantong parkir bertugas.</p>
            </div>
            <button type="button" onclick="closeModal('modalCreateOfficer')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.parking.officers.store') }}" method="POST" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Lengkap Petugas <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Rian Anggara" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" placeholder="Contoh: 081234567890" class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Username Login <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" required placeholder="Contoh: petugas_parkir1" class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Digunakan saat login di /parkir</span>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Password rahasia petugas</span>
                </div>
            </div>

            <!-- Penugasan Pos Kantong Parkir -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-map-pin text-rose-500"></i> Penugasan Pos Kantong Parkir:
                    </label>
                    <span class="text-[10px] text-slate-400">Pilih satu atau lebih</span>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Petugas hanya dapat memvalidasi karcis di kantong parkir yang dicentang. Jika tidak ada yang dicentang, petugas bertugas di semua pos (bebas).
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 max-h-40 overflow-y-auto">
                    @foreach($locations as $loc)
                        <label class="flex items-start gap-2 p-2 rounded-xl bg-white border border-slate-200 hover:border-rose-300 hover:bg-rose-50/30 cursor-pointer transition">
                            <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}" class="mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <div class="min-w-0 text-xs">
                                <span class="font-bold text-slate-800 block truncate">{{ $loc->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $loc->code ?: 'Pos Parkir' }} &bull; R4: {{ $loc->capacity_r4 }} | R2: {{ $loc->capacity_r2 }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Status Aktif -->
            <div class="pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Aktifkan akun petugas ini segera</span>
                </label>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalCreateOfficer')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Petugas Parkir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 7. MODAL EDIT PETUGAS PARKIR -->
<div id="modalEditOfficer" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div>
                <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-rose-600"></i> Edit Data Petugas Parkir
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui profil, username, password, atau penugasan pos.</p>
            </div>
            <button type="button" onclick="closeModal('modalEditOfficer')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form id="formEditOfficer" action="" method="POST" class="mt-4 space-y-4 overflow-y-auto pr-1">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Lengkap Petugas <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit_officer_name" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" id="edit_officer_phone" class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Username Login <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" id="edit_officer_username" required class="w-full text-xs font-mono font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Ganti Password (Opsional)</label>
                    <input type="password" name="password" id="edit_officer_password" minlength="6" placeholder="Kosongkan jika tak diubah" class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <!-- Penugasan Pos Kantong Parkir -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-map-pin text-rose-500"></i> Penugasan Pos Kantong Parkir:
                    </label>
                    <span class="text-[10px] text-slate-400">Pilih satu atau lebih</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 max-h-40 overflow-y-auto">
                    @foreach($locations as $loc)
                        <label class="flex items-start gap-2 p-2 rounded-xl bg-white border border-slate-200 hover:border-rose-300 hover:bg-rose-50/30 cursor-pointer transition">
                            <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}" id="edit_officer_loc_{{ $loc->id }}" class="edit-officer-location-cb mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <div class="min-w-0 text-xs">
                                <span class="font-bold text-slate-800 block truncate">{{ $loc->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $loc->code ?: 'Pos Parkir' }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Status Aktif -->
            <div class="pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                    <input type="checkbox" name="is_active" id="edit_officer_is_active" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Akun petugas aktif</span>
                </label>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('modalEditOfficer')" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Perbarui Data Petugas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function editLocation(loc) {
        document.getElementById('edit_loc_name').value = loc.name || '';
        document.getElementById('edit_loc_code').value = loc.code || '';
        document.getElementById('edit_loc_r4').value = loc.capacity_r4 || 0;
        document.getElementById('edit_loc_r2').value = loc.capacity_r2 || 0;
        document.getElementById('edit_loc_officer_name').value = loc.officer_name || '';
        document.getElementById('edit_loc_officer_phone').value = loc.officer_phone || '';
        document.getElementById('edit_loc_status').value = loc.status || 'open';
        document.getElementById('edit_loc_gmaps').value = loc.gmaps_url || '';
        document.getElementById('edit_loc_address').value = loc.address || '';
        document.getElementById('formEditLocation').action = `/admin/parking/locations/${loc.id}`;
        openModal('modalEditLocation');
    }

    function editOfficer(officer) {
        document.getElementById('edit_officer_name').value = officer.name || '';
        document.getElementById('edit_officer_username').value = officer.username || '';
        document.getElementById('edit_officer_phone').value = officer.phone || '';
        document.getElementById('edit_officer_password').value = '';
        document.getElementById('edit_officer_is_active').checked = Boolean(officer.is_active);

        // Reset all location checkboxes
        const checkboxes = document.querySelectorAll('.edit-officer-location-cb');
        checkboxes.forEach(cb => cb.checked = false);

        // Check assigned locations
        if (officer.locations && Array.isArray(officer.locations)) {
            officer.locations.forEach(loc => {
                const cb = document.getElementById('edit_officer_loc_' + loc.id);
                if (cb) cb.checked = true;
            });
        }

        document.getElementById('formEditOfficer').action = `/admin/parking/officers/${officer.id}`;
        openModal('modalEditOfficer');
    }

    function editTicket(ticket) {
        document.getElementById('edit_ticket_plate').value = ticket.license_plate || '';
        document.getElementById('edit_ticket_vtype').value = ticket.vehicle_type || 'r4_car';
        document.getElementById('edit_ticket_brand').value = ticket.vehicle_brand || '';
        document.getElementById('edit_ticket_color').value = ticket.vehicle_color || '';
        document.getElementById('edit_ticket_driver_name').value = ticket.driver_name || '';
        document.getElementById('edit_ticket_driver_phone').value = ticket.driver_phone || '';
        document.getElementById('edit_ticket_location_id').value = ticket.parking_location_id || '';
        document.getElementById('edit_ticket_status').value = ticket.status || 'booked';
        document.getElementById('edit_ticket_registration_id').value = ticket.competition_registration_id || '';
        document.getElementById('formEditTicket').action = `/admin/parking/tickets/${ticket.id}`;
        openModal('modalEditTicket');
    }

    function changeTicketStatus(ticketId, currentStatus) {
        document.getElementById('formStatusUpdate').action = `/admin/parking/tickets/${ticketId}/status`;
        const radios = document.getElementsByName('status');
        for (let r of radios) {
            r.checked = (r.value === currentStatus);
        }
        openModal('modalStatusUpdate');
    }

    function toggleCheckAll(source) {
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.ticket-checkbox:checked').length;
        document.getElementById('selectedCountBadge').innerText = `${count} dipilih`;
    }

    function submitBulkAssign() {
        const count = document.querySelectorAll('.ticket-checkbox:checked').length;
        if (count === 0) {
            alert('Silakan pilih minimal 1 tiket kendaraan untuk di-plot.');
            return;
        }
        const locId = document.getElementById('bulkLocationSelect').value;
        if (!locId) {
            alert('Silakan pilih kantong parkir tujuan.');
            return;
        }
        document.getElementById('formBulkAssign').submit();
    }

    function submitBulkDelete() {
        const count = document.querySelectorAll('.ticket-checkbox:checked').length;
        if (count === 0) {
            alert('Silakan centang minimal 1 tiket kendaraan untuk dihapus.');
            return;
        }
        if (!confirm(`Apakah Anda yakin ingin menghapus ${count} tiket parkir kendaraan yang dipilih secara permanen? Data yang dihapus tidak dapat dikembalikan.`)) {
            return;
        }
        const form = document.getElementById('formBulkAssign');
        form.action = "{{ route('admin.parking.tickets.destroy-multiple') }}";
        
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'DELETE';
        form.submit();
    }
</script>
@endsection
