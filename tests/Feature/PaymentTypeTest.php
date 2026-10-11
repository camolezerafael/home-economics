<?php

namespace Tests\Feature;

use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTypeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_user_can_create_payment_type(): void
    {
        $response = $this->post('/payment_type', [
            'name' => 'PIX',
            'description' => 'Pagamento instantâneo',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payment_types', [
            'user_id' => $this->user->id,
            'name' => 'PIX',
        ]);
    }

    public function test_user_cannot_create_duplicate_payment_type(): void
    {
        PaymentType::factory()->create(['user_id' => $this->user->id, 'name' => 'PIX']);

        $response = $this->post('/payment_type', [
            'name' => 'PIX',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_different_users_can_have_same_payment_type_name(): void
    {
        $otherUser = User::factory()->create();
        PaymentType::factory()->create(['user_id' => $this->user->id, 'name' => 'PIX']);

        $response = $this->actingAs($otherUser)->post('/payment_type', [
            'name' => 'PIX',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('payment_types', 2);
    }

    public function test_first_for_user_returns_first_by_name(): void
    {
        PaymentType::factory()->create(['user_id' => $this->user->id, 'name' => 'Boleto']);
        PaymentType::factory()->create(['user_id' => $this->user->id, 'name' => 'Cartão']);
        PaymentType::factory()->create(['user_id' => $this->user->id, 'name' => 'PIX']);

        $first = PaymentType::firstForUser($this->user->id);

        $this->assertEquals('Boleto', $first->name);
    }
}
