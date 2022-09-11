<?php

namespace App;

use Auth;
use App\Misionero_por_evento;
use Illuminate\Database\Eloquent\Model;
use App\Encuesta_de_satisfaccion;
use \App\Http\Controllers\FxC; 
use App\Http\Controllers\GenericController;
$FxC = new FxC();



class Fecha_de_evento extends Model
{
	protected $guarded = ['id']; 

    public function descrip_modelo()
    {    

        $fecha_de_inicio = strtotime($this->fecha_de_inicio);

        if (isset($this->solicitud->idioma->mnemo)) {
            $idioma = $this->solicitud->idioma->mnemo;
        }
        else {
            $idioma = 'es';
        }
        
        $Idioma_por_pais = $this->solicitud->idioma_por_pais();

        if ($idioma == 'en') {
            $nombre_dia = date("l", $fecha_de_inicio);
        }
        else {
            $numero_dia = date("N", $fecha_de_inicio);
            $fcx = new FxC();
            $nombre_dia = __($fcx->nombre_de_dia($numero_dia));
        }

        $GenericController = new GenericController();
        $fecha_de_inicio = $GenericController->FormatoFecha($this->fecha_de_inicio);
        $descripcion = $nombre_dia.' - '.$fecha_de_inicio;

        return $descripcion;
    }

    public function cant_inscriptos()
    {    
        //$cant = Inscripcion::where('fecha_de_evento_id', $this->id)->whereRaw('(sino_cancelo IS NULL OR sino_cancelo = "NO")')->count();        
        $cant = Inscripcion::where('fecha_de_evento_id', $this->id)->count();        
        return $cant;
    }
    public function cant_contactados()
    {    
        $cant = Inscripcion::where('fecha_de_evento_id', $this->id)->where('sino_envio_pedido_de_confirmacion', 'SI')->count();        
        return $cant;
    }
    public function cant_confirmados()
    {    
        $cant = Inscripcion::where('fecha_de_evento_id', $this->id)->where('sino_confirmo', 'SI')->whereRaw('(sino_cancelo IS NULL or sino_cancelo = "NO")')->count();        
        return $cant;
    }
    public function cant_vouchers()
    {    
        $cant = Inscripcion::where('fecha_de_evento_id', $this->id)->where('sino_envio_voucher', 'SI')->count();        
        return $cant;
    }
    public function cant_motivacion()
    {    
        $cant = Inscripcion::where('fecha_de_evento_id', $this->id)->where('sino_envio_motivacion', 'SI')->count();        
        return $cant;
    }

    public function cant_recordatorio()
    {    
        $cant = Inscripcion::where('fecha_de_evento_id', $this->id)->where('sino_envio_recordatorio', 'SI')->count();        
        return $cant;
    }
    public function cant_asistentes()
    {    
        $cant = Inscripcion::where('fecha_de_evento_id', $this->id)->where('sino_asistio', 'SI')->count();        
        return $cant;
    }
    public function cant_cancelados()
    {    
        $cant = Inscripcion::where('fecha_de_evento_id', $this->id)->where('sino_cancelo', 'SI')->count();        
        return $cant;
    }

    public function FormatoHora($hora, $Idioma_por_pais = null, $idioma = null) {

        $hora_time = strtotime($hora);

        if ($idioma == null) {
            $idioma = $this->solicitud->idioma->mnemo;
        }

        if ($Idioma_por_pais == null) {
            $Idioma_por_pais = $this->solicitud->idioma_por_pais();
        }
        

        if ($Idioma_por_pais->formato_de_hora_id == 1) {
            $formato24  = 'S';
        }
        else {
            $formato24  = 'N';
        }        

        $hora_mostrar_prev = '';
        $hora_mostrar_post = '';

        if ($formato24 == 'S') {
            if (date("i", $hora_time) == '00' and $idioma <> 'sv') {
                $hora_mostrar_post = date("G", $hora_time);
            }
            else {
                $hora_mostrar_post = date("G:i", $hora_time);                
            }



            if ($idioma == 'it') {
                $hora_mostrar_prev = 'alle ore ';
            }
            else {
                if ($idioma == 'sv') {
                    $hora_mostrar_prev = 'kl ';       
                }
                else { 
                    $hora_mostrar_post .= 'h';
                }
            }
            
        }
        else {
            $hora_mostrar_post = date("g:ia", $hora_time);
            if ($idioma == 'ja') {
                $hora_mostrar_prev='';
                $ampm_ja = date("a", $hora_time);
                $hora_ja = date("g", $hora_time);
                $minutos_ja = date("i", $hora_time);
                if ($ampm_ja == 'am') {
                    $ampm_ja = '午前';
                }
                else {
                    $ampm_ja = '午後';
                }
                
                $hora_mostrar_post = $ampm_ja.$hora_ja.'時';

                if ($minutos_ja <> '00') {                    
                    $hora_mostrar_post .= $minutos_ja.'分';
                }
            }
        }                    
        
        return  $hora_mostrar_prev.$hora_mostrar_post;
    }

