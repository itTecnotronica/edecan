<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

        return $this->pais->pais.', '.$this->provincia_estado_o_region.', '.$this->ciudad.', '.$this->direccion;
    }

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    } 

    public function localidad()
    {
        return $this->belongsTo('App\Localidad');
    } 

    public function zonas()
    {
        return $this->belongsTo('App\Zona');
    } 
}
