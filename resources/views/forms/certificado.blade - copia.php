<?php
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController();

$Idioma_por_pais = $Inscripcion->solicitud->idioma_por_pais();

$nombre_de_la_institucion = $Idioma_por_pais->nombre_de_la_institucion;
$denominacion_de_voucher = $Idioma_por_pais->denominacion_de_voucher;

$idioma = $Idioma_por_pais->idioma->mnemo;
App::setLocale($idioma);    

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0014)about:internet -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
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

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--Fireworks CS6 Dreamweaver CS6 target.  Created Thu Sep 03 15:58:20 GMT-0400 (Hora de verano del Este) 2020-->


</head>
<body bgcolor="#ffffff">
  <center>
    <table class="tabla-certificado" border="0" cellpadding="0" cellspacing="0" width="1000">
    <!-- fwtable fwsrc="<?php echo env('PATH_PUBLIC')?>img/certificados/1/Certificados Cursos 2020 - Nando.png" fwpage="Página 1" fwbase="certificado.png" fwstyle="Dreamweaver" fwdocid = "1553567897" fwnested="0" -->
      <tr>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="13" height="1" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="771" height="1" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="17" height="1" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="135" height="1" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="51" height="1" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="13" height="1" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="1" alt="" /></td>
      </tr>

      <tr>
       <td colspan="6"><img name="certificado_r1_c1" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r1_c1.png" width="1001.5" height="13" id="certificado_r1_c1" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="13" alt="" /></td>
      </tr>
      <tr>
       <td rowspan="3" colspan="2"  class="fondo-azul"><img name="certificado_r2_c1" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r2_c1.png" width="784" height="106" id="certificado_r2_c1" alt="" /></td>
       <td colspan="3"  class="fondo-azul"><img name="certificado_r2_c3" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r2_c3.png" width="203" height="24" id="certificado_r2_c3" alt="" /></td>
       <td rowspan="6"><img name="certificado_r2_c6" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r2_c6.png" width="13" height="258" id="certificado_r2_c6" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="24" alt="" /></td>
      </tr>
      <tr>
       <td colspan="3" class="fondo-azul"><img name="certificado_r3_c3" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r3_c3.png" width="203" height="46" id="certificado_r3_c3" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="46" alt="" /></td>
      </tr>
      <tr>
       <td rowspan="2"><img name="certificado_r4_c3" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r4_c3.png" width="17" height="136" id="certificado_r4_c3" alt="" /></td>
       <td rowspan="2"><img name="certificado_r4_c4" src="<?php echo $dir_imagen_url ?>" width="135" height="136" id="certificado_r4_c4" alt="" /></td>
       <td rowspan="2"><img name="certificado_r4_c5" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r4_c5.png" width="51" height="136" id="certificado_r4_c5" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="36" alt="" /></td>
      </tr>
      <tr>
       <td rowspan="3" colspan="2"><img name="certificado_r5_c1" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r5_c1.png" width="784" height="152" id="certificado_r5_c1" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="100" alt="" /></td>
      </tr>
      <tr>
       <td colspan="3"><img name="certificado_r6_c3" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r6_c3.png" width="203" height="21" id="certificado_r6_c3" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="21" alt="" /></td>
      </tr>
      <tr>
       <td colspan="3"><img name="certificado_r7_c3" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r7_c3.png" width="203" height="31" id="certificado_r7_c3" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="31" alt="" /></td>
      </tr>
      <tr>
       <td><img name="certificado_r8_c1" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r8_c1.png" width="13" height="83" id="certificado_r8_c1" alt="" /></td>
       <td rowspan="2" colspan="4">
         <div style="padding: 10px; width:954px;height:345px">

          <p class="titulo-nombre"><?php echo mb_strtoupper($Inscripcion->nombre, 'UTF-8'); ?> <?php echo mb_strtoupper($Inscripcion->apellido, 'UTF-8'); ?></p>

          <p class="texto1"><?php echo $texto1; ?></p>

          <p>
            <center><img class="img_firma" src="<?php echo $firma ?>"></center>
          </p>
           
         </div>
       </td>
       <td><img name="certificado_r8_c6" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r8_c6.png" width="13" height="83" id="certificado_r8_c6" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="83" alt="" /></td>
      </tr>
      <tr>
       <td><img name="certificado_r9_c1" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r9_c1.png" width="13" height="282" id="certificado_r9_c1" alt="" /></td>
       <td><img name="certificado_r9_c6" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r9_c6.png" width="13" height="282" id="certificado_r9_c6" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="282" alt="" /></td>
      </tr>
      <tr>
       <td colspan="6"><img name="certificado_r10_c1" src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/certificado_r10_c1.png" width="1000" height="71" id="certificado_r10_c1" alt="" /></td>
       <td><img src="<?php echo env('PATH_PUBLIC')?>img/certificados/1/spacer.gif" width="1" height="71" alt="" /></td>
      </tr>
    </table>
  </center>
</body>
</html>






