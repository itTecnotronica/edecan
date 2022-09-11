<?php

namespace App;

use App\Solicitud;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\GenericController;

class Leccion extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {    

        return $this->curso->nombre_del_curso.' - '.$this->nombre_de_la_leccion.' ('.$this->codigo_de_la_leccion.')';
    }

    public function curso()
    {
        return $this->belongsTo('App\Curso');
    } 


    public function texto_leccion($Leccion_por_pais_e_idioma = null)
    {
        if ($Leccion_por_pais_e_idioma->count() > 0) {
            $enlaces_de_la_leccion = $this->titulo_enlace_1.': '.$Leccion_por_pais_e_idioma[0]->url_enlace_a_la_leccion_1;    
        }
        else {
            $enlaces_de_la_leccion = $this->titulo_enlace_1.': '.$this->url_enlace_a_la_leccion_1;
        }

        
        if ($this->url_enlace_a_la_leccion_2 <> '') {
        	$enlaces_de_la_leccion .= "\n\n".$this->titulo_enlace_2.': '.$this->url_enlace_a_la_leccion_2;
        }
        if ($this->url_enlace_a_la_leccion_3 <> '') {
        	$enlaces_de_la_leccion .= "\n\n".$this->titulo_enlace_3.': '.$this->url_enlace_a_la_leccion_3;
        }
        if ($this->url_enlace_a_la_leccion_4 <> '') {
        	$enlaces_de_la_leccion .= "\n\n".$this->titulo_enlace_4.': '.$this->url_enlace_a_la_leccion_4;
        }
        if ($this->url_enlace_a_la_leccion_5 <> '') {
        	$enlaces_de_la_leccion .= "\n\n".$this->titulo_enlace_5.': '.$this->url_enlace_a_la_leccion_5;
        }

        return [
            'id' => $this->id,
            'codigo_de_la_leccion' => $this->codigo_de_la_leccion,
        	'nombre_de_la_leccion' => $this->nombre_de_la_leccion,
        	'enlaces_de_la_leccion' => $enlaces_de_la_leccion,
        	];

    }

    public function url_notificacion_leccion_finalizada($Solicitud, $modalidad = 1, $Idioma_por_pais) 
    {
        //$modalidad
        // 1 Codigo de Alumno
        // 2 Wabot

        $solicitud_id = $Solicitud->id;
        $leccion_id = $this->id;
        $hash = md5($this->codigo_de_la_leccion);
        if ($modalidad == 1) {
            $url_notificacion_leccion_finalizada = $Solicitud->dominioPublico()."fin-de-leccion/$leccion_id/$solicitud_id/$hash";
        }
        else {
            
            $url_notificacion_leccion_finalizada = 'https://api.whatsapp.com/send?phone=558003843646&text=gnosis_pre%20a%23'.$solicitud_id.'%20c%23'.$this->codigo_de_la_leccion; 

            if ($Idioma_por_pais->idioma_id == 5 or $Idioma_por_pais->idioma_id == 6) {
                $url_notificacion_leccion_finalizada = 'https://api.whatsapp.com/send?phone=558003843646&text=gnosis_pre_pt%20a%23'.$solicitud_id.'%20c%23'.$this->codigo_de_la_leccion; 
            }

            if ($Idioma_por_pais->idioma_id == 2) {
                $url_notificacion_leccion_finalizada = 'https://api.whatsapp.com/send?phone=558003843646&text=gnosis_pre_en%20a%23'.$solicitud_id.'%20c%23'.$this->codigo_de_la_leccion; 
            }

            if ($Idioma_por_pais->idioma_id == 3) {
                $url_notificacion_leccion_finalizada = 'https://api.whatsapp.com/send?phone=558003843646&text=gnosis_pre_fr%20a%23'.$solicitud_id.'%20c%23'.$this->codigo_de_la_leccion; 
            }
                       
        }
        
        return $url_notificacion_leccion_finalizada;
    } 
   

    protected $table = 'lecciones';
}
