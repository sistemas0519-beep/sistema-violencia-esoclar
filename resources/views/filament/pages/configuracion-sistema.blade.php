<x-filament-panels::page>
<div wire:key="config-panel">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl mb-8 bg-gradient-to-br from-slate-700 via-slate-600 to-slate-800 shadow-xl">
        <div class="absolute inset-0 opacity-[.05]" style="background-image:radial-gradient(circle at 25% 50%,white 1px,transparent 1px);background-size:28px 28px"></div>
        <div class="relative px-8 py-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center">
                    <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Configuración del Sistema</h2>
                    <p class="text-slate-300 text-sm">Gestión centralizada de parámetros</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Sidebar de Groups --}}
        <div class="lg:w-64 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Módulos</h3>
                </div>
                <nav class="p-2">
                    @foreach($this->grupos as $key => $grupo)
                        <button wire:click="cambiarGrupo('{{ $key }}')"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-all duration-200
                                    {{ $grupoActivo === $key
                                        ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-semibold'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ $grupoActivo === $key ? 'bg-indigo-100 dark:bg-indigo-800/50' : 'bg-gray-100 dark:bg-gray-700' }}">
                                <x-dynamic-component :component="$grupo['icon']"
                                    class="w-4 h-4 {{ $grupoActivo === $key ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}" />
                            </div>
                            <div>
                                <span class="text-sm">{{ $grupo['label'] }}</span>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $grupo['desc'] }}</p>
                            </div>
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        {{-- Config Form --}}
        <div class="flex-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200">
                            {{ $this->grupos[$grupoActivo]['label'] ?? 'Configuración' }}
                        </h3>
                        <p class="text-sm text-gray-400">
                            {{ $this->grupos[$grupoActivo]['desc'] ?? '' }}
                        </p>
                    </div>
                    <button wire:click="guardarConfiguracion"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-all shadow-sm hover:shadow-md">
                        <x-heroicon-o-check class="w-4 h-4" />
                        Guardar Cambios
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    @foreach($this->configuraciones as $config)
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4 pb-5 border-b border-gray-50 dark:border-gray-700/50 last:border-0 last:pb-0">
                            <div class="sm:w-1/3">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $config['etiqueta'] }}
                                </label>
                                @if($config['descripcion'])
                                    <p class="text-xs text-gray-400 mt-1">{{ $config['descripcion'] }}</p>
                                @endif
                            </div>
                            <div class="sm:w-2/3">
                                @if($config['tipo'] === 'boolean')
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               wire:model.live="valores.{{ $config['clave'] }}"
                                               value="1"
                                               class="sr-only peer"
                                               {{ $valores[$config['clave']] ?? false ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-indigo-600"></div>
                                        <span class="ms-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $valores[$config['clave']] ?? false ? 'Activado' : 'Desactivado' }}
                                        </span>
                                    </label>

                                @elseif($config['tipo'] === 'select')
                                    <select wire:model.live="valores.{{ $config['clave'] }}"
                                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach($config['opciones'] ?? [] as $opcion)
                                            <option value="{{ $opcion }}">{{ ucfirst($opcion) }}</option>
                                        @endforeach
                                    </select>

                                @elseif($config['tipo'] === 'number')
                                    <input type="number"
                                           wire:model.live="valores.{{ $config['clave'] }}"
                                           class="block w-full sm:w-40 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">

                                @else
                                    <input type="text"
                                           wire:model.live="valores.{{ $config['clave'] }}"
                                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if(empty($this->configuraciones))
                        <div class="text-center py-12 text-gray-400">
                            <x-heroicon-o-cog-6-tooth class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                            <p class="text-sm">No hay configuraciones para este grupo</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-filament-panels::page>
