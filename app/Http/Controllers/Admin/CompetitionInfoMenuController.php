<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionEvent;
use App\Models\CompetitionInfoMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CompetitionInfoMenuController extends Controller
{
    /**
     * Display a listing of information menus.
     */
    public function index(Request $request)
    {
        // 1. Self-healing check: Ensure table exists & seed defaults if empty
        try {
            if (!Schema::hasTable('competition_info_menus')) {
                Artisan::call('migrate', ['--force' => true]);
            }

            // Ensure contacts_data column exists
            if (Schema::hasTable('competition_info_menus') && !Schema::hasColumn('competition_info_menus', 'contacts_data')) {
                Schema::table('competition_info_menus', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->json('contacts_data')->nullable()->after('url_link');
                });
            }

            if (Schema::hasTable('competition_info_menus') && CompetitionInfoMenu::count() === 0) {
                $activeEvent = CompetitionEvent::where('is_active', true)->first() ?: CompetitionEvent::first();
                foreach (CompetitionInfoMenu::defaultItems() as $item) {
                    $item['competition_event_id'] = $activeEvent ? $activeEvent->id : null;
                    CompetitionInfoMenu::create($item);
                }
            } else {
                // Ensure existing Contact Person item has contacts_data and updated button_text
                $contactItem = CompetitionInfoMenu::where('title', 'like', '%Contact Person%')->first();
                if ($contactItem) {
                    $needSave = false;
                    if (empty($contactItem->contacts_data) || count($contactItem->contacts_data) === 0) {
                        $contactItem->contacts_data = CompetitionInfoMenu::defaultContacts();
                        $needSave = true;
                    }
                    if ($contactItem->button_text === 'Hubungi Contact Person' || $contactItem->button_text === 'Pilih Contact Person') {
                        $contactItem->button_text = 'Hubungi Panitia';
                        $needSave = true;
                    }
                    if ($needSave) {
                        $contactItem->save();
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently handle if database migration cannot run in current environment
        }

        $allEvents = collect();
        $selectedEvent = null;
        if (Schema::hasTable('competition_events')) {
            $allEvents = CompetitionEvent::orderByDesc('id')->get();
            $eventId = $request->query('event_id');
            if ($eventId) {
                $selectedEvent = CompetitionEvent::find($eventId);
            }
            if (!$selectedEvent) {
                $selectedEvent = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
            }
        }

        $query = CompetitionInfoMenu::orderBy('order_position')->orderBy('id');
        if ($selectedEvent) {
            $query->where(function($q) use ($selectedEvent) {
                $q->where('competition_event_id', $selectedEvent->id)
                  ->orWhereNull('competition_event_id');
            });
        }

        $infoMenus = $query->get();

        // Statistics
        $totalMenus = $infoMenus->count();
        $activeCount = $infoMenus->where('is_active', true)->count();
        $fileCount = $infoMenus->where('action_type', 'file')->count();
        $uploadedFileCount = $infoMenus->whereNotNull('file_path')->count();
        $linkCount = $infoMenus->whereIn('action_type', ['link', 'whatsapp'])->count();

        return view('admin.competition.info-menus.index', compact(
            'infoMenus',
            'allEvents',
            'selectedEvent',
            'totalMenus',
            'activeCount',
            'fileCount',
            'uploadedFileCount',
            'linkCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $activeEvent = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        $nextOrder = (CompetitionInfoMenu::max('order_position') ?? 0) + 1;

        return view('admin.competition.info-menus.create', compact('allEvents', 'activeEvent', 'nextOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_badge' => 'required|string|max:100',
            'icon' => 'required|string|max:100',
            'color_theme' => 'required|in:red,sky,rose,amber,emerald,purple,cyan,indigo',
            'description' => 'nullable|string|max:500',
            'action_type' => 'required|in:file,link,whatsapp,notice',
            'file_upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,jpg,jpeg,png|max:20480',
            'url_link' => 'nullable|string|max:500',
            'contacts_data' => 'nullable|array',
            'button_text' => 'required|string|max:100',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable',
            'competition_event_id' => 'nullable|exists:competition_events,id',
        ]);

        $filePath = null;
        if ($request->hasFile('file_upload')) {
            $filePath = $request->file('file_upload')->store('competition/info-docs', 'public');
        }

        $validated['file_path'] = $filePath;
        $validated['is_active'] = $request->has('is_active');
        $validated['order_position'] = $validated['order_position'] ?? ((CompetitionInfoMenu::max('order_position') ?? 0) + 1);

        if ($request->has('contacts_data') && is_array($request->input('contacts_data'))) {
            $validated['contacts_data'] = array_values(array_filter($request->input('contacts_data'), function($c) {
                return !empty($c['name']) || !empty($c['phone']);
            }));
        }

        unset($validated['file_upload']);

        CompetitionInfoMenu::create($validated);

        return redirect()->route('admin.competition-info-menus.index')
            ->with('success', 'Sub menu informasi lomba berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $infoMenu = CompetitionInfoMenu::findOrFail($id);
        $allEvents = CompetitionEvent::orderByDesc('id')->get();

        return view('admin.competition.info-menus.edit', compact('infoMenu', 'allEvents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $infoMenu = CompetitionInfoMenu::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_badge' => 'required|string|max:100',
            'icon' => 'required|string|max:100',
            'color_theme' => 'required|in:red,sky,rose,amber,emerald,purple,cyan,indigo',
            'description' => 'nullable|string|max:500',
            'action_type' => 'required|in:file,link,whatsapp,notice',
            'file_upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,jpg,jpeg,png|max:20480',
            'url_link' => 'nullable|string|max:500',
            'contacts_data' => 'nullable|array',
            'button_text' => 'required|string|max:100',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable',
            'remove_file' => 'nullable|boolean',
            'competition_event_id' => 'nullable|exists:competition_events,id',
        ]);

        // Handle file replacement or removal
        if ($request->hasFile('file_upload')) {
            if ($infoMenu->file_path && Storage::disk('public')->exists($infoMenu->file_path)) {
                Storage::disk('public')->delete($infoMenu->file_path);
            }
            $validated['file_path'] = $request->file('file_upload')->store('competition/info-docs', 'public');
        } elseif ($request->boolean('remove_file')) {
            if ($infoMenu->file_path && Storage::disk('public')->exists($infoMenu->file_path)) {
                Storage::disk('public')->delete($infoMenu->file_path);
            }
            $validated['file_path'] = null;
        } else {
            $validated['file_path'] = $infoMenu->file_path;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['order_position'] = $validated['order_position'] ?? $infoMenu->order_position;

        if ($request->has('contacts_data') && is_array($request->input('contacts_data'))) {
            $validated['contacts_data'] = array_values(array_filter($request->input('contacts_data'), function($c) {
                return !empty($c['name']) || !empty($c['phone']);
            }));
        }

        unset($validated['file_upload'], $validated['remove_file']);

        $infoMenu->update($validated);

        return redirect()->route('admin.competition-info-menus.index')
            ->with('success', 'Sub menu informasi "' . $infoMenu->title . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $infoMenu = CompetitionInfoMenu::findOrFail($id);

        if ($infoMenu->file_path && Storage::disk('public')->exists($infoMenu->file_path)) {
            Storage::disk('public')->delete($infoMenu->file_path);
        }

        $title = $infoMenu->title;
        $infoMenu->delete();

        return redirect()->route('admin.competition-info-menus.index')
            ->with('success', 'Sub menu "' . $title . '" berhasil dihapus!');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($id)
    {
        $infoMenu = CompetitionInfoMenu::findOrFail($id);
        $infoMenu->is_active = !$infoMenu->is_active;
        $infoMenu->save();

        $statusText = $infoMenu->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', 'Menu "' . $infoMenu->title . '" berhasil ' . $statusText . '!');
    }

    /**
     * Reset / restore 8 default menu items.
     */
    public function resetDefaults(Request $request)
    {
        $activeEvent = CompetitionEvent::where('is_active', true)->first() ?: CompetitionEvent::first();
        $eventId = $activeEvent ? $activeEvent->id : null;

        // Optionally delete existing or only insert missing defaults
        foreach (CompetitionInfoMenu::defaultItems() as $item) {
            $exists = CompetitionInfoMenu::where('title', $item['title'])->first();
            if (!$exists) {
                $item['competition_event_id'] = $eventId;
                CompetitionInfoMenu::create($item);
            }
        }

        return redirect()->route('admin.competition-info-menus.index')
            ->with('success', '8 Sub Menu default Informasi Lomba berhasil dipulihkan!');
    }

    /**
     * Quick upload file for a menu item directly from index card modal.
     */
    public function quickUpload(Request $request, $id)
    {
        $infoMenu = CompetitionInfoMenu::findOrFail($id);

        $request->validate([
            'file_upload' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip,jpg,jpeg,png|max:25600',
        ]);

        if ($infoMenu->file_path && Storage::disk('public')->exists($infoMenu->file_path)) {
            Storage::disk('public')->delete($infoMenu->file_path);
        }

        $filePath = $request->file('file_upload')->store('competition/info-docs', 'public');
        $infoMenu->file_path = $filePath;
        $infoMenu->action_type = 'file';
        $infoMenu->save();

        return redirect()->back()->with('success', 'Berkas untuk menu "' . $infoMenu->title . '" berhasil diunggah!');
    }
}
