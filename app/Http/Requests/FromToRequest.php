<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FromToRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:FRM,TO,FT'],
        ];
    }
}
