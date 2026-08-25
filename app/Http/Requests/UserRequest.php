<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
        ];

        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', 'string', 'min:5', 'max:255', 'confirmed'];
        } elseif ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            $rules['password'] = ['nullable', 'string', 'min:5', 'max:255', 'confirmed'];
        }

        return $rules;
    }
}
