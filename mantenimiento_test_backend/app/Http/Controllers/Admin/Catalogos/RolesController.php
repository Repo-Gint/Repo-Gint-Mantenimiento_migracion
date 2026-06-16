<?php

namespace App\Http\Controllers\Admin\Catalogos;

use App\Http\Controllers\Controller;
use App\Services\Admin\Services\Catalogos\RolesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller{
    protected RolesService $rolesService;

    public function __construct(
        RolesService $RolesService
    ){
        $this->rolesService = $RolesService;
    }

    public function registrarRol(Request $request) {
        try {
            return $this->rolesService->registrarRol($request->all());
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al registrar rol');
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

    public function obtenerListaRoles() {
        try {
            return $this->rolesService->obtenerListaRoles();
        } catch (\Throwable $error) {
            Log::alert('*********************************************');
            Log::alert('Error al obtener información de Roles');
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
}
