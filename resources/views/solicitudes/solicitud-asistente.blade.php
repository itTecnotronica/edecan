@extends('layouts.backend')

@section('contenido')

<?php 
if (!isset($paso)) {
  $paso = 1;
}
//echo __('messages.welcome');
$steps = 6;
$width_step = 19.5; 
$width_progress_bar = $width_step*($paso-1);

if ($paso == 1) {
  $titulo = __('Seleccione el tipo de evento');
}
if ($paso == 2) {
  //$url = env('PATH_PUBLIC').'Solicitudes/crear/listar-modelos-para-seleccion/'.$solicitud_id;
  if ($Solicitud->Tipo_de_evento->id == 1) { 
    $titulo = __('Datos del Solicitante');
  }
  else {
    $titulo = 'Datos del Solicitante y Temática de Conferencia';
  }
}
if ($paso == 3) {
  $url = env('PATH_PUBLIC').'Solicitudes/crear/fechas-de-evento/'.$solicitud_id;
  if ($Solicitud->Tipo_de_evento->id == 1) { 
    $titulo = __('Determine Fecha y lugar del inicio de cursos');
  }
  if ($Solicitud->Tipo_de_evento->id == 2) { 
    $titulo = __('Indique una o mas conferencias públicas');
  }  
  if ($Solicitud->Tipo_de_evento->id == 3) { 
    $titulo = __('Determine Fecha y hora del inicio de cursos');
  }  
  if ($Solicitud->Tipo_de_evento->id == 4) { 
    $titulo = __('Determine las opciones que tendra el formulario');
  }  
}
if ($paso == 4) {
  $titulo = __('Indique los datos de la campaña');
}
if ($paso == 5) {
  $titulo = __('Resumen de la Solicitud').' - id: '.$solicitud_id;
}
if ($paso == 6) {
  $titulo = __('Envio de Solicitud').' - id: '.$solicitud_id;
}

use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;

use \App\Http\Controllers\SolicitudController; 
$SolicitudController = new SolicitudController;

$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;

if (!isset($href_volver)) {
  $href_volver = 'javascript:window.history.back();';
}

?>

<style>
.wrapper {
background-color: white !important;
}
</style>


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


<!-- Content Header (Page header) -->
<section class="content-header">
<h1>
  <?php echo __('Nueva Soliciud'); ?>
  <small>Asistente</small>
</h1>
<ol class="breadcrumb">
  <li><a href="<?php echo env('PATH_PUBLIC')?>"><i class="fa fa-home"></i> Home</a></li>
  <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list">Solicitudes</a></li>
  <li class="active"><?php echo __('Nueva Soliciud'); ?></li>
</ol>
</section>

