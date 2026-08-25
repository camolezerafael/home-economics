<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string', 'max:60'],
            'initial_balance' => ['required', 'numeric'],
            'decimal_precision' => ['required', 'integer', 'min:1', 'max:18'],
            'type_id' => ['required', 'integer', Rule::exists('account_types', 'id')->where('user_id', auth()->id())],
        ];
    }
}
