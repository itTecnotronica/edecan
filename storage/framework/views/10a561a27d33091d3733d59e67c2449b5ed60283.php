

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Error</div>
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger">
                            <?php echo e($error); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app_gnosis', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>