@extends('layouts.admin')

@section('title', 'Input Data Pembina')
@section('page_title', 'Form Input Data Pembina PMR')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Header Box -->
    <div class="bg-slate-50 border-b border-slate-200 p-6 sm:px-10 flex items-center justify-between">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-stone-900 text-red-300 mb-2">
                <i class="fa-solid fa-shield-halved text-[11px]"></i> Data Khusus Pembina
            </div>
            <h2 class="text-xl font-extrabold text-slate-800">BIODATA PEMBINA PMR WIRA</h2>
            <p class="text-sm text-slate-500 mt-1">Lengkapi data profil Dewan Pembina / Guru Pembina PMR Wira SMAN 1 Ciawi.</p>
        </div>
        <div class="hidden sm:flex items-center gap-3 opacity-80">
            <img src="{{ asset('images/logo.png') }}" alt="PMR" class="h-10">
            <img src="{{ asset('images/wira.png') }}" alt="WIRA" class="h-10">
        </div>
    </div>

    <form action="{{ route('admin.advisors.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-10">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left Side (Photo Upload) -->
            <div class="lg:col-span-3">
                <div class="text-sm font-bold text-slate-700 mb-3">Upload Foto Pembina <span class="text-xs font-normal text-slate-400">(Opsional)</span></div>
                <div class="relative w-full aspect-[3/4] bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl overflow-hidden group hover:border-pmr-primary transition">
                    <img id="photo-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                    <div id="photo-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 group-hover:text-pmr-primary transition">
                        <i class="fa-solid fa-camera text-4xl mb-2"></i>
                        <span class="text-xs font-semibold">Pilih Foto</span>
                    </div>
                    <input type="file" name="photo" id="photo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div class="absolute bottom-3 right-3 z-20 w-8 h-8 bg-pmr-primary text-white rounded-full flex items-center justify-center shadow-lg pointer-events-none hidden" id="upload-icon">
                        <i class="fa-solid fa-upload text-sm"></i>
                    </div>
                </div>
                <div class="text-[11px] text-slate-500 mt-3 text-center">
                    Format: JPG, PNG, WEBP. Rasio 3:4 (Pas Foto Formal Pembina). Maks: 5MB.
                </div>
            </div>

            <!-- Right Side (Fields) -->
            <div class="lg:col-span-9 space-y-6">
                <!-- Data Utama -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-solid fa-id-card text-slate-400"></i> NIP (Nomor Induk Pegawai)
                        </label>
                        <input type="text" name="nip" value="{{ old('nip') }}" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 font-semibold text-slate-800" placeholder="Contoh: 198001012005011001">
                        <span class="text-[11px] text-slate-400 mt-1 block">NIP Pembina resmi (dapat digunakan untuk login Backoffice).</span>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Gelar Akademik (Opsional)</label>
                        <input type="text" name="title_degree" value="{{ old('title_degree') }}" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 font-semibold text-slate-800" placeholder="Contoh: S.Pd, M.Pd">
                        <span class="text-[11px] text-slate-400 mt-1 block">Gelar di belakang nama lengkap.</span>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap Pembina <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 font-bold text-slate-900" placeholder="Contoh: Drs. H. Mulyadi">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Jabatan Pembina <span class="text-rose-500">*</span></label>
                        <input list="position-options" name="position" value="{{ old('position', 'Pembina PMR') }}" required class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 font-semibold" placeholder="Pilih dari daftar atau ketik jabatan baru...">
                        <datalist id="position-options">
                            @foreach($positions as $pos)
                                <option value="{{ $pos }}"></option>
                            @endforeach
                            <option value="Pembina PMR"></option>
                            <option value="Guru Pembina"></option>
                            <option value="Pembina Teknis"></option>
                            <option value="Koordinator Pembina Ekstrakurikuler"></option>
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                        <i class="fa-regular fa-map text-slate-400"></i> Alamat Lengkap
                    </label>
                    <textarea name="address" rows="2" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: Jl. Veteran III No. 45, Ciawi, Bogor">{{ old('address') }}</textarea>
                </div>

                <!-- Atribut -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-regular fa-calendar text-slate-400"></i> Tempat & Tanggal Lahir
                        </label>
                        <div class="flex gap-2">
                            <input type="text" name="birth_place" value="{{ old('birth_place') }}" class="w-1/2 bg-white border border-slate-200 px-3 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm" placeholder="Tempat">
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-1/2 bg-white border border-slate-200 px-3 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Kelamin</label>
                        <select name="gender" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 appearance-none">
                            <option value="">Pilih...</option>
                            <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Kontak -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-regular fa-envelope text-slate-400"></i> Email Pembina
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: pembina@sman1ciawi.sch.id">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-solid fa-phone text-slate-400"></i> Nomor Telepon / WA
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: 0812xxxx">
                    </div>
                </div>

                <!-- Motto & Pesan Pembina -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                        <i class="fa-solid fa-quote-left text-slate-400"></i> Motto / Pesan Bimbingan
                    </label>
                    <textarea name="motto" rows="2" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: Mendidik relawan muda yang berkarakter tanggap dan berjiwa kemanusiaan.">{{ old('motto') }}</textarea>
                </div>

                <!-- Visi & Misi Pembinaan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-regular fa-eye text-slate-400"></i> Visi Pembinaan
                        </label>
                        <textarea name="vision" rows="2" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Tuliskan visi pembinaan...">{{ old('vision') }}</textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-solid fa-bullseye text-slate-400"></i> Misi Pembinaan
                        </label>
                        <textarea name="mission" rows="2" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Tuliskan misi pembinaan...">{{ old('mission') }}</textarea>
                    </div>
                </div>

                <!-- Pengaturan Urutan & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Urutan Tampil di Bagan (Angka)
                        </label>
                        <input type="number" name="order_position" value="{{ old('order_position', 1) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:ring-2 focus:ring-pmr-primary focus:border-pmr-primary transition" min="1">
                    </div>

                    <div class="pt-5">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 rounded text-pmr-primary focus:ring-pmr-primary border-slate-300">
                            <div>
                                <span class="text-sm font-bold text-slate-800">Status Pembina Aktif</span>
                                <span class="block text-xs text-slate-500">Tampilkan profil pembina di bagan kepengurusan publik</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.advisors.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 bg-pmr-primary text-white font-bold rounded-xl hover:bg-pmr-dark transition shadow-md shadow-red-950/20 flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Data Pembina
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photo-preview');
    const photoPlaceholder = document.getElementById('photo-placeholder');
    const uploadIcon = document.getElementById('upload-icon');

    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    photoPreview.src = event.target.result;
                    photoPreview.classList.remove('hidden');
                    photoPlaceholder.classList.add('hidden');
                    uploadIcon.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                photoPreview.src = '#';
                photoPreview.classList.add('hidden');
                photoPlaceholder.classList.remove('hidden');
                uploadIcon.classList.add('hidden');
            }
        });
    }
</script>
@endpush
