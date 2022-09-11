<?php
$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;


use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;

?>


@extends('layouts.backend')



@section('contenido')


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

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Content Header (Page header) -->
  <section class="content-header">
        {!! Form::open(array
          (
          'url' => env('PATH_PUBLIC').'traer-ranking-m', 
          'role' => 'form',
          'method' => 'POST',
          'id' => "form_ranking",
          'enctype' => 'multipart/form-data',
          'class' => 'form-horizontal',
          'ref' => 'form'
          )) 
        !!}


    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title">Desempeño</h3>
      </div>
      <div class="panel-body">
        <span class="box-tools pull-right">
          <p>Seleccione el período de tiempo

            <?php 
            if (isset($home)) { 
              $etiqueta_periodo = __('Seleccione el período de tiempo');
            }
            else {
              $etiqueta_periodo = $periodo_mostrar;
            }
            ?>

          <div class="input-group">
            <button type="button" class="btn btn-default pull-right" id="daterange-btn">
              <span>
                <i class="fa fa-calendar"></i> <?php echo $etiqueta_periodo ?>
              </span>
              <i class="fa fa-caret-down"></i>
            </button>
          </div>
        </p>
        </span>
          <input type="hidden" name="periodo" id="periodo" class="form-control">
          <input type="hidden" name="periodo_mostrar" id="periodo_mostrar" class="form-control">
      </div>
    </div>


      
        {!! Form::close() !!}

  </section>

  <section class="content" style="min-height: 1200px">
    <?php if (!isset($home)) { ?>

      <?php if ($Solicitudes->count() > 0) { ?>

        <!-------------------- FILA 1 --------------------->
        <div class="col-lg-12 col-xs-12">
          <!-- CANTIDAD DE CAMPAÑAS -->
            <?php 
            if ($Solicitudes->count() > 0) {
              $Solicitudes_top = $Solicitudes->sortByDesc('cant_campanias');
              $Solicitudes_top = $Solicitudes_top->take(3);
              $Solicitudes_top = $Solicitudes_top->values()->all();
            ?>
              <div class="col-lg-3 col-xs-12">
                <!-- small box -->
                <div class="small-box bg-aqua" data-toggle="tooltip" data-trigger="hover" data-original-title="<?php echo __('Cantidad de Campañas por País') ?>: <?php echo $periodo_mostrar ?>">
                  <div class="inner">
                    <h3><?php echo $gCont->formatoNumero($Solicitudes_top[0]->cant_campanias, 'entero') ?></h3>
                    <p>1° <?php echo $Solicitudes_top[0]->pais ?></p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                  </div>

                    <div class="small-box-footer tit-bg-rk">
                        <strong><?php echo __('Cantidad de Campañas por País') ?></strong>
                    </div>
                  <?php 
                  $i=0;
                  foreach ($Solicitudes_top as $Solicitud) { 
                  $i++;
                  ?>
                    <div class="small-box-footer">
                        <?php echo $i ?>° <?php echo $Solicitud->pais ?>: <strong><?php echo $gCont->formatoNumero($Solicitud->cant_campanias, 'entero') ?></strong>
                    </div>
                  <?php } ?>
                        <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-cant-campanias"><?php echo __('Lista Completa') ?></button>              
                </div>
              </div>

              <!-- MODAL CANTIDAD DE CAMPAÑAS -->
                <div class="modal fade" id="modal-cant-campanias">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo __('Cantidad de Campañas por País') ?> | <?php echo $periodo_mostrar ?> </h4>
                      </div>
                      <div class="modal-body">                            
                        <table class="table table-condensed table-hover">
                          <tbody>
                            <tr>
                              <th style="width: 10px">#</th>
                              <th><?php echo __('Pais') ?></th>
                              <th></th>
                            </tr>
                            <?php 
                            $Solicitudes_top = $Solicitudes->sortByDesc('cant_campanias');
                            $Solicitudes_top = $Solicitudes_top->values()->all();

                            $i=0;
                            foreach ($Solicitudes_top as $Solicitud) { 
                            $i++;
                            ?>                      
                            <tr>
                              <td><?php echo $i ?>°</td>
                              <td><?php echo $Solicitud->pais ?></td>
                              <td style="text-align: right;"><?php echo $gCont->formatoNumero($Solicitud->cant_campanias, 'entero') ?></td>
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
              <!-- MODAL CANTIDAD DE CAMPAÑAS -->
            <?php } ?>
          <!-- CANTIDAD DE CAMPAÑAS -->

          <!-- INVERSION -->
            <?php 
            if ($Solicitudes->count() > 0) {
              $Solicitudes_top = $Solicitudes->sortByDesc('importe');
              $Solicitudes_top = $Solicitudes_top->take(3);
              $Solicitudes_top = $Solicitudes_top->values()->all();
            ?>
              <div class="col-lg-3 col-xs-12">
                <!-- small box -->
                <div class="small-box bg-aqua" data-toggle="tooltip" data-trigger="hover" data-original-title="<?php echo __('Inversion en campañas') ?>: <?php echo $periodo_mostrar ?>">
                  <div class="inner">
                    <h3>$ <?php echo $gCont->formatoNumero($Solicitudes_top[0]->importe, 'entero') ?></h3>
                    <p>1° <?php echo $Solicitudes_top[0]->pais ?></p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-usd"></i>
                  </div>

                    <div class="small-box-footer tit-bg-rk">
                        <strong><?php echo __('Inversion en campañas') ?></strong>
                    </div>
                  <?php 
                  $i=0;
                  foreach ($Solicitudes_top as $Solicitud) { 
                  $i++;
                  ?>
                    <div class="small-box-footer">
                        <?php echo $i ?>° <?php echo $Solicitud->pais ?>: $ <strong><?php echo $gCont->formatoNumero($Solicitud->importe, 'entero') ?></strong>
                    </div>
                  <?php } ?>
                        <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-inversion"><?php echo __('Lista Completa') ?></button>              
                </div>
              </div>

              <!-- MODAL INVERSION -->
                <div class="modal fade" id="modal-inversion">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo __('Inversion en campañas') ?> | <?php echo $periodo_mostrar ?> </h4>
                      </div>
                      <div class="modal-body">                            
                        <table class="table table-condensed table-hover">
                          <tbody>
                            <tr>
                              <th style="width: 10px">#</th>
                              <th><?php echo __('Pais') ?></th>
                              <th></th>
                            </tr>
                            <?php 
                            $Solicitudes_top = $Solicitudes->sortByDesc('importe');
                            $Solicitudes_top = $Solicitudes_top->values()->all();

                            $i=0;
                            foreach ($Solicitudes_top as $Solicitud) { 
                            $i++;
                            ?>                      
                            <tr>
                              <td><?php echo $i ?>°</td>
                              <td><?php echo $Solicitud->pais ?></td>
                              <td style="text-align: right;">$ <?php echo $gCont->formatoNumero($Solicitud->importe, 'entero') ?></td>
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
              <!-- MODAL INVERSION -->
            <?php } ?>
          <!-- INVERSION -->

          <!-- ALCANCE -->
            <?php 
            if ($Solicitudes->count() > 0) {
              $Solicitudes_top = $Solicitudes->sortByDesc('alcance');
              $Solicitudes_top = $Solicitudes_top->take(3);
              $Solicitudes_top = $Solicitudes_top->values()->all();
            ?>
              <div class="col-lg-3 col-xs-12">
                <!-- small box -->
                <div class="small-box bg-aqua" data-toggle="tooltip" data-trigger="hover" data-original-title="<?php echo __('Cantidad de personas que fueron alcanzadas por la publicidad de las campañas') ?>: <?php echo $periodo_mostrar ?>">
                  <div class="inner">
                    <h3><?php echo $gCont->formatoNumero($Solicitudes_top[0]->alcance, 'entero') ?></h3>
                    <p>1° <?php echo $Solicitudes_top[0]->pais ?></p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-google"></i>
                  </div>

                    <div class="small-box-footer tit-bg-rk">
                        <strong><?php echo __('Personas que vieron la palabra GNOSIS') ?></strong>
                    </div>
                  <?php 
                  $i=0;
                  foreach ($Solicitudes_top as $Solicitud) { 
                  $i++;
                  ?>
                    <div class="small-box-footer">
                        <?php echo $i ?>° <?php echo $Solicitud->pais ?>: <strong><?php echo $gCont->formatoNumero($Solicitud->alcance, 'entero') ?></strong>
                    </div>
                  <?php } ?>
                        <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-alcance"><?php echo __('Lista Completa') ?></button>              
                </div>
              </div>

              <!-- MODAL ALCANCE -->
                <div class="modal fade" id="modal-alcance">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo __('Personas que vieron la palabra GNOSIS') ?> | <?php echo $periodo_mostrar ?> </h4>
                      </div>
                      <div class="modal-body">                            
                        <table class="table table-condensed table-hover">
                          <tbody>
                            <tr>
                              <th style="width: 10px">#</th>
                              <th><?php echo __('Pais') ?></th>
                              <th></th>
                            </tr>
                            <?php 
                            $Solicitudes_top = $Solicitudes->sortByDesc('alcance');
                            $Solicitudes_top = $Solicitudes_top->values()->all();

                            $i=0;
                            foreach ($Solicitudes_top as $Solicitud) { 
                            $i++;
                            ?>                      
                            <tr>
                              <td><?php echo $i ?>°</td>
                              <td><?php echo $Solicitud->pais ?></td>
                              <td style="text-align: right;"><?php echo $gCont->formatoNumero($Solicitud->alcance, 'entero') ?></td>
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
              <!-- MODAL ALCANCE -->
            <?php } ?>
          <!-- ALCANCE -->

          <!-- IMPRESIONES -->
            <?php 
            if ($Solicitudes->count() > 0) {
              $Solicitudes_top = $Solicitudes->sortByDesc('impresiones');
              $Solicitudes_top = $Solicitudes_top->take(3);
              $Solicitudes_top = $Solicitudes_top->values()->all();
            ?>
              <div class="col-lg-3 col-xs-12">
                <!-- small box -->
                <div class="small-box bg-aqua" data-toggle="tooltip" data-trigger="hover" data-original-title="<?php echo __('Cantidad de volantes digitales entregados') ?>: <?php echo $periodo_mostrar ?>">
                  <div class="inner">
                    <h3><?php echo $gCont->formatoNumero($Solicitudes_top[0]->impresiones, 'entero') ?></h3>
                    <p>1° <?php echo $Solicitudes_top[0]->pais ?></p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-file"></i>
                  </div>

                    <div class="small-box-footer tit-bg-rk">
                        <strong><?php echo __('Volantes Digitales Entregados') ?></strong>
                    </div>
                  <?php 
                  $i=0;
                  foreach ($Solicitudes_top as $Solicitud) { 
                  $i++;
                  ?>
                    <div class="small-box-footer">
                        <?php echo $i ?>° <?php echo $Solicitud->pais ?>: <strong><?php echo $gCont->formatoNumero($Solicitud->impresiones, 'entero') ?></strong>
                    </div>
                  <?php } ?>
                        <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-impresiones"><?php echo __('Lista Completa') ?></button>              
                </div>
              </div>

              <!-- MODAL IMPRESIONES -->
                <div class="modal fade" id="modal-impresiones">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo __('Volantes Digitales Entregados') ?> | <?php echo $periodo_mostrar ?> </h4>
                      </div>
                      <div class="modal-body">                            
                        <table class="table table-condensed table-hover">
                          <tbody>
                            <tr>
                              <th style="width: 10px">#</th>
                              <th><?php echo __('Pais') ?></th>
                              <th></th>
                            </tr>
                            <?php 
                            $Solicitudes_top = $Solicitudes->sortByDesc('impresiones');
                            $Solicitudes_top = $Solicitudes_top->values()->all();

                            $i=0;
                            foreach ($Solicitudes_top as $Solicitud) { 
                            $i++;
                            ?>                      
                            <tr>
                              <td><?php echo $i ?>°</td>
                              <td><?php echo $Solicitud->pais ?></td>
                              <td style="text-align: right;"><?php echo $gCont->formatoNumero($Solicitud->impresiones, 'entero') ?></td>
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
              <!-- MODAL IMPRESIONES -->
            <?php } ?>
          <!-- IMPRESIONES -->
        </div>


      <!-------------------- FILA 2 --------------------->

      <div class="col-lg-12 col-xs-12">
        <!-- CONVERSIONES -->
          <?php 
          if ($Solicitudes_optimas->count() > 0) {
            
            $Conversion_paises = collect();

            $Solicitudes_paises = $Solicitudes_optimas->groupBy('pais');
            
            foreach ($Solicitudes_paises as $key => $Solicitudes_pais) {
              $cant_inscriptos = $Solicitudes_pais->sum('cant_inscriptos');
              $cant_visualizaciones = $Solicitudes_pais->sum('cant_visualizaciones');

              if ($cant_inscriptos > 0 and $cant_visualizaciones > 0 ) {
                $conversion = $cant_visualizaciones/$cant_inscriptos;
                $datos = [
                  'pais' => $key, 
                  'cant_inscriptos' => $cant_inscriptos, 
                  'cant_visualizaciones' => $cant_visualizaciones, 
                  'conversion' => $conversion
                  ];

                $Conversion_paises->push($datos);
              }
            }

            $Conversion_top = $Conversion_paises->sortBy('conversion');
            $Conversion_top = $Conversion_top->take(3);
            $Conversion_top = $Conversion_top->values()->all();
            if (count($Conversion_top) > 0) {
            ?>
                <div class="col-lg-3 col-xs-12">
                  <!-- small box -->
                  <div class="small-box bg-aqua" data-toggle="tooltip" data-trigger="hover" data-original-title="<?php echo __('Mejor relación entre visualizaciones e inscriptos, es decir cantidad de visualizaciones promedio que se necesita para que una persona se inscriba, esto significa que la propuesta es mas atractiva cuando la conversión es menor') ?>: <?php echo $periodo_mostrar ?>">
                    <div class="inner">
                      <h3><?php echo $gCont->formatoNumero($Conversion_top[0]['conversion'], 'decimal') ?></h3>
                      <p>1° <?php echo $Conversion_top[0]['pais'] ?></p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-star-o"></i>
                    </div>

                      <div class="small-box-footer tit-bg-rk">
                          <strong><?php echo __('Mejor conversion') ?></strong>
                      </div>
                    <?php 
                    $i=0;
                    foreach ($Conversion_top as $Conversion) { 
                    $i++;
                    ?>
                      <div class="small-box-footer">
                          <?php echo $i ?>° <?php echo $Conversion['pais'] ?>: <strong><?php echo $gCont->formatoNumero($Conversion['conversion'], 'decimal') ?></strong>
                      </div>
                    <?php } ?>
                          <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-conversion"><?php echo __('Lista Completa') ?></button>              
                  </div>
                </div>

                <!-- MODAL CONVERSIONES -->
                  <div class="modal fade" id="modal-conversion">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title"><?php echo __('Volantes Digitales Entregados') ?> | <?php echo $periodo_mostrar ?> </h4>
                        </div>
                        <div class="modal-body">                            
                          <table class="table table-condensed table-hover">
                            <tbody>
                              <tr>
                                <th style="width: 10px">#</th>
                                <th><?php echo __('Pais') ?></th>
                                <th><?php echo __('Visualizaciones') ?></th>
                                <th><?php echo __('Inscripciones') ?></th>
                                <th><?php echo __('Conversion') ?></th>
                              </tr>
                              <?php 
                              $Conversion_top = $Conversion_paises->sortBy('conversion');
                              $Conversion_top = $Conversion_top->values()->all();

                              $i=0;
                              foreach ($Conversion_top as $Conversion) { 
                              $i++;
                              ?>                      
                              <tr>
                                <td><?php echo $i ?>°</td>
                                <td><?php echo $Conversion['pais'] ?></td>
                                <td style="text-align: right;"><?php echo $gCont->formatoNumero($Conversion['conversion'], 'decimal') ?></td>
                                <td style="text-align: right;"><?php echo $gCont->formatoNumero($Conversion['cant_visualizaciones'], 'entero') ?></td>
                                <td style="text-align: right;"><?php echo $gCont->formatoNumero($Conversion['cant_inscriptos'], 'entero') ?></td>
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
                <!-- MODAL CONVERSIONES -->
            <?php } ?>
          <?php } ?>
        <!-- CONVERSIONES -->

    
        <!-- INSCRIPCIONES -->
          <?php 
          if ($Inscripciones->count() > 0) {
            $Inscripciones_top = $Inscripciones->sortByDesc('cant_inscriptos');
            $Inscripciones_top = $Inscripciones_top->take(3);
            $Inscripciones_top = $Inscripciones_top->values()->all();
          ?>
            <div class="col-lg-3 col-xs-12">
              <!-- small box -->
              <div class="small-box bg-aqua" data-toggle="tooltip" data-trigger="hover" data-original-title="<?php echo __('Cantidad de personas que se inscribieron a los cursos o conferencias') ?>: <?php echo $periodo_mostrar ?>">
                <div class="inner">
                  <h3><?php echo $gCont->formatoNumero($Inscripciones_top[0]->cant_inscriptos, 'entero') ?></h3>
                  <p>1° <?php echo $Inscripciones_top[0]->pais ?></p>
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
                      <?php echo $i ?>° <?php echo $Inscripcion->pais ?>: <strong><?php echo $gCont->formatoNumero($Inscripcion->cant_inscriptos, 'entero') ?></strong>
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
                            <th style="width: 10px">#</th>
                            <th><?php echo __('Pais') ?></th>
                            <th><?php echo __('Cantidad') ?></th>
                          </tr>
                          <?php 
                          $Inscripciones_top = $Inscripciones->sortByDesc('cant_inscriptos');
                          $Inscripciones_top = $Inscripciones_top->values()->all();

                          $i=0;
                          foreach ($Inscripciones_top as $Inscripcion) { 
                          $i++;
                          ?>                      
                          <tr>
                            <td><?php echo $i ?>°</td>
                            <td><?php echo $Inscripcion->pais ?></td>
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

        <!-- CURSOS ONLINE PAIS -->
          <?php 
          if ($Online_paises->count() > 0) {
            $Online_paises_top = $Online_paises->sortByDesc('cant');
            $Online_paises_top = $Online_paises_top->take(3);
            $Online_paises_top = $Online_paises_top->values()->all();
          ?>
            <div class="col-lg-3 col-xs-12">
              <!-- small box -->
              <div class="small-box bg-aqua" data-toggle="tooltip" data-trigger="hover" data-original-title="<?php echo __('Cantidad de Inscriptos en cursos on line por país') ?>: <?php echo $periodo_mostrar ?>">
                <div class="inner">
                  <h3><?php echo $gCont->formatoNumero($Online_paises_top[0]->cant, 'entero') ?></h3>
                  <p>1° <?php echo $Online_paises_top[0]->pais ?></p>
                </div>
                <div class="icon">
                  <i class="fa fa-laptop"></i>
                </div>

                  <div class="small-box-footer tit-bg-rk">
                      <strong><?php echo __('Inscriptos a Curso Online por país') ?></strong>
                  </div>
                <?php 
                $i=0;
                foreach ($Online_paises_top as $Online_pais) { 
                $i++;
                ?>
                  <div class="small-box-footer">
                      <?php echo $i ?>° <?php echo $Online_pais->pais ?>: <strong><?php echo $gCont->formatoNumero($Online_pais->cant, 'entero') ?></strong>
                  </div>
                <?php } ?>
                      <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-cant"><?php echo __('Lista Completa') ?></button>              
              </div>
            </div>

            <!-- MODAL CURSOS ONLINE PAIS -->
              <div class="modal fade" id="modal-cant">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title"><?php echo __('Inscriptos a Curso Online por país') ?> | <?php echo $periodo_mostrar ?> </h4>
                    </div>
                    <div class="modal-body">                            
                      <table class="table table-condensed table-hover">
                        <tbody>
                          <tr>
                            <th style="width: 10px">#</th>
                            <th><?php echo __('Pais') ?></th>
                            <th><?php echo __('Cantidad') ?></th>
                          </tr>
                          <?php 
                          $Online_paises_top = $Online_paises->sortByDesc('cant');
                          $Online_paises_top = $Online_paises_top->values()->all();

                          $i=0;
                          foreach ($Online_paises_top as $Online_pais) { 
                          $i++;
                          ?>                      
                          <tr>
                            <td><?php echo $i ?>°</td>
                            <td><?php echo $Online_pais->pais ?></td>
                            <td style="text-align: right;"><?php echo $gCont->formatoNumero($Online_pais->cant, 'entero') ?></td>
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
            <!-- MODAL CURSOS ONLINE PAIS -->
          <?php } ?>
        <!-- CURSOS ONLINE PAIS -->


        <!-- CURSOS ONLINE PAIS -->
          <?php 
          if ($Online_ciudades->count() > 0) {
            $Online_ciudades_top = $Online_ciudades->sortByDesc('cant');
            $Online_ciudades_top = $Online_ciudades_top->take(3);
            $Online_ciudades_top = $Online_ciudades_top->values()->all();
          ?>
            <div class="col-lg-3 col-xs-12">
              <!-- small box -->
              <div class="small-box bg-aqua" data-toggle="tooltip" data-trigger="hover" data-original-title="<?php echo __('Cantidad de Inscriptos en cursos on line por ciudad') ?>: <?php echo $periodo_mostrar ?>">
                <div class="inner">
                  <h3><?php echo $gCont->formatoNumero($Online_ciudades_top[0]->cant, 'entero') ?></h3>
                  <p>1° <?php echo $Online_ciudades_top[0]->ciudad ?>, <?php echo $Online_ciudades_top[0]->pais ?></p>
                </div>
                <div class="icon">
                  <i class="fa fa-laptop"></i>
                </div>

                  <div class="small-box-footer tit-bg-rk">
                      <strong><?php echo __('Inscriptos a Curso Online por ciudad') ?></strong>
                  </div>
                <?php 
                $i=0;
                foreach ($Online_ciudades_top as $Online_pais) { 
                $i++;
                ?>
                  <div class="small-box-footer">
                      <?php echo $i ?>° <?php echo $Online_pais->ciudad ?>, <?php echo $Online_pais->pais ?>: <strong><?php echo $gCont->formatoNumero($Online_pais->cant, 'entero') ?></strong>
                  </div>
                <?php } ?>
                      <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-cant"><?php echo __('Lista Completa') ?></button>              
              </div>
            </div>

            <!-- MODAL CURSOS ONLINE PAIS -->
              <div class="modal fade" id="modal-cant">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title"><?php echo __('Inscriptos a Curso Online por ciudad') ?> | <?php echo $periodo_mostrar ?> </h4>
                    </div>
                    <div class="modal-body">                            
                      <table class="table table-condensed table-hover">
                        <tbody>
                          <tr>
                            <th style="width: 10px">#</th>
                            <th><?php echo __('Pais') ?></th>
                            <th><?php echo __('Ciudad') ?></th>
                            <th><?php echo __('Cantidad') ?></th>
                          </tr>
                          <?php 
                          $Online_ciudades_top = $Online_ciudades->sortByDesc('cant');
                          $Online_ciudades_top = $Online_ciudades_top->values()->all();

                          $i=0;
                          foreach ($Online_ciudades_top as $Online_pais) { 
                          $i++;
                          ?>                      
                          <tr>
                            <td><?php echo $i ?>°</td>
                            <td><?php echo $Online_pais->pais ?></td>
                            <td><?php echo $Online_pais->ciudad ?></td>
                            <td style="text-align: right;"><?php echo $gCont->formatoNumero($Online_pais->cant, 'entero') ?></td>
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
            <!-- MODAL CURSOS ONLINE PAIS -->
          <?php } ?>
        <!-- CURSOS ONLINE PAIS -->
      </div>


      <!-------------------- FILA 3 --------------------->

        <?php if ($Inscripciones->count() > 0) { ?>

          <div class="col-lg-12 col-xs-12">
            <!-- CONTACTADOS -->
              <?php 
              $Inscripciones = $Inscripciones->each(function ($item, $key) {
                  $item->campo_para_usar = $item->cant_contactados*100/$item->cant_inscriptos;
              });

              $widget = [
                'cant_top' => 3,
                'icono' => 'fa fa-whatsapp',
                'titulo' => __('Contactados'),
                'tooltip' => __('Personas inscriptas que fueron contactadas por los responsables inscripción de cada lugar').': '.$periodo_mostrar.' ('.__('el porcentaje siempre debería estar por encima del 95%').')'
                ];
              $modal = [
                'id' => 'modal-contactados',
                'titulo' => __('Contactados').' | '.$periodo_mostrar,
                ];
              $info = [
                'periodo_mostrar' => $periodo_mostrar,
                'recomendacion_buen_rendimiento' => __('el porcentaje siempre debería estar por encima del 95%'),
                'como_mejorar' => __('Recordar a los responsables de inscripción, la necesidad de contactar a todas las personas que se inscriben, no deben quedar personas en la lista de inscriptos sin contactar, una posible solución a esto es revisar las campañas que han tenido bajo rendimiento de contacto y sugerir una actualización en Capacitación al responsable de inscripción')
                ];
              $columnas = [
                'titulos' => [
                  __('Pais'), 
                  __('Inscripciones'), 
                  '% '.__('Contactados')
                  ], 
                'campos' => [
                  'pais',
                  'cant_inscriptos',
                  'campo_para_usar'
                  ],
                'columna_orden' => 'campo_para_usar'
                ];
              $ranking = $Inscripciones;
              $valores_color = [
                'warning' => [90, 95],
                'danger' => [0, 90]
                ];

                //dd(1111);
              App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
              ?>
            <!-- CONTACTADOS --> 

            <!-- CONFIRMADOS -->
              <?php 
              $Inscripciones = $Inscripciones->each(function ($item, $key) {
                  $item->campo_para_usar = $item->cant_confirmo*100/$item->cant_inscriptos;
              });

              $widget = [
                'cant_top' => 3,
                'icono' => 'fa fa-check',
                'titulo' => __('Confirmados'),
                'tooltip' => __('Personas inscriptas a una conferencia o curso que despues de ser contactados confirmaron su asistencia al evento').': '.$periodo_mostrar.' ('.__('un porcentaje óptimo debería estar por encima del 70%').')'
                ];
              $modal = [
                'id' => 'modal-confirmados',
                'titulo' => __('Confirmados').' | '.$periodo_mostrar,
                ];
              $info = [
                'periodo_mostrar' => $periodo_mostrar,
                'recomendacion_buen_rendimiento' => __('un porcentaje óptimo debería estar por encima del 70%'),
                'como_mejorar' => __('Es muy importante que cuando una persona se inscriba se la contacte dentro de las 2 o 3 horas siguientes, de no ser asi, las personas en un gran porcentaje pierden el interes de asistir, y por lo tanto caen los porcentajes de confirmación al evento. Cuando una persona se inscribe, y recibe rápidamente un mensaje de pedido de confirmación normalmente confirma su asistencia, y esto se traduce en mas altos porcentajes de asistencia al evento, este paso en el proceso de inscripción es fundamental y decisivo en el éxito de la convocatoria al evento y no debe descuidarse. El responsable de inscripción que haga esta tarea debe ser una persona con disponibilidad horaria para estar atento a las nuevas inscripcioones y contactarlas lo mas rápidamente. Una posible solución a esto es revisar las campañas que han tenido bajo rendimiento de confirmación y sugerir al responsable de inscripción que atienda con mayor rapidez las inscripciones o buscar alguna otra persona con mejor disponibilidad de tiempo para esto.')
                ];
              $columnas = [
                'titulos' => [
                  __('Pais'), 
                  __('Inscripciones'), 
                  '% '.__('Confirmados')
                  ], 
                'campos' => [
                  'pais',
                  'cant_inscriptos',
                  'campo_para_usar'
                  ],
                'columna_orden' => 'campo_para_usar'
                ];
              $ranking = $Inscripciones;
              $valores_color = [
                'warning' => [55, 70],
                'danger' => [0, 55]
                ];


              //App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
              ?>
            <!-- CONFIRMADOS --> 

            <!-- VOUCHER -->
              <?php 
              $Inscripciones = $Inscripciones->each(function ($item, $key) {
                  $item->campo_para_usar = $item->cant_confirmo*100/$item->cant_confirmo;
              });

              $widget = [
                'cant_top' => 3,
                'icono' => 'fa fa-ticket',
                'titulo' => __('Voucher enviado'),
                'tooltip' => __('Personas confirmadas a una conferencia o curso a las que se les ha enviado el voucher').': '.$periodo_mostrar.' ('.__('un porcentaje óptimo debe ser el 100%').')'
                ];
              $modal = [
                'id' => 'modal-voucher',
                'titulo' => __('Voucher enviado').' | '.$periodo_mostrar,
                ];
              $info = [
                'periodo_mostrar' => $periodo_mostrar,
                'recomendacion_buen_rendimiento' => __('un porcentaje óptimo debe ser el 100%'),
                'como_mejorar' => __('Es muy importante que cuando una persona ha confirmado su asistencia se le envie el voucher, esto hace que la persona le de mayor valor a la propuesta, de que persiva una seriedad y organización importante y comprenda que el pedido de confirmación que le hemos hecho es algo serio y real, y que el lugar lo tendrá reservado para ella, por lo tanto si no asistira luego por alguna razón, la persona se comunica normalmente para avisar esto y ceder su lugar a otra persona. Una posible solución a esto es revisar las campañas que han tenido bajo porcentaje de envio de voucher y comunicar al responsable de inscripción lo importante de enviar los voucher a todas las personas confirmadas.')
                ];
              $columnas = [
                'titulos' => [
                  __('Pais'), 
                  __('Inscripciones'), 
                  '% '.__('Voucher enviado')
                  ], 
                'campos' => [
                  'pais',
                  'cant_confirmo',
                  'campo_para_usar'
                  ],
                'columna_orden' => 'campo_para_usar'
                ];
              $ranking = $Inscripciones;
              $valores_color = [
                'warning' => [91, 98],
                'danger' => [0, 91]
                ];


              //App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
              ?>
            <!-- VOUCHER --> 

            <!-- MOTIVACION -->
              <?php 
              $Inscripciones = $Inscripciones->each(function ($item, $key) {
                  $item->campo_para_usar = $item->cant_motivacion*100/$item->cant_confirmo;
              });

              $widget = [
                'cant_top' => 3,
                'icono' => 'fa fa-smile-o',
                'titulo' => __('Motivación enviada'),
                'tooltip' => __('Personas confirmadas a las que se les envio algun tipo de material motivacional previo al evento').': '.$periodo_mostrar.' ('.__('un porcentaje óptimo debe ser el 100%').')'
                ];
              $modal = [
                'id' => 'modal-motivacion',
                'titulo' => __('Motivación enviada').' | '.$periodo_mostrar,
                ];
              $info = [
                'periodo_mostrar' => $periodo_mostrar,
                'recomendacion_buen_rendimiento' => __('un porcentaje óptimo debe ser el 100%'),
                'como_mejorar' => __('Es muy importante que cuando una persona ha confirmado su asistencia y luego de que se le ha enviado el voucher se le envie uno o dos días antes al evento el material de motivación, este envio tiene por objetivo recordar indirectamente el evento para que organice su agenday disponibilidad para el dia del evento y despertar en la persona el entusiasmo por asistir al evento a través del contenido que se le suministra. Una posible solución a esto es revisar las campañas que han tenido bajo porcentaje de envio de motivación y concientizar al responsable de inscripción acerca de la  importancia de enviar los voucher a todas las personas confirmadas.')
                ];
              $columnas = [
                'titulos' => [
                  __('Pais'), 
                  __('Confirmados'), 
                  '% '.__('Voucher enviado')
                  ], 
                'campos' => [
                  'pais',
                  'cant_confirmo',
                  'campo_para_usar'
                  ],
                'columna_orden' => 'campo_para_usar'
                ];
              $ranking = $Inscripciones;
              $valores_color = [
                'warning' => [91, 98],
                'danger' => [0, 91]
                ];


              //App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
              ?>
            <!-- MOTIVACION --> 
          </div>

          <div class="col-lg-12 col-xs-12">
            <!-- RECORDATORIO -->
              <?php 
              $Inscripciones = $Inscripciones->each(function ($item, $key) {
                  $item->campo_para_usar = $item->cant_recordatorio*100/$item->cant_confirmo;
              });

              $widget = [
                'cant_top' => 3,
                'icono' => 'fa fa-bell-o',
                'titulo' => __('Envio de Recordatorio'),
                'tooltip' => __('Personas a las que se les envio el recordatorio para que asistan al inicio del curso o conferencia inicial').': '.$periodo_mostrar.' ('.__('un porcentaje óptimo debe ser el 100%').')'
                ];
              $modal = [
                'id' => 'modal-motivacion',
                'titulo' => __('Motivación enviada').' | '.$periodo_mostrar,
                ];
              $info = [
                'periodo_mostrar' => $periodo_mostrar,
                'recomendacion_buen_rendimiento' => __('un porcentaje óptimo debe ser el 100%'),
                'como_mejorar' => __('Es muy importante que a toda persona que ha confirmado su asistencia se le envie el mensaje recordatorio del inicio del curso o conferencia, el envio debe hacerse en las horas de la mañana para que la persona pueda organizar su día teniendo en cuenta el evento al que se ha inscripto y ha confirmado su asistencia. Este paso es fundamental y decisivo en el éxito de la convocatoria al evento y no debe descuidarse. Una posible solución a esto es revisar las campañas que han tenido bajo porcentaje de envio de recordatorio y solicitar al responsable de inscripción que no descuide la tarea en próximas inscripciones, ya que las estadísicas nos indican de que cuando los recordatorios no se envian correctamente, los porcentajes de asistencia, caen al 15% o menos.')
                ];
              $columnas = [
                'titulos' => [
                  __('Pais'), 
                  __('Confirmados'), 
                  '% '.__('Envio de Recordatorio')
                  ], 
                'campos' => [
                  'pais',
                  'cant_confirmo',
                  'campo_para_usar'
                  ],
                'columna_orden' => 'campo_para_usar'
                ];
              $ranking = $Inscripciones;
              $valores_color = [
                'warning' => [91, 98],
                'danger' => [0, 91]
                ];


              //App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
              ?>
            <!-- RECORDATORIO --> 

            <!-- ASISTIO -->
              <?php 
              $Inscripciones = $Inscripciones->each(function ($item, $key) {
                  $item->campo_para_usar = $item->cant_asistio*100/$item->cant_inscriptos;
              });

              $widget = [
                'cant_top' => 3,
                'icono' => 'fa fa-qrcode',
                'titulo' => __('Asistentes'),
                'tooltip' => __('Personas que asistieron al inicio del curso o conferencia inicial').': '.$periodo_mostrar.' ('.__('el porcentaje siempre debería estar por encima del 50% del total de inscriptos').')'
                ];
              $modal = [
                'id' => 'modal-asistio',
                'titulo' => __('Asistentes').' | '.$periodo_mostrar,
                ];
              $info = [
                'periodo_mostrar' => $periodo_mostrar,
                'recomendacion_buen_rendimiento' => __('el porcentaje siempre debería estar por encima del 50% del total de inscriptos'),
                'como_mejorar' => __('Los porcentajes bajos de asistencia pueden deberse a distintos factores, enumeramos a los mas comunes para que en función de esto se analicen las campañas con rendimientos bajos de asistencia y se tomen las acciones necesarias para su correción. 1) No se enviaron los recordatorios, o se enviaron tarde. 2) No se registro la asistencia en el Sistema AC, es decir no se leyo el codigo QR del voucher ni tampoco se utilizó la lista de asistencias del sistema para registrar los asistentes. 3) Condiciones climáticas desfavorables el día del evento. 4) El evento se ha realizado en un lugar de dificil acceso o no apropiado para la asistencia masiva')
                ];
              $columnas = [
                'titulos' => [
                  __('Pais'), 
                  __('Inscriptos'), 
                  '% '.__('Asistentes')
                  ], 
                'campos' => [
                  'pais',
                  'cant_inscriptos',
                  'campo_para_usar'
                  ],
                'columna_orden' => 'campo_para_usar'
                ];
              $ranking = $Inscripciones;
              $valores_color = [
                'warning' => [45, 50],
                'danger' => [0, 45]
                ];


              //App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
              ?>
            <!-- ASISTIO --> 

            <!-- ASISTIO -->
              <?php 
              $Inscripciones = $Inscripciones->each(function ($item, $key) {
                  $item->campo_para_usar = $item->cant_recordatorio_prox*100/$item->cant_confirmo;
              });

              $widget = [
                'cant_top' => 3,
                'icono' => 'fa fa-bell',
                'titulo' => __('Recordatorio Próx clase'),
                'tooltip' => __('Personas a las que se les envio el recordatorio para que asistan a la segunda clase').': '.$periodo_mostrar.' ('.__('un porcentaje óptimo debe ser el 100%').')'
                ];
              $modal = [
                'id' => 'modal-recordatorio-prox',
                'titulo' => __('Recordatorio Próx clase').' | '.$periodo_mostrar,
                ];
              $info = [
                'periodo_mostrar' => $periodo_mostrar,
                'recomendacion_buen_rendimiento' => __('un porcentaje óptimo debe ser el 100%'),
                'como_mejorar' => __('Es muy importante que a toda persona que ha confirmado su asistencia se le envie el mensaje recordatorio para las clases siguientes, la persona normalmente al no tener incorporada en su rutina el curso, no recuerda muchas veces que debe asistir a la próxima clase, por eso es muy importante que el responsable de inscripción envie a todas las personas confirmadas un recordatorio posterior al inicio de los cursos. Una posible solución a esto es revisar las campañas que han tenido bajo porcentaje de envio de recordatorio a la próxima clase, y solicitar al responsable de inscripción que no descuide esta acción en próximas inscripciones.')
                ];
              $columnas = [
                'titulos' => [
                  __('Pais'), 
                  __('Confirmados'), 
                  '% '.__('Recordatorio Próx clase')
                  ], 
                'campos' => [
                  'pais',
                  'cant_confirmo',
                  'campo_para_usar'
                  ],
                'columna_orden' => 'campo_para_usar'
                ];
              $ranking = $Inscripciones;
              $valores_color = [
                'warning' => [45, 50],
                'danger' => [0, 45]
                ];


              //App::make('App\Http\Controllers\DashboardController')->modalRanking($widget, $modal, $info, $columnas, $ranking, $valores_color);
              ?>
            <!-- ASISTIO -->
          </div>


        <?php } 
        else { ?>

          <div class="col-lg-12 col-xs-12">
            <div class="alert alert-info alert-dismissible">
              <h4><i class="icon fa fa-info"></i> <?php echo __('Inscripciones') ?></h4>
              <?php echo __('No hemos encontrado inscripciones para') ?>: <?php echo $periodo_mostrar ?>
            </div> 
          </div>      
        <?php } ?>

      <?php } 
      else { ?>

        <div class="col-lg-12 col-xs-12">
          <div class="alert alert-info alert-dismissible">
            <h4><i class="icon fa fa-info"></i> <?php echo __('Campañas') ?></h4>
            <?php echo __('No hemos encontrado campañas para') ?>: <?php echo $periodo_mostrar ?>
          </div> 
        </div>      
      <?php } ?>



    <?php } ?>

  </section>



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

        <?php if (isset($home)) { ?>
          //startDate: moment().subtract(29, 'days'),
          //endDate  : moment()
        <?php } 
        else { ?>
          startDate: moment("<?php echo $periodo[0] ?>", "YYYY-MM-DD"),
          endDate: moment("<?php echo $periodo[1] ?>", "YYYY-MM-DD")
        <?php } ?>


      },
      function (start, end) {
        $('#daterange-btn').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
        $('#periodo').val(start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD'))
        $('#periodo_mostrar').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
        $( "#form_ranking" ).submit();
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
        //$("#contenidodash").html(data);

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

    <br>
@endsection
