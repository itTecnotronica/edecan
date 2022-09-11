<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material_de_leccion extends Model
{
	protected $guarded = ['id'];    


    public function app_tipo_de_contenido()
    {
        return $this->belongsTo('App\App_tipo_de_contenido');
    }

    public function leccion()
    {
        return $this->belongsTo('App\Leccion');
    }

    protected $table = 'materiales_de_leccion';

}
