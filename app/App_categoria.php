<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App_categoria extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

    	$descripcion = $this->app_nivel_de_acceso->app->app.' - '.$this->app_nivel_de_acceso->nombre_del_nivel.' ('.$this->app_nivel_de_acceso->nivel_de_acceso.') - '.$this->categoria;

        return $descripcion;
    }


    public function app_nivel_de_acceso()
    {
        return $this->belongsTo('App\App_nivel_de_acceso');
    }


}
