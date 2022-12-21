<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateTransactionRequest
 * @package App\Http\Requests
 * 
 * @property integer $account_id
 * @property string $transaction_type
 * @property string $description
 * @property integer $from_id
 * @property integer $to_id
 * @property integer $category_id
 * @property integer $payment_type_id
 * @property integer $value
 * @property integer $status
 * @property string $date_due
 * @property string $date_payment
 */
class UpdateTransactionRequest extends FormRequest
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
