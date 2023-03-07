<?php
use \App\Http\Controllers\SolicitudController; 
$SolicitudController = new SolicitudController;
$paisesDelEquipo = $SolicitudController->paisesDelEquipo();

$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;
$equipo_id = Auth::user()->equipo_id;


$tituloApp = 'Tecnotronica';
if ($_SERVER['HTTP_HOST'] == 'ac.igca.com.ar') {
    $tituloApp = 'IGCA';
}

?>


@extends('layouts.backend')



@section('contenido')

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Inicio
        <small><?php echo $tituloApp ?></small>
      </h1>
      <ol class="breadcrumb">
        <li class="active">Inicio</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <?php if ($rol_de_usuario_id <> '') { ?>
          <div class="col-xs-12">
            <h3><?php echo $mensaje_welcome ?></h3>
            <br>

            <?php 
            $i = 0;
            if ($Solicitudes_Alarmas <> null) {
              foreach ($Solicitudes_Alarmas as $Alarmas) { 
                $i++;

                if ($Alarmas['alarmas'] <> null) {
                  $cant = count($Alarmas['alarmas']);
                }
                else {
                  $cant = 0;
                }
                ?>
                <?php if ($cant >0 ) { ?>
                  <!-- SOLICITUDES ALARMAS -->
                    <div class="col-xs-12">
                      <div class="box">
                        <div class="box-header">
                          <h3 class="box-title">
                            <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="3 New Messages"><?php echo $cant ?></span>
                            ALARMAS: <?php echo $Alarmas['titulo'] ?></h3>

                            <div class="box-tools pull-right">
                              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                              </button>
                            </div>

                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                          <table id="table_sol_alarmas_<?php echo $i ?>" class="table table-bordered table-striped" style="max-width: 500px" >
                            <thead>
                            <tr>
                              <th><?php echo __('Acción') ?></th>
                              <th><?php echo __('Tipo') ?></th>
                              <th><?php echo __('Solicitud') ?></th>
                              <th><?php echo __('Código') ?></th>
                              <th><?php echo __('Solicitante') ?></th>
                              <th><?php echo __('Formulario') ?></th>
                              <th><?php echo __('Lista Inscriptos') ?></th>
                              <th><?php echo __('Estado') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($Alarmas['alarmas'] as $solicitud) { ?>
                            <tr>
                                <td>
                                  <div class="btn-group">
                                    <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/ver/<?php echo $solicitud['id']; ?>">
                                    <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                                  </a>
                                  </div>
                                </td>
                                <td><?php echo __($solicitud->Tipo_de_evento->tipo_de_evento); ?></td>
                                <td><?php echo $solicitud->id; ?></td>
                                <td><?php echo $solicitud->localidad_nombre(); ?></td>
                                <td><?php echo $solicitud->nombre_del_solicitante; ?></td>
                                <td><a href="<?php echo env('PATH_PUBLIC')?>f/<?php echo $solicitud->id ?>/<?php echo $solicitud->hash ?>" target="_blank">
                                  <button type="button" class="btn btn-primary"><i class="fa fa-file-text-o"></i></button>
                                </a></td>
                                <td><a href="<?php echo env('PATH_PUBLIC')?>f/i/<?php echo $solicitud->id ?>/<?php echo $solicitud->hash ?>" target="_blank"><button type="button" class="btn btn-primary"><i class="fa fa-list"></i></button></td>
                                <?php

                                $array_estado = $solicitud->estado();
                                $estado = $array_estado['estado'];
                                $class_estado = $array_estado['class_estado'];
                                $span_estado = $array_estado['span_estado'];
                                ?>                
                                <td><?php echo $span_estado; ?></td>
                            </tr>
                            <?php } ?>
                          </tbody>
                          </table>
                        </div>
                        <!-- /.box-body -->
                      </div>
                      <!-- /.box -->
                    </div>
                    <br>

                    <!-- DataTables -->

                    <script>
                      $(function () {
                        $('#table_sol_alarmas_<?php echo $i ?>').DataTable({
                          'language': {
                            'autoWidth': true,
                                'lengthMenu': 'Mostrar _MENU_ Registros por pagina',
                                'search': 'Buscar',
                                'zeroRecords': 'No hay resultados para la busqueda',
                                'info': 'Mostrando Pagina _PAGE_ de _PAGES_',
                                'infoEmpty': 'No hay registros',
                                'paginate': {
                                    'first':      'Primero',
                                    'last':       'Ultimo',
                                    'next':       'Siguiente',
                                    'previous':   'Anterior'
                                },
                                'infoFiltered': '(filtrado en _MAX_ registros totales)'
                            },
                            'order': [[ 1, 'asc' ]],
                            'columnDefs': [{ "width": "100px", "targets": 0 }], 
                        })
                      })
                    </script>
                  <!-- SOLICITUDES ALARMAS  -->
                <?php } ?>
              <?php } ?>
            <?php } ?>


            <!-- SOLICITUDES  -->
              <?php
              if ($Solicitudes <> null) {
                $cant = count($Solicitudes);
              }
              else {
                $cant = 0;
              }
              if ($cant > 0) {
              ?>
                <div class="col-xs-12">
                  <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">
                        <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="3 New Messages"><?php echo $cant ?></span>
                        <?php echo $titulo; ?></h3>

                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </button>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <table id="table" class="table table-bordered table-striped" style="max-width: 500px" >
                        <thead>
                        <tr>
                          <th><?php echo __('Acción') ?></th>
                          <th><?php echo __('Tipo') ?></th>
                          <th><?php echo __('Solicitud') ?></th>
                          <th><?php echo __('Código') ?></th>
                          <th><?php echo __('Solicitante') ?></th>
                          <th><?php echo __('Formulario') ?></th>
                          <th><?php echo __('Lista Inscriptos') ?></th>
                          <th><?php echo __('Estado') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($Solicitudes <> null) {
                          $cant = count($Solicitudes);
                        }
                        else {
                          $cant = 0;
                        }
                        ?>
                        <?php if ($cant >0 ) { ?>
                        <?php foreach ($Solicitudes as $solicitud) { ?>
                        <tr>
                            <td>
                              <div class="btn-group">
                                <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/ver/<?php echo $solicitud['id']; ?>">
                                <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                              </a>
                              </div>
                            </td>
                            <td><?php echo __($solicitud->Tipo_de_evento->tipo_de_evento); ?></td>
                            <td><?php echo $solicitud->id; ?></td>
                            <td><?php echo $solicitud->localidad_nombre(); ?></td>
                            <td><?php echo $solicitud->nombre_del_solicitante; ?></td>
                            <td><a href="<?php echo env('PATH_PUBLIC')?>f/<?php echo $solicitud->id ?>/<?php echo $solicitud->hash ?>" target="_blank">
                              <button type="button" class="btn btn-primary"><i class="fa fa-file-text-o"></i></button>
                            </a></td>
                            <td><a href="<?php echo env('PATH_PUBLIC')?>f/i/<?php echo $solicitud->id ?>/<?php echo $solicitud->hash ?>" target="_blank"><button type="button" class="btn btn-primary"><i class="fa fa-list"></i></button></td>
                            <?php

                            $array_estado = $solicitud->estado();
                            $estado = $array_estado['estado'];
                            $class_estado = $array_estado['class_estado'];
                            $span_estado = $array_estado['span_estado'];
                            ?>                
                            <td><?php echo $span_estado; ?></td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                      </tbody>
                      </table>
                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                </div>

                <div class="col-xs-3">
                  <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear"><button type="button" class="btn btn-block btn-info col-xs-3"><i class="fa fa-plus"></i> <?php echo __('Crear Solicitud') ?></button></a>
                </div>

              <?php } ?>

              <!-- DataTables -->

              <script>
                $(function () {
                  $('#table').DataTable({
                    'language': {
                      'autoWidth': true,
                          'lengthMenu': 'Mostrar _MENU_ Registros por pagina',
                          'search': 'Buscar',
                          'zeroRecords': 'No hay resultados para la busqueda',
                          'info': 'Mostrando Pagina _PAGE_ de _PAGES_',
                          'infoEmpty': 'No hay registros',
                          'paginate': {
                              'first':      'Primero',
                              'last':       'Ultimo',
                              'next':       'Siguiente',
                              'previous':   'Anterior'
                          },
                          'infoFiltered': '(filtrado en _MAX_ registros totales)'
                      },
                      'order': [[ 1, 'asc' ]],
                      'columnDefs': [{ "width": "100px", "targets": 0 }], 
                  })
                })
              </script>
            <!-- SOLICITUDES  -->

            <?php 
            if ($cant_autorizaciones > 0) {
            ?>
              <!-- AUTORIZACIONES -->
                <div class="col-xs-12">
                  <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">
                        <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="3 New Messages"><?php echo $cant_autorizaciones ?></span>
                        AUTORIZACIONES A NUEVOS USUARIOS</h3>

                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </button>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <table id="table_autorizaciones" class="table table-bordered table-striped" style="max-width: 500px" >
                        <thead>
                        <tr>
                          <th><?php echo __('Acción') ?></th>
                          <th><?php echo __('ID') ?></th>
                          <th><?php echo __('nombre') ?></th>
                          <th><?php echo __('email') ?></th>
                          <th><?php echo __('Equipo') ?></th>
                          <th><?php echo __('Pais') ?></th>
                          <th><?php echo __('Ciudad') ?></th>
                          <th><?php echo __('Lumisial') ?></th>
                          <th><?php echo __('Celular') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($Autorizaciones as $Autorizacion) { ?>
                        <tr>
                            <td>
                              <div class="btn-group">
                                <button type="button" class="btn btn-info" alt="editar" title="editar" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_user('<?php echo $Autorizacion->name; ?>', 'm', <?php echo $Autorizacion->id ?>)"><i class="fa fa-pencil"></i></button>
                              </div>
                            </td>
                            <td><?php echo $Autorizacion->id; ?></td>
                            <td><?php echo $Autorizacion->name; ?></td>
                            <td><?php echo $Autorizacion->email; ?></td>
                            <td><?php echo $Autorizacion->equipo_desc; ?></td>
                            <td><?php echo $Autorizacion->pais_desc; ?></td>
                            <td><?php echo $Autorizacion->ciudad; ?></td>
                            <td><?php echo $Autorizacion->lumisial; ?></td>
                            <td><?php echo $Autorizacion->celular; ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                      </table>
                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                </div>
                <br>

                <!-- DataTables -->

                <script>
                  $(function () {
                    $('#table_autorizaciones').DataTable({
                      'language': {
                        'autoWidth': true,
                            'lengthMenu': 'Mostrar _MENU_ Registros por pagina',
                            'search': 'Buscar',
                            'zeroRecords': 'No hay resultados para la busqueda',
                            'info': 'Mostrando Pagina _PAGE_ de _PAGES_',
                            'infoEmpty': 'No hay registros',
                            'paginate': {
                                'first':      'Primero',
                                'last':       'Ultimo',
                                'next':       'Siguiente',
                                'previous':   'Anterior'
                            },
                            'infoFiltered': '(filtrado en _MAX_ registros totales)'
                        },
                        'order': [[ 1, 'asc' ]],
                        'columnDefs': [{ "width": "100px", "targets": 0 }], 
                    })
                  })
                </script>
              <!-- AUTORIZACIONES  -->
            <?php
            } 
            ?>

            <!-- FUNCIONES ABM Y MODIFICAR SOLICITUD -->
              <?php 

                $gen_seteo = array(      
                  'no_mostrar_campos_abm' => 'password|remember_token|img_avatar|email|pais_id|idioma_id|telegram_chat_id|celular|sino_activo|lumisial|ciudad|diocesis|equipo_id',
                  'gen_url_siguiente' => 'back',
                );
              ?>   
                   
              <script type="text/javascript">

                function crearABM_user(nombre, gen_accion, gen_id = null) {
                  gen_seteo = '<?php echo serialize($gen_seteo) ?>'
                  $.ajax({
                    url: '<?php echo env('PATH_PUBLIC') ?>crearabm',
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      gen_modelo: 'User',
                      gen_seteo: gen_seteo,
                      gen_opcion: '',
                      gen_accion: gen_accion,
                      gen_id: gen_id
                    },
                    success: function success(data, status) {        
                      $("#modal-bodi-abm").html(data);
                      if (gen_accion == 'm') {
                        $("#modal-titulo").html('<?php echo __('Asignar Rol de Usuario y Equipo a') ?>: '+nombre);
                      }
                      if (gen_accion == 'b') {
                        $("#modal-titulo").html('<?php echo __('Borrar') ?> ');
                      }

                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                  });
                }


              </script>
            <!-- FUNCIONES ABM Y MODIFICAR SOLICITUD -->   

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

        <?php 
        } 
        else { 
          if ($equipo_id <= 5) {
        ?>

            <div class="alert alert-warning alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <h4><i class="icon fa fa-warning"></i> Alerta!</h4>
              <p><strong><?php echo __('Su usuario aún no ha sido autorizado. Debe solicitarlo a su supervisor.') ?></strong></p>
            </div>

        <?php }

        } ?>

        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->

<!-- DataTables -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>



@endsection
