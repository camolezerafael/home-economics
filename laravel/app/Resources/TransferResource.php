<?php

	namespace App\Resources;

	use Illuminate\Http\Resources\Json\JsonResource;

	class TransferResource extends JsonResource
	{
		/**
		 * Transform the resource into an array.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
		 */
		public function toArray( $request )
		{
			return [
				'from_account_id' => $this->from_account_id,
				'to_account_id'   => $this->to_account_id,
				'value'           => $this->value,
			];
		}
	}
