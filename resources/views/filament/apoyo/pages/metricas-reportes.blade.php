<x-filament-panels::page>
    {{-- Selector de período --}}
    <div class="mb-6 flex items-center gap-4">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Período:</label>
        <div class="flex gap-2">
            <button wire:click="$set('periodo', 'semana')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $periodo === 'semana' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                Esta Semana
            </button>
            <button wire:click="$set('periodo', 'mes')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $periodo === 'mes' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                Este Mes
            </button>
            <button wire:click="$set('periodo', 'anio')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $periodo === 'anio' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                Este Año
            </button>
        </div>
    </div>

    @php $metricas = $this->getMetricas(); @endphp

    {{-- KPIs Principales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-teal-100 dark:bg-teal-900 rounded-lg">
                    <x-heroicon-o-document-text class="w-6 h-6 text-teal-600 dark:text-teal-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Casos Atendidos</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metricas['casosAtendidos'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sesiones Completadas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metricas['sesionesCompletadas'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                    <x-heroicon-o-hand-raised class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Intervenciones</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metricas['intervencionesTotal'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-amber-100 dark:bg-amber-900 rounded-lg">
                    <x-heroicon-o-clipboard-document-check class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Solicitudes Atendidas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metricas['solicitudesAtendidas'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Indicadores de Rendimiento --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Tasa de Asistencia</p>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-bold {{ $metricas['tasaAsistencia'] >= 80 ? 'text-emerald-600' : ($metricas['tasaAsistencia'] >= 60 ? 'text-amber-600' : 'text-red-600') }}">
                    {{ $metricas['tasaAsistencia'] }}%
                </span>
            </div>
            <div class="mt-2 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="h-2 rounded-full {{ $metricas['tasaAsistencia'] >= 80 ? 'bg-emerald-500' : ($metricas['tasaAsistencia'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                     style="width: {{ min($metricas['tasaAsistencia'], 100) }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Efectividad Intervenciones</p>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-bold {{ $metricas['tasaEfectividad'] >= 70 ? 'text-emerald-600' : ($metricas['tasaEfectividad'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                    {{ $metricas['tasaEfectividad'] }}%
                </span>
            </div>
            <div class="mt-2 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="h-2 rounded-full {{ $metricas['tasaEfectividad'] >= 70 ? 'bg-emerald-500' : ($metricas['tasaEfectividad'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                     style="width: {{ min($metricas['tasaEfectividad'], 100) }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Tiempo Prom. Respuesta</p>
            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $metricas['tiempoPromedioRespuesta'] }}h</span>
            <p class="text-xs text-gray-400 mt-1">Desde solicitud hasta asignación</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Tiempo Prom. Resolución</p>
            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $metricas['tiempoPromedioResolucion'] }}h</span>
            <p class="text-xs text-gray-400 mt-1">Desde solicitud hasta resolución</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Distribución por estado de caso --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Casos por Estado</h3>
            <div class="space-y-3">
                @forelse ($metricas['casosPorEstado'] as $estado => $total)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full
                                @switch($estado)
                                    @case('pendiente') bg-amber-500 @break
                                    @case('en_proceso') bg-blue-500 @break
                                    @case('en_seguimiento') bg-teal-500 @break
                                    @case('cerrado') bg-emerald-500 @break
                                    @case('derivado') bg-gray-500 @break
                                    @default bg-gray-400
                                @endswitch
                            "></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $estado)) }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sin datos en este período</p>
                @endforelse
            </div>
        </div>

        {{-- Distribución por tipo de violencia --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribución por Tipo</h3>
            <div class="space-y-3">
                @forelse ($metricas['distribucionTipo'] as $tipo => $total)
                    @php $maxTipo = max($metricas['distribucionTipo']); @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 dark:text-gray-300">{{ ucfirst($tipo) }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $total }}</span>
                        </div>
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-teal-500 h-2 rounded-full" style="width: {{ $maxTipo > 0 ? round(($total / $maxTipo) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sin datos en este período</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Distribución por área temática --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribución por Área Temática</h3>
            <div class="space-y-3">
                @forelse ($metricas['distribucionArea'] as $area => $total)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $area)) }}</span>
                        <span class="px-2 py-1 text-xs font-semibold bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300 rounded-full">{{ $total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sin datos en este período</p>
                @endforelse
            </div>
        </div>

        {{-- Efectividad de intervenciones --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Efectividad de Intervenciones</h3>
            <div class="space-y-3">
                @php
                    $efectividadLabels = [
                        'muy_efectiva' => ['label' => 'Muy Efectiva', 'color' => 'bg-emerald-500'],
                        'efectiva' => ['label' => 'Efectiva', 'color' => 'bg-green-500'],
                        'parcial' => ['label' => 'Parcial', 'color' => 'bg-amber-500'],
                        'sin_efecto' => ['label' => 'Sin Efecto', 'color' => 'bg-red-500'],
                        'pendiente_evaluacion' => ['label' => 'Pendiente', 'color' => 'bg-gray-400'],
                    ];
                    $maxEfectividad = !empty($metricas['efectividadData']) ? max($metricas['efectividadData']) : 1;
                @endphp
                @forelse ($metricas['efectividadData'] as $efectividad => $total)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 dark:text-gray-300">{{ $efectividadLabels[$efectividad]['label'] ?? ucfirst($efectividad) }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $total }}</span>
                        </div>
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="{{ $efectividadLabels[$efectividad]['color'] ?? 'bg-gray-400' }} h-2 rounded-full"
                                 style="width: {{ round(($total / $maxEfectividad) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sin datos en este período</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tendencia semanal --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tendencia Semanal (Últimas 8 semanas)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <th class="pb-2 pr-4">Semana</th>
                        <th class="pb-2 pr-4 text-center">Casos</th>
                        <th class="pb-2 text-center">Sesiones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($metricas['tendencia'] as $semana)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 pr-4 text-gray-700 dark:text-gray-300">{{ $semana['semana'] }}</td>
                            <td class="py-2 pr-4 text-center">
                                <span class="px-2 py-1 bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300 rounded-full text-xs font-medium">
                                    {{ $semana['casos'] }}
                                </span>
                            </td>
                            <td class="py-2 text-center">
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium">
                                    {{ $semana['sesiones'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info adicional --}}
    <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Duración Prom. Sesión</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metricas['duracionPromedio'] }} min</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Sesiones No Asistidas</p>
                <p class="text-2xl font-bold text-red-600">{{ $metricas['sesionesNoAsistio'] }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Período Seleccionado</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ match($periodo) { 'semana' => 'Semanal', 'mes' => 'Mensual', 'anio' => 'Anual', default => 'Mensual' } }}
                </p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
