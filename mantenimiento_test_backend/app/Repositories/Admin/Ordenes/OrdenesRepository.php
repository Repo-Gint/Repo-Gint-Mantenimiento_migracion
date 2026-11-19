<?php

namespace App\Repositories\Admin\Ordenes;

use App\Models\TblOrdenesAsignacion;
use App\Models\Tblorders;
use App\Models\TblUsuarios;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrdenesRepository
{
    public function obtenerSesionPorToken(string $token)
    {
        return DB::table('tbl_sessions')
            ->where('token', $token)
            ->orWhere('token', hash('sha256', $token))
            ->first();
    }

    public function obtenerUsuarioPorId(int $idUser)
    {
        return DB::table('tbl_users')
            ->where('id_users', $idUser)
            ->first();
    }

    public function verificarPermisoPorUsuarioYSlug(int $idUser, string $slug): bool
    {
        return DB::table('tbl_user_permissions')
            ->join('cat_permissions', 'cat_permissions.id_permission', '=', 'tbl_user_permissions.id_permission')
            ->where('tbl_user_permissions.id_users', $idUser)
            ->where('cat_permissions.slug', $slug)
            ->exists();
    }

    public function obtenerEmpleadoPorId(int $idEmployee)
    {
        return DB::table('tbl_employee')
            ->where('id_employee', $idEmployee)
            ->first();
    }

    public function obtenerStatusPorId(int $idStatusOrder)
    {
        return DB::table('cat_status_order')
            ->where('id_status_order', $idStatusOrder)
            ->first();
    }

    public function registrarOrdenes(array $ordenes)
    {
        $registro = new Tblorders();

        $registro->id_type_orders       = $ordenes['id_type_orders'];
        $registro->id_area              = $ordenes['id_area'];
        $registro->id_cat_machines      = $ordenes['id_cat_machines'];
        $registro->id_machines          = $ordenes['id_machines'];
        $registro->id_departaments      = $ordenes['id_departaments'];
        $registro->id_employee          = $ordenes['id_employee'];
        $registro->id_type_maintenances = $ordenes['id_type_maintenances'];
        $registro->id_status_order      = 1;
        $registro->id_priority          = $ordenes['id_priority'];
        $registro->order_folio          = $ordenes['order_folio'];
        $registro->problem_description  = $ordenes['problem_description'];
        $registro->conclusion           = $ordenes['conclusion'] ?? null;
        $registro->application          = Carbon::now();
        $registro->start_date           = Carbon::now();
        $registro->end_date             = Carbon::now();
        $registro->save();

        return $registro;
    }

    public function registrarEvidenciasOrden(int $idOrder, array $rutas)
    {
        foreach ($rutas as $path) {
            DB::table('tbl_orders_evidence')->insert([
                'id_order'           => $idOrder,
                'url_evidence_image' => $path
            ]);
        }
    }

    public function cancelarOrden(int $id_order)
    {
        $orden = Tblorders::findOrFail($id_order);
        $orden->cancellation_date = Carbon::now();
        $orden->id_status_order   = 4;
        $orden->save();

        return $orden;
    }

    public function obtenerStatusOrdenes()
    {
        return DB::table('Cat_Status_Order')->get();
    }

    public function ObtenerListaGeneralOrdenes(int|string|null $pkArea, int|string|null $pkStatus, ?int $idEmployee = null)
    {
        $query = Tblorders::select(
            DB::raw("CONCAT('ORD-', tbl_orders.id_order) as folio"),
            'tbl_orders.id_order',
            'cat_departaments.Departament_ES',
            'cat_areas.area',
            'cat_machines.cat_machines',
            'tbl_machines.machines',
            'tbl_employee.Name',
            'cat_type_maintenances.type_maintenances',
            'cat_type_orders.type_orders',
            'cat_status_order.status',
            'cat_priority.priority',
            'tbl_orders.order_folio',
            'tbl_orders.problem_description',
            'tbl_orders.conclusion',
            'tbl_orders.application',
            'tbl_orders.start_date',
            'tbl_orders.end_date'
        )
            ->join('cat_areas',             'cat_areas.id_area',                          'tbl_orders.id_area')
            ->join('cat_status_order',      'cat_status_order.id_status_order',           'tbl_orders.id_status_order')
            ->join('cat_machines',          'cat_machines.id_cat_machines',               'tbl_orders.id_cat_machines')
            ->join('tbl_machines',          'tbl_machines.id_machines',                   'tbl_orders.id_machines')
            ->join('cat_departaments',      'cat_departaments.id_departaments',           'tbl_orders.id_departaments')
            ->join('tbl_employee',          'tbl_employee.id_employee',                   'tbl_orders.id_employee')
            ->join('cat_type_maintenances', 'cat_type_maintenances.id_type_maintenances', 'tbl_orders.id_type_maintenances')
            ->join('cat_type_orders',       'cat_type_orders.id_type_orders',             'tbl_orders.id_type_orders')
            ->join('cat_priority',          'cat_priority.id_priority',                   'tbl_orders.id_priority')
            ->where([
                ['tbl_orders.id_area',         $pkArea],
                ['tbl_orders.id_status_order', $pkStatus]
            ]);

        if ($idEmployee !== null) {
            $query->where('tbl_orders.id_employee', $idEmployee);
        }

        return $query->get();
    }

    public function obtenerDetalleOrden(int $pkOrden)
    {
        return Tblorders::select(
                'tbl_orders.id_order',
                'tbl_orders.id_machines',
                'tbl_orders.id_departaments',
                'tbl_orders.id_area',
                'tbl_orders.id_employee',
                'tbl_orders.id_cat_machines',
                'tbl_orders.id_type_maintenances',
                'tbl_orders.id_type_orders',
                'tbl_orders.id_status_order',
                'tbl_orders.id_priority',
                'tbl_orders.order_folio as folio_orden',
                'tbl_orders.problem_description',
                'tbl_orders.conclusion',
                'tbl_orders.application',
                'tbl_orders.start_date',
                'tbl_orders.end_date',
                'cat_departaments.Departament_ES',
                'cat_areas.area',
                'cat_machines.cat_machines',
                'tbl_machines.machines',
                'tbl_employee.Name as usuario',
                'tbl_employee.busines_mail',
                'cat_type_maintenances.type_maintenances',
                'cat_type_orders.type_orders',
                'cat_status_order.status',
                'cat_status_order.color',
                'cat_priority.priority'
            )
            ->join('cat_areas',             'cat_areas.id_area',                          '=', 'tbl_orders.id_area')
            ->join('cat_status_order',      'cat_status_order.id_status_order',           '=', 'tbl_orders.id_status_order')
            ->join('cat_machines',          'cat_machines.id_cat_machines',               '=', 'tbl_orders.id_cat_machines')
            ->join('tbl_machines',          'tbl_machines.id_machines',                   '=', 'tbl_orders.id_machines')
            ->join('cat_departaments',      'cat_departaments.id_departaments',           '=', 'tbl_orders.id_departaments')
            ->join('tbl_employee',          'tbl_employee.id_employee',                   '=', 'tbl_orders.id_employee')
            ->join('cat_type_maintenances', 'cat_type_maintenances.id_type_maintenances', '=', 'tbl_orders.id_type_maintenances')
            ->join('cat_type_orders',       'cat_type_orders.id_type_orders',             '=', 'tbl_orders.id_type_orders')
            ->join('cat_priority',          'cat_priority.id_priority',                   '=', 'tbl_orders.id_priority')
            ->where('tbl_orders.id_order', $pkOrden)
            ->first();
    }

    public function obtenerUsuariosAsignadosOrden(int $pkOrden)
    {
        return TblOrdenesAsignacion::where('tbl_orders_assignment.id_order', $pkOrden)
            ->join('tbl_users', 'tbl_users.id_users', '=', 'tbl_orders_assignment.id_users')
            ->join('tbl_employee', 'tbl_employee.id_employee', '=', 'tbl_users.id_employee')
            ->pluck('tbl_employee.Name');
    }

    public function obtenerIdsUsuariosAsignadosOrden(int $pkOrden)
    {
        return TblOrdenesAsignacion::where('id_order', $pkOrden)
            ->pluck('id_users');
    }

    public function obtenerUsuariosAsignacion()
    {
        return TblUsuarios::join('tbl_employee', 'tbl_employee.id_employee', '=', 'tbl_users.id_employee')
            ->select(
                'tbl_users.id_users',
                'tbl_employee.Name',
                'tbl_employee.Paternal',
                'tbl_employee.Maternal'
            )
            ->where('tbl_users.active', 1)
            ->get();
    }

    public function obtenerMaquinasPorAreaYCategoria(int $pkArea, int $pkCatalogoMaquina)
    {
        return DB::table('tbl_machines')
            ->where('id_area', $pkArea)
            ->where('id_cat_machines', $pkCatalogoMaquina)
            ->select('id_machines', 'machines')
            ->get();
    }

    public function obtenerEvidenciaOrden(int $pkOrden)
    {
        $baseUrl = config('app.url', 'http://localhost:8000');

        return DB::table('tbl_orders_evidence')
            ->select(
                'id_eviden_order',
                'url_evidence_image',
                DB::raw("CONCAT('{$baseUrl}/storage/', url_evidence_image) AS public_url"),
                DB::raw("CONCAT('" . public_path('storage/') . "', url_evidence_image) AS absolute_path")
            )
            ->where('id_order', $pkOrden)
            ->get();
    }

    public function obtenerResolucionOrden(int $pkOrden)
    {
        $baseUrl = config('app.url', 'http://localhost:8000');

        return DB::table('tbl_orders_resolution')
            ->select(
                'id_resolution_order',
                'id_order',
                'url_resolution_image',
                DB::raw("CONCAT('{$baseUrl}/storage/', url_resolution_image) AS public_url"),
                DB::raw("CONCAT('" . public_path('storage/') . "', url_resolution_image) AS absolute_path")
            )
            ->where('id_order', $pkOrden)
            ->get();
    }

    public function eliminarEvidenciaOrden(int $id_eviden_order)
    {
        DB::table('tbl_orders_evidence')
            ->where('id_eviden_order', $id_eviden_order)
            ->delete();
    }

    public function actualizarOrden(int $id, array $orden)
    {
        Tblorders::where('id_order', $id)
            ->update([
                'id_machines'          => $orden['id_machines'],
                'id_cat_machines'      => $orden['id_cat_machines'],
                'id_departaments'      => $orden['id_departaments'],
                'id_area'              => $orden['id_area'],
                'id_employee'          => $orden['id_employee'],
                'id_type_maintenances' => $orden['id_type_maintenances'],
                'id_type_orders'       => $orden['id_type_orders'],
                'id_priority'          => $orden['id_priority'],
                'order_folio'          => $orden['order_folio'],
                'problem_description'  => $orden['problem_description'],
                'conclusion'           => $orden['conclusion'] ?? null
            ]);
    }

    public function actualizarEvidencias(int $id_order, array $evidencias)
    {
        DB::table('tbl_orders_evidence')
            ->where('id_order', $id_order)
            ->delete();

        $this->registrarEvidenciasOrden($id_order, $evidencias);
    }

    public function depurarAsignacionesOrden(int $pkOrden)
    {
        TblOrdenesAsignacion::where('id_order', $pkOrden)->delete();
    }

    public function asignarOrden(array $orden)
    {
        $registro = new TblOrdenesAsignacion();
        $registro->id_order        = $orden['id_order'];
        $registro->id_users        = $orden['id_users'];
        $registro->assignment_date = Carbon::now();
        $registro->save();
    }

    public function cambiarStatusOrder(int $pkOrden, int $status)
    {
        $orden = Tblorders::findOrFail($pkOrden);
        $orden->id_status_order = $status;
        $orden->save();
    }

    public function obtenerCorreoYFolioOrden(int $id_order)
    {
        return DB::table('tbl_orders')
            ->select(
                'tbl_orders.id_order',
                DB::raw("CONCAT('ORD-', tbl_orders.id_order) as folio"),
                'tbl_employee.busines_mail as user_business_mail',
                'tbl_employee.Name as employee_name',
                'tbl_machines.machines'
            )
            ->join('tbl_employee', 'tbl_employee.id_employee', '=', 'tbl_orders.id_employee')
            ->leftJoin('tbl_machines', 'tbl_machines.id_machines', '=', 'tbl_orders.id_machines')
            ->where('tbl_orders.id_order', $id_order)
            ->first();
    }

    public function obtenerMensajesOrden(int $id_order)
    {
        return DB::table('tbl_chat_mesages_order')
            ->join('tbl_users', 'tbl_users.id_users', '=', 'tbl_chat_mesages_order.id_users')
            ->join('tbl_employee', 'tbl_employee.id_employee', '=', 'tbl_users.id_employee')
            ->select(
                'tbl_chat_mesages_order.id_messages_order',
                'tbl_chat_mesages_order.id_order',
                'tbl_chat_mesages_order.id_users',
                DB::raw("CONCAT(tbl_employee.Name, ' ', tbl_employee.Paternal) as remitente"),
                'tbl_chat_mesages_order.id_business_mail as business_mail',
                'tbl_chat_mesages_order.subject',
                'tbl_chat_mesages_order.content',
                'tbl_chat_mesages_order.send_date',
                'tbl_chat_mesages_order.id_status_order'
            )
            ->where('tbl_chat_mesages_order.id_order', $id_order)
            ->orderBy('tbl_chat_mesages_order.send_date', 'asc')
            ->get();
    }

    public function registrarMensajeOrden(array $data)
    {
        return DB::table('tbl_chat_mesages_order')->insertGetId([
            'id_order'         => $data['id_order'],
            'id_users'         => $data['id_users'],
            'id_business_mail' => $data['busines_mail'],
            'subject'          => $data['subject'],
            'content'          => $data['content'],
            'id_status_order'  => $data['id_status_order'] ?? 1,
            'send_date'        => Carbon::now(),
            'created_at'       => Carbon::now(),
            'updated_at'       => Carbon::now(),
        ]);
    }

    public function registrarSolucionOrden(array $data)
    {
        return DB::table('cat_solution_order')->insertGetId([
            'id_order'        => $data['id_order'],
            'id_status_order' => $data['id_status_order'],
            'id_users'        => $data['id_users'] ?? 1,
            'business_mail'   => $data['business_mail'],
            'id_coin'         => $data['id_coin'],
            'order_cost'      => $data['order_cost'],
            'resolution'      => $data['resolution'],
            'spare_parts'     => $data['spare_parts'],
            'materials'       => $data['materials'],
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ]);
    }

    public function registrarImagenesResolucion(int $id_order, array $rutas)
    {
        foreach ($rutas as $path) {
            DB::table('tbl_orders_resolution')->insert([
                'id_order'             => $id_order,
                'url_resolution_image' => $path,
                'created_at'           => Carbon::now(),
                'updated_at'           => Carbon::now()
            ]);
        }
    }

    public function obtenerDetalleSolucionOrden(int $id_order)
    {
        return DB::table('cat_solution_order')
            ->join('cat_coins', 'cat_coins.id_coins', '=', 'cat_solution_order.id_coin')
            ->select(
                'cat_solution_order.*',
                'cat_coins.coin'
            )
            ->where('cat_solution_order.id_order', $id_order)
            ->first();
    }

    public function eliminarOrden(int $id_order)
    {
        $orden = Tblorders::findOrFail($id_order);
        return $orden->delete();
    }

    public function obtenerDatosPdfOrden(int $pkOrden)
    {
        $orden = Tblorders::select(
                'tbl_orders.id_order',
                DB::raw("CONCAT('ORD-', tbl_orders.id_order) as folio"),
                'tbl_orders.problem_description',
                'tbl_orders.conclusion',
                'tbl_orders.application',
                'tbl_orders.start_date',
                'tbl_orders.end_date',
                'tbl_orders.order_folio',
                'cat_areas.area',
                'cat_departaments.Departament_ES',
                'cat_machines.cat_machines',
                'tbl_machines.machines',
                'tbl_employee.Name',
                'tbl_employee.busines_mail',
                'cat_type_maintenances.type_maintenances',
                'cat_type_orders.type_orders',
                'cat_status_order.status',
                'cat_priority.priority'
            )
            ->join('cat_areas',             'cat_areas.id_area',                          '=', 'tbl_orders.id_area')
            ->join('cat_status_order',      'cat_status_order.id_status_order',           '=', 'tbl_orders.id_status_order')
            ->join('cat_machines',          'cat_machines.id_cat_machines',               '=', 'tbl_orders.id_cat_machines')
            ->join('tbl_machines',          'tbl_machines.id_machines',                   '=', 'tbl_orders.id_machines')
            ->join('cat_departaments',      'cat_departaments.id_departaments',           '=', 'tbl_orders.id_departaments')
            ->join('tbl_employee',          'tbl_employee.id_employee',                   '=', 'tbl_orders.id_employee')
            ->join('cat_type_maintenances', 'cat_type_maintenances.id_type_maintenances', '=', 'tbl_orders.id_type_maintenances')
            ->join('cat_type_orders',       'cat_type_orders.id_type_orders',             '=', 'tbl_orders.id_type_orders')
            ->join('cat_priority',          'cat_priority.id_priority',                   '=', 'tbl_orders.id_priority')
            ->where('tbl_orders.id_order', $pkOrden)
            ->first();

        if (!$orden) {
            return null;
        }

        return [
            'orden'             => $orden,
            'usuariosAsignados' => $this->obtenerUsuariosAsignadosOrden($pkOrden),
            'evidencias'        => $this->obtenerEvidenciaOrden($pkOrden),
            'resoluciones'      => $this->obtenerResolucionOrden($pkOrden),
            'solucion'          => $this->obtenerDetalleSolucionOrden($pkOrden)
        ];
    }

    public function registrarPdfResolucion(int $id_order, string $path)
    {
        return DB::table('tbl_orders_resolution')->insert([
            'id_order'             => $id_order,
            'url_resolution_image' => $path,
            'created_at'           => Carbon::now(),
            'updated_at'           => Carbon::now()
        ]);
    }
}