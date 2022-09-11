<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;
use App\Http\Controllers\FxC; 
use App\Http\Controllers\FormController; 
use App\Curso;
use App\Asistencia;
use App\Cambio_de_solicitud_de_inscripcion;

class Inscripcion extends Model
{
    protected $guarded = ['id'];    


    public function descrip_modelo()
    {
        $titulo_de_conferencia_publica = '';
        if ($this->solicitud->Tipo_de_evento->id == 2) {
            $titulo_de_conferencia_publica = ' ('.$this->solicitud->Conferencia_publica['titulo_de_conferencia_publica'].')';
        }
        $detalle_solicitud = 'ID: '.$this->solicitud->id.' - '.$this->solicitud->Tipo_de_evento->tipo_de_evento.$titulo_de_conferencia_publica.' - '.$this->solicitud->localidad_nombre();

        $descripcion = $this->apellido.', '.$this->nombre." ($detalle_solicitud)";

        return $descripcion;
    }

    public function estado()
    {
        if ($this->sino_envio_pedido_de_confirmacion) {
            $estado = __('Pedido de confirmación enviado');
            $class_estado = 'bg-light-blue';
            $letra_estado = 'pce';
            if ($this->sino_envio_recordatorio_pedido_de_confirmacion) {
                $estado = __('Recordatorio de pedido de confirmación enviado');
                $class_estado = 'bg-light-blue';
                $letra_estado = 'rpce';
                if ($this->sino_confirmo) {
                    $estado = __('Confirmado');
                    $class_estado = 'bg-light-blue';
                    $letra_estado = 'c';

                    if ($this->sino_envio_voucher) {
                        $estado = __('Voucher enviado');
                        $class_estado = 'bg-light-blue';
                        $letra_estado = 've';

                        if ($this->sino_envio_motivacion) {
                            $estado = __('Motivación enviada');
                            $class_estado = 'bg-light-blue';
                            $letra_estado = 'me';

                            if ($this->sino_envio_recordatorio) {
                                $estado = __('Motivación y Recordatorio enviados');
                                $estado = __('Motivación enviada');
                                $class_estado = 'bg-light-blue';
                                $letra_estado = 'mre';
                            }

                        }
                        else {

                            if ($this->sino_envio_recordatorio) {
                                $estado = __('Recordatorio enviado sin motivación enviada');
                                $class_estado = 'bg-light-blue';
                                $letra_estado = 're';
                            }

                        }

                    }


                }

            }

        }
        else {
            $estado = __('Inscripto sin pedido de confirmación');
            $class_estado = 'bg-light-blue';
            $letra_estado = 'i';
        }


        $span_estado = '<span class="badge '.$class_estado.' datos-finales-asistente">'.$estado.'</span>';

        $array_estado = [
            'estado' => $estado,
            'letra_estado' => $letra_estado,
            'class_estado' => $class_estado,
            'span_estado' => $span_estado
        ];

        return $array_estado;
    }


    public function celular_wa($codigo_tel = null)
    {
        
        $celular_wa = trim($this->celular);

        if ($codigo_tel == null) {
            $pais_id = $this->solicitud->id_pais();
            $Pais = Pais::find($pais_id);
            if ($Pais <> null) {
              $codigo_tel = $Pais->codigo_tel;
            }
            else {
              $codigo_tel = '';
            }

        }
        /*
        if ($this->solicitud->tipo_de_evento_id == 3) {
            $codigo_tel = '';
        }
        */
        if (substr($celular_wa, 0, 1) <> '+') {

            if (substr($celular_wa, 0, 2) == '00') {
                $celular_wa_sin_00 = substr($celular_wa, 2, strlen($celular_wa)-2);
                $celular_wa = '+'.$celular_wa_sin_00;
            }
            else {
                if (substr($celular_wa, 0, strlen($codigo_tel)) <> $codigo_tel) {
                    $celular_wa = $codigo_tel.$celular_wa;
                }
            }
        }
        
        $celular_wa = str_replace('+', '', $celular_wa);
        $celular_wa = str_replace(' ', '', $celular_wa);
        $celular_wa = str_replace('-', '', $celular_wa);
        $celular_wa = str_replace('(', '', $celular_wa);
        $celular_wa = str_replace(')', '', $celular_wa);
        $celular_wa = str_replace(',', '', $celular_wa);
        $celular_wa = str_replace('.', '', $celular_wa);
        
        return $celular_wa;
    }



    public function celular_vCard($codigo_tel = null)
    {
        
        $celular_wa = trim($this->celular);

        if ($codigo_tel == null) {
            $pais_id = $this->solicitud->id_pais();
            $Pais = Pais::find($pais_id);
            if ($Pais <> null) {
              $codigo_tel = $Pais->codigo_tel;
            }
            else {
              $codigo_tel = '';
            }

        }
        /*
        if ($this->solicitud->tipo_de_evento_id == 3) {
            $codigo_tel = '';
        }
        */
        if (substr($celular_wa, 0, 1) <> '+') {

            if (substr($celular_wa, 0, 2) == '00') {
                $celular_wa_sin_00 = substr($celular_wa, 2, strlen($celular_wa)-2);
                $celular_wa = '+'.$celular_wa_sin_00;
            }
            else {
                if (substr($celular_wa, 0, strlen($codigo_tel)) <> $codigo_tel) {
                    $celular_wa = $codigo_tel.$celular_wa;
                }
            }

            $celular_wa = '+'.$celular_wa;
        }
        
        $celular_wa = str_replace(' ', '', $celular_wa);
        $celular_wa = str_replace('-', '', $celular_wa);
        $celular_wa = str_replace('(', '', $celular_wa);
        $celular_wa = str_replace(')', '', $celular_wa);
        $celular_wa = str_replace(',', '', $celular_wa);
        $celular_wa = str_replace('.', '', $celular_wa);
        
        return $celular_wa;
    }


    public function texto_final_sitio_web_y_redes($Idioma_por_pais) {


        $texto_final_sitio_web_y_redes = '';
        $sitio_web_y_redes = '';       

        $FormController = new FormController();
        $url_redes = $FormController->urlRedesEspeciales($this->solicitud_id);


        if (count($url_redes) > 0) {
            $url_fanpage = $url_redes['url_fanpage'];
            $url_sitio_web = $url_redes['url_sitio_web'];
            $url_youtube = $url_redes['url_youtube'];
            $url_twitter = $url_redes['url_twitter'];
            $url_instagram = $url_redes['url_instagram'];
        }
        else  {
            $url_fanpage = $Idioma_por_pais->url_fanpage;
            $url_sitio_web = $Idioma_por_pais->url_sitio_web;
            $url_youtube = $Idioma_por_pais->url_youtube;
            $url_twitter = $Idioma_por_pais->url_twitter;
            $url_instagram = $Idioma_por_pais->url_instagram;
        }


        if ($url_sitio_web <> '') {
            $sitio_web_y_redes .= __('Sitio Web').': '.$url_sitio_web."\n";
        }
        if ($url_fanpage <> '') {
            $sitio_web_y_redes .= 'Facebook: '.$url_fanpage."\n";
        }

        if ($url_youtube <> '') {
            $sitio_web_y_redes .= 'Youtube: '.$url_youtube.'?sub_confirmation=1'."\n";
        }

        if ($url_twitter <> '') {
            $sitio_web_y_redes .= 'Twitter: '.$url_twitter."\n";
        }

        if ($url_instagram <> '') {
            $sitio_web_y_redes .= 'Instagram: '.$url_instagram."\n";
        }


        if ($sitio_web_y_redes <> '') {
            $texto_final_sitio_web_y_redes = __('También lo invitamos visitar nuestro sitio web y redes sociales, donde encontrará mucho material acerca de este maravilloso conocimiento').':'."\n\n".$sitio_web_y_redes;
        }

        return $texto_final_sitio_web_y_redes;

    }


