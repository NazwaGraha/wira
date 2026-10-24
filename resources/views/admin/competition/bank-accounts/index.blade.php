@extends('layouts.admin')

@section('title', 'Setup Tujuan Rekening Transfer Lomba')
@section('page_title', 'Setup Rekening Transfer Dana (Mula, Madya, Wira)')

@section('top_actions')
    <a href="{{ route('lomba.register') }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Cek Form Pendaftaran Web
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

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-xl shadow-xs">
            <div class="font-bold text-sm mb-1 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Ada kesalahan input:
            </div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($event)
        <!-- Live Status & Info Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-6 rounded-2xl shadow-sm border border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-red-600 text-white">
                        <i class="fa-solid fa-credit-card mr-1"></i> Multi-Rekening Per Tingkat
                    </span>
                    <span class="text-xs text-slate-300 font-mono bg-white/10 px-2.5 py-1 rounded-lg border border-white/10">
                        Event: {{ $event->title }}
                    </span>
                </div>
                <h2 class="text-lg font-black text-white">Setup Tujuan Rekening Transfer Dana Lomba</h2>
                <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                    Atur rekening bank tujuan transfer pendaftaran secara spesifik per tingkatan: <strong>Tingkat Mula</strong> (SD/MI), <strong>Tingkat Madya</strong> (SMP/MTs), dan <strong>Tingkat Wira</strong> (SMA/SMK/MA). Pada formulir registrasi publik, nomor rekening yang muncul akan otomatis berganti sesuai tingkat yang dipilih kontingen.
                </p>
            </div>

            <!-- Quick Info Badge -->
            <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 w-full md:w-auto shrink-0 space-y-1.5 text-xs text-slate-300">
                <div class="font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-emerald-400"></i> Auto-Fallback Aman
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed max-w-xs">
                    Jika rekening khusus tingkatan dikosongkan, sistem otomatis mengalihkan ke rekening utama lomba.
                </p>
            </div>
        </div>

        <!-- Form Setup Rekening -->
        <form action="{{ route('admin.competition-bank-accounts.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <!-- 3 Kolom Kartu Rekening Per Tingkat -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- 1. TINGKAT MULA (SD / MI) -->
                <div class="bg-white rounded-2xl shadow-sm border-2 border-emerald-500/60 overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Header Mula -->
                        <div class="p-5 bg-gradient-to-r from-emerald-600 to-teal-700 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-base shadow-xs">
                                    <i class="fa-solid fa-child-reaching"></i>
                                </span>
                                <div>
                                    <span class="text-[10px] font-black tracking-wider uppercase text-emerald-200">Rekening A</span>
                                    <h3 class="font-black text-white text-base">Tingkat Mula (SD / MI)</h3>
                                </div>
                            </div>
                            <span class="text-xs font-black bg-white/20 px-2.5 py-1 rounded-lg">SD / MI</span>
                        </div>

                        <!-- Form Fields Mula -->
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Bank <span class="text-emerald-600">*</span>
                                </label>
                                <input type="text" name="bank_mula_name" value="{{ old('bank_mula_name', $event->bank_mula_name ?: 'BANK BRI') }}" placeholder="Contoh: BANK BRI / BSI" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-bold text-slate-800">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nomor Rekening Tujuan <span class="text-emerald-600">*</span>
                                </label>
                                <input type="text" name="bank_mula_number" value="{{ old('bank_mula_number', $event->bank_mula_number ?: '0123-01-001234-53-8') }}" placeholder="Contoh: 0123-01-001234-53-8" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-mono font-bold text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Atas Nama Rekening <span class="text-emerald-600">*</span>
                                </label>
                                <input type="text" name="bank_mula_holder" value="{{ old('bank_mula_holder', $event->bank_mula_holder ?: 'PMR WIRA SMANIC - MULA') }}" placeholder="Contoh: PMR WIRA SMANIC - MULA" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-bold text-slate-800">
                            </div>
                        </div>
                    </div>

                    <!-- Footer Info Mula -->
                    <div class="p-4 bg-emerald-50/60 border-t border-emerald-100 text-[11px] text-emerald-800 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-emerald-600"></i>
                        <span>Otomatis aktif saat kontingen memilih tingkat <strong>Mula (SD)</strong>.</span>
                    </div>
                </div>

                <!-- 2. TINGKAT MADYA (SMP / MTs) -->
                <div class="bg-white rounded-2xl shadow-sm border-2 border-blue-500/60 overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Header Madya -->
                        <div class="p-5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-base shadow-xs">
                                    <i class="fa-solid fa-user-group"></i>
                                </span>
                                <div>
                                    <span class="text-[10px] font-black tracking-wider uppercase text-blue-200">Rekening B</span>
                                    <h3 class="font-black text-white text-base">Tingkat Madya (SMP / MTs)</h3>
                                </div>
                            </div>
                            <span class="text-xs font-black bg-white/20 px-2.5 py-1 rounded-lg">SMP / MTs</span>
                        </div>

                        <!-- Form Fields Madya -->
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Bank <span class="text-blue-600">*</span>
                                </label>
                                <input type="text" name="bank_madya_name" value="{{ old('bank_madya_name', $event->bank_madya_name ?: ($event->bank_name ?: 'BANK BCA')) }}" placeholder="Contoh: BANK BCA" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nomor Rekening Tujuan <span class="text-blue-600">*</span>
                                </label>
                                <input type="text" name="bank_madya_number" value="{{ old('bank_madya_number', $event->bank_madya_number ?: ($event->bank_account_number ?: '521-098-4321')) }}" placeholder="Contoh: 521-098-4321" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono font-bold text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Atas Nama Rekening <span class="text-blue-600">*</span>
                                </label>
                                <input type="text" name="bank_madya_holder" value="{{ old('bank_madya_holder', $event->bank_madya_holder ?: ($event->bank_account_holder ?: 'PMR WIRA SMANIC - MADYA')) }}" placeholder="Contoh: PMR WIRA SMANIC - MADYA" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800">
                            </div>
                        </div>
                    </div>

                    <!-- Footer Info Madya -->
                    <div class="p-4 bg-blue-50/60 border-t border-blue-100 text-[11px] text-blue-800 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-blue-600"></i>
                        <span>Otomatis aktif saat kontingen memilih tingkat <strong>Madya (SMP)</strong>.</span>
                    </div>
                </div>

                <!-- 3. TINGKAT WIRA (SMA / SMK / MA) -->
                <div class="bg-white rounded-2xl shadow-sm border-2 border-amber-500/60 overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Header Wira -->
                        <div class="p-5 bg-gradient-to-r from-amber-500 to-amber-700 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-base shadow-xs">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </span>
                                <div>
                                    <span class="text-[10px] font-black tracking-wider uppercase text-amber-200">Rekening C</span>
                                    <h3 class="font-black text-white text-base">Tingkat Wira (SMA/SMK)</h3>
                                </div>
                            </div>
                            <span class="text-xs font-black bg-white/20 px-2.5 py-1 rounded-lg">SMA / SMK</span>
                        </div>

                        <!-- Form Fields Wira -->
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Bank <span class="text-amber-600">*</span>
                                </label>
                                <input type="text" name="bank_wira_name" value="{{ old('bank_wira_name', $event->bank_wira_name ?: 'BANK MANDIRI') }}" placeholder="Contoh: BANK MANDIRI" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm font-bold text-slate-800">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nomor Rekening Tujuan <span class="text-amber-600">*</span>
                                </label>
                                <input type="text" name="bank_wira_number" value="{{ old('bank_wira_number', $event->bank_wira_number ?: '133-00-9876543-2') }}" placeholder="Contoh: 133-00-9876543-2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm font-mono font-bold text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Atas Nama Rekening <span class="text-amber-600">*</span>
                                </label>
                                <input type="text" name="bank_wira_holder" value="{{ old('bank_wira_holder', $event->bank_wira_holder ?: 'PMR WIRA SMANIC - WIRA') }}" placeholder="Contoh: PMR WIRA SMANIC - WIRA" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm font-bold text-slate-800">
                            </div>
                        </div>
                    </div>

                    <!-- Footer Info Wira -->
                    <div class="p-4 bg-amber-50/60 border-t border-amber-100 text-[11px] text-amber-800 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-amber-600"></i>
                        <span>Otomatis aktif saat kontingen memilih tingkat <strong>Wira (SMA)</strong>.</span>
                    </div>
                </div>

            </div>

            <!-- Rekening Utama (Fallback Cadangan) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-landmark"></i>
                        </span>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">Rekening Utama / Default Lomba (Fallback Cadangan)</h3>
                            <p class="text-xs text-slate-500">Digunakan jika salah satu tingkatan di atas belum memiliki nomor rekening khusus</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase">
                        FALLBACK UTAMA
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Bank Utama
                        </label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $event->bank_name) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-bold text-slate-800" placeholder="Bank BCA">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor Rekening Utama
                        </label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $event->bank_account_number) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-mono font-bold text-slate-900" placeholder="1234567890">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Atas Nama Rekening
                        </label>
                        <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $event->bank_account_holder) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-bold text-slate-800" placeholder="PMR WIRA SMAN 1 CIAWI">
                    </div>
                </div>
            </div>

            <!-- Sticky Submit Footer -->
            <div class="sticky bottom-4 z-20 bg-white/95 backdrop-blur-md p-5 rounded-2xl shadow-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    <span>Perubahan rekening langsung berlaku secara *real-time* di formulir pendaftaran publik.</span>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('admin.competition-event.edit', $event->id) }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs px-6 py-2.5 rounded-xl transition flex items-center gap-2 shadow-lg shadow-red-900/20">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan 3 Rekening
                    </button>
                </div>
            </div>
        </form>

    @else
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm max-w-lg mx-auto space-y-4">
            <div class="w-16 h-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-2xl mx-auto">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900">Belum Ada Edisi Lomba Aktif</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Silakan buat edisi lomba baru terlebih dahulu di menu Pengaturan Event Lomba sebelum mengatur rekening transfer.
            </p>
            <a href="{{ route('admin.competition-event.create') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-sm">
                <i class="fa-solid fa-plus"></i> Buat Edisi Lomba Baru
            </a>
        </div>
    @endif

</div>
@endsection
