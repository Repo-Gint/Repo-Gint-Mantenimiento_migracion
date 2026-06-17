<?php

        namespace App\Services\Admin\Catalogos;
        use App\Repositories\Admin\Catalogos\MaquinasRepository;

        class MaquinasService {
            protected MaquinasRepository $maquinasRepository;

            public function __construct(
                MaquinasRepository $maquinasRepository
            ) {
                $this->maquinasRepository = $maquinasRepository;
            }

            public function registrarMaquina(array $maquina) {
                $pkMaquina = $this->maquinasRepository->registrarMaquina($maquina);

                return response()->json(
                    [
                        'pkMaquina' => $pkMaquina, 
                        'mensaje'   => 'Se registro la Maquina con éxito', 
                        'title'     => 'Registro exitoso'
                    ]
                );
            }

            public function obtenerListaMaquinas() {
                $maquinas = $this->maquinasRepository->obtenerListaMaquinas();

                return response()->json(
                    [
                        'maquinas' => $maquinas, 
                        'mensaje'  => 'Se obtuvo la información correctamente de Maquinas'
                    ]
                );
            }

            public function obtenerDetalleMaquina($pkMaquina) {
                $maquinas = $this->maquinasRepository->obtenerDetalleMaquina($pkMaquina);

                return response()->json(
                    [
                        'maquinas' => $maquinas[0],
                        'mensaje'  => 'Se obtuvo la información correcta'
                    ]
                );
            }

            public function actualizarMaquina($maquina) {
                $this->maquinasRepository->actualizarMaquina($maquina['pkMaquina'], $maquina['maquina']);

                return response()->json(
                    [
                        'title'   => 'Actualización exitosa', 
                        'mensaje' => 'Se actualizo correctamente la maquina'
                    ]
                );
            }

            public function cambiarStatusMaquina($pkMaquina) {

                $status = $this->maquinasRepository->cambiarStatusMaquina($pkMaquina);

                return response()->json(
                    [
                        'title'   => ($status ? 'Activar' : 'Inactivar'). ' maquina',
                        'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' el maquina con éxito'
                    ]
                );
            }
            
        }
