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


class ExtController extends Controller
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

    public function mapaDeCursosYConferencias($dias, $lang = NULL)
    {

        $this->setearIdiomaUsuario($lang);

        $Solicitudes = Solicitud::
            where('sino_aprobado_administracion', 'SI')
            ->whereRaw('(sino_es_campania_de_capacitacion IS NULL OR sino_es_campania_de_capacitacion = "NO")')
            ->whereRaw('id in (SELECT f.solicitud_id FROM fechas_de_evento f WHERE (DATEDIFF(NOW(), f.fecha_de_inicio) <= '.$dias.'))')
            ->whereNotNull('fecha_de_solicitud')
            ->whereRaw('(sino_cancelada IS NULL OR sino_cancelada = "NO")')
            ->whereRaw('id <> 1978')
            //->whereRaw('(id = 2987)')
            //->whereRaw('(fechas_de_evento.solicitud_id in (598, 593))')
            ->get();

        //$Fechas_de_evento = Fecha_de_evento::whereRaw('(id = 845)')->get();
        //dd($Solicitudes[0]->fechas_de_evento[0]->datos_url_google_maps());
        //$Fechas_de_evento = Fecha_de_evento::all();11


        $datos = [];

        foreach ($Solicitudes as $Solicitud) {
            //echo $Fecha_de_evento->Solicitud->localidad->localidad;
            $datos[] = $Solicitud->fechas_de_evento[0]->datos_url_google_maps();
        }
        // Uncomment to see all headers
        /*
        echo "<pre>";
        print_r($a);echo"<br>";
        echo "</pre>";
        */
        //dd(1);


        $lat_lon = '-0.397, 5.644';

        return View('reportes/mapa-de-cursos-y-conferencias')
        ->with('Solicitudes', $Solicitudes)
        ->with('lat_lon', $lat_lon)
        ->with('datos', $datos);


    }

    public function mapaDeCursosYConferenciasPorRegion($dias, $tipo, $id, $lang = NULL)
    {

        $this->setearIdiomaUsuario($lang);
        

        //TODO
        if ($tipo == '0') {
            $tipo_where = '1 = 1';
        }

        //SOLICITUD
        if ($tipo == '1') {
            $tipo_where = "id = $id";
        }

        //FECHA DE EVENTO
        if ($tipo == '2') {
            $tipo_where = "id in (SELECT f.solicitud_id FROM fechas_de_evento f WHERE f.id = $id)";
        }

        //LOCALIDAD
        if ($tipo == '3') {
            $tipo_where = "localidad_id = $id";
        }

        //PROVINCIA
        if ($tipo == '4') {
            $tipo_where = "localidad_id IN (SELECT l.id FROM localidades l INNER JOIN provincias pr ON pr.id = l.provincia_id WHERE pr.id = $id)";
        }

        //PAIS
        if ($tipo == '5') {
            $tipo_where = "localidad_id IN (SELECT l.id FROM localidades l INNER JOIN provincias pr ON pr.id = l.provincia_id WHERE pr.pais_id = $id)";
        }


        $Solicitudes = Solicitud::
            where('sino_aprobado_administracion', 'SI')
            ->whereRaw('(sino_es_campania_de_capacitacion IS NULL OR sino_es_campania_de_capacitacion = "NO")')
            ->whereRaw('id in (SELECT f.solicitud_id FROM fechas_de_evento f WHERE (DATEDIFF(NOW(), f.fecha_de_inicio) <= '.$dias.'))')
            ->whereNotNull('fecha_de_solicitud')
            ->whereRaw('(sino_cancelada IS NULL OR sino_cancelada = "NO")')
            ->whereRaw($tipo_where)

            //->whereRaw('(id in (6,9))')
            //->whereRaw('(fechas_de_evento.solicitud_id in (598, 593))')
            ->get();

        //$Fechas_de_evento = Fecha_de_evento::whereRaw('(id = 845)')->get();
        //dd($Solicitudes[0]->fechas_de_evento[0]->datos_url_google_maps());
        //$Fechas_de_evento = Fecha_de_evento::all();11


        $datos = [];
        $lat_lon = '';

        foreach ($Solicitudes as $Solicitud) {
            //echo $Fecha_de_evento->Solicitud->localidad->localidad;
            $datos_a = $Solicitud->fechas_de_evento[0]->datos_url_google_maps();

            if ($lat_lon == '') {
                if ($datos_a['latitud'] <> '' and $datos_a['longitud'] <> '') {
                    $lat_lon = $datos_a['latitud'].','.$datos_a['longitud'];
                }
            }

            $datos[] = $datos_a;
        }

        if ($lat_lon == '') {
            $lat_lon = '-0.397, 5.644';
        }

        //dd($lat_lon);

        // Uncomment to see all headers
        /*
        echo "<pre>";
        print_r($a);echo"<br>";
        echo "</pre>";
        */
        //dd(1);


        return View('reportes/mapa-de-cursos-y-conferencias')
        ->with('Solicitudes', $Solicitudes)
        ->with('lat_lon', $lat_lon)
        ->with('datos', $datos);

    }


    public function setearIdiomaUsuario($lang)
    {
        $lang_default = App::getLocale();

        if ($lang <> NULL) {
            if (file_exists(ENV('PATH_LANG_INTERNO').$lang)) {
                App::setLocale($lang);
            }
            else {
                $lang = $lang_default;
            }
        }
        else {
            $lang = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if (file_exists (ENV('PATH_LANG_INTERNO').$lang)) {
                App::setLocale($lang);
            }
            else {
                $lang = substr($lang, 0, 2);
                if (file_exists (ENV('PATH_LANG_INTERNO').$lang)) {
                    App::setLocale($lang);
                }
                else {
                    $lang = $lang_default;
                }
            }
        }
        //$lang = 'ee';
        //dd($lang);
        App::setLocale($lang);

        /*
        //
        $lang = str_replace('_', '-', $lang);
        dd(Lang::has('Volver', 'pt'));
        if (App::isLocale($lang)) {
            App::setLocale($lang);
            dd($lang);
        }
        else {
            $lang = substr($lang, 0, 2);
             if (App::isLocale($lang)) {
                App::setLocale($lang);
            }
        }
        */
    }

    public function mapaDeSedesArgentina()
    {

        $SedesDB = DB::connection('ageacac-ar')
            ->table('tb_sede_de_difusion as sd')
            ->select(DB::Raw('sd.id_sede_de_difusion, sd.direccion, l.localidad, sd.telefonos_fijos, sd.telefonos_celulares, sd.correos, sd.latitud_y_longitud_generada_google_maps, sd.latitud_y_longitud_google_maps, sd.url_enlace_a_mapa_de_ubicacion, sd.otra_informacion_adicional, p.provincia'))
            ->leftjoin('tb_localidad as l', 'sd.tb_localidad', '=', 'l.id_localidad')
            ->leftjoin('tb_provincia as p', 'l.tb_provincia', '=', 'p.id_provincia')
            ->whereRaw('sd.habilitada = "SI"')
            //->whereRaw('sd.id_sede_de_difusion = 129')
            //->whereNotNull('sd.latitud_y_longitud_google_maps')
            ->get();

        $Sedes = [];
        foreach ($SedesDB as $SedeDB) { 

            $datos = [
                'url' => '',
                'latitud' => '',
                'longitud' => '',
                ];

            $arrayLatLon = explode(', ', $SedeDB->latitud_y_longitud_google_maps);
            if (count($arrayLatLon) > 1) {
                $datos['url'] = 'http://maps.google.com/maps?q='.$arrayLatLon[0].','.$arrayLatLon[1];
                $datos['latitud'] = $arrayLatLon[0];
                $datos['longitud'] = $arrayLatLon[1];
            }
            else {
                $datos = $this->extrarLatLonUrlGoogleMaps($SedeDB->url_enlace_a_mapa_de_ubicacion);  
                if ($datos['latitud'] == '') {
                    $arrayLatLon = explode(', ', $SedeDB->latitud_y_longitud_generada_google_maps);
                    if (count($arrayLatLon) > 1) {
                        $datos['url'] = 'http://maps.google.com/maps?q='.$arrayLatLon[0].','.$arrayLatLon[1];
                        $datos['latitud'] = $arrayLatLon[0];
                        $datos['longitud'] = $arrayLatLon[1];
                    }
                }
                else {
                    $Sede_de_difusion = Sede_de_difusion::find($SedeDB->id_sede_de_difusion);
                    $Sede_de_difusion->latitud_y_longitud_google_maps = $datos['latitud'].', '.$datos['longitud'];
                    $Sede_de_difusion->save(); 
                }
            }

            //dd($datos);
            $Sedes[] = [
                'id_sede_de_difusion' => $SedeDB->id_sede_de_difusion,
                'direccion' => $SedeDB->direccion,
                'localidad' => $SedeDB->localidad,
                'telefonos_fijos' => $SedeDB->telefonos_fijos,
                'telefonos_celulares' => $SedeDB->telefonos_celulares,
                'correos' => $SedeDB->correos,
                'url_enlace_a_mapa_de_ubicacion' => $SedeDB->url_enlace_a_mapa_de_ubicacion,
                'otra_informacion_adicional' => $SedeDB->otra_informacion_adicional,
                'provincia' => $SedeDB->provincia,
                'datos' => $datos
            ];
        }
        

        return View('reportes/mapa-de-sedes')
        ->with('Sedes', $Sedes);

    }


    public function mapaDeSedesArgentinaGeocode()
    {

        $Sedes = DB::connection('ageacac-ar')
            ->table('tb_sede_de_difusion as sd')
            ->select(DB::Raw('sd.id_sede_de_difusion, sd.direccion, l.localidad, sd.telefonos_fijos, sd.telefonos_celulares, sd.correos, sd.latitud_y_longitud_generada_google_maps, sd.latitud_y_longitud_google_maps, sd.url_enlace_a_mapa_de_ubicacion, sd.otra_informacion_adicional, p.provincia'))
            ->leftjoin('tb_localidad as l', 'sd.tb_localidad', '=', 'l.id_localidad')
            ->leftjoin('tb_provincia as p', 'l.tb_provincia', '=', 'p.id_provincia')
            ->whereRaw('sd.habilitada = "SI"')
            //->whereRaw('sd.id_sede_de_difusion = 129')
            ->whereRaw('sd.latitud_y_longitud_google_maps IS NULL AND sd.latitud_y_longitud_generada_google_maps IS NULL')
            //->limit(11)
            ->get();

        

        return View('reportes/mapa-de-sedes-geocode')
        ->with('Sedes', $Sedes);

    }


    public function mapaDeSedes($pais_id)
    {

        $where_raw = "";
        if ($pais_id > 0) {
            $where_raw = "(s.pais_id = $pais_id)";
        }
        else {
            $where_raw = "(1 = 1)";
        }

        $Sedes = DB::table('sedes as s')
            ->select(DB::Raw('s.id, s.direccion, s.ciudad, s.telefono_con_whatsapp, s.email_correo, s.latitud, s.longitud, s.url_enlace_a_google_maps, s.informacion_adicional, s.provincia_estado_o_region, p.pais'))
            ->leftjoin('paises as p', 'p.id', '=', 's.pais_id')
            ->where('s.sino_activa', "SI")
            ->whereRaw($where_raw)
            //->whereNotNull('sd.latitud_y_longitud_google_maps')
            ->get();

        

        return View('reportes/mapa-de-sedes-por-pais')
        ->with('Sedes', $Sedes);

    }

    public function mapaDeInscriptos()
    {


        $Paises = DB::table('paises as p')
            ->select(DB::Raw('p.id, p.pais, p.latitud, p.longitud, COUNT(DISTINCT i.id) cant'))
            ->join('inscripciones as i', 'p.id', '=', 'i.pais_id')
            ->join('solicitudes as s', 's.id', '=', 'i.solicitud_id')
            ->where('s.tipo_de_evento_id', 3)
            //->whereRaw('AND s.tipo_de_evento_id = 3')
            ->groupBy('p.id', 'p.pais', 'p.latitud', 'p.longitud')
            //->whereNotNull('sd.latitud_y_longitud_google_maps')
            ->get();


        return View('reportes/mapa-de-inscriptos-por-pais')
        ->with('mostrar_inscriptos', 'SI')
        ->with('Paises', $Paises);

    }

    public function mapaDeCursos()
    {

        $Paises = DB::table('paises as p')
            ->select(DB::Raw('p.id, p.pais, p.latitud, p.longitud, COUNT(DISTINCT i.id) cant'))
            ->join('inscripciones as i', 'p.id', '=', 'i.pais_id')
            ->join('solicitudes as s', 's.id', '=', 'i.solicitud_id')
            ->where('s.tipo_de_evento_id', 3)
            //->whereRaw('AND s.tipo_de_evento_id = 3')
            ->groupBy('p.id', 'p.pais', 'p.latitud', 'p.longitud')
            //->whereNotNull('sd.latitud_y_longitud_google_maps')
            ->get();


        return View('reportes/mapa-de-inscriptos-por-pais')
        ->with('mostrar_inscriptos', 'NO')
        ->with('Paises', $Paises);

    }

    public function get_redirect_target($url)
    {
        $url_anterior = '';
        while ($url_anterior <> $url) {
            
            $url_anterior = $url;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = curl_exec($ch);
            curl_close($ch);
            // Check if there's a Location: header (redirect)
            if (preg_match('/^Location: (.+)$/im', $headers, $matches)) {
                $url = trim($matches[1]);
            }
            // If not, there was no redirect so return the original URL
            // (Alternatively change this to return false)

        }

        //dd($url_anterior);
        return $url;
    }



    public function extrarLatLonUrlGoogleMaps($url)
    {

        $url = $this->get_redirect_target($url);
        $datos = [
            'url' => $url,
            'latitud' => '',
            'longitud' => '',
            ];

        if ($url <> '') {
            $array_url = explode('!3d', $url);
            if (count($array_url) > 1) {
                $array_url = explode('?', $array_url[1]);
                $array_url = explode('!4d', $array_url[0]);
                if (count($array_url) > 1) {
                    $latitud = $array_url[0];
                    $longitud = $array_url[1];

                    $datos = [
                        'url' => $url,
                        'latitud' => $latitud,
                        'longitud' => $longitud,
                    ];
                }
            }


            if ($datos['latitud'] == '') {
                $array_url = explode('maps?q=', $url);

                if (count($array_url) > 1) {  
                    $array_url = explode(',', $array_url[1]);
                    if (count($array_url) > 1) {
                        $latitud = $array_url[0];
                        $longitud = $array_url[1];
                        if (is_numeric($latitud) and is_numeric($longitud)) {
                            $datos = [
                                'url' => $url,
                                'latitud' => $latitud,
                                'longitud' => $longitud,
                            ];
                        }
                    }
                }
            }
        }


        return $datos;
    }


    public function GuardarLatYLong(Request $request) {
        
        $fecha_de_evento_id = $_POST['fecha_de_evento_id'];
        $latitud = $_POST['latitud'];
        $longitud = $_POST['longitud'];

        $Fecha_de_evento = Fecha_de_evento::find($fecha_de_evento_id);
        $Fecha_de_evento->latitud = $latitud;
        $Fecha_de_evento->longitud = $longitud;
        $Fecha_de_evento->save();

        echo "SAVE -> Latitud: $latitud y Longitud: $longitud";

    }



    public function listCursos()
    {

        /*
        if ($pais_id <> null) {
            $where_raw_pais_id = "(p.id = $pais_id or s.pais_id = $pais_id)";
        }        
        */

        $campos_select = 's.id, s.`hash`, p.provincia, ';
        $Solicitudes = DB::table('solicitudes as s')
            ->select(DB::Raw('s.id, s.hash, p.pais, pr.provincia, IFNULL(s.escribe_tu_ciudad_sino_esta_en_la_lista_anterior, l.localidad) ciudad_sol, COUNT(DISTINCT i.id) cant_inscriptos'))
            ->leftjoin('inscripciones as i', 's.id', '=', 'i.solicitud_id')
            ->leftjoin('localidades as l', 'l.id', '=', 's.localidad_id')
            ->leftjoin('provincias as pr', 'pr.id', '=', 'l.provincia_id')
            ->leftjoin('paises as p', 'p.id', '=', 'pr.pais_id')
            ->groupBy(DB::Raw('s.id, s.hash, p.pais, pr.provincia, ciudad_sol'))
            ->whereRaw("s.sino_aprobado_administracion = 'SI' and (s.sino_cancelada IS NULL OR s.sino_cancelada = 'NO')")
            ->whereRaw("MONTH(s.fecha_de_solicitud) = 4 AND YEAR(s.fecha_de_solicitud) = 2020")
            ->whereRaw("(p.id = 1 or s.pais_id = 1)")
            //->whereNotNull('sd.latitud_y_longitud_google_maps')
            ->get();



        return View('reportes/list-cursos')
        ->with('mostrar_inscriptos', 'SI')
        ->with('Solicitudes', $Solicitudes);

    }


    public function listaDeSedes($pais_id)
    {


        $where_raw = "";
        if ($pais_id > 0) {
            $Pais = Pais::find($pais_id);
            $where_raw = "(s.pais_id = $pais_id)";
            $titulo = 'GNOSIS '.__('Sedes').' '.$Pais->pais;
            $mostrar_pais = false;
        }
        else {
            $Pais = null;
            $where_raw = "(1 = 1)";
            $titulo = 'GNOSIS '.__('Sedes');
            $mostrar_pais = true;
        }

        $Sedes = DB::table('sedes as s')
            ->select(DB::Raw('s.id, s.direccion, s.ciudad, s.telefono_con_whatsapp, s.email_correo, s.latitud, s.longitud, s.url_enlace_a_google_maps, s.informacion_adicional, s.provincia_estado_o_region, p.pais, p.codigo_tel'))
            ->leftjoin('paises as p', 'p.id', '=', 's.pais_id')
            ->where('s.sino_activa', "SI")
            ->whereRaw('s.id <> 962')
            ->whereRaw($where_raw)
            ->orderBy('p.pais')
            ->orderBy('s.provincia_estado_o_region')
            ->orderBy('s.ciudad')
            //->whereNotNull('sd.latitud_y_longitud_google_maps')
            ->get();


        return View('reportes/lista-de-sedes')
        ->with('mostrar_inscriptos', 'SI')
        ->with('titulo', $titulo)
        ->with('mostrar_pais', $mostrar_pais)
        ->with('Pais', $Pais)
        ->with('Sedes', $Sedes);

    }



    public function urlSedesPaises()
    {
        $Paises = Pais::whereRaw('id in (SELECT s.pais_id FROM sedes s)')->orderBy('pais', 'DESC')->get();

        return View('reportes/url-sedes-paises')
        ->with('mostrar_inscriptos', 'SI')
        ->with('Paises', $Paises);

    }




    public function mostrarFlyers($solicitud_id) {
        $Solicitud = Solicitud::find($solicitud_id);

        return View('forms/mostrar-flyers')        
        ->with('Solicitud', $Solicitud)
        ->with('dominio_publico', $Solicitud->dominioPublico());
    }

    public function crearFlyer($solicitud_id, $template) {

        $Solicitud = Solicitud::find($solicitud_id);


        if ($Solicitud->idioma_id <> '') {
            $idioma = $Solicitud->idioma->mnemo;
            App::setLocale($idioma);
        }
        
        $img = Image::make(env('PATH_PUBLIC_INTERNO').'img/temp-curso-'.$template.'.jpg');

        //$dir_fuente = env('PATH_PUBLIC_INTERNO').'fonts/Roboto-Regular.ttf';
        $dir_fuente = env('PATH_PUBLIC_INTERNO').'fonts/Roboto-Bold.ttf';

        $con_html = false;
        $titulo = $Solicitud->descripcion_sin_estado($con_html);
        $titulo = $this->to_unicode($titulo);
        $img->text($titulo, 20, 170, function($font) use ($dir_fuente) {
            $font->file($dir_fuente);
            $font->size(23);
            $font->color('#000');
            //$font->align('center');
            $font->valign('top');
            $font->angle(12);
        });
  

        $detalle_horarios_y_lugar = __('HORARIOS').': '."\n";
        $i = 0;
        foreach ($Solicitud->fechas_de_evento as $Fecha_de_evento) {
            $i++;
            if ($i <= 2) {
                $tipo = 'whatsapp';
                $con_inicio = true;
                $Idioma_por_pais = $Solicitud->idioma_por_pais();
                $Solicitud2 = null; 
                $idioma = null; 
                $ver_mapa = false;
                $con_dir_inicio_distinto = true;

                $detalle_horarios_y_lugar .= $Fecha_de_evento->armarDetalleFechasDeEventos($tipo, $con_inicio, $Idioma_por_pais, $Solicitud2, $idioma, $ver_mapa, $con_dir_inicio_distinto)."\n";
            }
            else {
                if ($i == 3) {
                    $detalle_horarios_y_lugar .= __('Consulte por otros días y horarios')."\n";
                }
            }

            //$detalle_horarios_y_lugar .= $Fecha_de_evento->dias_y_horarios()."\n";
            
        }
        //$detalle_horarios_y_lugar = str_replace('é', 'e', $detalle_horarios_y_lugar);
        //$detalle_horarios_y_lugar = utf8_decode('é');

        //$detalle_horarios_y_lugar = convert_cyr_string ($detalle_horarios_y_lugar, 'w', 'i');
        //setlocale(LC_CTYPE, 'es_ES');
        //dd($detalle_horarios_y_lugar);
        //dd($detalle_horarios_y_lugar);
        $detalle_horarios_y_lugar = $this->to_unicode($detalle_horarios_y_lugar);
        $img->text($detalle_horarios_y_lugar, 730, 680, function($font) use ($dir_fuente) {
            $font->file($dir_fuente);
            $font->size(14);
            $font->color('#fdf6e3');
            $font->align('right');
            $font->valign('bottom');
            //$font->angle(45);
        });


        $url_fanpage = str_replace('https://www.', '', $Solicitud->idioma_por_pais()->url_fanpage);
        $url_fanpage = $this->to_unicode($url_fanpage);
        $img->text($url_fanpage, 110, 715, function($font) use ($dir_fuente) {
            $font->file($dir_fuente);
            $font->size(13);
            $font->color('#fdf6e3');
            //$font->align('center');
            $font->valign('top');
            //$font->angle(45);
        });

        $url_sitio_web = str_replace('http://', '', $Solicitud->idioma_por_pais()->url_sitio_web);
        $url_sitio_web = str_replace('http2://', '', $url_sitio_web);
        $url_sitio_web = str_replace('www.', '', $url_sitio_web);

        $url_sitio_web = $this->to_unicode($url_sitio_web);
        $img->text($url_sitio_web, 350, 715, function($font) use ($dir_fuente) {
            $font->file($dir_fuente);
            $font->size(13);
            $font->color('#fdf6e3');
            //$font->align('center');
            $font->valign('top');
            //$font->angle(45);
        });

        $celular_responsable_de_inscripciones = $this->to_unicode($Solicitud->celular_responsable_de_inscripciones);
        $img->text($celular_responsable_de_inscripciones, 590, 715, function($font) use ($dir_fuente) {
            $font->file($dir_fuente);
            $font->size(17);
            $font->color('#fdf6e3');
            //$font->align('center');
            $font->valign('top');
            //$font->angle(45);
        });

        // save file as jpg with medium quality
        $img->save(env('PATH_PUBLIC_INTERNO').'img/nando-1.jpg', 60);
        //$img->response();

        return $img->response('jpg', 60);
    }

    public function to_unicode($string)
    {
        //dd($string);
        //$string = str_replace('\n', '-----', $string);
        //$string = "\n";
        $str = mb_convert_encoding($string, 'UCS-2', 'UTF-8');
        //dd($str);
        $arrstr = str_split($str, 2);
        $unistr = '';
        foreach ($arrstr as $n) {
            $dec = hexdec(bin2hex($n));
            $unistr .= '&#' . $dec . ';';
        }
        //dd($unistr);
        $unistr = str_replace('&#10;', "\n", $unistr);
        //dd($unistr);
        return $unistr;
    }



    public function saveTest(Request $request) {
        
        $texto = $_POST['texto'];
        $inscripcion_id = $_POST['inscripcion_id'];
        $puntuacion = $_POST['puntuacion'];
        $nombre_y_apellido = $_POST['nombre_y_apellido'];
        $modelo_de_evaluacion_id = $_POST['modelo_de_evaluacion_id'];

        $Evaluacion = new Evaluacion();
        $Evaluacion->texto = $texto;
        $Evaluacion->inscripcion_id = $inscripcion_id;
        $Evaluacion->puntuacion = $puntuacion;
        $Evaluacion->nombre_y_apellido = $nombre_y_apellido;
        $Evaluacion->modelo_de_evaluacion_id = $modelo_de_evaluacion_id;
        $Evaluacion->save();

        $Inscripcion = Inscripcion::find($inscripcion_id);
        if ($Inscripcion->count() > 0) {
            $Inscripcion->ultima_evaluacion = $Evaluacion->id;
            $Inscripcion->save();
        }
        
        $FormController = new FormController();        
        $forzar = false;
        $FormController->promocionarInscripto($Inscripcion, $forzar);

    }



    public function saveWabot(Request $request) {

        
        $callbacks = json_decode($request->getContent(), true);
        $callbacks = json_decode($callbacks, true);
        //dd($callbacks);
        
        $celular = $callbacks['callerid'];
        $dialog = $callbacks['dialog'];
        $username = $callbacks['dialog']['username'];
        $startmsg = ltrim($callbacks['dialog']['startmsg']);

        $palabra_clave_array = explode(' ', $startmsg);
        $palabra_clave = $palabra_clave_array[0];

        $palabras_claves_autorizadas = [
            'gnosis_in',
            'gnosis_in_pt',
            'gnosis_in_en',
            'gnosis_in_fr',
            'gnosis_inscripcion',
            '#gnosis_inscripcion',
            'gnosis_pre',
            'gnosis_pre_pt',
            'gnosis_pre_en',
            'gnosis_pre_fr'
        ];

        if (in_array($palabra_clave, $palabras_claves_autorizadas)) {     

            if ($palabra_clave == 'gnosis_in' or $palabra_clave == 'gnosis_in_pt' or $palabra_clave == 'gnosis_in_en' or $palabra_clave == 'gnosis_in_fr' or $palabra_clave == 'gnosis_inscripcion' or $palabra_clave == '#gnosis_inscripcion') {

                try {

                    $nombre = str_replace(array("\n", "\t", "\r"), '', $callbacks['interactions'][0]['answer']);
                    $nombre = $this->limpiarCadena($nombre);

                    if ($palabra_clave == 'gnosis_inscripcion' or $palabra_clave == '#gnosis_inscripcion') {
                        $ciudad = NULL;
                        $email = str_replace(array("\n", "\t", "\r",' '), '', $callbacks['interactions'][1]['answer']);


                    }
                    else {
                        $ciudad = str_replace(array("\n", "\t", "\r"), '', $callbacks['interactions'][1]['answer']);
                        $ciudad = $this->limpiarCadena($ciudad);

                        $email = str_replace(array("\n", "\t", "\r",' '), '', $callbacks['interactions'][2]['answer']);
                        $email = $this->limpiarCadena($email);
                    }

                    $startmsg_array = explode('a#', $startmsg);
                    if (count($startmsg_array) > 1) {
                        $startmsg_array_2 =  explode(' ', $startmsg_array[1]);
                        $solicitud_id = trim($startmsg_array_2[0]);
                    }
                    else {
                        $solicitud_id = 5909;   
                    }
                    //dd($solicitud_id);

                    $Solicitud = Solicitud::find($solicitud_id);
                    if ($Solicitud->fechas_de_evento->count() > 0) {
                        $Fecha_de_evento = $Solicitud->fechas_de_evento->slice(0, 1)->all();
                        $fecha_de_evento_id = $Fecha_de_evento[0]->id;
                    }
                    else {
                        $fecha_de_evento_id = null;    
                    }

                    if (substr($celular, 0, 2) == '55' and strlen($celular) == 12) {
                        $celular = substr($celular, 0, 4).'9'.$celular = substr($celular, 4, 8);
                    }            


                    $Inscripcion = new Inscripcion();      
                    $Inscripcion->solicitud_id = $solicitud_id;
                    $Inscripcion->fecha_de_evento_id = $fecha_de_evento_id;
                    //$Inscripcion->consulta = substr($request->getContent(), 0, 2000);
                    $Inscripcion->celular = '+'.$celular;
                    $Inscripcion->ciudad = substr($ciudad, 0, 60);
                    $Inscripcion->nombre = substr($nombre, 0, 45);
                    $Inscripcion->email_correo = substr($email, 0, 80);
                    $Inscripcion->codigo_alumno = $Inscripcion->codigo_del_alumno();          
                    $Inscripcion->save();    


                    $MauticController = new MauticController();
                    $systemsource = 'gnosis-incripcion-whatsapp';
                    $apellido = null;
                    $pais_id = null;                 
                    $MauticController->guardarContacto($Inscripcion->id, $Inscripcion->solicitud, $systemsource, $nombre, $apellido, $celular, $email, $pais_id, $ciudad);


                } catch(\Illuminate\Database\QueryException $ex){ 
                    $detalle_de_origen = 'Inscripcion Wabot: '.$request->getContent();
                    $Registro_de_error = new Registro_de_error;
                    $Registro_de_error->registro_de_error = $ex->getMessage();
                    $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                    $Registro_de_error->save();              
                  // Note any method of class PDOException can be called on $ex.
                }                
            }
        
            if ($palabra_clave == 'gnosis_pre' or $palabra_clave == 'gnosis_pre_pt' or $palabra_clave == 'gnosis_pre_en' or $palabra_clave == 'gnosis_pre_fr') {
                
                $aula_array = explode('#', $palabra_clave_array[1]);
                $aula = $aula_array[1];
                $aula = $this->limpiarCadena($aula);

                $clase_array = explode('#', $palabra_clave_array[2]);
                $clase = $clase_array[1];
                $clase = $this->limpiarCadena($clase);


                $sino = strtoupper(trim($callbacks['interactions'][0]['answer']));

                $celular_sin_9 = $celular;
                if (substr($celular, 2, 1) == 9 and substr($celular, 0, 2) == '54') {
                    $celular_sin_9 = substr($celular, 0, 2).substr($celular, 3);
                    //dd($celular_sin_9);
                }

                if (substr($celular, 2, 1) == 1 and substr($celular, 0, 2) == '52') {
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


                
                $Solicitud = Solicitud::where('id', $aula)->get();
                
                $error_flag = false;
                $es_leccion_normal = true;
                if ($Solicitud->count() > 0) {

                    if ( ($palabra_clave == 'gnosis_pre' and (strpos($sino, 'S') !== false)) or ($palabra_clave == 'gnosis_pre_pt' and (strpos($sino, 'S') !== false)) or ($palabra_clave == 'gnosis_pre_en' and (strpos($sino, 'Y') !== false)) or ($palabra_clave == 'gnosis_pre_fr' and (strpos($sino, 'OU') !== false)) ) {

                        $primera_letra_clase = substr($clase, 0, 1);
                        if ($primera_letra_clase == 'X') {
                            $es_leccion_normal = false;
                            $leccion_extra_id = substr($clase, 1);
                            $Leccion = Leccion_extra::where('id', $leccion_extra_id)->get();
                        }
                        else {                    
                            $Leccion = Leccion::where('curso_id', $Solicitud[0]->curso_id)->where('codigo_de_la_leccion', $clase)->get();
                        }
                        
                        $Inscripciones = Inscripcion::where('solicitud_id', $Solicitud[0]->id)
                        ->whereRaw("(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(celular,'#',''),')',''),'(',''),'-',''),' ',''),'+','') like '%".$celular."%' OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(celular,'#',''),')',''),'(',''),'-',''),' ',''),'+','') like '%".$celular_sin_9."%')")
                        ->get();

                          
                        if ($Leccion->count() > 0) {
                            foreach ($Inscripciones as $Inscripcion) {
                                
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
                        }
                        else {
                            $error_flag = true;
                            $Asistencia_error = new Asistencia();
                            $Asistencia_error->log = 'Leccion No Encontrada|'.$request->getContent();
                            $Asistencia_error->save(); 
                        }              

                        if ($Inscripciones->count() == 0) {
                            $error_flag = true;
                            $Asistencia_error = new Asistencia();
                            $Asistencia_error->log = 'Inscripcion No Encontrada|'.$request->getContent();
                            $Asistencia_error->save();
                        }

                    }
                    else {
                        $error_flag = true;
                        $Asistencia_error = new Asistencia();
                        $Asistencia_error->log = 'Respuesta Negativa |'.$request->getContent();
                        $Asistencia_error->save(); 
                    }

                }
                else {
                    $error_flag = true;
                    $Asistencia_error = new Asistencia();
                    $Asistencia_error->log = 'Solicitud No Encontrada|'.$request->getContent();
                    $Asistencia_error->save(); 
                }
                  

            }
        }
        else {
            $error_flag = true;
            $Asistencia_error = new Asistencia();
            $Asistencia_error->log = 'Palabra Clave No encontrada|'.$request->getContent();
            $Asistencia_error->save();             
        }

        $status = 'ok';

        return $status;
        

    }



    public function printCertificado($inscripcion_id, $hash)
    {  
        if (md5(ENV('PREFIJO_HASH').$inscripcion_id) == $hash) {
            $Inscripcion = Inscripcion::find($inscripcion_id);

            $Solicitud = $Inscripcion->solicitud;
            $Idioma_por_pais = $Solicitud->idioma_por_pais();

    /*
            $mensaje = 'Vision de Lista';
            $ch = curl_init("https://api.telegram.org/bot".ENV('TELEGRAM_BOT_TOKEN')."/sendMessage?chat_id=632979534&text=".$mensaje);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Configura cURL para devolver el resultado como cadena
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Configura cURL para que no verifique el peer del certificado dado que nuestra URL utiliza el protocolo HTTPS
            $info = curl_exec($ch); // Establece una sesión cURL y asigna la información a la variable $info
            curl_close($ch); // Cierra sesión cURL
    */

            $hash = md5(ENV('PREFIJO_HASH').$inscripcion_id);
            $url_qrcode = ENV('PATH_PUBLIC').'f/detalle-de-certificado/'.$inscripcion_id.'/'.$hash;
            $dir_imagen = env('PATH_PUBLIC_INTERNO').'qrcode/inscripciones/inscripcion-'.$inscripcion_id.'.png';
            $dir_imagen_url = env('PATH_PUBLIC').'qrcode/inscripciones/inscripcion-'.$inscripcion_id.'.png';

            QrCode::format('png');
            QrCode::size(200);
            QrCode::generate($url_qrcode, $dir_imagen);

            $cant_lecciones = Asistencia::where('inscripcion_id', $inscripcion_id)->distinct('leccion_id')->count('leccion_id');
            
            if ($cant_lecciones == 0) {
                $cant_lecciones = 23;
            }
            

            $idioma = $Idioma_por_pais->idioma->mnemo;
            App::setLocale($idioma);   

            $texto1 = __('Ha completado con éxito las').' '.$cant_lecciones.' '.__('lecciones del Curso de Auto Conocimiento On Line.');
            //$texto1 = __('Ha completado con éxito las');

            $firma = env('PATH_PUBLIC').'img/certificados/1/firma.png';

            if ($Idioma_por_pais->pais_id > 0) {
                $pais_id = $Idioma_por_pais->pais_id;
                $firma_file = env('PATH_PUBLIC_INTERNO').'img/certificados/1/firma_'.$pais_id.'.png';
                if (file_exists($firma_file)) {
                    $firma = env('PATH_PUBLIC').'img/certificados/1/firma_'.$pais_id.'.png';
                }
            }

            //dd($firma);
            $txt_numero_certificado = __('Certificado').' #'.$inscripcion_id;

            /*
            $data = [
                'Inscripcion' => $Inscripcion, 
                'dir_imagen_url' => $dir_imagen_url, 
                'firma' => $firma, 
                'texto1' => $texto1
            ];

            $pdf = PDF::loadView('forms.certificado', $data)->setPaper('a3', 'landscape');
            return $pdf->download('invoice.pdf');
            */

            $mnemo_lang = $Idioma_por_pais->idioma->mnemo;

            if (in_array($mnemo_lang, ['es-ve', 'es-es'])) {
                $mnemo_lang = 'es';                
            }

                  
            $blade_certificado = 'certificado';
            
            if ($Solicitud->id == 6227) {
                $blade_certificado = 'certificado2';    
            }

            return View('forms/'.$blade_certificado)        
            ->with('Inscripcion', $Inscripcion) 
            ->with('dir_imagen_url', $dir_imagen_url)
            ->with('firma', $firma)
            ->with('texto1', $texto1)
            ->with('txt_numero_certificado', $txt_numero_certificado)
            ->with('mnemo_lang', $mnemo_lang);   
        }
        else {
            echo 'ERROR';
        }  

    }




    public function printCertificadoPDF($inscripcion_id, $hash)
    {  
        if (md5(ENV('PREFIJO_HASH').$inscripcion_id) == $hash) {
            error_reporting(E_ALL ^ E_DEPRECATED);
            $Inscripcion = Inscripcion::find($inscripcion_id);

            $Solicitud = $Inscripcion->solicitud;
            $Idioma_por_pais = $Solicitud->idioma_por_pais();

    /*
            $mensaje = 'Vision de Lista';
            $ch = curl_init("https://api.telegram.org/bot".ENV('TELEGRAM_BOT_TOKEN')."/sendMessage?chat_id=632979534&text=".$mensaje);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Configura cURL para devolver el resultado como cadena
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Configura cURL para que no verifique el peer del certificado dado que nuestra URL utiliza el protocolo HTTPS
            $info = curl_exec($ch); // Establece una sesión cURL y asigna la información a la variable $info
            curl_close($ch); // Cierra sesión cURL
    */

            $hash = md5(ENV('PREFIJO_HASH').$inscripcion_id);
            $url_qrcode = ENV('PATH_PUBLIC').'f/detalle-de-certificado/'.$inscripcion_id.'/'.$hash;
            $dir_imagen = env('PATH_PUBLIC_INTERNO').'qrcode/inscripciones/inscripcion-'.$inscripcion_id.'.png';
            $dir_imagen_url = env('PATH_PUBLIC').'qrcode/inscripciones/inscripcion-'.$inscripcion_id.'.png';

            QrCode::format('png');
            QrCode::size(200);
            QrCode::generate($url_qrcode, $dir_imagen);

            $cant_lecciones = Asistencia::where('inscripcion_id', $inscripcion_id)->distinct('leccion_id')->count('leccion_id');
            
            if ($cant_lecciones == 0) {
                $cant_lecciones = 23;
            }
            

            $idioma = $Idioma_por_pais->idioma->mnemo;
            App::setLocale($idioma);   

            $texto1 = __('Ha completado con éxito las').' '.$cant_lecciones.' '.__('lecciones del Curso de Auto Conocimiento On Line.');
            //$texto1 = __('Ha completado con éxito las');

            $firma = env('PATH_PUBLIC').'img/certificados/1/firma.png';

            if ($Idioma_por_pais->pais_id > 0) {
                $pais_id = $Idioma_por_pais->pais_id;
                $firma_file = env('PATH_PUBLIC_INTERNO').'img/certificados/1/firma_'.$pais_id.'.png';
                if (file_exists($firma_file)) {
                    $firma = env('PATH_PUBLIC').'img/certificados/1/firma_'.$pais_id.'.png';
                }
            }

            //dd($firma);
            $txt_numero_certificado = __('Certificado').' #'.$inscripcion_id;

            /*
            $data = [
                'Inscripcion' => $Inscripcion, 
                'dir_imagen_url' => $dir_imagen_url, 
                'firma' => $firma, 
                'texto1' => $texto1
            ];

            $pdf = PDF::loadView('forms.certificado', $data)->setPaper('a3', 'landscape');
            return $pdf->download('invoice.pdf');
            */

            $mnemo_lang = $Idioma_por_pais->idioma->mnemo;

            if (in_array($mnemo_lang, ['es-ve', 'es-es'])) {
                $mnemo_lang = 'es';                
            }

            $data = [
                'Inscripcion' => $Inscripcion, 
                'dir_imagen_url' => $dir_imagen_url, 
                'firma' => $firma, 
                'texto1' => $texto1, 
                'txt_numero_certificado' => $txt_numero_certificado, 
                'mnemo_lang' => $mnemo_lang
            ];

            $pdf = PDF::loadView('forms.certificado-pdf', $data)->setPaper('c4', 'landscape');

            return $pdf->download('invoice.pdf');    
        }
        else {
            echo 'ERROR';
        }  

    }



    public function detalleDeCertificado($inscripcion_id, $hash)
    {   

        
        if (md5(ENV('PREFIJO_HASH').$inscripcion_id) == $hash) {
            $Inscripcion = Inscripcion::find($inscripcion_id);
            $Asistencias = Asistencia::where('inscripcion_id', $inscripcion_id)->get();

            $Inscripcion = Inscripcion::find($inscripcion_id);
            $Lecciones = DB::table('asistencias as a')
            ->select(DB::Raw('l.nombre_de_la_leccion, a.created_at'))
            ->join('lecciones as l', 'l.id', '=', 'a.leccion_id')
            ->where('a.inscripcion_id', $inscripcion_id)
            ->get();

            $Evaluaciones = DB::table('evaluaciones as e')
            ->select(DB::Raw('me.titulo_de_la_evaluacion, e.texto, e.created_at, e.puntuacion'))
            ->join('modelos_de_evaluacion as me', 'me.id', '=', 'e.modelo_de_evaluacion_id')
            ->where('e.inscripcion_id', $inscripcion_id)
            ->get();

            $Idioma_por_pais = $Inscripcion->solicitud->idioma_por_pais();
            $idioma = $Idioma_por_pais->idioma->mnemo;


            return View('forms/detalle-de-certificado')        
            ->with('Inscripcion', $Inscripcion)  
            ->with('Evaluaciones', $Evaluaciones)
            ->with('Lecciones', $Lecciones)
            ->with('idioma', $idioma);            
            }
        else {
            echo 'ERROR';
        }  


    }


    public function paginaEnlacesAsistenciaWabot($solicitud_id, $hash)
    {   

        if ($solicitud_id > 0) {
            $Solicitud = Solicitud::find($solicitud_id);
            $Idioma_por_pais = $Solicitud->idioma_por_pais();


            if ($Solicitud->hash == $hash) {  

                $Lecciones = Leccion::where('curso_id', $Solicitud->curso_id)->get();
                $Lecciones_extra = [];


                return View('forms/pagina-enlaces-asistencia-wabot')        
                ->with('Solicitud', $Solicitud)  
                ->with('Idioma_por_pais', $Idioma_por_pais)  
                ->with('Lecciones', $Lecciones)
                ->with('Lecciones_extra', $Lecciones_extra);
                }
            else {
                echo 'ERROR';
                }
            }
        else {
            echo 'ERROR';
        }  


    }


    public function paginaEnlacesAsistenciaExtraWabot($solicitud_id, $hash)
    {   

        if ($solicitud_id > 0) {
            $Solicitud = Solicitud::find($solicitud_id);

            if ($Solicitud->hash == $hash) {  

                $Lecciones = Leccion::where('curso_id', $Solicitud->curso_id)->get();
                $Lecciones_extra = Leccion_extra::all();

                return View('forms/pagina-enlaces-asistencia-wabot')        
                ->with('Solicitud', $Solicitud)  
                ->with('Lecciones', $Lecciones)
                ->with('Lecciones_extra', $Lecciones_extra);
                }
            else {
                echo 'ERROR';
                }
            }
        else {
            echo 'ERROR';
        }  


    }




    public function limpiarCadena($cadena) {
        $cadena_limpia = trim($cadena);
        $cadena_limpia = str_replace("'", "’", $cadena);
        $cadena_limpia = str_replace('"', "", $cadena_limpia);
        return $cadena_limpia;
        }



}
