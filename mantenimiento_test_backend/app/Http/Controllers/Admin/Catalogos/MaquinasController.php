<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Catalogos\MaquinasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaquinasController extends Controller {
    protected MaquinasService $maquinasService;

    public function __construct(MaquinasService $maquinasService) 
    {
        $this->maquinasService = $maquinasService;
    }

    public function obtenerRecursosRegistroMaquina()
    {
        try {
            return $this->maquinasService->obtenerRecursosRegistroMaquina();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Recursos registro maquinas');
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

    public function registrarMaquina(Request $request) 
    {
        try {
            return $this->maquinasService->registrarMaquina($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar la Maqina');
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

    public function obtenerListaMaquinas() 
    {
        try {
            return $this->maquinasService->obtenerListaMaquinas();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Maquinas');
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
    
    public function obtenerDetalleMaquina($pkMaquina) 
    {
        try {
            return $this->maquinasService->obtenerDetalleMaquina($pkMaquina); 
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de detalle de la Maquina');
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

    public function actualizarMaquina(Request $request) 
    {
        try {

        return $this->maquinasService->actualizarMaquina($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al actualizar Maquina');
            Log::alert($error->getMessage());

            return response()->json(
                [
                    'mensaje' => 'Ocurrio un error interno'
                ],
                500
            );
        }
    }

    public function cambiarStatusMaquina($pkMaquina) 
    {
        try {
            return $this->maquinasService->cambiarStatusMaquina($pkMaquina);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al cambiar Status De Maquina');
            Log::alert($error);
            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ]
            );
        }
    }

    public function obtenerMaquinasPorAreaYCategoria(Request $request) 
    {
        try {
            $pkArea = $request->input('id_area');
            $pkCatalogoMaquina = $request->input('id_cat_machines');

            return $this->maquinasService->obtenerMaquinasPorAreaYCategoria($pkArea, $pkCatalogoMaquina);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al filtrar máquinas por área y categoría');
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
