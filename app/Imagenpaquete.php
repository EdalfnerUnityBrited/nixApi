<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Imagenpaquete extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'imagen',
        'id_paquete',
    ];
         protected $hidden = [
        'id'
    ];
}
