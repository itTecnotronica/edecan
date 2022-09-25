<?php
$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;

$pais_id = Auth::user()->pais_id;

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
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/select2/dist/css/select2.min.css">
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
        <?php echo __('Buscar') ?> <?php echo __('Inscripcion') ?>
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li class="active">Inicio</li>
      </ol>
    </section>

    <section class="content">
      <div class="box box-primary">
        <div class="box-header">

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>

        </div>
        <div class="box-body">

          <div class="col-lg-3 col-xs-12">
            <div class="form-group">
              <label><?php echo __('ID') ?> <?php echo __('Inscripcion') ?></label>
              <input type="text" name="inscripcion_id" id="inscripcion_id" class="form-control">
            </div>

            <div class="form-group">
              <label><?php echo __('Codigo del alumno') ?></label>
              <input type="text" name="codigo_alumno" id="codigo_alumno" class="form-control">
            </div>

            <div class="form-group">
              <label><?php echo __('Solicitud') ?> ID</label>
              <input type="text" name="solicitud_id" id="solicitud_id" class="form-control">
            </div>
          </div>

          <div class="col-lg-3 col-xs-12">
            <div class="form-group">
              <label><?php echo __('Nombre') ?></label>
              <input type="text" name="nombre" id="nombre" class="form-control">
            </div>

            <div class="form-group">
              <label><?php echo __('Apellido') ?></label>
              <input type="text" name="apellido" id="apellido" class="form-control">
            </div>

            <div class="form-group">
              <label><?php echo __('Celular') ?></label>
              <input type="text" name="celular" id="celular" class="form-control">
            </div>
          </div>

          <div class="col-lg-3 col-xs-12">

            <div class="form-group">
              <label><?php echo __('Pais') ?> <?php echo __('Solicitud') ?></label>
              <?php $paises = App::make('App\Http\Controllers\HomeController')->get_paises();?>
              <?php echo Form::select("pais_id_solicitud", $paises, null, ['id' => "pais_id_solicitud", 'class' => 'form-control select2', 'style' => 'width: 100%;']); ?>
            </div>

            <div class="form-group">
              <label><?php echo __('Ciudad') ?> <?php echo __('Solicitud') ?></label>
              <?php $localidades = App::make('App\Http\Controllers\HomeController')->get_localidadesConProvincia(); ?>
              <?php array_push($localidades, [null => ''] ) ?>
              <?php echo Form::select("localidad_id", $localidades, null, ['id' => "localidad_id", 'class' => 'form-control select2', 'style' => 'width: 100%;']); ?>
            </div>

            <div class="form-group">
              <label><?php echo __('Correo') ?></label>
              <input type="text" name="email_correo" id="email_correo" class="form-control">
            </div>

          </div>   

          <div class="col-lg-3 col-xs-12">

            <div class="form-group">
              <label><?php echo __('Pais') ?> <?php echo __('Inscripto') ?></label>
              <?php $paises = App::make('App\Http\Controllers\HomeController')->get_paises();?>
              <?php echo Form::select("pais_id_inscripcion", $paises, null, ['id' => "pais_id_inscripcion", 'class' => 'form-control select2', 'style' => 'width: 100%;']); ?>
            </div>

            <div class="form-group">
              <label><?php echo __('Ciudad') ?> <?php echo __('Inscripto') ?></label>
              <input type="text" name="ciudad" id="ciudad" class="form-control">
            </div>

            <div class="form-group">
              <label><?php echo __('Idioma') ?> </label>
              <?php $idiomas = App::make('App\Http\Controllers\HomeController')->get_idiomas();?>
              <?php echo Form::select("idioma_id", $idiomas, null, ['id' => "idioma_id", 'class' => 'form-control select2', 'style' => 'width: 100%;']); ?>
            </div>
          </div>     

          <div class="col-lg-3 col-xs-12">

            <div class="form-group">
              <label><?php echo __('Id de Campaña (Contador)') ?> </label>
              <input type="text" name="campania_id" id="campania_id" class="form-control">
            </div>
          </div>          


          <div class="col-lg-3 col-xs-12">
            <div class="form-group">
              <br>
              <button type="button" class="btn btn-primary" onclick="traerListado()"><?php echo __('Buscar') ?></button>
            </div>
          </div>


        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          
        </div>
      </div>
    </section>
    <div id="lista"></div>

<!-- DataTables -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>


<!-- date-range-picker -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- bootstrap datepicker -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Select2 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/select2/dist/js/select2.full.min.js"></script>



<script type="text/javascript">

  function traerListado() {
    
    $("#lista").html('');

    $.ajax({
      url: '<?php echo env('PATH_PUBLIC') ?>listar-inscripciones',
      type: 'POST',
      dataType: 'html',
      async: true,
      data:{
        _token: "{{ csrf_token() }}",
        inscripcion_id: $('#inscripcion_id').val(),
        codigo_alumno: $('#codigo_alumno').val(),
        solicitud_id: $('#solicitud_id').val(),
        nombre: $('#nombre').val(),
        apellido: $('#apellido').val(),
        celular: $('#celular').val(),
        email_correo: $('#email_correo').val(),
        pais_id_solicitud: $('#pais_id_solicitud').val(),
        ciudad: $('#ciudad').val(),
        pais_id_inscripcion: $('#pais_id_inscripcion').val(),
        localidad_id: $('#localidad_id').val(),
        idioma_id: $('#idioma_id').val(),
        campania_id: $('#campania_id').val()

      },
      success: function success(data, status) {        
        $("#lista").html(data);

      },
      error: function error(xhr, textStatus, errorThrown) {
          alert(errorThrown);
      }
    });
  }
</script>

@endsection
