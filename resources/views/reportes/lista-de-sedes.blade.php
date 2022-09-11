<?php 


use \App\Http\Controllers\GenericController;
$GenericController = new GenericController;


function mostrar_correo($email_correo) {
  
  $html_correo = '';
  
  if (trim($email_correo) <> '') { 
    $html_correo = 'E-mail: <a href="mailto:'.$email_correo.'">'.$email_correo.'</a>';
  }

  return $html_correo;
}

function separ_tel($numero, $separador) {

  $telefonos = [];
  $posicion = strpos($numero, $separador);
  
  if ($posicion) {
    $telefonos = explode($separador, $numero);
  }

  return $telefonos;
}

function mostrar_tel($numero, $GenericController, $codigo_tel) {


  $texto = null;
  $etiqueta_wa = 'Enviar Whatsapp';
  $etiqueta_sms = 'Enviar SMS';
  $etiqueta_call = 'Llamar al Tel';
  $class_btn_wa = 'btn btn-sm btn-success';
  $class_btn_sms = 'btn btn-sm btn-primary';
  $class_btn_call = 'btn btn-sm btn-warning';
  $class_icon = 'fa fa-whatsapp';
  $style_btn = '';

  $a_telelefonos = [];

  $separadores = [',', ';', '/'];

  foreach ($separadores as $separador) {
    $telefonos = separ_tel($numero, $separador);
    foreach ($telefonos as $tel) {
      array_push($a_telelefonos, $tel);
    }
  }
  
  if (count($a_telelefonos) == 0) {
    array_push($a_telelefonos, $numero);
  }

  $print_tel = '';

  if (count($a_telelefonos) > 0) {
    foreach ($a_telelefonos as $tel) {
      if (strlen($tel) > 3) {
        $btn_enviar_wa = $GenericController->btn_enviar_wa($tel, $codigo_tel, $texto, $etiqueta_wa, $class_btn_wa, $class_icon, $style_btn);
        $btn_enviar_sms = $GenericController->btn_enviar_sms($tel, $codigo_tel, $texto, $etiqueta_sms, $class_btn_sms, $class_icon, $style_btn);
        //$btn_llamar = $GenericController->btn_enviar_sms($tel, $codigo_tel, $texto, $etiqueta_call, $class_btn_call, $class_icon, $style_btn);

        $print_tel .= '<p>Tel: ';        
        $print_tel .= '<a href="tel:'.$GenericController->celular_wa($tel, $codigo_tel).'" target="_blank">'.$tel.'</a></br>';
        $print_tel .= $btn_enviar_wa;
        $print_tel .= ' '.$btn_enviar_sms;
        $print_tel .= '</p>';        
      }

    }
  }

  return $print_tel;

}



 ?>
<!-- jQuery 3 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap 3.3.7 -->
<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/AdminLTE.min.css">
<!-- AdminLTE Skins. Choose a skin from the css/skins -->

<!-- Font Awesome -->
<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC') ?>bower_components/font-awesome/css/font-awesome.min.css">

<!-- DataTables -->
<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


<!DOCTYPE html>
<html>
<head>
  <title><?php echo $titulo ?></title>
</head>
<body>

<div class="container">
  <div class="row">
    <h3><?php echo $titulo ?></h3>

    <table id="table" class="table table-bordered table-striped" >
      <thead>
        <tr>
          <?php if ($mostrar_pais) { ?>
          <th v-show="show_col_id"><?php echo __('Pais') ?></th>
          <?php } ?>
          <th v-show="show_col_id" class="hidden-xs"><?php echo __('Provincia, Estado o Región') ?></th>
          <th v-show="show_col_id" class="hidden-xs"><?php echo __('Ciudad') ?></th>
          <th v-show="show_col_id" class="hidden-xs"><?php echo __('Direccion') ?></th>
          <th v-show="show_col_id" class="hidden-lg hidden-sm hidden-md"><?php echo __('Ubicación') ?></th>
          <th v-show="show_col_id"><?php echo __('Contacto') ?></th>
        </tr>
      </thead>
      <tbody>

      
      <?php foreach ($Sedes as $Sede) { ?>
        <tr>
          <?php if ($mostrar_pais) { ?>
          <td><?php echo $Sede->pais ?></td>
          <?php } ?>

          <td class="hidden-xs"><?php echo $Sede->provincia_estado_o_region ?></td>
          <td class="hidden-xs"><?php echo $Sede->ciudad ?></td>
          <td class="hidden-xs"><?php echo $Sede->direccion ?> <?php echo $Sede->informacion_adicional ?> <a href="<?php echo $Sede->url_enlace_a_google_maps ?>" target="_blank"><button type="button" class="btn btn-info btn-sm"><i class="fa fa-fw fa-map-marker" style="font-size: 17px"></i> Ver Ubicación</button></a></td>

          <td class="hidden-lg hidden-sm hidden-md">
            <?php echo $Sede->provincia_estado_o_region ?><br>
            <?php echo $Sede->ciudad ?><br>
            <?php echo $Sede->direccion ?> <?php echo $Sede->informacion_adicional ?> <a href="<?php echo $Sede->url_enlace_a_google_maps ?>" target="_blank"><button type="button" class="btn btn-info btn-sm"><i class="fa fa-fw fa-map-marker" style="font-size: 17px"></i> Ver Ubicación</button></a></td>

          <td><?php echo mostrar_tel($Sede->telefono_con_whatsapp, $GenericController, $Sede->codigo_tel) ?> <?php echo mostrar_correo($Sede->email_correo) ?></td>
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
              //'order': [[ 0, 'des' ]],
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