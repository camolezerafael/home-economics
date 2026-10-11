<?php

namespace Tests\Feature;

use App\Http\Controllers\TransactionController;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\Dashboard;
use App\Models\PaymentType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NumberFormatter;
use Tests\TestCase;

/**
 * Testes de caracterização: fixam o comportamento atual de transferências,
 * agrupamento, saldos por conta, totais do mês e gráfico do dashboard antes
 * de qualquer refatoração (rede de segurança do /reversa-refactor).
 */
class TransferCharacterizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Account $account1;

    protected Account $account2;

    protected Account $account3;

    protected PaymentType $paymentType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $accountType = AccountType::factory()->create(['user_id' => $this->user->id]);
        $this->account1 = Account::factory()->create(['user_id' => $this->user->id, 'type_id' => $accountType->id, 'name' => 'Conta A']);
        $this->account2 = Account::factory()->create(['user_id' => $this->user->id, 'type_id' => $accountType->id, 'name' => 'Conta B']);
        $this->account3 = Account::factory()->create(['user_id' => $this->user->id, 'type_id' => $accountType->id, 'name' => 'Conta C']);
        $this->paymentType = PaymentType::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
    }

    /**
     * @return array{0: Transaction, 1: Transaction} [saída, entrada]
     */
    private function createTransfer(string $description = 'Reserva', string $amount = '500.00'): array
    {
        $this->post('/transaction', [
            'account_id' => $this->account1->id,
            'account_to_id' => $this->account2->id,
            'payment_type_id' => $this->paymentType->id,
            'transaction_type' => 'TRANS',
            'description' => $description,
            'amount' => $amount,
            'date_due' => now()->format('Y-m-d'),
        ])->assertRedirect();

        $legs = Transaction::query()->where('description', $description)->orderBy('id')->get();
        $outgoing = $legs->first(fn (Transaction $t) => $t->getRawAmountAttribute() < 0);
        $incoming = $legs->first(fn (Transaction $t) => $t->getRawAmountAttribute() > 0);

        return [$outgoing, $incoming];
    }

    public function test_edit_transfer_exposes_paired_account_and_positive_amount(): void
    {
        [$outgoing] = $this->createTransfer();

        $response = $this->getJson("/transaction/{$outgoing->id}/edit");

        $response->assertOk();
        $response->assertJsonPath('item.account_id', $this->account1->id);
        $response->assertJsonPath('item.account_to_id', $this->account2->id);
        $this->assertEquals(500, $response->json('item.amount'));
        $response->assertJsonMissingPath('aggregates.categories');
    }

    public function test_edit_incoming_leg_keeps_origin_and_destination_orientation(): void
    {
        [, $incoming] = $this->createTransfer();

        $response = $this->getJson("/transaction/{$incoming->id}/edit");

        $response->assertOk();
        $response->assertJsonPath('item.account_id', $this->account1->id);
        $response->assertJsonPath('item.account_to_id', $this->account2->id);
    }

    public function test_update_transfer_updates_both_legs(): void
    {
        [$outgoing, $incoming] = $this->createTransfer();

        $this->patch("/transaction/{$outgoing->id}", [
            'account_id' => $this->account1->id,
            'account_to_id' => $this->account2->id,
            'payment_type_id' => $this->paymentType->id,
            'transaction_type' => 'TRANS',
            'description' => 'Reserva ajustada',
            'amount' => '300.00',
            'date_due' => now()->format('Y-m-d'),
        ])->assertRedirect();

        $this->assertDatabaseHas('transactions', ['id' => $outgoing->id, 'description' => 'Reserva ajustada', 'amount' => -30000, 'account_id' => $this->account1->id, 'status' => true]);
        $this->assertDatabaseHas('transactions', ['id' => $incoming->id, 'description' => 'Reserva ajustada', 'amount' => 30000, 'account_id' => $this->account2->id, 'status' => true]);
    }

    public function test_update_transfer_from_incoming_leg_updates_both_legs(): void
    {
        [$outgoing, $incoming] = $this->createTransfer();

        $this->patch("/transaction/{$incoming->id}", [
            'account_id' => $this->account1->id,
            'account_to_id' => $this->account2->id,
            'payment_type_id' => $this->paymentType->id,
            'transaction_type' => 'TRANS',
            'description' => 'Reserva pela entrada',
            'amount' => '250.00',
            'date_due' => now()->format('Y-m-d'),
        ])->assertRedirect();

        $this->assertDatabaseHas('transactions', ['id' => $incoming->id, 'description' => 'Reserva pela entrada', 'amount' => 25000, 'account_id' => $this->account2->id]);
        $this->assertDatabaseHas('transactions', ['id' => $outgoing->id, 'description' => 'Reserva pela entrada', 'amount' => -25000, 'account_id' => $this->account1->id]);
    }

    public function test_update_transfer_can_change_destination_account(): void
    {
        [$outgoing, $incoming] = $this->createTransfer();

        $this->patch("/transaction/{$outgoing->id}", [
            'account_id' => $this->account1->id,
            'account_to_id' => $this->account3->id,
            'payment_type_id' => $this->paymentType->id,
            'transaction_type' => 'TRANS',
            'description' => 'Reserva',
            'amount' => '500.00',
            'date_due' => now()->format('Y-m-d'),
        ])->assertRedirect();

        $this->assertDatabaseHas('transactions', ['id' => $incoming->id, 'account_id' => $this->account3->id]);
        $this->assertDatabaseHas('transactions', ['id' => $outgoing->id, 'account_id' => $this->account1->id]);
    }

    public function test_group_transfers_pairs_opposite_legs_and_keeps_unpaired(): void
    {
        [$outgoing, $incoming] = $this->createTransfer('Par', '120.00');
        $orphan = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account3->id,
            'transaction_type' => 'TRANS',
            'description' => 'Sem par',
            'amount' => '10.00',
            'status' => true,
            'date_due' => now(),
        ]);

        $grouped = Transaction::groupTransfers(Transaction::query()->with('account')->orderBy('id')->get());

        $this->assertCount(2, $grouped);
        $pair = $grouped[0];
        $this->assertSame($outgoing->id, $pair->id);
        $this->assertSame('Par', $pair->description);
        $this->assertEquals(120.0, $pair->amount);
        $this->assertSame('Conta A', $pair->account_from);
        $this->assertSame('Conta B', $pair->account_to);
        $this->assertSame([$outgoing->id, $incoming->id], $pair->transfer_ids);
        $this->assertSame(1, $pair->status);
        $this->assertSame($orphan->id, $grouped[1]->id);
    }

    public function test_amounts_paginated_groups_transfers_into_a_single_row(): void
    {
        $this->createTransfer('Par A', '100.00');
        $this->createTransfer('Par B', '200.00');
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'RECEI',
            'amount' => '50.00',
            'status' => true,
            'date_due' => now(),
        ]);

        $result = Transaction::getAmountsPaginated(now()->format('Y-m'), 'all', 'all', 10);

        $this->assertSame(['RECEI', 'FIXEX', 'VAREX', 'PEOPL', 'TAXES', 'TRANS'], array_keys($result));
        $this->assertSame(1, $result['RECEI']->total());
        $this->assertSame(2, $result['TRANS']->total());
        $this->assertSame(0, $result['FIXEX']->total());
    }

    public function test_full_balance_can_be_filtered_by_account(): void
    {
        $this->account1->update(['initial_balance' => '1000.00']);
        $this->account2->update(['initial_balance' => '200.00']);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'RECEI', 'amount' => '300.00', 'status' => true, 'date_due' => now()]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account2->id, 'transaction_type' => 'RECEI', 'amount' => '80.00', 'status' => false, 'date_due' => now()]);

        $month = now()->format('Y-m');

        $onlyFirst = Transaction::getFullBalance($month, (string) $this->account1->id, 'all', true);
        $this->assertEqualsWithDelta(300.00, $onlyFirst['month_balance'], 0.01);
        $this->assertEqualsWithDelta(1300.00, $onlyFirst['final_balance'], 0.01);

        $paidOnly = Transaction::getFullBalance($month, 'all', '1', true);
        $this->assertEqualsWithDelta(1500.00, $paidOnly['final_balance'], 0.01);

        $both = Transaction::getFullBalance($month, $this->account1->id.','.$this->account2->id, 'all', true);
        $this->assertEqualsWithDelta(1580.00, $both['final_balance'], 0.01);
    }

    public function test_combo_accounts_returns_formatted_balance_per_account(): void
    {
        $this->account1->update(['initial_balance' => '1000.00']);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'RECEI', 'amount' => '250.00', 'status' => true, 'date_due' => now()]);

        $month = now()->format('Y-m');
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);

        $combo = TransactionController::comboAccounts(true, true, $month);

        $this->assertSame(['all', $this->account1->id, $this->account2->id, $this->account3->id], array_keys($combo));
        $this->assertSame($formatter->format(1250.00), $combo[$this->account1->id]['balance']);
        $this->assertSame($formatter->format(0), $combo[$this->account2->id]['balance']);
        $this->assertSame($formatter->format(1250.00), $combo['all']['balance']);
        $this->assertSame('Conta A', $combo[$this->account1->id]['name']);
    }

    public function test_month_totals_split_paid_and_to_pay(): void
    {
        $month = now();
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'RECEI', 'amount' => '1000.00', 'status' => true, 'date_due' => $month]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'RECEI', 'amount' => '500.00', 'status' => false, 'date_due' => $month]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'FIXEX', 'amount' => '200.00', 'status' => true, 'date_due' => $month]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'VAREX', 'amount' => '300.00', 'status' => false, 'date_due' => $month]);

        $totals = Transaction::getMonthTotals($month->format('Y-m'));

        $this->assertEquals(1000.00, $totals['RECEI']['PAID']);
        $this->assertEquals(500.00, $totals['RECEI']['TO_PAY']);
        $this->assertEquals(1500.00, $totals['RECEI']['TOTAL']);
        $this->assertEquals(66.67, $totals['RECEI']['PERC']);
        $this->assertEquals(200.00, $totals['EXPEN']['PAID']);
        $this->assertEquals(300.00, $totals['EXPEN']['TO_PAY']);
        $this->assertEquals(500.00, $totals['EXPEN']['TOTAL']);
        $this->assertEquals(40.0, $totals['EXPEN']['PERC']);
    }

    public function test_dashboard_chart_data_aggregates_last_six_months_of_paid_items(): void
    {
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'RECEI', 'amount' => '1000.00', 'status' => true, 'date_due' => now()]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'RECEI', 'amount' => '400.00', 'status' => true, 'date_due' => now()->subMonth()]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'RECEI', 'amount' => '999.00', 'status' => false, 'date_due' => now()]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'FIXEX', 'amount' => '-100.00', 'status' => true, 'date_due' => now()]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'VAREX', 'amount' => '-50.00', 'status' => true, 'date_due' => now()]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $this->account1->id, 'transaction_type' => 'RECEI', 'amount' => '777.00', 'status' => true, 'date_due' => now()->subMonths(6)]);

        $chart = (new Dashboard)->getChartData($this->user->id);

        $this->assertCount(6, $chart['labels']);
        $this->assertCount(6, $chart['incoming']);
        $this->assertEquals(1000.00, $chart['incoming'][5]);
        $this->assertEquals(400.00, $chart['incoming'][4]);
        $this->assertEquals(0.0, $chart['incoming'][0]);
        $this->assertEquals($chart['outgoing'][5], $this->outgoingSum());
        $this->assertCount(6, $chart['transfers']);
    }

    public function test_transfer_without_payment_type_uses_first_payment_type_by_name(): void
    {
        $default = PaymentType::factory()->create(['user_id' => $this->user->id, 'name' => '0 Padrão']);

        $this->post('/transaction', [
            'account_id' => $this->account1->id,
            'account_to_id' => $this->account2->id,
            'transaction_type' => 'TRANS',
            'description' => 'Sem tipo',
            'amount' => '90.00',
            'date_due' => now()->format('Y-m-d'),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame(
            [$default->id, $default->id],
            Transaction::query()->where('description', 'Sem tipo')->pluck('payment_type_id')->all()
        );
    }

    public function test_update_transfer_without_payment_type_uses_default(): void
    {
        [$outgoing, $incoming] = $this->createTransfer();
        $default = PaymentType::factory()->create(['user_id' => $this->user->id, 'name' => '0 Padrão']);

        $this->patch("/transaction/{$outgoing->id}", [
            'account_id' => $this->account1->id,
            'account_to_id' => $this->account2->id,
            'transaction_type' => 'TRANS',
            'description' => 'Reserva',
            'amount' => '500.00',
            'date_due' => now()->format('Y-m-d'),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('transactions', ['id' => $outgoing->id, 'payment_type_id' => $default->id]);
        $this->assertDatabaseHas('transactions', ['id' => $incoming->id, 'payment_type_id' => $default->id]);
    }

    public function test_transfer_requires_payment_type_when_user_has_none(): void
    {
        PaymentType::query()->where('user_id', $this->user->id)->delete();

        $this->post('/transaction', [
            'account_id' => $this->account1->id,
            'account_to_id' => $this->account2->id,
            'transaction_type' => 'TRANS',
            'description' => 'Sem tipo cadastrado',
            'amount' => '90.00',
            'date_due' => now()->format('Y-m-d'),
        ])->assertSessionHasErrors('payment_type_id');
    }

    public function test_non_transfer_still_requires_payment_type(): void
    {
        $this->post('/transaction', [
            'account_id' => $this->account1->id,
            'transaction_type' => 'RECEI',
            'description' => 'Sem tipo',
            'amount' => '90.00',
            'date_due' => now()->format('Y-m-d'),
        ])->assertSessionHasErrors('payment_type_id');
    }

    public function test_index_lists_grouped_transfer_with_accounts_and_regular_rows(): void
    {
        [$outgoing] = $this->createTransfer('Par listado', '75.00');
        $from = \App\Models\FromTo::factory()->create(['user_id' => $this->user->id, 'name' => 'Mercado']);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'from_to_id' => $from->id,
            'payment_type_id' => $this->paymentType->id,
            'transaction_type' => 'FIXEX',
            'description' => 'Aluguel',
            'amount' => '300.00',
            'status' => false,
            'date_due' => now(),
        ]);

        $this->get('/transaction?f_date='.now()->format('Y-m'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Transactions/Index')
                ->where('items.TRANS.data.0.id', $outgoing->id)
                ->where('items.TRANS.data.0.account', 'Conta A → Conta B')
                ->where('items.TRANS.data.0.transaction_type', 'TRANS')
                ->where('items.TRANS.data.0.status', true)
                ->where('items.FIXEX.data.0.from_to', 'Mercado')
                ->where('items.FIXEX.data.0.payment_type', $this->paymentType->name)
                ->where('items.FIXEX.data.0.is_today', true)
                ->where('items.FIXEX.data.0.is_late', false)
            );
    }

    public function test_delete_transfer_removes_both_legs(): void
    {
        [$outgoing, $incoming] = $this->createTransfer('Para excluir');

        $this->deleteJson("/transaction/{$outgoing->id}")->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('transactions', ['id' => $outgoing->id]);
        $this->assertDatabaseMissing('transactions', ['id' => $incoming->id]);
    }

    public function test_delete_unpaired_transfer_leg_removes_only_itself(): void
    {
        $leg = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account1->id,
            'transaction_type' => 'TRANS',
            'description' => 'Sem par',
            'amount' => '10.00',
            'status' => true,
            'date_due' => now(),
        ]);
        $other = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account2->id,
            'transaction_type' => 'TRANS',
            'description' => 'Sem par',
            'amount' => '99.00',
            'status' => true,
            'date_due' => now(),
        ]);

        $this->deleteJson("/transaction/{$leg->id}")->assertOk();

        $this->assertDatabaseMissing('transactions', ['id' => $leg->id]);
        $this->assertDatabaseHas('transactions', ['id' => $other->id]);
    }

    private function outgoingSum(): float
    {
        $dashboard = new Dashboard;
        $month = now()->format('Y-m');

        return $dashboard->getOutgoingFixedBalance($this->user->id, $month) + $dashboard->getOutgoingVariableBalance($this->user->id, $month);
    }
}
