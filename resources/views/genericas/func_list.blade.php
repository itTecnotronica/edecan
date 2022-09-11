<?php 

if ($_SERVER['HTTP_HOST'] == 'localhost:1010' or $_SERVER['HTTP_HOST'] == 'ac.gnosis.is') {
  $path_public = env('PATH_PUBLIC');
}
else {
  $path_public = 'https://'.$_SERVER['HTTP_HOST'].'/';  
}

?>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo env('API_KEY_GOOGLE_MAPS')?>&libraries=places">
    </script>

<?php
if (isset($gen_seteo['gen_campos_a_ocultar'])) {
  $gen_campos_a_ocultar = $gen_seteo['gen_campos_a_ocultar'];
}
else {
  $gen_campos_a_ocultar = [];
}


$mostrar_titulo = 'SI';
if (isset($gen_seteo['mostrar_titulo'])) {
   $mostrar_titulo = $gen_seteo['mostrar_titulo'];
}

$titulo = $gen_nombre_tb_mostrar;
if (isset($gen_seteo['titulo'])) {
   $titulo = $gen_seteo['titulo'];
}

// TABLA CONDENSADA
$class_td = '';
$class_btn_accion = '';
if (isset($gen_seteo['tabla_condensada']) and $gen_seteo['tabla_condensada'] == 'SI') {
  $class_td = 'table_td_condensada';
  $class_btn_accion = 'btn-xs';
}


// SETEO OPCIONES DE TABLA
$table_searching = 'true';
$table_pageLength = 10;
$table_paging = 'true';
$table_contenedor = 'SI';

if (isset($gen_seteo['table'])) {
  $table = $gen_seteo['table'];

  // BUSQUEDA DE TABLA
  if (isset($table['searching'])) {
    $table_searching = $table['searching'];
  }

  // CANT FILAS POR PAGINACION
  if (isset($table['pageLength'])) {
    $table_pageLength = $table['pageLength'];
  }

  // PAGINACION
  if (isset($table['paging'])) {
    $table_paging = $table['paging'];
  }

  // CONTENEDOR
  if (isset($table['contenedor'])) {
    $table_contenedor = $table['contenedor'];
  }

}
else {
  $gen_campos_a_ocultar = [];
}


//var_dump($gen_campos_a_ocultar);

$titulo_formABM = str_replace('_', ' ', $gen_modelo);

if ($titulo_formABM == 'User') {
  $titulo_formABM = 'Usuario';
}

$random_id_table = rand(0, 9999999);

