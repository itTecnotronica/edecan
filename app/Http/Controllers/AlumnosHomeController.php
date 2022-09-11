<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Inscripcion;
use App\Asistencia;
use App\Leccion;
use App\Testimonio;
use App\Rating;
use App\Sugerencia;

use App\Idioma_por_pais;
use App\Idioma;



class AlumnosHomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom_auth');
    }

    public function inicializarVariables()
    {
        $prueba2 = 'test';
    }


    public function informacionPersonal(Request $request)
    {
       
        $inscripcion_id = $request->session()->get('alumno');

        $alumno = Inscripcion::where('id', $inscripcion_id)->first();

        return view('espacio_de_usuario/informacion_personal')
          ->with('alumno', $alumno);
    }

    public function imagenPerfil(Request $request)
    {
        $inscripcion_id = $request->session()->get('alumno');

        $alumno = Inscripcion::where('id', $inscripcion_id)->first();

        return view('espacio_de_usuario/imagen_perfil')
          ->with('alumno', $alumno);
    }

    public function lecciones(Request $request)
    {
       

        $inscripcion_id = $request->session()->get('alumno');

        $alumno = Inscripcion::where('id', $inscripcion_id)->first();


        // Obtenemos las asistencias

        $asistencias = Asistencia::where('inscripcion_id', $alumno->id)->get();

        // ponemos las clases hechas en un array
        $lecciones_hechas = $asistencias->map(function ($item, $key) {
            if(isset($item->leccion_id)){
                return $item->leccion->orden_de_leccion;
            } 
        });

        $lecciones_hechas = $lecciones_hechas->toArray();

        // Obtenemos el curso en base a las asistencias:
        // si no hay asistencias usamos curso 1 por defecto
        // esto hay que mejorarlo, tengo que encontrar de donde sacar el curso

        if($asistencias->count() > 0){
            $curso_id = $asistencias->first()->leccion->curso_id;
        }
        else{
            $curso_id = 1;
        }


        $lecciones = Leccion::where('curso_id', $curso_id)->get();

        $progreso = round(count($lecciones_hechas) / $lecciones->count() * 100);

        return view('espacio_de_usuario/lecciones')
          ->with('alumno', $alumno)
          ->with('lecciones', $lecciones)
          ->with('asistencias', $asistencias)
          ->with('lecciones_hechas', $lecciones_hechas)
          ->with('progreso', $progreso);
    }

    public function miTestimonio(Request $request)
    {
        $inscripcion_id = $request->session()->get('alumno');

        $alumno = Inscripcion::where('id', $inscripcion_id)->first();    

        $testimonio = Testimonio::where('inscripcion_id', $inscripcion_id)->first();

        $rating = Rating::where('inscripcion_id', $inscripcion_id);
        
        // Si no existe creamos uno nuevo
        if ( is_null($testimonio) ) {
           $testimonio = new Testimonio;
        }

        $view = view('espacio_de_usuario/mi_testimonio')
          ->with('alumno', $alumno)
          ->with('testimonio', $testimonio);

        if($rating->count() > 0){
          $view->with('rating', $rating->first());      
        }
        else{
          $view->with('rating', '');    
        }

        return $view;
          
    }

    public function sugerencias(Request $request)
    {
        $inscripcion_id = $request->session()->get('alumno');

        $alumno = Inscripcion::where('id', $inscripcion_id)->first();    

        $sugerencia = Sugerencia::where('inscripcion_id', $inscripcion_id)->first();

        // Si no existe creamos uno nuevo
        if ( is_null($sugerencia) ) {
           $sugerencia = new Sugerencia;
        }

        return view('espacio_de_usuario/sugerencias')
          ->with('alumno', $alumno)
          ->with('sugerencia', $sugerencia);
    }

    public function actualizarInfoAlumno(Request $request)
    {	
    	$atributos = $request->except('_token');

    	Inscripcion::where('id', $atributos["id"])
    	          ->update($atributos);

        return redirect('alumnos/informacionPersonal');
    }

    public function guardarTestimonio(Request $request)
    {

        $inscripcion_id = $request->session()->get('alumno');
        $atributos = $request->except('_token');

        $datos_testimonio = $atributos["testimonio"];

        // Quitamos espacios al principio y al final
        if($datos_testimonio["texto"]){
            $datos_testimonio["texto"] = trim($datos_testimonio["texto"]);
        }

        $datos_testimonio['inscripcion_id'] = $inscripcion_id;

        Testimonio::create($datos_testimonio);


        return redirect('alumnos/miTestimonio');
    }

    public function guardarSugerencia(Request $request)
    {

        $inscripcion_id = $request->session()->get('alumno');
        $atributos = $request->except('_token');

        $datos_sugerencia = $atributos["sugerencia"];

        // Quitamos espacios al principio y al final
        if($datos_sugerencia["texto"]){
            $datos_sugerencia["texto"] = trim($datos_sugerencia["texto"]);
        }

        $datos_sugerencia['inscripcion_id'] = $inscripcion_id;

        Sugerencia::create($datos_sugerencia);

            
        return redirect('alumnos/sugerencias');
    }

    public function guardarRating(Request $request)
    {

        $inscripcion_id = $request->session()->get('alumno');
        $atributos = $request->except('_token');

        $datos_rating = [];
        $datos_rating['rating'] = $atributos["rating"];


        // Nos fijamos si ya existe un rating de esta persona
        $rating = Rating::where('inscripcion_id', $inscripcion_id)->first();

        // Si no existe creamos uno nuevo
        if ( is_null($rating) ) {

           $datos_rating['inscripcion_id'] = $inscripcion_id;

           $nuevo_rating = rating::create($datos_rating);

        }
        // Caso contrario actualizamos el rating de este alumno
        else{

            $rating->update($datos_rating);

        }

        return response()->json([
            'status' => 'success'
        ]);

    }

    public function cambiarImagenPerfil(Request $request){

        $inscripcion_id = $request->session()->get('alumno');
        // $atributos = $request->except('_token');

        // La imagen esta en Base64
        $data = $request->all()['image'];

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        Storage::disk('local')->put($inscripcion_id . '/perfil.png', $data);

        return response()->json([
            'status' => 'success'
        ]);

    }

    public function ejemplo()
    {
        return view('ejemplo');
    }

}
