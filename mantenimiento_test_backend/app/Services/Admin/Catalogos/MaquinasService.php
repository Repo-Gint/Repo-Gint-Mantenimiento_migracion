<?php

namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\AreasRepository;
use App\Repositories\Admin\Catalogos\MaquinasCatalogoRepository;
use App\Repositories\Admin\Catalogos\MaquinasRepository;
use App\Models\TblMaquinas;
use Illuminate\Http\Request;

class MaquinasService {

    protected MaquinasRepository         $maquinasRepository;
    protected AreasRepository            $areasRepository;
    protected MaquinasCatalogoRepository $maquinasCatalogoRepository;

    public function __construct(
        MaquinasRepository         $maquinasRepository,
        AreasRepository            $areasRepository,
        MaquinasCatalogoRepository $maquinasCatalogoRepository
    ) {
        $this->maquinasRepository         = $maquinasRepository;
        $this->areasRepository            = $areasRepository;
        $this->maquinasCatalogoRepository = $maquinasCatalogoRepository;
    }

    public function obtenerRecursosRegistroMaquina() {
        $areas             = $this->areasRepository->obtenerListaAreas();
        $maquinasCatalogo  = $this->maquinasCatalogoRepository->obtenerListaCatalogoMaquina();

        return response()->json([
            'mensaje'  => 'Se obtuvo los recursos correctamente',
            'recursos' => [
                'listaareas'            => $areas,
                'listacatalogomaquina'  => $maquinasCatalogo,
            ]
        ]);
    }

    public function registrarMaquina(array $maquina) {
        $pkMaquina = $this->maquinasRepository->registrarMaquina($maquina);

        return response()->json([
            'pkMaquina' => $pkMaquina, 
            'mensaje'   => 'Se registró la máquina con éxito', 
            'title'     => 'Registro exitoso'
        ]);
    }

    public function obtenerListaMaquinas(Request $request = null) {
        $query = $this->maquinasRepository->obtenerListaMaquinas();

        if ($request) {
            $search        = $request->input('search');
            $idArea        = $request->input('id_area');
            $idCatMachine  = $request->input('id_cat_machines');
            $brand         = $request->input('brand');
            $active        = $request->input('active');

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('tbl_machines.machines', 'like', "%{$search}%")
                      ->orWhere('tbl_machines.brand', 'like',  "%{$search}%")
                      ->orWhere('tbl_machines.model', 'like',  "%{$search}%")
                      ->orWhere('tbl_machines.serial', 'like', "%{$search}%");
                });
            }

            if (!empty($idArea)) {
                $query->where('tbl_machines.id_area', $idArea);
            }

            if (!empty($idCatMachine)) {
                $query->where('tbl_machines.id_cat_machines', $idCatMachine);
            }

            if (!empty($brand)) {
                $query->where('tbl_machines.brand', $brand);
            }

            if (isset($active) && $active !== '') {
                $query->where('tbl_machines.active', $active);
            }
        }

        $maquinas = $query->get();

        // Obtener catálogos y recursos para los selectores de la interfaz
        $areasList       = $this->areasRepository->obtenerListaAreas();
        $catMachinesList = $this->maquinasCatalogoRepository->obtenerListaCatalogoMaquina();
        $marcasList      = TblMaquinas::select('brand')->distinct()->whereNotNull('brand')->pluck('brand');

        // Métricas reales para las tarjetas superiores
        $totalMaquinas   = TblMaquinas::count();
        $maquinasActivas = TblMaquinas::where('active', 1)->count();
        $demandaTotalKva = TblMaquinas::where('active', 1)->sum('kva');
        $pesoTotalKg     = TblMaquinas::where('active', 1)->sum('weight');

        return response()->json([
            'maquinas' => $maquinas,
            'filtros_recursos' => [
                'areas'        => $areasList,
                'cat_machines' => $catMachinesList,
                'marcas'       => $marcasList
            ],
            'metricas' => [
                'total_maquinaria' => $totalMaquinas,
                'activos'          => $maquinasActivas,
                'demanda_kva'      => $demandaTotalKva,
                'peso_kg'          => $pesoTotalKg
            ],
            'mensaje'  => 'Se obtuvo la información correctamente de máquinas'
        ]);
    }

    public function obtenerDetalleMaquina(int $pkMaquina) {
        $maquina = $this->maquinasRepository->obtenerDetalleMaquina($pkMaquina);

        return response()->json([
            'maquinas' => $maquina,
            'mensaje'  => 'Se obtuvo la información correcta'
        ]);
    }

    public function actualizarMaquina(array $maquinaData) {
        $this->maquinasRepository->actualizarMaquina($maquinaData['pkMaquina'], $maquinaData['maquina']);

        return response()->json([
            'title'   => 'Actualización exitosa', 
            'mensaje' => 'Se actualizó correctamente la máquina'
        ]);
    }

    public function cambiarStatusMaquina(int $pkMaquina) {
        $status = $this->maquinasRepository->cambiarStatusMaquina($pkMaquina);

        return response()->json([
            'title'   => ($status ? 'Activar' : 'Inactivar') . ' máquina',
            'mensaje' => 'Se ' . ($status ? 'activó' : 'inactivó') . ' la máquina con éxito'
        ]);
    }

    public function obtenerMaquinasPorAreaYCategoria(int $pkArea, int $pkCatalogoMaquina) {
        $maquinas = $this->maquinasRepository->obtenerMaquinasPorAreaYCategoria($pkArea, $pkCatalogoMaquina);

        return response()->json([
            'maquinas' => $maquinas,
            'mensaje'  => 'Se obtuvieron las máquinas filtradas correctamente'
        ]);
    }   
}