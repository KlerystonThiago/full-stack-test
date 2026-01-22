<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'personal_team' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'O administrador do team é obrigatório.',
            'user_id.exists' => 'O usuário selecionado não existe.',

            'name.required' => 'O nome do team é obrigatório.',
            'name.string' => 'O nome do team deve ser um texto válido.',
            'name.max' => 'O nome do team não pode ter mais de 255 caracteres.',

            'personal_team.boolean' => 'O campo personal team deve ser verdadeiro ou falso.',
        ];
    }
}
