@extends('layouts.espacio_de_usuario')

@section('contenido-principal')
    <form method="POST" action="actualizarInfoAlumno" accept-charset="UTF-8">

		{{ Form::token() }}
		<div class="box box-info">
		  <div class="box-header with-border">
		    <h3 class="box-title">Datos del alumno</h3>
		  </div>
		  <div class="box-body">

		  	<input type="hidden" name="id" value="{{ $alumno->id }}">	

		    <div class="input-group col-xs-12">
		      <span class="input-group-addon">Nombre</span>
		      <input type="text" class="form-control" name="nombre" value="{{ ucwords(strtolower($alumno->nombre)) }}" placeholder="Nombre">
		    </div>
		    <br>

		    <div class="input-group col-xs-12">
		      <span class="input-group-addon">Apellido</span>
		      <input type="text" class="form-control" name="apellido" value="{{ ucwords(strtolower($alumno->apellido)) }}" placeholder="Apellido">
		    </div>
		    <br>

		    <div class="input-group col-xs-12">
		      <span class="input-group-addon">Celular</span>
		      <input type="text" class="form-control" name="celular" value="{{ $alumno->celular }}" placeholder="Celular">
		    </div>
		    <br>

		    <div class="input-group col-xs-12">
		      <span class="input-group-addon">Correo</span>
		      <input type="text" class="form-control" name="email_correo" value="{{ strtolower($alumno->email_correo) }}" placeholder="Correo">
		    </div>
		    <br>



		    <div class="box-footer">
		      <button type="submit" class="btn btn-default">Cancelar</button>
		      <button type="submit" class="btn btn-info pull-right">Actualizar</button>
		    </div>
		    <!-- /input-group -->
		  </div>
		  <!-- /.box-body -->
		</div>

	</form>


	
@endsection