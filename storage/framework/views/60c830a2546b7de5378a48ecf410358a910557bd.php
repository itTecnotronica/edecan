
      <!-- jQuery 3 -->
      <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>

      <!-- Bootstrap 3.3.7 -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/AdminLTE.min.css">
      <!-- AdminLTE Skins. Choose a skin from the css/skins


<!-- DataTables -->
<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


<!DOCTYPE html>
<html>
<head>
  <title>LISTADO DE CURSOS ONLINE ARGENTINA 04/2020</title>
</head>
<body>

<div class="container">
<div class="row">

<table id="table" class="table table-bordered table-striped" style="max-width: 500px" >
  <thead>
    <tr>
      <th v-show="show_col_id"><?php echo __('Pais') ?></th>
      <th v-show="show_col_id"><?php echo __('Provincia') ?></th>
      <th v-show="show_col_id"><?php echo __('Ciudad') ?></th>
      <th v-show="show_col_id"><?php echo __('Inscriptos') ?></th>
      <th v-show="show_col_id"><?php echo __('Enlace') ?></th>
    </tr>
  </thead>
  <tbody>

  
  <?php foreach ($Solicitudes as $Solicitud) { ?>
    <tr>
      <td><?php echo $Solicitud->pais ?></td>
      <td><?php echo $Solicitud->provincia ?></td>
      <td><?php echo $Solicitud->ciudad_sol ?></td>
      <td><?php echo $Solicitud->cant_inscriptos ?></td>
      <td><a href="<?php echo ENV('PATH_PUBLIC') ?>f/<?php echo $Solicitud->id ?>/<?php echo $Solicitud->hash ?>" target="_blank"><?php echo ENV('PATH_PUBLIC') ?>f/<?php echo $Solicitud->id ?>/<?php echo $Solicitud->hash ?></a></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
</div>
</div>

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


    <!-- jQuery 3 -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-ui/jquery-ui.min.js"></script>

    <!-- DataTables -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>


</body>
</html>