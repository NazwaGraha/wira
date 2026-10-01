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
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email Pembina</label>
                            <input type="email" name="advisor_email" value="{{ old('advisor_email') }}" placeholder="email@sekolah.sch.id" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500">
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
                    <p class="text-xs text-slate-500 mb-3">Centang cabang lomba yang ingin diikuti. Anda dapat mendaftarkan <strong>lebih dari 1 regu</strong> (Regu A, Regu B, dst.) untuk masing-masing cabang lomba baik putra maupun putri.</p>

                    <div class="bg-blue-50 border border-blue-200 text-blue-900 p-4 rounded-2xl text-xs flex items-start gap-3 mb-4 shadow-2xs">
                        <i class="fa-solid fa-circle-info text-blue-600 text-base mt-0.5 shrink-0"></i>
                        <div class="leading-relaxed">
                            <strong>Informasi Pendaftaran Multi-Regu:</strong>
                            <ul class="list-disc pl-4 mt-1 space-y-0.5 text-blue-800">
                                <li>Sekolah diperbolehkan mendaftarkan <strong>lebih dari 1 regu</strong> untuk setiap cabang lomba (Putra, Putri, maupun Campuran).</li>
                                <li>Klik tombol <strong class="text-red-700 bg-red-100/80 px-1.5 py-0.5 rounded">+ Tambah Regu</strong> pada cabang lomba yang ingin dikirimkan 2 regu atau lebih.</li>
                                <li>Biaya registrasi dihitung secara otomatis berdasarkan total seluruh regu yang didaftarkan.</li>
                            </ul>
                        </div>
                    </div>

                    @foreach(['Mula', 'Madya', 'Wira'] as $lvl)
                        <div id="category-group-{{ $lvl }}" class="category-level-group space-y-3 {{ $currentLevel == $lvl ? '' : 'hidden' }}">
                            @foreach($categoriesByLevel->get($lvl, []) as $cat)
                                @php
                                    $isPa = $cat->gender_category === 'Putra';
                                    $isPi = $cat->gender_category === 'Putri';
                                    $isChecked = in_array($cat->id, old('categories', []));
                                @endphp
                                <div class="border {{ $isPa ? 'border-blue-200 bg-blue-50/20' : ($isPi ? 'border-rose-200 bg-rose-50/20' : 'border-slate-200 bg-slate-50/40') }} rounded-2xl p-4 hover:border-red-400 hover:shadow-xs transition space-y-3" id="cat-card-{{ $cat->id }}">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <label class="flex items-start sm:items-center gap-3 cursor-pointer flex-grow select-none">
                                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" data-fee="{{ intval($cat->registration_fee ?: ($event->registration_fee ?: 150000)) }}" class="cat-checkbox mt-1 sm:mt-0 w-5 h-5 text-red-600 rounded border-slate-300 focus:ring-red-500" {{ $isChecked ? 'checked' : '' }} onchange="onCategoryToggle({{ $cat->id }})">
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
                                                </div>
                                                <div class="text-xs text-slate-500 mt-1">
                                                    Tingkat: <span class="font-bold text-slate-700">PMR {{ $cat->level }}</span> &bull; Biaya: <strong class="text-red-600 font-bold font-mono">Rp {{ number_format($cat->registration_fee ?: ($event->registration_fee ?: 150000), 0, ',', '.') }}</strong> <span class="text-[11px] text-slate-400">/ Regu</span>
                                                </div>
                                            </div>
                                        </label>

                                        <button type="button" onclick="addTeamRow({{ $cat->id }})" id="btn-add-team-{{ $cat->id }}" class="btn-add-team {{ $isChecked ? '' : 'hidden' }} shrink-0 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                                            <i class="fa-solid fa-plus-circle"></i> + Tambah Regu
                                        </button>
                                    </div>

                                    <!-- Dynamic Team Inputs Container -->
                                    <div id="teams-container-{{ $cat->id }}" class="teams-container space-y-2 pt-2.5 border-t border-slate-200/60 {{ $isChecked ? '' : 'hidden' }}">
                                        @php
                                            $oldLabels = old('team_labels.'.$cat->id, ['Regu A']);
                                            if (!is_array($oldLabels)) $oldLabels = [$oldLabels];
                                            if (empty($oldLabels)) $oldLabels = ['Regu A'];
                                        @endphp
                                        @foreach($oldLabels as $idx => $lbl)
                                            <div class="team-input-row flex items-center gap-2">
                                                <span class="text-[11px] font-bold text-slate-500 w-16 shrink-0 row-index-label">Regu {{ chr(65 + $idx) }}:</span>
                                                <input type="text" name="team_labels[{{ $cat->id }}][]" value="{{ $lbl }}" placeholder="Label Regu, misal: Regu {{ chr(65 + $idx) }}" class="team-label-input flex-grow text-xs bg-white border border-slate-200 px-3 py-2 rounded-xl focus:outline-none focus:border-red-500 shadow-2xs font-semibold text-slate-800" oninput="calculateFee()">
                                                <button type="button" onclick="removeTeamRow(this, {{ $cat->id }})" class="btn-remove-team {{ count($oldLabels) > 1 ? '' : 'hidden' }} text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition" title="Hapus Regu Ini">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
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
                        <!-- Rekening Box -->
                        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-5 rounded-2xl shadow-sm">
                            <div class="text-[11px] text-red-400 font-bold uppercase tracking-wider mb-2">Tujuan Transfer Panitia</div>
                            <div class="text-lg font-black tracking-wide">{{ $event->bank_name ?? 'BANK BCA' }}</div>
                            <div class="text-2xl font-mono font-bold tracking-widest text-amber-300 my-1">{{ $event->bank_account_number ?? '1234567890' }}</div>
                            <div class="text-xs text-slate-300 font-medium">a.n {{ $event->bank_account_holder ?? 'PMR WIRA SMAN 1 CIAWI' }}</div>
                            
                            <div class="mt-4 pt-4 border-t border-slate-700 flex justify-between items-center text-xs">
                                <span class="text-slate-400">Biaya per Regu:</span>
                                <span class="font-bold text-white">Rp {{ number_format($event->registration_fee, 0, ',', '.') }}</span>
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
            }
        } else {
            container.classList.add('hidden');
            if (btnAdd) btnAdd.classList.add('hidden');
        }
        calculateFee();
    }

    function addTeamRow(catId) {
        const cb = document.querySelector(`input[name="categories[]"][value="${catId}"]`);
        if (cb && !cb.checked) {
            cb.checked = true;
            onCategoryToggle(catId);
            return;
        }

        const container = document.getElementById('teams-container-' + catId);
        const count = container.querySelectorAll('.team-input-row').length;
        const letter = String.fromCharCode(65 + count);

        const row = document.createElement('div');
        row.className = 'team-input-row flex items-center gap-2';
        row.innerHTML = `
            <span class="text-[11px] font-bold text-slate-500 w-16 shrink-0 row-index-label">Regu ${letter}:</span>
            <input type="text" name="team_labels[${catId}][]" value="Regu ${letter}" placeholder="Label Regu, misal: Regu ${letter}" class="team-label-input flex-grow text-xs bg-white border border-slate-200 px-3 py-2 rounded-xl focus:outline-none focus:border-red-500 shadow-2xs font-semibold text-slate-800" oninput="calculateFee()">
            <button type="button" onclick="removeTeamRow(this, ${catId})" class="btn-remove-team text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition" title="Hapus Regu Ini">
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
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
        const container = document.getElementById('teams-container-' + catId);
        const rows = container.querySelectorAll('.team-input-row');
        rows.forEach((r, idx) => {
            const removeBtn = r.querySelector('.btn-remove-team');
            if (rows.length > 1) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
        });
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
        document.querySelectorAll('.cat-checkbox:checked').forEach(cb => {
            updateRemoveButtons(cb.value);
        });
        calculateFee();
    });
</script>
@endpush
