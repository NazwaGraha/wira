@extends('layouts.admin')

@section('title', 'Daftar Ulang Peserta Lomba')
@section('page_title', 'Daftar Ulang / Presensi Peserta Lomba (Hari-H)')

@section('top_actions')
    <a href="{{ route('admin.competition-participants.index') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2">
        <i class="fa-solid fa-users-viewfinder"></i> Lihat Peserta Terverifikasi
    </a>
@endsection

@section('content')
<div class="space-y-6" x-data="checkinApp()">

    <!-- Event Selector -->
    @include('admin.competition.partials.event-selector')

    <!-- Statistik Presensi Kehadiran Hari-H -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Terverifikasi</div>
                <div class="text-3xl font-black text-slate-900 mt-1">{{ $totalVerified }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Kontingen Lunas</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-school"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Sudah Hadir (Daftar Ulang)</div>
                <div class="text-3xl font-black text-emerald-700 mt-1">{{ $totalCheckedIn }}</div>
                <div class="text-[11px] text-emerald-600 mt-0.5 font-medium">{{ $attendanceRate }}% dari total kontingen</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-amber-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Belum Daftar Ulang</div>
                <div class="text-3xl font-black text-amber-700 mt-1">{{ $totalPending }}</div>
                <div class="text-[11px] text-amber-600 mt-0.5 font-medium">Menunggu di lokasi</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white shadow-md flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Kehadiran Hari-H</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-black bg-emerald-500 text-slate-900">{{ $attendanceRate }}%</span>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-700 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-3 rounded-full transition-all duration-500" style="width: {{ $attendanceRate }}%"></div>
                </div>
                <div class="flex justify-between items-center text-[11px] text-slate-400 mt-1.5">
                    <span>{{ $totalCheckedIn }} Hadir</span>
                    <span>{{ $totalVerified }} Total</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Input Cepat & Scanner QR Code -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 sm:p-6 bg-gradient-to-r from-red-600 to-rose-700 text-white">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black tracking-tight">Meja Registrasi & Daftar Ulang Kontingen</h2>
                        <p class="text-xs text-red-100 mt-0.5">Scan QR Code dari Kwitansi Resmi pendaftar atau masukkan Nomor Registrasi (Contoh: SBB-W54342H)</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="toggleScanner()" 
                            class="px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-sm"
                            :class="scannerActive ? 'bg-amber-400 text-slate-900 hover:bg-amber-300' : 'bg-white text-red-700 hover:bg-red-50'">
                        <i class="fa-solid" :class="scannerActive ? 'fa-video-slash' : 'fa-camera'"></i>
                        <span x-text="scannerActive ? 'Tutup Kamera Scanner' : 'Buka Kamera Scanner QR'"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="p-5 sm:p-6 bg-slate-50 border-b border-slate-200">
            <!-- Box Scanner Kamera Live -->
            <div x-show="scannerActive" x-transition class="mb-6 bg-white p-4 rounded-2xl border-2 border-dashed border-red-300 max-w-md mx-auto">
                <div class="text-center mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 animate-pulse">
                        <i class="fa-solid fa-circle text-[8px] text-red-600"></i> Kamera Aktif - Arahkan ke QR Code Kwitansi
                    </span>
                </div>
                <div id="qr-reader" class="rounded-xl overflow-hidden shadow-inner bg-black w-full min-h-[260px]"></div>
                <p class="text-[11px] text-slate-400 text-center mt-2.5">
                    Pastikan QR Code di Kwitansi atau Kartu Peserta berada di dalam kotak pemindai.
                </p>
            </div>

            <!-- Form Input Manual Kode Registrasi -->
            <form @submit.prevent="lookupCode(inputCode)" class="flex flex-col sm:flex-row gap-3 max-w-2xl mx-auto">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-barcode text-lg"></i>
                    </div>
                    <input type="text" 
                           x-model="inputCode" 
                           placeholder="Ketik Nomor Registrasi (Contoh: SBB-W54342H atau scan barcode)..." 
                           class="w-full pl-12 pr-4 py-3.5 bg-white border-2 border-slate-300 focus:border-red-600 focus:ring-4 focus:ring-red-100 rounded-xl font-mono font-bold text-slate-800 placeholder-slate-400 text-sm tracking-wider uppercase transition shadow-sm"
                           autocomplete="off"
                           autofocus>
                </div>
                <button type="submit" 
                        :disabled="isLoading || !inputCode"
                        class="px-6 py-3.5 bg-slate-900 hover:bg-slate-800 disabled:opacity-50 text-white rounded-xl font-bold text-sm transition flex items-center justify-center gap-2 shadow-sm shrink-0">
                    <span x-show="!isLoading"><i class="fa-solid fa-magnifying-glass"></i> Cek Kontingen</span>
                    <span x-show="isLoading" class="flex items-center gap-2"><i class="fa-solid fa-circle-notch fa-spin"></i> Memeriksa...</span>
                </button>
            </form>
        </div>

        <!-- Alert Error / Notifikasi Pencarian -->
        <div x-show="errorMessage" x-transition class="p-4 bg-rose-50 border-b border-rose-200 text-rose-700 text-sm flex items-start justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg"></i>
                <span class="font-bold" x-text="errorMessage"></span>
            </div>
            <button type="button" @click="errorMessage = ''" class="text-rose-400 hover:text-rose-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Kartu Hasil Lookup Peserta (Siap Check-In) -->
        <div x-show="activeCandidate" x-transition class="p-6 bg-white border-b border-slate-200">
            <template x-if="activeCandidate">
                <div class="rounded-2xl border-2 p-6 transition-all"
                     :class="activeCandidate.is_checked_in ? 'border-emerald-300 bg-emerald-50/40' : 'border-blue-300 bg-blue-50/30'">
                    
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-5 border-b"
                         :class="activeCandidate.is_checked_in ? 'border-emerald-200' : 'border-blue-200'">
                        <div>
                            <div class="flex flex-wrap items-center gap-2.5 mb-2">
                                <span class="px-3 py-1 rounded-lg font-mono font-black text-xs uppercase tracking-wider bg-slate-900 text-white shadow-sm" x-text="activeCandidate.registration_code"></span>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black uppercase"
                                      :class="{
                                          'bg-blue-100 text-blue-800': activeCandidate.level === 'WIRA',
                                          'bg-emerald-100 text-emerald-800': activeCandidate.level === 'MADYA',
                                          'bg-amber-100 text-amber-800': activeCandidate.level === 'MULA'
                                      }"
                                      x-text="'TINGKAT ' + activeCandidate.level"></span>
                                
                                <template x-if="activeCandidate.is_checked_in">
                                    <span class="px-3 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-circle-check"></i> SUDAH DAFTAR ULANG
                                    </span>
                                </template>
                                <template x-if="!activeCandidate.is_checked_in">
                                    <span class="px-3 py-1 rounded-lg text-xs font-black bg-amber-500 text-white flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-clock"></i> BELUM DAFTAR ULANG (SIAP PRESENSI)
                                    </span>
                                </template>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight" x-text="activeCandidate.school_name"></h3>
                            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600 mt-2">
                                <div><i class="fa-solid fa-user-tie text-slate-400 mr-1.5"></i> Pembina: <strong class="text-slate-800" x-text="activeCandidate.advisor_name"></strong></div>
                                <div><i class="fa-solid fa-phone text-slate-400 mr-1.5"></i> <span x-text="activeCandidate.advisor_phone"></span></div>
                                <div><i class="fa-solid fa-money-bill-wave text-slate-400 mr-1.5"></i> Biaya: <strong class="text-emerald-700" x-text="'Rp ' + activeCandidate.total_fee"></strong></div>
                            </div>
                        </div>

                        <!-- Status Badge & Timestamp Kehadiran -->
                        <div class="flex flex-col items-start lg:items-end gap-2 shrink-0">
                            <template x-if="activeCandidate.is_checked_in">
                                <div class="bg-emerald-100/90 border border-emerald-300 rounded-xl p-3 text-left lg:text-right">
                                    <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wide">Waktu Kehadiran:</div>
                                    <div class="text-sm font-black text-emerald-950 mt-0.5" x-text="activeCandidate.checked_in_at"></div>
                                    <div class="text-xs text-emerald-800 mt-0.5">Petugas: <strong x-text="activeCandidate.checked_in_by"></strong></div>
                                    <template x-if="activeCandidate.checkin_notes">
                                        <div class="text-[11px] text-emerald-700 italic mt-1 bg-white/70 px-2 py-0.5 rounded" x-text="'Catatan: ' + activeCandidate.checkin_notes"></div>
                                    </template>
                                </div>
                            </template>

                            <div class="flex items-center gap-2">
                                <a :href="'/lomba/kwitansi/' + activeCandidate.registration_code" target="_blank" class="px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-receipt text-red-600"></i> Buka Kwitansi
                                </a>
                                <a :href="'/lomba/kartu-peserta/' + activeCandidate.registration_code" target="_blank" class="px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-id-card text-blue-600"></i> Kartu Peserta
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Regu / Tim Mata Lomba yang Didaftarkan -->
                    <div class="mt-4">
                        <div class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-list-check text-slate-400"></i>
                            <span>Daftar Regu & Mata Lomba Terdaftar (<span x-text="activeCandidate.teams_count"></span> Regu)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                            <template x-for="team in activeCandidate.teams" :key="team.id">
                                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                                    <div>
                                        <div class="text-xs font-black text-slate-800" x-text="team.name"></div>
                                        <div class="text-[11px] text-slate-500 font-medium" x-text="team.category"></div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700" x-text="'No. Urut: ' + team.order_number"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tombol Aksi Check-In / Batal Check-In -->
                    <div class="mt-6 pt-5 border-t flex flex-col sm:flex-row items-center justify-between gap-4"
                         :class="activeCandidate.is_checked_in ? 'border-emerald-200' : 'border-blue-200'">
                        <div class="w-full sm:w-auto">
                            <template x-if="!activeCandidate.is_checked_in">
                                <div class="w-full sm:w-80">
                                    <input type="text" x-model="checkinNotes" placeholder="Catatan opsional (misal: ID Card diserahkan)..." class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500">
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            <template x-if="!activeCandidate.is_checked_in">
                                <button type="button" 
                                        @click="processCheckin(activeCandidate.id)"
                                        :disabled="isProcessing"
                                        class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-sm transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30">
                                    <i class="fa-solid fa-check-double text-base"></i>
                                    <span>KONFIRMASI DAFTAR ULANG (HADIR)</span>
                                </button>
                            </template>

                            <template x-if="activeCandidate.is_checked_in">
                                <button type="button" 
                                        @click="cancelCheckin(activeCandidate.id)"
                                        :disabled="isProcessing"
                                        class="w-full sm:w-auto px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 rounded-xl font-bold text-xs transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    <span>Batalkan Status Kehadiran</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Tabel Kontingen & Filter Tab Presensi -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Tabs Status -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.competition-checkin.index', array_merge(request()->except('tab', 'page'), ['tab' => 'pending'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab == 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-clock"></i>
                    <span>Belum Hadir</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab == 'pending' ? 'bg-amber-700 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $totalPending }}</span>
                </a>

                <a href="{{ route('admin.competition-checkin.index', array_merge(request()->except('tab', 'page'), ['tab' => 'checked_in'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab == 'checked_in' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>Sudah Hadir (Daftar Ulang)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab == 'checked_in' ? 'bg-emerald-800 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $totalCheckedIn }}</span>
                </a>

                <a href="{{ route('admin.competition-checkin.index', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab == 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-list"></i>
                    <span>Semua Kontingen Lunas</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab == 'all' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $totalVerified }}</span>
                </a>
            </div>

            <!-- Filter Tingkat & Search -->
            <form action="{{ route('admin.competition-checkin.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                @if(request('event_id'))
                    <input type="hidden" name="event_id" value="{{ request('event_id') }}">
                @endif

                <select name="level" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700">
                    <option value="">Semua Tingkat</option>
                    <option value="mula" {{ $level == 'mula' ? 'selected' : '' }}>MULA (SD)</option>
                    <option value="madya" {{ $level == 'madya' ? 'selected' : '' }}>MADYA (SMP)</option>
                    <option value="wira" {{ $level == 'wira' ? 'selected' : '' }}>WIRA (SMA/K)</option>
                </select>

                <div class="relative">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari sekolah / kode..." class="pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 w-44 sm:w-56 focus:w-64 transition-all">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>

                @if($search || $level)
                    <a href="{{ route('admin.competition-checkin.index', ['tab' => $tab, 'event_id' => request('event_id')]) }}" class="p-2 text-slate-400 hover:text-rose-600 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-bold">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">No. Registrasi</th>
                        <th class="py-3 px-4">Sekolah / Kontingen</th>
                        <th class="py-3 px-4">Tingkat</th>
                        <th class="py-3 px-4">Pembina / Kontak</th>
                        <th class="py-3 px-4 text-center">Jml Regu</th>
                        <th class="py-3 px-4">Status Kehadiran</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($registrations as $index => $reg)
                        <tr class="hover:bg-slate-50/70 transition {{ $reg->is_checked_in ? 'bg-emerald-50/20' : '' }}">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                {{ $registrations->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <button type="button" 
                                        @click="lookupCode('{{ $reg->registration_code }}')" 
                                        class="font-mono font-black text-slate-900 bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded border border-slate-300 transition text-left inline-flex items-center gap-1.5">
                                    <span>{{ $reg->registration_code }}</span>
                                    <i class="fa-solid fa-arrow-pointer text-[10px] text-slate-400"></i>
                                </button>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $reg->school_name }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Biaya: Rp {{ number_format($reg->total_fee, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase
                                    {{ $reg->level == 'wira' ? 'bg-blue-100 text-blue-800' : ($reg->level == 'madya' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $reg->level }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ $reg->advisor_name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $reg->advisor_phone }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-800 font-bold font-mono">
                                    {{ $reg->teams->count() }} Regu
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($reg->is_checked_in)
                                    <div class="flex items-center gap-1.5 text-emerald-700 font-bold">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>HADIR</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">
                                        {{ $reg->checked_in_at ? $reg->checked_in_at->format('d/m/Y H:i') : '-' }} 
                                        ({{ $reg->checked_in_by ?? 'Panitia' }})
                                    </div>
                                    @if($reg->checkin_notes)
                                        <div class="text-[10px] text-slate-400 italic mt-0.5 truncate max-w-xs" title="{{ $reg->checkin_notes }}">
                                            "{{ $reg->checkin_notes }}"
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fa-regular fa-clock"></i> Belum Hadir
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(!$reg->is_checked_in)
                                        <form action="{{ route('admin.competition-checkin.process') }}" method="POST" onsubmit="return confirm('Konfirmasi daftar ulang kontingen {{ $reg->school_name }}?')">
                                            @csrf
                                            <input type="hidden" name="registration_id" value="{{ $reg->id }}">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center gap-1 shadow-xs">
                                                <i class="fa-solid fa-check"></i>
                                                <span>Check-In</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.competition-checkin.cancel', $reg->id) }}" method="POST" onsubmit="return confirm('Batalkan status kehadiran kontingen {{ $reg->school_name }}?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-rose-100 hover:text-rose-700 text-slate-600 font-semibold text-[11px] transition" title="Batalkan Check-In">
                                                <i class="fa-solid fa-rotate-left"></i> Batal
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button" @click="lookupCode('{{ $reg->registration_code }}')" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition" title="Detail & Scanner">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-clipboard-user text-4xl mb-3 text-slate-300"></i>
                                <p class="font-bold text-sm text-slate-600">Tidak ada data kontingen ditemukan</p>
                                <p class="text-xs text-slate-400 mt-1">Coba ubah filter pencarian atau pastikan sudah ada pendaftar terverifikasi lunas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Script Html5-Qrcode CDN untuk Pemindaian Kamera Langsung -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
function checkinApp() {
    return {
        inputCode: '',
        isLoading: false,
        isProcessing: false,
        errorMessage: '',
        activeCandidate: null,
        checkinNotes: '',
        scannerActive: false,
        html5QrCode: null,

        init() {
            // Auto focus input
        },

        toggleScanner() {
            if (this.scannerActive) {
                this.stopScanner();
            } else {
                this.startScanner();
            }
        },

        startScanner() {
            this.scannerActive = true;
            this.$nextTick(() => {
                if (!this.html5QrCode) {
                    this.html5QrCode = new Html5Qrcode("qr-reader");
                }
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                this.html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText, decodedResult) => {
                        // QR Code Terdeteksi
                        console.log("QR Code Terdeteksi:", decodedText);
                        this.stopScanner();
                        this.inputCode = decodedText;
                        this.lookupCode(decodedText);
                    },
                    (errorMessage) => {
                        // Scan berlangsung / frame belum menemukan QR
                    }
                ).catch((err) => {
                    console.error("Gagal membuka kamera:", err);
                    alert("Tidak dapat mengakses kamera: " + (err.message || err));
                    this.scannerActive = false;
                });
            });
        },

        stopScanner() {
            if (this.html5QrCode && this.html5QrCode.isScanning) {
                this.html5QrCode.stop().then(() => {
                    this.scannerActive = false;
                }).catch(err => {
                    console.error("Gagal stop kamera:", err);
                    this.scannerActive = false;
                });
            } else {
                this.scannerActive = false;
            }
        },

        lookupCode(code) {
            if (!code || !code.trim()) return;

            this.isLoading = true;
            this.errorMessage = '';
            this.activeCandidate = null;
            this.checkinNotes = '';

            fetch(`/admin/competition-checkin/lookup?code=${encodeURIComponent(code.trim())}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                this.isLoading = false;
                if (status === 200 && body.success) {
                    this.activeCandidate = body.registration;
                    // Scroll smooth ke kartu peserta
                    window.scrollTo({ top: 380, behavior: 'smooth' });
                } else {
                    this.errorMessage = body.message || 'Data pendaftaran tidak ditemukan atau belum lunas.';
                }
            })
            .catch(err => {
                this.isLoading = false;
                this.errorMessage = 'Terjadi kesalahan saat memeriksa data. Silakan coba lagi.';
                console.error(err);
            });
        },

        processCheckin(registrationId) {
            if (!confirm('Konfirmasi daftar ulang kontingen ' + this.activeCandidate.school_name + '?')) {
                return;
            }

            this.isProcessing = true;
            fetch("{{ route('admin.competition-checkin.process') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    registration_id: registrationId,
                    notes: this.checkinNotes
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isProcessing = false;
                if (data.success) {
                    alert(data.message);
                    // Reload halaman agar list & statistik presensi terupdate
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal memproses check-in.');
                }
            })
            .catch(err => {
                this.isProcessing = false;
                alert('Terjadi kesalahan jaringan.');
                console.error(err);
            });
        },

        cancelCheckin(registrationId) {
            if (!confirm('Apakah Anda yakin ingin membatalkan status kehadiran kontingen ini?')) {
                return;
            }

            this.isProcessing = true;
            fetch(`/admin/competition-checkin/${registrationId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.isProcessing = false;
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal membatalkan check-in.');
                }
            })
            .catch(err => {
                this.isProcessing = false;
                alert('Terjadi kesalahan jaringan.');
                console.error(err);
            });
        }
    };
}
</script>
@endsection
