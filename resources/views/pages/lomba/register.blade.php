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
                    <p class="text-xs text-slate-500 mb-3">Centang cabang lomba yang ingin diikuti. Setiap sekolah maksimal mengirimkan <strong>2 regu putra dan 2 regu putri</strong> untuk masing-masing cabang lomba umum, khusus cabang <strong>Olimpiade Kepalangmerahan maksimum 3 regu</strong>, dan untuk cabang <strong>Mewarnai tidak dibatasi</strong>.</p>

                    <div class="bg-amber-50 border border-amber-200 text-amber-950 p-4 rounded-2xl text-xs flex items-start gap-3 mb-4 shadow-2xs">
                        <i class="fa-solid fa-shield-halved text-amber-600 text-base mt-0.5 shrink-0"></i>
                        <div class="leading-relaxed space-y-1">
                            <strong class="text-amber-900 text-xs">Ketentuan Kuota Regu per Sekolah:</strong>
                            <ul class="list-disc pl-4 space-y-1 text-amber-900/90">
                                <li>Setiap sekolah maksimal mengirimkan 2 regu putra dan 2 regu putri untuk masing-masing cabang lomba umum.</li>
                                <li>Khusus cabang Olimpiade Kepalangmerahan masing-masing sekolah dibatasi maksimum 3 regu dan untuk cabang Mewarnai tidak dibatasi.</li>
                                <li>Pada cabang lomba dengan kuota 2 regu, kuota terkunci otomatis guna mencegah pendaftaran dengan regu lebih dari ketentuan.</li>
                            </ul>
                        </div>
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
                                                </div>
                                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                                                    <span>Tingkat: <span class="font-bold text-slate-700">PMR {{ $cat->level }}</span></span>
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
                                    <div id="teams-container-{{ $cat->id }}" class="teams-container space-y-2 pt-2.5 border-t border-slate-200/60 {{ $isChecked ? '' : 'hidden' }}">
                                        @php
                                            $defaultLabel = $isPa ? 'Regu Putra' : ($isPi ? 'Regu Putri' : 'Regu A');
                                            $oldLabels = old('team_labels.'.$cat->id, [$defaultLabel]);
                                            if (!is_array($oldLabels)) $oldLabels = [$oldLabels];
                                            if (empty($oldLabels)) $oldLabels = [$defaultLabel];
                                            if (!$isMulti) $oldLabels = array_slice($oldLabels, 0, 1);
                                        @endphp
                                        @foreach($oldLabels as $idx => $lbl)
                                            <div class="team-input-row flex items-center gap-2">
                                                <span class="text-[11px] font-bold text-slate-500 w-24 shrink-0 row-index-label">{{ $isMulti ? 'Regu ' . chr(65 + $idx) . ':' : ($isPa ? 'Regu Putra:' : ($isPi ? 'Regu Putri:' : 'Nama Regu:')) }}</span>
                                                <input type="text" name="team_labels[{{ $cat->id }}][]" value="{{ $lbl }}" placeholder="Label Regu" class="team-label-input flex-grow text-xs bg-white border border-slate-200 px-3 py-2 rounded-xl focus:outline-none focus:border-red-500 shadow-2xs font-semibold text-slate-800" oninput="calculateFee()" {{ !$isMulti ? '' : '' }}>
                                                @if($isMulti)
                                                    <button type="button" onclick="removeTeamRow(this, {{ $cat->id }})" class="btn-remove-team {{ count($oldLabels) > 1 ? '' : 'hidden' }} text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition" title="Hapus Regu Ini">
                                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
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
                            <div class="bg-red-50 border border-red-100 rounded-xl p-4 flex justify-between items-center">
                                <div>
                                    <div class="text-xs text-red-800 font-medium">Total Regu Didaftarkan:</div>
                                    <div class="text-sm font-bold text-red-900"><span id="selected-count">0</span> Regu Lomba</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-red-800 font-medium">Total Tagihan:</div>
                                    <div class="text-xl font-black text-red-700" id="total-fee-display">Rp 0</div>
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
    const feePerCat = {{ $event->registration_fee }};
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
        const labelText = isMulti ? `Regu ${letter}:` : 'Nama Regu:';
        const defaultVal = isMulti ? `Regu ${letter}` : 'Regu Utama';

        const row = document.createElement('div');
        row.className = 'team-input-row flex items-center gap-2';
        row.innerHTML = `
            <span class="text-[11px] font-bold text-slate-500 w-24 shrink-0 row-index-label">${labelText}</span>
            <input type="text" name="team_labels[${catId}][]" value="${defaultVal}" placeholder="Label Regu" class="team-label-input flex-grow text-xs bg-white border border-slate-200 px-3 py-2 rounded-xl focus:outline-none focus:border-red-500 shadow-2xs font-semibold text-slate-800" oninput="calculateFee()">
            ${isMulti ? `
            <button type="button" onclick="removeTeamRow(this, ${catId})" class="btn-remove-team text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition" title="Hapus Regu Ini">
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>` : ''}
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
                if (label) label.textContent = `Regu ${String.fromCharCode(65 + idx)}:`;
            }
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

        document.querySelectorAll('.cat-checkbox:checked').forEach(cb => {
            const catId = cb.value;
            const fee = parseFloat(cb.getAttribute('data-fee')) || feePerCat;
            const container = document.getElementById('teams-container-' + catId);
            let teamCount = 1;
            if (container) {
                const rows = container.querySelectorAll('.team-input-row');
                teamCount = Math.max(1, rows.length);
            }
            totalTeams += teamCount;
            totalFee += (teamCount * fee);
        });

        const countEl = document.getElementById('selected-count');
        const totalEl = document.getElementById('total-fee-display');
        if (countEl) countEl.innerText = totalTeams;
        if (totalEl) totalEl.innerText = 'Rp ' + totalFee.toLocaleString('id-ID');
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
