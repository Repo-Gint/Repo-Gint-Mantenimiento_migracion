<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblEmployee extends Model {
    use HasFactory;
    public    $timestamps = false;
    protected $primaryKey = 'id_employe';
    protected $table = 'tbl_employee';

    protected $fillable = [
        'id_employe',
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
