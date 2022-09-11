<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_de_evento extends Model
{
	protected $guarded = ['id'];    

    public function solicitud()
    {
        return $this->belongsTo('App\Solicitud');
    }

    protected $table = 'tipos_de_eventos';  
}
