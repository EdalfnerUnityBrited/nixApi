<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{
    //
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'nombre_evento',
        'privacidad',
        'categoria_evento',
        'id_creador',
        'fecha',
        'hora',
        'lugar',
        'descripcion',
        'cupo',
        'cover',
        'fotoPrincipal'
    ];

    public function scopeNombre_evento($query, $nombre_evento)
    {
    	if($nombre_evento)
    		return $query->Where('nombre_evento', 'LIKE', '%'.$nombre_evento.'%');
    }

        public function scopeCategoria_evento($query, $categoria_evento)
    {
    	if($categoria_evento)
    		return $query->Where('categoria_evento','LIKE','%'.$categoria_evento.'%');
    }

            public function scopeCover($query, $cover)
    {
    	if($cover)
    		return $query->Where('cover','<',$cover);
    }
            public function scopeCupo($query, $cupo)
    {
    	if($cupo)
    		return $query->Where('cupo','<',$cupo);
    }
            public function scopeLugar($query, $lugar)
    {
    	if($lugar)
    		return $query->orWhere('lugar','LIKE','%'.$lugar.'%');
    }
            public function scopeFecha($query, $fecha)
    {
    	if($fecha)
    		return $query->Where('fecha','LIKE','%'.$fecha.'%');
    }
            public function scopeHora($query, $hora)
    {
    	if($hora)
    		return $query->orWhere('hora','LIKE','%'.$hora.'%');
    }
}