    public function datosDelInstructor() {

        $cant = Grupo_de_solicitud::where('nro_de_grupo', $this->grupo)->where('solicitud_id', $this->solicitud_id)->count();
        if ($cant > 0) {
            $Grupo_de_solicitud_search = Grupo_de_solicitud::where('nro_de_grupo', $this->grupo)->where('solicitud_id', $this->solicitud_id)->get();
            $grupo_id = $Grupo_de_solicitud_search[0]->id;
            $Grupo_de_solicitud = Grupo_de_solicitud::find($grupo_id);
            $tel_responsable_inscripcion = $Grupo_de_solicitud->celular_responsable_de_inscripciones;
            $nombre_responsable_inscripcion = trim($Grupo_de_solicitud->nombre_responsable_de_inscripciones);
        }
        else {
            $tel_responsable_inscripcion = $this->solicitud->celular_responsable_de_inscripciones;
            $nombre_responsable_inscripcion = trim($this->solicitud->nombre_responsable_de_inscripciones);
        }        

        $instructor = [
            'tel_responsable_inscripcion' => $tel_responsable_inscripcion,
            'nombre_responsable_inscripcion' => $nombre_responsable_inscripcion
        ];

        return $instructor;

    }


    public function url_whatsapp($nombre_de_la_institucion = null, $tel_responsable_inscripcion = null, $nombre_responsable_inscripcion = null, $nombre_de_ciudad = null, $denominacion_de_voucher = null, $tipo_de_evento_id = null, $tipo_de_evento = null, $codigo_tel = null, $contesto_consulta = null, $Idioma_por_pais = null, $Solicitud = null, $idioma = null, $fecha_de_evento = null, $cant_encuestas = null)
    {

        if ($fecha_de_evento == null) {
            $fecha_de_evento = $this->fecha_de_evento;
        }

        if ($Idioma_por_pais == null) {
           $Idioma_por_pais = $fecha_de_evento->solicitud->idioma_por_pais();
        }
        
        if ($nombre_de_la_institucion == null or $denominacion_de_voucher == null) {
           $nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;
        }

        $buenos_dias_tardes_noches = __('Hola');


        if ($tel_responsable_inscripcion == null) {
            $tel_responsable_inscripcion = $this->solicitud->celular_responsable_de_inscripciones;
        }

        if ($nombre_responsable_inscripcion == null) {
            $nombre_responsable_inscripcion = trim($this->solicitud->nombre_responsable_de_inscripciones);
        }

        /*
        $cant = Grupo_de_solicitud::where('nro_de_grupo', $this->grupo)->where('solicitud_id', $this->solicitud_id)->count();
        if ($cant > 0) {
            $Grupo_de_solicitud_search = Grupo_de_solicitud::where('nro_de_grupo', $this->grupo)->where('solicitud_id', $this->solicitud_id)->get();
            $grupo_id = $Grupo_de_solicitud_search[0]->id;
            $Grupo_de_solicitud = Grupo_de_solicitud::find($grupo_id);
            $tel_responsable_inscripcion = $Grupo_de_solicitud->celular_responsable_de_inscripciones;
            $nombre_responsable_inscripcion = trim($Grupo_de_solicitud->nombre_responsable_de_inscripciones);
        }
        else {
            $tel_responsable_inscripcion = $this->solicitud->celular_responsable_de_inscripciones;
            $nombre_responsable_inscripcion = trim($this->solicitud->nombre_responsable_de_inscripciones);
        }
        */

        if ($nombre_de_ciudad == null) {
           $nombre_de_ciudad = $this->solicitud->localidad_nombre();
        }


        $inscrito_nombre = mb_strtoupper(trim($this->nombre), 'UTF-8');
        $inscrito_apellido = mb_strtoupper(trim($this->apellido), 'UTF-8');
        $denominacion_de_voucher = trim($Idioma_por_pais->denominacion_de_voucher);
        $inscripto_id = $this->id;
        $hash = md5(ENV('PREFIJO_HASH').$inscripto_id);
        $url_voucher = ENV('PATH_PUBLIC')."f/v/$inscripto_id/$hash";

        if ($tipo_de_evento_id == null) {
            $tipo_de_evento_id = $fecha_de_evento->solicitud->tipo_de_evento_id;
        }

        if ($tipo_de_evento == null) {
            $tipo_de_evento = __($fecha_de_evento->solicitud->tipo_de_evento->tipo_de_evento);
        }

        if ($tipo_de_evento_id == 1 or $tipo_de_evento_id == 3) {
            $txt_tipo_de_evento = __("el")." ".$tipo_de_evento;
        }
        else {
            $txt_tipo_de_evento = __("la")." ".$tipo_de_evento;
        }

        $detalle_fecha = $fecha_de_evento->armarDetalleFechasDeEventos('whatsapp', true, $Idioma_por_pais, $Solicitud, $idioma);
        $detalle_fecha_sin_inicio = $fecha_de_evento->armarDetalleFechasDeEventos('whatsapp', false, $Idioma_por_pais, $Solicitud, $idioma);
        $inicio_en_texto = $fecha_de_evento->FechayHoraInicio($Idioma_por_pais, $idioma);
        $hora_inicio = $fecha_de_evento->FormatoHora($fecha_de_evento->hora_de_inicio, $Idioma_por_pais, $idioma);
        $hora_continua = $fecha_de_evento->FormatoHora($fecha_de_evento->hora_continua($tipo_de_evento_id), $Idioma_por_pais, $idioma);
        $nombre_conferencia =  trim($fecha_de_evento->titulo_de_conferencia_publica);
        $consulta_del_inscripto = $this->consulta;
        $lugar_de_inicio = $fecha_de_evento->lugarDelEvento('whatsapp', true);
        $lugar_de_continuacion = $fecha_de_evento->lugarDelEvento('whatsapp', false);
        $prox_clase_dia = $fecha_de_evento->prox_clase_dia();

        $texto_encuesta_satisfaccion = '';
        if ($cant_encuestas < 1) {
            $hash_control = md5($this->created_at);
            $url_encuesta_satisfaccion = ENV('PATH_PUBLIC')."e/$inscripto_id/$hash_control";
            $texto_encuesta_satisfaccion = __('Ayúdenos contestando esta breve encuesta si asistió a la conferencia').': ';
            $texto_encuesta_satisfaccion .= $url_encuesta_satisfaccion;
        }

        $url_form_curso_online = '';
        $url_video_motivacion = '';
        if ($Idioma_por_pais <> null) {
            if ($Idioma_por_pais->url_form_curso_online <> '') {
                $url_form_curso_online = $Idioma_por_pais->url_form_curso_online;
            }
            else {
                $url_form_curso_online = $Idioma_por_pais->idioma->url_form_curso_online;
            }

            if ($Idioma_por_pais->url_video_motivacion <> '') {
                $url_video_motivacion = $Idioma_por_pais->url_video_motivacion;
            }
            else {
                $url_video_motivacion = $Idioma_por_pais->idioma->url_video_motivacion;
            }

        }
        if ($url_form_curso_online == '') {
            $url_form_curso_online = 'https://ac.gnosis.is/f/805/mooc-course-of-self-knowledge-on-line';
        }


        $fcx = new FxC();


        $fecha_de_inicio_del_curso_online = $this->fecha_de_evento->fecha_de_inicio;

        $url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual = '';

        if ($this->solicitud->tipo_de_evento_id == 3) {
            $hora = $this->Solicitud->hora_de_inicio_del_curso_online; 

            if ($Idioma_por_pais->formato_de_hora_id == 1) {
                $formato24  = 'S';
            }
            else {
                $formato24  = 'N';
            } 
            $cod_iso_idioma = null;
            if ($this->solicitud->idioma_id <> '') {
                $cod_iso_idioma = $this->solicitud->idioma->mnemo;
            }

            if ($fecha_de_inicio_del_curso_online <> '') {
                $fecha_de_inicio_del_curso_online = __('Inicio').': '.$fcx->convertirFechaATexto($fecha_de_inicio_del_curso_online, $hora, $formato24, $cod_iso_idioma);            
            }
            $url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual = $this->solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual;
        }


        $texto_final_sitio_web_y_redes = $this->texto_final_sitio_web_y_redes($Idioma_por_pais);

        $codigo_del_alumno = $this->codigo_alumno;
        $url_sitio_web = $Idioma_por_pais->url_sitio_web;
        $url_certificado = $this->url_certificado();

        // pedido_de_confirmacion_curso
        $patrones = array();
        $patrones[0] = '/nombre_de_la_institucion/';
        $patrones[1] = '/buenos_dias_tardes_noches/';
        $patrones[2] = '/lugar_de_inicio/';
        $patrones[3] = '/tel_responsable_inscripcion/';
        $patrones[4] = '/nombre_de_ciudad/';
        $patrones[5] = '/nombre_responsable_inscripcion/';
        $patrones[6] = '/inscrito_nombre/';
        $patrones[7] = '/inscrito_apellido/';
        $patrones[8] = '/denominacion_de_voucher/';
        $patrones[9] = '/txt_tipo_de_evento/';
        $patrones[10] = '/detalle_fecha_sin_inicio/';
        $patrones[11] = '/url_voucher/';
        $patrones[12] = '/inicio_en_texto/';
        $patrones[13] = '/hora_inicio/';
        $patrones[14] = '/nombre_conferencia/';
        $patrones[15] = '/consulta_del_inscripto/';
        $patrones[16] = '/hora_continua/';
        $patrones[17] = '/detalle_fecha/';
        $patrones[18] = '/lugar_de_continuacion/';
        $patrones[19] = '/prox_clase_dia/';
        $patrones[20] = '/texto_encuesta_satisfaccion/';
        $patrones[21] = '/url_form_curso_online/';
        $patrones[22] = '/fecha_de_inicio_del_curso_online/';
        $patrones[23] = '/url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual/';
        $patrones[24] = '/codigo_del_alumno/';
        $patrones[25] = '/url_sitio_web/';
        $patrones[26] = '/url_certificado/';
        $patrones[27] = '/url_video_motivacion/';
        $patrones[28] = '/texto_final_sitio_web_y_redes/';

        $sustituciones = array();
        $sustituciones[0] = $nombre_de_la_institucion;
        $sustituciones[1] = $buenos_dias_tardes_noches;
        $sustituciones[2] = $lugar_de_inicio;
        $sustituciones[3] = $tel_responsable_inscripcion;
        $sustituciones[4] = $nombre_de_ciudad;
        $sustituciones[5] = $nombre_responsable_inscripcion;
        $sustituciones[6] = $inscrito_nombre;
        $sustituciones[7] = $inscrito_apellido;
        $sustituciones[8] = $denominacion_de_voucher;
        $sustituciones[9] = $txt_tipo_de_evento;
        $sustituciones[10] = $detalle_fecha_sin_inicio;
        $sustituciones[11] = $url_voucher;
        $sustituciones[12] = $inicio_en_texto;
        $sustituciones[13] = $hora_inicio;
        $sustituciones[14] = $nombre_conferencia;
        $sustituciones[15] = $consulta_del_inscripto;
        $sustituciones[16] = $hora_continua;
        $sustituciones[17] = $detalle_fecha;
        $sustituciones[18] = $lugar_de_continuacion;
        $sustituciones[19] = $prox_clase_dia;
        $sustituciones[20] = $texto_encuesta_satisfaccion;
        $sustituciones[21] = $url_form_curso_online;
        $sustituciones[22] = $fecha_de_inicio_del_curso_online;
        $sustituciones[23] = $url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual;
        $sustituciones[24] = $codigo_del_alumno;
        $sustituciones[25] = $url_sitio_web;
        $sustituciones[26] = $url_certificado;
        $sustituciones[27] = $url_video_motivacion;
        $sustituciones[28] = $texto_final_sitio_web_y_redes;


        if ($tipo_de_evento_id == 1) {
            if ($this->sino_eleccion_modalidad_online <> 'SI') {
                $pedido_de_confirmacion = $Idioma_por_pais->Modelo_de_mensaje->pedido_de_confirmacion_curso; 
            }
            else { 
                $pedido_de_confirmacion = $Solicitud->envio_de_bienvenida_al_curso_online;   
            }
        }
        if ($tipo_de_evento_id == 2) {
            $pedido_de_confirmacion = $Idioma_por_pais->Modelo_de_mensaje->pedido_de_confirmacion_conferencia;    
        }
        if ($tipo_de_evento_id == 3) {

            if ($this->solicitud->envio_de_bienvenida_al_curso_online <> '') {
                $pedido_de_confirmacion = $this->solicitud->envio_de_bienvenida_al_curso_online;    
            }
            else {

                if ($Idioma_por_pais->envio_de_bienvenida_al_curso_online <> '') {
                    $pedido_de_confirmacion = $Idioma_por_pais->envio_de_bienvenida_al_curso_online;    
                }
                else {
                    $pedido_de_confirmacion = $Idioma_por_pais->Modelo_de_mensaje->envio_de_bienvenida_al_curso_online;    
                }             

            }
            
        }
       
        $pedido_de_confirmacion = preg_replace($patrones, $sustituciones, $pedido_de_confirmacion);
        $mail_pedido_de_confirmacion = $pedido_de_confirmacion;
        $urlencode_pedido_de_confirmacion = $this->CodificarURL($pedido_de_confirmacion, $Idioma_por_pais);
        $pedido_de_confirmacion = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_pedido_de_confirmacion;

        $no_respondieron_al_pedido_de_confirmacion = $Idioma_por_pais->Modelo_de_mensaje->no_respondieron_al_pedido_de_confirmacion;
        $no_respondieron_al_pedido_de_confirmacion = preg_replace($patrones, $sustituciones, $no_respondieron_al_pedido_de_confirmacion);
        $mail_no_respondieron_al_pedido_de_confirmacion = $no_respondieron_al_pedido_de_confirmacion;
        $urlencode_no_respondieron_al_pedido_de_confirmacion = $this->CodificarURL($no_respondieron_al_pedido_de_confirmacion, $Idioma_por_pais);
        $no_respondieron_al_pedido_de_confirmacion = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_no_respondieron_al_pedido_de_confirmacion;


        $envio_de_voucher = $Idioma_por_pais->Modelo_de_mensaje->envio_de_voucher;
        $envio_de_voucher = preg_replace($patrones, $sustituciones, $envio_de_voucher);
        $mail_envio_de_voucher = $envio_de_voucher;
        $urlencode_envio_de_voucher = $this->CodificarURL($envio_de_voucher, $Idioma_por_pais);
        $envio_de_voucher = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_voucher;

        $envio_de_motivacion = $Idioma_por_pais->Modelo_de_mensaje->envio_de_motivacion;
        $envio_de_motivacion = preg_replace($patrones, $sustituciones, $envio_de_motivacion);
        $mail_envio_de_motivacion = $envio_de_motivacion;
        $urlencode_envio_de_motivacion = $this->CodificarURL($envio_de_motivacion, $Idioma_por_pais);
        $envio_de_motivacion = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_motivacion;

        $envio_de_recordatorio = $Idioma_por_pais->Modelo_de_mensaje->envio_de_recordatorio;
        $envio_de_recordatorio = preg_replace($patrones, $sustituciones, $envio_de_recordatorio);
        $mail_envio_de_recordatorio = $envio_de_recordatorio;
        $urlencode_envio_de_recordatorio = $this->CodificarURL($envio_de_recordatorio, $Idioma_por_pais);
        $envio_de_recordatorio = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_recordatorio;

        $contesto_consulta = $Idioma_por_pais->Modelo_de_mensaje->envio_de_respuesta_a_consulta;
        $contesto_consulta = preg_replace($patrones, $sustituciones, $contesto_consulta);
        $mail_contesto_consulta = $contesto_consulta;
        $urlencode_contesto_consulta = $this->CodificarURL($contesto_consulta, $Idioma_por_pais);
        $contesto_consulta = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_contesto_consulta;

        $envio_de_recordatorio_prox_clase = $Idioma_por_pais->Modelo_de_mensaje->envio_de_recordatorio_clase_posterior;
        $envio_de_recordatorio_prox_clase = preg_replace($patrones, $sustituciones, $envio_de_recordatorio_prox_clase);
        $mail_envio_de_recordatorio_prox_clase = $envio_de_recordatorio_prox_clase;
        $urlencode_envio_de_recordatorio_prox_clase = $this->CodificarURL($envio_de_recordatorio_prox_clase, $Idioma_por_pais);
        $envio_de_recordatorio_prox_clase = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_recordatorio_prox_clase;

        $envio_de_recordatorio_prox_clase_no_asistente = $Idioma_por_pais->Modelo_de_mensaje->envio_de_recordatorio_clase_posterior_a_no_asistente;
        $envio_de_recordatorio_prox_clase_no_asistente = preg_replace($patrones, $sustituciones, $envio_de_recordatorio_prox_clase_no_asistente);
        $mail_envio_de_recordatorio_prox_clase_no_asistente = $envio_de_recordatorio_prox_clase_no_asistente;
        $urlencode_envio_de_recordatorio_prox_clase_no_asistente = $this->CodificarURL($envio_de_recordatorio_prox_clase_no_asistente, $Idioma_por_pais);
        $envio_de_recordatorio_prox_clase_no_asistente = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_recordatorio_prox_clase_no_asistente;


        if ($fecha_de_evento->solicitud->envio_de_certificado <> '') {
            $envio_de_certificado = $fecha_de_evento->solicitud->envio_de_certificado;
        }
        else {
            $envio_de_certificado = $Idioma_por_pais->Modelo_de_mensaje->envio_de_certificado;    
        }

        $envio_de_certificado = preg_replace($patrones, $sustituciones, $envio_de_certificado);
        $mail_envio_de_certificado = $envio_de_certificado;
        $urlencode_envio_de_certificado = $this->CodificarURL($envio_de_certificado, $Idioma_por_pais);
        $envio_de_certificado = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_certificado;


        if (trim($Idioma_por_pais->envio_de_invitacion_al_curso_online) <> '') {
            $envio_de_invitacion_al_curso_online = $Idioma_por_pais->envio_de_invitacion_al_curso_online;
        }
        else {
            $envio_de_invitacion_al_curso_online = $Idioma_por_pais->Modelo_de_mensaje->envio_de_invitacion_al_curso_online;
        }
        if ($envio_de_invitacion_al_curso_online <> '') {
            $envio_de_invitacion_al_curso_online = preg_replace($patrones, $sustituciones, $envio_de_invitacion_al_curso_online);
            $mail_envio_de_invitacion_al_curso_online = $envio_de_invitacion_al_curso_online;
            $urlencode_envio_de_invitacion_al_curso_online = $this->CodificarURL($envio_de_invitacion_al_curso_online, $Idioma_por_pais);
            $envio_de_invitacion_al_curso_online = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_invitacion_al_curso_online;
        }
        else {
            $mail_envio_de_invitacion_al_curso_online = '';
        }


        $url_whatsapp = [
            'pedido_de_confirmacion' => $pedido_de_confirmacion,
            'no_respondieron_al_pedido_de_confirmacion' => $no_respondieron_al_pedido_de_confirmacion,
            'envio_de_voucher' => $envio_de_voucher,
            'envio_de_motivacion' => $envio_de_motivacion,
            'envio_de_recordatorio' => $envio_de_recordatorio,
            'contesto_consulta' => $contesto_consulta,
            'envio_de_recordatorio_prox_clase' => $envio_de_recordatorio_prox_clase,
            'envio_de_recordatorio_prox_clase_no_asistente' => $envio_de_recordatorio_prox_clase_no_asistente,
            'envio_de_invitacion_al_curso_online' => $envio_de_invitacion_al_curso_online,
            'mail_pedido_de_confirmacion' => $mail_pedido_de_confirmacion,
            'mail_no_respondieron_al_pedido_de_confirmacion' => $mail_no_respondieron_al_pedido_de_confirmacion,
            'mail_envio_de_voucher' => $mail_envio_de_voucher,
            'mail_envio_de_motivacion' => $mail_envio_de_motivacion,
            'mail_envio_de_recordatorio' => $mail_envio_de_recordatorio,
            'mail_contesto_consulta' => $mail_contesto_consulta,
            'mail_envio_de_recordatorio_prox_clase' => $mail_envio_de_recordatorio_prox_clase,
            'mail_envio_de_recordatorio_prox_clase_no_asistente' => $mail_envio_de_recordatorio_prox_clase_no_asistente,
            'mail_envio_de_invitacion_al_curso_online' => $mail_envio_de_invitacion_al_curso_online,
            'envio_de_certificado' => $envio_de_certificado,
            'mail_envio_de_certificado' => $mail_envio_de_certificado,


        ];

        return $url_whatsapp;
    }


