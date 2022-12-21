<?php

namespace App\Http\Requests;

use App\Models\Transfer;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateTransferRequest
 * @package App\Http\Requests
 * 
 * @property integer $from_account_id
 * @property integer $to_account_id
 * @property integer $value
 */
class UpdateTransferRequest extends FormRequest
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

            'from_account_id' => [],

            'to_account_id' => [],

            'value' => [],

        ];
    }
}
