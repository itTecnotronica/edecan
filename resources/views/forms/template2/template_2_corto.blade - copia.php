<?php
use \App\Http\Controllers\SolicitudController; 

$SolicitudController = new SolicitudController;

$idioma_por_pais = $Solicitud->idioma_por_pais();
$pais_id = $idioma_por_pais->pais_id;
$locale_vee_validate = 'en';

if ($Solicitud->idioma_id <> '') {
    $idioma = $Solicitud->idioma->mnemo;           
    $locale_vee_validate = $Solicitud->idioma->locale_vee_validate;             
    App::setLocale($idioma);  
}
else {
  if ($idioma_por_pais->idioma_id <> '') {
      $idioma = $idioma_por_pais->idioma->mnemo;    
      $locale_vee_validate = $idioma_por_pais->idioma->locale_vee_validate;                    
      App::setLocale($idioma);  
  }
}

$cod_pais = '';
$cod_pais_tel = 'null';
if ($idioma_por_pais->pais->mnemo <> '') {
  $cod_pais = $idioma_por_pais->pais->mnemo;
  $cod_pais_tel = "'".$idioma_por_pais->pais->codigo_tel."'";
}

function quitar_www($url) {
  $url = str_replace('www.', '', $url);
  $url = str_replace('http://', '', $url);
  $url = str_replace('https://', '', $url);
  return $url;
}


if (Input::old('pais_id') <> '') {
  $pais_id_sel = Input::old('pais_id');
}
else {
  $pais_id_sel = $pais_id;
}

?>

@extends('layouts.template_2')

@section('titulo')
  <?php echo $Solicitud->descripcion_sin_estado() ?>
@endsection

