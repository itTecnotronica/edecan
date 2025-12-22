<?php 
$imgLogo = 'logo.jpg';
if ($_SERVER['HTTP_HOST'] == 'ac.igca.com.ar') {
    $imgLogo = 'igca.png';
}
?>

<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta name="facebook-domain-verification" content="w365zbvvazsahxk8ea6h6jise2oq6m" />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-46601315-3"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-46601315-3');
    </script>

    <meta charset="utf-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Tecnohouse - Viviendas')); ?></title>

    <!-- Styles -->
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="row">
                <br>
                <p align="center"><a href="<?php echo ENV('PATH_PUBLIC') ?>"><img src="<?php echo env('PATH_PUBLIC')?>img/<?php echo $imgLogo ?>"></a></p>
            </div>
        </div>
        </nav>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- Scripts -->
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
</body>
</html>
