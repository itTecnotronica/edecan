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
//Solicitudes segun usuario
Route::get('AULA/CODIGOALUMNO/{codigo}', 'AppController@getCodigoApp');
//S 
Route::get('AULA/APPUSUARIOID/{codigo_onesignal}', 'AppController@getAppUsuarioId');
//S 
Route::get('APP/GETCOORDENADA/{codigo_pais}/{latitud}/{longitud}', 'AppController@getCoordenada');
//
Route::get('APP/GETLISTAA/{pais_id}/{token}', 'AppController@getListAA');
Route::get('APP/GETLISTINSCRIPTOS/{solicitud_id}/{token}', 'AppController@getListInscriptos');
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
Route::get('GAPP/SAVEDEBITO/{id}/{tb_tarjeta_id}/{tb_tipo_de_tarjeta_id}/{tb_persona_id}/{numero_de_tarjeta}/{monto}/{observaciones}/{token}', 'AppController@saveDebito');
//GAPP Carnet
Route::get('GAPP/GETCARNET/{pais_id}/{id}/{token}', 'AppController@getCarnet');
Route::get('GAPP/SAVECARNET/{id}/{tb_tipo_de_carnet_id}/{tb_persona_id}/{token}', 'AppController@saveCarnet');
//GAPP Tablas
Route::get('GAPP/GETSEDES/{pais_id}/{token}', 'AppController@getSedes');
Route::get('GAPP/GETCENTROS/{pais_id}/{token}', 'AppController@getCentros');
Route::get('GAPP/GETEVENTOS/{pais_id}/{token}', 'AppController@getEventos');
Route::get('GAPP/GETTIPOCARNET/{pais_id}/{token}', 'AppController@getTipoCarnet');
Route::get('GAPP/GETTARJETA/{pais_id}/{token}', 'AppController@getTarjeta');
Route::get('GAPP/GETTIPOTARJETA/{pais_id}/{token}', 'AppController@getTipoTarjeta');
//GETMATERIAL
Route::get('GAPP/GETMATERIALSEARCH/{idioma_id}/{token}/{value}', 'AppController@getMaterialesSearch');
Route::get('GAPP/GETALLMATERIAL/{idioma_id}/{token}/{tipo}/{cant}/{autor}', 'AppController@getAllMateriales');
Route::get('GAPP/GETALLMATERIALRANDOM/{idioma_id}/{token}/{cant}', 'AppController@getAllMaterialesRandom');
//MIEMBROS
Route::get('GAPP/GETMIEMBRO/{token}/{documento}', 'AppController@getMiembro');
//
Route::get('APP/dialog', 'ExtController@dialog');

Route::get('MM/paises/{idioma_id}', 'AppController@getPaises');
Route::get('MM/ciudades/{idioma_id}/{pais_id}', 'AppController@getCiudades');
Route::get('MM/eventos/{idioma_id}/{pais_id}/{localidad_id}', 'AppController@getEventos2');
Route::get('MM/idiomas', 'AppController@getIdiomas');


Route::get('MM/ciudadesviejas', 'AppController@getLocalidades');

// My Gnosis
Route::post('MG/asistencia/notificar', 'ExtController@registrarAsistencia');
