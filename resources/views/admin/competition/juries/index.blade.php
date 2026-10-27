@extends('layouts.admin')

@section('title', 'Input & Registrasi Juri - SUA BHAKTI BERKARYA')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900 border border-slate-800 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-user-gear"></i> SUA BHAKTI BERKARYA &bull; Manajemen Juri
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-2.5">
                Input & Registrasi Juri
            </h1>
            <p class="text-slate-400 text-sm mt-1 max-w-2xl">
                Buat kredensial akun login khusus juri lapangan dan petakan cabang mata lomba yang ditugaskan secara multi-cabang.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('jury.login') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 border border-slate-700 font-bold text-xs flex items-center gap-2 shadow-sm transition">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Portal Juri
            </a>
            <a href="{{ route('admin.competition-scores.index') }}" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs flex items-center gap-2 shadow-lg shadow-red-900/30 transition">
                <i class="fa-solid fa-calculator"></i> Lembar Nilai Admin
            </a>
        </div>
    </div>

    @if (session('success'))
    <div class="p-4 rounded-2xl bg-emerald-950/70 border border-emerald-800 text-emerald-200 text-sm flex items-center justify-between shadow-lg">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">✕</button>
    </div>
    @endif

    @if ($errors->any())
    <div class="p-4 rounded-2xl bg-rose-950/70 border border-rose-800 text-rose-200 text-sm shadow-lg space-y-1">
        <div class="font-bold flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-rose-400"></i> Mohon periksa input form:
        </div>
        <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-300">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Main Grid: Form Tambah/Edit Juri (Kiri) & Daftar Akun Juri (Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ================= PANEL FORM INPUT AKUN & MAPPING JURI (5 cols) ================= -->
        <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl text-slate-200 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Formulir Petugas</span>
                    <h3 class="text-base font-black text-white mt-0.5 flex items-center gap-2" id="form-title">
                        <i class="fa-solid fa-user-plus text-amber-400"></i> Tambah Akun Juri Baru
                    </h3>
                </div>
                <button type="button" id="btn-cancel-edit" onclick="resetJuryForm()" class="hidden px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300">
                    Batal Edit
                </button>
            </div>

            <form id="jury-form" action="{{ route('admin.competition-juries.store') }}" method="POST" class="space-y-4">
                @csrf
                <div id="method-container"></div>

                <!-- Nama Lengkap Juri -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Nama Lengkap Juri <span class="text-rose-400">*</span></label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-3.5 text-slate-500 text-sm"></i>
                        <input type="text" name="name" id="jury-name" value="{{ old('name') }}" required placeholder="Contoh: Kak Hendra Kusuma" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 focus:outline-none rounded-2xl pl-10 pr-3.5 py-2.5 text-sm text-white font-bold placeholder-slate-600 transition">
                    </div>
                </div>

                <!-- Username & Password / PIN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Username Login <span class="text-rose-400">*</span></label>
                        <div class="relative">
                            <i class="fa-solid fa-at absolute left-3.5 top-3.5 text-slate-500 text-sm"></i>
                            <input type="text" name="username" id="jury-username" value="{{ old('username') }}" required placeholder="juri_tandu1" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 focus:outline-none rounded-2xl pl-10 pr-3.5 py-2.5 text-sm font-mono text-white placeholder-slate-600 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">PIN / Password <span class="text-rose-400">*</span></label>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-3.5 top-3.5 text-slate-500 text-sm"></i>
                            <input type="text" name="password" id="jury-password" placeholder="123456" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 focus:outline-none rounded-2xl pl-10 pr-3.5 py-2.5 text-sm font-mono text-white placeholder-slate-600 transition">
                        </div>
                        <span class="text-[10px] text-slate-500 mt-1 block" id="password-hint">Bisa berupa 6 digit PIN agar login di HP cepat.</span>
                    </div>
                </div>

                <!-- No WhatsApp -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">No. WhatsApp / HP (Opsional)</label>
                    <div class="relative">
                        <i class="fa-brands fa-whatsapp absolute left-3.5 top-3.5 text-slate-500 text-sm"></i>
                        <input type="text" name="phone" id="jury-phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 focus:outline-none rounded-2xl pl-10 pr-3.5 py-2.5 text-sm text-white placeholder-slate-600 transition">
                    </div>
                </div>

                <!-- Checklist Pemetaan Cabang Lomba -->
                <div class="pt-2 border-t border-slate-800">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-black uppercase text-amber-300 tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-list-check"></i> Centang Mata Lomba yang Ditugaskan:
                        </label>
                        <span class="text-[10px] text-slate-400">Bisa Multi-Cabang</span>
                    </div>

                    <div class="bg-slate-950 border border-slate-800 rounded-2xl p-3.5 space-y-3 max-h-72 overflow-y-auto">
                        @php
                            $groupedCategories = $categories->groupBy('level');
                        @endphp

                        @forelse ($groupedCategories as $lvl => $cats)
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-wider mb-1.5 flex items-center gap-1.5 
                                    {{ $lvl == 'Mula' ? 'text-emerald-400' : ($lvl == 'Madya' ? 'text-sky-400' : 'text-amber-400') }}">
                                    <i class="fa-solid fa-circle text-[6px]"></i> Tingkat {{ $lvl }} ({{ $lvl == 'Mula' ? 'SD' : ($lvl == 'Madya' ? 'SMP' : 'SMA') }})
                                </div>
                                <div class="space-y-1.5">
                                    @foreach ($cats as $cat)
                                        <label class="flex items-center justify-between p-2 rounded-xl bg-slate-900/90 hover:bg-slate-850 border border-slate-800 hover:border-slate-700 cursor-pointer transition">
                                            <div class="flex items-center gap-2.5">
                                                <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" class="jury-category-checkbox w-4 h-4 rounded text-amber-500 focus:ring-0 bg-slate-950 border-slate-700">
                                                <div>
                                                    <div class="font-bold text-white text-xs">{{ $cat->name }}</div>
                                                    <div class="text-[10px] text-slate-400">{{ $cat->code }} &bull; {{ $cat->gender_category ?? 'Umum' }}</div>
                                                </div>
                                            </div>
                                            <span class="text-[9px] font-mono font-bold px-2 py-0.5 rounded-full 
                                                {{ $cat->level == 'Mula' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($cat->level == 'Madya' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20') }}">
                                                {{ $cat->level }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-500 text-xs">
                                Belum ada mata lomba aktif. Buat mata lomba di modul Event Lomba terlebih dahulu.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" id="btn-submit-jury" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-sm py-3.5 rounded-2xl shadow-lg shadow-amber-950/40 transition flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Akun & Tugaskan Juri
                    </button>
                </div>
            </form>
        </div>

        <!-- ================= DAFTAR AKUN JURI & PEMETAANNYA (7 cols) ================= -->
        <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl text-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Data Master</span>
                    <h3 class="text-base font-black text-white mt-0.5 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-sky-400"></i> Daftar Juri Terdaftar ({{ $juries->count() }})
                    </h3>
                </div>
                <span class="text-xs font-mono bg-slate-800 text-slate-300 px-3 py-1 rounded-xl border border-slate-700">
                    Aktif
                </span>
            </div>

            @if ($juries->isEmpty())
                <div class="text-center py-12 bg-slate-950 border border-slate-800 rounded-2xl p-6">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mx-auto text-2xl mb-3">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <h4 class="font-bold text-white text-sm">Belum Ada Akun Juri</h4>
                    <p class="text-slate-400 text-xs mt-1 max-w-sm mx-auto">
                        Gunakan formulir di sebelah kiri untuk membuat akun juri dan memetakan cabang lomba tugasnya.
                    </p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($juries as $jury)
                        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 hover:border-slate-700 transition space-y-3">
                            <!-- Jury Info Header -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-red-600 to-rose-600 text-white font-black text-sm flex items-center justify-center shadow-md">
                                        <i class="fa-solid fa-gavel"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-sm text-white">{{ $jury->name }}</h4>
                                        <div class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-amber-400 font-bold"><i class="fa-solid fa-at text-[10px]"></i> {{ $jury->username }}</span>
                                            @if ($jury->phone)
                                                <span>&bull;</span>
                                                <span><i class="fa-brands fa-whatsapp text-emerald-400"></i> {{ $jury->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-1.5">
                                    <button type="button" 
                                        onclick="editJury({{ json_encode([
                                            'id' => $jury->id,
                                            'name' => $jury->name,
                                            'username' => $jury->username,
                                            'phone' => $jury->phone,
                                            'category_ids' => $jury->assignedCategories->pluck('id')->toArray(),
                                        ]) }})" 
                                        class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 flex items-center justify-center text-xs transition" title="Edit Juri">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('admin.competition-juries.destroy', $jury->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun juri {{ addslashes($jury->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-900/60 text-slate-400 hover:text-rose-400 flex items-center justify-center text-xs transition" title="Hapus Juri">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Assigned Categories Badges -->
                            <div>
                                <div class="text-[10px] font-bold uppercase text-slate-500 mb-1.5 flex items-center justify-between">
                                    <span>Cabang Lomba yang Ditugaskan:</span>
                                    <span class="font-mono text-slate-400 font-bold">{{ $jury->assignedCategories->count() }} Cabang</span>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($jury->assignedCategories as $c)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold border
                                            {{ $c->level == 'Mula' ? 'bg-emerald-950/60 text-emerald-300 border-emerald-800' : ($c->level == 'Madya' ? 'bg-sky-950/60 text-sky-300 border-sky-800' : 'bg-amber-950/60 text-amber-300 border-amber-800') }}">
                                            <i class="fa-solid fa-circle text-[5px]"></i> {{ $c->name }} ({{ $c->level }})
                                        </span>
                                    @empty
                                        <span class="text-xs text-rose-400 italic font-semibold">Belum ada penugasan cabang lomba.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    function editJury(data) {
        document.getElementById('form-title').innerHTML = '<i class="fa-solid fa-pen-to-square text-amber-400"></i> Edit Akun Juri: ' + data.name;
        document.getElementById('btn-submit-jury').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Juri';
        document.getElementById('btn-cancel-edit').classList.remove('hidden');
        document.getElementById('password-hint').innerText = 'Kosongkan jika tidak ingin mengubah password/PIN.';

        document.getElementById('jury-name').value = data.name;
        document.getElementById('jury-username').value = data.username;
        document.getElementById('jury-phone').value = data.phone || '';
        document.getElementById('jury-password').removeAttribute('required');

        // Form action & method
        const form = document.getElementById('jury-form');
        form.action = '{{ url("/admin/competition-juries") }}/' + data.id;
        document.getElementById('method-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Check checkboxes
        const checkboxes = document.querySelectorAll('.jury-category-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = data.category_ids.includes(parseInt(cb.value));
        });

        // Scroll to form on mobile
        window.scrollTo({ top: form.offsetTop - 100, behavior: 'smooth' });
    }

    function resetJuryForm() {
        document.getElementById('form-title').innerHTML = '<i class="fa-solid fa-user-plus text-amber-400"></i> Tambah Akun Juri Baru';
        document.getElementById('btn-submit-jury').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Akun & Tugaskan Juri';
        document.getElementById('btn-cancel-edit').classList.add('hidden');
        document.getElementById('password-hint').innerText = 'Bisa berupa 6 digit PIN agar login di HP cepat.';

        document.getElementById('jury-name').value = '';
        document.getElementById('jury-username').value = '';
        document.getElementById('jury-phone').value = '';
        document.getElementById('jury-password').setAttribute('required', 'required');

        const form = document.getElementById('jury-form');
        form.action = '{{ route("admin.competition-juries.store") }}';
        document.getElementById('method-container').innerHTML = '';

        const checkboxes = document.querySelectorAll('.jury-category-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
    }
</script>
@endsection
