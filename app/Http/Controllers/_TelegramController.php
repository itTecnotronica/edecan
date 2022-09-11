<?php

namespace App\Http\Controllers;

use Mpociot\BotMan\BotMan;
use Mpociot\BotMan\Messages\Message;
use Mpociot\BotMan\Attachments\Location;

use App\Registro_de_error;

use App;

class TelegramController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $token = '1791910378:AAFAv8x_fni94HYEZ7T9S8oNM03FxXIHftE';
    private $website = 'https://api.telegram.org/bot';

    public function __construct()
    {
        //$this->middleware('auth');
    }


    public function initBot02()
    {  

        
        $detalle_de_origen = 'Ingreso al BotTelegram';
        $Registro_de_error = new Registro_de_error;
        $Registro_de_error->registro_de_error = 'bot3';
        $Registro_de_error->detalle_de_origen = 'bot3';
        $Registro_de_error->save(); 
        

        $botman = app('botman');

        $botman->hears('hello|hola|hi', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

        $botman->hears('call me {name}', function ($bot, $name) {
            $bot->reply('Your name is: '.$name.' - Canal: '.$bot->getMessage()->getChannel());
        });


        $botman->fallback(function($bot) {
            $bot->reply('Sorry, I did not understand these commands. Here is a list of commands I understand: ...');
        });


        
        $botman->receivesImages(function($bot, $images) {
            $bot->reply('url: '.$images[0]);
        });
        
        $botman->receivesAudio(function($bot, $audio) {
            $bot->reply('url: '.$audio[0]);
        });

        $botman->hears('holis(.*)', function (BotMan $bot) {
            $bot->reply('hello anything.');
        });



        $botman->receivesLocation(function($bot, Location $location) {
            $lat = $location->getLatitude();
            $lng = $location->getLongitude();
            $bot->reply('location: '.$lat.' - '.$lng);
        });


        $botman->hears('enviar', function (BotMan $bot) {
            // Build message object
            $message = Message::create('This is my text');
            
            // Reply message object
            $bot->reply($message);
        });

        // mensaje directo 
        // $botman->say('Message', '632979534');


        // Calling direct to the API for Telegram
        $botman->hears('sticker', function(BotMan $bot) {
            $bot->sendRequest('sendMessage', [
                'chat_id' => '632979534',
                'text' => 'enviando desde sendRequest'
            ]);

            $bot->say('Mensaje Directo', '632979534');
        });


        // start listening
        $botman->listen();
        //dd($botman->listen());
        //return View('telegram/bot02');
    }

}

