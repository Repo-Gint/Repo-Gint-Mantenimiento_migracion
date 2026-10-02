<?php

namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\TipoMantenimientoRepository;
use Illuminate\Http\JsonResponse;

class TipoMantenimientoService
{
    protected TipoMantenimientoRepository $tipoMantenimientoRepository;

    public function __construct(TipoMantenimientoRepository $tipoMantenimientoRepository)
    {
        $this->tipoMantenimientoRepository = $tipoMantenimientoRepository;
    }

    public function registrarTipoMantenimiento(array $tipoMantenimiento): JsonResponse
    {
        $pktipoMantenimiento = $this->tipoMantenimientoRepository->registrarTipoMantenimiento($tipoMantenimiento);

        return response()->json([
            'pktipoMantenimiento' => $pktipoMantenimiento,
            'mensaje'             => 'Se registró el Tipo de Mantenimiento con éxito',
            'title'               => 'Registro exitoso'
        ]);
    }

    public function obtenerListaMantenimientos(array $filtros = []): JsonResponse
    {
        $search = $filtros['search'] ?? null;
        $estado = $filtros['estado'] ?? null;

        $tipoMantenimientos = $this->tipoMantenimientoRepository->obtenerListatipoMantenimientos($search, $estado);

        $totalCategorias = $tipoMantenimientos->count();
        $totalActivos    = $tipoMantenimientos->where('active', 1)->count();
        $porcentajeNorma = $totalCategorias > 0 ? round(($totalActivos / $totalCategorias) * 100) : 0;

        $datosConDetalle = $tipoMantenimientos->map(function ($item) {
            return [
                'id_type_maintenances' => $item->id_type_maintenances,
                'type_maintenances'    => $item->type_maintenances,
                'description'          => $this->obtenerDescripcionDinamica($item->type_maintenances),
                'color'                => $item->color,
                'acronym'              => $item->acronym,
                'active'               => $item->active,
                'estado'               => $item->estado
            ];
        });

        return response()->json([
            'tipoMantenimientos' => $datosConDetalle,
            'kpis' => [
                'totalCategorias' => $totalCategorias,
                'porcentajeNorma' => $porcentajeNorma
            ],
            'mensaje' => 'Se obtuvo la información de Tipo Mantenimiento'
        ]);
    }

    private function obtenerDescripcionDinamica(string $nombre): string
    {
        $lower = strtolower($nombre);
        if (str_contains($lower, 'correctivo')) {
            return 'Mantenimiento por avería o paro imprevisto';
        } elseif (str_contains($lower, 'preventivo')) {
            return 'Planes cíclicos y rondas de inspección sistemática';
        }
        return 'Gestión y control normativo de planta';
    }

    public function obtenerDetalletipoMantenimiento(int $pktipoMantenimiento): JsonResponse
    {
        $tipoMantenimiento = $this->tipoMantenimientoRepository->obtenerDetalletipoMantenimiento($pktipoMantenimiento);

        return response()->json([
            'tipoMantenimiento' => $tipoMantenimiento[0] ?? null,
            'mensaje'           => 'Se obtuvo la información correctamente'
        ]);
    }

    public function actualizartipoMantenimiento(array $tipoMantenimiento): JsonResponse
    {
        $this->tipoMantenimientoRepository->actualizartipoMantenimiento(
            $tipoMantenimiento['pktipoMantenimiento'], 
            $tipoMantenimiento['tipoMantenimiento']
        );

        return response()->json([
            'title'   => 'Actualización exitosa',
            'mensaje' => 'Se actualizó correctamente el Tipo de Mantenimiento'
        ]);
    }

    public function cambiarStatustipoMantenimiento(int $pktipoMantenimiento): JsonResponse
    {
        $status = $this->tipoMantenimientoRepository->cambiarStatustipoMantenimiento($pktipoMantenimiento);

        return response()->json([
            'title'   => ($status ? 'Activar' : 'Inactivar') . ' mantenimiento',
            'mensaje' => 'Se ' . ($status ? 'activó' : 'inactivó') . ' el mantenimiento con éxito'
        ]);
    }
}