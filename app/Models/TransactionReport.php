<?php

namespace App\Models;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Consultas de relatório sobre transactions (saldos, totais do mês, listagem
 * paginada e agrupamento de transferências), isoladas do model Transaction.
 */
class TransactionReport
{
    private const BALANCE_VIEWS = ['balance_incoming', 'balance_outgoing', 'balance_transfer'];

    private const PRECISION_DIVISOR = 100;

    public function __construct(private readonly ?int $userId) {}

    /**
     * @return array{month_balance: float, final_balance: float, final_balance_transfer: float}
     */
    public function fullBalance(string $dateFilter, string $accountFilter = 'all', string $paidFilter = 'all', bool $backwardsBalance = false): array
    {
        return $this->balanceFrom(
            $this->balanceSnapshot($dateFilter),
            $this->accountIds($accountFilter),
            $this->paidStatus($paidFilter),
            $backwardsBalance
        );
    }

    /**
     * Saldo final acumulado até o fim do mês, por conta e total.
     *
     * @return array{total: float, accounts: array<int, float>}
     */
    public function finalBalancesByAccount(string $dateFilter, string $paidFilter = '1'): array
    {
        return $this->accountBalances($this->balanceSnapshot($dateFilter), $this->paidStatus($paidFilter));
    }

    /**
     * Todos os saldos da listagem de movimentos com um único snapshot (4 queries):
     * mês, acumulado pago, acumulado previsto e saldo final pago por conta.
     *
     * @return array{month: array<string, float>, final: array<string, float>, estimated: array<string, float>, accounts: array{total: float, accounts: array<int, float>}}
     */
    public function listingBalances(string $dateFilter, string $accountFilter): array
    {
        $snapshot = $this->balanceSnapshot($dateFilter);
        $accountIds = $this->accountIds($accountFilter);

        return [
            'month' => $this->balanceFrom($snapshot, $accountIds, null, false),
            'final' => $this->balanceFrom($snapshot, $accountIds, 1, true),
            'estimated' => $this->balanceFrom($snapshot, $accountIds, null, true),
            'accounts' => $this->accountBalances($snapshot, 1),
        ];
    }

    /**
     * Somas agrupadas por conta e status de cada view: 'upto' (até o fim do mês)
     * e 'month' (somente dentro do mês), mais o saldo inicial de cada conta.
     *
     * @return array{rows: array<string, array<int, object>>, initials: array<int, float>}
     */
    private function balanceSnapshot(string $dateFilter): array
    {
        [$monthStart, $monthEnd] = $this->monthRange($dateFilter);

        $rows = [];
        foreach (self::BALANCE_VIEWS as $view) {
            $rows[$view] = DB::table($view)
                ->where('user_id', $this->userId)
                ->where('date_due', '<=', $monthEnd)
                ->groupBy('account_id', 'status')
                ->selectRaw(
                    'account_id, status, SUM(amount) as upto, SUM(CASE WHEN date_due >= ? THEN amount ELSE 0 END) as month_sum',
                    [$monthStart]
                )
                ->get()
                ->all();
        }

        $initials = DB::table('accounts')
            ->where('user_id', $this->userId)
            ->pluck('initial_balance', 'id')
            ->map(fn ($value) => (float) $value)
            ->all();

        return ['rows' => $rows, 'initials' => $initials];
    }

