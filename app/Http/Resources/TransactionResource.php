<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializa tanto um Transaction quanto a linha agrupada de transferência
 * produzida por TransactionReport::groupTransfers (stdClass).
 */
class TransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dateDue = Carbon::parse($this->date_due);
        $datePayment = data_get($this->resource, 'date_payment');
        $paidAt = $datePayment ? Carbon::parse($datePayment) : null;
        $status = (bool) $this->status;

        $isGroupedTransfer = data_get($this->resource, 'transfer_ids') !== null;

        $account = $isGroupedTransfer
            ? data_get($this->resource, 'account_from').' → '.data_get($this->resource, 'account_to')
            : data_get($this->resource, 'account.name', '');

        return [
            'id' => $this->id,
            'description' => $this->description,
            'account' => $account,
            'from_to' => data_get($this->resource, 'fromTo.name', ''),
            'category' => data_get($this->resource, 'category.name', ''),
            'transaction_type' => data_get($this->resource, 'transaction_type', 'TRANS'),
            'payment_type' => data_get($this->resource, 'paymentType.name', ''),
            'status' => $status,
            'amount' => $this->amount,
            'date_due' => $dateDue->format('d-m'),
            'date_payment' => $datePayment,
            'is_late' => ! $status && $dateDue->lt(Carbon::today()),
            'is_today' => $dateDue->isToday(),
            'is_paid_late' => $status && $paidAt?->gt($dateDue),
        ];
    }
}
