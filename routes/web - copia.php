<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::group(['middleware' => 'setear_idioma'], function () {	
	// RUTAS GENERICAS
	Route::post('/crearlista', 'GenericController@crearLista');
	Route::post('/crearabm', 'GenericController@crearABM');
	//Route::get('/abm/{accion}/{gen_modelo}/{id}', 'GenericController@show');
	//Route::get('/crearlista/{gen_modelo}', 'GenericController@crearLista');
	Route::post('/enviarabm/{gen_modelo}', 'GenericController@crearABM');
	Route::post('/store', 'GenericController@store');
	// FIN RUTAS GENERICAS
});
	

Route::group(['middleware' => 'auth'], function () {

	Route::get('/', 'HomeController@index');
	Route::get('/micuenta', 'HomeController@miCuenta');


	//ASISTENTE CREACION DE SOLICITUD
	Route::get('/Solicitudes/crear', function () {
	    return view('solicitudes/solicitud-asistente');
	});	
	Route::post('/Solicitudes/crear/listar-tipos-de-eventos-para-seleccion', 'SolicitudController@listarTiposDeEventosParaSeleccion');
	Route::get('/Solicitudes/crear/elegir-tipo-de-evento/{cliente_id}', 'SolicitudController@crearSolicitudElegirTipoDeEvento');
	Route::post('/Solicitudes/crear/fechas-de-evento/{solicitud_id}', 'SolicitudController@listarFechasDeEventos');
	Route::post('/Solicitudes/crear/conferencia-publica/{solicitud_id}', 'SolicitudController@listarConferenciasPublicas');
	Route::post('/Solicitudes/crear/datos-del-evento/{solicitud_id}', 'SolicitudController@GuardarDatosDelSolicitante');
	Route::get('/Solicitudes/crear/datos-del-evento/{solicitud_id}', 'SolicitudController@listarFechasDeEventos2');
	Route::get('/Solicitudes/crear/datos-de-la-campania/{solicitud_id}', 'SolicitudController@datosDeLaCampania');
	Route::post('/Solicitudes/crear/resumen-para-envio', 'SolicitudController@GuardarDatosCampania');
	Route::get('/Solicitudes/crear/enviar-solicitud/{solicitud_id}', 'SolicitudController@enviarSolicitud');


	//VIEJAS
	Route::get('/composicion/{modelo_id}', 'ModeloController@composicionDeModelo');
	Route::post('/crearlistamodelo', 'ModeloController@crearListaModelo');


	//SOLICITUDES	
	Route::get('/Solicitudes/list/{estado}', 'SolicitudController@index');
	Route::get('/Solicitudes/solicitud/ver/{solicitud_id}', 'SolicitudController@editarSolicitud');
	Route::get('/Solicitudes/solicitud/cambiar-cliente/{solicitud_id}/{cliente_id}', 'SolicitudController@cambiarClienteSolicitud');
	Route::get('/Solicitudes/solicitud/cambiar-modelo/{solicitud_id}/{modelo_id}', 'SolicitudController@cambiarModeloSolicitud');
	Route::post('/Solicitudes/solicitud/modificar-forma-de-pago/{solicitud_id}', 'SolicitudController@cambiarFormaDePago');
	Route::post('/Solicitudes/solicitud/aprobacion-administracion/{solicitud_id}', 'SolicitudController@aprobacionAdministracion');
	Route::post('/Solicitudes/solicitud/aprobacion-garantes/{solicitud_id}', 'SolicitudController@aprobacionGarantes');
	Route::post('/Solicitudes/solicitud/aprobacion-finalizada/{solicitud_id}', 'SolicitudController@aprobacionFinalizada');
	Route::post('/Solicitudes/solicitud/aprobacion-cancelada/{solicitud_id}', 'SolicitudController@aprobacionCancelada');
	Route::post('/Solicitudes/solicitud/aprobacion-published/{campania_mautic_id}/{sino_is_published}', 'MauticController@aprobacionPublished');

	Route::post('/Solicitudes/solicitud/guardar-obs-adm/{solicitud_id}', 'SolicitudController@guardarObsAdm');
	Route::post('/Solicitudes/solicitud/guardar-obs-gar/{solicitud_id}', 'SolicitudController@guardarObsGar');
	Route::post('/Solicitudes/solicitud/guardar-obs-sol-rev/{solicitud_id}', 'SolicitudController@guardarObsSolRev');
	Route::post('/Solicitudes/solicitud/guardar-obs-fin/{solicitud_id}', 'SolicitudController@guardarObsFin');
	Route::post('/Solicitudes/solicitud/guardar-obs-canc/{solicitud_id}', 'SolicitudController@guardarObsCanc');
	Route::post('/traer_monto_por_asistente_promedio', 'SolicitudController@traerMontoPorAsistentePromedio');
	Route::get('/Solicitudes/llenar', 'SolicitudController@llenar');
	Route::post('/f/i/setear-sino-solicitud/{codigo}/{solicitud_id}', 'FormController@setearSinoSolicitud');
	Route::post('/pagar-paypal', 'SolicitudController@PagarPaypal');
	Route::get('/pagar-paypal/ReturnUrlAuthorized', 'SolicitudController@returnUrlAuthorized');
	Route::get('/pagar-paypal/recuperar-operacion-pagada/{solicitud_id}', 'SolicitudController@recuperarOperacionPagada');
	Route::get('/resetear-campania/{solicitud_id}/{password_reset}', 'SolicitudController@resetearCampania');
	Route::get('/completar_url_redirect', 'HomeController@completarUrlRedirect');
	Route::get('/solicitudes-valores', 'ReportesController@solicitudesValores');
	Route::get('/Solicitudes/online/{estado}', 'SolicitudController@SolicitudesOnline');
	Route::get('/Solicitudes/recoleccion/{estado}', 'SolicitudController@SolicitudesRecoleccionDatos');



	//DASHBOARDS
	Route::post('/listar-solicitudes-estadisticas', 'ReportesController@listarSolicitudesEstadisticas');
	Route::post('/listar-encuestas', 'EncuestaController@reporteEncuestaSatisfaccionSearch');
	Route::get('/dashboard-oe', 'HomeController@dashboard');
	Route::post('/traer-dashboard-oe', 'HomeController@dashboardOE');
	Route::get('/ranking-m', 'HomeController@rankingMundial');
	Route::post('/traer-ranking-m', 'HomeController@traerRankingMundial');
	Route::get('/lista-de-usuarios', 'ReportesController@listaDeUsuarios');
	Route::get('/ranking-m-jhon', 'HomeController@rankingMundialJhon');	
	Route::post('/traer-ranking-m-jhon', 'HomeController@traerRankingMundialJhon');
	Route::post('/listar-inscripciones', 'FormController@listarInscripciones');
	Route::post('/listar-alumnos-avanzados', 'FormController@listarAlumnosAvanzandos');


	




	// RUTAS GENERICAS
	Route::get('/list/{gen_modelo}/{gen_opcion}', 'GenericController@index');

	// ENLACES A MATERIAL
	Route::get('/enlaces-a-material', function () {
	    return view('enlaces-a-material');
	});
	Route::get('/mapa-no-google', function () {
	    return view('reportes/mapa-no-google');
	});
	Route::get('/search-solicitudes-estadisticas', function () {
	    return view('reportes/search-solicitudes-estadisticas');
	});
	Route::get('/search-encuestas-de-satisfaccion', function () {
	    return view('reportes/search-encuestas-de-satisfaccion');
	});
	Route::get('/buscar-inscriptos', function () {
	    return view('reportes/search-inscripciones');
	});
	Route::get('/buscar-alumnos-avanzados', function () {
	    return view('reportes/search-buscar-alumnos-avanzandos');
	});

	

	Route::get('/test/{inscripcion_id}', 'HomeController@test');

	Route::get('/c/{fecha_de_evento_id}/{hash}', 'CursoController@cursoShow');
	Route::post('c/setear-sino-es-instructor', 'CursoController@setearSiEsInstructor');


	Route::get('prog', 'MauticController@programarCampaniaMautic');


});

