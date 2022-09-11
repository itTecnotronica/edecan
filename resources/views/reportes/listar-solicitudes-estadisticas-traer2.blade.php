<?php
$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;
?>


@extends('layouts.backend')



@section('contenido')

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
            <div class="box-body">
              <table id="lista-de-solicitudes" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th><?php echo __('ID') ?></th>
                    <th><?php echo __('Tipo de evento') ?></th>
                    <th><?php echo __('Título de la Conferencia Pública') ?></th>
                    <th><?php echo __('Provincia') ?></th>
                    <th><?php echo __('Ciudad') ?></th>
                    <th><?php echo __('importe') ?></th>
                    <th><?php echo __('Inscriptos') ?></th>
                    <th><?php echo __('Contactados') ?></th>
                    <th><?php echo __('Confirmados') ?></th>
                    <th><?php echo __('Voucher') ?></th>
                    <th><?php echo __('Motivacion') ?></th>
                    <th><?php echo __('Recordatorio') ?></th>
                    <th><?php echo __('Asistentes') ?></th>
                    <th><?php echo __('Envio de Recordatorio a Próxima clase') ?></th>
                    <th><?php echo __('Canceló la inscripción') ?></th>
                    <th><?php echo __('Visualizaciones') ?></th>
                  </tr>
                </thead>
                <tbody>                  
                  <?php foreach ($Solicitudes as $Solicitud) { ?>
                    <tr>
                      <td><?php echo $Solicitud->id ?></td>
                      <td><?php echo $Solicitud->tipo_de_evento ?></td>
                      <td><?php echo $Solicitud->titulo_de_conferencia_publica ?></td>
                      <td><?php echo $Solicitud->provincia ?></td>
                      <td><?php echo $Solicitud->localidad ?></td>
                      <td><?php echo $Solicitud->importe ?></td>
                      <td><?php echo $Solicitud->cant_inscriptos ?></td>
                      <td><?php echo $Solicitud->cant_contactados ?></td>
                      <td><?php echo $Solicitud->cant_confirmo ?></td>
                      <td><?php echo $Solicitud->cant_voucher ?></td>
                      <td><?php echo $Solicitud->cant_motivacion ?></td>
                      <td><?php echo $Solicitud->cant_recordatorio ?></td>
                      <td><?php echo $Solicitud->cant_asistio ?></td>
                      <td><?php echo $Solicitud->cant_recordatorio_prox ?></td>
                      <td><?php echo $Solicitud->cant_cancelo ?></td>
                      <td><?php echo $Solicitud->cant_visualizaciones ?></td>                  
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


<script>

  $(function () {
    $('#lista-de-solicitudes').DataTable({
        'responsive': true,
        'searching': true,
        'autoWidth': true,
        'pageLength': 100, 
        'paging': true,
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

</script>


@endsection