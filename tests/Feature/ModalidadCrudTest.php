<?php

namespace Tests\Feature;

use App\Models\Denuncia;
use App\Models\Modalidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreaDatos;
use Tests\TestCase;

class ModalidadCrudTest extends TestCase
{
    use CreaDatos, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_lista_modalidades(): void
    {
        Modalidad::create(['nombre' => 'ROBO']);

        $this->get(route('modalidades.index'))->assertOk()->assertSee('ROBO');
    }

    public function test_crea_modalidad(): void
    {
        $this->post(route('modalidades.store'), ['nombre' => 'EXTORSION'])
            ->assertRedirect(route('modalidades.index'));

        $this->assertDatabaseHas('modalidades', ['nombre' => 'EXTORSION']);
    }

    public function test_nombre_duplicado_falla_la_validacion(): void
    {
        Modalidad::create(['nombre' => 'ROBO']);

        $this->from(route('modalidades.create'))
            ->post(route('modalidades.store'), ['nombre' => 'ROBO'])
            ->assertSessionHasErrors('nombre');
    }

    public function test_actualiza_modalidad(): void
    {
        $modalidad = Modalidad::create(['nombre' => 'ROBO']);

        $this->put(route('modalidades.update', $modalidad), ['nombre' => 'HURTO'])
            ->assertRedirect(route('modalidades.index'));

        $this->assertDatabaseHas('modalidades', ['id' => $modalidad->id, 'nombre' => 'HURTO']);
    }

    public function test_no_elimina_modalidad_con_denuncias(): void
    {
        $modalidad = Modalidad::create(['nombre' => 'ROBO']);
        $distrito = $this->crearUbicacion();

        Denuncia::create([
            'anio' => 2025, 'mes' => 1,
            'distrito_id' => $distrito->id, 'modalidad_id' => $modalidad->id, 'cantidad' => 3,
        ]);

        $this->delete(route('modalidades.destroy', $modalidad))->assertSessionHas('error');
        $this->assertDatabaseHas('modalidades', ['id' => $modalidad->id]);
    }

    public function test_elimina_modalidad_sin_denuncias(): void
    {
        $modalidad = Modalidad::create(['nombre' => 'ROBO']);

        $this->delete(route('modalidades.destroy', $modalidad))->assertSessionHas('status');
        $this->assertDatabaseMissing('modalidades', ['id' => $modalidad->id]);
    }
}
