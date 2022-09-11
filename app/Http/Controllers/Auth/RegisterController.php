<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use App\Pais;
use App\Capacitacion_de_personal;
use App\Http\Controllers\NotificationController;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $modo = $_POST['modo'];
        $rol_de_usuario_id = null;

        
        if ($modo == 'cap') {
            $equipo_id = 25;
            $funcion = 'Alumno';
            $rol_de_usuario_id = 17;
        }
        else {

            $pais_id = $data['pais_id'];
            $NotificationController = new NotificationController();

            //NOTIFICO AL COORDINADOR DEL EQUIPO DE UNA NUEVA CAMPAÑA
            if ($pais_id == 6) {
                $user_id = 19;
            }            
            else {
                if ($pais_id == 1) {
                    $user_id = 41;   
                }            
                else {
                    $user_id = 28;   
                }
            }

            //$user_id = 1; 
            $Pais = Pais::find($pais_id);
            $pais = $Pais->pais;

            $mensaje = __('Se ha registrado un nuevo usuario para').' '.$pais.': '.$data['name'];        
            $NotificationController->enviarNotificacion(1, $user_id, $mensaje);       

            $mensaje = __('Se ha registrado un nuevo usuario para').' '.$pais.': '.$data['name'];   

            /*
            if ($data['equipo_id'] == 6) {
                $rol_de_usuario_id = 7;
            }
            else {
                $rol_de_usuario_id = null;
            }
            */
            //$equipo_id = $data['equipo_id'];
            //$funcion = $data['funcion'];
        }

        $usuario_creado = User::create([
            'name' => $data['name'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'pais_id' => $data['pais_id'],
            'idioma_id' => $data['idioma_id'],
            'celular' => $data['celular'],
            'ciudad' => $data['ciudad'],
            'lumisial' => $data['lumisial'],
            'diocesis' => $data['diocesis'],
            'sino_activo' => 'SI',
            'rol_de_usuario_id' => $rol_de_usuario_id,
        ]);

        if ($modo == 'cap') {
            $capacitacion_id = $data['capacitacion_id'];

            if ($capacitacion_id > 0) {
                $Capacitacion_de_personal = new Capacitacion_de_personal();
                $Capacitacion_de_personal->user_id = $usuario_creado->id;
                $Capacitacion_de_personal->capacitacion_id = $capacitacion_id;
                $Capacitacion_de_personal->save();
            }

        }
        

        return $usuario_creado;
    }
}
