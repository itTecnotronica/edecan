<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inscripcion_evento extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'tb_inscripcion_en_eventos';  
}