    public function FechayHoraInicio($Idioma_por_pais = null, $idioma = null) {

        $fcx = new FxC();
        $fecha_de_inicio = strtotime($this->fecha_de_inicio);

        if ($idioma == null) {
            if ($this->solicitud->idioma_id <> '') {
                $idioma = $this->solicitud->idioma->mnemo;
            }
        }

        if ($Idioma_por_pais == null) {
            $Idioma_por_pais = $this->solicitud->idioma_por_pais();
        }

        if ($idioma == 'en') {
            $nombre_dia = date("l", $fecha_de_inicio);
        }
        else {
            $numero_dia = date("N", $fecha_de_inicio);
            $nombre_dia = __($fcx->nombre_de_dia($numero_dia));
        }
        $dia = date("d", $fecha_de_inicio);
        $numero_de_mes = date("m", $fecha_de_inicio);
        $mes = $fcx->nombre_de_mes($numero_de_mes);
        $mes = __($mes);
        $hora = $this->FormatoHora($this->hora_de_inicio, $Idioma_por_pais, $idioma);

        $inicio = "$nombre_dia $dia de $mes $hora";

        if ($idioma == 'en') {
            $inicio = "$nombre_dia, $dia $mes $hora";
        }
        if ($idioma == 'fr') {
            $inicio = "$nombre_dia $dia $mes $hora";
        }
        if ($idioma == 'it') {
            $inicio = "$nombre_dia $dia $mes $hora";
        }
        if ($idioma == 'al') {
            $inicio = "$nombre_dia, $dia. $mes $hora";
        } 
        if ($idioma == 'sv') {
            $inicio = "$nombre_dia $mes $dia, $hora";
        }
        if ($idioma == 'hu') {
            $inicio = "$nombre_dia $mes $dia, $hora";
        }
        if ($idioma == 'ar') {
            $inicio = "$mes $dia $nombre_dia $hora";
        }  
        
        return  $inicio;
    }

    public function hora_continua($tipo_de_evento_id = null) {

        if ($tipo_de_evento_id == null) {
            $tipo_de_evento_id = $this->solicitud->Tipo_de_evento->id;
        }

        if ($tipo_de_evento_id == 1) {
            $prox_clase = $this->prox_clase();
            if ($prox_clase <> null) { 
                $hora_continua = $prox_clase['hora'];
            }
            else {
                $hora_continua = $this->hora_de_inicio;
            }
        }
        else {
            $hora_continua = $this->hora_de_inicio;
        }

        return  $hora_continua;
    }

    public function prox_clase() {

        $numero_dia_hoy = date("N");

        $array_dias = [
            null,
            $this->hora_lunes, 
            $this->hora_martes, 
            $this->hora_miercoles, 
            $this->hora_jueves, 
            $this->hora_viernes, 
            $this->hora_sabado, 
            $this->hora_domingo
        ];
        
        $prox_clase = null;
        
        for ($i=$numero_dia_hoy; $i<=7; $i++) {
            if ($array_dias[$i] <> '') {
                $prox_clase = [
                    'dia' => $i,
                    'hora' => $array_dias[$i],
                ];
                $i = 7;
            }
        }

        if ($prox_clase == null) {

            for ($i=1; $i<$numero_dia_hoy; $i++) {
                if ($array_dias[$i] <> '') {
                    $prox_clase = [
                        'dia' => $i,
                        'hora' => $array_dias[$i],
                    ];
                    $i = $numero_dia_hoy;
                }
            }

        }

        return $prox_clase;
    }


    public function prox_clase_dia() {

        $prox_clase = $this->prox_clase();
        //
        if ($prox_clase == null) {
            $fecha_de_inicio = strtotime($this->fecha_de_inicio);
            $numero_dia = date("N", $fecha_de_inicio);
        }
        else {
            $numero_dia = $prox_clase['dia'];
        }
        $fcx = new FxC();
        $nombre_dia = __($fcx->nombre_de_dia($numero_dia));
        
        return  $nombre_dia;
    }


