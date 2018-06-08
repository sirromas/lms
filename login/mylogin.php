<?php

session_start();
$loginurl = '';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/access/classes/Access.php';

//print_r($_REQUEST);
//die();

if ($_REQUEST) {

    $ac = new Access();

    $referrer = $_SERVER['HTTP_REFERER'];
    $pureReferrer = str_replace('?errorcode=3', '', $referrer);
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];

    if ($username != '' && $password != '') {
        if ($username == 'admin') {
            $errorcode = 3;

            $loginurl .= $pureReferrer . '?errorcode=' . $errorcode;
            header("Location: $loginurl");
        } // end if
        else {
            $userid = $ac->get_userid_by_credentials($username, $password);
            if ($userid == 0) {
                $errorcode = 3;
                $loginurl .= $pureReferrer . '?errorcode=' . $errorcode;
                header("Location: $loginurl");
            } // end if
            else {
                $_SESSION['userid'] = $userid;
                $roleid = $ac->get_user_role_by_id($userid);

                if ($roleid == 4) {
                    $groups = $ac->get_user_groups();
                    $status = $ac->has_confirmed($userid);
                    if ($status == 0) {
                        $dialog = $ac->get_tutor_access_dialog($userid, $groups);
                        echo $dialog;
                        die();
                    }
                    $groupid = $groups[0];
                    $url = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/grade.php?userid=$userid";
                    header("Location: $url");
                } // end if $roleid == 4


                if ($roleid == 5) {
                    $groups = $ac->get_user_groups();
                    $status = $ac->has_access($userid);
                    if ($status == 0) {
                        $dialog = $ac->get_acces_dialog($userid, $groups);
                        echo $dialog;
                        die();
                    }
                    $url = "http://www." . $_SERVER['SERVER_NAME'] . "/lms/students.php?userid=$userid";
                    header("Location: $url");
                } // end if $roleid == 5
            }
        } // end else
    } // end if
    else {
        $errorcode = 3;
        $loginurl .= $pureReferrer . '?errorcode=' . $errorcode;
        redirect($loginurl);
    } // end else
} // end if
else {
    $errorcode = 3;
    $loginurl .= $pureReferrer . '?errorcode=' . $errorcode;
    redirect($loginurl);
}

