<?php
$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;
?>


@extends('layouts.backend')



@section('contenido')


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
        <?php echo __('Desempeño') ?> 
      </h1>
      <ol class="breadcrumb">
      </ol>
      <ol class="breadcrumb">
        <!-- Date and time range -->
        <div class="form-group">

          <div class="input-group">
            <button type="button" class="btn btn-default pull-right" id="daterange-btn">
              <span>
                <i class="fa fa-calendar"></i> <?php echo __('Últimos 30 días') ?>
              </span>
              <i class="fa fa-caret-down"></i>
            </button>
          </div>
          <input type="hidden" name="periodo" id="periodo" class="form-control">
          <input type="hidden" name="periodo_mostrar" id="periodo_mostrar" class="form-control">
        </div>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div id="contenidodash"></div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->


<!-- date-range-picker -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- bootstrap datepicker -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script>
  $(function () {

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
        $('#periodo_mostrar').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
        traerDashboard();
      }
    )

    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })

  })
</script>


<script type="text/javascript">

  function traerDashboard() {

    $("#contenidodash").html('');

    $.ajax({
      url: '<?php echo env('PATH_PUBLIC') ?>traer-ranking-m',
      type: 'POST',
      dataType: 'html',
      async: true,
      data:{
        _token: "{{ csrf_token() }}",
        periodo: $("#periodo").val(),
        periodo_mostrar: $("#periodo_mostrar").val()
      },
      success: function success(data, status) {        
        $("#contenidodash").html(data);

      },
      error: function error(xhr, textStatus, errorThrown) {
          alert(errorThrown);
      }
    });
  }

<?php if (isset($home)) { ?>
  $("#periodo").val([moment().subtract(29, 'days').format('YYYY-MM-DD')+'|'+moment().format('YYYY-MM-DD')])
  $("#periodo_mostrar").val([moment().subtract(29, 'days').format('DD/MM/YYYY')+' - '+moment().format('DD/MM/YYYY')])
  traerDashboard();
<?php } ?>
</script>

<div class="col-md-3">
          <div class="box box-warning collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Collapsable</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="display: none;">
              The body of the box
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
    <br>
@endsection
