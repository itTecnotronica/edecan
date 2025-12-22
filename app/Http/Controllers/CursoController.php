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
use App\Misionero_por_evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GenericController;
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

class CursoController extends Controller
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



    public function cursoShow($fecha_de_evento_id, $hash)
    {  
       
        $hash_ok = md5(ENV('PREFIJO_HASH').$fecha_de_evento_id);

        if ($hash_ok == $hash) {   
        
            echo '<h1>Proximamente... (vuelva atras)</h1>';

            $Fecha_de_evento = Fecha_de_evento::find($fecha_de_evento_id);
            $Solicitud = $Fecha_de_evento->Solicitud;
            
            return View('solicitudes/curso')
            ->with('Solicitud', $Solicitud)
            ->with('Fecha_de_evento', $Fecha_de_evento);
         }
        else {
            echo 'ERROR';
        }  

    }

    public function setearSiEsInstructor()
    {   
        $user_id = $_POST['user_id'];
        $fecha_de_evento_id = $_POST['fecha_de_evento_id'];
        $sino = $_POST['sino'];

        if ($sino == 'SI') {
            $Misionero_por_evento = new Misionero_por_evento;
            $Misionero_por_evento->user_id = $user_id;
            $Misionero_por_evento->fecha_de_evento_id = $fecha_de_evento_id;
            $Misionero_por_evento->save();    
        }
        else {
            Misionero_por_evento::where('user_id', $user_id)->where('fecha_de_evento_id', $this->id)->delete();
        }

        return $sino;

    }




}

