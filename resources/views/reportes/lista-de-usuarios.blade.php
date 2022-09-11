<?php
$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;

$pais_id = Auth::user()->pais_id;

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
              <h3 class="box-title"><?php echo __('Lista de Usuarios') ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="overflow: auto">
              <table id="lista-de-usuarios" class="table table-hover" style="max-width: 500px" >
                <thead>
                  <tr>
                    <th><?php echo __('ID') ?></th>
                    <th><?php echo __('Nombre') ?></th>
                    <th><?php echo __('Email') ?></th>
                    <th><?php echo __('Celular') ?></th>
                    <th><?php echo __('Activo') ?></th>
                    <th><?php echo __('Ultimo Login') ?></th>
                    <th><?php echo __('Ultima Accion') ?></th>
                    <th><?php echo __('Lugar') ?></th>
                    <th><?php echo __('Equipo') ?></th>
                    <th><?php echo __('Funcion') ?></th>
                    <th><?php echo __('Observaciones') ?></th>
                  </tr>
                </thead>
                <tbody>                  
                  <?php 
                  foreach ($Usuarios as $Usuario) { 
                    ?>
                    <tr>
                      <td><?php echo $Usuario->id ?></td>
                      <td><?php echo $Usuario->name ?></td>
                      <td><?php echo $Usuario->email ?></td>
                      <td><?php echo $Usuario->celular ?></td>
                      <td><?php echo $Usuario->sino_activo ?></td>
                      <td><?php echo $Usuario->ultimo_login ?></td>
                      <td><?php echo $Usuario->ultima_accion ?></td>
                      <td><?php echo $Usuario->lugar ?></td>
                      <td><?php echo $Usuario->equipo ?></td>
                      <td><?php echo $Usuario->funcion ?></td>
                      <td><?php echo $Usuario->observaciones ?></td>
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
    $('#lista-de-usuarios').DataTable({
        'searching': true,
        'autoWidth': true,
        'pageLength': 10, 
        'paging': true,
        'order': [[ 0, 'asc' ]],
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