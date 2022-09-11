<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\Parametro;
use App\Tipo_de_evento;
use App\Reporte;
use App\Localidad;
use App\Fecha_de_evento;
use App\Moneda;
use App\Institucion;
use App\Pais;
use App\Parametros;
use App\Idioma_por_pais;
use App\Visualizacion_de_formulario;
use App\Evento_en_sitio;
use App\Envio;
use App\Asistencia;
use App\Inscripcion;
use App\Pais_por_equipo;
use App\Equipo;
use App\Tipo_de_curso_online;
use App\Cambio_de_solicitud_de_inscripcion;
use App\Canal_de_recepcion_del_curso;
use App\Capacitacion;
use App\Jobs\ColaProgramarCampaniaMautic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GenericController;
use App\Http\Controllers\MauticController;
use App\Http\Controllers\NotificationController;
use \App\Http\Controllers\FxC; 
use Auth;
use Session;
use PDF;
use App;
use Image;
use Igaster\LaravelCities\Geo;

use \Payment\PayPal\PayPalClient;
use \Payment\PayPal\Requests\AuthorizeCheckoutRequest;
use \Payment\PayPal\Requests\DoCheckoutRequest;



class SolicitudController extends Controller
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



    public function index($estado)
    {   
        $user_id = Auth::user()->id;
        //$flag = false;

        $titulo = __('Solicitudes');
        $where_raw_rol_usuario = '';
        $mostrar_x = 'N';

        

        //ADMINISTRADOR
        if (Auth::user()->rol_de_usuario_id == 1) {
            $where_raw_rol_usuario = '1 = 1';
        }
        
        //SUPERVISOR
        if (Auth::user()->rol_de_usuario_id == 2) {

            $paisesDelEquipo = $this->paisesDelEquipo();
            $where_raw_rol_usuario = $paisesDelEquipo['where_raw_rol_usuario'];
            
            //Habilito campañas de prueba para capacitacion
            $users_capacitacion = [28, 73, 50, 71, 65, 290, 74, 152];
            if (in_array(Auth::user()->id, $users_capacitacion)) {
                $where_raw_rol_usuario = "($where_raw_rol_usuario or (s.id in (6,9)))";
            }

            if (in_array(6, $paisesDelEquipo['Paises'])) {
                $mostrar_x = 'S';
            }
        }

        //EJECUTIVO
        if (Auth::user()->rol_de_usuario_id == 3) {
            $where_raw_rol_usuario = 'ejecutivo ='.Auth::user()->id;  
        }


        $Solicitudes_1 = null;
        $Solicitudes_2 = null;
        $where_estado = '0=0';

        $campos_select = 's.id, s.hash, CONCAT(te.tipo_de_evento, " ", IFNULL(s.titulo_del_formulario_personalizado, "")) as tipo_de_evento_fk, IFNULL(CONCAT(l.localidad, ", ", p.provincia, ", ", pa.pais), IFNULL(CONCAT(s.escribe_tu_ciudad_sino_esta_en_la_lista_anterior, ", ", pa2.pais), pa2.pais)) as localidad_fk, id.idioma, e.name as nombre_de_ejecutivo, s.nombre_del_solicitante, COUNT(i.id) inscripciones_cant, s.sino_aprobado_administracion, s.sino_aprobado_solicitar_revision, s.sino_cancelada, s.sino_aprobado_finalizada, u.name';
        $groupBy = 's.id, s.hash, tipo_de_evento_fk, localidad_fk, id.idioma, e.name, s.nombre_del_solicitante, s.sino_aprobado_administracion, s.sino_aprobado_solicitar_revision, s.sino_cancelada, s.sino_aprobado_finalizada, u.name';

        //SUPERVISOR
        if ($where_raw_rol_usuario <> '') {

            
            if($estado == 't') {
                $titulo .= ' ('.__('Todas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL';  
            }
            if($estado == 'p') {
                $titulo .= ' ('.__('Pendientes').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion IS NULL AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';            
            }
            if($estado == 'r') {
                $titulo .= ' ('.__('Revisar').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND (sino_aprobado_administracion = "NO" AND sino_aprobado_solicitar_revision = "SI") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';
            }
            if($estado == 'a') {
                $titulo .= ' ('.__('Aprobadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "SI" AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO") AND DATEDIFF(NOW(), fecha_de_solicitud) <= 60';                
            }
            if($estado == 'v') {
                $titulo .= ' ('.__('Aprobadas').' '.__('Viejas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "SI" AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO") AND DATEDIFF(NOW(), fecha_de_solicitud) > 60';                
            }
            if($estado == 'd') {
                $titulo .= ' ('.__('Desprobadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "NO" and (sino_aprobado_solicitar_revision = "NO" OR sino_aprobado_solicitar_revision IS NULL) AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';
            }
            if($estado == 'c') {
                $titulo .= ' ('.__('Canceladas').')';
                $where_estado = 'sino_cancelada = "SI"';
            }
            if($estado == 'f') {
                $titulo .= ' ('.__('Finalizadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_finalizada = "SI"';                
            }
            if($estado == 'x' and $mostrar_x == 'S') {
                $titulo .= ' ('.__('Pagadas sin enviar').')';
                $where_estado = 'fecha_de_solicitud IS NULL AND (payment_status IS NOT NULL AND payment_status = "authorized")';
            }      


            $Solicitudes_1 = DB::table('solicitudes as s')
                ->select(DB::Raw($campos_select))
                ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
                ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
                ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
                ->leftjoin('paises as pa', 'pa.id', '=', 'p.pais_id')
                ->leftjoin('paises as pa2', 'pa2.id', '=', 's.pais_id')
                ->leftjoin('users as e', 'e.id', '=', 's.ejecutivo')
                ->leftjoin('idiomas as id', 'id.id', '=', 's.idioma_id')
                ->leftjoin('inscripciones as i', 'i.solicitud_id', '=', 's.id')
                ->leftjoin('users as u', 's.user_id', '=', 'u.id')
                ->whereRaw($where_raw_rol_usuario)
                ->whereRaw($where_estado)
                //->whereRaw('(s.tipo_de_evento_id <> 3)')
                ->groupBy(DB::Raw($groupBy))
                ->get();


        }

        //$flag = true;
        //$titulo = __('Mis Solicitudes');

        if($estado == 't') {
            $titulo .= ' ('.__('Todas').')';
            $where_estado = 'user_id = '.$user_id.' AND fecha_de_solicitud IS NOT NULL';
        }
        if($estado == 'p') {
            $titulo .= ' ('.__('Pendientes').')';
            $where_estado = 'user_id = '.$user_id.' AND fecha_de_solicitud IS NOT NULL AND (sino_aprobado_administracion IS NULL or (sino_aprobado_administracion = "NO" AND sino_aprobado_solicitar_revision = "SI")) AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';                
        }
        if($estado == 'r') {
            $titulo .= ' ('.__('Revisar').')';
            $where_estado = 'user_id = '.$user_id.' AND fecha_de_solicitud IS NOT NULL AND (sino_aprobado_administracion = "NO" AND sino_aprobado_solicitar_revision IS NULL) AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';                
        }
        if($estado == 'a') {
            $titulo .= ' ('.__('Aprobadas').')';
            $where_estado = 'user_id = '.$user_id.' AND fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "SI" AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO") AND DATEDIFF(NOW(), fecha_de_solicitud) <= 60';                
        }
        if($estado == 'v') {
            $titulo .= ' ('.__('Aprobadas').')';
            $where_estado = 'user_id = '.$user_id.' AND fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "SI" AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO") AND DATEDIFF(NOW(), fecha_de_solicitud) > 60';                
        }
        if($estado == 'd') {
            $titulo .= ' ('.__('Desprobadas').')';
            $where_estado = 'user_id = '.$user_id.' AND fecha_de_solicitud IS NOT NULL AND (sino_aprobado_solicitar_revision = "NO") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';                
        }
        if($estado == 'c') {
            $titulo .= ' ('.__('Canceladas').')';
            $where_estado = 'user_id = '.$user_id.' AND fecha_de_solicitud IS NOT NULL AND sino_cancelada = "SI"';                
        }
        if($estado == 'f') {
            $titulo .= ' ('.__('Finalizadas').')';
            $where_estado = 'user_id = '.$user_id.' AND fecha_de_solicitud IS NOT NULL AND sino_aprobado_finalizada = "SI"';                
        }

        if($estado == 'x' and $mostrar_x == 'S') {
            $titulo .= ' ('.__('Pagadas sin enviar').')';
            $where_estado = 'user_id = '.$user_id;                
        }


        $Solicitudes_2 = DB::table('solicitudes as s')
            ->select(DB::Raw($campos_select))
            ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
            ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
            ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
            ->leftjoin('paises as pa', 'pa.id', '=', 'p.pais_id')
            ->leftjoin('paises as pa2', 'pa2.id', '=', 's.pais_id')
            ->leftjoin('users as e', 'e.id', '=', 's.ejecutivo')
            ->leftjoin('idiomas as id', 'id.id', '=', 's.idioma_id')
            ->leftjoin('inscripciones as i', 'i.solicitud_id', '=', 's.id')
            ->leftjoin('users as u', 's.user_id', '=', 'u.id')
            ->whereRaw($where_estado)
            //->whereRaw('(s.tipo_de_evento_id <> 3)')
            ->groupBy(DB::Raw($groupBy))
            ->get();

        if ($Solicitudes_1 <> null) {
            if ($Solicitudes_2 <> null) {
                $Solicitudes = $Solicitudes_1->merge($Solicitudes_2);
                $Solicitudes = $Solicitudes->unique()->values()->all();
            }
            else {
                $Solicitudes = $Solicitudes_1;
            }
        }
        else {
            if ($Solicitudes_2 <> null) {
                $Solicitudes = $Solicitudes_2;
            }
            else {
                $Solicitudes = null;
            }
        }

        return View('solicitudes/solicitudes')
        ->with('titulo', $titulo)
        ->with('Solicitudes', $Solicitudes)
        ->with('estado', $estado);
    }


    public function SolicitudesOnline($estado)
    {   
        $Roles = Auth::user()->roles();

        $titulo = __('Formularios');
    
        
        $where_estado = '0=0';

        $campos_select = 's.id, s.hash, CONCAT(te.tipo_de_evento, " ", IFNULL(s.titulo_del_formulario_personalizado, "")) as tipo_de_evento_fk, IFNULL(CONCAT(l.localidad, ", ", p.provincia, ", ", pa.pais), IFNULL(CONCAT(s.escribe_tu_ciudad_sino_esta_en_la_lista_anterior, ", ", pa2.pais), pa2.pais)) as localidad_fk, id.idioma, e.name as nombre_de_ejecutivo, s.nombre_del_solicitante, COUNT(i.id) inscripciones_cant, s.sino_aprobado_administracion, s.sino_aprobado_solicitar_revision, s.sino_cancelada, s.sino_aprobado_finalizada, u.name';
        $groupBy = 's.id, s.hash, tipo_de_evento_fk, localidad_fk, id.idioma, e.name, s.nombre_del_solicitante, s.sino_aprobado_administracion, s.sino_aprobado_solicitar_revision, s.sino_cancelada, s.sino_aprobado_finalizada, u.name';

        //SUPERVISOR
        if(in_array(13, $Roles) or in_array(1, $Roles)) {

            
            if($estado == 't') {
                $titulo .= ' ('.__('Todas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL';  
            }
            if($estado == 'p') {
                $titulo .= ' ('.__('Pendientes').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion IS NULL AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';            
            }
            if($estado == 'r') {
                $titulo .= ' ('.__('Revisar').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND (sino_aprobado_administracion = "NO" AND sino_aprobado_solicitar_revision = "SI") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';
            }
            if($estado == 'a') {
                $titulo .= ' ('.__('Aprobadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "SI" AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO") AND DATEDIFF(NOW(), fecha_de_solicitud) <= 60';                
            }
            if($estado == 'v') {
                $titulo .= ' ('.__('Aprobadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "SI" AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO") AND DATEDIFF(NOW(), fecha_de_solicitud) > 60';                
            }             
            if($estado == 'd') {
                $titulo .= ' ('.__('Desprobadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "NO" and (sino_aprobado_solicitar_revision = "NO" OR sino_aprobado_solicitar_revision IS NULL) AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';
            }
            if($estado == 'c') {
                $titulo .= ' ('.__('Canceladas').')';
                $where_estado = 'sino_cancelada = "SI"';
            }
            if($estado == 'f') {
                $titulo .= ' ('.__('Finalizadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_finalizada = "SI"';                
            }
            if($estado == 'x' and $mostrar_x == 'S') {
                $titulo .= ' ('.__('Pagadas sin enviar').')';
                $where_estado = 'fecha_de_solicitud IS NULL AND (payment_status IS NOT NULL AND payment_status = "authorized")';
            }      


            $Solicitudes_1 = DB::table('solicitudes as s')
                ->select(DB::Raw($campos_select))
                ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
                ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
                ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
                ->leftjoin('paises as pa', 'pa.id', '=', 'p.pais_id')
                ->leftjoin('paises as pa2', 'pa2.id', '=', 's.pais_id')
                ->leftjoin('users as e', 'e.id', '=', 's.ejecutivo')
                ->leftjoin('idiomas as id', 'id.id', '=', 's.idioma_id')
                ->leftjoin('inscripciones as i', 'i.solicitud_id', '=', 's.id')
                ->leftjoin('users as u', 's.user_id', '=', 'u.id')
                ->whereRaw($where_estado)
                ->whereRaw('(s.tipo_de_evento_id = 3)')
                ->groupBy(DB::Raw($groupBy))
                ->get();

        }

        return View('solicitudes/solicitudes')
        ->with('titulo', $titulo)
        ->with('Solicitudes', $Solicitudes_1)
        ->with('estado', $estado);
    }


    public function SolicitudesRecoleccionDatos($estado)
    {   
        $Roles = Auth::user()->roles();

        $titulo = __('Formularios');
    
        
        $where_estado = '0=0';

        $campos_select = 's.id, s.hash, CONCAT(te.tipo_de_evento, " ", IFNULL(s.titulo_del_formulario_personalizado, "")) as tipo_de_evento_fk, IFNULL(CONCAT(l.localidad, ", ", p.provincia, ", ", pa.pais), IFNULL(CONCAT(s.escribe_tu_ciudad_sino_esta_en_la_lista_anterior, ", ", pa2.pais), pa2.pais)) as localidad_fk, id.idioma, e.name as nombre_de_ejecutivo, s.nombre_del_solicitante, COUNT(i.id) inscripciones_cant, s.sino_aprobado_administracion, s.sino_aprobado_solicitar_revision, s.sino_cancelada, s.sino_aprobado_finalizada, u.name';
        $groupBy = 's.id, s.hash, tipo_de_evento_fk, localidad_fk, id.idioma, e.name, s.nombre_del_solicitante, s.sino_aprobado_administracion, s.sino_aprobado_solicitar_revision, s.sino_cancelada, s.sino_aprobado_finalizada, u.name';

        //SUPERVISOR
        if(in_array(13, $Roles) or in_array(1, $Roles)) {

            
            if($estado == 't') {
                $titulo .= ' ('.__('Todas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL';  
            }
            if($estado == 'p') {
                $titulo .= ' ('.__('Pendientes').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion IS NULL AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';            
            }
            if($estado == 'r') {
                $titulo .= ' ('.__('Revisar').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND (sino_aprobado_administracion = "NO" AND sino_aprobado_solicitar_revision = "SI") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';
            }
            if($estado == 'a') {
                $titulo .= ' ('.__('Aprobadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "SI" AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO") AND DATEDIFF(NOW(), fecha_de_solicitud) <= 60';                
            }
            if($estado == 'v') {
                $titulo .= ' ('.__('Aprobadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "SI" AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO") AND DATEDIFF(NOW(), fecha_de_solicitud) > 60';                
            }             
            if($estado == 'd') {
                $titulo .= ' ('.__('Desprobadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_administracion = "NO" and (sino_aprobado_solicitar_revision = "NO" OR sino_aprobado_solicitar_revision IS NULL) AND (sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")';
            }
            if($estado == 'c') {
                $titulo .= ' ('.__('Canceladas').')';
                $where_estado = 'sino_cancelada = "SI"';
            }
            if($estado == 'f') {
                $titulo .= ' ('.__('Finalizadas').')';
                $where_estado = 'fecha_de_solicitud IS NOT NULL AND sino_aprobado_finalizada = "SI"';                
            }
            if($estado == 'x' and $mostrar_x == 'S') {
                $titulo .= ' ('.__('Pagadas sin enviar').')';
                $where_estado = 'fecha_de_solicitud IS NULL AND (payment_status IS NOT NULL AND payment_status = "authorized")';
            }      


            $Solicitudes_1 = DB::table('solicitudes as s')
                ->select(DB::Raw($campos_select))
                ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
                ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
                ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
                ->leftjoin('paises as pa', 'pa.id', '=', 'p.pais_id')
                ->leftjoin('paises as pa2', 'pa2.id', '=', 's.pais_id')
                ->leftjoin('users as e', 'e.id', '=', 's.ejecutivo')
                ->leftjoin('idiomas as id', 'id.id', '=', 's.idioma_id')
                ->leftjoin('inscripciones as i', 'i.solicitud_id', '=', 's.id')
                ->leftjoin('users as u', 's.user_id', '=', 'u.id')
                ->whereRaw($where_estado)
                ->whereRaw('(s.tipo_de_evento_id = 4)')
                ->groupBy(DB::Raw($groupBy))
                ->get();

        }

        return View('solicitudes/solicitudes')
        ->with('titulo', $titulo)
        ->with('Solicitudes', $Solicitudes_1)
        ->with('estado', $estado);
    }



    public function valoresParaSelectEx($filas, $nombre_del_campo)
    {
        $valores = '';
        foreach ($filas as $fila) { 
            $valores .= '{ id: '.$fila['id'].', name: "'.$fila[$nombre_del_campo].'" }, ';
        }

        return $valores;

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

    public function crearSolicitudElegirTipoDeEvento($tipo_de_evento_id)
    {   
        //Session::put('cliente_id', $cliente_id);

        $Solicitud = new Solicitud;
        $Solicitud->tipo_de_evento_id = $tipo_de_evento_id;
        $Solicitud->user_id = Auth::user()->id;
        $Solicitud->save();


        $Localidades = Localidad::get();
        $valoresSchemaVFG_localidades = '';

        $Paises = Geo::getCountries();
/*
        foreach ($Paises as $Pais) {
            $ciudades = Geo::getCountry($Pais->)
            ->level(Geo::LEVEL_3)
            ->get(); 
        }
        */

        foreach ($Localidades as $Localidad) { 
            $valoresSchemaVFG_localidades .= '{ id: '.$Localidad['id'].', name: "'.$Localidad['localidad'].', '.$Localidad['provincia']->provincia.', '.$Localidad['provincia']->pais->pais.'" }, ';
        }


        $Paises = Pais::whereRaw('id in (SELECT ip.pais_id FROM idiomas_por_pais ip)')->get();
        $nombre_del_campo = 'pais';
        $valoresSchemaVFG_paises = $this->valoresParaSelectEx($Paises, $nombre_del_campo);


        $Tipo_de_curso_online = Tipo_de_curso_online::get();
        $nombre_del_campo = 'tipo_de_curso_online';
        $valoresSchemaVFG_tipos_de_curso_online = $this->valoresParaSelectEx($Tipo_de_curso_online, $nombre_del_campo);


        $Instituciones = Institucion::get();
        $nombre_del_campo = 'institucion';
        $valoresSchemaVFG_instituciones = $this->valoresParaSelectEx($Instituciones, $nombre_del_campo);

        $paso = 2;
        $pasos_info = $this->pasosInfo($Solicitud->id, $paso-1);    

        if ($Solicitud->tipo_de_evento_id == 3) {
            $required_escribe_tu_ciudad = 'false';
        }
        else {
            $required_escribe_tu_ciudad = 'true';
        }

        return View('solicitudes/solicitud-asistente')
        ->with('solicitud_id', $Solicitud->id)
        ->with('Solicitud', $Solicitud)
        ->with('paso', $paso)
        ->with('valoresSchemaVFG_localidades', $valoresSchemaVFG_localidades)
        ->with('required_escribe_tu_ciudad', $required_escribe_tu_ciudad)
        ->with('valoresSchemaVFG_paises', $valoresSchemaVFG_paises)
        ->with('valoresSchemaVFG_tipos_de_curso_online', $valoresSchemaVFG_tipos_de_curso_online)
        ->with('valoresSchemaVFG_instituciones', $valoresSchemaVFG_instituciones)
        ->with('pasos_info', $pasos_info);           
    }

    public function listarModelosParaSeleccion($solicitud_id)
    {  
        $gen_modelo = 'Modelo';
        $gen_opcion = 0;
        $acciones_extra = array('Seleccionar,fa fa-hand-pointer-o,Solicitudes/crear/elegir-modelo/'.$solicitud_id);
        $Sucursal = Sucursal::where('id', Auth::user()->sucursal_id)->get();
        $gen_seteo['filtros_por_campo'] = array('empresa_id' => $Sucursal[0]->Zona->empresa_id);
        $gen_seteo['gen_url_siguiente'] = 'back';

        $gen_campos_a_ocultar = array('empresa_id', 'sino_activo');
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
            'C',
            'R'
            ];

        $gen_filas = Modelo::where('sino_activo', 'SI')->get();

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




    public function listarFechasDeEventos($solicitud_id)
    {  
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();

        $gen_modelo = 'Fecha_de_evento';
        $gen_opcion = 0;
        $acciones_extra = array();
        $gen_seteo['filtros_por_campo'] = array('solicitud_id' => $solicitud_id);
        $gen_seteo['gen_url_siguiente'] = 'back';
        $gen_seteo['tabla_condensada'] = 'SI';
        $gen_seteo['mostrar_titulo'] = 'NO';
        $gen_seteo['titulo'] = '';
        $gen_seteo['table'] = [
            'searching' => 'false',
            'paging' => 'false',
            'pageLength' => 50
            ];
        $gen_seteo['filtros_por_campo'] = array(
                        'solicitud_id' => $solicitud_id        
                        );


        if ($Solicitud->Tipo_de_evento->id == 1) {
            $gen_campos_a_ocultar = array('solicitud_id', 'url_enlace_a_google_maps_inicio', 'url_enlace_a_google_maps_curso','titulo_de_conferencia_publica', 'resumen_de_la_conferencia', 'sino_agotado', 'url_enlace_a_google_maps_inicio_redirect_final', 'url_enlace_a_google_maps_curso_redirect_final', 'latitud', 'longitud');
            $gen_seteo['no_mostrar_campos_abm'] = 'titulo_de_conferencia_publica|resumen_de_la_conferencia|sino_agotado|url_enlace_a_google_maps_inicio_redirect_final|url_enlace_a_google_maps_curso_redirect_final|latitud|longitud|url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual';

        }
        if ($Solicitud->Tipo_de_evento->id == 2) {
            $gen_campos_a_ocultar = array('solicitud_id', 'url_enlace_a_google_maps_inicio', 'url_enlace_a_google_maps_curso', 'hora_lunes', 'hora_martes', 'hora_miercoles', 'hora_jueves', 'hora_viernes', 'hora_sabado', 'hora_domingo', 'sino_agotado', 'url_enlace_a_google_maps_inicio_redirect_final', 'url_enlace_a_google_maps_curso_redirect_final', 'latitud', 'longitud');
            $gen_seteo['no_mostrar_campos_abm'] = 'hora_lunes|hora_martes|hora_miercoles|hora_jueves|hora_viernes|hora_sabado|hora_domingo|direccion_del_curso|url_enlace_a_google_maps_curso|sino_agotado|url_enlace_a_google_maps_inicio_redirect_final|url_enlace_a_google_maps_curso_redirect_final|latitud|longitud|url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual';
        }
        if ($Solicitud->Tipo_de_evento->id == 3) {
            $gen_campos_a_ocultar = array('solicitud_id', 'url_enlace_a_google_maps_inicio', 'url_enlace_a_google_maps_curso', 'resumen_de_la_conferencia', 'sino_agotado', 'url_enlace_a_google_maps_inicio_redirect_final', 'url_enlace_a_google_maps_curso_redirect_final', 'latitud', 'longitud|direccion_de_inicio|url_enlace_a_google_maps_inicio|cupo_maximo_disponible_del_salon|direccion_del_curso|url_enlace_a_google_maps_curso');
            $gen_seteo['no_mostrar_campos_abm'] = 'resumen_de_la_conferencia|sino_agotado|url_enlace_a_google_maps_inicio_redirect_final|url_enlace_a_google_maps_curso_redirect_final|latitud|longitud|direccion_de_inicio|url_enlace_a_google_maps_inicio|cupo_maximo_disponible_del_salon|direccion_del_curso|url_enlace_a_google_maps_curso';

        }

             

        $GenericController = new GenericController();
        $gen_campos = $GenericController->traer_campos($gen_modelo, $gen_campos_a_ocultar);

        $gen_filas = Fecha_de_evento::where('solicitud_id', $solicitud_id)->get();

        $idioma_por_pais = $Solicitud->idioma_por_pais();    

        if (count($gen_filas) >= 6 and $idioma_por_pais->pais_id == 1) {
            $gen_permisos = [
                'R',
                'U',
                'D'
                ];
        }
        else {
            $gen_permisos = [
                'C',
                'R',
                'U',
                'D'
                ];
        }

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





    public function GuardarDatosDelSolicitante(Request $request) {

        $gCon = new GenericController();

        $solicitud_id = $_POST['solicitud_id'];
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $nombre_del_solicitante = $_POST['nombre_del_solicitante'];
        $celular_del_solicitante = $_POST['celular_del_solicitante'];
        $localidad_id = $_POST['localidad_id'];
        if (isset($_POST['tipo_de_curso_online_id'])) {
            $tipo_de_curso_online_id = $_POST['tipo_de_curso_online_id'];
        }
        else {
            if ($Solicitud->tipo_de_evento_id == 3) {
                $tipo_de_curso_online_id = 1;
            }
            else {
                $tipo_de_curso_online_id = null;
            }
            
        }

        if (isset($_POST['escribe_tu_ciudad_sino_esta_en_la_lista_anterior'])) {
            $escribe_tu_ciudad_sino_esta_en_la_lista_anterior = $_POST['escribe_tu_ciudad_sino_esta_en_la_lista_anterior'];
            $pais_id = $_POST['pais_id'];
        }


        if ($localidad_id > 0) {
            $Solicitud->localidad_id = $localidad_id;
        }        
        $Solicitud->nombre_del_solicitante = $nombre_del_solicitante;
        $Solicitud->celular_del_solicitante = $celular_del_solicitante;

        if (isset($_POST['escribe_tu_ciudad_sino_esta_en_la_lista_anterior'])) {
            $Solicitud->escribe_tu_ciudad_sino_esta_en_la_lista_anterior = $escribe_tu_ciudad_sino_esta_en_la_lista_anterior;
            $Solicitud->pais_id = $pais_id;
        }
        
        if ($Solicitud->tipo_de_evento_id == 3) {
            $Solicitud->tipo_de_curso_online_id = $tipo_de_curso_online_id;
        }


        if (isset($_POST['institucion_id'])) {
            $Solicitud->institucion_id = $_POST['institucion_id'];
        }        
        
        $Solicitud->save();  

        $cant_fechas = Fecha_de_evento::where('solicitud_id', $solicitud_id)->count();



        if ($Solicitud->tipo_de_evento_id == 3 and $tipo_de_curso_online_id <> 4) {
            $paso = 4;
            if ($Solicitud->id_pais() == 6) {
                $action_form = 'SolicitudController@PagarPaypal';
            }
            else {
                $action_form = 'SolicitudController@GuardarDatosCampania';   
            }            

            $Monedas = Moneda::get();
            $nombre_del_campo = 'moneda';
            $valoresSchemaVFG_monedas = $this->valoresParaSelectEx($Monedas, $nombre_del_campo);

            $Canales_de_recepcion_del_curso = Canal_de_recepcion_del_curso::whereRaw('id in (1, 2, 5, 9, 10)')->get();
            $nombre_del_campo = 'canal_de_recepcion_del_curso';
            $valoresSchemaVFG_canales_de_recepcion_del_curso = $this->valoresParaSelectEx($Canales_de_recepcion_del_curso, $nombre_del_campo);

            $Capacitaciones = Capacitacion::get();
            $nombre_del_campo = 'nombre_de_la_capacitacion';
            $valoresSchemaVFG_capacitaciones = $this->valoresParaSelectEx($Capacitaciones, $nombre_del_campo);


            $Idiomas_por_pais = Idioma_por_pais::where('pais_id', $Solicitud->id_pais())->where('institucion_id', $Solicitud->institucion_id)->get();
            //$Idiomas_por_pais = Idioma_por_pais::get();

            $valoresSchemaVFG_idiomas = '';

            if(count($Idiomas_por_pais) < 1) {
                $valoresSchemaVFG_idiomas = '{ id: 1, name: "Español" }, ';
            }
            else {
                foreach ($Idiomas_por_pais as $Idioma_por_pais) { 
                    $valoresSchemaVFG_idiomas .= '{ id: '.$Idioma_por_pais['idioma_id'].', name: "'.$Idioma_por_pais['idioma']->idioma.'" }, ';
                }
            }

            $nombre_del_campo = 'moneda';
            $valoresSchemaVFG_monedas = $this->valoresParaSelectEx($Monedas, $nombre_del_campo);            
        }
        else {
            $paso = 3;
            $action_form = '';
            $valoresSchemaVFG_monedas = '';
            $valoresSchemaVFG_canales_de_recepcion_del_curso = '';
            $valoresSchemaVFG_idiomas = '';
            $valoresSchemaVFG_capacitaciones = '';
        }
        $pasos_info = $this->pasosInfo($solicitud_id, $paso-1);

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $solicitud_id)        
        ->with('Solicitud', $Solicitud)            
        ->with('cant_fechas', $cant_fechas)          
        ->with('pasos_info', $pasos_info)          
        ->with('action_form', $action_form)     
        ->with('valoresSchemaVFG_monedas', $valoresSchemaVFG_monedas)
        ->with('valoresSchemaVFG_canales_de_recepcion_del_curso', $valoresSchemaVFG_canales_de_recepcion_del_curso)
        ->with('valoresSchemaVFG_idiomas', $valoresSchemaVFG_idiomas)
        ->with('valoresSchemaVFG_capacitaciones', $valoresSchemaVFG_capacitaciones)
        ->with('paso', $paso);              

    }

    public function listarFechasDeEventos2($solicitud_id) {

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $cant_fechas = Fecha_de_evento::where('solicitud_id', $solicitud_id)->count();

        $paso = 3;
        $pasos_info = $this->pasosInfo($solicitud_id, $paso-1);

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $solicitud_id)        
        ->with('Solicitud', $Solicitud)           
        ->with('cant_fechas', $cant_fechas)          
        ->with('pasos_info', $pasos_info)     
        ->with('paso', $paso);              


    }


    public function datosDeLaCampania($solicitud_id) {


        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $tipo_de_evento = __('Tipo de Evento').': '.$Solicitud->Tipo_de_evento->tipo_de_evento;

        $solicitante = __('Solicitante').': '.$Solicitud->nombre_del_solicitante."(".$Solicitud->localidad_nombre().")";

        $cant_fechas = Fecha_de_evento::where('solicitud_id', $solicitud_id)->count();
        $fechas = __('Fechas').': '.$cant_fechas;


        $Monedas = Moneda::get();
        $nombre_del_campo = 'moneda';
        $valoresSchemaVFG_monedas = $this->valoresParaSelectEx($Monedas, $nombre_del_campo);

        $Canales_de_recepcion_del_curso = Canal_de_recepcion_del_curso::whereRaw('id in (1, 2, 5, 9, 10)')->get();
        $nombre_del_campo = 'canal_de_recepcion_del_curso';
        $valoresSchemaVFG_canales_de_recepcion_del_curso = $this->valoresParaSelectEx($Canales_de_recepcion_del_curso, $nombre_del_campo);

        $Idiomas_por_pais = Idioma_por_pais::where('pais_id', $Solicitud->id_pais())->where('institucion_id', $Solicitud->institucion_id)->get();
        //$Idiomas_por_pais = Idioma_por_pais::get();

        $valoresSchemaVFG_idiomas = '';

        if(count($Idiomas_por_pais) < 1) {
            $valoresSchemaVFG_idiomas = '{ id: 1, name: "Español" }, ';
        }
        else {
            foreach ($Idiomas_por_pais as $Idioma_por_pais) { 
                $valoresSchemaVFG_idiomas .= '{ id: '.$Idioma_por_pais['idioma_id'].', name: "'.$Idioma_por_pais['idioma']->idioma.'" }, ';
            }
        }
   
        if ($Solicitud->id_pais() == 6) {
            $action_form = 'SolicitudController@PagarPaypal';
        }
        else {
            $action_form = 'SolicitudController@GuardarDatosCampania';   
        }

        $paso = 4;
        $pasos_info = $this->pasosInfo($solicitud_id, $paso-1);

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $solicitud_id)        
        ->with('Solicitud', $Solicitud)          
        ->with('pasos_info', $pasos_info)     
        ->with('valoresSchemaVFG_monedas', $valoresSchemaVFG_monedas)
        ->with('valoresSchemaVFG_canales_de_recepcion_del_curso', $valoresSchemaVFG_canales_de_recepcion_del_curso)
        ->with('valoresSchemaVFG_idiomas', $valoresSchemaVFG_idiomas)
        ->with('action_form', $action_form)
        ->with('paso', $paso);              


    }


    public function PagarPaypal(Request $request) {

        require_once(app_path() . '/Libraries/PayPal/PayPalClient.php');
        require_once(app_path() . '/Libraries/PayPal/PayPalConfiguration.php');
        require_once(app_path() . '/Libraries/PayPal/Requests/AuthorizeCheckoutRequest.php');
        require_once(app_path() . '/Libraries/PayPal/Responses/AuthorizeCheckoutResponse.php');
        require_once(app_path() . '/Libraries/PayPal/Util/Parser.php');     

        $solicitud_id = $_POST['solicitud_id'];
        $value = $_POST["monto_a_invertir"];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        
        if (isset($_POST['moneda_id'])) {
            $Solicitud->moneda_id = $_POST['moneda_id'];
        }
        if (isset($_POST['idioma_id'])) {
            $Solicitud->idioma_id = $_POST['idioma_id'];
            $idioma_por_pais = $Solicitud->idioma_por_pais();
            $Solicitud->curso_id = $idioma_por_pais->curso_id;
        }
        $Solicitud->monto_a_invertir = $_POST['monto_a_invertir'];       
        
        if (!isset($_POST['sino_solicitar_responsable_de_inscripcion']) or $_POST['sino_solicitar_responsable_de_inscripcion'] == 'NO') {
            $Solicitud->sino_solicitar_responsable_de_inscripcion = 'NO';
            $Solicitud->nombre_responsable_de_inscripciones = $_POST['nombre_responsable_de_inscripciones'];
            $Solicitud->celular_responsable_de_inscripciones = $_POST['celular_responsable_de_inscripciones'];
            //$Solicitud->email_correo_responsable_de_inscripciones = $_POST['email_correo_responsable_de_inscripciones'];
        }  
        else {            
            $Solicitud->sino_solicitar_responsable_de_inscripcion = 'SI';
        }

        $Solicitud->observaciones = $_POST['observaciones'];
        $hash = $this->hashSolicitud($Solicitud);
        $Solicitud->hash = $hash;
        $Solicitud->save();  


        $request = new AuthorizeCheckoutRequest();
        $request->ItemQuantity = 1;
        $request->ItemTotalValue = $value;
        $request->ItemValue = $value;
        $request->ProductDescription = "Divulgação Facebook da Tecnotronica";
        $request->ProductName = "Divulgação Facebook da Tecnotronica";
        $request->SubtotalValue = $value;
        $request->TotalValue = $value;
        $request->ReturnUrlAuthorized = env('PATH_PUBLIC')."pagar-paypal/ReturnUrlAuthorized";
        $request->ReturnUrlNotAuthorized = env('PATH_PUBLIC')."pagar-paypal/ReturnUrlNonAuthorizedPayPal";

        $payPalClient = new PayPalClient;
        $response = $payPalClient->AuthorizeCheckout($request);

        if ($response->Ack)
        {
            $args = [
                "paypal_token" => $response->Token,
                "paypal_value" => $value,
                "payment_status" => 'authorized',
                "payment_checkout_status" => 'authorized',
                "payment_paid" => 0
            ];

            $Solicitud = Solicitud::find($solicitud_id);

            foreach($args as $k => $v){
                //echo "|".gettype($v) . "|";
                $Solicitud->$k = $v;
            }

            $Solicitud->save();


            return redirect($response->getUrlDoCheckout());
            
            exit();
        }
        else
        {
            $mensaje['error'] = true;
            $mensaje['detalle'] = __('La integración con el módulo de pago no ha podido ser realizada');
            $mensaje['class'] = 'alert-warning'; 
        }

    }

    public function returnUrlAuthorized(Request $request) {

        require_once(app_path() . '/Libraries/PayPal/PayPalClient.php');
        require_once(app_path() . '/Libraries/PayPal/PayPalConfiguration.php');
        require_once(app_path() . '/Libraries/PayPal/Requests/DoCheckoutRequest.php');
        require_once(app_path() . '/Libraries/PayPal/Requests/AuthorizeCheckoutRequest.php');
        require_once(app_path() . '/Libraries/PayPal/Responses/AuthorizeCheckoutResponse.php');
        require_once(app_path() . '/Libraries/PayPal/Responses/DoCheckoutResponse.php');
        require_once(app_path() . '/Libraries/PayPal/Responses/CheckoutDetailsResponse.php');
        require_once(app_path() . '/Libraries/PayPal/Util/Parser.php');     


        try
        {
            $token = $_GET["token"];
            $payerId = $_GET["PayerID"];

            $Solicitudes = Solicitud::where('paypal_token', $token)->get();

            $payment = [];
            foreach ($Solicitudes as $Solicitud) { 
                $payment = $Solicitud;
            }


            if(!$payment) {
                $mensaje['error'] = true;
                $mensaje['detalle'] = __('No fue posible recuperar el pago');
                $mensaje['class'] = 'alert-warning';               
            }
            else {

                $payment->paypal_payerid = $payerId;

                $request = new DoCheckoutRequest;
                $request->Token = $payment->paypal_token;
                $request->PayerId = $payment->paypal_payerid;
                $request->ItemQuantity = 1;
                $request->ItemTotalValue = $payment->paypal_value;
                $request->ItemValue = $payment->paypal_value;
                $request->ProductDescription = "Divulgação Facebook da Tecnotronica";
                $request->ProductName = "Divulgação Facebook da Tecnotronica";
                $request->SubtotalValue = $payment->paypal_value;
                $request->TotalValue = $payment->paypal_value;
                
                $paypal = new PayPalClient();
                $responseDoCheckout = $paypal->DoCheckout($request);

                if ($responseDoCheckout->Ack) {
                    $details = $paypal->GetCheckoutDetails($token);

                    if ($details->Ack)
                    {
                        $payment->payment_checkout_status = $details->CheckoutStatus;
                        $payment->payment_status = $responseDoCheckout->PaymentStatus;
                        $payment->paypal_transaction_id = $responseDoCheckout->TransactionId;
                        $payment->payment_pending_reason = $responseDoCheckout->PedingReason;
                        $payment->payment_reason_code = $responseDoCheckout->ReasonCode;
                        $payment->payment_error_code = $responseDoCheckout->ErrorCode | $details->PaymentErrorCode;

                        $payment->payment_status = 'Paid';
                        $payment->payment_paid = 1;
                        $payment->payment_paid_date = date('Y-m-d H:i:s');

                        $Solicitud = Solicitud::find($payment->id);

                        foreach($payment as $k => $v){
                            //echo "|".gettype($v) . "|";
                            $Solicitud->$k = $v;
                        }

                        $Solicitud->save();

                        $mensaje['error'] = false;
                        $mensaje['detalle'] = __('Pago realizado con exito!');
                        $mensaje['class'] = 'alert-success';

                    }
                    else
                    {
                        $mensaje['error'] = true;
                        $mensaje['detalle'] = __('La integración con el módulo de pago no ha podido ser realizada');
                        $mensaje['class'] = 'alert-warning';
                    }
                }
                else
                {
                    $mensaje['error'] = true;
                    $mensaje['detalle'] = __('La integración con el módulo de pago no ha podido ser realizada');
                    $mensaje['class'] = 'alert-warning';
                }
            }
        }
        catch (Exception $ex)
        {
            $mensaje['error'] = true;
            $mensaje['detalle'] = __('Ocurrio un error al realizar el pago:') . $ex->getMessage();
            $mensaje['class'] = 'alert-warning';
            
        }


        $Fechas_de_eventos = Fecha_de_evento::where('solicitud_id', $payment->id)->get();
        $paso = 5;
        $pasos_info = $this->pasosInfo($payment->id, $paso-1);

        $href_volver = env('PATH_PUBLIC').'Solicitudes/crear/datos-de-la-campania/'.$payment->id;

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $payment->id)        
        ->with('Solicitud', $Solicitud)          
        ->with('Fechas_de_eventos', $Fechas_de_eventos)          
        ->with('pasos_info', $pasos_info)     
        ->with('paso', $paso)     
        ->with('mensaje', $mensaje)
        ->with('href_volver', $href_volver);     

    }



    public function recuperarOperacionPagada($solicitud_id) {

             
        $Solicitud = Solicitud::find($solicitud_id);

        $Fechas_de_eventos = Fecha_de_evento::where('solicitud_id', $Solicitud->id)->get();
        $paso = 5;
        $pasos_info = $this->pasosInfo($Solicitud->id, $paso-1);

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $Solicitud->id)        
        ->with('Solicitud', $Solicitud)          
        ->with('Fechas_de_eventos', $Fechas_de_eventos)          
        ->with('pasos_info', $pasos_info)     
        ->with('paso', $paso);     

    }


    public function GuardarDatosCampania(Request $request) {

        $solicitud_id = $_POST['solicitud_id'];
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();

        //$Solicitud->rector_diocesano_o_responsable = $_POST['rector_diocesano_o_responsable'];
        //$Solicitud->nombre_coordinador_de_difusion  = $_POST['nombre_coordinador_de_difusion'];
        //$Solicitud->celular_coordinador_de_difusion = $_POST['celular_coordinador_de_difusion'];
        if (isset($_POST['moneda_id'])) {
            $Solicitud->moneda_id = $_POST['moneda_id'];
        }
        if (isset($_POST['idioma_id'])) {
            $Solicitud->idioma_id = $_POST['idioma_id'];
            $idioma_por_pais = $Solicitud->idioma_por_pais();
            $Solicitud->curso_id = $idioma_por_pais->curso_id;
        }
        $Solicitud->monto_a_invertir = $_POST['monto_a_invertir'];       


        if (!isset($_POST['sino_solicitar_responsable_de_inscripcion']) or $_POST['sino_solicitar_responsable_de_inscripcion'] == 'NO') {
            $Solicitud->sino_solicitar_responsable_de_inscripcion = 'NO';
            $Solicitud->nombre_responsable_de_inscripciones = $_POST['nombre_responsable_de_inscripciones'];
            $Solicitud->celular_responsable_de_inscripciones = $_POST['celular_responsable_de_inscripciones'];
            //$Solicitud->email_correo_responsable_de_inscripciones = $_POST['email_correo_responsable_de_inscripciones'];
        }  
        else {            
            $Solicitud->sino_solicitar_responsable_de_inscripcion = 'SI';
        }

        /*
        if (!isset($_POST['sino_solicitar_responsable_de_fanpage']) or $_POST['sino_solicitar_responsable_de_fanpage'] == 'NO') {
            $Solicitud->sino_solicitar_responsable_de_fanpage = 'NO';
            $Solicitud->nombre_responsable_de_fanpage = $_POST['nombre_responsable_de_fanpage'];
            $Solicitud->celular_responsable_de_fanpage = $_POST['celular_responsable_de_fanpage'];
        }  
        else {            
            $Solicitud->sino_solicitar_responsable_de_fanpage = 'SI';
        }
        */

        $Solicitud->observaciones = $_POST['observaciones'];

        $hash = $this->hashSolicitud($Solicitud);
        $Solicitud->hash = $hash;
        
        $Fechas_de_eventos = Fecha_de_evento::where('solicitud_id', $solicitud_id)->get();

        $paso = 5;
        $pasos_info = $this->pasosInfo($solicitud_id, $paso-1);

        $gCont = new GenericController();
        
        if ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 2) {
            $Solicitud->fecha_de_inicio_del_curso_online = $gCont->FormatoFecha($_POST['fecha_de_inicio_del_curso_online']);
            $Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual = $_POST['url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual'];
        }
        if ($Solicitud->tipo_de_evento_id == 3 and ($Solicitud->tipo_de_curso_online_id == 3 or $Solicitud->tipo_de_curso_online_id == 5)) {
            $Solicitud->fecha_de_inicio_del_curso_online = $gCont->FormatoFecha($_POST['fecha_de_inicio_del_curso_online']);
            $Solicitud->hora_de_inicio_del_curso_online = $_POST['hora_de_inicio_del_curso_online'];
            $Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual = $_POST['url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual'];
        }

        if ($Solicitud->tipo_de_evento_id == 3) {

            if (isset($_POST['canal_de_recepcion_del_curso_id'])) {
                $Solicitud->canal_de_recepcion_del_curso_id = $_POST['canal_de_recepcion_del_curso_id'];
            }  

        }
        else {

            if (isset($_POST['sino_habilitar_modalidad_online']) and $_POST['sino_habilitar_modalidad_online'] == 'SI') {
                $Solicitud->sino_habilitar_modalidad_online = 'SI';
            }  
            else {            
                $Solicitud->sino_habilitar_modalidad_online = 'NO';
            }

        }


        $Solicitud->save();  

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $solicitud_id)        
        ->with('Solicitud', $Solicitud)          
        ->with('Fechas_de_eventos', $Fechas_de_eventos)          
        ->with('pasos_info', $pasos_info)     
        ->with('paso', $paso);              
    }



    public function enviarSolicitud($solicitud_id)
    {   

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $now = new \DateTime();
        $Solicitud->fecha_de_solicitud = $now->format('Y-m-d H:i:s');
        $Solicitud->AsignarEjecutivo();
        $Solicitud->save();


        $paso = 6;
        $pasos_info = $this->pasosInfo($solicitud_id, $paso);

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $solicitud_id)    
        ->with('Solicitud', $Solicitud)     
        ->with('paso', $paso)
        ->with('pasos_info', $pasos_info);               
    }





    public function pasosInfo($solicitud_id, $paso)
    { 

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $pasos_info = array();

        //PASO 1
        if ($paso >= 1) {
            $paso_1 = __('Tipo de Evento').': '.$Solicitud->Tipo_de_evento->tipo_de_evento;
            array_push($pasos_info, $paso_1);    
        }
        
        
        //PASO 2
        if ($paso >= 2) {
            $paso_2 = __('Solicitante').': '.$Solicitud->nombre_del_solicitante."(".$Solicitud->localidad_nombre().")";
            array_push($pasos_info, $paso_2); 
        }

        //PASO 3
        if ($paso >= 3) {
            $cant_fechas = Fecha_de_evento::where('solicitud_id', $solicitud_id)->count();
            $paso_3 = __('Fechas').': '.$cant_fechas;  
            array_push($pasos_info, $paso_3);   
        }
        
        //PASO 4        
        if ($paso >= 4) {
            $paso_4 = __('Monto').': '.$Solicitud->monto_a_invertir;
            array_push($pasos_info, $paso_4); 
        }

        //PASO 5    
        if ($paso >= 5) {
            $paso_5 = __('Revisión: OK');
            array_push($pasos_info, $paso_5); 
        }

        //PASO 6    
        if ($paso >= 6) {
            $paso_6 = __('Solicitud Enviada').'<br><i class="fa fa-fw fa-check-circle-o" style="font-size: 40px"></i>';
            array_push($pasos_info, $paso_6); 
        }

        return $pasos_info;

    }




    public function traerElementosPaginaSolicitud($solicitud_id)
    {   

        return array('Solicitud' => $Solicitud, 'Fechas_de_eventos' => $Fechas_de_eventos, 'autorizado' => $autorizado);
    }




    public function editarSolicitud($solicitud_id)
    {   

        $Roles = Auth::user()->roles();

        if (intval($solicitud_id)) {
            $Solicitud = Solicitud::find($solicitud_id);

            if ($Solicitud <> null) {
                $Fechas_de_eventos = Fecha_de_evento::
                    where('solicitud_id', $solicitud_id)
                    ->get();

                if (($Solicitud->tipo_de_evento_id <> 3 AND (Auth::user()->rol_de_usuario_id < 3 or (Auth::user()->rol_de_usuario_id == 3 and $Solicitud->ejecutivo == Auth::user()->id)  or ($Solicitud->user_id == Auth::user()->id) or (in_array(Auth::user()->rol_de_usuario_id, array(7, 9))))) OR ($Solicitud->tipo_de_evento_id == 3 AND (in_array(13, $Roles) or in_array(3, $Roles) or in_array(2, $Roles) or in_array(1, $Roles)  or in_array(7, $Roles)  or $Solicitud->user_id == Auth::user()->id) ))  {
                    $autorizado = true;
                }
                else {
                    $autorizado = false;   
                }

                $cant_fechas_de_eventos_sin_finalizar = Fecha_de_evento::
                    where('solicitud_id', $solicitud_id)
                    ->whereRaw('fecha_de_inicio >= NOW()')
                    ->count();


                if ($cant_fechas_de_eventos_sin_finalizar > 0 or ($Solicitud->tipo_de_campania_facebook_id == '' or $Solicitud->identificador_de_la_campania_de_facebook == '' or $Solicitud->importe_gastado == '' or $Solicitud->resultados == '' or $Solicitud->alcances == '' or $Solicitud->impresiones == '' or $Solicitud->frecuencia == '' or $Solicitud->clics_unicos == '')) {
                    $para_finalizar = 'N';
                }
                else  {
                    $para_finalizar = 'S';
                }
                
                /*
                if ($Solicitud->tipo_de_campania_facebook_id == '' or $Solicitud->campaña_id_facebook == '' or $Solicitud->importe_gastado == '' or $Solicitud->resultados == '' or $Solicitud->alcances == '' or $Solicitud->impresiones == '' or $Solicitud->frecuencia == '' or $Solicitud->clics_unicos == '') {
                    $falta_completar_datos_facebook = 'SI';
                }
                else {
                    $falta_completar_datos_facebook = 'NO';
                }
                */

                $Inscriptos_por_campania = DB::table('inscripciones as i')
                ->select(DB::Raw('c.id, c.campania, p.pais, c.moneda_importe_en_dolares, c.sino_es_campania_organica, COUNT(DISTINCT i.id) cant'))
                ->join('campanias as c', 'c.id', '=', 'i.campania_id')
                ->leftjoin('paises as p', 'p.id', '=', 'c.pais_id')
                ->where('i.solicitud_id', $solicitud_id)
                ->groupBy(DB::Raw('c.id, c.campania, p.pais, c.moneda_importe_en_dolares, c.sino_es_campania_organica'))
                ->get();


                $cant_inscriptos_derivados = Inscripcion::where('solicitud_original', $solicitud_id)->count();

                
                if ($autorizado) {
                    return View('solicitudes/solicitud')
                    ->with('Solicitud', $Solicitud)
                    ->with('Fechas_de_eventos', $Fechas_de_eventos)
                    ->with('para_finalizar', $para_finalizar)
                    ->with('Inscriptos_por_campania', $Inscriptos_por_campania)
                    ->with('cant_inscriptos_derivados', $cant_inscriptos_derivados);
                }
                else  {
                    $mensaje['error'] = true;
                    $mensaje['detalle'] = __('Ud. no está autorizado para ver esta página');
                    $mensaje['class'] = 'alert-warning'; 
                    return View('no-autorizado')->with('mensaje', $mensaje);
                }
            }
            else {
                echo 'ERROR';
            }
        }
        else {
            echo 'ERROR';
        }
    }


    /*
    private function properText($text){
        $text = mb_convert_encoding($text, "HTML-ENTITIES", "UTF-8");
        $text = preg_replace('~^(&([a-zA-Z0-9]);)~',htmlentities('${1}'),$text);
        return($text); 
    }
    */


    public function traerMontoPorAsistentePromedio() {
        $idioma_id = $_POST['idioma_id'];
        $solicitud_id = $_POST['solicitud_id'];
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();

        $Idioma_por_pais = Idioma_por_pais::where('idioma_id', $idioma_id)->where('pais_id', $Solicitud->id_pais())->first();
        
        $monto_por_asistente_promedio = 0;
        if ($Idioma_por_pais <> null) {
            $monto_por_asistente_promedio = $Idioma_por_pais->monto_por_asistente_promedio;
        }

        $cupo_maximo_disponible_del_salon = Fecha_de_evento::where('solicitud_id', $solicitud_id)->sum('cupo_maximo_disponible_del_salon');
        $monto_a_invertir_sugerido = $cupo_maximo_disponible_del_salon * $monto_por_asistente_promedio;

        return $monto_a_invertir_sugerido;
    }


    public function hashSolicitud($Solicitud) {

        $localidad_array = explode(' ', $Solicitud->localidad_nombre());
        $hash_localidad = '';
        foreach ($localidad_array as $palabra_localidad) {
            $hash_localidad .= ucfirst($palabra_localidad);
        }

        $hash_localidad = $this->sanear_string($hash_localidad);

        if ($Solicitud->tipo_de_evento_id == 3) {
            $fecha_de_inicio = strtotime($Solicitud->fecha_de_solicitud);    
        }
        else {
            $fecha_de_inicio = strtotime($Solicitud->fechas_de_evento[0]->fecha_de_inicio);
        }
        
        $hash_mes = date("m", $fecha_de_inicio);
        $hash_anio = date("y", $fecha_de_inicio);

        $hash = $hash_localidad.'-'.$hash_mes.'-'.$hash_anio;





        return $hash;
    }


    public function sanear_string($string) {
 
        $string = trim($string);
     
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
     
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
     
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
     
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
     
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
     
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );
     
        //Esta parte se encarga de eliminar cualquier caracter extraño
        /*
        $string = str_replace(
            array("\", "¨", "º", "-", "~",
                 "#", "@", "|", "!", """,
                 "·", "$", "%", "&", "/",
                 "(", ")", "?", "'", "¡",
                 "¿", "[", "^", "<code>", "]",
                 "+", "}", "{", "¨", "´",
                 ">", "< ", ";", ",", ":",
                 ".", " "),
            '',
            $string
        );
        */
     
        return $string;
    }

    public function llenar() {

        for($i=0; $i<=10; $i++) {

            $Solicitud = new Solicitud;
            $Solicitud->tipo_de_evento_id = 1;
            $Solicitud->user_id = Auth::user()->id;
            $Solicitud->localidad_id = 161;
            $Solicitud->fecha_de_solicitud = '2019-01-01';
            $Solicitud->AsignarEjecutivo();
            $Solicitud->save();

        }

        echo 'completado';
    }



    public function aprobacionAdministracion($solicitud_id)
    {   
        $sino_aprobado_administracion = $_POST['sino_aprobado_administracion'];
        
        $MauticController = new MauticController();

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->sino_aprobado_administracion = $sino_aprobado_administracion;
        if ($sino_aprobado_administracion == 'SI') {
            $Solicitud->observaciones_aprobado_administracion = NULL;    
            if ($Solicitud->sino_cancelada <> 'SI') {
                $isPublished = 1;        
            }
            else {
                $isPublished = 0;
            }
        }
        else {
            $isPublished = 0;
        }

        $Activado = Parametro::find(1);
        if ($Activado->sino_activado == 'SI') {
            if ($Solicitud->campania_mautic_id > 0) {
                $MauticController->onOffCampaniaMautic($Solicitud->campania_mautic_id, $isPublished);
            }
            else {
                if ($sino_aprobado_administracion == 'SI') {
                    dispatch(new ColaProgramarCampaniaMautic($solicitud_id));
                    //$campania_mautic_id = $MauticController->programarCampaniaMautic($solicitud_id);            
                    //$Solicitud->campania_mautic_id = $campania_mautic_id;                
                }
            }
        }



        $Solicitud->save();


        // Enviar Notificacion para Maria Laura
        $pais_id_lau = 0;
        if ($Solicitud->localidad_id <> '') {
            $pais_id_lau = $Solicitud->Localidad->Provincia->pais_id;
        }
        else {
            if ($Solicitud->pais_id <> '') {
                $pais_id_lau = $Solicitud->pais_id;
            }
        }
        if ($sino_aprobado_administracion == 'SI' and $pais_id_lau == 1) {
            $NotificationController = new NotificationController();

            $user_id = 198;
            $mensaje = __('Se ha aprobado una nueva Solicitud de Campaña').': '.$Solicitud->descripcion_sin_estado().' - '.__('Formulario de Inscripcion').' -> '.$Solicitud->url_form_inscripcion();
            $NotificationController->enviarNotificacion(1, $user_id, $mensaje); 
        }

        return $sino_aprobado_administracion;
    }




    public function aprobacionFinalizada($solicitud_id)
    {   
        $sino_aprobado_finalizada = $_POST['sino_aprobado_finalizada'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->sino_aprobado_finalizada = $sino_aprobado_finalizada;
        if ($sino_aprobado_finalizada == 'SI') {
            $Solicitud->observaciones_aprobado_finalizada = NULL;
        }
        $Solicitud->save();

        return $sino_aprobado_finalizada;
    }


    public function aprobacionCancelada($solicitud_id)
    {   
        $sino_cancelada = $_POST['sino_cancelada'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->sino_cancelada = $sino_cancelada;
        if ($sino_cancelada == 'SI') {
            $Solicitud->observaciones_cancelada = NULL;
            $isPublished = 0;
        }
        else {
            if ($Solicitud->sino_aprobado_administracion == 'SI') {
                $isPublished = 1;
            }
            else {
                $isPublished = 0;
            }
        } 
        
        $MauticController = new MauticController();

        $Activado = Parametro::find(1);
        if ($Activado->sino_activado == 'SI') {
            if ($Solicitud->campania_mautic_id > 0) {
                $MauticController->onOffCampaniaMautic($Solicitud->campania_mautic_id, $isPublished);
            }
            else {
                if ($isPublished == 1) {
                    dispatch(new ColaProgramarCampaniaMautic($solicitud_id));          
                }
            }
        }

        $Solicitud->save();    

        return $sino_cancelada;
    }



    public function aprobacionSolicitarRevision($solicitud_id)
    {   
        $sino_aprobado_solicitar_revision = $_POST['sino_aprobado_solicitar_revision'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->sino_aprobado_solicitar_revision = $sino_aprobado_solicitar_revision;
        $Solicitud->save();

        return $sino_aprobado_solicitar_revision;
    }


    public function guardarObsAdm($solicitud_id)
    {   
        $observaciones_aprobado_administracion = $_POST['observaciones_aprobado_administracion'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->observaciones_aprobado_administracion = $observaciones_aprobado_administracion;
        $Solicitud->save();
    }

    public function guardarObsGar($solicitud_id)
    {   
        $observaciones_aprobado_garantes = $_POST['observaciones_aprobado_garantes'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->observaciones_aprobado_garantes = $observaciones_aprobado_garantes;
        $Solicitud->save();
    }


    public function guardarObsSolRev($solicitud_id)
    {   
        $observaciones_aprobado_solicitar_revision = $_POST['observaciones_aprobado_solicitar_revision'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->observaciones_aprobado_solicitar_revision = $observaciones_aprobado_solicitar_revision;
        $Solicitud->save();
    }

    public function guardarObsFin($solicitud_id)
    {   
        $observaciones_aprobado_finalizada = $_POST['observaciones_aprobado_finalizada'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->observaciones_aprobado_finalizada = $observaciones_aprobado_finalizada;
        $Solicitud->save();
    }

    public function guardarObsCanc($solicitud_id)
    {   
        $observaciones_cancelada = $_POST['observaciones_cancelada'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->observaciones_cancelada = $observaciones_cancelada;
        $Solicitud->save();
    }

    public function resetearCampania($solicitud_id, $password_reset)
    {   
        if ($password_reset == 'flush' and (Auth::user()->id == 1 or (Auth::user()->id == 50 and ($solicitud_id == 6 or $solicitud_id == 9 or $solicitud_id == 12 or $solicitud_id == 6966 or $solicitud_id == 6871 or $solicitud_id == 1 or $solicitud_id == 4555 or $solicitud_id == 4701 or $solicitud_id == 5032 or $solicitud_id == 5111)))) {
            Visualizacion_de_formulario::where('solicitud_id', $solicitud_id)->delete();
            Visualizacion_de_formulario::whereRaw('inscripcion_id in (Select i.id From inscripciones i Where i.solicitud_id = '.$solicitud_id.')')->delete();
            Cambio_de_solicitud_de_inscripcion::whereRaw('inscripcion_id in (Select i.id From inscripciones i Where i.solicitud_id = '.$solicitud_id.')')->delete();
            Evento_en_sitio::where('solicitud_id', $solicitud_id)->delete();
            Asistencia::whereRaw('inscripcion_id in (Select i.id From inscripciones i Where i.solicitud_id = '.$solicitud_id.')')->delete();
            Envio::whereRaw('inscripcion_id in (Select i.id From inscripciones i Where i.solicitud_id = '.$solicitud_id.')')->delete();
            Inscripcion::where('solicitud_id', $solicitud_id)->delete();

            $mensaje['error'] = false;
            $mensaje['detalle'] = __('Campaña Reseteada');
        }
        else {
            $mensaje['error'] = true;
            $mensaje['detalle'] = __('Ud. No tiene los permisos para realizar esta operación de Reseteo de Campaña');
        }
        //dd($mensaje['detalle']);
        return redirect()->back()->withErrors([$mensaje['error'], $mensaje['detalle']]);

    }

    public function paisesDelEquipo($equipo_id = null) {

        if ($equipo_id == null) {
            $equipo_id = Auth::user()->equipo_id;
        }

        $Equipo = Equipo::find($equipo_id);

        $Paises[] = null;
        $where_paises = '';
        $in = '';

        if ($equipo_id <> '') {
            $in = 'in';
            $pais_id = $Equipo->pais_id;
            if ($pais_id == '') {
                $Paises_por_equipo = Pais_por_equipo::where('equipo_id', $equipo_id)->get();

                if (count($Paises_por_equipo) > 0) {
                    foreach ($Paises_por_equipo as $Pais) {
                        $Paises[] = $Pais->pais_id;
                    }                
                }
                else {
                    $Paises_por_equipo = Pais_por_equipo::all();
                    if (count($Paises_por_equipo) > 0) {
                        $in = 'not in';
                        foreach ($Paises_por_equipo as $Pais) {
                            $Paises[] = $Pais->pais_id;
                        }                
                    }
                    $Equipos = Equipo::whereNotNull('pais_id')->get();
                    if (count($Equipos) > 0) {
                        $in = 'not in';
                        foreach ($Equipos as $Equipo) {
                            $Paises[] = $Equipo->pais_id;
                        }                
                    }
                }
            }
            else {
                $Paises[] = $pais_id;
            }


            $where_paises = '';
            foreach ($Paises as $Pais) {
                if ($where_paises == '') {
                    $where_paises .= $Pais;    
                }
                else {
                    $where_paises .= ', '.$Pais;
                }
            }      

        }


        if ($equipo_id <> '') {
            $where_raw_rol_usuario = "IFNULL(pa.id, pa2.id) $in ($where_paises)";
                    
        }
        else {
            $where_raw_rol_usuario = "1=0";
        }

        $array = [
            'Paises' => $Paises,
            'where_raw_rol_usuario' => $where_raw_rol_usuario,
            'in_paises' => "$in ($where_paises)",
            'in' => $in,
            'where_paises' => "($where_paises)"
        ];

        //dd($array);

        return $array;

    }

}

