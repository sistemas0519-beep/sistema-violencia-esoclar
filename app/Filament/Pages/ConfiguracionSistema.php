<?php

namespace App\Filament\Pages;

use App\Models\AuditLog;
use App\Models\Configuracion;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ConfiguracionSistema extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Configuración';
    protected static ?string $title           = 'Configuración del Sistema';
    protected static ?int    $navigationSort  = 5;

    protected static string $view = 'filament.pages.configuracion-sistema';

    public string $grupoActivo = 'general';

    // Propiedades dinámicas para cada configuración
    public array $valores = [];

    public function mount(): void
    {
        $this->cargarValores();
    }

    protected function cargarValores(): void
    {
        $configuraciones = Configuracion::all();
        foreach ($configuraciones as $config) {
            $this->valores[$config->clave] = $config->valor;
        }
    }

    public function cambiarGrupo(string $grupo): void
    {
        $this->grupoActivo = $grupo;
    }

    public function getConfiguracionesProperty(): array
    {
        return Configuracion::where('grupo', $this->grupoActivo)
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    public function getGruposProperty(): array
    {
        return [
            'general'         => ['label' => 'General',         'icon' => 'heroicon-o-cog-6-tooth',       'desc' => 'Configuración general del sistema'],
            'notificaciones'  => ['label' => 'Notificaciones',  'icon' => 'heroicon-o-bell',              'desc' => 'Configuración de alertas y notificaciones'],
            'seguridad'       => ['label' => 'Seguridad',       'icon' => 'heroicon-o-shield-check',      'desc' => 'Políticas de seguridad y acceso'],
            'casos'           => ['label' => 'Casos',           'icon' => 'heroicon-o-clipboard-document', 'desc' => 'Configuración de gestión de casos'],
        ];
    }

    public function guardarConfiguracion(): void
    {
        $cambios = [];

        foreach ($this->valores as $clave => $valor) {
            $config = Configuracion::where('clave', $clave)->first();
            if ($config && $config->valor !== (string) $valor) {
                $anterior = $config->valor;
                Configuracion::establecer($clave, $valor);
                $cambios[] = "{$config->etiqueta}: {$anterior} → {$valor}";
            }
        }

        if (!empty($cambios)) {
            AuditLog::registrar(
                'editar',
                'configuracion',
                'Actualizó configuración del sistema: ' . implode(', ', array_slice($cambios, 0, 3)),
                null,
                null,
                ['cambios' => $cambios],
            );

            Notification::make()
                ->title('Configuración guardada')
                ->body(count($cambios) . ' parámetro(s) actualizado(s)')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Sin cambios')
                ->body('No se detectaron modificaciones')
                ->info()
                ->send();
        }

        $this->cargarValores();
    }
}
