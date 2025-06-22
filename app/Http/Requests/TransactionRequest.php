<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payer_wallet_id' => 'required|exists:wallets,id',
            'payee_wallet_id' => 'required|exists:wallets,id|different:payer_wallet_id',
            'value' => 'required|numeric|min:0.01'
        ];
    }

    public function messages(): array
    {
        return [
            'payer_wallet_id.required' => 'Payer wallet ID is required.',
            'payee_wallet_id.required' => 'Payee wallet ID is required.',
            'payee_wallet_id.different' => 'Payer and payee wallets cannot be the same.',
            'value.required' => 'Transaction value is required.',
            'value.numeric' => 'Transaction value must be a number.',
            'value.min' => 'Transaction value must be at least 0.01.'
        ];
    }
}
