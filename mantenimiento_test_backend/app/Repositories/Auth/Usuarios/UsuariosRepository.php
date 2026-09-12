<?php

        namespace App\Repositories\Auth\Usuarios;

        use App\Models\TblUsuarios;
        use App\Models\TblSessions;
use Illuminate\Support\Facades\DB;

            class UsuariosRepository
            {
            public function validarUsuarioExistente($busines_mail)
            {
                $query = TblUsuarios::where('busines_mail', strtolower(trim($busines_mail)));
            
                return $query->count();
            }
            
            public function registrarUsuario(array $usuario)
            {
                $registro = new TblUsuarios();
            
                $registro->id_rol_users = $usuario['id_rol_users'];
                $registro->id_employee  = $usuario['id_employee'];
                $registro->busines_mail = strtolower(trim($usuario['busines_mail']));
                $registro->password     = bcrypt($usuario['password']);
                $registro->active      = 1;
                $registro->save();
            
                return $registro->id_users;
            }

            public function obtenerListaGeneralUsuarios() {
                $query = TblUsuarios::select(
                    'tbl_users.id_users',
                    'cat_roles.roles as rol',
                    'tbl_employee.name as empleado',
                    'tbl_users.busines_mail',
                    'tbl_users.password',
                    'tbl_users.active',
            
                    DB::raw("
                        CASE
                            WHEN tbl_users.active = 1 THEN 'Activo'
                            ELSE 'Inactivo'
                        END as estado
                    ")
                )
            
                ->join('cat_roles', 'cat_roles.id_roles', '=', 'tbl_users.id_rol_users')
                ->join('tbl_employee', 'tbl_employee.id_employee', '=', 'tbl_users.id_employee');
            
                return $query->get();
            }
            
            public function obtenerDetalleUsuario(int $pkUsuario) {
                $query = TblUsuarios::select(
                    'id_users',
                    'id_rol_users',
                    'id_employee',
                    'busines_mail',
                    'password',
                    'active'
                )
                    ->where('id_users', $pkUsuario);

                return $query->get();
            }

            public function actualizarUsuario(int $id, array $usuario) {

                $actualizar = TblUsuarios::findOrFail($id);

                $actualizar->id_rol_users = $usuario['id_rol_users'];
                $actualizar->id_employee  = $usuario['id_employee'];
                $actualizar->busines_mail = $usuario['busines_mail'];
                $actualizar->save();
            }

            public function cambiarStatusUsuario(int $id) {

                $usuario = TblUsuarios::findOrFail($id);
                $usuario->active = $usuario->active ? 0 : 1;
                $usuario->save();

                return $usuario->active;
            }


            public function login (array $usuario) {
                $usuarioEncontrado = TblUsuarios::where('busines_mail', $usuario['busines_mail'])
                ->first();

                if (!$usuarioEncontrado) return 'no_usuario';
                if (!password_verify($usuario['password'], $usuarioEncontrado->password)) return 'mal_contraseña';;

                return $usuarioEncontrado;
            }

            public function cerrarSesion($token)
            {
                return TblSessions::where('token', hash('sha256', $token))->delete();
            }
        }
