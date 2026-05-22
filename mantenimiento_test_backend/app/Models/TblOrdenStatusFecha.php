<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblOrdenStatusFecha extends Model {
    use HasFactory;
    public $timestamps    = false;
    protected $primaryKey = 'id_orders_status_date';
    protected $table      = 'tbl_order_status_date'; 

    protected $fillable = [
        'id_order_status_date',
        'id_orden', 
        'date_pendient_order',
        'date_'
    ];


}
  