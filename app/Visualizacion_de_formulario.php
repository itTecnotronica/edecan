<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visualizacion_de_formulario extends Model
{
	protected $guarded = ['id'];    

    public function inscripcion()
    {
        return $this->belongsTo('App\Inscripcion');
    }

    public function sesion()
    {
        return $this->belongsTo('App\Sesion');
    }

    public function solicitud()
    {
        return $this->belongsTo('App\Solicitud');
    }    


    public function formulario()
    {
        return $this->belongsTo('App\Formulario');
    }    

    protected $table = 'visualizaciones_de_formulario';

}
