<html>
    <head>
  
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-46601315-3"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'UA-46601315-3');
        </script>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php 
        $localidad_text = '';
        if ($Solicitud->localidad <> '') { 
          $localidad_text = $Solicitud->localidad->localidad;
        }
        ?>
        <title><?php echo __('Lista de Inscriptos Históricos') ?> |  {{ __($Solicitud->tipo_de_evento->tipo_de_evento) }} {{ $localidad_text }}</title>

      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      <!-- Bootstrap 3.3.7 -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/font-awesome/css/font-awesome.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/Ionicons/css/ionicons.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>dist/css/AdminLTE.min.css">
      <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>dist/css/skins/_all-skins.min.css">
      <!-- Morris chart -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/morris.js/morris.css">
      <!-- jvectormap -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/jvectormap/jquery-jvectormap.css">
      <!-- Date Picker -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
      <!-- Daterange picker -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
      <!-- bootstrap wysihtml5 - text editor -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      
      <!-- DataTables -->
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


      <!-- Google Font -->
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

      <!-- jQuery 3 -->
      <script src="<?php echo $dominio_publico ?>bower_components/jquery/dist/jquery.min.js"></script>

      <link rel="stylesheet" href="<?php echo $dominio_publico ?>css/generic.css">
      <link rel="stylesheet" href="<?php echo $dominio_publico ?>css/style.css">

    <script src="<?php echo $dominio_publico ?>js/vue/vue.js"></script>
    <script src="<?php echo $dominio_publico ?>js/vee-validate/dist/vee-validate.js"></script>
    <script src="<?php echo $dominio_publico ?>js/vee-validate/dist/locale/es.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $dominio_publico ?>js/vue-form-generator/vfg.css">

    <style type="text/css">
      .btn-default.active, .btn-default:active, .open>.dropdown-toggle.btn-default {
          color: #fff;
          background-color: #919191;
          border-color: #919191;
      }
      .table-striped>tbody>tr:nth-of-type(odd) {
        background-color: #efefef;
      }
    </style>

    </head>
    <body style="overflow-x: auto;"> 

      <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
          <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
          <li data-target="#carousel-example-generic" data-slide-to="1"></li>
          <li data-target="#carousel-example-generic" data-slide-to="2"></li>
          <li data-target="#carousel-example-generic" data-slide-to="3"></li>
          <li data-target="#carousel-example-generic" data-slide-to="4"></li>
          <li data-target="#carousel-example-generic" data-slide-to="5"></li>
          <li data-target="#carousel-example-generic" data-slide-to="6"></li>
          <li data-target="#carousel-example-generic" data-slide-to="7"></li>
          <li data-target="#carousel-example-generic" data-slide-to="8"></li>
          <li data-target="#carousel-example-generic" data-slide-to="9"></li>
          <li data-target="#carousel-example-generic" data-slide-to="10"></li>
          <li data-target="#carousel-example-generic" data-slide-to="11"></li>
          <li data-target="#carousel-example-generic" data-slide-to="12"></li>
          <li data-target="#carousel-example-generic" data-slide-to="13"></li>
          <li data-target="#carousel-example-generic" data-slide-to="14"></li>
          <li data-target="#carousel-example-generic" data-slide-to="15"></li>
          <li data-target="#carousel-example-generic" data-slide-to="16"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
          <div class="item active">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id ?>/1">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/2">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/3">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/4">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/5">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/6">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/7">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/8">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/9">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/10">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/11">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/12">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/13">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/14">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/15">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/16">
          </div>
          <div class="item">
            <img id="imgFinal" src="<?php echo $dominio_publico ?>flyer/<?php echo $Solicitud->id  ?>/17">
          </div>
          
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
          <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
          <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>




    <!-- jQuery 3 -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo $dominio_publico ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Morris.js charts -->
    <script src="<?php echo $dominio_publico ?>bower_components/raphael/raphael.min.js"></script>
    <script src="<?php echo $dominio_publico ?>bower_components/morris.js/morris.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="<?php echo $dominio_publico ?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?php echo $dominio_publico ?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo $dominio_publico ?>bower_components/moment/min/moment.min.js"></script>
    <script src="<?php echo $dominio_publico ?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- datepicker -->
    <script src="<?php echo $dominio_publico ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="<?php echo $dominio_publico ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- Slimscroll -->
    <script src="<?php echo $dominio_publico ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo $dominio_publico ?>bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo $dominio_publico ?>dist/js/adminlte.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?php echo $dominio_publico ?>dist/js/pages/dashboard.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo $dominio_publico ?>dist/js/demo.js"></script>
    <!-- DataTables -->
    <script src="<?php echo $dominio_publico ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $dominio_publico ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

      
    </body>
</html>