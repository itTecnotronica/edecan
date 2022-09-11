<?php 
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;


$colores = ['#00BFFF', '#6DD900', '#FFBF00'];


?>
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

    <br>
    <!-- CANTIDAD DE CAMPAÑAS -->
    <?php 
    $tot_campanias = 0;
    foreach ($Solicitudes as $Solicitud) {
      $tot_campanias = $tot_campanias+$Solicitud->cant_campanias;
    }
    ?>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $gCont->formatoNumero($tot_campanias, 'entero') ?></h3>
          <p><?php echo __('Cantidad') ?> <?php echo __('Campañas') ?></p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <?php foreach ($Solicitudes as $Solicitud) { ?>
          <div class="small-box-footer">
              <?php echo __($Solicitud->tipo_de_evento) ?>: <strong><?php echo $gCont->formatoNumero($Solicitud->cant_campanias, 'entero') ?></strong>
          </div>
        <?php } ?>
      </div>
    </div>



    <!-- INVERSION -->
    <?php 
    $tot_importe = 0;
    foreach ($Solicitudes as $Solicitud) {
      $tot_importe = $tot_importe+$Solicitud->importe;
    }
    ?>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>$ <?php echo $gCont->formatoNumero($tot_importe, 'entero') ?></h3>
          <p><?php echo __('Importe') ?> </p>
        </div>
        <div class="icon">
          <i class="fa fa-usd"></i>
        </div>
        <?php foreach ($Solicitudes as $Solicitud) { ?>
          <div class="small-box-footer">
              <?php echo __($Solicitud->tipo_de_evento) ?>: <strong><?php echo $gCont->formatoNumero($Solicitud->importe, 'entero') ?></strong>
          </div>
        <?php } ?>
      </div>
    </div>



    <!-- ALCANCE -->
    <?php 
    $tot_alcance = 0;
    foreach ($Solicitudes as $Solicitud) {
      $tot_alcance = $tot_alcance+$Solicitud->alcance;
    }
    ?>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
          <h3><?php echo $gCont->formatoNumero($tot_alcance, 'entero') ?></h3>
          <p><?php echo __('Personas que vieron la palabra GNOSIS') ?></p>
        </div>
        <div class="icon">
          <i class="fa fa-google"></i>
        </div>
        <?php foreach ($Solicitudes as $Solicitud) { ?>
          <div class="small-box-footer">
              <?php echo __($Solicitud->tipo_de_evento) ?>: <strong><?php echo $gCont->formatoNumero($Solicitud->alcance, 'entero') ?></strong>
          </div>
        <?php } ?>
      </div>
    </div>


    <!-- IMPRESIONES -->
    <?php 
    $tot_impresiones = 0;
    foreach ($Solicitudes as $Solicitud) {
      $tot_impresiones = $tot_impresiones+$Solicitud->impresiones;
    }
    ?>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?php echo $gCont->formatoNumero($tot_impresiones, 'entero') ?></h3>
          <p><?php echo __('Volantes Digitales Entregados') ?></p>
        </div>
        <div class="icon">
          <i class="fa fa-file"></i>
        </div>
        <?php foreach ($Solicitudes as $Solicitud) { ?>
          <div class="small-box-footer">
              <?php echo __($Solicitud->tipo_de_evento) ?>: <strong><?php echo $gCont->formatoNumero($Solicitud->impresiones, 'entero') ?></strong>
          </div>
        <?php } ?>
      </div>
    </div>

    <!-- INSCRIPCIONES / VISUALIZACIONES -->
    <?php 
    $tot_inscripciones = 0;
    foreach ($Inscripciones as $Inscripcion) {
      $tot_inscripciones = $tot_inscripciones+$Inscripcion->cant_inscriptos;
    }
    $tot_visualizaciones = 0;
    foreach ($Visualizaciones as $Visualizacion) {
      $tot_visualizaciones = $tot_visualizaciones+$Visualizacion->cant_visualizaciones;
    }

    $porc_tot_inscripciones = round($tot_inscripciones * 100 / $tot_visualizaciones);
    ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box bg-aqua">
        <span class="info-box-icon"><i class="fa fa-pencil-square-o"></i></span>

        <div class="info-box-content">
          <span class="info-box-text"><?php echo __('Inscripciones') ?></span>
          <span class="info-box-number"><?php echo $gCont->formatoNumero($tot_inscripciones, 'entero') ?></span>

          <div class="progress">
            <div class="progress-bar" style="width: <?php echo $porc_tot_inscripciones ?>%"></div>
          </div>
              <span class="progress-description">
                <?php echo $porc_tot_inscripciones ?>% <?php echo __('Visualizaciones') ?> (<?php echo $gCont->formatoNumero($tot_visualizaciones, 'entero') ?>)
              </span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>



    <!-- CONTACTADOS -->
    <?php 
    $tot_contactados = 0;
    foreach ($Inscripciones as $Inscripcion) {
      $tot_contactados = $tot_contactados+$Inscripcion->cant_contactados;
    }

    $porc_tot_contactados = round($tot_contactados * 100 / $tot_inscripciones);
    ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box bg-aqua">
        <span class="info-box-icon"><i class="fa fa-whatsapp"></i></span>

        <div class="info-box-content">
          <span class="info-box-text"><?php echo __('Contactados') ?> </span>
          <span class="info-box-number"><?php echo $gCont->formatoNumero($tot_contactados, 'entero') ?></span>

          <div class="progress">
            <div class="progress-bar" style="width: <?php echo $porc_tot_contactados ?>%"></div>
          </div>
              <span class="progress-description">
                <?php echo $porc_tot_contactados ?>% <?php echo __('Inscripciones') ?> (<?php echo $gCont->formatoNumero($tot_inscripciones, 'entero') ?>)
              </span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>

    <div class="col-md-12">

      <section class="content">
        <div class="row">
          <div class="col-md-6">
            <!-- AREA CHART -->
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Area Chart</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="chart">
                  <canvas id="areaChart" style="height: 350px; width: 594px;" width="742" height="312"></canvas>
                </div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <!-- DONUT CHART -->
            <div class="box box-danger">
              <div class="box-header with-border">
                <h3 class="box-title">Donut Chart</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <canvas id="pieChart" style="height: 307px; width: 614px;" width="767" height="383"></canvas>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->

          </div>
          <!-- /.col (LEFT) -->
          <div class="col-md-6">
            <!-- LINE CHART -->
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Line Chart</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="chart">
                  <canvas id="lineChart" style="height: 350px; width: 594px;" width="742" height="312"></canvas>
                </div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <!-- BAR CHART -->
            <div class="box box-success">
              <div class="box-header with-border">
                <h3 class="box-title">Bar Chart</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="chart">
                  <canvas id="barChart" style="height: 229px; width: 594px;" width="742" height="286"></canvas>
                </div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->

          </div>
          <!-- /.col (RIGHT) -->
        </div>
        <!-- /.row -->

      </section>

    </div>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo __('Resultados') ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="overflow: auto">

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->



