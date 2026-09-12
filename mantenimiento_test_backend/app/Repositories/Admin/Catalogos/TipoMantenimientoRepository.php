<?php

        namespace App\Repositories\Admin\Catalogos;

use App\Models\CatTipoMantenimiento;
use Illuminate\Support\Facades\DB;

        class TipoMantenimientoRepository
                {
            public function registrarTipoMantenimiento(array $tipoMantenimiento) {
                $registro = new CatTipoMantenimiento();
                $registro->type_maintenances = $tipoMantenimiento['type_maintenances'];
                $registro->color             = $tipoMantenimiento['color'];
                $registro->acronym           = $tipoMantenimiento['acronym'];
                $registro->active            = 1;
                $registro->save();

                return $registro->id_type_maintenances;
            }

            public function obtenerListatipoMantenimientos() {
                $query  = CatTipoMantenimiento::select(
                    'id_type_maintenances',
                    'type_maintenances',
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

            public function obtenerDetalletipoMantenimiento(int $pktipoMantenimiento) {
                $query = CatTipoMantenimiento::select(
                    'id_type_maintenances',
                    'type_maintenances',
                    'color',
                    'acronym',
                    'active'
                )

                ->where([
                    ['id_type_maintenances', $pktipoMantenimiento],
                    ['Active', 1]
                ]);

                return $query->get();
            }

            public function actualizartipoMantenimiento(int $id, array $tipoMantenimiento) {
                $actualizar = CatTipoMantenimiento::findOrFail($id);

                $actualizar->type_maintenances = $tipoMantenimiento['type_maintenances'];
                $actualizar->color             = $tipoMantenimiento['color'];
                $actualizar->acronym           = $tipoMantenimiento['acronym'];
                $actualizar->save();
            }

            public function cambiarStatustipoMantenimiento(int $pktipoMantenimiento) {
                $tipoMantenimientos         = CatTipoMantenimiento::findOrFail($pktipoMantenimiento);
                $tipoMantenimientos->Active = $tipoMantenimientos->active ? 0 :1;
                $tipoMantenimientos->save();

                return $tipoMantenimientos->active;
            }
        }
    