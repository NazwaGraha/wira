@extends('layouts.admin')

@section('title', 'Tulis Siaran Email Informasi Lomba')
@section('page_title', 'Form Siaran Email & Informasi Lomba')

@section('top_actions')
    <a href="{{ route('admin.competition-broadcast.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
        &larr; Kembali ke Riwayat Siaran
    </a>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Kolom Kiri: Form Komposisi Pesan -->
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('admin.competition-broadcast.send') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 space-y-6">
                @csrf

                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-pen-nib text-red-600"></i> Komposisi Pesan Email Siaran
                    </h3>
                    <p class="text-xs text-slate-500">Pesan ini akan dikirimkan otomatis ke seluruh kontak email sekolah / pembina sesuai target yang Anda tentukan.</p>
                </div>

                <hr class="border-slate-100">

                <!-- 1. Kriteria Target Penerima -->
                <div class="space-y-4 bg-slate-50 p-4 sm:p-5 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-filter text-indigo-500"></i> Kriteria Target Penerima
                        </span>
                        <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100">
                            {{ count($recipients) }} Kontak Terpilih
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Edisi / Tahun Lomba</label>
                            <select name="target_event" onchange="updateFilter(this)" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-red-500">
                                <option value="all" {{ $targetEvent == 'all' ? 'selected' : '' }}>Semua Edisi (Lintas Tahun)</option>
                                @foreach($events as $ev)
                                    <option value="{{ $ev->id }}" {{ $targetEvent == (string)$ev->id ? 'selected' : '' }}>
                                        {{ $ev->title }} {{ $ev->is_active ? '⭐' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Jenjang Tingkat PMR</label>
                            <select name="target_level" onchange="updateFilter(this)" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-red-500">
                                <option value="all" {{ $targetLevel == 'all' ? 'selected' : '' }}>Semua Tingkat</option>
                                <option value="Mula" {{ $targetLevel == 'Mula' ? 'selected' : '' }}>Mula (SD)</option>
                                <option value="Madya" {{ $targetLevel == 'Madya' ? 'selected' : '' }}>Madya (SMP)</option>
                                <option value="Wira" {{ $targetLevel == 'Wira' ? 'selected' : '' }}>Wira (SMA)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Verifikasi</label>
                            <select name="target_status" onchange="updateFilter(this)" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-red-500">
                                <option value="all" {{ $targetStatus == 'all' ? 'selected' : '' }}>Semua Pendaftar</option>
                                <option value="verified" {{ $targetStatus == 'verified' ? 'selected' : '' }}>Hanya Lunas (Terverifikasi)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 2. Konten Email -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Subjek Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="Contoh: [PENGUMUMAN] Undangan Pendaftaran Lomba SUA BHAKTI BERKARYA IV 2026" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500 font-semibold text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Headline / Judul Banner di Dalam Email
                        </label>
                        <input type="text" name="headline" value="{{ old('headline', 'SUA BHAKTI BERKARYA') }}" placeholder="Contoh: SUA BHAKTI BERKARYA IV - 2026" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500 font-medium text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Isi Pesan / Informasi Kegiatan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="content" rows="8" required placeholder="Tuliskan isi pengumuman, undangan kegiatan, jadwal technical meeting, atau link unduh juklak juknis..." class="w-full bg-slate-50 border border-slate-200 p-4 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500 leading-relaxed text-slate-800">{{ old('content') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Teks Tombol Aksi (Opsional)
                            </label>
                            <input type="text" name="button_text" value="{{ old('button_text') }}" placeholder="Contoh: Buka Petunjuk Teknis Lomba" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500 font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Link / Tautan Tombol Aksi
                            </label>
                            <input type="url" name="button_url" value="{{ old('button_url') }}" placeholder="Contoh: https://wira.nazwagraha.com/lomba" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan Tambahan Panitia / Narahubung WhatsApp (Opsional)
                        </label>
                        <textarea name="notes" rows="2" placeholder="Contoh: Narahubung Panitia: 0812-xxxx-xxxx (Kak Reza)" class="w-full bg-slate-50 border border-slate-200 px-4 py-2 rounded-xl text-sm focus:bg-white focus:outline-none focus:border-red-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- 3. Mode Pengujian (Test Send) -->
                <div x-data="{ isTest: false }" class="bg-amber-50/60 p-4 rounded-xl border border-amber-200 space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_test_mode" value="1" x-model="isTest" class="w-4 h-4 text-red-600 rounded border-amber-300 focus:ring-red-500">
                        <span class="text-xs font-black text-amber-950">Mode Uji Coba (Kirim hanya ke email admin / penguji terlebih dahulu)</span>
                    </label>

                    <div x-show="isTest" x-collapse>
                        <label class="block text-[11px] font-bold text-amber-900 mb-1">Kirim Salinan Uji Coba Ke Alamat Email Ini:</label>
                        <input type="email" name="test_email" value="{{ auth()->user()->email ?? '' }}" placeholder="email.anda@gmail.com" class="w-full bg-white border border-amber-300 px-3 py-2 rounded-xl text-xs font-semibold focus:outline-none focus:border-red-500">
                        <p class="text-[10px] text-amber-700 mt-1">Gunakan opsi ini untuk melihat tampilan email di inbox Anda sebelum menyebarkannya ke seluruh sekolah.</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('admin.competition-broadcast.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                        Batal
                    </a>
                    <button type="submit" onclick="return confirm('Kirimkan siaran email ini sekarang?');" class="bg-red-600 hover:bg-red-700 text-white font-black text-xs px-6 py-3 rounded-xl transition shadow-md shadow-red-600/30 flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Siaran Email Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Live Audience Preview & Daftar Kontak -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-address-book text-emerald-600"></i> Kontak Penerima Terpilih
                        </h4>
                        <div class="text-[11px] text-slate-500 mt-0.5">Sesuai filter kriteria di sebelah kiri</div>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-black rounded-full">
                        {{ count($recipients) }} Email Unik
                    </span>
                </div>

                <div class="max-h-[550px] overflow-y-auto space-y-2 pr-1 divide-y divide-slate-100">
                    @forelse($recipients as $item)
                        <div class="pt-2 first:pt-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 text-xs truncate uppercase">{{ $item['school_name'] }}</div>
                                    <div class="text-[11px] text-slate-500 truncate mt-0.5 flex items-center gap-1">
                                        <i class="fa-regular fa-envelope text-[10px] text-slate-400"></i>
                                        <span class="font-mono text-slate-700 font-semibold">{{ $item['email'] }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $item['advisor_name'] }} &bull; {{ $item['advisor_phone'] }}
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black shrink-0 {{ $item['level'] == 'Mula' ? 'bg-blue-50 text-blue-700' : ($item['level'] == 'Madya' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $item['level'] }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-user-slash text-2xl mb-2 text-slate-300"></i>
                            <div>Tidak ada kontak yang cocok dengan filter yang dipilih.</div>
                        </div>
                    @endforelse
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-500 leading-relaxed">
                    <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i>
                    <strong>Anti Duplikasi:</strong> Setiap alamat email hanya akan menerima 1 salinan email siaran per pengiriman, meskipun mendaftarkan beberapa regu sekaligus.
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    function updateFilter(el) {
        const form = el.form;
        const targetEvent = form.querySelector('[name="target_event"]').value;
        const targetLevel = form.querySelector('[name="target_level"]').value;
        const targetStatus = form.querySelector('[name="target_status"]').value;

        const url = new URL(window.location.href);
        url.searchParams.set('target_event', targetEvent);
        url.searchParams.set('target_level', targetLevel);
        url.searchParams.set('target_status', targetStatus);

        window.location.href = url.toString();
    }
</script>
@endsection
