<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'email', 
                'max:255', 
                Rule::unique('customers', 'email')->ignore($this->customer->id)
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'document' => ['nullable', 'string', 'max:100'],
        ];
        
        if (auth()->user()->role_id === 1) {
            $rules['team_id'] = ['required', 'exists:teams,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do cliente é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            'email.required' => 'O e-mail do cliente é obrigatório.',
            'email.email' => 'O e-mail deve ser um endereço válido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado.',

            'phone.string' => 'O telefone deve ser um texto válido.',
            'phone.max' => 'O telefone não pode ter mais de 50 caracteres.',

            'address.string' => 'O endereço deve ser um texto válido.',
            'address.max' => 'O endereço não pode ter mais de 500 caracteres.',

            'document.string' => 'O documento deve ser um texto válido.',
            'document.max' => 'O documento não pode ter mais de 100 caracteres.',

            'team_id.required' => 'Selecione o team do cliente.',
            'team_id.exists' => 'O team selecionado não existe.',
        ];
    }
}
