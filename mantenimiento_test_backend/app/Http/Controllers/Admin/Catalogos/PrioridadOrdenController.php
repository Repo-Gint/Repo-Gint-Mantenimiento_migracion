<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Catalogos\PrioridadOrdenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrioridadOrdenController extends Controller
{
    protected PrioridadOrdenService $prioridadOrdenervice;

    public function __construct(
        PrioridadOrdenService $PrioridadOrdenervice
    ) {
        $this->prioridadOrdenervice = $PrioridadOrdenervice;
    }

    public function registrarPrioridadOrden(Request $request) {
        try {
            return $this->prioridadOrdenervice->registrarPrioridadOrden($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar la Prioridad de Orden');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ]
            );

        }
    }

    public function obtenerListaPrioridadOrden() {
        try {
            return $this->prioridadOrdenervice->obtenerListaPrioridadOrden();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de las listas Prioridad Orden');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ], 500
            );
        } 
    }

    public function obtenerDetallePrioridadOrden ( array $pkPrioridadOrden) {
        try {
            return $this->obtenerDetallePrioridadOrden($pkPrioridadOrden);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de detalle de Prioridad Orden');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ], 500
            );
        }
    }

    public function actualizarPrioridadOrden (Request $request) {
        try {
            return $this->prioridadOrdenervice->actualizarPrioridadOrden($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al actualizar Prioridad Orden');
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
