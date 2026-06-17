<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatTipoOrden extends Model {
    use HasFactory; 
    public $timestamps = false;
    protected $primaryKey = 'id_type_orders';
    protected $table = 'cat_type_orders';
    
    protected $fillable = [
        'id_type_orders',
        'type', 
        'acronym', 
        'color', 
        'active'
    ];
}
