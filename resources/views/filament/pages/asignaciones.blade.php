<x-filament-panels::page>

    {{-- Botón para crear nueva asignación --}}
    <div class="mb-6">
        <a href="{{ \App\Filament\Resources\AsignacionResource::getUrl('create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            ➕ Crear Nueva Asignación
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Lista de Psicólogos --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                <span class="text-lg">👨‍⚕️</span> Psicólogos Registrados
            </h3>
            
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($this->getPsicologos() as $psicologo)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                            {{ strtoupper(substr($psicologo->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                                {{ $psicologo->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $psicologo->especialidad ?? 'Sin especialidad' }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($psicologo->disponibilidad === 'disponible') bg-green-100 text-green-700
                            @elseif($psicologo->disponibilidad === 'ocupado') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($psicologo->disponibilidad ?? 'disponible') }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">No hay psicólogos registrados</p>
                @endforelse
            </div>
        </div>

        {{-- Pacientes Pendientes de Asignación --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                <span class="text-lg">👤</span> Pacientes Sin Asignar
            </h3>
            
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($this->getPacientesSinAsignar() as $paciente)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800">
                        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center text-amber-600 dark:text-amber-400 font-bold text-sm">
                            {{ strtoupper(substr($paciente->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                                {{ $paciente->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">
                                {{ $paciente->rol }}
                            </p>
                        </div>
                        <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">Sin asignar</span>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-400">Todos los pacientes tienen asignaciones activas</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Asignaciones Activas --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                <span class="text-lg">📋</span> Asignaciones Activas
            </h3>
            
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($this->getAsignacionesActivas() as $asignacion)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center text-green-600 dark:text-green-400 font-bold text-sm">
                            {{ strtoupper(substr($asignacion->psicologo->name ?? 'PS', 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                                {{ $asignacion->paciente->name ?? 'Paciente' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                → {{ $asignacion->psicologo->name ?? 'Psicólogo' }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                            {{ ucfirst($asignacion->frecuencia_atencion) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-400">No hay asignaciones activas</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Tabla de Todas las Asignaciones --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200">Todas las Asignaciones</h3>
        </div>
        
        {{ $this->table }}
    </div>

</x-filament-panels::page>