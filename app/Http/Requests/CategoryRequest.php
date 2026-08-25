<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('categories', 'name')->where('user_id', auth()->id())->ignore($this->route('category'))],
            'description' => ['nullable', 'string', 'min:3', 'max:255'],
        ];
    }
}
