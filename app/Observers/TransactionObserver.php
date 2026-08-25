<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Carbon;

class TransactionObserver
{
    public function saving(Transaction $transaction): void
    {
        if ($transaction->isDirty('status')) {
            $wasPaid = $transaction->getOriginal('status');
            $isPaid = $transaction->status;

            if ($isPaid && ! $wasPaid) {
                $transaction->date_payment = Carbon::now();
            } elseif (! $isPaid && $wasPaid) {
                $transaction->date_payment = null;
            }
        }
    }

    public function creating(Transaction $transaction): void
    {
        if ($transaction->isTransfer()) {
            $transaction->status = true;
            $transaction->date_payment = $transaction->date_due ?? Carbon::now();
        }
    }
}
