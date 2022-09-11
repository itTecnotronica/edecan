<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\Fecha_de_evento;
use App\Inscripcion;
use App\Asistencia;
use App\Registro_de_error;
use App\Pais;
use App\Envio;
use App\Evento_en_sitio;
use App\Contacto;
use App\Visualizacion_de_formulario;
use App\Formulario;
use App\Encuesta_de_satisfaccion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GenericController;
use App\Http\Controllers\FormController;

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

//use Mautic\Auth\ApiAuth;

//use App\Notifications\InvoicePaid;
use App\Notifications\TelegramNotification;
use App;

class EncuestaController extends Controller
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



    public function encShow($inscripcion_id, $hash)
    {  
        $sesion_id = Session()->get('sesion_id');

        $Inscripcion = Inscripcion::find($inscripcion_id);
        $Solicitud = $Inscripcion->solicitud;

        $cant_encuestas_hechas = Encuesta_de_satisfaccion::where('inscripcion_id', $inscripcion_id)->count();

        $hash_control = md5($Inscripcion->created_at);



        if ($Solicitud->idioma_id <> '') {
            $idioma = $Solicitud->idioma->mnemo;                        
            App::setLocale($idioma);  
        }
        else {
            if ($Solicitud->idioma_por_pais() <> null) {
                if ($idioma_por_pais->idioma_id <> '') {
                    $idioma = $idioma_por_pais->idioma->mnemo;                        
                    App::setLocale($idioma);  
                }                
            }
        }

        if ($hash_control == $hash) {   

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
                    $titulo = __('CURSO DE AUTO-CONOCIMIENTO').' '.__('ON LINE').'<br><strong>'.$Solicitud->localidad_nombre().'</strong>';  
                }
            }
            else {
                $titulo = $Solicitud->titulo_del_formulario_personalizado;
            }
 
            
            return View('forms/encuesta')        
            ->with('Inscripcion', $Inscripcion)   
            ->with('hash', $hash)
            ->with('titulo', $titulo)                   
            ->with('Solicitud', $Solicitud)             
            ->with('cant_encuestas_hechas', $cant_encuestas_hechas);                          
        }
        else {
            echo 'ERROR! Esta url no es válida';
        }          
        
    }
    
    public function limpiarCadena($cadena) {
        $caracteres_no_admitidos = array("'", '"');
        $cadena_limpia = str_replace($caracteres_no_admitidos, "", $cadena);
        return $cadena_limpia;
        }



    public function RegistrarEncuesta(Request $request) {
        
        $inscripcion_id = $_POST['inscripcion_id'];


        $Inscripcion = Inscripcion::find($inscripcion_id);
        $Solicitud = $Inscripcion->solicitud;

        $idioma = $Solicitud->idioma->mnemo;
        App::setLocale($idioma);    

        $inscripcion_id = $_POST['inscripcion_id'];

        if (isset($_POST['sino_asistio_a_la_conferencia_inicial'])) {
            $sino_asistio_a_la_conferencia_inicial = $_POST['sino_asistio_a_la_conferencia_inicial'];
        }
        else {
            $sino_asistio_a_la_conferencia_inicial = null;
        }

        if (isset($_POST['sino_participo_antes_de_alguna_conferencia_gnostica'])) {
            $sino_participo_antes_de_alguna_conferencia_gnostica = $_POST['sino_participo_antes_de_alguna_conferencia_gnostica'];
        }
        else {
            $sino_participo_antes_de_alguna_conferencia_gnostica = null;
        }


        if (isset($_POST['sino_evento_no_fue_lo_que_esperaba'])) {
            $sino_evento_no_fue_lo_que_esperaba = 'SI';
        }
        else {
            $sino_evento_no_fue_lo_que_esperaba = 'NO';
        }

        $sino_evento_no_fue_lo_que_esperaba = (isset($_POST['sino_evento_no_fue_lo_que_esperaba'])) ? $sino_evento_no_fue_lo_que_esperaba = 'SI' : $sino_evento_no_fue_lo_que_esperaba = 'NO';
        $sino_evento_demasiado_imprecisa = (isset($_POST['sino_evento_demasiado_imprecisa'])) ? $sino_evento_demasiado_imprecisa = 'SI' : $sino_evento_demasiado_imprecisa = 'NO';
        $sino_evento_poco_convincente = (isset($_POST['sino_evento_poco_convincente'])) ? $sino_evento_poco_convincente = 'SI' : $sino_evento_poco_convincente = 'NO';
        $sino_evento_demasiado_extensa = (isset($_POST['sino_evento_demasiado_extensa'])) ? $sino_evento_demasiado_extensa = 'SI' : $sino_evento_demasiado_extensa = 'NO';
        $sino_evento_no_se_cumplio_el_horario = (isset($_POST['sino_evento_no_se_cumplio_el_horario'])) ? $sino_evento_no_se_cumplio_el_horario = 'SI' : $sino_evento_no_se_cumplio_el_horario = 'NO';
        $sino_evento_estuvo_bien = (isset($_POST['sino_evento_estuvo_bien'])) ? $sino_evento_estuvo_bien = 'SI' : $sino_evento_estuvo_bien = 'NO';
        $sino_evento_estuvo_muy_bien = (isset($_POST['sino_evento_estuvo_muy_bien'])) ? $sino_evento_estuvo_muy_bien = 'SI' : $sino_evento_estuvo_muy_bien = 'NO';        
        $sino_evento_fue_clara = (isset($_POST['sino_evento_fue_clara'])) ? $sino_evento_fue_clara = 'SI' : $sino_evento_fue_clara = 'NO';
        $sino_evento_interesante = (isset($_POST['sino_evento_interesante'])) ? $sino_evento_interesante = 'SI' : $sino_evento_interesante = 'NO';
        $sino_comunicacion_fue_satisfactoria = (isset($_POST['sino_comunicacion_fue_satisfactoria'])) ? $sino_comunicacion_fue_satisfactoria = 'SI' : $sino_comunicacion_fue_satisfactoria = 'NO';
        $sino_comunicacion_las_respuestas_demoraban_mucho = (isset($_POST['sino_comunicacion_las_respuestas_demoraban_mucho'])) ? $sino_comunicacion_las_respuestas_demoraban_mucho = 'SI' : $sino_comunicacion_las_respuestas_demoraban_mucho = 'NO';
        $sino_comunicacion_el_trato_fue_ameno_y_cordial = (isset($_POST['sino_comunicacion_el_trato_fue_ameno_y_cordial'])) ? $sino_comunicacion_el_trato_fue_ameno_y_cordial = 'SI' : $sino_comunicacion_el_trato_fue_ameno_y_cordial = 'NO';
        $sino_comunicacion_me_resulto_un_poco_insistente = (isset($_POST['sino_comunicacion_me_resulto_un_poco_insistente'])) ? $sino_comunicacion_me_resulto_un_poco_insistente = 'SI' : $sino_comunicacion_me_resulto_un_poco_insistente = 'NO';
        $sino_comunicacion_me_hubiese_gustado_mas_contenidos = (isset($_POST['sino_comunicacion_me_hubiese_gustado_mas_contenidos'])) ? $sino_comunicacion_me_hubiese_gustado_mas_contenidos = 'SI' : $sino_comunicacion_me_hubiese_gustado_mas_contenidos = 'NO';
        $sino_continuidad_estoy_interesado_en_continuar = (isset($_POST['sino_continuidad_estoy_interesado_en_continuar'])) ? $sino_continuidad_estoy_interesado_en_continuar = 'SI' : $sino_continuidad_estoy_interesado_en_continuar = 'NO';
        $sino_continuidad_recomendaría_este_evento = (isset($_POST['sino_continuidad_recomendaría_este_evento'])) ? $sino_continuidad_recomendaría_este_evento = 'SI' : $sino_continuidad_recomendaría_este_evento = 'NO';
        $sino_continuidad_me_resulto_llamativo_que_sea_gratuito = (isset($_POST['sino_continuidad_me_resulto_llamativo_que_sea_gratuito'])) ? $sino_continuidad_me_resulto_llamativo_que_sea_gratuito = 'SI' : $sino_continuidad_me_resulto_llamativo_que_sea_gratuito = 'NO';
        $sino_continuidad_no_es_lo_que_estoy_buscando = (isset($_POST['sino_continuidad_no_es_lo_que_estoy_buscando'])) ? $sino_continuidad_no_es_lo_que_estoy_buscando = 'SI' : $sino_continuidad_no_es_lo_que_estoy_buscando = 'NO';


        $sugerencias = $this->limpiarCadena($_POST['sugerencias']);

               
        $Encuesta_de_satisfaccion = new Encuesta_de_satisfaccion;
        $Encuesta_de_satisfaccion->inscripcion_id = $inscripcion_id;
        $Encuesta_de_satisfaccion->sino_asistio_a_la_conferencia_inicial = $sino_asistio_a_la_conferencia_inicial;
        $Encuesta_de_satisfaccion->sino_participo_antes_de_alguna_conferencia_gnostica = $sino_participo_antes_de_alguna_conferencia_gnostica;
        $Encuesta_de_satisfaccion->sino_evento_no_fue_lo_que_esperaba = $sino_evento_no_fue_lo_que_esperaba;
        $Encuesta_de_satisfaccion->sino_evento_demasiado_imprecisa = $sino_evento_demasiado_imprecisa;
        $Encuesta_de_satisfaccion->sino_evento_poco_convincente = $sino_evento_poco_convincente;
        $Encuesta_de_satisfaccion->sino_evento_demasiado_extensa = $sino_evento_demasiado_extensa;
        $Encuesta_de_satisfaccion->sino_evento_no_se_cumplio_el_horario = $sino_evento_no_se_cumplio_el_horario;
        $Encuesta_de_satisfaccion->sino_evento_estuvo_bien = $sino_evento_estuvo_bien;
        $Encuesta_de_satisfaccion->sino_evento_estuvo_muy_bien = $sino_evento_estuvo_muy_bien;        
        $Encuesta_de_satisfaccion->sino_evento_fue_clara = $sino_evento_fue_clara;
        $Encuesta_de_satisfaccion->sino_evento_interesante = $sino_evento_interesante;
        $Encuesta_de_satisfaccion->sino_comunicacion_fue_satisfactoria = $sino_comunicacion_fue_satisfactoria;
        $Encuesta_de_satisfaccion->sino_comunicacion_las_respuestas_demoraban_mucho = $sino_comunicacion_las_respuestas_demoraban_mucho;
        $Encuesta_de_satisfaccion->sino_comunicacion_el_trato_fue_ameno_y_cordial = $sino_comunicacion_el_trato_fue_ameno_y_cordial;
        $Encuesta_de_satisfaccion->sino_comunicacion_me_resulto_un_poco_insistente = $sino_comunicacion_me_resulto_un_poco_insistente;
        $Encuesta_de_satisfaccion->sino_comunicacion_me_hubiese_gustado_mas_contenidos = $sino_comunicacion_me_hubiese_gustado_mas_contenidos;
        $Encuesta_de_satisfaccion->sino_continuidad_estoy_interesado_en_continuar = $sino_continuidad_estoy_interesado_en_continuar;
        $Encuesta_de_satisfaccion->sino_continuidad_recomendaría_este_evento = $sino_continuidad_recomendaría_este_evento;
        $Encuesta_de_satisfaccion->sino_continuidad_me_resulto_llamativo_que_sea_gratuito = $sino_continuidad_me_resulto_llamativo_que_sea_gratuito;
        $Encuesta_de_satisfaccion->sino_continuidad_no_es_lo_que_estoy_buscando = $sino_continuidad_no_es_lo_que_estoy_buscando;
        $Encuesta_de_satisfaccion->sugerencias = $sugerencias;
        $Encuesta_de_satisfaccion->save();



        $mensaje_box = '<h4> <i class="icon fa fa-check"> </i> '.__('Muchas gracias por su colaboración').' '.mb_strtoupper($Inscripcion->nombre, 'UTF-8').'</h4>'.__('Encuesta registrada');


        $url_invitacion_grupo_whatsapp = '';
        $url_fanpage = '';
        $url_youtube = '';
        $mnemo_face = '';
        $nombre_de_la_institucion = '';
        

        if ($Solicitud->localidad_id <> '') {
            $url_invitacion_grupo_whatsapp = $Solicitud->localidad->url_invitacion_grupo_whatsapp;
        }


        $FormController = new FormController();
        $url_redes = $FormController->urlRedesEspeciales($Solicitud->id);

        if (count($url_redes) > 0) {
            $url_invitacion_grupo_whatsapp = $url_redes['url_invitacion_grupo_whatsapp'];
            $url_fanpage = $url_redes['url_fanpage'];
            $url_youtube = $url_redes['url_youtube'];
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
                $titulo = __('CURSO DE AUTO-CONOCIMIENTO').' '.__('ON LINE').'<br><strong>'.$Solicitud->localidad_nombre().'</strong>';  
            }
        }
        else {
            $titulo = $Solicitud->titulo_del_formulario_personalizado;
        }

        $no_btn = true;

        $url_form_inscripcion = $Solicitud->url_form_inscripcion();
        $url_invitacion_grupo_facebook = ''; 
        
        $Idioma_por_pais = $Solicitud->idioma_por_pais();

        return View('forms/registracion-ok')          
        ->with('Solicitud', $Solicitud)            
        ->with('titulo', $titulo)          
        ->with('mensaje_box', $mensaje_box)         
        ->with('url_invitacion_grupo_whatsapp', $url_invitacion_grupo_whatsapp)
        ->with('url_fanpage', $url_fanpage)
        ->with('url_youtube', $url_youtube)         
        ->with('mnemo_face', $mnemo_face)
        ->with('url_form_inscripcion', $url_form_inscripcion)
        ->with('nombre_de_la_institucion', $nombre_de_la_institucion)
        ->with('url_invitacion_grupo_facebook', $url_invitacion_grupo_facebook)
        ->with('no_btn', $no_btn)
        ->with('registracion_encuesta', 'SI')
        ->with('dominio_publico', $Solicitud->dominioPublico($Idioma_por_pais));              


    }

    public function traerReporteEncuestaSatisfaccion($tipo, $id, $where_filtros = null) {


        if ($where_filtros == null) {
            //TODO
            if ($tipo == '0') {
                $tipo_where = '1 = 1';
            }

            //SOLICITUD
            if ($tipo == '1') {
                $tipo_where = "i.solicitud_id = $id";
            }

            //FECHA DE EVENTO
            if ($tipo == '2') {
                $tipo_where = "f.id = $id";
            }

            //LOCALIDAD
            if ($tipo == '3') {
                $tipo_where = "s.localidad_id = $id";
            }

            //PROVINCIA
            if ($tipo == '4') {
                $tipo_where = "l.provincia_id = $id";
            }

            //PAIS
            if ($tipo == '5') {
                $tipo_where = "p.pais_id = $id";
            }
        }
        else {
            $tipo_where = $where_filtros;
        }

        $campos_select  = 'SUM(CASE WHEN sino_asistio_a_la_conferencia_inicial = "SI" THEN 1 ELSE 0 END) asistio_si, ';
        $campos_select .= 'SUM(CASE WHEN sino_asistio_a_la_conferencia_inicial = "NO" THEN 1 ELSE 0 END) asistio_no, ';
        $campos_select .= 'SUM(CASE WHEN sino_asistio_a_la_conferencia_inicial IS NULL THEN 1 ELSE 0 END) asistio_nc, ';

        $campos_select .= 'SUM(CASE WHEN sino_participo_antes_de_alguna_conferencia_gnostica = "SI" THEN 1 ELSE 0 END) participo_si, ';
        $campos_select .= 'SUM(CASE WHEN sino_participo_antes_de_alguna_conferencia_gnostica = "NO" THEN 1 ELSE 0 END) participo_no, ';
        $campos_select .= 'SUM(CASE WHEN sino_participo_antes_de_alguna_conferencia_gnostica IS NULL THEN 1 ELSE 0 END) participo_nc, ';

        $campos_select .= 'SUM(CASE WHEN sino_evento_no_fue_lo_que_esperaba = "SI" THEN 1 ELSE 0 END) sino_evento_no_fue_lo_que_esperaba_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_evento_demasiado_imprecisa = "SI" THEN 1 ELSE 0 END) sino_evento_demasiado_imprecisa_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_evento_poco_convincente = "SI" THEN 1 ELSE 0 END) sino_evento_poco_convincente_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_evento_demasiado_extensa = "SI" THEN 1 ELSE 0 END) sino_evento_demasiado_extensa_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_evento_no_se_cumplio_el_horario = "SI" THEN 1 ELSE 0 END) sino_evento_no_se_cumplio_el_horario_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_evento_estuvo_bien = "SI" THEN 1 ELSE 0 END) sino_evento_estuvo_bien_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_evento_estuvo_muy_bien = "SI" THEN 1 ELSE 0 END) sino_evento_estuvo_muy_bien_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_evento_fue_clara = "SI" THEN 1 ELSE 0 END) sino_evento_fue_clara_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_evento_interesante = "SI" THEN 1 ELSE 0 END) sino_evento_interesante_cant, ';

        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_fue_satisfactoria = "SI" THEN 1 ELSE 0 END) sino_comunicacion_fue_satisfactoria_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_las_respuestas_demoraban_mucho = "SI" THEN 1 ELSE 0 END) sino_comunicacion_las_respuestas_demoraban_mucho_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_el_trato_fue_ameno_y_cordial = "SI" THEN 1 ELSE 0 END) sino_comunicacion_el_trato_fue_ameno_y_cordial_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_me_resulto_un_poco_insistente = "SI" THEN 1 ELSE 0 END) sino_comunicacion_me_resulto_un_poco_insistente_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_me_hubiese_gustado_mas_contenidos = "SI" THEN 1 ELSE 0 END) sino_comunicacion_me_hubiese_gustado_mas_contenidos_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_continuidad_estoy_interesado_en_continuar = "SI" THEN 1 ELSE 0 END) sino_continuidad_estoy_interesado_en_continuar_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_continuidad_recomendaría_este_evento = "SI" THEN 1 ELSE 0 END) sino_continuidad_recomendaría_este_evento_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_continuidad_me_resulto_llamativo_que_sea_gratuito = "SI" THEN 1 ELSE 0 END) sino_continuidad_me_resulto_llamativo_que_sea_gratuito_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_continuidad_no_es_lo_que_estoy_buscando = "SI" THEN 1 ELSE 0 END) sino_continuidad_no_es_lo_que_estoy_buscando_cant, ';

        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_fue_satisfactoria = "SI" THEN 1 ELSE 0 END) sino_comunicacion_fue_satisfactoria_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_las_respuestas_demoraban_mucho = "SI" THEN 1 ELSE 0 END) sino_comunicacion_las_respuestas_demoraban_mucho_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_el_trato_fue_ameno_y_cordial = "SI" THEN 1 ELSE 0 END) sino_comunicacion_el_trato_fue_ameno_y_cordial_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_me_resulto_un_poco_insistente = "SI" THEN 1 ELSE 0 END) sino_comunicacion_me_resulto_un_poco_insistente_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_comunicacion_me_hubiese_gustado_mas_contenidos = "SI" THEN 1 ELSE 0 END) sino_comunicacion_me_hubiese_gustado_mas_contenidos_cant, ';

        $campos_select .= 'SUM(CASE WHEN sino_continuidad_estoy_interesado_en_continuar = "SI" THEN 1 ELSE 0 END) sino_continuidad_estoy_interesado_en_continuar_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_continuidad_recomendaría_este_evento = "SI" THEN 1 ELSE 0 END) sino_continuidad_recomendaría_este_evento_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_continuidad_me_resulto_llamativo_que_sea_gratuito = "SI" THEN 1 ELSE 0 END) sino_continuidad_me_resulto_llamativo_que_sea_gratuito_cant, ';
        $campos_select .= 'SUM(CASE WHEN sino_continuidad_no_es_lo_que_estoy_buscando = "SI" THEN 1 ELSE 0 END) sino_continuidad_no_es_lo_que_estoy_buscando_cant, ';


        $campos_select .= 'COUNT(e.id) cant ';

        $Encuesta_cant = DB::table('encuestas_de_satisfaccion as e')
            ->select(DB::Raw($campos_select))
            ->leftjoin('inscripciones as i', 'i.id', '=', 'e.inscripcion_id')
            ->leftjoin('fechas_de_evento as f', 'f.id', '=', 'i.fecha_de_evento_id')
            ->leftjoin('solicitudes as s', 's.id', '=', 'i.solicitud_id')
            ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
            ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
            ->leftjoin('paises as pa', 'pa.id', '=', 'p.pais_id')
            ->whereRaw($tipo_where)
            ->get();


        $Encuestas_detalle = DB::table('encuestas_de_satisfaccion as e')
            ->select(DB::Raw('i.nombre, e.created_at, e.sugerencias, l.localidad, p.provincia, pa.pais, s.id, e.inscripcion_id'))
            ->leftjoin('inscripciones as i', 'i.id', '=', 'e.inscripcion_id')
            ->leftjoin('fechas_de_evento as f', 'f.id', '=', 'i.fecha_de_evento_id')
            ->leftjoin('solicitudes as s', 's.id', '=', 'i.solicitud_id')
            ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
            ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
            ->leftjoin('paises as pa', 'pa.id', '=', 'p.pais_id')
            ->whereRaw($tipo_where)
            ->whereRaw('e.sugerencias <> ""')
            ->get();

        //dd($Encuesta_de_satisfaccion[0]);

        $Encuestas = array($Encuesta_cant[0],  $Encuestas_detalle);

        return $Encuestas;
    }



    public function reporteEncuestaSatisfaccion($tipo, $id) {

        $Encuestas = $this->traerReporteEncuestaSatisfaccion($tipo, $id, null);

        return View('reportes/rep-encuesta-de-satisfaccion')          
        ->with('Encuesta_cant', $Encuestas[0])          
        ->with('Encuestas_detalle', $Encuestas[1]);        

    }




    public function reporteEncuestaSatisfaccionSearch(Request $request) {


        $where_filtros = '';


        if (isset($_POST['provincias'])) {
            $provincias = $_POST['provincias'];
            $where = 'p.id in ('.$provincias[0];            
            foreach ($provincias as $provincia_id) {
                $where .= ', '.$provincia_id;
            }
            $where .= ')';   

            $where_filtros .= $where;   
        }

        if (isset($_POST['localidades'])) {
            $localidades = $_POST['localidades'];
            $where = 'l.id in ('.$localidades[0];            
            foreach ($localidades as $localidad_id) {
                $where .= ', '.$localidad_id;
            }
            $where .= ')'; 

            if ($where_filtros == '') {
                $where_filtros .= $where;
            }
            else {                
                $where_filtros .= ' AND '.$where;
            }
        }

        $tipo_de_evento_id = $_POST['tipo_de_evento_id'];
        if ($tipo_de_evento_id <> '') {

            if ($where_filtros <> '') {                
                $where_filtros .= " AND";
            }
            
            $where_filtros .= " s.tipo_de_evento_id = $tipo_de_evento_id";
        }
        
        $titulo_de_conferencia_publica = $_POST['titulo_de_conferencia_publica'];
        if ($titulo_de_conferencia_publica <> '') {           

            if ($where_filtros <> '') {                
                $where_filtros .= " AND";
            }

            $where_filtros .= " f.titulo_de_conferencia_publica like '%$titulo_de_conferencia_publica%'";
        }

        if ($_POST['periodo'] <> '') {
            $periodo = $_POST['periodo'];
            $periodo = explode('|', $periodo);
            $desde = $periodo[0];
            $hasta = $periodo[1];

            if ($where_filtros <> '') {                
                $where_filtros .= " AND";
            }

            $where_filtros .= " (f.fecha_de_inicio >= '$desde' AND f.fecha_de_inicio <= '$hasta')";
        }

        if ($where_filtros <> '') {                
            $where_filtros .= " AND";
        }

        $where_filtros .= " pa.id = 1";

        $where_filtros = "($where_filtros)";


        $Encuestas = $this->traerReporteEncuestaSatisfaccion(null, null, $where_filtros);

        return View('reportes/rep-encuesta-de-satisfaccion')          
        ->with('Encuesta_cant', $Encuestas[0])          
        ->with('Encuestas_detalle', $Encuestas[1]);        

    }




}

