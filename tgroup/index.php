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
        ?>


        <p align="center">You can create up to four new courses.Please provide at least one course name to be created for you.</p>
        <div class="wrapper clearfix">
            <div align="center">
                <section class="userLogin userForm clearfix oneCol">
                    <div class="loginForm dsR21">

                        <div class='CSSTableGenerator' id='signupwrap'
                             style='table-layout: fixed; width: 620px; align: center;'>

                            <form class='cmxform' id='groupform' method="post" action=''>
                                <table>
                                    <tr>
                                        <td colspan='2'>Create new course</td>
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
                                        <td><label for='group1'>Course*</label></td>
                                        <td><input id="group1" style='background-color: rgb(250, 255, 189);width:173px;' /><span style='color: red; font-size: 12px;width:173px;' id='group1_err'></span></td>
                                    </tr>

                                    <tr>
                                        <td><label for='group2'>Course</label></td>
                                        <td><input id="group2" style='background-color: rgb(250, 255, 189);width:173px;' /><span style='color: red; font-size: 12px;width:173px;' id='group2_err'></span></td>
                                    </tr>

                                    <tr>
                                        <td><label for='group3'>Course</label></td>
                                        <td><input id="group3" style='background-color: rgb(250, 255, 189);width:173px;' /><span style='color: red; font-size: 12px;width:173px;' id='group3_err'></span></td>
                                    </tr>

                                    <tr>
                                        <td><label for='group4'>Course</label></td>
                                        <td><input id="group4" style='background-color: rgb(250, 255, 189);width:173px;' /><span style='color: red; font-size: 12px;width:173px;' id='group4_err'></span></td>
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
        <div style="margin: auto;width: 60%;padding: 10px;" id="group_created"></div>
    </body>
</html>
