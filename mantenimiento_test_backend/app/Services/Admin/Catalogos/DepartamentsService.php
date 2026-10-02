<?php

namespace App\Services\Admin\Catalogos;

use App\Repositories\Admin\Catalogos\DepartamentosRepository;
use App\Models\CatDepartament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DepartamentsService {
    protected DepartamentosRepository $departamentosRepository;

    public function __construct(DepartamentosRepository $departamentosRepository) {
        $this->departamentosRepository = $departamentosRepository;
    }

    public function registrarDepartamento(array $departamentos) {
        $pkDepartamento = $this->departamentosRepository->registrarDepartamento($departamentos);

        return response()->json([
            'pkDepartamento' => $pkDepartamento,
            'mensaje' => 'Se registró el Departamento con éxito', 
            'title'   => 'Registro exitoso'
        ]);
    }

    public function obtenerListaDepartamentos() {
        $departamentos = $this->departamentosRepository->obtenerListaDepartamentos();
        $totalDepartamentos = CatDepartament::count();
        $activosNave        = CatDepartament::where('Active', 1)->count();

        $otsAsignadas = 0;
        try {
            if (Schema::hasTable('tbl_orders')) {
                $otsAsignadas = DB::table('tbl_orders')->count();
            } elseif (Schema::hasTable('tblorders')) {
                $otsAsignadas = DB::table('tblorders')->count();
            } elseif (Schema::hasTable('orders')) {
                $otsAsignadas = DB::table('orders')->count();
            }
        } catch (\Exception $e) {
            $otsAsignadas = 0;
        }

        $totalColaboradores = 0;
        try {
            if (Schema::hasTable('tbl_users')) {
                $totalColaboradores = DB::table('tbl_users')->count();
            }
        } catch (\Exception $e) {
            $totalColaboradores = 0;
        }

        return response()->json([
            'departaments' => $departamentos,
            'metricas'     => [
                'total_departamentos' => $totalDepartamentos,
                'activos_nave'        => $activosNave,
                'ots_asignadas'       => $otsAsignadas,
                'colaboradores_total' => $totalColaboradores
            ],
            'mensaje'      => 'Se obtuvo la información correctamente'
        ]);
    }

    public function obtenerDetalleDepartamento(int $pkDepartamento) {
        $departamento = $this->departamentosRepository->obtenerDetalleDepartamento($pkDepartamento);

        return response()->json([
            'departamento' => $departamento[0],
            'mensaje'      => 'Se obtuvo la información correcta'
        ]);
    }

    public function actualizarDepartamento(array $departamento) {
        $this->departamentosRepository->actualizarDepartamento($departamento['pkDepartamento'], $departamento['departamento']);

        return response()->json([
            'title'   => 'Actualización exitosa', 
            'mensaje' => 'Se actualizó correctamente el departamento'
        ]);
    }

    public function cambiarStatusDepartamento(int $pkDepartamento) {
        $status = $this->departamentosRepository->cambiarStatusDepartamento($pkDepartamento);

        return response()->json([
            'title'   => ($status ? 'Activar' : 'Inactivar') . ' departamento',
            'mensaje' => 'Se ' . ($status ? 'activó' : 'inactivó') . ' el departamento con éxito'
        ]);
    }
}