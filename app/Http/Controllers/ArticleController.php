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
}
