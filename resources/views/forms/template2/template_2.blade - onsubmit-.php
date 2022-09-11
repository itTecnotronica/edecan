<?php
use \App\Http\Controllers\SolicitudController; 

$SolicitudController = new SolicitudController;

$idioma_por_pais = $Solicitud->idioma_por_pais();
$pais_id = $idioma_por_pais->pais_id;
$locale_vee_validate = 'en';

if ($Solicitud->idioma_id <> '') {
    $idioma = $Solicitud->idioma->mnemo;           
    $locale_vee_validate = $Solicitud->idioma->locale_vee_validate;             
    App::setLocale($idioma);  
}
else {
  if ($idioma_por_pais->idioma_id <> '') {
      $idioma = $idioma_por_pais->idioma->mnemo;    
      $locale_vee_validate = $idioma_por_pais->idioma->locale_vee_validate;                    
      App::setLocale($idioma);  
  }
}

$cod_pais = '';
$cod_pais_tel = 'null';
if ($idioma_por_pais->pais->mnemo <> '') {
  $cod_pais = $idioma_por_pais->pais->mnemo;
  $cod_pais_tel = "'".$idioma_por_pais->pais->codigo_tel."'";
}

function quitar_www($url) {
  $url = str_replace('www.', '', $url);
  $url = str_replace('http://', '', $url);
  $url = str_replace('https://', '', $url);
  return $url;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

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
    <meta name="description" content="Gnosis, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta name="author" content="gnosis.is">
    <meta name="keywords" content="Gnosis, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta property="og:title" content="Gnosis, <?php echo $Solicitud->descripcion_sin_estado() ?>" />
    <meta property="og:url" content="<?php echo $Solicitud->url_form_inscripcion() ?>" />
    <meta property="og:description" content="Gnosis, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta property="og:image" content="<?php echo env('PATH_PUBLIC')?>/img/sol-de-acuario-chico.jpg">

    <!-- Title Page-->
    <title><?php echo $Solicitud->descripcion_sin_estado() ?></title>

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

    <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>node_modules/intl-tel-input/build/css/intlTelInput.css">
 

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
      fbq('init', '465089661042722');
      fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=465089661042722&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->

    <?php  
    if (isset($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook); 
    }
    ?> 
    
</head>

<body>
  
    <div class="page-wrapper bg-gra-02 p-t-20 p-b-100 font-poppins" <?php echo $style_body ?>>
                     <center> <img class="sol-de-acuario-top img-responsive" src="<?php echo env('PATH_PUBLIC')?>/img/sol-de-acuario-chico-isologo.png" alt="GNOSIS" title="GNOSIS"></center>
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                      
                    <center><h2 class="title"><?php echo $titulo ?></h2></center>
                    <?php echo $titulo_fecha_inicio ?>
                    <center><p class="subtitulo-cursos"><?php echo $subtitulo ?></p></center>

                    <?php echo $imagen ?>

                    <!-- CURSO POR WHATSAPP -->
                    <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->sino_el_curso_es_por_whatsapp == 'SI' and ($Solicitud->sino_el_curso_es_por_classroom == '' or $Solicitud->sino_el_curso_es_por_classroom == 'NO')) { ?>
                      <div class="img-ancho-total img-responsive hidden-xs">
                        <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(8,184,37); background: linear-gradient(311deg, rgba(8,184,37,1) 0%, rgba(33,235,33,1) 21%, rgba(97,252,125,1) 100%);">
                        <img src="<?php echo env('PATH_PUBLIC')?>/img/whatsapp-icon.png">
                        <?php echo __('Curso por'); ?> WhatsApp
                        </p>
                      </div>    
                      <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                        <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(8,184,37); background: linear-gradient(311deg, rgba(8,184,37,1) 0%, rgba(33,235,33,1) 21%, rgba(97,252,125,1) 100%);">
                        <img src="<?php echo env('PATH_PUBLIC')?>/img/whatsapp-icon.png" style="width: 35px">
                        <?php echo __('Curso por'); ?> WhatsApp
                        </p>
                      </div>                    
                    <?php } ?>

                    <!-- CURSO POR FACEBOOK -->
                    <?php if ($Solicitud->tipo_de_evento->id == 3 and $Solicitud->sino_el_curso_es_por_facebook == 'SI') { ?>
                      <div class="img-ancho-total img-responsive hidden-xs">
                        <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(8,18,184);
background: linear-gradient(311deg, rgba(8,18,184,1) 0%, rgba(33,75,235,1) 21%, rgba(15,52,193,1) 100%);">
                        <img src="<?php echo env('PATH_PUBLIC')?>/img/facebook-icon.png">
                        <?php echo __('Curso por'); ?> Facebook
                        </p>
                      </div>    
                      <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                        <p style="font-size: 15px; font-weight: bold; color: #FFF; text-align: center; padding: 5px; background: rgb(8,18,184);
background: linear-gradient(311deg, rgba(8,18,184,1) 0%, rgba(33,75,235,1) 21%, rgba(15,52,193,1) 100%);">
                        <img src="<?php echo env('PATH_PUBLIC')?>/img/facebook-icon.png" style="width: 35px">
                        <?php echo __('Curso por'); ?> Facebook
                        </p>
                      </div>                    
                    <?php } ?>

                    <!-- CURSO POR INSTAGRAM -->
                    <?php if ($Solicitud->id == 5033) { ?>
                      <div class="img-ancho-total img-responsive hidden-xs">
                        <p style="font-size: 27px; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                        <img src="<?php echo env('PATH_PUBLIC')?>/img/instagram-icon.png">
                        <?php echo __('Curso por'); ?> Instagram
                        </p>
                      </div>    
                      <div class="img-ancho-total hidden-sm hidden-md hidden-lg">
                        <p style="font-size: 15px; font-weight: bold; text-align: center; color: #FFF; padding: 5px; background: rgb(63,94,251); background: radial-gradient(circle, rgba(63,94,251,1) 0%, rgba(252,70,107,1) 100%);">
                        <img src="<?php echo env('PATH_PUBLIC')?>/img/instagram-icon.png" style="width: 35px">
                        <?php echo __('Curso por'); ?> Instagram
                        </p>
                      </div>                    
                    <?php } ?>

                    <?php echo $resumen ?>

                    <?php if ($Solicitud->tipo_de_evento->id <> 4) { ?>
                    <h3><?php echo __('Completa tus datos para reservar un lugar!') ?></h3>
                    <?php } ?>



                          <!-- CAMPOS DE FORMULARIO -->      
                            <div class="panel-body" id="app-form">
                              {!! Form::open(array
                                (
                                'action' => 'FormController@RegistrarInscripcion', 
                                'role' => 'form',
                                'method' => 'POST',
                                'id' => "form_inscripcion",
                                'enctype' => 'multipart/form-data',
                                'class' => 'form-horizontal',
                                'ref' => 'form',
                                'onsubmit' => 'return validar()'
                                )) 
                              !!}
 
                                <div class="vue-form-generator">
                                    <fieldset>


                                        <div class="form-group required">
                                          <label for="nombre"><?php echo __('Nombre') ?></label>                          
                                          <input type="text" class="form-control" id="nombre" name="nombre" placeholder="<?php echo __('Nombre') ?>" maxlength="45">       
                                          <span id="_nombre" class="text-danger"></span>
                                          <div class="bg-danger" id="_nombre">{{$errors->first('nombre')}}</div>
                                        </div>

                                        <input type="submit" value="enviar">


                                    </fieldset>
                                    <!--div class="col-lg-12">            
                                      <pre>@{{ $data }}</pre>
                                    </div-->                                    
                                </div>


                                {!! Form::close() !!}
                            </div>
                          <!-- CAMPOS DE FORMULARIO -->

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

    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->


