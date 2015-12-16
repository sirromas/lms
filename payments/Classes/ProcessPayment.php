<?php

ini_set('display_errors', '1');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/lms/payments/Api/vendor/autoload.php');

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

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

    function prepare_order($order) {
        $exp_date = '20'.$order->cds_cc_exp_year . '-' . $order->cds_cc_exp_month;
        //echo "<br/>Expiration date: " . $exp_date . "<br/>";
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($order->cds_cc_number);
        $creditCard->setExpirationDate($exp_date);
        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);
        return $payment;
    }

    function create_profile($order) {
        $names = explode(" ", $order->cds_name);
        $billto = new AnetAPI\CustomerAddressExType();
        $billto->setFirstName($names[0]);
        $billto->setLastName($names[1]);
        $billto->setCompany("Student");
        $billto->setAddress($order->cds_address_1);
        $billto->setCity($order->cds_city);
        $billto->setState($order->cds_state);
        $billto->setZip($order->cds_zip);
        $billto->setCountry("USA");

        // Create a Customer Profile Request
        //  1. create a Payment Profile
        //  2. create a Customer Profile   
        //  3. Submit a CreateCustomerProfile Request
        //  4. Validate Profiiel ID returned

        $payment = $this->prepare_order($order);
        $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
        $paymentprofile->setCustomerType('individual');
        $paymentprofile->setBillTo($billto);
        $paymentprofile->setPayment($payment);
        $paymentprofiles[] = $paymentprofile;
        $customerprofile = new AnetAPI\CustomerProfileType();
        $customerprofile->setDescription($order->cds_name);
        $merchantCustomerId = time() . rand(1, 150);
        $customerprofile->setMerchantCustomerId($merchantCustomerId);
        $customerprofile->setEmail($order->cds_email);
        $customerprofile->setPaymentProfiles($paymentprofiles);

        $refId = 'ref' . time();
        $merchantAuthentication = $this->authorize();
        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setProfile($customerprofile);
        $controller = new AnetController\CreateCustomerProfileController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            echo "SUCCESS: PROFILE ID : " . $response->getCustomerProfileId() . "\n";
            echo "SUCCESS: PAYMENT PROFILE ID : " . $response->getCustomerPaymentProfileIdList()[0] . "\n";
            echo "<br/><br/><br/>";
        } else {
            echo "ERROR :  Invalid response\n";
            echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " . $response->getMessages()->getMessage()[0]->getText() . "\n";
            echo "<br/><br/><br/>";
        }
    }

    function make_transaction($post_order) {

        // Create the payment data for a credit card        
        $payment = $this->prepare_order($post_order);
        $merchantAuthentication = $this->authorize();
        $refId = 'ref' . time();

        // Order info
        $invoiceNo = time();
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($invoiceNo);
        $order->setDescription("Payment per semestr");

        // Line Item Info
        $lineitem = new AnetAPI\LineItemType();
        $lineitem->setItemId("Payment per semestr");
        $lineitem->setName("One time payment");
        $lineitem->setDescription("Paid access to LMS");
        $lineitem->setQuantity("1");
        $lineitem->setUnitPrice($this->PAYMENT_SUM);
        $lineitem->setTaxable("N");

        // Customer info 
        $custID = round(time() / 3785);
        $customer = new AnetAPI\CustomerDataType();
        $customer->setId($custID);
        $customer->setEmail($post_order->cds_email);

        //Ship To Info
        $names = explode(" ", $post_order->cds_name);
        $shipto = new AnetAPI\NameAndAddressType();
        $shipto->setFirstName($names[0]);
        $shipto->setLastName($names[1]);
        $shipto->setCompany('Student');
        $shipto->setAddress($post_order->cds_address_1);
        $shipto->setCity($post_order->cds_city);
        $shipto->setState($post_order->cds_state);
        $shipto->setZip($post_order->cds_zip);
        $shipto->setCountry("USA");

        // Bill To
        $billto = new AnetAPI\CustomerAddressType();
        $billto->setFirstName($names[0]);
        $billto->setLastName($names[1]);
        $billto->setCompany("Student");
        $billto->setAddress($post_order->cds_address_1);
        $billto->setCity($post_order->cds_city);
        $billto->setState($post_order->cds_state);
        $billto->setZip($post_order->cds_zip);
        $billto->setCountry("USA");

        //create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($this->PAYMENT_SUM);
        $transactionRequestType->setPayment($payment);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->addToLineItems($lineitem);
        $transactionRequestType->setCustomer($customer);
        $transactionRequestType->setBillTo($billto);
        $transactionRequestType->setShipTo($shipto);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        if ($response != null) {
            $tresponse = $response->getTransactionResponse();
            /*
             * 
            echo "<pre>";
            print_r($tresponse);
            echo "</pre>";
             * 
             */
            if (($tresponse != null) && ($tresponse->getResponseCode() == "1")) {
                //echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
                //echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
                $status=array('auth_code'=>$tresponse->getAuthCode(), 
                              'trans_id'=>$tresponse->getTransId(),
                              'sum'=>$this->PAYMENT_SUM);
                return $status;
            } else {
                //echo "Charge Credit Card ERROR :  Invalid response\n";
                return false;
            }
        } else {
            //echo "Charge Credit card Null response returned";
            return false;
        }
    }

}
