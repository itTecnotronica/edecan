<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

        return $this->zona.', '.$this->pais->pais;
    }

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    } 
}
