
@extends('layouts.backend')

@section('contenido')

<?php 
/*
echo date("G:H:s");
$hora = new DateTime("now", new DateTimeZone('America/New_York'));
echo '<br><br>'.$hora->format('G');
date_default_timezone_set('America/New_York');
*/

use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;

function sino_a_tf($sino) {
  if ($sino == 'SI') {
    $tf = 'true';
  }
  else {
    if ($sino == 'NO') {
        $tf = 'false';
      }
    else {
        $tf = 'null';
      }
  }

  return $tf;
}

?>

<!-- LIBRERIAS -->
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
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/generic.css">

  <!-- moment.min.js -->
  <!-- script src="<?php echo env('PATH_PUBLIC')?>js/Moment/moment-with-locales.min.js"></script -->
  <script src="<?php echo env('PATH_PUBLIC')?>js/Moment/moment.min.js"></script>
  <!-- datetimepicker.js -->
  <script src="<?php echo env('PATH_PUBLIC')?>js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css">  

  <!-- GoogleAddress -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBR3Wx1QeIIBC5ZD1C0o09QFzea9tz6ZbU&libraries=places"></script>

<!-- LIBRERIAS -->


<!-- Content Header (Page header) -->
<section class="content-header">
<h1>
  <?php echo __($Solicitud->Tipo_de_evento->tipo_de_evento) ?>: <?php echo $Fecha_de_evento->id; ?>
  <small>Localidad: <?php echo $Solicitud->localidad_nombre(); ?> </small>
</h1>


<ol class="breadcrumb">
  <li><a href="<?php echo env('PATH_PUBLIC')?>"><i class="fa fa-home"></i> Home</a></li>
  <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list/t"><?php echo __('Solicitudes') ?></a></li>
  <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/ver/<?php echo $Solicitud->id ?>"><?php echo __('Solicitud') ?></a></li>
  <li class="active"><?php echo __('Evento') ?> </li>
</ol>
</section>

