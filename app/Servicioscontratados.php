<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Servicioscontratados extends Model
{
        public $timestamps = false;
    protected $fillable = [
    	'id',
        'estado_servicio',
        'id_servicio',
        'fecha',
        'hora',
        'metodo_pago',
        'id_evento',
        'desglose',
    ];
}
