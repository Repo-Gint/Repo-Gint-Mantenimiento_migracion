<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblEmpleados extends Model {
    use HasFactory;
    public    $timestamps = false;
    protected $primaryKey = 'id_employee';
    protected $table = 'tbl_employee';

    protected $fillable = [
        'id_employee',
        'id_payroll',
        'id_departament',
        'Code',
        'Name',
        'Paternal',
        'Maternal',
        'Photo',
        'busines_mail',
        'active'
    ];
}
