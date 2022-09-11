<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento_en_sitio extends Model
{
	protected $guarded = ['id'];    

    public function tipo_de_evento_en_sitio()
    {
        return $this->belongsTo('App\Tipo_de_evento_en_sitio');
    }

    public function session()
    {
        return $this->belongsTo('App\Session');
    }

    public function solicitud()
    {
        return $this->belongsTo('App\Solicitud');
    }    

    protected $table = 'eventos_en_sitio';

}
