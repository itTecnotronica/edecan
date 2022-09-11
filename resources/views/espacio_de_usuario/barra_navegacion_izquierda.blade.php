<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">
  <!-- Sidebar user panel -->
  <div class="user-panel">
    <div class="pull-left image imagen-perfil">
      
      @if( file_exists('storage/' . $alumno->id . '/perfil.png') )
        <a href="<?php echo ENV('PATH_PUBLIC') ?>alumnos/imagenPerfil">
          <img src="{{ asset('storage/' . $alumno->id . '/perfil.png') }}" class="img-circle" alt="User Image">
        </a>
      @else
        <a href="<?php echo ENV('PATH_PUBLIC') ?>alumnos/imagenPerfil">
          <img src="{{ asset('dist/img/default-user.png') }}" class="img-circle" alt="User Image">
        </a>
      @endif    
    </div>
    <div class="pull-left info">
      <p>{{ $alumno->nombre }} {{ $alumno->apellido }}</p>
      <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
    </div>
  </div>

  <!-- /.search form -->
  <!-- sidebar menu: : style can be found in sidebar.less -->
  <ul class="sidebar-menu" data-widget="tree">
    <li class="header">NAVEGACION</li>
    <li>
      <a href="<?php echo ENV('PATH_PUBLIC') ?>alumnos/informacionPersonal">
        <i class="fa fa-user"></i> 
        <span>Informacion Personal</span>
      </a>
    </li>
    <li>
      <a href="<?php echo ENV('PATH_PUBLIC') ?>alumnos/lecciones">
        <i class="fa fa-book"></i> 
        <span>Lecciones</span></a>
    </li>
    <li>
      <a href="<?php echo ENV('PATH_PUBLIC') ?>alumnos/miTestimonio">
        <i class="fa fa-comments"></i> 
        <span>Dejanos tu Testimonio</span></a>
    </li>
    <li>

      @php

        if(isset($alumno) && isset($alumno->pais) && isset($alumno->pais->idioma_por_pais)  ){

          $idioma = $alumno->pais->idioma_por_pais->idioma;

          $invitacion = '';

          // chequea el idioma y configura la invitacion  
          // Lo estoy haciendo aca en la vista porque no se bien donde colocar esta logica en Laravel
          if (strpos($idioma->idioma, 'Español') !== false) {
            $invitacion = "Te invito a que conozcas este curso de autoconocimiento" . $idioma->url_form_curso_online;
          }

          if (strpos($idioma->idioma, 'Italiano') !== false) {
            $invitacion = "Ti invito a conoscere questo corso di auto-conoscenza" . $idioma->url_form_curso_online;
          }

          if (strpos($idioma->idioma, 'Portugues') !== false) {
            $invitacion = "Convido você a conhecer este curso de autoconhecimento" . $idioma->url_form_curso_online;
          }

          if (strpos($idioma->idioma, 'Frances') !== false) {
            $invitacion = "Je vous invite à connaître ce cours de connaissance de soi" . $idioma->url_form_curso_online;
          }


        } else {

          //Por defecto en espanol
          // $invitacion = "Te invito a que conozcas este curso de autoconocimiento" . $idioma->url_form_curso_online;
            $invitacion = "Te invito a que conozcas este curso de autoconocimiento" ;

        }


      @endphp

      <a href="whatsapp://send?text={{ $invitacion }}" data-action="share/whatsapp/share">
        <i class="fa fa-whatsapp"></i>
        <span>Invita a amigo</span>
      </a>
    </li>
    <li>
      <a href="https://wa.me/5492284548278">
        <i class="fa fa-whatsapp"></i>
        <span>Contactar Tutor</span>
      </a>
    </li>
    <li>
      <a href="<?php echo ENV('PATH_PUBLIC') ?>alumnos/sugerencias">
        <i class="fa fa-commenting"></i>
        <span>Dejanos una sugerencia!</span>
      </a>
    </li>
  </ul>
</section>
