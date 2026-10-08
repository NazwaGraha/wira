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
        $event->load(['categories' => function($q) {
            $q->orderBy('order_position')->orderBy('id');
        }]);

        $categoriesByLevel = [
            'Mula' => $event->categories->where('level', 'Mula'),
            'Madya' => $event->categories->where('level', 'Madya'),
            'Wira' => $event->categories->where('level', 'Wira'),
        ];

        return view('admin.competition.event.edit', compact('event', 'categoriesByLevel'));
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

    /**
     * Store a new competition category for this event.
     */
    public function storeCategory(Request $request, CompetitionEvent $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'level' => 'required|in:Mula,Madya,Wira',
            'gender_category' => 'required|in:Putra,Putri,Campuran,Umum',
            'scoring_type' => 'required|string|max:50',
            'point_tier' => 'required|in:tier_1,tier_2,tier_3',
            'registration_fee' => 'nullable|numeric|min:0',
            'max_team_members' => 'nullable|integer|min:1|max:20',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['competition_event_id'] = $event->id;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['registration_fee'] = $validated['registration_fee'] ?? ($event->registration_fee ?: 150000);
        $validated['max_team_members'] = $validated['max_team_members'] ?? 2;

        if (empty($validated['code'])) {
            $words = explode(' ', $validated['name']);
            $abbr = '';
            foreach ($words as $w) {
                $abbr .= strtoupper(substr($w, 0, 1));
            }
            $abbr = substr($abbr, 0, 4);
            $genderSuffix = match($validated['gender_category']) {
                'Putra' => 'PA',
                'Putri' => 'PI',
                default => 'UM',
            };
            $validated['code'] = "L{$abbr}-" . strtoupper($validated['level']) . "-{$genderSuffix}";
        }

        if (empty($validated['order_position'])) {
            $maxOrder = $event->categories()->max('order_position') ?? 0;
            $validated['order_position'] = $maxOrder + 1;
        }

        $category = CompetitionCategory::create($validated);

        return redirect()->route('admin.competition-event.edit', $event->id)
            ->with('success', "Cabang lomba '{$category->name} ({$category->level} - {$category->gender_category})' berhasil ditambahkan ke edisi ini!");
    }

    /**
     * Update an existing category within this event.
     */
    public function updateCategory(Request $request, CompetitionEvent $event, CompetitionCategory $category)
    {
        if ($category->competition_event_id !== $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'level' => 'required|in:Mula,Madya,Wira',
            'gender_category' => 'required|in:Putra,Putri,Campuran,Umum',
            'scoring_type' => 'required|string|max:50',
            'point_tier' => 'required|in:tier_1,tier_2,tier_3',
            'registration_fee' => 'nullable|numeric|min:0',
            'max_team_members' => 'nullable|integer|min:1|max:20',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['registration_fee'] = $validated['registration_fee'] ?? ($event->registration_fee ?: 150000);

        $category->update($validated);

        return redirect()->route('admin.competition-event.edit', $event->id)
            ->with('success', "Data cabang lomba '{$category->name}' berhasil diperbarui!");
    }

    /**
     * Remove a category from this event.
     */
    public function destroyCategory(CompetitionEvent $event, CompetitionCategory $category)
    {
        if ($category->competition_event_id !== $event->id) {
            abort(404);
        }

        $teamsCount = $category->teams()->count();
        if ($teamsCount > 0) {
            return redirect()->route('admin.competition-event.edit', $event->id)
                ->with('error', "Cabang lomba '{$category->name}' tidak dapat dihapus karena sudah ada {$teamsCount} regu peserta terdaftar. Silakan nonaktifkan statusnya jika tidak ingin dipertandingkan.");
        }

        $categoryName = $category->name . ' (' . $category->level . ' - ' . $category->gender_category . ')';
        $category->scores()->delete();
        $category->delete();

        return redirect()->route('admin.competition-event.edit', $event->id)
            ->with('success', "Cabang lomba '{$categoryName}' berhasil dihapus dari edisi ini.");
    }

    /**
     * Copy preset template categories (e.g. standard PMR competition set).
     */
    public function addPresetCategories(Request $request, CompetitionEvent $event)
    {
        $presetType = $request->input('preset_type');
        
        $standardList = [
            'mula' => [
                ['code' => 'LPP-MULA-PA', 'name' => 'Pertolongan Pertama', 'level' => 'Mula', 'gender_category' => 'Putra', 'scoring_type' => 'standard_time', 'point_tier' => 'tier_1'],
                ['code' => 'LPP-MULA-PI', 'name' => 'Pertolongan Pertama', 'level' => 'Mula', 'gender_category' => 'Putri', 'scoring_type' => 'standard_time', 'point_tier' => 'tier_1'],
                ['code' => 'LKTR-MULA-PA', 'name' => 'Ketangkasan Tandu Reguler', 'level' => 'Mula', 'gender_category' => 'Putra', 'scoring_type' => 'standard_time', 'point_tier' => 'tier_2'],
                ['code' => 'LKTR-MULA-PI', 'name' => 'Ketangkasan Tandu Reguler', 'level' => 'Mula', 'gender_category' => 'Putri', 'scoring_type' => 'standard_time', 'point_tier' => 'tier_2'],
                ['code' => 'LKCT-MULA', 'name' => 'Ketangkasan Cuci Tangan', 'level' => 'Mula', 'gender_category' => 'Umum', 'scoring_type' => 'standard_time', 'point_tier' => 'tier_2'],
                ['code' => 'MEWARNAI-MULA', 'name' => 'Lomba Mewarnai', 'level' => 'Mula', 'gender_category' => 'Umum', 'scoring_type' => 'multi_criteria', 'point_tier' => 'tier_2'],
                ['code' => 'MADING-MULA', 'name' => 'Mading Kreasi', 'level' => 'Mula', 'gender_category' => 'Umum', 'scoring_type' => 'multi_criteria', 'point_tier' => 'tier_1'],
            ],
            'madya' => [
                ['code' => 'LPP-MADYA-PA', 'name' => 'Pertolongan Pertama', 'level' => 'Madya', 'gender_category' => 'Putra', 'scoring_type' => 'written_practical_time', 'point_tier' => 'tier_1'],
                ['code' => 'LPP-MADYA-PI', 'name' => 'Pertolongan Pertama', 'level' => 'Madya', 'gender_category' => 'Putri', 'scoring_type' => 'written_practical_time', 'point_tier' => 'tier_1'],
                ['code' => 'LKTR-MADYA-PA', 'name' => 'Ketangkasan Tandu Reguler', 'level' => 'Madya', 'gender_category' => 'Putra', 'scoring_type' => 'standard_time', 'point_tier' => 'tier_1'],
                ['code' => 'LKTR-MADYA-PI', 'name' => 'Ketangkasan Tandu Reguler', 'level' => 'Madya', 'gender_category' => 'Putri', 'scoring_type' => 'standard_time', 'point_tier' => 'tier_1'],
                ['code' => 'LCT-MADYA', 'name' => 'Cepat Tepat', 'level' => 'Madya', 'gender_category' => 'Umum', 'scoring_type' => 'bracket_quiz', 'point_tier' => 'tier_2'],
                ['code' => 'OLIM-MADYA', 'name' => 'Olimpiade Kepalangmerahan', 'level' => 'Madya', 'gender_category' => 'Umum', 'scoring_type' => 'written_practical_time', 'point_tier' => 'tier_2'],
                ['code' => 'MADING-MADYA', 'name' => 'Mading Kreasi', 'level' => 'Madya', 'gender_category' => 'Umum', 'scoring_type' => 'multi_criteria', 'point_tier' => 'tier_2'],
                ['code' => 'FAV-MADYA', 'name' => 'PMR Favorite', 'level' => 'Madya', 'gender_category' => 'Umum', 'scoring_type' => 'social_engagement', 'point_tier' => 'tier_3'],
            ],
            'wira' => [
                ['code' => 'LPP-WIRA-PA', 'name' => 'Pertolongan Pertama', 'level' => 'Wira', 'gender_category' => 'Putra', 'scoring_type' => 'written_practical_time', 'point_tier' => 'tier_1'],
                ['code' => 'LPP-WIRA-PI', 'name' => 'Pertolongan Pertama', 'level' => 'Wira', 'gender_category' => 'Putri', 'scoring_type' => 'written_practical_time', 'point_tier' => 'tier_1'],
                ['code' => 'LKTR-WIRA-PA', 'name' => 'Ketangkasan Tandu Reguler', 'level' => 'Wira', 'gender_category' => 'Putra', 'scoring_type' => 'standard_time', 'point_tier' => 'tier_1'],
                ['code' => 'LKTR-WIRA-PI', 'name' => 'Ketangkasan Tandu Reguler', 'level' => 'Wira', 'gender_category' => 'Putri', 'scoring_type' => 'standard_time', 'point_tier' => 'tier_1'],
                ['code' => 'LCT-WIRA', 'name' => 'Cepat Tepat', 'level' => 'Wira', 'gender_category' => 'Umum', 'scoring_type' => 'bracket_quiz', 'point_tier' => 'tier_2'],
                ['code' => 'OLIM-WIRA', 'name' => 'Olimpiade Kepalangmerahan', 'level' => 'Wira', 'gender_category' => 'Umum', 'scoring_type' => 'written_practical_time', 'point_tier' => 'tier_2'],
                ['code' => 'MADING-WIRA', 'name' => 'Mading Kreasi', 'level' => 'Wira', 'gender_category' => 'Umum', 'scoring_type' => 'multi_criteria', 'point_tier' => 'tier_2'],
                ['code' => 'FAV-WIRA', 'name' => 'PMR Favorite', 'level' => 'Wira', 'gender_category' => 'Umum', 'scoring_type' => 'social_engagement', 'point_tier' => 'tier_3'],
            ]
        ];

        $toInsert = [];
        if ($presetType === 'mula_standard') {
            $toInsert = $standardList['mula'];
        } elseif ($presetType === 'madya_standard') {
            $toInsert = $standardList['madya'];
        } elseif ($presetType === 'wira_standard') {
            $toInsert = $standardList['wira'];
        } else {
            $toInsert = array_merge($standardList['mula'], $standardList['madya'], $standardList['wira']);
        }

        $addedCount = 0;
        $order = $event->categories()->max('order_position') ?? 0;
        foreach ($toInsert as $item) {
            $exists = $event->categories()
                ->where('level', $item['level'])
                ->where('name', $item['name'])
                ->where('gender_category', $item['gender_category'])
                ->exists();
            if (!$exists) {
                $order++;
                $event->categories()->create(array_merge($item, [
                    'order_position' => $order,
                    'registration_fee' => $event->registration_fee ?: 150000,
                    'is_active' => true,
                    'max_team_members' => 2,
                ]));
                $addedCount++;
            }
        }

        return redirect()->route('admin.competition-event.edit', $event->id)
            ->with('success', "Berhasil menambahkan {$addedCount} cabang lomba standar ke edisi ini!");
    }
}
