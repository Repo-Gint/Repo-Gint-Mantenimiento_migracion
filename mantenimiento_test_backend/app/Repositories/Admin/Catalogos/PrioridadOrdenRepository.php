<?php

        namespace App\Repositories\Admin\Catalogos;

use App\Models\CatPrioridadOrden;
use App\Models\CatTipoOrden;
use Illuminate\Support\Facades\DB;

        class PrioridadOrdenRepository
        {
            public function registrarPrioridadOrden(array $prioridadOrden) {
                $registro = new CatTipoOrden();

                $registro->priority     = $prioridadOrden['priority'];
                $registro->description  = $prioridadOrden['description'];
                $registro->color        = $prioridadOrden['color'];
                $registro->active       = 1;
                $registro->save();

                return $registro->id_priority;
            }

            public function obtenerListaPrioridadOrden() {

            $query = CatPrioridadOrden::select(
                'id_priority',
                'priority',
                'description',
                'color',
                DB::raw("
                                        CASE
                                            WHEN active = 1 THEN 'Activo'
                                            ELSE 'Inactivo'
                                            END AS estado
                ")
            );

            return $query->get();
            }

            public function obtenerDetallePrioridadOrden(array $pkPrioridadOrden) {
                $query = CatPrioridadOrden::select(
                    'id_priority',
                    'priority',
                    'description',
                    'color',
                    'active'
                )

                    ->where([
                        ['id_priority', $pkPrioridadOrden],
                        ['active', 1]
                    ]);

                    return $query->get();
            }

            public function actualizarPrioridadOrden(int $id, array $prioridadOrden) {
                $actualizar = CatPrioridadOrden::findOrFail($id);

                $actualizar->priority    = $prioridadOrden['priority'];
                $actualizar->description = $prioridadOrden['description'];
                $actualizar->color       = $prioridadOrden['color'];
                $actualizar->save();
            }
        }
