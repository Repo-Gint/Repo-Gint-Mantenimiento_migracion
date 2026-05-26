<?php

use App\Http\Controllers\Auth\Usuarios\UsuariosController;
use Illuminate\Support\Facades\Route;

Route::post('/usuarios/login', [UsuariosController::class, 'login']);


Route::post('/usuarios/registrarUsuario', [UsuariosController::class, 'registrarUsuario']);
Route::post('/usuarios/cerrarSesion',      [UsuariosController::class, 'cerrarSesion']);


