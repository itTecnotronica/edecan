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
    <!--script async src="https://www.googletagmanager.com/gtag/js?id=UA-46601315-3"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-46601315-3');
    </script-->

    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title><?php echo $Solicitud->institucion->institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?></title>

    <!-- Icons font CSS-->
    <link href="<?php echo $dominio_publico?>templates/2/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $dominio_publico?>templates/2/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="<?php echo $dominio_publico?>templates/2/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="<?php echo $dominio_publico?>templates/2/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="<?php echo $dominio_publico?>templates/2/css/main.css" rel="stylesheet" media="all">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo $dominio_publico?>bower_components/bootstrap/dist/css/bootstrap.min.css">

    <!-- vue.js -->
    <script src="<?php echo $dominio_publico?>js/vue/vue.js"></script>
    <script src="<?php echo $dominio_publico?>js/vee-validate/dist/vee-validate.js"></script>
    <script src="<?php echo $dominio_publico?>js/vee-validate/dist/locale/es.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $dominio_publico?>js/vue-form-generator/vfg.css">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WWP64FV');</script>
    <!-- End Google Tag Manager -->

    <!-- Facebook Pixel Code -->
    <!--script>
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
    /></noscript-->
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

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WWP64FV"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_script_body)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_script_body);
    }
    ?>



    <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">

                    <center><h2 class="title"><?php echo $titulo ?></strong></h2></center>

                    <?php if ($Solicitud->rtf_mensaje_resultado_de_inscripcion <> '') { ?>
                      <?php echo $Solicitud->rtf_mensaje_resultado_de_inscripcion ?>
                    <?php }
                    else { ?>
                      <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $mensaje_box ?>
                      </div>

                      <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->canal_de_recepcion_del_curso_id == 9) { ?>
                        <a href="https://www.instagram.com/cursodegnosis2/" target="_blank">
                          <div class="img-ancho-total img-responsive hidden-xs">
                            <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                            <img src="<?php echo $dominio_publico?>/img/instagram-icon.png">
                            <?php echo __('Curso por Instagram'); ?> 
                            </p>
                          </div>
                          <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                            <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                            <img src="<?php echo $dominio_publico?>/img/instagram-icon.png" style="width: 35px">
                            <?php echo __('Curso por Instagram'); ?> 
                            </p>
                          </div>
                        </a>
                        <p><h3><?php echo __('Para terminar tiénes que seguír nuestra cuenta de Instagram') ?><a href="<?php echo $Solicitud->url_enlace_cuenta_de_instagram ?>" target="_blank"><?php echo $Solicitud->nombre_de_usuario_instagram ?></a></h3> <?php echo __('Allí recibirás las lecciones semanalmente en sesiones en vivo, podrás hacer tus preguntas, entregaremos dinámicas, videos y material complementario. Todo desde la misma cuenta.') ?>
                        </p>
                        <p><?php echo __('Si queres recibir notificaciones y material extra por WhatsApp segui este enlace para que te agreguemos a una lista de distribución:') ?> <a href="https://api.whatsapp.com/send?phone=<?php echo $Solicitud->celular_responsable_de_inscripciones ?>&text=<?php echo __('Hola quiero suscribirme a las notificaciones WhatsApp del curso de autoconocimiento por Instagram.') ?>" target="_blank"><?php echo __('WhatsApp de notificaciones') ?></a></p>
                        <p><?php echo __('Si tenes alguna duda o consulta, hablá con uno de nuestros asistentes a este WhatsApp:') ?> <a href="https://api.whatsapp.com/send?phone=<?php echo $Solicitud->celular_responsable_de_inscripciones ?>&text=<?php echo __('Hola en relación al curso de autoconocimiento por Instagram quisiera consultar lo siguiente...') ?>" target="_blank"><?php echo __('Info por Whatsapp') ?></a>.
                        </p>

                          <br><br>
                      <?php }
                      else { ?>
                        <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->canal_de_recepcion_del_curso_id == 1) { ?>
                          <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(8,184,37); background: linear-gradient(311deg, rgba(8,184,37,1) 0%, rgba(33,235,33,1) 21%, rgba(97,252,125,1) 100%);">
                          <img src="<?php echo $dominio_publico?>/img/whatsapp-icon.png" style="width: 35px">
                          <?php echo __('Curso por WhatsApp'); ?>
                          </p>
                          <h3><?php echo __('messages.tit_requisito_cursos_whatsapp') ?></h3>
                          <p><?php echo __('messages.tit_info_requisito_cursos_whatsapp') ?></p>
                        <?php } ?>

                        <?php if (isset($url_invitacion_grupo_facebook) and $url_invitacion_grupo_facebook == '') { ?>



                          <?php if ($url_youtube <> '') { ?>
                          <div class="alert alert-success alert-danger">
                              <h4 class="pull-right"><img src="<?php echo $dominio_publico?>img/youtube-icon.png" style="margin-top: -5px; margin-bottom: 10px; " class="hidden-xs"> </h4>
                                  <a href="<?php echo $url_youtube ?>?sub_confirmation=1">
                                    <button type="button" class="btn btn-warning" style="background-color: #ff0000">
                                      <img src="<?php echo $dominio_publico?>img/youtube-icon-c.png" style="height: : 25px;">
                                      <?php echo __('Suscribete a nuestro canal de Youtube') ?>
                                      </button>
                                  </a>
                          </div>
                          <?php } ?>


                          <div class="alert alert-success alert-dismissible">
                              <h4 class="pull-right"><img src="<?php echo $dominio_publico?>img/whatsapp-icon.png"> WhatsApp</h4>
                              <?php if ($url_invitacion_grupo_whatsapp <> '') { ?>
                                  <a class="btn btn-default" href="<?php echo $url_invitacion_grupo_whatsapp ?>" role="button" style="white-space: normal;">
                                      <img src="<?php echo $dominio_publico?>img/whatsapp-icon.png" style="width: 25px">
                                      <?php echo __('Sumate a nuestro Grupo de') ?> Whatsapp
                                  </a>
                              <br><br>
                              <?php } ?>
                              <a class="btn btn-default" href="<?php echo $Solicitud->url_contacto_whatsapp_form() ?>" role="button" style="white-space: normal;">
                                  <img src="<?php echo $dominio_publico?>img/whatsapp-icon.png" style="width: 25px">
                                  <?php echo __('Quieres escribirnos? envianos un WhatsApp') ?>
                              </a>
                          </div>

                          <?php if ($url_fanpage <> '') { ?>
                              <div class="alert alert-info alert-dismissible">
                                  <h4 class="pull-right"><img src="<?php echo $dominio_publico?>img/facebook-icon.png"> Facebook</h4>

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

                        <?php }
                        else {?>
                            <h4><?php echo __('Confirme su inscripción ingresando al grupo de facebook en el siguiente botón') ?></h4>
                            <div class="alert alert-info alert-dismissible">
                                <h4 class="pull-right"><img src="<?php echo $dominio_publico?>img/facebook-icon.png"> Facebook</h4>
                                  <a class="btn btn-primary" href="<?php echo $url_invitacion_grupo_facebook ?>" role="button" style="white-space: normal;">
                                      <img src="<?php echo $dominio_publico?>img/facebook-icon.png" style="width: 25px">
                                      <?php echo __('Clic aquí').' - '.__('Curso de Auto-Conocimiento') ?> Facebook
                                  </a>
                            </div>
                        <?php } ?>

                      <?php } ?>

                      <br>

                      <?php if (!isset($no_btn)) { ?>
                      <a href="<?php echo $url_form_inscripcion ?>">
                        <button type="button" class="btn btn-success btn-lg center-block" style="white-space: normal;"><?php echo __('Quieres registrar a otra persona ?') ?></button>
                      </a>
                      <?php } ?>

                    <?php } ?>





              </div>



            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="<?php echo $dominio_publico?>templates/2/vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="<?php echo $dominio_publico?>templates/2/vendor/select2/select2.min.js"></script>
    <script src="<?php echo $dominio_publico?>templates/2/vendor/datepicker/moment.min.js"></script>
    <script src="<?php echo $dominio_publico?>templates/2/vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="<?php echo $dominio_publico?>templates/2/js/global.js"></script>

    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo $dominio_publico?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <?php if (strlen($Solicitud->url_redireccionar_automaticamente_al_enlace) > 5) { ?>
      <script type="text/javascript">
        setTimeout("location.href='<?php echo $Solicitud->url_redireccionar_automaticamente_al_enlace ?>'",5000)
      </script>
    <?php } ?>

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->


</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->
