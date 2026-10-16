<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModalidadRequest;
use App\Models\Modalidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModalidadController extends Controller
{
    public function index(): View
    {
        $modalidades = Modalidad::query()
            ->withCount('denuncias')
            ->withSum('denuncias as total_casos', 'cantidad')
            ->orderBy('nombre')
            ->get();

        return view('modalidades.index', compact('modalidades'));
    }

    public function create(): View
    {
        return view('modalidades.create', ['modalidad' => new Modalidad]);
    }

    public function store(ModalidadRequest $request): RedirectResponse
    {
        Modalidad::create($request->validated());

        return redirect()->route('modalidades.index')->with('status', 'Modalidad creada correctamente.');
    }

    public function edit(Modalidad $modalidad): View
    {
        return view('modalidades.edit', compact('modalidad'));
    }

    public function update(ModalidadRequest $request, Modalidad $modalidad): RedirectResponse
    {
        $modalidad->update($request->validated());

        return redirect()->route('modalidades.index')->with('status', 'Modalidad actualizada correctamente.');
    }

    public function destroy(Modalidad $modalidad): RedirectResponse
    {
        if ($modalidad->denuncias()->exists()) {
            return back()->with('error', 'No se puede eliminar "'.$modalidad->nombre.'": tiene denuncias asociadas.');
        }

        $modalidad->delete();

        return redirect()->route('modalidades.index')->with('status', 'Modalidad eliminada correctamente.');
    }
}
