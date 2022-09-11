<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Encuesta_de_satisfaccion extends Model
{
	protected $guarded = ['id'];    

    
    public function inscripcion()
    {
        return $this->belongsTo('App\Inscripcion');
    } 

    protected $table = 'encuestas_de_satisfaccion';

}
