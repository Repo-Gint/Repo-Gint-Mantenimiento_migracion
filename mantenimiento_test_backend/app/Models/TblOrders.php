<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tblorders extends Model {
    use HasFactory; 
    public $timestamps      = false; 
    protected $primaryKey = 'id_order'; 
    protected $table        = 'tbl_orders'; 

    protected $fillable = [
        'id_order',
        'id_machines',
        'id_cat_machines',    
        'id_departaments',
        'id_area', 
        'id_employee',
        'id_type_maintenances', 
        'id_type_orders',       
        'id_status_order', 
        'id_priority',
        'order_folio',
        'problem_description',
        'applicant',
        'application',
        'start_date',
        'cancellation_date', 
        'end_date'
    ];
}