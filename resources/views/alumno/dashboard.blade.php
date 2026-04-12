<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mi Panel — Alumno
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Bienvenida --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow">
                <h3 class="text-2xl font-bold">Hola, {{ auth()->user()->name }} 👋</h3>
                <p class="mt-1 opacity-90">
                    Este es tu espacio seguro. Puedes reportar cualquier situación y hacer seguimiento de tus casos.
                </p>
            </div>

            {{-- Acciones rápidas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Reportar incidente --}}
                <a href="{{ route('alumno.denuncia') }}"
                   class="flex items-center gap-4 bg-white border border-red-200 rounded-2xl p-5 shadow hover:shadow-md hover:border-red-400 transition group">
                    <div class="bg-red-100 text-red-600 rounded-xl p-3 group-hover:bg-red-200 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">Reportar un incidente</p>
                        <p class="text-sm text-gray-500">Anónimo o identificado, tú decides</p>
                    </div>
                </a>

                {{-- Ver mis casos --}}
                <a href="{{ route('alumno.mis-casos') }}"
                   class="flex items-center gap-4 bg-white border border-indigo-200 rounded-2xl p-5 shadow hover:shadow-md hover:border-indigo-400 transition group">
                    <div class="bg-indigo-100 text-indigo-600 rounded-xl p-3 group-hover:bg-indigo-200 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">Ver mis reportes</p>
                        <p class="text-sm text-gray-500">Consulta el estado de tus casos</p>
                    </div>
                </a>

            </div>

            {{-- Tarjeta de privacidad --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-center gap-4 bg-white border border-green-200 rounded-2xl p-5 shadow">
                    <div class="bg-green-100 text-green-600 rounded-xl p-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">Tus datos están protegidos</p>
                        <p class="text-sm text-gray-500">Solo el psicólogo asignado verá tu caso</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-white border border-yellow-200 rounded-2xl p-5 shadow">
                    <div class="bg-yellow-100 text-yellow-600 rounded-xl p-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">Seguimiento activo</p>
                        <p class="text-sm text-gray-500">El equipo revisará tu reporte pronto</p>
                    </div>
                </div>
            </div>

            {{-- Línea de ayuda --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 text-sm text-yellow-800 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>
                    <strong>¿Necesitas ayuda inmediata?</strong>
                    Habla directamente con tu orientador o llama a la línea de emergencias de tu institución.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
