<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Caso;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Administrador ─────────────────────────────────────────────────
        User::firstOrCreate(['email' => 'admin@escuela.edu'], [
            'name'     => 'Administrador',
            'password' => Hash::make('password'),
            'rol'      => 'admin',
        ]);

        // ─── Psicólogo ─────────────────────────────────────────────────────
        $psicologo = User::firstOrCreate(['email' => 'psicologo@escuela.edu'], [
            'name'     => 'Dra. María López',
            'password' => Hash::make('password'),
            'rol'      => 'psicologo',
        ]);

        // ─── Docente ───────────────────────────────────────────────────────
        User::firstOrCreate(['email' => 'docente@escuela.edu'], [
            'name'     => 'Prof. Carlos Romero',
            'password' => Hash::make('password'),
            'rol'      => 'docente',
        ]);

        // ─── Alumno ────────────────────────────────────────────────────────
        $alumno = User::firstOrCreate(['email' => 'alumno@escuela.edu'], [
            'name'     => 'Juan Pérez',
            'password' => Hash::make('password'),
            'rol'      => 'alumno',
        ]);

        // ─── Caso de prueba ────────────────────────────────────────────────
        Caso::firstOrCreate(['codigo_caso' => 'VIO-2026-DEMO1'], [
            'tipo_violencia' => 'verbal',
            'descripcion'    => 'Un compañero me insulta constantemente durante el recreo y me llama por apodos ofensivos frente a otros estudiantes.',
            'estado'         => 'pendiente',
            'es_anonimo'     => false,
            'denunciante_id' => $alumno->id,
            'asignado_a'     => null,
            'fecha_incidente'=> now()->subDays(5),
        ]);

        Caso::firstOrCreate(['codigo_caso' => 'VIO-2026-DEMO2'], [
            'tipo_violencia' => 'ciberacoso',
            'descripcion'    => 'Recibo mensajes amenazantes a través de redes sociales de personas que dicen ser compañeros del colegio.',
            'estado'         => 'en_proceso',
            'es_anonimo'     => true,
            'denunciante_id' => null,
            'asignado_a'     => $psicologo->id,
            'fecha_incidente'=> now()->subDays(10),
        ]);
    }
}
