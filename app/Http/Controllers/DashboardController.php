<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\User;
use App\Pais;
use App\Provincia;
use App\Localidad;
use App\Tipo_de_evento;
use App\Equipo;
use App\Idioma;
use App\Inscripcion;
use App\Fecha_de_evento;
use App\Http\Controllers\SolicitudController;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ExtController;



class DashboardController extends Controller
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



    public function modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color)
    {   

        
        echo View('dashboard/top-modulo-contacto')
        ->with('widget', $widget)
        ->with('modal', $modal)
        ->with('info', $info)
        ->with('columnas', $columnas)
        ->with('ranking', $ranking)
        ->with('valores_color', $valores_color)->render();

    }

    public function classColor($porc_warning, $porc_danger, $porc) {

      $class_fila = 'bg-green color-palette';
      
      if ($porc >= $porc_warning[0] and $porc <= $porc_warning[1] ) {
        $class_fila = 'bg-yellow color-palette';    
      }
      else {
        if ($porc >= $porc_danger[0] and $porc < $porc_danger[1] ) {
            $class_fila = 'bg-red color-palette';    
          }
      }

      return $class_fila;
    }

}
