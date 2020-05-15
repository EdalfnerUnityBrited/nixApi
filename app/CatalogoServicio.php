<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatalogoServicio extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'nombre',
        'categoriaevento',
        'direccion',
        'telefono',
        'horarioApertura',
        'horarioCierre',
        'calificacion',
        'id_usuario',
        'lunes',
        'martes',
        'miercoles',
        'jueves',
        'viernes',
        'sabado',
        'domingo',
    ];
}