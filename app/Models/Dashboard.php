<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard
{
    public function getMonthBalance(int $userId, string $yearMonth, bool $backwardsBalance = false): float
    {
        $balance = Transaction::getFullBalance($yearMonth, 'all', 'all', $backwardsBalance);

        return $balance['final_balance'];
    }

    private static function monthFilter(string $yearMonth): array
    {
        [$year, $month] = explode('-', $yearMonth);

        return [fn ($q) => $q->whereYear('date_due', $year)->whereMonth('date_due', $month)];
    }

    public function getIncomingBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        [$year, $month] = explode('-', $yearMonth);

        $query = DB::table('balance_incoming')
            ->where('user_id', $userId)
            ->whereYear('date_due', $year)
            ->whereMonth('date_due', $month);

        if ($onlyPaid) {
            $query->where('status', 1);
        }

        return $query->sum('amount') / 100;
    }

    public function getOutgoingFixedBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        [$year, $month] = explode('-', $yearMonth);

        $query = DB::table('balance_outgoing')
            ->where('user_id', $userId)
            ->whereYear('date_due', $year)
            ->whereMonth('date_due', $month)
            ->where('transaction_type', 'FIXEX');

        if ($onlyPaid) {
            $query->where('status', 1);
        }

        return $query->sum('amount') / 100;
    }

    public function getOutgoingVariableBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        [$year, $month] = explode('-', $yearMonth);

        $query = DB::table('balance_outgoing')
            ->where('user_id', $userId)
            ->whereYear('date_due', $year)
            ->whereMonth('date_due', $month)
            ->where('transaction_type', 'VAREX');

        if ($onlyPaid) {
            $query->where('status', 1);
        }

        return $query->sum('amount') / 100;
    }

    public function getPeopleBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        [$year, $month] = explode('-', $yearMonth);

        $query = DB::table('balance_outgoing')
            ->where('user_id', $userId)
            ->whereYear('date_due', $year)
            ->whereMonth('date_due', $month)
            ->where('transaction_type', 'PEOPL');

        if ($onlyPaid) {
            $query->where('status', 1);
        }

        return $query->sum('amount') / 100;
    }

    public function getTaxesBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        [$year, $month] = explode('-', $yearMonth);

        $query = DB::table('balance_outgoing')
            ->where('user_id', $userId)
            ->whereYear('date_due', $year)
            ->whereMonth('date_due', $month)
            ->where('transaction_type', 'TAXES');

        if ($onlyPaid) {
            $query->where('status', 1);
        }

        return $query->sum('amount') / 100;
    }

    public function getTransferBalance(int $userId, string $yearMonth, bool $onlyPaid = true): float
    {
        [$year, $month] = explode('-', $yearMonth);

        $query = DB::table('balance_transfer')
            ->where('user_id', $userId)
            ->whereYear('date_due', $year)
            ->whereMonth('date_due', $month);

        if ($onlyPaid) {
            $query->where('status', 1);
        }

        return $query->sum('amount') / 100;
    }

    public function getChartData(int $userId, int $months = 6): array
    {
        $labels = [];
        $incoming = [];
        $outgoing = [];
        $transfers = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $yearMonth = $date->format('Y-m');
            $labels[] = $date->translatedFormat('M');

            $incoming[] = $this->getIncomingBalance($userId, $yearMonth);
            $outgoing[] = $this->getOutgoingFixedBalance($userId, $yearMonth) + $this->getOutgoingVariableBalance($userId, $yearMonth);
            $transfers[] = $this->getTransferBalance($userId, $yearMonth);
        }

        return [
            'labels' => $labels,
            'incoming' => $incoming,
            'outgoing' => $outgoing,
            'transfers' => $transfers,
        ];
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
        $date = Carbon::createFromFormat('Y-m', $yearMonth)->subMonth()->format('Y-m');
        $previous = $this->getMonthBalance($userId, $date);

        return $this->calculatePercentage($current, $previous);
    }

    public function getLastIncomingBalancePercentage(int $userId, string $yearMonth, float $current): string
    {
        $date = Carbon::createFromFormat('Y-m', $yearMonth)->subMonth()->format('Y-m');
        $previous = $this->getIncomingBalance($userId, $date);

        return $this->calculatePercentage($current, $previous);
    }

    public function getLastOutgoingFixedPercentage(int $userId, string $yearMonth, float $current): string
    {
        $date = Carbon::createFromFormat('Y-m', $yearMonth)->subMonth()->format('Y-m');
        $previous = $this->getOutgoingFixedBalance($userId, $date);

        return $this->calculatePercentage($current, $previous);
    }

    public function getLastOutgoingVariablePercentage(int $userId, string $yearMonth, float $current): string
    {
        $date = Carbon::createFromFormat('Y-m', $yearMonth)->subMonth()->format('Y-m');
        $previous = $this->getOutgoingVariableBalance($userId, $date);

        return $this->calculatePercentage($current, $previous);
    }
}
