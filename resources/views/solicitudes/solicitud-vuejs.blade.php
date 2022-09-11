@extends('layouts.backend')

@section('contenido')

<?php 
$accion = 'a';
$solicitud_id =1;
$cliente_id = 1;;
$observaciones  = 1;
$modelo_id  = 1;
$lista_de_precio_id =1;
$total_de_metros_cuadrados  =10;
$valor_total =100;
$modelos_select  = '';
if ($accion == 'a') {
  $buttonTextSubmit = 'Insertar';
}
if ($accion == 'm') {
  $buttonTextSubmit = 'Modificar';
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

<link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/css/bootstrap-select.min.css">
<script type="text/javascript" src="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/js/bootstrap-select.min.js"></script>




<br>
<div class="col-md-12">
  <div class="box box-warning box-solid">
    <div class="box-header with-border">
      <h3 class="box-title">Solicitud</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
      </div>
      <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      <div id="app">
          <div class="panel-body">
            {!! Form::open(array
              (
              'action' => 'GenericController@store', 
              'role' => 'form',
              'method' => 'POST',
              'id' => "form_gen_modelo",
              'enctype' => 'multipart/form-data',
              'class' => 'form-horizontal',
              'ref' => 'form'
              )) 
            !!}
              <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
              <input type="hidden" name="solicitud_id" value="<?php echo $solicitud_id ?>">
              <select class="selectpicker" data-live-search="true">
  <option data-tokens="ketchup mustard">Hot Dog, Fries and a Soda</option>
  <option data-tokens="mustard">Burger, Shake and a Smile</option>
  <option data-tokens="frosting">Sugar, Spice and all things nice</option>
</select>

<table id="example">
  <tr>
    <td>hola</td>
  </tr>
</table>

            {!! Form::close() !!}
          </div>

        <!--div class="panel panel-default">
          <div class="panel-heading">Model</div>
          <div class="panel-body">
            <pre v-if="model" v-html="prettyJSON(model)"></pre>
          </div>
        </div-->

      </div>


      <script type="text/javascript">
      var VueFormGenerator = window.VueFormGenerator;

      var vm = new Vue({
        el: "#app",
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
          }
        },

        data: {
          model: {
            solicitud_id: '<?php echo $solicitud_id ?>',
            cliente_id: '<?php echo $cliente_id ?>',
            observaciones: '<?php echo $observaciones ?>',
            modelo_id: '<?php echo $modelo_id ?>',
            lista_de_precio_id: '<?php echo $lista_de_precio_id ?>',
            total_de_metros_cuadrados: '<?php echo $total_de_metros_cuadrados ?>',
            valor_total: '<?php echo $valor_total ?>'
          },
          schema: {
            fields: [

                {         
                type: "selectEx",      
                model: "modelo_id",    
                label: "Modelo",    
                required: true,    
                inputName: "modelo_id",    
                multi: "true",    
                multiSelect: false,
                selectOptions: { liveSearch: true, size: 'auto' }, 
                values: function() { return [ <?php echo $modelos_select; ?> ] },
                },
                {         
                type: "input",       
                inputType: "text",     
                model: "observaciones",    
                label: "Observaciones",    
                required: false,    
                inputName: "observaciones",
                id: "observaciones",
                },
                {         
                type: "input",       
                inputType: "text",     
                model: "total_de_metros_cuadrados",    
                label: "total_de_metros_cuadrados",    
                required: false,    
                inputName: "total_de_metros_cuadrados",
                },
            
              {
                type: "submit",
                label: "",
                buttonText: "<?php echo $buttonTextSubmit ?>",
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

$( document ).ready(function() {
$( "#observaciones" ).replaceWith( "<h2>New heading</h2>" );



var table = $('#example').DataTable();
 
table.row.add( {
        "name":       "Tiger Nixon",
        "position":   "System Architect",
        "salary":     "$3,120",
        "start_date": "2011/04/25",
        "office":     "Edinburgh",
        "extn":       "5421"
    } ).draw();

});

      </script>





      
    </div>
    <!-- /.box-body -->
  </div>

</div>
@endsection



