<?php

namespace Tests\Feature;

use App\Models\Denuncia;
use App\Models\Modalidad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreaDatos;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use CreaDatos, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $distrito = $this->crearUbicacion();
        $modalidad = Modalidad::create(['nombre' => 'ROBO']);

        Denuncia::create(['anio' => 2025, 'mes' => 3, 'distrito_id' => $distrito->id, 'modalidad_id' => $modalidad->id, 'cantidad' => 10]);
    }

    public function test_endpoint_denuncias_devuelve_estructura(): void
    {
        $this->getJson('/api/denuncias')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'anio', 'mes', 'departamento', 'provincia', 'distrito', 'modalidad', 'cantidad']],
            ]);
    }

    public function test_endpoint_resumen_devuelve_agregados(): void
    {
        $this->getJson('/api/denuncias/resumen')
            ->assertOk()
            ->assertJsonStructure(['total', 'por_anio', 'por_modalidad'])
            ->assertJsonPath('total', 10);
    }

    public function test_endpoint_segmentos_responde(): void
    {
        $this->getJson('/api/segmentos')->assertOk()->assertJsonStructure(['data']);
    }
}
