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
use App\Mail\OrderMessageMail;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function verificarPermisoUsuario($request, string $slug): bool {
        $idUser = $request->input('id_users_auth') ?? $request->attributes->get('id_users_auth');

        if (!$idUser) {
            $header = $request->header('Authorization');
            if ($header && str_starts_with($header, 'Bearer ')) {
                $token = str_replace('Bearer ', '', $header);
                $session = DB::table('tbl_sessions')->where('token', $token)->orWhere('token', hash('sha256', $token))->first();
                if ($session) {
                    $idUser = $session->id_users;
                }
            }
        }

        if (!$idUser) {
            return false;
        }

        $user = DB::table('tbl_users')->where('id_users', $idUser)->first();
        if (!$user) {
            return false;
        }

        if ((int)$user->id_rol_users === 2) {
            return true;
        }

        return DB::table('tbl_user_permissions')
            ->join('cat_permissions', 'cat_permissions.id_permission', '=', 'tbl_user_permissions.id_permission')
            ->where('tbl_user_permissions.id_users', $user->id_users)
            ->where('cat_permissions.slug', $slug)
            ->exists();
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

        return response()->json([
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
        ]);
    }

    public function registrarOrden(array $orden, $files) {
        $registro = $this->ordenesRepository->registrarOrdenes($orden, $files);

        try {
            $empleado = DB::table('tbl_employee')->where('id_employee', $orden['id_employee'])->first();
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
        } catch (\Throwable $e) {
            \Log::error('Error al enviar correos de nueva orden: ' . $e->getMessage());
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

    public function obtenerStatusOrdenes() {
        $ordenes = $this->ordenesRepository->obtenerStatusOrdenes();

        return response()->json([
            'ordenes' => $ordenes,
            'mensaje' => 'Se obtuvo la información de ordenes'
        ]);
    }

    public function ObtenerListaGeneralOrdenes($pkArea, $pkStatus, $idUser = null, $pkRol = null) {
        $ordenes = $this->ordenesRepository->ObtenerListaGeneralOrdenes($pkArea, $pkStatus, $idUser, $pkRol);

        return response()->json([
            'ordenes' => $ordenes,
            'mensaje' => 'Se obtuvo la información de ordenes'
        ]);
    }

    public function obtenerDetalleOrden(int $pkOrden) {
        $orden              = $this->ordenesRepository->obtenerDetalleOrden($pkOrden);
        $usuariosAsignados  = $this->ordenesRepository->obtenerUsuariosAsignadosOrden($pkOrden);
        $evidencias         = $this->ordenesRepository->obtenerEvidenciaOrden($pkOrden);

        return response()->json([
            'orden'             => $orden,
            'usuariosAsignados' => $usuariosAsignados,
            'evidencias'        => $evidencias,
            'mensaje'           => 'Se obtuvo la información correcta'
        ]);
    }

    public function eliminarEvidenciaOrden(int $id) {
        $evidencia = $this->ordenesRepository->eliminarEvidenciaOrden($id);

        return response()->json([
            'evidencia' => $evidencia,
            'mensaje'   => 'Evidencia eliminada correctamente'
        ]);
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
            'pkOrden'  => $id
        ]);
    }

    public function asignarOrden(int $pkOrden, array $idUsuario) {
        DB::beginTransaction();

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

        $this->ordenesRepository->depurarAsignacionesOrden($pkOrden);

        foreach ($idUsuario as $usuario) {
            $orden = [
                'id_order' => $pkOrden,
                'id_users' => $usuario
            ];

            $this->ordenesRepository->asignarOrden($orden);
        }

        DB::commit();
        return response()->json([
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
        DB::beginTransaction();
        try {
            $this->ordenesRepository->registrarMensajeOrden($requestData);
            $ordenInfo = $this->ordenesRepository->obtenerCorreoYFolioOrden($requestData['id_order']);

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

            DB::commit();
            return response()->json([
                'title'   => 'Mensaje enviado',
                'mensaje' => 'El mensaje se envió y se notificó por correo correctamente'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function finalizarORechazarOrden(array $data, $files = null)
    {
        DB::beginTransaction();
        try {
            $id_order = $data['id_order'] ?? $data['pkOrden'] ?? null;
            $id_status_order = $data['id_status_order'] ?? $data['idStatusOrder'] ?? $data['id_status'] ?? null;
    
            if (!$id_order || !$id_status_order) {
                return response()->json([
                    'title'   => 'Parámetros incompletos',
                    'mensaje' => 'Se requiere el ID de la orden y el nuevo estatus.'
                ], 400);
            }
    
            $this->ordenesRepository->cambiarStatusOrder($id_order, $id_status_order);
    
            $solutionData = null;
            $pdfContentAlVuelo = null; 

            if ((int)$id_status_order === 3) { 
                $payloadSolucion = [
                    'id_order'        => $id_order,
                    'id_status_order' => $id_status_order,
                    'id_users'        => $data['id_users'] ?? $data['id_users_auth'] ?? 1,
                    'business_mail'   => $data['business_mail'] ?? $data['busines_mail'] ?? null,
                    'id_coin'         => $data['id_coin'] ?? $data['id_coins'] ?? 1,
                    'order_cost'      => $data['order_cost'] ?? 0,
                    'resolution'      => $data['resolution'] ?? $data['solucion'] ?? 'Sin resolución registrada',
                    'spare_parts'     => $data['spare_parts'] ?? 'N/A',
                    'materials'       => $data['materials'] ?? 'N/A',
                ];
    
                $this->ordenesRepository->registrarSolucionOrden($payloadSolucion, $files);
                $solutionData = $this->ordenesRepository->obtenerDetalleSolucionOrden($id_order);

                $quiereEnviarPdf = isset($data['enviar_pdf']) ? filter_var($data['enviar_pdf'], FILTER_VALIDATE_BOOLEAN) : false;

                if ($quiereEnviarPdf) {
                    $datosPdf = $this->generarPdfOrden($id_order);
                    
                    if ($datosPdf) {
                        $pdfContentAlVuelo = $datosPdf['pdf']->output();
                    }
                }
            }
    
            $ordenInfo = $this->ordenesRepository->obtenerCorreoYFolioOrden($id_order);
            $statusModel = DB::table('cat_status_order')->where('id_status_order', $id_status_order)->first();
    
            $correoDestino = $ordenInfo ? $ordenInfo->user_business_mail : null;
    
            if (!empty($correoDestino)) {
                $baseUrl = 'http://localhost:8000/api/ordenes/calificar-solucion';
                
                $urlAceptar = "{$baseUrl}?id_order={$id_order}&respuesta=aceptado";
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
    
            DB::commit();
            return response()->json([
                'title'   => 'Operación exitosa',
                'mensaje' => 'El estatus se actualizó y la notificación fue procesada correctamente'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function calificarSolucionOrden($id_order, $respuesta)
    {
        $ordenInfo = $this->ordenesRepository->obtenerCorreoYFolioOrden($id_order);
        $correoMantenimiento = config('mail.from.address'); 

        if ($respuesta === 'aceptado') {
            $detallesExito = [
                'folio'     => $ordenInfo->folio ?? ('ORD-' . $id_order),
                'content'   => 'El usuario ' . ($ordenInfo->employee_name ?? '') . ' ha ACEPTADO la solución de la orden ' . ($ordenInfo->folio ?? '') . '. La orden ha quedado concluida.',
                'remitente' => $ordenInfo->employee_name ?? 'Cliente',
                'url'       => config('app.frontend_url', 'http://localhost:4200/login') . '/admin/ordenes/detalle/' . $id_order
            ];
            \Illuminate\Support\Facades\Mail::to($correoMantenimiento)->send(new \App\Mail\OrderMessageMail($detallesExito));

            return view('ordenes.mensajes-respuesta', [
                'titulo' => '¡Muchas gracias!',
                'mensaje' => 'Agradecemos su confirmación. La orden ' . ($ordenInfo->folio ?? '') . ' ha quedado concluida satisfactoriamente.',
                'tipo' => 'success'
            ]);
        } else {
            $detallesAlerta = [
                'folio' => $ordenInfo->folio ?? ('ORD-' . $id_order),
                'content' => 'El usuario ha indicado que NO está conforme con la solución aplicada en la orden ' . ($ordenInfo->folio ?? '') . '. Se requiere revisión.',
                'remitente' => $ordenInfo->employee_name ?? 'Cliente',
                'url' => config('app.frontend_url', 'http://localhost:4200/login') . '/admin/ordenes/detalle/' . $id_order
            ];

            \Illuminate\Support\Facades\Mail::to($correoMantenimiento)->send(new \App\Mail\OrderMessageMail($detallesAlerta));

            return view('ordenes.mensajes-respuesta', [
                'titulo' => 'Solicitud de revisión registrada',
                'mensaje' => 'En breve se le notificará al departamento de mantenimiento que la solución de la orden ' . ($ordenInfo->folio ?? '') . ' no fue satisfactoria.',
                'tipo' => 'warning'
            ]);
        }
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
        DB::beginTransaction();
        try {
            $datosPdf = $this->generarPdfOrden($id_order);
            
            if (!$datosPdf) {
                throw new \Exception('No se pudo generar el PDF de la orden.');
            }

            $pdf = $datosPdf['pdf'];
            $folio = $datosPdf['folio'];

            $fileName = 'Reporte_' . $folio . '_' . time() . '.pdf';
            $path = 'ordenes_pdfs/' . $fileName;
            
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $pdf->output());

            $baseUrl = config('app.url', 'http://localhost:8000');
            $publicUrl = $baseUrl . '/storage/' . $path;

            $ordenInfo = $this->ordenesRepository->obtenerCorreoYFolioOrden($id_order);

            $mensajeData = [
                'id_order'         => $id_order,
                'id_users'         => $id_user,
                'busines_mail'     => $ordenInfo->user_business_mail ?? 'admin@localhost',
                'subject'          => 'Reporte de Orden Finalizada',
                'content'          => 'Se ha generado el reporte final de la orden. Puedes descargarlo en el siguiente enlace: ' . $publicUrl,
                'id_status_order'  => 3, 
                'es_admin'         => true
            ];

            $this->ordenesRepository->registrarMensajeOrden($mensajeData);

            DB::commit();

            return [
                'title' => 'PDF Enviado',
                'mensaje' => 'El PDF se ha insertado en el chat correctamente.',
                'url' => $publicUrl
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}