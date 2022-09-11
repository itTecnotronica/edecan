<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alumno_avanzado extends Model
{
	protected $guarded = ['id'];    

    public function inscripcion()
    {
        return $this->belongsTo('App\Inscripcion');
    }

    public function estado_de_seguimiento()
    {
        return $this->belongsTo('App\Estado_de_seguimiento');
    }

    protected $table = 'alumnos_avanzados';  
}
