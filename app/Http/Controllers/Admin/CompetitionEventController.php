<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompetitionEventController extends Controller
{
    /**
     * Display the competition event settings form.
     */
    public function index()
    {
        $event = CompetitionEvent::where('is_active', true)->first();
        if (!$event) {
            $event = CompetitionEvent::first();
        }

        if (!$event) {
            $event = CompetitionEvent::create([
                'title' => 'SUA BHAKTI BERKARYA III TAHUN 2025',
                'slug' => 'sua-bhakti-berkarya-iii-2025',
                'theme' => 'AJANG PRESTASI RELAWAN MUDA PMR WIRA CIAWI',
                'start_date' => '2026-10-15',
                'end_date' => '2026-10-16',
                'location' => 'Kampus SMAN 1 Ciawi Bogor',
                'description' => 'Ajang kompetisi kepalangmerahan bergengsi tingkat Mula (SD), Madya (SMP), dan Wira (SMA/SMK/MA) se-Jabodetabek dan sekitarnya.',
                'registration_fee' => 150000,
                'bank_name' => 'Bank BCA',
                'bank_account_number' => '1234567890',
                'bank_account_holder' => 'PMR WIRA SMAN 1 CIAWI',
                'is_active' => true,
                'is_registration_open' => true,
            ]);
        }

        return view('admin.competition.event.index', compact('event'));
    }

    /**
     * Update the competition event settings.
     */
    public function update(Request $request)
    {
        $event = CompetitionEvent::where('is_active', true)->first();
        if (!$event) {
            $event = CompetitionEvent::first();
        }

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

        $event->update($validated);

        return redirect()->route('admin.competition-event.index')
            ->with('success', 'Pengaturan informasi event SUA BHAKTI BERKARYA berhasil disimpan!');
    }
}
