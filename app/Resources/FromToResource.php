<?php

	namespace App\Resources;

	use Illuminate\Http\Resources\Json\JsonResource;

	class FromToResource extends JsonResource
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
				'name' => $this->name,
				'type' => $this->type,
			];
		}
	}