Route::get('/mapa-de-cursos-y-conferencias/{dias}', 'ExtController@mapaDeCursosYConferencias');
Route::get('/mapa-de-cursos-y-conferencias/{dias}/{lang}', 'ExtController@mapaDeCursosYConferencias');
Route::get('/mapa-de-cursos-y-conferencias-por-region/{dias}/{tipo}/{id}', 'ExtController@mapaDeCursosYConferenciasPorRegion');
Route::get('/mapa-de-cursos-y-conferencias-por-region/{dias}/{tipo}/{id}/{lang}', 'ExtController@mapaDeCursosYConferenciasPorRegion');
Route::get('/mapa-de-sedes-argentina/', 'ExtController@mapaDeSedesArgentina');
Route::get('/mapa-de-sedes-argentina-geocode/', 'ExtController@mapaDeSedesArgentinaGeocode');
Route::get('/mapa-de-sedes/{pais_id}', 'ExtController@mapaDeSedes');
Route::get('/mapa-de-inscriptos', 'ExtController@mapaDeInscriptos');
Route::get('/mapa-de-cursos', 'ExtController@mapaDeCursos');
Route::post('/guardar-lat-y-long', 'ExtController@GuardarLatYLong');
Route::post('/gs/save-test', 'ExtController@saveTest');
Route::post('/wabot/save', 'ExtController@saveWabot');



