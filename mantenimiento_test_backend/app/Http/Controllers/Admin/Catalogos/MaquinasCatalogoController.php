<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Catalogos\MaquinasCatalogoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaquinasCatalogoController extends Controller
{
    protected MaquinasCatalogoService  $maquinasCatalogoService;

    public function __construct(MaquinasCatalogoService  $maquinasCatalogoService)
    {
        $this->maquinasCatalogoService = $maquinasCatalogoService;
    }

    public function registrarCatalogoMaquina(Request $request) {
        try {
            return $this->maquinasCatalogoService->registrarCatalogoMaquina($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar el Catalogo de la Maquina');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ], 500
            );
        }
    }

    public function obtenerListaCatalogoMaquina() {
        try {
            return $this->maquinasCatalogoService->obtenerListaCatalogoMaquina();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de los Catalogos Maquinas');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ], 500
            );
        }
    }

    public function obtenerDetalleCatalogoMaquina(int $pkCatalogoMaquina) {
        try {
            return $this->maquinasCatalogoService->obtenerDetalleCatalogoMaquina($pkCatalogoMaquina);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de detalle de Catalogo Maquina');
            Log::alert($error);

            return response()->json(
                [
                    'error'  => $error,
                    'mensaje'=> 'Ocurrio un error interno'
                ]
            );
        }
    }

    public function actualizarCatalogoMaquina(Request $request) {
        try {
            return $this->maquinasCatalogoService->actualizarMaquina($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al actualizar el Catalogo Maquina');
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
