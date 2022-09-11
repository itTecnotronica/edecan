<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use Auth;
use App\Log_accion;

use DB;
use App\Sesion;
use App\Registro_de_error;
use Session;
use URL;
use Browser;

class RegistroActividadDeUsuario
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (!Auth::guest())
        {
            $user_id = Auth::user()->id;
            if (!$request->session()->has('sesion_id')) {
                $sesion_id = null;
            }
            else {
                $sesion_id = $request->session()->get('sesion_id');
            }

            $Log_accion = new Log_accion();
            $Log_accion->user_id = $user_id;
            $Log_accion->sesion_id = $sesion_id;
            $Log_accion->url = substr(Request::fullUrl(), 0, 80);
            $Log_accion->save();            
        
        }
    
    return $next($request);

    }
}
