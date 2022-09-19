<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;
use DateTime;
use App\Idioma_por_pais;
use App\Fecha_de_evento;
use App\Texto_anuncios;
use App\Equipo;
use App\Leccion;
use App\Modelo_de_mensaje_curso;
use App\Grupo_de_solicitud;
use App\Leccion_por_pais_e_idioma;
use App;
use Solicitud as Solicitud2;
use App\Notifications\TelegramNotification;
Use Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GenericController;
use App\Http\Controllers\FxC;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SolicitudController;
use App\Campaign;
use App\Campaign_lead;


class Solicitud extends Model
{
	protected $guarded = ['id'];    


    public function descrip_modelo()
    {
        $titulo_de_conferencia_publica = '';

        if ($this->titulo_del_formulario_personalizado == '') {
            $descripcion = __($this->Tipo_de_evento->tipo_de_evento).$titulo_de_conferencia_publica.' - '.$this->localidad_nombre();
        }
        else {
            if ($this->Tipo_de_evento->id == 2) {
                $titulo_de_conferencia_publica = ' ('.$this->nombres_de_conferencias().')';
            }
            $array_estado = $this->estado();
            $estado = $array_estado['estado'];
            $descripcion = 'ID: '.$this->id.' - '.__($this->Tipo_de_evento->tipo_de_evento).$titulo_de_conferencia_publica.' - '.$this->localidad_nombre().' - '.__('Estado').': '.__($estado);
            if ($this->fecha_de_inicio_del_curso_online <> '') {
                $gCont = new GenericController();
                $descripcion .= ' - Inicio del Curso: '.$gCont->FormatoFecha($this->fecha_de_inicio_del_curso_online);
            } 
        }

        return $descripcion;
    }

    public function descripcion_con_estado_css()
    {
        $titulo_de_conferencia_publica = '';
        if ($this->Tipo_de_evento->id == 2) {
            $titulo_de_conferencia_publica = ' ('.$this->nombres_de_conferencias().')';
        }
        $array_estado = $this->estado();
        $span_estado = $array_estado['span_estado'];
        $descripcion = 'ID: '.$this->id.' - '.$this->Tipo_de_evento->tipo_de_evento.$titulo_de_conferencia_publica.' - '.$this->localidad_nombre().' - Estado: '.$span_estado;

        return $descripcion;
    }

    public function descripcion_sin_estado($con_html = true)
    {

        $descripcion = '';
        
        if ($this->idioma_id <> '') {
            $idioma = $this->idioma->mnemo;                        
            App::setLocale($idioma);  
        }

        if ($this->titulo_del_formulario_personalizado == '') {
            if ($this->tipo_de_evento->id == 1) {
                $descripcion = __('CURSO DE AUTO-CONOCIMIENTO').'<br><strong> '.$this->localidad_nombre().'</strong>';  
            }
            if ($this->tipo_de_evento->id == 2) {
                if ($this->cant() == 1) {
                    $descripcion = __('CONFERENCIA PÚBLICA').':<br><strong> '.$this->fechas_de_evento[0]->titulo_de_conferencia_publica.'</strong><br>'.$this->localidad_nombre();   
                }
                else {
                    $descripcion = __('CICLO DE CONFERENCIAS PÚBLICAS').'<br>'.' ('.$this->nombres_de_conferencias().')'.'<br>'.$this->localidad_nombre();            
                }                    
            }
            if ($this->tipo_de_evento->id == 3) {
                $descripcion = __('CURSO DE AUTO-CONOCIMIENTO ON LINE').'<br><strong> '.$this->localidad_nombre().'</strong>';  
            }
            if ($this->tipo_de_evento->id == 4) {
                $descripcion = '';  
            }
        }
        else {
            $descripcion = $this->titulo_del_formulario_personalizado;
        }

        if (!$con_html) {
            $descripcion = strip_tags($descripcion, '<br>');
            $descripcion = str_replace('<br>', ' | ', $descripcion);

        }

        return $descripcion;
    }



    public function nombres_de_conferencias() {

        $nombres_de_conferencias = '';
        
        if ($this->tipo_de_evento_id == 2) {
            foreach ($this->fechas_de_evento as $Fecha_de_evento) {
                $nombres_de_conferencias .= $Fecha_de_evento->titulo_de_conferencia_publica.' | ';
            }

            $len = strlen($nombres_de_conferencias);

            if ($len > 4) {
                $nombres_de_conferencias = substr($nombres_de_conferencias, 0, ($len-3));
            }
        }

        return $nombres_de_conferencias;

    }


    public function localidad_nombre()
    {
        if($this->localidad_id > 0) {
            $localidad = $this->Localidad->localidad;
        }
        else {
            $localidad = $this->escribe_tu_ciudad_sino_esta_en_la_lista_anterior;
        }

        if ($localidad == '' and  $this->pais_id <> '') {
            $localidad = __($this->pais->pais);   
        }

        return $localidad;
    }

    public function idioma_por_pais()
    {
        $institucion_id = $this->institucion_id;
        if ($institucion_id == null) {
            $institucion_id = 1;
        }

        $idioma_id = $this->idioma_id;
        if($this->localidad_id <> null) {
            $pais_id = $this->localidad->provincia->pais->id;
            $Idioma_por_pais = Idioma_por_pais::where('idioma_id', $idioma_id)->where('pais_id', $pais_id)->where('institucion_id', $institucion_id)->first();
            if ($Idioma_por_pais == null) {
                $Idioma_por_pais = Idioma_por_pais::where('pais_id', $pais_id)->first();
            }
        }
        else {
            if($this->pais_id <> null) {
                $pais_id = $this->pais_id;
            }
            else {
                if (Auth::user()) {
                    $pais_id = Auth::user()->pais_id;
                }
                else {
                    $pais_id = $this->user->pais_id;
                }                
            }
            if ($idioma_id <> null) {
                $Idioma_por_pais = Idioma_por_pais::where('pais_id', $pais_id)->where('idioma_id', $idioma_id)->where('institucion_id', $institucion_id)->first();
            }
            else {
                $Idioma_por_pais = Idioma_por_pais::where('pais_id', $pais_id)->first();
            }
        }

        if ($Idioma_por_pais == null) {
            $Idioma_por_pais = Idioma_por_pais::where('idioma_id', $idioma_id)->first();
        }

        if ($Idioma_por_pais == null) {
            $Idioma_por_pais = Idioma_por_pais::find(3);

        }


        return $Idioma_por_pais;
    }

    public function url_contacto_whatsapp_form() {
        $con_html = false;
        $url = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($this->celular_responsable_de_inscripciones).'&text=';
        $url2 = __('Hola').' '.$this->nombre_responsable_de_inscripciones.'. '.__('Sobre el evento').' '.$this->descripcion_sin_estado($con_html).', '.__('mi pregunta es la siguiente').':';
        //$url_encode = urlencode($url2);
        return $url.$url2;

    }
    public function url_contacto_whatsapp_anuncio() {
        $url = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($this->celular_responsable_de_inscripciones);
        return $url;

    }

