@extends('layouts.admin')

@section('page_title', 'Kelola Gambar Maskot Lomba')

@section('content')
<div class="space-y-6">

    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white flex items-center justify-center text-xl shadow-md shadow-amber-500/20">
                <i class="fa-solid fa-cat"></i>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900">Kelola Gambar Maskot Lomba</h2>
                <p class="text-xs text-slate-500">Unggah dan perbarui karakter maskot lomba untuk tingkat Mula, Madya, dan Wira.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.competition-scores.index', ['level' => $activeLevel]) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Input Nilai</span>
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Alert Errors -->
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 shadow-xs">
            <div class="flex items-center gap-2 mb-1">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>Gagal memperbarui maskot:</span>
            </div>
            <ul class="list-disc list-inside text-[11px] font-semibold text-rose-700 pl-4">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Level Tabs Switcher -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.competition-mascots.index', ['level' => 'Mula']) }}" class="px-6 py-3 rounded-xl font-extrabold text-sm transition {{ $activeLevel == 'Mula' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 border border-emerald-600' : 'bg-white text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200' }}">
            <i class="fa-solid fa-child-reaching mr-1.5"></i> PMR MULA (SD / Slayer Hijau)
        </a>
        <a href="{{ route('admin.competition-mascots.index', ['level' => 'Madya']) }}" class="px-6 py-3 rounded-xl font-extrabold text-sm transition {{ $activeLevel == 'Madya' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 border border-blue-600' : 'bg-white text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300 border border-slate-200' }}">
            <i class="fa-solid fa-user-group mr-1.5"></i> PMR MADYA (SMP / Slayer Biru)
        </a>
        <a href="{{ route('admin.competition-mascots.index', ['level' => 'Wira']) }}" class="px-6 py-3 rounded-xl font-extrabold text-sm transition {{ $activeLevel == 'Wira' ? 'bg-amber-400 text-slate-950 font-black shadow-md shadow-amber-400/30 border border-amber-400' : 'bg-white text-slate-700 hover:bg-amber-50 hover:text-amber-800 hover:border-amber-300 border border-slate-200' }}">
            <i class="fa-solid fa-graduation-cap mr-1.5"></i> PMR WIRA (SMA / Slayer Kuning)
        </a>
    </div>

    <!-- Mascot Cards Grid for Active Level -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach(['Putra', 'Putri', 'Umum'] as $cat)
            @php
                $item = $mascots[$activeLevel][$cat] ?? null;
                $cardColor = match($cat) {
                    'Putra' => ['border' => 'border-blue-200', 'badge_bg' => 'bg-blue-600', 'badge_text' => 'text-white', 'icon' => 'fa-mars', 'accent' => 'text-blue-600'],
                    'Putri' => ['border' => 'border-rose-200', 'badge_bg' => 'bg-rose-600', 'badge_text' => 'text-white', 'icon' => 'fa-venus', 'accent' => 'text-rose-600'],
                    default => ['border' => 'border-slate-200', 'badge_bg' => 'bg-slate-800', 'badge_text' => 'text-white', 'icon' => 'fa-users', 'accent' => 'text-slate-800'],
                };
            @endphp
            <div class="bg-white rounded-2xl border {{ $cardColor['border'] }} shadow-xs hover:shadow-md transition overflow-hidden flex flex-col justify-between">
                
                <!-- Card Header -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl font-black text-xs {{ $cardColor['badge_bg'] }} {{ $cardColor['badge_text'] }} shadow-xs uppercase tracking-wider">
                        <i class="fa-solid {{ $cardColor['icon'] }}"></i>
                        <span>Kategori {{ $cat }}</span>
                    </div>
                    <span class="text-[11px] font-mono font-bold text-slate-400">
                        {{ $activeLevel }}{{ $cat }}.png
                    </span>
                </div>

                <!-- Preview Area with Checkerboard Background -->
                <div class="p-6 flex flex-col items-center justify-center bg-radial from-slate-50 to-slate-100/80 min-h-[280px] relative group">
                    @if(!empty($item['url']))
                        <div class="relative w-44 h-56 flex items-center justify-center">
                            <img src="{{ $item['url'] }}" id="preview-image-{{ $activeLevel }}-{{ $cat }}" alt="Maskot {{ $activeLevel }} {{ $cat }}" class="max-h-full max-w-full object-contain filter drop-shadow-lg group-hover:scale-105 transition duration-300">
                        </div>
                    @else
                        <div class="w-32 h-44 rounded-2xl border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-xs text-center p-3">
                            <i class="fa-solid fa-image text-3xl mb-2 text-slate-300"></i>
                            <span>Belum ada gambar maskot</span>
                        </div>
                    @endif

                    <!-- Image Metadata Chips -->
                    @if(!empty($item['exists']))
                        <div class="mt-4 flex flex-wrap items-center justify-center gap-1.5 text-[10px] font-semibold text-slate-600">
                            @if($item['dimensions'])
                                <span class="bg-white/80 px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs">
                                    <i class="fa-solid fa-ruler-combined text-slate-400 mr-1"></i> {{ $item['dimensions'] }}
                                </span>
                            @endif
                            @if($item['size'])
                                <span class="bg-white/80 px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs">
                                    <i class="fa-solid fa-hard-drive text-slate-400 mr-1"></i> {{ $item['size'] }} KB
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Form Upload Card Footer -->
                <div class="p-5 border-t border-slate-100 bg-slate-50/50 space-y-3">
                    <form action="{{ route('admin.competition-mascots.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="hidden" name="level" value="{{ $activeLevel }}">
                        <input type="hidden" name="gender_category" value="{{ $cat }}">

                        <!-- File Input Button -->
                        <div>
                            <input type="file" name="mascot_image" id="input-mascot-{{ $activeLevel }}-{{ $cat }}" accept="image/png,image/jpeg,image/webp" required class="hidden" onchange="previewUploadImage(this, 'preview-image-{{ $activeLevel }}-{{ $cat }}', 'btn-submit-{{ $activeLevel }}-{{ $cat }}', 'label-file-{{ $activeLevel }}-{{ $cat }}')">
                            
                            <button type="button" onclick="document.getElementById('input-mascot-{{ $activeLevel }}-{{ $cat }}').click()" class="w-full bg-white hover:bg-slate-100 text-slate-700 font-bold py-2.5 px-3 rounded-xl border border-slate-200 transition text-xs flex items-center justify-center gap-2 shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-cloud-arrow-up text-amber-500"></i>
                                <span id="label-file-{{ $activeLevel }}-{{ $cat }}">Pilih Gambar Baru (.png)</span>
                            </button>
                        </div>

                        <!-- Submit Button (Enabled upon selecting a file) -->
                        <button type="submit" id="btn-submit-{{ $activeLevel }}-{{ $cat }}" class="w-full bg-slate-900 hover:bg-black text-white font-black py-2.5 px-3 rounded-xl transition text-xs flex items-center justify-center gap-2 shadow-sm disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                            <i class="fa-solid fa-check"></i>
                            <span>Simpan Maskot {{ $cat }}</span>
                        </button>
                    </form>

                    @if(!empty($item['url']))
                        <div class="flex items-center justify-between text-[11px] pt-1">
                            <span class="text-slate-400">Diperbarui: {{ $item['updated_at'] ?? '-' }}</span>
                            <a href="{{ $item['url'] }}" download="{{ $activeLevel }}{{ $cat }}.png" class="text-amber-700 hover:text-amber-800 font-bold flex items-center gap-1">
                                <i class="fa-solid fa-download text-[10px]"></i> Unduh
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        @endforeach
    </div>

    <!-- Petunjuk Format Gambar Maskot -->
    <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-5 text-xs text-amber-950 space-y-2 shadow-2xs">
        <div class="font-black flex items-center gap-2 text-sm text-amber-900">
            <i class="fa-solid fa-lightbulb text-amber-600 text-base"></i>
            <span>Tips Rekomendasi Format Gambar Maskot:</span>
        </div>
        <ul class="list-disc list-inside space-y-1 text-slate-700 text-xs pl-2">
            <li>Gunakan format <strong>PNG dengan latar belakang transparan</strong> agar karakter menyatu halus dengan kartu nilai.</li>
            <li>Rasio gambar disarankan <strong>Portrait / Vertikal (contoh: 768 × 1024 px atau 500 × 700 px)</strong>.</li>
            <li>Ukuran file maksimal <strong>5 MB</strong> per gambar.</li>
            <li>Setelah Anda klik <strong>Simpan Maskot</strong>, gambar akan langsung aktif dan muncul di seluruh kartu input nilai panitia & juri.</li>
        </ul>
    </div>

</div>

@push('scripts')
<script>
    function previewUploadImage(input, previewImgId, submitBtnId, labelId) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const previewImg = document.getElementById(previewImgId);
                if (previewImg) {
                    previewImg.src = e.target.result;
                    previewImg.classList.add('ring-4', 'ring-amber-400', 'rounded-xl');
                }
            };
            reader.readAsDataURL(file);

            const submitBtn = document.getElementById(submitBtnId);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-40', 'cursor-not-allowed');
                submitBtn.classList.add('bg-amber-600', 'hover:bg-amber-700');
                submitBtn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Upload Sekarang (' + (file.size / 1024).toFixed(0) + ' KB)';
            }

            const labelEl = document.getElementById(labelId);
            if (labelEl) {
                labelEl.textContent = file.name;
            }
        }
    }
</script>
@endpush
@endsection
