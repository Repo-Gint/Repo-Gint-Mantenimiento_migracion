<?php

namespace App\Repositories\Admin\Catalogos;

use App\Models\CatMaquinas;
use Illuminate\Support\Facades\DB;

class MaquinasCatalogoRepository
{
    public function registrarCatalogoMaquina(array $maquinaCatalogo)
    {
        $registro = new CatMaquinas();

        $registro->cat_machines = $maquinaCatalogo['cat_machines'];
        $registro->active    = 1;
        $registro->save();

        return $registro->id_cat_machines;
    }

    public function obtenerListaCatalogoMaquina()
    {
        $query = CatMaquinas::select(
            'id_cat_machines',
            'cat_machines',
            DB::raw("
                        CASE
                            WHEN cat_machines.active = 1 THEN 'Activo'
                            ELSE 'Inactivo'
                        END as estado
                    ")
        );

        return $query->get();
    }

    public function obtenerDetalleCatalogoMaquina(int $pkCatalogoMaquina) {
        $query = CatMaquinas::select(
            'id_cat_machines',
            'cat_machines',
            'active'
        )

        ->where([
            ['id_cat_machines', $pkCatalogoMaquina],
            ['active', 1]
        ]);

        return $query->get();
    }

    public function actualizarCatalogoMaquina(int $id, array $maquinaCatalogo) {
        $actualizar = CatMaquinas::findOrFail($id);

        $actualizar->cat_machines = $maquinaCatalogo['cat_machines'];
        $actualizar->save();
    }
}
