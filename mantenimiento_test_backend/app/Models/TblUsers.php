<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Model {
    use HasFactory; 
    public $timestamps = false;
    protected $primaryKey = 'id_orders';
    protected $table = 'tbl_orders';

    protected $fillable = [
        'id_usuario',
        'id_rol_users',
        'id_employee', 
        'name',
        'email',
        'password',
        'id_busines_mail',
    ];
}