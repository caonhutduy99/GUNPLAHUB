<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordAndOrderEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_request_a_password_reset_email(): void
    {
        $user = User::factory()->create();
        Notification::fake();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_password_reset_page_is_available_from_a_reset_link(): void
    {
        $response = $this->get(route('password.reset', ['token' => 'test-token', 'email' => 'customer@example.com']));

        $response->assertOk()->assertSee('Đặt lại mật khẩu');
    }

    public function test_customer_can_register_with_a_valid_password(): void
    {
        $this->post(route('register'), [
            'full_name' => 'Khách hàng mới',
            'email' => 'new-customer@example.com',
            'phone' => '0912345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-customer@example.com',
            'role' => 'customer',
        ]);
    }
}
