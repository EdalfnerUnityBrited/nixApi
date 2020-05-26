<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Citas extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'id_usuario',
        'id_servicio',
        'fecha',
        'hora'
    ];

}
