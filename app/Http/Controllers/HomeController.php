<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\User;
use App\Pais;
use App\Estado_de_seguimiento;
use App\Provincia;
use App\Localidad;
use App\Tipo_de_evento;
use App\Equipo;
use App\Idioma;
use App\Inscripcion;
use App\Fecha_de_evento;
use App\Capacitacion;
use App\Canal_de_recepcion_del_curso;
use App\Http\Controllers\SolicitudController;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ExtController;
use Hash;
use Validator;


class HomeController extends Controller
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



    public function index()
    {   
        //$Solicitudes = Solicitud::all();

        if(Auth::user()->rol_de_usuario_id == 7 or Auth::user()->rol_de_usuario_id == 9) {

            if (Auth::user()->rol_de_usuario_id <> 9) {
                $pais_id = Auth::user()->pais_id;
                $pais = '';
                if ($pais_id <> '') {
                  $pais = Auth::user()->pais->pais;
                }
            }
            else {
                $pais = '';
            }

            return View('dashboard/dash-oe')
            ->with('home', 'SI')
            ->with('pais', $pais);
        }

        else {
            $registros_home = $this->registros_home();

            return View('welcome')
            ->with('titulo', $registros_home['titulo'])
            ->with('Solicitudes', $registros_home['Solicitudes'])
            ->with('Solicitudes_Alarmas', $registros_home['Solicitudes_Alarmas'])
            ->with('cant_autorizaciones', $registros_home['cant_autorizaciones'])
            ->with('Autorizaciones', $registros_home['Autorizaciones'])
            ->with('mensaje_welcome', $registros_home['mensaje_welcome']);
        }

    }


    public function dashboard()
    {   
        $Roles = Auth::user()->roles();

        if(in_array(7, $Roles) or in_array(9, $Roles)) {

            if (in_array(7, $Roles)) {
                $pais_id = Auth::user()->pais_id;
                $pais = '';
                if ($pais_id <> '') {
                  $pais = Auth::user()->pais->pais;
                }
            }
            else {
                $pais = '';
            }

            return View('dashboard/dash-oe')
            ->with('home', 'SI')
            ->with('pais', $pais);
        }

        else {
            $registros_home = $this->registros_home();

            return View('welcome')
            ->with('titulo', $registros_home['titulo'])
            ->with('Solicitudes', $registros_home['Solicitudes'])
            ->with('Solicitudes_Alarmas', $registros_home['Solicitudes_Alarmas'])
            ->with('Autorizaciones', $registros_home['Autorizaciones'])
            ->with('cant_autorizaciones', $registros_home['cant_autorizaciones'])
            ->with('mensaje_welcome', $registros_home['mensaje_welcome']);
        }

    }

    public function dashboardOE()
    {   
        //dd($_POST['periodo']);

        $where_filtros = '';


        $periodo = $_POST['periodo'];
        $periodo_mostrar = $_POST['periodo_mostrar'];

        if ($_POST['periodo'] <> '') {
            $periodo = $_POST['periodo'];
            $periodo = explode('|', $periodo);
            $desde = $periodo[0];
            $hasta = $periodo[1];

            if ($where_filtros <> '') {                
                $where_filtros .= " AND";
            }

            $where_filtros .= " (s.fecha_de_solicitud >= '$desde' AND s.fecha_de_solicitud <= '$hasta')";
        }

        if ($_POST['pais_id'] <> '') {
            $pais_id = $_POST['pais_id'];

            if ($where_filtros <> '') {                
                $where_filtros .= " AND";
            }

            $where_filtros .= " p.id = $pais_id";

            $Pais = Pais::find($pais_id);
            $pais = $Pais->pais;
        }
        else {
            $pais_id = '';
            $pais = '';
        }


        DB::enableQueryLog();



        //CANTIDAD DE CAMPAÑAS (SOLICITUDES)
        $select = 'te.tipo_de_evento, COUNT(s.id) cant_campanias, SUM(s.alcances) alcance, SUM(s.impresiones) impresiones, ';
        $select .= 'SUM(CASE WHEN IFNULL(s.importe_gastado, 0) = 0 THEN IFNULL(s.monto_a_invertir, 0) ELSE s.importe_gastado END) importe';

        $Solicitudes = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        //->whereRaw('te.id NOT IN (3)')
        ->whereNotNull('s.fecha_de_solicitud')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros)      
        ->groupBy('te.tipo_de_evento')
        ->orderBy('te.tipo_de_evento')
        ->get();


        //CANTIDAD DE CAMPAÑAS X PROVINCIAS
        $select = 'p.pais, pr.provincia, COUNT(s.id) cant_campanias, ';
        $select .= 'SUM(CASE WHEN IFNULL(s.importe_gastado, 0) = 0 THEN IFNULL(s.monto_a_invertir, 0) ELSE s.importe_gastado END) importe';

        $Solicitudes_por_provincia = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->whereNotNull('s.fecha_de_solicitud')
        //->whereRaw('te.id NOT IN (3)')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros)      
        ->groupBy('p.pais', 'pr.provincia')
        ->orderBy('p.pais')
        ->orderBy('cant_campanias')
        ->get();



        //INSCRIPCIONES
        $select = 'te.id, te.tipo_de_evento detalle, ';
        $select .= 'COUNT(DISTINCT i.id) cant_inscriptos, ';
        $select .= 'SUM(CASE WHEN i.fecha_de_evento_id IS NOT NULL THEN 1 ELSE 0 END) cant_inscriptos_eligio, ';
        $select .= "SUM(CASE WHEN i.sino_envio_pedido_de_confirmacion = 'SI' IS NOT NULL THEN 1 ELSE 0 END) cant_contactados, ";
        $select .= "SUM(CASE WHEN i.sino_confirmo = 'SI' THEN 1 ELSE 0 END) cant_confirmo, ";
        $select .= "SUM(CASE WHEN i.sino_envio_voucher = 'SI' THEN 1 ELSE 0 END) cant_voucher, ";
        $select .= "SUM(CASE WHEN i.sino_envio_motivacion = 'SI' THEN 1 ELSE 0 END) cant_motivacion, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio = 'SI' THEN 1 ELSE 0 END) cant_recordatorio,";
        $select .= "SUM(CASE WHEN i.sino_asistio = 'SI' THEN 1 ELSE 0 END) cant_asistio, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio_proxima_clase = 'SI' OR sino_envio_recordatorio_proxima_clase_a_no_asistente = 'SI' THEN 1 ELSE 0 END) cant_recordatorio_prox, ";
        $select .= "SUM(CASE WHEN i.sino_cancelo = 'SI' THEN 1 ELSE 0 END) cant_cancelo ";

        $Inscripciones1 = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->whereNotNull('s.fecha_de_solicitud')
        //->whereRaw('te.id NOT IN (3)')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros)       
        ->groupBy('te.id', 'te.tipo_de_evento') 
        ->orderBy('te.tipo_de_evento')
        ->get();




        //INSCRIPCIONES
        $select_enc = 's.id, CONCAT("'.env('PATH_PUBLIC').'Solicitudes/solicitud/ver/", s.id) as enlace, te.tipo_de_evento, p.pais, pr.provincia, l.localidad, s.fecha_de_solicitud, s.alcances, s.impresiones';
        $select = ', COUNT(DISTINCT i.id) cant_inscriptos, ';
        $select .= 'SUM(CASE WHEN i.fecha_de_evento_id IS NOT NULL THEN 1 ELSE 0 END) cant_inscriptos_eligio, ';
        $select .= "SUM(CASE WHEN i.sino_envio_pedido_de_confirmacion = 'SI' IS NOT NULL THEN 1 ELSE 0 END) cant_contactados, ";
        $select .= "SUM(CASE WHEN i.sino_confirmo = 'SI' THEN 1 ELSE 0 END) cant_confirmo, ";
        $select .= "SUM(CASE WHEN i.sino_envio_voucher = 'SI' THEN 1 ELSE 0 END) cant_voucher, ";
        $select .= "SUM(CASE WHEN i.sino_envio_motivacion = 'SI' THEN 1 ELSE 0 END) cant_motivacion, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio = 'SI' THEN 1 ELSE 0 END) cant_recordatorio,";
        $select .= "SUM(CASE WHEN i.sino_asistio = 'SI' THEN 1 ELSE 0 END) cant_asistio, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio_proxima_clase = 'SI' OR sino_envio_recordatorio_proxima_clase_a_no_asistente = 'SI' THEN 1 ELSE 0 END) cant_recordatorio_prox, ";
        $select .= "SUM(CASE WHEN i.sino_cancelo = 'SI' THEN 1 ELSE 0 END) cant_cancelo, 1 campo_para_usar ";
        

        $Inscripciones2 = DB::table('solicitudes as s')
        ->select(DB::Raw($select_enc.$select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->whereNotNull('s.fecha_de_solicitud')
        //->whereRaw('te.id NOT IN (3)')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros)       
        ->groupBy(DB::Raw('s.id, enlace, te.tipo_de_evento, p.pais, pr.provincia, l.localidad, s.fecha_de_solicitud, s.alcances, s.impresiones')) 
        ->orderBy('te.tipo_de_evento')
        ->get();

        //dd(DB::getQueryLog());

        // PROMEDIO MUNDIAL
        $select = '"Promedio Mundial" as detalle, ';
        $select .= 'COUNT(DISTINCT i.id) cant_inscriptos, ';
        $select .= 'SUM(CASE WHEN i.fecha_de_evento_id IS NOT NULL THEN 1 ELSE 0 END) cant_inscriptos_eligio, ';
        $select .= "SUM(CASE WHEN i.sino_envio_pedido_de_confirmacion = 'SI' IS NOT NULL THEN 1 ELSE 0 END) cant_contactados, ";
        $select .= "SUM(CASE WHEN i.sino_confirmo = 'SI' THEN 1 ELSE 0 END) cant_confirmo, ";
        $select .= "SUM(CASE WHEN i.sino_envio_voucher = 'SI' THEN 1 ELSE 0 END) cant_voucher, ";
        $select .= "SUM(CASE WHEN i.sino_envio_motivacion = 'SI' THEN 1 ELSE 0 END) cant_motivacion, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio = 'SI' THEN 1 ELSE 0 END) cant_recordatorio,";
        $select .= "SUM(CASE WHEN i.sino_asistio = 'SI' THEN 1 ELSE 0 END) cant_asistio, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio_proxima_clase = 'SI' OR sino_envio_recordatorio_proxima_clase_a_no_asistente = 'SI' THEN 1 ELSE 0 END) cant_recordatorio_prox, ";
        $select .= "SUM(CASE WHEN i.sino_cancelo = 'SI' THEN 1 ELSE 0 END) cant_cancelo ";

        $Inscripciones_Mundial = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->whereNotNull('s.fecha_de_solicitud')
        //->whereRaw('te.id NOT IN (3)')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")    
        ->groupBy('detalle') 
        ->get();


        if ($Inscripciones1 <> null) {
            if ($Inscripciones_Mundial <> null) {
                $Inscripciones = $Inscripciones1->merge($Inscripciones_Mundial);
                //$Inscripciones = $Inscripciones->unique()->values()->all();
            }
            else {
                $Inscripciones = $Inscripciones1;
            }
        }
        


        // VISUALIZACIONES
        $select = 'te.tipo_de_evento, COUNT(vf.id) cant_visualizaciones';    

        $Visualizaciones = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->join('visualizaciones_de_formulario as vf', 'vf.solicitud_id', '=', 's.id')
        ->whereNotNull('s.fecha_de_solicitud')
        //->whereRaw('te.id NOT IN (3)')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros)       
        ->groupBy('te.tipo_de_evento')
        ->orderBy('te.tipo_de_evento')
        ->get();




        // CURSOS ONLINE
        if ($pais_id <> '') {
            $where_online = "p.id = $pais_id";
        }
        else {
            $where_online = "1=1";
        }

        $select = "YEAR(i.created_at) anio, MONTH(i.created_at) mes, CONCAT(MONTH(i.created_at),'/', YEAR(i.created_at)) periodo, COUNT(i.id) cant";        
        $Online_meses = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->join('paises as p', 'p.id', '=', 'i.pais_id')
        ->where('s.tipo_de_evento_id', 3)
        ->whereRaw($where_online)       
        ->groupBy('periodo')
        ->orderBy('anio')
        ->orderBy('mes')
        ->get();

        
        $select = "p.pais, i.ciudad, COUNT(i.id) cant";        
        $Online_ciudades = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->join('paises as p', 'p.id', '=', 'i.pais_id')
        ->where('s.tipo_de_evento_id', 3)
        ->whereRaw($where_online)       
        ->groupBy('p.pais', 'i.ciudad')
        ->orderBy('cant', 'desc')
        ->get();


        //DB::enableQueryLog();

        //CAMPAÑAS MAS OPTIMAS
        $select = 's.id, te.tipo_de_evento, p.pais, pr.provincia, l.localidad, s.fecha_de_solicitud, s.alcances, s.impresiones, ';
        $select .= 'CASE WHEN IFNULL(s.importe_gastado, 0) = 0 THEN IFNULL(s.monto_a_invertir, 0) ELSE s.importe_gastado END importe, ';
        $select .= '(SELECT COUNT(i.id) From inscripciones i Where i.solicitud_id = s.id) cant_inscriptos, ';
        $select .= '(SELECT COUNT(vf.id) From visualizaciones_de_formulario vf Where vf.solicitud_id = s.id) cant_visualizaciones ';

        $Solicitudes_optimas = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->whereNotNull('s.fecha_de_solicitud')
        //->whereRaw('te.id NOT IN (3)')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros) 
        ->get();



        
        //dd(DB::getQueryLog());


        return View('dashboard/dash-oe-conte')
        ->with('pais', $pais)
        ->with('periodo_mostrar', $periodo_mostrar)
        ->with('Inscripciones', $Inscripciones)
        ->with('Inscripciones1', $Inscripciones1)
        ->with('Inscripciones2', $Inscripciones2)
        ->with('Solicitudes', $Solicitudes)
        ->with('Solicitudes_por_provincia', $Solicitudes_por_provincia)
        ->with('Visualizaciones', $Visualizaciones)
        ->with('Online_meses', $Online_meses)
        ->with('Online_ciudades', $Online_ciudades)
        ->with('Solicitudes_optimas', $Solicitudes_optimas);

    }


    public function notificaciones()
    {   
        $Solicitudes = 0;
        $Autorizaciones = 0;
        $cant_solicitudes = 0;

        if (Auth::user()->rol_de_usuario_id > 0) {
            $registros_home = $this->registros_home();

            $cant_solicitudes = $registros_home['cant_solicitudes'] + $registros_home['cant_autorizaciones']+$registros_home['cant_alarmas'];
        }


        return $cant_solicitudes;
    }



    public function miCuenta()
    {   
        $user_id = Auth::user()->id;
        $User = User::find($user_id);

            DB::enableQueryLog();
        $cant_campanias = 0;
        if (Auth::user()->rol_de_usuario_id == 1) {
            $cant_campanias = Solicitud::whereNotNull('fecha_de_solicitud')->count();
        }
        if (Auth::user()->rol_de_usuario_id == 2) {
            $cant_campanias = Solicitud::whereNotNull('fecha_de_solicitud')->count();
        }
        if (Auth::user()->rol_de_usuario_id == 3) {
            $cant_campanias = Solicitud::whereNotNull('fecha_de_solicitud')->where('ejecutivo', $user_id)->count();
        }
        if (Auth::user()->rol_de_usuario_id == 4) {
            $cant_campanias = Solicitud::whereNotNull('fecha_de_solicitud')->where('user_id', $user_id)->count();
        }

        $cant_inscripciones = 0;
        if (Auth::user()->rol_de_usuario_id == 1) {
            $cant_inscripciones = Inscripcion::whereRaw('solicitud_id in (Select s.id FROM solicitudes as s WHERE s.fecha_de_solicitud IS NOT NULL)')->count();
            //dd(DB::getQueryLog());
        }
        if (Auth::user()->rol_de_usuario_id == 2) {
            $cant_inscripciones = Inscripcion::whereRaw('solicitud_id in (Select s.id FROM solicitudes as s WHERE s.fecha_de_solicitud IS NOT NULL)')->count();
        }
        if (Auth::user()->rol_de_usuario_id == 3) {
            $cant_inscripciones = Inscripcion::whereRaw('solicitud_id in (Select s.id FROM solicitudes as s WHERE s.fecha_de_solicitud IS NOT NULL and ejecutivo = '.$user_id.')')->count();
        }
        if (Auth::user()->rol_de_usuario_id == 4) {
            $cant_inscripciones = Inscripcion::whereRaw('solicitud_id in (Select s.id FROM solicitudes as s WHERE s.fecha_de_solicitud IS NOT NULL and user_id = '.$user_id.')')->count();
        }

        $cant_asistentes = 0;
        if (Auth::user()->rol_de_usuario_id == 1) {
            $cant_asistentes = Inscripcion::whereRaw('(solicitud_id in (Select s.id FROM solicitudes as s WHERE s.fecha_de_solicitud IS NOT NULL))')->where('sino_asistio', 'SI')->count();
        }
        if (Auth::user()->rol_de_usuario_id == 2) {
            $cant_asistentes = Inscripcion::whereRaw('(solicitud_id in (Select s.id FROM solicitudes as s WHERE s.fecha_de_solicitud IS NOT NULL))')->where('sino_asistio', 'SI')->count();
        }
        if (Auth::user()->rol_de_usuario_id == 3) {
            $cant_asistentes = Inscripcion::whereRaw('(solicitud_id in (Select s.id FROM solicitudes as s WHERE s.fecha_de_solicitud IS NOT NULL and ejecutivo = '.$user_id.'))')->where('sino_asistio', 'SI')->count();
        }
        if (Auth::user()->rol_de_usuario_id == 4) {
            $cant_asistentes = Inscripcion::whereRaw('(solicitud_id in (Select s.id FROM solicitudes as s WHERE s.fecha_de_solicitud IS NOT NULL and user_id = '.$user_id.'))')->where('sino_asistio', 'SI')->count();
        }

        return View('mi-cuenta')
        ->with('User', $User)
        ->with('cant_campanias', $cant_campanias)
        ->with('cant_inscripciones', $cant_inscripciones)
        ->with('cant_asistentes', $cant_asistentes);
    }


    public function registros_home() {

        $titulo = '';
        $Solicitudes = null;
        $Autorizaciones = null;
        $Solicitudes_Alarmas = null;
        $cant_solicitudes = 0;
        $cant_autorizaciones = 0;
        $cant_alarmas_1 = 0;
        $cant_alarmas_2 = 0;

        $SolicitudController = new SolicitudController();
        $paisesDelEquipo = $SolicitudController->paisesDelEquipo();
        $where_raw_rol_usuario = $paisesDelEquipo['where_raw_rol_usuario'];

        if (Auth::user()->rol_de_usuario_id <= 2) {
            $titulo = __('Solicitudes Pendientes de Aprobación');
            $Solicitudes = Solicitud::whereNull('sino_aprobado_administracion')->whereNotNull('fecha_de_solicitud')
            ->whereRaw('(sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")')
            ->get();
            $Autorizaciones = User::
            select(DB::Raw('Users.*, p.pais pais_desc'))
            ->leftjoin('paises as p', 'p.id', '=', 'users.pais_id')
            ->whereRaw('rol_de_usuario_id IS NULL')
            ->get();

            /*
            $Solicitudes_Alarmas = DB::table('solicitudes as s')
                ->select(DB::raw('distinct s.id, '))
                ->join('fechas_de_evento', 'fechas_de_evento.solicitud_id', '=', 'solicitudes.id')
                ->whereRaw('(sino_cancelada IS NULL OR sino_cancelada = "NO")')
                ->whereNotNull('fecha_de_solicitud')
                ->get();
            */


            $titulo_alarma_1 = __('Campañas con mas de un dia corriendo sin inscriptos');
            $Solicitudes_Alarmas_1 = Solicitud::
                select(DB::Raw('solicitudes.*'))
                ->join('fechas_de_evento as f', 'f.solicitud_id', '=', 'solicitudes.id')
                ->leftjoin('localidades as l', 'l.id', '=', 'solicitudes.localidad_id')
                ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
                ->leftjoin('paises as pa', 'pa.id', '=', 'p.pais_id')
                ->leftjoin('paises as pa2', 'pa2.id', '=', 'solicitudes.pais_id')
                ->where('sino_aprobado_administracion', 'SI')
                ->whereRaw('(sino_cancelada IS NULL OR sino_cancelada = "NO")')
                ->whereRaw('(Select count(i.id) from inscripciones i where i.solicitud_id = solicitudes.id) = 0')
                ->whereRaw('DATEDIFF(NOW(), f.fecha_de_inicio-6) > 2')
                ->whereNotNull('fecha_de_solicitud')
                ->whereRaw('(sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")')
                ->whereRaw('('.$where_raw_rol_usuario.')')
                ->get();
            $cant_alarmas_1 = $Solicitudes_Alarmas_1->count();

                //dd($Solicitudes_Alarmas_1);

            $titulo_alarma_2 = __('Campañas con inscriptos sin contactar por mas de un dia');
            $Solicitudes_Alarmas_2 = Solicitud::
                select(DB::Raw('solicitudes.*'))
                ->whereRaw('(solicitudes.id in (
                    Select i.solicitud_id 
                    from inscripciones i 
                    where DATEDIFF(NOW(), date_sub(i.created_at, INTERVAL 1 DAY)) > 0
                    and i.id not in (select e.inscripcion_id from envios e)
                    and i.sino_envio_pedido_de_confirmacion IS NULL))')
                ->leftjoin('localidades as l', 'l.id', '=', 'solicitudes.localidad_id')
                ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
                ->leftjoin('paises as pa', 'pa.id', '=', 'p.pais_id')
                ->leftjoin('paises as pa2', 'pa2.id', '=', 'solicitudes.pais_id')
                ->whereNotNull('fecha_de_solicitud')
                ->where('sino_aprobado_administracion', 'SI')
                ->whereRaw('(sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")')
                ->whereRaw('('.$where_raw_rol_usuario.')')
                ->get();
            $cant_alarmas_2 = $Solicitudes_Alarmas_2->count();

                //dd($Solicitudes_Alarmas_2);
            $Solicitudes_Alarmas = [
                ['titulo' => $titulo_alarma_1, 'alarmas' => $Solicitudes_Alarmas_1],
                ['titulo' => $titulo_alarma_2, 'alarmas' => $Solicitudes_Alarmas_2]
            ];
            //dd($Solicitudes_Alarmas_2[4]);
        }
        if (Auth::user()->rol_de_usuario_id == 2) {


            $titulo = __('Solicitudes Pendientes de Aprobación');
            $Solicitudes = Solicitud::whereNull('sino_aprobado_administracion')
                ->leftjoin('localidades as l', 'l.id', '=', 'solicitudes.localidad_id')
                ->leftjoin('provincias as p', 'p.id', '=', 'l.provincia_id')
                ->leftjoin('paises as pa', 'pa.id', '=', 'p.pais_id')
                ->leftjoin('paises as pa2', 'pa2.id', '=', 'solicitudes.pais_id')
                ->whereNotNull('fecha_de_solicitud')
                ->whereRaw($where_raw_rol_usuario)->get();

            $equipo_where = "";
            
            $Usuario_por_equipos = Auth::user()->usuario_por_equipo;  
            //dd($Usuario_por_equipos);
            if (count($Usuario_por_equipos) > 0) {
                $equipo_id = $Usuario_por_equipos[0]->equipo_id;  
                if ($equipo_id <> '' and intval($equipo_id)) {
                    $equipo_where = "users.equipo_id = $equipo_id or";
                }
            }
            else {
                $equipo_id = 99999;
            }

            $user_id = Auth::user()->id;            
            $Autorizaciones = User::
            select(DB::Raw('users.*, p.pais pais_desc'))
            ->leftjoin('paises as p', 'p.id', '=', 'users.pais_id')
            ->whereRaw("rol_de_usuario_id IS NULL and ($equipo_id in (SELECT ue.equipo_id from usuarios_por_equipo as ue WHERE ue.user_id = $user_id))")
            ->get();
            
        }
        if (Auth::user()->rol_de_usuario_id == 3) {
            $titulo = __('Solicitudes Pendientes de Aprobación');
            $Solicitudes = Solicitud::whereNull('sino_aprobado_administracion')->whereNotNull('fecha_de_solicitud')->where('ejecutivo', Auth::user()->id)
            ->whereRaw('(sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")')->get();
        }
        if (Auth::user()->rol_de_usuario_id == 4) {
            $titulo = __('Revisar Solicitudes Desaprobadas');
            $Solicitudes = Solicitud::where('user_id', Auth::user()->id)->where('sino_aprobado_administracion', 'NO')->whereNull('sino_aprobado_solicitar_revision')->whereNotNull('fecha_de_solicitud')
            ->whereRaw('(sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO") AND (sino_cancelada IS NULL OR sino_cancelada = "NO")')->get();
        }

        if ($Solicitudes <> null) {
            $cant_solicitudes = count($Solicitudes);    
        }
        if ($Autorizaciones <> null) {
            $cant_autorizaciones = count($Autorizaciones);
        }

        $cant_alarmas = $cant_alarmas_1 + $cant_alarmas_2;
        
        

        $mensaje_welcome = __('Bienvenido').' '.Auth::user()->name;

        $array_registros_home =[
            'titulo' => $titulo,
            'Solicitudes' => $Solicitudes,
            'Autorizaciones' => $Autorizaciones,
            'Solicitudes_Alarmas' => $Solicitudes_Alarmas,
            'cant_solicitudes' => $cant_solicitudes,
            'cant_autorizaciones' => $cant_autorizaciones,
            'cant_alarmas' => $cant_alarmas,
            'mensaje_welcome' => $mensaje_welcome
        ];

        return $array_registros_home;


    }

    public function get_paises()
    {
        $paises = Pais::orderBy('pais')->get();

        $array = array();
        $array[null] = '';
        foreach ($paises as $pais) {
            $array[$pais->id] = __($pais->pais);
        }

        return $array;
    }

    public function get_estados_de_seguimiento()
    {
        $estados_de_seguimiento = Estado_de_seguimiento::orderBy('estado_de_seguimiento')->get();

        $array = array();
        $array[null] = '';
        foreach ($estados_de_seguimiento as $estado) {
            $array[$estado->id] = __($estado->estado_de_seguimiento);
        }

        return $array;
    }
    
    public function get_provincias($pais_id = null)
    {
        if ($pais_id == null) {
            $provincias = Provincia::orderBy('provincia')->get();
        }
        else {
            $provincias = Provincia::orderBy('provincia')->where('pais_id', $pais_id)->get();
        }

        $array = array();
        foreach ($provincias as $provincia) {
            $array[$provincia->id] = $provincia->provincia;
        }

        return $array;
    }
    
    public function get_localidades($pais_id = null)
    {
        
        if ($pais_id == null) {
            $localidades = Localidad::orderBy('localidad')->get();
        }
        else {
            $localidades = Localidad::whereRaw("provincia_id in (Select id From provincias Where pais_id = $pais_id)")->orderBy('localidad')->get();
        }
        $array = array();
        foreach ($localidades as $localidad) {
            $array[$localidad->id] = $localidad->localidad;
        }

        return $array;
    }
    
    public function get_localidadesConProvincia($pais_id = null)
    {
        
        if ($pais_id == null) {
            $localidades = Localidad::select(DB::Raw('localidades.id, CONCAT(localidades.localidad, ", ",  pr.provincia) as nombre'))
            ->join('provincias as pr', 'pr.id', '=', 'localidades.provincia_id')
            ->orderBy('localidades.localidad')
            ->orderBy('pr.provincia')
            ->get();
        }
        else {
            $localidades = Localidad::select(DB::Raw('localidades.id, CONCAT(localidades.localidad, ", ",  pr.provincia) as nombre'))
            ->join('provincias as pr', 'pr.id', '=', 'localidades.provincia_id')
            ->where('pr.pais_id', $pais_id)
            ->orderBy('localidades.localidad')
            ->orderBy('pr.provincia')
            ->get();
            //dd($localidades);
        }
        $array = array();
        foreach ($localidades as $localidad) {
            $array[$localidad->id] = $localidad->nombre;
        }

        return $array;
    }

    public function get_tipos_de_evento()
    {
        $tipos_de_evento = Tipo_de_evento::all();

        $array = array();
        $array[null] = '';
        foreach ($tipos_de_evento as $tipo_de_evento) {
            $array[$tipo_de_evento->id] = $tipo_de_evento->tipo_de_evento;
        }
        
        return $array;
    }


    public function get_canales($array_canales_id = null)
    {
        if ($array_canales_id == null) {
            $where_raw = 'id in (1, 2, 5, 9, 10, 12)';
        }
        else {
            $where_raw = 'id in ('.$array_canales_id[0];
            foreach ($array_canales_id as $canal_id) {
                $where_raw .= ', '.$canal_id;
            }
            $where_raw .= ')';
        }

        $canales = Canal_de_recepcion_del_curso::orderBy('canal_de_recepcion_del_curso')->whereRaw($where_raw)->get();

        $array = array();
        $array[null] = '';
        foreach ($canales as $canal) {
            $array[$canal->id] = $canal->canal_de_recepcion_del_curso;
        }

        return $array;
    }

    public function get_equipos()
    {
        $equipos = Equipo::orderBy('equipo')->get();

        $array = array();
        $array[null] = '';
        foreach ($equipos as $equipo) {
            $array[$equipo->id] = $equipo->equipo;
        }
        
        return $array;
    }

    public function get_idiomas()
    {
        $idiomas = Idioma::all();

        $array = array();
        $array[null] = '';
        foreach ($idiomas as $idioma) {
            $array[$idioma->id] = $idioma->idioma;
        }

        return $array;
    }


    public function get_capacitaciones()
    {
        $capacitaciones = Capacitacion::all();

        $array = array();
        $array[null] = '';
        foreach ($capacitaciones as $capacitacion) {
            $array[$capacitacion->id] = $capacitacion->nombre_de_la_capacitacion;
        }

        return $array;
    }




    public function test($inscripcion_id)
    {
        $Inscripcion = Inscripcion::find($inscripcion_id);
        $url_whatsapp = $Inscripcion->url_whatsapp();
        $test = $url_whatsapp['pedido_de_confirmacion'];

        echo $test;

    }


    public function mapaDeCursosYConferencias($dias)
    {

        $Fechas_de_evento = Fecha_de_evento::
            join('solicitudes as s', 'fechas_de_evento.solicitud_id', '=', 's.id')
            ->where('s.sino_aprobado_administracion', 'SI')
            ->whereRaw('(s.sino_cancelada IS NULL OR s.sino_cancelada = "NO")')
            ->whereRaw('(DATEDIFF(NOW(), fechas_de_evento.fecha_de_inicio) <= '.$dias.')')
            ->whereNotNull('s.fecha_de_solicitud')
            ->whereRaw('(s.sino_aprobado_finalizada IS NULL OR s.sino_aprobado_finalizada = "NO") AND (s.sino_cancelada IS NULL OR s.sino_cancelada = "NO")')
            ->get();

        //$Fechas_de_evento = Fecha_de_evento::whereRaw('(id >= 369 and id <= 371)')->get();
        //dd($Fechas_de_evento->count());
        //$Fechas_de_evento = Fecha_de_evento::all();11


        $datos = [];

        foreach ($Fechas_de_evento as $Fecha_de_evento) {
            $datos[] =$Fecha_de_evento->datos_url_google_maps();
        }
        // Uncomment to see all headers
        /*
        echo "<pre>";
        print_r($a);echo"<br>";
        echo "</pre>";
        */


        return View('reportes/mapa-de-cursos-y-conferencias')
        ->with('Fechas_de_evento', $Fechas_de_evento)
        ->with('datos', $datos);

    }


    public function completarUrlRedirect()
    {

        $Fechas_de_evento = Fecha_de_evento::whereRaw('url_enlace_a_google_maps_inicio_redirect_final IS NULL')->get();
        //$Fechas_de_evento = Fecha_de_evento::where('id', 85)->get();
        foreach ($Fechas_de_evento as $Fecha_de_evento) {
        //dd($Fecha_de_evento);


            //$Fecha_de_evento = Fecha_de_evento::find($id);

            $HomeController = new HomeController();
            $ExtController = new ExtController();

            $url_enlace_a_google_maps_inicio_redirect_final = $ExtController->get_redirect_target($Fecha_de_evento->url_enlace_a_google_maps_inicio); 
            $Fecha_de_evento->url_enlace_a_google_maps_inicio_redirect_final = $url_enlace_a_google_maps_inicio_redirect_final;

            if ($Fecha_de_evento->url_enlace_a_google_maps_curso <> '') {
                $url_enlace_a_google_maps_curso_redirect_final = $ExtController->get_redirect_target($Fecha_de_evento->url_enlace_a_google_maps_curso); 
                $Fecha_de_evento->url_enlace_a_google_maps_curso_redirect_final = $url_enlace_a_google_maps_curso_redirect_final;                    
            }

            $Fecha_de_evento->save();
        }

        // Uncomment to see all headers
        /*
        echo "<pre>";
        print_r($a);echo"<br>";
        echo "</pre>";
        */
        echo 'ok';

    }



    public function estado($sino_aprobado_administracion, $sino_aprobado_solicitar_revision, $sino_cancelada, $sino_aprobado_finalizada)
    {
        $estado = '';
        $letra_estado = '';
        $class_estado  = '';
        $span_estado = '';

        if(Auth::user()) {
            $rol_de_usuario_id = Auth::user()->rol_de_usuario_id;
        }

        if (!Auth::guest()) {
            if ($sino_aprobado_administracion == 'SI' and ($sino_aprobado_finalizada == '' or $sino_aprobado_finalizada == 'NO') ) {
                $letra_estado = 'a';
                $estado = 'Aprobada';
                $class_estado = 'bg-green';
            }
            if ($rol_de_usuario_id < 3 ) {
              if ($sino_aprobado_administracion == 'NO' and ($sino_aprobado_solicitar_revision == 'NO' or $sino_aprobado_solicitar_revision == '')) {
                $letra_estado = 'd';
                $estado = 'Desaprobada';
                $class_estado = 'bg-red';
              }
            }
            else {
              if ($sino_aprobado_administracion == 'NO' ) {
                $letra_estado = 'd';
                $estado = 'Desaprobada';
                $class_estado = 'bg-red';
              }
            }
            if ($rol_de_usuario_id < 3 ) {
              if ($sino_aprobado_administracion == '') {
                $letra_estado = 'p';
                $estado = 'Pendiente';
                $class_estado = 'bg-yellow';
              }
            }
            else {
              if (($sino_aprobado_administracion == '') or ($sino_aprobado_administracion == 'NO' and $sino_aprobado_solicitar_revision == "SI")) {
                $letra_estado = 'p';
                $estado = 'Pendiente';
                $class_estado = 'bg-yellow';
              }
            }
            if ($rol_de_usuario_id < 3 ) {
              if ($sino_aprobado_administracion == "NO" AND $sino_aprobado_solicitar_revision == 'SI') {
                $letra_estado = 'r';
                $estado = 'Revisar';
                $class_estado = 'bg-yellow';
              }
            }
            else {
              if ($sino_aprobado_administracion == "NO" AND $sino_aprobado_solicitar_revision == '') {
                $letra_estado = 'r';
                $estado = 'Revisar';
                $class_estado = 'bg-yellow';
              }
            }

            if ($sino_cancelada == 'SI') {
                $letra_estado = 'c';
                $estado = 'Cancelada';
                $class_estado = 'bg-red';
            }
            if ($sino_aprobado_finalizada == 'SI') {
                $letra_estado = 'f';
                $estado = 'Finalizada';
                $class_estado = 'bg-blue';
            }
        }
        else {
            if ($sino_aprobado_administracion == 'SI' and ($sino_aprobado_finalizada == '' or $sino_aprobado_finalizada == 'NO') ) {
                $letra_estado = 'a';
                $estado = 'Aprobada';
                $class_estado = 'bg-green';
            }
            
            if ($sino_aprobado_administracion == 'NO' and ($sino_aprobado_solicitar_revision == 'NO' or $sino_aprobado_solicitar_revision == '')) {
                $letra_estado = 'd';
                $estado = 'Desaprobada';
                $class_estado = 'bg-red';
            }
            
            
            if ($sino_aprobado_administracion == '') {
                $letra_estado = 'p';
                $estado = 'Pendiente';
                $class_estado = 'bg-yellow';
            }
           
            if ($sino_aprobado_administracion == "NO" AND $sino_aprobado_solicitar_revision == 'SI') {
                $letra_estado = 'r';
                $estado = 'Revisar';
                $class_estado = 'bg-yellow';
            }
            if ($sino_cancelada == 'SI') {
                $letra_estado = 'c';
                $estado = 'Cancelada';
                $class_estado = 'bg-red';
            }
            if ($sino_aprobado_finalizada == 'SI') {
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
        //dd($sino_aprobado_administracion);
        return $array_estado;
    }



    public function rankingMundial()
    {   
        $Roles = Auth::user()->roles();

        if(in_array(7, $Roles) or in_array(9, $Roles)) {

            if (in_array(7, $Roles)) {
                $pais_id = Auth::user()->pais_id;
                $pais = '';
                if ($pais_id <> '') {
                  $pais = Auth::user()->pais->pais;
                }
            }
            else {
                $pais = '';
            }

            return View('dashboard/ranking-m')
            ->with('home', 'SI')
            ->with('pais', $pais);
        }

        else {
            $registros_home = $this->registros_home();

            return View('welcome')
            ->with('titulo', $registros_home['titulo'])
            ->with('Solicitudes', $registros_home['Solicitudes'])
            ->with('Solicitudes_Alarmas', $registros_home['Solicitudes_Alarmas'])
            ->with('Autorizaciones', $registros_home['Autorizaciones'])
            ->with('cant_autorizaciones', $registros_home['cant_autorizaciones'])
            ->with('mensaje_welcome', $registros_home['mensaje_welcome']);
        }

    }


    public function traerRankingMundial()
    {   
        //dd($_POST['periodo']);

        $where_filtros = '';


        $periodo = $_POST['periodo'];
        $periodo_mostrar = $_POST['periodo_mostrar'];

        if ($_POST['periodo'] <> '') {
            $periodo = $_POST['periodo'];
            $periodo = explode('|', $periodo);
            $desde = $periodo[0];
            $hasta = $periodo[1];

            if ($where_filtros <> '') {                
                $where_filtros .= " AND";
            }

            $where_filtros .= " (s.fecha_de_solicitud >= '$desde' AND s.fecha_de_solicitud <= '$hasta')";
        }


        //DB::enableQueryLog();



        //CANTIDAD DE CAMPAÑAS (SOLICITUDES)
        $select = 'p.pais, COUNT(s.id) cant_campanias, SUM(IFNULL(s.alcances, 0)) alcance, SUM(IFNULL(s.impresiones, 0)) impresiones, ';
        $select .= 'SUM(CASE WHEN IFNULL(s.importe_gastado, 0) = 0 THEN IFNULL(s.monto_a_invertir, 0) ELSE s.importe_gastado END) importe';

        $Solicitudes = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->whereNotNull('s.fecha_de_solicitud')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros)      
        ->groupBy('p.pais')
        ->orderBy('p.pais')
        ->get();





        //INSCRIPCIONES
        $select = 'p.pais, ';
        $select .= 'COUNT(DISTINCT i.id) cant_inscriptos, ';
        $select .= 'SUM(CASE WHEN i.fecha_de_evento_id IS NOT NULL THEN 1 ELSE 0 END) cant_inscriptos_eligio, ';
        $select .= "SUM(CASE WHEN i.sino_envio_pedido_de_confirmacion = 'SI' IS NOT NULL THEN 1 ELSE 0 END) cant_contactados, ";
        $select .= "SUM(CASE WHEN i.sino_confirmo = 'SI' THEN 1 ELSE 0 END) cant_confirmo, ";
        $select .= "SUM(CASE WHEN i.sino_envio_voucher = 'SI' THEN 1 ELSE 0 END) cant_voucher, ";
        $select .= "SUM(CASE WHEN i.sino_envio_motivacion = 'SI' THEN 1 ELSE 0 END) cant_motivacion, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio = 'SI' THEN 1 ELSE 0 END) cant_recordatorio,";
        $select .= "SUM(CASE WHEN i.sino_asistio = 'SI' THEN 1 ELSE 0 END) cant_asistio, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio_proxima_clase = 'SI' OR sino_envio_recordatorio_proxima_clase_a_no_asistente = 'SI' THEN 1 ELSE 0 END) cant_recordatorio_prox, ";
        $select .= "SUM(CASE WHEN i.sino_cancelo = 'SI' THEN 1 ELSE 0 END) cant_cancelo, 1 campo_para_usar ";

        $Inscripciones = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->whereNotNull('s.fecha_de_solicitud')
        ->whereRaw('s.id NOT IN (6, 9)')
        //->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros)       
        ->groupBy('p.pais') 
        ->orderBy('p.pais')
        ->get();


        // CURSOS ONLINE
        $where_online = " (i.created_at >= '$desde' AND i.created_at <= '$hasta')";
        
        $select = "p.pais, COUNT(i.id) cant";        
        $Online_paises = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->join('paises as p', 'p.id', '=', 'i.pais_id')
        ->where('s.tipo_de_evento_id', 3)
        ->whereRaw($where_online)   
        ->groupBy('p.pais')
        ->orderBy('cant', 'desc')
        ->get();

        $select = "p.pais, i.ciudad, COUNT(i.id) cant";        

        $Online_ciudades = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->join('paises as p', 'p.id', '=', 'i.pais_id')
        ->where('s.tipo_de_evento_id', 3)
        ->whereRaw($where_online)  
        ->groupBy('p.pais')
        ->groupBy('i.ciudad')
        ->orderBy('cant', 'desc')
        ->get();



        $select = 'p.pais, COUNT(u.id) cant';

        $Usuarios = DB::table('users as u')
        ->select(DB::Raw($select))
        ->join('paises as p', 'p.id', '=', 'u.pais_id')
        ->groupBy('p.pais')
        ->orderBy('cant', 'desc')
        ->get();

        
        //CAMPAÑAS MAS OPTIMAS
        $select = 's.id, te.tipo_de_evento, pr.pais_id, p.pais, pr.provincia, l.localidad, s.fecha_de_solicitud, s.alcances, s.impresiones, ';
        $select .= 'CASE WHEN IFNULL(s.importe_gastado, 0) = 0 THEN IFNULL(s.monto_a_invertir, 0) ELSE s.importe_gastado END importe, ';
        $select .= '(SELECT COUNT(i.id) From inscripciones i Where i.solicitud_id = s.id) cant_inscriptos, ';
        $select .= '(SELECT COUNT(vf.id) From visualizaciones_de_formulario vf Where vf.solicitud_id = s.id) cant_visualizaciones ';

        $Solicitudes_optimas = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->whereNotNull('s.fecha_de_solicitud')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros) 
        ->get();
        




        return View('dashboard/ranking-m')
        ->with('periodo', $periodo)
        ->with('periodo_mostrar', $periodo_mostrar)
        ->with('Inscripciones', $Inscripciones)
        ->with('Solicitudes', $Solicitudes)
        ->with('Online_paises', $Online_paises)
        ->with('Solicitudes_optimas', $Solicitudes_optimas)
        ->with('Online_ciudades', $Online_ciudades)
        ->with('Usuarios', $Usuarios);

    }


    public function rankingMundialJhon()
    {   
        //$Solicitudes = Solicitud::all();

        if(Auth::user()->rol_de_usuario_id == 7 or Auth::user()->rol_de_usuario_id == 9) {

            if (Auth::user()->rol_de_usuario_id <> 9) {
                $pais_id = Auth::user()->pais_id;
                $pais = '';
                if ($pais_id <> '') {
                  $pais = Auth::user()->pais->pais;
                }
            }
            else {
                $pais = '';
            }

            return View('dashboard-jhon/ranking-m-jhon')
            ->with('home', 'SI')
            ->with('pais', $pais);
        }

        else {
            $registros_home = $this->registros_home();

            return View('welcome')
            ->with('titulo', $registros_home['titulo'])
            ->with('Solicitudes', $registros_home['Solicitudes'])
            ->with('Solicitudes_Alarmas', $registros_home['Solicitudes_Alarmas'])
            ->with('Autorizaciones', $registros_home['Autorizaciones'])
            ->with('cant_autorizaciones', $registros_home['cant_autorizaciones'])
            ->with('mensaje_welcome', $registros_home['mensaje_welcome']);
        }

    }

    public function traerRankingMundialJhon()
    {   
        //dd($_POST['periodo']);

        $where_filtros = '';


        $periodo = $_POST['periodo'];
        $periodo_mostrar = $_POST['periodo_mostrar'];

        if ($_POST['periodo'] <> '') {
            $periodo = $_POST['periodo'];
            $periodo = explode('|', $periodo);
            $desde = $periodo[0];
            $hasta = $periodo[1];

            if ($where_filtros <> '') {                
                $where_filtros .= " AND";
            }

            $where_filtros .= " (s.fecha_de_solicitud >= '$desde' AND s.fecha_de_solicitud <= '$hasta')";
        }


    



        //CANTIDAD DE CAMPAÑAS (SOLICITUDES)
        $select = 'p.pais, COUNT(s.id) cant_campanias, SUM(IFNULL(s.alcances, 0)) alcance, SUM(IFNULL(s.impresiones, 0)) impresiones, ';
        $select .= 'SUM(CASE WHEN IFNULL(s.importe_gastado, 0) = 0 THEN IFNULL(s.monto_a_invertir, 0) ELSE s.importe_gastado END) importe';

        $Solicitudes = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->whereNotNull('s.fecha_de_solicitud')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros)      
        ->groupBy('p.pais')
        ->orderBy('p.pais')
        ->get();





        //INSCRIPCIONES
        $select = 'p.pais, ';
        $select .= 'COUNT(DISTINCT i.id) cant_inscriptos, ';
        $select .= 'SUM(CASE WHEN i.fecha_de_evento_id IS NOT NULL THEN 1 ELSE 0 END) cant_inscriptos_eligio, ';
        $select .= "SUM(CASE WHEN i.sino_envio_pedido_de_confirmacion = 'SI' IS NOT NULL THEN 1 ELSE 0 END) cant_contactados, ";
        $select .= "SUM(CASE WHEN i.sino_confirmo = 'SI' THEN 1 ELSE 0 END) cant_confirmo, ";
        $select .= "SUM(CASE WHEN i.sino_envio_voucher = 'SI' THEN 1 ELSE 0 END) cant_voucher, ";
        $select .= "SUM(CASE WHEN i.sino_envio_motivacion = 'SI' THEN 1 ELSE 0 END) cant_motivacion, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio = 'SI' THEN 1 ELSE 0 END) cant_recordatorio,";
        $select .= "SUM(CASE WHEN i.sino_asistio = 'SI' THEN 1 ELSE 0 END) cant_asistio, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio_proxima_clase = 'SI' OR sino_envio_recordatorio_proxima_clase_a_no_asistente = 'SI' THEN 1 ELSE 0 END) cant_recordatorio_prox, ";
        $select .= "SUM(CASE WHEN i.sino_cancelo = 'SI' THEN 1 ELSE 0 END) cant_cancelo, 1 campo_para_usar ";

        $Inscripciones = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->whereNotNull('s.fecha_de_solicitud')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros)       
        ->groupBy('p.pais') 
        ->orderBy('p.pais')
        ->get();


        // CURSOS ONLINE
        $where_online = " (i.created_at >= '$desde' AND i.created_at <= '$hasta')";
        
        $select = "p.pais, COUNT(i.id) cant";        
        $Online_paises = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->join('paises as p', 'p.id', '=', 'i.pais_id')
        ->where('s.tipo_de_evento_id', 3)
        ->whereRaw($where_online)   
        ->groupBy('p.pais')
        ->orderBy('cant', 'desc')
        ->get();

        $select = "p.pais, i.ciudad, COUNT(i.id) cant";        

        $Online_ciudades = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
        ->join('paises as p', 'p.id', '=', 'i.pais_id')
        ->where('s.tipo_de_evento_id', 3)
        ->whereRaw($where_online)  
        ->groupBy('p.pais')
        ->groupBy('i.ciudad')
        ->orderBy('cant', 'desc')
        ->get();



        $select = 'p.pais, COUNT(u.id) cant';

        $Usuarios = DB::table('users as u')
        ->select(DB::Raw($select))
        ->join('paises as p', 'p.id', '=', 'u.pais_id')
        ->groupBy('p.pais')
        ->orderBy('cant', 'desc')
        ->get();



        //CAMPAÑAS MAS OPTIMAS
        $select = 's.id, te.tipo_de_evento, pr.pais_id, p.pais, pr.provincia, l.localidad, s.fecha_de_solicitud, s.alcances, s.impresiones, ';
        $select .= 'CASE WHEN IFNULL(s.importe_gastado, 0) = 0 THEN IFNULL(s.monto_a_invertir, 0) ELSE s.importe_gastado END importe, ';
        $select .= '(SELECT COUNT(i.id) From inscripciones i Where i.solicitud_id = s.id) cant_inscriptos, ';
        $select .= '(SELECT COUNT(vf.id) From visualizaciones_de_formulario vf Where vf.solicitud_id = s.id) cant_visualizaciones ';

        $Solicitudes_optimas = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->join('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->join('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->join('paises as p', 'p.id', '=', 'pr.pais_id')
        ->whereNotNull('s.fecha_de_solicitud')
        ->whereRaw('s.id NOT IN (6, 9)')
        ->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->whereRaw($where_filtros) 
        ->get();
        
        



        return View('dashboard-jhon/ranking-m-jhon')
        ->with('periodo', $periodo)
        ->with('periodo_mostrar', $periodo_mostrar)
        ->with('Inscripciones', $Inscripciones)
        ->with('Solicitudes', $Solicitudes)
        ->with('Online_paises', $Online_paises)
        ->with('Solicitudes_optimas', $Solicitudes_optimas)
        ->with('Online_ciudades', $Online_ciudades)
        ->with('Usuarios', $Usuarios);

    }
    
    public function changePassword(Request $request) {
        $reglas = [
            'mypassword' => 'required',
            'password' => 'required|confirmed|min:6|max:18'
        ];

        $mensajes = [
            'mypassword.required' => 'El campo es requerido',
            'password.required' => 'El campo es requerido',
            'password.confirmed' => 'Los password no coinciden',
            'password.min' => 'El mínimo de caracteres es 6',
            'password.max' => 'El máximo de caracteres es 18',

        ];


        $validator = Validator::make($request->all(), $reglas, $mensajes);
        
        if ($validator->fails()) {
            $mensaje = 'error';
            return redirect(ENV('PATH_PUBLIC').'micuenta')->withErrors($validator)->with('mensaje', $mensaje);
        }
        else {
            if (Hash::check($request->mypassword, Auth::user()->password)) {
                $user = New User;
                $user->where('email', Auth::user()->email)
                ->update(['password' => bcrypt($request->password)]);

                $mensaje = 'Actualizacion de contraseña realizada exitosamente';

                return redirect(ENV('PATH_PUBLIC').'micuenta')
                ->with('mensaje', $mensaje);   
            }
            else {
                $mensaje['detalle'] = 'Error! La contraseña original no es la correcta';
                $mensaje['class'] = 'alert-warning';
                $mensaje['error'] = true;

                return redirect(ENV('PATH_PUBLIC').'micuenta')
                ->with('mensaje', $mensaje);   
            }
        }

    }
}
