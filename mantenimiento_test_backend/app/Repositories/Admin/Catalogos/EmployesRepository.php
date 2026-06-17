<?php


        namespace App\Repositories\Admin\Catalogos;
        use App\Models\TblEmployee;
        use Carbon\Carbon;
        use Illuminate\Support\Facades\DB;
        use Illuminate\Support\Facades\Log;

        class EmployesRepository
        {
            public function registrarEmpleado($empleado) {
                $registro = new TblEmployee();

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
                $query = TblEmployee::select(
                    'id_employe', 
                    'Code',
                    'Name', 
                    'Paternal',
                    'Maternal', 
                    'Photo', 
                    'busines_mail'  
                );

                return $query->get();
            }

            public function obtenerListaDetalleEmpleado($pkEmpleado) {
                $query = TblEmployee::select(
                    'id_employe', 
                    'Code',
                    'Name',
                    'Paternal',
                    'Maternal',
                    'Photo',
                    'busines_mail',
                    'active'
                )
                    ->where('id_employe', $pkEmpleado);

                return $query->get();
            }

            public function actualizarEmpleado($id, $empleado) {

            $actualizar = TblEmployee::findOrFail($id);
            $actualizar->Name          = $empleado['Name'];
            $actualizar->Paternal      = $empleado['Paternal'];
            $actualizar->Maternal      = $empleado['Photo'];
            $actualizar->busines_mail  = $empleado['busines_mail'];
            $actualizar->save();
            }

            

        }
      