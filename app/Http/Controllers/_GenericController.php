<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Opcion;
use App\KEY_COLUMN_USAGE;
use App\User;
use Auth;


use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Storage;


//accionesPosteriores
use App\Modelo;
use App\Composicion_de_modelo;
use App\Cliente;



class GenericController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private static $tb_plural_distintas = [
        "Conferencia_publica" => "conferencias_publicas", 
        "Fecha_de_evento" => "fechas_de_evento",
        "Tipo_de_evento" => "tipos_de_eventos", 
        "Pais" => "paises", 
        "Localidad" => "localidades",
        "Opcion" => "opciones",
        "Rol_de_usuario" => "roles_de_usuario",
        "Solicitud" => "solicitudes",
        "Inscripcion" => "inscripciones",
        "Idioma_por_pais" => "idiomas_por_pais",
        "Modelo_de_mensaje" => "modelos_de_mensajes",
        "Formato_de_hora" => "formatos_de_hora",
        "Asistencia" => "asistencias"
    ];


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
        $gen_campos_a_ocultar = ['id'];
        $gen_campos = $this->traer_campos($gen_modelo, $gen_campos_a_ocultar);
        $gen_permisos = [
            'C',
            'R',
            'U',
            'D'
            ];


        $this->accionesAnteriores($gen_modelo, $gen_accion, $gen_id);

        $gen_seteo = unserialize(stripslashes($_POST['gen_seteo']));
        $no_mostrar_campos_abm = [];

        if (isset($gen_seteo['no_mostrar_campos_abm'])) {
            $no_mostrar_campos_abm_mas = $gen_seteo['no_mostrar_campos_abm'];
            $array_no_mostrar_campos_abm_mas = explode('|', $no_mostrar_campos_abm_mas);
            foreach ($array_no_mostrar_campos_abm_mas as $no_mostrar_campo) {
                array_push($no_mostrar_campos_abm, $no_mostrar_campo);  
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

                if ($array_nombre[0] == 'urlencode' or $array_nombre[0] == 'rtf') {
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
                    $valor = $this->FormatoFecha($valor);
                }
                if ($campo['tipo'] == 'datetime') {
                    $valor = $this->FormatoFechayYHora($valor);
                }

                $valores[$nombre] = $valor;

            }            

            try {
                $resultado = call_user_func(array($this->dirModel($gen_modelo), 'create'), $valores);     
                $gen_id = $resultado->id;
                $mensaje['detalle'] = 'Inserci&oacute;n exitosa';

            } catch (\Illuminate\Database\QueryException $e) {
                $mensaje = $this->MensajeErrorDB($e->errorInfo);
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

                    if ($array_nombre[0] == 'urlencode' or $array_nombre[0] == 'rtf') {
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
                        $valor = $this->FormatoFecha($valor);
                    }

                    if ($campo['tipo'] == 'datetime') {
                        $valor = $this->FormatoFechayYHora($valor);
                    }

                    $registro->$nombre = $valor;
                }

            }     
              
            try {
                $registro->save(); 
                $mensaje['detalle'] = 'Modificaci&oacute;n exitosa';

            } catch (\Illuminate\Database\QueryException $e) {
                $mensaje = $this->MensajeErrorDB($e->errorInfo);
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
                $mensaje = 'Eliminaci&oacute;n exitosa';

            } catch (\Illuminate\Database\QueryException $e) {
                $mensaje = $this->MensajeErrorDB($e->errorInfo);
            }       

        }        

        $this->accionesPosteriores($gen_modelo, $gen_accion, $gen_id);

        if ($gen_url_siguiente <> '') {
            if ($gen_url_siguiente == 'back') {
                return redirect()->back()->withErrors([$mensaje]);
            }
            else {
                return redirect($gen_url_siguiente)->withErrors([$mensaje]);    
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

    public function MensajeErrorDB($errorInfo) {

        $codigo = $errorInfo[0];
        $detalle = $errorInfo[2];

        $mensaje['error'] = 'S';

        if ($codigo == 23000) {
            $mensaje['detalle'] = 'Error de integridad referencial, no puede eliminar este registro ya que esta en uso en otros lugares';
            $mensaje['class'] = 'alert-warning';
        }
        else {
            $mensaje['detalle'] = $detalle;
            $mensaje['class'] = 'alert-danger';
        }

        return $mensaje;


    }


    // INICIO FUNCIONES PARA TRAER CAMPOS

    public function traer_campos($gen_modelo, $gen_campos_a_ocultar = array()){
        $gen_campos = [];
        array_push($gen_campos_a_ocultar, 'created_at', 'updated_at'); 

        // Defino el Nombre de la Tabla
        $tb = strtolower($gen_modelo).'s';
        if(!Schema::hasTable($tb)) {
            $tb = static::$tb_plural_distintas[$gen_modelo];           
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

    public function traerCamposSchemaVFG($gen_modelo, $gen_accion, $gen_fila, $gen_campos_a_ocultar = array(), $filtros_por_campo = array()){
        $tb = strtolower($gen_modelo).'s';

        $gen_campos = [];
        $schema_vfg = array();
        // Defino el Nombre de la Tabla
        if(!Schema::hasTable($tb)) {
            $tb = static::$tb_plural_distintas[$gen_modelo];           
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
                if (count($gen_fila) > 0) {
                    $valor_del_campo = $gen_fila['original'][$nombre];
                }
                $schema = $this->armarSchemaVFG($nombre, $nombre_a_mostrar, $tipo, $longitud, $campo_fk, $nulo, $gen_accion, $valor_del_campo, $hidden);

                if (!is_null($valor_del_campo)) {
                    if($tipo == 'date' or $tipo == 'datetime' or $tipo == 'time') {
                        if ($tipo == 'time') {
                            $array_valor_del_campo = explode(':', $valor_del_campo);
                            $valor_del_campo = $array_valor_del_campo[0].':'.$array_valor_del_campo[1];
                            $valor_del_campo = 'moment("'.$valor_del_campo.'", "HH:mm")';
                        }
                        else {
                            $valor_del_campo = 'moment("'.$valor_del_campo.'").toDate()';    
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

                            if ($prefijo_campo == 'rtf' or ($tipo == 'varchar' and $longitud > 200) or array_key_exists($tipo, $tipos_para_textarea)) {
                                $valor_del_campo = json_encode($valor_del_campo);
                            }
                            else {
                                $valor_del_campo = "'$valor_del_campo'";    
                            }
                            
                        }
                    }
                }                
                else {
                    $valor_del_campo = 'null';
                }   

                // Relleno el Array
                array_push($schema_vfg, [
                    'nombre' => $nombre,
                    'valor_del_campo' => $valor_del_campo,
                    'schema' => $schema
                    ]);                   
            }

        }
        //dd($schema_vfg);
        return $schema_vfg;
    }


    static function nombreAMostrar($nombre_del_campo){


        $prefijos_a_quitar = ['img_', 'file_', 'sino_', 'email_', 'url_', 'rtf_', 'urlencode_'];
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
        
        return $nombre_a_mostrar;  
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
        $tb_rel_array = KEY_COLUMN_USAGE::where('TABLE_NAME', $tb)->where('CONSTRAINT_SCHEMA', env('DB_DATABASE'))->where('COLUMN_NAME', $nombre)->whereRaw('REFERENCED_TABLE_NAME IS NOT NULL')->first();
        if (count($tb_rel_array) > 0) {
            $rel_tb = $tb_rel_array->REFERENCED_TABLE_NAME;
            $rel_campo = $tb_rel_array->COLUMN_NAME;
            $gen_modelo_array = explode('_id', $rel_campo);
            $rel_modelo = ucfirst($gen_modelo_array[0]);
            $campos_de_rel_tb = $this->traer_campos($rel_modelo);

            $dirmodel = 'App\gen_modelo';
            $dirmodel = str_replace("gen_modelo", $rel_modelo, $dirmodel);
            $clase = new $dirmodel();
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

   
    public function armarSchemaVFG($nombre, $nombre_a_mostrar, $tipo, $longitud, $campo_fk, $nulo, $gen_accion, $valor_del_campo, $hidden, $filtros_campo = array(), $filtros_valor = array(), $onChange = ''){
        
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
                //Habilito la Búsqueda en el campo Select
                if(count($gen_filas) > 10) {
                    $habilitar_busqueda_en_select = 'true';
                }
                else {
                    $habilitar_busqueda_en_select = 'false';
                }
                
                foreach ($gen_filas as $fila) { 
                    $name = '';
                    $agregar = 'NO';
                    $i = 0;
                    if (count($filtros_campo) > 0) {
                        $agregar = 'SI';
                        foreach ($filtros_campo as $filtro_campo) {
                            if ($fila[$filtro_campo] <> $filtros_valor[$i]) {
                                $agregar = 'NO';
                            }
                            $i++;
                        }                        
                    }
                    else {
                        $agregar = 'SI';
                    }
                    if ($agregar == 'SI') {
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
                        $valores .= '{ id: '.$fila['id'].', name: "'.$name.'" }, ';
                    }
                }
                $schema_vfg .= '{';         
                $schema_vfg .= 'type: "selectEx",';      
                $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
                $schema_vfg .= 'model: "'.$nombre.'",';
                $schema_vfg .= 'id: "'.$nombre.'",';
                $schema_vfg .= 'required: '.$required.',';
                $schema_vfg .= 'disabled: '.$disabled.',';   
                $schema_vfg .= 'inputName: "'.$nombre.'",';       
                $schema_vfg .= 'multi: "true",';               
                $schema_vfg .= 'multiSelect: false,';            
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
                        $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
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
                        $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
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

                    // CAMPO SINO
                    if ($nombre_campo_array[0] == 'sino') {

                        
                        $schema_vfg .= '{';
                        $schema_vfg .= 'type: "switch",';
                        $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
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
                }

                
                if ($schema_vfg == '') {
                    //CAMPOS FECHA
                    if ($tipo == 'date' or $tipo == 'datetime' or $tipo == 'time') {
                        if ($tipo == 'date') {
                            $schema_vfg .= '{';        
                            $schema_vfg .= 'type: "dateTimePicker",';  
                            $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                            $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
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
                            $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
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
                            $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
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
                        if ($tipo == 'int' or $tipo == 'decimal') {
                            $schema_vfg .= '{';
                            
                            $schema_vfg .= 'inputType: "number",';
                            $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                            $schema_vfg .= 'type: "input",';        
                            $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
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
                            
                            if ($prefijo_campo == 'rtf' or ($tipo == 'varchar' and $longitud > 200) or array_key_exists($tipo, $tipos_para_textarea)) {
                                //CAMPOS TEXTO
                                $schema_vfg .= '{';           
                                $schema_vfg .= 'type: "textArea",';  
                                $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                                $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
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
                                $schema_vfg .= 'inputType: "text",';               
                                $schema_vfg .= 'type: "input",';  
                                $schema_vfg .= 'placeholder: "'. $nombre_a_mostrar.'",';
                                $schema_vfg .= 'label: "'. $nombre_a_mostrar.'",';
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
                                    $schema_vfg .= 'validator: VueFormGenerator.validators.email,';
                                }
                                else {
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
        else {
            $gen_permisos = [
                'C',
                'R',
                'U',
                'D'
                ]; 
        }


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
                    //$gen_filas = $gen_filas->whereRaw('(sucursal_id IS NULL OR rol_de_usuario_id IS NULL AND (1 = ?))', 1);
                }             
                    //$gen_filas = $gen_filas->toSql();
            }
            
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

        if (isset($gen_seteo['filtros_por_campo'])) {
          $filtros_por_campo = $gen_seteo['filtros_por_campo'];
        }
        else {
          $filtros_por_campo = array();  
        }

        $gen_permisos = [
            'C',
            'R',
            'U',
            'D'
            ];

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
        $schema_vfg = $this->traerCamposSchemaVFG($gen_modelo, $gen_accion, $gen_fila, $no_mostrar_campos_abm, $filtros_por_campo);

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
                $tb = static::$tb_plural_distintas[$gen_modelo];           
            }        
            // nombre a mostrar
            $nombre_tb_mostrar = ucfirst($tb);
            $nombre_tb_mostrar = str_replace('_', ' ', $nombre_tb_mostrar);
        }

        return $nombre_tb_mostrar;
    }

    public function mostrarValorCampo($campo, $valor, $tipo) {


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
        }

        if ($tipo == 'varchar') {
            //$valor_a_mostrar = substr($valor, 0, 500);
        }

        if ($tipo == 'date' and $valor <> '') {
            $valor_a_mostrar = $this->FormatoFecha($valor);
        }
        if ($tipo == 'datetime' and $valor <> '') {
            $valor_a_mostrar = $this->FormatoFechayYHora($valor);
        }
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


    public function generarScriptTextareaParaRTF($gen_modelo){
        $tb = strtolower($gen_modelo).'s';

        // Defino el Nombre de la Tabla
        if(!Schema::hasTable($tb)) {
            $tb = static::$tb_plural_distintas[$gen_modelo];           
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


    public function formatoNumero($numero, $tipo)
    {
        if ($tipo == 'decimal') {
            $numero_formateado = number_format($numero, 2, ',', '.');
        }
        else {
            $numero_formateado = number_format($numero, 0, ',', '.');
        }
        
        return $numero_formateado;
    }

    public function FormatoFechayYHora($valor) {
        $array_valor = explode(' ', $valor);
        $fecha = $this->FormatoFecha($array_valor[0]);
        $hora_completa = $array_valor[1];

        $array_hora = explode(':', $hora_completa);
        $hora = $array_hora[0].':'.$array_hora[1];

        $fechayhora = "$fecha $hora";
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

    public function accionesAnteriores($modelo, $accion, $id) {
        $id = intval($id);
        if($modelo == 'Composicion_de_modelo') {
            if ($accion == 'b') {
                $Composicion_de_modelo = Composicion_de_modelo::find($id);
                $modelo_id = $Composicion_de_modelo->modelo_id;
                $ComponentesDeModelo = Composicion_de_modelo::
                    where('modelo_id', $modelo_id)
                    ->get();
                $total_de_metros_cuadrados = 0;
                foreach ($ComponentesDeModelo as $Componente) {
                    if ($Componente->id <> $id) {
                        $total_de_metros_cuadrados = $total_de_metros_cuadrados +($Componente->ancho*$Componente->largo);
                    }
                }

                $Modelo = Modelo::find($modelo_id);
                $Modelo->total_de_metros_cuadrados = $total_de_metros_cuadrados;
                $Modelo->save();
            }
        }
    }

    public function accionesPosteriores($modelo, $accion, $id) {
        $id = intval($id);

        // INICIO Composicion_de_modelo
        if($modelo == 'Composicion_de_modelo') {
            if ($accion == 'a' or $accion == 'm') {
                $Composicion_de_modelo = Composicion_de_modelo::find($id);
                $modelo_id = $Composicion_de_modelo->modelo_id;
                $ComponentesDeModelo = Composicion_de_modelo::
                    where('modelo_id', $modelo_id)
                    ->get();
                $total_de_metros_cuadrados = 0;
                foreach ($ComponentesDeModelo as $Componente) {
                    $total_de_metros_cuadrados = $total_de_metros_cuadrados +($Componente->ancho*$Componente->largo);
                }

                $Modelo = Modelo::find($modelo_id);
                $Modelo->total_de_metros_cuadrados = $total_de_metros_cuadrados;
                $Modelo->save();
            }
        }
        // FIN Composicion_de_modelo

        // INICIO Cliente
        if($modelo == 'Cliente') {
            if ($accion == 'a') {
                $User = $this->getUser();
                $Cliente = Cliente::find($id);
                $Cliente->sucursal_id = Auth::user()->sucursal_id;
                $Cliente->user_id = Auth::user()->id;
                $Cliente->save();
            }
        }
        // FIN Composicion_de_modelo




    }




}