<!-- MAIN CONTENT -->
  <section class="content">
    <div class="row">

      <div id="app-solicitud" class="box box-primary">
        <div class="box-body">
            <div class="col-xs-12 col-lg-6">
              <?php if ($Solicitud->tipo_de_evento_id == 1) { ?>
                <a class="btn btn-block btn-social btn-instagram" data-toggle="modal" data-target="#modal-flyers" class="btn btn-default btn-md" style="margin-top: 10px;">
                  <i class="fa fa-instagram"></i> <?php echo __('Flyers') ?>
                </a>
              <?php } ?>
            </div>          

            <div class="col-xs-12 col-lg-6">
              <div v-bind:class="class_sino(es_instructor)">
                
                  <?php echo __('Soy instructor de este grupo') ?>

                  <label class="switch switch-inscripcion">
                    <input type="checkbox" v-on:change="setearSiEsInstructor()" v-model="es_instructor">
                    <span class="slider round"></span>
                    
                  </label>
              </div>
            </div>


                <!--div class="col-lg-12">            
                  <pre>@{{ $data }}</pre>
                </div-->  

        </div>
      </div>

      <!-- PANEL FECHAS -->

          <?php 
          $cant_inscriptos = 0;
          $cant_contactados = 0;
          $cant_confirmados = 0;
          $cant_vouchers = 0;
          $cant_motivacion = 0;
          $cant_recordatorio = 0;
          $cant_asistentes = 0;
          
          $cant_inscriptos = $cant_inscriptos + $Fecha_de_evento->cant_inscriptos();
          $cant_contactados = $cant_contactados + $Fecha_de_evento->cant_contactados();
          $cant_confirmados = $cant_confirmados + $Fecha_de_evento->cant_confirmados();
          $cant_vouchers = $cant_vouchers + $Fecha_de_evento->cant_vouchers();
          $cant_motivacion = $cant_motivacion + $Fecha_de_evento->cant_motivacion();
          $cant_recordatorio = $cant_recordatorio + $Fecha_de_evento->cant_recordatorio();
          $cant_asistentes = $cant_asistentes + $Fecha_de_evento->cant_asistentes();

          ?> 
          <!-- PANEL INSCRIPTOS -->
            <div class="box box-default collapsed-box box-solid">

              <div class="box-header with-border">
                <div class="col-xs-10 col-lg-3"><?php echo $Fecha_de_evento->armarDetalleFechasDeEventos()  ?></div>


                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-green" style="width: 60px"><i class="ion ion-ios-people-outline"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text"><?php echo __('Inscriptos') ?>: <strong><?php echo $Fecha_de_evento->cant_inscriptos() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Contactados') ?>: <strong><?php echo $Fecha_de_evento->cant_contactados() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Confirmados') ?>: <strong><?php echo $Fecha_de_evento->cant_confirmados() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Voucher') ?>: <strong><?php echo $Fecha_de_evento->cant_vouchers() ?></strong></span>
                        
                        
                      </div>

                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                  </div>

                  <div class="col-md-3 col-sm-6 col-xs-8">
                    <div class="info-box">
                      <span class="info-box-icon bg-green" style="width: 60px"><i class="ion ion-ios-people-outline"></i></span>

                      <div class="info-box-content">
                        <span class="info-box-text"><?php echo __('Motivacion') ?>: <strong><?php echo $Fecha_de_evento->cant_motivacion() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Recordatorio') ?>: <strong><?php echo $Fecha_de_evento->cant_recordatorio() ?></strong>
                        <span class="info-box-text"><?php echo __('Asistentes') ?>: <strong><?php echo $Fecha_de_evento->cant_asistentes() ?></strong></span>
                        <span class="info-box-text"><?php echo __('Cupo Máximo') ?>: <strong><?php echo $Fecha_de_evento->cupo_maximo_disponible_del_salon ?></strong></span>
                        
                      </div>
                      <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                  </div>

                  <?php if ($Solicitud->idioma_id == 1) { ?>
                  <div class="col-md-1 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <a href="<?php echo $Fecha_de_evento->url_encuesta_de_satisfaccion() ?>" target="_blank">
                        <span class="info-box-icon bg-red" style="width: 75px"><i class="ion ion-pie-graph"><p style="font-size: 30px;"><?php echo $Fecha_de_evento->cant_encuestas() ?></p></i></span>
                      </a>
                    </div>
                  </div>
                  <?php } ?>

                  <?php 
                  if ($Fecha_de_evento->cupo_maximo_disponible_del_salon < $Fecha_de_evento->cant_confirmados()) { 
                    $capacidad_max_estimada_confirmados = intval($Fecha_de_evento->cupo_maximo_disponible_del_salon*100/70);
                    $texto_exedio_capacidad = __('La cantidad de confirmados supera a la capacidad del salon, nuestras estadísticas nos permiten saber que el 70% de los confirmados son los que asisten, ud. podria llegar a confirmar hasta').' <strong>'.$capacidad_max_estimada_confirmados.'</strong> '.__('personas');

                  ?>
                    <div style="clear: both;">
                      <p class="p-error"><?php echo $texto_exedio_capacidad ?> </p>
                    </div>
                  <?php } ?>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_solicitud('Fecha_de_evento', 'm', <?php echo $Fecha_de_evento->id ?>)"><?php echo __('Modificar') ?></button> 
                  <button type="button" class="btn btn-default btn-md" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM_solicitud('Fecha_de_evento', 'b', <?php echo $Fecha_de_evento->id ?>)"><?php echo __('Eliminar') ?></button>
                    <a target="_blank" href="<?php echo env('PATH_PUBLIC')?>f/x/<?php echo $Solicitud->id ?>/<?php echo $Solicitud->hash ?>/<?php echo $Fecha_de_evento->id ?>">
                      <button alt="Descargar Lista a Excel" title="Descargar Lista a Excel" type="button" class="btn btn-default btn-md"><i class="fa fa-file-excel-o"></i></button>
                    </a>
                    <button type="button" class="btn btn-default btn-md" data-widget="collapse"><i class="fa fa-plus"></i>
                    </button>
                </div>
              </div>

              <div class="box-body">
                <div class="modal-body" id="modal-bodi-list_<?php echo $Fecha_de_evento->id ?>"></div>
                <?php 
                $gen_seteo = array(
                  'gen_url_siguiente' => 'back', 
                  'gen_permisos' => ['C','R','U', 'D'],
                  'gen_campos_a_ocultar' => 'solicitud_id|fecha_de_evento_id|sino_notificar_proximos_eventos|sino_acepto_politica_de_privacidad|sino_envio_pedido_de_confirmacion|sino_confirmo|sino_envio_recordatorio_pedido_de_confirmacion|sino_envio_voucher|sino_envio_motivacion|sino_envio_recordatorio||-created_at|url_enlace_a_google_maps_inicio_redirect_final|url_enlace_a_google_maps_curso_redirect_final|pais_id|ciudad|created_at|updated_at|',
                  'no_mostrar_campos_abm' => 'solicitud_id|fecha_de_evento_id',
                  'filtro_where' => array('fecha_de_evento_id', '=', $Fecha_de_evento->id),
                  'tabla_condensada' => 'SI'
                );

                ?>
                <script type="text/javascript">
                  
                  $.ajax({
                    url: '<?php echo env('PATH_PUBLIC')?>crearlista',
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      gen_modelo: 'Inscripcion',
                      gen_seteo: '<?php echo serialize($gen_seteo) ?>',
                      gen_opcion: ''
                    },
                    success: function success(data, status) {        
                      $("#modal-bodi-list_<?php echo $Fecha_de_evento->id ?>").html(data);
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                  });
                </script>              
              </div>
            </div>
          <!-- FIN PANEL INSCRIPTOS -->



      <!-- FIN PANEL FECHAS -->
       
  

  </section>
<!-- MAIN CONTENT -->

<!-- MODAL ABM -->
  <div class="modal modal fade" id="modal-solicitud-abm">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="modal-titulo">Info Modal</div></h4>
        </div>
        <div class="modal-body" id="modal-bodi-abm">

        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<!-- MODAL ABM -->
   

