<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
	protected $guarded = ['id'];    

    public function inscripcion()
    {
        return $this->belongsTo('App\Inscripcion');
    }

    public function codigo_de_envio()
    {
        return $this->belongsTo('App\Codigo_de_envio');
    }

    public function medio_de_envio()
    {
        return $this->belongsTo('App\Medio_de_envio');
    }    

}
