<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_de_curso_online extends Model
{
	protected $guarded = ['id'];    

    public function idioma()
    {
        return $this->belongsTo('App\Idioma');
    } 
    
    protected $table = 'tipos_de_curso_online';
}
