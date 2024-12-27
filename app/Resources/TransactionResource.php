<?php

	namespace App\Resources;

	use Carbon\Carbon;
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
		public function toArray( Request $request )
		{
			$dateDue = Carbon::createFromFormat( 'Y-m-d', $this->date_due );
			$datePayment = $this->date_payment ? Carbon::createFromFormat( 'Y-m-d', $this->date_payment ) : null;

			$late = false;
			if ( !$this->status && $dateDue->lt( Carbon::now() ) ) {
				$late = true;
			}

			$isToday = false;
			if ( $dateDue->eq( Carbon::now() ) ) {
				$isToday = true;
			}

			$isPaidLate = false;
			if ( $this->status && $datePayment?->gt( $dateDue ) ) {
				$isPaidLate = true;
			}

			return [
				'id'               => $this->id,
				'description'      => $this->description,
				'account'          => $this->account->name,
				'from_to'          => $this->from_to->name,
				'category'         => $this->category->name,
				'transaction_type' => $this->transaction_type,
				'payment_type'     => $this->payment_type->name,
				'status'           => (bool)$this->status,
				'amount'           => $this->amount,
				'date_due'         => $dateDue->format( 'd-m' ),
				'date_payment'     => $this->date_payment,
				'is_late'          => $late,
				'is_today'         => $isToday,
				'is_paid_late'     => $isPaidLate,
			];
		}
	}
