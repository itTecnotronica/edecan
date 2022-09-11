<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Auth;

use App\Sesion;
use App\Registro_de_error;
use Session;
use URL;
use Request;
use Browser;

class RegistroDeSesion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('sesion_id'))
        {

            $user_id = NULL;
            if (!Auth::guest())
            {
                $user_id = Auth::user()->id;
            }

            $ip_adress = $_SERVER['REMOTE_ADDR'];

            // INICIO DATOS DE NAVEGADOR
            $ua=$this->getBrowser();
            $navegador_nombre = $ua['name'];
            $navegador_version = $ua['version'];
            $plataforma = $ua['platform'];
            $user_agent = $ua['userAgent'];
            $lenguaje = $ua['language'];
            // FIN DATOS DE NAVEGADOR

            // INICIO DATOS DE DISPOSITIVO
            Browser::detect();
            //dd(Browser::detect());


            $Sesion = new Sesion;

            if (Browser::detect())
            {
                if (Browser::isMobile())
                {
                    $tipo_de_dispositivo = 'Mobile';
                }
                if (Browser::isTablet())
                {
                    $tipo_de_dispositivo = 'Tablet';
                }
                if (Browser::isDesktop())
                {
                    $tipo_de_dispositivo = 'Desktop';
                }

                if (Browser::isBot())
                {
                    $es_un_bot = 'SI';
                }
                else
                {
                    $es_un_bot = 'NO';
                }


                $sistema_operativo = Browser::platformFamily();
                $familia_de_dispositivo = Browser::deviceFamily();
                $modelo_de_dispositivo = Browser::deviceModel();

            }
            else {
                $tipo_de_dispositivo = NULL;
                $es_un_bot = NULL;
                $sistema_operativo = NULL;
                $familia_de_dispositivo = NULL;
                $modelo_de_dispositivo = NULL;
            }
            // FIN DATOS DE DISPOSITIVO

            $Sesion->user_id = $user_id;
            $Sesion->ip_adress = substr($ip_adress, 0, 20);
            $Sesion->tipo_de_dispositivo = $tipo_de_dispositivo;
            $Sesion->user_agent = substr($user_agent, 0, 50);
            $Sesion->lenguaje = substr($lenguaje, 0, 12);
            $Sesion->es_un_bot = $es_un_bot;
            $Sesion->pagina_de_origen = substr(URL::previous(), 0, 80);
            $Sesion->pagina_de_ingreso = substr(Request::fullUrl(), 0, 80);

            $Sesion->browser_name = substr(Browser::browserName(), 0, 35);
            //$Sesion->browser_name = 'ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss';
            $Sesion->browser_version = substr(Browser::browserVersion(), 0, 15);
            $Sesion->browser_family = substr(Browser::browserFamily(), 0, 25);
            $Sesion->browser_engine = substr(Browser::browserEngine(), 0, 10);
            $Sesion->platform_name = substr(Browser::platformName(), 0, 25);
            $Sesion->platform_family = substr(Browser::platformFamily(), 0, 15);
            $Sesion->platform_version = substr(Browser::platformVersion(), 0, 10);
            $Sesion->device_family = substr(Browser::deviceFamily(), 0, 40);
            $Sesion->device_model = substr(Browser::deviceModel(), 0, 40);
            $Sesion->mobile_grade = substr(Browser::mobileGrade(), 0, 5);
    
                
            try { 
              $Sesion->save();
            } catch(\Illuminate\Database\QueryException $ex){ 
                $detalle_de_origen = 'Ingreso al Formulario de Inscripcion: '.URL::previous();
                $Registro_de_error = new Registro_de_error;
                $Registro_de_error->registro_de_error = $ex->getMessage();
                $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                $Registro_de_error->save();              
              // Note any method of class PDOException can be called on $ex.
            }
            

            $request->session()->put('sesion_id', $Sesion->id);

        }
        else {
            $sesion_id = $request->session()->get('sesion_id');
            //Session::flush(); 

        }
        return $next($request);
    }

    public function getBrowser()
    {
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";
        $language= "";

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $u_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        else {
            $u_agent = '';
        }

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        $ub = "";
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);

        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                if (isset($matches['version'][1])) {
                    $version= $matches['version'][1];
                }
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $a_language = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $language = $a_language[0];
        }
        else {
            $language = '';
        }

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern,
            'language' => $language
        );
    }
}
