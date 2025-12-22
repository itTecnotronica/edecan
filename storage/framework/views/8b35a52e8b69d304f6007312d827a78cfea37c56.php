<?php

use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;

$ranking_ok = $ranking->sortByDesc($columnas['campos'][2]);
$ranking_ok = $ranking_ok->take($widget['cant_top']);
$ranking_ok = $ranking_ok->values()->all();


$col_0 = $columnas['campos'][0];
$col_1 = $columnas['campos'][1];
$col_2 = $columnas['campos'][2];
$col_orden = $columnas['columna_orden'];

if (isset($widget['titulo_1'])) {
  $titulo_1 = $widget['titulo_1'];
}
else {
  $titulo_1 = '1° '.$ranking_ok[0]->$col_0;
}

if (isset($widget['titulo_2'])) {
  $titulo_2 = $widget['titulo_2'];
}
else {
  $titulo_2 = $ranking_ok[0]->$col_2.'%';
}

if (isset($widget['valor_porc'])) {
  $valor_porc = $widget['valor_porc'];
}
else {
  $valor_porc = $ranking_ok[0]->$col_2;
}

if (isset($widget['descripcion'])) {
  $descripcion = $widget['descripcion'];
}
else {
  $descripcion = "de ".$ranking_ok[0]->$col_1.' '.$columnas['titulos'][1];
}




?>
<div class="col-md-3 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
  <div class="bg-purple-active color-palette" style="width: 100%;">
    <div class="info-box-content">
      <span class="info-box-text"><?php echo $widget['titulo'] ?> </span>
    </div>
  </div>
  <div class="info-box bg-gray" data-toggle="tooltip" data-original-title="<?php echo $widget['tooltip'] ?>" style="margin-bottom: 0px;">
    <span class="info-box-icon"><i class="<?php echo $widget['icono'] ?>"></i></span>

    <div class="info-box-content">
      <span class="info-box-text"><?php echo $titulo_1 ?> </span>
      <span class="info-box-number"><?php echo $titulo_2 ?></span>

    <div class="progress">
      <div class="progress-bar" style="width: <?php echo round($valor_porc) ?>%"></div>
    </div>   
    <span class="progress-description">
      <?php echo $descripcion ?>
    </span>   
    </div>
    <!-- /.info-box-content -->      
  </div>          
  <?php 
  $i = 0;
  foreach ($ranking_ok as $fila) { 
    $i++;
  ?> 
    <div class="bg-gray-active color-palette" style="width: 100%; border-bottom: 1px; border-bottom-style: solid; border-bottom-color: grey">
      <div class="info-box-content">
        <span class="info-box-text"><?php echo $i ?>° <?php echo $fila->$col_0 ?> <?php echo $gCont->formatoNumero($fila->$col_2, 'decimal') ?>% </span>
      </div>
    </div>
  <?php } ?>  
  <div class="btn btn-block btn-lista-completa"  data-toggle="modal" data-target="#<?php echo $modal['id'] ?>">
    <span class="info-box-text"><?php echo __('Lista Completa') ?> </span>
  </div>
  <!-- /.info-box -->
</div>


<!-- MODAL <?php echo $modal['titulo'] ?> -->
  <div class="modal fade" id="<?php echo $modal['id'] ?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><?php echo $modal['titulo'] ?></h4>
        </div>
        <div class="modal-body">        
          <div class="alert alert-info alert-dismissible">
            <h4><i class="icon fa fa-info"></i> <?php echo __('Recomendación de buen rendimiento: ') ?></h4>
            <?php echo $info['recomendacion_buen_rendimiento'] ?>
          </div>           
          <table class="table table-condensed table-hover">
            <tbody>
              <tr>
                <?php if (isset($modal['enlace'])) { ?>
                  <th></th>
                <?php } ?>
                <th style="width: 10px">#</th>
                <?php foreach ($columnas['titulos'] as $columna) { ?>  
                <th><?php echo $columna ?></th>
                <?php } ?>
              </tr>
              <?php
              $ranking_ok = $ranking->sortByDesc($col_orden);
              $ranking_ok = $ranking_ok->values()->all();
              
              $porc_danger = [0, 90];
              $i=0;
              foreach ($ranking_ok as $fila) { 
                $class_fila = App::make('App\Http\Controllers\DashboardController')->classColor($valores_color['warning'], $valores_color['danger'], $fila->$col_orden);
              $i++;
              ?>                      
              <tr class="<?php echo $class_fila ?>">
                <?php 
                if (isset($modal['enlace'])) { 
                  $col_enlace = $modal['enlace'];
                ?>
                  <td><a href="<?php echo $fila->$col_enlace ?>" target="_blank">
                    <button type="button" class="btn btn-xs btn-default" alt="editar" title="editar"><i class="fa fa-search"></i></button>
                  </a></td>
                <?php } ?>
                <td><?php echo $i ?>°</td>
                <?php 
                $j = 0;
                foreach ($columnas['campos'] as $columna) { 
                $j++;
                ?> 
                <td>
                  <?php 
                  if ($j == count($columnas['campos'])) { 
                    echo $gCont->formatoNumero($fila->$columna, 'decimal').'%';
                  }
                  else {
                    echo $fila->$columna;
                  }
                  ?>                  
                </td>
                <?php } ?>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          <strong><?php echo __('Referencias: ') ?></strong><br>
          <span class="badge bg-green color-palette txt-referencia"><?php echo __('Excelente rendimiento') ?></span><br>
          <span class=" badge bg-yellow color-palette txt-referencia"><?php echo __('Mejorar un poco') ?></span><br>
          <span class=" badge bg-red color-palette txt-referencia"><?php echo __('Estado Crítico, se debe mejorar bastante') ?></span><br><br>
          <strong><?php echo __('Como mejorar: ') ?></strong><br>
          <?php echo $info['como_mejorar'] ?>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<!-- MODAL <?php echo $modal['titulo'] ?> -->