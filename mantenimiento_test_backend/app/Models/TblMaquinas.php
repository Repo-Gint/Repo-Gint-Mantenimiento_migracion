<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMaquinas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id_machines';
    protected $table      = 'tbl_machines';
    
    protected $fillable = [
        'id_machines',
        'id_aea',
        'id_cat_machines',
        'machines',
        'brand',
        'model',
        'year',
        'serial',
        'weight',
        'voltaje',
        'amperage',
        'frequency',
        'kva',
        'active'
    ];
}
