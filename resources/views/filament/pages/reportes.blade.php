<x-filament-panels::page>

    {{-- ── Filtros ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Filtrar Reporte</h3>
        <form wire:submit.prevent class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{ $this->form }}
        </form>
    </div>

    {{-- ── Resumen de tarjetas ── --}}
    @php $r = $this->resumen; @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">

        @foreach([
            ['label' => 'Total Casos',   'val' => $r['total'],      'color' => 'indigo',  'icon' => '📋'],
            ['label' => 'Pendientes',    'val' => $r['pendiente'],  'color' => 'amber',   'icon' => '⏳'],
            ['label' => 'En Proceso',    'val' => $r['en_proceso'], 'color' => 'blue',    'icon' => '🔄'],
            ['label' => 'Resueltos',     'val' => $r['resuelto'],   'color' => 'green',   'icon' => '✅'],
            ['label' => 'Cerrados',      'val' => $r['cerrado'],    'color' => 'gray',    'icon' => '🔒'],
            ['label' => 'Anónimos',      'val' => $r['anonimos'],   'color' => 'purple',  'icon' => '👤'],
        ] as $card)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center shadow-sm">
                <div class="text-2xl mb-1">{{ $card['icon'] }}</div>
                <div class="text-2xl font-black text-gray-800 dark:text-white">{{ $card['val'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $card['label'] }}</div>
            </div>
        @endforeach

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- ── Por tipo de violencia ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                <span class="text-lg">📊</span> Casos por tipo de violencia
            </h3>

            @php
                $tipos = $this->porTipo;
                $maxTipo = collect($tipos)->max('total') ?: 1;
                $colores = [
                    'fisica'         => ['bg' => 'bg-red-500',    'light' => 'bg-red-50',    'text' => 'text-red-700'],
                    'psicologica'    => ['bg' => 'bg-orange-500', 'light' => 'bg-orange-50', 'text' => 'text-orange-700'],
                    'verbal'         => ['bg' => 'bg-yellow-500', 'light' => 'bg-yellow-50', 'text' => 'text-yellow-700'],
                    'sexual'         => ['bg' => 'bg-pink-500',   'light' => 'bg-pink-50',   'text' => 'text-pink-700'],
                    'ciberacoso'     => ['bg' => 'bg-blue-500',   'light' => 'bg-blue-50',   'text' => 'text-blue-700'],
                    'discriminacion' => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-50', 'text' => 'text-purple-700'],
                    'otro'           => ['bg' => 'bg-gray-400',   'light' => 'bg-gray-50',   'text' => 'text-gray-600'],
                ];
                $tipoNombre = [
                    'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
                    'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso',
                    'discriminacion' => 'Discriminación', 'otro' => 'Otro',
                ];
            @endphp

            @if(empty($tipos))
                <p class="text-sm text-gray-400 text-center py-6">Sin datos para mostrar</p>
            @else
                <div class="space-y-3">
                    @foreach($tipos as $t)
                        @php
                            $c   = $colores[$t['tipo']] ?? $colores['otro'];
                            $pct = round(($t['total'] / $maxTipo) * 100);
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $tipoNombre[$t['tipo']] ?? $t['tipo'] }}
                                </span>
                                <span class="font-bold {{ $c['text'] }}">{{ $t['total'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="{{ $c['bg'] }} h-2.5 rounded-full transition-all duration-500"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Por mes ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                <span class="text-lg">📅</span> Casos por mes
            </h3>

            @php
                $meses   = $this->porMes;
                $maxMes  = collect($meses)->max('total') ?: 1;
            @endphp

            @if(empty($meses))
                <p class="text-sm text-gray-400 text-center py-6">Sin datos para mostrar</p>
            @else
                <div class="space-y-3">
                    @foreach($meses as $m)
                        @php $pct = round(($m['total'] / $maxMes) * 100); @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $m['mes'] }}</span>
                                <span class="font-bold text-indigo-700">{{ $m['total'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-500"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ── Exportar ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Exportar datos</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Descarga los casos filtrados en formato CSV (compatible con Excel).
                </p>
            </div>
            <button wire:click="exportarCsv"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700
                           text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar CSV
            </button>
        </div>
    </div>

    {{-- ── Últimos 10 casos ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200">Casos recientes (últimos 10)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Denunciante</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Asignado</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($this->ultimosCasos as $caso)
                        @php
                            $estadoBadge = [
                                'pendiente'  => 'bg-yellow-100 text-yellow-700',
                                'en_proceso' => 'bg-blue-100 text-blue-700',
                                'resuelto'   => 'bg-green-100 text-green-700',
                                'cerrado'    => 'bg-gray-100 text-gray-600',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-4 py-3 font-mono font-bold text-indigo-700 dark:text-indigo-400 text-xs">
                                {{ $caso->codigo_caso }}
                            </td>
                            <td class="px-4 py-3 capitalize text-gray-700 dark:text-gray-300">
                                {{ $tipoNombre[$caso->tipo_violencia] ?? $caso->tipo_violencia }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $estadoBadge[$caso->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $caso->estado)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $caso->es_anonimo ? '🔒 Anónimo' : ($caso->denunciante?->name ?? '—') }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $caso->asignado?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                {{ $caso->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                No hay casos registrados con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-filament-panels::page>
