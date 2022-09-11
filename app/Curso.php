<?php

namespace App;

use App\Leccion;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

        return $this->nombre_del_curso.' ('.$this->idioma->idioma.')';
    }



    public function proximaLeccion($orden_de_leccion = -1)
    {
        $Leccion = Leccion::where('curso_id', $this->id)->where('orden_de_leccion', '>', $orden_de_leccion)->limit(1)->get();
        
        return $Leccion;
    } 

    public function idioma()
    {
        return $this->belongsTo('App\Idioma');
    } 
}
