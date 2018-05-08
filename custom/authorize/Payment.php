<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/authorize/Api/vendor/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class Payment extends Utils
{

    private $AUTHORIZENET_LOG_FILE;
    //private $LOGIN_ID = '6cUTfQ5238'; // sandbox data
    //private $TRANSACTION_KEY = '5bN8q5WT3qa257p9'; // sandbox data
    private $LOGIN_ID = '83uKk2VcBBsC'; // production data
    private $TRANSACTION_KEY = '23P447taH34H26h5'; // production data
    public $period = 28; // 28 days of installment 
    public $log_file_path;
    public $first_semestr_start;
    public $first_semestr_end;
    public $second_semestr_start;
    public $second_semestr_end;

    function __construct()
    {
        parent::__construct();
        $this->AUTHORIZENET_LOG_FILE = 'phplog';
        $this->log_file_path = $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/authorize/failed_transactions.log';
        $y = date("Y");
        $ny = $y + 1;
        /*
        $this->first_semestr_start = "09/01/$y";
        $this->first_semestr_end = "02/20/$ny";
        $this->second_semestr_start = "03/01/$y";
        $this->second_semestr_end = "08/20/$y";
        */

        $this->first_semestr_start = $this->get_semestr_date('first_semestr_start');
        $this->first_semestr_end = $this->get_semestr_date('first_semestr_end ');
        $this->second_semestr_start = $this->get_semestr_date('second_semestr_start');
        $this->second_semestr_end = $this->get_semestr_date('second_semestr_end');
    }

    /**
     * @param $item
     * @return mixed
     */
    function get_semestr_date($item)
    {
        $query = "select * from mdl_semestr_duration where item_text='$item'";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $value = $row['item_value'];
        }
        return $value;
    }

    /**
     * @param $stateid
     * @return mixed
     */
    function get_user_state($stateid)
    {
        $query = "select * from mdl_states where id=$stateid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $code = $row['code'];
        }
        return $code;
    }

    /**
     * @param $data
     */
    function save_log($data)
    {
        $fp = fopen($this->log_file_path, 'a');
        $date = date('m-d-Y h:i:s', time());
        fwrite($fp, $date . "\n");
        fwrite($fp, print_r($data, TRUE));
        fclose($fp);
    }

    /**
     * @return AnetAPI\MerchantAuthenticationType
     */
    function authorize()
    {
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->LOGIN_ID);
        $merchantAuthentication->setTransactionKey($this->TRANSACTION_KEY);
        return $merchantAuthentication;
    }

    /**
     * @return AnetAPI\MerchantAuthenticationType
     */
    function sandbox_authorize()
    {
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName('6cUTfQ5238');
        $merchantAuthentication->setTransactionKey('5bN8q5WT3qa257p9');
        return $merchantAuthentication;
    }

    /**
     * @param $user
     * @param $status
     * @return stdClass
     */
    function add_student_payment($user, $status)
    {
        $key = $this->generateRandomString();
        $card_last_four = substr($user->cardnumber, -4);
        $key_dates = $this->get_key_expiration_dates();
        $added = time();
        $query = "insert into mdl_card_payments "
            . "(userid,"
            . "courseid,"
            . "groupid,"
            . "card_last_four,"
            . "transid,"
            . "authcode,"
            . "auth_key,"
            . "start_date,"
            . "exp_date,"
            . "amount,"
            . "added) "
            . "values($status->userid,"
            . "$this->courseid,"
            . "$status->groupid,"
            . "'$card_last_four',"
            . "'$status->trans_id',"
            . "'$status->auth_code',"
            . "'$key',"
            . "'" . strtotime($key_dates->start) . "',"
            . "'" . strtotime($key_dates->end) . "',"
            . "'$user->amount',"
            . "'$added')";
        //echo "Query: " . $query . "<br>";
        $this->db->query($query);
        $keyObj = new stdClass();
        $keyObj->key = $key;
        $keyObj->s = $key_dates->start;
        $keyObj->e = $key_dates->end;
        return $keyObj;
    }

    /**
     * @return stdClass
     */
    function get_key_expiration_dates()
    {
        $m = date('m');
        $key = new stdClass();
        if ($m >= 2 && $m < 9) {
            // Second semestr
            $key->start = $this->second_semestr_start;
            $key->end = $this->second_semestr_end;
        } // end if
        else {
            // First semestr
            $key->start = $this->first_semestr_start;
            $key->end = $this->first_semestr_end;
        }
        return $key;
    }

    /**
     * @param $order
     * @return AnetAPI\PaymentType
     */
    function prepare_order($order)
    {
        $exp_date = $order->cardyear . '-' . $order->cardmonth;
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($order->cardnumber);
        $creditCard->setCardCode($order->cvv); // added new param - cvv
        $creditCard->setExpirationDate($exp_date);
        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);
        return $payment;
    }

    /**
     * @param $post_order
     * @return bool|stdClass
     */
    function make_transaction($post_order)
    {

        $names = explode(" ", $post_order->cardholder);
        if (count($names) == 2) {
            $firstname = $names[0];
            $lastname = $names[1];
        } // end if

        if (count($names) == 3) {
            $firstname = $names[0] . " " . $lastname = $names[1];
            $lastname = $names[2];
        } // end if

        $payment = $this->prepare_order($post_order);
        $merchantAuthentication = $this->sandbox_authorize();
        $refId = 'ref' . time();
        $state = $this->get_user_state($post_order->state);

        $invoiceNo = time();
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($invoiceNo);

        $order->setDescription($post_order->item);
        $lineitem = new AnetAPI\LineItemType();
        $lineitem->setItemId(time());
        $lineitem->setName($post_order->item);
        $lineitem->setDescription($post_order->item);
        $lineitem->setQuantity("1");
        $lineitem->setUnitPrice($post_order->amount);
        $lineitem->setTaxable("N");

        // Customer info 
        $custID = round(time() / 3785);
        $customer = new AnetAPI\CustomerDataType();
        $customer->setId($custID);
        $customer->setEmail($post_order->email);

        //Ship To Info
        $address = (string)$post_order->street . " " . (string)$post_order->city . " " . $state;

        $shipto = new AnetAPI\NameAndAddressType();
        $shipto->setFirstName($firstname);
        $shipto->setLastName($lastname);
        $shipto->setCompany('Student');
        $shipto->setAddress($address);
        $shipto->setCity($post_order->city);
        $shipto->setState($state);
        $shipto->setZip($post_order->zip);
        $shipto->setCountry("USA");

        // Bill To
        $billto = new AnetAPI\CustomerAddressType();
        $billto->setFirstName($firstname);
        $billto->setLastName($lastname);
        $billto->setCompany("Student");
        $billto->setAddress($address);
        $billto->setCity($post_order->city);
        $billto->setState($state);
        $billto->setZip($post_order->zip);
        $billto->setCountry("USA");

        //create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($post_order->amount);
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
        //$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if ($response != null) {
            $tresponse = $response->getTransactionResponse();
            if (($tresponse != null) && ($tresponse->getResponseCode() == "1")) {
                $userid = $this->get_user_id($post_order->email);
                if (!is_numeric($post_order->class)) {
                    //echo "Inside group name ...<br>";
                    $groupid = $this->get_group_id($post_order->class);
                } // end if
                else {
                    //echo "Inside group id ...<br>";
                    $groupid = $post_order->class;
                }

                //echo "Group ID: ".$groupid."<br>";

                $status = new stdClass();
                $status->auth_code = $tresponse->getAuthCode();
                $status->trans_id = $tresponse->getTransId();
                $status->response_code = $tresponse->getResponseCode();
                $status->userid = $userid;
                $status->groupid = $groupid;
                $key = $this->add_student_payment($post_order, $status);
                return $key;
            } // end if ($tresponse != null) && ($tresponse->getResponseCode() == "1")
            else {
                $this->save_log($tresponse);
                return false;
            }
        } // end if $response != null        
        else {
            //echo "Charge Credit card Null response returned";
            return false;
        }
    }

    /**
     * @param $post_order
     * @return string
     */
    function createSubscription($post_order)
    {

        // Common Set Up for API Credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->LOGIN_ID);
        $merchantAuthentication->setTransactionKey($this->TRANSACTION_KEY);
        $intervalLength = round($this->period / $post_order->payments_num);
        $refId = 'ref' . time();
        $start_date_h = date('Y-m-d', time()); // first subscription payment today
        $total_occurences = $post_order->payments_num;
        $expiration = $post_order->cds_cc_year . "-" . $post_order->cd_cc_month;
        $names = explode("/", $post_order->cds_name);

        // Customer info 
        $custID = round(time() / 3785);
        $customer = new AnetAPI\CustomerDataType();
        $customer->setId($custID);
        $customer->setEmail($post_order->cds_email);

        /*
         * 
          echo "<br>--------------------<br>";
          print_r($names);
          echo "<br>--------------------<br>";

          echo "First name: ".$firstname."<br>";
          echo "Last name: ".$lastname."<br>";
         * 
         */

        $firstname = $names[0];
        $lastname = $names[1];

        //$firstname = ($names[0] == '') ? "Loyal" : $names[0];
        //$lastname = ($names[2] == '') ? 'Client' : $names[2];
        // Subscription Type Info
        $subscription = new AnetAPI\ARBSubscriptionType();
        $subscription->setName("Subscription for $post_order->item");
        $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
        $interval->setLength($intervalLength);
        $interval->setUnit("days");
        $paymentSchedule = new AnetAPI\PaymentScheduleType();
        $paymentSchedule->setInterval($interval);
        $paymentSchedule->setStartDate(new DateTime($start_date_h));
        $paymentSchedule->setTotalOccurrences($total_occurences);
        $paymentSchedule->setTrialOccurrences("1");
        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setAmount($post_order->sum);
        $subscription->setTrialAmount("0.00");

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($post_order->cds_cc_number);
        $creditCard->setExpirationDate($expiration);
        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);
        $subscription->setPayment($payment);
        $billTo = new AnetAPI\NameAndAddressType();
        $billTo->setFirstName($firstname);
        $billTo->setLastName($lastname);
        $subscription->setBillTo($billTo);
        $request = new AnetAPI\ARBCreateSubscriptionRequest();
        $request->setmerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setSubscription($subscription);
        $controller = new AnetController\ARBCreateSubscriptionController($request);
        //$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        /*
         * 
          echo "--------Subscription response <pre>";
          print_r($response);
          echo "<br>-------------------------<br>";
          die('Stopped ....');
         * 
         */

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            $msg = $response->getSubscriptionId();
            //echo "Message: ".$msg."<br>";
        }  // end if ($response != null) && ($response->getMessages()->getResultCode() == "Ok")        
        else {
            $this->save_log($response);
            $errorMessages = $response->getMessages()->getMessage();
            $msg = $errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText();
        } // end else
        return $msg;
    }

    /**
     * @param $exp_date
     * @return string
     */
    function prepareExpirationDate($exp_date)
    {
        // MMYY - format
        $mm = substr($exp_date, 0, 2);
        $yy = substr($exp_date, 4);
        $date = $mm . $yy;
        return $date;
    }

    /**
     * @param $amount
     * @param $card_last_four
     * @param $exp_date
     * @param $trans_id
     * @return bool|AnetAPI\AnetApiResponseType
     */
    function makeRefund($amount, $card_last_four, $exp_date, $trans_id)
    {
        $merchantAuthentication = $this->authorize();
        $refId = 'ref' . time();
        $date = $this->prepareExpirationDate($exp_date);

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber(base64_decode($card_last_four));
        $creditCard->setExpirationDate($date);
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        //create a transaction
        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType("refundTransaction");
        $transactionRequest->setAmount($amount);
        $transactionRequest->setRefTransId($trans_id);
        $transactionRequest->setPayment($paymentOne);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequest);
        $controller = new AnetController\CreateTransactionController($request);
        //$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        if ($response != null) {
            $tresponse = $response->getTransactionResponse();

            //echo "Response: <pre>";
            //print_r($tresponse);
            //echo "</pre>";

            if (($tresponse != null) && ($tresponse->getResponseCode() == "1")) {
                //echo "it is ok ....";
                return TRUE;
            } // end if ($tresponse != null) && ($tresponse->getResponseCode() == \SampleCode\Constants::RESPONSE_OK)            
            else {
                $this->save_log($tresponse);
                return FALSE;
            }
        } // end if $response != null 
        else {
            //echo "Null resposnse .. ...";
            return FALSE;
        }
        return $response;
    }

}
