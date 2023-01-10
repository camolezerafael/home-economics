<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
		return [
			'account_id' => [],
			'transaction_type' => [],
			'description' => [],
			'from_id' => [],
			'to_id' => [],
			'category_id' => [],
			'payment_type_id' => [],
			'value' => [],
			'status' => [],
			'date_due' => [],
			'date_payment' => [],
		];
    }
}
