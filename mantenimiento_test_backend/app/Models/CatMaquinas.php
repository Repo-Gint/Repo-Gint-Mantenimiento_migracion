<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatMaquinas extends Model {
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id_cat_machines';
    protected $table      = 'cat_machines'; 
    protected $fillable = [
        'id_cat_machines', 
        'cat_machines',
        'active'
    ];
}