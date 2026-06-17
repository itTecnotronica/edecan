<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
	protected $guarded = ['id'];    


    public function idioma_por_pais()
    {
        return $this->hasMany('App\Idioma_por_pais');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    protected $table = 'paises';  
}
