<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{
    //
    public $timestamps = false;
    protected $fillable = [
        'nombre_evento',
        'privacidad',
        'categoria_evento',
        'id_creador',
        'fecha',
        'hora',
        'lugar',
        'descripcion',

    ];
         protected $hidden = [
        'id',
    ];
}
