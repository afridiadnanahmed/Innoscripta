<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserPreference;

class UserPreferenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Optionally run migrations if needed
        Artisan::call('migrate');
    }

    public function test_store_preferences()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/preferences', [
            'sources' => ['Example Source'],
            'categories' => ['Technology'],
            'authors' => ['John Doe'],
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'sources' => ['Example Source'],
                     'categories' => ['Technology'],
                     'authors' => ['John Doe'],
                 ]);
    }

    public function test_show_preferences()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        UserPreference::create([
            'user_id' => $user->id,
            'sources' => ['Example Source'],
            'categories' => ['Technology'],
            'authors' => ['John Doe'],
        ]);

        $response = $this->getJson('/api/preferences');

        $response->assertStatus(200)
                 ->assertJson([
                     'sources' => ['Example Source'],
                     'categories' => ['Technology'],
                     'authors' => ['John Doe'],
                 ]);
    }

    public function test_personalized_feed()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        UserPreference::create([
            'user_id' => $user->id,
            'sources' => ['Example Source'],
            'categories' => ['Technology'],
            'authors' => ['John Doe'],
        ]);

        $response = $this->getJson('/api/personalized-feed');

        $response->assertStatus(200);
        // Further assertions can be added based on expected data in the response
    }

        /**
     * Test fetching a personalized news feed based on user preferences.
     *
     * This test verifies that the personalized news feed endpoint returns articles
     * that match the user's preferences for sources, categories, and authors.
     * Articles that do not match the user's preferences should be excluded from
     * the response.
     *
     * @return void
     */
    public function test_personalized_feed_with_preferences()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        UserPreference::create([
            'user_id' => $user->id,
            'sources' => ['Example Source'],
            'categories' => ['Technology'],
            'authors' => ['John Doe'],
        ]);

        // Create articles that match and do not match the preferences
        News::factory()->create([
            'source' => 'Example Source',
            'category' => 'Technology',
            'author' => 'John Doe',
        ]);

        News::factory()->create([
            'source' => 'Another Source',
            'category' => 'Lifestyle',
            'author' => 'Jane Doe',
        ]);

        $response = $this->getJson('/api/personalized-feed');

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'source' => 'Example Source',
                    'category' => 'Technology',
                    'author' => 'John Doe',
                ])
                ->assertJsonMissing([
                    'source' => 'Another Source',
                    'category' => 'Lifestyle',
                    'author' => 'Jane Doe',
                ]);
    }

}
