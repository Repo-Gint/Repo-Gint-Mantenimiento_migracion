<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Catalogos\AreasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AreasController extends Controller {
    protected AreasService $areasService;

        public function __construct(AreasService $areasService)
    {
        $this->areasService = $areasService;
    }

    public function registrarArea(Request $request)
    {
        try {
            return $this->areasService->registrarArea($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar la Area');
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

      public function obtenerListaAreas()
    {
        try {
            return $this->areasService->obtenerListaAreas();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Areas');
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

    public function obtenerDetalleArea($pkArea)
    {
        try {
            return $this->areasService->obtenerDetalleArea($pkArea);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de detalle de Area');
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

    public function actualizarArea(Request $request)
    {
        try {

            return $this->areasService->actualizarArea($request->all());
        } catch (\Throwable $error) {

            Log::alert('*********************************************');
            Log::alert('Error al actualizar Area');
            Log::alert($error->getMessage());

            return response()->json([
                'mensaje' => 'Ocurrió un error interno'
            ], 500);
        }
    }

    public function cambiarStatusArea($pkArea)
    {
        try {
            return $this->areasService->cambiarStatusArea($pkArea);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al cambiar Status De Area');
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
