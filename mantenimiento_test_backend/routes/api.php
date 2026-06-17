<?php

use App\Http\Controllers\Admin\Catalogos\AreasController;
use App\Http\Controllers\Admin\Catalogos\DepartamentsController;
use App\Http\Controllers\Admin\Catalogos\MaquinasController;
use App\Http\Controllers\Admin\Catalogos\MonedasController;
use App\Http\Controllers\Admin\Catalogos\RolesController;
use App\Http\Controllers\Auth\Usuarios\UsuariosController;
use Illuminate\Support\Facades\Route;

//Usuarios
Route::post('/usuarios/login', [UsuariosController::class, 'login']);

Route::post('/usuarios/registrarUsuario',                                [UsuariosController::class, 'registrarUsuario']);
Route::post('/usuarios/cerrarSesion',                                    [UsuariosController::class, 'cerrarSesion']);
Route::get('/usuarios/obtenerListaGeneralUsuarios',                      [UsuariosController::class, 'obtenerListaGeneralUsuarios']);
Route::get('/usuarios/obtenerDetalleUsuario/{pkUsuario}',                [UsuariosController::class, 'obtenerDetalleUsuario']);
Route::get('/usuarios/obtenerRecursosRegistroUsuario',                   [UsuariosController::class, 'obtenerRecursosRegistroUsuario']);
Route::put('/usuarios/actualizarUsuario',                                [UsuariosController::class, 'actualizarUsuario']);
Route::get('/usuarios/cambiarStatusUsuario/{id}',                        [UsuariosController::class, 'cambiarStatusUsuario']);

//Areas
Route::post('/areas/registrarArea',                                      [AreasController::class, 'registrarArea']);
Route::get('/areas/obtenerListaAreas',                                   [AreasController::class, 'obtenerListaAreas']);
Route::get('/areas/obtenerDetalleArea/{pkArea}',                         [AreasController::class, 'obtenerDetalleArea']);
Route::put('/areas/actualizarArea',                                      [AreasController::class, 'actualizarArea']);
Route::get('/areas/cambiarStatusArea/{id}',                              [AreasController::class, 'cambiarStatusArea']);

//Departaments
Route::post('/departamentos/registrarDepartamento',                      [DepartamentsController::class, 'registrarDepartamento']);
Route::get('/departamentos/obtenerListaDepartamentos',                   [DepartamentsController::class, 'obtenerListaDepartamentos']);
Route::get('/departamentos/obtenerDetalleDepartamento/{pkDepartamento}', [DepartamentsController::class, 'obtenerDetalleDepartamento']);
Route::put('/departamentos/actualizarDepartamento',                      [DepartamentsController::class, 'actualizarDepartamento']);
Route::get('/departamentos/cambiarStatusDepartamento/{id}',              [DepartamentsController::class, 'cambiarStatusDepartamento']);

//Maquinas
Route::post('/maquinas/registrarMaquina',                                [MaquinasController::class, 'registrarMaquina']);
Route::get('/maquinas/obtenerListaMaquinas',                             [MaquinasController::class, 'obtenerListaMaquinas']);
Route::get('/maquinas/obtenerDetalleMaquina/{pkMaquina}',                [MaquinasController::class, 'obtenerDetalleMaquina']);
Route::put('/maquinas/actualizarMaquina',                                [MaquinasController::class, 'actualizarMaquina']);
Route::get('/maquinas/cambiarStatusMaquina/{id}',                        [MaquinasController::class, 'cambiarStatusMaquina']);

//Mondeas 
Route::post('/monedas/registrarMoneda',                                  [MonedasController::class,  'registrarMoneda']);
Route::get('/monedas/obtenerListaMonedas',                               [MonedasController::class,  'obtenerListaMonedas']);
Route::get('/monedas/obtenerDetalleMoneda/{pkMoneda}',                   [MonedasController::class,  'obtenerDetalleMoneda']);                            
Route::put('/monedas/actualizarMoneda',                                  [MonedasController::class,  'actualizarMoneda']);
Route::get('/monedas/cambiarStatusMoneda/{id}',                          [MonedasController::class,  'cambiarStatusMoneda']);

//Tipo Mantenimiento 




//Empleados

//Roles
Route::post('/roles/registrarRoles',    [RolesController::class, 'registrarRoles']);
Route::get('/roles/obtenerListaRoles',  [RolesController::class, 'obtenerListaRoles']);
