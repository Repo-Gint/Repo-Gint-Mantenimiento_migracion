<?php

namespace App\Services\Admin\Catalogos;
use App\Repositories\Admin\Catalogos\AreasRepository;

class AreasService {
    protected AreasRepository $areasRepository;

    public function __construct(
        AreasRepository $areasRepository
    ) {
        $this->areasRepository = $areasRepository;
    }

    public function registrarArea(array $areas)
    {
        $pkArea = $this->areasRepository->registrarArea($areas);

        return response()->json(
            [
                'pkArea'  => $pkArea,
                'mensaje' => 'Se registro el Area con éxito', 
                'title'   => 'Registro exitoso'
            ]
        );
    }

    public function obtenerListaAreas() {
        $areas = $this->areasRepository->obtenerListaAreas();

        return response()->json(
            [
                'areas'   => $areas, 
                'mensaje' => 'Se obtuvo la información correctamente de Areas'
            ]
        );
    }

    public function obtenerDetalleArea(int $pkArea) {
        $areas = $this->areasRepository->obtenerDetalleArea($pkArea); 

        return response()->json(
            [
                'area'    => $areas[0],
                'mensaje' => 'Se obtuvo la información correcta' 
            ]
        );
    }

    public function actualizarArea(array $area) {
        $this->areasRepository->actualizarArea($area['pkArea'], $area['area']);

        return response()->json(
            [
                'title'   => 'Actualización exitosa', 
                'mensaje' => 'Se actualizo correctamente el area'
            ]
        );
    }

    public function cambiarStatusArea(int $pkArea) {

        $status = $this->areasRepository->cambiarStatusArea($pkArea); 

        return response()-> json(
            [
                'title'   => ($status ? 'Activar' : 'Inactivar'). ' area',
                'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' el area con éxito'
            ]
        );
    }
}