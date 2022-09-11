<?php 
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;
?>

<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/buttons_datatables/buttons.dataTables.min.css">

    <!-- Content Header (Page header) -->

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
              <table id="lista-de-solicitudes" class="table table-hover" style="max-width: 500px" >
                <thead>
                  <tr>
                    <th><?php echo __('Planilla de Inscripción') ?></th>
                    <th><?php echo __('Inscripcion') ?> <?php echo __('ID') ?></th>
                    <th><?php echo __('Codigo de alumno') ?></th>
                    <th><?php echo __('Solicitud') ?> <?php echo __('ID') ?></th>
                    <th><?php echo __('Solicitud') ?> <?php echo __('ID') ?> (<?php echo __('Original') ?>)</th>
                    <th><?php echo __('Nombre') ?></th>
                    <th><?php echo __('Apellido') ?></th>
                    <th><?php echo __('Celular') ?></th>
                    <th><?php echo __('Correo') ?></th>
                    <th><?php echo __('Idioma') ?></th>
                    <th><?php echo __('Pais') ?> <?php echo __('Inscripcion') ?></th>
                    <th><?php echo __('Pais') ?> <?php echo __('Solicitud') ?></th>
                    <th><?php echo __('Ciudad') ?> <?php echo __('Inscripcion') ?></th>
                    <th><?php echo __('Ciudad') ?> <?php echo __('Solicitud') ?></th>
                    <th><?php echo __('Fecha') ?> <?php echo __('Inscripcion') ?></th>
                    <th><?php echo __('Ultima leccion vista') ?></th>
                    <th><?php echo __('Grupo') ?></th>
                    <th><?php echo __('Cancelo') ?></th>
                    <th><?php echo __('Causa de baja') ?></th>
                    <th><?php echo __('Responsable de inscripcion o tutor') ?></th>
                  </tr>
                </thead>
                <tbody>                  
                  <?php foreach ($Inscripciones as $Inscripcion) { ?>
                    <tr>
                      <td><a href="<?php echo env('PATH_PUBLIC')?>f/iinscripto/<?php echo $Inscripcion->solicitud_id ?>/<?php echo $Inscripcion->id ?>/<?php echo $Inscripcion->hash ?>" target="_blank"><button type="button" class="btn btn-sm btn-primary"><i class="fa fa-file-text-o"></i></button></td>
                      <td><?php echo $Inscripcion->codigo_alumno ?></td>
                      <td><?php echo $Inscripcion->solicitud_id ?></td>
                      <td><?php echo $Inscripcion->solicitud_original ?></td>
                      <td>
                        <?php echo $Inscripcion->solicitud_original ?>
                        <?php 
                        if ($Inscripcion->causa_de_cambio_de_solicitud <> '') {
                          echo '('.$Inscripcion->causa_de_cambio_de_solicitud.')';
                        }
                        ?>
                      </td>
                      <td><?php echo $Inscripcion->nombre ?></td>
                      <td><?php echo $Inscripcion->apellido ?></td>
                      <td><?php echo $Inscripcion->celular ?></td>
                      <td><?php echo $Inscripcion->email_correo ?></td>
                      <td><?php echo $Inscripcion->idioma ?></td>
                      <td><?php echo $Inscripcion->pais_inscripcion ?></td>
                      <td><?php echo $Inscripcion->pais_solicitud ?></td>
                      <td><?php echo $Inscripcion->ciudad ?></td>
                      <td><?php echo $Inscripcion->localidad ?></td>
                      <td><?php echo $gCont->FormatoFecha($Inscripcion->created_at) ?></td>
                      <td><?php echo $Inscripcion->nombre_de_la_leccion ?></td>
                      <td><?php echo $Inscripcion->grupo ?></td>
                      <td><?php echo $Inscripcion->sino_cancelo ?></td>
                      <td><?php echo $Inscripcion->causa_de_baja ?></td>
                      <td><?php echo $Inscripcion->nombre_responsable_de_inscripciones ?> <?php echo $Inscripcion->celular_responsable_de_inscripciones ?> </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
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


<script src="<?php echo env('PATH_PUBLIC')?>bower_components/buttons_datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/buttons_datatables/buttons.flash.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/buttons_datatables/jszip.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/buttons_datatables/pdfmake.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/buttons_datatables/vfs_fonts.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/buttons_datatables/buttons.html5.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/buttons_datatables/buttons.print.min.js"></script>

<script>

  $(function () {
    $('#lista-de-solicitudes').DataTable({
        'dom': 'Bfrtip',
        'buttons': [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        'searching': true,
        'autoWidth': true,
        'pageLength': 100, 
        'paging': true,
        'order': [[ 1, 'asc' ]],
        'columnDefs': [
          { width: "100px", targets: 0 },
          { className: "table_td_condensada", targets: "_all" },
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


