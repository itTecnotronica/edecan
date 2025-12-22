<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
	protected $guarded = ['id'];    


    public function descrip_modelo()
    {
    	$sesion = $this->id;
        return $sesion;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }


}
