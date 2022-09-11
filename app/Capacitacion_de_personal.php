<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Capacitacion_de_personal extends Model
{
	protected $guarded = ['id'];    


    public function user()
    {
        return $this->belongsTo('App\User');
    } 

    public function capacitacion()
    {
        return $this->belongsTo('App\Capacitacion');
    } 

    protected $table = 'capacitaciones_de_personal';

}
