<x-filament-panels::page>
<div wire:poll.15s wire:key="monitoreo-panel">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl mb-8 bg-gradient-to-br from-cyan-600 via-blue-600 to-indigo-700 shadow-xl">
        <div class="absolute inset-0 opacity-[.06]" style="background-image:radial-gradient(circle at 30% 50%,white 1px,transparent 1px);background-size:24px 24px"></div>
        <div class="relative px-8 py-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center">
                    <x-heroicon-o-computer-desktop class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Centro de Monitoreo</h2>
                    <p class="text-blue-200 text-sm">Métricas y estado del sistema en tiempo real</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white/10 text-white text-sm px-4 py-2 rounded-xl">
                    <x-heroicon-o-clock class="w-4 h-4" />
                    {{ now()->format('d/m/Y H:i') }}
                </div>
                <div class="flex items-center gap-2 bg-emerald-500/25 text-emerald-100 text-sm px-4 py-2 rounded-xl">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Auto-refresh 15s
                </div>
            </div>
        </div>
    </div>

    @php $m = $this->metricas; @endphp

    {{-- Alertas --}}
    @if(!empty($this->alertas))
        <div class="space-y-3 mb-8">
            @foreach($this->alertas as $alerta)
                <div class="flex items-center gap-4 px-5 py-4 rounded-xl border
                    {{ $alerta['nivel'] === 'danger' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : '' }}
                    {{ $alerta['nivel'] === 'warning' ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : '' }}
                    {{ $alerta['nivel'] === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800' : '' }}">
                    <x-dynamic-component :component="$alerta['icono']"
                        class="w-6 h-6 flex-shrink-0
                            {{ $alerta['nivel'] === 'danger' ? 'text-red-600 dark:text-red-400' : '' }}
                            {{ $alerta['nivel'] === 'warning' ? 'text-amber-600 dark:text-amber-400' : '' }}
                            {{ $alerta['nivel'] === 'success' ? 'text-emerald-600 dark:text-emerald-400' : '' }}" />
                    <span class="text-sm font-medium
                        {{ $alerta['nivel'] === 'danger' ? 'text-red-800 dark:text-red-200' : '' }}
                        {{ $alerta['nivel'] === 'warning' ? 'text-amber-800 dark:text-amber-200' : '' }}
                        {{ $alerta['nivel'] === 'success' ? 'text-emerald-800 dark:text-emerald-200' : '' }}">
                        {{ $alerta['mensaje'] }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- KPI Cards Row 1: Usuarios --}}
    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
        <x-heroicon-o-users class="w-4 h-4" /> Usuarios del Sistema
    </h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label' => 'Usuarios Totales', 'val' => $m['usuarios_totales'], 'color' => 'from-indigo-500 to-indigo-600', 'icon' => 'heroicon-o-users'],
            ['label' => 'Activos', 'val' => $m['usuarios_activos'], 'color' => 'from-emerald-500 to-emerald-600', 'icon' => 'heroicon-o-check-circle'],
            ['label' => 'Inactivos', 'val' => $m['usuarios_inactivos'], 'color' => 'from-gray-500 to-gray-600', 'icon' => 'heroicon-o-x-circle'],
            ['label' => 'Conectados Hoy', 'val' => $m['usuarios_hoy'], 'color' => 'from-blue-500 to-blue-600', 'icon' => 'heroicon-o-signal'],
        ] as $card)
            <div class="rounded-xl bg-gradient-to-br {{ $card['color'] }} p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                        <x-dynamic-component :component="$card['icon']" class="w-4 h-4 text-white" />
                    </div>
                    <span class="text-white/80 text-xs font-semibold uppercase tracking-wider">{{ $card['label'] }}</span>
                </div>
                <div class="text-3xl font-black text-white">{{ number_format($card['val']) }}</div>
            </div>
        @endforeach
    </div>

    {{-- KPI Cards Row 2: Casos --}}
    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
        <x-heroicon-o-clipboard-document class="w-4 h-4" /> Estado de Casos
    </h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        @foreach([
            ['label' => 'Total Casos', 'val' => $m['casos_totales'], 'color' => 'from-violet-500 to-violet-600', 'icon' => 'heroicon-o-document-text'],
            ['label' => 'Casos Hoy', 'val' => $m['casos_hoy'], 'color' => 'from-cyan-500 to-cyan-600', 'icon' => 'heroicon-o-calendar'],
            ['label' => 'Pendientes', 'val' => $m['casos_pendientes'], 'color' => 'from-amber-500 to-amber-600', 'icon' => 'heroicon-o-clock'],
            ['label' => 'En Proceso', 'val' => $m['casos_en_proceso'], 'color' => 'from-blue-500 to-blue-600', 'icon' => 'heroicon-o-arrow-path'],
        ] as $card)
            <div class="rounded-xl bg-gradient-to-br {{ $card['color'] }} p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                        <x-dynamic-component :component="$card['icon']" class="w-4 h-4 text-white" />
                    </div>
                    <span class="text-white/80 text-xs font-semibold uppercase tracking-wider">{{ $card['label'] }}</span>
                </div>
                <div class="text-3xl font-black text-white">{{ number_format($card['val']) }}</div>
            </div>
        @endforeach
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label' => 'Urgentes', 'val' => $m['casos_urgentes'], 'color' => $m['casos_urgentes'] > 0 ? 'from-red-500 to-red-600' : 'from-gray-400 to-gray-500', 'icon' => 'heroicon-o-fire'],
            ['label' => 'SLA Vencido', 'val' => $m['casos_sla_vencido'], 'color' => $m['casos_sla_vencido'] > 0 ? 'from-red-600 to-red-700' : 'from-gray-400 to-gray-500', 'icon' => 'heroicon-o-exclamation-triangle'],
            ['label' => 'Escalados', 'val' => $m['casos_escalados'], 'color' => $m['casos_escalados'] > 0 ? 'from-orange-500 to-orange-600' : 'from-gray-400 to-gray-500', 'icon' => 'heroicon-o-arrow-trending-up'],
            ['label' => 'Psicólogos Libres', 'val' => $m['psicologos_disponibles'] . '/' . $m['psicologos_total'], 'color' => 'from-teal-500 to-teal-600', 'icon' => 'heroicon-o-user-group'],
        ] as $card)
            <div class="rounded-xl bg-gradient-to-br {{ $card['color'] }} p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                        <x-dynamic-component :component="$card['icon']" class="w-4 h-4 text-white" />
                    </div>
                    <span class="text-white/80 text-xs font-semibold uppercase tracking-wider">{{ $card['label'] }}</span>
                </div>
                <div class="text-3xl font-black text-white">{{ $card['val'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Charts + Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Casos últimos 14 días --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <x-heroicon-o-chart-bar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Casos Últimos 14 Días</h3>
                    <p class="text-xs text-gray-400">Nuevos casos registrados por día</p>
                </div>
            </div>
            @php $casosPorDia = $this->casosPorDia; @endphp
            @if(!empty($casosPorDia))
                @php $maxDia = collect($casosPorDia)->max('total') ?: 1; @endphp
                <div class="flex items-end gap-1.5 h-40">
                    @foreach($casosPorDia as $dia)
                        @php $pct = ($dia['total'] / $maxDia) * 100; @endphp
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $dia['total'] }}</span>
                            <div class="w-full rounded-t-md bg-gradient-to-t from-blue-500 to-blue-400 transition-all duration-500"
                                 style="height: {{ max($pct, 5) }}%"></div>
                            <span class="text-[10px] text-gray-400">{{ $dia['fecha'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center h-40 text-gray-400 text-sm">
                    Sin datos de los últimos 14 días
                </div>
            @endif
        </div>

        {{-- Distribución de Roles --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                    <x-heroicon-o-user-group class="w-5 h-5 text-violet-600 dark:text-violet-400" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Distribución por Rol</h3>
                    <p class="text-xs text-gray-400">Usuarios activos por tipo de rol</p>
                </div>
            </div>
            @php
                $roles = $this->distribucionRoles;
                $totalRoles = collect($roles)->sum('total') ?: 1;
                $coloresRol = ['admin' => '#ef4444', 'psicologo' => '#10b981', 'docente' => '#3b82f6', 'alumno' => '#8b5cf6'];
                $nombresRol = ['admin' => 'Administrador', 'psicologo' => 'Psicólogo', 'docente' => 'Docente', 'alumno' => 'Alumno'];
            @endphp
            <div class="space-y-4">
                @foreach($roles as $role)
                    @php $pct = round(($role['total'] / $totalRoles) * 100, 1); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background: {{ $coloresRol[$role['rol']] ?? '#6b7280' }}"></span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $nombresRol[$role['rol']] ?? $role['rol'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400">{{ $pct }}%</span>
                                <span class="font-bold text-sm" style="color: {{ $coloresRol[$role['rol']] ?? '#6b7280' }}">{{ $role['total'] }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all duration-700" style="width: {{ $pct }}%; background: {{ $coloresRol[$role['rol']] ?? '#6b7280' }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Activity Log --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                <x-heroicon-o-clock class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Actividad Reciente</h3>
                <p class="text-xs text-gray-400">Últimas acciones registradas en el sistema</p>
            </div>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
            @forelse($this->actividadReciente as $actividad)
                <div class="px-6 py-3.5 flex items-center gap-4 hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                        {{ match($actividad['accion']) {
                            'crear' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600',
                            'editar' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-600',
                            'eliminar' => 'bg-red-100 dark:bg-red-900/40 text-red-600',
                            'login' => 'bg-gray-100 dark:bg-gray-700 text-gray-500',
                            'activar' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600',
                            'desactivar' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-600',
                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-500',
                        } }}">
                        <span class="text-xs font-black uppercase">
                            {{ mb_strtoupper(mb_substr($actividad['accion'], 0, 2)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700 dark:text-gray-300 truncate">
                            <span class="font-semibold">{{ $actividad['usuario'] }}</span>
                            — {{ $actividad['descripcion'] }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $actividad['modulo'] }} · {{ $actividad['fecha'] }}
                            @if($actividad['ip'])
                                · {{ $actividad['ip'] }}
                            @endif
                        </p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg font-semibold
                        {{ match($actividad['accion']) {
                            'crear' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                            'editar' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'eliminar' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                            default => 'bg-gray-50 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                        } }}">
                        {{ ucfirst($actividad['accion']) }}
                    </span>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-400">
                    <x-heroicon-o-clock class="w-10 h-10 mx-auto mb-2 text-gray-300" />
                    <p class="text-sm">No hay actividad registrada aún</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
</x-filament-panels::page>
