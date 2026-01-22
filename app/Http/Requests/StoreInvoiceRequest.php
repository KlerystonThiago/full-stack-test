<?php
// app/Http/Requests/StoreInvoiceRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'status_id' => ['required', 'exists:status,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.price' => ['required', 'numeric', 'min:0'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
        
        if (auth()->user()->role_id === 1) {
            $rules['team_id'] = ['required', 'exists:teams,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Selecione um cliente.',
            'customer_id.exists' => 'Cliente inválido.',
            'status_id.required' => 'Selecione um status.',
            'status_id.exists' => 'Status inválido.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'due_date.required' => 'A data de vencimento é obrigatória.',
            'due_date.date' => 'Data de vencimento inválida.',
            'products.required' => 'Adicione pelo menos um produto.',
            'products.min' => 'Adicione pelo menos um produto.',
            'team_id.required' => 'Selecione o team da fatura.',
            'team_id.exists' => 'Team inválido.',
        ];
    }
}
