<?php
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController();

$Idioma_por_pais = $Inscripcion->fecha_de_evento->solicitud->idioma_por_pais();

$nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;
$denominacion_de_voucher = $Idioma_por_pais->denominacion_de_voucher;

$idioma = $Idioma_por_pais->idioma->mnemo;
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
    <meta name="description" content="<?php echo $nombre_institucion ?> | {{ $denominacion_de_voucher }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}">
    <meta name="author" content="<?php echo $nombre_institucion ?>.is">
    <meta name="keywords" content="<?php echo $nombre_institucion ?> | {{ $denominacion_de_voucher }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }} ">
    <meta property="og:title" content="<?php echo $nombre_institucion ?> | {{ $denominacion_de_voucher }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}" />
    <meta property="og:url" content="https://www.<?php echo $nombre_institucion ?>.is" />
    <meta property="og:description" content="<?php echo $nombre_institucion ?> | {{ $denominacion_de_voucher }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}">
    <meta property="og:image" content="<?php echo $dir_imagen_url ?>">





    <!-- Title Page-->
    <title><?php echo $nombre_institucion ?> | {{ $denominacion_de_voucher }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}</title>

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
        <div class="wrapper wrapper--w960">
            <div class="card card-4">
                <div class="card-body">

                  <div class="row">
                  <div class="col-xs-12 col-md-6 col-lg-2">
                    <center><img class="sol-de-acuario-top img-responsive" src="<?php echo $imagen_top ?>" alt="<?php echo $nombre_institucion ?>" title="<?php echo $nombre_institucion ?>"></center>
                  </div>

                  <div class="col-xs-12 col-md-6 col-lg-3">
                    <center>
                      <h2><?php echo $nombre_institucion ?></h2>
                      <p class="tit1_voucher">{{ $nombre_de_la_institucion }} </p>
                      <p class="tit3_voucher">{{ $denominacion_de_voucher }}</p>
                      <p class="tit2_voucher">{{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}</p> 
                    </center>
                  </div>

                  <div class="col-xs-12 col-md-6 col-lg-4">
                    <br><?php echo __('Nombre'); ?>: <?php echo mb_strtoupper($Inscripcion->nombre, 'UTF-8'); ?> 
                    <br><?php echo __('Apellido'); ?>: <?php echo mb_strtoupper($Inscripcion->apellido, 'UTF-8'); ?> 
                    <?php if ($Inscripcion->fecha_de_evento_id <> '') { ?>
                    <br><?php echo __('Horario'); ?>: <?php echo $Inscripcion->fecha_de_evento->armarDetalleFechasDeEventos(); ?>       
                    <?php } ?>    
                  </div>
                  <div class="col-xs-12 col-md-6 col-lg-3">
                    <center><img src="<?php echo $dir_imagen_url ?>"></center>
                  </div>
                  </div>


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