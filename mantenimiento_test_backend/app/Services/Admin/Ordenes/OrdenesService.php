<?php

namespace App\Services\Admin\Ordenes;

use App\Repositories\Admin\Catalogos\AreasRepository;
use App\Repositories\Admin\Catalogos\DepartamentosRepository;
use App\Repositories\Admin\Catalogos\EmpleadosRepository;
use App\Repositories\Admin\Catalogos\MaquinasCatalogoRepository;
use App\Repositories\Admin\Catalogos\MaquinasRepository;
use App\Repositories\Admin\Catalogos\PrioridadOrdenRepository;
use App\Repositories\Admin\Catalogos\TipoMantenimientoRepository;
use App\Repositories\Admin\Catalogos\TipoOrdenRepository;
use App\Repositories\Admin\Ordenes\OrdenesRepository;
use Illuminate\Support\Facades\DB;

class OrdenesService
{
    protected OrdenesRepository           $ordenesRepository;
    protected AreasRepository             $areasRepository;
    protected DepartamentosRepository     $departamentosRepository;
    protected EmpleadosRepository         $empleadosRepository;
    protected MaquinasRepository          $maquinasRepository;
    protected TipoMantenimientoRepository $tipoMantenimientoRepository;
    protected TipoOrdenRepository         $tipoOrdenRepository;
    protected PrioridadOrdenRepository    $prioridadOrdenRepository;
    protected MaquinasCatalogoRepository  $maquinasCatalogoRepository;

    public function __construct(
        OrdenesRepository           $OrdenesRepository,
        AreasRepository             $AreasRepository,
        DepartamentosRepository     $DepartamentosRepository,
        EmpleadosRepository         $EmpleadosRepository,
        MaquinasRepository          $MaquinasRepository,
        TipoMantenimientoRepository $TipoMantenimientoRepository,
        TipoOrdenRepository         $TipoOrdenRepository,
        PrioridadOrdenRepository    $PrioridadOrdenRepository,
        MaquinasCatalogoRepository  $MaquinasCatalogoRepository,

    ) {
        $this->ordenesRepository           = $OrdenesRepository;
        $this->areasRepository             = $AreasRepository;
        $this->departamentosRepository     = $DepartamentosRepository;
        $this->empleadosRepository         = $EmpleadosRepository;
        $this->maquinasRepository          = $MaquinasRepository;
        $this->tipoMantenimientoRepository = $TipoMantenimientoRepository;
        $this->tipoOrdenRepository         = $TipoOrdenRepository;
        $this->prioridadOrdenRepository    = $PrioridadOrdenRepository;
        $this->maquinasCatalogoRepository  = $MaquinasCatalogoRepository;
    }

    public function obtenerRecursosRegistroOrden() {
        $areas             = $this->areasRepository->obtenerListaAreas();
        $maquinas          = $this->maquinasRepository->obtenerListaMaquinas();
        $departamentos     = $this->departamentosRepository->obtenerListaDepartamentos();
        $empleados         = $this->empleadosRepository->obtenerListaEmpleados();
        $tipoMantenimiento = $this->tipoMantenimientoRepository->obtenerListatipoMantenimientos();
        $tipoOrden         = $this->tipoOrdenRepository->obtenerListaTipoOrden();
        $prioridadOrden    = $this->prioridadOrdenRepository->obtenerListaPrioridadOrden();
        $maquinasCatalogo  = $this->maquinasCatalogoRepository->obtenerListaCatalogoMaquina();

        return response()->json(
            [
                'mensaje' => 'Se obtuvo los recursos correctamente',
                'recursos' => [
                    'listaareas'             => $areas,
                    'listamaquinas'          => $maquinas,
                    'listadepartamentos'     => $departamentos,
                    'listaempleados'         => $empleados,
                    'listatipomantenimiento' => $tipoMantenimiento,
                    'listatipoorden'         => $tipoOrden,
                    'listaprioridadorden'    => $prioridadOrden,
                    'listacatalogomaquina'   => $maquinasCatalogo,
                ]
            ]
        );
    }

    public function registrarOrden(array $orden, $files) {
        $this->ordenesRepository->registrarOrdenes($orden, $files);

        return response()->json(
            [
                'mensaje' => 'Se registro exitosamente con éxito',
                'title'   => 'Registro exitoso'
            ]
        );
    }

