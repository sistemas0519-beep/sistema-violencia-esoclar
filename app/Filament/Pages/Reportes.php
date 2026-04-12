<?php

namespace App\Filament\Pages;

use App\Models\Caso;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Reportes extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title           = 'Reportes y Estadísticas';
    protected static ?int    $navigationSort  = 3;

    protected static string $view = 'filament.pages.reportes';

    // ─── Filtros del formulario ────────────────────────────────────────────────
    public ?string $fecha_inicio  = null;
    public ?string $fecha_fin     = null;
    public ?string $tipo_violencia = null;
    public ?string $estado        = null;

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
                        'fisica'         => 'Física',
                        'psicologica'    => 'Psicológica',
                        'verbal'         => 'Verbal',
                        'sexual'         => 'Sexual',
                        'ciberacoso'     => 'Ciberacoso',
                        'discriminacion' => 'Discriminación',
                        'otro'           => 'Otro',
                    ])
                    ->placeholder('Todos')
                    ->nullable(),

                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente'  => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'resuelto'   => 'Resuelto',
                        'cerrado'    => 'Cerrado',
                    ])
                    ->placeholder('Todos')
                    ->nullable(),
            ])
            ->columns(4)
            ->statePath('');
    }

    // ─── Query base con filtros ────────────────────────────────────────────────
    protected function baseQuery()
    {
        $q = Caso::query();

        if ($this->fecha_inicio) {
            $q->whereDate('created_at', '>=', $this->fecha_inicio);
        }
        if ($this->fecha_fin) {
            $q->whereDate('created_at', '<=', $this->fecha_fin);
        }
        if ($this->tipo_violencia) {
            $q->where('tipo_violencia', $this->tipo_violencia);
        }
        if ($this->estado) {
            $q->where('estado', $this->estado);
        }

        return $q;
    }

    // ─── Datos computados para la vista ───────────────────────────────────────
    public function getResumenProperty(): array
    {
        $q = $this->baseQuery();

        return [
            'total'      => (clone $q)->count(),
            'pendiente'  => (clone $q)->where('estado', 'pendiente')->count(),
            'en_proceso' => (clone $q)->where('estado', 'en_proceso')->count(),
            'resuelto'   => (clone $q)->where('estado', 'resuelto')->count(),
            'cerrado'    => (clone $q)->where('estado', 'cerrado')->count(),
            'anonimos'   => (clone $q)->where('es_anonimo', true)->count(),
        ];
    }

    public function getPorTipoProperty(): array
    {
        return $this->baseQuery()
            ->select('tipo_violencia', DB::raw('count(*) as total'))
            ->groupBy('tipo_violencia')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'tipo'  => $r->tipo_violencia,
                'total' => $r->total,
            ])
            ->toArray();
    }

    public function getPorMesProperty(): array
    {
        return $this->baseQuery()
            ->select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('año', 'mes')
            ->orderBy('año')->orderBy('mes')
            ->get()
            ->map(fn ($r) => [
                'mes'   => Carbon::createFromDate($r->año, $r->mes, 1)->translatedFormat('M Y'),
                'total' => $r->total,
            ])
            ->toArray();
    }

    public function getUltimosCasosProperty()
    {
        return $this->baseQuery()
            ->with(['denunciante:id,name', 'asignado:id,name'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    // ─── Exportar a CSV ───────────────────────────────────────────────────────
    public function exportarCsv(): StreamedResponse
    {
        $casos = $this->baseQuery()
            ->with(['denunciante:id,name', 'asignado:id,name'])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'reporte-casos-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($casos) {
            $handle = fopen('php://output', 'w');

            // BOM para Excel (UTF-8)
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Código', 'Tipo de Violencia', 'Estado', 'Anónimo',
                'Denunciante', 'Asignado a', 'Fecha Incidente', 'Fecha Registro',
            ]);

            $tipoLabel = [
                'fisica' => 'Física', 'psicologica' => 'Psicológica', 'verbal' => 'Verbal',
                'sexual' => 'Sexual', 'ciberacoso' => 'Ciberacoso',
                'discriminacion' => 'Discriminación', 'otro' => 'Otro',
            ];
            $estadoLabel = [
                'pendiente' => 'Pendiente', 'en_proceso' => 'En Proceso',
                'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado',
            ];

            foreach ($casos as $c) {
                fputcsv($handle, [
                    $c->codigo_caso,
                    $tipoLabel[$c->tipo_violencia] ?? $c->tipo_violencia,
                    $estadoLabel[$c->estado]        ?? $c->estado,
                    $c->es_anonimo ? 'Sí' : 'No',
                    $c->es_anonimo ? 'Anónimo' : ($c->denunciante?->name ?? '—'),
                    $c->asignado?->name ?? 'Sin asignar',
                    $c->fecha_incidente?->format('d/m/Y') ?? '—',
                    $c->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
