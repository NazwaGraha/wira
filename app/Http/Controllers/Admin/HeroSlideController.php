<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('order_position')->get();
        return view('admin.hero.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.hero.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_path' => 'required|string|max:500',
            'caption' => 'nullable|string|max:500',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['order_position'] = $validated['order_position'] ?? (HeroSlide::max('order_position') + 1);
        $validated['is_active'] = $request->has('is_active');

        HeroSlide::create($validated);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Gambar slider banner berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $slide = HeroSlide::findOrFail($id);
        return view('admin.hero.edit', compact('slide'));
    }

    public function update(Request $request, $id)
    {
        $slide = HeroSlide::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_path' => 'required|string|max:500',
            'caption' => 'nullable|string|max:500',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $slide->update($validated);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Gambar slider banner berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', 'Gambar slider banner berhasil dihapus.');
    }
}
