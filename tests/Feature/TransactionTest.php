<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Category;
use App\Models\FromTo;
use App\Models\PaymentType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected AccountType $accountType;

    protected Account $account1;

    protected Account $account2;

    protected Category $category;

    protected FromTo $fromTo;

    protected PaymentType $paymentType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->accountType = AccountType::factory()->create(['user_id' => $this->user->id]);
        $this->account1 = Account::factory()->create([
            'user_id' => $this->user->id,
            'type_id' => $this->accountType->id,
            'decimal_precision' => 2,
        ]);
        $this->account2 = Account::factory()->create([
            'user_id' => $this->user->id,
            'type_id' => $this->accountType->id,
            'decimal_precision' => 2,
        ]);
        $this->category = Category::factory()->create(['user_id' => $this->user->id]);
        $this->fromTo = FromTo::factory()->create(['user_id' => $this->user->id, 'type' => FromTo::TYPE_TO]);
        $this->paymentType = PaymentType::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
    }

    public function test_user_can_create_receita(): void
    {
        $response = $this->post('/transaction', [
            'account_id' => $this->account1->id,
            'category_id' => $this->category->id,
            'from_to_id' => $this->fromTo->id,
            'payment_type_id' => $this->paymentType->id,
            'transaction_type' => 'RECEI',
            'description' => 'Salário',
            'amount' => '1500.00',
            'date_due' => now()->format('Y-m-d'),
            'status' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'RECEI',
            'description' => 'Salário',
            'amount' => 150000, // 1500.00 * 100
            'status' => false,
        ]);
    }

    public function test_user_can_create_transfer_dual_transaction(): void
    {
        $response = $this->post('/transaction', [
            'account_id' => $this->account1->id,
            'account_to_id' => $this->account2->id,
            'category_id' => $this->category->id,
            'from_to_id' => $this->fromTo->id,
            'payment_type_id' => $this->paymentType->id,
            'transaction_type' => 'TRANS',
            'description' => 'Reserva',
            'amount' => '500.00',
            'date_due' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();

        // Check outgoing transaction (negative amount)
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'TRANS',
            'description' => 'Reserva',
            'amount' => -50000, // -500.00 * 100
            'status' => true,
        ]);

        // Check incoming transaction (positive amount)
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'account_id' => $this->account2->id,
            'transaction_type' => 'TRANS',
            'description' => 'Reserva',
            'amount' => 50000, // 500.00 * 100
            'status' => true,
        ]);
    }

    public function test_user_cannot_create_transfer_with_same_account(): void
    {
        $response = $this->post('/transaction', [
            'account_id' => $this->account1->id,
            'account_to_id' => $this->account1->id,
            'transaction_type' => 'TRANS',
            'description' => 'Teste',
            'amount' => '100.00',
            'date_due' => now()->format('Y-m-d'),
            'payment_type_id' => $this->paymentType->id,
        ]);

        $response->assertSessionHasErrors('account_to_id');
    }

    public function test_user_can_toggle_status(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'FIXEX',
            'status' => false,
            'date_payment' => null,
        ]);

        $response = $this->post("/transaction/{$transaction->id}/status");

        $response->assertJson([
            'status' => true,
        ]);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => true,
        ]);
        $this->assertNotNull($transaction->fresh()->date_payment);
    }

    public function test_user_can_untoggle_status(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'FIXEX',
            'status' => true,
            'date_payment' => now(),
        ]);

        $response = $this->post("/transaction/{$transaction->id}/status");

        $response->assertJson([
            'status' => false,
        ]);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => false,
        ]);
        $this->assertNull($transaction->fresh()->date_payment);
    }

    public function test_user_cannot_toggle_transfer_status(): void
    {
        $transfer = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'TRANS',
            'status' => true,
        ]);

        $response = $this->post("/transaction/{$transfer->id}/status");

        $response->assertJson(['status' => true]); // Should remain unchanged
        $this->assertDatabaseHas('transactions', [
            'id' => $transfer->id,
            'status' => true,
        ]);
    }

    public function test_get_full_balance_calculates_correctly(): void
    {
        // Create test data
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'RECEI',
            'amount' => '5000.00',
            'status' => true,
            'date_due' => now(),
        ]);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'FIXEX',
            'amount' => '-2000.00',
            'status' => true,
            'date_due' => now(),
        ]);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'TRANS',
            'amount' => '-500.00',
            'status' => true,
            'date_due' => now(),
        ]);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account2->id,
            'transaction_type' => 'TRANS',
            'amount' => '500.00',
            'status' => true,
            'date_due' => now(),
        ]);
        $this->account1->update(['initial_balance' => '1000.00']);

        $month = now()->format('Y-m');
        $balance = Transaction::getFullBalance($month);

        $this->assertEqualsWithDelta(3500.00, $balance['month_balance'], 0.01); // 5000 - 2000 + 500 - 500
        $this->assertEqualsWithDelta(4500.00, $balance['final_balance'], 0.01); // 3500 + 1000
        $this->assertEqualsWithDelta(4500.00, $balance['final_balance_transfer'], 0.01); // 4500 + 0
    }

    public function test_user_isolated_from_other_users_transactions(): void
    {
        $otherUser = User::factory()->create();
        $otherType = AccountType::factory()->create(['user_id' => $otherUser->id]);
        $otherAccount = Account::factory()->create(['user_id' => $otherUser->id, 'type_id' => $otherType->id]);

        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'description' => 'Minha', 'transaction_type' => 'RECEI', 'date_due' => now()]);
        Transaction::factory()->create(['user_id' => $otherUser->id, 'account_id' => $otherAccount->id, 'description' => 'Outro', 'transaction_type' => 'RECEI', 'date_due' => now()]);

        $response = $this->get('/transaction');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('items.RECEI.data', 1)
            ->where('items.RECEI.data.0.description', 'Minha'));
    }
}
