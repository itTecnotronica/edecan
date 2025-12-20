<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leccion_extra extends Model
{


    public function url_notificacion_leccion_finalizada($solicitud_id, $modalidad = 1) 
    {
        //$modalidad
        // 1 Codigo de Alumno
        // 2 Wabot


        $Solicitud = Solicitud::find($solicitud_id);
        $leccion_id = $this->id;
        $hash = md5($this->nro_o_codigo);
        if ($modalidad == 1) {
            $url_notificacion_leccion_finalizada = $Solicitud->dominioPublico()."fin-de-leccion/$leccion_id/$solicitud_id/$hash";
        }
        else {
            $url_notificacion_leccion_finalizada = 'https://api.whatsapp.com/send?phone=558003843646&text=gnosis_pre%20a%23'.$Solicitud->id.'%20c%23'.$this->nro_o_codigo;            
        }
        
        return $url_notificacion_leccion_finalizada;
    } 
   


	protected $guarded = ['id'];    

    protected $table = 'lecciones_extra';

}

