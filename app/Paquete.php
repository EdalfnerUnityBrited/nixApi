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
    		return $query->where('nombre', 'LIKE', '%'.$nombre.'%');
    }
    public function scopePrecio($query, $precioInicio, $precioFinal)
    {
    	if($precioFinal){
    		return $query->where('precio','>',$precioInicio)
    					->where('precio','<=',$precioFinal);
        }

    }
   


}
