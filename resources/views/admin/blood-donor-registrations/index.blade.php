@extends('layouts.admin')

@section('title', 'Pendaftar Donor Darah')
@section('page_title', 'Data Pendaftar Donor Darah')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                <tr>
                    <th class="px-6 py-4">Nama Pendaftar</th>
                    <th class="px-6 py-4">Kontak</th>
                    <th class="px-6 py-4">Gol. Darah</th>
                    <th class="px-6 py-4">Event</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($registrations as $reg)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $reg->name }}</div>
                            <div class="text-xs text-slate-500">{{ $reg->gender ?? '-' }} &bull; {{ $reg->date_of_birth ? \Carbon\Carbon::parse($reg->date_of_birth)->age . ' Tahun' : '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-semibold">{{ $reg->phone }}</div>
                            <div class="text-xs text-slate-500">{{ $reg->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded font-bold">{{ $reg->blood_type }}{{ $reg->rhesus }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            @if($reg->event)
                                <div class="font-semibold text-slate-700">{{ $reg->event->title }}</div>
                                <div class="text-slate-500">{{ \Carbon\Carbon::parse($reg->event->event_date)->format('d M Y') }}</div>
                            @else
                                <span class="text-slate-400 italic">Tanpa Event Khusus</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.blood-donor-registrations.update', $reg->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded px-2 py-1 focus:ring-red-500 focus:border-red-500">
                                    <option value="pending" {{ $reg->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $reg->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ $reg->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="attended" {{ $reg->status == 'attended' ? 'selected' : '' }}>Hadir</option>
                                    <option value="completed" {{ $reg->status == 'completed' ? 'selected' : '' }}>Selesai Donor</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.blood-donor-registrations.destroy', $reg->id) }}" method="POST" onsubmit="return confirm('Hapus data pendaftar ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 transition" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <i class="fa-solid fa-users-slash text-4xl mb-3 text-slate-300 block"></i>
                            Belum ada pendaftar donor darah.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registrations->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $registrations->links() }}
        </div>
    @endif
</div>
@endsection
