@extends('layouts.admin')

@section('title', 'Tambah Pengurus / Jabatan')
@section('page_title', 'Tambah Pengurus / Jabatan Baru')

@section('top_actions')
<a href="{{ route('admin.organization.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Kembali ke Bagan</span>
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-extrabold text-slate-800 text-sm">Formulir Pengurus / Jabatan Baru</h2>
            <span class="text-xs text-slate-500">* Wajib diisi</span>
        </div>

        <form action="{{ route('admin.organization.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Tingkat Hirarki Level -->
            <div>
                <label for="level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Tingkat Hirarki Bagan <span class="text-red-500">*</span>
                </label>
                <select name="level" id="level" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition bg-white">
                    <option value="1" {{ old('level') == 1 ? 'selected' : '' }}>Tingkat 1 &mdash; Pembina PMR</option>
                    <option value="2" {{ old('level') == 2 ? 'selected' : '' }}>Tingkat 2 &mdash; Ketua Umum / Pimpinan</option>
                    <option value="3" {{ old('level') == 3 ? 'selected' : '' }}>Tingkat 3 &mdash; Pengurus Harian (BPH: Sekretaris / Bendahara)</option>
                    <option value="4" {{ old('level', 4) == 4 ? 'selected' : '' }}>Tingkat 4 &mdash; Koordinator Seksi / Divisi (Sie Kesehatan, Diklat, Humas, dll)</option>
                    <option value="5" {{ old('level') == 5 ? 'selected' : '' }}>Tingkat 5 &mdash; Anggota / Seksi Tambahan</option>
                </select>
                <p class="text-[11px] text-slate-500 mt-1">Menentukan posisi kartu dalam visual bagan organisasi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jabatan -->
                <div>
                    <label for="position" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Jabatan / Posisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="position" id="position" value="{{ old('position') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        placeholder="Contoh: Ketua Umum 2026/2027">
                </div>

                <!-- Nama Lengkap Pejabat -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Pejabat / Siswa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-pmr-primary focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        placeholder="Contoh: Muhammad Rizky Pratama">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subtitle / Keterangan Kelas -->
                <div>
                    <label for="subtitle" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Keterangan / Kelas / Peran
                    </label>
                    <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        placeholder="Contoh: Kelas XI-MIPA 1 / Piket UKS">
                </div>

                <!-- Icon FontAwesome -->
                <div>
                    <label for="icon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Icon FontAwesome (Opsional)
                    </label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', 'fa-solid fa-notes-medical') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-medium focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        placeholder="Contoh: fa-solid fa-notes-medical">
                    <p class="text-[11px] text-slate-400 mt-1">Bisa gunakan icon seperti fa-solid fa-notes-medical, fa-solid fa-bullhorn, dll.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <!-- Urutan Tampil -->
                <div>
                    <label for="order_position" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Urutan Tampil (Angka)
                    </label>
                    <input type="number" name="order_position" id="order_position" value="{{ old('order_position', 1) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition"
                        min="0">
                </div>

                <!-- Status Aktif -->
                <div class="pt-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-pmr-primary focus:ring-pmr-primary border-slate-300">
                        <div>
                            <span class="text-sm font-bold text-slate-800">Tampilkan di Bagan (Aktif)</span>
                            <span class="block text-xs text-slate-500">Centang agar langsung muncul di web publik</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.organization.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Pengurus</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
