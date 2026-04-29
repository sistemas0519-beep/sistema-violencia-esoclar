<x-filament-widgets::widget>
    @php
        $data = $this->getViewData();
    @endphp

    @if($data['tieneAlertas'])
    <div class="rounded-xl border border-red-200 bg-red-50 dark:bg-red-950/20 dark:border-red-800 p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex-shrink-0 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                <x-heroicon-m-bell-alert class="w-4 h-4 text-white"/>
            </div>
            <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">
                Alertas Críticas del Sistema
            </h3>
            <span class="ml-auto text-xs text-red-500 dark:text-red-400">Actualización automática cada 30s</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @if($data['slaVencidos'] > 0)
            <a href="/admin/casos?tableFilters[sla_vencido][value]=1"
               class="group flex items-center gap-2 bg-red-100 dark:bg-red-900/30 rounded-lg px-3 py-2 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0"/>
                <div>
                    <div class="text-xl font-bold text-red-700 dark:text-red-300 leading-none">{{ $data['slaVencidos'] }}</div>
                    <div class="text-xs text-red-600 dark:text-red-400 mt-0.5">SLA Vencido</div>
                </div>
            </a>
            @endif

            @if($data['urgentes'] > 0)
            <a href="/admin/casos?tableFilters[prioridad][value]=urgente"
               class="group flex items-center gap-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg px-3 py-2 hover:bg-orange-200 dark:hover:bg-orange-900/50 transition-colors">
                <x-heroicon-o-fire class="w-5 h-5 text-orange-600 dark:text-orange-400 flex-shrink-0"/>
                <div>
                    <div class="text-xl font-bold text-orange-700 dark:text-orange-300 leading-none">{{ $data['urgentes'] }}</div>
                    <div class="text-xs text-orange-600 dark:text-orange-400 mt-0.5">Urgentes</div>
                </div>
            </a>
            @endif

            @if($data['escalados'] > 0)
            <a href="/admin/casos?tableFilters[escalado][value]=1"
               class="group flex items-center gap-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg px-3 py-2 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition-colors">
                <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0"/>
                <div>
                    <div class="text-xl font-bold text-yellow-700 dark:text-yellow-300 leading-none">{{ $data['escalados'] }}</div>
                    <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-0.5">Escalados</div>
                </div>
            </a>
            @endif

            @if($data['sinAsignar'] > 0)
            <a href="/admin/casos?tableFilters[estado][value]=pendiente"
               class="group flex items-center gap-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg px-3 py-2 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                <x-heroicon-o-user-minus class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0"/>
                <div>
                    <div class="text-xl font-bold text-blue-700 dark:text-blue-300 leading-none">{{ $data['sinAsignar'] }}</div>
                    <div class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">Sin Asignar</div>
                </div>
            </a>
            @endif

            @if($data['severidadAlta'] > 0)
            <div class="flex items-center gap-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg px-3 py-2">
                <x-heroicon-o-shield-exclamation class="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0"/>
                <div>
                    <div class="text-xl font-bold text-purple-700 dark:text-purple-300 leading-none">{{ $data['severidadAlta'] }}</div>
                    <div class="text-xs text-purple-600 dark:text-purple-400 mt-0.5">Alta Severidad</div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="rounded-xl border border-green-200 bg-green-50 dark:bg-green-950/20 dark:border-green-800 p-4 flex items-center gap-3">
        <x-heroicon-o-check-circle class="w-6 h-6 text-green-500 flex-shrink-0"/>
        <div>
            <p class="text-sm font-medium text-green-800 dark:text-green-200">Sin alertas críticas</p>
            <p class="text-xs text-green-600 dark:text-green-400">
                Todos los casos están bajo control.
                @if($data['nuevosHoy'] > 0)
                    {{ $data['nuevosHoy'] }} nuevo(s) registrado(s) hoy.
                @endif
            </p>
        </div>
    </div>
    @endif
</x-filament-widgets::widget>
