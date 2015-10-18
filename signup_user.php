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

    function __construct($user_type)
    {
        $this->user_type = $user_type;
        $db = DB::getInstance();
        $this->db = $db;
    }

    function getCoursesList()
    {
        $list = "";
        $row = null;
        $list = $list . "<select id='courses' name='courses' style='background-color: rgb(250, 255, 189);'>";
        $list = $list . "<option value='0' selected>--------------------</option>";
        $query = "select id, fullname from mdl_course where groupmode=1";
        $result = $this->db->query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $list = $list . "<option value='" . $row['id'] . "'>" . $row['fullname'] . "</option>";
        }
        $list = $list . "</select>";
        return $list;
    }
    
    function isEmailUsed ($email) {
        $query="select email from mdl_user where email='$email'";
        return $this->db->numrows($query);
    }
    
    function isUserUsed ($username) {
        $query="select username from mdl_user where username='$username'";
        return $this->db->numrows($query);        
    }

    function getGroupsList($user = 'student', $course = NULL)
    {
        $list = "";
        $row = null;
        if ($user == 'student' && $course == null) {
            $list = $list . "<span id='for_gr'>";
            $list = $list . "<select id='groups' name='groups' style='background-color: rgb(250, 255, 189);'>";
            $list = $list . "<option value='0' selected>--------------------</option>";
            $list = $list . "</select>";
            $list = $list . "</span>";
            return $list;
        } elseif ($user == 'student' && $course != null) {
            $list = $list . "<span id='for_gr'>";
            $list = $list . "<select id='groups' name='groups' style='background-color: rgb(250, 255, 189);'>";
            $list = $list . "<option value='0' selected>---------------------</option>";
            $query = "select id, courseid, name from mdl_groups where courseid=" . $course . "";
            $result = $this->db->query($query);
            while ($row = mysql_fetch_assoc($result)) {
                $list = $list . "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            $list = $list . "</select>";
            $list = $list . "</span>";
            return $list;
        }
        if ($user == 'tutor' && $course == null) {
            $list = $list . "<span id='for_gr'>";
            $list = $list . "<select id='groups' name='groups' style='background-color: rgb(250, 255, 189);'>";
            $list = $list . "<option value='1'>1</option>";
            $list = $list . "<option value='2'>2</option>";
            $list = $list . "<option value='3'>3</option>";
            $list = $list . "<option value='4'>4</option>";
            $list = $list . "<option value='5'>5</option>";
            $list = $list . "<option value='6'>6</option>";
            $list = $list . "<option value='7'>7</option>";
            $list = $list . "<option value='8'>8</option>";
            $list = $list . "<option value='9'>9</option>";
            $list = $list . "<option value='10'>10</option>";
            $list = $list . "</select>";
            $list = $list . "</span>";
            return $list;
        }
    }

    function getSignUpForm()
    {
        $form = "<div class='CSSTableGenerator' id='signupwrap' style='width:620px;align:center;'>
             <form class='cmxform' id='signupform' method='post' action='http://mycodebusters.com/lms/moodle/login/signup.php' >    
              <table >
                    <tr>
                        <td colspan='2'>
                            User signup
                        </td>                        
                    </tr>
                    <tr>
                        <td >
                            <label for='firstname'>Firstname*</label>
                        </td>
                        <td>
                            <input id='firstname' name='firstname' type='text' style='background-color: rgb(250, 255, 189);'>&nbsp;<span style='color:red;font-size:12px;' id='fn_err'></span>
                        </td>
                                                
                    </tr>
                    <tr>
                        <td >
                            <label for='lastname'>Lastname*</label>
                        </td>
                        <td>
                            <input id='lastname' name='lastname' type='text' style='background-color: rgb(250, 255, 189);'> &nbsp;<span style='color:red;font-size:12px;' id='ln_err'></span>
                        </td>                        
                    </tr>
                    <tr>
                        <td >
                            <label for='username'>Username*</label>
                        </td>
                        <td>
                           <input id='username' name='username' type='text' style='background-color: rgb(250, 255, 189);'>&nbsp;<span style='color:red;font-size:12px;' id='username_err'>
                        </td>                                                   
                    </tr>
                    <tr>
                        <td>
                            <label for='password'>Password*</label>
                        </td>
                        <td>
                            <input id='password' name='password' type='password' style='background-color: rgb(250, 255, 189);'>&nbsp;<span style='color:red;font-size:12px;' id='pwd_err'>
                        </td>                        
                    </tr>
                    <tr>
                        <td>
                            <label for='email'>Email*</label>
                        </td>
                        <td>
                            <input id='email' name='email' type='email' style='background-color: rgb(250, 255, 189);'>&nbsp;<span style='color:red;font-size:12px;' id='email_err'>
                        </td>                        
                    </tr>                    
                     <tr>
                        <td>
                            <label for='city'>Street, city, state, zip</label>
                        </td>
                        <td>
                            <input id='address' name='address' style='background-color: rgb(250, 255, 189);'>
                        </td>                        
                    </tr> ";
        
        if ($this->user_type == 'student') {
            $courses = $this->getCoursesList();
            $groups = $this->getGroupsList('student', null);
            $form = $form . "<tr>
            <td>
            <label for='school'>Schoolname</label>
            </td>
            <td>
            <input id='school' name='school' style='background-color: rgb(250, 255, 189);'>
            </td>
            </tr>";
            $form = $form . "<input type='hidden' id='user_type' value='student'>";
            $form = $form . "<tr><td><label for='course'>Course</label></td><td>" . $courses . "</td>&nbsp;<span style='color:red;font-size:12px;' id='course_err'></span></tr>";
            $form = $form . "<tr><td><label for='group'>Group</label></td><td>" . $groups . "</td>&nbsp;<span style='color:red;font-size:12px;' id='group_err'></span></tr>";
        } else {
            $courses = $this->getCoursesList();
            $groups = $this->getGroupsList('tutor', null);
            $form = $form . "<input type='hidden' id='user_type' value='tutor'>";
            $form = $form . "<tr><td><label for='course'>Course</label></td><td>" . $courses . "</td><span style='color:red;font-size:12px;' id='course_err'></span></tr>";
            $form = $form . "<tr><td><label for='course'>Number of groups</label></td><td>" . $groups . "</td><span style='color:red;font-size:12px;' id='fn_err'></span></tr>";
        }
        
        $form = $form . "<tr><td colspan='2'><input class='submit' type='submit' value='Submit'></td></tr></table></form>
            </div>";
        return $form;
    }
}

?>