<?php

namespace App\Services\Admin\Catalogos;
use App\Repositories\Admin\Catalogos\TipoMantenimientoRepository;

class TipoMantenimientoService
{
    protected TipoMantenimientoRepository $tipoMantenimientoRepository;

    public function __construct(
        TipoMantenimientoRepository $tipoMantenimientoRepository
    ) {
        $this->tipoMantenimientoRepository = $tipoMantenimientoRepository;
    }

    public function registrarTipoMantenimiento(array $tipoMantenimiento) {

    $pktipoMantenimiento = $this->tipoMantenimientoRepository->registrarTipoMantenimiento($tipoMantenimiento);

    return response()->json(
        [
            'pktipoMantenimiento' => $pktipoMantenimiento,
            'mensaje'             => 'Se registro el Tipo de Mantenimiento con éxito',
            'title'               => 'Registro exitoso'
        ]
    );
}

    public function obtenerListaMantenimientos() {
        $tipoMantenimientos = $this->tipoMantenimientoRepository->obtenerListatipoMantenimientos();

        return response()->json(
            [
                'tipoMantenimientos' => $tipoMantenimientos, 
                'mensaje'            => 'Se obtuvo la información de Tipo Mantenimiento' 
            ]
        );
    }

    public function obtenerDetalletipoMantenimiento(int $pktipoMantenimiento) {
        $tipoMantenimiento = $this->tipoMantenimientoRepository->obtenerDetalletipoMantenimiento($pktipoMantenimiento);

        return response()->json(
            [
                'tipoMantenimiento' => $tipoMantenimiento[0],
                'mensaje'           => 'Se obtuvo la información con correcta'
            ]
        );
    }

    public function actualizartipoMantenimiento(array $tipoMantenimiento) {
        $this->tipoMantenimientoRepository->actualizartipoMantenimiento($tipoMantenimiento['pktipoMantenimiento'], $tipoMantenimiento['tipoMantenimiento']);

        return response()->json([
            'title'   => 'Actualización exitosa',
            'mensaje' => 'Se actualizo correctamente el Tipo de Mantenimiento'
        ]);
    }

    public function cambiarStatustipoMantenimiento(int $pktipoMantenimiento) {
                $status = $this->tipoMantenimientoRepository->cambiarStatustipoMantenimiento($pktipoMantenimiento);

                return response()->json(
                    [
                        'title'   => ($status ? 'Activar' : 'Inactivar'). ' mantenimiento',
                        'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' el mantenimiento con éxito'
                    ]
                );
            }
}