    public function cancelarOrden(int $id_order) {
        $ordenes = $this->ordenesRepository->cancelarOrden($id_order);

        return response(
            [
                'ordenes' => $ordenes,
                'mensaje' => 'Se ha cancelado la orden con exito'
            ]
        );
    }

    public function obtenerStatusOrdenes() {
        $ordenes = $this->ordenesRepository->obtenerStatusOrdenes();

        return response()->json(
            [
                'ordenes' => $ordenes,
                'mensaje' => 'Se obtuvo la información de ordenes'
            ]
        );
    }

    public function ObtenerListaGeneralOrdenes(int $pkArea, int $pkStatus) {
        $ordenes = $this->ordenesRepository->ObtenerListaGeneralOrdenes($pkArea, $pkStatus);

        return response()->json(
            [
                'ordenes' => $ordenes,
                'mensaje' => 'Se obtuvo la información de ordenes'
            ]);
    }

    public function obtenerDetalleOrden(int $pkOrden) {
        $orden              = $this->ordenesRepository->obtenerDetalleOrden($pkOrden);
        $usuariosAsignados  = $this->ordenesRepository->obtenerUsuariosAsignadosOrden($pkOrden);
        $evidencias         = $this->ordenesRepository->obtenerEvidenciaOrden($pkOrden);

        return response()->json(
            [
                'orden'             => $orden,
                'usuariosAsignados' => $usuariosAsignados,
                'evidencias'        => $evidencias,
                'mensaje'           => 'Se obtuvo la información correcta'
            ]
        );
    }


    public function eliminarEvidenciaOrden(int $id) {
        $evidencia = $this->ordenesRepository->eliminarEvidenciaOrden($id);

        return response()->json(
            [
                'evidencia' => $evidencia,
                'mensaje'    => 'Evidencia eliminada correctamente'
            ]
        );
    }

    public function actualizarOrden(array $orden)
    {
        $id = $orden['pkOrden'];

        $this->ordenesRepository->actualizarOrden($id, $orden);

        if (request()->hasFile('images')) {
            $rutas = [];

            foreach (request()->file('images') as $file) {
                $ruta = $file->store('ordenes', 'public');
                $rutas[] = $ruta;
            }

            $this->ordenesRepository->actualizarEvidencias($id, $rutas);
        }

        return response()->json([
            'title'    => 'Actualización exitosa',
            'mensajes' => 'Se actualizó correctamente el orden',
            'pkOrden' => $id
        ]);
    }


    public function asignarOrden(int $pkOrden, array $idUsuario) {
        DB::beginTransaction();

        if (!$pkOrden || !is_array($idUsuario)) {
            return response()->json(
                [
                    'mensaje' => 'Datos invalidos'
                ], 400 );
        }

        if (empty($idUsuario)) {
            return response()->json(
                [
                    'mensaje' => 'Debes asignar al menos un usuario'
                ], 400
            );
        }

        $this->ordenesRepository->depurarAsignacionesOrden($pkOrden);

        foreach ($idUsuario as $usuario) {
            $orden = [
                'id_order' => $pkOrden,
                'id_users' => $usuario
            ];

            $this->ordenesRepository->asignarOrden($orden);
        }

        DB::commit();
        return response()->json(
            [
                'mensaje' => 'Usuarios asignados correctamente'
            ]);
    }

    public function obtenerUsuariosAsignacion(int $pkOrden)
    {
        $usuarios = $this->ordenesRepository->obtenerUsuariosAsignacion();
        $asignados = $this->ordenesRepository->obtenerIdsUsuariosAsignadosOrden($pkOrden);

        $usuarios = $usuarios->map(function ($value) use ($asignados) {
            return [
                'value' => $value->id_users,
                'label' => $value->Name,
                'checked' => $asignados->contains($value->id_users)
            ];
        });

        return response()->json([
            'usuarios' => $usuarios
        ]);
    }


    public function obtenerMaquinasFiltradasPorAreaYCat(int $pkArea, int $pkCatalogoMaquina) {

        $maquinas = $this->maquinasRepository->obtenerMaquinasPorAreaYCategoria($pkArea, $pkCatalogoMaquina);

        return response()->json([
            'maquinas' => $maquinas,
            'mensaje'  => 'Se obtuvieron las máquinas filtradas correctamente'
        ]);
    }
}