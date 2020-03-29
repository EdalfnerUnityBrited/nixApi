<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Chats extends Model
{
	public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'id_proveedor',
    ];

     protected $hidden = [
        'id',
    ];
}
