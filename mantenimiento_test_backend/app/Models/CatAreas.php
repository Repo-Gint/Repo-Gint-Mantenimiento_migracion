<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatAreas extends Model {
    use HasFactory;
    public $timestamps    = false;
    protected $primaryKey = 'id_area';
    protected $table      = 'cat_areas';

    protected $fillable = [
        'id_area',
        'area',
        'acronym',
        'color',
        'active'
    ];
}