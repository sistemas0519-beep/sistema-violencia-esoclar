<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Panel del Psicólogo
            </h2>
            <a href="/apoyo"
               class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Panel de Apoyo
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 rounded-xl p-4 flex items-center gap-3 animate-fade-in">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Hero Banner --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-teal-600 via-teal-700 to-cyan-800 text-white rounded-2xl p-6 sm:p-8 shadow-lg">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 sm:w-48 sm:h-48 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-24 h-24 sm:w-36 sm:h-36 bg-cyan-400/10 rounded-full blur-xl"></div>
                <div class="relative">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-2xl sm:text-3xl font-bold tracking-tight">Bienvenido/a, {{ auth()->user()->name }}</h3>
                            <p class="mt-2 text-teal-100 text-sm sm:text-base max-w-xl">
                                Gestiona los casos asignados, realiza seguimientos y brinda el apoyo que los estudiantes necesitan.
                            </p>
                        </div>
                        <div class="flex items-center gap-2 text-teal-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ now()->translatedFormat('l, d \\d\\e F Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                {{-- Mis Asignados --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $misAsignados }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Mis Asignados</p>
                </div>

                {{-- Pendientes --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $pendientes }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Pendientes</p>
                </div>

                {{-- En Proceso --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $enProceso }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">En Proceso</p>
                </div>

                {{-- Resueltos --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $resueltos }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Resueltos</p>
                </div>

                {{-- Sin Asignar --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-red-100 hover:shadow-md transition-shadow col-span-2 sm:col-span-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-red-600">{{ $sinAsignar }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Sin Asignar</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Tabla de Casos (2/3 del ancho) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <h3 class="text-lg font-bold text-gray-800">Casos Asignados</h3>
                            <span class="text-xs text-gray-400">{{ $casos->total() }} caso(s) en total</span>
                        </div>

                        {{-- Vista Desktop: Tabla --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50/80">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Código</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Tipo</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Estado</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Denunciante</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Fecha</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-500 text-xs uppercase tracking-wider">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($casos as $caso)
                                        @php
                                            $colores = [
                                                'fisica' => 'bg-red-100 text-red-700', 'psicologica' => 'bg-orange-100 text-orange-700',
                                                'verbal' => 'bg-yellow-100 text-yellow-700', 'sexual' => 'bg-pink-100 text-pink-700',
                                                'ciberacoso' => 'bg-blue-100 text-blue-700', 'discriminacion' => 'bg-purple-100 text-purple-700',
                                                'otro' => 'bg-gray-100 text-gray-700',
                                            ];
                                            $etiquetas = [
                                                'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
                                                'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminación', 'otro' => 'Otro',
                                            ];
                                            $estadoColor = [
                                                'pendiente' => 'bg-amber-100 text-amber-700', 'en_proceso' => 'bg-blue-100 text-blue-700',
                                                'resuelto' => 'bg-green-100 text-green-700', 'cerrado' => 'bg-gray-100 text-gray-600',
                                            ];
                                            $estadoLabel = [
                                                'pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso',
                                                'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado',
                                            ];
                                        @endphp
                                        <tr class="hover:bg-gray-50/60 transition group">
                                            <td class="px-4 py-3.5">
                                                <span class="font-mono font-bold text-teal-700 text-xs">{{ $caso->codigo_caso }}</span>
                                            </td>
                                            <td class="px-4 py-3.5">
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $colores[$caso->tipo_violencia] ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ $etiquetas[$caso->tipo_violencia] ?? $caso->tipo_violencia }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5">
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $estadoColor[$caso->estado] ?? '' }}">
                                                    {{ $estadoLabel[$caso->estado] ?? $caso->estado }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5 text-gray-600 text-sm">
                                                {{ $caso->es_anonimo ? 'Anónimo' : ($caso->denunciante->name ?? '—') }}
                                            </td>
                                            <td class="px-4 py-3.5 text-gray-500 text-xs">
                                                {{ $caso->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3.5 text-center">
                                                <a href="{{ route('psicologo.caso', $caso) }}"
                                                   class="inline-flex items-center gap-1 text-teal-600 hover:text-teal-800 font-medium text-xs transition">
                                                    Ver
                                                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-12 text-center">
                                                <div class="flex flex-col items-center text-gray-400">
                                                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                    </svg>
                                                    <p class="font-medium">No hay casos por atender</p>
                                                    <p class="text-xs mt-1">Los nuevos casos aparecerán aquí</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Vista Mobile: Cards --}}
                        <div class="md:hidden divide-y divide-gray-50">
                            @forelse($casos as $caso)
                                @php
                                    $colores = [
                                        'fisica' => 'bg-red-100 text-red-700', 'psicologica' => 'bg-orange-100 text-orange-700',
                                        'verbal' => 'bg-yellow-100 text-yellow-700', 'sexual' => 'bg-pink-100 text-pink-700',
                                        'ciberacoso' => 'bg-blue-100 text-blue-700', 'discriminacion' => 'bg-purple-100 text-purple-700',
                                        'otro' => 'bg-gray-100 text-gray-700',
                                    ];
                                    $etiquetas = [
                                        'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
                                        'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminación', 'otro' => 'Otro',
                                    ];
                                    $estadoColor = [
                                        'pendiente' => 'bg-amber-100 text-amber-700', 'en_proceso' => 'bg-blue-100 text-blue-700',
                                        'resuelto' => 'bg-green-100 text-green-700', 'cerrado' => 'bg-gray-100 text-gray-600',
                                    ];
                                    $estadoLabel = [
                                        'pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso',
                                        'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado',
                                    ];
                                @endphp
                                <a href="{{ route('psicologo.caso', $caso) }}" class="block p-4 hover:bg-gray-50/60 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-mono font-bold text-teal-700 text-xs">{{ $caso->codigo_caso }}</span>
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $estadoColor[$caso->estado] ?? '' }}">
                                            {{ $estadoLabel[$caso->estado] ?? $caso->estado }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $colores[$caso->tipo_violencia] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $etiquetas[$caso->tipo_violencia] ?? $caso->tipo_violencia }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $caso->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1.5">
                                        {{ $caso->es_anonimo ? 'Anónimo' : ($caso->denunciante->name ?? '—') }}
                                    </p>
                                </a>
                            @empty
                                <div class="p-8 text-center text-gray-400">
                                    <p class="font-medium">No hay casos por atender</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Paginación --}}
                        @if($casos->hasPages())
                            <div class="px-4 sm:px-6 py-3 border-t border-gray-100 bg-gray-50/50">
                                {{ $casos->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar: Actividad Reciente --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Seguimientos Recientes --}}
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Actividad Reciente</h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @forelse($seguimientosRecientes as $seg)
                                <div class="px-4 sm:px-5 py-3.5 hover:bg-gray-50/60 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="shrink-0 w-8 h-8 rounded-full bg-teal-50 flex items-center justify-center mt-0.5">
                                            @php
                                                $accionIcons = [
                                                    'llamada' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
                                                    'reunion' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                                                    'intervencion' => 'M13 10V3L4 14h7v7l9-11h-7z',
                                                    'derivacion' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                                                    'cierre' => 'M5 13l4 4L19 7',
                                                ];
                                            @endphp
                                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $accionIcons[$seg->accion] ?? 'M9 12l2 2 4-4' }}"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-xs font-semibold text-teal-700 capitalize">{{ $seg->accion }}</span>
                                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $seg->fecha_seguimiento->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-0.5 truncate">{{ $seg->notas }}</p>
                                            @if($seg->caso)
                                                <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $seg->caso->codigo_caso }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-5 py-8 text-center text-gray-400">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs">Sin actividad reciente</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Acciones Rápidas --}}
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Acciones Rápidas</h3>
                        </div>
                        <div class="p-4 sm:p-5 space-y-2.5">
                            <a href="/apoyo/casos-sensibles"
                               class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 transition group">
                                <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center group-hover:bg-teal-200 transition">
                                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-teal-700 transition">Casos Sensibles</span>
                            </a>
                            <a href="/apoyo/calendario-sesiones"
                               class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 transition group">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700 transition">Calendario</span>
                            </a>
                            <a href="/apoyo/metricas-reportes"
                               class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-purple-50 transition group">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-purple-700 transition">Métricas</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        @keyframes fade-in { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in 0.4s ease-out; }
    </style>
</x-app-layout>
