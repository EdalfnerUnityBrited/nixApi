<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    public $timestamps = false;
    protected $fillable = ['tipoUsuario','nombre','apellidoP','apellidoM','email','fechaNac','contrasena','telefono','calificacion','fotoPerfil'];
}
