<?php

        namespace App\Services\Auth\Usuarios;
        use App\Repositories\Auth\Usuarios\UsuariosRepository;
        use Illuminate\Support\Str;
        use App\Models\TblSessions;
        use App\Repositories\Admin\Catalogos\EmpleadosRepository;
        use App\Repositories\Admin\Catalogos\RolesRepository;
    
        class UsuariosService
        {
            protected UsuariosRepository  $usuariosRepository;
            protected RolesRepository     $rolesRepository;
            protected EmpleadosRepository $empleadosRepository;

            public function __construct(
                UsuariosRepository  $UsuariosRepository,
                RolesRepository     $RolesRepository,
                EmpleadosRepository $EmpleadosRepository
            ) {
                $this->usuariosRepository  = $UsuariosRepository;
                $this->rolesRepository     = $RolesRepository;
                $this->empleadosRepository = $EmpleadosRepository;
            }

            public function obtenerRecursosRegistroUsuario() {
                $rol       =$this->rolesRepository->obtenerListaRoles();
                $empleado  =$this->empleadosRepository->obtenerListaEmpleados();

                return response()->json(
                    [
                        'mensaje' => 'Se obtuvo los recursos correctamente',
                        'recursos' => ['listaRol'       => $rol,
                                        'listaempleado' => $empleado
                        ]
                    ]
                );
            }

            public function registrarUsuario(array $usuario)
            {
                $correo = trim(strtolower($usuario['busines_mail']));
            
                if (!str_contains($correo, '@')) {
                    $correo .= '@grupointerconsult.com';
                }
            
                $resultado = $this->usuariosRepository->validarUsuarioExistente($correo);
            
                if ($resultado > 0) {
                    return response()->json([
                        'title'   => 'Correo existente',
                        'mensaje' => 'Ya existe un registro con el correo empresarial escrito'
                    ], 409);
                }
            
                $usuario['busines_mail'] = $correo;
            
                $pkUsuario = $this->usuariosRepository->registrarUsuario($usuario);
            
                return response()->json([
                    'pkUsuario' => $pkUsuario,
                    'mensaje'   => 'Se ha registrado correctamente el usuario',
                    'title'     => 'Registro exitoso'
                ], 201);
            }

            public function obtenerListaGeneralUsuarios() {
                $usuario = $this->usuariosRepository->obtenerListaGeneralUsuarios();

                return response()->json(
                    [
                        'usuarios' => $usuario,
                        'mensaje'  => 'Se obtuvo la información de Usuarios'
                    ]
                );
            }

            public function obtenerDetalleUsuario(int $pkUsuario) {
                $usuario = $this->usuariosRepository->obtenerDetalleUsuario($pkUsuario);

                return response()->json(
                    [
                        'usuario' => $usuario[0],
                        'mensaje' => 'Se obtuvo la informacion correctamente'
                    ]
                );
            }

            public function actualizarUsuario(array $usuario) {
                $this->usuariosRepository->actualizarUsuario($usuario['pkUsuario'], $usuario['usuario']);

                return response()->json(
                    [
                        'title'   => 'Actualización exitosa',
                        'mensaje' => 'Se actualizó correctamente el usuario'
                    ]
                );
            }

            public function cambiarStatusUsuario(int $id) {
                $status = $this->usuariosRepository->cambiarStatusUsuario($id);

                return response()->json(
                    [
                        'title'   => ($status ? 'Activar' : 'Inactivar') . ' usuario',
                        'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' el usuario con éxito'
                        ]
                );
            }

            public function login(array $usuario)
            {
                $resultado = $this->usuariosRepository->login($usuario);    
                if ($resultado === 'no_usuario') {
                    return response()->json([
                        'success' => 204,
                        'title'   => 'Usuario no encontrado',
                        'mensaje' => 'El usuario no existe o las credenciales son incorrectas'
                    ]);
                }    
                if ($resultado === 'mal_contraseña') {
                    return response()->json([
                        'success' => 204,
                        'title'   => 'Credenciales Incorrectas',
                        'mensaje' => 'Las credenciales son incorrectas'
                    ]);
                }    
                $token = Str::random(60);    
                TblSessions::create([
                    'id_users' => $resultado->id_users,
                    'token'      => hash('sha256', $token) 
                ]);    
                return response()->json([
                    'usuarios' => $resultado,
                    'token'    => $token,
                    'mensaje'  => 'Inicio de sesión correctamente'
                ]);
            }

            public function cerrarSesion($request)
             {
                 $header = $request->header('Authorization');
 
                 if (!$header || !str_starts_with($header, 'Bearer ')) {
                     return response()->json([
                         'success' => false,
                         'mensaje' => 'Token no proporcionado'
                     ], 401);
                 }
 
                 $token = str_replace('Bearer ', '', $header);
 
                 $eliminado = $this->usuariosRepository->cerrarSesion($token);
 
                 return response()->json([
                     'success' => (bool) $eliminado,
                     'mensaje' => $eliminado
                         ? 'Sesión cerrada con éxito'
                         : 'No se encontró la sesión'
                 ]);
             }

        }
    