Route::get('inscribirme', function () {
    return redirect('f/815/mooc-curso-de-auto-conocimiento');
});
Route::get('chile', function () {
    return redirect('f/4884/Chile-01-70');
});


Route::get('/prueba', function () {
    return view('prueba');
});	

Route::get('ZoomTaller', function () {
    return redirect('https://us02web.zoom.us/j/84294232419?pwd=czhzN2ExOHBCczlWZHI1U3hmMnN2dz09');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/delcache', function () {
    $exitCode = Artisan::call('cache:clear');
    echo 'Cache Borrada';
});


//FORMS
Route::group(['middleware' => 'sesion'], function () {	
	Route::get('/f/{solicitud_id}/{hash}', 'FormController@formShow');
	Route::get('/fc/{solicitud_id}/{hash}/{campania_id}', 'FormController@formShow');
	Route::get('/fapp/{solicitud_id}/{hash}/{campania_id}/{app_usuario_id}', 'FormController@formShow');
	Route::post('/f/registrar-inscripcion', 'FormController@RegistrarInscripcion')->middleware('cors');
	Route::get('/e/{inscripcion_id}/{hash}', 'EncuestaController@encShow');
	Route::post('/f/registrar-encuesta', 'EncuestaController@RegistrarEncuesta');
});
Route::get('/f/i/{solicitud_id}/{hash}', 'FormController@listInscriptos');
Route::get('/f/il/{solicitud_id}/{hash}/{limite}', 'FormController@listInscriptoslimit');
Route::get('/f/ilimite/{solicitud_id}/{hash}/{limite}', 'FormController@listInscriptoslimit');
Route::get('/f/ipaginar/{solicitud_id}/{hash}/{pagina}/{historico}', 'FormController@listInscriptosPaginar');
Route::get('/f/ifiltro/{filtro}/{solicitud_id}/{hash}', 'FormController@listInscriptosFiltro');
Route::get('/f/igrupo/{solicitud_id}/{hash}/{grupo_id}', 'FormController@listInscriptosGrupo');
Route::get('/f/icampania/{filtro}/{solicitud_id}/{campania_id}/{hash}', 'FormController@listInscriptosCampania');
Route::get('/f/ivarias/{solicitudes}/{hash}', 'FormController@listInscriptosVariasSolicitudes');
Route::get('/f/a/{solicitud_id}/{hash}', 'FormController@planillaAsistencia');
Route::get('/f/agrupo/{solicitud_id}/{hash}/{grupo_id}', 'FormController@planillaAsistenciaGrupo');
Route::get('/f/x/{solicitud_id}/{hash}/{fecha_de_evento_id}', 'FormController@listaInscripcionAExcel');
Route::post('/f/i/setear-sino/{codigo}/{inscripcion_id}/{solicitud_id}', 'FormController@setearSino');
Route::post('/f/i/setear-asistencia/{leccion_id}/{inscripcion_id}', 'FormController@setearAsistencia');
Route::post('/f/i/baja-de-alumno/{inscripcion_id}/{causa_de_baja_id}', 'FormController@bajaDeAlumno');
Route::post('/f/i/guardar-cel/{inscripcion_id}/{celular}', 'FormController@guardarCelular');
Route::post('/f/i/guardar-obs/{inscripcion_id}/{celular}', 'FormController@guardarObs');
Route::post('/f/i/guardar-grupo/{inscripcion_id}/{grupo_id}', 'FormController@guardarGrupo');
Route::post('/f/i/guardar-datos-grupo', 'FormController@guardarDatosGrupo');
Route::post('/f/i/registrar-envio/{codigo_de_envio_id}/{inscripcion_id}/{medio_de_envio_id}/{solicitud_id}', 'FormController@registrarEnvio');
Route::get('/f/v/{inscripcion_id}/{hash}', 'FormController@printVoucher');
Route::get('/f/certificado/{inscripcion_id}/{hash}', 'ExtController@printCertificado');
Route::get('/f/certificado-pdf/{inscripcion_id}/{hash}', 'ExtController@printCertificadoPDF');
Route::get('/f/contactDown/{solicitud_id}/{modo}/{id}/{tipo}/{cant_x_pagina}/{hash}', 'FormController@contactDown');
Route::get('/mautic', 'FormController@mautic');
Route::get('/f/registrar-asistencia/{inscripcion_id}/{hash}', 'FormController@registrarAsistencia');
Route::get('/f/detalle-de-certificado/{inscripcion_id}/{hash}', 'ExtController@detalleDeCertificado');
Route::post('/f/inscripcion/enviar-email/{inscripcion_id}/{codigo}/{mensaje}', 'FormController@enviarNotificacionInscripcion');
Route::post('/modificar-fecha-de-evento', 'FormController@cambiarDeHorarioAInscripto');
Route::post('/modificar-de-solicitud', 'FormController@cambiarDeSolicitudAInscripto');
Route::post('/f/ibuscar/{solicitudes}/{hash}', 'FormController@listInscriptosBusqueda');
Route::get('/f/ibuscar/{solicitudes}/{hash}', 'FormController@listInscriptosBusqueda');
Route::get('/f/iinscripto/{solicitud_id}/{inscripcion_id}/{hash}', 'FormController@verInscriptoEnPlanilla');
Route::post('/buscar-inscriptos', 'FormController@buscarInscriptos');
Route::post('/buscar-alumnos-avanzandos', 'FormController@buscarAlumnosAvanzandos');
Route::get('/f/h/{solicitud_id}/{hash}', 'FormController@listInscriptosHistoricos');
Route::get('/f/raw/{solicitud_id}/{hash}', 'ExtController@paginaEnlacesAsistenciaWabot');
Route::get('/f/rawx/{solicitud_id}/{hash}', 'ExtController@paginaEnlacesAsistenciaExtraWabot');