    /**
     * @param  array{rows: array<string, array<int, object>>, initials: array<int, float>}  $snapshot
     * @param  array<int, string>|null  $accountIds  null = todas as contas
     * @param  int|null  $paidStatus  null = pagos e previstos
     * @return array{month_balance: float, final_balance: float, final_balance_transfer: float}
     */
    private function balanceFrom(array $snapshot, ?array $accountIds, ?int $paidStatus, bool $backwards): array
    {
        $lookup = $accountIds === null ? null : array_flip(array_map('strval', $accountIds));
        $column = $backwards ? 'upto' : 'month_sum';

        $sums = [];
        foreach (self::BALANCE_VIEWS as $view) {
            $sum = 0.0;
            foreach ($snapshot['rows'][$view] as $row) {
                if ($lookup !== null && ! isset($lookup[(string) $row->account_id])) {
                    continue;
                }
                if ($paidStatus !== null && (int) $row->status !== $paidStatus) {
                    continue;
                }
                $sum += (float) $row->{$column};
            }
            $sums[$view] = $sum;
        }

        $initial = 0.0;
        foreach ($snapshot['initials'] as $accountId => $balance) {
            if ($lookup === null || isset($lookup[(string) $accountId])) {
                $initial += $balance;
            }
        }

        $incoming = $sums['balance_incoming'];
        $outgoing = abs($sums['balance_outgoing']);
        $transfer = $sums['balance_transfer'];

        $finalBalance = ($incoming + $initial - $outgoing + $transfer) / self::PRECISION_DIVISOR;

        return [
            'month_balance' => ($incoming - $outgoing + $transfer) / self::PRECISION_DIVISOR,
            'final_balance' => $finalBalance,
            'final_balance_transfer' => $finalBalance,
        ];
    }

    /**
     * @param  array{rows: array<string, array<int, object>>, initials: array<int, float>}  $snapshot
     * @return array{total: float, accounts: array<int, float>}
     */
    private function accountBalances(array $snapshot, ?int $paidStatus): array
    {
        $accountIds = array_keys($snapshot['initials']);
        foreach ($snapshot['rows'] as $viewRows) {
            foreach ($viewRows as $row) {
                $accountIds[] = (int) $row->account_id;
            }
        }

        $accounts = [];
        foreach (array_unique($accountIds) as $accountId) {
            $accounts[$accountId] = $this->balanceFrom($snapshot, [(string) $accountId], $paidStatus, true)['final_balance'];
        }

        return [
            'total' => $this->balanceFrom($snapshot, null, $paidStatus, true)['final_balance'],
            'accounts' => $accounts,
        ];
    }

    /**
     * @return array<int, string>|null
     */
    private function accountIds(string $accountFilter): ?array
    {
        return $accountFilter === 'all' ? null : explode(',', $accountFilter);
    }

    private function paidStatus(string $paidFilter): ?int
    {
        return $paidFilter === 'all' ? null : (int) ($paidFilter === '1');
    }

