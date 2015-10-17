<?php
require_once ('class.database.php');

/**
 *
 * @author sirromas
 *        
 */
class signup_user
{

    private $signup_form = "";

    private $db;

    private $user_type;

    function __construct($use_type)
    {
        $this->user_type = $use_type;
        $db = DB::getInstance();
        $this->db = $db;
    }

    function getCoursesList()
    {
        $list = "";
        $row = null;
        $list = $list . "<select id='courses' name='courses'>";
        $query = "select id, fullname from mdl_course where groupmode=1";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $list = $list . "<option value='" . $row['id'] . "'>" . $row['fullname'] . "</option>";
        }
        $list = $list . "</select>";
        return $list;
    }

    function getGroupsList()
    {
        $list = "";
        $row = null;
        $list = $list . "<select id='groups' name='groups'>";
        $query = "select id, courseid, name from mdl_groups";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $list = $list . "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        $list = $list . "</select>";
        return $list;
    }

    function getSignUpForm()
    {
        $form = "<form class='cmxform' id='signupForm' method='post' action=''>
        <fieldset>
        <legend>Validating a complete form</legend>
        <p>
        <label for='firstname'>Firstname</label>
        <input id='firstname' name='firstname' type='text'>
        </p>
        <p>
        <label for='lastname'>Lastname</label>
        <input id='lastname' name='lastname' type='text'>
        </p>
        <p>
        <label for='username'>Username</label>
        <input id='username' name='username' type='text'>
        </p>
        <p>
        <label for='password'>Password</label>
        <input id='password' name='password' type='password'>
        </p>
        <p>
        <label for='confirm_password'>Confirm password</label>
        <input id='confirm_password' name='confirm_password' type='password'>
        </p>
        <p>
        <label for='email'>Email</label>
        <input id='email' name='email' type='email'>
        </p>";
        
        $courses = $this->getCoursesList();
        $groups = $this->getGroupsList();
        
        if ($this->user_type == 'student') {            
            $form=$form. "<p><label for='course'>Course</label>".$courses."</p>";
            $form=$form. "<p><label for='group'>Group</label>".$groups."</p>";
        } else {
            $form=$form. "<p><label for='course'>Course</label>".$courses."</p>";
            $form=$form. "<p><label for='course'>Umber of groups</label><input id='group' name='group' type='text'></p>";
        }
        
        $form=$form. "</fieldset><p><input class='submit' type='submit' value='Submit'></p>";
        return $form;
    }
}

?>