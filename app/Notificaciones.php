<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notificaciones extends Model
{
     public $timestamps = false;
    protected $fillable = [
    	'id',
        'id_receptor',
        'contenido',
        'fechaInicio',
        'fechaFin',
        'tipoNotificacion',
        'id_evento'
    ];
}
