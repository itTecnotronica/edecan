<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('test',function(){
    return response([1,2,3,4],200);   
});

//APP
Route::get('LOGIN/{app_id}/{user}/{pass}/{token}', 'AppController@login');
Route::get('DASHBOARD/{app_id}/{nivel_de_acceso}/{token}', 'AppController@dashboard');
Route::get('CATEGORIAS/{app_id}/{nivel_de_acceso}/{token}', 'AppController@categorias');
Route::get('POSTEOS/{app_id}/{nivel_de_acceso}/{app_categoria_id}/{token}', 'AppController@posteos');
//MD
Route::get('POSTEOSNIVEL/{app_id}/{nivel_de_acceso}/{token}', 'AppController@posteosNivelAcceso');
Route::get('NIVELES/{app_id}/{nivel_de_acceso}/{token}', 'AppController@niveles');
//MAXI
Route::get('SITE/GET-INSCRIPTOS/{cant}', 'AppController@getInscriptos');


Route::post('login', 'API\UserController@login');
//Route::post('register', 'API\UserController@register');

Route::group(['middleware' => 'auth:api'], function(){
	Route::post('details', 'API\UserController@details');
	Route::post('paises', 'API\SeteosController@paises');
});
//conjunto del material segun la leccion
Route::get('AULA/LECCIONES/{nroLeccion}', 'AppController@getLecciones');
//la ulima leccion de un usuario
Route::get('AULA/ULTIMALECCION/{usuario}/{solicitud}', 'AppController@getUltimaLecciones');
//actualizo la leccion
Route::get('AULA/ACTUALIZOLECCION/{inscripcion_id}/{evaluacion}', 'AppController@actualizoLeccion');
//Solicitudes segun usuario
Route::get('AULA/SOLICITUD/{usuario}', 'AppController@getSolicitudes');
//
Route::get('INSERTLOG/{modulo}/{texto}/{pais}/{idioma}/{telefono}/{nombre}/{idmovil}/{onesignal}/{coordenada}', 'AppController@insertLog');
Route::get('LOG/{modulo}/{texto}', 'AppController@log');
//Solicitudes segun usuario
Route::get('AULA/CODIGOALUMNO/{codigo}', 'AppController@getCodigoApp');
//S 
Route::get('AULA/APPUSUARIOID/{codigo_onesignal}', 'AppController@getAppUsuarioId');
//S 
Route::get('APP/GETCOORDENADA/{codigo_pais}/{latitud}/{longitud}', 'AppController@getCoordenada');
//
Route::get('APP/GETLISTAA/{pais_id}/{token}', 'AppController@getListAA');
Route::get('APP/GETLISTINSCRIPTOS/{solicitud_id}/{token}', 'AppController@getListInscriptos');
Route::get('APP/GETLISTINSCRIPTOSBR/{solicitud_id}/{token}', 'AppController@getListInscriptosBR');
Route::get('APP/ACTUALIZAR-ESTADO-ALUMNO/{inscripto_id}/{instancia_de_seguimiento_id}/{observaciones}/{user_id}/{token}', 'AppController@actualizarEstadoAlumno');
//GAPP Usuario
Route::get('GAPP/GETUSUARIO/{pais_id}/{documento}/{token}', 'AppController@getUsuario');
Route::get('GAPP/SAVEUSUARIO/{id}/{nombre}/{apellido}/{tb_tipo_de_documento_id}/{numero_de_documento}/{nacionalidad}/{sexo}/{fecha_de_nacimiento}/{domicilio}/{localidad}/{tc_celular}/{mail_correo_electronico}/{token}', 'AppController@saveUsuario');
//GAPP Inscripcion
Route::get('GAPP/GETINSCRIPTOSALEVENTO/{pais_id}/{id_evento}/{token}', 'AppController@getInscriptosAlEvento'); 
Route::get('GAPP/GETINSCRIPTOALEVENTO/{pais_id}/{id_evento}/{persona_id}/{token}', 'AppController@getInscriptoAlEvento');
Route::get('GAPP/GETINSCRIPCION/{pais_id}/{id}/{token}', 'AppController@getInscripcion');
Route::get('GAPP/SAVEINSCRIPCION/{id}/{tb_evento_id}/{tb_persona_id}/{notas}/{token}', 'AppController@saveInscripcion');
Route::get('GAPP/DELETEINSCRIPCION/{id}/{token}', 'AppController@deleteInscripcion');
//GAPP Debitos
Route::get('GAPP/GETDEBITO/{pais_id}/{id}/{token}', 'AppController@getDebito');
Route::get('GAPP/GETDEBITOS/{pais_id}/{tb_tipo_de_debito_id}/{token}', 'AppController@getDebitos');
Route::get('GAPP/SAVEDEBITO/{id}/{tb_tarjeta_id}/{tb_tipo_de_tarjeta_id}/{tb_persona_id}/{numero_de_tarjeta}/{monto}/{observaciones}/{fechaVto}/{tb_tipo_de_debito_id}/{token}', 'AppController@saveDebito');
Route::get('GAPP/DELETEDEBITO/{id}/{token}', 'AppController@deleteDebito');
Route::get('GAPP/UPDATEDEBITOESTADO/{id}/{estado}/{token}', 'AppController@updatedebitoestado');
//GAPP Carnet
Route::get('GAPP/GETCARNET/{pais_id}/{busqueda}/{opcion}/{token}', 'AppController@getCarnet');
//getCarnetById
Route::get('GAPP/GETCARNETBYID/{pais_id}/{id}/{token}', 'AppController@getCarnetById');
Route::get('GAPP/DELETECARNET/{id}/{token}', 'AppController@deleteCarnet');
Route::get('GAPP/SAVECARNET/{id}/{tb_tipo_de_carnet_id}/{tb_persona_id}/{token}', 'AppController@saveCarnet');
Route::get('GAPP/SAVECARNETPAGADO/{id}/{token}', 'AppController@saveCarnetEstadoPagado');
Route::get('GAPP/SAVECARNETCONFECCION/{id}/{token}', 'AppController@saveCarnetEstadoConfeccion');
Route::get('GAPP/SAVECARNETENVIO/{id}/{token}', 'AppController@saveCarnetEstadoEnviado');
Route::get('GAPP/SAVECARNETVISTO/{id}/{token}', 'AppController@saveCarnetEstadoVisto');
Route::get('GAPP/SAVECARNETAUTORIZADO/{id}/{token}', 'AppController@saveCarnetEstadoAutorizado');
Route::get('GAPP/SAVECARNETLIMPIAR/{id}/{token}', 'AppController@saveCarnetEstadoLimpiar');
Route::post('GAPP/UPLOADPAGOCARNET', 'AppController@uploadPagoCarnet'); 
//GAPP Tablas
Route::get('GAPP/GETSEDES/{pais_id}/{token}', 'AppController@getSedes');
Route::get('GAPP/GETCENTROS/{pais_id}/{token}', 'AppController@getCentros');
Route::get('GAPP/GETEVENTOS/{pais_id}/{token}', 'AppController@getEventos');
Route::get('GAPP/GETTIPOCARNET/{pais_id}/{token}', 'AppController@getTipoCarnet');
Route::get('GAPP/GETTARJETA/{pais_id}/{token}', 'AppController@getTarjeta');
Route::get('GAPP/GETTIPOTARJETA/{pais_id}/{token}', 'AppController@getTipoTarjeta');
//GETMATERIAL
Route::get('GAPP/GETMATERIALSEARCH/{idioma_id}/{token}/{value}/{publico}', 'AppController@getMaterialesSearch');
Route::get('GAPP/GETALLMATERIAL/{idioma_id}/{token}/{tipo}/{cant}/{autor}/{publico}', 'AppController@getAllMateriales');
Route::get('GAPP/GETALLMATERIALRANDOM/{idioma_id}/{token}/{cant}/{publico}', 'AppController@getAllMaterialesRandom');
//MIEMBROS
Route::get('GAPP/GETMIEMBRO/{token}/{documento}', 'AppController@getMiembro');
Route::post('GAPP/UPDATEMIEMBROFOTO', 'AppController@updateMiembroFoto'); 
Route::post('GAPP/UPDATEMIEMBROFIRMA', 'AppController@updateMiembroFirma'); 
Route::get('GAPP/GETMIEMBROS/{busqueda}/{tipoMiembro}/{token}', 'AppController@getMiembros');
Route::get('GAPP/GETMIEMBROID/{id_usuario}/{token}', 'AppController@getMiembroId');
Route::get('GAPP/SAVEMIEMBROTELEFONO/{id_usuario}/{telefono}/{token}', 'AppController@updateMiembroTelefono');
Route::get('GAPP/SAVEMIEMBRO/{id_usuario}/{campo}/{valor}/{token}', 'AppController@updateMiembro');
//MIEMBROS OBSERVACION
Route::get('GAPP/SAVEMIEMBROOBSERVACION/{id_usuario}/{notas}/{opcion}/{token}', 'AppController@saveMiembroObservacion');
Route::get('GAPP/DELETEMIEMBROOBSERVACION/{id}/{token}', 'AppController@deleteMiembroObservacion');
Route::get('GAPP/GETMIEMBROSOBSERVACIONES/{token}', 'AppController@getMiembrosObservaciones');
Route::get('GAPP/GETMIEMBROOBSERVACIONES/{id_usuario}/{token}', 'AppController@getMiembroObservaciones');
//
Route::get('APP/dialog', 'ExtController@dialog');
//
Route::get('MM/paises/{idioma_id}', 'AppController@getPaises');
Route::get('MM/ciudades/{idioma_id}/{pais_id}', 'AppController@getCiudades');
Route::get('MM/eventos/{idioma_id}/{pais_id}/{localidad_id}', 'AppController@getEventos2');
Route::get('MM/idiomas', 'AppController@getIdiomas');
//
Route::get('MM/ciudadesviejas', 'AppController@getLocalidades');
//APORTES
Route::get('GAPP/GETMIEMBROSAPORTES/{id_Aporte}/{id_lumisial}/{id_year}/{token}', 'AppController@getMiembrosAportes');
Route::get('GAPP/GETMIEMBROAPORTE/{id}/{token}', 'AppController@getMiembroAporte');
Route::get('GAPP/GETLUMISIALES/{token}', 'AppController@getLumisiales');
Route::get('GAPP/SAVEAPORTE/{id_miembro}/{id_lumisial}/{monto}/{moneda}/{nro_comprobante}/{ejercicio}/{token}', 'AppController@saveAporte');
Route::post('GAPP/UPLOADAPORTE', 'AppController@uploadAporte'); 
Route::get('GAPP/DELETEAPORTE/{id}/{token}', 'AppController@deleteAporte');
Route::get('GAPP/GETAPORTESPORLUMISIAL/{year}/{token}', 'AppController@getAportesPorLumisial');
//LUMISIALES
Route::get('GAPP/ADDMIEMBROTEMPORAL/{documento}/{nombre}/{telefono}/{token}', 'AppController@addMiembroTemporal');
Route::get('GAPP/GETMIEMBROLUMISIAL/{lumisial}/{token}', 'AppController@getMiembroLumisial');
//CUENTAS-CONTABLES
Route::get('GAPP/GETMOVIMIENTOSCONTABLES/{responsable}/{periodo}/{token}', 'AppController@getMovimientosContables');
Route::get('GAPP/GETMOVIMIENTOCONTABLE/{id}/{token}', 'AppController@getMovimientoContable');
Route::post('GAPP/UPDATEMOVIMIENTOSCONTABLES', 'AppController@updateMovimientosContables');
Route::get('GAPP/DELETEMOVIMIENTOSCONTABLES/{id}/{token}', 'AppController@deleteMovimientosContables');
Route::post('GAPP/UPLOADMOVIMIENTOSCONTABLES', 'AppController@uploadMovimientosContables'); 
//ENCUENTA
Route::get('GAPP/ENCUESTAS', 'AppController@EncuestasIndex');
Route::get('GAPP/ENCUESTAS/{id}', 'AppController@EncuestasShow');
Route::post('GAPP/ENCUESTAS', 'AppController@EncuestasStore');
Route::post('GAPP/ENCUESTAS/{id}/respuestas', 'AppController@EncuestasRespuestas');
Route::get('GAPP/ENCUESTAS/{id}/resultados', 'AppController@getResultados');
Route::get('GAPP/ENCUESTAS/{id}/dni', 'AppController@getResultadosDni');
// My Gnosis
Route::post('MG/asistencia/notificar', 'ExtController@registrarAsistencia');
//Pase y salvo --  
Route::post('GAPP/ADDPASEYSALVO/{token}', 'AppController@addPaseYSalvo');
Route::get('GAPP/GETPASEYSALVO/{id}/{token}', 'AppController@getPaseYSalvo');
Route::get('GAPP/GETPAZYSALVOBYID/{id}/{token}', 'AppController@getPaseYSalvoById');
Route::get('GAPP/DELETEPASEYSALVO/{id}/{token}', 'AppController@deletePaseYSalvo');

//INSCRIPCIONES EDECAN
Route::post('EDECAN/INSCRIPCION/CREATE', 'FormController@RegistrarInscripcionAPI');