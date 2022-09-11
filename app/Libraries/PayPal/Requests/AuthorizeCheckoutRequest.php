<?php

namespace Payment\PayPal\Requests
{
    class AuthorizeCheckoutRequest
    {
        public $TotalValue ;
        public $SubtotalValue ;
        public $ProductName ;
        public $ProductDescription ;
        public $ItemTotalValue ;
        public $ItemQuantity ;
        public $ItemValue ;
        public $ReturnUrlAuthorized ;
        public $ReturnUrlNotAuthorized ;
    }
}