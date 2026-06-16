<?php

        namespace App\Repositories\Admin\Catalogos;

        use App\Models\CatAreas;
        use Illuminate\Support\Facades\DB;

        class AreasRepository
        {
            public function registrarArea(array $areas) {
                $registro = new CatAreas(); 
                $registro->area    = $areas['area'];
                $registro->acronym = $areas['acronym'];
                $registro->color   = $areas['color'];
                $registro->active  = 1;
                $registro->save();

                return $registro->id_area;
            }

            public function obtenerListaAreas() {
                $query = CatAreas::select(
                    'id_area', 
                    'area', 
                    'acronym', 
                    'color', 
                    'active',
                    DB::raw("
                                        CASE
                                            WHEN active = 1 THEN 'Activo'
                                            ELSE 'Inactivo'
                                            END as estado
                    ") 
                );

                return $query->get();
            }

            public function obtenerDetalleArea($pkArea) {
                $query = CatAreas::select(
                    'id_area', 
                    'area', 
                    'acronym', 
                    'color', 
                    'active'
                )
                
                    ->where([
                        ['id_area', $pkArea],
                        ['active', 1]
            ]);
                return $query->get();
            }

            public function actualizarArea($id, $area) {
                $actualizar = CatAreas::findOrFail($id); 
                
                $actualizar->area    = $area['area'];
                $actualizar->acronym = $area['acronym'];
                $actualizar->save();
            }

            public function cambiarStatusArea($pkArea) {
                $areas = CatAreas::findOrFail($pkArea);
                $areas->active = $areas->active ? 0 :1; 
                $areas->save();

                return $areas->active;
            }
        }