<?php
use \App\Http\Controllers\SolicitudController;

$SolicitudController = new SolicitudController;

$idioma_por_pais = $Solicitud->idioma_por_pais();

$idioma = $Solicitud->idioma->mnemo;
App::setLocale($idioma);
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">

    <!-- Title Page-->
    <title><?php echo $Solicitud->descripcion_sin_estado(false) ?></title>

    
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

    <!-- Facebook Pixel Code -->
    <!--script>
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
    /></noscript-->
    <!-- End Facebook Pixel Code -->

    <!--script>
      <?php if (isset($registracion_encuesta) and $registracion_encuesta == 'SI') {?>
        fbq('trackCustom', 'PollComplete');
      <?php }
      else {?>
        fbq('track', 'CompleteRegistration', {
          value: 1,
          currency: 'USD'
          });
      <?php } ?>
    </script-->

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_pixel_de_facebook);
    }

    if (isset($Solicitud->localidad->urlencode_pixel_de_facebook)) {
      echo urldecode($Solicitud->localidad->urlencode_pixel_de_facebook);
    }
    ?>

    <!--script>
      <?php if (isset($registracion_encuesta) and $registracion_encuesta == 'SI') {?>
        fbq('trackCustom', 'PollComplete');
      <?php }
      else {?>
        fbq('track', 'CompleteRegistration', {
          value: 1,
          currency: 'USD'
          });
      <?php } ?>
    </script-->


</head>


