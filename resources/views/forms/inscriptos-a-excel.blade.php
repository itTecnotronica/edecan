<?php
header('Content-type: application/vnd.ms-excel;charset=iso-8859-15');
header('Content-Disposition: attachment; filename='.$Solicitud->hash.'-'.$fecha_de_evento_id.'.xls');
$mensaje_np = __('No puedo asistir a este horario pero quisiera me contacten mas adelante por otros días y horarios');;

function telefono($numero) {
  $numero = str_replace(array(" ", "-"), array(""), $numero);
  $comienzo = strlen($numero);
  $resultado = '';
  while($comienzo>=0) {
    $resultado = substr($numero, $comienzo, 3) . " " . $resultado;
    $comienzo -= 3;
  }
  return $resultado;
}
?>

<style type="text/css">
  th, td {
    border-style: solid; 
    border-color: black; 
    border-width: 1px;
  }
</style>

<table id="table" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th><?php echo __('ID') ?></th>
            <th><?php echo __('Apellido') ?></th>
            <th><?php echo __('Nombre') ?></th>
            <th><?php echo __('Celular') ?></th>
            <th><?php echo __('Correo') ?></th>
            <th><?php echo __('Horario') ?></th>
            <th><?php echo __('Lugar') ?></th>
            <th><?php echo __('Consulta') ?></th>
            <th><?php echo __('Observaciones') ?></th>
            <th><?php echo __('Confirmado') ?></th>
            <th><?php echo __('Cancelo') ?></th>
            <th><?php echo __('Indique el presente con una P') ?></th>
        </tr>
    </thead>
    <tbody>
      <?php 
      $i = -1;
      foreach ($Inscripciones as $Inscripcion) { 
        $i++;

      ?>

          <tr>
              <td><?php echo $Inscripcion->id; ?></td>
              <td><?php echo $Inscripcion->apellido; ?></td>
              <td><?php echo $Inscripcion->nombre; ?></td>
              <td><?php echo telefono($Inscripcion->celular); ?></td>
              <td><?php echo $Inscripcion->email_correo; ?></td>
              <td>
                <?php 
                if ($Inscripcion->fecha_de_evento_id <> '') {
                  echo $Inscripcion->fecha_de_evento->armarDetalleFechasDeEventos('select'); 
                }
                else {
                  echo $mensaje_np;
                }
                ?>    
              </td>
              <td>
                <?php 
                if ($Inscripcion->fecha_de_evento_id <> '') {
                  echo $Inscripcion->fecha_de_evento->direccion_de_inicio;
                }
                ?>    
              </td>
              <td><?php echo $Inscripcion->consulta; ?></td>
              <td><?php echo $Inscripcion->observaciones; ?></td>
              <td><?php echo $Inscripcion->sino_confirmo; ?></td>
              <td><?php echo $Inscripcion->sino_cancelo; ?></td>
              <td></td>
          </tr>
      <?php } ?>
</tbody>
</table>