    /**
     * Intervalo [início, fim] do mês 'Y-m'; comparar com between usa o índice (user_id, date_due).
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function monthRange(string $dateFilter): array
    {
        $start = Carbon::createFromFormat('Y-m-d', $dateFilter.'-01')->startOfMonth();

        return [$start, $start->copy()->endOfMonth()];
    }

    /**
     * @return array{RECEI: array<string, float>, EXPEN: array<string, float>}
     */
    public function monthTotals(string $dateFilter): array
    {
        $results = DB::table('transactions')
            ->where('user_id', $this->userId)
            ->whereBetween('date_due', $this->monthRange($dateFilter))
            ->selectRaw("
                SUM(CASE WHEN transaction_type = 'RECEI' AND status = 1 THEN amount ELSE 0 END) as recei_paid,
                SUM(CASE WHEN transaction_type = 'RECEI' THEN amount ELSE 0 END) as recei_total,
                SUM(CASE WHEN transaction_type IN ('FIXEX','VAREX','PEOPL','TAXES') AND status = 1 THEN amount ELSE 0 END) as expen_paid,
                SUM(CASE WHEN transaction_type IN ('FIXEX','VAREX','PEOPL','TAXES') THEN amount ELSE 0 END) as expen_total
            ")
            ->first();

        $receiPaid = ($results->recei_paid ?? 0) / self::PRECISION_DIVISOR;
        $receiTotal = ($results->recei_total ?? 0) / self::PRECISION_DIVISOR;
        $expenPaid = ($results->expen_paid ?? 0) / self::PRECISION_DIVISOR;
        $expenTotal = ($results->expen_total ?? 0) / self::PRECISION_DIVISOR;

        return [
            'RECEI' => $this->totalsBlock($receiPaid, $receiTotal),
            'EXPEN' => $this->totalsBlock($expenPaid, $expenTotal),
        ];
    }

    /**
     * @return array{PAID: float|int, TO_PAY: float|int, TOTAL: float|int, PERC: float|int}
     */
    private function totalsBlock(float|int $paid, float|int $total): array
    {
        $percentage = ($total > 0) ? round(($paid / $total) * 100, 2) : (($paid > 0) ? 100 : 0);

        return [
            'PAID' => $paid,
            'TO_PAY' => $total - $paid,
            'TOTAL' => $total,
            'PERC' => $percentage,
        ];
    }

    /**
     * Pareia as pernas (saída negativa + entrada positiva) de cada transferência,
     * independentemente da ordem em que aparecem. Pernas sem par são mantidas.
     *
     * @param  iterable<int, Transaction>  $transfers
     * @return array<int, object|Transaction>
     */
    public static function groupTransfers(iterable $transfers): array
    {
        $items = [];
        $buckets = [];

        foreach ($transfers as $transfer) {
            $index = count($items);
            $items[$index] = $transfer;

            $raw = $transfer->getRawAmountAttribute();
            if ($raw === 0) {
                continue;
            }

            $key = $transfer->description.'|'.abs($raw).'|'.Carbon::parse($transfer->date_due)->format('Y-m-d');
            $buckets[$key][$raw < 0 ? 'out' : 'in'][] = $index;
        }

        $grouped = [];
        $used = [];

        foreach ($items as $index => $transfer) {
            if (isset($used[$index])) {
                continue;
            }
            $used[$index] = true;

            $raw = $transfer->getRawAmountAttribute();
            $partnerIndex = null;

            if ($raw !== 0) {
                $key = $transfer->description.'|'.abs($raw).'|'.Carbon::parse($transfer->date_due)->format('Y-m-d');
                $oppositeSide = $raw < 0 ? 'in' : 'out';

                foreach ($buckets[$key][$oppositeSide] ?? [] as $candidate) {
                    if (! isset($used[$candidate])) {
                        $partnerIndex = $candidate;
                        break;
                    }
                }
            }

            if ($partnerIndex === null) {
                $grouped[] = $transfer;

                continue;
            }

            $used[$partnerIndex] = true;
            $partner = $items[$partnerIndex];
            $outgoing = $raw < 0 ? $transfer : $partner;
            $incoming = $raw < 0 ? $partner : $transfer;

            $grouped[] = (object) [
                'id' => $outgoing->id,
                'transaction_type' => Transaction::TYPE_TRANS,
                'description' => $outgoing->description,
                'amount' => abs($outgoing->getRawAmountAttribute()) / pow(10, $outgoing->account?->decimal_precision ?? 2),
                'date_due' => $outgoing->date_due,
                'date_payment' => $outgoing->date_payment,
                'account_from' => $outgoing->account?->name ?? '—',
                'account_to' => $incoming->account?->name ?? '—',
                'transfer_ids' => [$outgoing->id, $incoming->id],
                'status' => 1,
            ];
        }

        return $grouped;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Pagination\LengthAwarePaginator>
     */
    public function amountsPaginated(string $dateFilter, string $accountFilter, string $paidFilter, int $perPage, int $page): array
    {
        $query = Transaction::query()
            ->with(['account', 'category', 'fromTo', 'paymentType'])
            ->where('user_id', $this->userId)
            ->whereBetween('date_due', $this->monthRange($dateFilter));

        if ($accountFilter !== 'all') {
            $query->whereIn('account_id', explode(',', $accountFilter));
        }

        if ($paidFilter !== 'all') {
            $query->where('status', $paidFilter === '1');
        }

        $query->orderBy('date_due', 'asc');

        $results = [];
        foreach (Transaction::TYPES as $type) {
            $typeQuery = (clone $query)->where('transaction_type', $type);

            if ($type === Transaction::TYPE_TRANS) {
                $collection = collect(self::groupTransfers($typeQuery->get()));
                $results[$type] = new LengthAwarePaginator(
                    $collection->forPage($page, $perPage),
                    $collection->count(),
                    $perPage,
                    $page
                );
            } else {
                $results[$type] = $typeQuery->paginate($perPage);
            }
        }

        return $results;
    }
}
