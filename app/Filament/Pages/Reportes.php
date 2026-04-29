<?php

namespace App\Filament\Pages;

use App\Models\Asignacion;
use App\Models\Caso;
use App\Models\User;
use App\Services\ReportesService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Reportes extends Page implements HasForms
{
    use InteractsWithForms;

    private const RESUMEN_DEFAULTS = [
        'total' => 0, 'pendiente' => 0, 'en_proceso' => 0,
        'resuelto' => 0, 'cerrado' => 0, 'anonimos' => 0,
        'sin_asignar' => 0, 'urgentes' => 0, 'sla_vencido' => 0, 'tasa_resolucion' => 0,
    ];

    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title           = 'Reportes y Estadisticas';
    protected static ?int    $navigationSort  = 3;

    protected static string $view = 'filament.pages.reportes';

    // Filtros activos
    public ?string $fecha_inicio    = null;
    public ?string $fecha_fin       = null;
    public ?string $tipo_violencia  = null;
    public ?string $estado          = null;
    public ?string $prioridad       = null;
    public ?string $grado_grupo     = null;
    public ?string $nivel_severidad = null;
    public ?string $docente_id      = null;
    public ?string $region_filtro   = null;
    public string  $periodo_comparar = 'mes_anterior';

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
                    ->label('Tipo de Violencia')
                    ->options([
                        'fisica' => 'Fisica', 'psicologica' => 'Psicologica', 'verbal' => 'Verbal',
                        'bullying' => 'Bullying', 'cyberbullying' => 'Cyberbullying',
                        'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso',
                        'discriminacion' => 'Discriminacion',
                    ])
                    ->placeholder('Todos')
                    ->nullable(),

                Select::make('estado')
                    ->label('Estado')
                    ->options(['pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso',
                               'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado'])
                    ->placeholder('Todos')
                    ->nullable(),

                Select::make('prioridad')
                    ->label('Prioridad')
                    ->options(['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'urgente' => 'Urgente'])
                    ->placeholder('Todas')
                    ->nullable(),

                Select::make('nivel_severidad')
                    ->label('Severidad')
                    ->options([1 => 'Niv. 1', 2 => 'Niv. 2', 3 => 'Niv. 3', 4 => 'Niv. 4', 5 => 'Niv. 5'])
                    ->placeholder('Todas')
                    ->nullable(),

                TextInput::make('grado_grupo')
                    ->label('Grado/Grupo')
                    ->placeholder('Ej: 3 A'),

                Select::make('region_filtro')
                    ->label('Región')
                    ->options(fn () => Caso::whereNotNull('region')->where('region', '!=', '')->distinct()->pluck('region', 'region')->map(fn ($v) => ucfirst(strtolower($v)))->toArray())
                    ->placeholder('Todas')
                    ->searchable()
                    ->nullable(),
            ])
            ->columns(3)
            ->statePath('');
    }

    public function limpiarFiltros(): void
    {
        $this->fecha_inicio = $this->fecha_fin = $this->tipo_violencia = null;
        $this->estado = $this->prioridad = $this->nivel_severidad = $this->grado_grupo = null;
        $this->region_filtro = null;
        $this->form->fill();
    }

    public function setPreset(string $preset): void
    {
        $this->limpiarFiltros();
        match ($preset) {
            'hoy'    => $this->fecha_inicio = $this->fecha_fin = today()->toDateString(),
            'semana' => [$this->fecha_inicio = today()->subDays(6)->toDateString(), $this->fecha_fin = today()->toDateString()],
            'mes'    => [$this->fecha_inicio = today()->startOfMonth()->toDateString(), $this->fecha_fin = today()->endOfMonth()->toDateString()],
            'anio'   => [$this->fecha_inicio = today()->startOfYear()->toDateString(), $this->fecha_fin = today()->endOfYear()->toDateString()],
            default  => null,
        };
        $this->form->fill([
            'fecha_inicio'  => $this->fecha_inicio,
            'fecha_fin'     => $this->fecha_fin,
        ]);
    }

    protected function baseQuery()
    {
        $q = Caso::query();
        if ($this->fecha_inicio)   $q->whereDate('created_at', '>=', $this->fecha_inicio);
        if ($this->fecha_fin)      $q->whereDate('created_at', '<=', $this->fecha_fin);
        if ($this->tipo_violencia) $q->where('tipo_violencia', $this->tipo_violencia);
        if ($this->estado)          $q->where('estado', $this->estado);
        if ($this->prioridad)         $q->where('prioridad', $this->prioridad);
        if ($this->nivel_severidad)   $q->where('nivel_severidad', $this->nivel_severidad);
        if ($this->grado_grupo)       $q->where('grado_grupo', 'like', '%' . $this->grado_grupo . '%');
        if ($this->region_filtro)     $q->where('region', $this->region_filtro);
        return $q;
    }

    public function getResumenProperty(): array
    {
        $resumen = ReportesService::getResumenCasos(
            $this->fecha_inicio, $this->fecha_fin, $this->tipo_violencia, $this->estado
        );
        return array_replace(self::RESUMEN_DEFAULTS, is_array($resumen) ? $resumen : []);
    }

    public function getResumenPeriodoAnteriorProperty(): array
    {
        // Calcular el periodo anterior
        $inicio = $this->fecha_inicio ? \Carbon\Carbon::parse($this->fecha_inicio) : now()->subMonth();
        $fin    = $this->fecha_fin ? \Carbon\Carbon::parse($this->fecha_fin) : now();
        $dias   = $inicio->diffInDays($fin);

        $q = Caso::query()
            ->whereDate('created_at', '>=', $inicio->copy()->subDays($dias + 1))
            ->whereDate('created_at', '<=', $inicio->copy()->subDay());

        $total   = $q->count();
        $resuelto = (clone $q)->whereIn('estado', ['resuelto', 'cerrado'])->count();
        return [
            'total'           => $total,
            'tasa_resolucion' => $total > 0 ? round($resuelto / $total * 100, 1) : 0,
        ];
    }

    public function getPorTipoProperty(): array
    {
        return ReportesService::getCasosPorTipo($this->fecha_inicio, $this->fecha_fin);
    }

    public function getPorMesProperty(): array
    {
        return ReportesService::getCasosPorMes($this->fecha_inicio, $this->fecha_fin);
    }

    public function getPorRegionProperty(): array
    {
        return ReportesService::getCasosPorRegion(8);
    }

    public function get14DiasProperty(): array
    {
        return ReportesService::getCasosUltimos14Dias();
    }

    /** Alias para compatibilidad con la vista JS (@js($this->diasData)) */
    public function getDiasDataProperty(): array
    {
        return $this->get14DiasProperty();
    }

    public function getTopEscuelasProperty(): array
    {
        return $this->baseQuery()
            ->select('escuela_nombre', DB::raw('count(*) as total'))
            ->whereNotNull('escuela_nombre')
            ->where('escuela_nombre', '!=', '')
            ->groupBy('escuela_nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($r) => ['escuela' => $r->escuela_nombre, 'total' => $r->total])
            ->toArray();
    }

    public function getTasasResolucionProperty(): array
    {
        return ReportesService::getTasasResolucionPorMes();
    }

    public function getAlertasCriticasProperty(): array
    {
        return [
            'urgentes'   => Caso::where('prioridad', 'urgente')->whereNotIn('estado', ['cerrado'])->count(),
            'sla_vencido' => Caso::where('sla_vencido', true)->whereNotIn('estado', ['cerrado'])->count(),
            'escalados'  => Caso::where('escalado', true)->whereNotIn('estado', ['cerrado'])->count(),
            'sin_atencion_48h' => Caso::where('estado', 'pendiente')
                ->where('created_at', '<=', now()->subHours(48))
                ->count(),
        ];
    }

    public function getDistribucionPrioridadProperty(): array
    {
        $total = $this->baseQuery()->count() ?: 1;
        return $this->baseQuery()
            ->select('prioridad', DB::raw('count(*) as total'))
            ->whereNotNull('prioridad')
            ->groupBy('prioridad')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'prioridad'  => $r->prioridad,
                'total'      => $r->total,
                'porcentaje' => round(($r->total / $total) * 100, 1),
            ])
            ->toArray();
    }

    public function getPorPsicologoProperty(): array
    {
        return collect(ReportesService::getCargaPorPsicologo())
            ->map(function ($row) {
                $row = is_array($row) ? $row : [];
                $activas    = (int) ($row['activas'] ?? $row['asignaciones_activas'] ?? 0);
                $finalizadas = (int) ($row['finalizadas'] ?? 0);
                return [
                    'id'          => $row['id'] ?? null,
                    'nombre'      => (string) ($row['nombre'] ?? 'Sin nombre'),
                    'especialidad'    => $row['especialidad'] ?? null,
                    'disponibilidad'  => $row['disponibilidad'] ?? null,
                    'activas'     => $activas,
                    'finalizadas' => $finalizadas,
                    'total'       => (int) ($row['total'] ?? ($activas + $finalizadas)),
                    'asignaciones_activas' => (int) ($row['asignaciones_activas'] ?? $activas),
                    'casos_activos' => (int) ($row['casos_activos'] ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    public function getPorUbicacionProperty(): array
    {
        return Caso::selectRaw('ubicacion_exacta, COUNT(*) as total')
            ->whereNotNull('ubicacion_exacta')
            ->groupBy('ubicacion_exacta')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => ['ubicacion' => $r->ubicacion_exacta, 'total' => $r->total])
            ->toArray();
    }

    public function getPorSeveridadProperty(): array
    {
        return Caso::selectRaw('nivel_severidad, COUNT(*) as total')
            ->whereNotNull('nivel_severidad')
            ->groupBy('nivel_severidad')
            ->orderBy('nivel_severidad')
            ->get()
            ->map(fn ($r) => ['nivel' => $r->nivel_severidad, 'total' => $r->total])
            ->toArray();
    }

    public function getAsignacionesResumenProperty(): array
    {
        return [
            'total'      => Asignacion::count(),
            'activas'    => Asignacion::where('estado', 'activa')->count(),
            'finalizadas' => Asignacion::where('estado', 'finalizada')->count(),
            'canceladas' => Asignacion::where('estado', 'cancelada')->count(),
        ];
    }

    public function getUltimosCasosProperty()
    {
        return $this->baseQuery()
            ->with(['denunciante:id,name', 'asignado:id,name'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    // ── Labels ─────────────────────────────────────────────────────────────────

    protected array $tipoLabel = [
        'fisica' => 'Fisica', 'psicologica' => 'Psicologica', 'verbal' => 'Verbal',
        'bullying' => 'Bullying', 'cyberbullying' => 'Cyberbullying',
        'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso', 'discriminacion' => 'Discriminacion',
    ];

    protected array $estadoLabel = [
        'pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso',
        'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado',
    ];

    protected function casoRows()
    {
        return $this->baseQuery()
            ->with(['denunciante:id,name', 'asignado:id,name', 'docenteResponsable:id,name'])
            ->orderByDesc('created_at')
            ->get();
    }

    // ── Exportaciones ──────────────────────────────────────────────────────────

    public function exportarCsv(): StreamedResponse
    {
        $casos    = $this->casoRows();
        $filename = 'reporte-casos-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($casos) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, [
                'Codigo', 'Tipo', 'Estado', 'Prioridad', 'Severidad',
                'Anonimo', 'Denunciante', 'Agresor', 'Victima',
                'Grado/Grupo', 'Ubicacion', 'Docente Responsable',
                'Asignado', 'Region', 'Escuela', 'Incidente', 'Registro',
            ]);
            foreach ($casos as $c) {
                fputcsv($handle, [
                    $c->codigo_caso,
                    $this->tipoLabel[$c->tipo_violencia] ?? $c->tipo_violencia,
                    $this->estadoLabel[$c->estado] ?? $c->estado,
                    ucfirst($c->prioridad ?? ''),
                    $c->nivel_severidad ?? '',
                    $c->es_anonimo ? 'Si' : 'No',
                    $c->es_anonimo ? 'Anonimo' : ($c->denunciante?->name ?? '-'),
                    $c->agresor_nombre ?? '-',
                    $c->victima_nombre ?? '-',
                    $c->grado_grupo ?? '-',
                    $c->ubicacion_exacta ?? '-',
                    $c->docenteResponsable?->name ?? '-',
                    $c->asignado?->name ?? 'Sin asignar',
                    $c->region ?? '-',
                    $c->escuela_nombre ?? '-',
                    $c->fecha_incidente?->format('d/m/Y') ?? '-',
                    $c->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportarExcel(): StreamedResponse
    {
        $casos    = $this->casoRows();
        $filename = 'reporte-casos-' . now()->format('Y-m-d') . '.xls';

        return response()->streamDownload(function () use ($casos) {
            $cols = [
                'Código', 'Tipo', 'Estado', 'Prioridad', 'Anónimo',
                'Denunciante', 'Asignado', 'Región', 'Provincia', 'Escuela',
                'Fecha Incidente', 'SLA Vencido', 'Escalado', 'Fecha Registro',
            ];

            echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
            echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . PHP_EOL;
            echo '<Styles>';
            echo '<Style ss:ID="h"><Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="11"/>';
            echo '<Interior ss:Color="#4F46E5" ss:Pattern="Solid"/></Style>';
            echo '<Style ss:ID="alt"><Interior ss:Color="#F5F3FF" ss:Pattern="Solid"/></Style>';
            echo '</Styles>' . PHP_EOL;
            echo '<Worksheet ss:Name="Casos"><Table>' . PHP_EOL;

            // Header
            echo '<Row ss:Height="22">';
            foreach ($cols as $col) {
                echo '<Cell ss:StyleID="h"><Data ss:Type="String">' . htmlspecialchars($col, ENT_XML1) . '</Data></Cell>';
            }
            echo '</Row>' . PHP_EOL;

            // Data rows
            foreach ($casos as $i => $c) {
                $styleId = ($i % 2 === 1) ? ' ss:StyleID="alt"' : '';
                $row = [
                    $c->codigo_caso,
                    $this->tipoLabel[$c->tipo_violencia]  ?? $c->tipo_violencia,
                    $this->estadoLabel[$c->estado]        ?? $c->estado,
                    ucfirst($c->prioridad                 ?? 'normal'),
                    $c->es_anonimo ? 'Sí' : 'No',
                    $c->es_anonimo ? 'Anónimo' : ($c->denunciante?->name ?? '—'),
                    $c->asignado?->name  ?? 'Sin asignar',
                    $c->region           ?? '—',
                    $c->provincia        ?? '—',
                    $c->escuela_nombre   ?? '—',
                    $c->fecha_incidente?->format('d/m/Y') ?? '—',
                    $c->sla_vencido ? 'Sí' : 'No',
                    $c->escalado    ? 'Sí' : 'No',
                    $c->created_at->format('d/m/Y H:i'),
                ];
                echo '<Row>';
                foreach ($row as $cell) {
                    echo '<Cell' . $styleId . '><Data ss:Type="String">' . htmlspecialchars((string) $cell, ENT_XML1) . '</Data></Cell>';
                }
                echo '</Row>' . PHP_EOL;
            }

            echo '</Table></Worksheet></Workbook>';
        }, $filename, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportarPdf(): StreamedResponse
    {
        $resumen  = $this->resumen;
        $porTipo  = $this->porTipo;
        $casos    = $this->casoRows();
        $filename = 'reporte-casos-' . now()->format('Y-m-d') . '.html';

        return response()->streamDownload(function () use ($resumen, $porTipo, $casos) {
            echo view('filament.pages.reportes-pdf', compact('resumen', 'porTipo', 'casos'))->render();
        }, $filename, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}
