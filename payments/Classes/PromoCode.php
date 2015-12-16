<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/lms/payments/Classes/PlaceOrder.php');

class PromoCode {

    private $db;

    function __construct() {
        $this->db = DB::getInstance();
    }

    function makePromoCodes($num = 10, $exp_date = 3) {
        $code_exp_date = time() + 86400 * $exp_date;
        $pr = new PlaceOrder();
        for ($i = 0; $i <= $num; $i++) {
            $code = $pr->generateRandomString();
            $query = "insert into mdl_promo_code "
                    . "(code,active,expire_date) "
                    . "values ('" . $code . "',
                               '1', 
                               '" . $code_exp_date . "')";
            echo "Query: ".$query. "<br/>";            
            $this->db->query($query);
        }
    }

}
