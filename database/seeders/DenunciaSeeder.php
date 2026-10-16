<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DenunciaSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $path = env('DENUNCIA_CSV') ?: database_path('data/denuncias_policiales.csv');

        if (! is_file($path)) {
            $muestra = database_path('data/ejemplo_denuncias.csv');

            if (is_file($muestra)) {
                $this->command->warn("No se encontró el CSV completo; usando la muestra: {$muestra}");
                $path = $muestra;
            } else {
                $this->command->error("No se encontró el archivo: {$path}");
                $this->command->line('Descarga el dataset y colócalo en database/data/ (ver README).');

                return;
            }
        }

        $this->limpiarTablas();

        $this->command->info('Pasada 1: leyendo e insertando dimensiones...');
        [$mapDptos, $mapProv, $mapDist, $mapMod] = $this->cargarDimensiones($path);

        $this->command->info('Pasada 2: insertando hechos (denuncias)...');
        $this->cargarDenuncias($path, $mapDist, $mapMod);

        $this->command->info('Importación completa.');
    }

    private function limpiarTablas(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (['denuncias', 'distritos', 'provincias', 'departamentos', 'modalidades'] as $tabla) {
            DB::table($tabla)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function cargarDimensiones(string $path): array
    {
        $handle = fopen($path, 'r');
        fgetcsv($handle, 0, ',', '"'); // descartar encabezado

        $departamentos = [];
        $provincias = [];
        $distritos = [];
        $modalidades = [];

        while (($fila = fgetcsv($handle, 0, ',', '"')) !== false) {
            if (count($fila) < 8) {
                continue;
            }
            [, , $dpto, $prov, $dist, $ubigeo, $modalidad] = $fila;
            $dpto = trim($dpto);
            $prov = trim($prov);
            $dist = trim($dist);
            $ubigeo = str_pad(trim($ubigeo), 6, '0', STR_PAD_LEFT);
            $modalidad = trim($modalidad);

            if ($dpto !== '') {
                $departamentos[$dpto] = true;
            }
            if ($dpto !== '' && $prov !== '') {
                $provincias[$dpto.'|'.$prov] = true;
            }
            if ($dpto !== '' && $prov !== '' && $dist !== '' && $ubigeo !== '') {
                $distritos[$ubigeo] = ['prov' => $dpto.'|'.$prov, 'nombre' => $dist];
            }
            if ($modalidad !== '') {
                $modalidades[$modalidad] = true;
            }
        }
        fclose($handle);

        $now = now();

        $mapDptos = [];
        foreach (array_keys($departamentos) as $nombre) {
            $mapDptos[$nombre] = DB::table('departamentos')->insertGetId([
                'nombre' => $nombre, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $mapProv = [];
        foreach (array_keys($provincias) as $clave) {
            [$dpto, $prov] = explode('|', $clave, 2);
            $mapProv[$clave] = DB::table('provincias')->insertGetId([
                'departamento_id' => $mapDptos[$dpto], 'nombre' => $prov,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $mapDist = [];
        foreach ($distritos as $ubigeo => $info) {
            $mapDist[$ubigeo] = DB::table('distritos')->insertGetId([
                'provincia_id' => $mapProv[$info['prov']], 'ubigeo' => $ubigeo,
                'nombre' => $info['nombre'], 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $mapMod = [];
        foreach (array_keys($modalidades) as $nombre) {
            $mapMod[$nombre] = DB::table('modalidades')->insertGetId([
                'nombre' => $nombre, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $this->command->info(sprintf(
            '  %d departamentos, %d provincias, %d distritos, %d modalidades',
            count($mapDptos), count($mapProv), count($mapDist), count($mapMod)
        ));

        return [$mapDptos, $mapProv, $mapDist, $mapMod];
    }

    private function cargarDenuncias(string $path, array $mapDist, array $mapMod): void
    {
        $handle = fopen($path, 'r');
        fgetcsv($handle, 0, ',', '"');

        // Agrupamos al grano de la tabla (año, mes, distrito, modalidad) y
        // sumamos la cantidad cuando dos filas apuntan al mismo distrito.
        $acumulado = [];

        while (($fila = fgetcsv($handle, 0, ',', '"')) !== false) {
            if (count($fila) < 8) {
                continue;
            }
            [$anio, $mes, , , , $ubigeo, $modalidad, $cantidad] = $fila;
            $ubigeo = str_pad(trim($ubigeo), 6, '0', STR_PAD_LEFT);
            $modalidad = trim($modalidad);

            $distritoId = $mapDist[$ubigeo] ?? null;
            $modalidadId = $mapMod[$modalidad] ?? null;

            if ($distritoId === null || $modalidadId === null) {
                continue;
            }

            $clave = $anio.'|'.$mes.'|'.$distritoId.'|'.$modalidadId;
            $acumulado[$clave] = ($acumulado[$clave] ?? 0) + (int) $cantidad;
        }
        fclose($handle);

        $buffer = [];
        $total = 0;

        foreach ($acumulado as $clave => $cantidad) {
            [$anio, $mes, $distritoId, $modalidadId] = explode('|', $clave);

            $buffer[] = [
                'anio' => (int) $anio,
                'mes' => (int) $mes,
                'distrito_id' => (int) $distritoId,
                'modalidad_id' => (int) $modalidadId,
                'cantidad' => $cantidad,
            ];

            if (count($buffer) >= 2000) {
                DB::table('denuncias')->insert($buffer);
                $total += count($buffer);
                $buffer = [];
            }
        }

        if ($buffer !== []) {
            DB::table('denuncias')->insert($buffer);
            $total += count($buffer);
        }

        $this->command->info("  {$total} filas insertadas en denuncias");
    }
}