    public function url_whatsapp_sin_evento($nombre_de_la_institucion = null, $tel_responsable_inscripcion = null, $nombre_responsable_inscripcion = null, $nombre_de_ciudad = null, $denominacion_de_voucher = null, $tipo_de_evento_id = null, $tipo_de_evento = null, $codigo_tel = null, $contesto_consulta = null, $Idioma_por_pais = null, $Solicitud = null)
    {

        if ($Solicitud == null or $Solicitud->id <> $this->solicitud_id) {
            $Solicitud = $this->solicitud;
        }

        if ($Idioma_por_pais == null) {
            $Idioma_por_pais = $Solicitud->idioma_por_pais();
        }

        if ($nombre_de_la_institucion == null or $denominacion_de_voucher == null) {
            $nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;
            $denominacion_de_voucher = $Idioma_por_pais->denominacion_de_voucher;
        }

        $buenos_dias_tardes_noches = __('Hola');

        if ($tel_responsable_inscripcion == null) {
            $tel_responsable_inscripcion = $Solicitud->celular_responsable_de_inscripciones;
        }

        if ($nombre_responsable_inscripcion == null) {
            $nombre_responsable_inscripcion = trim($Solicitud->nombre_responsable_de_inscripciones);
        }

        /*
        $cant = Grupo_de_solicitud::where('nro_de_grupo', $this->grupo)->where('solicitud_id', $this->solicitud_id)->count();
        if ($cant > 0) {
            $Grupo_de_solicitud_search = Grupo_de_solicitud::where('nro_de_grupo', $this->grupo)->where('solicitud_id', $this->solicitud_id)->get();
            $grupo_id = $Grupo_de_solicitud_search[0]->id;
            $Grupo_de_solicitud = Grupo_de_solicitud::find($grupo_id);
            $tel_responsable_inscripcion = $Grupo_de_solicitud->celular_responsable_de_inscripciones;
            $nombre_responsable_inscripcion = trim($Grupo_de_solicitud->nombre_responsable_de_inscripciones);
        }
        else {
            $tel_responsable_inscripcion = $Solicitud->celular_responsable_de_inscripciones;
            $nombre_responsable_inscripcion = trim($Solicitud->nombre_responsable_de_inscripciones);
        }
        */

        
        if ($nombre_de_ciudad == null) {
            $nombre_de_ciudad = $Solicitud->localidad_nombre();
        }

        $inscrito_nombre = mb_strtoupper(trim($this->nombre), 'UTF-8');
        $inscrito_apellido = mb_strtoupper(trim($this->apellido), 'UTF-8');
        $inscripto_id = $this->id;
        $hash = md5(ENV('PREFIJO_HASH').$inscripto_id);
        $url_voucher = ENV('PATH_PUBLIC')."f/v/$inscripto_id/$hash";

        if ($tipo_de_evento_id == null) {
            $tipo_de_evento_id = $Solicitud->tipo_de_evento_id;
        }

        if ($tipo_de_evento == null) {
            $tipo_de_evento = __($Solicitud->tipo_de_evento->tipo_de_evento);
        }

        if ($tipo_de_evento_id == 1 or $tipo_de_evento_id == 3) {
            $txt_tipo_de_evento = __("el")." ".$tipo_de_evento;
        }
        else {
            $txt_tipo_de_evento = __("la")." ".$tipo_de_evento;
        }

        $consulta_del_inscripto = $this->consulta;


        $url_form_curso_online = '';
        $url_sitio_web = '';
        $url_video_motivacion = '';

        if ($Idioma_por_pais <> null) {
            if ($Idioma_por_pais->url_form_curso_online <> '') {
                $url_form_curso_online = $Idioma_por_pais->url_form_curso_online;
            }
            else {
                $url_form_curso_online = $Idioma_por_pais->idioma->url_form_curso_online;
            }
            $url_sitio_web = $Idioma_por_pais->url_sitio_web;

            
            if ($Idioma_por_pais->url_video_motivacion <> '') {
                $url_video_motivacion = $Idioma_por_pais->url_video_motivacion;
            }
            else {
                $url_video_motivacion = $Idioma_por_pais->idioma->url_video_motivacion;
            }
        }
        if ($url_form_curso_online == '') {
            $url_form_curso_online = 'https://ac.gnosis.is/f/805/mooc-course-of-self-knowledge-on-line';
        }


        

        $fcx = new FxC();

        if ($Solicitud <> null) {
            $fecha_de_inicio_del_curso_online = $Solicitud->fecha_de_inicio_del_curso_online;    
        }
        $fecha_de_inicio_del_curso_online = $Solicitud->fecha_de_inicio_del_curso_online;

        $url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual = '';

        if ($Solicitud->tipo_de_evento_id == 3) {
            $hora = $Solicitud->hora_de_inicio_del_curso_online; 

            if ($Idioma_por_pais->formato_de_hora_id == 1) {
                $formato24  = 'S';
            }
            else {
                $formato24  = 'N';
            } 

            $cod_iso_idioma = null;
            if ($Idioma_por_pais <> null) {
                $cod_iso_idioma = $Idioma_por_pais->idioma->mnemo;
            }
            else {
                if ($Solicitud->idioma_id <> '') {
                    $cod_iso_idioma = $Solicitud->idioma->mnemo;
                }                
            }



            if ($fecha_de_inicio_del_curso_online <> '') {
                $fecha_de_inicio_del_curso_online = __('Inicio').': '.$fcx->convertirFechaATexto($fecha_de_inicio_del_curso_online, $hora, $formato24, $cod_iso_idioma);
            }
            $url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual = $Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual;
        }

        $hora_inicio = '';
        if ($Solicitud->hora_de_inicio <> '') {
            $hora_inicio = $fcx->FormatoHora($Solicitud->hora_de_inicio, $Idioma_por_pais, $idioma);
        }


        $texto_final_sitio_web_y_redes = $this->texto_final_sitio_web_y_redes($Idioma_por_pais);

        $url_enlace_cuenta_de_instagram = $Solicitud->url_enlace_cuenta_de_instagram;
        $nombre_de_usuario_instagram = $Solicitud->nombre_de_usuario_instagram;

        $codigo_del_alumno = $this->codigo_alumno;
        $url_certificado = $this->url_certificado();

        $icon_4diamantes = '💠';
        $icon_check = '✅';
        $icon_solcito = '🌞';
        $icon_flechita = '➡';
        $icon_manito = '👉';

        // pedido_de_confirmacion_curso
        $patrones = array();
        $patrones[0] = '/nombre_de_la_institucion/';
        $patrones[1] = '/buenos_dias_tardes_noches/';
        $patrones[3] = '/tel_responsable_inscripcion/';
        $patrones[4] = '/nombre_de_ciudad/';
        $patrones[5] = '/nombre_responsable_inscripcion/';
        $patrones[6] = '/inscrito_nombre/';
        $patrones[7] = '/inscrito_apellido/';
        $patrones[8] = '/denominacion_de_voucher/';
        $patrones[9] = '/txt_tipo_de_evento/';
        $patrones[11] = '/url_voucher/';
        $patrones[15] = '/consulta_del_inscripto/';
        $patrones[16] = '/url_form_curso_online/';
        $patrones[17] = '/fecha_de_inicio_del_curso_online/';
        $patrones[18] = '/url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual/';
        $patrones[19] = '/codigo_del_alumno/';
        $patrones[20] = '/icon_4diamantes/';
        $patrones[21] = '/icon_check/';
        $patrones[22] = '/icon_solcito/';
        $patrones[23] = '/icon_flechita/';
        $patrones[24] = '/hora_inicio/';
        $patrones[25] = '/icon_manito/';
        $patrones[26] = '/url_sitio_web/';
        $patrones[27] = '/url_certificado/';
        $patrones[28] = '/url_enlace_cuenta_de_instagram/';
        $patrones[29] = '/nombre_de_usuario_instagram/';
        $patrones[30] = '/url_video_motivacion/';
        $patrones[31] = '/texto_final_sitio_web_y_redes/';
        
        

        $sustituciones = array();
        $sustituciones[0] = $nombre_de_la_institucion;
        $sustituciones[1] = $buenos_dias_tardes_noches;
        $sustituciones[3] = $tel_responsable_inscripcion;
        $sustituciones[4] = $nombre_de_ciudad;
        $sustituciones[5] = $nombre_responsable_inscripcion;
        $sustituciones[6] = $inscrito_nombre;
        $sustituciones[7] = $inscrito_apellido;
        $sustituciones[8] = $denominacion_de_voucher;
        $sustituciones[9] = $txt_tipo_de_evento;
        $sustituciones[11] = $url_voucher;
        $sustituciones[15] = $consulta_del_inscripto;
        $sustituciones[16] = $url_form_curso_online;
        $sustituciones[17] = $fecha_de_inicio_del_curso_online;
        $sustituciones[18] = $url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual;
        $sustituciones[19] = $codigo_del_alumno;
        $sustituciones[20] = $icon_4diamantes;
        $sustituciones[21] = $icon_check;
        $sustituciones[22] = $icon_solcito;
        $sustituciones[23] = $icon_flechita;
        $sustituciones[24] = $hora_inicio;
        $sustituciones[25] = $icon_manito;
        $sustituciones[26] = $url_sitio_web;
        $sustituciones[27] = $url_certificado;
        $sustituciones[28] = $url_enlace_cuenta_de_instagram;
        $sustituciones[29] = $nombre_de_usuario_instagram;
        $sustituciones[30] = $url_video_motivacion;
        $sustituciones[31] = $texto_final_sitio_web_y_redes;


        //$pedido_de_confirmacion = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text=';


        if ($contesto_consulta == null) {
            $contesto_consulta = $Idioma_por_pais->Modelo_de_mensaje->envio_de_respuesta_a_consulta;
        }
        $contesto_consulta = preg_replace($patrones, $sustituciones, $contesto_consulta);
        $urlencode_contesto_consulta = $this->CodificarURL($contesto_consulta, $Idioma_por_pais);
        $contesto_consulta = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_contesto_consulta;



        $envio_de_invitacion_al_curso_online = $Idioma_por_pais->Modelo_de_mensaje->envio_de_invitacion_al_curso_online;
        if ($envio_de_invitacion_al_curso_online <> '') {
            $envio_de_invitacion_al_curso_online = preg_replace($patrones, $sustituciones, $envio_de_invitacion_al_curso_online);
            $mail_envio_de_invitacion_al_curso_online = $envio_de_invitacion_al_curso_online;
            $urlencode_envio_de_invitacion_al_curso_online = $this->CodificarURL($envio_de_invitacion_al_curso_online, $Idioma_por_pais);
            $envio_de_invitacion_al_curso_online = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_invitacion_al_curso_online;
        }
        else {
            $mail_envio_de_invitacion_al_curso_online = '';
        }




        if ($tipo_de_evento_id == 3) {

            if ($Solicitud->envio_de_bienvenida_al_curso_online <> '') {
                $pedido_de_informacion_sobre_horarios_disponibles = $Solicitud->envio_de_bienvenida_al_curso_online;    
            }
            else {
                if ($Idioma_por_pais->envio_de_bienvenida_al_curso_online <> '') {
                    $pedido_de_informacion_sobre_horarios_disponibles = $Idioma_por_pais->envio_de_bienvenida_al_curso_online;
                }
                else {
                    $pedido_de_informacion_sobre_horarios_disponibles = $Idioma_por_pais->Modelo_de_mensaje->envio_de_bienvenida_al_curso_online;
                }
            }

            
        }
        else {
            if ($tipo_de_evento_id == 4 and $Solicitud->envio_de_bienvenida_al_curso_online <> '') {
                $pedido_de_informacion_sobre_horarios_disponibles = $Solicitud->envio_de_bienvenida_al_curso_online;            
            }
            else {            
                $pedido_de_informacion_sobre_horarios_disponibles = $Idioma_por_pais->Modelo_de_mensaje->pedido_de_informacion_sobre_horarios_disponibles;
            }
        }

        $pedido_de_informacion_sobre_horarios_disponibles = preg_replace($patrones, $sustituciones, $pedido_de_informacion_sobre_horarios_disponibles);
        $mail_pedido_de_confirmacion = $pedido_de_informacion_sobre_horarios_disponibles;
        $urlencode_pedido_de_informacion_sobre_horarios_disponibles = $this->CodificarURL($pedido_de_informacion_sobre_horarios_disponibles, $Idioma_por_pais);
        $pedido_de_informacion_sobre_horarios_disponibles = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_pedido_de_informacion_sobre_horarios_disponibles;



        if ($Solicitud->envio_de_recordatorio_al_curso_online <> '') {
            $envio_de_recordatorio = $Solicitud->envio_de_recordatorio_al_curso_online;
        }
        else  {
            $envio_de_recordatorio = $Idioma_por_pais->envio_de_recordatorio_al_curso_online;
        }

        $envio_de_recordatorio = preg_replace($patrones, $sustituciones, $envio_de_recordatorio);
        $urlencode_envio_de_recordatorio = $this->CodificarURL($envio_de_recordatorio, $Idioma_por_pais);
        $envio_de_recordatorio = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_recordatorio;


        if ($Solicitud->envio_de_certificado <> '') {
            $envio_de_certificado = $Solicitud->envio_de_certificado;
        }
        else {
            $envio_de_certificado = $Idioma_por_pais->Modelo_de_mensaje->envio_de_certificado;    
        }

        $envio_de_certificado = preg_replace($patrones, $sustituciones, $envio_de_certificado);
        $mail_envio_de_certificado = $envio_de_certificado;
        $urlencode_envio_de_certificado = $this->CodificarURL($envio_de_certificado, $Idioma_por_pais);
        $envio_de_certificado = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_envio_de_certificado;

        $contesto_consulta = $Idioma_por_pais->Modelo_de_mensaje->envio_de_respuesta_a_consulta;
        $contesto_consulta = preg_replace($patrones, $sustituciones, $contesto_consulta);
        $mail_contesto_consulta = $contesto_consulta;
        $urlencode_contesto_consulta = $this->CodificarURL($contesto_consulta, $Idioma_por_pais);
        $contesto_consulta = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_contesto_consulta;

        $url_whatsapp = [
            'pedido_de_confirmacion' => $pedido_de_informacion_sobre_horarios_disponibles,
            'mail_pedido_de_confirmacion' => $mail_pedido_de_confirmacion,
            'contesto_consulta' => $contesto_consulta,
            'envio_de_invitacion_al_curso_online' => $envio_de_invitacion_al_curso_online,
            'mail_envio_de_invitacion_al_curso_online' => $mail_envio_de_invitacion_al_curso_online,
            'envio_de_recordatorio' => $envio_de_recordatorio,
            'mail_contesto_consulta' => $mail_contesto_consulta,
            'envio_de_certificado' => $envio_de_certificado,
            'mail_envio_de_certificado' => $mail_envio_de_certificado
        ];

        return $url_whatsapp;

        
    }



