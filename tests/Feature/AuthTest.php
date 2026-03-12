<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_registration_requires_email_verification_before_login()
    {
        Notification::fake();

        $response = $this->post(route('register.client.post'), [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'phone' => '+12345678901',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertGuest();

        $user = User::where('email', 'alice@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        // even with correct credentials, login should be denied until verification
        $login = $this->post(route('login.post'), [
            'email' => 'alice@example.com',
            'password' => 'password',
            'remember' => false,
        ]);
        $login->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_unverified_users_cannot_login()
    {
        // create an unverified user manually
        $user = User::create([
            'name' => 'Unverified Client',
            'email' => 'unverified@example.com',
            'phone' => '+22890000001',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'client',
            'email_verified_at' => null,
        ]);

        // try to log in with this unverified account
        $response = $this->post(route('login.post'), [
            'email' => 'unverified@example.com',
            'password' => 'password123',
            'remember' => false,
        ]);

        // should be rejected with an email verification error
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_delete_notification()
    {
        $user = User::factory()->create();
        $user->notifications()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Test', 'action_url' => '/'],
            'read_at' => null,
        ]);

        $this->actingAs($user);
        $notif = $user->notifications()->first();

        $response = $this->delete(route('notifications.destroy', $notif->id));
        $response->assertNoContent();
        $this->assertNull($user->notifications()->find($notif->id));
    }

}
