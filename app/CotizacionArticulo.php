<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CotizacionArticulo extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'cantidad',
        'id_cotizacion',
        'id_articulo'
    ];
}