    public function url_whatsapp_modelo_mensaje_curso($modelo_del_mensaje = null, $Idioma_por_pais = null, $nombre_responsable_inscripcion, $Solicitud, $Curso, $orden_de_leccion, $proximaLeccion_id, $codigo_de_la_leccion)
    {

        if ($Solicitud == null or $Solicitud->id <> $this->solicitud_id) {
            $Solicitud = $this->solicitud;
        }        
        
        $inscrito_nombre = mb_strtoupper($this->nombre, 'UTF-8');
        $inscrito_apellido = mb_strtoupper($this->apellido, 'UTF-8');

        if ($Idioma_por_pais == null) {
            $Idioma_por_pais = $Solicitud->idioma_por_pais();
        }

        $codigo_tel = $Idioma_por_pais->pais->codigo_tel;
        $curso_id = $Solicitud->curso_id;
        if ($curso_id == null) {
            $curso_id = 1;
        }
        $nombre_y_apellido_url = str_replace(' ', '+', $inscrito_nombre).'+'.str_replace(' ', '+', $inscrito_apellido);
        $url_sitio_web = $Idioma_por_pais->url_sitio_web;

        if ($nombre_responsable_inscripcion == null) {
            $nombre_responsable_inscripcion = trim($Solicitud->nombre_responsable_de_inscripciones);
        }

        /*
        $cant = Grupo_de_solicitud::where('nro_de_grupo', $this->grupo)->where('solicitud_id', $this->solicitud_id)->count();
        if ($cant > 0) {
            $Grupo_de_solicitud_search = Grupo_de_solicitud::where('nro_de_grupo', $this->grupo)->where('solicitud_id', $this->solicitud_id)->get();
            $grupo_id = $Grupo_de_solicitud_search[0]->id;
            $Grupo_de_solicitud = Grupo_de_solicitud::find($grupo_id);
            $nombre_responsable_inscripcion = trim($Grupo_de_solicitud->nombre_responsable_de_inscripciones);
        }
        else {
            $nombre_responsable_inscripcion = trim($Solicitud->nombre_responsable_de_inscripciones);
        }
        */




        if ($curso_id <> null) {
            /*
            $ultimaAsistencia = $this->ultimaAsistencia($curso_id);
            
            if ($ultimaAsistencia->count() > 0) {
                $orden_de_leccion = $ultimaAsistencia[0]->Leccion->orden_de_leccion;
            }
            else {
                $orden_de_leccion = -1;                
            }
            */

            if ($orden_de_leccion == null) {
                $orden_de_leccion = -1;                
            }

            //$Leccion = $Curso->proximaLeccion($orden_de_leccion);
            //$Leccion = Leccion::where('id', $proximaLeccion_id)->get();
            
            if ($proximaLeccion_id > 0) {
                $url_notificacion_leccion_finalizada_ultima = $this->url_notificacion_leccion_finalizada($proximaLeccion_id, $codigo_de_la_leccion, $Solicitud->id);
            }
            else {
                $url_notificacion_leccion_finalizada_ultima = '';
            }
            $img_donde_quira_que_vayas = ENV('PATH_PUBLIC').'img/donde-quiera-que-vayas.jpeg';



            $enlace_tp_1 = 'https://docs.google.com/forms/d/e/1FAIpQLSc7vJJowEKZiXZb_0Cj82fL5e8PlXSipnUFdUjm1JVNRTTMYw/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.900255810='.$this->id;

            $enlace_tp_2 = 'https://docs.google.com/forms/d/e/1FAIpQLSfV-Lm2nMat4yKoQGz2d5ZGPKiB_Tl93vukmIRgYU-16IJ0fw/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.1431311979='.$this->id;

            $enlace_tp_3 = 'https://docs.google.com/forms/d/e/1FAIpQLSfayslHXqYzmqIzbh-CweY9yaBejrAvw--ujPOdUmQ3AJWXVw/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.1286000777='.$this->id;


            //Portugues
            if ($Curso->idioma_id == 5) {
                $enlace_tp_1 = 'https://docs.google.com/forms/d/e/1FAIpQLSdO66FQpCmV30qipYASGyRBHG5j9cIkE_C2akNAeKaINNhrSA/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.900255810='.$this->id;

                $enlace_tp_2 = 'https://docs.google.com/forms/d/e/1FAIpQLSeI57JPBmY0Kwp2nnFC9kVKOL8YGh0DZLxzzkE_84rLhSfDiA/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.1431311979='.$this->id;

                $enlace_tp_3 = 'https://docs.google.com/forms/d/e/1FAIpQLSdM3trC-Va19ho1VtgwNz5D7ePfAgnAyHWwus9ZG44JpD5_vw/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.1286000777='.$this->id;

            }

            //Ingles
            if ($Curso->idioma_id == 2) {
                $enlace_tp_1 = 'https://docs.google.com/forms/d/e/1FAIpQLScPaaI5kK47gttsmQSIJyWUhRS5dIOAdlmYsUOi7Mlqv2rjtw/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.900255810='.$this->id;

                $enlace_tp_2 = 'https://docs.google.com/forms/d/e/1FAIpQLScDnFsGj6Aip0pPuYG4YqN5AICIqf3waOKI4j1NIwjyPVIOWw/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.1431311979='.$this->id;

                $enlace_tp_3 = 'https://docs.google.com/forms/d/e/1FAIpQLSe8Jhu02dfvkJikO6Ztk93tKCsxMDM8GX6tJRWgALHB0sX7ig/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.1286000777='.$this->id;

            }

            //Frances
            if ($Curso->idioma_id == 3) {
                $enlace_tp_1 = 'https://docs.google.com/forms/d/e/1FAIpQLSf3blOGv4ZFykXc6A_t9mok-KfXpiTfQ6VY1ApECzvp5Cg_Mg/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.900255810='.$this->id;

                $enlace_tp_2 = 'https://docs.google.com/forms/d/e/1FAIpQLSe4eV1UFkofMTQCtZ7DsfVsHqmFQ9BRV2IKVV9R6sMT2L6fhw/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.1431311979='.$this->id;

                $enlace_tp_3 = 'https://docs.google.com/forms/d/e/1FAIpQLSdlhrNK3OIoIm01xeAQJ0wP6p_MNpW62fmOKO-hhSgb_caC3A/viewform?usp=pp_url&entry.717885317='.$nombre_y_apellido_url.'&entry.1286000777='.$this->id;

            }

            
            $codigo_del_alumno = $this->codigo_alumno;

            // pedido_de_confirmacion_curso
            $patrones = array();
            $patrones[0] = '/inscrito_nombre/';
            $patrones[1] = '/nombre_responsable_inscripcion/';
            $patrones[2] = '/url_notificacion_leccion_finalizada_ultima/';   
            $patrones[3] = '/img_donde_quira_que_vayas/';        
            $patrones[4] = '/enlace_tp_1/';       
            $patrones[5] = '/inscrito_apellido/'; 
            $patrones[6] = '/enlace_tp_2/';      
            $patrones[7] = '/url_sitio_web/';    
            $patrones[8] = '/enlace_tp_3/';   
            $patrones[9] = '/codigo_del_alumno/';     

            $sustituciones = array();
            $sustituciones[0] = $inscrito_nombre;
            $sustituciones[1] = $nombre_responsable_inscripcion;
            $sustituciones[2] = $url_notificacion_leccion_finalizada_ultima;
            $sustituciones[3] = $img_donde_quira_que_vayas;
            $sustituciones[4] = $enlace_tp_1;
            $sustituciones[5] = $inscrito_apellido;
            $sustituciones[6] = $enlace_tp_2;
            $sustituciones[7] = $url_sitio_web;
            $sustituciones[8] = $enlace_tp_3;
            $sustituciones[9] = $codigo_del_alumno;


            $modelo_del_mensaje = preg_replace($patrones, $sustituciones, $modelo_del_mensaje);
            $urlencode_modelo_del_mensaje = $this->CodificarURL($modelo_del_mensaje, $Idioma_por_pais);
            $modelo_del_mensaje = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel).'&text='.$urlencode_modelo_del_mensaje;
        }

