<?php

        namespace App\Repositories\Admin\Catalogos;

use App\Models\CatTipoMantenimiento;
use Illuminate\Support\Facades\DB;

        class TipoMantenimiento
        {
            public function registrarTipoMantenimiento(array $tipoMantenimiento) {
                $registro = new CatTipoMantenimiento();
                $registro->type_maintances = $tipoMantenimiento['type_maintances'];
                $registro->color           = $tipoMantenimiento['color'];
                $registro->acronym         = $tipoMantenimiento['acronym'];
                $registro->active          = 1;
                $registro->save();

                return $registro->id_type_maintenances;
            }

            public function obtenerListatipoMantenimentos() {
                $query  = CatTipoMantenimiento::select(
                    'id_type_maintenances',
                    'type_maintances',
                    'color',
                    'acronym',
                    'active',
                    DB::raw("
                                                    CASE
                                                    WHEN Active = 1 THEN 'Activo'
                                                    ELSE 'Inactivo'
                                                    END as estado
                    ")
                );

                return $query->get();
            }

            public function obtenerDetalletipoMantenimento($pkMantenimiento) {
                $query = CatTipoMantenimiento::select(
                    'id_type_maintenances',
                    'type_maintances',
                    'color',
                    'acronym',
                    'active'
                )

                ->where([
                    ['id_type_maintenances', $pkMantenimiento],
                    ['Active', 1]
                ]);

                return $query->get();
            }

            public function actualizarMantenimiento($id, $mantenimiento) {
                $actualizar = CatTipoMantenimiento::findOrFail($id);

                $actualizar->type_maintances = $mantenimiento['type_maintances'];
                $actualizar->color = $mantenimiento['color'];
                $actualizar->acronym = $mantenimiento['acronym'];
                $actualizar->save();
            }

            public function cambiarStatusMantenimiento($pkMantenimiento) {
                $mantenimientos = CatTipoMantenimiento::findOrFail($pkMantenimiento);
                $mantenimientos->Active = $mantenimientos->Active ? 0 :1;
                $mantenimientos->save();

                return $mantenimientos->absctive();
            }
        }
    