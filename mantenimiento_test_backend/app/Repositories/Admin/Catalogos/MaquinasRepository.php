<?php

        namespace App\Repositories\Admin\Catalogos;

use App\Models\CatMaquinas;
use Illuminate\Support\Facades\DB;

        class MaquinasRepository
        {
            public function registrarMaquina(array $maquinas) {
                $registro = new CatMaquinas();
                $registro->machines  = $maquinas['machines'];
                $registro->id_area   = $maquinas['id_area'];
                $registro->brand     = $maquinas['brand'];
                $registro->model     = $maquinas['model'];
                $registro->year      = $maquinas['year'];
                $registro->serial    = $maquinas['serial'];
                $registro->weight    = $maquinas['weight'];
                $registro->voltaje   = $maquinas['voltaje'];
                $registro->amperage  = $maquinas['amperage'];
                $registro->frequency = $maquinas['frequency'];
                $registro->kva = $maquinas['kva'];
                $registro->active    = 1;
                $registro->save();

                return $registro->id_machines;
            }
        }
    