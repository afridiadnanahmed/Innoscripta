<?php
namespace Tests\Feature;

use App\Models\News;
use App\Models\User; // Ensure User model is imported
use Database\Factories\NewsFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    /**
     * Test fetching paginated news articles.
     *
     * @return void
     */
    public function test_can_fetch_paginated_articles()
    {
        // Create some news articles
        News::factory()->count(25)->create();

        // Create a single user instance
        $user = User::factory()->create();

        // Simulate authenticated user
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/articles?per_page=10');

        // Assert the response status is OK
        $response->assertStatus(Response::HTTP_OK);

        // Assert the response has pagination information
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'url',
                    'source',
                    'published_at',
                    'created_at',
                    'updated_at',
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total'
        ]);

        // Assert that the response contains the correct number of articles
        $this->assertCount(10, $response->json('data'));
    }


    /**
     * Test that an authenticated user can fetch paginated articles.
     *
     * This test verifies that a user can log in and obtain an authentication token.
     * It then uses this token to access a protected endpoint that fetches articles
     * with pagination. The test ensures that the response status is 200 OK.
     *
     * Steps:
     * 1. Log in with valid credentials to obtain an authentication token.
     * 2. Use the obtained token to make a GET request to the `/api/news` endpoint with pagination.
     * 3. Assert that the response status is 200 OK.
     *
     * @return void
     */
    public function test_authenticated_user_can_fetch_articles()
    {
        // Log in with the created user and get the token
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password', // Ensure this matches the hashed password
        ]);

        // Debug the response to confirm the token's key
        $responseData = $response->json();

        // Retrieve the token from the correct key
        $token = $responseData['access_token'] ?? null;

        if (is_null($token)) {
            $this->fail('Failed to retrieve the authentication token. Response: ' . json_encode($responseData));
        }

        // Use the token to access the protected route
        $response = $this->withToken($token)
                        ->getJson('/api/articles?per_page=10');

        $response->assertStatus(200);
    }

    

    /**
     * Set up the test environment.
     *
     * This method is called before each test method is executed. It prepares the 
     * testing environment by creating a user with known credentials for use 
     * in authentication tests. This ensures that the user exists in the 
     * database and has a hashed password, which allows for reliable testing 
     * of authentication-related functionality.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'), // Ensure the password is hashed
        ]);
    }

    /**
     * Test that an authenticated user can search for articles based on various filters.
     *
     * This test verifies that an authenticated user can use the search functionality
     * to filter articles by keyword, category, source, and date. It ensures that the
     * search endpoint correctly returns articles that match the provided criteria and 
     * that the response contains the expected results.
     *
     * Steps:
     * 1. Create sample articles with different attributes for testing.
     * 2. Log in to obtain an authentication token.
     * 3. Test searching by keyword to ensure the correct articles are returned.
     * 4. Test searching by category to verify articles are filtered by category.
     * 5. Test searching by source to confirm articles are filtered by source.
     * 6. Test searching by date to check that articles are filtered by publication date.
     * 7. Assert that the response status is 200 OK and that the correct articles are present in the response.
     *
     * @return void
     */

    public function test_authenticated_user_can_search_articles()
    {
        // Create sample articles
        $article1 = News::factory()->create([
            'title' => 'Tech Innovations',
            'description' => 'Latest tech trends',
            'category' => 'Technology',
            'source' => 'Tech Source',
            'published_at' => '2024-09-21',
        ]);

        $article2 = News::factory()->create([
            'title' => 'Health Tips',
            'description' => 'Health and wellness tips',
            'category' => 'Health',
            'source' => 'Health Source',
            'published_at' => '2024-09-20',
        ]);

        // Log in and get the token
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $token = $response->json('access_token');

        // Search by keyword
        $response = $this->withToken($token)
                        ->getJson('/api/articles/search?keyword=Health');

        $response->assertStatus(200)
                ->assertJsonFragment(['title' => 'Health Tips']);

        // Search by category
        $response = $this->withToken($token)
                        ->getJson('/api/articles/search?category=Technology');

        $response->assertStatus(200)
                ->assertJsonFragment(['title' => 'Tech Innovations']);

        // Search by source
        $response = $this->withToken($token)
                        ->getJson('/api/articles/search?source=Tech Source');

        $response->assertStatus(200)
                ->assertJsonFragment(['title' => 'Tech Innovations']);

        // Search by date
        $response = $this->withToken($token)
                        ->getJson('/api/articles/search?date=2024-09-21');

        $response->assertStatus(200)
                ->assertJsonFragment(['title' => 'Tech Innovations']);
    }

    /**
     * Test retrieving a single article by ID.
     *
     * @return void
     */
    public function test_can_retrieve_single_article()
    {
        // Arrange: Create a user and authenticate using Sanctum
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    
        // Create a news article
        $article = News::factory()->create([
            'title' => 'Test Article',
            'description' => 'Test article description',
            'url' => 'https://example.com/test-article',
            'source' => 'Example Source',
            'category' => 'Technology',
            'published_at' => now(),
        ]);
    
        // Act: Make a GET request to the article endpoint
        $response = $this->getJson("/api/articles/{$article->id}");
        // dd($response->getContent());
        // Assert: Check if the response is OK and the article details are returned correctly
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $article->id,
                    'title' => 'Test Article',
                    'description' => 'Test article description',
                    'url' => 'https://example.com/test-article',
                    'source' => 'Example Source',
                    'category' => 'Technology',
                ]
            ]);

            $response->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'url',
                    'source',
                    'category',
                    'published_at',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * Test retrieving a non-existent article.
     *
     * @return void
     */
    public function test_retrieve_non_existent_article_returns_404()
    {
        // Arrange: Create a user and authenticate using Sanctum
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act: Make a GET request to a non-existent article
        $response = $this->getJson('/api/articles/999'); // Assuming this article ID does not exist

        // Assert: The response should return a 404 status code
        $response->assertStatus(404)
             ->assertJson([
                 'message' => 'Article not found',
             ]);
    }

}
