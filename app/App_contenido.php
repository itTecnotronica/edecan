<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App_contenido extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

    	$descripcion = $this->app->app.' - '.$this->titulo;

        return $descripcion;
    }

    public function app()
    {
        return $this->belongsTo('App\App');
    }

    public function app_tipo_de_contenido()
    {
        return $this->belongsTo('App\App_tipo_de_contenido');
    }

}
