<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $leccion = $this->input('leccion', []);

        $leccion['es_muestra'] = $this->boolean(
            'leccion.es_muestra'
        );

        $this->merge([
            'leccion' => $leccion,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'leccion' => [
                'required',
                'array',
            ],
            'leccion.titulo' => [
                'required',
                'string',
                'max:255',
            ],
            'leccion.descripcion' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'leccion.vimeo_url' => [
                'required',
                'url',
                function (
                    string $attribute,
                    mixed $value,
                    Closure $fail
                ): void {
                    if (! is_string($value)) {
                        return;
                    }

                    $host = strtolower(
                        parse_url($value, PHP_URL_HOST) ?? ''
                    );

                    $hostsPermitidos = [
                        'vimeo.com',
                        'www.vimeo.com',
                        'player.vimeo.com',
                    ];

                    $path = parse_url($value, PHP_URL_PATH) ?? '';

                    if (
                        ! in_array($host, $hostsPermitidos, true)
                        || ! preg_match('/\/\d+/', $path)
                    ) {
                        $fail(
                            'Ingresa una URL válida de un video de Vimeo.'
                        );
                    }
                },
            ],
            'leccion.duracion_minutos' => [
                'nullable',
                'integer',
                'min:1',
                'max:600',
            ],
            'leccion.es_muestra' => [
                'required',
                'boolean',
            ],
            'leccion.estado' => [
                'required',
                Rule::in([
                    'borrador',
                    'publicado',
                ]),
            ],
            'leccion.orden' => [
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
            'leccion.titulo.required' => 'El título de la lección es obligatorio.',
            'leccion.titulo.max' => 'El título no puede superar los 255 caracteres.',

            'leccion.descripcion.max' => 'La descripción no puede superar los 5,000 caracteres.',

            'leccion.vimeo_url.required' => 'La URL del video de Vimeo es obligatoria.',
            'leccion.vimeo_url.url' => 'Ingresa una URL válida.',

            'leccion.duracion_minutos.integer' => 'La duración debe expresarse en minutos enteros.',
            'leccion.duracion_minutos.min' => 'La duración debe ser de al menos un minuto.',
            'leccion.duracion_minutos.max' => 'La duración no puede superar los 600 minutos.',

            'leccion.estado.required' => 'Selecciona el estado de la lección.',
            'leccion.estado.in' => 'El estado seleccionado no es válido.',

            'leccion.orden.integer' => 'El orden debe ser un número entero.',
            'leccion.orden.min' => 'El orden no puede ser negativo.',
            'leccion.orden.max' => 'El orden no puede superar 9999.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'leccion.titulo' => 'título',
            'leccion.descripcion' => 'descripción',
            'leccion.vimeo_url' => 'URL de Vimeo',
            'leccion.duracion_minutos' => 'duración',
            'leccion.es_muestra' => 'lección de muestra',
            'leccion.estado' => 'estado',
            'leccion.orden' => 'orden',
        ];
    }
}