@section('contenido')

    <div class="page-wrapper <?php echo $bgform ?> p-t-20 p-b-100 font-poppins" <?php echo $style_body ?>>
        <center> <img class="sol-de-acuario-top img-responsive" src="<?php echo $imagen_top ?>" alt="<?php echo $nombre_institucion ?>" title="<?php echo $nombre_institucion ?>"></center>
        
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                      
        
                    <?php if (isset($mensaje_redireccion)) { ?>
                      <?php if ($mensaje_redireccion <> '') { ?>
                      <!-- LISTA DE ERRORES -->
                        <section>    
                            <div class="col-xs-12">
                              <div class="alert bg-danger alert-dismissible">
                                <h5 class="text-danger tit-lista-de-errores"><i class="glyphicon glyphicon-warning-sign "></i> <?php echo __('Atención') ?></h5>  
                                <p><?php echo $mensaje_redireccion ?></p>
                              </div>
                            </div>   
                        </section> 
                      <!-- LISTA DE ERRORES -->
                      <?php } ?>
                    <?php } ?>


                    
                    <!-- TITULOS / IMAGEN / RESUMEN --> 
                      <center><h2 class="title"><?php echo $titulo ?></h2></center>
                      <?php echo $titulo_fecha_inicio ?>
                      <center><p class="subtitulo-cursos"><?php echo $subtitulo ?></p></center>

                      <?php echo $imagen ?>

                      <!-- CURSO POR WHATSAPP -->
                        <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->canal_de_recepcion_del_curso_id == 1) { ?>
                          <!--div class="img-ancho-total img-responsive hidden-xs">
                            <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(8,184,37); background: linear-gradient(311deg, rgba(8,184,37,1) 0%, rgba(33,235,33,1) 21%, rgba(97,252,125,1) 100%);">
                            <img src="<?php echo $dominio_publico?>/img/whatsapp-icon.png">
                            <?php echo __('Curso por'); ?> WhatsApp
                            </p>
                          </div>    
                          <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                            <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(8,184,37); background: linear-gradient(311deg, rgba(8,184,37,1) 0%, rgba(33,235,33,1) 21%, rgba(97,252,125,1) 100%);">
                            <img src="<?php echo $dominio_publico?>/img/whatsapp-icon.png" style="width: 35px">
                            <?php echo __('Curso por'); ?> WhatsApp
                            </p>
                          </div-->                    
                        <?php } ?>
                      <!-- CURSO POR WHATSAPP -->

                      <!-- CURSO POR FACEBOOK -->
                        <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->canal_de_recepcion_del_curso_id == 10) { ?>
                          <!--div class="img-ancho-total img-responsive hidden-xs">
                            <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(8,18,184); background: linear-gradient(311deg, rgba(8,18,184,1) 0%, rgba(33,75,235,1) 21%, rgba(15,52,193,1) 100%);">
                            <img src="<?php echo $dominio_publico?>/img/facebook-icon.png">
                            <?php echo __('Curso por'); ?> Facebook
                            </p>
                          </div>    
                          <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                            <p style="font-size: 15px; font-weight: bold; color: #FFF; text-align: center; padding: 5px; background: rgb(8,18,184);   background: linear-gradient(311deg, rgba(8,18,184,1) 0%, rgba(33,75,235,1) 21%, rgba(15,52,193,1) 100%);">
                            <img src="<?php echo $dominio_publico?>/img/facebook-icon.png" style="width: 35px">
                            <?php echo __('Curso por'); ?> Facebook
                            </p>
                          </div-->                    
                        <?php } ?>
                      <!-- CURSO POR FACEBOOK -->

                      <!-- CURSO POR INSTAGRAM -->
                        <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->canal_de_recepcion_del_curso_id == 9) { ?>
                          <!--div class="img-ancho-total img-responsive hidden-xs">
                            <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                            <img src="<?php echo $dominio_publico?>/img/instagram-icon.png">
                            <?php echo __('Curso por'); ?> Instagram
                            </p>
                          </div>    
                          <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                            <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                            <img src="<?php echo $dominio_publico?>/img/instagram-icon.png" style="width: 35px">
                            <?php echo __('Curso por'); ?> Instagram
                            </p>
                          </div-->                    
                        <?php } ?>
                      <!-- CURSO POR INSTAGRAM -->

                      <?php echo $resumen ?>

                      <?php if ($Solicitud->tipo_de_evento->id <> 4) { ?>
                      <h3><?php echo __('Completa tus datos para reservar un lugar!') ?></h3>
                      <?php } ?>
                    <!-- FIN TITULOS / IMAGEN / RESUMEN --> 


                    <!-- CAMPOS DE FORMULARIO -->                                                
                      <div class="panel-body" id="app-form">
                        {!! Form::open(array
                          (
                          'action' => 'FormController@RegistrarInscripcion', 
                          'role' => 'form',
                          'method' => 'POST',
                          'id' => "form_inscripcion",
                          'enctype' => 'multipart/form-data',
                          'class' => 'form-horizontal',
                          'ref' => 'form',
                          '@submit.prevent' => "validateBeforeSubmit"
                          )) 
                        !!}
                          
                          <!-- VUE-FORM-GENERATOR -->
                            <div class="vue-form-generator">
                                <fieldset>


                                    <!-- NOMBRE -->
                                      <div class="form-group required">
                                        <label for="nombre"><?php echo __('Nombre') ?></label>                                          
                                        <input v-validate="'required'" type="text" value="<?php echo Input::old('nombre') ?>" class="form-control" id="nombre" name="nombre" v-model="nombre" placeholder="<?php echo __('Nombre') ?>" data-vv-as="<?php echo __('Nombre') ?>"  required="required" v-bind:disabled="desabilitar" maxlength="45">
                                        <span v-show="errors.has('nombre')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('nombre') }}</span>                                          
                                        <div class="bg-danger" id="_nombre">{{$errors->first('nombre')}}</div>
                                      </div>
                                    <!-- NOMBRE -->

                                    <!-- APELLIDO -->
                                      <div class="form-group required">                                          
                                        <label for="apellido"><?php echo __('Apellido') ?></label>
                                        <input v-validate="'required'" type="text" value="<?php echo Input::old('apellido') ?>" class="form-control" id="apellido" name="apellido" v-model="apellido" placeholder="<?php echo __('Apellido') ?>" data-vv-as="<?php echo __('Apellido') ?>"  required="required" v-bind:disabled="desabilitar" maxlength="45">  
                                        <span v-show="errors.has('apellido')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('apellido') }}</span>
                                        <div class="bg-danger" id="_apellido">{{$errors->first('apellido')}}</div>
                                      </div>
                                    <!-- APELLIDO -->

                                    <!-- CELULAR -->
                                      <div class="form-group <?php echo $cel_requerido_class ?>">
                                        <label for="celular" style="width: 100%; float: left"><?php echo __('Nro de Teléfono Móvil (Celular)') ?></label>                          
                                        <input v-validate="<?php echo $cel_requerido_v_validate ?>" type="tel" value="<?php echo Input::old('celular') ?>" class="form-control" id="celular" name="celular" v-model="celular" data-vv-as="<?php echo __('Celular') ?>" <?php echo $cel_requerido_input ?> v-bind:disabled="desabilitar" maxlength="45" style="width: 50%; min-width: 200px; float: left" onchange="app['celular_completo'] = iti.getNumber()">
                                        <input type="hidden" name="celular_completo" id="celular_completo" v-model="celular_completo">
                                        <span v-show="errors.has('celular')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('celular') }}</span>
                                        <div class="bg-danger" id="_celular">{{$errors->first('celular')}}</div>
                                      </div>
                                    <!-- CELULAR -->

                                    <!-- CANAL DE RECEPCION -->
                                      <?php if (($Solicitud->sino_habilitar_pedido_de_canal_de_recepcion_del_curso == 'SI' and $Solicitud->tipo_de_evento_id == 3) or $Solicitud->id == 6 ) { ?>
                                        <div class="form-group required">
                                          <label for="pais"><?php echo __('En que plataforma te gustaria recibir el curso') ?></label> 
                                          <?php $Canales = App::make('App\Http\Controllers\HomeController')->get_canales();?>
                                          <?php echo Form::select("canal_de_recepcion_del_curso_id", $Canales, 1, ['id' => "canal_de_recepcion_del_curso_id", 'class' => 'form-control', 'required' => 'required', 'v-model' => 'canal_de_recepcion_del_curso_id', 'v-validate' => "'required'", 'data-vv-as' => __('En que plataforma te gustaria recibir el curso')]); ?>      
                                          <span v-show="errors.has('canal_de_recepcion_del_curso_id')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('canal_de_recepcion_del_curso_id') }}</span>
                                        </div>
                                      <?php } ?>
                                    <!-- CANAL DE RECEPCION -->

                                    <!-- EMAIL -->
                                      <div class="form-group <?php echo $mail_requerido_class ?>">
                                        <label for="email_correo"><?php echo __('Correo Electrónico') ?></label>                          
                                        <input data-vv-as="<?php echo __('Correo Electrónico') ?>" type="text" value="<?php echo Input::old('email_correo') ?>" class="form-control" id="email_correo" name="email_correo" v-model="email_correo" <?php echo $mail_requerido_input ?> placeholder="<?php echo __('Correo Electrónico') ?>" v-bind:disabled="desabilitar" maxlength="80">
                                        <span v-show="errors.has('email_correo')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('email_correo') }}</span>
                                        <div class="bg-danger" id="_email_correo">{{$errors->first('email_correo')}}</div>
                                      </div>
                                    <!-- EMAIL -->



                                    <!-- INICIO SI ES CURSO ONLINE -->
                                      <?php if ($Solicitud->tipo_de_evento_id == 3 or ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id == '') ) { ?>

                                        <!-- PAIS -->
                                          <div class="form-group required">
                                            <label for="pais"><?php echo __('Pais') ?></label> 
                                            <?php $paises = App::make('App\Http\Controllers\HomeController')->get_paises();?>
                                            <?php echo Form::select("pais_id", $paises, $pais_id_sel, ['id' => "pais_id", 'class' => 'form-control', 'required' => 'required', 'v-model' => 'pais_id', 'v-validate' => "'required'", 'data-vv-as' => __('Pais')]); ?>      
                                            <span v-show="errors.has('pais_id')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('pais_id') }}</span>
                                            <div class="bg-danger" id="_pais_id">{{$errors->first('pais_id')}}</div>
                                          </div>
                                        <!-- PAIS -->

                                        <!-- CIUDAD -->
                                          <div class="form-group required">
                                            <label for="ciudad"><?php echo __('Ciudad') ?></label>                          
                                            <input v-validate="'required'" type="text" value="<?php echo Input::old('ciudad') ?>" class="form-control" id="ciudad" name="ciudad" v-model="ciudad" placeholder="<?php echo __('Ciudad') ?>" data-vv-as="<?php echo __('Ciudad') ?>" required="required" v-bind:disabled="desabilitar" maxlength="50">       
                                            <span v-show="errors.has('ciudad')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('ciudad') }}</span>
                                            <div class="bg-danger" id="_ciudad">{{$errors->first('ciudad')}}</div>
                                          </div> 
                                        <!-- CIUDAD -->

                                      <?php } ?>
                                    <!-- FIN SI ES CURSO ONLINE -->

                                    <!-- LOCALIDAD | RECOLECCION DE DATOS-->
                                      <?php if ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id <> '') { ?>
                                        <div class="form-group required">
                                          <label for="ciudad"><?php echo __('Ciudad mas cercana a Ud.') ?></label>      
                                          <?php 
                                          $localidades = App::make('App\Http\Controllers\HomeController')->get_localidadesConProvincia($Solicitud->pais_id);
                                          echo Form::select("localidad_id", $localidades, 1, ['id' => "localidad_id", 'class' => 'form-control', 'required' => 'required', 'v-validate' => "'required'", 'v-model' => 'localidad_id', 'data-vv-as' => __('Ciudad')]); 
                                          ?>
                                          <span v-show="errors.has('localidad_id')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('localidad_id') }}</span>
                                          <div class="bg-danger" id="_localidad_id">{{$errors->first('localidad_id')}}</div>
                                        </div> 
                                      <?php } ?>
                                    <!-- LOCALIDAD | RECOLECCION DE DATOS -->
          
                                    
                                    <!-- INICIO SI TIENE FECHAS DE EVENTOS -->
                                      <?php if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 2 or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4)) { ?>
                                        <h4><?php echo $mensaje_fecha_de_evento ?></h4>
                                        <div class="form-group required"> 
                                          <?php 
                                          if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1) or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4)) {
                                            $type_opcion = 'radio';
                                            $required = 'required="required"';
                                            $required_vue = "'required'";
                                          }
                                          else {
                                            $type_opcion = 'checkbox';  
                                            $required = '';
                                            $required_vue = '';
                                          }
                                          ?>
                                          <!-- RECORRO LAS FECHAS DE EVENTOS -->
                                            <?php foreach ($Fechas_de_eventos as $Fecha_de_evento) { ?>

                                              <?php
                                              if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1) or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4)) {
                                                $nombre_campo = 'fecha_de_evento_id';
                                              }
                                              else {
                                                $nombre_campo = 'fecha_de_evento_id_'.$Fecha_de_evento->id;
                                              }
                                              ?> 

                                              <!-- FECHA DE EVENTOS -->
                                                <?php if ($pais_id == 1) {  ?>
                                                  <?php if ($Fecha_de_evento->sino_agotado == 'NO' or $Fecha_de_evento->sino_agotado == '') { ?>
                                                    <div class="input-group input-radio">
                                                      <span class="input-group-addon">
                                                        
                                                        <input v-validate="<?php echo $required_vue  ?>" type="<?php echo $type_opcion ?>" id="fecha_de_evento_id" name="<?php echo $nombre_campo  ?>" v-model="<?php echo $nombre_campo  ?>" <?php echo $required  ?> value="<?php echo $Fecha_de_evento->id ?>" v-bind:disabled="desabilitar" data-vv-as="<?php echo __('Seleccione una opción') ?>">                                                       
                                                      </span>
                                                      <div class="fecha-de-evento-radio">
                                                        <?php if ($Fecha_de_evento->sino_agotado == 'SI') { ?>
                                                          <p class="bg-danger txt_agotado"><?php echo __('CUPO AGOTADO') ?></p>
                                                        <?php } ?>                                                
                                                        <?php echo $Fecha_de_evento->armarDetalleFechasDeEventos('con_resumen')  ?>
                                                      </div>
                                                    </div>
                                                  <?php } ?>
                                                <?php } 
                                                else { ?>
                                                  <div class="input-group input-radio">
                                                    <span class="input-group-addon">
                                                      <?php 
                                                        $class_agotado = '';
                                                        if ($Fecha_de_evento->sino_agotado == 'SI') {
                                                          $class_agotado = 'agotado';
                                                        }
                                                        else {
                                                      ?>
                                                      <input v-validate="<?php echo $required_vue  ?>" type="<?php echo $type_opcion ?>" id="fecha_de_evento_id" name="<?php echo $nombre_campo  ?>" v-model="<?php echo $nombre_campo  ?>" <?php echo $required  ?> value="<?php echo $Fecha_de_evento->id ?>" v-bind:disabled="desabilitar" data-vv-as="<?php echo __('Seleccione una opción') ?>"> 
                                                      <?php } ?>
                                                    </span>
                                                    <div class="fecha-de-evento-radio <?php echo $class_agotado ?>">
                                                      <?php if ($Fecha_de_evento->sino_agotado == 'SI') { ?>
                                                        <p class="bg-danger txt_agotado"><?php echo __('CUPO AGOTADO') ?></p>
                                                      <?php } ?>                                                
                                                      <?php echo $Fecha_de_evento->armarDetalleFechasDeEventos('con_resumen')  ?>
                                                    </div>
                                                  </div>
                                              <?php } ?>
                                              <!-- FECHA DE EVENTOS -->
                                              
                                            <?php } ?> 
                                          <!-- FIN RECORRO LAS FECHAS DE EVENTOS -->


                                          <!-- OPCION MODALIDAD ONLINE -->
                                            <?php if ($Solicitud->sino_habilitar_modalidad_online == 'SI') { ?>
                                              <div class="input-group input-radio">
                                                <span class="input-group-addon">
                                                  <input v-validate="<?php echo $required_vue  ?>" type="<?php echo $type_opcion ?>" id="fecha_de_evento_id" name="fecha_de_evento_id" v-model="fecha_de_evento_id" <?php echo $required  ?> value="MO" v-bind:disabled="desabilitar" data-vv-as="<?php echo __('Seleccione una opción') ?>"> 
                                                </span>
                                                <div class="fecha-de-evento-radio"><?php echo __('Quisiera hacer este curso de forma online') ?></div>
                                              </div>

                                              <span v-show="errors.has('fecha_de_evento')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('fecha_de_evento') }}</span>
                                            <?php } ?> 
                                          <!-- OPCION MODALIDAD ONLINE -->



                                          <!-- FECHA DE EVENTOS: NO PUEDE ASISTIR -->
                                            <div class="input-group input-radio">
                                              <span class="input-group-addon">
                                                <input v-validate="<?php echo $required_vue  ?>" type="<?php echo $type_opcion ?>" id="fecha_de_evento_id" name="fecha_de_evento_id" v-model="fecha_de_evento_id" <?php echo $required  ?> value="NP" v-bind:disabled="desabilitar" data-vv-as="<?php echo __('Seleccione una opción') ?>"> 
                                              </span>
                                              <div class="fecha-de-evento-radio"><?php echo __('No puedo asistir a este horario pero quisiera me contacten mas adelante por otros días y horarios') ?></div>
                                            </div>

                                            <span v-show="errors.has('fecha_de_evento')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('fecha_de_evento') }}</span>
                                          <!-- FECHA DE EVENTOS: NO PUEDE ASISTIR -->
                                    
                                        </div>
                                      <?php } ?> 
                                    <!-- FIN SI TIENE FECHAS DE EVENTOS -->

                                    
                                    <!-- CONSULTA -->
                                      <?php if ($Solicitud->tipo_de_evento_id <> 4) { ?>
                                        <div class="form-group">
                                          <label for="consulta"><?php echo __('Alguna pregunta para hacernos?') ?></label>                          
                                          <textarea class="form-control" id="consulta" name="consulta" v-model="consulta" placeholder="<?php echo __('tu consulta') ?>" maxlength="300" v-bind:disabled="desabilitar"></textarea>  
                                          <span v-show="errors.has('consulta')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('consulta') }}</span>
                                        </div>
                                      <?php } ?> 
                                    <!-- CONSULTA -->

                                    <!-- NOTIFICAR PROXIMOS EVENTOS -->
                                      <div class="form-group">
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" id="sino_notificar_proximos_eventos" name="sino_notificar_proximos_eventos" v-model="sino_notificar_proximos_eventos" placeholder="Correo" v-bind:disabled="desabilitar"><?php echo __('Me gustaría recibir información sobre los próximos cursos y eventos gratuitos') ?>
                                          </label>
                                          <span v-show="errors.has('sino_notificar_proximos_eventos')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('sino_notificar_proximos_eventos') }}</span>
                                        </div>
                                      </div>
                                    <!-- NOTIFICAR PROXIMOS EVENTOS -->

                                    <!-- POLITICAS DE PRIVACIDAD -->
                                      <?php  if ($acepto_politica_de_privacidad) { ?> 
                                      <div class="form-group">
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" id="acepto_politica_de_privacidad" name="acepto_politica_de_privacidad" v-validate="'required'" v-model="acepto_politica_de_privacidad" v-bind:disabled="desabilitar" required="required" data-vv-as="<?php echo __('Acepto la política de privacidad') ?>">
                                            <?php echo __('Acepto la política de privacidad') ?>                                              
                                          </label>
                                          <span v-show="errors.has('acepto_politica_de_privacidad')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('acepto_politica_de_privacidad') }}</span>
                                        </div>
                                      </div>
                                      <h5><u><?php echo __('Política de privacidad') ?></u></h5>
                                      <p style="text-align: justify;"><?php echo $politica_de_privacidad ?></p>
                                      <?php } ?> 
                                    <!-- POLITICAS DE PRIVACIDAD -->

                                    <!-- LISTA DE ERRORES -->
                                      <section v-show="errors.count()>0">
                                        <div class="row errores-invisibles" v-bind:style="class_errores">    
                                          <div class="col-xs-12">
                                            <br>
                                            <div class="alert bg-danger alert-dismissible">
                                              <h5 class="text-danger tit-lista-de-errores"><i class="glyphicon glyphicon-warning-sign "></i> Error</h5>  
                                              <ul class="text-danger lista-de-errores">
                                                <li v-for="error in errors.all()">@{{ error }}</li>
                                              </ul>
                                            </div>
                                          </div>   
                                        </div>
                                      </section> 
                                    <!-- LISTA DE ERRORES -->
                                    
                                    <br><br>

                                    <!-- CAMPOS OCULTOS Y SUBMIT -->
                                      <?php 
                                      if ($Solicitud->label_boton_enviar <> '') {
                                        $labelSubmit = $Solicitud->label_boton_enviar;
                                      }
                                      else {
                                        $labelSubmit = 'Inscribirme';
                                      }
                                      ?>
                                      <div class="form-group">
                                          <input type="hidden" name="solicitud_id" value="<?php echo $Solicitud->id ?>">
                                          <input type="hidden" name="campania_id" value="<?php echo $campania_id ?>">
                                          <input type="hidden" name="app_usuario_id" value="<?php echo $app_usuario_id ?>">
                                          
                                          
                                          <!-- BOTON INSCRIBIRME 1 -->
                                          <center>
                                            <button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="desabilitar"> <?php echo __($labelSubmit) ?> </button>
                                          </center>
                                          <!-- BOTON INSCRIBIRME 1 -->

                                      </div>
                                    <!-- CAMPOS OCULTOS Y SUBMIT -->

                                    <br><br>

                                    <?php  if ($texto <> '') { ?> 
                                      <p style="text-align: justify"><br><?php echo $texto ?></p>
                                    
                                      <!-- BOTON INSCRIBIRME 5 -->
                                        <div class="form-group">
                                            <center><button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="desabilitar"> <?php echo __('Inscribirme') ?> </button></center>
                                        </div>
                                      <!-- BOTON INSCRIBIRME 5 -->

                                    <?php } ?> 
                                    
                                    <br><br>

                                    <!-- PANEL DATOS CONTACTO -->
                                      <div class="panel panel-danger">
                                        <div class="panel-heading">
                                          <h3 class="panel-title"><?php echo __('Contacto') ?></h3>
                                        </div>
                                        
                                        <!-- INFO DATOS PC -->
                                          <div class="panel-body hidden-xs visible-lg visible-md visible-sm">
                                            <?php $celular_responsable_de_inscripciones = str_replace('+', '', $Solicitud->celular_responsable_de_inscripciones); ?>
                                            <p>
                                              <?php echo __('Informes') ?>: <?php echo $Solicitud->celular_responsable_de_inscripciones ?>  <a href="<?php echo $Solicitud->url_contacto_whatsapp_form() ?>" target="_blank">(<?php echo __('Enviar WhatsApp') ?>)</a></p>
                                          </div>
                                        <!-- INFO DATOS PC -->

                                        <!-- INFO DATOS MOBILE -->
                                          <div class="panel-body visible-xs hidden-lg hidden-md hidden-sm">
                                            <?php $celular_responsable_de_inscripciones = str_replace('+', '', $Solicitud->celular_responsable_de_inscripciones); ?>
                                              <p class="text-xs">
                                                <?php echo __('Informes') ?>: <br>
                                                <?php echo $Solicitud->celular_responsable_de_inscripciones ?>  <a href="<?php echo $Solicitud->url_contacto_whatsapp_form() ?>" target="_blank">(<?php echo __('Enviar WhatsApp') ?>)</a>
                                              </p>
                                          </div>
                                        <!-- INFO DATOS MOBILE -->

                                      </div>
                                    <!-- FIN PANEL DATOS CONTACTO -->



                                </fieldset>
                                <!--div class="col-lg-12">            
                                  <pre>@{{ $data }}</pre>
                                </div-->                                    
                            </div>
                          <!-- VUE-FORM-GENERATOR -->


                          {!! Form::close() !!}
                      </div>
                    <!-- CAMPOS DE FORMULARIO -->
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="<?php echo $dominio_publico?>templates/2/vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="<?php echo $dominio_publico?>templates/2/vendor/select2/select2.min.js"></script>
    <script src="<?php echo $dominio_publico?>templates/2/vendor/datepicker/moment.min.js"></script>
    <script src="<?php echo $dominio_publico?>templates/2/vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="<?php echo $dominio_publico?>templates/2/js/global.js"></script>

    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo $dominio_publico?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">

