<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Imagenarticulo extends Model
{
     public $timestamps = false;
    protected $fillable = [
        'imagen',
        'id_articulo',
    ];
         protected $hidden = [
        'id'
    ];
}
