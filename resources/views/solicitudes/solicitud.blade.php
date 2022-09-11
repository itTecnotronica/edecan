
@extends('layouts.backend')

@section('contenido')

<?php 
/*
echo date("G:H:s");
$hora = new DateTime("now", new DateTimeZone('America/New_York'));
echo '<br><br>'.$hora->format('G');
date_default_timezone_set('America/New_York');
*/

use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;

use \App\Http\Controllers\SolicitudController; 
$SolicitudController = new SolicitudController;

$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;

if ($Solicitud->sino_aprobado_administracion == 'SI' and $rol_de_usuario_id == 3) {
    $modificar_solicitud = 'N';
}
else {
    $modificar_solicitud = 'S';
}

if ($Solicitud->sino_aprobado_administracion == 'SI') {
    $modificar_contrato = 'S';
}
else {
    $modificar_contrato = 'N';
}

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

$solicitudes_capacitacion = [1, 6, 9, 12, 6966, 6871, 4555, 4701, 5032, 5111, 11377, 11393, 11394, 11443, 11444];

?>

<!-- LIBRERIAS -->
  <!-- vue.js -->
  <script src="<?php echo env('PATH_PUBLIC')?>js/vue/vue.js"></script>
  <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/vee-validate.js"></script>
  <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/locale/es.js"></script>
  <script type="text/javascript" src="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.css">

  <script src="https://cdn.jsdelivr.net/vue.resource/1.3.1/vue-resource.min.js"></script>

  <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/css/bootstrap-select.min.css">
  <script type="text/javascript" src="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/js/bootstrap-select.min.js"></script>

  <!-- bootstrap slider -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>plugins/bootstrap-slider/slider.css">

  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/style.css">
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/generic.css">

  <!-- moment.min.js -->
  <!-- script src="<?php echo env('PATH_PUBLIC')?>js/Moment/moment-with-locales.min.js"></script -->
  <script src="<?php echo env('PATH_PUBLIC')?>js/Moment/moment.min.js"></script>
  <!-- datetimepicker.js -->
  <script src="<?php echo env('PATH_PUBLIC')?>js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css">  

  <!-- GoogleAddress -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBR3Wx1QeIIBC5ZD1C0o09QFzea9tz6ZbU&libraries=places"></script>

<!-- LIBRERIAS -->


<!-- Content Header (Page header) -->
<section class="content-header">
<h1>
  <?php echo __('Solicitud') ?>: <?php echo $Solicitud->id; ?>
  <small>Localidad: <?php echo $Solicitud->localidad_nombre(); ?> </small>
  <?php if (Auth::user()->id == 1 or (Auth::user()->id == 50 and in_array($Solicitud->id, $solicitudes_capacitacion))) { ?>
    <button type="button" data-toggle="modal" data-toggle="modal" data-target="#modal-confirmar-resetear-campaña" class="btn btn-danger btn-md" style="margin-left: 100px;"><?php echo __('Resetear Campaña') ?></button>
  <?php } ?>
</h1>


<ol class="breadcrumb">
  <li><a href="<?php echo env('PATH_PUBLIC')?>"><i class="fa fa-home"></i> Home</a></li>
  <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list/t"><?php echo __('Solicitudes') ?></a></li>
  <li class="active"><?php echo __('Solicitud') ?> </li>
</ol>
</section>

