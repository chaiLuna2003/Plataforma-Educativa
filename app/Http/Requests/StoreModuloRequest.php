<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreModuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'modulo' => [
                'required',
                'array',
            ],
            'modulo.titulo' => [
                'required',
                'string',
                'max:255',
            ],
            'modulo.descripcion' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'modulo.estado' => [
                'required',
                Rule::in([
                    'borrador',
                    'publicado',
                ]),
            ],
            'modulo.orden' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'modulo.titulo.required' => 'El título del módulo es obligatorio.',
            'modulo.titulo.max' => 'El título no puede superar los 255 caracteres.',

            'modulo.descripcion.max' => 'La descripción no puede superar los 5,000 caracteres.',

            'modulo.estado.required' => 'Selecciona el estado del módulo.',
            'modulo.estado.in' => 'El estado seleccionado no es válido.',

            'modulo.orden.integer' => 'El orden debe ser un número entero.',
            'modulo.orden.min' => 'El orden no puede ser negativo.',
            'modulo.orden.max' => 'El orden no puede superar 9999.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'modulo.titulo' => 'título',
            'modulo.descripcion' => 'descripción',
            'modulo.estado' => 'estado',
            'modulo.orden' => 'orden',
        ];
    }
}
