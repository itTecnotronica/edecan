<?php

namespace App\Http\Controllers;
use App\Envio_a_contacto;
use App\Instancia_de_envio;
use App\Contacto;
use App\Lista_de_envio;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ListasController extends Controller
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


    public function url_whatsapp($Modelo_de_mensaje, $parametros)
    {
        
        $inscrito_nombre = mb_strtoupper($parametros['nombre'], 'UTF-8');
        $inscrito_apellido = mb_strtoupper($parametros['apellido'], 'UTF-8');
        $telefono = $parametros['telefono'];
        $codigo_tel = $parametros['codigo_tel'];

        // pedido_de_confirmacion_curso
        $patrones = array();
        $patrones[0] = '/inscrito_nombre/';
        $patrones[1] = '/inscrito_apellido/';
        $sustituciones = array();
        $sustituciones[0] = $inscrito_nombre;
        $sustituciones[1] = $inscrito_apellido;


        $Modelo_de_mensaje = preg_replace($patrones, $sustituciones, $Modelo_de_mensaje);
        $Modelo_de_mensaje = $Modelo_de_mensaje;
        $urlencode_Modelo_de_mensaje = $this->CodificarURL($Modelo_de_mensaje);
        $Modelo_de_mensaje = 'https://api.whatsapp.com/send?phone='.$this->celular_wa($telefono, $codigo_tel).'&text='.$urlencode_Modelo_de_mensaje;


        $url_whatsapp = $Modelo_de_mensaje;

        return $url_whatsapp;
    }


    public function CodificarURL($string) {

        $entities = array('%20');
        $replacements = array('+');
        return str_replace($replacements, $entities, urlencode($string));
    }


    public function celular_wa($telefono, $codigo_tel)
    {
        $celular_wa = trim($telefono);
        
        if (substr($celular_wa, 0, 1) <> '+') {
            if (substr($celular_wa, 0, strlen($codigo_tel)) <> $codigo_tel) {
                $celular_wa = $codigo_tel.$celular_wa;
            }
        }
        
        $celular_wa = str_replace('+', '', $celular_wa);
        $celular_wa = str_replace(' ', '', $celular_wa);
        $celular_wa = str_replace('-', '', $celular_wa);
        $celular_wa = str_replace('(', '', $celular_wa);
        $celular_wa = str_replace(')', '', $celular_wa);
        $celular_wa = str_replace(',', '', $celular_wa);
        $celular_wa = str_replace('.', '', $celular_wa);
        
        return $celular_wa;
    }


    public function registrarEnvio($codigo_de_envio_id, $instancia_de_envio_id, $medio_de_envio_id)
    {  

        $Envio = new Envio_a_contacto;
        $Envio->instancia_de_envio_id = $instancia_de_envio_id;
        $Envio->codigo_de_envio_id = $codigo_de_envio_id;
        $Envio->medio_de_envio_id = $medio_de_envio_id;
        $Envio->save();

        //return $Inscripcion;

    }


    public function setearSino($codigo, $instancia_de_envio_id, $tipo_de_lista_de_envio_id)
    {   
        $sino = $_POST['sino'];
        
        $nombre_de_campo = 'sino_envio_'.$codigo;
        
        if ($codigo == 11) {
            $nombre_de_campo = 'sino_deshabilitar';
        }

        $Instancia_de_envio = Instancia_de_envio::find($instancia_de_envio_id);
        $contacto_id = $Instancia_de_envio->contacto_id;
        $Instancia_de_envio->$nombre_de_campo = $sino;
        $Instancia_de_envio->save();

        if ($codigo == 11 and $tipo_de_lista_de_envio_id <> 2) {
            $Contacto = Contacto::find($contacto_id);
            $Contacto->sino_deshabilitar = $sino;
            $Contacto->save();
        }

        return $Instancia_de_envio;

    }



    public function crearListas($cantidad, $tipo_de_lista_de_envio_id)
    {  

        for ($i=1; $i<=$cantidad; $i++) {
            $Lista_de_envio = new Lista_de_envio;
            $Lista_de_envio->encabezado_de_envio_id = 1;
            $Lista_de_envio->nombre_de_la_lista = "Lista $i";
            $Lista_de_envio->hash = rand();
            $Lista_de_envio->tipo_de_lista_de_envio_id = $tipo_de_lista_de_envio_id;
            $Lista_de_envio->save();
            echo env('PATH_PUBLIC')."le/".$Lista_de_envio->id."/".$Lista_de_envio->hash."<br>";
        }
        
        echo "$cantidad listas creadas";

    }




}
