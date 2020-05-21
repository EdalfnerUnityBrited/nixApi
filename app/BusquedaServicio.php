<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusquedaServicio extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'municipio',
        'precio',
        'cantidad',
        'categoria_evento'
    ];
    public function scopeMunicipio($query, $municipio)
    {
    	if($municipio)
    		return $query->orWhere('nombre_evento', 'LIKE', '%'.$nombre_evento.'%')
        ->Where('privacidad','=',"0");
    }
}
