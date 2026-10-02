<?php

namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\MaquinasCatalogoRepository;
use Illuminate\Http\Request;

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

        return response()->json([
            'pkCatalogoMaquina' => $pkCatalogoMaquina,
            'mensaje'           => 'Se registró la Categoría de la Máquina con éxito',
            'title'             => 'Registro exitoso'
        ]);
    }

    public function obtenerListaCatalogoMaquina(Request $request = null) {
        $query = $this->maquinasCatalogoRepository->obtenerListaCatalogoQuery();

        // Lógica de búsqueda y filtrado en el Service por Backend
        if ($request) {
            $search = $request->input('search');
            $active = $request->input('active');

            if (!empty($search)) {
                $query->where('cat_machines', 'like', "%{$search}%");
            }

            if (isset($active) && $active !== '') {
                $query->where('active', $active);
            }
        }

        $maquinaCatalogos = $query->get();

        return response()->json([
            'maquinaCatalogos' => $maquinaCatalogos,
            'mensaje'          => 'Se obtuvo la información correctamente de la Categoría de la Máquina'
        ]);
    }
    
    public function obtenerDetalleCatalogoMaquina(int $pkCatalogoMaquina) {
        $catalogo = $this->maquinasCatalogoRepository->obtenerDetalleCatalogoMaquina($pkCatalogoMaquina);

        return response()->json([
            'catmaquinas' => $catalogo,
            'mensaje'     => 'Se obtuvo el detalle de la Categoría de la Máquina'
        ]);
    }

    public function actualizarMaquina(array $maquinaCatalogo) {
        $this->maquinasCatalogoRepository->actualizarCatalogoMaquina($maquinaCatalogo['pkCatalogoMaquina'], $maquinaCatalogo['catmaquina']);
        
        return response()->json([
            'title'   => 'Actualización exitosa',
            'mensaje' => 'Se actualizó correctamente el Catálogo de la Máquina'
        ]);
    }

    public function cambiarStatusCatalogoMaquina(int $pkCatalogoMaquina) {
        $status = $this->maquinasCatalogoRepository->cambiarStatusCatalogoMaquina($pkCatalogoMaquina);

        return response()->json([
            'title'   => ($status ? 'Activar' : 'Inactivar') . ' catálogo',
            'mensaje' => 'Se ' . ($status ? 'activó' : 'inactivó') . ' el catálogo de máquina con éxito'
        ]);
    }
}