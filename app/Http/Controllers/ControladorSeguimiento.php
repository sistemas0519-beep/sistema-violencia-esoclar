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
        $userId = auth()->id();

        $casos = Caso::with('seguimientos')
            ->paraPsicologo($userId)
            ->orderByDesc('created_at')
            ->paginate(12);

        $misAsignados = Caso::where('asignado_a', $userId)->count();
        $pendientes = Caso::where('asignado_a', $userId)->where('estado', 'pendiente')->count();
        $enProceso = Caso::where('asignado_a', $userId)->where('estado', 'en_proceso')->count();
        $resueltos = Caso::where('asignado_a', $userId)->where('estado', 'resuelto')->count();
        $sinAsignar = Caso::whereNull('asignado_a')->where('estado', 'pendiente')->count();

        $seguimientosRecientes = Seguimiento::with(['caso', 'responsable'])
            ->where('responsable_id', $userId)
            ->orderByDesc('fecha_seguimiento')
            ->limit(5)
            ->get();

        return view('psicologo.dashboard', compact(
            'casos', 'misAsignados', 'pendientes', 'enProceso', 'resueltos',
            'sinAsignar', 'seguimientosRecientes'
        ));
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
