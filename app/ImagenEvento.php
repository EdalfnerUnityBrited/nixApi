<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImagenEvento extends Model
{
     public $timestamps = false;
    protected $fillable = [
        'imagen',
        'id_evento',
    ];
         protected $hidden = [
        'id'
    ];
}