    public function armarDetalleFechasDeEventos($tipo = 'html', $con_inicio = true, $Idioma_por_pais = null, $Solicitud = null, $idioma = null, $ver_mapa = true, $con_dir_inicio_distinto = true) {

        $detalle = '';

        if ($Solicitud == null) {
            $Solicitud = Solicitud::where('id', $this->solicitud_id)->first();
        }
            
        if ($Idioma_por_pais == null) {
            $Idioma_por_pais = $this->solicitud->idioma_por_pais();
        }

        $formato24  = 'S';
        if ($Idioma_por_pais <> null) {
            if ($Idioma_por_pais->formato_de_hora_id <> null) {
                if ($Idioma_por_pais->formato_de_hora_id == 1) {
                	$formato24  = 'S';
                }
                else {
                	$formato24  = 'N';
                }
            }
        }
        


        

		$inicio = $this->FechayHoraInicio($Idioma_por_pais, $idioma);

        // CURSO DE AUTOCONOCIMIENTO
            if ($Solicitud->Tipo_de_evento->id == 1 or $Solicitud->Tipo_de_evento->id == 3) {

                if ($this->titulo_de_conferencia_publica <> '' and $tipo <> 'select') {
                    if ($tipo == 'whatsapp') {
                        $detalle = '*'.$this->titulo_de_conferencia_publica."*\n";
                    }
                    else {
                        $detalle = '<h4>'.$this->titulo_de_conferencia_publica.'</h4>';
                    }

                }

                if ($this->titulo_de_conferencia_publica <> '' and $tipo == 'select') {
                    if ($tipo <> 'whatsapp') {
                        $detalle = $this->titulo_de_conferencia_publica.' | ';
                    }

                }

                $detalle .= __('Clases').": ".$this->dias_y_horarios($Idioma_por_pais, $idioma);

                // DIRECCION
                if ($Solicitud->Tipo_de_evento->id == 1) {
                    $misma_dir = 'N';
                    if ($tipo <> 'select') {
                        if ($this->direccion_de_inicio == $this->direccion_del_curso or $this->url_enlace_a_google_maps_inicio == $this->url_enlace_a_google_maps_curso or ($this->direccion_del_curso == '' and $this->url_enlace_a_google_maps_curso == '')) {
                            $misma_dir = 'S';
                            $direccion = $this->direccion_de_inicio;
                            $url_mapa = $this->url_enlace_a_google_maps_inicio;
                            if ($tipo == 'whatsapp') {
                                if ($con_inicio) {
                                    $detalle .= " (".__('Apertura de curso').": $inicio".")";
                                }
                                $detalle .= "\n".__('Lugar').": $direccion\n";
                                if ($ver_mapa) {
                                    $detalle .= __('Ver Mapa').": $url_mapa\n";
                                }
                            }
                            else {
                                if ($con_inicio) {
                                    $detalle .= " (".__('Apertura de curso').": $inicio".")";
                                }
                                $detalle .= "<br>".__('Lugar').": $direccion <br>";
                                if ($ver_mapa) {
                                    $detalle .= __('Ver Mapa').": <a href='$url_mapa' target='_blank' class='url_ver_mapa'>$url_mapa</a><br>";
                                }
                            }
                        }
                        else {
                            $direccion = $this->direccion_del_curso;
                            $url_mapa = $this->url_enlace_a_google_maps_curso;
                            if ($tipo == 'whatsapp') {
                                $detalle .= "\n".__('Lugar').": $direccion \n";
                                if ($ver_mapa) {
                                    $detalle .= __('Ver Mapa').": $url_mapa \n";
                                }
                            }
                            else {
                                $detalle .= "<br>".__('Lugar').": $direccion <br>";
                                if ($ver_mapa) {
                                    $detalle .= __('Ver Mapa').": <a href='$url_mapa' target='_blank' class='url_ver_mapa'>$url_mapa</a><br>";
                                }
                            }
                            
                            if ($con_inicio) {
                                $direccion = $this->direccion_de_inicio;
                                $url_mapa = $this->url_enlace_a_google_maps_inicio;
                                if ($tipo == 'whatsapp') {
                                    $detalle .= "\n".__('Apertura de curso').": $inicio"."\n";
                                    if ($con_dir_inicio_distinto) {
                                        $detalle .= __('Lugar').": $direccion \n";
                                    }
                                    if ($ver_mapa) {
                                        $detalle .= __('Ver Mapa').": $url_mapa";
                                    }
                                }
                                else {
                                    $detalle .= "<br>".__('Apertura de curso').": $inicio"."<br>";
                                    if ($con_dir_inicio_distinto) {
                                        $detalle .= __('Lugar').": $direccion <br>";
                                    }
                                    if ($ver_mapa) {
                                        $detalle .= __('Ver Mapa').": <a href='$url_mapa' target='_blank' class='url_ver_mapa'>$url_mapa</a>";
                                    }
                                }
                            }
                        }      
                    } 
                    else {
                        $direccion = $this->direccion_de_inicio;
                        $detalle .= ' '.__('Lugar').": $direccion";
                        
                        if ($con_inicio) {
                            $detalle .= " (".__('Inicio').": $inicio".")";
                        }
                    }
                }
                if ($Solicitud->Tipo_de_evento->id == 3) {

                    if ($tipo <> 'select') {
                        if ($tipo == 'whatsapp') {
                            if ($con_inicio) {
                                $detalle .= " (".__('Apertura de curso').": $inicio".")";
                            }
                        }
                        else {
                            if ($con_inicio) {
                                $detalle .= " (".__('Apertura de curso').": $inicio".")";
                            }
                        }
                    }


                }                
                // DIRECCION

            }
        // CURSO DE AUTOCONOCIMIENTO

        // CONFERENCIA PUBLICA
            if ($Solicitud->Tipo_de_evento->id == 2) {
                if ($tipo <> 'select') {
                    $direccion = $this->direccion_de_inicio;
                    $url_mapa = $this->url_enlace_a_google_maps_inicio;
                    if ($tipo == 'whatsapp') {
                        $detalle = '*'.$this->titulo_de_conferencia_publica."*\n";
                            if ($con_inicio) {
                                $detalle .= $inicio;
                            }                    
                            $detalle .= "\n".__('Lugar').": $direccion \n".__('Ver Mapa').": $url_mapa \n";
                    }
                    else {
                        $detalle = '<h4>'.$this->titulo_de_conferencia_publica.'</h4>';
                        if ($tipo == 'con_resumen') {
                            $detalle .= '<p><i>'.$this->resumen_de_la_conferencia.'</i></p>';
                        }
                        if ($con_inicio) {
                            $detalle .= $inicio;
                        }
                            $detalle .= "<br>".__('Lugar').": $direccion <br>".__('Ver Mapa').": <a href='$url_mapa' target='_blank' class='url_ver_mapa'>$url_mapa</a><br>";
                    }
                }
                else {
                     $detalle = $this->titulo_de_conferencia_publica;
                }
            }
        // CONFERENCIA PUBLICA

        // FORM RECOLECCION DE DATOS
            if ($Solicitud->Tipo_de_evento->id == 4) {
                     $detalle = $this->titulo_de_conferencia_publica;
                }
        // FORM RECOLECCION DE DATOS


        return $detalle;

    }


