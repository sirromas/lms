<?php

require_once './signup_user.php';
$user_type='stduent';
$group=new signup_user($user_type);
$group->getGroupsListNew();