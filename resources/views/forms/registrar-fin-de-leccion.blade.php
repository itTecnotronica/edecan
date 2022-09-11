<?php
$idioma = $Solicitud->idioma->mnemo;
App::setLocale($idioma);  
?>
<!DOCTYPE html>
<html lang="es">

<head>
  
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-46601315-3"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-46601315-3');
    </script>

    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title><?php echo $Solicitud->institucion->institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?></title>

    <!-- Icons font CSS-->
    <link href="<?php echo env('PATH_PUBLIC')?>templates/2/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="<?php echo env('PATH_PUBLIC')?>templates/2/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="<?php echo env('PATH_PUBLIC')?>templates/2/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="<?php echo env('PATH_PUBLIC')?>templates/2/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="<?php echo env('PATH_PUBLIC')?>templates/2/css/main.css" rel="stylesheet" media="all">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">

    <!-- vue.js -->
    <script src="<?php echo env('PATH_PUBLIC')?>js/vue/vue.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/vee-validate.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/locale/es.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.css">



</head>

<body>
    <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
              <div class="card-body">

                <center><h2 class="title">{{ __($Solicitud->descripcion_sin_estado()) }}</h2></center>

                            
                <div class="alert alert-<?php echo $class_resultado ?> alert-dismissible">
                  <h4><i class="icon fa fa-<?php echo $class_icon ?>"></i> <?php echo $titulo; ?>!</h4>
                  <?php echo $mensaje; ?>:<br> 
                  <p>
                    <strong>
                    <?php 
                      if ($Solicitud->institucion_id == 2) {
                        echo __('Lección').': '.$Leccion->orden_de_leccion;
                      }
                      else {
                        if ($Solicitud->id <> 6227) { 
                          echo __('Lección').': '.$Leccion->nombre_de_la_leccion;
                        }
                        else {
                          if ($Leccion->id == 81) {
                            echo '1° Encuentro: Domingo 13';
                          }
                          else {
                            echo '2° Encuentro: Domingo 21';
                          }
                        }
                      }                    
                    ?>                        
                    </strong>
                  </p>.
                </div>
              </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="<?php echo env('PATH_PUBLIC')?>templates/2/vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="<?php echo env('PATH_PUBLIC')?>templates/2/vendor/select2/select2.min.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>templates/2/vendor/datepicker/moment.min.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>templates/2/vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="<?php echo env('PATH_PUBLIC')?>templates/2/js/global.js"></script>

    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->

                            

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->
