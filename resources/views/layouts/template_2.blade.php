<!DOCTYPE html>
<html lang="pt-BR">

<head>
  
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WWP64FV');</script>
    <!-- End Google Tag Manager -->


    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta name="author" content="<?php echo $nombre_institucion ?>.is">
    <meta name="keywords" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta property="og:title" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?>" />
    <meta property="og:url" content="<?php echo $Solicitud->url_form_inscripcion() ?>" />
    <meta property="og:description" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta property="og:image" content="<?php echo $imagen_chica ?>">

    <!-- Title Page-->
    <title>@yield('titulo')</title>

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
    <script src="<?php echo $dominio_publico?>js/vee-validate/dist/locale/<?php echo $locale_vee_validate ?>.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $dominio_publico?>js/vue-form-generator/vfg.css">

    <link rel="stylesheet" href="<?php echo $dominio_publico?>node_modules/intl-tel-input/build/css/intlTelInput.css">
 


    <?php  
    if (isset($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook); 
    }
    ?> 
</head>

<body <?php echo $style_body ?>>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WWP64FV"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    
    @yield('contenido')


</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->
