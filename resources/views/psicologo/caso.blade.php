<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Caso {{ $caso->codigo_caso }}
            </h2>
            <a href="{{ route('psicologo.dashboard') }}"
               class="text-sm text-indigo-600 hover:underline">← Volver al panel</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 rounded-xl p-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Info del caso --}}
            <div class="bg-white shadow rounded-2xl p-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-bold text-gray-800">Información del Caso</h3>

                    @if(! $caso->asignado_a)
                        <form action="{{ route('caso.asignar', $caso) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-xl transition">
                                Asignarme este caso
                            </button>
                        </form>
                    @endif
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 font-medium">Código</dt>
                        <dd class="font-mono font-bold text-indigo-700">{{ $caso->codigo_caso }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Estado</dt>
                        <dd>
                            @php
                                $estadoColor = [
                                    'pendiente'  => 'bg-yellow-100 text-yellow-700',
                                    'en_proceso' => 'bg-blue-100 text-blue-700',
                                    'resuelto'   => 'bg-green-100 text-green-700',
                                    'cerrado'    => 'bg-gray-100 text-gray-600',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $estadoColor[$caso->estado] ?? '' }}">
                                {{ ucfirst(str_replace('_', ' ', $caso->estado)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Tipo de Violencia</dt>
                        <dd class="font-semibold capitalize">{{ str_replace('_', ' ', $caso->tipo_violencia) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Denunciante</dt>
                        <dd>{{ $caso->es_anonimo ? '🔒 Anónimo' : ($caso->denunciante->name ?? '—') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Asignado a</dt>
                        <dd>{{ $caso->asignado->name ?? 'Sin asignar' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Fecha del incidente</dt>
                        <dd>{{ $caso->fecha_incidente ? $caso->fecha_incidente->format('d/m/Y') : '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 font-medium">Descripción</dt>
                        <dd class="mt-1 text-gray-800 leading-relaxed">{{ $caso->descripcion }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Historial de seguimientos --}}
            <div class="bg-white shadow rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Seguimientos</h3>

                @forelse($caso->seguimientos as $seg)
                    <div class="border-l-4 border-indigo-400 pl-4 mb-4">
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span class="font-semibold text-gray-700">{{ $seg->responsable->name ?? 'Desconocido' }}</span>
                            <span>{{ $seg->fecha_seguimiento->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-xs text-indigo-600 mt-0.5 capitalize">Acción: {{ $seg->accion }}</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $seg->notas }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Aún no hay seguimientos registrados.</p>
                @endforelse
            </div>

            {{-- Formulario nuevo seguimiento --}}
            <div class="bg-white shadow rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Registrar Nuevo Seguimiento</h3>

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-300 text-red-700 rounded-xl p-4 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('seguimiento.store', $caso) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="accion" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Acción</label>
                        <select id="accion" name="accion" required
                                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Selecciona —</option>
                            <option value="llamada">Llamada</option>
                            <option value="reunion">Reunión</option>
                            <option value="intervencion">Intervención</option>
                            <option value="derivacion">Derivación</option>
                            <option value="cierre">Cierre</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div>
                        <label for="notas" class="block text-sm font-medium text-gray-700 mb-1">Notas / Observaciones</label>
                        <textarea id="notas" name="notas" rows="4" required
                                  class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Describe las acciones tomadas...">{{ old('notas') }}</textarea>
                    </div>

                    <div>
                        <label for="nuevo_estado" class="block text-sm font-medium text-gray-700 mb-1">Actualizar Estado del Caso</label>
                        <select id="nuevo_estado" name="nuevo_estado" required
                                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="pendiente"  {{ $caso->estado == 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ $caso->estado == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="resuelto"   {{ $caso->estado == 'resuelto'   ? 'selected' : '' }}>Resuelto</option>
                            <option value="cerrado"    {{ $caso->estado == 'cerrado'    ? 'selected' : '' }}>Cerrado</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl transition">
                        Guardar Seguimiento
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
