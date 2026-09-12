<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Catalogos\TipoOrdenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TipoOrdenController extends Controller {
    protected TipoOrdenService $tipoOrdenService;

    public function __construct(TipoOrdenService $tipoOrdenService)
    {
        $this->tipoOrdenService = $tipoOrdenService;
    }

    public function registrarTipoOrden(Request $request) {
        try {
            return $this->tipoOrdenService->registrarTipoOrden($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar el Tipo Orden');
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

    public function obtenerListaTipoOrden() {
        try {
            return $this->tipoOrdenService->obtenerListaTipoOrden();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información del Tipo de Ordenes');
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

    public function obtenerDetalleTipoOrden($pkTipoOrden) {
        try {
            return $this->tipoOrdenService->obtenerDetalleTipoOrden($pkTipoOrden);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de detalle de Tipo de Orden');
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

    public function actualizarTipoOrden(Request $request) {
        try {
            return $this->tipoOrdenService->actualizarTipoOrden($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al actualizar Tipo Orden');
            Log::alert($error->getMessage());

            return response()->json(
                [
                    'mensaje' => 'Ocurrio un error interno'
                ],
                500
            );
        }
    }

    public function cambiarStatusTipoOrden($pkTipoOrden) {
        try {
            return $this->tipoOrdenService->cambiarStatusTipoOrden($pkTipoOrden);
        } catch (\Throwable $error) {
             Log::alert('*********************************************');
            Log::alert('Error al cambiar Status De Tipo Orden');
            Log::alert($error);
            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ]
            );
        }
    }
}
