<?php

namespace App\Http\Controllers\Admin\Ordenes;

use App\Http\Controllers\Controller;
use App\Services\Admin\Ordenes\OrdenesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

            return response()->json([
                'error'   => $error,
                'mensaje' => 'Ocurrio un error interno'
            ], 500);
        }
    }

    public function registrarOrden(Request $request) {
        try {
            $orden = $request->all();
            $files = $request->file('images');

            $idUser = $request->input('id_users_auth') ?? $request->attributes->get('id_users_auth');

            if ($idUser) {
                $userRecord = DB::table('tbl_users')->where('id_users', $idUser)->first();
                if ($userRecord) {
                    $pkRol = (int)$userRecord->id_rol_users;
                    if ($pkRol === 1) {
                        $orden['id_employee'] = $userRecord->id_employee;
                    }
                }
            }

            return $this->ordenesService->registrarOrden($orden, $files);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar la orden');
            Log::alert($error);

            return response()->json([
                'error'   => $error,
                'mensaje' => 'Ocurrio un error interno'
            ]);
        }
    }

    public function cancelarOrden(Request $request, int $id_order)
    {
        try {
            // BLOQUEO ESTRICTO POR API DE PERMISOS
            if (!$this->ordenesService->verificarPermisoUsuario($request, 'cancelar_orden')) {
                return response()->json([
                    'title'   => 'Acceso denegado',
                    'mensaje' => 'No tienes el permiso del administrador, contacta al administrador para que te lo autorice.'
                ], 403);
            }

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

            return response()->json([
                'error'   => $error,
                'mensaje' => 'Ocurrio un error interno'
            ]);
        }
    }

    public function ObtenerListaGeneralOrdenes(Request $request) {
        try {
            $pkArea   = $request->input('pkArea');
            $pkStatus = $request->input('pkStatus');
            $idUser   = $request->input('id_users_auth') ?? $request->attributes->get('id_users_auth');

            $pkRol = 1; 
            if ($idUser) {
                $userRecord = DB::table('tbl_users')->where('id_users', $idUser)->first();
                if ($userRecord) {
                    $pkRol = $userRecord->id_rol_users;
                }
            }

            return $this->ordenesService->ObtenerListaGeneralOrdenes($pkArea, $pkStatus, $idUser, $pkRol);
        } catch (\Throwable $error) {
            Log::error('Error al obtener órdenes: ' . $error->getMessage());
            
            return response()->json([
                'error'   => $error->getMessage(),
                'mensaje' => 'Ocurrio un error interno'
            ], 500);
        }
    }

    public function obtenerDetalleOrden($pkOrden) {
        try {
            if (!is_numeric($pkOrden)) {
                return response()->json([
                    'mensaje' => 'El identificador de la orden es incorrecto'
                ], 400);
            }

            return $this->ordenesService->obtenerDetalleOrden((int)$pkOrden);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener detalle de la orden');
            Log::alert($error);

            return response()->json([
                'error'   => $error,
                'mensaje' => 'Ocurrio un eror interno'
            ], 500);
        }
    }

    public function eliminarEvidenciaOrden($id_eviden_order) {
        try {
            return $this->ordenesService->eliminarEvidenciaOrden($id_eviden_order);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al eliminar evidencia orden');
            Log::alert($error->getMessage());
            
            return response()->json([
                'error'   => $error,
                'mensaje' => 'Ocurrio un error inerno'
            ], 500);
        }
    }

    public function actualizarOrden(Request $request)
    {
        try {
            // BLOQUEO ESTRICTO POR API DE PERMISOS
            if (!$this->ordenesService->verificarPermisoUsuario($request, 'editar_orden')) {
                return response()->json([
                    'title'   => 'Acceso denegado',
                    'mensaje' => 'No tienes el permiso del administrador, contacta al administrador para que te lo autorice.'
                ], 403);
            }

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
            // BLOQUEO ESTRICTO POR API DE PERMISOS
            if (!$this->ordenesService->verificarPermisoUsuario($request, 'editar_orden')) {
                return response()->json([
                    'title'   => 'Acceso denegado',
                    'mensaje' => 'No tienes el permiso del administrador, contacta al administrador para que te lo autorice.'
                ], 403);
            }

            $pkOrden   = $request->input('pkOrden') ?? $request->input('id_order');
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

            return response()->json([
                'error'   => $error,
                'mensaje' => 'Ocurrio un error interno'
            ], 500);
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

            return response()->json([
                'error'   => $error,
                'mensaje' => 'Ocurrió un error interno'
            ], 500);
        }
    }

    public function obtenerMensajesOrden($id_order)
    {
        try {
            return $this->ordenesService->obtenerMensajesOrden($id_order);
        } catch (\Throwable $error) {
            Log::alert('Error al obtener mensajes: ' . $error->getMessage());
            return response()->json(['mensaje' => 'Ocurrió un error interno'], 500);
        }
    }

    public function enviarMensajeOrden(Request $request)
    {
        try {
            return $this->ordenesService->enviarMensajeYNotificacion($request->all());
        } catch (\Throwable $error) {
            Log::alert('Error al enviar mensaje y correo: ' . $error->getMessage());
            return response()->json(['mensaje' => 'Ocurrió un error interno'], 500);
        }
    }

    public function cambiarStatusYSolucionarOrden(Request $request)
    {
        try {
            return $this->ordenesService->finalizarORechazarOrden($request->all());
        } catch (\Throwable $error) {
            Log::alert('Error al cambiar estatus/solucionar orden: ' . $error->getMessage());
            return response()->json(['mensaje' => 'Ocurrió un error interno'], 500);
        }
    }

    public function calificarSolucionOrden(Request $request)
    {
        try {
            $id_order  = $request->input('id_order');
            $respuesta = $request->input('respuesta');

            return $this->ordenesService->calificarSolucionOrden($id_order, $respuesta);
        } catch (\Throwable $error) {
            Log::alert('Error al calificar solución de orden: ' . $error->getMessage());
            return response()->json(['mensaje' => 'Ocurrió un error interno'], 500);
        }
    }
}