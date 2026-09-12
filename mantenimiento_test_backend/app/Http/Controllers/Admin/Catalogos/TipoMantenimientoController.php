<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Catalogos\TipoMantenimientoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TipoMantenimientoController extends Controller
{
    protected TipoMantenimientoService $tipoMantenimientoService;

    public function __construct(TipoMantenimientoService $tipoMantenimientoService)
    {
        $this->tipoMantenimientoService = $tipoMantenimientoService;
    }

    public function registrarTipoMantenimiento(Request $request) {
        try {
            return $this->tipoMantenimientoService->registrarTipoMantenimiento($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar el Tipo de Mantenimiento');
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

    public function obtenerListatipoMantenimientos() {
        try {
            return $this->tipoMantenimientoService->obtenerListaMantenimientos();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información del Tipo de Mantenimiento');
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

    public function obtenerDetalletipoMantenimiento($pktipoMantenimiento) {
    try {
            return $this->tipoMantenimientoService->obtenerDetalletipoMantenimiento($pktipoMantenimiento);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de detalle de Tipo de Mantenimiento');
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

    public function actualizartipoMantenimiento(Request $request) {
        try {
            return $this->tipoMantenimientoService->actualizartipoMantenimiento($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al actualizar Tipo de Mantenimiento');
            Log::alert($error->getMessage());

            return response()->json(
                [
                    'mensaje' => 'Ocurrio un error interno'
                ],
                500
            );
        }
    }

    public function cambiarStatustipoMantenimiento($pktipoMantenimiento) {
        try {
            return $this->tipoMantenimientoService->cambiarStatustipoMantenimiento($pktipoMantenimiento);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al cambiar Status De Tipo de Mantenimiento');
            Log::alert($error->getMessage());

            return response()->json(
                [
                    'mensaje' => 'Ocurrio un error interno'
                ],
                500
            );
        }
    }
}
