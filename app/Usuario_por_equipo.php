<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario_por_equipo extends Model
{
	protected $guarded = ['id'];    



    public function descrip_modelo()
    {
        $descripcion = $this->equipo->equipo.' - '.$this->user->name;
        //$descripcion = 1;

        return $descripcion;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function equipo()
    {
        return $this->belongsTo('App\Equipo');
    }

    protected $table = 'usuarios_por_equipo';  
}
