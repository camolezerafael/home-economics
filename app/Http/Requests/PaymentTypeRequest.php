<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:30', Rule::unique('payment_types', 'name')->where('user_id', auth()->id())->ignore($this->route('payment_type'))],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
