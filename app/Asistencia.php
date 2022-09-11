<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
	protected $guarded = ['id'];    

    public function inscripcion()
    {
        return $this->belongsTo('App\Inscripcion');
    }

    public function leccion()
    {
        return $this->belongsTo('App\Leccion');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
