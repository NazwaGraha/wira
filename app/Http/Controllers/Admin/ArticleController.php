<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $status = $request->query('status');

        $query = Article::with('category');

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status && in_array($status, ['published', 'draft'])) {
            $query->where('status', $status);
        }

        $articles = $query->latest()->paginate(10)->withQueryString();

        $totalCount = Article::count();
        $publishedCount = Article::where('status', 'published')->count();
        $draftCount = Article::where('status', 'draft')->count();
        $totalViews = Article::sum('views_count');

        return view('admin.articles.index', compact('articles', 'search', 'status', 'totalCount', 'publishedCount', 'draftCount', 'totalViews'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'thumbnail' => 'nullable|string',
            'author_name' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['user_id'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured');
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        if (empty($validated['thumbnail'])) {
            $validated['thumbnail'] = '/mockups/07_artikel.jpg';
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil disimpan dan dipublikasikan!');
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        $categories = Category::all();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'thumbnail' => 'nullable|string',
            'author_name' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        if ($validated['status'] === 'published' && !$article->published_at) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Artikel telah berhasil dihapus.');
    }
}
