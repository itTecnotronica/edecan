<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leccion_por_pais_e_idioma extends Model
{
	protected $guarded = ['id'];    

    public function idioma_por_pais()
    {
        return $this->belongsTo('App\Idioma_por_pais');
    }

    public function leccion()
    {
        return $this->belongsTo('App\Leccion');
    }

    protected $table = 'lecciones_por_pais_e_idioma';

}
