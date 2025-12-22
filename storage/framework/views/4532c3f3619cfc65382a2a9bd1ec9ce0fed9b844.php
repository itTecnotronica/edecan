

<!-- jQuery 3 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap 3.3.7 -->
<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/AdminLTE.min.css">
<!-- AdminLTE Skins. Choose a skin from the css/skins -->


<!-- DataTables -->
<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Paises y Mapas por Pais</div>
                <div class="card-body">

                    <table id="table_condense" class="table table-bordered table-striped" >
                      <thead>
                        <tr>
                          <th v-show="show_col_id"><?php echo __('Pais') ?></th>
                          <th v-show="show_col_id"><?php echo __('Mapa de Sedes') ?></th>
                          <th v-show="show_col_id"><?php echo __('Listado de Sedes') ?></th>
                        </tr>
                      </thead>
                      <tbody>

                      
                      <?php foreach ($Paises as $Pais) { ?>
                        <tr>
                          <td><?php echo $Pais->pais ?></td>
                          <td><a href="<?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-sedes/<?php echo $Pais->id ?>" target="_blank"><?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-Paises/<?php echo $Pais->id ?></a></td>
                          <td><a href="<?php echo env('PATH_PUBLIC')?>lista-de-sedes/<?php echo $Pais->id ?>" target="_blank"><?php echo env('PATH_PUBLIC')?>lista-de-sedes/<?php echo $Pais->id ?></a></td>
                        </tr>
                      <?php } ?>
                      </tbody>
                    </table>

                          <!-- DataTables -->
                          <script>
                            $(function () {
                              $('#table').DataTable({
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
                                  'paging': false,
                                  'pageLength': 9999,
                                  'order': [[ 0, 'des' ]],
                                  'columnDefs': [{ "width": "100px", "targets": 0 }], 
                              })
                            })
                          </script>





                </div>
            </div>
        </div>
    </div>
</div>



<!-- jQuery 3 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-ui/jquery-ui.min.js"></script>


<!-- DataTables -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
