<?php
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController();

$Idioma_por_pais = $Inscripcion->solicitud->idioma_por_pais();

$nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;
$denominacion_de_voucher = $Idioma_por_pais->denominacion_de_voucher;

$idioma = $Idioma_por_pais->idioma->mnemo;
App::setLocale($idioma);    

?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/<?php echo env('PATH_PUBLIC')?>img/certificados/1/<?php echo env('PATH_PUBLIC')?>img/certificados/1/<?php echo env('PATH_PUBLIC')?>img/certificados/1/https://www.googletagmanager.com/gtag/js?id=UA-46601315-3"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-46601315-3');
    </script>

    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Gnosis | {{ __('Certificado') }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}">
    <meta name="author" content="gnosis.is">
    <meta name="keywords" content="Gnosis | {{ __('Certificado') }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }} ">
    <meta property="og:title" content="Gnosis | {{ __('Certificado') }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}" />
    <meta property="og:url" content="https://www.gnosis.is" />
    <meta property="og:description" content="Gnosis | {{ __('Certificado') }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}">
    <meta property="og:image" content="<?php echo $dir_imagen_url ?>">

    <link href="<?php echo env('PATH_PUBLIC')?>img/certificados/2/style.css" rel="stylesheet" media="all">



    <!-- Title Page-->
    <title>Gnosis | {{ __('Certificado') }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}</title>

</head>

<body>

  <center>
    <table>
        <tr>
          <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/2/certificado_top.jpg" width="1000" height="212" alt=""/></td>
        </tr>
        <tr>
          <td>
            <p class="titulo-nombre"><?php echo $Inscripcion->LetraCapital($Inscripcion->nombre) ?> <?php echo $Inscripcion->LetraCapital($Inscripcion->apellido); ?></p>
          </td>
        </tr>
        <tr>
          <td>
            <img src="<?php echo env('PATH_PUBLIC')?>img/certificados/2/certificado_down.jpg" width="1000" height="417" alt=""/>
          </td>
        </tr>
    </table>
  </center>

</body>
</html>



