<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cambio_de_solicitud_de_inscripcion extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

    	$descripcion = $this->app->app.' - '.$this->titulo;

        return $descripcion;
    }

    public function inscripcion()
    {
        return $this->belongsTo('App\Inscripcion');
    }

    public function solicitud_1()
    {
        return $this->belongsTo('App\Solicitud');
    }

    public function solicitud_2()
    {
        return $this->belongsTo('App\Solicitud');
    }

    protected $table = 'cambios_de_solicitudes_de_inscripciones';

}
