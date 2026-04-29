<x-filament-panels::page>
<div wire:poll.60s wire:key="monitoreo-panel">

    {{-- ─── Header ─────────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl mb-8 bg-gradient-to-br from-cyan-600 via-blue-600 to-indigo-700 shadow-xl">
        <div class="absolute inset-0 opacity-[.06]" style="background-image:radial-gradient(circle at 30% 50%,white 1px,transparent 1px);background-size:24px 24px"></div>
        <div class="relative px-8 py-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shadow-inner">
                    <x-heroicon-o-computer-desktop class="w-7 h-7 text-white" />
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white tracking-tight">Centro de Monitoreo</h2>
                    <p class="text-blue-200 text-sm mt-0.5">Métricas, alertas y salud del sistema en tiempo real</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur text-white text-sm px-4 py-2 rounded-xl border border-white/10">
                    <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                    {{ now()->format('d/m/Y') }}
                </div>
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur text-white text-sm px-4 py-2 rounded-xl border border-white/10">
                    <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                    {{ now()->format('H:i') }}
                </div>
                <div class="flex items-center gap-2 bg-emerald-500/30 backdrop-blur text-emerald-100 text-sm px-4 py-2 rounded-xl border border-emerald-400/30">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                    </span>
                    Auto-refresh 30s
                </div>
            </div>
        </div>
    </div>

    @php
        $m     = $this->metricas;
        $salud = $this->saludSistema;
        $tend  = $this->tendencias;
    @endphp

    {{-- ─── Alertas activas ────────────────────────────────────────────── --}}
    @if(!empty($this->alertas))
        <div class="space-y-2.5 mb-8">
            @foreach($this->alertas as $alerta)
                <div @class([
                    'flex items-start gap-4 px-5 py-4 rounded-xl border',
                    'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'         => $alerta['nivel'] === 'danger',
                    'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800'  => $alerta['nivel'] === 'warning',
                    'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800' => $alerta['nivel'] === 'success',
                ])>
                    <x-dynamic-component :component="$alerta['icono']" @class([
                        'w-5 h-5 flex-shrink-0 mt-0.5',
                        'text-red-600 dark:text-red-400'         => $alerta['nivel'] === 'danger',
                        'text-amber-600 dark:text-amber-400'     => $alerta['nivel'] === 'warning',
                        'text-emerald-600 dark:text-emerald-400' => $alerta['nivel'] === 'success',
                    ]) />
                    <span @class([
                        'text-sm font-medium',
                        'text-red-800 dark:text-red-200'         => $alerta['nivel'] === 'danger',
                        'text-amber-800 dark:text-amber-200'     => $alerta['nivel'] === 'warning',
                        'text-emerald-800 dark:text-emerald-200' => $alerta['nivel'] === 'success',
                    ])>{{ $alerta['mensaje'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ─── Salud del sistema ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
        <div @class([
            'rounded-xl border p-4 flex items-center gap-3',
            'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800' => $salud['db_status'] === 'ok',
            'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800'         => $salud['db_status'] === 'lento',
            'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'                 => $salud['db_status'] === 'error',
        ])>
            <x-heroicon-o-circle-stack @class([
                'w-8 h-8',
                'text-emerald-600 dark:text-emerald-400' => $salud['db_status'] === 'ok',
                'text-amber-600 dark:text-amber-400'     => $salud['db_status'] === 'lento',
                'text-red-600 dark:text-red-400'         => $salud['db_status'] === 'error',
            ]) />
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Base de Datos</p>
                <p class="font-bold text-gray-800 dark:text-gray-100">
                    {{ $salud['db_status'] === 'ok' ? 'Operativa' : ($salud['db_status'] === 'lento' ? 'Lenta' : 'Error') }}
                    @if($salud['db_ms'] !== null)
                        <span class="text-xs font-normal text-gray-400 ml-1">{{ $salud['db_ms'] }}ms</span>
                    @endif
                </p>
            </div>
        </div>

        <div @class([
            'rounded-xl border p-4 flex items-center gap-3',
            'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800' => $salud['cache_ok'],
            'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'                 => !$salud['cache_ok'],
        ])>
            <x-heroicon-o-bolt @class([
                'w-8 h-8',
                'text-emerald-600 dark:text-emerald-400' => $salud['cache_ok'],
                'text-red-600 dark:text-red-400'         => !$salud['cache_ok'],
            ]) />
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Caché</p>
                <p class="font-bold text-gray-800 dark:text-gray-100">{{ $salud['cache_ok'] ? 'Activa' : 'Sin caché' }}</p>
            </div>
        </div>

        <div class="rounded-xl border bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 p-4 flex items-center gap-3">
            <x-heroicon-o-cpu-chip class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Memoria PHP</p>
                <p class="font-bold text-gray-800 dark:text-gray-100">{{ $salud['mem_used_mb'] }} MB
                    <span class="text-xs font-normal text-gray-400">/ {{ $salud['mem_peak_mb'] }} pico</span>
                </p>
            </div>
        </div>

        <div class="rounded-xl border bg-violet-50 dark:bg-violet-900/20 border-violet-200 dark:border-violet-800 p-4 flex items-center gap-3">
            <x-heroicon-o-clipboard-document-list class="w-8 h-8 text-violet-600 dark:text-violet-400" />
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Eventos Hoy</p>
                <p class="font-bold text-gray-800 dark:text-gray-100">{{ number_format($salud['logs_hoy']) }}</p>
            </div>
        </div>
    </div>

    {{-- ─── KPI: Usuarios ───────────────────────────────────────────────── --}}
    <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
        <x-heroicon-o-users class="w-4 h-4" /> Usuarios del Sistema
    </h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $tendPctUsers = $tend['usuarios']['pct'];
            $kpiUsuarios = [
                ['label' => 'Usuarios Totales', 'val' => $m['usuarios_totales'],  'color' => 'from-indigo-500 to-indigo-600',   'icon' => 'heroicon-o-users',        'extra' => null],
                ['label' => 'Activos',           'val' => $m['usuarios_activos'],  'color' => 'from-emerald-500 to-emerald-600', 'icon' => 'heroicon-o-check-circle', 'extra' => ($m['usuarios_totales'] > 0 ? round(($m['usuarios_activos']/$m['usuarios_totales'])*100) : 0).'% del total'],
                ['label' => 'Inactivos',         'val' => $m['usuarios_inactivos'],'color' => 'from-slate-500 to-slate-600',     'icon' => 'heroicon-o-x-circle',     'extra' => null],
                ['label' => 'Acceso Hoy',        'val' => $m['usuarios_hoy'],      'color' => 'from-blue-500 to-blue-600',       'icon' => 'heroicon-o-signal',       'extra' => ($tendPctUsers >= 0 ? '+'.$tendPctUsers : $tendPctUsers).'% 7 días'],
            ];
        @endphp
        @foreach($kpiUsuarios as $card)
            <div class="rounded-2xl bg-gradient-to-br {{ $card['color'] }} p-5 shadow-sm hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                        <x-dynamic-component :component="$card['icon']" class="w-4 h-4 text-white" />
                    </div>
                    @if($card['extra'])
                        <span class="text-white/75 text-[10px] font-semibold bg-black/10 px-2 py-0.5 rounded-full">{{ $card['extra'] }}</span>
                    @endif
                </div>
                <div class="text-4xl font-black text-white mb-1">{{ number_format($card['val']) }}</div>
                <div class="text-white/75 text-xs font-semibold uppercase tracking-wider">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ─── KPI: Casos fila 1 (volumen) ────────────────────────────────── --}}
    <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
        <x-heroicon-o-clipboard-document class="w-4 h-4" /> Estado de Casos
    </h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        @php
            $tendPctCasos = $tend['casos']['pct'];
            $kpicasos1 = [
                ['label' => 'Total Casos',  'val' => $m['casos_totales'],    'color' => 'from-violet-500 to-violet-600', 'icon' => 'heroicon-o-document-text', 'extra' => ($tendPctCasos >= 0 ? '+'.$tendPctCasos : $tendPctCasos).'% semana'],
                ['label' => 'Nuevos Hoy',   'val' => $m['casos_hoy'],        'color' => 'from-cyan-500 to-cyan-600',    'icon' => 'heroicon-o-calendar',      'extra' => $m['casos_semana'].' esta semana'],
                ['label' => 'Pendientes',   'val' => $m['casos_pendientes'], 'color' => 'from-amber-500 to-amber-600', 'icon' => 'heroicon-o-clock',         'extra' => $m['casos_sin_asignar'].' sin asignar'],
                ['label' => 'En Proceso',   'val' => $m['casos_en_proceso'], 'color' => 'from-sky-500 to-sky-600',     'icon' => 'heroicon-o-arrow-path',    'extra' => null],
            ];
        @endphp
        @foreach($kpicasos1 as $card)
            <div class="rounded-2xl bg-gradient-to-br {{ $card['color'] }} p-5 shadow-sm hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                        <x-dynamic-component :component="$card['icon']" class="w-4 h-4 text-white" />
                    </div>
                    @if($card['extra'])
                        <span class="text-white/75 text-[10px] font-semibold bg-black/10 px-2 py-0.5 rounded-full">{{ $card['extra'] }}</span>
                    @endif
                </div>
                <div class="text-4xl font-black text-white mb-1">{{ number_format($card['val']) }}</div>
                <div class="text-white/75 text-xs font-semibold uppercase tracking-wider">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ─── KPI: Casos fila 2 (críticos) ──────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $kpicasos2 = [
                ['label' => 'Urgentes',  'val' => $m['casos_urgentes'],   'color' => $m['casos_urgentes']   > 0 ? 'from-red-500 to-red-600'     : 'from-gray-400 to-gray-500', 'icon' => 'heroicon-o-fire'],
                ['label' => 'SLA Vencido','val' => $m['casos_sla_vencido'],'color' => $m['casos_sla_vencido']> 0 ? 'from-rose-600 to-rose-700'   : 'from-gray-400 to-gray-500', 'icon' => 'heroicon-o-exclamation-triangle'],
                ['label' => 'Escalados', 'val' => $m['casos_escalados'],  'color' => $m['casos_escalados']  > 0 ? 'from-orange-500 to-orange-600': 'from-gray-400 to-gray-500', 'icon' => 'heroicon-o-arrow-trending-up'],
                ['label' => 'Sensibles', 'val' => $m['casos_sensibles'],  'color' => $m['casos_sensibles']  > 0 ? 'from-purple-600 to-purple-700': 'from-gray-400 to-gray-500', 'icon' => 'heroicon-o-shield-exclamation'],
            ];
        @endphp
        @foreach($kpicasos2 as $card)
            <div class="rounded-2xl bg-gradient-to-br {{ $card['color'] }} p-5 shadow-sm hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center mb-3">
                    <x-dynamic-component :component="$card['icon']" class="w-4 h-4 text-white" />
                </div>
                <div class="text-4xl font-black text-white mb-1">{{ number_format($card['val']) }}</div>
                <div class="text-white/75 text-xs font-semibold uppercase tracking-wider">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ─── KPI: Equipo y Sesiones ──────────────────────────────────────── --}}
    <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
        <x-heroicon-o-user-group class="w-4 h-4" /> Equipo y Sesiones
    </h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $pctDisp = $m['psicologos_total'] > 0 ? round(($m['psicologos_disponibles']/$m['psicologos_total'])*100) : 0;
            $kpiEquipo = [
                ['label' => 'Psicólogos Libres', 'val' => $m['psicologos_disponibles'].'/'.$m['psicologos_total'], 'color' => $pctDisp >= 50 ? 'from-teal-500 to-teal-600' : 'from-amber-500 to-amber-600', 'icon' => 'heroicon-o-user-plus',    'extra' => $pctDisp.'% disponibles'],
                ['label' => 'Asign. Activas',    'val' => $m['asignaciones_activas'],    'color' => 'from-blue-500 to-blue-600',    'icon' => 'heroicon-o-link',          'extra' => $m['asignaciones_completadas'].' completadas'],
                ['label' => 'Sesiones Hoy',      'val' => $m['sesiones_hoy'],            'color' => 'from-emerald-500 to-emerald-600','icon' => 'heroicon-o-calendar-days','extra' => $m['sesiones_completadas_hoy'].' completadas'],
                ['label' => 'Sesiones Semana',   'val' => $m['sesiones_semana'],         'color' => 'from-indigo-500 to-indigo-600','icon' => 'heroicon-o-chart-bar',     'extra' => null],
            ];
        @endphp
        @foreach($kpiEquipo as $card)
            <div class="rounded-2xl bg-gradient-to-br {{ $card['color'] }} p-5 shadow-sm hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                        <x-dynamic-component :component="$card['icon']" class="w-4 h-4 text-white" />
                    </div>
                    @if($card['extra'])
                        <span class="text-white/75 text-[10px] font-semibold bg-black/10 px-2 py-0.5 rounded-full">{{ $card['extra'] }}</span>
                    @endif
                </div>
                <div class="text-4xl font-black text-white mb-1">{{ $card['val'] }}</div>
                <div class="text-white/75 text-xs font-semibold uppercase tracking-wider">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ─── SLA Global + Casos por tipo de violencia ───────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- SLA Global --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center">
                    <x-heroicon-o-clock class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Cumplimiento SLA</h3>
                    <p class="text-xs text-gray-400">Porcentaje de casos atendidos en plazo</p>
                </div>
            </div>
            @php
                $slaColor = $m['sla_cumplido_pct'] >= 90 ? '#10b981' : ($m['sla_cumplido_pct'] >= 70 ? '#f59e0b' : '#ef4444');
                $slaDash  = round(($m['sla_cumplido_pct'] / 100) * 251);
            @endphp
            <div class="flex items-center gap-6 mb-5">
                <div class="relative w-24 h-24 flex-shrink-0">
                    <svg class="w-24 h-24 -rotate-90" viewBox="0 0 92 92">
                        <circle cx="46" cy="46" r="40" fill="none" stroke="#e5e7eb" stroke-width="10" class="dark:stroke-gray-700"/>
                        <circle cx="46" cy="46" r="40" fill="none"
                            stroke="{{ $slaColor }}"
                            stroke-width="10"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $slaDash }} 251"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-black" style="color: {{ $slaColor }}">{{ $m['sla_cumplido_pct'] }}%</span>
                    </div>
                </div>
                <div class="flex-1 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Casos con SLA</span>
                        <span class="font-bold text-gray-700 dark:text-gray-200">{{ number_format($m['sla_total']) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">SLA Vencidos</span>
                        <span class="font-bold text-red-600 dark:text-red-400">{{ number_format($m['casos_sla_vencido']) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Tasa Resolución</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $m['tasa_resolucion'] }}%</span>
                    </div>
                </div>
            </div>
            @if(!empty($this->resumenSla))
                <div class="space-y-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    @foreach($this->resumenSla as $sla)
                        @php $colorBar = $sla['pct_cumpl'] >= 90 ? 'bg-emerald-500' : ($sla['pct_cumpl'] >= 70 ? 'bg-amber-500' : 'bg-red-500'); @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-600 dark:text-gray-300 font-medium capitalize">{{ str_replace('_', ' ', $sla['tipo']) }}</span>
                                <span class="font-bold text-gray-700 dark:text-gray-200">{{ $sla['pct_cumpl'] }}%
                                    <span class="font-normal text-gray-400">({{ $sla['vencidos'] }}/{{ $sla['total'] }})</span>
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                <div class="{{ $colorBar }} h-2 rounded-full transition-all duration-700" style="width: {{ $sla['pct_cumpl'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Casos por tipo de violencia --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center">
                    <x-heroicon-o-chart-pie class="w-5 h-5 text-rose-600 dark:text-rose-400" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Casos por Tipo de Violencia</h3>
                    <p class="text-xs text-gray-400">Distribución total acumulada</p>
                </div>
            </div>
            @php
                $tiposData  = $this->casosPorTipo;
                $totalTipos = collect($tiposData)->sum('total') ?: 1;
                $coloresTipo = [
                    'fisica'         => '#ef4444',
                    'psicologica'    => '#f59e0b',
                    'verbal'         => '#fbbf24',
                    'sexual'         => '#dc2626',
                    'ciberacoso'     => '#3b82f6',
                    'discriminacion' => '#6b7280',
                    'negligencia'    => '#8b5cf6',
                    'acoso'          => '#ec4899',
                ];
            @endphp
            @if(!empty($tiposData))
                <div class="space-y-3.5">
                    @foreach($tiposData as $item)
                        @php
                            $pct   = round(($item['total'] / $totalTipos) * 100, 1);
                            $color = $coloresTipo[$item['tipo']] ?? '#6b7280';
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $color }}"></span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 capitalize font-medium">{{ str_replace('_', ' ', $item['tipo']) }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-gray-400">{{ $pct }}%</span>
                                    <span class="font-bold" style="color:{{ $color }}">{{ $item['total'] }}</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full transition-all duration-700" style="width:{{ $pct }}%; background:{{ $color }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center h-32 text-gray-400 text-sm">Sin datos disponibles</div>
            @endif
        </div>
    </div>

    {{-- ─── Casos por día + Distribución de roles ───────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Gráfico de barras: casos 14 días --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <x-heroicon-o-chart-bar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Casos Últimos 14 Días</h3>
                    <p class="text-xs text-gray-400">Nuevos casos por día</p>
                </div>
                <div class="ml-auto text-right">
                    <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $m['casos_semana'] }}</span>
                    <p class="text-xs text-gray-400">esta semana</p>
                </div>
            </div>
            @php $casosPorDia = $this->casosPorDia; $maxDia = collect($casosPorDia)->max('total') ?: 1; @endphp
            <div class="flex items-end gap-1 h-36">
                @foreach($casosPorDia as $dia)
                    @php $pct = ($dia['total'] / $maxDia) * 100; @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group">
                        <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">{{ $dia['total'] }}</span>
                        <div class="w-full rounded-t-md bg-gradient-to-t from-blue-600 to-blue-400 transition-all duration-500 hover:from-blue-500 hover:to-cyan-400"
                             style="height: {{ max($pct, 4) }}%"
                             title="{{ $dia['fecha'] }}: {{ $dia['total'] }} casos"></div>
                        <span class="text-[9px] text-gray-400">{{ $dia['fecha'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Distribución de roles --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                    <x-heroicon-o-user-group class="w-5 h-5 text-violet-600 dark:text-violet-400" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Distribución por Rol</h3>
                    <p class="text-xs text-gray-400">Usuarios activos por tipo de rol</p>
                </div>
                <div class="ml-auto text-right">
                    <span class="text-2xl font-black text-violet-600 dark:text-violet-400">{{ $m['usuarios_activos'] }}</span>
                    <p class="text-xs text-gray-400">activos</p>
                </div>
            </div>
            @php
                $roles      = $this->distribucionRoles;
                $totalRoles = collect($roles)->sum('total') ?: 1;
                $coloresRol = ['admin' => '#ef4444', 'psicologo' => '#10b981', 'asistente' => '#06b6d4', 'docente' => '#3b82f6', 'alumno' => '#8b5cf6'];
                $nombresRol = ['admin' => 'Administrador', 'psicologo' => 'Psicólogo', 'asistente' => 'Asistente', 'docente' => 'Docente', 'alumno' => 'Alumno'];
            @endphp
            <div class="space-y-4">
                @foreach($roles as $role)
                    @php $pct = round(($role['total'] / $totalRoles) * 100, 1); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $coloresRol[$role['rol']] ?? '#6b7280' }}"></span>
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

    {{-- ─── Top regiones ────────────────────────────────────────────────── --}}
    @php $regiones = $this->casosPorRegion; @endphp
    @if(!empty($regiones))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center">
                    <x-heroicon-o-map-pin class="w-5 h-5 text-teal-600 dark:text-teal-400" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-200">Distribución Geográfica</h3>
                    <p class="text-xs text-gray-400">Top regiones con más casos registrados</p>
                </div>
            </div>
            @php $maxReg = collect($regiones)->max('total') ?: 1; @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3.5">
                @foreach($regiones as $i => $reg)
                    @php $pct = round(($reg['total'] / $maxReg) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300 text-[10px] font-black flex items-center justify-center">{{ $i+1 }}</span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $reg['region'] }}</span>
                            </div>
                            <span class="font-bold text-sm text-teal-700 dark:text-teal-300">{{ number_format($reg['total']) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-teal-500 to-teal-400 h-2 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ─── Actividad reciente ──────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                <x-heroicon-o-clock class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Actividad Reciente</h3>
                <p class="text-xs text-gray-400">Últimas 20 acciones registradas en el sistema</p>
            </div>
            <div class="ml-auto">
                <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                    {{ $salud['logs_hoy'] }} eventos hoy
                </span>
            </div>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
            @forelse($this->actividadReciente as $actividad)
                @php
                    $accionMeta = match($actividad['accion']) {
                        'crear'      => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'text' => 'text-emerald-600 dark:text-emerald-400', 'badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'],
                        'editar'     => ['bg' => 'bg-blue-100 dark:bg-blue-900/40',       'text' => 'text-blue-600 dark:text-blue-400',       'badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
                        'eliminar'   => ['bg' => 'bg-red-100 dark:bg-red-900/40',         'text' => 'text-red-600 dark:text-red-400',         'badge' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
                        'activar'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'text' => 'text-emerald-600 dark:text-emerald-400', 'badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'],
                        'desactivar' => ['bg' => 'bg-amber-100 dark:bg-amber-900/40',     'text' => 'text-amber-600 dark:text-amber-400',     'badge' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
                        default      => ['bg' => 'bg-gray-100 dark:bg-gray-700',          'text' => 'text-gray-500 dark:text-gray-400',       'badge' => 'bg-gray-50 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
                    };
                    $rolColor = match($actividad['rol']) {
                        'admin'     => 'text-red-500 dark:text-red-400',
                        'psicologo' => 'text-emerald-600 dark:text-emerald-400',
                        'asistente' => 'text-cyan-600 dark:text-cyan-400',
                        'docente'   => 'text-blue-600 dark:text-blue-400',
                        default     => 'text-gray-400',
                    };
                @endphp
                <div class="px-6 py-3.5 flex items-center gap-4 hover:bg-gray-50/60 dark:hover:bg-gray-700/20 transition-colors">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 {{ $accionMeta['bg'] }} {{ $accionMeta['text'] }}">
                        <span class="text-[10px] font-black uppercase">{{ mb_strtoupper(mb_substr($actividad['accion'], 0, 2)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700 dark:text-gray-300 truncate">
                            <span class="font-semibold">{{ $actividad['usuario'] }}</span>
                            @if($actividad['rol'])
                                <span class="text-xs {{ $rolColor }} font-medium ml-1">({{ $actividad['rol'] }})</span>
                            @endif
                            <span class="text-gray-400 mx-1">—</span>
                            {{ $actividad['descripcion'] }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            <span class="font-medium text-gray-500 dark:text-gray-400">{{ $actividad['modulo'] }}</span>
                            · <span title="{{ $actividad['fecha_full'] }}">{{ $actividad['fecha'] }}</span>
                            @if($actividad['ip'])
                                · <span class="font-mono">{{ $actividad['ip'] }}</span>
                            @endif
                        </p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg font-semibold flex-shrink-0 {{ $accionMeta['badge'] }}">
                        {{ ucfirst($actividad['accion']) }}
                    </span>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-3 text-gray-200 dark:text-gray-700" />
                    <p class="text-sm text-gray-400">No hay actividad registrada aún</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
</x-filament-panels::page>