    public function lugarDelEvento($tipo, $lugar_de_inicio) {

        if ($this->direccion_de_inicio == $this->direccion_del_curso or $this->url_enlace_a_google_maps_inicio == $this->url_enlace_a_google_maps_curso or ($this->direccion_del_curso == '' and $this->url_enlace_a_google_maps_curso == '')) {
            $misma_dir = true;
        }
        else {
            $misma_dir = false;
        }

        if ($lugar_de_inicio or $misma_dir) {
            $direccion = $this->direccion_de_inicio;
            $url_mapa = $this->url_enlace_a_google_maps_inicio;
            $url_fachada = $this->url_enlace_foto_de_fachada_del_lugar;
        }
        else {
            $direccion = $this->direccion_del_curso;
            $url_mapa = $this->url_enlace_a_google_maps_curso;
            $url_fachada = $this->url_enlace_foto_de_fachada_del_lugar;
        }

        $detalle = '';
        if ($tipo == 'whatsapp') {
            if (trim($url_fachada) <> '') {
                $detalle .= "$url_fachada\n";
            }
            $detalle .= __('Lugar').": $direccion\n".__('Ver Mapa').": $url_mapa\n";
        }
        else {
            $detalle = __('Lugar').": $direccion <br>".__('Ver Mapa').": <a href='$url_mapa' target='_blank'>$url_mapa</a><br>";
            if (trim($url_fachada) <> '') {
                $detalle .= "<img src='$url_fachada' style='max-width: 100%'><br>";
            }
        }

        return $detalle;
    }

