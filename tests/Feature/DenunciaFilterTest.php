<?php

namespace Tests\Feature;

use App\Models\Denuncia;
use App\Models\Modalidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreaDatos;
use Tests\TestCase;

class DenunciaFilterTest extends TestCase
{
    use CreaDatos, RefreshDatabase;

    private Modalidad $robo;

    private Modalidad $hurto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());

        $distrito = $this->crearUbicacion('LIMA', 'LIMA', 'MIRAFLORES', '150122');
        $this->robo = Modalidad::create(['nombre' => 'ROBO']);
        $this->hurto = Modalidad::create(['nombre' => 'HURTO']);

        Denuncia::create(['anio' => 2025, 'mes' => 3, 'distrito_id' => $distrito->id, 'modalidad_id' => $this->robo->id, 'cantidad' => 10]);
        Denuncia::create(['anio' => 2024, 'mes' => 1, 'distrito_id' => $distrito->id, 'modalidad_id' => $this->hurto->id, 'cantidad' => 5]);
    }

    public function test_scope_sin_filtros_suma_todo(): void
    {
        $this->assertSame(15, (int) Denuncia::query()->filtrar([])->sum('cantidad'));
    }

    public function test_scope_filtra_por_anio(): void
    {
        $this->assertSame(10, (int) Denuncia::query()->filtrar(['anio' => 2025])->sum('cantidad'));
    }

    public function test_scope_filtra_por_modalidad(): void
    {
        $this->assertSame(5, (int) Denuncia::query()->filtrar(['modalidad_id' => $this->hurto->id])->sum('cantidad'));
    }

    public function test_explorador_responde_correctamente(): void
    {
        $this->get(route('denuncias.index'))->assertOk()->assertSee('MIRAFLORES');
    }

    public function test_exportar_csv_devuelve_los_datos(): void
    {
        $respuesta = $this->get(route('denuncias.exportar', ['anio' => 2025]));
        $respuesta->assertOk();

        $contenido = $respuesta->streamedContent();
        $this->assertStringContainsString('MIRAFLORES', $contenido);
        $this->assertStringContainsString('ROBO', $contenido);
        $this->assertStringContainsString('cantidad', $contenido);
    }
}
