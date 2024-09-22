<?php
namespace Tests\Feature;

use App\Models\News;
use App\Models\User; // Ensure User model is imported
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
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

}
