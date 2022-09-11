<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Tecnotronica | Solicitudes de Campañas</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- jQuery 3 -->
  <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>

  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/generic.css">

</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">



<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title>Au Register Forms by Colorlib</title>

    <!-- Icons font CSS-->
    <link href="<?php echo env('PATH_PUBLIC')?>templates/1/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="<?php echo env('PATH_PUBLIC')?>templates/1/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="<?php echo env('PATH_PUBLIC')?>templates/1/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="<?php echo env('PATH_PUBLIC')?>templates/1/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">

    <!-- Main CSS-->
    <link href="<?php echo env('PATH_PUBLIC')?>templates/1/css/main.css" rel="stylesheet" media="all">


    <!-- vue.js -->
    <script src="<?php echo env('PATH_PUBLIC')?>js/vue/vue.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/vee-validate.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/locale/es.js"></script>
    <script type="text/javascript" src="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.css">

    <script src="https://cdn.jsdelivr.net/vue.resource/1.3.1/vue-resource.min.js"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/css/bootstrap-select.min.css">
    <script type="text/javascript" src="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/js/bootstrap-select.min.js"></script>

    <!-- bootstrap slider -->
    <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>plugins/bootstrap-slider/slider.css">

    <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/style.css">


    <!-- moment.min.js -->
    <script src="<?php echo env('PATH_PUBLIC')?>js/Moment/moment.min.js"></script>
    <!-- datetimepicker.js -->
    <script src="<?php echo env('PATH_PUBLIC')?>js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css">


</head>

<body>
    <div class="page-wrapper bg-violet p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <center><h2 class="title">CURSO GRATUITO DE AUTOCONOCIMIENTO - {{ $Solicitud->Localidad->localidad }}</h2></center>

                          <!-- CAMPOS DE FORMULARIO -->      
                            <div class="panel-body">
                              {!! Form::open(array
                                (
                                'action' => 'SolicitudController@GuardarDatosCampania', 
                                'role' => 'form',
                                'method' => 'POST',
                                'id' => "form_gen_modelo",
                                'enctype' => 'multipart/form-data',
                                'class' => 'form-horizontal',
                                'ref' => 'form'
                                )) 
                              !!}
 
                                <div class="vue-form-generator" id="app-form">
                                    <fieldset>

                                        <div class="form-group required">
                                          <label for="nombre">Nombre</label>                          
                                          <input v-validate="'required|email'" type="text" class="form-control" id="nombre" name="nombre" v-model="nombre" placeholder="Nombre" required="required">       
                                          <span v-show="errors.has('nombre')" class="text-danger">@{{ errors.first('nombre') }}</span>
                                        </div>

                                        <div class="form-group">
                                              <button type="button" class="btn btn-default" v-on:click="validar_errores">Inscribirme</button>
                                        </div>

                                    </fieldset>
                                    <div class="col-lg-12">            
                                      <pre>@{{ $data }}</pre>
                                    </div>                                    
                                </div>


                                {!! Form::close() !!}
                            </div>
                          <!-- CAMPOS DE FORMULARIO -->

                            

                </div>
                <div class="card-heading"></div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="<?php echo env('PATH_PUBLIC')?>templates/1/vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="<?php echo env('PATH_PUBLIC')?>templates/1/vendor/select2/select2.min.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>templates/1/vendor/datepicker/moment.min.js"></script>
    <script src="<?php echo env('PATH_PUBLIC')?>templates/1/vendor/datepicker/daterangepicker.js"></script>

    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Main JS-->
    <script src="<?php echo env('PATH_PUBLIC')?>templates/1/js/global.js"></script>

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
            sino_notificar_proximos_eventos: null,
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
