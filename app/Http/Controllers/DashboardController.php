<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
	public function index()
	{
		$totals        = $this->getTotalsValues();
		$graphBalance  = $this->getGraphBalanceValues();
		$graphIncoming = $this->getGraphIncomingValues();
		$graphOutgoing = $this->getGraphOutgoingValues();

		return view('dashboard.index', compact('totals', 'graphBalance', 'graphIncoming', 'graphOutgoing'));
	}

	public function getTotalsValues()
	{
		$baseDate = Carbon::now();

		$totals['balance']            = Dashboard::getMonthBalance($baseDate);
		$totals['balance_percentage'] = Dashboard::getLastMonthBalancePercentage($baseDate, $totals['balance']);

		$totals['incoming']            = Dashboard::getIncomingBalance($baseDate);
		$totals['incoming_percentage'] = Dashboard::getLastIncomingBalancePercentage($baseDate, $totals['incoming']);

		$totals['fixed']            = Dashboard::getOutgoingFixedBalance($baseDate);
		$totals['fixed_percentage'] = Dashboard::getLastOutgoingFixedPercentage($baseDate, $totals['fixed']);

		$totals['variable']            = Dashboard::getOutgoingVariableBalance($baseDate);
		$totals['variable_percentage'] = Dashboard::getLastOutgoingVariablePercentage($baseDate, $totals['variable']);

		return $totals;
	}

	public function getGraphBalanceValues()
	{
		for ($m = 5; $m >= 0; $m--) {
			$date = Carbon::now()->firstOfMonth()->subMonths($m);

			$result['values'][] = Dashboard::getMonthBalance($date);
			$result['labels'][] = Str::of($date->translatedFormat('M'))->headline()->toString();
		}

		return $result;
	}


	public function getGraphIncomingValues()
	{
		for ($m = 5; $m >= 0; $m--) {
			$date = Carbon::now()->firstOfMonth()->subMonths($m);

			$result['values'][] = Dashboard::getIncomingBalance($date);
			$result['labels'][] = Str::of($date->translatedFormat('M'))->headline()->toString();
		}

		return $result;
	}


	public function getGraphOutgoingValues()
	{
		for ($m = 5; $m >= 0; $m--) {
			$date = Carbon::now()->firstOfMonth()->subMonths($m);

			$result['values'][] = Dashboard::getOutgoingFixedBalance($date) + Dashboard::getOutgoingVariableBalance($date);
			$result['labels'][] = Str::of($date->translatedFormat('M'))->headline()->toString();
		}

		return $result;
	}
}
