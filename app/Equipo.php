<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
	protected $guarded = ['id'];    

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    }

    public function coordinador_user()
    {
        return $this->belongsTo('App\User', 'coordinador_user_id');
    }

}
