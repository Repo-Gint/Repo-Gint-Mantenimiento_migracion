<?php

namespace App\Services\Admin\Catalogos;
use App\Repositories\Admin\Catalogos\MaquinasCatalogoRepository;

class MaquinasCatalogoService
{
    protected MaquinasCatalogoRepository $maquinasCatalogoRepository;

    public function __construct(
        MaquinasCatalogoRepository $maquinasCatalogoRepository
    ) {
        $this->maquinasCatalogoRepository = $maquinasCatalogoRepository;
    }

    public function registrarCatalogoMaquina(array $maquinaCatalogo) {
        $pkCatalogoMaquina = $this->maquinasCatalogoRepository->registrarCatalogoMaquina($maquinaCatalogo);

        return response()->json(
            [
                'pkCatalogoMaquina' => $pkCatalogoMaquina,
                'mensaje'           => 'Se registró la Categoría de la Máquina con éxito',
                'title'             => 'Registro exitoso'
            ]
        );
    }

    public function obtenerListaCatalogoMaquina() {
        $maquinaCatalogos = $this->maquinasCatalogoRepository->obtenerListaCatalogoMaquina();

        return response()->json(
            [
                'maquinaCatalogos' => $maquinaCatalogos,
                'mensaje'          => 'Se obtuvo la información correctamente de la Categoría de la Máquina'
            ]
        );
    }
    
    public function obtenerDetalleCatalogoMaquina(int $pkCatalogoMaquina) {
        $maquinaCatalogos = $this->maquinasCatalogoRepository->obtenerDetalleCatalogoMaquina($pkCatalogoMaquina);

        return response()->json(
            [
                'catmaquinas' =>  $maquinaCatalogos[0],
                'mensaje'     => 'Se obtuvo el detalle de la Categoría de la Máquina'
            ]
        );
    }

    public function actualizarMaquina(array $maquinaCatalogo) {
        $this->maquinasCatalogoRepository->actualizarCatalogoMaquina($maquinaCatalogo['pkCatalogoMaquina'], $maquinaCatalogo['catmaquina']);
        return response()->json(
            [
                'title'   => 'Actualización exitosa',
                'mensaje' => 'Se actualizó correctamente el Catálogo de la Máquina'
            ]
        );
    }
}