<?php

namespace App\Http\Controllers;
use App\App;
use App\Inscripcion;
use App\App_usuario;
use App\App_registro;
use App\Instancia_de_seguimiento;
use App\Alumno_avanzado;
use App\Persona;
use App\Sede;
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
use App\Miembros_observacion;
use App\MiembroAportes;
use App\Miembros_temporales;
use App\Miembros;
use App\Movimientos_Contables;
use App\Pregunta;
use App\Respuesta;
use App\Encuesta;
use App\Miembros_pases;
use App\Http\Controllers\GenericController;
use App\Http\Controllers\NotificationController;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
 


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
                            ins.ultima_leccion_vista  as ultima_evaluacion ,
                            lec.nombre_de_la_leccion '
                        )) 
             ->join('Lecciones as lec', 'lec.id', '=', 'ins.ultima_leccion_vista')
             ->where('ins.solicitud_id', $solicitud)    
             ->where('ins.app_usuario_id', $usuario)   
             ->where('lec.curso_id', 1)          
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
                            'inscripciones.ultima_leccion_vista as LeccionEnCurso',
                            'inscripciones.ultima_evaluacion as ConfirmadaLeccion',
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
        $mensaje_salida = $resultado = json_encode('Guardado Registro');
        return response($mensaje_salida,200);
    } 
    public function log($modulo,$texto )
    {
        $now = new \DateTime();
        DB::table('app_registros')->insert(
            array(  
                    'modulo' => $modulo, 
                    'dato' => $texto, 
                    'fecha' => $now 
                )
            );    
        $mensaje_salida = $resultado = json_encode('Guardado Registro');
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
                CONVERT(LATITUD, DECIMAL(13,10))  as Latitud,
                CONVERT(LONGITUD, DECIMAL(13,10))  as Longitud 
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

    //GAPP
    public function getUsuario($pais_id, $documento, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('tb_personas')    
            ->select(DB::Raw('tb_personas.id , 
                                tb_personas.nombre, 
                                tb_personas.apellido, 
                                tb_personas.numero_de_documento, 
                                tDoc.mnemo, 
                                tNac.nacionalidad, 
                                tSex.sexo, 
                                tb_personas.fecha_de_nacimiento, 
                                tb_personas.domicilio,  
                                tLoc.localidad,  
                                tProv.provincia, 
                                tb_personas.tc_celular, 
                                tb_personas.mail_correo_electronico, 
                                tb_personas.file_fotografia, 
                                tSed.direccion AS sedeDireccion, 
                                tSed.ciudad AS sedeCiudad, 
                                tSed.provincia_estado_o_region AS sedeRegion, 
                                tSed.nombre_del_lumisial AS sedeNombreLumisial,  
                                tPar.tb_centro_id idCentro, 
                                tPar.sede_id idSede')) 
            ->leftjoin('tb_tipo_de_documentos as tDoc', 'tb_personas.tb_tipo_de_documento_id', '=', 'tDoc.id')
            ->leftjoin('tb_nacionalidads as tNac', 'tb_personas.tb_nacionalidad_id', '=', 'tNac.id')
            ->leftjoin('tb_sexos as tSex', 'tb_personas.tb_sexo_id', '=', 'tSex.id')
            ->leftjoin('tb_localidads as tLoc', 'tb_personas.tb_localidad_id', '=', 'tLoc.id')
            ->leftjoin('tb_participacions as tPar', 'tb_personas.id', '=', 'tPar.id')
            ->leftjoin('sedes as tSed', 'tPar.sede_id', '=', 'tSed.id')
            ->leftjoin('tb_centros as tcen', 'tPar.tb_centro_id', '=', 'tcen.id') 
            ->leftjoin('tb_provincias as tProv', 'tLoc.tb_provincia_id', '=', 'tProv.id') 
            ->whereRaw("trim(REPLACE(tb_personas.numero_de_documento),'.','') = ? ",[trim($documento)]) 
            ->orderBy('tb_personas.numero_de_documento', 'desc')
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

    public function saveUsuario($id,
                                    $nombre,
                                    $apellido,
                                    $tb_tipo_de_documento_id,
                                    $numero_de_documento,
                                    $nacionalidad,
                                    $sexo,
                                    $fecha_de_nacimiento,
                                    $domicilio,
                                    $localidad, 
                                    $tc_celular,
                                    $mail_correo_electronico,
                                    $token )
    {
        if ($token == 'gapp') {
            $now = new \DateTime();

            $cant_persona = Persona::where('id', $id)->count();
            try {
                // Validate the value...
            
                if ($cant_persona > 0) {
                    $Tb_persona = Persona::find($id);
                    $Tb_persona->nombre = $nombre;
                    $Tb_persona->apellido = $apellido;
                    $Tb_persona->tb_tipo_de_documento_id = $tb_tipo_de_documento_id;
                    $Tb_persona->numero_de_documento = $numero_de_documento;
                    $Tb_persona->tb_nacionalidad_id = $nacionalidad;
                    $Tb_persona->tb_sexo_id = $sexo;
                    $Tb_persona->fecha_de_nacimiento = $fecha_de_nacimiento;
                    $Tb_persona->domicilio = $domicilio;
                    $Tb_persona->tb_localidad_id = $localidad; 
                    $Tb_persona->tc_celular = $tc_celular;
                    $Tb_persona->mail_correo_electronico = $mail_correo_electronico; 
                    $Tb_persona->save();  
                }
                else {
                    $Tb_persona = New App_usuario;
                    $Tb_persona->nombre = $nombre;
                    $Tb_persona->apellido = $apellido;
                    $Tb_persona->tb_tipo_de_documento_id = $tb_tipo_de_documento_id;
                    $Tb_persona->numero_de_documento = $numero_de_documento;
                    $Tb_persona->tb_nacionalidad_id = $nacionalidad;
                    $Tb_persona->tb_sexo_id = $sexo;
                    $Tb_persona->fecha_de_nacimiento = $fecha_de_nacimiento;
                    $Tb_persona->domicilio = $domicilio;
                    $Tb_persona->tb_localidad_id = $localidad; 
                    $Tb_persona->tc_celular = $tc_celular;
                    $Tb_persona->mail_correo_electronico = $mail_correo_electronico; 
                    $Tb_persona->save(); 
                }
                $mensaje_salida = 'Guardado. Id ' . $id;
            } catch(\Illuminate\Database\QueryException $ex){ 
                 
                $mensaje_salida = $ex->getMessage();
            }
             
           
        }
        else {
            $mensaje_salida = 'ERROR';
        }        
        return response($mensaje_salida,200);
    } 

    public function getEventos($pais_id, $token) {

        if ($token == 'gapp') {
            $whereRaw = "  (tEv.fecha_fin > NOW() )"; 

            $Personas = DB::table('app_eventos AS tEv')    
            ->select(DB::Raw('tEv.id Id,  
                                CONCAT(tte.tipo_de_evento," - ", tEv.evento) Evento, 
                                tEv.fecha_inicio Fecha'))   
            ->leftjoin('app_tipos_de_eventos AS tTe', 'tEv.tb_tipo_de_evento_id', '=', 'tTe.id')   
             ->whereRaw($whereRaw)  
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

    public function getInscripcion($pais_id, $id, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('app_inscripciones_en_eventos AS ins')    
            ->select(DB::Raw('ins.id, 
                                ins.fecha_inscripcion, 
                                tTe.tipo_de_evento, 
                                tEv.evento, 
                                ins.numero, 
                                tEv.fecha_inicio,
                                tEv.fecha_fin
                                ')) 
            ->leftjoin('app_eventos AS tEv', 'ins.tb_evento_id', '=', 'tEv.id')
            ->leftjoin('app_tipos_de_eventos AS tTe', 'tEv.tb_tipo_de_evento_id', '=', 'tTe.id') 
            ->where('ins.tb_persona_id', $id)   
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

    public function getInscriptosAlEvento($pais_id, $id_evento, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('app_inscripciones_en_eventos AS ins')    
            ->select(DB::Raw('ins.id, 
                                ins.fecha_inscripcion, 
                                tTe.tipo_de_evento, 
                                tEv.evento, 
                                ins.numero, 
                                tEv.fecha_inicio,
                                tEv.fecha_fin,
                                mbr.name nombre,
                                ins.notas,
                                lumi.name Lumisial,
                                lumi.uuid Id,
                                lumi.city Ciudad,
                                pro.description Provincia 
                                ')) 
            ->leftjoin('app_eventos AS tEv', 'ins.tb_evento_id', '=', 'tEv.id')
            ->leftjoin('app_tipos_de_eventos AS tTe', 'tEv.tb_tipo_de_evento_id', '=', 'tTe.id') 
            ->leftjoin('app_miembros AS mbr', 'mbr.registration', '=', 'ins.tb_persona_id') 
            ->leftjoin('app_miembros_lumisial as lumi', 'lumi.uuid', '=', 'mbr.lumisialUuid')
            ->leftjoin('app_miembros_provincia as pro', 'pro.uuid', '=', 'lumi.stateUuid')
            ->where('ins.tb_evento_id', $id_evento)   
            ->orderBy('ins.numero', 'desc')
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

    public function getInscriptoAlEvento($pais_id, $id_evento, $id_cliente, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('app_inscripciones_en_eventos AS ins')    
            ->select(DB::Raw('ins.id, 
                                ins.fecha_inscripcion, 
                                tTe.tipo_de_evento, 
                                tEv.evento, 
                                ins.numero, 
                                tEv.fecha_inicio,
                                tEv.fecha_fin,
                                mbr.name nombre 
                                ')) 
            ->leftjoin('app_eventos AS tEv', 'ins.tb_evento_id', '=', 'tEv.id')
            ->leftjoin('app_tipos_de_eventos AS tTe', 'tEv.tb_tipo_de_evento_id', '=', 'tTe.id') 
            ->leftjoin('app_miembros AS mbr', 'mbr.registration', '=', 'ins.tb_persona_id') 
            ->where('ins.tb_evento_id', $id_evento)   
            ->where('ins.tb_persona_id', $id_cliente)   
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

    public function saveInscripcion($id, $tb_evento_id,  $tb_persona_id, $notas, $token )
 
    {
            if ($token == 'gapp') {
                $now = new \DateTime(); 
                $cant_persona_evento = Inscripcion_Evento::where('tb_evento_id', $tb_evento_id)->count(); 
                $ya_estoy_registrado = Inscripcion_Evento::where('tb_evento_id', $tb_evento_id)
                ->where('tb_persona_id', $tb_persona_id)
                ->count();  
                $cant_persona = Inscripcion_Evento::where('id', $id)->count();
                try { 
                    if ($ya_estoy_registrado > 0) {
                        $mensaje_salida = json_encode('Evento ya registrado');
                    }
                    else {
                        if ($cant_persona > 0) {
                            $Inscripcion = Inscripcion_Evento::find($id);
                            $Inscripcion->tb_evento_id = $tb_evento_id;
                            $Inscripcion->tb_persona_id = $tb_persona_id;  
                            $Inscripcion->save();  
                        }
                        else {
                            $Inscripcion = New Inscripcion_Evento;
                            $Inscripcion->tb_evento_id = $tb_evento_id;
                            $Inscripcion->tb_persona_id = $tb_persona_id;
                            $Inscripcion->numero = $cant_persona_evento;
                            $Inscripcion->notas = $notas;
                            $Inscripcion->fecha_inscripcion = $now;
                            $Inscripcion->save(); 
                        }
                        $mensaje_salida = json_encode('Evento guardado ' . $Inscripcion->id);
                    }
                   
                } catch(\Illuminate\Database\QueryException $ex){ 

                $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
    public function deleteInscripcion($id, $token )
    {
            if ($token == 'gapp') { 
                
                try { 
                    $cant_persona = Inscripcion_Evento::where('id', $id)->delete(); 
                    $mensaje_salida = json_encode('Se Borra Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 

    public function getDebito($pais_id, $id, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('app_debitos AS deb')    
            ->select(DB::Raw('deb.id,  
                                deb.confeccionado, 
                                tTar.tarjeta, 
                                tTta.tipo_de_tarjeta, 
                                deb.numero_de_tarjeta, 
                                deb.debitando, 
                                deb.monto, 
                                deb.fecha_de_inicio_de_debito, 
                                deb.fecha_de_fin_de_debito, 
                                deb.observaciones, 
                                tTde.tipo_de_debito,
                                deb.updated_at,
                                deb.preapproval_id estado,
                                fecha_de_fin_de_debito fechaVto')) 
            ->leftjoin('app_tarjetas AS tTar', 'deb.tb_tarjeta_id', '=', 'tTar.id')
            ->leftjoin('app_tipos_de_tarjetas AS tTta', 'deb.tb_tipo_de_tarjeta_id', '=', 'tTta.id') 
            ->leftjoin('app_tipos_de_debitos AS tTde', 'deb.tb_tipo_de_debito_id', '=', 'tTde.id') 
            ->where('deb.tb_persona_id', $id)   
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

   public function getDebitos($pais_id, $tb_tipo_de_debito_id, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('app_debitos AS deb')    
            ->select(DB::Raw('deb.id, 
                                mbr.name nombre, 
                                mbr.documentNumber documento, 
                                deb.confeccionado, 
                                tTar.tarjeta, 
                                tTta.tipo_de_tarjeta, 
                                deb.numero_de_tarjeta, 
                                deb.debitando, 
                                deb.monto, 
                                deb.fecha_de_inicio_de_debito, 
                                deb.fecha_de_fin_de_debito, 
                                deb.observaciones, 
                                tTde.tipo_de_debito,
                                deb.updated_at,
                                deb.preapproval_id estado,
                                fecha_de_fin_de_debito fechaVto,
                                mbr.phoneNumber ,
                                mbr.img_imagen ')) 
            ->leftjoin('app_tarjetas AS tTar', 'deb.tb_tarjeta_id', '=', 'tTar.id')
            ->leftjoin('app_tipos_de_tarjetas AS tTta', 'deb.tb_tipo_de_tarjeta_id', '=', 'tTta.id') 
            ->leftjoin('app_tipos_de_debitos AS tTde', 'deb.tb_tipo_de_debito_id', '=', 'tTde.id')  
            ->leftjoin('app_miembros AS mbr', 'mbr.registration', '=', 'deb.tb_persona_id') 
            ->where('tTde.id', $tb_tipo_de_debito_id)   
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

    public function saveDebito($id, $tb_tarjeta_id, $tb_tipo_de_tarjeta_id,  $tb_persona_id, $numero_de_tarjeta,$monto, $observaciones, $fechaVto, $tb_tipo_de_debito_id,$token )
    {
            if ($token == 'gapp') { 
                $cant_persona = Debito::where('id', $id)->count();
                try { 

                    if ($cant_persona > 0) {
                        $enDebito = Debito::find($id);
                        $enDebito->tb_persona_id = $tb_persona_id;
                        $enDebito->tb_tarjeta_id = $tb_tarjeta_id;
                        $enDebito->tb_tipo_de_tarjeta_id = $tb_tipo_de_tarjeta_id;
                        $enDebito->tb_tipo_de_debito_id = $tb_tipo_de_debito_id;
                        $enDebito->numero_de_tarjeta = $numero_de_tarjeta;
                        $enDebito->debitando = 'NO';
                        $enDebito->monto = $monto;
                        $enDebito->observaciones = $observaciones; 
                        $enDebito->fecha_de_fin_de_debito = $fechaVto;  
                        // 
                        $enDebito->save();  
                    }
                    else {
                        $enDebito = New Debito;
                        $enDebito->tb_persona_id = $tb_persona_id;
                        $enDebito->tb_tarjeta_id = $tb_tarjeta_id;
                        $enDebito->tb_tipo_de_tarjeta_id = $tb_tipo_de_tarjeta_id;
                        $enDebito->tb_tipo_de_debito_id = $tb_tipo_de_debito_id;
                        $enDebito->numero_de_tarjeta = $numero_de_tarjeta;
                        $enDebito->debitando = 'NO';
                        $enDebito->monto = $monto;
                        $enDebito->observaciones = $observaciones;
                        $enDebito->fecha_de_fin_de_debito = $fechaVto; 
                        //
                        $enDebito->save(); 
                    }
                    $mensaje_salida = json_encode('Guardado. Id ' . $id);
                } catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
    public function updateDebitoEstado($id, $estado, $token )
    {
            if ($token == 'gapp') { 
                $cant_persona = Debito::where('id', $id)->count();
                try { 

                    if ($cant_persona > 0) {
                        $enDebito = Debito::find($id);
                        $enDebito->preapproval_id = $estado;                  
                        // 
                        $enDebito->save();  
                        $mensaje_salida = json_encode('Guardado. Id ' . $id);
                    }
                    else {
                        $mensaje_salida = json_encode('Usuario no encontrato. Id ' . $id);
                    }
                   
                } catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
 public function deleteDebito($id, $token )
    {
            if ($token == 'gapp') { 
                
                try { 
                    $cant_persona = Debito::where('id', $id)->delete(); 
                    $mensaje_salida = json_encode('Se Borra Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
    public function getTarjeta($pais_id, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('app_tarjetas as tar')    
            ->select(DB::Raw('tar.id, tar.tarjeta as descripcion'))  
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }
    public function getTipoTarjeta($pais_id, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('app_tipos_de_tarjetas as tta')    
            ->select(DB::Raw('tta.id, tta.tipo_de_tarjeta as descripcion'))  
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }


        return response($resultado,200);
    }
    public function getCarnet($pais_id, $busqueda, $opcion , $token) {

        if ($token == 'gapp') {
            $whereRaw = "1=1";
            if ($busqueda != '-') {
                $whereRaw =  " (mb.documentNumber like '%$busqueda%' OR mb.name like '%$busqueda%') " ;
            }
           else
           {
            $whereRaw =  " (fecha_envio is null) " ;
            if ($opcion=='created_at'){
                $whereRaw =  " (estado=1) " ;
            }
            if ($opcion=='fecha_visto'){
                $whereRaw =  " (estado=6) " ;
            }
            if ($opcion=='fecha_autorizado'){
                $whereRaw =  " (estado=5) " ;
            }
            if ($opcion=='fecha_de_pago'){
                $whereRaw =  " (estado=2) " ;
            }
            if ($opcion=='fecha_de_confeccion'){
                $whereRaw =  " (estado=3) " ;
            }
            if ($opcion=='fecha_envio'){
                $whereRaw =  " (estado=4) " ;
            }
            
           }  
            
            $Personas = DB::table('app_carnets AS car')    
            ->select(DB::Raw('tTip.tipo_de_carnet,
                                tTip.id id_tipo_de_carnet, 
                                car.id, 
                                car.confeccionado, 
                                car.pagado, 
                                car.fecha_de_pago, 
                                car.importe_pagado, 
                                car.fecha_de_confeccion, 
                                car.fecha_envio,
                                car.fecha_visto,
                                car.fecha_autorizado,
                                car.autorizado, 
                                car.created_at, 
                                car.envio,
                                car.estado,
                                mb.documentNumber,
                                mb.`name` Nombre,
                                car.tb_persona_id persona_id,
                                mb.phoneNumber,
                                lum.`name` Lumisial,
                                lum.city Ciudad,
                                pro.description Provincia,
                                mb.img_imagen,
                                car.img_comprobante,
                                mb.id id_federacion  ')) 
            ->leftjoin('app_tipos_de_carnets AS tTip', 'car.tb_tipo_de_carnet_id', '=', 'tTip.id')  
            ->leftjoin('app_miembros AS mb', 'car.tb_persona_id', '=', 'mb.id')  
            ->leftjoin('app_miembros_lumisial as lum', 'lum.uuid', '=', 'mb.lumisialUuid') 
            ->leftjoin('app_miembros_provincia as pro', 'pro.uuid', '=', 'lum.stateUuid')
            ->whereRaw($whereRaw)   
            ->orderBy('car.created_at', 'desc')
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }
    public function getCarnetById($pais_id, $id , $token) {

        if ($token == 'gapp') { 
            
            $Personas = DB::table('app_carnets AS car')    
            ->select(DB::Raw('tTip.tipo_de_carnet,
                                tTip.id id_tipo_de_carnet, 
                                car.id, 
                                car.confeccionado, 
                                car.pagado, 
                                car.fecha_de_pago, 
                                car.importe_pagado, 
                                car.fecha_de_confeccion, 
                                car.fecha_envio,
                                car.fecha_visto,
                                car.fecha_autorizado,
                                car.autorizado, 
                                car.created_at, 
                                car.envio,
                                car.estado,
                                mb.documentNumber,
                                mb.`name` Nombre,
                                car.tb_persona_id persona_id,
                                mb.phoneNumber,
                                lum.`name` Lumisial,
                                lum.city Ciudad,
                                pro.description Provincia,
                                mb.img_imagen,
                                car.img_comprobante  ')) 
            ->leftjoin('app_tipos_de_carnets AS tTip', 'car.tb_tipo_de_carnet_id', '=', 'tTip.id')  
            ->leftjoin('app_miembros AS mb', 'car.tb_persona_id', '=', 'mb.registration')  
            ->leftjoin('app_miembros_lumisial as lum', 'lum.uuid', '=', 'mb.lumisialUuid') 
            ->leftjoin('app_miembros_provincia as pro', 'pro.uuid', '=', 'lum.stateUuid')
            ->where('car.tb_persona_id', $id )    
            ->orderBy('car.created_at', 'desc')
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }
    public function deleteCarnet($id, $token )
    {
            if ($token == 'gapp') {  
                try { 
                    Carnet::where('id', $id)->delete(); 
                    $mensaje_salida = json_encode('Se Borra Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
    public function saveCarnet($id, $tb_tipo_de_carnet_id,  $tb_persona_id, $token )
    {
            if ($token == 'gapp') { 
                
                $enCarnet = Carnet::where('id', $id)->count(); 
                try { 
                   
                    if ($enCarnet > 0) { 
                        $enCarnet->tb_tipo_de_carnet_id = $tb_tipo_de_carnet_id;
                        $enCarnet->tb_persona_id = $tb_persona_id;
                        $enCarnet->tb_cara_de_carnet_id = 1; 
                        $enCarnet->estado = 1;        
                        // 
                        $enCarnet->save();  
                    }
                    else {
                        $enCarnet = New Carnet;
                        $enCarnet->tb_tipo_de_carnet_id = $tb_tipo_de_carnet_id;
                        $enCarnet->tb_persona_id = $tb_persona_id;
                        $enCarnet->estado = 1;
                        $enCarnet->tb_cara_de_carnet_id = 1;
                        $enCarnet->confeccionado = "No";
                        $enCarnet->pagado = "No";
                        //
                        $enCarnet->save(); 
                    }
                $mensaje_salida = json_encode('Guardado. Id ' . $id);
                } catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
    public function saveCarnetEstadoPagado($id,  $token )
    {
            $now = new \DateTime();  
            if ($token == 'gapp') {  
                try {  
                    $enCarnet = Carnet::find($id);
                    $enCarnet->estado = 2;
                    $enCarnet->fecha_de_pago = $now;         
                    // 
                    $enCarnet->save();  
                    //
                    $mensaje_salida = json_encode('Guardado. Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    }
    public function saveCarnetEstadoConfeccion($id,  $token )
    {
            $now = new \DateTime();  
            if ($token == 'gapp') {  
                try {   
                    $enCarnet = Carnet::find($id);
                    $enCarnet->estado = 3; 
                    $enCarnet->fecha_de_confeccion = $now;   
                    // 
                    $enCarnet->save();  
                    //
                    $mensaje_salida = json_encode('Guardado. Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    }
    public function saveCarnetEstadoEnviado($id, $token )
    {
            $now = new \DateTime();  
            if ($token == 'gapp') {  
                try {   
                    $enCarnet = Carnet::find($id);
                    $enCarnet->estado = 4; 
                    $enCarnet->fecha_envio = $now;         
                    // 
                    $enCarnet->save();  
                    //
                    $mensaje_salida = json_encode('Guardado. Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    }
    public function saveCarnetEstadoVisto($id, $token )
    {
            $now = new \DateTime();  
            if ($token == 'gapp') {  
                try {   
                    $enCarnet = Carnet::find($id);
                    $enCarnet->estado = 6; 
                    $enCarnet->fecha_visto = $now;         
                    // 
                    $enCarnet->save();  
                    //
                    $mensaje_salida = json_encode('Guardado. Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    }
    public function saveCarnetEstadoAutorizado($id, $token )
    {
            $now = new \DateTime();  
            if ($token == 'gapp') {  
                try {   
                    $enCarnet = Carnet::find($id);
                    $enCarnet->estado = 5; 
                    $enCarnet->fecha_autorizado = $now;
                    // 
                    $enCarnet->save();  
                    //
                    $mensaje_salida = json_encode('Guardado. Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    }
    public function saveCarnetEstadoLimpiar($id, $token )
    {
            $now = new \DateTime();  
            if ($token == 'gapp') {  
                try {   
                    $enCarnet = Carnet::find($id);
                    $enCarnet->estado = 1;  
                    $enCarnet->fecha_de_pago = null;      
                    $enCarnet->fecha_de_confeccion = null; 
                    $enCarnet->fecha_envio = null;
                    $enCarnet->fecha_visto = null; 
                    $enCarnet->fecha_autorizado = null;           
                    // 
                    $enCarnet->save();  
                    //
                    $mensaje_salida = json_encode('Guardado. Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    }
    public function getTipoCarnet($pais_id, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('app_tipos_de_carnets as ttc')    
            ->select(DB::Raw('ttc.id, 
                            ttc.tipo_de_carnet as descripcion'))  
            ->where('ttc.tb_articulo_id','<>', null)   
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }
    public function uploadPagoCarnet(Request $request) {
        $now = new \DateTime();  
        $file = $request->file('file');
       $id_carnet = $request->input('id_carnet');

       // Lógica para guardar el archivo en el servidor con el ID
       $fileName = "file_{$id_carnet}.jpg";

       $path = $request->file('file')->getRealPath();    
       $logo = file_get_contents($path);
       $img_comp = base64_encode($logo);

       //$file->move(storage_path('app/public/uploads'), $fileName);

       $carnetComprobante = Carnet::find($id_carnet);
       $carnetComprobante->img_comprobante = $img_comp; 
       $carnetComprobante->estado = 2;
       $carnetComprobante->fecha_de_pago = $now;         
       $carnetComprobante->save();  

       return response()->json(['message' => 'Archivo subido con éxito '  ]);
       
   }
    public function getMaterialesSearch($idioma_id, $token, $value, $publico) {
            $whereRaw1 = " (mat.titulo like '%$value%' OR mat.descripcion like '%$value%') "; 
            if ($publico == 1){
                $whereRaw1 = $whereRaw1 . " and (mat.sino_es_un_material_publico = 'SI')";  
            }
            $Personas1 = DB::table('materiales as mat')    
            ->select(DB::Raw('aut.autor , 
                                mat.titulo, 
                                mat.descripcion, 
                                mat.url_link, 
                                mat.url_imagen, 
                                mat.anio, 
                                mat.created_at, 
                                idi.idioma, 
                                tip.tipo_de_material'))  
            ->join('autores AS aut', 'mat.autor_id', '=', 'aut.id')
            ->join('idiomas AS idi', 'mat.idioma_id', '=', 'idi.id')
            ->join('tipos_de_materiales AS tip', 'mat.tipo_de_material_id', '=', 'tip.id')
            ->where('mat.idioma_id', $idioma_id )  
            ->where('mat.sino_autorizado', 'SI' )    
            ->where('mat.app_id', '1' )  
            ->whereRaw($whereRaw1)   
           ->orderBy('mat.titulo', 'desc')
            ->get();  
            //
            $whereRaw2 = " (par.text like '%$value%' ) "; 
                if ($publico == 1){
                    $whereRaw2 = $whereRaw2 . " and (mat.sino_es_un_material_publico = 'SI')";  
                }
                $Personas2 = DB::table('materiales as mat')    
                ->select(DB::Raw('aut.autor , 
                                    mat.titulo, 
                                    par.text descripcion, 
                                    mat.url_link, 
                                    mat.url_imagen, 
                                    mat.anio, 
                                    mat.created_at, 
                                    idi.idioma, 
                                    tip.tipo_de_material,
                                    mat.playlist'))  
                ->join('autores AS aut', 'mat.autor_id', '=', 'aut.id')
                ->join('idiomas AS idi', 'mat.idioma_id', '=', 'idi.id')
                ->join('tipos_de_materiales AS tip', 'mat.tipo_de_material_id', '=', 'tip.id')
                ->join('materiales_chapters AS cha', 'cha.title', '=', 'mat.id')
                ->join('materiales_paragraphs AS par', 'par.chapter_id', '=', 'cha.id')
                ->where('mat.idioma_id', $idioma_id )  
                ->where('mat.sino_autorizado', 'SI' )    
                ->where('mat.app_id', '1' )  
                ->whereRaw($whereRaw2)   
                 ->orderBy('mat.titulo', 'desc')
                ->get(); 
                

        if ($token == 'gapp') {
           $resultado = json_encode($Personas1);
        }
        else {
            if ($token == 'biblioteca') { 
                $combinado = $Personas1->merge($Personas2);
              $resultado = json_encode($combinado);
            }
            else {
                $resultado = 'ERROR';
            }
        }
        return response($resultado,200);
    }

    public function getAllMateriales($idioma_id, $token, $tipo, $cant, $autor, $publico) {

        if ($token == 'gapp') {
            $whereRaw = " 1=1 "; 
            if ($publico == 1){
                $whereRaw = "(mat.sino_es_un_material_publico = 'SI')";  
            } 
            $whereRaw2 = " 1=1 "; 
            if ($autor != 0){
                $whereRaw2 = "(mat.autor_id = ". $autor .")";  
            } 

            $Personas = DB::table('materiales as mat')    
            ->select(DB::Raw('  mat.id id_material,
                                aut.autor , 
                                mat.titulo, 
                                mat.descripcion, 
                                mat.url_link, 
                                mat.url_imagen, 
                                mat.anio, 
                                mat.created_at, 
                                idi.idioma, 
                                tip.tipo_de_material,
                                mat.playlist '))  
            ->join('autores AS aut', 'mat.autor_id', '=', 'aut.id')
            ->join('idiomas AS idi', 'mat.idioma_id', '=', 'idi.id')
            ->join('tipos_de_materiales AS tip', 'mat.tipo_de_material_id', '=', 'tip.id')
            ->where('mat.tipo_de_material_id', $tipo )   
            ->whereRaw($whereRaw2)   
            ->where('mat.idioma_id', $idioma_id ) 
            ->where('mat.sino_autorizado', 'SI' ) 
            ->where('mat.app_id', '1' ) 
            ->whereRaw($whereRaw)   
            ->limit($cant)
            ->orderBy('mat.titulo', 'asc')
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }
        return response($resultado,200);
    }

        public function getAllMaterialesRandom($idioma_id, $token, $cant, $publico) {

        if ($token == 'gapp') {
            $whereRaw = " 1=1 "; 
            if ($publico == 1){
                $whereRaw = "(mat.sino_es_un_material_publico = 'SI')";  
            } 
            $Personas = DB::table('materiales as mat')    
            ->select(DB::Raw('  mat.id id_material,
                                aut.autor , 
                                mat.titulo, 
                                mat.descripcion, 
                                mat.url_link, 
                                mat.url_imagen, 
                                mat.anio, 
                                mat.created_at, 
                                idi.idioma, 
                                tip.tipo_de_material, FLOOR(RAND() * 500) AS random_number'))  
            ->join('autores AS aut', 'mat.autor_id', '=', 'aut.id')
            ->join('idiomas AS idi', 'mat.idioma_id', '=', 'idi.id')
            ->join('tipos_de_materiales AS tip', 'mat.tipo_de_material_id', '=', 'tip.id')  
            ->where('mat.idioma_id', $idioma_id )    
            ->where('mat.sino_autorizado', 'SI' ) 
            ->where('mat.app_id', '1' ) 
            ->whereRaw($whereRaw)   
            ->limit($cant)
            ->orderBy('random_number', 'asc')
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }
        return response($resultado,200);
    }

    public function getMiembro($token, $documento) {

        if ($token == 'gapp') {
            $Miembros = DB::table('app_miembros AS mbr')    
            ->select(DB::Raw('  mbr.id,
                                mbr.country, 
                                mbr.birth, 
                                mbr.consecration, 
                                mbr.documentNumber, 
                                mbr.documentType, 
                                mbr.email, 
                                mbr.gender, 
                                mbr.instructorCoursePlace, 
                                mbr.instructorCourseYear, 
                                mbr.sino_isActive isActive, 
                                mbr.sino_isBishop isBishop, 
                                mbr.sino_isInstructor isInstructor, 
                                mbr.sino_isInstructorRegional isInstructorRegional, 
                                mbr.sino_isMissionActive isMissionActive, 
                                mbr.sino_isMissionary isMissionary, 
                                mbr.sino_isMissionaryInternational isMissionaryInternational, 
                                mbr.sino_isPriest isPriest, 
                                mbr.sino_isPriestActive isPriestActive, 
                                mbr.lumisialUuid, 
                                mbr.missionaryAvailable, 
                                mbr.missionaryCoursePlace, 
                                mbr.`name`, 
                                mbr.nationality, 
                                mbr.priestConsecration, 
                                mbr.priestType, 
                                mbr.registration, 
                                mbr.uuid, 
                                mbr.valid, 
                                mbr.missionaryCourseYear, 
                                mbr.partnerUuid, 
                                mbr.bishopConsecration, 
                                mbr.instructorRegionalCoursePlace, 
                                mbr.missionaryInternationalCoursePlace, 
                                mbr.missionaryInternationalCourseYear, 
                                mbr.instructorRegionalCourseYear,
                                mbr.img_imagen,
                                mbr.phoneNumber,
                                mbr.responsable,
                                lum.name as Lumisial,
                                dio.name as Zona,
                                mbr.firma,
                                mbr.cargoPrincipal,
                                mbr.sino_esEditor ')) 
            ->join('app_miembros_lumisial AS lum', 'lum.uuid', '=', 'mbr.lumisialUuid')  
             ->join('app_miembros_diocesis as dio', function($join) {
                $join->on(DB::raw("FIND_IN_SET(mbr.lumisialUuid, dio.Lumisial)"), '>', DB::raw('0'));
            })
            ->where('mbr.documentNumber', $documento )    
            ->where('mbr.sino_isActive','SI' )    
            ->get(); 
            $resultado = json_encode($Miembros);
        }
        else {
            $resultado = 'ERROR';
        }
        return response($resultado,200);
    }
    public function updateMiembroFoto(Request $request) {
        
        $file = $request->file('file');
        $id_miembro = $request->input('id'); 
        // 
        $fileName = "file_{$id_miembro}.jpg";
        //
        $path = $request->file('file')->getRealPath();    
        $logo = file_get_contents($path);
        $imagen = base64_encode($logo); 
        //
        $miembro = Miembros::find($id_miembro);
        $miembro->img_imagen = 'data:image/jpeg;base64,' . $imagen; 
        $miembro->save();  

        return response()->json(['message' => 'Se actualiza imagen']);
        
    }
        public function updateMiembroFirma(Request $request) {
        
        $file = $request->file('file');
        $id_miembro = $request->input('id'); 
        // 
        $fileName = "file_{$id_miembro}.jpg";
        //
        $path = $request->file('file')->getRealPath();    
        $logo = file_get_contents($path);
        $imagen = base64_encode($logo); 
        //
        $miembro = Miembros::find($id_miembro);
        $miembro->firma = 'data:image/jpeg;base64,' . $imagen; 
        $miembro->save();  

        return response()->json(['message' => 'Se actualiza imagen']);
        
    }
    //FIN-GAPP

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

    public function getListInscriptosBR($solicitud_id, $token) {
        
        $hash = md5('GNOSIS'.$solicitud_id);

        if ($token == $hash) {

            if ($solicitud_id <> "") {
                $whereRaw = "i.solicitud_id = $solicitud_id";
            }

            //DB::enableQueryLog();
            $Inscripciones = DB::table('inscripciones as i') 
            ->select(DB::Raw('
                i.id, 
                i.nombre, 
                i.apellido, 
                i.celular, 
                i.email_correo, 
                i.fecha_de_evento_id, 
                i.consulta, 
                i.sino_confirmo,
                i.sino_cancelo,
                i.sino_envio_voucher
                '))
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



    public function getPaises($idioma_id) {
        
        $Idioma = Idioma::find($idioma_id);

        app()->setLocale($Idioma->mnemo);

        $Paises = Pais::select('id', 'pais')->get();

        $Paises->transform(function ($item, $key) {
            $itemTransf = [
                'id' => $item->id,
                'pais' => __($item->pais)
            ];
            
            return $itemTransf;
        });
         
        $sorted = $Paises->sortBy('pais');

        $Paises = $sorted->values()->all();
         

        $resultado = json_encode($Paises);


        return response($resultado,200);
    }


    public function getLocalidades() {

        $Localidades = DB::table('localidades as l')
            ->select(DB::Raw('l.id, l.localidad as city, pr.provincia as region, p.pais as country'))
            ->leftjoin('provincias as pr', 'pr.id', '=', 'l.provincia_id')
            ->leftjoin('paises as p', 'p.id', '=', 'pr.pais_id')
            //->whereNotNull('sd.latitud_y_longitud_google_maps')
            ->get();
        $resultado = json_encode($Localidades);


        return response($resultado,200);
    }

    public function getCiudades($idioma_id, $pais_id) {

        $Idioma = Idioma::find($idioma_id);
        $Pais = Pais::find($pais_id);

        app()->setLocale($Idioma->mnemo);

        $where_raw = " p.id = $pais_id";
        
        /*
        $where_raw = "(";
        $where_raw .= " p.id = $pais_id";
        $where_raw .= " AND s.sino_aprobado_administracion = 'SI'";
        $where_raw .= " AND (
                            (s.tipo_de_evento_id in (1, 2) AND DATEDIFF(NOW(), fe.fecha_de_inicio)  <= 15) or 
                            (s.tipo_de_evento_id = 3 AND DATEDIFF(NOW(), s.fecha_de_inicio_del_curso_online)  <= 15) or 
                            DATEDIFF(NOW(), s.created_at)  <= 15
                            )";
        $where_raw .= " AND (s.sino_es_campania_de_capacitacion IS NULL OR s.sino_es_campania_de_capacitacion = 'NO')";
        $where_raw .= " AND (s.sino_aprobado_finalizada IS NULL OR s.sino_aprobado_finalizada = 'NO')";
        $where_raw .= ")";
        $where_raw .= "OR (";
        $where_raw .= " p.id = $pais_id";
        $where_raw .= " AND CHAR_LENGTH(TRIM(l.url_enlace_para_formulario_inactivo)) > 5";
        $where_raw .= ")";
        */

        $Localidades = DB::table('paises as p')
            //->select(DB::Raw("l.id, l.localidad as city, pr.provincia as region, p.pais as country, s.id as solicitud_id, CONCAT('https://ac.gnosis.is/fc/', s.id, '/', s.hash,'/399') as url_form_solicitud"))
            ->select(DB::Raw("DISTINCT l.id, l.localidad as city, pr.provincia as region"))
            ->leftjoin('provincias as pr', 'p.id', '=', 'pr.pais_id')
            ->leftjoin('localidades as l', 'pr.id', '=', 'l.provincia_id')
            ->leftjoin('solicitudes as s', 's.localidad_id', '=', 'l.id')
            ->leftjoin('fechas_de_evento as fe', 's.id', '=', 'fe.solicitud_id')
            //->where('p.id', $pais_id)
            ->whereRaw($where_raw)
            ->orderBy('l.localidad')
            ->get();


        /*
        $Idiomas_por_pais = Idioma_por_pais::
            where('pais_id', $pais_id)
            ->where('idioma_id', $idioma_id)->get();

        if ($Idiomas_por_pais->count() > 0) {
            $url_form_curso_online = $Idiomas_por_pais[0]->url_form_curso_online;
        }
        else {
            $Idioma = Idioma::find($idioma_id);            
            $url_form_curso_online = $Idioma->url_form_curso_online;
        }
        */
        
        app()->setLocale($Idioma->mnemo);

        $opcion_otra = [
            'id' => -1,
            'city' => __('No encuentro mi ciudad'),
            'region' => null,
        ];

        $Localidades->push($opcion_otra);

        

        $resultado = json_encode($Localidades);

        return response($resultado,200);
    }

    public function getIdiomas() {

        $Idiomas = Idioma::select('id', 'idioma', 'mnemo')->get();
        $resultado = json_encode($Idiomas);


        return response($resultado,200);
    }


    public function getEventos2($idioma_id, $pais_id, $localidad_id) {

        $Idioma = Idioma::find($idioma_id);
        $Localidad = Localidad::find($localidad_id);

        app()->setLocale($Idioma->mnemo);

        $url_form_curso_online = '';
        $Eventos = collect();
        $id_form_curso_online = '';
        $Idioma_por_pais = null;
        $ids_extra = [];

        $idioma_id_2 = $idioma_id;
        if ($pais_id == 2 and $idioma_id == 1) {
            $idioma_id_2 = 9;
        }
        if ($pais_id == 6 and $idioma_id == 5) {
            $idioma_id_2 = 6;
        }

        $Idioma_por_pais = Idioma_por_pais::where('idioma_id', $idioma_id_2)->where('pais_id', $pais_id)->where('institucion_id', 1)->first();

        if ($localidad_id <> -1) {
            // TRAIGO LOS EVENTOS DE LA LOCALIDAD
            $Eventos = DB::table('localidades as l')
                ->select(DB::Raw("s.id as id_form_solicitud, CONCAT('https://ac.gnosis.is/fc/', s.id, '/', s.hash,'/399') as url_form_solicitud, fe.id fecha_de_evento_id, '' info_evento, te.tipo_de_evento, s.tipo_de_evento_id, fe.titulo_de_conferencia_publica, fe.fecha_de_inicio, fe.hora_de_inicio, fe.direccion_de_inicio, s.tipo_de_curso_online_id, s.fecha_de_inicio_del_curso_online, s.hora_de_inicio_del_curso_online"))
                //->select(DB::Raw("DISTINCT l.id, l.localidad as city, pr.provincia as region"))
                ->leftjoin('solicitudes as s', 's.localidad_id', '=', 'l.id')
                ->leftjoin('fechas_de_evento as fe', 's.id', '=', 'fe.solicitud_id')
                ->leftjoin('tipos_de_eventos as te', 'te.id', '=', 's.tipo_de_evento_id')
                ->where('l.id', $localidad_id)
                //->where('s.id', 4367)            
                ->where('s.sino_aprobado_administracion', 'SI')
                //->whereRaw('((s.tipo_de_evento_id in (1, 2) AND fe.fecha_de_inicio >= "2023-02-20") or (s.tipo_de_evento_id = 3 AND s.fecha_de_inicio_del_curso_online >= "2023-02-20") or s.created_at >= "2023-02-20")')
                ->whereRaw('(
                    (s.tipo_de_evento_id in (1, 2) AND DATEDIFF(NOW(), fe.fecha_de_inicio)  <= 15) or 
                    (s.tipo_de_evento_id = 3 AND DATEDIFF(NOW(), s.fecha_de_inicio_del_curso_online)  <= 15) or 
                    DATEDIFF(NOW(), s.created_at)  <= 15
                )')
                ->whereRaw("(
                    s.sino_es_campania_de_capacitacion IS NULL OR 
                    s.sino_es_campania_de_capacitacion = '' OR 
                    s.sino_es_campania_de_capacitacion = 'NO'
                )")
                ->whereRaw("(
                    s.sino_aprobado_finalizada IS NULL OR 
                    s.sino_aprobado_finalizada = '' OR 
                    s.sino_aprobado_finalizada = 'NO'
                )")
                ->whereRaw("(
                    s.id not in (23035, 23034, 22998, 22996, 23152, 23123, 23122, 23109, 23108, 23107, 23210, 23182, 23181, 23165, 23160, 23230, 23229, 23228, 23216, 23436, 23098, 23067, 23036, 23307, 23291, 23406, 23311, 23310, 23309, 23247, 23232)
                )")
                ->get();


            // CONSTRUYO EL JSON DE EVENTOS
            $Eventos->transform(function ($item, $key) use ($Idioma) {
                
                $tipo = 'html';
                $con_inicio = true;
                $Solicitud = Solicitud::find($item->id_form_solicitud);
                $Idioma_por_pais = $Solicitud->idioma_por_pais();
                $idioma = $Idioma->mnemo;
                $ver_mapa = false;
                $con_dir_inicio_distinto = false;
                $con_html = true;

                app()->setLocale($Idioma->mnemo);


                // CARGO $info_evento 
                if ($item->fecha_de_evento_id > 0) {
                    $Fecha_de_evento = Fecha_de_evento::find($item->fecha_de_evento_id);
                    $info_evento = '<h3 class="text-sm">'.$Solicitud->descripcion_sin_estado($con_html).'</h3>';
                    $info_evento .= $Fecha_de_evento->armarDetalleFechasDeEventos($tipo, $con_inicio, $Idioma_por_pais, $Solicitud, $idioma, $ver_mapa, $con_dir_inicio_distinto);            
                }
                else {                
                    $info_evento = '<h3 class="text-sm">'.$Solicitud->descripcion_sin_estado($con_html).'</h3>';
                }

                $titulo = __($item->tipo_de_evento);

                if ($item->tipo_de_evento_id == 2) {
                    $titulo .= ': '.$item->titulo_de_conferencia_publica;
                }

                $fecha_de_inicio = '';
                $fecha_de_inicio_text = '';
                $hora_de_inicio = '';
                $lugar = '';

                // CARGO EL RESTO DE PROPIEDADES
                if ($item->fecha_de_evento_id > 0) {
                    $fecha_de_inicio = $item->fecha_de_inicio;
                    $hora_de_inicio = $item->hora_de_inicio;
                    $lugar = $item->direccion_de_inicio;
                }
                else {
                    if ($item->tipo_de_evento_id == 3 and in_array($item->tipo_de_curso_online_id, [2,3,5])) {
                        $fecha_de_inicio = $item->fecha_de_inicio_del_curso_online;
                        
                        
                        setlocale(LC_TIME, $Idioma->mnemo);
                        $fecha_de_inicio_carbon = Carbon::parse($fecha_de_inicio);
                        //$fi_array = explode('-', $fecha_de_inicio);
                        //$fecha_de_inicio_carbon = Carbon::createFromDate($fi_array[0], $fi_array[1], $fi_array[2]);
                        $fecha_de_inicio_text = $fecha_de_inicio_carbon->formatLocalized('%A %d %B %Y');
                        //dd($fecha_de_inicio_text);


                        if (in_array($item->tipo_de_curso_online_id, [3,5])) {
                            $hora_de_inicio = $item->hora_de_inicio_del_curso_online;
                            $fecha_de_inicio_carbon = Carbon::parse($fecha_de_inicio.' '.$hora_de_inicio);
                            //$hi_array = explode(':', $hora_de_inicio);
                            //$fecha_de_inicio_carbon = Carbon::createFromDate($fi_array[0], $fi_array[1], $fi_array[2], $hi_array[0], $hi_array[1], $hi_array[2]);
                            $fecha_de_inicio_text = $fecha_de_inicio_carbon->formatLocalized('%A %d %B %Y of %H');
                            //dd($fecha_de_inicio_text);
                        }
                    }
                }

                // DEFINO EL OBJETO A ENVIAR
                $itemTransf = [
                    'id_form_solicitud' => $item->id_form_solicitud,
                    'url_form_solicitud' => $item->url_form_solicitud,
                    'evento' => [
                        'fecha_de_evento_id' => $item->fecha_de_evento_id,
                        'html' => $info_evento,
                        'titulo' => $titulo,
                        'fecha_de_inicio' => $fecha_de_inicio,
                        'fecha_de_inicio_text' => $fecha_de_inicio_text,
                        'hora_de_inicio' => $hora_de_inicio,
                        'lugar' => $lugar
                    ]
                ];
                
                return $itemTransf;
            });
             
            $Eventos->all();
        }


        // SI LA LOCALIDAD TIENE UN SITIO WEB O URL (url_enlace_para_formulario_inactivo)
        // LA SUMO A LOS EVENTOS
        	if ($localidad_id <> -1) {
	            $Localidad = DB::table('localidades as l')
	                ->select('url_enlace_para_formulario_inactivo')
	                ->where('l.id', $localidad_id)
	                ->first();
                if ($Localidad->url_enlace_para_formulario_inactivo) {
    	            $html = '<strong>VISITA NUESTRA PAGINA WEB LOCAL</strong><br><a href="'.$Localidad->url_enlace_para_formulario_inactivo.'">'.$Localidad->url_enlace_para_formulario_inactivo.'</a>';
    	            $opcion_otra = [
    	                'id_form_solicitud' => NULL,
    	                'url_form_solicitud' => $Localidad->url_enlace_para_formulario_inactivo,
    	                'evento' => [
    	                    'fecha_de_evento_id' => null,
    	                    'html' => $html,
    	                    'titulo' => NULL,
    	                    'fecha_de_inicio' => NULL,
    	                    'hora_de_inicio' => NULL,
    	                    'lugar' => NULL
    	                ]
    	            ];

    	            $Eventos->push($opcion_otra);
                }
                    
	        }

        // TRAIGO LOS DATOS DE LA SEDE DE LA LOCALIDAD SI EXISTE
        // LA SUMO A LOS EVENTOS
            if ($localidad_id <> -1) {
                $GenericController = new GenericController();
                $Sedes = Sede::where('localidad_id', $localidad_id)->where('sino_activa', 'SI')->get();

                foreach ($Sedes as $Sede) {

                    $html = '<strong>SEDE: </strong>'.$Sede->ciudad.'<br>';
                    $html .= $Sede->direccion.'<br>';
                    $html .= '<a href="'.$Sede->url_enlace_a_google_maps.'">Ubicación Google Maps</a><br>';
                    $html .= $Sede->direccion.'<br>';
                    $html .= 'Tel: '.$Sede->telefono_con_whatsapp.'<br>';
                    $html .= 'Email: '.$Sede->email_correo.'<br>';
                    $html .= $Sede->informacion_adicional.'<br>';

                    /*
                    $numero =  $Sede->telefono_con_whatsapp;
                    $codigo_tel = '';
                    $btn_enviar_wa = $GenericController->btn_enviar_wa($numero, $codigo_tel);
                    $html .= __('WahtsApp').': '.$Sede->telefono_con_whatsapp.' -> '.$btn_enviar_wa;
                    */

                    $opcion_otra = [
                        'id_form_solicitud' => NULL,
                        'url_form_solicitud' => $Sede->url_enlace_a_google_maps,
                        'evento' => [
                            'fecha_de_evento_id' => null,
                            'html' => $html,
                            'titulo' => NULL,
                            'fecha_de_inicio' => NULL,
                            'hora_de_inicio' => NULL,
                            'lugar' => NULL
                        ]
                    ];

                    $Eventos->push($opcion_otra);
                       
                }
            }


        // SI NO HAY EVENTOS REGISTRADOS VOY A BUSCAR EVENTOS EXTRA ONLINE
        if ($Eventos->count() == 0 and !in_array($pais_id, [11, 9]) ) {
            // SI EL IDIOMA POR PAIS TIENE UN FORM ONLINE POR DEFECTO 
            if ($Idioma_por_pais <> null) {
                $id_form_curso_online = $Idioma_por_pais->id_form_curso_online;                
                if ($id_form_curso_online > 0) {
                    $ids_extra = [$id_form_curso_online];
                }
            }

            // SI NO HAY UN FORM ONLINE POR DEFECTO EN IDIOMA POR PAIS 
            if (count($ids_extra) == 0) {

                // ASIGNO LOS FORM ONLINE EXTRA POR IDIOMA

                // ESPAÑOL
                if ($idioma_id == 1) {
                    $ids_extra = [7536];
                    //$ids_extra = [7536, 7545, 7547, 7549];
                }

                // FRANCES
                if ($idioma_id == 3) {
                    $ids_extra = [7542];
                    //$ids_extra = [7542, 7546, 7883];
                }

                // INGLES
                if ($idioma_id == 2) {
                    $ids_extra = [7544];
                    //$ids_extra = [7544, 7878, 7886];
                }

                // PORTUGUES
                if ($idioma_id == 5) {
                    $ids_extra = [7543];
                    //$ids_extra = [7543, 7879, 7884];
                }

                if (count($ids_extra) == 0) {
                    $Idioma = Idioma::find($idioma_id);            
                    $id_form_curso_online = $Idioma->id_form_curso_online;
                    if ($id_form_curso_online > 0) {
                        $ids_extra = [$id_form_curso_online];
                    }

                }
            }

            // POR CADA FORM EXTRA LO SUMO A LOS EVENTOS
            if (count($ids_extra) > 0) {
                foreach ($ids_extra as $id_form_curso_online) {
                    $Solicitud = Solicitud::find($id_form_curso_online);
                    $con_html = true;
                    $info_evento = '<h3 class="text-sm">'.$Solicitud->descripcion_sin_estado($con_html).'</h3>';

                    $con_html = false;
                    $titulo = $Solicitud->descripcion_sin_estado($con_html);

                    $fecha_de_inicio = '';
                    $hora_de_inicio = '';
                    $lugar = '';

                    if ($Solicitud->tipo_de_evento_id == 3) {
                        if (in_array($Solicitud->tipo_de_curso_online_id, [2,3,5])) {
                        $fecha_de_inicio = $Solicitud->fecha_de_inicio_del_curso_online;
                        if (in_array($Solicitud->tipo_de_curso_online_id, [3,5])) {
                            $hora_de_inicio = $Solicitud->hora_de_inicio_del_curso_online;
                        }
                    }
                    if ($Solicitud->tipo_de_curso_online_id == 4) {                    
                            if ($Solicitud->fechas_de_evento->count()>0) {
                                $fecha_de_inicio = $Solicitud->fechas_de_evento[0]->fecha_de_inicio;
                                $hora_de_inicio = $Solicitud->fechas_de_evento[0]->hora_de_inicio;
                            }        
                        }
                    }

                    $opcion_otra = [
                        'id_form_solicitud' => $id_form_curso_online,
                        'url_form_solicitud' => $Solicitud->url_form_inscripcion_con_campania_id($id_form_curso_online),
                        'evento' => [
                            'fecha_de_evento_id' => null,
                            'html' => $info_evento,
                            'titulo' => $titulo,
                            'fecha_de_inicio' => $fecha_de_inicio,
                            'hora_de_inicio' => $hora_de_inicio,
                            'lugar' => $lugar
                        ]
                    ];

                    $Eventos->push($opcion_otra);
                }
            }
        }

        $resultado = json_encode($Eventos);

        return response($resultado,200);
    }

    public function getMiembros($busqueda, $tipoMiembro, $token) {

        try { 
            $whereRaw = "";
            if ($tipoMiembro == 'M'){
                $whereRaw = "(mie.sino_isMissionary = 'SI' or mie.sino_isMissionaryInternational='SI')"; 
                if ($busqueda != '-') {
                    $whereRaw =  $whereRaw . " AND (mie.documentNumber like '%$busqueda%' OR mie.name like '%$busqueda%') " ;
                } else {
                        $whereRaw =  " 1=1"; 
                    }
            }             
            if ($tipoMiembro == 'A'){ 
                    if ($busqueda != '-') {
                        $whereRaw =  " (mie.documentNumber like '%$busqueda%' OR mie.registration like '%$busqueda%' OR mie.name like '%$busqueda%') " ;
                    }
                    else {
                        $whereRaw =  " 1=1"; 
                    }
            }  
            if ($tipoMiembro == 'L'){ 
                    $whereRaw = "(lumi.uuid = '$busqueda')";  
            }  
            if ($token == 'gapp') {           
                        $Miembros = DB::table('app_miembros AS mie')    
                        ->select(DB::Raw('  mie.id, 
                                            mie.registration, 
                                            mie.name nombre,
                                            mie.name,
                                            mie.documentNumber documento,  
                                            mie.documentNumber , 
                                            mie.email, 	 
                                            mie.sino_isPriest ,
                                            mie.priestType,
                                            mie.sino_isInstructor,
                                            mie.sino_isMissionary,
                                            mie.sino_isActive,
                                            pro.description Provincia,
                                           lumi.name Lumisial,
                                           lumi.uuid idLumisial,
                                           mie.phoneNumber,
                                           mie.img_imagen,
                                           mie.sino_esEditor,
                                           mie.sino_isBishop  '))   
                        ->leftjoin('app_miembros_lumisial as lumi', 'lumi.uuid', '=', 'mie.lumisialUuid')
                        ->leftjoin('app_miembros_provincia as pro', 'pro.uuid', '=', 'lumi.stateUuid')
                        ->whereRaw($whereRaw)  
                        ->orderBy('mie.name', 'asc')
                        ->limit(200)
                        ->get(); 
                        $resultado = json_encode($Miembros);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
            }
            
        return response($resultado,200);
    }
    
    public function getBusquedaMiembros($busqueda, $tipoMiembro, $token) {
        try { 
            $whereRaw = "";
                       
                if ($tipoMiembro == 'A'){ 
                    if ($busqueda != '-') {
                        $whereRaw =  " (mie.documentNumber like '%$busqueda%' OR mie.name like '%$busqueda%') " ;
                    }
                    else {
                        $whereRaw =  " 1=1"; 
                    }
                }  
                       
            if ($token == 'gapp') {           
                        $Miembros = DB::table('app_miembros AS mie')    
                        ->select(DB::Raw('  mie.id, 
                                            mie.registration, 
                                            mie.name nombre,
                                            mie.documentNumber documento,  
                                            mie.email, 	 
                                            mie.sino_isPriest ,
                                            mie.sino_isMissionActive'))  
                        ->whereRaw($whereRaw)  
                        ->orderBy('mie.name', 'asc')
                        ->get(); 
                        $resultado = json_encode($Miembros);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
            }
            
        return response($resultado,200);
    }
    
    public function getMiembroId($id_usuario, $token) {

        if ($token == 'gapp') {
            $Miembros = DB::table('app_miembros AS mbr')    
            ->select(DB::Raw('  mbr.country, 
                                mbr.birth, 
                                mbr.consecration, 
                                mbr.documentNumber, 
                                mbr.documentType, 
                                mbr.email, 
                                mbr.gender, 
                                mbr.instructorCoursePlace, 
                                mbr.instructorCourseYear, 
                                mbr.sino_isActive isActive, 
                                mbr.sino_isBishop isBishop, 
                                mbr.sino_isInstructor isInstructor, 
                                mbr.sino_isInstructorRegional isInstructorRegional, 
                                mbr.sino_isMissionActive isMissionActive, 
                                mbr.sino_isMissionary isMissionary, 
                                mbr.sino_isMissionaryInternational isMissionaryInternational, 
                                mbr.sino_isPriest isPriest, 
                                mbr.sino_isPriestActive isPriestActive, 
                                mbr.lumisialUuid, 
                                mbr.missionaryAvailable, 
                                mbr.missionaryCoursePlace, 
                                mbr.`name`, 
                                mbr.nationality, 
                                mbr.priestConsecration, 
                                mbr.priestType, 
                                mbr.registration, 
                                mbr.uuid, 
                                mbr.valid, 
                                mbr.missionaryCourseYear, 
                                mbr.partnerUuid, 
                                mbr.bishopConsecration, 
                                mbr.instructorRegionalCoursePlace, 
                                mbr.missionaryInternationalCoursePlace, 
                                mbr.missionaryInternationalCourseYear, 
                                mbr.instructorRegionalCourseYear,
                                mbr.img_imagen,
                                mbr.phoneNumber '))   
            ->where('mbr.registration', $id_usuario )   
            ->get(); 
            $resultado = json_encode($Miembros);
        }
        else {
            $resultado = 'ERROR';
        }
        return response($resultado,200);
    }

  public function saveMiembroObservacion($id_usuario, $nota, $opcion , $token)
{
    // 1. Validar token rápido
    if ($token !== 'gapp') {
        return response()->json(['error' => 'Token inválido'], 401);
    }

    // 2. Manejo de fecha con Carbon (más seguro en Laravel)
    $fecha = new \DateTime();

    try {
        $observacion = new Miembros_observacion;
        $observacion->fecha = $fecha ; // Formato DB
        $observacion->observacion = $nota;
        $observacion->miembro_id = $id_usuario;
        $observacion->opcion = $opcion;
        $observacion->save();

        return response()->json([
            'res' => 'Guardado',
            'id' => $id_usuario
        ], 200);

    } catch (\Exception $ex) {
        // Capturamos cualquier error, no solo de Query
        return response()->json([
            'error' => 'Error al guardar',
            'detalle' => $ex->getMessage()
        ], 500);
    }
}

  public function saveMiembroObservacionPost(Request $request, $token)
{
    if ($token !== 'gapp') {
        return response('ERROR', 401);
    }

    $id_usuario = $request->id_usuario;
    $notas = $request->notas;

    if (!$id_usuario) {
        return response('ID de usuario requerido ' . $notas, 400);
    }

    try {
        // Iniciamos una transacción para seguridad de datos
        \DB::beginTransaction();

        // 1. Borramos todas las notas previas de este miembro
        Miembros_observacion::where('miembro_id', $id_usuario)->delete();

        // 2. Si el usuario envió notas, las insertamos
        if (is_array($notas) && count($notas) > 0) {
            foreach ($notas as $nota) {
                $observacion = new Miembros_observacion;
                
                // Procesar fecha: si es string de ISO (Ionic), convertir a formato DB
                $fecha = isset($nota['fecha']) ? new \DateTime($nota['fecha']) : new \DateTime();
                
                $observacion->fecha = $fecha;
                $observacion->observacion = $nota['observacion'];
                $observacion->miembro_id = $id_usuario; 
                $observacion->opcion = $nota['opcion']; 
                 $observacion->sector = $nota['sector']; 
                $observacion->save();
            }
        }

        \DB::commit();
        return response(json_encode('Sincronizado con éxito'), 200);

    } catch(\Exception $ex){  
        \DB::rollBack(); // Si algo falla, deshace el borrado
        return response($ex->getMessage(), 500);
    }
}
 

    public function deleteMiembroObservacion($id, $token )
    {
            if ($token == 'gapp') {  
                try { 
                    $cant_persona = Miembros_observacion::where('id', $id)->delete(); 
                    $mensaje_salida = json_encode('Se Borra Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
    public function getMiembrosObservaciones( $token) {

        try {  
            
            if ($token == 'gapp') {           
                        $Notas = DB::table('app_miembros as mie')    
                        ->select(DB::Raw('  mie.`name` nombre,
                                            mie.registration,
                                            mie.documentNumber,
                                            mie.phoneNumber,
                                            mie.id,
                                            mie.sino_isInstructor isInstructor,  
                                            mie.sino_isMissionActive isMissionActive, 
                                            mie.sino_isMissionary ')) 
                                            ->leftjoin('app_miembros_observaciones AS obs', 'mie.registration', '=', 'obs.miembro_id')
                                            ->leftjoin('app_respuestas AS res', 'res.respuesta', '=', 'mie.documentNumber')
                                            ->where('mie.sino_isActive', 'SI')
                                            ->where(function ($query) {
                                                $query->whereNotNull('obs.observacion')
                                                ->orWhereNotNull('res.respuesta');  
                                            })
                                            ->groupBy('mie.name')
                                            ->groupBy('mie.registration')
                                            ->groupBy('mie.phoneNumber')
                        ->get(); 
                        $resultado = json_encode($Notas);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
            }
            
        return response($resultado,200);
    }
    public function getMiembroObservaciones( $idMiembro, $token) {

        try {  
            
            if ($token == 'gapp') {           
                        $Notas = DB::table('app_miembros_observaciones AS mie')    
                        ->select(DB::Raw('  mie.fecha, 
                                            mie.opcion,
                                            mie.observacion,
                                            mie.miembro_id,
                                            mie.id,
                                            mie.sector '))  
                        ->where('mie.miembro_id', $idMiembro)
                        ->get(); 
                        $resultado = json_encode($Notas);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
            }
            
        return response($resultado,200);
    }
    public function getLumisiales($token) {

        try {  
            
            if ($token == 'gapp') {           
                        $Consulta = DB::table('app_miembros_lumisial as lum')    
                        ->select(DB::Raw(' pro.description Provincia,
                                           lum.city Ciudad,
                                           lum.name Lumisial,
                                           lum.uuid Id '))
                        ->leftjoin('app_miembros_provincia as pro', 'pro.uuid', '=', 'lum.stateUuid')
                        ->orderBy('Lumisial', 'asc')
                         ->get(); 
                        $resultado = json_encode($Consulta);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
            }
            
        return response($resultado,200);
    }
    public function getMiembrosAportes( $idAporte, $lumisial, $year, $token) {

        try {  
            $whereRaw = "1=1";
            if ($idAporte != '0')
            {
                $whereRaw =  $whereRaw . " and (apo.id='. $idAporte .')";  
            }
            if ($lumisial != '0')
            {
                $whereRaw = $whereRaw . " and (apo.id_lumisial='$lumisial')";
            }
            if ($year != '0')
            {
                $whereRaw = $whereRaw . " and (apo.ejercicio=$year)";  
            }

            if ($token == 'gapp') {           
                        $Notas = DB::table('app_miembros_aportes as apo')    
                        ->select(DB::Raw('  apo.id Id, 
                                            mie.`name` Nombre, 
                                            lum.`name` Lumisial,
                                            apo.monto Monto , 
                                            lum.city Ciudad,
                                            apo.moneda Moneda,
                                            apo.updated_at updated_at,
                                            apo.nro_comprobante NroComprobante,
                                            apo.ejercicio Ejercicio     '))  
                        ->leftjoin('app_miembros as mie', 'mie.id', '=', 'apo.id_miembro')
                        ->leftjoin('app_miembros_lumisial as lum', 'lum.uuid', '=', 'apo.id_lumisial') 
                        ->whereRaw($whereRaw) 
                        ->orderBy('apo.updated_at', 'desc')
                        ->get(); 
                        $resultado = json_encode($Notas);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
            }
            
        return response($resultado,200);
    }
    public function getMiembroAporte( $id, $token) {

        try {  
             
            if ($token == 'gapp') {           
                        $Notas = DB::table('app_miembros_aportes as apo')    
                        ->select(DB::Raw('  apo.id Id, 
                                            mie.`name` Nombre, 
                                            lum.`name` Lumisial,
                                            apo.monto Monto ,
                                            apo.img_comprobante Imagen,
                                            lum.city Ciudad,
                                            apo.moneda Moneda,
                                            apo.updated_at updated_at,
                                            apo.nro_comprobante NroComprobante,
                                            apo.ejercicio Ejercicio     '))  
                        ->leftjoin('app_miembros as mie', 'mie.id', '=', 'apo.id_miembro')
                        ->leftjoin('app_miembros_lumisial as lum', 'lum.uuid', '=', 'apo.id_lumisial') 
                        ->where('apo.id', $id )   
                        ->get(); 
                        $resultado = json_encode($Notas);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
            }
            
        return response($resultado,200);
    }
    public function saveAporte($id_miembro, $id_lumisial, $monto , $moneda, $nro_comprobante, $ejercicio,$token )
    {
            $now = new \DateTime();
            if ($token == 'gapp') {  
                try { 
                    $aporte = New MiembroAportes;
                    $aporte->updated_at = $now;
                    $aporte->id_miembro = $id_miembro;
                    $aporte->id_lumisial = $id_lumisial; 
                    $aporte->moneda = $moneda; 
                    $aporte->monto = $monto; 
                    $aporte->nro_comprobante = $nro_comprobante;
                    $aporte->ejercicio = $ejercicio;
                    //
                    $aporte->save(); 
                    //
                    $mensaje_salida = json_encode('Guardado. Id ' . $id_miembro);
                } catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
    public function uploadAporte(Request $request) {
      
         $file = $request->file('file');
        $id_miembro_aporte = $request->input('id_miembro_aporte');

        // Lógica para guardar el archivo en el servidor con el ID
        $fileName = "file_{$id_miembro_aporte}.jpg";

        $path = $request->file('file')->getRealPath();    
        $logo = file_get_contents($path);
        $img_comp = base64_encode($logo);

        //$file->move(storage_path('app/public/uploads'), $fileName);
 
        $miembroAporte = MiembroAportes::find($id_miembro_aporte);
        $miembroAporte->img_comprobante = $img_comp; 
        $miembroAporte->save();  

        return response()->json(['message' => 'Archivo subido con éxito '  ]);
        
    }
   
    public function deleteAporte($id, $token )
    {
            if ($token == 'gapp') {  
                try { 
                   MiembroAportes::where('id', $id)->delete(); 
                    $mensaje_salida = json_encode('Se Borra Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
    public function addMiembroTemporal($documento, $nombre, $telefono, $token )
    {
        $maximo = Miembros::max('registration');
        $maximo = $maximo+1;
        $now = new \DateTime();
        if ($token == 'gapp') {  
            try {  
                $temporal_count = Miembros::where('documentNumber', $documento)->count();
                if($temporal_count>0)
                {
                    $mensaje_salida = json_encode('El documento yo esta cargado ' . $documento);
                }
                else
                {
                    $temporal = New Miembros;
                    $temporal->documentNumber = $documento;
                    $temporal->name = $nombre;
                    $temporal->phoneNumber = $telefono;
                    $temporal->created_at = $now;
                    $temporal->registration = $maximo;
                    $temporal->save(); 
                    //
                    $mensaje_salida = json_encode('Se documento registracion temporal ' . $maximo);
                } 
            } 
            catch(\Illuminate\Database\QueryException $ex){  
            $mensaje_salida = $ex->getMessage();
            }
        }
        else {
            $mensaje_salida = 'ERROR';
        }        
        return response($mensaje_salida,200);
    }
    public function updateMiembroTelefono($registration, $telefono, $token )
    { 
        $now = new \DateTime();
        if ($token == 'gapp') {  
            try {  
                $temporal_count = Miembros::where('registration', $registration)->count();
                if($temporal_count>0)
                {
                    $temporal = Miembros::where('registration', $registration)->first();
                    $temporal->phoneNumber = $telefono;
                    $temporal->updated_at = $now; 
                    $temporal->save(); 
                    //
                    $mensaje_salida = json_encode('Se actualizo el telefono ' . $telefono);
                 }
                else
                {  
                    $mensaje_salida = json_encode('NroRegistration incorrecto ' . $registration);
                } 
            } 
            catch(\Illuminate\Database\QueryException $ex){  
            $mensaje_salida = $ex->getMessage();
            }
        }
        else {
            $mensaje_salida = 'ERROR';
        }        
        return response($mensaje_salida,200);
    }   
    public function updateMiembro($registration, $campo, $valor, $token)
    {
         $now = new \DateTime();
        if ($token !== 'gapp') {
            return response()->json('ERROR: Token inválido', 401);
        }

        try {
            $miembro = Miembros::where('registration', $registration)->first();

            if (!$miembro) {
                return response()->json('NroRegistration incorrecto ' . $registration, 404);
            }

            // Normalizamos el valor a mayúsculas para campos de tipo SI/NO
            $valorUpper = strtoupper($valor);

            switch ($campo) {
                case 'telefono':
                    $miembro->phoneNumber = $valor;
                    break;
                case 'email':
                    $miembro->email = $valor;
                    break;
                case 'nombre':
                    $miembro->name = $valor;
                    break;
                case 'nroDocumento':
                    $miembro->documentNumber = $valor;
                    break;
                case 'lumisial':
                    $miembro->lumisialUuid = $valor;
                    break;
                case 'editor':
                    $miembro->sino_esEditor = $valor;
                    break;
                case 'noeditor':
                    $miembro->sino_esEditor = null;
                    break;
                case 'activo':
                case 'inactivo':
                    $miembro->sino_isActive = ($campo == 'activo') ? 'SI' : 'NO';
                    break;

                // --- NUEVOS CAMPOS ---
                case 'misionero':
                    $miembro->sino_isMissionary = ($valorUpper == 'SI') ? 'SI' : 'NO';
                    break;
                case 'instructor':
                    $miembro->sino_isInstructor = ($valorUpper == 'SI') ? 'SI' : 'NO';
                    break;
                case 'ungido':
                    $miembro->sino_isPriest = ($valorUpper == 'SI') ? 'SI' : 'NO';
                    break;                 
                case 'tipoUngido':
                    $miembro->priestType = $valor;
                    break; 
                default:
                    return response()->json('Campo no reconocido: ' . $campo, 400);
            }

            $miembro->updated_at =$now; 
            $miembro->save();

            return response()->json('Se actualizo campo ' . $campo . ' con el valor ' . $valor, 200);

        } catch (\Exception $ex) {
            // Esto te dirá si el error es "Column not found" o "Data too long"
            return response()->json([
                'error' => $ex->getMessage(),
                'line' => $ex->getLine()
            ], 500);
        }
    } 
    public function storeOrUpdateMiembro(Request $request, $id = null)
    {
        
        // 1. Intentar buscar el miembro si viene un ID, de lo contrario crear instancia vacía
        if ($request->has('id') && $request->id != null) {
            $miembro = Miembros::find($request->id);
            if (!$miembro) {
                return response()->json(['message' => 'Miembro no encontrado'], 404);
            }
        } else {
            $miembro = new Miembros();
        }
        try {            
            // 2. Asignación de campos (Mapeo manual)
            // Datos Personales
            $miembro->sino_isActive  = $request->isActive;
            $miembro->name           = $request->name;
            $miembro->documentType   = $request->documentType;
            $miembro->documentNumber = $request->documentNumber;
            $miembro->gender         = $request->gender;
            $miembro->phoneNumber    = $request->phoneNumber;
            $miembro->email          = $request->email;
            $miembro->birth          = $request->birth;
            $miembro->consecration   = $request->consecration;
            $miembro->lumisialUuid   = $request->lumisialUuid;

            // Instructor
            $miembro->sino_isInstructor         = $request->isInstructor;
            $miembro->instructorCourseYear      = $request->instructorCourseYear;
            $miembro->instructorCoursePlace     = $request->instructorCoursePlace;

            // Misionero
            $miembro->sino_isMissionary         = $request->isMissionary;
            $miembro->sino_isMissionActive      = $request->isMissionActive;
            $miembro->missionaryCoursePlace     = $request->missionaryCoursePlace;
            $miembro->missionaryCourseYear      = $request->missionaryCourseYear;

            // Misionero Internacional
            $miembro->sino_isMissionaryInternational     = $request->isMissionaryInternational;
            $miembro->missionaryInternationalCoursePlace = $request->missionaryInternationalCoursePlace;
            $miembro->missionaryInternationalCourseYear  = $request->missionaryInternationalCourseYear;

            // Sacerdocio / Episcopado
            $miembro->sino_isPriest             = $request->isPriest;
            $miembro->sino_isPriestActive       = $request->isPriestActive;
            $miembro->priestType                = $request->priestType;
            $miembro->priestConsecration        = $request->priestConsecration;
            $miembro->sino_isBishop             = $request->isBishop;
            $miembro->bishopConsecration         = $request->bishopConsecration;

            // 3. Guardar en la base de datos
            $miembro->save();
        }
        catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Operación exitosa',
            'data' => $miembro
        ], 200);
       
    }
    public function getMiembroLumisial(  $lumisial , $token) {

        try {  
            $whereRaw = "1=1"; 
            if ($lumisial != '0')
            {
                $whereRaw = "  (mie.lumisialUuid='$lumisial')";
            } 
            if ($token == 'gapp') {           
                        $lumisiales = DB::table('app_miembros as mie')    
                        ->select(DB::Raw('  mie.name  , 
                                            mie.registration  , 
                                            mie.documentNumber ,
                                            mie.id,   
                                            mie.name nombre,
                                            mie.documentNumber documento,  
                                            mie.email, 	 
                                            mie.sino_isPriest ,
                                            mie.sino_isInstructor,
                                            mie.sino_isMissionary,
                                            mie.sino_isActive,
                                            pro.description Provincia,
                                            lumi.name Lumisial,
                                            lumi.uuid idLumisial,
                                            mie.phoneNumber,
                                            mie.img_imagen,
                                            mie.sino_esEditor,
                                            mie.sino_isBishop,
                                            mie.priestType    '))  
                        ->leftjoin('app_miembros_lumisial as lumi', 'lumi.uuid', '=', 'mie.lumisialUuid')
                        ->leftjoin('app_miembros_provincia as pro', 'pro.uuid', '=', 'lumi.stateUuid')
                         ->whereRaw($whereRaw) 
                         ->where('mie.sino_isActive', 'SI' )   
                        ->orderBy('mie.name', 'asc')
                        ->get(); 
                        $resultado = json_encode($lumisiales);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
            }
            
        return response($resultado,200);
    }
    public function getAportesPorLumisial($year, $token) {

        try {   
            if ($token == 'gapp') {           
                        $lumisiales = DB::table('app_miembros_aportes as apor')    
                        ->select(DB::Raw(' lumi.`name` lumisial, 
                                            COUNT(*) cantidad,
                                            sum( monto) monto , 
                                            apor.moneda,
                                            apor.ejercicio        '))  
                        ->leftjoin('app_miembros_lumisial as lumi', 'lumi.uuid', '=', 'apor.id_lumisial')
                        ->where('apor.ejercicio', $year ) 
                        ->groupBy('lumi.name')
                        ->groupBy('apor.moneda')
                        ->groupBy('apor.ejercicio')
                        ->orderBy('lumi.name', 'asc')
                        ->get(); 
                        $resultado = json_encode($lumisiales);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
            }
            
        return response($resultado,200);
    }
    public function updateMovimientosContables(Request $request){ 
     
        if ($request->input('id') == ''){
            $id = 0;
        }
        else {
            $id = $request->input('id');
        }
        if ($request->input('token') == 'gapp') {  
            try {  
                /* 
                id: Identificador único de cada movimiento contable.
                fecha: Fecha del movimiento.
                numero_comprobante: Número del comprobante asociado al movimiento.
                descripcion: Descripción del movimiento.
                tipo_movimiento: Tipo de movimiento (por ejemplo, ingreso o egreso).
                monto: Monto del movimiento.
                cliente_proveedor: Nombre del cliente o proveedor asociado (puede ser nulo).
                moneda: Moneda en la que se realizó el movimiento.
                tipo_cambio: Tipo de cambio aplicable (puede ser nulo).
                responsable: Persona responsable del movimiento.
                notas_adicionales: Notas adicionales sobre el movimiento (puede ser nulo).
                */
                $contable_count = Movimientos_Contables::where('id',  $request->input('id'))->count();
                if ($contable_count > 0) {
                    $tb_movimiento = Movimientos_Contables::find( $request->input('id'));
                    $tb_movimiento->fecha = $request->input('fecha');
                    $tb_movimiento->numero_comprobante = $request->input('numero_comprobante');
                    $tb_movimiento->descripcion = $request->input('descripcion');
                    $tb_movimiento->tipo_movimiento = $request->input('tipo_movimiento');
                    $tb_movimiento->monto = $request->input('monto');
                    $tb_movimiento->cliente_proveedor = $request->input('cliente_proveedor');
                    $tb_movimiento->moneda = $request->input('moneda');
                    $tb_movimiento->tipo_cambio = $request->input('tipo_cambio'); 
                    $tb_movimiento->responsable = $request->input('responsable');
                    $tb_movimiento->notas_adicionales = $request->input('notas_adicionales'); 
                    $tb_movimiento->save();  
                    $mensaje_salida = 'Actualizo Id ' . $tb_movimiento->id;
                }
                else {
                    $tb_movimiento = New Movimientos_Contables;
                    $tb_movimiento->fecha = $request->input('fecha');
                    $tb_movimiento->numero_comprobante = $request->input('numero_comprobante');
                    $tb_movimiento->descripcion = $request->input('descripcion');
                    $tb_movimiento->tipo_movimiento = $request->input('tipo_movimiento');
                    $tb_movimiento->monto = $request->input('monto');
                    $tb_movimiento->cliente_proveedor = $request->input('cliente_proveedor');
                    $tb_movimiento->moneda = $request->input('moneda');
                    $tb_movimiento->tipo_cambio = $request->input('tipo_cambio'); 
                    $tb_movimiento->responsable = $request->input('responsable');
                    $tb_movimiento->notas_adicionales = $request->input('notas_adicionales'); 
                    $tb_movimiento->save(); 
                    $mensaje_salida = 'Alta Id ' . $tb_movimiento->id;
                } 
            } 
            catch(\Illuminate\Database\QueryException $ex){  
            $mensaje_salida = $ex->getMessage();
            }
        }
        else {
            $mensaje_salida = 'ERROR  '  ;
        }        
        return response()->json(['message' =>  $mensaje_salida  ]);
    }
    public function deleteMovimientosContables($id, $token )
    {
            if ($token == 'gapp') {  
                try { 
                    Movimientos_Contables::where('id', $id)->delete(); 
                    $mensaje_salida = json_encode('Se Borra Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    } 
    public function uploadMovimientosContables(Request $request) {
      
        $file = $request->file('file');
       $id_movimiento_contable = $request->input('id');

       // Lógica para guardar el archivo en el servidor con el ID
       $fileName = "file_{$id_movimiento_contable}.jpg";

       $path = $request->file('file')->getRealPath();    
       $logo = file_get_contents($path);
       $img_comp = base64_encode($logo);

       //$file->move(storage_path('app/public/uploads'), $fileName);

       $movimiento_contable = Movimientos_Contables::find($id_movimiento_contable);
       $movimiento_contable->img_comprobante = $img_comp; 
       $movimiento_contable->save();  

       return response()->json(['message' => 'Archivo subido con éxito '  ]);
       
    }
    public function getMovimientosContables(  $responsable, $periodo , $token) {
     try {  
         $whereRaw = "  (YEAR(mc.fecha)= $periodo)";
        if ($responsable != '-')
        {
            $whereRaw = $whereRaw . " and (mc.responsable='$responsable')";
        }        
        if ($token == 'gapp') {           
                    $contable = DB::table('app_movimientos_contables AS mc')    
                    ->select(DB::Raw('  mc.id,
                                        mc.fecha,
                                        YEAR(mc.fecha) periodo,
                                        mc.numero_comprobante, 
                                        mc.descripcion, 
                                        mc.tipo_movimiento, 
                                        mc.monto, 
                                        mc.cliente_proveedor, 
                                        mc.moneda, 
                                        mc.tipo_cambio, 
                                        mc.responsable, 
                                        mc.notas_adicionales  '))  
                     ->whereRaw($whereRaw) 
                    ->orderBy('mc.fecha', 'asc')
                    ->get(); 
                    $resultado = json_encode($contable);            
        }
        else {
            $resultado = 'ERROR';
        }
     }
     catch(\Illuminate\Database\QueryException $ex){  
        $resultado = $ex->getMessage();
        }        
     return response($resultado,200);
    }
    public function getMovimientoContable(  $id , $token) {
        try {               
            if ($token == 'gapp') {           
                        $contable = DB::table('app_movimientos_contables AS mc')    
                        ->select(DB::Raw('  mc.id,
                                            mc.fecha, 
                                            mc.numero_comprobante, 
                                            mc.descripcion, 
                                            mc.tipo_movimiento, 
                                            mc.monto, 
                                            mc.cliente_proveedor, 
                                            mc.moneda, 
                                            mc.tipo_cambio, 
                                            mc.responsable, 
                                            mc.notas_adicionales,
                                            mc.img_comprobante   '))  
                        ->where('mc.id', $id )    
                        ->get(); 
                        $resultado = json_encode($contable);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
        }
            
        return response($resultado,200);
    }

    public function EncuestasIndex()
    {
        $encuestas = Encuesta::all(); 
        return response()->json($encuestas, 200);
    }

    public function EncuestasShow($id)
    {
         $encuesta = Encuesta::with('preguntas')->find($id); 
        
        if (!$encuesta) {
            return response()->json(['error' => 'Encuesta no encontrada'], 404);
        }

        return response()->json($encuesta, 200);
        //return response()->json(['message' => 'Respuestas guardadas correctamente'], 201);
    }

    public function EncuestasStore(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'preguntas' => 'required|array|min:1',
            'preguntas.*' => 'required|string|max:255',
        ]);

        try {
            $encuesta = Encuesta::create($request->only(['titulo', 'descripcion']));

            foreach ($request->preguntas as $index => $texto) {
                $encuesta->preguntas()->create([
                    'texto_pregunta' => $texto,
                    'orden' => $index + 1,
                ]);
            }

            return response()->json($encuesta, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la encuesta'], 500);
        }
    }
    public function EncuestasRespuestas(Request $request, $id )
    {
        //$request->validate([
        //    'respuestas' => 'required|array|min:1',
        //    'respuestas.*.id_pregunta' => 'required|integer|exists:preguntas,id_pregunta',
        //    'respuestas.*.respuesta' => 'required|string',
        //    'id_usuario' => 'required|integer',
        //]);
 
 

        try {
            $respuestasData = array_map(function ($respuesta) use ($id, $request) {
                return [
                    'id_encuesta' => $id,
                    'id_pregunta' => $respuesta['id_pregunta'],
                    'respuesta' => $respuesta['respuesta'],
                    'id_usuario' => $respuesta['id_usuario'],
                ];
            }, $request->respuestas);

            Respuesta::insert($respuestasData);

            return response()->json(['message' => 'Respuestas guardadas correctamente'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar las respuestas' . $e->getMessage()], 500);
        }
    }


    public function obtenerPreguntas($numeroEncuesta)
    {
        try {
            $preguntas = Pregunta::where('id_encuenta', $numeroEncuesta)
                ->orderBy('orden', 'asc')
                ->get()
                ->map(function ($pregunta) {
                    // Si es dropdown, obtiene las opciones de la tabla asociada
                    if ($pregunta->tipo_campo === 'dropdown' && $pregunta->tabla_dropdown) {
                        $pregunta->opciones = DB::table($pregunta->tabla_dropdown)->get();
                    } else {
                        $pregunta->opciones = [];
                    }
                    return $pregunta;
                });

            return response()->json([
                'success' => true,
                'data' => $preguntas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las preguntas: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getResultados($id_encuesta)
    {
        try {
         
            // Ejecutar el Stored Procedure
            $resultados = DB::select("CALL GetEncuestaResultados(?)", [$id_encuesta]);

            // Retornar como JSON
            return response()->json($resultados);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las preguntas: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getResultadosDni($dni)
    {
        try {
         
            // Ejecutar el Stored Procedure
            $resultados = DB::select("CALL GetEncuestaResultadosByDni(?)", [$dni]);

            // Retornar como JSON
            return response()->json($resultados);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las preguntas: ' . $e->getMessage(),
            ], 500);
        }
    } 
    // Pase y salvo 
    public function addPaseYSalvo(Request $request, $token)
    { 
        $now = new \DateTime();
        
        if ($token == 'gapp') {  
            try {  
                $temporal = new Miembros_pases;
                $temporal->fecha = $now; 

                // Asignación desde el POST
                $temporal->miembro_pase        = $request->input('miembro_pase');
                $temporal->nombre_pase         = $request->input('pasesNombresConcatenados');
                $temporal->duracion            = $request->input('paseDias');
                $temporal->motivo              = $request->input('paseMotivo');
                $temporal->participacion       = $request->input('paseParticipacion');  
                $temporal->miembro_id          = $request->input('paseMiembroId'); 
                $temporal->gender              = $request->input('paseEsGender'); // Corregido para no sobrescribir miembro_id

                // Campos de Ubicación
                $temporal->tipo_lugar_destino      = $request->input('tipoLugarDestino');
                $temporal->lumisial_nombre_origen  = $request->input('lumisialNombreOrigen');
                $temporal->lumisial_ciudad_origen  = $request->input('lumisialCiudadOrigen');
                $temporal->lumisial_provincia_origen = $request->input('lumisialProvinciaOrigen');
                $temporal->lumisial_nombre_destino = $request->input('lumisialNombreDestino');
                $temporal->lumisial_ciudad_destino = $request->input('lumisialCiudadDestino');
                $temporal->lumisial_provincia_destino = $request->input('lumisialProvinciaDestino');

                $temporal->created_at = $now;  
                
                $temporal->save(); 

                $mensaje_salida = "Nuevo pase registrado para " . $temporal->miembro_pase;
                return response()->json($mensaje_salida, 201);

            } catch(\Illuminate\Database\QueryException $ex){  
                return response()->json(['error' => $ex->getMessage()], 500);
            }
        } else {
            return response()->json(['error' => 'Token inválido'], 401);
        }        
    }

    public function getPaseYSalvo(  $miembro_id , $token) {
        try {               
            if ($token == 'gapp') {           
                        $contable = DB::table('app_miembros_pases AS pas')    
                        ->select(DB::Raw('  pas.id, 
                                            pas.fecha, 
                                            pas.miembro_pase, 
                                            pas.nombre_pase PaseNombre, 
                                            pas.duracion PaseDuracion, 
                                            pas.motivo PaseMotivo, 
                                            pas.participacion PaseParticipacion, 
                                            pas.miembro_id, 
                                            pas.updated_at, 
                                            pas.created_at, 
                                            pas.gender, 
                                            pas.tipo_lugar_destino, 
                                            pas.lumisial_nombre_origen lumisialNombreOrigen, 
                                            pas.lumisial_ciudad_origen limsialCiudadOrigen, 
                                            pas.lumisial_provincia_origen lumisialProvinciaOrigen, 
                                            pas.lumisial_nombre_destino lumisialNombreDestino, 
                                            pas.lumisial_ciudad_destino lumisialCiudadDestino, 
                                            pas.lumisial_provincia_destino lumisialProvinciaDestino '))  
                        ->where('pas.miembro_id', $miembro_id )
                        ->orWhere('pas.miembro_pase', $miembro_id)   
                        ->get(); 
                        $resultado = json_encode($contable);            
            }
            else {
                $resultado = 'ERROR';
            }
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
        }
            
        return response($resultado,200);
    }

    public function getPaseYSalvoById(  $id , $token) {
        try {                          
                $pase = DB::table('app_miembros_pases AS pas')    
                ->select(DB::Raw('  pas.id, 
                                    pas.fecha, 
                                    pas.miembro_pase, 
                                    pas.nombre_pase PaseNombre, 
                                    pas.duracion PaseDuracion, 
                                    pas.motivo PaseMotivo, 
                                    pas.participacion PaseParticipacion, 
                                    pas.miembro_id, 
                                    pas.updated_at, 
                                    pas.created_at, 
                                    pas.gender, 
                                    pas.tipo_lugar_destino, 
                                    pas.lumisial_nombre_origen lumisialNombreOrigen, 
                                    pas.lumisial_ciudad_origen limsialCiudadOrigen, 
                                    pas.lumisial_provincia_origen lumisialProvinciaOrigen, 
                                    pas.lumisial_nombre_destino lumisialNombreDestino, 
                                    pas.lumisial_ciudad_destino lumisialCiudadDestino, 
                                    pas.lumisial_provincia_destino lumisialProvinciaDestino '))  
                ->where('pas.id', $id )   
                ->get(); 
                $resultado = json_encode($pase);            
             
        }
        catch(\Illuminate\Database\QueryException $ex){  
            $resultado = $ex->getMessage();
        }
            
        return response($resultado,200);
    } 

    public function deletePaseYSalvo($id, $token )
    {
            if ($token == 'gapp') {  
                try { 
                    Miembros_pases::where('id', $id)->delete(); 
                    $mensaje_salida = json_encode('Se Borra Id ' . $id);
                } 
                catch(\Illuminate\Database\QueryException $ex){  
                    $mensaje_salida = $ex->getMessage();
                }
            }
            else {
                $mensaje_salida = 'ERROR';
            }        
            return response($mensaje_salida,200);
    }  

}//fin de archivo

