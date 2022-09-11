<?php

use \App\Http\Controllers\SolicitudController;
use App\Equipo;
use App\User;

if ($_SERVER['HTTP_HOST'] == 'localhost:1010' or $_SERVER['HTTP_HOST'] == 'ac.gnosis.is') {
  $path_public = env('PATH_PUBLIC');
}
else {
  $path_public = 'https://'.$_SERVER['HTTP_HOST'].'/';  
}


$SolicitudController = new SolicitudController;
$paisesDelEquipo = $SolicitudController->paisesDelEquipo();

$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;

$Roles = Auth::user()->roles();

$pais_id = Auth::user()->pais_id;
$pais = '';
if ($pais_id <> '') {
  $pais = Auth::user()->pais->pais;
}


$Equipo = Equipo::where('coordinador_user_id', Auth::user()->id)->count();

$es_coordinador_user_de_equipo = 'N';
if ($Equipo > 0) {
  $es_coordinador_user_de_equipo = 'S';
}


$user_id = Auth::user()->id;
$User = User::where('id', $user_id)->whereRaw('DATEDIFF(NOW(),updated_at) > 30')->get();

$permiso_todos = array(1,2,3,4);
$permiso_administrador = array(1);
$permiso_supervisor = array(1,2);
$permiso_solicitante = array(1,4);
$permiso_ejecutivo = array(1,3);
$permiso_oe = array(1,7);
$permiso_at = array(1,9);
$permiso_gestorapp = array(1,10);
$permiso_admsedes = array(1,11);
$permiso_reg_campanias = array(1,12);
$permiso_coord_cursos_online = array(1,13);
$permiso_gestor_de_paises = array(1,14);
$permiso_equipo_seguimiento = array(1,15);
$permiso_equipo_capacitacion = array(1,16);
$permiso_contable_nacional = array(1,18);
$permiso_contable_lumisial = array(1,18, 19);
$permiso_contable_lumisial = array(1,18, 19);
$permiso_rrhh = array(1,20);
$permiso_coord_equipo = array(1,21);





$cant_solicitudes = App::make('App\Http\Controllers\HomeController')->notificaciones();

if (Auth::user()->img_avatar <> '') {
  $img_avatar = Auth::user()->img_avatar;
}
else {
  $img_avatar = $path_public.'img/avatar-sin-imagen.png';
}


function permisoAutorizado($Roles, $permisos) {
  $autorizado = false;
  foreach ($Roles as $rol_id) {
    if (in_array($rol_id, $permisos)) {
      $autorizado = true;
    }
  }
  return $autorizado;
}

$titleHead = 'Tecnotronica'.__('Solicitudes de Campañas');
$tituloApp = 'Tecnotronica';
if ($_SERVER['HTTP_HOST'] == 'ac.igca.com.ar') {
    $titleHead = 'IGCA';
    $tituloApp = 'IGCA';
}

?>

<!DOCTYPE html>
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
  <title><?php echo $titleHead ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo $path_public?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $path_public?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo $path_public?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $path_public?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo $path_public?>dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo $path_public?>bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo $path_public?>bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo $path_public?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo $path_public?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo $path_public?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo $path_public?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- jQuery 3 -->
  <script src="<?php echo $path_public?>bower_components/jquery/dist/jquery.min.js"></script>

  <link rel="stylesheet" href="<?php echo $path_public?>css/generic.css">


<link rel="stylesheet" type="text/css" href="<?php echo $path_public?>js/bootstrap-select/css/bootstrap-select.min.css">
<script type="module" type="text/javascript" src="<?php echo $path_public?>js/bootstrap-select/js/bootstrap-select.min.js"></script>
  <!--script src="https://maps.googleapis.com/maps/api/js?key=<?php echo env('API_KEY_GOOGLE_MAPS')?>&libraries=places"></script-->