<!-- MAIN CONTENT -->
  <section class="content">
    <div class="row">
      <div id="app-solicitud" class="box box-primary">
        <div class="box-body">
          <div class="col-xs-12 col-lg-3">
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Fecha de Solicitud') ?>:</span> <?php echo $gCont->FormatoFecha($Solicitud->fecha_de_solicitud) ?><br>                  
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __($Solicitud->Tipo_de_evento->tipo_de_evento) ?></span><br>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo $Solicitud->localidad_nombre() ?></span>
              <?php if ($Solicitud->localidad_id == '' and $Solicitud->tipo_de_evento_id <> 3 and $Solicitud->tipo_de_evento_id <> 4) { ?>
                  <p class="bg-danger"><?php echo __('DEBE CREAR LA LOCALIDAD MEDIANTE LAS OPCIONES DE LA IZQUIERDA Y ASIGNARLA AQUI NUEVAMENTE PARA PODER APROBAR LA SOLICITUD') ?></p>
              <?php } ?>
              <br>
              <?php 
              $idioma = '';
              if ($Solicitud->idioma_id <> '') {
                $idioma = $Solicitud->idioma->idioma;
              }
              $moneda = '';
              if ($Solicitud->moneda_id <> '') {
                $moneda = $Solicitud->Moneda->moneda;
              }
              ?>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Idioma') ?>:</span> <?php echo $idioma ?><br>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Solicitante') ?>:</span> <?php echo $Solicitud->nombre_del_solicitante ?><br>              
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Celular') ?>:</span> <?php echo $Solicitud->celular_del_solicitante ?><br>
              <?php if ($Solicitud->payment_status <> '') { ?>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Estado') ?> Paypal:</span> <?php echo $Solicitud->payment_status ?><br>
              <?php 
              $paypal_neto = $Solicitud->paypal_value*(1-0.0479)-0.60;
              ?>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Importe') ?> Paypal:</span> $ <?php echo $Solicitud->paypal_value ?> (Neto: $<?php echo $paypal_neto ?>)<br>
              <?php } ?>
              <?php if ($Solicitud->ejecutivo <> '') { ?>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Ejecutivo de Campaña Asignado') ?>:</span> <?php echo $Solicitud->ejecutivo_asignado()->name ?><br>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Celular') ?>:</span> <?php echo $Solicitud->ejecutivo_asignado()->celular ?><br>
              <a href="https://api.whatsapp.com/send?phone=<?php echo $Solicitud->ejecutivo_asignado()->celular ?>&text=<?php echo __('Hola')?> <?php echo $Solicitud->ejecutivo_asignado()->name ?> <?php echo __('acerca de la campaña') ?> ID: <?php echo $Solicitud->id ?> de <?php echo $Solicitud->localidad_nombre() ?> <?php echo __('que hemos solicitado') ?>" target="_blank">
                <button type="button" class="btn btn-sm btn-success" alt="editar"><i class="fa fa-whatsapp"></i> <?php echo __('Enviar mensaje') ?></button>
              </a>
              <?php } ?>


              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Importe') ?>:</span> <?php echo $moneda ?> <?php echo $gCont->formatoNumero($Solicitud->monto_a_invertir, 'entero'); ?><br>
              <?php if ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id <> '') { ?>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Tipo de curso online') ?>:</span> <?php echo $Solicitud->tipo_de_curso_online->tipo_de_curso_online; ?><br>

              
                <?php if ($Solicitud->tipo_de_curso_online_id == 2 or $Solicitud->tipo_de_curso_online_id == 3) { ?> 
                <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Fecha de Inicio') ?>:</span> <?php echo $gCont->FormatoFecha($Solicitud->fecha_de_inicio_del_curso_online) ?> <?php echo $Solicitud->hora_de_inicio_del_curso_online ?><br>
                <?php } ?>
                <?php if ($Solicitud->tipo_de_curso_online_id <> 4 and $Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual <> '') { ?> 
                <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Enlace') ?>:</span> <a href="url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual" target="_blank"><?php echo $Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual ?></a><br>
                <?php } ?>
              <?php } ?>

              <?php if ($Solicitud->sino_solicitar_responsable_de_inscripcion == 'SI' and ($Solicitud->nombre_responsable_de_inscripciones == '' OR $Solicitud->celular_responsable_de_inscripciones == '')) { ?>
                <span class="badge bg-light-blue datos-finales-asistente">Solicitar Resp de Inscripción:</span> <?php echo $Solicitud->sino_solicitar_responsable_de_inscripcion ?><br>
                  <p class="bg-danger"><?php echo __('DEBE INDICAR EL NOMBRE Y TELEFONO DEL RESPONSABLE DE INSCRIPCION') ?></p>   
              <?php } 
              else {?>           
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Responsable de Inscripción') ?>:</span> <?php echo $Solicitud->nombre_responsable_de_inscripciones ?><br>
                <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Celular Responsable de Inscripción') ?>:</span> <?php echo $Solicitud->celular_responsable_de_inscripciones ?><br>

              <?php } ?>

              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Observaciones') ?>: </span><?php echo $Solicitud->observaciones ?><br><br>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Usuario Registrante') ?>:</span> <?php echo $Solicitud->user->name ?><br><?php echo __('Celular') ?>: <?php echo $Solicitud->user->celular ?><br><?php echo __('Correo') ?>: <?php echo $Solicitud->user->email ?>
              <a href="https://api.whatsapp.com/send?phone=<?php echo $Solicitud->celular_wa($Solicitud->user->celular) ?>&text=<?php echo __('Hola')?> <?php echo $Solicitud->user->name ?> <?php echo __('acerca de la campaña') ?> ID: <?php echo $Solicitud->id ?> de <?php echo $Solicitud->localidad_nombre() ?> <?php echo __('que has solicitado') ?>" target="_blank">
                <button type="button" class="btn btn-sm btn-success" alt="editar"><i class="fa fa-whatsapp"></i> <?php echo __('Enviar mensaje') ?></button><br><br>
              </a>
              <?php if ($cant_inscriptos_derivados > 0) { ?>
              <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Inscriptos') ?> <?php echo __('Derivados') ?>: </span><?php echo $cant_inscriptos_derivados ?>
              <?php } ?>
              <br><br>
              <hr>

              <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_solicitud('Solicitud', 'm', <?php echo $Solicitud->id ?>)"><?php echo __('Modificar') ?></button>
          </div>
              
          <div class="col-xs-12 col-lg-3">
              <!-- BOTON Ver Formulario -->
              <p class="txt_enlaces"><?php echo __('Formulario de Inscripcion') ?>: <strong><?php echo $Solicitud->url_form_inscripcion() ?></strong></p>
              <p><a target="_blank" href="<?php echo $Solicitud->url_form_inscripcion() ?>">
                <button type="button" class="btn btn-block btn-primary btn-md"><i class="fa fa-file-text-o"></i> <?php echo __('Formulario de Inscripcion') ?>  </button>
              </a></p>

              <!-- BOTON Ver Lista de Inscritos -->
              <?php if (($Solicitud->id_pais() == 1 and $rol_de_usuario_id <> 4) or $Solicitud->id_pais() <> 1) { ?>
                <p class="txt_enlaces"><?php echo __('Planilla de Inscripción') ?>: <strong><?php echo $Solicitud->url_planilla_inscripcion() ?></strong></p>
                <p><a target="_blank" href="<?php echo $Solicitud->url_planilla_inscripcion() ?>">
                  <button type="button" class="btn btn-block btn-primary btn-md"><i class="fa fa-list"></i> <?php echo __('Planilla de Inscripción') ?>  </button>
                </a></p>
              <?php } ?>

              <!-- BOTON Encuesta de Satisfaccion -->
              <p class="txt_enlaces"><?php echo __('Encuesta de Satisfacción') ?>: <strong><?php echo $Solicitud->url_encuesta_de_satisfaccion() ?></strong></p>
              <p><a target="_blank" href="<?php echo $Solicitud->url_encuesta_de_satisfaccion() ?>">
                <button type="button" class="btn btn-block btn-primary btn-md"><i class="ion ion-pie-graph"> </i> <?php echo __('Encuesta de Satisfacción') ?> (<?php echo $Solicitud->cant_encuestas() ?>) </button>

              </a></p>

              <!-- BOTON Enlaces Bot de WhatsApp  -->
              <p class="txt_enlaces"><?php echo __('Enalces Bot de WhatsApp') ?>: <strong><?php echo $Solicitud->url_enlaces_wabot() ?></strong></p>
              <p><a target="_blank" href="<?php echo $Solicitud->url_enlaces_wabot() ?>">
                  <button type="button" class="btn btn-block btn-danger btn-md">
                    <span class="pull-right-container">
                      <small class="label pull-left bg-yellow">Experimental</small>
                    </span>
                    <i class="fa fa-check-square-o"></i> <?php echo __('Enlaces Bot de WhatsApp') ?>  
                  </button>
              </a></p>

          </div>
          <div class="col-xs-12 col-lg-3">
              <!-- BOTON Descargar Lista de Inscriptos a Excel -->
              <p class="txt_enlaces"><?php echo __('Descargar lista de inscriptos a Excel') ?>: <strong><?php echo $Solicitud->url_planilla_inscripcion_excel(0) ?></strong></p>
              <p><a target="_blank" href="<?php echo $Solicitud->url_planilla_inscripcion_excel(0) ?>">
                <button type="button" class="btn btn-block btn-primary btn-md"><i class="fa fa-file-excel-o"></i> <?php echo __('Descargar lista de inscriptos a Excel') ?>  </button>
              </a></p>
              <!-- BOTON Ver Planilla de Asistencia -->
              <p class="txt_enlaces"><?php echo __('Planilla de Asistencia') ?>: <strong><?php echo $Solicitud->url_planilla_asistencia() ?></strong></p>
              <p><a target="_blank" href="<?php echo $Solicitud->url_planilla_asistencia() ?>">
                <button type="button" class="btn btn-block btn-primary btn-md"><i class="fa fa-check-square-o"></i> <?php echo __('Planilla de Asistencia') ?>  </button>
              </a></p>

              <!-- BOTON Invitar a Contactos Históricos -->
              <p class="txt_enlaces"><?php echo __('Invitar a Contactos Históricos') ?>: <strong><?php echo $Solicitud->url_planilla_contactos_historicos() ?></strong></p>
              <p>
                <a target="_blank" href="<?php echo $Solicitud->url_planilla_contactos_historicos() ?>">
                  <button type="button" class="btn btn-block btn-danger btn-md">
                    <span class="pull-right-container">
                      <small class="label pull-left bg-yellow">NEW</small>
                    </span>
                    <i class="fa fa-check-square-o"></i> <?php echo __('Invitar a Contactos Históricos') ?>  
                  </button>
                </a>
              </p>
              


              <?php if ($Solicitud->campania_mautic_id <> '') { ?>
                <!-- BOTON Enviar Mail a Contáctos Históricos -->
                <p class="txt_enlaces"><br><br><br></p>
                <p>
                    <button type="button" class="btn btn-block btn-warning btn-md" data-toggle="modal" data-target="#modal-mail-historicos">
                      <span class="pull-right-container">
                        <small class="label pull-left bg-yellow">Experimental</small>
                      </span>
                      <i class="fa fa-check-square-o"></i> <?php echo __('Campaña de mailing') ?>  
                    </button>
                </p>
              <?php } ?>
              


          </div>


        
          <br  style="clear: both;"><hr>
          <div class="col-xs-12 col-lg-6">
            <?php if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 3) { ?>
              <?php if ($Solicitud->tipo_de_evento_id == 1) { ?>
                <a href="<?php echo ENV('PATH_PUBLIC') ?>mostrar-flyers/<?php echo $Solicitud->id ?>" class="btn btn-block btn-social btn-instagram" style="margin-top: 10px;" target="_blank">
                  <i class="fa fa-instagram"></i> <?php echo __('Flyers') ?>
                </a>
              <?php } ?>
              <?php if ($rol_de_usuario_id <=3) { ?>
              <a class="btn btn-block btn-social btn-facebook" data-toggle="modal" data-target="#modal-texto-anuncios" class="btn btn-default btn-md" style="margin-top: 10px;">
                <i class="fa fa-facebook"></i> <?php echo __('Texto para los anuncios de Facebook') ?>
              </a>
              <?php } ?>
            <?php } ?>
          </div>          

          <?php if ($rol_de_usuario_id <=3) { ?>
            <div class="col-xs-12 col-lg-6">
              <div v-bind:class="class_sino(envio_enlaces_a_resp_inscripcion)">
                
                  <a v-bind:href="url_mensaje_extra('<?php echo $Solicitud->celular_wa($Solicitud->celular_responsable_de_inscripciones) ?>')" target="_blank">
                      <button type="button" class="btn btn-blanco" alt="editar" title="editar" v-on:click="marcar_envio(1, <?php echo $Solicitud->id ?>)"><i class="fa fa-whatsapp"></i></button>
                  </a>
                  <?php echo __('Notificar al Responsable de Inscripción') ?>

                  <label class="switch switch-inscripcion">
                    <input type="checkbox" v-on:change="setearSino(1, <?php echo $Solicitud->id ?>)" v-model="envio_enlaces_a_resp_inscripcion">
                    <span class="slider round"></span>
                    
                  </label>
              </div>
            </div>
          <?php } ?>


                <!--div class="col-lg-12">            
                  <pre>@{{ $data }}</pre>
                </div-->  

        </div>
      </div>


      <!-- PANEL FECHAS -->

          <?php 
          $cant_inscriptos = 0;
          $cant_contactados = 0;
          $cant_confirmados = 0;
          $cant_vouchers = 0;
          $cant_motivacion = 0;
          $cant_recordatorio = 0;
          $cant_asistentes = 0;
          $cant_cancelados = 0;
          
          foreach ($Fechas_de_eventos as $Fecha_de_evento) { 

            $cant_inscriptos = $cant_inscriptos + $Fecha_de_evento->cant_inscriptos();
            $cant_contactados = $cant_contactados + $Fecha_de_evento->cant_contactados();
            $cant_confirmados = $cant_confirmados + $Fecha_de_evento->cant_confirmados();
            $cant_vouchers = $cant_vouchers + $Fecha_de_evento->cant_vouchers();
            $cant_motivacion = $cant_motivacion + $Fecha_de_evento->cant_motivacion();
            $cant_recordatorio = $cant_recordatorio + $Fecha_de_evento->cant_recordatorio();
            $cant_asistentes = $cant_asistentes + $Fecha_de_evento->cant_asistentes();
            $cant_cancelados = $cant_cancelados + $Fecha_de_evento->cant_cancelados();



          ?> 
          <!-- PANEL INSCRIPTOS -->
            <div class="box box-default collapsed-box box-solid">

              <div class="box-header with-border">
                <div class="col-xs-10 col-lg-3">
                  <?php echo $Fecha_de_evento->armarDetalleFechasDeEventos()  ?>
                  <!-- BOTON para acceder al curso -->
                  <!--p><a target="_blank" href="<?php echo $Fecha_de_evento->url_curso() ?>">
                    <button type="button" class="btn btn-block btn-primary btn-sm" style="color: white"><i class="fa fa-users"></i> <?php echo __('Panel del curso') ?>  </button>
                  </a></p-->
                  </div>


                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-green" style="width: 60px; height: 110px"><i class="ion ion-ios-people-outline"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text"><?php echo __('Inscriptos') ?>: <strong><?php echo $Fecha_de_evento->cant_inscriptos() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Contactados') ?>: <strong><?php echo $Fecha_de_evento->cant_contactados() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Confirmados') ?>: <strong><?php echo $Fecha_de_evento->cant_confirmados() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Voucher') ?>: <strong><?php echo $Fecha_de_evento->cant_vouchers() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Motivacion') ?>: <strong><?php echo $Fecha_de_evento->cant_motivacion() ?></strong></span>
                        
                        
                      </div>

                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                  </div>

                  <div class="col-md-3 col-sm-6 col-xs-8">
                    <div class="info-box">
                      <span class="info-box-icon bg-green" style="width: 60px; height: 110px"><i class="ion ion-ios-people-outline"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text"><?php echo __('Recordatorio') ?>: <strong><?php echo $Fecha_de_evento->cant_recordatorio() ?></strong>
                        <span class="info-box-text"><?php echo __('Asistentes') ?>: <strong><?php echo $Fecha_de_evento->cant_asistentes() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Cancelados') ?>: <strong><?php echo $Fecha_de_evento->cant_cancelados() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Cupo Máximo') ?>: <strong><?php echo $Fecha_de_evento->cupo_maximo_disponible_del_salon ?></strong></span>
                        <span class="info-box-text">... <strong></strong></span>
                        
                      </div>
                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                  </div>

                  <?php if ($Solicitud->idioma_id == 1) { ?>
                  <div class="col-md-1 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <a href="<?php echo $Fecha_de_evento->url_encuesta_de_satisfaccion() ?>" target="_blank">
                        <span class="info-box-icon bg-red" style="width: 75px"><i class="ion ion-pie-graph"><p style="font-size: 30px;"><?php echo $Fecha_de_evento->cant_encuestas() ?></p></i></span>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php 
                  if ($Fecha_de_evento->cupo_maximo_disponible_del_salon < $Fecha_de_evento->cant_confirmados()) { 
                    $capacidad_max_estimada_confirmados = intval($Fecha_de_evento->cupo_maximo_disponible_del_salon*100/70);
                    $texto_exedio_capacidad = __('La cantidad de confirmados supera a la capacidad del salon, nuestras estadísticas nos permiten saber que el 70% de los confirmados son los que asisten, ud. podria llegar a confirmar hasta').' <strong>'.$capacidad_max_estimada_confirmados.'</strong> '.__('personas');

                  ?>
                    <div style="clear: both;">
                      <p class="p-error"><?php echo $texto_exedio_capacidad ?> </p>
                    </div>
                  <?php } ?>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_solicitud('Fecha_de_evento', 'm', <?php echo $Fecha_de_evento->id ?>)"><?php echo __('Modificar') ?></button> 
                  <button type="button" class="btn btn-default btn-md" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_solicitud('Fecha_de_evento', 'b', <?php echo $Fecha_de_evento->id ?>)"><?php echo __('Eliminar') ?></button>
                    <a target="_blank" href="<?php echo env('PATH_PUBLIC')?>f/x/<?php echo $Solicitud->id ?>/<?php echo $Solicitud->hash ?>/<?php echo $Fecha_de_evento->id ?>">
                      <button alt="Descargar Lista a Excel" title="Descargar Lista a Excel" type="button" class="btn btn-default btn-md"><i class="fa fa-file-excel-o"></i></button>
                    </a>
                    <!--button type="button" class="btn btn-default btn-md" data-widget="collapse"><i class="fa fa-plus"></i>
                    </button-->
                </div>
              </div>

              <div class="box-body">
                <div class="modal-body" id="modal-bodi-list_<?php echo $Fecha_de_evento->id ?>"></div>
                <?php 
                $gen_seteo = array(
                  'gen_url_siguiente' => 'back', 
                  'gen_permisos' => ['C','R','U', 'D'],
                  'gen_campos_a_ocultar' => 'solicitud_id|fecha_de_evento_id|sino_notificar_proximos_eventos|sino_acepto_politica_de_privacidad|sino_envio_pedido_de_confirmacion|sino_confirmo|sino_envio_recordatorio_pedido_de_confirmacion|sino_envio_voucher|sino_envio_motivacion|sino_envio_recordatorio||-created_at|url_enlace_a_google_maps_inicio_redirect_final|url_enlace_a_google_maps_curso_redirect_final',
                  'no_mostrar_campos_abm' => 'solicitud_id|fecha_de_evento_id',
                  'filtro_where' => array('fecha_de_evento_id', '=', $Fecha_de_evento->id),
                  'tabla_condensada' => 'SI'
                );

                ?>
                <!--script type="text/javascript">
                  
                  $.ajax({
                    url: '<?php echo env('PATH_PUBLIC')?>crearlista',
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      gen_modelo: 'Inscripcion',
                      gen_seteo: '<?php echo serialize($gen_seteo) ?>',
                      gen_opcion: ''
                    },
                    success: function success(data, status) {        
                      $("#modal-bodi-list_<?php echo $Fecha_de_evento->id ?>").html(data);
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                  });
                </script-->              
              </div>
            </div>
          <!-- FIN PANEL INSCRIPTOS -->
        <?php } ?>   


          <!-- PANEL INSCRIPTOS SIN FECHA -->
            <?php if ($Solicitud->tipo_de_evento_id <> 3 or $Solicitud->tipo_de_curso_online_id == 4) { ?>
              <div class="box box-default collapsed-box box-solid">

                <div class="box-header with-border">
                  <div class="col-xs-10 col-lg-4">No pueden en ningun horario</div>



                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-green"><i class="ion ion-ios-people-outline"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text"><?php echo __('Inscriptos') ?>: <strong><?php echo $Solicitud->cant_inscriptos_sin_fecha_de_evento() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Contactados') ?>: <strong><?php echo $Solicitud->cant_inscriptos_sin_fecha_de_evento_contactados() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Cancelados') ?>: <strong><?php echo $Solicitud->cant_inscriptos_cancelados() ?></strong></span>
                      </div>
                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                  </div>

                  <div class="box-tools pull-right">


                    <a target="_blank" href="<?php echo env('PATH_PUBLIC')?>f/x/<?php echo $Solicitud->id ?>/<?php echo $Solicitud->hash ?>/-1">
                      <button type="button" class="btn btn-default btn-md"><i class="fa fa-file-excel-o"></i>
                      </button>
                    </a>
                    <!--button type="button" class="btn btn-default btn-md" data-widget="collapse"><i class="fa fa-plus"></i>
                    </button-->
                  </div>
                </div>


                <div class="box-body">
                  <div class="modal-body" id="modal-bodi-list-sin-fecha"></div>
                  <?php 
                  $gen_seteo_listarCliente = array(
                    'gen_url_siguiente' => 'back', 
                    'gen_permisos' => ['C','R','U', 'D'],
                    'gen_campos_a_ocultar' => 'solicitud_id|fecha_de_evento_id|sino_notificar_proximos_eventos|sino_acepto_politica_de_privacidad|sino_envio_pedido_de_confirmacion|sino_confirmo|sino_envio_recordatorio_pedido_de_confirmacion|sino_envio_voucher|sino_envio_motivacion|sino_envio_recordatorio',
                    'no_mostrar_campos_abm' => 'solicitud_id|fecha_de_evento_id',
                    'filtro_where' => array(
                      ['fecha_de_evento_id', '=', ''],
                      ['solicitud_id', '=', $Solicitud->id]
                    ),
                    'tabla_condensada' => 'SI'
                  );

                  ?>
                  <!--script type="text/javascript">
                    
                    $.ajax({
                      url: '<?php echo env('PATH_PUBLIC')?>crearlista',
                      type: 'POST',
                      dataType: 'html',
                      async: true,
                      data:{
                        _token: "{{ csrf_token() }}",
                        gen_modelo: 'Inscripcion',
                        gen_seteo: '<?php echo serialize($gen_seteo_listarCliente) ?>',
                        gen_opcion: ''
                      },
                      success: function success(data, status) {        
                        $("#modal-bodi-list-sin-fecha").html(data);
                      },
                      error: function error(xhr, textStatus, errorThrown) {
                          alert(errorThrown);
                      }
                    });
                  </script-->              
                </div>
              </div>
            <?php } ?>
          <!-- FIN PANEL INSCRIPTOS SIN FECHA -->

          <!-- PANEL TOTALES -->

            <?php 
            $cant_inscriptos_unicos = $Solicitud->cant_inscriptos_unicos();
            $cant_inscriptos = $cant_inscriptos + $Solicitud->cant_inscriptos_sin_fecha_de_evento(); 
            $cant_contactados = $cant_contactados + $Solicitud->cant_inscriptos_sin_fecha_de_evento_contactados(); 
            $cant_cancelados = $cant_cancelados + $Solicitud->cant_inscriptos_cancelados(); 
            $cant_en_grupos = $Solicitud->cant_en_grupos(); 

            $cant_visualizaciones_x_inscripto = '';
            $cant_visualizaciones = $Solicitud->cant_visualizaciones(); 
            $cant_visualizaciones = $Solicitud->cant_visualizaciones(); 

            if ($cant_inscriptos > 0) {
              $cant_visualizaciones_x_inscripto = $Solicitud->cant_visualizaciones()/$cant_inscriptos; 
            }

            $mostrar_stats_costos = false;
            if ($Solicitud->importe_gastado > 0) {
              $mostrar_stats_costos = true;
              
              $costo_por_inscripto = 0;
              if ($cant_inscriptos > 0) {
                $costo_por_inscripto = $Solicitud->importe_gastado/$cant_inscriptos;
              }

              $costo_por_asistente = 0;
              if ($cant_asistentes > 0) {
                $costo_por_asistente = $Solicitud->importe_gastado/$cant_asistentes;  
              }
              
            }

            ?>
            <div class="box box-default collapsed-box box-solid">

              <div class="box-header with-border">
                <div class="col-xs-10 col-lg-1"><?php echo __('Totales') ?></div>

                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-green"><i class="ion ion-ios-people-outline"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text"><?php echo __('Inscriptos') ?>: <strong><?php echo $cant_inscriptos ?></strong></span>
                        <span class="info-box-text"><?php echo __('Contactados') ?>: <strong><?php echo $cant_contactados ?></strong></span>
                        <?php if ($Solicitud->tipo_de_evento_id <> 3 or $Solicitud->tipo_de_curso_online_id == 4) { ?>
                        <span class="info-box-text"><?php echo __('Confirmados') ?>: <strong><?php echo $cant_confirmados ?></strong></span>
                        <span class="info-box-text"><?php echo __('Voucher') ?>: <strong><?php echo $cant_vouchers ?></strong></span>
                        <?php } 
                        else { ?>
                        <span class="info-box-text"><?php echo __('En grupos') ?>: <strong><?php echo $cant_en_grupos ?></strong></span>
                        <span class="info-box-text"><?php echo __('Cancelados') ?>: <strong><?php echo $cant_cancelados ?></strong></span>
                        <?php } ?>
                        
                        
                      </div>

                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                  </div>
                  <?php if ($Solicitud->tipo_de_evento_id <> 3 or $Solicitud->tipo_de_curso_online_id == 4) { ?>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                      <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="ion ion-ios-people-outline"></i></span>

                        <div class="info-box-content">
                          <span class="info-box-text"><?php echo __('Motivacion') ?>: <strong><?php echo $cant_motivacion ?></strong></span>
                          <span class="info-box-text"><?php echo __('Recordatorio') ?>: <strong><?php echo $cant_recordatorio ?></strong>
                          <span class="info-box-text"><?php echo __('Asistentes') ?>: <strong><?php echo $cant_asistentes ?></strong></span>
                          <span class="info-box-text"><?php echo __('Cancelados') ?>: <strong><?php echo $cant_cancelados ?></strong></span>
                          
                        </div>
                        <!-- /.info-box-content -->
                      </div>
                      <!-- /.info-box -->
                    </div>
                  <?php } ?>


                  <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-blue"><i class="fa fa-bar-chart"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text"><?php echo __('Inscriptos') ?> <?php echo __('únicos') ?>: <strong><?php echo $cant_inscriptos_unicos ?></strong></span>
                        <span class="info-box-text"><?php echo __('Visualizaciones') ?>: <strong><?php echo $cant_visualizaciones ?></strong></span>
                        <span class="info-box-text"><?php echo __('Visualizaciones') ?>/<?php echo __('Inscriptos') ?>: <strong><?php echo $gCont->formatoNumero($cant_visualizaciones_x_inscripto, 'decimal'); ?></strong></span>
                        <?php if ($mostrar_stats_costos) { ?>
                          <span class="info-box-text"><?php echo __('Costo por Inscripto') ?>: <strong>$ <?php echo $gCont->formatoNumero($costo_por_inscripto, 'decimal'); ?></strong></span>
                          <span class="info-box-text"><?php echo __('Costo por Asistente') ?>: <strong>$ <?php echo $gCont->formatoNumero($costo_por_asistente, 'decimal'); ?></strong></span>
                        <?php } ?>
                        
                      </div>

                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                  </div>


              </div>

            </div>
          <!-- FIN PANEL TOTALES -->

          <!-- PANEL CAMPAÑAS -->
            <?php if ($Inscriptos_por_campania->count() > 0) { ?>
            <div class="box box-default collapsed-box box-solid">

              <div class="box-header with-border">
                <div class="col-xs-10 col-lg-6"><strong><?php echo __('Inscriptos') ?> <?php echo __('por') ?> <?php echo __('Campañas') ?></strong><br><br></div>

                  <table id="table" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th><?php echo __('Id') ?></th>
                        <th><?php echo __('Campaña') ?></th>
                        <th><?php echo __('Pais') ?></th>
                        <th><?php echo __('Importe') ?></th>
                        <th><?php echo __('Organica') ?></th>
                        <th><?php echo __('Inscrptos') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($Inscriptos_por_campania as $Inscriptos) { ?>
                      <tr>
                          <td><?php echo $Inscriptos->id; ?></td>
                          <td><?php echo $Inscriptos->campania; ?></td>
                          <td><?php echo $Inscriptos->pais; ?></td>
                          <td><?php echo $Inscriptos->moneda_importe_en_dolares; ?></td>
                          <td><?php echo $Inscriptos->sino_es_campania_organica; ?></td>
                          <td><?php echo $Inscriptos->cant; ?></td>
                      </tr>
                      <?php } ?>                    
                  </tbody>
                  </table>


              </div>

            </div>
            <?php } ?>
          <!-- FIN PANEL CAMPAÑAS -->

      <!-- FIN PANEL FECHAS -->
      <?php if (($Solicitud->Tipo_de_evento->id == 1) or $Solicitud->Tipo_de_evento->id == 2 or $Solicitud->Tipo_de_evento->id == 4 or ($Solicitud->Tipo_de_evento->id == 3 and $Solicitud->tipo_de_curso_online_id == 4)) { ?>
        <button style="margin-left: 20px; background-color: #d2d6de" type="button" class="btn btn-default btn-md" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_solicitud('Fecha_de_evento', 'a', -1)"><?php echo __('Insertar').' '.__('Fecha') ?></button>
      <?php } ?>
      <br><br>


        <div class="box box-default">

          <!-- APROBACION SOLICITAR REVISION --> 
            <?php if ($Solicitud->sino_aprobado_administracion == 'NO') { ?>
              <?php 
              $checked_sino_aprobado_solicitar_revision = '';
              $class_sino_aprobado_solicitar_revision = '';
              if ($Solicitud->sino_aprobado_solicitar_revision == 'SI') {
                $checked_sino_aprobado_solicitar_revision = 'checked="checked"';
                $class_sino_aprobado_solicitar_revision = 'bg-yellow';
                $txt_sino_aprobado_solicitar_revision = 'Solicitada';
              }
              if ($Solicitud->sino_aprobado_solicitar_revision == 'NO') {
                $checked_sino_aprobado_solicitar_revision = '';
                $class_sino_aprobado_solicitar_revision = 'bg-blue';
                $txt_sino_aprobado_solicitar_revision = 'Atendida';
              }
              if ($Solicitud->sino_aprobado_solicitar_revision == '') {
                $checked_sino_aprobado_solicitar_revision = '';
                $class_sino_aprobado_solicitar_revision = 'bg-grey';
                $txt_sino_aprobado_solicitar_revision = '';
              }
              ?>

              <div class="box-footer <?php echo $class_sino_aprobado_solicitar_revision ?>" id="box-footer-solicitud-solicitar_revision">
                <div class="col-xs-6">
                  <!-- Rounded switch -->
                  <div class="pull-left">
                    <span class="label_aprobacion">Revisi&oacute;n</span>
                    <?php if ($rol_de_usuario_id > 0) { ?>
                    <label class="switch">
                      <input id="sino_aprobado_solicitar_revision" type="checkbox" onclick="aprobacionSolicitarRevision(this.checked)" <?php echo $checked_sino_aprobado_solicitar_revision ?>>
                      <span class="slider round"></span>
                    </label>
                    <?php } ?>
                  </div>
                  <span id="estado_sino_aprobado_solicitar_revision" class="badge datos-finales-asistente" style="margin-top: 7px; background-color: #333; margin-left: 20px;"><?php echo $txt_sino_aprobado_solicitar_revision ?></span>
                </div>
                <div class="col-xs-6">
                  <?php if ($rol_de_usuario_id > 0) { ?>
                  <textarea maxlength="250" id="observaciones_aprobado_solicitar_revision" name="observaciones_aprobado_solicitar_revision" class="form-control" placeholder="Indique los motivos de la solicitud de revision" onkeydown="guardarObsSolRev(this.value)"><?php echo $Solicitud->observaciones_aprobado_solicitar_revision ?></textarea>
                  <?php } 
                  else { ?>
                  <?php echo $Solicitud->observaciones_aprobado_solicitar_revision ?>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
          <!-- FIN APROBACION SOLICITAR REVISION --> 



          <!-- APROBACION ADM --> 
            <?php 
            $checked_sino_aprobado_administracion = '';
            $class_sino_aprobado_administracion = '';
            if ($Solicitud->sino_aprobado_administracion == 'SI') {
              $checked_sino_aprobado_administracion = 'checked="checked"';
              $class_sino_aprobado_administracion = 'bg-olive';
              $txt_sino_aprobado_administracion = 'Aprobado';
              $class_observaciones_aprobado_administracion = 'oculto';
            }
            if ($Solicitud->sino_aprobado_administracion == 'NO') {
              $checked_sino_aprobado_administracion = '';
              $class_sino_aprobado_administracion = 'bg-red';
              $txt_sino_aprobado_administracion = 'Desaprobado';
              $class_observaciones_aprobado_administracion = 'visible';
            }
            if ($Solicitud->sino_aprobado_administracion == '') {
              $checked_sino_aprobado_administracion = '';
              $class_sino_aprobado_administracion = 'bg-grey';
              $txt_sino_aprobado_administracion = '';
              $class_observaciones_aprobado_administracion = 'oculto';
            }
            ?>

            <div class="box-footer <?php echo $class_sino_aprobado_administracion ?>" id="box-footer-solicitud-administracion">
              <div class="col-xs-6">
                <!-- Rounded switch -->
                <div class="pull-left">
                  <span class="label_aprobacion"><?php echo __('Aprobada para inscripción') ?></span>
                  <?php if ($rol_de_usuario_id <= 3) { ?>
                  <label class="switch">
                    <?php 
                    if (($Solicitud->localidad_id == '' and !in_array($Solicitud->tipo_de_evento_id, [3, 4])) or $Solicitud->nombre_responsable_de_inscripciones == '' or $Solicitud->celular_responsable_de_inscripciones == '') { 
                      $deshabilitar_aprobacion = 'disabled="disabled"';
                    }
                    else {
                      $deshabilitar_aprobacion = '';
                    }
                    ?>

                    <input id="sino_aprobado_administracion" type="checkbox" onclick="aprobacionAdministracion(this.checked)" <?php echo $checked_sino_aprobado_administracion ?> <?php echo $deshabilitar_aprobacion ?>>
                    <span class="slider round"></span>
                  </label>
                  <?php } ?>
                </div>
                <span id="estado_sino_aprobado_administracion" class="badge datos-finales-asistente" style="margin-top: 7px; background-color: #333; margin-left: 20px;"><?php echo $txt_sino_aprobado_administracion ?></span>
              </div>
              <div class="col-xs-6">
                <?php if ($rol_de_usuario_id < 3) { ?>
                <textarea maxlength="250" id="observaciones_aprobado_administracion" name="observaciones_aprobado_administracion" class="form-control <?php echo $class_observaciones_aprobado_administracion ?>" placeholder="Indique los motivos de la desaprobacion" onkeydown="guardarObsAdm(this.value)"><?php echo $Solicitud->observaciones_aprobado_administracion ?></textarea>
                <?php } 
                else { ?>
                <?php echo $Solicitud->observaciones_aprobado_administracion ?>
                <?php } ?>
              </div>
            </div>
          <!-- FIN APROBACION ADM --> 

        </div>

        <!-- PANEL CANCELADA -->
          <div class="box box-default">
            <?php if ($rol_de_usuario_id < 3) { ?>


              <?php 
              
                $checked_sino_cancelada = '';
                $class_sino_cancelada = '';
                if ($Solicitud->sino_cancelada == 'SI') {
                  $checked_sino_cancelada = 'checked="checked"';
                  $class_sino_cancelada = 'bg-red';
                  $txt_sino_cancelada = 'SI';
                  $observaciones_cancelada = '';
                }
                if ($Solicitud->sino_cancelada == 'NO') {
                  $checked_sino_cancelada = '';
                  $class_sino_cancelada = 'bg-grey';
                  $txt_sino_cancelada = 'NO';
                  $observaciones_cancelada = 'oculto';
                }
                if ($Solicitud->sino_cancelada == '') {
                  $checked_sino_cancelada = '';
                  $class_sino_cancelada = 'bg-grey';
                  $txt_sino_cancelada = '';
                  $observaciones_cancelada = 'oculto';
                }
              ?>

                <div class="box-footer <?php echo $class_sino_cancelada ?>" id="box-footer-solicitud-cancelada">
                  <div class="col-xs-6">
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <span class="label_aprobacion">CANCELADA</span>
                      <?php if ($rol_de_usuario_id < 3) { ?>
                      <label class="switch">
                        <input id="sino_cancelada" type="checkbox" onclick="aprobacioncancelada(this.checked)" <?php echo $checked_sino_cancelada ?>>
                        <span class="slider round"></span>
                      </label>
                      <?php } ?>
                    </div>
                    <span id="estado_sino_cancelada" class="badge datos-finales-asistente" style="margin-top: 7px; background-color: #333; margin-left: 20px;"><?php echo $txt_sino_cancelada ?></span>
                  </div>
                  <div class="col-xs-6">
                    <?php if ($rol_de_usuario_id < 3) { ?>
                    <textarea maxlength="250" id="observaciones_cancelada" name="observaciones_cancelada" class="form-control <?php echo $observaciones_cancelada ?>" placeholder="Indique las observaciones" onkeydown="guardarObsCanc(this.value)"><?php echo $Solicitud->observaciones_cancelada ?></textarea>
                    <?php } 
                    else { ?>
                    <?php echo $Solicitud->observaciones_cancelada ?>
                    <?php } ?>
                  </div>        
                </div>


            <?php } ?> 
          </div>       
        <!-- PANEL CANCELADA -->

        <!-- PANEL FACEBOOK ADS -->   
          <div class="box box-default">
            <div class="box-header">
              <h3 class="box-title"><i class="fa fa-facebook"></i> Facebook Ads </h3>
            </div>

            <div class="box-footer" id="box-footer-solicitud-administracion">

              <?php if ($Solicitud->tipo_de_campania_facebook_id <> '') { ?>
                <div class="col-xs-12 col-lg-3"> 
                  <?php echo __('Tipo de campania facebook') ?>: <?php echo __($Solicitud->tipo_de_campania_facebook->tipo_de_campania_facebook) ?><br>
                  <?php echo __('Identificador de la campania de facebook') ?>: <?php echo __($Solicitud->identificador_de_la_campania_de_facebook) ?><br>
                  <?php echo __('Importe gastado') ?>: <?php echo __($Solicitud->importe_gastado) ?><br>
                </div>

                <div class="col-xs-12 col-lg-3"> 
                  <?php echo __('Resultados') ?>: <?php echo __($Solicitud->resultados) ?><br>
                  <?php echo __('Alcances') ?>: <?php echo __($Solicitud->alcances) ?><br>
                  <?php echo __('Impresiones') ?>: <?php echo __($Solicitud->impresiones) ?><br>
                </div>


                <div class="col-xs-12 col-lg-3"> 
                  <?php echo __('Frecuencia') ?>: <?php echo __($Solicitud->frecuencia) ?><br>
                  <?php echo __('Clics unicos') ?>: <?php echo __($Solicitud->clics_unicos) ?><br><br>
                </div>

              <?php } ?>

              <div class="col-xs-12 col-lg-6"> 
                  <div class="col-xs-6">
                    <p><?php echo __('Para poder finalizar una campaña primero deben completarse los datos de facebook en esta sección') ?></p>
                  </div> 
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-primary btn-md btn-facebook" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_datos_face('Solicitud', 'm', <?php echo $Solicitud->id ?>)"><i class="fa fa-facebook"></i> <?php echo __('Ingresar Datos de Campaña de Facebook') ?></button>
                  </div>
              </div>
            </div>
          </div>
        <!-- PANEL FACEBOOK ADS -->         

        <!-- PANEL FINALIZADA -->
          <?php if ($para_finalizar == 'S') { ?>
            <div class="box box-default">
              <!-- APROBACION FINALIZADA --> 
                <?php 
                
                  $checked_sino_aprobado_finalizada = '';
                  $class_sino_aprobado_finalizada = '';
                  if ($Solicitud->sino_aprobado_finalizada == 'SI') {
                    $checked_sino_aprobado_finalizada = 'checked="checked"';
                    $class_sino_aprobado_finalizada = 'bg-blue';
                    $txt_sino_aprobado_finalizada = 'SI';
                    $observaciones_aprobado_finalizada = '';
                  }
                  if ($Solicitud->sino_aprobado_finalizada == 'NO') {
                    $checked_sino_aprobado_finalizada = '';
                    $class_sino_aprobado_finalizada = 'bg-grey';
                    $txt_sino_aprobado_finalizada = 'NO';
                    $observaciones_aprobado_finalizada = 'oculto';
                  }
                  if ($Solicitud->sino_aprobado_finalizada == '') {
                    $checked_sino_aprobado_finalizada = '';
                    $class_sino_aprobado_finalizada = 'bg-grey';
                    $txt_sino_aprobado_finalizada = '';
                    $observaciones_aprobado_finalizada = 'oculto';
                  }
                ?>

                  <div class="box-footer <?php echo $class_sino_aprobado_finalizada ?>" id="box-footer-solicitud-finalizada">
                    <div class="col-xs-6">
                      <!-- Rounded switch -->
                      <div class="pull-left">
                        <span class="label_aprobacion">FINALIZADA</span>
                        <?php if ($rol_de_usuario_id <= 3) { ?>
                        <label class="switch">
                          <input id="sino_aprobado_finalizada" type="checkbox" onclick="aprobacionfinalizada(this.checked)" <?php echo $checked_sino_aprobado_finalizada ?>>
                          <span class="slider round"></span>
                        </label>
                        <?php } ?>
                      </div>
                      <span id="estado_sino_aprobado_finalizada" class="badge datos-finales-asistente" style="margin-top: 7px; background-color: #333; margin-left: 20px;"><?php echo $txt_sino_aprobado_finalizada ?></span>
                    </div>
                    <div class="col-xs-6">
                      <?php if ($rol_de_usuario_id < 3) { ?>
                      <textarea maxlength="250" id="observaciones_aprobado_finalizada" name="observaciones_aprobado_finalizada" class="form-control <?php echo $observaciones_aprobado_finalizada ?>" placeholder="Indique las observaciones" onkeydown="guardarObsFin(this.value)"><?php echo $Solicitud->observaciones_aprobado_finalizada ?></textarea>
                      <?php } 
                      else { ?>
                      <?php echo $Solicitud->observaciones_aprobado_finalizada ?>
                      <?php } ?>
                    </div>        
                  </div>
              <!-- FIN APROBACION FINALIZADA --> 
            </div>
          <?php } ?>   
        <!-- PANEL FINALIZADA -->   

  

  </section>
<!-- MAIN CONTENT -->

<!-- MODAL ABM -->
  <div class="modal modal fade" id="modal-solicitud-abm">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="modal-titulo">Info Modal</div></h4>
        </div>
        <div class="modal-body" id="modal-bodi-abm">

        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<!-- MODAL ABM -->



<!-- MODAL RESETEAR CAMPAÑA -->
  <div class="modal modal fade" id="modal-confirmar-resetear-campaña">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Desea Resetear esta campaña?') ?></div></h4>
        </div>

        <div class="modal-body" id="modal-bodi-confirmar-resetear-campaña">                        
          <h3 class="text-warning"><?php echo __('Esta seguro que quiere eliminar todos los datos de inscripcion, asistencia, envios de mensajes de esta campaña?') ?></h3><br><br>
          <label>Ingrese la Clave de Reseteo: </label>
          <input id="password_reset" class="form-control" type="password" name="password_reset">
        </div>

        <div class="modal-footer">
          <center>
            <button type="button" class="btn btn-default" onclick="resetear_campania(<?php echo $Solicitud->id ?>)"><?php echo __('Aceptar') ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancelar') ?></button>
          </center>  
          <input type="hidden" name="sino_aprobado_administracion" value="NO">
        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<!-- MODAL RESETEAR CAMPAÑA -->



<!-- MODAL TEXTO ANUNCIOS -->
  <div class="modal modal fade" id="modal-mail-historicos">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Enviar Mail a Contáctos Históricos') ?></div></h4>
        </div>

        <div class="modal-body" id="modal-bodi-mail-historicos">  


          <?php if ($Solicitud->campania_mautic_id <> '') { ?>
            <?php 
            $emailsMauticCampaign = $Solicitud->emailsMauticCampaign();
            $Campaign = $emailsMauticCampaign['Campaign'];

            if ($Campaign <> null) {
              $Campaign_leads = $emailsMauticCampaign['Campaign_leads'];
              $Email_stats = $emailsMauticCampaign['Email_stats'];
              $cant_inscriptos = $emailsMauticCampaign['cant_inscriptos'];
              $modificar = $emailsMauticCampaign['modificar'];
              ?>

              <p>Hemos generado automáticamente una campaña de envio de mails a todos los contactos de la base de datos que concuerden con la ciudad de esta campaña y que hallan solicitado recibir información sobre próximos eventos </p>
            

              <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                  <div class="panel-heading" role="tab" id="headingOne" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <h4 class="panel-title"><?php echo __('Datos de la Campaña') ?></h4>
                  </div>
                  <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                      <table class="table table-bordered">
                        <tbody>

                          <tr>
                            <th><?php echo __('Fecha de Creación') ?></th>
                            <td><?php echo $gCont->FormatoFecha($Campaign->date_added) ?></td>
                          </tr>

                          <?php if ($modificar == 'SI') { ?>
                            <tr>
                              <th><?php echo __('Programada para enviar') ?></th>
                              <td>
                                <?php 

                                if ($Campaign->is_published == 1) { 
                                  $sino_is_published = 'SI';
                                }
                                else  { 
                                  $sino_is_published = 'NO';
                                }

                                $checked_sino_is_published = '';
                                $class_sino_is_published = '';
                                if ($sino_is_published == 'SI') {
                                  $checked_sino_is_published = 'checked="checked"';
                                }
                                if ($sino_is_published == 'NO') {
                                  $checked_sino_is_published = '';
                                }
                                ?>                              
                                <label class="switch">
                                  <input id="sino_is_published" type="checkbox" onclick="aprobacionPublished(this.checked)" <?php echo $checked_sino_is_published ?>>
                                  <span class="slider round"></span>
                                </label>
                                <span id="estado_sino_is_published" class="badge datos-finales-asistente" style="margin-top: 7px; background-color: #333; margin-left: 20px;"><?php echo $sino_is_published ?></span>

                              </td>
                            </tr>

                            <tr>
                              <th><?php echo __('Fecha de Envio') ?></th>
                              <td><?php echo $gCont->FormatoFechayYHora($Campaign->publish_up) ?></td>
                            </tr>

                            <tr>
                              <th><?php echo __('Destinatarios') ?></th>
                              <td><?php echo $Campaign_leads->count() ?></td>
                            </tr>
                          <?php } ?>

                          <tr>
                            <th><?php echo __('Enviados') ?></th>
                            <td><?php echo $Email_stats->enviados ?></td>
                          </tr>
                          <tr>
                            <?php 
                            if ($Email_stats->enviados > 0) {
                              $porc_leidos = round($Email_stats->leidos * 100 / $Email_stats->enviados);
                            }
                            else {
                              $porc_leidos = 0;
                            }
                            ?>
                            <th><?php echo __('Leidos') ?></th>
                            <td><?php echo $Email_stats->leidos ?> <span class="badge bg-green"><?php echo $porc_leidos ?>%</span></td>
                          </tr>
                          <tr>
                            <th></th>
                            <td>
                              <div class="progress progress-xs progress-striped active">
                                <div class="progress-bar progress-bar-success" style="width: <?php echo $porc_leidos ?>%"></div>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <?php 
                            if ($Email_stats->enviados > 0) {
                              $porc_inscriptos = round($cant_inscriptos * 100 / $Email_stats->enviados);
                            }
                            else {
                              $porc_inscriptos = 0;
                            }
                            ?>
                            <th><?php echo __('Inscriptos') ?></th>
                            <td><?php echo $cant_inscriptos ?> <span class="badge bg-green"><?php echo $porc_inscriptos ?>%</span></td>
                          </tr>
                          <tr>
                            <th></th>
                            <td>
                              <div class="progress progress-xs progress-striped active">
                                <div class="progress-bar progress-bar-success" style="width: <?php echo $porc_inscriptos ?>%"></div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading" role="tab" id="headingTwo" class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <h4 class="panel-title"><?php echo __('Contenido del Mail') ?></h4>
                  </div>
                  <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                    <div class="panel-body">
                      <a href="https://forms.gnosis.is/email/preview/<?php echo $Solicitud->mautic_email_id ?>" target="_blank"><p>Ver Email a Enviar</p></a>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading" role="tab" id="headingThree" class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <h4 class="panel-title"><?php echo __('Destinatarios') ?> (<?php echo $Campaign_leads->count() ?>)</h4>
                  </div>
                  <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                    <div class="panel-body">
                      <!------- TABLA CORREOS CAMPAÑA ------------>
                        <table id="table-campaign" class="table table-bordered table-striped" >
                          <thead>
                          <tr>
                              <th><?php echo __('Nombre') ?></th>
                              <th><?php echo __('Apellido') ?></th>
                              <th><?php echo __('Email') ?></th>
                          </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($Campaign_leads as $Lead) { ?>
                              <tr>
                                <td><?php echo $Lead->firstname; ?></td>
                                <td><?php echo $Lead->lastname; ?></td>
                                <td><?php echo $Lead->email; ?></td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      <!------- FIN TABLA CORREOS CAMPAÑA ------------>
                    </div>
                  </div>
                </div>
              </div>

            <?php } ?>
          <?php } ?>

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
<!-- MODAL TEXTO ANUNCIOS -->

<!-- MODAL TEXTO ANUNCIOS -->
  <div class="modal modal fade" id="modal-texto-anuncios">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Texto para los anuncios de Facebook') ?></div></h4>
        </div>

        <div class="modal-body" id="modal-bodi-texto-anuncios">                        
          <?php $textos = $Solicitud->texto_anuncios_facebook(); ?>
          <label>Titulo: </label> <input type="text" name="" class="form-control" value="<?php echo $textos['titulo'] ?>">
          <label>Descripcion: </label> <textarea class="form-control" rows="10"><?php echo $textos['descripcion'] ?></textarea>
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
<!-- MODAL TEXTO ANUNCIOS -->


<!-- RESETEAR CAMPAÑA -->  
  <script type="text/javascript">

    function resetear_campania(solicitud_id) {
      var password_reset = $("#password_reset").val()
      window.location = "<?php echo env('PATH_PUBLIC') ?>resetear-campania/"+solicitud_id+"/"+password_reset;
    }

  </script>
<!-- RESETEAR CAMPAÑA -->   
       


<!-- FUNCIONES ABM Y MODIFICAR SOLICITUD -->
  <?php 
  $gen_url_siguiente = env('PATH_PUBLIC').'Solicitudes/solicitud/ver/'.$Solicitud->id;
  $gen_seteo = array(
      'gen_url_siguiente' => $gen_url_siguiente, 
      'no_mostrar_campos_abm' => 'user_id|tipo_de_evento_id|sino_aprobado_administracion|sino_aprobado_solicitar_revision|sino_aprobado_finalizada|observaciones_aprobado_administracion|observaciones_aprobado_finalizada|hash|sino_envio_enlaces_a_resp_inscripcion|sino_cancelada|sino_envio_enlaces_a_resp_inscripcion|paypal_transaction_id|payment_pending_reason|payment_error_code|payment_status|payment_paid|payment_paid_date|paypal_payerid|paypal_token|paypal_value|payment_checkout_status|observaciones_aprobado_solicitar_revision|observaciones_cancelada|tipo_de_campania_facebook_id|identificador_de_la_campania_de_facebook|importe_gastado|resultados|alcances|impresiones|frecuencia|clics_unicos|latitud|longitud|campania_mautic_id'
    );

  if ($rol_de_usuario_id > 2) {
    $gen_seteo['no_mostrar_campos_abm'] .= '|ejecutivo|colpick_color_de_fondo_del_formulario|sino_asignacion_automatica|cupo_maximo';
  }

  /*
  if ($rol_de_usuario_id > 3) {
    $gen_seteo['no_mostrar_campos_abm'] .= '|titulo_del_formulario_personalizado|subtitulo_del_formulario_personalizado|img_imagen_del_formulario_personalizada|resumen_del_formulario_personalizado|texto_del_formulario_personalizado';
  }
  */

  $no_mostrar_campos_abm = '';
  if($Solicitud->tipo_de_evento_id == 1) {
    $no_mostrar_campos_abm = '|resumen_de_la_conferencia';
  }
  else {
    if ($Solicitud->tipo_de_evento_id <> 3) {
      $no_mostrar_campos_abm = '|hora_lunes|hora_martes|hora_miercoles|hora_jueves|hora_viernes|hora_sabado|hora_domingo|direccion_del_curso|url_enlace_a_google_maps_curso';
    }
    else {
      $no_mostrar_campos_abm = '|direccion_de_inicio|url_enlace_a_google_maps_inicio|direccion_del_curso|url_enlace_a_google_maps_curso|latitud|longitud|url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual|resumen_de_la_conferencia';
    }
    
  }

  $no_mostrar_campos_abm .= '|url_enlace_a_google_maps_inicio_redirect_final|url_enlace_a_google_maps_curso_redirect_final';

  if ($rol_de_usuario_id > 2 or $Solicitud->fecha_de_solicitud <> '') {
    $gen_seteo['no_mostrar_campos_abm'] .= '|fecha_de_solicitud';
  }


  if ($Solicitud->tipo_de_evento_id <> 3) {
    $gen_seteo['no_mostrar_campos_abm'] .= '|fecha_de_inicio_del_curso_online|url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual';
  }


  $gen_seteo_fecha_de_evento = array(
    'gen_url_siguiente' => $gen_url_siguiente, 
    'filtros_por_campo' => array(
        'solicitud_id' => $Solicitud->id        
        ),
    'no_mostrar_campos_abm' => $no_mostrar_campos_abm
      );
  ?>   
       
  <script type="text/javascript">

    function crearABM_solicitud(gen_modelo, gen_accion, gen_id = null) {
      $("#modal-bodi-abm").html('<?php echo __('Cargando...') ?>');
      if (gen_modelo == 'Fecha_de_evento') {
        gen_seteo = '<?php echo serialize($gen_seteo_fecha_de_evento) ?>'
      }
      else  {
        gen_seteo = '<?php echo serialize($gen_seteo) ?>'
      }
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC') ?>crearabm',
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
            $("#modal-titulo").html('<?php echo __('Insertar') ?> '+gen_modelo);
          }
          if (gen_accion == 'm') {
            $("#modal-titulo").html('<?php echo __('Modificar') ?> '+gen_modelo);
          }
          if (gen_accion == 'b') {
            $("#modal-titulo").html('<?php echo __('Borrar') ?> '+gen_modelo);
          }

        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }

  </script>
<!-- FUNCIONES ABM Y MODIFICAR SOLICITUD -->      


<!-- FUNCIONES ABM Y MODIFICAR SOLICITUD -->
  <?php 
  $gen_seteo = array(
      'gen_url_siguiente' => $gen_url_siguiente, 
      'no_mostrar_campos_abm' => 'user_id|tipo_de_evento_id|idioma_id|nombre_del_solicitante|celular_del_solicitante|localidad_id|escribe_tu_ciudad_sino_esta_en_la_lista_anterior|pais_id|moneda_id|monto_a_invertir|nombre_responsable_de_inscripciones|celular_responsable_de_inscripciones|sino_solicitar_responsable_de_inscripcion|observaciones|created_at|updated_at|sino_aprobado_administracion|sino_aprobado_solicitar_revision|sino_aprobado_finalizada|fecha_de_solicitud|observaciones_aprobado_administracion|observaciones_aprobado_solicitar_revision|observaciones_aprobado_finalizada|hash|ejecutivo|sino_cancelada|sino_envio_enlaces_a_resp_inscripcion|paypal_transaction_id|payment_pending_reason|payment_error_code|payment_status|payment_paid|payment_paid_date|paypal_payerid|paypal_token|paypal_value|payment_checkout_status|observaciones_cancelada|titulo_del_formulario_personalizado|subtitulo_del_formulario_personalizado|img_imagen_del_formulario_personalizada|rtf_resumen_del_formulario_personalizado|rtf_texto_del_formulario_personalizado|colpick_color_de_fondo_del_formulario'
    );
  ?>   
       
  <script type="text/javascript">

    function crearABM_datos_face(gen_modelo, gen_accion, gen_id = null) {

      gen_seteo = '<?php echo serialize($gen_seteo) ?>'
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC') ?>crearabm',
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
          if (gen_accion == 'm') {
            $("#modal-titulo").html('<?php echo __('Ingresar Datos de Campaña de Facebook') ?>');
          }

        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }

  </script>
<!-- FUNCIONES ABM Y MODIFICAR SOLICITUD -->      

<!-- FUNCIONES APROBACIONES Y REVISION -->
  <script type="text/javascript">
    

    function aprobacionPublished(estado) {

      if (estado) {
        sino_is_published = 'SI';
      }
      else {
        sino_is_published = 'NO';
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/aprobacion-published/<?php echo $Solicitud->campania_mautic_id ?>/'+sino_is_published,
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          sino_is_published: sino_is_published
        },
        success: function success(data, status) {    

          if (data == 'SI') {
            var txt_sino_is_published = 'Campaña de mailing lista para enviar';
            $("#box-footer-solicitud-administracion").attr('class', 'box-footer bg-olive');
          }
          else {
              var txt_sino_is_published = 'Campaña desactivada, no se enviara';
            $("#box-footer-solicitud-administracion").attr('class', 'box-footer bg-red');
          }

          $("#estado_sino_is_published").html(txt_sino_is_published);

          
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }


    function aprobacionAdministracion(estado) {

      if (estado) {
        sino_aprobado_administracion = 'SI';
        $("#observaciones_aprobado_administracion").attr('class', 'form-control oculto');
        $("#observaciones_aprobado_administracion").val('');
      }
      else {
        sino_aprobado_administracion = 'NO';
        $("#observaciones_aprobado_administracion").attr('class', 'form-control visible');
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/aprobacion-administracion/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          sino_aprobado_administracion: sino_aprobado_administracion
        },
        success: function success(data, status) {    

          if (data == 'SI') {
            var txt_sino_aprobado_administracion = 'Aprobado';
            $("#box-footer-solicitud-administracion").attr('class', 'box-footer bg-olive');
          }
          else {
              var txt_sino_aprobado_administracion = 'Desaprobado';
            $("#box-footer-solicitud-administracion").attr('class', 'box-footer bg-red');
          }

          $("#estado_sino_aprobado_administracion").html(txt_sino_aprobado_administracion);

          
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }


    function aprobacioncancelada(estado) {

      if (estado) {
        sino_cancelada = 'SI';
        $("#observaciones_cancelada").attr('class', 'form-control visible');
      }
      else {
        sino_cancelada = 'NO';
        $("#observaciones_cancelada").attr('class', 'form-control oculto');
        $("#observaciones_cancelada").val('');
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/aprobacion-cancelada/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          sino_cancelada: sino_cancelada
        },
        success: function success(data, status) {    

          if (data == 'SI') {
            var txt_sino_cancelada = 'SI';
            $("#box-footer-solicitud-cancelada").attr('class', 'box-footer bg-red');
          }
          else {
              var txt_sino_cancelada = 'NO';
            $("#box-footer-solicitud-cancelada").attr('class', 'box-footer bg-grey');
          }

          $("#estado_sino_cancelada").html(txt_sino_cancelada);

          
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }


    function aprobacionfinalizada(estado) {

      if (estado) {
        sino_aprobado_finalizada = 'SI';
        $("#observaciones_aprobado_finalizada").attr('class', 'form-control visible');
        $("#observaciones_aprobado_finalizada").val('');
      }
      else {
        sino_aprobado_finalizada = 'NO';
        $("#observaciones_aprobado_finalizada").attr('class', 'form-control oculto');
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/aprobacion-finalizada/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          sino_aprobado_finalizada: sino_aprobado_finalizada
        },
        success: function success(data, status) {    

          if (data == 'SI') {
            var txt_sino_aprobado_finalizada = 'SI';
            $("#box-footer-solicitud-finalizada").attr('class', 'box-footer bg-blue');
          }
          else {
              var txt_sino_aprobado_finalizada = 'NO';
            $("#box-footer-solicitud-finalizada").attr('class', 'box-footer bg-grey');
          }

          $("#estado_sino_aprobado_finalizada").html(txt_sino_aprobado_finalizada);

          
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }

    function aprobacionSolicitarRevision(estado) {

      if (estado) {
        sino_aprobado_solicitar_revision = 'SI';
      }
      else {
        sino_aprobado_solicitar_revision = 'NO';
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/aprobacion-solicitar-revision/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          sino_aprobado_solicitar_revision: sino_aprobado_solicitar_revision
        },
        success: function success(data, status) {    

          if (data == 'SI') {
            var txt_sino_aprobado_solicitar_revision = 'Solicitada';
            $("#box-footer-solicitud-solicitar_revision").attr('class', 'box-footer bg-yellow');
          }
          else {
              var txt_sino_aprobado_solicitar_revision = 'Atendida';
            $("#box-footer-solicitud-solicitar_revision").attr('class', 'box-footer bg-blue');
          }

          $("#estado_sino_aprobado_solicitar_revision").html(txt_sino_aprobado_solicitar_revision);

          
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }

    function guardarObsAdm(observaciones_aprobado_administracion) {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/guardar-obs-adm/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          observaciones_aprobado_administracion: observaciones_aprobado_administracion
        },
        success: function success(data, status) {  
          //$("#observaciones_aprobado_administracion_mensaje").html('guardado');     
        }
      });  
    }


    function guardarObsFin(observaciones_aprobado_finalizada) {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/guardar-obs-fin/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          observaciones_aprobado_finalizada: observaciones_aprobado_finalizada
        },
        success: function success(data, status) {  
          //$("#observaciones_aprobado_finalizada_mensaje").html('guardado');        
        }
      });  
    }


    function guardarObsCanc(observaciones_cancelada) {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/guardar-obs-canc/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          observaciones_cancelada: observaciones_cancelada
        },
        success: function success(data, status) {  
          //$("#observaciones_aprobado_finalizada_mensaje").html('guardado');        
        }
      });  
    }

    function guardarObsSolRev(observaciones_aprobado_solicitar_revision) {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/guardar-obs-sol-rev/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          observaciones_aprobado_solicitar_revision: observaciones_aprobado_solicitar_revision
        },
        success: function success(data, status) {  
          //$("#observaciones_aprobado_garantes_mensaje").html('guardado');        
        }
      });  
    }

  </script>