<!-- Main content -->
<section class="content">
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">


      <div class="box-header col-xs-1" style="margin-bottom: 20px; margin-right: 20px; z-index: 1">
        <a href="<?php echo $href_volver ?>">
          <button type="button" class="btn btn-success btn-lg"><i class="fa fa-step-backward"></i> <?php echo __('Volver'); ?></button>
        </a>    
      </div>  

      <div class="box-header col-xs-10" style="margin-bottom: 100px;">

        <!-- SLIDER -->
        <div class="slider slider-horizontal" id="green" style="margin-top: 20px; margin-bottom: 50px; box-shadow: none; background: none">
          <div class="slider-track">
            <div class="slider-selection" style="left: 0%; width: <?php echo $width_progress_bar; ?>%;"></div>
            <!--div class="slider-handle min-slider-handle round">0</div-->
            <?php 
            $left_step = 0;
            for($i=1; $i<=$steps; $i++) { 
              if ($i > 1) {
                $left_step = $left_step+$width_step;  
              }
              
              if ($i <= $paso) {
                $class_slider_gris = '';
              }
              else {
                $class_slider_gris = 'slider-gris';
              }

            ?>
            <div class="slider-handle max-slider-handle round <?php echo $class_slider_gris; ?>" style="left: <?php echo $left_step; ?>%"><?php echo $i; ?>
              <div class="txt_paso_info"><br>
                <?php 
                if (isset($pasos_info[$i-1])) {
                  echo $pasos_info[$i-1];
                }
                ?>                    
              </div>
            </div>
            <?php } ?>
          </div>
        </div>

      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row margin">
          <h2><?php echo $titulo; ?></h2>


          <div id="div-contenedor"></div>

          <!-- PASO 1 - DATOS SOLICITANTE -->
            <?php if ($paso == 1) { ?>          
                <div class="panel-body">
                  <div id="app-tipo-solicitud">                  

                    <div class="col-lg-6">                    
                      <p><a v-bind:href="enlaceCrearSolicitudSegunTipo(1)"><button type="button" class="btn btn-primary btn-lg center-block btn-sel-tipo-de-evento"><?php echo __('Curso de Auto-Conocimiento') ?></button></a></p>
                      <p><a v-bind:href="enlaceCrearSolicitudSegunTipo(2)"><button type="button" class="btn btn-primary btn-lg center-block btn-sel-tipo-de-evento"><?php echo __('Conferencia Pública') ?></button></a></p>
                      <p><a v-bind:href="enlaceCrearSolicitudSegunTipo(3)"><button type="button" class="btn btn-primary btn-lg center-block btn-sel-tipo-de-evento"><?php echo __('Curso On Line de Auto-Conocimiento') ?></button></a></p>
                      <p><a v-bind:href="enlaceCrearSolicitudSegunTipo(4)"><button type="button" class="btn btn-primary btn-lg center-block btn-sel-tipo-de-evento"><?php echo __('Formulario de Recoleccion de Datos') ?></button></a></p>

                      <hr>

                      <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>

                      <!--pre>@{{ $data }}</pre-->

                      </div>
                  </div>
                </div>
            <?php } ?>
          <!-- PASO 1 - DATOS SOLICITANTE -->

          <!-- PASO 2 - DATOS SOLICITANTE -->
            <?php if ($paso == 2) { ?>          
                <p><?php echo __('El solicitante será consultado ante dudas sobre esta campaña') ?></p>
                <div class="panel-body">
                  {!! Form::open(array
                    (
                    'url' => env('PATH_PUBLIC').'Solicitudes/crear/datos-del-evento/'.$Solicitud->id, 
                    'role' => 'form',
                    'method' => 'POST',
                    'id' => "form_gen_modelo",
                    'enctype' => 'multipart/form-data',
                    'class' => 'form-vertical',
                    'ref' => 'form'
                    )) 
                  !!}
                    <div id="app-datos-solicitente">                  
                      <div class="col-md-4 col-sm-6 col-xs-12">
                        <vue-form-generator @validated="onValidated" :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
                        <input type="hidden" name="solicitud_id" value="<?php echo $solicitud_id ?>">
                      </div>


                        <!--div class="col-lg-12">            
                            <pre>@{{ $data }}</pre>
                        </div--> 


                    </div>
                </div>

                {!! Form::close() !!}
            <?php } ?>
          <!-- PASO 2 - DATOS SOLICITANTE -->



          <!-- PASO 3 - FECHAS -->
            <?php if ($paso == 3 and (isset($cant_fechas) and $cant_fechas > 0))  { ?>
              <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear/datos-de-la-campania/<?php echo $solicitud_id?>">
                <button type="button" class="btn btn-success btn-lg center-block"><?php echo __('Continuar') ?></button>
              </a>
            <?php } ?>
          <!-- PASO 3 - FECHAS -->



          <!-- PASO 4 - DATOS SOLICITANTE -->
            <?php if ($paso == 4) { ?>  
                <div class="panel-body">
                  {!! Form::open(array
                    (
                    'action' => $action_form, 
                    'role' => 'form',
                    'method' => 'POST',
                    'id' => "form_gen_modelo",
                    'enctype' => 'multipart/form-data',
                    'class' => 'form-vertical',
                    'ref' => 'form'
                    )) 
                  !!}
                    <div id="app-datos-campania">                  
                      <div class="col-md-4 col-sm-6 col-xs-12">
                        <vue-form-generator @validated="onValidated" :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
                        <input type="hidden" name="solicitud_id" value="<?php echo $solicitud_id ?>">
                      </div>
                    </div>
                  {!! Form::close() !!}
                </div>
            <?php } ?>
          <!-- PASO 4 - DATOS SOLICITANTE -->







        <?php if ($paso == 5) { ?>
        <br>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <table class="table table-bordered tabla-datos-finales-asistente">
              <tbody>

                <tr>
                  <td>1.</td>
                  <td><?php echo __('Evento') ?></td>
                  <td>
                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo __($Solicitud->Tipo_de_evento->tipo_de_evento) ?></span><br>
                    <?php 
                    $pais = '';
                    if($Solicitud->localidad_id > 0) {
                        $localidad = $Solicitud->Localidad->localidad;
                        $pais = $Solicitud->Localidad->Provincia->Pais->pais;
                    }
                    else {
                        $localidad = $Solicitud->escribe_tu_ciudad_sino_esta_en_la_lista_anterior;
                        if ($Solicitud->pais_id <> '') {
                          $pais = $Solicitud->Pais->pais;
                        }
                    }                    
                    ?>
                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo $localidad ?> (<?php echo $pais ?>)</span><br>
                  </td>
                </tr>

                <tr>
                  <td>2.</td>
                  <td><?php echo __('Solicitante') ?></td>
                  <td>
                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo $Solicitud->nombre_del_solicitante ?></span><br>
                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Celular') ?>: <?php echo $Solicitud->celular_del_solicitante ?></span><br>
                  </td>
                </tr>

                <tr>
                  <td>3.</td>
                  <td><?php echo __('Fechas') ?></td>
                  <td>
                    <?php 
                    if ($Solicitud->Tipo_de_evento->id == 3 and $Solicitud->tipo_de_curso_online_id == 2) { 
                      echo __('Fecha de inicio del curso online').': '.$gCont->FormatoFecha($Solicitud->fecha_de_inicio_del_curso_online);
                    }
                    if ($Solicitud->Tipo_de_evento->id == 3 and $Solicitud->tipo_de_curso_online_id == 3) {
                      echo __('Fecha de inicio del curso online').': '.$gCont->FormatoFecha($Solicitud->fecha_de_inicio_del_curso_online).' '.$Solicitud->hora_de_inicio_del_curso_online;
                    } 
                    foreach ($Fechas_de_eventos as $Fecha_de_evento) { 
                      echo $Fecha_de_evento->armarDetalleFechasDeEventos().'<hr>';
                    } 
                    ?>
                  </td>
                </tr>

                <tr>
                  <td>4.</td>
                  <td><?php echo __('Campaña') ?></td>
                  <td>
                    <?php 
                    $idioma = '';
                    if ($Solicitud->idioma_id <> '') {
                      $idioma = $Solicitud->idioma->idioma;
                    }
                    $moneda = '';
                    if ($Solicitud->moneda_id <> '') {
                      $moneda = $Solicitud->Moneda->moneda;
                    }
                    ?>
                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Idioma') ?>: <?php echo $idioma ?></span><br>
                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Importe') ?>: <?php echo __($moneda) ?> <?php echo $gCont->formatoNumero($Solicitud->monto_a_invertir, 'entero'); ?> </span><br>
                    <?php if ($Solicitud->payment_status <> '') { ?>
                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Estado') ?> Paypal:</span> <?php echo $Solicitud->payment_status ?><br>
                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Importe') ?> Paypal:</span> $ <?php echo $Solicitud->paypal_value ?><br>
                    <?php } ?>                    
                    <?php if ($Solicitud->sino_solicitar_responsable_de_inscripcion == 'NO') { ?>
                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Responsable de Inscripción') ?>: <?php echo $Solicitud->nombre_responsable_de_inscripciones ?></span><br>
                      <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Celular Responsable de Inscripción') ?>: <?php echo $Solicitud->celular_responsable_de_inscripciones ?></span><br>
                    <?php } 
                    else {?>
                      <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Solicitar responsable de inscripción') ?>: <?php echo $Solicitud->sino_solicitar_responsable_de_inscripcions ?></span><br>
                    <?php } ?>

                    <span class="badge bg-light-blue datos-finales-asistente"><?php echo __('Observaciones') ?>: <?php echo $Solicitud->observaciones ?></span><br>
                  </td>
                </tr>

                <tr>
                  <td></td>
                  <td></td>
                  <td><h3><?php echo __('Por favor revisa el formulario para controlar que todos los datos estan correctos') ?></h3>
                    <!-- BOTON Ver Formulario -->
                      <?php 
                      $hash = $Solicitud->hash;
                      if ($hash == '') {
                        $hash = 'nulo';
                      } ?>
                      <a target="_blank" href="<?php echo $Solicitud->url_form_inscripcion() ?>">
                        <button type="button" class="btn btn-block btn-primary btn-lg"><i class="fa fa-file-text-o"></i> <?php echo __('Ver Formulario') ?> </button>
                      </a>
                  </td>
                </tr>
              </tbody>
            </table>
            <div style="width: 100%">
              <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear/enviar-solicitud/<?php echo $solicitud_id?>">
                <button type="button" class="btn btn-success btn-lg"><?php echo __('Enviar solicitud para aprobacion') ?></button>
              </a>
            </div>
          </div>

        <?php } ?>



        <?php if ($paso == 6) { ?>
          <br><br>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> <?php echo __('Felicitaciones') ?>!</h4>
            <?php echo __('Solicitud enviada satisfactoriamente.') ?>
          </div>
          <br>


          <?php if ($Solicitud->ejecutivo <> '') { ?>
          <p>
            <?php echo __('Esta campaña se ha asignado a un Ejecutivo de Campaña, desea comunicarse con el?') ?>:
<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="https://api.whatsapp.com/send?phone=<?php echo $Solicitud->ejecutivo_asignado()->celular ?>&text=<?php echo __('Hola').' '.$Solicitud->ejecutivo_asignado()->name.'...' ?>" target="_blank" >
              <span class="info-box-icon bg-green"><i class="fa fa-whatsapp"></i></span>
            </a>

            <div class="info-box-content">
              <span class=""><?php echo $Solicitud->ejecutivo_asignado()->name ?></span><br>
              <span class=""><?php echo __('Celular') ?>: </span>
              <span class=""><?php echo $Solicitud->ejecutivo_asignado()->celular ?></span>

              
              <a href="https://api.whatsapp.com/send?phone=<?php echo $Solicitud->ejecutivo_asignado()->celular ?>&text=<?php echo __('Hola')?> <?php echo $Solicitud->ejecutivo_asignado()->name ?> <?php echo __('acerca de la campaña') ?> ID: <?php echo $Solicitud->id ?> de <?php echo $Solicitud->localidad_nombre() ?> <?php echo __('que hemos solicitado') ?>" target="_blank">
                <button type="button" class="btn btn-sm btn-default" alt="editar"><i class="fa fa-whatsapp"></i> <?php echo __('Enviar mensaje') ?></button>
              </a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
              
          </p>
          <?php } ?>

          <br><br>
          <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear">
            <button type="button" class="btn btn-success btn-lg center-block"><?php echo __('Agregar nueva Solicitud') ?></button>
          </a>
        <?php } ?>

      </div>
    </div>
  </div>
