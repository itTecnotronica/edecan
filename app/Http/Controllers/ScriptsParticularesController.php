<?php

namespace App\Http\Controllers;
use App\Idioma;
use App\Solicitud;
use Illuminate\Http\Request;

class ScriptsParticularesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function scripts_particulares($gen_modelo, $filtros_por_campo)
    {
        $printJavaScript = '<script type="text/javascript">';
        $printJavaScript .= '$(document).ready(function() {';

        if ($gen_modelo == 'Fecha_de_evento') {          
            
            $solicitud_id = $filtros_por_campo['solicitud_id'];
            $Solicitud = Solicitud::find($solicitud_id);

            $valor_html = __('Insertar los datos del inicio de cursos');
            $printJavaScript .= "$('#modal-titulo-Fecha_de_evento').html('".$valor_html."');";
            
            if ($Solicitud->tipo_de_evento_id == 1) {
                $valor_html = '<p class="seccion-form">'.__('Fecha, Hora y Lugar donde se iniciara este curso').'</p>';
                $printJavaScript .= "$('#app-func-abm').before('".$valor_html."');";
            }

            $valor_html = '<p><span class="aclaracion_campo_form">'.__('Indique la calle, numeración y alguna referencia de ser necesario (no indique el nombre de la ciudad, ni el nombre del Lumisial), por ejemplo:').'</span> <span class="aclaracion_campo_form_ejemplo">'.__('Av. Mitre 855, Biblioteca Alberdi').'</span></p>';
            $printJavaScript .= "$('#direccion_de_inicio').before('".$valor_html."');";


            $valor_html = '<p><span class="aclaracion_campo_form">'.__('Indique el enlace a google maps donde esté ubicado el lugar, por ejemplo:').'</span> <span class="aclaracion_campo_form_ejemplo"><a target="_blank" href="https://goo.gl/maps/yqM3pMey16z">https://goo.gl/maps/yqM3pMey16z</a></span> </p>';
            $printJavaScript .= "$('#url_enlace_a_google_maps_inicio').before('".$valor_html."');";


            if ($Solicitud->tipo_de_evento_id == 1) {

                $valor_html = '<br><p class="seccion-form">'.__('Indique que dias y horarios se dictará este curso').'</p><p>'.__("Si el curso es Lunes y Miércoles a las 20hs, indique en Hora Lunes el valor 20:00 y en Hora Miércoles el valor 20:00").'</p>';
                  
                $printJavaScript .= "$('#cupo_maximo_disponible_del_salon').after('".$valor_html."' );";

                $valor_html = '<p><span class="aclaracion_campo_form">'.__('Si el curso se dicta en el mismo lugar donde inicia, no complete este campo, sino indique aquí la calle, numeración y alguna referencia donde se dictará el curso (no indique el nombre de la ciudad, ni el nombre del Lumisial), por ejemplo:').'</span> <span class="aclaracion_campo_form_ejemplo">Lavalle 382</span> </p>';
                    
                $printJavaScript .= "$('#direccion_del_curso').before('".$valor_html."');";

                $valor_html = '<p> <span class="aclaracion_campo_form">'.__('Si el curso se dicta en el mismo lugar donde inicia, no complete este campo, sino indique aquí el enlace a google maps donde esté ubicado el lugar donde se dictará el curso, por ejemplo:').'</span> <span class="aclaracion_campo_form_ejemplo"><a target="_blank" href="https://goo.gl/maps/Ad8RSPzichC2">https://goo.gl/maps/Ad8RSPzichC2</a></span> </p>';
                    
                $printJavaScript .= "$('#url_enlace_a_google_maps_curso').before('".$valor_html."');";
            }
            else {
                $printJavaScript .= "$('#titulo_de_conferencia_publica').attr('required', true);";
            }

        } 
        
        $printJavaScript .= '});';
        $printJavaScript .= '</script>';

        echo $printJavaScript;
    }

}