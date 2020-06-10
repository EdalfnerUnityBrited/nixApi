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
    		return $query->orWhere('nombre_evento', 'LIKE', '%'.$nombre_evento.'%')
        ->where('privacidad','=',"0");
    }

        public function scopeCategoria_evento($query, $categoria_evento)
    {
    	if($categoria_evento)
    		return $query->where('categoria_evento','LIKE','%'.$categoria_evento.'%')
        ->where('privacidad','=',"0");
    }

            public function scopeCover($query, $cover)
    {
    	if($cover)
    		return $query->where('cover','<',$cover)
        ->where('privacidad','=',"0");
    }
            public function scopeCupo($query, $cupo)
    {
    	if($cupo)
    		return $query->where('cupo','<',$cupo)
        ->where('privacidad','=',"0");
    }
            public function scopeLugar($query, $lugar)
    {
    	if($lugar)
    		return $query->where('municipio','LIKE','%'.$lugar.'%')
        ->where('privacidad','=',"0");
    }
            public function scopeFechaInicio($query, $fechaInicio,$fechaFinal)
    {
    	if($fechaInicio){
    		return $query->where('fecha','>',$fechaInicio)
                    ->where('fecha','<',$fechaFinal)
                    ->where('privacidad','=',"0");
        }

    }
          
    
}