</div>
</section>

  
          
<?php if (isset($url)) { ?>
<script type="text/javascript">
$.ajax({
  url: '<?php echo $url?>',
  type: 'POST',
  dataType: 'html',
  async: true,
  data:{
    _token: "{{ csrf_token() }}"
  },
  success: function success(data, status) {        
    $("#div-contenedor").html(data);
  },
  error: function error(xhr, textStatus, errorThrown) {
      alert(errorThrown);
  }
});

</script>
<?php } ?>

<!-- PASO 1 - TIPO DE SOLICITUD -->
  <?php if ($paso == 1) { ?>
    <script type="text/javascript">

      var VueFormGenerator = window.VueFormGenerator;

      VueFormGenerator.validators.decimal = function(value, field, model) {
        if (typeof value !== 'undefined') {
          /*
          if (typeof value == 'string') {
            valor = Number(value.replace(",", "."));
          }
          else {
            valor = value;
          }
          */
          valor = value;
          if(isNaN(valor)) {
            return ["No es un valor decimal"];
          }
        }
        return [];
      }

      var vm = new Vue({
        el: "#app-tipo-solicitud",
        components: {
          "vue-form-generator": VueFormGenerator.component
        },

        methods: {
          prettyJSON: function (json) {
            if (json) {
              json = JSON.stringify(json, undefined, 4);
              json = json.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
              return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                var cls = "number";
                if (/^"/.test(match)) {
                  if (/:$/.test(match)) {
                    cls = "key";
                  } else {
                    cls = "string";
                  }
                } else if (/true|false/.test(match)) {
                  cls = "boolean";
                } else if (/null/.test(match)) {
                  cls = "null";
                }
                return "<span class=\"" + cls + "\">" + match + "</span>";
              });
            }
          },
          onValidated(isValid, errors) {
            console.log("Validación del Form app-datos-solicitente: ", isValid, ", Errors:", errors);
            isValid = true
            /*
            var fecha_de_vencimiento_de_la_solicitud = $("#fecha_de_vencimiento_de_la_solicitud").val();
            console.log("fecha_de_vencimiento_de_la_solicitud: ", fecha_de_vencimiento_de_la_solicitud);
            var valor_total = Number($("#valor_total").val());
            var valor_total_calculado = Number($("#valor_total_calculado").val());
            var anticipo = $("#anticipo").val();
            if(valor_total < valor_total_calculado) {
              $("#valor_total_error").html('El valor ingresado es menor al valor calculado por m<sup>2</sup>, valor m&iacute;nimo: '+valor_total_calculado);
              isValid = false;
            }
            if(anticipo > valor_total) {
              $("#valor_total_error").html('El valor del anticipo no puede ser mayor al valor total de la propiedad');
              isValid = false;
            }
            if(fecha_de_vencimiento_de_la_solicitud == '') {
              $("#fecha_de_vencimiento_de_la_solicitud_error").html('Este campo es obligatorio');
              isValid = false;
            }
            */
            if (!isValid) {
                event.preventDefault();  
            }      
          }
        },

        data: {
          model: {
            sino_es_campania_de_capacitacion: false,
            url: ''
          },
          schema: {
            fields: [
              {
                type: "switch", 
                model: "sino_es_campania_de_capacitacion",     
                label: "Es una campaña de entrenamiento (CAPACITACIÓN)?",   
                id: "sino_solicitar_responsable_de_inscripcion_switch",  
                inputName: "sino_solicitar_responsable_de_inscripcion_switch",          
                textOn: "SI", textOff: "NO", valueOn: true, valueOff: false
              }
            ]
          },


          formOptions: {
            validateAfterLoad: false,
            validateAfterChanged: false
          }
        },

        methods: {
          enlaceCrearSolicitudSegunTipo(tipo_de_evento_id) {
            let parametro = ''
            if (this.model.sino_es_campania_de_capacitacion) {
              parametro = 't'
            }
            else {
              parametro = 'f'
            }

            let enlace = '<?php echo env('PATH_PUBLIC')?>Solicitudes/crear/elegir-tipo-de-evento/'+tipo_de_evento_id+'/'+parametro
            
            return enlace

          }
        }
      });

    </script>
  <?php } ?>
