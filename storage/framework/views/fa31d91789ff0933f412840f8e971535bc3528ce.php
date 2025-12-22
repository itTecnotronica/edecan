



<?php 

$modo = '';

$mostrar_equipo = true;
$mostrar_funcion = true;
$mostrar_capacitacion = false;

if (isset($_GET['modo'])) {
    $modo = $_GET['modo']; 

    if ($modo == 'cap') {
        $mostrar_equipo = false;
        $mostrar_funcion = false;
        $mostrar_capacitacion = true;
    }
}

?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Registrarse</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="<?php echo e(route('register')); ?>">
                        <?php echo e(csrf_field()); ?>


                        <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                            <label for="name" class="col-md-4 control-label"><?php echo __('Nombre') ?></label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>" required autofocus>

                                <?php if($errors->has('name')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('name')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo e($errors->has('apellido') ? ' has-error' : ''); ?>">
                            <label for="apellido" class="col-md-4 control-label"><?php echo __('Apellido') ?></label>

                            <div class="col-md-6">
                                <input id="apellido" type="text" class="form-control" name="apellido" value="<?php echo e(old('apellido')); ?>" required autofocus>

                                <?php if($errors->has('apellido')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('apellido')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                            <label for="email" class="col-md-4 control-label"><?php echo __('Correo Electrónico') ?></label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" required>

                                <?php if($errors->has('email')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                            <label for="password" class="col-md-4 control-label"><?php echo __('Contraseña') ?></label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                <?php if($errors->has('password')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label"><?php echo __('Confirmar Contraseña') ?></label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="pais_id" class="col-md-4 control-label"><?php echo __('Pais') ?></label>

                            <div class="col-md-6">
                                <?php $paises = App::make('App\Http\Controllers\HomeController')->get_paises();?>
                                <?php echo Form::select("pais_id", $paises, 1, ['id' => "pais_id", 'class' => 'form-control', 'required' => 'required']); ?>
                            </div>
                        </div>

                        <?php if($mostrar_capacitacion) { ?>
                        <div class="form-group">
                            <label for="equipo_id" class="col-md-4 control-label"><?php echo __('Capacitacion') ?></label>

                            <div class="col-md-6">
                                <?php $capacitaciones = App::make('App\Http\Controllers\HomeController')->get_capacitaciones();?>
                                <?php echo Form::select("capacitacion_id", $capacitaciones, null, ['id' => "capacitacion_id", 'class' => 'form-control', 'required' => 'required']); ?>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="form-group">
                            <label for="ciudad" class="col-md-4 control-label"><?php echo __('Ciudad') ?></label>

                            <div class="col-md-6">
                                <input id="ciudad" type="text" class="form-control" name="ciudad" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="lumisial" class="col-md-4 control-label"><?php echo __('Lumisial') ?></label>

                            <div class="col-md-6">
                                <input id="lumisial" type="text" class="form-control" name="lumisial" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="diocesis" class="col-md-4 control-label"><?php echo __('Diocesis') ?></label>

                            <div class="col-md-6">
                                <input id="diocesis" type="text" class="form-control" name="diocesis" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label"><?php echo __('Idioma') ?></label>

                            <div class="col-md-6">
                                <?php $idiomas = App::make('App\Http\Controllers\HomeController')->get_idiomas();?>
                                <?php echo Form::select("idioma_id", $idiomas, NULL, ['id' => "idioma_id", 'class' => 'form-control', 'required' => 'required']); ?>
                            </div>
                        </div>


                        <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                            <label for="celular" class="col-md-4 control-label"><?php echo __('Numero de Celular completo para Whatsapp, por ejemplo: +5491154872252') ?></label>

                            <div class="col-md-6">
                                <input id="celular" type="celular" class="form-control" name="celular" value="<?php echo e(old('celular')); ?>" required>

                                <?php if($errors->has('celular')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('celular')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <input id="modo" type="hidden" name="modo" value="<?php echo $modo ?>">


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo __('Registrarme') ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>