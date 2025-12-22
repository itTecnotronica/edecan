


<?php $__env->startSection('title'); ?>
Solicitud de Debito
<?php $__env->stopSection(); ?>

<?php $__env->startSection('titulo'); ?>
<?php echo __('Notificación de SistemaAC') ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contenido'); ?>


	<tr>
		<td class="content-block">
			<strong><?php echo $mensaje ?></strong><br><br>

			<br><br><br>

		</td>
	</tr>

<?php $__env->stopSection(); ?>								
<?php echo $__env->make('emails.templates.action', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>