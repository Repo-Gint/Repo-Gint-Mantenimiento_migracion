<?php

use App\Http\Controllers\Admin\Catalogos\AreasController;
use App\Http\Controllers\Admin\Catalogos\DepartamentsController;
use App\Http\Controllers\Admin\Catalogos\EmpleadosController;
use App\Http\Controllers\Admin\Catalogos\MaquinasCatalogoController;
use App\Http\Controllers\Admin\Catalogos\MaquinasController;
use App\Http\Controllers\Admin\Catalogos\MonedasController;
use App\Http\Controllers\Admin\Catalogos\PrioridadOrdenController;
use App\Http\Controllers\Admin\Catalogos\RolesController;
use App\Http\Controllers\Admin\Catalogos\TipoMantenimientoController;
use App\Http\Controllers\Admin\Catalogos\TipoOrdenController;
use App\Http\Controllers\Admin\Ordenes\OrdenesController;
use App\Http\Controllers\Auth\Usuarios\UsuariosController;
use Illuminate\Support\Facades\Route;

//Usuarios
Route::post('/usuarios/login', [UsuariosController::class, 'login']);

Route::post('/usuarios/registrarUsuario',                                             [UsuariosController::class, 'registrarUsuario']);
Route::post('/usuarios/cerrarSesion',                                                 [UsuariosController::class, 'cerrarSesion']);
Route::get('/usuarios/obtenerListaGeneralUsuarios',                                   [UsuariosController::class, 'obtenerListaGeneralUsuarios']);
Route::get('/usuarios/obtenerDetalleUsuario/{pkUsuario}',                             [UsuariosController::class, 'obtenerDetalleUsuario']);
Route::get('/usuarios/obtenerRecursosRegistroUsuario',                                [UsuariosController::class, 'obtenerRecursosRegistroUsuario']);
Route::put('/usuarios/actualizarUsuario',                                             [UsuariosController::class, 'actualizarUsuario']);
Route::get('/usuarios/cambiarStatusUsuario/{id}',                                     [UsuariosController::class, 'cambiarStatusUsuario']);
        

//Ordenes
Route::get('/ordenes/obtenerRecursosRegistroOrden',                                   [OrdenesController::class, 'obtenerRecursosRegistroOrden']); 
Route::post('/ordenes/registrarOrden',                                                [OrdenesController::class, 'registrarOrden']);
Route::post('/ordenes/ObtenerListaGeneralOrdenes',                                    [OrdenesController::class, 'ObtenerListaGeneralOrdenes']); 
Route::get('/ordenes/obtenerDetalleOrden/{pkOrden}',                                  [OrdenesController::class, 'obtenerDetalleOrden']);
Route::post('/ordenes/asignarOrden',                                                  [OrdenesController::class, 'asignarOrden']);
Route::post('/ordenes/actualizarOrden',                                               [OrdenesController::class, 'actualizarOrden']);
Route::get('/ordenes/obtenerStatusOrdenes',                                           [OrdenesController::class, 'obtenerStatusOrdenes']);
Route::delete('/ordenes/eliminarEvidenciaOrden/{id_eviden_order}',                    [OrdenesController::class, 'eliminarEvidenciaOrden']);
Route::get('/ordenes/obtenerMaquinasPorAreaYCategoria/{pkArea}/{pkCatalogoMaquina}',  [OrdenesController::class, 'obtenerMaquinasPorAreaYCategoria']);
Route::get('ordenes/cancelarOrden/{id}',                                              [OrdenesController::class, 'cancelarOrden']);
Route::get('/ordenes/obtenerUsuariosAsignacion/{pkOrden}',                            [OrdenesController::class, 'obtenerUsuariosAsignacion']);

//Areas           
Route::post('/areas/registrarArea',                                                   [AreasController::class, 'registrarArea']);
Route::get('/areas/obtenerListaAreas',                                                [AreasController::class, 'obtenerListaAreas']);
Route::get('/areas/obtenerDetalleArea/{pkArea}',                                      [AreasController::class, 'obtenerDetalleArea']);
Route::put('/areas/actualizarArea',                                                   [AreasController::class, 'actualizarArea']);
Route::get('/areas/cambiarStatusArea/{id}',                                           [AreasController::class, 'cambiarStatusArea']);
        
//Departaments            
Route::post('/departamentos/registrarDepartamento',                                   [DepartamentsController::class, 'registrarDepartamento']);
Route::get('/departamentos/obtenerListaDepartamentos',                                [DepartamentsController::class, 'obtenerListaDepartamentos']);
Route::get('/departamentos/obtenerDetalleDepartamento/{pkDepartamento}',              [DepartamentsController::class, 'obtenerDetalleDepartamento']);
Route::put('/departamentos/actualizarDepartamento',                                   [DepartamentsController::class, 'actualizarDepartamento']);
Route::get('/departamentos/cambiarStatusDepartamento/{id}',                           [DepartamentsController::class, 'cambiarStatusDepartamento']);
        
