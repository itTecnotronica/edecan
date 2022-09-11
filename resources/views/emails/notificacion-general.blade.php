@extends('emails.templates.action')


@section('title')
Solicitud de Debito
@endsection

@section('titulo')
<?php echo __('Notificación de SistemaAC') ?>
@endsection

@section('contenido')


	<tr>
		<td class="content-block">
			<strong><?php echo $mensaje ?></strong><br><br>

			<br><br><br>

		</td>
	</tr>

@endsection								