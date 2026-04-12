<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ControladorDenuncias extends Controller
{
    /**
     * Muestra el formulario de nueva denuncia (solo alumnos/docentes).
     */
    public function index()
    {
        return view('alumno.denuncia');
    }

    /**
     * Persiste la denuncia en la base de datos usando Eloquent.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_violencia' => 'required|in:fisica,psicologica,verbal,sexual,ciberacoso,discriminacion,otro',
            'descripcion'    => 'required|min:10|max:3000',
            'es_anonimo'     => 'nullable|boolean',
            'fecha_incidente'=> 'nullable|date|before_or_equal:today',
        ]);

        $esAnonimo = (bool) $request->input('es_anonimo', false);

        Caso::create([
            'codigo_caso'    => 'VIO-' . now()->format('Y') . '-' . strtoupper(Str::random(6)),
            'tipo_violencia' => $request->tipo_violencia,
            'descripcion'    => $request->descripcion,
            'estado'         => 'pendiente',
            'es_anonimo'     => $esAnonimo,
            'denunciante_id' => $esAnonimo ? null : auth()->id(),
            'fecha_incidente'=> $request->fecha_incidente,
        ]);

        return redirect()
            ->route('alumno.mis-casos')
            ->with('success', '✅ Tu reporte ha sido enviado con éxito. Un psicólogo lo revisará pronto.');
    }

    /**
     * Muestra los casos propios del alumno autenticado (no anónimos).
     */
    public function misCasos()
    {
        // delAlumno scope + seguimientos (responsable ya viene en $with del modelo)
        $casos = Caso::with('seguimientos')
            ->delAlumno(auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('alumno.mis-casos', compact('casos'));
    }
}
