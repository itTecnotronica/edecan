@extends('layouts.espacio_de_usuario')

@section('head')
  <!-- usamos esto para hacer ajax requests -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('cabecera')
	<h1>
      Dejanos tus sugerencias!
      <small>Como podriamos mejorar?</small>
    </h1>
@endsection

@section('contenido-principal')
	

	<div class="row">
		<div class="col col-md-12">
			    <form method="POST" action="guardarSugerencia" accept-charset="UTF-8">

					{{ Form::token() }}
					<div class="box box-info formulario-sugerencias">
					  	
						<br />

						<div class="form-group">
							<textarea required placeholder="" class="txta" name="sugerencia[texto]">{{ $sugerencia->texto }}</textarea>
						</div>

					    <div class="box-footer">
					      <button type="submit" class="btn btn-info pull-right">Guardar</button>
					    </div>
					    <!-- /input-group -->
					  </div>
					  <!-- /.box-body -->

					
				</form>
		</div>
		    
  

	</div>
    


@endsection