<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\Fecha_de_evento;
use App\Sede_de_difusion;
use App\Sesion;
use App\Inscripcion;
use App\Asistencia;
use App\Leccion;
use App\Evaluacion;
use App\Leccion_extra;
use App\Pais;
use App\Registro_de_error;

use App\Http\Controllers\MauticController;

use App;
use Image;
use QrCode;
use Auth;
use PDF;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Mautic\Auth\ApiAuth;


class AccionesEventualesController extends Controller
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

    public function convertirImgBase64Solicitudes()
    {

        $tipos = [
            ['data:image/gif;base64,', 'gif'],
            ['data:image/jpeg;base64,', 'jpg'],
            ['data:image/png;base64,', 'png'],
            ['data:image/webp;base64,', 'webp']
        ];

        foreach ($tipos as $tipo) {
            $criterio = $tipo[0];
            $extension = $tipo[1];
            $Solicitudes = Solicitud::whereRaw('file_imagen_del_formulario_personalizada like "'.$criterio.'%"')->limit(200)->get();


            foreach ($Solicitudes as $Solicitud) {
                $imagenEnBase64 = str_replace($criterio, '', $Solicitud->file_imagen_del_formulario_personalizada);
                //dd($imagenEnBase64);
                $nombre_img = "img-form-solicitud-id-".$Solicitud->id.".".$extension;
                $rutaImagenSalida = env('PATH_PUBLIC_INTERNO') . "storage/Solicitud/".$nombre_img;
                $imagenBinaria = base64_decode($imagenEnBase64);
                $bytes = file_put_contents($rutaImagenSalida, $imagenBinaria);
                echo "<p>$bytes bytes fueron escritos en $rutaImagenSalida</p>";
                $Solicitud->file_imagen_del_formulario_personalizada = "Solicitud/".$nombre_img;
                $Solicitud->save();
            }
        }

    }

}
