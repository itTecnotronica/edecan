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


Route::get('APP/GETLISTAA/{pais_id}/{token}', 'AppController@getListAA');
Route::get('APP/ACTUALIZAR-ESTADO-ALUMNO/{inscripto_id}/{instancia_de_seguimiento_id}/{observaciones}/{user_id}/{token}', 'AppController@actualizarEstadoAlumno');

Route::get('APP//dialog', 'ExtController@dialog');