<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCursoRequest;
use App\Http\Requests\UpdateCursoRequest;
use App\Models\Curso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CursoController extends Controller
{
    public function index(): View
    {
        $cursos = Curso::query()
            ->with('creador:id,name')
            ->withCount('modulos')
            ->orderBy('orden')
            ->latest('id')
            ->paginate(12);

        return view('admin.cursos.index', compact('cursos'));
    }

    public function create(): View
    {
        return view('admin.cursos.create');
    }

    public function store(StoreCursoRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        unset($datos['imagen']);

        $datos['slug'] = $this->generarSlugUnico($datos['titulo']);
        $datos['creado_por'] = $request->user()->id;
        $datos['orden'] = $datos['orden'] ?? 0;

        if (($datos['estado'] ?? 'borrador') === 'publicado') {
            $datos['publicado_at'] = now();
        }

        if ($request->hasFile('imagen')) {
            $datos['imagen_path'] = $request
                ->file('imagen')
                ->store('cursos/portadas', 'public');
        }

        $curso = Curso::create($datos);

        return redirect()
            ->route('admin.cursos.edit', $curso)
            ->with('success', 'Curso creado correctamente.');
    }

    public function show(Curso $curso): View
    {
        $curso->load([
            'creador:id,name',
            'modulos' => fn ($query) => $query->withCount('lecciones'),
        ]);

        return view('admin.cursos.show', compact('curso'));
    }

    public function edit(Curso $curso): View
    {
        $curso->load([
            'modulos' => fn ($query) => $query->withCount('lecciones'),
        ]);

        return view('admin.cursos.edit', compact('curso'));
    }

    public function update(
        UpdateCursoRequest $request,
        Curso $curso
    ): RedirectResponse {
        $datos = $request->validated();

        unset($datos['imagen']);

        if ($curso->titulo !== $datos['titulo']) {
            $datos['slug'] = $this->generarSlugUnico(
                $datos['titulo'],
                $curso
            );
        }

        $datos['orden'] = $datos['orden'] ?? 0;

        if (
            ($datos['estado'] ?? 'borrador') === 'publicado'
            && $curso->publicado_at === null
        ) {
            $datos['publicado_at'] = now();
        }

        if ($request->hasFile('imagen')) {
            $imagenAnterior = $curso->imagen_path;

            $datos['imagen_path'] = $request
                ->file('imagen')
                ->store('cursos/portadas', 'public');

            $curso->update($datos);

            if ($imagenAnterior) {
                Storage::disk('public')->delete($imagenAnterior);
            }
        } else {
            $curso->update($datos);
        }

        return redirect()
            ->route('admin.cursos.edit', $curso)
            ->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy(Curso $curso): RedirectResponse
    {
        if ($curso->modulos()->exists()) {
            return redirect()
                ->route('admin.cursos.index')
                ->with(
                    'error',
                    'No puedes eliminar un curso que contiene módulos. Archívalo primero.'
                );
        }

        if ($curso->imagen_path) {
            Storage::disk('public')->delete($curso->imagen_path);
        }

        $curso->delete();

        return redirect()
            ->route('admin.cursos.index')
            ->with('success', 'Curso eliminado correctamente.');
    }

    private function generarSlugUnico(
        string $titulo,
        ?Curso $cursoIgnorado = null
    ): string {
        $slugBase = Str::slug($titulo);

        if ($slugBase === '') {
            $slugBase = 'curso';
        }

        $slug = $slugBase;
        $contador = 2;

        while ($this->slugExiste($slug, $cursoIgnorado)) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        return $slug;
    }

    private function slugExiste(
        string $slug,
        ?Curso $cursoIgnorado = null
    ): bool {
        return Curso::query()
            ->where('slug', $slug)
            ->when(
                $cursoIgnorado,
                fn ($query) => $query->where(
                    'id',
                    '!=',
                    $cursoIgnorado->id
                )
            )
            ->exists();
    }
}