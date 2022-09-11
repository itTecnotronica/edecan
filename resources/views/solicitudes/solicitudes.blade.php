@extends('layouts.backend')

@section('contenido')

<?php
$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;

use App\Http\Controllers\HomeController;

$estado_url = $estado;

$Roles = Auth::user()->roles();
$permiso_ejecutivo = array(1,3);


function permisoAutorizado2($Roles, $permisos) {
  $autorizado = false;
  foreach ($Roles as $rol_id) {
    if (in_array($rol_id, $permisos)) {
      $autorizado = true;
    }
  }
  return $autorizado;
}

$pais_id = Auth::user()->pais_id;
$pais = '';
if ($pais_id <> '') {
  $pais = Auth::user()->pais->pais;
}

$HomeController = new HomeController();
?>
    
    <br>

      <div class="col-xs-12">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title"><?php echo $titulo; ?></h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="table" class="table table-bordered table-striped" style="max-width: 500px" >
              <thead>
              <tr>
                  <th><?php echo __('Acción') ?></th>
                  <th><?php echo __('Id') ?></th>
                  <th><?php echo __('Tipo') ?></th>
                  <th><?php echo __('Ciudad') ?></th>
                  <th><?php echo __('Importe') ?></th>
                  <th><?php echo __('Idioma') ?></th>
                  <th><?php echo __('Solicitante') ?>/<?php echo __('Registrante') ?></th>
                  <!--th><?php echo __('Formulario') ?></th>
                  <th><?php echo __('Lista Inscriptos') ?></th-->
                  <th><?php echo __('Ejecutivo') ?></th>
                  <th><?php echo __('Inscriptos') ?></th>
                  <th><?php echo __('Estado') ?></th>
              </tr>
              </thead>
              <tbody>
              <?php if ($Solicitudes <> null) { ?>
                <?php foreach ($Solicitudes as $solicitud) { ?>
                <tr>
                    <td>
                      <div class="btn-group">
                        <?php if ($estado_url == 'x') { ?>
                        <a href="<?php echo env('PATH_PUBLIC')?>pagar-paypal/recuperar-operacion-pagada/<?php echo $solicitud->id; ?>">
                        <?php }
                        else { ?>
                        <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/ver/<?php echo $solicitud->id; ?>">
                        <?php } ?>
                        <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                      </a>
                      </div>
                    </td>
                    <td><?php echo $solicitud->id; ?></td>
                    <td><?php echo __($solicitud->tipo_de_evento_fk); ?></td>
                    <td><?php echo $solicitud->localidad_fk; ?></td>              
                    <td><?php echo $solicitud->monto_a_invertir; ?></td>              
                    <td><?php echo __($solicitud->idioma); ?></td>     
                    <td><?php echo $solicitud->nombre_del_solicitante; ?> / <?php echo $solicitud->name; ?></td>
                    <!--td>
                      <?php 
                      $hash = $solicitud->hash;
                      if ($solicitud->hash == '') {
                        $hash = 'nulo';
                      }
                      ?>
                      <a href="<?php echo env('PATH_PUBLIC')?>f/<?php echo $solicitud->id ?>/<?php echo $hash ?>" target="_blank">
                      <button type="button" class="btn btn-primary"><i class="fa fa-file-text-o"></i></button>
                    </a></td>
                      <td><a href="<?php echo env('PATH_PUBLIC')?>f/i/<?php echo $solicitud->id ?>/<?php echo $hash ?>" target="_blank"><button type="button" class="btn btn-primary"><i class="fa fa-list"></i></button></td-->
                    <td><?php echo $solicitud->nombre_de_ejecutivo; ?></td>                                
                    <td><?php echo $solicitud->inscripciones_cant; ?></td>
                    <?php
                    $array_estado = $HomeController->estado($solicitud->sino_aprobado_administracion, $solicitud->sino_aprobado_solicitar_revision, $solicitud->sino_cancelada, $solicitud->sino_aprobado_finalizada);
                    $estado = $array_estado['estado'];
                    $class_estado = $array_estado['class_estado'];
                    $span_estado = $array_estado['span_estado'];
                    ?>                
                    <td><?php echo $span_estado; ?></td>


                </tr>
                <?php } ?>
              <?php } ?>
            </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>

      <?php if (!(permisoAutorizado2($Roles, [3]) and $pais_id == 30)) { ?>
      <div class="col-xs-3">
        <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear"><button type="button" class="btn btn-block btn-info col-xs-3"><i class="fa fa-plus"></i> Crear Solicitud</button></a>
      </div>
      <?php } ?>

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
              'order': [[ 1, 'asc' ]],
              'columnDefs': [{ "width": "100px", "targets": 0 }], 
          })
        })
      </script>
        

          
          



@endsection
