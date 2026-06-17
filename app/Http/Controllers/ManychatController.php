<?php

namespace App\Http\Controllers;

//accionesPosteriores
use App\Inscripcion;

use App\Http\Controllers\GenericController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


use Auth;

class ManychatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }


    public function registrarContactoEnManychat($inscripcion_id) {

        $Inscripcion = Inscripcion::find($inscripcion_id);
        $solicitud_id = $Inscripcion->solicitud_id;
        $celular = $Inscripcion->celular_wa();
        //$celular = '+'.$celular;

        //$subscriber_id = $this->findSubscriberByPhone($celular);
        $subscriber_id = $this->findByCustomField($celular);

        if (!$subscriber_id) {


            $url = 'https://api.manychat.com/fb/subscriber/createSubscriber';
            $apiToken = env('MANYCHAT_API_TOKEN');


            // 1. Preparar los datos
            $data = [
                "first_name"       => $Inscripcion->nombre,
                "last_name"        => $Inscripcion->apellido,
                "phone"            => $celular,
                "whatsapp_phone"   => $celular,
                "email"            => $Inscripcion->email_correo,
                "gender"           => null,
                "has_opt_in_sms"   => true,
                "has_opt_in_email" => true,
                "consent_phrase"   => __('Me gustaría recibir notificaciones o información sobre los próximos cursos y eventos gratuitos (WhatsApp o email)'),

                "custom_fields" => [
                    "name" => "edecan_code",
                    "value" => "$celular"
                ],
            ];

            $jsonData = json_encode($data);

            
            $resultado = $this->curlCall($url, $jsonData);

            //dd($resultado);

            $subscriber_id = null;

            if (!$resultado['error']) {
                $response = $resultado['response'];
                $subscriber_id = ($response['status'] == 'success' and count($response['data']) > 0) ? $response['data']['id'] : null;

                $this->asignarEdecanCode($subscriber_id, $celular);

            }
            

        }

        if ($subscriber_id) {
            $tag_id = $this->CrearOTraerTagId($solicitud_id);
            $tagAsignado = $this->asignarTagASubscriber($subscriber_id, $tag_id);
        }

        //dd($subscriber_id);

        return $subscriber_id;
    }




    public function findByCustomField($celular)
    {
        
        // 1. Construir la URL incluyendo el parámetro phone
        // Usamos urlencode por seguridad si el teléfono incluyera caracteres especiales como el +
        $url = 'https://api.manychat.com/fb/subscriber/findByCustomField?field_id=14257437&field_value='.$celular;

        //dd($url);

        $resultado = $this->curlCall($url);

        $subscriber_id = null;

        if (!$resultado['error']) {
            $response = $resultado['response'];
            if ($response['status'] == 'success' and count($response['data']) > 0) {
                foreach ($response['data'] as $data) {
                    if ($data['status'] == 'active') {
                        $subscriber_id = $data['id'];
                    }
                }
            }
        }

        return $subscriber_id;

    }



    public function findSubscriberByPhone($phone = '+59898160461')
    {
        
        // 1. Construir la URL incluyendo el parámetro phone
        // Usamos urlencode por seguridad si el teléfono incluyera caracteres especiales como el +
        $url = 'https://api.manychat.com/fb/subscriber/findBySystemField?phone='.$phone;

        //dd($url);

        $resultado = $this->curlCall($url);

        $subscriber_id = null;

        if (!$resultado['error']) {
            $response = $resultado['response'];
            $subscriber_id = ($response['status'] == 'success' and count($response['data']) > 0) ? $response['data']['id'] : null;
        }

        return $subscriber_id;
    }



    public function CrearOTraerTagId($solicitud_id)
    {
        
        $tag_id = $this->traerTagId($solicitud_id);

        if (!$tag_id) {
            $tag_id = $this->crearTag($solicitud_id);
            //dd('creado tag_id: '.$tag_id);
        }

        return $tag_id;
    }

    public function traerTagId($solicitud_id)
    {
        
        // 1. Construir la URL incluyendo el parámetro phone
        // Usamos urlencode por seguridad si el teléfono incluyera caracteres especiales como el +
        $url = 'https://api.manychat.com/fb/page/getTags';

        $resultado = $this->curlCall($url);
        $tags = null;
        $tag_id = null;
        if (!$resultado['error']) {
            $response = $resultado['response'];
            $tags = ($response['status'] == 'success' and count($response['data']) > 0) ? $response['data'] : null;
            $tagname = $this->tagName($solicitud_id);
            $tag = collect($tags)->where('name', $tagname)->first();

            if ($tag) {
                $tag_id = $tag['id'];
                //dd('existe tag_id: '.$tag_id);
            } 

        }

        return $tag_id;
    }



    public function crearTag($solicitud_id) {


        $url = 'https://api.manychat.com/fb/page/createTag';
        $apiToken = env('MANYCHAT_API_TOKEN');
        $tagname = $this->tagName($solicitud_id);


        // 1. Preparar los datos
        $data = [
            "name" => $tagname
        ];

        $jsonData = json_encode($data);

        $resultado = $this->curlCall($url, $jsonData);

        $tag_id = $this->traerTagId($solicitud_id);

        return $tag_id;
    }


    public function asignarTagASubscriber($subscriber_id, $tag_id) {


        $url = 'https://api.manychat.com/fb/subscriber/addTag';
        $apiToken = env('MANYCHAT_API_TOKEN');


        // 1. Preparar los datos
        $data = [
            "subscriber_id" => $subscriber_id,
            "tag_id" => $tag_id,
        ];

        $jsonData = json_encode($data);

        $resultado = $this->curlCall($url, $jsonData);


        if (!$resultado['error']) {
            $response = $resultado['response'];
            $tagAsignado = $response['status'] == 'success' ? true : false;
        }
        else {
            $tagAsignado = false;
        }

        //dd($subscriber_id);

        return $tagAsignado;

    }




    public function curlCall($url, $jsonData = null) {

        $apiToken = env('MANYCHAT_API_TOKEN');

        // 2. Inicializar cURL
        $ch = curl_init($url);

        // 3. Configurar opciones
        if ($jsonData) {
            // 3. Configurar opciones de cURL
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiToken,
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
        }
        else {
            // 3. Configurar opciones de cURL
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET'); // Especificamos que es GET
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiToken,
                'Accept: application/json'
            ]);
        }


        // 4. Ejecutar y capturar respuesta
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        

        curl_close($ch);

        $resultado = [
            'error' => true,
            'mensaje' => null,
            'response' => null,
        ];

        // 5. Manejo de errores de conexión
        if ($error) {
            //return response()->json(['error' => 'Error de conexión: ' . $error], 500);
            $resultado = [
                'error' => true,
                'mensaje' => 'Error de conexión: ' . $error,
                'response' => null,
            ];
        }
        else {
            
            //return response()->json($decodedResponse, $httpCode);
            $response = json_decode($response, true);
            
            if ($response['status'] == 'error') {
                $resultado = [
                    'error' => true,
                    'mensaje' => $response['message'],
                    'response' => $response,
                ];

            }
            else {

                $resultado = [
                    'error' => false,
                    'mensaje' => 'Sin errores',
                    'response' => $response,
                ];

            }

        }

        //dd($resultado);

        // 6. Decodificar y retornar la respuesta de ManyChat
        //$decodedResponse = json_decode($response, true);

        return $resultado;

    }

    public function tagName($solicitud_id) {

        $tagname = 'solicitud_id_'.$solicitud_id;
        
        return $tagname; 
    }


    public function asignarEdecanCode($subscriber_id, $edecan_code) {


        $url = 'https://api.manychat.com/fb/subscriber/setCustomFieldByName';
        $apiToken = env('MANYCHAT_API_TOKEN');

        // 1. Preparar los datos
        $data = [
            "subscriber_id" => $subscriber_id,
            "field_name" => 'edecan_code',
            "field_value" => $edecan_code,
        ];

        $jsonData = json_encode($data);

        $resultado = $this->curlCall($url, $jsonData);

        return $resultado;
    }




}
