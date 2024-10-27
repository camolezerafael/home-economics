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
				'description' => $this->description,
				'account' => $this->->account->name,
				'from_to_id' => $this->from_to->name,
				'category' => $this->category->name,
				'transaction_type' => $this->transaction_type,
				'payment_type' => $this->payment_type->name,
				'status' => $this->status,
				'value' => $this->value,
				'date_due' => $this->date_due,
				'date_payment' => $this->date_payment,
				'is_late' => $late,
			];
		}
	}
