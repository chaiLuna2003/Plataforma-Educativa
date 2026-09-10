<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCursoRequest extends FormRequest
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
            'titulo' => [
                'required',
                'string',
                'max:255',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:10000',
            ],
            'imagen' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
            'nivel' => [
                'required',
                Rule::in([
                    'basico',
                    'intermedio',
                    'avanzado',
                ]),
            ],
            'estado' => [
                'required',
                Rule::in([
                    'borrador',
                    'publicado',
                    'archivado',
                ]),
            ],
            'orden' => [
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
            'titulo.required' => 'El título del curso es obligatorio.',
            'titulo.max' => 'El título no puede superar los 255 caracteres.',

            'descripcion.max' => 'La descripción no puede superar los 10,000 caracteres.',

            'imagen.image' => 'La portada debe ser una imagen válida.',
            'imagen.mimes' => 'La portada debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La portada no puede pesar más de 4 MB.',

            'nivel.required' => 'Selecciona el nivel del curso.',
            'nivel.in' => 'El nivel seleccionado no es válido.',

            'estado.required' => 'Selecciona el estado del curso.',
            'estado.in' => 'El estado seleccionado no es válido.',

            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden no puede ser negativo.',
            'orden.max' => 'El orden no puede superar 9999.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'descripcion' => 'descripción',
            'imagen' => 'portada',
        ];
    }
}
