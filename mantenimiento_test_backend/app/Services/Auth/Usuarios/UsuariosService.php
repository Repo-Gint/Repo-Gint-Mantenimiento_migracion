<?php

namespace App\Services\Auth\Usuarios;

use App\Repositories\Auth\Usuarios\UsuariosRepository;
use Illuminate\Support\Str;
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
        $rol      = $this->rolesRepository->obtenerListaRoles();
        $empleado = $this->empleadosRepository->obtenerListaEmpleados();
        $permisos = $this->usuariosRepository->obtenerTodosPermisos();

        return response()->json([
            'mensaje'  => 'Se obtuvo los recursos correctamente',
            'recursos' => [
                'listaRol'      => $rol,
                'listaempleado' => $empleado,
                'listaPermisos' => $permisos
            ]
        ]);
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
        $usuario['password'] = bcrypt($usuario['password']); // Lógica de encriptación en el Service

        $pkUsuario = $this->usuariosRepository->registrarUsuario($usuario);

        if (isset($usuario['permisos']) && is_array($usuario['permisos'])) {
            $this->usuariosRepository->sincronizarPermisos($pkUsuario, $usuario['permisos']);
        }
    
        return response()->json([
            'pkUsuario' => $pkUsuario,
            'mensaje'   => 'Se ha registrado correctamente el usuario',
            'title'     => 'Registro exitoso'
        ], 201);
    }

    public function obtenerListaGeneralUsuarios() {
        $usuario = $this->usuariosRepository->obtenerListaGeneralUsuarios();

        return response()->json([
            'usuarios' => $usuario,
            'mensaje'  => 'Se obtuvo la información de Usuarios'
        ]);
    }

    public function obtenerDetalleUsuario(int $pkUsuario) {
        $usuario = $this->usuariosRepository->obtenerDetalleUsuario($pkUsuario);
        $permisosUsuario = $this->usuariosRepository->obtenerPermisosPorUsuario($pkUsuario);

        $dataUsuario = $usuario[0];
        $dataUsuario->permisos = $permisosUsuario;

        return response()->json([
            'usuario' => $dataUsuario,
            'mensaje' => 'Se obtuvo la informacion correctamente'
        ]);
    }

    public function actualizarUsuario(array $data) {
        $pkUsuario = $data['pkUsuario'];
        $datosUsuario = $data['usuario'];

        $datosUsuario['busines_mail'] = strtolower(trim($datosUsuario['busines_mail']));

        if (!empty($datosUsuario['password'])) {
            $datosUsuario['password'] = bcrypt($datosUsuario['password']); // Lógica en Service
        }

        $this->usuariosRepository->actualizarUsuario($pkUsuario, $datosUsuario);

        if (isset($datosUsuario['permisos']) && is_array($datosUsuario['permisos'])) {
            $this->usuariosRepository->sincronizarPermisos($pkUsuario, $datosUsuario['permisos']);
        }

        return response()->json([
            'title'   => 'Actualización exitosa',
            'mensaje' => 'Se actualizó correctamente el usuario'
        ]);
    }

    public function cambiarStatusUsuario(int $id) {
        $status = $this->usuariosRepository->cambiarStatusUsuario($id);

        return response()->json([
            'title'   => ($status ? 'Activar' : 'Inactivar') . ' usuario',
            'mensaje' => 'Se ' . ($status ? 'activo' : 'inactivo') . ' el usuario con éxito'
        ]);
    }

    public function login(array $usuario)
    {
        $correoLimpio = strtolower(trim($usuario['busines_mail']));
        $usuarioEncontrado = $this->usuariosRepository->buscarPorCorreo($correoLimpio);    

        if (!$usuarioEncontrado) {
            return response()->json([
                'success' => 204,
                'title'   => 'Usuario no encontrado',
                'mensaje' => 'El usuario no existe o las credenciales son incorrectas'
            ]);
        }    

        if (!password_verify($usuario['password'], $usuarioEncontrado->password)) { // Validación en Service
            return response()->json([
                'success' => 204,
                'title'   => 'Credenciales Incorrectas',
                'mensaje' => 'Las credenciales son incorrectas'
            ]);
        }    

        $token = Str::random(60);    
        $this->usuariosRepository->registrarSesion($usuarioEncontrado->id_users, hash('sha256', $token)); // Hashing en Service

        $permisos = $this->usuariosRepository->obtenerPermisosPorUsuario($usuarioEncontrado->id_users);

        return response()->json([
            'usuarios' => $usuarioEncontrado,
            'permisos' => $permisos,
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
        $tokenHash = hash('sha256', $token); // Lógica de hash en Service

        $eliminado = $this->usuariosRepository->cerrarSesion($tokenHash);

        return response()->json([
            'success' => (bool) $eliminado,
            'mensaje' => $eliminado ? 'Sesión cerrada con éxito' : 'No se encontró la sesión'
        ]);
    }

    public function obtenerPermisosUsuarioActual($request) {
        $header = $request->header('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['mensaje' => 'Token no proporcionado'], 401);
        }

        $token = str_replace('Bearer ', '', $header);
        $tokenHash = hash('sha256', $token); // Lógica de hash en Service

        $datos = $this->usuariosRepository->obtenerPermisosPorToken($tokenHash);

        if (!$datos) {
            return response()->json(['mensaje' => 'Sesión no válida'], 401);
        }

        return response()->json($datos);
    }
}