<?php

namespace Payment\PayPal {

    class PayPalConfiguration {

        private $sandbox = true;
        private $versionApi = "114.0";

        public function __construct()
        {
            
        }

        public function getVersionApi(){
            return $this->versionApi;
        }

        public function getSandbox(){
            return $this->sandbox;
        }

        public function getUser()
        {
            return ($this->sandbox) 
                ? "ensinamento-facilitator_api1.gnosisbrasil.com" 
                : "ensinamento_api1.gnosisbrasil.com";
        }

        public function getPassword()
        {
            return ($this->sandbox) ? "7YWP5R6SNK4BCZMU" : "8NQDXCGHMAUD73QD";
        }

        public function getSignature()
        {
            return ($this->sandbox) 
                ? "Af9IIxpUCVKczP0yObC3owL4X9chAuw2eo-fwuVxbuPxIscAplF0H7GB" 
                : "ANVH3tOUwJKCIJ8KPZiMcJV1BEkFAHfmboypCBiE3qH5De1xDVChl203";
        }

        public function getRequestCheckoutUrl()
        {
            return $this->sandbox ? "https://api-3t.sandbox.paypal.com/nvp" 
                : "https://api-3t.paypal.com/nvp";   
        }
        
        public function getBuildUrlCheckout($token)
        {
            return $this->sandbox 
                ? "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=" . $token
                : "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=" . $token;
        }

        public function getRestApiHost()
        {
            return $this->sandbox ? "https://api.sandbox.paypal.com" 
                : "https://api.sandbox.paypal.com";
        }

        public function getClientID()
        {
            return $this->sandbox 
                ? "AaDuKxlxnnYJqWPT5is51wO9qtVQwjeEpSvM1CoWV-7Ln8xXXl74uw8w6ESDGMbn9OEZqtOkIXhH7tEM" 
                : "AUzkDaQ7LEASwhbLCpwJuFpiwVOwn-_uK2s4ry_EdBVS8tL69wNRmE9L7tyseif73LTBoZbKJi7AoVNO";
        }

        public function getSecret()
        {
            return $this->sandbox 
                ? "EGs-OOdUO8QwzaUeEBE6a8-ZU3FSSBjugU1G-jPdtMygWCe8BM3mi5B9flJ7eXs7qVzSH8-059MiYzhz"
                : "EMgzsck7yrvuOgCN5jYXV98hz9X1pOJITa85aAPv7kZLSJtvUCh97SLhAxSoeYYamAJvJmuA18ifqxAs";
        }
    }
}