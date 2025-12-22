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
                <?php echo __('Cancelados') ?> @{{ cant_cancelados }} | 
                <span v-show="mostrar_fechas">
                  <?php echo __('Confirmados') ?> @{{ cant_confirmados }} | 
                  <?php echo __('Asistentes') ?> @{{ cant_asistentes }} |
                </span>
              </p>
              
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
                          <th><?php echo __('Cancelo') ?></th>
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


                      ?>

                        <tr v-show="mostrarFila(<?php echo $i ?>)" v-bind:style="class_promocionado(estados[<?php echo $i ?>].promocionado)">
                            <td v-show="show_col_id"><?php echo $Inscripcion->id; ?></td>
                            <td v-show="show_col_nro_orden"><?php echo $nro_de_orden+1; ?></td>
                            <?php if ($tipo_de_evento_id == 3) { ?>
                            <td v-show="show_col_ciudad"><?php echo $Inscripcion->ciudad; ?></td>
                            <?php } ?>
                            <td v-show="show_col_grupo"><?php echo $Inscripcion->grupo; ?></td>
                            <td v-show="show_col_prioridad">{{ calc_prioridad(<?php echo $i ?>) }}</td>
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
                              <span v-if="estados[<?php echo $i ?>].cancelo">SI</span>                                  
                            </td>
                        </tr>
                    <?php } ?>
                  </tbody>
                  </table>




            </div>
            <!-- /.box-body -->
          </div>
      </div>



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
                show_col_nro_orden: true,
                show_col_prioridad: false,
                show_col_comprimido: false,
                show_col_fecha: false,
                show_col_apellido: true,
                show_col_nombre: true,
                show_col_celular: false,
                show_col_email_correo: false,
                show_col_fecha_de_evento: true,
                <?php if ($tipo_de_evento_id == 3) { ?>
                show_col_pais: false,
                show_col_ciudad: true,
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
                    { detalle: '<?php echo __('Solo cancelados o con baja') ?>', id: 'solo_cancelados'}
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







    </body>
</html>