    public function solicitud()
    {
        return $this->belongsTo('App\Solicitud');
    }

    public function dias_y_horarios($Idioma_por_pais = null, $idioma = null) {

        if ($idioma == null) {
            $idioma = $this->solicitud->idioma->mnemo;
        }

        $y = 'y';

        if ($idioma == 'es') {
            $y = 'y';
        }
        if ($idioma == 'en') {
            $y = 'and';
        }
        if ($idioma == 'pt-BR') {
            $y = 'e';
        }
        if ($idioma == 'fr') {
            $y = 'et';
        }
        if ($idioma == 'it') {
            $y = 'e';
        }
        if ($idioma == 'de') {
            $y = 'und';
        }
        if ($idioma == 'sv') {
            $y = 'och';
        }
        if ($idioma == 'hu') {
            $y = 'és';
        }

        $array_fecha = array();
        // DIAS Y HORA DEL CURSO
        if($this->hora_lunes <> '') { 
            $dia = __('Lunes');
            $hora = $this->hora_lunes;
            array_push($array_fecha, array($dia, $hora));
        }

        if($this->hora_martes <> '') { 
            $dia = __('Martes');
            $hora = $this->hora_martes;
            array_push($array_fecha, array($dia, $hora));
        }

        if($this->hora_miercoles <> '') { 
            $dia = __('Miércoles');
            $hora = $this->hora_miercoles;
            array_push($array_fecha, array($dia, $hora));
        }

        if($this->hora_jueves <> '') { 
            $dia = __('Jueves');
            $hora = $this->hora_jueves;
            array_push($array_fecha, array($dia, $hora));
        }

        if($this->hora_viernes <> '') { 
            $dia = __('Viernes');
            $hora = $this->hora_viernes;
            array_push($array_fecha, array($dia, $hora));
        }

        if($this->hora_sabado <> '') { 
            $dia = __('Sábado');
            $hora = $this->hora_sabado;
            array_push($array_fecha, array($dia, $hora));
        }


        if($this->hora_domingo <> '') { 
            $dia = __('Domingo');
            $hora = $this->hora_domingo;
            array_push($array_fecha, array($dia, $hora));
        }

        $hora_1 = '';
        $i = 0;
        $misma_hora = 'SI';
        foreach ($array_fecha as $fecha) {
            $array_hora = explode(':', $fecha[1]);
            if ($array_hora[1] == '00' and  $idioma <> 'sv') {
                $hora_y_minutos = $array_hora[0];
            }
            else {
                $hora_y_minutos = $array_hora[0].':'.$array_hora[1];
            }
            if ($i == 0) {
                $hora_1 = $hora_y_minutos;
            }
            else {
                if ($hora_1 <> $hora_y_minutos) {
                    $misma_hora = 'NO';
                }
            }
            $i++;
        }

        $detalle = '';

        $cant_y = count($array_fecha)-1;
        $i = 0;

        if (count($array_fecha) > 0) {
            if ($misma_hora == 'SI') {
                foreach ($array_fecha as $fecha) {
                    /*
                    $array_hora = explode(':', $fecha[1]);
                    if ($array_hora[1] == '00') {
                        $hora_y_minutos = $array_hora[0];
                    }
                    else {
                        $hora_y_minutos = $array_hora[0].':'.$array_hora[1];
                    }
                    */
                    
                    if ($detalle == '') {
                        if ($idioma == 'pt-BR' or $idioma == 'pt') {
                            if ($this->hora_lunes <> '' or $this->hora_martes <> '' or $this->hora_miercoles <> '' or $this->hora_jueves <> '' or $this->hora_viernes <> '') {
                                $detalle = 'Todas '.$fecha[0];    
                            }
                            else {
                                $detalle = 'Todos '.$fecha[0];                                    
                            }
                        }                        
                        else {
                            $detalle = __('Cada').' '.$fecha[0];
                        }
                    }
                    else {
                        if ($cant_y == $i) {
                            $detalle .= " $y ".$fecha[0];
                        }
                        else {
                            $detalle .= ', '.$fecha[0];
                        }
                    }

                    $i++;   
                }
                $hora_1 = $this->FormatoHora($fecha[1], $Idioma_por_pais, $idioma);
                $detalle .= ' '.$hora_1;
            }
            else {
                foreach ($array_fecha as $fecha) {
                    /*
                    $array_hora = explode(':', $fecha[1]);
                    if ($array_hora[1] == '00') {
                        $hora_y_minutos = $array_hora[0];
                    }
                    else {
                        $hora_y_minutos = $array_hora[0].':'.$array_hora[1];
                    }
                    */

                    $hora_y_minutos = $this->FormatoHora($fecha[1], $Idioma_por_pais, $idioma);
                    if ($detalle == '') {
                        $detalle = $fecha[0].' '.$hora_y_minutos;
                    }
                    else {
                        if ($cant_y == $i) {
                            $detalle .= " $y ".$fecha[0].' '.$hora_y_minutos;
                        }
                        else {
                            $detalle .= ', '.$fecha[0].' '.$hora_y_minutos;
                        }
                    }

                    $i++;   
                }
            }
        }
        //$detalle = utf8_decode(str_replace('é', 'é', $detalle));
        //dd($detalle);
        return $detalle;


    }




