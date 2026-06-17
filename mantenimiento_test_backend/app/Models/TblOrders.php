<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tblorders extends Model {
    use HasFactory; 
    public $timestamps    = false; 
    protected $primariKey = 'id_orders'; 
    protected $table      = 'tbl_orders'; 

    protected $fillable = [
        'id_order',
        'id_machines',
        'id_departament',
        'id_area', 
        'id:employee',
        'id_maintenance',
        'id_type_order', 
        'id_prioridad',
        'order_folio',
        'problem_description',
        'applicant',
        'application date', 
        'start_date', 
        'end_date'
    ];
}   