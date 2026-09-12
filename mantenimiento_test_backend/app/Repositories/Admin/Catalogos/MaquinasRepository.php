<?php

namespace App\Repositories\Admin\Catalogos;

use App\Models\TblMaquinas;
use Illuminate\Support\Facades\DB;

class MaquinasRepository
{
    public function registrarMaquina(array $maquinas)
    {
        $registro = new TblMaquinas();
        $registro->machines        = $maquinas['machines'];
        $registro->id_area         = $maquinas['id_area'];
        $registro->id_cat_machines = $maquinas['id_cat_machines'];
        $registro->brand           = $maquinas['brand'];
        $registro->model           = $maquinas['model'];
        $registro->year            = $maquinas['year'];
        $registro->serial          = $maquinas['serial'];
        $registro->weight          = $maquinas['weight'];
        $registro->voltaje         = $maquinas['voltaje'];
        $registro->amperage        = $maquinas['amperage'];
        $registro->frequency       = $maquinas['frequency'];
        $registro->kva             = $maquinas['kva'];
        $registro->active          = 1;
        $registro->save();

        return $registro->id_machines;
    }

    public function obtenerListaMaquinas()
    {
        $query = TblMaquinas::select(
            'tbl_machines.id_machines',
            'tbl_machines.machines',
            'tbl_machines.id_area',
            'tbl_machines.id_cat_machines',
            'cat_machines.cat_machines',
            'cat_areas.area',
            'tbl_machines.brand',
            'tbl_machines.model',
            'tbl_machines.year',
            'tbl_machines.serial',
            'tbl_machines.weight',
            'tbl_machines.voltaje',
            'tbl_machines.amperage',
            'tbl_machines.frequency',
            'tbl_machines.kva',
            'tbl_machines.active',
            DB::raw("
                CASE
                    WHEN tbl_machines.active = 1 THEN 'Activo'
                    ELSE 'Inactivo'
                END as estado
            ")
        )
            ->join('cat_areas', 'cat_areas.id_area', '=', 'tbl_machines.id_area')
            ->join('cat_machines', 'cat_machines.id_cat_machines', '=', 'tbl_machines.id_cat_machines');

        return $query->get();
    }

    public function obtenerDetalleMaquina($pkMaquina)
{
    return TblMaquinas::select(
            'id_machines',
            'machines',
            'id_area',
            'id_cat_machines',
            'brand',
            'model',
            'year',
            'serial',
            'weight',
            'voltaje',
            'amperage',
            'frequency',
            'kva',
            'active',
        )
        ->where([
            ['id_machines', $pkMaquina]
        ])
        ->firstOrFail();
}

    public function actualizarMaquina($id, $maquina)
    {
        $actualizar = TblMaquinas::findOrFail($id);

        $actualizar->machines        = $maquina['machines'];
        $actualizar->id_area         = $maquina['id_area'];
        $actualizar->id_cat_machines = $maquina['id_cat_machines'];
        $actualizar->brand           = $maquina['brand'];
        $actualizar->model           = $maquina['model'];
        $actualizar->year            = $maquina['year'];
        $actualizar->serial          = $maquina['serial'];
        $actualizar->weight          = $maquina['weight'];
        $actualizar->voltaje         = $maquina['voltaje'];
        $actualizar->amperage        = $maquina['amperage'];
        $actualizar->frequency       = $maquina['frequency'];
        $actualizar->kva             = $maquina['kva'];
        $actualizar->save();
    }

    public function cambiarStatusMaquina($pkMaquina)
    {
        $maquinas         = TblMaquinas::findOrFail($pkMaquina);
        $maquinas->active = $maquinas->active ? 0 : 1;
        $maquinas->save();

        return $maquinas->active;
    }

    public function obtenerMaquinasPorAreaYCategoria($pkArea, $pkCatalogoMaquina)
    {
        return TblMaquinas::select('id_machines', 'machines')
            ->where('id_area', $pkArea)
            ->where('id_cat_machines', $pkCatalogoMaquina)
            ->where('active', 1) 
            ->get();
    }
}