<!-- INICIO APP app-form -->
    <script type="text/javascript">
        const config = {
          locale: '<?php echo $locale_vee_validate ?>', 
        };
        //moment.locale('es');
        //console.log(moment());
        Vue.use(VeeValidate, config);

        var app = new Vue({
          el: '#app-form',

          data: {
            apellido: null,
            nombre: null,
            cod_pais: null,
            celular: null,
            celular_completo: null,
            pais_id: <?php echo $pais_id ?>,
            canal_de_recepcion_del_curso_id: 1,
            email_correo: null,
            consulta: null,
            ciudad: <?php echo $ciudad ?>,
            fecha_de_evento_id: null,
            sino_notificar_proximos_eventos: true,
            acepto_politica_de_privacidad: false,
            mensaje_error: '',
            desabilitar: <?php echo $deshabilitar_formulario; ?>
          },

          methods: {
            validateBeforeSubmit() {
              this.$validator.validateAll().then((result) => {
                if (result) {
                  // eslint-disable-next-line
                  $('#form_inscripcion').submit()
                  return;
                }
              });
            }            
              
          },

          filters: {
            formatoMoneda: function (value) {
              let val = (value/1).toFixed(2).replace('.', ',')
              return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
          }

        })

      function validar() {
        enviar = true
        if ($('#nombre').val() == '') {
          enviar = false
          $('#_nombre').html('Campo Obligarotio')
        }
        return enviar
      }
    </script>
<!-- FIN APP app-form -->



 
<script src="<?php echo env('PATH_PUBLIC')?>node_modules/intl-tel-input/build/js/intlTelInput.js"></script>
<script>
var input = document.querySelector("#celular");
var iti = window.intlTelInput(input, {
  utilsScript: "<?php echo env('PATH_PUBLIC')?>node_modules/intl-tel-input/build/js/utils.js?1585994360633", // just for formatting/
  //placeholderNumberType: "FIXED_LINE",
  separateDialCode: true,
  preferredCountries: []
});
  input.addEventListener("countrychange", function() {
    if (iti.getNumber() != '') {
     app["celular"] = input.value;
     app["celular_completo"] = iti.getNumber();
    }
  });
  iti.setCountry("<?php echo $cod_pais ?>");
</script> 



</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->
