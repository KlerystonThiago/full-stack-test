<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'status_id' => ['required','exists:status,id'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'amount' => ['required','numeric','min:0'],
            'metadata' => ['nullable', 'array'],
            'products' => ['required','array','min:1'],
            'products.*.product_id' => ['required','exists:products,id'],
            'products.*.quantity' => ['required','integer','min:1'],
            'products.*.price' => ['required','numeric','min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Selecione um cliente',
            'status_id.required' => 'Selecione um status',
            'due_date.required' => 'Informe a data de vencimento',
            'products.required' => 'Adicione pelo menos um produto',
            'products.min' => 'Adicione pelo menos um produto',
        ];
    }
}
