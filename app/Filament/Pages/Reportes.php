<?php

namespace App\Filament\Pages;

use App\Models\Asignacion;
use App\Models\Caso;
use App\Services\ReportesService;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Reportes extends Page implements HasForms
{
    use InteractsWithForms;

    private const RESUMEN_DEFAULTS = [
        'total' => 0,
        'pendiente' => 0,
        'en_proceso' => 0,
        'resuelto' => 0,
        'cerrado' => 0,
        'anonimos' => 0,
        'sin_asignar' => 0,
        'urgentes' => 0,
        'sla_vencido' => 0,
        'tasa_resolucion' => 0,
    ];

    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Administraci�n';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title           = 'Reportes y Estad�sticas';
    protected static ?int    $navigationSort  = 3;

    protected static string $view = 'filament.pages.reportes';

    public ?string $fecha_inicio   = null;
    public ?string $fecha_fin      = null;
    public ?string $tipo_violencia = null;
    public ?string $estado         = null;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('fecha_inicio')
                    ->label('Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->maxDate(today()),

                DatePicker::make('fecha_fin')
                    ->label('Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->maxDate(today()),

                Select::make('tipo_violencia')
                    ->label('Tipo de violencia')
                    ->options([
                        'fisica' => 'F�sica', 'psicologica' => 'Psicol�gica', 'verbal' => 'Verbal',
                        'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminaci�n',
                    ])
                    ->placeholder('Todos')
                    ->nullable(),

                Select::make('estado')
                    ->label('Estado')
                    ->options(['pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso', 
                               'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado'])
                    ->placeholder('Todos')
                    ->nullable(),
            ])
            ->columns(4)
            ->statePath('');
    }

    public function limpiarFiltros(): void
    {
        $this->fecha_inicio = $this->fecha_fin = $this->tipo_violencia = $this->estado = null;
        $this->form->fill();
    }

    protected function baseQuery()
    {
        $q = Caso::query();
        if ($this->fecha_inicio) $q->whereDate('created_at', '>=', $this->fecha_inicio);
        if ($this->fecha_fin) $q->whereDate('created_at', '<=', $this->fecha_fin);
        if ($this->tipo_violencia) $q->where('tipo_violencia', $this->tipo_violencia);
        if ($this->estado) $q->where('estado', $this->estado);
        return $q;
    }

    public function getResumenProperty(): array
    {
        $resumen = ReportesService::getResumenCasos(
            $this->fecha_inicio, $this->fecha_fin, $this->tipo_violencia, $this->estado
        );

        return array_replace(self::RESUMEN_DEFAULTS, is_array($resumen) ? $resumen : []);
    }

    public function getPorTipoProperty(): array
    {
        return ReportesService::getCasosPorTipo($this->fecha_inicio, $this->fecha_fin);
    }

    public function getPorMesProperty(): array
    {
        return ReportesService::getCasosPorMes($this->fecha_inicio, $this->fecha_fin);
    }

    public function getUltimosCasosProperty()
    {
        return $this->baseQuery()
            ->with(['denunciante:id,name', 'asignado:id,name'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    public function getPorRegionProperty(): array
    {
        return ReportesService::getCasosPorRegion(8);
    }

    public function getPorPsicologoProperty(): array
    {
        return collect(ReportesService::getCargaPorPsicologo())
            ->map(function ($row) {
                $row = is_array($row) ? $row : [];

                $activas = (int) ($row['activas'] ?? $row['asignaciones_activas'] ?? 0);
                $finalizadas = (int) ($row['finalizadas'] ?? 0);

                return [
                    'id' => $row['id'] ?? null,
                    'nombre' => (string) ($row['nombre'] ?? 'Sin nombre'),
                    'especialidad' => $row['especialidad'] ?? null,
                    'disponibilidad' => $row['disponibilidad'] ?? null,
                    'activas' => $activas,
                    'finalizadas' => $finalizadas,
                    'total' => (int) ($row['total'] ?? ($activas + $finalizadas)),
                    'asignaciones_activas' => (int) ($row['asignaciones_activas'] ?? $activas),
                    'casos_activos' => (int) ($row['casos_activos'] ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    public function getAsignacionesResumenProperty(): array
    {
        return [
            'total' => Asignacion::count(),
            'activas' => Asignacion::where('estado', 'activa')->count(),
            'finalizadas' => Asignacion::where('estado', 'finalizada')->count(),
            'canceladas' => Asignacion::where('estado', 'cancelada')->count(),
        ];
    }

    protected array $tipoLabel = [
        'fisica' => 'F�sica', 'psicologica' => 'Psicol�gica', 'verbal' => 'Verbal',
        'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminaci�n',
    ];

    protected array $estadoLabel = [
        'pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso', 
        'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado',
    ];

    protected function casoRows()
    {
        return $this->baseQuery()
            ->with(['denunciante:id,name', 'asignado:id,name'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function exportarCsv(): StreamedResponse
    {
        $casos = $this->casoRows();
        $filename = 'reporte-casos-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($casos) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['C�digo', 'Tipo', 'Estado', 'An�nimo', 'Denunciante', 'Asignado', 'Regi�n', 'Escuela', 'Incidente', 'Registro']);
            foreach ($casos as $c) {
                fputcsv($handle, [
                    $c->codigo_caso,
                    $this->tipoLabel[$c->tipo_violencia] ?? $c->tipo_violencia,
                    $this->estadoLabel[$c->estado] ?? $c->estado,
                    $c->es_anonimo ? 'S�' : 'No',
                    $c->es_anonimo ? 'An�nimo' : ($c->denunciante?->name ?? '�'),
                    $c->asignado?->name ?? 'Sin asignar',
                    $c->region ?? '�',
                    $c->escuela_nombre ?? '�',
                    $c->fecha_incidente?->format('d/m/Y') ?? '�',
                    $c->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportarExcel(): StreamedResponse
    {
        $casos = $this->casoRows();
        $filename = 'reporte-casos-' . now()->format('Y-m-d') . '.xls';
        return response()->streamDownload(function () use ($casos) {
            echo '<?xml version="1.0" encoding="UTF-8"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Worksheet ss:Name="Casos"><Table>';
            echo '<Row><Cell><Data ss:Type="String">C�digo</Data></Cell><Cell><Data ss:Type="String">Tipo</Data></Cell></Row>';
            foreach ($casos as $c) {
                echo '<Row><Cell><Data ss:Type="String">' . htmlspecialchars($c->codigo_caso, ENT_XML1) . '</Data></Cell></Row>';
            }
            echo '</Table></Worksheet></Workbook>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel']);
    }
}