</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo $path_public?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b><i class="fa fa-home"></i></b></span>
      <!-- logo for regular state and mobile devices -->
      <i class="fa fa-star-o"></i> <?php echo $tituloApp ?></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="<?php echo $path_public?>">
              <i class="fa fa-bell-o"></i>
              <span class="label label-danger"><?php echo $cant_solicitudes ?></span>
            </a>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{{ $img_avatar }}}" class="user-image" alt="User Image">
              <span class="hidden-xs">{{{ Auth::user()->name }}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{{ $img_avatar }}}" class="img-circle" alt="User Image">

                <p>
                  {{{ Auth::user()->name }}} <br> {{{ Auth::user()->email }}}
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo $path_public?>micuenta" class="btn btn-default btn-flat">{{ __('Mi Cuenta') }}</a>
                </div>
                <div class="pull-right">
                  <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-default btn-flat">{{ __('Salir') }}</a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{{ $img_avatar }}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{{ Auth::user()->name }}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">{{ __('MENU') }}</li>

        <?php if (permisoAutorizado($Roles, $permiso_todos)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-file-text-o"></i>
            <span><?php echo __('Solicitudes') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if (!(permisoAutorizado($Roles, [3]) and $pais_id == 30)) { ?>
            <li><a href="<?php echo $path_public?>Solicitudes/crear"><i class="fa fa-file-text-o"></i> <span><?php echo __('Agregar Solicitud') ?></span></a></li>
            <?php } ?>
            <li><a href="<?php echo $path_public?>Solicitudes/list/p"><i class="fa fa-file-text-o"></i> <span><?php echo __('Pendientes') ?></span></a></li>
            <li><a href="<?php echo $path_public?>Solicitudes/list/r"><i class="fa fa-file-text-o"></i> <span><?php echo __('Revisar') ?></span></a></li>
            <li><a href="<?php echo $path_public?>Solicitudes/list/a"><i class="fa fa-file-text-o"></i> <span><?php echo __('Aprobadas') ?></span></a></li>
            <li><a href="<?php echo $path_public?>Solicitudes/list/v"><i class="fa fa-file-text-o"></i> <span><?php echo __('Aprobadas') ?> <?php echo __('Viejas') ?></span></a></li>
            <li><a href="<?php echo $path_public?>Solicitudes/list/d"><i class="fa fa-file-text-o"></i> <span><?php echo __('Desaprobadas') ?></span></a></li>
            <li><a href="<?php echo $path_public?>Solicitudes/list/c"><i class="fa fa-file-text-o"></i> <span><?php echo __('Canceladas') ?></span></a></li>
            <li><a href="<?php echo $path_public?>Solicitudes/list/f"><i class="fa fa-file-text-o"></i> <span><?php echo __('Finalizadas') ?></span></a></li>
            <li><a href="<?php echo $path_public?>Solicitudes/list/t"><i class="fa fa-file-text-o"></i> <span><?php echo __('Todas') ?></span></a></li>
            <?php if (permisoAutorizado($Roles, $permiso_administrador) or (permisoAutorizado($Roles, $permiso_supervisor) and in_array(6, $paisesDelEquipo['Paises']))) { ?>
            <li><a href="<?php echo $path_public?>Solicitudes/list/x"><i class="fa fa-file-text-o"></i> <span><?php echo __('Pagadas sin enviar') ?></span></a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <?php if (permisoAutorizado($Roles, $permiso_coord_cursos_online)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-file-text-o"></i>
            <span><?php echo __('Otros').' '.__('Formularios') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>Solicitudes/online/t"><i class="fa fa-file-text-o"></i> <span><?php echo __('Cursos Online') ?></span></a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>Solicitudes/recoleccion/t"><i class="fa fa-file-text-o"></i> <span><?php echo __('Recolección de Datos') ?></span></a></li>
          </ul>
        </li>
        <?php } ?>
        <?php if (permisoAutorizado($Roles, $permiso_todos)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-file-text-o"></i>
            <span><?php echo __('Enlaces a material') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>enlaces-a-material"><i class="fa fa-file-text-o"></i> <span><?php echo __('Enlaces a material') ?></span></a></li>
          </ul>
        </li>
        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_at) or permisoAutorizado($Roles, $permiso_oe) or permisoAutorizado($Roles, $permiso_equipo_seguimiento) or permisoAutorizado($Roles, $permiso_coord_cursos_online)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-file-text-o"></i>
            <span><?php echo __('Inscripciones') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>buscar-inscriptos"><i class="fa fa-circle-thin"></i> <span><?php echo __('Inscripciones') ?></span></a></li>
            <li><a href="<?php echo $path_public?>buscar-alumnos-avanzados"><i class="fa fa-circle-thin"></i> <span><?php echo __('Alumnos Avanzados') ?></span></a></li>
          </ul>
        </li>
        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_equipo_capacitacion)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-file-text-o"></i>
            <span><?php echo __('Capacitaciones') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>list/Capacitacion/0"><i class="fa fa-circle-thin"></i> <span><?php echo __('Capacitaciones') ?></span></a></li>
            <li><a href="<?php echo $path_public?>list/Capacitacion_de_personal/0"><i class="fa fa-circle-thin"></i> <span><?php echo __('Capacitaciones de personal') ?></span></a></li>
          </ul>
        </li>
        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_supervisor) or permisoAutorizado($Roles, $permiso_ejecutivo) or permisoAutorizado($Roles, $permiso_admsedes)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-map-o"></i>
            <span><?php echo __('Región') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>list/Localidad/0"><i class="fa fa-circle-thin"></i> <span><?php echo __('Localidades') ?></span></a></li>
            <li><a href="<?php echo $path_public?>list/Provincia/0"><i class="fa fa-circle-thin"></i> <span><?php echo __('Provincias') ?></span></a></li>
            <?php if (permisoAutorizado($Roles, $permiso_supervisor)) { ?>
            <li><a href="<?php echo $path_public?>list/Pais/0"><i class="fa fa-circle-thin"></i> <span>Paises</span></a></li>
            <?php } ?>
            <?php if (permisoAutorizado($Roles, $permiso_administrador) or permisoAutorizado($Roles, $permiso_gestor_de_paises)) { ?>
            <li><a href="<?php echo $path_public?>list/Idioma_por_pais/6"><i class="fa fa-circle-thin"></i> <span>Idiomas por País</span></a></li>
            <?php } ?>
            <?php if (permisoAutorizado($Roles, $permiso_administrador)) { ?>
            <li><a href="<?php echo $path_public?>list/Modelo_de_mensaje/9"><i class="fa fa-circle-thin"></i> <span>Modelos de Mensajes</span></a></li>
            <li><a href="<?php echo $path_public?>list/Texto_anuncios/0"><i class="fa fa-circle-thin"></i> <span>Textos de Anuncios</span></a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_administrador)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-map-o"></i>
            <span><?php echo __('Listas de Envios') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>list/Encabezado_de_envio/0"><i class="fa fa-circle-thin"></i> <span>Encabezados de envio</span></a></li>
            <li><a href="<?php echo $path_public?>list/Lista_de_envio/0"><i class="fa fa-circle-thin"></i> <span>Listas de envio</span></a></li>
            <li><a href="<?php echo $path_public?>list/Tipo_de_lista_de_envio/0"><i class="fa fa-circle-thin"></i> <span>Tipos de Listas de envio</span></a></li>
            <li><a href="<?php echo $path_public?>list/Instancia_de_envio/0"><i class="fa fa-circle-thin"></i> <span>Instancias de envio</span></a></li>
          </ul>
        </li>
        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_administrador)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-database"></i>
            <span>Referencias</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>list/Moneda/0"><i class="fa fa-dollar"></i> Monedas</a></li>
            <li><a href="<?php echo $path_public?>list/Idioma/0"><i class="fa fa-dollar"></i> Idiomas</a></li>
            <li><a href="<?php echo $path_public?>list/Institucion/0"><i class="fa fa-dollar"></i> Instituciones</a></li>
            <li><a href="<?php echo $path_public?>list/Formato_de_hora/0"><i class="fa fa-dollar"></i> Formatos de hora</a></li>
            <li><a href="<?php echo $path_public?>list/User/2"><i class="fa fa-key"></i> Usuarios</a></li>
            <li><a href="<?php echo $path_public?>list/Rol_de_usuario/0"><i class="fa fa-key"></i> Roles de Usuarios</a></li>
            <li><a href="<?php echo $path_public?>list/Rol_extra/0"><i class="fa fa-key"></i> Roles Extra</a></li>
            <li><a href="<?php echo $path_public?>list/Pais_por_equipo/0"><i class="fa fa-key"></i> Paises por Equipo</a></li>
            <li><a href="<?php echo $path_public?>list/Tipo_de_evento/0"><i class="fa fa-key"></i> Tipos de Eventos</a></li>
            <li><a href="<?php echo $path_public?>list/Tipo_de_curso_online/0"><i class="fa fa-key"></i> Tipos de Cursos Online</a></li>
            <li><a href="<?php echo $path_public?>list/Tipo_de_campania_facebook/0"><i class="fa fa-key"></i> Tipos de Campañas Facebook</a></li>
            <li><a href="<?php echo $path_public?>list/Curso/0"><i class="fa fa-key"></i> Cursos</a></li>
            <li><a href="<?php echo $path_public?>list/Modelo_de_evaluacion/0"><i class="fa fa-key"></i> Modelos de evaluacion</a></li>
            <li><a href="<?php echo $path_public?>list/Leccion/10"><i class="fa fa-key"></i> Lecciones</a></li>
            <li><a href="<?php echo $path_public?>list/Leccion_por_pais_e_idioma/0"><i class="fa fa-key"></i> Lecciones por Pais</a></li>
            <li><a href="<?php echo $path_public?>list/Leccion_extra/2"><i class="fa fa-key"></i> Lecciones Extra</a></li>
            <li><a href="<?php echo $path_public?>list/Modelo_de_mensaje_curso/0"><i class="fa fa-key"></i> Modelos de mensajes del curso</a></li>
            <li><a href="<?php echo $path_public?>list/Causa_de_baja/0"><i class="fa fa-key"></i> Causas de Baja</a></li>
            <li><a href="<?php echo $path_public?>list/Canal_de_recepcion_del_curso/0"><i class="fa fa-key"></i> Canales de Recepción del Curso</a></li>
            <li><a href="<?php echo $path_public?>list/Estado_de_seguimiento/0"><i class="fa fa-key"></i> Estados de seguimiento</a></li>
            <li><a href="<?php echo $path_public?>list/Modalidad_de_notificacion_de_asistencia/0"><i class="fa fa-key"></i> Modalidades de notificacion de asistencias</a></li>

          </ul>
        </li>
        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_rrhh)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-users"></i>
            <span>Usuarios y Equipos</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>list/User/2"><i class="fa fa-users"></i> <?php echo __('Usuarios') ?></a></li>
            <li><a href="<?php echo $path_public?>list/Equipo/0"><i class="fa fa-users"></i> Equipos</a></li>
            <li><a href="<?php echo $path_public?>list/Usuario_por_equipo/0"><i class="fa fa-users"></i> <?php echo __('Usuarios') ?> por Equipo</a></li>
            <li><a href="<?php echo $path_public?>lista-de-usuarios"><i class="fa fa-users"></i> <?php echo __('Usuarios') ?> Edecan</a></li>            
          </ul>
        </li>
        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_coord_equipo)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-users"></i>
            <span>Mi Equipo</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>list/Usuario_por_equipo/15"><i class="fa fa-users"></i> Usuarios por Equipo</a></li>
            <li><a href="<?php echo $path_public?>list/User/2"><i class="fa fa-key"></i> Usuarios</a></li>
            <li><a href="<?php echo $path_public?>list/Rol_extra/0"><i class="fa fa-key"></i> Roles Extra</a></li>            
          </ul>
        </li>
        <?php } ?>


        <?php if (permisoAutorizado($Roles, $permiso_gestorapp)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-database"></i>
            <span>Apps</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if (permisoAutorizado($Roles, $permiso_administrador)) { ?>
            <li><a href="<?php echo $path_public?>list/App/0"><i class="fa fa-dollar"></i> App</a></li>
            <li><a href="<?php echo $path_public?>list/App_categoria/0"><i class="fa fa-dollar"></i> App Categorias</a></li>
            <li><a href="<?php echo $path_public?>list/App_nivel_de_acceso/0"><i class="fa fa-key"></i> App Niveles de acceso</a></li>
            <li><a href="<?php echo $path_public?>list/App_registro/0"><i class="fa fa-key"></i> App Registro</a></li>
            <li><a href="<?php echo $path_public?>list/App_tipo_de_contenido/0"><i class="fa fa-key"></i> App tipos de contenido</a></li>
            <li><a href="<?php echo $path_public?>list/App_tipo_de_evento/0"><i class="fa fa-key"></i> App tipos de Eventos</a></li>
            <li><a href="<?php echo $path_public?>list/App_tipo_de_carnet/0"><i class="fa fa-key"></i> App Tipos de Carnet</a></li>
            <li><a href="<?php echo $path_public?>list/App_tipo_de_debito/0"><i class="fa fa-key"></i> App Tipos de Debito</a></li>
            <li><a href="<?php echo $path_public?>list/App_tipo_de_tarjeta/0"><i class="fa fa-key"></i> App Tipos de Tarjetas</a></li>
            <?php } ?>
            <li><a href="<?php echo $path_public?>list/App_miembro/13"><i class="fa fa-dollar"></i> App Miembros</a></li>
            <li><a href="<?php echo $path_public?>list/App_contenido/11"><i class="fa fa-dollar"></i> App Contenidos</a></li>
            <li><a href="<?php echo $path_public?>list/App_posteo/0"><i class="fa fa-key"></i> App Posteo</a></li>
            <li><a href="<?php echo $path_public?>list/Material_de_leccion/0"><i class="fa fa-key"></i> Materiales de Leccion</a></li>
            <li><a href="<?php echo $path_public?>list/App_carnet/0"><i class="fa fa-key"></i> App Carnets</a></li>
            <li><a href="<?php echo $path_public?>list/App_debito/0"><i class="fa fa-key"></i> App Debitos</a></li>
            <li><a href="<?php echo $path_public?>list/App_evento/0"><i class="fa fa-key"></i> App Eventos</a></li>
            <li><a href="<?php echo $path_public?>list/App_inscripcion_en_evento/0"><i class="fa fa-key"></i> App Inscripciones en Eventos</a></li>
            <li><a href="<?php echo $path_public?>list/App_tarjeta/0"><i class="fa fa-key"></i> App Tarjetas</a></li>

          </ul>
        </li>


        <li class="active treeview">
          <a href="#">
            <i class="fa fa-database"></i>
            <span>Materiales</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>list/Material/0"><i class="fa fa-dollar"></i> Materiales</a></li>
            <li><a href="<?php echo $path_public?>list/Autor/0"><i class="fa fa-dollar"></i> Autores</a></li>
            <li><a href="<?php echo $path_public?>list/Tipo_de_material/0"><i class="fa fa-dollar"></i> Tipos de Material</a></li>
          </ul>
        </li>

        <?php } ?>


        <?php if (permisoAutorizado($Roles, $permiso_oe) or permisoAutorizado($Roles, $permiso_at) or $es_coordinador_user_de_equipo == 'S') { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-print"></i>
            <span>Reportes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if (permisoAutorizado($Roles, $permiso_oe) or permisoAutorizado($Roles, $permiso_at)) { ?>
              <li><a href="<?php echo $path_public?>dashboard-oe"><i class="fa fa-dashboard"></i> <?php echo __('Tablero de Control') ?></a></li>
              <li><a href="<?php echo $path_public?>ranking-m"><i class="fa fa-line-chart"></i> <?php echo __('Estadísticas') ?></a></li>
            <?php } ?>
            <li><a href="<?php echo $path_public?>search-solicitudes-estadisticas"><i class="fa fa-list"></i> <?php echo __('Campañas') ?></a></li>
            <li><a href="<?php echo $path_public?>search-encuestas-de-satisfaccion"><i class="fa fa-check-square-o"></i> <?php echo __('Encuesta de Satisfacción') ?></a></li>
            <?php if (Auth::user()->id <> 790) { ?>
            <li><a href="<?php echo $path_public?>lista-de-usuarios"><i class="fa fa-users"></i> <?php echo __('Usuarios') ?> Edecan</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>


        <?php if (permisoAutorizado($Roles, $permiso_oe) or permisoAutorizado($Roles, $permiso_at) or $es_coordinador_user_de_equipo == 'S') { ?>
        <li class="treeview menu-open">
          <a href="#">
            <i class="fa fa-share"></i> <span><?php echo __('Ver Mapa') ?></span>
            <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
          </a>
          <ul class="treeview-menu" style="display: block;">
            <li><a href="<?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-inscriptos" target="_blank"><i class="fa fa-map-marker"></i> <?php echo __('Inscriptos a Cursos Online') ?></a></li>
            <?php if (Auth::user()->id <> 790) { ?>
            <li class="treeview">
              <a href="#"><i class="fa fa-circle-o"></i> <?php echo $pais ?>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-cursos-y-conferencias-por-region/0/5/<?php echo $pais_id ?>" target="_blank"><i class="fa fa-map-marker"></i> <?php echo __('Próximos Eventos') ?></a></li>
                <li><a href="<?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-cursos-y-conferencias-por-region/30/5/<?php echo $pais_id ?>" target="_blank"><i class="fa fa-map-marker"></i> <?php echo __('Eventos hasta un mes atras') ?></a></li>
              </ul>
            </li>
            <?php } ?>
            <li class="treeview">
              <a href="#"><i class="fa fa-circle-o"></i> <?php echo __('Todos') ?>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-cursos-y-conferencias/0" target="_blank"><i class="fa fa-map-marker"></i> <?php echo __('Próximos Eventos') ?></a></li>
                <li><a href="<?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-cursos-y-conferencias/30" target="_blank"><i class="fa fa-map-marker"></i> Eventos hasta un mes atras</a></li>
              </ul>
            </li>
          </ul>
        </li>

        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_oe) or permisoAutorizado($Roles, $permiso_at) or permisoAutorizado($Roles, $permiso_admsedes)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-flag-o"></i>
            <span>Sedes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if (Auth::user()->id <> 790) { ?>
            <li><a href="<?php echo $path_public?>list/Sede/7"><i class="fa fa-home"></i> Sedes</a></li>
            <li><a href="<?php echo $path_public?>list/Zona/0"><i class="fa fa-home"></i> Zonas</a></li>
            <?php } ?>
            <li><a href="<?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-sedes/<?php echo $pais_id ?>" target="_blank"><i class="fa fa-map-marker"></i> Mapa de Sedes <?php echo $pais ?></a></li>
            <li><a href="<?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-sedes/0" target="_blank"><i class="fa fa-map-marker"></i> Todas las Sedes </a></li>
            <!--li><a href="<?php echo env('PATH_PUBLIC_MAPS')?>mapa-de-sedes-argentina" target="_blank"><i class="fa fa-save"></i> Importar Sedes</a></li-->

          </ul>
        </li>
        <?php } ?>


        <?php if (permisoAutorizado($Roles, $permiso_administrador)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Parametrizaci&oacute;n</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $path_public?>list/Parametro/0"><i class="fa fa-check-square-o"></i> Parametros</a></li>
            <li><a href="<?php echo $path_public?>list/Opcion/14"><i class="fa fa-circle"></i> Opciones</a></li>
            <li><a href="<?php echo $path_public?>list/Codigo_de_envio/0"><i class="fa fa-circle"></i> Codigos de envio</a></li>
            <li><a href="<?php echo $path_public?>list/Medio_de_envio/0"><i class="fa fa-circle"></i> Medios de Envio</a></li>
          </ul>
        </li>
        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_administrador) or permisoAutorizado($Roles, $permiso_reg_campanias)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Metricas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if (permisoAutorizado($Roles, $permiso_reg_campanias)) { ?>
            <li><a href="<?php echo $path_public?>list/Campania/8"><i class="fa fa-circle-thin"></i> <span>Campañas</span></a></li>
            <?php } ?>
            <?php if (permisoAutorizado($Roles, $permiso_administrador)) { ?>
            <li><a href="<?php echo $path_public?>list/Sesion/0"><i class="fa fa-check-square-o"></i> Sesiones</a></li>
            <li><a href="<?php echo $path_public?>list/Formulario/0"><i class="fa fa-check-square-o"></i> Formularios</a></li>
            <li><a href="<?php echo $path_public?>list/Visualizacion_de_formulario/0"><i class="fa fa-check-square-o"></i> Visualizaciones de Formulario</a></li>
            <li><a href="<?php echo $path_public?>list/Evento_en_sitio/0"><i class="fa fa-circle"></i> Eventos en Sitio</a></li>
            <li><a href="<?php echo $path_public?>list/Tipo_de_evento_en_sitio/0"><i class="fa fa-circle"></i> Tipos de eventos en sitio</a></li>
            <li><a href="<?php echo $path_public?>list/Registro_de_error/0"><i class="fa fa-circle"></i> Registro de Errores</a></li>
            <?php } ?>

          </ul>
        </li>
        <?php } ?>

        <?php if (permisoAutorizado($Roles, $permiso_administrador) or permisoAutorizado($Roles, $permiso_contable_nacional) or permisoAutorizado($Roles, $permiso_contable_lumisial)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Contabilidad</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if (permisoAutorizado($Roles, $permiso_administrador) or permisoAutorizado($Roles, $permiso_contable_nacional)) { ?>
            
              <li><a href="<?php echo $path_public?>list/Persona_juridica/0"><i class="fa fa-check-square-o"></i> Personas Juridicas</a></li>
              <li><a href="<?php echo $path_public?>list/Cuenta_contable/0"><i class="fa fa-check-square-o"></i> Cuentas</a></li>
              <li><a href="<?php echo $path_public?>list/Caja_contable/0"><i class="fa fa-check-square-o"></i> Cajas</a></li>
              <li><a href="<?php echo $path_public?>list/Movimiento_contable/0"><i class="fa fa-check-square-o"></i> Todos los Movimientos</a></li>
            <?php } ?>

            <li><a href="<?php echo $path_public?>list/Movimiento_contable/12"><i class="fa fa-check-square-o"></i> Movimientos Lumisial</a></li>

          </ul>
        </li>
        <?php } ?>

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <?php
    if(isset($mensaje) or Session::get('mensaje') <> '' or $errors->any()) {

      if (Session::get('mensaje') <> '' and !isset($mensaje)) {
        $mensaje = Session::get('mensaje');
      }

      $mensaje_class = 'alert-success';
      $mensaje_icon = 'fa-check';
      $mensaje_detalle = '';

      if ($errors->any()) {
        if($errors->first()) {
          $mensaje_icon = 'fa fa-ban';
          $mensaje_class = 'alert-warning';
          foreach ($errors->all() as $error) {
            $mensaje_detalle .= "<p>$error</p>";
          }
        }
        else {
          $errors_array = $errors->all();
          $mensaje_detalle = $errors_array[1];
        }
      }
      else {
        if (isset($mensaje['class'])) {
          $mensaje_class = $mensaje['class'];
        }

        if (isset($mensaje['detalle'])) {
          $mensaje_detalle = $mensaje['detalle'];
        }
        else {
          $mensaje_detalle = $mensaje;
        }

        if (isset($mensaje['error']) and $mensaje['error']) {
          $mensaje_icon = 'fa fa-ban';
        }
      }

    ?>
      <section class="content-header">
        <div class="row">
          <div class="col-xs-12">
            <br>
            <div class="alert <?php echo $mensaje_class; ?> alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <h4><i class="icon fa <?php echo $mensaje_icon; ?>"></i> <?php echo $mensaje_detalle; ?></h4>
            </div>
          </div>
        </div>
      </section>
    <?php } ?>

    @yield('contenido')
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.0
    </div>
    <strong>Copyright &copy; 2014-2016 <a href="https://adminlte.io">Almsaeed Studio</a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Recent Activity</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-birthday-cake bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                <p>Will be 23 on April 24th</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-user bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                <p>New phone +1(800)555-1234</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                <p>nora@example.com</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-file-code-o bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                <p>Execution time 5 seconds</p>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="label label-danger pull-right">70%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Update Resume
                <span class="label label-success pull-right">95%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Laravel Integration
                <span class="label label-warning pull-right">50%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Back End Framework
                <span class="label label-primary pull-right">68%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Some information about this general settings option
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Allow mail redirect
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Other sets of options are available
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Expose author name in posts
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Allow the user to show his name in blog posts
            </p>
          </div>
          <!-- /.form-group -->

          <h3 class="control-sidebar-heading">Chat Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Show me as online
              <input type="checkbox" class="pull-right" checked>
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Turn off notifications
              <input type="checkbox" class="pull-right">
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Delete chat history
              <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
            </label>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->


