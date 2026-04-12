<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mis Reportes
            </h2>
            <a href="{{ route('alumno.denuncia') }}"
               class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Reporte
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- Info banner --}}
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-blue-700">
                    Solo puedes ver el estado de los reportes que <strong>no enviaste de forma anónima</strong>.
                    Los reportes anónimos son completamente confidenciales y no aparecen aquí.
                </p>
            </div>

            {{-- Sin casos --}}
            @if($casos->isEmpty())
                <div class="bg-white shadow rounded-2xl p-12 text-center">
                    <div class="text-5xl mb-4">📋</div>
                    <h3 class="text-lg font-bold text-gray-700 mb-2">Aún no tienes reportes</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Cuando envíes un reporte identificado, aparecerá aquí con su estado y seguimientos.
                    </p>
                    <a href="{{ route('alumno.denuncia') }}"
                       class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                        Enviar mi primer reporte
                    </a>
                </div>
            @else
                {{-- Lista de casos --}}
                <div class="space-y-5">
                    @foreach($casos as $caso)
                        @php
                            $estadoConfig = [
                                'pendiente'  => ['label' => 'Pendiente',   'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'dot' => 'bg-yellow-400'],
                                'en_proceso' => ['label' => 'En Proceso',  'bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-200',   'dot' => 'bg-blue-500'],
                                'resuelto'   => ['label' => 'Resuelto',    'bg' => 'bg-green-100',  'text' => 'text-green-700',  'border' => 'border-green-200',  'dot' => 'bg-green-500'],
                                'cerrado'    => ['label' => 'Cerrado',     'bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'border' => 'border-gray-200',   'dot' => 'bg-gray-400'],
                            ];
                            $tipoLabel = [
                                'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
                                'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminación', 'otro' => 'Otro',
                            ];
                            $st = $estadoConfig[$caso->estado] ?? $estadoConfig['pendiente'];
                        @endphp

                        <div class="bg-white shadow rounded-2xl overflow-hidden border border-gray-100">

                            {{-- Cabecera del caso --}}
                            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-bold text-indigo-700 text-sm">{{ $caso->codigo_caso }}</span>
                                    <span class="{{ $st['bg'] }} {{ $st['text'] }} text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                        {{ $st['label'] }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400">
                                    Enviado el {{ $caso->created_at->format('d/m/Y') }}
                                </span>
                            </div>

                            <div class="px-6 py-5">

                                {{-- Info del caso --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5 text-sm">
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Tipo</p>
                                        <p class="font-semibold text-gray-700">{{ $tipoLabel[$caso->tipo_violencia] ?? $caso->tipo_violencia }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Fecha del Incidente</p>
                                        <p class="font-semibold text-gray-700">
                                            {{ $caso->fecha_incidente ? $caso->fecha_incidente->format('d/m/Y') : 'No especificada' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Psicólogo Asignado</p>
                                        <p class="font-semibold text-gray-700">
                                            {{ $caso->asignado?->name ?? 'Pendiente de asignación' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Descripción --}}
                                <div class="bg-gray-50 rounded-xl p-4 mb-5">
                                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Tu reporte</p>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $caso->descripcion }}</p>
                                </div>

                                {{-- Progreso visual --}}
                                @php
                                    $pasos = ['pendiente', 'en_proceso', 'resuelto', 'cerrado'];
                                    $idx   = array_search($caso->estado, $pasos);
                                @endphp
                                <div class="mb-5">
                                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-3">Progreso del caso</p>
                                    <div class="flex items-center gap-0">
                                        @foreach(['Pendiente', 'En Proceso', 'Resuelto', 'Cerrado'] as $i => $paso)
                                            <div class="flex items-center {{ $i < count($pasos) - 1 ? 'flex-1' : '' }}">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2
                                                        {{ $i <= $idx
                                                            ? 'bg-indigo-600 border-indigo-600 text-white'
                                                            : 'bg-white border-gray-200 text-gray-400' }}">
                                                        @if($i < $idx)
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        @else
                                                            {{ $i + 1 }}
                                                        @endif
                                                    </div>
                                                    <span class="text-xs mt-1 font-medium
                                                        {{ $i <= $idx ? 'text-indigo-600' : 'text-gray-400' }}">
                                                        {{ $paso }}
                                                    </span>
                                                </div>
                                                @if($i < count($pasos) - 1)
                                                    <div class="flex-1 h-0.5 mx-1 mb-4
                                                        {{ $i < $idx ? 'bg-indigo-400' : 'bg-gray-200' }}"></div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Seguimientos / Historial --}}
                                @if($caso->seguimientos->isNotEmpty())
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-3">
                                            Actualizaciones ({{ $caso->seguimientos->count() }})
                                        </p>
                                        <div class="space-y-3">
                                            @foreach($caso->seguimientos->sortByDesc('fecha_seguimiento') as $seg)
                                                <div class="flex gap-3">
                                                    <div class="shrink-0 w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="bg-gray-50 rounded-xl px-4 py-3 flex-1">
                                                        <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                                                            <span class="font-semibold text-gray-600">
                                                                {{ $seg->responsable?->name ?? 'Psicólogo' }}
                                                            </span>
                                                            <span>{{ $seg->fecha_seguimiento->format('d/m/Y H:i') }}</span>
                                                        </div>
                                                        <p class="text-xs text-indigo-600 capitalize font-medium mb-1">
                                                            Acción: {{ $seg->accion }}
                                                        </p>
                                                        <p class="text-sm text-gray-700">{{ $seg->notas }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-sm text-gray-400 bg-gray-50 rounded-xl px-4 py-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        El psicólogo aún no ha registrado actualizaciones. Te avisaremos pronto.
                                    </div>
                                @endif

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
