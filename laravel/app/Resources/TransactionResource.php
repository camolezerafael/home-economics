<?php

	namespace App\Resources;

	use Illuminate\Http\Request;
	use Illuminate\Http\Resources\Json\JsonResource;

	class TransactionResource extends JsonResource
	{
		/**
		 * Transform the resource into an array.
		 *
		 * @param Request $request
		 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
		 */
		public function toArray(Request $request )
		{
			$dateDue = \Carbon\Carbon::createFromFormat('Y-m-d', $this->date_due);

			$late = false;
			if(!$this->status && $dateDue->lt(\Carbon\Carbon::now())){
				$late = true;
			}

			return [
				'id' => $this->id,
				'description' => $this->description,
				'account' => $this->account->name,
				'from_to' => $this->from_to->name,
				'category' => $this->category->name,
				'transaction_type' => $this->transaction_type,
				'payment_type' => $this->payment_type->name,
				'paid' => (bool)$this->status,
				'amount' => ($this->amount / (10 ** $this->account->decimal_precision)),
				'date_due' => $dateDue->format( 'd-m'),
				'date_payment' => $this->date_payment,
				'is_late' => $late,
			];
		}
	}
