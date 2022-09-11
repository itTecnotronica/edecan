<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App_inscripcion_en_evento extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'app_inscripciones_en_eventos';  
}
