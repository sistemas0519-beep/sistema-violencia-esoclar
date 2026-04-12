<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reportar Incidente
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-8">

                {{-- Éxito --}}
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-300 text-green-800 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                {{-- Errores de validación --}}
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-300 text-red-800 rounded-xl p-4">
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h3 class="text-xl font-bold text-gray-800 mb-1">Formulario de Reporte Seguro</h3>
                <p class="text-sm text-gray-500 mb-6">Tu información está protegida. Puedes enviar el reporte de forma anónima si lo prefieres.</p>

                <form action="{{ route('denuncia.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Anonimato --}}
                    <div>
                        <label for="es_anonimo" class="block text-sm font-medium text-gray-700 mb-1">
                            ¿Deseas que tu reporte sea anónimo?
                        </label>
                        <select id="es_anonimo" name="es_anonimo"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="1">Sí, quiero anonimato</option>
                            <option value="0">No, incluir mis datos</option>
                        </select>
                    </div>

                    {{-- Tipo de violencia --}}
                    <div>
                        <label for="tipo_violencia" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Violencia <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_violencia" name="tipo_violencia" required
                                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('tipo_violencia') border-red-400 @enderror">
                            <option value="">— Selecciona una opción —</option>
                            <option value="fisica"         {{ old('tipo_violencia') == 'fisica'         ? 'selected' : '' }}>Física</option>
                            <option value="psicologica"    {{ old('tipo_violencia') == 'psicologica'    ? 'selected' : '' }}>Psicológica</option>
                            <option value="verbal"         {{ old('tipo_violencia') == 'verbal'         ? 'selected' : '' }}>Verbal</option>
                            <option value="sexual"         {{ old('tipo_violencia') == 'sexual'         ? 'selected' : '' }}>Sexual</option>
                            <option value="ciberacoso"     {{ old('tipo_violencia') == 'ciberacoso'     ? 'selected' : '' }}>Ciberacoso / Cyberbullying</option>
                            <option value="discriminacion" {{ old('tipo_violencia') == 'discriminacion' ? 'selected' : '' }}>Discriminación</option>
                            <option value="otro"           {{ old('tipo_violencia') == 'otro'           ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                            Descripción del incidente <span class="text-red-500">*</span>
                        </label>
                        <textarea id="descripcion" name="descripcion" rows="5" required
                                  class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('descripcion') border-red-400 @enderror"
                                  placeholder="Describe qué sucedió, cuándo, dónde y quiénes estuvieron involucrados...">{{ old('descripcion') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Mínimo 10 caracteres.</p>
                    </div>

                    {{-- Fecha del incidente --}}
                    <div>
                        <label for="fecha_incidente" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha aproximada del incidente
                        </label>
                        <input type="date" id="fecha_incidente" name="fecha_incidente"
                               value="{{ old('fecha_incidente') }}"
                               max="{{ date('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    {{-- Botón enviar --}}
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-red-400">
                            Enviar Reporte Seguro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>