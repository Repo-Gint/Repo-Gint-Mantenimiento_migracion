<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TblUsuarios extends Model {
    use HasFactory; 
    public $timestamps = false;
    protected $primaryKey = 'id_orders';
    protected $table = 'tbl_users';

    protected $fillable = [
        'id_usuario',
        'id_rol_users',
        'id_employee', 
        'name',
        'password',
        'busines_mail',
    ];
}