<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonationEvent;
use Illuminate\Http\Request;

class BloodDonationEventController extends Controller
{
    public function index()
    {
        $events = BloodDonationEvent::orderBy('event_date', 'desc')->get();
        return view('admin.blood-donation-events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.blood-donation-events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'time_start' => 'required',
            'time_end' => 'nullable',
            'location' => 'required|string|max:255',
            'target_bags' => 'nullable|integer',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|max:10240',
            'registration_link' => 'nullable|string|max:255',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['is_registration_link_active'] = $request->has('is_registration_link_active');

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('donor-banners', 'public');
        }

        // If this is set as active, deactivate others
        if ($data['is_active']) {
            BloodDonationEvent::where('id', '!=', 0)->update(['is_active' => false]);
        }

        BloodDonationEvent::create($data);

        return redirect()->route('admin.blood-donation-events.index')->with('success', 'Jadwal donor darah berhasil ditambahkan.');
    }

    public function edit(BloodDonationEvent $bloodDonationEvent)
    {
        return view('admin.blood-donation-events.edit', compact('bloodDonationEvent'));
    }

    public function update(Request $request, BloodDonationEvent $bloodDonationEvent)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'time_start' => 'required',
            'time_end' => 'nullable',
            'location' => 'required|string|max:255',
            'target_bags' => 'nullable|integer',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|max:10240',
            'registration_link' => 'nullable|string|max:255',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['is_registration_link_active'] = $request->has('is_registration_link_active');

        if ($request->hasFile('banner_image')) {
            if ($bloodDonationEvent->banner_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($bloodDonationEvent->banner_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($bloodDonationEvent->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('donor-banners', 'public');
        }

        if ($data['is_active']) {
            BloodDonationEvent::where('id', '!=', $bloodDonationEvent->id)->update(['is_active' => false]);
        }

        $bloodDonationEvent->update($data);

        return redirect()->route('admin.blood-donation-events.index')->with('success', 'Jadwal donor darah berhasil diperbarui.');
    }

    public function destroy(BloodDonationEvent $bloodDonationEvent)
    {
        $bloodDonationEvent->delete();
        return back()->with('success', 'Jadwal dihapus.');
    }
}
