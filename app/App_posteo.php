<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App_posteo extends Model
{
	protected $guarded = ['id'];    

    public function app_contenido()
    {
        return $this->belongsTo('App\App_contenido');
    }

    public function app_categoria()
    {
        return $this->belongsTo('App\App_categoria');
    }

    public function app_nivel_de_acceso()
    {
        return $this->belongsTo('App\App_nivel_de_acceso');
    }


}
