<?php

namespace App\Http\Controllers\Auth\Login;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Http\Controllers\Auth\LoginController as DefaultLoginController;

class AlumnosController extends DefaultLoginController
{
    protected $redirectTo = '/alumnos/home';

    
    public function showLoginForm()
    {
        return view('auth.login.alumnos');
    }
    
    public function login(Request $request){

        $this->validate($request,[
            'id' => 'required',
            'correo_o_cel' => 'required'
        ]);

        $id = $request->id;
        $correo_o_cel = $request->correo_o_cel;


        

        $alumno = $this->doLogin($id, $correo_o_cel);
        
        if ($alumno === null) {
          return redirect()->back()->withErrors(['Usuario no encontrado']);
        }else{
           $request->session()->put('alumno', $alumno->id);
        }

        return redirect('alumnos/home');

    }

    public function logout(Request $request){

        $request->session()->forget('alumno');
        return redirect('alumnos/login');

    }


    protected function doLogin($id, $correo_o_cel){
        
        return DB::table('inscripciones')
            ->select('id')
            ->where([
                ['id', '=', $id]
            ])
            ->where(function ($query) use(&$correo_o_cel) {
                $query->where('celular', 'like', "%{$correo_o_cel}%" )
                      ->orWhere('email_correo', 'like', "%{$correo_o_cel}%");
            })
            ->first();    
    }

}
