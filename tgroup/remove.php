<?php session_start(); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <script src="https://code.jquery.com/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="tgroup.js"></script>          
        <link rel="stylesheet" href="../register.css" />
    </head>

    <body>

        <br/><br/><br/> <p align="center"><img src="globalizationplus.jpg"></p>

        <?php
        require_once './Course.php';
        $cs = new Course();
        $code = $_GET['secret_code'];
        $userid = $_GET['userid'];
        if ($userid != '') {
            $groups = $cs->getTutorGroups($userid);
        } // end if $userid!=''
        else {
            echo "<p align='center' style='color:red;'>Invalid user credentials</p>";
        }
        ?>


        <p align="center">Please select courses you want to delete </p>
        <div class="wrapper clearfix">
            <div align="center">
                <section class="userLogin userForm clearfix oneCol">
                    <div class="loginForm dsR21">

                        <div class='CSSTableGenerator' id='signupwrap'
                             style='table-layout: fixed; width: 620px; align: center;'>

                            <form class='cmxform' id='delete_group_form' method="post" action=''>
                                <table>
                                    <tr>
                                        <td colspan='2'>Delete old course</td>
                                    </tr>
                                    <tr>
                                        <td style='width: 250px;'><label for='email'>Email*</label></td>
                                        <td><input id='email' name='email' 
                                                   style='background-color: rgb(250, 255, 189);width:173px;'/>&nbsp;<span
                                                   style='color: red; font-size: 12px;' id='email_err'></span></td>
                                    </tr>												
                                    <tr>
                                        <td><label for='code'>Secret code*</label></td>
                                        <td><input id='code' name='code' value="<?php echo $code; ?>"
                                                   style='background-color: rgb(250, 255, 189);width:173px;' />
                                            &nbsp;<span
                                                style='color: red; font-size: 12px;' id='code_err'></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><label for='page'>School’s online page*</label></td>
                                        <td><input id='page' name='page' 
                                                   style='background-color: rgb(250, 255, 189);width:173px;'/>
                                            &nbsp;<span style='color: red; font-size: 12px;width:173px;' id='page_err'></span></td>
                                    </tr>

                                    <tr>
                                        <td><label for='groups'>Courses do be deleted*</label></td>
                                        <td><?php echo $groups; ?> </td>                                        
                                    </tr>
                                    
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td style='font: 13.3333px Arial;'><span id="group_err" style="color:red;"></span></td>
                                    </tr>

                                    <tr>
                                        <td colspan='2'><input class='submit' type='submit' value='Submit'/></td>
                                    </tr>
                                </table>
                            </form>
                        </div>                   
                    </div>
                    <br/>
            </div>
        </div>
        <div style="margin: auto;width: 60%;padding:10px;text-align:center;" id="group_created"></div>
    </body>
</html>
