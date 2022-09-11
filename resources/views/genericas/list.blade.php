<?php 

if ($_SERVER['HTTP_HOST'] == 'localhost:1010' or $_SERVER['HTTP_HOST'] == 'ac.gnosis.is') {
  $path_public = env('PATH_PUBLIC');
}
else {
  $path_public = 'https://'.$_SERVER['HTTP_HOST'].'/';  
}

?>
@extends('layouts.backend')

@section('contenido')


<div id="tabla"></div>

<?php 
$gen_seteo['gen_campos_a_ocultar'] = 'id';
?>

<script type="text/javascript">
$.ajax({
  url: '<?php echo $path_public ?>crearlista',
  type: 'POST',
  dataType: 'html',
  async: true,
  data:{
    _token: "{{ csrf_token() }}",
    gen_modelo: '<?php echo $gen_modelo ?>',
    gen_seteo: '<?php echo serialize($gen_seteo) ?>',
    gen_opcion: '<?php echo $gen_opcion ?>'
  },
  success: function success(data, status) {        
    $("#tabla").html(data);
  },
  error: function error(xhr, textStatus, errorThrown) {
      alert(errorThrown);
  }
});
</script>

@endsection
