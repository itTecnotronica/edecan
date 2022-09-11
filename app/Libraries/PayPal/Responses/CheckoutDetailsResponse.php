<?php

namespace Payment\PayPal\Responses;

use \Payment\PayPal\Util\Parser;

class CheckoutDetailsResponse
{
    public function __construct($body)
    {
        $this->Token = $body["TOKEN"];
        $this->Date = Parser::getDateTimeFromPayPal($body["TIMESTAMP"]);
        $this->CorrelationId = $body["CORRELATIONID"];
        $this->Ack = ($body["ACK"] == "Success" || $body["ACK"] == "SuccessWithWarning");
        $this->Version = $body["VERSION"];
        $this->Build = $body["BUILD"];

        $this->BillingAgreementAcceptedStatus = $body["BILLINGAGREEMENTACCEPTEDSTATUS"];
        $this->CheckoutStatus = $body["CHECKOUTSTATUS"];

        $this->Amount = (float)($body["AMT"]);
        $this->AmountItem = (float)($body["ITEMAMT"]);
        $this->ShippingAmount = (float)($body["SHIPPINGAMT"]);
        $this->HandlingAmount = (float)($body["HANDLINGAMT"]);
        $this->TaxAmount = (float)($body["TAXAMT"]);
        $this->InsuranceAmount = (float)($body["INSURANCEAMT"]);
        $this->ShippingDiscountAmount = (float)($body["SHIPDISCAMT"]);

        $this->ItemName = $body["L_NAME0"];
        $this->ItemQuantity = (int)($body["L_QTY0"]);
        $this->ItemTaxAmount = (float)($body["L_TAXAMT0"]);
        $this->ItemAmount = (float)($body["L_AMT0"]);

        $this->PaymentCurrencyCode = $body["PAYMENTREQUEST_0_CURRENCYCODE"];
        $this->PaymentAmount = (float)($body["PAYMENTREQUEST_0_AMT"]);
        $this->PaymentAmountItem = (float)($body["PAYMENTREQUEST_0_ITEMAMT"]);
        $this->PaymentShippingAmount = (float)($body["PAYMENTREQUEST_0_SHIPPINGAMT"]);
        $this->PaymentHandlingAmount = (float)($body["PAYMENTREQUEST_0_HANDLINGAMT"]);
        $this->PaymentTaxAmount = (float)($body["PAYMENTREQUEST_0_TAXAMT"]);
        $this->PaymentInsuranceAmount = (float)($body["PAYMENTREQUEST_0_INSURANCEAMT"]);
        $this->PaymentShippingDiscountAmount = (float)($body["PAYMENTREQUEST_0_SHIPDISCAMT"]);
        
        $this->PaymentAddressNormalizationStatus = $body["PAYMENTREQUEST_0_ADDRESSNORMALIZATIONSTATUS"];

        $this->PaymentItemName = $body["L_PAYMENTREQUEST_0_NAME0"];
        $this->PaymentItemQuantity = (int)($body["L_PAYMENTREQUEST_0_QTY0"]);
        $this->PaymentItemAmount = (float)($body["L_PAYMENTREQUEST_0_TAXAMT0"]);
        $this->PaymentItemAmount = (float)($body["L_PAYMENTREQUEST_0_AMT0"]);
        $this->PaymentErrorCode = $body["PAYMENTREQUESTINFO_0_ERRORCODE"];
    }

    public $Token;
    public $Date;
    public $CorrelationId;
    public $Ack;
    public $Version;
    public $Build;

    public $BillingAgreementAcceptedStatus;
    public $CheckoutStatus;

    public $Amount;
    public $AmountItem;
    public $ShippingAmount;
    public $HandlingAmount;
    public $TaxAmount;
    public $InsuranceAmount;
    public $ShippingDiscountAmount;

    public $ItemName;
    public $ItemQuantity;
    public $ItemTaxAmount;
    public $ItemAmount;
    
    public $PaymentCurrencyCode;
    public $PaymentAmount;
    public $PaymentAmountItem;
    public $PaymentShippingAmount;
    public $PaymentHandlingAmount;
    public $PaymentTaxAmount;
    public $PaymentInsuranceAmount;
    public $PaymentShippingDiscountAmount;
    
    public $PaymentAddressNormalizationStatus;
    public $PaymentItemName;
    public $PaymentItemQuantity;
    public $PaymentItemAmount;
    public $PaymentErrorCode;
}
