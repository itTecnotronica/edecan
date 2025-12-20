<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inscripcion_evento extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'app_inscripciones_en_eventos';  
}