<!-- PASO 1 - DATOS SOLICITANTE VUEJS -->

<!-- PASO 2 - DATOS SOLICITANTE VUEJS -->
  <?php if ($paso == 2) { ?>
    <script type="text/javascript">

      var VueFormGenerator = window.VueFormGenerator;

      VueFormGenerator.validators.decimal = function(value, field, model) {
        if (typeof value !== 'undefined') {
          /*
          if (typeof value == 'string') {
            valor = Number(value.replace(",", "."));
          }
          else {
            valor = value;
          }
          */
          valor = value;
          if(isNaN(valor)) {
            return ["No es un valor decimal"];
          }
        }
        return [];
      }

      var vm = new Vue({
        el: "#app-datos-solicitente",
        components: {
          "vue-form-generator": VueFormGenerator.component
        },

        methods: {
          prettyJSON: function (json) {
            if (json) {
              json = JSON.stringify(json, undefined, 4);
              json = json.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
              return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                var cls = "number";
                if (/^"/.test(match)) {
                  if (/:$/.test(match)) {
                    cls = "key";
                  } else {
                    cls = "string";
                  }
                } else if (/true|false/.test(match)) {
                  cls = "boolean";
                } else if (/null/.test(match)) {
                  cls = "null";
                }
                return "<span class=\"" + cls + "\">" + match + "</span>";
              });
            }
          },
          onValidated(isValid, errors) {
            console.log("Validación del Form app-datos-solicitente: ", isValid, ", Errors:", errors);
            isValid = true
            /*
            var fecha_de_vencimiento_de_la_solicitud = $("#fecha_de_vencimiento_de_la_solicitud").val();
            console.log("fecha_de_vencimiento_de_la_solicitud: ", fecha_de_vencimiento_de_la_solicitud);
            var valor_total = Number($("#valor_total").val());
            var valor_total_calculado = Number($("#valor_total_calculado").val());
            var anticipo = $("#anticipo").val();
            if(valor_total < valor_total_calculado) {
              $("#valor_total_error").html('El valor ingresado es menor al valor calculado por m<sup>2</sup>, valor m&iacute;nimo: '+valor_total_calculado);
              isValid = false;
            }
            if(anticipo > valor_total) {
              $("#valor_total_error").html('El valor del anticipo no puede ser mayor al valor total de la propiedad');
              isValid = false;
            }
            if(fecha_de_vencimiento_de_la_solicitud == '') {
              $("#fecha_de_vencimiento_de_la_solicitud_error").html('Este campo es obligatorio');
              isValid = false;
            }
            */
            if (!isValid) {
                event.preventDefault();  
            }      
          }
        },

        data: {
          model: {
            nombre_del_solicitante: null,
            celular_del_solicitante: null,
            escribe_tu_ciudad_sino_esta_en_la_lista_anterior: null,
            <?php if ($Solicitud->Tipo_de_evento->id == 2) { ?>      
            titulo_de_conferencia_publica: null,
            resumen_de_la_conferencia: null,
            <?php } ?>
            localidad_id: null,
            pais_id: null,
            institucion_id: 1
          },
          schema: {
            fields: [


              <?php if ($Solicitud->tipo_de_evento_id == 3) { ?>
              {
                type: "selectEx",
                label: "<?php echo __('Tipo de curso online') ?>",
                model: "tipo_de_curso_online_id",
                id: "tipo_de_curso_online_id",
                required: true,
                disabled: false,
                inputName: "tipo_de_curso_online_id",
                multi: "true",
                multiSelect: false,
                multiSelect: false,
                selectOptions: { 
                  liveSearch: false, 
                  size: 'auto' 
                },
                values: function() { 
                  return [ 
                    <?php 
                    echo $valoresSchemaVFG_tipos_de_curso_online;
                    ?>            
                    ] 
                },
                validator: VueFormGenerator.validators.required,
              },      
              <?php } ?>
              {
                type: "input",       
                inputType: "text",     
                model: "nombre_del_solicitante",    
                label: "<?php echo __('Nombre del Solicitante') ?>",    
                required: true,    
                inputName: "nombre_del_solicitante",
                id: "nombre_del_solicitante",
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string
              },

              {
                type: "input",       
                inputType: "text",     
                model: "celular_del_solicitante",    
                label: "<?php echo __('Celular del Solicitante (nro completo para Whatsapp ej: +5491154246578)') ?>",    
                required: true,    
                inputName: "celular_del_solicitante",
                id: "celular_del_solicitante",
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string
              },


              {
                type: "selectEx",
                label: "<?php echo __('Ciudad') ?>",
                model: "localidad_id",
                id: "localidad_id",
                required: false,
                disabled: false,
                inputName: "localidad_id",
                multi: "true",
                multiSelect: false,
                selectOptions: { 
                  liveSearch: true, 
                  size: 5 
                },
                values: function() { 
                  return [ 
                    <?php 
                    echo $valoresSchemaVFG_localidades;
                    ?>            
                    ] 
                },
                validator: VueFormGenerator.validators.required,
              },


              {
                type: "input",       
                inputType: "text",     
                model: "escribe_tu_ciudad_sino_esta_en_la_lista_anterior",    
                label: "<?php echo __('Escribe la ciudad sino esta en la lista anterior') ?>",    
                required: <?php echo $required_escribe_tu_ciudad ?>,    
                inputName: "escribe_tu_ciudad_sino_esta_en_la_lista_anterior",
                id: "escribe_tu_ciudad_sino_esta_en_la_lista_anterior",
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string,
                visible(model) {
                      var mostrar;
                      if (model.localidad_id == null) {
                          mostrar = true;
                      }
                      else {
                          mostrar = false;
                      }
                      return mostrar;
                  },
              validator: VueFormGenerator.validators.required
              },

              {
                type: "selectEx",
                label: "<?php echo __('Pais') ?>",
                model: "pais_id",
                id: "pais_id",
                required: true,
                disabled: false,
                inputName: "pais_id",
                multi: "true",
                multiSelect: false,
                selectOptions: { 
                  liveSearch: true, 
                  size: 10 
                },
                values: function() { 
                  return [ 
                    <?php 
                    echo $valoresSchemaVFG_paises;
                    ?>            
                    ] 
                },              
                validator: VueFormGenerator.validators.required,
                visible(model) {
                      var mostrar;
                      if (model.localidad_id == null) {
                          mostrar = true;
                      }
                      else {
                          mostrar = false;
                      }
                      return mostrar;
                  },
              },



              {
                type: "selectEx",
                label: "<?php echo __('Institucion') ?>",
                model: "institucion_id",
                id: "institucion_id",
                required: true,
                disabled: false,
                inputName: "institucion_id",
                multi: "true",
                multiSelect: false,
                multiSelect: false,
                selectOptions: { 
                  liveSearch: false, 
                  size: 'auto' 
                },
                values: function() { 
                  return [ 
                    <?php 
                    echo $valoresSchemaVFG_instituciones;
                    ?>            
                    ] 
                },
                validator: VueFormGenerator.validators.required,
              },

              {
                type: "submit",
                label: "",
                buttonText: "<?php echo __('Continuar') ?>",
                validateBeforeSubmit: true
              }
            ]
          },


          formOptions: {
            validateAfterLoad: false,
            validateAfterChanged: false
          }
        }
      });

    </script>
  <?php } ?>
