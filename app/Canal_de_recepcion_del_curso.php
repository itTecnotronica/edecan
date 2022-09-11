<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Canal_de_recepcion_del_curso extends Model
{
	protected $guarded = ['id'];    

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    protected $table = 'canales_de_recepcion_del_curso';  
}
