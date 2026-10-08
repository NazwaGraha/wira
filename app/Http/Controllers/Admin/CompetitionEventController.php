<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionEvent;
use App\Models\CompetitionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompetitionEventController extends Controller
{
    /**
     * Display a listing of all competition events (History & Active).
     */
    public function index()
    {
        $events = CompetitionEvent::withCount(['categories', 'registrations'])
            ->orderByDesc('id')
            ->get();

        $activeEvent = CompetitionEvent::where('is_active', true)->first() ?: $events->first();

        return view('admin.competition.event.index', compact('events', 'activeEvent'));
    }

    /**
     * Show the form for creating a new competition event edition.
     */
    public function create()
    {
        $activeEvent = CompetitionEvent::where('is_active', true)->first();
        return view('admin.competition.event.create', compact('activeEvent'));
    }

    /**
     * Store a newly created competition event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'theme' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'registration_fee' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:150',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'handbook_file' => 'nullable|file|mimes:pdf|max:15360',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_registration_open'] = $request->boolean('is_registration_open');

        // Handle File Uploads
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }
        if ($request->hasFile('handbook_file')) {
            $validated['handbook_file'] = $request->file('handbook_file')->store('events/handbooks', 'public');
        }

        // If this new event is set as active, deactivate other events
        if ($validated['is_active']) {
            CompetitionEvent::query()->update(['is_active' => false]);
        }

        $newEvent = CompetitionEvent::create($validated);

        // Duplicate categories from an existing event if requested
        if ($request->boolean('clone_categories') && $request->filled('clone_source_id')) {
            $sourceEvent = CompetitionEvent::find($request->clone_source_id);
            if ($sourceEvent) {
                foreach ($sourceEvent->categories as $cat) {
                    $newCat = $cat->replicate();
                    $newCat->competition_event_id = $newEvent->id;
                    $newCat->save();
                }
            }
        }

        return redirect()->route('admin.competition-event.index')
            ->with('success', 'Edisi lomba baru "' . $newEvent->title . '" berhasil dibuat dan tersimpan ke database!');
    }

    /**
     * Display the specified competition event details & archive history.
     */
    public function show(CompetitionEvent $event)
    {
        $event->load(['categories' => function($q) {
            $q->orderBy('order_position');
        }, 'registrations' => function($q) {
            $q->withCount('teams')->latest();
        }]);

        return view('admin.competition.event.show', compact('event'));
    }

    /**
     * Show the form for editing the specified competition event.
     */
    public function edit(CompetitionEvent $event)
    {
        return view('admin.competition.event.edit', compact('event'));
    }

    /**
     * Update the specified competition event in storage.
     */
    public function update(Request $request, CompetitionEvent $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'theme' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'registration_fee' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:150',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'handbook_file' => 'nullable|file|mimes:pdf|max:15360',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_registration_open'] = $request->boolean('is_registration_open');

        // Update slug if title changed
        if ($event->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Upload banner if provided
        if ($request->hasFile('banner_image')) {
            if ($event->banner_image && Storage::disk('public')->exists($event->banner_image)) {
                Storage::disk('public')->delete($event->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }

        // Upload handbook if provided
        if ($request->hasFile('handbook_file')) {
            if ($event->handbook_file && Storage::disk('public')->exists($event->handbook_file)) {
                Storage::disk('public')->delete($event->handbook_file);
            }
            $validated['handbook_file'] = $request->file('handbook_file')->store('events/handbooks', 'public');
        }

        // If marked active, ensure others are deactivated
        if ($validated['is_active'] && !$event->is_active) {
            CompetitionEvent::where('id', '!=', $event->id)->update(['is_active' => false]);
        }

        $event->update($validated);

        return redirect()->route('admin.competition-event.index')
            ->with('success', 'Data edisi lomba "' . $event->title . '" berhasil diperbarui!');
    }

    /**
     * Activate a specific competition event as the current active event.
     */
    public function activate(CompetitionEvent $event)
    {
        CompetitionEvent::where('id', '!=', $event->id)->update(['is_active' => false]);
        $event->update(['is_active' => true]);

        return redirect()->route('admin.competition-event.index')
            ->with('success', 'Edisi "' . $event->title . '" sekarang telah diatur sebagai EVENT AKTIF di sistem!');
    }

    /**
     * Delete an event if it has no verified registrations.
     */
    public function destroy(CompetitionEvent $event)
    {
        if ($event->registrations()->count() > 0) {
            return redirect()->route('admin.competition-event.index')
                ->with('error', 'Edisi lomba tidak dapat dihapus karena sudah memiliki data pendaftaran sekolah!');
        }

        if ($event->is_active) {
            return redirect()->route('admin.competition-event.index')
                ->with('error', 'Edisi yang sedang AKTIF tidak dapat dihapus! Silakan aktifkan edisi lain terlebih dahulu.');
        }

        $title = $event->title;
        $event->categories()->delete();
        $event->delete();

        return redirect()->route('admin.competition-event.index')
            ->with('success', 'Edisi lomba "' . $title . '" berhasil dihapus dari database.');
    }
}
