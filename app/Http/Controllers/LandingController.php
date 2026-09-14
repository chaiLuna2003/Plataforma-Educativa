<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $cursosDestacados = Curso::query()
            ->where('estado', 'publicado')
            ->withCount('leccionesPublicadas')
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        $whatsappNumber = (string) preg_replace(
            '/\D+/',
            '',
            (string) config('platform.whatsapp.number')
        );

        $whatsappContactUrl = $this->whatsappUrl(
            $whatsappNumber,
            'Hola, quiero solicitar información sobre la Plataforma Educativa CUCS.'
        );

        $whatsappTrialUrl = $this->whatsappUrl(
            $whatsappNumber,
            'Hola, quiero solicitar una prueba gratuita de la Plataforma Educativa CUCS.'
        );

        return view('welcome', [
            'cursosDestacados' => $cursosDestacados,
            'whatsappNumber' => $whatsappNumber,
            'whatsappContactUrl' => $whatsappContactUrl,
            'whatsappTrialUrl' => $whatsappTrialUrl,
        ]);
    }

    private function whatsappUrl(
        string $number,
        string $message
    ): string {
        return sprintf(
            'https://wa.me/%s?text=%s',
            $number,
            rawurlencode($message)
        );
    }
}