?>

    <!-- Content Header (Page header) -->
    <?php if ($mostrar_titulo == 'SI') { ?>
    <section class="content-header">
      <h1>
        <?php echo $titulo; ?>
      </h1>
    </section>
    <?php } ?>

    <?php if ($table_contenedor == 'SI') { ?>
    <!-- Main content -->
    <section class="content">
      <div class="row">

        <div class="col-xs-12" style="overflow: auto">

          <div class="box" style="display: inline-table;">
            <!-- /.box-header -->
            <div class="box-body">
    <?php } ?>
              <table id="table_<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>" class="table table-bordered table-striped" style="max-width: 500px" >
                <thead>
                <tr>
                    <th><?php echo __('Acción'); ?></th>
                  <?php
                  foreach ($gen_campos as $campo) {
                    if (!in_array($campo['nombre'], $gen_campos_a_ocultar)) {
                  ?>
                    <th><?php echo $campo['nombre_a_mostrar']; ?></th>
                  <?php
                    }
                  }
                  ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($gen_filas as $gen_fila) { ?>
                <tr>
                    <td>
                      <div class="btn-group">
                        <?php if (in_array("U", $gen_permisos)  and $gen_fila['sino_restringir_mod'] <> 'SI') { ?>
                        <button type="button" class="btn btn-info <?php echo $class_btn_accion ?>" onclick="crearABM_<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>('m', <?php echo $gen_fila['id']; ?>)" data-toggle="modal" data-target="#modal-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                        <!--button type="button" class="btn btn-info" onclick="crearABM('m', <?php echo $gen_fila[$gen_campos[0]['nombre']]; ?>)" data-toggle="modal" data-target="#modal-<?php echo $gen_modelo ?>" alt="editar" title="editar"><i class="fa fa-pencil"></i></button-->
                        <?php } ?>
                        <?php if (in_array("D", $gen_permisos) and $gen_fila['sino_restringir_del'] <> 'SI') { ?>
                        <button type="button" class="btn btn-info <?php echo $class_btn_accion ?>" onclick="crearABM_<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>('b', <?php echo $gen_fila['id']; ?>)" data-toggle="modal" data-target="#modal-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>" alt="eliminar" title="eliminar"><i class="fa fa-remove"></i></button>
                        <?php } ?>
                        <?php
                        if ($acciones_extra <> '') {
                          foreach ($acciones_extra as $accion_extra) {
                            $accion_extra_array = explode(',', $accion_extra);
                            if (count($accion_extra_array) > 0) {
                              $accion_extra_nombre = $accion_extra_array[0];
                              $accion_extra_class = $accion_extra_array[1];
                              $accion_extra_url = $accion_extra_array[2];
                        ?>
                       <button type="button" class="btn btn-info" onclick="window.location = '<?php echo $path_public.$accion_extra_url.'/'.$gen_fila[$gen_campos[0]['nombre']]; ?>'" alt="<?php echo $accion_extra_nombre; ?>" title="<?php echo $accion_extra_nombre; ?>"><i class="<?php echo $accion_extra_class; ?>"></i></button>
                        <?php
                            }
                          }
                        }
                        ?>
                      </div>
                    </td>
                  <?php
                  foreach ($gen_campos as $campo) {
                    if (!in_array($campo['nombre'], $gen_campos_a_ocultar)) {
                  ?>
                    <td>
                      <?php
                      $rel_tb = $gen_fila[$campo['rel_tb']];
                      if ($rel_tb == '') {
                        $valor = $gen_fila[$campo['nombre']];
                        $nombre = $campo['nombre'];
                        $tipo = $campo['tipo'];
                        //$valor_a_mostrar = '';
                        $valor_a_mostrar = App::make('App\Http\Controllers\GenericController')->mostrarValorCampo($nombre, $valor, $tipo);
                      }
                      else {
                        $gen_modelo_rel = strtolower($campo['rel_modelo']);
                        if ($campo['rel_campo_descripcion'] == 'descrip_modelo()') {
                          if($gen_fila->$gen_modelo_rel <> '') {
                            $valor_a_mostrar = $gen_fila->$gen_modelo_rel->descrip_modelo();
                          }
                          else {
                            $valor_a_mostrar = '';
                          }

                        }
                        else {
                          $rel_campo_descripcion = $campo['rel_campo_descripcion'];
                          //dd($gen_fila->tipo_de_documento);
                          if (isset($gen_fila->$gen_modelo_rel->$rel_campo_descripcion)) {
                            $valor_a_mostrar = $gen_fila->$gen_modelo_rel->$rel_campo_descripcion;
                          }
                          else {
                            $valor_a_mostrar = '';
                          }
                        }
                        //echo $gen_fila->$gen_modelo_rel."-----".$gen_modelo_rel;
                      }
                      echo $valor_a_mostrar;
                       ?>
                    </td>
                  <?php
                    }
                  }
                  ?>
                </tr>
                <?php } ?>
                </tfoot>
              </table>

      <?php if ($table_contenedor == 'SI') { ?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      <?php } ?>

        <div class="col-xs-3">
          <?php if (in_array("C", $gen_permisos)) { ?>
          <button type="button" class="btn btn-block btn-info col-xs-3 <?php echo $class_btn_accion ?>" data-toggle="modal" data-target="#modal-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>" onclick="crearABM_<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>('a', -1)"><i class="fa fa-plus"></i> <?php echo __('Agregar') ?></button>
          <?php } ?>
        </div>

    <?php if ($table_contenedor == 'SI') { ?>
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    <?php } ?>

<!-- DataTables -->

<script>
  $(function () {
    $('#table_<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>').DataTable({
        'searching': <?php echo $table_searching ?>,
        'autoWidth': true,
        'pageLength': <?php echo $table_pageLength ?>,
        'paging': <?php echo $table_paging ?>,
        'order': [[ 1, 'asc' ]],
        'columnDefs': [
          { width: "100px", targets: 0 },
          <?php if ($class_td <> '') { ?>
          { className: "<?php echo $class_td ?>", targets: "_all" },
          <?php } ?>
        ],
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
</script>


        <div class="modal modal fade" id="modal-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="modal-titulo-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>">Info Modal</div></h4>
              </div>
              <div class="modal-body" id="modal-bodi-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>">

              </div>

            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->


<script type="text/javascript">

function crearABM_<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>(gen_accion, gen_id = null) {
    $.ajax({
      url: '<?php echo $path_public ?>crearabm',
      type: 'POST',
      dataType: 'html',
      async: true,
      data:{
        _token: "{{ csrf_token() }}",
        gen_modelo: '<?php echo $gen_modelo ?>',
        gen_seteo: '<?php echo serialize($gen_seteo) ?>',
        gen_opcion: '<?php echo $gen_opcion ?>',
        gen_accion: gen_accion,
        gen_id: gen_id
      },
      success: function success(data, status) {
        console.log(11)
        $("#modal-bodi-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>").html(data);
        if (gen_accion == 'a') {
          $("#modal-titulo-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>").html('Insertar <?php echo __($titulo_formABM) ?>');
        }
        if (gen_accion == 'm') {
          $("#modal-titulo-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>").html('Modificar <?php echo __($titulo_formABM) ?>');
        }
        if (gen_accion == 'b') {
          $("#modal-titulo-<?php echo $gen_modelo ?>_<?php echo $random_id_table ?>").html('Borrar <?php echo __($titulo_formABM) ?>');
        }

      },
      error: function error(xhr, textStatus, errorThrown) {
          alert(errorThrown);
      }
    });
}





</script>

