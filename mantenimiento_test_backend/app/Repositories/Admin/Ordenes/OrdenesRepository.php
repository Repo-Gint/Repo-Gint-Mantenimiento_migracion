<?php

namespace App\Repositories\Admin\Ordenes;

use App\Models\TblOrdenesAsignacion;
use App\Models\Tblorders;
use App\Models\TblUsuarios;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrdenesRepository
{
    public function registrarOrdenes($ordenes, $files)
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
        $registro->application          = Carbon::now();
        $registro->start_date           = Carbon::now();
        $registro->end_date             = Carbon::now();
        $registro->save();

        if ($files) {
            foreach ($files as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();

                $path = $file->storeAs('orders', $filename, 'public');

                DB::table('tbl_orders_evidence')->insert([
                    'id_order'           => $registro->id_order,
                    'url_evidence_image' => $path
                ]);
            }
        }
        return $registro;
    }

    public function cancelarOrden($id_order)
    {
        $orden = Tblorders::findOrFail($id_order);
        $orden->cancellation_date = Carbon::now();
        $orden->id_status_order = 4;
        $orden->save();
        return $orden;
    }

    public function obtenerStatusOrdenes()
    {
        return DB::table('Cat_Status_Order')
            ->get();
    }

    public function ObtenerListaGeneralOrdenes($pkArea, $pkStatus, $idUser = null, $idRol = null)
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
            'tbl_orders.application',
            'tbl_orders.start_date',
            'tbl_orders.end_date'
        )
            ->join('cat_areas',             'cat_areas.id_area',                    'tbl_orders.id_area')
            ->join('cat_status_order',      'cat_status_order.id_status_order',     'tbl_orders.id_status_order')
            ->join('cat_machines',          'cat_machines.id_cat_machines',         'tbl_orders.id_cat_machines')
            ->join('tbl_machines',          'tbl_machines.id_machines',             'tbl_orders.id_machines')
            ->join('cat_departaments',      'cat_departaments.id_departaments',     'tbl_orders.id_departaments')
            ->join('tbl_employee',          'tbl_employee.id_employee',             'tbl_orders.id_employee')
            ->join('cat_type_maintenances', 'cat_type_maintenances.id_type_maintenances', 'tbl_orders.id_type_maintenances')
            ->join('cat_type_orders',       'cat_type_orders.id_type_orders',       'tbl_orders.id_type_orders')
            ->join('cat_priority',          'cat_priority.id_priority',             'tbl_orders.id_priority')
            ->where([
                ['tbl_orders.id_area',         $pkArea],
                ['tbl_orders.id_status_order', $pkStatus]
            ]);

        // FILTRO ESTRICTO POR ROL: 
        // Si el rol es Usuario (Rol 1), filtramos exclusivamente por las órdenes de su empleado asociado.
        if ((int)$idRol === 1) {
            $idEmployee = null;
            if ($idUser) {
                $idEmployee = DB::table('tbl_users')->where('id_users', $idUser)->value('id_employee');
            }

            if ($idEmployee) {
                $query->where('tbl_orders.id_employee', $idEmployee);
            } else {
                // Si no se encuentra empleado asociado, devolvemos una condición vacía por seguridad
                $query->where('tbl_orders.id_employee', -9999);
            }
        }

        return $query->get();
    }

    public function obtenerDetalleOrden($pkOrden)
    {
        $orden = Tblorders::select(
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
            ->join('cat_areas',             'cat_areas.id_area',                    '=', 'tbl_orders.id_area')
            ->join('cat_status_order',      'cat_status_order.id_status_order',     '=', 'tbl_orders.id_status_order')
            ->join('cat_machines',          'cat_machines.id_cat_machines',         '=', 'tbl_orders.id_cat_machines')
            ->join('tbl_machines',          'tbl_machines.id_machines',             '=', 'tbl_orders.id_machines')
            ->join('cat_departaments',      'cat_departaments.id_departaments',     '=', 'tbl_orders.id_departaments')
            ->join('tbl_employee',          'tbl_employee.id_employee',             '=', 'tbl_orders.id_employee')
            ->join('cat_type_maintenances', 'cat_type_maintenances.id_type_maintenances', '=', 'tbl_orders.id_type_maintenances')
            ->join('cat_type_orders',       'cat_type_orders.id_type_orders',       '=', 'tbl_orders.id_type_orders')
            ->join('cat_priority',          'cat_priority.id_priority',             '=', 'tbl_orders.id_priority')
            ->where('tbl_orders.id_order', $pkOrden)
            ->first();

        return $orden;
    }

    public function obtenerUsuariosAsignadosOrden(int $pkOrden)
    {
        $usuariosAsignados = TblOrdenesAsignacion::where('tbl_orders_assignment.id_order', $pkOrden)
            ->join('tbl_users', 'tbl_users.id_users', '=', 'tbl_orders_assignment.id_users')
            ->join('tbl_employee', 'tbl_employee.id_employee', '=', 'tbl_users.id_employee')
            ->pluck('tbl_employee.Name');

        return $usuariosAsignados;
    }

    public function obtenerIdsUsuariosAsignadosOrden($pkOrden)
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

    public function obtenerMaquinasPorAreaYCategoria($pkArea, $pkCatalogoMaquina)
    {
        return DB::table('tbl_machines')
            ->where('id_area', $pkArea)
            ->where('id_cat_machines', $pkCatalogoMaquina)
            ->select('id_machines', 'machines')
            ->get();
    }

    public function obtenerEvidenciaOrden(int $pkOrden)
    {
        $query = DB::table('tbl_orders_evidence')
            ->select(
                'id_eviden_order',
                DB::raw('CONCAT("http://localhost:8000/storage/", url_evidence_image) AS url_evidence_image')
            )
            ->where('id_order', $pkOrden);

        return $query->get();
    }

    public function eliminarEvidenciaOrden(int $id_eviden_order)
    {
        DB::table('tbl_orders_evidence')
            ->where('id_eviden_order', $id_eviden_order)
            ->delete();
    }

    public function actualizarOrden($id, $orden)
    {
        Tblorders::where('id_order', $id)
            ->update([
                'id_machines'         => $orden['id_machines'],
                'id_cat_machines'     => $orden['id_cat_machines'],
                'id_departaments'     => $orden['id_departaments'],
                'id_area'             => $orden['id_area'],
                'id_employee'         => $orden['id_employee'],
                'id_type_maintenances' => $orden['id_type_maintenances'],
                'id_type_orders'      => $orden['id_type_orders'],
                'id_priority'         => $orden['id_priority'],
                'order_folio'         => $orden['order_folio'],
                'problem_description' => $orden['problem_description']
            ]);
    }

    public function actualizarEvidencias($id_order, $evidencias)
    {
        DB::table('tbl_orders_evidence')
            ->where('id_order', $id_order)
            ->delete();

        foreach ($evidencias as $url_evidence_image) {
            DB::table('tbl_orders_evidence')->insert([
                'id_order'           => $id_order,
                'url_evidence_image' => $url_evidence_image
            ]);
        }
    }

    public function depurarAsignacionesOrden($pkOrden)
    {
        TblOrdenesAsignacion::where('id_order', $pkOrden)->delete();
    }

    public function asignarOrden(array $orden)
    {
        $registro = new TblOrdenesAsignacion();
        $registro->id_order         = $orden['id_order'];
        $registro->id_users         = $orden['id_users'];
        $registro->assignment_date  = Carbon::now();
        $registro->save();
    }

    public function cambiarStatusOrder($pkOrden, $status)
    {
        $orden = Tblorders::findOrFail($pkOrden);
        $orden->id_status_order = $status;
        $orden->save();
    }

    public function obtenerCorreoYFolioOrden(int $id_order)
    {
        return Tblorders::select(
                'tbl_orders.id_order',
                DB::raw("CONCAT('ORD-', tbl_orders.id_order) as folio"),
                'tbl_employee.busines_mail as user_business_mail',
                'tbl_employee.Name as employee_name'
            )
            ->join('tbl_employee', 'tbl_employee.id_employee', '=', 'tbl_orders.id_employee')
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
        // Obtenemos automáticamente el business_mail de la orden si no viene explícito
        $ordenInfo = $this->obtenerCorreoYFolioOrden($data['id_order']);
        $businessMail = $data['business_mail'] ?? ($ordenInfo->user_business_mail ?? null);

        return DB::table('cat_solution_order')->insertGetId([
            'id_order'       => $data['id_order'],
            'id_status_order'=> $data['id_status_order'],
            'id_users'       => $data['id_users'] ?? 1,
            'business_mail'  => $businessMail,
            'id_coin'        => $data['id_coin'],
            'order_cost'     => $data['order_cost'],
            'resolution'     => $data['resolution'],
            'spare_parts'    => $data['spare_parts'],
            'materials'      => $data['materials'],
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now(),
        ]);
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
}