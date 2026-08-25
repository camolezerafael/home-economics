<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $userId = auth()->id();
        $yearMonth = Carbon::now()->format('Y-m');

        $dashboard = new Dashboard;

        $balance = $dashboard->getMonthBalance($userId, $yearMonth);
        $incoming = $dashboard->getIncomingBalance($userId, $yearMonth);
        $fixed = $dashboard->getOutgoingFixedBalance($userId, $yearMonth);
        $variable = $dashboard->getOutgoingVariableBalance($userId, $yearMonth);

        $chartData = $dashboard->getChartData($userId);

        return Inertia::render('Dashboard', [
            'totals' => [
                'balance' => $balance,
                'balance_percentage' => $dashboard->getLastMonthBalancePercentage($userId, $yearMonth, $balance),
                'incoming' => $incoming,
                'incoming_percentage' => $dashboard->getLastIncomingBalancePercentage($userId, $yearMonth, $incoming),
                'fixed' => $fixed,
                'fixed_percentage' => $dashboard->getLastOutgoingFixedPercentage($userId, $yearMonth, $fixed),
                'variable' => $variable,
                'variable_percentage' => $dashboard->getLastOutgoingVariablePercentage($userId, $yearMonth, $variable),
            ],
            'graphBalance' => $chartData,
            'graphIncoming' => [
                'labels' => $chartData['labels'],
                'values' => $chartData['incoming'],
            ],
            'graphOutgoing' => [
                'labels' => $chartData['labels'],
                'values' => $chartData['outgoing'],
            ],
            'graphTransfers' => [
                'labels' => $chartData['labels'],
                'values' => $chartData['transfers'],
            ],
        ]);
    }
}
