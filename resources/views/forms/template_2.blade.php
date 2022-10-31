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

if ($Solicitud->id == 15095) {
  //dd(__('messages.informacion_del_curso_1'));
}

//echo 'idioma: '.App::getLocale($idioma);
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

<!DOCTYPE html>
<html lang="<?php echo $idioma ?>">

<head>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <!--script async src="https://www.googletagmanager.com/gtag/js?id=UA-46601315-3"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-46601315-3');
    </script-->

    <!-- Required meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta name="author" content="<?php echo $nombre_institucion ?>.is">
    <meta name="keywords" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta property="og:title" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?>" />
    <meta property="og:url" content="<?php echo $Solicitud->url_form_inscripcion() ?>" />
    <meta property="og:description" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta property="og:image" content="<?php echo $imagen_chica ?>">

    <!-- Title Page-->
    <title><?php echo $Solicitud->descripcion_sin_estado(false) ?></title>

    <!-- Icons font CSS-->
    <link href="<?php echo $dominio_publico?>templates/2/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $dominio_publico?>templates/2/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="<?php echo $dominio_publico?>templates/2/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $dominio_publico?>templates/2/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="<?php echo $dominio_publico?>templates/2/css/main.css" rel="stylesheet" media="all">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/bootstrap/dist/css/bootstrap.min.css">

    <!-- vue.js -->
    <script src="<?php echo $dominio_publico?>js/vue/vue.js"></script>
    <script src="<?php echo $dominio_publico?>js/vee-validate/dist/vee-validate.js"></script>
    <script src="<?php echo $dominio_publico?>js/vee-validate/dist/locale/<?php echo $locale_vee_validate ?>.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $dominio_publico?>js/vue-form-generator/vfg.css">

    <link rel="stylesheet" href="<?php echo $dominio_publico?>node_modules/intl-tel-input/build/css/intlTelInput.css">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WWP64FV');</script>
    <!-- End Google Tag Manager -->

    <!-- Facebook Pixel Code -->
    <!--script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '<?php echo env('PIXEL_AC_MUNDIAL')?>');
      fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=<?php echo env('PIXEL_AC_MUNDIAL')?>&ev=PageView&noscript=1"
    /></noscript-->
    <!-- End Facebook Pixel Code -->

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook);
    }
    ?>

</head>