<!-- INICIO APP app-solicitud -->
    <script type="text/javascript">
        const config = {
          locale: 'es', 
        };
        //moment.locale('es');
        //console.log(moment());
        Vue.use(VeeValidate, config);

        var app = new Vue({
          el: '#app-solicitud',

          data: {
            mensaje_extra: '',
            es_instructor: <?php echo $Fecha_de_evento->es_instructor(Auth::user()->id) ?>
          },

          methods: {                

            setearSiEsInstructor: function () {

              if (this.es_instructor) {
                sino = 'SI';
              }
              else {
                sino = 'NO';
              }
              

              $.ajax({
                url: '<?php echo env('PATH_PUBLIC')?>c/setear-sino-es-instructor',
                type: 'POST',
                dataType: 'html',
                async: true,
                data:{
                  _token: "{{ csrf_token() }}",
                  fecha_de_evento_id: <?php echo $Fecha_de_evento->id ?>,
                  user_id: <?php echo Auth::user()->id ?>,
                  sino: sino
                },
                success: function success(data, status) {    
                  //$("#resultado-a").html(data);
                  
                },
                error: function error(xhr, textStatus, errorThrown) {
                    alert(errorThrown);
                }
              });


            },
            

            marcar_envio: function (codigo, inscripcion_id) {
              if (codigo == 1) {
                  this.es_instructor = true;  
              }

              this.setearSino(codigo, this.es_instructor)
              /*
              $.ajax({
                url: '<?php echo env('PATH_PUBLIC')?>f/i/registrar-envio/'+codigo+'/'+inscripcion_id,
                type: 'POST',
                dataType: 'html',
                async: true,
                data:{
                  _token: "{{ csrf_token() }}",
                  sino: sino
                },
                success: function success(data, status) {    

                  
                },
                error: function error(xhr, textStatus, errorThrown) {
                    alert(errorThrown);
                }
              });
              */

            },
              
            class_sino: function (sino) {
              if (sino) {
                clase = 'bg-olive'
              }
              else {
                if (sino === null) {
                  clase = 'bg-grey'
                }
                else {
                  clase = 'bg-red'
                }
              }
              clase = clase+' div-paso-inscripcion'
              return clase
            },


            txt_sino: function (sino) {
              if (sino) {
                texto = 'SI'
              }
              else {
                if (sino === null) {
                  texto = ''
                }
                else {
                  texto = 'NO'
                }
              }
              
              return texto
            },
            

            url_mensaje_extra: function (celular, nombre, apellido) {
              mensaje = '<?php echo __('Hola').' '.$Solicitud->nombre_responsable_de_inscripciones.' '.__('mi nombre es').' '.Auth::user()->name.'. '.__('Te estoy enviando los datos de la campaña').': *'.$Solicitud->localidad_nombre()?>*:\n\n'
              mensaje = mensaje +'*<?php echo __('Formulario de Inscripcion') ?>*:\n <?php echo $Solicitud->url_form_inscripcion() ?> \n\n'
              mensaje = mensaje + '*<?php echo __('Planilla de Inscripción') ?>*:\n<?php echo $Solicitud->url_planilla_inscripcion() ?>\n\n'
              mensaje = mensaje + '*<?php echo __('Descargar lista de inscriptos a Excel') ?>*:\n <?php echo $Solicitud->url_planilla_inscripcion_excel(0) ?>\n\n'
              mensaje = mensaje + '*<?php echo __('Planilla de Asistencia') ?>*:\n <?php echo $Solicitud->url_planilla_asistencia() ?>\n\n'
                mensaje = mensaje + '*<?php echo __('Encuesta de Satisfacción') ?>*:\n <?php echo $Solicitud->url_encuesta_de_satisfaccion() ?>\n\n'
              url_mensaje_extra = 'https://api.whatsapp.com/send?phone='+celular+'&text='+mensaje;
              url_mensaje_extra = encodeURI(url_mensaje_extra)
              return url_mensaje_extra
            }
              
          },


        })
    </script>
<!-- FIN APP app-solicitud -->


<?php if ($Solicitud->tipo_de_evento_id == 1) { ?>
  <!-- MODAL FLYERS -->
    <div class="modal modal fade" id="modal-flyers">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><div id="modal-flyers-titulo"><?php echo __('Flyers') ?></div></h4>
          </div>

          <div class="modal-body" id="modal-bodi-flyers"> 

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
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id ?>/1">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/2">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/3">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/4">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/5">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/6">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/7">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/8">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/9">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/10">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/11">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/12">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/13">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/14">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/15">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/16">
                </div>
                <div class="item">
                  <img id="imgFinal" src="<?php echo env('PATH_PUBLIC')?>flyer/<?php echo $Solicitud->id  ?>/17">
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

          </div>

          <div class="modal-footer">
            <center>
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancelar') ?></button>
            </center>  
            <input type="hidden" name="sino_aprobado_administracion" value="NO">
          </div>

        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
  <!-- MODAL FLYERS -->

<?php } ?>

@endsection

