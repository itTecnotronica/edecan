<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\Fecha_de_evento;
use App\Inscripcion;
use App\Localidad;
use App\Asistencia;
use App\Registro_de_error;
use App\Pais;
use App\Envio;
use App\Evento_en_sitio;
use App\Contacto;
use App\Modelo_de_mensaje_curso;
use App\Visualizacion_de_formulario;
use App\Formulario;
use App\Leccion;
use App\Causa_de_baja;
use App\Canal_de_recepcion_del_curso;
use App\Grupo_de_solicitud;
use App\Evaluaciones;
use App\Cambio_de_solicitud_de_inscripcion;
use App\Causa_de_cambio_de_solicitud;
use App\Evaluacion;
use App\Curso;
use App\Alumno_avanzado;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GenericController;
use App\Http\Controllers\MauticController;
use App\Http\Controllers\ListasController;
use App\Http\Controllers\FxC; 
use Auth;
use Session;
use PDF;
use QrCode;
use URL;
use Storage;
use Filesystem;
use App\Http\Controllers\Mautic\MauticApi;
use App\Http\Controllers\Mautic\Auth\AuthInterface;
use App\Http\Controllers\Mautic\Auth\ApiAuth;
//use App\Libraries\mauticApi\lib\MauticApi;
//use Alkoumi\LaravelHijriDate\Hijri;

//use Mautic\Auth\ApiAuth;

//use App\Notifications\InvoicePaid;
use App\Notifications\TelegramNotification;
use App;
use setasign\FpdiProtection\FpdiProtection;

class FormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    private $cant_x_pagina = 100;

    public function formShow($solicitud_id, $hash, $campania_id = null, $app_usuario_id = null)
    {  
        $sesion_id = Session()->get('sesion_id');

        $now = new \DateTime();
        $fecha_now = $now->format('Y-m-d H:i:s');
        $url_anterior = substr(URL::previous(), 0, 199);

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();

        $modo = '';
        $array_hash = explode('!', $hash);

        if (count($array_hash) > 1) {
            if ($array_hash[1] == 'embebed') {
                $modo = 'embebed';
            }
            $hash = $array_hash[0];
        }

        if ($Solicitud <> null) {

            $mensaje_redireccion = $Solicitud->mensajeRedireccion();

            $cel_requerido = true;
            $mail_requerido = true;

            $acepto_politica_de_privacidad = false;
            $politica_de_privacidad = '';



            if ($Solicitud->idioma_por_pais() <> null) {
                $idioma_por_pais = $Solicitud->idioma_por_pais();    
                if ($idioma_por_pais->sino_cel_obligatorio == 'NO' and $Solicitud->canal_de_recepcion_del_curso_id <> 3) {
                    $cel_requerido = false;
                }
                if ($idioma_por_pais->sino_mail_obligatorio == 'NO') {
                    $mail_requerido = false;
                }
                else {
                    $mail_requerido = true;
                }


                if ($idioma_por_pais->sino_politicas_de_privacidad == 'SI' and trim($idioma_por_pais->texto_politicas_de_privacidad) <> '') {
                    $acepto_politica_de_privacidad = true;
                    $politica_de_privacidad = $idioma_por_pais->texto_politicas_de_privacidad;
                }

                if ($idioma_por_pais->idioma_id <> '') {
                    $idioma = $idioma_por_pais->idioma->mnemo;                        
                    App::setLocale($idioma);  
                }                
            }

            if (in_array($Solicitud->canal_de_recepcion_del_curso_id, [1,2,3,4,6,7,9,11])) {
                $cel_requerido = true;
            }

            if (in_array($Solicitud->canal_de_recepcion_del_curso_id, [5])) {
                $mail_requerido = true;
            }

            if ($cel_requerido) {
                $cel_requerido_class = 'required';
                $cel_requerido_v_validate = "'required'";
                $cel_requerido_input = 'required="required"';                
            }
            else {
                $cel_requerido_class = '';
                $cel_requerido_v_validate = '';
                $cel_requerido_input = '';
            }

            if ($mail_requerido) {
                $mail_requerido_class = 'required';
                $mail_requerido_input = 'required="required"';          
            }
            else {
                $mail_requerido_class = '';
                $mail_requerido_input = '';     
            }


            if ($Solicitud->idioma_id <> '') {
                $idioma = $Solicitud->idioma->mnemo;                        
                App::setLocale($idioma);  
            }

            if ($Solicitud->tipo_de_evento->id == 1 or ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->tipo_de_curso_online_id == 4) or $Solicitud->tipo_de_evento->id == 4) { 
                if ($Solicitud->cant() == 1 or $Solicitud->tipo_de_evento->id == 4) {
                    $mensaje_fecha_de_evento = __('Seleccione una opción');
                }
                else {                
                    $mensaje_fecha_de_evento = __('Seleccione uno de los horarios disponibles');
                }
            }
            else {
                if ($Solicitud->cant() == 1) {
                    $mensaje_fecha_de_evento = __('Seleccione una opción');

                }
                else {
                    $mensaje_fecha_de_evento = __('Seleccione alguna de las conferencias disponibles');                
                }
                
            }

            // DETERMINO EL TITULO
            $titulo = $Solicitud->descripcion_sin_estado();

            // DETERMINO EL SUB TITULO
            $subtitulo = '';
            if ($Solicitud->tipo_de_evento->id <> 4) {
                if ($Solicitud->subtitulo_del_formulario_personalizado == '') {
                    $subtitulo = __('ENTRADA GRATUITA - CUPOS LIMITADOS');
                }
                else {
                    $subtitulo = $Solicitud->subtitulo_del_formulario_personalizado;
                }
            }

            // DETERMINO EL TITULO DE FECHA DE INICIO
            $titulo_fecha_inicio = '';
            $Fecha_de_evento_new = new Fecha_de_evento();  

            if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->fecha_de_inicio_del_curso_online <> '') {
                $gCon = new GenericController();
                $fcx = new FxC();

                $fecha_inicio_format = $fcx->convertirFechaATexto($Solicitud->fecha_de_inicio_del_curso_online, '', 'S', $idioma);
                if ($Solicitud->tipo_de_curso_online_id == 3) {
                    $fecha_inicio_format .= ' '.$Fecha_de_evento_new->FormatoHora($Solicitud->hora_de_inicio_del_curso_online, $Solicitud->idioma_por_pais(), $Solicitud->idioma_por_pais()->idioma->mnemo);
                }

                //$fecha_inicio_format .= ' | '.Hijri::Date('l ، j F ، Y');

                $titulo_fecha_inicio = '<center><h3>'.__('Fecha de inicio').': '.$fecha_inicio_format.'</h3></center>';
            }

            

            // DETERMINO LA IMAGEN
            $imagen = '';
            if ($Solicitud->videoyt_video_youtube <> '') {
                $imagen = '<center><div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" width="100%" height="100%" src="https://www.youtube.com/embed/'.$Solicitud->videoyt_video_youtube.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div></center>';
                }
                else {

                    if ($Solicitud->file_imagen_del_formulario_personalizada == '') {
                        if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 3) {
                            if ($idioma_por_pais->pais_id <> 1) {
                                $imagen = '<img class="img-ancho-total" src="'.env('PATH_PUBLIC').'/templates/2/img/flamarion_original_rec.jpg">';
                            }
                            else {
                                $imagen = '<img class="img-ancho-total" src="'.env('PATH_PUBLIC').'/templates/2/img/noscete-ipsum.jpg">';
                            }

                        }
                    }
                    else {
                        $imagen = '<img class="img-ancho-total" src="'.env('PATH_PUBLIC').'storage/'.$Solicitud->file_imagen_del_formulario_personalizada.'">';
                    }
                }

            //DETERMINO EL VIDEO SI HA SIDO DEFINIDO
            $video_youtube = '';
            if ($Solicitud->videoyt_video_youtube <> '') {
                $video_youtube = '<br><br><center><iframe width="560" height="315" src="https://www.youtube.com/embed/'.$Solicitud->videoyt_video_youtube.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></center>';
            }            

            // DETERMINO EL RESUMEN
            $resumen = '';
            if ($Solicitud->rtf_resumen_del_formulario_personalizado == '') {
                if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 3) {
                    $resumen = '<h4>'.__('Algunos temas a desarrollar').':</h4>';
                    $resumen .= '<center><p>'.__('messages.algunos_temas').'<br><span class="subtitulo-cursos">'.__('CURSO LIBRE Y GRATUITO').'</span></p></center>';
                    /*
                    if ($Solicitud->tipo_de_evento_id == 1 and $idioma_por_pais->pais_id == 14) {
                        $mensaje_detalle = '+ El uso de mascarillas será obligatorio para todos los asistentes, por lo que se ruega a todos los usuarios que asistan con la suya.<br>';
                        $mensaje_detalle .= '+ Gel desinfectante, disponible en todo momento.<br>';
                        $mensaje_detalle .= '+ Distancia de seguridad.<br>';
                        $resumen .= '<div class="row"><div class="col-xs-12"><br><div class="alert alert-warning alert-dismissible">';
                        $resumen .= '<h4><i class="icon fa fa-check"></i> Durante las Conferencias se asegurar&aacute;</strong> de que todos los asistentes hacen uso de los equipos de protección personal:</h4>';
                        $resumen .= $mensaje_detalle;
                        $resumen .= '</div></div></div>';
                    }
                    */
                }
            }
            else {
                $resumen = '<p style="text-align: justify"><br>'.$Solicitud->rtf_resumen_del_formulario_personalizado.'</p>';
            }

            // DETERMINO EL TEXTO
            $texto = '';
            if ($Solicitud->rtf_texto_del_formulario_personalizado <> '') {
                $texto = $Solicitud->rtf_texto_del_formulario_personalizado;
            }

            $array_estado = $Solicitud->estado();
            $letra_estado = $array_estado['letra_estado'];
            $span_estado = $array_estado['span_estado'];

            $titulo_estado = '';
            $deshabilitar_formulario = "false";

            if ($letra_estado <> 'a') {
                $deshabilitar_formulario = 'true';
                $titulo_estado = __('Estado').": $span_estado";  
                $titulo .= ' - '.$titulo_estado;
            }

            if ($modo == 'embebed') {
                $blade_de_formulario = 'template_embebido';
                $formulario_id = null;
            }
            else {
                $Formularios = Formulario::where('sino_habilitado', 'SI')->get();
                $cant_forms = Formulario::where('sino_habilitado', 'SI')->count();
                
                $formulario_nro = rand(0, $cant_forms-1);

                $formulario_id = $Formularios[$formulario_nro]->id;
                $blade_de_formulario = $Formularios[$formulario_nro]->blade_de_formulario;
            }


            try { 
                $Visualizacion_de_formulario = new Visualizacion_de_formulario;
                $Visualizacion_de_formulario->sesion_id = $sesion_id;
                $Visualizacion_de_formulario->solicitud_id = $solicitud_id;
                $Visualizacion_de_formulario->formulario_id = $formulario_id;
                $Visualizacion_de_formulario->fecha_y_hora_visualizacion = $fecha_now;
                $Visualizacion_de_formulario->url_anterior = $url_anterior;
                $Visualizacion_de_formulario->save(); 
            } catch(\Illuminate\Database\QueryException $ex){ 
                $detalle_de_origen = 'Visualizacion de Formulario de Inscripcion Sesion_id: '.$sesion_id.' url previa: '.URL::previous();
                $Registro_de_error = new Registro_de_error;
                $Registro_de_error->registro_de_error = $ex->getMessage();
                $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                $Registro_de_error->save();       
            }

            if ($Solicitud->hash == $hash or $Solicitud->hash_anterior == $hash or ($Solicitud->hash ==  '' and $hash == '')) {    


                $url_redes = $this->urlRedesEspeciales($solicitud_id);

                if (isset($idioma_por_pais) and count($url_redes) == 0) {
                    $url_redes = [
                        'url_fanpage' => $idioma_por_pais->url_fanpage,
                        'url_sitio_web' => $idioma_por_pais->url_sitio_web,
                        'url_youtube' => $idioma_por_pais->url_youtube,
                        'url_twitter' => $idioma_por_pais->url_twitter,
                        'url_instagram' => $idioma_por_pais->url_instagram,
                        'url_tiktok' => $idioma_por_pais->url_tiktok
                    ];
                }

                $Fechas_de_eventos = Fecha_de_evento::where('solicitud_id', $solicitud_id)->orderBy('sino_agotado', 'asc')->get();  


                $style_body = '';
                if ($Solicitud->colpick_color_de_fondo_del_formulario <> '') { 
                    $style_body = 'style="background: '.$Solicitud->colpick_color_de_fondo_del_formulario.' !important"';
                }

                $ciudad = 'null';
                if ($Solicitud->localidad_id <> '') {
                    $ciudad = "'".$Solicitud->localidad->localidad."'";
                }

                if ($Solicitud->institucion_id == 2) {
                    $imagen_top = env('PATH_PUBLIC').'img/logo-asoprovida-form.png';
                    $imagen_chica = env('PATH_PUBLIC').'img/logo-asoprovida-chico.jpg';
                    $css_template = env('PATH_PUBLIC').'templates/2/css/main-asoprovida.css';
                    $bgform = 'bg-asoprovida';
                }
                else  { 
                    $idioma_img_gnosis = 'es';
                    //dd($idioma_por_pais);
                    if ($idioma_por_pais <> null) {
                        $array_mnemo = explode('-', $idioma_por_pais->idioma->mnemo);
                        if (count($array_mnemo) > 0) {
                            $idioma_img_gnosis = $array_mnemo[0];
                        }
                    }
                    $imagen_top_interno = env('PATH_PUBLIC_INTERNO').'img/gnosis-'.$idioma_img_gnosis.'.png';
                    if (file_exists($imagen_top_interno)) {
                        $imagen_top = env('PATH_PUBLIC').'img/gnosis-'.$idioma_img_gnosis.'.png';
                    }
                    else {
                        $imagen_top = env('PATH_PUBLIC').'img/gnosis.png';
                    }
                    $imagen_chica = env('PATH_PUBLIC').'img/sol-de-acuario-chico.jpg';
                    $css_template = env('PATH_PUBLIC').'templates/2/css/main.css';
                    $bgform = 'bg-gra-02';
                }

                $nombre_institucion = $Solicitud->institucion->institucion;

                if ($Solicitud->id == 6227) {
                  $bgform = 'bg-gra-02-papiro';  
                }    
                
                $Paises = Pais::all();



                return View('forms/'.$blade_de_formulario)        
                ->with('Fechas_de_eventos', $Fechas_de_eventos)
                ->with('titulo', $titulo)
                ->with('titulo_fecha_inicio', $titulo_fecha_inicio)
                ->with('subtitulo', $subtitulo)
                ->with('imagen', $imagen)              
                ->with('resumen', $resumen)
                ->with('texto', $texto)
                ->with('mensaje_fecha_de_evento', $mensaje_fecha_de_evento)
                ->with('deshabilitar_formulario', $deshabilitar_formulario) 
                ->with('cel_requerido_class', $cel_requerido_class)     
                ->with('cel_requerido_v_validate', $cel_requerido_v_validate)     
                ->with('cel_requerido_input', $cel_requerido_input) 
                ->with('mail_requerido_class', $mail_requerido_class)        
                ->with('mail_requerido_input', $mail_requerido_input)      
                ->with('acepto_politica_de_privacidad', $acepto_politica_de_privacidad)      
                ->with('politica_de_privacidad', $politica_de_privacidad)
                ->with('Solicitud', $Solicitud)
                ->with('url_redes', $url_redes)
                ->with('style_body', $style_body)
                ->with('campania_id', $campania_id)
                ->with('app_usuario_id', $app_usuario_id)
                ->with('ciudad', $ciudad)
                ->with('imagen_top', $imagen_top)
                ->with('imagen_chica', $imagen_chica)
                ->with('bgform', $bgform)
                ->with('nombre_institucion', $nombre_institucion)
                ->with('dominio_publico', $Solicitud->dominioPublico())
                ->with('Paises', $Paises)
                ->with('mensaje_redireccion', $mensaje_redireccion);
            }
            else {
                echo 'ERROR! Esta url no es válida';
            }            
        }
        else {

            try { 
                $Visualizacion_de_formulario = new Visualizacion_de_formulario;
                $Visualizacion_de_formulario->sesion_id = $sesion_id;
                $Visualizacion_de_formulario->solicitud_id = NULL;
                $Visualizacion_de_formulario->formulario_id = NULL;
                $Visualizacion_de_formulario->fecha_y_hora_visualizacion = $fecha_now;
                $Visualizacion_de_formulario->url_anterior = $url_anterior;
                $Visualizacion_de_formulario->save(); 

                /*
                $detalle_de_origen = 'ERROR en Visualizacion de Formulario de Inscripcion Sesion_id: '.$sesion_id.'  solicitud_id: '.$solicitud_id.' url previa: '.URL::previous();
                $Registro_de_error = new Registro_de_error;
                $Registro_de_error->registro_de_error = $ex->getMessage();
                $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                $Registro_de_error->save();
                */

            } catch(\Illuminate\Database\QueryException $ex){ 
                $detalle_de_origen = 'ERROR de Grabacion en Visualizacion de Formulario de Inscripcion Sesion_id: '.$sesion_id.' url previa: '.URL::previous();
                $Registro_de_error = new Registro_de_error;
                $Registro_de_error->registro_de_error = $ex->getMessage();
                $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                $Registro_de_error->save();       
            }

            echo 'ERROR Formulario no encontrado';
      
        }
        
    }
    
    public function limpiarCadena($cadena) {
        $cadena_limpia = trim($cadena);
        $cadena_limpia = str_replace("'", "’", $cadena);
        $cadena_limpia = str_replace('"', "", $cadena_limpia);
        return $cadena_limpia;
        }

    public function RegistrarInscripcion(Request $request) {



        $error_inscripcion = false;
        $solicitud_id = $_POST['solicitud_id'];
        $Solicitud = Solicitud::find($solicitud_id);
            
        $cel_requerido = 'required|';
        $mail_requerido = '';
        $canal_de_recepcion_del_curso_id_requerido = '';
        $pais_id_requerido = '';
        $ciudad_requerido = '';
        $localidad_id_requerido = '';
        $mensaje_box_fecha_de_evento = '';
    
        if ($Solicitud->idioma_por_pais() <> null) {
            $idioma_por_pais = $Solicitud->idioma_por_pais(); 
            if ($idioma_por_pais->sino_cel_obligatorio == 'NO') {
                $cel_requerido = '';
            }
            if ($idioma_por_pais->sino_mail_obligatorio == 'NO') {
                $mail_requerido = '';
            }
            else {
                $mail_requerido = 'required|';
            }

            $institucion_id = $idioma_por_pais->institucion_id;
        }
        else {
            $institucion_id = 1;
        }

        if ($Solicitud->tipo_de_evento_id == 3) { 
            $pais_id_requerido = 'required|';
            $ciudad_requerido = 'required|';
        }

        if (($Solicitud->sino_habilitar_pedido_de_canal_de_recepcion_del_curso == 'SI' and $Solicitud->tipo_de_evento_id == 3) or $Solicitud->id == 6 ) {
            $canal_de_recepcion_del_curso_id_requerido = 'required|';
        }

        if ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id <> '' and in_array($Solicitud->id, array(3747, 4033, 4034, 4035, 4036, 4037)) ) {
            $localidad_id_requerido = 'required|';
        }

        $this->validate($request, [
            'nombre' => 'required|max:45',
            'apellido' => 'required|max:45',
            'celular' => $cel_requerido.'max:45',
            'email_correo' => $mail_requerido.'max:80',
            'canal_de_recepcion_del_curso_id' => $canal_de_recepcion_del_curso_id_requerido,
            'pais_id' => $pais_id_requerido,
            'ciudad' => $ciudad_requerido.'max:50',
            'localidad_id' => $localidad_id_requerido,
            'consulta' => 'max:300'
        ]);




        $campania_id = null;
        $app_usuario_id = null;
        if (isset($_POST['campania_id'])) {
            $campania_id = $_POST['campania_id'];
            if ($campania_id == '' or $campania_id == 0) {
                $campania_id = null;

                if (isset($_POST['app_usuario_id'])) {
                    if ($_POST['app_usuario_id'] > 0) {
                        $app_usuario_id = $_POST['app_usuario_id'];
                    }
                }

            }
        }

        $embebed = '';
        if (isset($_POST['embebed'])) {
            $embebed = $_POST['embebed'];
        }

        $url_invitacion_grupo_facebook = '';

        $idioma = $Solicitud->idioma->mnemo;
        App::setLocale($idioma);    
               
        $apellido = $this->limpiarCadena($_POST['apellido']);
        $nombre = $this->limpiarCadena($_POST['nombre']);
        //$nombre = '444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444';
        $celular = $this->limpiarCadena($_POST['celular']);
        if (isset($_POST['celular_completo'])) {
            $celular_completo = $this->limpiarCadena($_POST['celular_completo']);
            if ($celular_completo <> '') {
                $celular = $celular_completo;
            }
        }
        
        $canal_de_recepcion_del_curso_id = null;
        if (isset($_POST['canal_de_recepcion_del_curso_id'])) {
            $canal_de_recepcion_del_curso_id = $this->limpiarCadena($_POST['canal_de_recepcion_del_curso_id']);
        }
        $email_correo = $this->limpiarCadena($_POST['email_correo']);
        $email_correo = str_replace(' ', '', $email_correo);
        if ($Solicitud->tipo_de_evento_id == 4) {
            $consulta = '';
        }
        else {
            $consulta = $this->limpiarCadena($_POST['consulta']);
        }

        if (isset($_POST['sino_notificar_proximos_eventos'])) {
            $notificar_proximos_eventos = 'SI';
        }
        else {
            $notificar_proximos_eventos = 'NO';
        }

        $pais = '';
        $ciudad = '';

        if (isset($_POST['fecha_de_evento_id'])) {
            $fecha_de_evento_id = $_POST['fecha_de_evento_id'];
        } 
        else {
            $fecha_de_evento_id = '';
        }       



        $se_registro_alguna_inscripcion = 'N';
        $inscripcion_ya_registrada = 'N';
        $themeofinterest = '';
        $eventid = '';
        $date_of_interest = '';
        $tags_mautic = ['id'.$solicitud_id];

        // cuando es un curso on-line o Recolección de Datos
        if (($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id <> 4) or $Solicitud->tipo_de_evento_id == 4) {

            if ($Solicitud->tipo_de_evento_id == 3 or ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id == '') or ($Solicitud->tipo_de_evento_id == 4 and  !in_array($Solicitud->id, array(3747, 4033, 4034, 4035, 4036, 4037)) ) ) {
                $pais_id = $this->limpiarCadena($_POST['pais_id']);
                $ciudad = $this->limpiarCadena($_POST['ciudad']);
            }

            if ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id <> '' and in_array($Solicitud->id, array(3747, 4033, 4034, 4035, 4036, 4037))) {
                $pais_id = $Solicitud->pais_id;
                $localidad_id = $this->limpiarCadena($_POST['localidad_id']);
                $Localidad = Localidad::find($localidad_id);
                $ciudad = $Localidad->localidad; 
            }

            $themeofinterest .= $Solicitud->Tipo_de_evento->tipo_de_evento;
            array_push($tags_mautic, $themeofinterest);    

            if ($Solicitud->tipo_de_evento_id == 6192)
                $whereLimiteDiasAnt = '(DATEDIFF(NOW(), created_at) BETWEEN -9999 and 9999)';
            else {
                $whereLimiteDiasAnt = '(DATEDIFF(NOW(), created_at) BETWEEN -7 and 7)';
            }

            if ($fecha_de_evento_id <> '') {
                $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                    ->where('nombre', $nombre)
                    ->where('celular', $celular)
                    ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                    ->where('fecha_de_evento_id', $fecha_de_evento_id)
                    ->get();
            }
            else  {
                $Inscripcion_previa = Inscripcion::where('solicitud_id', $solicitud_id)
                    //->where('nombre', $nombre)
                    //->where('apellido', $apellido)
                    //->where('celular', $celular)
                    //->where('pais_id', $pais_id)
                    //->where('ciudad', $ciudad)
                    ->whereRaw("((email_correo = '$email_correo' AND email_correo IS NOT NULL AND email_correo <> '') or (celular = '$celular'))")
                    ->whereRaw($whereLimiteDiasAnt)
                    ->get();
            }
                        
            if ($Inscripcion_previa->count() == 0) {

                $se_registro_alguna_inscripcion = 'S';

                $Inscripcion = new Inscripcion;
                $Inscripcion->solicitud_id = $solicitud_id;
                $Inscripcion->apellido = $apellido;
                $Inscripcion->nombre = $nombre;
                if ($celular <> '') {
                    $Inscripcion->celular = $celular;
                }
                if ($email_correo <> '') {
                    $Inscripcion->email_correo = $email_correo;
                }
                $Inscripcion->consulta = $consulta;
                $Inscripcion->pais_id = $pais_id;
                $Inscripcion->ciudad = $ciudad;
                $Inscripcion->campania_id = $campania_id;
                $Inscripcion->app_usuario_id = $app_usuario_id;
                $Inscripcion->canal_de_recepcion_del_curso_id = $canal_de_recepcion_del_curso_id;
                $Inscripcion->codigo_alumno = $Inscripcion->codigo_del_alumno();

                if ($fecha_de_evento_id <> '') {
                    $Inscripcion->fecha_de_evento_id = $fecha_de_evento_id;
                }

                if (isset($_POST['sino_notificar_proximos_eventos'])) {
                    $Inscripcion->sino_notificar_proximos_eventos = 'SI';
                }
                if (isset($_POST['acepto_politica_de_privacidad'])) {
                    $Inscripcion->sino_acepto_politica_de_privacidad = 'SI';
                }

                try { 
                    $Inscripcion->save(); 
                    $inscripcion_id = $Inscripcion->id;
                } catch(\Illuminate\Database\QueryException $ex){ 
                    $detalle_de_origen = 'Registracion de Inscripcion (curso on-line): '.URL::previous();
                    $Registro_de_error = new Registro_de_error;
                    $Registro_de_error->registro_de_error = $ex->getMessage();
                    $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                    $Registro_de_error->save();              
                    $error_inscripcion = true;
                }

            }            
            else {                        
                $inscripcion_ya_registrada = 'S';
                $inscripcion_id = $Inscripcion_previa[0]->id;
            }


        }



        // cuando es un curso o una sola conferencia
        if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1) or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4) ) {

            $inscripcion_id = NULL;

            if ($Solicitud->tipo_de_evento_id == 3) {
                $pais_id = $this->limpiarCadena($_POST['pais_id']);
                $ciudad = $this->limpiarCadena($_POST['ciudad']);
            }

            if ($Solicitud->Tipo_de_evento->id == 2) {
                $Fecha_de_evento = Fecha_de_evento::where('solicitud_id', $Solicitud->id)->get();
                $themeofinterest .= $Fecha_de_evento[0]->titulo_de_conferencia_publica;
                array_push($tags_mautic, $themeofinterest);
            }
            else {
                $themeofinterest .= $Solicitud->Tipo_de_evento->tipo_de_evento;
                array_push($tags_mautic, $themeofinterest);
            }
            

            if ($fecha_de_evento_id <> 'NP' and $fecha_de_evento_id <> '') {
                $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                    ->where('nombre', $nombre)
                    ->where('celular', $celular)
                    ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                    ->where('fecha_de_evento_id', $fecha_de_evento_id)
                    ->get();   

                
                $cant_Fecha_de_evento = Fecha_de_evento::where('id', $fecha_de_evento_id)->count();
                if ($cant_Fecha_de_evento > 0){
                    $Fecha_de_evento = Fecha_de_evento::find($fecha_de_evento_id);
                    $date_of_interest = $Fecha_de_evento->fecha_de_inicio; 
                }
            }
            else  {
                $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                    ->where('nombre', $nombre)
                    ->where('celular', $celular)
                    ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                    ->where('solicitud_id', $solicitud_id)
                    ->whereNull('fecha_de_evento_id')
                    ->get();

                array_push($tags_mautic, 'No Puede asistir');
            }
            
            if ($Inscripcion_previa->count() == 0) {

                $se_registro_alguna_inscripcion = 'S';

                $Inscripcion = new Inscripcion;
                $Inscripcion->solicitud_id = $solicitud_id;
                $Inscripcion->apellido = $apellido;
                $Inscripcion->nombre = $nombre;
                if ($celular <> '') {
                    $Inscripcion->celular = $celular;
                }
                if ($email_correo <> '') {
                    $Inscripcion->email_correo = $email_correo;
                }
                $Inscripcion->consulta = $consulta;

                if ($fecha_de_evento_id <> 'NP' and $fecha_de_evento_id <> 'MO') {
                    $Inscripcion->fecha_de_evento_id = $fecha_de_evento_id;
                }


                if (isset($_POST['sino_notificar_proximos_eventos'])) {
                    $Inscripcion->sino_notificar_proximos_eventos = 'SI';
                }
                if (isset($_POST['acepto_politica_de_privacidad'])) {
                    $Inscripcion->sino_acepto_politica_de_privacidad = 'SI';
                }
                $Inscripcion->campania_id = $campania_id;
                $Inscripcion->app_usuario_id = $app_usuario_id;
                $Inscripcion->canal_de_recepcion_del_curso_id = $canal_de_recepcion_del_curso_id;                
                $Inscripcion->codigo_alumno = $Inscripcion->codigo_del_alumno();
                
                if ($Solicitud->tipo_de_evento_id == 3) {
                    $Inscripcion->pais_id = $pais_id;
                    $Inscripcion->ciudad = $ciudad;
                }


                try { 
                    $Inscripcion->save(); 
                    $inscripcion_id = $Inscripcion->id;


                    if ($fecha_de_evento_id == 'MO') {
                        if ($Solicitud->derivar_inscriptos_modalidad_online_a_solicitud > 0) {
                            $Inscripcion->solicitud_id = $Solicitud->derivar_inscriptos_modalidad_online_a_solicitud;

                            $causa_de_cambio_de_solicitud_id = 5;

                            $Cambio = new Cambio_de_solicitud_de_inscripcion;
                            $Cambio->inscripcion_id = $Inscripcion->id;
                            $Cambio->causa_de_cambio_de_solicitud_id = $causa_de_cambio_de_solicitud_id;            
                            $Cambio->solicitud_origen = $solicitud_id;
                            $Cambio->solicitud_destino = $Solicitud->derivar_inscriptos_modalidad_online_a_solicitud;
                            $Cambio->save(); 

                            if ($Inscripcion->solicitud_original == '') {
                                $Inscripcion->solicitud_original = $solicitud_id;
                                $Inscripcion->causa_de_cambio_de_solicitud_id = $causa_de_cambio_de_solicitud_id;
                            }
                            $Inscripcion->solicitud_id = $Solicitud->derivar_inscriptos_modalidad_online_a_solicitud;
                            $Inscripcion->fecha_de_evento_id = NULL;
                            
                        }
                        $Inscripcion->sino_eleccion_modalidad_online = 'SI';
                        $Inscripcion->save();
                    }

                } catch(\Illuminate\Database\QueryException $ex){ 
                    $detalle_de_origen = 'Registracion de Inscripcion (cuando es un curso o una sola conferencia): '.URL::previous();
                    $Registro_de_error = new Registro_de_error;
                    $Registro_de_error->registro_de_error = $ex->getMessage();
                    $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                    $Registro_de_error->save();              
                    $error_inscripcion = true;
                }
                
                
                }
            else {                        
                $inscripcion_ya_registrada = 'S';
                $inscripcion_id = $Inscripcion_previa[0]->id;
            }
        }
        else {
            if ($Solicitud->tipo_de_evento_id <> 3) {
                // Cargo por cada Conferencia del ciclo de conferencias una inscripcion
                $Fechas_de_evento = Fecha_de_evento::where('solicitud_id', $solicitud_id)->get();
                foreach ($Fechas_de_evento as $Fecha_de_evento) {
                    if (isset($_POST['fecha_de_evento_id_'.$Fecha_de_evento->id])) {

                        $date_of_interest = $Fecha_de_evento->fecha_de_inicio; 
                        $fecha_de_evento_id = $_POST['fecha_de_evento_id_'.$Fecha_de_evento->id];
                        array_push($tags_mautic, $Fecha_de_evento->titulo_de_conferencia_publica);

                        $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                            ->where('nombre', $nombre)
                            ->where('celular', $celular)
                            ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                            ->where('fecha_de_evento_id', $fecha_de_evento_id)
                            ->get();

                        if ($Inscripcion_previa->count() == 0) {

                            $se_registro_alguna_inscripcion = 'S';

                            $Inscripcion = new Inscripcion;
                            $Inscripcion->solicitud_id = $solicitud_id;
                            $Inscripcion->apellido = $apellido;
                            $Inscripcion->nombre = $nombre;
                            if ($celular <> '') {
                                $Inscripcion->celular = $celular;
                            }
                            if ($email_correo <> '') {
                                $Inscripcion->email_correo = $email_correo;
                            }
                            $Inscripcion->consulta = $consulta;      

                            $Inscripcion->fecha_de_evento_id = $fecha_de_evento_id;

                            if (isset($_POST['sino_notificar_proximos_eventos'])) {
                                $Inscripcion->sino_notificar_proximos_eventos = 'SI';
                            }
                            if (isset($_POST['acepto_politica_de_privacidad'])) {
                                $Inscripcion->sino_acepto_politica_de_privacidad = 'SI';
                            }
                            $Inscripcion->campania_id = $campania_id;
                            $Inscripcion->app_usuario_id = $app_usuario_id;
                            $Inscripcion->canal_de_recepcion_del_curso_id = $canal_de_recepcion_del_curso_id;
                            $Inscripcion->codigo_alumno = $Inscripcion->codigo_del_alumno();

                            try { 
                                $Inscripcion->save(); 
                                $inscripcion_id = $Inscripcion->id;
                                $detalle_fecha = $Inscripcion->fecha_de_evento->armarDetalleFechasDeEventos('html', true, null, $Solicitud, null);
                                $mensaje_box_fecha_de_evento .= '<br><br><span style="color: #a19b91">'.$detalle_fecha.'</span>';
                            } catch(\Illuminate\Database\QueryException $ex){ 
                                $detalle_de_origen = 'Registracion de Inscripcion (Cargo por cada Conferencia del ciclo de conferencias una inscripcion): '.URL::previous();
                                $Registro_de_error = new Registro_de_error;
                                $Registro_de_error->registro_de_error = $ex->getMessage();
                                $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                                $Registro_de_error->save();              
                                $error_inscripcion = true;
                            }                        
                        }
                        else {                        
                            $inscripcion_ya_registrada = 'S';
                            $inscripcion_id = $Inscripcion_previa[0]->id;
                        }
                    }
                }
                // si no se registro ninguna inscripcion
                if ($se_registro_alguna_inscripcion == 'N' and $inscripcion_ya_registrada == 'N') {

                    $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                        ->where('nombre', $nombre)
                        ->where('solicitud_id', $solicitud_id)
                        ->where('celular', $celular)
                        ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                        ->whereNull('fecha_de_evento_id')
                        ->get();


                    if ($Inscripcion_previa->count() == 0) {
                        
                        $se_registro_alguna_inscripcion = 'S';

                        $Inscripcion = new Inscripcion;
                        $Inscripcion->solicitud_id = $solicitud_id;
                        $Inscripcion->apellido = $apellido;
                        $Inscripcion->nombre = $nombre;
                        if ($celular <> '') {
                            $Inscripcion->celular = $celular;
                        }
                        if ($email_correo <> '') {
                            $Inscripcion->email_correo = $email_correo;
                        }
                        $Inscripcion->email_correo = $email_correo;
                        $Inscripcion->consulta = $consulta;      

                        if (isset($_POST['sino_notificar_proximos_eventos'])) {
                            $Inscripcion->sino_notificar_proximos_eventos = 'SI';
                        }
                        if (isset($_POST['acepto_politica_de_privacidad'])) {
                            $Inscripcion->sino_acepto_politica_de_privacidad = 'SI';
                        }
                        $Inscripcion->campania_id = $campania_id;
                        $Inscripcion->app_usuario_id = $app_usuario_id;
                        $Inscripcion->canal_de_recepcion_del_curso_id = $canal_de_recepcion_del_curso_id;
                        $Inscripcion->codigo_alumno = $Inscripcion->codigo_del_alumno();

                        try { 
                            $Inscripcion->save(); 
                            $inscripcion_id = $Inscripcion->id;
                        } catch(\Illuminate\Database\QueryException $ex){ 
                            $detalle_de_origen = 'Registracion de Inscripcion (si no se registro ninguna inscripcion): '.URL::previous();
                            $Registro_de_error = new Registro_de_error;
                            $Registro_de_error->registro_de_error = $ex->getMessage();
                            $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                            $Registro_de_error->save();              
                            $error_inscripcion = true;
                        }
                    }
                    else {                        
                        $inscripcion_ya_registrada = 'S';
                        $inscripcion_id = $Inscripcion_previa[0]->id;
                    }

                }
            }

        }

        

        //INICIO MAUTIC
            if (ENV('APP_ENV') <> 'development') {
                if (($email_correo <> '' and $solicitud_id <> 7805 and $solicitud_id <> 8443) or $institucion_id <> 1) {
                    $settings = array(
                        'userName'   => 'fmadoz',             // Create a new user       
                        'password'   => 'fM@d0Z'              // Make it a secure password
                    );

                    // Initiate the auth object specifying to use BasicAuth
                    $initAuth = new ApiAuth();
                    $auth = $initAuth->newAuth($settings, 'BasicAuth');

                    $api = new MauticApi();


                    $contactApi = $api->newApi('contacts', $auth, 'https://forms.gnosis.is');


                    $searchFilter = 'email:'.$email_correo;
                    $contacts = $contactApi->getList($searchFilter);

                    $Pais = '';
                    if ($Solicitud->tipo_de_evento_id == 3 or $Solicitud->tipo_de_evento_id == 4) {
                        if ($pais_id <> '') {
                            $Pais = Pais::find($pais_id);
                            $pais = $Pais->pais;
                        }
                        else {
                            if ($Solicitud->pais_id <> '') {
                                $pais = $Solicitud->pais->pais;
                            }  
                            else {                  
                                $pais = '';
                            }
                        }
                    }
                    else {
                        $pais = $Solicitud->localidad->provincia->pais->pais;
                    }            
                    $countrystateregionlocal = $pais.' / ';
                    array_push($tags_mautic, $pais);

                    if (strpos($Solicitud->tags_mautic, '#') >= 0) {
                        $tags_de_solicitud = explode('#', trim($Solicitud->tags_mautic));
                        foreach ($tags_de_solicitud as $tag) {
                            if ($tag <> '') {
                                array_push($tags_mautic, $tag); 
                            }
                        }              
                    }

                    $provincia = '';
                    if ($Solicitud->tipo_de_evento_id <> 3) {
                        if ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id <> '' and  in_array($Solicitud->id, array(3747, 4033, 4034, 4035, 4036, 4037)) ) {
                            $provincia = $Localidad->provincia->provincia;
                        }
                        else {
                            if ($Solicitud->localidad_id <> '') {
                                $provincia = $Solicitud->localidad->provincia->provincia;
                            }
                        }
                        $countrystateregionlocal .= $provincia.' / ';
                        array_push($tags_mautic, $provincia);
                    }       

                    if ($Solicitud->tipo_de_evento_id == 3 or $Solicitud->tipo_de_evento_id == 4) {
                        $localidad = $ciudad;
                    }
                    else {
                        $localidad = $Solicitud->localidad->localidad;
                    }                
                    $countrystateregionlocal .= $localidad;
                    array_push($tags_mautic, $localidad);
                    array_push($tags_mautic, 'id'.$Solicitud->id);

                    $last_active = date("Y-m-d H:i:s");

                    if ($contacts['total'] == "0") {

                        //dd($contacts['total']);
                        //$id = 759;
                        //$response = $contactApi->get($id);
                        //$contact = $response[$contactApi->itemName()];
                        //$response = $contactApi->getList('', 0, 1);
                        $systemsource = 'gnosis-incripcion-sistemaAC';

                        if ($fecha_de_evento_id == 'MO') {
                            $fecha_de_evento_id = null;
                        }

                        $data = array(
                            "email" => $email_correo,
                            "firstname" => $nombre,
                            "lastname" => $apellido,
                            "mobile" => $celular,
                            "themeofinterest" => $themeofinterest,
                            //"description" => $themeofinterest,
                            "countrystateregionlocal" => $countrystateregionlocal,
                            "pais" => $pais,
                            "provincia" => $provincia,
                            "ciudad" => $localidad,
                            "campaign_id" => $Solicitud->id,
                            "eventid" => $fecha_de_evento_id,
                            "systemsource" => $systemsource,
                            "date_of_interest" => $date_of_interest,
                            "last_active" => $last_active,
                            "notificar_proximos_evento" => $notificar_proximos_eventos,                    
                            "tags" => $tags_mautic,
                        );


                        $asset = $contactApi->create($data);

                        if (isset($Inscripcion)) {
                            $Inscripcion->mautic_contact_id = $asset['contact']['id'];
                            $Inscripcion->save(); 
                        }
                        
                    }
                    else {
                        $contactId = key($contacts['contacts']);

                        $data = array(
                            'tags' => $tags_mautic,
                            'last_active' => $last_active,
                            "notificar_proximos_evento" => $notificar_proximos_eventos,  
                            "info_log_actualizacion" => 'FormController Actualizacion de Contacto'.'inscripcion_id: '.$inscripcion_id.' - '.$email_correo.' - '.rand(0,1000),


                        );

                        $createIfNotFound = false;

                        $contact = $contactApi->edit($contactId, $data, $createIfNotFound);
                        //dd($contactId);


                        if (isset($Inscripcion)) {
                            $Inscripcion->mautic_contact_id = $contactId;
                            $Inscripcion->save(); 
                        }

                    }

                }            
            }

        //FIN MAUTIC    
        
        if ($Solicitud->id == 12958) {
            $codigo = 1;
            $asunto = __('Pedido de confirmación');
            $this->enviarNotificacionInscripcion($inscripcion_id, $codigo, $asunto);
        }
        $Inscripcion = Inscripcion::find($inscripcion_id);
        $sesion_id = Session()->get('sesion_id');

        try { 
            $now = new \DateTime();
            $fecha_now = $now->format('Y-m-d H:i:s');
            $Visualizacion_de_formulario = Visualizacion_de_formulario::where('sesion_id', $sesion_id)->orderBy('id', 'desc')->first();
            if ($Visualizacion_de_formulario <> null) {
                $Visualizacion_de_formulario->inscripcion_id = $inscripcion_id;
                $Visualizacion_de_formulario->fecha_y_hora_inscripcion = $fecha_now;
                $Visualizacion_de_formulario->save(); 
            }
        } catch(\Illuminate\Database\QueryException $ex){ 
            $detalle_de_origen = 'Registracion de Inscripcion Sesion_id: '.$sesion_id.' url previa: '.URL::previous();
            $Registro_de_error = new Registro_de_error;
            $Registro_de_error->registro_de_error = $ex->getMessage();
            $Registro_de_error->detalle_de_origen = $detalle_de_origen;
            $Registro_de_error->save();       
        }

        if ($error_inscripcion) {
            $mensaje_box = '<h4>'.mb_strtoupper($nombre, 'UTF-8').'</h4>'.__('hay algun error con su inscripción, intentelo nuevamente, y si persiste comuníquese con nuestro responsable de inscripción para inscribirse telefónicamente').': <br><h3>'.$Solicitud->nombre_responsable_de_inscripciones.' '.__('Celular').': '.$Solicitud->celular_responsable_de_inscripciones.'</h3>';   
        }
        else {
            if ($se_registro_alguna_inscripcion == 'S') {
                 
                 $mensaje_box = '<h4> <i class="icon fa fa-check"> </i> '.__('Felicitaciones').' '.mb_strtoupper($nombre, 'UTF-8').'</h4>'.__('Inscripción registrada');
                
                
                if ($solicitud_id == 9467) {
                    $generarPdfVMAron = $this->generarPdfVMAron($inscripcion_id);
                    $nombre_archivo_pdf = $generarPdfVMAron['nombre_archivo_pdf'];
                    $password_pdf = $generarPdfVMAron['password_pdf'];
                    $mensaje_box = '<h4> <i class="icon fa fa-check"> </i> '.__('Felicitaciones').' '.mb_strtoupper($nombre, 'UTF-8').'</h4>';
                    $mensaje_box .= '<p>Este libro esta protegido por un password personal que debes usar para abrirlo. Anotalo en algun lugar</p><p> <br>Password: <strong>'.$password_pdf.'</strong></p>';
                    $mensaje_box .= 'Descarga tu libro mediante este enlace: <br><br><a href ="'.ENV('PATH_PUBLIC').'storage/books/'.$nombre_archivo_pdf.'" target="_blank">'.ENV('PATH_PUBLIC').'storage/books/'.$nombre_archivo_pdf.'</a> <br><br>';
                }
                



            }
            else {
                $mensaje_box = '<h4> <i class="icon fa fa-check"> </i> '.mb_strtoupper($nombre, 'UTF-8').'</h4>'.__('su inscripción ya ha sido registrada');

                $mensaje_box .= '<br><br>';

                $tipo = 'html';
                $con_inicio = true;
                $Idioma_por_pais = null;
                $idioma = null;
                $ver_mapa = true;
                $con_dir_inicio_distinto = true;

                /*
                Retomar quitando estos comentarios
                $infoInscripcion = $Inscripcion->InfoInscripcion($tipo, $con_inicio, $Idioma_por_pais, $Inscripcion->solicitud, $idioma, $ver_mapa, $con_dir_inicio_distinto);

                
                foreach ($infoInscripcion as $info) {
                    if ($info[0] <> '') {
                        $mensaje_box .= '<b>'.$info[0].'</b>: ';    
                    }
                    $mensaje_box .= $info[1].'<br>';
                }

                $MauticController = new MauticController();
                $mensaje_box .= '<br><i><b>'.$MauticController->enviarMailMautic($Inscripcion).'</b></i>';
                */
                
                
                if ($solicitud_id == 5033) {
                    $mensaje_box .= '<br><strong>Para terminar tenes que seguír nuestra cuenta de Instagram <a href="https://www.instagram.com/gnosisenvivo/" target="_blank">@gnosisenvivo</a></strong>';  
                     
                }


                if ($solicitud_id == 9467) {

                    $password_pdf = $inscripcion_id+255;

                    $nombre_archivo_pdf = 'GNOSIS-ESCUELA-DE-REGENERACION-HUMANA-V-M-ARON-321'.$inscripcion_id.'.pdf';

                    $mensaje_box .= '<p>Este libro esta protegido por un password personal que debes usar para abrirlo. Anotalo en algun lugar</p><p> <br>Password: <strong>'.$password_pdf.'</strong></p>';
                    $mensaje_box .= 'Descarga tu libro mediante este enlace: <br><br><a href ="'.ENV('PATH_PUBLIC').'storage/books/'.$nombre_archivo_pdf.'" target="_blank">'.ENV('PATH_PUBLIC').'storage/books/'.$nombre_archivo_pdf.'</a> <br><br>';
                }   


                



            }

            if ($mensaje_box_fecha_de_evento <> '') {
                $mensaje_box .= $mensaje_box_fecha_de_evento;
            }

            if ($Solicitud->url_redireccionar_automaticamente_al_enlace <> '' and $Solicitud->mensaje_para_el_usuario_mientras_se_redirecciona <> '') {
                $mensaje_box .= '<p>'.$Solicitud->mensaje_para_el_usuario_mientras_se_redirecciona.'</p>';
            }
        }

        $url_invitacion_grupo_whatsapp = '';
        $url_fanpage = '';
        $url_youtube = '';
        $mnemo_face = '';
        $nombre_de_la_institucion = '';
        $url_invitacion_grupo_facebook = ''; 


        if ($Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual <> '') {
            $url_invitacion_grupo_whatsapp = $Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual;
        }
        else {
            if ($Solicitud->localidad_id <> '') {
                $url_invitacion_grupo_whatsapp = $Solicitud->localidad->url_invitacion_grupo_whatsapp;
            }            
        }
        

        if ($Solicitud->url_enlace_de_invitacion_al_grupo_de_facebook_del_aula_virtual <> '') {
            $url_invitacion_grupo_facebook = $Solicitud->url_enlace_de_invitacion_al_grupo_de_facebook_del_aula_virtual;
        }


        $url_redes = $this->urlRedesEspeciales($solicitud_id);

        if (count($url_redes) > 0) {
            $url_invitacion_grupo_whatsapp = $url_redes['url_invitacion_grupo_whatsapp'];
            $url_fanpage = $url_redes['url_fanpage'];
            $url_youtube = $url_redes['url_youtube'];
            $url_tiktok = $url_redes['url_tiktok'];
            $mnemo_face = $url_redes['mnemo_face'];
            $nombre_de_la_institucion = $url_redes['nombre_de_la_institucion'];
        }
        else {

            if ($Solicitud->idioma_por_pais() <> null) {
                $idioma_por_pais = $Solicitud->idioma_por_pais();    

                if ($idioma_por_pais->url_invitacion_grupo_whatsapp <> '' and $url_invitacion_grupo_whatsapp == '') {
                    $url_invitacion_grupo_whatsapp = $idioma_por_pais->url_invitacion_grupo_whatsapp;
                }

                if ($idioma_por_pais->url_fanpage <> '') {
                    $url_fanpage = $idioma_por_pais->url_fanpage;
                }
                
                if ($idioma_por_pais->url_youtube <> '') {
                    $url_youtube = $idioma_por_pais->url_youtube;
                }

                if ($idioma_por_pais->idioma->mnemo == 'es') {
                    $mnemo_face = 'es_LA';
                }
                else {
                    $mnemo_face = $idioma_por_pais->idioma->mnemo;
                }

                if ($idioma_por_pais->nombre_de_la_institucion <> '') {
                    $nombre_de_la_institucion = $idioma_por_pais->nombre_de_la_institucion;
                }
            }
        }

        // DETERMINO EL TITULO
        $titulo = '';
        if ($Solicitud->titulo_del_formulario_personalizado == '') {
            if ($Solicitud->tipo_de_evento->id == 1) {
                $titulo = __('CURSO DE AUTO-CONOCIMIENTO').'<br><strong>'.$Solicitud->localidad_nombre().'</strong>';  
            }
            if ($Solicitud->tipo_de_evento->id == 2) {
                if ($Solicitud->cant() == 1) {
                    $titulo = __('CONFERENCIA PÚBLICA').':<br><strong> '.$Solicitud->fechas_de_evento[0]->titulo_de_conferencia_publica.'</strong><br>'.$Solicitud->localidad_nombre();   
                }
                else {
                    $titulo = __('CICLO DE CONFERENCIAS PÚBLICAS').'<br>'.$Solicitud->localidad_nombre();            
                }                    
            }
            if ($Solicitud->tipo_de_evento->id == 3) {
                $titulo = __('CURSO DE AUTO-CONOCIMIENTO ON LINE').'<br><strong>'.$Solicitud->localidad_nombre().'</strong>';  
            }
            if ($Solicitud->tipo_de_evento->id == 4) {
                $titulo = '';  
            }
        }
        else {
            $titulo = $Solicitud->titulo_del_formulario_personalizado;
        }

        if ($embebed == 'embebed') {
            $blade_de_formulario = 'registracion-ok-embebed';
        }
        else {
            $blade_de_formulario = 'registracion-ok';
        }

        $url_form_inscripcion = '';
        if ($campania_id == null) {
            $url_form_inscripcion = $Solicitud->url_form_inscripcion();
        }
        else {
            $url_form_inscripcion = $Solicitud->url_form_inscripcion_con_campania_id($campania_id);
        }

        return View('forms/'.$blade_de_formulario)          
        ->with('Solicitud', $Solicitud)            
        ->with('titulo', $titulo)          
        ->with('mensaje_box', $mensaje_box)         
        ->with('url_invitacion_grupo_whatsapp', $url_invitacion_grupo_whatsapp)
        ->with('url_invitacion_grupo_facebook', $url_invitacion_grupo_facebook)
        ->with('url_fanpage', $url_fanpage)
        ->with('url_youtube', $url_youtube)         
        ->with('mnemo_face', $mnemo_face)   
        ->with('url_form_inscripcion', $url_form_inscripcion)
        ->with('nombre_de_la_institucion', $nombre_de_la_institucion)
        ->with('dominio_publico', $Solicitud->dominioPublico());     


    }


    public function enviarNotificacionInscripcion($inscripcion_id, $codigo, $asunto) {

        $Inscripcion = Inscripcion::find($inscripcion_id);
        if ($Inscripcion->fecha_de_evento_id <> null) {
            $url_whatsapp = $Inscripcion->url_whatsapp();
        }
        else {
            $url_whatsapp = $Inscripcion->url_whatsapp_sin_evento();
        }


        $idioma = $Inscripcion->solicitud->idioma->mnemo;
        App::setLocale($idioma);  


        $url_click = '';
        $txt_click = '';
        $txt_no_responder = __('No responda a este correo, si necesita comunicarse con nosotros envienos un mensaje de texto, whatsapp o llamenos al').' '.$Inscripcion->Solicitud->celular_responsable_de_inscripciones;
        $txt_pie = '';

        $hash = md5(ENV('PREFIJO_HASH').$inscripcion_id);

        if ($codigo == 1) {
            $mensaje = $url_whatsapp['mail_pedido_de_confirmacion'];
            //$mensaje = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-~]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $url_whatsapp['mail_pedido_de_confirmacion']);
            $emailId = 27;
            if ($Inscripcion->solicitud->tipo_de_evento_id == 4) {
                $url_click = $Inscripcion->solicitud->idioma->url_form_curso_online;
                $txt_click = __('Click aquí para inscribirme a los cursos');    
            }
            else {
                $url_click = ENV('PATH_PUBLIC').'f/auto/confirmar-asistencia/'.$inscripcion_id.'/'.$hash;
                $txt_click = __('Click aqui para confirmar su asistencia');                
            }
        }
        if ($codigo == 2) {
            $mensaje = $url_whatsapp['mail_no_respondieron_al_pedido_de_confirmacion'];
            $emailId = 26;
            $url_click = ENV('PATH_PUBLIC').'f/auto/confirmar-asistencia/'.$inscripcion_id.'/'.$hash;
            $txt_click = __('Click aqui para confirmar su asistencia');
        }
        if ($codigo == 4) {
            $mensaje = $url_whatsapp['mail_envio_de_voucher'];
            $emailId = 28;
            $url_click = ENV('PATH_PUBLIC').'f/v/'.$inscripcion_id.'/'.$hash;
            $txt_click = __('Click aqui para ver su Voucher').' '._('Voucher');
        }
        if ($codigo == 5) {
            $mensaje = $url_whatsapp['mail_envio_de_motivacion'];
            $emailId = 29;
        }
        if ($codigo == 6) {
            $mensaje = $url_whatsapp['mail_envio_de_recordatorio'];
            $emailId = 41;
        }
        if ($codigo == 7) {
            $mensaje = $url_whatsapp['mail_contesto_consulta'];
            $emailId = 31;
        }
        if ($codigo == 9) {
            $mensaje = $url_whatsapp['mail_envio_de_recordatorio_prox_clase'];
            $emailId = 32;
            $url_click = ENV('PATH_PUBLIC').'e/'.$inscripcion_id.'/'.$hash;
            $txt_click = __('Click aqui para ver su encuesta de satisfacción');
        }
        if ($codigo == 10) {
            $mensaje = $url_whatsapp['mail_envio_de_recordatorio_prox_clase_no_asistente'];
            $emailId = 42;
            $url_click = ENV('PATH_PUBLIC').'e/'.$inscripcion_id.'/'.$hash;
            $txt_click = __('Click aqui para ver su encuesta de satisfacción');
        }
        if ($codigo == 12) {
            $mensaje = $url_whatsapp['mail_envio_de_invitacion_al_curso_online'];
            $emailId = 42;
            $url_click = ENV('PATH_PUBLIC').'e/'.$inscripcion_id.'/'.$hash;
            $txt_click = __('Click aqui para ver su encuesta de satisfacción');
        }
        if ($codigo == 23) {
            $mensaje_extra = '';
            if (isset($_POST['mensaje_extra'])) {
                $mensaje_extra = $_POST['mensaje_extra'];

                if ($mensaje_extra <> '') {
                    $mensaje = $mensaje_extra;
                    $emailId = 117;              
                }
            }
        }

        $mensaje = str_replace('*', '', $mensaje);


        
        //INICIO MAUTIC
            if (ENV('APP_ENV') <> 'development') {
                try {
                    $apiUrl = 'https://forms.gnosis.is';
                    $settings = array(
                        'userName'   => 'fmadoz',             // Create a new user       
                        'password'   => 'fM@d0Z'              // Make it a secure password
                    );

                    // Initiate the auth object specifying to use BasicAuth
                    $initAuth = new ApiAuth();
                    $auth = $initAuth->newAuth($settings, 'BasicAuth');
                    $api = new MauticApi();


                    $searchFilter = 'email:'.$Inscripcion->email_correo;
                    $contactApi = $api->newApi('contacts', $auth, $apiUrl);
                    $contacts = $contactApi->getList($searchFilter);
                    $contactId = key($contacts['contacts']);

                    $emailApi = $api->newApi("emails", $auth, $apiUrl);
                    
                
                    //$searchFilter = 'emailname:fernandomadoz@hotmail.com';
                    //$emails = $emailApi->getList($searchFilter);
                    //dd($emails);

                    //$email = $emailApi->create($data);
                    //$emailId = $email['email']['id'];
                    
                    //$email = $emailApi->get($emailId);
                    //dd($email);

                    //$asset = $contactApi->create($data);

                    //$emailApi = $api->newApi("emails", $auth, $apiUrl);

                    $data = array(
                        'tokens' => array(
                            '{asunto}' => $asunto,
                            '{mensaje}' => $mensaje,
                            '{url_click}' => $url_click,
                            '{txt_click}' => $txt_click,
                            '{txt_no_responder}' => $txt_no_responder,
                            '{txt_pie}' => $txt_pie

                        )
                    );

                    //dd($data);

                    $email = $emailApi->makeRequest('emails/'.$emailId.'/contact/'.$contactId.'/send', $data, 'POST');

                    //dd($email);

                    //$email = $emailApi->sendToContact($emailId, $contactId);

                    $tit_mensaje_envio = __('Email Enviado Exitosamente');
                    //dd($email);
                
                } catch (Exception $e) {
                    $tit_mensaje_envio = __('Error en el envio del Email');
                    //dd('error');
                    // Do Error handling
                }
            }
        //FIN MAUTIC 


        $mensaje_envio = '<div class="col-xs-12 col-lg-12">';
        $mensaje_envio .= '<br>';
        $mensaje_envio .= '<div class="alert alert-success alert-dismissible">';
        $mensaje_envio .= '<h4><i class="icon fa fa-send"></i> '.$tit_mensaje_envio.'</h4><p>'.$Inscripcion->email_correo.' ('.$asunto.')</p>';
        $mensaje_envio .= '</div>';
        $mensaje_envio .= '</div>';

        return $mensaje_envio;


    }


    public function enviarSolicitud($solicitud_id)
    {   

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $now = new \DateTime();
        $Solicitud->fecha_de_solicitud = $now->format('Y-m-d H:i:s');
        $Solicitud->save();

        $paso = 6;
        $pasos_info = $this->pasosInfo($solicitud_id, $paso);

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $solicitud_id)     
        ->with('paso', $paso)
        ->with('pasos_info', $pasos_info);               
    }





    public function pasosInfo($solicitud_id, $paso)
    { 

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $pasos_info = array();

        //PASO 1
        if ($paso >= 1) {
            $paso_1 = "Tipo de Evento: ".$Solicitud->Tipo_de_evento->tipo_de_evento;
            array_push($pasos_info, $paso_1);    
        }
        
        
        //PASO 2
        if ($paso >= 2) {
            if ($Solicitud->Tipo_de_evento->id == 2) {             
                $Conferencia_publica = Conferencia_publica::where('solicitud_id', $solicitud_id)->first();
                $paso_2 = "Solicitante: ".$Solicitud->nombre_del_solicitante."(".$Solicitud->localidad_nombre().") - ".$Conferencia_publica->titulo_de_conferencia_publica;
            }
            else {
                $Conferencia_publica = NULL;
                $paso_2 = "Solicitante: ".$Solicitud->nombre_del_solicitante."(".$Solicitud->localidad_nombre().")";
            }            
            array_push($pasos_info, $paso_2); 
        }

        //PASO 3
        if ($paso >= 3) {
            $cant_fechas = Fecha_de_evento::where('solicitud_id', $solicitud_id)->count();
            $paso_3 = 'Horarios: '.$cant_fechas;  
            array_push($pasos_info, $paso_3);   
        }
        
        //PASO 4        
        if ($paso >= 4) {
            $paso_4 = 'Monto: '.$Solicitud->monto_a_invertir;
            array_push($pasos_info, $paso_4); 
        }

        //PASO 5    
        if ($paso >= 5) {
            $paso_5 = 'Revisión: OK';
            array_push($pasos_info, $paso_5); 
        }

        //PASO 6    
        if ($paso >= 6) {
            $paso_6 = 'Solicitud Enviada<br><i class="fa fa-fw fa-check-circle-o" style="font-size: 40px"></i>';
            array_push($pasos_info, $paso_6); 
        }

        return $pasos_info;

    }



    public function traerInscripciones($solicitud_id, $campania_id = 0, $offset = 0, $cant_x_pagina = null, $grupo = null, $criterio = null, $historico = false, $recupero = array())
    {
        if ($cant_x_pagina == null) {
            $cant_x_pagina = $this->cant_x_pagina;
        }

        $solicitud_id_es_id = false;
        $Grupos = null;
                
        $whereRaw = '1 = 1';
        if (count($recupero) > 0) {
            $recupero_tipo = $recupero['tipo'];
            if ($recupero_tipo == 'pais') {
                $recupero_pais_id = $recupero['id'];
                $recupero_cant_dias = $recupero['cant_dias'];
                $whereRaw .= " and (inscripciones.pais_id = $recupero_pais_id or s.pais_id = $recupero_pais_id or pr.pais_id = $recupero_pais_id) and DATEDIFF(NOW(), IFNULL(f.fecha_de_inicio, inscripciones.created_at)) BETWEEN 30 AND $recupero_cant_dias and a.id is null and s.tipo_de_evento_id in (1,2)";
            } 
            $Grupos['cant_total_inscriptos'] = $Inscripciones = Inscripcion::whereRaw($whereRaw)
            ->leftjoin('fechas_de_evento as f', 'f.id', '=', 'inscripciones.fecha_de_evento_id')
            ->leftjoin('asistencias as a', 'a.inscripcion_id', '=', 'inscripciones.id')            
            ->leftjoin('solicitudes as s', 's.id', '=', 'inscripciones.solicitud_id')
            ->leftjoin('localidades as loc', 'loc.id', '=', 's.localidad_id')
            ->leftjoin('provincias as pr', 'pr.id', '=', 'loc.provincia_id')->count();
        }

        if ($historico) {
            $Solicitud_h = Solicitud::find($solicitud_id);
            $localidad_id_h = $Solicitud_h->localidad_id;
            $escribe_tu_ciudad_h = $Solicitud_h->escribe_tu_ciudad_sino_esta_en_la_lista_anterior_h;

            if ($localidad_id_h > 0 or trim($escribe_tu_ciudad_h) <> '') {
                if ($localidad_id_h > 0) {
                    $pais_id_h = $Solicitud_h->localidad->provincia->pais_id;
                    $localidad_h = $Solicitud_h->localidad->localidad;
                    $whereRaw .= " and (s.localidad_id = $localidad_id_h or (inscripciones.ciudad like '%$localidad_h%' and inscripciones.pais_id = $pais_id_h))";
                }
                else {
                    $pais_id_h = $Solicitud_h->pais_id;
                    $whereRaw .= " and ((loc.localidad like '%escribe_tu_ciudad_h%' and s.pais_id = $pais_id_h) or (inscripciones.ciudad like '%$escribe_tu_ciudad_h%' and inscripciones.pais_id = $pais_id_h))";
                }
            }
            else {
                $pais_id_h = $Solicitud_h->pais_id;
                $whereRaw .= " and (s.pais_id = $pais_id_h or inscripciones.pais_id = $pais_id_h)";
            }

            //$whereRaw .= " and inscripciones.created_at < '2022-03-01'";
            $whereRaw .= " and s.id <> $solicitud_id";
            //$whereRaw .= " and inscripciones.id = 198632 ";
            //$whereRaw .= " and inscripciones.sino_notificar_proximos_eventos = 'SI'";
            $Grupos['cant_total_inscriptos'] = $Inscripciones = Inscripcion::whereRaw($whereRaw)
            ->leftjoin('solicitudes as s', 's.id', '=', 'inscripciones.solicitud_id')->count();

        }
        else {
            if (is_array($solicitud_id)) {            
                $solicitudes = implode(', ', $solicitud_id);
                $whereRaw .= " and inscripciones.solicitud_id in ($solicitudes)";
            }
            else {            
                if ($solicitud_id > 0 and count($recupero) == 0) {
                    $solicitud_id_es_id = true;
                    $whereRaw .= " and (inscripciones.solicitud_id = $solicitud_id or (inscripciones.solicitud_original = $solicitud_id and inscripciones.causa_de_cambio_de_solicitud_id in (1, 4) ))";
                }
            }
        }

        if ($campania_id > 0) {
            $whereRaw .= " and inscripciones.campania_id = $campania_id";
        }

        if ($grupo > 0) {
            $whereRaw .= " and inscripciones.grupo = $grupo";
            $cant_x_pagina = 'all';
        }

        if ($criterio <> null) {
            $palabras = explode(" ", trim($criterio));
            foreach ($palabras as $palabra) {
                $whereRaw .= " and (";
                $whereRaw .= "inscripciones.id = '".$palabra."' or ";
                $whereRaw .= "inscripciones.solicitud_original = '".$palabra."' or ";
                $whereRaw .= "inscripciones.nombre like '%".$palabra."%' or ";
                $whereRaw .= "inscripciones.apellido like '%".$palabra."%' or ";
                $whereRaw .= "inscripciones.ciudad like '%".$palabra."%' or ";
                $whereRaw .= "p.pais like '%".$palabra."%' or ";
                $whereRaw .= "inscripciones.codigo_alumno like '%".$palabra."%' or ";
                $whereRaw .= "inscripciones.celular like '%".$palabra."%' or ";
                $whereRaw .= "inscripciones.observaciones like '%".$palabra."%' or ";
                $whereRaw .= "inscripciones.email_correo like '%".$palabra."%')";          
            }
        }

        $Modelos_alumno = null;

        if ($solicitud_id_es_id) {
            $Solicitud = Solicitud::find($solicitud_id);
            $Fechas_de_evento = Fecha_de_evento::select(DB::Raw('fechas_de_evento.id, fechas_de_evento.solicitud_id, fechas_de_evento.titulo_de_conferencia_publica, fechas_de_evento.resumen_de_la_conferencia, fechas_de_evento.fecha_de_inicio, fechas_de_evento.hora_de_inicio, fechas_de_evento.direccion_de_inicio, fechas_de_evento.url_enlace_a_google_maps_inicio, fechas_de_evento.url_enlace_foto_de_fachada_del_lugar, fechas_de_evento.cupo_maximo_disponible_del_salon, fechas_de_evento.hora_lunes, fechas_de_evento.hora_martes, fechas_de_evento.hora_miercoles, fechas_de_evento.hora_jueves, fechas_de_evento.hora_viernes, fechas_de_evento.hora_sabado, fechas_de_evento.hora_domingo, fechas_de_evento.direccion_del_curso, fechas_de_evento.url_enlace_a_google_maps_curso, fechas_de_evento.created_at, fechas_de_evento.updated_at, fechas_de_evento.sino_agotado, fechas_de_evento.url_enlace_a_google_maps_inicio_redirect_final, fechas_de_evento.url_enlace_a_google_maps_curso_redirect_final, fechas_de_evento.latitud, fechas_de_evento.longitud, fechas_de_evento.url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual, COUNT(i.id) cant_inscriptos'))
                ->where('fechas_de_evento.solicitud_id', $solicitud_id)
                ->leftjoin('inscripciones as i', 'fechas_de_evento.id', '=', 'i.fecha_de_evento_id')
                ->groupBy(DB::Raw('fechas_de_evento.id, fechas_de_evento.solicitud_id, fechas_de_evento.titulo_de_conferencia_publica, fechas_de_evento.resumen_de_la_conferencia, fechas_de_evento.fecha_de_inicio, fechas_de_evento.hora_de_inicio, fechas_de_evento.direccion_de_inicio, fechas_de_evento.url_enlace_a_google_maps_inicio, fechas_de_evento.cupo_maximo_disponible_del_salon, fechas_de_evento.hora_lunes, fechas_de_evento.hora_martes, fechas_de_evento.hora_miercoles, fechas_de_evento.hora_jueves, fechas_de_evento.hora_viernes, fechas_de_evento.hora_sabado, fechas_de_evento.hora_domingo, fechas_de_evento.direccion_del_curso, fechas_de_evento.url_enlace_a_google_maps_curso, fechas_de_evento.created_at, fechas_de_evento.updated_at, fechas_de_evento.sino_agotado, fechas_de_evento.url_enlace_a_google_maps_inicio_redirect_final, fechas_de_evento.url_enlace_a_google_maps_curso_redirect_final, fechas_de_evento.latitud, fechas_de_evento.longitud, fechas_de_evento.url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual'))
                ->get();
            $Idioma_por_pais = $Solicitud->idioma_por_pais();
            //dd($Fechas_de_evento[0]);
            //$pais_id = $Solicitud->id_pais();
            //$Pais = Pais::find($pais_id);
            $Grupos = $this->traerGrupos($Solicitud);
        }
        else {
            $Fechas_de_evento = null;
            $Idioma_por_pais = null;
            //$Pais = null;
        }
        //DB::enableQueryLog();
        $Inscripciones = Inscripcion::select(DB::Raw('inscripciones.id, inscripciones.solicitud_id, inscripciones.solicitud_original, inscripciones.causa_de_cambio_de_solicitud_id, inscripciones.apellido, inscripciones.nombre, inscripciones.celular, inscripciones.email_correo, inscripciones.pais_id, inscripciones.ciudad, inscripciones.consulta, inscripciones.fecha_de_evento_id, inscripciones.sino_notificar_proximos_eventos, inscripciones.sino_acepto_politica_de_privacidad, inscripciones.created_at, inscripciones.updated_at, inscripciones.sino_envio_pedido_de_confirmacion, inscripciones.sino_confirmo, inscripciones.sino_envio_recordatorio_pedido_de_confirmacion, inscripciones.sino_envio_voucher, inscripciones.sino_envio_motivacion, inscripciones.sino_envio_motivacion_2, inscripciones.sino_envio_motivacion_3, inscripciones.sino_envio_de_encuesta, inscripciones.sino_envio_recordatorio, inscripciones.sino_asistio, inscripciones.sino_contesto_consulta, inscripciones.sino_envio_recordatorio_proxima_clase, inscripciones.sino_envio_recordatorio_proxima_clase_a_no_asistente, inscripciones.sino_cancelo, inscripciones.sino_invitado_al_curso_online, inscripciones.sino_envio_1, inscripciones.sino_envio_2, inscripciones.sino_envio_3, inscripciones.sino_envio_4, inscripciones.sino_envio_5, inscripciones.sino_envio_6, inscripciones.sino_envio_7, inscripciones.sino_envio_8, inscripciones.sino_envio_9, inscripciones.sino_envio_10, sino_envio_certificado, inscripciones.observaciones, p.pais as nombre_pais, l.nombre_de_la_leccion, l.codigo_de_la_leccion, l.orden_de_leccion, lx.titulo, lx.nro_o_codigo, lp.id as proximaLeccion_id, lp.codigo_de_la_leccion proximaLeccion_codigo, c.canal_de_recepcion_del_curso, inscripciones.causa_de_baja_id, inscripciones.grupo, inscripciones.codigo_alumno, me.titulo_de_la_evaluacion, IFNULL(gs.nombre_responsable_de_inscripciones, s.nombre_responsable_de_inscripciones) nombre_responsable_de_inscripciones,  IFNULL(gs.celular_responsable_de_inscripciones, s.celular_responsable_de_inscripciones) celular_responsable_de_inscripciones, COUNT(enc.id) cant_encuestas, COUNT(DISTINCT l.id) cant_lecciones, COUNT(DISTINCT a.id) cant_asistencias, COUNT(DISTINCT e.modelo_de_evaluacion_id) cant_evaluaciones, inscripciones.sino_eleccion_modalidad_online, en.id envio_id, aa.estado_de_seguimiento_id, i.idioma'))
        ->whereRaw($whereRaw)
        ->leftjoin('fechas_de_evento as f', 'f.id', '=', 'inscripciones.fecha_de_evento_id')
        ->join('solicitudes as s', 's.id', '=', 'inscripciones.solicitud_id')
        ->leftjoin('idiomas as i', 'i.id', '=', 's.idioma_id')
        ->leftjoin('localidades as loc', 'loc.id', '=', 's.localidad_id')
        ->leftjoin('provincias as pr', 'pr.id', '=', 'loc.provincia_id')
        ->leftjoin('paises as p', 'p.id', '=', 'inscripciones.pais_id')
        ->leftjoin('encuestas_de_satisfaccion as enc', 'enc.inscripcion_id', '=', 'inscripciones.id')
        ->leftjoin('evaluaciones as e', 'e.inscripcion_id', '=', 'inscripciones.id')
        ->leftjoin('evaluaciones as ue', 'ue.id', '=', 'inscripciones.ultima_evaluacion')
        ->leftjoin('modelos_de_evaluacion as me', 'me.id', '=', 'ue.modelo_de_evaluacion_id')
        ->leftjoin('canales_de_recepcion_del_curso as c', 'c.id', '=', 'inscripciones.canal_de_recepcion_del_curso_id')
        ->leftjoin('alumnos_avanzados as aa', 'aa.inscripcion_id', '=', 'inscripciones.id')
        //->leftjoin('grupos_de_solicitud as gs', DB::Raw('gs.nro_de_grupo = inscripciones.grupo and gs.solicitud_id = inscripciones.solicitud_id'), 'and 1=1', 'and 1=1')
        ->leftjoin('envios as en', function ($join) use ($solicitud_id) {
            $join->on('en.solicitud_id', '=', DB::Raw($solicitud_id))->on('en.inscripcion_id', '=', 'inscripciones.id');
        })
        ->leftjoin('grupos_de_solicitud as gs', function ($join) {
            $join->on('gs.nro_de_grupo', '=', 'inscripciones.grupo')->on('gs.solicitud_id', '=', 'inscripciones.solicitud_id');
        })
        ->leftjoin('lecciones as l', 'l.id', '=', DB::raw('(SELECT a.leccion_id FROM asistencias as a JOIN lecciones as l2 ON l2.id = a.leccion_id WHERE a.inscripcion_id = inscripciones.id ORDER BY l2.orden_de_leccion DESC LIMIT 1)'))
        ->leftjoin('lecciones_extra as lx', 'lx.id', '=', DB::raw('(SELECT a.leccion_extra_id FROM asistencias as a JOIN lecciones_extra as lx2 ON lx2.id = a.leccion_extra_id WHERE a.inscripcion_id = inscripciones.id ORDER BY lx2.id DESC LIMIT 1)'))
        ->leftjoin('lecciones as lp', function ($join) {
            $join->on('lp.orden_de_leccion', '=', DB::Raw('IFNULL(l.orden_de_leccion, 0)+1'))
            ->on('lp.curso_id', '=', 's.curso_id')
            ->limit(1);
        })
        ->leftjoin('asistencias as a', 'a.inscripcion_id', '=', 'inscripciones.id')
        ->groupBy(DB::Raw('inscripciones.id, inscripciones.solicitud_id, inscripciones.solicitud_original, inscripciones.causa_de_cambio_de_solicitud_id, inscripciones.apellido, inscripciones.nombre, inscripciones.celular, inscripciones.email_correo, inscripciones.pais_id, inscripciones.ciudad, inscripciones.consulta, inscripciones.fecha_de_evento_id, inscripciones.sino_notificar_proximos_eventos, inscripciones.sino_acepto_politica_de_privacidad, inscripciones.created_at, inscripciones.updated_at, inscripciones.sino_envio_pedido_de_confirmacion, inscripciones.sino_confirmo, inscripciones.sino_envio_recordatorio_pedido_de_confirmacion, inscripciones.sino_envio_voucher, inscripciones.sino_envio_motivacion, inscripciones.sino_envio_motivacion_2, inscripciones.sino_envio_motivacion_3, inscripciones.sino_envio_de_encuesta, inscripciones.sino_envio_recordatorio, inscripciones.sino_asistio, inscripciones.sino_contesto_consulta, inscripciones.sino_envio_recordatorio_proxima_clase, inscripciones.sino_envio_recordatorio_proxima_clase_a_no_asistente, inscripciones.sino_cancelo, inscripciones.sino_invitado_al_curso_online, inscripciones.sino_envio_1, inscripciones.sino_envio_2, inscripciones.sino_envio_3, inscripciones.sino_envio_4, inscripciones.sino_envio_5, inscripciones.sino_envio_6, inscripciones.sino_envio_7, inscripciones.sino_envio_8, inscripciones.sino_envio_9, inscripciones.sino_envio_10, sino_envio_certificado, inscripciones.observaciones, p.pais, l.nombre_de_la_leccion, l.codigo_de_la_leccion, l.orden_de_leccion, lx.titulo, lx.nro_o_codigo, c.canal_de_recepcion_del_curso, inscripciones.causa_de_baja_id, inscripciones.grupo, inscripciones.codigo_alumno, me.titulo_de_la_evaluacion, nombre_responsable_de_inscripciones, celular_responsable_de_inscripciones, inscripciones.sino_eleccion_modalidad_online, en.id, aa.estado_de_seguimiento_id, i.idioma'))
        ->orderBy('id', 'desc');
        if ($cant_x_pagina <> 'all') {
            $Inscripciones = $Inscripciones->limit($cant_x_pagina);        
        }
        //dd($cant_x_pagina);
        if ($offset > 0) {
            $Inscripciones = $Inscripciones->offset($offset);        
        }
        $Inscripciones = $Inscripciones->get();  
        //dd($Inscripciones);
        //dd(DB::getQueryLog());

        /*
        if ($solicitud_id == 10440) {
            dd($Inscripciones);
        }
        */

        return [
            'Inscripciones' => $Inscripciones,
            'Fechas_de_evento' => $Fechas_de_evento,
            'Idioma_por_pais' => $Idioma_por_pais,
            //'Pais' => $Pais,
            'Grupos' => $Grupos     
        ];
    }

    public function traerGrupos ($Solicitud)
    {
        $solicitud_id = $Solicitud->id;

        $cant_total_inscriptos = Inscripcion::whereRaw("(solicitud_id = $solicitud_id or (solicitud_original = $solicitud_id and causa_de_cambio_de_solicitud_id in (1,4)))")->count();

        $max_grupo = 1;
        $max_grupo_inscripcion = Inscripcion::where('solicitud_id', $Solicitud->id)->max('grupo');
        if ($max_grupo_inscripcion > $max_grupo) {
            $max_grupo = $max_grupo_inscripcion;
        }

        $max_nro_de_grupo = Grupo_de_solicitud::where('solicitud_id', $Solicitud->id)->max('nro_de_grupo');
        if ($max_nro_de_grupo > $max_grupo) {
            $max_grupo = $max_nro_de_grupo;
        }

        $Grupos_de_solicitud = Grupo_de_solicitud::where('solicitud_id', $Solicitud->id)->get();

        if ($Solicitud->cant_de_grupos > $max_grupo) {
            $max_grupo = $Solicitud->cant_de_grupos;
        }
        else {
            $calculo_de_grupos = intval($cant_total_inscriptos/70);
            if ($cant_total_inscriptos%70 > 0) {
                $calculo_de_grupos++;
            }
            if ($calculo_de_grupos > $max_grupo) {
                $max_grupo = $calculo_de_grupos;
            }
        }

        if ($max_grupo > 300 ) {
            $max_grupo = 300;
        }
        $lista_de_grupos = [];
        for ($i = 1; $i <= $max_grupo; $i++) {
            if ($Grupos_de_solicitud->where('nro_de_grupo', $i)->count() > 0) {
                $Grupo = $Grupos_de_solicitud->where('nro_de_grupo', $i)->all();
                foreach ($Grupo as $G) {
                    $G = $G;
                }
                $grupo_id = $G['id'];
                $celular_responsable_de_inscripciones = $G['celular_responsable_de_inscripciones'];
                $nombre_responsable_de_inscripciones = $G['nombre_responsable_de_inscripciones'];
            }
            else {
                $grupo_id = 'null';
                $celular_responsable_de_inscripciones = '';
                $nombre_responsable_de_inscripciones = '';
            }

            $grupo = [
                'grupo_id' => $grupo_id,
                'nro_de_grupo' => $i,
                'url' => $Solicitud->url_grupo_whatsapp($i),
                'celular_responsable_de_inscripciones' => $celular_responsable_de_inscripciones,
                'nombre_responsable_de_inscripciones' => $nombre_responsable_de_inscripciones                
            ];
            array_push($lista_de_grupos, $grupo);
        }


        $Grupos = [
            'cant_total_inscriptos' => $cant_total_inscriptos,
            'max_grupo' => $max_grupo,
            'lista_de_grupos' => $lista_de_grupos
        ];

        return $Grupos;

    }

    public function traerModelosMensajesCurso($Solicitud, $Inscripciones, $Idioma_por_pais, $Grupo_de_solicitud)
    {

        $Modelos_grupo = [];
        $Modelos_alumno = [];

        if ($Idioma_por_pais->pais_id > 0) {
            $codigo_tel = $Idioma_por_pais->pais->codigo_tel;
        }
        else {
            $codigo_tel = '';
        }

        if ($Solicitud->tipo_de_evento_id == 3) {
            $curso_id = $Solicitud->curso_id;
            if ($curso_id == '') {
                $curso_id = 1;
            }

            $Curso = Curso::find($curso_id);

            $Modelos_de_mensajes_curso_grupo = Modelo_de_mensaje_curso::where('curso_id', $curso_id)->whereRaw('(sino_mensaje_para_alumno = "NO" OR sino_mensaje_para_alumno = "" OR sino_mensaje_para_alumno IS NULL)')->get();

            foreach ($Modelos_de_mensajes_curso_grupo as $Modelo) {
                $url_texto_modelo = $Solicitud->texto_modelo_del_mensaje_del_curso($Modelo->id, $Grupo_de_solicitud, $codigo_tel);

                $array_modelo = [
                    'titulo_del_mensaje' => $Modelo->titulo_del_mensaje,
                    'aclaracion' => $Modelo->aclaracion,
                    'url_texto_modelo' => $url_texto_modelo
                ];                    

                array_push($Modelos_grupo, $array_modelo);  
                
            }


            $Modelos_alumno = Modelo_de_mensaje_curso::where('curso_id', $curso_id)->where('sino_mensaje_para_alumno', 'SI')->get();

            if ($Modelos_alumno->count() > 0) {

                $Inscripciones->map(function($Inscripcion) use ($Modelos_alumno, $Idioma_por_pais, $Solicitud, $Curso){
                    $Modelos_extra = array();

                    if (!in_array($Solicitud->id, array(400, 815))) {
                        foreach ($Modelos_alumno as $Modelo) {       

                            if (in_array($Modelo->id, array(3, 10, 18, 34, 49)) and ($Solicitud->mensaje_previo_al_inicio_del_curso <> '' or $Idioma_por_pais->mensaje_previo_al_inicio_del_curso <> '')) {
                                if ($Solicitud->mensaje_previo_al_inicio_del_curso <> '') {
                                    $Modelo_de_mensaje_curso = $Solicitud->mensaje_previo_al_inicio_del_curso;
                                }
                                else {
                                    $Modelo_de_mensaje_curso = $Idioma_por_pais->mensaje_previo_al_inicio_del_curso;    
                                }
                            }            
                            else {
                                $Modelo_de_mensaje_curso = $Modelo->modelo_del_mensaje;
                            }            

                            $Modelo_extra = [
                                'titulo_del_mensaje' => $Modelo->titulo_del_mensaje, 
                                'aclaracion' => $Modelo->aclaracion, 
                                'url_del_mensaje' => $Inscripcion->url_whatsapp_modelo_mensaje_curso($Modelo_de_mensaje_curso, $Idioma_por_pais, $Inscripcion->nombre_responsable_de_inscripciones, $Solicitud, $Curso, $Inscripcion->orden_de_leccion, $Inscripcion->proximaLeccion_id, $Inscripcion->proximaLeccion_codigo)
                            ];
                            array_push($Modelos_extra, $Modelo_extra);
                        }
                    }
                    $Inscripcion->Modelos_extra = $Modelos_extra;
                });

            }
        }
        
        //dd($Inscripciones);
        $texto_lecciones_de_curso = $Solicitud->texto_lecciones_de_curso($Grupo_de_solicitud, $codigo_tel);
        $Causas_de_baja = Causa_de_baja::all();
        $Causas_de_cambio_de_solicitud = Causa_de_cambio_de_solicitud::whereRaw('id NOT IN (1,4,5)')->get();

        return [
            'Inscripciones' => $Inscripciones,
            'texto_lecciones_de_curso' => $texto_lecciones_de_curso,
            'Modelos_grupo' => $Modelos_grupo,
            'Modelos_alumno' => $Modelos_alumno,
            'Causas_de_baja' => $Causas_de_baja,
            'Causas_de_cambio_de_solicitud' => $Causas_de_cambio_de_solicitud
        ];
    }

    public function listInscriptos($solicitud_id, $hash)
    {  
       
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash) {              

            $now = new \DateTime();
            $fecha_now = $now->format('Y-m-d H:i:s');
            $Solicitud->ultimo_acceso_planilla_inscripcion = $fecha_now;
            $Solicitud->save();

                
            $traerInscripciones = $this->traerInscripciones($solicitud_id);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];   
            $Grupos = $traerInscripciones['Grupos'];
            $Grupo_de_solicitud = null;

            /*
            if ($Solicitud->id == 400 or $Solicitud->id == 815) {
                $Inscripciones_limit = $Inscripciones->slice(0, 100);
                $Inscripciones_limit = collect($Inscripciones_limit->all());
                $Inscripciones = $Inscripciones_limit;
            }
            */

            $traerModelosMensajesCurso = $this->traerModelosMensajesCurso($Solicitud, $Inscripciones, $Idioma_por_pais, $Grupo_de_solicitud);

            
            $Inscripciones = $traerModelosMensajesCurso['Inscripciones'];
            $texto_lecciones_de_curso = $traerModelosMensajesCurso['texto_lecciones_de_curso'];
            $Modelos_grupo = $traerModelosMensajesCurso['Modelos_grupo'];
            $Modelos_alumno = $traerModelosMensajesCurso['Modelos_alumno'];
            $Causas_de_baja = $traerModelosMensajesCurso['Causas_de_baja'];
            $Causas_de_cambio_de_solicitud = $traerModelosMensajesCurso['Causas_de_cambio_de_solicitud'];


            $Mensaje_limit = '';
            $cant_paginas = 0;
            $cant_x_pagina = $this->cant_x_pagina;
            if ($Grupos['cant_total_inscriptos'] > $cant_x_pagina) { 

                //$Inscripciones = $Inscripciones->reverse(); 
                $cant_paginas = intval($Grupos['cant_total_inscriptos']/$cant_x_pagina);
                $resto = $Grupos['cant_total_inscriptos']-($cant_paginas*$cant_x_pagina);
                if ($Grupos['cant_total_inscriptos']%$cant_x_pagina > 0) {
                    $cant_paginas++;
                }
                else {
                    $resto = $cant_x_pagina;
                }

                $Inscripciones_limit = $Inscripciones->slice(0, $resto);
                $Inscripciones_limit = collect($Inscripciones_limit->all());
                $Inscripciones = $Inscripciones_limit;
                $Mensaje_limit = "Dividimos los Inscriptos en páginas de ".$cant_x_pagina." (total de inscriptos ".$Grupos['cant_total_inscriptos'].").";


            }

            
            return View('forms/listar-inscriptos')
            ->with('Inscripciones', $Inscripciones) 
            ->with('Fechas_de_evento', $Fechas_de_evento)
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', $Idioma_por_pais)
            //->with('Pais', $Pais)
            ->with('texto_lecciones_de_curso', $texto_lecciones_de_curso)
            ->with('Modelos_grupo', $Modelos_grupo)
            ->with('Modelos_alumno', $Modelos_alumno)
            ->with('Causas_de_baja', $Causas_de_baja)
            ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
            ->with('Mensaje_limit', $Mensaje_limit)
            ->with('cant_paginas', $cant_paginas)
            ->with('cant_x_pagina', $cant_x_pagina)
            ->with('Grupos', $Grupos)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais));
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }



    public function listInscriptosHistoricos($solicitud_id, $hash)
    {  
       
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash) {              
            $campania_id = NULL;
            $offset = 0;
            $cant_x_pagina = null;
            $grupo = null;
            $criterio = null;
            $historico = true;
            $traerInscripciones = $this->traerInscripciones($solicitud_id, $campania_id, $offset, $cant_x_pagina, $grupo, $criterio, $historico);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];   
            $Grupos = $traerInscripciones['Grupos'];
            $Grupo_de_solicitud = null;


            $Mensaje_limit = '';
            $cant_paginas = 0;
            $cant_x_pagina = $this->cant_x_pagina;
            if ($Grupos['cant_total_inscriptos'] > $cant_x_pagina) { 

                //$Inscripciones = $Inscripciones->reverse(); 
                $cant_paginas = intval($Grupos['cant_total_inscriptos']/$cant_x_pagina);
                $resto = $Grupos['cant_total_inscriptos']-($cant_paginas*$cant_x_pagina);
                if ($Grupos['cant_total_inscriptos']%$cant_x_pagina > 0) {
                    $cant_paginas++;
                }
                else {
                    $resto = $cant_x_pagina;
                }

                $Inscripciones_limit = $Inscripciones->slice(0, $resto);
                $Inscripciones_limit = collect($Inscripciones_limit->all());
                $Inscripciones = $Inscripciones_limit;
                $Mensaje_limit = "Dividimos los Inscriptos en páginas de ".$cant_x_pagina." (total de inscriptos ".$Grupos['cant_total_inscriptos'].").";


            }

            $Idioma_por_pais = $Solicitud->idioma_por_pais();

            $titulo_planilla = __('Invitar a Contactos Históricos').' | ';
            if ($Solicitud->localidad_id > 0) {
                 $titulo_planilla .= __('Ciudad').': '.$Solicitud->localidad->localidad; 
            }
            else {
                 $titulo_planilla .= __('Pais').': '.$Solicitud->pais->pais;  
            }

            $parametros_paginacion = 'historico';

            
            return View('forms/listar-inscriptos-historicos')
            ->with('Inscripciones', $Inscripciones) 
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', $Idioma_por_pais)
            //->with('Pais', $Pais)
            ->with('Mensaje_limit', $Mensaje_limit)
            ->with('cant_paginas', $cant_paginas)
            ->with('cant_x_pagina', $cant_x_pagina)
            ->with('Grupos', $Grupos)
            ->with('titulo_planilla', $titulo_planilla)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais))
            ->with('parametros_paginacion', $parametros_paginacion)
            ->with('habilitar_derivacion', false)
            ->with('historico', true);
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }


    public function listInscriptosRecupero($tipo, $id, $cant_dias, $hash)
    {  

        $titulo_planilla = __('Invitar a cursos On-line').' | ';

        if ($tipo == 'pais') {

            $Solicitud = Solicitud::where('pais_id', $id)->whereNotNull('idioma_id')->orderBy('id', 'desc')->first();
            $Tipo = Pais::find($id);
            $tipo_hash = $Tipo->pais;
            $titulo_planilla .= __('Pais').': '.$Tipo->pais;

        }

        if ($tipo_hash == $hash) {   
            $solicitud_id = $Solicitud->id;
            $campania_id = NULL;
            $offset = 0;
            $cant_x_pagina = null;
            $grupo = null;
            $criterio = null;
            $historico = false;
            $recupero = [
                'tipo' => $tipo, 
                'id' => $id, 
                'cant_dias' => $cant_dias
            ];
            $traerInscripciones = $this->traerInscripciones($solicitud_id, $campania_id, $offset, $cant_x_pagina, $grupo, $criterio, $historico, $recupero);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];   
            $Grupos = $traerInscripciones['Grupos'];
            $Grupo_de_solicitud = null;


            $Causas_de_cambio_de_solicitud = Causa_de_cambio_de_solicitud::whereRaw('id = 8')->get();



            $Mensaje_limit = '';
            $cant_paginas = 0;
            $cant_x_pagina = $this->cant_x_pagina;
            //dd($traerInscripciones);
            if (isset($Grupos) and $Grupos['cant_total_inscriptos'] > $cant_x_pagina) { 

                //$Inscripciones = $Inscripciones->reverse(); 
                $cant_paginas = intval($Grupos['cant_total_inscriptos']/$cant_x_pagina);
                $resto = $Grupos['cant_total_inscriptos']-($cant_paginas*$cant_x_pagina);
                if ($Grupos['cant_total_inscriptos']%$cant_x_pagina > 0) {
                    $cant_paginas++;
                }
                else {
                    $resto = $cant_x_pagina;
                }

                $Inscripciones_limit = $Inscripciones->slice(0, $resto);
                $Inscripciones_limit = collect($Inscripciones_limit->all());
                $Inscripciones = $Inscripciones_limit;
                $Mensaje_limit = "Dividimos los Inscriptos en páginas de ".$cant_x_pagina." (total de inscriptos ".$Grupos['cant_total_inscriptos'].").";


            }

            $Idioma_por_pais = $Solicitud->idioma_por_pais();
            $parametros_paginacion = "recupero|$tipo|$id|$cant_dias|$hash";

            //dd($titulo_planilla);
            return View('forms/listar-inscriptos-historicos')
            ->with('Inscripciones', $Inscripciones) 
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', $Idioma_por_pais)
            //->with('Pais', $Pais)
            ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
            ->with('Mensaje_limit', $Mensaje_limit)
            ->with('cant_paginas', $cant_paginas)
            ->with('cant_x_pagina', $cant_x_pagina)
            ->with('Grupos', $Grupos)
            ->with('titulo_planilla', $titulo_planilla)
            ->with('parametros_paginacion', $parametros_paginacion)
            ->with('habilitar_derivacion', true)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais))
            ->with('historico', true);
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }


    public function listInscriptosPaginar($solicitud_id, $hash, $pagina, $parametros_paginacion)
    {  
        $cant_x_pagina = $this->cant_x_pagina;
        $cant_total_inscriptos = Inscripcion::whereRaw("(solicitud_id = $solicitud_id or (solicitud_original = $solicitud_id and causa_de_cambio_de_solicitud_id in (1,4)))")->count();
        $cant_paginas = intval($cant_total_inscriptos/$this->cant_x_pagina);
        $resto = $cant_total_inscriptos-($cant_paginas*$this->cant_x_pagina);
        if ($cant_total_inscriptos%$this->cant_x_pagina > 0) {
            $cant_paginas++;
        }
        
        $habilitar_derivacion = false;

        $titulo_planilla = '';



        if ($pagina == 'all') {
            $cant_x_pagina = 'all';
            $offset = 0;
        }
        else {
            if ($pagina == 1) {
                $cant_x_pagina = $resto;
                $offset = 0;
            }
            else {
                $cant_x_pagina = $this->cant_x_pagina;
                $offset = (($pagina-2)*$cant_x_pagina)+$resto;
                
            }
        }

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash) {

            $recupero = array();            
            $now = new \DateTime();
            $fecha_now = $now->format('Y-m-d H:i:s');
            $Solicitud->ultimo_acceso_planilla_inscripcion = $fecha_now;
            $Solicitud->save();

            
            $campania_id = NULL;
            $grupo = null;
            $criterio = null;

            $parametros_a = explode('|', $parametros_paginacion );

            $titulo_planilla = __('Invitar a cursos On-line').' | ';



            if ($parametros_a[0] == 'historico') {
                $historico = true;
                $titulo_planilla = __('Invitar a Contactos Históricos').' | ';
                if ($Solicitud->localidad_id > 0) {
                     $titulo_planilla .= __('Ciudad').': '.$Solicitud->localidad->localidad; 
                }
                else {
                     $titulo_planilla .= __('Pais').': '.$Solicitud->pais->pais;  
                }                
            }
            else {
                $habilitar_derivacion = true;
                $historico = false;
            }

            if ($parametros_a[0] == 'recupero') {
                $tipo = $parametros_a[1];
                $id = $parametros_a[2];
                $cant_dias = $parametros_a[3];

                if ($tipo == 'pais') {

                    $Tipo = Pais::find($id);
                    $tipo_hash = $Tipo->pais;
                    $titulo_planilla .= __('Pais').': '.$Tipo->pais;

                    $recupero = [
                        'tipo' => $tipo, 
                        'id' => $id, 
                        'cant_dias' => $cant_dias
                    ];

                    
                    $Solicitud = Solicitud::where('pais_id', $id)->whereNotNull('idioma_id')->orderBy('id', 'desc')->first();
                    $Tipo = Pais::find($id);
                    $tipo_hash = $Tipo->pais;
                    $titulo_planilla .= __('Pais').': '.$Tipo->pais;

                }  

            }
            


            $traerInscripciones = $this->traerInscripciones($solicitud_id, $campania_id, $offset, $cant_x_pagina, $grupo, $criterio, $historico, $recupero);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];
            $Grupos = $traerInscripciones['Grupos'];
            $Grupo_de_solicitud = null;



            $Mensaje_limit = '';
            $cant_paginas = 0;
            if ($Grupos['cant_total_inscriptos'] > $cant_x_pagina) {         
                $Mensaje_limit = "Dividimos los Inscriptos en páginas de ".$this->cant_x_pagina." (total de inscriptos ".$Grupos['cant_total_inscriptos'].").";

                $cant_paginas = intval($Grupos['cant_total_inscriptos']/$this->cant_x_pagina);
                if ($Grupos['cant_total_inscriptos']%$this->cant_x_pagina > 0) {
                    $cant_paginas++;
                }
            }

            if (!$historico and count($recupero) == 0) {
                $traerModelosMensajesCurso = $this->traerModelosMensajesCurso($Solicitud, $Inscripciones, $Idioma_por_pais, $Grupo_de_solicitud);

                $Inscripciones = $traerModelosMensajesCurso['Inscripciones'];
                $texto_lecciones_de_curso = $traerModelosMensajesCurso['texto_lecciones_de_curso'];
                $Modelos_grupo = $traerModelosMensajesCurso['Modelos_grupo'];
                $Modelos_alumno = $traerModelosMensajesCurso['Modelos_alumno'];
                $Causas_de_baja = $traerModelosMensajesCurso['Causas_de_baja'];
                $Causas_de_cambio_de_solicitud = $traerModelosMensajesCurso['Causas_de_cambio_de_solicitud'];
                $view = 'forms/listar-inscriptos';
            }
            else {
                $texto_lecciones_de_curso = null;
                $Modelos_grupo = null;
                $Modelos_alumno = null;
                $Causas_de_baja = null;
                $Causas_de_cambio_de_solicitud = Causa_de_cambio_de_solicitud::whereRaw('id = 8')->get();
                $view = 'forms/listar-inscriptos-historicos';
            }

            return View($view)
            ->with('Inscripciones', $Inscripciones) 
            ->with('Fechas_de_evento', $Fechas_de_evento)
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', $Idioma_por_pais)
            //->with('Pais', $Pais)
            ->with('texto_lecciones_de_curso', $texto_lecciones_de_curso)
            ->with('Modelos_grupo', $Modelos_grupo)
            ->with('Modelos_alumno', $Modelos_alumno)
            ->with('Causas_de_baja', $Causas_de_baja)
            ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
            ->with('Mensaje_limit', $Mensaje_limit)
            ->with('cant_paginas', $cant_paginas)
            ->with('pagina_actual', $pagina)
            ->with('cant_x_pagina', $cant_x_pagina)
            ->with('titulo_planilla', $titulo_planilla)
            ->with('parametros_paginacion', $parametros_paginacion)
            ->with('Grupos', $Grupos)
            ->with('habilitar_derivacion', $habilitar_derivacion)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais))
            ->with('historico', $historico);        
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }


    public function listInscriptoslimit($solicitud_id, $hash, $limite)
    {  
       
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash) {   
            
            $now = new \DateTime();
            $fecha_now = $now->format('Y-m-d H:i:s');
            $Solicitud->ultimo_acceso_planilla_inscripcion = $fecha_now;
            $Solicitud->save();

            
            $traerInscripciones = $this->traerInscripciones($solicitud_id);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];
            $Grupos = $traerInscripciones['Grupos'];
            $Grupo_de_solicitud = null;



            $cant = $Inscripciones->count();

            $Inscripciones_limit = $Inscripciones->slice($cant-$limite);
            $Inscripciones_limit = collect($Inscripciones_limit->all()); 
            $traerModelosMensajesCurso = $this->traerModelosMensajesCurso($Solicitud, $Inscripciones_limit, $Idioma_por_pais, $Grupo_de_solicitud);

            $Inscripciones = $traerModelosMensajesCurso['Inscripciones'];
            $texto_lecciones_de_curso = $traerModelosMensajesCurso['texto_lecciones_de_curso'];
            $Modelos_grupo = $traerModelosMensajesCurso['Modelos_grupo'];
            $Modelos_alumno = $traerModelosMensajesCurso['Modelos_alumno'];
            $Causas_de_baja = $traerModelosMensajesCurso['Causas_de_baja'];
            $Causas_de_cambio_de_solicitud = $traerModelosMensajesCurso['Causas_de_cambio_de_solicitud'];


            return View('forms/listar-inscriptos')
            ->with('Inscripciones', $Inscripciones) 
            ->with('Fechas_de_evento', $Fechas_de_evento)
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', $Idioma_por_pais)
            //->with('Pais', $Pais)
            ->with('texto_lecciones_de_curso', $texto_lecciones_de_curso)
            ->with('Modelos_grupo', $Modelos_grupo)
            ->with('Modelos_alumno', $Modelos_alumno)
            ->with('Causas_de_baja', $Causas_de_baja)
            ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
            ->with('Grupos', $Grupos)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais));           
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }



    public function listInscriptosFiltro($filtro, $solicitud_id, $hash)
    {  
       
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash and in_array($filtro, ['todos', 'nuevos', 'sincontactar'])) {   
                
            
            $now = new \DateTime();
            $fecha_now = $now->format('Y-m-d H:i:s');
            $Solicitud->ultimo_acceso_planilla_inscripcion = $fecha_now;
            $Solicitud->save();

            $traerInscripciones = $this->traerInscripciones($solicitud_id);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];
            $Grupos = $traerInscripciones['Grupos'];
            $Grupo_de_solicitud = null;



            if ($filtro == 'nuevos') {
                $Inscripciones_filtro = collect($Inscripciones->whereIn('sino_envio_pedido_de_confirmacion', [NULL])->all());            
            }

            if ($filtro == 'sincontactar') {
                $Inscripciones_filtro = collect($Inscripciones->whereIn('sino_envio_pedido_de_confirmacion', [NULL, 'NO'])->all());            
            }


            $traerModelosMensajesCurso = $this->traerModelosMensajesCurso($Solicitud, $Inscripciones_filtro, $Idioma_por_pais, $Grupo_de_solicitud);


            $Inscripciones = $traerModelosMensajesCurso['Inscripciones'];
            $texto_lecciones_de_curso = $traerModelosMensajesCurso['texto_lecciones_de_curso'];
            $Modelos_grupo = $traerModelosMensajesCurso['Modelos_grupo'];
            $Modelos_alumno = $traerModelosMensajesCurso['Modelos_alumno'];
            $Causas_de_baja = $traerModelosMensajesCurso['Causas_de_baja'];
            $Causas_de_cambio_de_solicitud = $traerModelosMensajesCurso['Causas_de_cambio_de_solicitud'];


            return View('forms/listar-inscriptos')
            ->with('Inscripciones', $Inscripciones) 
            ->with('Fechas_de_evento', $Fechas_de_evento)
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', $Idioma_por_pais)
            //->with('Pais', $Pais)
            ->with('texto_lecciones_de_curso', $texto_lecciones_de_curso)
            ->with('Modelos_grupo', $Modelos_grupo)
            ->with('Modelos_alumno', $Modelos_alumno)
            ->with('Causas_de_baja', $Causas_de_baja)
            ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
            ->with('Grupos', $Grupos)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais));         
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }



    public function listInscriptosGrupo($solicitud_id, $hash, $grupo)
    {  
       
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash) {   
            
            
            $now = new \DateTime();
            $fecha_now = $now->format('Y-m-d H:i:s');
            $Solicitud->ultimo_acceso_planilla_inscripcion = $fecha_now;
            $Solicitud->save();

            $campania_id = NULL;
            $offset = NULL;
            $cant_x_pagina = 999999;
            $traerInscripciones = $this->traerInscripciones($solicitud_id, $campania_id, $offset, $cant_x_pagina, $grupo);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];
            $Grupos = $traerInscripciones['Grupos'];
            $Grupo_de_solicitud = Grupo_de_solicitud::where('solicitud_id', $solicitud_id)->where('nro_de_grupo', $grupo)->get();
            if ($Grupo_de_solicitud->count() > 0) {
                $Grupo_de_solicitud = $Grupo_de_solicitud[0];
            }
            else {
                $Grupo_de_solicitud = null;
            }


            if ($grupo == '0') {
                $Inscripciones_filtro = collect($Inscripciones->whereIn('grupo', [NULL])->all());            
            }
            else {
                $Inscripciones_filtro = collect($Inscripciones->where('grupo', $grupo)->all());            
            }


            $traerModelosMensajesCurso = $this->traerModelosMensajesCurso($Solicitud, $Inscripciones_filtro, $Idioma_por_pais, $Grupo_de_solicitud);

            $Inscripciones = $traerModelosMensajesCurso['Inscripciones'];
            $texto_lecciones_de_curso = $traerModelosMensajesCurso['texto_lecciones_de_curso'];
            $Modelos_grupo = $traerModelosMensajesCurso['Modelos_grupo'];
            $Modelos_alumno = $traerModelosMensajesCurso['Modelos_alumno'];
            $Causas_de_baja = $traerModelosMensajesCurso['Causas_de_baja'];
            $Causas_de_cambio_de_solicitud = $traerModelosMensajesCurso['Causas_de_cambio_de_solicitud'];

            $ocultar_certificados = false;
            if ($Idioma_por_pais->pais_id == 2) {
                $ocultar_certificados = true;
            }


            return View('forms/listar-inscriptos')
            ->with('Inscripciones', $Inscripciones) 
            ->with('Fechas_de_evento', $Fechas_de_evento)
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', $Idioma_por_pais)
            //->with('Pais', $Pais)
            ->with('texto_lecciones_de_curso', $texto_lecciones_de_curso)
            ->with('Modelos_grupo', $Modelos_grupo)
            ->with('Modelos_alumno', $Modelos_alumno)
            ->with('Causas_de_baja', $Causas_de_baja)
            ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
            ->with('Grupos', $Grupos)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais))
            ->with('nro_de_grupo', $grupo)
            ->with('ocultar_certificados', $ocultar_certificados);       
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }
    public function listInscriptosCampania($filtro, $solicitud_id, $campania_id, $hash)
    {  
       
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash and in_array($filtro, ['todos', 'nuevos', 'sincontactar'])) {   
                
            $traerInscripciones = $this->traerInscripciones($solicitud_id);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];
            $Grupos = $traerInscripciones['Grupos'];



            if ($filtro == 'todos') {
                $Inscripciones_campania = $Inscripciones;            
            }

            if ($filtro == 'nuevos') {
                $Inscripciones_campania = collect($Inscripciones->whereIn('sino_envio_pedido_de_confirmacion', [NULL])->all());            
            }

            if ($filtro == 'sincontactar') {
                $Inscripciones_campania = collect($Inscripciones->whereIn('sino_envio_pedido_de_confirmacion', [NULL, 'NO'])->all());            
            }


            $traerModelosMensajesCurso = $this->traerModelosMensajesCurso($Solicitud, $Inscripciones_campania, $Idioma_por_pais);

            $Inscripciones = $traerModelosMensajesCurso['Inscripciones'];
            $texto_lecciones_de_curso = $traerModelosMensajesCurso['texto_lecciones_de_curso'];
            $Modelos_grupo = $traerModelosMensajesCurso['Modelos_grupo'];
            $Modelos_alumno = $traerModelosMensajesCurso['Modelos_alumno'];
            $Causas_de_baja = $traerModelosMensajesCurso['Causas_de_baja'];
            $Causas_de_cambio_de_solicitud = $traerModelosMensajesCurso['Causas_de_cambio_de_solicitud'];




            return View('forms/listar-inscriptos')
            ->with('Inscripciones', $Inscripciones) 
            ->with('Fechas_de_evento', $Fechas_de_evento)
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', $Idioma_por_pais)
            //->with('Pais', $Pais)
            ->with('texto_lecciones_de_curso', $texto_lecciones_de_curso)
            ->with('Modelos_grupo', $Modelos_grupo)
            ->with('Modelos_alumno', $Modelos_alumno)
            ->with('Causas_de_baja', $Causas_de_baja)
            ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
            ->with('Grupos', $Grupos)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais));          
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }    



    public function listInscriptosVariasSolicitudes($solicitudes,$hash)
    {  

        $array_solicitudes = explode(',', $solicitudes);
        $Solicitud = Solicitud::where('id', $array_solicitudes[0])->first();
        if ($Solicitud->hash == $hash) {                       

            $traerInscripciones = $this->traerInscripciones($array_solicitudes);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];
            $Grupos = $traerInscripciones['Grupos'];



            $traerModelosMensajesCurso = $this->traerModelosMensajesCurso($Solicitud, $Inscripciones, $Idioma_por_pais, $Grupos);

            $Inscripciones = $traerModelosMensajesCurso['Inscripciones'];
            $texto_lecciones_de_curso = $traerModelosMensajesCurso['texto_lecciones_de_curso'];
            $Modelos_grupo = $traerModelosMensajesCurso['Modelos_grupo'];
            $Modelos_alumno = $traerModelosMensajesCurso['Modelos_alumno'];
            $Causas_de_baja = $traerModelosMensajesCurso['Causas_de_baja'];
            $Causas_de_cambio_de_solicitud = $traerModelosMensajesCurso['Causas_de_cambio_de_solicitud'];


            return View('forms/listar-inscriptos')
            ->with('Inscripciones', $Inscripciones) 
            ->with('Fechas_de_evento', $Fechas_de_evento)
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', NULL)
            //->with('Pais', $Pais)
            ->with('texto_lecciones_de_curso', $texto_lecciones_de_curso)
            ->with('Modelos_grupo', $Modelos_grupo)
            ->with('Modelos_alumno', $Modelos_alumno)
            ->with('Causas_de_baja', $Causas_de_baja)
            ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
            ->with('Grupos', $Grupos)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais));   
            }
        else {
            echo 'ERROR direccion no valida';
        }  
    }




    public function listInscriptosBusqueda($solicitud_id, $hash)
    {  

       
        if (isset($_POST['criterio'])) {
            $criterio = $_POST['criterio'];
        }
        else {
            if (isset($_GET['criterio'])) {
                $criterio = $_GET['criterio'];
            }
            else {
                $criterio = '318723981723981291231892cn1naxjsdhak3';
            }
        }

        if (isset($_POST['historico'])) {
            $historico = true;
        }
        else {
            $historico = false;    
        }

        $Solicitud = Solicitud::find($solicitud_id);
        if ($Solicitud->hash == $hash) {              


            $campania_id = NULL;
            $offset = NULL;
            $cant_x_pagina = NULL;
            $grupo = NULL;
            $traerInscripciones = $this->traerInscripciones($solicitud_id, $campania_id, $offset, $cant_x_pagina, $grupo, $criterio, $historico);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];   
            $Grupos = $traerInscripciones['Grupos'];
            $Grupo_de_solicitud = null;


            if (!$historico){
                $traerModelosMensajesCurso = $this->traerModelosMensajesCurso($Solicitud, $Inscripciones, $Idioma_por_pais, $Grupo_de_solicitud);
            
                $Inscripciones = $traerModelosMensajesCurso['Inscripciones'];
                $texto_lecciones_de_curso = $traerModelosMensajesCurso['texto_lecciones_de_curso'];
                $Modelos_grupo = $traerModelosMensajesCurso['Modelos_grupo'];
                $Modelos_alumno = $traerModelosMensajesCurso['Modelos_alumno'];
                $Causas_de_baja = $traerModelosMensajesCurso['Causas_de_baja'];
                $Causas_de_cambio_de_solicitud = $traerModelosMensajesCurso['Causas_de_cambio_de_solicitud'];
            }



            $Mensaje_limit = '';
            $cant_paginas = 1;
            $cant_x_pagina = $this->cant_x_pagina;
            
            if ($Grupos['cant_total_inscriptos'] > $cant_x_pagina) { 

                //$Inscripciones = $Inscripciones->reverse(); 
                $cant_paginas = intval($Grupos['cant_total_inscriptos']/$cant_x_pagina);
                $resto = $Grupos['cant_total_inscriptos']-($cant_paginas*$cant_x_pagina);
                if ($Grupos['cant_total_inscriptos']%$cant_x_pagina > 0) {
                    $cant_paginas++;
                }
                else {
                    $resto = $cant_x_pagina;
                }

                $Inscripciones_limit = $Inscripciones->slice(0, $resto);
                $Inscripciones_limit = collect($Inscripciones_limit->all());
                $Inscripciones = $Inscripciones_limit;
                $Mensaje_limit = "Dividimos los Inscriptos en páginas de ".$cant_x_pagina." (total de inscriptos ".$Grupos['cant_total_inscriptos'].").";


            }
            

            if ($criterio == '318723981723981291231892cn1naxjsdhak3') {
                $criterio = null;
            }

            if ($historico) {
                $Idioma_por_pais = $Solicitud->idioma_por_pais();

                $titulo_planilla = __('Invitar a Contactos Históricos').' | ';
                if ($Solicitud->localidad_id > 0) {
                     $titulo_planilla .= __('Ciudad').': '.$Solicitud->localidad->localidad; 
                }
                else {
                     $titulo_planilla .= __('Pais').': '.$Solicitud->pais->pais;  
                }                
                $parametros_paginacion = 'historico';

                return View('forms/listar-inscriptos-historicos')
                ->with('Inscripciones', $Inscripciones) 
                ->with('Solicitud', $Solicitud)
                ->with('Idioma_por_pais', $Idioma_por_pais)
                //->with('Pais', $Pais)
                ->with('Mensaje_limit', $Mensaje_limit)
                ->with('cant_paginas', $cant_paginas)
                ->with('cant_x_pagina', $cant_x_pagina)
                ->with('Grupos', $Grupos)
                ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais))
                ->with('criterio', $criterio)
                ->with('parametros_paginacion', $parametros_paginacion)                
                ->with('titulo_planilla', $titulo_planilla)                
                ->with('habilitar_derivacion', false)
                ->with('historico', true);
            }
            else {
                return View('forms/listar-inscriptos')
                ->with('Inscripciones', $Inscripciones) 
                ->with('Fechas_de_evento', $Fechas_de_evento)
                ->with('Solicitud', $Solicitud)
                ->with('Idioma_por_pais', $Idioma_por_pais)
                //->with('Pais', $Pais)
                ->with('texto_lecciones_de_curso', $texto_lecciones_de_curso)
                ->with('Modelos_grupo', $Modelos_grupo)
                ->with('Modelos_alumno', $Modelos_alumno)
                ->with('Causas_de_baja', $Causas_de_baja)
                ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
                ->with('Mensaje_limit', $Mensaje_limit)
                ->with('cant_paginas', $cant_paginas)
                ->with('cant_x_pagina', $cant_x_pagina)
                ->with('Grupos', $Grupos)
                ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais))
                ->with('criterio', $criterio);

            }
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }



    public function verInscriptoEnPlanilla($solicitud_id, $inscripcion_id, $hash)
    {  

        $criterio = $inscripcion_id;

        $Solicitud = Solicitud::find($solicitud_id);
        if ($Solicitud->hash == $hash) {              


            $campania_id = NULL;
            $offset = NULL;
            $cant_x_pagina = NULL;
            $grupo = NULL;
            $traerInscripciones = $this->traerInscripciones($solicitud_id, $campania_id, $offset, $cant_x_pagina, $grupo, $criterio);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            //$Pais = $traerInscripciones['Pais'];
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];   
            $Grupos = $traerInscripciones['Grupos'];
            $Grupo_de_solicitud = null;


            $traerModelosMensajesCurso = $this->traerModelosMensajesCurso($Solicitud, $Inscripciones, $Idioma_por_pais, $Grupo_de_solicitud);

            
            $Inscripciones = $traerModelosMensajesCurso['Inscripciones'];
            $texto_lecciones_de_curso = $traerModelosMensajesCurso['texto_lecciones_de_curso'];
            $Modelos_grupo = $traerModelosMensajesCurso['Modelos_grupo'];
            $Modelos_alumno = $traerModelosMensajesCurso['Modelos_alumno'];
            $Causas_de_baja = $traerModelosMensajesCurso['Causas_de_baja'];
            $Causas_de_cambio_de_solicitud = $traerModelosMensajesCurso['Causas_de_cambio_de_solicitud'];


            $Mensaje_limit = '';
            $cant_paginas = 1;
            $cant_x_pagina = $this->cant_x_pagina;
            
            if ($Grupos['cant_total_inscriptos'] > $cant_x_pagina) { 

                //$Inscripciones = $Inscripciones->reverse(); 
                $cant_paginas = intval($Grupos['cant_total_inscriptos']/$cant_x_pagina);
                $resto = $Grupos['cant_total_inscriptos']-($cant_paginas*$cant_x_pagina);
                if ($Grupos['cant_total_inscriptos']%$cant_x_pagina > 0) {
                    $cant_paginas++;
                }
                else {
                    $resto = $cant_x_pagina;
                }

                $Inscripciones_limit = $Inscripciones->slice(0, $resto);
                $Inscripciones_limit = collect($Inscripciones_limit->all());
                $Inscripciones = $Inscripciones_limit;
                $Mensaje_limit = "Dividimos los Inscriptos en páginas de ".$cant_x_pagina." (total de inscriptos ".$Grupos['cant_total_inscriptos'].").";


            }
            

            return View('forms/listar-inscriptos')
            ->with('Inscripciones', $Inscripciones) 
            ->with('Fechas_de_evento', $Fechas_de_evento)
            ->with('Solicitud', $Solicitud)
            ->with('Idioma_por_pais', $Idioma_por_pais)
            //->with('Pais', $Pais)
            ->with('texto_lecciones_de_curso', $texto_lecciones_de_curso)
            ->with('Modelos_grupo', $Modelos_grupo)
            ->with('Modelos_alumno', $Modelos_alumno)
            ->with('Causas_de_baja', $Causas_de_baja)
            ->with('Causas_de_cambio_de_solicitud', $Causas_de_cambio_de_solicitud)
            ->with('Mensaje_limit', $Mensaje_limit)
            ->with('cant_paginas', $cant_paginas)
            ->with('cant_x_pagina', $cant_x_pagina)
            ->with('Grupos', $Grupos)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais))
            ->with('criterio', $criterio);
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }



    public function listEnvios($lista_de_envio_id, $hash)
    {  
       
        $Lista_de_envio = DB::table('listas_de_envios as l')
        ->select(DB::Raw('e.nombre, e.titulo_mensaje_1, e.titulo_mensaje_2, e.titulo_mensaje_3, e.titulo_mensaje_4, e.titulo_mensaje_5, e.mensaje_1, e.mensaje_2, e.mensaje_3, e.mensaje_4, e.mensaje_5, l.nombre_de_la_lista, l.hash, l.tipo_de_lista_de_envio_id'))
        ->leftjoin('encabezados_de_envios as e', 'e.id', '=', 'l.encabezado_de_envio_id')
        ->where('l.id', $lista_de_envio_id)
        ->first();



        $ListasController = new ListasController();
        
        
        if ($Lista_de_envio->hash == $hash) {   
            if ($Lista_de_envio->tipo_de_lista_de_envio_id == '' or $Lista_de_envio->tipo_de_lista_de_envio_id == 1) {
                $Contactos = DB::table('instancias_de_envios as i')
                ->select(DB::Raw('i.id, i.contacto_id, i.sino_envio_1, i.sino_envio_2, i.sino_envio_3, i.sino_envio_4, i.sino_envio_5, i.sino_deshabilitar, p.codigo_tel, c.ciudad, c.nombre, c.apellido, c.email, c.telefono, c.observaciones'))
                ->join('contactos as c', 'c.id', '=', 'i.contacto_id')
                ->join('paises as p', 'p.id', '=', 'c.pais_id')
                ->where('i.lista_de_envio_id', $lista_de_envio_id)
                ->whereRaw('(c.sino_deshabilitar IS NULL or c.sino_deshabilitar = "NO")')
                ->get();
            }
            else {
                $Contactos = DB::table('instancias_de_envios as i')
                ->select(DB::Raw('i.id, i.contacto_id, i.sino_envio_1, i.sino_envio_2, i.sino_envio_3, i.sino_envio_4, i.sino_envio_5, i.sino_deshabilitar, p.codigo_tel, c.ciudad, c.nombre, c.apellido, c.email_correo email, c.celular telefono, NULL observaciones'))
                ->join('inscripciones as c', 'c.id', '=', 'i.inscripcion_id')
                ->join('solicitudes as s', 's.id', '=', 'c.solicitud_id')
                ->join('localidades as l', 'l.id', '=', 's.localidad_id')
                ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
                ->join('paises as p', 'p.id', '=', 'pr.pais_id')
                ->where('i.lista_de_envio_id', $lista_de_envio_id)
                ->where('sino_notificar_proximos_eventos', 'SI')
                ->get();
            }

            //dd($Contactos_Historicos[1]->url_whatsapp('kkkk'));
            return View('forms/lista-de-envio')        
            ->with('Lista_de_envio', $Lista_de_envio)
            ->with('Contactos', $Contactos)
            ->with('ListasController', $ListasController);            
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }


    public function planillaAsistencia($solicitud_id, $hash)
    {  
       
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash) {            

            $campania_id = 0;
            $offset = 0;
            $cant_x_pagina = 99999999;
            $grupo = null;
            $traerInscripciones = $this->traerInscripciones($solicitud_id, $campania_id, $offset, $cant_x_pagina, $grupo);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            $Lecciones = Leccion::where('curso_id', $Solicitud->curso_id)->get();

            $Lecciones_extra = DB::table('inscripciones as i')
            ->select(DB::Raw('Distinct lx.id, lx.nro_o_codigo'))
            ->join('asistencias as a', 'i.id', '=', 'a.inscripcion_id')
            ->join('lecciones_extra as lx', 'lx.id', '=', 'a.leccion_extra_id')
            ->where('i.solicitud_id', $solicitud_id)
            ->orderBy('lx.id')
            ->get(); 




            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];   


            return View('forms/planilla-asistencia')        
            ->with('Inscripciones', $Inscripciones)       
            ->with('Fechas_de_evento', $Fechas_de_evento)        
            ->with('Solicitud', $Solicitud)        
            ->with('Lecciones', $Lecciones)       
            ->with('Lecciones_extra', $Lecciones_extra)        
            ->with('Idioma_por_pais', $Idioma_por_pais)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais));            
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }


    public function planillaAsistenciaGrupo($solicitud_id, $hash, $grupo)
    {  
       
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash) {            

            $campania_id = 0;
            $offset = 0;
            $cant_x_pagina = 99999999;
            $traerInscripciones = $this->traerInscripciones($solicitud_id, $campania_id, $offset, $cant_x_pagina, $grupo);

            $Inscripciones = $traerInscripciones['Inscripciones'];
            $Fechas_de_evento = $traerInscripciones['Fechas_de_evento'];
            $Lecciones = Leccion::where('curso_id', $Solicitud->curso_id)->get();
            $Idioma_por_pais = $traerInscripciones['Idioma_por_pais'];   

            $Lecciones_extra = DB::table('inscripciones as i')
            ->select(DB::Raw('Distinct lx.id, lx.nro_o_codigo'))
            ->join('asistencias as a', 'i.id', '=', 'a.inscripcion_id')
            ->join('lecciones_extra as lx', 'lx.id', '=', 'a.leccion_extra_id')
            ->where('i.solicitud_id', $solicitud_id)
            ->orderBy('lx.id')
            ->get(); 

            return View('forms/planilla-asistencia')        
            ->with('Inscripciones', $Inscripciones)       
            ->with('Fechas_de_evento', $Fechas_de_evento)        
            ->with('Solicitud', $Solicitud)        
            ->with('Lecciones', $Lecciones)       
            ->with('Lecciones_extra', $Lecciones_extra)        
            ->with('Idioma_por_pais', $Idioma_por_pais)
            ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais));            
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }



    public function listaInscripcionAExcel($solicitud_id, $hash, $fecha_de_evento_id)
    {  
       
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        if ($Solicitud->hash == $hash) {
            if (substr($fecha_de_evento_id, 0, 1) == 'G') {
                $nro_de_grupo = substr($fecha_de_evento_id, 1, 999999);
                //dd($nro_de_grupo);
                $Inscripciones = Inscripcion::where('solicitud_id', $solicitud_id)->where('grupo', $nro_de_grupo)->get();
            }
            else {
                if ($fecha_de_evento_id == -1) {
                    $Inscripciones = Inscripcion::where('solicitud_id', $solicitud_id)->whereNull('fecha_de_evento_id')->get();
                }
                if ($fecha_de_evento_id == 0) {
                    $Inscripciones = Inscripcion::where('solicitud_id', $solicitud_id)->get();
                }
                if ($fecha_de_evento_id > 0) {
                    $Inscripciones = Inscripcion::where('solicitud_id', $solicitud_id)->where('fecha_de_evento_id', $fecha_de_evento_id)->get();
                }
            }

            $nombre_de_archivo_xls = $Solicitud->hash.'-'.$fecha_de_evento_id;

            //INFO DE METODOS 
            //https://docs.laravel-excel.com/2.1/export/sizing.html

            \Excel::create($nombre_de_archivo_xls, function($excel) use ($Inscripciones, $Solicitud) {
             
                $excel->sheet(__('Inscriptos'), function($sheet) use($Inscripciones, $Solicitud) {

                $ancho = 8;

                if ($Solicitud->tipo_de_evento_id == 3) {
                    $lecciones = Leccion::where('curso_id', $Solicitud->curso_id)->get();
                    $Lecciones_extra = DB::table('inscripciones as i')
                    ->select(DB::Raw('Distinct lx.id, lx.nro_o_codigo'))
                    ->join('asistencias as a', 'i.id', '=', 'a.inscripcion_id')
                    ->join('lecciones_extra as lx', 'lx.id', '=', 'a.leccion_extra_id')
                    ->where('i.solicitud_id', $Solicitud->id)
                    ->orderBy('lx.id')
                    ->get(); 

                    $ancho_presente = 4;

                    $dimension_de_columnas = [
                        'A'     =>  8,
                        'B'     =>  8,
                        'C'     =>  15,
                        'D'     =>  15,
                        'E'     =>  15,
                        'F'     =>  20,
                        'G'     =>  15,
                        'H'     =>  15,
                        'I'     =>  8,
                        'J'     =>  8,
                        'K'     =>  8,
                        'L'     =>  8,
                        'M'     =>  14,
                        'N'     =>  $ancho_presente,
                        'O'     =>  $ancho_presente,
                        'P'     =>  $ancho_presente,
                        'Q'     =>  $ancho_presente,
                        'R'     =>  $ancho_presente,
                        'S'     =>  $ancho_presente,
                        'T'     =>  $ancho_presente,
                        'U'     =>  $ancho_presente,
                        'V'     =>  $ancho_presente,
                        'W'     =>  $ancho_presente,
                        'X'     =>  $ancho_presente,
                        'Y'     =>  $ancho_presente,
                        'Z'     =>  $ancho_presente,
                        'AA'     =>  $ancho_presente,
                        'AB'     =>  $ancho_presente,
                        'AC'     =>  $ancho_presente,
                        'AD'     =>  $ancho_presente,
                        'AE'     =>  $ancho_presente,
                        'AF'     =>  $ancho_presente
                        ];

                    $nombre_de_columnas = [
                        __('ID'), 
                        __('Nro de Orden'), 
                        __('Apellido'), 
                        __('Nombre'), 
                        __('Celular'), 
                        __('Correo'), 
                        __('Ciudad'), 
                        __('Pais'),  
                        __('Contacto'),
                        __('Cancelo'), 
                        __('Grupo de whatsapp'),
                        __('Codigo de alumno'), 
                        ];

                    foreach ($lecciones as $leccion) {
                        array_push($nombre_de_columnas, $leccion->codigo_de_la_leccion);                        
                    }
                    foreach ($Lecciones_extra as $leccion) {
                        array_push($nombre_de_columnas, $leccion->nro_o_codigo);                        
                    }

                }

                if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 2) {
                    $dimension_de_columnas = [
                        'A'     =>  8,
                        'B'     =>  15,
                        'C'     =>  15,
                        'D'     =>  15,
                        'E'     =>  20,
                        'F'     =>  30,
                        'G'     =>  15,
                        'H'     =>  15,
                        'I'     =>  15,
                        'J'     =>  15,
                        'K'     =>  15,
                        'L'     =>  10,
                        'K'     =>  8,
                        'L'     =>  25
                    ];

                    $nombre_de_columnas = [
                        __('ID'), 
                        __('Apellido'), 
                        __('Nombre'), 
                        __('Celular'), 
                        __('Correo'), 
                        __('Horario'), 
                        __('Lugar'), 
                        __('Ciudad'), 
                        __('Pais'), 
                        __('Consulta'), 
                        __('Observaciones'), 
                        __('Confirmado'), 
                        __('Cancelo'), 
                        __('Indique el presente con una P')
                        ];

                }

                if ($Solicitud->tipo_de_evento_id == 4) {
                    $dimension_de_columnas = [
                        'A'     =>  8,
                        'B'     =>  15,
                        'C'     =>  15,
                        'D'     =>  15,
                        'E'     =>  20,
                        'F'     =>  30,
                        'G'     =>  15,
                        'H'     =>  15,
                        'I'     =>  15,
                        'J'     =>  15,
                        'K'     =>  15,
                        'L'     =>  10,
                        'K'     =>  8,
                        'L'     =>  25
                    ];

                    $nombre_de_columnas = [
                        __('ID'), 
                        __('Apellido'), 
                        __('Nombre'), 
                        __('Celular'), 
                        __('Correo'), 
                        __('Horario'), 
                        __('Lugar'), 
                        __('Ciudad'), 
                        __('Pais'), 
                        __('Consulta'), 
                        __('Observaciones'), 
                        __('Confirmado'), 
                        __('Cancelo'), 
                        __('Canal de recepcion del curso')
                        ];

                }


                $sheet->setWidth($dimension_de_columnas);

                $sheet->row(1, $nombre_de_columnas);

                $mensaje_np = __('No puedo asistir a este horario pero quisiera me contacten mas adelante por otros días y horarios');

                foreach ($Inscripciones as $index => $Inscripcion) {

                    if ($Inscripcion->fecha_de_evento_id <> '') {
                      $horario = $Inscripcion->fecha_de_evento->armarDetalleFechasDeEventos('select'); 
                    }
                    else {
                      $horario = $mensaje_np;
                    }

                    if ($Inscripcion->fecha_de_evento_id <> '') {
                      $lugar = $Inscripcion->fecha_de_evento->direccion_de_inicio;
                    }
                    else {
                      $lugar = '';
                    }

                    if ($Inscripcion->pais_id <> '') {
                      $pais = $Inscripcion->pais->pais;
                    }
                    else {
                      $pais = '';
                    }

                    if ($Inscripcion->sino_asistio == 'SI') {
                        $asistio = 'P';
                    }
                    else {
                        $asistio = '';
                    }

                    if ($Solicitud->tipo_de_evento_id == 3) {
                        $linea = [
                            strval($Inscripcion->id), 
                            strval($index+1), 
                            strval($Inscripcion->apellido), 
                            strval($Inscripcion->nombre), 
                            strval($this->telefono($Inscripcion->celular)),
                            strval($Inscripcion->email_correo), 
                            strval($Inscripcion->ciudad),  
                            strval($pais), 
                            strval($Inscripcion->sino_envio_pedido_de_confirmacion), 
                            strval($Inscripcion->sino_cancelo), 
                            strval($Inscripcion->grupo), 
                            strval($Inscripcion->codigo_alumno)
                            ];

                        foreach ($lecciones as $leccion) {
                            $asistencia = $Inscripcion->asistencia->where('leccion_id', $leccion->id)->all();
                            if (count($asistencia) > 0) {
                                $sino_asistencia = 'SI';
                            }
                            else {
                                $sino_asistencia = '';
                            }
                            //dd($Inscripcion->asistencias);
                            array_push($linea, $sino_asistencia);                        
                        }

                        foreach ($Lecciones_extra as $leccion) {
                            $asistencia = $Inscripcion->asistencia->where('leccion_extra_id', $leccion->id)->all();
                            if (count($asistencia) > 0) {
                                $sino_asistencia = 'SI';
                            }
                            else {
                                $sino_asistencia = '';
                            }
                            //dd($Inscripcion->asistencias);
                            array_push($linea, $sino_asistencia);                        
                        }

                        $sheet->row($index+2, $linea); 
                    }

                    if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 2) {
                        $sheet->row($index+2, 
                            [
                            strval($Inscripcion->id), 
                            strval($Inscripcion->apellido), 
                            strval($Inscripcion->nombre), 
                            strval($this->telefono($Inscripcion->celular)), 
                            strval($Inscripcion->email_correo), 
                            strval($horario), 
                            strval($lugar),  
                            strval($Inscripcion->ciudad),  
                            strval($pais), 
                            strval($Inscripcion->consulta), 
                            strval($Inscripcion->observaciones), 
                            strval($Inscripcion->sino_confirmo), 
                            strval($Inscripcion->sino_cancelo), 
                            strval($asistio), 
                            ]
                        ); 
                    }

                    if ($Solicitud->tipo_de_evento_id == 4) {
                        $canal_de_recepcion_del_curso = '';
                        if ($Inscripcion->canal_de_recepcion_del_curso_id > 0) {
                            $canal_de_recepcion_del_curso = $Inscripcion->canal_de_recepcion_del_curso->canal_de_recepcion_del_curso;
                        }
                        $sheet->row($index+2, 
                            [
                            strval($Inscripcion->id), 
                            strval($Inscripcion->apellido), 
                            strval($Inscripcion->nombre), 
                            strval($this->telefono($Inscripcion->celular)), 
                            strval($Inscripcion->email_correo), 
                            strval($horario), 
                            strval($lugar),  
                            strval($Inscripcion->ciudad),  
                            strval($pais), 
                            strval($Inscripcion->consulta), 
                            strval($Inscripcion->observaciones), 
                            strval($Inscripcion->sino_confirmo), 
                            strval($Inscripcion->sino_cancelo), 
                            strval($canal_de_recepcion_del_curso), 
                            ]
                        ); 
                    }
                }
                             
             
                //$sheet->fromArray($users);
             
            });
             
            })->export('xlsx');

            /*            
            return View('forms/inscriptos-a-excel')        
            ->with('Inscripciones', $Inscripciones)      
            ->with('fecha_de_evento_id', $fecha_de_evento_id)            
            ->with('Solicitud', $Solicitud);            
            */
            }
        else {
            echo 'ERROR direccion no valida';
        }  

    }


    public function telefono($numero) {
      $cant = 3;
      $numero = str_replace(array(" ", "-"), array(""), $numero);
      $comienzo = strlen($numero);
      $car_falta = ($comienzo/$cant)-intval($comienzo/$cant);
      $car_falta = $car_falta*$cant;

      for ($i=1; $i <= $car_falta+1; $i++) { 
          $numero = ' '.$numero;
      }
      $resultado = '';
      while($comienzo>=0) {
        $resultado = substr($numero, $comienzo, $cant) . " " . $resultado;
        $comienzo = $comienzo - $cant;
      }

      //dd("$car_falta ($numero) | $resultado");
      return $resultado;
    }


    public function bajaDeAlumno($inscripcion_id, $causa_de_baja_id)
    {
        if ($causa_de_baja_id == 0) {
            $causa_de_baja_id = null;
        }
        $Inscripcion = Inscripcion::find($inscripcion_id);
        $Inscripcion->causa_de_baja_id = $causa_de_baja_id;
        $Inscripcion->save();         

        $mensaje_salida = '✅ Guardado';
        return $mensaje_salida;
    }

    public function guardarCelular($inscripcion_id, $celular)
    {
        $celular = $this->limpiarCadena($celular);
        $Inscripcion = Inscripcion::find($inscripcion_id);
        $Inscripcion->celular = $celular;
        $Inscripcion->save();       

        $mensaje_salida = '✅ Guardado <p style="color: green"><i>(para que los mensajes se actualicen con el nuevo número debe recargar esta planilla)</i></p>';
        return $mensaje_salida;
    }

    public function guardarMensajeAEnviar()
    {
        $solicitud_id = $_POST['solicitud_id'];
        $mensaje_extra = $_POST['mensaje_extra'];
        $mensaje_extra = $this->limpiarCadena($mensaje_extra);

        $Solicitud = Solicitud::find($solicitud_id);
        $Solicitud->envio_de_invitacion_a_contactos_historicos = $mensaje_extra;
        $Solicitud->save();       

        $mensaje_salida = '✅ Mensaje Guardado';
        return $mensaje_salida;
    }

    public function guardarObs($inscripcion_id, $observaciones)
    {
        $Inscripcion = Inscripcion::find($inscripcion_id);
        if ($observaciones == 'XXNADAXX') {
            $observaciones = NULL;
        }
        else {
            $observaciones = $this->limpiarCadena($observaciones);
        }
        $Inscripcion->observaciones = $observaciones;
        $Inscripcion->save();       

        $mensaje_salida = '✅ Guardado';
        return $mensaje_salida;
    }




    public function guardarGrupo($inscripcion_id, $grupo_id)
    {
        $grupo_id = $this->limpiarCadena($grupo_id);
        if ($grupo_id == 'ninguno') {
            $grupo_id = null;
        }
        $Inscripcion = Inscripcion::find($inscripcion_id);
        $Inscripcion->grupo = $grupo_id;
        $Inscripcion->save();       

        $mensaje_salida = '✅ Guardado';
        return $mensaje_salida;
    }

    public function guardarDatosGrupo(Request $request)
    {
        
        $solicitud_id = $_POST['solicitud_id'];
        $grupo_id = $_POST['grupo_id'];
        $nro_de_grupo = $_POST['id'];
        $celular_responsable_de_inscripciones = $_POST['celular_responsable_de_inscripciones'];
        $nombre_responsable_de_inscripciones = $_POST['nombre_responsable_de_inscripciones'];

        if ($grupo_id > 0) {
            $Grupo_de_solicitud = Grupo_de_solicitud::find($grupo_id);
        }
        else {
            $cant = Grupo_de_solicitud::where('nro_de_grupo', $nro_de_grupo)->where('solicitud_id', $solicitud_id)->count();
            if ($cant == 0) {
                $Grupo_de_solicitud = new Grupo_de_solicitud;
            }
            else {
                $Grupo_de_solicitud_search = Grupo_de_solicitud::where('nro_de_grupo', $nro_de_grupo)->where('solicitud_id', $solicitud_id)->get();
                $grupo_id = $Grupo_de_solicitud_search[0]->id;
                $Grupo_de_solicitud = Grupo_de_solicitud::find($grupo_id);
            }
            $Grupo_de_solicitud->solicitud_id = $solicitud_id;
            $Grupo_de_solicitud->nro_de_grupo = $nro_de_grupo;
        }
        $Grupo_de_solicitud->celular_responsable_de_inscripciones = $celular_responsable_de_inscripciones;
        $Grupo_de_solicitud->nombre_responsable_de_inscripciones = $nombre_responsable_de_inscripciones;
        $Grupo_de_solicitud->save();

        $mensaje_salida = '✅ Guardado <p style="color: green; font-size: 14px"><i><strong>para que los mensajes se actualicen con el nuevo número debe recargar esta planilla</strong></i></p>';

        return $mensaje_salida;
    }

    public function setearSino($codigo, $inscripcion_id, $solicitud_id = null)
    {   
        $sino = $_POST['sino'];
        $nombre_de_campo = '';

        if ($codigo == 1) {
            $nombre_de_campo = 'sino_envio_pedido_de_confirmacion';
        }
        if ($codigo == 2) {
            $nombre_de_campo = 'sino_envio_recordatorio_pedido_de_confirmacion';
        }
        if ($codigo == 3) {
            $nombre_de_campo = 'sino_confirmo';
        }
        if ($codigo == 4) {
            $nombre_de_campo = 'sino_envio_voucher';
        }
        if ($codigo == 5) {
            $nombre_de_campo = 'sino_envio_motivacion';
        }
        if ($codigo == 6) {
            $nombre_de_campo = 'sino_envio_recordatorio';
        }
        if ($codigo == 7) {
            $nombre_de_campo = 'sino_contesto_consulta';
        }
        if ($codigo == 8) {
            $nombre_de_campo = 'sino_asistio';
        }
        if ($codigo == 9) {
            $nombre_de_campo = 'sino_envio_recordatorio_proxima_clase';
        }
        if ($codigo == 10) {
            $nombre_de_campo = 'sino_envio_recordatorio_proxima_clase_a_no_asistente';
        }
        if ($codigo == 11) {
            $nombre_de_campo = 'sino_cancelo';
        }
        if ($codigo == 12) {
            $nombre_de_campo = 'sino_invitado_al_curso_online';
        }
        if ($codigo == 13) {
            $nombre_de_campo = 'sino_envio_1';
        }
        if ($codigo == 14) {
            $nombre_de_campo = 'sino_envio_2';
        }
        if ($codigo == 15) {
            $nombre_de_campo = 'sino_envio_3';
        }
        if ($codigo == 16) {
            $nombre_de_campo = 'sino_envio_4';
        }
        if ($codigo == 17) {
            $nombre_de_campo = 'sino_envio_5';
        }
        if ($codigo == 18) {
            $nombre_de_campo = 'sino_envio_6';
        }
        if ($codigo == 19) {
            $nombre_de_campo = 'sino_envio_7';
        }
        if ($codigo == 20) {
            $nombre_de_campo = 'sino_envio_8';
        }
        if ($codigo == 21) {
            $nombre_de_campo = 'sino_envio_9';
        }
        if ($codigo == 22) {
            $nombre_de_campo = 'sino_envio_10';
        }
        if ($codigo == 24) {
            $nombre_de_campo = 'sino_envio_certificado';
        }
        if ($codigo == 27) {
            $nombre_de_campo = 'sino_envio_motivacion_2';
        }
        if ($codigo == 28) {
            $nombre_de_campo = 'sino_envio_motivacion_3';
        }
        if ($codigo == 29) {
            $nombre_de_campo = 'sino_envio_de_encuesta';
        }

        if ($nombre_de_campo <> '') {
            $Inscripcion = Inscripcion::find($inscripcion_id);
            $Inscripcion->$nombre_de_campo = $sino;

            if (($nombre_de_campo == 'sino_envio_voucher' or $nombre_de_campo == 'sino_envio_motivacion') and $sino == 'SI' and ($Inscripcion->sino_confirmo == '' or $Inscripcion->sino_confirmo == 'NO')) {
                $Inscripcion->sino_confirmo = 'SI';
            }
            
            $Inscripcion->save();
        }

        /*
        if ($codigo == 25) {
            if ($sino == 'SI') {
                $cant_envios = Envio::where('inscripcion_id', $inscripcion_id)->where('solicitud_id', $solicitud_id)->count();
                if ($cant_envios == 0) {
                    $Envio = new Envio;
                    $Envio->inscripcion_id = $inscripcion_id;
                    $Envio->codigo_de_envio_id = $codigo;
                    $Envio->solicitud_id = $solicitud_id;
                    $Envio->medio_de_envio_id = 1;
                    $Envio->save();
            }
            else {
                Envio::where('inscripcion_id', $inscripcion_id)->where('solicitud_id', $solicitud_id)->delete();
            }
        }
        */

        if ($codigo == 26) {
            if ($sino == 'SI') {
                $sino_save = 'NO';
            }
            else {
                $sino_save = 'SI';
            }

            $Inscripcion = Inscripcion::find($inscripcion_id);
            $Inscripcion->sino_notificar_proximos_eventos = $sino_save;      
            $Inscripcion->save();      
        }

        if ($codigo == 8) {
            
            if ($sino == 'SI') {
                $email_correo = $Inscripcion->email_correo;
                
                //INICIO MAUTIC ASISTENCIA
                    if (ENV('APP_ENV') <> 'development') {
                        $settings = array(
                            'userName'   => 'fmadoz',             // Create a new user       
                            'password'   => 'fM@d0Z'              // Make it a secure password
                        );

                        // Initiate the auth object specifying to use BasicAuth
                        $initAuth = new ApiAuth();
                        $auth = $initAuth->newAuth($settings, 'BasicAuth');

                        $api = new MauticApi();

                        $contactApi = $api->newApi('contacts', $auth, 'https://forms.gnosis.is');
                        $searchFilter = 'email:'.$email_correo;
                        $contacts = $contactApi->getList($searchFilter);

                        $tags_mautic = array();
                        array_push($tags_mautic, 'ASISTIO');
                        $last_active = date("Y-m-d H:i:s");

                        if ($contacts['total'] <> "0") {
                            $contactId = key($contacts['contacts']);

                            $data = array(
                                'tags' => $tags_mautic,
                                'last_active' => $last_active,
                            );

                            $createIfNotFound = false;

                            $contact = $contactApi->edit($contactId, $data, $createIfNotFound);
                        }
                    }
                //FIN MAUTIC ASISTENCIA
                
            }

        }

        



        return $sino;

    }



    public function setearAsistencia($leccion_id, $inscripcion_id)
    {   
        $sino = $_POST['sino'];

        if ($sino == 'NO') {

            $deletedAsistencias = Asistencia::where('leccion_id', $leccion_id)->where('inscripcion_id', $inscripcion_id)->delete();
        }
        else {
            $Asistencia = new Asistencia;
            $Asistencia->inscripcion_id = $inscripcion_id;
            $Asistencia->leccion_id = $leccion_id;
            if (!Auth::guest()) {
                $Asistencia->user_id = Auth::user()->id;
            }
            $Asistencia->save(); 
        }

        return $sino;

    }


    public function setearSinoSolicitud($codigo, $solicitud_id)
    {   
        $sino = $_POST['sino'];

        if ($codigo == 1) {
            $nombre_de_campo = 'sino_envio_enlaces_a_resp_inscripcion';
        }

        $Solicitud = Solicitud::find($solicitud_id);
        $Solicitud->$nombre_de_campo = $sino;
        $Solicitud->save();

        //var_dump($Solicitud);

    }


    public function registrarEnvio($codigo_de_envio_id, $inscripcion_id, $medio_de_envio_id, $solicitud_id = null)
    {  

        if ($codigo_de_envio_id == 25) {
            $cant_envios = Envio::where('inscripcion_id', $inscripcion_id)->where('solicitud_id', $solicitud_id)->count();
            if ($cant_envios == 0) {
                $Envio = new Envio;
                $Envio->inscripcion_id = $inscripcion_id;
                $Envio->codigo_de_envio_id = $codigo_de_envio_id;
                $Envio->solicitud_id = $solicitud_id;
                $Envio->medio_de_envio_id = 1;
                $Envio->save();
            }
        }
        else {
            $Envio = new Envio;
            $Envio->inscripcion_id = $inscripcion_id;
            $Envio->codigo_de_envio_id = $codigo_de_envio_id;
            $Envio->medio_de_envio_id = $medio_de_envio_id;
            $Envio->save();
        }

        return 'SI';

    }

    public function printVoucher($inscripcion_id, $hash)
    {  
        if (md5(ENV('PREFIJO_HASH').$inscripcion_id) == $hash) {
            $Inscripcion = Inscripcion::find($inscripcion_id);
            $Solicitud = $Inscripcion->Solicitud;
    /*
            $mensaje = 'Vision de Lista';
            $ch = curl_init("https://api.telegram.org/bot".ENV('TELEGRAM_BOT_TOKEN')."/sendMessage?chat_id=632979534&text=".$mensaje);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Configura cURL para devolver el resultado como cadena
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Configura cURL para que no verifique el peer del certificado dado que nuestra URL utiliza el protocolo HTTPS
            $info = curl_exec($ch); // Establece una sesión cURL y asigna la información a la variable $info
            curl_close($ch); // Cierra sesión cURL
    */

            $hash = md5(ENV('PREFIJO_HASH').$inscripcion_id);
            $url_qrcode = $Solicitud->dominioPublico().'f/registrar-asistencia/'.$inscripcion_id.'/'.$hash;
            $dir_imagen = env('PATH_PUBLIC_INTERNO').'qrcode/inscripciones/inscripcion-'.$inscripcion_id.'.png';
            $dir_imagen_url = $Solicitud->dominioPublico().'qrcode/inscripciones/inscripcion-'.$inscripcion_id.'.png';

            QrCode::format('png');
            QrCode::size(200);
            QrCode::generate($url_qrcode, $dir_imagen);


            if ($Solicitud->institucion_id == 2) {
                $imagen_top = $Solicitud->dominioPublico().'img/logo-asoprovida-chico.jpg';
                $css_template = $Solicitud->dominioPublico().'templates/2/css/main-asoprovida.css';
                $bgform = 'bg-asoprovida';
            }
            else  { 
                $imagen_top = $Solicitud->dominioPublico().'img/sol-de-acuario-chico.jpg';
                $css_template = $Solicitud->dominioPublico().'templates/2/css/main.css';
                $bgform = 'bg-gra-02';
            }

            $nombre_institucion = $Solicitud->institucion->institucion;



            return View('forms/voucher')        
            ->with('Inscripcion', $Inscripcion) 
            ->with('imagen_top', $imagen_top) 
            ->with('css_template', $css_template) 
            ->with('bgform', $bgform) 
            ->with('nombre_institucion', $nombre_institucion) 
            ->with('dir_imagen_url', $dir_imagen_url);       
            }
        else {
            echo 'ERROR';
        }  

    }



    public function registrarAsistencia($inscripcion_id, $hash)
    {   

        $registro_asistiencia = 'NO';
        
        if (md5(ENV('PREFIJO_HASH').$inscripcion_id) == $hash) {
            $Inscripcion = Inscripcion::find($inscripcion_id);
            $Inscripcion->sino_asistio = 'SI';
            $Inscripcion->save();

            $email_correo = $Inscripcion->email_correo;

            $Asistencia = new Asistencia;
            $Asistencia->inscripcion_id = $inscripcion_id;
            if (!Auth::guest()) {
                $Asistencia->user_id = Auth::user()->id;
            }
            $Asistencia->save(); 
            $registro_asistiencia = 'SI';

            
            //INICIO MAUTIC ASISTENCIA
                if (ENV('APP_ENV') <> 'development') {
                    $settings = array(
                        'userName'   => 'fmadoz',             // Create a new user       
                        'password'   => 'fM@d0Z'              // Make it a secure password
                    );

                    // Initiate the auth object specifying to use BasicAuth
                    $initAuth = new ApiAuth();
                    $auth = $initAuth->newAuth($settings, 'BasicAuth');

                    $api = new MauticApi();

                    $contactApi = $api->newApi('contacts', $auth, 'https://forms.gnosis.is');
                    $searchFilter = 'email:'.$email_correo;
                    $contacts = $contactApi->getList($searchFilter);

                    $tags_mautic = array();
                    array_push($tags_mautic, 'ASISTIO');
                    $last_active = date("Y-m-d H:i:s");

                    if ($contacts['total'] <> "0") {
                        $contactId = key($contacts['contacts']);

                        $data = array(
                            'tags' => $tags_mautic,
                            'last_active' => $last_active,
                        );

                        $createIfNotFound = false;

                        $contact = $contactApi->edit($contactId, $data, $createIfNotFound);
                    }
                }
            //FIN MAUTIC ASISTENCIA
            

            return View('forms/asistencia-registrada')        
            ->with('Inscripcion', $Inscripcion) ;            
            }
        else {
            echo 'ERROR';
        }  


        if (isset($_POST['sino'])) {
            $sino = $_POST['sino'];

            if ($codigo == 8) {
                $nombre_de_campo = 'sino_asistio';
            }

            $Inscripcion = Inscripcion::find($inscripcion_id);
            $Inscripcion->$nombre_de_campo = $sino;
            $Inscripcion->save();



            return $nombre_de_campo;
        }

    }




    public function confirmarAsistencia($inscripcion_id, $hash)
    {   
        
        if (md5(ENV('PREFIJO_HASH').$inscripcion_id) == $hash) {
            $Inscripcion = Inscripcion::find($inscripcion_id);
            $Inscripcion->sino_confirmo = 'SI';
            $Inscripcion->save();

            return View('forms/asistencia-confirmada')        
            ->with('Inscripcion', $Inscripcion) ;            
            }
        else {
            echo 'ERROR';
        }  

    }

    public function listarTiposDeEventosParaSeleccion()
    {

        $gen_modelo = 'Tipo_de_evento';
        $gen_opcion = 0;
        $acciones_extra = array('Seleccionar,fa fa-hand-pointer-o,Solicitudes/crear/elegir-tipo-de-evento');
        $gen_seteo['filtros_por_campo'] = array('sucursal_id' => Auth::user()->sucursal_id);
        $gen_seteo['gen_url_siguiente'] = 'back';

        $gen_seteo['mostrar_titulo'] = 'NO';
        $gen_seteo['titulo'] = '';
        $gen_seteo['table'] = [
            'searching' => 'false',
            'paging' => 'false',
            'pageLength' => 50
            ];
        
        $gen_campos_a_ocultar = array();

        if ($gen_opcion > 0) {
            $Opcion = Opcion::where('id', $gen_opcion)->get();

            // Traigo los campos a Ocultar
            $campos_a_ocultar_array = explode('|', $Opcion[0]->no_listar_campos);
            foreach ($campos_a_ocultar_array as $campos_a_ocultar) {
                array_push($gen_campos_a_ocultar, $campos_a_ocultar);  
            }

            // Traigo las acciones extra
            $acciones_extra = explode('|', $Opcion[0]->acciones_extra);

        }        
        $GenericController = new GenericController();
        $gen_campos = $GenericController->traer_campos($gen_modelo, $gen_campos_a_ocultar);
        $gen_permisos = [
            'R'
            ];

        $gen_filas = Tipo_de_evento::all();

        //$gen_filas = call_user_func(array($this->dirModel($gen_modelo), 'all'), '*');
        //$gen_fila = call_user_func(array($this->dirModel($gen_modelo), 'find'), $gen_id);    

        $gen_nombre_tb_mostrar = $GenericController->nombreDeTablaAMostrar($gen_modelo);

        return View('genericas/func_list')
        ->with('gen_campos', $gen_campos)
        ->with('gen_modelo', $gen_modelo)
        ->with('gen_filas', $gen_filas)
        ->with('gen_seteo', $gen_seteo)
        ->with('gen_permisos', $gen_permisos)
        ->with('gen_opcion', $gen_opcion)
        ->with('gen_nombre_tb_mostrar', $gen_nombre_tb_mostrar)
        ->with('acciones_extra', $acciones_extra);       
    }

    public function Contact_data($Inscripcion, $modo, $id, $tipo) {

        if ($modo == 'grupo' or $modo == 'pagina') {
            $txtmodo = $modo.$id.'-';
        }
        else {
            $txtmodo = '';    
        }

        $nombre = $Inscripcion->nombre;
        $apellido = $Inscripcion->apellido;
        $descripcion = 'GNID'.$Inscripcion->solicitud_id.' '.$nombre.' '.$apellido.' '.$txtmodo.'-INS'.$Inscripcion->id.' ';
        $celular = $Inscripcion->celular_vCard();
        $email_correo = $Inscripcion->email_correo;
        $contact_group = 'ID'.$Inscripcion->solicitud_id.'-'.$Inscripcion->solicitud->hash;

        if ($tipo == 1) {
            $Contact_data = "BEGIN:VCARD\n";
            $Contact_data .= "VERSION:2.1\n";
            $Contact_data .= "N:$descripcion;;;\n";
            $Contact_data .= "FN:$descripcion\n";
            $Contact_data .= "TEL;CELL:$celular\n";
            $Contact_data .= "EMAIL;PREF:$email_correo\n";
            $Contact_data .= "X-GROUP-MEMBERSHIP:$contact_group\n";
            $Contact_data .= "X-GROUP-MEMBERSHIP:Gnosis Inscripcion\n";
            $Contact_data .= "END:VCARD\n";
        }

        return $Contact_data;

    }


    public function ContactDown($solicitud_id = null, $modo = 'todos', $id = null, $tipo = 1, $cant_x_pagina = null, $hash) {

        if ($modo == 'inscripcion' and $id > 0) {
            $Inscripciones = Inscripcion::where('id', $id)->orderBy('id')->get();
        }

        if ($modo == 'todos') {
            $Inscripciones = Inscripcion::where('solicitud_id', $solicitud_id)->orderBy('id')->get();
        }

        if ($modo == 'grupo') {
            $Inscripciones = Inscripcion::where('solicitud_id', $solicitud_id)->where('grupo', $id)->orderBy('id')->get();
        }

        if ($modo == 'pagina') {
            $cant_total_inscriptos = Inscripcion::where('solicitud_id', $solicitud_id)->count();
            $cant_paginas = intval($cant_total_inscriptos/$cant_x_pagina);
            $resto = $cant_total_inscriptos-($cant_paginas*$cant_x_pagina);
            if ($cant_total_inscriptos%$cant_x_pagina > 0) {
                $cant_paginas++;
            }

            $pagina = $id;
            if ($pagina == 1) {
                $cant_x_pagina = $resto;
                $offset = 0;
            }
            else {
                $cant_x_pagina = $cant_x_pagina;
                $offset = (($pagina-2)*$cant_x_pagina)+$resto;
                
            }

            $offset = (($pagina-2)*$cant_x_pagina)+$resto;
            $Inscripciones = Inscripcion::where('solicitud_id', $solicitud_id)->orderBy('id', 'desc')->offset($offset)->limit($cant_x_pagina)->get();
        }

        $Contact_data = '';

        $i = 0;
        $contador_grupo = 0;
        $grupo_nro = 1;
        foreach ($Inscripciones as $Inscripcion) {
            if ($Inscripcion->sino_cancelo <> "SI" AND $Inscripcion->causa_de_baja_id == '') {
                $Contact_data .= $this->Contact_data($Inscripcion, $modo, $id, $tipo);  
            }              
            //$Contact_data .= '|'.$Inscripcion->id;
        }

        $extra_nombre = '-'.$modo.'-'.$id;
        $archivo_nombre = 'vCard Gn - id '.$solicitud_id.$extra_nombre.'.vcf';  

        $archivo_path = storage_path('app/public/').$archivo_nombre;

        
        $res = Storage::disk('public')->put($archivo_nombre, $Contact_data);
        return response()->download($archivo_path)->deleteFileAfterSend(true);
    }



    public function mautic() {
        //require_once(app_path() . '/Libraries/mauticApi/lib/MauticApi.php');
/*

        // ApiAuth->newAuth() will accept an array of Auth settings
        $settings = array(
            'baseUrl'          => 'http://mkt.engajadospelobem.com.br',       // Base URL of the Mautic instance
            'version'          => 'OAuth2', // Version of the OAuth can be OAuth2 or OAuth1a. OAuth2 is the default value.
            'clientKey'        => '4s264vn96xic0w848wc00og04ksco0wok4g8cw8cwgo0c4k84k',       // Client/Consumer key from Mautic
            'clientSecret'     => 'dfanhdcxxao8c4ogg4ck4os4kc00oskwwoo00sww04c80kkw0',       // Client/Consumer secret key from Mautic
            'callback'         => 'http://localhost:1010/ac/public/'        // Redirect URI/Callback URI for this script
        );

        session_start();


        $initAuth = new ApiAuth;
        $auth = $initAuth->newAuth($settings);
  
        try {
            if ($auth->validateAccessToken()) {
                dd($auth);
                // Obtain the access token returned; call accessTokenUpdated() to catch if the token was updated via a
                // refresh token

                // $accessTokenData will have the following keys:
                // For OAuth1.0a: access_token, access_token_secret, expires
                // For OAuth2: access_token, expires, token_type, refresh_token

                if ($auth->accessTokenUpdated()) {
                    $accessTokenData = $auth->getAccessTokenData();

                    //store access token data however you want
                }
            }
        } catch (Exception $e) {
            dd('error');
            // Do Error handling
        }

*/
        

        // ApiAuth->newAuth() will accept an array of Auth settings
        $settings = array(
            'userName'   => 'fernandomadoz',             // Create a new user       
            'password'   => 'gnosis19'              // Make it a secure password
        );

        // Initiate the auth object specifying to use BasicAuth
        $initAuth = new ApiAuth();
        $auth = $initAuth->newAuth($settings, 'BasicAuth');

        $api = new MauticApi();
        $contactApi = $api->newApi('contacts', $auth, 'http://mkt.engajadospelobem.com.br/api/');

        $id = 759;
        $response = $contactApi->get($id);
        $contact = $response[$contactApi->itemName()];
        $response = $contactApi->getList('', 0, 1);

/*
        $api = new MauticApi();

        $contactApi = $api->newApi('contacts', $auth, 'https://mkt.engajadospelobem.com.br/s/dashboard');   
        $response = $contactApi->getList();
        echo $response['errors'][0]["message"];

*/
        //$MauticApi1 = new MauticApi;  
/*
        $params = array(
            'firstname' => 'Prince',
            'lastname'=> 'Ali Khan',
            'email' => 'princealikhan08@gmail.com'
        );

        Mautic::request('POST','contacts/new',$params);
*/
        //Mautic::request('GET','contacts');
        //Mautic::request('GET','contacts');
        /*
        dd($mau);
       
        $res = Storage::disk('public')->put($archivo_nombre, $Contact_data);
        return response()->download($archivo_path)->deleteFileAfterSend(true);
        */
    }



    public function cambiarDeHorarioAInscripto()
    {
        if (isset($_POST['inscripcion_id_modificar_fecha']) and isset($_POST['fecha_de_evento_id'])) {
            $inscripcion_id = $_POST['inscripcion_id_modificar_fecha'];
            $fecha_de_evento_id = $_POST['fecha_de_evento_id'];

            $Inscripcion = Inscripcion::find($inscripcion_id);
            if ($fecha_de_evento_id <> 'NP' and $fecha_de_evento_id <> 'MO') {
                $Inscripcion->fecha_de_evento_id = $fecha_de_evento_id;
            }
            else {
                $Inscripcion->fecha_de_evento_id = NULL;
            }

            if ($fecha_de_evento_id == 'MO') {
                $Inscripcion->sino_eleccion_modalidad_online = 'SI';
            }
            else {
                $Inscripcion->sino_eleccion_modalidad_online = 'NO';
            }

            $Inscripcion->save();
        }

        return redirect()->back();
        //return redirect()->back()->withErrors([$mensaje['error'], $mensaje['detalle']]);
    }


    public function cambiarDeSolicitudAInscripto()
    {
        if (isset($_POST['inscripcion_id_modificar']) and isset($_POST['solicitud_id_modificar'])) {
            $inscripcion_id = $_POST['inscripcion_id_modificar'];
            $solicitud_id_modificar = $_POST['solicitud_id_modificar'];
            $causa_de_cambio_de_solicitud_id = $_POST['causa_de_cambio_de_solicitud_id'];

            $Inscripcion = Inscripcion::find($inscripcion_id);

            $Cambio = new Cambio_de_solicitud_de_inscripcion;
            $Cambio->inscripcion_id = $Inscripcion->id;
            $Cambio->causa_de_cambio_de_solicitud_id = $causa_de_cambio_de_solicitud_id;            
            $Cambio->solicitud_origen = $Inscripcion->solicitud_id;
            $Cambio->solicitud_destino = $solicitud_id_modificar;
            $Cambio->save(); 

            if ($Inscripcion->solicitud_original == '') {
                $Inscripcion->solicitud_original = $Inscripcion->solicitud_id;
                $Inscripcion->causa_de_cambio_de_solicitud_id = $causa_de_cambio_de_solicitud_id;
            }
            $Inscripcion->solicitud_id = $solicitud_id_modificar;
            $Inscripcion->fecha_de_evento_id = NULL;
            /*
            if ($solicitud_id_modificar == 6870) {
                $Inscripcion->grupo = NULL;
            }
            */
            $Inscripcion->sino_envio_pedido_de_confirmacion = NULL;
            $Inscripcion->sino_confirmo = NULL;
            $Inscripcion->sino_envio_recordatorio_pedido_de_confirmacion = NULL;
            $Inscripcion->sino_envio_voucher = NULL;
            $Inscripcion->sino_envio_motivacion = NULL;
            $Inscripcion->sino_envio_recordatorio = NULL;
            $Inscripcion->sino_asistio = NULL;
            $Inscripcion->sino_contesto_consulta = NULL;
            $Inscripcion->sino_envio_recordatorio_proxima_clase = NULL;
            $Inscripcion->sino_envio_recordatorio_proxima_clase_a_no_asistente = NULL;
            $Inscripcion->sino_cancelo = NULL;



            $Inscripcion->save();


        }

        return redirect()->back();
        //return redirect()->back()->withErrors([$mensaje['error'], $mensaje['detalle']]);
    }




    public function urlRedesEspeciales($solicitud_id) {

        $url_redes = [];

        if ($solicitud_id == 805 or $solicitud_id == 4684) {
            $url_redes = [
                'url_fanpage' => 'https://www.facebook.com/Gnosis.EN',
                'url_sitio_web' => 'https://gnosis.is/en',
                'url_youtube' => 'https://www.youtube.com/channel/UCyKlHMDBp3Bo3hYtcz_9T0A',
                'url_twitter' => 'https://twitter.com/Gnosis_EN',
                'url_instagram' => 'https://www.instagram.com/Gnosis.EN',
                'url_tiktok' => '',
                'url_invitacion_grupo_whatsapp' => 'https://chat.whatsapp.com/IVjaTSe70BxDbLzQq2qfKK',
                'mnemo_face' => 'en_US',
                'nombre_de_la_institucion' => 'Gnostic Culture'
            ];            
        }

        if ($solicitud_id == 815 or $solicitud_id == 6313 or $solicitud_id == 6314) {
            $url_redes = [
                'url_fanpage' => 'https://www.facebook.com/Gnosis.ES',
                'url_sitio_web' => 'https://gnosis.is',
                'url_youtube' => 'https://www.youtube.com/channel/UCBjIsjpN2u9_HQXvCwiaUSQ',
                'url_twitter' => 'https://twitter.com/GnosisInter',
                'url_instagram' => 'https://www.instagram.com/gnosisinter',
                'url_tiktok' => '',
                'url_invitacion_grupo_whatsapp' => '',
                'mnemo_face' => 'en_LA',
                'nombre_de_la_institucion' => 'Cultura Gnóstica'
            ];            
        }


        if ($solicitud_id == 812) {
            $url_redes = [
                'url_fanpage' => 'https://facebook.com/gnosisbrasil',
                'url_sitio_web' => 'https://gnosis.is/pt',
                'url_youtube' => 'https://youtube.com/c/GnosisBrasilTV',
                'url_twitter' => 'https://twitter.com/gnosisbrazil',
                'url_instagram' => 'https://www.instagram.com/gnosisbrasil',
                'url_tiktok' => '',
                'url_invitacion_grupo_whatsapp' => '',
                'mnemo_face' => 'pt_BR',
                'nombre_de_la_institucion' => 'Cultura Gnóstica'
            ];            
        }



        return $url_redes;

    }



    public function notificarFinDeLeccion($leccion_id, $solicitud_id, $hash)
    {   


        $Leccion = Leccion::find($leccion_id);
        $hash_leccion = md5($Leccion->codigo_de_la_leccion);
        
        if ($hash_leccion == $hash) {
            $Solicitud = Solicitud::find($solicitud_id);
            $url_registrar_leccion_finalizada = $Solicitud->dominioPublico()."registrar-fin-de-leccion/$leccion_id/$solicitud_id/$hash";
            return View('forms/notificar-fin-de-leccion')        
            ->with('Solicitud', $Solicitud)
            ->with('Leccion', $Leccion)
            ->with('hash', $hash)
            ->with('url_registrar_leccion_finalizada', $url_registrar_leccion_finalizada);            
            }
        else {
            echo 'ERROR';
        }  

    }




    public function forzarPromocion($inscripcion_id) 
    {
        $Inscripcion = Inscripcion::find($inscripcion_id);
        $forzar = true;
        $this->promocionarInscripto($Inscripcion, $forzar);
        return redirect()->back();
    }

    public function promocionarInscripto($Inscripcion, $forzar = false)
    {   
        $promocionar = false;
        $Solicitud = $Inscripcion->Solicitud;
        $Idioma_por_pais = $Solicitud->idioma_por_pais();
        $pais_id = $Idioma_por_pais->pais_id;

        $leccion_id_fin_camara_basica = $Inscripcion->solicitud->curso->leccion_id_fin_camara_basica;
        if ($leccion_id_fin_camara_basica == '') {
            $leccion_id_fin_camara_basica = 17;
        }

        $Asistencia_Leccion_16 = Asistencia::where('inscripcion_id', $Inscripcion->id)->whereRaw('leccion_id BETWEEN '.($leccion_id_fin_camara_basica-2).' AND '.($leccion_id_fin_camara_basica+6))->count(); 
        //dd($Asistencia_Leccion_16);
        $Asistencias = Asistencia::where('inscripcion_id', $Inscripcion->id)->distinct('leccion_id')->count('leccion_id'); 
        $promedio_asistencia = $Asistencias*100/17;
        $cant_alumno_avanzado = Alumno_avanzado::where('inscripcion_id', $Inscripcion->id)->count();

        //$Evaluacion_final = Evaluacion::where('inscripcion_id', $Inscripcion->id)->where('modelo_de_evaluacion_id', 3)->count(); 
        $Evaluaciones = Evaluacion::where('inscripcion_id', $Inscripcion->id)->distinct('modelo_de_evaluacion_id')->count('modelo_de_evaluacion_id'); 

        if ($promedio_asistencia >= 50 and $Asistencia_Leccion_16 > 0) {                

            if ($cant_alumno_avanzado == 0)  {
                $Alumno_avanzado = new Alumno_avanzado;
                $Alumno_avanzado->inscripcion_id = $Inscripcion->id;
                $Alumno_avanzado->estado_de_seguimiento_id = 3;
                $Alumno_avanzado->cantidad_de_asistencias = $Asistencias;
                $Alumno_avanzado->cantidad_de_evaluaciones = $Evaluaciones;
                $Alumno_avanzado->save();            
            }
            else {
                $Alumno_avanzado = Alumno_avanzado::where('inscripcion_id', $Inscripcion->id)->get();
                if ($Alumno_avanzado[0]->estado_de_seguimiento_id == 1) {
                    $Alumno_avanzado[0]->estado_de_seguimiento_id = 2;
                }
                $Alumno_avanzado[0]->cantidad_de_asistencias = $Asistencias;
                $Alumno_avanzado[0]->cantidad_de_evaluaciones = $Evaluaciones;
                $Alumno_avanzado[0]->save();                         
            }
        }

        if ($Idioma_por_pais->sino_promocionar_inscriptos_a_camara_avanzada == 'SI' and $Solicitud->sino_asignacion_automatica <> 'SI') {

            //if ($promedio_asistencia >= 70 and $Evaluacion_final > 0 and $Asistencia_Leccion_16 > 0) {
            if ($promedio_asistencia >= 50 and $Asistencia_Leccion_16 > 0) {                
                $promocionar = true;   
            }

        }


        if ($promocionar or $forzar) {

            $Solicitudes = DB::table('solicitudes as s')
            ->select(DB::Raw('s.id, s.cupo_maximo, COUNT(distinct i.id) cant_inscriptos'))
            ->leftjoin('inscripciones as i', 's.id', '=', 'i.solicitud_id')
            ->where('s.sino_asignacion_automatica', 'SI')
            ->where('s.pais_id', $pais_id)
            ->whereNotNull('s.fecha_de_solicitud')    
            ->where('s.sino_aprobado_administracion', 'SI')
            ->whereRaw('(s.sino_aprobado_finalizada IS NULL OR s.sino_aprobado_finalizada = "NO") AND (s.sino_cancelada IS NULL OR s.sino_cancelada = "NO")')
            ->whereRaw('(DATEDIFF(NOW(), s.fecha_de_solicitud) < 300)')
            ->groupBy('s.id')
            ->groupBy('s.cupo_maximo')
            ->orderBy('s.id')
            ->get(); 

            //dd($Solicitudes);


            $promocionado = false;
            foreach ($Solicitudes as $Solicitud) {
                if ($Solicitud->cupo_maximo > $Solicitud->cant_inscriptos and !$promocionado ) {

                    $Inscripcion = Inscripcion::find($Inscripcion->id);

                        
                    if ($forzar) {
                        $causa_de_cambio_de_solicitud_id = 4;
                    }
                    else {
                        $causa_de_cambio_de_solicitud_id = 1;    
                    }

                    $Cambio = new Cambio_de_solicitud_de_inscripcion;
                    $Cambio->inscripcion_id = $Inscripcion->id;
                    $Cambio->causa_de_cambio_de_solicitud_id = $causa_de_cambio_de_solicitud_id;            
                    $Cambio->solicitud_origen = $Inscripcion->solicitud_id;
                    $Cambio->solicitud_destino = $Solicitud->id;
                    $Cambio->save(); 

                    if ($Inscripcion->solicitud_original == '') {
                        $Inscripcion->solicitud_original = $Inscripcion->solicitud_id;


                        $Inscripcion->causa_de_cambio_de_solicitud_id = $causa_de_cambio_de_solicitud_id;
                    }
                    $Inscripcion->solicitud_id = $Solicitud->id;
                    $Inscripcion->fecha_de_evento_id = NULL;
                    //$Inscripcion->grupo = NULL;
                    $Inscripcion->causa_de_baja_id = NULL;
                    $Inscripcion->save();

                    $promocionado = true;

                }
            }      
        }
    }


    public function registrarFinDeLeccion(Request $request)
    {   
        $solicitud_id = $_POST['solicitud_id'];
        $leccion_id = $_POST['leccion_id'];
        $hash = $_POST['hash'];
        $password = $_POST['password'];

        $password = str_replace(' ', '', $password);

        $Solicitud = Solicitud::find($solicitud_id);
        $Leccion = Leccion::find($leccion_id);
        $hash_leccion = md5($Leccion->codigo_de_la_leccion);

        $asitencia_registrada = false;
        
        if ($hash_leccion == $hash) {


            $idioma = $Solicitud->idioma->mnemo;
            App::setLocale($idioma);   

            
            if (in_array($solicitud_id, [4421, 4478, 4479, 4480, 4481, 4482,4483, 4484, 4485, 4486,4487,4488,4489, 4490, 4503, 4508, 4509, 4510, 4514, 4518, 4539])) {
                $Inscripciones = Inscripcion::whereRaw('(solicitud_id in (4421, 4478, 4479, 4480, 4481, 4482,4483, 4484, 4485, 4486,4487,4488,4489, 4490, 4503, 4508, 4509, 4510, 4514, 4518, 4539))')->get();    
            }
            else {
                if (in_array($solicitud_id, [4286, 4492, 4493, 4494, 4495, 4496, 4497, 4498, 4499, 4500, 4501, 4502])) {
                    $Inscripciones = Inscripcion::whereRaw('(solicitud_id in (4286, 4492, 4493, 4494, 4495, 4496, 4497, 4498, 4499, 4500, 4501, 4502))')->get();    
                }
                else {
                    $Inscripciones = Inscripcion::whereRaw("(solicitud_id = $solicitud_id or (solicitud_original = $solicitud_id and causa_de_cambio_de_solicitud_id in (1,4)))")->get();

                }
            }
            
            $fcx = new FxC();
            $password = $fcx->limpiarAcentos($password);
            
            foreach ($Inscripciones as $Inscripcion) {

                

                if (strtoupper($Inscripcion->codigo_alumno) == strtoupper($password)) {

                    $asitencia_registrada = true;

                    $Asistencia = new Asistencia;
                    $Asistencia->inscripcion_id = $Inscripcion->id;
                    $Asistencia->leccion_id = $leccion_id;
                    $Asistencia->save(); 

                    $Inscripcion->ultima_leccion_vista = $leccion_id;
                    $Inscripcion->save(); 

                    $this->promocionarInscripto($Inscripcion);


                    
                    //INICIO MAUTIC COMPLETO LECCION
                        if (ENV('APP_ENV') <> 'development') {
                            $settings = array(
                                'userName'   => 'fmadoz',             // Create a new user       
                                'password'   => 'fM@d0Z'              // Make it a secure password
                            );

                            // Initiate the auth object specifying to use BasicAuth
                            $initAuth = new ApiAuth();
                            $auth = $initAuth->newAuth($settings, 'BasicAuth');

                            $api = new MauticApi();

                            $contactApi = $api->newApi('contacts', $auth, 'https://forms.gnosis.is');
                            $searchFilter = 'email:'.$Inscripcion->email_correo;
                            $contacts = $contactApi->getList($searchFilter);

                            $tags_mautic = array();
                            array_push($tags_mautic, 'COMPLETO LECCION id: '.$leccion_id);
                            $last_active = date("Y-m-d H:i:s");

                            if ($contacts['total'] <> "0") {
                                $contactId = key($contacts['contacts']);

                                $data = array(
                                    'tags' => $tags_mautic,
                                    'last_active' => $last_active,
                                );

                                $createIfNotFound = false;

                                $contact = $contactApi->edit($contactId, $data, $createIfNotFound);
                            }
                        }
                    //FIN MAUTIC COMPLETO LECCION

                }
            }

            if ($asitencia_registrada) {
                $titulo = __('Felicitaciones');
                $mensaje = __('Hemos notificado al Tutor que has finalizado la Lección');
                $class_resultado = 'success';
                $class_icon = 'check';
            }
            else {
                $titulo = __('Error');
                $mensaje = __('El código de alumno ingresado no coincide con ningun alumno, puede consultarle el mismo a su tutor o informarle por mensaje que ya ha finalizado la lección').': ';    

                $class_resultado = 'danger';
                $class_icon = 'close';
            }
            
            return View('forms/registrar-fin-de-leccion')        
            ->with('Solicitud', $Solicitud)
            ->with('Leccion', $Leccion)
            ->with('titulo', $titulo)
            ->with('mensaje', $mensaje)
            ->with('class_resultado', $class_resultado)
            ->with('class_icon', $class_icon);            
            }
        else {
            echo 'ERROR';
        }  

    }


    
    public function paisesCodTelJson()
    {     
        
        $paises = Pais::select('pais as countryName', 'mnemo as code', 'codigo_tel as phoneCode')->orderBy('pais')->get();

        $paisesJson = json_encode($paises);
        
        return $paisesJson;
    }
    
    public function traerLeccionesVistas($inscripcion_id, $hash)
    {   
        
        $gCon = new GenericController();

        if (md5(ENV('PREFIJO_HASH').$inscripcion_id) == $hash) {
            $Inscripcion = Inscripcion::find($inscripcion_id);
            $Lecciones = DB::table('asistencias as a')
            ->select(DB::Raw('l.nombre_de_la_leccion, COUNT(DISTINCT a.id) cant_registros, MIN(a.created_at) primera_fecha'))
            ->join('lecciones as l', 'l.id', '=', 'a.leccion_id')
            ->where('a.inscripcion_id', $inscripcion_id)
            ->groupBy('l.nombre_de_la_leccion')
            ->orderBy('l.orden_de_leccion')
            ->get();

            $Lecciones_extra = DB::table('asistencias as a')
            ->select(DB::Raw('l.titulo as nombre_de_la_leccion, COUNT(DISTINCT a.id) cant_registros, MIN(a.created_at) primera_fecha'))
            ->join('lecciones_extra as l', 'l.id', '=', 'a.leccion_extra_id')
            ->where('a.inscripcion_id', $inscripcion_id)
            ->get();

            echo '<h4>'.$Inscripcion->nombre.' '.$Inscripcion->apellido.'</h4><br>';
            foreach ($Lecciones_extra as $Leccion) {
                $fecha_registro = explode(' ', $Leccion->primera_fecha);
                echo $Leccion->nombre_de_la_leccion.' <i style="color: grey"> (Cant asistencias: '.$Leccion->cant_registros.') - '.$gCon->FormatoFecha($fecha_registro[0]).'</i><br>';
            }
            foreach ($Lecciones as $Leccion) {
                $fecha_registro = explode(' ', $Leccion->primera_fecha);
                echo $Leccion->nombre_de_la_leccion.' <i style="color: grey"> (Cant asistencias: '.$Leccion->cant_registros.') - '.$gCon->FormatoFecha($fecha_registro[0]).'</i><br>';
            }

        }
        else {
            echo 'ERROR';
        }  

    }
    
    public function traerTPRealizados($inscripcion_id, $hash)
    {   

        if (md5(ENV('PREFIJO_HASH').$inscripcion_id) == $hash) {
            
            $gCon = new GenericController();

            $Inscripcion = Inscripcion::find($inscripcion_id);
            $TPs = DB::table('evaluaciones as e')
            ->select(DB::Raw('me.titulo_de_la_evaluacion, e.created_at, e.puntuacion, e.nombre_y_apellido, e.texto, me.file_archivo'))
            ->join('modelos_de_evaluacion as me', 'me.id', '=', 'e.modelo_de_evaluacion_id')
            ->where('e.inscripcion_id', $inscripcion_id)
            ->get();

            $html = '<h4>'.$Inscripcion->nombre.' '.$Inscripcion->apellido.'</h4><br>';
            foreach ($TPs as $TP) {
                $html .= '<p>'.$TP->titulo_de_la_evaluacion.' ('.__('Realizado').': '.$gCon->FormatoFecha($TP->created_at).')</p>';
                $html .= '<p><a href="'.env('PATH_PUBLIC').'storage/'.$TP->file_archivo.'" target="_blank">'.__('Ver respuestas correctas').'</a></p>';
                $html .= '<ul>';
                $html .= '<li>'.__('Puntuación').': '.$TP->puntuacion.'</li>';
                $html .= '<li>'.__('Nombre y Apellido').': '.$TP->nombre_y_apellido.'</li>';
                $html .= '<li>'.__('Texto').': '.str_replace('|', '<BR>', $TP->texto).'</li>';
                $html .= '</ul><hr>';
            }

            echo $html;

        }
        else {
            echo 'ERROR';
        }  

    }


    public function listarInscripciones()
    {
        $inscripcion_id = $_POST['inscripcion_id'];
        $codigo_alumno = $_POST['codigo_alumno'];
        $solicitud_id = $_POST['solicitud_id'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $celular = $_POST['celular'];
        $email_correo = $_POST['email_correo'];
        $pais_id_solicitud = $_POST['pais_id_solicitud'];
        $pais_id_inscripcion = $_POST['pais_id_inscripcion'];
        $ciudad = $_POST['ciudad'];
        $localidad_id = $_POST['localidad_id'];
        $idioma_id = $_POST['idioma_id'];

        $whereRaw = '1 = 1';

        if ($inscripcion_id > 0) {
            $whereRaw .= " and inscripciones.id = $inscripcion_id";
        }

        if ($codigo_alumno <> '') {
            $whereRaw .= " and inscripciones.codigo_alumno like '%".$codigo_alumno."%'";
        }

        if ($solicitud_id > 0) {
            $whereRaw .= " and (inscripciones.solicitud_id = $solicitud_id or (inscripciones.solicitud_original = $solicitud_id and inscripciones.causa_de_cambio_de_solicitud_id in (1, 4) ))";
        }

        if ($nombre <> '') {
            $whereRaw .= " and inscripciones.nombre like '%".$nombre."%'";
        }

        if ($apellido <> '') {
            $whereRaw .= " and inscripciones.apellido like '%".$apellido."%'";
        }

        if ($celular <> '') {
            $whereRaw .= " and inscripciones.celular like '%".$celular."%'";
        }

        if ($email_correo <> '') {
            $whereRaw .= " and inscripciones.email_correo like '%".$email_correo."%'";
        }

        if ($pais_id_solicitud <> '') {
            $whereRaw .= " and p2.id = $pais_id_solicitud";
        }

        if ($pais_id_inscripcion <> '') {
            $whereRaw .= " and p.id = $pais_id_inscripcion";
        }

        if ($ciudad <> '') {
            $whereRaw .= " and inscripciones.ciudad like '%".$ciudad."%'";
        }

        if ($localidad_id <> '') {
            $whereRaw .= " and lc.id = $localidad_id";
        }

        if ($idioma_id <> '') {
            $whereRaw .= " and s.idioma_id = $idioma_id";
        }

        //dd($whereRaw);

        //DB::enableQueryLog();
        $Inscripciones = Inscripcion::select(DB::Raw('inscripciones.id, inscripciones.solicitud_id, s.hash, inscripciones.solicitud_original, cc.causa_de_cambio_de_solicitud, inscripciones.apellido, inscripciones.nombre, inscripciones.celular, inscripciones.email_correo, p.pais pais_inscripcion, p2.pais pais_solicitud, inscripciones.ciudad, lc.localidad, inscripciones.created_at, l.nombre_de_la_leccion, inscripciones.sino_cancelo, cb.causa_de_baja, inscripciones.grupo, inscripciones.codigo_alumno, IFNULL(gs.nombre_responsable_de_inscripciones, s.nombre_responsable_de_inscripciones) nombre_responsable_de_inscripciones,  IFNULL(gs.celular_responsable_de_inscripciones, s.celular_responsable_de_inscripciones) celular_responsable_de_inscripciones, i.idioma'))
        ->whereRaw($whereRaw)
        ->leftjoin('fechas_de_evento as f', 'f.id', '=', 'inscripciones.fecha_de_evento_id')
        ->leftjoin('paises as p', 'p.id', '=', 'inscripciones.pais_id')
        ->leftjoin('encuestas_de_satisfaccion as enc', 'enc.inscripcion_id', '=', 'inscripciones.id')
        ->leftjoin('evaluaciones as e', 'e.id', '=', 'inscripciones.ultima_evaluacion')
        ->leftjoin('modelos_de_evaluacion as me', 'me.id', '=', 'e.modelo_de_evaluacion_id')
        ->leftjoin('canales_de_recepcion_del_curso as c', 'c.id', '=', 'inscripciones.canal_de_recepcion_del_curso_id')
        ->leftjoin('solicitudes as s', 's.id', '=', 'inscripciones.solicitud_id')
        ->leftjoin('localidades as lc', 'lc.id', '=', 's.localidad_id')
        ->leftjoin('provincias as pr', 'pr.id', '=', 'lc.provincia_id')
        ->leftjoin('paises as p2', 'p2.id', '=', 'pr.pais_id')
        ->leftjoin('idiomas as i', 'i.id', '=', 's.idioma_id')
        ->leftjoin('causas_de_cambio_de_solicitud as cc', 'cc.id', '=', 'inscripciones.causa_de_cambio_de_solicitud_id')
        ->leftjoin('causas_de_baja as cb', 'cb.id', '=', 'inscripciones.causa_de_baja_id')
        //->leftjoin('grupos_de_solicitud as gs', DB::Raw('gs.nro_de_grupo = inscripciones.grupo and gs.solicitud_id = inscripciones.solicitud_id'), 'and 1=1', 'and 1=1')
        ->leftjoin('grupos_de_solicitud as gs', function ($join) {
            $join->on('gs.nro_de_grupo', '=', 'inscripciones.grupo')->on('gs.solicitud_id', '=', 'inscripciones.solicitud_id');
        })
        ->leftjoin('lecciones as l', 'l.id', '=', DB::raw('(SELECT a.leccion_id FROM asistencias as a JOIN lecciones as l2 ON l2.id = a.leccion_id WHERE a.inscripcion_id = inscripciones.id ORDER BY l2.orden_de_leccion DESC LIMIT 1)'))
        ->orderBy('id', 'desc')
        ->get(); 

        //dd($Inscripciones);
        //dd(DB::getQueryLog());

        return View('reportes/listar-inscripciones-traer')
        ->with('Inscripciones', $Inscripciones);
    }






    public function listarAlumnosAvanzandos()
    {
        $alumno_avanzado_id = $_POST['alumno_avanzado_id'];
        $estado_de_seguimiento_id = $_POST['estado_de_seguimiento_id'];
        $inscripcion_id = $_POST['inscripcion_id'];
        $codigo_alumno = $_POST['codigo_alumno'];
        $solicitud_id = $_POST['solicitud_id'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $celular = $_POST['celular'];
        $email_correo = $_POST['email_correo'];
        $pais_id_solicitud = $_POST['pais_id_solicitud'];
        $pais_id_inscripcion = $_POST['pais_id_inscripcion'];
        $ciudad = $_POST['ciudad'];
        $localidad_id = $_POST['localidad_id'];
        $idioma_id = $_POST['idioma_id'];

        $whereRaw = '1 = 1';

        if ($alumno_avanzado_id > 0) {
            $whereRaw .= " and aa.id = $alumno_avanzado_id";
        }

        if ($estado_de_seguimiento_id > 0) {
            $whereRaw .= " and es.id = $estado_de_seguimiento_id";
        }

        if ($inscripcion_id > 0) {
            $whereRaw .= " and inscripciones.id = $inscripcion_id";
        }

        if ($codigo_alumno <> '') {
            $whereRaw .= " and inscripciones.codigo_alumno like '%".$codigo_alumno."%'";
        }

        if ($solicitud_id > 0) {
            $whereRaw .= " and (inscripciones.solicitud_id = $solicitud_id or (inscripciones.solicitud_original = $solicitud_id and inscripciones.causa_de_cambio_de_solicitud_id in (1, 4) ))";
        }

        if ($nombre <> '') {
            $whereRaw .= " and inscripciones.nombre like '%".$nombre."%'";
        }

        if ($apellido <> '') {
            $whereRaw .= " and inscripciones.apellido like '%".$apellido."%'";
        }

        if ($celular <> '') {
            $whereRaw .= " and inscripciones.celular like '%".$celular."%'";
        }

        if ($email_correo <> '') {
            $whereRaw .= " and inscripciones.email_correo like '%".$email_correo."%'";
        }

        if ($pais_id_solicitud <> '') {
            $whereRaw .= " and p2.id = $pais_id_solicitud";
        }

        if ($pais_id_inscripcion <> '') {
            $whereRaw .= " and p.id = $pais_id_inscripcion";
        }

        if ($ciudad <> '') {
            $whereRaw .= " and inscripciones.ciudad like '%".$ciudad."%'";
        }

        if ($localidad_id <> '') {
            $whereRaw .= " and lc.id = $localidad_id";
        }

        if ($idioma_id <> '') {
            $whereRaw .= " and s.idioma_id = $idioma_id";
        }

        //dd($whereRaw);

        //DB::enableQueryLog();
        $Inscripciones = Inscripcion::select(DB::Raw('aa.id aaid, es.estado_de_seguimiento, aa.cantidad_de_asistencias, aa.cantidad_de_evaluaciones, inscripciones.id iid, inscripciones.solicitud_id, s.hash, inscripciones.solicitud_original, cc.causa_de_cambio_de_solicitud, inscripciones.apellido, inscripciones.nombre, inscripciones.celular, inscripciones.email_correo, p.pais pais_inscripcion, p2.pais pais_solicitud, inscripciones.ciudad, lc.localidad, inscripciones.created_at, l.nombre_de_la_leccion, inscripciones.sino_cancelo, cb.causa_de_baja, inscripciones.grupo, inscripciones.codigo_alumno, IFNULL(gs.nombre_responsable_de_inscripciones, s.nombre_responsable_de_inscripciones) nombre_responsable_de_inscripciones,  IFNULL(gs.celular_responsable_de_inscripciones, s.celular_responsable_de_inscripciones) celular_responsable_de_inscripciones, i.idioma'))
        ->whereRaw($whereRaw)
        ->join('alumnos_avanzados as aa', 'aa.inscripcion_id', '=', 'inscripciones.id')
        ->leftjoin('estados_de_seguimiento as es', 'es.id', '=', 'aa.estado_de_seguimiento_id')
        ->leftjoin('fechas_de_evento as f', 'f.id', '=', 'inscripciones.fecha_de_evento_id')
        ->leftjoin('paises as p', 'p.id', '=', 'inscripciones.pais_id')
        ->leftjoin('encuestas_de_satisfaccion as enc', 'enc.inscripcion_id', '=', 'inscripciones.id')
        ->leftjoin('evaluaciones as e', 'e.id', '=', 'inscripciones.ultima_evaluacion')
        ->leftjoin('modelos_de_evaluacion as me', 'me.id', '=', 'e.modelo_de_evaluacion_id')
        ->leftjoin('canales_de_recepcion_del_curso as c', 'c.id', '=', 'inscripciones.canal_de_recepcion_del_curso_id')
        ->leftjoin('solicitudes as s', 's.id', '=', 'inscripciones.solicitud_id')
        ->leftjoin('localidades as lc', 'lc.id', '=', 's.localidad_id')
        ->leftjoin('provincias as pr', 'pr.id', '=', 'lc.provincia_id')
        ->leftjoin('paises as p2', 'p2.id', '=', 'pr.pais_id')
        ->leftjoin('idiomas as i', 'i.id', '=', 's.idioma_id')
        ->leftjoin('causas_de_cambio_de_solicitud as cc', 'cc.id', '=', 'inscripciones.causa_de_cambio_de_solicitud_id')
        ->leftjoin('causas_de_baja as cb', 'cb.id', '=', 'inscripciones.causa_de_baja_id')
        //->leftjoin('grupos_de_solicitud as gs', DB::Raw('gs.nro_de_grupo = inscripciones.grupo and gs.solicitud_id = inscripciones.solicitud_id'), 'and 1=1', 'and 1=1')
        ->leftjoin('grupos_de_solicitud as gs', function ($join) {
            $join->on('gs.nro_de_grupo', '=', 'inscripciones.grupo')->on('gs.solicitud_id', '=', 'inscripciones.solicitud_id');
        })
        ->leftjoin('lecciones as l', 'l.id', '=', DB::raw('(SELECT a.leccion_id FROM asistencias as a JOIN lecciones as l2 ON l2.id = a.leccion_id WHERE a.inscripcion_id = inscripciones.id ORDER BY l2.orden_de_leccion DESC LIMIT 1)'))
        ->orderBy('aa.id', 'desc')
        ->get(); 

        //dd($Inscripciones);
        //dd(DB::getQueryLog());

        return View('reportes/listar-alumnos-avanzados')
        ->with('Inscripciones', $Inscripciones);
    }






    public function multiPromo()
    {   

        /*

        $Inscripciones = DB::table('inscripciones as i')
        ->select(DB::Raw('DISTINCT i.id'))
        ->join('solicitudes as s', 's.id', '=', 'i.solicitud_id')
        ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
        ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
        ->join('asistencias as a', 'a.inscripcion_id', '=', 'i.id')
        ->where('a.leccion_id', '17')
        ->whereRaw('((p.pais_id = 1 OR s.pais_id = 1) and i.causa_de_cambio_de_solicitud_id IS NULL)')
        ->limit(400)
        ->get(); 

        

        $i=0;

        foreach ($Inscripciones as $Inscrip) {
            $i++;

            $Inscripcion = Inscripcion::find($Inscrip->id);

            echo "<br>$i)   ".$Inscripcion->solicitud_id.'  '.$Inscripcion->id.'    '.$Inscripcion->nombre.'    '.$Inscripcion->apellido;
        
            $promocionar = false;
            $Solicitud = $Inscripcion->Solicitud;
            $Idioma_por_pais = $Solicitud->idioma_por_pais();
            $pais_id = $Idioma_por_pais->pais_id;

            if ($Idioma_por_pais->sino_promocionar_inscriptos_a_camara_avanzada == 'SI' and $Solicitud->sino_asignacion_automatica <> 'SI') {

                $Asistencia_Leccion_16 = Asistencia::where('inscripcion_id', $Inscripcion->id)->where('leccion_id', 17)->count(); 

                $Asistencias = Asistencia::where('inscripcion_id', $Inscripcion->id)->distinct('leccion_id')->count('leccion_id'); 
                $promedio_asistencia = $Asistencias*100/17;

                //$Evaluacion_final = Evaluacion::where('inscripcion_id', $Inscripcion->id)->where('modelo_de_evaluacion_id', 3)->count(); 
                echo "prom: $promedio_asistencia - Eval $Asistencia_Leccion_16";
                //if ($promedio_asistencia >= 70 and $Evaluacion_final > 0 and $Asistencia_Leccion_16 > 0) {
                if ($promedio_asistencia >= 70 and $Asistencia_Leccion_16 > 0) {                
                    $promocionar = true;   
                    echo "| P->SI";
                }

            }

            
            if ($promocionar) {

                $Solicitudes = DB::table('solicitudes as s')
                ->select(DB::Raw('s.id, s.cupo_maximo, COUNT(distinct i.id) cant_inscriptos'))
                ->leftjoin('inscripciones as i', 's.id', '=', 'i.solicitud_id')
                ->where('s.sino_asignacion_automatica', 'SI')
                ->where('s.pais_id', $pais_id)
                ->whereNotNull('s.fecha_de_solicitud')    
                ->where('s.sino_aprobado_administracion', 'SI')
                ->whereRaw('(s.sino_aprobado_finalizada IS NULL OR s.sino_aprobado_finalizada = "NO") AND (s.sino_cancelada IS NULL OR s.sino_cancelada = "NO")')
                ->whereRaw('(DATEDIFF(NOW(), s.fecha_de_solicitud) < 300)')
                ->groupBy('s.id')
                ->groupBy('s.cupo_maximo')
                ->orderBy('s.id')
                ->get(); 

                //dd($Solicitudes);


                $promocionado = false;
                foreach ($Solicitudes as $Solicitud) {
                    
                    $cant_inscriptos = Inscripcion::where('solicitud_id', $Solicitud->id)->count();

                    if ($Solicitud->cupo_maximo > $cant_inscriptos and !$promocionado ) {
                    
                        //dd($Solicitud->id.' '.$cant_inscriptos);

                        $Inscripcion = Inscripcion::find($Inscripcion->id);

                        $Cambio = new Cambio_de_solicitud_de_inscripcion;
                        $Cambio->inscripcion_id = $Inscripcion->id;
                        $Cambio->causa_de_cambio_de_solicitud_id = 1;            
                        $Cambio->solicitud_origen = $Inscripcion->solicitud_id;
                        $Cambio->solicitud_destino = $Solicitud->id;
                        $Cambio->save(); 

                        if ($Inscripcion->solicitud_original == '') {
                            $Inscripcion->solicitud_original = $Inscripcion->solicitud_id;

                            
                            $causa_de_cambio_de_solicitud_id = 1;

                            $Inscripcion->causa_de_cambio_de_solicitud_id = $causa_de_cambio_de_solicitud_id;
                        }
                        $Inscripcion->solicitud_id = $Solicitud->id;
                        $Inscripcion->fecha_de_evento_id = NULL;
                        //$Inscripcion->grupo = NULL;
                        $Inscripcion->save();

                        echo " S->".$Solicitud->id;

                        $promocionado = true;

                    }
                }      
            }
            

        }        
            
        */
    }


    public function generarPdfVMAron($inscripcion_id) {


        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $files = [
            env('PATH_PUBLIC_INTERNO').'/GERHA.pdf',
        ];


        $pdf = new FpdiProtection();

        $password_pdf = $inscripcion_id+255;

        $ownerPassword = $pdf->setProtection([FpdiProtection::PERM_PRINT], $password_pdf, null, 3);
        //var_dump($ownerPassword);

        
        foreach ($files as $file) {
            $pageCount = $pdf->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $id = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($id);

                $pdf->AddPage($size['orientation'], $size);
                $pdf->useTemplate($id);
            }
        }

        $nombre_archivo_pdf = 'GNOSIS-ESCUELA-DE-REGENERACION-HUMANA-V-M-ARON-321'.$inscripcion_id.'.pdf';
        $pdf->Output('F', env('PATH_PUBLIC_INTERNO').'storage/books/'.$nombre_archivo_pdf);


        $generarPdfVMAron['nombre_archivo_pdf'] = $nombre_archivo_pdf;
        $generarPdfVMAron['password_pdf'] = $password_pdf;

        return $generarPdfVMAron;
    }

}