<body>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WWP64FV"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_script_body)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_script_body);
    }
    ?>
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
                          <div class="img-ancho-total img-responsive hidden-xs">
                            <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(8,184,37); background: linear-gradient(311deg, rgba(8,184,37,1) 0%, rgba(33,235,33,1) 21%, rgba(97,252,125,1) 100%);">
                            <!--img src="<?php echo $dominio_publico?>/img/whatsapp-icon.png"-->
                            <?php echo __('Curso por WhatsApp'); ?> 
                            </p>
                          </div>
                          <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                            <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(8,184,37); background: linear-gradient(311deg, rgba(8,184,37,1) 0%, rgba(33,235,33,1) 21%, rgba(97,252,125,1) 100%);">
                            <!--img src="<?php echo $dominio_publico?>/img/whatsapp-icon.png" style="width: 35px"-->
                            <?php echo __('Curso por WhatsApp'); ?> 
                            </p>
                          </div>
                        <?php } ?>
                      <!-- CURSO POR WHATSAPP -->

                      <!-- CURSO POR FACEBOOK -->
                        <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->canal_de_recepcion_del_curso_id == 10) { ?>
                          <div class="img-ancho-total img-responsive hidden-xs">
                            <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(8,18,184); background: linear-gradient(311deg, rgba(8,18,184,1) 0%, rgba(33,75,235,1) 21%, rgba(15,52,193,1) 100%);">
                            <!--img src="<?php echo $dominio_publico?>/img/facebook-icon.png"-->
                            <?php echo __('Curso por Facebook'); ?> 
                            </p>
                          </div>
                          <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                            <p style="font-size: 15px; font-weight: bold; color: #FFF; text-align: center; padding: 5px; background: rgb(8,18,184);   background: linear-gradient(311deg, rgba(8,18,184,1) 0%, rgba(33,75,235,1) 21%, rgba(15,52,193,1) 100%);">
                            <!--img src="<?php echo $dominio_publico?>/img/facebook-icon.png" style="width: 35px"-->
                            <?php echo __('Curso por Facebook'); ?> 
                            </p>
                          </div>
                        <?php } ?>
                      <!-- CURSO POR FACEBOOK -->

                      <!-- CURSO POR INSTAGRAM -->
                        <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->canal_de_recepcion_del_curso_id == 9) { ?>
                          <div class="img-ancho-total img-responsive hidden-xs">
                            <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                            <!--img src="<?php echo $dominio_publico?>/img/instagram-icon.png"-->
                            <?php echo __('Curso por Instagram'); ?> 
                            </p>
                          </div>
                          <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                            <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                            <!--img src="<?php echo $dominio_publico?>/img/instagram-icon.png" style="width: 35px"-->
                            <?php echo __('Curso por Instagram'); ?> 
                            </p>
                          </div>
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

                                    <!-- INICIO SI ES CURSO ONLINE -->
                                      <?php if ($Solicitud->tipo_de_evento_id == 3 or ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id == '') or ($Solicitud->tipo_de_evento_id == 4 and !in_array($Solicitud->id, array(3747, 4033)) ) )  { ?>

                                        <!-- PAIS -->
                                          <div class="form-group required">
                                            <label for="pais"><?php echo __('Pais') ?></label>
                                            <?php $paises = App::make('App\Http\Controllers\HomeController')->get_paises();?>
                                            <?php echo Form::select("pais_id", $paises, $pais_id_sel, ['id' => "pais_id", 'class' => 'form-control', 'required' => 'required', 'v-model' => 'pais_id', 'v-validate' => "'required'", 'data-vv-as' => __('Pais'), 'v-on:change' => 'cambiarCodigoTelPais(this)']); ?>
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
                                      <?php if (($Solicitud->sino_habilitar_pedido_de_canal_de_recepcion_del_curso == 'SI' and ($Solicitud->tipo_de_evento_id == 3 or $Solicitud->tipo_de_evento_id == 4)) or $Solicitud->id == 6 or $Solicitud->id == 10715 ) { ?>
                                          <?php 
                                          $array_canales_id = null;
                                          if ($Solicitud->id == 10715) {
                                            $array_canales_id = [1,2];
                                            } 
                                          if ($Solicitud->id == 15731) {
                                            $array_canales_id = [1,5,9];
                                            } 
                                          ?>
                                        <div class="form-group required">
                                          <label for="pais"><?php echo __('En que plataforma te gustaria recibir el curso') ?></label>
                                          <?php $Canales = App::make('App\Http\Controllers\HomeController')->get_canales($array_canales_id);?>
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


                                    <!-- LOCALIDAD | RECOLECCION DE DATOS-->
                                      <?php if ($Solicitud->tipo_de_evento_id == 4 and $Solicitud->pais_id <> '' and in_array($Solicitud->id, array(3747, 4033)) ) { ?>
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
                                      <?php if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 2 or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4) or ($Solicitud->tipo_de_evento_id == 4 and $Fechas_de_eventos->count() > 0) ) { ?>
                                        <h4><?php echo $mensaje_fecha_de_evento ?></h4>
                                        <div class="form-group required">
                                          <?php
                                          if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1) or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4) or ($Solicitud->tipo_de_evento_id == 4 and $Fechas_de_eventos->count() > 0)) {
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
                                              if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1) or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4) or ($Solicitud->tipo_de_evento_id == 4 and $Fechas_de_eventos->count() > 0)) {
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

                                                        <?php

                                                        $tipo = 'con_resumen';
                                                        if ($Solicitud->tipo_de_curso_online_id == 4) {
                                                          $con_inicio = false;
                                                          $ver_mapa = false;
                                                          $con_dir_inicio_distinto = false;
                                                        }
                                                        else {
                                                          $con_inicio = true;
                                                          $ver_mapa = true;
                                                          $con_dir_inicio_distinto = true;
                                                        }


                                                        echo $Fecha_de_evento->armarDetalleFechasDeEventos($tipo, $con_inicio, $idioma_por_pais, $Solicitud, $idioma, $ver_mapa, $con_dir_inicio_distinto);
                                                        ?>
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


                                          <?php if ($Solicitud->tipo_de_evento_id <> 4) { ?>
                                            <!-- FECHA DE EVENTOS: NO PUEDE ASISTIR -->
                                              <div class="input-group input-radio">
                                                <span class="input-group-addon">
                                                  <input v-validate="<?php echo $required_vue  ?>" type="<?php echo $type_opcion ?>" id="fecha_de_evento_id" name="fecha_de_evento_id" v-model="fecha_de_evento_id" <?php echo $required  ?> value="NP" v-bind:disabled="desabilitar" data-vv-as="<?php echo __('Seleccione una opción') ?>">
                                                </span>
                                                <div class="fecha-de-evento-radio"><?php echo __('No puedo asistir a este horario pero quisiera me contacten mas adelante por otros días y horarios') ?></div>
                                              </div>

                                              <span v-show="errors.has('fecha_de_evento')" class="errores-invisibles text-danger" v-bind:style="class_errores">@{{ errors.first('fecha_de_evento') }}</span>
                                            <!-- FECHA DE EVENTOS: NO PUEDE ASISTIR -->
                                          <?php } ?>

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
                                    <!-- INICIO CURSO DE AUTO-CONOCIMIENTO PRESENCIAL U ONLINE -->
                                      <?php  if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 3) { ?>
                                        <!-- CURSO POR WHATSAPP -->
                                          <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->canal_de_recepcion_del_curso_id == 1) { ?>
                                            <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(8,184,37); background: linear-gradient(311deg, rgba(8,184,37,1) 0%, rgba(33,235,33,1) 21%, rgba(97,252,125,1) 100%);">
                                            <!--img src="<?php echo $dominio_publico?>/img/whatsapp-icon.png" style="width: 35px"-->
                                            <?php echo __('Curso por WhatsApp'); ?>
                                            </p>
                                            <h3><?php echo __('messages.tit_requisito_cursos_whatsapp') ?></h3>
                                            <p><?php echo __('messages.tit_info_requisito_cursos_whatsapp') ?></p>
                                            <h3><?php echo __('messages.tit_info_cursos_whatsapp') ?></h3>
                                            <?php if ($Solicitud->institucion_id == 2) { ?>
                                              <p><?php echo __('messages.info_cursos_whatsapp_asoprovida') ?></p>
                                            <?php }
                                            else {?>
                                              <p><?php echo __('messages.info_cursos_whatsapp') ?></p>
                                            <?php } ?>

                                          <?php } ?>
                                        <!-- CURSO POR WHATSAPP -->

                                        <!-- CURSO POR INSTAGRAM -->
                                          <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->canal_de_recepcion_del_curso_id == 9) { ?>
                                            <div class="img-ancho-total img-responsive hidden-xs">
                                              <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                                              <img src="<?php echo $dominio_publico?>/img/instagram-icon.png">
                                              <?php echo __('Curso por Instagram'); ?> 
                                              </p>
                                            </div>
                                            <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                                              <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                                              <img src="<?php echo $dominio_publico?>/img/instagram-icon.png" style="width: 35px">
                                              <?php echo __('Curso por Instagram'); ?> 
                                              </p>
                                            </div>
                                            <!--h3><?php echo __('messages.tit_requisito_cursos_instagram') ?></h3>
                                            <p><?php echo __('messages.tit_info_requisito_cursos_instagram') ?></p>
                                            <h3><?php echo __('messages.tit_info_cursos_instagram') ?></h3>
                                            <p><strong><?php echo __('Registrate en este formulario y luego seguí nuestra cuenta de Instagram') ?> <a href="<?php echo $Solicitud->url_enlace_cuenta_de_instagram ?>" target="_blank"><?php echo $Solicitud->nombre_de_usuario_instagram ?></a></strong> <?php echo __('donde reicibirás las lecciones semanalmente en sesiones en vivo, podrás hacer tus preguntas, entregaremos dinámicas, videos y material complementario. Todo desde la misma cuenta') ?>.<br></p-->
                                          <?php } ?>
                                        <!-- CURSO POR INSTAGRAM -->


                                        <!-- INFO DEL CURSO -->
                                          <?php if ($Solicitud->sino_ocultar_informacion_del_curso_predeterminado <> 'SI' and ($Solicitud->institucion_id <> '2')) { ?>
                                            <h3><?php echo __('Información del Curso') ?></h3>
                                            <p><?php echo __('messages.informacion_del_curso_1') ?></p>
                                            <?php
                                            if ($Solicitud->tipo_de_evento_id == 3) {
                                              if ($pais_id == 10) {
                                                $informacion_del_curso_2 = str_replace('23', '16', __('messages.informacion_del_curso_2'));
                                              }
                                              else {
                                                //$informacion_del_curso_2 = str_replace('23', '17', __('messages.informacion_del_curso_2'));
                                                $informacion_del_curso_2 = __('messages.informacion_del_curso_2');
                                              }

                                            }
                                            else {
                                              $informacion_del_curso_2 = __('messages.informacion_del_curso_2');
                                            }

                                            ?>

                                            <p><?php echo $informacion_del_curso_2 ?></p>
                                            <p><?php echo __('messages.informacion_del_curso_3') ?></p>

                                            <img class="img-ancho-total hidden-xs visible-sm visible-md visible-lg" src="<?php echo $dominio_publico?>/templates/2/img/puerta-a-tu-mundo-interior.jpg">
                                            <img class="img-ancho-total visible-xs hidden-sm hidden-md hidden-lg" src="<?php echo $dominio_publico?>/templates/2/img/puerta-a-tu-mundo-interior-c.jpg">

                                            <br><br>
                                            <div class="form-group">
                                              <!-- BOTON INSCRIBIRME 2 -->
                                                <center>
                                                  <button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="desabilitar"> <?php echo __($labelSubmit) ?> </button>
                                                </center>
                                              <!-- BOTON INSCRIBIRME 2 -->
                                            </div><br><br>

                                            <h4><?php echo __('Algunas temáticas del curso') ?></h4>

                                            <p><?php echo __('messages.algunas_tematicas_1'); ?></p>
                                            <p><?php echo __('messages.algunas_tematicas_2') ?></p>
                                            <p><?php echo __('messages.algunas_tematicas_3') ?></p>
                                            <p><?php echo __('messages.algunas_tematicas_4') ?></p>
                                            <p><?php echo __('messages.algunas_tematicas_5') ?></p>
                                            <p><?php echo __('messages.algunas_tematicas_6') ?></p>
                                            <p><?php echo __('messages.algunas_tematicas_7') ?></p>
                                            <p><?php echo __('messages.algunas_tematicas_8') ?></p>
                                            <p><?php echo __('messages.algunas_tematicas_9') ?></p>

                                            <img class="img-ancho-total hidden-xs visible-sm visible-md visible-lg" src="<?php echo $dominio_publico?>/templates/2/img/antiguas-civilizaciones.jpg">
                                            <img class="img-ancho-total visible-xs hidden-sm hidden-md hidden-lg" src="<?php echo $dominio_publico?>/templates/2/img/antiguas-civilizaciones-c.jpg">
                                            <br><br>
                                            <div class="form-group">
                                                <!-- BOTON INSCRIBIRME 3 -->
                                                <center>
                                                  <button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="desabilitar"> <?php echo __($labelSubmit) ?> </button>
                                                </center>
                                                <!-- BOTON INSCRIBIRME 3 -->
                                            </div><br><br>

                                            <h4><?php echo __('El propósito de este curso') ?></h4>

                                            <p><?php echo __('messages.proposito_de_curso_1') ?></p>
                                            <p><?php echo __('messages.proposito_de_curso_2') ?></p>
                                            <p><?php echo __('messages.proposito_de_curso_3') ?></p>
                                            <p><?php echo __('messages.proposito_de_curso_4') ?></p>
                                            <p><?php echo __('messages.proposito_de_curso_5') ?></p>
                                            <p><?php echo __('messages.proposito_de_curso_6') ?></p>
                                            <p><?php echo __('messages.proposito_de_curso_7') ?></p>
                                            <p><?php echo __('messages.proposito_de_curso_8') ?></p>
                                            <br>

                                            <img class="img-ancho-total hidden-xs visible-sm visible-md visible-lg" src="<?php echo $dominio_publico?>/templates/2/img/escuela-de-atenas.jpg">
                                            <img class="img-ancho-total visible-xs hidden-sm hidden-md hidden-lg" src="<?php echo $dominio_publico?>/templates/2/img/escuela-de-atenas-c.jpg">
                                            <br><br>


                                            <!-- BOTON INSCRIBIRME 4 -->
                                              <div class="form-group">
                                                  <center><button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="desabilitar"> <?php echo __($labelSubmit) ?> </button></center>
                                              </div>
                                            <!-- BOTON INSCRIBIRME 4 -->

                                          <?php } ?>
                                        <!-- INFO DEL CURSO -->
                                      <?php } ?>
                                    <!-- FIN CURSO DE AUTO-CONOCIMIENTO PRESENCIAL U ONLINE -->

                                    <?php  if ($texto <> '') { ?>
                                      <p style="text-align: justify"><br><?php echo $texto ?></p>

                                      <!-- BOTON INSCRIBIRME 5 -->
                                        <div class="form-group">
                                            <center><button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="desabilitar"> <?php echo __($labelSubmit) ?> </button></center>
                                        </div>
                                      <!-- BOTON INSCRIBIRME 5 -->

                                    <?php } ?>

                                    <br><br>

                                    <!-- PANEL DATOS CONTACTO -->
                                      <?php if ($Solicitud->sino_ocultar_info_contacto <> 'SI') { ?>
                                        <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h3 class="panel-title"><?php echo __('Contacto') ?></h3>
                                          </div>

                                          <!-- INFO DATOS PC -->
                                            <div class="panel-body hidden-xs visible-lg visible-md visible-sm">
                                              <?php if($url_redes['url_youtube'] <> '') { ?>
                                                <!--center>
                                                  <a href="<?php echo $url_redes['url_youtube'] ?>?sub_confirmation=1" target="_blank">
                                                    <button type="button" class="btn btn-warning" style="background-color: #ff0000"><?php echo __('Suscribete a nuestro canal de Youtube') ?></button>
                                                  </a><br>
                                                <?php echo _('para acceder a todos nuestros videos') ?><br><br>
                                                </center-->
                                              <?php } ?>
                                              <?php $celular_responsable_de_inscripciones = str_replace('+', '', $Solicitud->celular_responsable_de_inscripciones); ?>
                                              <p>
                                                <?php echo __('Informes') ?>: <?php echo $Solicitud->celular_responsable_de_inscripciones ?>  <a href="<?php echo $Solicitud->url_contacto_whatsapp_form() ?>" target="_blank">(<?php echo __('Enviar WhatsApp') ?>)</a></p>
                                              <?php if($url_redes['url_sitio_web'] <> '') { ?>
                                                <p><?php echo __('Sitio Web') ?>: <a href="<?php echo $url_redes['url_sitio_web'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_sitio_web']) ?></a></p>
                                              <?php } ?>
                                              <?php if($url_redes['url_fanpage'] <> '') { ?>
                                              <p><?php echo __('Facebook') ?>: <a href="<?php echo $url_redes['url_fanpage'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_fanpage']) ?></a></p>
                                              <?php } ?>
                                              <?php if($url_redes['url_youtube'] <> '') { ?>
                                                <p><?php echo __('Youtube') ?>: <a href="<?php echo $url_redes['url_youtube'] ?>?sub_confirmation=1" target="_blank"><?php echo quitar_www($url_redes['url_youtube']) ?></a></p>
                                              <?php } ?>
                                              <?php if($url_redes['url_twitter'] <> '') { ?>
                                                <p><?php echo __('Twitter') ?>: <a href="<?php echo $url_redes['url_twitter'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_twitter']) ?></a></p>
                                              <?php } ?>
                                              <?php if($url_redes['url_instagram'] <> '') { ?>
                                                <p><?php echo __('Instagram') ?>: <a href="<?php echo $url_redes['url_instagram'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_instagram']) ?></a></p>
                                              <?php } ?>
                                              <?php if($url_redes['url_tiktok'] <> '') { ?>
                                                <p><?php echo __('TikTok') ?>: <a href="<?php echo $url_redes['url_tiktok'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_tiktok']) ?></a></p>
                                              <?php } ?>
                                            </div>
                                          <!-- INFO DATOS PC -->

                                          <!-- INFO DATOS MOBILE -->
                                            <div class="panel-body visible-xs hidden-lg hidden-md hidden-sm">
                                              <?php if($url_redes['url_youtube'] <> '') { ?>
                                                <!--center>
                                                  <a href="<?php echo $url_redes['url_youtube'] ?>?sub_confirmation=1" target="_blank">
                                                    <button type="button" class="btn btn-warning" style="background-color: #ff0000"><?php echo __('Suscribete a nuestro canal de Youtube') ?></button>
                                                  </a><br>
                                                <?php echo __('para acceder a todos nuestros videos') ?><br><br>
                                                </center-->
                                              <?php } ?>
                                              <?php $celular_responsable_de_inscripciones = str_replace('+', '', $Solicitud->celular_responsable_de_inscripciones); ?>
                                                <p class="text-xs" style="text-align: left;">
                                                  <?php echo __('Informes') ?>: <br>
                                                  <?php echo $Solicitud->celular_responsable_de_inscripciones ?>  <a href="<?php echo $Solicitud->url_contacto_whatsapp_form() ?>" target="_blank">(<?php echo __('Enviar WhatsApp') ?>)</a>
                                                </p>
                                              <?php if($url_redes['url_sitio_web'] <> '') { ?>
                                                <p class="text-xs" style="text-align: left;"><?php echo __('Sitio Web') ?>:  <br>
                                                  <a href="<?php echo $url_redes['url_sitio_web'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_sitio_web']) ?></a>
                                                </p>
                                              <?php } ?>
                                              <?php if($url_redes['url_fanpage'] <> '') { ?>
                                              <p class="text-xs" style="text-align: left;"><?php echo __('Facebook') ?>: <br>
                                                <a href="<?php echo $url_redes['url_fanpage'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_fanpage']) ?></a>
                                              </p>
                                              <?php } ?>
                                              <?php if($url_redes['url_youtube'] <> '') { ?>
                                                <p class="text-xs" style="text-align: left;"><?php echo __('Youtube') ?>:  <br>
                                                  <a href="<?php echo $url_redes['url_youtube'] ?>?sub_confirmation=1" target="_blank"><?php echo quitar_www($url_redes['url_youtube']) ?></a>
                                                </p>
                                              <?php } ?>
                                              <?php if($url_redes['url_twitter'] <> '') { ?>
                                                <p class="text-xs" style="text-align: left;"><?php echo __('Twitter') ?>:  <br>
                                                  <a href="<?php echo $url_redes['url_twitter'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_twitter']) ?></a>
                                                </p>
                                              <?php } ?>
                                              <?php if($url_redes['url_instagram'] <> '') { ?>
                                                <p class="text-xs" style="text-align: left;"><?php echo __('Instagram') ?>:  <br>
                                                  <a href="<?php echo $url_redes['url_instagram'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_instagram']) ?></a>
                                                </p>
                                              <?php } ?>
                                              <?php if($url_redes['url_tiktok'] <> '') { ?>
                                                <p><?php echo __('TikTok') ?>: <a href="<?php echo $url_redes['url_tiktok'] ?>" target="_blank"><?php echo quitar_www($url_redes['url_tiktok']) ?></a></p>
                                              <?php } ?>
                                            </div>
                                          <!-- INFO DATOS MOBILE -->

                                        </div>
                                      <?php } ?>
                                    <!-- FIN PANEL DATOS CONTACTO -->

                                    <!-- PANEL DATOS CONTACTO PERSONALIZADO -->
                                      <?php if ($Solicitud->sino_ocultar_info_contacto == 'SI' AND trim($Solicitud->rtf_info_contacto_personalizada) <> '') { ?>
                                        <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h3 class="panel-title"><?php echo __('Contacto') ?></h3>
                                          </div>

                                          <!-- INFO DATOS PC -->
                                            <div class="panel-body" style="text-align: left;">
                                              <?php echo $Solicitud->rtf_info_contacto_personalizada ?>
                                            </div>
                                          <!-- INFO DATOS MOBILE -->

                                        </div>
                                      <?php } ?>
                                    <!-- PANEL DATOS CONTACTO PERSONALIZADO -->



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
            },

            cambiarCodigoTelPais() {
              var paises = new Array();
              <?php foreach ($Paises as $Pais) { ?>
                paises[<?php echo $Pais->id ?>] = "<?php echo $Pais->mnemo ?>"
              <?php } ?>
              iti.setCountry(paises[this.pais_id])
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



</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->
