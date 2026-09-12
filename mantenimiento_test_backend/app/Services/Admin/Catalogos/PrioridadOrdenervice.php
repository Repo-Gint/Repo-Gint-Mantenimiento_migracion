<?php

namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\PrioridadOrdenRepository;

class PrioridadOrdenService
{
    protected PrioridadOrdenRepository $prioridadOrdenRepository;

    public function __construct(
        PrioridadOrdenRepository $prioridadOrdenRepository)
    {
        $this->prioridadOrdenRepository = $prioridadOrdenRepository;
    }

    public function registrarPrioridadOrden (array $prioridadOrden) {
        $pkPrioridadOrden = $this->prioridadOrdenRepository->registrarPrioridadOrden($prioridadOrden);

        return response()->json(
            [
                'pkPrioridadOrden' => $pkPrioridadOrden,
                'mensaje'          => 'Se registro la Prioridad de Orde con éxito',
                'title'            => 'Registro exitoso'
            ]
        );
    }

    public function obtenerListaPrioridadOrden () {
        $prioridadOrden = $this->prioridadOrdenRepository->obtenerListaPrioridadOrden();

        return response()->json(
            [
                'prioridadOrden' => $prioridadOrden,
                'mensaje'        => 'Se obtuvo la información de la prioridad de orden'
            ]
        );
    }

    public function obtenerDetallePrioridadOrden (array $pkPrioridadOrden) {
        $prioridadOrdenes = $this->prioridadOrdenRepository->obtenerDetallePrioridadOrden($pkPrioridadOrden);

        return response()->json(
            [
                'prioridadOrdenes' => $prioridadOrdenes[0],
                'mensaje'          => 'Se obtuvo el detalle de la Prioridad Orden correcta'
            ]
        );
    }

    public function actualizarPrioridadOrden (array $prioridadOrden) {
        $this->prioridadOrdenRepository->actualizarPrioridadOrden($prioridadOrden['pkprioridadOrden'], $prioridadOrden['']);

        return response()->json(
            [
                'title' => 'Actualización exitosa',
                'mensaje' => 'Se actualizo correctamente la prioridad de orden'
            ]
        );
    }
}