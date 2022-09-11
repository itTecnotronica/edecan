<?php 

use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;

use App\Http\Controllers\HomeController;
$HomeController = new HomeController();

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
                    <th><?php echo __('ID') ?></th>
                    <th><?php echo __('Estado') ?></th>
                    <th><?php echo __('Tipo de evento') ?></th>
                    <th><?php echo __('Título de la Conferencia Pública') ?></th>
                    <th><?php echo __('Fecha de solicitud') ?></th>
                    <th><?php echo __('Fecha de inicio') ?></th>
                    <th><?php echo __('Hora de inicio') ?></th>
                    <th><?php echo __('Provincia') ?></th>
                    <th><?php echo __('Ciudad') ?></th>
                    <th><?php echo __('Paypal') ?></th>
                    <th><?php echo __('Neto') ?></th>
                    <th><?php echo __('importe') ?></th>
                    <th><?php echo __('Saldo') ?></th>
                    <th><?php echo __('Observaciones') ?></th>
                    <th><?php echo __('Inscriptos') ?> <?php echo __('Fecha') ?></th>
                    <th><?php echo __('Inscriptos') ?> <?php echo __('Campaña') ?></th>
                    <th><?php echo __('Inscriptos') ?> <?php echo __('No pueden asistir') ?></th>
                    <th><?php echo __('Contactados') ?></th>
                    <th><?php echo __('Confirmados') ?></th>
                    <th><?php echo __('Voucher') ?></th>
                    <th><?php echo __('Motivacion') ?></th>
                    <th><?php echo __('Recordatorio') ?></th>
                    <th><?php echo __('Asistentes') ?></th>
                    <th><?php echo __('Envio de Recordatorio a Próxima clase') ?></th>
                    <th><?php echo __('Canceló la inscripción') ?></th>
                    <th><?php echo __('Visualizaciones') ?></th>
                    <th><?php echo __('Visualizaciones') ?>/<?php echo __('Inscriptos') ?> <?php echo __('Campaña') ?></th>
                  </tr>
                </thead>
                <tbody>                  
                  <?php foreach ($Solicitudes as $Solicitud) { ?>
                    <tr>
                      <td><?php echo $Solicitud->id ?></td>
                      <td>
                        <?php
                        $array_estado = $HomeController->estado($Solicitud->sino_aprobado_administracion, $Solicitud->sino_aprobado_solicitar_revision, $Solicitud->sino_cancelada, $Solicitud->sino_aprobado_finalizada);
                        $estado = $array_estado['estado'];
                        $class_estado = $array_estado['class_estado'];
                        $span_estado = $array_estado['span_estado'];
                        ?>                
                        <?php echo $span_estado; ?>
                      </td>
                      <td><?php echo $Solicitud->tipo_de_evento ?></td>
                      <td><?php echo $Solicitud->titulo_de_conferencia_publica ?></td>
                      <td><?php echo $gCont->FormatoFecha($Solicitud->fecha_de_solicitud) ?></td>
                      <td><?php echo $gCont->FormatoFecha($Solicitud->fecha_de_inicio) ?></td>
                      <td><?php echo $Solicitud->hora_de_inicio ?></td>
                      <td><?php echo $Solicitud->provincia ?></td>
                      <td><?php echo $Solicitud->localidad ?></td>
                      <td><?php echo $Solicitud->paypal_value ?></td>
                      <?php $paypal_neto = $Solicitud->paypal_value*(1-0.0479)-0.60; ?>
                      <td><?php echo $paypal_neto ?></td>
                      <td><?php echo $Solicitud->importe ?></td>
                      <td><?php echo $paypal_neto-$Solicitud->importe ?></td>
                      <td><?php echo $Solicitud->observaciones ?></td>
                      <td><?php echo $Solicitud->cant_inscriptos ?></td>
                      <td><?php echo $Solicitud->cant_inscriptos_total ?></td>
                      <td><?php echo $Solicitud->cant_inscriptos_sin_evento ?></td>
                      <td><?php echo $Solicitud->cant_contactados ?></td>
                      <td><?php echo $Solicitud->cant_confirmo ?></td>
                      <td><?php echo $Solicitud->cant_voucher ?></td>
                      <td><?php echo $Solicitud->cant_motivacion ?></td>
                      <td><?php echo $Solicitud->cant_recordatorio ?></td>
                      <td><?php echo $Solicitud->cant_asistio ?></td>
                      <td><?php echo $Solicitud->cant_recordatorio_prox ?></td>
                      <td><?php echo $Solicitud->cant_cancelo ?></td>
                      <td><?php echo $Solicitud->cant_visualizaciones ?></td>     
                      <td>
                        <?php 
                        if ($Solicitud->cant_inscriptos_total > 0) {
                          echo $gCont->formatoNumero($Solicitud->cant_visualizaciones/$Solicitud->cant_inscriptos_total, 'decimal'); 
                        }
                        else {
                          echo 0;
                        }
                        ?></td>                  
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


