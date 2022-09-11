<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modelo_de_evaluacion extends Model
{
	protected $guarded = ['id'];    

    public function curso()
    {
        return $this->belongsTo('App\Curso');
    }

    protected $table = 'modelos_de_evaluacion';

}
