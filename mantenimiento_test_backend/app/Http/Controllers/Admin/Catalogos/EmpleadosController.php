<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Services\Catalogos\EmpleadosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmpleadosController extends Controller
{
    protected EmpleadosService  $empleadosService;

    public function __construct(EmpleadosService  $empleadosService)
    {
        $this->empleadosService = $empleadosService;
    }

    public function registrarEmpleado(Request $request) {
        try {
            return $this->empleadosService->registrarEmpleado($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar el Empleado');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ], 500
            );
        }
    }

    public function obtenerListaEmpleados() {
        try {
            return $this->empleadosService->obtenerListaEmpleados();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de los Empleados');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ], 500
            );
        }
    }

    public function obtenerDetalleEmpleado(int $pkEmpleado) {
        try {
            return $this->empleadosService->obtenerDetalleEmpleado($pkEmpleado);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de detalle del Empleado');
            Log::alert($error);

            return response()->json(
                [
                    'error'  => $error,
                    'mensaje'=> 'Ocurrio un error interno'
                ]
            );
        }
    }

    public function actualizarEmpleado(Request $request) {
        try {
            return $this->empleadosService->actualizarEmpleado($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al actualizar el Empleado');
            Log::alert($error->getMessage());

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ], 500
            );
        }
    }
}
