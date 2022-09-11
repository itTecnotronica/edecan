

<script type="">
function Fecha_de_evento() {
	$('#modal-titulo-Fecha_de_evento').html('Inserir os dados do início do curso')
	$("#app-func-abm").before( '<p class="seccion-form ">Data, Horário e Local onde se iniciará este curso</p>' )
	$("#direccion_de_inicio").before( '<p> <span class="aclaracion_campo_form">Indique a rua, número e algum ponto de referência caso necessário (não indique o nome da cidade, nem o nome do lumisial, ex.:</span> <span class="aclaracion_campo_form_ejemplo">Av. Mitre 855, Biblioteca Alberdi</span> </p>')
	$("#url_enlace_a_google_maps_inicio").before( '<p> <span class="aclaracion_campo_form">Indique o link do google maps do local de início do curso. Ex.:</span> <span class="aclaracion_campo_form_ejemplo"><a href="https://goo.gl/maps/yqM3pMey16z">https://goo.gl/maps/yqM3pMey16z</a></span> </p>' )
	$("#cupo_maximo_disponible_del_salon").after( '<br><p class="seccion-form ">Indique os dias e horários do curso</p><p>Se o curso é Segunda e Sexta às 20h, indique em "Horário Segunda-feira" o valor "20:00" e em "Hora Sexta Feira" o valor "20:00"</p>' )
	$("#direccion_del_curso").before( '<p> <span class="aclaracion_campo_form">Se o curso será no mesmo local onde iniciou, não complete este campo, se não, indique aqui a Indique a rua, número e algum ponto de referência caso necessário (não indique o nome da cidade, nem o nome do lumisial, ex.:</span> <span class="aclaracion_campo_form_ejemplo">Lavalle 382</span> </p>')
	$("#url_enlace_a_google_maps_curso").before( '<p> <span class="aclaracion_campo_form">Se o curso será no mesmo local onde iniciou, não complete este campo, se não, indique o link do google maps do local do curso. Ex.:</span> <span class="aclaracion_campo_form_ejemplo"><a href="https://goo.gl/maps/Ad8RSPzichC2">https://goo.gl/maps/Ad8RSPzichC2</a></span> </p>' )	
}

	function Fecha_de_evento-es() {
	$('#modal-titulo-Fecha_de_evento').html('Insertar los datos del inicio de cursos')
	$("#app-func-abm").before( '<p class="seccion-form ">Fecha, Hora y Lugar donde se iniciara este curso</p>' )
	$("#direccion_de_inicio").before( '<p> <span class="aclaracion_campo_form">Indique la calle, numeración y alguna referencia de ser necesario (no indique el nombre de la ciudad, ni el nombre del Lumisial), por ejemplo:</span> <span class="aclaracion_campo_form_ejemplo">Av. Mitre 855, Biblioteca Alberdi</span> </p>')
	$("#url_enlace_a_google_maps_inicio").before( '<p> <span class="aclaracion_campo_form">Indique el enlace a google maps donde esté ubicado el lugar donde iniciarán los cursos, por ejemplo:</span> <span class="aclaracion_campo_form_ejemplo"><a href="https://goo.gl/maps/yqM3pMey16z">https://goo.gl/maps/yqM3pMey16z</a></span> </p>' )
	$("#cupo_maximo_disponible_del_salon").after( '<br><p class="seccion-form ">Indique que dias y horarios se dictará este curso</p><p>Si el curso es Lunes y Miércoles a las 20hs, indique en "Hora Lunes" el valor "20:00" y en "Hora Miércoles" el valor "20:00" </p>' )
	$("#direccion_del_curso").before( '<p> <span class="aclaracion_campo_form">Si el curso se dicta en el mismo lugar donde inicia, no complete este campo, sino indique aquí la calle, numeración y alguna referencia donde se dictará el curso (no indique el nombre de la ciudad, ni el nombre del Lumisial), por ejemplo:</span> <span class="aclaracion_campo_form_ejemplo">Lavalle 382</span> </p>')
	$("#url_enlace_a_google_maps_curso").before( '<p> <span class="aclaracion_campo_form">Si el curso se dicta en el mismo lugar donde inicia, no complete este campo, sino indique aquí el enlace a google maps donde esté ubicado el lugar donde se dictará el curso, por ejemplo:</span> <span class="aclaracion_campo_form_ejemplo"><a href="https://goo.gl/maps/Ad8RSPzichC2">https://goo.gl/maps/Ad8RSPzichC2</a></span> </p>' )	
}
</script>