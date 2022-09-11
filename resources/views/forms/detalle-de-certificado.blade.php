<?php
App::setLocale($idioma);   


use \App\Http\Controllers\GenericController; 
$gCon = new GenericController();

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
    <title>Gnosis | {{ __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento) }}</title>

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
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                  <div class="row">

                    <div class="col-xs-12 col-md-6 col-lg-4">
                      <center><img src="<?php echo env('PATH_PUBLIC')?>img/sol-de-acuario.jpg" class="sol-de-acuario-voucher img-responsive"></center>
                    </div>
                    <div class="col-xs-12 col-md-6 col-lg-8">
                    <center><h2 class="title">{{ __('Detalle del certificado') }}<br><?php echo $Inscripcion->nombre; ?> <?php echo $Inscripcion->apellido; ?></h2></center>
                    <center><h4>{{ __('Numero de certificado') }}: <?php echo $Inscripcion->id; ?></h4></center>
                    </div>
                              
                      
                    <div class="col-xs-12 col-md-12 col-lg-12">
                      
                        
                        <!-- DATOS DE INSCRIPTO -->
                        <div class="panel panel-info">
                          <div class="panel-heading"><?php echo __('Inscripto'); ?></div>
                            <table class="table table-striped">
                             <tr>
                               <td><?php echo __('Nombre'); ?></td>
                               <td><?php echo $Inscripcion->nombre; ?></td>
                             </tr>
                             <tr>
                               <td><?php echo __('Apellido'); ?></td>
                               <td><?php echo $Inscripcion->apellido; ?></td>
                             </tr>
                             <tr>
                               <td><?php echo __('Celular'); ?></td>
                               <td><?php echo $Inscripcion->celular; ?></td>
                             </tr>
                             <tr>
                               <td><?php echo __('Correo'); ?></td>
                               <td><?php echo $Inscripcion->email_correo; ?></td>
                             </tr>
                             <?php if ($Inscripcion->pais_id > 0) { ?>
                             <tr>
                               <td><?php echo __('Pais'); ?></td>
                               <td><?php echo $Inscripcion->pais->pais; ?></td>
                             </tr>
                             <?php } ?>
                             <?php if ($Inscripcion->ciudad <> '') { ?>
                             <tr>
                               <td><?php echo __('Ciudad'); ?></td>
                               <td><?php echo $Inscripcion->ciudad; ?></td>
                             </tr>
                             <?php } ?>
                          </table>
                        </div>
                        <!-- FIN DATOS DE INSCRIPTO -->
                        
                        <!-- DATOS DEL CURSO -->
                        <div class="panel panel-info">
                          <div class="panel-heading"><?php echo __($Inscripcion->solicitud->tipo_de_evento->tipo_de_evento); ?></div>
                          <table class="table table-striped">
                           <?php if ($Inscripcion->curso_id <> '') { ?>
                           <tr>
                             <td><?php echo __('Nombre'); ?></td>
                             <td><?php echo $Inscripcion->curso->nombre_del_curso; ?></td>
                           </tr>
                           <?php } ?>
                           <?php if ($Inscripcion->solicitud->localidad_id > 0) { ?>
                           <tr>
                             <td><?php echo __('Ciudad'); ?></td>
                             <td><?php echo $Inscripcion->solicitud->localidad->localidad; ?></td>
                           </tr>
                           <?php } ?>
                           <?php if ($Inscripcion->solicitud->pais_id > 0) { ?>
                           <tr>
                             <td><?php echo __('Ciudad'); ?></td>
                             <td><?php echo $Inscripcion->solicitud->pais->pais_id; ?></td>
                           </tr>
                           <?php } ?>
                           <?php if ($Inscripcion->pais_id > 0) { ?>
                           <tr>
                             <td><?php echo __('Pais'); ?></td>
                             <td><?php echo $Inscripcion->pais->pais; ?></td>
                           </tr>
                           <?php } ?>
                           <?php if ($Inscripcion->ciudad <> '') { ?>
                           <tr>
                             <td><?php echo __('Ciudad'); ?></td>
                             <td><?php echo $Inscripcion->ciudad; ?></td>
                           </tr>
                           <?php } ?>
                          </table>
                        </div>
                        <!-- FIN DATOS DE INSCRIPTO -->
                        
                        <!-- DATOS DEL INSTRUCTOR -->
                        <div class="panel panel-info">
                          <div class="panel-heading"><?php echo __('Instructor o Tutor'); ?></div>
                          <table class="table table-striped">
                           <?php $Instructor = $Inscripcion->datosDelInstructor(); ?>
                           <tr>
                             <td><?php echo __('Nombre'); ?></td>
                             <td><?php echo $Instructor['nombre_responsable_inscripcion']; ?></td>
                           </tr>
                           <tr>
                             <td><?php echo __('Celular'); ?></td>
                             <td><?php echo $Instructor['tel_responsable_inscripcion']; ?></td>
                           </tr>
                          </table>
                        </div>
                        <!-- FIN DATOS DEl INSTRUCTOR -->

                        <!-- DATOS DE ASISTENCIA -->
                        <div class="panel panel-info">
                          <!-- Default panel contents -->
                          <div class="panel-heading"><?php echo __('Asistencia Registrada'); ?></div>

                          <!-- List group -->
                          <ul class="list-group">
                            <?php 
                            foreach ($Lecciones as $Leccion) {
                              $fecha_registro = explode(' ', $Leccion->created_at);
                            ?>

                            <li class="list-group-item"><?php echo $Leccion->nombre_de_la_leccion ?> <i style="color: grey"> -  <?php echo $gCon->FormatoFecha($fecha_registro[0]); ?></i></li>

                            <?php } ?>
                          </ul>
                        </div>
                        <!-- FIN DATOS DE ASISTENCIA -->

                        <!-- DATOS DE EVALUACIONES -->
                        <div class="panel panel-info">
                          <!-- Default panel contents -->
                          <div class="panel-heading"><?php echo __('Trabajos de Repaso o Evaluaciones'); ?></div>

                          <!-- List group -->
                          <ul class="list-group">
                            <?php 
                            foreach ($Evaluaciones as $Evaluacion) {
                              $fecha_registro = explode(' ', $Evaluacion->created_at);
                            ?>

                            <li class="list-group-item">
                              <strong><?php echo $Evaluacion->titulo_de_la_evaluacion ?> </strong> <i style="color: grey"> -  <?php echo $gCon->FormatoFecha($fecha_registro[0]); ?></i><br>
                              <p><?php echo __('Puntuacion') ?>: <?php echo $Evaluacion->puntuacion ?>/100</p>
                              <p><?php echo $Evaluacion->texto ?> </p>
                            </li>

                            <?php } ?>
                          </ul>
                        </div>
                        <!-- FIN DATOS DE EVALUACIONES -->

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

                            

<!-- INICIO APP app-form -->
    <script type="text/javascript">
        const config = {
          locale: 'es', 
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
            fecha_de_evento_id: null,
            sino_notificar_proximos_eventos: true,
            sino_acepto_politica_de_privacidad: null,
            mensaje_error: ''
          },

          methods: {
              validar_errores: function(){
                // VALIDO SI HAY ERRORES
                this.$validator.validateAll().then(() => {
                    if (this.errors.any()) {
                      // SI HAY ERRORES
                      //this.guardar = false
                      //this.mostrar_mensaje_error = true
                      this.mensaje_error = 'Hay campos que corrergir'
                        //console.log(this.$validator.errors.items.length)
                        console.log(this.$validator.errors.get_items())
                    }
                    else {
                      // SI NO HAY ERRORES
                      //this.mostrar_mensaje_error = false
                      //this.guardar = true
                      //this.GenerarCotizacion()
                      this.mensaje_error = 'TUTO BENE'
                    }
                }).catch(() => {
                    this.title = this.errors;
                });
              },
            
              
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
