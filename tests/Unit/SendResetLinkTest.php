<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Tests\TestCase;

class SendResetLinkTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_password_reset_link_successfully()
    {
        // Fake notifications so no real email is sent
        Notification::fake();

        // Create a user to send the reset link to
        $user = User::factory()->create(['email' => 'test@test.com']);

        // Call the route/method that sends the reset link
        $response = $this->postJson('/api/password/forgot', [
            'email' => 'test@test.com',
        ]);

        // Assert that the response status is 200 (success)
        $response->assertStatus(200);

        // Assert that the reset link notification was sent
        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
    }

    /** @test */
    public function it_fails_to_send_reset_link_when_email_is_invalid()
    {
        // Call the route/method with an invalid email
        $response = $this->postJson('/api/password/forgot', [
            'email' => 'invalid-email',
        ]);

        // Assert that the response status is 422 (validation error)
        $response->assertStatus(422);

        // Assert the validation error message is present
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_fails_to_send_reset_link_for_nonexistent_email()
    {
        // Call the route/method with a non-existent email
        $response = $this->postJson('/api/password/forgot', [
            'email' => 'nonexistent@noexisting.com',
        ]);

        // Assert that the response status is 400 (failure)
        $response->assertStatus(400);

        // Optionally, you can check the response message
        $response->assertJson([
            'message' => __('passwords.user'),
        ]);
    }
}
