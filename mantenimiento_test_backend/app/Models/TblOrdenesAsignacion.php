<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblOrdenesAsignacion extends Model
{
    use HasFactory;
    public $timestamps    = false;
    protected $primaryKey = 'id_order_assignment';
    protected $table      = 'tbl_orders_assignment';

    protected $fillable   = [
        'id_order_assignment', 
        'id_order',
        'id_users',
        'assignment_date',
    ];
}