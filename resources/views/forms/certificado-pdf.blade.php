<?php

error_reporting(E_ALL ^ E_DEPRECATED);

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

    <link href="<?php echo env('PATH_PUBLIC')?>css/certificado_1.css" rel="stylesheet" media="all">



    <!-- Title Page-->
    <title>Gnosis | {{ __('Certificado') }} | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}</title>

</head>

<body>

  <center>


    <table  border="0" cellpadding="0" cellspacing="0" style="margin-left: 60px;">
      <tbody>
        <tr>
          <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r1_c1.png" alt=""/></td>
        </tr>
        <tr>
          <td >

            <table border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r2_c1.png" alt=""/></td>
                  <td ><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r2_c2<?php echo $mnemo_lang ?>.png"  alt=""/></td>
                  <td ><img src="<?php echo $dir_imagen_url ?>"  alt=""/></td>
                  <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r2_c8.png" alt=""  style="margin-left: 2px" /></td>
                </tr>
              </tbody>
            </table>

          </td>
        </tr>
        <tr>
          <td>

            <table  border="0" cellspacing="0" cellpadding="0" >
              <tbody>
                <tr>
                  <td >

                    <table  border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r7_c1.png"   alt=""/></td>
                        </tr>
                        <tr bgcolor="#F5D402">
                          <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r8_c1.png"   alt=""/></td>
                        </tr>
                      </tbody>
                    </table>

                  </td>
                  <td>

                   <div class="cuerpo-de-texto">

                    <p class="titulo-nombre"><?php echo mb_strtoupper($Inscripcion->nombre, 'UTF-8'); ?> <?php echo mb_strtoupper($Inscripcion->apellido, 'UTF-8'); ?></p>

                    <p class="texto1"><?php echo $texto1; ?></p>

                    <p>
                      <center><img class="img_firma" src="<?php echo $firma ?>"></center>
                    </p>
                     
                   </div>

                  </td>
                  <td >

                    <table  border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r7_c8.png"   alt=""/></td>
                        </tr>
                        <tr>
                          <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r8_c8.png"   alt=""/></td>
                        </tr>
                      </tbody>
                    </table>

                  </td>
                </tr>
              </tbody>
            </table>

          </td>
        </tr>
        <tr>
          <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r11_c1<?php echo $mnemo_lang ?>.png"  alt=""/></td>
        </tr>
      </tbody>
    </table>
  </center>



</body>
</html>



