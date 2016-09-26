<?php

header("Access-Control-Allow-Origin: *");
require('../config.php');
require_once($CFG->dirroot . '/user/editlib.php');

$authplugin = get_auth_plugin($CFG->registerauth);

if (!$authplugin->can_signup()) {
    print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
}

if ($_REQUEST) {

    $user_data = $_REQUEST['user'];
    $posted_user = json_decode($user_data);

    $user = new stdClass();
    $user->confirmed = 1; // It is alwayes confirmed, but we check payment status after user login
    $user->username = strtolower($posted_user->email);
    $user->password = $posted_user->pwd;
    $user->purepassword = $posted_user->pwd;
    $user->email = strtolower($posted_user->email);
    $user->email1 = strtolower($posted_user->email);
    $user->email2 = strtolower($posted_user->email);
    $user->firstname = $posted_user->firstname;
    $user->lastname = $posted_user->lastname;
    $user->courseid = $posted_user->courseid;
    $user->address = $posted_user->street . " " . $posted_user->city . " " . $posted_user->zip;
    $user->zip = $posted_user->zip;
    $user->city = $posted_user->city;
    $user->idnumber=$posted_user->title;
    $user->institution=$posted_user->school;
    $user->department=$posted_user->dep;
    $user->url=$posted_user->site;
    $user->lang = current_language();
    $user->firstaccess = 0;
    $user->timecreated = time();
    $user->mnethostid = $CFG->mnet_localhost_id;
    $user->secret = random_string(15);
    $user->auth = $CFG->registerauth;

    /*
     * 
      echo "<br>----------------<br>";
      print_r($user);
      echo "<br>----------------<br>";
      die();
     * 
     */

    // Initialize alternate name fields to empty strings.
    $namefields = array_diff(get_all_user_name_fields(), useredit_get_required_name_fields());
    foreach ($namefields as $namefield) {
        $user->$namefield = '';
    }

    // Perform signup process
    $authplugin->user_signup($user, false); // do not send confirmation  email
    
} // end if $_REQUEST