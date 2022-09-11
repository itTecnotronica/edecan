@extends('layouts.espacio_de_usuario')


@section('cabecera')
	<h1>
      Lecciones
      <small>Curso de Autoconocimiento</small>
    </h1>
@endsection

@section('contenido-principal')
  
  <div class="row">
    <div class="col-xs-12 col-md-6 col-md-offset-3">
      <div class="centered-header"> <h4> PROGRESO </h4> </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-md-6 col-md-offset-3">
      <div class="progress2">
        <div class="progress-value2" style="width:{{ $progreso }}%"> {{ $progreso . "%"}}</div>
      </div>
    </div>
  </div>
  
  <br />


  @forelse ($lecciones as $leccion)
      
      <!-- Permitimos ver al alumno todas las lecciones que ha marcado como vistas 
      como asi tambien la siguiente -->
      @if (in_array($leccion->orden_de_leccion, $lecciones_hechas) or in_array($leccion->orden_de_leccion - 1, $lecciones_hechas))
        <div class="row">
          <div class="col-md-6 col-md-offset-3 col-xs-12">
            <!-- small box -->
            <div class="small-box bg-green">
              <div class="inner">
                <h4>{{ $leccion->nombre_de_la_leccion }}</h4>

                @if ($leccion->url_avance_leccion == "")
                  <p>Leccion {{ $leccion->orden_de_leccion - 1 }} </p>
                @else
                  <p>Leccion {{ $leccion->orden_de_leccion - 1 }} | <a class="enlace" href="{{ $leccion->url_avance_leccion}}">Ver Avance</a> </p>
                @endif
              </div>
              <div class="icon" style="top: -13px "}>
                <i class="glyphicon glyphicon-ok-circle" style="font-size: 0.7em;"></i>
              </div>
              <a href="{{ $leccion->url_enlace_a_la_leccion_1 }}" class="small-box-footer">
                Ver Material
              </a>
            </div>
          </div>
        </div>
      @else
        <div class="row">
          <div class="col-md-6 col-md-offset-3 col-xs-12">
            <!-- small box -->
            <div class="small-box bg-gray-active">
              <div class="inner">
                <h4>{{ $leccion->nombre_de_la_leccion }}</h4>

                <p>Leccion {{ $leccion->orden_de_leccion - 1}}</p>
              </div>
              <div class="icon" style="top: -26px;" }>
                <i class="fa fa-hourglass-o" style="font-size: 0.7em;"></i>
              </div>
              <a href="#" class="small-box-footer">
                Disponible mas adelante
              </a>
            </div>
          </div>
        </div>
      @endif
  @empty
      <p>Este alumno no tiene lecciones</p>
  @endforelse



@endsection