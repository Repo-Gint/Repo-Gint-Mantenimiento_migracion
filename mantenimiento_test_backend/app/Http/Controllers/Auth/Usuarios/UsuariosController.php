<?php

namespace App\Http\Controllers\Auth\Usuarios;

use App\Http\Controllers\Controller;
use App\Services\Auth\Usuarios\UsuariosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UsuariosController extends Controller{
    protected UsuariosService $usuariosService;

    public function __construct(
        UsuariosService $UsuariosService
    ) {
        $this->usuariosService = $UsuariosService;
    }

    public function obtenerRecursosRegistroUsuario() {
                try {
            return $this->usuariosService->obtenerRecursosRegistroUsuario();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Recursos registro usuarios');
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

public function registrarUsuario(Request $request)
{
    try {

        Log::alert($request->all());

        return $this->usuariosService->registrarUsuario($request->all());

    } catch (\Throwable $error) {

        Log::alert('*********************************************');
        Log::alert('Error al registrar usuario');
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

    public function obtenerListaGeneralUsuarios() {
        try {
            return $this->usuariosService->obtenerListaGeneralUsuarios();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Usuario');
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

        public function obtenerDetalleUsuario($pkUsuario) {
            try {
                return $this->usuariosService->obtenerDetalleUsuario($pkUsuario);
            } catch (\Throwable $error) {
                Log::alert('*********************************************');
                Log::alert('Error al obtener información de Usuario PorPK');
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

        public function actualizarUsuario(Request $request)
        {
            try {
    
                return $this->usuariosService->actualizarUsuario($request->all());
            } catch (\Throwable $error) {
    
                Log::alert('*********************************************');
                Log::alert('Error al actualizar usuario');
                Log::alert($error->getMessage());
    
                return response()->json([
                    'mensaje' => 'Ocurrió un error interno'
                ], 500);
            }
        }
    
        public function cambiarStatusUsuario($id)
        {
            try {
                return $this->usuariosService->cambiarStatusUsuario($id);
            } catch (\Throwable $error) {
                Log::alert('*********************************************');
                Log::alert('Error al cambiar Status Del Usuario');
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


        public function login(Request $request)
        {
            try {
                return $this->usuariosService->login($request->all());
            } catch (\Throwable $error) {
                Log::alert('*********************************************');
                Log::alert('Error al iniciar sesión usuario');
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

    public function cerrarSesion(Request $request)
    {
        try {
            return $this->usuariosService->cerrarSesion($request);
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al cerrar sesión');
            Log::alert($error->getMessage());

            return response()->json([
                'mensaje' => 'Ocurrió un error interno'
            ], 500);
        }
    }
}
