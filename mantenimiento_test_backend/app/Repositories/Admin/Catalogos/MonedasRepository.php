<?php

        namespace App\Repositories\Admin\Catalogos;

        use App\Models\CatMonedas;
        use Illuminate\Support\Facades\DB;

        class MonedasRepository {

            public function registrarMoneda(array $moneda) {
                $registro = new CatMonedas(); 
                $registro->coin   = $moneda['coin'];
                $registro->active = 1;
                $registro->save();

                return $registro->id_coins;
            }

            public function obtenerListaMonedas()
            {
                $query = CatMonedas::select(
                'id_coins',
                'coin',
                'active',
                DB::raw("
                                        CASE
                                            WHEN active = 1 THEN 'Activo'
                                            ELSE 'Inactivo'
                                            END AS estado
                ")
            );

                return $query->get();
            }

            public function obtenerDetalleMoneda(int $pkMoneda) {
                $query = CatMonedas::select(
                    'id_coins', 
                    'coin',
                    'active',
                )
                    ->where([
                        ['id_coins', $pkMoneda], 
                        ['active', 1]
                ]);

                return $query->get();
            }

            public function actualizarMoneda(int $id, array $moneda) {
                $actualizar = CatMonedas::findOrFail($id);

                $actualizar->coin   = $moneda['coin'];
                $actualizar->save();
            }

            public function cambiarStatusMoneda(int $pkMoneda) {
                $monedas          =CatMonedas::findOrFail($pkMoneda);
                $monedas->active  = $monedas->active ? 0 :1;
                $monedas->save();

				return $monedas->active;
            }
        }
