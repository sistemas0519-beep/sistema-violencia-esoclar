<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Mi Perfil</h2>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20">
        <div class="py-6 sm:py-8 lg:py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                {{-- ── Banner de usuario ─────────────────────────────────── --}}
                <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-blue-600 to-violet-700 text-white rounded-2xl shadow-xl shadow-indigo-200/40 p-6 sm:p-8">
                    <div class="absolute top-0 right-0 -mt-6 -mr-6 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="relative flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-white/20 border border-white/20 flex items-center justify-center shadow-lg shrink-0">
                            <span class="text-2xl font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-extrabold">{{ auth()->user()->name }}</h1>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="text-blue-100 text-sm">{{ auth()->user()->email }}</span>
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/15 px-2.5 py-1 rounded-full border border-white/10 capitalize">
                                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                                    @php
                                        $labels = [
                                            'admin'     => 'Administrador',
                                            'alumno'    => 'Alumno',
                                            'docente'   => 'Docente',
                                            'psicologo' => 'Psicólogo',
                                            'asistente' => 'Asistente',
                                        ];
                                    @endphp
                                    {{ $labels[auth()->user()->rol] ?? auth()->user()->rol }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Actualizar datos personales ───────────────────────── --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">Datos personales</h3>
                            <p class="text-xs text-gray-500">Actualiza tu nombre y correo electrónico</p>
                        </div>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- ── Cambiar contraseña ────────────────────────────────── --}}
                <div id="update-password" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">Cambiar contraseña</h3>
                            <p class="text-xs text-gray-500">Asegura tu cuenta con una contraseña segura</p>
                        </div>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>


            </div>
        </div>
    </div>
</x-app-layout>
