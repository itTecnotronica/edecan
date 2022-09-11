<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Opcion;
use App\KEY_COLUMN_USAGE;
use App\User;
use Auth;
use App\Http\Controllers\ParticularController;
use App\Http\Controllers\TablasEnPlurarl;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Storage;

use DateTime;
use DateTimeZone;

use App;

use Carbon\Carbon;
use App\Notifications\TelegramNotification;

class GenericController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function index($gen_modelo, $gen_opcion)
    {
        
        

        $gen_campos_a_ocultar = array('empresa_id');
        if ($gen_opcion > 0) {
            $Opcion = Opcion::where('id', $gen_opcion)->get();
            $campos_a_ocultar_array = explode('|', $Opcion[0]->no_listar_campos);
            foreach ($campos_a_ocultar_array as $campos_a_ocultar) {
                array_push($gen_campos_a_ocultar, $campos_a_ocultar);  
            }
        }
        $gen_campos = $this->traer_campos($gen_modelo, $gen_campos_a_ocultar);
        $gen_permisos = [
            'C',
            'R',
            'U',
            'D'
            ];
        $gen_filas = call_user_func(array($this->dirModel($gen_modelo), 'all'), '*');

        return View('genericas/list')
        ->with('gen_campos', $gen_campos)
        ->with('gen_filas', $gen_filas)
        ->with('gen_modelo', $gen_modelo)
        ->with('gen_permisos', $gen_permisos)
        ->with('gen_opcion', $gen_opcion);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($gen_modelo)
    {        

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $gen_modelo = $request->gen_modelo;
        $gen_accion = $request->gen_accion;
        $gen_id = $request->gen_id;
        $gen_opcion = $request->gen_opcion;
        $gen_url_siguiente = $request->gen_url_siguiente;
        $gen_seteo = unserialize(stripslashes($request->gen_seteo));
        $gen_campos_a_ocultar = ['id'];

        //AGREGO LOS CAMPOS QUE NO HAY QUE GUARDAR
        if (isset($gen_seteo['no_mostrar_campos_abm'])) {
            if (is_array($gen_seteo['no_mostrar_campos_abm'])) {
                foreach ($gen_seteo['no_mostrar_campos_abm'] as $campo_no_guardar) {
                    array_push($gen_campos_a_ocultar, $campo_no_guardar);
                }
            }
        }


        $gen_campos = $this->traer_campos($gen_modelo, $gen_campos_a_ocultar);

        $gen_permisos = [
            'C',
            'R',
            'U',
            'D'
            ];

        $ParticularController = new ParticularController();
        $mensaje = $ParticularController->accionesAnteriores($gen_modelo, $gen_accion, $gen_id, $request);
        //dd($mensaje);


       

        if (!$mensaje['error']) {

            // Filtro los campos no mostrar que vengan por variable FORM
            $gen_seteo = unserialize(stripslashes($_POST['gen_seteo']));
            $no_mostrar_campos_abm = [];

            if (isset($gen_seteo['no_mostrar_campos_abm'])) {
                $no_mostrar_campos_abm_mas = $gen_seteo['no_mostrar_campos_abm'];
                if (is_array($no_mostrar_campos_abm_mas)) {
                    foreach ($no_mostrar_campos_abm_mas as $no_mostrar_campo) {
                        array_push($no_mostrar_campos_abm, $no_mostrar_campo);  
                    } 
                }
                else {
                    $array_no_mostrar_campos_abm_mas = explode('|', $no_mostrar_campos_abm_mas);
                    foreach ($array_no_mostrar_campos_abm_mas as $no_mostrar_campo) {
                        array_push($no_mostrar_campos_abm, $no_mostrar_campo);  
                    }        
                }     
            }

            //dd($no_mostrar_campos_abm);

            // Filtro los campos no mostrar que vengan por OPCION

            if ($gen_opcion > 0) {
                $Opcion = Opcion::where('id', $gen_opcion)->get();
                // Traigo los campos a Ocultar
                $no_mostrar_campos_abm_mas = $Opcion[0]->no_mostrar_campos_abm;  

                if (isset($no_mostrar_campos_abm_mas) and $no_mostrar_campos_abm_mas <> '') {
                    $array_no_mostrar_campos_abm_mas = explode('|', $no_mostrar_campos_abm_mas);
                    foreach ($array_no_mostrar_campos_abm_mas as $no_mostrar_campo) {
                        array_push($no_mostrar_campos_abm, $no_mostrar_campo);  
                    } 
                }
            }     





            if ($gen_accion == 'a') {
                foreach ($gen_campos as $campo) {                 
                    $nombre = $campo['nombre'];

                    $array_nombre = explode('_', $nombre);
                    $valor = $request->$nombre;

                    /*
                    if ($array_nombre[0] == 'rtf') {
                        $valor = preg_replace("/[\r\n|\n|\r]+/", "", $request->$nombre);
                    }
                    */

                    //if ($array_nombre[0] == 'urlencode' or $array_nombre[0] == 'rtf') {
                    if ($array_nombre[0] == 'urlencode') {
                        $valor = urlencode($request->$nombre);
                    }

                    // file
                    if ($array_nombre[0] == 'file') {
                        $nuevo_file = $request->file($nombre.'_nuevo');
                        if ($nuevo_file <> null) {
                            $valor = $request->file($nombre.'_nuevo')->store($gen_modelo);
                        }
                    } 

                    if ($campo['tipo'] == 'date') {
                        if ($valor <> '') {
                            $valor = $this->FormatoFecha($valor);
                        }
                        else {
                            $valor = NULL;
                        }
                    }
                    if ($campo['tipo'] == 'datetime') {
                        if ($valor <> '') {
                            $valor = $this->FormatoFechayYHora($valor);
                        }
                        else {
                            $valor = NULL;
                        }                        
                    }

                    // sino
                    if ($array_nombre[0] == 'sino') {
                        if ($valor <> 'SI') {
                            $valor = 'NO';
                        }
                    } 

                    $valores[$nombre] = $this->limpiarCadena($valor);

                }            

                try {
                    $resultado = call_user_func(array($this->dirModel($gen_modelo), 'create'), $valores);     
                    $gen_id = $resultado->id;
                    $mensaje['error'] = false;
                    $mensaje['detalle'] = 'Inserci&oacute;n exitosa';

                } catch (\Illuminate\Database\QueryException $e) {
                    $mensaje = $this->MensajeErrorDB($e->errorInfo, $gen_modelo);
                } 


            }
            if ($gen_accion == 'm') {     
                $registro = call_user_func(array($this->dirModel($gen_modelo), 'find'), $gen_id);   
                foreach ($gen_campos as $campo) { 
                    $nombre = $campo['nombre'];

                    if (!in_array($nombre, $no_mostrar_campos_abm)) {

                        $array_nombre = explode('_', $nombre);
                        $valor = $request->$nombre;

                        /*
                        if ($array_nombre[0] == 'rtf') {
                            $valor = preg_replace("/[\r\n|\n|\r]+/", "", $request->$nombre);
                        }
                        */

                        //if ($array_nombre[0] == 'urlencode' or $array_nombre[0] == 'rtf') {
                        if ($array_nombre[0] == 'urlencode') {                            
                            $valor = urlencode($request->$nombre);
                        }

                        // file
                        if ($array_nombre[0] == 'file') {
                            $nuevo_file = $request->file($nombre.'_nuevo');
                            if ($nuevo_file <> null) {
                                $valor = $request->file($nombre.'_nuevo')->store($gen_modelo);
                                if ($request->$nombre <> ''and Storage::exists($request->$nombre)) {
                                    Storage::delete($request->$nombre);
                                }
                            }
                        }       

                        if ($campo['tipo'] == 'date') {
                            if ($valor <> '') {   
                                $valor = $this->FormatoFecha($valor);
                            }
                            else {
                                $valor = NULL;
                            } 
                        }

                        if ($campo['tipo'] == 'datetime') {
                            if ($valor <> '') {   
                                $valor = $this->FormatoFechayYHora($valor);
                            }
                            else {
                                $valor = NULL;
                            }                             
                        }

                        if ($campo['tipo'] == 'time') {
                            if ($valor == '') { 
                                $valor = NULL;
                            }                             
                        }

                        // sino
                        if ($array_nombre[0] == 'sino') {
                            if ($valor <> 'SI') {
                                $valor = 'NO';
                            }
                        } 
                        $registro->$nombre = $this->limpiarCadena($valor);
                        //$registro->$nombre = $valor;
                    }

                }     
                try {
                    $registro->save(); 
                    $mensaje['error'] = false;
                    $mensaje['detalle'] = 'Modificaci&oacute;n exitosa';

                } catch (\Illuminate\Database\QueryException $e) {
                    //dd($e);
                    $mensaje = $this->MensajeErrorDB($e->errorInfo, $gen_modelo);
                }      

                
            }           
            if ($gen_accion == 'b') {   

                // file
                foreach ($gen_campos as $campo) {
                    $nombre = $campo['nombre'];       
                    $array_nombre = explode('_', $nombre);
                    if ($array_nombre[0] == 'file') {
                        if ($request->$nombre <> '' and Storage::exists($request->$nombre)) {
                            Storage::delete($request->$nombre);
                        }
                    }  
                }  

                $registro = call_user_func(array($this->dirModel($gen_modelo), 'find'), $gen_id);  
                try {
                    $registro->delete();
                    $mensaje['error'] = false;
                    $mensaje['detalle'] = 'Eliminaci&oacute;n exitosa';

                } catch (\Illuminate\Database\QueryException $e) {
                    $mensaje = $this->MensajeErrorDB($e->errorInfo, $gen_modelo);
                }       

            }        
        
            $ParticularController->accionesPosteriores($gen_modelo, $gen_accion, $gen_id);
        }

        

        
        if ($gen_url_siguiente <> '') {
            if ($gen_url_siguiente <> 'back' and !$mensaje['error']) {
                if (strpos($gen_url_siguiente, '/nro_de_id')) {
                    $gen_url_siguiente = str_replace('/nro_de_id', '', $gen_url_siguiente);
                    $gen_url_siguiente = $gen_url_siguiente.'/'.$gen_id;
                }
                return redirect($gen_url_siguiente)->withErrors([$mensaje['error'], $mensaje['detalle']]);    
            }
            else {                
                return redirect()->back()->withErrors([$mensaje['error'], $mensaje['detalle']]);                
            }
        }
        else {
            return View('genericas/list')
            ->with('gen_campos', $gen_campos)
            ->with('gen_modelo', $gen_modelo)
            ->with('gen_opcion', $gen_opcion)
            ->with('gen_permisos', $gen_permisos)
            ->with('mensaje', $mensaje);
        }
    }

    public function MensajeErrorDB($errorInfo, $modelo) {

        $codigo = $errorInfo[1];
        $detalle = $errorInfo[2];

        $nombre_de_modelo = $this->nombreDeTablaAMostrar($modelo);

        $mensaje['error'] = true;

        if ($codigo == 1451) {
            $mensaje['detalle'] = 'Error de integridad referencial, no puede eliminar este registro ya que esta en uso en otros lugares.'."<br><i>($detalle)</i>";
            $mensaje['class'] = 'alert-warning';
        }
        if ($codigo == 1062) {
            $mensaje['detalle'] = 'Error de valor duplicado, este registro de '.$nombre_de_modelo.' ya ha sido ingresado '."<br><i>($detalle)</i>";
            $mensaje['class'] = 'alert-warning';
        }
        if ($codigo == 999001) {
            $mensaje['detalle'] = $detalle;
            $mensaje['class'] = 'alert-warning';
        }
        if (!isset($mensaje['detalle'])) {
            $mensaje['detalle'] = $detalle;
            $mensaje['class'] = 'alert-danger';
        }

        return $mensaje;


    }


    // INICIO FUNCIONES PARA TRAER CAMPOS

    public function traer_campos($gen_modelo, $gen_campos_a_ocultar = array()){
        $gen_campos = [];

        if (!in_array('-created_at', $gen_campos_a_ocultar)) {
            array_push($gen_campos_a_ocultar, 'created_at'); 
        }

        if (!in_array('-updated_at', $gen_campos_a_ocultar)) {
            array_push($gen_campos_a_ocultar, 'updated_at'); 
        }

        // Defino el Nombre de la Tabla
        $tb = strtolower($gen_modelo).'s';
        if(!Schema::hasTable($tb)) {    
            $TablasEnPlurarl = new TablasEnPlurarl();
            $tb_plural_distintas = $TablasEnPlurarl->tablasEnPlural();    
            $tb = $tb_plural_distintas[$gen_modelo];   
        }

    
        

        //$tipo = $this->tipoDeCampo('holA'); 
    

        // Recorro los campos
        foreach (DB::select( "describe $tb")  as $field){           
            
            // nombre
            $nombre = $field->Field;

            // Excluyo los campos a ocultar    
                
            if (!in_array($nombre, $gen_campos_a_ocultar)) {


                $tipo = $this->tipoDeCampo($field->Type);
                $longitud = $this->longitudDeCampo($field->Type);
                $campo_fk = $this->CampoFK($tb, $nombre);
                if ($nombre == 'provincia_id') {
                    //dd($campo_fk);
                }
                $rel_tb = $campo_fk['rel_campo'];
                $rel_modelo = $campo_fk['rel_modelo'];
                $rel_campo_descripcion = $campo_fk['rel_campo_descripcion'];
                $nulo = $field->Null;
                $nombre_a_mostrar = $this->nombreAMostrar($nombre);

                // Relleno el Array
                array_push($gen_campos, [
                    //'todo' => $field, 
                    'nombre' => $nombre,
                    'nombre_a_mostrar' => $nombre_a_mostrar,
                    'tipo' => $tipo,
                    'longitud' => $longitud,
                    'rel_tb' => $rel_tb,
                    'rel_modelo' => $rel_modelo,
                    'rel_campo_descripcion' => $rel_campo_descripcion,
                    'gen_modelo' => $gen_modelo,
                    'nulo' => $nulo
                    ]);            
            }

            //dd($gen_campos);
        }

        return $gen_campos;
    }

    public function traerCamposSchemaVFG($gen_modelo, $gen_accion, $gen_fila, $gen_campos_a_ocultar = array(), $filtros_por_campo = array(), $filtros_rel = array()){

        
        $tb = strtolower($gen_modelo).'s';

        $gen_campos = [];
        $schema_vfg = array();
        // Defino el Nombre de la Tabla
        if(!Schema::hasTable($tb)) {
            $TablasEnPlurarl = new TablasEnPlurarl();
            $tb_plural_distintas = $TablasEnPlurarl->tablasEnPlural();    
            $tb = $tb_plural_distintas[$gen_modelo];      
        }

        // Recorro los campos
        foreach (DB::select( "describe $tb")  as $field){           
            
            // nombre
            $nombre = $field->Field;
            $valor_del_campo = NULL;

            //Si el campo es un filtro lo defino como hidden                
            if (array_key_exists($nombre, $filtros_por_campo)) {
                $hidden = 'SI';
                $valor_del_campo = $filtros_por_campo[$nombre];

            }
            else {
                $hidden = 'NO';
            }

            // Excluyo los campos a ocultar
            array_push($gen_campos_a_ocultar, 'created_at', 'updated_at', 'id');              
            if (!in_array($nombre, $gen_campos_a_ocultar)) {

                $tipo = $this->tipoDeCampo($field->Type);              
                $longitud = $this->longitudDeCampo($field->Type);
                $campo_fk = $this->CampoFK($tb, $nombre);       
                $nulo = $field->Null;
                $nombre_a_mostrar = $this->nombreAMostrar($nombre);


                // Defino el Schema de los campos para el Form-vue-generator                
                if (isset($gen_fila['original'])) {
                    $valor_del_campo = $gen_fila['original'][$nombre];
                }

                if (array_key_exists($nombre, $filtros_rel)) {
                    $filtros_campo_rel = $filtros_rel[$nombre]['campos'];
                    $filtros_valor_rel = $filtros_rel[$nombre]['valores'];
                }
                else {
                    $filtros_campo_rel = array();
                    $filtros_valor_rel = array();
                }

                $schema = $this->armarSchemaVFG($nombre, $nombre_a_mostrar, $tipo, $longitud, $campo_fk, $nulo, $gen_accion, $valor_del_campo, $hidden, $filtros_campo_rel, $filtros_valor_rel);

                if (($tipo == 'date' or $tipo == 'datetime' or $tipo == 'time') and $gen_accion == 'a' and $nulo == 'NO' and $valor_del_campo == '') {
                    $rellenar_campo_fecha_hoy = true;
                }
                else {
                    $rellenar_campo_fecha_hoy = false;
                }

                if (!is_null($valor_del_campo) or $rellenar_campo_fecha_hoy) {
                    if($tipo == 'date' or $tipo == 'datetime' or $tipo == 'time') {
                        if ($rellenar_campo_fecha_hoy) {
                            $valor_del_campo = 'moment()';   
                        }
                        else {
                            if ($tipo == 'time') {
                                $array_valor_del_campo = explode(':', $valor_del_campo);
                                $valor_del_campo = $array_valor_del_campo[0].':'.$array_valor_del_campo[1];
                                $valor_del_campo = 'moment("'.$valor_del_campo.'", "HH:mm")';
                            }
                            else {
                                $valor_del_campo = 'moment("'.$valor_del_campo.'").toDate()';    
                            }                              
                        }
                    }
                    else {
                        if($tipo == 'int' or $tipo == 'decimal') {
                            $valor_del_campo = "$valor_del_campo";
                        }
                        else {
                            $array_nombre = explode('_', $nombre);
                            $prefijo_campo = $array_nombre[0];


                            $tipos_para_textarea = [
                                'tinytext' => 255, 
                                'text' => 65535, 
                                'mediumtext' => 16777215, 
                                'longtext' => 4294967295, 
                                'tinyblob' => 255, 
                                'blob' => 65535, 
                                'mediumblob' => 16777215, 
                                'longblob' => 4294967295
                            ];

                            if ($prefijo_campo == 'urlencode') {
                                $valor_del_campo = urldecode($valor_del_campo);
                            }

                            if ($prefijo_campo == 'rtf' or ($tipo == 'varchar' and $longitud >= 300) or array_key_exists($tipo, $tipos_para_textarea)) {
                                $valor_del_campo = json_encode($valor_del_campo);
                            }
                            else {
                                $valor_del_campo = str_replace("'", '’', $valor_del_campo);
                                $valor_del_campo = "'$valor_del_campo'";    
                            }
                            
                        }
                    }
                }                
                else {
                    if ($gen_accion == 'a' and ($nombre == 'sino_activa' or $nombre == 'sino_activo')) {
                        $valor_del_campo = '"SI"';
                    }
                    else {
                        $valor_del_campo = 'null';
                    }
                }   

                // Relleno el Array
                array_push($schema_vfg, [
                    'nombre' => $nombre,
                    'valor_del_campo' => $valor_del_campo,
                    'schema' => $schema
                    ]);                   
            }

        }
        //dd(App::getLocale());
        //dd($schema_vfg);
        return $schema_vfg;
    }


    static function nombreAMostrar($nombre_del_campo){


        $prefijos_a_quitar = ['img_', 'file_', 'sino_', 'email_', 'url_', 'rtf_', 'urlencode_', 'colpick_', 'moneda_', 'googleaddress_', 'videoyt_'];
        $nombre_a_mostrar = $nombre_del_campo;
        foreach ($prefijos_a_quitar as $prefijo) {
            $nombre_a_mostrar = str_replace($prefijo, '', $nombre_a_mostrar);
        }

        $nombre_a_mostrar = ucfirst($nombre_a_mostrar);
        $nombre_campo_array = explode('_id', $nombre_a_mostrar);
        if (count($nombre_campo_array) > 0) {
            $nombre_a_mostrar = $nombre_campo_array[0];
        }
        else {
            $nombre_a_mostrar = $campo['gen_modelo'];                      
        }

        $nombre_a_mostrar = str_replace('_', ' ', $nombre_a_mostrar);

        if ($nombre_a_mostrar == 'User') {
            $nombre_a_mostrar = 'Usuario Registrante';
        }

        if ($nombre_a_mostrar == 'Name') {
            $nombre_a_mostrar = 'Nombre';
        }
        
        return __($nombre_a_mostrar);
    }
 

    static function longitudDeCampo($field_type){
        preg_match('/\((.+)\)/', $field_type, $longitud_array);
        if (count($longitud_array) > 0) {
            $longitud = $longitud_array[1];
        }
        else {
            $longitud = '';
        }   
        
        return $longitud;  
    }

    static function tipoDeCampo($field_type)
    {
        $tipo = (!str_contains($field_type, '('))? $field_type: substr($field_type, 0, strpos($field_type, '('));
        return $tipo;  
    }

    public function CampoFK($tb, $nombre){
        //SELECT * FROM KEY_COLUMN_USAGE WHERE TABLE_NAME = 'clientes'  
        $tb_rel_array = KEY_COLUMN_USAGE::where('TABLE_NAME', $tb)->where('CONSTRAINT_SCHEMA', env('DB_DATABASE'))->where('COLUMN_NAME', $nombre)->whereRaw('REFERENCED_TABLE_NAME IS NOT NULL')->get();

        if (count($tb_rel_array) > 0) {
            $rel_tb = $tb_rel_array[0]->REFERENCED_TABLE_NAME;
            $rel_campo = $tb_rel_array[0]->COLUMN_NAME;
            $gen_modelo_array = explode('_id', $rel_campo);
            $rel_modelo = ucfirst($gen_modelo_array[0]);
            $campos_de_rel_tb = $this->traer_campos($rel_modelo);

            $clase = $this->dirModel($rel_modelo);
            if (method_exists($clase, 'descrip_modelo')) {
                $rel_campo_descripcion = 'descrip_modelo()';
            }
            else {
                $rel_campo_descripcion = $campos_de_rel_tb[1]['nombre'];           
            }

        }
        else {
            $rel_tb = '';
            $rel_campo = '';
            $rel_modelo = '';
            $rel_campo_descripcion = '';
        } 

        return array(
            'rel_tb' => $rel_tb, 
            'rel_campo' => $rel_campo, 
            'rel_modelo' => $rel_modelo, 
            'rel_campo_descripcion' => $rel_campo_descripcion
        );  
    }    

   
    public function armarSchemaVFG($nombre, $nombre_a_mostrar, $tipo, $longitud, $campo_fk, $nulo, $gen_accion, $valor_del_campo, $hidden, $filtros_campo_rel = array(), $filtros_valor_rel = array(), $onChange = ''){

        $schema_vfg = '';
        if ($nulo == 'NO') {
            $required = 'true';
        }
        else {
            $required = 'false';
        }

        if ($gen_accion == 'b') {
            $disabled = 'true';
        }
        else {
            $disabled = 'false';
        }
        $rel_modelo = $campo_fk['rel_modelo'];  
        $rel_campo_descripcion  = $campo_fk['rel_campo_descripcion'];  
             
        $schema_vfg_extra = '';
        $valores = '';
        $array_nombre = explode('_', $nombre);
        $prefijo_campo = $array_nombre[0];

        

        if ($hidden == 'NO') {
            // CAMPO FK
            if ($rel_modelo <> '') {  
                $gen_filas = call_user_func(array($this->dirModel($rel_modelo), 'all'), '*');
                
                for ($i=0; $i < count($filtros_campo_rel); $i++) { 
                    $gen_filas = $gen_filas->whereIn($filtros_campo_rel[$i], $filtros_valor_rel[$i]);
                }
                
                //dd($filtros_campo_rel);
                //Habilito la Búsqueda en el campo Select
                if($gen_filas->count() > 10) {
                    $habilitar_busqueda_en_select = 'true';
                }
                else {
                    $habilitar_busqueda_en_select = 'false';
                }
                
                //recorro las filas para llenar el select
                foreach ($gen_filas as $fila) { 
                    $name = '';
                    if(is_array($rel_campo_descripcion)) {
                        foreach ($rel_campo_descripcion as $rel_campo) {
                            $name .= $fila[$rel_campo].' | ';
                        }
                    }
                    else {
                        if ($rel_campo_descripcion == 'descrip_modelo()') {
                            $name = $fila->descrip_modelo();
                        }
                        else {
                            $name = $fila[$rel_campo_descripcion];
                        }
                        
                    }
                    $valores .= '{ id: '.$fila['id'].', name: "'.__($name).'" }, ';

                }
                $schema_vfg .= '{';         
                $schema_vfg .= 'type: "selectEx",';      
                $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                $schema_vfg .= 'model: "'.$nombre.'",';
                $schema_vfg .= 'id: "'.$nombre.'",';
                $schema_vfg .= 'required: '.$required.',';
                $schema_vfg .= 'disabled: '.$disabled.',';   
                $schema_vfg .= 'inputName: "'.$nombre.'",';       
                $schema_vfg .= 'multi: "true",';                
                $schema_vfg .= 'multiSelect: false,';            
                $schema_vfg .= "selectOptions: { liveSearch: ".$habilitar_busqueda_en_select.", size: 'auto' },";               
                $schema_vfg .= 'values: function() { return [ '.$valores.' ] },';       
                if ($nulo == 'NO') {
                $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                }                
                if ($onChange <> '') {
                $schema_vfg .= 'onChanged(model, schema, event) {';
                $schema_vfg .= $onChange;
                $schema_vfg .= '},';
                }                        
                $schema_vfg .= '},';  

            }
            else {
                // NO CAMPO FK
                $nombre_campo_array = explode('_', $nombre);
                if (count($nombre_campo_array) > 0) {
                    if ($nombre_campo_array[0] == 'img') {
                        
                        $schema_vfg .= '{';
                        $schema_vfg .= 'type: "image",';
                        $schema_vfg .= 'label: "'. $nombre_a_mostrar.' (puede indicar un archivo o escribir la URL de la imagen)",';
                        $schema_vfg .= 'model: "'.$nombre.'",';    
                        $schema_vfg .= 'required: '.$required.',';
                        $schema_vfg .= 'disabled: '.$disabled.',';
                        $schema_vfg .= 'browse: true,'; 
                        $schema_vfg .= 'preview: true,'; 
                        if ($nulo == 'NO') {
                        $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                        }             
                        $schema_vfg .= 'validator: VueFormGenerator.validators.string,';
                        $schema_vfg .= '},';        

                        $schema_vfg .= '{';              
                        $schema_vfg .= 'type: "input",'; 
                        $schema_vfg .= 'inputType: "hidden",'; 
                        $schema_vfg .= 'model: "'.$nombre.'",';
                        $schema_vfg .= 'inputName: "'.$nombre.'",';   
                        if ($nulo == 'NO') {
                        $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                        }               
                        $schema_vfg .= '},'; 
                          
                    }

                    if ($nombre_campo_array[0] == 'file') {        

                        $schema_vfg .= '{';              
                        $schema_vfg .= 'type: "input",';
                        $schema_vfg .= 'inputType: "file",';
                        $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                        $schema_vfg .= 'inputName: "'.$nombre.'_nuevo",';   
                        if ($nulo == 'NO') {
                        $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                        }               
                        $schema_vfg .= '},'; 

                        $schema_vfg .= '{';              
                        $schema_vfg .= 'type: "input",'; 
                        $schema_vfg .= 'inputType: "hidden",'; 
                        $schema_vfg .= 'model: "'.$nombre.'",';
                        $schema_vfg .= 'id: "'.$nombre.'",';    
                        $schema_vfg .= 'inputName: "'.$nombre.'",';   
                        if ($nulo == 'NO') {
                        $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                        }               
                        $schema_vfg .= '},'; 
                          
                    }

                    if ($nombre_campo_array[0] == 'colpick') {
                        
                        $schema_vfg .= '{';
                        $schema_vfg .= 'type: "spectrum",';
                        $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
                        $schema_vfg .= 'model: "'.$nombre.'",';    
                        $schema_vfg .= 'id: "'.$nombre.'",';    
                        $schema_vfg .= 'inputName: "'.$nombre.'",';    
                        $schema_vfg .= 'required: '.$required.',';
                        $schema_vfg .= 'disabled: '.$disabled.',';
                        $schema_vfg .= '},';
                          
                    }

                    // CAMPO SINO
                    if ($nombre_campo_array[0] == 'sino') {

                        
                        $schema_vfg .= '{';
                        $schema_vfg .= 'type: "switch",';
                        $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                        $schema_vfg .= 'model: "'.$nombre.'",';
                        $schema_vfg .= 'textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO",';
                        if ($nulo == 'NO') {
                        $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                        }              
                        $schema_vfg .= '},';       
                        

                        $schema_vfg .= '{';              
                        $schema_vfg .= 'type: "input",';  
                        $schema_vfg .= 'inputType: "hidden",'; 
                        $schema_vfg .= 'model: "'.$nombre.'",';
                        $schema_vfg .= 'id: "'.$nombre.'",';    
                        $schema_vfg .= 'inputName: "'.$nombre.'",';   
                        if ($nulo == 'NO') {
                        $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                        }               
                        $schema_vfg .= '},'; 
                    }

                    // CAMPO googleAddress
                    if ($nombre_campo_array[0] == 'googleaddress') {

                        
                        $schema_vfg .= '{';
                        $schema_vfg .= 'type: "googleAddress",';
                        $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                        $schema_vfg .= 'model: "'.$nombre.'",';
                        $schema_vfg .= 'autocomplete: "section-blue shipping address-level2",';
                            $schema_vfg .= 'onPlaceChanged(value, place, rawPlace, model, schema) {';
                            $schema_vfg .= 'model.ciudad = place["locality"] == null ? place["administrative_area_level_2"] : place["locality"];';
                            $schema_vfg .= 'model.provincia_estado_o_region = place["administrative_area_level_1"];';
                            //$schema_vfg .= 'console.log(model.ciudad = place["administrative_area_level_2"]);';
                            $schema_vfg .= 'model.latitud = rawPlace["geometry"]["location"].lat().toString();';
                            $schema_vfg .= 'model.longitud = rawPlace["geometry"]["location"].lng().toString();;';
                            $schema_vfg .= 'model.pais = place["country"];';
                            $schema_vfg .= 'model.direccion = value;';
                            $schema_vfg .= 'model.url_enlace_a_google_maps = rawPlace["url"];';
                            //$schema_vfg .= 'model.pais_id = $("select[name='."'pais_id'".']").find('."'".'option[text="Argentina"]'."'".').val();';
                            //$schema_vfg .= 'model.pais_id = $("select[name='."'pais_id'".']").find("option:[text='."'".'Argentina'."'".']").val();';
                            //$schema_vfg .= 'console.log(model.pais_id);';
                            //$schema_vfg .= '$("select[name='."'pais_id'".']").val(model.pais_id);';
                            //$schema_vfg .= '$("select[name='."'pais_id'".']").selectpicker("render");';

                            //$schema_vfg .= 'console.log(model);';
                            //$schema_vfg .= 'console.log(rawPlace);';
                            //$schema_vfg .= 'console.log(place);';
                            //$schema_vfg .= 'console.log("lat: "+rawPlace["geometry"]["location"].lat());';
                            //$schema_vfg .= 'console.log("lng: "+rawPlace["geometry"]["location"].lng());';
                            //$schema_vfg .= 'console.log(rawPlace["geometry"]["viewport"]["ka"]);';
                            //$schema_vfg .= 'console.log(rawPlace["geometry"]["viewport"]["pa"]);';
                            //$schema_vfg .= 'console.log("url: "+rawPlace["url"]);';
                            //$schema_vfg .= 'console.log("calle: "+place["route"]);';
                            //$schema_vfg .= 'console.log("nro: "+place["street_number"]);';
                            //$schema_vfg .= 'console.log("localidad: "+place["locality"]);';
                            //$schema_vfg .= 'console.log("adm_1: "+place["administrative_area_level_1"]);';
                            //$schema_vfg .= 'console.log("adm_2: "+place["administrative_area_level_2"]);';
                            //$schema_vfg .= 'console.log("Pais: "+place["country"]);';
                            $schema_vfg .= '}';
                        $schema_vfg .= '},';      

                        $schema_vfg .= '{';              
                        $schema_vfg .= 'type: "input",';  
                        $schema_vfg .= 'inputType: "hidden",'; 
                        $schema_vfg .= 'model: "'.$nombre.'",';
                        $schema_vfg .= 'id: "'.$nombre.'",';    
                        $schema_vfg .= 'inputName: "'.$nombre.'",';   
                        if ($nulo == 'NO') {
                        $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                        }               
                        $schema_vfg .= '},';  
                        
                    }
                }

                
                if ($schema_vfg == '') {
                    //CAMPOS FECHA
                    if ($tipo == 'date' or $tipo == 'datetime' or $tipo == 'time') {
                        if ($tipo == 'date') {
                            $schema_vfg .= '{';        
                            $schema_vfg .= 'type: "dateTimePicker",';  
                            $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                            $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                            $schema_vfg .= 'model: "'.$nombre.'",';
                            $schema_vfg .= 'id: "'.$nombre.'",';    
                            $schema_vfg .= 'inputName: "'.$nombre.'",';    
                            $schema_vfg .= 'required: '.$required.',';
                            $schema_vfg .= 'disabled: '.$disabled.',';
                            if ($nulo == 'NO') {
                            $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                            }     
                            $schema_vfg .= 'validator: VueFormGenerator.validators.date,';
                            $schema_vfg .= 'dateTimePickerOptions: { format: "DD/MM/YYYY" },';
                            $schema_vfg .= 'onChanged: function(model, newVal, oldVal, field) {';
                            $schema_vfg .= 'model.age = moment().year() - moment(newVal).year();';
                            $schema_vfg .= '},';    
                            $schema_vfg .= '},';        
                            } 
                        if ($tipo == 'datetime') {
                            $schema_vfg .= '{';        
                            $schema_vfg .= 'type: "dateTimePicker",';  
                            $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                            $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                            $schema_vfg .= 'model: "'.$nombre.'",';
                            $schema_vfg .= 'id: "'.$nombre.'",';    
                            $schema_vfg .= 'inputName: "'.$nombre.'",';    
                            $schema_vfg .= 'required: '.$required.',';
                            $schema_vfg .= 'disabled: '.$disabled.',';
                            if ($nulo == 'NO') {
                            $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                            }     
                            $schema_vfg .= 'validator: VueFormGenerator.validators.date,';
                            $schema_vfg .= 'dateTimePickerOptions: { format: "DD/MM/YYYY HH:mm" },';
                            $schema_vfg .= 'onChanged: function(model, newVal, oldVal, field) {';
                            $schema_vfg .= 'model.age = moment().year() - moment(newVal).year();';
                            $schema_vfg .= '},';    
                            $schema_vfg .= '},';        
                            } 
                        if ($tipo == 'time') {
                            $schema_vfg .= '{';        
                            $schema_vfg .= 'type: "dateTimePicker",';  
                            $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                            $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                            $schema_vfg .= 'model: "'.$nombre.'",';
                            $schema_vfg .= 'id: "'.$nombre.'",';    
                            $schema_vfg .= 'inputName: "'.$nombre.'",';    
                            $schema_vfg .= 'required: '.$required.',';
                            $schema_vfg .= 'disabled: '.$disabled.',';
                            if ($nulo == 'NO') {
                            $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                            }     
                            $schema_vfg .= 'validator: VueFormGenerator.validators.date,';
                            $schema_vfg .= 'dateTimePickerOptions: { format: "HH:mm" },';
                            $schema_vfg .= 'onChanged: function(model, newVal, oldVal, field) {';
                            $schema_vfg .= 'model.age = moment().year() - moment(newVal).year();';
                            $schema_vfg .= '},';    
                            $schema_vfg .= '},';        
                            }                  
                    }
                    else {
                        //CAMPOS NUMERICOS
                        if ($tipo == 'int' or $tipo == 'decimal' or $tipo == 'float') {
                            $schema_vfg .= '{';
                            
                            $schema_vfg .= 'inputType: "number",';
                            $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                            $schema_vfg .= 'type: "input",';        
                            $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                            $schema_vfg .= 'model: "'.$nombre.'",';
                            $schema_vfg .= 'id: "'.$nombre.'",';  
                            $schema_vfg .= 'inputName: "'.$nombre.'",';    
                            $schema_vfg .= 'required: '.$required.',';
                            $schema_vfg .= 'disabled: '.$disabled.',';

                            if ($nulo == 'NO') {
                            $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                            }        

                            if ($tipo == 'int') {
                                $schema_vfg .= 'validator: VueFormGenerator.validators.integer,';
                                $schema_vfg .= 'step: 1,';
                                $schema_vfg .= 'min: 0,';
                            }
                            if ($tipo == 'decimal') {
                                $schema_vfg .= 'validator: VueFormGenerator.validators.decimal,';
                                $schema_vfg .= 'step: 0.01,';
                                $schema_vfg .= 'min: 0,';
                            }
                            if ($tipo == 'float') {
                                $schema_vfg .= 'validator: VueFormGenerator.validators.decimal,';
                                $schema_vfg .= 'step: 0.0000000000000001,';
                            }
                            
                            $schema_vfg .= '},'; 
                        }
                        else {

                            $tipos_para_textarea = [
                                'tinytext' => 255, 
                                'text' => 65535, 
                                'mediumtext' => 16777215, 
                                'longtext' => 4294967295, 
                                'tinyblob' => 255, 
                                'blob' => 65535, 
                                'mediumblob' => 16777215, 
                                'longblob' => 4294967295
                            ];

                            if (array_key_exists($tipo, $tipos_para_textarea)) {
                                $longitud = $tipos_para_textarea[$tipo];
                            }
                            
                            if ($prefijo_campo == 'rtf' or ($tipo == 'varchar' and $longitud >= 300) or array_key_exists($tipo, $tipos_para_textarea)) {
                                //CAMPOS TEXTO
                                $schema_vfg .= '{';           
                                $schema_vfg .= 'type: "textArea",';  
                                $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                                $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                                $schema_vfg .= 'model: "'.$nombre.'",';
                                $schema_vfg .= 'id: "'.$nombre.'",';  
                                $schema_vfg .= 'inputName: "'.$nombre.'",';    
                                $schema_vfg .= 'required: '.$required.',';
                                $schema_vfg .= 'rows: 5,';
                                $schema_vfg .= 'disabled: '.$disabled.',';
                                $schema_vfg .= 'max: '.$longitud.',';
                                if ($nulo == 'NO') {
                                $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                                }               
                                $schema_vfg .= 'validator: VueFormGenerator.validators.string,';
                                $schema_vfg .= '},'; 
                            }
                            else {
                                //CAMPOS TEXTO
                                $schema_vfg .= '{';             
                                $schema_vfg .= 'type: "input",';  
                                $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                                $schema_vfg .= 'label: "'.$nombre_a_mostrar.'",';
                                $schema_vfg .= 'model: "'.$nombre.'",';
                                $schema_vfg .= 'id: "'.$nombre.'",';  
                                $schema_vfg .= 'inputName: "'.$nombre.'",';    
                                $schema_vfg .= 'required: '.$required.',';
                                $schema_vfg .= 'max: '.$longitud.',';
                                $schema_vfg .= 'disabled: '.$disabled.',';
                                if ($nulo == 'NO') {
                                $schema_vfg .= 'validator: VueFormGenerator.validators.required,';
                                }               
                                if ($prefijo_campo == 'email') {
                                    $schema_vfg .= 'inputType: "email",';  
                                    $schema_vfg .= 'validator: VueFormGenerator.validators.email,';
                                }
                                else {
                                    $schema_vfg .= 'inputType: "text",';  
                                    $schema_vfg .= 'validator: VueFormGenerator.validators.string,';                                
                                }
                                $schema_vfg .= '},';   
                            }

                        }        
                    }
                }

            }
        }
        else {
            $schema_vfg .= '{';              
            $schema_vfg .= 'type: "input",';  
            $schema_vfg .= 'inputType: "hidden",'; 
            $schema_vfg .= 'model: "'.$nombre.'",';
            $schema_vfg .= 'inputName: "'.$nombre.'",';    
            $schema_vfg .= '},';             
        }
        return $schema_vfg;
    }

    // FIN FUNCIONES PARA TRAER CAMPOS

    public function crearLista()
    {
        DB::enableQueryLog();

        $gen_modelo = $_POST['gen_modelo'];
        $gen_opcion = $_POST['gen_opcion'];
        $acciones_extra = '';
        $filtro_user = '';

        $gen_seteo = unserialize(stripslashes($_POST['gen_seteo']));

        if (isset($gen_seteo['acciones_extra'])) {
            $acciones_extra = $gen_seteo['acciones_extra'];
        }
        else {
            $acciones_extra = '';
        }

        $gen_campos_a_ocultar = array('empresa_id');  
        $gen_campos_a_ocultar_mas = '';

        if ($gen_opcion > 0) {
            $Opcion = Opcion::where('id', $gen_opcion)->get();

            // Traigo los campos a Ocultar
            $gen_campos_a_ocultar_mas = $Opcion[0]->no_listar_campos;

            // Traigo las acciones extra
            if ($Opcion[0]->acciones_extra <> '') {
                $acciones_extra = explode('|', $Opcion[0]->acciones_extra);    
            }

            // Traigo los Permisos
            $permisos = $Opcion[0]->permisos;
            if ($permisos <> '') {
                $gen_permisos = array();
                for ($i=0; $i<strlen($permisos); $i++) {
                    array_push($gen_permisos, $permisos[$i]);                 
                }                
            }

            $opcion_seteo = explode('|', $Opcion[0]->seteo);

            foreach ($opcion_seteo as $seteo) {
                $seteo = explode('=', $seteo);
                if ($seteo[0] == 'filtro_user') {
                    $filtro_user = $seteo[1];
                }
            }

        }    
        else {
            if (isset($gen_seteo['gen_campos_a_ocultar'])) {
              $gen_campos_a_ocultar_mas = $gen_seteo['gen_campos_a_ocultar'];
            }           
        }    

        if ($gen_campos_a_ocultar_mas <> '') {
            $campos_a_ocultar_array = explode('|', $gen_campos_a_ocultar_mas);
            foreach ($campos_a_ocultar_array as $campos_a_ocultar) {
                array_push($gen_campos_a_ocultar, $campos_a_ocultar);  
            }        
        }

        $gen_campos = $this->traer_campos($gen_modelo, $gen_campos_a_ocultar);

        if (isset($gen_seteo['gen_permisos'])) {
          $gen_permisos = $gen_seteo['gen_permisos'];
        }
        if (!isset($gen_permisos)) {
            $gen_permisos = [
                'C',
                'R',
                'U',
                'D'
                ]; 
        }
        

        //date_default_timezone_set('America/New_York');
        $gen_filas = call_user_func(array($this->dirModel($gen_modelo), 'all'), '*');
        //$gen_filas = call_user_func(array($this->dirModel($gen_modelo), 'whereRaw'), 'solicitud_id = 5');
        //$gen_filas = call_user_func(array($this->dirModel($gen_modelo), 'select'), ['nombre', 'apellido']);
        //$gen_filas->query->Builder->select(['nombre', 'apellido']);
        //$gen_filas->query->Builder->where('solicitud_id', '=', 5);



        if (isset($gen_seteo['filtro_where'])) {
            $filtro_where = $gen_seteo['filtro_where'];
            if (!is_array($filtro_where[0])) {
                $gen_filas = $gen_filas->where($filtro_where[0], $filtro_where[1], $filtro_where[2]);
            }
            else {
                foreach ($filtro_where as $filtro) {
                    $gen_filas = $gen_filas->where($filtro[0], $filtro[1], $filtro[2]);
                    //$gen_filas = $gen_filas->whereRaw('(equipo_id in (1))');
                }             
                    //$gen_filas = $gen_filas->toSql();
            }
            
        }

        if ($filtro_user <> '') {

            $filtro_campo_tb_user = $filtro_user; 
            if ($filtro_user == 'user_id' or strpos($filtro_user, 'user_id')) {
                $filtro_campo_tb_user = 'id';
            }
            
            $gen_filas = $gen_filas->where($filtro_user, Auth::user()->$filtro_campo_tb_user);
        }


        //dd($gen_filas);
        //dd(DB::getQueryLog());
        
        $gen_nombre_tb_mostrar = $this->nombreDeTablaAMostrar($gen_modelo);
        $gen_seteo['gen_campos_a_ocultar'] = $gen_campos_a_ocultar;



        return View('genericas/func_list')
        ->with('gen_campos', $gen_campos)
        ->with('gen_modelo', $gen_modelo)
        ->with('gen_filas', $gen_filas)
        ->with('gen_seteo', $gen_seteo)
        ->with('gen_permisos', $gen_permisos)
        ->with('gen_opcion', $gen_opcion)
        ->with('gen_nombre_tb_mostrar', $gen_nombre_tb_mostrar)
        ->with('acciones_extra', $acciones_extra);       
    }



    public function crearABM()
    {
        $gen_modelo = $_POST['gen_modelo'];
        $gen_accion = $_POST['gen_accion'];
        $gen_id = $_POST['gen_id'];
        $gen_opcion = $_POST['gen_opcion'];
        $gen_seteo = unserialize(stripslashes($_POST['gen_seteo']));


        $gen_campos_a_ocultar = ['empresa_id'];  
        $gen_seteo['gen_campos_a_ocultar'] = $gen_campos_a_ocultar;

        $no_mostrar_campos_abm = ['empresa_id'];
        $no_mostrar_campos_abm_mas = '';

        if ($gen_opcion > 0) {
            $Opcion = Opcion::where('id', $gen_opcion)->get();
            // Traigo los campos a Ocultar
            $no_mostrar_campos_abm_mas = $Opcion[0]->no_mostrar_campos_abm;
        }            

        if (isset($gen_seteo['no_mostrar_campos_abm'])) {
            $no_mostrar_campos_abm_mas = $gen_seteo['no_mostrar_campos_abm'];
        }

        if ($no_mostrar_campos_abm_mas <> '') {
            $array_no_mostrar_campos_abm_mas = explode('|', $no_mostrar_campos_abm_mas);
            foreach ($array_no_mostrar_campos_abm_mas as $no_mostrar_campo) {
                array_push($no_mostrar_campos_abm, $no_mostrar_campo);  
            } 
        }

        $gen_seteo['no_mostrar_campos_abm'] = $no_mostrar_campos_abm;

        if (isset($gen_seteo['filtros_por_campo'])) {
          $filtros_por_campo = $gen_seteo['filtros_por_campo'];
        }
        else {
          $filtros_por_campo = array();  
        }


        if (isset($gen_seteo['filtros_rel'])) {
            $filtros_rel = $gen_seteo['filtros_rel'];
        }
        else {
          $filtros_rel = array();  
        }
        
        $gen_permisos = [
            'C',
            'R',
            'U',
            'D'
            ];

        $etiqueta_btn_gen_accion = '';
        if ($gen_accion == 'a') {
            $gen_fila = [];
            $etiqueta_btn_gen_accion = 'Insertar';
        }
        else {
            $gen_fila = call_user_func(array($this->dirModel($gen_modelo), 'find'), $gen_id);    
        }

        if ($gen_accion == 'm') {
            $etiqueta_btn_gen_accion = 'Modificar';
        }        
        
        if ($gen_accion == 'b') {
            $etiqueta_btn_gen_accion = 'Eliminar';
        }
        //$gen_campos = $this->traer_campos($gen_modelo, ['empresa_id']);
        $schema_vfg = $this->traerCamposSchemaVFG($gen_modelo, $gen_accion, $gen_fila, $no_mostrar_campos_abm, $filtros_por_campo, $filtros_rel);


        return View('genericas/func_abm')
        //->with('gen_campos', $gen_campos)
        ->with('gen_modelo', $gen_modelo)
        ->with('gen_fila', $gen_fila)
        ->with('gen_seteo', $gen_seteo)
        ->with('gen_accion', $gen_accion)
        ->with('gen_id', $gen_id)
        ->with('gen_permisos', $gen_permisos)
        ->with('gen_opcion', $gen_opcion)
        ->with('etiqueta_btn_gen_accion', $etiqueta_btn_gen_accion)
        ->with('schema_vfg', $schema_vfg);       
    }


    protected function dirModel($gen_modelo) {
        $dirmodel = 'App\gen_modelo';
        $dirmodel = str_replace("gen_modelo", $gen_modelo, $dirmodel);
        return $dirmodel;
    }


    public function nombreDeTablaAMostrar($gen_modelo) {
        if ($gen_modelo == 'User') {
            $nombre_tb_mostrar = 'Usuarios';
        }
        else {
            $tb = strtolower($gen_modelo).'s';
            if(!Schema::hasTable($tb)) {
                $TablasEnPlurarl = new TablasEnPlurarl();
                $tb_plural_distintas = $TablasEnPlurarl->tablasEnPlural();    
                $tb = $tb_plural_distintas[$gen_modelo];               
            }        
            // nombre a mostrar
            $nombre_tb_mostrar = ucfirst($tb);
            $nombre_tb_mostrar = str_replace('_', ' ', $nombre_tb_mostrar);
        }

        return $nombre_tb_mostrar;
    }

    public function mostrarValorCampo($campo, $valor, $tipo) {
        if ($tipo == 'timestamp') {
        //dd($valor);

        }
        $valor_a_mostrar = $valor;
        if ($tipo == 'int' or $tipo == 'decimal') {
            $valor_a_mostrar = $this->formatoNumero($valor, $tipo);
        }
        $nombre_campo_array = explode('_', $campo);
        if (count($nombre_campo_array) > 0) {
            if ($nombre_campo_array[0] == 'url') {
                $valor_a_mostrar = '<a href="'.$valor.'" target="_blank">'.$valor.'</a>';
            }
            if ($nombre_campo_array[0] == 'urlencode') {
                $valor_a_mostrar = urldecode($valor);
            }
            if ($nombre_campo_array[0] == 'img' and $valor <> '') {
                $data = explode(';', $valor);
                $tipo_array = explode('/', $data[0]);
                $data_app = explode(':', $tipo_array[0])[1];
                $tipo = $tipo_array[1];
                if ($data_app == 'image') {
                    $valor_a_mostrar = '<img src="'.$valor.'" style="width: 100px">';
                }
                else {
                    $valor_a_mostrar = 'formato desconocido';
                }
            }
            if ($nombre_campo_array[0] == 'file' and $valor <> '') {
                $array_extension = explode('.', $valor);
                $extension = strtolower($array_extension[count($array_extension)-1]);
                $class_fa_icon = 'fa-file-text-o';
                if ($extension == 'pdf') {
                    $class_fa_icon = 'fa-file-pdf-o';
                }
                if ($extension == 'doc' or $extension == 'docx') {
                    $class_fa_icon = 'fa-file-word-o';
                }

                if ($extension == 'jpg' or $extension == 'jpeg' or $extension == 'gif' or $extension == 'png') {
                    $valor_a_mostrar = '<a target="_blank" href="'.env('PATH_PUBLIC').'storage/'.$valor.'"><img src="'.env('PATH_PUBLIC').'storage/'.$valor.'" style="width: 100px"></a>';
                }
                else {
                    $valor_a_mostrar = '<a target="_blank" href="'.env('PATH_PUBLIC').'storage/'.$valor.'"><button type="button" class="btn btn-default btn-lg"><i class="fa '.$class_fa_icon.'"></i> '.$extension.'</button></a>';
                }
            }
            if ($nombre_campo_array[0] == 'rtf') {
                $valor_a_mostrar = '(texto enriquecido)';
            }
            if ($nombre_campo_array[0] == 'moneda') {
                $decimal = 2;
                $simbolo = '$';
                $valor_a_mostrar = $this->FormatoMoneda($valor, $decimal, $simbolo);
            }
            if ($nombre_campo_array[0] == 'videoyt') {
                if ($valor <> '') {
                    $valor_a_mostrar = '<iframe width="200" height="113" src="https://www.youtube.com/embed/'.$valor.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                }
            }
        }

        if ($tipo == 'varchar') {
            //$valor_a_mostrar = substr($valor, 0, 500);
        }

        if ($tipo == 'date' and $valor <> '') {
            $valor_a_mostrar = $this->FormatoFecha($valor);
        }
        if (($tipo == 'datetime' or $tipo == 'timestamp') and $valor <> '') {
            /*
            $valor = Carbon::now();
            $noonTodayLondonTime = Carbon::createFromTime(12, 0, 0, 'Europe/London');
            dd($noonTodayLondonTime);
            */
            //$valor->timezone('America/New_York');
            $valor_a_mostrar = $this->FormatoFechayYHora($valor);
            //$valor_a_mostrar = $valor->format('d/m/Y H:i:s');
        }
        /*
        if ($tipo == 'timestamp' and $valor <> '') {
            dd($valor);
            $timestamp = new DateTime();
            $timestamp->setTimestamp($valor);
            $valor = new DateTime($timestamp, new DateTimeZone('America/New_York'));
            $valor_a_mostrar = $valor->format('d/m/Y H:i:s');
        }
        */
        if ($tipo == 'time' and $valor <> '') {
            $array_valor_del_campo = explode(':', $valor);
            $valor_a_mostrar = $array_valor_del_campo[0].':'.$array_valor_del_campo[1];
            //$valor_a_mostrar = $valor;
            //dd($valor_a_mostrar);
        }

        return $valor_a_mostrar;
    }

    public function getUser()
    {
        $User = new User();

        $Usuario = User::find($User[0]['id']);
        //dd(Auth::user()->rol_de_usuario_id);
        return $User;
    }

/*
    // CKEDITOR 4
    public function generarScriptTextareaParaRTF($gen_modelo){
        $tb = strtolower($gen_modelo).'s';

        // Defino el Nombre de la Tabla
        if(!Schema::hasTable($tb)) { 
            $TablasEnPlurarl = new TablasEnPlurarl();
            $tb_plural_distintas = $TablasEnPlurarl->tablasEnPlural();    
            $tb = $tb_plural_distintas[$gen_modelo];              
        }

        foreach (DB::select( "describe $tb")  as $field){    
            $nombre = $field->Field;
            $array_nombre = explode('_', $nombre);
            if ($array_nombre[0] == 'rtf') {
                echo '<script>';
                echo '$(function () {';
                echo "CKEDITOR.replace('".$nombre."');";
                echo "$('.textarea').wysihtml5();";
                echo "})";
                echo "</script>";
            }
        }
    }
*/

    //CKEDITOR 5
    public function generarScriptTextareaParaRTF($gen_modelo){
        $tb = strtolower($gen_modelo).'s';

        // Defino el Nombre de la Tabla
        if(!Schema::hasTable($tb)) { 
            $TablasEnPlurarl = new TablasEnPlurarl();
            $tb_plural_distintas = $TablasEnPlurarl->tablasEnPlural();    
            $tb = $tb_plural_distintas[$gen_modelo];              
        }

        echo '<script>';
        //echo "import ImageInsert from '@ckeditor/ckeditor5-image/src/imageinsert';";
        echo "</script>";

        foreach (DB::select( "describe $tb")  as $field){    
            $nombre = $field->Field;
            $array_nombre = explode('_', $nombre);
            if ($array_nombre[0] == 'rtf') {
                echo '<script>';
                echo 'ClassicEditor';
                echo ".create( document.querySelector( '#".$nombre."' ), {";
                //echo "removePlugins: [ 'Heading' ],";
                //echo "removePlugins: [ 'Heading' ],";
                echo "toolbar: [ 'Heading', 'bold', 'italic',  'link', 'bulletedList', 'numberedList', 'blockQuote' ]";
                echo "}) ";
                echo '.catch( error => {';
                echo '  console.error( error );';
                echo '} );';
                echo "</script>";
            }
        }
    }

    public function formatoNumero($numero, $tipo)
    {
        if ($numero <> '') {
            if ($tipo == 'decimal') {
                $numero_formateado = number_format($numero, 2, ',', '.');
            }
            else {
                $numero_formateado = number_format($numero, 0, ',', '.');
            }
        }
        else {
            $numero_formateado = '';
        }
        return $numero_formateado;
    }

    public function FormatoFechayYHora($valor) {
        $fechayhora = '';
        if ($valor <> '') {
            $array_valor = explode(' ', $valor);
            $fecha = $this->FormatoFecha($array_valor[0]);
            $hora_completa = $array_valor[1];

            $array_hora = explode(':', $hora_completa);
            if (count($array_hora) == 3) {
                $hora = $array_hora[0].':'.$array_hora[1].':'.$array_hora[2];
            }
            else {
                $hora = $array_hora[0].':'.$array_hora[1];    
            }

            $fechayhora = "$fecha $hora";
        }
        return $fechayhora;

    }

    public function FormatoFecha($valor) {
        $fecha = '';
        
        if (!is_string($valor)) {
            if(!is_null($valor)) {
                $valor = $this->FormatoFecha($valor->format('Y-m-d H:i:s'));                   
                }
            else {
                $valor = '';
                }
            }       
        
        if ($valor <> '') {
            $fecha_array_inicial = explode(" ",$valor);
            if (count($fecha_array_inicial) > 1) {
                $valor = $fecha_array_inicial[0];
                }
            if (strstr($valor, '/') <> '') {
                $fecha_array = explode("/",$valor);
                $fecha = $fecha_array[2].'-'.$fecha_array[1].'-'.$fecha_array[0];
                }
            else {
                $fecha_array = explode("-",$valor);
                $fecha = $fecha_array[2].'/'.$fecha_array[1].'/'.$fecha_array[0];
                if (count($fecha_array_inicial) > 1) {
                    $fecha = $fecha.' '.$fecha_array_inicial[1];
                    }
                }
            }
        return $fecha;
    }


    public function FormatoHora($hora_completa) {
        $array_hora = explode(':', $hora_completa);
        $hora = $array_hora[0].':'.$array_hora[1].'hs';

        return $hora;

    }


    public function scriptsJSParticulares($gen_modelo) {
        return View('particulares/js')
        ->with('gen_modelo', $gen_modelo);        
    }



    public function enviarTelegram($chat_id, $mensaje)
    {

        $ch = curl_init("https://api.telegram.org/bot".ENV('TELEGRAM_BOT_TOKEN')."/sendMessage?chat_id=".$chat_id."&text=".$mensaje);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Configura cURL para devolver el resultado como cadena
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Configura cURL para que no verifique el peer del certificado dado que nuestra URL utiliza el protocolo HTTPS
        $info = curl_exec($ch); // Establece una sesión cURL y asigna la información a la variable $info
        curl_close($ch); // Cierra sesión cURL

        return redirect()->back();
    }


    public function limpiarCadena($cadena) {
        $caracteres_no_admitidos = array("'", '"');
        $cadena_limpia = str_replace($caracteres_no_admitidos, "", $cadena);


        if ($cadena_limpia == '') { 
            $cadena_limpia = NULL;
        } 
        return $cadena_limpia;
        }



    public function celular_wa($numero, $codigo_tel)
    {
        
        $celular_wa = trim($numero);


        
        $celular_wa = str_replace('+', '', $celular_wa);
        $celular_wa = str_replace(' ', '', $celular_wa);
        $celular_wa = str_replace('-', '', $celular_wa);
        $celular_wa = str_replace('(', '', $celular_wa);
        $celular_wa = str_replace(')', '', $celular_wa);
        $celular_wa = str_replace(',', '', $celular_wa);
        $celular_wa = str_replace('.', '', $celular_wa);
        
        if (substr($celular_wa, 0, 1) == '0') {
            $celular_wa = substr($celular_wa, 1);
        }

        if (substr($celular_wa, 0, 1) <> '+') {
            if (substr($celular_wa, 0, strlen($codigo_tel)) <> $codigo_tel) {
                $celular_wa = $codigo_tel.$celular_wa;
            }
        }

        return $celular_wa;
    }


    public function btn_enviar_wa($numero, $codigo_tel = '549', $texto = null, $etiqueta = 'Enviar Whatsapp', $class_btn = 'btn btn-sm btn-success', $class_icon = 'fa fa-whatsapp', $style_btn = '')
    {
        $html_btn = '';

        if ($numero <> '') {
            $numero = $this->celular_wa($numero, $codigo_tel);
            $texto_wa = '';
            if ($texto <> null) {
                $entities = array('%20');
                $replacements = array('+');
                $texto_wa = str_replace($replacements, $entities, urlencode($texto));                
            }
            
            $html_btn  = '<a href="https://api.whatsapp.com/send?phone='.$numero.'&text='.$texto_wa.'" target="_blank">';
            $html_btn .= '<button type="button" class="'.$class_btn.'" style="'.$style_btn.'"><i class="'.$class_icon.'"></i> '.$etiqueta.'</button>';
            $html_btn .= '</a>';
        }
        
        return $html_btn;
    }

    public function btn_enviar_sms($numero, $codigo_tel = '549', $texto = null, $etiqueta = 'Enviar SMS', $class_btn = 'btn btn-sm btn-primary', $class_icon = 'fa fa-whatsapp', $style_btn = '')
    {
        $html_btn = '';

        if ($numero <> '') {
            $numero = $this->celular_wa($numero, $codigo_tel);
            $texto_sms = '';
            if ($texto <> null) {
                $entities = array('%20');
                $replacements = array('+');
                $texto_sms = str_replace($replacements, $entities, urlencode($texto));                
            }
        

            $html_btn  = '<a href="sms:'.$numero.'?body='.$texto_sms.'" target="_blank">';
            $html_btn .= '<button type="button" class="'.$class_btn.'" style="'.$style_btn.'"><i class="'.$class_icon.'"></i> '.$etiqueta.'</button>';
            $html_btn .= '</a>';
        }
        
        return $html_btn;
    }

    public function btn_llamar($numero, $codigo_tel = '549', $texto = null, $etiqueta = 'Llamar al Tel', $class_btn = 'btn btn-sm btn-danger', $class_icon = 'fa fa-whatsapp', $style_btn = '')
    {
        $html_btn = '';

        if ($numero <> '') {
            $numero = $this->celular_wa($numero, $codigo_tel);
            $texto_sms = '';
            if ($texto <> null) {
                $entities = array('%20');
                $replacements = array('+');
                $texto_sms = str_replace($replacements, $entities, urlencode($texto));                
            }

            $html_btn  = '<a href="tel:'.$numero.'" target="_blank">';
            $html_btn .= '<button type="button" class="'.$class_btn.'" style="'.$style_btn.'"><i class="'.$class_icon.'"></i> '.$etiqueta.'</button>';
            $html_btn .= '</a>';
        }
        
        return $html_btn;
    }

    public function FormatoMoneda($numero, $decimal = 2, $simbolo = '$')
    {
        $nro_moneda = '$ '.number_format($numero, $decimal, ',', '.');
        return $nro_moneda;
    }


}


