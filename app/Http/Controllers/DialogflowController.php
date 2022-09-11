<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\Fecha_de_evento;
use App\Inscripcion;
use App\Asistencia;
use App\Leccion;
use App\Evaluacion;
use App\Leccion_extra;
use App\Pais;

use App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Mautic\Auth\ApiAuth;


class DialogflowController extends Controller
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


    public function dialog(Request $request) {
        

        $callbacks = json_decode($request->getContent(), true);
        $intent = $callbacks['queryResult']['intent']['displayName'];
        
        if ($intent == 'regAsistencia_cb') {
            $nro_de_leccion = $callbacks['queryResult']['parameters']['nro_de_leccion'][0];
            $email_tel = $callbacks['queryResult']['parameters']['email_tel'];

            $Inscripcion = $this->traerInscripcion($email_tel);
            
            if ($Inscripcion->count() > 0) {
                $nombre_y_apellido = $Inscripcion->nombre.' '.$Inscripcion->apellido;
                $mensaje = 'Hola '.$nombre_y_apellido;

                $mensaje = $this->registrarAsistencia($Inscripcion, $nro_de_leccion);

            }
            else {
                $mensaje = 'No te hemos encontrado';

            }

        }


        return response()->json([
          "fulfillmentMessages"=> [
            [
              "text"=> [
                "text"=> [$mensaje]
              ]
            ]
          ]

        ]);


        }


        public function traerInscripcion($email_tel) {

            $celular = $email_tel;
            $celular_sin_9 = $celular;
            if (substr($celular, 2, 1) == 9 and substr($celular, 0, 2) == '54') {
                $celular_sin_9 = substr($celular, 0, 2).substr($celular, 3);
                //dd($celular_sin_9);
            }

            if (substr($celular, 0, 2) == '55') {
                
                $celular_12 = $celular;
                $celular_13 = $celular;

                if(strlen($celular) == 12) {
                    $celular_12 = $celular;
                    $celular_13 = substr($celular, 0, 4).'9'.$celular = substr($celular, 4, 8);
                }
                else {
                    if(strlen($celular) == 13) {
                        $celular_13 = $celular;
                        $celular_12 = substr($celular, 0, 4).$celular = substr($celular, 5, 8);
                    }
                }

                $celular = $celular_13;
                $celular_sin_9 = $celular_12;

            }

            $Inscripciones = Inscripcion::
            whereRaw("(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(celular,'#',''),')',''),'(',''),'-',''),' ',''),'+','') like '%".$celular."%' OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(celular,'#',''),')',''),'(',''),'-',''),' ',''),'+','') like '%".$celular_sin_9."%')")
            ->orderBy('id', 'desc')
            ->get();

            if ($Inscripciones->count() == 0) {

                $Inscripciones = Inscripcion::
                    where('email_correo', $email_tel)
                    ->orderBy('id', 'desc')
                    ->get();
                }

            return $Inscripciones[0];

        

        }
        

        public function registrarAsistencia($Inscripcion, $nro_de_leccion) {
                                  
            $Solicitud = Solicitud::find($Inscripcion->solicitud_id);
            
            $clase = $nro_de_leccion;
            $error_flag = false;
            $es_leccion_normal = true;
            if ($Solicitud->count() > 0) {

                $primera_letra_clase = substr($clase, 0, 1);
                if ($primera_letra_clase == 'X') {
                    $es_leccion_normal = false;
                    $leccion_extra_id = substr($clase, 1);
                    $Leccion = Leccion_extra::where('id', $leccion_extra_id)->get();
                }
                else {                    
                    $Leccion = Leccion::where('curso_id', $Solicitud->curso_id)->where('codigo_de_la_leccion', $clase)->get();
                }
                
                  
                if ($Leccion->count() > 0) {
                    
                    //REGISTRO LA ASISTENCIA
                    $Asistencia = new Asistencia();
                    if ($es_leccion_normal) {
                        $Asistencia->leccion_id = $Leccion[0]->id;
                    
                        //ACTUALIZO LA ULTIMA LECCION VISTA
                        $Inscripcion_1 = Inscripcion::find($Inscripcion->id);
                        $Inscripcion_1->ultima_leccion_vista = $Leccion[0]->id;
                        $Inscripcion_1->save();
                    }
                    else {
                        $Asistencia->leccion_extra_id = $Leccion[0]->id;    
                    }
                    $Asistencia->inscripcion_id = $Inscripcion->id;
                    $Asistencia->save();
                }
                else {
                    $error_flag = true;
                    $Asistencia_error = new Asistencia();
                    $Asistencia_error->log = 'Leccion No Encontrada|'.$request->getContent();
                    $Asistencia_error->save(); 
                }              

                if ($Inscripcion->count() == 0) {
                    $error_flag = true;
                    $Asistencia_error = new Asistencia();
                    $Asistencia_error->log = 'Inscripcion No Encontrada|'.$request->getContent();
                    $Asistencia_error->save();
                }


            }
            else {
                $error_flag = true;
                $Asistencia_error = new Asistencia();
                $Asistencia_error->log = 'Solicitud No Encontrada|'.$request->getContent();
                $Asistencia_error->save(); 
            }

            if ($error_flag) {
                $mensaje = $Asistencia_error->log ;
            }
            else {
                $mensaje = 'Asistencia Registrada';    
            }
            
            return $mensaje;

        }


}
