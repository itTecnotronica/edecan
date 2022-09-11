<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\GenericController;

class Modelo_de_mensaje_curso extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

        return $this->titulo_del_mensaje.' ('.$this->curso->nombre_del_curso.')';
    }

    public function curso()
    {
        return $this->belongsTo('App\Curso');
    } 

    

    protected $table = 'modelos_de_mensajes_de_curso';
}
