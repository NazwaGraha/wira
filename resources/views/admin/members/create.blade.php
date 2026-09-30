@extends('layouts.admin')

@section('title', 'Input Biodata Anggota')
@section('page_title', 'Form Biodata Anggota PMR')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Header Box -->
    <div class="bg-slate-50 border-b border-slate-200 p-6 sm:px-10 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800">BIODATA ANGGOTA PMR</h2>
            <p class="text-sm text-slate-500 mt-1">Lengkapi data profil anggota PMR Wira di bawah ini.</p>
        </div>
        <div class="hidden sm:flex items-center gap-3 opacity-80">
            <img src="{{ asset('images/logo.png') }}" alt="PMR" class="h-10">
            <img src="{{ asset('images/wira.png') }}" alt="WIRA" class="h-10">
        </div>
    </div>

    <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-10">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left Side (Photo Upload) -->
            <div class="lg:col-span-3">
                <div class="text-sm font-bold text-slate-700 mb-3">Upload Foto Profil <span class="text-rose-500">*</span></div>
                <div class="relative w-full aspect-[3/4] bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl overflow-hidden group hover:border-pmr-primary transition">
                    <img id="photo-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                    <div id="photo-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 group-hover:text-pmr-primary transition">
                        <i class="fa-solid fa-camera text-4xl mb-2"></i>
                        <span class="text-xs font-semibold">Pilih Foto</span>
                    </div>
                    <input type="file" name="photo" id="photo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required>
                    
                    <div class="absolute bottom-3 right-3 z-20 w-8 h-8 bg-pmr-primary text-white rounded-full flex items-center justify-center shadow-lg pointer-events-none hidden" id="upload-icon">
                        <i class="fa-solid fa-upload text-sm"></i>
                    </div>
                </div>
                <div class="text-[11px] text-slate-500 mt-3 text-center">
                    Format: JPG, PNG. Rasio 3:4 (Pas Foto Seragam PMR). Maks: 5MB.
                </div>
            </div>

            <!-- Right Side (Fields) -->
            <div class="lg:col-span-9 space-y-6">
                <!-- Data Utama -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-regular fa-user text-slate-400"></i> NIS (Nomor Induk Siswa)
                        </label>
                        <input type="text" name="nis" value="{{ old('nis') }}" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: 12345678">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Jabatan (Opsional)</label>
                        <input list="position-options" name="position" value="{{ old('position') }}" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Pilih dari daftar atau ketik jabatan baru...">
                        <datalist id="position-options">
                            @foreach($positions as $pos)
                                <option value="{{ $pos }}"></option>
                            @endforeach
                            <option value="Anggota"></option>
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                        <i class="fa-regular fa-map text-slate-400"></i> Alamat Lengkap
                    </label>
                    <textarea name="address" rows="2" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: Jl. Raya Ciawi No. 123, Bogor">{{ old('address') }}</textarea>
                </div>

                <!-- Atribut -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kelas / Tingkat</label>
                        <select name="class_grade" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 appearance-none">
                            <option value="">Pilih...</option>
                            <option value="X" {{ old('class_grade') == 'X' ? 'selected' : '' }}>Kelas X (Sepuluh)</option>
                            <option value="XI" {{ old('class_grade') == 'XI' ? 'selected' : '' }}>Kelas XI (Sebelas)</option>
                            <option value="XII" {{ old('class_grade') == 'XII' ? 'selected' : '' }}>Kelas XII (Dua Belas)</option>
                            <option value="Alumni" {{ old('class_grade') == 'Alumni' ? 'selected' : '' }}>Alumni</option>
                        </select>
                    </div>
                </div>

                <!-- Kontak -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-regular fa-envelope text-slate-400"></i> Email
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: budi@gmail.com">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-solid fa-phone text-slate-400"></i> Nomor Telepon / WA
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: 0812xxxx">
                    </div>
                </div>

                <hr class="border-slate-100 my-4">

                <!-- Profil Tambahan -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Motto Hidup / Kata Mutiara</label>
                    <input type="text" name="motto" value="{{ old('motto') }}" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Contoh: Mengabdi untuk kemanusiaan tiada henti.">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Visi</label>
                    <textarea name="vision" rows="3" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Tuliskan visi Anda sebagai anggota PMR...">{{ old('vision') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Misi</label>
                    <textarea name="mission" rows="3" class="w-full bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Tuliskan misi / langkah konkrit Anda...">{{ old('mission') }}</textarea>
                </div>

                <div class="pt-6 border-t border-slate-200 flex justify-start gap-3">
                    <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-red-950/20 transition flex items-center gap-2">
                        SIMPAN / DAFTAR
                    </button>
                    <a href="{{ route('admin.members.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">
                        Batal
                    </a>
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

    photoInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                photoPreview.setAttribute('src', e.target.result);
                photoPreview.classList.remove('hidden');
                photoPlaceholder.classList.add('hidden');
                uploadIcon.classList.remove('hidden');
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
@endpush
