<?php

require_once './config.php';

function get_user_group1 ($user) {
    global $DB;
    
    $group_name=$DB->get_record('groups', array('id'=>$user->group));    
    print_r($group_name);
    
    return $group_name->name;    
}

$user=new stdClass();
$user->group=30;
$name=get_user_group1($user);

echo "<br/>Group name: $name<br/>";