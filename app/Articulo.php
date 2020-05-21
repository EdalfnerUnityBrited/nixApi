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
            return $query->Where('categoria_articulo', 'LIKE', '%'.$categoria.'%');
    }
    public function scopePrecio($query, $precioInicio, $precioFin)
    {
        if($precioInicio)
            return $query->Where('precio','<',$precio)
                        ->Where('precio','>',$precioFin);

    }
    public function scopePrecioPor($query, $precioPor)
    {
        if($precioPor)
            return $query->Where('precioPor', 'LIKE', '%'.$precioPor.'%');
    }
     
}
