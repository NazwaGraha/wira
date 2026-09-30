@extends('layouts.admin')

@section('title', 'Kelola Stok Darah')
@section('page_title', 'Kelola Stok Darah')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Status Stok Darah (Live)</h2>
            <p class="text-slate-500 text-sm mt-1">Ubah jumlah kantong darah untuk update status live di halaman publik.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($stocks as $stock)
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                <form action="{{ route('admin.blood-stocks.update', $stock->id) }}" method="POST" class="flex flex-col h-full">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center font-bold text-xl text-red-600 border border-red-200 shadow-sm">
                            {{ $stock->blood_type }}
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                            @if($stock->status == 'aman') bg-emerald-100 text-emerald-700
                            @elseif($stock->status == 'menipis') bg-amber-100 text-amber-700
                            @else bg-rose-100 text-rose-700 @endif
                        ">
                            {{ $stock->status }}
                        </span>
                    </div>

                    <div class="space-y-4 flex-grow">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jumlah Kantong</label>
                            <input type="number" name="bags_count" value="{{ $stock->bags_count }}" class="w-full bg-white border border-slate-300 px-3 py-2 rounded-xl text-slate-800 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition shadow-sm font-bold text-lg" min="0">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Status Live</label>
                            <select name="status" class="w-full bg-white border border-slate-300 px-3 py-2 rounded-xl text-slate-800 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition shadow-sm text-sm">
                                <option value="aman" {{ $stock->status == 'aman' ? 'selected' : '' }}>Aman (Hijau)</option>
                                <option value="menipis" {{ $stock->status == 'menipis' ? 'selected' : '' }}>Menipis (Kuning)</option>
                                <option value="kritis" {{ $stock->status == 'kritis' ? 'selected' : '' }}>Kritis (Merah)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-200">
                        <button type="submit" class="w-full bg-slate-800 hover:bg-pmr-primary text-white py-2 rounded-xl font-bold text-sm transition shadow-md hover:shadow-red-900/30 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
