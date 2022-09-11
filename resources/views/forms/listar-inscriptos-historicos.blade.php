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

function sino_a_tfinverso($sino) {
  if ($sino == 'SI') {
    $tf = 'false';
  }
  else {
    if ($sino == 'NO') {
        $tf = 'true';
      }
    else {
        $tf = 'null';
      }
  }

  return $tf;
}


function id_a_tf($id) {
  if ($id > 0) {
    $tf = 'true';
  }
  else {
    $tf = 'null';
  }

  return $tf;
}

$enviar_mail = 'false';
if (!Auth::guest()) {
    if(Auth::user()->id == 1 or Auth::user()->id == 33) {
      $enviar_mail = 'true';
    }
}


$tel_responsable_inscripcion = $Solicitud->celular_responsable_de_inscripciones;
$nombre_de_ciudad = $Solicitud->localidad_nombre();
$nombre_responsable_de_inscripciones = $Solicitud->nombre_responsable_de_inscripciones;
$tipo_de_evento_id = $Solicitud->tipo_de_evento_id;
$descripcion_sin_estado = __($Solicitud->descripcion_sin_estado(false));
$url_form_inscripcion = $Solicitud->url_form_inscripcion_contacto_historico();
$nombre_responsable_inscripcion = $Solicitud->nombre_responsable_de_inscripciones;
$Idioma_por_pais = $Solicitud->idioma_por_pais();

