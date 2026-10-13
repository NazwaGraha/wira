@extends('layouts.app')

@section('title', 'Area Anggota | Biodata Pribadi PMR Wira SMAN 1 Ciawi')

@section('content')
<div class="bg-slate-100 min-h-screen py-8 sm:py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Header & Selamat Datang -->
        <div class="bg-gradient-to-r from-red-900 via-pmr-primary to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5 text-center md:text-left flex-col md:flex-row">
                <div class="relative">
                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-4 border-white/20 shadow-lg">
                    <span class="absolute -bottom-2 -right-2 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider shadow {{ $member->effective_status === 'Purna' ? 'bg-amber-500 text-slate-950' : 'bg-emerald-500 text-white' }}">
                        {{ $member->effective_status }}
                    </span>
                </div>
                <div>
                    <div class="text-xs font-semibold text-red-200 uppercase tracking-widest mb-1 flex items-center justify-center md:justify-start gap-1.5">
                        <i class="fa-solid fa-id-card-clip"></i> Area Anggota PMR Wira
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $member->name }}</h1>
                    <div class="text-sm text-slate-200 mt-1 flex flex-wrap items-center justify-center md:justify-start gap-3">
                        <span>NRI: <strong class="text-white">{{ $member->nri }}</strong></span>
                        @if($member->nip)
                            <span>| NIP: <strong class="text-white">{{ $member->nip }}</strong></span>
                        @endif
                        <span>| Jabatan: <strong class="text-white">{{ $member->position ?: 'Anggota' }}</strong></span>
                        <span>| Tahun Masuk: <strong class="text-white">{{ $member->entry_year ?: '-' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Tombol Keluar & Backoffice (Jika berhak) -->
            <div class="flex items-center gap-3">
                @if(method_exists($member, 'canAccessBackoffice') && $member->canAccessBackoffice())
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl border border-white/20 transition flex items-center gap-2">
                        <i class="fa-solid fa-gauge-high"></i> Ke Backoffice
                    </a>
                @endif
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow transition flex items-center gap-2">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 rounded-2xl text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 p-4 bg-sky-50 border-l-4 border-sky-600 text-sky-800 rounded-2xl text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-info text-sky-600 text-lg"></i>
                <div>{{ session('info') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 text-red-800 rounded-2xl text-sm shadow-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Biodata Lengkap -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                    <h2 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-4 mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-user-check text-pmr-primary"></i> Biodata Pribadi Anggota
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Nomor Registrasi Induk (NRI)</span>
                            <span class="text-base font-bold text-slate-800">{{ $member->nri }}</span>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Nama Lengkap</span>
                            <span class="text-base font-bold text-slate-800">{{ $member->name }}</span>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Jabatan / Posisi</span>
                            <span class="text-base font-bold text-slate-800">{{ $member->position ?: 'Anggota' }}</span>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Status Keanggotaan</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold mt-1 {{ $member->effective_status === 'Purna' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                <i class="fa-solid fa-circle text-[8px]"></i> {{ $member->effective_status }} (Tahun Masuk: {{ $member->entry_year ?: '-' }})
                            </span>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Kelas / Tingkat</span>
                            <span class="text-base font-bold text-slate-800">{{ $member->class_grade ?: '-' }}</span>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Tempat, Tanggal Lahir</span>
                            <span class="text-base font-bold text-slate-800">
                                {{ $member->birth_place ?: '-' }}, {{ $member->birth_date ? $member->birth_date->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Jenis Kelamin</span>
                            <span class="text-base font-bold text-slate-800">{{ $member->gender ?: '-' }}</span>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Nomor Kontak (WhatsApp)</span>
                            <span class="text-base font-bold text-slate-800">{{ $member->phone ?: '-' }}</span>
                        </div>

                        <div class="sm:col-span-2 p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Alamat Email</span>
                            <span class="text-base font-bold text-slate-800">{{ $member->email ?: '-' }}</span>
                        </div>

                        <div class="sm:col-span-2 p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-semibold block uppercase">Alamat Domisili</span>
                            <span class="text-sm font-medium text-slate-800">{{ $member->address ?: '-' }}</span>
                        </div>

                        @if($member->motto)
                            <div class="sm:col-span-2 p-4 bg-red-50/50 rounded-2xl border border-red-100">
                                <span class="text-xs text-pmr-primary font-bold block uppercase tracking-wider mb-1">Motto Hidup</span>
                                <p class="text-sm italic text-slate-700">"{{ $member->motto }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Ganti Kata Sandi -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                    <h2 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-4 mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-pmr-primary"></i> Ganti Kata Sandi Akun
                    </h2>

                    <form action="{{ route('member.password.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Baru</label>
                                <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ulangi Kata Sandi Baru</label>
                                <input type="password" name="password_confirmation" required placeholder="Ketik ulang kata sandi baru" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-pmr-primary">
                            </div>
                        </div>

                        <button type="submit" class="px-6 py-3 bg-pmr-primary hover:bg-pmr-dark text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow transition">
                            <i class="fa-solid fa-save mr-1.5"></i> Perbarui Kata Sandi
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Pratinjau KTA Digital -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                    <h2 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-4 mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-address-card text-pmr-primary"></i> Kartu Tanda Anggota (KTA)
                    </h2>

                    <!-- KTA Card View -->
                    <div class="bg-gradient-to-br from-red-800 via-pmr-primary to-slate-900 text-white rounded-2xl p-5 shadow-xl relative overflow-hidden">
                        <div class="flex items-center justify-between border-b border-white/20 pb-3 mb-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('images/logo_login_official.png') }}" alt="Logo" class="h-7 bg-white rounded p-0.5">
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-red-200">KTA PMR WIRA</span>
                        </div>

                        <div class="flex gap-4 items-center">
                            <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="w-16 h-20 rounded-lg object-cover border-2 border-white/30 shadow">
                            <div class="min-w-0">
                                <div class="text-[10px] uppercase font-bold text-red-200">Nomor Registrasi Induk</div>
                                <div class="text-sm font-black tracking-wide">{{ $member->nri }}</div>
                                <div class="text-xs font-bold text-white mt-1 truncate">{{ $member->name }}</div>
                                <div class="text-[11px] text-red-100">{{ $member->position ?: 'Anggota PMR Wira' }}</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-white/10 flex justify-between items-center text-[10px] text-slate-300">
                            <span>SMAN 1 CIAWI</span>
                            <span class="font-bold text-white uppercase">{{ $member->effective_status }}</span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 mt-4 text-center">
                        Kartu identitas resmi keanggotaan PMR Wira SMAN 1 Ciawi unit masa bakti Ragana Dwi Pantara.
                    </p>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
