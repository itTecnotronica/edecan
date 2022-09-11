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
        <title><?php echo __('Planilla de Asistencia') ?> |  {{ __($Solicitud->tipo_de_evento->tipo_de_evento) }} {{ $localidad_text }}</title>

      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      <!-- Bootstrap 3.3.7 -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/bootstrap/dist/css/bootstrap.min.css">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/font-awesome/css/font-awesome.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/Ionicons/css/ionicons.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>dist/css/AdminLTE.min.css">
      <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>dist/css/skins/_all-skins.min.css">
      <!-- Morris chart -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/morris.js/morris.css">
      <!-- jvectormap -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/jvectormap/jquery-jvectormap.css">
      <!-- Date Picker -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
      <!-- Daterange picker -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
      <!-- bootstrap wysihtml5 - text editor -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      
      <!-- DataTables -->
      <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


      <!-- Google Font -->
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

      <!-- jQuery 3 -->
      <script src="<?php echo $dominio_publico?>bower_components/jquery/dist/jquery.min.js"></script>

      <link rel="stylesheet" href="<?php echo $dominio_publico?>css/generic.css">
      <link rel="stylesheet" href="<?php echo $dominio_publico?>css/style.css">

    <script src="<?php echo $dominio_publico?>js/vue/vue.js"></script>
    <script src="<?php echo $dominio_publico?>js/vee-validate/dist/vee-validate.js"></script>
    <script src="<?php echo $dominio_publico?>js/vee-validate/dist/locale/es.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $dominio_publico?>js/vue-form-generator/vfg.css">

    <style type="text/css">
      .table-striped>tbody>tr:nth-of-type(odd) {
        background-color: #efefef;
      }
    </style>

    </head>
    <body style="overflow-x: auto;"> 


    <div id="app-lista">
      <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo __('Planilla de Asistencia') ?> <?php echo $Solicitud->descrip_modelo(); ?></h3>
              <p class="bg-info">
                <select v-model="select_fechas_de_eventos" v-on:change="filtrar_tabla()" v-show="mostrar_fechas">
                  <option v-for="fecha_de_evento in fechas_de_evento" v-bind:value="fecha_de_evento.id">
                    @{{ fecha_de_evento.detalle }}
                  </option>
                </select>
              <strong> Totales:</strong> 
              <?php echo __('Inscriptos') ?> @{{ cant_inscriptos }} | 
              <?php echo __('Contactados') ?> @{{ cant_contactados }} | 
              <span v-show="mostrar_fechas">
                <?php echo __('Confirmados') ?> @{{ cant_confirmados }} | 
                <?php echo __('Voucher') ?> @{{ cant_voucher }} | 
                <?php echo __('Recordatorio') ?> @{{ cant_recordatorio }} | 
                <?php echo __('Asistentes') ?> @{{ cant_asistentes }} |
              </span>
            </p>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              


              
              <div class="col-xs-6 col-lg-2" style="margin-top: 10px; "> 
                <select v-model="valor_select_ver" class="form-control">
                  <option v-for="select in select_ver" v-bind:value="select.id">
                    @{{ select.detalle }}
                  </option>
                </select>
              </div>
              
              <div class="col-xs-6 col-lg-2" style="margin-bottom: 20px">  
                <div class="btn btn-block btn-social btn-instagram" data-toggle="modal" data-target="#modal-columnas" target="_blank" style="margin-top: 10px;">
                  <i class="fa fa-columns"></i> 
                  <span class="hidden-xs"><?php echo __('Habilitar columnas') ?></span>
                  <span class="hidden-lg hidden-sm hidden-md"><?php echo __('Columnas') ?></span>
                </div>              
              </div>

              <table id="table" class="table table-bordered table-striped" style="max-width: 500px" >
                  <thead>
                      <tr>
                          <th v-show="show_col_id"><?php echo __('ID') ?></th>
                          <th v-show="show_col_grupo"><?php echo __('Grupo de whatsapp') ?></th>
                          <th v-show="show_col_nro_orden"><?php echo __('Nro de Orden') ?></th>
                          <?php if ($Solicitud->tipo_de_evento_id == 3) { ?>
                          <th v-show="show_col_ciudad"><?php echo __('Ciudad') ?></th>
                          <?php } ?>
                          <th v-show="show_col_comprimido"><?php echo __('Datos') ?></th>
                          <th v-show="show_col_fecha"><?php echo __('Fecha') ?></th>
                          <th v-show="show_col_apellido"><?php echo __('Apellido') ?></th>
                          <th v-show="show_col_nombre"><?php echo __('Nombre') ?></th>
                          <th v-show="show_col_celular"><?php echo __('Celular') ?></th>
                          <th v-show="show_col_celular"></th>
                          <th v-show="show_col_email_correo"><?php echo __('Correo') ?></th>
                          <th v-show="show_col_fecha_de_evento && mostrar_fechas"><?php echo __('Horario') ?></th>
                          <?php if ($Solicitud->tipo_de_evento_id == 3) { ?>
                          <th v-show="show_col_pais"><?php echo __('Pais') ?></th>
                            <?php foreach ($Lecciones as $Leccion) { ?>
                              <th><?php echo $Leccion->codigo_de_la_leccion ?></th>
                            <?php } ?>
                            <?php foreach ($Lecciones_extra as $Leccion) { ?>
                              <th><?php echo $Leccion->nro_o_codigo ?></th>
                            <?php } ?>
                          <?php } ?>
                          <?php if ($Solicitud->tipo_de_evento_id <> 3) { ?>
                          <th v-show="show_col_estado"><?php echo __('Asistio') ?></th>
                          <?php } ?>
                      </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $i = -1;
                    foreach ($Inscripciones as $Inscripcion) { 
                      $i++;
                      if ($Inscripcion->fecha_de_evento_id > 0) {
                        $url_whatsapp = $Inscripcion->url_whatsapp();
                        $url_pedido_de_confirmacion = $url_whatsapp['pedido_de_confirmacion'];
                        $url_no_respondieron_al_pedido_de_confirmacion = $url_whatsapp['no_respondieron_al_pedido_de_confirmacion'];                      
                        $url_envio_de_voucher = $url_whatsapp['envio_de_voucher'];
                        $url_envio_de_motivacion = $url_whatsapp['envio_de_motivacion'];
                        $url_envio_de_recordatorio = $url_whatsapp['envio_de_recordatorio'];
                        $url_contesto_consulta = $url_whatsapp['contesto_consulta'];
                      }

                    ?>

                        <tr v-show="mostrarFila(<?php echo $i ?>)" v-bind:class="class_cancelo(estados[<?php echo $i ?>].cancelo)">
                            <td v-show="show_col_id"><?php echo $Inscripcion->id; ?></td>
                            <td v-show="show_col_grupo"><?php echo $Inscripcion->grupo; ?></td>
                            <td v-show="show_col_nro_orden"><?php echo $i+1; ?></td>
                            <?php if ($Solicitud->tipo_de_evento_id == 3) { ?>
                            <td v-show="show_col_ciudad"><?php echo $Inscripcion->ciudad; ?></td>
                            <?php } ?>
                            

                            <td v-show="show_col_comprimido">
                              ID: <?php echo $Inscripcion->id; ?><br>
                              <?php echo $Inscripcion->apellido; ?><br>
                              <?php echo $Inscripcion->nombre; ?><br>
                              <?php echo $Inscripcion->celular; ?> <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_inscripcion('Inscripcion', 'm', <?php echo $Inscripcion->id ?>)"><?php echo __('Modificar') ?></button> <br>
                              <?php echo $Inscripcion->email_correo; ?><br>
                              <i>
                              <?php 
                              if ($Inscripcion->fecha_de_evento_id > 0) {
                                echo $Inscripcion->fecha_de_evento->armarDetalleFechasDeEventos(); 
                              }
                              else {
                                echo $mensaje_np;
                              }
                              ?>
                            </i><br>
                              <?php if ($Inscripcion->consulta <> '') {?>
                                <p style="color: red"><strong>Consulta: <?php echo $Inscripcion->consulta ?></strong></p>
                              <?php } ?>
                              <br>
                              <strong>Estado: {{ span_estado(<?php echo $i ?>) }}</strong>
                            </td>
                            <td v-show="show_col_fecha"><?php echo $gCont->FormatoFechayYHora($Inscripcion->created_at); ?></td>
                            <td v-show="show_col_apellido"><?php echo $Inscripcion->apellido; ?></td>
                            <td v-show="show_col_nombre"><?php echo $Inscripcion->nombre; ?></td>
                            <td v-show="show_col_celular"><?php echo $Inscripcion->celular; ?></td>
                            <td v-show="show_col_celular">                     
                                <a href="http://api.whatsapp.com/send?phone=<?php echo $Inscripcion->celular_wa(); ?>" target="_blank">
                                  <button type="button" class="btn btn-success btn-xs"><i class="fa fa-fw fa-whatsapp" style="font-size: 19px"></i></button>
                                </a>                    
                            </td>
                            <td v-show="show_col_email_correo"><?php echo $Inscripcion->email_correo; ?></td>
                            <td v-show="show_col_fecha_de_evento && mostrar_fechas">
                              <?php 
                              if ($Inscripcion->fecha_de_evento_id > 0) {
                                echo $Inscripcion->fecha_de_evento->armarDetalleFechasDeEventos('html', true, $Idioma_por_pais, $Solicitud, $idioma); 
                              }
                              else {
                                echo $mensaje_np.'<br>';
                              }
                              ?>    
                            </td>
                            <?php if ($Solicitud->tipo_de_evento_id == 3) { ?>
                              <td v-show="show_col_pais"><?php echo $Inscripcion->nombre_pais; ?></td>                            
                              <?php foreach ($Lecciones as $Leccion) { ?>
                                <td>
                                  <!-- ASISTIO -->
                                  <label class="switch switch-inscripcion">
                                    <input type="checkbox" v-on:change="setearAsistencia(<?php echo $Leccion->id ?>, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].asistencia['<?php echo $Leccion->id ?>']"> 
                                    <span class="slider round"><p style="padding-left: 10px; padding-top: 7px"><?php echo $Inscripcion->asistio_a_leccion($Leccion->id); ?></p></span>                                      
                                  </label>

                                </td>
                              <?php } ?>                  
                              <?php foreach ($Lecciones_extra as $Leccion) { ?>
                                <td>
                                  <?php 
                                    $asistencia = $Inscripcion->asistencias->where('leccion_extra_id', $Leccion->id)->all();
                                    if (count($asistencia) > 0) {
                                        $sino_asistencia = 'SI';
                                    }
                                    else {
                                        $sino_asistencia = '';
                                    }
                                    echo $sino_asistencia;  

                                  ?>

                                </td>
                              <?php } ?>
                            <?php } ?>


                            <?php if ($Solicitud->tipo_de_evento->id <> 3) { ?>
                            <td v-show="show_col_estado">
                              <!-- ASISTIO -->
                              <div v-bind:class="class_sino(estados[<?php echo $i ?>].asistio)">
                                
                                  <?php echo __('Asistio') ?>

                                  <label class="switch switch-inscripcion">
                                    <input  type="checkbox" v-on:change="setearSino(8, <?php echo $i ?>, <?php echo $Inscripcion->id ?>)" v-model="estados[<?php echo $i ?>].asistio">
                                    <span class="slider round"></span>
                                    
                                  </label>
                              </div>
                            </td>



                            <?php } ?>
                        </tr>
                    <?php } ?>
              </tbody>
              </table>

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

                <?php if ($Solicitud->tipo_de_evento_id == 3) { ?>
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
              'order': [[ 1, 'asc' ]],
              'columnDefs': [{ "width": "100px", "targets": 0 }], 
          })
        })
      </script>


    <!-- jQuery 3 -->
    <script src="<?php echo $dominio_publico?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo $dominio_publico?>bower_components/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo $dominio_publico?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Morris.js charts -->
    <script src="<?php echo $dominio_publico?>bower_components/raphael/raphael.min.js"></script>
    <script src="<?php echo $dominio_publico?>bower_components/morris.js/morris.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo $dominio_publico?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="<?php echo $dominio_publico?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?php echo $dominio_publico?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo $dominio_publico?>bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo $dominio_publico?>bower_components/moment/min/moment.min.js"></script>
    <script src="<?php echo $dominio_publico?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- datepicker -->
    <script src="<?php echo $dominio_publico?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="<?php echo $dominio_publico?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- Slimscroll -->
    <script src="<?php echo $dominio_publico?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo $dominio_publico?>bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo $dominio_publico?>dist/js/adminlte.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?php echo $dominio_publico?>dist/js/pages/dashboard.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo $dominio_publico?>dist/js/demo.js"></script>
    <!-- DataTables -->
    <script src="<?php echo $dominio_publico?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $dominio_publico?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

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
                show_col_id: false,
                show_col_nro_orden: false,
                show_col_prioridad: false,
                show_col_comprimido: true,
                show_col_fecha: false,
                show_col_apellido: false,
                show_col_nombre: false,
                show_col_celular: true,
                show_col_email_correo: false,
                show_col_fecha_de_evento: false,
                <?php if ($Solicitud->tipo_de_evento_id == 3) { ?>
                show_col_pais: false,
                show_col_ciudad: false,
                <?php } ?>
                show_col_estado: true,
                show_col_grupo: true,
                estados: [
                <?php 
                foreach ($Inscripciones as $Inscripcion) { 
                  $fecha_de_evento_id = '-1';
                  if ($Inscripcion->fecha_de_evento_id <> '') {
                    $fecha_de_evento_id = $Inscripcion->fecha_de_evento_id;
                  }
                ?>
                      {
                        inscripcion_id: <?php echo $Inscripcion->id ?>,
                        fecha_de_evento_id: <?php echo $fecha_de_evento_id ?>,
                        envio_pedido_de_confirmacion: <?php echo sino_a_tf($Inscripcion->sino_envio_pedido_de_confirmacion) ?>,
                        envio_recordatorio_pedido_de_confirmacion: <?php echo sino_a_tf($Inscripcion->sino_envio_recordatorio_pedido_de_confirmacion) ?>,
                        confirmo: <?php echo sino_a_tf($Inscripcion->sino_confirmo) ?>,
                        envio_voucher: <?php echo sino_a_tf($Inscripcion->sino_envio_voucher) ?>,
                        envio_motivacion: <?php echo sino_a_tf($Inscripcion->sino_envio_motivacion) ?>,
                        envio_recordatorio: <?php echo sino_a_tf($Inscripcion->sino_envio_recordatorio) ?>,
                        asistio: <?php echo sino_a_tf($Inscripcion->sino_asistio) ?>,
                        cancelo: <?php echo sino_a_tf($Inscripcion->sino_cancelo) ?>,
                        causa_de_baja_id: '<?php echo $Inscripcion->causa_de_baja_id ?>',
                        asistencia: {                        
                          <?php foreach ($Lecciones as $Leccion) { ?>   
                                  
                            <?php echo $Leccion->id ?>: '<?php echo $Inscripcion->asistio_a_leccion($Leccion->id); ?>',
                            
                          <?php } ?>     

                        }

                      },
                <?php } ?>
                ],
                select_fechas_de_eventos: 'todos',
                fechas_de_evento: [
                    { detalle: '<?php echo __('Todos') ?>', id: 'todos'},
                    { detalle: '<?php echo __('No pueden asistir') ?>', id: '-1'},
                  <?php 
                  foreach ($Fechas_de_evento as $Fecha_de_evento) { 
                  ?>
                    { detalle: '<?php echo $Fecha_de_evento->armarDetalleFechasDeEventos('select') ?>', id: <?php echo $Fecha_de_evento->id ?> },
                  <?php } ?>
                ],
                mostrar_fechas: <?php echo $mostrar_fechas ?>,
                valor_select_ver: 'todos',
                select_ver: [
                    { detalle: '<?php echo __('Ver todos') ?>', id: 'todos'},
                    { detalle: '<?php echo __('Ocultar cancelados o con baja') ?>', id: 'ocultar_cancelados'},
                    { detalle: '<?php echo __('Solo cancelados o con baja') ?>', id: 'solo_cancelados'},
                    { detalle: '<?php echo __('Sin contactar') ?>', id: 'sin_contactar'}
                ],
              },

              methods: {                

                mostrarFila: function (i) {
                  mostrar = false
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

                  return mostrar
                  
              },

                setearSino: function (codigo, i, inscripcion_id) {

                  if (codigo == 8) {
                      estado = this.estados[i].asistio;  
                  }

                  if (estado) {
                    sino = 'SI';
                  }
                  else {
                    sino = 'NO';
                  }
                  

                  $.ajax({
                    url: '<?php echo $dominio_publico?>f/i/setear-sino/'+codigo+'/'+inscripcion_id+'/<?php echo $Solicitud->id ?>',
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


                },



                setearAsistencia: function (leccion_id, i, inscripcion_id) {

                  estado = this.estados[i].asistencia[leccion_id];   
                  if (estado) {
                    sino = 'SI';
                  }
                  else {
                    sino = 'NO';
                  }
                  

                  $.ajax({
                    url: '<?php echo $dominio_publico?>f/i/setear-asistencia/'+leccion_id+'/'+inscripcion_id,
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

                marcar_envio: function (codigo, i, inscripcion_id) {
                  if (codigo == 8) {
                      this.estados[i].asistio = true;  
                  }
                  this.setearSino(codigo, i, this.estados[i].inscripcion_id)

                  $.ajax({
                    url: '<?php echo $dominio_publico?>f/i/registrar-envio/'+codigo+'/'+inscripcion_id,
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

                class_cancelo: function (sino) {
                  clase = ''
                  if (sino) {
                    clase = 'bg-red'
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
                    estado = '<?php echo __('Pedido de confirmación enviado') ?>'

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
                    estado = '<?php echo __('Inscripto sin pedido de confirmación') ?>'
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

                    // total confirmados                    
                    if (situacion == 'confirmados') {
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].confirmo) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total voucher                    
                    if (situacion == 'voucher') {
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].envio_de_voucher) {
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
                      }
                    }

                    // total contactados
                    if (situacion == 'contactados') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].envio_pedido_de_confirmacion) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total confirmados
                    if (situacion == 'confirmados') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].confirmo) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total contactados
                    if (situacion == 'voucher') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].envio_de_voucher) {
                          cant = cant + 1
                        }
                      }
                    }


                    // total recordatorio
                    if (situacion == 'recordatorio') {                
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].fecha_de_evento_id == this.select_fechas_de_eventos && this.estados[i].envio_de_recordatorio) {
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
                      }
                    }



                  }
                  return cant
                },
                  
              },

              computed: {
                cant_inscriptos: function () {
                 cant = this.contar_cant_inscriptos('inscriptos')
                 return cant
                },
                
                cant_contactados: function () {
                 cant = this.contar_cant_inscriptos('contactados')
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

    <!-- FUNCIONES ABM Y MODIFICAR INSCRIPCION -->
      <?php 
      $gen_seteo = array(
        'gen_url_siguiente' => 'back', 
        'no_mostrar_campos_abm' => 'solicitud_id|fecha_de_evento_id|sino_notificar_proximos_eventos|sino_acepto_politica_de_privacidad|sino_envio_pedido_de_confirmacion|sino_confirmo|sino_envio_recordatorio_pedido_de_confirmacion|sino_envio_voucher|sino_envio_motivacion|sino_envio_recordatorio|sino_asistio'
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
                $("#modal-titulo").html('Insertar '+gen_modelo);
              }
              if (gen_accion == 'm') {
                $("#modal-titulo").html('Modificar '+gen_modelo);
              }
              if (gen_accion == 'b') {
                $("#modal-titulo").html('Borrar '+gen_modelo);
              }

            },
            error: function error(xhr, textStatus, errorThrown) {
                alert(errorThrown);
            }
          });
        }

        $( document ).ready(function() {
          if (screen.width > 1200 ) {
            var_app = app["_data"]
            var_app.show_col_nombre = true
            var_app.show_col_apellido = true
            <?php if ($Solicitud->tipo_de_evento->id == 3) { ?>            
              var_app.show_col_email_correo = false
              var_app.show_col_fecha_de_evento = false
              var_app.show_col_estado = true
            <?php }
            else { ?>     
              var_app.show_col_email_correo = true
              var_app.show_col_fecha_de_evento = true
              var_app.show_col_estado = true
            <?php } ?>
            var_app.show_col_comprimido = false
          }
            //$('input[type="search"]').val(1111);
        });

      </script>
    <!-- FUNCIONES ABM Y MODIFICAR INSCRIPCION -->      



    </body>
</html>
