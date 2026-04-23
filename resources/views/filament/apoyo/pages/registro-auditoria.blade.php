<x-filament-panels::page>
    <div class="mb-4">
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
            <div class="flex items-center gap-2">
                <x-heroicon-o-finger-print class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                <p class="text-sm text-amber-700 dark:text-amber-300">
                    Este registro muestra todas sus acciones en el sistema. Toda actividad es monitoreada para cumplir con las normativas de protección de datos.
                </p>
            </div>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
