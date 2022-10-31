<?php
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController();

$idioma = $Solicitud->idioma->mnemo;
App::setLocale($idioma); 

function sino_a_tf($sino) {
  if ($sino == 'SI') {
    $tf = 'true';
  }
  else {
    if ($sino == 'NO') {
        $tf = 'false';
      }
    else {
        $tf = 'null';
      }
  }

  return $tf;
}

$enviar_mail = 'false';
if (!Auth::guest()) {
    if(Auth::user()->id == 1 or Auth::user()->id == 33) {
      $enviar_mail = 'true';
    }
}

if ($Idioma_por_pais <> NULL) {
  $nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;
  $denominacion_de_voucher = $Idioma_por_pais->denominacion_de_voucher;
  $contesto_consulta = $Idioma_por_pais->Modelo_de_mensaje->envio_de_respuesta_a_consulta;
  $habilitar_invitacion_al_curso_online = $Idioma_por_pais->sino_habilitar_invitacion_al_curso_online;
}
else {
  $nombre_de_la_institucion = NULL;
  $denominacion_de_voucher = NULL;
  $contesto_consulta = NULL;
  $habilitar_invitacion_al_curso_online = NULL;  
}

$tel_responsable_inscripcion = $Solicitud->celular_responsable_de_inscripciones;
$nombre_de_ciudad = $Solicitud->localidad_nombre();
$nombre_responsable_de_inscripciones = $Solicitud->nombre_responsable_de_inscripciones;
$tipo_de_evento_id = $Solicitud->tipo_de_evento_id;
$tipo_de_evento = __($Solicitud->tipo_de_evento->tipo_de_evento);

if ($Idioma_por_pais->pais_id > 0) {
  $codigo_tel = $Idioma_por_pais->pais->codigo_tel;
}
else {
  $codigo_tel = '';
}

$fecha_de_solicitud = date_create($Solicitud->fecha_de_solicitud);
$now = date_create();
$interval = $fecha_de_solicitud->diff($now);
$cant_dias = $interval->format('%a');


$mensaje_mo = __('Quisiera hacer este curso de forma online');

if ($Solicitud->tipo_de_evento_id <> 3 or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4)) {
  $mostrar_fechas = 'true';
  $mensaje_np = __('No puedo asistir a este horario pero quisiera me contacten mas adelante por otros días y horarios');
  $estado_pedido_de_contacto = __('Pedido de confirmación enviado');
  $estado_sin_pedido_de_contacto = __('Inscripto sin pedido de confirmación');
}
else {
  $mostrar_fechas = 'false';
  $mensaje_np = '';
  $estado_pedido_de_contacto = __('Mensaje de bienvenida enviado');
  $estado_sin_pedido_de_contacto = __('Inscripto sin contactar');
}

if ($Grupos <> null) {
  $cant_total_inscriptos = $Grupos['cant_total_inscriptos'];
}
else {
  $cant_total_inscriptos = 0;  
}


if ($Grupos <> null) {
  $cant_total_inscriptos = $Grupos['cant_total_inscriptos'];
}
else {
  $cant_total_inscriptos = 0;  
}

$grupo = null;
if (isset($nro_de_grupo)) {
  $grupo = $nro_de_grupo; 
}

if (!isset($criterio)) {
  $criterio = '';
}

$url_envio_de_motivacion_2 = '';
$url_envio_de_motivacion_3 = '';

