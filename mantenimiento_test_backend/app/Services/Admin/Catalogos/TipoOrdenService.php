<?php

        namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\TipoOrdenRepository;

        class TipoOrdenService {
            protected TipoOrdenRepository $tipoOrdenRepository;

            public function __construct(
                TipoOrdenRepository $tipoOrdenRepository
            ) {
                $this->tipoOrdenRepository = $tipoOrdenRepository;
            }

            public function registrarTipoOrden(array $tipoOrden) {

            $pktipoOrden = $this->tipoOrdenRepository->registrarTipoOrden($tipoOrden);

            return response()->json(
                [
                    'pktipoOrden' => $pktipoOrden,
                    'mensaje'     => 'Se registro el Tipo de Orden con éxito', 
                    'title'       => 'Registro exitoso'
                ]
            );
        }

        public function obtenerListaTipoOrden() {
            $tipoOrdenes = $this->tipoOrdenRepository->obtenerListaTipoOrden();

            return response()->json(
                [
                    'tipoOrdenes' => $tipoOrdenes,
                    'mensaje'     => 'Se obtuvo la información de Tipo Orden'
                ]
            );
        }

        public function obtenerDetalleTipoOrden(int $pktipoOrden) {
            $tipoOrdenes = $this->tipoOrdenRepository->obtenerDetalleTipoOrden($pktipoOrden);

            return response()->json(
                [
                    'tipoOrdenes' => $tipoOrdenes[0],
                    'mensaje'     => 'Se obtuvo la información correcta'
                ]
            );
        }

        public function actualizarTipoOrden(array $tipoOrden) {
            $this->tipoOrdenRepository->actualizarTipoOrden($tipoOrden['pktipoOrden'], $tipoOrden['tipoOrden']);

            return response()->json(
                [
                    'title'   => 'Actualización exitosa', 
                    'mensaje' => 'Se actualizo correctamente el Tipo de Orden'
                ]
            );
        }

        public function cambiarStatusTipoOrden(int $pktipoOrden) {
            $status = $this->tipoOrdenRepository->cambiarStatusTipoOrden($pktipoOrden);

            return response()->json(
                [
                    'title'   => ($status ? 'Activar' : 'Inactivar'). ' tipo Orden',
                    'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' el tipo Orden con éxito'
                ]
            );
        }
            
    }
    