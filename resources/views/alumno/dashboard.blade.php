<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
            </div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Mi Panel</h2>
        </div>
    </x-slot>

    @php
        $hora = now()->hour;
        if ($hora < 12) $saludo = '¡Buenos días';
        elseif ($hora < 18) $saludo = '¡Buenas tardes';
        else $saludo = '¡Buenas noches';
        $porcentajeResueltos = $totalCasos > 0 ? round(($resueltos / $totalCasos) * 100) : 0;
        $firstName = explode(' ', auth()->user()->name)[0];
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20">
        <div class="py-4 sm:py-6 lg:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5 sm:space-y-6 lg:space-y-8">

                {{-- ════════════════════════════════════════════════
                     HERO BANNER — Saludo personalizado
                     ════════════════════════════════════════════════ --}}
                <section class="animate-fade-in-up stagger-1">
                    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-blue-600 to-violet-700 animate-gradient text-white rounded-2xl sm:rounded-3xl shadow-xl shadow-indigo-200/40">
                        {{-- Decorative elements --}}
                        <div class="hero-pattern absolute inset-0"></div>
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-48 h-48 sm:w-64 sm:h-64 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-12 -ml-8 w-40 h-40 bg-blue-400/15 rounded-full blur-2xl"></div>
                        <div class="absolute top-1/2 right-1/4 w-20 h-20 bg-violet-400/10 rounded-full blur-xl animate-float"></div>

                        <div class="relative p-5 sm:p-8 lg:p-10">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                                {{-- Avatar --}}
                                <div class="hidden sm:flex shrink-0">
                                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center shadow-lg">
                                        <span class="text-2xl lg:text-3xl font-bold text-white/90">{{ strtoupper(substr($firstName, 0, 1)) }}</span>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 sm:hidden mb-2">
                                        <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                            <span class="text-lg font-bold text-white/90">{{ strtoupper(substr($firstName, 0, 1)) }}</span>
                                        </div>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium bg-white/15 backdrop-blur-sm px-2.5 py-1 rounded-full border border-white/10">
                                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                            Alumno
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3 mb-1">
                                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight">
                                            {{ $saludo }}, {{ $firstName }}!
                                        </h1>
                                        <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium bg-white/15 backdrop-blur-sm px-3 py-1 rounded-full border border-white/10">
                                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                            Alumno
                                        </span>
                                    </div>
                                    <p class="text-blue-100/90 text-sm sm:text-base lg:text-lg max-w-2xl leading-relaxed">
                                        Este es tu espacio seguro. Puedes reportar cualquier situación y hacer seguimiento de tus casos con total confidencialidad.
                                    </p>

                                    {{-- Quick summary chips (mobile compact) --}}
                                    @if($totalCasos > 0)
                                        <div class="flex flex-wrap gap-2 mt-4">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/15 backdrop-blur px-3 py-1.5 rounded-lg border border-white/10">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                {{ $totalCasos }} {{ $totalCasos === 1 ? 'reporte' : 'reportes' }}
                                            </span>
                                            @if($pendientes > 0)
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-amber-400/20 backdrop-blur px-3 py-1.5 rounded-lg border border-amber-300/20">
                                                    <span class="w-1.5 h-1.5 bg-amber-300 rounded-full"></span>
                                                    {{ $pendientes }} pendiente{{ $pendientes > 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                            @if($enProceso > 0)
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-sky-400/20 backdrop-blur px-3 py-1.5 rounded-lg border border-sky-300/20">
                                                    <span class="w-1.5 h-1.5 bg-sky-300 rounded-full"></span>
                                                    {{ $enProceso }} en proceso
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Decorative illustration (desktop) --}}
                                <div class="hidden lg:block shrink-0">
                                    <div class="w-32 h-32 relative animate-float">
                                        <div class="absolute inset-0 bg-white/10 rounded-3xl rotate-6 backdrop-blur-sm"></div>
                                        <div class="absolute inset-2 bg-white/10 rounded-2xl -rotate-3 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Shimmer overlay --}}
                        <div class="absolute inset-0 animate-shimmer pointer-events-none"></div>
                    </div>
                </section>

                {{-- ════════════════════════════════════════════════
                     STATS CARDS — Métricas principales
                     ════════════════════════════════════════════════ --}}
                @if($totalCasos > 0)
                    <section class="animate-fade-in-up stagger-2">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
                            {{-- Total Reportes --}}
                            <div class="stat-card group bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100/80 hover:border-indigo-200 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-indigo-50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 flex items-center justify-center ring-1 ring-indigo-100">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">Total</span>
                                    </div>
                                    <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight counter-value">{{ $totalCasos }}</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">Reportes enviados</p>
                                </div>
                            </div>

                            {{-- Pendientes --}}
                            <div class="stat-card group bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100/80 hover:border-amber-200 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-amber-50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center ring-1 ring-amber-100">
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        @if($pendientes > 0)
                                            <span class="relative flex h-2.5 w-2.5">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight counter-value">{{ $pendientes }}</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">Pendientes</p>
                                </div>
                            </div>

                            {{-- En Proceso --}}
                            <div class="stat-card group bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100/80 hover:border-blue-200 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center ring-1 ring-blue-100">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </div>
                                        @if($enProceso > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">
                                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                Activo
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight counter-value">{{ $enProceso }}</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">En proceso</p>
                                </div>
                            </div>

                            {{-- Resueltos --}}
                            <div class="stat-card group bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100/80 hover:border-emerald-200 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center ring-1 ring-emerald-100">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        @if($resueltos > 0)
                                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight counter-value">{{ $resueltos }}</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">Resueltos</p>
                                </div>
                            </div>
                        </div>

                        {{-- Progress bar --}}
                        @if($totalCasos > 0)
                            <div class="mt-3 sm:mt-4 bg-white rounded-xl sm:rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100/80">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                        <p class="text-sm font-semibold text-gray-700">Progreso de resolución</p>
                                    </div>
                                    <p class="text-sm font-bold text-indigo-600">{{ $porcentajeResueltos }}%</p>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 sm:h-3 overflow-hidden">
                                    <div class="progress-bar-gradient h-full rounded-full animate-progress-fill transition-all duration-1000"
                                         style="width: {{ $porcentajeResueltos }}%">
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-2.5">
                                    <div class="flex items-center gap-4 text-[10px] sm:text-xs text-gray-400">
                                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Pendiente</span>
                                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> En proceso</span>
                                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Resuelto</span>
                                    </div>
                                    <p class="text-[10px] sm:text-xs text-gray-400">{{ $resueltos }}/{{ $totalCasos }}</p>
                                </div>
                            </div>
                        @endif
                    </section>
                @else
                    {{-- Empty state --}}
                    <section class="animate-fade-in-up stagger-2">
                        <div class="bg-white rounded-2xl sm:rounded-3xl p-8 sm:p-12 shadow-sm border border-gray-100 text-center">
                            <div class="empty-illustration mx-auto w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-br from-indigo-50 to-blue-100 flex items-center justify-center mb-5">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">No tienes reportes aún</h3>
                            <p class="text-sm sm:text-base text-gray-500 max-w-sm mx-auto">Cuando envíes un reporte, aquí podrás ver las estadísticas y el seguimiento de cada caso.</p>
                        </div>
                    </section>
                @endif

                {{-- ════════════════════════════════════════════════
                     CONSULTA DE EXPEDIENTE
                     ════════════════════════════════════════════════ --}}
                <section class="animate-fade-in-up stagger-3">
                    <div class="bg-white shadow-sm rounded-2xl sm:rounded-3xl border border-gray-100/80 overflow-hidden">
                        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-gray-800">Consultar expediente</h3>
                                <p class="text-[10px] sm:text-xs text-gray-400">Busca por número de expediente o por tu nombre</p>
                            </div>
                        </div>

                        <div class="px-5 sm:px-6 py-4 sm:py-5">
                            <form method="POST" action="{{ route('consultar.expediente') }}" id="dashSearchForm" class="space-y-3">
                                @csrf
                                <input type="hidden" name="tipo" id="dashTipoInput" value="codigo">

                                {{-- Tabs tipo --}}
                                <div class="flex gap-1.5 bg-gray-50 border border-gray-200 rounded-xl p-1">
                                    <button type="button"
                                            id="dashTabCodigo"
                                            onclick="dashSetTipo('codigo')"
                                            class="flex-1 text-xs sm:text-sm font-semibold py-1.5 px-3 rounded-lg bg-indigo-600 text-white shadow-sm transition-all">
                                        🔢 Código de expediente
                                    </button>
                                    <button type="button"
                                            id="dashTabNombre"
                                            onclick="dashSetTipo('nombre')"
                                            class="flex-1 text-xs sm:text-sm font-medium py-1.5 px-3 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-white transition-all">
                                        👤 Por nombre
                                    </button>
                                </div>

                                {{-- Input + botón --}}
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        id="dashBusquedaInput"
                                        name="busqueda"
                                        placeholder="Ej. VIO-2026-ABCDE1"
                                        class="flex-1 text-sm border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition placeholder-gray-400"
                                        required
                                        autocomplete="off"
                                    >
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        Consultar
                                    </button>
                                </div>
                                <p class="text-[11px] text-gray-400" id="dashHintText">
                                    Ingresa el código que recibiste al crear tu reporte (formato VIO-AÑO-CÓDIGO).
                                </p>
                            </form>
                        </div>
                    </div>
                </section>

                {{-- ════════════════════════════════════════════════
                     QUICK ACTIONS — Acciones principales
                     ════════════════════════════════════════════════ --}}
                <section class="animate-fade-in-up stagger-3">
                    <div class="flex items-center gap-2 mb-3 sm:mb-4">
                        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Acciones rápidas</h2>
                        <div class="flex-1 h-px bg-gradient-to-r from-gray-200 to-transparent"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        {{-- Reportar incidente --}}
                        <a href="{{ route('alumno.denuncia') }}"
                           class="action-card focus-ring group relative overflow-hidden bg-white border border-gray-100 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-sm">
                            {{-- Background decoration --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-red-50/50 to-orange-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-red-50 rounded-full opacity-0 group-hover:opacity-60 transition-all duration-500 group-hover:scale-110"></div>

                            <div class="relative">
                                <div class="flex items-start gap-4">
                                    <div class="shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-red-500 to-rose-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-red-200/50 group-hover:shadow-red-300/60 group-hover:scale-105 transition-all duration-300">
                                        <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="font-extrabold text-gray-800 text-base sm:text-lg">Reportar un incidente</p>
                                            <span class="hidden sm:inline-flex items-center px-2 py-0.5 text-[10px] font-bold text-red-700 bg-red-100 rounded-full uppercase tracking-wide">Nuevo</span>
                                        </div>
                                        <p class="text-sm text-gray-500 leading-relaxed">Puedes hacerlo de forma anónima o identificada. Tu reporte es confidencial.</p>
                                        <div class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-red-600 group-hover:text-red-700 transition-colors">
                                            Crear reporte
                                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        {{-- Ver mis casos --}}
                        <a href="{{ route('alumno.mis-casos') }}"
                           class="action-card focus-ring group relative overflow-hidden bg-white border border-gray-100 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-violet-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-indigo-50 rounded-full opacity-0 group-hover:opacity-60 transition-all duration-500 group-hover:scale-110"></div>

                            <div class="relative">
                                <div class="flex items-start gap-4">
                                    <div class="shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-violet-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200/50 group-hover:shadow-indigo-300/60 group-hover:scale-105 transition-all duration-300">
                                        <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="font-extrabold text-gray-800 text-base sm:text-lg">Ver mis reportes</p>
                                            @if($totalCasos > 0)
                                                <span class="inline-flex items-center justify-center w-6 h-6 text-[10px] font-bold text-indigo-700 bg-indigo-100 rounded-full">{{ $totalCasos }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 leading-relaxed">Consulta el estado de tus casos y revisa las actualizaciones del equipo.</p>
                                        <div class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 group-hover:text-indigo-700 transition-colors">
                                            Ver reportes
                                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </section>

                {{-- ════════════════════════════════════════════════
                     RECENT CASES — Timeline de últimos casos
                     ════════════════════════════════════════════════ --}}
                @if(isset($ultimosCasos) && $ultimosCasos->isNotEmpty())
                    <section class="animate-fade-in-up stagger-4">
                        <div class="bg-white shadow-sm rounded-2xl sm:rounded-3xl border border-gray-100/80 overflow-hidden">
                            <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 bg-gradient-to-r from-white to-gray-50/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm sm:text-base font-bold text-gray-800">Actividad reciente</h3>
                                            <p class="text-[10px] sm:text-xs text-gray-400">Últimos reportes enviados</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('alumno.mis-casos') }}"
                                       class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-all">
                                        Ver todos
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>

                            <div class="divide-y divide-gray-50 smooth-scroll max-h-[400px] overflow-y-auto">
                                @foreach($ultimosCasos as $index => $caso)
                                    @php
                                        $estadoConfig = [
                                            'pendiente'  => ['label' => 'Pendiente',  'bg' => 'bg-amber-50',   'text' => 'text-amber-700',  'border' => 'border-amber-200', 'dot' => 'bg-amber-400', 'icon_bg' => 'bg-amber-100'],
                                            'en_proceso' => ['label' => 'En Proceso', 'bg' => 'bg-blue-50',    'text' => 'text-blue-700',   'border' => 'border-blue-200',  'dot' => 'bg-blue-500',  'icon_bg' => 'bg-blue-100'],
                                            'resuelto'   => ['label' => 'Resuelto',   'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700','border' => 'border-emerald-200','dot' => 'bg-emerald-500','icon_bg' => 'bg-emerald-100'],
                                            'cerrado'    => ['label' => 'Cerrado',    'bg' => 'bg-gray-50',    'text' => 'text-gray-600',   'border' => 'border-gray-200',  'dot' => 'bg-gray-400',  'icon_bg' => 'bg-gray-100'],
                                        ];
                                        $st = $estadoConfig[$caso->estado] ?? $estadoConfig['pendiente'];
                                        $tipoConfig = [
                                            'fisica' => ['label' => 'Física', 'color' => 'text-red-600', 'bg' => 'bg-red-50'],
                                            'psicologica' => ['label' => 'Psicológica', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                                            'verbal' => ['label' => 'Verbal', 'color' => 'text-orange-600', 'bg' => 'bg-orange-50'],
                                            'sexual' => ['label' => 'Sexual', 'color' => 'text-rose-600', 'bg' => 'bg-rose-50'],
                                            'ciberacoso' => ['label' => 'Ciberacoso', 'color' => 'text-cyan-600', 'bg' => 'bg-cyan-50'],
                                            'discriminacion' => ['label' => 'Discriminación', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
                                            'otro' => ['label' => 'Otro', 'color' => 'text-gray-600', 'bg' => 'bg-gray-50'],
                                        ];
                                        $tipo = $tipoConfig[$caso->tipo_violencia] ?? $tipoConfig['otro'];
                                    @endphp
                                    <div class="animate-fade-in-up stagger-{{ $index + 4 }} timeline-connector px-5 sm:px-6 py-4 sm:py-5 hover:bg-gradient-to-r hover:from-indigo-50/30 hover:to-transparent transition-all duration-300 group cursor-default">
                                        <div class="flex gap-3 sm:gap-4">
                                            {{-- Timeline dot --}}
                                            <div class="shrink-0 pt-0.5">
                                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl {{ $st['icon_bg'] }} flex items-center justify-center ring-2 ring-white shadow-sm">
                                                    @if($caso->estado === 'resuelto')
                                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    @elseif($caso->estado === 'en_proceso')
                                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    @elseif($caso->estado === 'cerrado')
                                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                                                    @else
                                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Content --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 sm:gap-3">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="font-mono font-bold text-indigo-700 text-xs sm:text-sm bg-indigo-50/80 px-2 py-0.5 rounded-md">{{ $caso->codigo_caso }}</span>
                                                        <span class="{{ $st['bg'] }} {{ $st['text'] }} {{ $st['border'] }} border text-[10px] sm:text-xs font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1.5">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                                            {{ $st['label'] }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="{{ $tipo['bg'] }} {{ $tipo['color'] }} text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-md">{{ $tipo['label'] }}</span>
                                                        <span class="text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">{{ $caso->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                                <p class="text-xs sm:text-sm text-gray-600 mt-2 line-clamp-2 sm:line-clamp-1 leading-relaxed">{{ $caso->descripcion }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                {{-- ════════════════════════════════════════════════
                     INFO CARDS — Seguridad y seguimiento
                     ════════════════════════════════════════════════ --}}
                <section class="animate-fade-in-up stagger-5">
                    <div class="flex items-center gap-2 mb-3 sm:mb-4">
                        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Información</h2>
                        <div class="flex-1 h-px bg-gradient-to-r from-gray-200 to-transparent"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                        {{-- Card 1: Confidencialidad --}}
                        <div class="glass-card group rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-emerald-100/60 hover:border-emerald-200">
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200/40 group-hover:scale-105 transition-transform duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 text-sm sm:text-base">100% Confidencial</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1.5 leading-relaxed">Solo el psicólogo asignado tendrá acceso. Tu privacidad es nuestra prioridad.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Card 2: Seguimiento --}}
                        <div class="glass-card group rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-blue-100/60 hover:border-blue-200">
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200/40 group-hover:scale-105 transition-transform duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 text-sm sm:text-base">Seguimiento activo</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1.5 leading-relaxed">El equipo revisará tu reporte y te informará sobre los avances.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Card 3: Denuncia anónima --}}
                        <div class="glass-card group rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-violet-100/60 hover:border-violet-200 sm:col-span-2 lg:col-span-1">
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-violet-200/40 group-hover:scale-105 transition-transform duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 text-sm sm:text-base">Denuncia anónima</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1.5 leading-relaxed">No necesitas dar tu nombre. Puedes reportar sin identificarte.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ════════════════════════════════════════════════
                     HELP BANNER — Línea de ayuda
                     ════════════════════════════════════════════════ --}}
                <section class="animate-fade-in-up stagger-6">
                    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl border border-amber-200/60 shadow-sm">
                        {{-- Background --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-50 via-orange-50/60 to-yellow-50/40"></div>
                        <div class="absolute top-0 right-0 w-48 h-48 bg-amber-100/40 rounded-full -mr-12 -mt-12 blur-2xl"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-orange-100/30 rounded-full -ml-8 -mb-8 blur-xl"></div>

                        <div class="relative p-5 sm:p-7">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="shrink-0 w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-amber-200/50 animate-pulse-glow">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-amber-900 text-sm sm:text-base lg:text-lg">¿Necesitas ayuda inmediata?</p>
                                        <p class="text-xs sm:text-sm text-amber-800/80 mt-1 leading-relaxed max-w-lg">
                                            Habla directamente con tu orientador escolar o comunícate con la línea de emergencias. En situaciones graves, pide ayuda presencial.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:shrink-0">
                                    <div class="flex items-center gap-2 bg-white/70 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-amber-200/50">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <div>
                                            <p class="text-[10px] text-amber-600 font-medium uppercase tracking-wider">Línea SíseVe</p>
                                            <p class="text-sm font-bold text-amber-900">0800-76-888</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 bg-white/70 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-amber-200/50">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <div>
                                            <p class="text-[10px] text-amber-600 font-medium uppercase tracking-wider">Emergencias</p>
                                            <p class="text-sm font-bold text-amber-900">105</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ════════════════════════════════════════════════
                     FOOTER TIPS
                     ════════════════════════════════════════════════ --}}
                <section class="animate-fade-in-up stagger-7 pb-4">
                    <div class="flex items-center justify-center gap-3 text-xs text-gray-400">
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Tus datos están encriptados y protegidos. Solo personal autorizado puede acceder a la información de tus reportes.</span>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <script>
        function dashSetTipo(valor) {
            document.getElementById('dashTipoInput').value = valor;

            const tabCodigo = document.getElementById('dashTabCodigo');
            const tabNombre = document.getElementById('dashTabNombre');
            const input     = document.getElementById('dashBusquedaInput');
            const hint      = document.getElementById('dashHintText');

            if (valor === 'nombre') {
                tabNombre.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                tabNombre.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:bg-white');
                tabCodigo.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                tabCodigo.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:bg-white');
                input.placeholder = 'Ej. Juan Pérez García';
                hint.textContent  = 'Solo se muestran casos identificados (no anónimos). Máximo 10 resultados.';
            } else {
                tabCodigo.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                tabCodigo.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:bg-white');
                tabNombre.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                tabNombre.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:bg-white');
                input.placeholder = 'Ej. VIO-2026-ABCDE1';
                hint.textContent  = 'Ingresa el código que recibiste al crear tu reporte (formato VIO-AÑO-CÓDIGO).';
            }
            input.value = '';
            input.focus();
        }
    </script>
</x-app-layout>
