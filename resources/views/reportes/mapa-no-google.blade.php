@extends('layouts.backend')

@section('contenido')

 <div id="world-map" style="width: 600px; height: 400px"></div>
  <script>
    $(function(){
      $('#world-map').vectorMap({map: 'world_mill'});
    });
  </script>





@endsection