?>
<!DOCTYPE html>
<html>
    <head>
  
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-46601315-3"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'UA-46601315-3');
        </script>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php 
        $localidad_text = '';
        if ($Solicitud->localidad <> '') { 
          $localidad_text = $Solicitud->localidad->localidad;
        }
        ?>
        <title><?php echo __('Lista de Inscriptos') ?> |  {{ __($Solicitud->tipo_de_evento->tipo_de_evento) }} {{ $localidad_text }}</title>

      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      <!-- Bootstrap 3.3.7 -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/font-awesome/css/font-awesome.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/Ionicons/css/ionicons.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>dist/css/AdminLTE.min.css">
      <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>dist/css/skins/_all-skins.min.css">
      <!-- Morris chart -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/morris.js/morris.css">
      <!-- jvectormap -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/jvectormap/jquery-jvectormap.css">
      <!-- Date Picker -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
      <!-- Daterange picker -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
      <!-- bootstrap wysihtml5 - text editor -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      
      <!-- DataTables -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


      <!-- Google Font -->
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

      <!-- jQuery 3 -->
      <script src="<?php echo $dominio_publico ?>bower_components/jquery/dist/jquery.min.js"></script>

      <link rel="stylesheet" href="<?php echo $dominio_publico ?>css/generic.css">
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>css/style.css">

    <script src="<?php echo $dominio_publico ?>js/vue/vue.js"></script>
    <script src="<?php echo $dominio_publico ?>js/vee-validate/dist/vee-validate.js"></script>
    <script src="<?php echo $dominio_publico ?>js/vee-validate/dist/locale/es.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $dominio_publico ?>js/vue-form-generator/vfg.css">

    <style type="text/css">
      .btn-default.active, .btn-default:active, .open>.dropdown-toggle.btn-default {
          color: #fff;
          background-color: #919191;
          border-color: #919191;
      }
      .table-striped>tbody>tr:nth-of-type(odd) {
        background-color: #efefef;
      }
    </style>

    </head>
    <body style="overflow-x: auto;"> 

    <!-- INICIO app-lista -->    
    <div id="app-lista">
      <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo __('Lista de Inscritpos') ?> <?php echo $Solicitud->descrip_modelo(); ?></h3>
              <p class="bg-info">
                <select v-model="select_fechas_de_eventos" v-show="mostrar_fechas">
                  <option v-for="fecha_de_evento in fechas_de_evento" v-bind:value="fecha_de_evento.id">
                    @{{ fecha_de_evento.detalle }}
                  </option>
                </select>
              <strong> Totales:</strong> 
              <?php echo __('Inscriptos') ?> @{{ cant_inscriptos }} | 
              <?php echo __('Contactados') ?> @{{ cant_contactados }} | 
              <?php echo __('Cancelados') ?> @{{ cant_cancelados }} | 
              <span v-show="mostrar_fechas">
                <?php echo __('Confirmados') ?> @{{ cant_confirmados }} | 
                <?php echo __('Voucher') ?> @{{ cant_voucher }} | 
                <?php echo __('Recordatorio') ?> @{{ cant_recordatorio }} | 
                <?php echo __('Asistentes') ?> @{{ cant_asistentes }} |
              </span>
            </p>





            <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">  
              <a href="<?php echo $Solicitud->url_planilla_contactos_historicos() ?>" target="_blank">
                <div class="btn btn-block btn-social btn-danger" target="_blank" style="margin-top: 10px;">
                  <i class="fa fa-calendar"></i> 
                <span class="hidden-xs"><?php echo __('Invitar a Contactos Históricos') ?></span>
                <span class="hidden-lg hidden-sm hidden-md"><?php echo __('Histórico') ?></span>
                </div>     
              </a>         
            </div>

            <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">            
              <?php if ($Solicitud->tipo_de_evento_id == 3) { ?>
                <a data-toggle="modal" data-target="#modal-mensajes-modelos" class="btn btn-block btn-info" style="margin-top: 10px;">
                    <i class="fa fa-file-text-o"></i> <?php echo __('Modelos de Mensajes') ?>
                </a>
              <?php }
              else { ?>
               <a href="<?php echo ENV('PATH_PUBLIC') ?>mostrar-flyers/<?php echo $Solicitud->id ?>" class="btn btn-block btn-social btn-instagram" style="margin-top: 10px;" target="_blank">
                    <i class="fa fa-instagram"></i> <?php echo __('Flyers') ?>
                </a>
              <?php } ?>              
            </div>
            <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">  
              <div class="btn btn-block btn-social btn-instagram" data-toggle="modal" data-target="#modal-downcontactos" target="_blank" style="margin-top: 10px;">
                <i class="fa fa-mobile"></i> 
                <span class="hidden-xs"><?php echo __('Descargar contactos') ?></span>
                <span class="hidden-lg hidden-sm hidden-md"><?php echo __('Contactos') ?></span>
              </div>              
            </div>
            <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">  
              <a href="<?php echo $Solicitud->url_planilla_asistencia($grupo) ?>">
                <div class="btn btn-block btn-social btn-primary" target="_blank" style="margin-top: 10px;">
                  <i class="fa fa-check-square"></i> 
                <span class="hidden-xs"><?php echo __('Planilla de Asistencia') ?></span>
                <span class="hidden-lg hidden-sm hidden-md"><?php echo __('Asistencia') ?></span>
                </div>     
              </a>         
            </div>
            <?php 
            if (isset($nro_de_grupo)) { 
              $fecha_de_evento_id = 'G'.$nro_de_grupo;
            }
            else {
              $fecha_de_evento_id = 0;
            }
            ?>
            <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">  
              <a href="<?php echo $Solicitud->url_planilla_inscripcion_excel($fecha_de_evento_id) ?>">
                <div class="btn btn-block btn-social btn-success" target="_blank" style="margin-top: 10px;">
                  <i class="fa fa-file-excel-o"></i> 
                <span class="hidden-xs"><?php echo __('Descargar') ?> <?php echo __('Asistencia') ?></span>
                <span class="hidden-lg hidden-sm hidden-md"><?php echo __('Asistencia') ?></span>
                </div>     
              </a>         
            </div>

              <?php if (isset($Mensaje_limit) and $Mensaje_limit <> '') { ?>
                <div class="col-xs-12 col-lg-12">
                  <br>
                  <div class="alert alert-success  alert-dismissible">
                    <h4><i class="icon fa fa-files-o"></i> <?php echo $Mensaje_limit ?></h4>  
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo __('Páginas') ?>: 
                    <div class="btn-group">
                      <?php 
                      if (!isset($pagina_actual)) {
                        $pagina_actual = 1;
                      }
                      $j=0;
                      for ($i=$cant_paginas; $i>=1; $i--) {
                        $j++;
                        if ($j == $pagina_actual) {
                          $class_active = 'active';
                        }
                        else {
                          $class_active = '';
                        }
                      ?>
                      <a href="<?php echo $dominio_publico ?>f/ipaginar/<?php echo $Solicitud->id ?>/<?php echo $Solicitud->hash ?>/<?php echo $j ?>/0">
                        <button type="button" class="btn btn-default <?php echo $class_active ?>"><?php echo $i ?></button>
                      </a>
                      <?php } ?>
                    </div>
                    <?php 
                    if ($pagina_actual == 'all') {
                      $class_active = 'active';
                    }
                    else {
                      $class_active = '';
                    }
                    ?>
                    <a href="<?php echo $dominio_publico ?>f/ipaginar/<?php echo $Solicitud->id ?>/<?php echo $Solicitud->hash ?>/all/0">
                      <button type="button" class="btn btn-default <?php echo $class_active ?>"><?php echo __('Ver todos') ?></button>
                    </a>

                    



                  </div>   
                </div>      
              <?php } ?>
              
              <?php if (!isset($nro_de_grupo)) { ?>
                <div class="col-xs-12 col-lg-12">
                  {!! Form::open(array
                    (
                    'url' => $dominio_publico.'f/ibuscar/'.$Solicitud->id.'/'.$Solicitud->hash, 
                    'role' => 'form',
                    'method' => 'POST',
                    'id' => "form_gen_modelo",
                    'enctype' => 'multipart/form-data',
                    'class' => 'form-group',
                    'ref' => 'form'
                    )) 
                  !!}   
                    <div class="input-group">
                      <input type="text" name="criterio" placeholder="Indique el nombre, apellido, ID, ciudad, país, codigo de alumno, email o celular para filtrar" class="form-control" v-model="criterio">
                      <input type="hidden" name="solicitud_id" value="<?php echo $Solicitud->id ?> ">
                      <input type="hidden" name="hash" value="<?php echo $Solicitud->hash ?>">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary btn-flat">Buscar en toda la planilla</button>
                      </span>
                    </div>
                  {!! Form::close() !!} 
                </div>
              <?php } ?>

            <?php 
            if ($Solicitud->tipo_de_evento_id <> 3 or $Solicitud->tipo_de_curso_online_id == 4) {
              $comoVas = $Solicitud->comoVas($Fechas_de_evento);            
              $mensajes = $comoVas['mensajes'];
              $cant_mensajes = count($mensajes);
              $alertas = $comoVas['alertas'];
              $cant_alertas = count($alertas);
            ?>

              <?php 
              if ($cant_mensajes > 0) { 
                if ($cant_mensajes > 1) { ?>

              <div class="col-xs-12 col-lg-12">
                <br>
                <div class="alert alert-danger alert-dismissible">
                  <h4><i class="icon fa fa-question"></i> Como vas</h4>  
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <ul>   
                    <?php
                    foreach ($mensajes as $cv_mensaje) {
                      echo '<li style="padding-top: 10px">'.$cv_mensaje.'</li>';
                    }
                    ?>                  
                  </ul>                
                </div>  
              </div>      

              <?php 
                } 
              else {
              ?>
              <div class="col-xs-12 col-lg-12 alert alert-danger alert-dismissible">
                <h4> <?php echo $mensajes[0] ?> </h4>  
              </div>
              <?php
                }
              }
              ?>

              <?php if ($cant_alertas > 0) { ?>

              <div class="col-xs-12 col-lg-12">
                <br>
                <div class="alert alert-danger alert-dismissible">
                  <h4><i class="icon fa fa-bell-o"></i> Alertas</h4>  
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <ul>   
                    <?php
                    foreach ($alertas as $cv_alerta) {
                      echo "<li>$cv_alerta</li>";
                    }
                    ?>                  
                  </ul>                
                </div>  
              </div>      

              <?php } 
              }
              ?>



            
            <div class="col-xs-12 col-lg-12" style="margin-top: 10px; ">

              <div class="box box-warning collapsed-box box-solid" v-show="lista_de_advertencias.length > 0">
                <div class="box-header with-border" data-widget="collapse" style="cursor: pointer;">
                  <span data-toggle="tooltip" title="" class="badge" style="background-color: rgb(170, 108, 11); color: #FFF">@{{lista_de_advertencias.length}}</span>
                  <h3 class="box-title"><?php echo __('Advertencias') ?>: </h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool"><i class="fa fa-plus"></i>
                    </button>
                  </div>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">                    
                  <ul>
                    <li v-for="advertencia in verificar_advertencias"><span v-html="advertencia"></span></li>
                  </ul> 
                </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->

                <div v-show="lista_de_advertencias.length == 0" class="col-xs-12 col-lg-12 alert alert-success alert-dismissible">
                  <i class="fa fa-thumbs-o-up"></i> <?php echo __('Hasta ahora vas muy bien') ?></span>
                </div>

            </div>



              
            <div class="col-xs-6 col-lg-2" style="margin-top: 10px; "> 
              <select v-model="valor_select_ver" v-on:change="filtrar_tabla()" class="form-control">
                <option v-for="select in select_ver" v-bind:value="select.id">
                  @{{ select.detalle }}
                </option>
              </select>
            </div>

            <?php if (!isset($nro_de_grupo)) { ?>
            <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">  
              <div class="btn btn-block btn-social btn-success" data-toggle="modal" data-target="#modal-grupos-de-whatsapp" target="_blank" style="margin-top: 10px;">
                <i class="fa fa-whatsapp"></i> 
              <span class="hidden-xs"><?php echo __('Filtrar por Grupo de WhatsApp') ?></span>
              <span class="hidden-lg hidden-sm hidden-md"><?php echo __('Filtrar por Grupo') ?></span>
              </div>     
            </div>
            <?php } ?>
              
            <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">  
              <div class="btn btn-block btn-social btn-instagram" data-toggle="modal" data-target="#modal-mensaje-extra" target="_blank" style="margin-top: 10px;">
                <i class="fa fa-file-text"></i> 
                <span class="hidden-xs"><?php echo __('Mensaje Extra') ?></span>
                <span class="hidden-lg hidden-sm hidden-md"><?php echo __('Mensaje Extra') ?></span>
              </div>              
            </div>


              <div class="col-xs-12 col-lg-12" v-show="mostrar_supero_cupo()">
                <br>
                <div class="alert alert-danger alert-dismissible">
                  <h4><i class="icon fa fa-users"></i> Cupos Excedidos</h4>  
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <ul>   
                    <li v-for="fecha_de_evento in fechas_de_evento" v-show="fecha_de_evento.detalle != 'No pueden asistir' && fecha_de_evento.detalle != 'Todos' && supero_cupo(fecha_de_evento.id)[0]">
                      <strong>@{{ fecha_de_evento.detalle }}</strong> <br> @{{ supero_cupo(fecha_de_evento.id)[1] }} <br><br>
                    </li>
                  </ul>
                </div>
              </div>

              
              <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">  
                <div class="btn btn-block btn-social btn-instagram" data-toggle="modal" data-target="#modal-columnas" target="_blank" style="margin-top: 10px;">
                  <i class="fa fa-columns"></i> 
                  <span class="hidden-xs"><?php echo __('Habilitar columnas') ?></span>
                  <span class="hidden-lg hidden-sm hidden-md"><?php echo __('Columnas') ?></span>
                </div>              
              </div>
              
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              

              <table id="table" class="table table-bordered table-striped" style="max-width: 500px" >
                  <thead>
                      <tr>
                          <th v-show="show_col_id"><?php echo __('ID') ?></th>
                          <th v-show="show_col_nro_orden"><?php echo __('Nro de Orden') ?></th>
                          <?php if ($tipo_de_evento_id == 3) { ?>
                          <th v-show="show_col_ciudad"><?php echo __('Ciudad') ?></th>
                          <?php } ?>
                          <th v-show="show_col_grupo"><?php echo __('Grupo de whatsapp') ?></th>
                          <th v-show="show_col_prioridad"><?php echo __('Prioridad') ?></th>
                          <th v-show="show_col_comprimido"><?php echo __('Datos') ?></th>
                          <th v-show="show_col_fecha"><?php echo __('Fecha') ?></th>
                          <th v-show="show_col_apellido"><?php echo __('Apellido') ?></th>
                          <th v-show="show_col_nombre"><?php echo __('Nombre') ?></th>
                          <th v-show="show_col_celular"><?php echo __('Celular') ?></th>
                          <th v-show="show_col_celular"></th>
                          <th v-show="show_col_email_correo"><?php echo __('Correo') ?></th>
                          <th v-show="show_col_fecha_de_evento && mostrar_fechas"><?php echo __('Horario') ?></th>
                          <?php if ($tipo_de_evento_id == 3) { ?>
                          <th v-show="show_col_pais"><?php echo __('Pais') ?></th>
                          <?php } ?>
                          <th><?php echo __('Acción') ?></th>
                          <th v-show="show_col_estado"><?php echo __('Estado') ?></th>
                          <th v-show="mensaje_extra != ''"><?php echo __('Mensaje Extra') ?></th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php 
                    $i = -1;
                    foreach ($Inscripciones as $nro_de_orden => $Inscripcion) { 
                      $fecha_de_evento = null;
                      $i++;
                      $nombre = str_replace(array("\n", "\t", "\r"), '', str_replace("'", '’', htmlentities($Inscripcion->nombre)));
                      $apellido = str_replace("'", '’', htmlentities($Inscripcion->apellido));

                      if ($Inscripcion->fecha_de_evento_id > 0) {
                        $fecha_de_evento = null;
                        foreach ($Fechas_de_evento as $fecha_de_evento_iterar) {
                          if ($fecha_de_evento_iterar->id == $Inscripcion->fecha_de_evento_id) {
                            $fecha_de_evento = $fecha_de_evento_iterar;
                          }
                        }
                        $url_whatsapp = $Inscripcion->url_whatsapp($nombre_de_la_institucion, $Inscripcion->celular_responsable_de_inscripciones, $Inscripcion->nombre_responsable_de_inscripciones, $nombre_de_ciudad, $denominacion_de_voucher, $tipo_de_evento_id, $tipo_de_evento, $codigo_tel, $contesto_consulta, $Idioma_por_pais, $Solicitud, $idioma, $fecha_de_evento, $Inscripcion->cant_encuestas);
                        $url_pedido_de_confirmacion = $url_whatsapp['pedido_de_confirmacion'];
                        $url_sms_pedido_de_confirmacion = $url_whatsapp['sms_pedido_de_confirmacion'];
                        $url_no_respondieron_al_pedido_de_confirmacion = $url_whatsapp['no_respondieron_al_pedido_de_confirmacion'];                      
                        $url_sms_no_respondieron_al_pedido_de_confirmacion = $url_whatsapp['sms_no_respondieron_al_pedido_de_confirmacion'];                      
                        $url_envio_de_voucher = $url_whatsapp['envio_de_voucher'];
                        $url_sms_envio_de_voucher = $url_whatsapp['sms_envio_de_voucher'];
                        $url_envio_de_motivacion = $url_whatsapp['envio_de_motivacion'];
                        $url_sms_envio_de_motivacion = $url_whatsapp['sms_envio_de_motivacion'];
                        $url_envio_de_motivacion_2 = $url_whatsapp['envio_de_motivacion_2'];
                        $url_sms_envio_de_motivacion_2 = $url_whatsapp['sms_envio_de_motivacion_2'];
                        $url_envio_de_motivacion_3 = $url_whatsapp['envio_de_motivacion_3'];
                        $url_sms_envio_de_motivacion_3 = $url_whatsapp['sms_envio_de_motivacion_3'];
                        $url_envio_de_recordatorio = $url_whatsapp['envio_de_recordatorio'];
                        $url_sms_envio_de_recordatorio = $url_whatsapp['sms_envio_de_recordatorio'];
                        $url_contesto_consulta = $url_whatsapp['contesto_consulta'];
                        $url_sms_contesto_consulta = $url_whatsapp['sms_contesto_consulta'];
                        $url_envio_de_recordatorio_prox_clase = $url_whatsapp['envio_de_recordatorio_prox_clase'];
                        $url_sms_envio_de_recordatorio_prox_clase = $url_whatsapp['sms_envio_de_recordatorio_prox_clase'];
                        $url_envio_de_recordatorio_prox_clase_no_asistente = $url_whatsapp['envio_de_recordatorio_prox_clase_no_asistente'];
                        $url_sms_envio_de_recordatorio_prox_clase_no_asistente = $url_whatsapp['sms_envio_de_recordatorio_prox_clase_no_asistente'];
                        $url_envio_de_texto_encuesta_satisfaccion = $url_whatsapp['envio_de_texto_encuesta_satisfaccion'];
                        $url_sms_envio_de_texto_encuesta_satisfaccion = $url_whatsapp['sms_envio_de_texto_encuesta_satisfaccion'];                        
                        $url_envio_de_invitacion_al_curso_online = $url_whatsapp['envio_de_invitacion_al_curso_online'];
                        $url_sms_envio_de_invitacion_al_curso_online = $url_whatsapp['sms_envio_de_invitacion_al_curso_online'];
                        $url_envio_de_certificado = $url_whatsapp['envio_de_certificado'];
                        $url_sms_envio_de_certificado = $url_whatsapp['sms_envio_de_certificado'];
                      }
                      else {
                        $url_whatsapp = $Inscripcion->url_whatsapp_sin_evento($nombre_de_la_institucion, $Inscripcion->celular_responsable_de_inscripciones, $Inscripcion->nombre_responsable_de_inscripciones, $nombre_de_ciudad, $denominacion_de_voucher, $tipo_de_evento_id, $tipo_de_evento, $codigo_tel, $contesto_consulta, $Idioma_por_pais, $Solicitud);
                        $url_pedido_de_confirmacion = $url_whatsapp['pedido_de_confirmacion'];
                        $url_sms_pedido_de_confirmacion = $url_whatsapp['sms_pedido_de_confirmacion'];
                        $url_contesto_consulta = $url_whatsapp['contesto_consulta'];
                        $url_sms_contesto_consulta = $url_whatsapp['sms_contesto_consulta'];
                        $url_envio_de_invitacion_al_curso_online = $url_whatsapp['envio_de_invitacion_al_curso_online'];
                        $url_sms_envio_de_invitacion_al_curso_online = $url_whatsapp['sms_envio_de_invitacion_al_curso_online'];
                        $url_envio_de_recordatorio = $url_whatsapp['envio_de_recordatorio'];
                        $url_sms_envio_de_recordatorio = $url_whatsapp['sms_envio_de_recordatorio'];
                        $url_envio_de_certificado = $url_whatsapp['envio_de_certificado'];
                        $url_sms_envio_de_certificado = $url_whatsapp['sms_envio_de_certificado'];
                      }

                      $fecha_de_inicio = $Solicitud->fecha_de_inicio;

                      if ($Inscripcion->solicitud_id <> $Inscripcion->solicitud_original and $Inscripcion->solicitud_original == $Solicitud->id and ($Inscripcion->causa_de_cambio_de_solicitud_id == 1 or $Inscripcion->causa_de_cambio_de_solicitud_id == 4)) {
                        $promocionado = true;
                        $forzado = false;
                        if ($Inscripcion->causa_de_cambio_de_solicitud_id == 4) {
                          $forzado = true;
                        }
                      }
                      else {
                        $promocionado = false;  
                        $forzado = false;
                      }

                      if ($Inscripcion->cant_lecciones > 0) {
                        $cant_asistencias = $Inscripcion->cant_lecciones;
                      }
                      else {
                        $cant_asistencias = $Inscripcion->cant_asistencias;
                      }
                      
             
                      ?>

                        <tr v-show="mostrarFila(<?php echo $i ?>)" v-bind:style="class_promocionado(estados[<?php echo $i ?>].promocionado)">
                            <td v-show="show_col_id"><?php echo $Inscripcion->id; ?></td>
                            <td v-show="show_col_nro_orden"><?php echo $nro_de_orden+1; ?></td>
                            <?php if ($tipo_de_evento_id == 3) { ?>
                            <td v-show="show_col_ciudad"><?php echo $Inscripcion->ciudad; ?></td>
                            <?php } ?>
                            <td v-show="show_col_grupo"><?php echo $Inscripcion->grupo; ?></td>
                            <td v-show="show_col_prioridad">{{ calc_prioridad(<?php echo $i ?>) }}</td>
                            <td v-show="show_col_comprimido">
                              <div class="btn-group" style="float: right;">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                  <span class="caret"></span>
                                  <?php echo __('Acciones') ?>
                                  <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                  <?php if (!$promocionado) { ?>
                                    <li data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_inscripcion('Inscripcion', 'm', <?php echo $Inscripcion->id ?>)" style="padding: 5px; cursor: pointer;">
                                      <a><?php echo __('Modificar') ?> <?php echo __('Datos') ?></a>
                                    </li>
                                    <li data-toggle="modal" data-target="#modal-fecha-de-evento" onclick="$('#inscripcion_id_modificar_fecha').val(<?php echo $Inscripcion->id ?>);" style="padding: 5px; cursor: pointer;"><a><?php echo __('Modificar') ?> <?php echo __('Fecha') ?></a>
                                    </li>
                                      <li data-toggle="modal" data-target="#modal-cambio-de-solicitud" onclick="$('#inscripcion_id_modificar').val(<?php echo $Inscripcion->id ?>);" style="padding: 5px; cursor: pointer;">
                                        <a><?php echo __('Modificar') ?> <?php echo __('Solicitud') ?></a>
                                      </li>
                                      <?php if ($tipo_de_evento_id == 3 and $tipo_de_evento_id == 3) { ?>
                                        <!--li style="padding: 5px; cursor: pointer;">
                                          <a href="<?php echo $dominio_publico ?>forzar-promocion/<?php echo $Inscripcion->id ?>"><?php echo __('Promocionar a Cámara Avanzada') ?></a>
                                        </li-->
                                      <?php } ?>
                                  <?php } ?>
                                  <li>
                                    <a href="<?php echo $dominio_publico ?>f/contactDown/<?php echo $Solicitud->id; ?>/inscripcion/<?php echo $Inscripcion->id; ?>/1/1/<?php echo $Solicitud->hash; ?>" target="_blank">
                                      <?php echo __('Agendar Contacto vCard') ?>
                                    </a>
                                  </li>
                                </ul>
                              </div>


                             
                                                       


                              ID: <?php echo $Inscripcion->id ?> | <?php echo __('Fecha').': '.$gCont->FormatoFechayYHora($Inscripcion->created_at); ?> <br>                              
                              <?php echo __('Codigo del alumno') ?>: <?php echo $Inscripcion->codigo_alumno ?><br>
                              <strong style="font-size: 17px"><?php echo $nombre; ?> <?php echo $apellido; ?></strong>  | <?php echo $Inscripcion->email_correo; ?><br>
                              <?php echo __('Celular').': '; ?> 
                              <?php if (!$promocionado) { ?>
                                <input type="text" v-model="estados[<?php echo $i ?>].celular"> 
                                <button type="button" class="btn btn-primary btn-xs" v-on:click="guardarCel(<?php echo $i ?>)"><i class="fa fa-fw fa-save" style="font-size: 19px"></i></button>  
                                <a href="https://api.whatsapp.com/send?phone=<?php echo $Inscripcion->celular_wa($codigo_tel); ?>" target="_blank">
                                  <button type="button" class="btn btn-success btn-xs"><i class="fa fa-fw fa-whatsapp" style="font-size: 19px"></i></button>
                                </a>
                                <span v-html="estados[<?php echo $i ?>].celular_status_save"></span>


                              <?php 
                                } 
                              else { 
                                echo $Inscripcion->celular; 
                              }
                              ?>
                              <br>
                              <?php if ($tipo_de_evento_id == 3) { ?>
                                <?php echo __('Pais') ?>: <?php echo $Inscripcion->nombre_pais; ?> | <?php echo __('Ciudad') ?>: <?php echo $Inscripcion->ciudad; ?><br>
                              <?php } ?>
                              <i>
                              <?php 
                              if ($Inscripcion->fecha_de_evento_id > 0) {
                                echo $Inscripcion->fecha_de_evento->armarDetalleFechasDeEventos('html', true, $Idioma_por_pais, $Solicitud, $idioma).'<br>'; 
                              }
                              else {
                                if ($Solicitud->tipo_de_evento_id <> 3 or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4)) {
                                  if ($Inscripcion->sino_eleccion_modalidad_online == 'SI') {
                                    echo $mensaje_mo.'<br>';
                                  }
                                  else {
                                    echo $mensaje_np.'<br>';
                                  }
                                }
                              }
                              ?>  
                            </i>                               
                                                    
                            <?php if ($Inscripcion->canal_de_recepcion_del_curso <> '') { ?>
                              <?php echo __('En que app te gustaria recibir el curso') ?>: <?php echo $Inscripcion->canal_de_recepcion_del_curso ?>                        
                            <br>     
                            <?php } ?>
                            <strong><?php echo __('Cantidad') ?> <?php echo __('Asistencias') ?>: <?php echo $cant_asistencias ?><br>
                            <?php if ($Inscripcion->nombre_de_la_leccion <> '' or $Inscripcion->titulo <> '') { ?>                              
                              <strong>
                                <?php 
                                echo __('Ultima leccion vista').': ';
                                if ($Inscripcion->nombre_de_la_leccion <> '') {
                                  echo '('.$Inscripcion->codigo_de_la_leccion.')'.$Inscripcion->nombre_de_la_leccion;
                                }
                                else {
                                  echo '('.$Inscripcion->nro_o_codigo.')'.$Inscripcion->titulo;

                                }
                                ?>                                
                              </strong>
                              <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-traer-lecciones" v-on:click="traerLeccionesVistas(<?php echo $Inscripcion->id ?>, '<?php echo md5(ENV('PREFIJO_HASH').$Inscripcion->id) ?>')">Ver todas</button>
                              <br>
                            <?php } ?>      
                            <?php if ($Inscripcion->titulo_de_la_evaluacion <> '') { ?>            
                              <p>                  
                                <strong><?php echo __('Ultimo TP') ?>: <?php echo $Inscripcion->titulo_de_la_evaluacion ?></strong>
                                <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-tp-realizados" v-on:click="traerTPRealizados(<?php echo $Inscripcion->id ?>, '<?php echo md5(ENV('PREFIJO_HASH').$Inscripcion->id) ?>')"><?php echo __('Ver mas') ?></button>
                              </p>
                            <?php } ?>     

                            <?php if (!$promocionado) { ?>
                              <div class="btn-group">
                                  <div class="btn-group">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                      <span class="caret"></span>
                                      <i class="fa fa-whatsapp"></i> <?php echo __('Grupo de whatsapp') ?>: {{ estados[<?php echo $i ?>].grupo }}
                                      <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                      <li v-for="grupo in grupos" style="padding: 5px; cursor: pointer;" v-on:click="guardarGrupo(<?php echo $i ?>, grupo.id)">
                                        <a><i class="fa fa-whatsapp"></i> @{{ grupo.grupo }}</a>
                                      </li>
                                    </ul>
                                  </div>
                              </div>
                              <span v-html="estados[<?php echo $i ?>].grupo_status_save"></span> 
                              <?php } ?>  

                              <?php if ($Inscripcion->consulta <> '') {?>
                                <p style="color: red; max-width: 400px"><strong><?php echo __('Consulta') ?>: <?php echo $Inscripcion->consulta ?></strong></p>
                              <?php } ?>
                              <br><br>
                                <p>
                                  <textarea id="observaciones" v-model="estados[<?php echo $i ?>].observaciones" rows="2"  name="observaciones" class="form-control" placeholder="<?php echo __('Observaciones') ?>" v-on:change="estados[<?php echo $i ?>].observaciones = codificarCadena(estados[<?php echo $i ?>].observaciones)" maxlength="255"></textarea>
                                  <button type="button" class="btn btn-primary btn-xs" v-on:click="guardarObs(<?php echo $i ?>)"><i class="fa fa-fw fa-save" style="font-size: 19px"></i> <?php echo __('Guardar') ?> <?php echo __('Observaciones') ?></button>  
                                  <span v-html="estados[<?php echo $i ?>].obs_status_save"></span>
                                </p>
                              
                              <?php if ($Solicitud->tipo_de_evento_id <> 3 or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4)) { ?>
                              <br>
                              <strong>Estado: {{ span_estado(<?php echo $i ?>) }}</strong>
                              <?php } ?>
                              <?php 
                              if ($Inscripcion->solicitud_original <> '' and $Inscripcion->solicitud_original <> $Solicitud->id) { 
                                echo '<br><br>'.$Inscripcion->planilla_original();
                              } 
                              ?>
                              <?php 
                              if ($Inscripcion->estado_de_seguimiento_id > 3) { 
                                $Instancias_de_seguimiento = $Inscripcion->instancias_de_seguimiento();
                              ?>
                                <!-----------------INICIO SEGUIMIENTO------------->
                                <br><br>
                                <div class="col-xs-12 col-lg-6">
                                  <div class="box box-primary direct-chat direct-chat-primary collapsed-box">
                                    <div class="box-header with-border">
                                      <h3 class="box-title"><?php echo __('Seguimientos del Equipo de Contacto') ?></h3>

                                      <div class="box-tools pull-right">
                                        <span data-toggle="tooltip" title="" class="badge bg-light-blue"><?php echo count($Instancias_de_seguimiento) ?></span>
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                        </button>
                                      </div>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                      <!-- Conversations are loaded here -->
                                      <div class="direct-chat-messages">
                                        <!-- Message. Default to the left -->
                                        <?php foreach ($Instancias_de_seguimiento as $Seguimiento) { ?>
                                          <div class="direct-chat-msg">
                                            <div class="direct-chat-info clearfix">
                                              <span class="direct-chat-name pull-left"><?php echo $Seguimiento->name ?></span>
                                              <span class="direct-chat-timestamp pull-right"><?php echo $gCont->FormatoFechayYHora($Seguimiento->created_at); ?></span>
                                            </div>
                                            <!-- /.direct-chat-info -->
                                            <?php 
                                              if ($Seguimiento->img_avatar <> '') {
                                                $img_avatar = $Seguimiento->img_avatar;
                                              }
                                              else {
                                                $img_avatar = env('PATH_PUBLIC').'img/avatar-sin-imagen.png';
                                              }                                            
                                            ?>
                                            <img class="direct-chat-img" src="<?php echo $img_avatar ?>" alt="<?php echo $Seguimiento->name ?>"><!-- /.direct-chat-img -->
                                            <div class="direct-chat-text">
                                              <?php echo $Seguimiento->estado_de_seguimiento.': '.$Seguimiento->observaciones ?>
                                            </div>
                                            <!-- /.direct-chat-text -->
                                          </div>
                                          <!-- /.direct-chat-msg -->
                                        <?php } ?>
                                        <!-- /.direct-chat-msg -->
                                      </div>
                                      <!--/.direct-chat-messages-->
                                    </div>
                                    <!-- /.box-body -->
                                  </div>
                                </div>
                                <!-----------------FIN SEGUIMIENTO------------->
                              <?php } ?>

                            </td>
                            <td v-show="show_col_fecha"><?php echo $gCont->FormatoFechayYHora($Inscripcion->created_at); ?></td>
                            <td v-show="show_col_apellido"><?php echo $apellido; ?></td>
                            <td v-show="show_col_nombre"><?php echo $nombre; ?></td>
                            <td v-show="show_col_celular"><?php echo $Inscripcion->celular; ?></td>
                            <td v-show="show_col_celular">                     
                                <a href="https://api.whatsapp.com/send?phone=<?php echo $Inscripcion->celular_wa($codigo_tel); ?>" target="_blank">
                                  <button type="button" class="btn btn-success btn-xs"><i class="fa fa-fw fa-whatsapp" style="font-size: 19px"></i></button>
                                </a>                    
                            </td>
                            <td v-show="show_col_email_correo"><?php echo $Inscripcion->email_correo; ?></td>
                            <td v-show="show_col_fecha_de_evento">
                              <?php 
                              if ($Inscripcion->fecha_de_evento_id > 0) {
                                echo $Inscripcion->fecha_de_evento->armarDetalleFechasDeEventos('html', true, $Idioma_por_pais, $Solicitud, $idioma); 
                              }
                              else {
                                if ($Solicitud->sino_eleccion_modalidad_online == 'SI') {
                                  echo $mensaje_mo.'<br>';
                                }
                                else {
                                  echo $mensaje_np.'<br>';
                                }
                              }
                              ?>    
                            </td>
                            <?php if ($tipo_de_evento_id == 3) { ?>
                            <td v-show="show_col_pais"><?php echo $Inscripcion->nombre_pais; ?></td>
                            <?php } ?>

                            
                            <td>

                            <?php if ($promocionado) { ?>
                              <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-check"></i> PROMOCIONADO A CAMARA AVANZADA! <?php if ($forzado) echo " (Forzada)"; ?></h4>
                                <h5><i class="icon fa fa-file-text-o"></i> <?php echo $Inscripcion->planilla_promocion() ?> </h5>
                              </div>
                            <?php } ?>

                            <?php if ($Inscripcion->fecha_de_evento_id > 0 and $tipo_de_evento_id <> 3) { ?>

                                    <!-- ENVIO DE PEDIDO DE CONFIRMACION -->
                                    <div v-show="!estados[<?php echo $i ?>].cancelo && !estados[<?php echo $i ?>].confirmo && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_pedido_de_confirmacion)">
                                      
                                        <a href="<?php echo $url_pedido_de_confirmacion; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 1, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                        </a>

                                        <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(1, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Pedido de confirmación') ?>', <?php echo $i ?>)">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                        </a>
                                      
                                        <a href="<?php echo $url_sms_pedido_de_confirmacion; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 1, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                        </a>

                                        <?php echo __('Pedido de confirmación') ?>

                                        <label class="switch switch-inscripcion">
                                          <input  type="checkbox" v-on:change="setearSino(1, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_pedido_de_confirmacion">
                                          <span class="slider round"></span>
                                          
                                        </label>
                                    </div>
                                  
                                    <!-- ENVIO DE RESPUESTA DE CONSULTA -->
                                    <div v-show="mostrar_responder_consulta(<?php echo $i ?>) && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].contesto_consulta)">
                                      
                                        <a href="<?php echo $url_contesto_consulta; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 7, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                        </a>

                                        <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(7, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Responder Consulta') ?>', <?php echo $i ?>)">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                        </a>
                                        
                                        <a href="<?php echo $url_sms_contesto_consulta; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 7, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                        </a>

                                        <?php echo __('Responder Consulta') ?>

                                        <label class="switch switch-inscripcion">
                                          <input  type="checkbox" v-on:change="setearSino(7, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].contesto_consulta">
                                          <span class="slider round"></span>
                                          
                                        </label>
                                    </div>
                                  
                                    <!-- ENVIO DE NUEVO PEDIDO DE CONFIRMACION -->
                                    <div v-show="estados[<?php echo $i ?>].envio_pedido_de_confirmacion && !estados[<?php echo $i ?>].confirmo && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_recordatorio_pedido_de_confirmacion)">
                                      
                                        <a href="<?php echo $url_no_respondieron_al_pedido_de_confirmacion; ?>" target="_blank">
                                            <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 2, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                        </a>

                                        <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(2, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Nuevo pedido de confirmación') ?>', <?php echo $i ?>)">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                        </a>

                                        <a href="<?php echo $url_sms_no_respondieron_al_pedido_de_confirmacion; ?>" target="_blank">
                                            <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 2, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phonr"></i> sms</button>
                                        </a> 

                                        <?php echo __('Nuevo pedido de confirmación') ?>

                                        <label class="switch switch-inscripcion">
                                          <input type="checkbox" v-on:change="setearSino(2, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_recordatorio_pedido_de_confirmacion">
                                          <span class="slider round"></span>
                                          
                                        </label>
                                    </div>
                                    
                                    <!-- SI CONFIRMO -->
                                    <div v-show="(estados[<?php echo $i ?>].envio_pedido_de_confirmacion || estados[<?php echo $i ?>].confirmo) && !estados[<?php echo $i ?>].envio_voucher && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].confirmo)">
                                      <?php echo __('Confirmado') ?>

                                        <label class="switch switch-inscripcion">
                                          <input type="checkbox" v-on:change="setearSino(3, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].confirmo">
                                          <span class="slider round"></span>
                                        </label>
                                      </div>
                                    
                                    <!-- ENVIO DE VOUCHER -->
                                    <div v-show="estados[<?php echo $i ?>].confirmo && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_voucher)">   
                                     
                                        <a href="<?php echo $url_envio_de_voucher; ?>" target="_blank">
                                            <button type="button" class="btn btn-blanco btn-md" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 4, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                        </a>

                                        <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(4, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de Voucher') ?>', <?php echo $i ?>)">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                        </a>

                                        <a href="<?php echo $url_sms_envio_de_voucher; ?>" target="_blank">
                                            <button type="button" class="btn btn-blanco btn-md" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 4, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                        </a>

                                        <?php echo __('Envio de Voucher') ?>

                                        <label class="switch switch-inscripcion">
                                          <input type="checkbox" v-on:change="setearSino(4, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_voucher">
                                          <span class="slider round"></span>
                                          
                                        </label>
                                    </div>
                                  
                                    <!-- ENVIO DE MOTIVACION 1 -->
                                    <div v-show="estados[<?php echo $i ?>].envio_voucher && estados[<?php echo $i ?>].confirmo && estados[<?php echo $i ?>].envio_pedido_de_confirmacion && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_motivacion)">
                                      
                                        <a href="<?php echo $url_envio_de_motivacion; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 5, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                        </a>

                                        <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(5, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de Motivacion') ?>', <?php echo $i ?>)">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                        </a>

                                      
                                        <a href="<?php echo $url_sms_envio_de_motivacion; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 5, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                        </a>

                                        <?php echo __('Envio de Motivacion') ?>

                                        <label class="switch switch-inscripcion">
                                          <input type="checkbox" v-on:change="setearSino(5, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_motivacion">
                                          <span class="slider round"></span>
                                          
                                        </label>
                                    </div>
                                  
                                    <!-- ENVIO DE MOTIVACION 2 -->
                                    <div v-show="estados[<?php echo $i ?>].envio_voucher && estados[<?php echo $i ?>].confirmo && estados[<?php echo $i ?>].envio_pedido_de_confirmacion && !estados[<?php echo $i ?>].cancelo && url_envio_de_motivacion_2 != ''" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_motivacion_2)">
                                      
                                        <a href="<?php echo $url_envio_de_motivacion_2; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 27, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                        </a>

                                        <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(27, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de Motivacion') ?>', <?php echo $i ?>)">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                        </a>

                                      
                                        <a href="<?php echo $url_sms_envio_de_motivacion_2; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 27, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                        </a>

                                        <?php echo __('Envio de Motivacion 2') ?>

                                        <label class="switch switch-inscripcion">
                                          <input type="checkbox" v-on:change="setearSino(27, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_motivacion_2">
                                          <span class="slider round"></span>
                                          
                                        </label>
                                    </div>
                                  
                                    <!-- ENVIO DE MOTIVACION 3 -->
                                    <div v-show="estados[<?php echo $i ?>].envio_voucher && estados[<?php echo $i ?>].confirmo && estados[<?php echo $i ?>].envio_pedido_de_confirmacion && !estados[<?php echo $i ?>].cancelo && url_envio_de_motivacion_3 != ''" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_motivacion_3)">
                                      
                                        <a href="<?php echo $url_envio_de_motivacion_3; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 28, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                        </a>

                                        <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(28, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de Motivacion') ?>', <?php echo $i ?>)">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                        </a>

                                      
                                        <a href="<?php echo $url_sms_envio_de_motivacion_3; ?>" target="_blank">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 28, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                        </a>

                                        <?php echo __('Envio de Motivacion 3') ?>

                                        <label class="switch switch-inscripcion">
                                          <input type="checkbox" v-on:change="setearSino(28, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_motivacion_3">
                                          <span class="slider round"></span>
                                          
                                        </label>
                                    </div>
                                    
                                    <!-- ENVIO DE RECORDATORIO -->
                                    <?php if ($Inscripcion->enviarRecordatorioHoy($fecha_de_evento)) { ?>                                     
                                      <div v-show="estados[<?php echo $i ?>].envio_voucher && estados[<?php echo $i ?>].confirmo && estados[<?php echo $i ?>].envio_pedido_de_confirmacion && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_recordatorio)">
                                        
                                          <a href="<?php echo $url_envio_de_recordatorio; ?>" target="_blank">
                                              <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 6, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                          </a>

                                          <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(6, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de Recordatorio') ?>', <?php echo $i ?>)">
                                            <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                          </a>

                                          <a href="<?php echo $url_sms_envio_de_recordatorio; ?>" target="_blank">
                                              <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 6, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                          </a>

                                          <?php echo __('Envio de Recordatorio') ?>

                                          <label class="switch switch-inscripcion">
                                            <input type="checkbox" v-on:change="setearSino(6, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_recordatorio">
                                            <span class="slider round"></span>
                                            
                                          </label>
                                      </div>
                                    <?php } ?>
                              
                                    <?php if ($tipo_de_evento_id == 1) { ?>
                                      <!-- ENVIO DE RECORDATORIO PROX CLASE -->
                                      <div v-show="estados[<?php echo $i ?>].asistio && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_recordatorio_proxima_clase)">
                                        
                                          <a href="<?php echo $url_envio_de_recordatorio_prox_clase; ?>" target="_blank">
                                              <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 9, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                          </a>

                                          <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(9, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de Recordatorio Próxima clase') ?>', <?php echo $i ?>)">
                                            <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                          </a>

                                          <a href="<?php echo $url_sms_envio_de_recordatorio_prox_clase; ?>" target="_blank">
                                              <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 9, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                          </a>

                                          <?php echo __('Envio de Recordatorio Próxima clase') ?>

                                          <label class="switch switch-inscripcion">
                                            <input type="checkbox" v-on:change="setearSino(9, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_recordatorio_proxima_clase">
                                            <span class="slider round"></span>
                                            
                                          </label>
                                        </div>
                                      <?php } ?>

                                  <?php if ($Inscripcion->enviarRecordatorioProxClase($fecha_de_evento) and $tipo_de_evento_id == 1) { ?>
                                    <!-- ENVIO DE RECORDATORIO PROX CLASE A NO ASISTENTE-->
                                    <div v-show="estados[<?php echo $i ?>].confirmo && !estados[<?php echo $i ?>].asistio && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_recordatorio_proxima_clase_a_no_asistente)">
                                      
                                        <a href="<?php echo $url_envio_de_recordatorio_prox_clase_no_asistente; ?>" target="_blank">
                                            <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 10, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                        </a>

                                        <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(10, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de Recordatorio a Próxima clase a no Asistente') ?>', <?php echo $i ?>)">
                                          <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                        </a>

                                        <a href="<?php echo $url_sms_envio_de_recordatorio_prox_clase_no_asistente; ?>" target="_blank">
                                            <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 10, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                        </a>

                                        <?php echo __('Envio de Recordatorio a Próxima clase a no Asistente') ?>

                                        <label class="switch switch-inscripcion">
                                          <input type="checkbox" v-on:change="setearSino(10, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_recordatorio_proxima_clase_a_no_asistente">
                                          <span class="slider round"></span>
                                          
                                        </label>
                                    </div>
                                  <?php } ?>

                                  <?php if ($Inscripcion->enviarRecordatorioProxClase($fecha_de_evento) and $tipo_de_evento_id == 2) { ?>
                                    <!-- ENVIO DE ENCUESTA-->
                                    <div v-show="estados[<?php echo $i ?>].confirmo && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_de_encuesta)">
                                      
                                        <a href="<?php echo $url_envio_de_texto_encuesta_satisfaccion; ?>" target="_blank">
                                            <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 29, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                        </a>

                                        <a href="<?php echo $url_sms_envio_de_texto_encuesta_satisfaccion; ?>" target="_blank">
                                            <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 29, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                        </a>

                                        <?php echo __('Envio de Encuesta') ?>

                                        <label class="switch switch-inscripcion">
                                          <input type="checkbox" v-on:change="setearSino(29, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_de_encuesta">
                                          <span class="slider round"></span>
                                          
                                        </label>
                                    </div>
                                  <?php } ?>

                            <?php }
                            else { ?>

                              <!-- ENVIO DE PEDIDO DE CONFIRMACION -->
                              <div v-show="!estados[<?php echo $i ?>].promocionado && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_pedido_de_confirmacion)">

                                  <a href="<?php echo $url_pedido_de_confirmacion; ?>" target="_blank">
                                    <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 1, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                  </a>

                                  <?php if ($tipo_de_evento_id <> 3) { ?>                                  
                                  <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(1, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Contactado') ?>', <?php echo $i ?>)">
                                    <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                  </a>
                                  <?php } ?>


                                  <a href="<?php echo $url_sms_pedido_de_confirmacion; ?>" target="_blank">
                                    <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 1, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                  </a>

                                  <?php 
                                  if ($tipo_de_evento_id == 3) {
                                    echo __('Enviar Mensaje de Bienvenida');
                                  }
                                  else {
                                    echo __('Contactado');
                                  }
                                  ?>

                                  <label class="switch switch-inscripcion">
                                    <input  type="checkbox" v-on:change="setearSino(1, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_pedido_de_confirmacion">
                                    <span class="slider round"></span>
                                    
                                  </label>
                              </div>  


                              <?php if ($tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 77) { ?> 
                              <!-- ENVIO DE RECORDATORIO -->                                    
                                <div v-bind:class="class_sino(estados[<?php echo $i ?>].envio_recordatorio)">
                                  
                                    <a href="<?php echo $url_envio_de_recordatorio; ?>" target="_blank">
                                        <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 6, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                    </a>

                                    <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(6, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de Recordatorio') ?>', <?php echo $i ?>)">
                                      <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                    </a>

                                    <a href="<?php echo $url_sms_envio_de_recordatorio; ?>" target="_blank">
                                        <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 6, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                    </a>

                                    <?php echo __('Envio de Recordatorio') ?>

                                    <label class="switch switch-inscripcion">
                                      <input type="checkbox" v-on:change="setearSino(6, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_recordatorio">
                                      <span class="slider round"></span>
                                      
                                    </label>
                                </div>
                              <?php } ?>

                                                                
                              <!-- ENVIO DE RESPUESTA DE CONSULTA -->
                              <div v-show="!estados[<?php echo $i ?>].promocionado && mostrar_responder_consulta(<?php echo $i ?>) && !estados[<?php echo $i ?>].cancelo" v-bind:class="class_sino(estados[<?php echo $i ?>].contesto_consulta)">
                                
                                  <a href="<?php echo $url_contesto_consulta; ?>" target="_blank">
                                    <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 7, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button> 
                                  </a>

                                  <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(7, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Responder Consulta') ?>', <?php echo $i ?>)">
                                    <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                  </a>

                                  <a href="<?php echo $url_sms_contesto_consulta; ?>" target="_blank">
                                    <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 7, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button> 
                                  </a>

                                  <?php echo __('Responder Consulta') ?>

                                  <label class="switch switch-inscripcion">
                                    <input  type="checkbox" v-on:change="setearSino(7, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].contesto_consulta">
                                    <span class="slider round"></span>
                                    
                                  </label>
                              </div>

                            <?php } ?>  

                              <?php if ($habilitar_invitacion_al_curso_online <> 'NO' and ($Inscripcion->enviarInvitacionCursoOnline($Solicitud, $fecha_de_evento) or ($cant_dias > 60 and $tipo_de_evento_id <> 3)) and $url_envio_de_invitacion_al_curso_online <> '') { ?>
                                <!-- ENVIO DE INVITACION A CURSO ONLINE-->
                                <div v-bind:class="class_sino(estados[<?php echo $i ?>].envio_invitacion_al_curso_online)">
                                  
                                    <a href="<?php echo $url_envio_de_invitacion_al_curso_online; ?>" target="_blank">
                                        <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 12, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                    </a>

                                    <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(12, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de invitación a Curso Online') ?>', <?php echo $i ?>)">
                                      <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                    </a>

                                    <a href="<?php echo $url_sms_envio_de_invitacion_al_curso_online; ?>" target="_blank">
                                        <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 12, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                    </a>

                                    <?php echo __('Envio de invitación a Curso Online') ?>

                                    <label class="switch switch-inscripcion">
                                      <input type="checkbox" v-on:change="setearSino(12, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_invitacion_al_curso_online">
                                      <span class="slider round"></span>
                                      
                                    </label>
                                </div>
                              <?php } ?>

                              <!-- ENVIO MODELOS EXTRA -->
                                <?php if (isset($Inscripcion->Modelos_extra) and !$promocionado) { ?>
                                  <div class="box box-primary box-solid collapsed-box">
                                    <div class="box-header with-border" data-widget="collapse">
                                      <h3 class="box-title"><i class="fa fa-whatsapp"></i> <?php echo __('Mas Mensajes') ?></h3>

                                      <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool"><i class="fa fa-plus"></i>
                                        </button>
                                      </div>
                                      <!-- /.box-tools -->
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body" style="display: none;">

                                      <?php                                     
                                        $k = 0;
                                        foreach ($Inscripcion->Modelos_extra as $Modelo_extra) {
                                          $k++;
                                      ?>

                                        <div v-bind:class="class_sino(estados[<?php echo $i ?>].envio_<?php echo $k; ?>)">
                                          
                                            <a href="<?php echo $Modelo_extra['url_del_mensaje']; ?>" target="_blank">
                                              <button type="button" class="btn btn-blanco" alt="editar" title="editar" v-on:click="marcar_envio(1, <?php echo $k+12; ?>, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                            </a>

                                            <?php echo $Modelo_extra['titulo_del_mensaje']; ?> <i class="fa fa-fw fa-info-circle box-tools" data-toggle="tooltip" title="" data-original-title="<?php echo $Modelo_extra['aclaracion']; ?>" style="font-size: 30px; padding: 10px; margin-top: -20px"></i> 


                                            <label class="switch switch-inscripcion">
                                              <input  type="checkbox" v-on:change="setearSino(<?php echo $k+12; ?>, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_<?php echo $k; ?>">
                                              <span class="slider round"></span>
                                              
                                            </label>
                                        </div>
                                      
                                      <?php
                                        }
                                      ?>        
                                    </div>
                                    <!-- /.box-body -->
                                  </div>
                                <?php } ?>
                              <!-- FIN ENVIO MODELOS EXTRA -->
                                                          
                                 

                              <!-- ENVIO DE CERTIFICADO -->
                              <div v-show="estados[<?php echo $i ?>].promocionado || estados[<?php echo $i ?>].certificado" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_certificado)">
                                
                                  <a href="<?php echo $url_envio_de_certificado; ?>" target="_blank">
                                    <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 24, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                  </a>

                                  <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(24, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Envio de certificado') ?>', <?php echo $i ?>)">
                                    <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                  </a>

                                  <a href="<?php echo $url_sms_envio_de_certificado; ?>" target="_blank">
                                    <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 24, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> sms</button>
                                  </a>

                                  <a href="<?php echo $Inscripcion->url_certificado() ?>" target="_blank"><img src="<?php echo $dominio_publico ?>img/certified.png" style="width: 30px; vertical-align: middle;"></a><?php echo __('Envio de certificado') ?>

                                  <label class="switch switch-inscripcion">
                                    <input  type="checkbox" v-on:change="setearSino(24, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].envio_certificado">
                                    <span class="slider round"></span>
                                    
                                  </label>
                              </div>

                              <!-- CANCELO  -->                              
                              <div v-show="!estados[<?php echo $i ?>].promocionado" v-bind:class="class_sino_cancelo(estados[<?php echo $i ?>].cancelo)" style="margin-top: 30px; padding-top: 0px; padding-bottom: 0px;">
                                <h4 style="color: white"><i class="icon fa fa-ban"></i> <?php echo __('Canceló la inscripción') ?>
                                  <label class="switch switch-inscripcion">
                                    <input  type="checkbox" v-on:change="setearSino(11, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].cancelo">
                                    <span class="slider round"></span>
                                    
                                  </label></h4>

                                  <div>
                                    <label><?php echo __('Causa de baja') ?>: </label>
                                    <select v-model="estados[<?php echo $i ?>].causa_de_baja_id" v-on:change="modificarCausaDeBaja(<?php echo $i ?>)" style="color: #000">
                                      <option v-for="causa_de_baja in causas_de_baja" v-bind:value="causa_de_baja.id">
                                        @{{ causa_de_baja.causa_de_baja }}
                                      </option>
                                    </select>      
                                     <span v-html="estados[<?php echo $i ?>].causa_de_baja_status_save"></span> 
                                  </div> 
                              </div>  
                                   
                            </td>
                            <td v-show="!estados[<?php echo $i ?>].promocionado && mensaje_extra != ''">

                              <!-- ENVIO DE MENSAJE EXTRA x WHATSAPP -->   
                                <p>          
                                  <a v-bind:href="url_mensa_extra('<?php echo $Inscripcion->celular_wa($codigo_tel, $Solicitud) ?>', '<?php echo $nombre; ?>', '<?php echo $apellido ?>', '<?php echo $Inscripcion->codigo_alumno ?>')" target="_blank">
                                    <button type="button" class="btn btn-success" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 23, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i> <?php echo __('Enviar via Whatsapp') ?></button>
                                  </a>
                                </p>
                                <p>          
                                  <a v-bind:href="url_sms_mensa_extra('<?php echo $Inscripcion->celular_wa($codigo_tel, $Solicitud) ?>', '<?php echo $nombre; ?>', '<?php echo $apellido ?>', '<?php echo $Inscripcion->codigo_alumno ?>')" target="_blank">
                                    <button type="button" class="btn btn-primary" alt="enviar" title="enviar" v-on:click="marcar_envio(3, 23, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-phone"></i> <?php echo __('Enviar via SMS') ?></button>
                                  </a>
                                </p>
                                <p>
                                  <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(23, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Mensaje') ?>: <?php echo $Solicitud->descripcion_sin_estado(); ?>', <?php echo $i ?>)">
                                    <button type="button" class="btn btn-warning" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i> <?php echo __('Enviar via Email') ?></button>
                                  </a>
                                </p>
                            </td>


                            <td v-show="show_col_estado"><!--span class="badge bg-light-blue datos-finales-asistente">{{ span_estado(<?php echo $i ?>) }}</span-->
                              {{ span_estado(<?php echo $i ?>) }}
                            </td>
                        </tr>
                    <?php } ?>
                  </tbody>
                  </table>


              <!-- MODAL MENSAJE EXTRA -->
                <div class="modal modal fade" id="modal-mensaje-extra">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Mensaje Extra') ?></div></h4>
                      </div>

                      <div class="modal-body" id="modal-bodi-mensaje-extra">
                        <textarea id="mensaje_extra" v-model="mensaje_extra" rows="6" name="mensaje_extra" class="form-control" placeholder="<?php echo __('Indique el mensaje personalizado que quiere enviar') ?>"></textarea>
                        <p>Indique inscrito_nombre para que aparezca el nombre de la persona y inscrito_apellido para su apellido, por ejemplo si el mensaje es: "Hola inscrito_nombre inscrito_apellido queremos recordarte asistir con ropa comoda para el taller de meditación" se traduciria como "Hola Jose Perez queremos recordarte asistir con ropa comoda para el taller de meditación"</p>
                      </div>

                      <div class="modal-footer">
                        <center>
                          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Aceptar') ?></button>
                        </center>  
                        <input type="hidden" name="sino_aprobado_administracion" value="NO">
                      </div>

                    </div>
                    <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
                </div>
              <!-- MODAL MENSAJE EXTRA -->


              <!-- MODAL CONFIRMAR MAIL -->
                <div class="modal modal fade" id="modal-confirmar-mail">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Confirme el envio del E-Mail') ?></div></h4>
                      </div>

                      <div class="modal-body">                        
                        <p><?php echo __('Nombre') ?>: @{{ email_nombre }}</p>
                        <p><?php echo __('Apellido') ?>: @{{ email_apellido }}</p>
                        <p><?php echo __('Acción') ?>: @{{ email_asunto }}</p>
                        <br>
                        <strong><?php echo __('El envio de Email debe ser usado solo con los contactos a los que no pueda enviar mensajes de WhatsApp') ?></strong>
                      </div>
                      <div  id="modal-bodi-confirmar-mail">                        
                      </div>


                      <div class="modal-footer">
                        <center>
                          <button id="btn_enviar_mail" type="button" class="btn btn-default" v-on:click="procesar_envio_mail()"><?php echo __('Enviar') ?></button>
                          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cerrar') ?></button>
                        </center>  
                        <input type="hidden" name="sino_aprobado_administracion" value="NO">
                      </div>

                    </div>
                    <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
                </div>
              <!-- MODAL CONFIRMAR MAIL -->
                <?php if ($Solicitud->id == 99999999999) { ?>
                <div class="col-lg-12">            
                  <pre>@{{ $data }}</pre>
                </div>
                <?php } ?>  

            </div>
            <!-- /.box-body -->
          </div>
      </div>

      <!-- MODAL DOWNCONTACTOS -->
        <div class="modal modal fade" id="modal-downcontactos">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="modal-downcontactos-titulo"><?php echo __('Descargar').' '.__('Contactos') ?></div></h4>
              </div>

              <div class="modal-body" id="modal-bodi-downcontactos"> 

                <form role="form">
                  <!-- text input -->

                  <div class="form-group">
                      <select v-model="valor_select_contactdown" class="form-control">
                        <option v-for="select in select_contactdown" v-bind:value="select.id">
                          @{{ select.detalle }}
                        </option>
                      </select>
                  </div>


                  <p style="color: red">Solo se descargargarán los contactos que no esten cancelados o con causa de baja</p>
                  

                  <a v-show="valor_select_contactdown.substr(0, 8) == 'grupo_wa'" v-bind:href="urlgrupo(valor_select_contactdown.replace('grupo_wa_', ''))">
                    <button type="button" class="btn btn-primary"><i class="fa fa-mobile" style="padding-right: 5px; font-size: 20px"> </i>  <?php echo __('Descargar contactos del Grupo de Whatsapp') ?> @{{ valor_select_contactdown.replace('grupo_wa_', '') }}</button>
                  </a>

                  <a v-show="valor_select_contactdown == 'todos'" href="<?php echo $dominio_publico ?>f/contactDown/<?php echo $Solicitud->id; ?>/todos/0/1/9999999/<?php echo md5(ENV('PREFIJO_HASH').$Solicitud->id) ?>">
                    <button type="button" class="btn btn-primary"><i class="fa fa-mobile" style="padding-right: 5px; font-size: 20px"> </i>  <?php echo __('Descargar todos los contactos') ?> </button>
                  </a>

                  <div v-show="valor_select_contactdown == 'pagina'" class="form-group">
                    <label><?php echo __('Cantidad de contactos por página') ?></label>  
                      <input type="number" name="cant_x_pagina" class="form-control" v-model="cant_x_pagina">
                      <br>
                      <button type="button" class="btn btn-default" v-on:click="crearListasDeContactos()"><?php echo __('Generar lista de contactos') ?></button>
                      <hr>

                      <p v-show="valor_select_contactdown == 'pagina'"  v-for="lista in listas_de_contactos">
                        <a v-bind:href="lista.url">
                          <button type="button" class="btn btn-primary"><i class="fa fa-mobile" style="padding-right: 5px; font-size: 20px"> </i>  <?php echo __('Descargar').' '.__('Contactos') ?> @{{ lista.titulo }}</button>
                        </a>
                      </p>
                  </div>

                </form>

              </div>

              <div class="modal-footer">
                <center>
                  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cerrar') ?></button>
                </center>  
                <input type="hidden" name="sino_aprobado_administracion" value="NO">
              </div>

            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
      <!-- MODAL DOWNCONTACTOS -->

      <!-- MODAL DOWNCONTACTOS -->
        <div class="modal modal fade" id="modal-grupos-de-whatsapp">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="modal-grupos-de-whatsapp"><?php echo __('Filtrar por Grupo de WhatsApp') ?></div> <span v-html="datos_grupo_status_save"></span></h4>
              </div>

              <div class="modal-body" id="modal-bodi-grupos-de-whatsapp"> 

                <table class="table table-striped">
                  <tbody>
                    <tr>
                      <th style="width: 10px"><?php echo __('Grupo') ?></th>
                      <th><?php echo __('Tutor')  ?> <?php echo __('Celular')  ?> </th>
                      <th><?php echo __('Tutor')  ?> <?php echo __('Nombre')  ?> </th>
                      <th><?php echo __('Guardar')  ?> </th>
                      <th><?php echo __('Ir') ?></th>
                    </tr>
                    <tr v-for="grupo in grupos">
                      <td># @{{ grupo.id }}</td>
                      <td> 
                        <input v-show="grupo.id > 0" type="text" v-model="grupo.celular_responsable_de_inscripciones" placeholder="<?php echo __('Celular') ?> <?php echo __('Tutor') ?>"> 
                      </td>
                      <td> 
                        <input v-show="grupo.id > 0" type="text" v-model="grupo.nombre_responsable_de_inscripciones" placeholder="<?php echo __('Nombre') ?> <?php echo __('Tutor') ?>"> 
                      </td>
                      <td>                       
                          <button v-show="grupo.id > 0" type="button" class="btn btn-primary btn-xs" v-on:click="guardarDatosGrupo(grupo.grupo_id, grupo.id, grupo.celular_responsable_de_inscripciones, grupo.nombre_responsable_de_inscripciones)"><i class="fa fa-fw fa-save" style="font-size: 19px"></i></button>
                      </td>
                      <td>
                        <a v-bind:href="grupo.url">
                          <button type="button" class="btn btn-default"><?php echo __('Ir') ?></button>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>

              </div>

              <div class="modal-footer">
                <center>
                  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cerrar') ?></button>
                </center>  
                <input type="hidden" name="sino_aprobado_administracion" value="NO">
              </div>

            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
      <!-- MODAL DOWNCONTACTOS -->

      <!-- MODAL COLUMNAS -->
        <div class="modal modal fade" id="modal-columnas">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Habilitar columnas') ?></div></h4>
              </div>

              <div class="modal-body" id="modal-bodi-columnas">


              <table class="table">
                <tr>
                  <th><?php echo __('ID') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_id">
                        <span class="slider round"></span>
                      </label>
                    </div>
                  </td>

                  <th><?php echo __('Nro de Orden') ?>
                    <span class="pull-right-container">
                      <small class="label pull-left bg-blue">new</small>
                    </span>
                  </th>
                  <td>                    
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_nro_orden">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                </tr>

                <?php if ($tipo_de_evento_id == 3) { ?>
                <tr>
                  <th><?php echo __('Ciudad') ?>
                    <span class="pull-right-container">
                      <small class="label pull-left bg-blue">new</small>
                    </span>
                  </th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_ciudad">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <th><?php echo __('Pais') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_pais">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                </tr>
                <?php } ?>

                <tr>
                  <th><?php echo __('Prioridad') ?></th>                    
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_prioridad">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>

                  <th style="max-width: 400px !important"><?php echo __('Datos') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_comprimido">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                </tr>

                <tr>
                  <th><?php echo __('Fecha') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_fecha">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>

                  <th><?php echo __('Apellido') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_apellido">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                </tr>

                <tr>
                  <th><?php echo __('Nombre') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_nombre">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>

                  <th><?php echo __('Celular') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_celular">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                </tr>

                <tr>
                  <th><?php echo __('Correo') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_email_correo">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>

                  <th v-show="mostrar_fechas"><?php echo __('Horario') ?></th>
                  <td v-show="mostrar_fechas">
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_fecha_de_evento">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                </tr>

                <tr>
                  <th><?php echo __('Estado') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_estado">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <th><?php echo __('Grupo de whatsapp') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_grupo">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                </tr>

              </table>


              </div>

              <div class="modal-footer">
                <center>
                  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cerrar') ?></button>
                </center>  
                <input type="hidden" name="sino_aprobado_administracion" value="NO">
              </div>

            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
      <!-- MODAL COLUMNAS -->

    </div>
    <!-- FIN app-lista -->    

      <!-- DataTables -->
      <script>
        $(function () {
          $('#table').DataTable({
            'language': {
              'autoWidth': true,
                  'lengthMenu': '<?php echo __('Mostrar') ?> _MENU_ <?php echo __('Registros por pagina') ?>',
                  'search': '<?php echo __('Buscar') ?>',
                  'zeroRecords': '<?php echo __('No hay resultados para la busqueda') ?>',
                  'info': '<?php echo __('Mostrando Pagina') ?> _PAGE_ <?php echo __('de') ?> _PAGES_',
                  'infoEmpty': 'No hay registros',
                  'paginate': {
                      'first':      '<?php echo __('Primero') ?>',
                      'last':       '<?php echo __('Ultimo') ?>',
                      'next':       '<?php echo __('Siguiente') ?>',
                      'previous':   '<?php echo __('Anterior') ?>'
                  },
                  'infoFiltered': '(<?php echo __('filtrado') ?> _MAX_ <?php echo __('registros totales') ?>)'
              },
              'paging': false,
              'pageLength': 9999,
              'order': [[ 0, 'des' ]],
              'columnDefs': [{ "width": "100px", "targets": 0 }], 
          })
        })
      </script>


    <!-- jQuery 3 -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo $dominio_publico ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Morris.js charts -->
    <script src="<?php echo $dominio_publico ?>bower_components/raphael/raphael.min.js"></script>
    <script src="<?php echo $dominio_publico ?>bower_components/morris.js/morris.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="<?php echo $dominio_publico ?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?php echo $dominio_publico ?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo $dominio_publico ?>bower_components/moment/min/moment.min.js"></script>
    <script src="<?php echo $dominio_publico ?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- datepicker -->
    <script src="<?php echo $dominio_publico ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="<?php echo $dominio_publico ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- Slimscroll -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo $dominio_publico ?>bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo $dominio_publico ?>dist/js/adminlte.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?php echo $dominio_publico ?>dist/js/pages/dashboard.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo $dominio_publico ?>dist/js/demo.js"></script>
    <!-- DataTables -->
    <script src="<?php echo $dominio_publico ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $dominio_publico ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

    <!-- INICIO APP app-form -->
        <script type="text/javascript">
            const config = {
              locale: 'es', 
            };
            //moment.locale('es');
            //console.log(moment());
            Vue.use(VeeValidate, config);

            var app = new Vue({
              el: '#app-lista',

              data: {
                apellido: null,
                nombre: null,
                celular: null,
                email_correo: null,
                consulta: null,
                fecha_de_evento_id: null,
                sino_notificar_proximos_eventos: true,
                sino_acepto_politica_de_privacidad: null,
                mensaje_error: '',
                desabilitar: '',
                sino: null,
                show_col_id: false,
                show_col_nro_orden: false,
                show_col_prioridad: false,
                show_col_comprimido: true,
                show_col_fecha: false,
                show_col_apellido: false,
                show_col_nombre: false,
                show_col_celular: false,
                show_col_email_correo: false,
                show_col_fecha_de_evento: false,
                <?php if ($tipo_de_evento_id == 3) { ?>
                show_col_pais: false,
                show_col_ciudad: false,
                <?php } ?>
                show_col_estado: false,
                show_col_grupo: false,
                mensaje_extra: '',
                fecha_hoy: moment().format('DD/MM/YYYY'),
                email_nombre: null,
                email_apellido: null,
                email_asunto: null,
                email_codigo: null,
                email_inscripcion_id: null,
                email_i: null,
                enviar_mail: true,
                email_mensaje_extra: null,
                datos_grupo_status_save: null,
                url_envio_de_motivacion_2: '<?php echo $url_envio_de_motivacion_2 ?>',
                url_envio_de_motivacion_3: '<?php echo $url_envio_de_motivacion_3 ?>',
                estados: [
                <?php 
                foreach ($Inscripciones as $Inscripcion) { 
                  $hay_consulta = 'false';
                  if ($Inscripcion->consulta <> '') {
                    $hay_consulta = 'true';
                  }
                  if ($Inscripcion->sino_eleccion_modalidad_online == 'SI') {
                    $fecha_de_evento_id = 'null';  
                  } 
                  else {
                    $fecha_de_evento_id = '-1';
                  }
                  $fecha_de_inicio = 'null';
                  if ($Inscripcion->fecha_de_evento_id <> '') {
                    $fecha_de_evento_id = $Inscripcion->fecha_de_evento_id;

                    $fecha_de_evento = null;
                    foreach ($Fechas_de_evento as $fecha_de_evento_iterar) {
                      if ($fecha_de_evento_iterar->id == $fecha_de_evento_id) {
                        $fecha_de_evento = $fecha_de_evento_iterar;
                      }
                    }                    
                    $fecha_de_inicio = 'moment("'.$Inscripcion->fecha_de_evento->fecha_de_inicio.'").format("DD/MM/YYYY")';
                  }

                  if ($Inscripcion->solicitud_id <> $Inscripcion->solicitud_original and $Inscripcion->solicitud_original == $Solicitud->id and ($Inscripcion->causa_de_cambio_de_solicitud_id == 1 or $Inscripcion->causa_de_cambio_de_solicitud_id == 4)) {
                    $promocionado = 'true';
                  }
                  else {
                    $promocionado = 'false';  
                  }

                  if (!isset($ocultar_certificados) or !$ocultar_certificados) {
                    $ocultar_certificados = false;
                  }
                  else {
                    $ocultar_certificados = true;
                  }
                ?>
                  {
                    inscripcion_id: <?php echo $Inscripcion->id ?>,
                    promocionado: <?php echo $promocionado ?>,
                    certificado: <?php echo $Inscripcion->mostrarCertificado($Solicitud, $ocultar_certificados, $Inscripcion->cant_asistencias, $Inscripcion->orden_de_leccion, $Inscripcion->cant_evaluaciones) ?>,
                    fecha_de_evento_id: <?php echo $fecha_de_evento_id ?>,
                    fecha_de_inicio: <?php echo $fecha_de_inicio ?>,
                    envio_pedido_de_confirmacion: <?php echo sino_a_tf($Inscripcion->sino_envio_pedido_de_confirmacion) ?>,
                    envio_recordatorio_pedido_de_confirmacion: <?php echo sino_a_tf($Inscripcion->sino_envio_recordatorio_pedido_de_confirmacion) ?>,
                    confirmo: <?php echo sino_a_tf($Inscripcion->sino_confirmo) ?>,
                    envio_voucher: <?php echo sino_a_tf($Inscripcion->sino_envio_voucher) ?>,
                    envio_motivacion: <?php echo sino_a_tf($Inscripcion->sino_envio_motivacion) ?>,
                    envio_motivacion_2: <?php echo sino_a_tf($Inscripcion->sino_envio_motivacion_2) ?>,
                    envio_motivacion_3: <?php echo sino_a_tf($Inscripcion->sino_envio_motivacion_3) ?>,
                    envio_de_encuesta: <?php echo sino_a_tf($Inscripcion->sino_envio_de_encuesta) ?>,
                    envio_recordatorio: <?php echo sino_a_tf($Inscripcion->sino_envio_recordatorio) ?>,
                    prioridad: 1,
                    hay_consulta: <?php echo $hay_consulta ?>, 
                    contesto_consulta: <?php echo sino_a_tf($Inscripcion->sino_contesto_consulta) ?>,
                    asistio: <?php echo sino_a_tf($Inscripcion->sino_asistio) ?>,
                    envio_recordatorio_proxima_clase: <?php echo sino_a_tf($Inscripcion->sino_envio_recordatorio_proxima_clase) ?>,
                    envio_recordatorio_proxima_clase_a_no_asistente: <?php echo sino_a_tf($Inscripcion->sino_envio_recordatorio_proxima_clase_a_no_asistente) ?>,
                    cancelo: <?php echo sino_a_tf($Inscripcion->sino_cancelo) ?>,
                    envio_invitacion_al_curso_online: <?php echo sino_a_tf($Inscripcion->sino_invitado_al_curso_online) ?>,
                    envio_1: <?php echo sino_a_tf($Inscripcion->sino_envio_1) ?>,
                    envio_2: <?php echo sino_a_tf($Inscripcion->sino_envio_2) ?>,
                    envio_3: <?php echo sino_a_tf($Inscripcion->sino_envio_3) ?>,
                    envio_4: <?php echo sino_a_tf($Inscripcion->sino_envio_4) ?>,
                    envio_5: <?php echo sino_a_tf($Inscripcion->sino_envio_5) ?>,
                    envio_6: <?php echo sino_a_tf($Inscripcion->sino_envio_6) ?>,
                    envio_7: <?php echo sino_a_tf($Inscripcion->sino_envio_7) ?>,
                    envio_8: <?php echo sino_a_tf($Inscripcion->sino_envio_8) ?>,
                    envio_9: <?php echo sino_a_tf($Inscripcion->sino_envio_9) ?>,
                    envio_10: <?php echo sino_a_tf($Inscripcion->sino_envio_10) ?>,
                    envio_certificado: <?php echo sino_a_tf($Inscripcion->sino_envio_certificado) ?>,

                    causa_de_baja_id: '<?php echo $Inscripcion->causa_de_baja_id ?>',
                    causa_de_baja_status_save: '',
                    celular: '<?php echo $Inscripcion->celular ?>',
                    celular_status_save: '',
                    grupo: '<?php echo $Inscripcion->grupo ?>',
                    grupo_status_save: '',
                    observaciones: '<?php echo $Inscripcion->observaciones ?>',
                    obs_status_save: '',
                    sino_eleccion_modalidad_online: <?php echo sino_a_tf($Inscripcion->sino_eleccion_modalidad_online) ?>,
                  },
                <?php } ?>
                ],
                select_fechas_de_eventos: 'todos',
                fechas_de_evento: [
                    { detalle: '<?php echo __('Todos') ?>', id: 'todos'},
                    { detalle: '<?php echo __('No pueden asistir') ?>', id: '-1'},
                    { detalle: '<?php echo __('Modalidad Online') ?>', id: 'mo'},
                  <?php 
                  if ($Fechas_de_evento <> null) {
                    foreach ($Fechas_de_evento as $Fecha_de_evento) { 
                  ?>
                    { 
                      detalle: '<?php echo $Fecha_de_evento->armarDetalleFechasDeEventos('select', true, $Idioma_por_pais, $Solicitud, $idioma) ?>', 
                      cupo_maximo: '<?php echo $Fecha_de_evento->cupo_maximo_disponible_del_salon ?>', 
                      id: <?php echo $Fecha_de_evento->id ?> 
                    },
                  <?php 
                      } 
                    } 
                  ?>
                ],
                causas_de_baja: [
                    { causa_de_baja: '', id: 0},
                  <?php 
                  foreach ($Causas_de_baja as $Causa_de_baja) { 
                  ?>
                    { 
                      causa_de_baja: '<?php echo $Causa_de_baja->causa_de_baja ?>', 
                      id: <?php echo $Causa_de_baja->id ?>
                    },
                  <?php } ?>
                ],
                causas_de_cambio_de_solicitud: [
                  <?php 
                  foreach ($Causas_de_cambio_de_solicitud as $Causa_de_cambio_de_solicitud) { 
                  ?>
                    { 
                      causa_de_cambio_de_solicitud: '<?php echo $Causa_de_cambio_de_solicitud->causa_de_cambio_de_solicitud ?>', 
                      id: <?php echo $Causa_de_cambio_de_solicitud->id ?>
                    },
                  <?php } ?>
                ],
                grupos: [
                    { 
                      grupo_id: null, 
                      grupo: 'ninguno', 
                      id: 'ninguno', 
                      url: '<?php echo $Solicitud->url_grupo_whatsapp(0) ?>',
                      celular_responsable_de_inscripciones: '', 
                      nombre_responsable_de_inscripciones: ''
                      }, 
                  <?php 
                  if ($Grupos <> null) {
                    foreach ($Grupos['lista_de_grupos'] as $grupo) { ?>
                    { 
                      grupo_id: <?php echo $grupo['grupo_id'] ?>,
                      grupo: <?php echo $grupo['nro_de_grupo'] ?>,
                      id: <?php echo $grupo['nro_de_grupo'] ?>,
                      url: '<?php echo $grupo['url'] ?>',
                      celular_responsable_de_inscripciones: '<?php echo $grupo['celular_responsable_de_inscripciones'] ?>',
                      nombre_responsable_de_inscripciones: '<?php echo $grupo['nombre_responsable_de_inscripciones'] ?>'
                    },
                  <?php 
                    } 
                  }
                  ?>
                ],
                cant_x_pagina: 100,
                listas_de_contactos: [],
                valor_select_ver: 'todos',
                select_ver: [
                    { detalle: '<?php echo __('Ver todos') ?>', id: 'todos'},
                    { detalle: '<?php echo __('Ocultar cancelados o con baja') ?>', id: 'ocultar_cancelados'},
                    { detalle: '<?php echo __('Ocultar promocionados') ?>', id: 'ocultar_promocionados'},
                    { detalle: '<?php echo __('Solo promocionados') ?>', id: 'solo_promocionados'},
                    { detalle: '<?php echo __('Solo cancelados o con baja') ?>', id: 'solo_cancelados'},
                    { detalle: '<?php echo __('Sin contactar') ?>', id: 'sin_contactar'},
                    { detalle: '<?php echo __('Sin Grupo de whatsapp') ?>', id: 'sin_grupo'},
                  <?php 
                  if ($Grupos <> null) {
                    foreach ($Grupos['lista_de_grupos'] as $grupo) { ?>                    
                    { detalle: '<?php echo __('Grupo de whatsapp') ?>: <?php echo $grupo['nro_de_grupo'] ?>', id: 'grupo_wa_<?php echo $grupo['nro_de_grupo'] ?>'},
                <?php 
                    } 
                  }
                ?>
                ],
                valor_select_contactdown: '',
                select_contactdown: [
                    { detalle: '<?php echo __('Seleccione como quiere descargar los contactos') ?>', id: ''},
                    <?php if (!isset($nro_de_grupo)) { ?>
                      { detalle: '<?php echo __('Descargar todos') ?>', id: 'todos'},
                      { detalle: '<?php echo __('Descargar por Páginas') ?>', id: 'pagina'},
                      <?php 
                      if ($Grupos <> null) {
                        foreach ($Grupos['lista_de_grupos'] as $grupo) { ?>                    
                        { detalle: '<?php echo __('Grupo de whatsapp') ?>: <?php echo $grupo['nro_de_grupo'] ?>', id: 'grupo_wa_<?php echo $grupo['nro_de_grupo'] ?>'},
                      <?php 
                        } 
                      }
                      ?>

                    <?php } 
                    else {?>
                      { detalle: '<?php echo __('Grupo de whatsapp') ?>: <?php echo $nro_de_grupo?>', id: 'grupo_wa_<?php echo $nro_de_grupo ?>'},
                    <?php } ?>
                ],
                lista_de_advertencias: [],
                mostrar_fechas: <?php echo $mostrar_fechas ?>,
                cant_total_inscriptos: <?php echo $cant_total_inscriptos ?>,
                criterio: '<?php echo $criterio ?>',
              },

              methods: { 
                limpiarCadena: function (cadena) {
                  cadena = cadena.replace(/[/%<>]/gi, "");
                  cadena = cadena.replace(/[\\]/gi, "");
                  return cadena
                },

                codificarCadena: function (cadena) {
                  cadena = cadena.replace(/[/<>]/gi, "");
                  cadena = cadena.replace(/[\\]/gi, "");
                  cadena = cadena.replace(/[\n]/gi, " ");
                  return cadena
                },

                mostrarFila: function (i) {
                  mostrar = false                  
                  mostrar_fecha = false  
                  if (this.valor_select_ver == 'ocultar_cancelados' && (!this.estados[i].cancelo && this.estados[i].causa_de_baja_id == '')) {
                    mostrar = true
                  }
                  if (this.valor_select_ver == 'solo_cancelados' && (this.estados[i].cancelo ||  this.estados[i].causa_de_baja_id != '')) {
                    mostrar = true
                  }
                  if (this.valor_select_ver == 'sin_contactar' && !this.estados[i].envio_pedido_de_confirmacion && (!this.estados[i].cancelo && this.estados[i].causa_de_baja_id == '')) {
                    mostrar = true
                  }
                  if (this.valor_select_ver == 'todos') {
                    mostrar = true
                  }
                  var grupo_wa = this.valor_select_ver.split('grupo_wa_')
                  if (grupo_wa.length > 0 && grupo_wa[1] ==  this.estados[i].grupo) {
                    mostrar = true  
                  }
                  if (this.valor_select_ver == 'sin_grupo' && this.estados[i].grupo == '') {
                    mostrar = true
                  }
                  if (this.valor_select_ver == 'ocultar_promocionados' && !this.estados[i].promocionado && !this.estados[i].certificado) {
                    mostrar = true
                  }
                  if (this.valor_select_ver == 'solo_promocionados' && (this.estados[i].promocionado || this.estados[i].certificado)) {
                    mostrar = true
                  }

                  if (
                    !this.mostrar_fechas || 
                    (this.mostrar_fechas && 
                        (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos 
                          || this.select_fechas_de_eventos == 'todos' 
                          || (this.estados[i].fecha_de_evento_id == null && this.select_fechas_de_eventos == '-1' && this.estados[i].sino_eleccion_modalidad_online == null)))) {
                    mostrar_fecha = true
                  }


                  if (this.select_fechas_de_eventos == 'mo' && this.estados[i].sino_eleccion_modalidad_online) {
                    mostrar_fecha = true
                  }

                  if (mostrar && mostrar_fecha) {
                    mostrar_fin = true
                  }
                  else {
                    mostrar_fin = false  
                  }
                

                  return mostrar_fin
                  
              },

                guardarObs: function (i) {
                  app["estados"][i].obs_status_save = '<img src="<?php echo $dominio_publico ?>img/cargando.gif" width="30px">'
                  observaciones=encodeURI(this.estados[i].observaciones)
                  if (observaciones == '') {
                    observaciones='XXNADAXX'
                  }
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>f/i/guardar-obs/'+this.estados[i].inscripcion_id+'/'+observaciones,
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}"
                    },
                    success: function success(data, status) {   
                      app["estados"][i].obs_status_save = data
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 7, revise su conección a Internet - Error: '+errorThrown);
                    }
                  });                  
                }, 

                guardarCel: function (i) {
                  app["estados"][i].celular_status_save = '<img src="<?php echo $dominio_publico ?>img/cargando.gif" width="30px">'
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>f/i/guardar-cel/'+this.estados[i].inscripcion_id+'/'+this.estados[i].celular,
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}"
                    },
                    success: function success(data, status) {   
                      app["estados"][i].celular_status_save = data
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 6, revise su conección a Internet - Error: '+errorThrown);
                    }
                  });                  
                }, 

                guardarGrupo: function (i, id) {
                  app["estados"][i].grupo_status_save = '<img src="<?php echo $dominio_publico ?>img/cargando.gif" width="30px">'
                  app["estados"][i].grupo = id
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>f/i/guardar-grupo/'+this.estados[i].inscripcion_id+'/'+id,
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}"
                    },
                    success: function success(data, status) {   
                      app["estados"][i].grupo_status_save = data
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 6, revise su conección a Internet - Error: '+errorThrown);
                    }
                  });                  
                }, 

                guardarDatosGrupo: function (grupo_id, id, celular_responsable_de_inscripciones, nombre_responsable_de_inscripciones) {
                  app["datos_grupo_status_save"] = '<img src="<?php echo $dominio_publico ?>img/cargando.gif" width="30px">'
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>f/i/guardar-datos-grupo',
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      solicitud_id: <?php echo $Solicitud->id ?>,
                      grupo_id: grupo_id,
                      id: id,
                      celular_responsable_de_inscripciones: celular_responsable_de_inscripciones,
                      nombre_responsable_de_inscripciones: nombre_responsable_de_inscripciones

                    },
                    success: function success(data, status) {   
                      app["datos_grupo_status_save"] = data 
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 7, revise su conección a Internet - Error: '+errorThrown);
                    }
                  });                  
                }, 

                modificarCausaDeBaja:  function (i) {
                  app["estados"][i].causa_de_baja_status_save = '<img src="<?php echo $dominio_publico ?>img/cargando.gif" width="30px">'
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>f/i/baja-de-alumno/'+this.estados[i].inscripcion_id+'/'+this.estados[i].causa_de_baja_id,
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}"
                    },
                    success: function success(data, status) {   
                      app["estados"][i].causa_de_baja_status_save = data
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 5, revise su conección a Internet - Error: '+errorThrown);
                    }
                  });                  
                },   
 

                setearSino: function (codigo, i, inscripcion_id, estado_i = null) {
                  //console.log('codigo: '+codigo);
                  estado_tf = estado_i
                  if (codigo == 1) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_pedido_de_confirmacion;
                      }
                      this.estados[i].envio_pedido_de_confirmacion = !estado_tf;  
                  }
                  if (codigo == 2) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_recordatorio_pedido_de_confirmacion; 
                      }
                      this.estados[i].envio_recordatorio_pedido_de_confirmacion = !estado_tf;  
                  }
                  if (codigo == 3) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].confirmo;  
                      }
                      this.estados[i].confirmo = !estado_tf; 
                  }
                  if (codigo == 4) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_voucher;  
                      }
                      this.estados[i].envio_voucher = !estado_tf; 
                  }
                  if (codigo == 5) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_motivacion;  
                      }
                      this.estados[i].envio_motivacion = !estado_tf; 
                  }
                  if (codigo == 6) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_recordatorio;  
                      }
                      this.estados[i].envio_recordatorio = !estado_tf; 
                  }
                  if (codigo == 7) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].contesto_consulta;  
                      }
                      this.estados[i].contesto_consulta = !estado_tf; 
                  }
                  if (codigo == 9) {
                    if (estado_tf == null) {
                      estado_tf = this.estados[i].envio_recordatorio_proxima_clase;  
                    }
                      this.estados[i].envio_recordatorio_proxima_clase = !estado_tf; 
                  }
                  if (codigo == 10) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_recordatorio_proxima_clase_a_no_asistente;  
                      }
                      this.estados[i].envio_recordatorio_proxima_clase_a_no_asistente = !estado_tf; 
                  }
                  if (codigo == 11) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].cancelo;  
                      }
                      this.estados[i].cancelo = !estado_tf; 
                  }
                  if (codigo == 12) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_invitacion_al_curso_online;  
                      }
                      this.estados[i].envio_invitacion_al_curso_online = !estado_tf; 
                  }
                  if (codigo == 13) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_1;  
                      }
                      this.estados[i].envio_1 = !estado_tf; 
                  }
                  if (codigo == 14) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_2;  
                      }
                      this.estados[i].envio_2 = !estado_tf; 
                  }
                  if (codigo == 15) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_3;  
                      }
                      this.estados[i].envio_3 = !estado_tf; 
                  }
                  if (codigo == 16) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_4;  
                      }
                      this.estados[i].envio_4 = !estado_tf; 
                  }
                  if (codigo == 17) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_5;  
                      }
                      this.estados[i].envio_5 = !estado_tf; 
                  }
                  if (codigo == 18) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_6;  
                      }
                      this.estados[i].envio_6 = !estado_tf; 
                  }
                  if (codigo == 19) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_7;  
                      }
                      this.estados[i].envio_7 = !estado_tf; 
                  }
                  if (codigo == 20) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_8;  
                      }
                      this.estados[i].envio_8 = !estado_tf; 
                  }
                  if (codigo == 21) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_9;  
                      }
                      this.estados[i].envio_9 = !estado_tf; 
                  }
                  if (codigo == 22) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_10;  
                      }
                      this.estados[i].envio_10 = !estado_tf; 
                  }
                  if (codigo == 24) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_certificado;  
                      }
                      this.estados[i].envio_certificado = !estado_tf; 
                  }
                  if (codigo == 27) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_motivacion_2;  
                      }
                      this.estados[i].envio_motivacion_2 = !estado_tf; 
                  }
                  if (codigo == 28) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_motivacion_3;  
                      }
                      this.estados[i].envio_motivacion_3 = !estado_tf; 
                  }
                  if (codigo == 29) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_de_encuesta;  
                      }
                      this.estados[i].envio_de_encuesta = !estado_tf; 
                  }

                  //console.log('estado_tf0: '+estado_tf)
                  if (estado_tf) {
                    sino = 'SI';
                  }
                  else {
                    sino = 'NO';
                  }
                  

                  $.ajax({
                    url: '<?php echo $dominio_publico ?>f/i/setear-sino/'+codigo+'/'+inscripcion_id+'/<?php echo $Solicitud->id ?>',
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      sino: sino
                    },
                    success: function success(data, status)  { 
                      estado_tf = estado_i
                      //console.log('data1: '+data)
                      //console.log('estado_tf: '+estado_tf)
                      if (codigo == 1) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_pedido_de_confirmacion;
                          //console.log('estado_tf2: '+estado_tf)
                          app["estados"][i].envio_pedido_de_confirmacion = !estado_tf;  
                          //console.log('estado_tf3: '+app["estados"][i].envio_pedido_de_confirmacion)

                        }
                      }
                      if (codigo == 2) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_recordatorio_pedido_de_confirmacion; 
                          app["estados"][i].envio_recordatorio_pedido_de_confirmacion = !estado_tf;  
                        }
                      }
                      if (codigo == 3) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].confirmo;  
                          app["estados"][i].confirmo = !estado_tf; 
                        }
                      }
                      if (codigo == 4) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_voucher;  
                          app["estados"][i].envio_voucher = !estado_tf; 
                        }
                      }
                      if (codigo == 5) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_motivacion;  
                          app["estados"][i].envio_motivacion = !estado_tf; 
                        }
                      }
                      if (codigo == 6) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_recordatorio;  
                          app["estados"][i].envio_recordatorio = !estado_tf; 
                        }
                      }
                      if (codigo == 7) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].contesto_consulta;  
                          app["estados"][i].contesto_consulta = !estado_tf; 
                        }
                      }
                      if (codigo == 9) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_recordatorio_proxima_clase;  
                          app["estados"][i].envio_recordatorio_proxima_clase = !estado_tf; 
                        }
                      }
                      if (codigo == 10) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_recordatorio_proxima_clase_a_no_asistente;  
                          app["estados"][i].envio_recordatorio_proxima_clase_a_no_asistente = !estado_tf; 
                        }
                      }
                      if (codigo == 11) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].cancelo;  
                          app["estados"][i].cancelo = !estado_tf; 
                        }
                      }     
                      if (codigo == 12) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_invitacion_al_curso_online;  
                          app["estados"][i].envio_invitacion_al_curso_online = !estado_tf; 
                        }
                      }     
                      if (codigo == 13) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_1;  
                          app["estados"][i].envio_1 = !estado_tf; 
                        }
                      }   
                      if (codigo == 14) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_2;  
                          app["estados"][i].envio_2 = !estado_tf; 
                        }
                      }   
                      if (codigo == 15) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_3;  
                          app["estados"][i].envio_3 = !estado_tf; 
                        }
                      }   
                      if (codigo == 16) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_4;  
                          app["estados"][i].envio_4 = !estado_tf; 
                        }
                      }   
                      if (codigo == 17) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_5;  
                          app["estados"][i].envio_5 = !estado_tf; 
                        }
                      }   
                      if (codigo == 18) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_6;  
                          app["estados"][i].envio_6 = !estado_tf; 
                        }
                      }   
                      if (codigo == 19) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_7;  
                          app["estados"][i].envio_7 = !estado_tf; 
                        }
                      }   
                      if (codigo == 20) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_8;  
                          app["estados"][i].envio_8 = !estado_tf; 
                        }
                      }   
                      if (codigo == 21) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_9;  
                          app["estados"][i].envio_9 = !estado_tf; 
                        }
                      }   
                      if (codigo == 22) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_10;  
                          app["estados"][i].envio_10 = !estado_tf; 
                        }
                      }    
                      if (codigo == 24) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_certificado;  
                          app["estados"][i].envio_certificado = !estado_tf; 
                        }
                      }              
                      if (codigo == 27) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_motivacion_2;  
                          app["estados"][i].envio_motivacion_2 = !estado_tf; 
                        }
                      }  
                      if (codigo == 28) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_motivacion_3;  
                          app["estados"][i].envio_motivacion_3 = !estado_tf; 
                        }
                      } 
                      if (codigo == 29) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_de_encuesta;  
                          app["estados"][i].envio_de_encuesta = !estado_tf; 
                        }
                      }  
                      //console.log('paso 2')
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 2, revise su conección a Internet');
                    }
                  });


                },
                
                filtrar_tabla: function () {
                  //Filtro la tabla segun el valor del select de arriba
                  for (i = 0; i < this.fechas_de_evento.length; i++) { 
                    if (this.fechas_de_evento[i].id == this.select_fechas_de_eventos) {
                      //$('input[type="search"]').val(this.fechas_de_evento[i].detalle).trigger('keyUp');
                      
                      valor_de_filtro = this.fechas_de_evento[i].detalle       
                      if (this.select_fechas_de_eventos == 'todos') {
                        valor_de_filtro = ''                    
                      }

                      if (this.select_fechas_de_eventos == -1) {
                        valor_de_filtro = "<?php echo $mensaje_np ?>"                 
                      }
                      
                      var table = $('#table').DataTable();
                      table.search( valor_de_filtro ).draw();
                    }
                  }
                  
                },

                

                preparar_envio_mail: function (codigo, nombre, apellido, inscripcion_id, asunto, i) {
                  $('#modal-bodi-confirmar-mail').html('')
                  $('#btn_enviar_mail').show();
                  //console.log('entro preparar_envio_mail')
                  //console.log('Nombre: '+nombre)
                  this.email_nombre = nombre
                  this.email_apellido = apellido
                  this.email_codigo = codigo
                  this.email_inscripcion_id = inscripcion_id
                  this.email_i = i
                  this.email_asunto = asunto
                  //console.log("codigo: "+codigo+" - asunto: "+asunto)
                },

                procesar_envio_mail: function () {

                  //this.marcar_envio(2, this.email_codigo, this.email_i, this.email_inscripcion_id)

                  if (this.email_codigo == 23) {
                    var mensaje = this.mensaje_extra
                    mensaje = mensaje.replaceAll('inscrito_nombre', this.email_nombre.trim())
                    mensaje = mensaje.replaceAll('inscrito_apellido', this.email_apellido.trim())   
                    mensaje = mensaje.replaceAll('tel_responsable_inscripcion', '<?php echo $tel_responsable_inscripcion ?>')   

                    this.email_mensaje_extra = mensaje
                  }
                  else {
                    this.email_mensaje_extra = ''
                  }
                  
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>f/inscripcion/enviar-email/'+this.email_inscripcion_id+'/'+this.email_codigo+'/'+this.limpiarCadena(this.email_asunto),
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      sino: 'SI',
                      mensaje_extra: this.email_mensaje_extra
                    },
                    success: function success(data, status) {   
                      var new_html = ''+data+''
                      $('#modal-bodi-confirmar-mail').html(new_html)
                      app.marcar_envio(2, app["email_codigo"], app["email_i"], app["email_inscripcion_id"])
                      $('#btn_enviar_mail').hide();

                      
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 3, revise su conección a Internet - Error: '+errorThrown);
                    }
                  });

                },

                traerLeccionesVistas: function (inscripcion_id, hash) {

                  //this.marcar_envio(2, this.email_codigo, this.email_i, this.email_inscripcion_id)
                  
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>traer-lecciones-vistas/'+inscripcion_id+'/'+hash,
                    type: 'GET',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      sino: 'SI'
                    },
                    success: function success(data, status) {   
                      var new_html = ''+data+''
                      $('#modal-bodi-traer-lecciones').html(new_html)                      
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 6, revise su conección a Internet - Error: '+errorThrown);
                    }
                  });

                },

                traerTPRealizados: function (inscripcion_id, hash) {

                  //this.marcar_envio(2, this.email_codigo, this.email_i, this.email_inscripcion_id)
                  
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>traer-tp-realizados/'+inscripcion_id+'/'+hash,
                    type: 'GET',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      sino: 'SI'
                    },
                    success: function success(data, status) {   
                      var new_html = ''+data+''
                      $('#modal-bodi-tp-realizados').html(new_html)                      
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 6, revise su conección a Internet - Error: '+errorThrown);
                    }
                  });

                },


                marcar_envio: function (medio_de_envio_id, codigo, i, inscripcion_id) {

                  //console.log('paso 1')
                  this.setearSino(codigo, i, this.estados[i].inscripcion_id, true)
                  //console.log('paso 3')
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>f/i/registrar-envio/'+codigo+'/'+inscripcion_id+'/'+medio_de_envio_id+'/<?php echo $Solicitud->id ?>',
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      sino: sino
                    },
                    success: function success(data, status) {   
                      if (data == 'SI') {
                        //console.log('data:2'+data)

                        if (codigo == 1) {
                            app["estados"][i].envio_pedido_de_confirmacion = true; 
                            //console.log('eeee: '+app["estados"][i].envio_pedido_de_confirmacion)
                        }
                        if (codigo == 2) {
                            app["estados"][i].envio_recordatorio_pedido_de_confirmacion = true;  
                        }
                        if (codigo == 4) {
                            app["estados"][i].envio_voucher = true;  
                        }
                        if (codigo == 5) {
                            app["estados"][i].envio_motivacion = true;  
                        }
                        if (codigo == 6) {
                            app["estados"][i].envio_recordatorio = true;  
                        }
                        if (codigo == 7) {
                            app["estados"][i].contesto_consulta = true;  
                        }
                        if (codigo == 9) {
                            app["estados"][i].envio_recordatorio_proxima_clase = true;  
                        }
                        if (codigo == 10) {
                            app["estados"][i].envio_recordatorio_proxima_clase_a_no_asistente = true;  
                        }
                        if (codigo == 12) {
                            app["estados"][i].envio_invitacion_al_curso_online = true;  
                        }
                        if (codigo == 13) {
                            app["estados"][i].envio_1 = true;  
                        }
                        if (codigo == 14) {
                            app["estados"][i].envio_2 = true;  
                        }
                        if (codigo == 15) {
                            app["estados"][i].envio_3 = true;  
                        }
                        if (codigo == 16) {
                            app["estados"][i].envio_4 = true;  
                        }
                        if (codigo == 17) {
                            app["estados"][i].envio_5 = true;  
                        }
                        if (codigo == 18) {
                            app["estados"][i].envio_6 = true;  
                        }
                        if (codigo == 19) {
                            app["estados"][i].envio_7 = true;  
                        }
                        if (codigo == 20) {
                            app["estados"][i].envio_8 = true;  
                        }
                        if (codigo == 21) {
                            app["estados"][i].envio_9 = true;  
                        }
                        if (codigo == 22) {
                            app["estados"][i].envio_10 = true;  
                        }
                        if (codigo == 24) {
                            app["estados"][i].envio_certificado = true;  
                        }
                        if (codigo == 27) {
                            app["estados"][i].envio_motivacion_2 = true;  
                        }
                        if (codigo == 28) {
                            app["estados"][i].envio_motivacion_3 = true;  
                        }
                        if (codigo == 29) {
                            app["estados"][i].envio_de_encuesta = true;  
                        }
                        //console.log('paso 4')
                      }
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 4, revise su conección a Internet');
                    }
                  });

                },
                  
                class_sino: function (sino) {
                  if (sino) {
                    clase = 'bg-olive'
                  }
                  else {
                    if (sino === null) {
                      clase = 'bg-grey'
                    }
                    else {
                      clase = 'bg-red'
                    }
                  }
                  clase = clase+' div-paso-inscripcion'
                  return clase
                },

                class_sino_cancelo: function (sino) {
                  if (sino) {
                    clase = 'bg-red'
                  }
                  else {
                    if (sino === null) {
                      clase = 'bg-grey'
                    }
                    else {
                      clase = 'bg-grey'
                    }
                  }
                  clase = clase+' div-paso-inscripcion'
                  return clase
                },

                class_promocionado: function (promocionado) {
                  if (promocionado) {
                    clase = 'background-color: #b0afdc'
                  }
                  else {
                    clase = ''
                  }
                  return clase
                },

                mostrar_responder_consulta: function (i) {
                  if (this.estados[i].hay_consulta && ((this.estados[i].envio_pedido_de_confirmacion && !this.estados[i].confirmo) || this.estados[i].fecha_de_evento_id == '-1')) {
                    habilitar = true;
                  }
                  else {
                    habilitar = false;
                  }

                  return habilitar
                },



                txt_sino: function (sino) {
                  if (sino) {
                    texto = 'SI'
                  }
                  else {
                    if (sino === null) {
                      texto = ''
                    }
                    else {
                      texto = 'NO'
                    }
                  }
                  
                  return texto
                },
                span_estado: function (i) {
                  if (this.estados[i].envio_pedido_de_confirmacion) {
                    estado = '<?php echo $estado_pedido_de_contacto ?>'

                    if (this.estados[i].envio_recordatorio_pedido_de_confirmacion) {
                      estado = '<?php echo __('Recordatorio de pedido de confirmación enviado') ?>'

                      if (this.estados[i].confirmo) {
                        estado = '<?php echo __('Confirmado') ?>'

                        if (this.estados[i].envio_voucher) {
                          estado = '<?php echo __('Voucher enviado') ?>'

                          if (this.estados[i].envio_motivacion) {
                            estado = '<?php echo __('Motivación enviada') ?>'

                            if (this.estados[i].envio_recordatorio) {
                              estado = '<?php echo __('Motivación y Recordatorio enviados') ?>'
                            }

                          }
                          else {

                            if (this.estados[i].envio_recordatorio) {
                              estado = '<?php echo __('Recordatorio enviado sin motivación enviada') ?>'
                            }

                          }

                        }


                      }

                    }

                  }
                  else {
                    estado = '<?php echo $estado_sin_pedido_de_contacto ?>'
                  }

                  if (this.estados[i].cancelo) {
                    estado = '<?php echo __('Canceló') ?>'
                  }          

                  if (this.estados[i].asistio) {
                    estado = '<?php echo __('Asistio') ?>'
                  }                 

                  return estado
                },
                
                contar_cant_inscriptos: function (situacion) {
                  // cuento la cantidad para el total de arriba
                  cant = 0
                  if (this.select_fechas_de_eventos == 'todos') {
                    // si el select esta en todos

                    // total inscriptos
                    if (situacion == 'inscriptos') {
                      cant = this.estados.length;  
                    }

                    // total contactados
                    if (situacion == 'contactados') {
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].envio_pedido_de_confirmacion) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total cancelados
                    if (situacion == 'cancelados') {
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].cancelo) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total confirmados                    
                    if (situacion == 'confirmados') {
                      for (i = 0; i < this.estados.length; i++) { 
                        //console.log('confirmados: '+this.estados[i].confirmo)
                        if (this.estados[i].confirmo && !this.estados[i].cancelo) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total voucher                    
                    if (situacion == 'voucher') {
                      for (i = 0; i < this.estados.length; i++) { 
                        //console.log('voucher: '+this.estados[i].envio_voucher)
                        if (this.estados[i].envio_voucher) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total recordatorio                    
                    if (situacion == 'recordatorio') {
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].envio_recordatorio) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total asistio                    
                    if (situacion == 'asistio') {
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].asistio) {
                          cant = cant + 1
                        }
                      }
                    }
                    
                  }
                  else {    

                    // total inscriptos
                    if (situacion == 'inscriptos') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos) {
                          cant = cant + 1
                        }
                        if (!this.estados[i].fecha_de_evento_id && this.select_fechas_de_eventos == 'mo') {
                          cant = cant + 1
                        }
                      }
                    }

                    // total contactados
                    if (situacion == 'contactados') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].envio_pedido_de_confirmacion) {
                          cant = cant + 1
                        }
                        if (!this.estados[i].fecha_de_evento_id && this.select_fechas_de_eventos == 'mo' && this.estados[i].envio_pedido_de_confirmacion) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total cancelados
                    if (situacion == 'cancelados') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].cancelo) {
                          cant = cant + 1
                        }
                        if (!this.estados[i].fecha_de_evento_id && this.select_fechas_de_eventos == 'mo' && this.estados[i].cancelo) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total confirmados
                    if (situacion == 'confirmados') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].confirmo && !this.estados[i].cancelo) {
                        //console.log('confirmados2: '+this.estados[i].confirmo)
                          cant = cant + 1
                        }
                        if (!this.estados[i].fecha_de_evento_id && this.select_fechas_de_eventos == 'mo' && this.estados[i].confirmo && !this.estados[i].cancelo) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total contactados
                    if (situacion == 'voucher') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].envio_voucher) {
                        //console.log('voucher2: '+this.estados[i].envio_voucher)
                          cant = cant + 1
                        }
                        if (!this.estados[i].fecha_de_evento_id && this.select_fechas_de_eventos == 'mo' && this.estados[i].envio_voucher) {
                          cant = cant + 1
                        }
                      }
                    }


                    // total recordatorio
                    if (situacion == 'recordatorio') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].envio_recordatorio) {
                          cant = cant + 1
                        }
                        if (!this.estados[i].fecha_de_evento_id && this.select_fechas_de_eventos == 'mo' && this.estados[i].envio_recordatorio) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total asistio
                    if (situacion == 'asistio') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].asistio) {
                          cant = cant + 1
                        }
                        if (!this.estados[i].fecha_de_evento_id && this.select_fechas_de_eventos == 'mo' && this.estados[i].asistio) {
                          cant = cant + 1
                        }
                      }
                    }

                  }
                  return cant
                },

                url_mensa_extra: function (celular, nombre, apellido, codigo_del_alumno) {
                  mensaje = encodeURI(this.mensaje_extra)
                  mensaje = mensaje.replaceAll('inscrito_nombre', nombre.trim())
                  mensaje = mensaje.replaceAll('inscrito_apellido', apellido.trim())
                  mensaje = mensaje.replaceAll('codigo_del_alumno', codigo_del_alumno)
                  url_mensa_extra = 'https://api.whatsapp.com/send?phone='+celular+'&text='+mensaje;
                  return url_mensa_extra
                },

                url_sms_mensa_extra: function (celular, nombre, apellido, codigo_del_alumno) {
                  mensaje = encodeURI(this.mensaje_extra)
                  mensaje = mensaje.replaceAll('inscrito_nombre', nombre.trim())
                  mensaje = mensaje.replaceAll('inscrito_apellido', apellido.trim())
                  mensaje = mensaje.replaceAll('codigo_del_alumno', codigo_del_alumno)
                  url_mensa_extra = 'sms:'+celular+'?body='+mensaje;
                  return url_mensa_extra
                },



                calc_prioridad: function (i) {
                  prioridad = 3
                  if (this.estados[i].confirmo && this.fecha_hoy == this.estados[i].fecha_de_inicio && !this.estados[i].envio_recordatorio) {
                    prioridad = 1
                  }
                  else {
                    if (!this.estados[i].envio_pedido_de_confirmacion && this.estados[i].fecha_de_inicio_id != null) {
                      prioridad = 2
                    }
                  }
                 return prioridad
                },


                mostrar_supero_cupo: function () {
                  
                  var mostrar = false
                  for (i = 0; i < this.fechas_de_evento.length; i++) { 

                    if (this.fechas_de_evento[i].id != 'todos' && this.fechas_de_evento[i].id != -1 && this.fechas_de_evento[i].id != 'mo') {
                      supero_cupo = this.supero_cupo(this.fechas_de_evento[i].id)[0]
                      if (supero_cupo) {
                        mostrar = true
                      }
                    }
                  }
                  
                  return mostrar

                },

                supero_cupo: function (id) {

                  var cant = 0
                  // total confirmados              
                  for (i = 0; i < this.estados.length; i++) { 
                    if (this.estados[i].fecha_de_evento_id == id && this.estados[i].confirmo && !this.estados[i].cancelo) {
                      cant = cant + 1
                    }
                  }
                  //console.log('cant: '+cant)
                  var j = -1
                  for (i = 0; i < this.fechas_de_evento.length; i++) { 
                    if (this.fechas_de_evento[i].id == id) {
                      j = i;
                    }
                  }

                  var excedio = false
                  var mensaje = ''

                  if (cant >= this.fechas_de_evento[j].cupo_maximo*2) {
                    excedio = true
                    mensaje = '<?php echo __('Cupo excedido, cupo máximo') ?>'+': '+this.fechas_de_evento[j].cupo_maximo+' '+'<?php echo __('Confirmados') ?>'+': '+ cant
                  }
                  else {
                    excedio = false
                  }

                  return [excedio, mensaje]
                },


                crearListasDeContactos: function () {
                  //Filtro la tabla segun el valor del select de arriba
                  resto = this.cant_total_inscriptos%this.cant_x_pagina
                  //console.log('resto: '+resto)
                  if (resto > 0) {
                    sumar = 1
                  }
                  else {
                    sumar = 0
                  }
                  cant_listas = parseInt(this.cant_total_inscriptos/this.cant_x_pagina)+sumar
                  this.listas_de_contactos = []
                  j = 0
                  for (i = cant_listas; i >= 1; i--) { 
                    j++
                    this.listas_de_contactos.push({titulo: 'Grupo '+i, url: '<?php echo $dominio_publico ?>f/contactDown/<?php echo $Solicitud->id; ?>/pagina/'+j+'/1/'+this.cant_x_pagina+'/<?php echo $Solicitud->hash; ?>'})
                    }
                },
                

                urlgrupo: function (nro_de_grupo) {
                 url = '<?php echo $dominio_publico ?>f/contactDown/<?php echo $Solicitud->id; ?>/grupo/'+nro_de_grupo+'/1/9999999/<?php echo md5(ENV('PREFIJO_HASH').$Solicitud->id) ?>'
                 return url
                },
                  
              },

              computed: {      
                
                verificar_advertencias: function () {
                    this.lista_de_advertencias = []
                    inscriptos = this.contar_cant_inscriptos('inscriptos')
                    contactados = this.contar_cant_inscriptos('contactados')
                    cancelados = this.contar_cant_inscriptos('cancelados')
                    resta_contactar = inscriptos-(contactados + cancelados)
                    if (resta_contactar > 0) {
                      this.lista_de_advertencias.push('<?php echo '<strong>'.__('Resta contactar a').' '; ?>'+resta_contactar+'<?php echo '    ('; ?>'+inscriptos+'<?php echo' '.__('Inscriptos').' | '; ?>'+contactados+'<?php echo' '.__('contactados').' | '; ?>'+cancelados+'<?php echo' '.__('Cancelados').')</strong>. <p class="info_mensaje">'.__('No deben quedar inscriptos (no cancelados) sin contactar').'</p>'; ?>')
                    }
                    return this.lista_de_advertencias                    
                },

                cant_inscriptos: function () {
                 cant = this.contar_cant_inscriptos('inscriptos')
                 return cant
                },
                
                cant_contactados: function () {
                 cant = this.contar_cant_inscriptos('contactados')
                 return cant
                },

                cant_cancelados: function () {
                 cant = this.contar_cant_inscriptos('cancelados')
                 return cant
                },
                
                cant_confirmados: function () {
                 cant = this.contar_cant_inscriptos('confirmados')
                 return cant
                },
                
                cant_voucher: function () {
                 cant = this.contar_cant_inscriptos('voucher')
                 return cant
                },
                
                cant_recordatorio: function () {
                 cant = this.contar_cant_inscriptos('recordatorio')
                 return cant
                },

                cant_asistentes: function () {
                 cant = this.contar_cant_inscriptos('asistio')
                 return cant
                },
                 
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

    <!-- MODAL ABM -->
      <div class="modal modal fade" id="modal-solicitud-abm">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><div id="modal-titulo-solicitud-abm">Modificar</div></h4>
            </div>
            <div class="modal-body" id="modal-bodi-abm">

            </div>

          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <!-- MODAL ABM -->

    <!-- MODAL FECHA -->
      <div class="modal modal fade" id="modal-fecha-de-evento">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Indique a que horario desdea trasladar al inscripto') ?></div></h4>
            </div>
            <div class="modal-body" id="modal-bodi-fecha-de-evento">
                  {!! Form::open(array
                    (
                    'action' => 'FormController@cambiarDeHorarioAInscripto', 
                    'role' => 'form',
                    'method' => 'POST',
                    'id' => "form_gen_modelo",
                    'enctype' => 'multipart/form-data',
                    'class' => 'form-group',
                    'ref' => 'form'
                    )) 
                  !!}              
                    <?php 
                      if ($Fechas_de_evento <> null) {
                        foreach ($Fechas_de_evento as $Fecha_de_evento) { ?>

                      <div class="radio">
                        <label>
                          <input type="radio" name="fecha_de_evento_id" value="<?php echo $Fecha_de_evento->id; ?>"> <?php echo $Fecha_de_evento->armarDetalleFechasDeEventos('select', true, $Idioma_por_pais, $Solicitud, $idioma) ?>
                        </label>
                      </div>
                    <?php 
                        } 
                      }
                    ?>  
                      
                    <?php if ($Solicitud->tipo_de_evento_id <> 3 or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4)) { ?>
                      <div class="radio" v-show="mostrar_fechas">
                        <label>
                          <input type="radio" name="fecha_de_evento_id" value="NP"> <?php echo $mensaje_np ?>
                        </label>
                      </div>
                    <?php } ?> 
                      
                    <?php if ($Solicitud->sino_habilitar_modalidad_online == 'SI') { ?>
                      <div class="radio" v-show="mostrar_fechas">
                        <label>
                          <input type="radio" name="fecha_de_evento_id" value="MO"> <?php echo $mensaje_mo ?>
                        </label>
                      </div>
                    <?php } ?>  

                      <input type="hidden" name="inscripcion_id_modificar_fecha" id="inscripcion_id_modificar_fecha" value="">
                      <button type="submit" class="btn btn-default"><?php echo __('Modificar') ?></button>
                  {!! Form::close() !!}            
            </div>

          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <!-- MODAL FECHA -->

    <!-- MODAL CAMBIO SOLICITUD -->
      <div class="modal modal fade" id="modal-cambio-de-solicitud">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Mover el Alumno a otra solicitud') ?></div></h4>
            </div>
            <div class="modal-body" id="modal-bodi-fecha-de-evento">
                  {!! Form::open(array
                    (
                    'action' => 'FormController@cambiarDeSolicitudAInscripto', 
                    'role' => 'form',
                    'method' => 'POST',
                    'id' => "form_gen_modelo",
                    'enctype' => 'multipart/form-data',
                    'class' => 'form-group',
                    'ref' => 'form'
                    )) 
                  !!}              

                      
                      <form>
                        <div class="form-group">
                          <label>ID <?php echo __('Solicitud') ?>:</label>
                          <input class="form-control" type="text" name="solicitud_id_modificar" id="solicitud_id_modificar" value="">
                        </div>
                        <div class="form-group">
                          <label><?php echo __('Causa de cambio') ?>: </label>
                          <select class="form-control" name="causa_de_cambio_de_solicitud_id"  style="color: #000">
                            <option value="7" required="required"></option>
                            <?php foreach ($Causas_de_cambio_de_solicitud as $Causa_de_cambio_de_solicitud) {  ?>
                            <option value="<?php echo $Causa_de_cambio_de_solicitud->id ?>">
                              <?php echo $Causa_de_cambio_de_solicitud->causa_de_cambio_de_solicitud ?>
                            </option>
                            <?php } ?>
                          </select>      
                        </div>
                        <input type="hidden" name="inscripcion_id_modificar" id="inscripcion_id_modificar" value="">
                        <button type="submit" class="btn btn-default"><?php echo __('Modificar') ?></button>
                      </form>

                      
                      
                  {!! Form::close() !!}            
            </div>

          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <!-- MODAL CAMBIO SOLICITUD -->



    <!-- MODAL TRAER LECCIONES -->
      <div class="modal modal fade" id="modal-traer-lecciones">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Lecciones vistas') ?></div></h4>
            </div>
            <div class="modal-body" id="modal-bodi-traer-lecciones">

            </div>
            <div class="modal-footer" id="modal-footer-traer-lecciones">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cerrar') ?></button>
            </div>

          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <!-- MODAL TRAER LECCIONES -->


    <!-- MODAL TP REALIZADOS -->
      <div class="modal modal fade" id="modal-tp-realizados">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><div id="modal-titulo"><?php echo __('TP Realizados') ?></div></h4>
            </div>
            <div class="modal-body" id="modal-bodi-tp-realizados">

            </div>
            <div class="modal-footer" id="modal-footer-tp-realizados">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cerrar') ?></button>
            </div>

          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <!-- MODAL TP REALIZADOS -->




    <!-- FUNCIONES ABM Y MODIFICAR INSCRIPCION -->
      <?php 
      $gen_seteo = array(
        'gen_url_siguiente' => 'back', 
        'no_mostrar_campos_abm' => 'solicitud_id|sino_notificar_proximos_eventos|sino_acepto_politica_de_privacidad|sino_envio_pedido_de_confirmacion|sino_confirmo|sino_envio_recordatorio_pedido_de_confirmacion|sino_envio_voucher|sino_envio_motivacion|sino_envio_recordatorio|sino_asistio|fecha_de_evento_id|sino_envio_recordatorio_proxima_clase|sino_envio_recordatorio_proxima_clase_a_no_asistente|sino_cancelo|sino_contesto_consulta|sino_invitado_al_curso_online|campania_id|sino_es_organico|sino_form_corto|cp_form_id|cp_campaign_name|cp_ad_set_name|cp_ad_name|sino_es_lead|pais_iso|sino_envio_1|sino_envio_2|sino_envio_3|sino_envio_4|sino_envio_5|sino_envio_6|sino_envio_7|sino_envio_8|sino_envio_9|sino_envio_10|sino_envio_certificado|causa_de_baja_id|ultima_leccion_vista|canal_de_recepcion_del_curso_id|apellido2|nombre2|grupo'
          );
      ?>   
           
      <script type="text/javascript">

        function crearABM_inscripcion(gen_modelo, gen_accion, gen_id = null) {

          gen_seteo = '<?php echo serialize($gen_seteo) ?>'
          $.ajax({
            url: '<?php echo $dominio_publico ?>crearabm',
            type: 'POST',
            dataType: 'html',
            async: true,
            data:{
              _token: "{{ csrf_token() }}",
              gen_modelo: gen_modelo,
              gen_seteo: gen_seteo,
              gen_opcion: '',
              gen_accion: gen_accion,
              gen_id: gen_id
            },
            success: function success(data, status) {        
              $("#modal-bodi-abm").html(data);
              if (gen_accion == 'a') {
                $("#modal-titulo-solicitud-abm").html('Insertar '+gen_modelo);
              }
              if (gen_accion == 'm') {
                $("#modal-titulo-solicitud-abm").html('Modificar '+gen_modelo);
              }
              if (gen_accion == 'b') {
                $("#modal-titulo-solicitud-abm").html('Borrar '+gen_modelo);
              }

            },
            error: function error(xhr, textStatus, errorThrown) {
                alert('Error de Conectividad 1, revise su conección a Internet');
            }
          });
        }

        $( document ).ready(function() {
            //$('input[type="search"]').val(1111);
        });

      </script>
    <!-- FUNCIONES ABM Y MODIFICAR INSCRIPCION -->      





    <!-- MODAL MENSAJES MODELOS -->
      <div class="modal modal fade" id="modal-mensajes-modelos">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><div id="modal-modal-mensajes-modelos-titulo"><?php echo __('Mensajes Modelos') ?></div></h4>
            </div>

            <div class="modal-body" id="modal-bodi-mensajes-modelos-titulo"> 

              <!-- MENSAJES GRUPO -->
                <div class="box box-primary box-solid collapsed-box">
                  <div class="box-header with-border" data-widget="collapse" style="cursor: pointer;">
                    <h3 class="box-title"><i class="fa fa-whatsapp"></i> <?php echo __('Mensajes para el Grupo') ?></h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool"><i class="fa fa-plus"></i>
                      </button>
                    </div>
                    <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body" style="display: none;">
                    <?php foreach ($Modelos_grupo as $Modelo) { ?>
                      <a class="btn btn-block btn-success btn-social" href="<?php echo $Modelo['url_texto_modelo'] ?>" target="_blank">
                        <i class="fa fa-whatsapp"></i> <?php echo $Modelo['titulo_del_mensaje'] ?>
                      </a>
                      <p class="text-green"><?php echo $Modelo['aclaracion'] ?></p>
                      
                      <hr>
                    <?php } ?>
                  </div>
                  <!-- /.box-body -->
                </div>
              <!-- FIN MENSAJES GRUPO -->


              <!-- LECCIONES -->
                <div class="box box-primary box-solid collapsed-box">
                  <div class="box-header with-border" data-widget="collapse" style="cursor: pointer;">
                    <h3 class="box-title"><i class="fa fa-whatsapp"></i> <?php echo __('Lecciones') ?></h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool"><i class="fa fa-plus"></i>
                      </button>
                    </div>
                    <!-- /.box-tools -->
                  </div>
                  <!-- /.box-header -->
                  <div class="box-body" style="display: none;">
                    <?php foreach ($texto_lecciones_de_curso as $texto_leccion) { ?>
                      <a class="btn btn-block btn-success btn-social" href="<?php echo $texto_leccion['url_whatsapp_texto'] ?>" target="_blank">
                        <i class="fa fa-whatsapp"></i> <?php echo $texto_leccion['nombre_de_la_leccion'] ?> <strong style="float: right"><?php echo __('Enviar') ?>: 
                          <?php
                          $fecha_de_envio_de_leccion = $gCont->FormatoFecha($texto_leccion['fecha_de_envio']); 
                          if ($fecha_de_envio_de_leccion == '04/05/2020' and $Solicitud->tipo_de_curso_online_id == 5 and $texto_leccion['nombre_de_la_leccion'] == 'Introducción al Curso de Auto-Conocimiento') {
                            $fecha_de_envio_de_leccion = '05/05/2020';
                          }
                          echo $fecha_de_envio_de_leccion;
                          ?></strong>
                      </a>
                    <?php } ?>
                  </div>
                  <!-- /.box-body -->
                </div>
              <!-- FIN LECCIONES -->
              

            </div>

            <div class="modal-footer">
              <center>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cerrar') ?></button>
              </center>  
              <input type="hidden" name="sino_aprobado_administracion" value="NO">
            </div>

          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <!-- MODAL FLYERS -->
    </body>
</html>