//Maquinas            
Route::post('/maquinas/registrarMaquina',                                             [MaquinasController::class, 'registrarMaquina']);
Route::get('/maquinas/obtenerListaMaquinas',                                          [MaquinasController::class, 'obtenerListaMaquinas']);
Route::get('/maquinas/obtenerDetalleMaquina/{pkMaquina}',                             [MaquinasController::class, 'obtenerDetalleMaquina']);
Route::get('/maquinas/obtenerRecursosRegistroMaquina',                                [MaquinasController::class, 'obtenerRecursosRegistroMaquina']);
Route::put('/maquinas/actualizarMaquina',                                             [MaquinasController::class, 'actualizarMaquina']);
Route::get('/maquinas/cambiarStatusMaquina/{id}',                                     [MaquinasController::class, 'cambiarStatusMaquina']);
Route::get('/maquinas/obtenerMaquinasPorAreaYCategoria',                              [MaquinasController::class, 'obtenerMaquinasPorAreaYCategoria']);

//Catalogo de Maquinas
Route::post('/catalogosmaquinas/registrarCatalogoMaquina',                            [MaquinasCatalogoController::class, 'registrarCatalogoMaquina']);
Route::get('/catalogosmaquinas/obtenerListaCatalogoMaquina',                           [MaquinasCatalogoController::class, 'obtenerListaCatalogoMaquina']);
Route::get('/catalogosmaquinas/obtenerDetalleCatalogoMaquina/{pkCatalogoMaquina}',    [MaquinasCatalogoController::class, 'obtenerDetalleCatalogoMaquina']);
Route::put('/catalogosmaquinas/actualizarCatalogoMaquina',                            [MaquinasCatalogoController::class, 'actualizarCatalogoMaquina']);

//Mondeas             
Route::post('/monedas/registrarMoneda',                                               [MonedasController::class,  'registrarMoneda']);
Route::get('/monedas/obtenerListaMonedas',                                            [MonedasController::class,  'obtenerListaMonedas']);
Route::get('/monedas/obtenerDetalleMoneda/{pkMoneda}',                                [MonedasController::class,  'obtenerDetalleMoneda']);                            
Route::put('/monedas/actualizarMoneda',                                               [MonedasController::class,  'actualizarMoneda']);
Route::get('/monedas/cambiarStatusMoneda/{id}',                                       [MonedasController::class,  'cambiarStatusMoneda']);
        
//Tipo Orden              
Route::post('/tipoOrdenes/registrarTipoOrden',                                        [TipoOrdenController::class, 'registrarTipoOrden']);
Route::get('/tipoOrdenes/obtenerListaTipoOrden',                                      [TipoOrdenController::class, 'obtenerListaTipoOrden']);
Route::get('/tipoOrdenes/obtenerDetalleTipoOrden/{pktipoOrden}',                      [TipoOrdenController::class, 'obtenerDetalleTipoOrden']);
Route::put('/tipoOrdenes/actualizarTipoOrden',                                        [TipoOrdenController::class, 'actualizarTipoOrden']);
Route::get('/tipoOrdenes/cambiarStatusTipoOrden/{id}',                                [TipoOrdenController::class, 'cambiarStatusTipoOrden']);                 

//Prioridad Oreden
Route::post('/prioridadOrden/registrarPrioridadOrden',                                [PrioridadOrdenController::class, 'registrarPrioridadOrden']);
Route::get('/prioridadOrden/obtenerListaPrioridadOrden',                              [PrioridadOrdenController::class, 'obtenerListaPrioridadOrden']);
Route::get('/prioridadOrden/obtenerDetallePrioridadOrden/{pkPrioridadOrden}',         [PrioridadOrdenController::class, 'obtenerPrioridadOrden']);
Route::put('/prioridadOrden/actualizarPrioridadOrden',                                [PrioridadOrdenController::class, 'actualizarPrioridadOrden']);

//Tipo Mantenimiento
Route::post('/tipoMantenimiento/registrarTipoMantenimiento',                          [TipoMantenimientoController::class, 'registrarTipoMantenimiento']);
Route::get('/tipoMantenimiento/obtenerListatipoMantenimientos',                       [TipoMantenimientoController::class, 'obtenerListatipoMantenimientos']);
Route::get('/tipoMantenimiento/obtenerDetalletipoMantenimiento/{pktipoMantenimiento}',[TipoMantenimientoController::class, 'obtenerDetalletipoMantenimiento']);
Route::put('/tipoMantenimiento/actualizartipoMantenimiento',                          [TipoMantenimientoController::class, 'actualizartipoMantenimiento']);
Route::get('/tipoMantenimiento/cambiarStatustipoMantenimiento/{id}',                  [TipoMantenimientoController::class, 'cambiarStatustipoMantenimiento']);

//Empleados     
Route::post('/empleados/registrarEmpleado',                                           [EmpleadosController::class, 'registrarEmpleado']);
Route::get('/empleados/obtenerListaEmpleados',                                        [EmpleadosController::class, 'obtenerListaEmpleados']);
Route::get('/empleados/obtenerDetalleEmpleado/{pkEmpleado}',                          [EmpleadosController::class, 'obtenerDetalleEmpleado']);
Route::put('/empleados/actualizarEmpleado',                                           [EmpleadosController::class, 'actualizarEmpleado']);
Route::get('/empleados/cambiarStatusMaquina/{id}',                                    [EmpleadosController::class, 'cambiarStatusMaquina']);

//Roles
Route::post('/roles/registrarRoles',    [RolesController::class, 'registrarRoles']);
Route::get('/roles/obtenerListaRoles',  [RolesController::class, 'obtenerListaRoles']);
