const config = {
  locale: 'es', 
};


Vue.use(VeeValidate, config);

var app = new Vue({
  el: '#app',
  data: {
      mostrar_mensaje_error: false,
      mensaje_error: '',
      guardar: false,

  <?php 
  foreach ($gen_campos as $campo) {
    if (!in_array($campo['nombre'], $gen_campos_a_ocultar)) { 

      // ASIGNO EL VALOR DEL CAMPO SI ES ALTA O BAJA
      if ($gen_accion == 'm' or $gen_accion == 'b') {
        echo $campo['nombre'].": '".$gen_fila[$campo['nombre']]."',";
      }
      else {
        echo $campo['nombre'].": '',";
      }
  ?>

  <?php
    }
  }
  ?>
  },
  methods: {
    validar_errores: function(){
      // VALIDO SI HAY ERRORES
      this.$validator.validateAll().then(() => {
          if (this.errors.any()) {
            // SI HAY ERRORES
            this.guardar = false
            this.mostrar_mensaje_error = true
            this.mensaje_error = 'Hay campos que corrergir'
          }
          else {
            // SI NO HAY ERRORES
            this.mostrar_mensaje_error = false
            this.guardar = true
            this.$refs.formABM.submit()
          }
      }).catch(() => {
          this.title = this.errors;
      });
    }
  },  

})