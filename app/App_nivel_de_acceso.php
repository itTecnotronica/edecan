<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App_nivel_de_acceso extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

    	$descripcion = $this->app->app.' - '.$this->nombre_del_nivel.' (Nivel: '.$this->nivel_de_acceso.')';

        return $descripcion;
    }

    public function app()
    {
        return $this->belongsTo('App\App');
    }

    protected $table = 'app_niveles_de_acceso';

}
