<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'nombre',
        'categoria_articulo',
        'descripcion',
        'precioPor',
        'precio',
        'id_catalogoServicio',
    ];
    public function scopeNombre($query, $nombre)
    {
        if($nombre)
            return $query->orWhere('nombre', 'LIKE', '%'.$nombre.'%');
    }
    public function scopeCategoria($query, $categoria)
    {
        if($categoria)
            return $query->where('categoria_articulo', 'LIKE', '%'.$categoria.'%');
    }
    public function scopePrecio($query, $precioInicio, $precioFin)
    {
        if($precioFin)
            return $query->where('precio','>',$precioInicio)
                        ->where('precio','<=',$precioFin);

    }
     
}
