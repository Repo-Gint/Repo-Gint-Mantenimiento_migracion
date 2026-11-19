<?php

namespace App\Services\Admin\Ordenes;

use App\Mail\OrderMessageMail;
use App\Mail\OrderStatusMail;
use App\Repositories\Admin\Catalogos\AreasRepository;
use App\Repositories\Admin\Catalogos\DepartamentosRepository;
use App\Repositories\Admin\Catalogos\EmpleadosRepository;
use App\Repositories\Admin\Catalogos\MaquinasCatalogoRepository;
use App\Repositories\Admin\Catalogos\MaquinasRepository;
use App\Repositories\Admin\Catalogos\PrioridadOrdenRepository;
use App\Repositories\Admin\Catalogos\TipoMantenimientoRepository;
use App\Repositories\Admin\Catalogos\TipoOrdenRepository;
use App\Repositories\Admin\Ordenes\OrdenesRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
        $this->ordenesRepository             = $OrdenesRepository;
        $this->areasRepository               = $AreasRepository;
        $this->departamentosRepository       = $DepartamentosRepository;
        $this->empleadosRepository           = $EmpleadosRepository;
        $this->maquinasRepository            = $MaquinasRepository;
        $this->tipoMantenimientoRepository   = $TipoMantenimientoRepository;
        $this->tipoOrdenRepository           = $TipoOrdenRepository;
        $this->prioridadOrdenRepository      = $PrioridadOrdenRepository;
        $this->maquinasCatalogoRepository    = $MaquinasCatalogoRepository;
    }

    public function verificarPermisoUsuario(Request $request, string $slug): bool
    {
        $idUser = $request->input('id_users_auth') ?? $request->attributes->get('id_users_auth');

        if (!$idUser) {
            $header = $request->header('Authorization');
            if ($header && str_starts_with($header, 'Bearer ')) {
                $token   = str_replace('Bearer ', '', $header);
                $session = $this->ordenesRepository->obtenerSesionPorToken($token);
                if ($session) {
                    $idUser = $session->id_users;
                }
            }
        }

        if (!$idUser) {
            return false;
        }

        $user = $this->ordenesRepository->obtenerUsuarioPorId((int)$idUser);
        if (!$user) {
            return false;
        }

        if ((int)$user->id_rol_users === 2) {
            return true;
        }

        return $this->ordenesRepository->verificarPermisoPorUsuarioYSlug((int)$user->id_users, $slug);
    }

    public function obtenerRecursosRegistroOrden()
    {
        $areas             = $this->areasRepository->obtenerListaAreas();
        $maquinas          = $this->maquinasRepository->obtenerListaMaquinas();
        $departamentos     = $this->departamentosRepository->obtenerListaDepartamentos();
        $empleados         = $this->empleadosRepository->obtenerListaEmpleados();
        $tipoMantenimiento = $this->tipoMantenimientoRepository->obtenerListatipoMantenimientos();
        $tipoOrden         = $this->tipoOrdenRepository->obtenerListaTipoOrden();
        $prioridadOrden    = $this->prioridadOrdenRepository->obtenerListaPrioridadOrden();
        $maquinasCatalogo  = $this->maquinasCatalogoRepository->obtenerListaCatalogoMaquina();

        return response()->json([
            'mensaje'  => 'Se obtuvo los recursos correctamente',
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
        ]);
    }

    public function registrarOrden(array $orden, array|UploadedFile|null $files = null, mixed $idUser = null)
    {
        if ($idUser) {
            $userRecord = $this->ordenesRepository->obtenerUsuarioPorId((int)$idUser);
            if ($userRecord && (int)$userRecord->id_rol_users === 1) {
                $orden['id_employee'] = $userRecord->id_employee;
            }
        }

        $rutasEvidencia = $this->guardarArchivosEnDisco($files, 'orders');

        $registro = DB::transaction(function () use ($orden, $rutasEvidencia) {
            $nuevaOrden = $this->ordenesRepository->registrarOrdenes($orden);

            if (!empty($rutasEvidencia)) {
                $this->ordenesRepository->registrarEvidenciasOrden((int)$nuevaOrden->id_order, $rutasEvidencia);
            }

            return $nuevaOrden;
        });

        $empleado        = $this->ordenesRepository->obtenerEmpleadoPorId((int)$orden['id_employee']);
        $nombreRemitente = $empleado ? $empleado->Name : 'Usuario del Sistema';

        if ($empleado && !empty($empleado->busines_mail)) {
            $detallesUsuario = [
                'folio'     => 'ORD-' . $registro->id_order,
                'content'   => 'Has creado una orden y en seguida se ha notificado al de mantenimiento sobre tu orden, pronto recibirás más información sobre tu orden.',
                'remitente' => $nombreRemitente,
                'url'       => 'http://localhost:4200/login'
            ];
            Mail::to($empleado->busines_mail)->send(new OrderMessageMail($detallesUsuario));
        }

        $correoAdmin = config('mail.from.address');
        if (!empty($correoAdmin)) {
            $detallesAdmin = [
                'folio'     => 'ORD-' . $registro->id_order,
                'content'   => 'Tienes una nueva orden (ORD-' . $registro->id_order . '), deseas darle seguimiento dale click para entrar a la plataforma.',
                'remitente' => $nombreRemitente,
                'url'       => 'http://localhost:4200/login'
            ];
            Mail::to($correoAdmin)->send(new OrderMessageMail($detallesAdmin));
        }

        return response()->json([
            'mensaje' => 'Se registró exitosamente con éxito',
            'title'   => 'Registro exitoso'
        ]);
    }

    public function cancelarOrden(int $id_order)
    {
        $ordenes = $this->ordenesRepository->cancelarOrden($id_order);

        return response()->json([
            'ordenes' => $ordenes,
            'mensaje' => 'Se ha cancelado la orden con éxito'
        ], 200);
    }

    public function eliminarOrden(int $id_order)
    {
        $this->ordenesRepository->eliminarOrden($id_order);

        return response()->json([
            'mensaje' => 'La orden ha sido eliminada correctamente.',
            'title'   => 'Se elimino la orden con éxito'
        ], 200);
    }

    public function obtenerStatusOrdenes()
    {
        $ordenes = $this->ordenesRepository->obtenerStatusOrdenes();

        return response()->json([
            'ordenes' => $ordenes,
            'mensaje' => 'Se obtuvo la información de ordenes'
        ]);
    }

    public function ObtenerListaGeneralOrdenes(int|string|null $pkArea, int|string|null $pkStatus, mixed $idUser = null)
    {
        $pkRol      = 1;
        $idEmployee = null;

        if ($idUser) {
            $userRecord = $this->ordenesRepository->obtenerUsuarioPorId((int)$idUser);
            if ($userRecord) {
                $pkRol      = (int)$userRecord->id_rol_users;
                $idEmployee = $userRecord->id_employee;
            }
        }

        if ($pkRol === 1 && !$idEmployee) {
            $idEmployee = -9999;
        }

        $ordenes = $this->ordenesRepository->ObtenerListaGeneralOrdenes(
            $pkArea,
            $pkStatus,
            $pkRol === 1 ? $idEmployee : null
        );

        return response()->json([
            'ordenes' => $ordenes,
            'mensaje' => 'Se obtuvo la información de ordenes'
        ]);
    }

    public function obtenerDetalleOrden(int $pkOrden)
    {
        $orden             = $this->ordenesRepository->obtenerDetalleOrden($pkOrden);
        $usuariosAsignados = $this->ordenesRepository->obtenerUsuariosAsignadosOrden($pkOrden);
        $evidencias        = $this->ordenesRepository->obtenerEvidenciaOrden($pkOrden);

        return response()->json([
            'orden'             => $orden,
            'usuariosAsignados' => $usuariosAsignados,
            'evidencias'        => $evidencias,
            'mensaje'           => 'Se obtuvo la información correcta'
        ]);
    }

    public function eliminarEvidenciaOrden(int $id)
    {
        $this->ordenesRepository->eliminarEvidenciaOrden($id);

        return response()->json([
            'mensaje' => 'Evidencia eliminada correctamente'
        ]);
    }

    public function actualizarOrden(array $orden, array|UploadedFile|null $files = null)
    {
        $id = (int)$orden['pkOrden'];

        DB::transaction(function () use ($id, $orden, $files) {
            $this->ordenesRepository->actualizarOrden($id, $orden);

            if ($files) {
                $rutas         = [];
                $listaArchivos = is_array($files) ? $files : [$files];

                foreach ($listaArchivos as $file) {
                    $rutas[] = $file->store('ordenes', 'public');
                }

                $this->ordenesRepository->actualizarEvidencias($id, $rutas);
            }
        });

        return response()->json([
            'title'    => 'Actualización exitosa',
            'mensajes' => 'Se actualizó correctamente el orden',
            'pkOrden'  => $id
        ]);
    }

    public function asignarOrden(int $pkOrden, mixed $idUsuario)
    {
        if (!$pkOrden || !is_array($idUsuario)) {
            return response()->json([
                'mensaje' => 'Datos invalidos'
            ], 400);
        }

        if (empty($idUsuario)) {
            return response()->json([
                'mensaje' => 'Debes asignar al menos un usuario'
            ], 400);
        }

        DB::transaction(function () use ($pkOrden, $idUsuario) {
            $this->ordenesRepository->depurarAsignacionesOrden($pkOrden);

            foreach ($idUsuario as $usuario) {
                $this->ordenesRepository->asignarOrden([
                    'id_order' => $pkOrden,
                    'id_users' => $usuario
                ]);
            }
        });

        return response()->json([
            'mensaje' => 'Usuarios asignados correctamente'
        ]);
    }

    public function obtenerUsuariosAsignacion(int $pkOrden)
    {
        $usuarios  = $this->ordenesRepository->obtenerUsuariosAsignacion();
        $asignados = $this->ordenesRepository->obtenerIdsUsuariosAsignadosOrden($pkOrden);

        $usuarios = $usuarios->map(function ($value) use ($asignados) {
            return [
                'value'   => $value->id_users,
                'label'   => $value->Name,
                'checked' => $asignados->contains($value->id_users)
            ];
        });

        return response()->json([
            'usuarios' => $usuarios
        ]);
    }

    public function obtenerMaquinasFiltradasPorAreaYCat(int $pkArea, int $pkCatalogoMaquina)
    {
        $maquinas = $this->maquinasRepository->obtenerMaquinasPorAreaYCategoria($pkArea, $pkCatalogoMaquina);

        return response()->json([
            'maquinas' => $maquinas,
            'mensaje'  => 'Se obtuvieron las máquinas filtradas correctamente'
        ]);
    }

    public function obtenerMensajesOrden(int $id_order)
    {
        $mensajes = $this->ordenesRepository->obtenerMensajesOrden($id_order);

        return response()->json([
            'mensajes' => $mensajes,
            'mensaje'  => 'Se obtuvieron los mensajes correctamente'
        ]);
    }

    public function enviarMensajeYNotificacion(array $requestData)
    {
        DB::transaction(function () use ($requestData) {
            $this->ordenesRepository->registrarMensajeOrden($requestData);
            $ordenInfo = $this->ordenesRepository->obtenerCorreoYFolioOrden((int)$requestData['id_order']);

            $destinatario = $requestData['es_admin']
                ? $ordenInfo->user_business_mail
                : config('mail.from.address');

            $detallesCorreo = [
                'folio'     => $ordenInfo->folio,
                'content'   => $requestData['content'],
                'remitente' => $requestData['remitente_nombre'] ?? 'Administración',
                'url'       => config('app.frontend_url', 'http://localhost:4200') . '/admin/ordenes/consulta-ordenes'
            ];

            Mail::to($destinatario)->send(new OrderMessageMail($detallesCorreo));
        });

        return response()->json([
            'title'   => 'Mensaje enviado',
            'mensaje' => 'El mensaje se envió y se notificó por correo correctamente'
        ]);
    }

    public function finalizarORechazarOrden(array $data, array|UploadedFile|null $files = null)
    {
        $id_order        = $data['id_order'] ?? $data['pkOrden'] ?? null;
        $id_status_order = $data['id_status_order'] ?? $data['idStatusOrder'] ?? $data['id_status'] ?? null;

        if (!$id_order || !$id_status_order) {
            return response()->json([
                'title'   => 'Parámetros incompletos',
                'mensaje' => 'Se requiere el ID de la orden y el nuevo estatus.'
            ], 400);
        }

        $id_order        = (int)$id_order;
        $id_status_order = (int)$id_status_order;

        DB::transaction(function () use ($data, $files, $id_order, $id_status_order) {
            $this->ordenesRepository->cambiarStatusOrder($id_order, $id_status_order);

            $ordenInfo         = $this->ordenesRepository->obtenerCorreoYFolioOrden($id_order);
            $solutionData      = null;
            $pdfContentAlVuelo = null;

            if ($id_status_order === 3) {
                $businessMail = $data['business_mail'] ?? $data['busines_mail'] ?? ($ordenInfo->user_business_mail ?? null);

                $payloadSolucion = [
                    'id_order'        => $id_order,
                    'id_status_order' => $id_status_order,
                    'id_users'        => $data['id_users'] ?? $data['id_users_auth'] ?? 1,
                    'business_mail'   => $businessMail,
                    'id_coin'         => $data['id_coin'] ?? $data['id_coins'] ?? 1,
                    'order_cost'      => $data['order_cost'] ?? 0,
                    'resolution'      => $data['resolution'] ?? $data['solucion'] ?? 'Sin resolución registrada',
                    'spare_parts'     => $data['spare_parts'] ?? 'N/A',
                    'materials'       => $data['materials'] ?? 'N/A',
                ];

                $this->ordenesRepository->registrarSolucionOrden($payloadSolucion);

                $rutasResolucion = $this->guardarArchivosEnDisco($files, 'resolution');
                if (!empty($rutasResolucion)) {
                    $this->ordenesRepository->registrarImagenesResolucion($id_order, $rutasResolucion);
                }

                $solutionData    = $this->ordenesRepository->obtenerDetalleSolucionOrden($id_order);
                $quiereEnviarPdf = isset($data['enviar_pdf']) ? filter_var($data['enviar_pdf'], FILTER_VALIDATE_BOOLEAN) : false;

                if ($quiereEnviarPdf) {
                    $datosPdf = $this->generarPdfOrden($id_order);
                    if ($datosPdf) {
                        $pdfContentAlVuelo = $datosPdf['pdf']->output();
                    }
                }
            }

            $statusModel   = $this->ordenesRepository->obtenerStatusPorId($id_status_order);
            $correoDestino = $ordenInfo ? $ordenInfo->user_business_mail : null;

            if (!empty($correoDestino)) {
                $baseUrl     = 'http://localhost:8000/api/ordenes/calificar-solucion';
                $urlAceptar  = "{$baseUrl}?id_order={$id_order}&respuesta=aceptado";
                $urlRechazar = "{$baseUrl}?id_order={$id_order}&respuesta=rechazado";

                $correoData = [
                    'folio'         => $ordenInfo->folio ?? ('ORD-' . $id_order),
                    'employee_name' => $ordenInfo->employee_name ?? 'Usuario',
                    'machine'       => $ordenInfo->machines ?? 'Máquina asignada',
                    'status'        => $statusModel ? $statusModel->status : 'Actualizado',
                    'status_id'     => $id_status_order,
                    'solution'      => $solutionData,
                    'url_aceptar'   => $urlAceptar,
                    'url_rechazar'  => $urlRechazar,
                    'url'           => 'http://localhost:4200/login'
                ];

                Mail::to($correoDestino)->send(new OrderStatusMail($correoData, $pdfContentAlVuelo));
            }
        });

        return response()->json([
            'title'   => 'Operación exitosa',
            'mensaje' => 'El estatus se actualizó y la notificación fue procesada correctamente'
        ]);
    }

    public function calificarSolucionOrden(int $id_order, string $respuesta)
    {
        $ordenInfo           = $this->ordenesRepository->obtenerCorreoYFolioOrden($id_order);
        $correoMantenimiento = config('mail.from.address');

        if ($respuesta === 'aceptado') {
            $detallesExito = [
                'folio'     => $ordenInfo->folio ?? ('ORD-' . $id_order),
                'content'   => 'El usuario ' . ($ordenInfo->employee_name ?? '') . ' ha ACEPTADO la solución de la orden ' . ($ordenInfo->folio ?? '') . '. La orden ha quedado concluida.',
                'remitente' => $ordenInfo->employee_name ?? 'Cliente',
                'url'       => config('app.frontend_url', 'http://localhost:4200/login') . '/admin/ordenes/detalle/' . $id_order
            ];
            Mail::to($correoMantenimiento)->send(new OrderMessageMail($detallesExito));

            return view('ordenes.mensajes-respuesta', [
                'titulo'  => '¡Muchas gracias!',
                'mensaje' => 'Agradecemos su confirmación. La orden ' . ($ordenInfo->folio ?? '') . ' ha quedado concluida satisfactoriamente.',
                'tipo'    => 'success'
            ]);
        }

        $detallesAlerta = [
            'folio'     => $ordenInfo->folio ?? ('ORD-' . $id_order),
            'content'   => 'El usuario ha indicado que NO está conforme con la solución aplicada en la orden ' . ($ordenInfo->folio ?? '') . '. Se requiere revisión.',
            'remitente' => $ordenInfo->employee_name ?? 'Cliente',
            'url'       => config('app.frontend_url', 'http://localhost:4200/login') . '/admin/ordenes/detalle/' . $id_order
        ];
        Mail::to($correoMantenimiento)->send(new OrderMessageMail($detallesAlerta));

        return view('ordenes.mensajes-respuesta', [
            'titulo'  => 'Solicitud de revisión registrada',
            'mensaje' => 'En breve se le notificará al departamento de mantenimiento que la solución de la orden ' . ($ordenInfo->folio ?? '') . ' no fue satisfactoria.',
            'tipo'    => 'warning'
        ]);
    }

    public function generarPdfOrden(int $pkOrden)
    {
        $datosPdf = $this->ordenesRepository->obtenerDatosPdfOrden($pkOrden);

        if (!$datosPdf) {
            return null;
        }

        $brandImage = file_exists(public_path('images/brand_logo.png'))
            ? public_path('images/brand_logo.png')
            : null;

        $pdf = Pdf::loadView('pdf.orden.orden', [
            'orden'             => $datosPdf['orden'],
            'usuariosAsignados' => $datosPdf['usuariosAsignados'],
            'evidencias'        => $datosPdf['evidencias'],
            'resoluciones'      => $datosPdf['resoluciones'],
            'solucion'          => $datosPdf['solucion'],
            'brandImage'        => $brandImage
        ]);

        $pdf->setPaper('letter', 'portrait');

        return [
            'pdf'   => $pdf,
            'folio' => $datosPdf['orden']->folio
        ];
    }

    public function insertarPdfEnChat(int $id_order, int $id_user = 1)
    {
        $publicUrl = DB::transaction(function () use ($id_order, $id_user) {
            $datosPdf = $this->generarPdfOrden($id_order);

            if (!$datosPdf) {
                throw new Exception('No se pudo generar el PDF de la orden.');
            }

            $pdf      = $datosPdf['pdf'];
            $folio    = $datosPdf['folio'];
            $fileName = 'Reporte_' . $folio . '_' . time() . '.pdf';
            $path     = 'ordenes_pdfs/' . $fileName;

            Storage::disk('public')->put($path, $pdf->output());

            $baseUrl   = config('app.url', 'http://localhost:8000');
            $url       = $baseUrl . '/storage/' . $path;
            $ordenInfo = $this->ordenesRepository->obtenerCorreoYFolioOrden($id_order);

            $mensajeData = [
                'id_order'        => $id_order,
                'id_users'        => $id_user,
                'busines_mail'    => $ordenInfo->user_business_mail ?? 'admin@localhost',
                'subject'         => 'Reporte de Orden Finalizada',
                'content'         => 'Se ha generado el reporte final de la orden. Puedes descargarlo en el siguiente enlace: ' . $url,
                'id_status_order' => 3,
                'es_admin'        => true
            ];

            $this->ordenesRepository->registrarMensajeOrden($mensajeData);

            return $url;
        });

        return [
            'title'   => 'PDF Enviado',
            'mensaje' => 'El PDF se ha insertado en el chat correctamente.',
            'url'     => $publicUrl
        ];
    }

    private function guardarArchivosEnDisco(array|UploadedFile|null $files, string $carpeta): array
    {
        if (!$files) {
            return [];
        }

        $rutas         = [];
        $listaArchivos = is_array($files) ? $files : [$files];

        foreach ($listaArchivos as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $rutas[]  = $file->storeAs($carpeta, $filename, 'public');
        }

        return $rutas;
    }
}