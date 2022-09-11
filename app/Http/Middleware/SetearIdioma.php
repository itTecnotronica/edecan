<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Auth;

use App\Sesion;
use App\Registro_de_error;
use Session;
use URL;
use Request;
use Browser;
use App;

class SetearIdioma
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
        if (!Auth::guest()) {
            if (Auth::user()->idioma_id <> '') {
                $idioma = Auth::user()->idioma->mnemo;
                App::setLocale($idioma);    
            }
        }
        return $next($request);
    }
}
