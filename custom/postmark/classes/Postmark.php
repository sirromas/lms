<?php

/**
 * Description of Postmark
 *
 * @author moyo
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';
require_once '../vendor/autoload.php';

use Postmark\PostmarkClient;

class Postmark extends Utils {

    function __construct() {
        parent::__construct();
    }

}
