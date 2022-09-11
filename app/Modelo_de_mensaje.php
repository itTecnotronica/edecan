<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modelo_de_mensaje extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'modelos_de_mensajes';  
}