    public function datos_url_google_maps()
    {



        $fecha_de_inicio = strtotime($this->fecha_de_inicio);
        $fecha_de_inicio = strtotime(date("d-m-Y 00:00:00", $fecha_de_inicio));
        $now = strtotime(date("d-m-Y 00:00:00",time()));
        if ($now < $fecha_de_inicio or $this->url_enlace_a_google_maps_curso_redirect_final == '') {
            $url = $this->url_enlace_a_google_maps_inicio_redirect_final;
        }
        else {
            $url = $this->url_enlace_a_google_maps_curso_redirect_final;
        }


        $datos = [
            'url' => $url,
            'latitud' => '',
            'longitud' => '',
            ];


        if ($this->latitud <> '' and $this->longitud <> '') {
            $datos = [
                'url' => $url,
                'latitud' => $this->latitud,
                'longitud' => $this->longitud,
            ];
        }
        else {

            $array_url = explode('!3d', $url);
            $sum_indice = 0;

            if (count($array_url) == 3) {
                $array_url = explode('!4d', $array_url[2]);
                if (count($array_url) > 1) {
                    $latitud = $array_url[0];
                    $longitud = $array_url[1];

                    $datos = [
                        'url' => $url,
                        'latitud' => $latitud,
                        'longitud' => $longitud,
                    ];
                }
            }

            if (count($array_url) == 2) {
                $array_url = explode('?', $array_url[1]);
                $array_url = explode('!4d', $array_url[0]);
                if (count($array_url) > 1) {
                    $latitud = $array_url[0];
                    $longitud = $array_url[1];

                    $datos = [
                        'url' => $url,
                        'latitud' => $latitud,
                        'longitud' => $longitud,
                    ];
                }
            }

            if ($datos['latitud'] == '') {
                $array_url = explode('maps?q=', $url);

                if (count($array_url) > 1) {  
                    $array_url = explode(',', $array_url[1]);
                    if (count($array_url) > 1) {
                        $latitud = $array_url[0];
                        $longitud = $array_url[1];
                        if (is_numeric($latitud) and is_numeric($longitud)) {
                            $datos = [
                                'url' => $url,
                                'latitud' => $latitud,
                                'longitud' => $longitud,
                            ];
                        }
                    }
                }
            }
        }


        return $datos;
    }


    public function cant_encuestas()
    {    
        $cant = Encuesta_de_satisfaccion::
        join('inscripciones as i', 'i.id', '=', 'encuestas_de_satisfaccion.inscripcion_id')
        ->where('i.fecha_de_evento_id', $this->id)
        ->count();        
        return $cant;
    }

    public function url_encuesta_de_satisfaccion()
    {
        $url = env('PATH_PUBLIC').'reportes/encuesta-satisfaccion/2/'.$this->id;
        return $url;
    }


    public function url_curso()
    {
        $hash = md5(ENV('PREFIJO_HASH').$this->id);
        $url = env('PATH_PUBLIC').'c/'.$this->id.'/'.$hash;
        return $url;
    }

    public function es_instructor($user_id = null) 
    {
        $es_instructor = 'false';

        if ($user_id == null and Auth::user()) {
            $user_id = Auth::user()->id;
        }

        if ($user_id > 0) {
            $cant_misionero_por_evento = Misionero_por_evento::where('user_id', $user_id)->where('fecha_de_evento_id', $this->id)->count();

            if ($cant_misionero_por_evento > 0) {
                $es_instructor = 'true';
            }
        }

        return $es_instructor;
    }

    protected $table = 'fechas_de_evento';  
}
