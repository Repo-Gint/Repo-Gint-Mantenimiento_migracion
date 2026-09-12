<?php

        namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\DepartamentosRepository;

        class DepartamentsService {
            protected DepartamentosRepository $departamentosRepository;

            public function __construct(
                DepartamentosRepository $departamentosRepository
            ) {
                $this->departamentosRepository = $departamentosRepository;
            }

            public function registrarDepartamento(array $departamentos) {
                
                $pkDepartamento = $this->departamentosRepository->registrarDepartamento($departamentos);

                return response()->json(
                    [
                        'pkDepartamento'  => $pkDepartamento,
                        'mensaje' => 'Se registro el Departamento con éxito', 
                        'title'   => 'Registro exitoso'
                    ]
                );
            }

            public function obtenerListaDepartamentos() {
                $departamentos = $this->departamentosRepository->obtenerListaDepartamentos();

                return response()->json(
                    [
                        'departaments' => $departamentos,
                        'mensaje'      => 'Se obtuvo la información coorectamente'
                    ]
                );
            }

            public function obtenerDetalleDepartamento(int $pkDepartamento) {
                $departamento = $this->departamentosRepository->obtenerDetalleDepartamento($pkDepartamento);

                return response()->json(
                    [
                        'departamento' => $departamento[0],
                        'mensaje'      => 'Se obtuvo la información correcta'
                    ]
                );
            }

            public function actualizarDepartamento(array $departamento) {
                $this->departamentosRepository->actualizarDepartamento($departamento['pkDepartamento'], $departamento['departamento']);

                return response()->json(
                    [
                    'title'   => 'Actualización exitosa', 
                    'mensaje' => 'Se actualizo correctamente el departamento'
                    ]
                );
            }

            public function cambiarStatusDepartamento(int $pkDepartamento) {
                $status = $this->departamentosRepository->cambiarStatusDepartamento($pkDepartamento);

                return response()->json(
                    [
                        'title'   => ($status ? 'Activar' : 'Inactivar'). ' departamento',
                        'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' el departamento con éxito'
                    ]
                );
            }
        }
    