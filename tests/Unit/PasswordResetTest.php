<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resets_password_successfully()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        // Create a password reset token
        $token = Password::createToken($user);

        // Send password reset request
        $response = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        // Log the response for debugging
        $response->dump();

        // Check the response status
        $response->assertStatus(200);

        // Verify the password has been reset
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword', $user->password));
    }

    /** @test */
    public function it_fails_to_reset_password_when_email_is_invalid()
    {
        // Send password reset request with an invalid email
        $response = $this->postJson('/api/password/reset', [
            'email' => 'invalid@example.com',
            'token' => Str::random(60),
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        // Log the response for debugging
        $response->dump();

        // Check the response status
        $response->assertStatus(400);
        $response->assertJson([
            'message' => __('passwords.user'),
        ]);
    }

    /** @test */
    public function it_fails_to_reset_password_when_token_is_invalid()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        // Send password reset request with an invalid token
        $response = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => 'invalidtoken',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        // Log the response for debugging
        $response->dump();

        // Check the response status
        $response->assertStatus(400);
        $response->assertJson([
            'message' => __('passwords.token'),
        ]);
    }

    /** @test */
    public function it_fails_to_reset_password_when_password_confirmation_does_not_match()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        // Create a password reset token
        $token = Password::createToken($user);

        // Send password reset request with mismatched password confirmation
        $response = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'differentpassword',
        ]);

        // Log the response for debugging
        $response->dump();

        // Check the response status
        $response->assertStatus(422); // Validation error
        $response->assertJsonValidationErrors('password');
    }
}
