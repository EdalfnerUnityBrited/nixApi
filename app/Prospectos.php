<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prospectos extends Model
{
    public $timestamps = false;
    protected $fillable = [
    	'id',
        'estado',
        'confirmacionasistencia',
        'id_evento',
        'id_prospecto',
        'invited_at'
    ];



}
