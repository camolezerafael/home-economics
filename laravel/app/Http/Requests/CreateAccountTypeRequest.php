<?php

namespace App\Http\Requests;

use App\Models\AccountType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateAccountTypeRequest
 * @package App\Http\Requests
 * 
 * @property string $name
 * @property string $description
 */
class CreateAccountTypeRequest extends FormRequest
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

        ];
    }
}
