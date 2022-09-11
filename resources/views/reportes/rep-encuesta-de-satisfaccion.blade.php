<?php 

use \App\Http\Controllers\GenericController; 
$gCont = new GenericController();

$cant = $Encuesta_cant->cant;

?>

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



    <!-- Main content -->
    <section class="content">
      <div class="row">

	    <?php if ($cant < 10) { ?>
	      <section class="content-header">
	        <div class="row">    
	          <div class="col-xs-12">
	            <br>
	            <div class="alert alert-danger alert-dismissible">
	              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	              <h4><i class="icon fa fa-warning"></i> Las encuestas se solicitan a las personas cuando el responsable de inscripcion envian a las personas inscriptas el <strong style="color: #000">"Envio de Recordatorio Próxima clase"</strong>  </h4>  
	              <p style="font-size: 15px; margin-left: 35px; font-style: italic;">
		              <?php if ($cant > 0) { ?>
		              	Esta encuesta tiene solamente <strong><?php echo $cant ?></strong> personas que han contestado
			          <?php }
			          else { ?>
			          	Esta encuesta no tiene personas que han contestado, es probable que no se hallan enviado los <u>recordatorio de próxima clase</u>
			          <?php } ?>
		          </p>
	            </div>
	          </div>   
	        </div>
	      </section> 
  		<?php } ?>

      	
        <div class="col-lg-12">

        	<!-- ASISTIO -->
				<?php 

				$asistio_si = $Encuesta_cant->asistio_si;
				$asistio_no = $Encuesta_cant->asistio_no;
				$asistio_nc = $Encuesta_cant->asistio_nc;

				if ($cant > 0) {
					$asistio_si_porc = intval($asistio_si * 100 / $cant);
					$asistio_no_porc = intval($asistio_no * 100 / $cant);
					$asistio_nc_porc = intval($asistio_nc * 100 / $cant);
				}
				else {
					$asistio_si_porc = 0;
					$asistio_no_porc = 0;
					$asistio_nc_porc = 0;	
				}

				?>
	        	<div class="col-xs-12 col-lg-6">
					<div class="box box-default">
						<div class="box-header with-border">
						  <h3 class="box-title"><?php echo __('¿Asistió a la conferencia inicial?') ?></h3>
						</div>

						<div class="box-body">
						  <div class="row">
						    <div class="col-md-8">
						      	<div id="canvas-holder">
									<canvas id="pie-asistio" width="300" height="300"/>
								</div>
						    </div>
						    <div class="col-md-4">
						      <ul class="chart-legend clearfix">
						        <li><i class="fa fa-circle-o text-red"></i> <strong><?php echo __('SI'); ?>:</strong>  <?php echo $asistio_si ?> (<?php echo $asistio_si_porc ?>%)</li>
						        <li><i class="fa fa-circle-o text-green"></i> <strong><?php echo __('NO'); ?>:</strong>  <?php echo $asistio_no ?> (<?php echo $asistio_no_porc ?>%)</li>
						        <li><i class="fa fa-circle-o text-yellow"></i> <strong><?php echo __('No contesto'); ?>:</strong>  <?php echo $asistio_nc ?> (<?php echo $asistio_nc_porc ?>%)</li>
						      </ul>
						    </div>
						  </div>
						</div>
					</div>

					<script type="text/javascript">

							var pie_asistio = [
									{
										value: <?php echo $asistio_si ?>,
										color:"#F7464A",
										highlight: "#FF5A5E",
										label: "<?php echo __('SI'); ?>"
									},
									{
										value: <?php echo $asistio_no ?>,
										color: "#46BFBD",
										highlight: "#5AD3D1",
										label: "<?php echo __('NO'); ?>"
									},
									{
										value: <?php echo $asistio_nc ?>,
										color: "#FDB45C",
										highlight: "#FFC870",
										label: "<?php echo __('No contesto'); ?>"
									},

								];

								window.onload = function(){
								};
					</script>
				</div>
			<!-- FIN ASISTIO -->

        	<!-- PARTICIPO -->
				<?php 

				$cant = $Encuesta_cant->cant;
				$participo_si = $Encuesta_cant->participo_si;
				$participo_no = $Encuesta_cant->participo_no;
				$participo_nc = $Encuesta_cant->participo_nc;

				if ($cant > 0) {
					$participo_si_porc = intval($participo_si * 100 / $cant);
					$participo_no_porc = intval($participo_no * 100 / $cant);
					$participo_nc_porc = intval($participo_nc * 100 / $cant);
				}
				else {
					$participo_si_porc = 0;
					$participo_no_porc = 0;
					$participo_nc_porc = 0;	
				}

				?>
	        	<div class="col-xs-12 col-lg-6">
					<div class="box box-default">
						<div class="box-header with-border">
						  <h3 class="box-title"><?php echo __('¿Participó antes de alguna conferencia gnóstica?') ?></h3>
						</div>

						<div class="box-body">
						  <div class="row">
						    <div class="col-md-8">
						      	<div id="canvas-holder">
									<canvas id="pie-participo" width="300" height="300"/>
								</div>
						    </div>
						    <div class="col-md-4">
						      <ul class="chart-legend clearfix">
						        <li><i class="fa fa-circle-o text-red"></i> <strong><?php echo __('SI'); ?>:</strong>  <?php echo $participo_si ?> (<?php echo $participo_si_porc ?>%)</li>
						        <li><i class="fa fa-circle-o text-green"></i> <strong><?php echo __('NO'); ?>:</strong>  <?php echo $participo_no ?> (<?php echo $participo_no_porc ?>%)</li>
						        <li><i class="fa fa-circle-o text-yellow"></i> <strong><?php echo __('No contesto'); ?>:</strong>  <?php echo $participo_nc ?> (<?php echo $participo_nc_porc ?>%)</li>
						      </ul>
						    </div>
						  </div>
						</div>
					</div>

					<script type="text/javascript">

							var pie_participo = [
									{
										value: <?php echo $participo_si ?>,
										color:"#F7464A",
										highlight: "#FF5A5E",
										label: "<?php echo __('SI'); ?>"
									},
									{
										value: <?php echo $participo_no ?>,
										color: "#46BFBD",
										highlight: "#5AD3D1",
										label: "<?php echo __('NO'); ?>"
									},
									{
										value: <?php echo $participo_nc ?>,
										color: "#FDB45C",
										highlight: "#FFC870",
										label: "<?php echo __('No contesto'); ?>"
									},

								];

								window.onload = function(){
								};
					</script>
				</div>
			<!-- FIN PARTICIPO -->


        	<!-- EVENTO -->
	        	<div class="col-xs-12 col-lg-12">
					<div class="box box-default">
						<div class="box-header with-border">
						  <h3 class="box-title"><?php echo __('En cuanto al evento y la conferencia') ?></h3>
						</div>

						<div class="box-body">
						  <div class="row">
						    <div class="col-md-8">
								<div style="width: 50%">
									<canvas id="barra-evento" height="450" width="700"></canvas>
								</div>
						    </div>
						    <div class="col-md-4">
						      <ul class="chart-legend clearfix">
						        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('No fue lo que esperaba'); ?>:</strong>  <?php echo $Encuesta_cant->sino_evento_no_fue_lo_que_esperaba_cant ?></li>
						        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Demasiado imprecisa'); ?>:</strong>  <?php echo $Encuesta_cant->sino_evento_demasiado_imprecisa_cant ?></li>
						        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Poco convincente'); ?>:</strong>  <?php echo $Encuesta_cant->sino_evento_poco_convincente_cant ?></li>
						        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Demasiado extensa'); ?>:</strong>  <?php echo $Encuesta_cant->sino_evento_demasiado_extensa_cant ?></li>
						        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('No se cumplió el horario'); ?>:</strong>  <?php echo $Encuesta_cant->sino_evento_no_se_cumplio_el_horario_cant ?></li>
						        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Estuvo bien'); ?>:</strong>  <?php echo $Encuesta_cant->sino_evento_estuvo_bien_cant ?></li>
						        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Estuvo muy bien'); ?>:</strong>  <?php echo $Encuesta_cant->sino_evento_estuvo_muy_bien_cant ?></li>
						        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Fue clara'); ?>:</strong>  <?php echo $Encuesta_cant->sino_evento_fue_clara_cant ?></li>
						        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Interesante'); ?>:</strong>  <?php echo $Encuesta_cant->sino_evento_interesante_cant ?></li>
						        
						      </ul>
						    </div>
						  </div>
						</div>
					</div>




					<script>
					var randomScalingFactor = function(){ return Math.round(Math.random()*100)};

					var barEventoData = {
						labels : ["No fue lo que esperaba", "Demasiado imprecisa", "Poco convincente", "Demasiado extensa", "No se cumplió el horario", "Estuvo bien", "Estuvo muy bien", "Fue clara", "Interesante"],
						datasets : [
							{
								fillColor : "rgba(220,220,220,0.5)",
								strokeColor : "rgba(220,220,220,0.8)",
								data : [
									<?php echo $Encuesta_cant->sino_evento_no_fue_lo_que_esperaba_cant ?>, 
									<?php echo $Encuesta_cant->sino_evento_demasiado_imprecisa_cant ?>, 
									<?php echo $Encuesta_cant->sino_evento_poco_convincente_cant ?>, 
									<?php echo $Encuesta_cant->sino_evento_demasiado_extensa_cant ?>, 
									<?php echo $Encuesta_cant->sino_evento_no_se_cumplio_el_horario_cant ?>, 
									<?php echo $Encuesta_cant->sino_evento_estuvo_bien_cant ?>, 
									<?php echo $Encuesta_cant->sino_evento_estuvo_muy_bien_cant ?>, 
									<?php echo $Encuesta_cant->sino_evento_fue_clara_cant ?>, 
									<?php echo $Encuesta_cant->sino_evento_interesante_cant ?>
									]
							}
						]

					}

					</script>
				</div>
			<!-- FIN EVENTO -->


        	<!-- COMUNICACION -->
	        	<div class="col-xs-12 col-lg-6">
					<div class="box box-default">
						<div class="box-header with-border">
						  <h3 class="box-title"><?php echo __('Sobre la comunicación previa al evento') ?></h3>
						</div>

						<div class="box-body">
						  <div class="row">
						    <div class="col-md-9">
								<div style="width: 50%">
									<canvas id="barra-comunicacion" height="450" width="500"></canvas>
								</div>
						    </div>
						    <div class="col-md-3">
						      <ul class="chart-legend clearfix">
					        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('La comunicación fue satisfactoria'); ?>:</strong>  <?php echo $Encuesta_cant->sino_comunicacion_fue_satisfactoria_cant ?></li>
					        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Las respuestas demoraban mucho'); ?>:</strong>  <?php echo $Encuesta_cant->sino_comunicacion_las_respuestas_demoraban_mucho_cant ?></li>
					        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('El trato fue ameno y cordial'); ?>:</strong>  <?php echo $Encuesta_cant->sino_comunicacion_el_trato_fue_ameno_y_cordial_cant ?></li>
					        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Me resultó un poco insistente'); ?>:</strong>  <?php echo $Encuesta_cant->sino_comunicacion_me_resulto_un_poco_insistente_cant ?></li>
					        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Me hubiese gustado más contenidos o información'); ?>:</strong>  <?php echo $Encuesta_cant->sino_comunicacion_me_hubiese_gustado_mas_contenidos_cant ?></li>
						        
						      </ul>
						    </div>
						  </div>
						</div>
					</div>




					<script>
					var randomScalingFactor = function(){ return Math.round(Math.random()*100)};

					var barComunicacionData = {
						labels : ["La comunicación fue satisfactoria", "Las respuestas demoraban mucho", "El trato fue ameno y cordial", "Me resultó un poco insistente", "Me hubiese gustado más cont..."],
						datasets : [
							{
								fillColor : "rgba(220,220,220,0.5)",
								strokeColor : "rgba(220,220,220,0.8)",
								data : [
									<?php echo $Encuesta_cant->sino_comunicacion_fue_satisfactoria_cant ?>, 
									<?php echo $Encuesta_cant->sino_comunicacion_las_respuestas_demoraban_mucho_cant ?>, 
									<?php echo $Encuesta_cant->sino_comunicacion_el_trato_fue_ameno_y_cordial_cant ?>, 
									<?php echo $Encuesta_cant->sino_comunicacion_me_resulto_un_poco_insistente_cant ?>, 
									<?php echo $Encuesta_cant->sino_comunicacion_me_hubiese_gustado_mas_contenidos_cant ?>
									]
							}
						]

					}

					</script>
				</div>
			<!-- FIN COMUNICACION -->



        	<!-- CONTINUIDAD -->
	        	<div class="col-xs-12 col-lg-6">
					<div class="box box-default">
						<div class="box-header with-border">
						  <h3 class="box-title"><?php echo __('Sobre la continuidad y recomendación a otros') ?></h3>
						</div>

						<div class="box-body">
						  <div class="row">
						    <div class="col-md-8">
								<div style="width: 50%">
									<canvas id="barra-continuidad" height="450" width="400"></canvas>
								</div>
						    </div>
						    <div class="col-md-4">
						      <ul class="chart-legend clearfix">
					        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Estoy interesado en continuar con estos cursos'); ?>:</strong>  <?php echo $Encuesta_cant->sino_continuidad_estoy_interesado_en_continuar_cant ?></li>
					        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Recomendaría este evento a un amigo'); ?>:</strong>  <?php echo $Encuesta_cant->sino_continuidad_recomendaría_este_evento_cant ?></li>
					        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('Me resulto llamativo que las actividades sean totalmente gratuitas'); ?>:</strong>  <?php echo $Encuesta_cant->sino_continuidad_me_resulto_llamativo_que_sea_gratuito_cant ?></li>
					        <li><i class="fa fa-circle-o text-blue"></i> <strong><?php echo __('No es la propuesta de capacitación que estoy buscando en este momento'); ?>:</strong>  <?php echo $Encuesta_cant->sino_continuidad_no_es_lo_que_estoy_buscando_cant ?></li>
						      </ul>
						    </div>
						  </div>
						</div>
					</div>




					<script>
					var randomScalingFactor = function(){ return Math.round(Math.random()*100)};

					var barContinuidadData = {
						labels : ["Estoy interesado en cont...", "Recomendaría este evento...", "Me resulto llamativo...", "No es la propuesta..."],
						datasets : [
							{
								fillColor : "rgba(220,220,220,0.5)",
								strokeColor : "rgba(220,220,220,0.8)",
								data : [
									<?php echo $Encuesta_cant->sino_continuidad_estoy_interesado_en_continuar_cant ?>, 
									<?php echo $Encuesta_cant->sino_continuidad_recomendaría_este_evento_cant ?>, 
									<?php echo $Encuesta_cant->sino_continuidad_me_resulto_llamativo_que_sea_gratuito_cant ?>, 
									<?php echo $Encuesta_cant->sino_continuidad_no_es_lo_que_estoy_buscando_cant ?>
									]
							}
						]

					}

					</script>
				</div>
			<!-- FIN CONTINUIDAD -->


			<!-- SUGERENCIAS -->
				<div class="col-xs-12 col-lg-6">
					<?php foreach ($Encuestas_detalle as $Encuesta_detalle) {?>
						<div class="direct-chat-msg">
		                  <div class="direct-chat-info clearfix">
		                    <span class="direct-chat-name pull-left"><?php echo $Encuesta_detalle->id ?>, <?php echo $Encuesta_detalle->inscripcion_id ?>, <?php echo $Encuesta_detalle->nombre ?>, <?php echo $Encuesta_detalle->localidad ?>, <?php echo $Encuesta_detalle->provincia ?> (<?php echo $Encuesta_detalle->pais ?>)</span>
		                    <span class="direct-chat-timestamp pull-right"><?php echo $gCont->FormatoFechayYHora($Encuesta_detalle->created_at); ?></span>
		                  </div>
		                  <!-- /.direct-chat-info -->
		                  <i class="fa fa-user direct-chat-img" style="font-size: 35px"></i>
		                  <div class="direct-chat-text">
		                    <?php echo $Encuesta_detalle->sugerencias ?>
		                  </div>
		                  <!-- /.direct-chat-text -->
		                </div>
	             	<?php } ?>
	            </div>
			<!-- FIN SUGERENCIAS -->

		</div>
	</div>
</section>




<!-- jQuery 3 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>



<script type="text/javascript">
$( document ).ready(function() {
		var ctx_participo = document.getElementById("pie-participo").getContext("2d");
		window.myPie = new Chart(ctx_participo).Pie(pie_participo);

		var ctx_asistio = document.getElementById("pie-asistio").getContext("2d");
		window.myPie = new Chart(ctx_asistio).Pie(pie_asistio);


		var ctx_evento = document.getElementById("barra-evento").getContext("2d");
		window.myBar = new Chart(ctx_evento).Bar(barEventoData);

		var ctx_comunicacion = document.getElementById("barra-comunicacion").getContext("2d");
		window.myBar = new Chart(ctx_comunicacion).Bar(barComunicacionData);

		var ctx_continuidad = document.getElementById("barra-continuidad").getContext("2d");
		window.myBar = new Chart(ctx_continuidad).Bar(barContinuidadData);

});

</script>

<!-- Bootstrap 3.3.7 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/adminlte.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap  -->
<script src="<?php echo env('PATH_PUBLIC')?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/chart.js/Chart.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/pages/dashboard2.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/demo.js"></script>