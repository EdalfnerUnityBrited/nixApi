<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zonaservicio extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'id_catalogoServicio',
        'municipio'
    ];
}
