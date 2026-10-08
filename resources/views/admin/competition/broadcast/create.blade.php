@extends('layouts.admin')

@section('title', 'Tulis Siaran Email Informasi Lomba')
@section('page_title', 'Form Siaran Email & Informasi Lomba')

@push('styles')
    <!-- Summernote Lite CSS & Custom Styling -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        .note-editor.note-frame {
            border: 1px solid #e2e8f0 !important;
            border-radius: 14px !important;
            overflow: hidden !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            background: #ffffff !important;
        }
        .note-toolbar {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 8px 10px !important;
        }
        .note-btn {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 5px 9px !important;
            font-size: 12px !important;
            color: #334155 !important;
            transition: all 0.15s ease !important;
        }
        .note-btn:hover, .note-btn.active {
            background: #fee2e2 !important;
            border-color: #ef4444 !important;
            color: #980000 !important;
        }
        .note-editable {
            font-family: inherit !important;
            font-size: 14px !important;
            line-height: 1.7 !important;
            color: #1e293b !important;
            min-height: 320px !important;
            padding: 20px !important;
            background: #ffffff !important;
        }
        .note-editable img {
            max-width: 100% !important;
            border-radius: 8px !important;
            margin: 6px 0 !important;
            transition: outline 0.15s ease;
        }
        .note-editable img:hover {
            outline: 2px dashed #dc2626 !important;
        }
        .note-modal .modal-content {
            border-radius: 16px !important;
            border: none !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        .note-float-center {
            display: block !important;
            margin-left: auto !important;
            margin-right: auto !important;
            float: none !important;
        }
        .note-popover .popover-content {
            border-radius: 10px !important;
            padding: 6px !important;
            background: #1e293b !important;
            color: #ffffff !important;
        }
        .note-popover .btn-group .note-btn {
            background: #334155 !important;
            border-color: #475569 !important;
            color: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
        }
        .note-popover .btn-group .note-btn:hover {
            background: #dc2626 !important;
            border-color: #ef4444 !important;
        }
    </style>
@endpush

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
            <form id="broadcast-form" action="{{ route('admin.competition-broadcast.send') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 space-y-6">
                @csrf

                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-pen-nib text-red-600"></i> Komposisi Pesan Email Siaran
                    </h3>
                    <p class="text-xs text-slate-500">Pesan ini akan dikirimkan otomatis ke seluruh kontak email sekolah / pembina sesuai target yang Anda tentukan.</p>
                </div>

                <hr class="border-slate-100">

                <!-- 1. Kriteria Target Penerima Database -->
                <div class="space-y-4 bg-slate-50 p-4 sm:p-5 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-filter text-indigo-500"></i> Kriteria Penerima dari Database
                        </span>
                        <span id="db-badge-count" class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100">
                            {{ count($recipients) }} Kontak dari Database
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

                    <!-- Tambahan Email Manual di Luar Database -->
                    <div class="pt-3 border-t border-slate-200">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-user-plus text-red-600"></i>
                                <span>Tambahan Email Penerima Lainnya (Di Luar Database)</span>
                            </label>
                            <span id="additional-count-badge" class="text-[10px] font-black text-slate-600 bg-slate-200 px-2 py-0.5 rounded-full">
                                0 Email Tambahan
                            </span>
                        </div>
                        <textarea id="additional_emails" name="additional_emails" rows="3" oninput="handleAdditionalEmailsInput(this.value)" placeholder="Ketik atau paste email tambahan (bisa lebih dari 1). Pisahkan dengan koma (,) atau baris baru (enter).&#10;Contoh:&#10;pembina.baru@gmail.com, sman1ciawi@sch.id&#10;pmr.bogorraya@gmail.com" class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs font-mono text-slate-800 focus:outline-none focus:border-red-500 leading-relaxed">{{ old('additional_emails') }}</textarea>
                        <p class="text-[11px] text-slate-500 mt-1.5 flex items-start gap-1.5 leading-snug">
                            <i class="fa-solid fa-circle-info text-sky-500 mt-0.5 text-xs"></i>
                            <span>Bisa memasukkan beberapa alamat email sekaligus. Email tambahan ini akan otomatis ikut menerima siaran bersama daftar sekolah di database.</span>
                        </p>
                    </div>
                </div>

                <!-- 2. Konten Email & Text Editor Lengkap -->
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
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Isi Pesan / Informasi Kegiatan <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-slate-400 font-semibold flex items-center gap-1">
                                <i class="fa-regular fa-image text-red-500"></i> Mendukung sisip gambar & atur ukuran/posisi
                            </span>
                        </div>
                        
                        <!-- Rich Text Editor (Summernote) -->
                        <textarea id="content-editor" name="content" required>{!! old('content') !!}</textarea>
                        
                        <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-lightbulb text-amber-500"></i>
                            <span><strong>Tips Gambar:</strong> Klik pada gambar yang telah disisipkan untuk mengatur ukuran (100%, 50%, 25%, drag sudut) dan posisi (Rata Kiri, Tengah, Rata Kanan).</span>
                        </p>
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
                        <div class="text-[11px] text-slate-500 mt-0.5">Ringkasan kontak database & email tambahan</div>
                    </div>
                    <span id="total-recipients-pill" class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-black rounded-full">
                        {{ count($recipients) }} Total Penerima
                    </span>
                </div>

                <!-- Summary Counter Stats -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Dari Database</div>
                        <div class="font-black text-slate-800 text-base mt-0.5">{{ count($recipients) }} Kontak</div>
                    </div>
                    <div class="p-3 bg-red-50/60 rounded-xl border border-red-100">
                        <div class="text-[10px] font-bold text-red-700 uppercase">Tambahan Manual</div>
                        <div id="additional-preview-count" class="font-black text-red-700 text-base mt-0.5">0 Kontak</div>
                    </div>
                </div>

                <!-- Preview List -->
                <div class="max-h-[500px] overflow-y-auto space-y-2 pr-1 divide-y divide-slate-100">
                    
                    <!-- Dynamic Container for Custom Additional Emails -->
                    <div id="custom-emails-container" class="space-y-2"></div>

                    <!-- Database Emails -->
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
                        <div id="no-db-recipients" class="py-8 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-user-slash text-2xl mb-2 text-slate-300"></i>
                            <div>Tidak ada kontak dari database yang cocok dengan filter yang dipilih.</div>
                        </div>
                    @endforelse
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-500 leading-relaxed">
                    <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i>
                    <strong>Anti Duplikasi:</strong> Setiap alamat email unik hanya akan menerima 1 salinan email per pengiriman.
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
    <!-- jQuery & Summernote Lite Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

    <script>
        const initialDbRecipientsCount = {{ count($recipients) }};
        const dbEmailsList = {!! json_encode(array_keys($recipients)) !!};

        $(document).ready(function() {
            // Inisialisasi Summernote Lite
            $('#content-editor').summernote({
                placeholder: 'Tuliskan pengumuman kegiatan, jadwal, juklak/juknis, atau informasi resmi lainnya...',
                tabsize: 2,
                height: 380,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'hr']],
                    ['imagePosition', ['imgAlignLeft', 'imgAlignCenter', 'imgAlignRight', 'imgAlignReset']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                buttons: {
                    imgAlignLeft: function() {
                        return $.summernote.ui.button({
                            contents: '<i class="fa-solid fa-align-left text-xs text-blue-500"></i> <span class="text-[11px] font-bold">Kiri</span>',
                            tooltip: 'Posisikan Gambar: Rata Kiri',
                            click: function() { alignSelectedImage('left'); }
                        }).render();
                    },
                    imgAlignCenter: function() {
                        return $.summernote.ui.button({
                            contents: '<i class="fa-solid fa-align-center text-xs text-red-500"></i> <span class="text-[11px] font-black text-red-600">Center</span>',
                            tooltip: 'Posisikan Gambar: Rata Tengah / Center',
                            click: function() { alignSelectedImage('center'); }
                        }).render();
                    },
                    imgAlignRight: function() {
                        return $.summernote.ui.button({
                            contents: '<i class="fa-solid fa-align-right text-xs text-emerald-500"></i> <span class="text-[11px] font-bold">Kanan</span>',
                            tooltip: 'Posisikan Gambar: Rata Kanan',
                            click: function() { alignSelectedImage('right'); }
                        }).render();
                    },
                    imgAlignReset: function() {
                        return $.summernote.ui.button({
                            contents: '<i class="fa-solid fa-arrows-rotate text-xs text-slate-400"></i> <span class="text-[11px]">Normal</span>',
                            tooltip: 'Reset Posisi Gambar (Normal)',
                            click: function() { alignSelectedImage('reset'); }
                        }).render();
                    }
                },
                popover: {
                    image: [
                        ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                        ['imageAlignment', ['imgAlignLeft', 'imgAlignCenter', 'imgAlignRight', 'imgAlignReset']],
                        ['remove', ['removeMedia']]
                    ],
                    link: [
                        ['link', ['linkDialogShow', 'unlink']]
                    ],
                    table: [
                        ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                        ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
                    ]
                },
                callbacks: {
                    onImageUpload: function(files) {
                        for (let i = 0; i < files.length; i++) {
                            uploadBroadcastImage(files[i]);
                        }
                    }
                }
            });

            // Trigger preview jika ada nilai lama pada additional emails
            const existingExtra = document.getElementById('additional_emails').value;
            if (existingExtra) {
                handleAdditionalEmailsInput(existingExtra);
            }
        });

        window.lastClickedImage = null;

        // Fungsi Penataan Posisi Gambar (Kiri, Center/Tengah, Kanan, Normal)
        function alignSelectedImage(alignment) {
            var $img = null;

            // 1. Cek dari selection box data('target')
            var $selection = $('.note-control-selection');
            if ($selection.length && $selection.is(':visible')) {
                var t = $selection.data('target');
                if (t && $(t).length) {
                    $img = $(t);
                }
            }

            // 2. Cek restoreTarget dari summernote
            if (!$img || !$img.length) {
                try {
                    var restored = $('#content-editor').summernote('restoreTarget');
                    if (restored && $(restored).length) $img = $(restored);
                } catch(e) {}
            }

            // 3. Cek gambar yang terakhir diklik
            if ((!$img || !$img.length) && window.lastClickedImage && $(window.lastClickedImage).length) {
                $img = $(window.lastClickedImage);
            }

            // 4. Fallback ke gambar pertama di editor jika hanya ada 1 gambar
            if ((!$img || !$img.length) && $('.note-editable img').length === 1) {
                $img = $('.note-editable img').first();
            }

            if (!$img || !$img.length) {
                alert('Silakan klik pada gambar terlebih dahulu untuk mengatur posisinya.');
                return;
            }

            // Simpan referensi terakhir
            window.lastClickedImage = $img[0];

            // Reset class posisi sebelumnya
            $img.removeClass('note-float-left note-float-right note-float-center');

            if (alignment === 'center') {
                $img.addClass('note-float-center');
                $img.css({
                    'float': 'none',
                    'display': 'block',
                    'margin-left': 'auto',
                    'margin-right': 'auto',
                    'margin-top': '14px',
                    'margin-bottom': '14px'
                });
                if ($img.parent().is('p, div')) {
                    $img.parent().css('text-align', 'center');
                }
            } else if (alignment === 'left') {
                $img.addClass('note-float-left');
                $img.css({
                    'float': 'left',
                    'display': 'inline-block',
                    'margin-left': '0',
                    'margin-right': '18px',
                    'margin-bottom': '14px',
                    'margin-top': '4px'
                });
                if ($img.parent().is('p, div') && $img.parent().css('text-align') === 'center') {
                    $img.parent().css('text-align', '');
                }
            } else if (alignment === 'right') {
                $img.addClass('note-float-right');
                $img.css({
                    'float': 'right',
                    'display': 'inline-block',
                    'margin-left': '18px',
                    'margin-right': '0',
                    'margin-bottom': '14px',
                    'margin-top': '4px'
                });
                if ($img.parent().is('p, div') && $img.parent().css('text-align') === 'center') {
                    $img.parent().css('text-align', '');
                }
            } else { // normal / reset
                $img.css({
                    'float': 'none',
                    'display': 'inline-block',
                    'margin-left': '',
                    'margin-right': '',
                    'margin-top': '',
                    'margin-bottom': ''
                });
                if ($img.parent().is('p, div') && $img.parent().css('text-align') === 'center') {
                    $img.parent().css('text-align', '');
                }
            }

            // Sinkronisasi posisi handle seleksi Summernote
            var editorContext = $('#content-editor').data('summernote');
            if (editorContext) {
                try {
                    editorContext.invoke('handle.update', $img[0]);
                } catch(e) {}
            }

            setTimeout(function() {
                updateImageSelectionToolbar($img);
            }, 50);
        }

        // Floating quick-bar langsung di atas gambar saat diklik atau setelah di-resize pointer
        function updateImageSelectionToolbar(targetImg) {
            var $selection = $('.note-control-selection');
            if ($selection.length && $selection.is(':visible')) {
                var $target = targetImg || $selection.data('target');
                if (!$target || !$($target).length) return;

                var $existing = $selection.find('.note-image-quick-align');
                if (!$existing.length) {
                    var $bar = $(`
                        <div class="note-image-quick-align" style="position: absolute; top: -42px; left: 50%; transform: translateX(-50%); background: #0f172a; color: white; padding: 4px 8px; border-radius: 9999px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 4px; z-index: 1070; pointer-events: auto; white-space: nowrap; border: 1px solid #334155;">
                            <span style="font-size: 10px; font-weight: 800; color: #94a3b8; margin: 0 4px 0 2px; text-transform: uppercase; letter-spacing: 0.5px;">Posisi:</span>
                            <button type="button" class="btn-align-left" title="Rata Kiri" style="background: #1e293b; color: white; border: 1px solid #475569; border-radius: 9999px; padding: 3px 9px; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-align-left text-blue-400"></i> Kiri
                            </button>
                            <button type="button" class="btn-align-center" title="Center (Rata Tengah)" style="background: #dc2626; color: white; border: 1px solid #ef4444; border-radius: 9999px; padding: 3px 10px; font-size: 11px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-align-center text-amber-300"></i> Center
                            </button>
                            <button type="button" class="btn-align-right" title="Rata Kanan" style="background: #1e293b; color: white; border: 1px solid #475569; border-radius: 9999px; padding: 3px 9px; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-align-right text-emerald-400"></i> Kanan
                            </button>
                            <button type="button" class="btn-align-reset" title="Reset Posisi Normal" style="background: #1e293b; color: #94a3b8; border: 1px solid #475569; border-radius: 9999px; padding: 3px 8px; font-size: 11px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-arrows-rotate"></i> Normal
                            </button>
                        </div>
                    `);
                    $bar.find('.btn-align-left').on('click mousedown', function(e) { e.preventDefault(); e.stopPropagation(); alignSelectedImage('left'); });
                    $bar.find('.btn-align-center').on('click mousedown', function(e) { e.preventDefault(); e.stopPropagation(); alignSelectedImage('center'); });
                    $bar.find('.btn-align-right').on('click mousedown', function(e) { e.preventDefault(); e.stopPropagation(); alignSelectedImage('right'); });
                    $bar.find('.btn-align-reset').on('click mousedown', function(e) { e.preventDefault(); e.stopPropagation(); alignSelectedImage('reset'); });
                    $selection.append($bar);
                }

                // Cek posisi agar tidak keluar dari batas atas editor
                var selTop = $selection.position().top;
                if (selTop < 45) {
                    $selection.find('.note-image-quick-align').css({ 'top': 'auto', 'bottom': '-42px' });
                } else {
                    $selection.find('.note-image-quick-align').css({ 'bottom': 'auto', 'top': '-42px' });
                }
            }
        }

        // Listener saat klik gambar atau selesai drag resize dengan pointer
        $(document).on('click', '.note-editable img', function(e) {
            window.lastClickedImage = this;
            setTimeout(function() {
                updateImageSelectionToolbar($(window.lastClickedImage));
            }, 60);
        });

        $(document).on('mouseup', function() {
            setTimeout(function() {
                var $selection = $('.note-control-selection');
                if ($selection.is(':visible')) {
                    var $t = $selection.data('target');
                    if ($t && $($t).length) {
                        window.lastClickedImage = $($t)[0];
                        updateImageSelectionToolbar($($t));
                    }
                }
            }, 80);
        });

        // AJAX Upload Image Handler
        function uploadBroadcastImage(file) {
            const data = new FormData();
            data.append('image', file);
            data.append('_token', '{{ csrf_token() }}');

            // Tampilkan status uploading sementara
            const $status = $('<div class="text-xs text-red-600 font-bold p-2 bg-red-50 rounded">Mengunggah gambar...</div>');
            $('.note-editor').append($status);

            $.ajax({
                url: "{{ route('admin.competition-broadcast.upload-image') }}",
                cache: false,
                contentType: false,
                processData: false,
                data: data,
                type: "POST",
                success: function(response) {
                    $status.remove();
                    if (response && response.url) {
                        $('#content-editor').summernote('insertImage', response.url, function($image) {
                            $image.css('max-width', '100%');
                            $image.addClass('rounded-lg');
                        });
                    }
                },
                error: function(xhr) {
                    $status.remove();
                    console.error('Upload Error:', xhr);
                    const msg = xhr.responseJSON && xhr.responseJSON.message 
                        ? xhr.responseJSON.message 
                        : 'Gagal mengunggah gambar. Pastikan format jpeg/png/webp dan ukuran di bawah 10MB.';
                    alert(msg);
                }
            });
        }

        // Live Preview & Counter untuk Email Tambahan Manual
        function handleAdditionalEmailsInput(text) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const rawParts = text.split(/[\r\n,;]+/);
            const validEmails = [];

            rawParts.forEach(part => {
                const clean = part.trim().toLowerCase();
                if (clean && emailRegex.test(clean) && !validEmails.includes(clean)) {
                    validEmails.push(clean);
                }
            });

            // Update badge di textarea
            const count = validEmails.length;
            document.getElementById('additional-count-badge').textContent = `${count} Email Tambahan`;
            document.getElementById('additional-preview-count').textContent = `${count} Kontak`;

            // Update Total Penerima Pill
            const total = initialDbRecipientsCount + count;
            document.getElementById('total-recipients-pill').textContent = `${total} Total Penerima`;

            // Render daftar email tambahan di kolom kanan
            const container = document.getElementById('custom-emails-container');
            container.innerHTML = '';

            if (validEmails.length > 0) {
                const header = document.createElement('div');
                header.className = 'text-[11px] font-black text-red-600 uppercase tracking-wider pb-1 flex items-center gap-1';
                header.innerHTML = '<i class="fa-solid fa-plus-circle"></i> Email Tambahan Baru:';
                container.appendChild(header);

                validEmails.forEach(email => {
                    const isDuplicateWithDb = dbEmailsList.includes(email);
                    const itemEl = document.createElement('div');
                    itemEl.className = 'p-2 bg-red-50/70 border border-red-200 rounded-xl flex items-center justify-between gap-2';
                    itemEl.innerHTML = `
                        <div class="min-w-0">
                            <div class="font-bold text-slate-800 text-xs font-mono truncate">${email}</div>
                            <div class="text-[10px] text-red-600 font-semibold">${isDuplicateWithDb ? '⚠️ Sudah ada di DB (Akan digabung)' : 'Kontak Tambahan (Custom)'}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-black bg-red-600 text-white shrink-0">
                            MANUAL
                        </span>
                    `;
                    container.appendChild(itemEl);
                });
            }
        }

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
@endpush
