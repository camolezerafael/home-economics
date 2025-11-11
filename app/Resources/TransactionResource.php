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

		// Handle transfers specially - show both accounts
		$accountDisplay = $this->account ? $this->account->name : '';
		if ( $this->transaction_type === 'TRANS' && isset( $this->account_id_from ) && isset( $this->account_id_to ) ) {
			// Use already loaded account relationships if available, otherwise load them
			$accountFrom = null;
			$accountTo = null;
			
			if ( isset( $this->account_from ) && $this->account_from ) {
				$accountFrom = $this->account_from;
			} elseif ( $this->account_id_from ) {
				$accountFrom = \App\Models\Account::find( $this->account_id_from );
			}
			
			if ( isset( $this->account_to ) && $this->account_to ) {
				$accountTo = $this->account_to;
			} elseif ( $this->account_id_to ) {
				$accountTo = \App\Models\Account::find( $this->account_id_to );
			}
			
			$fromAccount = $accountFrom ? $accountFrom->name : '';
			$toAccount = $accountTo ? $accountTo->name : '';
			$accountDisplay = $fromAccount . ' → ' . $toAccount;
		}

		return [
			'id'               => $this->id,
			'description'      => $this->description,
			'account'          => $accountDisplay,
			'from_to'          => $this->from_to ? $this->from_to->name : '',
			'category'         => $this->category ? $this->category->name : '',
			'transaction_type' => $this->transaction_type,
			'payment_type'     => $this->payment_type ? $this->payment_type->name : '',
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
