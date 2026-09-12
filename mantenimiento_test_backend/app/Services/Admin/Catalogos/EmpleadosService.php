<?php

namespace App\Services\Admin\Services\Catalogos;

use App\Repositories\Admin\Catalogos\EmpleadosRepository;

class EmpleadosService
{
    protected EmpleadosRepository $empleadosRepository;

    public function __construct(
        EmpleadosRepository $empleadosRepository
    ) {

        $this->empleadosRepository = $empleadosRepository;
    }

    public function registrarEmpleado(array $empleado)
    {
        $pkEmpleado = $this->empleadosRepository->registrarEmpleado($empleado);

        return response()->json(
            [
                'pkEmpleado' => $pkEmpleado,
                'mensaje'    => 'Se registro el empleadocon éxito',
                'title'      => 'Registro exitoso'
            ]
        );
    }

    public function obtenerListaEmpleados()
    {
        $empleados = $this->empleadosRepository->obtenerListaEmpleados();

        return response()->json(
            [
                'empleados' => $empleados,
                'mensaje'   => 'Se obtuvo la información correctamente de los empleados'
            ]
        );
    }

    public function obtenerDetalleEmpleado(int $pkEmpleado)
    {
        $empleados = $this->empleadosRepository->obtenerDetalleEmpleado($pkEmpleado);

        return response()->json(
            [
                'empleados'  => $empleados,
                'mensaje'    => 'Se obtuvo el detalle del empleado con exito'
            ]
        );
    }

    public function actualizarEmpleado(array $empleado)
    {
        $this->empleadosRepository->actualizarEmpleado($empleado['pkEmpleado'], $empleado['empleado']);
        return response()->json(
            [
                'title'   => 'Actualización exitosa',
                'mensaje' => 'Se actualizó correctamente el Empleado'
            ]
        );
    }
}
