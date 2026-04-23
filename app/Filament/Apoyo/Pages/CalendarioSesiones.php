<?php

namespace App\Filament\Apoyo\Pages;

use App\Models\Sesion;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;

class CalendarioSesiones extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Asesoría y Apoyo';
    protected static ?string $navigationLabel = 'Calendario';
    protected static ?string $title = 'Calendario de Sesiones';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.apoyo.pages.calendario-sesiones';

    public int $mes;
    public int $anio;

    public function mount(): void
    {
        $this->mes = now()->month;
        $this->anio = now()->year;
    }

    public function mesAnterior(): void
    {
        $fecha = Carbon::createFromDate($this->anio, $this->mes, 1)->subMonth();
        $this->mes = $fecha->month;
        $this->anio = $fecha->year;
    }

    public function mesSiguiente(): void
    {
        $fecha = Carbon::createFromDate($this->anio, $this->mes, 1)->addMonth();
        $this->mes = $fecha->month;
        $this->anio = $fecha->year;
    }

    public function hoy(): void
    {
        $this->mes = now()->month;
        $this->anio = now()->year;
    }

    public function getSesionesDelMes(): array
    {
        $inicio = Carbon::createFromDate($this->anio, $this->mes, 1)->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        $sesiones = Sesion::where('profesional_id', auth()->id())
            ->whereBetween('fecha', [$inicio, $fin])
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy(fn ($s) => $s->fecha->format('Y-m-d'))
            ->toArray();

        return $sesiones;
    }

    public function getDiasDelMes(): array
    {
        $inicio = Carbon::createFromDate($this->anio, $this->mes, 1);
        $fin = $inicio->copy()->endOfMonth();

        // Ajustar para que empiece en lunes
        $inicioCalendario = $inicio->copy()->startOfWeek(Carbon::MONDAY);
        $finCalendario = $fin->copy()->endOfWeek(Carbon::SUNDAY);

        $dias = [];
        $period = CarbonPeriod::create($inicioCalendario, $finCalendario);

        foreach ($period as $dia) {
            $dias[] = [
                'fecha'     => $dia->format('Y-m-d'),
                'dia'       => $dia->day,
                'esMesActual' => $dia->month === $this->mes,
                'esHoy'     => $dia->isToday(),
            ];
        }

        return $dias;
    }

    public function getNombreMes(): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        return $meses[$this->mes] . ' ' . $this->anio;
    }
}
