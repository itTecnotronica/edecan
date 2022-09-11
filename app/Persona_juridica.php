<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona_juridica extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'personas_juridicas';  

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    }
}
