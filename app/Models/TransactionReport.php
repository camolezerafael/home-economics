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
        [$year, $month] = explode('-', $dateFilter);
        $dateEnd = Carbon::createFromFormat('Y-m-d', $dateFilter.'-01')->endOfMonth();
        $accountIds = $accountFilter === 'all' ? null : explode(',', $accountFilter);

        $sums = [];
        foreach (self::BALANCE_VIEWS as $view) {
            $query = DB::table($view)->where('user_id', $this->userId);

            if ($accountIds !== null) {
                $query->whereIn('account_id', $accountIds);
            }

            if ($backwardsBalance) {
                $query->where('date_due', '<=', $dateEnd);
            } else {
                $query->whereYear('date_due', $year)->whereMonth('date_due', $month);
            }

            if ($paidFilter !== 'all') {
                $query->where('status', $paidFilter === '1');
            }

            $sums[$view] = (float) $query->sum('amount');
        }

        $initialQuery = DB::table('accounts')->where('user_id', $this->userId);
        if ($accountIds !== null) {
            $initialQuery->whereIn('id', $accountIds);
        }
        $initial = (float) $initialQuery->sum('initial_balance');

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
     * Saldo final acumulado até o fim do mês, por conta e total, em uma passada
     * (uma query agrupada por view, em vez de quatro queries por conta).
     *
     * @return array{total: float, accounts: array<int, float>}
     */
    public function finalBalancesByAccount(string $dateFilter, string $paidFilter = '1'): array
    {
        $dateEnd = Carbon::createFromFormat('Y-m-d', $dateFilter.'-01')->endOfMonth();

        $byView = [];
        foreach (self::BALANCE_VIEWS as $view) {
            $query = DB::table($view)
                ->where('user_id', $this->userId)
                ->where('date_due', '<=', $dateEnd);

            if ($paidFilter !== 'all') {
                $query->where('status', $paidFilter === '1');
            }

            $byView[$view] = $query
                ->groupBy('account_id')
                ->selectRaw('account_id, SUM(amount) as total')
                ->pluck('total', 'account_id')
                ->map(fn ($value) => (float) $value)
                ->all();
        }

        $initials = DB::table('accounts')
            ->where('user_id', $this->userId)
            ->pluck('initial_balance', 'id')
            ->map(fn ($value) => (float) $value)
            ->all();

        $accountIds = array_unique(array_merge(
            array_keys($initials),
            array_keys($byView['balance_incoming']),
            array_keys($byView['balance_outgoing']),
            array_keys($byView['balance_transfer']),
        ));

        $accounts = [];
        foreach ($accountIds as $accountId) {
            $accounts[$accountId] = (
                ($byView['balance_incoming'][$accountId] ?? 0.0)
                + ($initials[$accountId] ?? 0.0)
                - abs($byView['balance_outgoing'][$accountId] ?? 0.0)
                + ($byView['balance_transfer'][$accountId] ?? 0.0)
            ) / self::PRECISION_DIVISOR;
        }

        $total = (
            array_sum($byView['balance_incoming'])
            + array_sum($initials)
            - abs(array_sum($byView['balance_outgoing']))
            + array_sum($byView['balance_transfer'])
        ) / self::PRECISION_DIVISOR;

        return ['total' => $total, 'accounts' => $accounts];
    }

    /**
     * @return array{RECEI: array<string, float>, EXPEN: array<string, float>}
     */
    public function monthTotals(string $dateFilter): array
    {
        [$year, $month] = explode('-', $dateFilter);
        $results = DB::table('transactions')
            ->where('user_id', $this->userId)
            ->whereYear('date_due', $year)->whereMonth('date_due', $month)
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
                'id' => $outgoing->id.'_'.$incoming->id,
                'description' => $outgoing->description,
                'amount' => abs($outgoing->getRawAmountAttribute()) / pow(10, $outgoing->account?->decimal_precision ?? 2),
                'date_due' => $outgoing->date_due,
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
        [$year, $month] = explode('-', $dateFilter);

        $query = Transaction::query()
            ->with(['account', 'category', 'fromTo', 'paymentType'])
            ->where('user_id', $this->userId)
            ->whereYear('date_due', $year)->whereMonth('date_due', $month);

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