if ($Idioma_por_pais->pais_id > 0) {
  $codigo_tel = $Idioma_por_pais->pais->codigo_tel;
  if ($parametros_paginacion == 'historico') {
    if ($Solicitud->envio_de_invitacion_a_contactos_historicos <> '') {
      $mensaje_extra = $Solicitud->envio_de_invitacion_a_contactos_historicos;
    }
    else {
      $mensaje_extra = $Idioma_por_pais->modelo_de_mensaje->envio_de_invitacion_a_contactos_historicos;
    }
  }
  else {
    $mensaje_extra = $Idioma_por_pais->modelo_de_mensaje->envio_de_invitacion_a_contactos_recupero;   
  }
  $nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;
}
else {
  $codigo_tel = '';
  $mensaje_extra = '';
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
        <title><?php echo $titulo_planilla ?></title>

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
              <h3 class="box-title"><?php echo $titulo_planilla ?></h3>
              <p class="bg-info">
            </p>


            <?php if ($Solicitud->tipo_de_evento_id <> 3) { ?>
            <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">            
              <a href="<?php echo ENV('PATH_PUBLIC') ?>mostrar-flyers/<?php echo $Solicitud->id ?>" class="btn btn-block btn-social btn-instagram" style="margin-top: 10px;" target="_blank">
                    <i class="fa fa-instagram"></i> <?php echo __('Flyers') ?>
                </a>   
            </div>
            <?php } ?>


            <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">  
              <div class="btn btn-block btn-social btn-instagram" data-toggle="modal" data-target="#modal-columnas" target="_blank" style="margin-top: 10px;">
                <i class="fa fa-columns"></i> 
                <span class="hidden-xs"><?php echo __('Habilitar columnas') ?></span>
                <span class="hidden-lg hidden-sm hidden-md"><?php echo __('Columnas') ?></span>
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
                <div class="col-xs-12 col-lg-6">
                  <div style="margin-top: 10px;">
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
                      <input type="hidden" name="historico" value="<?php echo $historico ?>">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary btn-flat">Buscar en toda la planilla</button>
                      </span>
                    </div>
                  {!! Form::close() !!} 
                </div>
                </div>
              <?php } ?>



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
                      <a href="<?php echo $dominio_publico ?>f/ipaginar/<?php echo $Solicitud->id ?>/<?php echo $Solicitud->hash ?>/<?php echo $j ?>/<?php echo $parametros_paginacion ?>">
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

                    if ($habilitar_derivacion) {
                      $parametros_paginacion_ver_todo = $parametros_paginacion;
                    }
                    else {
                      $parametros_paginacion_ver_todo = "1";
                    }
                    ?>

                    <a href="<?php echo $dominio_publico ?>f/ipaginar/<?php echo $Solicitud->id ?>/<?php echo $Solicitud->hash ?>/all/<?php echo $parametros_paginacion_ver_todo ?>">
                      <button type="button" class="btn btn-default <?php echo $class_active ?>"><?php echo __('Ver todos') ?></button>
                    </a>

                    



                  </div>   
                </div>      
              <?php } ?>

              <div class="col-xs-12 col-lg-12">
                <h4><i class="icon fa fa-file-text-o"></i> <?php echo __('Mensaje a enviar') ?></h4>  
                <textarea id="mensaje_extra" v-model="mensaje_extra" rows="6" name="mensaje_extra" class="form-control" placeholder="<?php echo __('Indique el mensaje personalizado que quiere enviar') ?>"></textarea>
                <button type="button" class="btn btn-primary btn-xs" v-on:click="guardarMensajeAEnviar()"><i class="fa fa-fw fa-save" style="font-size: 19px"></i></button>  
                <span v-html="mensaje_a_enviar_status_save"></span>

              </div>


              
              
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              

              <table id="table" class="table table-bordered table-striped" style="max-width: 500px" >
                  <thead>
                      <tr>
                          <th v-show="show_col_id"><?php echo __('ID') ?></th>
                          <th v-show="show_col_nro_orden"><?php echo __('Nro de Orden') ?></th>
                          <th v-show="show_col_ciudad"><?php echo __('Ciudad') ?></th>
                          <th v-show="show_col_grupo"><?php echo __('Grupo de whatsapp') ?></th>
                          <th v-show="show_col_prioridad"><?php echo __('Prioridad') ?></th>
                          <th v-show="show_col_comprimido"><?php echo __('Datos') ?></th>
                          <th v-show="show_col_fecha"><?php echo __('Fecha') ?></th>
                          <th v-show="show_col_apellido"><?php echo __('Apellido') ?></th>
                          <th v-show="show_col_nombre"><?php echo __('Nombre') ?></th>
                          <th v-show="show_col_celular"><?php echo __('Celular') ?></th>
                          <th v-show="show_col_celular"></th>
                          <th v-show="show_col_email_correo"><?php echo __('Correo') ?></th>
                          <th v-show="show_col_pais"><?php echo __('Pais') ?></th>
                          <th v-show="show_col_estado"><?php echo __('Estado') ?></th>
                          <th v-show="show_col_idioma"><?php echo __('Idioma') ?></th>
                          <th><?php echo __('Acción') ?></th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php 
                    $i = -1;
                    foreach ($Inscripciones as $nro_de_orden => $Inscripcion) { 
                      $i++;
                      $nombre = str_replace(array("\n", "\t", "\r"), '', str_replace("'", '’', htmlentities($Inscripcion->nombre)));
                      $apellido = str_replace("'", '’', htmlentities($Inscripcion->apellido));
                      ?>

                        <tr v-show="mostrarFila(<?php echo $i ?>)">
                            <td v-show="show_col_id"><?php echo $Inscripcion->id; ?></td>
                            <td v-show="show_col_nro_orden"><?php echo $nro_de_orden+1; ?></td>
                            <td v-show="show_col_ciudad"><?php echo $Inscripcion->ciudad; ?></td>
                            <td v-show="show_col_grupo"><?php echo $Inscripcion->grupo; ?></td>
                            <td v-show="show_col_prioridad">{{ calc_prioridad(<?php echo $i ?>) }}</td>
                            <td v-show="show_col_comprimido">
                              <div style="float: right;">
                                <?php if ($parametros_paginacion <> 'historico') {?>
                                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-cambio-de-solicitud" onclick="$('#inscripcion_id_modificar').val(<?php echo $Inscripcion->id ?>);" style="margin-left: 10px; cursor: pointer;">
                                    <?php echo __('Derivar') ?>
                                  </button>
                                <?php } ?>
                              </div>

                              <div class="btn-group" style="float: right;">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                  <span class="caret"></span>
                                  <?php echo __('Acciones') ?>
                                  <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                  <li data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_inscripcion('Inscripcion', 'm', <?php echo $Inscripcion->id ?>)" style="padding: 5px; cursor: pointer;">
                                    <a><?php echo __('Modificar') ?> <?php echo __('Datos') ?></a>
                                  </li>
                                  <li>
                                    <a href="<?php echo $dominio_publico ?>f/contactDown/<?php echo $Solicitud->id; ?>/inscripcion/<?php echo $Inscripcion->id; ?>/1/1/<?php echo $Solicitud->hash; ?>" target="_blank">
                                      <?php echo __('Agendar Contacto vCard') ?>
                                    </a>
                                  </li>
                                </ul>
                              </div>


                             
                                                       


                              ID: <?php echo $Inscripcion->id ?> | <?php echo __('Fecha').': '.$gCont->FormatoFechayYHora($Inscripcion->created_at); ?> <br>       
                              <strong><?php echo __('Solicitud') ?> ID: <?php echo $Inscripcion->solicitud_id ?></strong><br>
                              <?php echo __('Codigo del alumno') ?>: <?php echo $Inscripcion->codigo_alumno ?><br>
                              <?php echo $nombre; ?> <?php echo $apellido; ?>  | <?php echo $Inscripcion->email_correo; ?><br>
                              <?php echo __('Celular').': '; ?> 
                              <input type="text" v-model="estados[<?php echo $i ?>].celular"> 
                              <button type="button" class="btn btn-primary btn-xs" v-on:click="guardarCel(<?php echo $i ?>)"><i class="fa fa-fw fa-save" style="font-size: 19px"></i></button>  
                              <!--a href="https://api.whatsapp.com/send?phone=<?php echo $Inscripcion->celular_wa($codigo_tel); ?>" target="_blank">
                                <button type="button" class="btn btn-success btn-xs"><i class="fa fa-fw fa-whatsapp" style="font-size: 19px"></i></button>
                              </a-->
                              <span v-html="estados[<?php echo $i ?>].celular_status_save"></span>
                              <br>
                                <?php echo __('Pais') ?>: <?php echo $Inscripcion->nombre_pais; ?> | <?php echo __('Ciudad') ?>: <?php echo $Inscripcion->ciudad; ?><br>
                              <i> 
                            </i>                               
                                                    
                            <?php if ($Inscripcion->canal_de_recepcion_del_curso <> '') { ?>
                              <?php echo __('En que app te gustaria recibir el curso') ?>: <?php echo $Inscripcion->canal_de_recepcion_del_curso ?>                        
                            <br>     
                            <?php } ?>
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


                              <?php if ($Inscripcion->consulta <> '') {?>
                                <p style="color: red; max-width: 400px"><strong><?php echo __('tu consulta') ?>: <?php echo $Inscripcion->consulta ?></strong></p>
                              <?php } ?>
                              <br><br>
                                <p>
                                  <textarea id="observaciones" v-model="estados[<?php echo $i ?>].observaciones" rows="2"  name="observaciones" class="form-control" placeholder="<?php echo __('Observaciones') ?>" v-on:change="estados[<?php echo $i ?>].observaciones = codificarCadena(estados[<?php echo $i ?>].observaciones)" maxlength="255"></textarea>
                                  <button type="button" class="btn btn-primary btn-xs" v-on:click="guardarObs(<?php echo $i ?>)"><i class="fa fa-fw fa-save" style="font-size: 19px"></i> <?php echo __('Guardar') ?> <?php echo __('Observaciones') ?></button>  
                                  <span v-html="estados[<?php echo $i ?>].obs_status_save"></span>
                                </p>
                              
                              <?php 
                              if ($Inscripcion->solicitud_original <> '' and $Inscripcion->solicitud_original <> $Solicitud->id) { 
                                echo '<br><br>'.$Inscripcion->planilla_original();
                              } 
                              ?>
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
                            <td v-show="show_col_pais"><?php echo $Inscripcion->nombre_pais; ?></td> 
                            <td v-show="show_col_idioma"><?php echo $Inscripcion->idioma; ?></td> 
                            <td>

                              <!-- ENVIO DE PEDIDO DE CONFIRMACION -->
                                <div v-show="!estados[<?php echo $i ?>].notificar_proximos_eventos" v-bind:class="class_sino(estados[<?php echo $i ?>].envio_contacto_historico)">
                                  
                                    <a v-bind:href="url_mensa_extra('<?php echo $Inscripcion->celular_wa($codigo_tel, $Solicitud) ?>', '<?php echo $nombre; ?>', '<?php echo $apellido ?>', '<?php echo $Inscripcion->codigo_alumno ?>')" target="_blank">
                                      <button type="button" class="btn btn-blanco" alt="enviar" title="enviar" v-on:click="marcar_envio(1, 25, <?php echo $i; ?>, <?php echo $Inscripcion->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                    </a>
                                    <!--a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(25, '<?php echo $nombre; ?>', '<?php echo $apellido; ?>', <?php echo $Inscripcion->id ?>, '<?php echo __('Pedido de confirmación') ?>', <?php echo $i ?>)">
                                      <button type="button" class="btn btn-blanco" alt="enviar" title="enviar"><i class="fa fa-envelope-o"></i></button>
                                    </a-->
                                    <?php echo __('Envío de invitación') ?>
                                </div>



                              <!-- NO DESEA MAS INFO  -->                              
                              <div v-bind:class="class_sino_cancelo(estados[<?php echo $i ?>].notificar_proximos_eventos)" style="margin-top: 30px; padding-top: 0px; padding-bottom: 0px;">
                                <h4 style="color: white"><i class="icon fa fa-ban"></i> <?php echo __('no desea recibir mas informacion ') ?>
                                  <label class="switch switch-inscripcion">
                                    <input  type="checkbox" v-on:change="setearSino(26, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].notificar_proximos_eventos">
                                    <span class="slider round"></span>
                                    
                                  </label></h4> 
                              </div> 


                            </td>


                            <td v-show="show_col_estado"><!--span class="badge bg-light-blue datos-finales-asistente">{{ span_estado(<?php echo $i ?>) }}</span-->
                              {{ span_estado(<?php echo $i ?>) }}
                            </td>


                        </tr>
                    <?php } ?>
                  </tbody>
                  </table>


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

                <!--div class="col-lg-12">            
                  <pre>@{{ $data }}</pre>
                </div-->  

            </div>
            <!-- /.box-body -->
          </div>
      </div>


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
                  <th><?php echo __('Idioma') ?></th>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_idioma">
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
                mensaje_error: '',
                desabilitar: '',
                show_col_id: false,
                show_col_nro_orden: false,
                show_col_prioridad: false,
                show_col_comprimido: true,
                show_col_fecha: false,
                show_col_apellido: false,
                show_col_nombre: false,
                show_col_celular: false,
                show_col_email_correo: false,
                show_col_pais: false,
                show_col_ciudad: false,
                show_col_estado: false,
                show_col_idioma: true,
                show_col_grupo: false,
                mensaje_extra: <?php echo json_encode($mensaje_extra) ?>,
                mensaje_a_enviar_status_save: null,
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
                estados: [
                <?php 
                foreach ($Inscripciones as $Inscripcion) { 
                  $hay_consulta = 'false';
                  if ($Inscripcion->consulta <> '') {
                    $hay_consulta = 'true';
                  }
                  $fecha_de_inicio = 'null';


                  if (!isset($ocultar_certificados) or !$ocultar_certificados) {
                    $ocultar_certificados = false;
                  }
                  else {
                    $ocultar_certificados = true;
                  }
                ?>
                  {
                    inscripcion_id: <?php echo $Inscripcion->id ?>,
                    prioridad: 1,
                    cancelo: <?php echo sino_a_tf($Inscripcion->sino_cancelo) ?>,
                    notificar_proximos_eventos: <?php echo sino_a_tfinverso($Inscripcion->sino_notificar_proximos_eventos) ?>,                    
                    envio_contacto_historico: <?php echo id_a_tf($Inscripcion->envio_id) ?>,
                    causa_de_cambio_de_solicitud_id: '<?php echo $Inscripcion->causa_de_cambio_de_solicitud_id ?>',
                    celular: '<?php echo $Inscripcion->celular ?>',
                    celular_status_save: '',
                    observaciones: '<?php echo $Inscripcion->observaciones ?>',
                    obs_status_save: '',
                  },
                <?php } ?>
                ],
                cant_x_pagina: 100,
                listas_de_contactos: [],
                valor_select_ver: 'todos',
                select_ver: [
                    { detalle: '<?php echo __('Ver todos') ?>', id: 'todos'},
                    { detalle: '<?php echo __('Desea recibir mas informacion') ?>', id: 'ocultar_no_mas_info'},
                    { detalle: '<?php echo __('No desea recibir mas informacion') ?>', id: 'solo_no_mas_info'},
                    { detalle: '<?php echo __('Sin contactar') ?>', id: 'sin_contactar'},
                ],
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
                  if (this.valor_select_ver == 'ocultar_no_mas_info' && !this.estados[i].notificar_proximos_eventos) {
                    mostrar = true
                  }
                  if (this.valor_select_ver == 'solo_no_mas_info' && this.estados[i].notificar_proximos_eventos) {
                    mostrar = true
                  }
                  if (this.valor_select_ver == 'sin_contactar' && !this.estados[i].envio_contacto_historico) {
                    mostrar = true
                  }
                  if (this.valor_select_ver == 'todos') {
                    mostrar = true
                  }
                

                  return mostrar
                  
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

                guardarMensajeAEnviar: function () {
                  app["mensaje_a_enviar_status_save"] = '<img src="<?php echo $dominio_publico ?>img/cargando.gif" width="30px">'
                  $.ajax({
                    url: '<?php echo $dominio_publico ?>f/i/guardar-mensaje-a-enviar',
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      solicitud_id: <?php echo $Solicitud->id ?>,
                      mensaje_extra: this.mensaje_extra,
                    },
                    success: function success(data, status) {   
                      app["mensaje_a_enviar_status_save"] = data
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 10, revise su conección a Internet - Error: '+errorThrown);
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
                  console.log('codigo: '+codigo);
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

                  if (codigo == 25) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].envio_contacto_historico;  
                      }
                      this.estados[i].envio_contacto_historico = !estado_tf; 
                  }

                  if (codigo == 26) {
                      if (estado_tf == null) {
                        estado_tf = this.estados[i].notificar_proximos_eventos;  
                      }
                      this.estados[i].notificar_proximos_eventos = estado_tf; 
                  }

                  console.log('estado_tf0: '+estado_tf)
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
                      console.log('data1: '+data)
                      console.log('estado_tf: '+estado_tf)
                      if (codigo == 1) {
                        if (estado_tf == null) {
                          estado_tf = app["estados"][i].envio_pedido_de_confirmacion;
                          console.log('estado_tf2: '+estado_tf)
                          app["estados"][i].envio_pedido_de_confirmacion = !estado_tf;  
                          console.log('estado_tf3: '+app["estados"][i].envio_pedido_de_confirmacion)

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
                      console.log('paso 2')
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert('Error de Conectividad 2, revise su conección a Internet');
                    }
                  });


                },
                

                

                preparar_envio_mail: function (codigo, nombre, apellido, inscripcion_id, asunto, i) {
                  $('#modal-bodi-confirmar-mail').html('')
                  $('#btn_enviar_mail').show();
                  console.log('entro preparar_envio_mail')
                  console.log('Nombre: '+nombre)
                  this.email_nombre = nombre
                  this.email_apellido = apellido
                  this.email_codigo = codigo
                  this.email_inscripcion_id = inscripcion_id
                  this.email_i = i
                  this.email_asunto = asunto
                  console.log("codigo: "+codigo+" - asunto: "+asunto)
                },

                procesar_envio_mail: function () {

                  //this.marcar_envio(2, this.email_codigo, this.email_i, this.email_inscripcion_id)

                  if (this.email_codigo == 25) {
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

                  console.log('paso 1')
                  this.setearSino(codigo, i, this.estados[i].inscripcion_id, true)
                  console.log('paso 3')
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
                        console.log('data:2'+data)

                        if (codigo == 1) {
                            app["estados"][i].envio_pedido_de_confirmacion = true; 
                            console.log('eeee: '+app["estados"][i].envio_pedido_de_confirmacion)
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
                        if (codigo == 25) {
                            app["estados"][i].envio_contacto_historico = true;  
                        }
                        
                        console.log('paso 4')
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
                

                url_mensa_extra: function (celular, nombre, apellido, codigo_del_alumno) {
                  mensaje = encodeURI(this.mensaje_extra)
                  mensaje = mensaje.replaceAll('inscrito_nombre', nombre.trim())
                  mensaje = mensaje.replaceAll('inscrito_apellido', apellido.trim())
                  mensaje = mensaje.replaceAll('codigo_del_alumno', codigo_del_alumno)
                  mensaje = mensaje.replaceAll('nombre_de_la_institucion', '<?php echo $nombre_de_la_institucion ?>')
                  mensaje = mensaje.replaceAll('txt_tipo_de_evento', '<?php echo $descripcion_sin_estado ?>')
                  mensaje = mensaje.replaceAll('url_form_inscripcion', '<?php echo $url_form_inscripcion ?>')
                  mensaje = mensaje.replaceAll(/icon_4diamantes/g, '💠')
                  mensaje = mensaje.replaceAll(/icon_check/g, '✅')
                  mensaje = mensaje.replaceAll(/icon_manito/g, '👉')
                  mensaje = mensaje.replaceAll(/nombre_responsable_inscripcion/g, '<?php echo $nombre_responsable_inscripcion ?>')
                  url_mensa_extra = 'https://api.whatsapp.com/send?phone='+celular+'&text='+mensaje;
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




                crearListasDeContactos: function () {
                  //Filtro la tabla segun el valor del select de arriba
                  resto = this.cant_total_inscriptos%this.cant_x_pagina
                  console.log('resto: '+resto)
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
              },  
            )
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

          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <!-- MODAL FECHA -->

    <?php if (isset($habilitar_derivacion) and $habilitar_derivacion) {?>
    <!-- MODAL CAMBIO SOLICITUD -->
      <div class="modal modal fade" id="modal-cambio-de-solicitud">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Derivar el Alumno a otra solicitud') ?></div></h4>
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
                          <input class="form-control" type="text" name="solicitud_id_modificar" id="solicitud_id_modificar" value="11000">
                        </div>
                        <div class="form-group">
                          <label><?php echo __('Causa de cambio') ?>: </label>
                          <select class="form-control" name="causa_de_cambio_de_solicitud_id"  style="color: #000">
                            <option value="7" required="required"></option>
                            <?php foreach ($Causas_de_cambio_de_solicitud as $Causa_de_cambio_de_solicitud) {  ?>
                            <option value="<?php echo $Causa_de_cambio_de_solicitud->id ?>" selected>
                              <?php echo $Causa_de_cambio_de_solicitud->causa_de_cambio_de_solicitud ?>
                            </option>
                            <?php } ?>
                          </select>      
                        </div>
                        <input type="hidden" name="inscripcion_id_modificar" id="inscripcion_id_modificar" value="">
                        <button type="submit" class="btn btn-primary"><?php echo __('Derivar') ?></button>
                      </form>

                      
                      
                  {!! Form::close() !!}            
            </div>

          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
    <!-- MODAL CAMBIO SOLICITUD -->
    <?php } ?>

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




    </body>
</html>
