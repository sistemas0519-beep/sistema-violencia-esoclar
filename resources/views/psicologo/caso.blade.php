<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('psicologo.dashboard') }}"
                   class="shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    Caso <span class="text-teal-700 font-mono">{{ $caso->codigo_caso }}</span>
                </h2>
            </div>
            @php
                $estadoColor = [
                    'pendiente'  => 'bg-amber-100 text-amber-700',
                    'en_proceso' => 'bg-blue-100 text-blue-700',
                    'resuelto'   => 'bg-green-100 text-green-700',
                    'cerrado'    => 'bg-gray-100 text-gray-600',
                ];
            @endphp
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $estadoColor[$caso->estado] ?? '' }}">
                {{ ucfirst(str_replace('_', ' ', $caso->estado)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 rounded-xl p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Columna principal --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Info del caso --}}
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Información del Caso</h3>
                            @if(! $caso->asignado_a)
                                <form action="{{ route('caso.asignar', $caso) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                        Asignarme este caso
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="p-4 sm:p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div class="bg-gray-50 rounded-xl p-3.5">
                                    <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Tipo de Violencia</dt>
                                    @php
                                        $tipoColor = [
                                            'fisica' => 'bg-red-100 text-red-700', 'psicologica' => 'bg-orange-100 text-orange-700',
                                            'verbal' => 'bg-yellow-100 text-yellow-700', 'sexual' => 'bg-pink-100 text-pink-700',
                                            'ciberacoso' => 'bg-blue-100 text-blue-700', 'discriminacion' => 'bg-purple-100 text-purple-700',
                                            'otro' => 'bg-gray-100 text-gray-700',
                                        ];
                                    @endphp
                                    <dd>
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $tipoColor[$caso->tipo_violencia] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst(str_replace('_', ' ', $caso->tipo_violencia)) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3.5">
                                    <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Denunciante</dt>
                                    <dd class="font-semibold text-gray-700">{{ $caso->es_anonimo ? 'Anónimo' : ($caso->denunciante->name ?? '—') }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3.5">
                                    <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Asignado a</dt>
                                    <dd class="font-semibold text-gray-700">{{ $caso->asignado->name ?? 'Sin asignar' }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3.5">
                                    <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Fecha del incidente</dt>
                                    <dd class="font-semibold text-gray-700">{{ $caso->fecha_incidente ? $caso->fecha_incidente->format('d/m/Y') : '—' }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3.5">
                                    <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Escuela</dt>
                                    <dd class="font-semibold text-gray-700">{{ $caso->escuela_nombre ?? '-' }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3.5">
                                    <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Ubicación</dt>
                                    <dd class="font-semibold text-gray-700">{{ collect([$caso->region, $caso->provincia, $caso->distrito])->filter()->join(' / ') ?: '-' }}</dd>
                                </div>
                            </dl>

                            <div class="mt-4 bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-2">Descripción</dt>
                                <dd class="text-sm text-gray-800 leading-relaxed">{{ $caso->descripcion }}</dd>
                            </div>
                        </div>
                    </div>

                    {{-- Historial de seguimientos --}}
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">
                                Historial de Seguimientos
                                @if($caso->seguimientos->isNotEmpty())
                                    <span class="text-gray-400 font-normal">({{ $caso->seguimientos->count() }})</span>
                                @endif
                            </h3>
                        </div>

                        <div class="p-4 sm:p-6">
                            @forelse($caso->seguimientos as $seg)
                                <div class="flex gap-3 sm:gap-4 {{ !$loop->last ? 'mb-4 pb-4 border-b border-gray-100' : '' }}">
                                    <div class="shrink-0 flex flex-col items-center">
                                        <div class="w-9 h-9 rounded-full bg-teal-50 flex items-center justify-center">
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
                                        @if(!$loop->last)
                                            <div class="w-0.5 flex-1 bg-gray-100 mt-1"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                                            <span class="text-sm font-semibold text-gray-700">{{ $seg->responsable->name ?? 'Desconocido' }}</span>
                                            <span class="text-xs text-gray-400">{{ $seg->fecha_seguimiento->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 capitalize mb-1.5">{{ $seg->accion }}</span>
                                        <p class="text-sm text-gray-600 leading-relaxed">{{ $seg->notas }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm text-gray-400">Aún no hay seguimientos registrados</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Sidebar: Formulario nuevo seguimiento --}}
                <div class="lg:col-span-1">
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden sticky top-6">
                        <div class="px-4 sm:px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-teal-50 to-cyan-50">
                            <h3 class="text-sm font-bold text-teal-800 uppercase tracking-wider">Nuevo Seguimiento</h3>
                        </div>

                        <div class="p-4 sm:p-5">
                            @if($errors->any())
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 text-xs">
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('seguimiento.store', $caso) }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="accion" class="block text-xs font-medium text-gray-600 mb-1.5">Tipo de Acción</label>
                                    <select id="accion" name="accion" required
                                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-gray-50 transition">
                                        <option value="">Selecciona</option>
                                        <option value="llamada">Llamada</option>
                                        <option value="reunion">Reunión</option>
                                        <option value="intervencion">Intervención</option>
                                        <option value="derivacion">Derivación</option>
                                        <option value="cierre">Cierre</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="notas" class="block text-xs font-medium text-gray-600 mb-1.5">Notas / Observaciones</label>
                                    <textarea id="notas" name="notas" rows="4" required
                                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-gray-50 transition resize-none"
                                              placeholder="Describe las acciones tomadas...">{{ old('notas') }}</textarea>
                                </div>

                                <div>
                                    <label for="nuevo_estado" class="block text-xs font-medium text-gray-600 mb-1.5">Estado del Caso</label>
                                    <select id="nuevo_estado" name="nuevo_estado" required
                                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-gray-50 transition">
                                        <option value="pendiente"  {{ $caso->estado == 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_proceso" {{ $caso->estado == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="resuelto"   {{ $caso->estado == 'resuelto'   ? 'selected' : '' }}>Resuelto</option>
                                        <option value="cerrado"    {{ $caso->estado == 'cerrado'    ? 'selected' : '' }}>Cerrado</option>
                                    </select>
                                </div>

                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold py-3 rounded-xl transition shadow-sm text-sm">
                                    Guardar Seguimiento
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
