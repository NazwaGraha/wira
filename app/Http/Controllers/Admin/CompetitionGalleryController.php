<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionEvent;
use App\Models\CompetitionGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompetitionGalleryController extends Controller
{
    /**
     * Tampilkan galeri momen/dokumentasi per edisi lomba.
     */
    public function index(Request $request)
    {
        $events = CompetitionEvent::orderByDesc('start_date')->orderByDesc('id')->get();

        $selectedEventId = $request->query('event_id')
            ?: ($events->firstWhere('is_active', true)?->id ?: $events->first()?->id);

        $selectedEvent = $events->firstWhere('id', $selectedEventId) ?: $events->first();

        $galleries = collect();
        if ($selectedEvent) {
            $galleries = CompetitionGallery::where('competition_event_id', $selectedEvent->id)
                ->orderBy('order_position')
                ->latest()
                ->get();
        }

        return view('admin.competition.gallery.index', compact('events', 'selectedEvent', 'galleries'));
    }

    /**
     * Upload satu atau beberapa foto dokumentasi sekaligus (Multiple Upload).
     */
    public function store(Request $request)
    {
        $request->validate([
            'competition_event_id' => 'required|exists:competition_events,id',
            'image_files' => 'required|array|min:1',
            'image_files.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'caption' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:100',
        ]);

        $eventId = $request->competition_event_id;
        $files = $request->file('image_files');
        $uploadedCount = 0;

        foreach ($files as $index => $file) {
            $path = $file->store('competition/galleries', 'public');

            CompetitionGallery::create([
                'competition_event_id' => $eventId,
                'image_path' => $path,
                'caption' => $request->caption ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'tag' => $request->tag ?: 'Momen Lomba',
                'order_position' => $index,
                'is_active' => true,
            ]);

            $uploadedCount++;
        }

        return redirect()->route('admin.competition-galleries.index', ['event_id' => $eventId])
            ->with('success', "Berhasil mengunggah {$uploadedCount} foto dokumentasi momen lomba!");
    }

    /**
     * Perbarui keterangan / caption foto.
     */
    public function update(Request $request, CompetitionGallery $gallery)
    {
        $validated = $request->validate([
            'caption' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:100',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $gallery->update([
            'caption' => $validated['caption'] ?? $gallery->caption,
            'tag' => $validated['tag'] ?? $gallery->tag,
            'order_position' => $validated['order_position'] ?? $gallery->order_position,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return redirect()->route('admin.competition-galleries.index', ['event_id' => $gallery->competition_event_id])
            ->with('success', 'Keterangan foto dokumentasi berhasil diperbarui.');
    }

    /**
     * Hapus foto dokumentasi.
     */
    public function destroy(CompetitionGallery $gallery)
    {
        $eventId = $gallery->competition_event_id;

        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        return redirect()->route('admin.competition-galleries.index', ['event_id' => $eventId])
            ->with('success', 'Foto dokumentasi berhasil dihapus.');
    }
}
