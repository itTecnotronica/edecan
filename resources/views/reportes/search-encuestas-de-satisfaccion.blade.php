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
        <?php echo __('Encuesta de Satisfacción') ?>
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
          <div class="col-md-6">
            <div class="form-group">
              <label><?php echo __('Región') ?></label>
              <?php $provincias = App::make('App\Http\Controllers\HomeController')->get_provincias($pais_id);?>
              <?php echo Form::select("provincias", $provincias, null, ['id' => "provincias", 'class' => 'form-control select2', 'multiple' => 'multiple', 'style' => 'width: 100%;']); ?>
            </div>

            <div class="form-group">
              <label><?php echo __('Ciudad') ?></label>
              <?php $localidades = App::make('App\Http\Controllers\HomeController')->get_localidades($pais_id);?>
              <?php echo Form::select("localidades", $localidades, null, ['id' => "localidades", 'class' => 'form-control select2', 'multiple' => 'multiple', 'style' => 'width: 100%;']); ?>
            </div>

            <div class="form-group">
              <label><?php echo __('Tipo de Evento') ?></label>
              <?php $tipos_de_evento = App::make('App\Http\Controllers\HomeController')->get_tipos_de_evento();?>
              <?php echo Form::select("tipo_de_evento_id", $tipos_de_evento, null, ['id' => "tipo_de_evento_id", 'class' => 'form-control select2', 'style' => 'width: 100%;']); ?>
            </div>

          </div>
          <div class="col-md-6">

            <div class="form-group">
              <label><?php echo __('Título de la Conferencia Pública') ?></label>
              <input type="text" name="titulo_de_conferencia_publica" id="titulo_de_conferencia_publica" class="form-control">
            </div>

            <!-- Date and time range -->
            <div class="form-group">
              <label><?php echo __('Período') ?></label>

              <div class="input-group">
                <button type="button" class="btn btn-default pull-right" id="daterange-btn">
                  <span>
                    <i class="fa fa-calendar"></i> <?php echo __('Indique el Período') ?>
                  </span>
                  <i class="fa fa-caret-down"></i>
                </button>
              </div>
              <input type="hidden" name="periodo" id="periodo" class="form-control">
            </div>

            

          </div>

        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          <button type="button" class="btn btn-primary" onclick="traerListado()"><?php echo __('Eviar') ?></button>
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

<script>
  $(function () {//Initialize Select2 Elements
    $('.select2').select2()

    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          '<?php echo __('Hoy') ?>'       : [moment(), moment()],
          '<?php echo __('Ayer') ?>'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          '<?php echo __('Últimos 7 días') ?>' : [moment().subtract(6, 'days'), moment()],
          '<?php echo __('Últimos 30 días') ?>': [moment().subtract(29, 'days'), moment()],
          '<?php echo __('Este mes') ?>'  : [moment().startOf('month'), moment().endOf('month')],
          '<?php echo __('Mes Anterior') ?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#daterange-btn').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
        $('#periodo').val(start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD'))
      }
    )

    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })

  })
</script>


<script type="text/javascript">

  function traerListado() {
    
    $("#lista").html('');

    $.ajax({
      url: '<?php echo env('PATH_PUBLIC') ?>listar-encuestas',
      type: 'POST',
      dataType: 'html',
      async: true,
      data:{
        _token: "{{ csrf_token() }}",
        provincias: $("#provincias").val(),
        localidades: $("#localidades").val(),
        tipo_de_evento_id: $("#tipo_de_evento_id").val(),
        titulo_de_conferencia_publica: $("#titulo_de_conferencia_publica").val(),
        periodo: $("#periodo").val()
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
