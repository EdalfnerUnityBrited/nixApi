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
        'fotoPrincipal',
        'municipio',
        'created_at'
    ];

    public function scopeNombre_evento($query, $nombre_evento)
    {
    	if($nombre_evento)
    		return $query->Where('nombre_evento', 'LIKE', '%'.$nombre_evento.'%')
        ->Where('privacidad','=',"0");
    }

        public function scopeCategoria_evento($query, $categoria_evento)
    {
    	if($categoria_evento)
    		return $query->Where('categoria_evento','LIKE','%'.$categoria_evento.'%')
        ->Where('privacidad','=',"0");
    }

            public function scopeCover($query, $cover)
    {
    	if($cover)
    		return $query->Where('cover','<',$cover)
        ->Where('privacidad','=',"0");
    }
            public function scopeCupo($query, $cupo)
    {
    	if($cupo)
    		return $query->Where('cupo','<',$cupo)
        ->Where('privacidad','=',"0");
    }
            public function scopeLugar($query, $lugar)
    {
    	if($lugar)
    		return $query->orWhere('municipio','LIKE','%'.$lugar.'%')
        ->Where('privacidad','=',"0");
    }
            public function scopeFechaInicio($query, $fechaInicio,$fechaFinal)
    {
    	if($fechaInicio){
    		return $query->Where('fecha','>',$fechaInicio)
                    ->Where('fecha','<',$fechaFinal)
                    ->Where('privacidad','=',"0");
        }

    }
          
    
}

