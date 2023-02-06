<?php

namespace App\Models;

use App\Models\ModelBase\DashboardBase;
use App\Models\Views\BalanceIncomingView;
use App\Models\Views\BalanceOutgoingView;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Dashboard extends DashboardBase
{
	public static function getMonthBalance(Carbon $date)
	{
		return Transaction::getFullBalance($date->format('Y-m'), 'all', 'all', true)->final_balance;
	}

	public static function getLastMonthBalancePercentage(Carbon $date, float $currentMonthBalance)
	{
		$lastMonthBalance = Transaction::getFullBalance($date->subMonth()->format('Y-m'), 'all', 'all', true)->final_balance;

		return self::calculatePercentage($lastMonthBalance, $currentMonthBalance);
	}

	public static function getIncomingBalance(Carbon $date)
	{
		$dateLocal = Carbon::createFromFormat('Y-m-d', $date->format('Y-m-01'));

		return BalanceIncomingView::all()
								  ->where('user_id', Auth::id())
								  ->whereBetween('date_due', [$dateLocal->format('Y-m-01'), $dateLocal->lastOfMonth()->format('Y-m-d')])
								  // ->where('status', 1)
								  ->sum('amount');
	}

	public static function getLastIncomingBalancePercentage(Carbon $date, float $currentIncoming)
	{
		$dateLocal = Carbon::createFromFormat('Y-m-d', $date->subMonth()->format('Y-m-01'));

		$lastIncoming = BalanceIncomingView::all()
										   ->where('user_id', Auth::id())
										   ->whereBetween('date_due', [$dateLocal->format('Y-m-01'), $dateLocal->lastOfMonth()->format('Y-m-d')])
										   // ->where('status', 1)
										   ->sum('amount');

		return self::calculatePercentage($lastIncoming, $currentIncoming);
	}

	public static function getOutgoingFixedBalance(Carbon $date)
	{
		$dateLocal = Carbon::createFromFormat('Y-m-d', $date->format('Y-m-01'));

		return BalanceOutgoingView::all()
								  ->where('user_id', Auth::id())
								  ->whereBetween('date_due', [$dateLocal->format('Y-m-01'), $dateLocal->lastOfMonth()->format('Y-m-d')])
								  // ->where('status', 1)
								  ->where('transaction_type', 'FIXEX')
								  ->sum('amount');
	}

	public static function getLastOutgoingFixedPercentage(Carbon $date, float $currentOutgoingFixed)
	{
		$dateLocal = Carbon::createFromFormat('Y-m-d', $date->subMonth()->format('Y-m-01'));

		$lastOutgoingFixed = BalanceOutgoingView::all()
												->where('user_id', Auth::id())
												->whereBetween('date_due', [$dateLocal->format('Y-m-01'), $dateLocal->lastOfMonth()->format('Y-m-d')])
												// ->where('status', 1)
												->where('transaction_type', 'FIXEX')
												->sum('amount');

		return self::calculatePercentage($lastOutgoingFixed, $currentOutgoingFixed);
	}

	public static function getOutgoingVariableBalance(Carbon $date)
	{
		$dateLocal = Carbon::createFromFormat('Y-m-d', $date->format('Y-m-01'));

		return BalanceOutgoingView::all()
								  ->where('user_id', Auth::id())
								  ->whereBetween('date_due', [$dateLocal->format('Y-m-01'), $dateLocal->lastOfMonth()->format('Y-m-d')])
								  // ->where('status', 1)
								  ->where('transaction_type', 'VAREX')
								  ->sum('amount');
	}

	public static function getLastOutgoingVariablePercentage(Carbon $date, float $currentOutgoingVariable)
	{
		$dateLocal = Carbon::createFromFormat('Y-m-d', $date->subMonth()->format('Y-m-01'));

		$lastOutgoingVariable = BalanceOutgoingView::all()
												   ->where('user_id', Auth::id())
												   ->whereBetween('date_due', [$dateLocal->format('Y-m-01'), $dateLocal->lastOfMonth()->format('Y-m-d')])
												   // ->where('status', 1)
												   ->where('transaction_type', 'VAREX')
												   ->sum('amount');

		return self::calculatePercentage($lastOutgoingVariable, $currentOutgoingVariable);
	}

	public static function calculatePercentage($lastValue, $currentValue)
	{
		if ($lastValue !== 0.0 && $currentValue === 0.0) {
			return -100;
		}

		return round(($currentValue / ($lastValue ?: 1) * 100) - 100, 1);
	}

}
