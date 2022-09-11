<?php
use \App\Http\Controllers\SolicitudController;

$SolicitudController = new SolicitudController;

$idioma_por_pais = $Solicitud->idioma_por_pais();
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

</head>

<body <?php echo $style_body ?>>

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_script_body)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_script_body);
    }
    ?>





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
                                '@submit.prevent' => "validateBeforeSubmit"
                                ))
                              !!}

                                <div class="vue-form-generator">
                                    <fieldset>

                                        <div class="form-group required">
                                          <label for="nombre"><?php echo __('Nombre') ?></label>
                                          <input v-validate="'required'" type="text" class="form-control" id="nombre" name="nombre" v-model="nombre" placeholder="<?php echo __('Nombre') ?>" data-vv-as="<?php echo __('Nombre') ?>"  required="required" v-bind:disabled="desabilitar" maxlength="45">
                                          <span v-show="errors.has('nombre')" class="text-danger">@{{ errors.first('nombre') }}</span>
                                        </div>


                                        <div class="form-group required">
                                          <label for="apellido"><?php echo __('Apellido') ?></label>
                                          <input v-validate="'required'" type="text" class="form-control" id="apellido" name="apellido" v-model="apellido" placeholder="<?php echo __('Apellido') ?>" data-vv-as="<?php echo __('Apellido') ?>"  required="required" v-bind:disabled="desabilitar" maxlength="45">
                                          <span v-show="errors.has('apellido')" class="text-danger">@{{ errors.first('apellido') }}</span>
                                        </div>

                                        <div class="form-group <?php echo $cel_requerido_class ?>">
                                          <label for="celular"><?php echo __('Nro de Teléfono Móvil (Celular), nro completo para Whatsapp con el +, por ej:') ?> <?php echo $Solicitud->idioma_por_pais()->pais->ejemplo_de_nro_de_celular_para_formulario; ?></label>
                                          <input v-validate="<?php echo $cel_requerido_v_validate ?>" type="text" class="form-control" id="celular" name="celular" v-model="celular" data-vv-as="<?php echo __('Celular') ?>"  placeholder="<?php echo $Solicitud->idioma_por_pais()->pais->ejemplo_de_nro_de_celular_para_formulario; ?>" <?php echo $cel_requerido_input ?> v-bind:disabled="desabilitar" maxlength="45">
                                          <p style="font-style: italic; font-size: 12px; font-weight: bold"><i class="fa fa-fw fa-question-circle"></i> <?php echo __('Si no sabes como completarlo, haz click aqui') ?> <a href="<?php echo $Solicitud->url_contacto_whatsapp_form() ?>" target="_blank">(<?php echo __('Enviar WhatsApp') ?>)</a> <?php echo __('para enviarnos un mensaje por Whatsapp y te asistiremos rápidamente') ?></p>
                                          <span v-show="errors.has('celular')" class="text-danger">@{{ errors.first('celular') }}</span>
                                        </div>

                                        <div class="form-group <?php echo $mail_requerido_class ?>">
                                          <label for="email_correo"><?php echo __('Correo Electrónico') ?></label>
                                          <input data-vv-as="<?php echo __('Correo Electrónico') ?>" type="text" class="form-control" id="email_correo" name="email_correo" v-model="email_correo" <?php echo $mail_requerido_input ?> placeholder="<?php echo __('Correo Electrónico') ?>" v-bind:disabled="desabilitar" maxlength="80">
                                          <span v-show="errors.has('email_correo')" class="text-danger">@{{ errors.first('email_correo') }}</span>
                                        </div>


                                          <?php
                                          // INICIO SINO ES CURSO ONLINE
                                          if ($Solicitud->tipo_de_evento_id == 3) {
                                          ?>
                                            <div class="form-group required">
                                              <label for="pais"><?php echo __('Pais') ?></label>
                                              <?php $paises = App::make('App\Http\Controllers\HomeController')->get_paises();?>
                                              <?php echo Form::select("pais_id", $paises, $Solicitud->pais_id, ['id' => "pais_id", 'class' => 'form-control', 'required' => 'required', 'v-model' => 'pais_id', 'v-validate' => "'required'", 'data-vv-as' => __('Pais')]); ?>
                                              <span v-show="errors.has('pais_id')" class="text-danger">@{{ errors.first('pais_id') }}</span>
                                            </div>
                                            <div class="form-group required">
                                              <label for="ciudad"><?php echo __('Ciudad') ?></label>
                                              <input v-validate="'required'" type="text" class="form-control" id="ciudad" name="ciudad" v-model="ciudad" placeholder="<?php echo __('ciudad') ?>" data-vv-as="<?php echo __('Ciudad') ?>" required="required" v-bind:disabled="desabilitar" maxlength="50">
                                              <span v-show="errors.has('ciudad')" class="text-danger">@{{ errors.first('ciudad') }}</span>
                                            </div>
                                          <?php } ?>

                                          <?php
                                          // RECOLECCION DE DATOS
                                          if ($Solicitud->tipo_de_evento_id == 4) {
                                          ?>
                                            <div class="form-group required">
                                              <label for="ciudad"><?php echo __('Ciudad mas cercana a Ud.') ?></label>
                                              <?php
                                              $localidades = App::make('App\Http\Controllers\HomeController')->get_localidadesConProvincia($Solicitud->pais_id);
                                              echo Form::select("localidad_id", $localidades, 1, ['id' => "localidad_id", 'class' => 'form-control', 'required' => 'required', 'v-validate' => "'required'", 'v-model' => 'localidad_id', 'data-vv-as' => __('Ciudad')]);
                                              ?>
                                              <span v-show="errors.has('localidad_id')" class="text-danger">@{{ errors.first('localidad_id') }}</span>
                                            </div>
                                          <?php } ?>


                                          <?php
                                          // INICIO SINO ES CURSO ONLINE
                                          if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 2) {
                                          ?>

                                            <h4><?php echo $mensaje_fecha_de_evento ?></h4>
                                            <div class="form-group required">
                                            <?php
                                            // INICIO SI NO ES CURSO ONLINE
                                            if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1)) {
                                              $type_opcion = 'radio';
                                              $required = 'required="required"';
                                              $required_vue = "'required'";
                                            }
                                            else {
                                              $type_opcion = 'checkbox';
                                              $required = '';
                                              $required_vue = '';
                                            }
                                            foreach ($Fechas_de_eventos as $Fecha_de_evento) {

                                              if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1)) {
                                                $nombre_campo = 'fecha_de_evento_id';
                                              }
                                              else {
                                                $nombre_campo = 'fecha_de_evento_id_'.$Fecha_de_evento->id;
                                              }

                                          ?>


                                              <div class="input-group input-radio">
                                                <span class="input-group-addon">
                                                  <?php
                                                    $class_agotado = '';
                                                    if ($Fecha_de_evento->sino_agotado == 'SI') {
                                                      $class_agotado = 'agotado';
                                                    }
                                                    else {
                                                  ?>
                                                  <input v-validate="<?php echo $required_vue  ?>" type="<?php echo $type_opcion ?>" id="fecha_de_evento_id" name="<?php echo $nombre_campo  ?>" v-model="<?php echo $nombre_campo  ?>" <?php echo $required  ?> value="<?php echo $Fecha_de_evento->id ?>" v-bind:disabled="desabilitar" data-vv-as="<?php echo __('Seleccione una opción') ?>">
                                                  <?php } ?>
                                                </span>
                                                <div class="fecha-de-evento-radio <?php echo $class_agotado ?>">
                                                  <?php if ($Fecha_de_evento->sino_agotado == 'SI') { ?>
                                                    <p class="bg-danger txt_agotado"><?php echo __('CUPO AGOTADO') ?></p>
                                                  <?php } ?>
                                                  <?php echo $Fecha_de_evento->armarDetalleFechasDeEventos('con_resumen')  ?>
                                                </div>
                                              </div><!-- /input-group -->


                                            <?php } ?>

                                              <div class="input-group input-radio">
                                                <span class="input-group-addon">
                                                  <input v-validate="<?php echo $required_vue  ?>" type="<?php echo $type_opcion ?>" id="fecha_de_evento_id" name="fecha_de_evento_id" v-model="fecha_de_evento_id" <?php echo $required  ?> value="NP" v-bind:disabled="desabilitar" data-vv-as="<?php echo __('Seleccione una opción') ?>">
                                                </span>
                                                <div class="fecha-de-evento-radio"><?php echo __('No puedo asistir a este horario pero quisiera me contacten mas adelante por otros días y horarios') ?></div>
                                              </div><!-- /input-group -->

                                            <span v-show="errors.has('fecha_de_evento')" class="text-danger">@{{ errors.first('fecha_de_evento') }}</span>

                                          </div>
                                        <?php
                                          }
                                        // FIN SINO ES CURSO ONLINE
                                        ?>


                                        <?php
                                        // SI NO ES RECOLECCION DE DATOS
                                        if ($Solicitud->tipo_de_evento_id <> 4) {
                                        ?>
                                        <div class="form-group">
                                          <label for="consulta"><?php echo __('Alguna pregunta para hacernos?') ?></label>
                                          <textarea class="form-control" id="consulta" name="consulta" v-model="consulta" placeholder="<?php echo __('tu consulta') ?>" maxlength="300" v-bind:disabled="desabilitar"></textarea>
                                          <span v-show="errors.has('consulta')" class="text-danger">@{{ errors.first('consulta') }}</span>
                                        </div>
                                        <?php } ?>

                                        <div class="form-group">
                                          <div class="checkbox">
                                            <label>
                                              <input type="checkbox" id="sino_notificar_proximos_eventos" name="sino_notificar_proximos_eventos" v-model="sino_notificar_proximos_eventos" placeholder="Correo" v-bind:disabled="desabilitar"><?php echo __('Me gustaría recibir información sobre los próximos cursos y eventos gratuitos') ?>
                                            </label>
                                            <span v-show="errors.has('sino_notificar_proximos_eventos')" class="text-danger">@{{ errors.first('sino_notificar_proximos_eventos') }}</span>
                                          </div>
                                        </div>

                                        <?php  if ($acepto_politica_de_privacidad) { ?>
                                        <div class="form-group">
                                          <div class="checkbox">
                                            <label>
                                              <input type="checkbox" id="acepto_politica_de_privacidad" name="acepto_politica_de_privacidad" v-validate="'required'" v-model="acepto_politica_de_privacidad" v-bind:disabled="desabilitar" required="required" data-vv-as="<?php echo __('Acepto la política de privacidad') ?>">
                                              <?php echo __('Acepto la política de privacidad') ?>
                                            </label>
                                            <span v-show="errors.has('acepto_politica_de_privacidad')" class="text-danger">@{{ errors.first('acepto_politica_de_privacidad') }}</span>
                                          </div>
                                        </div>
                                        <h5><u><?php echo __('Política de privacidad') ?></u></h5>
                                        <p style="text-align: justify;"><?php echo $politica_de_privacidad ?></p>
                                        <?php } ?>


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
                                            <?php
                                            if ($Solicitud->label_boton_enviar <> '') {
                                              $labelSubmit = $Solicitud->label_boton_enviar;
                                            }
                                            else {
                                              $labelSubmit = 'Inscribirme';
                                            }
                                            ?>
                                            <div class="form-group">
                                                <input type="hidden" name="solicitud_id" value="<?php echo $Solicitud->id ?>">
                                                <input type="hidden" name="campania_id" value="<?php echo $campania_id ?>">
                                                <center><button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="desabilitar"> <?php echo __($labelSubmit) ?> </button></center>
                                            </div>
                                        </div>



                                    </fieldset>
                                    <!--div class="col-lg-12">
                                      <pre>@{{ $data }}</pre>
                                    </div-->
                                </div>


                                {!! Form::close() !!}
                            </div>
                          <!-- CAMPOS DE FORMULARIO -->




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
            pais_id: null,
            ciudad: null,
            fecha_de_evento_id: null,
            sino_notificar_proximos_eventos: true,
            acepto_politica_de_privacidad: false,
            mensaje_error: '',
            desabilitar: <?php echo $deshabilitar_formulario; ?>,
            guardar: null
          },

          methods: {

            validateBeforeSubmit() {
              //this.$Validator.localize('en', dict);
              this.$validator.validateAll().then((result) => {
                if (result) {
                  // eslint-disable-next-line
                  $('#form_inscripcion').submit()
                  return;
                }
                //console.log(this.$Validator.localize('en', dict))
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
