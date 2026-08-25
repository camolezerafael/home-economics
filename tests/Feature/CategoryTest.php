<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_user_can_create_valid_category(): void
    {
        $response = $this->post('/category', [
            'name' => 'Alimentação',
            'description' => 'Gastos com comida e mercado',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'user_id' => $this->user->id,
            'name' => 'Alimentação',
            'description' => 'Gastos com comida e mercado',
        ]);
    }

    public function test_user_cannot_create_category_with_empty_name(): void
    {
        $response = $this->post('/category', [
            'name' => '',
            'description' => 'Test',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_cannot_create_category_with_short_description(): void
    {
        $response = $this->post('/category', [
            'name' => 'Transporte',
            'description' => 'ab',
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_user_can_create_category_with_empty_description(): void
    {
        $response = $this->post('/category', [
            'name' => 'Lazer',
            'description' => '',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'user_id' => $this->user->id,
            'name' => 'Lazer',
            'description' => null,
        ]);
    }

    public function test_user_can_edit_category(): void
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Alimentação',
            'description' => 'Comida',
        ]);

        $response = $this->patch("/category/{$category->id}", [
            'name' => 'Comida',
            'description' => 'Restaurantes e mercado',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Comida',
            'description' => 'Restaurantes e mercado',
        ]);
    }

    public function test_user_cannot_edit_category_with_invalid_description(): void
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->patch("/category/{$category->id}", [
            'name' => 'Transporte',
            'description' => 'xy',
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_user_can_delete_category_without_transactions(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete("/category/{$category->id}");

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_user_cannot_delete_category_with_transactions(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        \App\Models\Transaction::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id]);

        $response = $this->delete("/category/{$category->id}");

        $response->assertStatus(409);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
