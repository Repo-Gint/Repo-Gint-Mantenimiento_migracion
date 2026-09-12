<?php

        namespace App\Repositories\Admin\Catalogos;

        use App\Models\CatTipoOrden;
        use Illuminate\Support\Facades\DB;

        class TipoOrdenRepository {
            
            public function registrarTipoOrden(array $tipoOrden) {
                $registro = new CatTipoOrden(); 
                $registro->type_orders    = $tipoOrden['type_orders'];
                $registro->color   = $tipoOrden['color'];
                $registro->active  = 1;
                $registro->save();

                return $registro->id_type_orders;
            }

            public function obtenerListaTipoOrden() {
                $query = CatTipoOrden::select(
                    'id_type_orders',
                    'type_orders', 
                    'color',
                    'active',
                    DB::raw("
                                            CASE 
                                                WHEN active = 1 THEN 'Activo'
                                                ELSE 'Inactivo'
                                                END AS estado
                    ")
                );

                return $query->get();
            }

            public function obtenerDetalleTipoOrden(int $pktipoOrden) {
                $query = CatTipoOrden::select(
                    'id_type_orders', 
                    'type_orders',
                    'color',
                    'active', 
                )    
                    ->where([
                        ['id_type_orders', $pktipoOrden], 
                        ['active', 1]
                    ]);

                    return $query->get();
            }

            public function actualizarTipoOrden(int $id, array $tipoOrden) {
                $actualizar = CatTipoOrden::findOrFail($id);

                $actualizar->type_orders    = $tipoOrden['type_orders'];
                $actualizar->color   = $tipoOrden['color'];
                $actualizar->save();
            }

            public function cambiarStatusTipoOrden(int $pktipoOrden) {
                $tipoOrdenes         = CatTipoOrden::findOrFail($pktipoOrden);

                $tipoOrdenes->active = $tipoOrdenes->active ? 0 :1;
                $tipoOrdenes->save();

                return $tipoOrdenes->active;
            }
        }
