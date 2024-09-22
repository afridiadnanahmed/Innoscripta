<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sources' => 'array|nullable',
            'categories' => 'array|nullable',
            'authors' => 'array|nullable',
        ]);

        $user = Auth::user();

        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['sources', 'categories', 'authors'])
        );

        return response()->json($preferences);
    }

    public function show()
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        return response()->json($preferences);
    }

    public function personalizedFeed()
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        if (!$preferences) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        $query = News::query();

        if ($preferences->sources) {
            $query->whereIn('source', $preferences->sources);
        }

        if ($preferences->categories) {
            $query->whereIn('category', $preferences->categories);
        }

        if ($preferences->authors) {
            $query->whereIn('author', $preferences->authors); // Ensure your News model has an `author` field
        }

        $news = $query->get();

        return response()->json($news);
    }
}
