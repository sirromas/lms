<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . 'lms/payments/vendor/autoload.php');

class ProcessPayment {

    private $db;
    public $PAYMENT_SUM = '20.00';
    private $AUTHORIZENET_LOG_FILE;
    private $LOGIN_ID = '6cUTfQ5238'; // Test sandbox for now
    private $TRANSACTION_KEY = '3JdSm73D2R624xxr'; // Test sandbox for now

    function __construct() {
        $this->db = DB::getInstance();
        $this->AUTHORIZENET_LOG_FILE = 'phplog';
    }

    function authorize() {
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->LOGIN_ID);
        $merchantAuthentication->setTransactionKey($this->TRANSACTION_KEY);
        return $merchantAuthentication;
    }

    function prepare_order($card_no, $year_exp, $month_exp) {
        $exp_date = $year_exp . '-' . $month_exp;
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($card_no);
        $creditCard->setExpirationDate($exp_date);
        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);
        return $payment;
    }

    function make_transaction($card_no, $year_exp, $month_exp) {
        $merchantAuthentication = $this->authorize();
        $payment = $this->prepare_order($card_no, $year_exp, $month_exp);
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($this->PAYMENT_SUM);
        $transactionRequestType->setPayment($payment);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setTransactionRequest($transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        if ($response != null) {
            $tresponse = $response->getTransactionResponse();
            if (($tresponse != null) && ($tresponse->getResponseCode() == "1")) {
                echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
                echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
            } // end if ($tresponse != null) && ($tresponse->getResponseCode() == "1")
            else {
                echo "Charge Credit Card ERROR :  Invalid response\n";
            }
        } // end if $response != null 
        else {
            echo "Charge Credit card Null response returned";
        }
    }

}
