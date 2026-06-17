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
                $registro->kva       = $maquinas['kva'];
                $registro->active    = 1;
                $registro->save();

                return $registro->id_machines;
            }

            public function obtenerListaMaquinas() {
                $query = CatMaquinas::select(
                    'id_machines',
                    'machines',
                    'id_area',
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
                    DB::raw("               CASE 
                                                WHEN active = 1 THEN 'Activo'
                                                ELSE 'Inactivo'
                                                END as estado
                    ")
                );

                return $query->get();
            }

            public function obtenerDetalleMaquina($pkMaquina) {
                $query = CatMaquinas::select(
                    'id_machines',
                    'machines',
                    'id_area',
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
                        ['id_machines', $pkMaquina],
                        ['active', 1]
                    ]);

                    return $query->get();
            }
            
            public function actualizarMaquina($id, $maquina) {
                $actualizar = CatMaquinas::findOrFail($id);

                $actualizar->machines    = $maquina['machines'];
                $actualizar->id_area     = $maquina['id_area'];
                $actualizar->brand       = $maquina['brand'];
                $actualizar->model       = $maquina['model'];
                $actualizar->year        = $maquina['year'];
                $actualizar->serial      = $maquina['serial'];
                $actualizar->weight      = $maquina['weight'];
                $actualizar->voltaje     = $maquina['voltaje'];
                $actualizar->amperage    = $maquina['amperage'];
                $actualizar->frequency   = $maquina['frequency'];
                $actualizar->kva         = $maquina['kva'];
                $actualizar->save();
            }

            public function cambiarStatusMaquina($pkMaquina) {
                $maquinas         = CatMaquinas::findOrFail($pkMaquina);
                $maquinas->active = $maquinas->active ? 0 :1;
                $maquinas->save();

                return $maquinas->active;
            }
        }
    