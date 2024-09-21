<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{

    public function fetchAndStoreNews()
    {
        try {
            $apiKey = env('NEWS_API_KEY');
            $url = 'https://newsapi.org/v2/top-headlines?country=us&domains=techcrunch.com&from=2024-09-19&to=2024-09-21&apiKey=' . $apiKey;

            $response = Http::get($url);
            $newsData = $response->json();

            if ($response->successful() && isset($newsData['articles'])) {
                foreach ($newsData['articles'] as $article) {
                    // Convert the 'publishedAt' date to MySQL format
                    $publishedAt = Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s');

                    News::updateOrCreate(
                        ['title' => $article['title']], // Unique constraint
                        [
                            'description' => $article['description'],
                            'url' => $article['url'],
                            'source' => $article['source']['name'],
                            'published_at' => $publishedAt, // Save in MySQL format
                        ]
                    );
                }

                return response()->json(['message' => 'News fetched and stored successfully.'], 200);
            } else {
                return response()->json(['error' => 'Failed to fetch news. HTTP Status: ' . $response->status()], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching news: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

}
