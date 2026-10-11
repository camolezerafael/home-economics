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
        [$year, $month] = explode('-', $yearMonth);

        $query = DB::table($view)
            ->where('user_id', $userId)
            ->whereYear('date_due', $year)
            ->whereMonth('date_due', $month);

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
