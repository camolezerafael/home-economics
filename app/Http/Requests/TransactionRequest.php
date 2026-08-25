<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', auth()->id()),
            ],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('user_id', auth()->id()),
            ],
            'from_to_id' => [
                'nullable',
                'integer',
                Rule::exists('from_to', 'id')->where('user_id', auth()->id()),
            ],
            'payment_type_id' => [
                'required',
                'integer',
                Rule::exists('payment_types', 'id')->where('user_id', auth()->id()),
            ],
            'transaction_type' => [
                'required',
                'string',
                Rule::in(Transaction::TYPES),
            ],
            'description' => ['required', 'string', 'max:30'],
            'amount' => ['required', 'numeric'],
            'date_due' => ['required', 'date_format:Y-m-d'],
            'status' => ['sometimes', 'boolean'],
        ];

        if ($this->input('transaction_type') === Transaction::TYPE_TRANS) {
            $rules['account_to_id'] = [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', auth()->id()),
                'different:account_id',
            ];
        }

        return $rules;
    }
}
