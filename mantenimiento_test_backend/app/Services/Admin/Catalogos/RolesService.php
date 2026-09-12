<?php

namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\RolesRepository;

class RolesService
{
    protected RolesRepository $rolesRepository;

    public function __construct(
        RolesRepository $rolesRepository
    ) {
        $this->rolesRepository = $rolesRepository;
    }

    public function registrarRol(array $rol) {
        $pkRol = $this->rolesRepository->registrarRoles($rol);

        return response()->json(
            [
                'pkRol' => $pkRol,
                'mensaje' => 'Se Registro el Rol con éxito',
                'title'  => 'Registro exitoso'
            ]
        );
    }

    public function obtenerListaRoles() {
        $rol= $this->rolesRepository->obtenerListaRoles();

        return response()->json(
            [
                'rol' => $rol,
                'mensaje' => 'Se obtuvo la informacin correcta'
            ]
        );
    }
}