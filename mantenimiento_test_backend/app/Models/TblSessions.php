<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblSessions extends Model
{
    use HasFactory;
    public $timestamps    = false;
    protected $primaryKey = 'id_sessions';
    protected $table      = 'tbl_sessions';

    protected $fillable   = [
        'id_sessions', 
        'id_users', 
        'token'
    ];
}