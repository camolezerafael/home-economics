<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Dashboard;
use App\Models\PaymentType;
use App\Models\Transaction;
use App\Models\TransactionReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Prova que os caminhos otimizados (snapshot único) produzem os mesmos valores
 * que os métodos individuais, e que o número de queries é constante.
 */
class BalanceSnapshotEquivalenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    /** @var array<int, Account> */
    protected array $accounts = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $type = AccountType::factory()->create(['user_id' => $this->user->id]);
        $paymentType = PaymentType::factory()->create(['user_id' => $this->user->id]);

        foreach ([['Conta A', '1000.00'], ['Conta B', '250.50'], ['Conta C', '0']] as [$name, $initial]) {
            $this->accounts[] = Account::factory()->create([
                'user_id' => $this->user->id,
                'type_id' => $type->id,
                'name' => $name,
                'initial_balance' => $initial,
            ]);
        }

        $rows = [
            // [conta, tipo, valor, pago, meses de deslocamento]
            [0, 'RECEI', '3000.00', true, 0],
            [0, 'RECEI', '1200.00', false, 0],
            [1, 'RECEI', '800.00', true, -1],
            [0, 'FIXEX', '-450.00', true, 0],
            [0, 'FIXEX', '-100.00', false, 0],
            [1, 'FIXEX', '-300.00', true, -1],
            [1, 'VAREX', '-75.25', true, 0],
            [2, 'VAREX', '-60.00', false, -1],
            [2, 'TAXES', '-30.00', true, 0],
            [0, 'PEOPL', '-20.00', true, -2],
            [0, 'TRANS', '-200.00', true, 0],
            [1, 'TRANS', '200.00', true, 0],
            [2, 'RECEI', '10.00', true, -3],
            [1, 'RECEI', '55.00', true, 1],
        ];

        foreach ($rows as [$accountIndex, $transactionType, $amount, $paid, $shift]) {
            Transaction::factory()->create([
                'user_id' => $this->user->id,
                'account_id' => $this->accounts[$accountIndex]->id,
                'payment_type_id' => $paymentType->id,
                'transaction_type' => $transactionType,
                'amount' => $amount,
                'status' => $paid,
                'date_due' => now()->startOfMonth()->addDays(9)->addMonths($shift),
            ]);
        }

        $this->actingAs($this->user);
    }

    public function test_listing_balances_match_individual_full_balance_calls(): void
    {
        $month = now()->format('Y-m');
        $ids = $this->accounts[0]->id.','.$this->accounts[1]->id;

        foreach (['all', (string) $this->accounts[1]->id, $ids] as $filter) {
            $snapshot = Transaction::query()->getModel()->listingBalances($month, $filter);

            $this->assertEquals(Transaction::getFullBalance($month, $filter, 'all'), $snapshot['month']);
            $this->assertEquals(Transaction::getFullBalance($month, $filter, '1', true), $snapshot['final']);
            $this->assertEquals(Transaction::getFullBalance($month, $filter, 'all', true), $snapshot['estimated']);
        }
    }

    public function test_listing_account_balances_match_per_account_full_balance(): void
    {
        $month = now()->format('Y-m');
        $balances = Transaction::finalBalancesByAccount($month);

        foreach ($this->accounts as $account) {
            $expected = Transaction::getFullBalance($month, (string) $account->id, '1', true)['final_balance_transfer'];
            $this->assertEqualsWithDelta($expected, $balances['accounts'][$account->id], 0.0001);
        }

        $this->assertEqualsWithDelta(
            Transaction::getFullBalance($month, 'all', '1', true)['final_balance_transfer'],
            $balances['total'],
            0.0001
        );
    }

    public function test_listing_balances_use_a_constant_number_of_queries(): void
    {
        DB::enableQueryLog();
        (new TransactionReport($this->user->id))->listingBalances(now()->format('Y-m'), 'all');
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertSame(4, $queries);
    }

    public function test_dashboard_summary_matches_individual_metrics(): void
    {
        $dashboard = new Dashboard;
        $userId = $this->user->id;
        $month = now()->format('Y-m');

        $summary = $dashboard->getSummary($userId, $month);

        $balance = $dashboard->getMonthBalance($userId, $month);
        $incoming = $dashboard->getIncomingBalance($userId, $month);
        $fixed = $dashboard->getOutgoingFixedBalance($userId, $month);
        $variable = $dashboard->getOutgoingVariableBalance($userId, $month);

        $expected = [
            'balance' => $balance,
            'balance_percentage' => $dashboard->getLastMonthBalancePercentage($userId, $month, $balance),
            'incoming' => $incoming,
            'incoming_percentage' => $dashboard->getLastIncomingBalancePercentage($userId, $month, $incoming),
            'fixed' => $fixed,
            'fixed_percentage' => $dashboard->getLastOutgoingFixedPercentage($userId, $month, $fixed),
            'variable' => $variable,
            'variable_percentage' => $dashboard->getLastOutgoingVariablePercentage($userId, $month, $variable),
        ];

        foreach ($expected as $key => $value) {
            if (is_string($value)) {
                $this->assertSame($value, $summary['totals'][$key], $key);
            } else {
                $this->assertEqualsWithDelta($value, $summary['totals'][$key], 0.0001, $key);
            }
        }

        $chart = $dashboard->getChartData($userId);
        $this->assertSame($chart['labels'], $summary['chart']['labels']);
        foreach (['incoming', 'outgoing', 'transfers'] as $series) {
            foreach ($chart[$series] as $index => $value) {
                $this->assertEqualsWithDelta($value, $summary['chart'][$series][$index], 0.0001, "{$series}[{$index}]");
            }
        }
    }

    public function test_dashboard_summary_uses_a_constant_number_of_queries(): void
    {
        DB::enableQueryLog();
        (new Dashboard)->getSummary($this->user->id, now()->format('Y-m'));
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertSame(4, $queries);
    }

    public function test_dashboard_and_listing_pages_render(): void
    {
        $this->get('/dashboard')->assertOk()->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('totals.balance')
            ->has('graphBalance.labels', 6)
        );

        $this->get('/transaction?f_date='.now()->format('Y-m'))->assertOk()->assertInertia(fn ($page) => $page
            ->component('Transactions/Index')
            ->has('comboAccounts')
            ->has('monthBalance.month_balance')
            ->has('finalBalance.final_balance')
            ->has('estimatedBalance.final_balance')
        );
    }
}
