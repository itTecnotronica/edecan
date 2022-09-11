<?php
use \App\Http\Controllers\SolicitudController;

$SolicitudController = new SolicitudController;

$idioma_por_pais = $Solicitud->idioma_por_pais();

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
    <title>Gnosis | {{ __($Solicitud->tipo_de_evento->tipo_de_evento) }}</title>

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


    <!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '<?php echo env('PIXEL_AC_MUNDIAL')?>');
      fbq('track', 'PageView');
    </script>

    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=<?php echo env('PIXEL_AC_MUNDIAL')?>&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->

    <script>
      <?php if (isset($registracion_encuesta) and $registracion_encuesta == 'SI') {?>
        fbq('trackCustom', 'PollComplete');
      <?php }
      else {?>
        fbq('track', 'CompleteRegistration', {
          value: 1,
          currency: 'USD'
          });
      <?php } ?>
    </script>

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook);
    }
    ?>

    <script>
      <?php if (isset($registracion_encuesta) and $registracion_encuesta == 'SI') {?>
        fbq('trackCustom', 'PollComplete');
      <?php }
      else {?>
        fbq('track', 'CompleteRegistration', {
          value: 1,
          currency: 'USD'
          });
      <?php } ?>
    </script>

</head>


<body>

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_script_body)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_script_body);
    }
    ?>
                    <div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <?php echo $mensaje_box ?></h4>
                    </div>



                    <div class="alert alert-success alert-dismissible">
                        <h4 class="pull-right"><img src="<?php echo env('PATH_PUBLIC')?>img/whatsapp-icon.png"> WhatsApp</h4>
                        <?php if ($url_invitacion_grupo_whatsapp <> '') { ?>
                            <a class="btn btn-default" href="<?php echo $url_invitacion_grupo_whatsapp ?>" role="button" target="_blank">
                                <img src="<?php echo env('PATH_PUBLIC')?>img/whatsapp-icon.png" style="width: 25px">
                                <?php echo __('Sumate a nuestro Grupo de Whatsapp') ?>
                            </a>
                        <br><br>
                        <?php } ?>
                        <a class="btn btn-default" href="<?php echo $Solicitud->url_contacto_whatsapp_form() ?>" role="button" target="_blank">
                            <img src="<?php echo env('PATH_PUBLIC')?>img/whatsapp-icon.png" style="width: 25px">
                            <?php echo __('Quieres escribirnos? envianos un WhatsApp') ?>
                        </a>
                    </div>

                    <?php if ($url_fanpage <> '') { ?>
                        <div class="alert alert-info alert-dismissible">
                            <h4 class="pull-right"><img src="<?php echo env('PATH_PUBLIC')?>img/facebook-icon.png"> Facebook</h4>

                                <p><?php echo __('Dale Me Gusta a nuestra Fanpage') ?></p>
                                <div id="fb-root"></div>
                                <script async defer crossorigin="anonymous" src="https://connect.facebook.net/<?php echo $mnemo_face ?>/sdk.js#xfbml=1&version=v3.3&appId=1630189860351788&autoLogAppEvents=1"></script>
                                <div class="fb-page"
                                  data-href="<?php echo $url_fanpage ?>"
                                  data-width="380"
                                  data-hide-cover="false"
                                  data-show-facepile="false"></div>

                                  <br><br>

                                <?php echo __('Comparti este evento con tus amigos') ?>: <div class="fb-share-button" data-href="<?php echo $url_form_inscripcion ?>" data-layout="button" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Flocalhost%3A1010%2Fac%2Fpublic%2Ff%2F6%2FVenadoTuerto-03-19&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Compartir</a></div>
                        </div>
                    <?php } ?>





                    <br>

                    <a href="<?php echo $url_form_inscripcion ?>!embebed">
                      <button type="button" class="btn btn-success btn-lg center-block"><?php echo __('Quieres registrar a otra persona ?') ?></button>
                    </a>



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
