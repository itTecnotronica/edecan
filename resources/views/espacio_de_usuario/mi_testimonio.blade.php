@extends('layouts.espacio_de_usuario')

@section('head')
  <!-- usamos esto para hacer ajax requests -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('cabecera')
	<h1>
      ¿Que te parece el curso?
      <small>Dejanos tu sentir!</small>

      <div class="my-rating"></div>
    </h1>
@endsection

@section('contenido-principal')
	

	<div class="row">
		<div class="col col-md-12">
			    <form method="POST" action="guardarTestimonio" accept-charset="UTF-8">

					{{ Form::token() }}
					<div class="box box-info formulario-testimonios">
					  	
						<br />

						<div class="form-group">
							<textarea required placeholder="" class="txta" name="testimonio[texto]">{{ $testimonio->texto }}</textarea>
						</div>

						<div class="checkbox">
						  <label>

						  	
						    <input type="checkbox" {{ $testimonio->autorizacion == '1' ?  "checked" : "" }}  name="testimonio[autorizacion]" value="1">
						    Autorizo a AGEACAC a utilizar a publicar este testimonio
						  </label>
						</div>

					    <div class="box-footer">
					      <button type="submit" class="btn btn-info pull-right">Guardar</button>
					    </div>
					    <!-- /input-group -->
					  </div>
					  <!-- /.box-body -->

					
				</form>
		</div>
		    
        <script type="text/javascript">
        	window.setTimeout(() => {
        			$( document ).ready(function() {
        		       var rating = {!! json_encode($rating) !!};
        		       console.log('rating', rating);
        		       $('.my-rating').starRating('setRating', rating);
        			})
        	})	
        	
        </script>


	</div>
    


@endsection