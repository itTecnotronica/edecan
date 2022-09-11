<?php

namespace App\Http\Controllers;
use App\App;
use App\Inscripcion;
use App\App_usuario;
use App\App_registro;
use App\Instancia_de_seguimiento;
use App\Alumno_avanzado;

use Auth;

use Illuminate\Support\Facades\DB;



class AppController extends Controller
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


    public function checkToken($app_id, $token) {

        $App = App::find($app_id);
        
        $auth = false;

        if ($App->token == $token) {
            $auth = true;
        }

        return $auth;
    }

    public function login($app_id, $user, $pass, $token) {

        $auth = $this->checkToken($app_id, $token);
        $mensaje_de_error = null;
        $array_usuario = null;

        if ($auth) {

                if (Auth::attempt(['email' => $user, 'password' => $pass])) {
                    $user = Auth::user();
                    
                        $array_usuario = [
                            "id_persona" => Auth::user()->id,
                            "nivel_de_acceso" => 20,
                            "nombre" => Auth::user()->name,
                            "apellido" => '',
                            "tb_tipo_de_documento" => '',
                            "numero_de_documento" => '',
                            "tb_sexo" => '',
                            "fecha_de_nacimiento" => '',
                            "tf_tel_fijo" => '',
                            "tc_celular" => Auth::user()->celular,
                            "mail_correo_electronico" => Auth::user()->email,
                            "profesion_u_oficio" => Auth::user()->funcion,
                            "lumisial" => Auth::user()->lumisial,
                            "localidad" => '',
                            "provincia" => '',
                            "file_fotografia" => '',
                            "mensaje_de_error" => null
                            ];
                    
                }
                else {
                    $Usuario = DB::connection('ageacac-ar')
                    ->table('vw_personas_con_nivel_de_acceso')
                    ->select(DB::Raw('id_persona,nivel_de_acceso,nombre,apellido,tb_tipo_de_documento,numero_de_documento,tb_sexo, fecha_de_nacimiento, tf_tel_fijo, tc_celular, mail_correo_electronico, profesion_u_oficio,lumisial, localidad, provincia, file_fotografia'))
                    ->where('numero_de_documento', $user)
            
                    ->get();
                        if ($Usuario->count() > 0) {
                            $Usuario = $Usuario[0];                
                            $array_usuario = [
                                "id_persona" => $Usuario->id_persona,
                                "nivel_de_acceso" => $Usuario->nivel_de_acceso,
                                "nombre" => $Usuario->nombre,
                                "apellido" => $Usuario->apellido,
                                "tb_tipo_de_documento" => $Usuario->tb_tipo_de_documento,
                                "numero_de_documento" => $Usuario->numero_de_documento,
                                "tb_sexo" => $Usuario->tb_sexo,
                                "fecha_de_nacimiento" => $Usuario->fecha_de_nacimiento,
                                "tf_tel_fijo" =>$Usuario->tf_tel_fijo,
                                "tc_celular" => $Usuario->tc_celular,
                                "mail_correo_electronico" => $Usuario->mail_correo_electronico,
                                "profesion_u_oficio" => $Usuario->profesion_u_oficio,
                                "lumisial" => $Usuario->lumisial,
                                "localidad" => $Usuario->localidad,
                                "provincia" => $Usuario->provincia,
                                "file_fotografia" => $Usuario->file_fotografia,
                                "mensaje_de_error" => null
                            ];
                        }
                        else {
                            $array_usuario = [
                                "mensaje_de_error" => 'Usuario no encontrado'
                            ];
                        }                      
                }            
           
            $resultado = json_encode($array_usuario);
        }
        else {
            $resultado = 'ERROR!';
        }

        return response($resultado,200);
    }


    public function dashboard($app_id, $nivel_de_acceso, $token) {

        $auth = $this->checkToken($app_id, $token);

        if ($auth) {

            $App_posteos = DB::table('app_posteos as p')
                ->select(DB::Raw('ca.categoria, IFNULL(p.titulo_alternativo, c.titulo) titulo, c.descripcion, c.url_link, c.img_imagen, c.created_at, c.app_tipo_de_contenido_id'))
                ->join('app_categorias as ca', 'ca.id', '=', 'p.app_categoria_id')
                ->join('app_contenidos as c', 'c.id', '=', 'p.app_contenido_id')
                ->join('app_niveles_de_acceso as na', 'na.id', '=', 'ca.app_nivel_de_acceso_id')
                ->where('na.nivel_de_acceso', '<=', $nivel_de_acceso)
                ->where('na.app_id', $app_id)
                ->where('p.sino_publicar_en_dashboard', 'SI')
                ->orderBy('p.created_at', 'desc')
                ->get();         

            $resultado = json_encode($App_posteos);
        }
        else {

            $resultado = 'ERROR!';
        }

        return response($resultado,200);
    }


    public function categorias($app_id, $nivel_de_acceso, $token) {

        $auth = $this->checkToken($app_id, $token);

        if ($auth) {

            $App_categorias = DB::table('app_posteos as p')
                ->select(DB::Raw('DISTINCT ca.id, CONCAT(ca.categoria," - ", na.nombre_del_nivel) as categoria'))
                ->join('app_categorias as ca', 'ca.id', '=', 'p.app_categoria_id')
                ->join('app_niveles_de_acceso as na', 'na.id', '=', 'ca.app_nivel_de_acceso_id')
                ->where('na.nivel_de_acceso', '<=', $nivel_de_acceso)
                ->where('na.app_id', $app_id)
                ->orderBy('ca.categoria', 'desc')
                ->get();    

            //dd($App_categorias);
            $resultado = json_encode($App_categorias);
        }
        else {

            $resultado = 'ERROR!';
        }

        return response($resultado,200);
    }

    public function niveles($app_id, $nivel_de_acceso, $token) {

        $auth = $this->checkToken($app_id, $token);

        if ($auth) {

            $App_categorias = DB::table('app_posteos as p')
                ->select(DB::Raw('DISTINCT na.id, na.nombre_del_nivel as categoria'))
                ->join('app_categorias as ca', 'ca.id', '=', 'p.app_categoria_id')
                ->join('app_niveles_de_acceso as na', 'na.id', '=', 'ca.app_nivel_de_acceso_id')
                ->where('na.nivel_de_acceso', '<=', $nivel_de_acceso)
                ->where('na.app_id', $app_id)
                ->where('na.id', '<>', 2)
                ->orderBy('na.nombre_del_nivel', 'asc')
                ->get();    

            //dd($App_categorias);
            $resultado = json_encode($App_categorias);
        }
        else {

            $resultado = 'ERROR!';
        }

        return response($resultado,200);
    }


    public function posteos($app_id, $nivel_de_acceso, $app_categoria_id, $token) {

        $auth = $this->checkToken($app_id, $token);

        if ($auth) {

            $App_posteos = DB::table('app_posteos as p')
                ->select(DB::Raw('ca.categoria, IFNULL(p.titulo_alternativo, c.titulo) titulo, c.descripcion, c.url_link, c.img_imagen, c.created_at, c.app_tipo_de_contenido_id'))
                ->join('app_categorias as ca', 'ca.id', '=', 'p.app_categoria_id')
                ->join('app_contenidos as c', 'c.id', '=', 'p.app_contenido_id')
                ->join('app_niveles_de_acceso as na', 'na.id', '=', 'ca.app_nivel_de_acceso_id')
                ->where('na.nivel_de_acceso', '<=', $nivel_de_acceso)
                ->where('na.app_id', $app_id)
                ->where('ca.id', $app_categoria_id)
                ->orderBy('p.created_at', 'desc')
                ->get();         
            //dd($App_posteos);
            $resultado = json_encode($App_posteos);
        }
        else {

            $resultado = 'ERROR!';
        }

        return response($resultado,200);
    }
    //MD 
    public function posteosNivelAcceso($app_id, $nivel_de_acceso, $token) {

        $auth = $this->checkToken($app_id, $token);

        if ($auth) {

            $App_posteos = DB::table('app_posteos as p')
                ->select(DB::Raw('ca.categoria, IFNULL(p.titulo_alternativo, c.titulo) titulo, c.descripcion, c.url_link, c.img_imagen, c.created_at, c.app_tipo_de_contenido_id'))
                ->join('app_categorias as ca', 'ca.id', '=', 'p.app_categoria_id')
                ->join('app_contenidos as c', 'c.id', '=', 'p.app_contenido_id')
                ->join('app_niveles_de_acceso as na', 'na.id', '=', 'ca.app_nivel_de_acceso_id') 
                ->where('na.id', $nivel_de_acceso)
                ->where('na.app_id', $app_id)
                ->orderBy('c.created_at', 'desc')
                ->get();         
            //dd($App_posteos);
            $resultado = json_encode($App_posteos);
        }
        else {

            $resultado = 'ERROR!';
        }

        return response($resultado,200);
    }

    public function getLecciones($nroLeccion) {

        $subscribers = DB::table('Lecciones as lec')  
        ->select(DB::Raw('mat.id, 
                            lec.orden_de_leccion,  
                            lec.nombre_de_la_leccion, 
                            mat.app_tipo_de_contenido_id,
                            mat.titulo, 
                            mat.url_enlace, 
                            url_enlace_a_la_leccion_2 urlPortada '
                        ))
            ->join('materiales_de_leccion as mat', 'lec.id', '=', 'mat.leccion_id')
             ->where('lec.curso_id', 1)    
             ->where('lec.orden_de_leccion', $nroLeccion)             
            ->orderBy('lec.orden_de_leccion', 'desc')
            ->get();

        $resultado = json_encode($subscribers);

        return response($resultado,200);
    }

    public function getUltimaLecciones($usuario, $solicitud) {

        $subscribers = DB::table('inscripciones as ins')  
            ->select(DB::Raw('  ins.id,
                            ins.solicitud_id,
                            ins.codigo_alumno,
                            ins.ultima_leccion_vista as ultima_evaluacion  '
                        )) 
             ->where('ins.solicitud_id', $solicitud)    
             ->where('ins.app_usuario_id', $usuario)         
            ->get();

        $resultado = json_encode($subscribers);

        return response($resultado,200);
    }
    
    public function getInscriptos($cant) {

        $subscribers = Inscripcion::select(
                            'inscripciones.id', 
                            'inscripciones.nombre', 
                            'inscripciones.apellido', 
                            DB::Raw('IFNULL(inscripciones.ciudad, l.localidad) as ciudad'), 
                            DB::Raw('IFNULL(p.pais, pa.pais) as pais'),
                            's.tipo_de_evento_id',
                            'te.tipo_de_evento',
                            'f.titulo_de_conferencia_publica'
                        )
            ->leftjoin('paises as p', 'p.id', '=', 'inscripciones.pais_id')
            ->leftjoin('solicitudes as s', 's.id', '=', 'inscripciones.solicitud_id')            
            ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
            ->leftjoin('provincias as pr', 'pr.id', '=', 'l.provincia_id')
            ->leftjoin('paises as pa', 'pa.id', '=', 'pr.pais_id') 
            ->leftjoin('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id') 
            ->leftjoin('fechas_de_evento as f', 'f.id', '=', 'inscripciones.fecha_de_evento_id')
            //->where('s.tipo_de_evento_id', 1)           
            ->orderBy('id', 'desc')
            ->limit($cant)
            ->get();

        $resultado = $subscribers->toArray();

        return response($resultado,200);
    }

        
    public function getSolicitudes($celular) {
 
        $subscribers = Inscripcion::select(
                            'sol.id as Solicitud', 
                            'inscripciones.id as IdAlumno', 
                            'inscripciones.Nombre as NombreAlumno',  
                            'inscripciones.apellido as ApellidoAlumno',
                            'inscripciones.celular as CelularAlumno',
                            'inscripciones.codigo_alumno as CodAlumno',
                            'inscripciones.ultima_evaluacion as Nota',
                            'pas.pais as Pais',
                            'inscripciones.ciudad as Ciudad',
                            'inscripciones.consulta as Consulta' 
                        ) 
            ->leftjoin('solicitudes as sol', 'sol.id', '=', 'inscripciones.solicitud_id')   
            ->leftjoin('paises as pas', 'pas.id', '=', 'inscripciones.pais_id')    
            ->where('inscripciones.causa_de_baja_id', null)     
            ->where('sol.user_id', $celular)       
            //->orderBy('id', 'desc')
             ->get();

        $resultado = $subscribers->toArray();

        return response($resultado,200);
    } 

    public function actualizoLeccion($inscripcion_id, $evaluacion)
    {
        $Inscripcion = Inscripcion::find($inscripcion_id);
        $Inscripcion->ultima_evaluacion = $evaluacion;
        $Inscripcion->save();         

        $mensaje_salida = 'Guardado';
        return response($mensaje_salida,200);
    } 

    public function insertLog($modulo,$texto,$pais,$idioma,$telefono,$nombre,$idmovil,$onesignal,$coordenada)
    {
        $now = new \DateTime();
        DB::table('app_registros')->insert(
            array(  
                    'modulo' => $modulo, 
                    'dato' => $texto, 
                    'fecha' => $now, 
                    'telefono' => $telefono,
                    'pais' => $pais,
                    'idioma' => $idioma,
                    'nombre' => $nombre,
                    'idmovil' => $idmovil,
                    'onesignal' => $onesignal,
                    'coordenada' => $coordenada
                )
            );    
        $mensaje_salida = 'Guardado';
        return response($mensaje_salida,200);
    } 



    public function getAppUsuarioId($codigo_onesignal, $pais_id = null, $idioma_id = null)
    {
        $cant_app_usuarios = App_usuario::where('codigo_onesignal', $codigo_onesignal)->count();
        
        if ($cant_app_usuarios > 0) {
            $App_usuario = App_usuario::where('codigo_onesignal', $codigo_onesignal)->get();
            $app_usuario_id = $App_usuario[0]->id;
        }
        else {
            $App_usuario = New App_usuario;
            $App_usuario->codigo_onesignal = $codigo_onesignal;
            $App_usuario->pais_id = $pais_id;
            $App_usuario->idioma_id = $idioma_id;
            $App_usuario->save();
            
            $app_usuario_id = $App_usuario->id;
        }

        return response($app_usuario_id, 200);
    } 

    public function getCodigoApp($codAlumno) { 

        $subscribers = Inscripcion::select( 
                            'inscripciones.app_usuario_id as RegistroApp'  
                        ) 
            ->where('inscripciones.codigo_alumno', $codAlumno)         
            //->orderBy('id', 'desc')
             ->get();

        $resultado = $subscribers->toArray();

        return response($resultado,200);
    }

    public function getCoordenada($codPais, $latitud, $longitud) { 

        $subscribers = DB::table('sedes as sed')  
                ->select(DB::Raw('(acos(sin(radians(LATITUD )) * sin(radians('. $latitud .')) + 
                cos(radians(LATITUD )) * cos(radians('. $latitud .')) * 
                cos(radians(LONGITUD ) - radians('. $longitud .'))) * 6378) as 
                distanciaPunto1Punto2 , 
                sed.direccion as Direccion,
                sed.ciudad as Ciudad,
                LATITUD as Latitud,
                LONGITUD as Longitud 
                ') 
                )
            ->where('sed.pais_id', $codPais)     
            ->orderBy('distanciaPunto1Punto2', 'asc')
            ->limit(15)
             ->get();

        $resultado = $subscribers->toArray();

        return response($resultado,200);
    }

    public function webhook(Request $request) {

        $challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];

        if ($verify_token === 'abc123') {
          echo $challenge;
        }

        return response($resultado,200);
        
    }




    public function init()
    {  

        $input = file_get_contents('php://input');
        $update = json_decode($input, TRUE);

        $chatId = $update['message']['chat']['id'];
        $message = $update['message']['text'];

        switch($message) {
            case '/start':
                $response = 'Me has iniciado';
                $this->sendMessage($chatId, $response);
                break;
            case '/info':
                $response = 'Hola! Soy @trecno_bot';
                $this->sendMessage($chatId, $response);
                break;
            default:
                $response = 'No te he entendido';
                $this->sendMessage($chatId, $response);
                break;
        }

    }




    public function sendMessage($chatId, $response) {
        $url = $this->website.$this->token.'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
        file_get_contents($url);
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


    public function dialog() {

        $resultado = '123';

        return response($resultado,200);
    }


}

