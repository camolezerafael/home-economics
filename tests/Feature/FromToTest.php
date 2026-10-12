<?php

namespace Tests\Feature;

use App\Models\FromTo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FromToTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_user_can_create_from_to(): void
    {
        $response = $this->post('/from_to', [
            'name' => 'Mercado Central',
            'description' => 'Supermercado',
            'type' => FromTo::TYPE_FRM,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('from_to', [
            'user_id' => $this->user->id,
            'name' => 'Mercado Central',
            'type' => FromTo::TYPE_FRM,
        ]);
    }

    public function test_user_cannot_create_from_to_with_invalid_type(): void
    {
        $response = $this->post('/from_to', [
            'name' => 'Teste',
            'type' => 'INVALID',
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_user_cannot_create_from_to_with_legacy_spec_types(): void
    {
        foreach (['PESS', 'ESTB', 'OUTR'] as $type) {
            $response = $this->post('/from_to', [
                'name' => "Test {$type}",
                'type' => $type,
            ]);

            $response->assertSessionHasErrors('type');
        }

        $this->assertDatabaseCount('from_to', 0);
    }

    public function test_user_can_create_from_to_with_all_types(): void
    {
        foreach ([FromTo::TYPE_FRM, FromTo::TYPE_TO, FromTo::TYPE_FT] as $type) {
            $response = $this->post('/from_to', [
                'name' => "Test {$type}",
                'type' => $type,
            ]);
            $response->assertRedirect();
        }

        $this->assertDatabaseCount('from_to', 3);
    }

    public function test_factory_generates_names_that_fit_the_column(): void
    {
        for ($i = 0; $i < 300; $i++) {
            $this->assertLessThanOrEqual(30, mb_strlen(FromTo::factory()->make()->name));
        }
    }

    public function test_user_can_delete_from_to_without_transactions(): void
    {
        $fromTo = FromTo::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete("/from_to/{$fromTo->id}");

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('from_to', ['id' => $fromTo->id]);
    }
}
