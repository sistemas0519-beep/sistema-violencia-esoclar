<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel del Docente
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 text-white rounded-2xl p-6 shadow">
                <h3 class="text-2xl font-bold">Hola, {{ auth()->user()->name }} 👋</h3>
                <p class="mt-1 opacity-90">Puedes reportar situaciones de violencia que observes en el plantel.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('alumno.denuncia') }}"
                   class="flex items-center gap-4 bg-white border border-indigo-200 rounded-2xl p-5 shadow hover:shadow-md hover:border-indigo-400 transition">
                    <div class="bg-indigo-100 text-indigo-600 rounded-xl p-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">Registrar un reporte</p>
                        <p class="text-sm text-gray-500">Documenta incidentes observados</p>
                    </div>
                </a>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-sm text-blue-800">
                <strong>Recuerda:</strong> Todos los reportes son confidenciales y serán atendidos por el equipo de psicología.
            </div>
        </div>
    </div>
</x-app-layout>
