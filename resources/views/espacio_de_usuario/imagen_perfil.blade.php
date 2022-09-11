@extends('layouts.espacio_de_usuario')

@section('head')
  <!-- usamos esto para hacer POST ajax requests -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('cabecera')
	<h1>
      Cambiar foto de perfil


      

    </h1>

@endsection


@section('contenido-principal')
	<div class="row">
		<div class="col col-md-12">
			
			<div class="profile">
			        <div class="photo">
			            <input type="file" accept="image/*">
			            <div class="photo__helper">
			                <div class="photo__frame photo__frame--circle">
			                    <canvas class="photo__canvas"></canvas>
			                    <div class="message is-empty">
			                        <p class="message--desktop">Toca aqui para subir una imagen.</p>
			                        <p class="message--mobile">Toca aqui para subir una imagen.</p>
			                    </div>
			                    <div class="message is-loading">
			                        <i class="fa fa-2x fa-user"></i>
			                    </div>
			                    <div class="message is-dragover">
			                        <i class="fa fa-2x fa-cloud-upload"></i>
			                        <p>Drop your photo</p>
			                    </div>
			                    <div class="message is-wrong-file-type">
			                        <p>Solo imagenes permitidas.</p>
			                        <p class="message--desktop">Toca aqui para subir una imagen.</p>
			                        <p class="message--mobile">Toca aqui para subir una imagen.</p>
			                    </div>
			                    <div class="message is-wrong-image-size">
			                        <p>Tu foto debe tener mas de 350 pixeles de ancho.</p>
			                    </div>
			                </div>
			            </div>

			            <div class="photo__options hide">
			                <div class="photo__zoom">
			                    <input type="range" class="zoom-handler">
			                </div><a href="javascript:;" class="remove"><i class="fa fa-trash"></i></a>
			            </div>
			        </div>
			    </div>
		</div>
		
	</div>
	<div class="row">
		<div class="button-container">
			<button type="button" id="uploadBtn">Guardar Imagen</button>
		
			<img src="" alt="" class="preview">
			<img src="" alt="" class="preview preview--rounded">	
		</div>
	</div>


@endsection