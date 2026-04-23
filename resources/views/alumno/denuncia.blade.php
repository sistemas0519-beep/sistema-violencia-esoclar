<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Reportar Incidente</h2>
        </div>
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
        .field-input {
            width: 100%; border: 1px solid #d1d5db; border-radius: .75rem;
            padding: .5rem .75rem; font-size: .875rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .field-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
        .field-input.has-error { border-color: #f87171; background: #fff7f7; }
        .field-input:disabled { background: #f9fafb; cursor: not-allowed; color: #9ca3af; }
        @keyframes fadeSlide { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .animate-fade-slide { animation: fadeSlide .25s ease both; }
        .spinner { display:inline-block; width:1rem; height:1rem; border:2px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:spin .7s linear infinite; }
        .spinner-dark { display:inline-block; width:.9rem; height:.9rem; border:2px solid #d1d5db; border-top-color:#6b7280; border-radius:50%; animation:spin .7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <div
        class="py-8"
        x-data="denunciaEscuela()"
        x-init="init()"
    >
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div class="animate-fade-slide bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-2xl p-4 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->has('_throttle'))
                <div class="animate-fade-slide bg-amber-50 border border-amber-300 text-amber-800 rounded-2xl p-4 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <p class="font-medium">{{ $errors->first('_throttle') }}</p>
                </div>
            @endif

            @php $generalErrors = collect($errors->messages())->except('_throttle')->flatten()->all(); @endphp
            @if(count($generalErrors) > 0)
                <div class="animate-fade-slide bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 shadow-sm">
                    <p class="font-semibold text-sm mb-1">Corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-sm">
                        @foreach($generalErrors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="bg-gradient-to-r from-red-600 to-rose-500 px-6 py-5 text-white">
                    <div class="flex items-start gap-4">
                        <div class="p-2.5 bg-white/20 rounded-xl backdrop-blur-sm shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Formulario de Reporte Seguro</h3>
                            <p class="text-sm text-white/80 mt-0.5">Tu información está protegida. Puedes reportar de forma anónima si lo prefieres.</p>
                        </div>
                    </div>
                </div>

                <form
                    action="{{ route('denuncia.store') }}"
                    method="POST"
                    class="p-6 space-y-6"
                    @submit.prevent="enviarFormulario"
                    x-ref="form"
                >
                    @csrf

                    <input type="hidden" name="region"         x-model="regionNombre">
                    <input type="hidden" name="provincia"      x-model="provinciaNombre">
                    <input type="hidden" name="distrito"       x-model="distritoNombre">
                    <input type="hidden" name="escuela_nombre" x-model="escuelaNombre">
                    <input type="hidden" name="codigo_modular" x-model="codigoModular">

                    {{-- SECCIÓN 1: PRIVACIDAD --}}
                    <section>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">1. Privacidad del reporte</h4>
                        <div
                            class="rounded-xl border-2 p-4 cursor-pointer transition-colors select-none"
                            :class="esAnonimo ? 'border-indigo-200 bg-indigo-50' : 'border-gray-200 bg-gray-50'"
                            @click="esAnonimo = !esAnonimo"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors shrink-0"
                                         :class="esAnonimo ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                                        <svg x-show="esAnonimo" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Enviar reporte anónimo</p>
                                        <p class="text-xs text-gray-500">Tu nombre no aparecerá en el reporte</p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                      :class="esAnonimo ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-600'"
                                      x-text="esAnonimo ? 'Anónimo' : 'Con mis datos'"></span>
                            </div>
                            <div x-show="!esAnonimo" x-transition class="mt-3 pt-3 border-t border-gray-200">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Se incluirá tu nombre: <strong>{{ auth()->user()->name }}</strong></span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="es_anonimo" :value="esAnonimo ? '1' : '0'">
                    </section>

                    {{-- SECCIÓN 2: UBICACIÓN --}}
                    <section>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">2. Ubicación del incidente</h4>

                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-3 text-sm text-blue-800 mb-4">
                            <p class="font-medium">¿Cómo ubicar tu escuela?</p>
                            <p class="mt-1 text-xs text-blue-700">Selecciona región → provincia → distrito y luego haz clic en <strong>Buscar escuela</strong>. El buscador usa el padrón oficial del MINEDU.</p>
                        </div>

                        @if(old('escuela_nombre'))
                            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800 flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Escuela guardada: <strong>{{ old('escuela_nombre') }}</strong> — {{ old('distrito') }}, {{ old('provincia') }}, {{ old('region') }}</span>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="region_codigo" class="block text-sm font-medium text-gray-700 mb-1">Región <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="region_codigo" x-model="regionCodigo" @change="onRegionChange" :disabled="cargandoRegiones"
                                            class="field-input pr-8 @error('region') has-error @enderror">
                                        <option value="" x-text="cargandoRegiones ? 'Cargando...' : 'Selecciona una región'"></option>
                                        <template x-for="item in regiones" :key="item.codigo">
                                            <option :value="item.codigo" x-text="item.nombre"></option>
                                        </template>
                                    </select>
                                    <span x-show="cargandoRegiones" class="absolute right-2.5 top-2.5"><span class="spinner-dark"></span></span>
                                </div>
                            </div>
                            <div>
                                <label for="provincia_codigo" class="block text-sm font-medium text-gray-700 mb-1">Provincia <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="provincia_codigo" x-model="provinciaCodigo" @change="onProvinciaChange" :disabled="!regionCodigo || cargandoProvincias"
                                            class="field-input pr-8 @error('provincia') has-error @enderror">
                                        <option value="" x-text="cargandoProvincias ? 'Cargando...' : (!regionCodigo ? 'Selecciona región primero' : 'Selecciona una provincia')"></option>
                                        <template x-for="item in provincias" :key="item.codigo">
                                            <option :value="item.codigo" x-text="item.nombre"></option>
                                        </template>
                                    </select>
                                    <span x-show="cargandoProvincias" class="absolute right-2.5 top-2.5"><span class="spinner-dark"></span></span>
                                </div>
                            </div>
                            <div>
                                <label for="distrito_codigo" class="block text-sm font-medium text-gray-700 mb-1">Distrito <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="distrito_codigo" x-model="distritoCodigo" @change="onDistritoChange" :disabled="!provinciaCodigo || cargandoDistritos"
                                            class="field-input pr-8 @error('distrito') has-error @enderror">
                                        <option value="" x-text="cargandoDistritos ? 'Cargando...' : (!provinciaCodigo ? 'Selecciona provincia primero' : 'Selecciona un distrito')"></option>
                                        <template x-for="item in distritos" :key="item.codigo">
                                            <option :value="item.codigo" x-text="item.nombre"></option>
                                        </template>
                                    </select>
                                    <span x-show="cargandoDistritos" class="absolute right-2.5 top-2.5"><span class="spinner-dark"></span></span>
                                </div>
                            </div>
                            <div class="flex items-end">
                                <button type="button" @click="abrirBuscador" :disabled="!distritoCodigo"
                                        class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-semibold py-2.5 rounded-xl transition text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Buscar escuela
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Escuela seleccionada <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="escuelaNombre" readonly placeholder="Selecciona tu escuela desde el buscador"
                                           class="field-input bg-white @error('escuela_nombre') has-error @enderror">
                                    @error('escuela_nombre')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Código modular</label>
                                    <input type="text" x-model="codigoModular" readonly placeholder="Se completa automáticamente" class="field-input bg-white">
                                </div>
                            </div>
                            <template x-if="escuelaNivel || escuelaDireccion">
                                <div class="mt-3 pt-3 border-t border-gray-200 flex flex-wrap gap-2 text-xs text-gray-500">
                                    <span x-show="escuelaNivel" class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-medium" x-text="escuelaNivel"></span>
                                    <span x-show="escuelaDireccion" x-text="escuelaDireccion"></span>
                                </div>
                            </template>
                            <p class="mt-2 text-xs text-gray-400">Si cambias la ubicación, la escuela seleccionada se limpiará automáticamente.</p>
                        </div>
                    </section>

                    {{-- SECCIÓN 3: DETALLES --}}
                    <section>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">3. Detalles del incidente</h4>
                        <div class="space-y-4">

                            <div>
                                <label for="tipo_violencia" class="block text-sm font-medium text-gray-700 mb-1">Tipo de violencia <span class="text-red-500">*</span></label>
                                <select id="tipo_violencia" name="tipo_violencia" required x-model="tipoViolencia"
                                        class="field-input @error('tipo_violencia') has-error @enderror">
                                    <option value="">Selecciona una opción</option>
                                    <option value="fisica"         {{ old('tipo_violencia') == 'fisica'         ? 'selected' : '' }}>Física — golpes, empujones u otras agresiones corporales</option>
                                    <option value="psicologica"    {{ old('tipo_violencia') == 'psicologica'    ? 'selected' : '' }}>Psicológica — intimidación, humillación o manipulación emocional</option>
                                    <option value="verbal"         {{ old('tipo_violencia') == 'verbal'         ? 'selected' : '' }}>Verbal — insultos, gritos o lenguaje degradante</option>
                                    <option value="sexual"         {{ old('tipo_violencia') == 'sexual'         ? 'selected' : '' }}>Sexual — tocamientos o acoso de naturaleza sexual</option>
                                    <option value="ciberacoso"     {{ old('tipo_violencia') == 'ciberacoso'     ? 'selected' : '' }}>Ciberacoso / Cyberbullying — acoso mediante redes o mensajes</option>
                                    <option value="discriminacion" {{ old('tipo_violencia') == 'discriminacion' ? 'selected' : '' }}>Discriminación — por raza, género, discapacidad u otro</option>
                                    <option value="otro"           {{ old('tipo_violencia') == 'otro'           ? 'selected' : '' }}>Otro tipo de violencia</option>
                                </select>
                                @error('tipo_violencia')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                <template x-if="tipoViolencia === 'sexual' || tipoViolencia === 'fisica'">
                                    <div class="mt-2 rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                        <span>Este tipo de reporte tiene <strong>prioridad alta</strong> y será atendido dentro de las próximas 4–24 horas.</span>
                                    </div>
                                </template>
                            </div>

                            <div>
                                <label for="fecha_incidente" class="block text-sm font-medium text-gray-700 mb-1">Fecha aproximada del incidente</label>
                                <input type="date" id="fecha_incidente" name="fecha_incidente"
                                       value="{{ old('fecha_incidente') }}" max="{{ date('Y-m-d') }}"
                                       class="field-input @error('fecha_incidente') has-error @enderror">
                                @error('fecha_incidente')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción del incidente <span class="text-red-500">*</span></label>
                                <textarea id="descripcion" name="descripcion" rows="6" required
                                          x-model="descripcion"
                                          placeholder="Describe qué sucedió, cuándo, dónde y quiénes estuvieron involucrados. Cuanta más información proporciones, mejor podrá atenderse el caso."
                                          class="field-input resize-none @error('descripcion') has-error @enderror">{{ old('descripcion') }}</textarea>
                                <div class="mt-1 flex items-center justify-between">
                                    @error('descripcion')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @else
                                        <p class="text-xs text-gray-400">Mínimo 10 caracteres.</p>
                                    @enderror
                                    <p class="text-xs font-medium ml-auto"
                                       :class="descripcion.length > 2800 ? 'text-red-600' : descripcion.length > 2400 ? 'text-amber-600' : 'text-gray-400'"
                                       x-text="`${descripcion.length} / 3000`"></p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- SUBMIT --}}
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <button type="submit" :disabled="enviando"
                                    class="w-full sm:w-auto flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 disabled:bg-red-300 disabled:cursor-not-allowed text-white font-semibold py-3 px-8 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
                                <span x-show="enviando" class="spinner"></span>
                                <span x-show="!enviando">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                </span>
                                <span x-text="enviando ? 'Enviando reporte...' : 'Enviar Reporte Seguro'"></span>
                            </button>
                            <a href="{{ route('alumno.mis-casos') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Ver mis reportes anteriores →</a>
                        </div>
                        <p class="mt-3 text-xs text-gray-400">Al enviar, aceptas que la información será tratada con confidencialidad y usada únicamente para la atención del caso.</p>
                    </div>

                </form>
            </div>
        </div>

        {{-- MODAL: Buscador de escuelas --}}
        <div x-show="mostrarModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6"
             @keydown.escape.window="cerrarBuscador()">
            <div @click.outside="cerrarBuscador()" class="w-full max-w-4xl rounded-2xl bg-white shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 shrink-0">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">Buscar escuela activa</h4>
                        <p class="text-sm text-gray-500 mt-0.5" x-text="ubicacionTexto || 'Selecciona la ubicación para buscar'"></p>
                    </div>
                    <button type="button" @click="cerrarBuscador()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4 overflow-y-auto flex-1">
                    <div class="flex gap-3">
                        <input type="text" x-model="terminoBusqueda" @keydown.enter.prevent="buscarEscuelas"
                               placeholder="Busca por nombre o código modular..." x-ref="inputBusqueda"
                               class="field-input flex-1">
                        <button type="button" @click="buscarEscuelas" :disabled="cargandoEscuelas"
                                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-300 text-white font-semibold px-5 py-2.5 rounded-xl transition whitespace-nowrap text-sm">
                            <span x-show="cargandoEscuelas" class="spinner"></span>
                            <span x-text="cargandoEscuelas ? 'Buscando...' : 'Buscar'"></span>
                        </button>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs text-amber-800">
                        Usa el padrón oficial del MINEDU (colegios activos). Puedes filtrar por nombre o código modular.
                    </div>
                    <template x-if="errorEscuelas">
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="errorEscuelas"></div>
                    </template>
                    <template x-if="mensajeBusqueda && !errorEscuelas && !cargandoEscuelas">
                        <div class="text-sm text-gray-500" x-text="mensajeBusqueda"></div>
                    </template>
                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <template x-if="cargandoEscuelas">
                            <div class="px-4 py-10 text-center text-sm text-gray-500 flex flex-col items-center gap-3">
                                <div class="w-8 h-8 border-2 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                                <span>Consultando el padrón oficial del MINEDU...</span>
                            </div>
                        </template>
                        <template x-if="!cargandoEscuelas && escuelas.length">
                            <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                                <template x-for="escuela in escuelas" :key="`${escuela.codigo_modular}-${escuela.anexo}`">
                                    <button type="button" @click="seleccionarEscuela(escuela)"
                                            class="w-full text-left px-4 py-3.5 hover:bg-indigo-50 transition group">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-gray-800 group-hover:text-indigo-700 text-sm" x-text="escuela.nombre"></p>
                                                <p class="text-xs text-gray-500 mt-0.5" x-text="`${escuela.region} / ${escuela.provincia} / ${escuela.distrito}`"></p>
                                                <p class="text-xs text-gray-400 truncate" x-text="escuela.direccion || 'Sin dirección registrada'"></p>
                                                <span class="inline-block mt-1 text-xs bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded font-medium" x-text="escuela.nivel_modalidad"></span>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <p class="text-sm font-mono text-indigo-700 font-semibold" x-text="escuela.codigo_modular"></p>
                                                <p class="text-xs text-gray-400" x-text="`Anexo ${escuela.anexo}`"></p>
                                            </div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template x-if="!cargandoEscuelas && !escuelas.length && !errorEscuelas">
                            <div class="px-4 py-10 text-center text-sm text-gray-500">No se encontraron colegios activos con esos filtros.</div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function denunciaEscuela() {
            return {
                endpoints: {
                    regiones:   @js(route('catalogos.regiones')),
                    provincias: @js(route('catalogos.provincias')),
                    distritos:  @js(route('catalogos.distritos')),
                    escuelas:   @js(route('catalogos.escuelas')),
                },
                regiones: [], provincias: [], distritos: [], escuelas: [],
                cargandoRegiones: false, cargandoProvincias: false,
                cargandoDistritos: false, cargandoEscuelas: false,
                mostrarModal: false,
                terminoBusqueda: '', mensajeBusqueda: '', errorEscuelas: '',
                regionCodigo: '', provinciaCodigo: '', distritoCodigo: '',
                regionNombre:    @js(old('region', '')),
                provinciaNombre: @js(old('provincia', '')),
                distritoNombre:  @js(old('distrito', '')),
                escuelaNombre:   @js(old('escuela_nombre', '')),
                codigoModular:   @js(old('codigo_modular', '')),
                escuelaNivel: '', escuelaDireccion: '',
                esAnonimo:     @js((bool) old('es_anonimo', true)),
                tipoViolencia: @js(old('tipo_violencia', '')),
                descripcion:   @js(old('descripcion', '')),
                enviando: false,

                get ubicacionTexto() {
                    return [this.regionNombre, this.provinciaNombre, this.distritoNombre].filter(Boolean).join(' / ');
                },

                async init() { await this.cargarRegiones(); },

                async fetchJson(url) {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) throw new Error('Error en la consulta.');
                    return await res.json();
                },

                limpiarEscuela() { this.escuelaNombre = ''; this.codigoModular = ''; this.escuelaNivel = ''; this.escuelaDireccion = ''; },

                selectedName(list, code) { const f = list.find(i => i.codigo === code); return f ? f.nombre : ''; },

                async cargarRegiones() {
                    this.cargandoRegiones = true;
                    try { this.regiones = await this.fetchJson(this.endpoints.regiones); } catch {} finally { this.cargandoRegiones = false; }
                },

                async onRegionChange() {
                    this.regionNombre = this.selectedName(this.regiones, this.regionCodigo);
                    this.provinciaCodigo = ''; this.provinciaNombre = '';
                    this.distritoCodigo  = ''; this.distritoNombre  = '';
                    this.provincias = []; this.distritos = []; this.escuelas = [];
                    this.mensajeBusqueda = ''; this.errorEscuelas = '';
                    this.limpiarEscuela();
                    if (!this.regionCodigo) return;
                    this.cargandoProvincias = true;
                    try { this.provincias = await this.fetchJson(`${this.endpoints.provincias}?region=${encodeURIComponent(this.regionCodigo)}`); }
                    catch {} finally { this.cargandoProvincias = false; }
                },

                async onProvinciaChange() {
                    this.provinciaNombre = this.selectedName(this.provincias, this.provinciaCodigo);
                    this.distritoCodigo  = ''; this.distritoNombre  = '';
                    this.distritos = []; this.escuelas = [];
                    this.mensajeBusqueda = ''; this.errorEscuelas = '';
                    this.limpiarEscuela();
                    if (!this.provinciaCodigo) return;
                    this.cargandoDistritos = true;
                    try { this.distritos = await this.fetchJson(`${this.endpoints.distritos}?provincia=${encodeURIComponent(this.provinciaCodigo)}`); }
                    catch {} finally { this.cargandoDistritos = false; }
                },

                onDistritoChange() {
                    this.distritoNombre = this.selectedName(this.distritos, this.distritoCodigo);
                    this.escuelas = []; this.mensajeBusqueda = ''; this.errorEscuelas = '';
                    this.limpiarEscuela();
                },

                abrirBuscador() {
                    if (!this.distritoCodigo) return;
                    this.mostrarModal = true;
                    this.terminoBusqueda = ''; this.escuelas = [];
                    this.errorEscuelas = ''; this.mensajeBusqueda = 'Cargando colegios activos...';
                    this.buscarEscuelas();
                    this.$nextTick(() => this.$refs.inputBusqueda?.focus());
                },

                cerrarBuscador() { this.mostrarModal = false; },

                async buscarEscuelas() {
                    this.cargandoEscuelas = true; this.errorEscuelas = ''; this.mensajeBusqueda = '';
                    try {
                        const p = new URLSearchParams({ ubigeo: this.distritoCodigo, region: this.regionCodigo });
                        if (this.terminoBusqueda.trim()) p.set('q', this.terminoBusqueda.trim());
                        const r = await this.fetchJson(`${this.endpoints.escuelas}?${p}`);
                        this.escuelas = r.data || [];
                        this.mensajeBusqueda = this.escuelas.length
                            ? `${r.meta?.count || this.escuelas.length} colegio(s) activo(s) encontrado(s).`
                            : 'No se encontraron colegios activos con esos filtros.';
                    } catch {
                        this.escuelas = [];
                        this.errorEscuelas = 'No se pudo consultar el padrón oficial del MINEDU. Intenta nuevamente.';
                    } finally { this.cargandoEscuelas = false; }
                },

                seleccionarEscuela(escuela) {
                    this.escuelaNombre    = escuela.nombre;
                    this.codigoModular    = escuela.codigo_modular;
                    this.escuelaNivel     = escuela.nivel_modalidad || '';
                    this.escuelaDireccion = escuela.direccion || '';
                    if (escuela.region)    this.regionNombre   = escuela.region;
                    if (escuela.provincia) this.provinciaNombre = escuela.provincia;
                    if (escuela.distrito)  this.distritoNombre  = escuela.distrito;
                    this.mostrarModal = false;
                },

                enviarFormulario() {
                    if (!this.escuelaNombre) {
                        alert('Por favor selecciona una escuela antes de enviar el reporte.');
                        return;
                    }
                    this.enviando = true;
                    this.$refs.form.submit();
                },
            };
        }
    </script>
</x-app-layout>