    public function celular_wa($celular_responsable_de_inscripciones, $codigo_tel = '')
    {
        $celular_wa = trim($celular_responsable_de_inscripciones);

        if ($codigo_tel == '') {
            $pais_id = $this->id_pais();
            if ($pais_id <> '') {
                $Pais = Pais::find($pais_id);
                $codigo_tel = $Pais->codigo_tel;            
            }
        }

        if ($codigo_tel <> '') {
            if (substr($celular_wa, 0, 1) <> '+') {
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
    

    public function id_pais()
    {
        $pais_id = '';
        if($this->localidad_id > 0) {
            $pais_id = $this->localidad->provincia->pais->id;
        }
        else {
            if($this->pais_id > 0) {
                $pais_id = $this->pais_id;
            }
            else {
                if (!Auth::guest()) {
                    $pais_id = Auth::user()->pais_id;
                }
            }            
        }
        return $pais_id;
    }


    public function estado()
    {
        $estado = '';
        $letra_estado = '';
        $class_estado  = '';
        $span_estado = '';

        if(Auth::user()) {
            $rol_de_usuario_id = Auth::user()->rol_de_usuario_id;
        }

        if (!Auth::guest()) {
            if ($this->sino_aprobado_administracion == 'SI' and ($this->sino_aprobado_finalizada == '' or $this->sino_aprobado_finalizada == 'NO') ) {
                $letra_estado = 'a';
                $estado = 'Aprobada';
                $class_estado = 'bg-green';
            }
            if ($rol_de_usuario_id < 3 ) {
              if ($this->sino_aprobado_administracion == 'NO' and ($this->sino_aprobado_solicitar_revision == 'NO' or $this->sino_aprobado_solicitar_revision == '')) {
                $letra_estado = 'd';
                $estado = 'Desaprobada';
                $class_estado = 'bg-red';
              }
            }
            else {
              if ($this->sino_aprobado_administracion == 'NO' ) {
                $letra_estado = 'd';
                $estado = 'Desaprobada';
                $class_estado = 'bg-red';
              }
            }
            if ($rol_de_usuario_id < 3 ) {
              if ($this->sino_aprobado_administracion == '') {
                $letra_estado = 'p';
                $estado = 'Pendiente';
                $class_estado = 'bg-yellow';
              }
            }
            else {
              if (($this->sino_aprobado_administracion == '') or ($this->sino_aprobado_administracion == 'NO' and $this->sino_aprobado_solicitar_revision == "SI")) {
                $letra_estado = 'p';
                $estado = 'Pendiente';
                $class_estado = 'bg-yellow';
              }
            }
            if ($rol_de_usuario_id < 3 ) {
              if ($this->sino_aprobado_administracion == "NO" AND $this->sino_aprobado_solicitar_revision == 'SI') {
                $letra_estado = 'r';
                $estado = 'Revisar';
                $class_estado = 'bg-yellow';
              }
            }
            else {
              if ($this->sino_aprobado_administracion == "NO" AND $this->sino_aprobado_solicitar_revision == '') {
                $letra_estado = 'r';
                $estado = 'Revisar';
                $class_estado = 'bg-yellow';
              }
            }

            if ($this->sino_cancelada == 'SI') {
                $letra_estado = 'c';
                $estado = 'Cancelada';
                $class_estado = 'bg-red';
            }
            if ($this->sino_aprobado_finalizada == 'SI') {
                $letra_estado = 'f';
                $estado = 'Finalizada';
                $class_estado = 'bg-blue';
            }
        }
        else {
            if ($this->sino_aprobado_administracion == 'SI' and ($this->sino_aprobado_finalizada == '' or $this->sino_aprobado_finalizada == 'NO') ) {
                $letra_estado = 'a';
                $estado = 'Aprobada';
                $class_estado = 'bg-green';
            }
            
            if ($this->sino_aprobado_administracion == 'NO' and ($this->sino_aprobado_solicitar_revision == 'NO' or $this->sino_aprobado_solicitar_revision == '')) {
                $letra_estado = 'd';
                $estado = 'Desaprobada';
                $class_estado = 'bg-red';
            }
            
            
            if ($this->sino_aprobado_administracion == '') {
                $letra_estado = 'p';
                $estado = 'Pendiente';
                $class_estado = 'bg-yellow';
            }
           
            if ($this->sino_aprobado_administracion == "NO" AND $this->sino_aprobado_solicitar_revision == 'SI') {
                $letra_estado = 'r';
                $estado = 'Revisar';
                $class_estado = 'bg-yellow';
            }
            if ($this->sino_cancelada == 'SI') {
                $letra_estado = 'c';
                $estado = 'Cancelada';
                $class_estado = 'bg-red';
            }
            if ($this->sino_aprobado_finalizada == 'SI') {
                $letra_estado = 'f';
                $estado = 'Finalizada';
                $class_estado = 'bg-blue';
            }
        }

                    

        $span_estado = '<span class="badge '.$class_estado.' datos-finales-asistente">'.__($estado).'</span>';


        $array_estado = [
            'estado' => $estado,
            'letra_estado' => $letra_estado,
            'class_estado' => $class_estado,
            'span_estado' => $span_estado
        ];
        //dd($this->sino_aprobado_administracion);
        return $array_estado;
    }

    public function cant()
    {
        
        $cant = Fecha_de_evento::where('solicitud_id', $this->id)->count();
        return $cant;
    }

    public function cant_inscriptos_sin_fecha_de_evento()
    {    
        $cant = Inscripcion::whereNull('fecha_de_evento_id')->where('solicitud_id', $this->id)->count();        
        return $cant;
    }

    public function cant_inscriptos_sin_fecha_de_evento_contactados()
    {    
        $cant = Inscripcion::whereNull('fecha_de_evento_id')->where('solicitud_id', $this->id)->where('sino_envio_pedido_de_confirmacion', 'SI')->count();        
        return $cant;
    }

    public function cant_inscriptos_cancelados()
    {    
        $cant = Inscripcion::whereNull('fecha_de_evento_id')->where('solicitud_id', $this->id)->where('sino_cancelo', 'SI')->count();        
        return $cant;
    }

    public function cant_inscriptos()
    {    
        $cant = Inscripcion::where('solicitud_id', $this->id)->count();        
        return $cant;
    }

    public function cant_inscriptos_unicos()
    {    
        $cant = Inscripcion::where('solicitud_id', $this->id)->distinct('email_correo')->count('email_correo');
        return $cant;
    }

    public function cant_visualizaciones()
    {    
        //$cant = Visualizacion_de_formulario::where('solicitud_id', $this->id)->whereRaw("(url_anterior  NOT like 'https://ac.gnosis.is%' or url_anterior = 'https://ac.gnosis.is/f/registrar-inscripcion')")->count();        
        $cant = Visualizacion_de_formulario::where('solicitud_id', $this->id)->count();        
        return $cant;
    }

    public function cant_en_grupos()
    {    
        $cant = Inscripcion::where('solicitud_id', $this->id)->where('grupo', '>', 0)->count();        
        return $cant;
    }


    public function ejecutivo_asignado()
    {
        
        $ejecutivo = User::find($this->ejecutivo);
        return $ejecutivo;
    }

    public function tipo_de_evento()
    {
        return $this->belongsTo('App\Tipo_de_evento');
    }

    /*
    public function conferencia_publica()
    {
        return $this->hasOne(Conferencia_publica::class);
    } 
    */   
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function AsignarEjecutivo()
    {

        $NotificationController = new NotificationController();
        $pais_id = $this->id_pais();

        //busco el equipo y los paises que dependen de equipo
        $Equipos = Equipo::where('pais_id', $pais_id)->get();
        if (count($Equipos) == 1) {
            $equipo_id = $Equipos[0]->id;
            $asignar_ejecutivos = $Equipos[0]->sino_asignacion_de_ejecutivos_automatica;
            $coordinador_user_id = $Equipos[0]->coordinador_user_id;
        }
        else {
            $Paises_por_equipo = Pais_por_equipo::where('pais_id', $pais_id)->get();
            if (count($Paises_por_equipo) > 0) {
                $equipo_id = $Paises_por_equipo[0]->equipo_id;
                $asignar_ejecutivos = $Paises_por_equipo[0]->equipo->sino_asignacion_de_ejecutivos_automatica;
                $coordinador_user_id = $Paises_por_equipo[0]->equipo->coordinador_user_id;
            }
            else {
                $Equipos = Equipo::whereNull('pais_id')->get();
                $equipo_id = $Equipos[0]->id;
                $asignar_ejecutivos = $Equipos[0]->sino_asignacion_de_ejecutivos_automatica;
                $coordinador_user_id = $Equipos[0]->coordinador_user_id;
            }

        }
        
        $SolicitudController = new SolicitudController();
        $paisesDelEquipo = $SolicitudController->paisesDelEquipo($equipo_id);
        $in_paises = $paisesDelEquipo['in_paises'];

        if ($this->ejecutivo == '') {
            $ejecutivo_id = '';
            if ($asignar_ejecutivos == "SI") {
                $ult_solicitud_id = Solicitud::
                whereRaw("localidad_id in (SELECT l.id FROM localidades l INNER JOIN provincias p ON l.provincia_id = p.id WHERE p.pais_id $in_paises)")
                ->whereNotNull('ejecutivo')
                ->whereRaw('ejecutivo in (SELECT u.id FROM users u WHERE u.rol_de_usuario_id = 3)')
                ->max('id');


                $Ejecutivos = DB::table('usuarios_por_equipo as ue')
                ->select(DB::raw('DISTINCT u.*'))
                ->join('users as u', 'u.id', '=', 'ue.user_id')
                ->leftjoin('roles_extra as re', 'u.id', '=', 're.user_id')
                ->whereRaw('(u.rol_de_usuario_id = 3 or re.rol_de_usuario_id = 3)')
                ->where('ue.equipo_id', $equipo_id)
                ->whereRaw('(u.sino_activo IS NULL OR u.sino_activo = "SI")')
                ->orderBy('id')
                ->get();

                
                /*
                else {
                    $ult_solicitud_id = Solicitud::
                    whereRaw('localidad_id in (SELECT l.id FROM localidades l INNER JOIN provincias p ON l.provincia_id = p.id WHERE p.pais_id <> 6)')
                    ->whereNotNull('ejecutivo')
                    ->whereRaw('ejecutivo in (SELECT u.id FROM users u WHERE u.rol_de_usuario_id = 3)')
                    ->max('id');            
                    $Ejecutivos = User::where('rol_de_usuario_id', 3)->where('pais_id', '<>', 6)->orderBy('id')->get();
                }
                */

                $ult_solicitud = Solicitud::find($ult_solicitud_id);
                $primer_ejecutivo = 'N';
                if ($ult_solicitud <> null) {
                    $ult_ejecutivo_id = $ult_solicitud->ejecutivo;
                

                    $prox = 'N';
                    if ($ult_ejecutivo_id <> '') {
                        foreach ($Ejecutivos as $Ejecutivo) {
                            if ($prox == 'S') {
                                $ejecutivo_id = $Ejecutivo->id;
                            }

                            if ($Ejecutivo->id == $ult_ejecutivo_id or $Ejecutivo->id == 0) {
                                $prox = 'S';
                            }
                            else {
                                $prox = 'N';    
                            }
                        }
                        if ($prox == 'S' or $ejecutivo_id == '') {
                            $primer_ejecutivo = 'S';
                        }
                    }
                    else {
                        $primer_ejecutivo = 'S';
                    }
                }
                else {
                    $primer_ejecutivo = 'S';
                }

                if ($primer_ejecutivo == 'S') {
                    $ejecutivo_id = $Ejecutivos[0]->id;
                }
                
                $this->ejecutivo = $ejecutivo_id;
                $ejecutivo_asignado = User::find($ult_solicitud_id);
                //Mail::to('fernandomadoz@hotmail.com')->send('te enviamos un nuevo mail');

                $ejecutivo = $this->ejecutivo_asignado();
                $user_id = $ejecutivo->id;
                $mensaje = __('Se le ha asignado una nueva campaña').': '.$this->descripcion_sin_estado().' - '.__('ir a la campaña').' -> '.env('PATH_PUBLIC')."Solicitudes/solicitud/ver/".$this->id;
                $NotificationController->enviarNotificacion(1, $user_id, $mensaje);  

                $mensaje = __('Se le ha asignado una nueva campaña').': '.$this->descripcion_sin_estado().' - '.__('ir a la campaña').' -> <a href="'.env('PATH_PUBLIC').'Solicitudes/solicitud/ver/'.$this->id.'">'.env('PATH_PUBLIC')."Solicitudes/solicitud/ver/".$this->id;
                $NotificationController->enviarNotificacion(2, $user_id, $mensaje);  

            }
        }

        //NOTIFICO AL COORDINADOR DEL EQUIPO DE UNA NUEVA CAMPAÑA
        $Pais = Pais::find($pais_id);
        $pais = $Pais->pais;

        $mensaje = __('Se ha solicitado una nueva campaña para').' '.$pais.': '.$this->descripcion_sin_estado().' - '.__('ir a la campaña').' -> '.env('PATH_PUBLIC')."Solicitudes/solicitud/ver/".$this->id;        
        $NotificationController->enviarNotificacion(1, $coordinador_user_id, $mensaje);       

        $mensaje = __('Se ha solicitado una nueva campaña para').' '.$pais.': '.$this->descripcion_sin_estado().' - '.__('ir a la campaña').' -> <a href="'.env('PATH_PUBLIC').'Solicitudes/solicitud/ver/'.$this->id.'">'.env('PATH_PUBLIC')."Solicitudes/solicitud/ver/".$this->id;  
        $NotificationController->enviarNotificacion(2, $coordinador_user_id, $mensaje);                 
                   

    }


    public function dominioPublico($Idioma_por_pais = null, $dominio_publico = null)
    {
        if ($Idioma_por_pais == null) {
            $Idioma_por_pais = $this->idioma_por_pais();
        }
    
        if ($dominio_publico == null) {
            if (strpos($Idioma_por_pais->dominio_publico, $_SERVER['HTTP_HOST'])) {
                $dominio_publico = $Idioma_por_pais->dominio_publico;    
            }
            else {
                $dominio_publico = env('PATH_PUBLIC');
            }
        }
    
        return $dominio_publico;
    }

    public function url_wabot_inscripcion()
    {
        
        $Idioma_por_pais = $this->idioma_por_pais();

        $url = 'https://api.whatsapp.com/send?phone=558003843646&text=gnosis_in%20a%23'.$this->id.'%20(haz%20click%20para%20empezar)';
        
        if ($Idioma_por_pais->idioma_id == 5 or $Idioma_por_pais->idioma_id == 6) {
            $url = 'https://api.whatsapp.com/send?phone=558003843646&text=gnosis_in_pt%20a%23'.$this->id.'%20(haz%20click%20para%20empezar)';
        }

        if ($Idioma_por_pais->idioma_id == 2) {
            $url = 'https://api.whatsapp.com/send?phone=558003843646&text=gnosis_in_en%20a%23'.$this->id.'%20(haz%20click%20para%20empezar)';
        }

        if ($Idioma_por_pais->idioma_id == 3) {
            $url = 'https://api.whatsapp.com/send?phone=558003843646&text=gnosis_in_fr%20a%23'.$this->id.'%20(haz%20click%20para%20empezar)';
        }
        

        return $url;
    }

    public function url_form_inscripcion()
    {

        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'f/'.$this->id.'/'.$this->hash;
        return $url;
    }

    public function url_form_inscripcion_contacto_historico()
    {

        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'fc/'.$this->id.'/'.$this->hash.'/217';
        return $url;
    }

    public function url_form_inscripcion_con_campania_id($campania_id)
    {
        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'fc/'.$this->id.'/'.$this->hash.'/'.$campania_id;
        return $url;
    }

    public function url_planilla_inscripcion()
    {
        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'f/i/'.$this->id.'/'.$this->hash;
        return $url;
    }

    public function url_planilla_inscripcion_excel($fecha_de_evento_id)
    {
        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'f/x/'.$this->id.'/'.$this->hash.'/'.$fecha_de_evento_id;
        return $url;
    }

    public function url_planilla_asistencia($grupo = null)
    {
        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        if ($grupo === null) {
            $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'f/a/'.$this->id.'/'.$this->hash;
        }
        else {
            $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'f/agrupo/'.$this->id.'/'.$this->hash.'/'.$grupo;    
        }
        return $url;
    }


    public function url_planilla_contactos_historicos()
    {
        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'f/h/'.$this->id.'/'.$this->hash;
        return $url;
    }

    public function url_enlaces_wabot()
    {
        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'f/raw/'.$this->id.'/'.$this->hash;
        return $url;
    }

    public function url_encuesta_de_satisfaccion()
    {
        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'reportes/encuesta-satisfaccion/1/'.$this->id;
        return $url;
    }

    public function url_grupo_whatsapp($grupo_id)
    {
        $Idioma_por_pais = $this->idioma_por_pais();
        $dominio_publico = $Idioma_por_pais->dominio_publico;
        $url = $this->dominioPublico($Idioma_por_pais, $dominio_publico).'f/igrupo/'.$this->id.'/'.$this->hash.'/'.$grupo_id;
        return $url;
    }

    public function cant_encuestas()
    {    
        $cant = Encuesta_de_satisfaccion::
        join('inscripciones as i', 'i.id', '=', 'encuestas_de_satisfaccion.inscripcion_id')
        ->where('i.solicitud_id', $this->id)
        ->count();        
        return $cant;
    }

    public function texto_anuncios_facebook()
    {

        $nombre_de_ciudad = $this->localidad_nombre();
        $ciudad_en_mayuscula = strtoupper($this->localidad_nombre());
        $icon_manito = '👉';
        $icon_check = '✅';
        $url_form = $this->url_form_inscripcion();
        $tel_responsable_inscripcion = $this->celular_responsable_de_inscripciones;
        $url_whatsapp = $this->url_contacto_whatsapp_anuncio();

        $detalle_horarios_y_lugar = 'HORARIOS: '."\n"."-------"."\n\n";
        foreach ($this->fechas_de_evento as $Fecha_de_evento) {
            $tipo = 'whatsapp';
            $con_inicio = true;
            $Idioma_por_pais = null;
            $Solicitud = null; 
            $idioma = null; 
            $ver_mapa = false;
            $con_dir_inicio_distinto = true;

            $detalle_horarios_y_lugar .= $Fecha_de_evento->armarDetalleFechasDeEventos($tipo, $con_inicio, $Idioma_por_pais, $Solicitud, $idioma, $ver_mapa, $con_dir_inicio_distinto);
            
            $detalle_horarios_y_lugar .= "-------"."\n\n";

            //$detalle_horarios_y_lugar .= $Fecha_de_evento->dias_y_horarios()."\n";
            
        }

        // pedido_de_confirmacion_curso
        $patrones = array();
        $patrones[0] = '/nombre_de_ciudad/';
        $patrones[1] = '/icon_manito/';
        $patrones[2] = '/url_form/';
        $patrones[3] = '/tel_responsable_inscripcion/';
        $patrones[4] = '/url_whatsapp/';
        $patrones[5] = '/detalle_horarios_y_lugar/';
        $patrones[6] = '/ciudad_en_mayuscula/';
        $patrones[7] = '/icon_check/';
        $sustituciones = array();
        $sustituciones[0] = $nombre_de_ciudad;
        $sustituciones[1] = $icon_manito;
        $sustituciones[2] = $url_form;
        $sustituciones[3] = $tel_responsable_inscripcion;
        $sustituciones[4] = $url_whatsapp;
        $sustituciones[5] = $detalle_horarios_y_lugar;
        $sustituciones[6] = $ciudad_en_mayuscula;
        $sustituciones[7] = $icon_check;


        $Idioma_por_pais = $this->idioma_por_pais();
        $tipo_de_evento_id = $this->tipo_de_evento_id;
        $texto_anuncios_id = '';

        $titulo = '';
        $descripcion = '';                

        $Texto_anuncios_cant = Texto_anuncios::where('idioma_por_pais_id', $Idioma_por_pais->id)->where('tipo_de_evento_id', $tipo_de_evento_id)->count();


        if ($Texto_anuncios_cant == 0) {
            if ($tipo_de_evento_id == 1) {
                $Texto_anuncios = Texto_anuncios::find(1);
                $titulo = $Texto_anuncios->titulo;
                $descripcion = $Texto_anuncios->descripcion;                
            }  
            if ($tipo_de_evento_id == 3) {
                $Texto_anuncios = Texto_anuncios::find(2);
                $titulo = $Texto_anuncios->titulo;
                $descripcion = $Texto_anuncios->descripcion;                
            }          

        }
        else {
            $Texto_anuncios = Texto_anuncios::where('idioma_por_pais_id', $Idioma_por_pais->id)->where('tipo_de_evento_id', $tipo_de_evento_id)->get();
            $titulo = $Texto_anuncios[0]->titulo;
            $descripcion = $Texto_anuncios[0]->descripcion;                
        }
       
        $titulo = preg_replace($patrones, $sustituciones, $titulo);
        $descripcion = preg_replace($patrones, $sustituciones, $descripcion);


        $textos = [
            'titulo' => $titulo,
            'descripcion' => $descripcion
        ];

        return $textos;
    }


    
    public function texto_lecciones_de_curso($Grupo_de_solicitud = null)
    {


        $curso_id = $this->curso_id;
        if ($curso_id == '') {
            $curso_id = 1;
        }
        $icon_solcito = '🌞';
        $icon_flechita = '➡';
        
        $gCont = new GenericController();
        $Idioma_por_pais = $this->idioma_por_pais();
        $nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;

        $tel_responsable_inscripcion = $this->celular_responsable_de_inscripciones;
        $nombre_responsable_inscripcion = trim($this->nombre_responsable_de_inscripciones);
        if ($Grupo_de_solicitud <> null) {
            $cant = Grupo_de_solicitud::where('nro_de_grupo', $Grupo_de_solicitud->nro_de_grupo)->where('solicitud_id', $this->id)->count();
            if ($cant > 0) {
                $Grupo_de_solicitud_search = Grupo_de_solicitud::where('nro_de_grupo', $Grupo_de_solicitud->nro_de_grupo)->where('solicitud_id', $this->id)->get();
                $grupo_id = $Grupo_de_solicitud_search[0]->id;
                $Grupo_de_solicitud = Grupo_de_solicitud::find($grupo_id);
                $tel_responsable_inscripcion = $Grupo_de_solicitud->celular_responsable_de_inscripciones;
                $nombre_responsable_inscripcion = trim($Grupo_de_solicitud->nombre_responsable_de_inscripciones);
            }
        }

        $url_whatsapp = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($tel_responsable_inscripcion);

        if ($this->envio_de_leccion <> '') {
            $envio_de_leccion = $this->envio_de_leccion;
        }
        else {
            if ($Idioma_por_pais->envio_de_leccion <> '') {
                $envio_de_leccion = $Idioma_por_pais->envio_de_leccion;
            }
            else  {
                $envio_de_leccion = $Idioma_por_pais->Modelo_de_mensaje->envio_de_leccion;
            }
        }



        $texto_lecciones_de_curso = [];

        $Lecciones = Leccion::where('curso_id', $curso_id)->orderBy('orden_de_leccion')->get();


        $i = 0;


        foreach ($Lecciones as $Leccion) {

            $Leccion_por_pais_e_idioma = Leccion_por_pais_e_idioma::where('idioma_por_pais_id', $Idioma_por_pais->id)->where('leccion_id', $Leccion->id)->get();
            //dd($Idioma_por_pais->id);
            $texto_leccion = $Leccion->texto_leccion($Leccion_por_pais_e_idioma);
            $leccion_id = $texto_leccion['id'];
            $codigo_de_la_leccion = $texto_leccion['codigo_de_la_leccion'];
            $nombre_de_la_leccion = $texto_leccion['nombre_de_la_leccion'];
            $enlaces_de_la_leccion = $texto_leccion['enlaces_de_la_leccion'];

            $modalidad_de_notificacion_de_asistencia_id = 1;
            if ($this->modalidad_de_notificacion_de_asistencia_id > 1) {
                $modalidad_de_notificacion_de_asistencia_id = $this->modalidad_de_notificacion_de_asistencia_id;
            }
            
            $url_notificacion_leccion_finalizada = $Leccion->url_notificacion_leccion_finalizada($this, $modalidad_de_notificacion_de_asistencia_id, $Idioma_por_pais);
            
            $url_wabot_notificacion_leccion_finalizada = $Leccion->url_notificacion_leccion_finalizada($this, 2,$Idioma_por_pais);

            $patrones = array();
            $patrones[0] = '/nombre_de_la_leccion/';
            $patrones[1] = '/enlaces_de_la_leccion/';
            $patrones[2] = '/icon_solcito/';
            $patrones[3] = '/icon_flechita/';
            $patrones[4] = '/url_whatsapp/';
            $patrones[5] = '/tel_responsable_inscripcion/';
            $patrones[6] = '/nombre_de_la_institucion/';
            $patrones[7] = '/nombre_responsable_inscripcion/';
            $patrones[8] = '/codigo_de_la_leccion/';
            $patrones[9] = '/url_notificacion_leccion_finalizada/';
            $patrones[10] = '/url_wabot_notificacion_leccion_finalizada/';
            $sustituciones = array();
            $sustituciones[0] = $nombre_de_la_leccion;
            $sustituciones[1] = $enlaces_de_la_leccion;
            $sustituciones[2] = $icon_solcito;
            $sustituciones[3] = $icon_flechita;
            $sustituciones[4] = $url_whatsapp;
            $sustituciones[5] = $tel_responsable_inscripcion;
            $sustituciones[6] = $nombre_de_la_institucion;
            $sustituciones[7] = $nombre_responsable_inscripcion;
            $sustituciones[8] = $codigo_de_la_leccion;
            $sustituciones[9] = $url_notificacion_leccion_finalizada;
            $sustituciones[10] = $url_wabot_notificacion_leccion_finalizada;

            if ($Idioma_por_pais->pais_id == 1) {
                if ($this->fecha_de_inicio_del_curso_online == '2020-05-04' and $leccion_id == 1 and $Idioma_por_pais->envio_de_leccion <> '') {
                    $envio_de_leccion = $Idioma_por_pais->envio_de_leccion;
                }
                else  {
                    $envio_de_leccion = $Idioma_por_pais->Modelo_de_mensaje->envio_de_leccion;
                }

            }

            $texto = preg_replace($patrones, $sustituciones, $envio_de_leccion);
            $fxc = new FxC();
            $texto_codificado = $fxc->CodificarURL($texto);

            $fecha_de_envio = '';
            if ($this->fecha_de_inicio_del_curso_online <> '') {
                $fecha_de_envio = date('Y-m-d', strtotime($this->fecha_de_inicio_del_curso_online. " + $i weeks"));
            }

            $array_texto = [
                'codigo_de_la_leccion' => $codigo_de_la_leccion,
                'nombre_de_la_leccion' => $nombre_de_la_leccion,
                'fecha_de_envio' => $fecha_de_envio,
                'url_whatsapp_texto' => $url_whatsapp.'&text='.$texto_codificado
            ];


            $i++;
            

            array_push($texto_lecciones_de_curso, $array_texto);
            
        }

        return $texto_lecciones_de_curso;
    }



    public function texto_modelo_del_mensaje_del_curso($modelo_de_mensaje_curso_id, $Grupo_de_solicitud = null, $codigo_tel = '')
    {

        $icon_4diamantes = '💠';
        $icon_check = '✅';
        $icon_solcito = '🌞';
        $icon_flechita = '➡';

        if ($this->idioma_id <> '') {
            $idioma = $this->idioma->mnemo;           
            $locale_vee_validate = $this->idioma->locale_vee_validate;             
            App::setLocale($idioma);  
        }
        else {
            $idioma_por_pais = $this->idioma_por_pais();
            if ($idioma_por_pais->idioma_id <> '') {
                $idioma = $idioma_por_pais->idioma->mnemo;    
                $locale_vee_validate = $idioma_por_pais->idioma->locale_vee_validate;                    
                App::setLocale($idioma);  
            }
        }

        $tel_responsable_inscripcion = $this->celular_responsable_de_inscripciones;
        $nombre_responsable_inscripcion = trim($this->nombre_responsable_de_inscripciones);
        if ($Grupo_de_solicitud <> null) {
            $cant = Grupo_de_solicitud::where('nro_de_grupo', $Grupo_de_solicitud->nro_de_grupo)->where('solicitud_id', $this->id)->count();
            if ($cant > 0) {
                $Grupo_de_solicitud_search = Grupo_de_solicitud::where('nro_de_grupo', $Grupo_de_solicitud->nro_de_grupo)->where('solicitud_id', $this->id)->get();
                $grupo_id = $Grupo_de_solicitud_search[0]->id;
                $Grupo_de_solicitud = Grupo_de_solicitud::find($grupo_id);
                $tel_responsable_inscripcion = $Grupo_de_solicitud->celular_responsable_de_inscripciones;
                $nombre_responsable_inscripcion = trim($Grupo_de_solicitud->nombre_responsable_de_inscripciones);
            }

        }

        $gCont = new GenericController();
        $url_whatsapp = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($tel_responsable_inscripcion, $codigo_tel);
        $Idioma_por_pais = $this->idioma_por_pais();
        $nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;

        $Modelo_de_mensaje_curso = $Idioma_por_pais->Modelo_de_mensaje->envio_de_leccion;


        $texto_lecciones_de_curso = [];

        $Modelo_de_mensaje_curso = Modelo_de_mensaje_curso::find($modelo_de_mensaje_curso_id);
        $url_sitio_web = $Idioma_por_pais->url_sitio_web;

        $modalidad_de_notificacion_de_asistencia_id = 1;
        if ($this->modalidad_de_notificacion_de_asistencia_id > 1) {
            $modalidad_de_notificacion_de_asistencia_id = $this->modalidad_de_notificacion_de_asistencia_id;
        } 

        $explicacion_modalidad_notificacion_de_asistencia = __('messages.explicacion_modalidad_notificacion_de_asistencia_'.$modalidad_de_notificacion_de_asistencia_id);

        $patrones = array();
        $patrones[0] = '/icon_4diamantes/';
        $patrones[1] = '/icon_check/';
        $patrones[2] = '/icon_solcito/';
        $patrones[3] = '/icon_flechita/';
        $patrones[4] = '/url_whatsapp/';
        $patrones[5] = '/tel_responsable_inscripcion/';
        $patrones[6] = '/nombre_de_la_institucion/';
        $patrones[7] = '/nombre_responsable_inscripcion/';
        $patrones[8] = '/url_sitio_web/';
        $patrones[9] = '/explicacion_modalidad_notificacion_de_asistencia/';
        $sustituciones = array();
        $sustituciones[0] = $icon_4diamantes;
        $sustituciones[1] = $icon_check;
        $sustituciones[2] = $icon_solcito;
        $sustituciones[3] = $icon_flechita;
        $sustituciones[4] = $url_whatsapp;
        $sustituciones[5] = $tel_responsable_inscripcion;
        $sustituciones[6] = $nombre_de_la_institucion;
        $sustituciones[7] = $nombre_responsable_inscripcion;
        $sustituciones[8] = $url_sitio_web;
        $sustituciones[9] = $explicacion_modalidad_notificacion_de_asistencia;

        $texto = preg_replace($patrones, $sustituciones, $Modelo_de_mensaje_curso->modelo_del_mensaje);
        $fxc = new FxC();
        $texto_codificado = $fxc->CodificarURL($texto);
        $url_texto_modelo_del_mensaje_del_curso = $url_whatsapp.'&text='.$texto_codificado;

        return $url_texto_modelo_del_mensaje_del_curso;
    }
    
    public function comoVas($Fechas_de_evento)
    {
        $mensajes = array();
        $alertas = array();

        //INSCRIPCIONES
        $select = 's.id, ';
        $select .= 'COUNT(DISTINCT i.id) cant_inscriptos, ';
        $select .= 'SUM(CASE WHEN i.fecha_de_evento_id IS NOT NULL THEN 1 ELSE 0 END) cant_inscriptos_eligio, ';
        $select .= "SUM(CASE WHEN i.sino_envio_pedido_de_confirmacion = 'SI' THEN 1 ELSE 0 END) cant_contactados, ";
        $select .= "SUM(CASE WHEN i.sino_confirmo = 'SI' THEN 1 ELSE 0 END) cant_confirmo, ";
        $select .= "SUM(CASE WHEN i.sino_envio_voucher = 'SI' THEN 1 ELSE 0 END) cant_voucher, ";
        $select .= "SUM(CASE WHEN i.sino_envio_motivacion = 'SI' THEN 1 ELSE 0 END) cant_motivacion, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio = 'SI' THEN 1 ELSE 0 END) cant_recordatorio,";
        $select .= "SUM(CASE WHEN i.sino_asistio = 'SI' THEN 1 ELSE 0 END) cant_asistio, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio_proxima_clase = 'SI' OR sino_envio_recordatorio_proxima_clase_a_no_asistente = 'SI' THEN 1 ELSE 0 END) cant_recordatorio_prox, ";
        $select .= "SUM(CASE WHEN i.sino_cancelo = 'SI' THEN 1 ELSE 0 END) cant_cancelo,";
        $select .= "1 campo_para_usar ";

        $Inscripciones = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        //->whereNotNull('s.fecha_de_solicitud')
        //->whereRaw('s.id NOT IN (6, 9)')
        ->where('s.id', $this->id)
        ->whereRaw("(i.sino_cancelo IS NULL or i.sino_cancelo = 'NO')")
        //->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        //->whereRaw($where_filtros)       
        ->groupBy('s.id') 
        ->get();

        //DB::enableQueryLog();
        //FECHAS
        $select = 'f.id, ';
        $select .= "DATEDIFF(fecha_de_inicio, NOW()) dif_fecha_de_inicio, ";
        $select .= 'COUNT(DISTINCT i.id) cant_inscriptos, ';
        $select .= "SUM(CASE WHEN i.sino_confirmo = 'SI' THEN 1 ELSE 0 END) cant_confirmo, ";
        $select .= "SUM(CASE WHEN i.sino_envio_voucher = 'SI' THEN 1 ELSE 0 END) cant_voucher, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio = 'SI' THEN 1 ELSE 0 END) cant_recordatorio, ";
        $select .= "SUM(CASE WHEN i.sino_asistio = 'SI' THEN 1 ELSE 0 END) cant_asistio, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio_proxima_clase = 'SI' OR sino_envio_recordatorio_proxima_clase_a_no_asistente = 'SI' THEN 1 ELSE 0 END) cant_recordatorio_prox, ";
        $select .= "SUM(CASE WHEN i.sino_cancelo = 'SI' THEN 1 ELSE 0 END) cant_cancelo,";
        $select .= "1 campo_para_usar ";

        $Inscripciones_fechas = DB::table('fechas_de_evento as f')
        ->select(DB::Raw($select))
        ->join('inscripciones as i', 'i.fecha_de_evento_id', '=', 'f.id')
        ->where('f.solicitud_id', $this->id)
        ->whereRaw("(i.sino_cancelo IS NULL or i.sino_cancelo = 'NO')")
        ->groupBy('f.id') 
        ->get();
        //dd(DB::getQueryLog());


        //dd($Inscripciones_fechas);

        if ($Inscripciones->count() > 0) {

            $Inscripciones = $Inscripciones[0];

            $cant_inscriptos = $Inscripciones->cant_inscriptos;
            $cant_inscriptos_eligio = $Inscripciones->cant_inscriptos_eligio;
            $cant_contactados = $Inscripciones->cant_contactados;
            $cant_confirmo = $Inscripciones->cant_confirmo;
            $cant_voucher = $Inscripciones->cant_voucher;
            $cant_motivacion = $Inscripciones->cant_motivacion;


            foreach ($Inscripciones_fechas as $Inscripciones_fecha) {
                
                $Fecha_de_evento = $Fechas_de_evento->find($Inscripciones_fecha->id);

                $dif_fecha_de_inicio = $Inscripciones_fecha->dif_fecha_de_inicio;
                $cant_inscriptos_f = $Inscripciones_fecha->cant_inscriptos;
                $cant_confirmo_f = $Inscripciones_fecha->cant_confirmo;
                $cant_recordatorio_f = $Inscripciones_fecha->cant_recordatorio;
                $cant_asistio_f = $Inscripciones_fecha->cant_asistio;
                $cant_recordatorio_prox_f = $Inscripciones_fecha->cant_recordatorio_prox;

                $resta_cant_recordatorio_f = $cant_confirmo_f - $cant_recordatorio_f;
                $resta_cant_recordatorio_prox_clase_f = $cant_confirmo_f - $cant_recordatorio_f;

                $alerta_recordatorios = '';
                $detalleFechas = $Fecha_de_evento->armarDetalleFechasDeEventos('select', false, null, null, null, false);
                $porc_asistencia_f = round($cant_asistio_f*100/$cant_inscriptos_f);

                if ($dif_fecha_de_inicio <= 0 and $cant_confirmo_f > $cant_recordatorio_f) {
                    $mensajes[] = '<strong>'.$resta_cant_recordatorio_f.' '.__('Envio de Recordatorio').' '.__('sin enviar').'</strong>. ('.$detalleFechas.') <p class="info_mensaje">'.__('No deben quedar recordatorios sin enviar, esto reduce mucho la cantidad de personas que asisten').'</p>';
                }

                if ($dif_fecha_de_inicio == 1) {
                    $alertas[] = __('Mañana en la mañana, deberias enviar el recordatorio para el grupo de').' '.$detalleFechas;
                }

                if ($dif_fecha_de_inicio == 0) {
                    $alertas[] = __('Hoy en la mañana, deberias enviar el recordatorio para el grupo de').' '.$detalleFechas;
                }

                if ($dif_fecha_de_inicio < 0 and $porc_asistencia_f < 50) {
                    $mensajes[] = '<strong>'.__("Asistencia Registrada").' '.$porc_asistencia_f.'%. '.'</strong> -> '.$detalleFechas.'<p class="info_mensaje">'.__('el porcentaje siempre debería estar por encima del 50% del total de inscriptos').'. '.__('Los porcentajes bajos de asistencia pueden deberse a distintos factores, enumeramos a los mas comunes para que en función de esto se analicen las campañas con rendimientos bajos de asistencia y se tomen las acciones necesarias para su correción. 1) No se enviaron los recordatorios, o se enviaron tarde. 2) No se registro la asistencia en el Sistema AC, es decir no se leyo el codigo QR del voucher ni tampoco se utilizó la lista de asistencias del sistema para registrar los asistentes. 3) Condiciones climáticas desfavorables el día del evento. 4) El evento se ha realizado en un lugar de dificil acceso o no apropiado para la asistencia masiva').'</p>';
                }

                if ($cant_confirmo_f > $cant_recordatorio_prox_f and $dif_fecha_de_inicio <= -7 ) {
                    $mensajes[] = '<strong>'.$resta_cant_recordatorio_prox_clase_f.' '.__('Envio de Recordatorio a Próxima clase').' '.__('sin enviar').'</strong>. ('.$detalleFechas.') <p class="info_mensaje">'.__('Es muy importante que a toda persona que ha confirmado su asistencia se le envie el mensaje recordatorio para las clases siguientes, la persona normalmente al no tener incorporada en su rutina el curso, no recuerda muchas veces que debe asistir a la próxima clase, por eso es muy importante que el responsable de inscripción envie a todas las personas confirmadas un recordatorio posterior al inicio de los cursos. Una posible solución a esto es revisar las campañas que han tenido bajo porcentaje de envio de recordatorio a la próxima clase, y solicitar al responsable de inscripción que no descuide esta acción en próximas inscripciones.').'</p>';
                }

                if ($cant_confirmo_f >= $Fecha_de_evento->cupo_maximo_disponible_del_salon*2) {
                    $mensajes[] = '<strong>'.__('Cupo excedido, cupo máximo').' '.$Fecha_de_evento->cupo_maximo_disponible_del_salon.', '.__('Confirmados').' '.$cant_confirmo_f.'</strong>. ('.$detalleFechas.')</p>';                    
                }


            }

            $cant_recordatorio = $Inscripciones->cant_recordatorio;
            $cant_asistio = $Inscripciones->cant_asistio;
            $cant_recordatorio_prox = $Inscripciones->cant_recordatorio_prox;
            $cant_cancelo = $Inscripciones->cant_cancelo;
            
            $porc_contactados = $cant_contactados * 100 / $cant_inscriptos;


            if ($this->tipo_de_evento_id <> 3 or $this->tipo_de_curso_online_id == 4) {
                if ($cant_inscriptos_eligio > 0) {
                    $porc_confirmo = $cant_confirmo * 100 / $cant_inscriptos_eligio;
                }
                else {
                    $porc_confirmo = 0;    
                }
                $resta_comtactar = $cant_inscriptos - $cant_contactados;
                $resta_voucher = $cant_confirmo - $cant_voucher;
                $resta_motivacion = $cant_confirmo - $cant_motivacion;
                
                if ($resta_comtactar > 0) {
                    $mensajes[] = '<strong>'.$resta_comtactar.' '.__('Inscriptos').' '.__('no contactados').'</strong>';
                }
                
                if ($porc_confirmo < 70) {
                    $mensajes[] = '<strong>'.round($porc_confirmo, 2).'% '.__('Confirmados').'</strong>'.'. <p class="info_mensaje">'.__('El porcentaje de confirmación esta por debajo del 70%, esto puede deberse a que no estas contactando con rapidez a las personas inscriptas, recuerda que la recomendación es hacerlo dentro de las 2 o 3 horas luego de la inscripción. Tu porcentaje actual de confirmación es de').'<strong> '.round($porc_confirmo, 2).'% '.'</strong></p>';
                }

                if ($cant_voucher < $cant_confirmo) {
                    $mensajes[] = '<strong>'.$resta_voucher.' '.__('Vouchers').' '.__('sin enviar').'</strong>';
                }

                //if ($cant_motivacion < $cant_confirmo and $dif_fecha_de_inicio <= 2) {
                /*
                if ($cant_motivacion < $cant_confirmo) {
                    $mensajes[] = '<strong>'.$resta_motivacion.' '.__('Motivacion').' '.__('sin enviar').'</strong>';
                }
                */
            }
            else {            
                $resta_contactar = $cant_inscriptos-($cant_contactados + $cant_cancelo);
                
                if ($resta_contactar > 0) {
                    $mensajes[] = '<strong>'.__('Resta contactar a').' '.$resta_contactar.'    ('.$cant_inscriptos.' '.__('Inscriptos').' | '.$cant_contactados.' '.__('contactados').' | '.$cant_cancelo.' '.__('Cancelados').')</strong>. <p class="info_mensaje">'.__('No deben quedar inscriptos (no cancelados) sin contactar').'</p>';
                }

            }

        }


        if (count($mensajes) == 0) {
            $mensajes[] = 'Hasta ahora vas muy bien';
        }

        //dd(count($mensajes));
        
        $resutados = [
                'mensajes' => $mensajes,
                'alertas' => $alertas
            ];
        
        //dd($resutados);

        return $resutados;
    }



    public function mensajeRedireccion()
    {
        $a_estado = $this->estado();
        $letra_estado = $a_estado['letra_estado'];
        $mensaje_redireccion = '';

        if ($this->institucion_id == 1) {
            if ($this->idioma_id <> '') {
                $idioma = $this->idioma->mnemo;           
                $locale_vee_validate = $this->idioma->locale_vee_validate;             
                App::setLocale($idioma);  
            }
            else {
                $idioma_por_pais = $this->idioma_por_pais();
                if ($idioma_por_pais->idioma_id <> '') {
                    $idioma = $idioma_por_pais->idioma->mnemo;    
                    $locale_vee_validate = $idioma_por_pais->idioma->locale_vee_validate;                    
                    App::setLocale($idioma);  
                }
            }


            if ($letra_estado <> 'a') {
                $mensaje_redireccion = __('Este formulario se encuentra desactivado para inscripciones, para poder inscribirse a nuestros cursos activos diríjase a').':<br><br><strong><a href="'.$this->idioma->url_form_curso_online.'">'.$this->idioma->url_form_curso_online.'</strong></a>';
            }
            else {
                $fecha_hoy = new DateTime("now");

                $fecha_de_solicitud = new DateTime(date('Y-m-d', strtotime($this->fecha_de_solicitud)));
                $interval_1 = date_diff($fecha_hoy, $fecha_de_solicitud);
                $cant_dias_fecha_de_solicitud = $interval_1->format('%a');

                if ($this->ultimo_acceso_planilla_inscripcion <> '') {
                    $ultimo_acceso_planilla_inscripcion = new DateTime(date('Y-m-d', strtotime($this->ultimo_acceso_planilla_inscripcion)));
                    $interval_2 = date_diff($fecha_hoy, $ultimo_acceso_planilla_inscripcion);   
                    $cant_dias_ultimo_acceso_planilla_inscripcion = $interval_2->format('%a');
                }
                else {
                    $cant_dias_ultimo_acceso_planilla_inscripcion = 9999999;
                }           

                if ($cant_dias_fecha_de_solicitud > 60 and $cant_dias_ultimo_acceso_planilla_inscripcion > 15) {
                    $mensaje_redireccion = __('Este formulario no esta recibiendo atención por parte de nuestro personal, para poder inscribirse a nuestros cursos activos diríjase a').':<br><br><strong><a href="'.$this->idioma->url_form_curso_online.'">'.$this->idioma->url_form_curso_online.'</strong></a>';
                }

            }
        }


        return $mensaje_redireccion;
    }


    public function emailsMauticCampaign()
    {
        $Campaign = Campaign::find($this->campania_mautic_id);

        $Campaign_leads = DB::connection('mautic')
        ->table('campaign_leads as cl')
        ->join('leads as l', 'l.id', '=', 'cl.lead_id')
        ->select(DB::Raw('cl.lead_id, l.firstname, l.lastname, l.email'))
        ->where('cl.campaign_id', $this->campania_mautic_id)
        ->get();

        $Email_stats = DB::connection('mautic')
        ->table('email_stats')
        ->select(DB::Raw('COUNT(id) as enviados, SUM(is_read) as leidos'))
        ->where('email_id', $this->mautic_email_id)
        ->get();        

        $cant_inscriptos = Inscripcion::where('campania_id', 244)->where('solicitud_id', $this->id)->count();        

        if ($Email_stats[0]->enviados > 0) {
          $modificar = 'NO';
        }
        else {
          $modificar = 'SI';
        }

        $emailsMauticCampaign = [
            'Campaign' => $Campaign,
            'Campaign_leads' => $Campaign_leads,
            'Email_stats' => $Email_stats[0],
            'cant_inscriptos' => $cant_inscriptos,
            'modificar' => $modificar
        ];


        return $emailsMauticCampaign;
    }
    

    public function idioma()
    {
        return $this->belongsTo('App\Idioma');
    }
    
    public function pais()
    {
        return $this->belongsTo('App\Pais');
    }
    
    public function moneda()
    {
        return $this->belongsTo('App\Moneda');
    }
    
    public function localidad()
    {
        return $this->belongsTo('App\Localidad');
    }
    
    public function tipo_de_campania_facebook()
    {
        return $this->belongsTo('App\Tipo_de_campania_facebook');
    }
    

    public function fechas_de_evento()
    {
        return $this->hasMany('App\Fecha_de_evento');
    }
    

    public function tipo_de_curso_online()
    {
        return $this->belongsTo('App\Tipo_de_curso_online');
    }

    public function curso()
    {
        return $this->belongsTo('App\Curso');
    }

    public function institucion()
    {
        return $this->belongsTo('App\Institucion');
    }

    public function canal_de_recepcion_del_curso()
    {
        return $this->belongsTo('App\Canal_de_recepcion_del_curso');
    }

    protected $table = 'solicitudes';
}
