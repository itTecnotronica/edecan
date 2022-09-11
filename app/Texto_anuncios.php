<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Texto_anuncios extends Model
{
	protected $guarded = ['id'];    

    public function idioma_por_pais()
    {
        return $this->belongsTo('App\Idioma_por_pais');
    }


    public function tipo_De_evento()
    {
        return $this->belongsTo('App\Tipo_De_evento');
    }

    protected $table = 'textos_anuncios';  
}
