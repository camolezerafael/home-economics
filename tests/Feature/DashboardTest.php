<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected AccountType $accountType;

    protected Account $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->accountType = AccountType::factory()->create(['user_id' => $this->user->id]);
        $this->account = Account::factory()->create([
            'user_id' => $this->user->id,
            'type_id' => $this->accountType->id,
            'decimal_precision' => 2,
            'initial_balance' => '1000.00',
        ]);
        $this->actingAs($this->user);
    }

    public function test_dashboard_shows_correct_totals(): void
    {
        // Create test transactions for current month
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'transaction_type' => 'RECEI',
            'amount' => '5000.00',
            'status' => true,
            'date_due' => now(),
        ]);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'transaction_type' => 'FIXEX',
            'amount' => '-2000.00',
            'status' => true,
            'date_due' => now(),
        ]);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'transaction_type' => 'VAREX',
            'amount' => '-1000.00',
            'status' => true,
            'date_due' => now(),
        ]);

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('totals.balance')
            ->has('totals.incoming')
            ->has('totals.fixed')
            ->has('totals.variable')
            ->has('graphBalance')
            ->has('graphIncoming')
            ->has('graphOutgoing')
            ->has('graphTransfers')
        );
    }

    public function test_dashboard_only_shows_paid_by_default(): void
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'transaction_type' => 'RECEI',
            'amount' => '3000.00',
            'status' => true,
            'date_due' => now(),
        ]);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'transaction_type' => 'RECEI',
            'amount' => '2000.00', // (pending)
            'status' => false,
            'date_due' => now(),
        ]);

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('totals.incoming', 3000) // Only paid (3000), not 5000
        );
    }

    public function test_calculate_percentage_edge_cases(): void
    {
        $dashboard = new \App\Models\Dashboard;

        // last = 0, current > 0 -> 999%
        $this->assertEquals('+999%', $dashboard->calculatePercentage(100, 0));

        // last > 0, current = 0 -> -100%
        $this->assertEquals('-100%', $dashboard->calculatePercentage(0, 500));

        // Normal case
        $this->assertEquals('+50%', $dashboard->calculatePercentage(150, 100));
        $this->assertEquals('-50%', $dashboard->calculatePercentage(50, 100));

        // Both zero -> 0%
        $this->assertEquals('0%', $dashboard->calculatePercentage(0, 0));
    }

    public function test_dashboard_isolated_by_user(): void
    {
        $otherUser = User::factory()->create();
        $otherType = AccountType::factory()->create(['user_id' => $otherUser->id]);
        $otherAccount = Account::factory()->create(['user_id' => $otherUser->id, 'type_id' => $otherType->id]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'transaction_type' => 'RECEI',
            'amount' => '1000.00',
            'status' => true,
            'date_due' => now(),
        ]);
        Transaction::factory()->create([
            'user_id' => $otherUser->id,
            'account_id' => $otherAccount->id,
            'transaction_type' => 'RECEI',
            'amount' => '5000.00',
            'date_due' => now(),
        ]);

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('totals.incoming', 1000) // Only user's data
        );
    }
}
