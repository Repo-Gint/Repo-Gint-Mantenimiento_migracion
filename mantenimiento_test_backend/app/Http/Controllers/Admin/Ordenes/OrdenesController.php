<?php

namespace App\Http\Controllers\Admin\Ordenes;

use App\Http\Controllers\Controller;
use App\Services\Admin\Ordenes\OrdenesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdenesController extends Controller
{
    protected OrdenesService $ordenesService;

    public function __construct(
        OrdenesService $OrdenesService
    ) {
        $this->ordenesService = $OrdenesService;
    }

    public function obtenerRecursosRegistroOrden() {
        try {
            return $this->ordenesService->obtenerRecursosRegistroOrden();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Recursos registro ordenes');
            Log::alert($error);

            return response()->json(
                [
                    'error' => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ], 500 
            );
        }
    }

    public function registrarOrden (Request $request) {
        try {
            $orden = $request->all();
            $files = $request->file('images');

            return $this->ordenesService->registrarOrden($orden, $files);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar la orden');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ]
            );
        }
    }

    public function cancelarOrden(int $id_order)
    {
        try {
            return $this->ordenesService->cancelarOrden($id_order);

        } catch (\Throwable $error) {

            Log::alert('*********************************************');
            Log::alert('Error al cancelar orden');
            Log::alert($error->getMessage());

            return response()->json([
                'mensaje' => $error->getMessage()
            ], 400);
        }
    }

    public function obtenerStatusOrdenes() {
        try {
            return $this->ordenesService->obtenerStatusOrdenes();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Status Orden');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ]
            );
        }
    }

    public function ObtenerListaGeneralOrdenes(Request $request) {
        try {
            $pkArea = $request->all()['pkArea'];
            $pkStatus = $request->all()['pkStatus'];

            return $this->ordenesService->ObtenerListaGeneralOrdenes($pkArea, $pkStatus);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Ordenes');
            Log::alert($error);
            
            return response()->json(
                [
                    'error' => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ], 500
            );
        }
    }

    public function obtenerDetalleOrden($pkOrden) {
        try {
            return $this->ordenesService->obtenerDetalleOrden($pkOrden);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener detalle de la orden');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un eror interno'
                ]
            );
        }
    }

    
    public function eliminarEvidenciaOrden ($id_eviden_order) {
        try {
            return $this->ordenesService->eliminarEvidenciaOrden($id_eviden_order);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al eliminar evidencia orden');
            Log::alert($error->getMessage());
            
            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error inerno'
                ], 500
                );
        }
    }

    public function actualizarOrden(Request $request)
    {
        try {
            return $this->ordenesService->actualizarOrden($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al actualizar orden');
            Log::alert($error->getMessage());

            return response()->json([
                'mensaje' => 'Ocurrió un error interno'
            ], 500);
        }
    }

    public function asignarOrden(Request $request) {
    try {
        $pkOrden = $request->input('pkOrden') ?? $request->input('id_order');
        $idUsuario = $request->input('idUsuario') ?? $request->input('id_users');

        return $this->ordenesService->asignarOrden($pkOrden, $idUsuario);
    } catch (\Throwable $error) {
        Log::alert('*********************************************');
        Log::alert('Error al asignar Orden');
        Log::alert($error->getMessage());

        return response()->json([
            'error'   => $error,
            'mensaje' => 'Ocurrio un error interno'
        ], 500);
    }
}

    public function obtenerUsuariosAsignacion(int $pkOrden) {
        try {
            return $this->ordenesService->obtenerUsuariosAsignacion($pkOrden);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener usuarios para asignación');
            Log::alert($error);

            return response()->json(
                [
                    'error'   => $error,
                    'mensaje' => 'Ocurrio un error interno'
                ],
                500
            );
        }
    }

    public function obtenerMaquinasPorAreaYCategoria(int $pkArea, int $pkCatalogoMaquina) 
{
    try {
        return $this->ordenesService->obtenerMaquinasFiltradasPorAreaYCat($pkArea, $pkCatalogoMaquina);
        
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
