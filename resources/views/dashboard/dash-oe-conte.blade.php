<?php 
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;


$colores = ['0, 191, 255', '109, 217, 0', '255, 0, 0', '255, 191, 0', '255, 255, 0'];

$total_inscriptos_online = 0;
foreach ($Online_meses as $Online_mes) { 
  $total_inscriptos_online = $total_inscriptos_online + $Online_mes->cant;
} 

$cant_inscriptos_tot = $Inscripciones2->sum('cant_inscriptos');

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
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


    <style type="text/css">
      .tit-bg-rk {
        background-color: #343434 !important;
      }

      .btn-lista-completa {
        background-color: #6c6c6c; 
        color: #FFF; 
        padding: 2px;
      }

      .txt-referencia {
        width: 60%;
        font-size: 16px !important;
        text-align: left;
      }

    </style>

    <section class="content-header">
      <h1>
        <?php echo $pais; ?>
      </h1>
    </section>

    <br>
    <?php if ($Solicitudes->count() > 0) { ?>

      <div class="col-lg-12 col-xs-12">
        <!-- CANTIDAD DE CAMPAÑAS -->
          <?php 
          $tot_campanias = 0;
          foreach ($Solicitudes as $Solicitud) {
            $tot_campanias = $tot_campanias+$Solicitud->cant_campanias;
          }
          ?>
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua" data-toggle="tooltip" data-original-title="<?php echo __('Campañas') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?>">
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
        <!-- CANTIDAD DE CAMPAÑAS -->

        <!-- INVERSION -->
          <?php 
          $tot_importe = 0;
          foreach ($Solicitudes as $Solicitud) {
            $tot_importe = $tot_importe+$Solicitud->importe;
          }
          ?>
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green" data-toggle="tooltip" data-original-title="<?php echo __('Monto invertido en las campañas') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?> (<?php echo __('expresado en la moneda local') ?>)">
              <div class="inner">
                <h3>$ <?php echo $gCont->formatoNumero($tot_importe, 'entero') ?></h3>
                <p><?php echo __('Inversion') ?> </p>
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
        <!-- INVERSION -->

        <!-- ALCANCE -->
          <?php 
          $tot_alcance = 0;
          foreach ($Solicitudes as $Solicitud) {
            $tot_alcance = $tot_alcance+$Solicitud->alcance;
          }
          ?>
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red" data-toggle="tooltip" data-original-title="<?php echo __('Cantidad de personas que fueron alcanzadas por la publicidad de las campañas') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?>">
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
        <!-- ALCANCE -->

        <!-- IMPRESIONES -->
          <?php 
          $tot_impresiones = 0;
          foreach ($Solicitudes as $Solicitud) {
            $tot_impresiones = $tot_impresiones+$Solicitud->impresiones;
          }
          ?>
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow" data-toggle="tooltip" data-original-title="<?php echo __('Cantidad de volantes digitales entregados') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?>">
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
        <!-- IMPRESIONES -->
      </div>

    <?php } 
    else { ?>

      <div class="col-lg-12 col-xs-12">
        <div class="alert alert-info alert-dismissible">
          <h4><i class="icon fa fa-info"></i> <?php echo __('Campañas') ?></h4>
          <?php echo __('No hemos encontrado campañas para') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?>
        </div> 
      </div>      
    <?php } ?>

    <?php if ($Inscripciones2->count() > 0) { ?>

      <div class="col-lg-12 col-xs-12">
        <!-- INSCRIPCIONES -->
          <?php 
          if ($Inscripciones->count() > 0) {
            $Inscripciones_top = $Inscripciones2->sortByDesc('cant_inscriptos');
            $Inscripciones_top = $Inscripciones_top->take(3);
            $Inscripciones_top = $Inscripciones_top->values()->all();
          ?>
            <div class="col-lg-3 col-xs-12">
              <!-- small box -->
              <div class="small-box bg-aqua" data-toggle="tooltip" data-trigger="hover" data-original-title="<?php echo __('Cantidad de personas que se inscribieron a los cursos o conferencias') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?>" style="margin-bottom: 0px;">
                <div class="inner">
                  <h3><?php echo $gCont->formatoNumero($cant_inscriptos_tot, 'entero') ?></h3>
                  <p>1° <?php echo $Inscripciones_top[0]->localidad ?></p>
                </div>
                <div class="icon">
                  <i class="fa fa-pencil-square-o"></i>
                </div>

                  <div class="small-box-footer tit-bg-rk">
                      <strong><?php echo __('Inscripciones') ?></strong>
                  </div>
                <?php 
                $i=0;
                foreach ($Inscripciones_top as $Inscripcion) { 
                $i++;
                ?>
                  <div class="small-box-footer">
                      <?php echo $i ?>° <?php echo $Inscripcion->localidad ?>: <strong><?php echo $gCont->formatoNumero($Inscripcion->cant_inscriptos, 'entero') ?></strong>
                  </div>
                <?php } ?>
                      <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-cant_inscriptos"><?php echo __('Lista Completa') ?></button>              
              </div>
            </div>

            <!-- MODAL INSCRIPCIONES -->
              <div class="modal fade" id="modal-cant_inscriptos">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title"><?php echo __('Inscripciones') ?> | <?php echo $periodo_mostrar ?> </h4>
                    </div>
                    <div class="modal-body">                            
                      <table class="table table-condensed table-hover">
                        <tbody>
                          <tr>
                            <th style="width: 10px"></th>
                            <th style="width: 10px">#</th>
                            <th><?php echo __('Pais') ?></th>
                            <th><?php echo __('Cantidad') ?></th>
                          </tr>
                          <?php 
                          $Inscripciones_top = $Inscripciones2->sortByDesc('cant_inscriptos');
                          $Inscripciones_top = $Inscripciones_top->values()->all();

                          $i=0;
                          foreach ($Inscripciones_top as $Inscripcion) { 
                          $i++;
                          ?>                      
                          <tr>
                            <td>
                              <a href="<?php echo $Inscripcion->enlace ?>" target="_blank">
                                <button type="button" class="btn btn-xs btn-default" alt="editar" title="editar"><i class="fa fa-search"></i></button>
                              </a>
                            </td>
                            <td><?php echo $i ?>°</td>
                            <td><?php echo $Inscripcion->localidad ?></td>
                            <td style="text-align: right;"><?php echo $gCont->formatoNumero($Inscripcion->cant_inscriptos, 'entero') ?></td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
            <!-- MODAL INSCRIPCIONES -->
          <?php } ?>
        <!-- INSCRIPCIONES -->

        <!-- CONTACTADOS -->
          <?php 
          $Inscripciones_top = $Inscripciones2->each(function ($item, $key) {
              if ($item->cant_inscriptos > 0) {
                $item->campo_para_usar = $item->cant_contactados*100/$item->cant_inscriptos;
              }
              else {
                $item->campo_para_usar = 0;  
              }
          });


          $titulo_1 = '';
          $titulo_2 = $gCont->formatoNumero($Inscripciones_top->sum('cant_contactados'), 'entero');
          if ($cant_inscriptos_tot > 0) {
            $valor_porc = $Inscripciones_top->sum('cant_contactados')*100/$cant_inscriptos_tot;
          }
          else {
            $valor_porc = 0;  
          }
          $valor_porc = $gCont->formatoNumero($valor_porc, 'decimal');
          $descripcion = $valor_porc.'% de '.$cant_inscriptos_tot.' Inscripciones';

          $widget = [
            'cant_top' => 3,
            'icono' => 'fa fa-whatsapp',
            'titulo' => __('Contactados'),
            'tooltip' => __('Personas inscriptas que fueron contactadas por los responsables inscripción de cada lugar').': '.$pais.' '.$periodo_mostrar.' ('.__('el porcentaje siempre debería estar por encima del 95%').')',
            'titulo_1' => $titulo_1,
            'titulo_2' => $titulo_2,
            'valor_porc' => $valor_porc,
            'descripcion' => $descripcion
            ];
          $modal = [
            'id' => 'modal-contactados',
            'titulo' => __('Contactados').' | '.$pais.' | '.$periodo_mostrar,
            'enlace' => 'enlace'
            ];
          $info = [
            'periodo_mostrar' => $periodo_mostrar,
            'recomendacion_buen_rendimiento' => __('el porcentaje siempre debería estar por encima del 95%'),
            'como_mejorar' => __('Recordar a los responsables de inscripción, la necesidad de contactar a todas las personas que se inscriben, no deben quedar personas en la lista de inscriptos sin contactar, una posible solución a esto es revisar las campañas que han tenido bajo rendimiento de contacto y sugerir una actualización en Capacitación al responsable de inscripción')
            ];
          $columnas = [
            'titulos' => [
              __('Localidad'), 
              __('Inscripciones'), 
              '% '.__('Contactados')
              ], 
            'campos' => [
              'localidad',
              'cant_inscriptos',
              'campo_para_usar'
              ],
            'columna_orden' => 'campo_para_usar'
            ];
          $ranking = $Inscripciones_top;
          $valores_color = [
            'warning' => [90, 95],
            'danger' => [0, 90]
            ];

          App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
          ?>
        <!-- CONTACTADOS -->


        <!-- CONFIRMADOS -->
          <?php 
          $Inscripciones_top = $Inscripciones2->each(function ($item, $key) {
              if ($item->cant_inscriptos > 0) {
                $item->campo_para_usar = $item->cant_confirmo*100/$item->cant_inscriptos;
              }
              else {
                $item->campo_para_usar = 0;  
              }                          
          });

          $titulo_1 = '';
          $titulo_2 = $gCont->formatoNumero($Inscripciones_top->sum('cant_confirmo'), 'entero');
          if ($cant_inscriptos_tot > 0) {
            $valor_porc = $Inscripciones_top->sum('cant_confirmo')*100/$cant_inscriptos_tot;
          }
          else {
            $valor_porc = 0;  
          }
          $valor_porc = $gCont->formatoNumero($valor_porc, 'decimal');        
          $descripcion = $valor_porc.'% de '.$cant_inscriptos_tot.' Inscripciones';

          $widget = [
            'cant_top' => 3,
            'icono' => 'fa fa-check',
            'titulo' => __('Confirmados'),
            'tooltip' => __('Personas inscriptas a una conferencia o curso que despues de ser contactados confirmaron su asistencia al evento').': '.$pais.' '.$periodo_mostrar.' ('.__('un porcentaje óptimo debería estar por encima del 70%').')',
            'titulo_1' => $titulo_1,
            'titulo_2' => $titulo_2,
            'valor_porc' => $valor_porc,
            'descripcion' => $descripcion
            ];
          $modal = [
            'id' => 'modal-confirmados',
            'titulo' => __('Confirmados').' | '.$pais.' | '.$periodo_mostrar,
            'enlace' => 'enlace'
            ];
          $info = [
            'periodo_mostrar' => $periodo_mostrar,
            'recomendacion_buen_rendimiento' => __('un porcentaje óptimo debería estar por encima del 70%'),
            'como_mejorar' => __('Es muy importante que cuando una persona se inscriba se la contacte dentro de las 2 o 3 horas siguientes, de no ser asi, las personas en un gran porcentaje pierden el interes de asistir, y por lo tanto caen los porcentajes de confirmación al evento. Cuando una persona se inscribe, y recibe rápidamente un mensaje de pedido de confirmación normalmente confirma su asistencia, y esto se traduce en mas altos porcentajes de asistencia al evento, este paso en el proceso de inscripción es fundamental y decisivo en el éxito de la convocatoria al evento y no debe descuidarse. El responsable de inscripción que haga esta tarea debe ser una persona con disponibilidad horaria para estar atento a las nuevas inscripcioones y contactarlas lo mas rápidamente. Una posible solución a esto es revisar las campañas que han tenido bajo rendimiento de confirmación y sugerir al responsable de inscripción que atienda con mayor rapidez las inscripciones o buscar alguna otra persona con mejor disponibilidad de tiempo para esto.')
            ];
          $columnas = [
            'titulos' => [
              __('Localidad'), 
              __('Inscripciones'), 
              '% '.__('Confirmados')
              ], 
            'campos' => [
              'localidad',
              'cant_inscriptos',
              'campo_para_usar'
              ],
            'columna_orden' => 'campo_para_usar'
            ];
          $ranking = $Inscripciones_top;
          $valores_color = [
            'warning' => [55, 70],
            'danger' => [0, 55]
            ];

          App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
          ?>
        <!-- CONFIRMADOS -->


        <!-- ASISTENTES -->
          <?php 
          $Inscripciones_top = $Inscripciones2->each(function ($item, $key) {
              if ($item->cant_inscriptos > 0) {
                $item->campo_para_usar = $item->cant_asistio*100/$item->cant_inscriptos;
              }
              else {
                $item->campo_para_usar = 0;  
              }               
          });

          $titulo_1 = '';
          $titulo_2 = $gCont->formatoNumero($Inscripciones_top->sum('cant_asistio'), 'entero');
          if ($cant_inscriptos_tot > 0) {
            $valor_porc = $Inscripciones_top->sum('cant_asistio')*100/$cant_inscriptos_tot;
          }
          else {
            $valor_porc = 0;  
          }
          $valor_porc = $gCont->formatoNumero($valor_porc, 'decimal');        
          $descripcion = $valor_porc.'% de '.$cant_inscriptos_tot.' Inscripciones';


          $widget = [
            'cant_top' => 3,
            'icono' => 'fa fa-qrcode',
            'titulo' => __('Asistentes'),
            'tooltip' => __('Personas que asistieron al inicio del curso o conferencia inicial').': '.$pais.' '.$periodo_mostrar.' ('.__('el porcentaje siempre debería estar por encima del 50% del total de inscriptos').')',
            'titulo_1' => $titulo_1,
            'titulo_2' => $titulo_2,
            'valor_porc' => $valor_porc,
            'descripcion' => $descripcion
            ];
          $modal = [
            'id' => 'modal-asistio',
            'titulo' => __('Asistentes').' | '.$pais.' | '.$periodo_mostrar,
            'enlace' => 'enlace'
            ];
          $info = [
            'periodo_mostrar' => $periodo_mostrar,
            'recomendacion_buen_rendimiento' => __('el porcentaje siempre debería estar por encima del 50% del total de inscriptos'),
            'como_mejorar' => __('Los porcentajes bajos de asistencia pueden deberse a distintos factores, enumeramos a los mas comunes para que en función de esto se analicen las campañas con rendimientos bajos de asistencia y se tomen las acciones necesarias para su correción. 1) No se enviaron los recordatorios, o se enviaron tarde. 2) No se registro la asistencia en el Sistema AC, es decir no se leyo el codigo QR del voucher ni tampoco se utilizó la lista de asistencias del sistema para registrar los asistentes. 3) Condiciones climáticas desfavorables el día del evento. 4) El evento se ha realizado en un lugar de dificil acceso o no apropiado para la asistencia masiva')
            ];
          $columnas = [
            'titulos' => [
              __('Localidad'), 
              __('Inscriptos'), 
              '% '.__('Asistentes')
              ], 
            'campos' => [
              'localidad',
              'cant_inscriptos',
              'campo_para_usar'
              ],
            'columna_orden' => 'campo_para_usar'
            ];
          $ranking = $Inscripciones_top;
          $valores_color = [
            'warning' => [45, 50],
            'danger' => [0, 45]
            ];

          App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
          ?>
        <!-- ASISTENTES --> 
      </div>


    <?php } 
    else { ?>

      <div class="col-lg-12 col-xs-12">
        <div class="alert alert-info alert-dismissible">
          <h4><i class="icon fa fa-info"></i> <?php echo __('Inscripciones') ?></h4>
          <?php echo __('No hemos encontrado inscripciones para') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?>
        </div> 
      </div>      
    <?php } ?>


    <!-- PANEL INSCRIPTOS Y CAMPAÑAS X PROVINCIA -->
      
        <section class="content">
          <div class="row">
            <div class="col-lg-12 col-xs-12">
              <!-- PANEL INSCRIPTOS -->
                <div class="col-md-6">
                  
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      <h3 class="box-title"><?php echo __('Inscriptos por etapa en %') ?></h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    <div class="box-body">
                      <div class="chart" data-toggle="tooltip" data-original-title="<?php echo __('Etapas en el proceso de inscripción para todas las campañas de') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?> (<?php echo __('el valor mundial es un valor de referencia promedio que deberia estar por debajo de nuestros resultados') ?>)">
                          <canvas id="evolucionInscriptos" width="400" height="250"></canvas>
                      </div>

                      <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-default">
                        <?php echo __('Referencia') ?> <?php echo __('Etapas') ?>
                      </button>
                      <!-- MODAL REFERENCIAS -->
                        <div class="modal fade" id="modal-default">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"><?php echo __('Referencias') ?> <?php echo __('Etapas') ?></h4>
                              </div>
                              <div class="modal-body">                            
                                <table class="table table-condensed table-hover">
                                  <tbody>
                                    <tr>
                                      <th style="width: 10px">#</th>
                                      <th><?php echo __('Etapas') ?></th>
                                      <th></th>
                                    </tr>
                                    <tr>
                                      <td>1.</td>
                                      <td><?php echo __('Inscriptos') ?></td>
                                      <td><?php echo __('Personas que se inscribieron') ?></td>
                                    </tr>
                                    <tr>
                                      <td>2.</td>
                                      <td><?php echo __('Eligio un horario') ?></td>
                                      <td><?php echo __('Personas que se inscribieron y seleccionaron un horario para asistir') ?></td>
                                    </tr>
                                    <tr>
                                      <td>3.</td>
                                      <td><?php echo __('Contactados') ?></td>
                                      <td><?php echo __('Personas inscriptas que fueron contactadas por los responsables inscripción de cada lugar') ?></td>
                                    </tr>
                                    <tr>
                                      <td>4.</td>
                                      <td><?php echo __('Confirmados') ?></td>
                                      <td><?php echo __('Personas inscriptas a una conferencia o curso que despues de ser contactados confirmaron su asistencia al evento') ?></td>
                                    </tr>
                                    <tr>
                                      <td>5.</td>
                                      <td><?php echo __('Voucher') ?></td>
                                      <td><?php echo __('Personas confirmadas a una conferencia o curso a las que se les ha enviado el voucher') ?></td>
                                    </tr>
                                    <tr>
                                      <td>6.</td>
                                      <td><?php echo __('Motivacion') ?></td>
                                      <td><?php echo __('Personas confirmadas a las que se les envio algun tipo de material motivacional previo al evento') ?></td>
                                    </tr>
                                    <tr>
                                      <td>7.</td>
                                      <td><?php echo __('Recordatorio') ?></td>
                                      <td><?php echo __('Personas a las que se les envio el recordatorio para que asistan al inicio del curso o conferencia inicial') ?></td>
                                    </tr>
                                    <tr>
                                      <td>8.</td>
                                      <td><?php echo __('Asistentes') ?></td>
                                      <td><?php echo __('Personas que asistieron al inicio del curso o conferencia inicial') ?></td>
                                    </tr>
                                    <tr>
                                      <td>9.</td>
                                      <td><?php echo __('Recordatorio Próx clase') ?></td>
                                      <td><?php echo __('Personas a las que se les envio el recordatorio para que asistan a la segunda clase') ?></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                              </div>
                            </div>
                            <!-- /.modal-content -->
                          </div>
                          <!-- /.modal-dialog -->
                        </div>
                      <!-- MODAL REFERENCIAS -->

                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                
                </div>
              <!-- PANEL INSCRIPTOS -->

              <!-- CAMPAÑAS X PROVINCIA -->
                <div class="col-md-6">
                  <!-- AREA CHART -->
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      <h3 class="box-title"><?php echo __('Campañas por Provincia/Departamento/Estado') ?></h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    <div class="box-body" data-toggle="tooltip" data-original-title="<?php echo __('Cantidad de campañas por provincia, estado o región') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?>">
                      <div class="chart">
                        <canvas id="campaniasPorProvincia" width="400" height="270"></canvas>
                      </div>
                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                  
                </div>
              <!-- CAMPAÑAS X PROVINCIA -->
            </div>
          </div>
        </section>      
    <!-- PANEL INSCRIPTOS Y CAMPAÑAS X PROVINCIA -->


    <!-- CURSOS ONLINE -->
      <section class="content">
        <div class="row">
          <div class="col-lg-12 col-xs-12">
            <div class="box">

              <div class="box-header with-border">
                <h3 class="box-title"><?php echo __('Cursos Online') ?></h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <div class="row">

                  <!-- GRAFICO CURSOS ONLINE -->
                    <div class="col-md-6">                    
                      <div class="chart" data-toggle="tooltip" data-original-title="<?php echo __('Cantidad de Inscriptos en cursos on line por país') ?> <?php echo $pais ?>">
                        <!-- Sales Chart Canvas -->
                        <canvas id="inscriptos-cursos-online" width="400" height="250"></canvas>
                      </div>
                      <!-- /.chart-responsive -->
                    </div>
                  <!-- GRAFICO CURSOS ONLINE -->

                  <!-- TABLA CURSOS ONLINE -->
                    <div class="col-md-6">
                      <table id="ciudades-curso-online" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th><?php echo __('Pais') ?></th>
                            <th><?php echo __('Ciudad') ?></th>
                            <th><?php echo __('Inscriptos') ?></th>
                          </tr>
                        </thead>
                        <tbody>                  
                          <?php foreach ($Online_ciudades as $Online_ciudad) { ?>
                            <tr>
                              <td><?php echo $Online_ciudad->pais ?></td>
                              <td><?php echo $Online_ciudad->ciudad ?></td>
                              <td><?php echo $Online_ciudad->cant ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>

                    </div>
                  <!-- TABLA CURSOS ONLINE -->
                </div>
                <!-- /.row -->
              </div>
              <!-- TOTALES FOOTER -->
                <div class="box-footer">
                  <div class="row">
                    <div class="col-sm-3 col-xs-6">
                      <div class="description-block border-right">
                        <h5 class="description-header"><?php echo $gCont->formatoNumero($total_inscriptos_online, 'entero') ?></h5>
                        <span class="description-text"><?php echo __('Total') ?> <?php echo __('Inscriptos') ?></span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                      <div class="description-block border-right">
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                      <div class="description-block">
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                      <div class="description-block border-right">
                        <h5 class="description-header"><?php echo $gCont->formatoNumero($Online_ciudades->count(), 'entero') ?></h5>
                        <span class="description-text"><?php echo __('Total') ?> <?php echo __('Ciudades') ?></span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                  </div>
                  <!-- /.row -->
                </div>
              <!-- TOTALES FOOTER -->
            </div>
            <!-- /.box -->
          </div>
        </div>
      </section>
    <!-- CURSOS ONLINE -->


    <div class="col-lg-12 col-xs-12">
      <div class="nav-tabs-custom">        
        <ul class="nav nav-tabs pull-right">
          <li><a href="#tab_3" data-toggle="tab"><?php echo __('Menor costo por conversión') ?></a></li>
          <li><a href="#tab_2" data-toggle="tab"><?php echo __('Menor costo por llegada a personas') ?></a></li>
          <li class="active"><a href="#tab_1" data-toggle="tab"><?php echo __('Mejor costo por inscripto') ?></a></li>
          <li class="pull-left header"><i class="fa fa-th"></i> <?php echo __('Campañas Óptimas') ?></li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
            <!-- CAMPAÑAS MEJOR COSTO -->
              <table id="campanias-mejor-costo" class="table table-bordered table-hover" data-toggle="tooltip" data-original-title="<?php echo __('Estas son las campañas para') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?> <?php echo __('ordenadas por COSTO MENOR DE INSCRIPTO (el monto esta expresado en la moneda local del pais)') ?>">
                <thead>
                  <tr>
                    <th><?php echo __('Acción') ?></th>
                    <th><?php echo __('COSTO X INSCRIPTO') ?></th>
                    <th><?php echo __('Tipo de evento') ?></th>
                    <th><?php echo __('Pais') ?></th>
                    <th><?php echo __('Región') ?></th>
                    <th><?php echo __('Ciudad') ?></th>
                    <th><?php echo __('Fecha de solicitud') ?></th>
                    <th><?php echo __('Importe') ?></th>
                    <th><?php echo __('Inscriptos') ?></th>
                  </tr>
                </thead>
                <tbody>                  
                  <?php foreach ($Solicitudes_optimas as $Solicitud) { ?>
                    <?php if ($Solicitud->cant_inscriptos > 0) { ?>                
                      <tr>
                        <td>
                          <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/ver/<?php echo $Solicitud->id; ?>">
                            <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-hand-pointer-o"></i></button>
                          </a>
                        </td>
                        <th><?php echo round($Solicitud->importe / $Solicitud->cant_inscriptos, 2) ?></th>
                        <td><?php echo $Solicitud->tipo_de_evento ?></td>
                        <td><?php echo $Solicitud->pais ?></td>
                        <td><?php echo $Solicitud->provincia ?></td>
                        <td><?php echo $Solicitud->localidad ?></td>
                        <td><?php echo $Solicitud->fecha_de_solicitud ?></td>
                        <td><?php echo $Solicitud->importe ?></td>
                        <td><?php echo $Solicitud->cant_inscriptos ?></td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            <!-- CAMPAÑAS MEJOR COSTO -->
          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_2">
            <!-- CAMPAÑAS MEJOR ALCANCE -->
              <table id="campanias-mejor-alcance" class="table table-bordered table-hover" data-toggle="tooltip" data-original-title="<?php echo __('Estas son las campañas para') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?> <?php echo __('ordenadas por  menor costo para llegar a las personas (el monto esta expresado en la moneda local del pais)') ?>">
                <thead>
                  <tr>
                    <th><?php echo __('Acción') ?></th>
                    <th><?php echo __('Costo por llegada a cada persona') ?></th>
                    <th><?php echo __('Tipo de evento') ?></th>
                    <th><?php echo __('Pais') ?></th>
                    <th><?php echo __('Región') ?></th>
                    <th><?php echo __('Ciudad') ?></th>
                    <th><?php echo __('Fecha de solicitud') ?></th>
                    <th><?php echo __('Importe') ?></th>
                    <th><?php echo __('Alcance') ?></th>
                  </tr>
                </thead>
                <tbody>                  
                  <?php foreach ($Solicitudes_optimas as $Solicitud) { ?>
                    <?php if ($Solicitud->alcances > 0) { ?>                              
                      <tr>
                        <td>
                          <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/ver/<?php echo $Solicitud->id; ?>">
                            <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                          </a>
                        </td>
                        <td><?php echo round($Solicitud->importe / $Solicitud->alcances, 2) ?></td>
                        <td><?php echo $Solicitud->tipo_de_evento ?></td>
                        <td><?php echo $Solicitud->pais ?></td>
                        <td><?php echo $Solicitud->provincia ?></td>
                        <td><?php echo $Solicitud->localidad ?></td>
                        <td><?php echo $Solicitud->fecha_de_solicitud ?></td>
                        <td><?php echo $Solicitud->importe ?></td>
                        <td><?php echo $Solicitud->alcances ?></td>
                      </tr>                              
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            <!-- CAMPAÑAS MEJOR ALCANCE -->
          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_3">
            <!-- CAMPAÑAS MEJOR CONVERSION -->
              <table id="campanias-mejor-conversion" class="table table-bordered table-hover" data-toggle="tooltip" data-original-title="<?php echo __('Estas son las campañas para') ?>: <?php echo $pais ?> <?php echo $periodo_mostrar ?> <?php echo __('ordenadas por  mejor relación entre visualizaciones e inscriptos, es decir cantidad de visualizaciones promedio que se necesita para que una persona se inscriba, esto significa que la propuesta es mas atractiva cuando la conversión es menor') ?>">
                <thead>
                  <tr>
                    <th><?php echo __('Acción') ?></th>
                    <th><?php echo __('Visualizaciones') ?> / <?php echo __('Inscripciones') ?> </th>
                    <th><?php echo __('Tipo de evento') ?></th>
                    <th><?php echo __('Pais') ?></th>
                    <th><?php echo __('Región') ?></th>
                    <th><?php echo __('Ciudad') ?></th>
                    <th><?php echo __('Fecha de solicitud') ?></th>
                    <th><?php echo __('Visualizaciones') ?></th>
                    <th><?php echo __('Inscripciones') ?></th>
                  </tr>
                </thead>
                <tbody>                  
                  <?php foreach ($Solicitudes_optimas as $Solicitud) { ?>
                    <?php if ($Solicitud->cant_inscriptos > 0) { ?>                              
                      <tr>
                        <td>
                          <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/ver/<?php echo $Solicitud->id; ?>">
                            <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                          </a>
                        </td>
                        <td>
                          <?php 
                            if ($Solicitud->cant_inscriptos > 0) {
                              echo round($Solicitud->cant_visualizaciones/$Solicitud->cant_inscriptos, 2);
                            }
                            else {
                              echo 0;  
                            }            
                          ?>
                        </td>
                        <td><?php echo $Solicitud->tipo_de_evento ?></td>
                        <td><?php echo $Solicitud->pais ?></td>
                        <td><?php echo $Solicitud->provincia ?></td>
                        <td><?php echo $Solicitud->localidad ?></td>
                        <td><?php echo $Solicitud->fecha_de_solicitud ?></td>
                        <td><?php echo $Solicitud->cant_visualizaciones ?></td>
                        <td><?php echo $Solicitud->cant_inscriptos ?></td>
                      </tr>                              
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            <!-- CAMPAÑAS MEJOR CONVERSION -->
          </div>
          <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
      </div>
      <!-- nav-tabs-custom -->
    </div>

<!-- jQuery 3 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- ChartJS -->
<!--script src="<?php echo env('PATH_PUBLIC')?>bower_components/chart.js/Chart.js"></script-->
<script src="<?php echo env('PATH_PUBLIC')?>node_modules/chart.js/dist/Chart.js"></script>
<!-- FastClick -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/demo.js"></script>
<!-- page script -->


<!-- DataTables -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>

  // EVOLUCION INSCRIPTOS
    var ctx = document.getElementById('evolucionInscriptos');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels  : ['<?php echo __('Inscriptos') ?>', '<?php echo __('Eligio un horario') ?>', '<?php echo __('Contactados') ?>', '<?php echo __('Confirmados') ?>', '<?php echo __('Voucher') ?>', '<?php echo __('Motivacion') ?>', '<?php echo __('Recordatorio') ?>', '<?php echo __('Asistentes') ?>', '<?php echo __('Recordatorio Próx clase') ?>'],
            datasets: [
            <?php 
            $i = -1;
            foreach ($Inscripciones as $Inscripcion) {
              $i++;
              $cant_inscriptos = $Inscripcion->cant_inscriptos;
              $cant_inscriptos_eligio = round($Inscripcion->cant_inscriptos_eligio * 100 / $cant_inscriptos);
              $cant_contactados = round($Inscripcion->cant_contactados * 100 / $cant_inscriptos);
              $cant_confirmo = round($Inscripcion->cant_confirmo * 100 / $cant_inscriptos);
              $cant_voucher = round($Inscripcion->cant_voucher * 100 / $cant_inscriptos);
              $cant_motivacion = round($Inscripcion->cant_motivacion * 100 / $cant_inscriptos);
              $cant_recordatorio = round($Inscripcion->cant_recordatorio * 100 / $cant_inscriptos);
              $cant_asistio = round($Inscripcion->cant_asistio * 100 / $cant_inscriptos);
              $cant_recordatorio_prox = round($Inscripcion->cant_recordatorio_prox * 100 / $cant_inscriptos);          
              $cant_inscriptos = 100;
            ?>        
            {
                label: '<?php echo $Inscripcion->detalle ?>',         
                data: [
                  <?php echo $cant_inscriptos ?>, 
                  <?php echo $cant_inscriptos_eligio ?>, 
                  <?php echo $cant_contactados ?>, 
                  <?php echo $cant_confirmo ?>, 
                  <?php echo $cant_voucher ?>, 
                  <?php echo $cant_motivacion ?>, 
                  <?php echo $cant_recordatorio ?>, 
                  <?php echo $cant_asistio ?>, 
                  <?php echo $cant_recordatorio_prox ?>
                  ],
                backgroundColor: [
                    'rgba(<?php echo $colores[$i] ?>, 0.5)',
                    'rgba(<?php echo $colores[$i] ?>, 0.5)',
                    'rgba(<?php echo $colores[$i] ?>, 0.5)',
                    'rgba(<?php echo $colores[$i] ?>, 0.5)',
                    'rgba(<?php echo $colores[$i] ?>, 0.5)',
                    'rgba(<?php echo $colores[$i] ?>, 0.5)',
                    'rgba(<?php echo $colores[$i] ?>, 0.5)',
                    'rgba(<?php echo $colores[$i] ?>, 0.5)',
                    'rgba(<?php echo $colores[$i] ?>, 0.5)'
                ],
                borderColor: [
                    'rgba(<?php echo $colores[$i] ?>, 1)',
                    'rgba(<?php echo $colores[$i] ?>, 1)',
                    'rgba(<?php echo $colores[$i] ?>, 1)',
                    'rgba(<?php echo $colores[$i] ?>, 1)',
                    'rgba(<?php echo $colores[$i] ?>, 1)',
                    'rgba(<?php echo $colores[$i] ?>, 1)',
                    'rgba(<?php echo $colores[$i] ?>, 1)',
                    'rgba(<?php echo $colores[$i] ?>, 1)',
                    'rgba(<?php echo $colores[$i] ?>, 1)'
                ],
                borderWidth: 4
            },
          <?php } ?>
          ],
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            return value+'%';
                        }
                    }
                }]
            },

            elements: {
                line: {
                    tension: 0 // disables bezier curves
                }
            },

            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = tooltipItem.value + '%';
                        return label;
                    }
                }
              }
        }
    });
  // EVOLUCION INSCRIPTOS

  // CAMPAÑAS X PROVINCIA
    var ctx = document.getElementById('campaniasPorProvincia');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels  : [
            <?php foreach ($Solicitudes_por_provincia as $Solicitud) { ?>   
            '<?php echo $Solicitud->provincia; ?>', 
            <?php } ?>
            ],
            datasets: [   
            {
                label: '<?php echo __('Cantidad') ?> <?php echo __('Campañas') ?>',         
                data: [
                <?php foreach ($Solicitudes_por_provincia as $Solicitud) { ?> 
                  <?php echo $Solicitud->cant_campanias ?>, 
                <?php } ?>
                  ],
                borderWidth: 1
            },
          ],
        },
        options: {

            elements: {
                line: {
                    tension: 0 // disables bezier curves
                }
            }
        }
    });
  // CAMPAÑAS X PROVINCIA

  // CURSOS ONLINE
    var ctx = document.getElementById('inscriptos-cursos-online');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels  : [
            <?php foreach ($Online_meses as $Online_mes) { ?>   
            '<?php echo $Online_mes->periodo; ?>', 
            <?php } ?>
            ],
            datasets: [   
            {
                label: '<?php echo __('Inscriptos') ?>',         
                data: [
                <?php foreach ($Online_meses as $Online_mes) { ?> 
                  <?php echo $Online_mes->cant ?>, 
                <?php } ?>
                  ],
                backgroundColor: [
                    'rgba(<?php echo $colores[0] ?>, 0.2)'
                ],
                borderColor: [
                    'rgba(<?php echo $colores[0] ?>, 1)'
                ],
                borderWidth: 4
            },
          ],
        },
        options: {

            elements: {
                line: {
                    tension: 0 // disables bezier curves
                }
            }
        }
    });
  // CURSOS ONLINE


  // TABLA CURSOS ONLINE
    $(function () {
      $('#ciudades-curso-online').DataTable({
        "lengthChange": false,
          'responsive': true,
          'searching': true,
          'autoWidth': true,
          'pageLength': 6, 
          'menu': false,
          'order': [[ 2, 'desc' ]],
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


          
      })
    })
  // TABLA CURSOS ONLINE

  // TABLA CAMPAÑAS MEJOR COSTO
    $(function () {
      $('#campanias-mejor-costo').DataTable({
          'lengthChange': false,
          'responsive': true,
          'searching': true,
          'autoWidth': true,
          'pageLength': 6, 
          'menu': false,
          'order': [[ 1, 'asc' ]],
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


          
      })
    })
  // TABLA CAMPAÑAS MEJOR COSTO


  // TABLA CAMPAÑAS MEJOR ALCANCE
    $(function () {
      $('#campanias-mejor-alcance').DataTable({
        "lengthChange": false,
          'responsive': true,
          'searching': true,
          'autoWidth': true,
          'pageLength': 6, 
          'menu': false,
          'order': [[ 1, 'asc' ]],
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


          
      })
    })
  // TABLA CAMPAÑAS MEJOR ALCANCE



  // TABLA CAMPAÑAS MEJOR CONVERSION
    $(function () {
      $('#campanias-mejor-conversion').DataTable({
        "lengthChange": false,
          'responsive': true,
          'searching': true,
          'autoWidth': true,
          'pageLength': 6, 
          'menu': false,
          'order': [[ 1, 'asc' ]],
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


          
      })
    })
  // TABLA CAMPAÑAS MEJOR CONVERSION


</script>

