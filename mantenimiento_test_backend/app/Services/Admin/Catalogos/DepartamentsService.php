<?php

        namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\DepartamentsRepository;

        class DepartamentsService {
            protected DepartamentsRepository $departamentsRepository;

            public function __construct(
                DepartamentsRepository $departamentsRepository
            ) {
                $this->departamentsRepository = $departamentsRepository;
               
            }

            public function registrarDepartamento(array $departamentos) {

                $pkDepartamento = $this->departamentsRepository->registrarDepartamento($departamentos);

                return response()->json(
                    [
                        'pkDepartamento'  => $pkDepartamento,
                        'mensaje' => 'Se registro el Departamento con éxito', 
                        'title'   => 'Registro exitoso'
                    ]
                );
            }

            public function obtenerListaDepartamentos() {
                $departamentos = $this->departamentsRepository->obtenerListaDepartamentos();

                return response()->json(
                    [
                        'departaments' => $departamentos,
                        'mensaje'      => 'Se obtuvo la información coorectamente'
                    ]
                );
            }

            public function obtenerDetalleDepartamento($pkDepartamento) {
                $departamento = $this->departamentsRepository->obtenerDetalleDepartamento($pkDepartamento);

                return response()->json(
                    [
                        'departamento' => $departamento[0],
                        'mensaje'      => 'Se obtuvo la información correcta'
                    ]
                );
            }

            public function actualizarDepartamento($departamento) {
                $this->departamentsRepository->actualizarDepartamento($departamento['pkDepartamento'], $departamento['departamento']);

                return response()->json(
                    [
                    'title'   => 'Actualización exitosa', 
                    'mensaje' => 'Se actualizo correctamente el departamento'
                    ]
                );
            }

            public function cambiarStatusDepartamento($pkDepartamento) {
                $status = $this->departamentsRepository->cambiarStatusDepartamento($pkDepartamento);

                return response()->json(
                    [
                        'title'   => ($status ? 'Activar' : 'Inactivar'). ' departamento',
                        'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' el departamento con éxito'
                    ]
                );
            }
        }
    