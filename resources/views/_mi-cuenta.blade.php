<?php
$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;
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
        <?php echo __('Mi Cuenta') ?>
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><?php echo __('Inicio') ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">

        <div class="col-md-4 col-md-offset-4">
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-aqua-active">
              <h3 class="widget-user-username"><?php echo $User->name ?></h3>
              <h5 class="widget-user-desc"><?php echo $User->rol_de_usuario->rol_de_usuario ?></h5>
            </div>
            <div class="widget-user-image">
              <?php 
              if ($User->file_imagen_de_perfil == '') {
                $avatar = env('PATH_PUBLIC').'/img/avatar-sin-imagen.png';
              }
              else {
                $avatar = env('PATH_PUBLIC').'storage/'.$User->file_imagen_de_perfil;
              }
              ?>
              <img class="img-circle" src="<?php echo $avatar ?>" alt="User Avatar">
            </div>

            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="box-footer no-padding">
              <ul class="nav nav-stacked">
                <li><a href="#"><?php echo __('Correo') ?> <span class="pull-right badge bg-blue"><?php echo $User->email ?></span></a></li>
                <li><a href="#"><?php echo __('Rol de Usuario') ?> <span class="pull-right badge bg-aqua"><?php echo $User->rol_de_usuario->rol_de_usuario ?></span></a></li>
                <li><a href="#"><?php echo __('Telegram Chat ID') ?> <span class="pull-right badge bg-grey"><?php echo $User->telegram_chat_id ?></span></a></li>
                <li><a href="#"><?php echo __('Celular') ?> <span class="pull-right badge bg-yellow"><?php echo $User->celular ?></span></a></li>
              </ul>
            </div>
            <div class="box-footer no-padding">
              <p>
              <button type="button" style="margin: 10px" class="btn btn-primary btn-md pull-right" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_user(<?php echo $User->id ?>)"><?php echo __('Modificar') ?></button>
              </p><br>
            </div>
          </div>
          <!-- /.widget-user -->
        </div>


        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->

<!-- DataTables -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>


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


<!-- FUNCIONES MODIFICAR DATOS -->
  <?php 
  $gen_seteo = array(
      'gen_url_siguiente' => 'back', 
      'no_mostrar_campos_abm' => 'password|rol_de_usuario_id|remember_token'
    );
  ?>   

  <script type="text/javascript">

    function crearABM_user(gen_id = null) {

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC') ?>crearabm',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          gen_modelo: 'User',
          gen_seteo: '<?php echo serialize($gen_seteo) ?>',
          gen_opcion: '',
          gen_accion: 'm',
          gen_id: gen_id
        },
        success: function success(data, status) {        
          $("#modal-bodi-abm").html(data);
          if (gen_accion == 'm') {
            $("#modal-titulo").html('Modificar Datos');
          }

        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }

  </script>
<!-- FUNCIONES MODIFICAR DATOS -->     

@endsection
