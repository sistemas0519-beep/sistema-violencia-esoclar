<?php

namespace Tests\Feature;

use App\Models\Caso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de integración para el flujo de reportar incidentes.
 *
 * Cubre:
 *   - Creación exitosa de denuncia autenticada
 *   - Denuncia anónima sin denunciante_id
 *   - Rate limiting (máx 3 por hora)
 *   - Validación de campos requeridos
 *   - Cálculo de prioridad y SLA
 */
class DenunciaTest extends TestCase
{
    use RefreshDatabase;

    private User $alumno;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alumno = User::factory()->create([
            'rol'    => 'alumno',
            'activo' => true,
        ]);
    }

    public function test_alumno_puede_reportar_incidente(): void
    {
        $response = $this->actingAs($this->alumno)
            ->post(route('denuncia.store'), $this->datosValidos());

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('casos', [
            'tipo_violencia' => 'fisica',
            'estado'         => 'pendiente',
            'denunciante_id' => $this->alumno->id,
            'es_anonimo'     => 0,
        ]);
    }

    public function test_denuncia_anonima_no_guarda_denunciante_id(): void
    {
        $datos = $this->datosValidos(['es_anonimo' => '1']);

        $this->actingAs($this->alumno)
            ->post(route('denuncia.store'), $datos);

        $this->assertDatabaseHas('casos', [
            'es_anonimo'     => 1,
            'denunciante_id' => null,
        ]);
    }

    public function test_denuncia_requiere_tipo_violencia(): void
    {
        $datos = $this->datosValidos();
        unset($datos['tipo_violencia']);

        $response = $this->actingAs($this->alumno)
            ->post(route('denuncia.store'), $datos);

        $response->assertSessionHasErrors('tipo_violencia');
        $this->assertDatabaseCount('casos', 0);
    }

    public function test_denuncia_requiere_descripcion_minima(): void
    {
        $response = $this->actingAs($this->alumno)
            ->post(route('denuncia.store'), $this->datosValidos(['descripcion' => 'corto']));

        $response->assertSessionHasErrors('descripcion');
    }

    public function test_caso_urgente_tiene_prioridad_alta(): void
    {
        $this->actingAs($this->alumno)
            ->post(route('denuncia.store'), $this->datosValidos(['tipo_violencia' => 'sexual']));

        $caso = Caso::first();

        $this->assertEquals('urgente', $caso->prioridad);
        $this->assertNotNull($caso->sla_limite);
    }

    public function test_rate_limit_maximo_tres_reportes_por_hora(): void
    {
        // Enviar 3 denuncias exitosas
        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($this->alumno)
                ->post(route('denuncia.store'), $this->datosValidos());
        }

        // La 4.ª debe fallar por throttle
        $response = $this->actingAs($this->alumno)
            ->post(route('denuncia.store'), $this->datosValidos());

        $response->assertSessionHasErrors('_throttle');
        $this->assertDatabaseCount('casos', 3);
    }

    public function test_usuario_no_autenticado_no_puede_reportar(): void
    {
        $response = $this->post(route('denuncia.store'), $this->datosValidos());

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('casos', 0);
    }

    public function test_codigo_caso_tiene_formato_correcto(): void
    {
        $this->actingAs($this->alumno)
            ->post(route('denuncia.store'), $this->datosValidos());

        $caso = Caso::first();

        $this->assertMatchesRegularExpression('/^VIO-\d{4}-[A-Z0-9]{6}$/', $caso->codigo_caso);
    }

    private function datosValidos(array $override = []): array
    {
        return array_merge([
            'tipo_violencia' => 'fisica',
            'descripcion'    => 'Descripción detallada del incidente ocurrido en el patio de la escuela.',
            'es_anonimo'     => '0',
            'fecha_incidente'=> now()->subDay()->format('Y-m-d'),
            'region'         => 'Lima',
            'provincia'      => 'Lima',
            'distrito'       => 'San Isidro',
            'escuela_nombre' => 'I.E. San Martín',
            'codigo_modular' => '0123456',
        ], $override);
    }
}
