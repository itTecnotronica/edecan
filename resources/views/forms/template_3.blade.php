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

if ($Solicitud->id == 15095) {
  //dd(__('messages.informacion_del_curso_1'));
}

//echo 'idioma: '.App::getLocale($idioma);
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


if (Input::old('pais_id') <> '') {
  $pais_id_sel = Input::old('pais_id');
}
else {
  $pais_id_sel = $pais_id;
}

?>

<!DOCTYPE html>
<html lang="<?php echo $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">

    <!-- Required meta tags-->
    <meta charset="utf-8">
    <meta name="description" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado(false) ?>">
    <meta name="author" content="<?php echo $nombre_institucion ?>.is">
    <meta name="keywords" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado(false) ?>">
    <meta property="og:title" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado(false) ?>" />
    <meta property="og:url" content="<?php echo $Solicitud->url_form_inscripcion() ?>" />
    <meta property="og:description" content="<?php echo $nombre_institucion ?>, <?php echo $Solicitud->descripcion_sin_estado(false) ?>">
    <meta property="og:image" content="<?php echo $imagen_chica ?>">

    <!-- Title Page-->
    <title><?php echo $Solicitud->descripcion_sin_estado(false) ?></title>


    <!-- Codigos tel paises -->
    <link rel="stylesheet" href="<?php echo $dominio_publico?>node_modules/intl-tel-input/build/css/intlTelInput.css">
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'gnosis-dark': '#0a0514',
                        'gnosis-purple': '#2e1065',
                        'gnosis-gold': '#cfb568',
                        'gnosis-gold-dark': '#b45309',
                        'gnosis-accent': '#7c3aed',
                        'light-bg': '#f8fafc',
                        'light-violet': '#ede9fe',
                    },
                    fontFamily: {
                        heading: ['Cinzel', 'serif'],
                        body: ['Lato', 'sans-serif'],
                    },
                    backgroundImage: {
                        'cosmos': "radial-gradient(circle at center, #1e1b4b 0%, #000000 100%)",
                        'light-sky': "radial-gradient(circle at center, #ffffff 0%, #f3f4f6 100%)",
                    }
                }
            }
        }
    </script>

    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .light-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(124, 58, 237, 0.1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }
        
        .gold-gradient-text {
            background: linear-gradient(to right, #fcd34d, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .violet-gradient-text {
            background: linear-gradient(to right, #4c1d95, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .floating-whatsapp {
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(37, 211, 102, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }
        
        html { scroll-behavior: smooth; }

        .hero-background {
            background-image: url('https://ac.gnosis.is/img/landing/recurso-01.jpg');
            background-size: cover;
            /* Posición ajustada para móvil: 68% */
            background-position: 68% center; 
            background-repeat: no-repeat;
        }

        @media (min-width: 768px) {
            .hero-background {
                background-position: center top; 
            }
        }

        .mobile-text-shadow {
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
        }
        
        html:not(.dark) .mobile-text-shadow {
            text-shadow: none;
        }

        .info-pill {
            background: rgba(46, 16, 101, 0.8);
            border: 1px solid #7c3aed;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
        }
        
        html:not(.dark) .info-pill {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #7c3aed;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.15);
            color: #2e1065;
        }
    </style>


    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WWP64FV');</script>
    <!-- End Google Tag Manager -->

</head>
<body class="font-body bg-light-bg text-gray-800 dark:bg-gnosis-dark dark:text-white overflow-x-hidden transition-colors duration-500">

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WWP64FV"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_script_body)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_script_body);
    }
    ?>

    <?php if (isset($mensaje_redireccion)) { ?>
      <?php if ($mensaje_redireccion <> '') { ?>
      <!-- LISTA DE ERRORES -->
          <div class="max-w-4xl mx-auto bg-[#F3E6E6] rounded-lg p-6 mt-28 mb-10 relative z-50 border border-transparent shadow-lg">
          <!-- Encabezado -->
          <div class="flex items-center gap-3 mb-4 text-red-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <h2 class="text-2xl font-bold"><?php echo __('Atención') ?></h2>
          </div>

          <!-- Texto -->
          <p class="text-gray-800 text-lg mb-6">
            <?php echo $mensaje_redireccion ?>
          </p>

        </div>                      
        <section>
            <div class="col-xs-12">
              <div class="alert bg-danger alert-dismissible">
                <h5 class="text-danger tit-lista-de-errores"><i class="glyphicon glyphicon-warning-sign "></i> </h5>
                <p></p>
              </div>
            </div>
        </section>
      <!-- LISTA DE ERRORES -->
      <?php } ?>
    <?php } ?>

    <!-- Navbar -->
    <nav class="absolute top-0 w-full z-[100] py-4 px-6 md:px-12 flex justify-between items-center pointer-events-auto">
        <img src="https://ac.gnosis.is/img/imagotipo.png" alt="Logotipo Gnosis" class="h-12 md:h-16 drop-shadow-lg transition-all duration-300 select-none relative z-[101]" loading="lazy">
        
        <div class="flex items-center gap-4 relative z-[101]">
            
            <!-- SWITCH DE TEMA MEJORADO (Toggle Visual) -->
            <button id="theme-toggle" type="button" class="relative w-16 h-8 rounded-full bg-indigo-100 dark:bg-black/50 border border-indigo-200 dark:border-white/20 shadow-inner flex items-center transition-colors duration-300 cursor-pointer touch-manipulation group" aria-label="Cambiar Tema">
                <!-- Icono Sol (Izquierda - Visible siempre pero apagado en dark) -->
                <i class="fas fa-sun text-yellow-500 text-xs absolute left-2 opacity-100 z-10 transition-opacity"></i>
                
                <!-- Icono Luna (Derecha - Visible siempre pero apagado en light) -->
                <i class="fas fa-moon text-indigo-400 text-xs absolute right-2 opacity-100 z-10 transition-opacity"></i>
                
                <!-- Círculo Deslizante (Handle) -->
                <div class="w-6 h-6 bg-white rounded-full shadow-md transform transition-transform duration-300 absolute left-1 dark:translate-x-8 z-20 flex items-center justify-center">
                    <!-- Icono activo dinámico dentro del círculo -->
                    <i class="fas fa-sun text-yellow-500 text-[10px] block dark:hidden"></i>
                    <i class="fas fa-moon text-indigo-600 text-[10px] hidden dark:block"></i>
                </div>
            </button>

            <a href="#registro" class="hidden md:inline-block bg-gradient-to-r from-gnosis-purple to-gnosis-accent dark:from-gnosis-gold dark:to-gnosis-gold-dark text-white dark:text-black font-bold py-2 px-6 rounded-full hover:scale-105 transition transform shadow-lg text-sm border border-transparent dark:border-none">
                RESERVAR CUPO
            </a>
        </div>
    </nav>

    <!-- SECCIÓN 1: HERO -->
    <header id="inicio" class="relative min-h-screen flex items-center justify-center pt-28 pb-12 px-4 md:px-8 hero-background transition-all duration-500">
        <div class="absolute inset-0 bg-gradient-to-r from-white/95 via-white/80 to-white/30 dark:from-gnosis-dark/90 dark:via-gnosis-dark/60 dark:to-transparent transition-all duration-500"></div>

        <div class="container mx-auto relative z-10 grid md:grid-cols-12 gap-8 items-center">
            
            <!-- COLUMNA IZQUIERDA (Padding izquierdo en escritorio) -->
            <div class="md:col-span-6 lg:col-span-5 lg:pl-16 text-left space-y-6">
                {!! Form::open(
                    [
                      'action' => 'FormController@RegistrarInscripcion',
                      'role' => 'form',
                      'method' => 'POST',
                      'id' => "form_inscripcion",
                      'enctype' => 'multipart/form-data',
                      'class' => 'form-horizontal',
                      'ref' => 'form',
                      '@submit.prevent' => "validateBeforeSubmit"
                    ]
                  )
                !!}


                <input type="hidden" name="solicitud_id" value="<?php echo $Solicitud->id ?>">
                <input type="hidden" name="campania_id" value="<?php echo $campania_id ?>">
                <input type="hidden" name="app_usuario_id" value="<?php echo $app_usuario_id ?>">
                <input type="hidden" name="apellido" value="">
                <input type="hidden" name="embebed" value="">
                <input type="hidden" name="canal_de_recepcion_del_curso_id" value="">
                <input type="hidden" name="consulta" value="">
                <input type="hidden" name="sino_notificar_proximos_eventos" value="SI">
                <input type="hidden" name="acepto_politica_de_privacidad" value="SI">

                <!-- TITULO CORREGIDO: Gradient Violeta en Light / Blanco en Dark -->
                <h2 class="text-3xl dark:text-gnosis-gold font-bold tracking-widest leading-relaxed"><?php echo str_replace("'", '', $ciudad) ?></h2>
                <h1 class="font-body text-3xl md:text-5xl font-black md:font-bold leading-tight drop-shadow-xl mobile-text-shadow dark:text-white text-gnosis-purple">
                    <span class="block">DESPERTAR INTERIOR:</span>
                    <span class="block mt-2 font-black tracking-wide text-2xl md:text-4xl bg-clip-text text-transparent bg-gradient-to-r from-[#4c1d95] to-[#7c3aed] dark:text-yellow-300 pb-2">
                        CURSO GRATUITO DE GNOSIS Y AUTOCONOCIMIENTO
                    </span>
                </h1>
                
                <h2 class="text-base md:text-lg text-gray-700 dark:text-gray-100 font-bold md:font-light leading-relaxed mobile-text-shadow">
                    Domina las claves para <span class="font-extrabold text-gnosis-purple dark:text-white">conocerte a ti mismo</span> y transforma tu vida en solo <span class="text-gnosis-accent dark:text-gnosis-gold font-extrabold">23 clases</span>, descubre tu propósito interior.
                </h2>


                <!-- INICIO SI TIENE FECHAS DE EVENTOS -->
                @if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 2 or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4) or ($Solicitud->tipo_de_evento_id == 4 and $Fechas_de_eventos->count() > 0) )

                  <!-- BLOQUE DE SELECCIÓN DE HORARIOS (COMPRIMIDO) -->
                  <!-- Cambio: p-5 -> p-3 para reducir altura -->
                  <div class="bg-white dark:bg-white/5 p-3 rounded-2xl border-4 border-[#8430ce] dark:border-gnosis-gold/30 shadow-2xl dark:shadow-lg transition-all transform hover:scale-[1.01] duration-300">
                    <div class="flex items-center gap-2 mb-2 border-b border-gray-200 dark:border-white/10 pb-1">
                        <i class="fas fa-certificate text-gnosis-accent dark:text-gnosis-gold text-lg"></i>
                        <span class="text-xs font-bold uppercase tracking-wide text-gnosis-purple dark:text-white">Con materiales de apoyo y certificado</span>
                    </div>

                    <p class="text-sm font-bold text-gray-800 dark:text-gnosis-gold mb-2 flex items-center">
                        <i class="far fa-calendar-check mr-2 text-lg text-gnosis-accent dark:text-gnosis-gold"></i> 
                        Elige tu horario y sede ideal:
                    </p>
                    
                    <!-- Cambio: space-y-3 -> space-y-1 para compactar opciones -->
                    <div class="space-y-1">


                      
                      @if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1) or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4) or ($Solicitud->tipo_de_evento_id == 4 and $Fechas_de_eventos->count() > 0))
                        @php                     
                        $type_opcion = 'radio';
                        $required = 'required="required"';
                        $required_vue = "'required'";
                        @endphp                     
                      @else 
                        @php                     
                        $type_opcion = 'checkbox';
                        $required = '';
                        $required_vue = '';
                        @endphp                     
                      @endif
                      
                      <!-- RECORRO LAS FECHAS DE EVENTOS -->
                        @foreach ($Fechas_de_eventos as $Fecha_de_evento)

                          @if ($Solicitud->tipo_de_evento_id == 1 or ($Solicitud->tipo_de_evento_id == 2 and $Solicitud->cant() == 1) or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4) or ($Solicitud->tipo_de_evento_id == 4 and $Fechas_de_eventos->count() > 0))
                              @php $nombre_campo = 'fecha_de_evento_id'; @endphp
                          @else
                              @php $nombre_campo = 'fecha_de_evento_id_'.$Fecha_de_evento->id; @endphp
                          @endif

                          <!-- Opción -->
                          <label class="flex items-start gap-3 p-2 rounded-xl border-2 border-[#8430ce] hover:border-gnosis-accent dark:border-white/10 dark:hover:border-gnosis-gold bg-slate-50 dark:bg-black/40 cursor-pointer transition-all group hover:shadow-md">
                              <input type="radio" id="fecha_de_evento_id" name="{{$nombre_campo}}" value="{{$Fecha_de_evento->id}}" class="mt-1 w-4 h-4 text-gnosis-accent focus:ring-gnosis-accent border-gray-300 dark:border-gray-500 dark:bg-gray-700 dark:checked:bg-gnosis-gold" checked>
                              <div class="flex flex-col">
                                  <?php
                                  $tipo = 'con_resumen';
                                    $con_inicio = true;
                                    $ver_mapa = false;
                                    $con_dir_inicio_distinto = false;
                                  ?>

                                  {!! nl2br($Fecha_de_evento->armarDetalleFechasDeEventos($tipo, $con_inicio, $idioma_por_pais, $Solicitud, $idioma, $ver_mapa, $con_dir_inicio_distinto)) !!}
                              </div>
                          </label>

                        @endforeach
                      <!-- FIN RECORRO LAS FECHAS DE EVENTOS -->


                        <!-- Opción NO PUEDO-->
                        <label class="flex items-center gap-3 p-1.5 rounded-lg border border-dashed border-[#8430ce] dark:border-gray-600 hover:border-gnosis-accent dark:hover:border-gray-400 bg-transparent cursor-pointer transition-all opacity-80 hover:opacity-100">
                            <input type="radio" name="fecha_de_evento_id" value="interesado_futuro" class="w-3 h-3 text-gray-500 focus:ring-gray-500 border-gray-300 dark:border-gray-500 dark:bg-gray-700">
                            <span class="text-[10px] font-semibold text-gray-600 dark:text-gray-300">No puedo en estos horarios pero estoy interesado/a</span>
                        </label>

                    </div>
                  </div>
                @endif
                <!-- FIN SI TIENE FECHAS DE EVENTOS -->

                <!-- FORMULARIO (COMPRIMIDO) -->
                <!-- Cambio: p-6 -> p-4, mt-6 -> mt-3 -->
                <div id="registro" class="bg-white dark:bg-white/5 p-4 rounded-2xl border-4 border-[#8430ce] dark:border-gnosis-gold/30 w-full mt-3 transition-all duration-300 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.15)] dark:shadow-black/60 relative overflow-hidden">
                    <!-- Brillo decorativo superior -->
                    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/50 to-transparent"></div>
                    
                    <!-- Cambio: space-y-4 -> space-y-2 -->
                        <div>
                            <label for="nombre" class="sr-only">Nombre Completo</label>
                            <!-- Cambio: py-3 -> py-2 -->
                            <input type="text" id="nombre" name="nombre" placeholder="* Nombre Completo:" required 
                                class="mb-2 w-full px-4 py-2 bg-slate-50 dark:bg-white text-gray-900 rounded-lg border-2 border-indigo-300 dark:border-gray-300 focus:border-gnosis-purple dark:focus:border-gnosis-gold focus:ring-2 focus:ring-gnosis-accent dark:focus:ring-gnosis-gold outline-none placeholder-gray-500 font-medium shadow-sm transition-all focus:shadow-md text-sm">
                        </div>
                        
                        <div>
                            <label for="email" class="sr-only">Correo Electrónico</label>
                            <!-- Cambio: py-3 -> py-2 -->
                            <input type="email" id="email_correo" name="email_correo" placeholder="* Correo Electrónico:" required 
                                class="mb-2 w-full px-4 py-2 bg-slate-50 dark:bg-white text-gray-900 rounded-lg border-2 border-indigo-300 dark:border-gray-300 focus:border-gnosis-purple dark:focus:border-gnosis-gold focus:ring-2 focus:ring-gnosis-accent dark:focus:ring-gnosis-gold outline-none placeholder-gray-500 font-medium shadow-sm transition-all focus:shadow-md text-sm">
                        </div>

                        <!-- TELÉFONO -->
                        <div>
                            <label for="telefono" class="sr-only">Teléfono Móvil</label>
                            <!-- Cambio: py-3 -> py-2 -->
                            <input type="tel" id="celular" name="celular" placeholder="* WhatsApp (Sin 0 ni 15)" required 
                                class="mb-2 w-full px-4 py-2 bg-slate-50 dark:bg-white text-gray-900 rounded-lg border-2 border-indigo-300 dark:border-gray-300 focus:border-gnosis-purple dark:focus:border-gnosis-gold focus:ring-2 focus:ring-gnosis-accent dark:focus:ring-gnosis-gold outline-none placeholder-gray-500 font-medium shadow-sm transition-all focus:shadow-md text-sm" onchange="actualizarCelularCompletoapp()">
                            <input type="hidden" name="celular_completo" id="celular_completo" v-model="celular_completo">
                        </div>

                        @if(1==0)
                        <!-- PAÍS Y CIUDAD -->
                        <div>
                            <label for="pais_origen" class="sr-only">País</label>
                            <div class="mb-2 relative mb-2">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 dark:text-gray-400 pointer-events-none z-10">
                                    <i class="fas fa-map-marker-alt text-xs"></i>
                                </span>
                                <!-- Cambio: py-3 -> py-2 -->
                                <select id="pais_id" name="pais_id" required class="w-full pl-10 px-4 py-2 bg-slate-50 dark:bg-white text-gray-900 rounded-lg border-2 border-indigo-300 dark:border-gray-300 focus:border-gnosis-purple dark:focus:border-gnosis-gold focus:ring-2 focus:ring-gnosis-accent dark:focus:ring-gnosis-gold outline-none font-medium appearance-none cursor-pointer text-gray-500 focus:text-gray-900 transition-all shadow-sm text-sm">
                                    <option value="" disabled>* Selecciona tu País de Residencia</option>
                                    <option value="Argentina" selected>Argentina</option>
                                    <option value="Bolivia">Bolivia</option>
                                    <option value="Chile">Chile</option>
                                    <option value="Colombia">Colombia</option>
                                    <option value="Costa Rica">Costa Rica</option>
                                    <option value="Ecuador">Ecuador</option>
                                    <option value="El Salvador">El Salvador</option>
                                    <option value="España">España</option>
                                    <option value="Estados Unidos">Estados Unidos</option>
                                    <option value="Guatemala">Guatemala</option>
                                    <option value="Honduras">Honduras</option>
                                    <option value="México">México</option>
                                    <option value="Nicaragua">Nicaragua</option>
                                    <option value="Panamá">Panamá</option>
                                    <option value="Paraguay">Paraguay</option>
                                    <option value="Perú">Perú</option>
                                    <option value="República Dominicana">República Dominicana</option>
                                    <option value="Uruguay">Uruguay</option>
                                    <option value="Venezuela">Venezuela</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label for="ciudad" class="sr-only">Ciudad</label>
                            <div class="mb-2 relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 dark:text-gray-400 pointer-events-none z-10">
                                    <i class="fas fa-city text-xs"></i>
                                </span>
                                <!-- Cambio: py-3 -> py-2 -->
                                <input type="text" id="ciudad" name="ciudad" placeholder="* Ciudad de Residencia" required 
                                    class="mb-2 w-full pl-10 px-4 py-2 bg-slate-50 dark:bg-white text-gray-900 rounded-lg border-2 border-indigo-300 dark:border-gray-300 focus:border-gnosis-purple dark:focus:border-gnosis-gold focus:ring-2 focus:ring-gnosis-accent dark:focus:ring-gnosis-gold outline-none placeholder-gray-500 font-medium shadow-sm transition-all focus:shadow-md text-sm">
                            </div>
                        </div>
                        @endif

                        <!-- Cambio: py-4 -> py-3 -->
                        <button type="submit" class="my-2 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-blue-500/40 transition duration-300 transform hover:-translate-y-1 text-center uppercase tracking-wide text-sm md:text-base ring-2 ring-white/20">
                            Quiero reservar mi lugar GRATIS
                        </button>
                        
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 text-center">
                            <i class="fas fa-lock mr-1"></i> Tus datos están 100% seguros y protegidos.
                        </p>
                    </form>
                </div>

              {!! Form::close() !!}
            </div>
            <!-- COLUMNA DERECHA -->
            <div class="hidden md:flex md:col-span-6 lg:col-span-7 justify-center relative h-full items-center">
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-gnosis-accent/30 rounded-full blur-3xl animate-pulse"></div>
            </div>
        </div>
    </header>

    <!-- SECCIÓN 2 y 3: MÓDULOS (Textos Restaurados y Extendidos) -->
    <section class="py-20 px-4 md:px-8 bg-gradient-to-b from-white to-indigo-50 dark:bg-cosmos dark:from-transparent dark:to-transparent relative overflow-hidden transition-colors duration-500">
        <div class="absolute inset-0 opacity-10 dark:opacity-30" style="background-image: radial-gradient(currentColor 1px, transparent 1px); background-size: 50px 50px; color: var(--tw-prose-body);"></div>

        <div class="container mx-auto relative z-10">
            <h2 class="font-heading text-3xl md:text-4xl text-center mb-16 text-gnosis-purple dark:text-white">
                ¿QUÉ VAS A <span class="text-gnosis-accent dark:text-gnosis-gold">DESCUBRIR</span> EN ESTE VIAJE?
            </h2>

            <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                <!-- TARJETA 1 -->
                <article class="light-card dark:glass-effect dark:bg-transparent rounded-2xl overflow-hidden hover:border-gnosis-accent/50 dark:hover:border-gnosis-gold/50 transition duration-500 group flex flex-col h-full">
                    <div class="h-56 overflow-hidden relative shrink-0">
                        <img src="https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Luz interior" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" loading="lazy">
                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-gnosis-purple/90 to-transparent dark:from-black dark:to-transparent p-4">
                            <span class="inline-block bg-white/90 text-gnosis-purple dark:bg-gnosis-purple/90 dark:border dark:border-gnosis-gold dark:text-white text-xs font-bold px-3 py-1 rounded mb-1">
                                MÓDULO 1
                            </span>
                            <h3 class="font-heading text-lg font-bold text-white tracking-wide">PSICOLOGÍA REVOLUCIONARIA</h3>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h4 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-lg mb-4 leading-tight">
                            Despierta tu Conciencia: Distingue entre tu Personalidad, Esencia y Ego
                        </h4>
                        <!-- Texto Restaurado -->
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-4 grow">
                            Aprenderás a distinguir entre el <strong>Ego, la Personalidad y la Esencia</strong>. Descubrirás por qué reaccionamos como lo hacemos ante la vida y recibirás las herramientas prácticas para comenzar a disolver los bloqueos psicológicos que impiden tu felicidad. 
                            <br><br>
                            No se trata solo de teoría; es un mapa para navegar tu propio mundo interior. Descubrirás por qué la <strong>Auto-Observación</strong> es la herramienta más poderosa para dejar de ser una víctima de las circunstancias.
                        </p>
                        <div class="mt-auto border-t border-gray-200 dark:border-white/10 pt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                                <span class="text-gnosis-accent dark:text-gnosis-gold font-bold not-italic"><i class="fas fa-star mr-1"></i> El resultado:</span> 
                                Al finalizar, tendrás la claridad mental necesaria para iniciar el Despertar de tu Conciencia y dejar de repetir los mismos errores.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- TARJETA 2 -->
                <article class="light-card dark:glass-effect dark:bg-transparent rounded-2xl overflow-hidden hover:border-gnosis-accent/50 dark:hover:border-gnosis-gold/50 transition duration-500 group flex flex-col h-full">
                    <div class="h-56 overflow-hidden relative shrink-0">
                        <img src="https://images.unsplash.com/photo-1593811167562-9cef47bfc4d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Meditación" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" loading="lazy">
                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-gnosis-purple/90 to-transparent dark:from-black dark:to-transparent p-4">
                            <span class="inline-block bg-white/90 text-gnosis-purple dark:bg-gnosis-purple/90 dark:border dark:border-gnosis-gold dark:text-white text-xs font-bold px-3 py-1 rounded mb-1">
                                MÓDULO 2
                            </span>
                            <h3 class="font-heading text-lg font-bold text-white tracking-wide">PRÁCTICA Y COMPROBACIÓN</h3>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h4 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-lg mb-4 leading-tight">
                            Deja atrás las creencias: Experimenta la Verdad a través de la Meditación
                        </h4>
                        <!-- Texto Restaurado -->
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-4 grow">
                            La Gnosis no es cuestión de fe, es cuestión de experiencia directa. Aquí dejaremos a un lado los dogmas para entrar en el laboratorio de tu propia mente. Dominarás técnicas milenarias de <strong>Relajación y Concentración</strong> para silenciar el ruido mental.
                            <br><br>
                            Aprenderás el uso correcto de <strong>Mantrams y Runas</strong>, herramientas vibratorias que activan facultades latentes, y realizarás ejercicios de retrospección para sanar tu pasado.
                        </p>
                        <div class="mt-auto border-t border-gray-200 dark:border-white/10 pt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                                <span class="text-gnosis-accent dark:text-gnosis-gold font-bold not-italic"><i class="fas fa-star mr-1"></i> El resultado:</span> 
                                Dejarás de "creer" para empezar a "saber". Experimentarás la realidad de tu ser interior y accederás a estados de paz y lucidez.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- TARJETA 3 -->
                <article class="light-card dark:glass-effect dark:bg-transparent rounded-2xl overflow-hidden hover:border-gnosis-accent/50 dark:hover:border-gnosis-gold/50 transition duration-500 group flex flex-col h-full">
                    <div class="h-56 overflow-hidden relative shrink-0">
                        <img src="https://images.unsplash.com/photo-1534447677768-be436bb09401?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Cosmos" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" loading="lazy">
                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-gnosis-purple/90 to-transparent dark:from-black dark:to-transparent p-4">
                             <span class="inline-block bg-white/90 text-gnosis-purple dark:bg-gnosis-purple/90 dark:border dark:border-gnosis-gold dark:text-white text-xs font-bold px-3 py-1 rounded mb-1">
                                MÓDULO 3
                            </span>
                            <h3 class="font-heading text-lg font-bold text-white tracking-wide">LEYES CÓSMICAS</h3>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h4 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-lg mb-4 leading-tight">
                            Rompe el Ciclo: Karma, Reencarnación y los Misterios
                        </h4>
                        <!-- Texto Restaurado -->
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-4 grow">
                            Nada es casualidad, todo es causalidad. En este módulo desvelaremos la mecánica oculta del universo. Comprenderás las leyes de <strong>Evolución e Involución</strong> y cómo la ley de <strong>Recurrencia</strong> te atrapa repitiendo los mismos dramas vida tras vida.
                            <br><br>
                            Abordaremos sin tabúes el misterio de <strong>la Muerte</strong> y sus procesos, el Retorno, y aprenderás a diferenciar entre <strong>Karma</strong> (consecuencia) y <strong>Dharma</strong> (recompensa), dándote la llave para modificar tu propio Destino.
                        </p>
                        <div class="mt-auto border-t border-gray-200 dark:border-white/10 pt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                                <span class="text-gnosis-accent dark:text-gnosis-gold font-bold not-italic"><i class="fas fa-star mr-1"></i> El resultado:</span> 
                                Perderás el miedo a lo desconocido y tomarás el timón de tu existencia, dejando de ser una víctima de las circunstancias.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- TARJETA 4 -->
                <article class="light-card dark:glass-effect dark:bg-transparent rounded-2xl overflow-hidden hover:border-gnosis-accent/50 dark:hover:border-gnosis-gold/50 transition duration-500 group flex flex-col h-full">
                    <div class="h-56 overflow-hidden relative shrink-0">
                        <img src="https://images.unsplash.com/photo-1600618528240-fb9fc964b853?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Alquimia" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" loading="lazy">
                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-gnosis-purple/90 to-transparent dark:from-black dark:to-transparent p-4">
                             <span class="inline-block bg-white/90 text-gnosis-purple dark:bg-gnosis-purple/90 dark:border dark:border-gnosis-gold dark:text-white text-xs font-bold px-3 py-1 rounded mb-1">
                                MÓDULO 4
                            </span>
                            <h3 class="font-heading text-lg font-bold text-white tracking-wide">LA ESTRUCTURA ESPIRITUAL</h3>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h4 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-lg mb-4 leading-tight">
                            Ingeniería del Alma: Alquimia y Cuerpos Solares
                        </h4>
                        <!-- Texto Restaurado -->
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-4 grow">
                            El ser humano actual es una "semilla" con posibilidades latentes. Descubre el mapa secreto de los <strong>4 Caminos</strong> y la ciencia sagrada de la <strong>Alquimia</strong> para transmutar tu energía creadora.
                            <br><br>
                            Te enseñaremos cómo construir tus <strong>Cuerpos Internos</strong> (Astral, Mental, Causal) para tener presencia consciente en dimensiones superiores. Conocerás los pasos precisos de los <strong>Procesos Iniciáticos</strong> y cómo lograr el verdadero <strong>Dominio de la Naturaleza</strong> interior.
                        </p>
                        <div class="mt-auto border-t border-gray-200 dark:border-white/10 pt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                                <span class="text-gnosis-accent dark:text-gnosis-gold font-bold not-italic"><i class="fas fa-star mr-1"></i> El resultado:</span> 
                                Pasarás de ser una criatura mecánica a un creador consciente, iniciando el camino hacia la Auto-Realización íntima del Ser.
                            </p>
                        </div>
                    </div>
                </article>
            </div>
            
            <div class="text-center mt-12">
                <!-- BOTÓN ACTUALIZADO: Azul sólido con texto blanco en modo claro -->
                <a href="#registro" class="inline-block bg-blue-600 hover:bg-blue-500 text-white border-2 border-transparent dark:bg-transparent dark:border-gnosis-gold dark:text-gnosis-gold dark:hover:bg-gnosis-gold dark:hover:text-black font-bold py-3 px-8 rounded-full transition duration-300 shadow-md">
                    RESERVAR MI LUGAR AHORA
                </a>
            </div>
        </div>
    </section>

    <!-- SECCIÓN 4: INSTITUCIONAL -->
    <section class="py-20 bg-gradient-to-br from-indigo-100 to-white dark:from-gnosis-dark dark:to-indigo-950 text-gray-800 dark:text-white transition-colors duration-500">
        <div class="container mx-auto px-4 md:px-8">
            <div class="text-center mb-12">
                <h2 class="text-gnosis-accent dark:text-gnosis-gold font-bold tracking-widest text-sm uppercase mb-2">Respaldo Internacional</h2>
                <h3 class="font-heading text-3xl md:text-5xl font-bold text-gnosis-purple dark:text-white drop-shadow-lg">
                    GNOSIS ARGENTINA
                </h3>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-12 bg-white/50 dark:bg-black/20 p-8 rounded-3xl border border-white dark:border-white/5 shadow-xl dark:shadow-none backdrop-blur-sm">
                <div class="w-full lg:w-1/2 flex justify-center">
                    <div class="relative w-full max-w-md aspect-square rounded-xl overflow-hidden shadow-2xl border-4 border-gnosis-accent/20 dark:border-gnosis-gold/30">
                        <img src="https://ac.gnosis.is/img/landing/templo-del-saber.jpg" alt="Templo del Saber" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="w-full lg:w-1/2 space-y-6">
                    <ul class="space-y-6">
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-white dark:bg-gradient-to-br dark:from-gnosis-purple dark:to-black flex items-center justify-center border-2 border-gnosis-accent dark:border-gnosis-gold shadow-md group-hover:scale-110 transition duration-300">
                                <i class="fas fa-landmark text-gnosis-accent dark:text-gnosis-gold text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-lg font-heading transition">Trayectoria Sólida</h4>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Asociación civil con más de <strong class="text-gnosis-purple dark:text-white">70 años</strong> de labor ininterrumpida.</p>
                            </div>
                        </li>
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-white dark:bg-gradient-to-br dark:from-gnosis-purple dark:to-black flex items-center justify-center border-2 border-gnosis-accent dark:border-gnosis-gold shadow-md group-hover:scale-110 transition duration-300">
                                <i class="fas fa-globe text-gnosis-accent dark:text-gnosis-gold text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-lg font-heading transition">Red Internacional</h4>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Parte de una Federación Global con más de <strong class="text-gnosis-purple dark:text-white">1.000 sedes</strong> en 30 países.</p>
                            </div>
                        </li>
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-white dark:bg-gradient-to-br dark:from-gnosis-purple dark:to-black flex items-center justify-center border-2 border-gnosis-accent dark:border-gnosis-gold shadow-md group-hover:scale-110 transition duration-300">
                                <i class="fas fa-users text-gnosis-accent dark:text-gnosis-gold text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-lg font-heading transition">Impacto Masivo</h4>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Más de <strong class="text-gnosis-purple dark:text-white">1.200.000 inscriptos</strong> históricos.</p>
                            </div>
                        </li>
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-white dark:bg-gradient-to-br dark:from-gnosis-purple dark:to-black flex items-center justify-center border-2 border-gnosis-accent dark:border-gnosis-gold shadow-md group-hover:scale-110 transition duration-300">
                                <i class="fas fa-graduation-cap text-gnosis-accent dark:text-gnosis-gold text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-lg font-heading transition">Experiencia Educativa</h4>
                                <p class="text-gray-600 dark:text-gray-300 text-sm"><strong class="text-gnosis-purple dark:text-white">17.000 cursos</strong> impartidos.</p>
                            </div>
                        </li>
                        <li class="flex items-start group">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-white dark:bg-gradient-to-br dark:from-gnosis-purple dark:to-black flex items-center justify-center border-2 border-gnosis-accent dark:border-gnosis-gold shadow-md group-hover:scale-110 transition duration-300">
                                <i class="fas fa-hand-holding-heart text-gnosis-accent dark:text-gnosis-gold text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-lg font-heading transition">Sin Fines de Lucro</h4>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Vocación de servicio y difusión cultural genuina.</p>
                            </div>
                        </li>
                    </ul>

                    <div class="pt-6">
                        <a href="#registro" class="block w-full text-center bg-gnosis-purple hover:bg-indigo-900 dark:bg-gnosis-gold dark:hover:bg-yellow-400 text-white dark:text-black font-bold text-xl py-4 rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                            ¡QUIERO INSCRIBIRME AHORA!
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-gnosis-purple dark:bg-black py-8 text-center text-indigo-200 dark:text-gray-500 text-xs border-t border-indigo-900 dark:border-gray-800 transition-colors duration-500">
        <div class="container mx-auto px-4">
            <img src="https://ac.gnosis.is/img/imagotipo.png" alt="Gnosis Logo Small" class="h-8 mx-auto mb-4 opacity-50 dark:opacity-50 grayscale hover:grayscale-0 transition filter brightness-200 dark:brightness-100" loading="lazy">
            <p>Daxus Latam © 2025 – Todos los derechos reservados</p>
        </div>
    </footer>

    <!-- BOTÓN FLOTANTE WHATSAPP -->
    <!--a href="https://wa.me/5493804201747?text=Hola,%20quisiera%20más%20información%20sobre%20el%20curso%20de%20Gnosis" 
       target="_blank" 
       rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center shadow-2xl transition transform hover:scale-110 floating-whatsapp group"
       aria-label="Contactar por WhatsApp">
        <i class="fab fa-whatsapp text-3xl md:text-4xl"></i>
    </a-->

    <!-- SCRIPT DE CAMBIO DE TEMA REPARADO -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const htmlElement = document.documentElement;

            // Función para alternar el tema
            const toggleTheme = () => {
                htmlElement.classList.toggle('dark');
                if (htmlElement.classList.contains('dark')) {
                    localStorage.theme = 'dark';
                } else {
                    localStorage.theme = 'light';
                }
            };

            // Verificar preferencia inicial (Light por defecto salvo guardado Dark)
            if (localStorage.theme === 'dark') {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }

            // Evento Click (Ratón)
            themeToggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                toggleTheme();
            });

            // Evento Touch (Móvil) para respuesta inmediata
            themeToggleBtn.addEventListener('touchstart', (e) => {
                e.preventDefault(); // Prevenir doble disparo si el navegador emula click
                toggleTheme();
            }, { passive: false });
        });
    </script>


    <!-- SCRIPT CELULAR -->
      <script src="<?php echo $dominio_publico?>node_modules/intl-tel-input/build/js/intlTelInput.js"></script>
      <script>

        var input = document.querySelector("#celular");

        var iti = window.intlTelInput(input, {
            utilsScript: "<?php echo $dominio_publico?>node_modules/intl-tel-input/build/js/utils.js?1585994360633", // just for formatting/
            //placeholderNumberType: "FIXED_LINE",
            separateDialCode: true,
            preferredCountries: []
          });
        
        function actualizarCelularCompletoapp() {
            var celular_completo = document.querySelector("#celular_completo");
            celular_completo.value = iti.getNumber();
        }

        
        input.addEventListener("countrychange", function() {
          if (iti.getNumber() != '') {
            actualizarCelularCompletoapp();
          }
        });
        
        iti.setCountry("<?php echo $cod_pais ?>");

        
      </script>
    <!-- SCRIPT CELULAR -->


</body>
</html>
<!-- end document-->
