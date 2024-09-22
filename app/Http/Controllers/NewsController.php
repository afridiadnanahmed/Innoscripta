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
                            'description' => $article['description']??"",
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

    /**
     * Fetch news from The New York Times API and store it in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchNewsFromNYT()
    {
        try {
            $apiKey = env('NYT_API_KEY');
            $url = 'https://api.nytimes.com/svc/topstories/v2/us.json?api-key=' . $apiKey;

            // Send GET request to NYT API
            $response = Http::get($url);
            $newsData = $response->json();

            if ($response->successful() && isset($newsData['results'])) {
                foreach ($newsData['results'] as $article) {
                    // Convert 'published_date' to MySQL format
                    $publishedAt = Carbon::parse($article['published_date'])->format('Y-m-d H:i:s');

                    // Save article to the database
                    News::updateOrCreate(
                        ['title' => $article['title']], // Unique constraint
                        [
                            'description' => $article['abstract'], // Use 'abstract' for short description
                            'url' => $article['url'],
                            'source' => 'The New York Times',
                            'published_at' => $publishedAt, // Save in MySQL datetime format
                        ]
                    );
                }

                return response()->json(['message' => 'NYT news fetched and stored successfully.'], 200);
            } else {
                return response()->json(['error' => 'Failed to fetch news from NYT. HTTP Status: ' . $response->status()], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching NYT news: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

}
