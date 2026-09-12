<?php

        namespace App\Repositories\Admin\Catalogos;
        use App\Models\CatDepartament;
        use Illuminate\Support\Facades\DB;

        class DepartamentosRepository
        {
            public function registrarDepartamento(array $departamentos) {
                $registro = new CatDepartament();
                $registro->Departament_ES  = $departamentos['Departament_ES'];
                $registro->Departament_EN  = $departamentos['Departament_EN'];
                $registro->Acronym         = $departamentos['Acronym'];
                $registro->active          = 1;
                $registro->save();

                return $registro->id_departaments;
            }

            public function obtenerListaDepartamentos() {
                $query = CatDepartament::select(
                    'id_departaments', 
                    'Departament_ES', 
                    'Departament_EN',
                    'Acronym', 
                    'Active', 
                    DB::raw("
                                            CASE
                                            WHEN Active = 1 THEN 'Activo'
                                            ELSE 'Inactivo'
                                            END as estado    
                    ")
                );

                return $query->get();
            }

            public function obtenerDetalleDepartamento($pkDepartamento) {
                $query = CatDepartament::select(
                    'id_departaments', 
                    'Departament_ES', 
                    'Departament_EN', 
                    'Acronym', 
                    'Active'
                )

                ->where([
                    ['id_departaments', $pkDepartamento], 
                    ['Active', 1]
                ]);

                return $query->get();
            }

            public function actualizarDepartamento($id, $departamento) {
                $actualizar = CatDepartament::findOrFail($id);

                $actualizar->Departament_ES = $departamento['Departament_ES'];
                $actualizar->Departament_EN = $departamento['Departament_EN'];
                $actualizar->Acronym        = $departamento['Acronym'];
                $actualizar->save();
            }

            public function cambiarStatusDepartamento($pkDepartamento) {
                $departamentos = CatDepartament::findOrFail($pkDepartamento);
                $departamentos->Active = $departamentos->Active ? 0 :1;
                $departamentos->save();

                return $departamentos->Active;
            }

        }
      