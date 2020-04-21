<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cupo extends Model
{
     public $timestamps = false;
    protected $fillable = [
  		'id_evento',
        'cupo',
        'asistentes',

    ];
         protected $hidden = [
        'id'
    ];
}
