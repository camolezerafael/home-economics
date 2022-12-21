<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateAccountRequest
 * @package App\Http\Requests
 * 
 * @property string $name
 * @property string $description
 * @property integer $initial_balance
 * @property integer $decimal_precision
 * @property integer $type_id
 */
class UpdateAccountRequest extends FormRequest
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

            'name' => [],

            'description' => [],

            'initial_balance' => [],

            'decimal_precision' => [],

            'type_id' => [],

        ];
    }
}
