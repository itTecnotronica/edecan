<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pais_por_equipo extends Model
{
	protected $guarded = ['id'];    



    public function descrip_modelo()
    {
        $descripcion = $this->equipo->equipo.' - '.$this->pais->pais;
        //$descripcion = 1;

        return $descripcion;
    }

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    }

    public function equipo()
    {
        return $this->belongsTo('App\Equipo');
    }

    protected $table = 'paises_por_equipo';  
}
