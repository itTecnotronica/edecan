<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campania extends Model
{
	protected $guarded = ['id'];    

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    }

}
