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
        $registro->active       = 1;
        $registro->save();

        return $registro->id_cat_machines;
    }

    public function obtenerListaCatalogoMaquina()
    {
        return $this->obtenerListaCatalogoQuery()->get();
    }

    public function obtenerListaCatalogoQuery()
    {
        return CatMaquinas::select(
            'id_cat_machines',
            'cat_machines',
            'active',
            DB::raw("
                CASE
                    WHEN cat_machines.active = 1 THEN 'Activo'
                    ELSE 'Inactivo'
                END as estado
            ")
        );
    }

    public function obtenerDetalleCatalogoMaquina(int $pkCatalogoMaquina) {
        return CatMaquinas::select(
            'id_cat_machines',
            'cat_machines',
            'active'
        )
        ->where('id_cat_machines', $pkCatalogoMaquina)
        ->firstOrFail();
    }

    public function actualizarCatalogoMaquina(int $id, array $maquinaCatalogo) {
        $actualizar = CatMaquinas::findOrFail($id);
        $actualizar->cat_machines = $maquinaCatalogo['cat_machines'];
        $actualizar->save();
    }

    public function cambiarStatusCatalogoMaquina(int $pkCatalogoMaquina) {
        $catalogo = CatMaquinas::findOrFail($pkCatalogoMaquina);
        $catalogo->active = $catalogo->active ? 0 : 1;
        $catalogo->save();

        return $catalogo->active;
    }
}