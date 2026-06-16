<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatRoles extends Model {
    use       HasFactory;
    public    $timestamps = false;
    protected $primaryKey = 'id_roles';
    protected $table      =  'cat_roles';
    
    protected $fillable = [
        'id_roles',
        'roles',
        'description'
    ];
}