<?php

require_once './classes/Utils.php';
$u = new Utils2();
$username = $_POST['username'];
$groupname = $_POST['groupname'];
$u->add_trial_key($username, $groupname);
