<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Misionero_por_evento extends Model
{
	protected $guarded = ['id'];    

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function fecha_de_evento()
    {
        return $this->belongsTo('App\Fecha_de_evento');
    }

    protected $table = 'misioneros_por_evento';  
}
