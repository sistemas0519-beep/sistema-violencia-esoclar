<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FullDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ──────────────────────────────────────────────────────────────────────
        // 1. USUARIOS
        // ──────────────────────────────────────────────────────────────────────
        $admin = \App\Models\User::firstOrCreate(['email' => 'admin@escuela.edu'], [
            'name'     => 'Administrador Principal',
            'password' => Hash::make('password'),
            'rol'      => 'admin',
        ]);

        $psicologa1 = \App\Models\User::firstOrCreate(['email' => 'psicologo@escuela.edu'], [
            'name'         => 'Dra. María López',
            'password'     => Hash::make('password'),
            'rol'          => 'psicologo',
            'especialidad' => 'Psicología Clínica Infantil',
            'disponibilidad' => 'disponible',
            'max_pacientes'  => 15,
        ]);

        $psicologo2 = \App\Models\User::firstOrCreate(['email' => 'psicologo2@escuela.edu'], [
            'name'         => 'Lic. Roberto Sánchez',
            'password'     => Hash::make('password'),
            'rol'          => 'psicologo',
            'especialidad' => 'Psicología Educativa',
            'disponibilidad' => 'disponible',
            'max_pacientes'  => 12,
        ]);

        $docente1 = \App\Models\User::firstOrCreate(['email' => 'docente@escuela.edu'], [
            'name'     => 'Prof. Carlos Romero',
            'password' => Hash::make('password'),
            'rol'      => 'docente',
        ]);

        $docente2 = \App\Models\User::firstOrCreate(['email' => 'docente2@escuela.edu'], [
            'name'     => 'Prof. Ana González',
            'password' => Hash::make('password'),
            'rol'      => 'docente',
        ]);

        $alumno1 = \App\Models\User::firstOrCreate(['email' => 'alumno@escuela.edu'], [
            'name'     => 'Juan Pérez García',
            'password' => Hash::make('password'),
            'rol'      => 'alumno',
        ]);

        $alumno2 = \App\Models\User::firstOrCreate(['email' => 'alumno2@escuela.edu'], [
            'name'     => 'Sofía Torres Ríos',
            'password' => Hash::make('password'),
            'rol'      => 'alumno',
        ]);

        $alumno3 = \App\Models\User::firstOrCreate(['email' => 'alumno3@escuela.edu'], [
            'name'     => 'Miguel Flores Díaz',
            'password' => Hash::make('password'),
            'rol'      => 'alumno',
        ]);

        $alumno4 = \App\Models\User::firstOrCreate(['email' => 'alumno4@escuela.edu'], [
            'name'     => 'Lucía Mendoza Vega',
            'password' => Hash::make('password'),
            'rol'      => 'alumno',
        ]);

        $alumno5 = \App\Models\User::firstOrCreate(['email' => 'alumno5@escuela.edu'], [
            'name'     => 'Diego Castillo Mora',
            'password' => Hash::make('password'),
            'rol'      => 'alumno',
        ]);

        // ──────────────────────────────────────────────────────────────────────
        // 2. CASOS
        // ──────────────────────────────────────────────────────────────────────
        $caso1 = \App\Models\Caso::firstOrCreate(['codigo_caso' => 'VIO-2026-0001'], [
            'tipo_violencia'    => 'verbal',
            'descripcion'       => 'Un compañero insulta constantemente a la víctima durante el recreo y la llama por apodos ofensivos frente a otros estudiantes.',
            'estado'            => 'en_proceso',
            'prioridad'         => 'alta',
            'es_anonimo'        => false,
            'denunciante_id'    => $alumno1->id,
            'asignado_a'        => $psicologa1->id,
            'fecha_incidente'   => now()->subDays(15),
            'region'            => 'Lima',
            'provincia'         => 'Lima',
            'distrito'          => 'San Borja',
            'escuela_nombre'    => 'I.E. Nuestra Señora de Guadalupe',
            'codigo_modular'    => '0302368',
            'es_sensible'       => false,
            'nivel_urgencia'    => 'inmediata',
            'area_tematica'     => 'acoso_escolar',
        ]);

        $caso2 = \App\Models\Caso::firstOrCreate(['codigo_caso' => 'VIO-2026-0002'], [
            'tipo_violencia'    => 'ciberacoso',
            'descripcion'       => 'La estudiante recibe mensajes amenazantes a través de redes sociales de personas que dicen ser compañeros del colegio.',
            'estado'            => 'en_proceso',
            'prioridad'         => 'alta',
            'es_anonimo'        => true,
            'denunciante_id'    => null,
            'asignado_a'        => $psicologa1->id,
            'fecha_incidente'   => now()->subDays(20),
            'region'            => 'Lima',
            'provincia'         => 'Lima',
            'distrito'          => 'Miraflores',
            'escuela_nombre'    => 'I.E. Rosa de Santa María',
            'codigo_modular'    => '0302376',
            'es_sensible'       => true,
            'nivel_sensibilidad' => 'sensible',
            'nivel_urgencia'    => 'inmediata',
            'area_tematica'     => 'ciberacoso',
        ]);

        $caso3 = \App\Models\Caso::firstOrCreate(['codigo_caso' => 'VIO-2026-0003'], [
            'tipo_violencia'    => 'fisica',
            'descripcion'       => 'El estudiante reporta que un grupo de compañeros lo golpea en el camino a casa. Presenta marcas visibles.',
            'estado'            => 'pendiente',
            'prioridad'         => 'urgente',
            'es_anonimo'        => false,
            'denunciante_id'    => $docente1->id,
            'asignado_a'        => null,
            'fecha_incidente'   => now()->subDays(3),
            'region'            => 'Lima',
            'provincia'         => 'Lima',
            'distrito'          => 'Ate',
            'escuela_nombre'    => 'I.E. San Marcos',
            'codigo_modular'    => '0302384',
            'es_sensible'       => true,
            'nivel_sensibilidad' => 'altamente_confidencial',
            'nivel_urgencia'    => 'inmediata',
            'area_tematica'     => 'violencia_fisica',
        ]);

        $caso4 = \App\Models\Caso::firstOrCreate(['codigo_caso' => 'VIO-2026-0004'], [
            'tipo_violencia'    => 'psicologica',
            'descripcion'       => 'La estudiante presenta síntomas de ansiedad severa después de ser excluida sistemáticamente por su grupo de compañeras.',
            'estado'            => 'en_proceso',
            'prioridad'         => 'media',
            'es_anonimo'        => false,
            'denunciante_id'    => $alumno2->id,
            'asignado_a'        => $psicologo2->id,
            'fecha_incidente'   => now()->subDays(30),
            'region'            => 'Lima',
            'provincia'         => 'Lima',
            'distrito'          => 'La Molina',
            'escuela_nombre'    => 'I.E. María Auxiliadora',
            'codigo_modular'    => '0302392',
            'es_sensible'       => false,
            'nivel_urgencia'    => 'media',
            'area_tematica'     => 'otro',
        ]);

        $caso5 = \App\Models\Caso::firstOrCreate(['codigo_caso' => 'VIO-2026-0005'], [
            'tipo_violencia'    => 'discriminacion',
            'descripcion'       => 'Estudiante reporta discriminación por orientación sexual por parte de docentes y compañeros.',
            'estado'            => 'resuelto',
            'prioridad'         => 'alta',
            'es_anonimo'        => true,
            'denunciante_id'    => null,
            'asignado_a'        => $psicologa1->id,
            'fecha_incidente'   => now()->subDays(60),
            'region'            => 'Lima',
            'provincia'         => 'Lima',
            'distrito'          => 'Surco',
            'escuela_nombre'    => 'I.E. Inmaculada',
            'codigo_modular'    => '0302400',
            'es_sensible'       => true,
            'nivel_sensibilidad' => 'sensible',
            'nivel_urgencia'    => 'inmediata',
            'area_tematica'     => 'discriminacion',
        ]);

        $caso6 = \App\Models\Caso::firstOrCreate(['codigo_caso' => 'VIO-2026-0006'], [
            'tipo_violencia'    => 'verbal',
            'descripcion'       => 'Docente reporta que un estudiante usa lenguaje ofensivo y amenazante con sus compañeros de manera reiterada.',
            'estado'            => 'pendiente',
            'prioridad'         => 'baja',
            'es_anonimo'        => false,
            'denunciante_id'    => $docente2->id,
            'asignado_a'        => null,
            'fecha_incidente'   => now()->subDays(5),
            'region'            => 'Lima',
            'provincia'         => 'Lima',
            'distrito'          => 'San Isidro',
            'escuela_nombre'    => 'I.E. La Salle',
            'codigo_modular'    => '0302418',
            'es_sensible'       => false,
            'nivel_urgencia'    => 'baja',
            'area_tematica'     => 'acoso_escolar',
        ]);

        // ──────────────────────────────────────────────────────────────────────
        // 3. ASIGNACIONES
        // ──────────────────────────────────────────────────────────────────────
        $asig1 = \App\Models\Asignacion::firstOrCreate(
            ['caso_id' => $caso1->id, 'psicologo_id' => $psicologa1->id],
            [
                'paciente_id'          => $alumno1->id,
                'notas'                => 'Caso de acoso verbal. Requiere atención psicológica inmediata.',
                'fecha_inicio'         => now()->subDays(14)->toDateString(),
                'frecuencia_atencion'  => 'semanal',
                'dia_atencion'         => 'lunes',
                'hora_atencion'        => '10:00:00',
                'estado'               => 'activa',
                'created_by'           => $admin->id,
            ]
        );

        $asig2 = \App\Models\Asignacion::firstOrCreate(
            ['caso_id' => $caso2->id, 'psicologo_id' => $psicologa1->id],
            [
                'paciente_id'          => $alumno2->id,
                'notas'                => 'Caso de ciberacoso con impacto emocional severo.',
                'fecha_inicio'         => now()->subDays(18)->toDateString(),
                'frecuencia_atencion'  => 'semanal',
                'dia_atencion'         => 'miercoles',
                'hora_atencion'        => '14:00:00',
                'estado'               => 'activa',
                'created_by'           => $admin->id,
            ]
        );

        $asig3 = \App\Models\Asignacion::firstOrCreate(
            ['caso_id' => $caso4->id, 'psicologo_id' => $psicologo2->id],
            [
                'paciente_id'          => $alumno4->id,
                'notas'                => 'Exclusión social con síntomas de ansiedad. Terapia cognitivo-conductual recomendada.',
                'fecha_inicio'         => now()->subDays(25)->toDateString(),
                'frecuencia_atencion'  => 'semanal',
                'dia_atencion'         => 'jueves',
                'hora_atencion'        => '11:00:00',
                'estado'               => 'activa',
                'created_by'           => $admin->id,
            ]
        );

        $asig4 = \App\Models\Asignacion::firstOrCreate(
            ['caso_id' => $caso5->id, 'psicologo_id' => $psicologa1->id],
            [
                'paciente_id'          => $alumno5->id,
                'notas'                => 'Caso cerrado. Estudiante mejoró significativamente.',
                'fecha_inicio'         => now()->subDays(58)->toDateString(),
                'fecha_fin'            => now()->subDays(10)->toDateString(),
                'frecuencia_atencion'  => 'semanal',
                'dia_atencion'         => 'viernes',
                'hora_atencion'        => '09:00:00',
                'estado'               => 'finalizada',
                'motivo_fin'           => 'Objetivos terapéuticos alcanzados.',
                'created_by'           => $admin->id,
            ]
        );

        // ──────────────────────────────────────────────────────────────────────
        // 4. SESIONES
        // ──────────────────────────────────────────────────────────────────────
        $sesion1 = \App\Models\Sesion::firstOrCreate(
            ['caso_id' => $caso1->id, 'fecha' => now()->subDays(14)->toDateString(), 'profesional_id' => $psicologa1->id],
            [
                'paciente_id'     => $alumno1->id,
                'asignacion_id'   => $asig1->id,
                'tipo_sesion'     => 'evaluacion_inicial',
                'modalidad'       => 'presencial',
                'estado'          => 'completada',
                'hora_inicio'     => '10:00:00',
                'hora_fin'        => '11:00:00',
                'lugar'           => 'Oficina Psicología',
                'objetivo'        => 'Evaluación inicial y establecimiento de rapport.',
                'resumen_sesion'  => 'Se realizó evaluación inicial. El estudiante muestra afectación emocional moderada. Se estableció alianza terapéutica básica.',
                'duracion_real_minutos' => 55,
            ]
        );

        $sesion2 = \App\Models\Sesion::firstOrCreate(
            ['caso_id' => $caso1->id, 'fecha' => now()->subDays(7)->toDateString(), 'profesional_id' => $psicologa1->id],
            [
                'paciente_id'     => $alumno1->id,
                'asignacion_id'   => $asig1->id,
                'tipo_sesion'     => 'intervencion',
                'modalidad'       => 'presencial',
                'estado'          => 'completada',
                'hora_inicio'     => '10:00:00',
                'hora_fin'        => '11:00:00',
                'lugar'           => 'Oficina Psicología',
                'objetivo'        => 'Trabajar estrategias de afrontamiento ante el acoso.',
                'resumen_sesion'  => 'Se trabajaron técnicas de asertividad. El estudiante participó activamente y mostró progreso.',
                'duracion_real_minutos' => 60,
            ]
        );

        $sesion3 = \App\Models\Sesion::firstOrCreate(
            ['caso_id' => $caso1->id, 'fecha' => now()->addDays(1)->toDateString(), 'profesional_id' => $psicologa1->id],
            [
                'paciente_id'       => $alumno1->id,
                'asignacion_id'     => $asig1->id,
                'tipo_sesion'       => 'intervencion',
                'modalidad'         => 'presencial',
                'estado'            => 'programada',
                'hora_inicio'       => '10:00:00',
                'hora_fin'          => '11:00:00',
                'lugar'             => 'Oficina Psicología',
                'objetivo'          => 'Evaluación de progreso y técnicas de manejo emocional.',
                'notas_preparacion' => 'Revisar avances de semana anterior. Introducir técnicas de relajación.',
            ]
        );

        $sesion4 = \App\Models\Sesion::firstOrCreate(
            ['caso_id' => $caso4->id, 'fecha' => now()->subDays(20)->toDateString(), 'profesional_id' => $psicologo2->id],
            [
                'paciente_id'     => $alumno4->id,
                'asignacion_id'   => $asig3->id,
                'tipo_sesion'     => 'evaluacion_inicial',
                'modalidad'       => 'presencial',
                'estado'          => 'completada',
                'hora_inicio'     => '11:00:00',
                'hora_fin'        => '12:00:00',
                'lugar'           => 'Sala de Orientación',
                'objetivo'        => 'Evaluación inicial del caso de exclusión social.',
                'resumen_sesion'  => 'Estudiante muy reservada. Se identificaron patrones de pensamiento negativos. Plan de intervención diseñado.',
                'duracion_real_minutos' => 65,
            ]
        );

        $sesion5 = \App\Models\Sesion::firstOrCreate(
            ['caso_id' => $caso4->id, 'fecha' => now()->subDays(13)->toDateString(), 'profesional_id' => $psicologo2->id],
            [
                'paciente_id'     => $alumno4->id,
                'asignacion_id'   => $asig3->id,
                'tipo_sesion'     => 'intervencion',
                'modalidad'       => 'presencial',
                'estado'          => 'completada',
                'hora_inicio'     => '11:00:00',
                'hora_fin'        => '12:00:00',
                'lugar'           => 'Sala de Orientación',
                'objetivo'        => 'Trabajo en autoestima y habilidades sociales.',
                'resumen_sesion'  => 'Se trabajaron técnicas de reestructuración cognitiva. Buena recepción de la estudiante.',
                'duracion_real_minutos' => 58,
            ]
        );

        // ──────────────────────────────────────────────────────────────────────
        // 5. SEGUIMIENTOS
        // ──────────────────────────────────────────────────────────────────────
        \App\Models\Seguimiento::firstOrCreate(
            ['caso_id' => $caso1->id, 'fecha_seguimiento' => now()->subDays(13)],
            [
                'responsable_id'    => $psicologa1->id,
                'accion'            => 'intervencion',
                'notas'             => 'Primera sesión completada. Se asignó psicóloga al caso. El alumno se mostró cooperativo. Se coordinó con docente tutor para observación en aula.'
            ]
        );

        \App\Models\Seguimiento::firstOrCreate(
            ['caso_id' => $caso1->id, 'fecha_seguimiento' => now()->subDays(6)],
            [
                'responsable_id'    => $psicologa1->id,
                'accion'            => 'reunion',
                'notas'             => 'Segunda sesión realizada. Coordinación con dirección. Se reportó mejora en el estado emocional. Dirección fue informada para tomar medidas con el agresor.'
            ]
        );

        \App\Models\Seguimiento::firstOrCreate(
            ['caso_id' => $caso2->id, 'fecha_seguimiento' => now()->subDays(17)],
            [
                'responsable_id'    => $psicologa1->id,
                'accion'            => 'reunion',
                'notas'             => 'Evaluación inicial realizada. Coordinación con padres. Se contactó con los padres para informar sobre el caso. Se solicitó apoyo familiar.'
            ]
        );

        \App\Models\Seguimiento::firstOrCreate(
            ['caso_id' => $caso3->id, 'fecha_seguimiento' => now()->subDays(2)],
            [
                'responsable_id'    => $admin->id,
                'accion'            => 'otro',
                'notas'             => 'Caso recibido. Pendiente de asignación urgente. Caso de violencia física activo. Requiere atención inmediata. Se notificó a dirección y padres.'
            ]
        );

        \App\Models\Seguimiento::firstOrCreate(
            ['caso_id' => $caso4->id, 'fecha_seguimiento' => now()->subDays(19)],
            [
                'responsable_id'    => $psicologo2->id,
                'accion'            => 'intervencion',
                'notas'             => 'Inicio de intervención psicológica. Se coordinó con docentes para observación del grupo. Plan de mediación grupal en preparación.'
            ]
        );

        \App\Models\Seguimiento::firstOrCreate(
            ['caso_id' => $caso5->id, 'fecha_seguimiento' => now()->subDays(55)],
            [
                'responsable_id'    => $psicologa1->id,
                'accion'            => 'reunion',
                'notas'             => 'Inicio de intervención. Reunión con dirección. Se implementó protocolo de no discriminación. Caso derivado a consejo escolar.'
            ]
        );

        \App\Models\Seguimiento::firstOrCreate(
            ['caso_id' => $caso5->id, 'fecha_seguimiento' => now()->subDays(20)],
            [
                'responsable_id'    => $psicologa1->id,
                'accion'            => 'cierre',
                'notas'             => 'Cierre del caso con resultados positivos. La situación mejoró significativamente. Se implementaron talleres de inclusión. Caso resuelto.',
            ]
        );

        // ──────────────────────────────────────────────────────────────────────
        // 6. INTERVENCIONES
        // ──────────────────────────────────────────────────────────────────────
        \App\Models\Intervencion::firstOrCreate(
            ['codigo' => 'INT-2026-0001'],
            [
                'caso_id'               => $caso1->id,
                'profesional_id'        => $psicologa1->id,
                'sesion_id'             => $sesion1->id,
                'tipo_intervencion'     => 'acompanamiento',
                'estado'                => 'completada',
                'descripcion'           => 'Intervención psicológica individual para manejo del acoso verbal.',
                'acciones_realizadas'   => 'Evaluación psicológica inicial, técnicas de asertividad, desarrollo de habilidades sociales.',
                'resultados_observados' => 'El estudiante muestra mayor confianza y capacidad de respuesta asertiva.',
                'recomendaciones'       => 'Continuar con sesiones semanales. Incorporar actividades grupales.',
                'efectividad'           => 'efectiva',
                'fecha_inicio'          => now()->subDays(14)->toDateString(),
                'fecha_fin'             => now()->subDays(7)->toDateString(),
                'requiere_seguimiento'  => true,
                'proximo_seguimiento'   => now()->addDays(7)->toDateString(),
            ]
        );

        \App\Models\Intervencion::firstOrCreate(
            ['codigo' => 'INT-2026-0002'],
            [
                'caso_id'               => $caso2->id,
                'profesional_id'        => $psicologa1->id,
                'tipo_intervencion'     => 'acompanamiento',
                'estado'                => 'en_curso',
                'descripcion'           => 'Intervención por ciberacoso con componente de trauma.',
                'acciones_realizadas'   => 'Evaluación del impacto emocional. Técnicas de regulación emocional. Coordinación con padres.',
                'resultados_observados' => 'Identificados patrones de respuesta al estrés. Se trabajó en estrategias de afrontamiento.',
                'recomendaciones'       => 'Apoyo familiar activo. Restricción temporal de redes sociales.',
                'efectividad'           => 'parcial',
                'fecha_inicio'          => now()->subDays(18)->toDateString(),
                'requiere_seguimiento'  => true,
                'proximo_seguimiento'   => now()->addDays(3)->toDateString(),
            ]
        );

        \App\Models\Intervencion::firstOrCreate(
            ['codigo' => 'INT-2026-0003'],
            [
                'caso_id'               => $caso4->id,
                'profesional_id'        => $psicologo2->id,
                'sesion_id'             => $sesion4->id,
                'tipo_intervencion'     => 'acompanamiento',
                'estado'                => 'en_curso',
                'descripcion'           => 'Terapia cognitivo-conductual para manejo de ansiedad social.',
                'acciones_realizadas'   => 'Reestructuración cognitiva, entrenamiento en habilidades sociales, exposición gradual.',
                'resultados_observados' => 'Reducción moderada de síntomas de ansiedad. Mejora en participación grupal.',
                'recomendaciones'       => 'Continuar con terapia. Involucrar a tutores para apoyo en aula.',
                'efectividad'           => 'efectiva',
                'fecha_inicio'          => now()->subDays(20)->toDateString(),
                'requiere_seguimiento'  => true,
                'proximo_seguimiento'   => now()->addDays(5)->toDateString(),
            ]
        );

        \App\Models\Intervencion::firstOrCreate(
            ['codigo' => 'INT-2026-0004'],
            [
                'caso_id'               => $caso5->id,
                'profesional_id'        => $psicologa1->id,
                'tipo_intervencion'     => 'mediacion',
                'estado'                => 'completada',
                'descripcion'           => 'Intervención de mediación y trabajó en inclusión escolar.',
                'acciones_realizadas'   => 'Mediación grupal, talleres de diversidad e inclusión, sensibilización docente.',
                'resultados_observados' => 'Mejora significativa en el clima escolar. El estudiante fue reintegrado al grupo.',
                'recomendaciones'       => 'Seguimiento mensual. Mantener los talleres de inclusión.',
                'efectividad'           => 'muy_efectiva',
                'fecha_inicio'          => now()->subDays(55)->toDateString(),
                'fecha_fin'             => now()->subDays(10)->toDateString(),
                'requiere_seguimiento'  => false,
            ]
        );

        // ──────────────────────────────────────────────────────────────────────
        // 7. RECURSOS DE APOYO
        // ──────────────────────────────────────────────────────────────────────
        $recursos = [
            [
                'creado_por' => $psicologa1->id,
                'titulo'     => 'Protocolo de Actuación ante el Bullying Escolar',
                'contenido'  => 'Guía completa para docentes y psicólogos sobre cómo identificar, reportar y actuar ante casos de acoso escolar. Incluye formularios de registro y rutas de atención.',
                'categoria'  => 'protocolo',
                'etiquetas'  => json_encode(['bullying', 'protocolo', 'docentes', 'acción']),
                'es_publico' => true,
                'destacado'  => true,
                'visitas'    => 145,
            ],
            [
                'creado_por' => $psicologo2->id,
                'titulo'     => 'Técnicas de Relajación para Adolescentes',
                'contenido'  => 'Conjunto de técnicas de respiración y relajación adaptadas para adolescentes en situación de estrés o ansiedad. Incluye ejercicios prácticos y materiales descargables.',
                'categoria'  => 'guia_intervencion',
                'etiquetas'  => json_encode(['relajación', 'adolescentes', 'ansiedad', 'técnicas']),
                'es_publico' => true,
                'destacado'  => false,
                'visitas'    => 89,
            ],
            [
                'creado_por' => $psicologa1->id,
                'titulo'     => 'Guía para Padres: Señales de Alerta de Ciberacoso',
                'contenido'  => 'Información práctica para que los padres puedan identificar señales de ciberacoso en sus hijos y cómo actuar. Incluye consejos sobre supervisión digital responsable.',
                'categoria'  => 'guia_intervencion',
                'etiquetas'  => json_encode(['ciberacoso', 'padres', 'señales', 'internet']),
                'es_publico' => true,
                'destacado'  => true,
                'visitas'    => 212,
            ],
            [
                'creado_por' => $admin->id,
                'titulo'     => 'Directorio de Servicios de Apoyo Psicológico Lima',
                'contenido'  => 'Listado actualizado de servicios de atención psicológica gratuita o de bajo costo en Lima Metropolitana. Incluye hospitales, ONGs y centros comunitarios.',
                'categoria'  => 'recurso_externo',
                'etiquetas'  => json_encode(['directorio', 'Lima', 'apoyo', 'servicios']),
                'es_publico' => true,
                'destacado'  => false,
                'visitas'    => 67,
            ],
            [
                'creado_por' => $psicologo2->id,
                'titulo'     => 'Manual de Habilidades Sociales para Estudiantes',
                'contenido'  => 'Manual interactivo para el desarrollo de habilidades sociales en adolescentes: comunicación asertiva, resolución de conflictos y trabajo en equipo.',
                'categoria'  => 'material_psicoeducativo',
                'etiquetas'  => json_encode(['habilidades sociales', 'asertividad', 'conflictos', 'manual']),
                'es_publico' => true,
                'destacado'  => true,
                'visitas'    => 183,
            ],
            [
                'creado_por' => $psicologa1->id,
                'titulo'     => 'Ficha de Evaluación Psicológica Inicial - CONFIDENCIAL',
                'contenido'  => 'Formulario estructurado para la evaluación psicológica inicial de estudiantes afectados por violencia escolar. USO EXCLUSIVO DE PSICÓLOGOS.',
                'categoria'  => 'formato',
                'etiquetas'  => json_encode(['evaluación', 'formulario', 'confidencial', 'psicólogos']),
                'es_publico' => false,
                'destacado'  => false,
                'visitas'    => 34,
            ],
        ];

        foreach ($recursos as $recurso) {
            \App\Models\RecursoApoyo::firstOrCreate(
                ['titulo' => $recurso['titulo']],
                $recurso
            );
        }

        // ──────────────────────────────────────────────────────────────────────
        // 8. NOTAS CONFIDENCIALES
        // ──────────────────────────────────────────────────────────────────────
        \App\Models\NotaConfidencial::firstOrCreate(
            ['caso_id' => $caso1->id, 'autor_id' => $psicologa1->id],
            [
                'contenido'   => 'Nota interna: El alumno reveló situación familiar difícil que puede estar contribuyendo a su vulnerabilidad. Se requiere coordinación con servicio social. No compartir con docentes sin autorización.',
                'visibilidad' => 'psicologos',
                'es_critica'  => false,
            ]
        );

        \App\Models\NotaConfidencial::firstOrCreate(
            ['caso_id' => $caso2->id, 'autor_id' => $psicologa1->id],
            [
                'contenido'   => 'La estudiante menciona que los mensajes provienen de una cuenta falsa posiblemente de un excompañero. Familia está coordinando con la policía. Mantener en reserva.',
                'visibilidad' => 'psicologos',
                'es_critica'  => true,
            ]
        );

        \App\Models\NotaConfidencial::firstOrCreate(
            ['caso_id' => $caso3->id, 'autor_id' => $admin->id],
            [
                'contenido'   => 'Caso de violencia física. Se tomaron fotografías de las lesiones como evidencia. Padres notificados. Posible derivación a fiscalía de familia si continúa.',
                'visibilidad' => 'solo_autor',
                'es_critica'  => true,
            ]
        );

        // ──────────────────────────────────────────────────────────────────────
        // 9. ACTIVIDAD DEL SISTEMA (LOG)
        // ──────────────────────────────────────────────────────────────────────
        $actividades = [
            ['usuario_id' => $admin->id,      'accion' => 'login',           'descripcion' => 'Inicio de sesión exitoso'],
            ['usuario_id' => $psicologa1->id, 'accion' => 'crear_caso',      'descripcion' => 'Creó el caso VIO-2026-0001'],
            ['usuario_id' => $psicologa1->id, 'accion' => 'actualizar_caso', 'descripcion' => 'Actualizó estado del caso VIO-2026-0002 a en_proceso'],
            ['usuario_id' => $admin->id,      'accion' => 'asignar_caso',    'descripcion' => 'Asignó caso VIO-2026-0001 a Dra. María López'],
            ['usuario_id' => $psicologo2->id, 'accion' => 'crear_sesion',    'descripcion' => 'Programó sesión para caso VIO-2026-0004'],
            ['usuario_id' => $docente1->id,   'accion' => 'reportar_caso',   'descripcion' => 'Reportó nuevo caso de violencia física VIO-2026-0003'],
            ['usuario_id' => $psicologa1->id, 'accion' => 'completar_sesion','descripcion' => 'Completó sesión de terapia individual caso VIO-2026-0001'],
            ['usuario_id' => $admin->id,      'accion' => 'login',           'descripcion' => 'Inicio de sesión exitoso'],
        ];

        foreach ($actividades as $act) {
            \App\Models\ActividadSistema::create([
                'user_id'    => $act['usuario_id'],
                'tipo'       => $act['accion'],
                'nivel'      => 'info',
                'mensaje'    => $act['descripcion'],
                'ip_address' => '127.0.0.' . rand(1, 10),
                'created_at' => now()->subHours(rand(1, 72)),
                'updated_at' => now()->subHours(rand(1, 72)),
            ]);
        }

        // ──────────────────────────────────────────────────────────────────────
        // 10. SOLICITUDES DE ASESORÍA
        // ──────────────────────────────────────────────────────────────────────
        \App\Models\SolicitudAsesoria::firstOrCreate(
            ['solicitante_id' => $docente1->id, 'caso_id' => $caso1->id],
            [
                'codigo'          => 'SOL-2026-0001',
                'atendido_por'    => $psicologa1->id,
                'tipo'            => 'orientacion',
                'descripcion'     => 'Necesito orientación sobre cómo manejar al agresor en el aula sin generar más conflictos.',
                'estado'          => 'completada',
                'prioridad'       => 'media',
                'motivo'          => 'Orientación pedagógica',
                'observaciones_resolucion' => 'Se recomienda mantener comunicación discreta con el alumno agresor, establecer límites claros y reportar cualquier nueva incidencia.',
                'fecha_solicitud'  => now()->subDays(8),
                'fecha_asignacion' => now()->subDays(8),
                'fecha_resolucion' => now()->subDays(6),
            ]
        );

        \App\Models\SolicitudAsesoria::firstOrCreate(
            ['solicitante_id' => $docente2->id, 'caso_id' => null, 'tipo' => 'capacitacion'],
            [
                'codigo'          => 'SOL-2026-0002',
                'atendido_por'    => $psicologo2->id,
                'tipo'            => 'seguimiento',
                'descripcion'     => '¿Podría brindarme capacitación sobre señales tempranas de acoso entre estudiantes?',
                'estado'          => 'pendiente',
                'prioridad'       => 'baja',
                'motivo'          => 'Capacitación docente',
                'fecha_solicitud'  => now()->subDays(4),
            ]
        );

        // ──────────────────────────────────────────────────────────────────────
        // 11. MENSAJES
        // ──────────────────────────────────────────────────────────────────────
        \App\Models\Mensaje::firstOrCreate(
            ['remitente_id' => $psicologa1->id, 'destinatario_id' => $admin->id, 'asunto' => 'Actualización caso VIO-2026-0001'],
            [
                'caso_id'         => $caso1->id,
                'asunto'          => 'Actualización caso VIO-2026-0001',
                'contenido'       => 'Estimado Administrador, informo que inicié la intervención con el estudiante Juan Pérez. La primera sesión de evaluación fue satisfactoria.',
                'prioridad'       => 'normal',
                'es_confidencial' => false,
                'leido_en'        => now()->subDays(12),
            ]
        );

        \App\Models\Mensaje::firstOrCreate(
            ['remitente_id' => $admin->id, 'destinatario_id' => $psicologa1->id, 'asunto' => 'Re: Actualización caso VIO-2026-0001'],
            [
                'caso_id'         => $caso1->id,
                'asunto'          => 'Re: Actualización caso VIO-2026-0001',
                'contenido'       => 'Gracias por la actualización. Por favor mantener informados a los padres del estudiante sobre el proceso.',
                'prioridad'       => 'normal',
                'es_confidencial' => false,
                'leido_en'        => now()->subDays(11),
            ]
        );

        \App\Models\Mensaje::firstOrCreate(
            ['remitente_id' => $docente1->id, 'destinatario_id' => $psicologa1->id, 'asunto' => 'Observación en aula - Juan'],
            [
                'caso_id'         => $caso1->id,
                'asunto'          => 'Observación en aula - Juan',
                'contenido'       => 'He observado que Juan está más tranquilo esta semana. El compañero agresor fue amonestado por dirección.',
                'prioridad'       => 'normal',
                'es_confidencial' => false,
            ]
        );

        // ──────────────────────────────────────────────────────────────────────
        // AUDIT LOG
        // ──────────────────────────────────────────────────────────────────────
        $auditLogs = [
            ['user_id' => $admin->id,      'action' => 'created', 'model_type' => 'App\\Models\\Caso',     'model_id' => $caso1->id, 'description' => 'Caso VIO-2026-0001 creado'],
            ['user_id' => $admin->id,      'action' => 'created', 'model_type' => 'App\\Models\\Caso',     'model_id' => $caso2->id, 'description' => 'Caso VIO-2026-0002 creado'],
            ['user_id' => $admin->id,      'action' => 'updated', 'model_type' => 'App\\Models\\Caso',     'model_id' => $caso1->id, 'description' => 'Estado cambiado a en_proceso'],
            ['user_id' => $psicologa1->id, 'action' => 'created', 'model_type' => 'App\\Models\\Sesion',   'model_id' => $sesion1->id, 'description' => 'Sesión de evaluación inicial creada'],
            ['user_id' => $psicologo2->id, 'action' => 'created', 'model_type' => 'App\\Models\\Sesion',   'model_id' => $sesion4->id, 'description' => 'Sesión programada para caso VIO-2026-0004'],
        ];

        foreach ($auditLogs as $log) {
            \App\Models\AuditLog::create([
                'user_id'      => $log['user_id'],
                'accion'       => $log['action'],
                'modulo'       => 'casos',
                'descripcion'  => $log['description'],
                'modelo_tipo'  => $log['model_type'],
                'modelo_id'    => $log['model_id'],
                'ip_address'   => '127.0.0.1',
                'created_at'   => now()->subHours(rand(1, 120)),
                'updated_at'   => now()->subHours(rand(1, 120)),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Base de datos completa con todos los datos de prueba.');
    }
}