<!-- jQuery 3 -->
<!--script src="<?php echo $path_public?>bower_components/jquery/dist/jquery.min.js"></script-->
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo $path_public?>bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo $path_public?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="<?php echo $path_public?>bower_components/raphael/raphael.min.js"></script>
<script src="<?php echo $path_public?>bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo $path_public?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?php echo $path_public?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo $path_public?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo $path_public?>bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo $path_public?>bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo $path_public?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo $path_public?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo $path_public?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo $path_public?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo $path_public?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo $path_public?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo $path_public?>dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo $path_public?>dist/js/demo.js"></script>
<!-- DataTables -->
<script src="<?php echo $path_public?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $path_public?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>


<?php if ($User->count() > 0) { ?>
<!-- MODAL ABM -->
  <div class="modal modal fade" id="modal-actualizar-datos-usuario">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="modal-titulo"><?php echo __('Revise y actualice sus datos') ?></div></h4>
        </div>
        <div class="modal-body" id="modal-bodi-actualizar-datos-usuario">

        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<!-- MODAL ABM -->

<!-- FUNCIONES MODIFICAR DATOS -->
  <?php 
  $gen_seteo = array(
      'gen_url_siguiente' => 'back', 
      'no_mostrar_campos_abm' => 'password|rol_de_usuario_id|remember_token'
    );
  ?>   

  <script type="text/javascript">
    $('#modal-actualizar-datos-usuario').modal('show');

    $.ajax({
      url: '<?php echo env('PATH_PUBLIC') ?>crearabm',
      type: 'POST',
      dataType: 'html',
      async: true,
      data:{
        _token: "{{ csrf_token() }}",
        gen_modelo: 'User',
        gen_seteo: '<?php echo serialize($gen_seteo) ?>',
        gen_opcion: '',
        gen_accion: 'm',
        gen_id: <?php echo $user_id ?>
      },
      success: function success(data, status) {        
        $("#modal-bodi-actualizar-datos-usuario").html(data);
        if (gen_accion == 'm') {
          $("#modal-titulo").html('Modificar Datos');
        }

      },
      error: function error(xhr, textStatus, errorThrown) {
          alert(errorThrown);
      }
    });

  </script>
<!-- FUNCIONES MODIFICAR DATOS -->     

<?php } ?>

</body>
</html>
