<?php

namespace App\Filament\Pages;

use App\Models\Asignacion;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Asignaciones extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Panel de Asignaciones';

    protected static ?string $navigationLabel = 'Asignaciones';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.asignaciones';

    public function crearAsignacion(): void
    {
        $data = $this->form->getState();

        Asignacion::create([
            'psicologo_id' => $data['psicologo_id'],
            'paciente_id' => $data['paciente_id'],
            'caso_id' => $data['caso_id'],
            'notas' => $data['notas'],
            'fecha_inicio' => $data['fecha_inicio'],
            'frecuencia_atencion' => $data['frecuencia_atencion'],
            'dia_atencion' => $data['dia_atencion'],
            'hora_atencion' => $data['hora_atencion'],
            'estado' => 'activa',
            'created_by' => auth()->id(),
        ]);

        $this->resetForm();
        Notification::make()
            ->success()
            ->title('Asignacion creada exitosamente.')
            ->send();
    }

    public function resetForm(): void
    {
        $this->psicologo_id = null;
        $this->paciente_id = null;
        $this->caso_id = null;
        $this->notas = null;
        $this->fecha_inicio = today()->toDateString();
        $this->frecuencia_atencion = 'semanal';
        $this->dia_atencion = 'lunes';
        $this->hora_atencion = '09:00';
    }

    public function getPsicologos(): Collection
    {
        return User::where('rol', 'psicologo')
            ->orderBy('name')
            ->get();
    }

    public function getPacientesSinAsignar(): Collection
    {
        $pacientesConAsignacion = Asignacion::where('estado', 'activa')
            ->pluck('paciente_id');

        return User::where('rol', 'alumno')
            ->whereNotIn('id', $pacientesConAsignacion)
            ->orderBy('name')
            ->get();
    }

    public function getAsignacionesActivas(): Collection
    {
        return Asignacion::with(['psicologo', 'paciente', 'caso'])
            ->where('estado', 'activa')
            ->orderByDesc('created_at')
            ->get();
    }

    protected function getTableQuery(): Builder
    {
        return Asignacion::with(['psicologo', 'paciente', 'creador']);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('paciente.name')
                ->label('Paciente')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('psicologo.name')
                ->label('Psicólogo')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('caso.codigo_caso')
                ->label('Caso')
                ->placeholder('Sin caso'),
            Tables\Columns\TextColumn::make('fecha_inicio')
                ->label('Fecha Inicio')
                ->date('d/m/Y'),
            Tables\Columns\TextColumn::make('frecuencia_atencion')
                ->label('Frecuencia')
                ->badge()
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'semanal' => 'Semanal',
                    'quincenal' => 'Quincenal',
                    'mensual' => 'Mensual',
                    default => $state,
                }),
            Tables\Columns\BadgeColumn::make('estado')
                ->colors([
                    'success' => 'activa',
                    'warning' => 'finalizada',
                    'danger' => 'cancelada',
                ])
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'activa' => 'Activa',
                    'finalizada' => 'Finalizada',
                    'cancelada' => 'Cancelada',
                    default => $state,
                }),
            Tables\Columns\TextColumn::make('creador.name')
                ->label('Creado por')
                ->toggleable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ];
    }
}
