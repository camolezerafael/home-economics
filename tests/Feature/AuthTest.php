<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->post('/sign-up', [
            'name' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'joao@exemplo.com']);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'joao@exemplo.com']);

        $response = $this->post('/sign-up', [
            'name' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'joao@exemplo.com',
            'password' => bcrypt('12345'),
        ]);

        $response = $this->post('/sign-in', [
            'email' => 'joao@exemplo.com',
            'password' => '12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'joao@exemplo.com', 'password' => bcrypt('12345')]);

        $response = $this->post('/sign-in', [
            'email' => 'joao@exemplo.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/sign-out');

        $response->assertRedirect('/sign-in');
        $this->assertGuest();
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->patch('/profile', [
            'name' => 'Novo Nome',
            'email' => 'novo@exemplo.com',
        ]);

        $response->assertRedirect('/profile');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Novo Nome', 'email' => 'novo@exemplo.com']);
    }

    public function test_user_can_delete_account_with_password_confirmation(): void
    {
        $user = User::factory()->create(['password' => bcrypt('12345')]);
        $this->actingAs($user);

        $response = $this->delete('/profile', [
            'password' => '12345',
        ]);

        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_rate_limiting_on_login(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/sign-in', [
                'email' => 'test@exemplo.com',
                'password' => 'wrong',
            ]);
        }

        $response = $this->post('/sign-in', [
            'email' => 'test@exemplo.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }
}
