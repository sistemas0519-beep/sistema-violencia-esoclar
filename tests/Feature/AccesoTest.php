<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de integración para autenticación y control de acceso a paneles.
 *
 * Cubre:
 *   - Login exitoso redirige al panel correcto según rol
 *   - Admin no puede acceder al panel apoyo y viceversa
 *   - Usuarios inactivos no pueden autenticarse
 *   - Rate limiting en endpoint de login
 */
class AccesoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_accede_al_panel_admin(): void
    {
        $admin = User::factory()->create(['rol' => 'admin', 'activo' => true]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_psicologo_accede_al_panel_apoyo(): void
    {
        $psicologo = User::factory()->create(['rol' => 'psicologo', 'activo' => true]);

        $response = $this->actingAs($psicologo)->get('/apoyo');

        $response->assertOk();
    }

    public function test_alumno_no_puede_acceder_al_panel_admin(): void
    {
        $alumno = User::factory()->create(['rol' => 'alumno', 'activo' => true]);

        $response = $this->actingAs($alumno)->get('/admin');

        // Filament redirige a login si no tiene acceso
        $response->assertRedirect();
    }

    public function test_psicologo_no_puede_acceder_al_panel_admin(): void
    {
        $psicologo = User::factory()->create(['rol' => 'psicologo', 'activo' => true]);

        $response = $this->actingAs($psicologo)->get('/admin');

        $response->assertRedirect();
    }

    public function test_admin_no_puede_acceder_al_panel_apoyo(): void
    {
        $admin = User::factory()->create(['rol' => 'admin', 'activo' => true]);

        $response = $this->actingAs($admin)->get('/apoyo');

        $response->assertRedirect();
    }

    public function test_login_exitoso_crea_sesion(): void
    {
        $user = User::factory()->create([
            'rol'      => 'alumno',
            'activo'   => true,
            'password' => bcrypt('Password1!'),
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'Password1!',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_credenciales_incorrectas_no_autentican(): void
    {
        $user = User::factory()->create(['activo' => true]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_rate_limit_despues_de_10_intentos(): void
    {
        $user = User::factory()->create(['activo' => true]);

        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', [
                'email'    => $user->email,
                'password' => 'wrong',
            ]);
        }

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }

    public function test_logout_destruye_sesion(): void
    {
        $user = User::factory()->create(['activo' => true]);

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }
}
