<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
	protected $guarded = ['id'];    



    public function pais()
    {
        return $this->belongsTo('App\Pais');
    } 

    public $timestamps = false;
}