<!-- PASO 2 - DATOS SOLICITANTE VUEJS -->

<!-- PASO 4 - DATOS SOLICITANTE VUEJS -->
  <?php if ($paso == 4) { ?>
    <script type="text/javascript">

      var VueFormGenerator = window.VueFormGenerator;

      VueFormGenerator.validators.decimal = function(value, field, model) {
        if (typeof value !== 'undefined') {
          /*
          if (typeof value == 'string') {
            valor = Number(value.replace(",", "."));
          }
          else {
            valor = value;
          }
          */
          valor = value;
          if(isNaN(valor)) {
            return ["No es un valor decimal"];
          }
        }
        return [];
      }

      var vm = new Vue({
        el: "#app-datos-campania",
        components: {
          "vue-form-generator": VueFormGenerator.component
        },

        methods: {
          prettyJSON: function (json) {
            if (json) {
              json = JSON.stringify(json, undefined, 4);
              json = json.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
              return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                var cls = "number";
                if (/^"/.test(match)) {
                  if (/:$/.test(match)) {
                    cls = "key";
                  } else {
                    cls = "string";
                  }
                } else if (/true|false/.test(match)) {
                  cls = "boolean";
                } else if (/null/.test(match)) {
                  cls = "null";
                }
                return "<span class=\"" + cls + "\">" + match + "</span>";
              });
            }
          },
          onValidated(isValid, errors) {
            console.log("Validación del Form app-datos-campania: ", isValid, ", Errors:", errors);
            //isValid = true
            /*
            var fecha_de_vencimiento_de_la_solicitud = $("#fecha_de_vencimiento_de_la_solicitud").val();
            console.log("fecha_de_vencimiento_de_la_solicitud: ", fecha_de_vencimiento_de_la_solicitud);
            var valor_total = Number($("#valor_total").val());
            var valor_total_calculado = Number($("#valor_total_calculado").val());
            var anticipo = $("#anticipo").val();
            if(valor_total < valor_total_calculado) {
              $("#valor_total_error").html('El valor ingresado es menor al valor calculado por m<sup>2</sup>, valor m&iacute;nimo: '+valor_total_calculado);
              isValid = false;
            }
            if(anticipo > valor_total) {
              $("#valor_total_error").html('El valor del anticipo no puede ser mayor al valor total de la propiedad');
              isValid = false;
            }
            if(fecha_de_vencimiento_de_la_solicitud == '') {
              $("#fecha_de_vencimiento_de_la_solicitud_error").html('Este campo es obligatorio');
              isValid = false;
            }
            */
            if (!isValid) {
                event.preventDefault();  
            }      
          }
        },

        data: {
          model: {
            rector_diocesano_o_responsable: null,
            nombre_coordinador_de_difusion: null, 
            celular_coordinador_de_difusion: null,
            moneda_id: null,
            monto_a_invertir: null,
            nombre_responsable_de_inscripciones: null,
            celular_responsable_de_inscripciones: null,
            email_correo_responsable_de_inscripciones: null,
            canal_de_recepcion_del_curso_id: null,
            sino_solicitar_responsable_de_inscripcion: 'NO',
            nombre_responsable_de_fanpage: null,
            celular_responsable_de_fanpage: null,
            sino_solicitar_responsable_de_fanpage: 'NO',
            observaciones: null
          },
          schema: {
            fields: [
            /*
              {
                type: "input",       
                inputType: "text",     
                model: "rector_diocesano_o_responsable",    
                label: "Rector diocesano o responsable",    
                required: true,    
                inputName: "rector_diocesano_o_responsable",
                id: "rector_diocesano_o_responsable",
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string
              },

              {
                type: "input",       
                inputType: "text",     
                model: "nombre_coordinador_de_difusion",      
                inputName: "nombre_coordinador_de_difusion",
                id: "nombre_coordinador_de_difusion",
                label: "Nombre del Coordinador de Difusión",  
                required: true,    
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string
              },

              {
                type: "input",       
                inputType: "text",     
                model: "celular_coordinador_de_difusion",      
                inputName: "celular_coordinador_de_difusion",
                id: "celular_coordinador_de_difusion",
                label: "Celular del Coordinador de Difusión (nro completo para Whatsapp ej: +5491154246578)",  
                required: true,    
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string
              },
              */

              <?php if ($Solicitud->tipo_de_evento_id == 3) { ?>


                {
                  type: "selectEx",
                  label: "<?php echo __('Canal de recepcion del curso') ?>",
                  model: "canal_de_recepcion_del_curso_id",
                  id: "canal_de_recepcion_del_curso_id",
                  required: false,
                  disabled: false,
                  inputName: "canal_de_recepcion_del_curso_id",
                  multi: "true",
                  multiSelect: false,
                  multiSelect: false,
                  selectOptions: { 
                    liveSearch: false, 
                    size: 'auto' 
                  },
                  values: function() { 
                    return [ 
                      <?php 
                      echo $valoresSchemaVFG_canales_de_recepcion_del_curso;
                      ?>            
                      ] 
                  },
                  validator: VueFormGenerator.validators.required,
                }, 

                <?php if ($Solicitud->institucion_id == 3) { ?>
                {
                  type: "selectEx",
                  label: "<?php echo __('Capacitacion') ?>",
                  model: "capacitacion_id",
                  id: "capacitacion_id",
                  required: true,
                  disabled: false,
                  inputName: "capacitacion_id",
                  multi: "true",
                  multiSelect: false,
                  multiSelect: false,
                  selectOptions: { 
                    liveSearch: false, 
                    size: 'auto' 
                  },
                  values: function() { 
                    return [ 
                      <?php 
                      echo $valoresSchemaVFG_capacitaciones;
                      ?>            
                      ] 
                  },
                  validator: VueFormGenerator.validators.required,
                },  
                <?php } ?>

                <?php if ($Solicitud->tipo_de_curso_online_id == 2 or $Solicitud->tipo_de_curso_online_id == 3 or $Solicitud->tipo_de_curso_online_id == 5) { ?>                
                {
                  type: "dateTimePicker",
                  label: "<?php echo __('Fecha de inicio del curso online') ?>",
                  model: "fecha_de_inicio_del_curso_online",
                  id: "fecha_de_inicio_del_curso_online",
                  inputName: "fecha_de_inicio_del_curso_online",
                  required: true,
                  placeholder: "",
                  min: moment("2019-01-01").toDate(),
                  max: moment("2050-01-01").toDate(),
                  validator: VueFormGenerator.validators.date,

                  dateTimePickerOptions: {
                      format: "DD/MM/YYYY"
                  },            

                  onChanged: function(model, newVal, oldVal, field) {
                      model.age = moment().year() - moment(newVal).year();
                  }
                },
                <?php } ?>
                <?php if ($Solicitud->tipo_de_curso_online_id == 3 or $Solicitud->tipo_de_curso_online_id == 5) { ?>               
                {
                  type: "dateTimePicker",
                  label: "<?php echo __('Hora de inicio del curso online') ?>",
                  model: "hora_de_inicio_del_curso_online",
                  id: "hora_de_inicio_del_curso_online",
                  inputName: "hora_de_inicio_del_curso_online",
                  required: true,
                  placeholder: "",
                  validator: VueFormGenerator.validators.required,
                  validator: VueFormGenerator.validators.date,

                  dateTimePickerOptions: {
                      format: "HH:mm"
                  },            

                  onChanged: function(model, newVal, oldVal, field) {
                      model.age = moment().year() - moment(newVal).year();
                  }
                },
                <?php } ?>
                <?php if ($Solicitud->tipo_de_curso_online_id == 2 or $Solicitud->tipo_de_curso_online_id == 3 or $Solicitud->tipo_de_curso_online_id == 5) { ?>     
                {
                  type: "input",       
                  inputType: "text",     
                  model: "url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual",    
                  label: "<?php echo __('Enlace de invitacion al grupo de whatsapp del aula virtual') ?>",    
                  required: false,    
                  inputName: "url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual",
                  id: "url_enlace_de_invitacion_al_grupo_de_whatsapp_del_aula_virtual",
                  validator: VueFormGenerator.validators.required,
                  validator: VueFormGenerator.validators.string
                },  
                <?php } ?>              
              <?php } ?>
              <?php if ($Solicitud->tipo_de_evento_id == 1) { ?>
                {
                  type: "switch", 
                  model: "sino_habilitar_modalidad_online",     
                  label: "<?php echo __('Habilitar opcion de curso online para los inscriptos') ?>",   
                  id: "sino_habilitar_modalidad_online_switch",  
                  inputName: "sino_habilitar_modalidad_online_switch",          
                  textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO"
                },
                {
                  type: "input", 
                  inputType: "hidden", 
                  model: "sino_habilitar_modalidad_online",
                  inputName: "sino_habilitar_modalidad_online",          
                  textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO"
                },
              <?php } ?>             
              {
                type: "selectEx",
                label: "<?php echo __('Moneda del monto a invertir') ?>",
                model: "moneda_id",
                id: "moneda_id",
                required: true,
                disabled: false,
                inputName: "moneda_id",
                multi: "true",
                multiSelect: false,
                multiSelect: false,
                selectOptions: { 
                  liveSearch: false, 
                  size: 'auto' 
                },
                values: function() { 
                  return [ 
                    <?php 
                    echo $valoresSchemaVFG_monedas;
                    ?>            
                    ] 
                },
                validator: VueFormGenerator.validators.required,
              },

              {
                type: "selectEx",
                label: "<?php echo __('Idioma del Formulario') ?>",
                model: "idioma_id",
                id: "idioma_id",
                required: true,
                disabled: false,
                inputName: "idioma_id",
                multi: "true",
                multiSelect: false,
                multiSelect: false,
                selectOptions: { 
                  liveSearch: false, 
                  size: 'auto' 
                },
                values: function() { 
                  return [ 
                    <?php 
                    echo $valoresSchemaVFG_idiomas;
                    ?>            
                    ] 
                },
                validator: VueFormGenerator.validators.required,
                onChanged(model, schema, event) {
                  //console.log('"'+$('[name="lista_de_precio_id"]').val()+'"');
                  $.ajax({
                    url: '<?php echo env('PATH_PUBLIC')?>traer_monto_por_asistente_promedio',
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      idioma_id: Number($('[name="idioma_id"]').val()),
                      solicitud_id: <?php echo $Solicitud->id ?>
                    },
                    success: function success(data, status) {     
                      var monto_a_invertir_sugerido = Number(data)
                      $( "#monto_a_invertir" ).before( '<p style="color: blue">Monto a Invertir Sugerido: $'+monto_a_invertir_sugerido+"</p>" )
                      model.monto_a_invertir = monto_a_invertir_sugerido;
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                  });


                },
              },

            {
              type: "input",       
              inputType: "number",     
              model: "monto_a_invertir",    
              label: "<?php echo __('Monto a Invertir') ?>",    
              required: true,    
              inputName: "monto_a_invertir",
              id: "monto_a_invertir",
              step: 1,
              min: 1,
              validator: VueFormGenerator.validators.required
            },


              {
                type: "switch", 
                model: "sino_solicitar_responsable_de_inscripcion",     
                label: "<?php echo __('Solicitar responsable de inscripción') ?>",   
                id: "sino_solicitar_responsable_de_inscripcion_switch",  
                inputName: "sino_solicitar_responsable_de_inscripcion_switch",          
                textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO"
              },
              {
                type: "input", 
                inputType: "hidden", 
                model: "sino_solicitar_responsable_de_inscripcion",
                inputName: "sino_solicitar_responsable_de_inscripcion",          
                textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO"
              },

              {
                type: "input",       
                inputType: "text",     
                model: "nombre_responsable_de_inscripciones",      
                inputName: "nombre_responsable_de_inscripciones",
                id: "nombre_responsable_de_inscripciones",
                label: "<?php echo __('Nombre del Responsable de Inscripción') ?>",  
                required: true,    
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string,
                visible(model) {
                      if (model.sino_solicitar_responsable_de_inscripcion == 'NO') {
                          mostrar = true
                      }
                      else {
                          mostrar = false
                      }
                      return mostrar
                  },
              },

              {
                type: "input",       
                inputType: "text",     
                model: "celular_responsable_de_inscripciones",      
                inputName: "celular_responsable_de_inscripciones",
                id: "celular_responsable_de_inscripciones",
                label: "<?php echo __('Celular del Responsable de Inscripción (nro completo para Whatsapp ej: +5491154246578)') ?>",  
                required: true,    
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string,
                visible(model) {
                      if (model.sino_solicitar_responsable_de_inscripcion == 'NO') {
                          mostrar = true
                      }
                      else {
                          mostrar = false
                      }
                      return mostrar
                  },
              },

              /*

              {
                type: "input",       
                inputType: "text",     
                model: "email_correo_responsable_de_inscripciones",      
                inputName: "email_correo_responsable_de_inscripciones",
                id: "email_correo_responsable_de_inscripciones",
                label: "<?php echo __('Correo del Responsable de Inscripción') ?>",  
                required: true,    
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.email,
                visible(model) {
                      if (model.sino_solicitar_responsable_de_inscripcion == 'NO') {
                          mostrar = true
                      }
                      else {
                          mostrar = false
                      }
                      return mostrar
                  },
              },

              
              {
                type: "switch", 
                model: "sino_solicitar_responsable_de_fanpage",     
                label: "Solicitar responsable de Fanpage",   
                id: "sino_solicitar_responsable_de_fanpage",  
                inputName: "sino_solicitar_responsable_de_fanpage",          
                textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO",
              },
              {
                type: "input", 
                inputType: "hidden", 
                model: "sino_solicitar_responsable_de_inscripcion",
                inputName: "sino_solicitar_responsable_de_inscripcion",          
                textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO"
              },

              {
                type: "input",       
                inputType: "text",     
                model: "nombre_responsable_de_fanpage",      
                inputName: "nombre_responsable_de_fanpage",
                id: "nombre_responsable_de_fanpage",
                label: "Nombre del Responsable de Fanpage",  
                required: true,    
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string,
                visible(model) {
                      if (model.sino_solicitar_responsable_de_fanpage == 'NO') {
                          mostrar = true
                      }
                      else {
                          mostrar = false
                      }
                      return mostrar
                  },
              },



              {
                type: "input",       
                inputType: "text",     
                model: "celular_responsable_de_fanpage",      
                inputName: "celular_responsable_de_fanpage",
                id: "celular_responsable_de_fanpage",
                label: "Celular del Responsable de Fanpage",  
                required: true,    
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string,
                visible(model) {
                      if (model.sino_solicitar_responsable_de_fanpage == 'NO') {
                          mostrar = true
                      }
                      else {
                          mostrar = false
                      }
                      return mostrar
                  },
              },
              */



              {         
                type: "textArea",       
                model: "observaciones",  
                id: "observaciones",  
                label: "<?php echo __('Consulta / mensaje / aclaraciones / observaciones') ?>",   
                inputName: "observaciones", 
                required: false,    
                hint: "Max 200 caracteres",
                max: 200,
                placeholder: "",
                required: false,    
                rows: 4,
                validator: VueFormGenerator.validators.required,
                validator: VueFormGenerator.validators.string
              },  


              {
                type: "submit",
                label: "",
                buttonText: "<?php echo __('Continuar') ?>",
                validateBeforeSubmit: true
              }
            ]
          },


          formOptions: {
            validateAfterLoad: false,
            validateAfterChanged: false
          }
        }
      });

    </script>
  <?php } ?>
<!-- PASO 4 - DATOS SOLICITANTE VUEJS -->


@endsection



