<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of article categories.
     */
    public function index()
    {
        $categories = Category::withCount('articles')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
            'color' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan nama tersebut sudah ada.',
            'slug.unique' => 'Slug kategori sudah digunakan.',
        ]);

        $baseSlug = Str::slug($request->filled('slug') ? $request->input('slug') : $validated['name']);
        if (empty($baseSlug)) {
            $baseSlug = 'kategori-' . Str::random(5);
        }

        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;

        if (empty($validated['color'])) {
            $validated['color'] = '#980000';
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan ke database!");
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $kategori_artikel)
    {
        $category = $kategori_artikel;

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:100|unique:categories,slug,' . $category->id,
            'color' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan nama tersebut sudah ada.',
            'slug.unique' => 'Slug kategori sudah digunakan.',
        ]);

        if ($request->filled('slug') && $request->input('slug') !== $category->slug) {
            $baseSlug = Str::slug($request->input('slug'));
            $slug = $baseSlug;
            $counter = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        if (empty($validated['color'])) {
            $validated['color'] = '#980000';
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', "Kategori '{$category->name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $kategori_artikel)
    {
        $category = $kategori_artikel;
        $name = $category->name;
        $articleCount = $category->articles()->count();

        $category->delete();

        $msg = "Kategori '{$name}' telah berhasil dihapus.";
        if ($articleCount > 0) {
            $msg .= " ({$articleCount} artikel terkait otomatis diatur tanpa kategori).";
        }

        return redirect()->route('admin.categories.index')->with('success', $msg);
    }

    /**
     * AJAX endpoint to quickly create a category from the article form modal.
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'color' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan nama ini sudah terdaftar.',
        ]);

        $baseSlug = Str::slug($validated['name']);
        if (empty($baseSlug)) {
            $baseSlug = 'kategori-' . Str::random(5);
        }

        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;
        $validated['color'] = $validated['color'] ?: '#980000';

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Kategori '{$category->name}' berhasil ditambahkan!",
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'color' => $category->color,
            ],
        ]);
    }
}
