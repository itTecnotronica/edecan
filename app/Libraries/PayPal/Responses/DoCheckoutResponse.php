<?php

namespace Payment\PayPal\Responses;

use \Payment\PayPal\Util\Parser;

class DoCheckoutResponse
{
    public function __construct($body)
    {

        $this->Token = $body["TOKEN"];
        $this->Date = Parser::getDateTimeFromPayPal($body["TIMESTAMP"]);
        $this->CorrelationId = $body["CORRELATIONID"];
        $this->Ack = ($body["ACK"] == "Success" || $body["ACK"] == "SuccessWithWarning");
        $this->Version = $body["VERSION"];
        $this->Build = $body["BUILD"];

        $this->SuccessPageRedirectRequested = (bool)($body["SUCCESSPAGEREDIRECTREQUESTED"]);
        $this->InsuranceOptionSelected =(bool)($body["INSURANCEOPTIONSELECTED"]);
        $this->ShippingOptionDefault =(bool)($body["SHIPPINGOPTIONISDEFAULT"]);

        $this->TransactionId = $body["PAYMENTINFO_0_TRANSACTIONID"];
        $this->TransactionType = $body["PAYMENTINFO_0_TRANSACTIONTYPE"];
        $this->PaymentType = $body["PAYMENTINFO_0_PAYMENTTYPE"];
        $this->OrderTime = Parser::getDateTimeFromPayPal($body["PAYMENTINFO_0_ORDERTIME"]);
        $this->Amount = (float)($body["PAYMENTINFO_0_AMT"]);
        $this->FeeAmount = (float)($body["PAYMENTINFO_0_FEEAMT"]);
        $this->SellerPayPalAccountId = $body["PAYMENTINFO_0_SELLERPAYPALACCOUNTID"];
        $this->TaxAmount = (float)($body["PAYMENTINFO_0_TAXAMT"]);
        $this->CurrencyCode = $body["PAYMENTINFO_0_CURRENCYCODE"];
        $this->PaymentStatus = $body["PAYMENTINFO_0_PAYMENTSTATUS"];
        $this->PedingReason = $body["PAYMENTINFO_0_PENDINGREASON"];
        $this->ReasonCode = $body["PAYMENTINFO_0_REASONCODE"];
        $this->ProtectionEligibility = $body["PAYMENTINFO_0_PROTECTIONELIGIBILITY"];
        $this->ProtectionEligibilityType = $body["PAYMENTINFO_0_PROTECTIONELIGIBILITYTYPE"];
        $this->SecureMercantAccountId = $body["PAYMENTINFO_0_SECUREMERCHANTACCOUNTID"];
        $this->ErrorCode = $body["PAYMENTINFO_0_ERRORCODE"];
    }

    public $Token;
    public $Date;
    public $CorrelationId;
    public $Ack;
    public $Version;
    public $Build;

    public $SuccessPageRedirectRequested;
    public $InsuranceOptionSelected;
    public $ShippingOptionDefault;

    public $TransactionId;
    public $TransactionType;
    public $PaymentType;
    public $OrderTime;
    public $SellerPayPalAccountId;
    public $Amount;
    public $FeeAmount;
    public $TaxAmount;
    public $CurrencyCode;
    public $PaymentStatus;
    public $PedingReason;
    public $ReasonCode;
    public $ProtectionEligibility;
    public $ProtectionEligibilityType;
    public $SecureMercantAccountId;
    public $ErrorCode;
}
