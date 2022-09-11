@extends('emails.templates.action')


@section('title')
Autorizaci&oacute;n de Preinscripci&oacute;n
@endsection

@section('titulo')
Autorizaci&oacute;n de Preinscripci&oacute;n
@endsection

@section('contenido')

	<tr>
		<td class="content-block">
			<strong><?php echo $nombre ?> <?php echo $apellido ?></strong>, del Lumisial <?php echo $lumisial ?> esta solicit&aacute;ndole Paz y Salvo para su inscripci&oacute;n a: <strong><?php echo $evento ?></strong>
			<br><br><br>
		</td>
	</tr>
	<tr>
		<td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler">
			<a href="<?php echo env('PATH_PUBLIC')?>confirmar/preinscripcion/SI/<?php echo $id_preinscripcion_a_evento ?>/<?php echo $hash_validacion ?>" class="btn-primary" itemprop="url">Confirmar Autorizaci&oacute;n</a><br><br><br>
		</td>
	</tr>
	<tr>
		<td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler">
			<a href="<?php echo env('PATH_PUBLIC')?>confirmar/preinscripcion/NO/<?php echo $id_preinscripcion_a_evento ?>/<?php echo $hash_validacion ?>" class="btn-cancel" itemprop="url">Denegar Autorizaci&oacute;n</a><br><br><br>
		</td>
	</tr>

@endsection								