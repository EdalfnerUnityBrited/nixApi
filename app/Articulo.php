<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'nombre',
        'categoria_articulo',
        'descripcion',
        'precioPor',
        'precio',
        'id_catalogoServicio',
    ];
}