        return $modelo_del_mensaje;
        
    }

    public function url_notificacion_leccion_finalizada($leccion_id, $codigo_de_la_leccion, $solicitud_id) 
    {
        $hash = md5($codigo_de_la_leccion);
        $url_notificacion_leccion_finalizada = ENV('PATH_PUBLIC')."fin-de-leccion/$leccion_id/$solicitud_id/$hash";
        return $url_notificacion_leccion_finalizada;
    }

    public function ultimaAsistencia($curso_id)
    {
        $ultimaAsistencia = Asistencia::
            join('lecciones as l', 'l.id', '=', 'asistencias.leccion_id')
            ->where('l.curso_id', $curso_id)
            ->where('asistencias.inscripcion_id', $this->id)
            ->orderBy('l.orden_de_leccion', 'desc')
            ->limit(1)
            ->get();

        return $ultimaAsistencia;
    }



    public function enviarRecordatorioHoy($fecha_de_evento = null) {

        $enviar = false;

        if ($fecha_de_evento == null) {
            $fecha_de_evento = $this->fecha_de_evento;
        }

        if ($this->fecha_de_evento_id <> '') {
            $fecha_de_inicio = strtotime($fecha_de_evento->fecha_de_inicio);
            $fecha_de_inicio = date("d-m-Y 00:00:00", $fecha_de_inicio);
            $now = date("d-m-Y 00:00:00",time());
            if ($now == $fecha_de_inicio) {
                $enviar = true;
            }
        }
        //echo $fecha_de_inicio.'---'.$now;
        return $enviar;
    }

    public function enviarRecordatorioProxClase($fecha_de_evento = null) {

        $enviar = false;

        if ($fecha_de_evento == null) {
            $fecha_de_evento = $this->fecha_de_evento;
        }

        if ($this->fecha_de_evento_id <> '') {
            $fecha_de_inicio = strtotime($fecha_de_evento->fecha_de_inicio);
            $fecha_de_inicio = strtotime(date("d-m-Y 00:00:00", $fecha_de_inicio));
            $now = strtotime(date("d-m-Y 00:00:00",time()));
            if ($now > $fecha_de_inicio) {
                $enviar = true;
            }
        }
        
        return $enviar;
    }


    public function enviarInvitacionCursoOnline($Solicitud = null, $fecha_de_evento = null) {

        $enviar = false;  

        if ($this->sino_asistio == '' or $this->sino_asistio == 'NO') {
            if ($Solicitud == null) {
                $Solicitud = $this->solicitud;
            }

            $fecha_de_inicio = '';

            // sino viene null en fecha de evento
            if ($fecha_de_evento == null) {
                // veo si la inscripcion tiene un evento
                if ($this->fecha_de_evento_id <> null) {
                        $fecha_de_inicio = $this->fecha_de_evento->fecha_de_inicio;
                }         
            }
            else {
                // saco la fecha de inicio del evento que viene por la funcion
                $fecha_de_inicio = $fecha_de_evento->fecha_de_inicio;
            }

            if ($fecha_de_inicio <> '') {
                $fecha_de_inicio = date_create($fecha_de_inicio);
                $now = date_create();
                $interval = $fecha_de_inicio->diff($now);
                $cant_dias = $interval->format('%a');
                if ($cant_dias > 10) {
                    $enviar = true;
                }
                else {
                    $enviar = false;  
                }
            }
        }

        if ($fecha_de_evento == null and $this->fecha_de_evento_id == null) {
            $enviar = true;
        }

        if ($this->sino_cancelo == 'SI') {
            $enviar = true;
        }

        if ($Solicitud->tipo_de_evento_id == 3) {
            $enviar = false;
        }
        else {
            $enviar = true;    
        }

        

        return $enviar;
    }

    public function CodificarURL($string, $Idioma_por_pais = null) {
        //$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
        //$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");

        if ($Idioma_por_pais == null) {
            $Idioma_por_pais = $this->solicitud->idioma_por_pais();        
        }

        if ($Idioma_por_pais <> null) {
            $mnemo_idioma = $Idioma_por_pais->idioma->mnemo;

            if ($mnemo_idioma == 'es') {
                $entities = array(' de el ', ' a el ');
                $replacements = array(' del ', ' al ');
                $string = str_replace($entities, $replacements, $string);
            }
            if ($mnemo_idioma == 'it') {
                $entities = array(' de il ', ' a el ', ' de lei ');
                $replacements = array(' del ', ' al ', ' della ');
                $string = str_replace($entities, $replacements, $string);
            }

            if ($mnemo_idioma == 'pt-BR') {
                $entities = array(' do ele ', 'Evento Hoje: la Conferência Pública', ' do la ');
                $replacements = array(' do ', 'Evento Hoje: a Conferência Pública', ' da ');
                $string = str_replace($entities, $replacements, $string);
            }
        }

        $entities = array('%20');
        $replacements = array('+');
        return str_replace($replacements, $entities, urlencode($string));
    }

    public function codigo_del_alumno()
    {
        if ($this->codigo_alumno == '') {
            $nombre_array = explode(' ', $this->nombre);
            $nombre = $nombre_array[0];
            if ($this->celular <> '') {
                $celular = $this->celular_wa('+00');
                $numero = substr($celular, -3);
            }
            else {
                $numero = '123';
            }
            
            $codigo_del_alumno = $nombre.$numero;

            $fcx = new FxC();
            $codigo_del_alumno = $fcx->limpiarAcentos($codigo_del_alumno);

            $codigo_del_alumno = strtoupper($codigo_del_alumno);

            $loopear = true;
            $i = 0;
            while ($loopear) {                
                $cant_duplicados = Inscripcion::whereRaw("UPPER(TRIM(codigo_alumno)) = '$codigo_del_alumno'")
                    ->where('solicitud_id', $this->solicitud_id)
                    ->count();

                if ($cant_duplicados == 0) {
                    $loopear = false;
                }
                else {
                    $codigo_del_alumno = $nombre.$numero.$i;                    
                    $codigo_del_alumno = $fcx->limpiarAcentos($codigo_del_alumno);
                    $codigo_del_alumno = strtoupper($codigo_del_alumno);
                    $i++;
                }
            
            }

        }
        else {
            $codigo_del_alumno = $this->codigo_alumno;
        }

        return $codigo_del_alumno;
    }

    public function planilla_promocion()
    {
        $Promocion = Cambio_de_solicitud_de_inscripcion::where('inscripcion_id', $this->id)->whereRaw('causa_de_cambio_de_solicitud_id in (1, 4)')->orderBy('id')->get();
        $Solicitud = Solicitud::find($Promocion[0]->solicitud_destino);
        $mensaje = 'Solicitud Destino: '.$Solicitud->id.' | Tutor: '.$Solicitud->nombre_responsable_de_inscripciones.' cel: '.$Solicitud->celular_responsable_de_inscripciones;
        $boton_wa = '<a href="https://api.whatsapp.com/send?phone='.$Solicitud->celular_wa($Solicitud->celular_responsable_de_inscripciones).'" target="_blank">';
        $boton_wa .= '<button type="button" class="btn btn-default btn-xs"><i class="fa fa-fw fa-whatsapp" style="font-size: 19px"></i> Enviar WhatsApp</button></a>';

        return $mensaje.$boton_wa;
    }

    public function planilla_original()
    {
        $Origen = Cambio_de_solicitud_de_inscripcion::where('inscripcion_id', $this->id)->where('solicitud_destino', $this->solicitud_id)->orderBy('id')->limit(1)->get();
        if ($Origen->count() > 0) {
            $Solicitud = Solicitud::find($Origen[0]->solicitud_origen);
            $mensaje = $this->nombre. ' '.$this->apellido.' viene derivado(a) de la Planilla ID: '.$Solicitud->id.' | '.$Solicitud->descripcion_sin_estado().' | Inscriptor/Tutor: '.$Solicitud->nombre_responsable_de_inscripciones.' cel: '.$Solicitud->celular_responsable_de_inscripciones;
            $boton_wa = ' <a href="https://api.whatsapp.com/send?phone='.$Solicitud->celular_wa($Solicitud->celular_responsable_de_inscripciones).'" target="_blank">';
            $boton_wa .= '<button type="button" class="btn btn-success btn-xs"><i class="fa fa-fw fa-whatsapp" style="font-size: 19px"></i></button></a>';
            
            return $mensaje.$boton_wa;
        }
    }



    public function mostrarCertificado($Solicitud, $ocultar_certificados, $cant_asistencias, $orden_de_ultima_leccion_vista, $cant_evaluaciones) {
        
        $certificado = 'false';


        if (!$ocultar_certificados) {

            $cant_minima_de_lecciones = 16;
            if (!is_null($Solicitud->cant_de_asistencias_para_certificado)) {
                $cant_minima_de_lecciones = $Solicitud->cant_de_asistencias_para_certificado;            
            } 

            $cant_minima_de_evaluaciones = 0;
            if (!is_null($Solicitud->cant_de_evaluaciones_para_certificado)) {
                $cant_minima_de_evaluaciones = $Solicitud->cant_de_evaluaciones_para_certificado;            
            } 

            if ($cant_asistencias >= $cant_minima_de_lecciones and $cant_evaluaciones >= $cant_minima_de_evaluaciones) {
                $certificado = 'true';
            }

            /*
            if ($orden_de_ultima_leccion_vista >= 17) {
                $certificado = 'true';    
            }
            */

        }

        return $certificado;
    }



    public function url_certificado($pdf = false)
    {
        $inscripto_id = $this->id;
        $hash = md5(ENV('PREFIJO_HASH').$inscripto_id);
        if ($pdf) {
            $url_certificado = ENV('PATH_PUBLIC')."f/certificado-pdf/$inscripto_id/$hash";
        }
        else {
            $url_certificado = ENV('PATH_PUBLIC')."f/certificado/$inscripto_id/$hash";
        }
        return $url_certificado;
    }



    public function asistio_a_leccion($leccion_id)
    {

        $asistencias = $this->asistencias->where('leccion_id', $leccion_id)->all();

        if (count($asistencias) > 0) {
            $sino_asistencia = 'SI';
        }
        else {
            $sino_asistencia = '';
        }
        
        return $sino_asistencia;
    }

    public function LetraCapital($frase)
    {
        $a_nombre = explode(' ', $frase);
        $fraseCapital = '';
        foreach ($a_nombre as $nombre) {
            $fraseCapital .= ucfirst($nombre).' ';
        }
        return $fraseCapital;
    }

    public function url_form_inscripcion()
    {
        $url = env('PATH_PUBLIC').'f/'.$this->id.'/'.$this->hash;
        return $url;
    }

    public function solicitud()
    {
        return $this->belongsTo('App\Solicitud');
    }

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    }
    
    public function fecha_de_evento()
    {
        return $this->belongsTo('App\Fecha_de_evento');
    }

    public function asistencias()
    {
        return $this->hasMany('App\Asistencia');
    }

    protected $table = 'inscripciones';  
}
