<?php

        namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\MonedasRepository;

        class MonedasService {
            protected MonedasRepository $monedasRepository;

            public function __construct(
                MonedasRepository $monedasRepository
            ) {
                $this->monedasRepository = $monedasRepository;
            }

            public function registrarMoneda(array $moneda) {
                $pkMoneda = $this->monedasRepository->registrarMoneda($moneda);

                return response()->json(
                    [
                        'pkMoneda' => $pkMoneda, 
                        'mensaje'  => 'Se registro la Moneda con éxito', 
                        'title'    => 'Registro exitoso'
                    ]
                );
            }

            public function obtenerListaMonedas() {
                $monedas = $this->monedasRepository->obtenerListaMonedas();

                return response()->json(
                    [
                        'monedas' => $monedas, 
                        'mensaje' => 'Se obtuvo la información de Monedas'
                    ]
                );
            }

            public function obtenerDetalleMoneda(int $pkMoneda) {
                $monedas = $this->monedasRepository->obtenerDetalleMoneda($pkMoneda); 

                return response()->json(
                    [
                        'monedas' => $monedas[0],
                        'mensaje' => 'Se obtuvo la información correcta'
                    ]
                );
            }

            public function actualizarMoneda(array $moneda) {
                $this->monedasRepository->actualizarMoneda($moneda['pkMoneda'], $moneda['moneda']);

                return response()->json(
                    [
                        'title'   => 'Actualización exitosa', 
                        'mensaje' => 'Se actualizo correctamente la moneda'
                    ]
                );
            }

            public function cambiarStatusMoneda(int $pkMoneda) {
                
                $status = $this->monedasRepository->cambiarStatusMoneda($pkMoneda);

                return response()->json(
                    [
                        'title'   => ($status ? 'Activar' : 'Inactivar'). ' moneda', 
                        'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' la moneda con éxito'
                    ]
                );
            }
            
        }
    