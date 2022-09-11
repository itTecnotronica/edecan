<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

        return $this->provincia.', '.$this->pais->pais;
    }

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    } 
}
