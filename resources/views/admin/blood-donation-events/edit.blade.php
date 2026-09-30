@extends('layouts.admin')

@section('title', 'Edit Jadwal Donor')
@section('page_title', 'Edit Jadwal Donor Darah')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-toolbar.ql-snow {
        border: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        background-color: #f8fafc;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        padding: 12px 14px !important;
    }
    .ql-container.ql-snow {
        border: none !important;
        font-family: inherit !important;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
        background-color: #ffffff;
    }
    .ql-editor {
        min-height: 250px;
        font-size: 0.95rem;
        line-height: 1.75;
        color: #1e293b;
        padding: 16px 20px !important;
    }
    .ql-editor.ql-blank::before {
        color: #94a3b8;
        font-style: normal;
        left: 20px;
    }
    .ql-snow .ql-stroke { stroke: #475569; }
    .ql-snow .ql-fill { fill: #475569; }
    .ql-snow .ql-picker { color: #475569; font-weight: 600; }
    .ql-snow.ql-toolbar button:hover, .ql-snow.ql-toolbar button:focus, .ql-snow.ql-toolbar button.ql-active,
    .ql-snow.ql-toolbar .ql-picker-label:hover, .ql-snow.ql-toolbar .ql-picker-item:hover { color: #980000 !important; }
    .ql-snow.ql-toolbar button:hover .ql-stroke, .ql-snow.ql-toolbar button.ql-active .ql-stroke { stroke: #980000 !important; }
    .ql-snow.ql-toolbar button:hover .ql-fill, .ql-snow.ql-toolbar button.ql-active .ql-fill { fill: #980000 !important; }
</style>
@endpush

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <form id="event-form" action="{{ route('admin.blood-donation-events.update', $bloodDonationEvent->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama/Judul Event <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $bloodDonationEvent->title) }}" required class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Banner Kegiatan / Gambar Poster (Opsional)</label>
                @if($bloodDonationEvent->banner_image)
                    <div class="mb-3 w-48 h-32 rounded-xl border border-slate-200 overflow-hidden bg-slate-100">
                        <img src="{{ Storage::url($bloodDonationEvent->banner_image) }}" alt="Banner" class="w-full h-full object-cover">
                    </div>
                @endif
                <input type="file" name="banner_image" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-pmr-primary file:text-white hover:file:bg-pmr-dark file:cursor-pointer transition">
                <span class="text-xs text-slate-400 mt-1 block">Format: JPG/PNG/GIF. Biarkan kosong jika tidak ingin mengubah banner.</span>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Event <span class="text-rose-500">*</span></label>
                <input type="date" name="event_date" value="{{ old('event_date', $bloodDonationEvent->event_date ? $bloodDonationEvent->event_date->format('Y-m-d') : '') }}" required class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>

            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Jam Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" name="time_start" value="{{ old('time_start', $bloodDonationEvent->time_start ? \Carbon\Carbon::parse($bloodDonationEvent->time_start)->format('H:i') : '') }}" required class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Jam Selesai</label>
                    <input type="time" name="time_end" value="{{ old('time_end', $bloodDonationEvent->time_end ? \Carbon\Carbon::parse($bloodDonationEvent->time_end)->format('H:i') : '') }}" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi <span class="text-rose-500">*</span></label>
                <input type="text" name="location" value="{{ old('location', $bloodDonationEvent->location) }}" required class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Target Darah (Kantong)</label>
                <input type="number" name="target_bags" value="{{ old('target_bags', $bloodDonationEvent->target_bags) }}" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Link Pendaftaran (Opsional)</label>
                <input type="url" name="registration_link" value="{{ old('registration_link', $bloodDonationEvent->registration_link) }}" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi (Opsional)</label>
                <textarea name="description" id="description" class="hidden">{{ old('description', $bloodDonationEvent->description) }}</textarea>
                <div class="bg-white rounded-2xl border border-slate-300 shadow-sm focus-within:ring-2 focus-within:ring-pmr-primary focus-within:border-pmr-primary transition overflow-hidden">
                    <div id="quill-editor">
                        {!! old('description', $bloodDonationEvent->description) !!}
                    </div>
                </div>
            </div>
            
            <div class="col-span-1 md:col-span-2 space-y-3">
                <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="is_registration_link_active" value="1" {{ old('is_registration_link_active', $bloodDonationEvent->is_registration_link_active) ? 'checked' : '' }} class="w-5 h-5 text-red-500 rounded border-slate-300 focus:ring-red-500">
                    <div>
                        <div class="font-bold text-slate-800">Aktifkan Form/Link Pendaftaran</div>
                        <div class="text-xs text-slate-500">Tampilkan tombol atau link pendaftaran online untuk kegiatan ini.</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $bloodDonationEvent->is_active) ? 'checked' : '' }} class="w-5 h-5 text-red-500 rounded border-slate-300 focus:ring-red-500">
                    <div>
                        <div class="font-bold text-slate-800">Tampilkan Countdown di Halaman Depan</div>
                        <div class="text-xs text-slate-500">Hanya satu event yang bisa aktif sebagai countdown di halaman publik. Event lain yang aktif akan otomatis dimatikan.</div>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
            <a href="{{ route('admin.blood-donation-events.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">Batal</a>
            <button type="submit" class="bg-pmr-primary hover:bg-pmr-dark text-white px-5 py-2.5 rounded-xl font-bold shadow-md shadow-red-950/20 transition flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const icons = Quill.import('ui/icons');
        icons['undo'] = '<svg viewBox="0 0 18 18"><polygon class="ql-fill ql-stroke" points="6 10 4 12 2 10 6 10"></polygon><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
        icons['redo'] = '<svg viewBox="0 0 18 18"><polygon class="ql-fill ql-stroke" points="12 10 14 12 16 10 12 10"></polygon><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

        const toolbarOptions = [
            ['undo', 'redo'],
            [{ 'font': [] }, { 'size': ['small', false, 'large', 'huge'] }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            ['blockquote', 'code-block'],
            [{ 'list': 'ordered'}, { 'list': 'bullet'}, { 'list': 'check' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],
            [{ 'align': [] }],
            ['link', 'image', 'video'],
            ['clean']
        ];

        const quill = new Quill('#quill-editor', {
            modules: {
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        undo: function() { this.quill.history.undo(); },
                        redo: function() { this.quill.history.redo(); }
                    }
                },
                history: { delay: 500, maxStack: 200, userOnly: true }
            },
            theme: 'snow',
            placeholder: 'Tuliskan deskripsi lengkap acara donor darah...'
        });

        const form = document.getElementById('event-form');
        const descriptionInput = document.getElementById('description');

        form.addEventListener('submit', function (e) {
            const html = quill.root.innerHTML;
            const text = quill.getText().trim();
            const hasMedia = html.includes('<img') || html.includes('<iframe') || html.includes('<video');
            descriptionInput.value = (text.length === 0 && !hasMedia) ? '' : html;
        });
    });
</script>
@endpush
