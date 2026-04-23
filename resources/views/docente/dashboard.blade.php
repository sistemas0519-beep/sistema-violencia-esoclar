<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            Panel del Docente
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Hero Banner --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-violet-700 text-white rounded-2xl p-6 sm:p-8 shadow-lg">
                <div class="absolute top-0 right-0 -mt-6 -mr-6 w-40 h-40 sm:w-56 sm:h-56 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-purple-400/10 rounded-full blur-xl"></div>
                <div class="relative">
                    <div class="flex items-start gap-4">
                        <div class="hidden sm:flex shrink-0 w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl sm:text-3xl font-bold tracking-tight">Hola, {{ auth()->user()->name }}</h3>
                            <p class="mt-2 text-purple-100 text-sm sm:text-base max-w-lg">
                                Como docente, tu rol es clave para detectar situaciones de riesgo. Reporta incidentes que observes para proteger a los estudiantes.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-purple-200 text-sm mt-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ now()->translatedFormat('l, d \\d\\e F Y') }}
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalReportes }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Mis Reportes</p>
                </div>
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $pendientes }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Pendientes</p>
                </div>
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $enProceso }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">En Proceso</p>
                </div>
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $resueltos }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Resueltos</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Acciones y contenido principal --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Acción Principal --}}
                    <a href="{{ route('alumno.denuncia') }}"
                       class="group block relative overflow-hidden bg-white border-2 border-indigo-100 rounded-2xl p-6 sm:p-7 shadow-sm hover:shadow-lg hover:border-indigo-300 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-indigo-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative flex items-center gap-5">
                            <div class="shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200/50 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-800 text-lg sm:text-xl">Registrar un reporte</p>
                                <p class="text-sm text-gray-500 mt-1">Documenta incidentes de violencia observados en el plantel educativo</p>
                            </div>
                            <svg class="w-6 h-6 text-gray-300 group-hover:text-indigo-400 group-hover:translate-x-1 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>

                    {{-- Últimos Reportes --}}
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Mis Últimos Reportes</h3>
                        </div>

                        @if($ultimosReportes->isNotEmpty())
                            {{-- Mobile view --}}
                            <div class="sm:hidden divide-y divide-gray-50">
                                @foreach($ultimosReportes as $reporte)
                                    @php
                                        $estadoConfig = [
                                            'pendiente'  => ['label' => 'Pendiente',  'bg' => 'bg-amber-100',  'text' => 'text-amber-700',  'dot' => 'bg-amber-400'],
                                            'en_proceso' => ['label' => 'En Proceso', 'bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500'],
                                            'resuelto'   => ['label' => 'Resuelto',   'bg' => 'bg-green-100',  'text' => 'text-green-700',  'dot' => 'bg-green-500'],
                                            'cerrado'    => ['label' => 'Cerrado',    'bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'dot' => 'bg-gray-400'],
                                        ];
                                        $st = $estadoConfig[$reporte->estado] ?? $estadoConfig['pendiente'];
                                        $tipoLabel = [
                                            'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
                                            'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminación', 'otro' => 'Otro',
                                        ];
                                    @endphp
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-mono font-bold text-indigo-700 text-xs">{{ $reporte->codigo_caso }}</span>
                                            <span class="{{ $st['bg'] }} {{ $st['text'] }} text-xs font-semibold px-2 py-0.5 rounded-full flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                                {{ $st['label'] }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $tipoLabel[$reporte->tipo_violencia] ?? $reporte->tipo_violencia }} &middot; {{ $reporte->created_at->diffForHumans() }}</p>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-1">{{ $reporte->descripcion }}</p>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Desktop view --}}
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50/80">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Código</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Tipo</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Estado</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Psicólogo</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($ultimosReportes as $reporte)
                                            @php
                                                $estadoConfig = [
                                                    'pendiente'  => ['label' => 'Pendiente',  'bg' => 'bg-amber-100',  'text' => 'text-amber-700'],
                                                    'en_proceso' => ['label' => 'En Proceso', 'bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
                                                    'resuelto'   => ['label' => 'Resuelto',   'bg' => 'bg-green-100',  'text' => 'text-green-700'],
                                                    'cerrado'    => ['label' => 'Cerrado',    'bg' => 'bg-gray-100',   'text' => 'text-gray-600'],
                                                ];
                                                $st = $estadoConfig[$reporte->estado] ?? $estadoConfig['pendiente'];
                                                $tipoColor = [
                                                    'fisica' => 'bg-red-100 text-red-700', 'psicologica' => 'bg-orange-100 text-orange-700',
                                                    'verbal' => 'bg-yellow-100 text-yellow-700', 'sexual' => 'bg-pink-100 text-pink-700',
                                                    'ciberacoso' => 'bg-blue-100 text-blue-700', 'discriminacion' => 'bg-purple-100 text-purple-700',
                                                    'otro' => 'bg-gray-100 text-gray-700',
                                                ];
                                                $tipoLabel = [
                                                    'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
                                                    'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminación', 'otro' => 'Otro',
                                                ];
                                            @endphp
                                            <tr class="hover:bg-gray-50/60 transition">
                                                <td class="px-4 py-3.5 font-mono font-bold text-indigo-700 text-xs">{{ $reporte->codigo_caso }}</td>
                                                <td class="px-4 py-3.5">
                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $tipoColor[$reporte->tipo_violencia] ?? 'bg-gray-100 text-gray-700' }}">
                                                        {{ $tipoLabel[$reporte->tipo_violencia] ?? $reporte->tipo_violencia }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3.5">
                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $st['bg'] }} {{ $st['text'] }}">
                                                        {{ $st['label'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3.5 text-gray-600 text-sm">{{ $reporte->asignado?->name ?? 'Pendiente' }}</td>
                                                <td class="px-4 py-3.5 text-gray-500 text-xs">{{ $reporte->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-8 sm:p-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-50 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="font-medium text-gray-600">Aún no has registrado reportes</p>
                                <p class="text-sm text-gray-400 mt-1">Los reportes que registres aparecerán aquí</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Guía rápida --}}
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Guía del Docente</h3>
                        </div>
                        <div class="p-4 sm:p-5 space-y-4">
                            <div class="flex gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">1</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Observa</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Presta atención a cambios de conducta o señales de alerta en los estudiantes.</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xs">2</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Reporta</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Documenta el incidente con el mayor detalle posible, de forma clara y objetiva.</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-xs">3</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Acompaña</p>
                                    <p class="text-xs text-gray-500 mt-0.5">El equipo de psicología se encargará. Tú puedes apoyar desde el aula.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Información --}}
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Información</h3>
                        </div>
                        <div class="p-4 sm:p-5 space-y-3">
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-blue-50">
                                <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Confidencialidad</p>
                                    <p class="text-xs text-blue-700 mt-0.5">Todos los reportes son tratados con absoluta reserva.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-green-50">
                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-green-800">Protocolo</p>
                                    <p class="text-xs text-green-700 mt-0.5">Cada reporte sigue un protocolo de atención profesional.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-amber-50">
                                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-amber-800">Respuesta rápida</p>
                                    <p class="text-xs text-amber-700 mt-0.5">Los casos urgentes son atendidos con prioridad inmediata.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alerta --}}
                    <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-9 h-9 bg-gradient-to-br from-amber-400 to-orange-500 text-white rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-amber-900">Emergencia</p>
                                <p class="text-xs text-amber-800 mt-1">
                                    En caso de peligro inminente, contacta directamente a las autoridades de la institución.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
