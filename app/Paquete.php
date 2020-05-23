<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'nombre',
        'descripcion',
        'precio',
        'fotoPaquete',
        'id_servicio'
    ];
    public function scopeNombre($query, $nombre)
    {
    	if($nombre)
    		return $query->orWhere('nombre', 'LIKE', '%'.$nombre.'%');
    }
    public function scopePrecio($query, $precioInicio, $precioFin)
    {
    	if($precioInicio)
    		return $query->Where('precio','<',$precioInicio)
    					->Where('precio','>',$precioFin);

    }
   


}
