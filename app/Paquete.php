<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'nombre',
        'descripcion',
        'precio',
        'fotoPaquete',
        'id_servicio'
    ];
}
