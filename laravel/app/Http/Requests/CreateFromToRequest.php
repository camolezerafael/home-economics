<?php

namespace App\Http\Requests;

use App\Models\FromTo;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateFromToRequest
 * @package App\Http\Requests
 * 
 * @property string $name
 * @property string $type
 */
class CreateFromToRequest extends FormRequest
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

            'type' => [],

        ];
    }
}
