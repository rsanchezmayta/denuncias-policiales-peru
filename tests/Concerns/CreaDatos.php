<?php

namespace Tests\Concerns;

use App\Models\Departamento;
use App\Models\Distrito;
use App\Models\Provincia;

trait CreaDatos
{
    protected function crearUbicacion(
        string $dpto = 'LIMA',
        string $prov = 'LIMA',
        string $dist = 'MIRAFLORES',
        string $ubigeo = '150122',
    ): Distrito {
        $departamento = Departamento::create(['nombre' => $dpto]);
        $provincia = Provincia::create(['departamento_id' => $departamento->id, 'nombre' => $prov]);

        return Distrito::create([
            'provincia_id' => $provincia->id,
            'ubigeo' => $ubigeo,
            'nombre' => $dist,
        ]);
    }
}
