<?php

        namespace App\Repositories\Admin\Catalogos;

use App\Models\CatRoles;

        class RolesRepository
        {
            public function registrarRoles(array $rol) {

                $registro = new CatRoles();
                $registro->roles       = $rol['roles'];
                $registro->description = $rol['description'];
                $registro->save();

                return $registro->id_roles;
            }
            
            public function obtenerListaRoles() {
                $query = CatRoles::select(
                'id_roles',
                'roles',
                'description'
                );

                return $query->get();
            }
        }
      