<?php

namespace Payment\PayPal;

use \Payment\PayPal\Requests\AuthorizeCheckoutRequest;
use \Payment\PayPal\Requests\DoCheckoutRequest;

use \Payment\PayPal\Responses\AuthorizeCheckoutResponse;
use \Payment\PayPal\Responses\CheckoutDetailsResponse;
use \Payment\PayPal\Responses\DoCheckoutResponse;

class PayPalClient
{
    public function __construct()
    {
        
    }

    /**
     * Envia uma requisição NVP para uma API PayPal.
     *
     * @param array $requestNvp Define os campos da requisição.
     * @param boolean $sandbox Define se a requisição será feita no sandbox ou no
     *                         ambiente de produção.
     *
     * @return array Campos retornados pela operação da API. O array de retorno poderá
     *               ser vazio, caso a operação não seja bem sucedida. Nesse caso, os
     *               logs de erro deverão ser verificados.
     */
    function sendNvpRequest(array $requestNvp)
    {
        $config = new PayPalConfiguration;
        $sandbox = $config->getSandbox();

        //Endpoint da API
        $apiEndpoint  = 'https://api-3t.' . ($sandbox? 'sandbox.': null);
        $apiEndpoint .= 'paypal.com/nvp';
    
        //Executando a operação
        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestNvp));
    
        $response = urldecode(curl_exec($curl));
    
        curl_close($curl);
    
        //Tratando a resposta
        $responseNvp = array();
    
        if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
            foreach ($matches['name'] as $offset => $name) {
                $responseNvp[$name] = $matches['value'][$offset];
            }
        }
    
        //Verificando se deu tudo certo e, caso algum erro tenha ocorrido,
        //gravamos um log para depuração.
        if (isset($responseNvp['ACK']) && $responseNvp['ACK'] != 'Success') {
            for ($i = 0; isset($responseNvp['L_ERRORCODE' . $i]); ++$i) {
                $message = sprintf("PayPal NVP %s[%d]: %s\n",
                                $responseNvp['L_SEVERITYCODE' . $i],
                                $responseNvp['L_ERRORCODE' . $i],
                                $responseNvp['L_LONGMESSAGE' . $i]);
    
                error_log($message);
            }
        }
    
        return $responseNvp;
    }

    /**
     * return AuthorizeCheckoutRequest
     */
    public function AuthorizeCheckout(AuthorizeCheckoutRequest $param) {

        $config = new PayPalConfiguration;

        setlocale (LC_ALL, 'en_US');

        $requestNvp = [
            "USER" =>$config->getUser(),
            "PWD" =>$config->getPassword(),
            "SIGNATURE" =>$config->getSignature(),
            "METHOD" =>"SetExpressCheckout",
            "VERSION" =>$config->getVersionApi(),
            "LOCALECODE" =>"pt_BR",
            "PAYMENTREQUEST_0_PAYMENTACTION" =>"SALE",
            "PAYMENTREQUEST_0_AMT" => $param->TotalValue,
            "PAYMENTREQUEST_0_CURRENCYCODE" =>"BRL",
            "PAYMENTREQUEST_0_ITEMAMT" => $param->SubtotalValue,
            "PAYMENTREQUEST_0_INVNUM" => "",
            "L_PAYMENTREQUEST_0_NAME0" =>$param->ProductName,
            "L_PAYMENTREQUEST_0_DESC0" =>$param->ProductDescription,
            "L_PAYMENTREQUEST_0_AMT0" =>$param->ItemTotalValue,
            "L_PAYMENTREQUEST_0_QTY0" =>$param->ItemQuantity,
            "L_PAYMENTREQUEST_0_ITEMAMT" =>$param->ItemValue,
            "RETURNURL" =>$param->ReturnUrlAuthorized,
            "CANCELURL" =>$param->ReturnUrlNotAuthorized
        ];

        //Envia a requisição e obtém a resposta da PayPal
        $responseNvp = $this->sendNvpRequest($requestNvp);
        
        return AuthorizeCheckoutResponse::buildAuthorizeCheckoutResponse($responseNvp); 
    }

    /**
     * return DoCheckoutResponse
     */
    public function DoCheckout(DoCheckoutRequest $param) {
        $config = new PayPalConfiguration();

        setlocale (LC_ALL, 'en_US');

        $content = [
            "USER" =>$config->getUser(),
            "PWD" =>$config->getPassword(),
            "SIGNATURE" =>$config->getSignature(),
            "METHOD" => "DoExpressCheckoutPayment",
            'VERSION' => $config->getVersionApi(),
            "TOKEN" => $param->Token,
            "PAYERID" => $param->PayerId,
            "PAYMENTREQUEST_0_PAYMENTACTION" => "SALE",
            "PAYMENTREQUEST_0_AMT" => $param->TotalValue,
            "PAYMENTREQUEST_0_CURRENCYCODE" => "BRL",
            "PAYMENTREQUEST_0_ITEMAMT" => $param->SubtotalValue,
            "PAYMENTREQUEST_0_INVNUM" => "",
            "L_PAYMENTREQUEST_0_NAME0" => $param->ProductName,
            "L_PAYMENTREQUEST_0_DESC0" => $param->ProductDescription,
            "L_PAYMENTREQUEST_0_AMT0" => $param->ItemTotalValue,
            "L_PAYMENTREQUEST_0_QTY0" => $param->ItemQuantity,
            "L_PAYMENTREQUEST_0_ITEMAMT" => $param->ItemValue
        ];

        $responseNvp = $this->sendNvpRequest($content);
        return new DoCheckoutResponse($responseNvp); 
    }

    /**
     * return CheckoutDetailsResponse
     */
    public function GetCheckoutDetails($token)
    {
        $config = new PayPalConfiguration();

        $content = [
            "USER" =>$config->getUser(),
            "PWD" =>$config->getPassword(),
            "SIGNATURE" =>$config->getSignature(),
            'VERSION' => $config->getVersionApi(),
            "METHOD" => "GetExpressCheckoutDetails",
            "TOKEN" => $token
        ];

        $responseNvp = $this->sendNvpRequest($content);
        return new CheckoutDetailsResponse($responseNvp);
    }
}

