<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cotizaciones extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'estado',
        'total',
        'id_servicio',
        'id_evento'
    ];
}
