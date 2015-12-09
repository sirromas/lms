<?php

require_once './Classes/Keys.php';

$email='me@somemail';
$key='hgcdfy5ghjtcchHGfduyskss7hkh';

$keys=new Keys();
$key_exists=$keys->getKey($email, $key);

if ($key_exists) {
    echo "<br>Key exists<br/>";
}
else {
    echo "<br>Key does not exist<br/>";
}

