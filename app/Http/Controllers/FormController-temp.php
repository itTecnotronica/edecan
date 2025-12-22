<?php

    public function RegistrarInscripcion(Request $request) {
    

        $solicitud_id = $this->limpiarCadena($_POST['solicitud_id']);

        $Solicitud = Solicitud::find($solicitud_id);
            
        $cel_requerido = 'required|';
        $mail_requerido = '';
        $canal_de_recepcion_del_curso_id_requerido = '';
        $pais_id_requerido = '';
        $ciudad_requerido = '';
        $localidad_id_requerido = '';

        if ($Solicitud->idioma_por_pais() <> null) {
            $idioma_por_pais = $Solicitud->idioma_por_pais(); 
            if ($idioma_por_pais->sino_cel_obligatorio == 'NO') {
                $cel_requerido = '';
            }
            if ($idioma_por_pais->sino_mail_obligatorio == 'NO') {
                $mail_requerido = '';
            }
            else {
                $mail_requerido = 'required|';
            }

        }

        if ($Solicitud->tipo_de_evento_id == 3) { 
            $pais_id_requerido = 'required|';
            $ciudad_requerido = 'required|';
        }

        if ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id <> '' and in_array($Solicitud->id, array(3747, 4033, 4034, 4035, 4036, 4037)) ) {
            $localidad_id_requerido = 'required|';
        }


        $this->validate($request, [
            'nombre' => 'required|max:45',
            'apellido' => 'max:45',
            'celular' => $cel_requerido.'max:45',
            'email_correo' => $mail_requerido.'max:80',
            'canal_de_recepcion_del_curso_id' => $canal_de_recepcion_del_curso_id_requerido,
            'pais_id' => $pais_id_requerido,
            'ciudad' => $ciudad_requerido.'max:50',
            'localidad_id' => $localidad_id_requerido,
            'consulta' => 'max:300'
        ]);


        $campania_id = $this->limpiarCadena($_POST['campania_id'] ?? null);
        $app_usuario_id = $this->limpiarCadena($_POST['app_usuario_id']);
        $embebed = $this->limpiarCadena($_POST['embebed']);
        $nombre = $this->limpiarCadena($_POST['nombre']);
        $apellido = $this->limpiarCadena($_POST['apellido']);
        $celular = $this->limpiarCadena($_POST['celular']);
        $fecha_de_evento_id = $this->limpiarCadena($_POST['fecha_de_evento_id']);

        $celular_completo = $this->limpiarCadena($celular_completo);
        $celular = $celular_completo <> '' ? $celular_completo : $celular;
        $canal_de_recepcion_del_curso_id = $this->limpiarCadena($canal_de_recepcion_del_curso_id);
        $email_correo = $this->limpiarCadena($email_correo);
        $consulta = $this->limpiarCadena($consulta);
        $pais_id = $this->limpiarCadena($_POST['pais_id']);
        $ciudad = $this->limpiarCadena($_POST['ciudad']);
        $localidad_id = $this->limpiarCadena($_POST['localidad_id']);
        

        if (isset($_POST['sino_notificar_proximos_eventos'])) {
            $notificar_proximos_eventos = 'SI';
        }
        else {
            $notificar_proximos_eventos = 'NO';
        }

        if (isset($_POST['acepto_politica_de_privacidad'])) {
            $acepto_politica_de_privacidad = 'SI';
        }
        else {
            $acepto_politica_de_privacidad = 'NO';
        }


        if ($Solicitud->tipo_de_evento_id <> 3) {
            // Cargo por cada Conferencia del ciclo de conferencias una inscripcion
            $Fechas_de_evento = Fecha_de_evento::where('solicitud_id', $solicitud_id)->get();
            foreach ($Fechas_de_evento as $Fecha_de_evento) {
                if (isset($_POST['fecha_de_evento_id_'.$Fecha_de_evento->id])) {
                    $fecha_de_evento_ids[] = $_POST['fecha_de_evento_id_'.$Fecha_de_evento->id];
                }
            }
        }


        $data = $this->RegistrarInscripcionProceso($solicitud_id, $campania_id, $app_usuario_id, $embebed, $nombre, $apellido, $celular, $celular_completo, $canal_de_recepcion_del_curso_id, $email_correo, $consulta, $fecha_de_evento_id, $fecha_de_evento_ids, $pais_id, $ciudad, $localidad_id, $notificar_proximos_eventos, $acepto_politica_de_privacidad);

        return View($data['blade_de_formulario'])          
        ->with('Solicitud', $data['Solicitud'])
        ->with('inscripcion_id', $data['inscripcion_id'])
        ->with('titulo', $data['titulo'])
        ->with('mensaje_box', $data['mensaje_box'])
        ->with('url_invitacion_grupo_whatsapp', $data['url_invitacion_grupo_whatsapp'])
        ->with('url_invitacion_grupo_facebook', $data['url_invitacion_grupo_facebook'])
        ->with('url_redireccionar_automaticamente_al_enlace', $data['url_redireccionar_automaticamente_al_enlace'])
        ->with('url_fanpage', $data['url_fanpage'])
        ->with('url_youtube', $data['url_youtube'])
        ->with('mnemo_face', $data['mnemo_face'])
        ->with('url_form_inscripcion', $data['url_form_inscripcion'])
        ->with('nombre_de_la_institucion', $data['nombre_de_la_institucion'])
        ->with('dominio_publico', $data['dominio_publico']);


    }


    public function RegistrarInscripcionProceso($solicitud_id, $campania_id, $app_usuario_id, $embebed, $nombre, $apellido, $celular, $celular_completo, $canal_de_recepcion_del_curso_id, $email_correo, $consulta, $fecha_de_evento_id, $fecha_de_evento_ids, $pais_id, $ciudad, $localidad_id, $notificar_proximos_eventos, $acepto_politica_de_privacidad) {


        $error_inscripcion = false;
        $inscripcion_id = null;
        $Solicitud = Solicitud::find($solicitud_id);
            
        $mensaje_box_fecha_de_evento = '';

        $GrupoAsignado = $this->asignarGrupo($Solicitud);
        $nro_de_grupo = $GrupoAsignado['nro_de_grupo'];
        $url_redireccionar_automaticamente_al_enlace = null;
        
        if ($Solicitud->idioma_por_pais() <> null) {
            $idioma_por_pais = $Solicitud->idioma_por_pais(); 
            $institucion_id = $idioma_por_pais->institucion_id;
        }
        else {
            $institucion_id = 1;
        }


        $url_invitacion_grupo_facebook = '';

        $idioma = $Solicitud->idioma->mnemo;
        App::setLocale($idioma);    

        
        if ($Solicitud->tipo_de_evento_id == 4) {
            $consulta = '';
        }


        $pais = '';
        $ciudad = '';

        $se_registro_alguna_inscripcion = 'N';
        $inscripcion_ya_registrada = 'N';
        $themeofinterest = '';
        $eventid = '';
        $date_of_interest = '';
        $tags_mautic = ['id'.$solicitud_id];

        // cuando es un curso on-line o Recolección de Datos
        if (($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id <> 4) or $Solicitud->tipo_de_evento_id == 4) {

            if ($Solicitud->tipo_de_evento_id == 3 or ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id == '') or ($Solicitud->tipo_de_evento_id == 4 and  !in_array($Solicitud->id, array(3747, 4033, 4034, 4035, 4036, 4037)) ) ) {
            }

            if ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id <> '' and in_array($Solicitud->id, array(3747, 4033, 4034, 4035, 4036, 4037))) {
                $pais_id = $Solicitud->pais_id;
                $Localidad = Localidad::find($localidad_id);
                $ciudad = $Localidad->localidad; 
            }

            $themeofinterest .= $Solicitud->Tipo_de_evento->tipo_de_evento;
            array_push($tags_mautic, $themeofinterest);    

            if ($Solicitud->tipo_de_evento_id == 6192)
                $whereLimiteDiasAnt = '(DATEDIFF(NOW(), created_at) BETWEEN -9999 and 9999)';
            else {
                $whereLimiteDiasAnt = '(DATEDIFF(NOW(), created_at) BETWEEN -7 and 7)';
            }

            if ($fecha_de_evento_id <> '') {
                $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                    ->where('nombre', $nombre)
                    ->where('celular', $celular)
                    ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                    ->where('fecha_de_evento_id', $fecha_de_evento_id)
                    ->get();
            }
            else  {
                $Inscripcion_previa = Inscripcion::where('solicitud_id', $solicitud_id)
                    //->where('nombre', $nombre)
                    //->where('apellido', $apellido)
                    //->where('celular', $celular)
                    //->where('pais_id', $pais_id)
                    //->where('ciudad', $ciudad)
                    ->whereRaw("((email_correo = '$email_correo' AND email_correo IS NOT NULL AND email_correo <> '') or (celular = '$celular'))")
                    ->whereRaw($whereLimiteDiasAnt)
                    ->get();
            }
                        
            if ($Inscripcion_previa->count() == 0) {

                $se_registro_alguna_inscripcion = 'S';

                $Inscripcion = new Inscripcion;
                $Inscripcion->solicitud_id = $solicitud_id;
                $Inscripcion->apellido = $apellido;
                $Inscripcion->nombre = $nombre;
                if ($celular <> '') {
                    $Inscripcion->celular = $celular;
                }
                if ($email_correo <> '') {
                    $Inscripcion->email_correo = $email_correo;
                }
                $Inscripcion->consulta = $consulta;
                $Inscripcion->pais_id = $pais_id;
                $Inscripcion->ciudad = $ciudad;
                $Inscripcion->campania_id = $campania_id;
                $Inscripcion->app_usuario_id = $app_usuario_id;
                $Inscripcion->canal_de_recepcion_del_curso_id = $canal_de_recepcion_del_curso_id;
                $Inscripcion->codigo_alumno = $Inscripcion->codigo_del_alumno();
                $Inscripcion->grupo = $nro_de_grupo;

                if ($fecha_de_evento_id <> '') {
                    $Inscripcion->fecha_de_evento_id = $fecha_de_evento_id;
                }

                $Inscripcion->sino_notificar_proximos_eventos = $notificar_proximos_eventos;
                $Inscripcion->sino_acepto_politica_de_privacidad = $acepto_politica_de_privacidad;

                try { 
                    $Inscripcion->save(); 
                    $inscripcion_id = $Inscripcion->id;
                } catch(\Illuminate\Database\QueryException $ex){ 
                    $detalle_de_origen = 'Registracion de Inscripcion (curso on-line): '.URL::previous();
                    $Registro_de_error = new Registro_de_error;
                    $Registro_de_error->registro_de_error = $ex->getMessage();
                    $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                    $Registro_de_error->save();              
                    $error_inscripcion = true;
                }

            }            
            else {                        
                $inscripcion_ya_registrada = 'S';
                $inscripcion_id = $Inscripcion_previa[0]->id;
            }


        }



        // cuando es un curso o una sola conferencia
        if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1) or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4) ) {

            $inscripcion_id = NULL;


            if ($Solicitud->Tipo_de_evento->id == 2) {
                $Fecha_de_evento = Fecha_de_evento::where('solicitud_id', $Solicitud->id)->get();
                $themeofinterest .= $Fecha_de_evento[0]->titulo_de_conferencia_publica;
                array_push($tags_mautic, $themeofinterest);
            }
            else {
                $themeofinterest .= $Solicitud->Tipo_de_evento->tipo_de_evento;
                array_push($tags_mautic, $themeofinterest);
            }
            

            if ($fecha_de_evento_id <> 'NP' and $fecha_de_evento_id <> '') {
                $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                    ->where('nombre', $nombre)
                    ->where('celular', $celular)
                    ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                    ->where('fecha_de_evento_id', $fecha_de_evento_id)
                    ->get();   

                
                $cant_Fecha_de_evento = Fecha_de_evento::where('id', $fecha_de_evento_id)->count();
                if ($cant_Fecha_de_evento > 0){
                    $Fecha_de_evento = Fecha_de_evento::find($fecha_de_evento_id);
                    $date_of_interest = $Fecha_de_evento->fecha_de_inicio; 
                }
            }
            else  {
                $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                    ->where('nombre', $nombre)
                    ->where('celular', $celular)
                    ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                    ->where('solicitud_id', $solicitud_id)
                    ->whereNull('fecha_de_evento_id')
                    ->get();

                array_push($tags_mautic, 'No Puede asistir');
            }
            
            if ($Inscripcion_previa->count() == 0) {

                $se_registro_alguna_inscripcion = 'S';

                $Inscripcion = new Inscripcion;
                $Inscripcion->solicitud_id = $solicitud_id;
                $Inscripcion->apellido = $apellido;
                $Inscripcion->nombre = $nombre;
                if ($celular <> '') {
                    $Inscripcion->celular = $celular;
                }
                if ($email_correo <> '') {
                    $Inscripcion->email_correo = $email_correo;
                }
                $Inscripcion->consulta = $consulta;

                if ($fecha_de_evento_id <> 'NP' and $fecha_de_evento_id <> 'MO') {
                    $Inscripcion->fecha_de_evento_id = $fecha_de_evento_id;
                }

                $Inscripcion->sino_notificar_proximos_eventos = $notificar_proximos_eventos;
                $Inscripcion->sino_acepto_politica_de_privacidad = $acepto_politica_de_privacidad;
                $Inscripcion->campania_id = $campania_id;
                $Inscripcion->app_usuario_id = $app_usuario_id;
                $Inscripcion->canal_de_recepcion_del_curso_id = $canal_de_recepcion_del_curso_id;                
                $Inscripcion->codigo_alumno = $Inscripcion->codigo_del_alumno();
                $Inscripcion->grupo = $nro_de_grupo;

                if ($Solicitud->tipo_de_evento_id == 3) {
                    $Inscripcion->pais_id = $pais_id;
                    $Inscripcion->ciudad = $ciudad;
                }


                try { 
                    $Inscripcion->save(); 
                    $inscripcion_id = $Inscripcion->id;


                    if ($fecha_de_evento_id == 'MO') {
                        if ($Solicitud->derivar_inscriptos_modalidad_online_a_solicitud > 0) {
                            $Inscripcion->solicitud_id = $Solicitud->derivar_inscriptos_modalidad_online_a_solicitud;

                            $causa_de_cambio_de_solicitud_id = 5;

                            $Cambio = new Cambio_de_solicitud_de_inscripcion;
                            $Cambio->inscripcion_id = $Inscripcion->id;
                            $Cambio->causa_de_cambio_de_solicitud_id = $causa_de_cambio_de_solicitud_id;            
                            $Cambio->solicitud_origen = $solicitud_id;
                            $Cambio->solicitud_destino = $Solicitud->derivar_inscriptos_modalidad_online_a_solicitud;
                            $Cambio->save(); 

                            if ($Inscripcion->solicitud_original == '') {
                                $Inscripcion->solicitud_original = $solicitud_id;
                                $Inscripcion->causa_de_cambio_de_solicitud_id = $causa_de_cambio_de_solicitud_id;
                            }
                            $Inscripcion->solicitud_id = $Solicitud->derivar_inscriptos_modalidad_online_a_solicitud;
                            $Inscripcion->fecha_de_evento_id = NULL;
                            
                        }
                        $Inscripcion->sino_eleccion_modalidad_online = 'SI';
                        $Inscripcion->save();
                    }

                } catch(\Illuminate\Database\QueryException $ex){ 
                    $detalle_de_origen = 'Registracion de Inscripcion (cuando es un curso o una sola conferencia): '.URL::previous();
                    $Registro_de_error = new Registro_de_error;
                    $Registro_de_error->registro_de_error = $ex->getMessage();
                    $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                    $Registro_de_error->save();              
                    $error_inscripcion = true;
                }
                
                
                }
            else {                        
                $inscripcion_ya_registrada = 'S';
                $inscripcion_id = $Inscripcion_previa[0]->id;
            }
        }
        else {
            if ($Solicitud->tipo_de_evento_id <> 3) {
                // Cargo por cada Conferencia del ciclo de conferencias una inscripcion
                $Fechas_de_evento = Fecha_de_evento::where('solicitud_id', $solicitud_id)->get();
                foreach ($fecha_de_evento_ids as $fecha_de_evento_id) {

                    $date_of_interest = $Fecha_de_evento->fecha_de_inicio; 
                    array_push($tags_mautic, $Fecha_de_evento->titulo_de_conferencia_publica);

                    $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                        ->where('nombre', $nombre)
                        ->where('celular', $celular)
                        ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                        ->where('fecha_de_evento_id', $fecha_de_evento_id)
                        ->get();

                    if ($Inscripcion_previa->count() == 0) {

                        $se_registro_alguna_inscripcion = 'S';

                        $Inscripcion = new Inscripcion;
                        $Inscripcion->solicitud_id = $solicitud_id;
                        $Inscripcion->apellido = $apellido;
                        $Inscripcion->nombre = $nombre;
                        if ($celular <> '') {
                            $Inscripcion->celular = $celular;
                        }
                        if ($email_correo <> '') {
                            $Inscripcion->email_correo = $email_correo;
                        }
                        $Inscripcion->consulta = $consulta;      

                        $Inscripcion->fecha_de_evento_id = $fecha_de_evento_id;

                        $Inscripcion->sino_notificar_proximos_eventos = $notificar_proximos_eventos;
                        $Inscripcion->sino_acepto_politica_de_privacidad = $acepto_politica_de_privacidad;
                        $Inscripcion->campania_id = $campania_id;
                        $Inscripcion->app_usuario_id = $app_usuario_id;
                        $Inscripcion->canal_de_recepcion_del_curso_id = $canal_de_recepcion_del_curso_id;
                        $Inscripcion->codigo_alumno = $Inscripcion->codigo_del_alumno();
                        $Inscripcion->grupo = $nro_de_grupo;

                        try { 
                            $Inscripcion->save(); 
                            $inscripcion_id = $Inscripcion->id;
                            $detalle_fecha = $Inscripcion->fecha_de_evento->armarDetalleFechasDeEventos('html', true, null, $Solicitud, null);
                            $mensaje_box_fecha_de_evento .= '<br><br><span style="color: #a19b91">'.$detalle_fecha.'</span>';
                        } catch(\Illuminate\Database\QueryException $ex){ 
                            $detalle_de_origen = 'Registracion de Inscripcion (Cargo por cada Conferencia del ciclo de conferencias una inscripcion): '.URL::previous();
                            $Registro_de_error = new Registro_de_error;
                            $Registro_de_error->registro_de_error = $ex->getMessage();
                            $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                            $Registro_de_error->save();              
                            $error_inscripcion = true;
                        }                        
                    }
                    else {                        
                        $inscripcion_ya_registrada = 'S';
                        $inscripcion_id = $Inscripcion_previa[0]->id;
                    }
                }
                // si no se registro ninguna inscripcion
                if ($se_registro_alguna_inscripcion == 'N' and $inscripcion_ya_registrada == 'N') {

                    $Inscripcion_previa = Inscripcion::where('apellido', $apellido)
                        ->where('nombre', $nombre)
                        ->where('solicitud_id', $solicitud_id)
                        ->where('celular', $celular)
                        ->whereRaw("((email_correo = '$email_correo') OR (email_correo IS NULL AND '$email_correo' = ''))")
                        ->whereNull('fecha_de_evento_id')
                        ->get();


                    if ($Inscripcion_previa->count() == 0) {
                        
                        $se_registro_alguna_inscripcion = 'S';

                        $Inscripcion = new Inscripcion;
                        $Inscripcion->solicitud_id = $solicitud_id;
                        $Inscripcion->apellido = $apellido;
                        $Inscripcion->nombre = $nombre;
                        if ($celular <> '') {
                            $Inscripcion->celular = $celular;
                        }
                        if ($email_correo <> '') {
                            $Inscripcion->email_correo = $email_correo;
                        }
                        $Inscripcion->email_correo = $email_correo;
                        $Inscripcion->consulta = $consulta;      

                        $Inscripcion->sino_notificar_proximos_eventos = $notificar_proximos_eventos;
                        $Inscripcion->sino_acepto_politica_de_privacidad = $acepto_politica_de_privacidad;
                        $Inscripcion->campania_id = $campania_id;
                        $Inscripcion->app_usuario_id = $app_usuario_id;
                        $Inscripcion->canal_de_recepcion_del_curso_id = $canal_de_recepcion_del_curso_id;
                        $Inscripcion->codigo_alumno = $Inscripcion->codigo_del_alumno();
                        $Inscripcion->grupo = $nro_de_grupo;

                        try { 
                            $Inscripcion->save(); 
                            $inscripcion_id = $Inscripcion->id;
                        } catch(\Illuminate\Database\QueryException $ex){ 
                            $detalle_de_origen = 'Registracion de Inscripcion (si no se registro ninguna inscripcion): '.URL::previous();
                            $Registro_de_error = new Registro_de_error;
                            $Registro_de_error->registro_de_error = $ex->getMessage();
                            $Registro_de_error->detalle_de_origen = $detalle_de_origen;
                            $Registro_de_error->save();              
                            $error_inscripcion = true;
                        }
                    }
                    else {                        
                        $inscripcion_ya_registrada = 'S';
                        $inscripcion_id = $Inscripcion_previa[0]->id;
                    }

                }
            }

        }

        

        //INICIO MAUTIC
        //QUITAR COMENTARIOS CUANDO EL SERVIDOR DE MAUTIC ESTE NUEVAMENTE RESTAURADO
            if (ENV('APP_ENV') <> 'development') {
                if (($email_correo <> '' and $solicitud_id <> 7805 and $solicitud_id <> 8443) or $institucion_id <> 1) {
                    $settings = array(
                        'userName'   => 'fmadoz',             // Create a new user       
                        'password'   => 'fM@d0Z'              // Make it a secure password
                    );

                    // Initiate the auth object specifying to use BasicAuth
                    $initAuth = new ApiAuth();
                    $auth = $initAuth->newAuth($settings, 'BasicAuth');

                    $api = new MauticApi();


                    $contactApi = $api->newApi('contacts', $auth, 'https://forms.gnosis.is');


                    $searchFilter = 'email:'.$email_correo;
                    $contacts = $contactApi->getList($searchFilter);

                    $Pais = '';
                    if ($Solicitud->tipo_de_evento_id == 3 or $Solicitud->tipo_de_evento_id == 4) {
                        if ($pais_id <> '') {
                            $Pais = Pais::find($pais_id);
                            $pais = $Pais->pais;
                        }
                        else {
                            if ($Solicitud->pais_id <> '') {
                                $pais = $Solicitud->pais->pais;
                            }  
                            else {                  
                                $pais = '';
                            }
                        }
                    }
                    else {
                        $pais = $Solicitud->localidad->provincia->pais->pais;
                    }            
                    $countrystateregionlocal = $pais.' / ';
                    array_push($tags_mautic, $pais);

                    if (strpos($Solicitud->tags_mautic, '#') >= 0) {
                        $tags_de_solicitud = explode('#', trim($Solicitud->tags_mautic));
                        foreach ($tags_de_solicitud as $tag) {
                            if ($tag <> '') {
                                array_push($tags_mautic, $tag); 
                            }
                        }              
                    }

                    $provincia = '';
                    if ($Solicitud->tipo_de_evento_id <> 3) {
                        if ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id <> '' and  in_array($Solicitud->id, array(3747, 4033, 4034, 4035, 4036, 4037)) ) {
                            $provincia = $Localidad->provincia->provincia;
                        }
                        else {
                            if ($Solicitud->localidad_id <> '') {
                                $provincia = $Solicitud->localidad->provincia->provincia;
                            }
                        }
                        $countrystateregionlocal .= $provincia.' / ';
                        array_push($tags_mautic, $provincia);
                    }       

                    if ($Solicitud->tipo_de_evento_id == 3 or $Solicitud->tipo_de_evento_id == 4) {
                        $localidad = $ciudad;
                    }
                    else {
                        $localidad = $Solicitud->localidad->localidad;
                    }                
                    $countrystateregionlocal .= $localidad;
                    array_push($tags_mautic, $localidad);
                    array_push($tags_mautic, 'id'.$Solicitud->id);

                    $last_active = date("Y-m-d H:i:s");


                    if (!isset($contacts['errors'])) {
                        if ($contacts['total'] == "0") {

                            //dd($contacts['total']);
                            //$id = 759;
                            //$response = $contactApi->get($id);
                            //$contact = $response[$contactApi->itemName()];
                            //$response = $contactApi->getList('', 0, 1);
                            $systemsource = 'gnosis-incripcion-sistemaAC';

                            if ($fecha_de_evento_id == 'MO') {
                                $fecha_de_evento_id = null;
                            }

                            $data = array(
                                "email" => $email_correo,
                                "firstname" => $nombre,
                                "lastname" => $apellido,
                                "mobile" => $celular,
                                "themeofinterest" => $themeofinterest,
                                //"description" => $themeofinterest,
                                "countrystateregionlocal" => $countrystateregionlocal,
                                "pais" => $pais,
                                "provincia" => $provincia,
                                "ciudad" => $localidad,
                                "campaign_id" => $Solicitud->id,
                                "eventid" => $fecha_de_evento_id,
                                "systemsource" => $systemsource,
                                "date_of_interest" => $date_of_interest,
                                "last_active" => $last_active,
                                "notificar_proximos_evento" => $notificar_proximos_eventos,                    
                                "tags" => $tags_mautic,
                            );


                            $asset = $contactApi->create($data);

                            if (isset($Inscripcion)) {
                                $Inscripcion->mautic_contact_id = $asset['contact']['id'];
                                $Inscripcion->save(); 
                            }
                            
                        }
                        else {
                            $contactId = key($contacts['contacts']);

                            $data = array(
                                'tags' => $tags_mautic,
                                'last_active' => $last_active,
                                "notificar_proximos_evento" => $notificar_proximos_eventos,  
                                "info_log_actualizacion" => 'FormController Actualizacion de Contacto'.'inscripcion_id: '.$inscripcion_id.' - '.$email_correo.' - '.rand(0,1000),


                            );

                            $createIfNotFound = false;

                            $contact = $contactApi->edit($contactId, $data, $createIfNotFound);
                            //dd($contactId);


                            if (isset($Inscripcion)) {
                                $Inscripcion->mautic_contact_id = $contactId;
                                $Inscripcion->save(); 
                            }

                        }
                    }

                }            
            }
        //FIN MAUTIC    
        
        if ($Solicitud->id == 12958) {
            $codigo = 1;
            $asunto = __('Pedido de confirmación');
            $this->enviarNotificacionInscripcion($inscripcion_id, $codigo, $asunto);
        }
        $Inscripcion = Inscripcion::find($inscripcion_id);
        $sesion_id = Session()->get('sesion_id');

        try { 
            $now = new \DateTime();
            $fecha_now = $now->format('Y-m-d H:i:s');
            $Visualizacion_de_formulario = Visualizacion_de_formulario::where('sesion_id', $sesion_id)->orderBy('id', 'desc')->first();
            if ($Visualizacion_de_formulario <> null) {
                $Visualizacion_de_formulario->inscripcion_id = $inscripcion_id;
                $Visualizacion_de_formulario->fecha_y_hora_inscripcion = $fecha_now;
                $Visualizacion_de_formulario->save(); 
            }
        } catch(\Illuminate\Database\QueryException $ex){ 
            $detalle_de_origen = 'Registracion de Inscripcion Sesion_id: '.$sesion_id.' url previa: '.URL::previous();
            $Registro_de_error = new Registro_de_error;
            $Registro_de_error->registro_de_error = $ex->getMessage();
            $Registro_de_error->detalle_de_origen = $detalle_de_origen;
            $Registro_de_error->save();       
        }

        if ($error_inscripcion) {
            $mensaje_box = '<h4>'.mb_strtoupper($nombre, 'UTF-8').'</h4>'.__('hay algun error con su inscripción, intentelo nuevamente, y si persiste comuníquese con nuestro responsable de inscripción para inscribirse telefónicamente').': <br><h3>'.$Solicitud->nombre_responsable_de_inscripciones.' '.__('Celular').': '.$Solicitud->celular_responsable_de_inscripciones.'</h3>';   
        }
        else {
            if ($se_registro_alguna_inscripcion == 'S') {
                 
                 $mensaje_box = '<h4> <i class="icon fa fa-check"> </i> '.__('Felicitaciones').' '.mb_strtoupper($nombre, 'UTF-8').'</h4>'.__('Inscripción registrada');
                
                
                if ($solicitud_id == 9467) {
                    $generarPdfVMAron = $this->generarPdfVMAron($inscripcion_id);
                    $nombre_archivo_pdf = $generarPdfVMAron['nombre_archivo_pdf'];
                    $password_pdf = $generarPdfVMAron['password_pdf'];
                    $mensaje_box = '<h4> <i class="icon fa fa-check"> </i> '.__('Felicitaciones').' '.mb_strtoupper($nombre, 'UTF-8').'</h4>';
                    $mensaje_box .= '<p>Este libro esta protegido por un password personal que debes usar para abrirlo. Anotalo en algun lugar</p><p> <br>Password: <strong>'.$password_pdf.'</strong></p>';
                    $mensaje_box .= 'Descarga tu libro mediante este enlace: <br><br><a href ="'.ENV('PATH_PUBLIC').'storage/books/'.$nombre_archivo_pdf.'" target="_blank">'.ENV('PATH_PUBLIC').'storage/books/'.$nombre_archivo_pdf.'</a> <br><br>';
                }
                



            }
            else {
                $mensaje_box = '<h4> <i class="icon fa fa-check"> </i> '.mb_strtoupper($nombre, 'UTF-8').'</h4>'.__('su inscripción ya ha sido registrada');

                $mensaje_box .= '<br><br>';

                $tipo = 'html';
                $con_inicio = true;
                $Idioma_por_pais = null;
                $idioma = null;
                $ver_mapa = true;
                $con_dir_inicio_distinto = true;

                /*
                Retomar quitando estos comentarios
                $infoInscripcion = $Inscripcion->InfoInscripcion($tipo, $con_inicio, $Idioma_por_pais, $Inscripcion->solicitud, $idioma, $ver_mapa, $con_dir_inicio_distinto);

                
                foreach ($infoInscripcion as $info) {
                    if ($info[0] <> '') {
                        $mensaje_box .= '<b>'.$info[0].'</b>: ';    
                    }
                    $mensaje_box .= $info[1].'<br>';
                }

                $MauticController = new MauticController();
                $mensaje_box .= '<br><i><b>'.$MauticController->enviarMailMautic($Inscripcion).'</b></i>';
                */
                
                
                if ($solicitud_id == 5033) {
                    $mensaje_box .= '<br><strong>Para terminar tenes que seguír nuestra cuenta de Instagram <a href="https://www.instagram.com/gnosisenvivo/" target="_blank">@gnosisenvivo</a></strong>';  
                     
                }


                if ($solicitud_id == 9467) {

                    $password_pdf = $inscripcion_id+255;

                    $nombre_archivo_pdf = 'GNOSIS-ESCUELA-DE-REGENERACION-HUMANA-V-M-ARON-321'.$inscripcion_id.'.pdf';

                    $mensaje_box .= '<p>Este libro esta protegido por un password personal que debes usar para abrirlo. Anotalo en algun lugar</p><p> <br>Password: <strong>'.$password_pdf.'</strong></p>';
                    $mensaje_box .= 'Descarga tu libro mediante este enlace: <br><br><a href ="'.ENV('PATH_PUBLIC').'storage/books/'.$nombre_archivo_pdf.'" target="_blank">'.ENV('PATH_PUBLIC').'storage/books/'.$nombre_archivo_pdf.'</a> <br><br>';
                }   


                



            }

            if ($mensaje_box_fecha_de_evento <> '') {
                $mensaje_box .= $mensaje_box_fecha_de_evento;
            }

            if ($Solicitud->url_redireccionar_automaticamente_al_enlace <> '' and $Solicitud->mensaje_para_el_usuario_mientras_se_redirecciona <> '') {
                $mensaje_box .= '<p>'.$Solicitud->mensaje_para_el_usuario_mientras_se_redirecciona.'</p>';
            }
        }

        $url_invitacion_grupo_whatsapp = '';
        $url_fanpage = '';
        $url_youtube = '';
        $mnemo_face = '';
        $nombre_de_la_institucion = '';
        $url_invitacion_grupo_facebook = ''; 


        if ($Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual <> '') {
            $url_invitacion_grupo_whatsapp = $Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual;
        }
        else {
            if ($Solicitud->localidad_id <> '') {
                $url_invitacion_grupo_whatsapp = $Solicitud->localidad->url_invitacion_grupo_whatsapp;
            }            
        }
        

        if ($Solicitud->url_enlace_de_invitacion_al_grupo_de_facebook_del_aula_virtual <> '') {
            $url_invitacion_grupo_facebook = $Solicitud->url_enlace_de_invitacion_al_grupo_de_facebook_del_aula_virtual;
        }


        $url_redes = $this->urlRedesEspeciales($solicitud_id);

        if (count($url_redes) > 0) {
            $url_invitacion_grupo_whatsapp = $url_redes['url_invitacion_grupo_whatsapp'];
            $url_fanpage = $url_redes['url_fanpage'];
            $url_youtube = $url_redes['url_youtube'];
            $url_tiktok = $url_redes['url_tiktok'];
            $mnemo_face = $url_redes['mnemo_face'];
            $nombre_de_la_institucion = $url_redes['nombre_de_la_institucion'];
        }
        else {

            if ($Solicitud->idioma_por_pais() <> null) {
                $idioma_por_pais = $Solicitud->idioma_por_pais();    

                if ($idioma_por_pais->url_invitacion_grupo_whatsapp <> '' and $url_invitacion_grupo_whatsapp == '') {
                    $url_invitacion_grupo_whatsapp = $idioma_por_pais->url_invitacion_grupo_whatsapp;
                }

                if ($idioma_por_pais->url_fanpage <> '') {
                    $url_fanpage = $idioma_por_pais->url_fanpage;
                }
                
                if ($idioma_por_pais->url_youtube <> '') {
                    $url_youtube = $idioma_por_pais->url_youtube;
                }

                if ($idioma_por_pais->idioma->mnemo == 'es') {
                    $mnemo_face = 'es_LA';
                }
                else {
                    $mnemo_face = $idioma_por_pais->idioma->mnemo;
                }

                if ($idioma_por_pais->nombre_de_la_institucion <> '') {
                    $nombre_de_la_institucion = $idioma_por_pais->nombre_de_la_institucion;
                }
            }
        }

        // DETERMINO EL TITULO
        $titulo = '';
        if ($Solicitud->titulo_del_formulario_personalizado == '') {
            if ($Solicitud->tipo_de_evento->id == 1) {
                $titulo = __('CURSO DE AUTO-CONOCIMIENTO').'<br><strong>'.$Solicitud->localidad_nombre().'</strong>';  
            }
            if ($Solicitud->tipo_de_evento->id == 2) {
                if ($Solicitud->cant() == 1) {
                    $titulo = __('CONFERENCIA PÚBLICA').':<br><strong> '.$Solicitud->fechas_de_evento[0]->titulo_de_conferencia_publica.'</strong><br>'.$Solicitud->localidad_nombre();   
                }
                else {
                    $titulo = __('CICLO DE CONFERENCIAS PÚBLICAS').'<br>'.$Solicitud->localidad_nombre();            
                }                    
            }
            if ($Solicitud->tipo_de_evento->id == 3) {
                $titulo = __('CURSO DE AUTO-CONOCIMIENTO ON LINE').'<br><strong>'.$Solicitud->localidad_nombre().'</strong>';  
            }
            if ($Solicitud->tipo_de_evento->id == 4) {
                $titulo = '';  
            }
        }
        else {
            $titulo = $Solicitud->titulo_del_formulario_personalizado;
        }

        if ($embebed == 'embebed') {
            $blade_de_formulario = 'registracion-ok-embebed';
        }
        else {
            
            if ($Solicitud->tipo_de_evento_id == 1 and strlen($Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual) > 10 and $idioma_por_pais->idioma_id == 1) {
                $blade_de_formulario = 'template_3_registracion-ok';
            }
            else {
                $blade_de_formulario = 'registracion-ok';
            }
        }

        $url_form_inscripcion = '';
        if ($campania_id == null) {
            $url_form_inscripcion = $Solicitud->url_form_inscripcion();
        }
        else {
            $url_form_inscripcion = $Solicitud->url_form_inscripcion_con_campania_id($campania_id);
        }

        $url_invitacion_grupo_whatsapp = $this->agregarHttp($url_invitacion_grupo_whatsapp);
        $url_invitacion_grupo_facebook = $this->agregarHttp($url_invitacion_grupo_facebook);
        $url_fanpage = $this->agregarHttp($url_fanpage);
        $url_youtube = $this->agregarHttp($url_youtube);

        if ($Solicitud->url_redireccionar_automaticamente_al_enlace <> '') {
            $url_redireccionar_automaticamente_al_enlace = $this->agregarHttp($Solicitud->url_redireccionar_automaticamente_al_enlace);
        }
        else {
            //if (!in_array($Solicitud->id, [7880, 7881, 7882, 7883, 7884, 7886, 7887, 7889, 7524, 7536, 7542, 7543, 7544, 7549, 7550])) {
            if (!in_array($Solicitud->id, [7880, 7881, 7882, 7883, 7884, 7886, 7887, 7889, 7524, 7549, 7550])) {
                $celular_redireccion = $GrupoAsignado['celular_responsable_de_inscripciones'] ? $GrupoAsignado['celular_responsable_de_inscripciones'] : $Solicitud->celular_responsable_de_inscripciones;
                $url_redireccionar_automaticamente_al_enlace = 'https://api.whatsapp.com/send/?phone='.$Solicitud->celular_wa($celular_redireccion).'&text='.__('Hola. Estoy interesado en participar y ya he completado el formulario').'&type=phone_number&app_absent=0';   
            }
        }


        $data = [
            'blade_de_formulario' => 'forms/'.$blade_de_formulario,
            'Solicitud' => $Solicitud,
            'inscripcion_id' => $inscripcion_id,
            'titulo' => $titulo,
            'mensaje_box' => $mensaje_box,
            'url_invitacion_grupo_whatsapp' => $url_invitacion_grupo_whatsapp,
            'url_invitacion_grupo_facebook' => $url_invitacion_grupo_facebook,
            'url_redireccionar_automaticamente_al_enlace' => $url_redireccionar_automaticamente_al_enlace,
            'url_fanpage' => $url_fanpage,
            'url_youtube' => $url_youtube,
            'mnemo_face' => $mnemo_face,
            'url_form_inscripcion' => $url_form_inscripcion,
            'nombre_de_la_institucion' => $nombre_de_la_institucion,
            'dominio_publico' => $Solicitud->dominioPublico(),
        ];
        
        return $data;

    }


