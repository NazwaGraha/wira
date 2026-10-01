<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use Illuminate\Http\Request;

class CompetitionFeeController extends Controller
{
    public function index(Request $request)
    {
        $event = CompetitionEvent::where('is_active', true)->first();
        if (!$event) {
            $event = CompetitionEvent::first();
        }

        $level = $request->query('level');

        $query = CompetitionCategory::where('competition_event_id', $event?->id)
            ->orderBy('order_position');

        if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
            $query->where('level', $level);
        }

        $categories = $query->get();
        $categoriesByLevel = $categories->groupBy('level');

        return view('admin.competition.fees.index', compact('event', 'categories', 'categoriesByLevel', 'level'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'fees' => 'required|array',
            'fees.*' => 'required|numeric|min:0',
            'event_registration_fee' => 'nullable|numeric|min:0',
        ]);

        $event = CompetitionEvent::where('is_active', true)->first();
        if ($event && isset($validated['event_registration_fee'])) {
            $event->update([
                'registration_fee' => $validated['event_registration_fee'],
            ]);
        }

        $updatedCount = 0;
        foreach ($validated['fees'] as $catId => $fee) {
            $category = CompetitionCategory::find($catId);
            if ($category) {
                $category->update([
                    'registration_fee' => $fee,
                ]);
                $updatedCount++;
            }
        }

        return redirect()->back()->with('success', "Biaya pendaftaran untuk {$updatedCount} cabang lomba berhasil diperbarui!");
    }
}
