<?php
use \App\Http\Controllers\SolicitudController;

$SolicitudController = new SolicitudController;

$idioma_por_pais = $Solicitud->idioma_por_pais();

$idioma = $Solicitud->idioma->mnemo;
App::setLocale($idioma);

$locale_vee_validate = 'en';
if ($Solicitud->idioma->locale_vee_validate <> '') {
  $locale_vee_validate = $Solicitud->idioma->locale_vee_validate;
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
    <meta name="description" content="Gnosis, <?php echo __('Encuesta de Satisfacción') ?>">
    <meta name="author" content="gnosis.is">
    <meta name="keywords" content="Gnosis, <?php echo $Solicitud->descripcion_sin_estado() ?>">
    <meta property="og:title" content="Gnosis, <?php echo __('Encuesta de Satisfacción') ?>" />
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

    <!-- vue.js -->
    <script src="<?php echo env('PATH_PUBLIC')?>js/vue/vue.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/vee-validate.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/locale/<?php echo $locale_vee_validate ?>.js"></script>
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

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook);
    }
    ?>

    <script>
      fbq('trackCustom', 'PollView');
    </script>

</head>

<body>

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_script_body)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_script_body);
    }
    ?>


    <div class="page-wrapper bg-gra-02 p-t-20 p-b-100 font-poppins">
                     <center> <img class="sol-de-acuario-top img-responsive" src="<?php echo env('PATH_PUBLIC')?>/img/sol-de-acuario-chico-isologo.png" alt="GNOSIS" title="GNOSIS"></center>
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                    <?php if ($cant_encuestas_hechas == 0) { ?>
                    <center><h2 class="title"><?php echo __('Encuesta de Satisfacción') ?></h2></center>
                    <center><h3 class="subtitulo-cursos"><?php echo $titulo ?></h3></center>

                    <h4><?php echo __('¡Ayúdanos a mejorar tu próxima experiencia!') ?></h4>


                          <!-- CAMPOS DE FORMULARIO -->
                            <div class="panel-body" id="app-form">
                              {!! Form::open(array
                                (
                                'action' => 'EncuestaController@RegistrarEncuesta',
                                'role' => 'form',
                                'method' => 'POST',
                                'id' => "form_inscripcion",
                                'enctype' => 'multipart/form-data',
                                'class' => 'form-horizontal',
                                'ref' => 'form',
                                '@submit.prevent' => "validateBeforeSubmit"
                                ))
                              !!}

                                <div class="vue-form-generator">

                                    <fieldset>

                                      <hr>
                                      <p>1) <?php echo __('¿Asistió a la conferencia inicial?'); ?></p>
                                      <div class="radio">
                                        <label class="radio-inline">
                                          <input type="radio" name="sino_asistio_a_la_conferencia_inicial" id="asistio1" value="SI" required="required">
                                          <?php echo __('SI'); ?>
                                        </label>
                                      </div>
                                      <div class="radio">
                                        <label class="radio-inline">
                                          <input type="radio" name="sino_asistio_a_la_conferencia_inicial" id="asistio2" value="NO" required="required">
                                          <?php echo __('NO'); ?>
                                        </label>
                                      </div>
                                      <hr>

                                      <p>2) <?php echo __('¿Participó antes de alguna conferencia gnóstica?'); ?></p>
                                      <div class="radio">
                                        <label>
                                          <input type="radio" name="sino_participo_antes_de_alguna_conferencia_gnostica" id="participo1" value="SI" required="required">
                                          <?php echo __('SI'); ?>
                                        </label>
                                      </div>
                                      <div class="radio">
                                        <label>
                                          <input type="radio" name="sino_participo_antes_de_alguna_conferencia_gnostica" id="participo2" value="NO" required="required">
                                          <?php echo __('NO'); ?>
                                        </label>
                                      </div>
                                      <hr>

                                      <p>3) <?php echo __('En cuanto al evento y a la conferencia'); ?>:</p>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_evento_no_fue_lo_que_esperaba" value="S">
                                            <?php echo __('No fue lo que esperaba'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_evento_demasiado_imprecisa" value="S">
                                            <?php echo __('Demasiado imprecisa'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_evento_poco_convincente" value="S">
                                            <?php echo __('Poco convincente'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_evento_demasiado_extensa" value="S">
                                            <?php echo __('Demasiado extensa'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_evento_no_se_cumplio_el_horario" value="S">
                                            <?php echo __('No se cumplió el horario'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_evento_estuvo_bien" value="S">
                                            <?php echo __('Estuvo bien'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_evento_estuvo_muy_bien" value="S">
                                            <?php echo __('Estuvo muy bien'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_evento_fue_clara" value="S">
                                            <?php echo __('Fue clara'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_evento_interesante" value="S">
                                            <?php echo __('Interesante'); ?>
                                        </label>
                                      </div>
                                      <hr>



                                      <p>4) <?php echo __('Sobre la comunicación previa al evento'); ?>:</p>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_comunicacion_fue_satisfactoria" value="S">
                                            <?php echo __('La comunicación fue satisfactoria'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_comunicacion_las_respuestas_demoraban_mucho" value="S">
                                            <?php echo __('Las respuestas demoraban mucho'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_comunicacion_el_trato_fue_ameno_y_cordial" value="S">
                                            <?php echo __('El trato fue ameno y cordial'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_comunicacion_me_resulto_un_poco_insistente" value="S">
                                            <?php echo __('Me resultó un poco insistente'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_comunicacion_me_hubiese_gustado_mas_contenidos" value="S">
                                            <?php echo __('Me hubiese gustado más contenidos o información'); ?>
                                        </label>
                                      </div>

                                      <hr>
                                      <p>5) <?php echo __('Sobre la continuidad y recomendación a otros'); ?>:</p>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_continuidad_estoy_interesado_en_continuar" value="S">
                                            <?php echo __('Estoy interesado en continuar con estos cursos'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_continuidad_recomendaría_este_evento" value="S">
                                            <?php echo __('Recomendaría este evento a un amigo'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_continuidad_me_resulto_llamativo_que_sea_gratuito" value="S">
                                            <?php echo __('Me resultó llamativo que las actividades sean totalmente gratuitas'); ?>
                                        </label>
                                      </div>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="sino_continuidad_no_es_lo_que_estoy_buscando" value="S">
                                            <?php echo __('No es la propuesta de capacitación que estoy buscando en este momento'); ?>
                                        </label>
                                      </div>

                                      <hr>
                                      <p>6) <?php echo __('Con el objeto de mejorar nuestra propuesta cultural nos gustaría conocer sus sugerencias') ?></p>
                                        <textarea class="form-control" id="sugerencias" name="sugerencias" v-model="consulta" placeholder="<?php echo __('sugerencias') ?>" maxlength="300" v-bind:disabled="desabilitar"></textarea>
                                        <span v-show="errors.has('consulta')" class="text-danger">@{{ errors.first('consulta') }}</span>

                                      <input type="hidden" name="inscripcion_id" value="<?php echo $Inscripcion->id ?>">





                                        <section v-show="errors.count()>0">
                                          <div class="row">
                                            <div class="col-xs-12">
                                              <br>
                                              <div class="alert bg-danger alert-dismissible">
                                                <h5 class="text-danger tit-lista-de-errores"><i class="glyphicon glyphicon-warning-sign "></i> Error</h5>
                                                <ul class="text-danger lista-de-errores" >
                                                  <li v-for="error in errors.all()">@{{ error }}</li>
                                                </ul>
                                              </div>
                                            </div>
                                          </div>
                                        </section>

                                        <br><br>
                                        <div class="form-group">
                                            <input type="hidden" name="solicitud_id" value="<?php echo $Inscripcion->id ?>">
                                            <input type="hidden" name="solicitud_id" value="<?php echo $hash ?>">
                                            <center><button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="desabilitar"> <?php echo __('Enviar') ?> </button></center>
                                        </div>


                                    </fieldset>
                                    <!--div class="col-lg-12">
                                      <pre>@{{ $data }}</pre>
                                    </div-->
                                </div>


                                {!! Form::close() !!}
                            </div>
                          <!-- CAMPOS DE FORMULARIO -->
                <?php
                }
                else {
                  echo '<h2>'.__('Ud. ya ha completado esta encuesta, agradecemos su colaboración!').'</h2>';
                } ?>
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
            celular: null,
            email_correo: null,
            consulta: null,
            ciudad: null,
            fecha_de_evento_id: null,
            sino_notificar_proximos_eventos: true,
            acepto_politica_de_privacidad: false,
            mensaje_error: '',
            desabilitar: false
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
    </script>
<!-- FIN APP app-form -->

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->
