@extends('layouts.admin')

@section('title', 'Setup Jadwal & Biaya Pendaftaran Lomba')
@section('page_title', 'Setup Jadwal & Biaya Pendaftaran Lomba (Gelombang 1 & 2)')

@section('top_actions')
    <a href="{{ route('lomba.register') }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Cek Form Pendaftaran
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Event Selector & History Bar -->
    @include('admin.competition.partials.event-selector')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    @if($event)
        @php
            $activeWave = $event->getActiveWave();
            $activeLabel = $event->getActiveWaveLabel();
            $targetDate = $event->getCountdownTarget();
        @endphp

        <!-- Live Status Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-6 rounded-2xl shadow-sm border border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $activeWave === 1 ? 'bg-emerald-500 text-slate-950' : ($activeWave === 2 ? 'bg-amber-400 text-slate-950' : ($activeWave === 'upcoming' ? 'bg-blue-400 text-slate-950' : 'bg-rose-500 text-white')) }}">
                        <i class="fa-solid {{ $activeWave === 1 || $activeWave === 2 ? 'fa-clock' : 'fa-circle-info' }} mr-1"></i>
                        Status: {{ $activeLabel }}
                    </span>
                    @if($targetDate)
                        <span class="text-xs text-slate-300 font-mono bg-white/10 px-2.5 py-1 rounded-lg border border-white/10">
                            Target: {{ $targetDate->translatedFormat('d F Y H:i') }} WIB
                        </span>
                    @endif
                </div>
                <h2 class="text-lg font-black text-white">Setup Jadwal & Biaya Pendaftaran Lomba 2 Gelombang</h2>
                <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                    Atur rentang tanggal pendaftaran untuk <strong>Gelombang 1</strong> dan <strong>Gelombang 2</strong> beserta biaya masing-masing gelombang. Sistem otomatis mengalihkan harga pendaftaran dan menjalankan waktu hitung mundur (countdown) secara *real-time*.
                </p>
            </div>

            <!-- Quick Bulk Applier (Gelombang 1 & Gelombang 2) -->
            <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 w-full md:w-auto shrink-0 space-y-3">
                <div class="text-[11px] font-black text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-400"></i>
                    Terapkan Cepat ke Semua Cabang
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] text-emerald-300 font-bold mb-1">Gelombang 1 (Rp)</label>
                        <input type="number" id="bulk-fee-g1" placeholder="130000" value="130000" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs font-bold text-white focus:outline-none focus:border-emerald-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] text-amber-300 font-bold mb-1">Gelombang 2 (Rp)</label>
                        <input type="number" id="bulk-fee-g2" placeholder="150000" value="150000" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs font-bold text-white focus:outline-none focus:border-amber-500 font-mono">
                    </div>
                </div>
                <button type="button" onclick="applyBulkFees()" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs py-2 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-check-double"></i> Terapkan ke Seluruh Input
                </button>
            </div>
        </div>

        <!-- Main Form for Schedule & Fees -->
        <form action="{{ route('admin.competition-fees.update') }}" method="POST" id="fee-setup-form" class="space-y-6">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <!-- Card 1: Pengaturan Jadwal 2 Gelombang -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-red-100 text-pmr-primary flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-calendar-days"></i>
                        </span>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">Jadwal & Periode 2 Gelombang Pendaftaran</h3>
                            <p class="text-xs text-slate-500">Tentukan tanggal dan jam buka/tutup Gelombang 1 dan Gelombang 2</p>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer select-none bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs hover:bg-slate-50 transition">
                        <input type="checkbox" name="is_waves_enabled" value="1" {{ ($event && $event->is_waves_enabled) ? 'checked' : '' }} class="w-4 h-4 text-pmr-primary rounded border-slate-300 focus:ring-pmr-primary">
                        <span class="text-xs font-bold text-slate-700">Aktifkan 2 Gelombang</span>
                    </label>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Gelombang 1 -->
                    <div class="bg-emerald-50/40 border border-emerald-200/80 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-black uppercase tracking-wider text-emerald-800">Gelombang 1 (Early Bird / Pembukaan Awal)</span>
                            </div>
                            @if($activeWave === 1)
                                <span class="text-[10px] font-black bg-emerald-600 text-white px-2 py-0.5 rounded-full uppercase">Sedang Berjalan</span>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama / Label Gelombang 1</label>
                            <input type="text" name="wave1_name" value="{{ old('wave1_name', $event->wave1_name ?? 'Gelombang 1') }}" class="w-full text-xs font-bold bg-white border border-slate-200 px-3.5 py-2.5 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Contoh: Gelombang 1 (Early Bird)">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Mulai Buka Pendaftaran</label>
                                <input type="datetime-local" name="wave1_start_date" value="{{ old('wave1_start_date', $event && $event->wave1_start_date ? $event->wave1_start_date->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs font-semibold bg-white border border-slate-200 px-3 py-2 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Batas Akhir Gelombang 1</label>
                                <input type="datetime-local" name="wave1_end_date" value="{{ old('wave1_end_date', $event && $event->wave1_end_date ? $event->wave1_end_date->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs font-semibold bg-white border border-slate-200 px-3 py-2 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Gelombang 2 -->
                    <div class="bg-amber-50/40 border border-amber-200/80 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse"></span>
                                <span class="text-xs font-black uppercase tracking-wider text-amber-800">Gelombang 2 (Reguler / Lanjutan)</span>
                            </div>
                            @if($activeWave === 2)
                                <span class="text-[10px] font-black bg-amber-600 text-white px-2 py-0.5 rounded-full uppercase">Sedang Berjalan</span>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama / Label Gelombang 2</label>
                            <input type="text" name="wave2_name" value="{{ old('wave2_name', $event->wave2_name ?? 'Gelombang 2') }}" class="w-full text-xs font-bold bg-white border border-slate-200 px-3.5 py-2.5 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Contoh: Gelombang 2 (Reguler)">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Mulai Buka Gelombang 2</label>
                                <input type="datetime-local" name="wave2_start_date" value="{{ old('wave2_start_date', $event && $event->wave2_start_date ? $event->wave2_start_date->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs font-semibold bg-white border border-slate-200 px-3 py-2 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Batas Akhir Gelombang 2 (Tutup)</label>
                                <input type="datetime-local" name="wave2_end_date" value="{{ old('wave2_end_date', $event && $event->wave2_end_date ? $event->wave2_end_date->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs font-semibold bg-white border border-slate-200 px-3 py-2 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Level Filter Tabs -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.competition-fees.index', request()->except('level')) }}" class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition {{ !$level ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    Semua Tingkatan
                </a>
                <a href="{{ route('admin.competition-fees.index', array_merge(request()->query(), ['level' => 'Mula'])) }}" class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition {{ $level == 'Mula' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    <i class="fa-solid fa-child-reaching mr-1"></i> PMR MULA (SD)
                </a>
                <a href="{{ route('admin.competition-fees.index', array_merge(request()->query(), ['level' => 'Madya'])) }}" class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition {{ $level == 'Madya' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    <i class="fa-solid fa-user-group mr-1"></i> PMR MADYA (SMP)
                </a>
                <a href="{{ route('admin.competition-fees.index', array_merge(request()->query(), ['level' => 'Wira'])) }}" class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition {{ $level == 'Wira' ? 'bg-amber-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    <i class="fa-solid fa-graduation-cap mr-1"></i> PMR WIRA (SMA)
                </a>
            </div>

            <!-- Tabel Tarif Per Cabang Lomba (Single Unified Table - NO DOUBLE INPUT) -->
            @php
                $displayLevels = $level ? [$level] : ['Mula', 'Madya', 'Wira'];
            @endphp

            <div class="space-y-8">
                @foreach($displayLevels as $lvl)
                    @php
                        $lvlCats = $categoriesByLevel->get($lvl, collect());
                    @endphp

                    @if($lvlCats->isNotEmpty())
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <!-- Level Header -->
                            <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ $lvl == 'Mula' ? 'bg-emerald-50/50' : ($lvl == 'Madya' ? 'bg-blue-50/50' : 'bg-amber-50/50') }}">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-white text-sm {{ $lvl == 'Mula' ? 'bg-emerald-600' : ($lvl == 'Madya' ? 'bg-blue-600' : 'bg-amber-600') }}">
                                        @if($lvl == 'Mula') <i class="fa-solid fa-child-reaching"></i>
                                        @elseif($lvl == 'Madya') <i class="fa-solid fa-user-group"></i>
                                        @else <i class="fa-solid fa-graduation-cap"></i> @endif
                                    </span>
                                    <div>
                                        <h3 class="font-black text-slate-900 text-base">Tingkat PMR {{ $lvl }} ({{ $lvl == 'Mula' ? 'SD/MI' : ($lvl == 'Madya' ? 'SMP/MTs' : 'SMA/SMK/MA') }})</h3>
                                        <div class="text-xs text-slate-500">{{ $lvlCats->count() }} Cabang Mata Lomba</div>
                                    </div>
                                </div>

                                <button type="button" onclick="setGroupFees('{{ $lvl }}')" class="text-xs font-bold text-slate-700 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-xl shadow-2xs hover:bg-slate-50 transition self-start sm:self-auto">
                                    <i class="fa-solid fa-clone mr-1 text-slate-400"></i> Samakan Tarif Jenjang Ini
                                </button>
                            </div>

                            <!-- Categories List Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs text-slate-700">
                                    <thead class="bg-slate-50 text-slate-700 uppercase font-black border-b border-slate-200">
                                        <tr>
                                            <th class="px-5 py-3.5 w-14 text-center">No</th>
                                            <th class="px-5 py-3.5 min-w-[200px]">Kode & Nama Cabang Lomba</th>
                                            <th class="px-5 py-3.5 w-36">Kategori Gender</th>
                                            <th class="px-5 py-3.5 w-44">Model Penilaian</th>
                                            <th class="px-5 py-3.5 w-48 text-right bg-emerald-50/30 border-l border-emerald-100">
                                                <span class="text-emerald-800 font-black">Biaya Gelombang 1</span>
                                                <div class="text-[9px] text-emerald-600 font-semibold normal-case">(Tarif Awal)</div>
                                            </th>
                                            <th class="px-5 py-3.5 w-48 text-right bg-amber-50/30 border-l border-amber-100">
                                                <span class="text-amber-800 font-black">Biaya Gelombang 2</span>
                                                <div class="text-[9px] text-amber-600 font-semibold normal-case">(Tarif Reguler)</div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($lvlCats as $index => $cat)
                                            @php
                                                $isPa = $cat->gender_category === 'Putra';
                                                $isPi = $cat->gender_category === 'Putri';
                                                $feeG1 = intval($cat->registration_fee_wave1 !== null ? $cat->registration_fee_wave1 : ($cat->registration_fee ?: 130000));
                                                $feeG2 = intval($cat->registration_fee_wave2 !== null ? $cat->registration_fee_wave2 : ($cat->registration_fee ?: 150000));
                                            @endphp
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <!-- Number -->
                                                <td class="px-5 py-4 text-center font-bold text-slate-400">
                                                    {{ $index + 1 }}
                                                </td>

                                                <!-- Code & Name -->
                                                <td class="px-5 py-4">
                                                    <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                        {{ $cat->code }}
                                                    </span>
                                                    <div class="font-black text-slate-900 text-sm mt-1">
                                                        {{ $cat->name }}
                                                    </div>
                                                </td>

                                                <!-- Gender Category Badge -->
                                                <td class="px-5 py-4">
                                                    @if($isPa)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200">
                                                            <i class="fa-solid fa-mars text-blue-600"></i> Putra
                                                        </span>
                                                    @elseif($isPi)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-200">
                                                            <i class="fa-solid fa-venus text-rose-600"></i> Putri
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                                            <i class="fa-solid fa-users text-slate-500"></i> Umum / Campuran
                                                        </span>
                                                    @endif
                                                </td>

                                                <!-- Scoring Type -->
                                                <td class="px-5 py-4 text-slate-500 font-medium">
                                                    {{ ucwords(str_replace('_', ' ', $cat->scoring_type)) }}
                                                </td>

                                                <!-- Biaya Gelombang 1 Input -->
                                                <td class="px-5 py-4 text-right bg-emerald-50/20 border-l border-emerald-100">
                                                    <div class="inline-flex items-center relative">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                        <input type="number" step="1000" min="0" name="fees_wave1[{{ $cat->id }}]" value="{{ $feeG1 }}" data-level="{{ $lvl }}" class="fee-input-g1 w-36 pl-9 pr-3 py-2 bg-white border border-emerald-300 rounded-xl text-xs font-black text-emerald-950 font-mono text-right focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-2xs">
                                                    </div>
                                                </td>

                                                <!-- Biaya Gelombang 2 Input -->
                                                <td class="px-5 py-4 text-right bg-amber-50/20 border-l border-amber-100">
                                                    <div class="inline-flex items-center relative">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                        <input type="number" step="1000" min="0" name="fees_wave2[{{ $cat->id }}]" value="{{ $feeG2 }}" data-level="{{ $lvl }}" class="fee-input-g2 w-36 pl-9 pr-3 py-2 bg-white border border-amber-300 rounded-xl text-xs font-black text-amber-950 font-mono text-right focus:ring-2 focus:ring-amber-500 focus:outline-none shadow-2xs">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Sticky Submit Footer -->
            <div class="mt-8 sticky bottom-4 z-20 bg-white/95 backdrop-blur-md p-5 rounded-2xl shadow-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500">
                    <i class="fa-solid fa-circle-info text-pmr-primary mr-1"></i>
                    Jadwal dan tarif gelombang yang disimpan akan otomatis aktif sesuai hitungan waktu real-time.
                </div>
                <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-extrabold text-xs shadow-lg shadow-red-900/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal & Biaya 2 Gelombang
                </button>
            </div>
        </form>
    @else
        <!-- Empty State when no upcoming/active event is available -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm text-center max-w-xl mx-auto my-8 space-y-5">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto text-2xl shadow-xs border border-amber-100">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <div class="space-y-2">
                <h3 class="text-lg sm:text-xl font-black text-slate-900">Belum Ada Edisi Lomba Aktif atau Mendatang</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-md mx-auto">
                    Dropdown di menu ini hanya menampilkan edisi yang <strong>aktif</strong> atau yang jadwalnya <strong>belum selesai / akan datang</strong>. Edisi lama yang tanggalnya sudah lewat otomatis diarsipkan dan tidak ditampilkan di sini.
                </p>
            </div>
            <div class="pt-2 flex flex-col sm:flex-row justify-center gap-3">
                <a href="{{ route('admin.competition-event.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs px-6 py-3 rounded-xl shadow-lg shadow-red-900/10 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Buat Edisi Lomba Baru
                </a>
                <a href="{{ route('admin.competition-event.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs px-6 py-3 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left"></i> Kelola Riwayat Edisi
                </a>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function applyBulkFees() {
        const val1 = document.getElementById('bulk-fee-g1').value;
        const val2 = document.getElementById('bulk-fee-g2').value;

        if (!val1 || val1 < 0 || !val2 || val2 < 0) {
            alert('Silakan masukkan nominal tarif yang valid untuk Gelombang 1 dan Gelombang 2.');
            return;
        }

        const msg = `Terapkan ke seluruh cabang lomba:\n• Gelombang 1: Rp ${parseInt(val1).toLocaleString('id-ID')}\n• Gelombang 2: Rp ${parseInt(val2).toLocaleString('id-ID')}\nLanjutkan?`;
        if (confirm(msg)) {
            document.querySelectorAll('.fee-input-g1').forEach(input => {
                input.value = val1;
            });
            document.querySelectorAll('.fee-input-g2').forEach(input => {
                input.value = val2;
            });
        }
    }

    function setGroupFees(level) {
        const nominal1 = prompt(`Masukkan tarif Gelombang 1 (Rp) untuk semua cabang PMR ${level}:`, "130000");
        if (nominal1 === null || nominal1 === '') return;
        const val1 = parseInt(nominal1);
        if (isNaN(val1) || val1 < 0) {
            alert('Nominal Gelombang 1 tidak valid.');
            return;
        }

        const nominal2 = prompt(`Masukkan tarif Gelombang 2 (Rp) untuk semua cabang PMR ${level}:`, "150000");
        if (nominal2 === null || nominal2 === '') return;
        const val2 = parseInt(nominal2);
        if (isNaN(val2) || val2 < 0) {
            alert('Nominal Gelombang 2 tidak valid.');
            return;
        }

        document.querySelectorAll(`.fee-input-g1[data-level="${level}"]`).forEach(input => {
            input.value = val1;
        });
        document.querySelectorAll(`.fee-input-g2[data-level="${level}"]`).forEach(input => {
            input.value = val2;
        });
    }
</script>
@endpush
