<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Curso;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $planes = Plan::query()
            ->withCount(['cursos', 'usuarios'])
            ->orderBy('orden')
            ->orderBy('id')
            ->paginate(12);

        return view('admin.planes.index', compact('planes'));
    }

    public function create(): View
    {
        $cursos = $this->obtenerCursos();

        return view('admin.planes.create', compact('cursos'));
    }

    public function store(StorePlanRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $cursoIds = $datos['cursos'] ?? [];

        unset($datos['cursos']);

        $datos['slug'] = $this->generarSlugUnico($datos['nombre']);
        $datos['orden'] = $datos['orden'] ?? 0;

        $plan = DB::transaction(function () use ($datos, $cursoIds) {
            $plan = Plan::create($datos);

            $plan->cursos()->sync($cursoIds);

            return $plan;
        });

        return redirect()
            ->route('admin.planes.edit', $plan)
            ->with('success', 'Plan creado correctamente.');
    }

    public function edit(Plan $plan): View
    {
        $plan->load('cursos:id');
        $cursos = $this->obtenerCursos();

        return view(
            'admin.planes.edit',
            compact('plan', 'cursos')
        );
    }

    public function update(
        UpdatePlanRequest $request,
        Plan $plan
    ): RedirectResponse {
        $datos = $request->validated();
        $cursoIds = $datos['cursos'] ?? [];

        unset($datos['cursos']);

        if ($plan->nombre !== $datos['nombre']) {
            $datos['slug'] = $this->generarSlugUnico(
                $datos['nombre'],
                $plan
            );
        }

        $datos['orden'] = $datos['orden'] ?? 0;

        DB::transaction(function () use (
            $plan,
            $datos,
            $cursoIds
        ): void {
            $plan->update($datos);
            $plan->cursos()->sync($cursoIds);
        });

        return redirect()
            ->route('admin.planes.edit', $plan)
            ->with('success', 'Plan actualizado correctamente.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->usuarios()->exists()) {
            return redirect()
                ->route('admin.planes.index')
                ->with(
                    'error',
                    'No puedes eliminar un plan que tiene usuarios asignados.'
                );
        }

        $plan->delete();

        return redirect()
            ->route('admin.planes.index')
            ->with('success', 'Plan eliminado correctamente.');
    }

    /**
     * @return Collection<int, Curso>
     */
    private function obtenerCursos()
    {
        return Curso::query()
            ->orderBy('orden')
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'estado']);
    }

    private function generarSlugUnico(
        string $nombre,
        ?Plan $planIgnorado = null
    ): string {
        $slugBase = Str::slug($nombre);

        if ($slugBase === '') {
            $slugBase = 'plan';
        }

        $slug = $slugBase;
        $contador = 2;

        while ($this->slugExiste($slug, $planIgnorado)) {
            $slug = $slugBase.'-'.$contador;
            $contador++;
        }

        return $slug;
    }

    private function slugExiste(
        string $slug,
        ?Plan $planIgnorado = null
    ): bool {
        return Plan::query()
            ->where('slug', $slug)
            ->when(
                $planIgnorado,
                fn ($query) => $query->where(
                    'id',
                    '!=',
                    $planIgnorado->id
                )
            )
            ->exists();
    }
}
