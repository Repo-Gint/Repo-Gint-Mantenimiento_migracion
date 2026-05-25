<?php

        namespace App\Repositories\Auth\Usuarios;

        use App\Models\TblUsuarios;
        use App\Models\TblSessions; 
        use Carbon\Carbon;
        use Illuminate\Support\Facades\DB;
        use Illuminate\Support\Facades\Log;

            class UsuariosRepository
            {
                public function validarUsuarioExistente($email)
                {
                    $query = TblUsuarios::where('email', $email);

                    return $query->count();
                }

                public function registrarUsuario($usuario)
                {
                    $registro = new TblUsuarios();

                    $registro->id_rol_users        = $usuario['id_rol_users'];
                    $registro->id_employee         = $usuario['id_employee'];
                    $registro->id_busines_mail     = $usuario['id_busines_mail'];
                    $registro->name                = $usuario['name'];
                    $registro->email               = $usuario['email'];
                    $registro->password            = bcrypt($usuario['password']);
                    $registro->save();

                    return $registro->id_usuario;
                }

            public function login ($usuario) {
                $usuarioEncontrado = TblUsuarios::where('email', $usuario['email'])
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
      