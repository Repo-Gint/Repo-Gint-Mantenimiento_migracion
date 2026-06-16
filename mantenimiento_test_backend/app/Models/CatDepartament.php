<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatDepartament extends Model {
    use HasFactory; 
    public $timestamps    = false;
    protected $primaryKey = 'id_departaments';
    protected $table      = 'cat_departaments';

    protected $fillable = [
        'id_departaments',
        'Departament_ES', 
        'Departament_EN', 
        'Acronym',
        'Active'
    ];
}
