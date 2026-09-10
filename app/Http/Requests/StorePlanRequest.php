<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo']),
            ],
            'orden' => ['nullable', 'integer', 'min:0'],
            'cursos' => ['nullable', 'array'],
            'cursos.*' => [
                'integer',
                'distinct',
                'exists:cursos,id',
            ],
        ];
    }
}
