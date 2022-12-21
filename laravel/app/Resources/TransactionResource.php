<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [

            'user_id' => $this->user_id,

            'account_id' => $this->account_id,

            'transaction_type' => $this->transaction_type,

            'description' => $this->description,

            'from_id' => $this->from_id,

            'to_id' => $this->to_id,

            'category_id' => $this->category_id,

            'payment_type_id' => $this->payment_type_id,

            'value' => $this->value,

            'status' => $this->status,

            'date_due' => $this->date_due,

            'date_payment' => $this->date_payment,

        ];
    }
}
