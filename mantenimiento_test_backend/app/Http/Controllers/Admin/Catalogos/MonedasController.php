<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Catalogos\MonedasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonedasController extends Controller {
    protected MonedasService $monedasService;

    public function __construct(MonedasService $monedasService) 
        {
            $this->monedasService = $monedasService;
        }

    public function registrarMoneda(Request $request) 
    {
          try {
            return $this->monedasService->registrarMoneda($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar la Moneda');
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

    public function obtenerListaMonedas() 
    {
        try {
            return $this->monedasService->obtenerListaMonedas();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Monedas');
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
    
    public function obtenerDetalleMoneda($pkMoneda)
       {
        try {
            return $this->monedasService->obtenerDetalleMoneda($pkMoneda); 
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de detalle de la Moneda');
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

    public function actualizarMoneda(Request $request) 
     {
        try {

        return $this->monedasService->actualizarMoneda($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al actualizar Moneda');
            Log::alert($error->getMessage());

            return response()->json(
                [
                    'mensaje' => 'Ocurrio un error interno'
                ],
                500
            );
        }
    }

    public function cambiarStatusMoneda($pkMoneda) 
      {
        try {
            return $this->monedasService->cambiarStatusMoneda($pkMoneda);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al cambiar Status De Moneda');
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
