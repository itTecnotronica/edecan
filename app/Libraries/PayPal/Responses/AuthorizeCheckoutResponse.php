<?php

namespace Payment\PayPal\Responses;

use \Payment\PayPal\Util\Parser;
use \Payment\PayPal\PayPalConfiguration;

class AuthorizeCheckoutResponse
{
    public function __construct()
    {

    }

    public static function buildAuthorizeCheckoutResponse($body)
    {
        $Ack = ($body["ACK"] == "Success" || $body["ACK"] == "SuccessWithWarning");

        if (!$Ack)
            throw new Exception($body["L_LONGMESSAGE0"]);

        $m = new AuthorizeCheckoutResponse;
        $m->Ack = $body["ACK"];
        $m->Token = $body["TOKEN"];
        $m->Date = Parser::getDateTimeFromPayPal($body["TIMESTAMP"]);
        $m->CorrelationId = $body["CORRELATIONID"];
        $m->Version = $body["VERSION"];
        $m->Build = $body["BUILD"];

        return $m;
    }

    public  $Token ;
    public  $Date ;
    public  $CorrelationId ;
    public  $Ack ;
    public  $Version ;
    public  $Build ;

    public function getUrlDoCheckout()
    {
        return (new PayPalConfiguration())->getBuildUrlCheckout($this->Token);   
    }
}
