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
            ->where('tb_personas.numero_de_documento', $documento)  
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
            $Personas = DB::table('app_eventos AS tEv')    
            ->select(DB::Raw('tEv.id Id,  
                                CONCAT(tte.tipo_de_evento," - ", tEv.evento) Evento, 
                                tEv.fecha_inicio Fecha'))   
            ->leftjoin('app_tipos_de_eventos AS tTe', 'tEv.tb_tipo_de_evento_id', '=', 'tTe.id')   
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

    public function saveInscripcion($id, $tb_evento_id,  $tb_persona_id, $token )
    {
            if ($token == 'gapp') {
                $now = new \DateTime();
                $cant_persona_evento = Inscripcion_Evento::where('tb_evento_id', $tb_evento_id)->count();
                $cant_persona = Inscripcion_Evento::where('id', $id)->count();
                try { 

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
                        $Inscripcion->fecha_inscripcion = $now;
                        $Inscripcion->save(); 
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
                                tTde.tipo_de_debito')) 
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
    public function saveDebito($id, $tb_tarjeta_id, $tb_tipo_de_tarjeta_id,  $tb_persona_id, $numero_de_tarjeta,$monto, $observaciones, $token )
    {
            if ($token == 'gapp') { 
                $cant_persona = Debito::where('id', $id)->count();
                try { 

                    if ($cant_persona > 0) {
                        $enDebito = Debito::find($id);
                        $enDebito->tb_persona_id = $tb_persona_id;
                        $enDebito->tb_tarjeta_id = $tb_tarjeta_id;
                        $enDebito->tb_tipo_de_tarjeta_id = $tb_tipo_de_tarjeta_id;
                        $enDebito->numero_de_tarjeta = $numero_de_tarjeta;
                        $enDebito->debitando = 'NO';
                        $enDebito->monto = $monto;
                        $enDebito->observaciones = $observaciones;                        
                        // 
                        $enDebito->save();  
                    }
                    else {
                        $enDebito = New Debito;
                        $enDebito->tb_persona_id = $tb_persona_id;
                        $enDebito->tb_tarjeta_id = $tb_tarjeta_id;
                        $enDebito->tb_tipo_de_tarjeta_id = $tb_tipo_de_tarjeta_id;
                        $enDebito->numero_de_tarjeta = $numero_de_tarjeta;
                        $enDebito->debitando = 'NO';
                        $enDebito->monto = $monto;
                        $enDebito->observaciones = $observaciones;
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
    public function getCarnet($pais_id, $id, $token) {

        if ($token == 'gapp') {
            $Personas = DB::table('app_carnets AS car')    
            ->select(DB::Raw('tTip.tipo_de_carnet, 
                                car.id, 
                                car.confeccionado, 
                                car.pagado, 
                                car.fecha_de_pago, 
                                car.importe_pagado, 
                                car.fecha_de_confeccion, 
                                car.fecha_de_vencimiento, 
                                car.autorizado, 
                                car.created_at, 
                                car.envio ')) 
            ->leftjoin('app_tipos_de_carnets AS tTip', 'car.tb_tipo_de_carnet_id', '=', 'tTip.id') 
            ->where('car.tb_persona_id', $id)   
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
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
                        // 
                        $enCarnet->save();  
                    }
                    else {
                        $enCarnet = New Carnet;
                        $enCarnet->tb_tipo_de_carnet_id = $tb_tipo_de_carnet_id;
                        $enCarnet->tb_persona_id = $tb_persona_id;
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

    public function getMaterialesSearch($idioma_id, $token, $value) {

        if ($token == 'gapp') {
            $Personas = DB::table('materiales as mat')    
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
            ->where('mat.titulo','like', '%'.$value.'%')    
           ->orderBy('mat.titulo', 'desc')
            ->get(); 
            $resultado = json_encode($Personas);
        }
        else {
            $resultado = 'ERROR';
        }

        return response($resultado,200);
    }

    public function getAllMateriales($idioma_id, $token, $tipo, $cant, $autor) {

        if ($token == 'gapp') {
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
                                tip.tipo_de_material'))  
            ->join('autores AS aut', 'mat.autor_id', '=', 'aut.id')
            ->join('idiomas AS idi', 'mat.idioma_id', '=', 'idi.id')
            ->join('tipos_de_materiales AS tip', 'mat.tipo_de_material_id', '=', 'tip.id')
            ->where('mat.tipo_de_material_id', $tipo )   
            ->where('mat.autor_id', $autor )   
            ->where('mat.idioma_id', $idioma_id ) 
            ->where('mat.sino_autorizado', 'SI' )   
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

        public function getAllMaterialesRandom($idioma_id, $token, $cant) {

        if ($token == 'gapp') {
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
                                mbr.img_imagen'))   
            ->where('mbr.documentNumber', $documento )    
            ->get(); 
            $resultado = json_encode($Miembros);
        }
        else {
            $resultado = 'ERROR';
        }
        return response($resultado,200);
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


        $Localidades = DB::table('paises as p')
            //->select(DB::Raw("l.id, l.localidad as city, pr.provincia as region, p.pais as country, s.id as solicitud_id, CONCAT('https://ac.gnosis.is/fc/', s.id, '/', s.hash,'/399') as url_form_solicitud"))
            ->select(DB::Raw("DISTINCT l.id, l.localidad as city, pr.provincia as region"))
            ->leftjoin('provincias as pr', 'p.id', '=', 'pr.pais_id')
            ->leftjoin('localidades as l', 'pr.id', '=', 'l.provincia_id')
            ->leftjoin('solicitudes as s', 's.localidad_id', '=', 'l.id')
            ->leftjoin('fechas_de_evento as fe', 's.id', '=', 'fe.solicitud_id')
            ->where('p.id', $pais_id)
            ->where('s.sino_aprobado_administracion', 'SI')
            ->whereRaw('((s.tipo_de_evento_id in (1, 2) AND fe.fecha_de_inicio >= "2023-02-20") or (s.tipo_de_evento_id = 3 AND s.fecha_de_inicio_del_curso_online >= "2023-02-20") or s.created_at >= "2023-02-20")')
            ->whereRaw("(s.sino_es_campania_de_capacitacion IS NULL OR s.sino_es_campania_de_capacitacion = 'NO')")
            ->whereRaw("(s.sino_aprobado_finalizada IS NULL OR s.sino_aprobado_finalizada = 'NO')")
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
                ->whereRaw('((s.tipo_de_evento_id in (1, 2) AND fe.fecha_de_inicio >= "2023-02-20") or (s.tipo_de_evento_id = 3 AND s.fecha_de_inicio_del_curso_online >= "2023-02-20") or s.created_at >= "2023-02-20")')
                ->whereRaw("(s.sino_es_campania_de_capacitacion IS NULL OR s.sino_es_campania_de_capacitacion = 'NO')")
                ->whereRaw("(s.sino_aprobado_finalizada IS NULL OR s.sino_aprobado_finalizada = 'NO')")
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
                    $ids_extra = [7536, 7545, 7547, 7549];
                }

                // FRANCES
                if ($idioma_id == 3) {
                    $ids_extra = [7542, 7546, 7883];
                }

                // INGLES
                if ($idioma_id == 2) {
                    $ids_extra = [7544, 7878, 7886];
                }

                // PORTUGUES
                if ($idioma_id == 5) {
                    $ids_extra = [7543, 7879, 7884];
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

                    if ($Solicitud->tipo_de_evento_id == 3 and in_array($Solicitud->tipo_de_curso_online_id, [2,3,5])) {
                        $fecha_de_inicio = $Solicitud->fecha_de_inicio_del_curso_online;
                        if (in_array($Solicitud->tipo_de_curso_online_id, [3,5])) {
                            $hora_de_inicio = $Solicitud->hora_de_inicio_del_curso_online;
                        }
                    }

                    $opcion_otra = [
                        'id_form_solicitud' => $id_form_curso_online,
                        'url_form_solicitud' => $Solicitud->url_form_inscripcion_con_campania_id(399),
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


}

