@extends('layouts.admin')

@section('title', 'Tambah Edisi SUA BHAKTI BERKARYA Baru')
@section('page_title', 'Tambah Edisi Lomba Baru (SUA BHAKTI BERKARYA)')

@section('top_actions')
    <a href="{{ route('admin.competition-event.index') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2 shadow-xs">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Kembali ke Riwayat</span>
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

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

    <form action="{{ route('admin.competition-event.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. Identitas Edisi Baru -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200 space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">Identitas Edisi Penyelenggaraan Baru</h3>
                    <p class="text-xs text-slate-500">Masukkan nama edisi tahun kegiatan baru yang akan dicatat di database</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Judul Event / Nama Lomba <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 text-sm font-black text-slate-900" placeholder="Contoh: SUA BHAKTI BERKARYA IV TAHUN 2026">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tema / Slogan / Badge Atas
                </label>
                <input type="text" name="theme" value="{{ old('theme') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 text-sm font-semibold text-slate-800" placeholder="Contoh: AJANG PRESTASI RELAWAN MUDA PMR WIRA CIAWI">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Singkat Lomba
                </label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 text-sm text-slate-700 leading-relaxed" placeholder="Ajang kompetisi kepalangmerahan bergengsi tingkat Mula, Madya, dan Wira...">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- 2. Fitur Duplikasi Cabang Lomba Otomatis -->
        @if($activeEvent && $activeEvent->categories()->count() > 0)
            <div class="bg-indigo-50/70 border border-indigo-200 rounded-2xl p-6 space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 text-base">
                        <i class="fa-solid fa-copy"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-indigo-950 text-sm">Duplikasi Otomatis Cabang Lomba</h4>
                        <p class="text-xs text-indigo-800 mt-0.5">
                            Salin otomatis seluruh <strong>{{ $activeEvent->categories()->count() }} cabang lomba</strong> (Mula, Madya, Wira) dari <strong>{{ $activeEvent->title }}</strong> ke edisi baru ini agar Anda tidak perlu menginput ulang satu per satu.
                        </p>
                    </div>
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-indigo-950">
                        <input type="checkbox" name="clone_categories" value="1" checked class="w-4 h-4 text-indigo-600 rounded border-indigo-300 focus:ring-indigo-500">
                        <span>Ya, salin otomatis semua cabang lomba ke edisi baru ini</span>
                    </label>
                    <input type="hidden" name="clone_source_id" value="{{ $activeEvent->id }}">
                </div>
            </div>
        @endif

        <!-- 3. Jadwal & Lokasi -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200 space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-emerald-600"></i>
                <span>Jadwal Pelaksanaan & Lokasi</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Mulai
                    </label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Selesai
                    </label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Lokasi Pelaksanaan
                </label>
                <input type="text" name="location" value="{{ old('location', 'Kampus SMAN 1 Ciawi Bogor') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-semibold" placeholder="Kampus SMAN 1 Ciawi Bogor">
            </div>
        </div>

        <!-- 4. Biaya & Rekening -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200 space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-amber-500"></i>
                <span>Biaya & Rekening Pembayaran</span>
            </h3>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Biaya Registrasi Default (per Regu)
                </label>
                <div class="relative max-w-xs">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" name="registration_fee" value="{{ old('registration_fee', 150000) }}" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-mono font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Bank
                    </label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', 'Bank BCA') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor Rekening
                    </label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', '1234567890') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-mono font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Atas Nama
                    </label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', 'PMR WIRA SMAN 1 CIAWI') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-red-500 text-sm font-bold">
                </div>
            </div>
        </div>

        <!-- 5. Status Event Baru -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200 space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-toggle-on text-emerald-500"></i>
                <span>Status Penyelenggaraan</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl border bg-slate-50 border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-900">Jadikan Sebagai Event Aktif</div>
                        <div class="text-[11px] text-slate-500">Langsung tampil di website utama</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="p-4 rounded-xl border bg-slate-50 border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-900">Buka Pendaftaran Online</div>
                        <div class="text-[11px] text-slate-500">Izinkan pendaftaran via web</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_registration_open" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.competition-event.index') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-black py-3 px-6 rounded-xl shadow-md shadow-red-900/30 transition flex items-center gap-2 text-xs">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Edisi Baru ke Database</span>
            </button>
        </div>

    </form>

</div>
@endsection