<body>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WWP64FV"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <?php
    if (isset($Solicitud->idioma_por_pais()->urlencode_script_body)) {
      echo urldecode($Solicitud->idioma_por_pais()->urlencode_script_body);
    }
    ?>


    <!-- SECCIÓN: PASO FINAL -->
    <section class="bg-slate-50 dark:bg-gnosis-dark text-gray-800 dark:text-white relative border-t border-gray-200 dark:border-gray-800 transition-colors duration-500">
        <div class="bg-gnosis-purple text-white dark:bg-gnosis-gold dark:text-black font-bold text-center py-2 uppercase tracking-widest text-sm">
            ¡ATENCIÓN: PASO FINAL!
        </div>
        
        <div class="container mx-auto px-4 py-16 text-center max-w-3xl">
            <img src="https://ac.gnosis.is/img/imagotipo.png" alt="Gnosis Logo" class="h-16 mx-auto mb-6 drop-shadow-lg transition-all duration-300 filter dark:brightness-100 brightness-75">
            
            <h2 class="text-2xl md:text-3xl font-heading font-bold text-gnosis-purple dark:text-gnosis-gold mb-8">
                ¡Tu viaje de autoconocimiento está a un paso de comenzar!
            </h2>
            
            <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-8 mb-4 border border-gray-300 dark:border-gray-600 relative overflow-hidden">
                <div class="bg-gradient-to-r from-gnosis-accent to-gnosis-purple dark:from-gnosis-purple dark:to-gnosis-gold h-full rounded-full flex items-center justify-end px-3 font-bold text-white text-sm animate-pulse w-[90%]">
                    90%
                </div>
            </div>
            
            <div class="flex items-start justify-center gap-3 text-left mb-8 bg-white border-gray-200 dark:bg-white/5 p-4 rounded-lg border dark:border-white/10 shadow-sm dark:shadow-none">
                <i class="fas fa-exclamation-triangle text-gnosis-accent dark:text-gnosis-gold text-xl mt-1"></i>
                <div class="text-sm md:text-base">
                    <p class="font-bold text-gnosis-purple dark:text-gnosis-gold mb-1">
                        <a href="{{$Solicitud->url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual}}">Únete al grupo exclusivo de WhatsApp</a>
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 text-xs md:text-sm">
                        El grupo es silenciado y se usará exclusivamente para enviarte los enlaces a las clases.
                    </p>
                </div>
            </div>
            
            <a href="https://chat.whatsapp.com/..." target="_blank" class="inline-flex items-center justify-center w-full md:w-auto bg-green-600 hover:bg-green-500 text-white font-bold text-lg py-4 px-8 rounded-lg shadow-[0_0_20px_rgba(34,197,94,0.4)] transition transform hover:-translate-y-1 uppercase tracking-wide gap-3 group">
                <i class="fab fa-whatsapp text-2xl group-hover:scale-110 transition duration-300"></i>
                ÚNETE AL GRUPO DE WHATSAPP AHORA
            </a>
        </div>
    </section>

    <!-- SECCIÓN: BENEFICIOS WHATSAPP -->
    <section class="py-20 px-4 bg-white dark:bg-cosmos relative overflow-hidden border-t border-gray-100 dark:border-white/5 transition-colors duration-500">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#6366f1 1px, transparent 1px); background-size: 40px 40px;"></div>
        
        <div class="container mx-auto relative z-10">
            <!-- TÍTULO AGRANDADO -->
            <h2 class="text-3xl md:text-5xl font-heading font-bold text-center mb-16 text-gnosis-purple dark:text-gnosis-gold leading-tight">
                ¿Qué beneficios exclusivos te esperan <br class="hidden md:block"> en el grupo de WhatsApp?
            </h2>

            <div class="grid md:grid-cols-12 gap-8 md:gap-4 items-center">
                <!-- Columna Texto: Orden 1 en Móvil (Primero) -->
                <div class="space-y-8 order-1 md:order-1 md:col-span-4 relative z-30">
                    
                    <!-- Item 1 -->
                    <div class="flex items-start gap-4 group bg-slate-50 dark:bg-black/40 p-4 rounded-xl backdrop-blur-sm border border-gray-200 dark:border-white/5 hover:border-gnosis-accent/30 dark:hover:border-gnosis-gold/30 transition shadow-sm dark:shadow-none">
                        <!-- Icono Número agrandado a w-12 h-12 y texto xl -->
                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 text-indigo-700 dark:bg-gradient-to-b dark:from-gnosis-gold dark:to-yellow-700 rounded-lg flex items-center justify-center dark:text-black font-heading font-bold text-xl shadow-sm dark:shadow-lg dark:border dark:border-yellow-300 group-hover:scale-110 transition duration-300">
                            1
                        </div>
                        <div>
                            <!-- Título agrandado a text-2xl -->
                            <h3 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-2xl mb-2">Material Exclusivo</h3>
                            <!-- Descripción agrandada a text-lg -->
                            <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                                Guías, audios y lecturas.
                            </p>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="flex items-start gap-4 group bg-slate-50 dark:bg-black/40 p-4 rounded-xl backdrop-blur-sm border border-gray-200 dark:border-white/5 hover:border-gnosis-accent/30 dark:hover:border-gnosis-gold/30 transition shadow-sm dark:shadow-none">
                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 text-indigo-700 dark:bg-gradient-to-b dark:from-gnosis-gold dark:to-yellow-700 rounded-lg flex items-center justify-center dark:text-black font-heading font-bold text-xl shadow-sm dark:shadow-lg dark:border dark:border-yellow-300 group-hover:scale-110 transition duration-300">
                            2
                        </div>
                        <div>
                            <h3 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-2xl mb-2">Orientación Directa</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                                Canal con instructores.
                            </p>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="flex items-start gap-4 group bg-slate-50 dark:bg-black/40 p-4 rounded-xl backdrop-blur-sm border border-gray-200 dark:border-white/5 hover:border-gnosis-accent/30 dark:hover:border-gnosis-gold/30 transition shadow-sm dark:shadow-none">
                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 text-indigo-700 dark:bg-gradient-to-b dark:from-gnosis-gold dark:to-yellow-700 rounded-lg flex items-center justify-center dark:text-black font-heading font-bold text-xl shadow-sm dark:shadow-lg dark:border dark:border-yellow-300 group-hover:scale-110 transition duration-300">
                            3
                        </div>
                        <div>
                            <h3 class="text-gnosis-purple dark:text-gnosis-gold font-bold text-2xl mb-2">Comunidad Viva</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                                Conexión con estudiantes.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Columna Imagen: Orden 2 en Móvil (Debajo) + Imagen Gigante -->
                <div class="flex justify-center order-2 md:order-2 md:col-span-8 relative z-20 mt-4 md:mt-0">
                    <div class="relative w-full flex justify-center translate-x-4 md:translate-x-0">
                        <div class="absolute inset-0 bg-indigo-500/10 dark:bg-gnosis-gold/10 blur-3xl rounded-full scale-[2]"></div>
                        <img src="https://ac.gnosis.is/img/landing/contenido-grupo-whatsapp.png" 
                             alt="Contenido Exclusivo" 
                             class="relative w-full drop-shadow-2xl hover:scale-[1.2] transition duration-700 animate-[pulse_5s_ease-in-out_infinite] transform scale-125 md:scale-[1] origin-center z-10">
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

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->


</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->
