<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\Fecha_de_evento;
use App\Sede_de_difusion;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;



class ReportesController extends Controller
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

    public function listarSolicitudesEstadisticas(Request $request)
    {

        $where_filtros = '';


        if (isset($_POST['provincias'])) {
            $provincias = $_POST['provincias'];
            $where = 'pr.id in ('.$provincias[0];            
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
            
            $where_filtros .= " te.id = $tipo_de_evento_id";
        }
        
        $titulo_de_conferencia_publica = $_POST['titulo_de_conferencia_publica'];
        if ($titulo_de_conferencia_publica <> '') {           

            if ($where_filtros <> '') {                
                $where_filtros .= " AND";
            }

            $where_filtros .= " fe.titulo_de_conferencia_publica like '%$titulo_de_conferencia_publica%'";
        }

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

        if ($where_filtros == '') {  
            $where_filtros = "1=1";
        }

        $where_filtros = "($where_filtros)";

        //dd($where_filtros );

        $select = 's.id, te.tipo_de_evento, fe.titulo_de_conferencia_publica, s.fecha_de_solicitud, pr.provincia, l.localidad, s.sino_aprobado_administracion, s.sino_aprobado_solicitar_revision, s.sino_cancelada, s.sino_aprobado_finalizada, ';
        $select .= 's.paypal_value, s.observaciones, ';
        $select .= 'CASE WHEN s.importe_gastado = 0 THEN s.monto_a_invertir ELSE s.importe_gastado END importe, ';
        $select .= '(SELECT COUNT(vf.id) FROM visualizaciones_de_formulario vf WHERE vf.solicitud_id = s.id) cant_visualizaciones, ';
        $select .= '(SELECT COUNT(i2.id) FROM inscripciones i2 WHERE i2.solicitud_id = s.id) cant_inscriptos_total, ';
        $select .= '(SELECT COUNT(i2.id) FROM inscripciones i2 WHERE i2.solicitud_id = s.id AND i2.fecha_de_evento_id IS NULL) cant_inscriptos_sin_evento, ';
        $select .= 'fe.fecha_de_inicio, fe.hora_de_inicio, fe.hora_de_inicio, COUNT(DISTINCT i.id) cant_inscriptos, ';
        $select .= "SUM(CASE WHEN i.sino_envio_pedido_de_confirmacion = 'SI' IS NOT NULL THEN 1 ELSE 0 END) cant_contactados, ";
        $select .= "SUM(CASE WHEN i.sino_confirmo = 'SI' THEN 1 ELSE 0 END) cant_confirmo, ";
        $select .= "SUM(CASE WHEN i.sino_envio_voucher = 'SI' THEN 1 ELSE 0 END) cant_voucher, ";
        $select .= "SUM(CASE WHEN i.sino_envio_motivacion = 'SI' THEN 1 ELSE 0 END) cant_motivacion, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio = 'SI' THEN 1 ELSE 0 END) cant_recordatorio,";
        $select .= "SUM(CASE WHEN i.sino_asistio = 'SI' THEN 1 ELSE 0 END) cant_asistio, ";
        $select .= "SUM(CASE WHEN i.sino_envio_recordatorio_proxima_clase = 'SI' OR sino_envio_recordatorio_proxima_clase_a_no_asistente = 'SI' THEN 1 ELSE 0 END) cant_recordatorio_prox, ";
        $select .= "SUM(CASE WHEN i.sino_cancelo = 'SI' THEN 1 ELSE 0 END) cant_cancelo ";
        
        //DB::enableQueryLog();

        $Solicitudes = DB::table('solicitudes as s')
        ->select(DB::Raw($select))
        ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
        ->join('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
        ->leftjoin('provincias as pr', 'pr.id', '=', 'l.provincia_id')
        ->leftjoin('paises as p', 'p.id', '=', 'pr.pais_id')
        ->leftjoin('paises as p2', 'p2.id', '=', 's.pais_id')
        ->leftjoin('fechas_de_evento as fe', 'fe.solicitud_id', '=', 's.id')
        ->leftjoin('inscripciones as i', 'i.fecha_de_evento_id', '=', 'fe.id')
        ->whereNotNull('s.fecha_de_solicitud')
        ->whereRaw('s.id NOT IN (6, 9)')
        //->whereRaw("(sino_cancelada IS NULL OR sino_cancelada = 'NO')")
        //->whereRaw("((sino_cancelada IS NULL OR sino_cancelada = 'NO' OR (sino_cancelada = 'SI' AND (SELECT COUNT(i.id) FROM inscripciones i WHERE i.solicitud_id = s.id) > 10)))")
        ->where('p.id', Auth::user()->pais_id)
        //->whereRaw('s.id in (44, 144, 193, 198)')
        ->whereRaw($where_filtros)
        //->limit(10)
        ->groupBy('s.id')
        ->orderBy('s.id')
        ->get();

        //dd(DB::getQueryLog());
        //dd($where_filtros);

        return View('reportes/listar-solicitudes-estadisticas-traer')
        ->with('Solicitudes', $Solicitudes);

    }





    public function listaDeUsuarios(Request $request)
    {

        $Roles = Auth::user()->roles();

        if(in_array(9, $Roles) or Auth::user()->id == 1) { 
            $where_raw = '(1 = 1)';
        }
        else {
            $where_raw = "(p.id = ".Auth::user()->pais_id.')';
        }

        $select = "u.id, u.name, u.email, u.celular, MAX(ll.created_at) ultimo_login, MAX(la.created_at) ultima_accion, MAX(e.equipo) equipo, CONCAT(IFNULL(p.pais, ''), ' ', IFNULL(u.ciudad, '')) lugar, u.funcion, u.sino_activo, u.observaciones";

        $Usuarios = DB::table('users as u')
        ->select(DB::Raw($select))
        ->leftjoin('log_acciones as la', 'la.user_id', '=', 'u.id')
        ->leftjoin('log_logins as ll', 'll.user_id', '=', 'u.id')
        ->leftjoin('paises as p', 'p.id', '=', 'u.pais_id')
        ->leftjoin('usuarios_por_equipo as ue', 'ue.user_id', '=', 'u.id')
        ->leftjoin('equipos as e', 'e.id', '=', 'ue.equipo_id')
        ->whereRaw($where_raw)
        ->orderBy('p.id')
        ->groupBy(DB::Raw('u.id, u.name, u.email, u.celular, p.pais, u.ciudad, u.funcion, u.sino_activo, u.observaciones'))
        ->get();

        return View('reportes/lista-de-usuarios')
        ->with('Usuarios', $Usuarios);

    }



}
