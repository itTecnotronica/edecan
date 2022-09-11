<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instancia_de_seguimiento extends Model
{
	protected $guarded = ['id'];    
	
    public function estado_de_seguimiento()
    {
        return $this->belongsTo('App\Estado_de_seguimiento');
    }

    public function inscripcion()
    {
        return $this->belongsTo('App\Inscripcion');
    }

    protected $table = 'instancias_de_seguimiento';  
}
