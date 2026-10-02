<?php

namespace App\Repositories\Admin\Catalogos;

use App\Models\CatTipoMantenimiento;
use Illuminate\Support\Facades\DB;

class TipoMantenimientoRepository
{
    public function registrarTipoMantenimiento(array $tipoMantenimiento): int
    {
        $registro = new CatTipoMantenimiento();
        $registro->type_maintenances = $tipoMantenimiento['type_maintenances'];
        $registro->color             = $tipoMantenimiento['color'];
        $registro->acronym           = $tipoMantenimiento['acronym'];
        $registro->active            = 1;
        $registro->save();

        return $registro->id_type_maintenances;
    }

    public function obtenerListatipoMantenimientos(?string $search = null, ?string $estado = null)
    {
        $query = CatTipoMantenimiento::select(
            'id_type_maintenances',
            'type_maintenances',
            'color',
            'acronym',
            'active',
            DB::raw("
                CASE
                    WHEN active = 1 THEN 'Activo'
                    ELSE 'Inactivo'
                END as estado
            ")
        );

        // Búsqueda multi-columna desde BD
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('type_maintenances', 'LIKE', "%{$search}%")
                  ->orWhere('acronym', 'LIKE', "%{$search}%")
                  ->orWhere('color', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por Estado
        if ($estado === 'activos') {
            $query->where('active', 1);
        } elseif ($estado === 'inactivos') {
            $query->where('active', 0);
        }

        return $query->get();
    }

    public function obtenerDetalletipoMantenimiento(int $pktipoMantenimiento)
    {
        return CatTipoMantenimiento::select(
            'id_type_maintenances',
            'type_maintenances',
            'color',
            'acronym',
            'active'
        )
        ->where([
            ['id_type_maintenances', $pktipoMantenimiento],
            ['active', 1]
        ])
        ->get();
    }

    public function actualizartipoMantenimiento(int $id, array $tipoMantenimiento): void
    {
        $actualizar = CatTipoMantenimiento::findOrFail($id);
        $actualizar->type_maintenances = $tipoMantenimiento['type_maintenances'];
        $actualizar->color             = $tipoMantenimiento['color'];
        $actualizar->acronym           = $tipoMantenimiento['acronym'];
        $actualizar->save();
    }

    public function cambiarStatustipoMantenimiento(int $pktipoMantenimiento): int
    {
        $tipoMantenimiento = CatTipoMantenimiento::findOrFail($pktipoMantenimiento);
        $tipoMantenimiento->active = $tipoMantenimiento->active ? 0 : 1;
        $tipoMantenimiento->save();

        return $tipoMantenimiento->active;
    }
}