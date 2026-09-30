<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $categorySlug = $request->query('kategori');

        $query = Article::with('category')->where('status', 'published');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($categorySlug) {
            $query->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Featured article on top of list
        $featuredArticle = null;
        if (!$search && !$categorySlug) {
            $featuredArticle = Article::with('category')
                ->where('status', 'published')
                ->where('is_featured', true)
                ->latest('published_at')
                ->first();
        }

        $articles = $query->when($featuredArticle, fn($q) => $q->where('id', '!=', $featuredArticle->id))
            ->latest('published_at')
            ->paginate(6)
            ->withQueryString();

        $categories = Category::withCount(['articles' => function($q) {
            $q->where('status', 'published');
        }])->get();

        $popularArticles = Article::where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->take(4)
            ->get();

        return view('pages.articles.index', compact('articles', 'featuredArticle', 'categories', 'popularArticles', 'search', 'categorySlug'));
    }

    public function show($slug)
    {
        $article = Article::with(['category', 'user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment view count
        $article->increment('views_count');

        $relatedArticles = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('status', 'published')
            ->take(3)
            ->get();

        $categories = Category::withCount(['articles' => function($q) {
            $q->where('status', 'published');
        }])->get();

        return view('pages.articles.show', compact('article', 'relatedArticles', 'categories'));
    }
}
