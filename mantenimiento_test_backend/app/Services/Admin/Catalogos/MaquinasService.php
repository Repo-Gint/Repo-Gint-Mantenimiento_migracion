<?php

        namespace App\Services\Admin\Catalogos;

        use App\Repositories\Admin\Catalogos\AreasRepository;
        use App\Repositories\Admin\Catalogos\MaquinasCatalogoRepository;
        use App\Repositories\Admin\Catalogos\MaquinasRepository;

        class MaquinasService {

            protected MaquinasRepository         $maquinasRepository;
            protected AreasRepository            $areasRepository;
            protected MaquinasCatalogoRepository $maquinasCatalogoRepository;

            public function __construct(
                MaquinasRepository         $maquinasRepository,
                AreasRepository            $areasRepository,
                MaquinasCatalogoRepository $maquinasCatalogoRepository

            ) {
                $this->maquinasRepository         = $maquinasRepository;
                $this->areasRepository            = $areasRepository;
                $this->maquinasCatalogoRepository = $maquinasCatalogoRepository;
            }

            public function obtenerRecursosRegistroMaquina() {
                $areas            = $this->areasRepository->obtenerListaAreas();
                $maquinasCatalogo  = $this->maquinasCatalogoRepository->obtenerListaCatalogoMaquina();

                return response()->json(
                    [
                        'mensaje'  => 'Se obtuvo los recursos correctamente',
                        'recursos' => [
                            'listaareas'             => $areas,
                            'listacatalogomaquina'   => $maquinasCatalogo,
                        ]
                    ]
                );
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

            public function obtenerDetalleMaquina(int $pkMaquina) {
                $maquinas = $this->maquinasRepository->obtenerDetalleMaquina($pkMaquina);

                return response()->json(
                    [
                        'maquinas' => $maquinas,
                        'mensaje'  => 'Se obtuvo la información correcta'
                    ]
                );
            }

            public function actualizarMaquina(array $maquina) {
                $this->maquinasRepository->actualizarMaquina($maquina['pkMaquina'], $maquina['maquina']);

                return response()->json(
                    [
                        'title'   => 'Actualización exitosa', 
                        'mensaje' => 'Se actualizo correctamente la maquina'
                    ]
                );
            }

            public function cambiarStatusMaquina(int $pkMaquina) {

                $status = $this->maquinasRepository->cambiarStatusMaquina($pkMaquina);

                return response()->json(
                    [
                        'title'   => ($status ? 'Activar' : 'Inactivar'). ' maquina',
                        'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' el maquina con éxito'
                    ]
                );
            }

            public function obtenerMaquinasPorAreaYCategoria(int $pkArea, int $pkCatalogoMaquina) {
                $maquinas = $this->maquinasRepository->obtenerMaquinasPorAreaYCategoria($pkArea, $pkCatalogoMaquina);

                    return response()->json([
                        'maquinas' => $maquinas,
                        'mensaje'  => 'Se obtuvieron las máquinas filtradas correctamente'
                    ]);
            }   
        }
