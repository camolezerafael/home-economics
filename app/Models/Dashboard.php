<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard
{
    private const PRECISION_DIVISOR = 100;

    public function getMonthBalance(int $userId, string $yearMonth, bool $backwardsBalance = false): float
    {
        $balance = Transaction::getFullBalance($yearMonth, 'all', 'all', $backwardsBalance);

        return $balance['final_balance'];
    }

    public function getIncomingBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        return $this->monthBalance('balance_incoming', $userId, $yearMonth, $onlyPaid);
    }

    public function getOutgoingFixedBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        return $this->monthBalance('balance_outgoing', $userId, $yearMonth, $onlyPaid, Transaction::TYPE_FIXEX);
    }

    public function getOutgoingVariableBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        return $this->monthBalance('balance_outgoing', $userId, $yearMonth, $onlyPaid, Transaction::TYPE_VAREX);
    }

    public function getTransferBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        return $this->monthBalance('balance_transfer', $userId, $yearMonth, $onlyPaid);
    }

    /**
     * @return array{labels: array<int, string>, incoming: array<int, float>, outgoing: array<int, float>, transfers: array<int, float>}
     */
    public function getChartData(int $userId, int $months = 6): array
    {
        $firstMonth = Carbon::now()->startOfMonth()->subMonths($months - 1);
        $lastMonth = Carbon::now()->endOfMonth();

        $incoming = $this->paidSumsByMonth('balance_incoming', $userId, $firstMonth, $lastMonth);
        $outgoing = $this->paidSumsByMonth('balance_outgoing', $userId, $firstMonth, $lastMonth, [Transaction::TYPE_FIXEX, Transaction::TYPE_VAREX]);
        $transfers = $this->paidSumsByMonth('balance_transfer', $userId, $firstMonth, $lastMonth);

        $chart = ['labels' => [], 'incoming' => [], 'outgoing' => [], 'transfers' => []];

        for ($i = 0; $i < $months; $i++) {
            $date = $firstMonth->copy()->addMonths($i);
            $key = $date->format('Y-m');

            $chart['labels'][] = $date->translatedFormat('M');
            $chart['incoming'][] = ($incoming[$key] ?? 0) / self::PRECISION_DIVISOR;
            $chart['outgoing'][] = ($outgoing[$key] ?? 0) / self::PRECISION_DIVISOR;
            $chart['transfers'][] = ($transfers[$key] ?? 0) / self::PRECISION_DIVISOR;
        }

        return $chart;
    }

    /**
     * Cards + gráficos do dashboard com 4 queries (3 views + contas), em vez de uma por métrica/mês.
     * Equivale a combinar getMonthBalance, get*Balance, get*Percentage e getChartData.
     *
     * @return array{totals: array<string, float|string>, chart: array{labels: array<int, string>, incoming: array<int, float>, outgoing: array<int, float>, transfers: array<int, float>}}
     */
    public function getSummary(int $userId, string $yearMonth, int $months = 6): array
    {
        $current = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->startOfMonth();
        $previous = $current->copy()->subMonth();
        $chartFirst = Carbon::now()->startOfMonth()->subMonths($months - 1);

        $from = $chartFirst->lt($previous) ? $chartFirst : $previous;
        $to = Carbon::now()->endOfMonth()->gt($current->copy()->endOfMonth()) ? Carbon::now()->endOfMonth() : $current->copy()->endOfMonth();

        $incoming = $this->sumsByMonth('balance_incoming', $userId, $from, $to);
        $outgoing = $this->sumsByMonth('balance_outgoing', $userId, $from, $to);
        $transfer = $this->sumsByMonth('balance_transfer', $userId, $from, $to);
        $initial = (float) DB::table('accounts')->where('user_id', $userId)->sum('initial_balance');

        $balance = fn (string $month): float => (
            $this->monthSum($incoming, $month)
            + $initial
            - abs($this->monthSum($outgoing, $month))
            + $this->monthSum($transfer, $month)
        ) / self::PRECISION_DIVISOR;
        $paidIncoming = fn (string $month): float => $this->monthSum($incoming, $month, true) / self::PRECISION_DIVISOR;
        $paidFixed = fn (string $month): float => $this->monthSum($outgoing, $month, true, Transaction::TYPE_FIXEX) / self::PRECISION_DIVISOR;
        $paidVariable = fn (string $month): float => $this->monthSum($outgoing, $month, true, Transaction::TYPE_VAREX) / self::PRECISION_DIVISOR;

        $key = $current->format('Y-m');
        $previousKey = $previous->format('Y-m');

        $totals = [
            'balance' => $balance($key),
            'balance_percentage' => $this->calculatePercentage($balance($key), $balance($previousKey)),
            'incoming' => $paidIncoming($key),
            'incoming_percentage' => $this->calculatePercentage($paidIncoming($key), $paidIncoming($previousKey)),
            'fixed' => $paidFixed($key),
            'fixed_percentage' => $this->calculatePercentage($paidFixed($key), $paidFixed($previousKey)),
            'variable' => $paidVariable($key),
            'variable_percentage' => $this->calculatePercentage($paidVariable($key), $paidVariable($previousKey)),
        ];

        $chart = ['labels' => [], 'incoming' => [], 'outgoing' => [], 'transfers' => []];
        for ($i = 0; $i < $months; $i++) {
            $date = $chartFirst->copy()->addMonths($i);
            $month = $date->format('Y-m');

            $chart['labels'][] = $date->translatedFormat('M');
            $chart['incoming'][] = $paidIncoming($month);
            $chart['outgoing'][] = $paidFixed($month) + $paidVariable($month);
            $chart['transfers'][] = $this->monthSum($transfer, $month, true) / self::PRECISION_DIVISOR;
        }

        return ['totals' => $totals, 'chart' => $chart];
    }

    /**
     * Soma crua por mês/status/tipo: ['Y-m' => [status => [type => soma]]].
     *
     * @return array<string, array<int, array<string, float>>>
     */
    private function sumsByMonth(string $view, int $userId, Carbon $from, Carbon $to): array
    {
        $sums = [];

        $rows = DB::table($view)
            ->where('user_id', $userId)
            ->whereBetween('date_due', [$from, $to])
            ->groupBy('date_due', 'status', 'transaction_type')
            ->selectRaw('date_due, status, transaction_type, SUM(amount) as total')
            ->get();

        foreach ($rows as $row) {
            $month = Carbon::parse($row->date_due)->format('Y-m');
            $status = (int) $row->status;
            $sums[$month][$status][$row->transaction_type] = ($sums[$month][$status][$row->transaction_type] ?? 0.0) + (float) $row->total;
        }

        return $sums;
    }

    /**
     * @param  array<string, array<int, array<string, float>>>  $sums
     */
    private function monthSum(array $sums, string $month, bool $onlyPaid = false, ?string $type = null): float
    {
        $total = 0.0;

        foreach ($sums[$month] ?? [] as $status => $byType) {
            if ($onlyPaid && $status !== 1) {
                continue;
            }

            foreach ($byType as $transactionType => $value) {
                if ($type === null || $transactionType === $type) {
                    $total += $value;
                }
            }
        }

        return $total;
    }

    public function calculatePercentage(float $current, float $previous): string
    {
        if ($previous == 0) {
            return $current > 0 ? '+999%' : '0%';
        }

        $percentage = (($current - $previous) / abs($previous)) * 100;
        $sign = $percentage >= 0 ? '+' : '';

        return $sign.round($percentage, 1).'%';
    }

    public function getLastMonthBalancePercentage(int $userId, string $yearMonth, float $current): string
    {
        return $this->calculatePercentage($current, $this->getMonthBalance($userId, $this->previousMonth($yearMonth)));
    }

    public function getLastIncomingBalancePercentage(int $userId, string $yearMonth, float $current): string
    {
        return $this->calculatePercentage($current, $this->getIncomingBalance($userId, $this->previousMonth($yearMonth)));
    }

    public function getLastOutgoingFixedPercentage(int $userId, string $yearMonth, float $current): string
    {
        return $this->calculatePercentage($current, $this->getOutgoingFixedBalance($userId, $this->previousMonth($yearMonth)));
    }

    public function getLastOutgoingVariablePercentage(int $userId, string $yearMonth, float $current): string
    {
        return $this->calculatePercentage($current, $this->getOutgoingVariableBalance($userId, $this->previousMonth($yearMonth)));
    }

    private function previousMonth(string $yearMonth): string
    {
        return Carbon::createFromFormat('Y-m', $yearMonth)->subMonth()->format('Y-m');
    }

    private function monthBalance(string $view, int $userId, string $yearMonth, bool $onlyPaid, ?string $transactionType = null): float
    {
        $start = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->startOfMonth();

        $query = DB::table($view)
            ->where('user_id', $userId)
            ->whereBetween('date_due', [$start, $start->copy()->endOfMonth()]);

        if ($transactionType !== null) {
            $query->where('transaction_type', $transactionType);
        }

        if ($onlyPaid) {
            $query->where('status', 1);
        }

        return $query->sum('amount') / self::PRECISION_DIVISOR;
    }

    /**
     * Soma dos itens pagos por mês ('Y-m' => soma crua), em uma query por view.
     *
     * @param  array<int, string>|null  $transactionTypes
     * @return array<string, int|float>
     */
    private function paidSumsByMonth(string $view, int $userId, Carbon $from, Carbon $to, ?array $transactionTypes = null): array
    {
        $query = DB::table($view)
            ->where('user_id', $userId)
            ->where('status', 1)
            ->whereBetween('date_due', [$from, $to]);

        if ($transactionTypes !== null) {
            $query->whereIn('transaction_type', $transactionTypes);
        }

        $sums = [];
        foreach ($query->get(['date_due', 'amount']) as $row) {
            $key = Carbon::parse($row->date_due)->format('Y-m');
            $sums[$key] = ($sums[$key] ?? 0) + $row->amount;
        }

        return $sums;
    }
}
