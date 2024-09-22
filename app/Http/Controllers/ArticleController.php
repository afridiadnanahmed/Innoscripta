<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Retrieve a single article by its ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Fetch the article by ID
        $article = News::find($id);

        // If article not found, return a 404 error
        if (!$article) {
            return response()->json([
                'message' => 'Article not found',
            ], 404);
        }

        // Return the article details in the response
        return response()->json([
            'data' => $article,
        ], 200);
    }

    /**
     * Fetch a personalized news feed based on user preferences.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function personalizedFeed()
    {
        $user = Auth::user();
        $preferences = null;

        if ($user) {
            // Fetch user preferences
            $preferences = UserPreference::where('user_id', $user->id)->first();
        }

        $query = News::query();

        // Apply user preferences if available
        if ($preferences) {
            if ($preferences->sources) {
                $query->whereIn('source', $preferences->sources);
            }

            if ($preferences->categories) {
                $query->whereIn('category', $preferences->categories);
            }

            if ($preferences->authors) {
                $query->whereIn('author', $preferences->authors); // Ensure your Article model has an `author` field
            }
        }

        $articles = $query->get();

        return response()->json(['data' => $articles]);
    }
}
