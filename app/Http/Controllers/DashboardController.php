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

        $summary = (new Dashboard)->getSummary($userId, $yearMonth);
        $chartData = $summary['chart'];

        return Inertia::render('Dashboard', [
            'totals' => $summary['totals'],
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
