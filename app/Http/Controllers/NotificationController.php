<?php

namespace App\Http\Controllers;
use App\User;

use Auth;
use Mail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GenericController;
use App\Mail\NotificacionEmail;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function enviarNotificacion($medio, $user_id, $mensaje)
    {   
        //$medio
        //0 = todos
        //1 = telegram
        //2 = e-mail

        if (env('APP_ENV') <> 'development') {
            $destinatario = User::find($user_id);

            if ($medio == 0 or $medio == 1) {
                $chat_id = $destinatario->telegram_chat_id;

                if ($chat_id <> '') {
                    $GenericController = new GenericController();
                    $gen_campos = $GenericController->enviarTelegram($chat_id, $mensaje);        
                }             
            }

            if ($medio == 0 or $medio == 2) {
                $mail = $destinatario->email;
                if ($mail <> '') {
                    Mail::to($destinatario)->send(new NotificacionEmail($mensaje));       
                }  
            }
        }

    }


}
