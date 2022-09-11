<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Caja_contable extends Model
{
	protected $guarded = ['id'];    

    protected $table = 'cajas_contables';  

    public function persona_juridica()
    {
        return $this->belongsTo('App\Persona_juridica');
    }
}
