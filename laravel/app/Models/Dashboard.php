<?php

namespace App\Models;

use App\Models\ModelBase\DashboardBase;

class Dashboard extends DashboardBase
{
	public function getMonthBalance(){
		return Transaction::getFullBalance(\Carbon\Carbon::now()->format('Y-m'), 'all', true);
	}

	public function getIncomingBalance(){

	}
}
