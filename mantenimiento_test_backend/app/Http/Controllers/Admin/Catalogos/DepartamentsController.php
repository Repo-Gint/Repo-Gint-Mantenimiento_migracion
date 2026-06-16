<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Catalogos\DepartamentsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartamentsController extends Controller {
    protected DepartamentsService $departamentsService;

    public function __construct(DepartamentsService $departamentsService) 
    {
        $this->departamentsService = $departamentsService;
    }

    public function registrarDepartamento(Request $request) {
        try {
            return $this->departamentsService->registrarDepartamento($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar el Departamento');
            Log::alert($error);

            return response()->json(
                [
                    'error' => $error,
                    'mensaje' => 'Ocurrió un error interno'
                ],
                500
            );
        }
    }

    public function obtenerListaDepartamentos()
    {
        try {
            return $this->departamentsService->obtenerListaDepartamentos();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Departamentos');
            Log::alert($error);
            return response()->json(
                [
                    'error' => $error,
                    'mensaje' => 'Ocurrió un error interno'
                ],
                500
            );
        }
    }

    public function obtenerDetalleDepartamento($pkDepartamento)
    {
        try {
            return $this->departamentsService->obtenerDetalleDepartamento($pkDepartamento);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de detalle de Departamento');
            Log::alert($error);
            return response()->json(
                [
                    'error' => $error,
                    'mensaje' => 'Ocurrió un error interno'
                ],
                500
            );
        }
    }

       public function actualizarDepartamento(Request $request)
    {
        try {

            return $this->departamentsService->actualizarDepartamento($request->all());
        } catch (\Throwable $error) {

            Log::alert('*********************************************');
            Log::alert('Error al actualizar Departamento');
            Log::alert($error->getMessage());

            return response()->json([
                'mensaje' => 'Ocurrió un error interno'
            ], 500);
        }
    }

    public function cambiarStatusDepartamento($pkDepartamento)
    {
        try {
            return $this->departamentsService->cambiarStatusDepartamento($pkDepartamento);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al cambiar Status De Departamento');
            Log::alert($error);
            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrió un error interno'
                ],
                500
            );
        }
    }
    
}
