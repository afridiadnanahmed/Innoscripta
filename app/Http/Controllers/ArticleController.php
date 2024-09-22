<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
        /**
     * Fetch a paginated list of news articles.
     *
     * This endpoint retrieves a paginated list of articles from the 'news' table.
     * The number of articles per page can be specified with the 'per_page' query parameter.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaginatedArticles(Request $request)
    {
        // Fetch news articles with pagination
        $news = News::paginate($request->input('per_page', 15));

        return response()->json($news);
    }

    /**
     * Fetch articles with optional filters for keyword, date, category, and source.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = News::query();

        // Filter by keyword
        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->input('keyword') . '%')
                  ->orWhere('description', 'like', '%' . $request->input('keyword') . '%');
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('published_at', $request->input('date'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        // Paginate results
        $articles = $query->paginate($request->input('per_page', 10));

        return response()->json($articles);
    }
}
