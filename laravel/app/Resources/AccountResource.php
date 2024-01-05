<?php

	namespace App\Resources;

	use Illuminate\Http\Resources\Json\JsonResource;

	class AccountResource extends JsonResource
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
				'user_id' => $this->user_id,
				'name' => $this->name,
				'description' => $this->description,
				'initial_balance' => $this->initial_balance,
				'decimal_precision' => $this->decimal_precision,
				'type_id' => $this->type_id,
			];
		}

		public function paginationInformation( $request, $paginated, $default )
		{
			$default[ 'links' ][ 0 ][ 'label' ]                                = __( 'Previous' );
			$default[ 'links' ][ count( $default[ 'links' ] ) - 1 ][ 'label' ] = __( 'Next' );

			return $default;
		}
	}