Route::get('/curso-de-auto-conocimiento-on-line/inscripcion/{pais}', 'FormController@inscripcionCursoOnLine');

Route::get('/le/{lista_de_envio_id}/{hash}', 'FormController@listEnvios');
Route::post('/le/registrar-envio/{codigo_de_envio_id}/{inscripcion_id}/{medio_de_envio_id}', 'ListasController@registrarEnvio');
Route::post('/le/setear-sino/{codigo}/{contacto_id}/{tipo_de_lista_de_envio_id}', 'ListasController@setearSino');
Route::get('/crearlistas/{cantidad}/{tipo_de_lista_de_envio_id}', 'ListasController@crearListas');
Route::get('/flyer/{solicitud_id}/{template}', 'ExtController@crearFlyer');
Route::get('/f/auto/confirmar-asistencia/{inscripcion_id}/{hash}', 'FormController@confirmarAsistencia');
Route::get('/fin-de-leccion/{leccion_id}/{solicitud_id}/{hash}', 'FormController@notificarFinDeLeccion');
Route::post('/registrar-fin-de-leccion', 'FormController@registrarFinDeLeccion');
Route::get('/data/paises-cod-tel', 'FormController@paisesCodTelJson');
Route::get('/traer-lecciones-vistas/{inscripcion_id}/{hash}', 'FormController@traerLeccionesVistas');
Route::get('/traer-tp-realizados/{inscripcion_id}/{hash}', 'FormController@traerTPRealizados');
Route::get('/forzar-promocion/{inscripcion_id}', 'FormController@forzarPromocion');
Route::get('/pdf/', 'ExtController@pdf');



//REPORTES
Route::get('/reportes/encuesta-satisfaccion/{tipo}/{id}', 'EncuestaController@reporteEncuestaSatisfaccion');
Route::get('/cursos-arg', 'ExtController@listCursos');


//APP
/*
Route::get('/APP/DASHBOARD/{app_id}/{nivel_de_acceso}/{token}', 'AppController@dashboard');
Route::get('/APP/CATEGORIAS/{app_id}/{nivel_de_acceso}/{token}', 'AppController@categorias');
Route::get('/APP/POSTEOS/{app_id}/{nivel_de_acceso}/{app_categoria_id}/{token}', 'AppController@posteos');
*/

//MAXI
//Route::get('SITE/GET-INSCRIPTOS/{cant}', 'AppController@getInscriptos');

//FACEAPP
Route::get('webhook', 'FacebookController@webhook');
Route::get('platform', 'FacebookController@platform');



//TEMP
Route::get('multipromo', 'FormController@multiPromo');


// BOT TELEGRAM
Route::get('/telegram/bot01', 'TelegramController@initBot01');
Route::get('/telegram/bot02', 'TelegramController@initBot02');
Route::post('/telegram/bot02', 'TelegramController@initBot02');


// Comandos Artisan
Route::get('/queue_work', function () {
    $exitCode = Artisan::call('queue:work');
    echo 'Cola procesada una vez';
});

// Comandos Artisan
Route::get('/queue_work_once', function () {
    $exitCode = Artisan::call('queue:work', [
        '--once'
    ]);
    echo 'Cola procesada una vez';
});

// Comandos Artisan
Route::get('/queue_restart', function () {
    $exitCode = Artisan::call('queue:restart');
    echo 'Cola reiniciada';
});

