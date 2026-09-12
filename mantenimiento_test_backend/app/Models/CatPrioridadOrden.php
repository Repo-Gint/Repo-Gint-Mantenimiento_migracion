<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatPrioridadOrden extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id_priority';
    protected $table      = 'cat_priority';

    protected $fillable = [
        'id_priority',
        'priority',
        'description',
        'color',
        'active',
    ];
}