<!-- FUNCIONES APROBACIONES Y REVISION -->

<!-- INICIO APP app-solicitud -->
    <script type="text/javascript">
        const config = {
          locale: 'es', 
        };
        //moment.locale('es');
        //console.log(moment());
        Vue.use(VeeValidate, config);

        var app = new Vue({
          el: '#app-solicitud',

          data: {
            mensaje_extra: '',
            envio_enlaces_a_resp_inscripcion: <?php echo sino_a_tf($Solicitud->sino_envio_enlaces_a_resp_inscripcion) ?>
          },

          methods: {                

            setearSino: function (codigo, inscripcion_id) {

              if (codigo == 1) {
                  estado = this.envio_enlaces_a_resp_inscripcion;  
              }

              if (estado) {
                sino = 'SI';
              }
              else {
                sino = 'NO';
              }
              

              $.ajax({
                url: '<?php echo env('PATH_PUBLIC')?>f/i/setear-sino-solicitud/'+codigo+'/'+<?php echo $Solicitud->id ?>,
                type: 'POST',
                dataType: 'html',
                async: true,
                data:{
                  _token: "{{ csrf_token() }}",
                  sino: sino
                },
                success: function success(data, status) {    
                  //$("#resultado-a").html(data);
                  
                },
                error: function error(xhr, textStatus, errorThrown) {
                    alert(errorThrown);
                }
              });


            },
            

            marcar_envio: function (codigo, inscripcion_id) {
              if (codigo == 1) {
                  this.envio_enlaces_a_resp_inscripcion = true;  
              }

              this.setearSino(codigo, this.envio_enlaces_a_resp_inscripcion)
              /*
              $.ajax({
                url: '<?php echo env('PATH_PUBLIC')?>f/i/registrar-envio/'+codigo+'/'+inscripcion_id,
                type: 'POST',
                dataType: 'html',
                async: true,
                data:{
                  _token: "{{ csrf_token() }}",
                  sino: sino
                },
                success: function success(data, status) {    

                  
                },
                error: function error(xhr, textStatus, errorThrown) {
                    alert(errorThrown);
                }
              });
              */

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
            

            url_mensaje_extra: function (celular, nombre, apellido) {
              mensaje = '<?php echo __('Hola').' '.$Solicitud->nombre_responsable_de_inscripciones.' '.__('mi nombre es').' '.Auth::user()->name.'. '.__('Te estoy enviando los datos de la campaña').': *'.$Solicitud->localidad_nombre()?>*:\n\n'
              mensaje = mensaje +'*<?php echo __('Formulario de Inscripcion') ?>*:\n <?php echo $Solicitud->url_form_inscripcion() ?> \n\n'
              mensaje = mensaje + '*<?php echo __('Planilla de Inscripción') ?>*:\n<?php echo $Solicitud->url_planilla_inscripcion() ?>\n\n'
              mensaje = mensaje + '*<?php echo __('NUEVO!') ?> -> <?php echo __('Invitar a Contactos Históricos') ?>*:\n<?php echo $Solicitud->url_planilla_contactos_historicos() ?>\n\n'
              mensaje = mensaje + '*<?php echo __('Descargar lista de inscriptos a Excel') ?>*:\n <?php echo $Solicitud->url_planilla_inscripcion_excel(0) ?>\n\n'
              mensaje = mensaje + '*<?php echo __('Planilla de Asistencia') ?>*:\n <?php echo $Solicitud->url_planilla_asistencia() ?>\n\n'
                mensaje = mensaje + '*<?php echo __('Encuesta de Satisfacción') ?>*:\n <?php echo $Solicitud->url_encuesta_de_satisfaccion() ?>\n\n'
                mensaje = mensaje + '*<?php echo __('Usuario registrante') ?>*:\n <?php echo $Solicitud->user->name ?>\n'
                mensaje = mensaje + ' <?php echo __('Celular') ?>: +<?php echo $Solicitud->user->celular ?>\n'
                mensaje = mensaje + ' _<?php echo __('Contactar') ?>:_ https://api.whatsapp.com/send?phone=<?php echo $Solicitud->celular_wa($Solicitud->user->celular) ?>\n\n'
              url_mensaje_extra = 'https://api.whatsapp.com/send?phone='+celular+'&text='+mensaje;
              url_mensaje_extra = encodeURI(url_mensaje_extra)
              return url_mensaje_extra
            }
              
          },


        })
    </script>
<!-- FIN APP app-solicitud -->


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
              'order': [[ 1, 'asc' ]],
              'columnDefs': [{ "width": "100px", "targets": 0 }], 
          })
        })


        $(function () {
          $('#table-campaign').DataTable({
            'bLengthChange' : false,
            'pageLength' : 10,
            'language': {
                  'autoWidth': true,
                  'search': '<?php echo __('Buscar') ?>',
                  'zeroRecords': '<?php echo __('No hay resultados para la busqueda') ?>',
                  'info': '',
                  'infoEmpty': 'No hay registros',
                  'paginate': {
                      'first':      '<?php echo __('Primero') ?>',
                      'last':       '<?php echo __('Ultimo') ?>',
                      'next':       '<?php echo __('Siguiente') ?>',
                      'previous':   '<?php echo __('Anterior') ?>'
                  },
                  'infoFiltered': '(<?php echo __('filtrado') ?> _MAX_ <?php echo __('registros totales') ?>)'
              },
              'order': [[ 0, 'asc' ]],
              'columnDefs': [{ "width": "100px", "targets": 0 }], 
          })
        })

      </script>
<style type="text/css">
  .dataTables_filter{
      float: right;
    }
</style>
@endsection

