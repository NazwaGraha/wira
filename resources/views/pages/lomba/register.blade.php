@extends('layouts.app')

@section('title', 'Form Pendaftaran Lomba PMR - ' . ($event->title ?? ''))

@section('content')
<div class="bg-slate-900 text-white pt-24 sm:pt-32 pb-8 sm:pb-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="bg-red-500/20 text-red-400 border border-red-500/30 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
            Formulir Resmi
        </span>
        <h1 class="text-2xl sm:text-4xl font-extrabold mt-3">Pendaftaran Kontingen Lomba</h1>
        <p class="text-slate-400 text-xs sm:text-sm mt-2 max-w-xl mx-auto">
            Isi data sekolah, pilih cabang lomba yang diikuti, dan lampirkan bukti pembayaran untuk mendapatkan e-Kwitansi & Kartu Peserta.
        </p>
    </div>
</div>

<div class="bg-slate-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-xl"></i>
                <div class="text-sm font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl shadow-sm">
                <div class="font-bold text-sm mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Mohon periksa kembali isian form Anda:
                </div>
                <ul class="list-disc pl-5 text-xs space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $wave = $activeWave ?? ($event ? $event->getActiveWave() : 1);
            $waveLabelText = $waveLabel ?? ($event ? $event->getActiveWaveLabel() : 'Gelombang 1');
            $targetCountdown = $countdownTarget ?? ($event ? $event->getCountdownTarget() : null);
        @endphp

        <!-- Wave Status & Live Countdown Banner -->
        @if($event && ($event->is_waves_enabled || $event->wave1_end_date))
            <div class="mb-6 bg-gradient-to-r {{ $wave == 1 ? 'from-amber-600 via-orange-600 to-red-600' : 'from-emerald-700 via-teal-700 to-cyan-700' }} text-white rounded-2xl p-5 sm:p-6 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid {{ $wave == 1 ? 'fa-fire text-yellow-300 animate-pulse' : 'fa-bolt-lightning text-yellow-200 animate-pulse' }}"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="bg-white/25 text-white text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full border border-white/20">
                                Periode Aktif
                            </span>
                            <span class="text-sm font-black tracking-wide">{{ $waveLabelText }}</span>
                        </div>
                        <div class="text-xs text-white/90 mt-1">
                            @if($wave == 1 && $event->wave1_end_date)
                                Tarif Gelombang 1 berlaku hingga <strong>{{ $event->wave1_end_date->translatedFormat('d F Y, H:i') }} WIB</strong>. Setelah itu tarif otomatis beralih ke {{ $event->wave2_name ?: 'Gelombang 2' }}.
                            @elseif($wave == 2 && $event->wave2_end_date)
                                Pendaftaran Gelombang 2 ditutup pada <strong>{{ $event->wave2_end_date->translatedFormat('d F Y, H:i') }} WIB</strong>.
                            @endif
                        </div>
                    </div>
                </div>

                @if($targetCountdown)
                    <div class="bg-black/25 backdrop-blur px-4 py-3 rounded-xl border border-white/10 flex items-center justify-between sm:justify-start gap-3">
                        <div class="text-[11px] text-white/80 font-bold uppercase tracking-wider">Sisa Waktu:</div>
                        <div id="register-countdown-timer" class="font-mono font-black text-sm sm:text-base text-yellow-300 flex items-center gap-1.5" data-target="{{ $targetCountdown->toIso8601String() }}">
                            <span id="reg-days">00</span><span class="text-[10px] text-white/70">h</span> :
                            <span id="reg-hours">00</span><span class="text-[10px] text-white/70">j</span> :
                            <span id="reg-minutes">00</span><span class="text-[10px] text-white/70">m</span> :
                            <span id="reg-seconds">00</span><span class="text-[10px] text-white/70">d</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <form action="{{ route('lomba.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" id="registration-form">
            @csrf
            
            <div class="p-6 sm:p-8 space-y-8">
                
                <!-- STEP 1: Tingkatan Sekolah -->
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2 mb-3">
                        <span class="w-6 h-6 rounded-full bg-red-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                        Pilih Tingkatan Kontingen
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @php $currentLevel = old('level', request('level', 'Madya')); @endphp
                        
                        <!-- MULA (HIJAU) -->
                        <label id="card-Mula" onclick="switchLevel('Mula')" class="level-card cursor-pointer border-2 rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 relative {{ $currentLevel == 'Mula' ? 'bg-emerald-600 text-white border-emerald-600 shadow-lg shadow-emerald-700/20 scale-[1.02]' : 'bg-emerald-50/60 text-emerald-950 border-emerald-200 hover:border-emerald-400 hover:bg-emerald-50' }}">
                            <input type="radio" name="level" value="Mula" class="sr-only" {{ $currentLevel == 'Mula' ? 'checked' : '' }}>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg {{ $currentLevel == 'Mula' ? 'bg-white/20 text-white' : 'bg-emerald-200/70 text-emerald-900' }}">
                                    Tingkat SD
                                </span>
                                <div class="check-icon w-6 h-6 rounded-full flex items-center justify-center text-sm {{ $currentLevel == 'Mula' ? 'bg-white text-emerald-600' : 'hidden' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </div>
                            <div>
                                <div class="font-black text-lg">PMR Mula</div>
                                <div class="text-xs {{ $currentLevel == 'Mula' ? 'text-emerald-100' : 'text-emerald-700' }} mt-0.5">SD / MI / Sederajat</div>
                            </div>
                        </label>

                        <!-- MADYA (BIRU) -->
                        <label id="card-Madya" onclick="switchLevel('Madya')" class="level-card cursor-pointer border-2 rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 relative {{ $currentLevel == 'Madya' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-700/20 scale-[1.02]' : 'bg-blue-50/60 text-blue-950 border-blue-200 hover:border-blue-400 hover:bg-blue-50' }}">
                            <input type="radio" name="level" value="Madya" class="sr-only" {{ $currentLevel == 'Madya' ? 'checked' : '' }}>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg {{ $currentLevel == 'Madya' ? 'bg-white/20 text-white' : 'bg-blue-200/70 text-blue-900' }}">
                                    Tingkat SMP
                                </span>
                                <div class="check-icon w-6 h-6 rounded-full flex items-center justify-center text-sm {{ $currentLevel == 'Madya' ? 'bg-white text-blue-600' : 'hidden' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </div>
                            <div>
                                <div class="font-black text-lg">PMR Madya</div>
                                <div class="text-xs {{ $currentLevel == 'Madya' ? 'text-blue-100' : 'text-blue-700' }} mt-0.5">SMP / MTs / Sederajat</div>
                            </div>
                        </label>

                        <!-- WIRA (KUNING / AMBER) -->
                        <label id="card-Wira" onclick="switchLevel('Wira')" class="level-card cursor-pointer border-2 rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 relative {{ $currentLevel == 'Wira' ? 'bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-600/20 scale-[1.02]' : 'bg-amber-50/60 text-amber-950 border-amber-200 hover:border-amber-400 hover:bg-amber-50' }}">
                            <input type="radio" name="level" value="Wira" class="sr-only" {{ $currentLevel == 'Wira' ? 'checked' : '' }}>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg {{ $currentLevel == 'Wira' ? 'bg-white/20 text-white' : 'bg-amber-200/70 text-amber-900' }}">
                                    Tingkat SMA
                                </span>
                                <div class="check-icon w-6 h-6 rounded-full flex items-center justify-center text-sm {{ $currentLevel == 'Wira' ? 'bg-white text-amber-600' : 'hidden' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </div>
                            <div>
                                <div class="font-black text-lg">PMR Wira</div>
                                <div class="text-xs {{ $currentLevel == 'Wira' ? 'text-amber-100' : 'text-amber-700' }} mt-0.5">SMA / SMK / MA / Sederajat</div>
                            </div>
                        </label>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- STEP 2: Data Sekolah & Pembina -->
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 rounded-full bg-red-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                        Identitas Sekolah & Pendamping
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Sekolah / Unit PMR <span class="text-rose-500">*</span></label>
                            <input type="text" name="school_name" value="{{ old('school_name') }}" required placeholder="Contoh: SMPN 1 CIAWI BOGOR" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500 uppercase font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Pembina / Pelatih <span class="text-rose-500">*</span></label>
                            <input type="text" name="advisor_name" value="{{ old('advisor_name') }}" required placeholder="Nama lengkap pendamping" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp Pembina <span class="text-rose-500">*</span></label>
                            <input type="text" name="advisor_phone" value="{{ old('advisor_phone') }}" required placeholder="0812xxxxxxxx" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="advisor_email" value="{{ old('advisor_email') }}" required placeholder="contoh: kontak@sekolah.sch.id / pmr@gmail.com" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500">
                            <p class="text-[11px] text-slate-500 mt-1.5 flex items-start gap-1.5 leading-snug">
                                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 text-xs"></i>
                                <span>Bisa menggunakan <strong>alamat email sekolah, email pembina, atau email organisasi / PMR</strong>. Digunakan untuk informasi kegiatan, undangan lomba, dan tanda terima pendaftaran.</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Sekolah</label>
                            <input type="text" name="school_address" value="{{ old('school_address') }}" placeholder="Kota/Kabupaten Bogor" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500">
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- STEP 3: Pilih Cabang Mata Lomba -->
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-red-600 text-white text-xs flex items-center justify-center font-bold">3</span>
                        Pilih Cabang Lomba & Jumlah Regu
                    </h3>
                    <p class="text-xs text-slate-500 mb-3">Centang cabang lomba yang ingin diikuti.</p>

                    <div class="bg-amber-50 border border-amber-200 text-amber-950 p-4 rounded-2xl text-xs flex items-start gap-3 mb-4 shadow-2xs">
                        <i class="fa-solid fa-shield-halved text-amber-600 text-base mt-0.5 shrink-0"></i>
                        <div class="leading-relaxed space-y-2 w-full">
                            <div>
                                <strong class="text-amber-900 text-xs">Ketentuan Kuota Regu per Sekolah:</strong>
                                <ul class="list-disc pl-4 space-y-0.5 text-amber-900/90 mt-0.5">
                                    <li>Setiap sekolah maksimal mengirimkan 2 regu putra dan 2 regu putri untuk masing-masing cabang lomba umum.</li>
                                    <li>Khusus cabang Olimpiade Kepalangmerahan masing-masing sekolah dibatasi maksimum 3 regu dan untuk cabang Mewarnai tidak dibatasi.</li>
                                </ul>
                            </div>
                            <div class="pt-1.5 border-t border-amber-200/80">
                                <strong class="text-amber-900 text-xs">Aturan Jumlah Peserta / Anggota dalam 1 Regu:</strong>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-1 text-[11px] text-amber-900/90 mt-1">
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 text-[9px]"></i> <span><strong>Pertolongan Pertama:</strong> 3 orang / regu</span></div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 text-[9px]"></i> <span><strong>Ketangkasan Tandu:</strong> 2 orang / regu</span></div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 text-[9px]"></i> <span><strong>Ketangkasan Cuci Tangan:</strong> 2 orang / regu</span></div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 text-[9px]"></i> <span><strong>Lomba Mewarnai:</strong> 1 orang / regu</span></div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 text-[9px]"></i> <span><strong>Cepat Tepat:</strong> 3 orang / regu</span></div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 text-[9px]"></i> <span><strong>Pendidikan Remaja Sebaya:</strong> 2 orang / regu</span></div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 text-[9px]"></i> <span><strong>Olimpiade Kepalangmerahan:</strong> 1 orang / regu</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Banner Fasilitas Upload Excel Daftar Peserta Per Regu -->
                    <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 border border-emerald-200 text-emerald-950 p-4 rounded-2xl text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 text-sm shadow-xs">
                                <i class="fa-solid fa-file-excel"></i>
                            </div>
                            <div>
                                <div class="font-extrabold text-emerald-950 text-xs flex items-center gap-1.5 flex-wrap">
                                    <span>Upload Daftar Peserta Tiap Regu (Format Excel)</span>
                                    <span class="bg-emerald-200 text-emerald-800 text-[10px] font-black uppercase px-2 py-0.5 rounded-full">Tersedia</span>
                                </div>
                                <p class="text-[11px] text-emerald-800/90 mt-0.5">
                                    Unggah file Excel daftar nama peserta lomba untuk masing-masing regu. Nama peserta otomatis tersimpan ke database & tertera di kartu peserta.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('lomba.template-peserta') }}" download class="inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-xs transition shrink-0">
                            <i class="fa-solid fa-cloud-arrow-down"></i>
                            <span>Unduh Template Excel Regu (.xlsx)</span>
                        </a>
                    </div>

                    @foreach(['Mula', 'Madya', 'Wira'] as $lvl)
                        <div id="category-group-{{ $lvl }}" class="category-level-group space-y-3 {{ $currentLevel == $lvl ? '' : 'hidden' }}">
                            @foreach($categoriesByLevel->get($lvl, []) as $cat)
                                @php
                                    $isPa = $cat->gender_category === 'Putra';
                                    $isPi = $cat->gender_category === 'Putri';
                                    $isMulti = $cat->isMultiTeamAllowed();
                                    $maxAllowed = $cat->maxTeamsPerSchool();
                                    $isChecked = in_array($cat->id, old('categories', []));
                                    $currentCatFee = $cat->getFeeForWave($wave ?? 1);
                                @endphp
                                <div class="border {{ $isPa ? 'border-blue-200 bg-blue-50/20' : ($isPi ? 'border-rose-200 bg-rose-50/20' : 'border-slate-200 bg-slate-50/40') }} rounded-2xl p-4 hover:border-red-400 hover:shadow-xs transition space-y-3" id="cat-card-{{ $cat->id }}" data-multi="{{ $isMulti ? 'true' : 'false' }}" data-max-teams="{{ $maxAllowed }}">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <label class="flex items-start sm:items-center gap-3 cursor-pointer flex-grow select-none">
                                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" data-fee="{{ intval($currentCatFee) }}" class="cat-checkbox mt-1 sm:mt-0 w-5 h-5 text-red-600 rounded border-slate-300 focus:ring-red-500" {{ $isChecked ? 'checked' : '' }} onchange="onCategoryToggle({{ $cat->id }})">
                                            <div>
                                                <div class="flex items-center flex-wrap gap-2">
                                                    <span class="font-black text-slate-900 text-sm">{{ $cat->name }}</span>
                                                    @if($isPa)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200">
                                                            <i class="fa-solid fa-mars text-blue-600"></i> Kategori Putra
                                                        </span>
                                                    @elseif($isPi)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-200">
                                                            <i class="fa-solid fa-venus text-rose-600"></i> Kategori Putri
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                                            <i class="fa-solid fa-users text-slate-500"></i> Umum / Campuran
                                                        </span>
                                                    @endif

                                                    @if($cat->isUnlimitedTeams())
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-800 border border-purple-300" title="Kuota regu tidak dibatasi">
                                                            <i class="fa-solid fa-infinity text-purple-600"></i> Kuota: Tidak Dibatasi
                                                        </span>
                                                    @elseif($maxAllowed == 3)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300" title="Khusus cabang ini dibatasi maksimum 3 regu per sekolah">
                                                            <i class="fa-solid fa-layer-group text-emerald-600"></i> Kuota: Maks. 3 Regu
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-300" title="Dibatasi maksimal 2 regu per sekolah">
                                                            <i class="fa-solid fa-user-group text-blue-600"></i> Kuota: Maks. 2 Regu
                                                        </span>
                                                    @endif

                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300" title="Aturan jumlah anggota per regu">
                                                        <i class="fa-solid fa-users text-emerald-600"></i> {{ $cat->getRequiredTeamMembers() }} Orang / Regu
                                                    </span>
                                                </div>
                                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                                                    <span>Tingkat: <span class="font-bold text-slate-700">PMR {{ $cat->level }}</span></span>
                                                    <span>&bull;</span>
                                                    <span>Jumlah Peserta: <strong class="text-emerald-700 font-bold">{{ $cat->getRequiredTeamMembers() }} Orang</strong></span>
                                                    <span>&bull;</span>
                                                    <span>Biaya ({{ $wave == 2 ? ($event->wave2_name ?: 'Gel. 2') : ($event->wave1_name ?: 'Gel. 1') }}): <strong class="text-red-600 font-bold font-mono">Rp {{ number_format($currentCatFee, 0, ',', '.') }}</strong> <span class="text-[11px] text-slate-400">/ Regu</span></span>
                                                    @if($cat->registration_fee_wave1 !== null && $cat->registration_fee_wave2 !== null && $cat->registration_fee_wave1 != $cat->registration_fee_wave2)
                                                        <span class="text-[10px] text-slate-400 font-medium">
                                                            @if($wave == 1)
                                                                (Tarif Gel. 2: Rp {{ number_format($cat->registration_fee_wave2, 0, ',', '.') }})
                                                            @else
                                                                (Tarif Gel. 1: Rp {{ number_format($cat->registration_fee_wave1, 0, ',', '.') }})
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>

                                        @if($isMulti)
                                            <button type="button" onclick="addTeamRow({{ $cat->id }})" id="btn-add-team-{{ $cat->id }}" class="btn-add-team {{ $isChecked ? '' : 'hidden' }} shrink-0 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                                                <i class="fa-solid fa-plus-circle"></i> + Tambah Regu
                                            </button>
                                        @else
                                            <div id="btn-add-team-{{ $cat->id }}" class="hidden"></div>
                                        @endif
                                    </div>

                                    <!-- Dynamic Team Inputs Container -->
                                    <div id="teams-container-{{ $cat->id }}" class="teams-container space-y-2.5 pt-2.5 border-t border-slate-200/60 {{ $isChecked ? '' : 'hidden' }}">
                                        @php
                                            $oldLabels = old('team_labels.'.$cat->id);
                                            if (!is_array($oldLabels) || empty($oldLabels)) {
                                                $oldLabels = ['Regu A'];
                                            }
                                            if (!$isMulti) $oldLabels = array_slice($oldLabels, 0, 1);
                                        @endphp
                                        @foreach($oldLabels as $idx => $lbl)
                                            @php
                                                $letter = chr(65 + $idx);
                                                $rowLabel = $isMulti ? 'Regu ' . $letter . ':' : 'Regu A:';
                                                $rowVal = (isset($lbl) && trim((string)$lbl) !== '' && !in_array($lbl, ['Regu Putra', 'Regu Putri'])) ? $lbl : ('Regu ' . $letter);
                                            @endphp
                                            <div class="team-input-row bg-white border border-slate-200/90 rounded-xl p-3 shadow-2xs space-y-2.5 transition">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-bold text-slate-600 w-24 shrink-0 row-index-label">{{ $rowLabel }}</span>
                                                    <input type="text" name="team_labels[{{ $cat->id }}][{{ $idx }}]" value="{{ $rowVal }}" placeholder="Label Regu (cth: Regu {{ $letter }})" class="team-label-input flex-grow text-xs bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl focus:bg-white focus:outline-none focus:border-red-500 shadow-2xs font-semibold text-slate-800" oninput="calculateFee()">
                                                    @if($isMulti)
                                                        <button type="button" onclick="removeTeamRow(this, {{ $cat->id }})" class="btn-remove-team {{ count($oldLabels) > 1 ? '' : 'hidden' }} text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition" title="Hapus Regu Ini">
                                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Upload File Excel Peserta Regu Ini -->
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-100 text-xs">
                                                    <div class="flex items-center flex-wrap gap-2">
                                                        <input type="file" name="team_files[{{ $cat->id }}][{{ $idx }}]" accept=".xlsx,.xls,.csv" class="hidden team-file-input" onchange="handleTeamExcelUpload(this, {{ $cat->id }})">
                                                        <input type="hidden" name="team_members[{{ $cat->id }}][{{ $idx }}]" class="team-members-json" value="">
                                                        
                                                        <button type="button" onclick="triggerFileInput(this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 hover:border-emerald-400 active:scale-95 transition shadow-2xs cursor-pointer">
                                                            <i class="fa-solid fa-file-excel text-emerald-600 text-xs"></i>
                                                            <span class="btn-upload-text">Upload Excel Peserta</span>
                                                        </button>

                                                        <a href="{{ route('lomba.template-peserta') }}" download class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 hover:text-emerald-700 hover:underline px-1.5 py-1 rounded transition" title="Unduh format template Excel untuk regu ini">
                                                            <i class="fa-solid fa-download text-emerald-600 text-[10px]"></i>
                                                            <span>Template Excel</span>
                                                        </a>
                                                    </div>

                                                    <div class="team-file-status text-[11px] text-slate-400 flex items-center gap-1.5 truncate max-w-full sm:max-w-xs">
                                                        <span class="status-text italic text-slate-400">(Opsional: unggah daftar peserta)</span>
                                                    </div>
                                                </div>

                                                <!-- Live Preview Box (Tampil saat file excel berhasil di-parse) -->
                                                <div class="team-members-preview-box hidden bg-emerald-50/70 border border-emerald-200 rounded-lg p-2.5 text-xs text-emerald-950 space-y-1.5">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="font-bold flex items-center gap-1.5 text-[11px] text-emerald-900">
                                                            <i class="fa-solid fa-users text-emerald-600"></i>
                                                            <span class="preview-count-label">0 Peserta Terdaftar di Excel</span>
                                                        </span>
                                                        <button type="button" onclick="togglePreviewList(this)" class="btn-toggle-preview text-[10px] font-bold text-emerald-700 hover:text-emerald-900 underline cursor-pointer">
                                                            Sembunyikan
                                                        </button>
                                                    </div>
                                                    <div class="preview-members-list flex flex-wrap gap-1 max-h-32 overflow-y-auto pt-1"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <hr class="border-slate-100">

                <!-- SECTION: Unggah Massal Berkas Peserta Lomba (1 File untuk Seluruh Regu) -->
                <div id="mass-upload-section" class="bg-gradient-to-br from-emerald-50/70 via-white to-slate-50 border-2 border-dashed border-emerald-300 rounded-3xl p-5 sm:p-7 shadow-sm transition space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-emerald-100/90 pb-4">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#107c41] to-emerald-700 text-white flex items-center justify-center text-xl shadow-md shrink-0">
                                <i class="fa-solid fa-file-excel"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Unggah Massal Berkas Peserta Lomba</h3>
                                    <span class="bg-[#107c41] text-white text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full shadow-2xs">Kolektif 1x Upload</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    Cukup unduh <strong>1 file template Excel</strong> yang otomatis memuat cabang yang Anda centang di atas, isi nama siswa, lalu unggah di sini untuk seluruh regu.
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-2">
                            <button type="button" onclick="downloadDynamicMassTemplate()" id="btn-download-mass-template" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#107c41] to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 active:scale-95 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow transition shrink-0 cursor-pointer">
                                <i class="fa-solid fa-cloud-arrow-down text-sm text-emerald-200"></i>
                                <span>Unduh Template Excel Massal (.xlsx)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Informasi Otomatis & Alur Ringkas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                        <div class="bg-white/90 border border-emerald-200/90 rounded-2xl p-3.5 flex items-start gap-3 shadow-2xs">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-xs">1</span>
                            <div>
                                <strong class="text-slate-800 text-xs">No. Registrasi Sistem</strong>
                                <p class="text-[11px] text-slate-500 mt-0.5">Ditanam &amp; terkunci otomatis:</p>
                                <span class="inline-block mt-1 font-mono text-red-600 font-black bg-red-50 px-2 py-0.5 rounded border border-red-200/60 text-xs" id="display-preview-reg-code">{{ $previewRegistrationCode ?? 'SBB-M54342H' }}</span>
                            </div>
                        </div>

                        <div class="bg-white/90 border border-emerald-200/90 rounded-2xl p-3.5 flex items-start gap-3 shadow-2xs">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-xs">2</span>
                            <div>
                                <strong class="text-slate-800 text-xs">Dropdown Cabang Pilihan</strong>
                                <p class="text-[11px] text-slate-500 mt-0.5">Kolom <em>Cabang Lomba</em> otomatis hanya berisi pilihan cabang yang Anda centang pada Langkah 3 di atas.</p>
                            </div>
                        </div>

                        <div class="bg-white/90 border border-emerald-200/90 rounded-2xl p-3.5 flex items-start gap-3 shadow-2xs">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-xs">3</span>
                            <div>
                                <strong class="text-slate-800 text-xs">Otomatis Terpetakan</strong>
                                <p class="text-[11px] text-slate-500 mt-0.5">Siswa otomatis tersimpan ke tabel regu &amp; kategori masing-masing di database sekolah.</p>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Dropzone Area -->
                    <div class="relative bg-white border-2 border-dashed border-emerald-300 hover:border-emerald-500 rounded-2xl p-6 text-center transition cursor-pointer group shadow-2xs" onclick="document.getElementById('mass_participants_file_input').click()">
                        <input type="file" name="mass_participants_file" id="mass_participants_file_input" accept=".xlsx,.xls,.csv" class="hidden" onchange="handleMassExcelUpload(this)">
                        <input type="hidden" name="registration_code" id="registration_code_input" value="{{ $previewRegistrationCode ?? 'SBB-M54342H' }}">

                        <div class="flex flex-col items-center justify-center gap-2.5">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl transition">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition">Klik untuk Memilih Berkas Excel Massal</span>
                                <span class="text-xs text-slate-500 block mt-0.5">atau seret dan lepas berkas (.xlsx, .xls) ke area ini</span>
                            </div>
                            <div id="mass-file-info-badge" class="hidden inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-300 mt-1 shadow-2xs">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                <span id="mass-file-name-text">Template_Massal.xlsx</span>
                                <span id="mass-file-size-text" class="text-emerald-700/70 text-[10px]"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview Container for Mass Excel -->
                    <div id="mass-preview-container" class="hidden bg-emerald-50/70 border border-emerald-300 rounded-2xl p-4 sm:p-5 text-xs space-y-3.5 shadow-2xs">
                        <div class="flex items-center justify-between gap-2 border-b border-emerald-200/90 pb-3">
                            <div class="flex items-center gap-2 font-bold text-emerald-950 text-sm">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                                <span id="mass-preview-summary-title">0 Peserta Berhasil Terdeteksi</span>
                            </div>
                            <button type="button" onclick="toggleMassPreviewDetails()" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 underline cursor-pointer" id="btn-toggle-mass-preview">
                                Sembunyikan Rincian
                            </button>
                        </div>

                        <div id="mass-preview-teams-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-72 overflow-y-auto pr-1">
                            <!-- Dynamic Cards for each detected team -->
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- STEP 4: Pembayaran & Bukti Transfer -->
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 rounded-full bg-red-600 text-white text-xs flex items-center justify-center font-bold">4</span>
                        Rincian Biaya & Pembayaran
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                        @php
                            $bankAccounts = $event->getAllBankAccounts();
                            $activeAccount = $event->getBankAccountForLevel($currentLevel);
                            $acc1 = $activeAccount['account_1'] ?? $activeAccount;
                            $acc2 = $activeAccount['account_2'] ?? null;
                        @endphp

                        <!-- Rekening Box Dinamis Per Tingkat (Mendukung 2 Rekening) -->
                        <div id="bank-card-container" class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-900 text-white p-5 rounded-2xl shadow-sm border border-slate-700/80 relative overflow-hidden transition-all duration-300 space-y-4">
                            <div class="flex items-center justify-between gap-2">
                                <div id="bank-badge-display" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full {{ $currentLevel === 'Mula' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($currentLevel === 'Wira' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30') }} text-[10px] font-black uppercase tracking-wider">
                                    <i class="fa-solid fa-circle-dot text-[8px] animate-pulse"></i>
                                    <span>Tujuan Transfer PMR {{ $currentLevel }} ({{ $currentLevel === 'Mula' ? 'SD/MI' : ($currentLevel === 'Wira' ? 'SMA/SMK' : 'SMP/MTs') }})</span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 bg-white/5 px-2 py-0.5 rounded-md">2 Opsi Rekening</span>
                            </div>

                            <!-- Pilihan Rekening 1 (Utama) -->
                            <div class="bg-white/10 rounded-xl p-3.5 border border-white/10 space-y-2">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-300 flex items-center gap-1.5">
                                        <i class="fa-solid fa-building-columns text-amber-400"></i>
                                        <span id="bank-name-display">{{ $acc1['bank_name'] }}</span>
                                    </span>
                                    <span class="text-[9px] bg-amber-400/20 text-amber-300 px-2 py-0.5 rounded font-black uppercase">Rekening 1</span>
                                </div>

                                <div class="flex items-center justify-between gap-2">
                                    <div class="overflow-hidden">
                                        <div class="text-lg sm:text-xl font-mono font-black text-amber-300 tracking-wider truncate" id="bank-number-display">{{ $acc1['account_number'] }}</div>
                                    </div>
                                    <button type="button" onclick="copyAccountNumber('bank-number-display', 'copy-btn-text-1')" class="bg-white/15 hover:bg-white/25 active:scale-95 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition shrink-0 cursor-pointer" id="btn-copy-account-1" title="Salin nomor rekening 1">
                                        <i class="fa-regular fa-copy"></i>
                                        <span id="copy-btn-text-1">Salin</span>
                                    </button>
                                </div>

                                <div class="text-[11px] text-slate-300">
                                    <span class="text-slate-400">a.n</span>
                                    <strong class="text-white uppercase tracking-wide ml-1" id="bank-holder-display">{{ $acc1['account_holder'] }}</strong>
                                </div>
                            </div>

                            <!-- Pilihan Rekening 2 (Alternatif) -->
                            <div id="bank-account-2-container" class="bg-white/10 rounded-xl p-3.5 border border-white/10 space-y-2 {{ !empty($acc2['account_number']) ? '' : 'hidden' }}">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-300 flex items-center gap-1.5">
                                        <i class="fa-solid fa-building-columns text-emerald-400"></i>
                                        <span id="bank-name-2-display">{{ $acc2['bank_name'] ?? 'BANK BSI' }}</span>
                                    </span>
                                    <span class="text-[9px] bg-emerald-400/20 text-emerald-300 px-2 py-0.5 rounded font-black uppercase">Rekening 2</span>
                                </div>

                                <div class="flex items-center justify-between gap-2">
                                    <div class="overflow-hidden">
                                        <div class="text-lg sm:text-xl font-mono font-black text-emerald-300 tracking-wider truncate" id="bank-number-2-display">{{ $acc2['account_number'] ?? '-' }}</div>
                                    </div>
                                    <button type="button" onclick="copyAccountNumber('bank-number-2-display', 'copy-btn-text-2')" class="bg-white/15 hover:bg-white/25 active:scale-95 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition shrink-0 cursor-pointer" id="btn-copy-account-2" title="Salin nomor rekening 2">
                                        <i class="fa-regular fa-copy"></i>
                                        <span id="copy-btn-text-2">Salin</span>
                                    </button>
                                </div>

                                <div class="text-[11px] text-slate-300">
                                    <span class="text-slate-400">a.n</span>
                                    <strong class="text-white uppercase tracking-wide ml-1" id="bank-holder-2-display">{{ $acc2['account_holder'] ?? '-' }}</strong>
                                </div>
                            </div>

                            <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                                <span>Silakan transfer ke salah satu nomor rekening (Rekening 1 atau 2) di atas.</span>
                            </div>
                            
                            <div class="pt-3 border-t border-slate-700/80 flex justify-between items-center text-xs">
                                <span class="text-slate-400">Periode Pendaftaran:</span>
                                <span class="font-bold text-amber-300">{{ $waveLabelText }}</span>
                            </div>
                        </div>

                        <!-- Ringkasan & Upload -->
                        <div class="space-y-4">
                            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-xs text-red-800 font-medium">Total Regu Didaftarkan:</div>
                                        <div class="text-sm font-bold text-red-900"><span id="selected-count">0</span> Regu Lomba</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-red-800 font-medium">Total Tagihan:</div>
                                        <div class="text-xl font-black text-red-700" id="total-fee-display">Rp 0</div>
                                    </div>
                                </div>
                                <div id="fee-breakdown-list" class="mt-3 pt-3 border-t border-red-200/60 space-y-1 text-[11px] text-red-900/90 hidden">
                                    <!-- Rincian item dinamis -->
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Upload Bukti Transfer <span class="text-rose-500">*</span></label>
                                <input type="file" name="payment_proof" accept="image/*,.pdf" required class="w-full text-xs text-slate-600 bg-slate-50 border border-slate-200 rounded-xl file:mr-3 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:text-xs file:font-bold file:bg-red-600 file:text-white hover:file:bg-red-700">
                                <div class="text-[10px] text-slate-500 mt-1">Format: JPG, PNG, atau PDF. Maks: 5MB.</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Submit Footer -->
            <div class="bg-slate-50 border-t border-slate-200 p-6 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 text-center sm:text-left">
                    Pastikan seluruh data sudah diisi dengan benar sebelum menekan tombol kirim.
                </div>
                <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-extrabold shadow-lg shadow-red-900/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Pendaftaran Sekarang
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const feePerCat = {{ intval($event->registration_fee ?: 150000) }};
    const bankAccountsMap = @json($bankAccounts ?? []);

    function updateBankAccountDisplay(lvl) {
        if (!bankAccountsMap || !bankAccountsMap[lvl]) return;
        const accData = bankAccountsMap[lvl];
        const acc1 = accData.account_1 || accData;
        const acc2 = accData.account_2 || null;

        // Rekening 1
        const bankNameEl = document.getElementById('bank-name-display');
        const bankNumberEl = document.getElementById('bank-number-display');
        const bankHolderEl = document.getElementById('bank-holder-display');

        if (bankNameEl && acc1.bank_name) bankNameEl.textContent = acc1.bank_name;
        if (bankNumberEl && acc1.account_number) bankNumberEl.textContent = acc1.account_number;
        if (bankHolderEl && acc1.account_holder) bankHolderEl.textContent = acc1.account_holder;

        // Rekening 2
        const bankName2El = document.getElementById('bank-name-2-display');
        const bankNumber2El = document.getElementById('bank-number-2-display');
        const bankHolder2El = document.getElementById('bank-holder-2-display');
        const container2El = document.getElementById('bank-account-2-container');

        if (acc2 && acc2.account_number) {
            if (bankName2El) bankName2El.textContent = acc2.bank_name;
            if (bankNumber2El) bankNumber2El.textContent = acc2.account_number;
            if (bankHolder2El) bankHolder2El.textContent = acc2.account_holder;
            if (container2El) container2El.classList.remove('hidden');
        } else {
            if (container2El) container2El.classList.add('hidden');
        }

        const bankBadgeEl = document.getElementById('bank-badge-display');
        const bankCardEl = document.getElementById('bank-card-container');
        const copyBtn1El = document.getElementById('btn-copy-account-1');
        const copyBtn2El = document.getElementById('btn-copy-account-2');

        if (bankBadgeEl) {
            if (lvl === 'Mula') {
                bankBadgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-black uppercase tracking-wider';
                bankBadgeEl.innerHTML = '<i class="fa-solid fa-circle-dot text-[8px] animate-pulse"></i><span>Tujuan Transfer PMR Mula (SD/MI)</span>';
                if (bankCardEl) {
                    bankCardEl.className = 'bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950/80 text-white p-5 rounded-2xl shadow-md border-2 border-emerald-500/60 relative overflow-hidden transition-all duration-300 space-y-4';
                }
                if (copyBtn1El) copyBtn1El.className = 'bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition shrink-0 cursor-pointer';
                if (copyBtn2El) copyBtn2El.className = 'bg-emerald-700 hover:bg-emerald-600 active:scale-95 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition shrink-0 cursor-pointer';
            } else if (lvl === 'Madya') {
                bankBadgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-[10px] font-black uppercase tracking-wider';
                bankBadgeEl.innerHTML = '<i class="fa-solid fa-circle-dot text-[8px] animate-pulse"></i><span>Tujuan Transfer PMR Madya (SMP/MTs)</span>';
                if (bankCardEl) {
                    bankCardEl.className = 'bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950/80 text-white p-5 rounded-2xl shadow-md border-2 border-blue-500/60 relative overflow-hidden transition-all duration-300 space-y-4';
                }
                if (copyBtn1El) copyBtn1El.className = 'bg-blue-600 hover:bg-blue-500 active:scale-95 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition shrink-0 cursor-pointer';
                if (copyBtn2El) copyBtn2El.className = 'bg-blue-700 hover:bg-blue-600 active:scale-95 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition shrink-0 cursor-pointer';
            } else if (lvl === 'Wira') {
                bankBadgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-black uppercase tracking-wider';
                bankBadgeEl.innerHTML = '<i class="fa-solid fa-circle-dot text-[8px] animate-pulse"></i><span>Tujuan Transfer PMR Wira (SMA/SMK)</span>';
                if (bankCardEl) {
                    bankCardEl.className = 'bg-gradient-to-br from-slate-950 via-slate-900 to-amber-950/80 text-white p-5 rounded-2xl shadow-md border-2 border-amber-500/60 relative overflow-hidden transition-all duration-300 space-y-4';
                }
                if (copyBtn1El) copyBtn1El.className = 'bg-amber-600 hover:bg-amber-500 active:scale-95 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition shrink-0 cursor-pointer';
                if (copyBtn2El) copyBtn2El.className = 'bg-amber-700 hover:bg-amber-600 active:scale-95 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition shrink-0 cursor-pointer';
            }
        }
    }

    function copyAccountNumber(numId, btnTextId) {
        const numEl = document.getElementById(numId);
        if (!numEl) return;
        const rawText = numEl.textContent.trim().replace(/[^0-9]/g, '') || numEl.textContent.trim();
        
        const setSuccess = () => {
            const btnText = document.getElementById(btnTextId);
            if (btnText) {
                btnText.textContent = 'Tersalin!';
                setTimeout(() => {
                    btnText.textContent = 'Salin';
                }, 2000);
            }
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(rawText).then(setSuccess).catch(() => {
                fallbackCopy(rawText, setSuccess);
            });
        } else {
            fallbackCopy(rawText, setSuccess);
        }
    }

    function fallbackCopy(text, callback) {
        const input = document.createElement('textarea');
        input.value = text;
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        input.select();
        try {
            document.execCommand('copy');
            if (callback) callback();
        } catch (e) {
            console.error('Copy failed', e);
        }
        document.body.removeChild(input);
    }

    function switchLevel(lvl) {
        // Reset Mula
        const cardMula = document.getElementById('card-Mula');
        cardMula.className = 'level-card cursor-pointer border-2 rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 relative bg-emerald-50/60 text-emerald-950 border-emerald-200 hover:border-emerald-400 hover:bg-emerald-50';
        cardMula.querySelector('.check-icon').classList.add('hidden');
        cardMula.querySelector('input').checked = false;

        // Reset Madya
        const cardMadya = document.getElementById('card-Madya');
        cardMadya.className = 'level-card cursor-pointer border-2 rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 relative bg-blue-50/60 text-blue-950 border-blue-200 hover:border-blue-400 hover:bg-blue-50';
        cardMadya.querySelector('.check-icon').classList.add('hidden');
        cardMadya.querySelector('input').checked = false;

        // Reset Wira
        const cardWira = document.getElementById('card-Wira');
        cardWira.className = 'level-card cursor-pointer border-2 rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 relative bg-amber-50/60 text-amber-950 border-amber-200 hover:border-amber-400 hover:bg-amber-50';
        cardWira.querySelector('.check-icon').classList.add('hidden');
        cardWira.querySelector('input').checked = false;

        // Activate Selected
        if (lvl === 'Mula') {
            cardMula.className = 'level-card cursor-pointer border-2 rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 relative bg-emerald-600 text-white border-emerald-600 shadow-lg shadow-emerald-700/20 scale-[1.02]';
            cardMula.querySelector('.check-icon').classList.remove('hidden');
            cardMula.querySelector('input').checked = true;
        } else if (lvl === 'Madya') {
            cardMadya.className = 'level-card cursor-pointer border-2 rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 relative bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-700/20 scale-[1.02]';
            cardMadya.querySelector('.check-icon').classList.remove('hidden');
            cardMadya.querySelector('input').checked = true;
        } else if (lvl === 'Wira') {
            cardWira.className = 'level-card cursor-pointer border-2 rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 relative bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-600/20 scale-[1.02]';
            cardWira.querySelector('.check-icon').classList.remove('hidden');
            cardWira.querySelector('input').checked = true;
        }

        // Switch category groups
        document.querySelectorAll('.category-level-group').forEach(g => g.classList.add('hidden'));
        const targetGroup = document.getElementById('category-group-' + lvl);
        if (targetGroup) {
            targetGroup.classList.remove('hidden');
        }

        // Switch destination bank account per level
        updateBankAccountDisplay(lvl);

        // Update prefix No. Registrasi Sistem
        const regCodeInput = document.getElementById('registration_code_input');
        const regCodeDisplay = document.getElementById('display-preview-reg-code');
        if (regCodeInput && regCodeDisplay) {
            let currentCode = regCodeInput.value || 'SBB-M54342H';
            const char = lvl === 'Mula' ? 'U' : (lvl === 'Wira' ? 'W' : 'M');
            const newCode = currentCode.replace(/^SBB-[UMW]/, 'SBB-' + char);
            regCodeInput.value = newCode;
            regCodeDisplay.textContent = newCode;
        }

        // Uncheck hidden group checkboxes and hide their team containers
        document.querySelectorAll('.category-level-group.hidden input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
            const cId = cb.value;
            const cont = document.getElementById('teams-container-' + cId);
            const btn = document.getElementById('btn-add-team-' + cId);
            if (cont) cont.classList.add('hidden');
            if (btn) btn.classList.add('hidden');
        });

        calculateFee();
    }

    function onCategoryToggle(catId) {
        const cb = document.querySelector(`input[name="categories[]"][value="${catId}"]`);
        const container = document.getElementById('teams-container-' + catId);
        const btnAdd = document.getElementById('btn-add-team-' + catId);

        if (cb && cb.checked) {
            container.classList.remove('hidden');
            if (btnAdd) btnAdd.classList.remove('hidden');
            if (container.querySelectorAll('.team-input-row').length === 0) {
                addTeamRow(catId);
            } else {
                updateRemoveButtons(catId);
            }
        } else {
            container.classList.add('hidden');
            if (btnAdd) btnAdd.classList.add('hidden');
        }
        calculateFee();
    }

    function triggerFileInput(btn) {
        const card = btn.closest('.team-input-row');
        if (!card) return;
        const fileInput = card.querySelector('.team-file-input');
        if (fileInput) fileInput.click();
    }

    function handleTeamExcelUpload(input, catId) {
        const card = input.closest('.team-input-row');
        if (!card) return;

        const file = input.files && input.files[0];
        if (!file) return;

        const statusEl = card.querySelector('.team-file-status');
        const btnUploadText = card.querySelector('.btn-upload-text');
        const membersInput = card.querySelector('.team-members-json');

        if (statusEl) {
            statusEl.innerHTML = `<span class="text-amber-600 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-spinner fa-spin text-amber-500"></i> Membaca file ${escapeHtml(file.name)}...</span>`;
        }
        if (btnUploadText) btnUploadText.textContent = 'Membaca Excel...';

        const formData = new FormData();
        formData.append('excel_file', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("lomba.parse-excel-peserta") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(errData => { throw new Error(errData.message || 'Gagal memproses file Excel.'); });
            }
            return res.json();
        })
        .then(data => {
            if (data.success && Array.isArray(data.members)) {
                if (membersInput) {
                    membersInput.value = JSON.stringify(data.members);
                }
                if (statusEl) {
                    statusEl.innerHTML = `<span class="text-emerald-700 font-bold flex items-center gap-1"><i class="fa-solid fa-circle-check text-emerald-600"></i> <span class="truncate max-w-[130px] sm:max-w-[200px]" title="${escapeHtml(data.filename)}">${escapeHtml(data.filename)}</span> (${data.count} Peserta)</span>`;
                }
                if (btnUploadText) btnUploadText.textContent = 'Ganti Excel';
                renderMembersPreview(card, data.members);
            } else {
                throw new Error(data.message || 'Format file Excel tidak sesuai.');
            }
        })
        .catch(err => {
            if (statusEl) {
                statusEl.innerHTML = `<span class="text-rose-600 font-semibold flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> ${escapeHtml(err.message)}</span>`;
            }
            if (btnUploadText) btnUploadText.textContent = 'Upload Excel Peserta';
        });
    }

    function renderMembersPreview(card, members) {
        const previewBox = card.querySelector('.team-members-preview-box');
        const countLabel = card.querySelector('.preview-count-label');
        const listEl = card.querySelector('.preview-members-list');
        if (!previewBox || !listEl) return;

        if (!members || members.length === 0) {
            previewBox.classList.add('hidden');
            return;
        }

        if (countLabel) {
            countLabel.textContent = `${members.length} Peserta Terdaftar di Excel`;
        }
        listEl.innerHTML = '';

        members.forEach((m, idx) => {
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-white text-emerald-900 border border-emerald-200 shadow-2xs';
            const name = m.name || m.nama || (typeof m === 'string' ? m : `Peserta ${idx + 1}`);
            const role = (m.role || m.peran) ? ` (${m.role || m.peran})` : '';
            badge.innerHTML = `<span class="font-bold text-emerald-700">${idx + 1}.</span> <span>${escapeHtml(name)}${escapeHtml(role)}</span>`;
            listEl.appendChild(badge);
        });

        previewBox.classList.remove('hidden');
    }

    function togglePreviewList(btn) {
        const card = btn.closest('.team-input-row');
        if (!card) return;
        const listEl = card.querySelector('.preview-members-list');
        if (!listEl) return;
        if (listEl.classList.contains('hidden')) {
            listEl.classList.remove('hidden');
            btn.textContent = 'Sembunyikan';
        } else {
            listEl.classList.add('hidden');
            btn.textContent = 'Lihat Pratinjau';
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function downloadDynamicMassTemplate() {
        const checkedBoxes = Array.from(document.querySelectorAll('.cat-checkbox:checked'));
        if (checkedBoxes.length === 0) {
            alert('Silakan centang minimal 1 cabang lomba pada Langkah 3 terlebih dahulu agar template Excel memuat pilihan cabang lomba Anda.');
            const step3El = document.querySelector('.category-level-group:not(.hidden)') || document.querySelector('.category-level-group');
            if (step3El) step3El.scrollIntoView({ behavior: 'smooth' });
            return;
        }

        const schoolName = document.querySelector('input[name="school_name"]')?.value || '';
        const activeLevelRadio = document.querySelector('input[name="level"]:checked');
        const level = activeLevelRadio ? activeLevelRadio.value : 'Madya';
        const advisorName = document.querySelector('input[name="advisor_name"]')?.value || '';
        const advisorPhone = document.querySelector('input[name="advisor_phone"]')?.value || '';
        const advisorEmail = document.querySelector('input[name="advisor_email"]')?.value || '';
        const schoolAddress = document.querySelector('input[name="school_address"]')?.value || '';
        const regCode = document.getElementById('registration_code_input')?.value || '';

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("lomba.download-mass-template") }}';
        form.style.display = 'none';

        const addField = (name, val) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = val;
            form.appendChild(input);
        };

        addField('_token', '{{ csrf_token() }}');
        addField('school_name', schoolName);
        addField('level', level);
        addField('advisor_name', advisorName);
        addField('advisor_phone', advisorPhone);
        addField('advisor_email', advisorEmail);
        addField('school_address', schoolAddress);
        addField('registration_code', regCode);

        checkedBoxes.forEach(cb => {
            addField('categories[]', cb.value);
            const catId = cb.value;
            const container = document.getElementById('teams-container-' + catId);
            if (container) {
                const labelInputs = container.querySelectorAll('.team-label-input');
                labelInputs.forEach((inp, idx) => {
                    addField(`team_labels[${catId}][${idx}]`, inp.value);
                });
            }
        });

        document.body.appendChild(form);
        form.submit();
        setTimeout(() => form.remove(), 2500);
    }

    function handleMassExcelUpload(input) {
        const file = input.files && input.files[0];
        if (!file) return;

        const infoBadge = document.getElementById('mass-file-info-badge');
        const fileNameText = document.getElementById('mass-file-name-text');
        const fileSizeText = document.getElementById('mass-file-size-text');
        const previewContainer = document.getElementById('mass-preview-container');
        const summaryTitle = document.getElementById('mass-preview-summary-title');
        const teamsList = document.getElementById('mass-preview-teams-list');

        if (infoBadge && fileNameText) {
            infoBadge.classList.remove('hidden');
            fileNameText.textContent = `Sedang membaca ${file.name}...`;
            if (fileSizeText) fileSizeText.textContent = `(${(file.size / 1024).toFixed(1)} KB)`;
        }

        const formData = new FormData();
        formData.append('excel_file', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("lomba.parse-mass-excel") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(errData => { throw new Error(errData.message || 'Gagal memproses berkas Excel massal.'); });
            }
            return res.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Berkas tidak dapat dibaca.');
            }

            if (fileNameText) {
                fileNameText.textContent = `${file.name} (${data.total_participants} Peserta, ${data.teams_count} Regu)`;
            }

            if (summaryTitle) {
                summaryTitle.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-600 text-base"></i> <span>${data.total_participants} Peserta Berhasil Terdeteksi untuk ${data.teams_count} Regu</span>`;
            }

            if (teamsList) {
                teamsList.innerHTML = '';
                if (Array.isArray(data.grouped) && data.grouped.length > 0) {
                    data.grouped.forEach((grp, idx) => {
                        const isPa = grp.gender_category === 'Putra';
                        const isPi = grp.gender_category === 'Putri';
                        const genderBadgeClass = isPa ? 'bg-blue-100 text-blue-800 border-blue-200' : (isPi ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-slate-100 text-slate-800 border-slate-200');

                        const card = document.createElement('div');
                        card.className = 'bg-white border border-emerald-200 rounded-xl p-3 shadow-2xs space-y-2';

                        let membersHtml = '';
                        if (Array.isArray(grp.members)) {
                            grp.members.forEach((m, mIdx) => {
                                const isLeader = (m.role || '').toLowerCase().includes('ketua');
                                const roleBadgeClass = isLeader ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-700';
                                membersHtml += `
                                    <div class="flex items-center justify-between gap-1 text-[11px] py-0.5 border-b border-slate-100 last:border-none">
                                        <span class="truncate font-semibold text-slate-800">${mIdx + 1}. ${escapeHtml(m.name)}</span>
                                        <span class="text-[9px] px-1.5 py-0.2 rounded ${roleBadgeClass} shrink-0">${escapeHtml(m.role || 'Anggota')}</span>
                                    </div>
                                `;
                            });
                        }

                        const exp = grp.expected_members_count || 2;
                        const isExact = (grp.members_count === exp);
                        const sizeBadge = isExact
                            ? `<span class="bg-emerald-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shrink-0 shadow-2xs"><i class="fa-solid fa-circle-check"></i> ${grp.members_count} / ${exp} Siswa</span>`
                            : `<span class="bg-amber-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shrink-0 shadow-2xs" title="Standar kuota cabang ini: ${exp} orang per regu"><i class="fa-solid fa-triangle-exclamation"></i> ${grp.members_count} Siswa (Standar: ${exp})</span>`;

                        card.innerHTML = `
                            <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-1.5">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs">${escapeHtml(grp.category_name)}</div>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded border ${genderBadgeClass}">${escapeHtml(grp.gender_category)}</span>
                                        <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-1.5 py-0.2 rounded">${escapeHtml(grp.team_label)}</span>
                                    </div>
                                </div>
                                ${sizeBadge}
                            </div>
                            <div class="space-y-0.5 pt-0.5 max-h-28 overflow-y-auto pr-0.5">
                                ${membersHtml}
                            </div>
                        `;
                        teamsList.appendChild(card);
                    });
                }
            }

            if (previewContainer) {
                previewContainer.classList.remove('hidden');
            }

            // Beri notifikasi di status regu pada Langkah 3 di atas
            if (Array.isArray(data.grouped)) {
                data.grouped.forEach(grp => {
                    document.querySelectorAll('.team-input-row').forEach(row => {
                        const lblInput = row.querySelector('.team-label-input');
                        if (lblInput && (lblInput.value.toLowerCase().trim() === grp.team_label.toLowerCase().trim() || row.innerText.toLowerCase().includes(grp.category_name.toLowerCase()))) {
                            const statusEl = row.querySelector('.team-file-status');
                            if (statusEl) {
                                statusEl.innerHTML = `<span class="text-emerald-700 font-bold flex items-center gap-1"><i class="fa-solid fa-circle-check text-emerald-600"></i> Terisi dari File Massal (${grp.members_count} Peserta)</span>`;
                            }
                        }
                    });
                });
            }
        })
        .catch(err => {
            if (infoBadge && fileNameText) {
                fileNameText.innerHTML = `<span class="text-rose-600 font-bold"><i class="fa-solid fa-circle-exclamation"></i> ${escapeHtml(err.message)}</span>`;
            }
        });
    }

    function toggleMassPreviewDetails() {
        const teamsList = document.getElementById('mass-preview-teams-list');
        const btn = document.getElementById('btn-toggle-mass-preview');
        if (!teamsList || !btn) return;
        if (teamsList.classList.contains('hidden')) {
            teamsList.classList.remove('hidden');
            btn.textContent = 'Sembunyikan Rincian';
        } else {
            teamsList.classList.add('hidden');
            btn.textContent = 'Lihat Rincian';
        }
    }

    function addTeamRow(catId) {
        const card = document.getElementById('cat-card-' + catId);
        const isMulti = card ? card.dataset.multi === 'true' : true;
        const maxTeams = card ? parseInt(card.dataset.maxTeams || (isMulti ? '3' : '1')) : 3;
        const container = document.getElementById('teams-container-' + catId);
        const count = container ? container.querySelectorAll('.team-input-row').length : 0;

        if (count >= maxTeams) {
            alert(`Cabang lomba ini dibatasi maksimal ${maxTeams} regu per sekolah.`);
            return;
        }

        const cb = document.querySelector(`input[name="categories[]"][value="${catId}"]`);
        if (cb && !cb.checked) {
            cb.checked = true;
            onCategoryToggle(catId);
            return;
        }

        const letter = String.fromCharCode(65 + count);
        const labelText = isMulti ? `Regu ${letter}:` : 'Regu A:';
        const defaultVal = isMulti ? `Regu ${letter}` : 'Regu A';

        const row = document.createElement('div');
        row.className = 'team-input-row bg-white border border-slate-200/90 rounded-xl p-3 shadow-2xs space-y-2.5 transition';
        row.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-600 w-24 shrink-0 row-index-label">${labelText}</span>
                <input type="text" name="team_labels[${catId}][${count}]" value="${defaultVal}" placeholder="Label Regu (cth: Regu ${letter})" class="team-label-input flex-grow text-xs bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl focus:bg-white focus:outline-none focus:border-red-500 shadow-2xs font-semibold text-slate-800" oninput="calculateFee()">
                ${isMulti ? `
                <button type="button" onclick="removeTeamRow(this, ${catId})" class="btn-remove-team text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition" title="Hapus Regu Ini">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>` : ''}
            </div>

            <!-- Upload File Excel Peserta Regu Ini -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-100 text-xs">
                <div class="flex items-center flex-wrap gap-2">
                    <input type="file" name="team_files[${catId}][${count}]" accept=".xlsx,.xls,.csv" class="hidden team-file-input" onchange="handleTeamExcelUpload(this, ${catId})">
                    <input type="hidden" name="team_members[${catId}][${count}]" class="team-members-json" value="">
                    
                    <button type="button" onclick="triggerFileInput(this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 hover:border-emerald-400 active:scale-95 transition shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-file-excel text-emerald-600 text-xs"></i>
                        <span class="btn-upload-text">Upload Excel Peserta</span>
                    </button>

                    <a href="{{ route('lomba.template-peserta') }}" download class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 hover:text-emerald-700 hover:underline px-1.5 py-1 rounded transition" title="Unduh format template Excel untuk regu ini">
                        <i class="fa-solid fa-download text-emerald-600 text-[10px]"></i>
                        <span>Template Excel</span>
                    </a>
                </div>

                <div class="team-file-status text-[11px] text-slate-400 flex items-center gap-1.5 truncate max-w-full sm:max-w-xs">
                    <span class="status-text italic text-slate-400">(Opsional: unggah daftar peserta)</span>
                </div>
            </div>

            <!-- Live Preview Box -->
            <div class="team-members-preview-box hidden bg-emerald-50/70 border border-emerald-200 rounded-lg p-2.5 text-xs text-emerald-950 space-y-1.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-bold flex items-center gap-1.5 text-[11px] text-emerald-900">
                        <i class="fa-solid fa-users text-emerald-600"></i>
                        <span class="preview-count-label">0 Peserta Terdaftar di Excel</span>
                    </span>
                    <button type="button" onclick="togglePreviewList(this)" class="btn-toggle-preview text-[10px] font-bold text-emerald-700 hover:text-emerald-900 underline cursor-pointer">
                        Sembunyikan
                    </button>
                </div>
                <div class="preview-members-list flex flex-wrap gap-1 max-h-32 overflow-y-auto pt-1"></div>
            </div>
        `;
        container.appendChild(row);
        updateRemoveButtons(catId);
        calculateFee();
    }

    function removeTeamRow(btn, catId) {
        const row = btn.closest('.team-input-row');
        row.remove();
        updateRemoveButtons(catId);
        calculateFee();
    }

    function updateRemoveButtons(catId) {
        const card = document.getElementById('cat-card-' + catId);
        const isMulti = card ? card.dataset.multi === 'true' : true;
        const maxTeams = card ? parseInt(card.dataset.maxTeams || (isMulti ? '3' : '1')) : 3;
        const container = document.getElementById('teams-container-' + catId);
        if (!container) return;
        const rows = container.querySelectorAll('.team-input-row');
        rows.forEach((r, idx) => {
            const letter = String.fromCharCode(65 + idx);
            const removeBtn = r.querySelector('.btn-remove-team');
            if (removeBtn) {
                if (rows.length > 1) {
                    removeBtn.classList.remove('hidden');
                } else {
                    removeBtn.classList.add('hidden');
                }
            }
            if (isMulti) {
                const label = r.querySelector('.row-index-label');
                if (label) label.textContent = `Regu ${letter}:`;
            }
            const labelInput = r.querySelector('.team-label-input');
            if (labelInput) {
                labelInput.name = `team_labels[${catId}][${idx}]`;
                labelInput.placeholder = `Label Regu (cth: Regu ${letter})`;
            }
            const fileInput = r.querySelector('.team-file-input');
            if (fileInput) fileInput.name = `team_files[${catId}][${idx}]`;
            const membersInput = r.querySelector('.team-members-json');
            if (membersInput) membersInput.name = `team_members[${catId}][${idx}]`;
        });

        // Toggle btn-add-team state
        const btnAdd = document.getElementById('btn-add-team-' + catId);
        if (btnAdd) {
            if (rows.length >= maxTeams) {
                btnAdd.disabled = true;
                btnAdd.classList.add('opacity-40', 'cursor-not-allowed');
                btnAdd.title = `Maksimal ${maxTeams} regu telah tercapai`;
            } else {
                btnAdd.disabled = false;
                btnAdd.classList.remove('opacity-40', 'cursor-not-allowed');
                btnAdd.title = '+ Tambah Regu';
            }
        }
    }

    function calculateFee() {
        let totalTeams = 0;
        let totalFee = 0;
        const breakdownItems = [];

        document.querySelectorAll('.cat-checkbox:checked').forEach(cb => {
            const catId = cb.value;
            const rawFee = cb.getAttribute('data-fee');
            const fee = (rawFee !== null && rawFee !== '' && !isNaN(rawFee)) ? parseFloat(rawFee) : feePerCat;
            const container = document.getElementById('teams-container-' + catId);
            let teamCount = 1;
            if (container) {
                const rows = container.querySelectorAll('.team-input-row');
                teamCount = Math.max(1, rows.length);
            }
            totalTeams += teamCount;
            const subtotal = teamCount * fee;
            totalFee += subtotal;

            const card = document.getElementById('cat-card-' + catId);
            const catName = card ? (card.querySelector('.font-black.text-slate-900')?.innerText || 'Cabang Lomba') : 'Cabang Lomba';
            breakdownItems.push({
                name: catName,
                count: teamCount,
                fee: fee,
                subtotal: subtotal
            });
        });

        const countEl = document.getElementById('selected-count');
        const totalEl = document.getElementById('total-fee-display');
        const breakdownEl = document.getElementById('fee-breakdown-list');

        if (countEl) countEl.innerText = totalTeams;
        if (totalEl) totalEl.innerText = 'Rp ' + totalFee.toLocaleString('id-ID');

        if (breakdownEl) {
            if (breakdownItems.length > 0) {
                breakdownEl.classList.remove('hidden');
                let html = '<div class="font-bold text-[10px] uppercase text-red-800 tracking-wider mb-1">Rincian Tagihan per Cabang:</div>';
                breakdownItems.forEach(item => {
                    const feeText = item.fee > 0 ? ('Rp ' + item.subtotal.toLocaleString('id-ID')) : '<span class="text-emerald-700 font-bold">Gratis (Rp 0)</span>';
                    html += `<div class="flex justify-between items-center py-0.5 border-b border-red-100 last:border-none">
                        <span class="truncate pr-2">${escapeHtml(item.name)} (${item.count} Regu)</span>
                        <span class="font-mono font-bold shrink-0">${feeText}</span>
                    </div>`;
                });
                breakdownEl.innerHTML = html;
            } else {
                breakdownEl.classList.add('hidden');
                breakdownEl.innerHTML = '';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi tampilan nomor rekening sesuai tingkatan yang aktif
        const activeLevelRadio = document.querySelector('input[name="level"]:checked');
        const initialLevel = activeLevelRadio ? activeLevelRadio.value : '{{ $currentLevel ?? "Madya" }}';
        updateBankAccountDisplay(initialLevel);

        // Bind radio change listeners agar selalu sinkron jika berpindah tingkatan
        document.querySelectorAll('input[name="level"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    switchLevel(this.value);
                }
            });
        });

        document.querySelectorAll('.cat-checkbox:checked').forEach(cb => {
            updateRemoveButtons(cb.value);
        });
        calculateFee();

        // Live Countdown Timer for registration page
        (function initRegisterCountdown() {
            const timerEl = document.getElementById('register-countdown-timer');
            if (!timerEl) return;
            const targetStr = timerEl.getAttribute('data-target');
            if (!targetStr) return;

            const targetTime = new Date(targetStr).getTime();
            const daysEl = document.getElementById('reg-days');
            const hoursEl = document.getElementById('reg-hours');
            const minutesEl = document.getElementById('reg-minutes');
            const secondsEl = document.getElementById('reg-seconds');

            function updateTimer() {
                const now = new Date().getTime();
                const diff = targetTime - now;

                if (diff <= 0) {
                    if (daysEl) daysEl.textContent = '00';
                    if (hoursEl) hoursEl.textContent = '00';
                    if (minutesEl) minutesEl.textContent = '00';
                    if (secondsEl) secondsEl.textContent = '00';
                    // Auto reload to fetch updated wave tariff
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                    return;
                }

                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);

                if (daysEl) daysEl.textContent = String(d).padStart(2, '0');
                if (hoursEl) hoursEl.textContent = String(h).padStart(2, '0');
                if (minutesEl) minutesEl.textContent = String(m).padStart(2, '0');
                if (secondsEl) secondsEl.textContent = String(s).padStart(2, '0');
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        })();
    });
</script>
@endpush
