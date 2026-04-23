<x-filament-panels::page>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        {{-- Header del calendario --}}
        <div class="flex items-center justify-between mb-6">
            <button wire:click="mesAnterior" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <x-heroicon-o-chevron-left class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            </button>

            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $this->getNombreMes() }}</h2>
                <button wire:click="hoy"
                    class="px-3 py-1 text-xs bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300 rounded-full hover:bg-teal-200 transition">
                    Hoy
                </button>
            </div>

            <button wire:click="mesSiguiente" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            </button>
        </div>

        @php
            $dias = $this->getDiasDelMes();
            $sesiones = $this->getSesionesDelMes();
            $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        @endphp

        {{-- Encabezados días --}}
        <div class="grid grid-cols-7 gap-1 mb-1">
            @foreach ($diasSemana as $d)
                <div class="text-center text-xs font-semibold text-gray-500 dark:text-gray-400 py-2">
                    {{ $d }}
                </div>
            @endforeach
        </div>

        {{-- Grilla de días --}}
        <div class="grid grid-cols-7 gap-1">
            @foreach ($dias as $dia)
                @php
                    $sesionesDia = $sesiones[$dia['fecha']] ?? [];
                    $tieneSesiones = count($sesionesDia) > 0;
                @endphp
                <div class="min-h-[90px] rounded-lg border p-1 text-xs transition
                    {{ $dia['esMesActual'] ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' : 'bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800 opacity-50' }}
                    {{ $dia['esHoy'] ? 'ring-2 ring-teal-500 border-teal-300' : '' }}">

                    <div class="flex items-center justify-between mb-1">
                        <span class="font-medium {{ $dia['esHoy'] ? 'text-teal-600 dark:text-teal-400 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $dia['dia'] }}
                        </span>
                        @if ($tieneSesiones)
                            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300 rounded-full">
                                {{ count($sesionesDia) }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-0.5 overflow-hidden" style="max-height: 60px;">
                        @foreach (array_slice($sesionesDia, 0, 3) as $sesion)
                            @php
                                $color = match($sesion['estado']) {
                                    'completada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
                                    'cancelada', 'no_asistio' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                    'en_curso' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
                                    default => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                };
                            @endphp
                            <div class="rounded px-1 py-0.5 truncate {{ $color }}"
                                 title="{{ \Carbon\Carbon::parse($sesion['hora_inicio'])->format('H:i') }} — {{ $sesion['paciente']['name'] ?? 'N/A' }}">
                                {{ \Carbon\Carbon::parse($sesion['hora_inicio'])->format('H:i') }}
                                {{ $sesion['paciente']['name'] ?? '' }}
                            </div>
                        @endforeach
                        @if (count($sesionesDia) > 3)
                            <div class="text-gray-400 text-center">+{{ count($sesionesDia) - 3 }} más</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Leyenda --}}
        <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded bg-blue-200 dark:bg-blue-800"></span> Programada
            </div>
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded bg-amber-200 dark:bg-amber-800"></span> En Curso
            </div>
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded bg-emerald-200 dark:bg-emerald-800"></span> Completada
            </div>
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded bg-red-200 dark:bg-red-800"></span> Cancelada / No Asistió
            </div>
        </div>
    </div>
</x-filament-panels::page>
