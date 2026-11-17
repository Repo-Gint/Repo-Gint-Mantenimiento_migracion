<?php

namespace App\Repositories\Admin\Catalogos;
use App\Models\TblEmpleados;

class EmpleadosRepository
{
    public function registrarEmpleado(array $empleado) {
        $registro = new TblEmpleados();

        // Cambiado 'Code' por 'slug', y minúsculas para coincidir con la BD
        $registro->slug         = $empleado['slug']; 
        $registro->name         = $empleado['name'];
        $registro->paternal     = $empleado['paternal'];
        $registro->maternal     = $empleado['maternal'];
        $registro->photo        = $empleado['photo'];
        $registro->busines_mail = $empleado['busines_mail'];
        $registro->active       = 1;
        $registro->save();

        // Corregido el error tipográfico: id_employe -> id_employee
        return $registro->id_employee; 
    }

    public function obtenerListaEmpleados() {
        $query = TblEmpleados::select(
            'id_employee', 
            'slug',         // Reemplaza 'Code'
            'name',         // Minúsculas
            'paternal',
            'maternal', 
            'photo', 
            'busines_mail'  
        );

        return $query->get();
    }

    public function obtenerDetalleEmpleado(int $pkEmpleado) {
        $query = TblEmpleados::select(
            'id_employee', 
            'slug',         // Reemplaza 'Code'
            'name',
            'paternal',
            'maternal',
            'photo',
            'busines_mail',
            'active'
        )
        ->where('id_employee', $pkEmpleado);

        return $query->get();
    }

    public function actualizarEmpleado(int $id, array $empleado) {
        $actualizar = TblEmpleados::findOrFail($id);
        
        $actualizar->name         = $empleado['name'];
        $actualizar->paternal     = $empleado['paternal'];
        
        // CORRECCIÓN: Tenías asignado $empleado['Photo'] a Maternal
        $actualizar->maternal     = $empleado['maternal']; 
        
        // Agregada la actualización de la foto que faltaba
        if (isset($empleado['photo'])) {
            $actualizar->photo    = $empleado['photo'];
        }
        
        $actualizar->busines_mail = $empleado['busines_mail'];
        $actualizar->save();
    }
}