<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatTipoMantenimiento extends Model {
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id_type_maintenances';
    protected $table      = 'cat_type_maintenances';

    protected $fillable = [
        'id_type_maintenances', 
        'type_maintenances',
        'color',
        'acronym',
        'active'
    ];
}
