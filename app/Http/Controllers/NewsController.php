<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{

    /**
     * Fetch news from The NewsAPI and store it in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchNewsFromNewsApi()
    {
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => env('NEWS_API_KEY'),
            'country' => 'us',
        ]);
    
        $articles = $response->json('articles');
    
        foreach ($articles as $article) {
            News::updateOrCreate(
                ['url' => $article['url']],
                [
                    'title' => $article['title'],
                    'author' => $article['author'] ?? 'Unknown', // Add author
                    'description' => $article['description'] ?? "",
                    'source' => $article['source']['name'],
                    'published_at' => Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s'),
                    'category' => $article['category'] ?? 'General', // Add category with default
                ]
            );
        }
    }
    

    /**
     * Fetch news from The New York Times API and store it in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchNewsFromNYT()
    {
        $response = Http::get('https://api.nytimes.com/svc/topstories/v2/home.json', [
            'api-key' => env('NYT_API_KEY'),
        ]);
    
        $articles = $response->json('results');
    
        foreach ($articles as $article) {
            News::updateOrCreate(
                ['url' => $article['url']],
                [
                    'title' => $article['title'],
                    'author' => $article['byline'] ?? 'Unknown', // Add author with default
                    'description' => $article['abstract'] ?? "",
                    'source' => 'NYT',
                    'published_at' => Carbon::parse($article['published_date'])->format('Y-m-d H:i:s'),
                    'category' => $article['section'] ?? 'General', // Add category with default
                ]
            );
        }
    }
    

}
