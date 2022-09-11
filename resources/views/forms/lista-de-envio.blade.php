<?php
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController();

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
$mensaje_np = __('No puedo asistir a este horario pero quisiera me contacten mas adelante por otros días y horarios');

$enviar_mail = 'false';
if (!Auth::guest()) {
    if(Auth::user()->id == 1 or Auth::user()->id == 33) {
      $enviar_mail = 'true';
    }
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


        <title><?php echo __('Contacto Histórico') ?> </title>

      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      <!-- Bootstrap 3.3.7 -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/font-awesome/css/font-awesome.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/Ionicons/css/ionicons.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/AdminLTE.min.css">
      <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/skins/_all-skins.min.css">
      <!-- Morris chart -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/morris.js/morris.css">
      <!-- jvectormap -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/jvectormap/jquery-jvectormap.css">
      <!-- Date Picker -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
      <!-- Daterange picker -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
      <!-- bootstrap wysihtml5 - text editor -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      
      <!-- DataTables -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


      <!-- Google Font -->
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

      <!-- jQuery 3 -->
      <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>

      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/generic.css">
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/style.css">

    <script src="<?php echo env('PATH_PUBLIC')?>js/vue/vue.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/vee-validate.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/locale/es.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.css">

    </head>
    <body style="overflow-x: auto;"> 


    <div id="app-lista">
      <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo __('Contacto Histórico') ?></h3>
              <p class="bg-info">
                <strong> Totales:</strong> 
                <?php echo __('Cantidad') ?> @{{ cant_inscriptos }} | 
                <?php echo __('Envio 1') ?> @{{ cant_envio_1 }} | 
                <?php echo __('Envio 2') ?> @{{ cant_envio_2 }} | 
                <?php echo __('Envio 3') ?> @{{ cant_envio_3 }} 
              </p>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              
              <table class="table">
                <tr>
                  <th><?php echo __('ID') ?></th>
                  <th><?php echo __('Prioridad') ?></th>
                  <th><?php echo __('Datos') ?></th>
                  <th><?php echo __('Apellido') ?></th>
                  <th><?php echo __('Nombre') ?></th>
                  <th><?php echo __('telefono') ?></th>
                  <th><?php echo __('Correo') ?></th>
                  <th><?php echo __('Estado') ?></th>
                  <th><?php echo __('Mensaje Extra') ?></th>
                </tr>
                <tr>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_id">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_prioridad">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_comprimido">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_apellido">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_nombre">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_telefono">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_email">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <td>
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <label class="switch">
                        <input type="checkbox" v-model="show_col_estado">
                        <span class="slider round"></span>
                      </label>
                    </div>                  
                  </td>
                  <td>
                    <button type="button" class="btn btn-default btn-md" data-toggle="modal" data-target="#modal-mensaje-extra"><?php echo __('Redactar') ?></button>           
                  </td>
                </tr>
              </table>

              <table id="table" class="table table-bordered table-striped" style="max-width: 500px" >
                  <thead>
                      <tr>
                          <th v-show="show_col_id"><?php echo __('ID') ?></th>
                          <th v-show="show_col_prioridad"><?php echo __('Prioridad') ?></th>
                          <th v-show="show_col_comprimido"><?php echo __('Datos') ?></th>
                          <th v-show="show_col_apellido"><?php echo __('Apellido') ?></th>
                          <th v-show="show_col_nombre"><?php echo __('Nombre') ?></th>
                          <th v-show="show_col_telefono"><?php echo __('telefono') ?></th>
                          <th v-show="show_col_email"><?php echo __('Correo') ?></th>
                          <th><?php echo __('Acción') ?></th>
                          <th v-show="show_col_estado"><?php echo __('Estado') ?></th>
                          <th v-show="mensaje_extra != ''"><?php echo __('Mensaje Extra') ?></th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php 
                    $i = -1;
                    foreach ($Contactos as $Contacto) { 
                      $i++;
                        $parametros['nombre'] = $Contacto->nombre;
                        $parametros['apellido'] = $Contacto->apellido;
                        $parametros['telefono'] = $Contacto->telefono;
                        $parametros['codigo_tel'] = $Contacto->codigo_tel;
                        
                        $url_whatsapp_1 = $ListasController->url_whatsapp($Lista_de_envio->mensaje_1, $parametros);
                        $url_whatsapp_2 = $ListasController->url_whatsapp($Lista_de_envio->mensaje_2, $parametros);
                        $url_whatsapp_3 = $ListasController->url_whatsapp($Lista_de_envio->mensaje_3, $parametros);

                        if ($Lista_de_envio->tipo_de_lista_de_envio_id == 1) {
                          $gen_modelo_abm = 'Contacto';
                        }
                        else {
                          $gen_modelo_abm = 'Inscripcion';
                        }
             
                      ?>

                        <tr>
                            <td v-show="show_col_id"><?php echo $Contacto->id; ?></td>
                            <td v-show="show_col_prioridad">{{ calc_prioridad(<?php echo $i ?>) }}</td>
                            <td v-show="show_col_comprimido">
                              ID: <?php echo $Contacto->id; ?><br>
                              <?php echo $Contacto->apellido; ?><br>
                              <?php echo $Contacto->nombre; ?><br>
                              <?php echo $Contacto->telefono; ?>  <br>
                              <?php echo $Contacto->email; ?><br>
                            <br>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_inscripcion('<?php echo $gen_modelo_abm ?>', 'm', <?php echo $Contacto->id ?>)"><?php echo __('Modificar') ?> <?php echo __('Datos') ?></button> 
                            <br>
                            <br>
                              <?php if ($Contacto->observaciones <> '') {?>
                                <p style="color: blue"><strong><?php echo __('Observaciones') ?>: <?php echo $Contacto->observaciones ?></strong></p>
                              <?php } ?>



                              <a href="<?php echo env('PATH_PUBLIC')?>f/contactDown/<?php echo $Contacto->id; ?>/TMG/1" target="_blank">
                                <button type="button" alt="editar" title="editar" class="btn btn-verde btn-md"><i class="fa fa-mobile"></i> <?php echo __('Agendar Contacto vCard') ?></button>
                              </a>
                              <br>
                              <strong>Estado: {{ span_estado(<?php echo $i ?>) }}</strong>
                            </td>
                            <td v-show="show_col_apellido"><?php echo $Contacto->apellido; ?></td>
                            <td v-show="show_col_nombre"><?php echo $Contacto->nombre; ?></td>
                            <td v-show="show_col_telefono"><?php echo $Contacto->telefono; ?></button></td>
                            <td v-show="show_col_email"><?php echo $Contacto->email; ?></td>                            
                            <td>

                              <!-- ENVIO 1 -->
                              <?php if ($Lista_de_envio->titulo_mensaje_1 <> '') { ?>
                                <div v-bind:class="class_sino(estados[<?php echo $i ?>].sino_envio_1)">
                                  
                                    <a href="<?php echo $url_whatsapp_1; ?>" target="_blank">
                                      <button type="button" class="btn btn-blanco" alt="editar" title="editar" v-on:click="marcar_envio(1, 1, <?php echo $i; ?>, <?php echo $Contacto->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                    </a>
                                    <?php echo $Lista_de_envio->titulo_mensaje_1 ?>

                                    <label class="switch switch-inscripcion">
                                      <input  type="checkbox" v-on:change="setearSino(1, <?php echo $i ?>, <?php echo $Contacto->id ?>)" v-model="estados[<?php echo $i ?>].sino_envio_1">
                                      <span class="slider round"></span>
                                      
                                    </label>
                                </div>
                              <?php } ?>


                              <!-- ENVIO 2 -->
                              <?php if ($Lista_de_envio->titulo_mensaje_2 <> '') { ?>
                                <div v-bind:class="class_sino(estados[<?php echo $i ?>].sino_envio_2)">
                                  
                                    <a href="<?php echo $url_whatsapp_2; ?>" target="_blank">
                                      <button type="button" class="btn btn-blanco" alt="editar" title="editar" v-on:click="marcar_envio(1, 2, <?php echo $i; ?>, <?php echo $Contacto->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                    </a>
                                    <?php echo $Lista_de_envio->titulo_mensaje_2 ?>

                                    <label class="switch switch-inscripcion">
                                      <input  type="checkbox" v-on:change="setearSino(2, <?php echo $i ?>, <?php echo $Contacto->id ?>)" v-model="estados[<?php echo $i ?>].sino_envio_2">
                                      <span class="slider round"></span>
                                      
                                    </label>
                                </div>
                              <?php } ?>

                              <!-- ENVIO 3 -->
                              <?php if ($Lista_de_envio->titulo_mensaje_3 <> '') { ?>
                                <div v-bind:class="class_sino(estados[<?php echo $i ?>].sino_envio_3)">
                                  
                                    <a href="<?php echo $url_whatsapp_3; ?>" target="_blank">
                                      <button type="button" class="btn btn-blanco" alt="editar" title="editar" v-on:click="marcar_envio(1, 3, <?php echo $i; ?>, <?php echo $Contacto->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                    </a>
                                    <?php echo $Lista_de_envio->titulo_mensaje_3 ?>

                                    <label class="switch switch-inscripcion">
                                      <input  type="checkbox" v-on:change="setearSino(3, <?php echo $i ?>, <?php echo $Contacto->id ?>)" v-model="estados[<?php echo $i ?>].sino_envio_3">
                                      <span class="slider round"></span>
                                      
                                    </label>
                                </div>
                              <?php } ?>

                              <!-- ENVIO 4 -->
                              <?php if ($Lista_de_envio->titulo_mensaje_4 <> '') { ?>
                                <div v-bind:class="class_sino(estados[<?php echo $i ?>].sino_envio_4)">
                                  
                                    <a href="<?php echo $url_whatsapp_4; ?>" target="_blank">
                                      <button type="button" class="btn btn-blanco" alt="editar" title="editar" v-on:click="marcar_envio(1, 4, <?php echo $i; ?>, <?php echo $Contacto->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                    </a>
                                    <?php echo $Lista_de_envio->titulo_mensaje_4 ?>

                                    <label class="switch switch-inscripcion">
                                      <input  type="checkbox" v-on:change="setearSino(4, <?php echo $i ?>, <?php echo $Contacto->id ?>)" v-model="estados[<?php echo $i ?>].sino_envio_4">
                                      <span class="slider round"></span>
                                      
                                    </label>
                                </div>
                              <?php } ?>

                              <!-- ENVIO 5 -->
                              <?php if ($Lista_de_envio->titulo_mensaje_5 <> '') { ?>
                                <div v-bind:class="class_sino(estados[<?php echo $i ?>].sino_envio_5)">
                                  
                                    <a href="<?php echo $url_whatsapp_5; ?>" target="_blank">
                                      <button type="button" class="btn btn-blanco" alt="editar" title="editar" v-on:click="marcar_envio(1, 5, <?php echo $i; ?>, <?php echo $Contacto->id ?>)"><i class="fa fa-whatsapp"></i></button>
                                    </a>
                                    <?php echo $Lista_de_envio->titulo_mensaje_5 ?>

                                    <label class="switch switch-inscripcion">
                                      <input  type="checkbox" v-on:change="setearSino(5, <?php echo $i ?>, <?php echo $Contacto->id ?>)" v-model="estados[<?php echo $i ?>].sino_envio_5">
                                      <span class="slider round"></span>
                                      
                                    </label>
                                </div>
                              <?php } ?>



                              <!-- DESHABILITAR -->                              
                              <div v-bind:class="class_sino_deshabilitar(estados[<?php echo $i ?>].deshabilitar)" style="margin-top: 30px; padding-top: 0px; padding-bottom: 0px;">
                                <h4 style="color: white"><i class="icon fa fa-ban"></i> <?php echo __('Canceló la inscripción') ?>
                                  <label class="switch switch-inscripcion">
                                    <input  type="checkbox" v-on:change="setearSino(11, <?php echo $i ?>, <?php echo $Contacto->id ?>)" v-model="estados[<?php echo $i ?>].deshabilitar">
                                    <span class="slider round"></span>
                                    
                                  </label></h4>
                              </div>  
                                   
                            </td>
                            <td v-show="mensaje_extra != ''">

                              <!-- ENVIO DE MENSAJE EXTRA x WHATSAPP -->             
                                  <a v-bind:href="url_mensa_extra('<?php echo $ListasController->celular_wa($Contacto->telefono, $Contacto->codigo_tel) ?>', '<?php echo $Contacto->nombre ?>', '<?php echo $Contacto->apellido ?>')" target="_blank">
                                    <button type="button" class="btn btn-success" alt="editar" title="editar" v-on:click="marcar_envio(1, 1, <?php echo $i; ?>, <?php echo $Contacto->id ?>)"><i class="fa fa-whatsapp"></i> <?php echo __('Enviar via Whatsapp') ?></button>
                                  </a>
                                  <a v-show="enviar_mail" data-toggle="modal" data-target="#modal-confirmar-mail" v-on:click="preparar_envio_mail(1, '<?php echo $Contacto->nombre; ?>', '<?php echo $Contacto->apellido; ?>', <?php echo $Contacto->id ?>, '<?php echo __('Mensaje Extra') ?>', <?php echo $i ?>)">
                                    <button type="button" class="btn btn-blanco" alt="editar" title="editar"><i class="fa fa-envelope-o"></i></button>
                                  </a>
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
                        <p>Indique inscrito_nombre para que apareza el nombre de la persona y inscrito_apellido para su apellido, por ejemplo si el mensaje es: "Hola inscrito_nombre inscrito_apellido queremos recordarte asistir con ropa comoda para el taller de meditación" se traduciria como "Hola Jose Perez queremos recordarte asistir con ropa comoda para el taller de meditación"</p>
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

                      <div class="modal-body" id="modal-bodi-confirmar-mail">                        
                        <p><?php echo __('Nombre') ?>: @{{ email_nombre }}</p>
                        <p><?php echo __('Apellido') ?>: @{{ email_apellido }}</p>
                        <p><?php echo __('Acción') ?>: @{{ email_asunto }}</p>
                      </div>

                      <div class="modal-footer">
                        <center>
                          <button type="button" class="btn btn-default" v-on:click="procesar_envio_mail()"><?php echo __('Enviar') ?></button>
                          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancelar') ?></button>
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
              'order': [[ 0, 'des' ]],
              'columnDefs': [{ "width": "100px", "targets": 0 }], 
          })
        })
      </script>


    <!-- jQuery 3 -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Morris.js charts -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/raphael/raphael.min.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/morris.js/morris.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="<?php echo env('PATH_PUBLIC')?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/moment/min/moment.min.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- datepicker -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="<?php echo env('PATH_PUBLIC')?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- Slimscroll -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo env('PATH_PUBLIC')?>dist/js/adminlte.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?php echo env('PATH_PUBLIC')?>dist/js/pages/dashboard.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo env('PATH_PUBLIC')?>dist/js/demo.js"></script>
    <!-- DataTables -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

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
                telefono: null,
                email: null,
                mensaje_error: '',
                desabilitar: '',
                sino: null,
                show_col_id: false,
                show_col_prioridad: false,
                show_col_comprimido: true,
                show_col_apellido: false,
                show_col_nombre: false,
                show_col_telefono: false,
                show_col_email: false,
                show_col_estado: false,
                mensaje_extra: '',
                email_nombre: null,
                email_apellido: null,
                email_asunto: null,
                email_codigo: null,
                email_instancia_de_envio_id: null,
                email_i: null,
                enviar_mail: <?php echo $enviar_mail ?>,
                tipo_de_lista_de_envio_id: <?php echo $Lista_de_envio->tipo_de_lista_de_envio_id ?>,
                estados: [
                <?php 
                foreach ($Contactos as $Contacto) { 
                ?>
                      {
                        instancia_de_envio_id: <?php echo $Contacto->id ?>,
                        sino_envio_1: <?php echo sino_a_tf($Contacto->sino_envio_1) ?>,
                        sino_envio_2: <?php echo sino_a_tf($Contacto->sino_envio_2) ?>,
                        sino_envio_3: <?php echo sino_a_tf($Contacto->sino_envio_3) ?>,
                        sino_envio_4: <?php echo sino_a_tf($Contacto->sino_envio_4) ?>,
                        sino_envio_5: <?php echo sino_a_tf($Contacto->sino_envio_5) ?>,
                        prioridad: 1,
                        deshabilitar: <?php echo sino_a_tf($Contacto->sino_deshabilitar) ?>
                      },
                <?php } ?>
                ]
              },

              methods: {                

                setearSino: function (codigo, i, instancia_de_envio_id) {
                  if (codigo == 1) {
                      estado = this.estados[i].sino_envio_1;  
                  }
                  if (codigo == 2) {
                      estado = this.estados[i].sino_envio_2;  
                  }
                  if (codigo == 3) {
                      estado = this.estados[i].sino_envio_3;  
                  }
                  if (codigo == 4) {
                      estado = this.estados[i].sino_envio_4;  
                  }
                  if (codigo == 5) {
                      estado = this.estados[i].sino_envio_5;  
                  }
                  if (codigo == 11) {
                      estado = this.estados[i].deshabilitar;  
                  }

                  if (estado) {
                    sino = 'SI';
                  }
                  else {
                    sino = 'NO';
                  }
                  

                  $.ajax({
                    url: '<?php echo env('PATH_PUBLIC')?>le/setear-sino/'+codigo+'/'+instancia_de_envio_id+'/'+this.tipo_de_lista_de_envio_id,
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      sino: sino
                    },
                    success: function success(data, status) {    
                      //console.log('setearSino: '+data)                      
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                  });


                },
                

                

                preparar_envio_mail: function (codigo, nombre, apellido, instancia_de_envio_id, asunto, i) {
                  this.email_nombre = nombre
                  this.email_apellido = apellido
                  this.email_codigo = codigo
                  this.email_instancia_de_envio_id = instancia_de_envio_id
                  this.email_i = i
                  this.email_asunto = asunto
                },

                procesar_envio_mail: function () {

                  this.marcar_envio(2, this.email_codigo, this.email_i, this.email_instancia_de_envio_id)
                  
                  $.ajax({
                    url: '<?php echo env('PATH_PUBLIC')?>f/inscripcion/enviar-email/'+this.email_instancia_de_envio_id+'/'+this.email_codigo+'/'+this.email_asunto,
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      sino: 'SI'
                    },
                    success: function success(data, status) {   
                      var html_previo = $('#modal-bodi-confirmar-mail').html()
                      var new_html = ''+data+''
                      $('#modal-bodi-confirmar-mail').html(html_previo+new_html)
                      
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                  });

                },


                marcar_envio: function (medio_de_envio_id, codigo, i, instancia_de_envio_id) {
                  if (codigo == 1) {
                      this.estados[i].sino_envio_1 = true;  
                  }
                  if (codigo == 2) {
                      this.estados[i].sino_envio_2 = true;  
                  }
                  if (codigo == 3) {
                      this.estados[i].sino_envio_3 = true;  
                  }
                  if (codigo == 4) {
                      this.estados[i].sino_envio_4 = true;  
                  }
                  if (codigo == 5) {
                      this.estados[i].sino_envio_5 = true;  
                  }

                  this.setearSino(codigo, i, this.estados[i].instancia_de_envio_id)

                  $.ajax({
                    url: '<?php echo env('PATH_PUBLIC')?>le/registrar-envio/'+codigo+'/'+instancia_de_envio_id+'/'+medio_de_envio_id,
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

                class_sino_deshabilitar: function (sino) {
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
                  estado = '';
                  if (this.estados[i].sino_envio_1) {
                    estado = '<?php echo __('Envio 1') ?>'

                    if (this.estados[i].sino_envio_2) {
                      estado = '<?php echo __('Envio 2') ?>'

                      if (this.estados[i].sino_envio_3) {
                        estado = '<?php echo __('Envio 3') ?>'

                      }

                    }

                  }

                  if (this.estados[i].deshabilitar) {
                    estado = '<?php echo __('Canceló') ?>'
                  }           

                  return estado
                },
                
                contar_cant_inscriptos: function (situacion) {
                  // cuento la cantidad para el total de arriba
                  cant = 0

                    // total inscriptos
                    if (situacion == 'inscriptos') {
                      cant = this.estados.length;  
                    }

                    // total envio_1
                    if (situacion == 'envio_1') {
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].sino_envio_1 && !this.estados[i].deshabilitar) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total envio_2
                    if (situacion == 'envio_2') {
                      for (i = 0; i < this.estados.length; i++) { 
                        if (this.estados[i].sino_envio_2 && !this.estados[i].deshabilitar) {
                          cant = cant + 1
                        }
                      }
                    }

                    // total confirmados                    
                    if (situacion == 'envio_3') {
                      for (i = 0; i < this.estados.length; i++) { 
                        //console.log('confirmados: '+this.estados[i].sino_envio_3)
                        if (this.estados[i].sino_envio_3 && !this.estados[i].deshabilitar) {
                          cant = cant + 1
                        }
                      }
                    }

                    
                  return cant
                },

                url_mensa_extra: function (telefono, nombre, apellido) {
                  mensaje = this.mensaje_extra
                  mensaje = mensaje.replace('inscrito_nombre', nombre)
                  mensaje = mensaje.replace('inscrito_apellido', apellido)
                  url_mensa_extra = 'https://api.whatsapp.com/send?phone='+telefono+'&text='+mensaje;
                  return url_mensa_extra
                },



                calc_prioridad: function (i) {
                  prioridad = 1
                 return prioridad
                },

                  
              },

              computed: {
                cant_inscriptos: function () {
                 cant = this.contar_cant_inscriptos('inscriptos')
                 return cant
                },
                
                cant_envio_1: function () {
                 cant = this.contar_cant_inscriptos('envio_1')
                 return cant
                },
                
                cant_envio_2: function () {
                 cant = this.contar_cant_inscriptos('envio_2')
                 return cant
                },
                
                cant_envio_3: function () {
                 cant = this.contar_cant_inscriptos('envio_3')
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




    <!-- FUNCIONES ABM Y MODIFICAR INSCRIPCION -->
      <?php 
      $gen_seteo = array(
        'gen_url_siguiente' => 'back', 
        'no_mostrar_campos_abm' => 'solicitud_id|sino_notificar_proximos_eventos|sino_acepto_politica_de_privacidad|sino_sino_envio_1|sino_sino_envio_3|sino_sino_envio_2|sino_envio_voucher|sino_envio_motivacion|sino_envio_recordatorio|sino_asistio|fecha_de_evento_id|sino_envio_recordatorio_proxima_clase|sino_envio_recordatorio_proxima_clase_a_no_asistente|sino_deshabilitar|sino_contesto_consulta|sino_envio_pedido_de_confirmacion|sino_confirmo|sino_envio_recordatorio_pedido_de_confirmacion|sino_cancelo'
          );
      ?>   
           
      <script type="text/javascript">

        function crearABM_inscripcion(gen_modelo, gen_accion, gen_id = null) {

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
                alert(errorThrown);
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
