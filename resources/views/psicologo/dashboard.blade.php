<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel del Psicólogo
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 rounded-xl p-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header --}}
            <div class="bg-gradient-to-r from-teal-600 to-cyan-700 text-white rounded-2xl p-6 shadow">
                <h3 class="text-2xl font-bold">Bienvenida, {{ auth()->user()->name }} 👩‍⚕️</h3>
                <p class="mt-1 opacity-90">Aquí están los casos pendientes y los que tienes asignados.</p>
            </div>

            {{-- Tabla de casos --}}
            <div class="bg-white shadow rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Código</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Denunciante</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Asignado a</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($casos as $caso)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-mono font-bold text-indigo-700">{{ $caso->codigo_caso }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $colores = [
                                            'fisica'         => 'bg-red-100 text-red-700',
                                            'psicologica'    => 'bg-orange-100 text-orange-700',
                                            'verbal'         => 'bg-yellow-100 text-yellow-700',
                                            'sexual'         => 'bg-pink-100 text-pink-700',
                                            'ciberacoso'     => 'bg-blue-100 text-blue-700',
                                            'discriminacion' => 'bg-purple-100 text-purple-700',
                                            'otro'           => 'bg-gray-100 text-gray-700',
                                        ];
                                        $etiquetas = [
                                            'fisica' => 'Física', 'psicologica' => 'Psicológica',
                                            'verbal' => 'Verbal', 'sexual' => 'Sexual',
                                            'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminación',
                                            'otro' => 'Otro',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colores[$caso->tipo_violencia] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $etiquetas[$caso->tipo_violencia] ?? $caso->tipo_violencia }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $estadoColor = [
                                            'pendiente'  => 'bg-yellow-100 text-yellow-700',
                                            'en_proceso' => 'bg-blue-100 text-blue-700',
                                            'resuelto'   => 'bg-green-100 text-green-700',
                                            'cerrado'    => 'bg-gray-100 text-gray-600',
                                        ];
                                        $estadoLabel = [
                                            'pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso',
                                            'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $estadoColor[$caso->estado] ?? '' }}">
                                        {{ $estadoLabel[$caso->estado] ?? $caso->estado }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $caso->es_anonimo ? '🔒 Anónimo' : ($caso->denunciante->name ?? '—') }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $caso->asignado->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    {{ $caso->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('psicologo.caso', $caso) }}"
                                       class="text-indigo-600 hover:underline font-medium text-xs">Ver detalle</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay casos por atender.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div>{{ $casos->links() }}</div>

        </div>
    </div>
</x-app-layout>
