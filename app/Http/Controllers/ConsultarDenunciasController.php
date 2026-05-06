<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Seguimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultarDenunciasController extends Controller
{
    /**
     * Página de inicio del módulo.
     * - Si ya está autenticado como alumno/docente → redirige a la lista.
     * - Si está autenticado como otro rol → muestra aviso.
     * - Si no está autenticado → muestra formulario de login.
     */
    public function index()
    {
        if (Auth::check()) {
            $rol = Auth::user()->rol;
            if (in_array($rol, ['alumno', 'docente'])) {
                return redirect()->route('mis-denuncias.lista');
            }
            // Otro rol autenticado (admin, psicologo, asistente) → mensaje
            return view('mis-denuncias.login', [
                'rolNoPermitido' => $rol,
            ]);
        }

        return view('mis-denuncias.login', [
            'rolNoPermitido' => null,
        ]);
    }

    /**
     * Procesar el formulario de login del módulo.
     * Solo alumnos y docentes pueden acceder; los demás roles reciben error.
     */
    public function procesarLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (! in_array($user->rol, ['alumno', 'docente'])) {
                // Desautenticar inmediatamente y mostrar error
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Este módulo es exclusivo para alumnos y docentes. Usa el acceso correspondiente a tu perfil.']);
            }

            return redirect()->route('mis-denuncias.lista');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Las credenciales no son correctas. Verifica tu correo y contraseña.']);
    }

    /**
     * Lista de denuncias del usuario autenticado.
     * Solo accesible por alumno / docente.
     */
    public function lista(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! in_array($user->rol, ['alumno', 'docente'])) {
            Auth::logout();
            return redirect()->route('mis-denuncias.login')
                ->withErrors(['email' => 'Acceso no autorizado. Solo alumnos y docentes pueden acceder a este módulo.']);
        }

        $query = Caso::with(['asignado:id,name,rol'])
            ->where('denunciante_id', $user->id)
            ->orderByDesc('created_at');

        // Filtro por estado
        if ($request->filled('estado') && in_array($request->input('estado'), ['pendiente', 'en_proceso', 'resuelto', 'cerrado'])) {
            $query->where('estado', $request->input('estado'));
        }

        // Filtro por búsqueda de código
        if ($request->filled('q')) {
            $q = strtoupper(trim($request->input('q')));
            $query->where('codigo_caso', 'like', "%{$q}%");
        }

        $casos = $query->paginate(10)->withQueryString();

        // Estadísticas rápidas
        $stats = Caso::where('denunciante_id', $user->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'pendiente'  THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
                SUM(CASE WHEN estado = 'resuelto'   THEN 1 ELSE 0 END) as resueltos,
                SUM(CASE WHEN estado = 'cerrado'    THEN 1 ELSE 0 END) as cerrados
            ")->first();

        return view('mis-denuncias.index', compact('casos', 'stats', 'user'));
    }

    /**
     * Detalle de una denuncia específica.
     * Solo puede ver su propio caso (alumno / docente).
     */
    public function detalle(string $codigo)
    {
        $user = Auth::user();

        if (! $user || ! in_array($user->rol, ['alumno', 'docente'])) {
            Auth::logout();
            return redirect()->route('mis-denuncias.login');
        }

        $caso = Caso::with([
            'asignado:id,name,rol,especialidad',
            'seguimientos' => fn ($q) => $q->orderByDesc('fecha_seguimiento'),
        ])
        ->where('codigo_caso', strtoupper($codigo))
        ->where('denunciante_id', $user->id)
        ->firstOrFail();

        return view('mis-denuncias.detalle', compact('caso', 'user'));
    }

    /**
     * Cerrar sesión y redirigir al login del módulo.
     */
    public function salir(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mis-denuncias.login')
            ->with('status', 'Has cerrado sesión correctamente.');
    }
}
