<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaqueteArticulo extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'id_paquete',
        'id_articulo'
    ];
}
