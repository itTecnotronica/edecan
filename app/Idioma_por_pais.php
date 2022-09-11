<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Idioma_por_pais extends Model
{
	protected $guarded = ['id'];    



    public function descrip_modelo()
    {
        $descripcion = $this->pais->pais.' - '.$this->idioma->idioma;
        //$descripcion = 1;

        return $descripcion;
    }

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function idioma()
    {
        return $this->belongsTo('App\Idioma');
    }


    public function modelo_de_mensaje()
    {
        return $this->belongsTo('App\Modelo_de_mensaje');
    }

    public function formato_de_hora()
    {
        return $this->belongsTo('App\Formato_de_hora');
    }

    public function institucion()
    {
        return $this->belongsTo('App\Institucion');
    }

    protected $table = 'idiomas_por_pais';  
}
