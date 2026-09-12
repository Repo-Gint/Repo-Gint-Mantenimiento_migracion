<?php

        namespace App\Repositories\Admin\Catalogos;
        use App\Models\TblEmpleados;

        class EmpleadosRepository
        {
            public function registrarEmpleado(array $empleado) {
                $registro = new TblEmpleados();

                $registro->Code         = $empleado['Code'];
                $registro->Name         = $empleado['Name'];
                $registro->Paternal     = $empleado['Paternal'];
                $registro->Maternal     = $empleado['Maternal'];
                $registro->Photo        = $empleado['Photo'];
                $registro->busines_mail = $empleado['busines_mail'];
                $registro->active       = 1;
                $registro->save();

                return $registro->id_employe;
            }

            public function obtenerListaEmpleados() {
                $query = TblEmpleados::select(
                    'id_employee', 
                    'Code',
                    'Name', 
                    'Paternal',
                    'Maternal', 
                    'Photo', 
                    'busines_mail'  
                );

                return $query->get();
            }

            public function obtenerDetalleEmpleado(int $pkEmpleado) {
                $query = TblEmpleados::select(
                    'id_employee', 
                    'Code',
                    'Name',
                    'Paternal',
                    'Maternal',
                    'Photo',
                    'busines_mail',
                    'active'
                )
                    ->where('id_employee', $pkEmpleado);

                return $query->get();
            }

            public function actualizarEmpleado(int $id, array $empleado) {

            $actualizar = TblEmpleados::findOrFail($id);
            $actualizar->Name          = $empleado['Name'];
            $actualizar->Paternal      = $empleado['Paternal'];
            $actualizar->Maternal      = $empleado['Photo'];
            $actualizar->busines_mail  = $empleado['busines_mail'];
            $actualizar->save();
            }

            

        }
    