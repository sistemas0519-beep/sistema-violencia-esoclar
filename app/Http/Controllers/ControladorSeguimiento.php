<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Seguimiento;
use Illuminate\Http\Request;

class ControladorSeguimiento extends Controller
{
    /**
     * Muestra todos los casos asignados al psicólogo autenticado.
     */
    public function index()
    {
        // Usa el scope paraPsicologo, asignado se carga automáticamente en el modelo
        $casos = Caso::with('seguimientos')
            ->paraPsicologo(auth()->id())
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('psicologo.dashboard', compact('casos'));
    }

    /**
     * Muestra el detalle de un caso con sus seguimientos.
     */
    public function show(Caso $caso)
    {
        $caso->load(['denunciante', 'seguimientos.responsable']);
        return view('psicologo.caso', compact('caso'));
    }

    /**
     * Registra un nuevo seguimiento y actualiza el estado del caso.
     */
    public function registrarSeguimiento(Request $request, Caso $caso)
    {
        $request->validate([
            'notas'      => 'required|min:5|max:2000',
            'accion'     => 'required|in:llamada,reunion,intervencion,derivacion,cierre,otro',
            'nuevo_estado'=> 'required|in:pendiente,en_proceso,resuelto,cerrado',
        ]);

        // Insertar seguimiento
        Seguimiento::create([
            'caso_id'          => $caso->id,
            'responsable_id'   => auth()->id(),
            'notas'            => $request->notas,
            'accion'           => $request->accion,
            'fecha_seguimiento'=> now(),
        ]);

        // Actualizar estado del caso
        $caso->update([
            'estado'     => $request->nuevo_estado,
            'asignado_a' => $caso->asignado_a ?? auth()->id(),
        ]);

        return redirect()
            ->route('psicologo.caso', $caso)
            ->with('success', 'Seguimiento registrado con éxito.');
    }

    /**
     * Asigna un caso al psicólogo autenticado.
     */
    public function asignar(Caso $caso)
    {
        $caso->update([
            'asignado_a' => auth()->id(),
            'estado'     => 'en_proceso',
        ]);

        return redirect()
            ->route('psicologo.caso', $caso)
            ->with('success', 'Caso asignado a ti correctamente.');
    }
}