<!-- jQuery 3 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/chart.js/Chart.js"></script>
<!-- FastClick -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/demo.js"></script>
<!-- page script -->
<script>
  $(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    var areaChartCanvas = $('#areaChart').get(0).getContext('2d')
    // This will get the first returned node in the jQuery collection.
    var areaChart       = new Chart(areaChartCanvas)

    var areaChartData = {

      labels  : ['<?php echo __('Inscriptos') ?>', '<?php echo __('Eligio un horario') ?>', '<?php echo __('Contactados') ?>', '<?php echo __('Confirmados') ?>', '<?php echo __('Voucher') ?>', '<?php echo __('Motivacion') ?>', '<?php echo __('Recordatorio') ?>', '<?php echo __('Asistentes') ?>', '<?php echo __('Recordatorio Próx clase') ?>'],
      datasets: [
        <?php 
        $i = -1;
        foreach ($Inscripciones as $Inscripcion) {
          $i++;
        ?>
          {
            label               : '<?php echo $Inscripcion->tipo_de_evento ?>',
            fillColor           : '<?php echo $colores[$i] ?>',
            strokeColor         : '<?php echo $colores[$i] ?>',
            pointColor          : '<?php echo $colores[$i] ?>',
            pointStrokeColor    : '#000',
            pointHighlightFill  : '#fff',
            pointHighlightStroke: 'rgba(220,220,220,1)',
            data                : [
              <?php echo $Inscripcion->cant_inscriptos ?>, 
              <?php echo $Inscripcion->cant_inscriptos_eligio ?>, 
              <?php echo $Inscripcion->cant_contactados ?>, 
              <?php echo $Inscripcion->cant_confirmo ?>, 
              <?php echo $Inscripcion->cant_voucher ?>, 
              <?php echo $Inscripcion->cant_motivacion ?>, 
              <?php echo $Inscripcion->cant_recordatorio ?>, 
              <?php echo $Inscripcion->cant_asistio ?>, 
              <?php echo $Inscripcion->cant_recordatorio_prox ?>
              ],
          },

        <?php } ?>
      ]
    }

    var areaChartOptions = {
      //Boolean - If we should show the scale at all
      showScale               : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - Whether the line is curved between points
      bezierCurve             : false,
      //Number - Tension of the bezier curve between points
      bezierCurveTension      : 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot                : true,
      //Number - Radius of each point dot in pixels
      pointDotRadius          : 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth     : 3,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius : 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke           : true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth      : 2,
      //Boolean - Whether to fill the dataset with a color
      datasetFill             : true,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio     : true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive              : true,

  legend: {
    display: true,
    position: 'top',
    labels: {
      boxWidth: 80,
      fontColor: 'black'
    }
  },
  scales: {
    xAxes: [{
      gridLines: {
        display: true,
        color: "black"
      },
      scaleLabel: {
        display: true,
        labelString: "Time in Seconds",
        fontColor: "red"
      }
    }],
    yAxes: [{
      gridLines: {
        color: "black",
        borderDash: [2, 5],
      },
      scaleLabel: {
        display: true,
        labelString: "Speed in Miles per Hour",
        fontColor: "green"
      }
    }]
  }

    }

    //Create the line chart
    areaChart.Line(areaChartData, areaChartOptions)

    //-------------
    //- LINE CHART -
    //--------------
    var lineChartCanvas          = $('#lineChart').get(0).getContext('2d')
    var lineChart                = new Chart(lineChartCanvas)
    var lineChartOptions         = areaChartOptions
    lineChartOptions.datasetFill = false
    lineChart.Line(areaChartData, lineChartOptions)

    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieChart       = new Chart(pieChartCanvas)
    var PieData        = [
      {
        value    : 700,
        color    : '#f56954',
        highlight: '#f56954',
        label    : 'Chrome'
      },
      {
        value    : 500,
        color    : '#00a65a',
        highlight: '#00a65a',
        label    : 'IE'
      },
      {
        value    : 400,
        color    : '#f39c12',
        highlight: '#f39c12',
        label    : 'FireFox'
      },
      {
        value    : 600,
        color    : '#00c0ef',
        highlight: '#00c0ef',
        label    : 'Safari'
      },
      {
        value    : 300,
        color    : '#3c8dbc',
        highlight: '#3c8dbc',
        label    : 'Opera'
      },
      {
        value    : 100,
        color    : '#d2d6de',
        highlight: '#d2d6de',
        label    : 'Navigator'
      }
    ]
    var pieOptions     = {
      //Boolean - Whether we should show a stroke on each segment
      segmentShowStroke    : true,
      //String - The colour of each segment stroke
      segmentStrokeColor   : '#fff',
      //Number - The width of each segment stroke
      segmentStrokeWidth   : 2,
      //Number - The percentage of the chart that we cut out of the middle
      percentageInnerCutout: 50, // This is 0 for Pie charts
      //Number - Amount of animation steps
      animationSteps       : 100,
      //String - Animation easing effect
      animationEasing      : 'easeOutBounce',
      //Boolean - Whether we animate the rotation of the Doughnut
      animateRotate        : true,
      //Boolean - Whether we animate scaling the Doughnut from the centre
      animateScale         : false,
      //Boolean - whether to make the chart responsive to window resizing
      responsive           : true,
      // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio  : true,
      //String - A legend template
      legendTemplate       : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    pieChart.Doughnut(PieData, pieOptions)

    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
    var barChart                         = new Chart(barChartCanvas)
    var barChartData                     = areaChartData
    barChartData.datasets[1].fillColor   = '#00a65a'
    barChartData.datasets[1].strokeColor = '#00a65a'
    barChartData.datasets[1].pointColor  = '#00a65a'
    var barChartOptions                  = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero        : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - If there is a stroke on each bar
      barShowStroke           : true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth          : 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing         : 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing       : 1,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to make the chart responsive
      responsive              : true,
      maintainAspectRatio     : true
    }

    barChartOptions.datasetFill = false
    barChart.Bar(barChartData, barChartOptions)
  })
</script>