</body>

</html>
<!-- end document-->


<!-- INICIO APP app-form -->
    <script type="text/javascript">
        const config = {
          locale: '<?php echo $locale_vee_validate ?>', 
        };
        //moment.locale('es');
        //console.log(moment());
        Vue.use(VeeValidate, config);

        var app = new Vue({
          el: '#app-form',

          data: {
            apellido: '<?php echo Input::old('apellido') ?>',
            nombre: '<?php echo Input::old('nombre') ?>',
            cod_pais: null,
            celular: '<?php echo Input::old('celular') ?>',
            celular_completo: '<?php echo Input::old('celular') ?>',
            pais_id: <?php echo $pais_id_sel ?>,
            canal_de_recepcion_del_curso_id: 1,
            email_correo: '<?php echo Input::old('email_correo') ?>',
            consulta: '<?php echo Input::old('consulta') ?>',
            ciudad: <?php echo $ciudad ?>,
            fecha_de_evento_id: null,
            sino_notificar_proximos_eventos: true,
            acepto_politica_de_privacidad: false,
            class_errores: 'visibility: visible !important',
            mensaje_error: '',
            desabilitar: <?php echo $deshabilitar_formulario; ?>
          },

          methods: {
            validateBeforeSubmit() {
              this.$validator.validateAll().then((result) => {
                if (result) {
                  // eslint-disable-next-line
                  $('#form_inscripcion').submit()
                  return;
                }
                else {
                  $('#form_inscripcion').submit()
                }
              });
            }
              
          },

          filters: {
            formatoMoneda: function (value) {
              let val = (value/1).toFixed(2).replace('.', ',')
              return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
          }

        })

      
    </script>
<!-- FIN APP app-form -->



<!-- SCRIPT CELULAR -->
  <script src="<?php echo $dominio_publico?>node_modules/intl-tel-input/build/js/intlTelInput.js"></script>
  <script>
  var input = document.querySelector("#celular");
  var iti = window.intlTelInput(input, {
    utilsScript: "<?php echo $dominio_publico?>node_modules/intl-tel-input/build/js/utils.js?1585994360633", // just for formatting/
    //placeholderNumberType: "FIXED_LINE",
    separateDialCode: true,
    preferredCountries: []
  });
    input.addEventListener("countrychange", function() {
      if (iti.getNumber() != '') {
       app["celular"] = input.value;
       app["celular_completo"] = iti.getNumber();
      }
    });
    iti.setCountry("<?php echo $cod_pais ?>");
  </script> 
<!-- SCRIPT CELULAR -->



@endsection
