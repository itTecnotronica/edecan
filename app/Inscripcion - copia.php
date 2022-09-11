<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

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


    public function celular_wa($codigo_tel = null, $Solicitud = null)
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

        if ($Solicitud <> null) {
            if ($Solicitud->tipo_de_evento_id == 3) {
                $codigo_tel = '';
            }            
        }
        else {
            if ($this->solicitud->tipo_de_evento_id == 3) {
                $codigo_tel = '';
            } 
        } 

        if (substr($celular_wa, 0, 1) <> '+') {
            if (substr($celular_wa, 0, strlen($codigo_tel)) <> $codigo_tel) {
                $celular_wa = $codigo_tel.$celular_wa;
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

    public function texto_final_sitio_web_y_redes($Idioma_por_pais) {


        $texto_final_sitio_web_y_redes = '';
        $sitio_web_y_redes = '';        

        $url_fanpage = $Idioma_por_pais->url_fanpage;
        if ($url_fanpage <> '') {
            $sitio_web_y_redes .= 'Facebook: '.$url_fanpage."\n";
        }

        $url_sitio_web = $Idioma_por_pais->url_sitio_web;
        if ($url_sitio_web <> '') {
            $sitio_web_y_redes .= 'Sitio Web: '.$url_sitio_web."\n";
        }

        $url_youtube = $Idioma_por_pais->url_youtube;
        if ($url_youtube <> '') {
            $sitio_web_y_redes .= 'Youtube: '.$url_youtube.'?sub_confirmation=1'."\n";
        }

        $url_twitter = $Idioma_por_pais->url_twitter;
        if ($url_twitter <> '') {
            $sitio_web_y_redes .= 'Twitter: '.$url_twitter."\n";
        }

        $url_instagram = $Idioma_por_pais->url_instagram;
        if ($url_instagram <> '') {
            $sitio_web_y_redes .= 'Instagram: '.$url_instagram."\n";
        }

        if ($sitio_web_y_redes <> '') {
            $texto_final_sitio_web_y_redes = "\n\n".__('También lo invitamos visitar nuestro sitio web y redes sociales, donde encontrará mucho material acerca de este maravilloso conocimiento').':'."\n\n".$sitio_web_y_redes;
        }

        return $texto_final_sitio_web_y_redes;

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
    	   $tel_responsable_inscripcion = $fecha_de_evento->solicitud->celular_responsable_de_inscripciones;
        }

        if ($nombre_de_ciudad == null) {
    	   $nombre_de_ciudad = $fecha_de_evento->solicitud->localidad_nombre();
        }

        if ($nombre_responsable_inscripcion == null) {
    	   $nombre_responsable_inscripcion = $fecha_de_evento->solicitud->nombre_responsable_de_inscripciones;
        }

    	$inscrito_nombre = mb_strtoupper($this->nombre, 'UTF-8');
    	$inscrito_apellido = mb_strtoupper($this->apellido, 'UTF-8');
        $denominacion_de_voucher = $Idioma_por_pais->denominacion_de_voucher;
        $inscripto_id = $this->id;
        $hash = md5(ENV('PREFIJO_HASH').$inscripto_id);
        $url_voucher = ENV('PATH_PUBLIC')."f/v/$inscripto_id/$hash";

        if ($tipo_de_evento_id == null) {
            $tipo_de_evento_id = $fecha_de_evento->solicitud->tipo_de_evento_id;
        }

        if ($tipo_de_evento == null) {
            $tipo_de_evento = __($fecha_de_evento->solicitud->tipo_de_evento->tipo_de_evento);
        }

        if ($tipo_de_evento_id == 1) {
            if ($Idioma_por_pais->pais_id == 6) {
                $txt_tipo_de_evento = $tipo_de_evento;
            }
            else {
                $txt_tipo_de_evento = __("el")." ".$tipo_de_evento;
            }
        }
        else {
            $txt_tipo_de_evento = __("la")." ".$tipo_de_evento;
        }

        $detalle_fecha = $fecha_de_evento->armarDetalleFechasDeEventos('whatsapp', true, $Idioma_por_pais, $Solicitud, $idioma);
        $detalle_fecha_sin_inicio = $fecha_de_evento->armarDetalleFechasDeEventos('whatsapp', false, $Idioma_por_pais, $Solicitud, $idioma);
        $inicio_en_texto = $fecha_de_evento->FechayHoraInicio($Idioma_por_pais, $idioma);
        $hora_inicio = $fecha_de_evento->FormatoHora($fecha_de_evento->hora_de_inicio, $Idioma_por_pais, $idioma);
        $hora_continua = $fecha_de_evento->FormatoHora($fecha_de_evento->hora_continua($tipo_de_evento_id), $Idioma_por_pais, $idioma);
        $nombre_conferencia =  $fecha_de_evento->titulo_de_conferencia_publica;
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


        if ($tipo_de_evento_id == 1) {
            $pedido_de_confirmacion = $Idioma_por_pais->Modelo_de_mensaje->pedido_de_confirmacion_curso;    
        }
        else {
            $pedido_de_confirmacion = $Idioma_por_pais->Modelo_de_mensaje->pedido_de_confirmacion_conferencia;    
        }
       
        $pedido_de_confirmacion = preg_replace($patrones, $sustituciones, $pedido_de_confirmacion);
        $mail_pedido_de_confirmacion = $pedido_de_confirmacion;
        $urlencode_pedido_de_confirmacion = $this->CodificarURL($pedido_de_confirmacion, $Idioma_por_pais);
        $pedido_de_confirmacion = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text='.$urlencode_pedido_de_confirmacion;

        $no_respondieron_al_pedido_de_confirmacion = $Idioma_por_pais->Modelo_de_mensaje->no_respondieron_al_pedido_de_confirmacion;
        $no_respondieron_al_pedido_de_confirmacion = preg_replace($patrones, $sustituciones, $no_respondieron_al_pedido_de_confirmacion);
        $mail_no_respondieron_al_pedido_de_confirmacion = $no_respondieron_al_pedido_de_confirmacion;
        $urlencode_no_respondieron_al_pedido_de_confirmacion = $this->CodificarURL($no_respondieron_al_pedido_de_confirmacion, $Idioma_por_pais);
        $no_respondieron_al_pedido_de_confirmacion = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text='.$urlencode_no_respondieron_al_pedido_de_confirmacion;


        $envio_de_voucher = $Idioma_por_pais->Modelo_de_mensaje->envio_de_voucher;
        $envio_de_voucher = preg_replace($patrones, $sustituciones, $envio_de_voucher);
        $mail_envio_de_voucher = $envio_de_voucher;
        $urlencode_envio_de_voucher = $this->CodificarURL($envio_de_voucher, $Idioma_por_pais);
        $envio_de_voucher = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text='.$urlencode_envio_de_voucher;

        $envio_de_motivacion = $Idioma_por_pais->Modelo_de_mensaje->envio_de_motivacion;
        $envio_de_motivacion = preg_replace($patrones, $sustituciones, $envio_de_motivacion);
        $envio_de_motivacion .= $this->texto_final_sitio_web_y_redes($Idioma_por_pais);
        $mail_envio_de_motivacion = $envio_de_motivacion;
        $urlencode_envio_de_motivacion = $this->CodificarURL($envio_de_motivacion, $Idioma_por_pais);
        $envio_de_motivacion = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text='.$urlencode_envio_de_motivacion;

        $envio_de_recordatorio = $Idioma_por_pais->Modelo_de_mensaje->envio_de_recordatorio;
        $envio_de_recordatorio = preg_replace($patrones, $sustituciones, $envio_de_recordatorio);
        $mail_envio_de_recordatorio = $envio_de_recordatorio;
        $urlencode_envio_de_recordatorio = $this->CodificarURL($envio_de_recordatorio, $Idioma_por_pais);
        $envio_de_recordatorio = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text='.$urlencode_envio_de_recordatorio;

        $contesto_consulta = $Idioma_por_pais->Modelo_de_mensaje->envio_de_respuesta_a_consulta;
        $contesto_consulta = preg_replace($patrones, $sustituciones, $contesto_consulta);
        $mail_contesto_consulta = $contesto_consulta;
        $urlencode_contesto_consulta = $this->CodificarURL($contesto_consulta, $Idioma_por_pais);
        $contesto_consulta = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text='.$urlencode_contesto_consulta;

        $envio_de_recordatorio_prox_clase = $Idioma_por_pais->Modelo_de_mensaje->envio_de_recordatorio_clase_posterior;
        $envio_de_recordatorio_prox_clase = preg_replace($patrones, $sustituciones, $envio_de_recordatorio_prox_clase);
        $mail_envio_de_recordatorio_prox_clase = $envio_de_recordatorio_prox_clase;
        $urlencode_envio_de_recordatorio_prox_clase = $this->CodificarURL($envio_de_recordatorio_prox_clase, $Idioma_por_pais);
        $envio_de_recordatorio_prox_clase = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text='.$urlencode_envio_de_recordatorio_prox_clase;

        $envio_de_recordatorio_prox_clase_no_asistente = $Idioma_por_pais->Modelo_de_mensaje->envio_de_recordatorio_clase_posterior_a_no_asistente;
        $envio_de_recordatorio_prox_clase_no_asistente = preg_replace($patrones, $sustituciones, $envio_de_recordatorio_prox_clase_no_asistente);
        $mail_envio_de_recordatorio_prox_clase_no_asistente = $envio_de_recordatorio_prox_clase_no_asistente;
        $urlencode_envio_de_recordatorio_prox_clase_no_asistente = $this->CodificarURL($envio_de_recordatorio_prox_clase_no_asistente, $Idioma_por_pais);
        $envio_de_recordatorio_prox_clase_no_asistente = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text='.$urlencode_envio_de_recordatorio_prox_clase_no_asistente;


        $url_whatsapp = [
            'pedido_de_confirmacion' => $pedido_de_confirmacion,
            'no_respondieron_al_pedido_de_confirmacion' => $no_respondieron_al_pedido_de_confirmacion,
            'envio_de_voucher' => $envio_de_voucher,
            'envio_de_motivacion' => $envio_de_motivacion,
            'envio_de_recordatorio' => $envio_de_recordatorio,
            'contesto_consulta' => $contesto_consulta,
            'envio_de_recordatorio_prox_clase' => $envio_de_recordatorio_prox_clase,
            'envio_de_recordatorio_prox_clase_no_asistente' => $envio_de_recordatorio_prox_clase_no_asistente,
            'mail_pedido_de_confirmacion' => $mail_pedido_de_confirmacion,
            'mail_no_respondieron_al_pedido_de_confirmacion' => $mail_no_respondieron_al_pedido_de_confirmacion,
            'mail_envio_de_voucher' => $mail_envio_de_voucher,
            'mail_envio_de_motivacion' => $mail_envio_de_motivacion,
            'mail_envio_de_recordatorio' => $mail_envio_de_recordatorio,
            'mail_contesto_consulta' => $mail_contesto_consulta,
            'mail_envio_de_recordatorio_prox_clase' => $mail_envio_de_recordatorio_prox_clase,
            'mail_envio_de_recordatorio_prox_clase_no_asistente' => $mail_envio_de_recordatorio_prox_clase_no_asistente
        ];

        return $url_whatsapp;
    }


    public function url_whatsapp_sin_evento($nombre_de_la_institucion = null, $tel_responsable_inscripcion = null, $nombre_responsable_inscripcion = null, $nombre_de_ciudad = null, $denominacion_de_voucher = null, $tipo_de_evento_id = null, $tipo_de_evento = null, $codigo_tel = null, $contesto_consulta = null, $Idioma_por_pais = null, $Solicitud = null)
    {

        if ($Idioma_por_pais == null) {
            if ($Solicitud <> null) {
                $Idioma_por_pais = $Solicitud->idioma_por_pais();
            }
            else {
                $Idioma_por_pais = $this->solicitud->idioma_por_pais();
            }
        }

        if ($nombre_de_la_institucion == null or $denominacion_de_voucher == null) {
            $nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;
            $denominacion_de_voucher = $Idioma_por_pais->denominacion_de_voucher;
        }

        $buenos_dias_tardes_noches = __('Hola');

        if ($tel_responsable_inscripcion == null) {
            if ($Solicitud <> null) {
                $tel_responsable_inscripcion = $Solicitud->celular_responsable_de_inscripciones;
            }
            else {
                $tel_responsable_inscripcion = $this->solicitud->celular_responsable_de_inscripciones;
            }            
        }
        
        if ($nombre_de_ciudad == null) {
            if ($Solicitud <> null) {
                $nombre_de_ciudad = $Solicitud->localidad_nombre();
            }
            else {
                $nombre_de_ciudad = $this->solicitud->localidad_nombre();
            }  
        }

        if ($nombre_responsable_inscripcion == null) {
            if ($Solicitud <> null) {
                $nombre_responsable_inscripcion = $Solicitud->nombre_responsable_de_inscripciones;
            }
            else {
                $nombre_responsable_inscripcion = $this->solicitud->nombre_responsable_de_inscripciones;
            } 
        }

        $inscrito_nombre = mb_strtoupper($this->nombre, 'UTF-8');
        $inscrito_apellido = mb_strtoupper($this->apellido, 'UTF-8');
        $inscripto_id = $this->id;
        $hash = md5(ENV('PREFIJO_HASH').$inscripto_id);
        $url_voucher = ENV('PATH_PUBLIC')."f/v/$inscripto_id/$hash";

        if ($tipo_de_evento_id == null) {
            if ($Solicitud <> null) {
                $tipo_de_evento_id = $Solicitud->tipo_de_evento_id;
            }
            else {
                $tipo_de_evento_id = $this->solicitud->tipo_de_evento_id;
            } 
        }

        if ($tipo_de_evento == null) {
            if ($Solicitud <> null) {
                $tipo_de_evento = __($Solicitud->tipo_de_evento->tipo_de_evento);
            }
            else {
                $tipo_de_evento = __($this->solicitud->tipo_de_evento->tipo_de_evento);
            } 
        }

        if ($tipo_de_evento_id == 1) {
            $txt_tipo_de_evento = __("el")." ".$tipo_de_evento;
        }
        else {
            $txt_tipo_de_evento = __("la")." ".$tipo_de_evento;
        }

        $consulta_del_inscripto = $this->consulta;



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


        $pedido_de_confirmacion = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text=';


        if ($contesto_consulta == null) {
            $contesto_consulta = $Idioma_por_pais->Modelo_de_mensaje->envio_de_respuesta_a_consulta;
        }
        $contesto_consulta = preg_replace($patrones, $sustituciones, $contesto_consulta);
        $urlencode_contesto_consulta = $this->CodificarURL($contesto_consulta, $Idioma_por_pais);
        $contesto_consulta = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($codigo_tel, $Solicitud).'&text='.$urlencode_contesto_consulta;

        $url_whatsapp = [
            'pedido_de_confirmacion' => $pedido_de_confirmacion,
            'contesto_consulta' => $contesto_consulta
        ];

        return $url_whatsapp;

        
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
                $entities = array(' de il ', ' a el ');
                $replacements = array(' del ', ' al ');
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

    protected $table = 'inscripciones';  
}
