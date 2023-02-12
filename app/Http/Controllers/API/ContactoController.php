<?php

namespace App\Http\Controllers;
use App\App;
use App\Inscripcion;
use App\App_usuario;
use App\App_registro;
use App\Instancia_de_seguimiento;
use App\Alumno_avanzado;
use App\Persona;
use App\Inscripcion_Evento;
use App\Debito;
use App\Carnet;
use App\Idioma;
use App\Idioma_por_pais;
use App\Pais;
use App\Localidad;
use App\Solicitud;
use App\Fecha_de_evento;
use Carbon\Carbon;

use Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;



class ContactoController extends Controller
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




    public function getListAA($pais_id, $token) {

        if ($token == 'mauricio') {

            if ($pais_id <> "") {
                $whereRaw = "p.id = $pais_id";
            }

            //dd($whereRaw);

            //DB::enableQueryLog();
            $Inscripciones = DB::table('inscripciones') 
            ->select(DB::Raw('aa.id aaid, es.estado_de_seguimiento, aa.cantidad_de_asistencias, aa.cantidad_de_evaluaciones, inscripciones.id iid, inscripciones.solicitud_id, s.hash, inscripciones.solicitud_original, cc.causa_de_cambio_de_solicitud, inscripciones.apellido, inscripciones.nombre, inscripciones.celular, inscripciones.email_correo, p.pais pais_inscripcion, p2.pais pais_solicitud, inscripciones.ciudad, lc.localidad, DATE_FORMAT(inscripciones.created_at, "%d/%M/%Y") fecha_de_inscripcion, l.nombre_de_la_leccion, inscripciones.sino_cancelo, cb.causa_de_baja, inscripciones.grupo, inscripciones.codigo_alumno, IFNULL(gs.nombre_responsable_de_inscripciones, s.nombre_responsable_de_inscripciones) nombre_responsable_de_inscripciones,  IFNULL(gs.celular_responsable_de_inscripciones, s.celular_responsable_de_inscripciones) celular_responsable_de_inscripciones, i.idioma, DATE_FORMAT(a.created_at, "%d/%M/%Y") ultima_asistencia'))
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
            ->leftjoin('asistencias as a', 'a.id', '=', DB::raw('(SELECT a.id FROM asistencias as a WHERE a.inscripcion_id = inscripciones.id ORDER BY a.created_at DESC LIMIT 1)'))
            ->orderBy('aa.id', 'desc')
            ->get(); 


            $resultado = json_encode($Inscripciones);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

    public function getListInscriptos($solicitud_id, $token) {

        if ($token == 'mauricio') {

            if ($solicitud_id <> "") {
                $whereRaw = "i.solicitud_id = $solicitud_id AND (i.sino_cancelo IS NULL or sino_cancelo = 'NO')";
            }

            //DB::enableQueryLog();
            $Inscripciones = DB::table('inscripciones as i') 
            ->select(DB::Raw('i.id, i.solicitud_id, i.apellido, i.nombre, i.celular, i.email_correo, p.pais, i.ciudad, i.consulta, i.fecha_de_evento_id, i.sino_notificar_proximos_eventos, i.created_at, i.observaciones, i.campania_id, i.sino_invitado_al_curso_online, cb.causa_de_baja, i.grupo, i.codigo_alumno, i.solicitud_original, cc.causa_de_cambio_de_solicitud, i.sino_envio_certificado, i.sino_ingreso_a_segunda_camara, i.sino_eleccion_modalidad_online'))
            ->leftjoin('paises as p', 'p.id', '=', 'i.pais_id')
            ->leftjoin('causas_de_baja as cb', 'cb.id', '=', 'i.causa_de_baja_id')
            ->leftjoin('causas_de_cambio_de_solicitud as cc', 'cc.id', '=', 'i.causa_de_cambio_de_solicitud_id')
            ->whereRaw($whereRaw)
            ->orderBy('i.id', 'asc')
            ->get(); 

            $resultado = json_encode($Inscripciones);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

    public function actualizarEstadoAlumno($inscripcion_id, $estado_de_seguimiento_id, $observaciones, $user_id, $token) {

        if ($token == 'mauricio') {
            $Instancia_de_seguimiento = new Instancia_de_seguimiento();
            $Instancia_de_seguimiento->estado_de_seguimiento_id = $estado_de_seguimiento_id;
            $Instancia_de_seguimiento->inscripcion_id = $inscripcion_id;
            $Instancia_de_seguimiento->observaciones = $observaciones;
            $Instancia_de_seguimiento->user_id = $user_id;
            $Instancia_de_seguimiento->save();

            $Alumno_avanzado = Alumno_avanzado::where('inscripcion_id', $inscripcion_id)->first();
            $Alumno_avanzado->estado_de_seguimiento_id = $estado_de_seguimiento_id;
            $Alumno_avanzado->save();

            $resultado = json_encode('Registro guardado');
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

}

