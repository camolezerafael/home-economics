<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected AccountType $accountType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->accountType = AccountType::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
    }

    public function test_user_can_list_accounts(): void
    {
        Account::factory()->count(3)->create(['user_id' => $this->user->id, 'type_id' => $this->accountType->id]);

        $response = $this->get('/account');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->has('items.data', 3));
    }

    public function test_user_can_create_account(): void
    {
        $response = $this->post('/account', [
            'name' => 'Minha Conta',
            'description' => 'Conta Corrente Principal',
            'initial_balance' => '1500.50',
            'decimal_precision' => 2,
            'type_id' => $this->accountType->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('accounts', [
            'user_id' => $this->user->id,
            'name' => 'Minha Conta',
            'initial_balance' => 150050, // 1500.50 * 100
            'decimal_precision' => 2,
        ]);
    }

    public function test_user_can_create_crypto_account_with_8_precision(): void
    {
        $cryptoType = AccountType::factory()->create(['user_id' => $this->user->id, 'name' => 'Bitcoin Wallet']);

        $response = $this->post('/account', [
            'name' => 'BTC Wallet',
            'description' => 'Carteira Bitcoin',
            'initial_balance' => '0.00012345',
            'decimal_precision' => 8,
            'type_id' => $cryptoType->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('accounts', [
            'user_id' => $this->user->id,
            'name' => 'BTC Wallet',
            'initial_balance' => 12345, // 0.00012345 * 10^8
            'decimal_precision' => 8,
        ]);
    }

    public function test_user_can_edit_account(): void
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'type_id' => $this->accountType->id,
            'initial_balance' => 100000,
        ]);

        $response = $this->patch("/account/{$account->id}", [
            'name' => 'Conta Editada',
            'description' => 'Nova descrição',
            'initial_balance' => '2500.75',
            'decimal_precision' => 2,
            'type_id' => $this->accountType->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Conta Editada',
            'initial_balance' => 250075,
        ]);
    }

    public function test_user_can_delete_account_without_transactions(): void
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'type_id' => $this->accountType->id,
        ]);

        $response = $this->delete("/account/{$account->id}");

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }

    public function test_user_cannot_delete_account_with_transactions(): void
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'type_id' => $this->accountType->id,
        ]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $account->id]);

        $response = $this->delete("/account/{$account->id}");

        $response->assertStatus(409);
        $response->assertJsonFragment(['message' => 'Existem itens registrados usando esta conta. Esta informação não pode ser deletada.']);
        $this->assertDatabaseHas('accounts', ['id' => $account->id]);
    }

    public function test_user_cannot_access_other_user_account(): void
    {
        $otherUser = User::factory()->create();
        $otherType = AccountType::factory()->create(['user_id' => $otherUser->id]);
        $otherAccount = Account::factory()->create([
            'user_id' => $otherUser->id,
            'type_id' => $otherType->id,
        ]);

        $response = $this->delete("/account/{$otherAccount->id}");

        $response->assertNotFound();
    }
}
